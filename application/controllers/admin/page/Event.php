<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Event class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>페이지설정>EVENT관리 controller 입니다.
 */
class Event extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'page/event';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Event', 'Event_group','Event_rel','Cmall_item','Cmall_category','Board');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Event_model';

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
     * 목록을 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_page_event_index';
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
            'eve_id' => $param->sort('eve_id', 'asc'),
            'eve_title' => $param->sort('eve_title', 'asc'),
            'eve_order' => $param->sort('eve_order', 'asc'),
            'eve_datetime' => $param->sort('eve_datetime', 'asc'),
        );
        $findex = $this->input->get('findex', null, 'eve_order');
        $forder = $this->input->get('forder', null, 'asc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');
        $egr_id = $this->input->get('egr_id', null, '');

        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        $where = array();
        if ($egr_id) {
            $where = array('egr_id' => $egr_id);
        }

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('eve_id', 'egr_id', 'eve_title', 'eve_content', 'eve_mobile_content', 'eve_datetime', 'event.mem_id'); // 검색이 가능한 필드
        $this->{$this->modelname}->search_field_equal = array('eve_id', 'egr_id', 'event.mem_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->{$this->modelname}->allow_order_field = array('eve_id', 'eve_title', 'eve_order', 'eve_datetime'); // 정렬이 가능한 필드
        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['display_name'] = display_username(
                element('mem_userid', $val),
                    element('mem_nickname', $val),
                    element('mem_icon', $val)
                );
                $result['list'][$key]['eventgroup'] = $this->Event_group_model->get_one(element('egr_id', $val));

                $countwhere = array(
                    'eve_id' => element('eve_id', $val),
                );

                $result['list'][$key]['eventcount'] = $this->Event_rel_model->count_by($countwhere);
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
        $search_option = array('eve_title' => '제목', 'eve_content' => '내용', 'eve_mobile_content' => '모바일내용', 'eve_datetime' => '날짜');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = admin_url('page/eventgroup');
        $view['view']['write_url'] = admin_url($this->pagedir . '/write?egr_id=' . $egr_id);
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
        $eventname = 'event_admin_page_eve_write';
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

        $egr_id = (int) $this->input->get('egr_id');
        if (empty($egr_id) OR $egr_id < 1) {
            show_404();
        }
        $primary_key = $this->{$this->modelname}->primary_key;

        /**
         * 수정 페이지일 경우 기존 데이터를 가져옵니다
         */
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
                'field' => 'egr_id',
                'label' => 'EVENT그룹',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'eve_order',
                'label' => '정렬순서',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'eve_title',
                'label' => '색션제목',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'eve_start_date',
                'label' => '이벤트시작일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'eve_end_date',
                'label' => '이벤트종료일',
                'rules' => 'trim|alpha_dash|exact_length[10]',
            ),
            array(
                'field' => 'eve_activated',
                'label' => '이벤트활성화',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'eve_content',
                'label' => '내용',
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
                $view['view']['data'] = $getdata;
            }
            $view['view']['data']['eventgroup'] = $this->Event_group_model->get_one($egr_id);

            $param =& $this->querystring;
            $findex = $this->input->get('findex') ? $this->input->get('findex') : 'cit_id';
            $forder = $this->input->get('forder', null, 'desc');
            $sfield = $this->input->get('sfield', null, '');
            $skeyword = $this->input->get('skeyword', null, '');

            $event_rel = $where_in = array();
            $event_rel = $this->Event_model->get_event($pid);
            if($event_rel)
            foreach($event_rel as $eveval)
                array_push($where_in,element('cit_id',$eveval));

            $this->Board_model->set_where_in('cit_id',$where_in);
            $this->Board_model->allow_search_field = array('cit_goods_code', 'cit_key', 'cit_name', 'cit_datetime', 'cit_updated_datetime', 'cit_content', 'cit_mobile_content', 'cit_price'); // 검색이 가능한 필드
            $this->Board_model->search_field_equal = array('cit_goods_code', 'cit_price'); // 검색중 like 가 아닌 = 검색을 하는 필드
            $this->Board_model->allow_order_field = array('cit_id', 'cit_key', 'cit_price_sale', 'cit_name', 'cit_datetime', 'cit_updated_datetime', 'cit_hit', 'cit_sell_count', 'cit_price'); // 정렬이 가능한 필드
            if(empty($where_in))
                $cresult = array();
            else 
                $cresult = $this->Board_model->get_item_list('','', '', '', $findex, $forder, $sfield, $skeyword);

            $list_num = element('total_rows', $cresult) ? element('total_rows', $cresult) : 0;
            if (element('list', $cresult)) {
            foreach (element('list', $cresult) as $key => $val) {
                // $result['list'][$key]['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $val));
                $cresult['list'][$key]['category'] = $this->Cmall_category_model->get_category(element('cit_id', $val));
                // $result['list'][$key]['attr'] = $this->Cmall_attr_model->get_attr(element('cit_id', $val));
                
                
                $cmall_wishlist_where = array(
                    'cit_id' => element('cit_id', $val),
                    
                );
                

                

                if(empty(element('cit_name', $val)) || empty(element('cit_price', $val)) || empty(element('cit_post_url', $val)) || empty(element('cit_goods_code', $val)) || empty(element('cbr_id', $val)))
                    $cresult['list'][$key]['warning'] = 1 ; 
                else 
                    $cresult['list'][$key]['warning'] = '' ; 


                $cresult['list'][$key]['display_tag'] = '';
                $crawlwhere = array(
                    'cit_id' => element('cit_id', $val),
                );
                

                $cresult['list'][$key]['display_label'] = '';
                $crawlwhere = array(
                    'cit_id' => element('cit_id', $val),
                );
                

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

            $view['view']['list_delete_url'] = admin_url($this->pagedir.'/event_in_listdelete/'.$pid.'?' . $param->output());
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

            $eve_start_date = $this->input->post('eve_start_date') ? $this->input->post('eve_start_date') : null;
            $eve_end_date = $this->input->post('eve_end_date') ? $this->input->post('eve_end_date') : null;            
            $eve_activated = $this->input->post('eve_activated') ? $this->input->post('eve_activated') : 0;
            $eve_order = $this->input->post('eve_order') ? $this->input->post('eve_order') : 0;
            $eve_content = $this->input->post('eve_content') ? $this->input->post('eve_content') : '';

            

            $updatedata = array(
                'egr_id' => $this->input->post('egr_id', null, 0),
                'eve_title' => $this->input->post('eve_title', null, ''),
                'eve_start_date' => $eve_start_date,
                'eve_end_date' => $eve_end_date,
                'eve_activated' => $eve_activated,
                'eve_order' => $eve_order,
                'eve_content' => $eve_content,
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
                $updatedata['eve_datetime'] = cdate('Y-m-d H:i:s');
                $updatedata['eve_ip'] = $this->input->ip_address();
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
        $eventname = 'event_admin_page_eve_listdelete';
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

                    $this->Event_rel_model->delete_where(array('eve_id' => $val));
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

    public function event_in_listdelete($eve_id)
    {
        
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallitem_listupdate';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        if (empty($eve_id)) {
            show_404();
        }
        /**
         * 체크한 게시물의 업데이트를 실행합니다
         */
        
        $this->load->model(array('Event_rel_model'));

        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            
            $this->Event_rel_model->delete_event($eve_id, $this->input->post('chk'));    
            
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
        $redirecturl = admin_url($this->pagedir.'/write/' . $eve_id. '?' . $param->output());

        redirect($redirecturl);
    }
}
