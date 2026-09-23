<?php
require "../config/db.php";
 
$id = $_GET['id'] ?? null;
 
if (!$id || !is_numeric($id)) {
    die("Regista non valido.");
}
 
$sql = "SELECT id_autore, nome, cognome, nazionalità, data_nascita FROM autori WHERE id_autore = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$risultato = mysqli_stmt_get_result($stmt);
$autore = mysqli_fetch_assoc($risultato);
 
if (!$autore) {
    die("Regista non trovato.");
}
 
$sql_film = "SELECT f.id_film, f.titolo, f.anno_uscita, f.locandina,
                    a.nome AS nome_regista, a.cognome AS cognome_regista
             FROM film f
             JOIN autori a ON f.id_autore = a.id_autore
             WHERE f.id_autore = ?
             ORDER BY f.titolo";
$stmt_film = mysqli_prepare($conn, $sql_film);
mysqli_stmt_bind_param($stmt_film, "i", $id);
mysqli_stmt_execute($stmt_film);
$risultato_film = mysqli_stmt_get_result($stmt_film);
 
$film_del_regista = [];
while ($riga = mysqli_fetch_assoc($risultato_film)) {
    $film_del_regista[] = $riga;
}
 
require "../includes/header_pubblico.php";
?>
 
<a href="index.php" class="btn btn-outline-secondary btn-sm mb-4">&larr; Torna all'elenco</a>
 
<h1><?= htmlspecialchars($autore['nome'] . ' ' . $autore['cognome']) ?></h1>
<p class="text-muted">
  <?= htmlspecialchars($autore['nazionalita'] ?? '') ?>
  <?php if ($autore['data_nascita']): ?>
    &middot; nato il <?= date("d/m/Y", strtotime($autore['data_nascita'])) ?>
  <?php endif; ?>
</p>
 
<h4 class="mt-4 mb-3">Film diretti</h4>
 
<?php if (empty($film_del_regista)): ?>
  <p>Nessun film registrato per questo regista.</p>
<?php else: ?>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($film_del_regista as $film): ?>
      <div class="col">
        <?php require "../includes/card_film.php"; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
 
<?php require "../includes/footer_pubblico.php"; ?>