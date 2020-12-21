<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fcm class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>회원설정>포인트관리 controller 입니다.
 */
class Fcm extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Fcm','Device','Member','Member_group');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'dhtml_editor');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('pagination', 'querystring'));
    }

    /**
     * 게시판 목록입니다.
     */
    public function lists()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_fcm_lists';
        $this->load->event($eventname);

        

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $view['view']['list'] = $list = $this->_get_list();

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();
        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        $getdata = array();
        $getdata['mgroup'] = $this->Member_group_model->get_all_group();
        $view['view']['data']['mgroup'] = $getdata['mgroup'];
        /**
         * 레이아웃을 정의합니다
         */

        $layoutconfig = array(
            'path' => 'fcm',
            'layout' => 'layout',
            'skin' => 'list',
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }


    /**
     * 게시물 열람 페이지입니다
     */
    public function post($post_id = 0, $print = false)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_fcm_post';
        $this->load->event($eventname);
        
        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            show_404();
        }

        $post = $this->Fcm_model->get_one($post_id);
        $view['view']['post'] = $post;

        if ( ! element('fcm_id', $post)) {
            show_404();
        }

        $getdata = array();
        $getdata['mgroup'] = $this->Member_group_model->get_all_group();
        $view['view']['data']['mgroup'] = $getdata['mgroup'];

        $skeyword = $this->input->get('skeyword', null, '');
        
        // 본인인증 사용하는 경우 - 끝

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step1'] = Events::trigger('step1', $eventname);
        
        
        // 세션 생성
        if ( ! $this->session->userdata('fcm_id_' . $post_id)) {
            $this->session->set_userdata(
                'fcm_id_' . $post_id,
                '1'
            );
        }

        $view['view']['post']['display_datetime'] = display_datetime(
            element('fcm_datetime', $post)
        );

        
        $view['view']['post']['content'] = '';


        $view['view']['post_url'] = $post_url = base_url('fcm/post/'.$post_id);

        $param =& $this->querystring;


        $can_modify = $is_admin ? true : false;
        $can_delete = $is_admin ? true : false;
        
        $view['view']['modify_url'] = $can_modify ? base_url('fcm/modify/'.element('fcm_id', $post) . '?' . $param->output()) : '';
        $view['view']['delete_url'] = $can_delete ? base_url('fcm/fcm_delete/' . element('fcm_id', $post) . '?' . $param->output()) : '';

        if ($skeyword) {
            $view['view']['list_url'] = base_url('fcm/lists');
            $view['view']['search_list_url'] = base_url('fcm/lists?' . $param->output());
        } else {
            $view['view']['list_url'] = base_url('fcm/lists?' . $param->output());
            $view['view']['search_list_url'] = '';
        }
        
        

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step2'] = Events::trigger('step2', $eventname);

            
        $layoutconfig = array(
            'path' => 'fcm',
            'layout' => 'layout',
            'skin' => 'post',
        );

        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        
        $this->view = element('view_skin_file', element('layout', $view));
            
         
    }

    /**
     * 게시판 목록페이지입니다.
     */
    public function _get_list($from_view = '')
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_fcm_get_list';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('list_before', $eventname);

        $return = array();

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $order_by_field = 'fcm_id';

        $findex = $this->input->get('findex', null, $order_by_field);
        $forder = 'desc';
        $sfield = $sfieldchk = $this->input->get('sfield', null, '');
        if ($sfield === 'fcm_both') {
            $sfield = array('fcm_title', 'fcm_message');
        }
        $skeyword = $this->input->get('skeyword', null, '');
        
        $per_page =  20;
        $offset = ($page - 1) * $per_page;

        $this->Fcm_model->allow_search_field = array('fcm_id', 'fcm_title', 'fcm_message', 'fcm_both'); // 검색이 가능한 필드
        

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step1'] = Events::trigger('list_step1', $eventname);

        
        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array();
        

        $result = $this->Fcm_model
            ->get_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['post_url'] = base_url('/fcm/post/'.element('fcm_id', $val));
                $result['list'][$key]['display_send_date'] = element('fcm_send_date', $val) ? display_datetime(element('fcm_send_date', $val),'full') : display_datetime(element('fcm_reg_date', $val),'full');

                
                $result['list'][$key]['delete_url'] = $is_admin ? base_url('fcm/fcm_delete/' . element('fcm_id', $val) . '?' . $param->output()) : '';
                if ($param->output()) {
                    $result['list'][$key]['post_url'] .= '?' . $param->output();
                }

                $result['list'][$key]['num'] = $list_num--;
            }
        }

        $return['data'] = $result;
        

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step2'] = Events::trigger('list_step2', $eventname);


        /**
         * primary key 정보를 저장합니다
         */
        $return['primary_key'] = $this->Fcm_model->primary_key;
        

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = base_url('/fcm/lists/') . '?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;      
        $config['num_links'] = 5;        
        $this->pagination->initialize($config);
        $return['paging'] = $this->pagination->create_links();
        $return['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $search_option = array(
            'fcm_title' => '제목',
            'fcm_message' => '메세지'
        );
        $return['search_option'] = search_option($search_option, $sfield);
        if ($skeyword) {
            $return['list_url'] = base_url('/fcm/lists');
            $return['search_list_url'] = base_url('/fcm/lists' . '?' . $param->output());
        } else {
            $return['list_url'] = base_url('/fcm/lists' . '?' . $param->output());;
            $return['search_list_url'] = '';
        }

        
        if ($is_admin) {
            $return['write_url'] = base_url('/fcm/write');

        }


        $can_delete = $is_admin ? true : false;


        $return['list_delete_url'] = site_url('fcm/fcm_delete/?' . $param->output());

        return $return;
    }

    /**
     * 게시물 작성 페이지입니다
     */
    public function write()
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_fcm_write';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);
        
        Events::trigger('after', $eventname);

        $this->_write_common();
    
    }


    /**
     * 게시물 작성과 답변에 공통으로 쓰입니다
     */
    public function _write_common($origin = '', $reply = '')
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_fcm_write_common';
        $this->load->event($eventname);

        $param =& $this->querystring;

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('common_before', $eventname);

        $view['view']['post'] = array();

        $primary_key = $this->Fcm_model->primary_key;

        $getdata = array();
        $getdata['mgroup'] = $this->Member_group_model->get_all_group();

        $view['view']['data']['mgroup'] = $getdata['mgroup'];
        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'fcm_title',
                'label' => '제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'fcm_message',
                'label' => '메세지',
                'rules' => 'trim|required',
            ),
        );


        $this->form_validation->set_rules($config);
        $form_validation = $this->form_validation->run();

        $file_error = '';
        $uploadfiledata = array();
        

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step2'] = Events::trigger('common_step2', $eventname);

        


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($form_validation === false OR $file_error) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('common_formrunfalse', $eventname);

            if ($file_error) {
                $view['view']['message'] = $file_error;
            }

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['before_layout'] = Events::trigger('common_before_layout', $eventname);

            /**
             * 레이아웃을 정의합니다
             */
            

            
            $layoutconfig = array(
                'path' => 'fcm',
                'layout' => 'layout',
                'skin' => 'write',
                
            );
            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));

        } else {

            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('common_formruntrue', $eventname);

           

            $fcm_title = $this->input->post('fcm_title', null, '');
            $fcm_message = $this->input->post('fcm_message', null, '');
            $fcm_target =  $this->input->post('fcm_target', null, '');
            $fcm_target_group =  json_encode($this->input->post('fcm_target_group', null, ''));
            $fcm_send_date = $this->input->post('fcm_send_date', null, '');

            $updatedata = array(
                
                'fcm_title' => $fcm_title,
                'fcm_message' => $fcm_message,
                'fcm_target' => $fcm_target,
                'fcm_reg_date' => date('Y-m-d H:i:s'),
                'fcm_send_date' => $fcm_send_date,
                'fcm_target_group' => $fcm_target_group,
            );

            $fcm_id = $this->Fcm_model->insert($updatedata);
            

            $this->session->set_flashdata(
                'message',
                '게시물이 정상적으로 입력되었습니다'
            );
            
            
            $fcm_send_list =  $this->_get_fcm_send_list($fcm_target,$this->input->post('fcm_target_group', null, ''));
            
            $this->sendFCMMessage($fcm_id,$fcm_send_list,$fcm_title,$fcm_message);
            

            // 이벤트가 존재하면 실행합니다
            Events::trigger('common_after', $eventname);

            /**
             * 게시물의 신규입력 또는 수정작업이 끝난 후 뷰 페이지로 이동합니다
             */
            $redirecturl = base_url('/fcm/lists/');
            // redirect($redirecturl);
        }
    }

    /**
     * 게시물 수정 페이지입니다
     */
    public function modify($post_id = 0)
    {
        return false;
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_fcm_modify';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            show_404();
        }

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
        $post = $this->Fcm_model->get_one($post_id);
        if ( ! element('fcm_id', $post)) {
            show_404();
        }

        $view['view']['post'] = $post;
        

        $postwhere = array(
            'fcm_id' => $post_id,
        );

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();
        


        $primary_key = $this->Fcm_model->primary_key;

        $getdata = array();
        $getdata['mgroup'] = $this->Member_group_model->get_all_group();
        $view['view']['data']['mgroup'] = $getdata['mgroup'];

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step1'] = Events::trigger('step1', $eventname);

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'fcm_id',
                'label' => 'FCMID',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'fcm_title',
                'label' => '제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'fcm_message',
                'label' => '메세지',
                'rules' => 'trim|required',
            ),
        );

        


        $this->form_validation->set_rules($config);
        $form_validation = $this->form_validation->run();
        $file_error = '';

        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($form_validation === false OR $file_error) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;

            if ($file_error) {
                $view['view']['message'] = $file_error;
            }

            

            

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

            /**
             * 레이아웃을 정의합니다
             */
            

            
            $layoutconfig = array(
                'path' => 'fcm',
                'layout' => 'layout',
                'skin' => 'write',
            
            );
            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));

        } else {

            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            if( $this->input->post($primary_key) != $post_id ){
                // $_POST['post_id'] 값과 $_GET['post_id'] 값이 틀린 경우입니다.
            }

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            $fcm_title = $this->input->post('fcm_title', null, '');
            $fcm_message = $this->input->post('fcm_message', null, ''); 
            $fcm_target =  $this->input->post('fcm_target', null, '');
            $fcm_target_group =  json_encode($this->input->post('fcm_target_group', null, ''));
            $fcm_send_date = $this->input->post('fcm_send_date', null, ''); 

            
            $updatedata = array(
                'fcm_title' => $fcm_title,
                'fcm_message' => $fcm_message,
                'fcm_target' => $fcm_target,
                'fcm_send_date' => $fcm_send_date,
                'fcm_target_group' => $fcm_target_group,
            );
                
            // 이벤트가 존재하면 실행합니다
            Events::trigger('before_post_update', $eventname);

            $this->Fcm_model->update($post_id, $updatedata);

            

            // 이벤트가 존재하면 실행합니다
            Events::trigger('after', $eventname);


            $this->session->set_flashdata(
                'message',
                '게시물이 정상적으로 수정되었습니다'
            );

            /**
             * 게시물의 신규입력 또는 수정작업이 끝난 후 뷰 페이지로 이동합니다
             */
            $param =& $this->querystring;
            $redirecturl = base_url('fcm/lists/') . '?' . $param->output();

            redirect($redirecturl);
        }
    }

    /**
     * FCM 게시물 삭제하기
     */
    public function fcm_delete($post_id = 0)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_postact_delete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            show_404();
        }
        
        $this->load->model('Fcm_model');
        $post = $this->Fcm_model->get_one($post_id);

        if ( ! element('fcm_id', $post)) {
            show_404();
        }

        

        $is_admin = $this->member->is_admin();

        if ($is_admin === false) {
            alert('이 게시판의 글은 관리자에 의해서만 삭제가 가능합니다');
            return false;
        }
        

        

        $this->Fcm_model->delete($post_id);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        $this->session->set_flashdata(
            'message',
            '게시물이 정상적으로 삭제되었습니다'
        );

        /**
         * 게시물의 신규입력 또는 수정작업이 끝난 후 뷰 페이지로 이동합니다
         */
        $param =& $this->querystring;
        $redirecturl = base_url('fcm/lists/') . '?' . $param->output();

        redirect($redirecturl);

    }


    public function sendFCMMessage($fcm_id = 0,$token = array(),$fcm_title='',$fcm_message='')
    {   


        
        $eventname = 'event_sendFCMMessage';
        $this->load->event($eventname);

        require_once(FCPATH . 'plugin/google/fcm/vendor/autoload.php'); 

        // Instantiate the client with the project api_token and sender_id.
        $client = new \Fcm\FcmClient('AAAAdz4rKCU:APA91bGkXxjgopvZQ-jAJosF8XP5VE_qGuAusAy2u4P2Z6SvGeNsXo803a5BpV5RldbiYlf_YTOvPzRg4xHm8pxcfHatc0Li_FlmXEeP9l4Il-VtronzRnlUPQPHIqI4uk9XpHWjSAxS', '');

        // $notification = new \Fcm\Push\Notification();
        $notification = new \Fcm\Push\Notification();

        if(element('dev_token',$token)){
            
            // Enhance the notification object with our custom options.
            $notification
                ->addRecipient('fvetjC6SuPI:APA91bGjepUPF50lWWz7DHZEbX06QZUlj2DmZrzcqM_PK0KQdiCFU9NF7b2r5aLiYKdCW3maa_Rhcb0z3hqSe57AP6hhzC6eAuEoDaAj-nQcwGkafItV1dxoFlVDFykb586xmD--tck6')
                // ->addRecipient(element('dev_token',$token))
                ->setTitle($fcm_title)
                ->setBody($fcm_message)                
                // ->addData('title', $fcm_title)
                // ->addData('msg', $fcm_message)                
                ->addData('category', 'https://naver.com')
                ->addData('icon', '/assets/images/favi.png')
                ;

            // Send the notification to the Firebase servers for further handling.
            $result = $client->send($notification);

            unset($result['results']);

            
            $updatedata = array(                
                'fcm_result' => json_encode($result),
            );
                
            // 이벤트가 존재하면 실행합니다
            Events::trigger('before_post_update', $eventname);

            $this->Fcm_model->update($fcm_id, $updatedata);

            
            
        }
    }



    public function _get_fcm_send_list($fcm_target = 0,$fcm_target_group = '')
    {
        $where = array();
        
        if($fcm_target ==='1'){
            $result = $this->Device_model
                ->get_list('','', $where);
            $return ='';
            if (element('list', $result)) {
                foreach (element('list', $result) as $key => $val) {
                    $return['mem_id'][$key] = element('mem_id',$val);
                    $return['dev_token'][$key] = element('dev_token',$val);
                }
            }
            return $return;
        } elseif($fcm_target ==='2'){
            $where['mem_denied'] = 0;            
            
            $result = $this->Device_model->get_admin_list('','', $where);
            $return = array();
            if (element('list', $result)) {
                foreach (element('list', $result) as $key => $val) {                    
                    $return['mem_id'][$key] = element('mem_id',$val);
                    $return['dev_token'][$key] = element('dev_token',$val);
                    
                }
            }
            
            return $return;

        } elseif($fcm_target ==='3'){
            $where['mem_denied'] = 0;                        
            
            if($fcm_target_group)
                $where_in['mgr_id'] = $fcm_target_group;    
            
            $this->Member_model->allow_order_field = array('member.mem_id'); // 정렬이 가능한 필드
            $result = $this->Member_model->get_admin_list('','', $where,'','member.mem_id','','','','',$where_in);

            $return ='';
            if (element('list', $result)) {
                foreach (element('list', $result) as $key => $val) {
                    $return['mem_id'][$key] = element('mem_id',$val);
                    $return['dev_token'][$key] = element('mem_token',$val);
                    
                    
                }
            }

            return $return;
        }
    }


    
}
