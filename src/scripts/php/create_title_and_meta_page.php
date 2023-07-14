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
    $promptPath = content_url() . '/plugins/SEOContent/src/scripts/php/prompts.json';

    try {


        $jsonString = file_get_contents($promptPath);
        $prompts = json_decode($jsonString, true);
    } catch (Exception $e) {
    }
?>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">

    <script>
        document.getElementById('wpcontent').style.paddingLeft = 0
    </script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/src/style/title_and_meta_page.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


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
                                <option value="retry" class="hide-if-no-js">Neu Generieren</option>
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
                                <div> <button onclick="addSpecialCharacter(this ,'►')">►</button> </div>
                                <div> <button onclick="addSpecialCharacter(this ,'◄')">◄</button> </div>
                                <div> <button onclick="addSpecialCharacter(this ,'▲')">▲</button> </div>
                                <div> <button onclick="addSpecialCharacter(this ,'▼')">▼</button> </div>
                                <div> <button onclick="addSpecialCharacter(this ,'✓')">✓</button> </div>
                                <div> <button onclick="addSpecialCharacter(this ,'✅')">✅</button> </div>
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


            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <td style="padding-left: 30px;">

                        <input id="cb-select-all" type="checkbox" name="post[]" value="" onclick="selectAll()">
                    </td>

                    <td style="display: flex; justify-content: end;margin-left:auto;"">
                        <img onclick=" switchSnippets('pc')" src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\computer.png" alt="computer" style="width: 20px; ">
                        <img onclick="switchSnippets('mobile')" src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\iphone.png" alt="mobile" style="width: 20px; margin-right: 20px;">
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
