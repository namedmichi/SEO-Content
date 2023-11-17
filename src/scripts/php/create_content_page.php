<?php
add_action('admin_menu', 'rudr_submenu');
function rudr_submenu()
{

    add_submenu_page(
        'tools.php', // parent page slug
        'Texte Erstellen',
        'Texte Erstellen',
        'edit_posts',
        'nmd_create_content',
        'nmd_create_content_callback',
        0 // menu position
    );
}

function injectAjaxFunction()
{
    wp_enqueue_script('my-ajax-script', content_url() . '/plugins/SEOContent/src/scripts/js/ask_gpt_content_page_chat.js', array('jquery'), '1.0', true);

    // Pass the Ajax URL to the JavaScript file
    wp_localize_script('my-ajax-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('injectAjax', 'injectAjaxFunction');
function nmd_create_content_callback()
{
    do_action('injectAjax');


    $testPath = content_url() . '/plugins/SEOContent/src/scripts/php/templateTest.json';
    $promptPath = content_url() . '/plugins/SEOContent/src/scripts/php/prompts.json';



    try {


        $jsonString = file_get_contents($promptPath);
        $hardPrompts = json_decode($jsonString, true);
        $jsonString = file_get_contents($testPath);
        $testTemplates = json_decode($jsonString, true);
    } catch (Exception $e) {
    }




?>

    <script>
        document.getElementById('wpcontent').style.paddingLeft = 0
    </script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/src/style/content_page.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="<?php echo content_url() ?>/plugins/SEOContent/admin/css/seocontent-admin.css">


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
                <p id="loadingText">some Text</p>
            </span>
        </div>

    </div>
    <div class="background">
        <div class="header">
            <a class="noPaddingMargin" href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">

                <img src="<?php echo content_url() ?>\plugins\SEOContent\src\assets\seologo.png" alt="Seo logo">
            </a>
            <nav>
                <a href="<?php echo admin_url('admin.php?page=seo_content.php'); ?>">Startseite</a>
                <a href="<?php echo admin_url('admin.php?page=content.php'); ?>" id="textErstellen">Texte erstellen</a>
                <a href="<?php echo admin_url('admin.php?page=image.php'); ?>">Bilder erstellen</a>
                <a href="<?php echo admin_url('admin.php?page=title_meta.php'); ?>">Meta-Daten</a>
                <a target="_blank" style="margin-left: auto" href="https://www.seo-kueche.de/ratgeber/anleitung-plugin-seo-content/">Hilfe und Anleitung</a>
            </nav>
        </div>




        <div id="infoText" class="infoText">
            <p>Tipps zur Erstellung optimaler Inhalte erhalten Sie in unserem Ratgeber: <a href="https://www.seo-kueche.de/ratgeber/">https://www.seo-kueche.de/ratgeber/</a></p>
        </div>
        <div class="contentContainer">
            <h1 class="wp-heading-inline"> Texte erstellen</h1>
            <div>
                <form class="buttonBar" id="form">

                    <input required placeholder="Thema" name="nmd_topic_input" id="nmd_topic_input"></input>
                    <select id="nmd_stil_select" name="nmd_stil_select">
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

                    <select required name="nmd_abschnitte_select" id="nmd_abschnitte_select" onchange="headingCount(this.value)">
                        <option value="" disabled selected>Anzahl der Überschriften</option>
                        <option value="1" onclick="headingCount(1)">1</option>
                        <option value="2" onclick="headingCount(2)">2</option>
                        <option value="3" onclick="headingCount(3)">3</option>
                    </select>
                    <select required name="nmd_inhalt_select" id="nmd_inhalt_select">
                        <option value="" disabled selected>Absätze pro Überschriften</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <input required type="number" name="nmd_words_count" id="nmd_words_count" placeholder="Wörter pro Absatz">


                    <input type="submit" class="button action" id="submitButton" onclick="check_requirements()" value="Generieren">
                </form>
            </div>
            <div style="display: flex; align-items: center">
                <div class="update-nag notice notice-warning inline" style="display: none;margin-top: 3px;" id="seoErrorMsg">Das ist ein Fehler</div>
                <button style="display: none;height: 32px;margin-right: 8px;" class="button action" id="ignoreButton" onclick="ask_gpt_content_page()">Trotzdem fortfahren</button>
                <button style="display: none;" class="button action" id="cancelButton" onclick="cancel()">Abbrechen</button>
            </div>
            <div class="wrap">
                <div class="links container">

                    <div id="keywordTab" class="tab" style="background: gainsboro;" title="Bitte wählen Sie erst die Anzahl der überschriften aus">
                        <div style="display: flex;  align-items: center;" onclick="showTab('keyword', 1)">
                            <h2>Keyword Optimierer</h2>
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
                        <div id="keywordContainer" style="display: none;">
                            <!-- <button id="keywordRechercheButton" style="margin-left: 8px;" class="button action" onclick="get_keywords()">Keyword Recherche mit KI durchführen</button> -->
                            <div id="keywordsAddContainer">
                                <div class="keywordDiv">
                                    <svg class="removeKeywordDiv" onclick="removeKeywordDiv(this)" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />
                                    </svg>
                                    <label for="keyword">Keyword:</label>
                                    <br>
                                    <input name="keyword" id="keyword" type="text">
                                    <br>
                                    <label for="keywordAnzahl">Vorkommen im Text:</label>
                                    <br>
                                    <input type="number" name="keywordAnzahl" id="keywordAnzahl" style="width: 8ch;">
                                    <br>
                                    <label for="keywordWhere">Vorkommen in:</label>
                                    <p>Überschrift inkl. Absätze</p>
                                    <div class="flexCenter">
                                        <label class="keywordWhereId1" for="1">1</label>
                                        <input type="checkbox" name="1" class="keywordWhereId1 keywordWhereId1Value" id="1" value="1">
                                        <label class="keywordWhereId2" for="2">2</label>
                                        <input type="checkbox" name="2" class="keywordWhereId2 keywordWhereId2Value" id="2" value="2">
                                        <label class="keywordWhereId3" for="3">3</label>
                                        <input type="checkbox" name="3" class="keywordWhereId3 keywordWhereId3Value" id="3" value="3">

                                    </div>
                                    <br>
                                    <br>
                                    <label for="synonym">Synonyme(optional):</label>
                                    <br>
                                    <input name="synonym" id="synonym" type="text" placeholder="Synonym1, Synonym2, ...">
                                    <br>
                                    <label for="beschreibung">Beschreibung(Optional):</label>
                                    <br>
                                    <input type="text" name="beschreibung" id="beschreibung">
                                    <br>
                                </div>
                            </div>
                            <button class="button button-primary" type="button" onclick="addKeyword()">+ Weiteres Keyword hinzufügen</button>
                            <!-- <button class="button button-primary" style="background-color: #e42a2a;border-color: #e42a2a; width: 50%" type="button" onclick="removeKeyword()">- Keyword Entfernen</button> -->
                        </div>
                    </div>

                    <div id="faqTab" class="tab">
                        <div style="display: flex;  align-items: center;" onclick="showTab('faq', 2)">
                            <h2>FAQ Generator</h2>

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
                        <div id="faqContainer" style="display: none;">
                            <button class="button button-primary" type="button" onclick="generateFAQ()"> Fragen und Antworten Generieren</button>
                            <div id="faq" class="faq">
                                <label for="question">Frage 1:</label>
                                <br>
                                <input id="question" class="eingabe" name="question" type="text">
                                <br>
                                <label for="answer">Antwort:</label>
                                <br>
                                <input type="text" id="answer" name="answer" class="eingabe">
                            </div>
                            <div class="faqButtons">
                                <button class="button button-primary" type="button" onclick="addFAQ()">+ Weitere Frage hinzufügen</button>

                                <button class="button button-primary" type="button" onclick="generateAnswers()"> Antworten per KI Generieren</button>
                                <br>
                                <!-- <div style="display: flex; align-items: center; ">
            
                                    <input type="checkbox" id="addFAQtoPage" name="addFAQtoPage" value="Bike">
                                    <label for="addFAQtoPage"> FAQ für die Seite verwenden</label>
                                </div> -->

                            </div>
                        </div>
                    </div>
                    <div id="templateTab" class="tab">
                        <div style="display: flex;  align-items: center;" onclick="showTab('template', 3)">
                            <h2>Prompt-Vorlagen</h2>
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
                            <label for="template_name">Vorlagen Name</label>
                            <textarea name="template_name" id="template_name" cols="30" rows="1"></textarea>
                            <label for="template_description">Vorlagen Beschreibung</label>
                            <textarea name="template_description" id="template_description" cols="30" rows="1"></textarea>
                            <div class="alignMiddle">

                                <label for="unterordner_select">Unterordner: &nbsp;&nbsp;</label>
                                <select name="unterordner_select" id="unterordner_select">

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

                                    <label for="folder_name">Ordner Name</label>
                                    <textarea name="folder_name" id="folder_name" cols="30" rows="1"></textarea>
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
                                        <select name="folder_select" id="folder_select">
                                            <?php
                                            foreach ($testTemplates as $index => $element) {
                                                echo '<option value="' . $index . '">' . $index . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <label for="subFolder_name">Unterorder Name</label>
                                    <textarea name="subFolder_name" id="subFolder_name" cols="30" rows="1"></textarea>
                                    <button class="button button-primary" onclick="createSubFolder()">Unterordner erstellen</button>

                                </div>


                            </div>


                        </div>
                    </div>

                </div>
                <div class="mitte container">
                    <div class="mainInputs">
                        <div class="flex_button_header">
                            <div style="display: flex;">
                                <div class="alignMiddle">
                                    <h2>Titel</h2>
                                    <img style="margin: 0;" id="infoIconTitle" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">

                                </div>
                                <div id="infoIconTitleText" class="infoTextMini">
                                    Der Titel wird verwendet, um Ihre Seite bzw. Beitrag zu benennen. Aus dem Titel wird von WordPress automatisch die H1-Überschrift, der Seitentitel und die URL generiert. Der Titel hat somit einen Hohen Einfluss auf den Erfolg Ihrer Seite und sollte das Haupt-Keyword gleich zu Beginn enthalten. Zudem wird der Titel in der Beitrags- und Seitenübersicht von WordPress angezeigt. </div>
                            </div>
                        </div>
                        <textarea name="nmd_title_input" id="nmd_title_input" cols="170" rows="1"></textarea>
                        <div class="abschnitte" style="display: none;">
                            <div class='flex_button_header'>
                                <div style="display: flex;">
                                    <div class="alignMiddle">
                                        <h2>Überschriften</h2>
                                        <img style="margin: 0;" id="infoIconUeberschrift" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                    </div>
                                    <div id="infoIconUeberschriftText" class="infoTextMini">
                                        Der Hauptinhalt bildet den Kern Ihrer Seite und hat einen großen Einfluss auf den Erfolg Ihrer Zielseiten in Suchmaschinen. Nutzen Sie den Keyword-Optimierer, um festzulegen, welche Neben-Keywords Sie innerhalb der Teilüberschriften und Absätze behandeln möchten. Tipps zur optimalen Arbeit mit dem Keyword-Optimierer finden Sie in unserer Anleitung unter dem Menüpunkt „Hilfe und Anleitung“. </div>
                                </div>
                            </div>

                        </div>
                        <textarea name="nmd_abschnitte_input" id="nmd_abschnitte_input" cols="170" rows="10" style="display: none !important"></textarea>
                        <div class="Inhalt">
                            <div class="flex_button_header">
                                <div style="display: flex;">
                                    <div class="alignMiddle">
                                        <h2>Inhalt</h2>
                                        <img style="margin: 0;" id="infoIconInhalt" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                    </div>
                                    <div id="infoIconInhaltText" class="infoTextMini">
                                        Der Hauptinhalt bildet den Kern Ihrer Seite und hat einen großen Einfluss auf den Erfolg Ihrer Zielseiten in Suchmaschinen. Nutzen Sie den Keyword-Optimierer, um festzulegen, welche Neben-Keywords Sie innerhalb der Teilüberschriften und Absätze behandeln möchten. Tipps zur optimalen Arbeit mit dem Keyword-Optimierer finden Sie in unserer Anleitung unter dem Menüpunkt „Hilfe und Anleitung“. </div>
                                </div>
                            </div>
                            <div style="display: flex;  flex-direction: column;">
                                <div class="checkboxDiv">
                                    <label for="includeInfos" style="font-size: 1rem;">Unternehmensinformationen verwenden</label>

                                    &nbsp;
                                    <input type="checkbox" name="includeInfos" id="includeInfos" style="width: 16px">
                                    <img style="margin: 0;" id="infoIconUnternehmensinfo" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                    <div id="infoIconUnternehmensinfoText" class="infoTextMini">
                                        Der Titel wird verwendet, um Ihre Seite bzw. Beitrag zu benennen. Aus dem Titel wird von WordPress automatisch die H1-Überschrift, der Seitentitel und die URL generiert. Der Titel hat somit einen Hohen Einfluss auf den Erfolg Ihrer Seite und sollte das Haupt-Keyword gleich zu Beginn enthalten. Zudem wird der Titel in der Beitrags- und Seitenübersicht von WordPress angezeigt. </div>
                                </div>
                                <div>
                                    <div class="alignMiddle">
                                        <label style="font-size: 1rem;">Wähle eine Option:</label>
                                        <img style="margin: 0;" id="infoIcon1ktinfo" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                    </div>
                                    <div id="infoIconKontaktinfoText" class="infoTextMini">
                                        Der Titel wird verwendet, um Ihre Seite bzw. Beitrag zu benennen. Aus dem Titel wird von WordPress automatisch die H1-Überschrift, der Seitentitel und die URL generiert. Der Titel hat somit einen Hohen Einfluss auf den Erfolg Ihrer Seite und sollte das Haupt-Keyword gleich zu Beginn enthalten. Zudem wird der Titel in der Beitrags- und Seitenübersicht von WordPress angezeigt. </div>


                                    <input type="radio" id="option1" name="kontaktTyp" value="page">
                                    <label style="font-size:1rem;" for="option1">Kontakseite einbinden</label><br>
                                    <input type="radio" id="option2" name="kontaktTyp" value="form">
                                    <label style="font-size:1rem;" for="option2">Kontaktformular einbinden</label><br>
                                    <input type="radio" id="option3" name="kontaktTyp" value="not">
                                    <label style="font-size:1rem;" for="option3">Keins</label><br>
                                </div>
                                <!-- <div class="checkboxDiv">
                                    <label for="includeShortcode" style="font-size: 1rem;">Kontaktformular einfügen</label>
                                    &nbsp;
                                    <input type="checkbox" name="includeShortcode" id="includeShortcode" style="width: 16px">
                                </div> -->


                            </div>
                            <br>

                        </div>
                        <textarea name="nmd_inhalt_input" id="nmd_inhalt_input" cols="170" rows="10"></textarea>
                        <div class="Inhalt">
                            <div style="display: flex;">
                                <h2>Meta-Beschreibung</h2>
                                <img id="infoIconExcerp" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                <div id="infoIconExcerpText" class="infoTextMini">
                                    Die Vorschau wird als Textauszug innerhalb von Kategorien angezeigt. Die Vorschau hat nur für die Kategorie-Seiten, auf welchen sie angezeigt wird, eine Relevanz und ist für den SEO-Erfolg Ihrer Zielseite nicht von Bedeutung. </div>
                            </div>
                        </div>
                        <textarea name="nmd_excerp_input" id="nmd_excerp_input" cols="170" rows="10"></textarea>
                        <div>
                            <div class="beitragtyp">


                                <h2>Beitragtyp</h2>
                                <select name="nmd_typ_select" id="nmd_typ_select">
                                    <option value="page">Seite</option>
                                    <option value="post">Beitrag</option>
                                </select>

                                <button class="button action" onclick="create_content_page()" style="width: 100%; height: 40px">Seite erzeugen</button>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="rechts container">

                    <div class="prompting">
                        <div class="alignMiddle">
                            <label for="templatePrompt">Vorgefertigtes Zielseiten-Template verwenden</label>
                            <div class="alignMiddle">
                                <img style="margin: 0;" id="infoIconTemplate" class="infoIconMini" src="<?php echo content_url() ?>/plugins/SEOContent/src/assets/infoIcon.png" alt="icon">
                                <input style="margin-left:4px" type="checkbox" name="templatePrompt" id="templatePrompt">
                            </div>
                            <div id="infoIconTemplateText" class="infoTextMini">
                                Die Vorschau wird als Textauszug innerhalb von Kategorien angezeigt. Die Vorschau hat nur für die Kategorie-Seiten, auf welchen sie angezeigt wird, eine Relevanz und ist für den SEO-Erfolg Ihrer Zielseite nicht von Bedeutung. </div>

                        </div>
                        <h3>Titel</h3>
                        <textarea name="title_prompt" id="title_prompt" cols="30" rows="10"><?php echo $hardPrompts["titlePrompt"] ?></textarea>
                        <h3>Überschriften</h3>
                        <textarea name="abschnitte_prompt" id="abschnitte_prompt" cols="30" rows="10"><?php echo $hardPrompts["ueberschriftenPrompt"] ?></textarea>
                        <h3>Inhalt</h3>
                        <textarea name="inhalt_prompt" id="inhalt_prompt" cols="30" rows="10"><?php echo $hardPrompts["inhaltPrompt"] ?></textarea>
                        <h3>Meta-Beschreibung</h3>
                        <textarea name="excerp_prompt" id="excerp_prompt" cols="30" rows="10"><?php echo $hardPrompts["excerpPrompt"] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hover über Info Text Icon


        function addMouseOverAndOutEventListeners(id) {
            document.getElementById(id).addEventListener('mouseover', function() {
                document.getElementById(id + 'Text').style.display = 'flex';
            });
            document.getElementById(id).addEventListener('mouseout', function() {
                document.getElementById(id + 'Text').style.display = 'none';
            });
        }

        addMouseOverAndOutEventListeners('infoIconTitle');
        addMouseOverAndOutEventListeners('infoIconUeberschrift');
        addMouseOverAndOutEventListeners('infoIconInhalt');
        addMouseOverAndOutEventListeners('infoIconExcerp');
        addMouseOverAndOutEventListeners('infoIconUnternehmensinfo');
        addMouseOverAndOutEventListeners('infoIconKontaktinfo');
        addMouseOverAndOutEventListeners('infoIconTemplate');

        // Hover über Info Text Container


        function addMouseOverandOutInfoIconText(elementId) {
            document.getElementById(elementId).addEventListener('mouseover', function() {
                document.getElementById(elementId).style.display = 'flex';
            });
            document.getElementById(elementId).addEventListener('mouseout', function() {
                document.getElementById(elementId).style.display = 'none';
            });
        }

        addMouseOverandOutInfoIconText('infoIconTitleText');

        addMouseOverandOutInfoIconText('infoIconUeberschriftText');

        addMouseOverandOutInfoIconText('infoIconInhaltText');

        addMouseOverandOutInfoIconText('infoIconExcerpText');
    </script>

<?php

}
