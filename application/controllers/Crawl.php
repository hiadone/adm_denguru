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

require_once FCPATH . '/plugin/google/natural-language/vendor/autoload.php';
use Google\Cloud\Language\LanguageClient;

class Crawl extends CB_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Post','Post_link','Post_extra_vars','Post_meta','Crawl','Crawl_link', 'Crawl_file','Crawl_tag','Vision_api_label','Board_crawl','Cmall_item','Cmall_category', 'Cmall_category_rel','Board_category','Board_group_category','Cmall_brand','Cmall_attr', 'Cmall_attr_rel','Tag_word');

    protected $imageAnnotator = null;
    protected $translate = null;

    protected $tag_word = array();
    protected $_select = '';

    protected $db2;

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array','typography','security');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('querystring','aws_s3','form_validation'));

        $this->imageAnnotator = new ImageAnnotatorClient([
            'credentials' => 'glowing-harmony-278705-73ffa79d6108.json'
        ]);


        # Instantiates a client
        // $this->translate = new TranslateClient([
        //     'key' => config_item('translate_key')
        // ]);

        $projectId = 'denguru3-71f74';
        
        $this->naturallanguage = new LanguageClient([
            // 'projectId' => $projectId,
            'keyFilePath' => 'glowing-harmony-278705-73ffa79d6108.json'
        ]);

        $this->db2 = $this->load->database('db2', TRUE);
    }


    /**
     * 전체 메인 페이지입니다
     */
    
   
    public function crawling_update($post_id = 0,$brd_id = 0)
    {



        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $post_id = (int) $post_id;
        $brd_id = (int) $brd_id;
        if ((empty($post_id) OR $post_id < 1) && (empty($brd_id) OR $brd_id < 1)) {
            show_404();
        }

        if(!empty($post_id)){
            $post = $this->Post_model->get_one($post_id);
            if ( ! element('post_id', $post)) {
                show_404();
            }
        }

        
        
        

        
        
        $crawlwhere = array(
            'brd_id' => $brd_id,
        );

        $board = $this->board->item_all($brd_id);
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        
        if($post_id){
            // $postwhere = array(
            //     'post_id' => $post_id,
            // );
            



            // $crawl = $this->Cmall_item_model
            //     ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');


            // foreach ($crawl as $c_key => $c_value) {
            //     $this->board->delete_cmall(element('cit_id',$c_value));
            // }
        } 

        if($brd_id){
            // $brdwhere = array(
            //     'brd_id' => $brd_id,
            // );
            



            // $post = $this->Post_model
            //     ->get('', '', $brdwhere);


            // foreach ($post as $c_key => $c_value) {
            //     $this->board->delete_post(element('post_id',$c_value));
            // }

        } 

        

        $brdwhere = array(
                'crawl_item.brd_id' => $brd_id,
        );
        
        

        $this->_select='cb_crawl_item.*,cb_crawl_detail.cdt_brand1,cb_crawl_detail.cdt_brand2,cb_crawl_detail.cdt_brand3,cb_crawl_detail.cdt_brand4,cb_crawl_detail.cdt_brand5' ;
        $result = $this->get_admin_list('','',$brdwhere);


        if (element('list', $result)) {
                foreach (element('list', $result) as $key => $val){ 

                $item = array();
                $_post_id='';
                $cbr_id = array();
                
                $where = array(
                    'brd_id' => element('brd_id', $val),
                    'cit_goods_code' => element('crw_goods_code', $val),

                );

                
                
                $item = $this->Cmall_item_model->get_one('','',$where);

                if($post_id && element('cit_id',$item)) {
                    if(element('post_id',$item) != $post_id)  continue;
                }

                // if($this->Cmall_item_model->count_by($where)) {
                //     echo element('brd_id', $val).'스토어의 '.element('crw_goods_code', $val). '코드 존재<br>';
                //     continue;

                // }

                if(empty(element('crw_goods_code', $val))) {
                    echo element('brd_id', $val).'스토어의 '.element('crw_id', $val). '상품 코드 없다<br>';
                    continue;
                }


                $_post_id = $this->write(element('brd_id', $board),$val);

                for($a=1 ; $a <6;$a++ ){
                    if(element('crw_brand'.$a,$val))
                        if($this->cmall_brand(element('crw_brand'.$a,$val)))
                            $cbr_id[] = $this->cmall_brand(element('crw_brand'.$a,$val));
                }

                for($a=1 ; $a <6;$a++ ){
                    if(element('cdt_brand'.$a,$val))
                        if($this->cmall_brand(element('cdt_brand'.$a,$val)))
                            $cbr_id[] = $this->cmall_brand(element('cdt_brand'.$a,$val));
                }

                if(element('cit_id',$item)){

                    $updatedata = array(
                        
                        // 'post_id' => $_post_id,
                        'cit_name' => element('crw_name',$val),
                        'cit_summary' => element('crawl_sub_title',$val,'') ,
                        'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crw_price',$val))) ,
                        'cit_updated_datetime' => cdate('Y-m-d H:i:s'),                    
                        'cit_post_url' => element('crw_post_url',$val,''),
                        'cit_is_soldout' => element('crw_is_soldout', $val),
                        'cit_status' => element('is_del', $val) ? 0 : 1 ,
                        'cbr_id' => isset($cbr_id[0]) ? $cbr_id[0] : element('brd_brand', $board),
                        'cit_price_sale' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crw_price_sale',$val))) ,
                        // 'cit_type1' => element('cit_type1', $ivalue) ? 1 : 0,
                        // 'cit_type2' => element('cit_type2', $ivalue) ? 1 : 0,
                        // 'cit_type3' => element('cit_type3', $ivalue) ? 1 : 0,
                        // 'cit_type4' => element('cit_type4', $ivalue) ? 1 : 0,
                        
                    );


                    

                    
                    // $updatedata['cit_key'] = 'c_'.element('cit_id',$item);
                            
                    $this->Cmall_item_model->update(element('cit_id',$item), $updatedata);



                    # 이미지 URL 추출
                    // $imageUrl = $this->http_path_to_url($this->valid_url($board_crawl,$crawl_info[$ikey]['img_src']),element('pln_url', $value));

                    



                    
                    # 이미지 파일명 추출

                    

                    
                    
                    
                    # 이미지 파일이 맞는 경우
                    if (element('crw_file_1',$val)) {
                        
                        $img_src_array= explode('/', element('crw_file_1',$val));
                        $imageName = end($img_src_array);    

                        $oldimg_src_array= explode('/', element('cit_file_1',$item));
                        $oldimageName = end($oldimg_src_array);    

                        if($oldimageName !== $imageName){
                            $filetype = mime_content_type(config_item('uploads_dir') . '/crawlitem/'.element('crw_file_1',$val));

                            // echo $filetype;
                            $this->load->library('upload');
                            $upload_path_ =config_item('uploads_dir') . '/crawlitem/'.element('crw_file_1',$val);
                            $upload_path = config_item('uploads_dir') . '/cmallitem/';
                            if (is_dir($upload_path) === false) {
                                mkdir($upload_path, 0707);
                                $file = $upload_path . 'index.php';
                                $f = @fopen($file, 'w');
                                @fwrite($f, '');
                                @fclose($f);
                                @chmod($file, 0644);
                            }
                            $upload_path .= element('brd_id', $val) . '/';
                            if (is_dir($upload_path) === false) {
                                mkdir($upload_path, 0707);
                                $file = $upload_path . 'index.php';
                                $f = @fopen($file, 'w');
                                @fwrite($f, '');
                                @fclose($f);
                                @chmod($file, 0644);
                            }
                            $upload_path .= $_post_id . '/';
                            if (is_dir($upload_path) === false) {
                                mkdir($upload_path, 0707);
                                $file = $upload_path . 'index.php';
                                $f = @fopen($file, 'w');
                                @fwrite($f, '');
                                @fclose($f);
                                @chmod($file, 0644);
                            }

                            
                            copy(
                                $upload_path_,
                                $upload_path.$imageName
                            );
                            
                            @unlink($upload_path.$oldimageName);

                            $deleted = $this->aws_s3->delete_file($upload_path.$oldimageName);

                            $upload = $this->aws_s3->upload_file($upload_path_,'',$upload_path.$imageName,$filetype);       


                            
                                
                            if($upload){
                                $updatedata['cit_file_1'] = element('brd_id', $val) . '/'.$_post_id . '/'.$imageName;
                                $this->Cmall_item_model->update($cit_id, $updatedata);
                            }
                        }
                        
                    } 
                } else {

                    $updatedata = array(
                        
                        'post_id' => $_post_id,
                        'cit_name' => element('crw_name',$val),
                        'cit_summary' => element('crawl_sub_title',$val,'') ,
                        'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crw_price',$val))) ,
                        'cit_datetime' => cdate('Y-m-d H:i:s'),                    
                        'cit_post_url' => element('crw_post_url',$val,''),
                        'brd_id' => element('brd_id', $val),                    
                        'cit_goods_code' => element('crw_goods_code', $val),                        
                        'cit_is_soldout' => element('crw_is_soldout', $val),
                        'cit_status' => 1,
                        'cbr_id' => isset($cbr_id[0]) ? $cbr_id[0] : element('brd_brand', $board),
                        'cit_price_sale' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crw_price_sale',$val))) ,
                        // 'cit_type1' => element('cit_type1', $ivalue) ? 1 : 0,
                        // 'cit_type2' => element('cit_type2', $ivalue) ? 1 : 0,
                        // 'cit_type3' => element('cit_type3', $ivalue) ? 1 : 0,
                        // 'cit_type4' => element('cit_type4', $ivalue) ? 1 : 0,
                        
                    );


                    

                    $cit_id = $this->Cmall_item_model->insert($updatedata);
                    $updatedata = array();
                    $updatedata['cit_key'] = 'c_'.$cit_id;
                            
                    $this->Cmall_item_model->update($cit_id, $updatedata);



                    # 이미지 URL 추출
                    // $imageUrl = $this->http_path_to_url($this->valid_url($board_crawl,$crawl_info[$ikey]['img_src']),element('pln_url', $value));

                    



                    
                    # 이미지 파일명 추출

                    

                    
                    
                    
                    # 이미지 파일이 맞는 경우
                    if (element('crw_file_1',$val)) {
                        
                        $img_src_array= explode('/', element('crw_file_1',$val));
                        $imageName = end($img_src_array);    
                        $filetype = mime_content_type(config_item('uploads_dir') . '/crawlitem/'.element('crw_file_1',$val));

                        // echo $filetype;
                        $this->load->library('upload');
                        $upload_path_ =config_item('uploads_dir') . '/crawlitem/'.element('crw_file_1',$val);
                        $upload_path = config_item('uploads_dir') . '/cmallitem/';
                        if (is_dir($upload_path) === false) {
                            mkdir($upload_path, 0707);
                            $file = $upload_path . 'index.php';
                            $f = @fopen($file, 'w');
                            @fwrite($f, '');
                            @fclose($f);
                            @chmod($file, 0644);
                        }
                        $upload_path .= element('brd_id', $val) . '/';
                        if (is_dir($upload_path) === false) {
                            mkdir($upload_path, 0707);
                            $file = $upload_path . 'index.php';
                            $f = @fopen($file, 'w');
                            @fwrite($f, '');
                            @fclose($f);
                            @chmod($file, 0644);
                        }
                        $upload_path .= $_post_id . '/';
                        if (is_dir($upload_path) === false) {
                            mkdir($upload_path, 0707);
                            $file = $upload_path . 'index.php';
                            $f = @fopen($file, 'w');
                            @fwrite($f, '');
                            @fclose($f);
                            @chmod($file, 0644);
                        }

                        

                        
                        copy(
                            $upload_path_,
                            $upload_path.$imageName
                        );
                        $upload = $this->aws_s3->upload_file($upload_path_,'',$upload_path.$imageName,$filetype);       


                        
                            
                        if($upload){
                            $updatedata['cit_file_1'] = element('brd_id', $val) . '/'.$_post_id . '/'.$imageName;
                            $this->Cmall_item_model->update($cit_id, $updatedata);
                        }
                            
                        
                    } 
                }
                
            }
            
        }

        
        
    }


    function extract_html($url, $proxy='', $proxy_userpwd='', $referrer='') {


        $response = array();
        $response['code']='';
        $response['message']='';
        $response['status']=false;  
        
        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';
        // $agent = 'Mozilla/5.0 (iPad; CPU OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Version/7.0 Mobile/11D257 Safari/9537.53',
        // Some websites require referrer
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if(empty($referrer))
            $referrer = $scheme . '://' . $host; 
        

        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_PROXY, $proxy);
        // curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_userpwd);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_REFERER, $referrer);
        
        // if ( !file_exists(COOKIE_FILENAME) || !is_writable(COOKIE_FILENAME) ) {
        //     $response['status']=false;
        //     $response['message']='Cookie file is missing or not writable.';
        //     return $response;
        // }
     
        // curl_setopt($curl, CURLOPT_COOKIESESSION, 0);
        // curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILENAME);
        // curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILENAME);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        
        // allow to crawl https webpages
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        
        // the download speed must be at least 1 byte per second
        curl_setopt($curl,CURLOPT_LOW_SPEED_LIMIT, 1);
        
        // if the download speed is below 1 byte per second for more than 30 seconds curl will give up
        curl_setopt($curl,CURLOPT_LOW_SPEED_TIME, 30);
        

        

        $content = curl_exec($curl);
        
        
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
     
        $response['code'] = $code;
        
        if ($content === false) {
            $response['status'] = false;
            $response['content'] = curl_error($curl);
        }
        else{
            $response['status'] = true;
            $response['content'] = $content;
        }
        
        curl_close($curl);
        
        return $response;
        
    }


    function extract_html_post($url, $proxy='', $proxy_userpwd='', $referrer='', $data='') {


        $response = array();
        $response['code']='';
        $response['message']='';
        $response['status']=false;  
        
        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';
        
        // Some websites require referrer
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if(empty($referrer))
            $referrer = $scheme . '://' . $host; 
        

        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, isset($data));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($curl, CURLOPT_PROXY, $proxy);
        // curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_userpwd);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_REFERER, $referrer);
        
        // if ( !file_exists(COOKIE_FILENAME) || !is_writable(COOKIE_FILENAME) ) {
        //     $response['status']=false;
        //     $response['message']='Cookie file is missing or not writable.';
        //     return $response;
        // }
     
        // curl_setopt($curl, CURLOPT_COOKIESESSION, 0);
        // curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILENAME);
        // curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILENAME);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        
        // allow to crawl https webpages
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        
        // the download speed must be at least 1 byte per second
        curl_setopt($curl,CURLOPT_LOW_SPEED_LIMIT, 1);
        
        // if the download speed is below 1 byte per second for more than 30 seconds curl will give up
        curl_setopt($curl,CURLOPT_LOW_SPEED_TIME, 30);
        

        

        $content = curl_exec($curl);
        
        
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
     
        $response['code'] = $code;
        
        if ($content === false) {
            $response['status'] = false;
            $response['content'] = curl_error($curl);
        }
        else{
            $response['status'] = true;
            $response['content'] = $content;
        }
        
        curl_close($curl);
        
        return $response;
        
    }



    public function crawling_cit_type($cit_post_id=0,$cit_type='')
    {



        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $cit_post_id = (int) $cit_post_id;
        if (empty($cit_post_id) OR $cit_post_id < 1) {
            show_404();
        }

        $cit_post = $this->Post_model->get_one($cit_post_id);

        $where = array();

        $this->db->from('post');
        $this->db->where('brd_id', element('brd_id',$cit_post));
        $this->db->group_start();
        if($cit_type === 'cit_type3'){
            $this->db->like('lower(post_title)', '신상');
            $this->db->or_like('lower(post_title)', 'new');            
        } elseif($cit_type === 'cit_type2'){
            $this->db->like('lower(post_title)', '인기');
            $this->db->or_like('lower(post_title)', 'best');            
            $this->db->or_like('lower(post_title)', '베스트');            
        }
        
        $this->db->group_end();
        $res = $this->db->get();
        $result = $res->result_array();
        
        $post_id = element('post_id',element(0,$result));
        $post = $this->Post_model->get_one($post_id);
        if ( ! element('post_id', $post)) {
            show_404();
        }

        $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($post_id);
        $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);
        
        $where = array(
                'post_id' => $cit_post_id,
                );
        $updatedata = array(
            $cit_type => 0,
        );
        $this->Cmall_item_model->update('', $updatedata,$where);

        $crawlwhere = array(
            'brd_id' => element('brd_id', $post),
        );

        $board_crawl = $this->Board_crawl_model->get_one('','',$crawlwhere);
        if ( ! element('brd_id', $board_crawl)) {
            show_404();
        }

        $postwhere = array(
            'post_id' => $post_id,
        );
        $link = $this->Post_link_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');


        require_once FCPATH . 'plugin/simplehtmldom/simple_html_dom.php';



        foreach ($link as $key => $value) {
            
            
        
            $proxy_userpwd = 'username:password';
            $proxy_userpwd = '';
            $proxies[] = '10.0.0.1:80';
            $proxies[] = '10.0.0.2:8080'; 
            $proxies[] = '10.0.0.3:80'; 
            $proxies[] = '10.0.0.4:8080'; 
            $proxies[] = '10.0.0.5:80'; 


            $proxy_count = count($proxies) - 1;
             

            $proxy = $proxies[rand(0,$proxy_count)];
            $proxy = 0;
             
            
            
            $linkupdate = array(
                'pln_status' => 1,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);

            if(element('pln_page', $value)){
                $param =& $this->querystring;

                $pln_url = parse_url(element('pln_url', $value));

                parse_str($pln_url['query'] ,$query_);
                
                
                


                for($page=$query_['page'];element('pln_page', $value) >= $page;$page++){
                    $result = $this->extract_html($pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query']), $proxy, $proxy_userpwd);

                    if($result['code']===200){

                        // 기존 항목을 모두 지운다.

                        $html = new simple_html_dom();
                        $html->load($result['content']);

                        
                        $crawl_info=array();
                        $is_pln_error=false;
                        


                        if(element('post_content', $post))
                            eval(element('post_content', $post));
                        elseif(element('brd_content', $board_crawl))
                            eval(element('brd_content', $board_crawl));

                        foreach($crawl_info as $ikey => $ivalue){
                            $where = array(
                                    'post_id' => $cit_post_id,
                                    
                                    'cit_goods_code' => element('crawl_goods_code', $ivalue),
                                    );
                            $updatedata = array(
                                $cit_type => 1,
                            );
                            $this->Cmall_item_model->update('', $updatedata,$where);

                            
                            

                        }
                        $linkupdate = array(
                            'pln_status' => 0,
                        );
                        if(!$is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
                    } else {
                        continue;
                    }
                }
            } else {

                $result = $this->extract_html(element('pln_url', $value), $proxy, $proxy_userpwd);

                if($result['code']===200){

                    // 기존 항목을 모두 지운다.

                    $html = new simple_html_dom();
                    $html->load($result['content']);

                    
                    $crawl_info=array();
                    $is_pln_error=false;
                    


                    if(element('post_content', $post))
                        eval(element('post_content', $post));
                    elseif(element('brd_content', $board_crawl))
                        eval(element('brd_content', $board_crawl));

                    foreach($crawl_info as $ikey => $ivalue){
                        $where = array(
                                'post_id' => $cit_post_id,
                                
                                'cit_goods_code' => element('crawl_goods_code', $ivalue),
                                );
                        $updatedata = array(
                            $cit_type => 1,
                        );
                        $this->Cmall_item_model->update('', $updatedata,$where);

                        
                        

                    }
                    $linkupdate = array(
                        'pln_status' => 0,
                    );
                    if(!$is_pln_error)
                        $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
                } else {
                    continue;
                }
            }
            

        }

            
        // redirect(post_url(element('brd_key', $board_crawl),$post_id));
           

        
         

        
         
        //Proxy configuration
        
        
        

        // $html = html_purifier('http://www.hutsandbay.com/product/list-clothing.html?cate_no=62&page=1');

        // $ch = curl_init(); 
        // curl_setopt($ch, CURLOPT_URL, 'http://www.hutsandbay.com/product/list-clothing.html?cate_no=62&page=1'); 
        // curl_setopt($ch, CURLOPT_HEADER, 0); 
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)"); 
        
        // $contents = curl_exec($ch); 
        
        // curl_close($ch);

        
        
        // $ch = curl_init();
        // curl_setopt ($ch, CURLOPT_URL, $url);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt ($ch, CURLOPT_SSLVERSION,1);
        // curl_setopt ($ch, CURLOPT_HEADER, 0);
        // curl_setopt ($ch, CURLOPT_POST, 0);
        // curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        // curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
        // curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        // $result = curl_exec($ch);
        // curl_close($ch);

        // $fp = fopen("/tmp/loca.jpg", "w"); 
        // $ch = curl_init(); 
        // curl_setopt($ch, CURLOPT_URL, "http://remotedomain.com/image.jpg"); 
        // curl_setopt($ch, CURLOPT_HEADER, 0); 
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)"); 
        // curl_setopt($ch, CURLOPT_FILE, $fp); 
        // curl_exec($ch); 
        // fclose($fp); 
        // curl_close($ch);


        /*
         * 정규식 가져오기 (일부 사이트는 방지가 되어 있을 수 있으니 정규식 지정전에 전체 가져오기를 해보세요)
         */
        // preg_match('/<!doctype html>(.*?)<\/html>/is', $snoopy->results, $html);
        // echo $html[0];

        // $view = array();
        // $view['view'] = array();

        // // 이벤트가 존재하면 실행합니다
        // $view['view']['event']['before'] = Events::trigger('before', $eventname);

        // $where = array(
        //     'brd_search' => 1,
        // );
        // $board_id = $this->Board_model->get_board_list($where);
        // $board_list = array();
        // if ($board_id && is_array($board_id)) {
        //     foreach ($board_id as $key => $val) {
        //         $board_list[] = $this->board->item_all(element('brd_id', $val));
        //     }
        // }
        // $view['view']['board_list'] = $board_list;
        
        
        
    }


    public function crawling_overwrite($post_id=0,$brd_id =0)
    {
exit;

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $post_id = (int) $post_id;
        $brd_id = (int) $brd_id;
        if ((empty($post_id) OR $post_id < 1) && (empty($brd_id) OR $brd_id < 1)) {
            show_404();
        }

        if(!empty($post_id)){
            $post = $this->Post_model->get_one($post_id);
            if ( ! element('post_id', $post)) {
                show_404();
            }

            
        }

        // if(strstr(strtolower(element('post_title',$post)),'신상') || strstr(strtolower(element('post_title',$post)),'new') || strstr(strtolower(element('post_title',$post)),'best') || strstr(strtolower(element('post_title',$post)),'베스트') || strstr(strtolower(element('post_title',$post)),'추천') || strstr(strtolower(element('post_title',$post)),'인기')) return false ;
        
        // $category = $this->Board_group_category_model->get_category_info(1, element('post_category', $post));


        // $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($post_id);
        // $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);

        
        

        
        
        $crawlwhere = array(
            'brd_id' => $brd_id,
        );

        $board = $this->board->item_all($brd_id);
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        
        if($post_id){
            $postwhere = array(
                'post_id' => $post_id,
            );
            



            $crawl = $this->Cmall_item_model
                ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');


            foreach ($crawl as $c_key => $c_value) {
                $this->board->delete_cmall(element('cit_id',$c_value));
            }
        } 

        if(empty($post_id)){
            if($brd_id){
                $brdwhere = array(
                    'brd_id' => $brd_id,
                );
                



                $post = $this->Post_model
                    ->get('', '', $brdwhere);


                foreach ($post as $c_key => $c_value) {
                    $this->board->delete_post(element('post_id',$c_value));
                }

            } 
        }


        $brdwhere = array(
                'crawl_item.brd_id' => $brd_id,
        );
        
        

        $this->_select='cb_crawl_item.*,cb_crawl_detail.cdt_brand1,cb_crawl_detail.cdt_brand2,cb_crawl_detail.cdt_brand3,cb_crawl_detail.cdt_brand4,cb_crawl_detail.cdt_brand5' ;
        $result = $this->get_admin_list('','',$brdwhere);

        
        if (element('list', $result)) {
                foreach (element('list', $result) as $key => $val){ 
 

                $_post_id='';
                $cbr_id = array();
                
                $where = array(
                    'brd_id' => element('brd_id', $val),
                    'cit_goods_code' => element('crw_goods_code', $val),
                );
                
                if($this->Cmall_item_model->count_by($where)) {
                    echo element('brd_id', $val).'스토어의 '.element('crw_goods_code', $val). '코드 존재<br>';
                    continue;

                }

                if(empty(element('crw_goods_code', $val))) {
                    echo element('brd_id', $val).'스토어의 '.element('crw_id', $val). '상품 코드 없다<br>';
                    continue;
                }


                $_post_id = $this->write(element('brd_id', $board),$val);

                for($a=1 ; $a <6;$a++ ){
                    if(element('crw_brand'.$a,$val))
                        if($this->cmall_brand(element('crw_brand'.$a,$val)))
                            $cbr_id[] = $this->cmall_brand(element('crw_brand'.$a,$val));
                }

                for($a=1 ; $a <6;$a++ ){
                    if(element('cdt_brand'.$a,$val))
                        if($this->cmall_brand(element('cdt_brand'.$a,$val)))
                            $cbr_id[] = $this->cmall_brand(element('cdt_brand'.$a,$val));
                }


                $updatedata = array(
                    
                    'post_id' => $_post_id,
                    'cit_name' => element('crw_name',$val),
                    'cit_summary' => element('crawl_sub_title',$val,'') ,
                    'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crw_price',$val))) ,
                    'cit_datetime' => cdate('Y-m-d H:i:s'),                    
                    'cit_post_url' => element('crw_post_url',$val,''),
                    'brd_id' => element('brd_id', $val),                    
                    'cit_goods_code' => element('crw_goods_code', $val),                        
                    'cit_is_soldout' => element('crw_is_soldout', $val),
                    'cit_status' => 1,
                    'cbr_id' => isset($cbr_id[0]) ? $cbr_id[0] : element('brd_brand', $board),
                    'cit_price_sale' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crw_price_sale',$val))) ,
                    // 'cit_type1' => element('cit_type1', $ivalue) ? 1 : 0,
                    // 'cit_type2' => element('cit_type2', $ivalue) ? 1 : 0,
                    // 'cit_type3' => element('cit_type3', $ivalue) ? 1 : 0,
                    // 'cit_type4' => element('cit_type4', $ivalue) ? 1 : 0,
                    
                );


                

                $cit_id = $this->Cmall_item_model->insert($updatedata);
                $updatedata = array();
                $updatedata['cit_key'] = 'c_'.$cit_id;
                        
                $this->Cmall_item_model->update($cit_id, $updatedata);



                # 이미지 URL 추출
                // $imageUrl = $this->http_path_to_url($this->valid_url($board_crawl,$crawl_info[$ikey]['img_src']),element('pln_url', $value));

                



                
                # 이미지 파일명 추출

                

                
                
                
                # 이미지 파일이 맞는 경우
                if (element('crw_file_1',$val)) {
                    
                    $img_src_array= explode('/', element('crw_file_1',$val));
                    $imageName = end($img_src_array);    
                    $filetype = mime_content_type(config_item('uploads_dir') . '/crawlitem/'.element('crw_file_1',$val));

                    // echo $filetype;
                    $this->load->library('upload');
                    $upload_path_ =config_item('uploads_dir') . '/crawlitem/'.element('crw_file_1',$val);
                    $upload_path = config_item('uploads_dir') . '/cmallitem/';
                    if (is_dir($upload_path) === false) {
                        mkdir($upload_path, 0707);
                        $file = $upload_path . 'index.php';
                        $f = @fopen($file, 'w');
                        @fwrite($f, '');
                        @fclose($f);
                        @chmod($file, 0644);
                    }
                    $upload_path .= element('brd_id', $val) . '/';
                    if (is_dir($upload_path) === false) {
                        mkdir($upload_path, 0707);
                        $file = $upload_path . 'index.php';
                        $f = @fopen($file, 'w');
                        @fwrite($f, '');
                        @fclose($f);
                        @chmod($file, 0644);
                    }
                    $upload_path .= $_post_id . '/';
                    if (is_dir($upload_path) === false) {
                        mkdir($upload_path, 0707);
                        $file = $upload_path . 'index.php';
                        $f = @fopen($file, 'w');
                        @fwrite($f, '');
                        @fclose($f);
                        @chmod($file, 0644);
                    }

                    

                    copy(
                        $upload_path_,
                        $upload_path.$imageName
                    );

                    $upload = $this->aws_s3->upload_file($upload_path_,'',$upload_path.$imageName,$filetype);       


                    
                        
                    if($upload){
                        $updatedata['cit_file_1'] = element('brd_id', $val) . '/'.$_post_id . '/'.$imageName;
                        $this->Cmall_item_model->update($cit_id, $updatedata);
                    }
                        
                    
                } 
            }
            
        }

        
    }


    public function vision_api_label($post_id=0,$brd_id = 0,$cit_id = 0)
    {


        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $is_admin = $this->member->is_admin();

        if(empty($is_admin)) exit;

        $post_id = (int) $post_id;
        $brd_id = (int) $brd_id;
        $cit_id = (int) $cit_id;
        if ((empty($post_id) OR $post_id < 1) && (empty($brd_id) OR $brd_id < 1)) {
            show_404();
        }

        if(!empty($post_id)){
            $post = $this->Post_model->get_one($post_id);
            if ( ! element('post_id', $post)) {
                show_404();
            }

            
        }

       

        
        

        
        
        $crawlwhere = array(
            'brd_id' => $brd_id,
        );

        $board = $this->board->item_all($brd_id);
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        

        if(!empty($brd_id)){
            $postwhere = array(
                'brd_id' => $brd_id,
            );
        }


        if($post_id){
            $postwhere['post_id'] = $post_id;
            
        }


        if($cit_id){
            $postwhere['cit_id'] = $cit_id;
        }

        


        



        $crawl = $this->Cmall_item_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');

        foreach ($crawl as $c_key => $c_value) {

            $label_text = array();
            $label = array();
            $label_tag = array();
            $tag_ = array();

                $brdwhere = array(
                        'crawl_item.brd_id' => element('brd_id',$c_value),
                        'crawl_item.crw_goods_code' => element('cit_goods_code',$c_value),
                );
                
                
                $this->_select='cb_crawl_item.*,cb_crawl_detail.cdt_file_1,cb_crawl_detail.cdt_content' ;
                
                $result = $this->get_admin_list('','',$brdwhere);
                
        

                if(element('cit_file_1',$c_value) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1',$c_value)) < 5020868) {
                    $label_text[] = $this->detect_label(element('cit_id', $c_value),config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1',$c_value),$c_value['cit_name']);
                    
                } 


                if(element('cdt_file_1',element(0,element('list',$result))) && filesize(config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result)))) < 5020868) {
                    $label_text[] = $this->detect_label(element('cit_id', $c_value),config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result))),$c_value['cit_name']);
                    
                } elseif(element('cdt_file_1',element(0,element('list',$result))) && filesize(config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result)))) > 5020868) {


                    $label_text[] = $this->detect_label(element('cit_id', $c_value),thumb_url('crawlitemdetail',config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result)))),$c_value['cit_name']);
                }

                
                if(element('cdt_file_1',element(0,element('list',$result))) && filesize(config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result)))) < 5020868) {
                    $label_tag[] = $this->detect_tag(element('cit_id', $c_value),config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result))),$c_value['cit_name']);
                    
                } elseif(element('cdt_file_1',element(0,element('list',$result))) && filesize(config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result)))) > 5020868) {


                    $label_tag[] = $this->detect_tag(element('cit_id', $c_value),thumb_url('crawlitemdetail',config_item('uploads_dir') . '/crawlitemdetail/' . element('cdt_file_1',element(0,element('list',$result)))),$c_value['cit_name']);
                }


                $label_tag['cit_text'] = element('cdt_content',element(0,element('list',$result))) ? implode("\n",element('cdt_content',element(0,element('list',$result)))) : '';
                $label_tag['cit_name'] = element('crw_name',element(0,element('list',$result))) ? element('crw_name',element(0,element('list',$result))) : element('crw_name',element(0,element('list',$result)));

                $tag_ = $this->getnaturallanguage($label_tag);


                if($label_text){
                    foreach($label_text as $val){
                        foreach($val as $val_){
                            if(!in_array($val_,$label))
                                array_push($label,$val_);
                        }
                        
                    }

                }

                if($tag_){
                    foreach($tag_ as $val){
                        if(!in_array($val,$label))
                            array_push($label,$val);
                    }
                }
                
                if(count($label)){
                    $deletewhere = array(
                        'cit_id' => element('cit_id', $c_value),
                    );
                    $this->Vision_api_label_model->delete_where($deletewhere);            
                    if ($label && is_array($label)) {
                        foreach ($label as $key => $value) {
                            $value = trim($value);
                            if ($value) {
                                $tagdata = array(
                                    'post_id' => element('post_id', $c_value),
                                    'cit_id' => element('cit_id', $c_value),
                                    'brd_id' => element('brd_id', $c_value),
                                    'val_tag' => $value,
                                );
                                $this->Vision_api_label_model->insert($tagdata);
                            }
                        }
                    }
                }
                
        }

           
            
            
        
            // if($a > 10 ) exit;
         


        
        


        
        
        
        
    }
    

    public function crawling_category_update($post_id=0,$brd_id = 0)
    {



        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $is_admin = $this->member->is_admin();

        if(empty($is_admin)) exit;

        $post_id = (int) $post_id;
        $brd_id = (int) $brd_id;
        if ((empty($post_id) OR $post_id < 1) && (empty($brd_id) OR $brd_id < 1)) {
            show_404();
        }

        // $crawlwhere = array(
        //     'brd_id' => $brd_id,
        // );

        $board = $this->board->item_all($brd_id);
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        
        if($post_id){
            $postwhere = array(
                'post_id' => $post_id,
            );
            



            $result['list'] = $this->Cmall_item_model
                ->get('', '', $postwhere);


           
        } 

        if(empty($post_id)){
            if($brd_id){
                $brdwhere = array(
                    'brd_id' => $brd_id,
                );
                



                $result['list'] = $this->Cmall_item_model
                ->get('', '', $brdwhere);


                

            } 
        }

       

        $board = $this->board->item_all($brd_id);

        
        
        
        

        

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val){ 
                

                $c_category=array();
                $category='';
                $all_category=array();
                $all_attr=array();

                $post = $this->Post_model->get_one(element('post_id',$val));

                $category = $this->Board_group_category_model->get_category_info(1, element('post_category', $post));
                
                if($category)
                    $c_category[] = $category['bca_value'];
                if(element('bca_parent', $category)){
                    $category = $this->Board_group_category_model->get_category_info(1, element('bca_parent', $category));    
                    $c_category[] = $category['bca_value'];
                }
                
                
                $all_category = $this->Cmall_category_model->get_all_category();
                $all_attr = $this->Cmall_attr_model->get_all_attr();

                

                $cmall_category=array();
                $cmall_attr=array();
                $crawl_tag_arr=array();            
                $crawl_tag_text=array();


                $crawl_tag_arr = $this->Crawl_tag_model->get('','',array('cit_id' => element('cit_id',$val)));

                foreach($crawl_tag_arr as $t_value){
                    
                    array_push($crawl_tag_text,element('cta_tag',$t_value));
                }


               

                

                $deletewhere = array(
                    'cit_id' => element('cit_id',$val),
                );

                $this->Cmall_category_rel_model->delete_where($deletewhere);   

                foreach($all_category as $a_cvalue){
                    
                    foreach($a_cvalue as $a_cvalue_){
                        
                        
                        if(empty(element('cca_text',$a_cvalue_))) continue; 

                        if($this->crawl_tag_to_category(element('cca_text',$a_cvalue_),$crawl_tag_text)){
                            $cmall_category[element('cca_id',$a_cvalue_)] = element('cca_id',$a_cvalue_);

                            if(element('cca_parent',$a_cvalue_)){
                                $cmall_category[element('cca_parent',$a_cvalue_)] = element('cca_parent',$a_cvalue_);
                                $cmall_category[element('cca_id',$this->Cmall_category_model->get_category_info(element('cca_parent',$a_cvalue_)))] = element('cca_id',$this->Cmall_category_model->get_category_info(element('cca_parent',$a_cvalue_)));
                            }

                            
                        }
                                            
                    }
                    
                    
                }
                if($cmall_category){                                      
                    $this->Cmall_category_rel_model->save_category(element('cit_id',$val), $cmall_category);    

                }
                
                

                
                
                foreach($all_category as $a_cvalue2){
                    foreach($a_cvalue2 as $a_cvalue2_){
                        if($this->category_check(element('cca_value',$a_cvalue2_),$c_category)){
                            $cmall_category[element('cca_id',$a_cvalue2_)] = element('cca_id',$a_cvalue2_);
                            if(element('cca_parent',$a_cvalue2_)){
                                $cmall_category[element('cca_parent',$a_cvalue2_)] = element('cca_parent',$a_cvalue2_);
                                $cmall_category[element('cca_id',$this->Cmall_category_model->get_category_info(element('cca_parent',$a_cvalue2_)))] = element('cca_id',$this->Cmall_category_model->get_category_info(element('cca_parent',$a_cvalue2_)));
                            }
                        }
                    }
                }

                if($cmall_category)
                    $this->Cmall_category_rel_model->save_category(element('cit_id',$val), $cmall_category);


                $deletewhere = array(
                    'cit_id' => element('cit_id',$val),
                );

                $this->Cmall_attr_rel_model->delete_where($deletewhere);   

                foreach($all_attr as $a_cvalue){
                    
                    foreach($a_cvalue as $a_cvalue_){
                        
                        
                        if(empty(element('cat_text',$a_cvalue_))) continue; 

                        if($this->crawl_tag_to_attr(element('cat_text',$a_cvalue_),$crawl_tag_text)){
                            $cmall_attr[element('cat_id',$a_cvalue_)] = element('cat_id',$a_cvalue_);

                            if(element('cat_parent',$a_cvalue_)){
                                $cmall_attr[element('cat_parent',$a_cvalue_)] = element('cat_parent',$a_cvalue_);
                                $cmall_attr[element('cat_id',$this->Cmall_attr_model->get_attr_info(element('cat_parent',$a_cvalue_)))] = element('cat_id',$this->Cmall_attr_model->get_attr_info(element('cat_parent',$a_cvalue_)));
                            }

                            
                        }
                                            
                    }
                    
                    
                }
                if($cmall_attr){                                      
                    $this->Cmall_attr_rel_model->save_attr(element('cit_id',$val), $cmall_attr);    

                }
            }

        }

    }

    public function crawling_tag_update($post_id=0,$brd_id = 0)
    {


        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $is_admin = $this->member->is_admin();

        if(empty($is_admin)) exit;

        $post_id = (int) $post_id;
        $brd_id = (int) $brd_id;
        if ((empty($post_id) OR $post_id < 1) && (empty($brd_id) OR $brd_id < 1)) {
            show_404();
        }


        



        

        // $crawlwhere = array(
        //     'brd_id' => $brd_id,
        // );

        $board = $this->board->item_all($brd_id);
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        
        if($post_id){
            $postwhere = array(
                'post_id' => $post_id,
            );
            



            $result['list'] = $this->Cmall_item_model
                ->get('', '', $postwhere);


           
        } 

        if(empty($post_id)){
            if($brd_id){
                $brdwhere = array(
                    'brd_id' => $brd_id,
                );
                



                $result['list'] = $this->Cmall_item_model
                ->get('', '', $brdwhere);


                

            } 
        }

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val){ 

                $post = $this->Post_model->get_one(element('post_id',$val));
            
                $post['category'] = $this->Board_category_model->get_category_info(element('brd_id', $post), element('post_category', $post));
                if(empty($post['category'])) 
                $post['category'] = $this->Board_group_category_model->get_category_info(1, element('post_category', $post));

                if(empty($post['category']['bca_parent']))
                    $this->tag_word = $this->Tag_word_model->get('','',array('tgw_category' =>$post['category']['bca_key']));
                else 
                    $this->tag_word = $this->Tag_word_model->get('','',array('tgw_category' =>$post['category']['bca_parent'])); 


                $all_category=array();
        
        
        // $all_category = $this->Cmall_category_model->get_all_category();
        

        
        
        

        
                $crawlwhere = array(
                    'cit_id' => element('cit_id', $val),
                );

        
                $tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
                if ($tag && is_array($tag)) {
                    $tag_array=array();
                    foreach ($tag as $tvalue) {
                        if (element('val_tag', $tvalue)) {
                            array_push($tag_array,trim(element('val_tag', $tvalue)));
                        }
                    }
                    

                    $translate_text = array();

                    
                    foreach($this->tag_word as $word){
                        foreach ($tag_array as $tval) {
                            $arr_str = preg_split("//u", element('tgw_value',$word), -1, PREG_SPLIT_NO_EMPTY);
                            
                            if(count($arr_str) > 2){
                                if(strpos(strtolower(str_replace(" ","",$tval)),strtolower(str_replace(" ","",element('tgw_value',$word)))) !== false ){
                                    if(!in_array(element('tgw_value',$word),$translate_text))
                                        array_push($translate_text,element('tgw_value',$word));       
                                }     
                            } else {
                                if(strtolower(str_replace(" ","",$tval)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){
                                    if(!in_array(element('tgw_value',$word),$translate_text))
                                        array_push($translate_text,element('tgw_value',$word));       
                                }     
                            }
                            
                            
                        }
                        
                        
                    }

                    if(count($translate_text)){
                        

                        
                        
                        if ($translate_text && is_array($translate_text)) {
                            foreach ($translate_text as  $text) {
                                // $value = trim($value);
                                if ($text) {

                                    $where = array(
                                            'post_id' => element('post_id', $val),
                                            'cit_id' => element('cit_id', $val),
                                            'brd_id' => element('brd_id', $val),
                                            'cta_tag' => $text,
                                        );
                                    if(!$this->Crawl_tag_model->count_by($where)) {
                                        
                                        $tagdata = array(
                                            'post_id' => element('post_id', $val),
                                            'cit_id' => element('cit_id', $val),
                                            'brd_id' => element('brd_id', $val),
                                            'cta_tag' => $text,
                                        );
                                        $this->Crawl_tag_model->insert($tagdata);
                                    }
                                }
                            }
                        }                  
                    } 
                }
        
            

                

                
            }
        }   


        
    }


    public function crawling_tag_overwrite($post_id=0,$brd_id = 0)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $is_admin = $this->member->is_admin();

        if(empty($is_admin)) exit;

        $post_id = (int) $post_id;
        $brd_id = (int) $brd_id;
        if ((empty($post_id) OR $post_id < 1) && (empty($brd_id) OR $brd_id < 1)) {
            show_404();
        }


        



        

        // $crawlwhere = array(
        //     'brd_id' => $brd_id,
        // );

        $board = $this->board->item_all($brd_id);
        if ( ! element('brd_id', $board)) {
            show_404();
        }

        
        if($post_id){
            $postwhere = array(
                'post_id' => $post_id,
            );
            



            $result['list'] = $this->Cmall_item_model
                ->get('', '', $postwhere);


           
        } 

        if(empty($post_id)){
            if($brd_id){
                $brdwhere = array(
                    'brd_id' => $brd_id,
                );
                



                $result['list'] = $this->Cmall_item_model
                ->get('', '', $brdwhere);


                

            } 
        }

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val){ 

                $post = $this->Post_model->get_one(element('post_id',$val));
            
                $post['category'] = $this->Board_category_model->get_category_info(element('brd_id', $post), element('post_category', $post));
                if(empty($post['category'])) 
                $post['category'] = $this->Board_group_category_model->get_category_info(1, element('post_category', $post));


                if(empty($post['category']['bca_parent']))
                    $this->tag_word = $this->Tag_word_model->get('','',array('tgw_category' =>$post['category']['bca_key']));
                else 
                    $this->tag_word = $this->Tag_word_model->get('','',array('tgw_category' =>$post['category']['bca_parent'])); 


                $all_category=array();
        
        
        // $all_category = $this->Cmall_category_model->get_all_category();
        

        
        
       

       
                $crawlwhere = array(
                    'cit_id' => element('cit_id', $val),
                );

        
                $tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
                if ($tag && is_array($tag)) {
                    $tag_array=array();
                    foreach ($tag as $tvalue) {
                        if (element('val_tag', $tvalue)) {
                            array_push($tag_array,trim(element('val_tag', $tvalue)));
                        }
                    }
                    

                    $translate_text = array();

                    
                    foreach($this->tag_word as $word){
                        foreach ($tag_array as $tval) {
                            $arr_str = preg_split("//u", element('tgw_value',$word), -1, PREG_SPLIT_NO_EMPTY);
                            
                            if(count($arr_str) > 1){
                                if(strpos(strtolower(str_replace(" ","",$tval)),strtolower(str_replace(" ","",element('tgw_value',$word)))) !== false ){
                                    if(!in_array(element('tgw_value',$word),$translate_text))
                                        array_push($translate_text,element('tgw_value',$word));       
                                }     
                            } else {
                                if(strtolower(str_replace(" ","",$tval)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){
                                    if(!in_array(element('tgw_value',$word),$translate_text))
                                        array_push($translate_text,element('tgw_value',$word));       
                                }     
                            }
                            
                            
                        }
                        
                        
                    }

                    if(count($translate_text)){
                        

                        
                        $deletewhere = array(
                            'cit_id' => element('cit_id',$val),
                        );
                        $this->Crawl_tag_model->delete_where($deletewhere);            
                        if ($translate_text && is_array($translate_text)) {
                            foreach ($translate_text as  $text) {
                                // $value = trim($value);
                                if ($text) {
                                    $tagdata = array(
                                        'post_id' => element('post_id', $val),
                                        'cit_id' => element('cit_id', $val),
                                        'brd_id' => element('brd_id', $val),
                                        'cta_tag' => $text,
                                    );
                                    $this->Crawl_tag_model->insert($tagdata);
                                }
                            }
                        }                  
                    } 
                }
     
            

                

                
            }
        }   


    }
 

    function valid_url($board_crawl = array() , $crawl_url=''){



        $b = parse_url(trim(element('brd_url',$board_crawl)));            
        $c = trim($crawl_url);
        
        

        if(strpos($c,$b['host']) === false ){
            if($this->form_validation->valid_url($c))
                return $c;
            else {
                // return element('brd_url',$board_crawl).$c;
                return $b['scheme']."://".$b['host'].$c;
            }
        } else {


           $d = parse_url($c);          
            if($d['host'])
                return $b['scheme']."://".strstr($c,$d['host']) ;
            else 
                return $b['scheme']."://".strstr($c,$b['host']) ;
        }
    }


    function detect_label($cit_id=0,$path='',$crawl_title,$translate=0)
    {

        // return;
        

        if (empty($cit_id) OR $cit_id < 1) {
            show_404();
        }

        $citem = $this->Cmall_item_model->get_one($cit_id);
        if ( ! element('cit_id', $citem)) {
            show_404();
        }

        # Your Google Cloud Platform project ID
        // $projectId = 'petproject-235609';

        # annotate the image
        $image = file_get_contents($path);
        $response = $this->imageAnnotator->labelDetection($image);
        $labels = $response->getLabelAnnotations();
        $translate_text=array();
        $convert_text=array();

        $target = 'ko';

        if ($labels) {
            
            foreach ($labels as $label) {

                // if($translate){
                //     $translation = $this->translate->translate($label->getDescription(), [
                //         'target' => $target
                //     ]);
                    
                //     array_push($translate_text,$translation['text']);
                
                // } else {

                    array_push($translate_text,$label->getDescription());
                // }        
            }
            
                
            
        } else {
            return 'No label found';
        }
        
        
        

        $this->imageAnnotator->close();
        
        return $translate_text;



        
    }     

    


    function detect_tag($cit_id=0,$path='',$crawl_title='',$translate=0)
    {
        // return;
        
        // $mecab = new \MeCab\Tagger(array('-d', '/usr/local/lib/mecab/dic/mecab-ko-dic'));

        if (empty($cit_id) OR $cit_id < 1) {
            show_404();
        }

        $citem = $this->Cmall_item_model->get_one($cit_id);
        if ( ! element('cit_id', $citem)) {
            show_404();
        }

        # Your Google Cloud Platform project ID
        // $projectId = 'petproject-235609';

        # annotate the image
        $image = file_get_contents($path);

        $response = $this->imageAnnotator->textDetection($image);
        $texts = $response->getTextAnnotations();
        $translate_text=array();
        $convert_text=array();
        $all_category=array();
        $row_language=array();
        // $all_category = $this->Cmall_category_model->get_all_category();

        $target = 'ko';

        if ($texts) {
            
             
            
            foreach ($texts as $text) {

                $naturalentity =array();
                $naturalentity_word = '';

                if(strlen($text->getDescription()) < 10) continue;
                
                $row_language[] = $text->getDescription();
                

                // foreach ($language->entities() as $entity) {
                //      $naturalentity[$entity['name']] = $entity['name'];

                // }
                
                // foreach ($naturalentity as $val) {

                    

                //     $naturalentity_word .= $val;
                // }

                // if($translate){
                //     $translation = $this->translate->translate($text->getDescription(), [
                //         'target' => $target
                //     ]);
                    
                //     array_push($translate_text,$translation['text']);
                
                // } else {
                    
                    
                //     foreach($this->tag_word as $word){
                //         foreach ($naturalentity as $val) {
                //             // if(strpos(strtolower($val),strtolower(str_replace(" ","",$word))) !== false ){
                //             if(strtolower(str_replace(" ","",$val)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){                                
                //                 if(!in_array(element('tgw_value',$word),$translate_text))
                //                     array_push($translate_text,element('tgw_value',$word));       
                //             } 
                //         }
                //     }
                    
                // }       

                
               
            }
            
                
            
        } else {
            return 'No label found';
        }
        
        

        $this->imageAnnotator->close();
        
        return implode("\n",$row_language);



        
        }   

    // function getSubstring($str, $length)
    // {
    //     $str = trim($str);
    //     if (strlen($str) <= $length)
    //         return $str;
    //     $strArr = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    //     $cutStr = '';
    //     $i=0;
    //     foreach ($strArr as $s) {
    //         $len1 = strlen($s);
    //         $len2 = strlen($cutStr) + $len1;
    //         if ($len2 > $length)
    //             break;
    //         else
    //             $cutStr .= $s;
    //         $i++;
    //     }
    //     return $cutStr;
    // }

    function getnaturallanguage($row_tag=array())
    {


        // return;
        

        
        $translate_text=array();
            
            
        
        $naturalentity_ =array();
        if($row_tag['cit_name']){
            $language_ = explode(" ",$row_tag['cit_name']);

            foreach ($language_ as $entity) {
                 $naturalentity_[$entity] = $entity;
            }
        }
        
        if($row_tag){
            $language_ = $this->naturallanguage->analyzeEntities(implode("\n",$row_tag));

            
        }

        foreach ($language_->entities() as $entity) {
            $naturalentity_[$entity['name']] = $entity['name'];
        }

        // foreach($this->tag_word as $word){
        //     foreach ($naturalentity_ as $val) {
        //         $arr_str = preg_split("//u", element('tgw_value',$word), -1, PREG_SPLIT_NO_EMPTY);
                
        //         if(count($arr_str) > 1){
        //             if(strpos(strtolower(str_replace(" ","",$val)),strtolower(str_replace(" ","",element('tgw_value',$word)))) !== false ){
        //                 if(!in_array(element('tgw_value',$word),$translate_text))
        //                     array_push($translate_text,element('tgw_value',$word));       
        //             }     
        //         } else {
        //             if(strtolower(str_replace(" ","",$val)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){
        //                 if(!in_array(element('tgw_value',$word),$translate_text))
        //                     array_push($translate_text,element('tgw_value',$word));       
        //             }     
        //         }
                
                
        //     }
            
            
        // }

        
        
        return $naturalentity_;



        
    }   

    function label_tag_convert($cit_id,$translate_text=array(),$crawl_title)
    {



        $this->Crawl_tag_model->get('','',array('cit_id' => $cit_id, 'cta_tag' => 'aaa'));

        $find_letters=array(
                        'aaa' =>array('aaa'=>'aaa'
                                )

                        );
        

        if(strpos($string, $translate_text) !== false)
        {
            echo 'All the letters are found in the string!';
        }       
        
        return $translate_text;



        
    }   

    function getMsgArr($msg) {
       // $convMsg = mb_convert_encoding($msg, "UTF-8", "EUC-KR");

        $convMsg = $msg;
       $resultArr = array();


       $pattern = '/[\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}a-zA-Z]+/u';
       preg_match_all($pattern,$convMsg,$match);
       // $resultArr[3] = mb_convert_encoding(implode('',$match[0]),"EUC-KR", "UTF-8");
       $resultArr[3] = implode('',$match[0]);

       return $resultArr;
   }

    function http_path_to_url($path, $base_uri) 
    { 
        if (preg_match("@^[a-z]{1}[a-z0-9\+\-\.]+:@i", $path)) return $path; 
        else if ($path=="") return $base_uri; 

        $base_a = parse_url($base_uri); 
        $base_a['shp']  = substr($base_uri, 0, strlen($base_uri) - strlen($base_a['path'].(isset($base_a['query']) ? '?'.$base_a['query'] : '').(isset($base_a['fragment']) ? '#'.$base_a['fragment'] : ''))); 

        if (preg_match("@^//@i", $path)) { 
            return $base_a['scheme'].":".$path; 
        } else if (preg_match("@^\?@", $path)) { 
            return $base_a['shp'].$base_a['path'].$path; 
        } else if (preg_match("@^#@", $path)) { 
            return preg_replace("@#$@", "", substr($base_uri, 0, strlen($base_uri)-strlen($base_a['fragment']))).$path; 
        } else { 
            if (preg_match("@^(/\.+)+@", $path)) { 
                return $base_a['shp'].$path; 
            } else { 
                if ($path[0]!="/" && isset($base_a['path']) && $base_a['path']!='') { 
                    $base_a['file'] = str_replace('/', '', strrchr($base_a['path'], '/')); // 파일명 
                    if (!preg_match("@/@", $base_a['path'])) $base_a['file'] = $base_a['path']; // 파일 만으로 되어 있을 경우 위에서 "/" 검색이 안 되므로 
                    $base_a['dir']  = substr($base_a['path'], 0, strlen($base_a['path']) - strlen($base_a['file'])); // 디렉토리, "/" 포함 
                } 

                // 2007-06 : query에 프로토콜이 있을 경우 parse_url가 제대로 작동하지 않으므로 임시 변환 부분 추가 
                if (preg_match("@[a-z]{1}[a-z0-9\+\-\.]+:[/]{2,}@i", $path)) { 
                    $md5    = md5(microtime()).md5(microtime()); 
                    $path   = str_replace("://", ":__".$md5."__/__/", $path); 
                    $op_a   = parse_url($path); 
                    $op_a['query']  = str_replace(":__".$md5."__/__/", "://", $op_a['query']); 
                    $path   = str_replace(":__".$md5."__/__/", "://", $path); 
                } else { 
                    $op_a   = parse_url($path); 
                } 

                $base_a['dir'] = empty($base_a['dir']) ? '':$base_a['dir'];
                $op_a['path'] = empty($op_a['path']) ? '':$op_a['path'];

                $tp_a   = explode("/", $base_a['dir'].$op_a['path']); 
                $tp_c   = count($tp_a); 
                $ap_a   = array(); 
                for ($i=0; $i < $tp_c; $i++) { 
                    if ($tp_a[$i]=="..") { 
                        if (count($ap_a) >= 1) $ap_a = array_slice($ap_a, 0, count($ap_a)-1); 
                        if ($i==$tp_c-1) $ap_a[] = ""; // 마지막일 경우 
                    } else if ($tp_a[$i]==".") { 
                        if ($i==$tp_c-1) $ap_a[] = ""; // 마지막일 경우 
                    } else { 
                        $ap_a[] = $tp_a[$i]; 
                    } 
                } 

                $ap = implode("/", $ap_a); 
                if (!preg_match("@^/@", $ap)) $ap = "/".$ap; 

                return $base_a['shp'] .$ap .(isset($op_a['query']) ? '?'.$op_a['query'] : '') .(isset($op_a['fragment']) ? '#'.$op_a['fragment'] : ''); 
            } 
        } 
    } 

    
   

    function crawling_checking()
    {}


    function crawling_item_update($crawl_key,$crawl_mode,$crawl_type)
    {   

        if(empty($crawl_key) && empty($crawl_mode) && empty($crawl_type))
            show_404();


        $this->load->model('Board_model');

        switch ($crawl_mode) {
            case 'all':
                $where = array(
                            'brd_search' => 1,
                        );
                break;
            case 'group':
                $where = array(
                            'bgr_id' => $crawl_key,
                        );
                break;
            case 'board':
                $where = array(
                            'brd_id' => $crawl_key,
                        );
                break;
            
            case 'post':
                $where = array(
                            'post_id' => $crawl_key,
                        );
                break;

            case 'item':
                $where = array(
                            'cit_id' => $crawl_key,
                        );
                break;

            default:
                show_404();
                break;
        }
        
        
        


        if( $crawl_mode === 'post'){
            
            $where['post_del <>'] = 2;
            

            
            $post = $this->Post_model
                ->get_one('', '', $where);                

                    if($crawl_type==='update'){
                        $this->crawling_update(element('post_id', $post),element('brd_id', $post));
                    } 

                    if($crawl_type==='overwrite'){
                        $this->crawling_overwrite(element('post_id', $post),element('brd_id', $post));
                    }
                    if($crawl_type==='tag_update'){
                        
                        $this->crawling_tag_update(element('post_id', $post),element('brd_id', $post));
                    }
                    if($crawl_type==='tag_overwrite'){
                        
                        $this->crawling_tag_overwrite(element('post_id', $post),element('brd_id', $post));
                    }
                    if($crawl_type==='vision_api_label'){
                        
                        $this->vision_api_label(element('post_id', $post),element('brd_id', $post));
                    }
                    if($crawl_type==='category_update'){
                        $this->crawling_category_update(element('post_id', $post),element('brd_id', $post));
                    }
                
        }elseif($crawl_mode === 'item'){
            $select = 'cit_id,post_id, brd_id';
            $cmall = $this->Cmall_item_model->get_one('', $select,$where);        

                    if($crawl_type==='update'){
                        $this->crawling_update(element('post_id', $cmall),element('brd_id', $cmall));
                    } 

                    if($crawl_type==='overwrite'){
                        $this->crawling_overwrite(element('post_id', $cmall),element('brd_id', $cmall));
                    }
                    if($crawl_type==='tag_update'){
                        
                        $this->crawling_tag_update(element('post_id', $cmall),element('brd_id', $cmall),element('cit_id', $cmall));
                    }
                    if($crawl_type==='tag_overwrite'){
                        
                        $this->crawling_tag_overwrite(element('post_id', $cmall),element('brd_id', $cmall),element('cit_id', $cmall));
                    }
                    if($crawl_type==='vision_api_label'){
                        
                        $this->vision_api_label(element('post_id', $cmall),element('brd_id', $cmall),element('cit_id', $cmall));
                    }
                    if($crawl_type==='category_update'){
                        $this->crawling_category_update(element('post_id', $cmall),element('brd_id', $cmall),element('cit_id', $cmall));
                    }
        } else {

            $board_id = $this->Board_model->get_board_list($where);
            $board_list = array();
            if ($board_id && is_array($board_id)) {
                foreach ($board_id as $key => $val) {

                    if($crawl_type==='update'){
                        $this->crawling_update(0,element('brd_id', $val));
                    } 

                    if($crawl_type==='overwrite'){
                        $this->crawling_overwrite(0,element('brd_id', $val));
                    }
                    if($crawl_type==='tag_update'){
                        
                        $this->crawling_tag_update(0,element('brd_id', $val));
                    }
                    if($crawl_type==='tag_overwrite'){
                        
                        $this->crawling_tag_overwrite(0,element('brd_id', $val));
                    }
                    if($crawl_type==='vision_api_label'){
                        
                        $this->vision_api_label(0,element('brd_id', $val));
                    }
                    if($crawl_type==='category_update'){
                        $this->crawling_category_update(0,element('brd_id', $val));
                    }
                }
            }
        }

            
    }

    function category_check($value,$array)
    {   
        $v_arr = explode('/',$value);

        foreach($v_arr as $v){            
            if(in_array($v,$array))
                return true;
        }
        

    }

    function crawl_tag_to_category($cca_text,$crawl_tag_text)
    {   
        $cca_text_arr = explode(',',$cca_text);

        foreach($cca_text_arr as $c_value){

            foreach($crawl_tag_text as $t_value){

                $cta_tag = preg_split("//u", $t_value, -1, PREG_SPLIT_NO_EMPTY);
                
                
                
                if(strtolower($c_value) === strtolower($t_value))
                    return true;
            }
        }
        

    }

    function crawl_tag_to_attr($cat_text,$crawl_tag_text)
    {   
        $cat_text_arr = explode(',',$cat_text);

        foreach($cat_text_arr as $c_value){

            foreach($crawl_tag_text as $t_value){

                $cta_tag = preg_split("//u", $t_value, -1, PREG_SPLIT_NO_EMPTY);
                
                
                
                if(strtolower($c_value) === strtolower($t_value))
                    return true;
            }
        }
        

    }

    function cmall_brand($brand_word)
    {   
        

        $arr_str = preg_split("//u", $brand_word, -1, PREG_SPLIT_NO_EMPTY);
                
        

        // $brand_word = strtolower(str_replace(" ","",$brand_word));
        $result = $this->Cmall_brand_model->get();
        if($brand_word){
            foreach($result as $value){
                

                // if(element('cbr_value_en',$value) && strpos($brand_word,str_replace(" ","",strtolower(element('cbr_value_en',$value))))!== false)
                //     return element('cbr_id',$value);
                // if(element('cbr_value_kr',$value) && strpos($brand_word,str_replace(" ","",element('cbr_value_kr',$value)))!== false)
                //     return element('cbr_id',$value);
                // if(element('cbr_value_kr',$value) && strpos(str_replace(" ","",element('cbr_value_kr',$value)),$brand_word)!== false)
                //     return element('cbr_id',$value);
                // if(element('cbr_value_en',$value) && strpos(str_replace(" ","",strtolower(element('cbr_value_en',$value))),$brand_word) !== false )
                //     return element('cbr_id',$value);


                // if(count($arr_str) > 1){

                    
                    
                    

                    $cbr_value_en = preg_split("//u", element('cbr_value_en',$value), -1, PREG_SPLIT_NO_EMPTY);
                
                    if(count($cbr_value_en) > 3){
                        if(element('cbr_value_en',$value) && strpos(strtolower(cut_str($brand_word, count(preg_split("//u", element('cbr_value_en',$value), -1, PREG_SPLIT_NO_EMPTY))+2)),strtolower(element('cbr_value_en',$value)))!== false)
                            return element('cbr_id',$value);
                        if(element('cbr_value_en',$value) && strpos(strtolower(cut_str(element('cbr_value_en',$value), count(preg_split("//u",$brand_word , -1, PREG_SPLIT_NO_EMPTY))+2)),strtolower($brand_word)) !== false )
                            return element('cbr_id',$value);
                    } else {
                        if(element('cbr_value_en',$value) && strtolower(cut_str($brand_word, count(preg_split("//u", element('cbr_value_en',$value), -1, PREG_SPLIT_NO_EMPTY))+2)) === strtolower(element('cbr_value_en',$value)))
                            return element('cbr_id',$value);
                        if(element('cbr_value_en',$value) && strtolower(cut_str(element('cbr_value_en',$value), count(preg_split("//u",$brand_word , -1, PREG_SPLIT_NO_EMPTY))+2)) === strtolower($brand_word)) 
                            return element('cbr_id',$value);
                    }
                    $arr_str_kr = preg_split("//u", element('cbr_value_kr',$value), -1, PREG_SPLIT_NO_EMPTY);


                    

                    

                    if(count($arr_str_kr) > 2){
                        if(element('cbr_value_kr',$value) && strpos(strtolower(cut_str($brand_word, count(preg_split("//u", element('cbr_value_kr',$value), -1, PREG_SPLIT_NO_EMPTY))+2)),strtolower(element('cbr_value_kr',$value)))!== false)
                            return element('cbr_id',$value);
                        if(element('cbr_value_kr',$value) && strpos(strtolower(cut_str(element('cbr_value_kr',$value), count(preg_split("//u",$brand_word , -1, PREG_SPLIT_NO_EMPTY))+2)),strtolower($brand_word))!== false)
                            return element('cbr_id',$value);
                    } else {
                        if(element('cbr_value_kr',$value) && strtolower(cut_str($brand_word, count(preg_split("//u", element('cbr_value_kr',$value), -1, PREG_SPLIT_NO_EMPTY))+2)) === strtolower(element('cbr_value_kr',$value)))
                            return element('cbr_id',$value);
                        if(element('cbr_value_kr',$value) && strtolower(cut_str(element('cbr_value_kr',$value), count(preg_split("//u",$brand_word , -1, PREG_SPLIT_NO_EMPTY))+2)) === strtolower($brand_word)) 
                            return element('cbr_id',$value);
                    }
                    
                // } else {
                //     if(element('cbr_value_en',$value) && strtolower($brand_word) === strtolower(element('cbr_value_en',$value)))
                //         return element('cbr_id',$value);
                //     if(element('cbr_value_kr',$value) && strtolower($brand_word) === strtolower(element('cbr_value_kr',$value)))
                //         return element('cbr_id',$value);
                    
                // }
            }
        }

         
        
    }

    public function get_extension($filename)
    {
        $x = explode('.', $filename);

        if (count($x) === 1)
        {
              return '';
        }

        $ext = end($x);
        return $ext;
    }

    public function get_storelist()
    {
        $this->output->set_content_type('application/json');
        $this->load->model(array('Board_model'));

        $where = array(
            'brd_blind' => 0,
        );
        $result = $this->Board_model->get_crawl_list($where);
        $data = array();
                    $storelist = array(
'https://smartstore.naver.com/heedong',
'https://smartstore.naver.com/petworldstore',
'https://smartstore.naver.com/marcostore',
'https://smartstore.naver.com/thesoy',
'https://smartstore.naver.com/0002shop',
'https://smartstore.naver.com/my_fluffy',
'https://smartstore.naver.com/wandookongmom',
'https://smartstore.naver.com/petsoban',
'https://smartstore.naver.com/rayvonne',
'https://smartstore.naver.com/dajunghanmarket',
'https://smartstore.naver.com/pop_and_puppy',
'https://smartstore.naver.com/barbichon',
'https://smartstore.naver.com/triplebees',
'https://smartstore.naver.com/groomingdale',
'https://smartstore.naver.com/bandalpet',
'https://smartstore.naver.com/chocopet',
'https://smartstore.naver.com/mungsday_party',
'https://smartstore.naver.com/dd2planet',
'https://smartstore.naver.com/i_love_to_eat',
'https://smartstore.naver.com/market-ben',
'https://smartstore.naver.com/lilalovesit',
'https://smartstore.naver.com/matroospetshop',
'https://smartstore.naver.com/smilepet',
'https://smartstore.naver.com/peakrise',
'https://smartstore.naver.com/maykit',
'https://smartstore.naver.com/jbwoody',
'https://smartstore.naver.com/cherrypet',
'https://smartstore.naver.com/chunilcarpet/products/2769620857',
'https://smartstore.naver.com/hyponic/category/fe53dade98044ff2b8f73b298baddddb?cp=1',
'https://smartstore.naver.com/collie',
'https://smartstore.naver.com/wellcare_pet',
'https://smartstore.naver.com/uglobal',
'https://smartstore.naver.com/oguogu_',
'https://smartstore.naver.com/habit_',
'https://smartstore.naver.com/ruruandmimi',
'https://smartstore.naver.com/breezytail',
'https://smartstore.naver.com/benebone',
'https://smartstore.naver.com/robedog',
'https://smartstore.naver.com/deilive/category/50000155?cp=1',
'https://smartstore.naver.com/gucci0209',
'https://smartstore.naver.com/greydog',
'https://smartstore.naver.com/byglam/category/f4b6025ef5714a8d822ecadb8a231f82?cp=1',
'https://smartstore.naver.com/carrepetian',
'https://smartstore.naver.com/colettemungmung',
'https://smartstore.naver.com/marafiki_official',
'https://smartstore.naver.com/lattefood',
'https://smartstore.naver.com/momsmind123',
'https://smartstore.naver.com/alldayorganic',
'https://smartstore.naver.com/mabelles',
'https://smartstore.naver.com/starpets',
'https://smartstore.naver.com/cozyrosyday',
'https://smartstore.naver.com/riruri_jmom',
'https://smartstore.naver.com/moodonpet',
'https://smartstore.naver.com/ribbongbong',
'https://smartstore.naver.com/petpang',
'https://smartstore.naver.com/mymarie',
'https://smartstore.naver.com/dellalola',
'https://smartstore.naver.com/munglab',
'https://smartstore.naver.com/mong_n_cheek',
'https://smartstore.naver.com/mongtailor',
'https://smartstore.naver.com/ruhena',
'https://smartstore.naver.com/looloofit',
'https://smartstore.naver.com/krono',
'https://smartstore.naver.com/timbiriche',
'https://smartstore.naver.com/woolly',
'https://smartstore.naver.com/pepemall',
'https://smartstore.naver.com/awesomepuppy',
'https://smartstore.naver.com/littledeus',
'https://smartstore.naver.com/kuma1231',
'https://smartstore.naver.com/littlepaw',
'https://smartstore.naver.com/dancingdogginuts',
'https://smartstore.naver.com/zamboakr',
'https://smartstore.naver.com/myvef',
'https://smartstore.naver.com/gansikmeokdog',
'https://smartstore.naver.com/dogcat2',
'https://smartstore.naver.com/gaebabking',
'https://smartstore.naver.com/dajunghagae',
'https://smartstore.naver.com/doggune?NaPm=ct%3Dk6by3qcw%7Cci%3Dcheckout%7Ctr%3Dds%7Ctrx%3D%7Chk%3D6a92852d3dbc644eee9b649f248ad3ce5655eae5',
'https://smartstore.naver.com/romancedog',
'https://smartstore.naver.com/dm-',
'https://smartstore.naver.com/brooklynpetco?NaPm=ct%3Dk65udf9o%7Cci%3Dcheckout%7Ctr%3Dds%7Ctrx%3D%7Chk%3Dc16c520c26ea2002ab7d19b059b645e5ab42753d',
'https://smartstore.naver.com/blueneko',
'https://smartstore.naver.com/slowstep3',
'https://smartstore.naver.com/mbtq',
'https://smartstore.naver.com/cocker_sister',
'https://smartstore.naver.com/cocomn8642',
'https://smartstore.naver.com/titano',
'https://smartstore.naver.com/purefriends',
'https://smartstore.naver.com/fromb/category/7f79a8993de5493cb1fb881efd49e1d9?cp=1',
'https://smartstore.naver.com/langdog',
'https://smartstore.naver.com/petdodam0',
'https://smartstore.naver.com/pet-story',
'https://smartstore.naver.com/dogjeja',
'https://smartstore.naver.com/aa4070809',
'https://stellaandchewys.modoo.at/?link=au07evou',
'https://smallbatch.modoo.at/?link=7gtln3pc',
'https://noondog.modoo.at/?link=dm8uma4d',
'https://dogtoc.modoo.at/?link=1maty8ji',
'http://bonpuppy.com/',
'https://shopping.naver.com/pet/stores/100045370',
'https://shopping.naver.com/pet/stores/100144583',
'https://shopping.naver.com/pet/stores/100205785',
'https://shopping.naver.com/pet/stores/100161745',
'https://shopping.naver.com/pet/stores/100131988',
'https://shopping.naver.com/pet/stores/100167771',
'https://www.biteme.co.kr/main/index.php',
'https://www.fitpetmall.com/',
'http://www.gaenimshop.com/',
'http://ssfw.kr',
'http://www.diditable.com/',
'http://ainsoap.com/',
'http://www.gulliverdog.co.kr/main/index.php',
'http://www.edenchien.com/main/index.php',
'http://mytrianon.co.kr/shop/goods/goods_list.php?&category=003',
'http://www.petesthe.co.kr/main/index.php',
'http://bourdog.com/main/index.php',
'http://www.naturalex.co.kr/shop/main/index.php',
'http://vlab.kr/',
'https://www.wangzzang.com/',
'www.petgear.kr/',
'https://duit.kr/',
'https://eledog.co.kr/',
'http://andblank.com/home',
'https://yosemite.pet/',
'http://baumeo.net/33',
'https://www.varram.co.kr/all#',
'https://hipaw.co.kr',
'https://www.vuumpet.co.kr/',
'https://plumstudio.co.kr/78',
'http://vavox.co.kr/',
'http://gettouch.co.kr/',
'https://www.cheesesun.com/',
'http://www.oraeorae.com/',
'http://www.cocochien.kr/',
'http://www.hydewolf.co.kr/index.html',
'http://www.itsdog.com/index.html',
'http://www.petnoriter.co.kr/',
'http://www.ecofoam.co.kr/index.html',
'http://www.puppygallery.co.kr/index.html',
'http://www.amylovespet.co.kr/index.html',
'http://www.dermadog.co.kr/index.html',
'http://www.affetto.co.kr/index.html',
'http://howlpot.com/',
'https://www.smallstuff.kr/',
'https://www.guilty-pleasure.co.kr/shop',
'https://www.comercotte.com/',
'https://melonicoco.com/',
'https://www.ddoang.com/',
'https://pawunion.kr/shop',
'http://www.harryspet.com/',
'https://www.wilddog.co.kr/shop',
'https://bonjourtou-tou.com/',
'https://pethod.co.kr/',
'https://www.purplestore.co.kr/',
'http://www.dhuman.co.kr/view/main',
'http://www.montraum.com/common/process/shopmain.asp?iniCategory=2&thisCategory=22',
'http://www.petsandme.co.kr/main/main.php',
'http://gubas.co.kr/',
'http://www.bodeum.co.kr/html/shop/index.php',
'http://www.queenpuppy.co.kr/index.html',
'https://www.wekino.co.kr/products/category/6',
'https://dog114.kr/main/index',
'http://shop.i-avec.com',
'https://www.wconcept.co.kr/Life/001014',
'http://www.betterskorea.com/',
'http://m.rudolphshop.kr/',
'http://monchouchou.co.kr/',
'https://pethroom.com/',
'http://double-comma.com/product/list.html?cate_no=70',
'http://honestmeal.kr/',
'http://uglugl.com/',
'http://peppymeal.kr/',
'http://su-su.kr/index.html',
'https://aboutmeal.co.kr/',
'http://bowbowpet.com/',
'http://www.yolohollo.com/',
'http://beatto.kr/',
'https://munikund.com/index.html',
'http://terrylatte.com/',
'http://its-sunnyoutside.com/',
'http://littlecollin.kr/',
'http://opaaap.com/',
'http://www.hutsandbay.com/index.html',
'https://tustus.co.kr/',
'http://choandkang.com/',
'http://dfang.co.kr/',
'https://www.arrr.kr/index.html',
'http://bridge.dog/#&panel1-1',
'http://eyoushop.co.kr/',
'http://www.coteacote.kr/',
'http://dogshower.co.kr/',
'http://lora.kr/',
'http://buildapuppy.com/product/list.html?cate_no=59',
'http://www.inherent.co.kr/',
'http://pet-paradise.kr/index.html',
);



        $list_num = $result['total_rows'];

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                // $flag=true;
                // $b = parse_url(prep_url(rtrim(trim(element('brd_url',$val)), '/').'/'));
                
                
                // foreach ($storelist as $skey => $sval) {
                    
                //     $a = parse_url(prep_url(rtrim(trim($sval), '/').'/'));
                //     if($b['host'] == $a['host']){
                //         $flag = false;
                //         if($b['path'] == $a['path']){
                            
                //         }                        
                        
                //     }

                //     if(!$flag) break;
                    
                // }
                // if($flag) {              
                //     continue;
                // }else {
                    
                    $data['list'][$key]['brd_id'] = element('brd_id',$val);
                    $data['list'][$key]['brd_name'] = element('brd_name',$val);
                    $data['list'][$key]['brd_url'] = element('brd_url',$val);                    
                    $data['list'][$key]['brd_comment'] = element('brd_comment',$val);                  
                // }
                
                
            }
        }
        

        // foreach ($storelist as $skey => $sval) {

        //     $flag=true;
        //         $b = parse_url(prep_url(rtrim(trim($sval), '/').'/'));
                
                
            
        //                 foreach($data['list']  as $val){                    
        //             $a = parse_url(prep_url(rtrim(trim(element('brd_url',$val)), '/').'/'));
        //             if($b['host'] == $a['host']){
        //                 $flag = false;
        //                 if($b['path'] == $a['path']){
                            
        //                 }                        
                        
        //             }

                    
                    
        //         }
        //         if($flag) echo $sval."<br>";
        // }



        // if ($storelist) {
        //     foreach ($storelist as $skey => $sval) {
                
        //         $flag=true;
                
        //         $a = parse_url(prep_url(rtrim(trim($sval), '/').'/'));
                
        //         foreach (element('list', $result) as $key => $val) {
        //             $b = parse_url(prep_url(rtrim(trim(element('brd_url',$val)), '/').'/' ));    
                    
        //             if($b['host'] == $a['host']){                       
                        
                        
        //                     if($b['path'] == $a['path']){
        //                     // $flag = false;
        //                     }                           
        //                   $flag = false;
        //             }

        //             if(!$flag) break;
                    
        //         }
        //         if(!$flag) continue;
        //         else {
        //             echo $sval."<br>";
        //         }
                
                
        //     }
        // }
        
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
        
    }

    public function insert_itemlist($brd_id = 0,$crw_id = 0)
    {
        
        $this->output->set_content_type('application/json');
        if (empty($brd_id)) {
            $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if(empty($crw_id)){
            if (empty($this->input->post('crw_name'))) {
                $result = array('resultcode'=>1002,'message' => 'crw_name 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }


            if ($this->input->post('crw_price') == '') {
                $result = array('resultcode'=>1004,'message' => 'crw_price 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            if (!isset($_FILES['crw_file_1'])) {
                $result = array('resultcode'=>1005,'message' => 'crw_file_1 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            if ($this->input->post('crw_is_soldout') == '') {
                $result = array('resultcode'=>1006,'message' => 'crw_is_soldout 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            if ($this->input->post('crw_goods_code')=='') {
                $result = array('resultcode'=>1007,'message' => 'crw_goods_code 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            if (empty($this->input->post('crw_post_url'))) {
                $result = array('resultcode'=>1008,'message' => 'crw_post_url 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            if ($this->input->post('crw_price_sale') == '') {
                $result = array('resultcode'=>1009,'message' => 'crw_price_sale 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            if (empty($this->input->post('crw_category1'))) {
                $result = array('resultcode'=>1010,'message' => 'crw_category1 가 없습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            $pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9])+/';
            $str = $this->input->post('crw_category1');
            
            preg_match_all($pattern, $str, $match);
            $crw_category1 = implode('', $match[0]);
            




            $DB2 = $this->load->database('db2', TRUE);
            
            $DB2->where(array('brd_id' => $brd_id ,'crw_goods_code' => $this->input->post('crw_goods_code')));

            

            if ($DB2->count_all_results('crawl_item')) {
                $result = array('resultcode'=>1007,'message' => '중복된 crw_goods_code 가 존재 합니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }


            $this->load->library('upload');
            
            for ($k = 1; $k <= 10; $k++) {
                if (isset($_FILES) && isset($_FILES['crw_file_' . $k]) && isset($_FILES['crw_file_' . $k]['name']) && $_FILES['crw_file_' . $k]['name']) {
                    $upload_path = config_item('uploads_dir') . '/crawlitem/';
                    if (is_dir($upload_path) === false) {
                        mkdir($upload_path, 0707);
                        $file = $upload_path . 'index.php';
                        $f = @fopen($file, 'w');
                        @fwrite($f, '');
                        @fclose($f);
                        @chmod($file, 0644);
                    }
                    $upload_path .= $brd_id . '/';
                    if (is_dir($upload_path) === false) {
                        mkdir($upload_path, 0707);
                        $file = $upload_path . 'index.php';
                        $f = @fopen($file, 'w');
                        @fwrite($f, '');
                        @fclose($f);
                        @chmod($file, 0644);
                    }
                    $upload_path .= $crw_category1 . '/';
                    if (is_dir($upload_path) === false) {
                        mkdir($upload_path, 0707);
                        $file = $upload_path . 'index.php';
                        $f = @fopen($file, 'w');
                        @fwrite($f, '');
                        @fclose($f);
                        @chmod($file, 0644);
                    }

                    $uploadconfig = array();
                    $uploadconfig['upload_path'] = $upload_path;
                    $uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif';
                    $uploadconfig['max_size'] = '10000';
                    $uploadconfig['encrypt_name'] = true;

                    $this->upload->initialize($uploadconfig);

                    if ($this->upload->do_upload('crw_file_' . $k)) {
                        $img = $this->upload->data();
                        $crw_file[$k] = $brd_id . '/' . $crw_category1 . '/' . element('file_name', $img);
                    } else {
                        $file_error = $this->upload->display_errors();
                        return $file_error;
                        // break;

                    }
                }
            }

           

            $crw_name = $this->input->post('crw_name');
            $crw_price = $this->input->post('crw_price');
            $crw_post_url = $this->input->post('crw_post_url');
            $crw_goods_code = $this->input->post('crw_goods_code');
            $crw_is_soldout = $this->input->post('crw_is_soldout');        
            $crw_price_sale = $this->input->post('crw_price_sale');

            $DB2 = $this->load->database('db2', TRUE);

            $updatedata = array(
                
                'crw_name' => $crw_name,
                'crw_price' => $crw_price ,
                'crw_datetime' => cdate('Y-m-d H:i:s'),
                'crw_updated_datetime' => cdate('Y-m-d H:i:s'),
                'crw_post_url' => $crw_post_url,
                'brd_id' => $brd_id,
                'crw_goods_code' => $crw_goods_code,
                'crw_is_soldout' => $crw_is_soldout,
                'crw_price_sale' => $crw_price_sale ,
                
            );

            for ($k = 1; $k <= 10; $k++) {
                if (isset($crw_file[$k]) && $crw_file[$k]) {
                    $updatedata['crw_file_' . $k] = $crw_file[$k];
                }
            }

            for ($k = 1; $k <= 6; $k++) {
                if (!empty($this->input->post('crw_brand' . $k))) {
                    $updatedata['crw_brand' . $k] = $this->input->post('crw_brand' . $k);
                }
            }

            for ($k = 1; $k <= 3; $k++) {
                if (!empty($this->input->post('crw_category' . $k))) {
                    $pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9])+/';
                    $str = $this->input->post('crw_category' . $k);
                    
                    preg_match_all($pattern, $str, $match);
                    $updatedata['crw_category' . $k] = implode('', $match[0]);
                    
                }
            }

            $DB2->insert('crawl_item', $updatedata);
            $crw_id = $DB2->insert_id();
            

            if(empty($crw_id)){
                $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.','crw_id' => $crw_id);
        } else {

            $DB2 = $this->load->database('db2', TRUE);
            $DB2->from('crawl_item');
            if ($crw_id) {
                $DB2->where('crw_id', $crw_id);
            }
            
            
            $DB2->limit(1,0);
            
            $result = $DB2->get();
            $crawl_item = $result->row_array();

            if (empty(element('crw_id',$crawl_item))) {
                $result = array('resultcode'=>1000,'message' => '없는 crw_id 입니다..');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            

            $updatedata = array(
                'crw_updated_datetime' => cdate('Y-m-d H:i:s'),
                'is_del' => 0,
            );

            for ($k = 1; $k <= 6; $k++) {
                if (!empty($this->input->post('crw_brand' . $k))) {
                    $updatedata['crw_brand' . $k] = $this->input->post('crw_brand' . $k);
                }
            }

            for ($k = 1; $k <= 3; $k++) {
                if (!empty($this->input->post('crw_category' . $k))) {

                    $pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9])+/';
                    $str = $this->input->post('crw_category' . $k);
                    
                    preg_match_all($pattern, $str, $match);
                    $updatedata['crw_category' . $k] = implode('', $match[0]);

                    
                }
            }

            $array = array(
                'crw_name', 'crw_price', 'crw_post_url', 'crw_goods_code',
                'crw_price_sale'
            );
            foreach ($array as $value) {
                if(!empty($this->input->post($value)))
                    $updatedata[$value] = $this->input->post($value);
            }
            if($this->input->post('crw_is_soldout') !=='')
                $updatedata['crw_is_soldout'] = $this->input->post('crw_is_soldout');
            

            if ( ! empty($updatedata)) {
                if ( ! empty($crw_id)) {
                    $DB2->where('crw_id', $crw_id);
                }
                
                $DB2->set($updatedata);
                $crw_id = $DB2->update('crawl_item');

                if(empty($crw_id)){
                    $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
                    exit(json_encode($result,JSON_UNESCAPED_UNICODE));
                }
            } else {
                $result = array('resultcode'=>9000,'message' => 'updatedata 에 오류가 발생하였습니다.');
                    exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
        }

	
	exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }

    public function insert_itemdetail($brd_id , $crw_id)
    {
        $this->output->set_content_type('application/json');
        if (empty($crw_id)) {
            $result = array('resultcode'=>1000,'message' => 'crw_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($brd_id)) {
            $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $DB2 = $this->load->database('db2', TRUE);
        $DB2->from('crawl_item');
        if ($crw_id) {
            $DB2->where('crw_id', $crw_id);
        }
        
        
        $DB2->limit(1,0);
        
        $result = $DB2->get();
        $crawl_item = $result->row_array();

        
        if (empty(element('crw_id',$crawl_item))) {
            $result = array('resultcode'=>1000,'message' => '없는 crw_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

       if (element('brd_id', $crawl_item) !== $brd_id) {
            $result = array('resultcode'=>1001,'message' => '잘못된 brd_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $this->load->library('upload');
        $file = json_encode($_FILES);
            $post = json_encode($_POST);
            log_message('error', $file);
            log_message('error', $post);
        for ($k = 1; $k <= 10; $k++) {
            if (isset($_FILES) && isset($_FILES['cdt_file_' . $k]) && isset($_FILES['cdt_file_' . $k]['name']) && $_FILES['cdt_file_' . $k]['name']) {
                $upload_path = config_item('uploads_dir') . '/crawlitemdetail/';
                if (is_dir($upload_path) === false) {
                    mkdir($upload_path, 0707);
                    $file = $upload_path . 'index.php';
                    $f = @fopen($file, 'w');
                    @fwrite($f, '');
                    @fclose($f);
                    @chmod($file, 0644);
                }
                $upload_path .= $brd_id . '/';
                if (is_dir($upload_path) === false) {
                    mkdir($upload_path, 0707);
                    $file = $upload_path . 'index.php';
                    $f = @fopen($file, 'w');
                    @fwrite($f, '');
                    @fclose($f);
                    @chmod($file, 0644);
                }
                $upload_path .= element('crw_category1',$crawl_item) . '/';
                if (is_dir($upload_path) === false) {
                    mkdir($upload_path, 0707);
                    $file = $upload_path . 'index.php';
                    $f = @fopen($file, 'w');
                    @fwrite($f, '');
                    @fclose($f);
                    @chmod($file, 0644);
                }

                $uploadconfig = array();
                $uploadconfig['upload_path'] = $upload_path;
                $uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif';
                // $uploadconfig['max_size'] = '10000';
                $uploadconfig['encrypt_name'] = true;

                $this->upload->initialize($uploadconfig);

                if ($this->upload->do_upload('cdt_file_' . $k)) {
                    $img = $this->upload->data();
                    $cdt_file[$k] = $brd_id . '/' . element('crw_category1',$crawl_item) . '/' . element('file_name', $img);
                    
                } else {
                    $file_error = $this->upload->display_errors();
                    return $file_error;
                    // break;

                }
            }
        }

       

        $cdt_content = $this->input->post('cdt_content');

        $DB2 = $this->load->database('db2', TRUE);

        

        $updatedata = array(
            'crw_id' => $crw_id,
            'cdt_content' => $cdt_content,
            'brd_id' => $brd_id,
            'cdt_datetime' => cdate('Y-m-d H:i:s'),
            'cdt_updated_datetime' => cdate('Y-m-d H:i:s'),
            
        );

        for ($k = 1; $k <= 10; $k++) {
            if (isset($cdt_file[$k]) && $cdt_file[$k]) {
                $updatedata['cdt_file_' . $k] = $cdt_file[$k];
            }
        }

        for ($k = 1; $k <= 6; $k++) {
            if (!empty($this->input->post('cdt_brand' . $k))) {
                $updatedata['cdt_brand' . $k] = $this->input->post('cdt_brand' . $k);
            }
        }

        $DB2->insert('crawl_detail', $updatedata);
        $cdt_id = $DB2->insert_id();
        

        if(empty($cdt_id)){
            $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        

        $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    
    }

    public function get_itemlist($brd_id = 0, $crw_id = 0)
    {   

        $this->output->set_content_type('application/json');
        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $DB2 = $this->load->database('db2', TRUE);
        $DB2->select('crawl_item.*');
        $DB2->from('crawl_item');

        

        $where = array();
        if($brd_id){
            $where['brd_id'] = $brd_id;
        }

        if($crw_id){
            $where['crw_id'] = $crw_id;
        }
        
        $DB2->where($where);
        // $this->db2->limit($limit);
        
        $qry = $DB2->get();
        $res = $qry->result_array();
        
        $result = array('brd_id'=>$brd_id,'item' => $res);

        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }

    public function get_itemdetail($brd_id, $cdt_id = 0)
    {   
        $this->output->set_content_type('application/json');
        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

         $DB2 = $this->load->database('db2', TRUE);
        $DB2->select('cb_crawl_detail.*');
        $DB2->from('cb_crawl_detail');
        
        $where = array(
            'brd_id' => $brd_id
        );
            
        if($cdt_id){
            $where['cdt_id'] = $cdt_id;
        }
        $DB2->where($where);
        // $this->db2->where($where);
        // $this->db2->limit($limit);
        
        $qry = $DB2->get();
        $result = $qry->result_array();
        
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }

    public function insert_order($brd_id  = 0,$mem_id = 0,$cor_order_no = '',$cor_key = '',$cor_pay_type = '')
    {   
        $this->output->set_content_type('application/json');
        $this->load->model(array('Member_model','Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model','Unique_id_model'));
        
        if (empty($brd_id)) {
            $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($cor_key)) {
            $result = array('resultcode'=>1008,'message' => 'cor_key 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($cor_order_no)) {
            $result = array('resultcode'=>1002,'message' => 'cor_order_no 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }


        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($mem_id)) {
            $result = array('resultcode'=>1000,'message' => 'mem_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        

        
        

        

        $member = $this->Member_model->get_by_memid($mem_id,'mem_id,mem_userid,mem_nickname,mem_email,mem_phone');


        if ( ! element('mem_id', $member)) {
            $result = array('resultcode'=>1000,'message' => '없는 mem_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }


        $order = $this->Cmall_order_model->get_one('','',array('brd_id' => $brd_id,'cor_order_no' =>$cor_order_no));
        if (element('cor_id', $order)) {

            $this->Cmall_order_model->delete(element('cor_id', $order));
            $this->Cmall_order_detail_model->delete_where(array('cor_id' => element('cor_id', $order)));
            // $result = array('resultcode'=>1003,'message' => '이미 존재하는 cor_order_no 입니다.');
            // exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        

        $unique_id = $this->Unique_id_model->get_id($this->input->ip_address());

        $cor_goods_code = array();
        $cod_count = array();
        $total_price_sum = (int) $this->input->post('total_price_sum',null,0);
        $cor_goods_code = $this->input->post('cor_goods_code',null,'');

        if ( ! is_array($cor_goods_code))
        {
            $cor_goods_code = array($cor_goods_code);
        }


        $cod_count = $this->input->post('cod_count',null,'');

        if ( ! is_array($cod_count))
        {
            $cod_count = array($cod_count);
        }

        $cor_content = $this->input->post('cor_content',null,'');
        $od_status = 'order'; //주문상태
        $cor_id = $unique_id;

        
            $post = json_encode($_POST);
        
            log_message('error', $post);

        

        $insertdata['cor_id'] = $cor_id;
        $insertdata['mem_id'] = $mem_id;
        $insertdata['mem_nickname'] = element('mem_nickname', $member,'');
        $insertdata['mem_email'] = element('mem_email', $member,'');
        $insertdata['mem_phone'] = element('mem_phone', $member,'');
        $insertdata['cor_pay_type'] = $cor_pay_type;
        $insertdata['cor_content'] = $cor_content;
        $insertdata['cor_ip'] = $this->input->ip_address();
        $insertdata['cor_useragent'] = $this->agent->agent_string();
        $insertdata['is_test'] = $this->cbconfig->item('use_pg_test');
        $insertdata['status'] = $od_status;
        $insertdata['cor_status'] = 0;
        $insertdata['cor_datetime'] = date('Y-m-d H:i:s');
        $insertdata['mem_realname'] = element('mem_nickname', $member,'');
        $insertdata['cor_total_money'] = $total_price_sum;        
        $insertdata['cor_key'] = urldecode($cor_key);
        $insertdata['cor_order_no'] = $cor_order_no;
        $insertdata['brd_id'] = $brd_id;
        
        

        
        $res = $this->Cmall_order_model->insert($insertdata);
        if ($res) {
            if($cor_goods_code && is_array($cor_goods_code)){
                foreach ($cor_goods_code as $key => $val) {
                    $item = $this->Cmall_item_model
                        ->get_one('', '',array('brd_id' => $brd_id,'cit_goods_code' => $val));
                    $insertdetail = array(
                        'cor_id' => $cor_id,
                        'mem_id' => $mem_id,
                        'brd_id' => $brd_id,
                        'cit_id' => element('cit_id', $item,0),
                        'cde_id' => element('cit_id', $item,0),
                        'cod_download_days' => '',
                        'cod_count' => element($key,$cod_count,1),
                        'cod_status' => $od_status,
                    );
                    $this->Cmall_order_detail_model->insert($insertdetail);
                    
                }
            }
        }

        if(empty($res)){
            $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        

        $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        
    }

    public function update_order($brd_id  = 0,$mem_id = 0,$cor_order_no = '')
    {   
        $this->output->set_content_type('application/json');
        $this->load->model(array('Member_model','Cmall_item_model', 'Cmall_order_model', 'Cmall_order_detail_model','Unique_id_model'));
        
        if (empty($mem_id)) {
            $result = array('resultcode'=>1000,'message' => 'mem_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($brd_id)) {
            $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        // if (empty($cor_key)) {
        //     $result = array('resultcode'=>1008,'message' => 'cor_key 가 없습니다.');
        //     exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        // }

        if (empty($cor_order_no)) {
            $result = array('resultcode'=>1002,'message' => 'cor_order_no 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $order = $this->Cmall_order_model->get_one('','',array('brd_id' => $brd_id,'cor_order_no' =>$cor_order_no));
        if ( ! element('cor_id', $order)) {
            $result = array('resultcode'=>1003,'message' => '없는 cor_order_no 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        
        if ($this->member->is_admin() === false
            && element('mem_id', $order) !== $mem_id) {
            $result = array('resultcode'=>1004,'message' => '잘못된 접근입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        

        

        $member = $this->Member_model->get_by_memid($mem_id,'mem_id,mem_userid,mem_nickname,mem_email,mem_phone');


        if ( ! element('mem_id', $member)) {
            $result = array('resultcode'=>1000,'message' => '없는 mem_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $updatedata = array();
        $array = array(
            'cor_carrier', 'cor_track', 'cor_memo'
        );
        foreach ($array as $value) {
            if(!empty($this->input->post($value)))
                $updatedata[$value] = $this->input->post($value);
        }
        
        $updatedata['cor_status'] = 0;
        $updatedata['cor_approve_datetime'] = date('Y-m-d H:i:s');
        $updatedata['status'] = 'deposit';
        
        $mod_history = '';
        $mod_history .= date('Y-m-d H:i:s', time()).' '.$mem_id.' 주문'.element('cor_memo',$updatedata,'').' 처리'."\n";
        
        if($mod_history){

            $mod_history = $order['cor_order_history'].$mod_history;

            $updatedata['cor_order_history'] = $mod_history;
        }

        $cor_goods_code = array();
        $cod_count = array();
        
           $post = json_encode($_POST);
        
            log_message('error', $post);
            
        $cor_goods_code = $this->input->post('cor_goods_code',null,'');
        $cod_count = $this->input->post('cod_count',null,'');
        $od_status = 'order'; //주문상태

        $res = $this->Cmall_order_model->update(element('cor_id', $order), $updatedata);

        if ($res) {
            if($cor_goods_code && is_array($cor_goods_code)){
                $this->Cmall_order_detail_model->delete_where(array('cor_id' => element('cor_id', $order)));
                foreach ($cor_goods_code as $key => $val) {
                    $item = $this->Cmall_item_model
                        ->get_one('', '',array('brd_id' => $brd_id,'cit_goods_code' => $val));

                    
                    $insertdetail = array(
                        'cor_id' => element('cor_id', $order),
                        'mem_id' => $mem_id,
                        'brd_id' => $brd_id,
                        'cit_id' => element('cit_id', $item,0),
                        'cde_id' => element('cit_id', $item,0),
                        'cod_download_days' => '',
                        'cod_count' => element($key,$cod_count,1),
                        'cod_status' => $od_status,
                    );
                    $this->Cmall_order_detail_model->insert($insertdetail);
                    
                }
            }
        }

        if(empty($res)){
            $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        

        $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        
    }

    public function get_order($cor_id)
    {   
        $this->output->set_content_type('application/json');
        if (empty($cor_id)) {
            $result = array('resultcode'=>1000,'message' => 'cor_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $this->load->model(array('Cmall_order_model'));
        $order = $this->Cmall_order_model->get_one($cor_id);

        if (element('cor_id',$order)) {
            $result = array('resultcode'=>1000,'message' => '없는 cor_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        exit(json_encode($order,JSON_UNESCAPED_UNICODE));
        
    }

    public function get_orderdetail($cor_id)
    {
        $this->output->set_content_type('application/json');
        if (empty($cor_id)) {
            $result = array('resultcode'=>1000,'message' => 'cor_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $this->load->model(array('Cmall_order_model','Cmall_order_detail_model'));

        $order = $this->Cmall_order_model->get_one($cor_id);

        if (element('cor_id',$order)) {
            $result = array('resultcode'=>1000,'message' => '없는 cor_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $orderdetail = $this->Cmall_order_detail_model->get_by_key(element('cor_key',$order));

        if (element('cor_id',$orderdetail)) {
            $result = array('resultcode'=>1000,'message' => '없는 cor_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        exit(json_encode($orderdetail,JSON_UNESCAPED_UNICODE));
    }

    public function is_delete($crw_id)
    {

            
        
        // if (empty($brd_id)) {
        //     $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
        //     exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        // }

        // $board = $this->board->item_all($brd_id);

        // if ( ! element('brd_id', $board)) {
        //     $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
        //     exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        // }

         $crw_id = (int) $crw_id;
        if (empty($crw_id) OR $crw_id < 1) {
            $result = array('resultcode'=>1000,'message' => '잘못된  crw_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $DB2 = $this->load->database('db2', TRUE);
        $DB2->from('crawl_item');
        if ($crw_id) {
            $DB2->where('crw_id', $crw_id);
        }
        
        
        $DB2->limit(1,0);
        
        $result = $DB2->get();
        $crawl_item = $result->row_array();

        if (empty(element('crw_id',$crawl_item))) {
            $result = array('resultcode'=>1000,'message' => '없는 crw_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

            

        $updatedata = array(
            'crw_updated_datetime' => cdate('Y-m-d H:i:s'),
            'is_del' => 1,
        );  
            

        if ( ! empty($updatedata)) {
            if ( ! empty($crw_id)) {
                $DB2->where('crw_id', $crw_id);
            }
            
            $DB2->set($updatedata);
            $crw_id = $DB2->update('crawl_item');

            if(empty($crw_id)){
                $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }
        } else {
            $result = array('resultcode'=>9000,'message' => 'updatedata 에 오류가 발생하였습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
        

        
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        
    }

    public function html_write_file($brd_id,$type)
    {

        $this->load->helper('file');
        $this->output->set_content_type('application/json');


        $upload_path = config_item('uploads_dir') . '/html_write/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= cdate('Y') . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= cdate('m') . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }
 $upload_path .= $brd_id . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }




            $data      = $this->input->post('data');


        $write_file_path =  $upload_path;

        if (write_file($write_file_path.$type.'.html', $data))
        {
            // chmod($write_file_path, 0644);
            $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');



            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }


        $result = array('resultcode'=>2,'message' => '오류 입니다.');

        exit(json_encode($result,JSON_UNESCAPED_UNICODE));



    }


    public function order_html_write_file($brd_id,$mem_id=2)
    {   

 



        $this->load->helper('file');
        $this->output->set_content_type('application/json');

        $this->load->model(array('Member_model','Board_crawl_model'));
        


        if (empty($brd_id)) {
            $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($mem_id)) {
            $result = array('resultcode'=>1000,'message' => 'mem_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($this->input->post('pointer_url'))) {
            $result = array('resultcode'=>1002,'message' => 'pointer_url 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($this->input->post('data'))) {
            $result = array('resultcode'=>1003,'message' => 'data 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }


        $crawlwhere = array(
            'brd_id' => $brd_id,
        );

        $board_crawl = $this->Board_crawl_model->get_one('','brd_content,brd_content_detail,brd_id,brd_order_key',$crawlwhere);
        if ( ! element('brd_id', $board_crawl)) {
            show_404();
        }

        $pointer_url = $this->input->post('pointer_url');
        
        $pointer_url_ = parse_url($pointer_url);
        $cor_key='';

        if(element('brd_order_key',$board_crawl) ==='sixshop'){
            
            

            

            $src_array_= explode('/', $pointer_url_['path']);

            $fruit = array_pop($src_array_);
            $fruit1 = array_pop($src_array_);

            if($pointer_url_['path'])                
                $cor_key = '/'.$fruit.'/'.$fruit1;


        } else {

            $pointer_url_ = parse_url($pointer_url);

            if(!empty($pointer_url_['query']))
                parse_str($pointer_url_['query'] ,$query_);



            if(!empty($query_[element('brd_order_key',$board_crawl)]))
                $cor_key = $query_[element('brd_order_key',$board_crawl)];
        }

        // if($brd_id == '315'){
        if(element('brd_order_key',$board_crawl) ==='parse'){            
            require_once FCPATH . 'plugin/simplehtmldom/simple_html_dom.php';

            $html_dom='';
            $html = new simple_html_dom();
            $html->load($this->input->post('data',null,''));


            if(element('brd_content_detail', $board_crawl)){
                eval(element('brd_content_detail', $board_crawl));
            }

            

            // $cor_key = '/'.$orderinfo['order_no'];
        } 
        $cor_pay_type = '';
         if(strpos($pointer_url_['host'],'pay.naver.com') !==false){
            require_once FCPATH . 'plugin/simplehtmldom/simple_html_dom.php';
            $html_dom='';
            $html = new simple_html_dom();
            $html->load($this->input->post('data',null,''));
            $html_dom = $html->find('#ct > div.ord_cont > div.ordf_sc > div.ordinf_tlb > table > tbody > tr.ord_num.btn_tr > td > div > strong',0)->plaintext;

            if($html_dom){
                $cor_key = '/'.$html_dom;
                
            }

            $cor_pay_type = 'naverpay';
        }

        if(empty($cor_key)){
            
            
            $result = array('resultcode'=>2,'message' => '오류 입니다 cor_key 없습니다..');
                    log_message('error', $result['message']);
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            

            // $cor_key = date('Ymdhi');
        } 

        $upload_path = config_item('uploads_dir') . '/html_write/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= cdate('Y') . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= cdate('m') . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= $brd_id . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }
        


        
        $data = $this->input->post('data');
        
        $DB2 = $this->load->database('db2', TRUE);
        
        $DB2->select('cb_cmall_order.*');
        $DB2->from('cb_cmall_order');
        
        $where = array(
            'brd_id' => $brd_id,
            'cor_key' => $cor_key,
            'mem_id' => $mem_id,
        );

        $DB2->where($where);
        // $this->db2->where($where);
        // $this->db2->limit($limit);
        
        $qry = $DB2->get();
        $result_order = $qry->result_array();

        foreach($result_order as $value_order){

            if($value_order['cor_id']){
                $DB2->where($where);
                $del_result = $DB2->delete('cb_cmall_order');

                @unlink(config_item('uploads_dir') . '/html_write/'.$value_order['cor_file_1']);
            }
        }

        
        $write_file_path =  $upload_path;
        $write_file_name =  'order_'.str_replace('=','',str_replace('?','',str_replace('/','',$cor_key))).'.html';
        
        if (write_file($write_file_path.$write_file_name, $data))
        {   



            
            

            

            $member = $this->Member_model->get_by_memid($mem_id,'mem_id,mem_userid,mem_nickname,mem_email,mem_phone');


            if ( ! element('mem_id', $member)) {
                $result = array('resultcode'=>1000,'message' => '없는 mem_id 입니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }


          

            
            
            $updatedata = array(
                
                'mem_id' => $mem_id,
                
                'cor_datetime' => cdate('Y-m-d H:i:s'),
                'cor_ip' => $this->input->ip_address(),
                'cor_useragent' => $this->agent->agent_string(),
                
                'cor_pay_type' => $cor_pay_type,
                'cor_key' => $cor_key,
                'brd_id' => $brd_id,
                'cor_file_1' => cdate('Y') . '/' . cdate('m') . '/' .$brd_id . '/'.$write_file_name,
            );

            // log_message('error', explode(',',$updatedata));
            $DB2->insert('cb_cmall_order', $updatedata);
            $cor_id = $DB2->insert_id();
            
            if(empty($cor_id)){
                $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
                log_message('error', $result['message']);
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            // chmod($write_file_path, 0644);
            $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
            
            if(element('brd_order_key',$board_crawl) ==='parse'){            
                if(empty($html_dom))
                    log_message('error', 'order'.$cor_id.'cor_key 에러');
            }
            

            $retval = 1;
            
            $cmd=FCPATH.'python/bin/start.order.sh '.$cor_id;
            // echo $cmd;
            @exec($cmd, $output, $retval);

            

            // $this->insert_order($brd_id,$mem_id,$cor_key,$cor_key,$cor_pay_type);
            log_message('error', $result['message']);
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }


        $result = array('resultcode'=>2,'message' => '오류 입니다.');
                log_message('error', $result['message']);
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        

        
    }
    
    public function get_order_html_file($cor_id)
    {
        $this->output->set_content_type('application/json');

        
        


        if (empty($cor_id)) {
            $result = array('resultcode'=>1001,'message' => 'cor_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        
        $DB2 = $this->load->database('db2', TRUE);
        
        $DB2->select('cb_cmall_order.cor_id,cb_cmall_order.mem_id,cb_cmall_order.brd_id,cb_cmall_order.cor_key,cb_cmall_order.cor_file_1,cb_cmall_order.cor_pay_type');
        $DB2->from('cb_cmall_order');
        
        $where = array(
            'cor_id' => $cor_id,
        );

        $DB2->where($where);
        // $this->db2->where($where);
        // $this->db2->limit($limit);
        
        $qry = $DB2->get();
        $result_order = $qry->row_array();

        if ( ! element('cor_id', $result_order)) {
            $result = array('resultcode'=>1000,'message' => '없는 cor_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $result_order['cor_file_1'] = site_url(config_item('uploads_dir') . '/html_write/'.$result_order['cor_file_1']);
        
        // $result_order['cor_file_1'] = FCPATH.$result_order['cor_file_1'];
        
        $crawlwhere = array(
            'brd_id' => element('brd_id', $result_order),
        );

        $board_crawl = $this->Board_crawl_model->get_one('','brd_url',$crawlwhere);

        $result_order['brd_url'] = element('brd_url', $board_crawl);

        $result_order['cor_key'] = urlencode($result_order['cor_key']);

        if($result_order){
                    
            exit(json_encode($result_order,JSON_UNESCAPED_UNICODE));
        }


        $result = array('resultcode'=>2,'message' => '오류 입니다.');
                
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }


    public function orderstatus_html_write_file($brd_id,$mem_id=2,$cor_id=0)
    {   

        

        $this->load->helper('file');
        $this->output->set_content_type('application/json');

        $this->load->model(array('Member_model','Board_crawl_model','Cmall_order_model'));
        


        if (empty($brd_id)) {
            $result = array('resultcode'=>1001,'message' => 'brd_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        $board = $this->board->item_all($brd_id);

        if ( ! element('brd_id', $board)) {
            $result = array('resultcode'=>1001,'message' => '잘못된  brd_id 입니다..');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($mem_id)) {
            $result = array('resultcode'=>1000,'message' => 'mem_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }

        if (empty($this->input->post('data'))) {
            $result = array('resultcode'=>1003,'message' => 'data 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        
        $crawlwhere = array(
            'brd_id' => $brd_id,
        );

        $board_crawl = $this->Board_crawl_model->get_one('','brd_id,brd_order_key',$crawlwhere);
        if ( ! element('brd_id', $board_crawl)) {
            show_404();
        }


      
        


     


        $upload_path = config_item('uploads_dir') . '/html_write/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= cdate('Y') . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= cdate('m') . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }

        $upload_path .= $brd_id . '/';
        if (is_dir($upload_path) === false) {
            mkdir($upload_path, 0707);
            $file = $upload_path . 'index.php';
            $f = @fopen($file, 'w');
            @fwrite($f, '');
            @fclose($f);
            @chmod($file, 0644);
        }
        


        
            $data      = $this->input->post('data');
        
        $DB2 = $this->load->database('db2', TRUE);
        
        $DB2->select('cb_cmall_orderstatus.*');
        $DB2->from('cb_cmall_orderstatus');
        
        $where = array(
            'brd_id' => $brd_id,
            'cor_id' => $cor_id,
        );

        $DB2->where($where);
        // $this->db2->where($where);
        // $this->db2->limit($limit);
        
        $qry = $DB2->get();
        $result_order = $qry->result_array();

        foreach($result_order as $value_order){

            if($value_order['cor_id']){
                $DB2->where($where);
                $del_result = $DB2->delete('cb_cmall_orderstatus');

                @unlink(config_item('uploads_dir') . '/html_write/'.$value_order['cos_file_1']);
            }
        }

        
        $write_file_path =  $upload_path;

        if (write_file($write_file_path.'orderstatus_'.$cor_id.'.html', $data))
        {   




            
            

            

            $member = $this->Member_model->get_by_memid($mem_id,'mem_id,mem_userid,mem_nickname,mem_email,mem_phone');


            if ( ! element('mem_id', $member)) {
                $result = array('resultcode'=>1000,'message' => '없는 mem_id 입니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }


            $order = $this->Cmall_order_model->get_one($cor_id,'cor_order_no');
            
            if ( ! element('cor_id', $order)) {
                log_message('error', $cor_id. '은 없는 cor_id 입니다');
                // $result = array('resultcode'=>1003,'message' => '없는 cor_order_no 입니다.');
                // exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }
            
            
            $updatedata = array(
                
                'mem_id' => $mem_id,
                
                'cos_datetime' => cdate('Y-m-d H:i:s'),
                'cos_ip' => $this->input->ip_address(),
                'cos_useragent' => $this->agent->agent_string(),
                
                'cos_order_no' => element('cor_order_no', $order,''),
                'cor_id' => $cor_id,
                'brd_id' => $brd_id,
                'cos_file_1' => cdate('Y') . '/' . cdate('m') . '/' .$brd_id . '/'.'orderstatus_'.$cor_id.'.html',
            );

            
            $DB2->insert('cb_cmall_orderstatus', $updatedata);
            $cor_id_ = $DB2->insert_id();
           
            if(empty($cor_id_)){
                $result = array('resultcode'=>9000,'message' => 'DB 입력시 알 수 없는 오류가 발생하였습니다.');
                exit(json_encode($result,JSON_UNESCAPED_UNICODE));
            }

            $retval = 1;
            
            $cmd=FCPATH.'python/bin/start.orderstatus.sh '.$cor_id_;
            // echo $cmd;
            @exec($cmd, $output, $retval);

            // chmod($write_file_path, 0644);
            $result = array('resultcode'=>1,'message' => '정상적으로 입력되었습니다.');
                    
            $this->Cmall_order_model->update($cor_id,array('cor_status' =>1));
                    
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }


        $result = array('resultcode'=>2,'message' => '오류 입니다.');
                
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        

        
    }


    public function get_orderstatus_html_file($cos_id)
    {
        $this->output->set_content_type('application/json');

        
        


        if (empty($cos_id)) {
            $result = array('resultcode'=>1001,'message' => 'cos_id 가 없습니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        
        $DB2 = $this->load->database('db2', TRUE);
        
        $DB2->select('cb_cmall_orderstatus.cos_id,cb_cmall_orderstatus.mem_id,cb_cmall_orderstatus.brd_id,cb_cmall_orderstatus.cor_id,cb_cmall_orderstatus.cos_file_1,cb_cmall_orderstatus.cos_order_no');
        $DB2->from('cb_cmall_orderstatus');
        
        $where = array(
            'cos_id' => $cos_id,
        );

        $DB2->where($where);
        // $this->db2->where($where);
        // $this->db2->limit($limit);
        
        $qry = $DB2->get();
        $result_order = $qry->row_array();


        if ( ! element('cos_id', $result_order)) {
            $result = array('resultcode'=>1000,'message' => '없는 cos_id 입니다.');
            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
        }
        
        // $result_order['cos_file_1'] = FCPATH.$result_order['cos_file_1'];
        $result_order['cos_file_1'] = site_url(config_item('uploads_dir') . '/html_write/'.$result_order['cos_file_1']);



        $crawlwhere = array(
            'brd_id' => element('brd_id', $result_order),
        );

        $board_crawl = $this->Board_crawl_model->get_one('','brd_url',$crawlwhere);


        $this->load->model(array('Cmall_order_model'));

        $order = $this->Cmall_order_model->get_one(element('cor_id', $result_order),'cor_pay_type');

        $result_order['cor_pay_type'] = element('cor_pay_type', $order,'');
        $result_order['brd_url'] = element('brd_url', $board_crawl,'');

        if($result_order){
                    
            exit(json_encode($result_order,JSON_UNESCAPED_UNICODE));
        }


        $result = array('resultcode'=>2,'message' => '오류 입니다.');
                
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }

    public function crawl_delete($brd_id)
    {

        
        $this->output->set_content_type('application/json');

        
        


       
        
        $DB2 = $this->load->database('db2', TRUE);       
        
        
        $deletewhere = array(
                'brd_id' => $brd_id,
            );

        $DB2->where($deletewhere);

        $DB2->delete('cb_crawl_detail');

        $DB2->where($deletewhere);

        $DB2->delete('cb_crawl_item');
            


        $result = array('resultcode'=>1,'message' => '삭제 되어 습니다.');
                
        exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }


    public function write($board_id = 0,$post_arr = array())
    {
        
        $board = $this->board->item_all($board_id);
        

        return $this->_write_common($board,$post_arr);
    }

    public function _write_common($board,$post_arr = array())
    {



        $view = array();
        $view['view'] = array();

        $view['view']['post'] = array();

        $view['view']['board'] = $board;
        $view['view']['board_key'] = element('brd_key', $board);
        $mem_id = (int) $this->member->item('mem_id');

        $primary_key = $this->Post_model->primary_key;

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin(
            array(
                'board_id' => element('brd_id', $board),
                'group_id' => element('bgr_id', $board),
            )
        );

        


     



            $content_type =  0;


            $post_num = $this->Post_model->next_post_num();
            $post_reply = '';

            $metadata = array();
            $post_title = array();

            if(element('crw_category1', $post_arr))
                $post_title[] = element('crw_category1', $post_arr) ;

            if(element('crw_category2', $post_arr))
                $post_title[] = element('crw_category2', $post_arr) ;

            if(element('crw_category3', $post_arr))
                $post_title[] = element('crw_category3', $post_arr) ;

            
            $post_title = implode("-",$post_title) ;

            $res = $this->Post_model->get_one('','post_id',array('post_title' => $post_title,'brd_id' => element('brd_id', $board)));

            if($res){
                return $res['post_id'];
            }

            $post_page_key = '';
            $post_content = '';
            $post_content_detail = '';
            $post_comment = '';
            

            $updatedata = array(
                'post_num' => $post_num,
                'post_reply' => $post_reply,
                'post_title' => $post_title,
                'post_page_key' => $post_page_key,
                'post_content' => $post_content,
                'post_content_detail' => $post_content_detail,
                'post_comment' => $post_comment,
                'post_html' => $content_type,
                'post_datetime' => cdate('Y-m-d H:i:s'),
                'post_updated_datetime' => cdate('Y-m-d H:i:s'),
                'post_ip' => $this->input->ip_address(),
                'brd_id' => element('brd_id', $board),
            );

            // if ($mem_id) {
            //     if (element('use_anonymous', $board)) {
            //         $updatedata['mem_id'] = (-1) * $mem_id;
            //         $updatedata['post_userid'] = '';
            //         $updatedata['post_username'] = '익명사용자';
            //         $updatedata['post_nickname'] = '익명사용자';
            //         $updatedata['post_email'] = '';
            //         $updatedata['post_homepage'] = '';
            //     } else {
            //         $updatedata['mem_id'] = $mem_id;
            //         $updatedata['post_userid'] = $this->member->item('mem_userid');
            //         $updatedata['post_username'] = $this->member->item('mem_username');
            //         $updatedata['post_nickname'] = $this->member->item('mem_nickname');
            //         $updatedata['post_email'] = $this->member->item('mem_email');
            //         $updatedata['post_homepage'] = $this->member->item('mem_homepage');
            //     }
            // }

            $updatedata['mem_id'] = 1;
            $updatedata['post_userid'] = 'admin';
            $updatedata['post_username'] = '관리자';
            $updatedata['post_nickname'] = '관리자';
            $updatedata['post_email'] = 'admin@denguru.kr';
            $updatedata['post_homepage'] = 0;
            // if ($this->member->is_member() === false && $this->input->post('post_password')) {
            //     if ( ! function_exists('password_hash')) {
            //         $this->load->helper('password');
            //     }
            //     $updatedata['post_password'] = password_hash($this->input->post('post_password'), PASSWORD_BCRYPT);
            // }

            
            // if (element('use_post_secret', $board) === '2') {
            //     $updatedata['post_secret'] = 1;
            // }
            
            
            if (element('use_category', $board)) {
                $updatedata['post_category'] = $this->input->post('post_category', null, '');
            }

            $updatedata['post_device']
                = ($this->cbconfig->get_device_type() === 'mobile') ? 'mobile' : 'desktop';

            $post_id = $this->Post_model->insert($updatedata);

           

         
            return $post_id;
            
          

           

           

           
        
    
    }

    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {   

        
        
        $join[] = array('table' => 'crawl_detail', 'on' => 'crawl_detail.crw_id = crawl_item.crw_id', 'type' => 'left');
        
        $select = $this->_select;
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }

    public function _get_list_common($select = '', $join = '', $limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR',$where_in = '')
    {

        $this->db2 = $this->load->database('db2', TRUE);
            $findex = 'crawl_item.crw_id';
     

        $forder = (strtoupper($forder) === 'ASC') ? 'ASC' : 'DESC';
        $sop = (strtoupper($sop) === 'AND') ? 'AND' : 'OR';

        $count_by_where = array();
        $search_where = array();
        $search_like = array();
        $search_or_like = array();
        if ($sfield && is_array($sfield)) {
            foreach ($sfield as $skey => $sval) {
                $ssf = $sval;
                if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
                    if (in_array($ssf, $this->search_field_equal)) {
                        $search_where[$ssf] = $skeyword;
                    } else {
                        $swordarray = explode(' ', $skeyword);
                        foreach ($swordarray as $str) {
                            if (empty($ssf)) {
                                continue;
                            }
                            if ($sop === 'AND') {
                                $search_like[] = array($ssf => $str);
                            } else {
                                $search_or_like[] = array($ssf => $str);
                            }
                        }
                    }
                }
            }
        } else {
            $ssf = $sfield;
            if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
                if (in_array($ssf, $this->search_field_equal)) {
                    $search_where[$ssf] = $skeyword;
                } else {
                    $swordarray = explode(' ', $skeyword);
                    foreach ($swordarray as $str) {
                        if (empty($ssf)) {
                            continue;
                        }
                        if ($sop === 'AND') {
                            $search_like[] = array($ssf => $str);
                        } else {
                            $search_or_like[] = array($ssf => $str);
                        }
                    }
                }
            }
        }

        if ($select) {
            $this->db2->select($select);
        }
        $this->db2->from('crawl_item');
        if ( ! empty($join['table']) && ! empty($join['on'])) {
            if (empty($join['type'])) {
                $join['type'] = 'left';
            }
            $this->db2->join($join['table'], $join['on'], $join['type']);
        } elseif (is_array($join)) {
            foreach ($join as $jkey => $jval) {
                if ( ! empty($jval['table']) && ! empty($jval['on'])) {
                    if (empty($jval['type'])) {
                        $jval['type'] = 'left';
                    }
                    $this->db2->join($jval['table'], $jval['on'], $jval['type']);
                }
            }
        }

        if ($where) {
            $this->db2->where($where);
        }
        
        

        // if($this->where_in){
            
        //     $this->db2->group_start();
                    
        //     foreach ($this->where_in as $skey => $sval) {
        //         $this->db2->where_in($skey, $sval);
        //     }
            
        //     $this->db2->group_end();
            
            
        // }

        
       
        $this->db2->order_by($findex, $forder);
        if ($limit) {
            $this->db2->limit($limit, $offset);
        }

        $qry = $this->db2->get();
        $result['list'] = $qry->result_array();


        

        return $result;
    
    }
}
