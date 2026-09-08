//stampare numeri pari dato un array di numeri
<? php
$numeri = [1,2,3,4,5,6,7,8];

foreach($numeri as $numero){
    if($numero %2 ==0 ){
        echo "Numeri pari: $numero<br>";
    }
    else{
        echo"Numeri dispari: $numero<br>";
    }
}
?>