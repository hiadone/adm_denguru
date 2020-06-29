<?php
$cmall_count = $this->board->get_cmall_count();
$cmall =array();


foreach ($cmall_count as $val) 
{
	$cmall[element('brd_id',$val)] = element('rownum',$val);
}
$k = 0;
$is_open = false;
if (element('board_list', $view)) {
	foreach (element('board_list', $view) as $key => $board) {
		$config = array(
			'skin' => 'bootstrap',
			'brd_key' => element('brd_key', $board),
			'brd_id' => element('brd_id', $board),
			'limit' => 5,
			'length' => 40,
			'is_gallery' => '',
			'image_width' => '',
			'image_height' => '',
			'cache_minute' => 1,
			'cmall_count' => element(element('brd_id', $board),$cmall),
			
		);
		if ($k % 2 === 0) {
			echo '<div class="row">';
			$is_open = true;
		}
		echo $this->board->latest($config);
		if ($k % 2 === 1) {
			echo '</div>';
			$is_open = false;
		}
		$k++;
	}
}
if ($is_open) {
	echo '</div>';
	$is_open = false;
}
// $imageUrl='http://biteme.co.kr/data/editor/goods/181002/Artboard 1_200347.jpg';






// $img_src_array= explode('/', $imageUrl);
//                     $imageName = end($img_src_array);
              

//                      str_replace($imageName,rawurlencode($imageName),$imageUrl);







// // $mecab = new \MeCab\Tagger();

// //         // 사전 파일 경로 지정 시

// $mecab = new \MeCab\Tagger(array('-d', '/usr/local/lib/mecab/dic/mecab-ko-dic'));



// // 형태소만 배열로 획득

// // $result = $mecab->parse('테리라떼 테라노 슬리브리스 (아이보리)'); // or $split = mecab_split('안녕하세요? 반갑습니다.');

// $result = $mecab->parse("강아지용 10종 TASTER PACK (맛보기용)");
// // echo $result."<br>";
// $code = array('NNG','NNP');
// $mecab_array = array();
// //결과값에서 줄단위로 분리
//     preg_match_all('/[^EOS](.*)\n/', $result, $find_code);
 
//     //각줄별로 루프를 돌며 텍스트와 태그(코드)값분리
//     for($i=0; $i < count($find_code[0]); $i++)
//     {
//         preg_match('/(.*)(?=\t)/', $find_code[0][$i], $find_text); // text
//         preg_match('/(?<=\t)([^\,]+)/', $find_code[0][$i], $find_tag); // tag
//         //걸러내고자하는 코드가 있을시
//         if(count($code) > 0)
//         {
//             //걸러내려는 코드안에 태그가 포함되는지
//             if(in_array($find_tag[0],$code)
//                 //중복되는 텍스트가 있는지
//                 && in_array($find_text[0],$mecab_array) === false)
//             {
//                 $mecab_array[] = $find_text[0];
// //태그값은 필요 없어 주석
// //              $mecab_array[$i]["code"] = $find_tag[0];
//             }
//         } else {
//             //중복되는 텍스트가 있는지
//             if(in_array($find_text[0],$mecab_array) === false)
//             {
//                 $mecab_array[] = $find_text[0];
// //태그값은 필요 없어 주석
// //              $mecab_array[$i]["code"] = $find_tag[0];
//             }
//         }
//     }
// print_r($mecab_array);

?>

