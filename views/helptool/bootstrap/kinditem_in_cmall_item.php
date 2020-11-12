<div class="modal-body">
    <div class="box">
        <form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
            <div class="box-search">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        
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
                <div class="row">전체 : <?php echo element('total_rows', element('data', $view), 0); ?>건</div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="chkall" id="chkall" /></th>
                                <th>상품코드</th>
                                <th>카테고리</th>
                                <th>이미지</th>
                                <th><a href="<?php echo element('cit_name', element('sort', $view)); ?>">상품명</a></th>
                                <th><a href="<?php echo element('cit_price', element('sort', $view)); ?>">판매가격</a></th>
                                <!-- <th>Vision API label</th> -->
                                
                                <th><a href="<?php echo element('cit_price_sale', element('sort', $view)); ?>">할인가격</a></th>
                                
                                <th><a href="<?php echo element('cit_sell_count', element('sort', $view)); ?>">판매량</a></th>
                                <th><a href="<?php echo element('cit_hit', element('sort', $view)); ?>">조회수</a></th>
                                <th>스크랩</th>
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (element('list', element('data', $view))) {
                            foreach (element('list', element('data', $view)) as $result) {

                        ?>
                            <tr class="<?php echo element('checked', $result) ? 'success':''; ?> ">
                                <td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" <?php echo element('checked',$result) ? 'checked':'';?>/></td>
                                <td><a href="<?php echo post_url('',element('post_id', $result)); ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span> <?php echo html_escape(element('cit_key', $result)); ?></a></td>
                                <td style="width:130px;">
                                    <?php foreach (element('category', $result) as $cv) { echo '<label class="label label-info">' . html_escape(element('cca_value', $cv)) . '</label> ';} ?>
                                    <?php if (element('cit_type1', $result)) { ?><label class="label label-danger">추천</label> <?php } ?>
                                    <?php if (element('cit_type2', $result)) { ?><label class="label label-warning">인기</label> <?php } ?>
                                    <?php if (element('cit_type3', $result)) { ?><label class="label label-default">신상품</label> <?php } ?>
                                    <?php if (element('cit_type4', $result)) { ?><label class="label label-primary">할인</label> <?php } ?>
                                </td>
                                <td>
                                    <?php if (element('cit_file_1', $result)) {?>
                                        <a href="<?php echo element('cit_post_url', $result); ?>" target="_blank">
                                            <img src="<?php echo thumb_url('cmallitem', element('cit_file_1', $result)); ?>" alt="<?php echo html_escape(element('cit_name', $result)); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
                                        </a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php 
                                    
                                        echo '<div><label class="label label-default">'.element('brd_name', $result).'</label></div>';
                                    ?>
                                    <?php echo html_escape(element('cit_name', $result)); ?>                                    
                                </td>
                                <td><?php echo html_escape(element('cit_price', $result)); ?></td>
                                
                                <!-- <td>
                                   <textarea name="vision_api_label[<?php echo element('cit_id', $result); ?>]" id="val_tag_<?php echo element('cit_id', $result); ?>" data-cit_id="<?php echo element('cit_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="선택 옵션 (엔터로 구분하여 입력)"><?php echo html_escape(element('display_label', $result)); ?></textarea>
                                </td> -->
                               
                                
                                <td><?php echo html_escape(element('cit_price_sale', $result)); ?></td>
                                
                                <td class="text-right"><?php echo number_format(element('cit_sell_count', $result)); ?></td>
                                <td class="text-right"><?php echo number_format(element('cit_hit', $result)); ?></td>
                                <td class="text-right"><?php echo number_format(element('cmall_wishlist_count', $result)); ?></td>
                                
                                
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
</div>
    <script type="text/javascript">
    //<![CDATA[


  

    <?php if($this->session->flashdata('message')){ ?>
        // window.opener.location.reload();
    <?php } ?>
    //]]>
    </script>

