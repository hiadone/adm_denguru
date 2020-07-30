<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tagwordadm class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>컨텐츠몰관리>카테고리관리 controller 입니다.
 */
class Tagwordadm extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'cmall/tagwordadm';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Tag_word');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Tag_word_model';

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'cmall');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('querystring', 'cmalllib'));
    }

    /**
     * 카테고리관리를 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_tagwordadm_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        

        $getdata = array();
        $word = $this->Tag_word_model->get();
        if ($word && is_array($word)) {
            $word_array=array();
            foreach ($word as $wvalue) {
                if (element('tgw_value', $wvalue)) {
                    
                    $word_array['tgw_value_'.element('tgw_category', $wvalue)][] = trim(element('tgw_value', $wvalue));
                }
            }
            
            foreach ($word_array as $wkey => $wvalue) {
                $getdata[] = implode("\n",$wvalue);
            }
            
        }

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        
            $config = array(                
                array(
                    'field' => 'tgw_value_1',
                    'label' => '패션 태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_2',
                    'label' => '푸드 태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_3',
                    'label' => '산책 외출 태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_4',
                    'label' => '이동 태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_5',
                    'label' => '홈리빙 태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_6',
                    'label' => '놀이 장난감 태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_7',
                    'label' => '미용 목용태그사전',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'tgw_value_8',
                    'label' => '기타 태그사전',
                    'rules' => 'trim',
                ),
            );
        
        $this->form_validation->set_rules($config);


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($this->form_validation->run() === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            $tgw_value_1 = $this->input->post('tgw_value_1') ? $this->input->post('tgw_value_1') : '';
            $tgw_value_2 = $this->input->post('tgw_value_2') ? $this->input->post('tgw_value_2') : '';
            $tgw_value_3 = $this->input->post('tgw_value_3') ? $this->input->post('tgw_value_3') : '';
            $tgw_value_4 = $this->input->post('tgw_value_4') ? $this->input->post('tgw_value_4') : '';
            $tgw_value_5 = $this->input->post('tgw_value_5') ? $this->input->post('tgw_value_5') : '';
            $tgw_value_6 = $this->input->post('tgw_value_6') ? $this->input->post('tgw_value_6') : '';
            $tgw_value_7 = $this->input->post('tgw_value_7') ? $this->input->post('tgw_value_7') : '';
            $tgw_value_8 = $this->input->post('tgw_value_8') ? $this->input->post('tgw_value_8') : '';
            
            for($t=1;$t < 9;$t++) {

                if(empty(${'tgw_value_'.$t})) continue;
                $tgw_word_text=array();
                
                $tgw_word_text = str_replace("\n",",",${'tgw_value_'.$t});

                $tgw_word_text = preg_replace("/[ #\&\-%=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $tgw_word_text);

                $tgw_word_text = explode(",",$tgw_word_text);

                

                if(count($tgw_word_text)){
                    $deletewhere = array(
                        'tgw_category' => ($t+5),
                    );
                    $this->Tag_word_model->delete_where($deletewhere);            
                    if ($tgw_word_text && is_array($tgw_word_text)) {
                        $i =0;
                        foreach ($tgw_word_text as  $value) {
                            $value = trim($value);
                            if ($value) {
                            $i++;
                                $tagdata = array(
                                    'tgw_id' => $i,
                                    'tgw_value' => $value,
                                    'tgw_category' => ($t+5),
                                    
                                );
                                $this->Tag_word_model->insert($tagdata);
                            }
                        }
                    }
                    
                }
            }
            
            $this->session->set_flashdata(
                    'message',
                    '정상적으로 입력되었습니다'
                );
            redirect(admin_url($this->pagedir), 'refresh');

            
        }

    
        $view['view']['data'] = $getdata;

        

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'index');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 카테고리 삭제
     */
    public function delete($cbr_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_tagwordadm_delete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        $cbr_id = (int) $cbr_id;
        if (empty($cbr_id) OR $cbr_id < 1) {
            show_404();
        }

        $this->Tag_word_model->delete($cbr_id);
        

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        /**
         * 삭제가 끝난 후 목록페이지로 이동합니다
         */
        $this->session->set_flashdata(
            'message',
            '정상적으로 삭제되었습니다'
        );
        $param =& $this->querystring;
        $redirecturl = admin_url($this->pagedir . '?' . $param->output());

        redirect($redirecturl);
    }
}
