<div class="box">
	<div class="box-table">
		<?php
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
			<div class="form-group">
				<label class="col-sm-2 control-label">이미지 업로드</label>
				<div class="col-sm-10">
					<?php
					if (element('the_image', element('data', $view))) {
					?>
						<img src="<?php echo thumb_url('theme',element('the_image', element('data', $view))); ?>" alt="배너 이미지" title="배너 이미지" />
						<label for="the_image_del">
							<input type="checkbox" name="the_image_del" id="the_image_del" value="1" <?php //echo set_checkbox('the_image_del', '1'); ?> /> 삭제
						</label>
					<?php
					}
					?>
					<input type="file" name="the_image" id="the_image" />
					<p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="the_title" value="<?php echo set_value('the_title', element('the_title', element('data', $view))); ?>" /> </div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">시작일</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control datepicker" name="the_start_date" value="<?php echo set_value('the_start_date', element('the_start_date', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">종료일</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control datepicker" name="the_end_date" value="<?php echo set_value('the_end_date', element('the_end_date', element('data', $view))); ?>" />
				</div>
			</div>
			
			<!-- <div class="form-group">
				<label class="col-sm-2 control-label">URL</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="the_url" value="<?php echo set_value('the_url', element('the_url', element('data', $view))); ?>" />
				</div>
			</div> -->
			
			
			<div class="form-group">
				<label class="col-sm-2 control-label">정렬순서</label>
				<div class="col-sm-10">
					<input type="number" class="form-control" name="the_order" value="<?php echo set_value('the_order', (int) element('the_order', element('data', $view))); ?>" />
					<div class="help-inline">정렬 순서가 큰 값이 먼저 출력됩니다</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">활성화</label>
				<div class="col-sm-10">
					<label class="radio-inline" for="the_activated_1">
						<input type="radio" name="the_activated" id="the_activated_1" value="1" <?php echo set_radio('the_activated', '1', (element('the_activated', element('data', $view)) !== '0' ? true : false)); ?> /> 활성
					</label>
					<label class="radio-inline" for="the_activated_0">
						<input type="radio" name="the_activated" id="the_activated_0" value="0" <?php echo set_radio('the_activated', '0', (element('the_activated', element('data', $view)) === '0' ? true : false)); ?> /> 비활성
					</label>
				</div>
			</div>

			

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
			the_title: 'required',
			the_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
			the_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
			the_width: { number:true },
			the_height: { number:true },
			the_order: { number:true },
			the_activated: 'required'
		}
	});
});
//]]>
</script>
