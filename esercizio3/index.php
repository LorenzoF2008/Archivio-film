//Esercizio 3, creare un array multdimensioanale con 4-5 film (aarray di array associativi) e stampare un elenco HTML,con un foreach.//
<?php
$films= [
    [ "titolo" => "Avatar", "anno"=> 2009, "duarata"=> 162],
    [ "titolo" => "inception", "anno" => 2010, "durata" => 148],
    [ "titolo" => "shutter island", "anno" => 2010, "durata" => 138],
    [ "titolo" => "Look back", "anno" => 2024, "durata" => 58] 
    ];
    ?>
    <!DOCTYPE html>
    <html lang="it">
        <head>
            <meta charset="UTF-8">
            </head>
            <body>
                <h1> FILM </h1>
                <?php foreach($films as $film){ ?>
                    <p>Film:<?php echo $film["titolo"];?></p>
                    <p>Anno:<?php echo $film["anno"];?></p>
                    <p>Duarata:<?php echo $film["durata"];?></p>
                <?php } ?>
            </body>
            </html>
            