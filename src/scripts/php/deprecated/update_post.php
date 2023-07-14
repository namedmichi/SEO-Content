<?php

try {

    require_once(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/wp-load.php');
} catch (\Throwable $th) {
    echo $th;
    try {
        //code...
        require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/wp-load.php');
    } catch (\Throwable $th) {
        echo $th;
    }
}
$pageId = isset($_POST['pageId']) ? $_POST['pageId'] : '';
$newTitle = isset($_POST['newTitle']) ? $_POST['newTitle'] : '';
$newExcerpt = isset($_POST['newExcerpt']) ? $_POST['newExcerpt'] : '';

// Update the page using WordPress functions
$pageData = array(
    'ID' => $pageId,
    'post_title' => $newTitle,
    'post_excerpt' => $newExcerpt
);

echo wp_update_post($pageData);

echo 'Page updated successfully!';
