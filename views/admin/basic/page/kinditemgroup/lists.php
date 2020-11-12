<div class="box">
    <div class="box-table">
        <?php
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-inline', 'name' => 'flist', 'id' => 'flist');
        echo form_open(current_full_url(), $attributes);
        ?>
            <div class="box-table-header">
                <!-- <div class="btn-group btn-group-sm" role="group">
                    <a href="?" class="btn btn-sm <?php echo ($this->input->get('eve_activated') !== 'Y' && $this->input->get('eve_activated') !== 'N') ? 'btn-success' : 'btn-default'; ?>">전체itme</a>
                    <a href="?eve_activated=Y" class="btn btn-sm <?php echo ($this->input->get('eve_activated') === 'Y') ? 'btn-success' : 'btn-default'; ?>">활성</a>
                    <a href="?eve_activated=N" class="btn btn-sm <?php echo ($this->input->get('eve_activated') === 'N') ? 'btn-success' : 'btn-default'; ?>">비활성</a>
                </div> -->
                <?php
                ob_start();
                ?>
                    <div class="btn-group pull-right" role="group" aria-label="...">
                        <a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">전체 목록</a>
                        <button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button>
                        <button type="button" class="btn btn-danger btn-sm admin-manage-list" onClick="kinditem_in_cmall_item(<?php echo element('kig_id', $view); ?>);" >종속 item 추가</button>
                        
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
                            <th>번호</a></th>
                            <th>이미지</th>
                            <th><a href="<?php echo element('cit_name', element('sort', $view)); ?>">상품명</a></th>
                            <!-- <th><a href="<?php echo element('eve_device', element('sort', $view)); ?>">접속기기</a></th> -->
                            <th><a href="<?php echo element('kir_start_date', element('sort', $view)); ?>">시작일시</a></th>
                            <th><a href="<?php echo element('kir_end_date', element('sort', $view)); ?>">종료일시</a></th>
                            <!-- <th>시간</th>
                            <th>가운데정렬</th> -->
                            <th class="px100"><a href="<?php echo element('kir_order', element('sort', $view)); ?>">정렬순서</a></th>
                            <th>action</th>
                            <th><input type="checkbox" name="chkall" id="chkall" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (element('list', element('data', $view))) {
                        foreach (element('list', element('data', $view)) as $result) {
                    ?>
                        <tr>
                            <td><?php echo number_format(element('num', $result)); ?></td>
                            <td><?php if (element('cit_file_1', $result)) {?><img src="<?php echo cdn_url('cmallitem', element('cit_file_1', $result)); ?>" alt="<?php echo html_escape(element('cit_name', $result)); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" /><?php } ?></td>
                            <td><?php echo html_escape(element('cit_name', $result)); ?></td>
                            <!-- <td class="text-center"><?php echo element('eve_device', $result); ?></td> -->
                            <td><?php echo element('kir_start_date', $result); ?></td>
                            <td><?php echo element('kir_end_date', $result); ?></td>
                            <!-- <td class="text-center"><?php echo element('eve_disable_hours', $result); ?></td>
                            <td><?php echo element('eve_is_center', $result) ? '가운데정렬' : ''; ?></td> -->
                            <td><?php echo element('kir_order', $result); ?></td>                            
                            
                            <td><a href="<?php echo admin_url($this->pagedir); ?>/listswrite/<?php echo element('kir_id', $result); ?>?<?php echo $this->input->server('QUERY_STRING', null, ''); ?>" class="btn btn-outline btn-default btn-xs">수정</a></td>
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
