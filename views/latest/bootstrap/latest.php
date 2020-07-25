<div class="col-md-6">
	<div class="panel panel-default">
	<!-- Default panel contents -->
		<div class="panel-heading">
			<?php echo html_escape(element('board_name', element('board', $view))); ?>
			<button class="btn btn-info btn-xs">총 상품 <?php echo number_format(element('cmall_count',$view)); ?> 개</button>
			<button class="btn btn-warning btn-xs">총 warning 상품 <?php echo number_format(element('warning_count',$view)); ?> 개</button>
			<button class="btn btn-warning btn-xs">카테고리 없는 상품 <?php echo number_format(element('cmall_count',$view) -element('notcategory_count',$view)); ?> 개</button>
			
			<div class="view-all pull-right">
				<a href="<?php echo board_url(element('brd_key', element('board', $view))); ?>" title="<?php echo html_escape(element('board_name', element('board', $view))); ?>">더보기 <i class="fa fa-angle-right"></i></a>
			</div>
		</div>

		<!-- Table -->
		<div class="table-responsive">
			<table class="table table-hover">
				<tbody>
				<?php
				$i = 0;
				if (element('latest', $view)) {
					foreach (element('latest', $view) as $key => $value) {
				?>
					<tr>
						<td><a href="<?php echo element('url', $value); ?>" title="<?php echo html_escape(element('title', $value)); ?>"><?php echo html_escape(element('title', $value)); ?></a>
							<?php if (element('post_comment_count', $value)) { ?> <span class="latest_comment_count"> +<?php echo element('post_comment_count', $value); ?></span><?php } ?>
						</td>
						<td class="px80"><?php echo element('display_datetime', $value); ?></td>
					</tr>
				<?php
						$i++;
					}
				}
				while ($i < element('latest_limit', $view)) {
				?>
					<tr>
						<td>게시물이 없습니다</td>
						<td class="px80"></td>
					</tr>
				<?php
						$i++;
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
