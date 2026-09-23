<?php
require "../config/db.php";
 
// Leggiamo i filtri dall'URL (metodo GET, così restano visibili/condivisibili e la pagina resta "bookmarkabile")
$titolo_cercato = trim($_GET['titolo'] ?? '');
$id_genere_filtro = trim($_GET['genere'] ?? '');
$id_autore_filtro = trim($_GET['regista'] ?? '');
 
// Costruiamo la query pezzo per pezzo
$sql = "SELECT f.id_film, f.titolo, f.anno_uscita, f.locandina,
               a.nome AS nome_regista, a.cognome AS cognome_regista
        FROM film f
        LEFT JOIN autori a ON f.id_autore = a.id_autore";
 
$condizioni = [];
$parametri = [];
 
if ($titolo_cercato !== '') {
    $condizioni[] = "f.titolo LIKE ?";
    $parametri[] = "%" . $titolo_cercato . "%";
}
if ($id_genere_filtro !== '') {
    $condizioni[] = "f.id_film IN (SELECT id_film FROM film_generi WHERE id_genere = ?)";
    $parametri[] = $id_genere_filtro;
}
if ($id_autore_filtro !== '') {
    $condizioni[] = "f.id_autore = ?";
    $parametri[] = $id_autore_filtro;
}
 
if (!empty($condizioni)) {
    $sql .= " WHERE " . implode(" AND ", $condizioni);
}
$sql .= " ORDER BY f.titolo";
 
$stmt = mysqli_prepare($conn, $sql);
if (!empty($parametri)) {
    mysqli_stmt_execute($stmt, $parametri);
} else {
    mysqli_stmt_execute($stmt);
}
$risultato = mysqli_stmt_get_result($stmt);
 
$film_trovati = [];
while ($riga = mysqli_fetch_assoc($risultato)) {
    $film_trovati[] = $riga;
}
 
// Per popolare le tendine del form di ricerca
$generi = [];
$risultato_generi = mysqli_query($conn, "SELECT id_genere, nome FROM generi ORDER BY nome");
while ($riga = mysqli_fetch_assoc($risultato_generi)) {
    $generi[] = $riga;
}
 
$autori = [];
$risultato_autori = mysqli_query($conn, "SELECT id_autore, nome, cognome FROM autori ORDER BY cognome");
while ($riga = mysqli_fetch_assoc($risultato_autori)) {
    $autori[] = $riga;
}
 
require "../includes/header_pubblico.php";
?>
 
<h1 class="mb-4">Tutti i film</h1>
 
<form method="GET" action="index.php" class="row g-3 mb-4 bg-white p-3 rounded shadow-sm">
  <div class="col-md-4">
    <label for="titolo" class="form-label">Cerca per titolo</label>
    <input type="text" id="titolo" name="titolo" class="form-control" value="<?= htmlspecialchars($titolo_cercato) ?>">
  </div>
  <div class="col-md-3">
    <label for="genere" class="form-label">Genere</label>
    <select id="genere" name="genere" class="form-select">
      <option value="">Tutti</option>
      <?php foreach ($generi as $genere): ?>
        <option value="<?= $genere['id_genere'] ?>" <?= ($id_genere_filtro == $genere['id_genere']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($genere['nome']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <label for="regista" class="form-label">Regista</label>
    <select id="regista" name="regista" class="form-select">
      <option value="">Tutti</option>
      <?php foreach ($autori as $autore): ?>
        <option value="<?= $autore['id_autore'] ?>" <?= ($id_autore_filtro == $autore['id_autore']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($autore['nome'] . ' ' . $autore['cognome']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button type="submit" class="btn btn-primary w-100">Cerca</button>
  </div>
</form>
 
<?php if (empty($film_trovati)): ?>
  <div class="alert alert-info">Nessun film trovato con questi criteri di ricerca.</div>
<?php else: ?>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($film_trovati as $film): ?>
      <div class="col">
        <?php require "../includes/card_film.php"; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
 
<?php require "../includes/footer_pubblico.php"; ?>