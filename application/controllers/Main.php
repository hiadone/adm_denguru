<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Main class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 메인 페이지를 담당하는 controller 입니다.
 */


class Main extends CB_Controller
{

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array('Board','Cmall_item','Post','Cmall_category');

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
		$this->load->library(array('querystring'));
	}


	/**
	 * 전체 메인 페이지입니다
	 */
	public function index()
	{


		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_main_index';
		$this->load->event($eventname);

		

		
		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);


		$set_where = "(cit_name = '' OR (cit_price = 0 and cit_is_soldout =0 ) OR cit_post_url = '' OR cit_goods_code = '' OR cit_file_1 = '' OR cb_cmall_item.cbr_id = 0)";

		$view['view']['warning_count'] = $this->Cmall_item_model->total_count_by('','brd_id',$set_where);

		
		$view['view']['cmall_count'] = $this->Cmall_item_model->total_count_by('','brd_id');



		


		// if(!empty($view['view']['cmall_count'] - $a_t))
		$view['view']['notcategory_count'] = $this->Cmall_category_model->get_brdcategory();


		$where = array(
			'brd_search' => 1,
		);
		$board_id = $this->Board_model->get_board_list($where);
		$board_list = array();
		if ($board_id && is_array($board_id)) {
			foreach ($board_id as $key => $val) {
				if($this->input->get('warning')){

					foreach ($view['view']['warning_count'] as $wval) 
					{	

						if(element('brd_id', $val) == element('brd_id',$wval)){		

							$board_list[] = $this->board->item_all(element('brd_id', $wval));
							break;
						}
						
					}

					
				}
				elseif($this->input->get('notcategory')){
					$nocate_flag=false;
					foreach ($view['view']['notcategory_count'] as $nval) 
					{	

						if( element('brd_id', $val) == element('brd_id',$nval)){			
							foreach($view['view']['cmall_count'] as $cval){
								if( element('brd_id', $cval) == element('brd_id',$nval)){			

									if(element('rownum',$cval) - element('cnt',$nval)){
										$nocate_flag= true;
										
										break;
									}
								}
							}
							
						}

						if($nocate_flag){
							$board_list[] = $this->board->item_all(element('brd_id', $val));
							break;
						}
						
					}
					
				}
				else
					$board_list[] = $this->board->item_all(element('brd_id', $val));

				$search_list[] = $this->board->item_all(element('brd_id', $val));
			}
		}

		$view['view']['search_list'] = $search_list;
		$view['view']['board_list'] = $board_list;
		$view['view']['canonical'] = site_url();

		// $or_where = array(
		// 		'cit_name' => '',
		// 		'cit_price' => 0,
		// 		'cit_post_url' => '',
		// 		'cit_goods_code' => '',
		// 		'cit_file_1' => '',
		// 		'cbr_id' => 0,
		// );
			
		
		
		


		// $view['view']['notcategory_count'] = $this->Post_model->total_count_by(array('post_category' => 0));

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);
		
		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = $this->cbconfig->item('site_meta_title_main');
		$meta_description = $this->cbconfig->item('site_meta_description_main');
		$meta_keywords = $this->cbconfig->item('site_meta_keywords_main');
		$meta_author = $this->cbconfig->item('site_meta_author_main');
		$page_name = $this->cbconfig->item('site_page_name_main');
		
		$layoutconfig = array(
			'path' => 'main',
			'layout' => 'layout',
			'skin' => 'main',
			'layout_dir' => $this->cbconfig->item('layout_main'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_main'),
			'use_sidebar' => $this->cbconfig->item('sidebar_main'),
			'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_main'),
			'skin_dir' => $this->cbconfig->item('skin_main'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_main'),
			'page_title' => $page_title,
			'meta_description' => $meta_description,
			'meta_keywords' => $meta_keywords,
			'meta_author' => $meta_author,
			'page_name' => $page_name,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}
	
}


