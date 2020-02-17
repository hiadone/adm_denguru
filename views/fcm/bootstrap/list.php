<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<?php echo element('headercontent', element('board', element('list', $view))); ?>

<div class="board">
	<h3>FCM 관리(앱 푸쉬)</h3>
	<?php echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info">', '</div>'); ?>
	<div class="row mb20">
		<div class="col-md-12">
			<div class=" searchbox">
				<form class="navbar-form navbar-right pull-right" action="<?php echo base_url('/fcm/lists'); ?>" onSubmit="return postSearch(this);">
					<input type="hidden" name="findex" value="<?php echo html_escape($this->input->get('findex')); ?>" />
					<div class="form-group">
						<select class="form-control pull-left px100" name="sfield">
							<option value="fcm_both" <?php echo ($this->input->get('sfield') === 'fcm_both') ? ' selected="selected" ' : ''; ?>>제목+메세지</option>
							<option value="fcm_title" <?php echo ($this->input->get('sfield') === 'fcm_title') ? ' selected="selected" ' : ''; ?>>제목</option>
							<option value="fcm_content" <?php echo ($this->input->get('sfield') === 'fcm_content') ? ' selected="selected" ' : ''; ?>>메세지</option>
							
						</select>
						<input type="text" class="form-control px150" placeholder="Search" name="skeyword" value="<?php echo html_escape($this->input->get('skeyword')); ?>" />
						<button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i></button>

						<?php if (element('search_list_url', element('list', $view))) { ?>
							<a class="btn btn-primary btn-sm" href="<?php echo element('list_url', element('list', $view)); ?>" ><i class="fa fa-refresh"></i></a>
						<?php } ?>

						
						
					</div>
				</form>
			</div>
		</div>
		<script type="text/javascript">
		//<![CDATA[
		function postSearch(f) {
			var skeyword = f.skeyword.value.replace(/(^\s*)|(\s*$)/g,'');
			if (skeyword.length < 2) {
				alert('2글자 이상으로 검색해 주세요');
				f.skeyword.focus();
				return false;
			}
			return true;
		}
		
		
		//]]>
		</script>
	</div>

	

	<?php
	$attributes = array('name' => 'fboardlist', 'id' => 'fboardlist');
	echo form_open('', $attributes);
	?>

		<table class="table table-hover">
			<thead>
				<tr>
					
					<th>번호</th>
					<th>제목</th>
					<!-- <th>메세지</th> -->
					<th>타켓</th>
					<th>전송날짜</th>
					<th >전송결과</th>
					<th>action</th>

					
				</tr>
			</thead>
			<tbody>
			<?php
			if (element('list', element('data', element('list', $view)))) {
				foreach (element('list', element('data', element('list', $view))) as $result) {
			?>
				<tr>					
					<td><?php echo element('num', $result); ?></td>
					<td>
						<a href="<?php echo element('post_url', $result); ?>" style="" title="<?php echo html_escape(element('fcm_title', $result)); ?>"><?php echo html_escape(element('fcm_title', $result)); ?></a>						
					</td>
					<!-- <td><?php echo element('fcm_message', $result); ?></td> -->
					<td>
						<?php

						if(element('fcm_target', $result)==='1') echo '모든 사용자';
						elseif(element('fcm_target', $result)==='2') echo '가입 회원';
						elseif(element('fcm_target', $result)==='3') echo '특정그룹회원';
						$group_value = json_decode(element('fcm_target_group',  $result), true);
						$fcm_target_group=array();
						$html='';
						if (element('mgroup', element('data', $view))) {
							$html.="<div><small>";
							foreach (element('mgroup', element('data', $view)) as $key => $value) {
							

								
								 is_array($group_value) && in_array(element('mgr_id', $value), $group_value) ? array_push($fcm_target_group,element('mgr_title', $value)) : '';
								
								
							}
							
							$html.=implode(",",$fcm_target_group);
							$html.="</small></div>";

							echo $html;
						}
						?>
						</td>
					<td><?php echo element('display_send_date', $result); ?></td>
					<td style="width:300px;word-break:break-all"><?php echo element('fcm_result', $result); ?></td>
					<td>
						<?php if (element('delete_url', $result)) { ?>
							<button type="button" class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url="<?php echo element('delete_url', $result); ?>">삭제</button>
						<?php } ?>
					</td>
				</tr>
			<?php
				}
			}
			if ( ! element('notice_list', element('list', $view)) && ! element('list', element('data', element('list', $view)))) {
			?>
				<tr>
					<td colspan="6" class="nopost">게시물이 없습니다</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	<?php echo form_close(); ?>

	<div class="border_button">
		<div class="pull-left mr10">
			<a href="<?php echo element('list_url', element('list', $view)); ?>" class="btn btn-default btn-sm">목록</a>
			<?php if (element('search_list_url', element('list', $view))) { ?>
				<!-- <a href="<?php echo element('search_list_url', element('list', $view)); ?>" class="btn btn-default btn-sm">검색목록</a> -->
			<?php } ?>
			<!-- <button class="btn btn-danger btn-sm" onClick="post_multi_action('multi_delete', '0', '선택하신 글들을 완전삭제하시겠습니까?');"><i class="fa fa-trash-o"></i> 선택삭제하기</button> -->
		</div>		
		<?php if (element('write_url', element('list', $view))) { ?>
			<div class="pull-right">
				<a href="<?php echo element('write_url', element('list', $view)); ?>" class="btn btn-success btn-sm">FCM 발송 하기</a>
			</div>
		<?php } ?>
	</div>
	<nav><?php echo element('paging', element('list', $view)); ?></nav>
</div>

<?php echo element('footercontent', element('board', element('list', $view))); ?>

<?php
if (element('highlight_keyword', element('list', $view))) {
	$this->managelayout->add_js(base_url('assets/js/jquery.highlight.js')); ?>
<script type="text/javascript">
//<![CDATA[
$('#fboardlist').highlight([<?php echo element('highlight_keyword', element('list', $view));?>]);
//]]>
</script>
<?php } ?>
