
<?php
$numeri = [1,2,3,4,5,6,7,8];

?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
<?php
foreach($numeri as $numero){
    if($numero %2 == 0 ){
        echo "Numeri pari:" . $numero;

    }
    else{
        echo"Numeri dispari:" . $numero;
    }
}
?>
</body>
</html>

