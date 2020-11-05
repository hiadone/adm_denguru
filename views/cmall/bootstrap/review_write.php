<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<div class="modal-header">
	<h4 class="modal-title">상품후기 작성</h4>
</div>
<div class="modal-body">
	<div class="form-horizontal ">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fwrite', 'id' => 'fwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
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
								<input type="checkbox" name="<?php echo $del_column; ?>" id="<?php echo $del_column; ?>" value="1" <?php //echo set_checkbox($del_column, '1'); ?> /> 삭제
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
			<div class="form-group mt20">
				<label for="cre_title" class="col-sm-2 control-label">평점</label>
				<div class="col-sm-10 review-score">
					<label for="cre_score_5" class="col-xs-6 col-sm-4"><input type="radio" name="cre_score" id="cre_score_5" value="5" <?php echo set_radio('cre_score', '5', ((element('cre_score', element('data', $view)) === '5' OR ! element('cre_score', element('data', $view))) ? true : false)); ?> /> <img src="<?php echo element('view_skin_url', $layout); ?>/images/star5.png" alt="매우만족" title="매우만족" style="vertical-align:top;" /></label>
					<label for="cre_score_4" class="col-xs-6 col-sm-4"><input type="radio" name="cre_score" id="cre_score_4" value="4" <?php echo set_radio('cre_score', '4', (element('cre_score', element('data', $view)) === '4' ? true : false)); ?> /> <img src="<?php echo element('view_skin_url', $layout); ?>/images/star4.png" alt="만족" title="만족" style="vertical-align:top;" /></label>
					<label for="cre_score_3" class="col-xs-6 col-sm-4"><input type="radio" name="cre_score" id="cre_score_3" value="3" <?php echo set_radio('cre_score', '3', (element('cre_score', element('data', $view)) === '3' ? true : false)); ?> /> <img src="<?php echo element('view_skin_url', $layout); ?>/images/star3.png" alt="보통" title="보통" style="vertical-align:top;" /></label>
					<label for="cre_score_2" class="col-xs-6 col-sm-4"><input type="radio" name="cre_score" id="cre_score_2" value="2" <?php echo set_radio('cre_score', '2', (element('cre_score', element('data', $view)) === '2' ? true : false)); ?> /> <img src="<?php echo element('view_skin_url', $layout); ?>/images/star2.png" alt="불만" title="불만" style="vertical-align:top;" /></label>
					<label for="cre_score_1" class="col-xs-6 col-sm-4"><input type="radio" name="cre_score" id="cre_score_1" value="1" <?php echo set_radio('cre_score', '1', (element('cre_score', element('data', $view)) === '1' ? true : false)); ?> /> <img src="<?php echo element('view_skin_url', $layout); ?>/images/star1.png" alt="매우불만" title="매우불만" style="vertical-align:top;" /></label>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<div class="pull-right">
					<a href="javascript:;" class="btn btn-default" onClick="window.close();">취소</a>
					<button type="submit" class="btn btn-primary">작성완료</button>
				</div>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$('#fwrite').validate({
		rules: {
			cre_title : { required:true},
			cre_content : {<?php echo ($this->cbconfig->item('use_cmall_product_review_dhtml')) ? 'required_' . $this->cbconfig->item('cmall_product_review_editor_type') : 'required'; ?> : true }
		}
	});
});
//]]>
</script>
