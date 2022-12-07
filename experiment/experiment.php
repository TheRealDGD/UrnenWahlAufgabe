<!doctype html>
<html lang="de">

<?php
    function writeData($filename,$data)
    {
        $date = date('Y-m-d H:i:s');
        file_put_contents($filename,"{$date},{$data}\r\n",FILE_APPEND);
    }

    session_start();

    $probs = [0=>1,3,5,4];
    $boxName = [1=>'A',2=>'B'];

    if ($_SERVER['SERVER_NAME'] == "localhost")
    {
        $fileData = "/var/www/data/data.txt";
        $fileMail = "/var/www/data/mail.txt";
    } else
    {
        $fileData = "../../.lifedata/experiment/data.txt";
        $fileMail = "../../.lifedata/experiment/mail.txt";
    }
    // UmfrageID wurde eingegeben
    if ($_POST['UmfrageID'])
    {
        $_SESSION['UmfrageID'] = $_POST['UmfrageID'];
    }

    // Aktuellen Zustand prüfen / zurücksetzen
    if (($_POST['step'] == $null) || ($_SESSION['UmfrageID'] == $null))
    {
        $step = 'start';
        $_SESSION['UmfrageID'] = $null;
        $round=$_SESSION['round']=1;
        $mode=$_SESSION['mode']=1;
        $tickets=$_SESSION['tickets'] = 0;
    } else
    {
        $step = $_POST['step'];
        $round=$_SESSION['round'];
        $mode=$_SESSION['mode'];
        $tickets=$_SESSION['tickets'];
    }


    // Ergbnisse wurden übermittelt, abspeichern und nächste Runde vorbereiten.
    if ($_POST['color'])
    {
        if ($round>0) 
        {
            writeData($fileData,"{$_SESSION['UmfrageID']},{$round},{$mode},{$_POST['color']},{$_POST['box']},{$_POST['p']},{$_POST['time']}");

            if (($mode==2) && ($_POST['color'] == "rot")) // gewinnspiel
            {
                    $tickets+=($_POST['box']==1?1:3);
                    $_SESSION['tickets']=$tickets;
            }
        }

        if (++$round > 3)
        {
            if ($mode==1) 
                $step = "priceGame";
            else
                $step = "finish";
        } else
            $step = "play";

        $_SESSION['round'] = $round;
        $_SESSION['mode'] = $mode;
    }

    if (($step=="trial") || ($step=="play"))
    {
        $red = $probs[$round];
        $blue = 10-$red;
        $pText = "{$red}:{$blue}";
        $p = $probs[$round]/10;
    }
    ?>

    <head>
        <title>Urnenaufgabe</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="experiment.css">
        <script src="./experiment.js"></script>
    </head>

    <body onload="load('<?=$pText?>',<?=$p?>)">
        <h1>Experiment Urnenaufgabe</h1>
        <p><?php //var_dump($_SESSION); ?></p>
        <form id="form" action="experiment.php" method="POST">

        <?php switch($step) { 
            case 'start': ?>
                <p>Herzlich Willkommen bei dem praktischen Teil meiner Studie.<br>
                Um hier weiterzumachen, solltest du alle Fragebögen bearbeitet haben.<br>
                Bitte lies dir die Aufgabenstellung konzentriert durch.</p>

                <p>Für die Zuordnung zu den von dir beantworteten Fragebögen, gib hier nun bitte den selben Code an.<br>
                Zur Erinnerung: - zweiter Buchstabe des eigenen Vornamens - zweiter Buchstabe des (Geburts-) oder
                Nachnamens - die ersten 3 Buchstaben des Geburtsorts - die Anzahl älterer Geschwister (zweistellig angeben)</p>

                <input type="text" id="UmfrageID" name="UmfrageID" placeholder="Code" required pattern="[a-zA-ZäöüÄÖÜß]{5}\d{2}">
                <input type="hidden" id="step" name="step" value="intro">
                <input type="submit" value="weiter">
                <?php break;?>

            <?php case 'intro':?>
                <p>Dir werden als nächstes zwei verschiedene Szenarien beschrieben, die jeweils drei unterschiedliche Aufgaben beinhalten.<br>
                Bei diesen geht es darum Entscheidungen zwischen 2 Urnen zu treffen. Dabei enthalten beide Urnen immer 10 Kugeln.</p>
                <p>Urne A enthält dabei immer ein bekanntes Verhältnis an Kugeln, während das Verhältnis bei Urne B
                unbekannt ist.<br>
                (Von 10 roten Kugeln und 0 blauen Kugel bis zu 0 roten Kugeln und 10 blauen Kugeln ist jede Zusammensetzung möglich.
                <b>Jede mögliche Zusammensetzung von roten und blauen Kugeln ist gleich wahrscheinlich!)</b></p>
                <p>Um es besser zu verstehen, werden wir erstmal eine Proberunde machen</p>

                <input type="hidden" id="step" name="step" value="trial">
                <input type="submit" value="weiter">

                <?php 
                    $_SESSION['round']=0;
                    $_SESSION['mode']=1;
                    break;
                ?>
            <?php case 'priceGame':?>
                <p><b>Im zweiten Teil dieses Experiments gibt es eine kleine Veränderung.</b><br>
                Die Urnenaufgabe bleibt gleich und auch die Wahrscheinlichkeiten sind unverändert.<br>
                Jedoch hast du nun die Möglichkeit mit dem Ziehen einer roten Kugel etwas zu gewinnen, 
                denn in diesem Szenario findet eine Verlosung statt.<br>
                Ziehst du diesmal eine rote Kugel aus Urne A wird dein Name EINMAL in einen Lostopf für eine 30
                Euro-Amazon Gutschein Verlosung gelegt.<br>
                Ziehst du jedoch eine rote Kugel aus Urne B wird dein Name DREIMAL in diesen Lostopf gelegt.</p>
                <p>Bitte betrachte jede Entscheidung unabhängig voneinander. Beantworte die Fragen bitte, auch wenn
                du nicht an der Verlosung teilnehmen möchtest.</p>

                <input type="hidden" id="step" name="step" value="play">
                <input type="submit" value="weiter">

                <?php 
                    $_SESSION['round']=1;
                    $_SESSION['mode']=2;
                    break;
                ?>
            <?php case 'finish':?>
                <p>Du hast <?=$tickets?> Los(e) für das Gewinnspiel<br>
                Wenn du an der Verlosung teilnehmen möchtest, gib hier nun bitte deine E-Mail-Adresse an.</p>
                <p>Ich bedanke mich vielmals für die Teilnahme an meiner Studie.</p>
                <p>Bei Fragen kannst du dich jederzeit an mich wenden unter <a href="mailto:blodt.leah@stud.hs-fresenius.de">blodt.leah@stud.hs-fresenius.de</a></p>

                <input type="email" id="mail" name="mail">
                <input type="hidden" id="step" name="step" value="saveMail">
                <input type="submit" value="weiter">
                <?php break;?>
            <?php case 'saveMail':
                writeData($fileMail,"{$_POST['mail']},{$tickets}");
                ?>
                <p>Ich bedanke mich vielmals für die Teilnahme an meiner Studie.</p>
                <p>Die Antworten wurden gespeichert und du kannst das Fenster jetzt schlie&szlig;en</p>
                <p>Bei Fragen kannst du dich jederzeit an mich wenden unter <a href="mailto:blodt.leah@stud.hs-fresenius.de">blodt.leah@stud.hs-fresenius.de</a></p>
                <?php break;?>
            <?php case 'trial':?>
            <?php case 'play':
                if ($round==0)
                    echo "<h2>Proberunde</h2>";
                elseif (($round==1) && ($mode==1))
                    echo "<h2>Das war die Proberunde, jetzt beginnt die eigentliche Aufgabe.</h2>";

                if ($round>0)
                {
                    echo "<h2>Teil {$mode}, Runde {$round}</h2>";
                }
                
                ?>
                <p>Auf dem Tisch stehen die zwei Urnen A und B, die rote und blaue Kugeln enthalten.<br> 
                Deine Aufgabe ist es, aus einer der Urnen eine Kugel zu ziehen. Ziel ist es, eine <font color=red>rote Kugel</font> zu ziehen.<br>
                <list>
                    <li id="li1">Urne A enthält 10 Kugeln mit einem <b>bekannten</b> Verhältnis</li>
                    <li id="li2">Urne A enthält <font color=red><?= $red ?> rote</font> Kugeln und <font color=blue><?= $blue ?> blaue</font> Kugeln </li>
                    <li>Urne B enthält 10 Kugeln mit einem <b>unbekannten</b> Verhältnis</li>
                </list>
                <p>Zur Erinnerung: von 10 roten Kugeln und 0 blauen Kugel bis zu 0 roten Kugeln und 10 blauen Kugeln ist jede Zusammensetzung möglich.<br>
                Jede mögliche Zusammensetzung von roten und blauen Kugeln ist gleich wahrscheinlich!</p>


                <canvas id="canvas" width="400" height="250" style=></canvas>
                <p id="result">Ergebnis</p>

                <input type="button" id="ok" name="ok" value ="OK">
                <input type="submit" id="submit" value ="Weiter">

                <input type="hidden" id="color" name="color" value="">
                <input type="hidden" id="box" name="box" value="">
                <input type="hidden" id="p" name="p" value="">
                <input type="hidden" id="time" name="time" value="">
                <input type="hidden" id="step" name="step" value="<?=$step?>">
                <?php break;?>
            <?php default:?>
                <?php break;?>
        <?php } ?>
    </body>
</html>






