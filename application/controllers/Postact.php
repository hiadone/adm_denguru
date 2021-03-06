<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Postact class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 게시물 열람 페이지에서 like, scrap,신고 등 각종 이벤트를 발생할 때 필요한 controller 입니다.
 */
class Postact extends CB_Controller
{

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array('Post');

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
		$this->load->library(array('querystring', 'accesslevel', 'email', 'notelib', 'point'));
	}


	/**
	 * 게시물 삭제하기
	 */
	public function delete($post_id = 0)
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
		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			alert('해당 게시물에서만 접근 가능합니다');
		}

		$post = $this->Post_model->get_one($post_id);

		if ( ! element('post_id', $post)) {
			show_404();
		}

		$board = $this->board->item_all(element('brd_id', $post));
		$mem_id = (int) $this->member->item('mem_id');

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if (element('block_delete', $board) && $is_admin === false) {
			alert('이 게시판의 글은 관리자에 의해서만 삭제가 가능합니다');
			return false;
		}
		if (element('protect_post_day', $board) > 0 && $is_admin === false) {
			if (ctimestamp() - strtotime(element('post_datetime', $post)) >= element('protect_post_day', $board) * 86400) {
				alert('이 게시판은 ' . element('protect_post_day', $board) . '일 이상된 게시글의 삭제를 금지합니다');
				return false;
			}
		}
		if (element('protect_comment_num', $board) > 0 && $is_admin === false) {
			if (element('protect_comment_num', $board) <= element('post_comment_count', $post)) {
				alert(element('protect_comment_num', $board) . '개 이상의 댓글이 달린 게시글은 삭제할 수 없습니다');
				return false;
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('step1', $eventname);

		if (element('mem_id', $post)) {
			if ($is_admin === false
				AND $mem_id !== abs(element('mem_id', $post))) {
				alert('회원님은 이 글을 삭제할 권한이 없습니다');
				return false;
			}
		} else {

			$view= array();
			$view['view'] = array();

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['step2'] = Events::trigger('step2', $eventname);

			if ($is_admin !== false) {
				$this->session->set_userdata(
					'can_delete_' . element('post_id', $post),
					'1'
				);
			}
			if ( ! $this->session->userdata('can_delete_' . element('post_id', $post))
				&& $this->input->post('modify_password')) {

				if ( ! function_exists('password_hash')) {
					$this->load->helper('password');
				}
				if ( password_verify($this->input->post('modify_password'), element('post_password', $post))) {
					$this->session->set_userdata(
						'can_delete_' . element('post_id', $post),
						'1'
					);
					redirect(current_url());
				} else {
					$view['view']['message'] = '패스워드가 잘못 입력되었습니다';
				}
			}
			if ( ! $this->session->userdata('can_delete_' . element('post_id', $post))) {

				// 이벤트가 존재하면 실행합니다
				$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

				/**
				 * 레이아웃을 정의합니다
				 */
				$view['view']['info'] = '게시글 삭제를 위한 패스워드 입력페이지입니다.<br />패스워드를 입력하시면 게시글 삭제가 가능합니다';

				$page_title = element('board_name', $board) . ' 글삭제';
				$layout_dir = element('board_layout', $board) ? element('board_layout', $board) : $this->cbconfig->item('layout_board');
				$mobile_layout_dir = element('board_mobile_layout', $board) ? element('board_mobile_layout', $board) : $this->cbconfig->item('mobile_layout_board');
				$use_sidebar = element('board_sidebar', $board) ? element('board_sidebar', $board) : $this->cbconfig->item('sidebar_board');
				$use_mobile_sidebar = element('board_mobile_sidebar', $board) ? element('board_mobile_sidebar', $board) : $this->cbconfig->item('mobile_sidebar_board');
				$skin_dir = element('board_skin', $board) ? element('board_skin', $board) : $this->cbconfig->item('skin_board');
				$mobile_skin_dir = element('board_mobile_skin', $board) ? element('board_mobile_skin', $board) : $this->cbconfig->item('mobile_skin_board');
				$layoutconfig = array(
					'path' => 'board',
					'layout' => 'layout',
					'skin' => 'password',
					'layout_dir' => $layout_dir,
					'mobile_layout_dir' => $mobile_layout_dir,
					'use_sidebar' => $use_sidebar,
					'use_mobile_sidebar' => $use_mobile_sidebar,
					'skin_dir' => $skin_dir,
					'mobile_skin_dir' => $mobile_skin_dir,
					'page_title' => $page_title,
				);
				$view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
				$this->data = $view;
				$this->layout = element('layout_skin_file', element('layout', $view));
				$this->view = element('view_skin_file', element('layout', $view));
				return true;
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('step3', $eventname);

		if (element('use_post_delete_log', $board)) {
			$updata = array(
				'post_del' => 1,
			);
			$this->Post_model->update(element('post_id', $post), $updata);
			$metadata = array(
				'delete_mem_id' => $mem_id,
				'delete_mem_nickname' => $this->member->item('mem_nickname'),
				'delete_datetime' => cdate('Y-m-d H:i:s'),
				'delete_ip' => $this->input->ip_address(),
			);
			$this->load->model('Post_meta_model');
			$this->Post_meta_model
				->save(element('post_id', $post), element('brd_id', $board), $metadata);
		} else {
			$this->board->delete_post($post_id);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		redirect(board_url(element('brd_key', $board)));

	}

	
	/**
	 * 목록에서 여러 게시물 선택삭제하기
	 */
	public function multi_delete($flag = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_multi_delete';
		$this->load->event($eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$post_ids = $this->input->post('chk_post_id');
		if (empty($post_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}

		foreach ($post_ids as $post_id) {
			$post_id = (int) $post_id;
			if (empty($post_id) OR $post_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$post = $this->Post_model->get_one($post_id);
			$board = $this->board->item_all(element('brd_id', $post));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			$this->board->delete_post($post_id);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array('success' => '선택된 게시글이 삭제되었습니다');
		exit(json_encode($result));

	}


	/**
	 * 댓글 삭제하기
	 */
	public function delete_comment()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_delete_comment';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$cmt_id = (int) $this->input->post('cmt_id');
		if (empty($cmt_id) OR $cmt_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		exit($this->board->delete_comment_check($cmt_id));

	}


	/**
	 * 목록에서 여러 댓글 선택삭제하기
	 */
	public function comment_multi_delete()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_multi_delete';
		$this->load->event($eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$cmt_ids = $this->input->post('chk_comment_id');
		if (empty($cmt_ids)) {
			$result = array('error' => '선택된 댓글이 없습니다.');
			exit(json_encode($result));
		}

		foreach ($cmt_ids as $cmt_id) {
			$cmt_id = (int) $cmt_id;
			if (empty($cmt_id) OR $cmt_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$return = $this->board->delete_comment_check($cmt_id, '', 1);
			$result = json_decode($return, true);
			if (element('error', $result)) {
				exit($return);
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array('success' => '선택된 댓글이 삭제되었습니다');
		exit(json_encode($result));
	}


	/**
	 * 첨부파일 다운로드 하기
	 */
	public function download($file_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_download';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$file_id = (int) $file_id;
		if (empty($file_id) OR $file_id < 1) {
			show_404();
		}

		$this->load->model(array('Post_file_model', 'Comment_model', 'Like_model'));

		$file = $this->Post_file_model->get_one($file_id);

		if ( ! element('pfi_id', $file)) {
			show_404();
		}
		if ( ! $this->session->userdata('post_id_' . element('post_id', $file))) {
			alert('해당 게시물에서만 접근 가능합니다');
		}
		$post = $this->Post_model->get_one(element('post_id', $file));
		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		$alertmessage = $this->member->is_member()
			? '회원님은 다운로드 할 수 있는 권한이 없습니다'
			: '비회원은 다운로드할 수 있는 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';
		$check = array(
			'group_id' => element('bgr_id', $board),
			'board_id' => element('brd_id', $board),
		);
		$this->accesslevel->check(
			element('access_download', $board),
			element('access_download_level', $board),
			element('access_download_group', $board),
			$alertmessage,
			$check
		);

		$mem_id = (int) $this->member->item('mem_id');

		if (element('comment_to_download', $board) && $is_admin === false
			&& $mem_id && $mem_id !== (int) element('mem_id', $post)) {
			$where = array(
				'post_id' => element('post_id', $post),
				'mem_id' => $mem_id,
			);
			$cmt_count = $this->Comment_model->count_by($where);
			if ($cmt_count === 0) {
				alert('댓글을 작성하신 후에 다운로드가 가능합니다.\\n댓글을 먼저 입력해주세요');
				return false;
			}
		}

		if (element('like_to_download', $board) && $is_admin === false
			&& $mem_id && $mem_id !== (int) element('mem_id', $post)) {
			$where = array(
				'target_id' => element('post_id', $post),
				'target_type' => 1,
				'mem_id' => $mem_id,
			);
			$like_count = $this->Like_model->count_by($where);
			if ($like_count === 0) {
				alert('추천을 하신 후에 다운로드가 가능합니다.\\n이 게시글을 먼저 추천해주세요');
				return false;
			}
		}

		if ($mem_id !== (int) element('mem_id', $post) && element('use_point', $board)) {

			$point = $this->point->insert_point(
				$mem_id,
				element('point_filedownload', $board),
				element('board_name', $board) . ' ' . element('post_id', $file) . ' 파일 다운로드',
				'filedownload',
				$file_id,
				'파일 다운로드'
			);

			if (element('point_filedownload', $board) < 0
				&& $point < 0
				&& $this->cbconfig->item('block_download_zeropoint')) {
				$this->point->delete_point(
					$mem_id,
					'filedownload',
					$file_id,
					'파일 다운로드'
				);
				alert('회원님은 포인트가 부족하므로 다운로드하실 수 없습니다. 다운로드시 ' . (element('point_filedownload', $board) * -1) . ' 포인트가 차감됩니다');
				return false;
			}
			$point = $this->point->insert_point(
				element('mem_id', $post),
				element('point_filedownload_uploader', $board),
				element('board_name', $board) . ' ' . element('post_id', $file) . ' 파일 다운로드',
				'file_uploader',
				$file_id,
				'파일 다운로드 업로더 - ' . $mem_id
			);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('step1', $eventname);

		if ( ! $this->session->userdata('post_file_download_' . element('pfi_id', $file))) {

			$this->session->set_userdata(
				'post_file_download_' . element('pfi_id', $file),
				'1'
			);

			if (element('use_download_log', $board)) {
				$insertdata = array(
					'pfi_id' => element('pfi_id', $file),
					'post_id' => element('post_id', $file),
					'brd_id' => element('brd_id', $file),
					'mem_id' => $mem_id,
					'pfd_datetime' => cdate('Y-m-d H:i:s'),
					'pfd_ip' => $this->input->ip_address(),
					'pfd_useragent' => $this->agent->agent_string(),
				);
				$this->load->model('Post_file_download_log_model');
				$this->Post_file_download_log_model->insert($insertdata);
			}
			$this->Post_file_model->update_plus(element('pfi_id', $file), 'pfi_download', 1);

		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$this->load->helper('download');

		// Read the file's contents
		$data = file_get_contents(config_item('uploads_dir') . '/post/' . element('pfi_filename', $file));
		$name = element('pfi_originname', $file);
		force_download($name, $data);

	}


	/**
	 * 링크 클릭 하기
	 */
	public function link($link_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_link';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$link_id = (int) $link_id;
		if (empty($link_id) OR $link_id < 1) {
			show_404();
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model(array('Post_link_model'));

		$link = $this->Post_link_model->get_one($link_id);

		if ( ! element('pln_id', $link)) {
			show_404();
		}
		if ( ! $this->session->userdata('post_id_' . element('post_id', $link))) {
			alert('해당 게시물에서만 접근 가능합니다');
		}

		$post = $this->Post_model->get_one(element('post_id', $link));
		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! $this->session->userdata('post_link_click_' . element('pln_id', $link))) {

			$this->session->set_userdata(
				'post_link_click_' . element('pln_id', $link),
				'1'
			);

			if (element('use_link_click_log', $board)) {
				$insertdata = array(
					'pln_id' => element('pln_id', $link),
					'post_id' => element('post_id', $link),
					'brd_id' => element('brd_id', $link),
					'mem_id' => $mem_id,
					'plc_datetime' => cdate('Y-m-d H:i:s'),
					'plc_ip' => $this->input->ip_address(),
					'plc_useragent' => $this->agent->agent_string(),
				);
				$this->load->model('Post_link_click_log_model');
				$this->Post_link_click_log_model->insert($insertdata);
			}
			$this->Post_link_model->update_plus(element('pln_id', $link), 'pln_hit', 1);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		redirect(prep_url(strip_tags(element('pln_url', $link))));

	}


	/**
	 * 게시물 추천/비추천 하기
	 */
	public function post_like($post_id = 0, $like_type = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_like';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$target_type = 1; //원글

		$result = array();
		$this->output->set_content_type('application/json');

		if ($this->member->is_member() === false) {
			$result = array('error' => '로그인 후 이용해주세요');
			exit(json_encode($result));
		}
		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$like_type = (int) $like_type;
		if ($like_type !== 1 AND $like_type !== 2) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model(array('Post_model', 'Like_model'));

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post)) {
			$result = array('error' => '삭제된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_post_like', $board) && $like_type === 1) {
			$result = array('error' => '이 게시판은 추천 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		if ( ! element('use_post_dislike', $board) && $like_type === 2) {
			$result = array('error' => '이 게시판은 비추천 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		if (abs(element('mem_id', $post)) === $mem_id) {
			$result = array('error' => '본인의 글에는 추천/비추천 기능을 사용할 수 없습니다');
			exit(json_encode($result));
		}

		$select = 'lik_id, lik_type';
		$where = array(
			'target_id' => $post_id,
			'target_type' => $target_type,
			'mem_id' => $mem_id,
		);
		$exist = $this->Like_model->get_one('', $select, $where);

		if (element('lik_id', $exist)) {
			$status = element('lik_type', $exist) === '1' ? '추천' : '비추천';
			$result = array('error' => '이미 이 글을 ' . $status . '하셨습니다');
			exit(json_encode($result));
		}

		$insertdata = array(
			'target_id' => $post_id,
			'target_type' => $target_type,
			'brd_id' => element('brd_id', $post),
			'mem_id' => $mem_id,
			'target_mem_id' => abs(element('mem_id', $post)),
			'lik_type' => $like_type,
			'lik_datetime' => cdate('Y-m-d H:i:s'),
			'lik_ip' => $this->input->ip_address(),
		);
		$this->Like_model->insert($insertdata);
		if (element('use_point', $board)) {
			if ($like_type === 1) {
				$this->point->insert_point(
					$mem_id,
					element('point_post_like', $board),
					element('board_name', $board) . ' ' . $post_id . ' 추천',
					'like',
					$post_id,
					'추천'
				);
				$this->point->insert_point(
					abs(element('mem_id', $post)),
					element('point_post_liked', $board),
					element('board_name', $board) . ' ' . $post_id . ' 추천받음',
					'liked',
					$post_id,
					'추천받음'
				);
			}
			if ($like_type === 2) {
				$this->point->insert_point(
					$mem_id,
					element('point_post_dislike', $board),
					element('board_name', $board) . ' ' . $post_id . ' 추천',
					'like',
					$post_id,
					'추천'
				);
				$this->point->insert_point(
					abs(element('mem_id', $post)),
					element('point_post_disliked', $board),
					element('board_name', $board) . ' ' . $post_id . ' 비추천받음',
					'disliked',
					$post_id,
					'비추천받음'
				);
			}
		}

		$where = array(
			'target_id' => $post_id,
			'target_type' => $target_type,
			'lik_type' => $like_type,
		);
		$count = $this->Like_model->count_by($where);


		if ($like_type === 1) {
			$field = 'post_like';
		}
		elseif ($like_type === 2) {
			$field = 'post_dislike';
		}

		$updata = array(
			$field => $count,
		);
		$this->Post_model->update($post_id, $updata);

		$status = $like_type === 1 ? '추천' : '비추천';
		$success = '이 글을 ' . $status . ' 하셨습니다';

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array('success' => $success, 'count' => $count);
		exit(json_encode($result));

	}


	/**
	 * 댓글 추천/비추천 하기
	 */
	public function comment_like($cmt_id = 0, $like_type = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_like';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$target_type = 2; //댓글

		$result = array();
		$this->output->set_content_type('application/json');

		if ($this->member->is_member() === false) {
			$result = array('error' => '로그인 후 이용해주세요');
			exit(json_encode($result));
		}

		$cmt_id = (int) $cmt_id;
		if (empty($cmt_id) OR $cmt_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$like_type = (int) $like_type;
		if ($like_type !== 1 AND $like_type !== 2) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model(array('Comment_model', 'Like_model'));

		$select = 'cmt_id, post_id, mem_id, cmt_del';
		$comment = $this->Comment_model->get_one($cmt_id, $select);

		if ( ! element('cmt_id', $comment)) {
			$result = array('error' => '존재하지 않는 댓글입니다');
			exit(json_encode($result));
		}
		if (element('cmt_del', $comment)) {
			$result = array('error' => '삭제된 댓글입니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one(element('post_id', $comment), $select);

		if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_comment_like', $board) && $like_type === 1) {
			$result = array('error' => '이 게시판은 추천 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		if ( ! element('use_comment_dislike', $board) && $like_type === 2) {
			$result = array('error' => '이 게시판은 비추천 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		if (abs(element('mem_id', $comment)) === $mem_id) {
			$result = array('error' => '본인의 글에는 추천/비추천 기능을 사용할 수 없습니다');
			exit(json_encode($result));
		}

		$select = 'lik_id, lik_type';
		$where = array(
			'target_id' => $cmt_id,
			'target_type' => $target_type,
			'mem_id' => $mem_id,
		);
		$exist = $this->Like_model->get_one('', $select, $where);

		if (element('lik_id', $exist)) {
			$status = element('lik_type', $exist) === '1' ? '추천' : '비추천';
			$result = array('error' => '이미 이 글을 ' . $status . '하셨습니다');
			exit(json_encode($result));
		}

		$insertdata = array(
			'target_id' => $cmt_id,
			'target_type' => $target_type,
			'brd_id' => element('brd_id', $post),
			'mem_id' => $mem_id,
			'target_mem_id' => abs(element('mem_id', $comment)),
			'lik_type' => $like_type,
			'lik_datetime' => cdate('Y-m-d H:i:s'),
			'lik_ip' => $this->input->ip_address(),
		);
		$this->Like_model->insert($insertdata);
		if ($like_type === 1) {
			$field = 'cmt_like';
		}
		if ($like_type === 2) {
			$field = 'cmt_dislike';
		}
		if (element('use_point', $board)) {
			if ($like_type === 1) {
				$this->point->insert_point(
					$mem_id,
					element('point_comment_like', $board),
					element('board_name', $board) . ' ' . $cmt_id . ' 추천',
					'comment_like',
					$cmt_id,
					'추천'
				);
				$this->point->insert_point(
					abs(element('mem_id', $comment)),
					element('point_comment_liked', $board),
					element('board_name', $board) . ' ' . $cmt_id . ' 추천받음',
					'comment_liked',
					$cmt_id,
					'추천받음'
				);
			}
			if ($like_type === 2) {
				$this->point->insert_point(
					$mem_id,
					element('point_comment_dislike', $board),
					element('board_name', $board) . ' ' . $cmt_id . ' 추천',
					'comment_like',
					$cmt_id,
					'추천'
				);
				$this->point->insert_point(
					abs(element('mem_id', $comment)),
					element('point_comment_disliked', $board),
					element('board_name', $board) . ' ' . $cmt_id . ' 비추천받음',
					'disliked',
					$cmt_id,
					'비추천받음'
				);
			}
		}

		$where = array(
			'target_id' => $cmt_id,
			'target_type' => $target_type,
			'lik_type' => $like_type,
		);
		$count = $this->Like_model->count_by($where);

		$updata = array(
			$field => $count
		);
		$this->Comment_model->update($cmt_id, $updata);

		$status = $like_type === 1 ? '추천' : '비추천';
		$success = '이 글을 ' . $status . ' 하셨습니다';

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array('success' => $success, 'count' => $count);
		exit(json_encode($result));

	}


	/**
	 * 게시물 스크랩 하기
	 */
	public function post_scrap($post_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_scrap';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		if ($this->member->is_member() === false) {
			$result = array('error' => '로그인 후 이용해주세요');
			exit(json_encode($result));
		}

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model('Scrap_model');

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post)) {
			$result = array('error' => '삭제된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_scrap', $board)) {
			$result = array('error' => '이 게시판은 스크랩 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$where = array(
			'post_id' => $post_id,
			'mem_id' => $mem_id,
		);
		$exist = $this->Scrap_model->get_one('', 'scr_id', $where);

		if (element('scr_id', $exist)) {
			$result = array('error' => '이미 이 글을 스크랩 하셨습니다');
			exit(json_encode($result));
		}

		$insertdata = array(
			'post_id' => $post_id,
			'brd_id' => element('brd_id', $post),
			'mem_id' => $mem_id,
			'target_mem_id' => abs(element('mem_id', $post)),
			'scr_datetime' => cdate('Y-m-d H:i:s'),
		);
		$this->Scrap_model->insert($insertdata);

		$where = array(
			'post_id' => $post_id,
		);
		$count = $this->Scrap_model->count_by($where);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = '이 글을 스크랩 하셨습니다';
		$result = array('success' => $success, 'count' => $count);
		exit(json_encode($result));

	}


	/**
	 * 게시물 신고 하기
	 */
	public function post_blame($post_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_blame';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$target_type = 1; // 원글
		$this->output->set_content_type('application/json');

		if ($this->member->is_member() === false) {
			$result = array('error' => '로그인 후 이용해주세요');
			exit(json_encode($result));
		}

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$post = $this->Post_model->get_one($post_id);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post)) {
			$result = array('error' => '삭제된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_blame', $board)) {
			$result = array('error' => '이 게시판은 신고 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$check = array(
			'group_id' => element('bgr_id', $board),
			'board_id' => element('brd_id', $board),
		);
		$can_blame = $this->accesslevel->is_accessable(
			element('access_blame', $board),
			element('access_blame_level', $board),
			element('access_blame_group', $board),
			$check
		);

		if ($can_blame === false) {
			$result = array('error' => '회원님은 신고할 수 있는 권한이 없습니다');
			exit(json_encode($result));
		}

		$where = array(
			'target_id' => $post_id,
			'target_type' => $target_type,
			'mem_id' => $mem_id,
		);
		$this->load->model('Blame_model');
		$exist = $this->Blame_model->get_one('', 'bla_id', $where);

		if (element('bla_id', $exist)) {
			$result = array('error' => '이미 이 글을 신고 하셨습니다');
			exit(json_encode($result));
		}

		$insertdata = array(
			'target_id' => $post_id,
			'target_type' => $target_type,
			'brd_id' => element('brd_id', $post),
			'mem_id' => $mem_id,
			'target_mem_id' => abs(element('mem_id', $post)),
			'bla_datetime' => cdate('Y-m-d H:i:s'),
			'bla_ip' => $this->input->ip_address(),
		);
		$this->Blame_model->insert($insertdata);

		$this->Post_model->update_plus($post_id, 'post_blame', 1);

		$count = element('post_blame', $post) + 1;

		$emailsendlistadmin = array();
		$notesendlistadmin = array();
		$smssendlistadmin = array();
		$emailsendlistpostwriter = array();
		$notesendlistpostwriter = array();
		$smssendlistpostwriter = array();

		$writer = $this->Member_model->get_one(abs(element('mem_id', $post)));

		if (element('send_email_blame_super_admin', $board)
			OR element('send_note_blame_super_admin', $board)
			OR element('send_sms_blame_super_admin', $board)) {
			$mselect = 'mem_id, mem_email, mem_nickname, mem_phone';
			$superadminlist = $this->Member_model
				->get_superadmin_list($mselect);
		}
		if (element('send_email_blame_group_admin', $board)
			OR element('send_note_blame_group_admin', $board)
			OR element('send_sms_blame_group_admin', $board)) {
			$this->load->model('Board_group_admin_model');
			$groupadminlist = $this->Board_group_admin_model
				->get_board_group_admin_member(element('bgr_id', $board));
		}
		if (element('send_email_blame_board_admin', $board)
			OR element('send_note_blame_board_admin', $board)
			OR element('send_sms_blame_board_admin', $board)) {
			$this->load->model('Board_admin_model');
			$boardadminlist = $this->Board_admin_model
				->get_board_admin_member(element('brd_id', $board));
		}

		if (element('send_email_blame_super_admin', $board) && $superadminlist) {
			foreach ($superadminlist as $key => $value) {
				$emailsendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_email_blame_group_admin', $board) && $groupadminlist) {
			foreach ($groupadminlist as $key => $value) {
				$emailsendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_email_blame_board_admin', $board) && $boardadminlist) {
			foreach ($boardadminlist as $key => $value) {
				$emailsendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_email_blame_post_writer', $board)
			&& element('mem_receive_email', $writer)) {
			$emailsendlistpostwriter['mem_email'] = element('post_email', $post);
		}
		if (element('send_note_blame_super_admin', $board) && $superadminlist) {
			foreach ($superadminlist as $key => $value) {
				$notesendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_note_blame_group_admin', $board) && $groupadminlist) {
			foreach ($groupadminlist as $key => $value) {
				$notesendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_note_blame_board_admin', $board) && $boardadminlist) {
			foreach ($boardadminlist as $key => $value) {
				$notesendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_note_blame_post_writer', $board) && element('mem_use_note', $writer)) {
			$notesendlistpostwriter = $writer;
		}
		if (element('send_sms_blame_super_admin', $board) && $superadminlist) {
			foreach ($superadminlist as $key => $value) {
				$smssendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_sms_blame_group_admin', $board) && $groupadminlist) {
			foreach ($groupadminlist as $key => $value) {
				$smssendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_sms_blame_board_admin', $board) && $boardadminlist) {
			foreach ($boardadminlist as $key => $value) {
				$smssendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_sms_blame_post_writer', $board)
			&& element('mem_phone', $writer)
			&& element('mem_receive_sms', $writer)) {
			$smssendlistpostwriter = $writer;
		}

		$searchconfig = array(
			'{홈페이지명}',
			'{회사명}',
			'{홈페이지주소}',
			'{게시글제목}',
			'{게시글내용}',
			'{게시글작성자닉네임}',
			'{게시글작성자아이디}',
			'{게시글작성시간}',
			'{게시글주소}',
			'{게시판명}',
			'{게시판주소}',
		);
		$autolink = element('use_auto_url', $board) ? true : false;
		$popup = element('content_target_blank', $board) ? true : false;
		$replaceconfig = array(
			$this->cbconfig->item('site_title'),
			$this->cbconfig->item('company_name'),
			site_url(),
			element('post_title', $post),
			display_html_content(element('post_content', $post), element('post_html', $post), element('post_image_width', $board), $autolink, $popup),
			element('post_nickname', $post),
			element('post_userid', $post),
			element('post_datetime', $post),
			post_url(element('brd_key', $board), element('post_id', $post)),
			element('brd_name', $board),
			board_url(element('brd_key', $board)),
		);
		$replaceconfig_escape = array(
			html_escape($this->cbconfig->item('site_title')),
			html_escape($this->cbconfig->item('company_name')),
			site_url(),
			html_escape(element('post_title', $post)),
			display_html_content(element('post_content', $post), element('post_html', $post), element('post_image_width', $board), $autolink, $popup),
			html_escape(element('post_nickname', $post)),
			element('post_userid', $post),
			element('post_datetime', $post),
			post_url(element('brd_key', $board), element('post_id', $post)),
			html_escape(element('brd_name', $board)),
			board_url(element('brd_key', $board)),
		);
		if ($emailsendlistadmin) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_email_blame_admin_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_email_blame_admin_content')
			);
			foreach ($emailsendlistadmin as $akey => $aval) {
				$this->email->clear(true);
				$this->email->from($this->cbconfig->item('webmaster_email'), $this->cbconfig->item('webmaster_name'));
				$this->email->to(element('mem_email', $aval));
				$this->email->subject($title);
				$this->email->message($content);
				$this->email->send();
			}
		}
		if ($emailsendlistpostwriter) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_email_blame_post_writer_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_email_blame_post_writer_content')
			);
			$this->email->clear(true);
			$this->email->from($this->cbconfig->item('webmaster_email'), $this->cbconfig->item('webmaster_name'));
			$this->email->to(element('mem_email', $emailsendlistpostwriter));
			$this->email->subject($title);
			$this->email->message($content);
			$this->email->send();
		}
		if ($notesendlistadmin) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_note_blame_admin_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_note_blame_admin_content')
			);
			foreach ($notesendlistadmin as $akey => $aval) {
				$note_result = $this->notelib->send_note(
					$sender = 0,
					$receiver = element('mem_id', $aval),
					$title,
					$content,
					1
				);
			}
		}
		if ($notesendlistpostwriter && element('mem_id', $notesendlistpostwriter)) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_note_blame_post_writer_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_note_blame_post_writer_content')
			);
			$note_result = $this->notelib->send_note(
				$sender = 0,
				$receiver = element('mem_id', $notesendlistpostwriter),
				$title,
				$content,
				1
			);
		}
		if ($smssendlistadmin) {
			if (file_exists(APPPATH . 'libraries/Smslib.php')) {
				$this->load->library(array('smslib'));
				$content = str_replace(
					$searchconfig,
					$replaceconfig,
					$this->cbconfig->item('send_sms_blame_admin_content')
				);
				$sender = array(
					'phone' => $this->cbconfig->item('sms_admin_phone'),
				);
				$receiver = array();
				foreach ($smssendlistadmin as $akey => $aval) {
					$receiver[] = array(
						'mem_id' => element('mem_id', $aval),
						'name' => element('mem_nickname', $aval),
						'phone' => element('mem_phone', $aval),
					);
				}
				$smsresult = $this->smslib->send($receiver, $sender, $content, $date = '', '게시글 신고 알림');
			}
		}
		if ($smssendlistpostwriter) {
			if (file_exists(APPPATH . 'libraries/Smslib.php')) {
				$this->load->library(array('smslib'));
				$content = str_replace(
					$searchconfig,
					$replaceconfig,
					$this->cbconfig->item('send_sms_blame_post_writer_content')
				);
				$sender = array(
					'phone' => $this->cbconfig->item('sms_admin_phone'),
				);
				$receiver = array();
				$receiver[] = $smssendlistpostwriter;
				$smsresult = $this->smslib->send($receiver, $sender, $content, $date = '', '게시글 신고 알림');
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array(
			'success' => '이 글을 신고 하셨습니다',
			'count' => $count,
		);
		exit(json_encode($result));

	}


	/**
	 * 댓글 신고 하기
	 */
	public function comment_blame($cmt_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_blame';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$target_type = 2; // 댓글
		$this->output->set_content_type('application/json');

		if ($this->member->is_member() === false) {
			$result = array('error' => '로그인 후 이용해주세요');
			exit(json_encode($result));
		}

		$cmt_id = (int) $cmt_id;
		if (empty($cmt_id) OR $cmt_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model(array('Comment_model'));

		$comment = $this->Comment_model->get_one($cmt_id);

		if ( ! element('cmt_id', $comment)) {
			$result = array('error' => '존재하지 않는 댓글입니다');
			exit(json_encode($result));
		}
		if (element('cmt_del', $comment)) {
			$result = array('error' => '삭제된 댓글입니다');
			exit(json_encode($result));
		}

		$post = $this->Post_model->get_one(element('post_id', $comment));

		if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('use_comment_blame', $board)) {
			$result = array('error' => '이 게시판은 댓글 신고 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$check = array(
			'group_id' => element('bgr_id', $board),
			'board_id' => element('brd_id', $board),
		);
		$can_blame = $this->accesslevel->is_accessable(
			element('access_blame', $board),
			element('access_blame_level', $board),
			element('access_blame_group', $board),
			$check
		);

		if ($can_blame === false) {
			$result = array('error' => '회원님은 신고할 수 있는 권한이 없습니다');
			exit(json_encode($result));
		}

		$where = array(
			'target_id' => $cmt_id,
			'target_type' => $target_type,
			'mem_id' => $mem_id,
		);
		$this->load->model('Blame_model');
		$exist = $this->Blame_model->get_one('', 'bla_id', $where);

		if (element('bla_id', $exist)) {
			$result = array('error' => '이미 이 댓글을 신고 하셨습니다');
			exit(json_encode($result));
		}

		$insertdata = array(
			'target_id' => $cmt_id,
			'target_type' => $target_type,
			'brd_id' => element('brd_id', $post),
			'mem_id' => $mem_id,
			'target_mem_id' => abs(element('mem_id', $comment)),
			'bla_datetime' => cdate('Y-m-d H:i:s'),
			'bla_ip' => $this->input->ip_address(),
		);
		$this->Blame_model->insert($insertdata);

		$this->Comment_model->update_plus($cmt_id, 'cmt_blame', 1);

		$count = element('cmt_blame', $comment) + 1;

		$emailsendlistadmin = array();
		$notesendlistadmin = array();
		$smssendlistadmin = array();
		$emailsendlistpostwriter = array();
		$notesendlistpostwriter = array();
		$smssendlistpostwriter = array();
		$emailsendlistcmtwriter = array();
		$notesendlistcmtwriter = array();
		$smssendlistcmtwriter = array();
		$post_writer = $this->Member_model->get_one(abs(element('mem_id', $post)));
		$comment_writer = $this->Member_model->get_one(abs(element('mem_id', $comment)));


		if (element('send_email_comment_blame_super_admin', $board)
			OR element('send_note_comment_blame_super_admin', $board)
			OR element('send_sms_comment_blame_super_admin', $board)) {
			$mselect = 'mem_id, mem_email, mem_nickname, mem_phone';
			$superadminlist = $this->Member_model->get_superadmin_list($mselect);
		}
		if (element('send_email_comment_blame_group_admin', $board)
			OR element('send_note_comment_blame_group_admin', $board)
			OR element('send_sms_comment_blame_group_admin', $board)) {
			$this->load->model('Board_group_admin_model');
			$groupadminlist = $this->Board_group_admin_model
				->get_board_group_admin_member(element('bgr_id', $board));
		}
		if (element('send_email_comment_blame_board_admin', $board)
			OR element('send_note_comment_blame_board_admin', $board)
			OR element('send_sms_comment_blame_board_admin', $board)) {
			$this->load->model('Board_admin_model');
			$boardadminlist = $this->Board_admin_model
				->get_board_admin_member(element('brd_id', $board));
		}
		if (element('send_email_comment_blame_super_admin', $board) && $superadminlist) {
			foreach ($superadminlist as $key => $value) {
				$emailsendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_email_comment_blame_group_admin', $board) && $groupadminlist) {
			foreach ($groupadminlist as $key => $value) {
				$emailsendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_email_comment_blame_board_admin', $board) && $boardadminlist) {
			foreach ($boardadminlist as $key => $value) {
				$emailsendlistadmin[$value['mem_id']] = $value;
			}
		}
		if ((element('mem_email', $post_writer) && element('post_receive_email', $post))
			OR (element('send_email_comment_blame_post_writer', $board) && element('mem_receive_email', $post_writer))) {
			$emailsendlistpostwriter['mem_email'] = $post['post_email'];
		}
		if (element('send_email_comment_blame_comment_writer', $board)) {
			$emailsendlistcmtwriter['mem_email'] = element('cmt_email', $comment);
		}
		if (element('send_note_comment_blame_super_admin', $board) && $superadminlist) {
			foreach ($superadminlist as $key => $value) {
				$notesendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_note_comment_blame_group_admin', $board) && $groupadminlist) {
			foreach ($groupadminlist as $key => $value) {
				$notesendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_note_comment_blame_board_admin', $board) && $boardadminlist) {
			foreach ($boardadminlist as $key => $value) {
				$notesendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_note_comment_blame_post_writer', $board)
			&& element('mem_use_note', $post_writer)) {
			$notesendlistpostwriter['mem_id'] = element('mem_id', $post_writer);
		}
		if (element('send_note_comment_blame_comment_writer', $board)
			&& element('mem_use_note', $comment_writer)) {
			$notesendlistcmtwriter['mem_id'] = element('mem_id', $comment_writer);
		}
		if (element('send_sms_comment_blame_super_admin', $board) && $superadminlist) {
			foreach ($superadminlist as $key => $value) {
				$smssendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_sms_comment_blame_group_admin', $board) && $groupadminlist) {
			foreach ($groupadminlist as $key => $value) {
				$smssendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_sms_comment_blame_board_admin', $board) && $boardadminlist) {
			foreach ($boardadminlist as $key => $value) {
				$smssendlistadmin[$value['mem_id']] = $value;
			}
		}
		if (element('send_sms_comment_blame_post_writer', $board)
			&& element('mem_phone', $post_writer)
			&& element('mem_receive_sms', $post_writer)) {
			$smssendlistpostwriter['mem_id'] = element('mem_id', $post_writer);
			$smssendlistpostwriter['mem_nickname'] = element('mem_nickname', $post_writer);
			$smssendlistpostwriter['mem_phone'] = element('mem_phone', $post_writer);
		}
		if (element('send_sms_comment_blame_comment_writer', $board)
			&& element('mem_phone', $comment_writer)
			&& element('mem_receive_sms', $comment_writer)) {
			$smssendlistcmtwriter['mem_id'] = element('mem_id', $comment_writer);
			$smssendlistcmtwriter['mem_nickname'] = element('mem_nickname', $comment_writer);
			$smssendlistcmtwriter['mem_phone'] = element('mem_phone', $comment_writer);
		}

		$searchconfig = array(
			'{홈페이지명}',
			'{회사명}',
			'{홈페이지주소}',
			'{댓글내용}',
			'{댓글작성자닉네임}',
			'{댓글작성자아이디}',
			'{댓글작성시간}',
			'{댓글주소}',
			'{게시글제목}',
			'{게시글내용}',
			'{게시글작성자닉네임}',
			'{게시글작성자아이디}',
			'{게시글작성시간}',
			'{게시글주소}',
			'{게시판명}',
			'{게시판주소}',
		);
		$autolink = element('use_auto_url', $board) ? true : false;
		$popup = element('content_target_blank', $board) ? true : false;
		$replaceconfig = array(
			$this->cbconfig->item('site_title'),
			$this->cbconfig->item('company_name'),
			site_url(),
			display_html_content(element('cmt_content', $comment), 0),
			element('cmt_nickname', $comment),
			element('cmt_userid', $comment),
			element('cmt_datetime', $comment),
			post_url(element('brd_key', $board), element('post_id', $post)) . '#comment_' . element('cmt_id', $comment),
			element('post_title', $post),
			display_html_content(element('post_content', $post), element('post_html', $post), element('post_image_width', $board), $autolink, $popup),
			element('post_nickname', $post),
			element('post_userid', $post),
			element('post_datetime', $post),
			post_url(element('brd_key', $board), element('post_id', $post)),
			element('brd_name', $board),
			board_url(element('brd_key', $board)),
		);
		$replaceconfig_escape = array(
			html_escape($this->cbconfig->item('site_title')),
			html_escape($this->cbconfig->item('company_name')),
			site_url(),
			display_html_content(element('cmt_content', $comment), 0),
			html_escape(element('cmt_nickname', $comment)),
			html_escape(element('cmt_userid', $comment)),
			element('cmt_datetime', $comment),
			post_url(element('brd_key', $board), element('post_id', $post)) . '#comment_' . element('cmt_id', $comment),
			html_escape(element('post_title', $post)),
			display_html_content(element('post_content', $post), element('post_html', $post), element('post_image_width', $board), $autolink, $popup),
			html_escape(element('post_nickname', $post)),
			element('post_userid', $post),
			element('post_datetime', $post),
			post_url(element('brd_key', $board), element('post_id', $post)),
			html_escape(element('brd_name', $board)),
			board_url(element('brd_key', $board)),
		);

		if ($emailsendlistadmin) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_email_comment_blame_admin_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_email_comment_blame_admin_content')
			);
			foreach ($emailsendlistadmin as $akey => $aval) {
				$this->email->clear(true);
				$this->email->from($this->cbconfig->item('webmaster_email'), $this->cbconfig->item('webmaster_name'));
				$this->email->to(element('mem_email', $aval));
				$this->email->subject($title);
				$this->email->message($content);
				$this->email->send();
			}
		}
		if ($emailsendlistpostwriter) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_email_comment_blame_post_writer_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_email_comment_blame_post_writer_content')
			);
			$this->email->clear(true);
			$this->email->from($this->cbconfig->item('webmaster_email'), $this->cbconfig->item('webmaster_name'));
			$this->email->to(element('mem_email', $emailsendlistpostwriter));
			$this->email->subject($title);
			$this->email->message($content);
			$this->email->send();
		}
		if ($emailsendlistcmtwriter) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_email_comment_blame_comment_writer_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_email_comment_blame_comment_writer_content')
			);
			$this->email->clear(true);
			$this->email->from($this->cbconfig->item('webmaster_email'), $this->cbconfig->item('webmaster_name'));
			$this->email->to(element('mem_email', $emailsendlistcmtwriter));
			$this->email->subject($title);
			$this->email->message($content);
			$this->email->send();
		}
		if ($notesendlistadmin) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_note_comment_blame_admin_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_note_comment_blame_admin_content')
			);
			foreach ($notesendlistadmin as $akey => $aval) {
				$note_result = $this->notelib->send_note(
					$sender = 0,
					$receiver = element('mem_id', $aval),
					$title,
					$content,
					1
				);
			}
		}
		if ($notesendlistpostwriter && element('mem_id', $notesendlistpostwriter)) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_note_comment_blame_post_writer_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_note_comment_blame_post_writer_content')
			);
			$note_result = $this->notelib->send_note(
				$sender = 0,
				$receiver = element('mem_id', $notesendlistpostwriter),
				$title,
				$content,
				1
			);
		}
		if ($notesendlistcmtwriter && element('mem_id', $notesendlistcmtwriter)) {
			$title = str_replace(
				$searchconfig,
				$replaceconfig,
				$this->cbconfig->item('send_note_comment_blame_comment_writer_title')
			);
			$content = str_replace(
				$searchconfig,
				$replaceconfig_escape,
				$this->cbconfig->item('send_note_comment_blame_comment_writer_content')
			);
			$note_result = $this->notelib->send_note(
				$sender = 0,
				$receiver = element('mem_id', $notesendlistcmtwriter),
				$title,
				$content,
				1
			);
		}
		if ($smssendlistadmin) {
			if (file_exists(APPPATH . 'libraries/Smslib.php')) {
				$this->load->library(array('smslib'));
				$content = str_replace(
					$searchconfig,
					$replaceconfig,
					$this->cbconfig->item('send_sms_comment_blame_admin_content')
				);
				$sender = array(
					'phone' => $this->cbconfig->item('sms_admin_phone'),
				);
				$receiver = array();
				foreach ($smssendlistadmin as $akey => $aval) {
					$receiver[] = array(
						'mem_id' => element('mem_id', $aval),
						'name' => element('mem_nickname', $aval),
						'phone' => element('mem_phone', $aval),
					);
				}
				$smsresult = $this->smslib->send($receiver, $sender, $content, $date = '', '댓글 신고 알림');
			}
		}
		if ($smssendlistpostwriter) {
			if (file_exists(APPPATH . 'libraries/Smslib.php')) {
				$this->load->library(array('smslib'));
				$content = str_replace(
					$searchconfig,
					$replaceconfig,
					$this->cbconfig->item('send_sms_comment_blame_post_writer_content')
				);
				$sender = array(
					'phone' => $this->cbconfig->item('sms_admin_phone'),
				);
				$receiver = array();
				$receiver[] = $smssendlistpostwriter;
				$smsresult = $this->smslib->send($receiver, $sender, $content, $date = '', '댓글 신고 알림');
			}
		}
		if ($smssendlistcmtwriter) {
			if (file_exists(APPPATH . 'libraries/Smslib.php')) {
				$this->load->library(array('smslib'));
				$content = str_replace(
					$searchconfig,
					$replaceconfig,
					$this->cbconfig->item('send_sms_comment_blame_comment_writer_content')
				);
				$sender = array(
					'phone' => $this->cbconfig->item('sms_admin_phone'),
				);
				$receiver = array();
				$receiver[] = $smssendlistcmtwriter;
				$smsresult = $this->smslib->send($receiver, $sender, $content, $date = '', '댓글 신고 알림');
			}
		}


		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array(
			'success' => '이 댓글을 신고 하셨습니다',
			'count' => $count,
		);
		exit(json_encode($result));

	}


	/**
	 * 게시물 비밀글 설정 및 해제 하기
	 */
	public function post_secret($post_id = 0, $flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_secret';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));
		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		if ( ! element('use_post_secret', $board)) {
			$result = array('error' => '이 게시판은 비밀글 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$updatedata = array(
			'post_secret' => $flag,
		);
		$this->Post_model->update($post_id, $updatedata);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '게시물을 비밀글 처리하셨습니다' : '게시물을 비밀글을 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 게시물 비밀글 설정 및 해제 하기
	 */
	public function post_multi_secret($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_secret';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_ids = $this->input->post('chk_post_id');
		if (empty($post_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($post_ids as $post_id) {
			$post_id = (int) $post_id;
			if (empty($post_id) OR $post_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id';
			$post = $this->Post_model->get_one($post_id, $select);

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));
			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			if ( ! element('use_post_secret', $board)) {
				$result = array('error' => '이 게시판은 비밀글 기능을 사용하지 않습니다');
				exit(json_encode($result));
			}

			$updatedata = array(
				'post_secret' => $flag,
			);
			$this->Post_model->update($post_id, $updatedata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '게시물을 비밀글 처리하셨습니다' : '게시물을 비밀글을 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 댓글 비밀글 설정 및 해제 하기
	 */
	public function comment_secret($cmt_id = 0, $flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_secret';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$cmt_id = (int) $cmt_id;
		if (empty($cmt_id) OR $cmt_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		$this->load->model('Comment_model');

		$select = 'cmt_id, post_id, mem_id, cmt_del';
		$comment = $this->Comment_model->get_one($cmt_id, $select);

		if ( ! element('cmt_id', $comment)) {
			$result = array('error' => '존재하지 않는 댓글입니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one(element('post_id', $comment), $select);

		if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));
		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		if ( ! element('use_comment_secret', $board)) {
			$result = array('error' => '이 게시판은 댓글에 비밀글 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$updatedata = array(
			'cmt_secret' => $flag,
		);
		$this->Comment_model->update($cmt_id, $updatedata);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '댓글을 비밀글 처리하셨습니다' : '댓글을 비밀글을 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 댓글 비밀글 설정 및 해제 하기
	 */
	public function comment_multi_secret($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_multi_secret';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$cmt_ids = $this->input->post('chk_comment_id');
		if (empty($cmt_ids)) {
			$result = array('error' => '선택된 댓글이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		$this->load->model('Comment_model');

		foreach ($cmt_ids as $cmt_id) {

			$cmt_id = (int) $cmt_id;
			if (empty($cmt_id) OR $cmt_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'cmt_id, post_id, mem_id, cmt_del';
			$comment = $this->Comment_model->get_one($cmt_id, $select);

			if ( ! element('cmt_id', $comment)) {
				$result = array('error' => '존재하지 않는 댓글입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id, post_del';
			$post = $this->Post_model->get_one(element('post_id', $comment), $select);

			if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
				$result = array('error' => '해당 게시물에서만 접근 가능합니다');
				exit(json_encode($result));
			}

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));
			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			if ( ! element('use_comment_secret', $board)) {
				$result = array('error' => '이 게시판은 댓글에 비밀글 기능을 사용하지 않습니다');
				exit(json_encode($result));
			}

			$updatedata = array(
				'cmt_secret' => $flag,
			);
			$this->Comment_model->update($cmt_id, $updatedata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '댓글을 비밀글 처리하셨습니다' : '댓글을 비밀글을 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 댓글 감춤/해제 하기
	 */
	public function post_hide_comment($post_id = 0, $flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_hide_comment';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		$updatedata = array(
			'post_hide_comment' => $flag,
		);
		$this->Post_model->update($post_id, $updatedata);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '댓글감춤 처리를 하였습니다' : '댓글감춤을 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 공지사항 올림/내림 설정
	 */
	public function post_notice($post_id = 0, $flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_notice';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$flag = ((int) $flag === 1) ? 1 : 0;

		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post)) {
			$result = array('error' => '삭제된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		$updatedata = array(
			'post_notice' => $flag,
		);
		$this->Post_model->update($post_id, $updatedata);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 게시글을 공지로 등록하였습니다' : '이 게시글을 공지에서 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 여러 게시물의 공지사항 올림/내림 설정
	 */
	public function post_multi_notice($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_notice';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_ids = $this->input->post('chk_post_id');
		if (empty($post_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($post_ids as $post_id) {
			$post_id = (int) $post_id;
			if (empty($post_id) OR $post_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id, post_del';
			$post = $this->Post_model->get_one($post_id, $select);

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}
			if (element('post_del', $post)) {
				$result = array('error' => '삭제된 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			$updatedata = array(
				'post_notice' => $flag,
			);
			$this->Post_model->update($post_id, $updatedata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 게시글을 공지로 등록하였습니다' : '이 게시글을 공지에서 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 게시물 블라인드 설정/해제하기
	 */
	public function post_blame_blind($post_id = 0, $flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id, post_blame, post_del';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post)) {
			$result = array('error' => '삭제된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		if ( ! element('use_blame', $board)) {
			$result = array('error' => '이 게시판은 신고 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$blame_count = $flag ? element('blame_blind_count', $board): 0;
		$updatedata = array(
			'post_blame' => $blame_count,
		);
		$this->Post_model->update($post_id, $updatedata);

		$postwhere = array(
            'post_id' => $post_id,
        );
		$this->load->model(array('Cmall_item_model'));
		$cresult['list'] = $this->Cmall_item_model
		    ->get('', '', $postwhere);
		if (element('list', $cresult)) {			
		    foreach (element('list', $cresult) as $ckey => $cval){ 


		    	

		    	
		    	$cit_id = (int) element('cit_id',$cval);
		    	if (empty($cit_id) OR $cit_id < 1) {
		    		$result = array('error' => '잘못된 접근입니다');
		    		exit(json_encode($result));
		    	}

		    	$select = 'cit_id,post_id, brd_id,  cit_status';
		    	$cmail = $this->Cmall_item_model->get_one($cit_id, $select);

		    	if ( ! element('cit_id', $cmail)) {
		    		$result = array('error' => '존재하지 않는 게시물입니다');
		    		exit(json_encode($result));
		    	}
		    	

		    	$cit_status = ((int) $flag === 1) ? 0 : 1;
		    	$updatedata = array(
		    		'cit_status' => $cit_status,
		    	);
		    	$this->Cmall_item_model->update($cit_id, $updatedata);

		    }
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 게시글을 블라인드 처리하였습니다' : '이 게시글을 블라인드 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 게시물 블라인드 설정/해제하기
	 */
	public function post_multi_blame_blind($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_ids = $this->input->post('chk_post_id');
		if (empty($post_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($post_ids as $post_id) {
			$post_id = (int) $post_id;
			if (empty($post_id) OR $post_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id, post_blame, post_del';
			$post = $this->Post_model->get_one($post_id, $select);

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}
			if (element('post_del', $post)) {
				$result = array('error' => '삭제된 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			if ( ! element('use_blame', $board)) {
				$result = array('error' => '이 게시판은 신고 기능을 사용하지 않습니다');
				exit(json_encode($result));
			}

			$blame_count = $flag ? element('blame_blind_count', $board): 0;
			$updatedata = array(
				'post_blame' => $blame_count,
			);
			$this->Post_model->update($post_id, $updatedata);

			$postwhere = array(
                'post_id' => $post_id,
            );
			$this->load->model(array('Cmall_item_model'));
			$cresult['list'] = $this->Cmall_item_model
			    ->get('', '', $postwhere);
			if (element('list', $cresult)) {
				
			    foreach (element('list', $cresult) as $ckey => $cval){ 


			    	

			    	
			    	$cit_id = (int) element('cit_id',$cval);
			    	if (empty($cit_id) OR $cit_id < 1) {
			    		$result = array('error' => '잘못된 접근입니다');
			    		exit(json_encode($result));
			    	}

			    	$select = 'cit_id,post_id, brd_id,  cit_status';
			    	$cmail = $this->Cmall_item_model->get_one($cit_id, $select);

			    	if ( ! element('cit_id', $cmail)) {
			    		$result = array('error' => '존재하지 않는 게시물입니다');
			    		exit(json_encode($result));
			    	}
			    	

			    	$cit_status = ((int) $flag === 1) ? 0 : 1;
			    	$updatedata = array(
			    		'cit_status' => $cit_status,
			    	);
			    	$this->Cmall_item_model->update($cit_id, $updatedata);

			    }
			}

		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 게시글을 블라인드 처리하였습니다' : '이 게시글을 블라인드 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 댓글 블라인드 설정/해제하기
	 */
	public function comment_blame_blind($cmt_id = 0, $flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$cmt_id = (int) $cmt_id;
		if (empty($cmt_id) OR $cmt_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		$this->load->model('Comment_model');

		$select = 'cmt_id, post_id, mem_id, cmt_blame, cmt_del';
		$comment = $this->Comment_model->get_one($cmt_id, $select);

		if ( ! element('cmt_id', $comment)) {
			$result = array('error' => '존재하지 않는 댓글입니다');
			exit(json_encode($result));
		}
		if (element('cmt_del', $comment)) {
			$result = array('error' => '삭제된 댓글입니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id, post_blame, post_del';
		$post = $this->Post_model->get_one(element('post_id', $comment), $select);

		if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post)) {
			$result = array('error' => '삭제된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		if ( ! element('use_comment_blame', $board)) {
			$result = array('error' => '이 게시판은 댓글에 신고 기능을 사용하지 않습니다');
			exit(json_encode($result));
		}

		$blame_count = $flag ? element('blame_blind_count', $board): 0;
		$updatedata = array(
			'cmt_blame' => $blame_count,
		);
		$this->Comment_model->update($cmt_id, $updatedata);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 댓글을 블라인드 처리하였습니다' : '이 댓글을 블라인드 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 댓글 블라인드 설정/해제하기
	 */
	public function comment_multi_blame_blind($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$cmt_ids = $this->input->post('chk_comment_id');
		if (empty($cmt_ids)) {
			$result = array('error' => '선택된 댓글이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		$this->load->model('Comment_model');

		foreach ($cmt_ids as $cmt_id) {
			$cmt_id = (int) $cmt_id;
			if (empty($cmt_id) OR $cmt_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'cmt_id, post_id, mem_id, cmt_blame, cmt_del';
			$comment = $this->Comment_model->get_one($cmt_id, $select);

			if ( ! element('cmt_id', $comment)) {
				$result = array('error' => '존재하지 않는 댓글입니다');
				exit(json_encode($result));
			}
			if (element('cmt_del', $comment)) {
				$result = array('error' => '삭제된 댓글입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id, post_blame, post_del';
			$post = $this->Post_model->get_one(element('post_id', $comment), $select);

			if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
				$result = array('error' => '해당 게시물에서만 접근 가능합니다');
				exit(json_encode($result));
			}

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}
			if (element('post_del', $post)) {
				$result = array('error' => '삭제된 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			if ( ! element('use_comment_blame', $board)) {
				$result = array('error' => '이 게시판은 댓글에 신고 기능을 사용하지 않습니다');
				exit(json_encode($result));
			}

			$blame_count = $flag ? element('blame_blind_count', $board): 0;
			$updatedata = array(
				'cmt_blame' => $blame_count,
			);
			$this->Comment_model->update($cmt_id, $updatedata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 댓글을 블라인드 처리하였습니다' : '이 댓글을 블라인드 해제하셨습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	/**
	 * 게시물 휴지통으로 이동하기
	 */
	public function post_trash($post_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_trash';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();

		$this->output->set_content_type('application/json');

		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}
		if ( ! $this->session->userdata('post_id_' . $post_id)) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		$this->load->model(array('Post_meta_model', 'Comment_model', 'Comment_meta_model'));

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one($post_id, $select);

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post) === '1') {
			$result = array('error' => '이미 삭제된 게시물입니다');
			exit(json_encode($result));
		}
		if (element('post_del', $post) === '2') {
			$result = array('error' => '이미 휴지통으로 이동된 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$updatedata = array(
			'post_del' => 2,
		);
		$this->Post_model->update($post_id, $updatedata);
		$metadata = array(
			'trash_mem_id' => $mem_id,
			'trash_datetime' => cdate('Y-m-d H:i:s'),
			'trash_ip' => $this->input->ip_address(),
		);
		$this->Post_meta_model->save($post_id, element('brd_id', $board), $metadata);

		$where = array(
			'post_id' => $post_id,
		);
		$cmts = $this->Comment_model->get('', 'cmt_id', $where);
		if ($cmts && is_array($cmts)) {
			foreach ($cmts as $cmt) {
				$cmt_id = element('cmt_id', $cmt);
				$updatedata = array(
					'cmt_del' => 2,
				);
				$this->Comment_model->update($cmt_id, $updatedata);
				$metadata = array(
					'trash_mem_id' => $mem_id,
					'trash_datetime' => cdate('Y-m-d H:i:s'),
					'trash_ip' => $this->input->ip_address(),
				);
				$this->Comment_meta_model->save($cmt_id, $metadata);
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = '이 게시글을 휴지통으로 이동하였습니다';
		$result = array(
			'success' => $success,
			'url' => board_url(element('brd_key', $board)),
		);
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 게시물 휴지통으로 이동하기
	 */
	public function post_multi_trash($flag = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_trash';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$post_ids = $this->input->post('chk_post_id');
		if (empty($post_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}

		$this->load->model(array('Post_meta_model', 'Comment_model', 'Comment_meta_model'));

		foreach ($post_ids as $post_id) {

			$post_id = (int) $post_id;
			if (empty($post_id) OR $post_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id, post_del';
			$post = $this->Post_model->get_one($post_id, $select);

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}
			if (element('post_del', $post) === '1') {
				$result = array('error' => '이미 삭제된 게시물입니다');
				exit(json_encode($result));
			}
			if (element('post_del', $post) === '2') {
				$result = array('error' => '이미 휴지통으로 이동된 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			$mem_id = (int) $this->member->item('mem_id');

			$updatedata = array(
				'post_del' => 2,
			);
			$this->Post_model->update($post_id, $updatedata);
			$metadata = array(
				'trash_mem_id' => $mem_id,
				'trash_datetime' => cdate('Y-m-d H:i:s'),
				'trash_ip' => $this->input->ip_address(),
			);
			$this->Post_meta_model->save($post_id, element('brd_id', $board), $metadata);

			$where = array(
				'post_id' => $post_id,
			);
			$cmts = $this->Comment_model->get('', 'cmt_id', $where);
			if ($cmts && is_array($cmts)) {
				foreach ($cmts as $cmt) {
					$cmt_id = element('cmt_id', $cmt);
					$updatedata = array(
						'cmt_del' => 2
					);
					$this->Comment_model->update($cmt_id, $updatedata);
					$metadata = array(
						'trash_mem_id' => $mem_id,
						'trash_datetime' => cdate('Y-m-d H:i:s'),
						'trash_ip' => $this->input->ip_address(),
					);
					$this->Comment_meta_model->save($cmt_id, $metadata);
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array('success' => '해당 게시글을 휴지통으로 이동하였습니다');
		exit(json_encode($result));

	}


	/**
	 * 댓글 휴지통으로 이동하기
	 */
	public function comment_trash($cmt_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_trash';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();

		$this->output->set_content_type('application/json');

		$cmt_id = (int) $cmt_id;
		if (empty($cmt_id) OR $cmt_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$this->load->model(array('Comment_model', 'Comment_meta_model'));

		$select = 'cmt_id, post_id, mem_id, cmt_del';
		$comment = $this->Comment_model->get_one($cmt_id, $select);

		if ( ! element('cmt_id', $comment)) {
			$result = array('error' => '존재하지 않는 댓글입니다');
			exit(json_encode($result));
		}
		if (element('cmt_del', $comment) === '1') {
			$result = array('error' => '이미 삭제된 댓글입니다');
			exit(json_encode($result));
		}
		if (element('cmt_del', $comment) === '2') {
			$result = array('error' => '이미 휴지통으로 이동된 댓글입니다');
			exit(json_encode($result));
		}

		$select = 'post_id, brd_id, mem_id, post_del';
		$post = $this->Post_model->get_one(element('post_id', $comment), $select);

		if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			exit(json_encode($result));
		}

		if ( ! element('post_id', $post)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $post));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		$mem_id = (int) $this->member->item('mem_id');

		$updatedata = array(
			'cmt_del' => 2,
		);
		$this->Comment_model->update($cmt_id, $updatedata);
		$metadata = array(
			'trash_mem_id' => $mem_id,
			'trash_datetime' => cdate('Y-m-d H:i:s'),
			'trash_ip' => $this->input->ip_address(),
		);
		$this->Comment_meta_model->save($cmt_id, $metadata);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = '이 댓글을 휴지통으로 이동하였습니다';
		$result = array(
			'success' => $success,
			'url' => post_url(element('brd_key', $board), element('post_id', $post)),
		);
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 댓글 휴지통으로 이동하기
	 */
	public function comment_multi_trash()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_comment_multi_trash';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		$cmt_ids = $this->input->post('chk_comment_id');
		if (empty($cmt_ids)) {
			$result = array('error' => '선택된 댓글이 없습니다.');
			exit(json_encode($result));
		}

		$this->load->model(array('Comment_model', 'Comment_meta_model'));

		foreach ($cmt_ids as $cmt_id) {

			$cmt_id = (int) $cmt_id;
			if (empty($cmt_id) OR $cmt_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$select = 'cmt_id, post_id, mem_id, cmt_del';
			$comment = $this->Comment_model->get_one($cmt_id, $select);

			if ( ! element('cmt_id', $comment)) {
				$result = array('error' => '존재하지 않는 댓글입니다');
				exit(json_encode($result));
			}
			if (element('cmt_del', $comment) === '1') {
				$result = array('error' => '이미 삭제된 댓글입니다');
				exit(json_encode($result));
			}
			if (element('cmt_del', $comment) === '2') {
				$result = array('error' => '이미 휴지통으로 이동된 댓글입니다');
				exit(json_encode($result));
			}

			$select = 'post_id, brd_id, mem_id, post_del';
			$post = $this->Post_model->get_one(element('post_id', $comment), $select);

			if ( ! $this->session->userdata('post_id_' . element('post_id', $comment))) {
				$result = array('error' => '해당 게시물에서만 접근 가능합니다');
				exit(json_encode($result));
			}

			if ( ! element('post_id', $post)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $post));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			$mem_id = (int) $this->member->item('mem_id');

			$updatedata = array(
				'cmt_del' => 2,
			);
			$this->Comment_model->update($cmt_id, $updatedata);
			$metadata = array(
				'trash_mem_id' => $mem_id,
				'trash_datetime' => cdate('Y-m-d H:i:s'),
				'trash_ip' => $this->input->ip_address(),
			);
			$this->Comment_meta_model->save($cmt_id, $metadata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = '선택하신 댓글을 휴지통으로 이동하였습니다';
		$result = array('success' => $success);
		exit(json_encode($result));

	}



	/**
	 * 스팸 키워드 체크하기
	 */
	public function filter_spam_keyword()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_filter_spam_keyword';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->output->set_content_type('application/json');

		$title = strip_tags($this->input->post('title'));
		$content = strip_tags($this->input->post('content'));

		$spam_word = explode(',', trim($this->cbconfig->item('spam_word')));
		$return_title = '';
		$return_content = '';

		if ($spam_word) {
			for ($i = 0; $i < count($spam_word); $i++) {
				$str = trim($spam_word[$i]);
				if ($title) {
					$pos = stripos($title, $str);
					if ($pos !== false) {
						$return_title = $str;
						break;
					}
				}
				if ($content) {
					$pos = stripos($content, $str);
					if ($pos !== false) {
						$return_content = $str;
						break;
					}
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$return = array(
			'title' => $return_title,
			'content' => $return_content,
		);
		$json = json_encode($return);

		exit($json);
	}


	/**
	 * 게시물 임시저장하기
	 */
	public function tempsave()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_tempsave';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->output->set_content_type('application/json');

		$brd_key = trim(urldecode($this->input->post('brd_key')));
		$brd_id = $this->board->item_key('brd_id', $brd_key);
		$post_title = trim(urldecode($this->input->post('post_title')));
		$post_content = trim(urldecode($this->input->post('post_content')));

		if (empty($brd_id)) {
			exit();
		}
		if (empty($post_content)
			OR $post_content === '<p>&nbsp;</p>'
			OR $post_content === '<div>&nbsp;</div>'
			OR $post_content === '&nbsp;') {
			exit();
		}
		if ($this->member->is_member() === false) {
			exit();
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model('Tempsave_model');

		$where = array(
			'brd_id' => $brd_id,
			'mem_id' => $mem_id,
		);
		$result = $this->Tempsave_model->get_one('', '', $where);

		if (element('tmp_id', $result)) {
			$updatedata = array(
				'tmp_title' => $post_title,
				'tmp_content' => $post_content,
				'tmp_ip' => $this->input->ip_address(),
				'tmp_datetime' => cdate('Y-m-d H:i:s'),
			);
			$this->Tempsave_model->update(element('tmp_id', $result), $updatedata);
		} else {
			$insertdata = array(
				'brd_id' => $brd_id,
				'tmp_title' => $post_title,
				'tmp_content' => $post_content,
				'mem_id' => $mem_id,
				'tmp_ip' => $this->input->ip_address(),
				'tmp_datetime' => cdate('Y-m-d H:i:s'),
			);
			$this->Tempsave_model->insert($insertdata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$return = array('success' => 'ok');
		$json = json_encode($return);

		exit($json);
	}


	/**
	 * 임시저장되어있는 게시물 불러오기
	 */
	public function get_tempsave()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_get_tempsave';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->output->set_content_type('application/json');

		$brd_key = trim(urldecode($this->input->post('brd_key')));
		$brd_id = $this->board->item_key('brd_id', $brd_key);

		if (empty($brd_id)) {
			return;
		}
		if ($this->member->is_member() === false) {
			return;
		}
		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model('Tempsave_model');

		$where = array(
			'brd_id' => $brd_id,
			'mem_id' => $mem_id,
		);
		$result = $this->Tempsave_model->get_one('', 'tmp_title, tmp_content', $where);
		$result['success'] = 'ok';

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$json = json_encode($result);

		exit($json);
	}


	/**
	 * 게시물을 네이버에 핑 보내는 페이지입니다
	 */
	public function naversyndi($post_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_naversyndi';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		if ( ! $this->cbconfig->item('naver_syndi_key')) {
			die('신디케이션 키가 입력되지 않았습니다');
		}
		$post_id = (int) $post_id;
		if (empty($post_id) OR $post_id < 1) {
			show_404();
		}

		$post = $this->Post_model->get_one($post_id);

		if ( ! element('post_id', $post)) {
			show_404();
		}
		if (element('post_del', $post)) {
			show_404();
		}

		$board = $this->board->item_all(element('brd_id', $post));

		if ( ! element('brd_id', $board)) {
			show_404();
		}

		if ( ! element('use_naver_syndi', $board)) {
			die('이 게시판은 신디케이션 기능을 사용하지 않습니다');
		}
		if (element('access_view', $board)) {
			die('비회원이 읽기가 가능한 게시판만 신디케이션을 지원합니다');
		}
		if (element('post_secret', $post)) {
			die('비밀글은 신디케이션을 지원하지 않습니다');
		}
		if (element('use_personal', $board)) {
			die('1:1 게시판은 신디케이션을 지원하지 않습니다');
		}

		$site_url = trim(site_url(), '/');

		$post_content = display_html_content(
			element('post_content', $post),
			element('post_html', $post),
			element('post_image_width', $board),
			element('use_auto_url', $board),
			element('content_target_blank', $board)
		);
		$content = str_replace(array('&amp;', '&nbsp;'), array('&', ' '), $post_content);
		$summary = str_replace(array('&amp;', '&nbsp;'), array('&', ' '), html_escape(strip_tags(element('post_content', $post))));


		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		header('content-type: text/xml');
		header('cache-control: no-cache, must-revalidate');
		header('pragma: no-cache');

		$xml = "";
		$xml .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<feed xmlns=\"http://webmastertool.naver.com\">\n";
		$xml .= "<id>" . $site_url . "</id>\n";
		$xml .= "<title>naver syndication feed document</title>\n";
		$xml .= "<author>\n";
		$xml .= "<name>webmaster</name>\n";
		$xml .= "</author>\n";
		$xml .= "<updated>" . cdate('Y-m-d\TH:i:s\+09:00') . "</updated>\n";
		$xml .= "<link rel=\"site\" href=\"" . $site_url . "\" title=\"" . html_escape($this->cbconfig->item('site_title')) . "\" />\n";
		$xml .= "<entry>\n";
		$xml .= "<id>" . post_url(element('brd_key', $board), $post_id) . "</id>\n";
		$xml .= "<title><![CDATA[" . html_escape(element('post_title', $post)) . "]]></title>\n";
		$xml .= "<author>\n";
		$xml .= "<name>" . html_escape(element('post_nickname', $post)) . "</name>\n";
		$xml .= "</author>\n";
		$xml .= "<updated>" .date('Y-m-d\TH:i:s\+09:00', strtotime(element('post_updated_datetime', $post))) . "</updated>\n";
		$xml .= "<published>" . date('Y-m-d\TH:i:s\+09:00', strtotime(element('post_datetime', $post))) . "</published>\n";
		$xml .= "<link rel=\"via\" href=\"" . board_url(element('brd_key', $board)) . "\" title=\"" . html_escape(element('brd_name', $board)) . "\" />\n";
		$xml .= "<link rel=\"mobile\" href=\"" . post_url(element('brd_key', $board), $post_id) . "\" />\n";
		$xml .= "<content type=\"html\"><![CDATA[{$content}]]></content>\n";
		$xml .= "<summary type=\"text\"><![CDATA[{$summary}]]></summary>\n";
		$xml .= "<category term=\"" . element('brd_key', $board) . "\" label=\"" . html_escape(element('brd_name', $board)) . "\" />\n";
		$xml .= "</entry>\n";
		$xml .= "</feed>";

		echo $xml;
	}


	/**
	 * 링크 클릭 하기
	 */
	public function cit_link($cit_id = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_link';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$cit_id = (int) $cit_id;
		$mem_id = (int) $this->member->item('mem_id');
		if (empty($cit_id) OR $cit_id < 1) {
			show_404();
		}

		

		$this->load->model(array('Cmall_item_model'));

		$link = $this->Cmall_item_model->get_one($cit_id);


		if ( ! element('post_id', $link)) {
			show_404();
		}
		

		$post = $this->Post_model->get_one(element('post_id', $link));
		$board = $this->board->item_all(element('brd_id', $post));
		
		if ( ! $this->session->userdata('crawl_link_click_' . element('cit_id', $link))) {

			$this->session->set_userdata(
				'crawl_link_click_' . element('cit_id', $link),
				'1'
			);

			if (element('use_link_click_log', $board)) {
				
				$insertdata = array(
					'pln_id' => element('pln_id', $link),
					'post_id' => element('post_id', $link),
					'brd_id' => element('brd_id', $link),
					'cit_id' => element('cit_id', $link),
					'clc_datetime' => cdate('Y-m-d H:i:s'),
					'clc_ip' => $this->input->ip_address(),
					'clc_useragent' => $this->agent->agent_string(),
					'mem_id' => $mem_id,
				);
				$this->load->model('Crawl_link_click_log_model');
				$this->Crawl_link_click_log_model->insert($insertdata);
				
			}
			$this->Cmall_item_model->update_plus(element('cit_id', $link), 'cit_hit', 1);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		redirect(prep_url(strip_tags(element('cit_post_url', $link))));

	}


	public function cta_tag_update($cit_id = 0, $cta_tag = '')
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_postact_post_extra';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $result = array();
        $this->output->set_content_type('application/json');

        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            $result = array('error' => '잘못된 접근입니다');
            exit(json_encode($result));
        }


        
        $this->load->model(array('Crawl_tag_model','Cmall_item_model','Crawl_delete_tag_model','Crawl_manual_tag_model'));

        $crawltagwhere = array(
			'cit_id' => $cit_id,
		);

        $select = 'cit_id,post_id,brd_id';

        $cmail_item = $this->Cmall_item_model->get_one('',$select,$crawltagwhere);
        
        
        if ( ! element('cit_id', $cmail_item)) {
            $result = array('error' => '존재하지 않는 항목입니다');
            exit(json_encode($result));
        }


        $is_admin = $this->member->is_admin();

        if ($is_admin === false &&  $this->member->item('mem_level') < 1) {
            $result = array('error' => '접근권한이 없습니다');
            exit(json_encode($result));
        }

        $cta_tag_text=array();
        
        $cta_tag_text = explode("\n",urldecode($cta_tag));
        
        if(count($cta_tag_text)){
            $deletewhere = array(
                'cit_id' => element('cit_id', $cmail_item),
            );
            $this->Crawl_tag_model->delete_where($deletewhere);            
            if ($cta_tag_text && is_array($cta_tag_text)) {
                foreach ($cta_tag_text as $key => $value) {
                    $value = trim($value);
                    if ($value) {

                    	$where = array(
                    	            
		                            'cit_id' => element('cit_id', $cmail_item),
		                            
                    	            'cta_tag' => $value,
                    	        );

                    	

                    	if(!$this->Crawl_tag_model->count_by($where)) {

                    		$where = array(
                    	            
		                            'cit_id' => element('cit_id', $cmail_item),
		                            
                    	            'cdt_tag' => $value,
                    	        );

                    		

                    		if(!$this->Crawl_delete_tag_model->count_by($where)) {


                    			$where = array(
                    	            
		                            'cit_id' => element('cit_id', $cmail_item),
		                            
                    	            'cmt_tag' => $value,
                    	        );
                    			if($this->Crawl_manual_tag_model->count_by($where)) {
			                        $tagdata = array(
			                            'post_id' => element('post_id', $cmail_item),
			                            'cit_id' => element('cit_id', $cmail_item),
			                            'brd_id' => element('brd_id', $cmail_item),
			                            'cta_tag' => $value,
			                            'is_manual' => 1,
			                        );
			                        $this->Crawl_tag_model->insert($tagdata);
		                    	} else {
		                    		$tagdata = array(
			                            'post_id' => element('post_id', $cmail_item),
			                            'cit_id' => element('cit_id', $cmail_item),
			                            'brd_id' => element('brd_id', $cmail_item),
			                            'cta_tag' => $value,
			                            // 'is_manual' => 1,
			                        );
			                        $this->Crawl_tag_model->insert($tagdata);
		                    	}
		                    }
                    	}
                    }
                }
            }
            
        }
        
        
       	
        
        $result = array('success' => '저장되었습니다');
                exit(json_encode($result));

    }

    public function cmt_tag_update($cit_id = 0, $cmt_tag = '')
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_postact_post_extra';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $result = array();
        $this->output->set_content_type('application/json');

        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            $result = array('error' => '잘못된 접근입니다');
            exit(json_encode($result));
        }


        
        $this->load->model(array('Crawl_manual_tag_model','Crawl_tag_model','Crawl_delete_tag_model','Cmall_item_model'));

        $crawltagwhere = array(
			'cit_id' => $cit_id,
		);

        $select = 'cit_id,post_id,brd_id';

        $cmail_item = $this->Cmall_item_model->get_one('',$select,$crawltagwhere);
        
        
        if ( ! element('cit_id', $cmail_item)) {
            $result = array('error' => '존재하지 않는 항목입니다');
            exit(json_encode($result));
        }


        $is_admin = $this->member->is_admin();

        if ($is_admin === false &&  $this->member->item('mem_level') < 1) {
            $result = array('error' => '접근권한이 없습니다');
            exit(json_encode($result));
        }

        $cmt_tag_text=array();
        
        $cmt_tag_text = explode("\n",urldecode($cmt_tag));
        
        if(count($cmt_tag_text)){
            $deletewhere = array(
                'cit_id' => element('cit_id', $cmail_item),
            );
            $this->Crawl_manual_tag_model->delete_where($deletewhere);


            $deletewhere = array(
                'cit_id' => element('cit_id', $cmail_item),
                'is_manual' => 1,
            );
            $this->Crawl_tag_model->delete_where($deletewhere);
            if ($cmt_tag_text && is_array($cmt_tag_text)) {
                foreach ($cmt_tag_text as $key => $value) {
                    $value = trim($value);
                    if ($value) {

                    	$where = array(
                	            // 'post_id' => element('post_id', $cmail_item),
	                            'cit_id' => element('cit_id', $cmail_item),
	                            // 'brd_id' => element('brd_id', $cmail_item),
                	            'cmt_tag' => $value,
                	        );

                    	

                    	if(!$this->Crawl_manual_tag_model->count_by($where)) {

	                        $tagdata = array(
	                            'post_id' => element('post_id', $cmail_item),
	                            'cit_id' => element('cit_id', $cmail_item),
	                            'brd_id' => element('brd_id', $cmail_item),
	                            'cmt_tag' => $value,
	                            // 'is_manual' => 1,
	                        );
	                        $this->Crawl_manual_tag_model->insert($tagdata);
                    	}


		            	$countwhere = array(
	            	            // 'post_id' => element('post_id', $cmail_item),
	            	            'cit_id' => element('cit_id', $cmail_item),
	            	            // 'brd_id' => element('brd_id', $cmail_item),
	            	            'cdt_tag' => $value,
	            	        );
	            		$dtag = $this->Crawl_delete_tag_model->get_one('','',$countwhere);

	            		if(!element('cdt_id',$dtag)){

	            			$where = array(
                	            // 'post_id' => element('post_id', $cmail_item),
	            			    'cit_id' => element('cit_id', $cmail_item),
	            			    // 'brd_id' => element('brd_id', $cmail_item),
	            			    'cta_tag' => $value,
                	        );

                    	

                    		if(!$this->Crawl_tag_model->count_by($where)) {
		            			$tagdata = array(
		            			    'post_id' => element('post_id', $cmail_item),
		            			    'cit_id' => element('cit_id', $cmail_item),
		            			    'brd_id' => element('brd_id', $cmail_item),
		            			    'cta_tag' => $value,
		            			    'is_manual' => 1,
		            			);
		            			$this->Crawl_tag_model->insert($tagdata);
	            			}
	            		}
		            		                
							            
                    }
                }
            }
            
        }
        
        
       	
        
        $result = array('success' => '저장되었습니다');
                exit(json_encode($result));

    }


    public function cdt_tag_update($cit_id = 0, $cdt_tag = '')
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_postact_post_extra';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $result = array();
        $this->output->set_content_type('application/json');

        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            $result = array('error' => '잘못된 접근입니다');
            exit(json_encode($result));
        }


        
        $this->load->model(array('Crawl_delete_tag_model','Crawl_tag_model','Cmall_item_model','Crawl_manual_tag_model'));

        $crawltagwhere = array(
			'cit_id' => $cit_id,
		);

        $select = 'cit_id,post_id,brd_id';

        $cmail_item = $this->Cmall_item_model->get_one('',$select,$crawltagwhere);
        
        
        if ( ! element('cit_id', $cmail_item)) {
            $result = array('error' => '존재하지 않는 항목입니다');
            exit(json_encode($result));
        }


        $is_admin = $this->member->is_admin();

        if ($is_admin === false &&  $this->member->item('mem_level') < 1) {
            $result = array('error' => '접근권한이 없습니다');
            exit(json_encode($result));
        }

        $cdt_tag_text=array();
        
        $cdt_tag_text = explode("\n",urldecode($cdt_tag));
        
        if(count($cdt_tag_text)){
            $deletewhere = array(
                'cit_id' => element('cit_id', $cmail_item),
            );
            $this->Crawl_delete_tag_model->delete_where($deletewhere);            
            if ($cdt_tag_text && is_array($cdt_tag_text)) {
                foreach ($cdt_tag_text as $key => $value) {
                    $value = trim($value);
                    if ($value) {

                    	$where = array(
                    	            
	                            'cit_id' => element('cit_id', $cmail_item),
	                            
                    	            'cdt_tag' => $value,
                    	        );

                    	

                    	if(!$this->Crawl_delete_tag_model->count_by($where)) {

	                        $tagdata = array(
	                            'post_id' => element('post_id', $cmail_item),
	                            'cit_id' => element('cit_id', $cmail_item),
	                            'brd_id' => element('brd_id', $cmail_item),
	                            'cdt_tag' => $value,
	                            // 'is_manual' => 1,
	                        );
	                        $this->Crawl_delete_tag_model->insert($tagdata);
	                     }

					    $deletewhere = array(
					        // 'post_id' => element('post_id', $cmail_item),
            	            'cit_id' => element('cit_id', $cmail_item),
            	            // 'brd_id' => element('brd_id', $cmail_item),
            	            'cta_tag' => $value,
					        
					    );
					    
					    $this->Crawl_tag_model->delete_where($deletewhere);            

	            		
                    }
                }
            }
            
        }
        
        $deletewhere = array(
            'cit_id' => element('cit_id', $cmail_item),
            'is_manual' => 1,
        );
        $this->Crawl_tag_model->delete_where($deletewhere);
        
        $where = array(
            'cit_id' => element('cit_id', $cmail_item),
        );
        $manual_tag = $this->Crawl_manual_tag_model->get('','',$where);     
       	
       	foreach($manual_tag as $tval){
       		$value = trim(element('cmt_tag',$tval));
       		if ($value) {
       		    

       		    $countwhere = array(
       		        // 'post_id' => element('post_id',$tval),
       		        'cit_id' => element('cit_id',$tval),
       		        // 'brd_id' => element('brd_id',$tval),
       		        'cdt_tag' => $value,
       		    );
       		    $dtag = $this->Crawl_delete_tag_model->get_one('','',$countwhere);

       		    if(!element('cdt_id',$dtag)){       		        
       		            
   		            $where = array(
                                // 'post_id' => $this->input->post('post_id', null, ''),
                                'cit_id' => element('cit_id',$tval),
                                // 'brd_id' => $this->input->post('brd_id', null, ''),
                                'cta_tag' => $value,
                            );

                        

                    if(!$this->Crawl_tag_model->count_by($where)) {
       		            $tagdata = array(
       		                'post_id' => element('post_id',$tval),
       		                'cit_id' => element('cit_id',$tval),
       		                'brd_id' => element('brd_id',$tval),
       		                'cta_tag' => $value,
       		                'is_manual' => 1,
       		            );
       		            $this->Crawl_tag_model->insert($tagdata);
       		        }
       		    }
       		}
       	}
        
        $result = array('success' => '저장되었습니다');
                exit(json_encode($result));

    }



    /**
	 * 목록에서 여러 게시물 선택삭제하기
	 */
	public function cit_multi_delete($flag = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_multi_delete';
		$this->load->event($eventname);

		$result = array();
		$this->output->set_content_type('application/json');

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_item_model'));

		$cit_ids = $this->input->post('chk');
		
		
		if (empty($cit_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}

		foreach ($cit_ids as $cit_id) {
			$cit_id = (int) $cit_id;
			if (empty($cit_id) OR $cit_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}

			$cmall = $this->Cmall_item_model->get_one($cit_id);
			$board = $this->board->item_all(element('brd_id', $cmall));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			$this->board->delete_cmall($cit_id);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$result = array('success' => '선택된 게시글이 삭제되었습니다');
		exit(json_encode($result));

	}


	/**
	 * 목록에서 여러 게시물 블라인드 설정/해제하기
	 */
	public function cit_status($cit_id = 0,$flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_item_model'));

		$result = array();
		$this->output->set_content_type('application/json');

		
		$flag = ((int) $flag === 1) ? 1 : 0;

		
		$cit_id = (int) $cit_id;
		if (empty($cit_id) OR $cit_id < 1) {
			$result = array('error' => '잘못된 접근입니다');
			exit(json_encode($result));
		}

		$select = 'cit_id,post_id, brd_id,  cit_status';
		$cmail = $this->Cmall_item_model->get_one($cit_id, $select);

		if ( ! element('cit_id', $cmail)) {
			$result = array('error' => '존재하지 않는 게시물입니다');
			exit(json_encode($result));
		}

		$board = $this->board->item_all(element('brd_id', $cmail));

		$is_admin = $this->member->is_admin(
			array(
				'board_id' => element('brd_id', $board),
				'group_id' => element('bgr_id', $board),
			)
		);

		if ($is_admin === false) {
			$result = array('error' => '접근권한이 없습니다');
			exit(json_encode($result));
		}

		

		$cit_status = $flag ? $flag : 0;
		$updatedata = array(
			'cit_status' => $cit_status,
		);
		$this->Cmall_item_model->update($cit_id, $updatedata);
		

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 항목을 블라인드 처리하였습니다' : '이 항목을 블라인드 해제하셨습니다';
		$result = array('success' => $success,'reload' => 1);
		exit(json_encode($result));

	}

	/**
	 * 목록에서 여러 게시물 블라인드 설정/해제하기
	 */
	public function cit_multi_status($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_item_model'));

		$result = array();
		$this->output->set_content_type('application/json');

		$cit_ids = $this->input->post('chk');
		if (empty($cit_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($cit_ids as $cit_id) {
			$cit_id = (int) $cit_id;
			if (empty($cit_id) OR $cit_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}
			
			$select = 'cit_id,post_id, brd_id,  cit_status';
			$cmall = $this->Cmall_item_model->get_one($cit_id, $select);

			if ( ! element('cit_id', $cmall)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $cmall));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			

			$cit_status = $flag ? $flag : 0;
			$updatedata = array(
				'cit_status' => $cit_status,
			);
			$this->Cmall_item_model->update($cit_id, $updatedata);
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 항목을 블라인드 해제하셨습니다' : '이 항목을 블라인드 처리하였습니다' ;
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	public function insert_fcm_token($dev_token = '',$mem_id = 0)
    {
        
        if(empty($dev_token)) {
            $result = array('result' => 'error');        
			exit(json_encode($result));
		}

        $this->load->model('Device_model');
        $insertdata = array(
            'mem_id' => $mem_id,
            'dev_token' => $dev_token,
            'dev_register_datetime' => cdate('Y-m-d H:i:s'),
            
        );
        
        $dev_id = $this->Device_model
            ->insert($insertdata);



        if(!empty($mem_id)){
            $updatedata = array(            
                'mem_token' => $dev_token,
            );

            $this->Member_model
                ->update($mem_id,$updatedata);
        }

	    if($dev_id){
			$result = array('result' => 'success');
			exit(json_encode($result));
	    }

	    $result = array('result' => 'error');
			exit(json_encode($result));
	}

	public function get_post_list($brd_id = 0)
    {
        
        // 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_link';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$brd_id = (int) $brd_id;
		if (empty($brd_id) OR $brd_id < 1) {
			show_404();
		}

		$mem_id = (int) $this->member->item('mem_id');

		$this->load->model(array('Post_model'));

		$where = array('brd_id' => $brd_id);
		$select = 'post_id, brd_id, mem_id, post_del';
		$result = $this->Post_model
			->get_post_list('','', $where);

		
		exit(json_encode($result['list']));
	}

	public function cit_color_update($cit_id = 0, $cit_color = '')
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_postact_post_extra';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $result = array();
        $this->output->set_content_type('application/json');

        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            $result = array('error' => '잘못된 접근입니다');
            exit(json_encode($result));
        }


        
        $this->load->model(array('Cmall_item_model'));

        $crawltagwhere = array(
			'cit_id' => $cit_id,
		);

        $select = 'cit_id,post_id,brd_id';

        $cmail_item = $this->Cmall_item_model->get_one('',$select,$crawltagwhere);
        
        
        if ( ! element('cit_id', $cmail_item)) {
            $result = array('error' => '존재하지 않는 항목입니다');
            exit(json_encode($result));
        }


        $is_admin = $this->member->is_admin();

        if ($is_admin === false &&  $this->member->item('mem_level') < 1) {
            $result = array('error' => '접근권한이 없습니다');
            exit(json_encode($result));
        }

        $cit_color_text=array();
        
        $cit_color_text = explode("\n",urldecode($cit_color));
        
        if(count($cit_color_text)){
            $updatedata = array(
    			'cit_color' => urldecode($cit_color),
    		);
    		$this->Cmall_item_model->update($cit_id, $updatedata);
            
        }

        
       	
        
        $result = array('success' => '저장되었습니다');
                exit(json_encode($result));

    }


    public function crawling_update($post_id = 0)
    {
    	if(empty($post_id)){
            $result = array('success' => '실패');
            alert('post_id 값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/php '.FCPATH.'/index.php Crawl crawling_update '.$post_id.'> /dev/null 2>/dev/null & /tmp/crawl_crawling_update_'.$_SERVER['HTTP_HOST'].'.log';
    	echo $cmd;
    	@exec($cmd, $output, $retval);

    	$result = array('success' => '실행되었습니다');
        alert('실행되었습니다');
        // exit(json_encode($result));
    }

    public function crawling_overwrite($post_id=0)
    {
    	if(empty($post_id)){
            $result = array('success' => '실패');
            alert('post_id 값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/curl -k '.base_url('crawl/crawling_overwrite/'.$post_id).' --connect-timeout 6000 > /tmp/crawl_crawling_overwrite_'.$_SERVER['HTTP_HOST'].'.log ';
    	echo $cmd;    	
    	@exec($cmd, $output, $retval);
    	$result = array('success' => '실행되었습니다');
        alert('실행되었습니다');
        // exit(json_encode($result));
    }

    public function crawling_category_update($post_id=0)
    {	
    	if(empty($post_id)){
            $result = array('success' => '실패');
            alert('post_id 값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/curl -k '.base_url('crawl/crawling_category_update/'.$post_id).' --connect-timeout 6000 >  /tmp/crawl_crawling_category_update_'.$_SERVER['HTTP_HOST'].'.log';
    	echo $cmd;
    	@exec($cmd, $output, $retval);

    	$result = array('success' => '실행되었습니다');
        alert('실행되었습니다');
        // exit(json_encode($result));
    }

    public function crawling_tag_update($post_id=0)
    {	
    	if(empty($post_id)){
            $result = array('success' => '실패');
            alert('post_id 값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/curl -k '.base_url('crawl/crawling_tag_update/'.$post_id).' --connect-timeout 6000 >  /tmp/crawl_crawling_tag_update_'.$_SERVER['HTTP_HOST'].'.log';
    	echo $cmd;
    	@exec($cmd, $output, $retval);

    	
    	$result = array('success' => '실행되었습니다');
        alert('실행되었습니다');
        // exit(json_encode($result));
    }

    public function crawling_tag_overwrite($post_id=0)
    {	
    	if(empty($post_id)){
            $result = array('success' => '실패');
            alert('post_id 값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/curl -k '.base_url('crawl/crawling_tag_overwrite/'.$post_id).' --connect-timeout 6000 >  /tmp/crawl_crawling_tag_overwrite_'.$_SERVER['HTTP_HOST'].'.log';
    	echo $cmd;
    	@exec($cmd, $output, $retval);

    	
    	$result = array('success' => '실행되었습니다');
        alert('실행되었습니다');
        // exit(json_encode($result));
    }

    public function vision_api_label($post_id=0)
    {	
    	if(empty($post_id)){
            $result = array('success' => '실패');
            alert('post_id 값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/curl -k '.base_url('crawl/vision_api_label/'.$post_id).' --connect-timeout 6000 >  /tmp/vision_api_label'.$_SERVER['HTTP_HOST'].'.log';
    	echo $cmd;
    	@exec($cmd, $output, $retval);

    	
    	$result = array('success' => '실행되었습니다');
        alert('실행되었습니다');
        // exit(json_encode($result));
    }

    function crawling_item_update($crawl_key,$crawl_mode,$crawl_type)
    {	

    	if(empty($crawl_key) && empty($crawl_mode) && empty($crawl_type)){    		
            $result = array('success' => '실패');
            alert('crawl_key crawl_mode crawl_type  값이 없습니다.');
        	// exit(json_encode($result));
    	}

    	$retval = 1;
    	$cmd='';

    	$cmd='/usr/bin/curl -k '.base_url('crawl/crawling_item_update/'.$crawl_key.'/'.$crawl_mode.'/'.$crawl_type).' --connect-timeout 6000 > /tmp/crawl_crawling_item_update_'.$_SERVER['HTTP_HOST'].'.log';
    	echo $cmd;
    	exit;
    	// @exec($cmd, $output, $retval);

    	$result = array('success' => '실행되었습니다');
    	alert('실행되었습니다');
        // exit(json_encode($result));
    }

   	

   	function attr_update()
    {	
    	$url = base_url('postact/attr');
    	$aaa = array(
    		'크기',
'연령별',
'색상',
'소재',
'재료',
'사용계절',
'특징',
    	);
    	foreach($aaa as $val){
	    	$data = array(
	    		'type' => 'add',
	    		'cat_value' => $val,
	    	);

	    	$ch = curl_init();
	    	curl_setopt($ch, CURLOPT_URL, $url);
	    	curl_setopt($ch, CURLOPT_POST, sizeof($data));
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    	$result = curl_exec($ch);

	    	echo $result;
	    	curl_close($ch);
    	}
    	$obj = json_decode($result);

    	
    }

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
                
				// redirect(admin_url($this->pagedir.'/attr'), 'refresh');

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

				// redirect(admin_url($this->pagedir.'/attr'), 'refresh');

			}
		}

		// $getdata = $this->Cmall_attr_model->get_all_attr();

		
		// $view['view']['data'] = $getdata;

		// /**
		//  * primary key 정보를 저장합니다
		//  */
		// $view['view']['primary_key'] = $primary_key;

		// // 이벤트가 존재하면 실행합니다
		// $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		// /**
		//  * 어드민 레이아웃을 정의합니다
		//  */
		// $layoutconfig = array('layout' => 'layout', 'skin' => 'attr');
		// $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		// $this->data = $view;
		// $this->layout = element('layout_skin_file', element('layout', $view));
		// $this->view = element('view_skin_file', element('layout', $view));
	}

		

    	

	public function multi_crawling_item_update($crawl_mode,$crawl_type)
	{

		if( empty($crawl_mode) && empty($crawl_type)){    		
            $result = array('success' => '실패');
            alert('crawl_mode crawl_type  값이 없습니다.');
        	// exit(json_encode($result));
    	}

		$result = array();
		$this->output->set_content_type('application/json');

		
		$this->load->model(array('Cmall_item_model'));

		$cit_ids = $this->input->post('chk');
		
		
		if (empty($cit_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}

		foreach ($cit_ids as $cit_id) {
			$cit_id = (int) $cit_id;
			if (empty($cit_id) OR $cit_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}
			
			$select = 'cit_id,post_id, brd_id';
			$cmall = $this->Cmall_item_model->get_one($cit_id, $select);

			if ( ! element('cit_id', $cmall)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

	    	$retval = 1;
	    	$cmd='';

	    	$cmd='/usr/bin/curl -k '.base_url('crawl/crawling_item_update/'.$cit_id.'/'.$crawl_mode.'/'.$crawl_type).' --connect-timeout 86000 > /tmp/crawl_crawling_item_update_'.$_SERVER['HTTP_HOST'].'.log';
    	
	    	echo $cmd;
	    	@exec($cmd, $output, $retval);

			    	
			    	
			        
		}

		

		$result = array('success' => '실행되었습니다');
		exit(json_encode($result));

	}

	public function cit_multi_type1($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_item_model'));

		$result = array();
		$this->output->set_content_type('application/json');

		$cit_ids = $this->input->post('chk');
		if (empty($cit_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($cit_ids as $cit_id) {
			$cit_id = (int) $cit_id;
			if (empty($cit_id) OR $cit_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}
			
			$select = 'cit_id,post_id, brd_id';
			$cmall = $this->Cmall_item_model->get_one($cit_id, $select);

			if ( ! element('cit_id', $cmall)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $cmall));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			

			$cit_type1 = $flag ? $flag : 0;
			$updatedata = array(
				'cit_type1' => $cit_type1,
			);
			$this->Cmall_item_model->update($cit_id, $updatedata);
		}


		

        
		delete_cache_files('/latest','cit-order-');
    	
		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 항목을 베스트 상품 등록 하셨습니다' : '이 항목을 베스트 상품 해제 하였습니다' ;
		$result = array('success' => $success);
		exit(json_encode($result));

	}

	public function cit_multi_type2($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_item_model'));

		$result = array();
		$this->output->set_content_type('application/json');

		$cit_ids = $this->input->post('chk');
		if (empty($cit_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($cit_ids as $cit_id) {
			$cit_id = (int) $cit_id;
			if (empty($cit_id) OR $cit_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}
			
			$select = 'cit_id,post_id, brd_id';
			$cmall = $this->Cmall_item_model->get_one($cit_id, $select);

			if ( ! element('cit_id', $cmall)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			$board = $this->board->item_all(element('brd_id', $cmall));

			$is_admin = $this->member->is_admin(
				array(
					'board_id' => element('brd_id', $board),
					'group_id' => element('bgr_id', $board),
				)
			);

			if ($is_admin === false) {
				$result = array('error' => '접근권한이 없습니다');
				exit(json_encode($result));
			}

			

			$cit_type2 = $flag ? $flag : 0;
			$updatedata = array(
				'cit_type2' => $cit_type2,
			);
			$this->Cmall_item_model->update($cit_id, $updatedata);
		}		

        delete_cache_files('/latest','cit-order-');

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 항목을 인기 상품 등록 하셨습니다' : '이 항목을 인기 상품 해제 하였습니다' ;
		$result = array('success' => $success);
		exit(json_encode($result));

	}


	public function cit_order_update($cit_id = 0, $cit_order = 0)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_postact_post_extra';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        $result = array();
        $this->output->set_content_type('application/json');

        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            $result = array('error' => '잘못된 접근입니다');
            exit(json_encode($result));
        }


        
        $this->load->model(array('Cmall_item_model'));

        $crawltagwhere = array(
			'cit_id' => $cit_id,
		);

        $select = 'cit_id,post_id,brd_id,cit_type1,cit_type2,cit_type3,cit_type4';

        $cmail_item = $this->Cmall_item_model->get_one('',$select,$crawltagwhere);
        
        
        if ( ! element('cit_id', $cmail_item)) {
            $result = array('error' => '존재하지 않는 항목입니다');
            exit(json_encode($result));
        }


        $is_admin = $this->member->is_admin();

        if ($is_admin === false &&  $this->member->item('mem_level') < 1) {
            $result = array('error' => '접근권한이 없습니다');
            exit(json_encode($result));
        }
        
        
		$updatedata = array(
			'cit_order'.$this->input->post('cit_type') => $cit_order,
		);
		$this->Cmall_item_model->update($cit_id, $updatedata);
		

		// $cachename = 'latest/cit-' . element('cit_type1', $config).element('cit_type2', $config).element('cit_type3', $config).element('cit_type4', $config) . '-' . $limit;
		delete_cache_files('/latest','cit-order-');
		

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

        $result = array('success' => '저장되었습니다');
        exit(json_encode($result));

    }

    public function cre_multi_type1($flag = 0)
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_postact_post_multi_blame_blind';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->load->model(array('Cmall_review_model'));

		$result = array();
		$this->output->set_content_type('application/json');

		$cre_ids = $this->input->post('chk');
		if (empty($cre_ids)) {
			$result = array('error' => '선택된 게시물이 없습니다.');
			exit(json_encode($result));
		}
		$flag = ((int) $flag === 1) ? 1 : 0;

		foreach ($cre_ids as $cre_id) {
			$cre_id = (int) $cre_id;
			if (empty($cre_id) OR $cre_id < 1) {
				$result = array('error' => '잘못된 접근입니다');
				exit(json_encode($result));
			}
			
			$select = 'cre_id';
			$cmall = $this->Cmall_review_model->get_one($cre_id, $select);

			if ( ! element('cre_id', $cmall)) {
				$result = array('error' => '존재하지 않는 게시물입니다');
				exit(json_encode($result));
			}

			

			

			$cre_type1 = $flag ? $flag : 0;
			$updatedata = array(
				'cre_type1' => $cre_type1,
			);
			$this->Cmall_review_model->update($cre_id, $updatedata);
		}


		

        
		delete_cache_files('/latest','cit-order-');
    	
		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		$success = ($flag) ? '이 항목을 리뷰우선노출 등록 하셨습니다' : '이 항목을 리뷰우선노출 해제 하였습니다' ;
		$result = array('success' => $success);
		exit(json_encode($result));

	}
}
