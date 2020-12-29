<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Review class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>컨텐츠몰관리>상품사용후기 controller 입니다.
 */
class Review extends CB_Controller
{

	/**
	 * 관리자 페이지 상의 현재 디렉토리입니다
	 * 페이지 이동시 필요한 정보입니다
	 */
	public $pagedir = 'cmall/review';

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array('Cmall_review','Review_file','Cmall_item');

	/**
	 * 이 컨트롤러의 메인 모델 이름입니다
	 */
	protected $modelname = 'Cmall_review_model';

	/**
	 * 헬퍼를 로딩합니다
	 */
	protected $helpers = array('form', 'array', 'cmall', 'dhtml_editor');

	function __construct()
	{
		parent::__construct();

		/**
		 * 라이브러리를 로딩합니다
		 */
		$this->load->library(array('pagination', 'querystring', 'cmalllib','videoplayer'));
	}

	/**
	 * 목록을 가져오는 메소드입니다
	 */
	public function index()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_review_index';
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
			'cre_id' => $param->sort('cre_id', 'asc'),
			'cit_id' => $param->sort('cit_id', 'asc'),
			'cit_name' => $param->sort('cit_name', 'asc'),			
			'cre_good' => $param->sort('cre_good', 'asc'),
			'cre_bad' => $param->sort('cre_bad', 'asc'),
			'cre_score' => $param->sort('cre_score', 'asc'),
			'cre_status' => $param->sort('cre_status', 'asc'),
		);
		$findex = $this->input->get('findex') ? $this->input->get('findex') : $this->{$this->modelname}->primary_key;
		$forder = $this->input->get('forder', null, 'desc');
		$sfield = $this->input->get('sfield', null, '');
		$skeyword = $this->input->get('skeyword', null, '');

		$per_page = admin_listnum();
		$offset = ($page - 1) * $per_page;

        $where = array();

        if(!empty($this->input->get('cre_type'))){
            
            $where['cre_type'.$this->input->get('cre_type')] = 1;

            
        } 
		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$this->{$this->modelname}->allow_search_field = array('cre_id', 'cit_id', 'cre_good', 'cre_bad', 'cmall_review.mem_id', 'cre_score', 'cre_datetime', 'cre_tip', 'cre_status', 'cit_name', 'cit_key'); // 검색이 가능한 필드
		$this->{$this->modelname}->search_field_equal = array('cre_id', 'cit_id', 'cmall_review.mem_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
		$this->{$this->modelname}->allow_order_field = array('cre_id', 'cit_id', 'cre_title', 'cre_score', 'cit_name', 'cit_key', 'cre_status'); // 정렬이 가능한 필드
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
		$search_option = array('cre_good' => '좋았던점', 'cre_bad' => '안좋은점', 'cit_name' => '상품명', 'cit_key' => '상품코드', 'cre_datetime' => '입력일');
		$view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
		$view['view']['search_option'] = search_option($search_option, $sfield);
		$view['view']['listall_url'] = admin_url($this->pagedir);
		$view['view']['write_url'] = admin_url($this->pagedir . '/write');
		$view['view']['list_delete_url'] = admin_url($this->pagedir . '/listdelete/?' . $param->output());
		$view['view']['list_update_url'] = admin_url($this->pagedir . '/listupdate/?' . $param->output());

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
		$eventname = 'event_admin_cmall_review_write';
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

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		 * 수정 페이지일 경우 기존 데이터를 가져옵니다
		 */
		$getdata = array();
		if ($pid) {
			$getdata = $this->{$this->modelname}->get_one($pid);
			$getdata['cit_name'] = element('cit_name',$this->Cmall_item_model->get_one(element('cit_id',$getdata),'cit_name'),'없는 상품입니다.');


			$view['view']['file'] = $file = $this->Review_file_model
				->get('', '', array('cre_id' => element('cre_id',$getdata)), '', '', 'rfi_id', 'ASC');

			if ($file && is_array($file)) {
				foreach ($file as $key => $value) {					
					$view['view']['file'][$key]['download_link']
						= site_url('postact/download/' . element('rfi_id', $value));
				}
			}

			$view['view']['file_download'] = array();
			$view['view']['file_image'] = array();

			$play_extension = array('acc', 'flv', 'f4a', 'f4v', 'mov', 'mp3', 'mp4', 'm4a', 'm4v', 'oga', 'ogg', 'rss', 'webm');

			if ($file && is_array($file)) {
				foreach ($file as $key => $value) {
					$file_player = '';
					if (element('rfi_is_image', $value)) {
						$value['thumb_image_url'] = cdn_url('cmall_review' , element('rfi_filename', $value));
						
						$view['view']['file'][$key]['rfi_is_image'] = element('rfi_is_image', $value);
						$view['view']['file'][$key]['image_url'] = $value['thumb_image_url'];
					} else {
						// $value['download_link'] = site_url('postact/download/' . element('rfi_id', $value));
						// $view['view']['file'][$key] = $value;
						if ( in_array(element('rfi_type', $value), $play_extension)) {
							$file_player = $this->videoplayer->get_jwplayer(cdn_url('cmall_review' , element('rfi_filename', $value)));

							$view['view']['file'][$key]['rfi_is_image'] = element('rfi_is_image', $value);
							$view['view']['file'][$key]['file_player'] = $file_player;
							
						}
					}
				}
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
                'field' => 'cre_good',
                'label' => '좋았던 점 ',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cre_bad',
                'label' => '아쉬운 점',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'cre_tip',
                'label' => '나만의 팁',
                'rules' => 'trim',
            ),
			array(
				'field' => 'cre_score',
				'label' => '점수',
				'rules' => 'trim|required|numeric|is_natural_no_zero|greater_than_equal_to[1]|less_than_equal_to[5]',
			),
			array(
				'field' => 'cre_status',
				'label' => '승인여부',
				'rules' => 'trim|numeric',
			),
		);
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run();
		$file_error = '';
		$uploadfiledata = array();
		$uploadfiledata2 = array();
		if ( $form_validation ) {
			$this->load->library('upload');
			$this->load->library('aws_s3');
			if (isset($_FILES) && isset($_FILES['cre_file']) && isset($_FILES['cre_file']['name']) && is_array($_FILES['cre_file']['name'])) {
                $filecount = count($_FILES['cre_file']['name']);
                $upload_path = config_item('uploads_dir') . '/cmall_review/';
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

                foreach ($_FILES['cre_file']['name'] as $i => $value) {
                    if ($value) {
                        $uploadconfig = array();
                        $uploadconfig['upload_path'] = $upload_path;
                        $uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif|acc|flv|f4a|f4v|mov|mp3|mp4|m4a|m4v|oga|ogg|rss|webm';
                        $uploadconfig['max_size'] = 100 * 1024;
                        $uploadconfig['encrypt_name'] = true;

                        $this->upload->initialize($uploadconfig);
                        $_FILES['userfile']['name'] = $_FILES['cre_file']['name'][$i];
                        $_FILES['userfile']['type'] = $_FILES['cre_file']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['cre_file']['tmp_name'][$i];
                        $_FILES['userfile']['error'] = $_FILES['cre_file']['error'][$i];
                        $_FILES['userfile']['size'] = $_FILES['cre_file']['size'][$i];
                        if ($this->upload->do_upload()) {
                            $filedata = $this->upload->data();

                            $uploadfiledata[$i] = array();
                            $uploadfiledata[$i]['rfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
                            $uploadfiledata[$i]['rfi_originname'] = element('orig_name', $filedata);
                            $uploadfiledata[$i]['rfi_filesize'] = intval(element('file_size', $filedata) * 1024);
                            $uploadfiledata[$i]['rfi_width'] = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
                            $uploadfiledata[$i]['rfi_height'] = element('image_height', $filedata) ? element('image_height', $filedata) : 0;
                            $uploadfiledata[$i]['rfi_type'] = str_replace('.', '', element('file_ext', $filedata));
                            $uploadfiledata[$i]['is_image'] = element('is_image', $filedata) ? element('is_image', $filedata) : 0;

                            $upload = $this->aws_s3->upload_file($this->upload->upload_path,$this->upload->file_name,$upload_path);
                        } else {
                            $file_error = $this->upload->display_errors();
                            break;
                        }
                    }
                }
            }

            if (isset($_FILES) && isset($_FILES['cre_file_update'])
                && isset($_FILES['cre_file_update']['name'])
                && is_array($_FILES['cre_file_update']['name'])
                && $file_error === '') {
                $filecount = count($_FILES['cre_file_update']['name']);
                $upload_path = config_item('uploads_dir') . '/cmall_review/';
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

                foreach ($_FILES['cre_file_update']['name'] as $i => $value) {
                    if ($value) {
                        $uploadconfig = array();
                        $uploadconfig['upload_path'] = $upload_path;
                        $uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif|acc|flv|f4a|f4v|mov|mp3|mp4|m4a|m4v|oga|ogg|rss|webm';
                        $uploadconfig['max_size'] = 100 * 1024;
                        $uploadconfig['encrypt_name'] = true;
                        $this->upload->initialize($uploadconfig);
                        $_FILES['userfile']['name'] = $_FILES['cre_file_update']['name'][$i];
                        $_FILES['userfile']['type'] = $_FILES['cre_file_update']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['cre_file_update']['tmp_name'][$i];
                        $_FILES['userfile']['error'] = $_FILES['cre_file_update']['error'][$i];
                        $_FILES['userfile']['size'] = $_FILES['cre_file_update']['size'][$i];
                        if ($this->upload->do_upload()) {
                            $filedata = $this->upload->data();

                            $oldcrefile = $this->Review_file_model->get_one($i);
                            if ((int) element('cre_id', $oldcrefile) !== (int) element('cre_id', $getdata)) {
                                alert('잘못된 접근입니다');
                            }
                            @unlink(config_item('uploads_dir') . '/cmall_review/' . element('rfi_filename', $oldcrefile));

                            $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/cmall_review/' . element('rfi_filename', $oldcrefile));

                            $uploadfiledata2[$i] = array();
                            $uploadfiledata2[$i]['rfi_id'] = $i;
                            $uploadfiledata2[$i]['rfi_filename'] = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata);
                            $uploadfiledata2[$i]['rfi_originname'] = element('orig_name', $filedata);
                            $uploadfiledata2[$i]['rfi_filesize'] = intval(element('file_size', $filedata) * 1024);
                            $uploadfiledata2[$i]['rfi_width'] = element('image_width', $filedata)
                                ? element('image_width', $filedata) : 0;
                            $uploadfiledata2[$i]['rfi_height'] = element('image_height', $filedata)
                                ? element('image_height', $filedata) : 0;
                            $uploadfiledata2[$i]['rfi_type'] = str_replace('.', '', element('file_ext', $filedata));
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
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			$view['view']['data'] = $getdata;

			/**
			 * primary key 정보를 저장합니다
			 */
			$view['view']['primary_key'] = $primary_key;

			if ($file_error) {
				$view['view']['alert_message'] = $file_error;
			}

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

			$content_type = $this->cbconfig->item('use_cmall_product_review_dhtml') ? 1 : 0;
			$cre_status = $this->input->post('cre_status') ? 1 : 0;

			$updatedata = array(
				'cre_good' => $this->input->post('cre_good', null, ''),
                'cre_bad' => $this->input->post('cre_bad', null, ''),
                'cre_tip' => $this->input->post('cre_tip', null, ''),
                'cre_score' => $this->input->post('cre_score', null, 0),				
				'cre_status' => $cre_status,
			);

			$updatedata['cre_update_datetime'] = cdate('Y-m-d H:i:s');		

			$this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);
			$this->cmalllib->update_review_count(element('cit_id', $getdata));

			$file_updated = false;
            $file_changed = false;
            if ($uploadfiledata
                && is_array($uploadfiledata)
                && count($uploadfiledata) > 0) {
                foreach ($uploadfiledata as $pkey => $pval) {
                    if ($pval) {
                        $fileupdate = array(
                            'cre_id' => element('cre_id',$getdata),
                            'brd_id' => element('brd_id',$getdata),
                            'mem_id' => element('mem_id',$getdata),
                            'rfi_originname' => element('rfi_originname', $pval),
                            'rfi_filename' => element('rfi_filename', $pval),
                            'rfi_filesize' => element('rfi_filesize', $pval),
                            'rfi_width' => element('rfi_width', $pval),
                            'rfi_height' => element('rfi_height', $pval),
                            'rfi_type' => element('rfi_type', $pval),
                            'rfi_is_image' => element('is_image', $pval),
                            'rfi_datetime' => cdate('Y-m-d H:i:s'),
                            'rfi_ip' => $this->input->ip_address(),
                        );
                        $file_id = $this->Review_file_model->insert($fileupdate);
                        // if ( ! element('is_image', $pval)) {
                        //     if (element('use_point', $board)) {
                        //         $point = $this->point->insert_point(
                        //             $mem_id,
                        //             element('point_fileupload', $board),
                        //             element('board_name', $board) . ' ' . $post_id . ' 파일 업로드',
                        //             'fileupload',
                        //             $file_id,
                        //             '파일 업로드'
                        //         );
                        //     }
                        // }
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
                            'mem_id' => element('mem_id',$getdata),
                            'rfi_originname' => element('rfi_originname', $pval),
                            'rfi_filename' => element('rfi_filename', $pval),
                            'rfi_filesize' => element('rfi_filesize', $pval),
                            'rfi_width' => element('rfi_width', $pval),
                            'rfi_height' => element('rfi_height', $pval),
                            'rfi_type' => element('rfi_type', $pval),
                            'rfi_is_image' => element('is_image', $pval),
                            'rfi_datetime' => cdate('Y-m-d H:i:s'),
                            'rfi_ip' => $this->input->ip_address(),
                        );
                        $this->Review_file_model->update($pkey, $fileupdate);
                        // if ( ! element('is_image', $pval)) {
                        //     if (element('use_point', $board)) {
                        //         $point = $this->point->insert_point(
                        //             $mem_id,
                        //             element('point_fileupload', $board),
                        //             element('board_name', $board) . ' ' . $post_id . ' 파일 업로드',
                        //             'fileupload',
                        //             $pkey,
                        //             '파일 업로드'
                        //         );
                        //     }
                        // } else {
                        //     $this->point->delete_point(
                        //         $mem_id,
                        //         'fileupload',
                        //         $pkey,
                        //         '파일 업로드'
                        //     );
                        // }
                        $file_changed = true;
                    }
                }
            }
            if ($this->input->post('cre_file_del')) {
                foreach ($this->input->post('cre_file_del') as $key => $val) {
                    if ($val === '1' && ! isset($uploadfiledata2[$key])) {
                        $oldcrefile = $this->Review_file_model->get_one($key);

                        if ( ! element('cre_id', $oldcrefile) OR (int) element('cre_id', $oldcrefile) !== (int) element('cre_id', $getdata)) {
                            alert('잘못된 접근입니다.');
                        }
                        @unlink(config_item('uploads_dir') . '/cmall_review/' . element('rfi_filename', $oldcrefile));

                        $deleted = $this->aws_s3->delete_file(config_item('s3_folder_name') . '/cmall_review/' . element('rfi_filename', $oldcrefile));
                        $this->Review_file_model->delete($key);
                        // $this->point->delete_point(
                        //     $mem_id,
                        //     'fileupload',
                        //     $key,
                        //     '파일 업로드'
                        // );
                        $file_changed = true;
                    }
                }
            }

            $updatedata['cre_image'] = 0;
            $updatedata['cre_file'] = 0;
            $result = $this->Review_file_model->get_review_file_count($this->input->post($primary_key));
            if ($result && is_array($result)) {
                $total_cnt = 0;
                foreach ($result as $value) {
                    if (element('rfi_is_image', $value)) {
                        $updatedata['cre_image'] = element('cnt', $value);
                        $total_cnt += element('cnt', $value);
                    } else {
                        $updatedata['cre_file'] = element('cnt', $value);
                        $total_cnt += element('cnt', $value);
                    }
                }
            }

            $this->{$this->modelname}->update($this->input->post($primary_key), $updatedata);


			$this->session->set_flashdata(
				'message',
				'정상적으로 수정되었습니다'
			);

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
	 * 목록 페이지에서 선택수정을 하는 경우 실행되는 메소드입니다
	 */
	public function listupdate()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_review_listupdate';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		 * 체크한 게시물의 업데이트를 실행합니다
		 */
		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$updatedata = array(
						'cre_status' => 1,
					);
					$this->{$this->modelname}->update($val, $updatedata);
					$data = $this->{$this->modelname}->get_one($val);
					$this->cmalllib->update_review_count(element('cit_id', $data));
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		 * 업데이트가 끝난 후 목록페이지로 이동합니다
		 */
		$this->session->set_flashdata(
			'message',
			'정상적으로 승인되었습니다'
		);
		$param =& $this->querystring;
		$redirecturl = admin_url($this->pagedir . '?' . $param->output());

		redirect($redirecturl);
	}

	/**
	 * 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
	 */
	public function listdelete()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_cmall_review_listdelete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		 * 체크한 게시물의 삭제를 실행합니다
		 */
		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$data = $this->{$this->modelname}->get_one($val);
					$this->{$this->modelname}->delete($val);
                    $this->cmalllib->_review_delete($val);
					$this->cmalllib->update_review_count(element('cit_id', $data));
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
