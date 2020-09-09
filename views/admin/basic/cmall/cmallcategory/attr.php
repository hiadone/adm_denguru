<div class="box">
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir); ?>" onclick="return check_form_changed();">카테고리 관리</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/attr'); ?>" onclick="return check_form_changed();">제품특성 관리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/kind'); ?>" onclick="return check_form_changed();">견종 관리</a></li>
            
        </ul>
    </div>
    <div class="box-table">
        <button type="button" class="btn btn-success btn-sm" onClick="javascript:$('.ckd_text').toggle();">태그사전 감추기</button
        <div class="box-table">
            <?php
            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                <input type="hidden" name="is_submit" value="1" />
                <input type="hidden" name="type" value="add" />
                <div class="form-group">
                    <label class="col-sm-2 control-label">제품특성 추가</label>
                    <div class="col-sm-8 form-inline">
                        <select name="cat_parent" class="form-control">
                            <option value="0">최상위제품특성</option>
                            <?php
                            $data = element('data', $view);
                            function cmall_ca_select($p, $data,$len)
                            {
                                $return = '';
                                $nextlen = $len + 1;
                                if ($p && is_array($p)) {
                                    foreach ($p as $result) {

                                        $margin='';
                                        if ($len) {

                                            for($i=0;$len > $i;$i++)
                                                $margin .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        }

                                        $return .= '<option value="' . html_escape(element('cat_id', $result)) . '">' . $margin.html_escape(element('cat_value', $result)) . '의 하위제품특성</option>';
                                        $parent = element('cat_id', $result);
                                        $return .= cmall_ca_select(element($parent, $data), $data,$nextlen);
                                    }
                                }
                                return $return;
                            }
                            echo cmall_ca_select(element(0, $data), $data,0);
                            ?>
                        </select>
                        <input type="text" name="cat_value" class="form-control" value="" placeholder="제품특성명 입력" />
                        <textarea class="form-control" style="width:300px;" name="cat_text" id="cat_text" rows="1" placeholder="사전 (콤마로 구분하여 입력)"><?php echo set_value('cat_text', element('cat_text', element('data', $view))); ?></textarea>
                        <!-- <input type="number" name="cat_order" class="form-control" value="0" placeholder="정렬순서" /> -->
                        <button type="submit" class="btn btn-success btn-sm">추가하기</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>

        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        // echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
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


                        if ($len) {
                            $return .= '<li class="list-group-item">
                                            <div class="form-horizontal">
                                                <div class="form-group" style="margin-bottom:0;">';
                            $return .= '<div style="width:10px;float:left;margin-left:' . $margin . 'px;margin-right:10px;"><span class="fa fa-arrow-right"></span></div>';
                        } else {
                            $return .= '<li class="list-group-item" style="background-color:#e5e5e5">
                                            <div class="form-horizontal">
                                                <div class="form-group" style="margin-bottom:0;">';
                        }

                        
                        $return .= '<div class="pl10">
                            <div class="cat-cat-id-' . element('cat_id', $result) . '">
                                ' . html_escape(element('cat_value', $result)) . ' 
                                <button class="btn btn-primary btn-xs" onClick="cat_modify(\'' . element('cat_id', $result) . '\')"><span class="glyphicon glyphicon-edit"></span></button>';
                        if ( ! element(element('cat_id', $result), $data)) {
                            $return .= '                    <button class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url = "' . admin_url('cmall/cmallcategory/attr_delete/' . element('cat_id', $result)) . '"><span class="glyphicon glyphicon-trash"></span></button>';
                        }
                        $return .= '<button class="ckd_text" style="display:none;">'.html_escape(element('cat_text', $result)).'</button>';
                        $return .= '    </div><div class="form-inline mod-cat-id-' . element('cat_id', $result) . '" style="display:none;">';
                        $return .= form_open(current_full_url(), $attributes);
                        $return .= '<input type="hidden" name="cat_id"  value="' . element('cat_id', $result) . '" />
                                                            <input type="hidden" name="type" value="modify" />
                                                            <div class="form-group" style="margin-left:0;">
                                                                제품특성명 <input type="text" class="form-control" name="cat_value" value="' . html_escape(element('cat_value', $result)) . '" />
                                                                사전 <textarea  class="form-control" style="width:300px;" name="cat_text" rows="1" >' . html_escape(element('cat_text', $result)) . '</textarea>
                                                                
                                                                <button class="btn btn-primary btn-xs" type="submit" >저장</button>
                                                                <a href="javascript:;" class="btn btn-default btn-xs" onClick="cat_cancel(\'' . element('cat_id', $result) . '\')">취소</a>
                                                            </div>';
                        $return .= form_close();
                        $return .= '</div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </li>';
                        $parent = element('cat_id', $result);
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
                    <label class="col-sm-2 control-label">제품특성 추가</label>
                    <div class="col-sm-8 form-inline">
                        <select name="cat_parent" class="form-control">
                            <option value="0">최상위제품특성</option>
                            <?php
                            $data = element('data', $view);
                            
                            echo cmall_ca_select(element(0, $data), $data,0);
                            ?>
                        </select>
                        <input type="text" name="cat_value" class="form-control" value="" placeholder="제품특성명 입력" />
                        <textarea class="form-control" style="width:300px;" name="cat_text" id="cat_text" rows="1" placeholder="사전 (콤마로 구분하여 입력)"><?php echo set_value('cat_text', element('cat_text', element('data', $view))); ?></textarea>
                        <!-- <input type="number" name="cat_order" class="form-control" value="0" placeholder="정렬순서" /> -->
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
            cat_value: {required :true},
            // cat_order: {required :true, numeric:true},
        }
    });
});

function cat_modify(cat_id) {
    $('.cat-cat-id-' + cat_id).hide();
    $('.mod-cat-id-' + cat_id).show();
}
function cat_cancel(cat_id) {
    $('.cat-cat-id-' + cat_id).show();
    $('.mod-cat-id-' + cat_id).hide();
}
//]]>
</script>
