<div class="box">
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir); ?>" onclick="return check_form_changed();">크롤링 리스트</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/aaaa'); ?>" onclick="return check_form_changed();">스토어별 리스트 비교</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/bbbb'); ?>" onclick="return check_form_changed();">스토어별 상품 히스토리</a></li>
            
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
                            <th>상품 숫자</th>                            
                            <th>상품 상세 숫자</th>
                            <th>상품 이미지 숫자</th>
                            <th>상품 텍스트 숫자</th>
                            
                            <th>warning_count</th>
                            <th>비교 </th>
                            
                            <!-- <th>action</th>
                            <th><input type="checkbox" name="chkall" id="chkall" /></th> -->
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (element('list', element('data', $view))) {
                        foreach (element('list', element('data', $view)) as $result) {
                    ?>
                        <tr class="<?php echo ((element('d_cnt', $result) * 0.8) > element('d_file_cnt', $result) || (element('cnt', $result) * 0.1) < element('w_cnt', $result)) ? 'warning':''; ?> ">
                            <td><?php echo element('num', $result); ?></td>
                            <td><?php echo element('brd_id', $result); ?></td>
                            <td><a href="<?php echo admin_url($this->pagedir.'?sfield=brd_id2&skeyword='.element('brd_id', $result)); ?>"><?php echo element('brd_name', $result); ?></a></td>
                            <td><?php echo element('cnt', $result); ?></td>
                            <td><?php echo element('d_cnt', $result); ?></td>
                            <td><?php echo element('d_file_cnt', $result); ?></td>                          
                            <td><?php echo element('d_content_cnt', $result); ?></td>                          
                            <!-- <td><?php echo element('a_cnt', $result); ?></td>                          
                            <td><?php echo element('b_cnt', $result); ?></td>                           -->
                            <td><?php echo element('w_cnt', $result) ?></td>
                            <td><?php echo (element('cnt', $result) - element('d_cnt', $result)); ?></td>
                            
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
