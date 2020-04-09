<div class="box">
	<div class="box-table">
		<?php		
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		
		?>
			
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2">태그사전 #1</a></h4>
				<a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab3">
				<?php 
				$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite');
				echo form_open(current_full_url(), $attributes);
				 ?>
				
				
				<div class="form-group">
					
					<div class="col-sm-3">
						<label class="">패션 태그사전</label>
						<textarea class="form-control" name="tgw_value_1" id="tgw_value_1" rows="15"><?php echo element('tgw_value_1', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
					</div>
					<div class="col-sm-3">
						<label class="">푸드 태그사전</label>
						<textarea class="form-control" name="tgw_value_2" id="tgw_value_2" rows="15"><?php echo element('tgw_value_2', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
					</div>
					<div class="col-sm-3">
						<label class="">산책외출 태그사전</label>
						<textarea class="form-control" name="tgw_value_3" id="tgw_value_3" rows="15"><?php echo element('tgw_value_3', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
					</div>
					<div class="col-sm-3">
						<label class="">이동 태그사전</label>
						<textarea class="form-control" name="tgw_value_4" id="tgw_value_4" rows="15"><?php echo element('tgw_value_4', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
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
			
			
			<div class="btn-group pull-right" role="group" aria-label="...">				
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>

	<div class="box-table">
		
			
			<div class="box-table-header">
				<h4><a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2">태그사전 #2</a></h4>
				<a data-toggle="collapse" href="#cmalltab2" aria-expanded="true" aria-controls="cmalltab2"><i class="fa fa-chevron-up pull-right"></i></a>
			</div>
			<div class="collapse in" id="cmalltab3">
				<?php		
				$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite');
				echo form_open(current_full_url(), $attributes);
				?>
				
				
				<div class="form-group">

					<div class="col-sm-3">
						<label class="">홈리빙 태그사전</label>
						<textarea class="form-control" name="tgw_value_5" id="tgw_value_5" rows="15"><?php echo element('tgw_value_5', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
					</div>
					<div class="col-sm-3">
						<label class="">놀이 장난감 태그사전</label>
						<textarea class="form-control" name="tgw_value_6" id="tgw_value_6" rows="15"><?php echo element('tgw_value_6', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
					</div>
					<div class="col-sm-3">
						<label class="">미용·목욕 태그사전</label>
						<textarea class="form-control" name="tgw_value_7" id="tgw_value_7" rows="15"><?php echo element('tgw_value_7', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
					</div>
					<div class="col-sm-3">
						<label class="">기타</label>
						<textarea class="form-control" name="tgw_value_8" id="tgw_value_8" rows="15"><?php echo element('tgw_value_8', element('data', $view)); ?></textarea>
						<div class="help-block">태그 입력(엔터 또는 콤마로 구분하여 입력)</div>
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
			
			
			<div class="btn-group pull-right" role="group" aria-label="...">				
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>
<!-- CSS , JS -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

			



