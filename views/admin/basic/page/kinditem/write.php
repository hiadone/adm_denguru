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
            <input type="hidden" name="kig_id"  value="<?php echo element('kig_id', element('kinditemgroup', element('data', $view))); ?>" />
            <input type="hidden" name="cit_id" id="cit_id"  value="" />

                    <button type="button" class="btn btn-default btn-sm admin-manage-list" onClick="event_in_cmall_item(<?php echo element(element('primary_key', $view), element('data', $view)); ?>);" >이벤트에 상품 종속 시키기</button>
            
            <div class="form-horizontal">
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">상품리스트</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="cit_name" value="<?php echo set_value('cit_name', element('cit_name', element('data', $view))); ?>" />
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">시작일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="kir_start_date" value="<?php echo set_value('kir_start_date', element('kir_start_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">종료일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="kir_end_date" value="<?php echo set_value('kir_end_date', element('kir_end_date', element('data', $view))); ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">정렬순서</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="kir_order" value="<?php echo set_value('kir_order', element('kir_order', element('data', $view)) + 0); ?>" />
                        <div class="help-inline">정렬 순서가 작은 값이 먼저 출력됩니다</div>
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
            cit_id: 'required',
            cit_name: 'required',
            kir_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
            kir_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
            kir_order: { number:true }
            
        }
    });
});
//]]>
</script>
