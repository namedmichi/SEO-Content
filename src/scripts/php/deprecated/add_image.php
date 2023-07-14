<?php
// example:
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
$description = 'some description';
$files = isset($_POST['image_urls']) ? $_POST['image_urls'] : '';
$count = isset($_POST['count']) ? $_POST['count'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
echo $files;
if (!class_exists('WP_Http')) {
    include_once(ABSPATH . WPINC . '/class-http.php');
}


for ($i = 1; $i <= $count; $i++) {
    $http = new WP_Http();

    $response = $http->request($files[$i - 1]);
    if ($response['response']['code'] !== 200) {
        echo "fail1";
        return false;
    }
    $upload = wp_upload_bits("$title.jpg", null, $response['body']);
    echo $upload['error'];
    echo $upload['file'];
    $file_path = $upload['file'];
    $file_name = basename($file_path);
    $file_type = wp_check_filetype($file_name, null);
    $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
    $wp_upload_dir = wp_upload_dir();

    $post_info = array(
        'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
        'post_mime_type' => $file_type['type'],
        'post_title'     => $title,
        'post_excerpt' => "",
        'post_content'   => "",
        'post_status'    => 'inherit',
    );

    $attach_id = wp_insert_attachment($post_info, $file_path);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
    wp_update_attachment_metadata($attach_id,  $attach_data);
    update_post_meta($attach_id, '_wp_attachment_image_alt', $title);
    echo $attach_id;
}
