<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Other class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>페이지설정>팝업관리 controller 입니다.
 */
class Other extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'page/other';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Other');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Other_model';

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
        $eventname = 'event_admin_page_other_index';
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
            'oth_id' => $param->sort('oth_id', 'asc'),
            'oth_title' => $param->sort('oth_title', 'asc'),
            'oth_url' => $param->sort('oth_url', 'asc'),
            'oth_hit' => $param->sort('oth_hit', 'asc'),
            'oth_order' => $param->sort('oth_order', 'asc'),
            'oth_activated' => $param->sort('oth_activated', 'asc'),
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
        $this->{$this->modelname}->allow_search_field = array('oth_id', 'oth_title', 'oth_url'); // 검색이 가능한 필드
        $this->{$this->modelname}->search_field_equal = array('oth_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->{$this->modelname}->allow_order_field = array('oth_id', 'oth_start_date', 'oth_end_date', 'oth_title', 'oth_url', 'oth_hit', 'oth_order', 'oth_activated'); // 정렬이 가능한 필드

        $where = array();
        if ($this->input->get('oth_activated') === 'Y') {
            $where['oth_activated'] = '1';
        }
        if ($this->input->get('oth_activated') === 'N') {
            $where['oth_activated'] = '0';
        }

        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                if (element('oth_image', $val)) {
                    $result['list'][$key]['thumb_url'] = thumb_url('other', element('oth_image', $val), '80');
                }
                if (empty($val['oth_start_date']) OR $val['oth_start_date'] === '0000-00-00') {
                    $result['list'][$key]['oth_start_date'] = '미지정';
                }
                if (empty($val['oth_end_date']) OR $val['oth_end_date'] === '0000-00-00') {
                    $result['list'][$key]['oth_end_date'] = '미지정';
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
        $search_option = array('oth_title' => '이미지 설명', 'oth_url' => '이미지 URL');
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
        $eventname = 'event_admin_page_other_write';
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
                'field' => 'oth_start_date',
                'label' => '배너시작일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'oth_end_date',
                'label' => '배너종료일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'oth_title',
                'label' => '이미지 설명',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'oth_url',
                'label' => 'URL',
                'rules' => 'trim',
            ),
            array(
                'field' => 'oth_width',
                'label' => '이미지 가로값',
                'rules' => 'trim|required|numeric|is_natural',
            ),
            array(
                'field' => 'oth_height',
                'label' => '이미지 세로값',
                'rules' => 'trim|required|numeric|is_natural',
            ),
            array(
                'field' => 'oth_order',
                'label' => '정렬순서',
                'rules' => 'trim|required|numeric|is_natural',
            ),
            array(
                'field' => 'oth_activated',
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
            if (isset($_FILES) && isset($_FILES['oth_image']) && isset($_FILES['oth_image']['name']) && $_FILES['oth_image']['name']) {
                $upload_path = config_item('uploads_dir') . '/other/';
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

                if ($this->upload->do_upload('oth_image')) {
                    $img = $this->upload->data();
                    $updatephoto = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);

                    // $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);                
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
            $view['view']['message'] = $file_error;

            if ($pid) {
                if (empty($getdata['oth_start_date']) OR $getdata['oth_start_date'] === '0000-00-00') {
                    $getdata['oth_start_date'] = '';
                }
                if (empty($getdata['oth_end_date']) OR $getdata['oth_end_date'] === '0000-00-00') {
                    $getdata['oth_end_date'] = '';
                }
                $view['view']['data'] = $getdata;
            }

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

            $content_type = $this->cbconfig->item('use_popup_dhtml') ? 1 : 0;

            $oth_start_date = $this->input->post('oth_start_date');
            if ( ! $oth_start_date) $oth_start_date = null;
            $oth_end_date = $this->input->post('oth_end_date');
            if ( ! $oth_end_date) $oth_end_date = null;
            $oth_width = $this->input->post('oth_width') ? $this->input->post('oth_width') : 0;
            $oth_height = $this->input->post('oth_height') ? $this->input->post('oth_height') : 0;
            $oth_order = $this->input->post('oth_order') ? $this->input->post('oth_order') : 0;
            $oth_activated = $this->input->post('oth_activated') ? 1 : 0;

            $updatedata = array(
                'oth_start_date' => $oth_start_date,
                'oth_end_date' => $oth_end_date,
                'oth_title' => $this->input->post('oth_title', null, ''),
                'oth_url' => $this->input->post('oth_url', null, ''),
                'oth_width' => $oth_width,
                'oth_height' => $oth_height,
                'oth_order' => $oth_order,
                'oth_activated' => $oth_activated,
            );
            if ($this->input->post('oth_image_del')) {
                $updatedata['oth_image'] = '';
            } elseif ($updatephoto) {
                $updatedata['oth_image'] = $updatephoto;
            }
            if (element('oth_image', $getdata) && ($this->input->post('oth_image_del') OR $updatephoto)) {
                // 기존 파일 삭제
                @unlink(config_item('uploads_dir') . '/other/' . element('oth_image', $getdata));

                // $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/other/' . element('oth_image', $getdata));
            }

            /**
             * 게시물을 수정하는 경우입니다
             */
            if ($this->input->post($primary_key)) {
                $this->cache->delete('other/other-' . element('oth_title', $getdata) . '-random-' . cdate('Y-m-d'));
                $this->cache->delete('other/other-' . element('oth_title', $getdata) . '-order-' . cdate('Y-m-d'));
                $this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);
                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['oth_datetime'] = cdate('Y-m-d H:i:s');                
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
        $eventname = 'event_admin_page_other_listdelete';
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
                    $this->cache->delete('other/other-' . element('oth_title', $getdata) . '-random-' . cdate('Y-m-d'));
                    $this->cache->delete('other/other-' . element('oth_title', $getdata) . '-order-' . cdate('Y-m-d'));
                    
                    if($this->{$this->modelname}->delete($val)){
                        if (element('oth_image', $getdata)) {
                            // 기존 파일 삭제
                            @unlink(config_item('uploads_dir') . '/other/' . element('oth_image', $getdata));

                            // $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/other/' . element('oth_image', $getdata));
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
}
