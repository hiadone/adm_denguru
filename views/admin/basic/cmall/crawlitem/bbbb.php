<div class="box">
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir); ?>" onclick="return check_form_changed();">크롤링 리스트</a></li>
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir . '/aaaa'); ?>" onclick="return check_form_changed();">스토어별 리스트 비교</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/bbbb'); ?>" onclick="return check_form_changed();">스토어별 상품 히스토리</a></li>
            
        </ul>
    </div>
    <div class="box-table">
       
        <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>num</th>
                            <th>스토어 ID</th>
                            <th>스토어명</th>
                            <th>최근 상품 숫자</th>                            
                            <th>최근 상품 삭제 숫자</th>
                            <th>평균 상품 숫자</th>                            
                            <th>평균 상품 삭제 숫자</th>
                            
                            
                            <th>비교</th>
                            <th>action</th>
                            <th><input type="checkbox" name="chkall" id="chkall" /></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (element('list', element('data', $view))) {
                        foreach (element('list', element('data', $view)) as $result) {

                    ?>
                        <tr class="<?php if(((element('cit_count', $result) - element('cit_is_del_count', $result)) < element('cit_count', $result) * 0.7 || (element('cit_count_avg', $result) - element('cit_count', $result)) > element('cit_count_avg', $result) * 0.7 || (element('cit_is_del_count', $result) - element('cit_is_del_count_avg', $result)) > element('cit_is_del_count_avg', $result) * 1.7)) echo 'warning';
                            if((element('cit_count', $result) - element('cit_is_del_count', $result)) < element('cit_count', $result) * 0.2) echo ' danger';
                        ?> ">
                            <td><?php echo element('num', $result); ?></td>
                            <td><?php echo element('brd_id', $result); ?></td>
                            <td><a href="<?php echo board_url(element('brd_key', $result)); ?>"><?php echo element('brd_name', $result); ?></a></td>
                            <td><?php echo number_format(element('cit_count', $result)); ?></td>
                            <td><?php echo number_format(element('cit_is_del_count', $result)); ?></td>
                            <td><?php echo number_format(element('cit_count_avg', $result),2); ?></td>
                            <td><?php echo number_format(element('cit_is_del_count_avg', $result),2); ?></td>                          
                            <!-- <td><?php echo element('a_cnt', $result); ?></td>                          
                            <td><?php echo element('b_cnt', $result); ?></td>                           -->
                            
                            
                            <td></td>
                            <td><a href="<?php echo admin_url($this->pagedir.'/bbbbdelete/'.element('brd_id', $result)); ?>" class="btn btn-danger btn-xs">history 삭제</a></td>
                            <td></td>
                            
                        </tr>
                    <?php
                        }
                    }
                    if ( ! element('list', element('data', $view))) {
                    ?>
                        <tr>
                            <td colspan="14" class="nopost">자료가 없습니다</td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
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
