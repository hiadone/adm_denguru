<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmallcategory class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>컨텐츠몰관리>카테고리관리 controller 입니다.
 */
class Cmallcategory extends CB_Controller
{

	/**
	 * 관리자 페이지 상의 현재 디렉토리입니다
	 * 페이지 이동시 필요한 정보입니다
	 */
	public $pagedir = 'cmall/cmallcategory';

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array('Cmall_category','Cmall_attr','Cmall_kind');

	/**
	 * 이 컨트롤러의 메인 모델 이름입니다
	 */
	protected $modelname = 'Cmall_category_model';

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
		$eventname = 'event_admin_cmall_cmallcategory_index';
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

		/**
		 * 전송된 데이터의 유효성을 체크합니다
		 */
		if ($this->input->post('type') === 'add') {
			$config = array(
				array(
					'field' => 'cca_parent',
					'label' => '상위카테고리',
					'rules' => 'trim',
				),
				array(
					'field' => 'cca_value',
					'label' => '카테고리명',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'cca_order',
					'label' => '정렬순서',
					'rules' => 'trim|numeric|is_natural',
				),
				array(
					'field' => 'cca_text',
					'label' => '카테고리사전',
					'rules' => 'trim',
				),
			);
		} else {
			$config = array(
				array(
					'field' => 'cca_id',
					'label' => '카테고리아이디',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'cca_value',
					'label' => '카테고리명',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'cca_order',
					'label' => '정렬순서',
					'rules' => 'trim|numeric|is_natural',
				),
				array(
					'field' => 'cca_text',
					'label' => '카테고리사전',
					'rules' => 'trim',
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

				$cca_text_arr = array();
				$cca_text_ = '';
				$cca_order = $this->input->post('cca_order') ? $this->input->post('cca_order') : 0;
				$cca_text = $this->input->post('cca_text') ? $this->input->post('cca_text') : '';
				$cca_text = str_replace("\n",",",$cca_text);

				$cca_text_arr = explode(",",urldecode($cca_text));

                

                if(count($cca_text_arr)){
                    
                    if ($cca_text_arr && is_array($cca_text_arr)) {
                        $text_array=array();
                        foreach ($cca_text_arr as  $value) {
                            $value = trim($value);
                            if ($value) 
                            	array_push($text_array,$value);
                        }
                    }
                    if($text_array && is_array($text_array)) $cca_text_ = implode(",",$text_array);
                }

				$insertdata = array(
					'cca_value' => $this->input->post('cca_value', null, ''),
					'cca_parent' => $this->input->post('cca_parent', null, 0),
					'cca_order' => $cca_order,
					'cca_text' => $cca_text_,
				);
				$this->Cmall_category_model->insert($insertdata);
				$this->cache->delete('cmall-category-all');
				$this->cache->delete('cmall-category-detail');

				

				$this->session->set_flashdata(
                    'message',
                    '정상적으로 저장되었습니다'
                );
                
				redirect(admin_url($this->pagedir), 'refresh');

			}
			if ($this->input->post('type') === 'modify') {
				$cca_text_arr = array();
				$cca_text_ = '';
				$cca_order = $this->input->post('cca_order') ? $this->input->post('cca_order') : 0;
				$cca_text = $this->input->post('cca_text') ? $this->input->post('cca_text') : '';
				$cca_text = str_replace("\n",",",$cca_text);

				$cca_text_arr = explode(",",urldecode($cca_text));

                

                if(count($cca_text_arr)){
                    
                    if ($cca_text_arr && is_array($cca_text_arr)) {
                        $text_array=array();
                        foreach ($cca_text_arr as  $value) {
                            $value = trim($value);
                            if ($value) 
                            	array_push($text_array,$value);
                        }
                    }
                    if($text_array && is_array($text_array)) $cca_text_ = implode(",",$text_array);
                }

				$updatedata = array(
					'cca_value' => $this->input->post('cca_value', null, ''),
					'cca_order' => $cca_order,
					'cca_text' => $cca_text_,
				);
				$this->Cmall_category_model->update($this->input->post('cca_id'), $updatedata);
				$this->cache->delete('cmall-category-all');
				$this->cache->delete('cmall-category-detail');

				

				$this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );

				redirect(admin_url($this->pagedir), 'refresh');

			}
		}

		$getdata = $this->Cmall_category_model->get_all_category();
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
	 * 제품특성관리를 가져오는 메소드입니다
	 */
	public function attr()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_cmallattr_attr';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$primary_key = $this->Cmall_attr_model->primary_key;

		/**
		 * Validation 라이브러리를 가져옵니다
		 */
		$this->load->library('form_validation');

		/**
		 * 전송된 데이터의 유효성을 체크합니다
		 */
		if ($this->input->post('type') === 'add') {
			$config = array(
				array(
					'field' => 'cat_parent',
					'label' => '상위제품특성',
					'rules' => 'trim',
				),
				array(
					'field' => 'cat_value',
					'label' => '제품특성명',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'cat_text',
					'label' => '제품특성사전',
					'rules' => 'trim',
				),
				// array(
				// 	'field' => 'cat_order',
				// 	'label' => '정렬순서',
				// 	'rules' => 'trim|numeric|is_natural',
				// ),
			);
		} else {
			$config = array(
				array(
					'field' => 'cat_id',
					'label' => '제품특성아이디',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'cat_value',
					'label' => '제품특성명',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'cat_text',
					'label' => '제품특성사전',
					'rules' => 'trim',
				),
				// array(
				// 	'field' => 'cat_order',
				// 	'label' => '정렬순서',
				// 	'rules' => 'trim|numeric|is_natural',
				// ),
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

				$cat_text_arr = array();
				$cat_text_ = '';				
				$cat_order = $this->input->post('cat_order') ? $this->input->post('cat_order') : 0;
				$cat_text = $this->input->post('cat_text') ? $this->input->post('cat_text') : '';
				$cat_text = str_replace("\n",",",$cat_text);

				$cat_text_arr = explode(",",urldecode($cat_text));

                

                if(count($cat_text_arr)){
                    
                    if ($cat_text_arr && is_array($cat_text_arr)) {
                        $text_array=array();
                        foreach ($cat_text_arr as  $value) {
                            $value = trim($value);
                            if ($value) 
                            	array_push($text_array,$value);
                        }
                    }
                    if($text_array && is_array($text_array)) $cat_text_ = implode(",",$text_array);
                }

				$insertdata = array(
					'cat_value' => $this->input->post('cat_value', null, ''),
					'cat_parent' => $this->input->post('cat_parent', null, 0),
					'cat_text' => $cat_text_,
					'cat_order' => $cat_order,
				);
				$this->Cmall_attr_model->insert($insertdata);
				$this->cache->delete('cmall-attr-all');
				$this->cache->delete('cmall-attr-detail');

				

				$this->session->set_flashdata(
                    'message',
                    '정상적으로 저장되었습니다'
                );
                
				redirect(admin_url($this->pagedir.'/attr'), 'refresh');

			}
			if ($this->input->post('type') === 'modify') {

				$cat_text_arr = array();
				$cat_text_ = '';				
				$cat_order = $this->input->post('cat_order') ? $this->input->post('cat_order') : 0;
				$cat_text = $this->input->post('cat_text') ? $this->input->post('cat_text') : '';
				$cat_text = str_replace("\n",",",$cat_text);

				$cat_text_arr = explode(",",urldecode($cat_text));

                

                if(count($cat_text_arr)){
                    
                    if ($cat_text_arr && is_array($cat_text_arr)) {
                        $text_array=array();
                        foreach ($cat_text_arr as  $value) {
                            $value = trim($value);
                            if ($value) 
                            	array_push($text_array,$value);
                        }
                    }
                    if($text_array && is_array($text_array)) $cat_text_ = implode(",",$text_array);
                }

				$updatedata = array(
					'cat_value' => $this->input->post('cat_value', null, ''),
					'cat_text' => $cat_text_,
					'cat_order' => $cat_order,
				);
				$this->Cmall_attr_model->update($this->input->post('cat_id'), $updatedata);
				$this->cache->delete('cmall-attr-all');
				$this->cache->delete('cmall-attr-detail');

				

				$this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );

				redirect(admin_url($this->pagedir.'/attr'), 'refresh');

			}
		}

		$getdata = $this->Cmall_attr_model->get_all_attr();

		
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
		$layoutconfig = array('layout' => 'layout', 'skin' => 'attr');
		$view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}

	/**
	 * 카테고리 삭제
	 */
	public function delete($cca_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_cmallcategory_delete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		 * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		 */
		$cca_id = (int) $cca_id;
		if (empty($cca_id) OR $cca_id < 1) {
			show_404();
		}

		$this->Cmall_category_model->delete($cca_id);
		$this->cache->delete('cmall-category-all');
		$this->cache->delete('cmall-category-detail');

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

	/**
	 * 제품특성 삭제
	 */
	public function attr_delete($cat_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_cmallattr_delete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		 * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		 */
		$cat_id = (int) $cat_id;
		if (empty($cat_id) OR $cat_id < 1) {
			show_404();
		}

		$this->Cmall_attr_model->delete($cat_id);
		$this->cache->delete('cmall-attr-all');
		$this->cache->delete('cmall-attr-detail');

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
		$redirecturl = admin_url($this->pagedir . '/attr?' . $param->output());

		redirect($redirecturl);
	}


	public function kind()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_kind';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$primary_key = $this->Cmall_kind_model->primary_key;

		/**
		 * Validation 라이브러리를 가져옵니다
		 */
		$this->load->library('form_validation');

		/**
		 * 전송된 데이터의 유효성을 체크합니다
		 */
		if ($this->input->post('type') === 'add') {
			$config = array(
				array(
                    'field' => 'ckd_value_kr',
                    'label' => '한글 견종명',
                    'rules' => 'trim|required|is_unique[cmall_kind.ckd_value_kr]',
                ),
                array(
                    'field' => 'ckd_value_en',
                    'label' => '영문 견종명 ',
                    'rules' => 'trim|required|is_unique[cmall_kind.ckd_value_en]',
                ),
				array(
					'field' => 'ckd_text',
					'label' => '견종사전',
					'rules' => 'trim',
				),
				array(
					'field' => 'ckd_size',
					'label' => '견종크기',
					'rules' => 'trim|required',
				),
			);
		} else {
			$config = array(
				array(
					'field' => 'ckd_id',
					'label' => '견종아이디',
					'rules' => 'trim|required',
				),
				array(
                    'field' => 'ckd_value_kr',
                    'label' => '한글 브랜드명',
                    'rules' => 'trim|is_unique[cmall_kind.ckd_value_kr.ckd_id.' . $this->input->post('ckd_id') . ']',
                ),
                array(
                    'field' => 'ckd_value_en',
                    'label' => '영문 브랜드명 ',
                    'rules' => 'trim|is_unique[cmall_kind.ckd_value_en.ckd_id.' . $this->input->post('ckd_id') . ']',
                ),
				array(
					'field' => 'ckd_text',
					'label' => '견종사전',
					'rules' => 'trim',
				),
				array(
					'field' => 'ckd_size',
					'label' => '견종크기',
					'rules' => 'trim|required',
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

				$ckd_text_arr = array();
				$ckd_text_ = '';				
				$ckd_size = $this->input->post('ckd_size') ? $this->input->post('ckd_size') : '';

				if($ckd_size === "소형견") $ckd_size = 4;
				elseif($ckd_size === "중형견") $ckd_size = 5;
				elseif($ckd_size === "대형견") $ckd_size = 6;
				$ckd_text = $this->input->post('ckd_text') ? $this->input->post('ckd_text') : '';
				$ckd_text = str_replace("\n",",",$ckd_text);

				$ckd_text_arr = explode(",",urldecode($ckd_text));

                

                if(count($ckd_text_arr)){
                    
                    if ($ckd_text_arr && is_array($ckd_text_arr)) {
                        $text_array=array();
                        foreach ($ckd_text_arr as  $value) {
                            $value = trim($value);
                            if ($value) 
                            	array_push($text_array,$value);
                        }
                    }
                    if($text_array && is_array($text_array)) $ckd_text_ = implode(",",$text_array);
                }

				$insertdata = array(
					'ckd_value_kr' => $this->input->post('ckd_value_kr', null, ''),
                    'ckd_value_en' => $this->input->post('ckd_value_en', null, ''),
					'ckd_text' => $ckd_text_,
					'ckd_size' => $ckd_size,
				);
				$this->Cmall_kind_model->insert($insertdata);
				$this->cache->delete('cmall-kind-all');
				$this->cache->delete('cmall-kind-detail');

				

				$this->session->set_flashdata(
                    'message',
                    '정상적으로 저장되었습니다'
                );
                
				redirect(admin_url($this->pagedir.'/kind'), 'refresh');

			}
			if ($this->input->post('type') === 'modify') {

				$ckd_text_arr = array();
				$ckd_text_ = '';				
				$ckd_size = $this->input->post('ckd_size') ? $this->input->post('ckd_size') : '';

				if($ckd_size === "소형견") $ckd_size = 4;
				elseif($ckd_size === "중형견") $ckd_size = 5;
				elseif($ckd_size === "대형견") $ckd_size = 6;

				$ckd_text = $this->input->post('ckd_text') ? $this->input->post('ckd_text') : '';
				$ckd_text = str_replace("\n",",",$ckd_text);

				$ckd_text_arr = explode(",",urldecode($ckd_text));

                

                if(count($ckd_text_arr)){
                    
                    if ($ckd_text_arr && is_array($ckd_text_arr)) {
                        $text_array=array();
                        foreach ($ckd_text_arr as  $value) {
                            $value = trim($value);
                            if ($value) 
                            	array_push($text_array,$value);
                        }
                    }
                    if($text_array && is_array($text_array)) $ckd_text_ = implode(",",$text_array);
                }

				$updatedata = array(
					'ckd_value_kr' => $this->input->post('ckd_value_kr', null, ''),
                    'ckd_value_en' => $this->input->post('ckd_value_en', null, ''),
					'ckd_text' => $ckd_text_,
					'ckd_size' => $ckd_size,
				);
				$this->Cmall_kind_model->update($this->input->post('ckd_id'), $updatedata);
				$this->cache->delete('cmall-kind-all');
				$this->cache->delete('cmall-kind-detail');

				

				$this->session->set_flashdata(
                    'message',
                    '정상적으로 수정되었습니다'
                );

				redirect(admin_url($this->pagedir.'/kind'), 'refresh');

			}
		}

		$getdata = $this->Cmall_kind_model->get_all_kind();

		

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
		$layoutconfig = array('layout' => 'layout', 'skin' => 'kind');
		$view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}

	public function kind_delete($ckd_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_cmallkind_delete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		 * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		 */
		$ckd_id = (int) $ckd_id;
		if (empty($ckd_id) OR $ckd_id < 1) {
			show_404();
		}

		$this->Cmall_kind_model->delete($ckd_id);
		$this->cache->delete('cmall-kind-all');
		$this->cache->delete('cmall-kind-detail');

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
		$redirecturl = admin_url($this->pagedir . '/kind?' . $param->output());

		redirect($redirecturl);
	}
}
