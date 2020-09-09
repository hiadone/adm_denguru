<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helptool class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 각종 헬프페이지에 관련된 controller 입니다.
 */
class Helptool extends CB_Controller
{

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array('Post');

	/**
	 * 헬퍼를 로딩합니다
	 */
	protected $helpers = array('form', 'array', 'file', 'string');

	function __construct()
	{
		parent::__construct();

		/**
		 * 라이브러리를 로딩합니다
		 */
		$this->load->library(array('pagination', 'querystring'));
	}


	/**
	 * 이미지 크게 보기
	 */
	public function viewimage()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_viewimage';
		$this->load->event($eventname);

		$view = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$view['view']['imgurl'] = $this->input->get('imgurl', null, '');

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = '이미지 보기';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'viewimage',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 이모티콘 보기
	 */
	public function emoticon()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_emoticon';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);
		$view['view']['emoticon'] = get_filenames(config_item('uploads_dir') . '/emoticon');

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = '이모티콘';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'emoticon',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 특수문자 보기
	 */
	public function specialchars()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_specialchars';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();


		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$chars = "、 。 · ‥ … ¨ 〃 ― ∥ ＼ ∼ ‘ ’ “ ” 〔 〕 〈 〉 《 》 「 」 『 』 【 】 ± × ÷ ≠ ≤ ≥ ∞ ∴ ° ′ ″ ℃ Å ￠ ￡ ￥ ♂ ♀ ∠ ⊥ ⌒ ∂ ∇ ≡ ≒ § ※ ☆ ★ ○ ● ◎ ◇ ◆ □ ■ △ ▲ ▽ ▼ → ← ↑ ↓ ↔ 〓 ≪ ≫ √ ∽ ∝ ∵ ∫ ∬ ∈ ∋ ⊆ ⊇ ⊂ ⊃ ∩ ∧ ∨ ￢ ⇒ ⇔ ∀ ∃ ´ ～ ˇ ˘ ˝ ˚ ˙ ¸ ˛ ¡ ¿ ː ∮ ∑ ∏ ¤ ℉ ‰ ◁ ◀ ▷ ▶ ♤ ♠ ♡ ♥ ♧ ♣ ⊙ ◈ ▣ ◐ ◑ ▒ ▤ ▥ ▨ ▧ ▦ ▩ ♨ ☏ ☎ ☜ ☞ ¶ † ‡ ↕ ↗ ↙ ↖ ↘ ♭ ♩ ♪ ♬ ㉿ ㈜ № ㏇ ™ ㏂ ㏘ ℡";

		$view['view']['char'] = explode(' ', $chars);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = '특수문자';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'specialchars',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 게시물변경로그 보기
	 */
	public function post_history($post_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_history';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			alert('잘못된 접근입니다');
			return false;
		}

		$select = 'post_id, brd_id, mem_id, post_title';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			alert('존재하지 않는 게시물입니다');
			return false;
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_posthistory', $board)) {
			alert('게시물 변경로그를 사용하지 않는 게시판입니다');
			return false;
		}

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		/**
		 * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		 */
		$param =& $this->querystring;
		$page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
		$this->load->model('Post_history_model');
		$findex = $this->Post_history_model->primary_key;
		$forder = 'desc';

		$per_page = 10;
		$offset = ($page - 1) * $per_page;

		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$where = array(
			'post.post_id' => $post_id,
		);
		$result = $this->Post_history_model
			->get_list($per_page, $offset, $where, '', $findex, $forder);
		$list_num = $result['total_rows'] - ($page - 1) * $per_page;

		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				$result['list'][$key]['display_name'] = display_username(
					element('mem_userid', $val),
					element('mem_nickname', $val)
				);
				$result['list'][$key]['post_display_name'] = display_username(
					element('post_userid', $val),
					element('post_nickname', $val)
				);
				$result['list'][$key]['num'] = $list_num--;
			}
		}
		$view['view']['data'] = $result;

		/**
		 * 페이지네이션을 생성합니다
		 */
		$config['base_url'] = site_url('helptool/post_history/' . $post_id) . '?' . $param->replace('page');
		$config['total_rows'] = $result['total_rows'];
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$view['view']['paging'] = $this->pagination->create_links();
		$view['view']['page'] = $page;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = element('post_title', $post) . ' > 게시물 변경 로그';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'post_history',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 게시물변경로그 상세 보기
	 */
	public function post_history_view($post_id = 0, $phi_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_history_view';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			alert('잘못된 접근입니다');
			return false;
		}

		$phi_id = (int) $phi_id;
		if (empty($phi_id) OR $phi_id < 1) {
			alert('잘못된 접근입니다');
			return false;
		}

		$select = 'post_id, brd_id, mem_id';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			alert('존재하지 않는 게시물입니다');
			return false;
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_posthistory', $board)) {
			alert('게시물 변경로그를 사용하지 않는 게시판입니다');
			return false;
		}

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$param =& $this->querystring;

		$this->load->model('Post_history_model');
		$result = $this->Post_history_model->get_one($phi_id);

		if ( ! element('phi_id', $result)) {
			alert('존재하지 않는 게시물입니다');
			return false;
		}

		$select = 'mem_id, mem_userid, mem_nickname, mem_icon';
		$result['member'] = $dbmember = $this->Member_model
			->get_by_memid(element('mem_id', $result), $select);
		$result['display_name'] = display_username(
			element('mem_userid', $dbmember),
			element('mem_nickname', $dbmember)
		);
		$result['post'] = $post = $this->Post_model->get_one(element('post_id', $result));
		if ($post) {
			$result['board'] = $board = $this->board->item_all(element('brd_id', $post));
		}
		$image_width = ($this->cbconfig->get_device_view_type() === 'mobile')
			? element('post_mobile_image_width', $board)
			: element('post_image_width', $board);
		$result['post_display_name'] = display_username(
			element('post_userid', $post),
			element('post_nickname', $post)
		);
		$result['content'] = display_html_content(
			element('phi_content', $result),
			element('phi_content_html_type', $result),
			$image_width
		);

		$where = array(
			'post_id' => element('post_id', $result),
			'phi_id <' => element('phi_id', $result),
		);
		$prev = $this->Post_history_model->get('', '', $where, 1, 0, 'phi_id', 'DESC');
		if ($prev && element(0, $prev)) {
			$p = element(0, $prev);
			$p['content'] = display_html_content(
				element('phi_content', $p),
				element('phi_content_html_type', $p),
				$image_width
			);
			$result['prev'] = $p;
		}

		$view['view']['data'] = $result;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = element('post_title', $post) . ' > 게시물 변경 로그';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'post_history_view',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 다운로드로그 보기
	 */
	public function download_log($post_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_download_log';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			alert('잘못된 접근입니다');
			return false;
		}

		$this->load->model('Post_file_download_log_model');

		$select = 'post_id, brd_id, mem_id, post_title';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			alert('존재하지 않는 게시물입니다');
			return false;
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_download_log', $board)) {
			alert('다운로드 로그를 사용하지 않는 게시판입니다');
			return false;
		}

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);
		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		/**
		 * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		 */
		$param =& $this->querystring;
		$page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
		$findex = $this->Post_file_download_log_model->primary_key;
		$forder = 'desc';

		$per_page = 10;
		$offset = ($page - 1) * $per_page;

		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$where = array(
			'post.post_id' => $post_id,
		);
		$result = $this->Post_file_download_log_model
			->get_list($per_page, $offset, $where, '', $findex, $forder);
		$list_num = $result['total_rows'] - ($page - 1) * $per_page;
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				$select = 'mem_id, mem_userid, mem_nickname, mem_icon';
				$result['list'][$key]['member'] = $dbmember = $this->Member_model
					->get_by_memid(element('mem_id', $val), $select);
				$result['list'][$key]['display_name'] = display_username(
					element('mem_userid', $dbmember),
					element('mem_nickname', $dbmember)
				);
				$result['list'][$key]['post_display_name'] = display_username(
					element('post_userid', $val),
					element('post_nickname', $val)
				);
				$result['list'][$key]['num'] = $list_num--;
			}
		}
		$view['view']['data'] = $result;

		/**
		 * 페이지네이션을 생성합니다
		 */
		$config['base_url'] = site_url('helptool/download_log/' . $post_id) . '?' . $param->replace('page');
		$config['total_rows'] = $result['total_rows'];
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$view['view']['paging'] = $this->pagination->create_links();
		$view['view']['page'] = $page;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = element('post_title', $post) . ' > 다운로드 로그';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'download_log',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 링크클릭로그 보기
	 */
	public function link_click_log($post_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_link_click_log';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			alert('잘못된 접근입니다');
			return false;
		}

		$this->load->model('Post_link_click_log_model');

		$select = 'post_id, brd_id, mem_id, post_title';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			alert('존재하지 않는 게시물입니다');
			return false;
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_link_click_log', $board)) {
			alert('링크클릭로그를 사용하지 않는 게시판입니다');
			return false;
		}

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		/**
		 * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		 */
		$param =& $this->querystring;
		$page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
		$findex = $this->Post_link_click_log_model->primary_key;
		$forder = 'desc';

		$per_page = 10;
		$offset = ($page - 1) * $per_page;

		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$where = array(
			'post.post_id' => $post_id,
		);
		$result = $this->Post_link_click_log_model
			->get_list($per_page, $offset, $where, '', $findex, $forder);
		$list_num = $result['total_rows'] - ($page - 1) * $per_page;
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				$select = 'mem_id, mem_userid, mem_nickname, mem_icon';
				$result['list'][$key]['member'] = $dbmember = $this->Member_model
					->get_by_memid(element('mem_id', $val), $select);
				$result['list'][$key]['display_name'] = display_username(
					element('mem_userid', $dbmember),
					element('mem_nickname', $dbmember),
					element('mem_icon', $dbmember)
				);
				$result['list'][$key]['post_display_name'] = display_username(
					element('post_userid', $val),
					element('post_nickname', $val)
				);
				$result['list'][$key]['num'] = $list_num--;
			}
		}
		$view['view']['data'] = $result;

		/**
		 * 페이지네이션을 생성합니다
		 */
		$config['base_url'] = site_url('helptool/link_click_log/' . $post_id) . '?' . $param->replace('page');
		$config['total_rows'] = $result['total_rows'];
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$view['view']['paging'] = $this->pagination->create_links();
		$view['view']['page'] = $page;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = element('post_title', $post) . ' > 링크클릭 로그';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'link_click_log',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 게시물 복사 밎 이동
	 */
	public function post_copy($type = 'copy', $post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_copy';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin !== 'super') {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array(
			'Blame_model', 'Board_model', 'Board_group_model',
			'Comment_model', 'Like_model', 'Post_extra_vars_model',
			'Post_file_model', 'Post_file_download_log_model', 'Post_history_model',
			'Post_link_model', 'Post_link_click_log_model', 'Post_meta_model',
			'Post_tag_model', 'Scrap_model'
		));

		$post_id_list = '';
		if ($this->input->post('chk_post_id')) {
			$post_id_list = '';
			$chk_post_id = $this->input->post('chk_post_id');
			foreach ($chk_post_id as $val) {
				if (empty($post_id)) {
					$post_id = $val;
				}
				$post_id_list .= $val . ',';
			}
		} elseif ($post_id) {
			$post_id_list = $post_id;
		}
		if ($this->input->post('post_id_list')) {
			$post_id_list = $this->input->post('post_id_list');
		}
		$view['view']['post_id_list'] = $post_id_list;

		$post = $this->Post_model->get_one($post_id);
		$board = $this->board->item_all(element('brd_id', $post));

		if ($type !== 'move') {
			$type = 'copy';
		}
		$view['view']['post'] = $post;
		$view['view']['board'] = $board;
		$view['view']['typetext'] = $typetext = ($type === 'copy') ? '복사' : '이동';

		$config = array(
			array(
				'field' => 'is_submit',
				'label' => '체크',
				'rules' => 'trim',
			),
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($form_validation === false OR ! $this->input->post('is_submit')) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			$result = $this->Board_model->get_board_list();
			if ($result && is_array($result)) {
				foreach ($result as $key => $value) {
					$result[$key]['group'] = $this->Board_group_model
						->get_one(element('bgr_id', $value));
				}
			}
			$view['view']['list'] = $result;

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = element('post_title', $post) . ' > 게시물 ' . $typetext;
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'post_copy',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		} else {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			$old_brd_id = element('brd_id', $board);
			$new_brd_id = (int) $this->input->post('chk_brd_id');

			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {
					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}

						$post = $this->Post_model->get_one($post_id);
						$board = $this->board->item_all(element('brd_id', $post));

						if ($type === 'copy') {
							// 게시글 복사

							// 이벤트가 존재하면 실행합니다
							$view['view']['event']['copy_before'] = Events::trigger('copy_before', $eventname);

							$post_num = $this->Post_model->next_post_num();

							$post_content = $post['post_content'];
							if ($this->cbconfig->item('use_copy_log')) {
								$br = $post['post_html'] ? '<br /><br />' : "\n";
								$post_content .= $br . '[이 게시물은 '
									. $this->member->item('mem_nickname') . ' 님에 의해 '
									. cdate('Y-m-d H:i:s') . ' '
									. element('brd_name', $board) . ' 에서 복사됨]';
							}
							$insertdata = array(
								'post_num' => $post_num,
								'post_reply' => $post['post_reply'],
								'brd_id' => $new_brd_id,
								'post_title' => $post['post_title'],
								'post_content' => $post_content,
								'mem_id' => $post['mem_id'],
								'post_userid' => $post['post_userid'],
								'post_username' => $post['post_username'],
								'post_nickname' => $post['post_nickname'],
								'post_email' => $post['post_email'],
								'post_homepage' => $post['post_homepage'],
								'post_datetime' => $post['post_datetime'],
								'post_password' => $post['post_password'],
								'post_updated_datetime' => $post['post_updated_datetime'],
								'post_update_mem_id' => $post['post_update_mem_id'],
								'post_link_count' => $post['post_link_count'],
								'post_secret' => $post['post_secret'],
								'post_html' => $post['post_html'],
								'post_notice' => $post['post_notice'],
								'post_receive_email' => $post['post_receive_email'],
								'post_hit' => $post['post_hit'],
								'post_ip' => $post['post_ip'],
								'post_device' => $post['post_device'],
								'post_file' => $post['post_file'],
								'post_image' => $post['post_image'],
								'post_del' => $post['post_del'],
							);
							$new_post_id = $this->Post_model->insert($insertdata);

							$postwhere = array(
								'post_id' => $post_id,
							);
							$filedata = $this->Post_file_model->get('', '', $postwhere);
							if ($filedata) {
								foreach ($filedata as $data) {
									$exp = explode('/', $data['pfi_filename']);
									$new_file_name = $exp[0] . '/' . $exp['1'] . '/' . random_string('alnum',30) . '.' . $data['pfi_type'];
									$fileinsert = array(
										'post_id' => $new_post_id,
										'brd_id' => $new_brd_id,
										'mem_id' => $data['mem_id'],
										'pfi_originname' => $data['pfi_originname'],
										'pfi_filename' => $new_file_name,
										'pfi_filesize' => $data['pfi_filesize'],
										'pfi_width' => $data['pfi_width'],
										'pfi_height' => $data['pfi_height'],
										'pfi_type' => $data['pfi_type'],
										'pfi_is_image' => $data['pfi_is_image'],
										'pfi_datetime' => $data['pfi_datetime'],
										'pfi_ip' => $data['pfi_ip'],
									);
									$this->Post_file_model->insert($fileinsert);
									copy(
										config_item('uploads_dir') . '/post/' . $data['pfi_filename'],
										config_item('uploads_dir') . '/post/' . $new_file_name
									);
								}
							}

							$postwhere = array(
								'post_id' => $post_id,
							);
							$linkdata = $this->Post_link_model->get('', '', $postwhere);
							if ($linkdata) {
								foreach ($linkdata as $data) {
									$linkinsert = array(
										'post_id' => $new_post_id,
										'brd_id' => $new_brd_id,
										'pln_url' => $data['pln_url'],
									);
									$this->Post_link_model->insert($linkinsert);
								}
							}

							$postwhere = array(
								'post_id' => $post_id,
							);
							$metadata = $this->Post_meta_model->get('', '', $postwhere);
							if ($metadata) {
								foreach ($metadata as $data) {
									$metainsert = array(
										'post_id' => $new_post_id,
										'brd_id' => $new_brd_id,
										'pmt_key' => $data['pmt_key'],
										'pmt_value' => $data['pmt_value'],
									);
									$this->Post_meta_model->insert($metainsert);
								}
							}

							$postwhere = array(
								'post_id' => $post_id,
							);
							$tagdata = $this->Post_tag_model->get('', '', $postwhere);
							if ($tagdata) {
								foreach ($tagdata as $data) {
									$taginsert = array(
										'post_id' => $new_post_id,
										'brd_id' => $new_brd_id,
										'pta_tag' => $data['pta_tag'],
									);
									$this->Post_tag_model->insert($taginsert);
								}
							}

							// 이벤트가 존재하면 실행합니다
							$view['view']['event']['copy_after'] = Events::trigger('copy_after', $eventname);

						}
						if ($type === 'move') {

							// 이벤트가 존재하면 실행합니다
							$view['view']['event']['move_before'] = Events::trigger('move_before', $eventname);

							// post table update
							$postupdate = array(
								'brd_id' => $new_brd_id,
							);

							if ($this->cbconfig->item('use_copy_log')) {
								$post_content = $post['post_content'];
								$br = $post['post_html'] ? '<br /><br />' : "\n";
								$post_content .= $br . '[이 게시물은 '
									. $this->member->item('mem_nickname') . ' 님에 의해 '
									. cdate('Y-m-d H:i:s') . ' '
									. element('brd_name', $board) . ' 에서 이동됨]';
								$postupdate['post_content'] = $post_content;
							}

							$this->Post_model->update($post_id, $postupdate);


							$dataupdate = array(
								'brd_id' => $new_brd_id,
							);
							$where = array(
								'target_id' => $post_id,
								'target_type' => 1,
							);
							$this->Blame_model->update('', $dataupdate, $where);
							$this->Like_model->update('', $dataupdate, $where);

							$where = array(
								'post_id' => $post_id,
							);
							$this->Comment_model->update('', $dataupdate, $where);
							$this->Post_extra_vars_model->update('', $dataupdate, $where);
							$this->Post_file_model->update('', $dataupdate, $where);
							$this->Post_file_download_log_model->update('', $dataupdate, $where);
							$this->Post_history_model->update('', $dataupdate, $where);
							$this->Post_link_model->update('', $dataupdate, $where);
							$this->Post_link_click_log_model->update('', $dataupdate, $where);
							$this->Post_meta_model->update('', $dataupdate, $where);
							$this->Post_tag_model->update('', $dataupdate, $where);
							$this->Scrap_model->update('', $dataupdate, $where);

							// 이벤트가 존재하면 실행합니다
							$view['view']['event']['move_after'] = Events::trigger('move_after', $eventname);

						}
					}
				}
			}

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['after'] = Events::trigger('after', $eventname);

			$alert = ($type === 'copy') ? '게시글 복사가 완료되었습니다' : '게시글 이동이 완료되었습니다';
			alert_close($alert);
		}
	}


	/**
	 * 게시물 카테고리 변경하기
	 */
	public function post_change_category($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Board_category_model','Board_group_category_model','Cmall_category_model','Cmall_category_rel_model','Cmall_item_model'));

		$post_id_list = '';
		if ($this->input->post('chk_post_id')) {
			$post_id_list = '';
			$chk_post_id = $this->input->post('chk_post_id');
			foreach ($chk_post_id as $val) {
				if (empty($post_id)) {
					$post_id = $val;
				}
				$post_id_list .= $val . ',';
			}
		} elseif ($post_id) {
			$post_id_list = $post_id;
		}
		if ($this->input->post('post_id_list')) {
			$post_id_list = $this->input->post('post_id_list');
		}
		$view['view']['post_id_list'] = $post_id_list;

		$cit_id_list = '';
		if ($this->input->post('chk')) {
			$cit_id_list = '';
			$chk_cit_id = $this->input->post('chk');
			foreach ($chk_cit_id as $val) {
				if (empty($cit_id)) {
					$cit_id = $val;
				}
				$cit_id_list .= $val . ',';
			}
		}
		if ($this->input->post('cit_id_list')) {
			$cit_id_list = $this->input->post('cit_id_list');
		}


		$view['view']['cit_id_list'] = $cit_id_list;


		$post = $this->Post_model->get_one($post_id);
		$board = $this->board->item_all(element('brd_id', $post));

		$view['view']['post'] = $post;
		$view['view']['board'] = $board;

		$config = array(
			array(
				'field' => 'is_submit',
				'label' => '체크',
				'rules' => 'trim',
			),
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($form_validation === false OR ! $this->input->post('is_submit')) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			
			
			
			$view['view']['data']['all_category'] = $this->Cmall_category_model->get_all_category();

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = element('brd_name', $board) . ' > 카테고리 변경';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'post_change_category',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		} else {
			
			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);
			$cmall_category = $this->input->post('cmall_category', null, '');
			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {
					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id', $postwhere, '', '');

						foreach ($Cmall_item as $c_key => $c_value) {

						    $this->Cmall_category_rel_model->save_category(element('cit_id', $c_value), $cmall_category,1);
						}
						

						

						// $post = $this->Post_model->get_one($post_id);
						// $board = $this->board->item_all(element('brd_id', $post));


						// $chk_post_category = $this->input->post('chk_post_category', null, '');

						// $postupdate = array(
						// 	'post_category' => $chk_post_category,
						// );
						// $this->Post_model->update($post_id, $postupdate);
					}
				}
			}

			if ($cit_id_list) {
				$arr = explode(',', $cit_id_list);
				if ($arr) {					
					
					foreach($arr as $val){
					
						if (empty($val)) {
							continue;
						}
						


						$this->Cmall_category_rel_model->save_category($val, $cmall_category,1);
						
					}
				
				}
			}
			alert_refresh_close('카테고리가 변경되었습니다');
		}
	}


	/**
	 * 구글지도
	 */
	public function googlemap()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_googlemap';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = '구글지도';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'googlemap',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		//$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 구글지도
	 */
	public function googlemap_search()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_googlemap_search';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		 * 레이아웃을 정의합니다
		 */
		$page_title = '구글지도';
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'googlemap_search',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			'page_title' => $page_title,
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}


	/**
	 * 게시물 카테고리 변경하기
	 */
	public function crawl_item_write($post_id,$crawl_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'crawl_item_write';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		if (empty($post_id)) {
			show_404();
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$this->_write_common($post_id);
	}


	public function _write_common($post_id)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'crawl_item_write_write_common';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		$view['view']['post'] = array();

		$this->load->model(array('Post_model','Post_link_model','Crawl_model','Crawl_link_model', 'Crawl_file_model'));

		

		/**
		 * Validation 라이브러리를 가져옵니다
		 */
		$this->load->library('form_validation');

		/**
		 * 전송된 데이터의 유효성을 체크합니다
		 */
		$config = array(
			array(
				'field' => 'crawl_title',
				'label' => '제목',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'crawl_price',
				'label' => '가격',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'crawl_post_url',
				'label' => '상품 URL',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'crawl_goods_code',
				'label' => '상품 코드',
				'rules' => 'trim|required',
			),
		);
		

		$post = $this->Post_model->get_one($post_id);
		$view['view']['post'] = $post;

		$linkwhere = array(
			'post_id' => $post_id,
		);
		$link = $this->Post_link_model
			->get('', '', $linkwhere, '', '', 'pln_id', 'ASC');

		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		$file_error = '';
		$uploadfiledata = array();

		if ($form_validation ) {

			$this->load->library('upload');
			$this->load->library('aws_s3');

			if (isset($_FILES) && isset($_FILES['crawl_file']) && isset($_FILES['crawl_file']['name']) && is_array($_FILES['crawl_file']['name'])) {
				$filecount = count($_FILES['crawl_file']['name']);
				$upload_path = config_item('uploads_dir') . '/crawl/';
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

				foreach ($_FILES['crawl_file']['name'] as $i => $value) {
					if ($value) {
						$uploadconfig = array();
						$uploadconfig['upload_path'] = $upload_path;
						$uploadconfig['allowed_types'] = '*';
						$uploadconfig['max_size'] = 2 * 1024;
						$uploadconfig['encrypt_name'] = true;

						$this->upload->initialize($uploadconfig);
						$_FILES['userfile']['name'] = $_FILES['crawl_file']['name'][$i];
						$_FILES['userfile']['type'] = $_FILES['crawl_file']['type'][$i];
						$_FILES['userfile']['tmp_name'] = $_FILES['crawl_file']['tmp_name'][$i];
						$_FILES['userfile']['error'] = $_FILES['crawl_file']['error'][$i];
						$_FILES['userfile']['size'] = $_FILES['crawl_file']['size'][$i];
						if ($this->upload->do_upload()) {
							$filedata = $this->upload->data();

							$uploadfiledata[$i] = array();
							$uploadfiledata[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
							$uploadfiledata[$i]['cfi_originname'] = element('orig_name', $filedata);
							$uploadfiledata[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
							$uploadfiledata[$i]['cfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
							$uploadfiledata[$i]['cfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
							$uploadfiledata[$i]['cfi_type'] = str_replace('.', '', element('file_ext', $filedata));
							$uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? 1 : 0;

							$upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);
							
						} else {
							$file_error = $this->upload->display_errors();
							break;
						}
					}
				}
			}
		}


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
			 * 레이아웃을 정의합니다
			 */
			$page_title = 'Itme 추가';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'crawl_item_write',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
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
			

			$updatedata = array(
			    
			    'post_id' => $post_id,
			    'crawl_title' => $this->input->post('crawl_title', null, ''),
			    'crawl_price' => preg_replace("/[^0-9]*/s", "", $this->input->post('crawl_price', null, '')) ,

			    'crawl_datetime' => cdate('Y-m-d H:i:s'),
			    'crawl_updated_datetime' => cdate('Y-m-d H:i:s'),
			    'crawl_post_url' => $this->input->post('crawl_post_url', null, ''),
			    'brd_id' => element('brd_id', $post),
			    'pln_id' => element('pln_id', element(0, $link)),
			    'crawl_goods_code' => $this->input->post('crawl_goods_code', null, ''),
			);



			$crawl_id = $this->Crawl_model->insert($updatedata);

			

			$file_updated = false;
			if ($uploadfiledata
				&& is_array($uploadfiledata) && count($uploadfiledata) > 0) {
				foreach ($uploadfiledata as $pkey => $pval) {
					if ($pval) {
						$fileupdate = array(
						    'crawl_id' => $crawl_id,
						    'post_id' => $post_id,
						    'brd_id' => element('brd_id', $post),
						    'cfi_originname' => element('cfi_originname', $pval),
						    'cfi_filename' => element('cfi_filename', $pval),
						    'cfi_filesize' => element('cfi_filesize', $pval),
						    'cfi_width' => element('cfi_width', $pval),
						    'cfi_height' => element('cfi_height', $pval),
						    'cfi_type' => element('cfi_type', $pval),
						    'cfi_is_image' => element('is_image', $pval),
						    'cfi_datetime' => cdate('Y-m-d H:i:s'),
						    'cfi_ip' => $this->input->ip_address(),
						);
						$file_id = $this->Crawl_file_model->insert($fileupdate);
						
						$file_updated = true;
					}
				}
			}
			

			alert_refresh_close('추가되었습니다');
		}
			

	}

	public function crawl_item_modify($post_id,$crawl_id)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'crawl_item_modify_modify';
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

		$crawl_id = (int) $crawl_id;
		if (empty($crawl_id) OR $crawl_id < 1) {
			show_404();
		}

		$this->load->model(array('Post_model','Post_link_model','Crawl_model','Crawl_link_model', 'Crawl_file_model'));

		/**
		 * 수정 페이지일 경우 기존 데이터를 가져옵니다
		 */
		$post = $this->Post_model->get_one($post_id);
		if ( ! element('post_id', $post)) {
			show_404();
		}
		if (element('post_del', $post)) {
			alert('삭제된 글은 수정하실 수 없습니다');
			return false;
		}

		$view['view']['post'] = $post;


		$crawl = $this->Crawl_model->get_one($crawl_id);		

		$view['view']['crawl'] = $crawl;


		$postwhere = array(
			'post_id' => $post_id,
		);
		$link = $this->Post_link_model->get('', '', $postwhere, '', '', 'pln_id', 'ASC');
		
		$crawlwhere = array(
			'crawl_id' => $crawl_id,
		);
		$view['view']['file'] = $file
			= $this->Crawl_file_model->get('', '', $crawlwhere, '', '', 'cfi_id', 'ASC');
		if ($file && is_array($file)) {
			foreach ($file as $key => $value) {
				$view['view']['file'][$key]['download_link']
					= site_url('postact/download/' . element('cfi_id', $value));
			}
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
				'field' => 'crawl_title',
				'label' => '제목',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'crawl_price',
				'label' => '가격',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'crawl_post_url',
				'label' => '상품 URL',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'crawl_goods_code',
				'label' => '상품 코드',
				'rules' => 'trim|required',
			),
		);
		
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		$file_error = '';
		$uploadfiledata = array();
		$uploadfiledata2 = array();
		if ($form_validation ) {
			$this->load->library('upload');
			$this->load->library('aws_s3');
			if (isset($_FILES) && isset($_FILES['crawl_file']) && isset($_FILES['crawl_file']['name']) && is_array($_FILES['crawl_file']['name'])) {
				$filecount = count($_FILES['crawl_file']['name']);
				$upload_path = config_item('uploads_dir') . '/crawl/';
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

				foreach ($_FILES['crawl_file']['name'] as $i => $value) {
					if ($value) {
						$uploadconfig = array();
						$uploadconfig['upload_path'] = $upload_path;
						$uploadconfig['allowed_types'] = '*';
						$uploadconfig['max_size'] = 2 * 1024;
						$uploadconfig['encrypt_name'] = true;

						$this->upload->initialize($uploadconfig);
						$_FILES['userfile']['name'] = $_FILES['crawl_file']['name'][$i];
						$_FILES['userfile']['type'] = $_FILES['crawl_file']['type'][$i];
						$_FILES['userfile']['tmp_name'] = $_FILES['crawl_file']['tmp_name'][$i];
						$_FILES['userfile']['error'] = $_FILES['crawl_file']['error'][$i];
						$_FILES['userfile']['size'] = $_FILES['crawl_file']['size'][$i];
						if ($this->upload->do_upload()) {
							$filedata = $this->upload->data();

							$uploadfiledata[$i] = array();
							$uploadfiledata[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
							$uploadfiledata[$i]['cfi_originname'] = element('orig_name', $filedata);
							$uploadfiledata[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
							$uploadfiledata[$i]['cfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
							$uploadfiledata[$i]['cfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
							$uploadfiledata[$i]['cfi_type'] = str_replace('.', '', element('file_ext', $filedata));
							$uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;

							$upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);                
						} else {
							$file_error = $this->upload->display_errors();
							break;
						}
					}
				}
			}
			if (isset($_FILES) && isset($_FILES['crawl_file_update'])
				&& isset($_FILES['crawl_file_update']['name'])
				&& is_array($_FILES['crawl_file_update']['name'])
				&& $file_error === '') {
				$filecount = count($_FILES['crawl_file_update']['name']);
				$upload_path = config_item('uploads_dir') . '/crawl/';
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

				foreach ($_FILES['crawl_file_update']['name'] as $i => $value) {
					if ($value) {
						$uploadconfig = array();
						$uploadconfig['upload_path'] = $upload_path;
						$uploadconfig['allowed_types'] = '*';
						$uploadconfig['max_size'] = 2 * 1024;
						$uploadconfig['encrypt_name'] = true;
						$this->upload->initialize($uploadconfig);
						$_FILES['userfile']['name'] = $_FILES['crawl_file_update']['name'][$i];
						$_FILES['userfile']['type'] = $_FILES['crawl_file_update']['type'][$i];
						$_FILES['userfile']['tmp_name'] = $_FILES['crawl_file_update']['tmp_name'][$i];
						$_FILES['userfile']['error'] = $_FILES['crawl_file_update']['error'][$i];
						$_FILES['userfile']['size'] = $_FILES['crawl_file_update']['size'][$i];
						if ($this->upload->do_upload()) {
							$filedata = $this->upload->data();

							$oldpostfile = $this->Crawl_file_model->get_one($i);
							if ((int) element('crawl_id', $oldpostfile) !== (int) element('crawl_id', $crawl)) {
								alert('잘못된 접근입니다');
							}
							@unlink(config_item('uploads_dir') . '/crawl/' . element('cfi_filename', $oldpostfile));

							$deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/crawl/' . element('cfi_filename', $oldpostfile));

							$uploadfiledata2[$i] = array();
							$uploadfiledata2[$i]['cfi_id'] = $i;
							$uploadfiledata2[$i]['cfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
							$uploadfiledata2[$i]['cfi_originname'] = element('orig_name', $filedata);
							$uploadfiledata2[$i]['cfi_filesize'] = intval(element('file_size', $filedata) * 1024);
							$uploadfiledata2[$i]['cfi_width'] = element('image_width', $filedata)
								? element('image_width', $filedata) : 0;
							$uploadfiledata2[$i]['cfi_height'] = element('image_height', $filedata)
								? element('image_height', $filedata) : 0;
							$uploadfiledata2[$i]['cfi_type'] = str_replace('.', '', element('file_ext', $filedata));
							$uploadfiledata2[$i]['is_image'] = element('is_image', $filedata)
								? element('is_image', $filedata) : 0;

							$upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);                
						} else {
							$file_error = $this->upload->display_errors();
							break;
						}
					}
				}
			}
		}


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
			 * 레이아웃을 정의합니다
			 */
			$page_title = 'Itme 수정';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'crawl_item_write',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
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
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			

			$crawl_title = $this->input->post('crawl_title', null, '');
			

			
			

			
			$updatedata = array(
			    
			    'post_id' => $post_id,
			    'crawl_title' => $this->input->post('crawl_title', null, ''),
			    'crawl_price' => preg_replace("/[^0-9]*/s", "", $this->input->post('crawl_price', null, '')) ,

			    'crawl_datetime' => cdate('Y-m-d H:i:s'),
			    'crawl_updated_datetime' => cdate('Y-m-d H:i:s'),
			    'crawl_post_url' => $this->input->post('crawl_post_url', null, ''),
			    'brd_id' => element('brd_id', $post),			    
			    'crawl_goods_code' => $this->input->post('crawl_goods_code', null, ''),
			);



			$this->Crawl_model->update($crawl_id,$updatedata);


			
			
			

			

			$file_updated = false;
			$file_changed = false;
			if ($uploadfiledata
				&& is_array($uploadfiledata)
				&& count($uploadfiledata) > 0) {
				foreach ($uploadfiledata as $pkey => $pval) {
					if ($pval) {
						$fileupdate = array(
						    'crawl_id' => $crawl_id,
						    'post_id' => $post_id,
						    'brd_id' => element('brd_id', $post),
						    'cfi_originname' => element('cfi_originname', $pval),
						    'cfi_filename' => element('cfi_filename', $pval),
						    'cfi_filesize' => element('cfi_filesize', $pval),
						    'cfi_width' => element('cfi_width', $pval),
						    'cfi_height' => element('cfi_height', $pval),
						    'cfi_type' => element('cfi_type', $pval),
						    'cfi_is_image' => element('is_image', $pval),
						    'cfi_datetime' => cdate('Y-m-d H:i:s'),
						    'cfi_ip' => $this->input->ip_address(),
						);
						$file_id = $this->Crawl_file_model->insert($fileupdate);
						
						$file_updated = true;
					}
				}
				$file_changed = true;
			}
			if ($uploadfiledata2
				&& is_array($uploadfiledata2)
				&& count($uploadfiledata2) > 0) {
				foreach ($uploadfiledata2 as $pkey => $pval) {
					if ($pval) {
						$fileupdate = array(
						    'crawl_id' => $crawl_id,
						    'post_id' => $post_id,
						    'brd_id' => element('brd_id', $post),
						    'cfi_originname' => element('cfi_originname', $pval),
						    'cfi_filename' => element('cfi_filename', $pval),
						    'cfi_filesize' => element('cfi_filesize', $pval),
						    'cfi_width' => element('cfi_width', $pval),
						    'cfi_height' => element('cfi_height', $pval),
						    'cfi_type' => element('cfi_type', $pval),
						    'cfi_is_image' => element('is_image', $pval),
						    'cfi_datetime' => cdate('Y-m-d H:i:s'),
						    'cfi_ip' => $this->input->ip_address(),
						);
						$this->Crawl_file_model->update($pkey, $fileupdate);
						
						$file_changed = true;
					}
				}
			}
			if ($this->input->post('crawl_file_del')) {
				foreach ($this->input->post('crawl_file_del') as $key => $val) {
					if ($val === '1' && ! isset($uploadfiledata2[$key])) {
						$oldpostfile = $this->Crawl_file_model->get_one($key);
						if ( ! element('crawl_id', $oldpostfile) OR (int) element('crawl_id', $oldpostfile) !== (int) element('crawl_id', $crawl)) {
							alert('잘못된 접근입니다.');
						}

						@unlink(config_item('uploads_dir') . '/crawl/' . element('cfi_filename', $oldpostfile));

						$deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/crawl/' . element('cfi_filename', $oldpostfile));

						

						$this->Crawl_file_model->delete($key);
						
						$file_changed = true;
					}
				}
			}
			

			alert_refresh_close('수정 되었습니다');
		}
			
	}


	public function event_in_cmall_item($eve_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_cmallitem_index';
		$this->load->event($eventname);

		if (empty($eve_id)) {
			show_404();
		}
		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_wishlist_model','Cmall_item_model','Cmall_category_model','Event_model'));

		/**
		 * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		 */
		$param =& $this->querystring;
		$page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
		$view['view']['sort'] = array(
			'cit_id' => $param->sort('cit_id', 'asc'),
			'cit_key' => $param->sort('cit_key', 'asc'),
			'cit_price_sale' => $param->sort('cit_price_sale', 'asc'),
			'cit_name' => $param->sort('cit_name', 'asc'),
			'cit_datetime' => $param->sort('cit_datetime', 'asc'),
			'cit_updated_datetime' => $param->sort('cit_updated_datetime', 'asc'),
			'cit_hit' => $param->sort('cit_hit', 'asc'),
			'cit_sell_count' => $param->sort('cit_sell_count', 'asc'),
			'cit_price' => $param->sort('cit_price', 'asc'),

		);
		$findex = $this->input->get('findex') ? $this->input->get('findex') : $this->Cmall_item_model->primary_key;
		$forder = $this->input->get('forder', null, 'desc');
		$sfield = $this->input->get('sfield', null, 'cit_name');
		$skeyword = $this->input->get('skeyword', null, '');

		if(!empty($this->input->get('warning'))){
			$or_where = array(
				'cit_name' => '',
				'cit_price' => 0,
				'cit_post_url' => '',
				'cit_goods_code' => '',
				'cit_file_1' => '',
				'cbr_id' => 0,
			);
			
			// $this->Cmall_item_model->or_where($or_where);

			$this->Cmall_item_model->set_where("(cit_name = '' OR (cit_price = 0 and cit_is_soldout =0 ) OR cit_post_url = '' OR cit_goods_code = '' OR cit_file_1 = '' OR cb_cmall_item.cbr_id = 0)",'',false);
		} 
		
		$per_page = admin_listnum();
		$offset = ($page - 1) * $per_page;
		$event_rel = array();
		$event_rel = $this->Event_model->get_event($eve_id);

		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$this->Cmall_item_model->allow_search_field = array('cit_goods_code', 'cit_key', 'cit_name', 'cit_datetime', 'cit_updated_datetime', 'cit_content', 'cit_mobile_content', 'cit_price'); // 검색이 가능한 필드
		$this->Cmall_item_model->search_field_equal = array('cit_goods_code', 'cit_price'); // 검색중 like 가 아닌 = 검색을 하는 필드
		$this->Cmall_item_model->allow_order_field = array('cit_id', 'cit_key', 'cit_price_sale', 'cit_name', 'cit_datetime', 'cit_updated_datetime', 'cit_hit', 'cit_sell_count', 'cit_price'); // 정렬이 가능한 필드
		$result = $this->Cmall_item_model
			->get_admin_list($per_page, $offset, '', '', $findex, $forder, $sfield, $skeyword);

		$list_num = $result['total_rows'] - ($page - 1) * $per_page;
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				// $result['list'][$key]['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $val));
				$result['list'][$key]['category'] = $this->Cmall_category_model->get_category(element('cit_id', $val));
				// $result['list'][$key]['attr'] = $this->Cmall_attr_model->get_attr(element('cit_id', $val));
				if($event_rel)
				foreach($event_rel as $eveval){

					if(element('cit_id',$val) === 	element('cit_id',$eveval)){
						$result['list'][$key]['checked'] =  1;

						break;
					}
				}
				
				$cmall_wishlist_where = array(
					'cit_id' => element('cit_id', $val),
					
				);
				$cmall_wishlist_count = $this->Cmall_wishlist_model
					->count_by($cmall_wishlist_where);

				$result['list'][$key]['cmall_wishlist_count'] = $cmall_wishlist_count ? $cmall_wishlist_count : 0;

				if(empty(element('cit_name', $val)) || empty(element('cit_price', $val)) || empty(element('cit_post_url', $val)) || empty(element('cit_goods_code', $val)) || empty(element('cbr_id', $val)))
					$result['list'][$key]['warning'] = 1 ; 
				else 
					$result['list'][$key]['warning'] = '' ; 


				$result['list'][$key]['display_tag'] = '';
				$crawlwhere = array(
					'cit_id' => element('cit_id', $val),
				);
				

				$result['list'][$key]['display_label'] = '';
				$crawlwhere = array(
					'cit_id' => element('cit_id', $val),
				);
				

				$result['list'][$key]['num'] = $list_num--;
			}
		}

		$view['view']['data'] = $result;

		/**
		 * primary key 정보를 저장합니다
		 */
		$view['view']['primary_key'] = $this->Cmall_item_model->primary_key;

		/**
		 * 페이지네이션을 생성합니다
		 */
		
		$config['base_url'] = site_url('helptool/event_in_cmall_item/' . $eve_id) . '?' . $param->replace('page');
		$config['total_rows'] = $result['total_rows'];
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$view['view']['paging'] = $this->pagination->create_links();
		$view['view']['page'] = $page;



		/**
		 * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
		 */
		$search_option = array( 'cit_name' => '상품명');
		$view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
		$view['view']['search_option'] = search_option($search_option, $sfield);
		$view['view']['listall_url'] = site_url('helptool/event_in_cmall_item/' . $eve_id);
		$view['view']['list_update_url'] = site_url('helptool/event_in_listupdate/'.$eve_id.'?' . $param->output());
		$view['view']['list_delete_url'] = site_url('helptool/event_in_listdelete/'.$eve_id.'?' . $param->output());

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 어드민 레이아웃을 정의합니다
		 */
		
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'event_in_cmall_item',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			
		);

		
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
		
	}

	public function event_in_listupdate($eve_id)
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
			
			$this->Event_rel_model->save_event($eve_id, $this->input->post('chk'));    
			
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		 * 업데이트가 끝난 후 목록페이지로 이동합니다
		 */
		$this->session->set_flashdata(
			'message',
			'정상적으로 추가 되었습니다'
		);
		$param =& $this->querystring;
		$redirecturl = site_url('helptool/event_in_cmall_item/' . $eve_id. '?' . $param->output());

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
		$redirecturl = site_url('helptool/event_in_cmall_item/' . $eve_id. '?' . $param->output());

		redirect($redirecturl);
	}


	public function theme_in_store($the_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'theme_admin_cmall_cmallitem_index';
		$this->load->event($eventname);

		if (empty($the_id)) {
			show_404();
		}
		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Board_model','Theme_model'));

		/**
		 * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		 */
		$param =& $this->querystring;
		$page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
		
		$findex = $this->input->get('findex') ? $this->input->get('findex') : $this->Board_model->primary_key;
		$forder = $this->input->get('forder', null, 'desc');
		$sfield = $this->input->get('sfield', null, 'brd_name');
		$skeyword = $this->input->get('skeyword', null, '');

		
		
		$per_page = admin_listnum();
		$offset = ($page - 1) * $per_page;
		$theme_rel = array();
		$theme_rel = $this->Theme_model->get_theme_rel($the_id);


		$where = array(
            'brd_blind' => 0,
        );
		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$this->Board_model->allow_search_field = array('brd_name'); // 검색이 가능한 필드
		// $this->Board_model->search_field_equal = array('cit_goods_code', 'cit_price'); // 검색중 like 가 아닌 = 검색을 하는 필드
		
		$result = $this->Board_model
			->get_list('','', $where, '', $findex, $forder, $sfield, $skeyword);

		$list_num = $result['total_rows'];
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				// $result['list'][$key]['meta'] = $this->Cmall_item_meta_model->get_all_meta(element('cit_id', $val));
				
				// $result['list'][$key]['attr'] = $this->Cmall_attr_model->get_attr(element('cit_id', $val));
				if($theme_rel)
				foreach($theme_rel as $othval){

					if(element('brd_id',$val) === 	element('brd_id',$othval)){
						$result['list'][$key]['checked'] =  1;

						break;
					}
				}

			


		
				

				$result['list'][$key]['num'] = $list_num--;
			}
		}

		$view['view']['data'] = $result;

		/**
		 * primary key 정보를 저장합니다
		 */
		$view['view']['primary_key'] = $this->Board_model->primary_key;

		/**
		 * 페이지네이션을 생성합니다
		 */
		
		// $config['base_url'] = site_url('helptool/theme_in_store/' . $the_id) . '?' . $param->replace('page');
		// $config['total_rows'] = $result['total_rows'];
		// $config['per_page'] = $per_page;
		// $this->pagination->initialize($config);
		// $view['view']['paging'] = $this->pagination->create_links();
		// $view['view']['page'] = $page;



		/**
		 * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
		 */
		$search_option = array( 'brd_name' => '스토어명');
		$view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
		$view['view']['search_option'] = search_option($search_option, $sfield);
		$view['view']['listall_url'] = site_url('helptool/theme_in_store/' . $the_id);
		$view['view']['list_update_url'] = site_url('helptool/theme_in_listupdate/'.$the_id.'?' . $param->output());
		$view['view']['list_delete_url'] = site_url('helptool/theme_in_listdelete/'.$the_id.'?' . $param->output());

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 어드민 레이아웃을 정의합니다
		 */
		
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'theme_in_store',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
			
		);

		
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
		
	}

	public function theme_in_listupdate($the_id)
	{
		
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'theme_admin_cmall_cmallitem_listupdate';
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
			
			$this->Theme_rel_model->save_theme($the_id, $this->input->post('chk'));    
			
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		 * 업데이트가 끝난 후 목록페이지로 이동합니다
		 */
		$this->session->set_flashdata(
			'message',
			'정상적으로 추가 되었습니다'
		);
		$param =& $this->querystring;
		$redirecturl = site_url('helptool/theme_in_store/' . $the_id. '?' . $param->output());

		redirect($redirecturl);
	}

	public function theme_in_listdelete($the_id)
	{
		
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'theme_admin_cmall_cmallitem_listupdate';
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
		$redirecturl = site_url('helptool/theme_in_store/' . $the_id. '?' . $param->output());

		redirect($redirecturl);
	}

	public function post_change_brand($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_brand_model','Cmall_item_model'));

		$post_id_list = '';
		if ($this->input->post('chk_post_id')) {
			$post_id_list = '';
			$chk_post_id = $this->input->post('chk_post_id');
			foreach ($chk_post_id as $val) {
				if (empty($post_id)) {
					$post_id = $val;
				}
				$post_id_list .= $val . ',';
			}
		} elseif ($post_id) {
			$post_id_list = $post_id;
		}
		if ($this->input->post('post_id_list')) {
			$post_id_list = $this->input->post('post_id_list');
		}
		$view['view']['post_id_list'] = $post_id_list;

		$cit_id_list = '';
		if ($this->input->post('chk')) {
			$cit_id_list = '';
			$chk_cit_id = $this->input->post('chk');
			foreach ($chk_cit_id as $val) {
				if (empty($cit_id)) {
					$cit_id = $val;
				}
				$cit_id_list .= $val . ',';
			}
		}
		if ($this->input->post('cit_id_list')) {
			$cit_id_list = $this->input->post('cit_id_list');
		}
		$view['view']['cit_id_list'] = $cit_id_list;

		
		$post = $this->Post_model->get_one($post_id);
		$board = $this->board->item_all(element('brd_id', $post));

		$view['view']['post'] = $post;
		$view['view']['board'] = $board;

		$config = array(
			array(
				'field' => 'is_submit',
				'label' => '체크',
				'rules' => 'trim',
			),
			array(
				'field' => 'brd_brand_text',
				'label' => '브랜드',
				'rules' => 'trim',
			),
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($form_validation === false OR ! $this->input->post('is_submit')) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			
			
			
			$view['view']['data']['brand_list'] = $this->Cmall_brand_model->get();

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = element('brd_name', $board) . ' > 브랜드 변경';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'post_change_brand',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		} else {
			
			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			if($this->input->post('brd_brand_text',null,'')){
				$this->db->select('cbr_id');			
				$this->db->from('cmall_brand');
				$this->db->where('cbr_value_kr', $this->input->post('brd_brand_text',null,''));
				$this->db->or_where('cbr_value_en', $this->input->post('brd_brand_text',null,''));
				$result = $this->db->get();
				$brd_brand = $result->row_array();
			}

			$cit_brand = empty($brd_brand) ? 0 : element('cbr_id',$brd_brand);

			$updatedata = array(							
							'cbr_id' => $cit_brand,
							
						);

			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {
					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id', $postwhere, '', '');


						


						foreach ($Cmall_item as $c_key => $c_value) {

							$this->Cmall_item_model->update(element('cit_id', $c_value), $updatedata);
						    
						}
						

						

						// $post = $this->Post_model->get_one($post_id);
						// $board = $this->board->item_all(element('brd_id', $post));


						// $chk_post_category = $this->input->post('chk_post_category', null, '');

						// $postupdate = array(
						// 	'post_category' => $chk_post_category,
						// );
						// $this->Post_model->update($post_id, $postupdate);
					}
				}
			}

			if ($cit_id_list) {
				$arr = explode(',', $cit_id_list);
				if ($arr) {					
					foreach($arr as $val){
						if (empty($val)) {
							continue;
						}
						


						$this->Cmall_item_model->update($val, $updatedata);
						
					}
				
				}
			}
			alert_refresh_close('브랜드가 변경되었습니다');
		}
	}


	public function post_delete_tag($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = $getdata = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Crawl_tag_model','Crawl_manual_tag_model','Crawl_delete_tag_model','Cmall_item_model','Vision_api_label_model'));

		$post_id_list = '';
		if ($this->input->post('chk_post_id')) {
			$post_id_list = '';
			$chk_post_id = $this->input->post('chk_post_id');
			foreach ($chk_post_id as $val) {
				if (empty($post_id)) {
					$post_id = $val;
				}
				$post_id_list .= $val . ',';
			}
		} elseif ($post_id) {
			$post_id_list = $post_id;
		}
		if ($this->input->post('post_id_list')) {
			$post_id_list = $this->input->post('post_id_list');
		}
		$view['view']['post_id_list'] = $post_id_list;

		$cit_id_list = '';
		if ($this->input->post('chk')) {
			$cit_id_list = '';
			$chk_cit_id = $this->input->post('chk');
			foreach ($chk_cit_id as $val) {
				if (empty($cit_id)) {
					$cit_id = $val;
				}
				$cit_id_list .= $val . ',';
			}
		} 
		if ($this->input->post('cit_id_list')) {
			$cit_id_list = $this->input->post('cit_id_list');
		}
		$view['view']['cit_id_list'] = $cit_id_list;

		
		$post = $this->Post_model->get_one($post_id);
		$board = $this->board->item_all(element('brd_id', $post));

		$view['view']['post'] = $post;
		$view['view']['board'] = $board;

		$config = array(
			array(
				'field' => 'is_submit',
				'label' => '체크',
				'rules' => 'trim',
			),
			
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($form_validation === false OR ! $this->input->post('is_submit')) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			
			
			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {

					$getdata['cdt_tag'] = '';
					$getdata['val_tag'] = '';
					$tag_array=array();
					$label_array=array();

					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id', $postwhere, '', '');


						

						
						foreach ($Cmall_item as $c_key => $c_value) {

							
							$crawlwhere = array(
								'cit_id' => element('cit_id',$c_value),
							);
							$tag = $this->Crawl_delete_tag_model->get('', '', $crawlwhere, '', '', 'cdt_id', 'ASC');
							if ($tag && is_array($tag)) {
								
								foreach ($tag as $tvalue) {
									if (element('cdt_tag', $tvalue)) {
										if(!in_array(trim(element('cdt_tag', $tvalue)),$tag_array))
										array_push($tag_array,trim(element('cdt_tag', $tvalue)));
									}
								}
								
							}
						}
						
						

						$getdata['val_tag'] = '';
						$label_array=array();
						$crawlwhere = array(
							'cit_id' => element('cit_id',$c_value),
						);
						$tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
						if ($tag && is_array($tag)) {
							
							foreach ($tag as $tvalue) {
								if (element('val_tag', $tvalue)) {
									if(!in_array(trim(element('val_tag', $tvalue)),$label_array))
									array_push($label_array,trim(element('val_tag', $tvalue)));
								}
							}
							
						}

						
					}

					$getdata['val_tag'] = implode("\n",$label_array);
					$getdata['cdt_tag'] = implode("\n",$tag_array);
				}
			}


			if ($cit_id_list) {
				$arr = explode(',', $cit_id_list);

				$getdata['cdt_tag'] = '';
				$getdata['val_tag'] = '';
				$tag_array=array();
				$label_array=array();

				if ($arr) {
					foreach($arr as $val){
						if (empty($val)) {
							continue;
						}
						

						
						

							
						$crawlwhere = array(
							'cit_id' => $val,
						);
						$tag = $this->Crawl_delete_tag_model->get('', '', $crawlwhere, '', '', 'cdt_tag', 'ASC');
						if ($tag && is_array($tag)) {
							
							foreach ($tag as $tvalue) {
								if (element('cdt_tag', $tvalue)) {
									if(!in_array(trim(element('cdt_tag', $tvalue)),$tag_array))
									array_push($tag_array,trim(element('cdt_tag', $tvalue)));
								}
							}
							
						}
						
						
						

						
						
						$crawlwhere = array(
							'cit_id' => $val,
						);
						$tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
						if ($tag && is_array($tag)) {
							
							foreach ($tag as $tvalue) {
								if (element('val_tag', $tvalue)) {
									if(!in_array(trim(element('val_tag', $tvalue)),$label_array))
									array_push($label_array,trim(element('val_tag', $tvalue)));
								}
							}
							
						}

						

						
						
					}

				
				$getdata['val_tag'] = implode("\n",$label_array);
				$getdata['cdt_tag'] = implode("\n",$tag_array);
					
				}
			}
			
			

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

			$view['view']['data']['val_tag'] = element('val_tag', $getdata);
			$view['view']['data']['cta_tag1'] = element('cdt_tag', $getdata);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = '태그 삭제';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'post_add_tag',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		} else {
			
			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);
			$cta_tag1 = $this->input->post('cta_tag1') ? $this->input->post('cta_tag1') : '';

			$cta_tag_text=array();
			
			$cta_tag_text = explode("\n",urldecode($cta_tag1));

			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {
					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id,post_id,brd_id', $postwhere, '', '');


						


						foreach ($Cmall_item as $c_key => $c_value) {


							if(count($cta_tag_text)){
							    // $deletewhere = array(
							    //     'cit_id' => $pid,
							    //     'is_manual' => 0,
							    // );
							    // $this->Crawl_tag_model->delete_where($deletewhere);            
							    if ($cta_tag_text && is_array($cta_tag_text)) {
							        foreach ($cta_tag_text as $key => $value) {


							            $value = trim($value);
							            if ($value) {

							            	$deletewhere = array(
	            						        'post_id' => element('post_id', $c_value),
	            	            	            'cit_id' => element('cit_id', $c_value),
	            	            	            'brd_id' => element('brd_id', $c_value),
	            	            	            'cta_tag' => $value,
	            						        
	            						    );
	            						    $this->Crawl_tag_model->delete_where($deletewhere);    
	            						    
						            		$countwhere = array(
						            	            'post_id' => element('post_id', $c_value),
						            	            'cit_id' => element('cit_id', $c_value),
						            	            'brd_id' => element('brd_id', $c_value),
						            	            'cdt_tag' => $value,
						            	        );
						            		$tag = $this->Crawl_delete_tag_model->get_one('','',$countwhere);
						            		if(!element('cdt_id',$tag)){
						            			
						            			$tagdata = array(
						            			    'post_id' => element('post_id', $c_value),
						            			    'cit_id' => element('cit_id', $c_value),
						            			    'brd_id' => element('brd_id', $c_value),
						            			    'cdt_tag' => $value,
						            			    // 'is_manual' => 1,
						            			);
						            			$this->Crawl_delete_tag_model->insert($tagdata);
						            		}

							                
							            }
							        }
							    }
							    
							}
						    
						}
						

						

						// $post = $this->Post_model->get_one($post_id);
						// $board = $this->board->item_all(element('brd_id', $post));


						// $chk_post_category = $this->input->post('chk_post_category', null, '');

						// $postupdate = array(
						// 	'post_category' => $chk_post_category,
						// );
						// $this->Post_model->update($post_id, $postupdate);
					}
				}
			}

			if ($cit_id_list) {
				
				$arr = explode(',', $cit_id_list);
				if ($arr) {					
					foreach($arr as $val){
						if (empty($val)) {
							continue;
						}
						
						$Cmall_item = $this->Cmall_item_model
						    ->get_one($val, 'cit_id,post_id,brd_id');

						if(count($cta_tag_text)){
							    


							    if ($cta_tag_text && is_array($cta_tag_text)) {
							        foreach ($cta_tag_text as $key => $value) {


							            $value = trim($value);
							            if ($value) {

	            						    $deletewhere = array(
	            						        'post_id' => element('post_id', $Cmall_item),
	            	            	            'cit_id' => $val,
	            	            	            'brd_id' => element('brd_id', $Cmall_item),
	            	            	            'cta_tag' => $value,
	            						        
	            						    );
	            						    $this->Crawl_tag_model->delete_where($deletewhere);            

						            		$countwhere = array(
						            	            'post_id' => element('post_id', $Cmall_item),
						            	            'cit_id' => $val,
						            	            'brd_id' => element('brd_id', $Cmall_item),
						            	            'cdt_tag' => $value,
						            	        );

						            		$tag = $this->Crawl_delete_tag_model->get_one('','',$countwhere);
						            		if(!element('cdt_id',$tag)){						            		
						            			
						            			$tagdata = array(
						            			    'post_id' => element('post_id', $Cmall_item),
						            			    'cit_id' => $val,
						            			    'brd_id' => element('brd_id', $Cmall_item),
						            			    'cdt_tag' => $value,
						            			    // 'is_manual' => 1,
						            			);
						            			$this->Crawl_delete_tag_model->insert($tagdata);
						            		}

							                
							            }
							        }
							    }
							    
							}
						    
						}
						
					}
				
				}
			
			alert_refresh_close('태그가 변경되었습니다');
		}
	}

	public function post_add_tag($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = $getdata = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Crawl_tag_model','Crawl_manual_tag_model','Cmall_item_model','Crawl_delete_tag_model','Vision_api_label_model'));

		$post_id_list = '';
		if ($this->input->post('chk_post_id')) {
			$post_id_list = '';
			$chk_post_id = $this->input->post('chk_post_id');
			foreach ($chk_post_id as $val) {
				if (empty($post_id)) {
					$post_id = $val;
				}
				$post_id_list .= $val . ',';
			}
		} elseif ($post_id) {
			$post_id_list = $post_id;
		}
		if ($this->input->post('post_id_list')) {
			$post_id_list = $this->input->post('post_id_list');
		}
		$view['view']['post_id_list'] = $post_id_list;

		$cit_id_list = '';
		if ($this->input->post('chk')) {
			$cit_id_list = '';
			$chk_cit_id = $this->input->post('chk');
			foreach ($chk_cit_id as $val) {
				if (empty($cit_id)) {
					$cit_id = $val;
				}
				$cit_id_list .= $val . ',';
			}
		} 
		if ($this->input->post('cit_id_list')) {
			$cit_id_list = $this->input->post('cit_id_list');
		}
		$view['view']['cit_id_list'] = $cit_id_list;

		
		$post = $this->Post_model->get_one($post_id);
		$board = $this->board->item_all(element('brd_id', $post));

		$view['view']['post'] = $post;
		$view['view']['board'] = $board;

		$config = array(
			array(
				'field' => 'is_submit',
				'label' => '체크',
				'rules' => 'trim',
			),
			array(
				'field' => 'brd_brand_text',
				'label' => '브랜드',
				'rules' => 'trim',
			),
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($form_validation === false OR ! $this->input->post('is_submit')) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			
			
			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {

					$getdata['cmt_tag'] = '';
					$getdata['val_tag'] = '';
					$tag_array=array();
					$label_array=array();

					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id', $postwhere, '', '');


						

						
						foreach ($Cmall_item as $c_key => $c_value) {

							
							$crawlwhere = array(
								'cit_id' => element('cit_id',$c_value),
							);
							$tag = $this->Crawl_manual_tag_model->get('', '', $crawlwhere, '', '', 'cmt_id', 'ASC');
							if ($tag && is_array($tag)) {
								
								foreach ($tag as $tvalue) {
									if (element('cmt_tag', $tvalue)) {
										if(!in_array(trim(element('cmt_tag', $tvalue)),$tag_array))
										array_push($tag_array,trim(element('cmt_tag', $tvalue)));
									}
								}
								
							}
						}
						
						

						$getdata['val_tag'] = '';
						$label_array=array();
						$crawlwhere = array(
							'cit_id' => element('cit_id',$c_value),
						);
						$tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
						if ($tag && is_array($tag)) {
							
							foreach ($tag as $tvalue) {
								if (element('val_tag', $tvalue)) {
									if(!in_array(trim(element('val_tag', $tvalue)),$label_array))
									array_push($label_array,trim(element('val_tag', $tvalue)));
								}
							}
							
						}

						
					}

					$getdata['val_tag'] = implode("\n",$label_array);
					$getdata['cmt_tag'] = implode("\n",$tag_array);
				}
			}


			if ($cit_id_list) {
				$arr = explode(',', $cit_id_list);

				$getdata['cmt_tag'] = '';
				$getdata['val_tag'] = '';
				$tag_array=array();
				$label_array=array();

				if ($arr) {
					foreach($arr as $val){
						if (empty($val)) {
							continue;
						}
						

						
						

							
						$crawlwhere = array(
							'cit_id' => $val,
						);
						$tag = $this->Crawl_manual_tag_model->get('', '', $crawlwhere, '', '', 'cmt_id', 'ASC');
						if ($tag && is_array($tag)) {
							
							foreach ($tag as $tvalue) {
								if (element('cmt_tag', $tvalue)) {
									if(!in_array(trim(element('cmt_tag', $tvalue)),$tag_array))
									array_push($tag_array,trim(element('cmt_tag', $tvalue)));
								}
							}
							
						}
						
						
						

						
						
						$crawlwhere = array(
							'cit_id' => $val,
						);
						$tag = $this->Vision_api_label_model->get('', '', $crawlwhere, '', '', 'val_id', 'ASC');
						if ($tag && is_array($tag)) {
							
							foreach ($tag as $tvalue) {
								if (element('val_tag', $tvalue)) {
									if(!in_array(trim(element('val_tag', $tvalue)),$label_array))
									array_push($label_array,trim(element('val_tag', $tvalue)));
								}
							}
							
						}

						

						
						
					}

				
				$getdata['val_tag'] = implode("\n",$label_array);
				$getdata['cmt_tag'] = implode("\n",$tag_array);
					
				}
			}
			
			

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

			$view['view']['data']['val_tag'] = element('val_tag', $getdata);
			$view['view']['data']['cta_tag1'] = element('cmt_tag', $getdata);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = '태그 추가';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'post_add_tag',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		} else {
			
			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);
			$cta_tag1 = $this->input->post('cta_tag1') ? $this->input->post('cta_tag1') : '';

			$cta_tag_text=array();
			
			$cta_tag_text = explode("\n",urldecode($cta_tag1));

			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {
					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id,post_id,brd_id', $postwhere, '', '');


						


						foreach ($Cmall_item as $c_key => $c_value) {


							if(count($cta_tag_text)){
							    // $deletewhere = array(
							    //     'cit_id' => $pid,
							    //     'is_manual' => 0,
							    // );
							    // $this->Crawl_tag_model->delete_where($deletewhere);            
							    if ($cta_tag_text && is_array($cta_tag_text)) {
							        foreach ($cta_tag_text as $key => $value) {


							            $value = trim($value);
							            if ($value) {

							            	$countwhere = array(
						            	            'post_id' => element('post_id', $c_value),
						            	            'cit_id' => element('cit_id', $c_value),
						            	            'brd_id' => element('brd_id', $c_value),
						            	            'cdt_tag' => $value,
						            	        );
						            		$dtag = $this->Crawl_delete_tag_model->get_one('','',$countwhere);

						            		if(!element('cdt_id',$dtag)){


								            	$countwhere = array(
							            	            'post_id' => element('post_id', $c_value),
							            	            'cit_id' => element('cit_id', $c_value),
							            	            'brd_id' => element('brd_id', $c_value),
							            	            'cta_tag' => $value,
							            	        );
							            		$tag = $this->Crawl_tag_model->get_one('','',$countwhere);
							            		if(!element('cta_id',$tag)){
							            			
							            			$tagdata = array(
							            			    'post_id' => element('post_id', $c_value),
							            			    'cit_id' => element('cit_id', $c_value),
							            			    'brd_id' => element('brd_id', $c_value),
							            			    'cta_tag' => $value,
							            			    // 'is_manual' => 1,
							            			);
							            			$this->Crawl_tag_model->insert($tagdata);
							            		}
							            	}
							            	// $deletewhere = array(
	            						 //        'post_id' => element('post_id', $c_value),
					            	  //           'cit_id' => element('cit_id', $c_value),
					            	  //           'brd_id' => element('brd_id', $c_value),
	            	      //       	            'cta_tag' => $value,
	            						        
	            						 //    );
	            						 //    $this->Crawl_tag_delete_model->delete_where($deletewhere); 

						            		$countwhere = array(
						            	            'post_id' => element('post_id', $c_value),
						            	            'cit_id' => element('cit_id', $c_value),
						            	            'brd_id' => element('brd_id', $c_value),
						            	            'cmt_tag' => $value,
						            	        );
						            		$tag = $this->Crawl_manual_tag_model->get_one('','',$countwhere);
						            		if(!element('cmt_id',$tag)){
						            			
						            			$tagdata = array(
						            			    'post_id' => element('post_id', $c_value),
						            			    'cit_id' => element('cit_id', $c_value),
						            			    'brd_id' => element('brd_id', $c_value),
						            			    'cmt_tag' => $value,
						            			    // 'is_manual' => 1,
						            			);
						            			$this->Crawl_manual_tag_model->insert($tagdata);
						            		}

							                
							            }
							        }
							    }
							    
							}
						    
						}
						

						

						// $post = $this->Post_model->get_one($post_id);
						// $board = $this->board->item_all(element('brd_id', $post));


						// $chk_post_category = $this->input->post('chk_post_category', null, '');

						// $postupdate = array(
						// 	'post_category' => $chk_post_category,
						// );
						// $this->Post_model->update($post_id, $postupdate);
					}
				}
			}

			if ($cit_id_list) {
				$arr = explode(',', $cit_id_list);
				if ($arr) {					
					foreach($arr as $val){
						if (empty($val)) {
							continue;
						}
						
						$Cmall_item = $this->Cmall_item_model
						    ->get_one($val, 'cit_id,post_id,brd_id');

						if(count($cta_tag_text)){
							    // $deletewhere = array(
							    //     'cit_id' => $pid,
							    //     'is_manual' => 0,
							    // );
							    // $this->Crawl_tag_model->delete_where($deletewhere);            
							    if ($cta_tag_text && is_array($cta_tag_text)) {
							        foreach ($cta_tag_text as $key => $value) {


							            $value = trim($value);
							            if ($value) {

							            	$countwhere = array(
						            	            'post_id' => element('post_id', $Cmall_item),
						            	            'cit_id' => element('cit_id', $Cmall_item),
						            	            'brd_id' => element('brd_id', $Cmall_item),
						            	            'cdt_tag' => $value,
						            	        );
						            		$dtag = $this->Crawl_delete_tag_model->get_one('','',$countwhere);

						            		if(!element('cdt_id',$dtag)){

								            	$countwhere = array(
							            	            'post_id' => element('post_id', $Cmall_item),
							            	            'cit_id' => element('cit_id', $Cmall_item),
							            	            'brd_id' => element('brd_id', $Cmall_item),
							            	            'cta_tag' => $value,
							            	        );
							            		$tag = $this->Crawl_tag_model->get_one('','',$countwhere);

							            		if(!element('cta_id',$tag)){
							            			
							            			$tagdata = array(
							            			    'post_id' => element('post_id', $Cmall_item),
							            			    'cit_id' => element('cit_id', $Cmall_item),
							            			    'brd_id' => element('brd_id', $Cmall_item),
							            			    'cta_tag' => $value,
							            			    // 'is_manual' => 1,
							            			);
							            			$this->Crawl_tag_model->insert($tagdata);
							            		}
						            		}
							            	// $deletewhere = array(
	            						 //        'post_id' => element('post_id', $Cmall_item),
					            	  //           'cit_id' => $val,
					            	  //           'brd_id' => element('brd_id', $Cmall_item),
	            	      //       	            'cta_tag' => $value,
	            						        
	            						 //    );
	            						 //    $this->Crawl_tag_delete_model->delete_where($deletewhere); 
	            						    

						            		$countwhere = array(
						            	            'post_id' => element('post_id', $Cmall_item),
						            	            'cit_id' => $val,
						            	            'brd_id' => element('brd_id', $Cmall_item),
						            	            'cmt_tag' => $value,
						            	        );

						            		$tag = $this->Crawl_manual_tag_model->get_one('','',$countwhere);
						            		if(!element('cmt_id',$tag)){						            		
						            			
						            			$tagdata = array(
						            			    'post_id' => element('post_id', $Cmall_item),
						            			    'cit_id' => $val,
						            			    'brd_id' => element('brd_id', $Cmall_item),
						            			    'cmt_tag' => $value,
						            			    // 'is_manual' => 1,
						            			);
						            			$this->Crawl_manual_tag_model->insert($tagdata);
						            		}

							                
							            }
							        }
							    }
							    
							}
						    
						}
						
					}
				
				}
			
			alert_refresh_close('태그가 변경되었습니다');
		}
	}

	public function post_change_attr($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_attr_model','Cmall_attr_rel_model','Cmall_item_model'));

		$post_id_list = '';
		if ($this->input->post('chk_post_id')) {
			$post_id_list = '';
			$chk_post_id = $this->input->post('chk_post_id');
			foreach ($chk_post_id as $val) {
				if (empty($post_id)) {
					$post_id = $val;
				}
				$post_id_list .= $val . ',';
			}
		} elseif ($post_id) {
			$post_id_list = $post_id;
		}
		if ($this->input->post('post_id_list')) {
			$post_id_list = $this->input->post('post_id_list');
		}
		$view['view']['post_id_list'] = $post_id_list;

		$cit_id_list = '';
		if ($this->input->post('chk')) {
			$cit_id_list = '';
			$chk_cit_id = $this->input->post('chk');
			foreach ($chk_cit_id as $val) {
				if (empty($cit_id)) {
					$cit_id = $val;
				}
				$cit_id_list .= $val . ',';
			}
		}
		if ($this->input->post('cit_id_list')) {
			$cit_id_list = $this->input->post('cit_id_list');
		}


		$view['view']['cit_id_list'] = $cit_id_list;


		$post = $this->Post_model->get_one($post_id);
		$board = $this->board->item_all(element('brd_id', $post));

		$view['view']['post'] = $post;
		$view['view']['board'] = $board;

		$config = array(
			array(
				'field' => 'is_submit',
				'label' => '체크',
				'rules' => 'trim',
			),
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($form_validation === false OR ! $this->input->post('is_submit')) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			
			
			
			$view['view']['data']['all_attr'] = $this->Cmall_attr_model->get_all_attr();

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = element('brd_name', $board) . ' > 특성 변경';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'post_change_attr',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		} else {
			
			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);
			$cmall_attr = $this->input->post('cmall_attr', null, '');
			if ($post_id_list) {
				$arr = explode(',', $post_id_list);
				if ($arr) {
					$arrsize = count($arr);
					for ($k= $arrsize-1; $k>= 0; $k--) {
						$post_id = element($k, $arr);
						if (empty($post_id)) {
							continue;
						}
						
						
						$postwhere['post_id'] = $post_id;
						


						
						


						



						$Cmall_item = $this->Cmall_item_model
						    ->get('', 'cit_id', $postwhere, '', '');

						foreach ($Cmall_item as $c_key => $c_value) {

						    $this->Cmall_attr_rel_model->save_attr(element('cit_id', $c_value), $cmall_attr,1);
						}
						

						

						// $post = $this->Post_model->get_one($post_id);
						// $board = $this->board->item_all(element('brd_id', $post));


						// $chk_post_attr = $this->input->post('chk_post_attr', null, '');

						// $postupdate = array(
						// 	'post_attr' => $chk_post_attr,
						// );
						// $this->Post_model->update($post_id, $postupdate);
					}
				}
			}

			if ($cit_id_list) {
				$arr = explode(',', $cit_id_list);
				if ($arr) {					
					
					foreach($arr as $val){
					
						if (empty($val)) {
							continue;
						}
						


						$this->Cmall_attr_rel_model->save_attr($val, $cmall_attr,1);
						
					}
				
				}
			}
			alert_refresh_close('특성이 변경되었습니다');
		}
	}


	public function search_category($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_category_model','Cmall_category_rel_model'));
		

	
		



		
		
		
		$view['view']['data']['all_category'] = $this->Cmall_category_model->get_all_category();



		/**
		 * 레이아웃을 정의합니다
		 */
		
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'search_category',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
		
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));

		
	}

	public function search_attr($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_attr_model'));
		

	
		



		
		
		
		$view['view']['data']['all_attr'] = $this->Cmall_attr_model->get_all_attr();



		/**
		 * 레이아웃을 정의합니다
		 */
		
		$layoutconfig = array(
			'path' => 'helptool',
			'layout' => 'layout_popup',
			'skin' => 'search_attr',
			'layout_dir' => $this->cbconfig->item('layout_helptool'),
			'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
			'skin_dir' => $this->cbconfig->item('skin_helptool'),
			'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
		
		);
		$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));

		
	}

	public function search_tag($post_id = '')
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_helptool_post_change_category';
		$this->load->event($eventname);

		$is_admin = $this->member->is_admin();

		if ($is_admin === false) {
			alert('접근권한이 없습니다');
			return false;
		}

		$view = $getdata = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$this->load->model(array('Crawl_tag_model'));

		

		

		
		

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			
			
			


			
				

			$getdata['cta_tag'] = '';
			

			
					

					
					

						
			$tag_array = array();
			$tag = $this->Crawl_tag_model->get('', '', '', '1000', '', 'cta_id', 'ASC');
			if ($tag && is_array($tag)) {
				
				foreach ($tag as $tvalue) {
					if (element('cta_tag', $tvalue)) {
						if(!in_array(trim(element('cta_tag', $tvalue)),$tag_array))
						array_push($tag_array,trim(element('cta_tag', $tvalue)));
					}
				}
				
			}
						
						

						
						
						

						

						
						
					

				
				
			$getdata['cta_tag'] = implode("\n",$tag_array);
			
			
			

			

			
			$view['view']['data']['cta_tag'] = element('cta_tag', $getdata);

			/**
			 * 레이아웃을 정의합니다
			 */
			$page_title = '검색 태그 추가';
			$layoutconfig = array(
				'path' => 'helptool',
				'layout' => 'layout_popup',
				'skin' => 'search_tag',
				'layout_dir' => $this->cbconfig->item('layout_helptool'),
				'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_helptool'),
				'skin_dir' => $this->cbconfig->item('skin_helptool'),
				'mobile_skin_dir' => $this->cbconfig->item('mobile_skin_helptool'),
				'page_title' => $page_title,
			);
			$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
			$this->data = $view;
			$this->layout = element('layout_skin_file', element('layout', $view));
			$this->view = element('view_skin_file', element('layout', $view));

		
	}
}
