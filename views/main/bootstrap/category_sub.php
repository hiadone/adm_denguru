<?php 
$this->managelayout->add_css(base_url('views/main/bootstrap/css/main.css')); 



$data = element('data',element('main',$view));








 ?>
<!DOCTYPE html>
<html lang="ko">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
</head>
<body>
    <div class="wrap">
        

        

        <div class="main">
            <?php 
            if(element('egr_type',$data) === '3'){
                echo '<div class="img_box"><img src="'.element('egr_detail_image_url',$data).'" alt="예시배너" class="img"></div>';
            }
            

            if(element('egr_type',$data) === '2'){
                
                
                echo '<div class="img_box"><img src="'.element('egr_detail_image_url',$data).'" alt="예시배너" class="img"></div>';
                
                
                if(element('secionlist',$data))
                    foreach(element('secionlist',$data) as $secionlist){
                        
                
                        echo '<div class="sect07">
                            <ul class="item_list03">';

                
                        if(element('itemlists',$secionlist))
                            foreach(element('itemlists',$secionlist) as $val){
                
                
                         ?>
                            <li class="item_box">
                                <a href="home_item_info.html">
                                    <div class="item_thum">
                                        <img src="<?php echo element('cit_image',$val) ?>" alt="상품이미지" class="img">
                                    </div>
                                    <div class="item_txt_box">
                                        <div class="item_shop"><?php echo element('brd_name',$val) ?></div>
                                        <div class="item_name"><?php echo element('cit_name',$val) ?></div>
                                        <div class="item_price"><?php echo element('cit_price_sale',$val) ?> </div>
                                    </div>
                                </a>
                            </li>
                        <?php 
                        
                           
                        }
                        
                    
                    echo '</ul>';
                    echo '</div>';
                }
               
            }
             
            if(element('egr_type',$data) === '1'){
                
                echo '<div class="img_box"><img src="'.element('egr_detail_image_url',$data).'" alt="예시배너" class="img"></div>';
                if(element('secionlist',$data))
                    foreach(element('secionlist',$data) as $secionlist){

                        echo '<div class="sect08 special_item_list_container">
                        <h1 class="title09">'.element('eve_title',$secionlist).'</h1>
                        <ul class="item_list03">';
                        
                        if(element('itemlists',$secionlist))
                            foreach(element('itemlists',$secionlist) as $val){
                
                         ?>
                            <li class="item_box">
                                <a href="home_item_info.html">
                                    <div class="item_thum">
                                        <img src="<?php echo element('cit_image',$val) ?>" alt="상품이미지" class="img">
                                    </div>
                                    <div class="item_txt_box">
                                        <div class="item_shop"><?php echo element('brd_name',$val) ?></div>
                                        <div class="item_name"><?php echo element('cit_name',$val) ?></div>
                                        <div class="item_price"><?php echo element('cit_price_sale',$val) ?> </div>
                                    </div>
                                </a>
                            </li>
                        <?php 
                        
                           
                        }
                        
                    
                    echo '</ul>';
                    echo '</div>';
                }
               
            }
            ?>
            <div style="background-color: #fff;padding-bottom: 80px;"></div>
            <?php if(element('egr_type',$data) === '3'){  ?>
                <div class="shop_shortcut_box">
                    <button type="button" class="btn btn_accent btn_big btn_big_round">이벤트 참여하기</button>
                </div>

            <?php } ?>
            
        </div>
    </div>

</body>
</html>


