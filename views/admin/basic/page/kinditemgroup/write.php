<div class="box">
	<div class="box-table">
		<?php
		echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
		$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
		echo form_open(current_full_url(), $attributes);
		?>
			<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />

			<div class="form-group">
				<label class="col-sm-2 control-label">견종명</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="ckd_value_kr" value="<?php echo set_value('ckd_value_kr', element('ckd_value_kr', element('data', $view))); ?>" />

					
				</div>
			</div>			
			<div class="btn-group pull-right" role="group" aria-label="...">
				<button type="button" class="btn btn-default btn-sm btn-history-back" >취소하기</button>
				<button type="submit" class="btn btn-success btn-sm">저장하기</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[

var searchSource = [
	<?php
	if (element('all_kind',element('data',$view))) {
	    foreach (element('all_kind', element('data', $view)) as $result) {
	 		echo '"'.element('ckd_value_kr',$result).'","'.element('ckd_value_en',$result).'",';
		}
	}
	        
	?>
	"========" 
]; // 배열 형태로 


$("input[name='ckd_value_kr']")
.on("keydown", function( event ) {	
    if((event.keyCode === $.ui.keyCode.ENTER || event.keyCode === $.ui.keyCode.TAB) && $(this).autocomplete("instance").menu.active) {
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

$(function() {
	$('#fadminwrite').validate({
		rules: {
			ckd_value_kr: 'required',
		}
	});
});
//]]>
</script>
