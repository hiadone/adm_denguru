<div class="box">
    <div class="box-table">
        <?php
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open_multipart(current_full_url(), $attributes);
        ?>
            <input type="hidden" name="<?php echo element('primary_key', $view); ?>"    value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
            <input type="hidden" name="egr_id"  value="<?php echo element('egr_id', element('eventgroup', element('data', $view))); ?>" />
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">이미지 업로드</label>
                    <div class="col-sm-10">
                        <?php
                        if (element('eve_image', element('data', $view))) {
                        ?>
                            <img src="<?php echo cdn_url('event',element('eve_image', element('data', $view))); ?>" alt="배너 이미지" title="배너 이미지" />
                            <label for="eve_image_del">
                                <input type="checkbox" name="eve_image_del" id="eve_image_del" value="1" <?php //echo set_checkbox('eve_image_del', '1'); ?> /> 삭제
                            </label>
                        <?php
                        }
                        ?>
                        <input type="file" name="eve_image" id="eve_image" />
                        <p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">색션제목</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="eve_title" value="<?php echo set_value('eve_title', element('eve_title', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">시작일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="eve_start_date" value="<?php echo set_value('eve_start_date', element('eve_start_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">종료일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="eve_end_date" value="<?php echo set_value('eve_end_date', element('eve_end_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트정렬</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="eve_is_center_1">
                            <input type="radio" name="eve_is_center" id="eve_is_center_1" value="1" <?php echo set_radio('eve_is_center', '1', (element('eve_is_center', element('data', $view)) === '1' ? true : false)); ?> /> 가운데정렬
                        </label>
                        <label class="radio-inline" for="eve_is_center_0">
                            <input type="radio" name="eve_is_center" id="eve_is_center_0" value="0" <?php echo set_radio('eve_is_center', '0', (element('eve_is_center', element('data', $view)) !== '1' ? true : false)); ?> /> 좌측정렬
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">좌측위치</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_left" value="<?php echo set_value('eve_left', element('eve_left', element('data', $view))); ?>" />px - 좌측정렬시만 해당
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">상단위치</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_top" value="<?php echo set_value('eve_top', element('eve_top', element('data', $view))); ?>" />px
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트길이</label>
                    <div class="col-sm-10">
                        가로 <input type="number" class="form-control" name="eve_width" value="<?php echo set_value('eve_width', element('eve_width', element('data', $view))); ?>" />px,
                        세로 <input type="number" class="form-control" name="eve_height" value="<?php echo set_value('eve_height', element('eve_height', element('data', $view))); ?>" />px
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트표시기기</label>
                    <div class="col-sm-10 form-inline">
                        <select class="form-control" name="eve_device">
                            <option value="all" <?php echo set_select('eve_device', 'all', (element('eve_device', element('data', $view)) === 'all' ? true : false)); ?>>모든기기</option>
                            <option value="pc" <?php echo set_select('eve_device', 'pc', (element('eve_device', element('data', $view)) === 'pc' ? true : false)); ?>>PC만</option>
                            <option value="mobile" <?php echo set_select('eve_device', 'mobile', (element('eve_device', element('data', $view)) === 'mobile' ? true : false)); ?>>모바일만</option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트이뜨는페이지</label>
                    <div class="col-sm-10 form-inline">
                        <select class="form-control" name="eve_page">
                            <option value="0" <?php echo set_select('eve_page', '0', (element('eve_page', element('data', $view)) !== '1' ? true : false)); ?>>홈페이지에서만</option>
                            <option value="1" <?php echo set_select('eve_page', '1', (element('eve_page', element('data', $view)) === '1' ? true : false)); ?>>모든페이지에서</option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">시간</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_disable_hours" value="<?php echo set_value('eve_disable_hours', element('eve_disable_hours', element('data', $view))); ?>" /> 시간, 닫기 버튼 클릭시 쿠키적용시간, 해당 시간동안 이벤트이 더이상 보이지 않습니다
                    </div>
                </div> -->
                <div class="form-group">
                    <label class="col-sm-2 control-label">정렬순서</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_order" value="<?php echo set_value('eve_order', element('eve_order', element('data', $view)) + 0); ?>" />
                        <div class="help-inline">정렬 순서가 작은 값이 먼저 출력됩니다</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트활성화</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="eve_activated_1">
                            <input type="radio" name="eve_activated" id="eve_activated_1" value="1" <?php echo set_radio('eve_activated', '1', (element('eve_activated', element('data', $view)) !== '0' ? true : false)); ?> /> 활성
                        </label>
                        <label class="radio-inline" for="eve_activated_0">
                            <input type="radio" name="eve_activated" id="eve_activated_0" value="0" <?php echo set_radio('eve_activated', '0', (element('eve_activated', element('data', $view)) === '0' ? true : false)); ?> /> 비활성
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트타입</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="eve_type_1">
                            <input type="radio" name="eve_type" id="eve_type_1" value="1" <?php echo set_radio('eve_type', '1', (element('eve_type', element('data', $view)) !== '2' ? true : false)); ?> /> 소제목 있는 타입
                        </label>
                        <label class="radio-inline" for="eve_type_2">
                            <input type="radio" name="eve_type" id="eve_type_2" value="2" <?php echo set_radio('eve_type', '2', (element('eve_type', element('data', $view)) === '2' ? true : false)); ?> /> 소제목 없는 타입
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">내용</label>
                    <div class="col-sm-10">
                        <?php echo display_dhtml_editor('eve_content', set_value('eve_content', element('eve_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_popup_dhtml'), $editor_type = $this->cbconfig->item('popup_editor_type')); ?>
                    </div>
                </div>
                
                
                


                <div class="btn-group pull-right" role="group" aria-label="...">
                    <button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
                    <button type="submit" class="btn btn-success btn-sm">저장하기</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>



<script type="text/javascript">
//<![CDATA[
$(function() {
    $('#fadminwrite').validate({
        rules: {
            eve_title: 'required',
            eve_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
            eve_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
            // eve_is_center: { required:true, number:true },
            // eve_left: { required :'#eve_is_center_1:checked', number:true },
            // eve_top: { required:true, number:true },
            // eve_width: { required:true, number:true },
            // eve_height: { required:true, number:true },
            // eve_device: 'required',
            // eve_page: 'required',
            // eve_disable_hours: { required:true, number:true },
            eve_activated: 'required',
            eve_order: { number:true }
            
        }
    });
});
//]]>
</script>
