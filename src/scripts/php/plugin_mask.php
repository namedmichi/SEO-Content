<?php

class Nmd_Form
{

    private $config = '{"title":"Küchen GPT","prefix":"nmd_form_","domain":"nmd-form","class_name":"Nmd_Form","post-type":["page"],"context":"normal","priority":"high",
		"fields":[
					{"type":"textarea","label":"Dienstleistung(Pflicht)","default":"SEO Marketing","rows":"1","id":"nmd_form_dienstleistung"},
					{"type":"textarea","label":"Firmenname(Pflicht)","default":"SEO Küche","rows":"1","id":"nmd_form_firmenname"},
					{"type":"textarea","label":"Ort(Pflicht)","default":"Kolbermoor","rows":"1","id":"nmd_form_ort"},
					{"type":"textarea","label":"Gründungsjahr","default":"2009","rows":"1","id":"nmd_form_jahr"},
					{"type":"textarea","label":"Service1","default":"Online Marketing","rows":"1","id":"nmd_form_service1"},
					{"type":"textarea","label":"Service2","default":"Suchmaschienenoptimierung","rows":"1","id":"nmd_form_service2"},
					{"type":"textarea","label":"Service3","default":"Facebook Ads","rows":"1","id":"nmd_form_service3"},
					{"type":"textarea","label":"Service4","default":"Google Ads","rows":"1","id":"nmd_form_service4"},
					{"type":"textarea","label":"Service5","default":"Beratung","rows":"1","id":"nmd_form_service5"},
					{"type":"select","label":"Schreibstil","default":"Seriös","options":"seriösen : Seriös\r\nwerblichen : Werblich","id":"nmd_form_stil"}
		], 
		"fieldsAdvanced":[
					{"type":"textarea","label":"Thema(Pflicht)","default":"SEO Marketing","rows":"1","id":"nmd_form_adv_thema"},
					{"type":"textarea","label":"Ort(Pflicht)","default":"Kolbermoor","rows":"1","id":"nmd_form_adv_ort"},
					{"type":"select","label":"Suchintention","default":"kommerzielle","options":"kommerzielle : kommerzielle\r\ninformationale : informationale","id":"nmd_form_adv_intention"}					
				] 
		}';

    public function __construct()
    {
        //Plugin Maske

        $this->config = json_decode($this->config, true);
        //add_action('add_meta_boxes', [$this, 'add_meta_boxes']);

        add_action('save_post', [$this, 'save_post']);
    }


    public function add_meta_boxes()
    {
        foreach ($this->config['post-type'] as $screen) {
            add_meta_box(
                sanitize_title($this->config['title']),
                $this->config['title'],
                [$this, 'add_meta_box_callback'],
                $screen,
                $this->config['context'],
                $this->config['priority']
            );
        }
    }

    public function save_post($post_id)
    {
        foreach ($this->config['fields'] as $field) {
            switch ($field['type']) {
                default:
                    if (isset($_POST[$field['id']])) {
                        $sanitized = sanitize_text_field($_POST[$field['id']]);
                        update_post_meta($post_id, $field['id'], $sanitized);
                    }
            }
        }
    }




    public function add_meta_box_callback()
    {
        echo "
			<head>
			
			<link rel='stylesheet' href='<?php echo content_url()?>/plugins/SEOContent/gpt_mask_style.css'>
			
			</head>
			";

        echo "<div class='flex-container'>";
        echo "<div class='left'>";
?>
        <script>
            // Wechsel zwischen einfacher und erweiterter Ansicht 

            function openTab(evt, mode) {
                var i, tabcontent, tablinks;

                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }

                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                if (mode == 'Simple') {
                    document.getElementById('simpleResultSection').style.display = "block";
                    document.getElementById('advancedResultSection').style.display = "none";
                } else {
                    document.getElementById('advancedResultSection').style.display = "block";
                    document.getElementById('simpleResultSection').style.display = "none";

                }
                document.getElementById(mode).style.display = "block";
                evt.currentTarget.className += " active";
            }
        </script>
        <!-- Tab links -->
        <div class="tab">
            <button id='startTab' class="tablinks active" onclick="openTab(event, 'Simple')">Einfach</button>
            <button class="tablinks" onclick="openTab(event, 'Advanced')">Erweitert</button>
        </div>

        <?php
        $this->fields_table();
        // $gblock = get_post(194);
        // echo apply_filters('the_content', $gblock->post_content);
        ?>
        </div>
        <div class='right'>
            <div id="simpleResultSection">
                <div id='gpt_result' contenteditable='true'> </div>
                <div id='gpt_result_text' contenteditable='true'> </div>
                <div id='gpt_service1_header' contenteditable='true'> </div>
                <div id='gpt_service1_text' contenteditable='true'> </div>
                <div id='gpt_service2_header' contenteditable='true'> </div>
                <div id='gpt_service2_text' contenteditable='true'> </div>
                <div id='gpt_service3_header' contenteditable='true'> </div>
                <div id='gpt_service3_text' contenteditable='true'> </div>
                <div id='gpt_service4_header' contenteditable='true'> </div>
                <div id='gpt_service4_text' contenteditable='true'> </div>
                <div id='gpt_service5_header' contenteditable='true'> </div>
                <div id='gpt_service5_text' contenteditable='true'> </div>
            </div>
            <div id="advancedResultSection" style="display: none;">
                <div id="gpt_adv_keywords" style="font-size: 24px;"> &nbsp;&nbsp;</div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <!--  -->
        <script type="text/javascript">
            var service_count = 1;
            document.getElementById('nmd_form_service2row').style.display = "none";
            document.getElementById('nmd_form_service3row').style.display = "none";
            document.getElementById('nmd_form_service4row').style.display = "none";
            document.getElementById('nmd_form_service5row').style.display = "none";

            function add_service() {
                service_count++;

                document.getElementById('nmd_form_service' + service_count + 'row').style.display = "table-row";


            }
        </script>
        <script src="<?php echo content_url() ?>/plugins/SEOContent/src/scripts/js/ask_gpt.js"></script>
        <script src="<?php echo content_url() ?>/plugins/SEOContent/src/scripts/js/ask_gpt_advanced.js"></script>

    <?php

    }
    private function fields_table()
    { ?>
        <style>
            /* Style the tab */
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
            }

            /* Style the buttons that are used to open the tab content */
            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
            }

            /* Change background color of buttons on hover */
            .tab button:hover {
                background-color: #ddd;
            }

            /* Create an active/current tablink class */
            .tab button.active {
                background-color: #ccc;
            }

            /* Style the tab content */
            .tabcontent {
                display: none;
                padding: 6px 12px;
                border: 1px solid #ccc;
                border-top: none;
            }
        </style>
        <?php
        $pageid = get_the_ID();

        ?>
        <script type="text/javascript">
            console.log('Before')

            function paste() {

                if (!document.body.classList.contains('block-editor-page')) {
                    alert('Gutenberg ist nicht installiert. Vorgang wird abgebrochen')
                    return;
                }
                service_headers = []
                service_texts = []

                for (let index = 1; index <= service_count; index++) {
                    service_headers.push(document.getElementById('gpt_service' + index + '_header').innerHTML);
                    service_texts.push(document.getElementById('gpt_service' + index + '_text').innerHTML);

                }

                result = document.getElementById('gpt_result').innerHTML;
                resultText = document.getElementById('gpt_result_text').innerHTML;

                $.ajax({
                    url: '<?php echo content_url() ?>/plugins/SEOContent/src/scripts/php/gpt_ajax.php',
                    method: 'POST',
                    data: {
                        service_count: service_count,
                        page_id: '<?php echo $pageid; ?>',
                        contentHeader: result,
                        contentText: resultText,
                        service_headers: service_headers,
                        service_texts: service_texts,
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
            console.log('after')
        </script>
        <div id="Simple" class="tabcontent">
            <table class="form-table" role="presentation">
                <tbody>
                    <?php
                    foreach ($this->config['fields'] as $field) {
                    ?><tr id="<?php echo $field['id']; ?>row">
                            <th scope="row"><?php $this->label($field); ?></th>
                            <td><?php $this->field($field); ?></td>
                        </tr>
                    <?php
                    }
                    ?>



                </tbody>

            </table>
            <button onclick="ask_gpt()">Entwurf erzeugen</button>
            <button id="einfuegen" onclick="paste()">Entwurf einfügen</button>
            <button onclick="add_service()">Service hinzufÜgen</button>
        </div>
        <script>
            document.getElementById('Simple').style.display = "block";
        </script>
        <div id="Advanced" class="tabcontent">
            <table class="form-table" role="presentation">
                <tbody>
                    <?php
                    foreach ($this->config['fieldsAdvanced'] as $field) {
                    ?><tr id="<?php echo $field['id']; ?>row">
                            <th scope="row"><?php $this->label($field); ?></th>
                            <td><?php $this->field($field); ?></td>
                        </tr>
                    <?php
                    }
                    ?>



                </tbody>

            </table>
            <button onclick="ask_gpt_advanced()">Entwurf erzeugen</button>
            <button id="einfuegen_adv">Entwurf einfügen</button>
        </div>



<?php
    }

    private function label($field)
    {
        switch ($field['type']) {
            default:
                printf(
                    '<label class="" for="%s">%s</label>',
                    $field['id'],
                    $field['label']
                );
        }
    }

    private function field($field)
    {
        switch ($field['type']) {
            case 'range':
                $this->input_minmax($field);
                break;
            case 'select':
                $this->select($field);
                break;
            case 'textarea':
                $this->textarea($field);
                break;
            default:
                $this->input($field);
        }
    }

    private function input($field)
    {
        printf(
            '<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
            isset($field['class']) ? $field['class'] : '',
            $field['id'],
            $field['id'],
            isset($field['pattern']) ? "pattern='{$field['pattern']}'" : '',
            $field['type'],
            $this->value($field)
        );
    }

    private function input_minmax($field)
    {
        printf(
            '<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s">',
            $field['id'],
            isset($field['max']) ? "max='{$field['max']}'" : '',
            isset($field['min']) ? "min='{$field['min']}'" : '',
            $field['id'],
            isset($field['step']) ? "step='{$field['step']}'" : '',
            $field['type'],
            $this->value($field)
        );
    }

    private function select($field)
    {
        printf(
            '<select id="%s" name="%s">%s</select>',
            $field['id'],
            $field['id'],
            $this->select_options($field)
        );
    }

    private function select_selected($field, $current)
    {
        $value = $this->value($field);
        if ($value === $current) {
            return 'selected';
        }
        return '';
    }

    private function select_options($field)
    {
        $output = [];
        $options = explode("\r\n", $field['options']);
        $i = 0;
        foreach ($options as $option) {
            $pair = explode(':', $option);
            $pair = array_map('trim', $pair);
            $output[] = sprintf(
                '<option %s value="%s"> %s</option>',
                $this->select_selected($field, $pair[0]),
                $pair[0],
                $pair[1]
            );
            $i++;
        }
        return implode('<br>', $output);
    }

    private function textarea($field)
    {
        printf(
            '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
            $field['id'],
            $field['id'],
            isset($field['rows']) ? $field['rows'] : 5,
            $this->value($field)
        );
    }

    private function value($field)
    {
        global $post;
        if (metadata_exists('post', $post->ID, $field['id'])) {
            $value = get_post_meta($post->ID, $field['id'], true);
        } else if (isset($field['default'])) {
            $value = $field['default'];
        } else {
            return '';
        }
        return str_replace('\u0027', "'", $value);
    }
}
new Nmd_Form;
