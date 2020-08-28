<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="modal-header">
    <h4 class="modal-title"><?php echo element('page_title',  $layout); ?></h4>
</div>
<div class="modal-body">
    <div class="box">

        <div class="box-table">
            <?php
            echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
            echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
            echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                
                <div>                
                    <div class="form-group">
                                        <label class="col-sm-2 control-label">태그 리스트</label>
                                        <div class="col-sm-4">
                                            <textarea class="form-control" name="cta_tag" id="cta_tag" rows="5"><?php echo set_value('cta_tag', element('cta_tag', element('data', $view))); ?></textarea>
                                            <div class="help-block">태그 입니다(수정 불가)</div>
                                        </div>
                                        <label class="col-sm-2 control-label">검색할 태그들 </label>
                                        <div class="col-sm-4">
                                            <textarea class="form-control" name="search_tag" id="search_tag" rows="5"></textarea>
                                            <div class="help-block"><?php echo element('page_title',  $layout); ?>(엔터로 구분하여 입력)</div>
                                        </div>
                                    </div>
                    <div class="btn-group pull-right" role="group" aria-label="...">        

                        <button type="button" class="btn btn-success btn-sm" onClick="add()"><?php echo element('page_title',  $layout); ?>하기</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
//<![CDATA[
function add() {

    
    
    
   

        var input = "<button class='btn btn-default btn-xs where-btn'><input type='hidden'  name='search_tag' value='"+$('#search_tag').val().replace("\n", ",")+"'></button>";
        

        $("#where",opener.document).append(input);
        
   
    
    
    // self.close();
}
//]]>
</script>