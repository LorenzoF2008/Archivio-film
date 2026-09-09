// Esercizio 2 , creare un array associativo  film e stamparlo formatatto HTML//
<?php
$film = ["titolo" => "Matrix", "anno" => 1999, "durata" => 136];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
</head>
    <body>
        <h1>FILM</h1>
        <p>Film:<?php echo $film['titolo'];?></p>
        <p>Anno:<?php echo $film['anno'];?></p>
        <p>Durata:<?php echo $film['durata'];?></p>
    </body>
</html>
