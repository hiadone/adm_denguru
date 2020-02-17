<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php $this->managelayout->add_js('/assets/js/jquery-ui-timepicker-addon.js'); ?>
<?php $this->managelayout->add_css('/assets/css/jquery-ui-timepicker-addon.css'); ?>
<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<?php echo element('headercontent', element('board', $view)); ?>

<div class="board">
	<h3>FCM 발송</h3>
	<?php
	echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
	echo show_alert_message(element('message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info">', '</div>');
	$attributes = array('class' => 'form-horizontal', 'name' => 'fwrite', 'id' => 'fwrite');
	echo form_open(current_full_url(), $attributes);
	?>
		<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('post', $view)); ?>" />
		<div class="form-horizontal box-table">
			<div class="form-group">
				<label for="fcm_title" class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10" style="display:table;">
					<input type="text" class="form-control" name="fcm_title" id="fcm_title" value="<?php echo set_value('fcm_title', element('fcm_title', element('post', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label for="fcm_target" class="col-sm-2 control-label">발송타켓
					
				</label>
				<div class="col-sm-10 form-inline" style="display:table;">
					<?php

					$group_value = json_decode(element('fcm_target', element('post', $view)), true);
					$html='';
					if (element('mgroup', element('data', $view))) {
						foreach (element('mgroup', element('data', $view)) as $key => $value) {
							$html .= '	<label class="checkbox-inline">
								<input type="checkbox" name="fcm_target[]" value="' . element('mgr_id', $value) . '" ';
							if($group_value)
								$html .= is_array($group_value) && in_array(element('mgr_id', $value), $group_value) ? 'checked="checked"' : '';
							else 
								$html .= 'checked="checked"';
							$html .= ' /> ' . element('mgr_title', $value) . '</label>';
						}
						
						// echo $html;
					}
					?>
					<?php
					$config = array(
						'column_name' => 'fcm_target',
						'column_group_name' => 'fcm_target_group',
						'column_value' => element('fcm_target', element('post', $view)),
						'column_group_value' => element('fcm_target_group', element('post', $view)),
						'mgroup' => element('mgroup', element('data', $view)),
						);
					echo get_fcm_send_selectbox($config);
					?>
				</div>
			</div>
			<div class="form-group">
				<label for="fcm_send_date" class="col-sm-2 control-label">예약발송시간</label>
				<div class="col-sm-10" style="display:table;">
					<input type="text" class="form-control input datetimepicker" readonly="readonly" name="fcm_send_date" id="fcm_send_date" value="<?php echo set_value('fcm_send_date', element('fcm_send_date', element('post', $view))); ?>" />
					<span style="color:red;">* 미 입력시 즉시 발송</span>

				</div>
			</div>
			
			<div class="form-group">
				<label for="fcm_message" class="col-sm-2 control-label">내용</label>
				<div class="col-sm-10" style="display:table;">
					<?php echo display_dhtml_editor('fcm_message', set_value('fcm_message', element('fcm_message', element('post', $view))), $classname = 'form-control ', $is_dhtml_editor = false, $editor_type = $this->cbconfig->item('post_editor_type')); ?>
				</div>
			</div>

			<div class="border_button text-center mt20">
				<button type="button" class="btn btn-default btn-sm btn-history-back">취소</button>
				<button type="submit" class="btn btn-success btn-sm">작성완료</button>
			</div>
		</div>
	<?php echo form_close(); ?>
</div>

<?php echo element('footercontent', element('board', $view)); ?>


<?php
if ( element('is_use_captcha', element('board', $view)) ) {
	if ($this->cbconfig->item('use_recaptcha')) {
		$this->managelayout->add_js(base_url('assets/js/recaptcha.js'));
	} else {
		$this->managelayout->add_js(base_url('assets/js/captcha.js'));
	}
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$('#fwrite').validate({
		rules: {
			fcm_title: {required :true, minlength:2, maxlength:60},
			fcm_message: {required :true}
		}
	});
});

$('.datetimepicker').datetimepicker({
    dateFormat:'yy-mm-dd',
    monthNamesShort:[ '1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월' ],
    dayNamesMin:[ '일', '월', '화', '수', '목', '금', '토' ],
    changeMonth:true,
    changeYear:true,
    showMonthAfterYear:true,

    // timepicker 설정
    timeFormat:'HH:mm',
    controlType:'select',
    oneLine:true,
});



//]]>
</script>
