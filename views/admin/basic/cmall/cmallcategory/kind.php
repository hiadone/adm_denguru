<div class="box">
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li role="presentation" ><a href="<?php echo admin_url($this->pagedir); ?>" onclick="return check_form_changed();">카테고리 관리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/attr'); ?>" onclick="return check_form_changed();">제품특성 관리</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/kind'); ?>" onclick="return check_form_changed();">견종 관리</a></li>
            
        </ul>

    </div>

    <div class="box-table">
        <button type="button" class="btn btn-success btn-sm" onClick="javascript:$('.ckd_text').toggle();">태그사전 감추기</button>
        <div class="box-table">
            <?php


            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                <input type="hidden" name="is_submit" value="1" />
                <input type="hidden" name="type" value="add" />
                <div class="form-group">
                    <label class="col-sm-1 control-label">견종 추가</label>
                    <div class="col-sm-11 form-inline">
                        <select name="ckd_parent" class="form-control">
                            <option value="0">최상위견종</option>
                            <?php
                            $data = element('data', $view);

                            function cmall_ca_select($p, $data,$len)
                            {
                                $return = '';
                                $nextlen = $len + 1;
                                if ($p && is_array($p)) {
                                    foreach ($p as $result) {
                                        $margin='';
                                        if ($len) {
                                            
                                            for($i=0;$len > $i;$i++)
                                                $margin .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        }

                                        $return .= '<option value="' . html_escape(element('ckd_id', $result)) . '"> ' . $margin .html_escape(element('ckd_value_kr', $result)) . '의 하위순위견종</option>';
                                        $parent = element('ckd_id', $result);
                                        $return .= cmall_ca_select(element($parent, $data), $data,$nextlen);
                                    }
                                }
                                return $return;
                            }
                            echo cmall_ca_select(element(0, $data), $data,0);
                            ?>
                        </select>
                        <input type="text" name="ckd_value_kr" class="form-control" value="" placeholder="한글 견종 입력" />
                        <input type="text" name="ckd_value_en" class="form-control" value="" placeholder="영문 견종 입력" />
                        <input type="text" name="ckd_size" class="form-control" value="" placeholder="견종 크기 입력" />

                        <textarea class="form-control" style="width:300px;" name="ckd_text" id="ckd_text" rows="1" placeholder="사전 (콤마로 구분하여 입력)"></textarea>
                        <button type="submit" class="btn btn-success btn-sm">추가하기</button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
        <?php
        
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        ?>
        <ul class="list-group">
            <?php
            

            $data = element('data', $view);
            function cmall_ca_list($p, $data, $len)
            {   
                $return ='';
                $nextlen = $len + 1;
                if ($p && is_array($p)) {
                    foreach ($p as $result) {
                        $margin = 20 * $len;
                        $ckd_size='';

                        
                        if(element('ckd_size', $result) === "4") $ckd_size = "소형견";
                        elseif(element('ckd_size', $result) === "5") $ckd_size = "중형견";
                        elseif(element('ckd_size', $result) === "6") $ckd_size =  "대형견";

                        $attributes = array('class' => 'form-inline', 'name' => 'fbrand');
                        
                        
                        if ($len) {
                            $return .= '<li class="list-group-item">
                                            <div class="form-horizontal">
                                                <div class="form-group" style="margin-bottom:0;">';
                            $return .= '<div style="width:10px;float:left;margin-left:' . $margin . 'px;margin-right:10px;"><span class="fa fa-arrow-right"></span></div>';
                        } else {
                            $return .= '<li class="list-group-item" style="background-color:#e5e5e5">
                                            <div class="form-horizontal">
                                                <div class="form-group" style="margin-bottom:0;">';
                        }
                        $return .= '<div class="pl10 ">
                            <div class="cat-ckd-id-' . element('ckd_id', $result) . '">
                                ' . html_escape(element('ckd_value_kr', $result)) . ' (' . html_escape(element('ckd_value_en', $result)) . ')'.'<button class="btn btn-info btn-xs">'.$ckd_size.'</button>
                                <button class="btn btn-primary btn-xs" onClick="ckd_modify(\'' . element('ckd_id', $result) . '\')"><span class="glyphicon glyphicon-edit"></span></button>';
                        
                        $return .= '                    <button class="btn btn-danger btn-xs btn-one-delete" data-one-delete-url = "' . admin_url('cmall/cmallcategory/kind_delete/' . element('ckd_id', $result)) . '"><span class="glyphicon glyphicon-trash"></span></button>';
                        $return .= '<button class="ckd_text" >'.html_escape(element('ckd_text', $result)).'</button>';
                        $return .= '    </div><div class="form-inline mod-ckd-id-' . element('ckd_id', $result) . '" style="display:none;">';
                        $return .= form_open(current_full_url(), $attributes);
                        $return .= '<input type="hidden" name="ckd_id"  value="' . element('ckd_id', $result) . '" />
                                                            <input type="hidden" name="type" value="modify" />
                                                            <div class="form-group" style="margin-left:0;">
                                                                한글 브랜드명 <input type="text" class="form-control" name="ckd_value_kr" value="' . html_escape(element('ckd_value_kr', $result)) . '" />
                                                                영문 브랜드명 <input type="text" class="form-control" name="ckd_value_en" value="' . html_escape(element('ckd_value_en', $result)) . '"/>
                                                                크기  <input type="text" class="form-control" name="ckd_size" value="' . $ckd_size . '"/>
                                                                사전 <textarea  class="form-control" style="width:300px;" name="ckd_text" rows="1" >' . html_escape(element('ckd_text', $result)) . '</textarea>
                                                                <button class="btn btn-primary btn-xs" type="submit" >저장</button>
                                                                <a href="javascript:;" class="btn btn-default btn-xs" onClick="ckd_cancel(\'' . element('ckd_id', $result) . '\')">취소</a>
                                                            </div>';
                        $return .= form_close();
                        $return .= '</div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </li>';
                        $parent = element('ckd_id', $result);
                        $return .= cmall_ca_list(element($parent, $data), $data, $nextlen);
                    }
                    
                }
                return $return;
            }

            echo cmall_ca_list(element(0, $data), $data, 0);
            ?>
        </ul>
    <div>
        <div class="box-table">
            <?php
            $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
            echo form_open(current_full_url(), $attributes);
            ?>
                <input type="hidden" name="is_submit" value="1" />
                <input type="hidden" name="type" value="add" />
                <div class="form-group">
                    <label class="col-sm-1 control-label">견종 추가</label>
                    <div class="col-sm-11 form-inline">
                        <select name="ckd_parent" class="form-control">
                            <option value="0">최상위견종</option>
                            <?php
                            $data = element('data', $view);

                            
                            echo cmall_ca_select(element(0, $data), $data,0);
                            ?>
                        </select>
                        <input type="text" name="ckd_value_kr" class="form-control" value="" placeholder="한글 견종 입력" />
                        <input type="text" name="ckd_value_en" class="form-control" value="" placeholder="영문 견종 입력" />
                        <input type="text" name="ckd_size" class="form-control" value="" placeholder="견종 크기 입력" />

                        <textarea class="form-control" style="width:300px;" name="ckd_text" id="ckd_text" rows="1" placeholder="사전 (콤마로 구분하여 입력)"></textarea>
                        <button type="submit" class="btn btn-success btn-sm">추가하기</button>
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
$(function() {
    $('#fadminwrite', 'input[name=fcategory]').validate({
        rules: {
            ckd_value_kr: {required :true},
            ckd_value_en: {required :true},
        }
    });
});



function ckd_modify(ckd_id) {
    $('.cat-ckd-id-' + ckd_id).hide();
    $('.mod-ckd-id-' + ckd_id).show();
}
function ckd_cancel(ckd_id) {
    $('.cat-ckd-id-' + ckd_id).show();
    $('.mod-ckd-id-' + ckd_id).hide();
}


var ckd_value_kr_list = [
    <?php
    if (element(0, element('data', $view))) {
        foreach (element(0, element('data', $view)) as $result) {
            echo '"'.element('ckd_value_kr',$result).'",';
        }
    }
            
    ?>
    "========" 
]; // 배열 형태로 


var ckd_value_en_list = [
    <?php
    if (element(0, element('data', $view))) {
        foreach (element(0, element('data', $view)) as $result) {
            echo '"'.element('ckd_value_en',$result).'",';
        }
    }
            
    ?>
    "========" 
]; // 배열 형태로 


$("input[name='ckd_value_kr']")
    .on("keydown", function( event ) {
        if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {            
            event.preventDefault();
        }
    })
    .autocomplete({  //오토 컴플릿트 시작
        source : ckd_value_kr_list,    // source 는 자동 완성 대상
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
               // disabled: flag, //자동완성 기능 끄기
        position: { my : "right top", at: "right bottom" },    //잘 모르겠음
        close : function(event){    //자동완성창 닫아질때 호출
            console.log(1);
        }
    });


$("input[name='ckd_value_en']")
    .on("keydown", function( event ) {
        if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {            
            event.preventDefault();
        }
    })
    .autocomplete({  //오토 컴플릿트 시작
        source : ckd_value_en_list,    // source 는 자동 완성 대상
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
               // disabled: flag, //자동완성 기능 끄기
        position: { my : "right top", at: "right bottom" },    //잘 모르겠음
        close : function(event){    //자동완성창 닫아질때 호출
            console.log(1);
        }
    });
//]]>
</script>
