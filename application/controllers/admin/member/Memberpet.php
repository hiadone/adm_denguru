<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Memberpet class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>회원설정>회원관리 controller 입니다.
 */
class Memberpet extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'member/memberpet';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Member_meta', 'Member_group', 'Member_pet','Pet_allergy', 'Pet_attr', 'Cmall_kind','Pet_allergy_rel','Pet_attr_rel');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Member_pet_model';

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'chkstring');

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
        $eventname = 'event_admin_member_memberpet_index';
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
            'mem_userid' => $param->sort('member.mem_userid', 'asc'),
            'mem_nickname' => $param->sort('member.mem_nickname', 'asc'),
            'pet_name' => $param->sort('pet_name', 'asc'),
            
        );
        $findex = $this->input->get('findex', null, 'pet_id');
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');


        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('pet_name','member.mem_userid', 'member.mem_nickname'); // 검색이 가능한 필드
        
        $this->{$this->modelname}->allow_order_field = array('pet_name','member.mem_userid', 'member.mem_nickname'); // 정렬이 가능한 필드

        $where = array();
        
        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                
                
                if (element('pet_photo', $val)) {
                    // $result['list'][$key]['thumb_url'] = thumb_url('member_photo', element('pet_photo', $val), '80');
                    $result['list'][$key]['thumb_url'] = cdn_url('member_photo', element('pet_photo', $val));
                    
                }

                $result['list'][$key]['display_name'] = display_username(
                    element('mem_userid', $val),
                    element('mem_nickname', $val)
                );
                $result['list'][$key]['display_pet_name'] = element('pet_name', $val);
                

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
        $search_option = array('member.mem_nickname' => '닉네임','member.mem_userid' => '회원ID', 'pet_name' => '펫네임');
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
        $eventname = 'event_admin_member_memberpet_write';
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
            $where = array(
                'pet_id' => $pid,
            );   
            $getdata['mem_userid'] = element('mem_userid',$this->Member_model->get_by_memid(element('mem_id',$getdata),'mem_userid'));

            $pet_kind_text = array();
            if(element('ckd_id', $getdata))
                $pet_kind_text = $this->Cmall_kind_model->get_kind_info(element('ckd_id', $getdata));

            
            if(element('ckd_value_kr',$pet_kind_text))
                $getdata['pet_kind_text']  = element('ckd_value_kr',$pet_kind_text);
            else            
                $getdata['pet_kind_text']  = '';  

            $pet_attr = $this->Pet_attr_model->get_attr(element('pet_id',$getdata));            
            
            if ($pet_attr) {
                foreach ($pet_attr as $akey => $aval) {
                    $getdata['pet_attr'][] = $aval['pat_id'];
                }
            }
            
            $pet_allergy_rel = $this->Pet_allergy_model->get_allergy(element('pet_id',$getdata));       

            if ($pet_allergy_rel) {
                foreach ($pet_allergy_rel as $akey => $aval) {
                    $getdata['pet_allergy_rel'][] = $aval['pag_id'];
                }
            }
        }
        

        
        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

         if ( ! function_exists('password_hash')) {
            $this->load->helper('password');
        }

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'mem_userid',
                'label' => '회원아이디',
                'rules' => 'trim|required|min_length[3]|max_length[50]|is_checked[member.mem_userid]',
            ),
            array(
                'field' => 'pet_name',
                'label' => '펫 이름',
                'rules' => 'trim|required|min_length[2]|max_length[20]',
            ),
            array(
                'field' => 'pet_birthday',
                'label' => '펫 생일',
                'rules' => 'trim|required|exact_length[10]',
            ),
            array(
                'field' => 'pet_sex',
                'label' => '성별',
                'rules' => 'trim|exact_length[1]',
            ),
            array(
                'field' => 'pet_profile_content',
                'label' => '펫 자기소개',
                'rules' => 'trim',
            ),
            
            
        );
        
        $this->form_validation->set_rules($config);
        $form_validation = $this->form_validation->run();
        $file_error = '';
        $updatephoto = '';
        $file_error2 = '';
        $updateicon = '';

        if ($form_validation) {
            $this->load->library('upload');
            $this->load->library('aws_s3');
            if (isset($_FILES) && isset($_FILES['pet_photo']) && isset($_FILES['pet_photo']['name']) && $_FILES['pet_photo']['name']) {
                $upload_path = config_item('uploads_dir') . '/member_photo/';
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
                // $uploadconfig['max_width'] = '2000';
                // $uploadconfig['max_height'] = '1000';
                $uploadconfig['encrypt_name'] = true;

                $this->upload->initialize($uploadconfig);

                if ($this->upload->do_upload('pet_photo')) {
                    $img = $this->upload->data();
                    $updatephoto = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);

                    $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);
                } else {
                    $file_error = $this->upload->display_errors();

                }
            }

            if (isset($_FILES)
                && isset($_FILES['pet_backgroundimg'])
                && isset($_FILES['pet_backgroundimg']['name'])
                && $_FILES['pet_backgroundimg']['name']) {
                $upload_path = config_item('uploads_dir') . '/member_icon/';
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
                // $uploadconfig['max_width'] = '2000';
                // $uploadconfig['max_height'] = '1000';
                $uploadconfig['encrypt_name'] = true;

                $this->upload->initialize($uploadconfig);

                if ($this->upload->do_upload('pet_backgroundimg')) {
                    $img = $this->upload->data();
                    $updateicon = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);
                    $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);
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

            $view['view']['data'] = $getdata;

            $pet_attr = $this->Pet_attr_model->get_all_attr();
            
            

            
            $view['view']['config']['pet_form'] = element(2,$pet_attr);
            $view['view']['config']['pet_kind'] = element(0,$this->Cmall_kind_model->get_all_kind());
            $view['view']['config']['pet_attr'] = element(1,$pet_attr);
            $view['view']['config']['pet_age'] = element(3,$pet_attr);;
            
            $view['view']['config']['pet_allergy_rel'] = $this->Pet_allergy_model->get_all_allergy();

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;

            $html_content = '';
            $k = 0;
            

            $view['view']['html_content'] = $html_content;

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
            $mem_id = element('mem_id',$this->Member_model->get_by_userid($this->input->post('mem_userid'),'mem_id'));
            $pet_sex = $this->input->post('pet_sex') ? $this->input->post('pet_sex') : 0;
            $pet_neutral = $this->input->post('pet_neutral') ? $this->input->post('pet_neutral') : 0;
            $pet_weight = $this->input->post('pet_weight') ? $this->input->post('pet_weight') : 0;
            $pet_attr = $this->input->post('pet_attr', null, '');
            $pat_id = $this->input->post('pat_id') ? $this->input->post('pat_id') : 0;
            $pet_is_allergy = $this->input->post('pet_is_allergy') ? $this->input->post('pet_is_allergy') : 0;            
            $pet_allergy_rel = $this->input->post('pet_allergy_rel', null, '');

            

            if($this->input->post('pet_kind_text',null,'')){
                $this->db->select('ckd_id');            
                $this->db->from('cmall_kind');
                $this->db->where('ckd_value_kr', $this->input->post('pet_kind_text',null,''));
                $this->db->or_where('ckd_value_en', $this->input->post('pet_kind_text',null,''));
                $result = $this->db->get();
                $cmall_kind = $result->row_array();
            }

            $ckd_id = empty($cmall_kind) ? 0 : element('ckd_id',$cmall_kind);

            $updatedata = array(
                'mem_id' => $mem_id,
                'pet_name' => $this->input->post('pet_name', null, ''),
                'pet_birthday' => $this->input->post('pet_birthday', null, ''),
                'pet_sex' => $pet_sex,
                'pet_neutral' => $pet_neutral,
                'pet_weight' => $pet_weight,                
                'pet_is_allergy' => $pet_is_allergy,
                'pat_id' => $pat_id,
                'ckd_id' => $ckd_id,
                
            );

            
            
            $metadata = array();

           
            if (element('pet_nickname', $getdata) !== $this->input->post('pet_nickname')) {
                $updatedata['pet_nickname'] = $this->input->post('pet_nickname', null, '');
                $metadata['meta_nickname_datetime'] = cdate('Y-m-d H:i:s');
            }
            

            if ($this->input->post('pet_photo_del')) {
                $updatedata['pet_photo'] = '';
            } 

            if ($updatephoto) {
                $updatedata['pet_photo'] = $updatephoto;
            }
            if (element('pet_photo', $getdata) && ($this->input->post('pet_photo_del') OR $updatephoto)) {
                // 기존 파일 삭제
                @unlink(config_item('uploads_dir') . '/member_photo/' . element('pet_photo', $getdata));
                $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/member_photo/' . element('pet_photo', $getdata));
            }
            if ($this->input->post('pet_backgroundimg_del')) {
                $updatedata['pet_backgroundimg'] = '';
            } elseif ($updateicon) {
                $updatedata['pet_backgroundimg'] = $updateicon;
            }
            if (element('pet_backgroundimg', $getdata) && ($this->input->post('pet_backgroundimg_del') OR $updateicon)) {
                // 기존 파일 삭제
                @unlink(config_item('uploads_dir') . '/member_icon/' . element('pet_backgroundimg', $getdata));
                $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/member_icon/' . element('pet_backgroundimg', $getdata));
            }

            /**
             * 게시물을 수정하는 경우입니다
             */
            if ($this->input->post($primary_key)) {
                $pet_id = $this->input->post($primary_key);
                $this->{$this->modelname}->update($pet_id, $updatedata);
                
                $this->Pet_allergy_rel_model->save_attr($pet_id, $pet_allergy_rel);
                $this->Pet_attr_rel_model->save_attr($pet_id, $pet_attr);

                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['pet_register_datetime'] = cdate('Y-m-d H:i:s');

                $pet_id = $this->{$this->modelname}->insert($updatedata);

                $this->Pet_allergy_rel_model->save_attr($pet_id, $pet_allergy_rel);
                $this->Pet_attr_rel_model->save_attr($pet_id, $pet_attr);

                $this->session->set_flashdata(
                    'message',
                    '정상적으로 입력되었습니다'
                );
            }

            if($pet_id && $this->input->post('pet_main', null, '')){

                $petdata = $this->{$this->modelname}->get_one($pet_id);

                $this->{$this->modelname}->update('',array('pet_main' => 0),array('mem_id' => element('mem_id',$petdata)));

                $this->{$this->modelname}->update($pet_id,array('pet_main' => 1));
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
     * 엑셀로 데이터를 추출합니다.
     */
    public function excel()
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_member_memberpet_excel';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $findex = $this->input->get('findex', null, 'pet_name');
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('pet_name','member.mem_userid', 'member.mem_nickname'); // 검색이 가능한 필드
        
        $this->{$this->modelname}->allow_order_field = array('pet_name','member.mem_userid', 'member.mem_nickname'); // 정렬이 가능한 필드

        
        $result = $this->{$this->modelname}
            ->get_admin_list('', '', $where, '', $findex, $forder, $sfield, $skeyword);

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $where = array(
                    'pet_id' => element('pet_id', $val),
                );
                
                $result['list'][$key]['display_name'] = display_username(
                    element('mem_nickname', $val)
                );
                $result['list'][$key]['display_pet_name'] = display_username(
                    element('pet_name', $val)
                );


            }
        }

        $view['view']['data'] = $result;
        $view['view']['all_group'] = $this->Member_group_model->get_all_group();

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $this->{$this->modelname}->primary_key;


        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=펫정보_' . cdate('Y_m_d') . '.xls');
        echo $this->load->view('admin/' . ADMIN_SKIN . '/' . $this->pagedir . '/excel', $view, true);
    }

    /**
     * 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
     */
    public function listdelete()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_member_memberpet_listdelete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $this->member->delete_pet($val);
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
