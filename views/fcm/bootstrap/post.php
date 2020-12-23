<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>
<?php	$this->managelayout->add_js(base_url('plugin/zeroclipboard/ZeroClipboard.js')); ?>

<?php
if (element('syntax_highlighter', element('board', $view)) OR element('comment_syntax_highlighter', element('board', $view))) {
	$this->managelayout->add_css(base_url('assets/js/syntaxhighlighter/styles/shCore.css'));
	$this->managelayout->add_css(base_url('assets/js/syntaxhighlighter/styles/shThemeMidnight.css'));
	$this->managelayout->add_js(base_url('assets/js/syntaxhighlighter/scripts/shCore.js'));
	$this->managelayout->add_js(base_url('assets/js/syntaxhighlighter/scripts/shBrushJScript.js'));
	$this->managelayout->add_js(base_url('assets/js/syntaxhighlighter/scripts/shBrushPhp.js'));
	$this->managelayout->add_js(base_url('assets/js/syntaxhighlighter/scripts/shBrushCss.js'));
	$this->managelayout->add_js(base_url('assets/js/syntaxhighlighter/scripts/shBrushXml.js'));
?>
	<script type="text/javascript">
	SyntaxHighlighter.config.clipboardSwf = '<?php echo base_url('assets/js/syntaxhighlighter/scripts/clipboard.swf'); ?>';
	var is_SyntaxHighlighter = true;
	SyntaxHighlighter.all();
	</script>
<?php } ?>

<?php echo element('headercontent', element('board', $view)); ?>

<div class="board">
	<h3>FCM 발송 상세정보</h3>
	<?php echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>'); ?>
	<div class="form-horizontal box-table">
		<div class="form-group">
			<label for="fcm_title" class="col-sm-2 control-label">제목</label>
			<label class="col-sm-10" style="padding-top:7px;" >
				<?php echo element('fcm_title', element('post', $view)); ?>
			</label>
		</div>
		<div class="form-group">
				<label for="fcm_target" class="col-sm-2 control-label">발송타켓
					
				</label>
				<div class="col-sm-10 form-inline" style="display:table;">
					
					<?php
					$config = array(
						'column_name' => 'fcm_target',
						'column_group_name' => 'fcm_target_group',
						'column_value' => element('fcm_target', element('post', $view)),
						'column_group_value' => element('fcm_target_group', element('post', $view)),
						'mgroup' => element('mgroup', element('data', $view)),
						);
					echo get_fcm_send_selectbox($config);
					?>
				</div>
			</div>
		<div class="form-group">
			<label for="fcm_send_date" class="col-sm-2 control-label">전송시간</label>
			<label class="col-sm-10" style="padding-top:7px;" >
				<?php echo element('fcm_send_date', element('post', $view)); ?>
					


			</label>
		</div>
		
		<div class="form-group">
			<label for="fcm_message" class="col-sm-2 control-label">내용</label>
			<label class="col-sm-10" style="padding-top:7px;" >
				<?php echo element('fcm_message', element('post', $view)) ?>
			</label>
		</div>

		<div class="form-group">
			<label for="fcm_deeplinkinfo" class="col-sm-2 control-label">딥링크 정보</label>
			<label class="col-sm-10" style="padding-top:7px;" >
				<?php echo element('fcm_deeplinkinfo', element('post', $view)) ?>
			</label>
		</div>
		
		<div class="border_button text-center mt20">
			<a href="<?php echo element('list_url', $view); ?>" class="btn btn-default btn-sm">목록</a>
			
			<?php	if (element('delete_url', $view)) { ?>
				<button type="button" class="btn btn-danger btn-sm btn-one-delete" data-one-delete-url="<?php echo element('delete_url', $view); ?>">삭제</button>
			<?php } ?>
		</div>
	</div>
</div>
<?php echo element('footercontent', element('board', $view)); ?>

<?php if (element('target_blank', element('board', $view))) { ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
	$("#post-content a[href^='http']").attr('target', '_blank');
});
//]]>
</script>
<?php } ?>

<script type="text/javascript">
//<![CDATA[
var client = new ZeroClipboard($('.copy_post_url'));
client.on('ready', function( readyEvent ) {
	client.on('aftercopy', function( event ) {
		alert('게시글 주소가 복사되었습니다. \'Ctrl+V\'를 눌러 붙여넣기 해주세요.');
	});
});
//]]>
</script>
<?php
if (element('highlight_keyword', $view)) {
	$this->managelayout->add_js(base_url('assets/js/jquery.highlight.js'));
?>
	<script type="text/javascript">
	//<![CDATA[
		$('#post-content').highlight([<?php echo element('highlight_keyword', $view);?>]);
	//]]>
	</script>
<?php } ?>
