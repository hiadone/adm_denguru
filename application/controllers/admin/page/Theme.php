<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Theme class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>페이지설정>팝업관리 controller 입니다.
 */
class Theme extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'page/theme';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Theme');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Theme_model';

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
        $this->load->library(array('pagination', 'querystring'));
    }

    /**
     * 목록을 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_theme_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $view['view']['sort'] = array(
            'the_id' => $param->sort('the_id', 'asc'),
            'the_title' => $param->sort('the_title', 'asc'),
            'the_url' => $param->sort('the_url', 'asc'),
            'the_hit' => $param->sort('the_hit', 'asc'),
            'the_order' => $param->sort('the_order', 'asc'),
            'the_activated' => $param->sort('the_activated', 'asc'),
        );
        $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->{$this->modelname}->primary_key;
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('the_id', 'the_title', 'the_url'); // 검색이 가능한 필드
        $this->{$this->modelname}->search_field_equal = array('the_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->{$this->modelname}->allow_order_field = array('the_id', 'the_start_date', 'the_end_date', 'the_title', 'the_url', 'the_hit', 'the_order', 'the_activated'); // 정렬이 가능한 필드

        $where = array();
        if ($this->input->get('the_activated') === 'Y') {
            $where['the_activated'] = '1';
        }
        if ($this->input->get('the_activated') === 'N') {
            $where['the_activated'] = '0';
        }

        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                if (element('the_image', $val)) {
                    $result['list'][$key]['thumb_url'] = thumb_url('theme', element('the_image', $val),80);
                }
                if (empty($val['the_start_date']) OR $val['the_start_date'] === '0000-00-00') {
                    $result['list'][$key]['the_start_date'] = '미지정';
                }
                if (empty($val['the_end_date']) OR $val['the_end_date'] === '0000-00-00') {
                    $result['list'][$key]['the_end_date'] = '미지정';
                }
                $result['list'][$key]['num'] = $list_num--;
            }
        }

        $view['view']['data'] = $result;

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $this->{$this->modelname}->primary_key;

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
        $search_option = array('the_title' => '이미지 설명', 'the_url' => '이미지 URL');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = admin_url($this->pagedir);
        $view['view']['write_url'] = admin_url($this->pagedir . '/write');
        $view['view']['list_delete_url'] = admin_url($this->pagedir . '/listdelete/?' . $param->output());

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
        $eventname = 'event_admin_page_theme_write';
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
                'field' => 'the_start_date',
                'label' => '시작일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'the_end_date',
                'label' => '종료일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'the_title',
                'label' => '제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'the_url',
                'label' => 'URL',
                'rules' => 'trim',
            ),
            // array(
            //     'field' => 'the_width',
            //     'label' => '이미지 가로값',
            //     'rules' => 'trim|required|numeric|is_natural',
            // ),
            // array(
            //     'field' => 'the_height',
            //     'label' => '이미지 세로값',
            //     'rules' => 'trim|required|numeric|is_natural',
            // ),
            array(
                'field' => 'the_order',
                'label' => '정렬순서',
                'rules' => 'trim|numeric|is_natural',
            ),
            array(
                'field' => 'the_activated',
                'label' => '배너활성화',
                'rules' => 'trim',
            ),
        );


        $this->form_validation->set_rules($config);
        $form_validation = $this->form_validation->run();
        $file_error = '';
        $updatephoto = '';

        if ($form_validation) {
            $this->load->library('upload');
            $this->load->library('aws_s3');
            if (isset($_FILES) && isset($_FILES['the_image']) && isset($_FILES['the_image']['name']) && $_FILES['the_image']['name']) {
                $upload_path = config_item('uploads_dir') . '/theme/';
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
                $uploadconfig['max_size'] = '2000';
                $uploadconfig['max_width'] = '1000';
                $uploadconfig['max_height'] = '1000';
                $uploadconfig['encrypt_name'] = true;

                $this->upload->initialize($uploadconfig);

                if ($this->upload->do_upload('the_image')) {
                    $img = $this->upload->data();
                    $updatephoto = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);

                    $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);                
                } else {
                    $file_error = $this->upload->display_errors();
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
            $view['view']['alert_message'] = $file_error;

            if ($pid) {
                
                if (empty($getdata['the_start_date']) OR $getdata['the_start_date'] === '0000-00-00') {
                    $getdata['the_start_date'] = '';
                }
                if (empty($getdata['the_end_date']) OR $getdata['the_end_date'] === '0000-00-00') {
                    $getdata['the_end_date'] = '';
                }
                $view['view']['data'] = $getdata;
            }


            $this->load->model(array('Board_model','Theme_model'));

            /**
             * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
             */
            $param =& $this->querystring;
            
            
            $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->Board_model->primary_key;
            $forder = $this->input->get('forder', null, 'desc');
            $sfield = $this->input->get('sfield', null, 'brd_name');
            $skeyword = $this->input->get('skeyword', null, '');

            
            
            
            $theme_rel = $where_in =array();
            $theme_rel = $this->Theme_model->get_theme_rel($pid);
            if($theme_rel)
            foreach($theme_rel as $othval)
                array_push($where_in,element('brd_id',$othval));


            $this->Board_model->set_where_in('brd_id',$where_in);
            /**
             * 게시판 목록에 필요한 정보를 가져옵니다.
             */
            $this->Board_model->allow_search_field = array('brd_name'); // 검색이 가능한 필드
            // $this->Board_model->search_field_equal = array('cit_goods_code', 'cit_price'); // 검색중 like 가 아닌 = 검색을 하는 필드
            
            if(empty($where_in))
                $cresult = array();
            else 
                $cresult = $this->Board_model->get_list('','', '', '', $findex, $forder, $sfield, $skeyword);

            

            $list_num = element('total_rows', $cresult) ? element('total_rows', $cresult) : 0;
            if (element('list', $cresult)) {
                foreach (element('list', $cresult) as $key => $val) {
                    // $cresult['list'][$key]['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $val));
                    
                    // $cresult['list'][$key]['attr'] = $this->Cmall_attr_model->get_attr(element('cit_id', $val));
                    
                    

                


            
                    

                    $cresult['list'][$key]['num'] = $list_num--;
                }
            }

            $view['view']['cdata'] = $cresult;
            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

            $view['view']['list_delete_url'] = admin_url($this->pagedir.'/theme_in_listdelete/'.$pid.'?' . $param->output());

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

            $content_type = $this->cbconfig->item('use_popup_dhtml') ? 1 : 0;

            $the_start_date = $this->input->post('the_start_date');
            if ( ! $the_start_date) $the_start_date = null;
            $the_end_date = $this->input->post('the_end_date');
            if ( ! $the_end_date) $the_end_date = null;
            $the_width = $this->input->post('the_width') ? $this->input->post('the_width') : 0;
            $the_height = $this->input->post('the_height') ? $this->input->post('the_height') : 0;
            $the_order = $this->input->post('the_order') ? $this->input->post('the_order') : 0;
            $the_activated = $this->input->post('the_activated') ? 1 : 0;

            $updatedata = array(
                'the_start_date' => $the_start_date,
                'the_end_date' => $the_end_date,
                'the_title' => $this->input->post('the_title', null, ''),
                'the_url' => $this->input->post('the_url', null, ''),
                'the_width' => $the_width,
                'the_height' => $the_height,
                'the_order' => $the_order,
                'the_activated' => $the_activated,
            );
            if ($this->input->post('the_image_del')) {
                $updatedata['the_image'] = '';
            } 
            if ($updatephoto) {
                $updatedata['the_image'] = $updatephoto;
            }
            if (element('the_image', $getdata) && ($this->input->post('the_image_del') OR $updatephoto)) {
                // 기존 파일 삭제
                @unlink(config_item('uploads_dir') . '/theme/' . element('the_image', $getdata));

                $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/theme/' . element('the_image', $getdata));
            }

            /**
             * 게시물을 수정하는 경우입니다
             */
            if ($this->input->post($primary_key)) {
                $this->cache->delete('theme/theme-' . element('the_title', $getdata) . '-random-' . cdate('Y-m-d'));
                $this->cache->delete('theme/theme-' . element('the_title', $getdata) . '-order-' . cdate('Y-m-d'));
                $this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);
                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['the_datetime'] = cdate('Y-m-d H:i:s');                
                $updatedata['mem_id'] = $this->member->item('mem_id');
                $this->{$this->modelname}->insert($updatedata);
                $this->session->set_flashdata(
                    'message',
                    '정상적으로 입력되었습니다'
                );
            }

            // 이벤트가 존재하면 실행합니다
            Events::trigger('after', $eventname);

            /**
             * 게시물의 신규입력 또는 수정작업이 끝난 후 목록 페이지로 이동합니다
             */
            $param =& $this->querystring;
            $redirecturl = admin_url($this->pagedir . '?' . $param->output());

            redirect($redirecturl);
        }
    }

    


    /**
     * 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
     */
    public function listdelete()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_theme_listdelete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);
        $this->load->library('aws_s3');
        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $getdata = $this->{$this->modelname}->get_one($val);
                    $this->cache->delete('theme/theme-' . element('the_title', $getdata) . '-random-' . cdate('Y-m-d'));
                    $this->cache->delete('theme/theme-' . element('the_title', $getdata) . '-order-' . cdate('Y-m-d'));
                    
                    if($this->{$this->modelname}->delete($val)){
                        if (element('the_image', $getdata)) {
                            // 기존 파일 삭제
                            @unlink(config_item('uploads_dir') . '/theme/' . element('the_image', $getdata));

                            $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/theme/' . element('the_image', $getdata));
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

    public function theme_in_listdelete($the_id)
    {
        
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallitem_listupdate';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        if (empty($the_id)) {
            show_404();
        }
        /**
         * 체크한 게시물의 업데이트를 실행합니다
         */
        
        $this->load->model(array('Theme_rel_model'));

        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            
            $this->Theme_rel_model->delete_theme($the_id, $this->input->post('chk'));    
            
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        /**
         * 업데이트가 끝난 후 목록페이지로 이동합니다
         */
        $this->session->set_flashdata(
            'message',
            '정상적으로 삭제 되었습니다'
        );
        $param =& $this->querystring;
        $redirecturl = admin_url($this->pagedir.'/write/' . $the_id. '?' . $param->output());

        redirect($redirecturl);
    }
}
