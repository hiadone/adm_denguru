<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info">', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info">', '</div>');

        
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />

			<div class="form-group">
			    <label class="col-sm-2 control-label">이미지 업로드</label>
			    <div class="col-sm-10">
			        <?php
			        if (element('egr_image', element('data', $view))) {
			        ?>
			            <img src="<?php echo cdn_url('eventgroup',element('egr_image', element('data', $view))); ?>" alt="배너 이미지" title="배너 이미지" />
			            <label for="egr_image_del">
			                <input type="checkbox" name="egr_image_del" id="egr_image_del" value="1" <?php //echo set_checkbox('egr_image_del', '0'); ?> /> 삭제
			            </label>
			        <?php
			        }
			        ?>
			        <input type="file" name="egr_image" id="egr_image" />
			        <p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
			    </div>
			</div>

            <div class="form-group">
                <label class="col-sm-2 control-label">상세 이미지 업로드</label>
                <div class="col-sm-10">
                    <?php
                    if (element('egr_detail_image', element('data', $view))) {
                    ?>
                        <img src="<?php echo cdn_url('eventgroup',element('egr_detail_image', element('data', $view))); ?>" alt="배너 이미지" title="배너 이미지" />
                        <label for="egr_image_del">
                            <input type="checkbox" name="egr_detail_image_del" id="egr_detail_image_del" value="1" <?php echo set_checkbox('egr_image_del', '0'); ?> /> 삭제
                        </label>
                    <?php
                    }
                    ?>
                    <input type="file" name="egr_detail_image" id="egr_detail_image" />
                    <p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-2 control-label">제목</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="egr_title" value="<?php echo set_value('egr_title', element('egr_title', element('data', $view))); ?>" />
				</div>
			</div>
            <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트타입</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="egr_type_1">
                            <input type="radio" name="egr_type" id="egr_type_1" value="1" <?php echo set_radio('egr_type', '1', ((element('egr_type', element('data', $view)) !== '2'  && element('egr_type', element('data', $view))) !== '3' ? true : false)); ?> /> 소제목 있는 스페셜
                        </label>
                        <label class="radio-inline" for="egr_type_2">
                            <input type="radio" name="egr_type" id="egr_type_2" value="2" <?php echo set_radio('egr_type', '2', (element('egr_type', element('data', $view)) === '2' ? true : false)); ?> /> 소제목 없는 스페셜
                        </label>
                        <label class="radio-inline" for="egr_type_3">
                            <input type="radio" name="egr_type" id="egr_type_3" value="3" <?php echo set_radio('egr_type', '3', (element('egr_type', element('data', $view)) === '3' ? true : false)); ?> /> 그냥 이벤트
                        </label>
                    </div>
                </div>
			<div class="form-group">
                <label class="col-sm-2 control-label">시작일</label>
                <div class="col-sm-10 form-inline">
                    <input type="text" class="form-control datepicker" name="egr_start_date" value="<?php echo set_value('egr_start_date', element('egr_start_date', element('data', $view))); ?>" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">종료일</label>
                <div class="col-sm-10 form-inline">
                    <input type="text" class="form-control datepicker" name="egr_end_date" value="<?php echo set_value('egr_end_date', element('egr_end_date', element('data', $view))); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">정렬순서</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="egr_order" value="<?php echo set_value('egr_order', element('egr_order', element('data', $view)) + 0); ?>" />
                    <div class="help-inline">정렬 순서가 작은 값이 먼저 출력됩니다</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">이벤트활성화</label>
                <div class="col-sm-10">
                    <label class="radio-inline" for="egr_activated_1">
                        <input type="radio" name="egr_activated" id="egr_activated_1" value="1" <?php echo set_radio('egr_activated', '1', (element('egr_activated', element('data', $view)) !== '0' ? true : false)); ?> /> 활성
                    </label>
                    <label class="radio-inline" for="egr_activated_0">
                        <input type="radio" name="egr_activated" id="egr_activated_0" value="0" <?php echo set_radio('egr_activated', '0', (element('egr_activated', element('data', $view)) === '0' ? true : false)); ?> /> 비활성
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">내용</label>
                <div class="col-sm-10">
                    <?php echo display_dhtml_editor('egr_content', set_value('egr_content', element('egr_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_popup_dhtml'), $editor_type = $this->cbconfig->item('popup_editor_type')); ?>
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
			egr_title: 'required'
			
		}
	});
});
//]]>
</script>
