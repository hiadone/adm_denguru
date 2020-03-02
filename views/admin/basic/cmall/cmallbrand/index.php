<div class="box">
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
					$attributes = array('class' => 'form-inline', 'name' => 'fbrand');
					$return .= '<li class="list-group-item">
										<div class="form-horizontal">
											<div class="form-group" style="margin-bottom:0;">';
					
					$return .= '<div class="pl10">
						<div class="cat-cbr-id-' . element('cbr_id', $result) . '">
							' . html_escape(element('cbr_value_kr', $result)) . ' (' . html_escape(element('cbr_value_en', $result)) . ')
							<button class="btn btn-primary btn-xs" onClick="cat_modify(\'' . element('cbr_id', $result) . '\')"><span class="glyphicon glyphicon-edit"></span></button>';
					
					$return .= '					<button class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url = "' . admin_url('cmall/cmallbrand/delete/' . element('cbr_id', $result)) . '"><span class="glyphicon glyphicon-trash"></span></button>';
					
					$return .= '	</div><div class="form-inline mod-cbr-id-' . element('cbr_id', $result) . '" style="display:none;">';
					$return .= form_open(current_full_url(), $attributes);
					$return .= '<input type="hidden" name="cbr_id"	value="' . element('cbr_id', $result) . '" />
														<input type="hidden" name="type" value="modify" />
														<div class="form-group" style="margin-left:0;">
															한글 브랜드명 <input type="text" class="form-control" name="cbr_value_kr" value="' . html_escape(element('cbr_value_kr', $result)) . '" />
															영문 브랜드명 <input type="text" class="form-control" name="cbr_value_en" value="' . html_escape(element('cbr_value_en', $result)) . '"/>
															<button class="btn btn-primary btn-xs" type="submit" >저장</button>
															<a href="javascript:;" class="btn btn-default btn-xs" onClick="cat_cancel(\'' . element('cbr_id', $result) . '\')">취소</a>
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
					<label class="col-sm-2 control-label">브랜드 추가</label>
					<div class="col-sm-8 form-inline">
						
						<input type="text" name="cbr_value_kr" class="form-control" value="" placeholder="한글 브랜드명 입력" />
						<input type="text" name="cbr_value_en" class="form-control" value="" placeholder="영문 브랜드명 입력" />
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
	$('#fadminwrite', 'input[name=fbrand]').validate({
		rules: {
			cbr_value_kr: {required :true},
			cbr_value_en: {required :true},
		}
	});
});

function cat_modify(cbr_id) {
	$('.cat-cbr-id-' + cbr_id).hide();
	$('.mod-cbr-id-' + cbr_id).show();
}
function cat_cancel(cbr_id) {
	$('.cat-cbr-id-' + cbr_id).show();
	$('.mod-cbr-id-' + cbr_id).hide();
}
//]]>
</script>
