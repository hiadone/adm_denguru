<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		echo show_alert_message(element('message', $view), '<div class="alert alert-warning">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
			<div class="form-group">
				<label class="col-sm-2 control-label">회원아이디</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control" name="mem_userid" value="<?php echo set_value('mem_userid', element('mem_userid', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 네임</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="pet_name" value="<?php echo set_value('pet_name', element('pet_name', element('data', $view))); ?>" />
				</div>
			</div>
		
			<div class="form-group">
				<label class="col-sm-2 control-label">성별</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input type="radio" name="pet_sex" value="1" <?php echo set_radio('pet_sex', '1', (element('pet_sex', element('data', $view)) === '1' ? true : false)); ?> /> 남자
						<input type="radio" name="pet_sex" value="2" <?php echo set_radio('pet_sex', '2', (element('pet_sex', element('data', $view)) === '2' ? true : false)); ?> /> 여자						
					</div>
					<input type="checkbox" name="pet_neutral" value="1" <?php echo set_checkbox('pet_neutral', '1', (element('pet_neutral', element('data', $view)) ? true : false)); ?> /> 중성
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 생일</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="pet_birthday" value="<?php echo set_value('pet_birthday', element('pet_birthday', element('data', $view))); ?>" />
					<p class="help-block">YYYY-MM-DD</p>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label">펫 몸무게</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="pet_weight" value="<?php echo set_value('pet_weight', element('pet_weight', element('data', $view))); ?>" /> 숫자만 입력하세요 
				</div>
			</div>

			<div class="form-group">
				
				<div class="form-group">
					<label class="col-sm-2 control-label">펫 체형</label>
					<div class="col-sm-10">
						<div class="input-group">						
						<?php foreach(element('pet_form',element('config',$view)) as $key => $val){ 

							?>
							<input type="radio" name="pat_id" value="<?php echo element('pat_id',$val) ?>" <?php echo set_radio('pat_id', '1', (element('pat_id', element('data', $view)) == element('pat_id',$val) ? true : false)); ?> /> <?php echo element('pat_value',$val) ?>
						<?php } ?>
						
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label">펫 품종</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="pet_kind_text" value="<?php echo set_value('pet_kind_text', element('pet_kind_text', element('data', $view))); ?>" />

					
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 특징</label>
				<div class="col-sm-10">
					<?php
					$open = false;
					$attr = element('pet_attr',element('config',$view));

					

					$item_attr = element('pet_attr', element('data', $view));

					if ($attr) {
						$i = 0;
						foreach ($attr as $key => $val) {
							$display = (is_array($item_attr) && in_array(element('pat_id', $val), $item_attr)) ? "block" : 'none';
							if ($i%5== 0) {
								echo '<div>';
								$open = true;
							}
							echo '<div class="checkbox checkbox-inline" style="vertical-align:top;">';
							$pat_checked = (is_array($item_attr) && in_array(element('pat_id', $val), $item_attr)) ? 'checked="checked"' : '';
							echo '<label for="pat_id_' . element('pat_id', $val) . '"><input type="checkbox" name="pet_attr[]" value="' . element('pat_id', $val) . '" ' . $pat_checked . ' id="pat_id_' . element('pat_id', $val) . '" onclick="display_pet_attr(this.checked,\'cattrwrap_' . element('pat_id', $val) . '\');" />' . element('pat_value', $val) . '</label> ';
							// echo get_subattr($attr, $item_attr, element('pat_id', $val), $display);
							echo '</div>';
							if ($i%5== 4) {
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
					function get_subattr($attr, $item_attr, $key, $display)
					{

						$subcat = element($key, $attr);
						$html = '';
						if ($subcat) {
							$html .= '<div class="form-group" id="cattrwrap_' . $key . '" style="vertical-align:margin-left:10px;top;display:' . $display . ';" >';
							foreach ($subcat as $skey => $sval) {
								$display = (is_array($item_attr) && in_array(element('pat_id', $sval), $item_attr)) ? 'block' : 'none';
								$pat_checked = (is_array($item_attr) && in_array(element('pat_id', $sval), $item_attr)) ? 'checked="checked"' : '';
								$html .= '<div class="checkbox-inline" style="vertical-align:top;margin-left:10px;">';
								$html .= '<label for="pat_id_' . element('pat_id', $sval) . '"><input type="checkbox" name="pet_attr[]" value="' . element('pat_id', $sval) . '" ' . $pat_checked . ' id="pat_id_' . element('pat_id', $sval) . '" onclick="display_pet_attr(this.checked,\'cattrwrap_' . element('pat_id', $sval) . '\');" /> ' . element('pat_value', $sval) . '</label>';
								$html .= get_subattr($attr, $item_attr, element('pat_id', $sval), $display);
								$html .= '</div>';
							}
							$html .= '</div>';
						}
						return $html;
					}

					?>
					<script type="text/javascript">
					//<![CDATA[
					function display_pet_attr(check, idname) {
						// if (check === true) {
						// 	$('#' + idname).show();
						// } else {
						// 	$('#' + idname).hide();
						// 	$('#' + idname).find('input:checkbox').attr('checked', false);
						// }
					}
					//]]>
					</script>
				</div>
			</div>
			

			
			
			<div class="form-group">
				
				<div class="form-group">
					<label class="col-sm-2 control-label">펫 알레르기</label>
					<div class="col-sm-10">
						<div class="input-group">
						
						<input type="radio" name="pet_is_allergy" value="1" <?php echo set_radio('pet_is_allergy', '1', (element('pet_is_allergy', element('data', $view)) === '1' ? true : false)); ?> /> 있어요
						<input type="radio" name="pet_is_allergy" value="0" <?php echo set_radio('pet_is_allergy', '0', (element('pet_is_allergy', element('data', $view)) !== '1' ? true : false)); ?> /> 없어요					
						
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label">펫 알레르기 세부사항</label>
				<div class="col-sm-10">
					<?php
					$open = false;
					$allergy = element('pet_allergy_rel',element('config',$view));

					

					$item_allergy = element('pet_allergy_rel', element('data', $view));
					if (element(0, $allergy)) {
						$i = 0;
						foreach (element(0, $allergy) as $key => $val) {
							$display = (is_array($item_allergy) && in_array(element('pag_id', $val), $item_allergy)) ? "block" : 'none';
							if ($i%3== 0) {
								echo '<div>';
								$open = true;
							}
							echo '<div class="checkbox " style="vertical-align:top;">';
							$pag_checked = (is_array($item_allergy) && in_array(element('pag_id', $val), $item_allergy)) ? 'checked="checked"' : '';
							echo '<label for="pag_id_' . element('pag_id', $val) . '"><input type="checkbox" name="pet_allergy_rel[]" value="' . element('pag_id', $val) . '" ' . $pag_checked . ' id="pag_id_' . element('pag_id', $val) . '" onclick="display_pet_allergy(this.checked,\'callergywrap_' . element('pag_id', $val) . '\');" />' . element('pag_value', $val) . '</label> ';
							echo get_suballergy($allergy, $item_allergy, element('pag_id', $val), $display);
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
					function get_suballergy($allergy, $item_allergy, $key, $display)
					{

						$subcat = element($key, $allergy);
						$html = '';
						if ($subcat) {
							$html .= '<div class="form-group" id="callergywrap_' . $key . '" style="vertical-align:margin-left:10px;top;display:' . $display . ';" >';
							foreach ($subcat as $skey => $sval) {
								$display = (is_array($item_allergy) && in_array(element('pag_id', $sval), $item_allergy)) ? 'block' : 'none';
								$pag_checked = (is_array($item_allergy) && in_array(element('pag_id', $sval), $item_allergy)) ? 'checked="checked"' : '';
								$html .= '<div class="checkbox-inline" style="vertical-align:top;margin-left:10px;">';
								$html .= '<label for="pag_id_' . element('pag_id', $sval) . '"><input type="checkbox" name="pet_allergy_rel[]" value="' . element('pag_id', $sval) . '" ' . $pag_checked . ' id="pag_id_' . element('pag_id', $sval) . '" onclick="display_pet_allergy(this.checked,\'callergywrap_' . element('pag_id', $sval) . '\');" /> ' . element('pag_value', $sval) . '</label>';
								$html .= get_suballergy($allergy, $item_allergy, element('pag_id', $sval), $display);
								$html .= '</div>';
							}
							$html .= '</div>';
						}
						return $html;
					}

					?>
					<script type="text/javascript">
					//<![CDATA[
					function display_pet_allergy(check, idname) {
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
			
			<div class="form-group">
					<label class="col-sm-2 control-label">메인 펫 </label>
					<div class="col-sm-10">
						<label for="pet_main" class="checkbox-inline">
							<input type="checkbox" name="pet_main" id="pet_main" value="1" <?php echo set_checkbox('pet_main', '1', (element('pet_main', element('data', $view)) ? true : false)); ?> /> 메인
						</label>
						
						<div class="help-inline" >체크하시면, 메인페이지에 메인 펫으로 출력됩니다</div>
					</div>
				</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 사진</label>
				<div class="col-sm-10">
					<?php
					if (element('pet_photo', element('data', $view))) {
					?>
						<img src="<?php echo cdn_url('member_photo',element('pet_photo', element('data', $view))); ?>" alt="회원 사진" title="회원 사진" />
						<label for="pet_photo_del">
							<input type="checkbox" name="pet_photo_del" id="pet_photo_del" value="1" <?php echo set_checkbox('pet_photo_del', '1'); ?> /> 삭제
						</label>
					<?php
					}
					?>
					<input type="file" name="pet_photo" id="pet_photo" />
					<p class="help-block">가로길이 : 120px, 세로길이 : 120px 에 최적화되어있습니다, gif, jpg, png 파일 업로드가 가능합니다</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 배경</label>
				<div class="col-sm-10">
					<?php
					if (element('pet_backgroundimg', element('data', $view))) {
					?>
						<img src="<?php echo member_icon_url(element('pet_backgroundimg', element('data', $view)),0,0); ?>" alt="회원 아이콘" title="회원 아이콘" />
						<label for="pet_backgroundimg_del">
							<input type="checkbox" name="pet_backgroundimg_del" id="pet_backgroundimg_del" value="1" <?php echo set_checkbox('pet_backgroundimg_del', '1'); ?> /> 삭제
						</label>
					<?php
					}
					?>
					<input type="file" name="pet_backgroundimg" id="pet_backgroundimg" />
					<p class="help-block">가로길이 : 400px, 세로길이 : 200px 에 최적화되어있습니다, gif, jpg, png 파일 업로드가 가능합니다</p>
				</div>
			</div>
			
			
			
			
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 자기소개</label>
				<div class="col-sm-10">
					<textarea class="form-control" rows="5" name="pet_profile_content"><?php echo set_value('pet_profile_content', element('pet_profile_content', element('data', $view))); ?></textarea>
				</div>
			</div>
			
			
			<div class="btn-group pull-right" role="group" aria-label="...">
				<button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$('#fadminwrite').validate({
		rules: {
			mem_userid: { required: true },
			pet_name: {minlength:2, maxlength:20,required: true }
			
		}
	});
});


var searchSource = [
	<?php
	if (element('pet_kind',element('config',$view))) {
	    foreach (element('pet_kind', element('config', $view)) as $result) {
	 		echo '"'.element('ckd_value_kr',$result).'","'.element('ckd_value_en',$result).'",';
		}
	}
	        
	?>
	"========" 
]; // 배열 형태로 


$("input[name='pet_kind_text']")
.on("keydown", function( event ) {	
    if((event.keyCode === $.ui.keyCode.ENTER || event.keyCode === $.ui.keyCode.TAB) && $(this).autocomplete("instance").menu.active) {
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
