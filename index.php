<?php
$Nomesito = "Archivio Film";
$Benvenuto = "Benvenuti nel mio " . $Nomesito . "! Qui potrete trovare informazioni sui film piu acclamati dalla critica.";
$titolo = "Django";
$anno = 2012;
$regista = "Quentin Tarantino";
$genere = "Western";
?>
<!Doctype html>
<head>
    <meta charset="UTF-8">
    <title>Archivio Film</title>
    <body>
        <p> <?php echo $Benvenuto; ?></p>
        <p>Film: <?php echo $titolo; ?></p>
        <p>Anno: <?php echo $anno; ?></p>
        <p>Regista: <?php echo $regista; ?></p>
        <p>Genere: <?php echo $genere; ?></p>
    </body>
</head>
</html>
