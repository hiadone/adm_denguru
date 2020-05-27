<div class="box">
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open(current_full_url(), $attributes);
        ?>
            <input type="hidden" name="<?php echo element('primary_key', $view); ?>"    value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">제목</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="noti_title" value="<?php echo set_value('noti_title', element('noti_title', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">시작일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="noti_start_date" value="<?php echo set_value('noti_start_date', element('noti_start_date', element('data', $view))); ?>" />
                        시작일과 종료일은 따로 지정 안하셔도 됩니다.
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">종료일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="noti_end_date" value="<?php echo set_value('noti_end_date', element('noti_end_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">팝업정렬</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="noti_is_center_1">
                            <input type="radio" name="noti_is_center" id="noti_is_center_1" value="1" <?php echo set_radio('noti_is_center', '1', (element('noti_is_center', element('data', $view)) === '1' ? true : false)); ?> /> 가운데정렬
                        </label>
                        <label class="radio-inline" for="noti_is_center_0">
                            <input type="radio" name="noti_is_center" id="noti_is_center_0" value="0" <?php echo set_radio('noti_is_center', '0', (element('noti_is_center', element('data', $view)) !== '1' ? true : false)); ?> /> 좌측정렬
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">좌측위치</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="noti_left" value="<?php echo set_value('noti_left', element('noti_left', element('data', $view))); ?>" />px - 좌측정렬시만 해당
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">상단위치</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="noti_top" value="<?php echo set_value('noti_top', element('noti_top', element('data', $view))); ?>" />px
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">팝업길이</label>
                    <div class="col-sm-10">
                        가로 <input type="number" class="form-control" name="noti_width" value="<?php echo set_value('noti_width', element('noti_width', element('data', $view))); ?>" />px,
                        세로 <input type="number" class="form-control" name="noti_height" value="<?php echo set_value('noti_height', element('noti_height', element('data', $view))); ?>" />px
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">팝업표시기기</label>
                    <div class="col-sm-10 form-inline">
                        <select class="form-control" name="noti_device">
                            <option value="all" <?php echo set_select('noti_device', 'all', (element('noti_device', element('data', $view)) === 'all' ? true : false)); ?>>모든기기</option>
                            <option value="pc" <?php echo set_select('noti_device', 'pc', (element('noti_device', element('data', $view)) === 'pc' ? true : false)); ?>>PC만</option>
                            <option value="mobile" <?php echo set_select('noti_device', 'mobile', (element('noti_device', element('data', $view)) === 'mobile' ? true : false)); ?>>모바일만</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">팝업이뜨는페이지</label>
                    <div class="col-sm-10 form-inline">
                        <select class="form-control" name="noti_page">
                            <option value="0" <?php echo set_select('noti_page', '0', (element('noti_page', element('data', $view)) !== '1' ? true : false)); ?>>홈페이지에서만</option>
                            <option value="1" <?php echo set_select('noti_page', '1', (element('noti_page', element('data', $view)) === '1' ? true : false)); ?>>모든페이지에서</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">시간</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="noti_disable_hours" value="<?php echo set_value('noti_disable_hours', element('noti_disable_hours', element('data', $view))); ?>" /> 시간, 닫기 버튼 클릭시 쿠키적용시간, 해당 시간동안 팝업이 더이상 보이지 않습니다
                    </div>
                </div> -->
                <div class="form-group">
                    <label class="col-sm-2 control-label">정렬순서</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="noti_order" value="<?php echo set_value('noti_order', element('noti_order', element('data', $view)) + 0); ?>" />
                        <div class="help-inline">정렬 순서가 작은 값이 먼저 출력됩니다</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">공지활성화</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="noti_activated_1">
                            <input type="radio" name="noti_activated" id="noti_activated_1" value="1" <?php echo set_radio('noti_activated', '1', (element('noti_activated', element('data', $view)) === '1' ? true : false)); ?> /> 활성
                        </label>
                        <label class="radio-inline" for="noti_activated_0">
                            <input type="radio" name="noti_activated" id="noti_activated_0" value="0" <?php echo set_radio('noti_activated', '0', (element('noti_activated', element('data', $view)) !== '1' ? true : false)); ?> /> 비활성
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">내용</label>
                    <div class="col-sm-10">
                        <?php echo display_dhtml_editor('noti_content', set_value('noti_content', element('noti_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_popup_dhtml'), $editor_type = $this->cbconfig->item('popup_editor_type')); ?>
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
            noti_title: 'required',
            noti_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
            noti_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
            // noti_is_center: { required:true, number:true },
            // noti_left: { required :'#noti_is_center_1:checked', number:true },
            // noti_top: { required:true, number:true },
            // noti_width: { required:true, number:true },
            // noti_height: { required:true, number:true },
            // noti_device: 'required',
            // noti_page: 'required',
            // noti_disable_hours: { required:true, number:true },
            noti_activated: 'required',
            noti_order: { number:true },
            noti_content : {<?php echo ($this->cbconfig->item('use_popup_dhtml')) ? 'required_' . $this->cbconfig->item('popup_editor_type') : 'required'; ?> : true }
        }
    });
});
//]]>
</script>
