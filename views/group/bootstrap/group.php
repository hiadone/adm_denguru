<?php
defined('BASEPATH') OR exit('No direct script access allowed');

echo display_html_content(element('headercontent', element('group', $view)));

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
<?php if (element('crawl_tag_update', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_tag_update', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 태그 update</a>
			</div>
		<?php } ?>


		<?php if (element('crawl_category_update', element('group', $view))) { ?>
			<div class="pull-right pr10">
				<a href="<?php echo element('crawl_category_update', element('group', $view)); ?>" class="btn btn-warning btn-sm">group 카테고리 및 제품특성 update</a>
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