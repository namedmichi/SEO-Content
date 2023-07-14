<?php


require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');

$path = __DIR__ . '\history.json';
$jsonString = file_get_contents($path);
$jsonData = json_decode($jsonString, true);


$page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
$contentHeader = isset($_POST['contentHeader']) ? wp_kses_post($_POST['contentHeader']) : '';
$contentText = isset($_POST['contentText']) ? wp_kses_post($_POST['contentText']) : '';
$service_count  = isset($_POST['service_count']) ? intval($_POST['service_count']) : 0;

$service_headers = isset($_POST['service_headers']) ? $_POST['service_headers'] : [];
$service_texts = isset($_POST['service_texts']) ? $_POST['service_texts'] : [];



$blockHeader = [
    'post_content' => '<!-- wp:heading {"level":2} --> '  . $contentHeader . ' <!-- /wp:heading -->',
    'post_type' => 'wp_block',
    'post_name' => 'my-paragraph-block',
];
$blockText = [
    'post_content' => '<!-- wp:paragraph --> '  . $contentText . ' <!-- /wp:paragraph -->',
    'post_type' => 'wp_block',
    'post_name' => 'my-paragraph-block',
];

$blocksServiceHeaders = [];
$blocksServiceTexts = [];
for ($i = 1; $i <= $service_count; $i++) {

    if (isset($_POST['service_headers'][$i - 1])) {
        $blocksServiceHeaders[$i - 1] =
            [
                'post_content' => '<!-- wp:heading {"level":2} --> '  . $service_headers[$i - 1]  . ' <!-- /wp:heading -->',
                'post_type' => 'wp_block',
                'post_name' => 'my-paragraph-block',
            ];
        $blocksServiceTexts[$i - 1] =
            [
                'post_content' => '<!-- wp:paragraph --> '  . $service_texts[$i - 1]  . ' <!-- /wp:paragraph -->',
                'post_type' => 'wp_block',
                'post_name' => 'my-paragraph-block',
            ];
    }
}


$block_ids = [];
$block_ids[0] = wp_insert_post($blockHeader);
$block_ids[1] = wp_insert_post($blockText);

$datas = [];


$datas[0] =
    [
        "pageID" => $page_id,
        "blockID" => $block_ids[0]
    ];
$datas[1] =
    [
        "pageID" => $page_id,
        "blockID" => $block_ids[1]
    ];
$inp = file_get_contents($path);
$tempArray = json_decode($inp);
array_push($tempArray, $datas[0]);
array_push($tempArray, $datas[1]);
$idcount = 2;
for ($i = 1; $i <= $service_count; $i++) {
    if ($_POST['service_headers'][$i - 1] !== '') {
        echo "Service nr. " . $i . " added to datas array";
        $block_ids[$idcount] = wp_insert_post($blocksServiceHeaders[$i - 1]);
        $idcount++;
        $block_ids[$idcount] = wp_insert_post($blocksServiceTexts[$i - 1]);
        $idcount--;
        $datas[$idcount] =
            [
                "pageID" => $page_id,
                "blockID" => $block_ids[$idcount]
            ];
        $idcount++;
        $datas[$idcount] =
            [
                "pageID" => $page_id,
                "blockID" => $block_ids[$idcount]
            ];
        $idcount++;
    }
}




$jsonData = json_encode($tempArray, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);


$page = get_post($page_id);

$content = $page->post_content;


$content .= '<!-- wp:block {"ref":' . $block_ids[0]  . '} /-->';
$content .= '<!-- wp:block {"ref":' . $block_ids[1] . '} /-->';
$idcount = 2;
for ($i = 1; $i <= $service_count; $i++) {
    if ($_POST['service_headers'][$i - 1] !== '') {
        echo "Service nr. " . $i . " added to content";
        $content .= '<!-- wp:block {"ref":' . $block_ids[$idcount] . '} /-->';
        $idcount++;
        $content .= '<!-- wp:block {"ref":' . $block_ids[$idcount] . '} /-->';
        $idcount++;
    }
}


$page->post_content = $content;
wp_update_post($page);
echo $content, $page_id;
