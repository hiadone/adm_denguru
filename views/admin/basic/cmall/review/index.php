<div class="box">
	<div class="box-table">
		<?php
		echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		$attributes = array('class' => 'form-inline', 'name' => 'fboardlist', 'id' => 'fboardlist');
		echo form_open(current_full_url(), $attributes);
		?>
			<div class="box-table-header">
			<?php
			ob_start();
			?>

				<div class="pull-left">

					<button type="button" class="btn btn-default btn-sm admin-manage-list"><i class="fa fa-cog big-fa"></i> 관리</button>
                	<div class=" btn-admin-manage-layer admin-manage-layer-list">

	                    <div class="item" onClick="post_multi_action('cre_multi_type1', '1', '선택하신 항목을 리뷰 우선 노출 등록 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i>리뷰우선노출등록</div>
	                    <div class="item" onClick="post_multi_action('cre_multi_type1', '0', '선택하신 항목을 리뷰 우선 노출 해제 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i>리뷰우선노출해제</div>
               
                	</div>
            	</div>	

				<div class="btn-group pull-right" role="group" aria-label="...">
					<a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">전체목록</a>
					<a href="<?php echo element('listall_url', $view); ?>?cre_type2=1" class="btn btn-warning btn-sm">리뷰상품 추천 목록</a>
					<a href="<?php echo element('listall_url', $view); ?>?cre_type2=2" class="btn btn-danger btn-sm">리뷰상품 비추천 목록</a>
					<a href="<?php echo element('listall_url', $view); ?>?cre_type=1" class="btn btn-primary btn-sm">리뷰 우선 노출 목록</a>
					<button type="button" class="btn btn-outline btn-default btn-sm btn-list-update btn-list-selected disabled" data-list-update-url = "<?php echo element('list_update_url', $view); ?>" >선택승인</button>
					<button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button>
				</div>
			<?php
			$buttons = ob_get_contents();
			ob_end_flush();
			?>
			</div>
			<div class="row">전체 : <?php echo element('total_rows', element('data', $view), 0); ?>건</div>
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th><a href="<?php echo element('cre_id', element('sort', $view)); ?>">번호</a></th>
							<th><a href="<?php echo element('cit_name', element('sort', $view)); ?>">상품명</a></th>
							<th><a href="<?php echo element('cre_good', element('sort', $view)); ?>">좋았던점</a></th>
							<th><a href="<?php echo element('cre_bad', element('sort', $view)); ?>">아쉬운점</a></th>
							<th>작성자</th>
							<th>일시</th>
							<th><a href="<?php echo element('cre_score', element('sort', $view)); ?>">평점</a></th>
							<th><a href="<?php echo element('cre_score', element('sort', $view)); ?>">조회수</a></th>
							<th><a href="<?php echo element('cre_score', element('sort', $view)); ?>">추천수</a></th>
							<th><a href="<?php echo element('cre_type2', element('sort', $view)); ?>">상품추천</a></th>
							<th><a href="<?php echo element('cre_status', element('sort', $view)); ?>">승인</a></th>
							<th>수정</th>
							<th><input type="checkbox" name="chkall" id="chkall" /></th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (element('list', element('data', $view))) {
						foreach (element('list', element('data', $view)) as $result) {
					?>
						<tr>
							<td><?php echo number_format(element('num', $result)); ?></td>
							<td><a href="<?php echo goto_url(cmall_item_url(element('cit_key', $result))); ?>" target="_blank"><?php echo html_escape(element('cit_name', $result)); ?></a></td>
							<td style="width:250px;"><?php echo html_escape(element('cre_good', $result)); ?></td>
							<td style="width:250px;"><?php echo html_escape(element('cre_bad', $result)); ?></td>
							<td><?php echo element('display_name', $result); ?> <?php if (element('mem_userid', $result)) { ?> ( <a href="?sfield=cmall_review.mem_id&amp;skeyword=<?php echo element('mem_id', $result); ?>"><?php echo html_escape(element('mem_userid', $result)); ?></a> ) <?php } ?></td>
							<td><?php echo display_datetime(element('cre_datetime', $result), 'full'); ?></td>
							<td><?php echo str_repeat('&#9733;', element('cre_score', $result)); ?></td>
							<td><?php echo element('cre_hit', $result); ?></td>
							<td><?php echo element('cre_like', $result); ?></td>
							<td>
								<?php 
								if(element('cre_type2', $result) === '1')
									echo '<button class="btn btn-xs btn-warning">추천</button>';
								elseif(element('cre_type2', $result) === '2') 
									echo '<button class="btn btn-xs btn-danger">비추천</button>';
								?>
									
							</td>
							<td><?php echo (element('cre_status', $result)) ? '<button class="btn btn-xs btn-default">승인</button>' : '<button class="btn btn-xs btn-danger">미승인</button>'; ?></td>
							<td><a href="<?php echo admin_url($this->pagedir); ?>/write/<?php echo element(element('primary_key', $view), $result); ?>?<?php echo $this->input->server('QUERY_STRING', null, ''); ?>" class="btn btn-outline btn-default btn-xs">수정</a>
								<?php if (element('cre_type1', $result)) { ?><label class="label label-danger">리뷰우선노출</label> <?php } ?>
							</td>
							<td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" /></td>
						</tr>
					<?php
						}
					}
					if ( ! element('list', element('data', $view))) {
					?>
						<tr>
							<td colspan="9" class="nopost">자료가 없습니다</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="box-info">
				<?php echo element('paging', $view); ?>
				<div class="pull-left ml20"><?php echo admin_listnum_selectbox();?></div>
				<?php echo $buttons; ?>
			</div>
		<?php echo form_close(); ?>
	</div>
	<form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
		<div class="box-search">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<select class="form-control" name="sfield" >
						<?php echo element('search_option', $view); ?>
					</select>
					<div class="input-group">
						<input type="text" class="form-control" name="skeyword" value="<?php echo html_escape(element('skeyword', $view)); ?>" placeholder="Search for..." />
						<span class="input-group-btn">
							<button class="btn btn-default btn-sm" name="search_submit" type="submit">검색!</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
