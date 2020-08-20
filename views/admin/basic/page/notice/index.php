<div class="box">
    <div class="box-table">
        <?php
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-inline', 'name' => 'flist', 'id' => 'flist');
        echo form_open(current_full_url(), $attributes);
        ?>
            <div class="box-table-header">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="?" class="btn btn-sm <?php echo ($this->input->get('noti_activated') !== 'Y' && $this->input->get('noti_activated') !== 'N') ? 'btn-success' : 'btn-default'; ?>">전체공지</a>
                    <a href="?noti_activated=Y" class="btn btn-sm <?php echo ($this->input->get('noti_activated') === 'Y') ? 'btn-success' : 'btn-default'; ?>">활성</a>
                    <a href="?noti_activated=N" class="btn btn-sm <?php echo ($this->input->get('noti_activated') === 'N') ? 'btn-success' : 'btn-default'; ?>">비활성</a>
                </div>
                <?php
                ob_start();
                ?>
                    <div class="btn-group pull-right" role="group" aria-label="...">
                        <a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">전체목록</a>
                        <button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button>
                        <a href="<?php echo element('write_url', $view); ?>" class="btn btn-outline btn-danger btn-sm">공지추가</a>
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
                            <th class="px80"><a href="<?php echo element('noti_id', element('sort', $view)); ?>">번호</a></th>
                            <th><a href="<?php echo element('noti_title', element('sort', $view)); ?>">제목</a></th>
                            <th>이미지</th>
                            <!-- <th><a href="<?php echo element('noti_device', element('sort', $view)); ?>">접속기기</a></th> -->
                            <th class="px150"><a href="<?php echo element('noti_start_date', element('sort', $view)); ?>">시작일시</a></th>
                            <th class="px150"><a href="<?php echo element('noti_end_date', element('sort', $view)); ?>">종료일시</a></th>
                            <!-- <th>시간</th>
                            <th>가운데정렬</th> -->
                            <th class="px100"><a href="<?php echo element('noti_order', element('sort', $view)); ?>">정렬순서</a></th>
                            <th class="px100"><a href="<?php echo element('noti_activated', element('sort', $view)); ?>">활성여부</a></th>
                            <th class="px100">알림발송</th>
                            <th class="px100">수정</th>
                            <th class="px50"><input type="checkbox" name="chkall" id="chkall" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (element('list', element('data', $view))) {
                        foreach (element('list', element('data', $view)) as $result) {
                    ?>
                        <tr>
                            <td><?php echo number_format(element('num', $result)); ?></td>
                            <td><?php echo html_escape(element('noti_title', $result)); ?></td>
                            <td><?php if (element('cdn_url', $result)) {?><img src="<?php echo element('cdn_url', $result); ?>" alt="<?php echo html_escape(element('noti_title', $result)); ?>" title="<?php echo html_escape(element('noti_title', $result)); ?>" class="thumbnail mg0" style="width:80px;" /><?php } ?></td>
                            <!-- <td class="text-center"><?php echo element('noti_device', $result); ?></td> -->
                            <td><?php echo element('noti_start_date', $result); ?></td>
                            <td><?php echo element('noti_end_date', $result); ?></td>
                            <!-- <td class="text-center"><?php echo element('noti_disable_hours', $result); ?></td>
                            <td><?php echo element('noti_is_center', $result) ? '가운데정렬' : ''; ?></td> -->
                            <td><?php echo element('noti_order', $result); ?></td>
                            <td><?php echo element('noti_activated', $result) ? '<button type="button" class="btn btn-xs btn-primary">활성</button>' : '<button type="button" class="btn btn-xs btn-info">비활성</button>'; ?></td>
                            <td><a href="<?php echo admin_url($this->pagedir); ?>/notification_send/<?php echo element(element('primary_key', $view), $result); ?>" class="btn btn-outline btn-danger btn-xs">발송</a></td>
                            <td><a href="<?php echo admin_url($this->pagedir); ?>/write/<?php echo element(element('primary_key', $view), $result); ?>?<?php echo $this->input->server('QUERY_STRING', null, ''); ?>" class="btn btn-outline btn-default btn-xs">수정</a></td>
                            <td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" /></td>
                        </tr>
                    <?php
                        }
                    }
                    if ( ! element('list', element('data', $view))) {
                    ?>
                        <tr>
                            <td colspan="10" class="nopost">자료가 없습니다</td>
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
                    <select class="form-control" name="sfield" >
                        <?php echo element('search_option', $view); ?>
                    </select>
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
