<?php

function injectAjaxFunctionMeta()
{
    wp_enqueue_script('my-ajax-script-meta', content_url() . '/plugins/SEOContent/src/scripts/js/ask_gpt_title_meta.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script-meta', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('my-ajax-script-meta', 'nmd', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}


add_action('injectAjaxMeta', 'injectAjaxFunctionMeta');

function nmd_create_title_and_meta_callback()
{

    do_action('injectAjaxMeta');
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

                        <button class="button action" type="button" onclick="getPages('pages')">Seiten laden</button>
                        <button class="button action" type="button" onclick="getPages('posts')">Beiträge laden</button>

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
        <button class="button action" style="width: 100px; margin-top: 8px" onclick="showSettings()">Einstellungen</button>
        <div class="promting" id="settingsOverlay" style="top: 400px;">
            <div style="margin-left: auto; cursor: pointer; width: fit-content;">
                <svg style="margin-left: auto; cursor: pointer" onclick="closeSettings()" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />
                </svg>
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
                    <div id="titleContainer" style="display: block;">

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
                    <div id="metaContainer" style="display: block;">
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
                    <div id="templateContainer" class="templateContainer" style="display: block;">


                        <label for="template_name">Name: </label>
                        <br>
                        <textarea name="template_name" id="template_name" cols="30" rows="1"></textarea>

                        <br>

                        <label for="template_description">Beschreibung: </label>
                        <br>
                        <textarea name="template_description" id="template_description" cols="30" rows="1"></textarea>

                        <br>


                        <label for="unterordner_select">Unterordner:;</label>
                        <br>
                        <select name="unterordner_select" id="unterordner_select">

                        </select>

                        <br>
                        <button style="margin-top: 8px;" class="button button-primary" onclick="save_template()">Speichern</button>
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


                                <label for="folder_name">Name:</label>
                                <br>
                                <textarea style="margin-top:8px" name="folder_name" id="folder_name" cols="30" rows="1"></textarea>

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


                                <label for="folder_select">Überordner: &nbsp;&nbsp;</label>
                                <br>
                                <select style="margin-top:8px" name="folder_select" id="folder_select" style="margin-left: auto;">
                                    <?php
                                    foreach ($metaPromptTemplates as $index => $element) {
                                        echo '<option value="' . $index . '">' . $index . '</option>';
                                    }
                                    ?>
                                </select>

                                <br>

                                <label for="subFolder_name">Name:</label>
                                <br>
                                <textarea style="margin-top:8px" name="subFolder_name" id="subFolder_name" cols="30" rows="1"></textarea>

                                <br>
                                <button class="button button-primary" onclick="createSubFolder()">Unterordner erstellen</button>

                            </div>


                        </div>


                    </div>
                </div>
            </div>
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
                    <div id="sonderContainer" style="display: block;">


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
                            <div> <button onclick="addSpecialCharacter(this,'😊')">😊</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🌟')">🌟</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🚀')">🚀</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🌺')">🌺</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🤩')">🤩</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🍕')">🍕</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🎉')">🎉</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🎈')">🎈</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🍦')">🍦</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🎂')">🎂</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🍺')">🍺</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🍸')">🍸</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🌈')">🌈</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'📚')">📚</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🖋')">🖋</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🖼')">🖼</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🎸')">🎸</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🎮')">🎮</button> </div>
                            <div> <button onclick="addSpecialCharacter(this,'🚗')">🚗</button> </div>



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
                <tr style="    padding-top: 10px;    padding-bottom: 10px;">
                    <td style="padding-left: 30px;padding-top: 5px;width: 1%;">

                        <input style="margin-left: 1px;" id="cb-select-all" type="checkbox" name="post[]" value="" onclick="selectAll()">
                    </td>
                    <td class="firstTd tdHeading" style="display: flex;justify-content: center;font-size: 1.2em;">
                        Aktuelle Daten
                    </td>
                    <td class="secondTd" style="display: flex;justify-content: center;font-size: 1.2em;">
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
