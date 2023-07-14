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

$path = __DIR__ . '\templates.json';
$prompts = [
    'Anfangsprompts' => ["Vorgefertigtes Template", "Verfasse einen Titel für einen Artikel über  {topic}  . Stil:  {stil} . Ton: {ton} . Sollte zwischen 40 und 60 Zeichen lang sein.", "'Schreibe {abschnitte} aufeinanderfolgende Überschriften für einen Artikel über 	{title} . Stil: {stil}  Ton: {ton}  . Jede Überschrift ist zwischen 40 und 60 Zeichen lang. Benutze <h2></h2> tags für die Überschriften. Schreibe nur die genannte anzahl an überschriften';", "'Verfasse einen Artikel über {title} . Stil: {stil} . Ton: {ton} . Der Artikel ist nach folgenden aufeinanderfolgenden h2 Überschriften organisiert: {ueberschriften} . Schreibe {inhaltCount} Absätze pro Überschrift. ';
    ", "'Schreibe ein Excerp über den Titel ' +	title +	' . Style: ' +stil +	'. Tone: ' +ton +'. Muss zwischen 40 und 60 Zeichen lang sein. Schreibe nur die genannte anzahl an Absätzen'", "informativ", "neutral", "page"]

];
$jsonData = json_encode($prompts, JSON_PRETTY_PRINT);
file_put_contents($path, $jsonData);
