<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
			<div class="form-group">
				<label class="col-sm-2 control-label">이미지 업로드</label>
				<div class="col-sm-10">
					<?php
					if (element('oth_image', element('data', $view))) {
					?>
						<img src="<?php echo thumb_url('other',element('oth_image', element('data', $view))); ?>" alt="배너 이미지" title="배너 이미지" />
						<label for="oth_image_del">
							<input type="checkbox" name="oth_image_del" id="oth_image_del" value="1" <?php echo set_checkbox('oth_image_del', '1'); ?> /> 삭제
						</label>
					<?php
					}
					?>
					<input type="file" name="oth_image" id="oth_image" />
					<p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">이미지 설명</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="oth_title" value="<?php echo set_value('oth_title', element('oth_title', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">시작일</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control datepicker" name="oth_start_date" value="<?php echo set_value('oth_start_date', element('oth_start_date', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">종료일</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control datepicker" name="oth_end_date" value="<?php echo set_value('oth_end_date', element('oth_end_date', element('data', $view))); ?>" />
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">URL</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="oth_url" value="<?php echo set_value('oth_url', element('oth_url', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">배너 사이즈</label>
				<div class="col-sm-10 form-inline">
					가로 :
					<input type="number" class="form-control" name="oth_width" value="<?php echo set_value('oth_width', (int) element('oth_width', element('data', $view))); ?>" />px
					,
					세로 :
					<input type="number" class="form-control" name="oth_height" value="<?php echo set_value('oth_height', (int) element('oth_height', element('data', $view))); ?>" />px
					<div class="help-inline">가로값과 세로값을 입력하시면 입력하신 사이즈로 배너가 출력이 되며, 입력하지 않으면 업로드한 원본 크기대로 출력됩니다</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">정렬순서</label>
				<div class="col-sm-10">
					<input type="number" class="form-control" name="oth_order" value="<?php echo set_value('oth_order', (int) element('oth_order', element('data', $view))); ?>" />
					<div class="help-inline">정렬 순서가 큰 값이 먼저 출력됩니다</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">활성화</label>
				<div class="col-sm-10">
					<label class="radio-inline" for="oth_activated_1">
						<input type="radio" name="oth_activated" id="oth_activated_1" value="1" <?php echo set_radio('oth_activated', '1', (element('oth_activated', element('data', $view)) !== '0' ? true : false)); ?> /> 활성
					</label>
					<label class="radio-inline" for="oth_activated_0">
						<input type="radio" name="oth_activated" id="oth_activated_0" value="0" <?php echo set_radio('oth_activated', '0', (element('oth_activated', element('data', $view)) === '0' ? true : false)); ?> /> 비활성
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
			oth_title: 'required',
			oth_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
			oth_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
			oth_width: { number:true },
			oth_height: { number:true },
			oth_order: { number:true },
			oth_activated: 'required'
		}
	});
});
//]]>
</script>
