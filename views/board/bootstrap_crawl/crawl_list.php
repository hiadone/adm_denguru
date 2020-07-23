<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<?php echo element('headercontent', element('board', element('list', $view))); ?>

<div class="board">
    
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
            
        </div>
        <div class="col-md-6">
            <div class=" searchbox">
                <form class="navbar-form navbar-right pull-right" action="<?php echo site_url(uri_string()); ?>" onSubmit="return postSearch(this);">
                    <input type="hidden" name="findex" value="<?php echo html_escape($this->input->get('findex')); ?>" />
                    <input type="hidden" name="category_id" value="<?php echo html_escape($this->input->get('category_id')); ?>" />
                    <div class="form-group">
                        <select class="form-control pull-left px100" name="sfield">                            
                            <option value="cit_name" <?php echo ($this->input->get('sfield') === 'cit_name') ? ' selected="selected" ' : ''; ?>>제목</option>
                            
                            
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
            if (skeyword.length < 2) {
                alert('2글자 이상으로 검색해 주세요');
                f.skeyword.focus();
                return false;
            }
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
    ?>
        <ul class="nav nav-tabs clearfix">
            <li role="presentation" <?php if ( ! $this->input->get('category_id')) { ?>class="active" <?php } ?>><a href="<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>?findex=<?php echo html_escape($this->input->get('findex')); ?>&category_id=">전체</a></li>
            <?php
            if (element(0, $category)) {
                foreach (element(0, $category) as $ckey => $cval) {
            ?>
                <li role="presentation" <?php if ($this->input->get('category_id') === element('bca_key', $cval)) { ?>class="active" <?php } ?>><a href="<?php echo board_url(element('brd_key', element('board', element('list', $view)))); ?>?findex=<?php echo html_escape($this->input->get('findex')); ?>&category_id=<?php echo element('bca_key', $cval); ?>"><?php echo html_escape(element('bca_value', $cval)); ?></a></li>
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
    <div class="row">
        <div>전체 : <?php echo element('total_rows', element('data', element('list', $view))); ?>건</div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <?php if (element('is_admin', $view)) { ?><th><input onclick="if (this.checked) all_boardlist_checked(true); else all_boardlist_checked(false);" type="checkbox" /></th><?php } ?>
                    <th>번호</th>
                    <th>IMG</th>
                    <th>제목</th>
                    <th>카테고리</th>
                    <th>상품코드</th>
                    <th>가격</th>
                    <th>제품특성</th>
                    <th>Vision API label</th>
                    <th>태그</th>
                    <th>날짜</th>
                    <th>판매여부</th>
                    <th>판매량</th>
                    <th>조회수</th>
                    <th>스크랩</th>
                    <th>action</th>
                </tr>
            </thead>
            
            <tbody>
            <?php
            
            if (element('list', element('data', element('list', $view)))) {
                foreach (element('list', element('data', element('list', $view))) as $result) {
            ?>
                <tr class="<?php echo element('warning', $result) ? 'warning':''; ?> ">
                    <?php if (element('is_admin', $view)) { ?><th scope="row" class="text-center"><input type="checkbox" name="chk_post_id[]" value="<?php echo element('cit_id', $result); ?>" /></th><?php } ?>
                    <td ><?php echo element('num', $result); ?></td>
                    <td>
                        <a href="<?php echo element('cit_post_url', $result); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" target="_blank"><img src="<?php echo element('origin_image_url', $result); ?>" alt="<?php echo html_escape(element('cit_name', $result)); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" target="_blank" class="thumbnail img-responsive" style="width:<?php echo element('gallery_image_width', element('board', $view)); ?>px;height:<?php echo element('gallery_image_height', element('board', $view)); ?>px;" /></a>
                    </td>
                    <td class="px300">
                        <?php if (element('category', $result)) { ?><a href="<?php echo board_url(element('brd_key', element('board', element('crawl', $view)))); ?>?category_id=<?php echo html_escape(element('bca_key', element('category', $result))); ?>"><span class="label label-default"><?php echo html_escape(element('bca_value', element('category', $result))); ?></span></a><?php } ?>
                        <?php if (element('post_reply', $result)) { ?><span class="label label-primary" style="margin-left:<?php echo strlen(element('post_reply', $result)) * 10; ?>px">Re</span><?php } ?>
                        <a href="<?php echo element('cit_link', $result); ?>" style="
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
                        " title="<?php echo html_escape(element('cit_name', $result)); ?>" target="_blank"><?php echo html_escape(element('cit_name', $result)); ?></a>
                        
                        <?php if (element('is_hot', $result)) { ?><span class="label label-danger">Hot</span><?php } ?>
                        <?php if (element('is_new', $result)) { ?><span class="label label-warning">New</span><?php } ?>
                        <?php if (element('ppo_id', $result)) { ?><i class="fa fa-bar-chart"></i><?php } ?>
                        <?php if (element('post_comment_count', $result)) { ?><span class="label label-warning">+<?php echo element('post_comment_count', $result); ?></span><?php } ?>
                        <?php 
                            if(element('display_brand', $result))
                                echo '<div>브랜드 : <label class="label label-default">'.element('display_brand', $result).'</label></div>';
                        ?>
                        <?php 
                        if(element('cit_summary', $result)){
                            echo '<div><sub>'.element('cit_summary', $result). '</sub></div>';
                        }    
                        ?>
                        
                    </td>
                    <td style="width:130px;">
                                <?php foreach (element('category', $result) as $cv) { echo '<label class="label label-info">' . html_escape(element('cca_value', $cv)) . '</label> ';} ?>
                                <?php if (element('cit_type1', $result)) { ?><label class="label label-danger">추천</label> <?php } ?>
                                <?php if (element('cit_type2', $result)) { ?><label class="label label-warning">인기</label> <?php } ?>
                                <?php if (element('cit_type3', $result)) { ?><label class="label label-default">신상품</label> <?php } ?>
                                <?php if (element('cit_type4', $result)) { ?><label class="label label-primary">할인</label> <?php } ?>
                            </td>
                    <td ><?php echo element('cit_goods_code', $result); ?></td>
                    <td ><?php echo number_format(element('display_price', $result)); ?>
                        
                        <?php 
                            if(element('cit_is_soldout', $result))
                                echo '<div><button class="btn btn-danger btn-xs" type="button">Sold out</button></div>';
                        ?>
                        
                        
                    </td>
                    <td style="width:130px;">
                        <?php foreach (element('attr', $result) as $cv) { echo '<label class="label label-primary">' . html_escape(element('cat_value', $cv)) . '</label> ';} ?>
                    </td>
                    <td>
                        <textarea name="vision_api_label[<?php echo element('cit_id', $result); ?>]" id="val_tag_<?php echo element('cit_id', $result); ?>" data-cit_id="<?php echo element('cit_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="이미지 분석 라벨입니다(수정 불가)"><?php echo html_escape(element('display_label', $result)); ?></textarea>
                        </td>
                    <td>
                        <textarea name="cta_tag[<?php echo element('cit_id', $result); ?>]" id="cta_tag_<?php echo element('cit_id', $result); ?>" data-cit_id="<?php echo element('cit_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="선택 옵션 (엔터로 구분하여 입력)"><?php echo html_escape(element('display_tag', $result)); ?></textarea>
                        </td>
                    <td><?php echo element('display_datetime', $result); ?></td>
                    <td><a href="javascript:post_action_crawl('cit_status', '<?php echo element('cit_id', $result);?>', '<?php echo empty(element('cit_status', $result)) ? '1':'0';?>',0);" class="btn <?php echo empty(element('cit_status', $result)) ? 'btn-primary':'btn-warning';?> btn-xs"><?php echo empty(element('cit_status', $result)) ? 'disable' : 'enable'; ?></a></td>
                    <td class="text-right"><?php echo number_format(element('cit_sell_count', $result)); ?></td>
                    <td class="text-right"><?php echo number_format(element('cit_hit', $result)); ?></td>
                    <td class="text-right"><?php echo number_format(element('cmall_wishlist_count', $result)); ?></td>
                    <td><a href="<?php echo admin_url('cmall/cmallitem/write/' . element('cit_id', $result)); ?>" target="_blank" class="btn-sm btn-xs btn-default">수정</a>
                    <br>
                    <br>
                    <a href="<?php echo admin_url('cmall/crawlitem?sfield=brd_id2&skeyword=' . element('brd_id', $result).'&crw_goods_code='.element('cit_goods_code', $result)); ?>" target="_blank" class="btn-sm btn-xs btn-info">원본</a>
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
    </div>
    <?php echo form_close(); ?>

    <div class="border_button">
        <div class="pull-left mr10">
            <a href="<?php echo element('list_url', element('list', $view)); ?>" class="btn btn-default btn-sm">전체목록</a>
            <?php if (element('search_list_url', element('list', $view))) { ?>
                <a href="<?php echo element('search_list_url', element('list', $view)); ?>" class="btn btn-default btn-sm">검색목록</a>
            <?php } ?>
        </div>
        <?php if (element('is_admin', $view)) { ?>
            <div class="pull-left">
                <div class="btn btn-danger btn-sm" onClick="multi_crawling_item_update('item', 'vision_api_label', '선택하신 항목을 vision_api_label update ?');"><i class="fa fa-trash-o"></i>item vision_api_label update</div>
                    <div class="btn btn-danger btn-sm" onClick="multi_crawling_item_update('item', 'tag_overwrite', '선택하신 항목을 item tag overwrite ?');"><i class="fa fa-trash-o"></i>item tag overwrite</div>
                    <div class="btn btn-danger btn-sm" onClick="multi_crawling_item_update('item', 'tag_update', '선택하신 항목을 item tag update ?');"><i class="fa fa-trash-o"></i>item tag update</div>
                    
                    <div class="btn btn-danger btn-sm" onClick="multi_crawling_item_update('item', 'category_update', '선택하신 항목을 item category update ?');"><i class="fa fa-trash-o"></i>item category update</div>
                    <div class="btn btn-danger btn-sm" onClick="post_multi_action('cit_multi_delete', '0', '선택하신 항목을 완전삭제하시겠습니까?');"><i class="fa fa-trash-o"></i> 선택삭제하기</div>
                    <a href="<?php echo element('list_url', element('list', $view)); ?>?warning=1" class="btn btn-warning btn-sm">warning 목록</a>
                    <div class="btn btn-primary btn-sm" onClick="post_multi_action('cit_multi_status', '0', '선택하신 글들을 블라인드 해제 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i> 블라인드해제</div>
                    <div class="btn btn-primary btn-sm" onClick="post_multi_action('cit_multi_status', '1', '선택하신 글들을 블라인드 처리 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i> 블라인드처리</div>
            </div>

            <div class="pull-right">
                    <div class="" ><a href="<?php echo admin_url('cmall/cmallitem/write/') ?>" target="_blank" class="btn btn-success btn-sm">Item 추가</a></div>
            </div>
        <?php } ?>
        <?php if (element('write_url', element('list', $view))) { ?>
            <div class="pull-right">
                <a href="<?php echo element('write_url', element('list', $view)); ?>" class="btn btn-success btn-sm">글쓰기</a>
            </div>
        <?php } ?>
    </div>
    <nav><?php echo element('paging', element('list', $view)); ?></nav>
</div>

<?php echo element('footercontent', element('board', element('list', $view))); ?>



<script type="text/javascript">
//<![CDATA[


$(document).on('change', 'textarea[name^=cta_tag]', function() {


    post_action_crawl('cta_tag_update', $(this).data('cit_id'),'cta_tag_');
});


$(document).on('change', 'textarea[name^=cit_color]', function() {


    post_action_crawl('cit_color_update', $(this).data('cit_id'),'cit_color_');
});



//]]>
</script>


<?php
if (element('highlight_keyword', element('list', $view))) {
    $this->managelayout->add_js(base_url('assets/js/jquery.highlight.js')); ?>
<script type="text/javascript">
//<![CDATA[
$('#fboardlist').highlight([<?php echo element('highlight_keyword', element('list', $view));?>]);
//]]>
</script>
<?php } ?>
