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
						<a href="<?php echo element('warning_url', $view); ?>" class="btn btn-warning btn-sm">warning 목록</a>
						<!-- <button type="button" class="btn btn-outline btn-default btn-sm btn-list-update btn-list-selected disabled" data-list-update-url = "<?php echo element('list_update_url', $view); ?>" >선택수정</button>
						<button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button> -->
						
					</div>
				<?php
				$buttons = ob_get_contents();
				ob_end_flush();
				?>
			</div>
			<div class="row">전체 : <?php echo element('total_rows', element('data', $view), 0); ?>건</div>
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th>상품코드</th>
							<th><a href="<?php echo element('brd_id', element('sort', $view)); ?>">스토어</a></th>
							<th><a href="<?php echo element('crw_category1', element('sort', $view)); ?>">카테고리</a></th>
							<th><a href="<?php echo element('crw_brand1', element('sort', $view)); ?>">브랜드</a></th>
							<th>이미지</th>
							<th>상세이미지</th>
							<th>상세TEXT</th>
							<th>브랜드2</th>
							<th><a href="<?php echo element('crw_name', element('sort', $view)); ?>">상품명</a></th>
							<th><a href="<?php echo element('crw_price', element('sort', $view)); ?>">판매가격</a></th>
							<!-- <th>Vision API label</th> -->
                    		
							<th><a href="<?php echo element('crw_price_sale', element('sort', $view)); ?>">할인가격</a></th>
							<th><a href="<?php echo element('crw_category1', element('sort', $view)); ?>">판매여부</a></th>
							<!-- <th>수정</th>
							<th><input type="checkbox" name="chkall" id="chkall" /></th> -->
						</tr>
					</thead>
					<tbody>

					<?php
					if (element('list', element('data', $view))) {
						foreach (element('list', element('data', $view)) as $result) {
					?>
						<tr class="<?php echo element('warning', $result) ? 'warning':''; ?> ">
							<td><?php echo element('crw_goods_code', $result); ?>
								<br>
								<br>
								<?php echo element('crw_id', $result); ?>
							</td>
							<td><?php echo element('brd_name', $result); ?></td>
							<td >
								<?php echo html_escape(rawurldecode(element('crw_category1', $result))); ?><br>
								<?php echo html_escape(rawurldecode(element('crw_category2', $result))); ?><br>
								<?php echo html_escape(rawurldecode(element('crw_category3', $result))); ?>
							</td>
							<td >
								<?php echo html_escape(element('crw_brand1', $result)); ?><br>
								<?php echo html_escape(element('crw_brand2', $result)); ?><br>
								<?php echo html_escape(element('crw_brand3', $result)); ?><br>
								<?php echo html_escape(element('crw_brand4', $result)); ?><br>
								<?php echo html_escape(element('crw_brand5', $result)); ?>

								
							</td>
							<td>
								
								<?php if (element('crw_file_1', $result)) {?>
									<a href="<?php echo element('crw_post_url', $result); ?>" target="_blank">
										<img src="<?php echo thumb_url('crawlitem', element('crw_file_1', $result), 80); ?>" alt="<?php echo html_escape(element('crw_name', $result)); ?>" title="<?php echo html_escape(element('crw_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
									</a>
								<?php } ?>
							</td>
							<td>
								
								<?php if (element('cdt_file_1', $result)) {?>
									<a href="<?php echo element('crw_post_url', $result); ?>" target="_blank">
										<img src="<?php echo thumb_url('crawlitem', element('cdt_file_1', $result), 80); ?>" alt="<?php echo html_escape(element('crw_name', $result)); ?>" title="<?php echo html_escape(element('crw_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
									</a>
								<?php } ?>
							</td>
							<td>
								<?php echo html_escape(element('cdt_context', $result)); ?><br>
								
							</td>
							<td>
								<?php echo html_escape(element('cdt_brand1', $result)); ?><br>
								<?php echo html_escape(element('cdt_brand2', $result)); ?><br>
								<?php echo html_escape(element('cdt_brand3', $result)); ?><br>
								<?php echo html_escape(element('cdt_brand4', $result)); ?><br>
								<?php echo html_escape(element('cdt_brand5', $result)); ?>
							</td>
							<td><a href="<?php echo element('crw_post_url', $result); ?>" target="_blank"><?php echo html_escape(element('crw_name', $result)); ?></a>
								<br>
								<br>
	<?php echo element('crw_datetime', $result); ?>
								<br>
								<?php echo element('crw_updated_datetime', $result); ?>
							</td>
							<td><?php echo html_escape(element('crw_price', $result)); ?></td>
							
							<!-- <td>
		                       <textarea name="vision_api_label[<?php echo element('crw_id', $result); ?>]" id="val_tag_<?php echo element('crw_id', $result); ?>" data-crw_id="<?php echo element('crw_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="선택 옵션 (엔터로 구분하여 입력)"><?php echo html_escape(element('display_label', $result)); ?></textarea>
		                    </td> -->
		                   
							
							<td><?php echo html_escape(element('crw_price_sale', $result)); ?></td>
							<td><?php echo element('crw_is_soldout', $result) ? 'sold out' : '-'; ?></td>
							
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
			<div class="box-info">
				<?php echo element('paging', $view); ?>
				<div class="pull-left ml20"><?php echo admin_listnum_selectbox();?></div>
				<?php echo $buttons; ?>
			</div>
		<?php echo form_close(); ?>
	</div>
	<form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
		<div class="box-search">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<select class="form-control" name="sfield" >
						<?php echo element('search_option', $view); ?>
					</select>
					<div class="input-group">
						<input type="text" class="form-control" name="skeyword" value="<?php echo html_escape(element('skeyword', $view)); ?>" placeholder="Search for..." />
						<span class="input-group-btn">
							<button class="btn btn-default btn-sm" name="search_submit" type="submit">검색!</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
//<![CDATA[


$(document).on('change', 'textarea[name^=cta_tag]', function() {
    post_action_crawl('cta_tag_update', $(this).data('crw_id'),'cta_tag_');
});



//]]>
</script>

