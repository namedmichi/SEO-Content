<?
function nmd_create_help_callback()
{
?>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">

    <div class="header">
        <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

            <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
        </a>
        <nav>
            <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">Startseite</a>
            <a href="<?php echo admin_url('admin.php?page=content.php'); ?>">Texte erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=image.php'); ?>">Bilder erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">Titel und Metabeschreibung verbessern</a>
            <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/">Hilfe und Anleitung</a>
        </nav>
    </div>
    <iframe src="https://scribehow.com/embed/How_to_Generate_SEO_Content_for_a_Website__Q3GNJd5mTPeIlDyATigr7A" width="100%" height="640" allowfullscreen frameborder="0"></iframe>
<?
}
