<div class="box">
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir); ?>" onclick="return check_form_changed();">카테고리 관리</a></li>
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir . '/attr'); ?>" onclick="return check_form_changed();">제품특성 관리</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/breed'); ?>" onclick="return check_form_changed();">견종 관리</a></li>
            
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
            $data = element('data', $view);
            function cmall_ca_list($p, $data, $len)
            {
                $return = '';
                $nextlen = $len + 1;
                if ($p && is_array($p)) {
                    foreach ($p as $result) {
                        $margin = 20 * $len;
                        $attributes = array('class' => 'form-inline', 'name' => 'fcategory');
                        $return .= '<li class="list-group-item">
                                            <div class="form-horizontal">
                                                <div class="form-group" style="margin-bottom:0;">';
                        if ($len) {
                            $return .= '<div style="width:10px;float:left;margin-left:' . $margin . 'px;margin-right:10px;"><span class="fa fa-arrow-right"></span></div>';
                        }
                        $return .= '<div class="pl10">
                            <div class="cat-cat-id-' . element('ced_id', $result) . '">
                                ' . html_escape(element('ced_value', $result)) . ' 
                                <button class="btn btn-primary btn-xs" onClick="ced_modify(\'' . element('ced_id', $result) . '\')"><span class="glyphicon glyphicon-edit"></span></button>';
                        if ( ! element(element('ced_id', $result), $data)) {
                            $return .= '                    <button class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url = "' . admin_url('cmall/cmallcategory/breed_delete/' . element('ced_id', $result)) . '"><span class="glyphicon glyphicon-trash"></span></button>';
                        }
                        $return .= '    </div><div class="form-inline mod-cat-id-' . element('ced_id', $result) . '" style="display:none;">';
                        $return .= form_open(current_full_url(), $attributes);
                        $return .= '<input type="hidden" name="ced_id"  value="' . element('ced_id', $result) . '" />
                                                            <input type="hidden" name="type" value="modify" />
                                                            <div class="form-group" style="margin-left:0;">
                                                                견종명 <input type="text" class="form-control" name="ced_value" value="' . html_escape(element('ced_value', $result)) . '" />
                                                                사전 <textarea  class="form-control" style="width:300px;" name="ced_text" rows="1" >' . html_escape(element('ced_text', $result)) . '</textarea>
                                                                
                                                                <button class="btn btn-primary btn-xs" type="submit" >저장</button>
                                                                <a href="javascript:;" class="btn btn-default btn-xs" onClick="ced_cancel(\'' . element('ced_id', $result) . '\')">취소</a>
                                                            </div>';
                        $return .= form_close();
                        $return .= '</div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </li>';
                        $parent = element('ced_id', $result);
                        $return .= cmall_ca_list(element($parent, $data), $data, $nextlen);
                    }
                }
                return $return;
            }
            echo cmall_ca_list(element(0, $data), $data, 0);
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
                        <select name="ced_parent" class="form-control">
                            <option value="0">최상위견종</option>
                            <?php
                            $data = element('data', $view);
                            function cmall_ca_select($p, $data)
                            {
                                $return = '';
                                if ($p && is_array($p)) {
                                    foreach ($p as $result) {
                                        $return .= '<option value="' . html_escape(element('ced_id', $result)) . '">' . html_escape(element('ced_value', $result)) . '의 하위견종</option>';
                                        $parent = element('ced_id', $result);
                                        $return .= cmall_ca_select(element($parent, $data), $data);
                                    }
                                }
                                return $return;
                            }
                            echo cmall_ca_select(element(0, $data), $data);
                            ?>
                        </select>
                        <input type="text" name="ced_value" class="form-control" value="" placeholder="견종명 입력" />
                        <textarea class="form-control" style="width:300px;" name="ced_text" id="ced_text" rows="1" placeholder="사전 (콤마로 구분하여 입력)"><?php echo set_value('ced_text', element('ced_text', element('data', $view))); ?></textarea>
                        <!-- <input type="number" name="ced_order" class="form-control" value="0" placeholder="정렬순서" /> -->
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
            ced_value: {required :true},
            // ced_order: {required :true, numeric:true},
        }
    });
});

function ced_modify(ced_id) {
    $('.cat-cat-id-' + ced_id).hide();
    $('.mod-cat-id-' + ced_id).show();
}
function ced_cancel(ced_id) {
    $('.cat-cat-id-' + ced_id).show();
    $('.mod-cat-id-' + ced_id).hide();
}
//]]>
</script>