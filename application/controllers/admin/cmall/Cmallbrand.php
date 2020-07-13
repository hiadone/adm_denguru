<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmallbrand class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>컨텐츠몰관리>카테고리관리 controller 입니다.
 */
class Cmallbrand extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'cmall/cmallbrand';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Cmall_brand');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Cmall_brand_model';

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'cmall');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('querystring', 'cmalllib'));
    }

    /**
     * 카테고리관리를 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallbrand_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $primary_key = $this->{$this->modelname}->primary_key;

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        $config = array();
        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        if ($this->input->post('type') === 'add') {
            $config = array(
                array(
                    'field' => 'cbr_value_kr',
                    'label' => '한글 브랜드명',
                    'rules' => 'trim|is_unique[cmall_brand.cbr_value_kr]',
                ),
                array(
                    'field' => 'cbr_value_en',
                    'label' => '영문 브랜드명 ',
                    'rules' => 'trim|is_unique[cmall_brand.cbr_value_en]',
                ),
            );
        } elseif ($this->input->post('type') === 'modify') {
            $config = array(
                array(
                    'field' => 'cbr_id',
                    'label' => '브랜드아이디',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'cbr_value_kr',
                    'label' => '한글 브랜드명',
                    'rules' => 'trim|is_unique[cmall_brand.cbr_value_kr.cbr_id.' . $this->input->post('cbr_id') . ']',
                ),
                array(
                    'field' => 'cbr_value_en',
                    'label' => '영문 브랜드명 ',
                    'rules' => 'trim|is_unique[cmall_brand.cbr_value_en.cbr_id.' . $this->input->post('cbr_id') . ']',
                ),
            );
            
        }
        $this->form_validation->set_rules($config);


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($this->form_validation->run() === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            if ($this->input->post('type') === 'add') {

                
                $insertdata = array(
                    'cbr_value_kr' => $this->input->post('cbr_value_kr', null, ''),
                    'cbr_value_en' => $this->input->post('cbr_value_en', null, ''),
                    
                );
                $this->Cmall_brand_model->insert($insertdata);
                $this->cache->delete('cmall-brand-all');
                $this->cache->delete('cmall-brand-detail');

                $this->session->set_flashdata(
                    'message',
                    '정상적으로 저장되었습니다'
                );
                redirect(admin_url($this->pagedir), 'refresh');

            }
            if ($this->input->post('type') === 'modify') {
                $updatedata = array(
                    'cbr_value_kr' => $this->input->post('cbr_value_kr', null, ''),
                    'cbr_value_en' => $this->input->post('cbr_value_en', null, ''),
                );
                $this->Cmall_brand_model->update($this->input->post('cbr_id'), $updatedata);
                $this->cache->delete('cmall-brand-all');
                $this->cache->delete('cmall-brand-detail');

                

                $this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );

                redirect(admin_url($this->pagedir), 'refresh');

            }
        }

        $getdata = $this->Cmall_brand_model->get_all_brand();
        $view['view']['data'] = $getdata;

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $primary_key;

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
     * 카테고리 삭제
     */
    public function delete($cbr_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallbrand_delete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        $cbr_id = (int) $cbr_id;
        if (empty($cbr_id) OR $cbr_id < 1) {
            show_404();
        }

        $this->Cmall_brand_model->delete($cbr_id);
        $this->cache->delete('cmall-brand-all');
        $this->cache->delete('cmall-brand-detail');

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
