<div class="modal-header">
	<h4 class="modal-title">게시물 카테고리 변경</h4>
</div>
<div class="modal-body">
	<?php
	echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
	$attributes = array('class' => 'form-horizontal', 'name' => 'fwrite', 'id' => 'fwrite');
	echo form_open(current_full_url(), $attributes);
	?>
		<input type="hidden" name="is_submit" value="1" />
		<input type="hidden" name="post_id_list" value="<?php echo element('post_id_list', $view); ?>" />
		<table class="table table-striped mt20">
			<tbody>
			<?php
				$open = false;
				$category = element('all_category', element('data', $view));
				$item_category = element('category', element('data', $view));
				if (element(0, $category)) {
					$i = 0;
					foreach (element(0, $category) as $key => $val) {
						$display = (is_array($item_category) && in_array(element('cca_id', $val), $item_category)) ? "block" : 'none';
						if ($i%3== 0) {
							echo '<div>';
							$open = true;
						}
						echo '<div class="checkbox" style="vertical-align:top;">';
						$cat_checked = (is_array($item_category) && in_array(element('cca_id', $val), $item_category)) ? 'checked="checked"' : '';
						echo '<label for="cca_id_' . element('cca_id', $val) . '"><input type="checkbox" name="cmall_category[]" value="' . element('cca_id', $val) . '" ' . $cat_checked . ' id="cca_id_' . element('cca_id', $val) . '" onclick="display_cmall_category(this.checked,\'catwrap_' . element('cca_id', $val) . '\');" />' . element('cca_value', $val) . '</label> ';
						echo get_subcat($category, $item_category, element('cca_id', $val), $display);
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
				function get_subcat($category, $item_category, $key, $display)
				{

					$subcat = element($key, $category);
					$html = '';
					if ($subcat) {
						$html .= '<div class="form-group" id="catwrap_' . $key . '" style="vertical-align:margin-left:10px;top;display:' . $display . ';" >';
						foreach ($subcat as $skey => $sval) {
							$display = (is_array($item_category) && in_array(element('cca_id', $sval), $item_category)) ? 'block' : 'none';
							$cat_checked = (is_array($item_category) && in_array(element('cca_id', $sval), $item_category)) ? 'checked="checked"' : '';
							$html .= '<div class="checkbox-inline" style="vertical-align:top;margin-left:10px;">';
							$html .= '<label for="cca_id_' . element('cca_id', $sval) . '"><input type="checkbox" name="cmall_category[]" value="' . element('cca_id', $sval) . '" ' . $cat_checked . ' id="cca_id_' . element('cca_id', $sval) . '" onclick="display_cmall_category(this.checked,\'catwrap_' . element('cca_id', $sval) . '\');" /> ' . element('cca_value', $sval) . '</label>';
							$html .= get_subcat($category, $item_category, element('cca_id', $sval), $display);
							$html .= '</div>';
						}
						$html .= '</div>';
					}
					return $html;
				}

				?>
				<script type="text/javascript">
				//<![CDATA[
				function display_cmall_category(check, idname) {
					if (check === true) {
						$('#' + idname).show();
					} else {
						$('#' + idname).hide();
						$('#' + idname).find('input:checkbox').attr('checked', false);
					}
				}
				//]]>
				</script>
			</tbody>
		</table>
		<div class="pull-right" style="margin:20px;">
			<button class="btn btn-primary" type="submit">변경하기</button>
			<button class="btn btn-default" onClick="window.close();">닫기</button>
		</div>
	<?php echo form_close(); ?>
</div>
