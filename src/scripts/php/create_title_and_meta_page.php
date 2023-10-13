<?php

function testScriptMeta()
{
    wp_enqueue_script('my-ajax-script-meta', content_url() . '/plugins/SEOContent/src/scripts/js/ask_gpt_title_meta.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script-meta', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('my-ajax-script-meta', 'nmd', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}


add_action('scriptTestMeta', 'testScriptMeta');

function nmd_create_title_and_meta_callback()
{

    do_action('scriptTestMeta');
    $metaPromptTemplates = content_url() . '/plugins/SEOContent/src/scripts/php/metaPromptTemplates.json';
    $promptPath = content_url() . '/plugins/SEOContent/src/scripts/php/prompts.json';

    try {


        $jsonString = file_get_contents($promptPath);
        $prompts = json_decode($jsonString, true);
        $jsonString2 = file_get_contents($metaPromptTemplates);
        $metaPromptTemplates = json_decode($jsonString2, true);
    } catch (Exception $e) {
    }
?>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">

    <script>
        document.getElementById('wpcontent').style.paddingLeft = 0
    </script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/src/style/title_and_meta_page.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <div id="overlay" style="display: none;">

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


    <div class="header">
        <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

            <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
        </a>
        <nav>
            <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">Startseite</a>
            <a href="<?php echo admin_url('admin.php?page=content.php'); ?>">Texte erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=image.php'); ?>">Bilder erstellen</a>
            <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>" id="title_meta">Meta-Daten</a>
            <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/anleitung-plugin-seo-content/">Hilfe und Anleitung</a>
        </nav>
    </div>
    <div class="wrapper">


        <div class="top">
            <div class="top_left">
                <div class="topTitle">
                    <h1 class="wp-heading-inline"> Titel und Metabeschreibung neu erstellen</h1>
                    <div>

                        <button class="button action" type="button" onclick="getPages('pages')">Seiten-meta laden</button>
                        <button class="button action" type="button" onclick="getPages('posts')">Beiträge-meta laden</button>

                    </div>
                </div>
                <label for="marketing">Marketingeinstellungen mit in die Prompts übernehmen</label>
                <input type="checkbox" name="marketing" id="marketing">
                <div class="actions">
                    <div id="buttonBar" style="display: none; flex-wrap: wrap;">
                        <div class="firstButtons">

                            <select name="action" id="nmd_multiple_action">
                                <option value="" disabled selected>Mehrfachaktionen</option>
                                <option value="submit">Übernehmen</option>
                            </select>
                            <button onclick="multipleAction()" class="button action">Übernehmen</button>
                        </div>
                        <div class="secondButtons">


                            <select name="action" id="nmd_style">
                                <option value="" disabled selected>Schreibstil</option>
                                <option value="serious">Seriös</option>
                                <option value="authoritative">Bestimmt</option>
                                <option value="emotional">Emotional</option>
                                <option value="empathetic">Empathisch</option>
                                <option value="formal">Formell</option>
                                <option value="friendly">Freundlich</option>
                                <option value="humorous">Humorvoll</option>
                                <option value="informal">Informell</option>
                                <option value="ironic">Ironisch</option>
                                <option value="cold">Kalt</option>
                                <option value="clinical">Klinisch</option>
                                <option value="optimistic">Optimistisch</option>
                                <option value="pessimistic">Pessimistisch</option>
                                <option value="playful">Spielerisch</option>
                                <option value="sarcastic">Sarkastisch</option>
                                <option value="sympathetic">Sympathisch</option>
                                <option value="tentative">Vorsichtig</option>
                                <option value="promotional">Werblich</option>
                                <option value="confident">Zuversichtlich</option>
                                <option value="cynical">Zynisch</option>
                            </select>

                            <button class="button action" onclick="generateNewSnippets()">Generieren</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="promting">

            <div>
                <div id="sonderTab" class="tab">
                    <div style="display: flex;  align-items: center;" onclick="showTab('sonder', 3)">
                        <h2>Sonderzeichen</h2>
                        <span id="arrowUp3" style="margin-left: auto; margin-right: 1rem">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                            </svg>
                        </span>
                        <span id="arrowDown3" style="margin-left: auto; margin-right: 1rem; display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                            </svg>
                        </span>
                    </div>
                    <div id="sonderContainer" style="display: none;">


                        <div class="flex-buttons" id="nmd_special_select">
                            <div> <button style="border: 1px solid black;" onclick="addSpecialCharacter(this ,'►')">►</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'◄')">◄</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'▲')">▲</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'▼')">▼</button> </div>
                            <div> <button style="border: 1px solid black;" onclick="addSpecialCharacter(this ,'✓')">✓</button> </div>
                            <div> <button style="border: 1px solid black;" onclick="addSpecialCharacter(this ,'✅')">✅</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'✘')">✘</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'✚')">✚</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'☆')">☆</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'★')">★</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'✪')">✪</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'♥')">♥</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'☎')">☎</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'✈')">✈</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'☀')">☀</button> </div>
                            <div> <button onclick="addSpecialCharacter(this ,'☂')">☂</button> </div>

                        </div>
                    </div>
                </div>

            </div>

            <div>
                <div id="titleTab" class="tab">
                    <div style="display: flex;  align-items: center;" onclick="showTab('title', 1)">
                        <h2>Anweisung Titel</h2>
                        <span id="arrowUp1" style="margin-left: auto; margin-right: 1rem">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                            </svg>
                        </span>
                        <span id="arrowDown1" style="margin-left: auto; margin-right: 1rem; display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                            </svg>
                        </span>
                    </div>
                    <div id="titleContainer" style="display: none;">

                        <h4>Anweisung für KI</h4>
                        <textarea name="promtTitel" id="promtTitel" cols="40" rows="6" style="resize: none;"><?php echo $prompts['newTitlePrompt'] ?></textarea>
                    </div>
                </div>

            </div>

            <div>
                <div id="metaTab" class="tab">
                    <div style="display: flex;  align-items: center;" onclick="showTab('meta', 2)">
                        <h2>Anweisung Beschreibung</h2>
                        <span id="arrowUp2" style="margin-left: auto; margin-right: 1rem">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                            </svg>
                        </span>
                        <span id="arrowDown2" style="margin-left: auto; margin-right: 1rem; display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                            </svg>
                        </span>
                    </div>
                    <div id="metaContainer" style="display: none;">
                        <h4>Anweisung für KI</h4>
                        <textarea name="promtMeta" id="promtMeta" cols="40" rows="6" style="resize: none;"><?php echo $prompts['newMetaPrompt'] ?></textarea>

                    </div>
                </div>

            </div>
            <div>
                <div id="templateTab" class="tab">
                    <div style="display: flex;  align-items: center;" onclick="showTab('template', 3)">
                        <h2>Vorlagen</h2>
                        <span id="arrowUp3" style="margin-left: auto; margin-right: 1rem">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                            </svg>
                        </span>
                        <span id="arrowDown3" style="margin-left: auto; margin-right: 1rem; display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                            </svg>
                        </span>
                    </div>
                    <div id="templateContainer" class="templateContainer" style="display: none;">

                        <!-- <button class="button button-primary" onclick="template_default()">Templates Zurücksetzen</button> -->
                        <?php
                        $i = 0;
                        $folderCount = 0;
                        $subFolderCount = 0;
                        $currentFolder = "";
                        $currentSubFolder = "";

                        foreach ($metaPromptTemplates as $index => $element) {
                            $currentFolder = $index;
                            echo "<div class='folderTab'>";
                            echo "<div class='folderHeaderFlex' onclick='showFolder($folderCount)'>";
                            echo "<h2 style='margin-top: 0px'  >$index</h2>";
                            echo '<span class="editPen" onclick="editFolder(' . "'"  . $currentFolder . "'"  .  ')">   <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>  </span>';
                            echo '<span onclick="delete_template_Folder(' . "'"  . $currentFolder . "'"  .  ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                            echo '<span id="folderArrowUp' . $folderCount . '" style=" margin-right: 1rem">';
                            echo ' <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                            echo '<path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />';
                            echo '  </svg>';
                            echo ' </span>';
                            echo '<span id="folderArrowDown' . $folderCount . '" style=" margin-right: 1rem; display: none;">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                            echo '<path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />';
                            echo ' </svg>';
                            echo '</span>';
                            echo "</div>";
                            echo "<div id='folderContainer$folderCount' class='folderContainer'>";
                            foreach ($element as $index => $element) {
                                $currentSubFolder = $index;
                                echo "<div class='folderTab'>";
                                echo "<div class='folderHeaderFlex' onclick='showSubFolder("  . $subFolderCount . ")'>";
                                echo "<h3  class='subFolderHeader'>$index</h3>";
                                echo '<span class="editPen" onclick="editFolder(' . "'"   . $currentSubFolder  . "'"  .  ')">   <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>  </span>';
                                echo '<span onclick="delete_template_subFolder(' . "'"  . $currentFolder . "','" . $currentSubFolder  . "'"  .  ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                                echo '<span id="subFolderArrowUp' . $subFolderCount . '" style=" margin-right: 1rem">';
                                echo ' <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                                echo '<path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />';
                                echo '  </svg>';
                                echo ' </span>';
                                echo '<span id="subFolderArrowDown' . $subFolderCount . '" style=" margin-right: 1rem; display: none;">';
                                echo '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">';
                                echo '<path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />';
                                echo ' </svg>';
                                echo '</span>';
                                echo "</div>";
                                echo "<div id='subFolderContainer$subFolderCount' class='folderContainer'>";
                                foreach ($element as $index => $element) {
                                    echo '<div class="template_card" onclick="get_template(' . "'" . $currentFolder . "'," . "'" . $currentSubFolder . "'," . "'" . $index . "'" . ')">';
                                    echo '<div class="template_left">';
                                    echo '<span title="' . $element[0] . '" style="margin-right:0">' . $index  . '</span>';
                                    echo '<span class="editPen" onclick="editFolder(' . "'"   . $index  . "'"  .  ')">   <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>  </span>';

                                    echo '</div>';
                                    echo '<span onclick="delete_template(' . "'" . $currentFolder . "'," . "'" . $currentSubFolder . "'," . "'" . $index . "'" . ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                                    echo '</div>';
                                }


                                echo "</div>";
                                echo "</div>";
                                $subFolderCount++;
                            }
                            echo "</div>";
                            echo "</div>";
                            $folderCount++;
                        }






                        // foreach ($prompts as $index => $element) {
                        //     echo '<div class="template_card" onclick="get_template(' . "'" . $index . "'" . ')">';
                        //     echo '<div class="template_left">';
                        //     echo '<span title="' . $element[0] . '">' . $index  . '</span>';

                        //     echo '</div>';
                        //     echo '<span onclick="delete_template(' . "'" . $index . "'" . ')"><svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg></span>';
                        //     echo '</div>';
                        // }
                        ?>
                        <div class="alignMiddle">
                            <label for="template_name">Name: </label>
                            <textarea name="template_name" id="template_name" cols="30" rows="1"></textarea>
                        </div>
                        <br>
                        <div class="alignMiddle">
                            <label for="template_description">Beschreibung: </label>
                            <textarea name="template_description" id="template_description" cols="30" rows="1"></textarea>
                        </div>
                        <br>
                        <div class="alignMiddle">

                            <label for="unterordner_select">Unterordner:;</label>
                            <select style="margin-left: auto;" name="unterordner_select" id="unterordner_select">

                                <?php
                                foreach ($metaPromptTemplates as $index => $element) {
                                    $lastFolder = $index;
                                    foreach ($element as $index => $element) {
                                        echo '<option value="' . $index . ',' . $lastFolder . '">' . $lastFolder . ': ' . $index . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <br>
                        <button class="button button-primary" onclick="save_template()">Speichern</button>
                        <div id="folderTab" class="tab subTab" style="margin-bottom: 0;">
                            <div style="display: flex;  align-items: center;" onclick="showTab('folder', 8)">
                                <h3>Neuen Ordner erstellen</h3>
                                <span id="arrowUp8" style="margin-left: auto; margin-right: 1rem">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                    </svg>
                                </span>
                                <span id="arrowDown8" style="margin-left: auto; margin-right: 1rem; display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                    </svg>
                                </span>
                            </div>
                            <div id="folderContainer" style="display: none;">

                                <div class="alignMiddle">
                                    <label for="folder_name">Name:</label>
                                    <textarea name="folder_name" id="folder_name" cols="30" rows="1"></textarea>
                                </div>
                                <br>
                                <button class="button button-primary" onclick="createFolder()">Ordner erstellen</button>
                                <br>
                            </div>


                        </div>

                        <div id="subFolderTab" class="tab subTab">
                            <div style="display: flex;  align-items: center;" onclick="showTab('subFolder', 9)">
                                <h3>Neuen Unterordner erstellen</h3>
                                <span id="arrowUp9" style="margin-left: auto; margin-right: 1rem">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                                    </svg>
                                </span>
                                <span id="arrowDown9" style="margin-left: auto; margin-right: 1rem; display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                    </svg>
                                </span>
                            </div>
                            <div id="subFolderContainer" style="display: none;">
                                <div class="alignMiddle">

                                    <label for="folder_select">Überordner: &nbsp;&nbsp;</label>
                                    <select name="folder_select" id="folder_select" style="margin-left: auto;">
                                        <?php
                                        foreach ($metaPromptTemplates as $index => $element) {
                                            echo '<option value="' . $index . '">' . $index . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <br>
                                <div class="alignMiddle">
                                    <label for="subFolder_name">Name:</label>
                                    <textarea name="subFolder_name" id="subFolder_name" cols="30" rows="1"></textarea>
                                </div>
                                <br>
                                <button class="button button-primary" onclick="createSubFolder()">Unterordner erstellen</button>

                            </div>


                        </div>


                    </div>
                </div>
            </div>

        </div>

        <table>

            <div style="display: flex; justify-content: end;margin-left:auto;">
                <img onclick=" switchSnippets('pc')" src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\computer.png" alt="computer" style="width: 20px; ">
                <img onclick="switchSnippets('mobile')" src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\iphone.png" alt="mobile" style="width: 20px; margin-right: 20px;">
            </div>
            <thead>
                <tr>
                    <td style="padding-left: 30px;padding-top: 5px;width: 1%;">

                        <input style="margin-left: 1px;" id="cb-select-all" type="checkbox" name="post[]" value="" onclick="selectAll()">
                    </td>
                    <td class="firstTd" style="display: flex;justify-content: center;">
                        Alte Daten
                    </td>
                    <td class="secondTd" style="display: flex;justify-content: center;">
                        Optimierte Daten
                    </td>
                </tr>
            </thead>
        </table>
        <table>
            <tbody id="table_body">

            </tbody>

        </table>

        <div id="list">



        </div>
    </div>



<?php

}
