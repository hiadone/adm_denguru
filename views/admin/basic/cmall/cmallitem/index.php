<div class="box">
	<div class="box-table">
		<?php
		echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
		$attributes = array('class' => 'form-inline', 'name' => 'fboardlist', 'id' => 'fboardlist');
		echo form_open(current_full_url(), $attributes);
		?>
			<div class="box-table-header ">
				<?php
				ob_start();
				?>	
				<div class="pull-left">
                <button type="button" class="btn btn-default btn-sm admin-manage-list"><i class="fa fa-cog big-fa"></i> 관리</button>
                <div class=" btn-admin-manage-layer admin-manage-layer-list" style="display: none;">
                    
                    <div class="item" onClick="post_multi_change_category();"><i class="fa fa-tags"></i> 카테고리변경</div>
                    
                    

                    
                    <div class="item" onClick="post_multi_change_brand();"><i class="fa fa-tags"></i> 브랜드변경</div>
                    <div class="item" onClick="post_multi_add_tag();"><i class="fa fa-tags"></i> 태그추가</div>
                    <div class="item" onClick="post_multi_delete_tag();"><i class="fa fa-tags"></i> 태그삭제</div>
                    
                    <div class="item" onClick="post_multi_action('cit_multi_status', '1', '선택하신 글들을 블라인드 해제 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i> 블라인드해제</div>
                    <div class="item" onClick="post_multi_action('cit_multi_status', '0', '선택하신 글들을 블라인드 처리 하시겠습니까?');"><i class="fa fa-exclamation-circle"></i> 블라인드처리</div>
               
                </div>
            </div>
					<div class="btn-group pull-right" role="group" aria-label="...">


						<a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">전체목록</a>

						<a href="<?php echo element('listall_url', $view); ?>?cit_type=1" class="btn btn-warning btn-sm">베스트상품 목록</a>
						<a href="<?php echo element('listall_url', $view); ?>?cit_type=2" class="btn btn-warning btn-sm">인기상품  목록</a>
						<a href="<?php echo element('listall_url', $view); ?>?cit_type=3" class="btn btn-warning btn-sm">신상품 목록</a>						
						<a href="<?php echo element('listall_url', $view); ?>?warning=1" class="btn btn-warning btn-sm">warning 목록</a>
						<a href="<?php echo element('listall_url', $view); ?>?nocategory=1" class="btn btn-warning btn-sm">nocategory 목록</a>
						<button type="button" class="btn btn-outline btn-default btn-sm btn-list-update btn-list-selected disabled" data-list-update-url = "<?php echo element('list_update_url', $view); ?>" >선택수정</button>
						<button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button>
						<a href="<?php echo element('write_url', $view); ?>" class="btn btn-outline btn-danger btn-sm">상품추가</a>
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
							<th>상품코드</th>
							<th>카테고리</th>
							<th>이미지</th>
							<th><a href="<?php echo element('cit_name', element('sort', $view)); ?>">상품명</a></th>
							<th><a href="<?php echo element('cit_price', element('sort', $view)); ?>">판매가격</a></th>
							<!-- <th>Vision API label</th> -->
                    		<th>태그</th>
							<th><a href="<?php echo element('cit_price_sale', element('sort', $view)); ?>">할인가격</a></th>
							<th><a href="<?php echo element('cit_status', element('sort', $view)); ?>">판매여부</a></th>
							<th><a href="<?php echo element('cit_sell_count', element('sort', $view)); ?>">판매량</a></th>
							<th><a href="<?php echo element('cit_hit', element('sort', $view)); ?>">조회수</a></th>
							<th>스크랩</th>
							<th>수정</th>
							<th><input type="checkbox" name="chkall" id="chkall" /></th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (element('list', element('data', $view))) {
						foreach (element('list', element('data', $view)) as $result) {
					?>
						<tr class="<?php echo element('warning', $result) ? 'warning':''; ?> ">
							<td><a href="<?php echo post_url('',element('post_id', $result)); ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span> <?php echo html_escape(element('cit_key', $result)); ?></a>
								<br>
								<?php if (element('cit_type1', $result)) { ?><label class="label label-danger">추천</label> <?php } ?>
								<?php if (element('cit_type2', $result)) { ?><label class="label label-warning">인기</label> <?php } ?>
								<?php if (element('cit_type3', $result)) { ?><label class="label label-default">신상품</label> <?php } ?>
								<?php if (element('cit_type4', $result)) { ?><label class="label label-primary">할인</label> <?php } ?>
							</td>
							<td style="width:130px;">
								<?php foreach (element('category', $result) as $cv) { echo '<label class="label label-info">' . html_escape(element('cca_value', $cv)) . '</label> ';} ?>
								
							</td>
							<td>
								<?php if (element('cit_file_1', $result)) {?>
									<a href="<?php echo element('cit_post_url', $result); ?>" target="_blank">
										<img src="<?php echo thumb_url('cmallitem', element('cit_file_1', $result)); ?>" alt="<?php echo html_escape(element('cit_name', $result)); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
									</a>
								<?php } ?>
							</td>
							<td><input type="text" name="cit_name[<?php echo element(element('primary_key', $view), $result); ?>]" class="form-control" value="<?php echo html_escape(element('cit_name', $result)); ?>" /></td>
							<td><input type="number" name="cit_price[<?php echo element(element('primary_key', $view), $result); ?>]" class="form-control" value="<?php echo html_escape(element('cit_price', $result)); ?>" /></td>
							
							<!-- <td>
		                       <textarea name="vision_api_label[<?php echo element('cit_id', $result); ?>]" id="val_tag_<?php echo element('cit_id', $result); ?>" data-cit_id="<?php echo element('cit_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="선택 옵션 (엔터로 구분하여 입력)"><?php echo html_escape(element('display_label', $result)); ?></textarea>
		                    </td> -->
		                    <td>
		                       <textarea name="cta_tag[<?php echo element('cit_id', $result); ?>]" id="cta_tag_<?php echo element('cit_id', $result); ?>" data-cit_id="<?php echo element('cit_id', $result); ?>" class="form-control options" style="margin-top:5px;height:120px;" placeholder="선택 옵션 (엔터로 구분하여 입력)"><?php echo html_escape(element('display_tag', $result)); ?></textarea>
		                    </td>
							
							<td><input type="number" name="cit_price_sale[<?php echo element(element('primary_key', $view), $result); ?>]" class="form-control" value="<?php echo html_escape(element('cit_price_sale', $result)); ?>" /></td>
							<td><input type="checkbox" name="cit_status[<?php echo element(element('primary_key', $view), $result); ?>]" value="1" <?php echo set_checkbox('cit_status', '1', (element('cit_status', $result) ? true : false)); ?> /></td>
							<td class="text-right"><?php echo number_format(element('cit_sell_count', $result)); ?></td>
							<td class="text-right"><?php echo number_format(element('cit_hit', $result)); ?></td>
							<td class="text-right"><?php echo number_format(element('cmall_wishlist_count', $result)); ?></td>
							<td><a href="<?php echo admin_url($this->pagedir); ?>/write/<?php echo element(element('primary_key', $view), $result); ?>?<?php echo $this->input->server('QUERY_STRING', null, ''); ?>" class="btn btn-outline btn-default btn-xs">수정</a>
							<br>
							<br>
							<a href="<?php echo admin_url('cmall/crawlitem?sfield=brd_id2&skeyword=' . element('brd_id', $result).'&crw_goods_code='.element('cit_goods_code', $result)); ?>" target="_blank" class="btn-sm btn-xs btn-info">원본</a>
							

							</td>
							<td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" /></td>
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
				
			</div>
			<div class="box-table-header ">
				<?php echo $buttons; ?>
			</div>
		<?php echo form_close(); ?>
	</div>
	<form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
		<div>추가 검색 조건 
		<div id="where">
		<?php 


			$html ='';
			if(!empty($this->input->get('cit_name'))){
				
				$where['cit_name'] = element(0,$this->input->get('cit_name'));
				$html = "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='cit_name[]' value='".$where['cit_name']."'>'".$where['cit_name']."'</button>";
				
			} 


			if(!empty($this->input->get('cit_price'))){
				
				$where['cit_price'] = element(0,$this->input->get('cit_price'));

				$html .= "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='cit_price[]' value='".$where['cit_price']."'>'".$where['cit_price']."'</button>";

				
			} 

			if($this->input->get('brd_id')){

	            
	            $res = $this->input->get('brd_id');
	            
	            if($res){
	                $brd_id_arr=array();
	                foreach ($res as $key => $value) {

	                	$html .= "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='brd_id[]' value='".$value."'>'".$value."'</button>";
	                    
	                }

	                

	                
	                // $this->db2->group_end();
	            }
	        } 
			
			if($this->input->get('cbr_id')){


				$res = $this->input->get('cbr_id');
				
				if($res){
				    $cbr_id_arr=array();
				    foreach ($res as $key => $value) {
				        $html .= "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='cbr_id[]' value='".$value."'>'".$value."'</button>";
				    }

				    


				    // $this->db2->group_end();
				}



			}

			if($this->input->get('cca_id')) {


				$res = $this->input->get('cca_id');
				
				if($res){
				    $cca_id_arr=array();
				    foreach ($res as $key => $value) {
				        $html .= "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='cca_id[]' value='".$value."'>'".$value."'</button>";
				    }

				    
				    

				    // $this->db2->group_end();
				}
				
			}


			if($this->input->get('cat_id')) {


				$res = $this->input->get('cat_id');
				
				if($res){
				    $cat_id_arr=array();
				    foreach ($res as $key => $value) {
				        $html .= "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='cat_id[]' value='".$value."'>'".$value."'</button>";
				    }

				    
				}
				
			}


			if($this->input->get('search_tag')) {


				$value = $this->input->get('search_tag');
				
				
				        $html .= "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='search_tag' value='".$value."'>'".$value."'</button>";
				    

				    
				
				
			}
			echo $html;
		?>
		</div>
		
		<div class="box-search">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<select class="form-control" name="sfield" >
						<?php echo element('search_option', $view); ?>
					</select>
					<div class="input-group">
						<input type="text" class="form-control" name="skeyword" id="skeyword" value="<?php echo html_escape(element('skeyword', $view)); ?>" placeholder="Search for..." />
						<span class="input-group-btn">
							<button type="button" class="btn btn-default btn-sm" id="addsearch">추가!</button>
							<button class="btn btn-danger btn-sm" name="search_submit" type="submit">검색!</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[

$('input[type="text"]').keydown(function() {
  if (event.keyCode === 13) {
    event.preventDefault();
  };
});




$(document).on('click', 'button.where-btn', function() {
    
  $(this).remove();
});


$(document).on('click', '#addsearch', function() {

	if($("select[name='sfield']").val() =='brd_id' ||  $("select[name='sfield']").val() =='cit_price' ||  $("select[name='sfield']").val() =='cbr_id'){

		var input = "<button type='button' class='btn btn-default btn-xs where-btn'><input type='hidden' name='"+$("select[name='sfield']").val()+"[]' value='"+$("#skeyword").val()+"'>'"+$("#skeyword").val()+"'</button>";
		
		$('#where').append(input);
	}
	
});
$("select[name='sfield']").change(function(e){

		$("#skeyword").off("keydown", function( event ) {});

		if($(this).val() =='brd_id') board_list();

		if($(this).val() =='cbr_id') brand_list();

		if($(this).val() =='cca_id') search_category();

		if($(this).val() =='cat_id') search_attr();

		if($(this).val() =='cta_id') search_tag();
	    	
	    });

$(document).on('change', 'textarea[name^=cta_tag]', function() {
    post_action_crawl('cta_tag_update', $(this).data('cit_id'),'','cta_tag_');
});


var searchbrand_list = [
	<?php
	if (element('brand_list', $view)) {
	    foreach (element('brand_list', $view) as $result) {
	 		echo '"'.element('cbr_value_kr',$result).'","'.element('cbr_value_en',$result).'",';
		}
	}
	        
	?>
	"========" 
]; // 배열 형태로 


var searchboard_list = [
	<?php
	if (element('board_list', $view)) {
	    foreach (element('board_list', $view) as $result) {
	 		echo '"'.element('brd_name',$result).'",';
		}
	}
	        
	?>
	"========" 
]; // 배열 형태로 

function search_category() {
	
	var sub_win = window.open(cb_url + '/helptool/search_category', 'search_category', 'left=100, top=100, width=620, height=500, scrollbars=1');

	
	;
	
}

function search_attr() {
	
	var sub_win = window.open(cb_url + '/helptool/search_attr', 'search_attr', 'left=100, top=100, width=620, height=500, scrollbars=1');

	
	;
	
}

function search_tag() {
	
	var sub_win = window.open(cb_url + '/helptool/search_tag', 'search_tag', 'left=100, top=100, width=620, height=500, scrollbars=1');

	
	;
	
}

function board_list(){
	$("#skeyword")
	.on("keydown", function( event ) {
	    if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
	        event.preventDefault();
	    }
	})
	.autocomplete({  //오토 컴플릿트 시작
	    source : searchbrand_list,    // source 는 자동 완성 대상
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
}

function brand_list(){
	$("#skeyword")
	.on("keydown", function( event ) {
	    if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
	        event.preventDefault();
	    }
	})
	.autocomplete({  //오토 컴플릿트 시작
	    source : searchbrand_list,    // source 는 자동 완성 대상
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
	    // disabled: true, //자동완성 기능 끄기
	    position: { my : "right top", at: "right bottom" },    //잘 모르겠음
	    close : function(event){    //자동완성창 닫아질때 호출
	        console.log(1);
	    }
	});
}
board_list();
//]]>
</script>

