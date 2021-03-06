<?php
defined('BASEPATH') OR exit('No direct script access allowed');


echo display_html_content(element('headercontent', element('group', $view)));

$cmall_count =array();

$cmall_total = 0;

foreach (element('cmall_count', $view) as $val) 
{
	foreach(element('search_list', $view) as $gval){
		if(element('brd_id',$gval) === element('brd_id',$val)){
			$cmall_count[element('brd_id',$val)] = element('rownum',$val);
			$cmall_total +=element('rownum',$val); 
			break;
		}
	}
	
}




$warning_count =array();

$warning_total = 0;


foreach (element('warning_count', $view) as $val) 
{	
	foreach(element('search_list', $view) as $gval){
		if(element('brd_id',$gval) === element('brd_id',$val)){
			$warning_count[element('brd_id',$val)] = element('rownum',$val);

			$warning_total +=element('rownum',$val); 
			break;
		}
	}
}




$notcategory_count =array();

$notcategory_total = 0;


foreach (element('notcategory_count', $view) as $val) 
{
	foreach(element('search_list', $view) as $gval){
		if(element('brd_id',$gval) === element('brd_id',$val)){
			$notcategory_count[element('brd_id',$val)] = element('cnt',$val);

			$notcategory_total +=element('cnt',$val); 
			break;
		}
	}
}


?>
<div class="board">
	<h3><?php echo html_escape(element('bgr_name', element('group', $view))); ?>
		<a href='<?php  echo site_url($this->uri->uri_string())?>' class="btn btn-info btn-xs">총 상품 <?php echo number_format($cmall_total); ?> 개</a>

		<a href='<?php  echo site_url($this->uri->uri_string())?>?warning=1' class="btn btn-warning btn-xs">총 warning 상품 <?php echo number_format($warning_total); ?> 개</a>
		<a href='<?php  echo site_url($this->uri->uri_string())?>?notcategory=1' class="btn btn-warning btn-xs">카테고리 없는 총 상품 <?php echo number_format($cmall_total -$notcategory_total); ?> 개</a>

		<!-- <button class="btn btn-warning btn-xs">총 warning 상품 <?php echo number_format($warning_total); ?> 개</button>
		<button class="btn btn-warning btn-xs">카테고리 없는 총 상품 <?php echo number_format($cmall_total - $notcategory_total); ?> 개</button> -->
	</h3>
</div>

<?php
$k = 0;
$is_open = false;
if (element('board_list', $view)) {
	foreach (element('board_list', $view) as $key => $board) {
		$config = array(
			'skin' => 'bootstrap',
			'brd_id' => element('brd_id', $board),
			'limit' => 5,
			'length' => 25,
			'is_gallery' => '',
			'image_width' => '',
			'image_height' => '',
			'cache_minute' => 1,
			'cmall_count' => element(element('brd_id', $board),$cmall_count),
			'warning_count' => element(element('brd_id', $board),$warning_count),
			'notcategory_count' => element(element('brd_id', $board),$notcategory_count),
		);
		if ($k % 2 === 0) {
			echo '<div class="row">';
			$is_open = true;
		}
		echo $this->board->latest($config);
		if ($k % 2 === 1) {
			echo '</div>';
			$is_open = false;
		}
		$k++;
	}
}
if ($is_open) {
	echo '</div>';
	$is_open = false;
}


echo display_html_content(element('footercontent', element('group', $view)));

?>

	<?php if (element('crawl_attr_update', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_attr_update', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 제품특성 update</a>
			</div>
		<?php } ?>

	<?php if (element('crawl_tag_update', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_tag_update', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 태그 update</a>
			</div>
		<?php } ?>

		<?php if (element('crawl_tag_overwrite', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_tag_overwrite', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 태그 overWrite</a>
			</div>
		<?php } ?>

		
		<?php if (element('crawl_category_update2', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_category_update2', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 카테고리  update</a>
			</div>
		<?php } ?>

		

		<?php if (element('crawl_update', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_update', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 크롤링 update</a>
			</div>
		<?php } ?>


		<?php if (element('crawl_overwrite', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_overwrite', element('group', $view)); ?>" class="btn btn-danger btn-sm">group 크롤링 overWrite</a>
			</div>
		<?php } ?>



<script>

// $('.btn-warning').click(function(){	
// 	if ( ! confirm('정말 실행 하겠습니까?')) 
// 		{ event.preventDefault() ;return false; }
// });
	</script>