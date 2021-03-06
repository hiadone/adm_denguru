<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<?php 
echo element('headercontent', element('board', element('list', $view))); 


$cmall_count =array();

$cmall_total = 0;

foreach (element('cmall_count', $view) as $val) 
{

	$cmall_total +=element('rownum',$val); 
}




$warning_count =array();

$warning_total = 0;


foreach (element('warning_count', $view) as $val) 
{	
	

	$warning_total +=element('rownum',$val); 
	
}




$notcategory_count =array();

$notcategory_total = 0;


foreach (element('notcategory_count', $view) as $val) 
{
	
	$notcategory_total +=element('cnt',$val); 
	
}


?>


?>

<div class="board">
	<h3><a href="<?php echo admin_url('board/boards/write/'.element('brd_id', element('board', element('list', $view)))); ?>"><span class="glyphicon glyphicon-new-window"></span></a><?php echo html_escape(element('board_name', element('board', element('list', $view)))); ?><?php echo element('brd_comment', element('board_crawl', element('list', $view))) ? '('.element('brd_comment', element('board_crawl', element('list', $view))).')' : '' ?><span class="ml20" style="font-size:14px;"><i class="fa fa-link"></i><a href="<?php echo element('brd_url', element('board_crawl', element('list', $view))); ?>" target="_blank"><?php echo element('brd_url', element('board_crawl', element('list', $view))); ?></a></span>
		<a href='<?php  echo site_url($this->uri->uri_string())?>' class="btn btn-info btn-xs">총 상품 <?php echo number_format($cmall_total); ?> 개</a>
		<a href='<?php  echo site_url($this->uri->uri_string())?>?warning=1' class="btn btn-warning btn-xs">총 warning 상품 <?php echo number_format($warning_total); ?> 개</a>
		<a href='<?php  echo site_url($this->uri->uri_string())?>?notcategory=1' class="btn btn-warning btn-xs">카테고리 없는 총 상품 <?php echo number_format($cmall_total - $notcategory_total); ?> 개</a>
	</h3>
	<div class="row mb20">
		<div class="col-xs-6 form-inline">
			<?php if ( ! element('access_list', element('board', element('list', $view))) && element('use_rss_feed', element('board', element('list', $view)))) { ?>
				<a href="<?php echo rss_url(element('brd_key', element('board', element('list', $view)))); ?>" class="btn btn-danger btn-sm" title="<?php echo html_escape(element('board_name', element('board', element('list', $view)))); ?> RSS 보기"><i class="fa fa-rss"></i></a>
			<?php } ?>
			<select class="form-control px150" onchange="location.href='<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>?category_id=<?php echo html_escape($this->input->get('categroy_id')); ?>&amp;findex=' + this.value;">
				<option value="">정렬하기</option>
				<option value="post_datetime desc" <?php echo $this->input->get('findex') === 'post_datetime desc' ? 'selected="selected"' : '';?>>날짜순</option>
				<option value="post_hit desc" <?php echo $this->input->get('findex') === 'post_hit desc' ? 'selected="selected"' : '';?>>조회수</option>
				<option value="post_comment_count desc" <?php echo $this->input->get('findex') === 'post_comment_count desc' ? 'selected="selected"' : '';?>>댓글수</option>
				<?php if (element('use_post_like', element('board', element('list', $view)))) { ?>
					<option value="post_like desc" <?php echo $this->input->get('findex') === 'post_like desc' ? 'selected="selected"' : '';?>>추천순</option>
				<?php } ?>
			</select>
			<?php if (element('use_category', element('board', element('list', $view))) && ! element('cat_display_style', element('board', element('list', $view)))) { ?>
				<select class="form-control px150" onchange="location.href='<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>?findex=<?php echo html_escape($this->input->get('findex')); ?>&category_id=' + this.value;">
					<option value="">카테고리선택</option>
					<?php
					$category = element('category', element('board', element('list', $view)));
					function ca_select($p = '', $category = '', $category_id = '')
					{
						$return = '';
						if ($p && is_array($p)) {
							foreach ($p as $result) {
								if(element('bca_key', $result)){
									$exp = explode('.', element('bca_key', $result));
									$len = (element(1, $exp)) ? strlen(element(1, $exp)) : 0;
									$space = str_repeat('-', $len);
									$return .= '<option value="' . html_escape(element('bca_key', $result)) . '"';
									if (element('bca_key', $result) === $category_id) {
										$return .= 'selected="selected"';
									}
									$return .= '>' . $space . html_escape(element('bca_value', $result)) . '</option>';
									$parent = element('bca_key', $result);
									$return .= ca_select(element($parent, $category), $category, $category_id);
								} else {
									$exp = explode('.', element('bca_key', $result));
									$len = (element(1, $exp)) ? strlen(element(1, $exp)) : 0;
									$space = str_repeat('-', $len);
									$return .= '<option value="' . html_escape(element('bca_key', $result)) . '"';
									if (element('bca_key', $result) === $category_id) {
										$return .= 'selected="selected"';
									}
									$return .= '>' . $space . html_escape(element('bca_value', $result)) . '</option>';
									$parent = element('bca_key', $result);
									$return .= ca_select(element($parent, $category), $category, $category_id);
								}
								
							}
						}
						return $return;
					}

					echo ca_select(element(0, $category), $category, $this->input->get('category_id'));
					?>
				</select>
			<?php } ?>
		</div>
		<div class="col-md-6">
			<div class=" searchbox">
				<form class="navbar-form navbar-right pull-right" action="<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>" onSubmit="return postSearch(this);">
					<input type="hidden" name="findex" value="<?php echo html_escape($this->input->get('findex')); ?>" />
					<input type="hidden" name="category_id" value="<?php echo html_escape($this->input->get('category_id')); ?>" />
					<div class="form-group">
						<select class="form-control pull-left px100" name="sfield">							
							<option value="post_title" <?php echo ($this->input->get('sfield') === 'post_title') ? ' selected="selected" ' : ''; ?>>제목</option>
							<option value="cit_name" <?php echo ($this->input->get('sfield') === 'cit_name') ? ' selected="selected" ' : ''; ?>>상품명</option>							
							<!-- <option value="cca_value" <?php echo ($this->input->get('sfield') === 'cca_value') ? ' selected="selected" ' : ''; ?>>카테고리</option> -->
							<option value="cta_tag" <?php echo ($this->input->get('sfield') === 'cta_tag') ? ' selected="selected" ' : ''; ?>>태그</option>
							<option value="cat_value" <?php echo ($this->input->get('sfield') === 'cat_value') ? ' selected="selected" ' : ''; ?>>특성</option>
							
						</select>
						<input type="text" class="form-control px150" placeholder="Search" name="skeyword" value="<?php echo html_escape($this->input->get('skeyword')); ?>" />
						<button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i></button>
					</div>
				</form>
			</div>
			
			<?php if (element('point_info', element('list', $view))) { ?>
				<div class="point-info pull-right mr10">
					<button class="btn-point-info btn-link" data-toggle="popover" data-trigger="focus" data-placement="left" title="포인트안내" data-content="<?php echo element('point_info', element('list', $view)); ?>"
					><i class="fa fa-info-circle fa-lg"></i></button>
				</div>
			<?php } ?>
		</div>
		<script type="text/javascript">
		//<![CDATA[
		function postSearch(f) {
			var skeyword = f.skeyword.value.replace(/(^\s*)|(\s*$)/g,'');
			// if (skeyword.length < 1) {
			// 	alert('2글자 이상으로 검색해 주세요');
			// 	f.skeyword.focus();
			// 	return false;
			// }
			return true;
		}
		
		$('.btn-point-info').popover({
			template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-title"></div><div class="popover-content"></div></div>',
			html : true
		});
		//]]>
		</script>
	</div>

	<?php
	if (element('use_category', element('board', element('list', $view))) && element('cat_display_style', element('board', element('list', $view))) === 'tab') {

		$category = element('category', element('board', element('list', $view)));
		$category_cnt = element('category_cnt', element('board', element('list', $view)));

		

	?>
		<ul class="nav nav-tabs clearfix">
			<li role="presentation" <?php if ( ! $this->input->get('category_id')) { ?>class="active" <?php } ?>><a href="<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>?findex=<?php echo html_escape($this->input->get('findex')); ?>&category_id=">전체 (<?php echo number_format(element('total', $category_cnt)); ?>)</a></li>
			<?php
			if (element(0, $category)) {
				foreach (element(0, $category) as $ckey => $cval) {

			?>
				<li role="presentation" <?php if ($this->input->get('category_id') === element('cca_id', $cval)) { ?>class="active" <?php } ?>><a href="<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>?findex=<?php echo html_escape($this->input->get('findex')); ?>&category_id=<?php echo element('cca_id', $cval); ?>"><?php echo html_escape(element('cca_value', $cval)); ?> (<?php echo number_format(element(element('cca_id', $cval), $category_cnt)); ?>)</a></li>
			<?php
				}
			}
			?>
		</ul>
	<?php
	}
	?>

	<?php
	$attributes = array('name' => 'fboardlist', 'id' => 'fboardlist');
	echo form_open('', $attributes);
	?>

		<table class="table table-hover">
			<thead>
				<tr>
					<?php if (element('is_admin', $view)) { ?><th><input onclick="if (this.checked) all_boardlist_checked(true); else all_boardlist_checked(false);" type="checkbox" /></th><?php } ?>
					<th>번호</th>
					<th>제목</th>
					<th>상품수</th>
					<th>글쓴이</th>
					<th>날짜</th>
					<th>조회수</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if (element('notice_list', element('list', $view))) {
				foreach (element('notice_list', element('list', $view)) as $result) {
			?>
				<tr>
					<?php if (element('is_admin', $view)) { ?><th scope="row"><input type="checkbox" name="chk_post_id[]" value="<?php echo element('post_id', $result); ?>" /></th><?php } ?>
					<td><span class="label label-primary">공지</span></td>
					<td>
						<?php if (element('post_reply', $result)) { ?><span class="label label-primary" style="margin-left:<?php echo strlen(element('post_reply', $result)) * 10; ?>px">Re</span><?php } ?>
						<a href="<?php echo element('post_url', $result); ?>" style="
							<?php
							if (element('title_color', $result)) {
								echo 'color:' . element('title_color', $result) . ';';
							}
							if (element('title_font', $result)) {
								echo 'font-family:' . element('title_font', $result) . ';';
							}
							if (element('title_bold', $result)) {
								echo 'font-weight:bold;';
							}
							if (element('post_id', element('post', $view)) === element('post_id', $result)) {
								echo 'font-weight:bold;';
							}
							?>
						" title="<?php echo html_escape(element('title', $result)); ?>"><?php echo html_escape(element('title', $result)); ?></a>
						<?php if (element('is_mobile', $result)) { ?><span class="fa fa-wifi"></span><?php } ?>
						<?php if (element('post_file', $result)) { ?><span class="fa fa-download"></span><?php } ?>
						<?php if (element('post_secret', $result)) { ?><span class="fa fa-lock"></span><?php } ?>
						<?php if (element('ppo_id', $result)) { ?><i class="fa fa-bar-chart"></i><?php } ?>
						<?php if (element('post_comment_count', $result)) { ?><span class="label label-warning">+<?php echo element('post_comment_count', $result); ?></span><?php } ?>
					<td><?php echo element('cmallitem_count', $result); ?></td>
					<td><?php echo element('display_name', $result); ?></td>
					<td><?php echo element('display_datetime', $result); ?></td>
					<td><?php echo number_format(element('post_hit', $result)); ?></td>
				</tr>
			<?php
				}
			}
			if (element('list', element('data', element('list', $view)))) {
				foreach (element('list', element('data', element('list', $view))) as $result) {

			?>
				<tr>
					<?php if (element('is_admin', $view)) { ?><th scope="row"><input type="checkbox" name="chk_post_id[]" value="<?php echo element('post_id', $result); ?>" /></th><?php } ?>
					<td><?php echo element('num', $result); ?></td>
					<td>
						<?php 
                        if (element('category', $result)) { 
                            foreach(element('category', $result) as $va){
                                
                                if(empty(element('cca_value',$va))) continue;
                                    if(element('cca_value',$va) =='no category')
                                        echo '<span class="label label-warning">'.html_escape(element('cca_value',$va)).'('.element('cnt',$va).')</span> ';
                                    else
                                        echo '<span class="label label-default">'.html_escape(element('cca_value',$va)).'('.element('cnt',$va).')</span> ';
                                
                            }
                        }
                        ?>
							
						<?php if (element('post_reply', $result)) { ?><span class="label label-primary" style="margin-left:<?php echo strlen(element('post_reply', $result)) * 10; ?>px">Re</span><?php } ?>
						<a href="<?php echo element('post_url', $result); ?>" style="
							<?php
							if (element('title_color', $result)) {
								echo 'color:' . element('title_color', $result) . ';';
							}
							if (element('title_font', $result)) {
								echo 'font-family:' . element('title_font', $result) . ';';
							}
							if (element('title_bold', $result)) {
								echo 'font-weight:bold;';
							}
							if (element('post_id', element('post', $view)) === element('post_id', $result)) {
								echo 'font-weight:bold;';
							}
							?>
						" title="<?php echo html_escape(element('title', $result)); ?>"><?php echo element('title', $result); ?></a>
						<?php if (element('is_mobile', $result)) { ?><span class="fa fa-wifi"></span><?php } ?>
						<?php if (element('post_file', $result)) { ?><span class="fa fa-download"></span><?php } ?>
						<?php if (element('post_secret', $result)) { ?><span class="fa fa-lock"></span><?php } ?>
						<?php if (element('is_hot', $result)) { ?><span class="label label-danger">Hot</span><?php } ?>
						<?php if (element('is_new', $result)) { ?><span class="label label-warning">New</span><?php } ?>
						<?php if (element('ppo_id', $result)) { ?><i class="fa fa-bar-chart"></i><?php } ?>
						<?php if (element('post_comment_count', $result)) { ?><span class="label label-warning">+<?php echo element('post_comment_count', $result); ?></span><?php } ?>
						<?php if (element('warning_count', $result)) { ?>
						<button class="btn btn-warning btn-xs">warning  <?php echo element('warning_count', $result); ?> 개</button><?php } ?>
						<?php if (element('disable', $result)) { ?>
						<button class="btn btn-primary btn-xs">disable  <?php echo element('disable', $result); ?> 개</button><?php } ?>
					</td>
					<td><?php echo element('cmall_count', $result); ?> 개</td>
					<td><?php echo element('display_name', $result); ?></td>
					<td><?php echo element('display_datetime', $result); ?></td>
					<td><?php echo number_format(element('post_hit', $result)); ?></td>
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
				<a href="<?php echo element('search_list_url', element('list', $view)); ?>" class="btn btn-default btn-sm">검색목록</a>
			<?php } ?>
		</div>
		<?php if (element('is_admin', $view)) { ?>
			<div class="pull-left">
				<button type="button" class="btn btn-default btn-sm admin-manage-list"><i class="fa fa-cog big-fa"></i> 관리</button>
				<div class="btn-admin-manage-layer admin-manage-layer-list">
					
						<!-- <div class="item" onClick="document.location.href='<?php echo admin_url('board/boards/write/' . element('brd_id', element('board', element('list', $view)))); ?>';"><i class="fa fa-cog"></i> 게시판설정</div>
						<div class="item" onClick="post_multi_copy('copy');"><i class="fa fa-files-o"></i> 복사하기</div>
						<div class="item" onClick="post_multi_copy('move');"><i class="fa fa-arrow-right"></i> 이동하기</div> -->
					<div class="item" onClick="post_multi_change_category();"><i class="fa fa-tags"></i> 카테고리변경</div>
					<div class="item" onClick="post_multi_change_brand();"><i class="fa fa-tags"></i> 브랜드변경</div>
					<div class="item" onClick="post_multi_change_attr();"><i class="fa fa-tags"></i> 특성변경</div>					
					<div class="item" onClick="post_multi_add_tag();"><i class="fa fa-tags"></i> 태그추가</div>
					<div class="item" onClick="post_multi_delete_tag();"><i class="fa fa-tags"></i> 태그삭제</div>

					
					<div class="item" onClick="post_multi_action('multi_delete', '0', '선택하신 글들을 완전삭제하시겠습니까?');"><i class="fa fa-trash-o"></i> 선택삭제하기</div>
					<!-- <div class="item" onClick="post_multi_action('post_multi_secret', '0', '선택하신 글들을 비밀글을 해제하시겠습니까?');"><i class="fa fa-unlock"></i> 비밀글해제</div>
					<div class="item" onClick="post_multi_action('post_multi_secret', '1', '선택하신 글들을 비밀글로 설정하시겠습니까?');"><i class="fa fa-lock"></i> 비밀글로</div>
					<div class="item" onClick="post_multi_action('post_multi_notice', '0', '선택하신 글들을 공지를 내리시겠습니까?');"><i class="fa fa-bullhorn"></i> 공지내림</div>
					<div class="item" onClick="post_multi_action('post_multi_notice', '1', '선택하신 글들을 공지로 등록 하시겠습니까?');"><i class="fa fa-bullhorn"></i> 공지올림</div> -->
					<div class="item" onClick="post_multi_action('post_multi_blame_blind', '0', '선택하신 글들을 블라인드 해제 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i> 블라인드해제</div>
					<div class="item" onClick="post_multi_action('post_multi_blame_blind', '1', '선택하신 글들을 블라인드 처리 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i> 블라인드처리</div>
					<!-- <div class="item" onClick="post_multi_action('post_multi_trash', '', '선택하신 글들을 휴지통으로 이동하시겠습니까?');"><i class="fa fa-trash"></i> 휴지통으로</div> -->
				</div>
			</div>
		<?php } ?>
		<?php if (element('write_url', element('list', $view))) { ?>
			<div class="pull-right">
				<a href="<?php echo element('write_url', element('list', $view)); ?>" class="btn btn-success btn-sm">글쓰기</a>
			</div>
		<?php } ?>

		<?php if (element('crawl_attr_update', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_attr_update', element('list', $view)); ?>" class="btn btn-warning btn-sm">게시글 전체 상품 제품특성 update</a>
			</div>
		<?php } ?>

		<?php if (element('crawl_tag_update', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_tag_update', element('list', $view)); ?>" class="btn btn-warning btn-sm">게시글 전체 태그 update</a>
			</div>
		<?php } ?>

		<?php if (element('crawl_tag_overwrite', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_tag_overwrite', element('list', $view)); ?>" class="btn btn-warning btn-sm">게시글 전체 태그 overwrite</a>
			</div>
		<?php } ?>
	
		<?php if (element('vision_api_label', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('vision_api_label', element('list', $view)); ?>" class="btn btn-warning btn-sm">vision_api_label update</a>
			</div>
		<?php } ?>	
		<?php if (element('crawl_category_update', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_category_update', element('list', $view)); ?>" class="btn btn-warning btn-sm">게시글 전체 카테고리 </a>
			</div>
		<?php } ?>

		<?php if (element('crawl_update', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_update', element('list', $view)); ?>" class="btn btn-warning btn-sm">게시글 전체 크롤링 update</a>
			</div>
		<?php } ?>


		<?php if (element('crawl_overwrite', element('list', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_overwrite', element('list', $view)); ?>" class="btn btn-danger btn-sm">게시글 전체 크롤링 overWrite</a>
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


<script>

$('button.btn-warning').click(function(){	
	if ( ! confirm('정말 실행 하겠습니까?')) 
		{ event.preventDefault() ;return false; }
});
	</script>
