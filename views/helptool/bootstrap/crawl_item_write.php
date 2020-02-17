<div class="modal-header">
    <h4 class="modal-title">Item 추가</h4>
</div>
<div class="modal-body">
    <?php
    echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
    echo show_alert_message(element('message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
    $attributes = array('class' => 'form-horizontal', 'name' => 'fwrite', 'id' => 'fwrite', 'onsubmit' => 'return submitContents(this)');
    echo form_open_multipart(current_full_url(), $attributes);
    ?>
        <div class="form-group">
            <label for="crawl_title" class="col-xs-2 control-label">제목</label>
            <div class="col-xs-10" style="display:table;">
                <input type="text" class="form-control" name="crawl_title" id="crawl_title" value="<?php echo set_value('crawl_title', element('crawl_title', element('crawl', $view))); ?>" />
                
            </div>
        </div>
        <div class="form-group">
            <label for="crawl_price" class="col-xs-2 control-label">가격</label>
            <div class="col-xs-10" style="display:table;">
                <input type="text" class="form-control" name="crawl_price" id="crawl_price" value="<?php echo set_value('crawl_price', element('crawl_price', element('crawl', $view))); ?>" />
                
            </div>
        </div>
        <div class="form-group">
            <label for="crawl_goods_code" class="col-xs-2 control-label">상품코드</label>
            <div class="col-xs-10" style="display:table;">
                <input type="text" class="form-control" name="crawl_goods_code" id="crawl_goods_code" value="<?php echo set_value('crawl_goods_code', element('crawl_goods_code', element('crawl', $view))); ?>" />
                
            </div>
        </div>
        <div class="form-group">
            <label for="crawl_post_url" class="col-xs-2 control-label">상품 URL</label>
            <div class="col-xs-10" style="display:table;">
                <input type="text" class="form-control" name="crawl_post_url" id="crawl_post_url" value="<?php echo set_value('crawl_post_url', element('crawl_post_url', element('crawl', $view))); ?>" />
                
            </div>
        </div>
        <?php
        
            $file_count = 1;
            for ($i = 0; $i < $file_count; $i++) {
                $download_link = html_escape(element('download_link', element($i, element('file', $view))));
                $file_column = $download_link ? 'crawl_file_update[' . element('cfi_id', element($i, element('file', $view))) . ']' : 'crawl_file[' . $i . ']';
                $del_column = $download_link ? 'crawl_file_del[' . element('cfi_id', element($i, element('file', $view))) . ']' : '';
        ?>
            <div class="form-group">
                <label for="<?php echo $file_column; ?>" class="col-xs-2 control-label">파일 #<?php echo $i+1; ?></label>
                <div class="col-xs-10">
                    <input type="file" class="form-control" name="<?php echo $file_column; ?>" id="<?php echo $file_column; ?>" />
                        <label for="<?php echo $del_column; ?>">
                            <input type="checkbox" name="<?php echo $del_column; ?>" id="<?php echo $del_column; ?>" value="1" <?php echo set_checkbox($del_column, '1'); ?> /> 삭제
                        </label>
                        <?php if($download_link){ ?>
                        <img src="<?php echo thumb_url('crawl',element('cfi_filename', element($i, element('file', $view)))) ?>" alt="이미지" title="이미지" />
                        <?php } ?>
                    
                </div>
            </div>
        <?php
            }        
        ?>
        <input type="hidden" name="is_submit" value="1" />
        
        <div class="pull-right" style="margin:20px;">
            
            <button class="btn btn-primary" type="submit">적용하기</button>
            <button class="btn btn-default" onClick="window.close();">닫기</button>
        </div>
    <?php echo form_close(); ?>
</div>
