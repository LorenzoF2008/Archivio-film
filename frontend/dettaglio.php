<?php
require "../config/db.php";
 
$id = $_GET['id'] ?? null;
 
if (!$id || !is_numeric($id)) {
    die("Film non valido.");
}
 
$sql = "SELECT f.id_film, f.titolo, f.anno_uscita, f.durata_minuti, f.trama, f.locandina,
               a.id_autore, a.nome AS nome_regista, a.cognome AS cognome_regista
        FROM film f
        LEFT JOIN autori a ON f.id_autore = a.id_autore
        WHERE f.id_film = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$risultato = mysqli_stmt_get_result($stmt);
$film = mysqli_fetch_assoc($risultato);
 
if (!$film) {
    die("Film non trovato.");
}
 
$sql_generi = "SELECT g.nome
               FROM generi g
               JOIN film_generi fg ON g.id_genere = fg.id_genere
               WHERE fg.id_film = ?";
$stmt_generi = mysqli_prepare($conn, $sql_generi);
mysqli_stmt_bind_param($stmt_generi, "i", $id);
mysqli_stmt_execute($stmt_generi);
$risultato_generi = mysqli_stmt_get_result($stmt_generi);
 
$nomi_generi = [];
while ($riga = mysqli_fetch_assoc($risultato_generi)) {
    $nomi_generi[] = $riga['nome'];
}
 
$percorso_immagine = $film['locandina']
    ? "/archivio-film/assets/img/" . $film['locandina']
    : "/archivio-film/assets/img/placeholder.png";
 
require "../includes/header_pubblico.php";
?>
 
<a href="index.php" class="btn btn-outline-secondary btn-sm mb-4">&larr; Torna all'elenco</a>
 
<div class="row">
  <div class="col-md-4">
    <img src="<?= htmlspecialchars($percorso_immagine) ?>" class="img-fluid rounded shadow-sm" alt="Locandina">
  </div>
  <div class="col-md-8">
    <h1><?= htmlspecialchars($film['titolo']) ?></h1>
    <p class="text-muted">
      <?= htmlspecialchars($film['anno_uscita'] ?? '-') ?>
      <?php if ($film['durata_minuti']): ?>
        &middot; <?= htmlspecialchars($film['durata_minuti']) ?> min
      <?php endif; ?>
    </p>
 
    <p>
      <?php foreach ($nomi_generi as $nome_genere): ?>
        <span class="badge bg-secondary"><?= htmlspecialchars($nome_genere) ?></span>
      <?php endforeach; ?>
    </p>
 
    <p>
      <strong>Regista:</strong>
      <?php if ($film['id_autore']): ?>
        <a href="regista.php?id=<?= $film['id_autore'] ?>">
          <?= htmlspecialchars($film['nome_regista'] . ' ' . $film['cognome_regista']) ?>
        </a>
      <?php else: ?>
        Non specificato
      <?php endif; ?>
    </p>
 
    <h5 class="mt-4">Trama</h5>
    <p><?= nl2br(htmlspecialchars($film['trama'] ?? 'Nessuna trama disponibile.')) ?></p>
  </div>
</div>
 
<?php require "../includes/footer_pubblico.php"; ?>