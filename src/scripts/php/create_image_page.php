<?php


// Fügt das Bilder erstellen Menü in die obere Admin leiste ein



// Fügt das Bilder erstellen Menü in die Werkzeug leiste ein
add_action('admin_menu', 'nmd_image_page');

function nmd_image_page()
{

    add_submenu_page(
        'tools.php', // parent page slug
        'Bilder Erstellen',
        'Bilder Erstellen',
        'edit_posts',
        'nmd_create_image',
        'nmd_create_image_callback',
        0 // menu position
    );
}


function myAjax()
{
    wp_enqueue_script('my-ajax-script2', content_url() . '/plugins/SEOContent/src/scripts/js/ask_gpt_image_page.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script2', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}


add_action('injectAjax2', 'myAjax');
function nmd_create_image_callback()
{
    do_action('injectAjax2');

?>

    <div id="overlay">

        <div class="lds-roller">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <p style="position: absolute;top: 16px;left: 16px;">Loading</p>
            <span class="overlayBackground">
                <p id="loadingText" style="margin-bottom: 82px;">some Text</p>
            </span>
        </div>

    </div>
    <script>
        document.getElementById('wpcontent').style.paddingLeft = 0
    </script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/src/style/image_page.css">
    <div class="header">
        <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

            <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
        </a>
        <nav>
            <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">Startseite</a>
            <a href="<?php echo admin_url('admin.php?page=content.php'); ?>">Texte erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=image.php'); ?>" id="bilderErstellem">Bilder erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">Meta-Daten</a>
            <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/anleitung-plugin-seo-content/">Hilfe und Anleitung</a>
        </nav>
    </div>
    <div class="background">




        <div class="wrap">

            <div class="mitte container">
                <div class="mainInputs">
                    <div style="display: flex; align-items: center; ">
                        <h1 class="wp-heading-inline">Bilder erstellen lassen</h1>


                    </div>
                    <div class="flex_button_header">

                        <div class="flexAlignMiddle">
                            <select name="count" id="count" aria-placeholder="Anzahl der Bilder">
                                <option value="" disabled selected>Anzahl der Bilder</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <button class="button action" onclick="create_image()">Generieren</button>
                            <button class="button action" onclick="add_image()">Erzeugte Bilder hinzufügen</button>

                        </div>




                    </div>
                    <textarea placeholder="Thema" name="nmd_image_prompt" id="nmd_image_prompt"></textarea>
                    <div class="bilder">
                        <div class="imageFlexContainer">

                            <div class="fakeImage"><img id="nmd_image_1" alt=""></div>
                            <input type="checkbox" name="selectImage" id="selectImage">
                            <button class="button action" onclick="editImage(1)">Bearbeiten</button>
                        </div>
                        <div class="imageFlexContainer">

                            <div class="fakeImage"><img id="nmd_image_2" alt=""></div>
                            <input type="checkbox" name="selectImage" id="selectImage">
                            <button class="button action" onclick="editImage(2)">Bearbeiten</button>

                        </div>
                        <div class="imageFlexContainer">

                            <div class="fakeImage"><img id="nmd_image_3" alt=""></div>
                            <input type="checkbox" name="selectImage" id="selectImage">
                            <button class="button action" onclick="editImage(3)">Bearbeiten</button>

                        </div>
                    </div>

                </div>
            </div>
            <div class="editImage container">
                <h2 class="wp-heading-inline">Bilder bearbeiten</h2>
                <div class="editImageContainer">
                    <div>
                        <div class=" toolbar">
                            <label title="Legt die Stiftdicke fest" class="spaced" for="pen-size"><svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path d="M339.3 367.1c27.3-3.9 51.9-19.4 67.2-42.9L568.2 74.1c12.6-19.5 9.4-45.3-7.6-61.2S517.7-4.4 499.1 9.6L262.4 187.2c-24 18-38.2 46.1-38.4 76.1L339.3 367.1zm-19.6 25.4l-116-104.4C143.9 290.3 96 339.6 96 400c0 3.9 .2 7.8 .6 11.6C98.4 429.1 86.4 448 68.8 448H64c-17.7 0-32 14.3-32 32s14.3 32 32 32H208c61.9 0 112-50.1 112-112c0-2.5-.1-5-.2-7.5z" />
                                </svg></label>
                            <input class="spaced" type="range" id="pen-size" min="5" max="60" value="3">
                            <br>
                            <div>
                                <label title="Radierer" for="erase"><svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 576 512" style="margin-right: 8px;"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M290.7 57.4L57.4 290.7c-25 25-25 65.5 0 90.5l80 80c12 12 28.3 18.7 45.3 18.7H288h9.4H512c17.7 0 32-14.3 32-32s-14.3-32-32-32H387.9L518.6 285.3c25-25 25-65.5 0-90.5L381.3 57.4c-25-25-65.5-25-90.5 0zM297.4 416H288l-105.4 0-80-80L227.3 211.3 364.7 348.7 297.4 416z" />
                                    </svg></label>
                                <input type="checkbox" name="erase" id="erase">
                            </div>
                        </div>
                    </div>
                    <div id="image-container">
                        <div style="display: flex; flex-direction: column">
                            <button id="upload_image_button" class="button">Bild aus Mediathek einfügen</button>
                            <input type="file" id="image-upload" accept="image/*">
                        </div>
                        <div style="position: relative;">
                            <canvas id="image-canvas"></canvas>
                            <canvas id="tempCanvas" style="position: absolute; top: 0;left: 0; "></canvas>
                        </div>
                        <canvas id="image-canvas-hidden"></canvas>
                        <img id="editedImage" src="" alt="">
                    </div>
                    <textarea name="editPrompt" id="editPrompt" cols="30" rows="1" placeholder="Hier Prompt für die Bildbearbeitung eingeben"></textarea>
                    <button class="button action" id="submit-button">Inpaint absenden</button>
                    <!-- <button class="button action" id="submit-button" onclick="imageVariation()">Variante erstellen(ohne Prompt)</button> -->
                    <button class="button action" id="submit-button" onclick="reuseImage()">Erstelltes Bild in die bearbeitung senden</button>
                    <button class="button action" onclick="saveEditedImage()">Bild speichern</button>
                    <input id="image_url" type="text" name="image_url" style="display: none;" />

                </div>
            </div>
        </div>
    </div>

    <script>

    </script>

<?php

}
