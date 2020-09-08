<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		echo show_alert_message(element('message', $view), '<div class="alert alert-auto-close alert-dismissible alert-warning">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
			<div class="form-group">
				<label class="col-sm-2 control-label">상품명</label>
				<div class="col-sm-10">
					<?php 
						echo element('cit_name', element('data', $view));
					 ?>
				</div>
			</div>
			<?php 
			

			$file_count = 10;
			for ($i = 0; $i < $file_count; $i++) {

				$rfi_is_image = element('rfi_is_image', element($i, element('file', $view)));
				$download_link = html_escape(element('download_link', element($i, element('file', $view))));
				$file_column = $download_link ? 'cre_file_update[' . element('rfi_id', element($i, element('file', $view))) . ']' : 'cre_file[' . $i . ']';
				$del_column = $download_link ? 'cre_file_del[' . element('rfi_id', element($i, element('file', $view))) . ']' : '';
			?>
				<div class="form-group">
					<label for="<?php echo $file_column; ?>" class="col-sm-2 control-label">파일 #<?php echo $i+1; ?></label>
					<div class="col-sm-10">
						<input type="file" class="form-control" name="<?php echo $file_column; ?>" id="<?php echo $file_column; ?>" />
						<?php if ($download_link) { ?>
							<?php if ($rfi_is_image) { ?>

							<img src="<?php echo element('image_url', element($i, element('file', $view))); ?>" alt=" 이미지" title="이미지" />

							
							<?php } else {?>
								<?php echo element('file_player', element($i, element('file', $view))); ?>

							<?php } ?>
							<label for="<?php echo $del_column; ?>">
								<input type="checkbox" name="<?php echo $del_column; ?>" id="<?php echo $del_column; ?>" value="1" <?php echo set_checkbox($del_column, '1'); ?> /> 삭제
							</label>
						<?php } ?>
					</div>
				</div>
			<?php
			}
			
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label">좋은점</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="cre_good" value="<?php echo set_value('cre_good', element('cre_good', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">아쉬운점</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="cre_bad" value="<?php echo set_value('cre_bad', element('cre_bad', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">나만의 TIP</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="cre_tip" value="<?php echo set_value('cre_tip', element('cre_tip', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">평점</label>
				<div class="col-sm-10 form-inline">
					<select name="cre_score" class="form-control">
						<option value="">평점주기</option>
						<option value="5" <?php echo set_select('cre_score', '5', (element('cre_score', element('data', $view)) === '5' ? true : false)); ?>>&#9733;&#9733;&#9733;&#9733;&#9733; (5)</option>
						<option value="4" <?php echo set_select('cre_score', '4', (element('cre_score', element('data', $view)) === '4' ? true : false)); ?>>&#9733;&#9733;&#9733;&#9733;&#9734; (4)</option>
						<option value="3" <?php echo set_select('cre_score', '3', (element('cre_score', element('data', $view)) === '3' ? true : false)); ?>>&#9733;&#9733;&#9733;&#9734;&#9734; (3)</option>
						<option value="2" <?php echo set_select('cre_score', '2', (element('cre_score', element('data', $view)) === '2' ? true : false)); ?>>&#9733;&#9733;&#9734;&#9734;&#9734; (2)</option>
						<option value="1" <?php echo set_select('cre_score', '1', (element('cre_score', element('data', $view)) === '1' ? true : false)); ?>>&#9733;&#9734;&#9734;&#9734;&#9734; (1)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">승인</label>
				<div class="col-sm-10 form-inline">
					<label class="radio-inline" for="cre_status_1" >
						<input type="radio" name="cre_status" id="cre_status_1" value="1" <?php echo set_checkbox('cre_status', '1', (element('cre_status', element('data', $view)) === '1' ? true : false)); ?> /> 승인 (사용자에게 보임)
					</label>
					<label class="radio-inline" for="cre_status_0" >
						<input type="radio" name="cre_status" id="cre_status_0" value="0" <?php echo set_checkbox('cre_status', '0', (element('cre_status', element('data', $view)) !== '1' ? true : false)); ?> /> 미승인 (사용자에게 보이지 않음)
					</label>
				</div>
			</div>
			<!-- <div class="form-group">
				<label class="col-sm-2 control-label">내용</label>
				<div class="col-sm-10">
					<?php echo display_dhtml_editor('cre_content', set_value('cre_content', element('cre_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_cmall_product_review_dhtml'), $editor_type = $this->cbconfig->item('cmall_product_review_editor_type')); ?>
				</div>
			</div> -->
			<div class="btn-group pull-right" role="group" aria-label="...">
				<button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$('#fadminwrite').validate({
		rules: {
			cre_title: 'required',
			cre_content : {<?php echo ($this->cbconfig->item('use_cmall_product_review_dhtml')) ? 'required_' . $this->cbconfig->item('cmall_product_review_editor_type') : 'required'; ?> : true }
		}
	});
});
//]]>
</script>
