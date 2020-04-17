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
            'credentials' => 'denguru3-71f74-firebase-adminsdk-mm99m-66017c38dd.json'
        ]);


        # Instantiates a client
        // $this->translate = new TranslateClient([
        //     'key' => config_item('translate_key')
        // ]);

        $projectId = 'denguru3-71f74';
        
        $this->naturallanguage = new LanguageClient([
            'projectId' => $projectId,
            'keyFilePath' => 'denguru3-71f74-firebase-adminsdk-mm99m-66017c38dd.json'
        ]);
    }


    /**
     * 전체 메인 페이지입니다
     */
    
   
    public function crawling_update($post_id = 0)
    {   
        
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
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
        $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);
       

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
             
            $cmallwhere = array(
                'pln_id' => element('pln_id',$value),
            );
            $cmall = $cmall_out = $this->Cmall_item_model
                ->get('', '', $cmallwhere, '', '', 'pln_id', 'ASC');
            
            // 1. 완료,2. 크롤링중,3. 크롤링실패,4. 크롤링업데이트중,5. 크롤링업데이트 실패,6. 태그화중,7. 태그화 실
            $linkupdate = array(
                'pln_status' => 4,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
            sleep(5);

            if(element('pln_page', $value)){
                $param =& $this->querystring;

                $pln_url = parse_url(element('pln_url', $value));

                parse_str($pln_url['query'] ,$query_);
                
                
                


                for($page=$query_['page'];element('pln_page', $value) >= $page;$page++){
                    echo $pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query'])."<br>";
                    
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
                        
             
                        if(count($crawl_info) ){

                            foreach($crawl_info as $ikey => $ivalue){

                                $flag=false;
                                foreach ($cmall as $c_key => $c_value) {
                                    if(element('crawl_goods_code',$ivalue) === element('cit_goods_code',$c_value)){
                                        unset($cmall_out[$c_key]);
                                        $flag=true;

                                        $updatedata = array(                                        
                                            'post_id' => $post_id,
                                            'cit_name' => element('crawl_title',$ivalue) ? element('crawl_title',$ivalue) : element('cit_name',$c_value) ,
                                            'cit_summary' => element('crawl_sub_title',$ivalue) ? element('crawl_sub_title',$ivalue) : element('cit_summary',$c_value) ,
                                            'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ? preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$ivalue))) : element('cit_price',$c_value) ,
                                            'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                            'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) ? $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) : element('cit_post_url', $c_value),
                                            'brd_id' => element('brd_id', $board_crawl),
                                            'pln_id' => element('pln_id', $value),
                                            'cit_goods_code' => element('crawl_goods_code', $ivalue) ? element('crawl_goods_code', $ivalue) : element('cit_goods_code', $c_value),                        
                                            'cit_is_soldout' => element('crawl_is_soldout', $ivalue) ? element('crawl_is_soldout', $ivalue) : element('cit_is_soldout', $c_value),
                                            
                                        );

                                        // if((element('crawl_title',$ivalue) || element('cit_name',$c_value)) && (preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) || element('cit_price',$c_value)) && ($this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) || element('cit_post_url',$c_value)) && (element('crawl_goods_code',$ivalue) || element('cit_goods_code',$c_value)))
                                        //     $updatedata['cit_val1'] = 0;
                                        // else 
                                        //     $updatedata['cit_val1'] = 1;
                                        $this->Cmall_item_model->update(element('cit_id',$c_value),$updatedata);

                                        break;
                                    }
                                }

                                if($flag){
                                    

                                    
                                     
                                } else {

                                    // $this->board->delete_cmall(element('cit_id',$o_value));

                                     $updatedata = array(
                                
                                        'post_id' => $post_id,
                                        'cit_name' => element('crawl_title',$ivalue) ,
                                        'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                        'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$ivalue))) ,
                                        'cit_datetime' => cdate('Y-m-d H:i:s'),
                                        'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                        'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                        'brd_id' => element('brd_id', $board_crawl),
                                        'pln_id' => element('pln_id', $value),
                                        'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                        'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                        'cit_status' => 1,
                                        // 'cit_type1' => element('cit_type1', $ivalue) ? 1 : 0,
                                        // 'cit_type2' => element('cit_type2', $ivalue) ? 1 : 0,
                                        'cit_type3' => 1,
                                        // 'cit_type4' => element('cit_type4', $ivalue) ? 1 : 0,
                                        
                                        
                                    );

                                    $cit_id = $this->Cmall_item_model->insert($updatedata);

                                    $updatedata = array();
                                    $updatedata['cit_key'] = 'c_'.$cit_id;
                                            
                                    $this->Cmall_item_model->update($cit_id, $updatedata);

                                    # 이미지 URL 추출
                                    // $imageUrl = $this->valid_url($board_crawl,$crawl_info[$ikey]['img_src']);
                                    $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url(element('img_src',$ivalue),element('pln_url', $value)));
                                    
                                    # 이미지 파일명 추출
                                    
                                    $img_src_array = parse_url(urldecode($imageUrl));
                        // $img_src_array= explode('://', $imageUrl);
                        $img_src_array_= explode('/', $img_src_array['path']);
                        $imageName = end($img_src_array_);

                        $encode_url=array();
                        foreach($img_src_array as $u_key => $u_value){
                            $img_src_array[$u_key] = rawurlencode($img_src_array[$u_key]);
                        }
                        $imageUrl = $img_src_array['scheme'].'://'.$img_src_array['host'].$img_src_array['path'];
                        // $imageUrl = $img_src_array[0].'://'.$imageUrl;
                        
                        // $imageUrl = str_replace("%3F","?",$imageUrl);
                        // $imageUrl = str_replace("%26","&",$imageUrl);
                        $imageUrl = str_replace("%2F","/",$imageUrl);
                        echo "<br>".$imageUrl."<br>";
                                    // echo "<br>".$imageUrl."<br>";

                                    // $img_src_array= explode('/', $imageUrl);
                                    // $imageName = end($img_src_array);
                                    
                                    

                                    # 이미지 파일이 맞는 경우
                                    if ($fileinfo = getimagesize($imageUrl)) {


                                        # 이미지 다운로드
                                        $imageFile = $this->extract_html($imageUrl,'','',element('referrer', $ivalue,''));

                                        # 파일 생성 후 저장
                                        $filetemp = fopen($imageName, 'w');
                                        fwrite($filetemp, $imageFile['content']);
                                        fclose($filetemp); // Closing file handle

                                        $file_error = '';
                                        $uploadfiledata = array();
                                        $uploadfiledata2 = array();

                                        $this->load->library('upload');
                                        
                                        $upload_path = config_item('uploads_dir') . '/cmallitem/';
                                        if (is_dir($upload_path) === false) {
                                            mkdir($upload_path, 0707);
                                            $file = $upload_path . 'index.php';
                                            $f = @fopen($file, 'w');
                                            @fwrite($f, '');
                                            @fclose($f);
                                            @chmod($file, 0644);
                                        }
                                        $upload_path .= element('brd_id', $board_crawl) . '/';
                                        if (is_dir($upload_path) === false) {
                                            mkdir($upload_path, 0707);
                                            $file = $upload_path . 'index.php';
                                            $f = @fopen($file, 'w');
                                            @fwrite($f, '');
                                            @fclose($f);
                                            @chmod($file, 0644);
                                        }
                                        $upload_path .= $post_id . '/';
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
                                        $uploadconfig['allowed_types'] = '*';
                                        $uploadconfig['max_size'] = 2 * 1024;
                                        $uploadconfig['encrypt_name'] = true;

                                        $this->upload->initialize($uploadconfig);
                                        $_FILES['userfile']['name'] = $imageName;
                                        $_FILES['userfile']['type'] = $fileinfo['2'];
                                        $_FILES['userfile']['tmp_name'] = $imageName;
                                        $_FILES['userfile']['error'] = 0;
                                        $_FILES['userfile']['size'] = $fileinfo['bits'];

                                        if ($this->upload->do_upload('userfile','crawl')) {
                                            $filedata = $this->upload->data();


                                            @unlink($imageName);

                                            $i=1;
                                            $uploadfiledata[$i] = array();
                                            $uploadfiledata[$i]['cfi_filename'] = element('brd_id', $board_crawl) . '/' . $post_id . '/' . element('file_name', $filedata);
                                            $uploadfiledata[$i]['cfi_originname'] = element('orig_name', $filedata);
                                            $uploadfiledata[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
                                            $uploadfiledata[$i]['cfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
                                            $uploadfiledata[$i]['cfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
                                            $uploadfiledata[$i]['cfi_type'] = element('file_type', $filedata);
                                            $uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;

                                            $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path,element('file_type', $filedata));       
                                        } else {
                                            $file_error = $this->upload->display_errors();
                                            break;
                                        }


                                        $file_updated = false;
                                        $file_changed = false;
                                        $updatedata = array();
                                        if ($uploadfiledata
                                            && is_array($uploadfiledata)
                                            && count($uploadfiledata) > 0) {
                                            foreach ($uploadfiledata as $pkey => $pval) {
                                               if ($pval) {
                                                   $updatedata['cit_file_' . $pkey] = element('cfi_filename', $pval);
                                                   
                                                   if(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) < 8920868) {
                                                       $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                                       
                                                   } elseif(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) > 8920868) {
                                                       $this->detect_label($cit_id,thumb_url('cmallitem',config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)),$ivalue['crawl_title']);
                                                   }

                                                   // if(element('crawl_title',$ivalue) && preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) && $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) && element('crawl_goods_code', $ivalue))
                                                   //     $updatedata['cit_val1'] = 0 ;
                                                   //  else $updatedata['cit_val1'] = 1 ;
                                               }
                                           }

                                           

                                           $this->Cmall_item_model->update($cit_id, $updatedata);
                                            $file_changed = true;
                                        }
                                        
                                    }
                                    //     && preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) === element('crawl_price',$c_value)) {
                                    //     unset($crawl_out[$c_key]);
                                    //     $flag=true;
                                    //     break;
                                        

                                    // }
                                        
                                   
                                    
                                }
                            }

                            
                        } else {
                            $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 5));
                            continue;    
                        }
                    } else {
                        $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 5));
                        continue;
                    }
                }
                // print_r($crawl_out);
                if($cmall_out){
                    foreach ($cmall_out as $o_key => $o_value) {

                        $crawlfilewhere = array(
                            'cit_id' => element('cit_id', $o_value),
                        );
                        $this->board->delete_cmall(element('cit_id',$o_value));
                    }
                }
                
                if(!$is_pln_error)
                    $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 1));
                if($is_pln_error)
                    $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 5));
            } else {
                echo element('pln_url', $value)."<br>";
                $result = $this->extract_html(element('pln_url', $value), $proxy, $proxy_userpwd);

                echo $result['code']."<br>";
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
                    
                    
                    if(count($crawl_info) ){

                        foreach($crawl_info as $ikey => $ivalue){

                            $flag=false;
                            foreach ($cmall as $c_key => $c_value) {
                                if(element('crawl_goods_code',$ivalue) === element('cit_goods_code',$c_value)){
                                    unset($cmall_out[$c_key]);
                                    $flag=true;

                                    $updatedata = array(                                        
                                        'post_id' => $post_id,
                                        'cit_name' => element('crawl_title',$ivalue) ? element('crawl_title',$ivalue) : element('cit_name',$c_value) ,
                                        'cit_summary' => element('crawl_sub_title',$ivalue) ? element('crawl_sub_title',$ivalue) : element('cit_summary',$c_value) ,
                                        'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ? preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$ivalue))) : element('cit_price',$c_value) ,
                                        'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                        'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) ? $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) : element('cit_post_url', $c_value),
                                        'brd_id' => element('brd_id', $board_crawl),
                                        'pln_id' => element('pln_id', $value),
                                        'cit_goods_code' => element('crawl_goods_code', $ivalue) ? element('crawl_goods_code', $ivalue) : element('cit_goods_code', $c_value),                        
                                        'cit_is_soldout' => element('crawl_is_soldout', $ivalue) ? element('crawl_is_soldout', $ivalue) : element('cit_is_soldout', $c_value),
                                        
                                    );
                                    // if((element('crawl_title',$ivalue) || element('cit_name',$c_value)) && (preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) || element('cit_price',$c_value)) && ($this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) || element('cit_post_url',$c_value)) && (element('crawl_goods_code',$ivalue) || element('cit_goods_code',$c_value)))
                                    //         $updatedata['cit_val1'] = 0;
                                    //     else 
                                    //         $updatedata['cit_val1'] = 1;
                                    $this->Cmall_item_model->update(element('cit_id',$c_value),$updatedata);
                                    break;
                                }
                            }

                            if($flag){
                                

                                
                                 
                            } else {

                                // $this->board->delete_cmall(element('cit_id',$o_value));

                                 $updatedata = array(
                            
                                    'post_id' => $post_id,
                                    'cit_name' => element('crawl_title',$ivalue) ,
                                    'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                    'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$ivalue))) ,
                                    'cit_datetime' => cdate('Y-m-d H:i:s'),
                                    'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                    'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                    'brd_id' => element('brd_id', $board_crawl),
                                    'pln_id' => element('pln_id', $value),
                                    'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                    'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                    'cit_status' => 1,
                                    // 'cit_type1' => element('cit_type1', $ivalue) ? 1 : 0,
                                    // 'cit_type2' => element('cit_type2', $ivalue) ? 1 : 0,
                                    'cit_type3' =>  1 ,
                                    // 'cit_type4' => element('cit_type4', $ivalue) ? 1 : 0,
                                    
                                );

                                $cit_id = $this->Cmall_item_model->insert($updatedata);

                                $updatedata = array();
                                $updatedata['cit_key'] = 'c_'.$cit_id;
                                        
                                $this->Cmall_item_model->update($cit_id, $updatedata);

                                # 이미지 URL 추출
                                // $imageUrl = $this->valid_url($board_crawl,$crawl_info[$ikey]['img_src']);
                                $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url(element('img_src',$ivalue),element('pln_url', $value)));
                                
                                                            # 이미지 파일명 추출
                            
$img_src_array = parse_url(urldecode($imageUrl));
                        // $img_src_array= explode('://', $imageUrl);
                        $img_src_array_= explode('/', $img_src_array['path']);
                        $imageName = end($img_src_array_);

                        $encode_url=array();
                        foreach($img_src_array as $u_key => $u_value){
                            $img_src_array[$u_key] = rawurlencode($img_src_array[$u_key]);
                        }
                        $imageUrl = $img_src_array['scheme'].'://'.$img_src_array['host'].$img_src_array['path'];
                        // $imageUrl = $img_src_array[0].'://'.$imageUrl;
                        
                        // $imageUrl = str_replace("%3F","?",$imageUrl);
                        // $imageUrl = str_replace("%26","&",$imageUrl);
                        $imageUrl = str_replace("%2F","/",$imageUrl);
                        echo "<br>".$imageUrl."<br>";
                            // echo "<br>".$imageUrl."<br>";

                            // $img_src_array= explode('/', $imageUrl);
                            // $imageName = end($img_src_array);
                                
                                

                                # 이미지 파일이 맞는 경우
                                if ($fileinfo = getimagesize($imageUrl)) {


                                    # 이미지 다운로드
                                    $imageFile = $this->extract_html($imageUrl,'','',element('referrer', $ivalue,''));

                                    # 파일 생성 후 저장
                                    $filetemp = fopen($imageName, 'w');
                                    fwrite($filetemp, $imageFile['content']);
                                    fclose($filetemp); // Closing file handle

                                    $file_error = '';
                                    $uploadfiledata = array();
                                    $uploadfiledata2 = array();

                                    $this->load->library('upload');
                                    
                                    $upload_path = config_item('uploads_dir') . '/cmallitem/';
                                    if (is_dir($upload_path) === false) {
                                        mkdir($upload_path, 0707);
                                        $file = $upload_path . 'index.php';
                                        $f = @fopen($file, 'w');
                                        @fwrite($f, '');
                                        @fclose($f);
                                        @chmod($file, 0644);
                                    }
                                    $upload_path .= element('brd_id', $board_crawl) . '/';
                                    if (is_dir($upload_path) === false) {
                                        mkdir($upload_path, 0707);
                                        $file = $upload_path . 'index.php';
                                        $f = @fopen($file, 'w');
                                        @fwrite($f, '');
                                        @fclose($f);
                                        @chmod($file, 0644);
                                    }
                                    $upload_path .= $post_id . '/';
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
                                    $uploadconfig['allowed_types'] = '*';
                                    $uploadconfig['max_size'] = 2 * 1024;
                                    $uploadconfig['encrypt_name'] = true;

                                    $this->upload->initialize($uploadconfig);
                                    $_FILES['userfile']['name'] = $imageName;
                                    $_FILES['userfile']['type'] = $fileinfo['2'];
                                    $_FILES['userfile']['tmp_name'] = $imageName;
                                    $_FILES['userfile']['error'] = 0;
                                    $_FILES['userfile']['size'] = $fileinfo['bits'];

                                    if ($this->upload->do_upload('userfile','crawl')) {
                                        $filedata = $this->upload->data();


                                        @unlink($imageName);

                                        $i=1;
                                        $uploadfiledata[$i] = array();
                                        $uploadfiledata[$i]['cfi_filename'] = element('brd_id', $board_crawl) . '/' . $post_id . '/' . element('file_name', $filedata);
                                        $uploadfiledata[$i]['cfi_originname'] = element('orig_name', $filedata);
                                        $uploadfiledata[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
                                        $uploadfiledata[$i]['cfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
                                        $uploadfiledata[$i]['cfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
                                        $uploadfiledata[$i]['cfi_type'] = element('file_type', $filedata);
                                        $uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;

                                        $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path,element('file_type', $filedata));       
                                    } else {
                                        $file_error = $this->upload->display_errors();
                                        break;
                                    }


                                    $file_updated = false;
                                    $file_changed = false;
                                    if ($uploadfiledata
                                        && is_array($uploadfiledata)
                                        && count($uploadfiledata) > 0) {
                                        foreach ($uploadfiledata as $pkey => $pval) {
                                           if ($pval) {
                                               $updatedata['cit_file_' . $pkey] = element('cfi_filename', $pval);

                                               if(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) < 8920868) {
                                                   $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                                   
                                               } elseif(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) > 8920868) {
                                                   $this->detect_label($cit_id,thumb_url('cmallitem',config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)),$ivalue['crawl_title']);
                                               }

                                               // if(element('crawl_title',$ivalue) && preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) && $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) && element('crawl_goods_code', $ivalue))
                                               //     $updatedata['cit_val1'] = 0 ;
                                               //  else $updatedata['cit_val1'] = 1 ;
                                               
                                           }
                                       }
                                       $this->Cmall_item_model->update($cit_id, $updatedata);
                                        $file_changed = true;
                                    }
                                    
                                }
                                //     && preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) === element('crawl_price',$c_value)) {
                                //     unset($crawl_out[$c_key]);
                                //     $flag=true;
                                //     break;
                                    

                                // }
                                    
                               
                                
                            }
                        }

                        // print_r($crawl_out);
                        if($cmall_out){
                            foreach ($cmall_out as $o_key => $o_value) {

                                $crawlfilewhere = array(
                                    'cit_id' => element('cit_id', $o_value),
                                );
                                $this->board->delete_cmall(element('cit_id',$o_value));
                            }
                        }
                        if(!$is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 1));
                        if($is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 5));
                    } else {
                        $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 5));
                        continue;    
                    }
                    
                } else {
                    $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 5));
                }
                
                
            }
        }


        
        // $this->crawling_cit_type($post_id,'cit_type3');
        // $this->crawling_cit_type($post_id,'cit_type2');
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


    public function crawling_overwrite($post_id=0)
    {


        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            show_404();
        }

        $post = $this->Post_model->get_one($post_id);
        if ( ! element('post_id', $post)) {
            show_404();
        }

        // if(strstr(strtolower(element('post_title',$post)),'신상') || strstr(strtolower(element('post_title',$post)),'new') || strstr(strtolower(element('post_title',$post)),'best') || strstr(strtolower(element('post_title',$post)),'베스트') || strstr(strtolower(element('post_title',$post)),'추천') || strstr(strtolower(element('post_title',$post)),'인기')) return false ;
        

        $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($post_id);
        $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);

        
        

        
        
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



        $crawl = $this->Cmall_item_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');

        foreach ($crawl as $c_key => $c_value) {
            $this->board->delete_cmall(element('cit_id',$c_value));
        }

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
                'pln_status' => 2,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);

            if(element('pln_page', $value)){
                $param =& $this->querystring;

                $pln_url = parse_url(element('pln_url', $value));

                parse_str($pln_url['query'] ,$query_);
                
                
                


                for($page=$query_['page'];element('pln_page', $value) >= $page;$page++){
                    echo $pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query'])."<br>";
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
                                'post_id' => $post_id,
                                'cit_goods_code' => element('crawl_goods_code', $ivalue),
                            );
                            
                            if($this->Cmall_item_model->count_by($where)) continue;


                            $updatedata = array(
                                
                                'post_id' => $post_id,
                                'cit_name' => element('crawl_title',$ivalue) ,
                                'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$ivalue))) ,
                                'cit_datetime' => cdate('Y-m-d H:i:s'),
                                'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                'brd_id' => element('brd_id', $board_crawl),
                                'pln_id' => element('pln_id', $value),
                                'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                'cit_status' => 1,
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
                            // $imageUrl = $this->http_path_to_url($this->valid_url($board_crawl,$crawl_img[$ikey]['img_src']),element('pln_url', $value));

                            $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url(element('img_src',$ivalue),element('pln_url', $value)));


                            
                            # 이미지 파일명 추출
                            
                            $img_src_array = parse_url(urldecode($imageUrl));
                        // $img_src_array= explode('://', $imageUrl);
                        $img_src_array_= explode('/', $img_src_array['path']);
                        $imageName = end($img_src_array_);

                        $encode_url=array();
                        foreach($img_src_array as $u_key => $u_value){
                            $img_src_array[$u_key] = rawurlencode($img_src_array[$u_key]);
                        }
                        $imageUrl = $img_src_array['scheme'].'://'.$img_src_array['host'].$img_src_array['path'];
                        // $imageUrl = $img_src_array[0].'://'.$imageUrl;
                        
                        // $imageUrl = str_replace("%3F","?",$imageUrl);
                        // $imageUrl = str_replace("%26","&",$imageUrl);
                        $imageUrl = str_replace("%2F","/",$imageUrl);
                        echo "<br>".$imageUrl."<br>";

                            // $img_src_array= explode('/', $imageUrl);
                            // $imageName = end($img_src_array);
                            
                            
                            # 이미지 파일이 맞는 경우
                            if ($fileinfo = getimagesize($imageUrl)) {


                                # 이미지 다운로드
                                $imageFile = $this->extract_html($imageUrl,'','',element('referrer', $ivalue,''));

                                # 파일 생성 후 저장
                                $filetemp = fopen($imageName, 'w');
                                fwrite($filetemp, $imageFile['content']);
                                fclose($filetemp); // Closing file handle

                                $file_error = '';
                                $uploadfiledata = array();
                                $uploadfiledata2 = array();

                                $this->load->library('upload');
                                
                                $upload_path = config_item('uploads_dir') . '/cmallitem/';
                                if (is_dir($upload_path) === false) {
                                    mkdir($upload_path, 0707);
                                    $file = $upload_path . 'index.php';
                                    $f = @fopen($file, 'w');
                                    @fwrite($f, '');
                                    @fclose($f);
                                    @chmod($file, 0644);
                                }
                                $upload_path .= element('brd_id', $board_crawl) . '/';
                                if (is_dir($upload_path) === false) {
                                    mkdir($upload_path, 0707);
                                    $file = $upload_path . 'index.php';
                                    $f = @fopen($file, 'w');
                                    @fwrite($f, '');
                                    @fclose($f);
                                    @chmod($file, 0644);
                                }
                                $upload_path .= $post_id . '/';
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
                                $uploadconfig['allowed_types'] =  '*';
                                $uploadconfig['max_size'] = 2 * 1024;
                                $uploadconfig['encrypt_name'] = true;

                                $this->upload->initialize($uploadconfig);
                                $_FILES['userfile']['name'] = $imageName;
                                $_FILES['userfile']['type'] = $fileinfo['2'];
                                $_FILES['userfile']['tmp_name'] = $imageName;
                                $_FILES['userfile']['error'] = 0;
                                $_FILES['userfile']['size'] = $fileinfo['bits'];

                                if ($this->upload->do_upload('userfile','crawl')) {
                                    $filedata = $this->upload->data();


                                    @unlink($imageName);

                                    $i=1;
                                    $uploadfiledata[$i] = array();
                                    $uploadfiledata[$i]['cfi_filename'] = element('brd_id', $board_crawl) . '/' . $post_id . '/' . element('file_name', $filedata);
                                    $uploadfiledata[$i]['cfi_originname'] = element('orig_name', $filedata);
                                    $uploadfiledata[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
                                    $uploadfiledata[$i]['cfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
                                    $uploadfiledata[$i]['cfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
                                    // $uploadfiledata[$i]['cfi_type'] = str_replace('.', '', element('file_ext', $filedata));

                                    $uploadfiledata[$i]['cfi_type'] = element('file_type', $filedata);                            
                                    $uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;

                                     $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path,element('file_type', $filedata));       
                                } else {
                                    $file_error = $this->upload->display_errors();
                                    break;
                                }


                                $file_updated = false;
                                $file_changed = false;
                                $updatedata = array();
                                if ($uploadfiledata
                                    && is_array($uploadfiledata)
                                    && count($uploadfiledata) > 0) {
                                    foreach ($uploadfiledata as $pkey => $pval) {
                                        if ($pval) {
                                            $updatedata['cit_file_' . $pkey] = element('cfi_filename', $pval);
                                            
                                            

                                            if(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) < 8920868) {
                                                $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                                
                                            } elseif(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) > 8920868) {
                                                $this->detect_label($cit_id,thumb_url('cmallitem',config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)),$ivalue['crawl_title']);
                                            }
                                            // if(element('crawl_title',$ivalue) && preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) && $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) && element('crawl_goods_code', $ivalue))
                                            // $updatedata['cit_val1'] = 0 ;
                                            // else $updatedata['cit_val1'] = 1 ;
                                        }
                                    }

                                   
                                    $this->Cmall_item_model->update($cit_id, $updatedata);
                                    
                                    
                                }
                            }

                        }
                        if(!$is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 1));
                        if($is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 3));
                    } else {
                        $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 3));
                        continue;
                    }
                }
            } else {
                echo element('pln_url', $value)."<br>";
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
                            'post_id' => $post_id,
                            'cit_goods_code' => element('crawl_goods_code', $ivalue),
                        );
                        
                        if($this->Cmall_item_model->count_by($where)) continue;

                        if(empty(element('crawl_goods_code', $ivalue))) continue;

                        $updatedata = array(
                            
                            'post_id' => $post_id,
                            'cit_name' => element('crawl_title',$ivalue),
                            'cit_summary' => element('crawl_sub_title',$ivalue) ,
                            'cit_price' => preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$ivalue))) ,
                            'cit_datetime' => cdate('Y-m-d H:i:s'),
                            'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                            'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                            'brd_id' => element('brd_id', $board_crawl),
                            'pln_id' => element('pln_id', $value),
                            'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                            'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                            'cit_status' => 1,
                            'cit_brand' => element('cit_brand',$ivalue) ? element('cit_brand',$ivalue) : 0,
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

                        $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url(element('img_src',$ivalue),element('pln_url', $value)));



                        
                        # 이미지 파일명 추출

                        $img_src_array = parse_url(urldecode($imageUrl));
                        // $img_src_array= explode('://', $imageUrl);
                        $img_src_array_= explode('/', $img_src_array['path']);
                        $imageName = end($img_src_array_);

                        $encode_url=array();
                        foreach($img_src_array as $u_key => $u_value){
                            $img_src_array[$u_key] = rawurlencode($img_src_array[$u_key]);
                        }
                        $imageUrl = $img_src_array['scheme'].'://'.$img_src_array['host'].$img_src_array['path'];
                        // $imageUrl = $img_src_array[0].'://'.$imageUrl;
                        
                        // $imageUrl = str_replace("%3F","?",$imageUrl);
                        // $imageUrl = str_replace("%26","&",$imageUrl);
                        $imageUrl = str_replace("%2F","/",$imageUrl);
                        echo "<br>".$imageUrl."<br>";

                        // $img_src_array= explode('/', $imageUrl);
                        // $imageName = end($img_src_array);
                        
                        
                        # 이미지 파일이 맞는 경우
                        if ($fileinfo = getimagesize($imageUrl)) {


                            # 이미지 다운로드
                            $imageFile = $this->extract_html($imageUrl,'','',element('referrer', $ivalue,''));

                            # 파일 생성 후 저장
                            $filetemp = fopen($imageName, 'w');
                            fwrite($filetemp, $imageFile['content']);
                            fclose($filetemp); // Closing file handle

                            $file_error = '';
                            $uploadfiledata = array();
                            $uploadfiledata2 = array();

                            $this->load->library('upload');
                            
                            $upload_path = config_item('uploads_dir') . '/cmallitem/';
                            if (is_dir($upload_path) === false) {
                                mkdir($upload_path, 0707);
                                $file = $upload_path . 'index.php';
                                $f = @fopen($file, 'w');
                                @fwrite($f, '');
                                @fclose($f);
                                @chmod($file, 0644);
                            }
                            $upload_path .= element('brd_id', $board_crawl) . '/';
                            if (is_dir($upload_path) === false) {
                                mkdir($upload_path, 0707);
                                $file = $upload_path . 'index.php';
                                $f = @fopen($file, 'w');
                                @fwrite($f, '');
                                @fclose($f);
                                @chmod($file, 0644);
                            }
                            $upload_path .= $post_id . '/';
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
                            $uploadconfig['allowed_types'] =  '*';
                            $uploadconfig['max_size'] = 2 * 1024;
                            $uploadconfig['encrypt_name'] = true;

                            $this->upload->initialize($uploadconfig);
                            $_FILES['userfile']['name'] = $imageName;
                            $_FILES['userfile']['type'] = $fileinfo['2'];
                            $_FILES['userfile']['tmp_name'] = $imageName;
                            $_FILES['userfile']['error'] = 0;
                            $_FILES['userfile']['size'] = $fileinfo['bits'];

                            if ($this->upload->do_upload('userfile','crawl')) {
                                $filedata = $this->upload->data();


                                @unlink($imageName);

                                $i=1;
                                $uploadfiledata[$i] = array();
                                $uploadfiledata[$i]['cfi_filename'] = element('brd_id', $board_crawl) . '/' . $post_id . '/' . element('file_name', $filedata);
                                $uploadfiledata[$i]['cfi_originname'] = element('orig_name', $filedata);
                                $uploadfiledata[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
                                $uploadfiledata[$i]['cfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
                                $uploadfiledata[$i]['cfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
                                // $uploadfiledata[$i]['cfi_type'] = str_replace('.', '', element('file_ext', $filedata));

                                $uploadfiledata[$i]['cfi_type'] = element('file_type', $filedata);                            
                                $uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;

                                 $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path,element('file_type', $filedata));       
                            } else {
                                $file_error = $this->upload->display_errors();
                                break;
                            }


                            $file_updated = false;
                            $file_changed = false;
                            $updatedata = array();
                            if ($uploadfiledata
                                && is_array($uploadfiledata)
                                && count($uploadfiledata) > 0) {
                                foreach ($uploadfiledata as $pkey => $pval) {
                                    if ($pval) {
                                        $updatedata['cit_file_' . $pkey] = element('cfi_filename', $pval);
                                        
                                        if(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) < 8920868) {
                                            $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                            
                                        } elseif(element('cfi_filename', $pval) && filesize(config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)) > 8920868) {


                                            $this->detect_label($cit_id,thumb_url('cmallitem',config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval)),$ivalue['crawl_title']);
                                        }
                                        
                                        // if(element('crawl_title',$ivalue) && preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) && $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))) && element('crawl_goods_code', $ivalue))
                                        //     $updatedata['cit_val1'] = 0 ;
                                        // else $updatedata['cit_val1'] = 1 ;
                                    }
                                }
                                
                                $this->Cmall_item_model->update($cit_id, $updatedata);
                                
                            }
                        } else 
                            $is_pln_error = true;
                    }
                    if(!$is_pln_error)
                        $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 1));
                    if($is_pln_error)
                        $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 3));
                } else {
                    $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 3));
                    continue;
                }
            
            
            }
            

        }

        // $this->crawling_cit_type($post_id,'cit_type3');
        // $this->crawling_cit_type($post_id,'cit_type2');
        // redirect(post_url(element('brd_key', $board),$post_id));
           

        
         

        
         
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
    

    public function crawling_category_update($post_id=0)
    {



        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
        $this->load->event($eventname);

        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            show_404();
        }

        $post = $this->Post_model->get_one($post_id);
        if ( ! element('post_id', $post)) {
            show_404();
        }

        // if(strstr(strtolower(element('post_title',$post)),'신상') || strstr(strtolower(element('post_title',$post)),'new') || strstr(strtolower(element('post_title',$post)),'best') || strstr(strtolower(element('post_title',$post)),'베스트') || strstr(strtolower(element('post_title',$post)),'추천') || strstr(strtolower(element('post_title',$post)),'인기')) return false ;
        

        $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($post_id);
        $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);

        $board = $this->board->item_all(element('brd_id', $post));

        $c_category=array();
        $category='';
        $all_category=array();
        $all_attr=array();

        $category = $this->Board_group_category_model->get_category_info(1, element('post_category', $post));
        if($category)
            $c_category[] = $category['bca_value'];
        if(element('bca_parent', $category)){
            $category = $this->Board_group_category_model->get_category_info(1, element('bca_parent', $category));    
            $c_category[] = $category['bca_value'];
        }
        if(element('bca_parent', $category)){
            $category = $this->Board_group_category_model->get_category_info(1, element('bca_parent', $category));    
            $c_category[] = $category['bca_value'];
        }
        if(element('bca_parent', $category)){
            $category = $this->Board_group_category_model->get_category_info(1, element('bca_parent', $category));    
            $c_category[] = $category['bca_value'];
        }

        
        
        $all_category = $this->Cmall_category_model->get_all_category();
        $all_attr = $this->Cmall_attr_model->get_all_attr();
        
        
        
        
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
        

        $crawl = $this->Cmall_item_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');

        foreach ($crawl as $c_key => $c_value) {
            $cmall_category=array();
            $cmall_attr=array();
            $crawl_tag_arr=array();
            $vision_label_arr=array();
            $crawl_tag_text=array();


            $crawl_tag_arr = $this->Crawl_tag_model->get('','',array('cit_id' => element('cit_id',$c_value)));

            foreach($crawl_tag_arr as $t_value){
                
                array_push($crawl_tag_text,element('cta_tag',$t_value));
            }


            $vision_label_arr = $this->Vision_api_label_model->get('','',array('cit_id' => element('cit_id',$c_value)));

            foreach($vision_label_arr as $l_value){
                
                array_push($crawl_tag_text,element('val_tag',$l_value));
            }

            

            $deletewhere = array(
                'cit_id' => element('cit_id',$c_value),
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
                $this->Cmall_category_rel_model->save_category(element('cit_id',$c_value), $cmall_category);    

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
                $this->Cmall_category_rel_model->save_category(element('cit_id',$c_value), $cmall_category);


            $deletewhere = array(
                'cit_id' => element('cit_id',$c_value),
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
                $this->Cmall_attr_rel_model->save_attr(element('cit_id',$c_value), $cmall_attr);    

            }

        }

    }

    public function crawling_tag_update($post_id=0)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
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
        $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);

        $board = $this->board->item_all(element('brd_id', $post));
        
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
            'brd_id' => element('brd_id', $post),
        );

        $board_crawl = $this->Board_crawl_model->get_one('','',$crawlwhere);
        if ( ! element('brd_id', $board_crawl)) {
            show_404();
        }

        
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

        $postwhere = array(
            'post_id' => $post_id,
            // 'cit_id' => 9347,
        );
        

        $crawl = $this->Cmall_item_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'asc');


        
        require_once FCPATH . 'plugin/simplehtmldom/simple_html_dom.php';
        $a=0;
        foreach ($crawl as  $value) {


            $where = array(
                    'cit_id' => element('cit_id',$value),
                );
            $crawl_tag_count = $this->Crawl_tag_model->count_by($where);            
            if($crawl_tag_count) continue;
            $tag_ = array();
            echo 'cit_post_url : '.element('cit_post_url', $value)."<br>";
            $result = $this->extract_html(element('cit_post_url', $value), $proxy, $proxy_userpwd);
            
            $linkupdate = array(
                'pln_status' => 6,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);

            // $itemupdate = array(
            //     'cit_val1' => 1,
            // );

            // $this->Cmall_item_model->update(element('cit_id',$value),$itemupdate);  
            if($result['code']===200){

                // 기존 항목을 모두 지운다.
                

                $html = new simple_html_dom();
                
                $html->load($result['content']);

                $cit_info=array();
                $cit_img=array();
                $cit_text=array();
                
                $cit_info['crawl_title'] = '';
                $cit_info['crawl_sub_title'] = '';
                $cit_info['crawl_price'] = '';
                $cit_info['crawl_brand'] = '';
                $cit_info['crawl_size'] = '';
                $cit_info['crawl_color'] = '';
                $cit_info['cit_is_soldout'] = '';
                $cit_info['crawl_material'] = '';

                if(element('post_content_detail', $post))
                    eval(element('post_content_detail', $post));
                elseif(element('brd_content_detail', $board_crawl))
                    eval(element('brd_content_detail', $board_crawl));
                
                
                

                $cit_info['cit_brand'] = $this->cmall_brand($cit_info['crawl_brand']);
                
                // if(empty($cit_info['cit_brand'])){
                //     $crawl_title_ = element('crawl_title',$cit_info) ? element('crawl_title',$cit_info) : element('cit_name',$value);

                //     $crawl_title_ = str_replace(" ","",$crawl_title_);
                //     $crawl_title_ = str_replace("[","",$crawl_title_);
                //     $crawl_title_ = str_replace("]","",$crawl_title_);

                //     $cit_info['cit_brand'] = $this->cmall_brand($crawl_title_);
                // }
                

                $updatedata = array(                                        
                    
                    'cit_name' => element('crawl_title',$cit_info) ? element('crawl_title',$cit_info) : element('cit_name',$value),
                    'cit_summary' => element('crawl_sub_title',$cit_info) ? element('crawl_sub_title',$cit_info) : element('cit_summary',$value) ,
                    'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$cit_info)) ? preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$cit_info))) : element('cit_price',$value) ,
                    'cit_brand' => element('cit_brand',$cit_info) ? element('cit_brand',$cit_info) : element('cit_brand', $value),
                    'cit_is_soldout' => element('crawl_is_soldout', $cit_info) ? element('crawl_is_soldout', $cit_info) : element('cit_is_soldout', $value),
                    
                );

                

                $this->Cmall_item_model->update(element('cit_id',$value),$updatedata);

                foreach($cit_img as $ikey => $ivalue){


                    


                    



                    # 이미지 URL 추출
                   

                    $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url(element('img_src', $ivalue),element('cit_post_url', $value)));


                    
                    # 이미지 파일명 추출
                    $img_src_array = parse_url(urldecode($imageUrl));
                        // $img_src_array= explode('://', $imageUrl);
                        $img_src_array_= explode('/', $img_src_array['path']);
                        $imageName = end($img_src_array_);

                        $encode_url=array();
                        foreach($img_src_array as $u_key => $u_value){
                            $img_src_array[$u_key] = rawurlencode($img_src_array[$u_key]);
                        }
                        $imageUrl = $img_src_array['scheme'].'://'.$img_src_array['host'].$img_src_array['path'];
                        // $imageUrl = $img_src_array[0].'://'.$imageUrl;
                        
                        // $imageUrl = str_replace("%3F","?",$imageUrl);
                        // $imageUrl = str_replace("%26","&",$imageUrl);
                        $imageUrl = str_replace("%2F","/",$imageUrl);
                        echo "<br>".$imageUrl."<br>";

                    if($this->get_extension($imageName) ==='gif') continue;

                    # 이미지 파일이 맞는 경우
                    if ($fileinfo = getimagesize($imageUrl)) {
                        
                        
                        if($fileinfo['1'] < 80) continue;
                        # 이미지 다운로드
                        
                        $imageFile = $this->extract_html($imageUrl,'','',element('referrer', $ivalue,''));

                        # 파일 생성 후 저장
                        $filetemp = fopen($imageName, 'w');
                        fwrite($filetemp, $imageFile['content']);
                        fclose($filetemp); // Closing file handle

                        
                        if($imageName && filesize($imageName) < 8920868) {
                            $tag_[] = $this->detect_tag(element('cit_id',$value),$imageName);
                        } elseif($imageName && filesize($imageName) > 8920868) {
                            $tag_[] = $this->detect_tag(element('cit_id',$value),thumb_url('cmallitem',$imageName));
                        }

                         @unlink($imageName);
                        
                    }
                }

// echo "<br>cit_info";
// print_r($cit_info);
// echo "<br>cit_text";
// print_r($cit_text);
// echo "<br>cit_img";
// print_r($cit_img);
// continue;

                foreach($cit_text as $tkey => $tvalue){
                    if(element('text',$tvalue))
                        $tag_[] = $this->detect_tag2(element('text',$tvalue));
                }

                   
                $tag_[] = $this->detect_tag3(element('crawl_title',$cit_info) ? element('crawl_title',$cit_info) : element('cit_name',$value),element('crawl_sub_title',$cit_info) ? element('crawl_sub_title',$cit_info) : element('cit_summary',$value));
                $a++;
           
                // if($a > 10 ) exit;
            } else {
                $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 7));
                continue;
            }

            $translate_text = array();
            foreach($tag_ as $word){
                foreach($word as $val_){                            
                    
                    if(!in_array($val_,$translate_text))
                        array_push($translate_text,$val_);       
                     
                }
            }

            if(count($translate_text)){
                

                
                // $deletewhere = array(
                //     'cit_id' => element('cit_id',$value),
                // );
                // $this->Crawl_tag_model->delete_where($deletewhere);            
                if ($translate_text && is_array($translate_text)) {
                    foreach ($translate_text as  $text) {
                        // $value = trim($value);
                        if ($text) {
                            $tagdata = array(
                                'post_id' => element('post_id', $value),
                                'cit_id' => element('cit_id', $value),
                                'brd_id' => element('brd_id', $value),
                                'cta_tag' => $text,
                            );
                            $this->Crawl_tag_model->insert($tagdata);
                        }
                    }
                }
                $linkupdate = array(
                    'pln_status' => 1,
                );

                $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
            } else {

                $linkupdate = array(
                    'pln_status' => 7,
                );
                $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
            }

            // $itemupdate = array(
            //     'cit_val1' => 0,
            // );
            // if($cit_info['cit_brand'] && (element('crawl_title',$cit_info) || element('cit_name',$value)) && (preg_replace("/[^0-9]*/s", "", element('crawl_price',$cit_info)) || element('cit_price',$value)))
            //     $this->Cmall_item_model->update(element('cit_id',$value),$itemupdate);  
        }
        


    }


    public function crawling_tag_overwrite($post_id=0)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_crawl_index';
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
        $post['meta'] = $this->Post_meta_model->get_all_meta($post_id);

        $board = $this->board->item_all(element('brd_id', $post));
        
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
            'brd_id' => element('brd_id', $post),
        );

        $board_crawl = $this->Board_crawl_model->get_one('','',$crawlwhere);
        if ( ! element('brd_id', $board_crawl)) {
            show_404();
        }

        
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

        $postwhere = array(
            'post_id' => $post_id,
            // 'cit_id' => 9347,
        );
        

        $crawl = $this->Cmall_item_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'asc');


        
        require_once FCPATH . 'plugin/simplehtmldom/simple_html_dom.php';
        $a=0;
        foreach ($crawl as  $value) {
            $tag_ = array();
            echo 'cit_post_url : '.element('cit_post_url', $value)."<br>";
            $result = $this->extract_html(element('cit_post_url', $value), $proxy, $proxy_userpwd);
            
            $linkupdate = array(
                'pln_status' => 6,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);

            // $itemupdate = array(
            //     'cit_val1' => 1,
            // );

            // $this->Cmall_item_model->update(element('cit_id',$value),$itemupdate);  
            if($result['code']===200){

                // 기존 항목을 모두 지운다.
                

                $html = new simple_html_dom();
                
                $html->load($result['content']);

                $cit_info=array();
                $cit_img=array();
                $cit_text=array();
                
                $cit_info['crawl_title'] = '';
                $cit_info['crawl_sub_title'] = '';
                $cit_info['crawl_price'] = '';
                $cit_info['crawl_brand'] = '';
                $cit_info['crawl_size'] = '';
                $cit_info['crawl_color'] = '';
                $cit_info['cit_is_soldout'] = '';
                $cit_info['crawl_material'] = '';

                if(element('post_content_detail', $post))
                    eval(element('post_content_detail', $post));
                elseif(element('brd_content_detail', $board_crawl))
                    eval(element('brd_content_detail', $board_crawl));
                
                
                

                $cit_info['cit_brand'] = $this->cmall_brand($cit_info['crawl_brand']);
                
                // if(empty($cit_info['cit_brand'])){
                //     $crawl_title_ = element('crawl_title',$cit_info) ? element('crawl_title',$cit_info) : element('cit_name',$value);

                //     $crawl_title_ = str_replace(" ","",$crawl_title_);
                //     $crawl_title_ = str_replace("[","",$crawl_title_);
                //     $crawl_title_ = str_replace("]","",$crawl_title_);

                //     $cit_info['cit_brand'] = $this->cmall_brand($crawl_title_);
                // }
                

                $updatedata = array(                                        
                    
                    'cit_name' => element('crawl_title',$cit_info) ? element('crawl_title',$cit_info) : element('cit_name',$value),
                    'cit_summary' => element('crawl_sub_title',$cit_info) ? element('crawl_sub_title',$cit_info) : element('cit_summary',$value) ,
                    'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$cit_info)) ? preg_replace("/[^0-9]*/s", "", str_replace("&#8361;","",element('crawl_price',$cit_info))) : element('cit_price',$value) ,
                    'cit_brand' => element('cit_brand',$cit_info) ? element('cit_brand',$cit_info) : element('cit_brand', $value),
                    'cit_is_soldout' => element('crawl_is_soldout', $cit_info) ? element('crawl_is_soldout', $cit_info) : element('cit_is_soldout', $value),
                    
                );

                

                $this->Cmall_item_model->update(element('cit_id',$value),$updatedata);

                foreach($cit_img as $ikey => $ivalue){


                    


                    



                    # 이미지 URL 추출
                   

                    $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url(element('img_src', $ivalue),element('cit_post_url', $value)));


                    
                    # 이미지 파일명 추출
                    $img_src_array = parse_url(urldecode($imageUrl));
                        // $img_src_array= explode('://', $imageUrl);
                        $img_src_array_= explode('/', $img_src_array['path']);
                        $imageName = end($img_src_array_);

                        $encode_url=array();
                        foreach($img_src_array as $u_key => $u_value){
                            $img_src_array[$u_key] = rawurlencode($img_src_array[$u_key]);
                        }
                        $imageUrl = $img_src_array['scheme'].'://'.$img_src_array['host'].$img_src_array['path'];
                        // $imageUrl = $img_src_array[0].'://'.$imageUrl;
                        
                        // $imageUrl = str_replace("%3F","?",$imageUrl);
                        // $imageUrl = str_replace("%26","&",$imageUrl);
                        $imageUrl = str_replace("%2F","/",$imageUrl);
                        echo "<br>".$imageUrl."<br>";

                    if($this->get_extension($imageName) ==='gif') continue;

                    # 이미지 파일이 맞는 경우
                    if ($fileinfo = getimagesize($imageUrl)) {
                        
                        
                        if($fileinfo['1'] < 80) continue;
                        # 이미지 다운로드
                        
                        $imageFile = $this->extract_html($imageUrl,'','',element('referrer', $ivalue,''));

                        # 파일 생성 후 저장
                        $filetemp = fopen($imageName, 'w');
                        fwrite($filetemp, $imageFile['content']);
                        fclose($filetemp); // Closing file handle

                        
                        if($imageName && filesize($imageName) < 8920868) {
                            $tag_[] = $this->detect_tag(element('cit_id',$value),$imageName);
                        } elseif($imageName && filesize($imageName) > 8920868) {
                            $tag_[] = $this->detect_tag(element('cit_id',$value),thumb_url('cmallitem',$imageName));
                        }

                         @unlink($imageName);
                        
                    }
                }

// echo "<br>cit_info";
// print_r($cit_info);
// echo "<br>cit_text";
// print_r($cit_text);
// echo "<br>cit_img";
// print_r($cit_img);
// continue;

                foreach($cit_text as $tkey => $tvalue){
                    if(element('text',$tvalue))
                        $tag_[] = $this->detect_tag2(element('text',$tvalue));
                }

                   
                $tag_[] = $this->detect_tag3(element('crawl_title',$cit_info) ? element('crawl_title',$cit_info) : element('cit_name',$value),element('crawl_sub_title',$cit_info) ? element('crawl_sub_title',$cit_info) : element('cit_summary',$value));
                $a++;
           
                // if($a > 10 ) exit;
            } else {
                $this->Post_link_model->update(element('pln_id',$value),array( 'pln_status' => 7));
                continue;
            }

            $translate_text = array();
            foreach($tag_ as $word){
                foreach($word as $val_){                            
                    
                    if(!in_array($val_,$translate_text))
                        array_push($translate_text,$val_);       
                     
                }
            }

            if(count($translate_text)){
                

                
                $deletewhere = array(
                    'cit_id' => element('cit_id',$value),
                );
                $this->Crawl_tag_model->delete_where($deletewhere);            
                if ($translate_text && is_array($translate_text)) {
                    foreach ($translate_text as  $text) {
                        // $value = trim($value);
                        if ($text) {
                            $tagdata = array(
                                'post_id' => element('post_id', $value),
                                'cit_id' => element('cit_id', $value),
                                'brd_id' => element('brd_id', $value),
                                'cta_tag' => $text,
                            );
                            $this->Crawl_tag_model->insert($tagdata);
                        }
                    }
                }
                $linkupdate = array(
                    'pln_status' => 1,
                );

                $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
            } else {

                $linkupdate = array(
                    'pln_status' => 7,
                );
                $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
            }

            // $itemupdate = array(
            //     'cit_val1' => 0,
            // );
            // if($cit_info['cit_brand'] && (element('crawl_title',$cit_info) || element('cit_name',$value)) && (preg_replace("/[^0-9]*/s", "", element('crawl_price',$cit_info)) || element('cit_price',$value)))
            //     $this->Cmall_item_model->update(element('cit_id',$value),$itemupdate);  
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

                if($translate){
                    $translation = $this->translate->translate($label->getDescription(), [
                        'target' => $target
                    ]);
                    
                    array_push($translate_text,$translation['text']);
                
                } else {

                    array_push($translate_text,$label->getDescription());
                }        
            }
            
                
            
        } else {
            return 'No label found';
        }
        
        if(count($translate_text)){
            $deletewhere = array(
                'cit_id' => element('cit_id', $citem),
            );
            $this->Vision_api_label_model->delete_where($deletewhere);            
            if ($translate_text && is_array($translate_text)) {
                foreach ($translate_text as $key => $value) {
                    $value = trim($value);
                    if ($value) {
                        $tagdata = array(
                            'post_id' => element('post_id', $citem),
                            'cit_id' => element('cit_id', $citem),
                            'brd_id' => element('brd_id', $citem),
                            'val_tag' => $value,
                        );
                        $this->Vision_api_label_model->insert($tagdata);
                    }
                }
            }

            // $convert_text = $this->label_tag_convert($translate_text,$crawl_title);
            // $deletewhere = array(
            //     'cit_id' => element('cit_id', $citem),
            // );
            // $this->Crawl_tag_model->delete_where($deletewhere);            
            // if ($convert_text && is_array($convert_text)) {
            //     foreach ($convert_text as $key => $value) {
            //         $value = trim($value);
            //         if ($value) {
            //             $tagdata = array(
            //                 'post_id' => element('post_id', $citem),
            //                 'cit_id' => element('cit_id', $citem),
            //                 'brd_id' => element('brd_id', $citem),
            //                 'cta_tag' => $value,
            //             );
            //             $this->Crawl_tag_model->insert($tagdata);
            //         }
            //     }
            // }
            
        }
        

        $this->imageAnnotator->close();
        
        return $translate_text;



        
    }   

    function detect_tag2($crawl_text='',$translate=0)
    {   
// return;
        $translate_text=array();


        

        
            
             
            
        
        $naturalentity =array();
        $naturalentity_word = '';
        
        $language = $this->naturallanguage->analyzeEntities($crawl_text);

        foreach ($language->entities() as $entity) {
             $naturalentity[$entity['name']] = $entity['name'];
        }
        
        // foreach ($naturalentity as $val) {

            

        //     $naturalentity_word .= $val;
        // }

        
            
            
        foreach($this->tag_word as $word){
            foreach ($naturalentity as $val) {
                // if(strpos(strtolower($val),strtolower(str_replace(" ","",$word))) !== false ){
                if(strtolower(str_replace(" ","",$val)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){
                    if(!in_array(element('tgw_value',$word),$translate_text))
                        array_push($translate_text,element('tgw_value',$word));       
                } 
            }
        }

        
        
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

        // $all_category = $this->Cmall_category_model->get_all_category();

        $target = 'ko';

        if ($texts) {
            
             
            
            foreach ($texts as $text) {

                $naturalentity =array();
                $naturalentity_word = '';

                if(strlen($text->getDescription()) < 20) continue;
                
                $language = $this->naturallanguage->analyzeEntities($text->getDescription());
                

                foreach ($language->entities() as $entity) {
                     $naturalentity[$entity['name']] = $entity['name'];

                }
                
                // foreach ($naturalentity as $val) {

                    

                //     $naturalentity_word .= $val;
                // }

                if($translate){
                    $translation = $this->translate->translate($text->getDescription(), [
                        'target' => $target
                    ]);
                    
                    array_push($translate_text,$translation['text']);
                
                } else {
                    
                    
                    foreach($this->tag_word as $word){
                        foreach ($naturalentity as $val) {
                            // if(strpos(strtolower($val),strtolower(str_replace(" ","",$word))) !== false ){
                            if(strtolower(str_replace(" ","",$val)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){                                
                                if(!in_array(element('tgw_value',$word),$translate_text))
                                    array_push($translate_text,element('tgw_value',$word));       
                            } 
                        }
                    }
                    
                }       

                
               
            }
            
                
            
        } else {
            return 'No label found';
        }
        
        

        $this->imageAnnotator->close();
        
        return $translate_text;



        
    }   

    function detect_tag3($cit_name='',$cit_summary ='')
    {


        // return;
        

        
        $translate_text=array();

        $all_category=array();

        // $all_category = $this->Cmall_category_model->get_all_category();
        
            
             
            
            
        
        $naturalentity_ =array();
        if($cit_name){
            $language_ = explode(" ",$cit_name);

            foreach ($language_ as $entity) {
                 $naturalentity_[$entity] = $entity;
            }
        }
        
        if($cit_summary){
            $language_ = $this->naturallanguage->analyzeEntities($cit_summary);

            foreach ($language_->entities() as $entity) {
                 $naturalentity_[$entity['name']] = $entity['name'];
            }
        }

        foreach($this->tag_word as $word){
            foreach ($naturalentity_ as $val) {
                $arr_str = preg_split("//u", element('tgw_value',$word), -1, PREG_SPLIT_NO_EMPTY);
                
                if(count($arr_str) > 1){
                    if(strpos(strtolower(str_replace(" ","",$val)),strtolower(str_replace(" ","",element('tgw_value',$word)))) !== false ){
                        if(!in_array(element('tgw_value',$word),$translate_text))
                            array_push($translate_text,element('tgw_value',$word));       
                    }     
                } else {
                    if(strtolower(str_replace(" ","",$val)) === strtolower(str_replace(" ","",element('tgw_value',$word)))){
                        if(!in_array(element('tgw_value',$word),$translate_text))
                            array_push($translate_text,element('tgw_value',$word));       
                    }     
                }
                
                
            }
            
            
        }

        
        
        return $translate_text;



        
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
            
            default:
                show_404();
                break;
        }
        
        
        $board_id = $this->Board_model->get_board_list($where);
        $board_list = array();
        if ($board_id && is_array($board_id)) {
            foreach ($board_id as $key => $val) {

                $where = array(
                    'brd_id' => element('brd_id', $val),
                );
                $where['post_del <>'] = 2;
                

                
                $result = $this->Post_model
                    ->get_post_list('', '', $where);                

                if (element('list', $result)) {
                    foreach (element('list', $result) as $key => $val) {
                        if($crawl_type==='update'){
                            $this->crawling_update(element('post_id', $val));
                        } 

                        if($crawl_type==='overwrite'){
                            $this->crawling_overwrite(element('post_id', $val));
                        }
                        if($crawl_type==='tag_update'){
                            
                            $this->crawling_tag_update(element('post_id', $val));
                        }
                        if($crawl_type==='tag_overwrite'){
                            
                            $this->crawling_tag_overwrite(element('post_id', $val));
                        }
                        if($crawl_type==='category_update'){
                            $this->crawling_category_update(element('post_id', $val));
                        }
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
                
                    if(count($cbr_value_en) > 2){
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


                    

                    

                    if(count($arr_str_kr) > 1){
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
    
}
