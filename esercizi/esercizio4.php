
<?php 
$nome= "Lorenzo";
function saluta($nome) {
    return " ciao " . $nome . " come stai? ";
}
?>
<DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php echo saluta($nome); ?>
</body>
</html>

