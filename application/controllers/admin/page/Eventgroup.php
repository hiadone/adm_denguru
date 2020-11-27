<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Eventgroup class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>페이지설정>EVENT관리 controller 입니다.
 */
class Eventgroup extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'page/eventgroup';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Event', 'Event_group','Event_rel');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Event_group_model';

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array','dhtml_editor');

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
        $eventname = 'event_admin_page_eventgroup_index';
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
            'egr_id' => $param->sort('egr_id', 'asc'),
            'egr_title' => $param->sort('egr_title', 'asc'),
            'egr_key' => $param->sort('egr_key', 'asc'),            
            'egr_datetime' => $param->sort('egr_datetime', 'asc'),
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
        $this->{$this->modelname}->allow_search_field = array('egr_id', 'egr_key',  'sfield', 'event_group.mem_id', 'egr_title'); // 검색이 가능한 필드
        $this->{$this->modelname}->search_field_equal = array('egr_id', 'event_group.mem_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->{$this->modelname}->allow_order_field = array('egr_id', 'egr_key',  'egr_datetime'); // 정렬이 가능한 필드
        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, '', '', $findex, $forder, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['display_name'] = display_username(
                    element('mem_userid', $val),
                    element('mem_nickname', $val),
                    element('mem_icon', $val)
                );
                

                if (element('egr_image', $val)) {
                    $result['list'][$key]['cdn_url'] = cdn_url('eventgroup', element('egr_image', $val));

                }

                $countwhere = array(
                    'egr_id' => element('egr_id', $val),
                );

                $result['list'][$key]['eventcount'] = $this->Event_model->count_by($countwhere);
                
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
        $search_option = array('egr_title' => '제목', 'egr_datetime' => '날짜');
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
        $eventname = 'event_admin_page_eventgroup_write';
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
                'field' => 'egr_title',
                'label' => '제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'egr_start_date',
                'label' => '시작일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'egr_end_date',
                'label' => '종료일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'egr_order',
                'label' => '정렬순서',
                'rules' => 'trim|required|numeric|is_natural',
            ),
            array(
                'field' => 'egr_activated',
                'label' => '이벤트활성화',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'egr_content',
                'label' => '내용',
                'rules' => 'trim',
            ),            
            array(
                'field' => 'egr_content',
                'label' => '내용',
                'rules' => 'trim',
            ),            
            array(
                'field' => 'egr_type',
                'label' => '이벤트타입',
                'rules' => 'trim|required|numeric',
            ),
        );
        

        $this->form_validation->set_rules($config);
        $form_validation = $this->form_validation->run();
        $file_error = '';
        $file_error2 = '';
        $updatephoto = '';
        $updatephoto2 = '';

        if ($form_validation) {
            $this->load->library('upload');
            
            if (isset($_FILES) && isset($_FILES['egr_image']) && isset($_FILES['egr_image']['name']) && $_FILES['egr_image']['name']) {
                $upload_path = config_item('uploads_dir') . '/eventgroup/';
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
                $uploadconfig['max_size'] = 10 * 1024;
                $uploadconfig['max_width'] = '2000';
                $uploadconfig['max_height'] = '2000';
                $uploadconfig['encrypt_name'] = true;

                $this->upload->initialize($uploadconfig);

                if ($this->upload->do_upload('egr_image')) {
                    $img = $this->upload->data();
                    $updatephoto = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);

                    $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);                
                } else {
                    $file_error = $this->upload->display_errors();
                }
            }

            if (isset($_FILES) && isset($_FILES['egr_detail_image']) && isset($_FILES['egr_detail_image']['name']) && $_FILES['egr_detail_image']['name']) {
                $upload_path = config_item('uploads_dir') . '/eventgroup/';
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
                $uploadconfig['max_size'] = 20 * 1024;
                $uploadconfig['max_width'] = '2000';
                $uploadconfig['max_height'] = '4000';
                $uploadconfig['encrypt_name'] = true;

                $this->upload->initialize($uploadconfig);

                if ($this->upload->do_upload('egr_detail_image')) {
                    $img = $this->upload->data();
                    $updatephoto2 = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);

                    $upload2 = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);                
                } else {
                    $file_error2 = $this->upload->display_errors();
                }
            }
        }

        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($form_validation === false OR $file_error !== '' OR $file_error2 !== '') {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);
            $view['view']['alert_message'] = $file_error . $file_error2;

            if ($pid) {
                if (empty($getdata['egr_start_date']) OR $getdata['egr_start_date'] === '0000-00-00') {
                    $getdata['egr_start_date'] = '';
                }
                if (empty($getdata['egr_end_date']) OR $getdata['egr_end_date'] === '0000-00-00') {
                    $getdata['egr_end_date'] = '';
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

            $egr_start_date = $this->input->post('egr_start_date') ? $this->input->post('egr_start_date') : null;
            $egr_end_date = $this->input->post('egr_end_date') ? $this->input->post('egr_end_date') : null;            
            $egr_activated = $this->input->post('egr_activated') ? $this->input->post('egr_activated') : 0;
            $egr_order = $this->input->post('egr_order') ? $this->input->post('egr_order') : 0;
            $egr_content = $this->input->post('egr_content') ? $this->input->post('egr_content') : '';
            $egr_type = $this->input->post('egr_type') ? $this->input->post('egr_type') : 0;
            

            $updatedata = array(
                'egr_title' => $this->input->post('egr_title', null, ''),
                'egr_start_date' => $egr_start_date,
                'egr_end_date' => $egr_end_date,
                'egr_activated' => $egr_activated,
                'egr_order' => $egr_order,
                'egr_content' => $egr_content,
                'egr_type' => $egr_type,
            );

            

            /**
             * 게시물을 수정하는 경우입니다
             */
            if ($this->input->post($primary_key)) {

                if ($this->input->post('egr_image_del')) {
                    $updatedata['egr_image'] = '';
                } 
                if ($updatephoto) {
                    $updatedata['egr_image'] = $updatephoto;
                }
                if (element('egr_image', $getdata) && ($this->input->post('egr_image_del') OR $updatephoto)) {
                    // 기존 파일 삭제
                    @unlink(config_item('uploads_dir') . '/eventgroup/' . element('egr_image', $getdata));

                    $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/eventgroup/' . element('egr_image', $getdata));
                }

                if ($this->input->post('egr_detail_image_del')) {
                    $updatedata['egr_detail_image'] = '';
                } 
                if ($updatephoto2) {
                    $updatedata['egr_detail_image'] = $updatephoto2;
                }
                if (element('egr_detail_image', $getdata) && ($this->input->post('egr_detail_image_del') OR $updatephoto2)) {
                    // 기존 파일 삭제
                    @unlink(config_item('uploads_dir') . '/eventgroup/' . element('egr_detail_image', $getdata));

                    $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/eventgroup/' . element('egr_detail_image', $getdata));
                }
                $this->cache->delete('event_group/event_group-info-' . cdate('Y-m-d'));
                $this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);
                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                
                if ($updatephoto) {
                    $updatedata['egr_image'] = $updatephoto;
                }

                if ($updatephoto2) {
                    $updatedata['egr_detail_image'] = $updatephoto2;
                }
                $updatedata['egr_datetime'] = cdate('Y-m-d H:i:s');
                $updatedata['egr_ip'] = $this->input->ip_address();
                $updatedata['mem_id'] = $this->member->item('mem_id');
                $egr_id = $this->{$this->modelname}->insert($updatedata);


                $updatedata['egr_key'] = 'e_'.$egr_id;

                $this->{$this->modelname}->update($egr_id, $updatedata);
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
        $eventname = 'event_admin_page_eventgroup_listdelete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {

                    $getdata = $this->{$this->modelname}->get_one($val);

                    if(element('egr_image', $getdata))
                        $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/eventgroup/' . element('egr_image', $getdata));

                    if(element('egr_detail_image', $getdata))
                        $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/eventgroup/' . element('egr_detail_image', $getdata));

                    
                    $where = array(
                        'egr_id' => $val,
                    );
                    $this->cache->delete('event_group/event_group-info-' . cdate('Y-m-d'));
                    $res = $this->Event_model->get('','',$where);

                    if ($res && is_array($res)) {                        
                        foreach ($res as $evalue) {
                            if (element('eve_id', $evalue)) {

                                $this->Event_rel_model->delete_where(array('eve_id' => element('eve_id', $evalue)));
                                
                                $this->Event_model->delete(element('eve_id', $evalue));
                            }
                        }
                    }

                    $this->{$this->modelname}->delete($val);
                    
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

    public function notification_send($pid)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_notice_write';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        
        $pid = (int) $pid;
        if (empty($pid) OR $pid < 1) {
            show_404();
        }
        
        

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
        
        
        $getdata = $this->{$this->modelname}->get_one($pid);
     
        $this->load->library('notificationlib');
        $this->load->model('member_model','Notification_model');

        $result = $this->Member_model   
            ->get_admin_list();
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $countwhere = array(
                    'target_mem_id' => element('mem_id', $val),
                    'not_type' => 'event',
                    'not_content_id' => element('egr_id',$getdata),
                    
                );
                if($this->Notification_model->count_by($countwhere)) continue;

                $egr_file ='';
                if(element('egr_image',$getdata))
                    $egr_file =  cdn_url('eventgroup', element('egr_image', $getdata));

                $protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
                $not_url = $protocol.'://api.denguru.kr/event/post/'.element('egr_id', $getdata); 

                $this->notificationlib->set_noti(
                    1,
                    element('mem_id', $val),
                    'event',
                    element('egr_id',$getdata),
                    element('egr_title',$getdata),
                    $not_url,
                    $egr_file,
                );
            }
        }

        $this->session->set_flashdata(
            'message',
            '정상적으로 발송되었습니다'
        );

        $param =& $this->querystring;
        $redirecturl = admin_url($this->pagedir . '?' . $param->output());
        
        redirect($redirecturl);
        
        
    }
}
