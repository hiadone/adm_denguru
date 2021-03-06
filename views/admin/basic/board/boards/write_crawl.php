<div class="box">
    <div class="box-header">
        <h4 class="pb10 pull-left"><?php echo html_escape($this->board->item_id('brd_name', element('brd_id', element('data', $view)))); ?> <a href="<?php echo goto_url(board_url(html_escape($this->board->item_id('brd_key', element('brd_id', element('data', $view)))))); ?>" class="btn-xs" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a></h4>
        <?php if (element('boardlist', $view)) { ?>
        <div class="pull-right">
            <select name="brd_id" class="form-control" onChange="location.href='<?php echo admin_url($this->pagedir . '/write_crawl'); ?>/' + this.value;">
                <?php foreach (element('boardlist', $view) as $key => $value) { ?>
                    <option value="<?php echo element('brd_id', $value); ?>" <?php echo set_select('brd_id', element('brd_id', $value), (element('brd_id', element('data', $view)) == element('brd_id', $value) ? true : false)); ?>><?php echo html_escape(element('brd_name', $value)); ?></option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>
        <div class="clearfix"></div>
        <ul class="nav nav-tabs">
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">기본정보</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/write_crawl/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">메타사이트정보</a></li>
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir . '/write_list/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">목록페이지</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_post/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물열람</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물작성</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_category/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">카테고리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_comment/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">댓글기능</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_general/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">일반기능</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_point/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">포인트기능</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_alarm/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">메일/쪽지/문자</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_rss/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">RSS/사이트맵 설정</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_access/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">권한관리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_extravars/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">사용자정의</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_admin/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시판관리자</a></li>
        </ul>
    </div>
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open(current_full_url(), $attributes);
        ?>
            <input type="hidden" name="is_submit" value="1" />
            <input type="hidden" name="<?php echo element('primary_key', $view); ?>"    value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
            <div class="box-table-header">
                <h4><a data-toggle="collapse" href="#boardtab1" aria-expanded="true" aria-controls="boardtab1">스토어 정보 페이지</a></h4>
                <a data-toggle="collapse" href="#boardtab1" aria-expanded="true" aria-controls="boardtab1"><i class="fa fa-chevron-up pull-right"></i></a>
            </div>
            <div class="collapse in" id="boardtab1">
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 주소 URL</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_url" value="<?php echo set_value('brd_url', element('brd_url', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 상품 KEY</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_goods_key" value="<?php echo set_value('brd_goods_key', element('brd_goods_key', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 크롤링 코멘트</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_crawl_comment" value="<?php echo set_value('brd_crawl_comment', element('brd_crawl_comment', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 주문 URL</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_order_url" value="<?php echo set_value('brd_order_url', element('brd_order_url', element('data', $view))); ?>" />
                        <p class="help-block">"구매해 주셔서 감사합니다." 에 해당되는 url</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 주문현황 URL</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_orderstatus_url" value="<?php echo set_value('brd_orderstatus_url', element('brd_orderstatus_url', element('data', $view))); ?>" />
                        <p class="help-block">주문현황(대기중,배송중,배송완료) 와 운송장 번호가 조회 되는 url</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 주문 key</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_order_key" value="<?php echo set_value('brd_order_key', element('brd_order_key', element('data', $view))); ?>" />
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 url key</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_url_key" value="<?php echo set_value('brd_url_key', element('brd_url_key', element('data', $view))); ?>" />
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 전화번호</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_phone" value="<?php echo set_value('brd_phone', element('brd_phone', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 비회원 주문 조회 페이지</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_nomember_order_url" value="<?php echo set_value('brd_nomember_order_url', element('brd_nomember_order_url', element('data', $view))); ?>" />
                    </div>
                </div>
            </div>

            <div class="box-table-header">
                <h4><a data-toggle="collapse" href="#boardtab2" aria-expanded="true" aria-controls="boardtab1">스토어 회원 정보 관리</a></h4>
                <a data-toggle="collapse" href="#boardtab2" aria-expanded="true" aria-controls="boardtab2"><i class="fa fa-chevron-up pull-right"></i></a>
            </div>
            <div class="collapse in" id="boardtab2">
                <div class="form-group">
                    <label class="col-sm-2 control-label">스토어 회원가입 URL</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_url" value="<?php echo set_value('brd_register_url', element('brd_register_url', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 id 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_id" value="<?php echo set_value('brd_register_id', element('brd_register_id', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 이름 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_name" value="<?php echo set_value('brd_register_name', element('brd_register_name', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 우편번호 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_zipcode" value="<?php echo set_value('brd_register_zipcode', element('brd_register_zipcode', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 주소 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_addr1" value="<?php echo set_value('brd_register_addr1', element('brd_register_addr1', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 상세 주소 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_addr2" value="<?php echo set_value('brd_register_addr2', element('brd_register_addr2', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 hidden 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_hidden" value="<?php echo set_value('brd_register_hidden', element('brd_register_hidden', element('data', $view))); ?>" />
                        <p class="help-block">우편번호를 절차 대로 잘 입력되엇다고 확인 하는 필드명 없으면 무시 </p>
                    </div>
                </div>
               <div class="form-group">
                    <label class="col-sm-2 control-label">회원 전화번호 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_phone" value="<?php echo set_value('brd_register_phone', element('brd_register_phone', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 핸드폰번호 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_handphone" value="<?php echo set_value('brd_register_handphone', element('brd_register_handphone', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 이메일 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_email" value="<?php echo set_value('brd_register_email', element('brd_register_email', element('data', $view))); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 생년월일 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_birthday" value="<?php echo set_value('brd_register_birthday', element('brd_register_birthday', element('data', $view))); ?>" />
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 질문 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_quest" value="<?php echo set_value('brd_register_quest', element('brd_register_quest', element('data', $view))); ?>" />
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">회원 질문 확인 필드</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control " name="brd_register_answer" value="<?php echo set_value('brd_register_answer', element('brd_register_answer', element('data', $view))); ?>" />
                        
                    </div>
                </div>
            </div>

           
            
            <div class="box-table-header">
                <h4><a data-toggle="collapse" href="#boardtab3" aria-expanded="true" aria-controls="boardtab1">스토어 크롤링 관리</a></h4>
                <a data-toggle="collapse" href="#boardtab3" aria-expanded="true" aria-controls="boardtab3"><i class="fa fa-chevron-up pull-right"></i></a>
            </div>
            <div class="collapse in" id="boardtab3">
                <div class="form-group">
                    <label class="col-sm-2 control-label">사이트 크롤링 로직</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="10" name="brd_content"><?php echo set_value('brd_content', element('brd_content', element('data', $view))); ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">사이트 상품 상세 페이지 크롤링 로직</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="10" name="brd_content_detail"><?php echo set_value('brd_content_detail', element('brd_content_detail', element('data', $view))); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="box-table">
                <div class="box-table-header">
                    <h4><a data-toggle="collapse" href="#boardtab2" aria-expanded="true" aria-controls="boardtab2">...</a></h4>
                    <a data-toggle="collapse" href="#boardtab2" aria-expanded="true" aria-controls="boardtab2"><i class="fa fa-chevron-up pull-right"></i></a>
                </div>
                <div class="collapse in" id="boardtab2">
                    
                </div>
            </div>
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a href="<?php echo admin_url($this->pagedir); ?>" class="btn btn-default btn-sm">목록으로</a>
                <button type="submit" class="btn btn-success btn-sm">저장하기</button>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $('#fadminwrite').validate({
        rules: {
            list_count: {required :true, number:true, min:1 },
            mobile_list_count: {required :true, number:true, min:1 },
            page_count: {required :true, number:true, min:1 },
            mobile_page_count: {required :true, number:true, min:1 },
            new_icon_hour: {required :true, number:true },
            mobile_new_icon_hour: {required :true, number:true },
            hot_icon_hit: {required :true, number:true },
            mobile_hot_icon_hit: {required :true, number:true },
            hot_icon_day: {required :true, number:true },
            mobile_hot_icon_day: {required :true, number:true },
            subject_length: {required :true, number:true },
            mobile_subject_length: {required :true, number:true },
            gallery_cols: {required:true, number:true},
            gallery_image_width: {required:true, number:true},
            gallery_image_height: {required:true, number:true},
            mobile_gallery_cols: {required:true, number:true},
            mobile_gallery_image_width: {required:true, number:true},
            mobile_gallery_image_height: {required:true, number:true}
        }
    });
});

var form_original_data = $('#fadminwrite').serialize();
function check_form_changed() {
    if ($('#fadminwrite').serialize() !== form_original_data) {
        if (confirm('저장하지 않은 정보가 있습니다. 저장하지 않은 상태로 이동하시겠습니까?')) {
            return true;
        } else {
            return false;
        }
    }
    return true;
}
//]]>
</script>
