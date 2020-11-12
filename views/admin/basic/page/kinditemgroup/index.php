<div class="box">
	<div class="box-table">
		<?php
		echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		$attributes = array('class' => 'form-inline', 'name' => 'flist', 'id' => 'flist');
		echo form_open(current_full_url(), $attributes);
		?>
			<div class="box-table-header">
				<?php
				ob_start();
				?>
					<div class="btn-group pull-right" role="group" aria-label="...">
						<a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">견종별 item 전체목록</a>
						<button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button>
						<a href="<?php echo element('write_url', $view); ?>" class="btn btn-outline btn-danger btn-sm">견종별 item 추가</a>
					</div>
				<?php
				$buttons = ob_get_contents();
				ob_end_flush();
				?>
			</div>
			<div class="row">전체 : <?php echo count(element('list', element('data', $view), 0)); ?>건</div>
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th>번호</th>
							<th><a href="<?php echo element('ckd_value_kr', element('sort', $view)); ?>">견종 명</a></th>
							<th><a href="<?php echo element('ckd_value_en', element('sort', $view)); ?>">견종 영문명</a></th>
							<th><a href="<?php echo element('ckd_size', element('sort', $view)); ?>">견종 사이즈</a></th>
							<th><a href="<?php echo element('kinditem_count', element('sort', $view)); ?>">종속된 상품 수</a></th>
							<th>action</th>
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
							<td><?php echo html_escape(element('ckd_value_kr', $result)); ?></td>
							<td><?php echo html_escape(element('ckd_value_en', $result)); ?></td>
							<td>
								<?php 

								if(element('ckd_size', $result)=='4') echo '소형견';
								elseif(element('ckd_size', $result)=='5') echo '중형견';
								elseif(element('ckd_size', $result)=='6') echo '대형견';
								?>
							</td>
							<td><?php echo (int) element('kinditem_count', $result); ?></td>
							<td><a href="<?php echo admin_url($this->pagedir.'/lists/'.element(element('primary_key', $view), $result)); ?>" class="btn btn-outline btn-primary btn-xs" >종속 아이템 관리</a></td>
							<td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" /></td>
							
							
						</tr>
					<?php
						}
					}
					if ( ! element('list', element('data', $view))) {
					?>
						<tr>
							<td colspan="13" class="nopost">자료가 없습니다</td>
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
	<input type="hidden" name="kig_id" value="<?php echo $this->input->get('kig_id'); ?>" />
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
