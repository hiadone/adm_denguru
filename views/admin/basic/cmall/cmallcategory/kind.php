<div class="box">
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir); ?>" onclick="return check_form_changed();">카테고리 관리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/attr'); ?>" onclick="return check_form_changed();">제품특성 관리</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/kind'); ?>" onclick="return check_form_changed();">견종 관리</a></li>
            
        </ul>
    </div>
    <div class="box-table">
        <?php
        
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        ?>
        <ul class="list-group">
            <?php
            $return ='';

            $data = element('data', $view);

            
            if ($data && is_array($data)) {

                foreach ($data as $result) {
                    $ckd_size='';

                    
                    if(element('ckd_size', $result) === "4") $ckd_size = "소형견";
                    elseif(element('ckd_size', $result) === "5") $ckd_size = "중형견";
                    elseif(element('ckd_size', $result) === "6") $ckd_size =  "대형견";

                    $attributes = array('class' => 'form-inline', 'name' => 'fbrand');
                    $return .= '<li class="list-group-item">
                                        <div class="form-horizontal">
                                            <div class="form-group" style="margin-bottom:0;">';
                    
                    $return .= '<div class="pl10">
                        <div class="cat-ckd-id-' . element('ckd_id', $result) . '">
                            ' . html_escape(element('ckd_value_kr', $result)) . ' (' . html_escape(element('ckd_value_en', $result)) . ')'.'<button class="btn btn-info btn-xs">'.$ckd_size.'</button>
                            <button class="btn btn-primary btn-xs" onClick="cat_modify(\'' . element('ckd_id', $result) . '\')"><span class="glyphicon glyphicon-edit"></span></button>';
                    
                    $return .= '                    <button class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url = "' . admin_url('cmall/cmallcategory/kind_delete/' . element('ckd_id', $result)) . '"><span class="glyphicon glyphicon-trash"></span></button>';
                    $return .= '<button>'.html_escape(element('ckd_text', $result)).'</button>';
                    $return .= '    </div><div class="form-inline mod-ckd-id-' . element('ckd_id', $result) . '" style="display:none;">';
                    $return .= form_open(current_full_url(), $attributes);
                    $return .= '<input type="hidden" name="ckd_id"  value="' . element('ckd_id', $result) . '" />
                                                        <input type="hidden" name="type" value="modify" />
                                                        <div class="form-group" style="margin-left:0;">
                                                            한글 브랜드명 <input type="text" class="form-control" name="ckd_value_kr" value="' . html_escape(element('ckd_value_kr', $result)) . '" />
                                                            영문 브랜드명 <input type="text" class="form-control" name="ckd_value_en" value="' . html_escape(element('ckd_value_en', $result)) . '"/>
                                                            크기  <input type="text" class="form-control" name="ckd_size" value="' . $ckd_size . '"/>
                                                            사전 <textarea  class="form-control" style="width:300px;" name="ckd_text" rows="1" >' . html_escape(element('ckd_text', $result)) . '</textarea>
                                                            <button class="btn btn-primary btn-xs" type="submit" >저장</button>
                                                            <a href="javascript:;" class="btn btn-default btn-xs" onClick="cat_cancel(\'' . element('ckd_id', $result) . '\')">취소</a>
                                                        </div>';
                    $return .= form_close();
                    $return .= '</div>
                                                </div>
                                                </div>
                                            </div>
                                        </li>';
                    
                }
                echo $return;
            }
                
            ?>
        </ul>
    <div>
        <div class="box-table">
            <?php
            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                <input type="hidden" name="is_submit" value="1" />
                <input type="hidden" name="type" value="add" />
                <div class="form-group">
                    <label class="col-sm-2 control-label">견종 추가</label>
                    <div class="col-sm-8 form-inline">
                        
                        <input type="text" name="ckd_value_kr" class="form-control" value="" placeholder="한글 견종 입력" />
                        <input type="text" name="ckd_value_en" class="form-control" value="" placeholder="영문 견종 입력" />
                        <input type="text" name="ckd_size" class="form-control" value="" placeholder="견종 크기 입력" />
                        <textarea class="form-control" style="width:300px;" name="ckd_text" id="ckd_text" rows="1" placeholder="사전 (콤마로 구분하여 입력)"><?php echo set_value('ckd_text', element('ckd_text', element('data', $view))); ?></textarea>
                        <button type="submit" class="btn btn-success btn-sm">추가하기</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $('#fadminwrite', 'input[name=fcategory]').validate({
        rules: {
            ckd_value_kr: {required :true},
            ckd_value_en: {required :true},
        }
    });
});



function cat_modify(ckd_id) {
    $('.cat-ckd-id-' + ckd_id).hide();
    $('.mod-ckd-id-' + ckd_id).show();
}
function cat_cancel(ckd_id) {
    $('.cat-ckd-id-' + ckd_id).show();
    $('.mod-ckd-id-' + ckd_id).hide();
}
//]]>
</script>
