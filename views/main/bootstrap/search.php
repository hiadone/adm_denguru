<?php 
$this->managelayout->add_css(base_url('views/main/bootstrap/css/main.css')); 



$data = element('data',element('main',$view));

$other = element('other',$view);






 ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DENGURU :: 영양제 검색결과</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</head>
<body>
    <div class="wrap">
        <header class="header02">
            <h1 class="blind">댕구루 검색페이지</h1>
            <a href="javascript:document.referrer? history.back() : location.href = 'search.html'" class="btn_goback"><img src="/views/main/bootstrap/images/icon-goback.svg" alt="뒤로가기" class="icon"></a>
            <div class="h_search_box">
                <form action="">
                    <label for="inpSearch" class="lab_search"><img src="/views/main/bootstrap/images/icon-search.svg" alt="검색" class="icon_search"></label>
                    <input type="search" name="inpSearch" class="inp_search" id="inpSearch" value="<?php echo element('oth_title',$other) ?>">
                    <span class="search_result">
                        <?php echo number_format(element('total_rows',$data)) ?>건
                    </span>
                    <button type="button" class="btn_del btn_linkstyle blind" id="btnDel"><img src="/views/main/bootstrap/images/icon-del.svg" alt="취소" class="icon"></button>
                </form>
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
            <section class="sect03 sect_filter_cate mt_head02 swiper-container swiper-container-initialized swiper-container-horizontal swiper-container-free-mode">
                <h2 class="blind">필터설정</h2>
                <ul class="btn_box swiper-wrapper" id="swiper-wrapper-f2949386a7e7c615" aria-live="polite">
                    <li class="filter_btn swiper-slide swiper-slide-active" role="group" aria-label="1 / 4" style="margin-right: 8px;">
                        <button type="button" class="btn_filter_popup btn btn_normal_line btn_mid btn_mid_round" aria-controls="tab-panel01">가격</button>
                    </li>
                    <!-- <li class="filter_btn swiper-slide">
                        <button type="button" class="btn_filter_popup btn btn_main btn_mid btn_mid_round" aria-controls="tab-panel01">27,000 ~ 31,000</button>
                    </li> -->
                    <li class="filter_btn swiper-slide swiper-slide-next" role="group" aria-label="2 / 4" style="margin-right: 8px;">
                        <button type="button" class="btn_filter_popup btn btn_normal_line btn_mid btn_mid_round" aria-controls="tab-panel02">사이즈</button>
                    </li>
                    <li class="filter_btn swiper-slide" role="group" aria-label="3 / 4" style="margin-right: 8px;">
                        <button type="button" class="btn_filter_popup btn btn_normal_line btn_mid btn_mid_round" aria-controls="tab-panel03">연령</button>
                    </li>
                    <li class="filter_btn swiper-slide" role="group" aria-label="4 / 4" style="margin-right: 8px;">
                        <button type="button" class="btn_filter_popup btn btn_normal_line btn_mid btn_mid_round" aria-controls="tab-panel04">카테고리</button>
                    </li>
                    <!-- <li class="filter_btn swiper-slide">
                        <button type="button" class="btn_filter_popup btn btn_main btn_mid btn_mid_round" aria-controls="tab-panel04">카테고리 <span class="num">2</span></button>
                    </li> -->
                </ul>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></section>
            <section class="search_result sect04">
                <h2 class="blind">검색결과 목록</h2>
                <div class="search_result_top_bar">
                    <div class="filter_info">
                        <img src="/views/main/bootstrap/images/icon-filter.svg" alt="필터" class="icon">
                        <?php 
                            if(element('member',$data)){
                            $diff = abs(strtotime(date('Y-m-d')) - strtotime(element('pet_birthday',element('member',$data))));

                            $years = floor($diff / (365*60*60*24));
                            $months = floor(($diff) / (30*60*60*24));

                            if(element('pet_sex',element('member',$data))  == '1')
                                echo '남아 ';
                            elseif(element('pet_sex',element('member',$data))  == '2')
                                echo '여아 ';

                            $cat_value = '';
                            // if(element('pet_form_str',$pval)) 
                            //     echo '<li class="profile_tag">'.element('pet_form_str',$pval).'</li>';
                            if($years < 1) 
                                $cat_value = '퍼피 ';
                            elseif($years < 7) 
                                $cat_value = '어덜트 ';
                            elseif($years > 6) 
                                $cat_value = '시니어 ';
                                echo $cat_value ;
                            if(element('ckd_size_str',element('member',$data))) 
                                echo element('ckd_size_str',element('member',$data)).' ';
                            if(element('pet_kind',element('member',$data))) 
                                echo element('pet_kind',element('member',$data));
                        }
                        ?>
                        
                    </div>
                    <div class="select_sort_box">
                        <select name="filterSort" id="filterSort" class="select_sort">
                            <option value="">인기순</option>
                            <option value="">신상품순</option>
                            <option value="">저가순</option>
                            <option value="">고가순</option>
                        </select>
                    </div>
                </div>
                <div class="search_result_items">
                    <div class="items_wrap">
                        <ul class="item_list03">
                            <?php 
                            $i=0;
                            if(element('list',$data))
                                foreach(element('list',$data) as $val){
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
                                        <div class="item_price"><?php echo element('cit_price_sale',$val) ?> </div>
                                    </div>
                                </a>
                            </li>
                            <?php 
                            
                               
                            }
                            ?>


                           
                        </ul>
                    </div>
                </div>
            <section class="sect01">
                <div class="btn_box_bottom">
                    <a href="<?php echo site_url('main/search/'.element('oth_id', $view).'/'.element('oth_id', $view).'/'.(element('page', $view) + 1)); ?>" class="btn_more js-btn-review-list-more">다음페이지<img src="/views/main/bootstrap/images/icon-angle-down.svg" alt="아래화살표" class="icon"></a>
                </div>
            </section>
            <div class="pd_gnb_bottom" style="height: 32px;background-color: #fff;"></div>
            <div class="btn_fixed_box">
                <button type="button" class="btn btn_circle btn_normal_line btn_write btn-top" id="btnTop" style="display: none;"><img src="/views/main/bootstrap/images/icon-backtop.svg" alt="맨위로" class="icon icon_up"></button>
            </div>
        </div>
        <div class="popup_wrap" id="popupWrap">
            <div class="popup_filter_container">
                <h2 class="blind">필터설정</h2>
                <div class="popup_top_bar" role="tablist">
                    <button class="btn_tab btn_tab01 btn_linkstyle" type="button" role="tab" aria-controls="tab-panel01">가격</button>
                    <button class="btn_tab btn_tab02 btn_linkstyle" type="button" role="tab" aria-controls="tab-panel02">사이즈</button>
                    <button class="btn_tab btn_tab03 btn_linkstyle" type="button" role="tab" aria-controls="tab-panel03">연령</button>
                    <button class="btn_tab btn_tab04 btn_linkstyle" type="button" role="tab" aria-controls="tab-panel04">카테고리</button>
                </div>
                <div class="popup_main">
                    <div id="tab-panel01" class="tab_panel filter_price_container" role="tabpanel">
                        <h3 class="blind">가격</h3>
                        <div class="top_box">
                            
                            <div class="btn_filter_info_box popover_container">
                                <button type="button" class="btn_filter_info btn_linkstyle" onclick="togglePopover('popover01')">
                                    <img src="/views/main/bootstrap/images/icon-info.svg" alt="안내" class="icon">
                                </button>
                                <div class="popover popover_main popover_right_top popover_filter_info" id="popover01">그래프의 높이가 높을수록 해당 가격대에 상품이 많다는 뜻 이에요.</div>
                            </div>
                        </div>
                        <div class="img_box"><img src="/views/main/bootstrap/images/filter-graph_02.png" alt="" class="img"></div>
                        <div class="price_range_txt">
                            <span class="price price_row">27,000</span>
                            ~
                            <span class="price price_high">315,000+</span>
                        </div>
                    </div>
                    <div id="tab-panel02" class="filter_cate_container tab_panel" role="tabpanel">
                        <div class="filter_cate_box show" id="filterCateSizeBig">
                            <div class="filter_cate_header">
                                전체 <span class="filter_item_num">1,234</span>
                            </div>
                            <ul class="filter_cate_big_box">
                                <!-- <li class="filter_cate_big">
                                    <button type="button" class="btn_linkstyle btn_filter_cate_big" onclick="openSmallCate('filterCateSizeBig', 'filterCateSizeSmall01')"> 초소형견 <span class="filter_item_num">434</span></button>
                                </li> -->
                                <li class="filter_cate_big">
                                    <button type="button" class="btn_linkstyle btn_filter_cate_big" onclick="openSmallCate('filterCateSizeBig', 'filterCateSizeSmall02')"> 소형견 <span class="filter_item_num">500</span></button>
                                </li>
                                <li class="filter_cate_big">
                                    <button type="button" class="btn_linkstyle btn_filter_cate_big" onclick="openSmallCate('filterCateSizeBig', 'filterCateSizeSmall03')"> 중형견 <span class="filter_item_num">200</span></button>
                                </li>
                                <li class="filter_cate_big">
                                    <button type="button" class="btn_linkstyle btn_filter_cate_big" onclick="openSmallCate('filterCateSizeBig', 'filterCateSizeSmall04')"> 대형견 <span class="filter_item_num">100</span></button>
                                </li>
                            </ul>
                        </div>
                        <!-- <div class="filter_cate_box" id="filterCateSizeSmall01">
                            <div class="filter_cate_header">
                                초소형견 <span class="filter_item_num">434</span>
                                <button class="btn_linkstyle btn_filter_cate_del" onclick="closeSmallCate('filterCateSizeBig', 'filterCateSizeSmall01')"><img src="/views/main/bootstrap/images/icon-del.svg" alt="닫기" class="icon"></button>
                            </div>
                            <ul class="filter_cate_small_box">
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed001" class="inp_blind" hidden>
                                    <label for="filterPetSizeBreed001" class="lab_checkbox">타이니 토이 푸들 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed00" class="inp_blind" hidden>
                                    <label for="filterPetSizeBreed00" class="lab_checkbox">티컵푸들 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed003" class="inp_blind" hidden>
                                    <label for="filterPetSizeBreed003" class="lab_checkbox">브뤼셀 그리펀 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed004" class="inp_blind" hidden>
                                    <label for="filterPetSizeBreed004" class="lab_checkbox">제페니스 친 <span class="filter_item_num">100</span></label>
                                </li>
                            </ul>
                        </div> -->
                        <div class="filter_cate_box" id="filterCateSizeSmall02">
                            <div class="filter_cate_header">
                                소형견 <span class="filter_item_num">500</span>
                                <button class="btn_linkstyle btn_filter_cate_del" onclick="closeSmallCate('filterCateSizeBig', 'filterCateSizeSmall02')"><img src="/views/main/bootstrap/images/icon-del.svg" alt="닫기" class="icon"></button>
                            </div>
                            <ul class="filter_cate_small_box">
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed005" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed005" class="lab_checkbox">노르웨지안 룬데훈트 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed006" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed006" class="lab_checkbox">볼로네즈 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed007" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed007" class="lab_checkbox">잉글리시 토이 스패니얼 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed008" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed008" class="lab_checkbox">허배너스 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed009" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed009" class="lab_checkbox">미니어처 푸들 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed010" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed010" class="lab_checkbox">토이 푸들 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed011" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed011" class="lab_checkbox">미니어처 슈나우저 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed012" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed012" class="lab_checkbox">아펜핀셔 <span class="filter_item_num">100</span></label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter_cate_box" id="filterCateSizeSmall03">
                            <div class="filter_cate_header">
                                중형견 <span class="filter_item_num">500</span>
                                <button class="btn_linkstyle btn_filter_cate_del" onclick="closeSmallCate('filterCateSizeBig', 'filterCateSizeSmall03')"><img src="/views/main/bootstrap/images/icon-del.svg" alt="닫기" class="icon"></button>
                            </div>
                            <ul class="filter_cate_small_box">
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed037" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed037" class="lab_checkbox">글렌 오브 이말 테리어 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed038" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed038" class="lab_checkbox">브리타니 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed039" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed039" class="lab_checkbox">삽살개 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed040" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed040" class="lab_checkbox">스카이 테리어 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed041" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed041" class="lab_checkbox">시코쿠 켄 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed042" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed042" class="lab_checkbox">실리엄 테리어 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed043" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed043" class="lab_checkbox">오스트레일리언 캐틀 도그 <span class="filter_item_num">100</span></label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter_cate_box" id="filterCateSizeSmall04">
                            <div class="filter_cate_header">
                                대형견 <span class="filter_item_num">500</span>
                                <button class="btn_linkstyle btn_filter_cate_del" onclick="closeSmallCate('filterCateSizeBig', 'filterCateSizeSmall04')"><img src="/views/main/bootstrap/images/icon-del.svg" alt="닫기" class="icon"></button>
                            </div>
                            <ul class="filter_cate_small_box">
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed084" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed084" class="lab_checkbox">그레이트 스위스 마운틴 도그 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed085" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed085" class="lab_checkbox">네오폴리탄 마스티프 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed086" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed086" class="lab_checkbox">뉴펀들랜드 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed087" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed087" class="lab_checkbox">도고 아르헨티 <span class="filter_item_num">100</span>노</label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed088" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed088" class="lab_checkbox">도사견 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed089" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed089" class="lab_checkbox">러프 콜리 <span class="filter_item_num">100</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetSizeBreed" id="filterPetSizeBreed090" class="inp_blind" hidden="">
                                    <label for="filterPetSizeBreed090" class="lab_checkbox">잉글리시 마스티프 <span class="filter_item_num">100</span></label>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div id="tab-panel03" class="tab_panel filter_cate_container" role="tabpanel">
                        <div class="filter_cate_box show">
                            <div class="filter_cate_header">
                                전체 <span class="filter_item_num">1,234</span>
                            </div>
                            <ul class="filter_cate_small_box">
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetAge" id="filterPetAge01" class="inp_blind" hidden="">
                                    <label for="filterPetAge01" class="lab_checkbox">퍼피(1살 미만) <span class="filter_item_num">500</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetAge" id="filterPetAge02" class="inp_blind" hidden="">
                                    <label for="filterPetAge02" class="lab_checkbox">어덜트(1살 이상 ~ 7살 이하) <span class="filter_item_num">234</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetAge" id="filterPetAge03" class="inp_blind" hidden="">
                                    <label for="filterPetAge03" class="lab_checkbox">시니어(7살 이상) <span class="filter_item_num">500</span></label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="tab-panel04" class="tab_panel filter_cate_container" role="tabpanel">
                        <div class="filter_cate_box show">
                            <div class="filter_cate_header">
                                건강관리 <span class="filter_item_num">12,345</span>
                            </div>
                            <ul class="filter_cate_small_box">
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetItemCate" id="filterPetItemCate01" class="inp_blind" hidden="">
                                    <label for="filterPetItemCate01" class="lab_checkbox">구강청결제 <span class="filter_item_num">500</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetItemCate" id="filterPetItemCate02" class="inp_blind" hidden="">
                                    <label for="filterPetItemCate02" class="lab_checkbox">구강티슈 <span class="filter_item_num">234</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetItemCate" id="filterPetItemCate03" class="inp_blind" hidden="">
                                    <label for="filterPetItemCate03" class="lab_checkbox">눈/귀 관리용품 <span class="filter_item_num">500</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetItemCate" id="filterPetItemCate04" class="inp_blind" hidden="">
                                    <label for="filterPetItemCate04" class="lab_checkbox">영양제 <span class="filter_item_num">234</span></label>
                                </li>
                                <li class="filter_cate_small">
                                    <input type="checkbox" name="filterPetItemCate" id="filterPetItemCate05" class="inp_blind" hidden="">
                                    <label for="filterPetItemCate05" class="lab_checkbox">유산균 <span class="filter_item_num">500</span></label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="loading">
                        <img src="/views/main/bootstrap/images/icon-load01.svg" alt="" class="icon_load">
                    </div>
                </div>
                <div class="popup_bottom">
                    <button class="btn btn_linkstyle btn_refresh"><img src="/views/main/bootstrap/images/icon-refresh-gray.svg" alt="refresh" class="icon"> <span class="filter-cate-js"></span> 재설정</button>
                    <button type="button" class="btn btn_accent btn_half" id="btnShowItem">200개 상품보기</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="js/header.js"></script> -->
    <script src="js/search_filter.js"></script>
    <script>
        var swiper = new Swiper('.sect_filter_cate', {
            slidesPerView: 'auto',
            spaceBetween: 8,
            freeMode: true,
        });

        var elPopoverAll = document.querySelectorAll('.popover');

        function showPopover(id) {
            var elPopover = document.getElementById(id);

            elPopover.classList.add(SHOW);
            console.log(id);
        }
        function togglePopover(id) {
            var elPopover = document.getElementById(id);

            if(elPopover.classList.contains(SHOW)){
                elPopover.classList.remove(SHOW);
            } else if(!elPopover.classList.contains(SHOW)){
                elPopover.classList.add(SHOW);
            }
        }
        document.getElementById('popupWrap').addEventListener('click',function(e){
            if(e.target === this){
                elTabPanelAll.forEach(function(el){
                    if(el.classList.contains(SHOW)){
                        closeFilterPopup(el);
                    }
                });
            };
        });

    </script>
    <script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
    <script src="js/btn_top.js"></script>

</body>
</html>