<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kinditemgroup class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>페이지설정>EVENT관리 controller 입니다.
 */
class Kinditemgroup extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'page/kinditemgroup';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Kinditem_group','Kinditem','Kinditem_rel','Cmall_kind','Cmall_item');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Kinditem_group_model';

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
        $eventname = 'event_admin_page_kinditemgroup_index';
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
            'ckd_value_kr' => $param->sort('ckd_value_kr', 'asc'),
            'ckd_value_en' => $param->sort('ckd_value_en', 'asc'),            
            'ckd_size' => $param->sort('ckd_size', 'asc'),
            'kinditem_count' => $param->sort('kinditem_count', 'asc'),
        );


        $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->{$this->modelname}->primary_key;
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        $where = array();
        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('ckd_value_kr', 'ckd_value_en'); // 검색이 가능한 필드
        // $this->{$this->modelname}->search_field_equal = array('ckd_value_kr', 'ckd_value_en'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->{$this->modelname}->allow_order_field = array('ckd_value_kr', 'ckd_value_en',  'ckd_size',  'kinditem_count'); // 정렬이 가능한 필드
        $result = $this->{$this->modelname}
            ->get_admin_list('','', $where, '', $findex, $forder, $sfield, $skeyword);

        $list_num = count($result['list']);
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                

                $countwhere = array(
                    'kir_id' => element('kir_id', $val),
                );

                // $result['list'][$key]['Kinditem_count'] = $this->Kinditem_rel_model->count_by($countwhere);
                $result['list'][$key]['num'] = $list_num--;
            }
        }

        $view['view']['data'] = $result;

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $this->{$this->modelname}->primary_key;

        
        $config['base_url'] = admin_url($this->pagedir) . '?' . $param->replace('page');
        $config['total_rows'] = count($result['list']);
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $search_option = array('ckd_value_kr' => '견종명', 'ckd_value_en' => '견종명(영문)');
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

    public function lists($kig_id)
    {


        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_kinditem_index';
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
            'cit_name' => $param->sort('cit_name', 'asc'),            
            'kir_order' => $param->sort('(0.1/kir_order)', 'asc'),            
            'kir_start_date' => $param->sort('kir_start_date', 'asc'),
            'kir_end_date' => $param->sort('kir_end_date', 'asc'),
        );
        $findex = $this->input->get('findex', null, '(0.1/kir_order)');
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');
        

        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        $where = array();
        if ($kig_id) {
            $where = array('kinditem_group.kig_id' => $kig_id);
        }

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('cit_name'); // 검색이 가능한 필드
        // $this->{$this->modelname}->search_field_equal = array('kir_id', 'kig_id', 'kinditem.mem_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->{$this->modelname}->allow_order_field = array('kir_order', 'kir_start_date', 'kir_end_date'); // 정렬이 가능한 필드
        $result = $this->{$this->modelname}
            ->get_item_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);

        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                // $result['list'][$key]['kinditemgroup'] = $this->kinditem_group_model->get_one(element('kig_id', $val));
                $result['list'][$key]['num'] = $list_num--;
            }
        }

        $view['view']['data'] = $result;


        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = 'kir_id';
        $view['view']['kig_id'] = $kig_id;

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
        $search_option = array('cit_name' => '상풍명');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = admin_url('page/kinditemgroup');
        $view['view']['write_url'] = admin_url($this->pagedir . '/write?kig_id=' . $kig_id);
        $view['view']['list_delete_url'] = admin_url($this->pagedir . '/itemlistdelete/'.$kig_id.'?' . $param->output());

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'lists');
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
        $eventname = 'event_admin_page_kinditemgroup_write';
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
                'field' => 'ckd_value_kr',
                'label' => '견종명',
                'rules' => 'trim|required|callback__ckd_value_kr_check',
            ),            
        );
        

        $this->form_validation->set_rules($config);
        $form_validation = $this->form_validation->run();
        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($form_validation === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

            
            $view['view']['data']['all_kind'] = element(0,$this->Cmall_kind_model->get_all_kind());

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

            if($this->input->post('ckd_value_kr',null,'')){
                $this->db->select('ckd_id');            
                $this->db->from('cmall_kind');
                $this->db->where('ckd_value_kr', $this->input->post('ckd_value_kr',null,''));
                $result = $this->db->get();
                $cmall_kind = $result->row_array();
            }

            $ckd_id = empty($cmall_kind) ? 0 : element('ckd_id',$cmall_kind);

            

            $updatedata = array(
                'ckd_id' => $ckd_id,                
            );

            

            /**
             * 게시물을 수정하는 경우입니다
             */
            if ($this->input->post($primary_key)) {

                $this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);
                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                $updatedata['kig_datetime'] = cdate('Y-m-d H:i:s');
                
                $kig_id = $this->{$this->modelname}->insert($updatedata);

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
        $eventname = 'event_admin_page_kinditemgroup_listdelete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $this->{$this->modelname}->delete($val);
                    $deletewhere = array(
                        'kig_id' => $val,
                    );
                    $this->Kinditem_rel_model->delete_where($deletewhere);
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

    
    public function listswrite($pid = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_kdi_write';
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
        

        
        $primary_key = $this->Kinditem_rel_model->primary_key;

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
        if ($pid) {

            $getdata = $this->Kinditem_rel_model->get_one($pid);
            $getdata2 = $this->Cmall_item_model->get_one(element('cit_id',$getdata));
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
                'field' => 'kir_id',
                'label' => 'item 키',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'cit_id',
                'label' => '상품 아이디',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'kir_order',
                'label' => '정렬순서',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'kir_start_date',
                'label' => '시작일',
                'rules' => 'trim',
            ),
            array(
                'field' => 'kir_end_date',
                'label' => '종료일',
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

            if ($pid) {
                $view['view']['data'] = array_merge($getdata,$getdata2);
            }
            
            // $view['view']['data']['kinditemgroup'] = $this->Kinditem_group_model->get_one($kig_id);

            /**
             * primary key 정보를 저장합니다
             */
            $view['view']['primary_key'] = $primary_key;

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

            /**
             * 어드민 레이아웃을 정의합니다
             */
            $layoutconfig = array('layout' => 'layout', 'skin' => 'listswrite');
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

            
            $kir_order = $this->input->post('kir_order') ? $this->input->post('kir_order') : 0;

            $updatedata = array(                
                'cit_id' => $this->input->post('cit_id', null, ''),
                'kir_order' => $this->input->post('kir_order', null, ''),
                'kir_start_date' => $this->input->post('kir_start_date', null, ''),
                'kir_end_date' => $this->input->post('kir_end_date', null, ''),
                
            );

            /**
             * 게시물을 수정하는 경우입니다
             */
            if ($this->input->post($primary_key)) {
                $this->Kinditem_rel_model->update($this->input->post($primary_key), $updatedata);
                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );
            } else {
                /**
                 * 게시물을 새로 입력하는 경우입니다
                 */
                
                $this->Kinditem_rel_model->insert($updatedata);
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
            $redirecturl = admin_url($this->pagedir . '/lists/'.element('kig_id',$getdata).'?' . $param->output());
            redirect($redirecturl);
        }
    }

    public function itemlistdelete($kig_id)
    {   

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_kinditemgroup_listdelete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $deletewhere = array(
                        'kir_id' => $val,
                    );
                    $this->Kinditem_rel_model->delete_where($deletewhere);
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
        $redirecturl = admin_url($this->pagedir . '/lists/'.$kig_id.'?' . $param->output());
        redirect($redirecturl);
    }

    public function _ckd_value_kr_check($str)
    {
        
        $this->db->select('ckd_id');            
        $this->db->from('cmall_kind');
        $this->db->where('ckd_value_kr', $str);
        $result = $this->db->get();
        $cmall_kind = $result->row_array();
        

        $ckd_id = empty($cmall_kind) ? 0 : element('ckd_id',$cmall_kind);

        if(!$ckd_id){
            $this->form_validation->set_message(
                '_ckd_value_kr_check',
                $str . ' 은(는) 없는 견종입니다.'
            );
            return false;
        }

        if($this->Kinditem_group_model->count_by(array('ckd_id' => $ckd_id))){
            $this->form_validation->set_message(
                '_ckd_value_kr_check',
                $str . ' 은(는) 이미 등록된 견종입니다.'
            );
            return false;
        }
        return true;
    }
}
