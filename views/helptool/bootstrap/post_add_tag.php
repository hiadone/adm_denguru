<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="modal-header">
    <h4 class="modal-title"><?php echo element('page_title',  $layout); ?></h4>
</div>
<div class="modal-body">
    <div class="box">

        <div class="box-table">
            <?php
            echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
            echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
            echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                <input type="hidden" name="is_submit" value="1" />
                
                <input type="hidden" name="post_id_list" value="<?php echo element('post_id_list', $view); ?>" />
                <input type="hidden" name="cit_id_list" value="<?php echo element('cit_id_list', $view); ?>" />
                <input type="hidden" name="<?php echo element('primary_key', $view); ?>"    value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
                <div>                
                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Vision API label</label>
                                        <div class="col-sm-4">
                                            <textarea class="form-control" name="val_tag" id="val_tag" rows="5"><?php echo set_value('val_tag', element('val_tag', element('data', $view))); ?></textarea>
                                            <div class="help-block">이미지 분석 라벨입니다(수정 불가)</div>
                                        </div>
                                        <label class="col-sm-2 control-label">태그</label>
                                        <div class="col-sm-4">
                                            <textarea class="form-control" name="cta_tag1" id="cta_tag1" rows="5"><?php echo set_value('cta_tag1', element('cta_tag1', element('data', $view))); ?></textarea>
                                            <div class="help-block">태그 입력(엔터로 구분하여 입력)</div>
                                        </div>
                                    </div>
                    <div class="btn-group pull-right" role="group" aria-label="...">                    
                        <button type="submit" class="btn btn-success btn-sm">저장하기</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[


var searchSource = [
    <?php
    if (element('brand_list', element('data', $view))) {
        foreach (element('brand_list', element('data', $view)) as $result) {
            echo '"'.element('cbr_value_kr',$result).'","'.element('cbr_value_en',$result).'",';
        }
    }
            
    ?>
    "========" 
]; // 배열 형태로 
$("#brd_brand_text")
.on("keydown", function( event ) {
    if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
        event.preventDefault();
    }
})
.autocomplete({  //오토 컴플릿트 시작
    source : searchSource,    // source 는 자동 완성 대상
    select: function(event, ui) {
        this.value = "";
        this.value = ui.item.value;

        return false;
    },
    focus : function(event, ui) {    //포커스 가면
        return false;//한글 에러 잡기용도로 사용됨
    },
    minLength: 1,// 최소 글자수
    autoFocus: true, //첫번째 항목 자동 포커스 기본값 false
    classes: {    //잘 모르겠음
        "ui-autocomplete": "highlight"
    },
    delay: 100,    //검색창에 글자 써지고 나서 autocomplete 창 뜰 때 까지 딜레이 시간(ms)
//            disabled: true, //자동완성 기능 끄기
    position: { my : "right top", at: "right bottom" },    //잘 모르겠음
    close : function(event){    //자동완성창 닫아질때 호출
        console.log(1);
    }
});

 
//]]>
</script>
