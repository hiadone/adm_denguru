<?php 
$this->managelayout->add_css(base_url('views/main/bootstrap/css/main.css')); 

$member = element('member',element('layout',element('main',$view)));


$layout = element('layout',element('main',$view));


$petlist = element('petlist',element('member',element('layout',element('main',$view))));

$ai_recom_ = element('ai_recom',$view);

$type1 = element('type1',element('data',element('main',$view)));

$type2 = element('type2',element('data',element('main',$view)));

$denguru_recom_ = element('denguru_recom',$view);

$reviewlist = element('data',element('reviewlist',$view));


 ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DENGURU</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</head>
<body>
    <div class="wrap">
        <div class="pd_header01"></div>
        <header class="header01">
            <h1 class="h_logo_box"><a href="index.html" class="h_link"><img src="/views/main/bootstrap/images/logo-horizontal.svg" alt="DENGURU 로고" class="h_logo"></a></h1>
            <div class="h_search_box">
                <a href="search.html">
                    <div class="icon_box"><img src="/views/main/bootstrap/images/icon-search.svg" alt="검색" class="icon"></div>
                    <div class="search_txt">추천검색어</div>
                </a>
            </div>
            <div class="h_btn">
                <!-- <span class="has_alarm"><span class="blind">알림이 있습니다</span></span> -->
                <a href="my_notification.html"><img src="/views/main/bootstrap/images/icon-bell-with-dot.svg" alt="알림" class="icon"></a>
            </div>
        </header>

        <div class="img_box"><a href="<?php echo element('ban_click_url',element('main_top',element('banner',$layout))) ?>"><img src="<?php echo element('ban_image_url',element('main_top',element('banner',$layout))) ?>" alt="영양제 최대 특가전" class="bnr"></a>
        </div>

        <nav class="gnb_bottom">
            <h2 class="blind">하단 네비게이션</h2>
            <ul class="gnb_list">
                <li class="gnb_item">
                    <a href="store_rank.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-store.svg" alt="store" class="gnb_icon">
                        <span class="gnb_txt blind">store</span>
                    </a>
                </li>
                <li class="gnb_item">
                    <a href="category_main.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-category.svg" alt="category" class="gnb_icon">
                        <span class="gnb_txt blind">category</span>
                    </a>
                </li>
                <li class="gnb_item active">
                    <a href="index.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-home-active.svg" alt="home" class="gnb_icon">
                        <span class="gnb_txt blind">home</span>
                    </a>
                </li>
                <li class="gnb_item">
                    <a href="pick_main.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-pick.svg" alt="pick" class="gnb_icon">
                        <span class="gnb_txt blind">pick</span>
                    </a>
                </li>
                <li class="gnb_item">
                    <a href="my_main_resister.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-my.svg" alt="my" class="gnb_icon">
                        <span class="gnb_txt blind">my</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="main">
            <!-- main_profile -->
            <?php 

            if(empty(element('list',$petlist))){ ?>
            
            <section class="main_profile">
                <h2 class="blind">마이펫 프로필</h2>
                <ul class="profile_list swiper-wrapper">
                    <li class="profile swiper-slide">
                        <div class="profile_img"><img src="/views/main/bootstrap/images/profile-noimg.png" alt="프로필 이미지" class="img"></div>
                        <div class="profile_message01">
                            <span class="emph">사랑스러운 우리아이</span>를<br>등록해주세요.
                        </div>
                        <div class="profile_message02">
                            댕구루 AI가 꼭! 맞는 제품을 추천해드릴께요!
                        </div>
                        <div class="resister_btn_box">
                            <a href="home_resister_pet.html" class="btn btn_big btn_accent btn_resister">
                                등록하기
                            </a>
                        </div>
                    </li>
                </ul>
            </section>
            <?php 
            } else {?>
            
            <section class="main_profile swiper-container">
                <h2 class="blind">마이펫 프로필</h2>
                <ul class="profile_list swiper-wrapper">

                    <?php 

                    if(element('list',$petlist))
                        foreach(element('list',$petlist) as $pval){



                            $diff = abs(strtotime(date('Y-m-d')) - strtotime(element('pet_birthday',$pval)));

                            $years = floor($diff / (365*60*60*24));
                            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                     ?>       

                     
                    <li class="profile swiper-slide">
                        <div class="profile_img"><img src="<?php echo element('pet_photo_url',$pval) ?>" alt="레오 사진" class="img"></div>
                        <div class="profile_name">
                            <?php echo element('pet_name',$pval) ?>
                            <span class="profile_name_sm">(<?php echo element('pet_sex',$pval) == '1' ? '남아' : '여아' ?>/<?php echo $months ?>개월)</span>
                        </div>
                        <ul class="profile_tag_list">
                            <li class="profile_tag"><?php echo element('pet_form_str',$pval) ?></li>
                            <li class="profile_tag"><?php echo element('ckd_size_str',$pval) ?></li>
                            <li class="profile_tag"><?php echo element('pet_kind',$pval) ?></li>
                        </ul>
                        <ul class="profile_tag_list profile_tag_list_feathers">
                            <?php 

                            foreach(element('pet_attr',$pval) as $value){
                                echo '<li class="profile_tag">'.element('pat_value',$value).'</li>';    
                            }

                             ?>
                            
                            
                        </ul>
                        <ul class="profile_tag_list profile_tag_list_allergy">
                            <li class="profile_tag">

                                <?php 
                                $val =array();
                                foreach(element('pet_allergy_rel',$pval) as $value){
                                    $val[]=element('pag_value',$value);    
                                }
                                
                                echo implode(", ",$val);
                                 ?>
                                
                            </li>
                        </ul>
                        <a href="home_resister_pet_edit.html" class="btn_modify_pet"><img src="/views/main/bootstrap/images/btn_setting.png" alt="레오 정보 수정하기" class="img"></a>
                    </li>
                    <?php } ?>
                    
                </ul>
                <div class="swiper-pagination pagination_main"></div>
                <a href="home_resister_pet.html" class="btn_add">
                    추가 <img src="/views/main/bootstrap/images/btn_plus.png" alt="+" class="icon">
                </a>
            </section>
            <?php } ?>
            <!-- 댕구루 AI 추천 -->
            
                <?php 

                if($ai_recom_)
                    foreach($ai_recom_ as $ai_recom){

                ?>
                    <section class="sect02 sect_recommand_ai">
                <h2 class="title01"><img src="/views/main/bootstrap/images/logo-icon.svg" alt="로고아이콘" class="logo_icon"> 댕구루 AI 추천</h2>
                        <div class="sub_title01">
                            <span class="emph01 js-petname"><?php echo element('pet_name',element('pet_info',$ai_recom)) ?>
                            </span>에게 맞춤 제품을 추천해드려요.
                            <br>
                            <span class="emph02"><span class="js-petage"><?php echo element('pet_age',element('pet_info',$ai_recom)) ?>살</span>, <span class="js-petbreed"><?php echo element('pet_kind',element('pet_info',$ai_recom)) ?></span> <span class="js-petpercent">80%</span></span>가 최근 <span class="emph02">일주일</span>동안 가장 관심있는 제품입니다.
                        </div>
                        <div class="list_info_bar01">
                            <div class="list_num"><span class="num"><?php echo element('total_rows',$ai_recom) ?></span> items</div>
                            <select name="listSort" id="listSort" class="list_order">
                                <option value="">추천순</option>
                                <option value="">ㅇㅇ순</option>
                            </select>
                        </div>
                        <div class="items_wrap swiper-container">
                            <div class="itmes_container swiper-wrapper">
                                <ul class="item_list01 swiper-slide">
                    
                                <?php 
                                $i=0;
                                if(element('list',$ai_recom))
                                    foreach(element('list',$ai_recom) as $val){
                                    $i++;
                                 ?>
                                <li class="item_box">
                                    <a href="home_item_info.html">
                                        <div class="item_thum">
                                            <img src="<?php echo element('cit_image',$val) ?>" alt="상품이미지" class="img">
                                        </div>
                                        <div class="item_txt_box">
                                            <div class="item_shop"><?php echo element('brd_name',$val) ?></div>
                                            <div class="item_name"><?php echo element('cit_name',$val) ?></div>
                                            <div class="item_price"><?php echo element('cit_price_sale',$val) ?> <span class="rate_discount"><?php echo element('cit_price_sale_percent',$val) ?>%</span></div>
                                            <div class="item_price_before"><?php echo element('cit_price',$val) ?></div>
                                        </div>
                                    </a>
                                </li>
                                <?php 
                                
                                    if(!($i % 4)){
                                        echo '</ul>
                                            <ul class="item_list01 swiper-slide">';
                                    }
                                }
                                ?>
                            </ul>
                            
                        </div>
                        <div class="swiper-pagination pagination_main"></div>
                    </div>
                </section>
                <?php 
                    }
                    
                 ?>
                
            
            <!-- 오늘의 BEST ITEMS -->
            <section class="sect01">
                <h2 class="title01">오늘의 BEST ITEMS</h2>
                <div class="items_wrap">
                    <ul class="item_list02">
                        <?php 
                        $i=0;
                        if(element('list',$type1))
                            foreach(element('list',$type1) as $val){
                            $i++;
                         ?>
                         <li class="item_box">
                            <a href="home_item_info.html">
                                <div class="item_thum">
                                    <img src="<?php echo element('cit_image',$val) ?>" alt="상품이미지" class="img">
                                </div>
                                <div class="item_txt_box">
                                    <div class="item_shop"><?php echo element('brd_name',$val) ?></div>
                                    <div class="item_name"><?php echo element('cit_name',$val) ?></div>
                                    <div class="item_price"><?php echo element('cit_price_sale',$val) ?></div>
                                </div>
                            </a>
                        </li>
                        <?php 
                        } 
                        ?>
                    </ul>
                    <div class="btn_box_bottom">
                        <a href="home_best.html" class="btn_more">더보기 <img src="/views/main/bootstrap/images/icon-angle-right.svg" alt=">" class="icon"></a>
                    </div>
                </div>
            </section>
            <!-- 광고 -->
            <section class="sect_ad01">
                <h2 class="blind">광고영역</h2>

                <a href="<?php echo element('ban_click_url',element('main_middle',element('banner',$layout))) ?>"><img src="<?php echo element('ban_image_url',element('main_middle',element('banner',$layout))) ?>" alt="영양제 최대 특가전" class="bnr"></a>
            </section>
            <!-- 지금 뜨는 인기 ITEMS -->
            <section class="sect02 sect_hot_items">
                <h2 class="title01">지금 뜨는 인기 ITEMS</h2>
                
                <div class="hash_list_container swiper-container">
                    <h3 class="blind">인기 검색어</h3>
                    <ul class="item_list04 swiper-wrapper">

                        <?php 

                        if(element('list',element('top',$type2)))
                            foreach(element('list',element('top',$type2)) as $val){
                        ?>
                            <li class="item_box swiper-slide">
                                <a href="<?php echo element('search_url',$val) ?>">
                                    <div class="item_thum"><img src="<?php echo element('oth_image',$val) ?>" alt="#<?php echo element('oth_title',$val) ?>" class="img"></div>
                                    <div class="hash">#<?php echo element('oth_title',$val) ?></div>
                                </a>
                            </li>
                        <?php         
                        }
                        ?>
                    </ul>
                </div>
                <div class="items_wrap swiper-container">
                    <div class="itmes_container swiper-wrapper">
                        <ul class="item_list01 swiper-slide">
                            <?php 
                            $i=0;
                            if(element('list',element('middle',$type2)))
                                foreach(element('list',element('middle',$type2)) as $val){
                                $i++;
                             ?>
                            <li class="item_box">
                                <a href="home_item_info.html">
                                    <div class="item_thum">
                                        <img src="<?php echo element('cit_image',$val) ?>" alt="상품이미지" class="img">
                                    </div>
                                    <div class="item_txt_box">
                                        <div class="item_shop"><?php echo element('brd_name',$val) ?></div>
                                        <div class="item_name"><?php echo element('cit_name',$val) ?></div>
                                        <div class="item_price"><?php echo element('cit_price_sale',$val) ?> <span class="rate_discount"><?php echo element('cit_price_sale_percent',$val) ?>%</span></div>
                                        <div class="item_price_before"><?php echo element('cit_price',$val) ?></div>
                                    </div>
                                </a>
                            </li>
                            <?php 
                            
                                if(!($i % 4)){
                                    echo '</ul>
                                        <ul class="item_list01 swiper-slide">';
                                }
                            }
                            ?>
                        </ul>
                        
                    </div>
                    <div class="swiper-pagination  pagination_main"></div>
                </div>
            </section>

           


            <!-- oo를 위해 이런건 어때요? -->
            
                    <?php 

                    if($denguru_recom_)
                        foreach($denguru_recom_ as $denguru_recom){

                    ?>
                    <section class="sect01 sect_recommand_pet">
                <h2 class="title01" style="margin-bottom: 8px;"><img src="/views/main/bootstrap/images/logo-icon.svg" alt="로고아이콘" class="logo_icon"> <span class="emph js-petname"><?php echo element('pet_name',element('pet_info',$denguru_recom)) ?></span>를 위해 이런건 어때요?</h2>
                <div class="sub_title01" style="margin-bottom: 16px;">
                    <?php 
                    $pet_attr= array();
                        if(element('pet_attr',element('pet_info',$denguru_recom))) 
                            foreach(element('pet_attr',element('pet_info',$denguru_recom)) as $val) {
                                $pet_attr[] = element('pat_value',$val);
                            }
                     ?>
                    <span class="emph02 js-petfeature"><?php echo implode(', ',$pet_attr) ?></span>의 특징을 가진 <span class="emph02"><span class="js-petage"><?php echo element('pet_age',element('pet_info',$denguru_recom)) ?>살</span>, <span class="js-petbreed"><?php echo element('pet_kind',element('pet_info',$denguru_recom)) ?></span></span> 아이들이 가장 많이 찾는 제품이에요.
                </div>
                <div class="items_wrap">
                    <ul class="item_list03">

                        <?php 
                        $i=0;
                        if(element('list',$denguru_recom))
                            foreach(element('list',$denguru_recom) as $val){
                            $i++;
                         ?>
                        <li class="item_box">
                            <a href="home_item_info.html">
                                <div class="item_thum">
                                    <img src="<?php echo element('cit_image',$val) ?>" alt="상품이미지" class="img">
                                </div>
                                <div class="item_txt_box">
                                    <div class="item_shop"><?php echo element('brd_name',$val) ?></div>
                                    <div class="item_name"><?php echo element('cit_name',$val) ?></div>
                                    <div class="item_price"><?php echo element('cit_price_sale',$val) ?></div>
                                </div>
                            </a>
                        </li>
                        <?php 
                        }
                        ?>
                        
                    </ul>
                    <div class="btn_box_bottom">
                        <a href="" class="btn_more"><img src="/views/main/bootstrap/images/icon-refresh.svg" alt="새로고침" class="icon"> 추천 상품 새로 보기</a>
                    </div>
                </div>

            </section>
            <?php 
            }
             ?>
            <!-- 광고 -->
            <section class="sect_ad01">
                <h2 class="blind">광고영역</h2>
                <a href="<?php echo element('ban_click_url',element('main_bottom',element('banner',$layout))) ?>"><img src="<?php echo element('ban_image_url',element('main_bottom',element('banner',$layout))) ?>" alt="영양제 최대 특가전" class="bnr"></a>
            </section>
            <!-- 리뷰 -->
            <section class="sect01">
                <h2 class="title01">REVIEW</h2>
                <ul class="review_list01">
                    <?php 

                    if(element('list',$reviewlist))
                        foreach(element('list',$reviewlist) as $val){   
                            $pet_attr = array();
                            foreach(element('pet_attr',$val) as $aval)
                                $pet_attr[] = element('pat_value',$aval);
                    ?>
                    <li class="review_card">
                        <div class="review_profile_box">
                            <a href="home_review_user.html">
                                <div class="profile_thum"><img src="<?php echo element('pet_photo_url',$val) ?>" alt="레오 프로필" class="img"></div>
                                <div class="profile_txt_box">
                                    <div class="user_id"><?php echo element('mem_userid',$val) ?> (<?php echo element('pet_name',$val) ?>)</div>
                                    <div class="user_features">
                                        <?php echo element('pet_age',$val) ?>살/<?php echo element('pet_kind',$val) ?>/
                                        <?php echo implode(", ",$pet_attr) ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="review_item_info_box">
                            <div class="review_item_name"><a href="home_review_item.html"><?php echo element('brd_name',$val) ?><?php echo element('cit_name',$val) ?></a></div>
                            <div class="review_post_info">
                                <span class="stars">
                                    <?php 

                                    for($i =0 ; (int) element('cit_review_average',$val) > $i ; $i++){
                                        echo '<img src="/views/main/bootstrap/images/icon-star.svg" alt="별" class="star">';
                                    }
                                    ?>
                                </span>
                                <span class="post_date"><?php echo display_datetime(element('cre_datetime',$val),'sns') ?></span>
                                <a href="" class="link_report">신고하기</a>
                            </div>
                        </div>
                        <div class="review_txt_box">
                            <div class="review_txt01">
                                <a href="home_review_item.html#review03">
                                좋은점 : <?php echo element('cre_good',$val) ?>
                                아쉬운점 : <?php echo element('cre_bad',$val) ?>
                                나만의 팁 : <?php echo element('cre_tip',$val) ?>
                                </a>
                            </div>
                        </div>
                        <div class="review_img_box">

                            <?php 
                            foreach(element('review_image',$val) as $aval){
                                echo '<a><img src="'.element('pat_value',$aval).'" alt="리뷰이미지" class="img"></a>';
                            }
                             ?>
                            
                        </div>
                        <div class="btn_box">
                            <button class="btn btn_mid btn_mid_round btn_normal_line btn_like btn_right" onclick="clickReviewHeart(this)"><img src="/views/main/bootstrap/images/icon-heart-o.svg" alt="하트" class="icon"><span class="num"><?php echo element('reviewlikestatus',$val) ?></span></button>
                        </div>
                    </li>
                    <?php         
                        }
                    ?>
                    
                </ul>
                <div class="btn_box_bottom">
                    <a href="" class="btn_more js-btn-review-list-more">더보기 <img src="/views/main/bootstrap/images/icon-angle-down.svg" alt="아래화살표" class="icon"></a>
                </div>
            </section>
            <div class="pd_gnb_bottom" style="height: 24px;background-color: #fff;"></div>
            <div class="btn_fixed_box">
                <button type="button" class="btn btn_circle btn_normal_line btn_write btn-top" id="btnTop"><img src="/views/main/bootstrap/images/icon-backtop.svg" alt="맨위로" class="icon icon_up"></button>
            </div>
            <!-- .main end -->
        </div>
        <!-- .wrap end -->
    </div>
    <script>
        var swiperProfile = new Swiper('.main_profile.swiper-container', {
          spaceBetween: 0,
          effect: 'fade',
          fadeEffect: {
            crossFade: true
          },
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
        });

        

        var swiper = new Swiper('.sect_recommand_ai .items_wrap, .sect_hot_items .items_wrap', {
            slidesPerView: 1.25,
            spaceBetween: 8,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
        var swiper = new Swiper('.hash_list_container', {
            slidesPerView: 'auto',
            spaceBetween: 0,
        });

        // 좋아요 버튼
        var elReviewHeart = document.querySelectorAll('.btn_like');

        var BTN_NORMAL_LINE = 'btn_normal_line';
        var BTN_MAIN_LINE = 'btn_main_line';
        var hrefHeart = 'images/icon-heart.svg';
        var hrefHeartO = 'images/icon-heart-o.svg';

        function changeBtnStyle(el){
            if(el.classList.contains(BTN_NORMAL_LINE)){
                el.classList.remove(BTN_NORMAL_LINE);
                el.classList.add(BTN_MAIN_LINE);
                el.querySelector('.icon').setAttribute('src',hrefHeart);
                el.blur();
            } else if(el.classList.contains(BTN_MAIN_LINE)){
                el.classList.remove(BTN_MAIN_LINE);
                el.classList.add(BTN_NORMAL_LINE);
                el.querySelector('.icon').setAttribute('src',hrefHeartO);
                el.blur();
            }
        }

        // elReviewHeart.forEach(function(el){
        //     el.addEventListener('click',function(){
        //         var elNum = el.querySelector('.num');
        //         var numberLike = elNum.textContent;

        //         if(el.classList.contains(BTN_NORMAL_LINE)){
        //             elNum.textContent = Number(numberLike) + 1;
        //         }else if(el.classList.contains(BTN_MAIN_LINE)){
        //             elNum.textContent = Number(numberLike) - 1;
        //         }
        //         changeBtnStyle(this);
        //     });
        // })
        function clickReviewHeart(el) {
            var elNum = el.querySelector('.num');
            var numberLike = elNum.textContent;

            if(el.classList.contains(BTN_NORMAL_LINE)){
                elNum.textContent = Number(numberLike) + 1;
            }else if(el.classList.contains(BTN_MAIN_LINE)){
                elNum.textContent = Number(numberLike) - 1;
            }
            changeBtnStyle(el);
        }

        // 단순 보여주기용 - 리뷰섹션에서 더보기 클릭시 5개더 노출 
        var elReviewItem = document.querySelector('.js-review-item');
        var elBtnReviewMore = document.querySelector('.js-btn-review-list-more');
        
        elBtnReviewMore.addEventListener('click',function(e){
            e.preventDefault();
            for (let index = 0; index < 5; index++) {
                var addItem = elReviewItem.cloneNode(true);
                var addItemBtn = addItem.querySelector('.btn');
                elReviewItem.insertAdjacentElement('afterend', addItem);
                addItem.querySelector('.num').textContent = Number(addItem.querySelector('.num').textContent) + 5 - index;

                if(addItemBtn.classList.contains(BTN_MAIN_LINE)){
                    addItemBtn.classList.remove(BTN_MAIN_LINE);
                    addItemBtn.classList.add(BTN_NORMAL_LINE);
                    addItemBtn.querySelector('.icon').setAttribute('src',hrefHeartO);
                    addItemBtn.blur();
                }
            }
            this.classList.add('blind');
            // console.log(addItem);
        });

    </script>
    
    
    <!-- <script src="js/header.js"></script> -->
</body>
</html>