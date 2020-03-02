<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab1" aria-expanded="true" aria-controls="cmalltab1">분류</a></h4>
				<a data-toggle="collapse" href="#cmalltab1" aria-expanded="true" aria-controls="cmalltab1"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab1">
				<div class="form-group">
					<label class="col-sm-2 control-label">분류</label>
					<div class="col-sm-10">
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
					</div>
				</div>
			</div>
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2">기본정보</a></h4>
				<a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab2">
				<div class="form-group">
					<label class="col-sm-2 control-label">내부상품페이지주소</label>
					<div class="col-sm-10 form-inline">
						<?php echo cmall_item_url(); ?> <input type="text" class="form-control" name="cit_key" value="<?php echo set_value('cit_key', element('cit_key', element('data', $view))); ?>" /> 페이지주소를 입력해주세요
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">실제상품페이지주소</label>
					<div class="col-sm-10 ">
						<input type="text" class="form-control" name="cit_post_url" value="<?php echo set_value('cit_post_url', element('cit_post_url', element('data', $view))); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">상품명</label>
					<div class="col-sm-10 form-inline">
						<input type="text" class="form-control" name="cit_name" value="<?php echo set_value('cit_name', element('cit_name', element('data', $view))); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">판매가격</label>
					<div class="col-sm-10 form-inline">
						<input type="number" class="form-control" name="cit_price" value="<?php echo set_value('cit_price', element('cit_price', element('data', $view))); ?>" /> 원
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">브랜드</label>
					<div class="col-sm-10 form-inline">
						<input type="text" class="form-control" id="cit_brand_text" name="cit_brand_text" value="<?php echo set_value('cit_brand_text', element('cit_brand_text', element('data', $view))); ?>" /> 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">정렬순서</label>
					<div class="col-sm-10 form-inline">
						<input type="number" class="form-control" name="cit_order" value="<?php echo set_value('cit_order', element('cit_order', element('data', $view))); ?>" />
						<div class="help-inline">정렬순서가 낮은 상품이 먼저 나옵니다</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">쇼핑몰명</label>
					<?php if (element('boardlist', element('data', $view))) { ?>
					<div class="pull-left">
						<select name="brd_id" class="form-control" onChange="get_post_list(this.value);">
							<option value="선택">선택</option>
							<?php foreach (element('boardlist', element('data', $view)) as $key => $value) { ?>
								<option value="<?php echo element('brd_id', $value); ?>" <?php echo set_select('brd_id', element('brd_id', $value), (element('brd_id', element('data', $view)) === element('brd_id', $value) ? true : false)); ?>><?php echo html_escape(element('brd_name', $value)); ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">게시글명</label>
					
					<div class="pull-left">
						<select name="post_id" class="form-control" id="post_id">
							<?php if (element('list',element('postlist', element('data', $view)))) { ?>
								<?php foreach (element('list',element('postlist', element('data', $view))) as $key => $value) { 

									?>
									<option value="<?php echo element('post_id', $value); ?>" <?php echo set_select('post_id', element('post_id', $value), (element('post_id', element('data', $view)) === element('post_id', $value) ? true : false)); ?>><?php echo html_escape(element('post_title', $value)); ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
					
				</div>
			</div>
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab3" aria-expanded="true" aria-controls="cmalltab3">세부정보</a></h4>
				<a data-toggle="collapse" href="#cmalltab3" aria-expanded="true" aria-controls="cmalltab3"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab3">
				
				<div class="form-group">
					<label class="col-sm-2 control-label">기본설명</label>
					<div class="col-sm-10">
						<textarea class="form-control" name="cit_summary" id="cit_summary" rows="3"><?php echo set_value('cit_summary', element('cit_summary', element('data', $view))); ?></textarea>
						<div class="help-block">요약설명을 입력해주세요</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">기본정보</label>
					<div class="col-sm-10">
						<?php for ($k= 1; $k<= 10; $k++) { ?>
							<div class="form-group form-inline">
								기본정보 <?php echo $k; ?> 제목 <input type="text" class="form-control" name="info_title_<?php echo $k; ?>" value="<?php echo set_value('info_title_' . $k, element('info_title_' . $k, element('data', $view))); ?>" />
								기본정보 <?php echo $k; ?> 값 <input type="text" class="form-control" name="info_content_<?php echo $k; ?>" value="<?php echo set_value('info_content_' . $k, element('info_content_' . $k, element('data', $view))); ?>" />
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">사용자데모</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="demo_user_link" value="<?php echo set_value('demo_user_link', element('demo_user_link', element('data', $view))); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">관리자데모</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="demo_admin_link" value="<?php echo set_value('demo_admin_link', element('demo_admin_link', element('data', $view))); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">상품유형</label>
					<div class="col-sm-10">
						<label for="cit_type1" class="checkbox-inline">
							<input type="checkbox" name="cit_type1" id="cit_type1" value="1" <?php echo set_checkbox('cit_type1', '1', (element('cit_type1', element('data', $view)) ? true : false)); ?> /> 추천
						</label>
						<label for="cit_type2" class="checkbox-inline">
							<input type="checkbox" name="cit_type2" id="cit_type2" value="1" <?php echo set_checkbox('cit_type2', '1', (element('cit_type2', element('data', $view)) ? true : false)); ?> /> 인기
						</label>
						<label for="cit_type3" class="checkbox-inline">
							<input type="checkbox" name="cit_type3" id="cit_type3" value="1" <?php echo set_checkbox('cit_type3', '1', (element('cit_type3', element('data', $view)) ? true : false)); ?> /> 신상품
						</label>
						<label for="cit_type4" class="checkbox-inline">
							<input type="checkbox" name="cit_type4" id="cit_type4" value="1" <?php echo set_checkbox('cit_type4', '1', (element('cit_type4', element('data', $view)) ? true : false)); ?> /> 할인
						</label>
						<div class="help-inline" >체크하시면, 메인페이지에 각 분류에 출력됩니다</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">판매가능</label>
					<div class="col-sm-10">
						<label for="cit_status" class="checkbox-inline">
							<input type="checkbox" name="cit_status" id="cit_status" value="1" <?php echo set_checkbox('cit_status', '1', (element('cit_status', element('data', $view)) ? true : false)); ?> /> 판매합니다
						</label>
						<div class="help-inline" >체크를 해제하시면 상품 리스트에서 사라지며, 구매할 수 없습니다. </div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">sold out</label>
					<div class="col-sm-10">
						<label for="cit_is_soldout" class="checkbox-inline">
							<input type="checkbox" name="cit_is_soldout" id="cit_is_soldout" value="1" <?php echo set_checkbox('cit_is_soldout', '1', (element('cit_is_soldout', element('data', $view)) ? true : false)); ?> /> Sold out
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Vision API label</label>
					<div class="col-sm-3">
						<textarea class="form-control" name="val_tag" id="val_tag" rows="5"><?php echo set_value('val_tag', element('val_tag', element('data', $view))); ?></textarea>
						<div class="help-block">이미지 분석 라벨입니다(수정 불가)</div>
					</div>
					<label class="col-sm-1 control-label">태그</label>
					<div class="col-sm-3">
						<textarea class="form-control" name="cta_tag" id="cta_tag" rows="5"><?php echo set_value('cta_tag', element('cta_tag', element('data', $view))); ?></textarea>
						<div class="help-block">태그 입력(엔터로 구분하여 입력)</div>
					</div>
					<div class="form-group">
					<label class="col-sm-1 control-label">색상</label>
					<div class="col-sm-2">
						<textarea class="form-control" name="cit_color" id="cit_color" rows="3"><?php echo set_value('cit_color', element('cit_color', element('data', $view))); ?></textarea>
						<div class="help-block">태그 입력(엔터로 구분하여 입력)</div>
					</div>
				</div>
				</div>
				
				
				<!-- <div class="form-group">
					<label class="col-sm-2 control-label">다운로드 가능기간</label>
					<div class="col-sm-10 form-inline">
						<input type="number" class="form-control" name="cit_download_days" value="<?php echo set_value('cit_download_days', (int) element('cit_download_days', element('data', $view))); ?>" />일
						<div class="help-inline" >해당기간동안 계속 다운로드 받을 수 있습니다. 0 이면 기간제한 없이 한번 결제후 언제든지 다운로드가 가능합니다</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">판매자 회원아이디</label>
					<div class="col-sm-10 form-inline">
						<input type="text" class="form-control" name="seller_mem_userid" value="<?php echo set_value('seller_mem_userid', element('seller_mem_userid', element('data', $view))); ?>" />
					</div>
				</div> -->
			</div>
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab4" aria-expanded="true" aria-controls="cmalltab4">상품내용</a></h4>
				<a data-toggle="collapse" href="#cmalltab4" aria-expanded="true" aria-controls="cmalltab4"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab4">
				<div class="form-group">
					<label class="col-sm-2 control-label">내용</label>
					<div class="col-sm-10">
						<?php echo display_dhtml_editor('cit_content', set_value('cit_content', element('cit_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_cmall_product_dhtml'), $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">모바일내용</label>
					<div class="col-sm-10">
						<?php echo display_dhtml_editor('cit_mobile_content', set_value('cit_mobile_content', element('cit_mobile_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_cmall_product_dhtml'), $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
						모바일 내용이 일반웹페이지 내용과 다를 경우에 입력합니다. 같은 경우는 입력하지 않으셔도 됩니다
					</div>
				</div>
			</div>
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab5" aria-expanded="true" aria-controls="cmalltab5">상품옵션</a></h4>
				<a data-toggle="collapse" href="#cmalltab5" aria-expanded="true" aria-controls="cmalltab5"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab5">
				<div class="table-responsive">
					<table class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th>옵션명 <a href="javascript:;" class="btn btn-xs btn-danger" onClick="add_option();">옵션추가</a></th>
								<th>첨부파일</th>
								<th>추가금액</th>
								<th>사용여부</th>
							</tr>
						</thead>
						<tbody id="item_option_wrap">
						<?php
						if (element('item_detail', element('data', $view))) {
							foreach (element('item_detail', element('data', $view)) as $detail) {
						?>
							<tr>
								<td><input type="text" class="form-control" name="cde_title_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="<?php echo html_escape(element('cde_title', $detail)); ?>" /></td>
								<td class="form-inline">
									<input type="file" class="form-control" name="cde_file_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" />
									<?php if (element('cde_filename', $detail)) { ?>
										<a href="<?php echo admin_url('cmall/itemdownload/download/' . element('cde_id', $detail)); ?>" class="ct_file_name"><?php echo html_escape(element('cde_originname', $detail));?></a>
									<?php } ?>
								</td>
								<td><input type="number" class="form-control" name="cde_price_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="<?php echo (int) element('cde_price', $detail); ?>" />원</td>
								<td><input type="checkbox" name="cde_status_update[<?php echo html_escape(element('cde_id', $detail)); ?>]" value="1" <?php echo (element('cde_status', $detail)) ? ' checked="checked" ' : ''; ?> /></td>
							</tr>
						<?php
							}
						}
						?>
						</tbody>
					</table>
				</div>
				<script type="text/javascript">
				//<![CDATA[
				function add_option() {
					$('#item_option_wrap').append('<tr><td><input type="text" class="form-control" name="cde_title[]" value="" /></td><td class="form-inline"><input type="file" class="form-control" name="cde_file[]" /></td><td><input type="number" class="form-control" name="cde_price[]" value="0" />원</td><td><input type="checkbox" name="cde_status[]" value="1" checked="checked" /></td></tr>');
				}
				//]]>
				</script>
			</div>
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab6" aria-expanded="true" aria-controls="cmalltab6">이미지</a></h4>
				<a data-toggle="collapse" href="#cmalltab6" aria-expanded="true" aria-controls="cmalltab6"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab6">
			<?php for ($k = 1; $k<= 10; $k++) { ?>
				<div class="form-group">
					<label class="col-sm-2 control-label">이미지 <?php echo $k; ?></label>
					<div class="col-sm-10 form-inline">
					<?php
					if (element('cit_file_' . $k, element('data', $view))) {
					?>
						<img src="<?php echo thumb_url('cmallitem', element('cit_file_' . $k, element('data', $view)), 80); ?>" alt="<?php echo isset($detail) ? html_escape(element('cde_title', $detail)) : ''; ?>" title="<?php echo isset($detail) ? html_escape(element('cde_title', $detail)) : ''; ?>" />
						<label for="cit_file_<?php echo $k; ?>_del">
							<input type="checkbox" name="cit_file_<?php echo $k; ?>_del" id="cit_file_<?php echo $k; ?>_del" value="1" <?php echo set_checkbox('cit_file_' . $k . '_del', '1'); ?> /> 삭제
						</label>
					<?php
					}
					?>
						<input type="file" name="cit_file_<?php echo $k; ?>" id="cit_file_<?php echo $k; ?>" />
					</div>
				</div>
			<?php } ?>
			</div>
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab4" aria-expanded="true" aria-controls="cmalltab4">상/하단 내용</a></h4>
				<a data-toggle="collapse" href="#cmalltab4" aria-expanded="true" aria-controls="cmalltab4"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab4">
				<div class="form-group">
					<label class="col-sm-2 control-label">일반 상단 내용</label>
					<div class="col-sm-10">
						<?php echo display_dhtml_editor('header_content', set_value('header_content', element('header_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = true, $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">일반 하단 내용</label>
					<div class="col-sm-10">
						<?php echo display_dhtml_editor('footer_content', set_value('footer_content', element('footer_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = true, $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">모바일 상단 내용</label>
					<div class="col-sm-10">
						<?php echo display_dhtml_editor('mobile_header_content', set_value('mobile_header_content', element('mobile_header_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = true, $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">모바일 하단 내용</label>
					<div class="col-sm-10">
						<?php echo display_dhtml_editor('mobile_footer_content', set_value('mobile_footer_content', element('mobile_footer_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = true, $editor_type = $this->cbconfig->item('cmall_product_editor_type')); ?>
					</div>
				</div>
			</div>
			<div class="btn-group pull-right" role="group" aria-label="...">
				<button type="button" class="btn btn-default btn-sm btn-history-back" >목록으로</button>
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>
<!-- CSS , JS -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[
jQuery(function($) {
	$('#fadminwrite').validate({
		rules: {
			cit_key: {required:true, minlength:3, maxlength:50, alpha_dash : true},
			cit_name: 'required',
			cit_order: 'required',
			cit_price: { required:true, number:true },
			cit_content : {<?php echo ($this->cbconfig->item('use_cmall_product_dhtml')) ? 'valid_' . $this->cbconfig->item('cmall_product_editor_type') : ''; ?> : true },
			cit_mobile_content : {<?php echo ($this->cbconfig->item('use_cmall_product_dhtml')) ? 'valid_' . $this->cbconfig->item('cmall_product_editor_type') : ''; ?> : true },
			header_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true },
			footer_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true },
			mobile_header_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true },
			mobile_footer_content : { valid_<?php echo $this->cbconfig->item('cmall_product_editor_type'); ?> : true }
		},
		submitHandler: function(form) {

			// 옵션 입력 체크
			var option_count = 0;
			var option_file  = 0;
			var io_price	 = 0;
			var max_io_price = 0;
			var is_price_chk = false;

			$("input[name^=cde_title]").each(function(index) {
				if($.trim($(this).val()).length > 0) {
					option_count++;
					is_price_chk = false;

					if(!form.cit_id.value) {
						if($.trim($("input[name^=cde_file]").eq(index).val()).length > 0) {
							option_file++;
							is_price_chk = true;
						}
					} else {
						if($(".ct_file_name").eq(index).length > 0) {
							option_file++;
							is_price_chk = true;
						} else {
							if($.trim($("input[name^=cde_file]").eq(index).val()).length > 0) {
								option_file++;
								is_price_chk = true;
							}
						}
					}

					if(is_price_chk) {
						io_price = parseInt($.trim($("input[name^=cde_price_update]").eq(index).val()));
						if(max_io_price < io_price)
							max_io_price = io_price;
					}
				}
			});

			// if(option_count == 0) {
			// 	alert("상품옵션을 하나이상 입력해 주십시오.");
			// 	$("#cmalltab5").focus();
			// 	return false;
			// }

			// if(option_count > 0 && (option_file == 0 || option_count > option_file)) {
			// 	alert("입력하신 상품옵션의 파일을 업로드해 주십시오.");
			// 	$("#cmalltab5").focus();
			// 	return false;
			// }

			form.submit();
		}
	});
});


function get_post_list(brd_id){
	$.ajax({
		url : cb_url + '/postact/get_post_list/'+brd_id,
		type : 'get',
		dataType : 'json',
		success : function(data) {
			$('#post_id').empty();
			for (i = 0; i < data.length; i++){
				var option = $("<option value="+data[i].post_id+">"+data[i].post_title+"</option>");
                $('#post_id').append(option);
				
			}	
		}
			
	});
}

function split( val ) {

    return val.split( /,\s*/ );

}

function extractLast( term ) {

    return split( term ).pop();

}




    
var searchSource = [
	<?php
	if (element('brand_list', element('data', $view))) {
	    foreach (element('brand_list', element('data', $view)) as $result) {
	 		echo '"'.element('cbr_value_kr',$result).'","'.element('cbr_value_en',$result).'",';
		}
	}
	        
	?>
	"========" 
]; // 배열 형태로 
$("#cit_brand_text")
.on("keydown", function( event ) {
    if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
        event.preventDefault();
    }
})
.autocomplete({  //오토 컴플릿트 시작
    source : searchSource,    // source 는 자동 완성 대상
    select: function(event, ui) {
        this.value = "";
        this.value = ui.item.value;

        return false;
    },
    focus : function(event, ui) {    //포커스 가면
        return false;//한글 에러 잡기용도로 사용됨
    },
    minLength: 1,// 최소 글자수
    autoFocus: true, //첫번째 항목 자동 포커스 기본값 false
    classes: {    //잘 모르겠음
        "ui-autocomplete": "highlight"
    },
    delay: 100,    //검색창에 글자 써지고 나서 autocomplete 창 뜰 때 까지 딜레이 시간(ms)
//            disabled: true, //자동완성 기능 끄기
    position: { my : "right top", at: "right bottom" },    //잘 모르겠음
    close : function(event){    //자동완성창 닫아질때 호출
        console.log(1);
    }
});
        
    

//]]>
</script>
			



