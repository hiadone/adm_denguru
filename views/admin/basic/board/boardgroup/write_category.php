<div class="box">
    <?php
    if (element('bgr_id', element('data', $view))) {
    ?>
        <div class="box-header">
            <?php if (element('grouplist', $view)) { ?>
                <div class="pull-right">
                    <select name="bgr_id" class="form-control" onChange="location.href='<?php echo admin_url($this->pagedir . '/write_category'); ?>/' + this.value;">
                        <?php foreach (element('grouplist', $view) as $key => $value) { ?>
                            <option value="<?php echo element('bgr_id', $value); ?>" <?php echo set_select('bgr_id', element('bgr_id', $value), ((string) element('bgr_id', element('data', $view)) === element('bgr_id', $value) ? true : false)); ?>><?php echo html_escape(element('bgr_name', $value)); ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            <ul class="nav nav-tabs">
                <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write/' . element('bgr_id', element('data', $view))); ?>">기본정보</a></li>
                <li role="presentation" ><a href="<?php echo admin_url($this->pagedir . '/write_admin/' . element('bgr_id', element('data', $view))); ?>">그룹관리자</a></li>
                <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/write_category/' . element('bgr_id', element('data', $view))); ?>" >그룹카테고리</a></li>
            </ul>
        </div>
    <?php
    }
    ?>
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        ?>        
        <ul class="list-group">
            <?php
            $data = element('data', $view);
            function ca_list($p, $data)
            {
                $return = '';
                if ($p && is_array($p)) {
                    foreach ($p as $result) {
                        $exp = explode('.', element('bca_key', $result));
                        $len = (element(1, $exp)) ? strlen(element(1, $exp)) : 0;
                        $margin = $len * 20;
                        $attributes = array('class' => 'form-inline', 'name' => 'fcategory');
                        $return .= '<li class="list-group-item">
                                            <div class="form-horizontal">
                                                <div class="form-group" style="margin-bottom:0;">';
                        if ($len) {
                            $return .= '<div style="width:10px;float:left;margin-left:' . $margin . 'px;margin-right:10px;"><span class="fa fa-arrow-right"></span></div>';
                        }
                        $return .= '<div class="pl10">
                            <div class="cat-bca-id-' . element('bca_id', $result) . '">
                                ' . html_escape(element('bca_value', $result)) . ' (' . html_escape(element('bca_order', $result)) . ')
                                <button class="btn btn-primary btn-xs" onClick="cat_modify(\'' . element('bca_id', $result) . '\')"><span class="glyphicon glyphicon-edit"></span></button>';
                        if ( ! element(element('bca_key', $result), $data)) {
                            $return .= '<button class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url = "' . admin_url('board/boardgroup/write_category_delete/' . element('bgr_id', $data) . '/' . element('bca_id', $result)) . '"><span class="glyphicon glyphicon-trash"></span></button>';
                        }
                        $return .= '                </div>
                                                        <div class="form-inline mod-bca-id-' . element('bca_id', $result) . '" style="display:none;">';
                        $return .= form_open(current_full_url(), $attributes);
                        $return .= '
                                                            <input type="hidden" name="bgr_id"  value="' . element('bgr_id', $data) . '" />
                                                            <input type="hidden" name="bca_id"  value="' . element('bca_id', $result) . '" />
                                                            <input type="hidden" name="type" value="modify" />
                                                            <div class="form-group" style="margin-left:0;">
                                                                카테고리명 <input type="text" class="form-control" name="bca_value" value="' . html_escape(element('bca_value', $result)) . '" />
                                                                정렬순서 <input type="number" class="form-control" name="bca_order" value="' . html_escape(element('bca_order', $result)) . '"/>
                                                                <button class="btn btn-primary btn-xs" type="submit" >저장</button>
                                                                <a href="javascript:;" class="btn btn-default btn-xs" onClick="cat_cancel(\'' . element('bca_id', $result) . '\')">취소</a>
                                                            </div>';
                        $return .= form_close();
                        $return .= '                </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </li>';
                        $parent = element('bca_key', $result);
                        $return .= ca_list(element($parent, $data), $data);
                    }
                }
                return $return;
            }
            echo ca_list(element(0, $data), $data);
            ?>
        </ul>
    </div>
    <div>
        <div class="box-table">
            <?php
            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                <input type="hidden" name="is_submit" value="1" />
                <input type="hidden" name="bgr_id"  value="<?php echo element('bgr_id', element('data', $view)); ?>" />
                <input type="hidden" name="type" value="add" />
                <div class="form-group">
                    <label class="col-sm-2 control-label">카테고리 추가</label>
                    <div class="col-sm-8 form-inline">
                        <select name="bca_parent" class="form-control">
                            <option value="0">최상위카테고리</option>
                            <?php
                            $data = element('data', $view);
                            function ca_select($p, $data)
                            {
                                $return = '';
                                if ($p && is_array($p)) {
                                    foreach ($p as $result) {
                                        $return .= '<option value="' . html_escape(element('bca_key', $result)) . '">' . html_escape(element('bca_value', $result)) . '의 하위카테고리</option>';
                                        $parent = element('bca_key', $result);
                                        $return .= ca_select(element($parent, $data), $data);
                                    }
                                }
                                return $return;
                            }
                            echo ca_select(element(0, $data), $data);
                            ?>
                        </select>
                        <input type="text" name="bca_value" class="form-control" value="" placeholder="카테고리명 입력" />
                        <input type="number" name="bca_order" class="form-control" value="" placeholder="정렬순서" />
                        <button type="submit" class="btn btn-success btn-sm">추가하기</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
function cat_modify(bca_id) {
    $('.cat-bca-id-' + bca_id).hide();
    $('.mod-bca-id-' + bca_id).show();
}
function cat_cancel(bca_id) {
    $('.cat-bca-id-' + bca_id).show();
    $('.mod-bca-id-' + bca_id).hide();
}
//]]>
</script>
