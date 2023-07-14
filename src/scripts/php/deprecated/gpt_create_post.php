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


try {
    //code...
    $title = isset($_POST['title']) ? wp_kses_post($_POST['title']) : '';
    $inhalt  = isset($_POST['inhalt']) ? wp_kses_post($_POST['inhalt']) : '';
    $excerpt  = isset($_POST['excerpt']) ? wp_kses_post($_POST['excerpt']) : '';
    $typ = isset($_POST['typ']) ? wp_kses_post($_POST['typ']) : '';

    $wordpress_post = array(
        'post_title' => $title,
        'post_content' => $inhalt,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => $typ,
        'post_excerpt' => $excerpt,
    );


    $id = wp_insert_post($wordpress_post);

    echo $id;
} catch (\Throwable $th) {
    echo $th;
}
wp_die();
