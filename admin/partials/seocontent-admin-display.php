<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.seo-kueche.de
 * @since      1.0.0
 *
 * @package    SEOContent
 * @subpackage SEOContent/admin/partials
 */


function myAjax3()
{
    wp_enqueue_script('my-ajax-script4', content_url() . '/plugins/SEOContent/src/scripts/js/main_page.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script4', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}




add_action('scriptTest4', 'myAjax3');
do_action('scriptTest4');
?>
<style>
    <?php include 'admin\css\seocontent-admin.css'; ?>
</style>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">

<script>
    function getHomeUrl() {
        var href = window.location.href;
        var index = href.indexOf('/wp-admin');
        var homeUrl = href.substring(0, index);
        return homeUrl;
    }
    var homeUrl = getHomeUrl();
    document.getElementById('wpcontent').style.paddingLeft = 0

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
</script>
<div class="settingsContainer">
    <div class="header">
        <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

            <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
        </a>
        <nav>
            <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>" id="startseite">Startseite</a>
            <a href="<?php echo admin_url('admin.php?page=content.php'); ?>">Texte erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=image.php'); ?>">Bilder erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">Meta-Daten</a>
            <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/anleitung-plugin-seo-content/">Hilfe und Anleitung</a>
        </nav>
    </div>
    <div class="mainContainer">
        <div class="topText" id="topText">
            <div class="leftText">
                <h2>Optimieren Sie Ihre Seite mit dem SEO Content Plugin</h2>
                <ul role="list">
                    <li data-icon="✓"> <b>Bild-Erstellung</b> – Sie erhalten Bilder mit passenden SEO-Attributen, welche direkt in Ihre Mediathek gespeichert werden.</li>
                    <li data-icon="✓"> <b>Text-Erstellung</b> – Für Sie werden SEO-konforme Texte für die Erstellung von Zielseiten generiert.</li>
                    <li data-icon="✓"> <b>Meta-Daten</b> – Das Plugin optimiert Ihre Meta-Daten auf Knopfdruck nach den gängigen Regeln der Suchmaschinenoptimierung</li>
                </ul>
            </div>
            <button onclick="closePopup()" class="closeButton">
                <span style="font-size: 1.3rem;">×</span>
            </button>
            <div class="rightButton">
                <center>

                    <p>Benötigen Sie weiter Hilfe?</p>
                </center>
                <a target="_blank" href="https://www.seo-kueche.de/leistungen/seo/">
                    <button>Strategische Beratung anfordern</button>
                </a>

            </div>
        </div>
        <script>
            if (getCookie('popup') == 'closed') {
                document.getElementById('topText').style.display = 'none';
            }
        </script>
        <div class="bottomContainer">
            <div class="overview">
                <div class="servicesContainer">
                    <div class="servicesRow">
                        <a href="<?php echo admin_url('admin.php?page=content.php'); ?>">
                            <div class="servicesCard">
                                <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\text-erstellen.png" alt="texte" class="serviceIcon">
                                <h3>Texte erstellen</h3>
                            </div>

                        </a>
                        <a href="<?php echo admin_url('admin.php?page=image.php'); ?>">

                            <div class="servicesCard">
                                <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\bilder-erstellen.png" alt="bilder" class="serviceIcon">
                                <h3>Bilder erstellen</h3>
                            </div>
                        </a>
                    </div>
                    <div class="servicesRow">
                        <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">

                            <div class="servicesCard">
                                <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\meta-daten.png" alt="meta" class="serviceIcon">
                                <h3>Meta-Daten</h3>
                            </div>
                        </a>

                    </div>
                    <div class="rightButton">
                        <a target="_blank" href="https://www.seo-kueche.de/seo-content-plugin/#verbesserungen">
                            <button>Verbesserungsvorschläge</button>
                        </a>

                    </div>
                </div>
            </div>

            <div class="settingsForm">
                <div style="display: flex;  flex-direction: column; width: fit-content;">
                    <h3>Haupteinstellungen</h3>

                    <label for="apiKey">OpenAI Apikey <a target="_blank" href="https://platform.openai.com/overview">
                            <span><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z" />
                                </svg></span>

                        </a>
                    </label>
                    <input type="text" name="apiKey" id="apiKey">
                    <h3>Unternehmenseinstellungen</h3>
                    <p>Tragen Sie hier Ihre Unternehmensdaten ein, um diese später automatisiert in die Texterstellung einfließen zu lassen.</p>
                    <label for="firmenname">Firmenname</label>
                    <input type="text" name="firmenname" id="firmenname">
                    <label for="adresse">Adresse</label>
                    <input type="text" name="adresse" id="adresse">
                    <label for="Gewerbe">Gewerbe</label>
                    <input type="text" name="Gewerbe" id="Gewerbe">
                    <label for="whyUs">2-3 Sätze, die beschreiben, was Sie besonders auszeichnet und von der Konkurrenz abhebt.</label>
                    <input type="text" name="whyUs" id="whyUs">
                    <h3>Marketingeinstellungen</h3>
                    <label for="usps">USP´s</label>
                    <input type="text" name="usps" id="usps" placeholder="USP1, USP2, ...">
                    <label for="cta">Call to Actions</label>
                    <input type="text" name="cta" id="cta" placeholder="CTA1, CTA2, ...">
                    <label for="shortcode">Kontaktformular Shortcode</label>
                    <input type="text" name="shortcode" id="shortcode" placeholder="[shortcode]" style="margin-bottom: 16px;">
                    <div class="rightButton" style="padding-bottom: 20px;">
                        <button onclick="saveSettings()">Einstellungen übernehmen</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>