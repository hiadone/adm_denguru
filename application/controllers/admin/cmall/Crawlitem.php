<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Crawlitem class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>컨텐츠몰관리>상품관리 controller 입니다.
 */
class Crawlitem extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'cmall/crawlitem';

    protected $db2;
    protected $or_where =array();
    protected $or_like =array();
    protected $where =array();

    protected $allow_search_field =array();

    protected $search_field_equal =array();

    protected $allow_order_field =array();

    

   

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Board');

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'cmall', 'dhtml_editor');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('pagination', 'querystring', 'cmalllib'));


        $this->db2 = $this->load->database('db2', TRUE);
    }

    /**
     * 목록을 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_crawlitem_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $this->load->model(array('Board_model'));
        
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $view['view']['sort'] = array(
            'crw_id' => $param->sort('crawl_item.crw_id', 'asc'),
            'crw_price_sale' => $param->sort('crw_price_sale', 'asc'),
            'crw_name' => $param->sort('crw_name', 'asc'),
            'crw_datetime' => $param->sort('crw_datetime', 'asc'),
            'crw_updated_datetime' => $param->sort('crw_updated_datetime', 'asc'),            
            'crw_price' => $param->sort('crw_price', 'asc'),
            'crw_category1' => $param->sort('crw_category1', 'asc'),
            'crw_brand1' => $param->sort('crw_brand1', 'asc'),

        );
        $findex = $this->input->get('findex') ? $this->input->get('findex') : 'crawl_item.crw_id';
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

         $per_page = admin_listnum();

        if($this->input->get('warning')){
            
            
            $this->where['crawl_item.brd_id']=0;
        }

        if($this->input->get('sfield') === 'brd_id'){

            
            $this->db->like('brd_name',$this->input->get('skeyword'));
            $res = $this->Board_model->get('','brd_id');
            
            if($res){
                $brd_id_arr=array();
                foreach ($res as $key => $value) {
                    $brd_id_arr[] = element('brd_id',$value);
                }

                $this->where['crawl_item.brd_id'] =  $brd_id_arr[0];


                // $this->db2->group_end();
            }
            // $this->where['crawl_item.brd_id'] =  $this->input->get('skeyword');

        }elseif($this->input->get('sfield') === 'brd_id2'){

            
           
           $this->where['crawl_item.brd_id'] =  $this->input->get('skeyword');

        }  elseif($this->input->get('sfield') === 'crw_category'){
            
            $this->or_like['crw_category1'] = rawurlencode($this->input->get('skeyword'));
            $this->or_like['crw_category2'] = rawurlencode($this->input->get('skeyword'));
            $this->or_like['crw_category3'] = rawurlencode($this->input->get('skeyword'));
            
            
            
            
        } elseif($this->input->get('sfield') === 'crw_brand'){

            
            
            $this->or_like['crw_brand1'] = $this->input->get('skeyword');
            $this->or_like['crw_brand2'] = $this->input->get('skeyword');
            $this->or_like['crw_brand3'] = $this->input->get('skeyword');
            $this->or_like['crw_brand4'] = $this->input->get('skeyword');
            $this->or_like['crw_brand5'] = $this->input->get('skeyword');

            
        } 

        if($this->input->get('crw_goods_code')){

            
            
            $this->where['crw_goods_code'] =  $this->input->get('crw_goods_code');

            
        }

        
        if(!empty($this->input->get('warning'))){
            $or_where = array(
                'crw_name' => '',
                'crw_price' => 0,
                'crw_post_url' => '',
                'crw_goods_code' => '',                
                'crw_category1' => '',
            );
            
            $this->or_where  = $or_where;

            
        } 
        $this->allow_search_field = array('crw_goods_code', 'crw_price','crw_name'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->search_field_equal = array(); 
        $this->allow_order_field = array('crawl_item.crw_id','crawl_item.brd_id','crw_goods_code', 'crw_price_sale', 'crw_name', 'crw_price'); // 정렬이 가능한 필드



        
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        
        $result = $this->get_admin_list($per_page, $offset, '', '', $findex, $forder, $sfield, $skeyword);

        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                
                // $this->db2->select($select);
                
                if(!$this->input->get('warning')){
                    $this->db2->from('crawl_detail');
                    
                    $this->db2->where(array('crw_id' => element('crw_id', $val)));
                    
                    // if (is_numeric($limit) && is_numeric($offset)) {
                    //     $this->db->limit($limit, $offset);
                    // }
                    $aaa = $this->db2->get();
                    $result2 = $aaa->row_array();
                    
                    $result['list'][$key]['cdt_brand1'] = element('cdt_brand1', $result2);
                    $result['list'][$key]['cdt_brand2'] = element('cdt_brand2', $result2);
                    $result['list'][$key]['cdt_brand3'] = element('cdt_brand3', $result2);
                    $result['list'][$key]['cdt_brand4'] = element('cdt_brand4', $result2);
                    $result['list'][$key]['cdt_brand5'] = element('cdt_brand5', $result2);

                    $result['list'][$key]['cdt_file_1'] = element('cdt_file_1', $result2);
                    $result['list'][$key]['cdt_content'] = cut_str(element('cdt_content', $result2),100);
                }
                if(empty(element('crw_name', $val)) || (empty(element('crw_price', $val)) && empty(element('crw_price_sale', $val)) && empty(element('crw_is_soldout', $val))  ) || empty(element('crw_post_url', $val)) || (empty(element('crw_brand1', $val)) && empty(element('crw_brand1', $val)) && empty(element('crw_brand1', $val)) && empty(element('crw_brand2', $val)) && empty(element('crw_brand3', $val)) && empty(element('crw_brand4', $val)) && empty(element('crw_brand5', $val)) && empty(element('cdt_brand1', $val)) && empty(element('cdt_brand2', $val)) && empty(element('cdt_brand3', $val)) && empty(element('cdt_brand4', $val)) && empty(element('cdt_brand5', $val)) ) || (empty(element('crw_category1', $val)) && empty(element('crw_category2', $val)) && empty(element('crw_category3', $val)) ))
                    $result['list'][$key]['warning'] = 1 ; 
                else 
                    $result['list'][$key]['warning'] = '' ; 

                // if($this->input->get('warning')){
                //     if($result['list'][$key]['warning']) $warning++;
                // }
                
                
                $result['list'][$key]['brd_name'] = $this->board->item_id('brd_name',element('brd_id', $val));

                
                

                $result['list'][$key]['num'] = $list_num--;
            }
        }
        
        
        $view['view']['data'] = $result;

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = 'crw_id';

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = admin_url($this->pagedir) . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $search_option = array('brd_id' => '스토어 명','crw_goods_code' => '상품코드', 'crw_name' => '상품명', 'crw_price' => '판매가격', 'crw_category' => '카테고리명', 'crw_brand' => '브랜드명');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = admin_url($this->pagedir);
        $view['view']['warning_url'] = admin_url($this->pagedir.'?warning=1&'.$param->output());
        $view['view']['list_delete_url'] = admin_url($this->pagedir . '/listdelete/?' . $param->output());
        $view['view']['list_update_url'] = admin_url($this->pagedir . '/listupdate/?' . $param->output());

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
     * 게시판 글쓰기 또는 수정 페이지를 가져오는 메소드입니다
     */
    public function write($pid = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_crawlitem_write';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        if ($pid) {
            $pid = (int) $pid;
            if (empty($pid) OR $pid < 1) {
                show_404();
            }
        }
        $primary_key = $this->{$this->modelname}->primary_key;

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
        $getdata = array();
        if ($pid) {
            $getdata = $this->{$this->modelname}->get_one($pid);
            $cmall_item_meta = $this->Cmall_item_meta_model->get_all_meta(element('crw_id', $getdata));
            if (is_array($cmall_item_meta)) {
                $getdata = array_merge($getdata, $cmall_item_meta);
            }
            $cat = $this->Cmall_category_model->get_category(element('crw_id', $getdata));
            if ($cat) {
                foreach ($cat as $ck => $cv) {
                    $getdata['category'][] = $cv['cca_id'];
                }
            }
            $cattr = $this->Cmall_attr_model->get_attr(element('crw_id', $getdata));
            if ($cattr) {
                foreach ($cattr as $ctk => $ctv) {
                    $getdata['attr'][] = $ctv['cat_id'];
                }
            }
        } else {
            // 기본값 설정
            $getdata['crw_key'] = time();
            $getdata['crw_status'] = '1';
        }

        $getdata['cta_tag'] = '';
        $crawlwhere = array(
            'crw_id' => element('crw_id', $getdata),
        );
        $tag = $this->Crawl_tag_model->get('', '', $crawlwhere, '', '', 'cta_id', 'ASC');
        if ($tag && is_array($tag)) {
            $tag_array=array();
            foreach ($tag as $tvalue) {
                if (element('cta_tag', $tvalue)) {
                    array_push($tag_array,trim(element('cta_tag', $tvalue)));
                }
            }
            $getdata['cta_tag'] = implode("\n",$tag_array);
        }
        
        $getdata['val_tag'] = '';
        $crawlwhere = array(
            'crw_id' => element('crw_id', $getdata),
        );
        $tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
        if ($tag && is_array($tag)) {
            $tag_array=array();
            foreach ($tag as $tvalue) {
                if (element('val_tag', $tvalue)) {
                    array_push($tag_array,trim(element('val_tag', $tvalue)));
                }
            }
            $getdata['val_tag'] = implode("\n",$tag_array);
        }
        
        $getdata['boardlist'] = $this->Board_model->get_board_list();

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'is_submit',
                'label' => '전송',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_name',
                'label' => '상품명',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'crw_brand_text',
                'label' => '브랜드',
                'rules' => 'trim',
            ),
            array(
                'field' => 'crw_order',
                'label' => '상품정렬순서',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'crw_type1',
                'label' => '추천',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_type2',
                'label' => '인기',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_type3',
                'label' => '신상품',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_type4',
                'label' => '할인',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_status',
                'label' => '출력여부',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_is_soldout',
                'label' => 'Sold out',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'crw_summary',
                'label' => '기본설명',
                'rules' => 'trim',
            ),
            array(
                'field' => 'crw_content',
                'label' => '상품내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'crw_mobile_content',
                'label' => '모바일상품내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'crw_price',
                'label' => '상품가격',
                'rules' => 'trim|required|numeric|is_natural',
            ),
            array(
                'field' => 'crw_price_sale',
                'label' => '할인가격',
                'rules' => 'trim|required|numeric|is_natural',
            ),
            array(
                'field' => 'crw_download_days',
                'label' => '다운로드기간제한',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_1',
                'label' => '기본정보1제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_1',
                'label' => '기본정보1내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_2',
                'label' => '기본정보2제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_2',
                'label' => '기본정보2내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_3',
                'label' => '기본정보3제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_3',
                'label' => '기본정보3내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_4',
                'label' => '기본정보4제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_4',
                'label' => '기본정보4내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_5',
                'label' => '기본정보5제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_5',
                'label' => '기본정보5내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_6',
                'label' => '기본정보6제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_6',
                'label' => '기본정보6내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_7',
                'label' => '기본정보7제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_7',
                'label' => '기본정보7내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_8',
                'label' => '기본정보8제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_8',
                'label' => '기본정보8내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_9',
                'label' => '기본정보9제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_9',
                'label' => '기본정보9내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_title_10',
                'label' => '기본정보10제목',
                'rules' => 'trim',
            ),
            array(
                'field' => 'info_content_10',
                'label' => '기본정보10내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'item_layout',
                'label' => '레이아웃',
                'rules' => 'trim',
            ),
            array(
                'field' => 'item_mobile_layout',
                'label' => '모바일레이아웃',
                'rules' => 'trim',
            ),
            array(
                'field' => 'item_sidebar',
                'label' => '사이드바',
                'rules' => 'trim',
            ),
            array(
                'field' => 'item_mobile_sidebar',
                'label' => '모바일사이드바',
                'rules' => 'trim',
            ),
            array(
                'field' => 'item_skin',
                'label' => '스킨',
                'rules' => 'trim',
            ),
            array(
                'field' => 'item_mobile_skin',
                'label' => '모바일스킨',
                'rules' => 'trim',
            ),
            array(
                'field' => 'header_content',
                'label' => '상단내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'footer_content',
                'label' => '하단내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'mobile_header_content',
                'label' => '모바일상단내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'mobile_footer_content',
                'label' => '모바일하다내용',
                'rules' => 'trim',
            ),
            array(
                'field' => 'demo_user_link',
                'label' => '사용자데모',
                'rules' => 'trim',
            ),
            array(
                'field' => 'demo_admin_link',
                'label' => '관리자데모',
                'rules' => 'trim',
            ),
            array(
                'field' => 'crw_post_url',
                'label' => '실제상품페이지주소',
                'rules' => 'trim',
            ),
        );
        if ($this->input->post($primary_key)) {
            $config[] = array(
                'field' => 'crw_key',
                'label' => '페이지주소',
                'rules' => 'trim|required|alpha_dash|min_length[3]|max_length[50]|is_unique[cmall_item.crw_key.crw_id.' . $getdata['crw_id'] . ']',
            );
        } else {
            $config[] = array(
                'field' => 'crw_key',
                'label' => '페이지주소',
                'rules' => 'trim|required|alpha_dash|min_length[3]|max_length[50]|is_unique[cmall_item.crw_key]',
            );
        }
        $this->form_validation->set_rules($config);

        $form_validation = $this->form_validation->run();
        $file_error = '';

        if ($form_validation) {
            $this->load->library('upload');
            $this->load->library('aws_s3');
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
                    $uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif';
                    $uploadconfig['max_size'] = '5000';
                    $uploadconfig['encrypt_name'] = true;

                    $this->upload->initialize($uploadconfig);

                    if ($this->upload->do_upload('crw_file_' . $k)) {
                        $img = $this->upload->data();
                        $crw_file[$k] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);

                        $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);
                    } else {
                        $file_error = $this->upload->display_errors();
                        break;

                    }
                }
            }
        }

        $uploadfiledata = array();
        $uploadfiledata2 = array();

        if ($form_validation && $file_error === '') {
            $this->load->library('upload');
            if (isset($_FILES) && isset($_FILES['cde_file']) && isset($_FILES['cde_file']['name']) && is_array($_FILES['cde_file']['name'])) {
                $filecount = count($_FILES['cde_file']['name']);
                $upload_path = config_item('uploads_dir') . '/crawlitemdetail/';
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

                foreach ($_FILES['cde_file']['name'] as $i => $value) {
                    if ($value) {
                        $uploadconfig = array();
                        $uploadconfig['upload_path'] = $upload_path;
                        $uploadconfig['allowed_types'] = '*';
                        $uploadconfig['encrypt_name'] = true;

                        $this->upload->initialize($uploadconfig);
                        $_FILES['userfile']['name'] = $_FILES['cde_file']['name'][$i];
                        $_FILES['userfile']['type'] = $_FILES['cde_file']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['cde_file']['tmp_name'][$i];
                        $_FILES['userfile']['error'] = $_FILES['cde_file']['error'][$i];
                        $_FILES['userfile']['size'] = $_FILES['cde_file']['size'][$i];
                        if ($this->upload->do_upload()) {
                            $filedata = $this->upload->data();

                            $uploadfiledata[$i]['cde_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
                            $uploadfiledata[$i]['cde_originname'] = element('orig_name', $filedata);
                            $uploadfiledata[$i]['cde_filesize'] = intval(element('file_size', $filedata) * 1024);
                            $uploadfiledata[$i]['cde_type'] = str_replace('.', '', element('file_ext', $filedata));
                            $uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;
                            $cde_title = $this->input->post('cde_title');
                            $uploadfiledata[$i]['cde_title'] = element($i, $cde_title);
                            $cde_price = $this->input->post('cde_price');
                            $uploadfiledata[$i]['cde_price'] = element($i, $cde_price) ? element($i, $cde_price) : 0;
                            $cde_status = $this->input->post('cde_status');
                            $uploadfiledata[$i]['cde_status'] = element($i, $cde_status) ? element($i, $cde_status) : 0;

                        } else {
                            $file_error = $this->upload->display_errors();
                            break;
                        }
                    }
                }
            }
            if (isset($_FILES) && isset($_FILES['cde_file_update']) && isset($_FILES['cde_file_update']['name']) && is_array($_FILES['cde_file_update']['name']) && $file_error === '') {
                $filecount = count($_FILES['cde_file_update']['name']);
                $upload_path = config_item('uploads_dir') . '/crawlitemdetail/';
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

                foreach ($_FILES['cde_file_update']['name'] as $i => $value) {
                    if ($value) {
                        $uploadconfig = array();
                        $uploadconfig['upload_path'] = $upload_path;
                        $uploadconfig['allowed_types'] = '*';
                        $uploadconfig['encrypt_name'] = true;
                        $this->upload->initialize($uploadconfig);
                        $_FILES['userfile']['name'] = $_FILES['cde_file_update']['name'][$i];
                        $_FILES['userfile']['type'] = $_FILES['cde_file_update']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['cde_file_update']['tmp_name'][$i];
                        $_FILES['userfile']['error'] = $_FILES['cde_file_update']['error'][$i];
                        $_FILES['userfile']['size'] = $_FILES['cde_file_update']['size'][$i];
                        if ($this->upload->do_upload()) {
                            $filedata = $this->upload->data();

                            $uploadfiledata2[$i]['cde_id'] = $i;
                            $uploadfiledata2[$i]['cde_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
                            $uploadfiledata2[$i]['cde_originname'] = element('orig_name', $filedata);
                            $uploadfiledata2[$i]['cde_filesize'] = intval(element('file_size', $filedata) * 1024);
                            $uploadfiledata2[$i]['cde_type'] = str_replace('.', '', element('file_ext', $filedata));
                            $uploadfiledata2[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;
                        } else {
                            $file_error = $this->upload->display_errors();
                            break;
                        }
                    }
                }
            }
        }


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($form_validation === false OR $file_error !== '') {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

            if ($file_error) {
                $view['view']['message'] = $file_error;
            }

            $getdata = array();
            $brand_text = '';
            $getdata['crw_status'] = '1';
            if ($pid) {
                $getdata = $this->{$this->modelname}->get_one($pid);
                $cmall_item_meta = $this->Cmall_item_meta_model->get_all_meta(element('crw_id', $getdata));
                if (is_array($cmall_item_meta)) {
                    $getdata = array_merge($getdata, $cmall_item_meta);
                }
                $cat = $this->Cmall_category_model->get_category(element('crw_id', $getdata));
                if ($cat) {
                    foreach ($cat as $ck => $cv) {
                        $getdata['category'][] = $cv['cca_id'];
                    }
                }
                $cattr = $this->Cmall_attr_model->get_attr(element('crw_id', $getdata));
                if ($cattr) {
                    foreach ($cattr as $ctk => $ctv) {
                        $getdata['attr'][] = $ctv['cat_id'];
                    }
                }
                $where = array(
                    'crw_id' => element('crw_id', $getdata),
                );
                $getdata['item_detail'] = $this->Cmall_item_detail_model->get('', '', $where, '', '', 'cde_id', 'ASC');

                $getdata['cta_tag'] = '';
                $crawlwhere = array(
                    'crw_id' => $pid,
                );
                $tag = $this->Crawl_tag_model->get('', '', $crawlwhere, '', '', 'cta_id', 'ASC');
                if ($tag && is_array($tag)) {
                    $tag_array=array();
                    foreach ($tag as $tvalue) {
                        if (element('cta_tag', $tvalue)) {
                            array_push($tag_array,trim(element('cta_tag', $tvalue)));
                        }
                    }
                    $getdata['cta_tag'] = implode("\n",$tag_array);
                }
                
                $getdata['val_tag'] = '';
                $crawlwhere = array(
                    'crw_id' => $pid,
                );
                $tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
                if ($tag && is_array($tag)) {
                    $tag_array=array();
                    foreach ($tag as $tvalue) {
                        if (element('val_tag', $tvalue)) {
                            array_push($tag_array,trim(element('val_tag', $tvalue)));
                        }
                    }
                    $getdata['val_tag'] = implode("\n",$tag_array);
                }

                
                $getdata['postlist'] = $this->Post_model->get_post_list('','',array('brd_id' => element('brd_id',$getdata)));
            }
            if(element('crw_brand', $getdata))
                $brand_text = $this->Cmall_brand_model->get_one(element('crw_brand', $getdata));

            if(element('cbr_value_kr',$brand_text))
                $getdata['crw_brand_text']  = element('cbr_value_kr',$brand_text);
            elseif(element('cbr_value_kr',$brand_text))
                $getdata['crw_brand_text']  = element('cbr_value_en',$brand_text);
            else
                $getdata['crw_brand_text']  = '';

            $getdata['boardlist'] = $this->Board_model->get_board_list();

            $getdata['brand_list'] = $this->Cmall_brand_model->get();
            $view['view']['data'] = $getdata;
            $view['view']['data']['item_layout_option'] = get_skin_name(
                '_layout',
                set_value('item_layout', element('item_layout', $getdata)),
                '기본설정따름'
            );
            $view['view']['data']['item_mobile_layout_option'] = get_skin_name(
                '_layout',
                set_value('item_mobile_layout', element('item_mobile_layout', $getdata)),
                '기본설정따름'
            );
            $view['view']['data']['item_skin_option'] = get_skin_name(
                'cmall',
                set_value('item_skin', element('item_skin', $getdata)),
                '기본설정따름'
            );
            $view['view']['data']['item_mobile_skin_option'] = get_skin_name(
                'cmall',
                set_value('item_mobile_skin', element('item_mobile_skin', $getdata)),
                '기본설정따름'
            );
            $view['view']['data']['all_category'] = $this->Cmall_category_model->get_all_category();
            $view['view']['data']['all_attr'] = $this->Cmall_attr_model->get_all_attr();

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

            /**
             * 어드민 레이아웃을 정의합니다
             */
            $layoutconfig = array('layout' => 'layout', 'skin' => 'write');
            $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            
            
            if($this->input->post('crw_brand_text',null,'')){
                $this->db2->select('cbr_id');            
                $this->db2->from('cmall_brand');
                $this->db2->where('cbr_value_kr', $this->input->post('crw_brand_text',null,''));
                $this->db2->or_where('cbr_value_en', $this->input->post('crw_brand_text',null,''));
                $result = $this->db2->get();
                $crw_brand = $result->row_array();
            }

            $crw_order = $this->input->post('crw_order') ? $this->input->post('crw_order') : 0;
            $crw_brand = empty($crw_brand) ? 0 : $crw_brand;
            $crw_type1 = $this->input->post('crw_type1') ? $this->input->post('crw_type1') : 0;
            $crw_type2 = $this->input->post('crw_type2') ? $this->input->post('crw_type2') : 0;
            $crw_type3 = $this->input->post('crw_type3') ? $this->input->post('crw_type3') : 0;
            $crw_type4 = $this->input->post('crw_type4') ? $this->input->post('crw_type4') : 0;
            $crw_status = $this->input->post('crw_status') ? 1 : 0;
            $crw_is_soldout = $this->input->post('crw_is_soldout') ? 1 : 0;
            $content_type = $this->cbconfig->item('use_cmall_product_dhtml') ? 1 : 0;
            $crw_price = $this->input->post('crw_price') ? $this->input->post('crw_price') : 0;
            $crw_price_sale = $this->input->post('crw_price_sale') ? $this->input->post('crw_price_sale') : 0;
            $crw_download_days = $this->input->post('crw_download_days') ? $this->input->post('crw_download_days') : 0;
            $cta_tag = $this->input->post('cta_tag') ? $this->input->post('cta_tag') : '';


            $updatedata = array(
                'crw_key' => $this->input->post('crw_key', null, ''),
                'crw_name' => $this->input->post('crw_name', null, ''),
                'crw_brand' => element('cbr_id',$crw_brand),
                'crw_order' => $crw_order,
                'crw_type1' => $crw_type1,
                'crw_type2' => $crw_type2,
                'crw_type3' => $crw_type3,
                'crw_type4' => $crw_type4,
                'crw_status' => $crw_status,
                'crw_is_soldout' => $crw_is_soldout,
                'crw_summary' => $this->input->post('crw_summary', null, ''),
                'crw_content' => $this->input->post('crw_content', null, ''),
                'crw_mobile_content' => $this->input->post('crw_mobile_content', null, ''),
                'crw_content_html_type' => $content_type,
                'crw_price' => $crw_price,
                'crw_price_sale' => $crw_price_sale,
                'crw_updated_datetime' => cdate('Y-m-d H:i:s'),
                'crw_download_days' => $crw_download_days,
                'post_id' => $this->input->post('post_id', null, ''),
                'brd_id' => $this->input->post('brd_id', null, ''),
                'crw_color' => $this->input->post('crw_color', null, ''),
            );

            for ($k = 1; $k <= 10; $k++) {
                if ($this->input->post('crw_file_' . $k . '_del')) {
                    $updatedata['crw_file_' . $k] = '';

                    @unlink(config_item('uploads_dir') . '/crawlitem/' . $getdata['crw_file_' . $k]);
                    $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/crawlitem/' . $getdata['crw_file_' . $k]);
                } elseif (isset($crw_file[$k]) && $crw_file[$k]) {
                    $updatedata['crw_file_' . $k] = $crw_file[$k];
                }
            }

            $array = array(
                'info_title_1', 'info_content_1', 'info_title_2', 'info_content_2', 'info_title_3',
                'info_content_3', 'info_title_4', 'info_content_4', 'info_title_5', 'info_content_5',
                'info_title_6', 'info_content_6', 'info_title_7', 'info_content_7', 'info_title_8',
                'info_content_8', 'info_title_9', 'info_content_9', 'info_title_10', 'info_content_10',
                'item_layout', 'item_mobile_layout', 'item_sidebar', 'item_mobile_sidebar', 'item_skin',
                'item_mobile_skin', 'header_content', 'footer_content', 'mobile_header_content',
                'mobile_footer_content', 'demo_user_link', 'demo_admin_link', 'seller_mem_userid', 'crw_post_url'
            );

            $metadata = array();
            foreach ($array as $value) {
                $metadata[$value] = $this->input->post($value, null, '');
            }
            $metadata['updated_mem_id'] = $this->member->item('mem_id');
            $metadata['updated_ip_address'] = $this->input->ip_address();
            $metadata['seller_mem_id'] = '';
            if ($this->input->post('seller_mem_userid')) {
                $mem = $this->Member_model->get_by_userid($this->input->post('seller_mem_userid'), 'mem_id');
                $metadata['seller_mem_id'] = element('mem_id', $mem);
            }


            /**
             * 게시물을 수정하는 경우입니다
             */
            $cmall_category = $this->input->post('cmall_category', null, '');
            $cmall_attr = $this->input->post('cmall_attr', null, '');

            if ($this->input->post($primary_key)) {
                $this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);
                $this->Cmall_item_meta_model->save($pid, $metadata);
                $this->Cmall_category_rel_model->save_category($this->input->post($primary_key), $cmall_category);
                $this->Cmall_attr_rel_model->save_attr($this->input->post($primary_key), $cmall_attr);

                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['crw_datetime'] = cdate('Y-m-d H:i:s');
                $updatedata['mem_id'] = $this->member->item('mem_id');
                $pid = $this->{$this->modelname}->insert($updatedata);

                $metadata['ip_address'] = $this->input->ip_address();

                $this->Cmall_item_meta_model->save($pid, $metadata);
                $this->Cmall_category_rel_model->save_category($pid, $cmall_category);
                $this->Cmall_attr_rel_model->save_attr($pid, $cmall_attr);

                $this->session->set_flashdata(
                    'message',
                    '정상적으로 입력되었습니다'
                );
            }

            $cta_tag_text=array();
            
            $cta_tag_text = explode("\n",urldecode($cta_tag));

            if(count($cta_tag_text)){
                $deletewhere = array(
                    'crw_id' => $pid,
                );
                $this->Crawl_tag_model->delete_where($deletewhere);            
                if ($cta_tag_text && is_array($cta_tag_text)) {
                    foreach ($cta_tag_text as $key => $value) {
                        $value = trim($value);
                        if ($value) {
                            $tagdata = array(
                                'post_id' => $this->input->post('post_id', null, ''),
                                'crw_id' => $pid,
                                'brd_id' => $this->input->post('brd_id', null, ''),
                                'cta_tag' => $value,
                            );
                            $this->Crawl_tag_model->insert($tagdata);
                        }
                    }
                }
                
            }

            $this->load->model('Cmall_item_history_model');
            $historydata = array(
                'crw_id' => $pid,
                'mem_id' => $this->member->item('mem_id'),
                'chi_title' => $this->input->post('crw_name', null, ''),
                'chi_content' => $this->input->post('crw_content', null, ''),
                'chi_content_html_type' => $content_type,
                'chi_ip' => $this->input->ip_address(),
                'chi_datetime' => cdate('Y-m-d H:i:s'),

            );
            $this->Cmall_item_history_model->insert($historydata);

            $file_updated = false;
            $file_changed = false;
            if ($uploadfiledata && is_array($uploadfiledata) && count($uploadfiledata) > 0) {
                foreach ($uploadfiledata as $pkey => $pval) {
                    if ($pval) {
                        $cde_price = element('cde_price', $pval) ? element('cde_price', $pval) : 0;
                        $cde_is_image = element('is_image', $pval) ? 1 : 0;
                        $cde_status = element('cde_status', $pval) ? element('cde_status', $pval) : 0;
                        $fileupdate = array(
                            'crw_id' => $pid,
                            'mem_id' => $this->member->item('mem_id'),
                            'cde_title' => element('cde_title', $pval),
                            'cde_price' => $cde_price,
                            'cde_originname' => element('cde_originname', $pval),
                            'cde_filename' => element('cde_filename', $pval),
                            'cde_filesize' => element('cde_filesize', $pval),
                            'cde_type' => element('cde_type', $pval),
                            'cde_is_image' => $cde_is_image,
                            'cde_datetime' => cdate('Y-m-d H:i:s'),
                            'cde_ip' => $this->input->ip_address(),
                            'cde_status' => $cde_status,
                        );
                        $file_id = $this->Cmall_item_detail_model->insert($fileupdate);
                    }
                }
            }
            if ($uploadfiledata2 && is_array($uploadfiledata2) && count($uploadfiledata2) > 0) {
                foreach ($uploadfiledata2 as $pkey => $pval) {
                    if ($pval) {
                        $cde_is_image = element('is_image', $pval) ? 1 : 0;
                        $fileupdate = array(
                            'mem_id' => $this->member->item('mem_id'),
                            'cde_originname' => element('cde_originname', $pval),
                            'cde_filename' => element('cde_filename', $pval),
                            'cde_filesize' => element('cde_filesize', $pval),
                            'cde_type' => element('cde_type', $pval),
                            'cde_is_image' => $cde_is_image,
                            'cde_datetime' => cdate('Y-m-d H:i:s'),
                            'cde_ip' => $this->input->ip_address(),
                        );
                        $this->Cmall_item_detail_model->update($pkey, $fileupdate);
                    }
                }
            }

            if ($this->input->post('cde_title_update')) {
                foreach ($this->input->post('cde_title_update') as $pkey => $pval) {
                    $cde_price = element($pkey, $this->input->post('cde_price_update')) ? element($pkey, $this->input->post('cde_price_update')) : 0;
                    $cde_status = element($pkey, $this->input->post('cde_status_update')) ? 1 : 0;
                    $update = array(
                        'cde_title' => element($pkey, $this->input->post('cde_title_update')),
                        'cde_price' => $cde_price,
                        'cde_status' => $cde_status,
                    );
                    $this->Cmall_item_detail_model->update($pkey, $update);
                }
            }

            // 이벤트가 존재하면 실행합니다
            Events::trigger('after', $eventname);

            if ($this->input->post($primary_key)) {
                redirect(current_url(), 'refresh');
            } else {
                $param =& $this->querystring;
                $redirecturl = admin_url($this->pagedir . '?' . $param->output());
                redirect($redirecturl);
            }
        }
    }

    /**
     * 목록 페이지에서 선택수정을 하는 경우 실행되는 메소드입니다
     */
    public function listupdate()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_crawlitem_listupdate';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 체크한 게시물의 업데이트를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {

            $crw_name = $this->input->post('crw_name');
            $crw_price = $this->input->post('crw_price');
            $crw_price_sale = $this->input->post('crw_price_sale');
            $crw_status = $this->input->post('crw_status');

            $item_layout = $this->input->post('item_layout');
            $item_mobile_layout = $this->input->post('item_mobile_layout');
            $item_sidebar = $this->input->post('item_sidebar');
            $item_mobile_sidebar = $this->input->post('item_mobile_sidebar');
            $item_skin = $this->input->post('item_skin');
            $item_mobile_skin = $this->input->post('item_mobile_skin');

            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $crw_price_update = element($val, $crw_price) ? element($val, $crw_price) : 0;
                    $crw_price_sale_update = element($val, $crw_price_sale) ? element($val, $crw_price_sale) : 0;
                    $crw_status_update = element($val, $crw_status) ? 1 : 0;
                    $updatedata = array(
                        'crw_name' => element($val, $crw_name),
                        'crw_price' => $crw_price_update,
                        'crw_price_sale' => $crw_price_sale_update,
                        'crw_status' => $crw_status_update,
                    );
                    $metadata = array(
                        'item_layout' => element($val, $item_layout),
                        'item_mobile_layout' => element($val, $item_mobile_layout),
                        'item_sidebar' => element($val, $item_sidebar),
                        'item_mobile_sidebar' => element($val, $item_mobile_sidebar),
                        'item_skin' => element($val, $item_skin),
                        'item_mobile_skin' => element($val, $item_mobile_skin),
                    );
                    $this->{$this->modelname}->update($val, $updatedata);
                    $this->Cmall_item_meta_model->save($val, $metadata);
                }
            }
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        /**
         * 업데이트가 끝난 후 목록페이지로 이동합니다
         */
        $this->session->set_flashdata(
            'message',
            '정상적으로 수정되었습니다'
        );
        $param =& $this->querystring;
        $redirecturl = admin_url($this->pagedir . '?' . $param->output());

        redirect($redirecturl);
    }

    /**
     * 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
     */
    public function listdelete()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_crawlitem_listdelete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $this->load->model(array('Cmall_wishlist_model','Crawl_link_click_log_model'));

        $this->load->library('aws_s3');
        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $getdata = $this->{$this->modelname}->get_one($val);
                    if($getdata){
                        $this->{$this->modelname}->delete($val);
                        $this->Cmall_item_meta_model->deletemeta($val);
                        $this->Cmall_item_detail_model->delete($val);

                        $deletewhere = array(
                            'crw_id' => $val,
                        );
                        $this->Crawl_link_click_log_model->delete_where($deletewhere);
                        $this->Crawl_tag_model->delete_where($deletewhere);            
                        $this->Cmall_wishlist_model->delete_where($deletewhere);
                        $this->Vision_api_label_model->delete_where($deletewhere);
                        
                        for ($i=1; $i <= 10; $i++)
                        {   
                            if($getdata['crw_file_'.$i]){
                                @unlink(config_item('uploads_dir') . '/crawlitem/' . $getdata['crw_file_'.$i]); 
                                $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/crawlitem/' . $getdata['crw_file_'.$i]);
                            }
                        }
                    }
                }
            }
        }

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

    public function _get_list_common($select = '', $join = '', $limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR',$where_in = '')
    {
     
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

        if ($this->where) {


            $this->db2->where($this->where);
        }
        
        if($this->or_where){

            
            // $this->db2->group_start();
                    
            // foreach ($this->or_where as $skey => $sval) {
            //     $this->db2->or_where($skey, $sval);
            // }
            
            $this->db2->group_start();
                $this->db2->or_where('crw_name', '');
                $this->db2->or_where('crw_post_url', '');
                $this->db2->or_where('crw_goods_code', '');

                $this->db2->group_start('','or');
                    $this->db2->where('crw_price', 0);
                    $this->db2->where('crw_price_sale', 0);
                    $this->db2->where('crw_is_soldout', 0);
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_brand1', '');
                    $this->db2->where('crw_brand2', '');
                    $this->db2->where('crw_brand3', '');
                    $this->db2->where('crw_brand4', '');
                    $this->db2->where('crw_brand5', '');

                    if($this->input->get('warning')){
                        $this->db2->where('cdt_brand1', '');
                        $this->db2->where('cdt_brand2', '');
                        $this->db2->where('cdt_brand3', '');
                        $this->db2->where('cdt_brand4', '');
                        $this->db2->where('cdt_brand5', '');
                    }
                    
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_brand1', '');
                    $this->db2->where('crw_brand2', '');
                    $this->db2->where('crw_brand3', '');
                    $this->db2->where('crw_brand4', '');
                    $this->db2->where('crw_brand5', '');

                    if($this->input->get('warning')){
                        $this->db2->where('cdt_brand1 is null',null,false);
                        $this->db2->where('cdt_brand2 is null',null,false);
                        $this->db2->where('cdt_brand3 is null',null,false);
                        $this->db2->where('cdt_brand4 is null',null,false);
                        $this->db2->where('cdt_brand5 is null',null,false);
                    }
                    
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_category1', '');
                    $this->db2->where('crw_category2', '');
                    $this->db2->where('crw_category3', '');
                $this->db2->group_end();
            $this->db2->group_end();

            
            
        }

        // if($this->where_in){
            
        //     $this->db2->group_start();
                    
        //     foreach ($this->where_in as $skey => $sval) {
        //         $this->db2->where_in($skey, $sval);
        //     }
            
        //     $this->db2->group_end();
            
            
        // }

        if($this->or_like){

            
            $this->db2->group_start();
                    
            foreach ($this->or_like as $skey => $sval) {
                $this->db2->or_like($skey, $sval);
            }
            
            $this->db2->group_end();
        }
        
        if ($search_where) {
            $this->db2->where($search_where);
        }
        if ($like) {
            $this->db2->like($like);
        }
        if ($search_like) {
            foreach ($search_like as $item) {
                foreach ($item as $skey => $sval) {
                    $this->db2->like($skey, $sval);
                }
            }
        }
        if ($search_or_like) {
            $this->db2->group_start();
            foreach ($search_or_like as $item) {
                foreach ($item as $skey => $sval) {
                    $this->db2->or_like($skey, $sval);
                }
            }
            $this->db2->group_end();
        }
        if ($count_by_where) {
            $this->db2->where($count_by_where);
        }

        $this->db2->order_by($findex, $forder);
        if ($limit) {
            $this->db2->limit($limit, $offset);
        }
        $qry = $this->db2->get();
        $result['list'] = $qry->result_array();


        $this->db2->select('count(*) as rownum');
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
        if ($this->where) {
            $this->db2->where($this->where);
        }

        if($this->or_where){

            
            // $this->db2->group_start();
                    
            // foreach ($this->or_where as $skey => $sval) {
            //     $this->db2->or_where($skey, $sval);
            // }
            
            $this->db2->group_start();
                $this->db2->or_where('crw_name', '');
                $this->db2->or_where('crw_post_url', '');
                $this->db2->or_where('crw_goods_code', '');

                $this->db2->group_start('','or');
                    $this->db2->where('crw_price', 0);
                    $this->db2->where('crw_price_sale', 0);
                    $this->db2->where('crw_is_soldout', 0);
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_brand1', '');
                    $this->db2->where('crw_brand2', '');
                    $this->db2->where('crw_brand3', '');
                    $this->db2->where('crw_brand4', '');
                    $this->db2->where('crw_brand5', '');

                    if($this->input->get('warning')){
                        $this->db2->where('cdt_brand1', '');
                        $this->db2->where('cdt_brand2', '');
                        $this->db2->where('cdt_brand3', '');
                        $this->db2->where('cdt_brand4', '');
                        $this->db2->where('cdt_brand5', '');
                    }
                    
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_brand1', '');
                    $this->db2->where('crw_brand2', '');
                    $this->db2->where('crw_brand3', '');
                    $this->db2->where('crw_brand4', '');
                    $this->db2->where('crw_brand5', '');

                    if($this->input->get('warning')){
                        $this->db2->where('cdt_brand1 is null',null,false);
                        $this->db2->where('cdt_brand2 is null',null,false);
                        $this->db2->where('cdt_brand3 is null',null,false);
                        $this->db2->where('cdt_brand4 is null',null,false);
                        $this->db2->where('cdt_brand5 is null',null,false);
                    }
                    
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_category1', '');
                    $this->db2->where('crw_category2', '');
                    $this->db2->where('crw_category3', '');
                $this->db2->group_end();
            $this->db2->group_end();

            
            
        }
        
        if ($search_where) {
            $this->db2->where($search_where);
        }
        //  if($this->where_in){
            
        //     $this->db2->group_start();
                    
        //     foreach ($this->where_in as $skey => $sval) {
                
        //         $this->db2->where_in($skey, $sval);
        //     }
            
        //     $this->db2->group_end();
            
            
        // }

        if($this->or_like){

            
            $this->db2->group_start();
                    
            foreach ($this->or_like as $skey => $sval) {
                $this->db2->or_like($skey, $sval);
            }
            
            $this->db2->group_end();
        }
        if ($like) {
            $this->db2->like($like);
        }
        if ($search_like) {
            foreach ($search_like as $item) {
                foreach ($item as $skey => $sval) {
                    $this->db2->like($skey, $sval);
                }
            }
        }
        if ($search_or_like) {
            $this->db2->group_start();
            foreach ($search_or_like as $item) {
                foreach ($item as $skey => $sval) {
                    $this->db2->or_like($skey, $sval);
                }
            }
            $this->db2->group_end();
        }
        if ($count_by_where) {
            $this->db2->where($count_by_where);
        }
        $qry = $this->db2->get();
        $rows = $qry->row_array();
        $result['total_rows'] = $rows['rownum'];

        return $result;
    }

    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {   

        
        if($this->input->get('warning'))
            $join[] = array('table' => 'crawl_detail', 'on' => 'crawl_detail.crw_id = crawl_item.crw_id', 'type' => 'left');
        else
            $join = '';

        $result = $this->_get_list_common($select ='', $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }

    public function get_count($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {   

        
        
            $join[] = array('table' => 'crawl_detail', 'on' => 'crawl_detail.crw_id = crawl_item.crw_id', 'type' => 'inner');
        

        $result = $this->_get_count_common($select ='', $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }

    public function _get_count_common($select = '', $join = '', $limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR',$where_in = '')
    {

        $this->db2->select('count(*) as rownum');
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
        if ($this->where) {
            $this->db2->where($this->where);
        }

        if($this->or_where){

            
            // $this->db2->group_start();
                    
            // foreach ($this->or_where as $skey => $sval) {
            //     $this->db2->or_where($skey, $sval);
            // }
            
            $this->db2->group_start();
                $this->db2->or_where('crw_name', '');
                $this->db2->or_where('crw_post_url', '');
                $this->db2->or_where('crw_goods_code', '');

                $this->db2->group_start('','or');
                    $this->db2->where('crw_price', 0);
                    $this->db2->where('crw_price_sale', 0);
                    $this->db2->where('crw_is_soldout', 0);
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_brand1', '');
                    $this->db2->where('crw_brand2', '');
                    $this->db2->where('crw_brand3', '');
                    $this->db2->where('crw_brand4', '');
                    $this->db2->where('crw_brand5', '');

                    
                        $this->db2->where('cdt_brand1', '');
                        $this->db2->where('cdt_brand2', '');
                        $this->db2->where('cdt_brand3', '');
                        $this->db2->where('cdt_brand4', '');
                        $this->db2->where('cdt_brand5', '');
                    
                    
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_brand1', '');
                    $this->db2->where('crw_brand2', '');
                    $this->db2->where('crw_brand3', '');
                    $this->db2->where('crw_brand4', '');
                    $this->db2->where('crw_brand5', '');

                    
                        $this->db2->where('cdt_brand1 is null',null,false);
                        $this->db2->where('cdt_brand2 is null',null,false);
                        $this->db2->where('cdt_brand3 is null',null,false);
                        $this->db2->where('cdt_brand4 is null',null,false);
                        $this->db2->where('cdt_brand5 is null',null,false);
                    
                    
                $this->db2->group_end();

                $this->db2->group_start('','or');
                    $this->db2->where('crw_category1', '');
                    $this->db2->where('crw_category2', '');
                    $this->db2->where('crw_category3', '');
                $this->db2->group_end();
            $this->db2->group_end();

            
            
        }
        
        
        //  if($this->where_in){
            
        //     $this->db2->group_start();
                    
        //     foreach ($this->where_in as $skey => $sval) {
                
        //         $this->db2->where_in($skey, $sval);
        //     }
            
        //     $this->db2->group_end();
            
            
        // }

        if($this->or_like){

            
            $this->db2->group_start();
                    
            foreach ($this->or_like as $skey => $sval) {
                $this->db2->or_like($skey, $sval);
            }
            
            $this->db2->group_end();
        }
        
        
        
        
        $qry = $this->db2->get();
        $rows = $qry->row_array();
        $result['total_rows'] = $rows['rownum'];

        return $result;
    }

    public function aaaa()
    {
        

        $view = array();
        $view['view'] = array();

        

        $this->load->model(array('Board_model'));
        
        
       

        if($this->input->get('sfield') === 'brd_id'){

            
            $this->db->like('brd_name',$this->input->get('skeyword'));
            $res = $this->Board_model->get('','brd_id');
            
            if($res){
                $brd_id_arr=array();
                foreach ($res as $key => $value) {
                    $brd_id_arr[] = element('brd_id',$value);
                }

                $this->where_in['crawl_item.brd_id'] =  $brd_id_arr;


                // $this->db2->group_end();
            }
        } 

        if($this->input->get('sfield') === 'brd_id2'){

            
            

                $this->where_in['crawl_item.brd_id'] =  $this->input->get('skeyword');


            // $this->db2->group_end();
            
        } 
        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->db2->select('brd_id,count(*) as cnt');

        $this->db2->group_by('brd_id');
        $this->db2->order_by('brd_id');
        $this->db2->from('crawl_item');
        $aaa = $this->db2->get();
        $result['list'] = $aaa->result_array();
        
        

        $i=0;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                
                // $this->db2->select($select);
                
                
                //     $this->db2->where(array('brd_id' => element('brd_id', $val),'cdt_file_1 !=' => '' ));
                // $result['list'][$key]['a_cnt'] = $this->db2->count_all_results('crawl_detail');

                // $this->db2->where(array('brd_id' => element('brd_id', $val),'cdt_content1 !=' => '' ));
                // $result['list'][$key]['b_cnt'] = $this->db2->count_all_results('crawl_detail');

                
                $this->db2->where(array('brd_id' => element('brd_id', $val)));
                $result['list'][$key]['d_cnt'] = $this->db2->count_all_results('crawl_detail');

            
               
                // }
               
                



                
                
                $result['list'][$key]['brd_name'] = $this->board->item_id('brd_name',element('brd_id', $val));

                
                // $result['list'][$key]['warning_count'] = $this->warning_count(element('brd_id', $val));

                
                
                $result['list'][$key]['num'] = $i++;

                
            }
        }

        $view['view']['data'] = $result;


       

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'aaaa');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    public function warning_count($brd_id =0)
    {

        

        $view = array();
        $view['view'] = array();

        

        $this->load->model(array('Board_model'));
        
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        

        


        
            
           
           $this->where['crawl_item.brd_id'] =  $brd_id;

        
        
            $or_where = array(
                'crw_name' => '',
                'crw_price' => 0,
                'crw_post_url' => '',
                'crw_goods_code' => '',                
                'crw_category1' => '',
            );
            
            $this->or_where  = $or_where;

            
        
        



        
        

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        
        $result = $this->get_count();

        return $result['total_rows'];
        
        
        // exit(json_encode($result,JSON_UNESCAPED_UNICODE));
    }
}

