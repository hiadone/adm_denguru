<div class="box">
    <div class="box-table">
        <?php
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open_multipart(current_full_url(), $attributes);
        ?>
            
            <input type="hidden" name="<?php echo element('primary_key', $view) ?>"  value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
            <input type="hidden" name="brd_id"  value="<?php echo element('brd_id', element('data', $view)); ?>" />
            

                    
            
            <div class="form-horizontal">
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">상품이미지</label>
                    <div class="col-sm-6">
                        <?php if (element('brd_image', element('data', $view))) {?><img src="<?php echo cdn_url('board', element('brd_image', element('data', $view))); ?>" alt="<?php echo html_escape(element('brd_name', element('data', $view))); ?>" title="<?php echo html_escape(element('brd_name', element('data', $view))); ?>" class="thumbnail mg0" style="width:80px;" /><?php } ?>
                        
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">상품명</label>
                    <div class="col-sm-6">
                        <?php echo element('brd_name', element('data', $view)); ?>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">시작일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="thr_start_date" value="<?php echo set_value('thr_start_date', element('thr_start_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">종료일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="thr_end_date" value="<?php echo set_value('thr_end_date', element('thr_end_date', element('data', $view))); ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">정렬순서</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="thr_order" value="<?php echo set_value('thr_order', element('thr_order', element('data', $view)) + 0); ?>" />
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
            brd_id: 'required',
            brd_name: 'required',
            thr_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
            thr_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
            thr_order: { number:true }
            
        }
    });
});
//]]>
</script>
