<?php 
$this->managelayout->add_css(base_url('views/main/bootstrap/css/main.css')); 
$this->managelayout->add_js(base_url('views/main/bootstrap/js/btn_top.js')); 


$data = element('data',element('main',$view));








 ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DENGURU:: 카테고리/스페셜</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</head>
<body>
    <div class="wrap">
        <header class="header06">
            <h1 class="h_logo_box"><a href="index.html" class="h_link"><img src="/views/main/bootstrap/images/logo-horizontal.svg" alt="DENGURU 로고" class="h_logo"></a></h1>
            <div class="h_btn h_btn_mlauto">
                <a href="search.html" class=""><img src="/views/main/bootstrap/images/icon-search-gray.svg" alt="검색"></a>
            </div>
            <div class="h_btn">
                <!-- <span class="has_alarm"><span class="blind">알림이 있습니다</span></span> -->
                <a href="my_notification.html"><img src="/views/main/bootstrap/images/icon-bell-with-dot.svg" alt="알림" class="icon"></a>
            </div>
        </header>

        <nav class="gnb_bottom">
            <h2 class="blind">하단 네비게이션</h2>
            <ul class="gnb_list">
                <li class="gnb_item">
                    <a href="store_rank.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-store.svg" alt="store" class="gnb_icon">
                        <span class="gnb_txt blind">store</span>
                    </a>
                </li>
                <li class="gnb_item active">
                    <a href="category_main.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-category-active.svg" alt="category" class="gnb_icon">
                        <span class="gnb_txt blind">category</span>
                    </a>
                </li>
                <li class="gnb_item">
                    <a href="index.html" class="gnb_link">
                        <img src="/views/main/bootstrap/images/gnb-home.svg" alt="home" class="gnb_icon">
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
            <div class="pd_header06"></div>
            <div class="category_wrap">
                <h2 class="title08">Category</h2>
                <div class="store_list_hash">
                    <div class="store_list_swipe_wrap swiper-container swiper-container-initialized swiper-container-horizontal">
                        <ul class="store_list_swipe_container swiper-wrapper" id="swiper-wrapper-37131b3b9d462552" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
                            <li style="width: 8px; height: 1px; margin-right: 8px;" class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 11"></li>
                            <li class="store_box swiper-slide swiper-slide-next" role="group" aria-label="2 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-all.svg" alt="전체" class="img"></div>
                                    <div class="store_name">전체</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="3 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate03.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-fashion.svg" alt="패션" class="img"></div>
                                    <div class="store_name">패션</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="4 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-food.svg" alt="푸드" class="img"></div>
                                    <div class="store_name">푸드</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="5 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-walk.svg" alt="산책 외출" class="img"></div>
                                    <div class="store_name">산책·외출</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="6 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-move.svg" alt="이동" class="img"></div>
                                    <div class="store_name">이동</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="7 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-living.svg" alt="홈 리빙" class="img"></div>
                                    <div class="store_name">홈·리빙</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="8 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-toy.svg" alt="놀이 장난감" class="img"></div>
                                    <div class="store_name">놀이·장난감</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="9 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-clean.svg" alt="미용 목욕 위생 배변" class="img"></div>
                                    <div class="store_name">미용·목욕·위생·배변</div>
                                </a>
                            </li>
                            <li class="store_box swiper-slide" role="group" aria-label="10 / 11" style="margin-right: 8px;">
                                <a href="category_click_cate02.html">
                                    <div class="thumb_box"><img src="/views/main/bootstrap/images/icon-cate-etc.svg" alt="기타" class="img"></div>
                                    <div class="store_name">기타</div>
                                </a>
                            </li>
                            <li style="width: 8px; height: 1px; margin-right: 8px;" class="swiper-slide" role="group" aria-label="11 / 11"></li>
                        </ul>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                </div>
            </div>
            <div class="special_wrap">
                <h2 class="title08">Special</h2>
                <div class="store_list_hash">
                    <div class="title_hash">#댕구루픽</div>
                </div>
                <ul class="banner_list">
                    <?php 
                    $i=0;
                    if(element('list',$data))
                        foreach(element('list',$data) as $val){
                        $i++;
                     ?>
                    <li class="banner_box">
                        <a href="<?php echo site_url('main/category_sub/'.element('egr_id', $val)); ?>" class="img_box">
                            <img src="<?php echo element('egr_image_url',$val) ?>" alt="<?php echo element('egr_title',$val) ?>" class="img">
                        </a>
                    </li>
                    <?php 
                    
                       
                    }
                    ?>
                    
                    
                </ul>
            </div>

            <!-- <div class="pd_gnb_bottom"></div> -->
            <div class="pd_gnb_bottom" style="height: 24px;background-color: #fff;"></div>
            <div class="btn_fixed_box">
                <button type="button" class="btn btn_circle btn_normal_line btn_write btn-top" id="btnTop"><img src="/views/main/bootstrap/images/icon-backtop.svg" alt="맨위로" class="icon icon_up"></button>
            </div>
        </div>
    </div>

    <script>
        var swiper = new Swiper('.store_list_swipe_wrap', {
            slidesPerView: 'auto',
            spaceBetween: 8,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

    </script>
     
     

</body>
</html>