<div class="box">
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open_multipart(current_full_url(), $attributes);
        ?>
            <input type="hidden" name="<?php echo element('primary_key', $view); ?>"    value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">이미지 업로드</label>
                    <div class="col-sm-10">
                        <?php
                        if (element('eve_image', element('data', $view))) {
                        ?>
                            <img src="<?php echo event_image_url(element('eve_image', element('data', $view)), '', 150); ?>" alt="배너 이미지" title="배너 이미지" />
                            <label for="eve_image_del">
                                <input type="checkbox" name="eve_image_del" id="eve_image_del" value="1" <?php echo set_checkbox('eve_image_del', '1'); ?> /> 삭제
                            </label>
                        <?php
                        }
                        ?>
                        <input type="file" name="eve_image" id="eve_image" />
                        <p class="help-block">gif, jpg, png 파일 업로드가 가능합니다</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">제목</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="eve_title" value="<?php echo set_value('eve_title', element('eve_title', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">시작일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="eve_start_date" value="<?php echo set_value('eve_start_date', element('eve_start_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">종료일</label>
                    <div class="col-sm-10 form-inline">
                        <input type="text" class="form-control datepicker" name="eve_end_date" value="<?php echo set_value('eve_end_date', element('eve_end_date', element('data', $view))); ?>" />
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트정렬</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="eve_is_center_1">
                            <input type="radio" name="eve_is_center" id="eve_is_center_1" value="1" <?php echo set_radio('eve_is_center', '1', (element('eve_is_center', element('data', $view)) === '1' ? true : false)); ?> /> 가운데정렬
                        </label>
                        <label class="radio-inline" for="eve_is_center_0">
                            <input type="radio" name="eve_is_center" id="eve_is_center_0" value="0" <?php echo set_radio('eve_is_center', '0', (element('eve_is_center', element('data', $view)) !== '1' ? true : false)); ?> /> 좌측정렬
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">좌측위치</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_left" value="<?php echo set_value('eve_left', element('eve_left', element('data', $view))); ?>" />px - 좌측정렬시만 해당
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">상단위치</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_top" value="<?php echo set_value('eve_top', element('eve_top', element('data', $view))); ?>" />px
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트길이</label>
                    <div class="col-sm-10">
                        가로 <input type="number" class="form-control" name="eve_width" value="<?php echo set_value('eve_width', element('eve_width', element('data', $view))); ?>" />px,
                        세로 <input type="number" class="form-control" name="eve_height" value="<?php echo set_value('eve_height', element('eve_height', element('data', $view))); ?>" />px
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트표시기기</label>
                    <div class="col-sm-10 form-inline">
                        <select class="form-control" name="eve_device">
                            <option value="all" <?php echo set_select('eve_device', 'all', (element('eve_device', element('data', $view)) === 'all' ? true : false)); ?>>모든기기</option>
                            <option value="pc" <?php echo set_select('eve_device', 'pc', (element('eve_device', element('data', $view)) === 'pc' ? true : false)); ?>>PC만</option>
                            <option value="mobile" <?php echo set_select('eve_device', 'mobile', (element('eve_device', element('data', $view)) === 'mobile' ? true : false)); ?>>모바일만</option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트이뜨는페이지</label>
                    <div class="col-sm-10 form-inline">
                        <select class="form-control" name="eve_page">
                            <option value="0" <?php echo set_select('eve_page', '0', (element('eve_page', element('data', $view)) !== '1' ? true : false)); ?>>홈페이지에서만</option>
                            <option value="1" <?php echo set_select('eve_page', '1', (element('eve_page', element('data', $view)) === '1' ? true : false)); ?>>모든페이지에서</option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">시간</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_disable_hours" value="<?php echo set_value('eve_disable_hours', element('eve_disable_hours', element('data', $view))); ?>" /> 시간, 닫기 버튼 클릭시 쿠키적용시간, 해당 시간동안 이벤트이 더이상 보이지 않습니다
                    </div>
                </div> -->
                <div class="form-group">
                    <label class="col-sm-2 control-label">정렬순서</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="eve_order" value="<?php echo set_value('eve_order', element('eve_order', element('data', $view)) + 0); ?>" />
                        <div class="help-inline">정렬 순서가 작은 값이 먼저 출력됩니다</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">이벤트활성화</label>
                    <div class="col-sm-10">
                        <label class="radio-inline" for="eve_activated_1">
                            <input type="radio" name="eve_activated" id="eve_activated_1" value="1" <?php echo set_radio('eve_activated', '1', (element('eve_activated', element('data', $view)) === '1' ? true : false)); ?> /> 활성
                        </label>
                        <label class="radio-inline" for="eve_activated_0">
                            <input type="radio" name="eve_activated" id="eve_activated_0" value="0" <?php echo set_radio('eve_activated', '0', (element('eve_activated', element('data', $view)) !== '1' ? true : false)); ?> /> 비활성
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">내용</label>
                    <div class="col-sm-10">
                        <?php echo display_dhtml_editor('eve_content', set_value('eve_content', element('eve_content', element('data', $view))), $classname = 'form-control dhtmleditor', $is_dhtml_editor = $this->cbconfig->item('use_popup_dhtml'), $editor_type = $this->cbconfig->item('popup_editor_type')); ?>
                    </div>
                </div>
                

                <div class="pull-left mr10">
                    <button type="button" class="btn btn-default btn-sm admin-manage-list" onClick="event_in_cmall_item(<?php echo element(element('primary_key', $view), element('data', $view)); ?>);" >이벤트에 상품 종속 시키기</button>
                </div>

                <div class="btn-group pull-right" role="group" aria-label="...">
                    <button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
                    <button type="submit" class="btn btn-success btn-sm">저장하기</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal-body">
    <div class="box">
      <!--   <form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
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
        </form> -->
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
                                <th><a href="<?php echo element('cit_status', element('sort', $view)); ?>">판매여부</a></th>
                                <th><a href="<?php echo element('cit_sell_count', element('sort', $view)); ?>">판매량</a></th>
                                <th><a href="<?php echo element('cit_hit', element('sort', $view)); ?>">조회수</a></th>
                                
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (element('list', element('cdata', $view))) {
                            foreach (element('list', element('cdata', $view)) as $result) {

                        ?>
                            <tr >
                                <td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element('cit_id', $result); ?>" /></td>
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
                                            <img src="<?php echo cdn_url('cmallitem', element('cit_file_1', $result)); ?>" alt="<?php echo html_escape(element('cit_name', $result)); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
                                        </a>
                                    <?php } ?>
                                </td>
                                <td><?php echo html_escape(element('cit_name', $result)); ?></td>
                                <td><?php echo html_escape(element('cit_price', $result)); ?></td>
                                
                                <!-- <td>
                                   <textarea name="vision_api_label[<?php echo element('cit_id', $result); ?>]" id="val_tag_<?php echo element('cit_id', $result); ?>" data-cit_id="<?php echo element('cit_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="선택 옵션 (엔터로 구분하여 입력)"><?php echo html_escape(element('display_label', $result)); ?></textarea>
                                </td> -->
                               
                                
                                <td><?php echo html_escape(element('cit_price_sale', $result)); ?></td>
                                <td> <?php echo element('cit_status', $result) ? '판매중' : '삭제처리' ?></td>
                                <td class="text-right"><?php echo number_format(element('cit_sell_count', $result)); ?></td>
                                <td class="text-right"><?php echo number_format(element('cit_hit', $result)); ?></td>
                                
                                
                                
                            </tr>
                        <?php
                            }
                        }
                        if ( ! element('list', element('cdata', $view))) {
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
       <!--  <form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
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
        </form> -->
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $('#fadminwrite').validate({
        rules: {
            eve_title: 'required',
            eve_start_date: { alpha_dash:true, minlength:10, maxlength:10 },
            eve_end_date: { alpha_dash:true, minlength:10, maxlength:10 },
            // eve_is_center: { required:true, number:true },
            // eve_left: { required :'#eve_is_center_1:checked', number:true },
            // eve_top: { required:true, number:true },
            // eve_width: { required:true, number:true },
            // eve_height: { required:true, number:true },
            // eve_device: 'required',
            // eve_page: 'required',
            // eve_disable_hours: { required:true, number:true },
            eve_activated: 'required',
            eve_order: { number:true },
            eve_content : {<?php echo ($this->cbconfig->item('use_popup_dhtml')) ? 'required_' . $this->cbconfig->item('popup_editor_type') : 'required'; ?> : true }
        }
    });
});
//]]>
</script>
