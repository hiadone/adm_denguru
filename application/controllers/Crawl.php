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


class Crawl extends CB_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Post','Post_link','Post_extra_vars','Post_meta','Crawl','Crawl_link', 'Crawl_file','Crawl_tag','Vision_api_label','Board_crawl','Cmall_item','Cmall_category', 'Cmall_category_rel','Board_category','Board_group_category');

    protected $imageAnnotator = null;
    protected $translate = null;


    protected $tag_word = array(
                            'sesame street' => array('sesame','sst','세서미'),
                            '빕' => array('빕','bib'),
                            '빅버드' => array('빅버드'),
                            '양털' => array('fleece','양털'),
                            '레트로' => array('레트로','retro','복고'),
                            );

    protected $brand_word = array('쿠로모찌' => 
                                array('쿠로모찌','KUROMOCHI'),
                            );

    protected $category_word = array(
                                '패션'=> array(
                                        '탑',
                                        '아우터'=> array('니트/스웨터/가디건','패딩'),
                                        '티셔츠/블라우스/후드'=> array('티셔츠','민소매 티셔츠','셔츠/블라우스','맨투맨/스웨트셔츠','후드'),
                                        '드레스/스커트',
                                        '올인원/팬츠',
                                        '케이프/스카프/머플러',
                                        '모자/가방',
                                        '악세서리' => array('신발/양말','안경/선글라스','헤어핀/주얼리'),
                                        '시즌상품/코스튬'=> array('수영복/구명조끼','한복','할로윈','생일파티','레인코트'),
                                        '펫펨족(커플룩)',
                                        ),
                                '푸드'=> array(
                                        '주식' => array('습식사료','자연식','건식사료','에어/동결건조 사료','소프트사료'),
                                        '간식'=> array('수제간식','동결/건조간식','치석제거껌','뼈간식','사사미/육포','저키/스틱/트릿','캔/파우치','분유/우유/음료','빵/케이크/쿠키','소시지/파우더'),
                                        '영양제'=> array('유산균','종합영양제','오메가3','관절/칼슘','면역력','눈건강','피부/모질','덴탈케어','불안/스트레스','심장/심혈관'),
                                        ),

                                '산책·외출'=> array(
                                        '하네스/가슴줄/목줄/리드줄' => array('하네스/가슴줄','목줄','리드줄'),
                                        '인식표/목걸이',
                                        '산책용품'=> array('산책가방','배변봉투','물통','기타'),
                                        ),
                                '이동' => array(
                                        '이동가방/슬링백' => array('이동가방','슬링백','백팩'),
                                        '유모차/캐리어' => array('유모차/웨건','캐리어/이동장'),
                                        '카시트/차량용품'=> array('카시트','차량용품'),
                                        ),
                                '홈·리빙(펫테리어)' => array(
                                        '하우스' => array('하우스','켄넬','안전문/펜스','펫도어'),
                                        '쿠션/방석/침대',
                                        '식기'=> array('식탁세트','세라믹','스텐','유리','자동급식기','기타'),
                                        '계단/스텝',
                                        '미끄럼방지 매트',
                                        '스페셜데이'=> array('파티용품','생일상','포토존','명절'),
                                        ),
                                '놀이·장난감'=> array(
                                        '노즈워크',
                                        '장난감/인형',
                                        '공/원반',
                                        '훈련용품',
                                        ),
                                '미용·목욕·위생·배변'=> array(
                                        '배변패드/배변판/기저귀'=> array('배변패드/배변판','기저귀'),
                                        '목욕용품'=> array('샴푸/컨디셔너/비누','스파/입욕제','욕조','타올/드라이가운','드라이룸'),
                                        '브러쉬/미용관리'=> array('브러쉬','보습제','미스트','발톱깎이','미용가위/클리퍼'),
                                        '구강관리' => array('치약/칫솔','구강티슈'),
                                        '눈/귀 관리' => array('눈세정제','귀세정제'),
                                        '넥카라',
                                        '탈취/소독/위생용품',
                                        ),
                                '기타',
                                );

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
        $this->load->library(array('querystring','aws_s3','form_validation'));

        $this->imageAnnotator = new ImageAnnotatorClient([
            'credentials' => './denguru2-51a54-34c83efd96e6.json'
        ]);


        # Instantiates a client
        $this->translate = new TranslateClient([
            'key' => config_item('translate_key')
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
            
            $linkupdate = array(
                'pln_error' => 1,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);


            if(element('pln_page', $value)){
                $param =& $this->querystring;

                $pln_url = parse_url(element('pln_url', $value));

                parse_str($pln_url['query'] ,$query_string);
                
                
                


                for($page=$query_string['page'];element('pln_page', $value) >= $page;$page++){
                    echo $pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query'])."<br>";
                    
                    $result = $this->extract_html($pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query']), $proxy, $proxy_userpwd);

                    if($result['code']===200){

                        // 기존 항목을 모두 지운다.
                        
                        

                        

                        $html = new simple_html_dom();
                        $html->load($result['content']);

                        $crawl_info=array();
                        $crawl_img=array();
                        $is_pln_error=false;
                        

                        if(element('post_content', $post))
                            eval(element('post_content', $post));
                        elseif(element('brd_content', $board_crawl))
                            eval(element('brd_content', $board_crawl));
                        
             
                        if(count($crawl_info) && count($crawl_img)){

                            foreach($crawl_info as $ikey => $ivalue){

                                $flag=false;
                                foreach ($cmall as $c_key => $c_value) {
                                    if(element('crawl_goods_code',$ivalue) === element('cit_goods_code',$c_value)){
                                        unset($cmall_out[$c_key]);
                                        $flag=true;
                                        break;
                                    }
                                }

                                if($flag){
                                    

                                    $updatedata = array(                                        
                                        'post_id' => $post_id,
                                        'cit_name' => element('crawl_title',$ivalue) ,
                                        'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                        'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                                        'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                        'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                        'brd_id' => element('brd_id', $board_crawl),
                                        'pln_id' => element('pln_id', $value),
                                        'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                        'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                        
                                    );

                                    $this->Cmall_item_model->update(element('cit_id',$c_value),$updatedata);
                                     
                                } else {

                                    // $this->board->delete_cmall(element('cit_id',$o_value));

                                     $updatedata = array(
                                
                                        'post_id' => $post_id,
                                        'cit_name' => element('crawl_title',$ivalue) ,
                                        'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                        'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                                        'cit_datetime' => cdate('Y-m-d H:i:s'),
                                        'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                        'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                        'brd_id' => element('brd_id', $board_crawl),
                                        'pln_id' => element('pln_id', $value),
                                        'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                        'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                        'cit_status' => 1,
                                        
                                    );

                                    $cit_id = $this->Cmall_item_model->insert($updatedata);

                                    $updatedata = array();
                                    $updatedata['cit_key'] = 'c_'.$cit_id;
                                            
                                    $this->Cmall_item_model->update($cit_id, $updatedata);

                                    # 이미지 URL 추출
                                    // $imageUrl = $this->valid_url($board_crawl,$crawl_img[$ikey]['img_src']);
                                    $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url($crawl_img[$ikey]['img_src'],element('pln_url', $value)));
                                    
                                    # 이미지 파일명 추출
                                    
                                    $img_src_array= explode('/', $imageUrl);
                                    $imageName = end($img_src_array);
                                    
                                    

                                    # 이미지 파일이 맞는 경우
                                    if ($fileinfo = getimagesize($imageUrl)) {


                                        # 이미지 다운로드
                                        $imageFile = $this->extract_html($imageUrl);

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
                                            $uploadfiledata[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
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
                                                   $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                                   
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
                            continue;    
                        }
                    } else {
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
                $linkupdate = array(
                    'pln_error' => 0,
                );
                if(!$is_pln_error)
                    $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
            } else {
                echo element('pln_url', $value)."<br>";
                $result = $this->extract_html(element('pln_url', $value), $proxy, $proxy_userpwd);

                if($result['code']===200){
                    // 기존 항목을 모두 지운다.
                    
                    

                    

                    $html = new simple_html_dom();
                    $html->load($result['content']);

                    $crawl_info=array();
                    $crawl_img=array();
                    $is_pln_error=false;
                    

                    if(element('post_content', $post))
                        eval(element('post_content', $post));
                    elseif(element('brd_content', $board_crawl))
                        eval(element('brd_content', $board_crawl));
                    
                    
                    if(count($crawl_info) && count($crawl_img)){

                        foreach($crawl_info as $ikey => $ivalue){

                            $flag=false;
                            foreach ($cmall as $c_key => $c_value) {
                                if(element('crawl_goods_code',$ivalue) === element('cit_goods_code',$c_value)){
                                    unset($cmall_out[$c_key]);
                                    $flag=true;
                                    break;
                                }
                            }

                            if($flag){
                                

                                $updatedata = array(                                        
                                    'post_id' => $post_id,
                                    'cit_name' => element('crawl_title',$ivalue) ,
                                    'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                    'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                                    'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                    'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                    'brd_id' => element('brd_id', $board_crawl),
                                    'pln_id' => element('pln_id', $value),
                                    'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                    'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                    
                                );

                                $this->Cmall_item_model->update(element('cit_id',$c_value),$updatedata);
                                 
                            } else {

                                // $this->board->delete_cmall(element('cit_id',$o_value));

                                 $updatedata = array(
                            
                                    'post_id' => $post_id,
                                    'cit_name' => element('crawl_title',$ivalue) ,
                                    'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                    'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                                    'cit_datetime' => cdate('Y-m-d H:i:s'),
                                    'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                    'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                    'brd_id' => element('brd_id', $board_crawl),
                                    'pln_id' => element('pln_id', $value),
                                    'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                    'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                    'cit_status' => 1,
                                    
                                );

                                $cit_id = $this->Cmall_item_model->insert($updatedata);

                                $updatedata = array();
                                $updatedata['cit_key'] = 'c_'.$cit_id;
                                        
                                $this->Cmall_item_model->update($cit_id, $updatedata);

                                # 이미지 URL 추출
                                // $imageUrl = $this->valid_url($board_crawl,$crawl_img[$ikey]['img_src']);
                                $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url($crawl_img[$ikey]['img_src'],element('pln_url', $value)));
                                
                                # 이미지 파일명 추출
                                
                                $img_src_array= explode('/', $imageUrl);
                                $imageName = end($img_src_array);
                                
                                

                                # 이미지 파일이 맞는 경우
                                if ($fileinfo = getimagesize($imageUrl)) {


                                    # 이미지 다운로드
                                    $imageFile = $this->extract_html($imageUrl);

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
                                        $uploadfiledata[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
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
                                               $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                               
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
                        $linkupdate = array(
                            'pln_error' => 0,
                        );
                        if(!$is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
                    } else {
                        continue;    
                    }
                }
            }
        }


        
        $this->crawling_cit_type($post_id,'cit_type3');
        $this->crawling_cit_type($post_id,'cit_type2');
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


    function extract_html($url, $proxy='', $proxy_userpwd='') {


        $response = array();
        $response['code']='';
        $response['message']='';
        $response['status']=false;  
        
        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';
        
        // Some websites require referrer
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $referrer = $scheme . '://' . $host; 
        
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($curl, CURLOPT_POST, 0);
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
                'pln_error' => 1,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);

            if(element('pln_page', $value)){
                $param =& $this->querystring;

                $pln_url = parse_url(element('pln_url', $value));

                parse_str($pln_url['query'] ,$query_string);
                
                
                


                for($page=$query_string['page'];element('pln_page', $value) >= $page;$page++){
                    $result = $this->extract_html($pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query']), $proxy, $proxy_userpwd);

                    if($result['code']===200){

                        // 기존 항목을 모두 지운다.

                        $html = new simple_html_dom();
                        $html->load($result['content']);

                        $crawl_info=array();
                        $crawl_img=array();
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
                            'pln_error' => 0,
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
                    $crawl_img=array();
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
                        'pln_error' => 0,
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
                'pln_error' => 1,
            );

            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);

            if(element('pln_page', $value)){
                $param =& $this->querystring;

                $pln_url = parse_url(element('pln_url', $value));

                parse_str($pln_url['query'] ,$query_string);
                
                
                


                for($page=$query_string['page'];element('pln_page', $value) >= $page;$page++){
                    echo $pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query'])."<br>";
                    $result = $this->extract_html($pln_url['scheme']."://".$pln_url['host'].$pln_url['path'].'?'.$param->replace('page',$page,$pln_url['query']), $proxy, $proxy_userpwd);
                    
                    if($result['code']===200){

                        // 기존 항목을 모두 지운다.
                        
                        

                        $html = new simple_html_dom();
                        $html->load($result['content']);

                        $crawl_info=array();
                        $crawl_img=array();
                        $is_pln_error=false;



                        if(element('post_content', $post))
                            eval(element('post_content', $post));
                        elseif(element('brd_content', $board_crawl))
                            eval(element('brd_content', $board_crawl));
                        



                        
                
                        

                        

                        foreach($crawl_info as $ikey => $ivalue){


                            


                            $updatedata = array(
                                
                                'post_id' => $post_id,
                                'cit_name' => element('crawl_title',$ivalue) ,
                                'cit_summary' => element('crawl_sub_title',$ivalue) ,
                                'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                                'cit_datetime' => cdate('Y-m-d H:i:s'),
                                'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                                'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                                'brd_id' => element('brd_id', $board_crawl),
                                'pln_id' => element('pln_id', $value),
                                'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                                'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                                'cit_status' => 1,
                            );

                            $cit_id = $this->Cmall_item_model->insert($updatedata);

                            

                            

                            
                            $updatedata = array();
                            $updatedata['cit_key'] = 'c_'.$cit_id;
                                    
                            $this->Cmall_item_model->update($cit_id, $updatedata);

                            # 이미지 URL 추출
                            // $imageUrl = $this->http_path_to_url($this->valid_url($board_crawl,$crawl_img[$ikey]['img_src']),element('pln_url', $value));

                            $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url($crawl_img[$ikey]['img_src'],element('pln_url', $value)));


                            
                            # 이미지 파일명 추출
                            
                            $img_src_array= explode('/', $imageUrl);
                            $imageName = end($img_src_array);
                            
                            
                            # 이미지 파일이 맞는 경우
                            if ($fileinfo = getimagesize($imageUrl)) {


                                # 이미지 다운로드
                                $imageFile = $this->extract_html($imageUrl);

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
                                    $uploadfiledata[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
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
                                            $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                            
                                        }
                                    }
                                    $this->Cmall_item_model->update($cit_id, $updatedata);
                                }
                            }

                        }
                        $linkupdate = array(
                            'pln_error' => 0,
                        );
                        if(!$is_pln_error)
                            $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
                    } else {
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
                    $crawl_img=array();
                    $is_pln_error=false;



                    if(element('post_content', $post))
                        eval(element('post_content', $post));
                    elseif(element('brd_content', $board_crawl))
                        eval(element('brd_content', $board_crawl));
                    

                    

                    
            
                    

                    

                    foreach($crawl_info as $ikey => $ivalue){


                        


                        $updatedata = array(
                            
                            'post_id' => $post_id,
                            'cit_name' => element('crawl_title',$ivalue) ,
                            'cit_summary' => element('crawl_sub_title',$ivalue) ,
                            'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                            'cit_datetime' => cdate('Y-m-d H:i:s'),
                            'cit_updated_datetime' => cdate('Y-m-d H:i:s'),
                            'cit_post_url' => $this->valid_url($board_crawl,$this->http_path_to_url(element('crawl_post_url',$ivalue),element('pln_url', $value))),
                            'brd_id' => element('brd_id', $board_crawl),
                            'pln_id' => element('pln_id', $value),
                            'cit_goods_code' => element('crawl_goods_code', $ivalue),                        
                            'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                            'cit_status' => 1,
                        );

                        $cit_id = $this->Cmall_item_model->insert($updatedata);
                        $updatedata = array();
                        $updatedata['cit_key'] = 'c_'.$cit_id;
                                
                        $this->Cmall_item_model->update($cit_id, $updatedata);



                        # 이미지 URL 추출
                        // $imageUrl = $this->http_path_to_url($this->valid_url($board_crawl,$crawl_img[$ikey]['img_src']),element('pln_url', $value));

                        $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url($crawl_img[$ikey]['img_src'],element('pln_url', $value)));


                        
                        # 이미지 파일명 추출
                        
                        $img_src_array= explode('/', $imageUrl);
                        $imageName = end($img_src_array);
                        
                        
                        # 이미지 파일이 맞는 경우
                        if ($fileinfo = getimagesize($imageUrl)) {


                            # 이미지 다운로드
                            $imageFile = $this->extract_html($imageUrl);

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
                                $uploadfiledata[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
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
                                        $this->detect_label($cit_id,config_item('uploads_dir') . '/cmallitem/' . element('cfi_filename', $pval),$ivalue['crawl_title']);
                                        
                                    }
                                }
                                $this->Cmall_item_model->update($cit_id, $updatedata);
                            }
                        }

                    }
                    $linkupdate = array(
                        'pln_error' => 0,
                    );
                    if(!$is_pln_error)
                        $this->Post_link_model->update(element('pln_id',$value),$linkupdate);
                } else {
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

        $category = $this->Board_group_category_model->get_category_info(element('bgr_id', $board), element('post_category', $post));
        if($category)
            $c_category[] = $category['bca_value'];
        if(element('bca_parent', $category)){
            $category = $this->Board_group_category_model->get_category_info(element('bgr_id', $board), element('bca_parent', $category));    
            $c_category[] = $category['bca_value'];
        }
        if(element('bca_parent', $category)){
            $category = $this->Board_group_category_model->get_category_info(element('bgr_id', $board), element('bca_parent', $category));    
            $c_category[] = $category['bca_value'];
        }
        if(element('bca_parent', $category)){
            $category = $this->Board_group_category_model->get_category_info(element('bgr_id', $board), element('bca_parent', $category));    
            $c_category[] = $category['bca_value'];
        }

        
        
        $all_category = $this->Cmall_category_model->get_all_category();
        

        
        
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
            foreach($all_category as $a_cvalue){
                foreach($a_cvalue as $a_cvalue_){
                    if($this->crawl_title_to_category(element('cca_text',$a_cvalue_),element('cit_name',$c_value)))
                        $cmall_category[] = element('cca_id',$a_cvalue_);
                    if(element('cit_summary',$c_value) && $this->crawl_title_to_category(element('cca_text',$a_cvalue_),element('cit_summary',$c_value)))
                        $cmall_category[] = element('cca_id',$a_cvalue_);
                }
                
                
            }

            $this->Cmall_category_rel_model->save_category($cit_id, $cmall_category);
        }

        $cmall_category=array();
        // foreach($all_category as $a_cvalue){
        //     foreach($a_cvalue as $a_cvalue_){
        //     if($this->category_check(element('cca_value',$a_cvalue_),$c_category))
        //         $cmall_category[] = element('cca_id',$a_cvalue_);
        //     }
        // }

        // $this->Cmall_category_rel_model->save_category($cit_id, $cmall_category);
        
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

        
        $all_category=array();
        
        
        $all_category = $this->Cmall_category_model->get_all_category();
        

        
        
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
        );
        

        $crawl = $this->Cmall_item_model
            ->get('', '', $postwhere, '', '', 'pln_id', 'ASC');


        
        require_once FCPATH . 'plugin/simplehtmldom/simple_html_dom.php';

        foreach ($crawl as  $value) {
            
            echo element('cit_post_url', $value)."<br>";
            $result = $this->extract_html(element('cit_post_url', $value), $proxy, $proxy_userpwd);
            
            if($result['code']===200){

                // 기존 항목을 모두 지운다.
                

                $html = new simple_html_dom();
                $html->load($result['content']);

                $cit_info=array();
                $cit_img=array();
                


                // if(element('post_content', $post))
                //     eval(element('post_content', $post));
                // elseif(element('brd_content', $board_crawl))
                //     eval(element('brd_content', $board_crawl));
                

                $html_dom = $html->find('div.-product-detail-right',0);

                if(!$html_dom){
                    log_message('error', '$html_dom post_id:'.$post_id);
                    
                }

                $iteminfo = array();
                $cit_info['crawl_title'] = '';
                $cit_info['crawl_sub_title'] = '';
                $cit_info['crawl_price'] = '';
                $cit_info['crawl_brand'] = '';
                $cit_info['crawl_size'] = '';
                $cit_info['crawl_color'] = '';


                if($html_dom->find('div.-name'))                
                    if($html_dom->find('div.-name',0)->find('h1',0))           
                        $cit_info['crawl_title'] = $html_dom->find('div.-name',0)->find('h1',0)->plaintext;
               
               if($html_dom->find('div.-name'))                
                    if($html_dom->find('div.-name',0)->find('h2',0))           
                        $cit_info['crawl_sub_title'] = $html_dom->find('div.-name',0)->find('h2',0)->plaintext;
                        
                

                if($html_dom->find('td.price'))                
                    if($html_dom->find('td.price',0))           
                        $cit_info['crawl_price'] = $html_dom->find('td.price',0)->plaintext;

                if($html_dom->find('td.sale'))                
                    if($html_dom->find('td.sale',0))           
                        $cit_info['crawl_price'] = $html_dom->find('td.sale',0)->plaintext;

                if($html_dom->find('td.prd_brand'))                
                    if($html_dom->find('td.prd_brand',0))           
                        $cit_info['crawl_brand'] = $html_dom->find('td.prd_brand',0)->plaintext;
                        
                
                    
               
                print_r($cit_info);
                exit;
                
                $html_dom = $html->find('div.cont',0);

                if(!$html_dom){
                    log_message('error', '$html_dom post_id:'.$post_id);
                    
                }

                $i=0;



                if($html_dom->find('img')){
                    foreach($html_dom->find('img') as $gallery) {
                        $iteminfo = array();

                        
                        
                        $cit_info[$i]['img_src'] = '';
                        

                        $itemimg = array();


                        if($gallery->{'ec-data-src'})                        
                            $itemimg['img_src'] = $gallery->{'ec-data-src'};
                        
                        if(!empty($itemimg['img_src'])) {
                            $cit_img[$i]['img_src'] = $itemimg['img_src'];
                        } else {
                            log_message('error', '$img_src post_id:'.$post_id);
                            
                        }
                        
                        

                        $i++;
                    }
                    
                } else {
                    log_message('error', '$html_dom post_id:'.$post_id);
                    
                }
                

                $updatedata = array(                                        
                    
                    'cit_name' => element('crawl_title',$ivalue) ,
                    'cit_summary' => element('crawl_sub_title',$ivalue) ,
                    'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                    'cit_price' => preg_replace("/[^0-9]*/s", "", element('crawl_price',$ivalue)) ,
                    'cit_is_soldout' => element('crawl_is_soldout', $ivalue),
                    
                );

                

                $this->Cmall_item_model->update(element('cit_id',$c_value),$updatedata);

                foreach($cit_img as $ikey => $ivalue){


                    


                    



                    # 이미지 URL 추출
                   

                    $imageUrl = $this->valid_url($board_crawl,$this->http_path_to_url($cit_img[$ikey]['img_src'],element('cit_post_url', $value)));


                    
                    # 이미지 파일명 추출
                    
                    $img_src_array= explode('/', $imageUrl);
                    $imageName = end($img_src_array);
                    
                    echo $imageUrl."<br>";

                    # 이미지 파일이 맞는 경우
                    if ($fileinfo = getimagesize($imageUrl)) {

                        # 이미지 다운로드
                        $imageFile = $this->extract_html($imageUrl);

                        # 파일 생성 후 저장
                        $filetemp = fopen($imageName, 'w');
                        fwrite($filetemp, $imageFile['content']);
                        fclose($filetemp); // Closing file handle

                        $this->detect_tag(element('cit_id',$value),$imageName);

                         @unlink($imageName);
                        
                    }
                }
                exit;
            } else {
                continue;
            }
           
        }


    }


    function valid_url($board_crawl = array() , $crawl_url=''){



        $b = parse_url(trim(element('brd_url',$board_crawl)));            
        $c = trim($crawl_url);
        
        

        if(strpos($c,$b['host']) === false ){
            if($this->form_validation->valid_url($c))
                return $c;
            else 
                return element('brd_url',$board_crawl).$c;
        } else {
             return $b['scheme']."://".strstr($c,$b['host']) ;
        }
    }


    function detect_label($cit_id=0,$path='',$crawl_title,$translate=0)
    {

        
        

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


    function detect_tag($cit_id=0,$path='',$crawl_title='',$translate=0)
    {

        

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
        
       
        $target = 'ko';

        if ($texts) {
            
            foreach ($texts as $text) {
print_r($text->getDescription());
                if($translate){
                    $translation = $this->translate->translate($text->getDescription(), [
                        'target' => $target
                    ]);
                    
                    array_push($translate_text,$translation['text']);
                
                } else {

                    array_push($translate_text,$text->getDescription());
                }        
            }
            
                
           // print_r($translate_text);
        } else {
            return 'No label found';
        }
        
        // if(count($translate_text)){
            

        //     $convert_text = $this->label_tag_convert(element('cit_id', $citem),$translate_text,$crawl_title);
        //     // $deletewhere = array(
        //     //     'cit_id' => element('cit_id', $citem),
        //     // );
        //     // $this->Crawl_tag_model->delete_where($deletewhere);            
        //     if ($convert_text && is_array($convert_text)) {
        //         foreach ($convert_text as $key => $value) {
        //             $value = trim($value);
        //             if ($value) {
        //                 $tagdata = array(
        //                     'post_id' => element('post_id', $citem),
        //                     'cit_id' => element('cit_id', $citem),
        //                     'brd_id' => element('brd_id', $citem),
        //                     'cta_tag' => $value,
        //                 );
        //                 $this->Crawl_tag_model->insert($tagdata);
        //             }
        //         }
        //     }
            
        // }
        

        $this->imageAnnotator->close();
        
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

    function crawl_title_to_category($value,$string)
    {   
        $v_arr = explode(',',$value);

        foreach($v_arr as $v){            
            if(in_array($v,$string))
                return true;
        }
        

    }

    
}
