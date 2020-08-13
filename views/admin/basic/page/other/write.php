<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open_multipart(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
			<div class="form-group">
				<label class="col-sm-2 control-label">이미지 업로드</label>
				<div class="col-sm-10">
					<?php
					if (element('oth_image', element('data', $view))) {
					?>
						<img src="<?php echo thumb_url('other',element('oth_image', element('data', $view))); ?>" alt="배너 이미지" title="배너 이미지" />
						<label for="oth_image_del">
							<input type="checkbox" name="oth_image_del" id="oth_image_del" value="1" <?php echo set_checkbox('oth_image_del', '1'); ?> /> 삭제
						</label>
					<?php
					}
					?>
					<input type="file" name="oth_image" id="oth_image" />
					<p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">이미지 설명</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="oth_title" value="<?php echo set_value('oth_title', element('oth_title', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">시작일</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control datepicker" name="oth_start_date" value="<?php echo set_value('oth_start_date', element('oth_start_date', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">종료일</label>
				<div class="col-sm-10 form-inline">
					<input type="text" class="form-control datepicker" name="oth_end_date" value="<?php echo set_value('oth_end_date', element('oth_end_date', element('data', $view))); ?>" />
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">URL</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="oth_url" value="<?php echo set_value('oth_url', element('oth_url', element('data', $view))); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">배너 사이즈</label>
				<div class="col-sm-10 form-inline">
					가로 :
					<input type="number" class="form-control" name="oth_width" value="<?php echo set_value('oth_width', (int) element('oth_width', element('data', $view))); ?>" />px
					,
					세로 :
					<input type="number" class="form-control" name="oth_height" value="<?php echo set_value('oth_height', (int) element('oth_height', element('data', $view))); ?>" />px
					<div class="help-inline">가로값과 세로값을 입력하시면 입력하신 사이즈로 배너가 출력이 되며, 입력하지 않으면 업로드한 원본 크기대로 출력됩니다</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">정렬순서</label>
				<div class="col-sm-10">
					<input type="number" class="form-control" name="oth_order" value="<?php echo set_value('oth_order', (int) element('oth_order', element('data', $view))); ?>" />
					<div class="help-inline">정렬 순서가 큰 값이 먼저 출력됩니다</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label">활성화</label>
				<div class="col-sm-10">
					<label class="radio-inline" for="oth_activated_1">
						<input type="radio" name="oth_activated" id="oth_activated_1" value="1" <?php echo set_radio('oth_activated', '1', (element('oth_activated', element('data', $view)) !== '0' ? true : false)); ?> /> 활성
					</label>
					<label class="radio-inline" for="oth_activated_0">
						<input type="radio" name="oth_activated" id="oth_activated_0" value="0" <?php echo set_radio('oth_activated', '0', (element('oth_activated', element('data', $view)) === '0' ? true : false)); ?> /> 비활성
					</label>
				</div>
			</div>

			<div class="pull-left mr10">
                    <button type="button" class="btn btn-default btn-sm admin-manage-list" onClick="theme_in_store(<?php echo element(element('primary_key', $view), element('data', $view)); ?>);" >테마에 스토어 종속 시키기</button>
                </div>

			<div class="btn-group pull-right" role="group" aria-label="...">
				<button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>

    <div class="box">
		<div class="box-table">
            <?php
            echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
            $attributes = array('class' => 'form-inline', 'name' => 'flist', 'id' => 'flist');
            echo form_open(current_full_url(), $attributes);
            ?>
                <div class="box-table-header">
                    <?php
                    ob_start();
                    ?>
                        <div class="btn-group pull-right" role="group" aria-label="...">
                            <a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">전체목록</a>
                            <button type="button" class="btn btn-outline btn-default btn-sm btn-list-update btn-list-selected " data-list-update-url = "<?php echo element('list_update_url', $view); ?>" >선택추가</button>
                            <button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected " data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button>
                        </div>
                    <?php
                    $buttons = ob_get_contents();
                    ob_end_flush();
                    ?>
                </div>
                <div class="row">전체 : <?php echo element('total_rows', element('cdata', $view), 0); ?>건</div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="chkall" id="chkall" /></th>
                                <th>스토어명</th>                                
                                <th>이미지</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (element('list', element('cdata', $view))) {
                            foreach (element('list', element('cdata', $view)) as $result) {

                        ?>
                            <tr class="<?php echo element('checked', $result) ? 'success':''; ?> ">
                                <td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" <?php echo element('checked',$result) ? 'checked':'';?>/></td>
                                <td><a href="<?php echo board_url(element('brd_key', $result)); ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span> <?php echo html_escape(element('brd_name', $result)); ?></a></td>
                               
                                <td>
                                    <?php if (element('brd_image', $result)) {?>
                                       
                                            <img src="<?php echo thumb_url('board', element('brd_image', $result)); ?>" alt="<?php echo html_escape(element('brd_name', $result)); ?>" title="<?php echo html_escape(element('brd_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
                                        
                                    <?php } ?>
                                </td>
                               
                            </tr>
                        <?php
                            }
                        }
                        if ( ! element('list', element('cdata', $view))) {
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
                <div class="box-info">
                    <?php echo element('paging', $view); ?>
                    <div class="pull-left ml20"><?php echo admin_listnum_selectbox();?></div>
                    <?php echo $buttons; ?>
                </div>
            <?php echo form_close(); ?>
        </div>
    
</div>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$('#fadminwrite').validate({
		rules: {
			oth_title: 'required',
			oth_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
			oth_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
			oth_width: { number:true },
			oth_height: { number:true },
			oth_order: { number:true },
			oth_activated: 'required'
		}
	});
});
//]]>
</script>
