<?php
require "../config/db.php";
 
$sql = "SELECT f.id_film, f.titolo, f.anno_uscita, f.locandina,
               a.nome AS nome_regista, a.cognome AS cognome_regista
        FROM film f
        LEFT JOIN autori a ON f.id_autore = a.id_autore
        ORDER BY f.titolo";
$risultato = mysqli_query($conn, $sql);
 
$film_trovati = [];
while ($riga = mysqli_fetch_assoc($risultato)) {
    $film_trovati[] = $riga;
}
 
require "../includes/header_pubblico.php";
?>
 
<h1 class="mb-4">Tutti i film</h1>
 
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
  <?php foreach ($film_trovati as $film): ?>
    <div class="col">
      <?php require "../includes/card_film.php"; ?>
    </div>
  <?php endforeach; ?>
</div>
 
<?php require "../includes/footer_pubblico.php"; ?>