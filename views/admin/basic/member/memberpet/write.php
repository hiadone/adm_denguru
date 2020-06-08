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
						
						<?php foreach(config_item('pet_form') as $key => $val){ ?>
							<input type="radio" name="pet_form" value="<?php echo $key ?>" <?php echo set_radio('pet_form', '1', (element('pet_form', element('data', $view)) == $key ? true : false)); ?> /> <?php echo $val ?>
						<?php } ?>
						
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label">펫 품종</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="pet_kind" value="<?php echo set_value('pet_kind', element('pet_kind', element('data', $view))); ?>" />
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">펫 특징</label>
				<div class="col-sm-10">
					<div class="input-group">
					<?php 
					
					foreach(config_item('pet_attr') as $key => $val){
						echo '<div class="col-sm-2"><input type="checkbox" name="pet_attr[]" value="'.$key.'" '.set_checkbox('pet_attr', $key, (in_array($key,explode(',',element('pet_attr', element('data', $view)))) ? true : false)).' /> '.$val .'</div>';
					}
					?>
					
					</div>
				</div>
			</div>
			
			<div class="form-group">
				
				<div class="form-group">
					<label class="col-sm-2 control-label">펫 알레르기</label>
					<div class="col-sm-10">
						<div class="input-group">
						
						<input type="radio" name="pet_allergy" value="1" <?php echo set_radio('pet_allergy', '1', (element('pet_allergy', element('data', $view)) === '1' ? true : false)); ?> /> 있어요
						<input type="radio" name="pet_allergy" value="0" <?php echo set_radio('pet_allergy', '0', (element('pet_allergy', element('data', $view)) !== '1' ? true : false)); ?> /> 없어요					
						
						</div>
					</div>
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
						<img src="<?php echo member_photo_url(element('pet_photo', element('data', $view)),0,0); ?>" alt="회원 사진" title="회원 사진" />
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
//]]>
</script>
