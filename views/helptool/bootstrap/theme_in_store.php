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
                                <th>번호</th>
                                <th>스토어명</th>                                
                                <th>이미지</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (element('list', element('data', $view))) {
                            foreach (element('list', element('data', $view)) as $result) {

                        ?>
                            <tr class="<?php echo element('checked', $result) ? 'success':''; ?> ">
                                <td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" <?php echo element('checked',$result) ? 'checked':'';?>/></td>
                                <td><?php echo number_format(element('num', $result)); ?></td>
                                <td><a href="<?php echo board_url(element('brd_key', $result)); ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span> <?php echo html_escape(element('brd_name', $result)); ?></a></td>
                               
                                <td>
                                    <?php if (element('brd_image', $result)) {?>
                                       
                                            <img src="<?php echo thumb_url('board', element('brd_image', $result)); ?>" alt="<?php echo html_escape(element('brd_name', $result)); ?>" title="<?php echo html_escape(element('brd_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
                                        
                                    <?php } ?>
                                </td>
                               
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
        window.opener.location.reload();
    <?php } ?>
    //]]>
    </script>

