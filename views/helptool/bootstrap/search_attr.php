<div class="modal-header">
    <h4 class="modal-title">게시물 특성</h4>
</div>
<div class="modal-body">
    <?php
    echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
    $attributes = array('class' => 'form-horizontal', 'name' => 'fwrite', 'id' => 'fwrite');
    echo form_open(current_full_url(), $attributes);
    ?>
        
        <table class="table table-striped mt20">
            <tbody>
            <?php
                $open = false;
                $attr = element('all_attr', element('data', $view));
                $item_attr = element('attr', element('data', $view));
                if (element(0, $attr)) {
                    $i = 0;
                    foreach (element(0, $attr) as $key => $val) {
                        $display = (is_array($item_attr) && in_array(element('cat_id', $val), $item_attr)) ? "block" : 'none';
                        if ($i%3== 0) {
                            echo '<div>';
                            $open = true;
                        }
                        echo '<div class="checkbox" style="vertical-align:top;">';
                        $cat_checked = (is_array($item_attr) && in_array(element('cat_id', $val), $item_attr)) ? 'checked="checked"' : '';
                        echo '<label for="cat_id_' . element('cat_id', $val) . '"><input type="checkbox" name="cmall_attr[]" value="' . element('cat_id', $val) . '" ' . $cat_checked . ' id="cat_id_' . element('cat_id', $val) . '" onclick="display_cmall_attr(this.checked,\'catwrap_' . element('cat_id', $val) . '\');" label="' . element('cat_value', $val) . '" />' . element('cat_value', $val) . '</label> ';
                        echo get_subcat($attr, $item_attr, element('cat_id', $val), 'block');
                        echo '</div>';
                        if ($i%3== 2) {
                            echo '</div>';
                            $open = false;
                        }
                        $i++;
                    }
                    if ($open) {
                        echo '</div>';
                        $open = false;
                    }
                }
                function get_subcat($attr, $item_attr, $key, $display)
                {

                    $subcat = element($key, $attr);
                    $html = '';
                    if ($subcat) {
                        $html .= '<div class="form-group" id="catwrap_' . $key . '" style="vertical-align:margin-left:10px;top;display:' . $display . ';" >';
                        foreach ($subcat as $skey => $sval) {
                            $display = (is_array($item_attr) && in_array(element('cat_id', $sval), $item_attr)) ? 'block' : 'none';
                            $cat_checked = (is_array($item_attr) && in_array(element('cat_id', $sval), $item_attr)) ? 'checked="checked"' : '';
                            $html .= '<div class="checkbox-inline" style="vertical-align:top;margin-left:10px;">';
                            $html .= '<label for="cat_id_' . element('cat_id', $sval) . '"><input type="checkbox" name="cmall_attr[]" value="' . element('cat_id', $sval) . '" ' . $cat_checked . ' id="cat_id_' . element('cat_id', $sval) . '" onclick="display_cmall_attr(this.checked,\'catwrap_' . element('cat_id', $sval) . '\');" label="' . element('cat_value', $sval) . '" /> ' . element('cat_value', $sval) . '</label>';
                            $html .= get_subcat($attr, $item_attr, element('cat_id', $sval), $display);
                            $html .= '</div>';
                        }
                        $html .= '</div>';
                    }
                    return $html;
                }

                ?>
                <script type="text/javascript">
                //<![CDATA[
                // function display_cmall_attr(check, idname) {
                //     if (check === true) {
                //         $('#' + idname).show();
                //     } else {
                //         $('#' + idname).hide();
                //         $('#' + idname).find('input:checkbox').attr('checked', false);
                //     }
                // }
                //]]>
                </script>
            </tbody>
        </table>
        <div class="pull-right" style="margin:20px;">
            <button class="btn btn-primary" type="button" onClick="add()">검색추가하기</button>
            <button class="btn btn-default" onClick="window.close();">닫기</button>
        </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
//<![CDATA[
function add() {

    
    
    
    $("input[name='cmall_attr[]']:checked").each(function (index) {  

        var input = "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden'  name='cat_id[]' value='"+$(this).attr('label')+"'>'"+$(this).attr('label')+"'</button>";

        
        

        $(".searchwhere",opener.document).append(input);
        
    });  
    
    
    // self.close();
}
//]]>
</script>