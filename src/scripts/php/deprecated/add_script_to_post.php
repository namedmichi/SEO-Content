<?php
function gpt_add_script_to_post_function()
{

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
  $postId = isset($_POST['response']) ? wp_kses_post($_POST['response']) : '';
  $faqOutput = isset($_POST['faq']) ? wp_kses_post($_POST['faq']) : '';

  $page = get_post($postId);
  $content = $page->post_content;

  // Check if the post is a page
  if ($page->post_type === 'page') {
    // Append the script tag to the post content
    $script = '<script type="application/ld+json">'  . $faqOutput . ' </script> ';

    $content .= $script;
    $page->post_content = $content;
    wp_update_post($page);
  }
  wp_die();
}
