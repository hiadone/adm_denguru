<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Crawl class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 메인 페이지를 담당하는 controller 입니다.
 */


require_once FCPATH . '/plugin/google/cloud-vision/vendor/autoload.php';
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

require_once FCPATH . '/plugin/google/translate/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Translate\TranslateClient;


class Google_vision extends CB_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Post','Post_extra_vars','Crawl','Crawl_file','Crawl_tag');

    protected $imageAnnotator = null;
    protected $translate = null;

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('querystring'));


        $this->imageAnnotator = new ImageAnnotatorClient([
            'credentials' => './petproject-eb9bf7297b07.json'
        ]);


        # Instantiates a client
        $this->translate = new TranslateClient([
            'key' => 'AIzaSyCHmP5y6oQqYKh28vbRmuaPZ_MUTe0sFwo'
        ]);
    }


    /**
     * 전체 메인 페이지입니다
     */
    public function get_google_vision($post_id = 0)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_detect_label_index';
        $this->load->event($eventname);

        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            show_404();
        }

        $post = $this->Post_model->get_one($post_id);
        if ( ! element('post_id', $post)) {
            show_404();
        }

        $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($post_id);
        


        $board = $this->board->item_all(element('brd_id', $post));
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        $postwhere = array(
            'post_id' => $post_id,
        );

        $crawl_file = $this->Crawl_file_model
                        ->get('', '', $postwhere, '', '', 'crawl_id', 'ASC');

        
        foreach ($crawl_file as $key => $value) {

            $this->detect_label(element('crawl_id', $value),config_item('uploads_dir') . '/crawl/' . element('cfi_filename', $value));
                        
                        
                        
        }

    }   
    

    function detect_label($crawl_id=0,$path='')
    {

        
        

        if (empty($crawl_id) OR $crawl_id < 1) {
            show_404();
        }

        $crawl = $this->Crawl_model->get_one($crawl_id);
        if ( ! element('crawl_id', $crawl)) {
            show_404();
        }

        # Your Google Cloud Platform project ID
        // $projectId = 'petproject-235609';

        # annotate the image
        $image = file_get_contents($path);
        $response = $this->imageAnnotator->labelDetection($image);
        $labels = $response->getLabelAnnotations();
        $translate_text=array();

        $target = 'ko';

        if ($labels) {
            foreach ($labels as $label) {

                $translation = $this->translate->translate($label->getDescription(), [
                    'target' => $target
                ]);
                
                array_push($translate_text,$translation['text']);
                
                
            }
        } else {
            return 'No label found';
        }

        if(count($translate_text)){
            $deletewhere = array(
                'crawl_id' => element('crawl_id', $crawl),
            );
            $this->Crawl_tag_model->delete_where($deletewhere);            
            if ($translate_text && is_array($translate_text)) {
                foreach ($translate_text as $key => $value) {
                    $value = trim($value);
                    if ($value) {
                        $tagdata = array(
                            'post_id' => element('post_id', $crawl),
                            'crawl_id' => element('crawl_id', $crawl),
                            'brd_id' => element('brd_id', $crawl),
                            'cta_tag' => $value,
                        );
                        $this->Crawl_tag_model->insert($tagdata);
                    }
                }
            }
            
        }
        

        $this->imageAnnotator->close();
        
        return $translate_text;



        
    }
}
