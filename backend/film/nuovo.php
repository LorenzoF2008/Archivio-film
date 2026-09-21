<?php
require "../../config/db.php"; 
 
$errori = [];
$titolo = "";
$anno_uscita = "";
$durata_minuti = "";
$trama = "";
$id_autore_selezionato = "";
$generi_selezionati = [];
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = trim($_POST['titolo'] ?? '');
    $anno_uscita = trim($_POST['anno_uscita'] ?? '');
    $durata_minuti = trim($_POST['durata_minuti'] ?? '');
    $trama = trim($_POST['trama'] ?? '');
    $id_autore_selezionato = trim($_POST['id_autore'] ?? '');
    $generi_selezionati = $_POST['generi'] ?? []; // array di id genere, es. ["1", "3"]
 
    if ($titolo === '') {
        $errori[] = "Il titolo è obbligatorio.";
    }
    if ($anno_uscita !== '' && !is_numeric($anno_uscita)) {
        $errori[] = "L'anno di uscita deve essere un numero.";
    }
    if ($durata_minuti !== '' && !is_numeric($durata_minuti)) {
        $errori[] = "La durata deve essere un numero.";
    }
 
    // Gestione dell'upload della locandina (facoltativa)
    $nome_file_locandina = null;
    if (!empty($_FILES['locandina']['name'])) {
        
        $estensioni_ammesse = ['jpg', 'jpeg', 'png', 'webp'];
        $estensione = strtolower(pathinfo($_FILES['locandina']['name'], PATHINFO_EXTENSION));
 
        if (!in_array($estensione, $estensioni_ammesse)) {
            $errori[] = "La locandina deve essere un'immagine (jpg, png o webp).";
        } elseif ($_FILES['locandina']['size'] > 2 * 1024 * 1024) { // limite: 2 MB
            $errori[] = "La locandina non può superare i 2 MB.";
        } else {
            // Generiamo un nome file unico, per evitare che due upload con lo stesso nome si sovrascrivano
            $nome_file_locandina = uniqid("film_") . "." . $estensione;
            $percorso_destinazione = "../../assets/img/" . $nome_file_locandina;
            move_uploaded_file($_FILES['locandina']['tmp_name'], $percorso_destinazione);
        }
    }
 
    if (empty($errori)) {
        $anno_db = $anno_uscita !== '' ? $anno_uscita : null;
        $durata_db = $durata_minuti !== '' ? $durata_minuti : null;
        $id_autore_db = $id_autore_selezionato !== '' ? $id_autore_selezionato : null;
 
        $sql = "INSERT INTO film (titolo, anno_uscita, durata_minuti, trama, locandina, id_autore)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "siissi", $titolo, $anno_db, $durata_db, $trama, $nome_file_locandina, $id_autore_db);
        mysqli_stmt_execute($stmt);
 
        $id_film_nuovo = mysqli_insert_id($conn);
 
        // Inseriamo un collegamento in film_generi per ogni genere selezionato
        $sql_genere = "INSERT INTO film_generi (id_film, id_genere) VALUES (?, ?)";
        $stmt_genere = mysqli_prepare($conn, $sql_genere);
        foreach ($generi_selezionati as $id_genere) {
            mysqli_stmt_bind_param($stmt_genere, "ii", $id_film_nuovo, $id_genere);
            mysqli_stmt_execute($stmt_genere);
        }
 
        header("Location: elenco.php");
        exit;
    }
}
 
// Recuperiamo l'elenco di tutti gli autori, per popolare la <select>
$autori = [];
$risultato_autori = mysqli_query($conn, "SELECT id_autore, nome, cognome FROM autori ORDER BY cognome");
while ($riga = mysqli_fetch_assoc($risultato_autori)) {
    $autori[] = $riga;
}
 
// Recuperiamo l'elenco di tutti i generi, per popolare le checkbox
$generi = [];
$risultato_generi = mysqli_query($conn, "SELECT id_genere, nome FROM generi ORDER BY nome");
while ($riga = mysqli_fetch_assoc($risultato_generi)) {
    $generi[] = $riga;
}
 
require "../../includes/header.php";
?>
 
<h2 class="mb-3">Nuovo Film</h2>
 
<?php if (!empty($errori)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errori as $errore): ?>
        <li><?= htmlspecialchars($errore) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
 
<div class="card shadow-sm" style="max-width: 650px;">
  <div class="card-body">
    <form method="POST" action="nuovo.php" enctype="multipart/form-data">
 
      <div class="mb-3">
        <label for="titolo" class="form-label">Titolo</label>
        <input type="text" id="titolo" name="titolo" class="form-control" value="<?= htmlspecialchars($titolo) ?>">
      </div>
 
      <div class="row">
        <div class="col mb-3">
          <label for="anno_uscita" class="form-label">Anno di uscita</label>
          <input type="number" id="anno_uscita" name="anno_uscita" class="form-control" value="<?= htmlspecialchars($anno_uscita) ?>">
        </div>
        <div class="col mb-3">
          <label for="durata_minuti" class="form-label">Durata (minuti)</label>
          <input type="number" id="durata_minuti" name="durata_minuti" class="form-control" value="<?= htmlspecialchars($durata_minuti) ?>">
        </div>
      </div>
 
      <div class="mb-3">
        <label for="trama" class="form-label">Trama</label>
        <textarea id="trama" name="trama" class="form-control" rows="4"><?= htmlspecialchars($trama) ?></textarea>
      </div>
 
      <div class="mb-3">
        <label for="id_autore" class="form-label">Regista</label>
        <select id="id_autore" name="id_autore" class="form-select">
          <option value="">-- Nessuno / non specificato --</option>
          <?php foreach ($autori as $autore): ?>
            <option value="<?= $autore['id_autore'] ?>" <?= ($id_autore_selezionato == $autore['id_autore']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($autore['nome'] . ' ' . $autore['cognome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
 
      <div class="mb-3">
        <label class="form-label">Generi</label><br>
        <?php foreach ($generi as $genere): ?>
          <div class="form-check form-check-inline">
            <input type="checkbox" class="form-check-input" name="generi[]"
                   id="genere_<?= $genere['id_genere'] ?>" value="<?= $genere['id_genere'] ?>"
                   <?= in_array($genere['id_genere'], $generi_selezionati) ? 'checked' : '' ?>>
            <label class="form-check-label" for="genere_<?= $genere['id_genere'] ?>">
              <?= htmlspecialchars($genere['nome']) ?>
            </label>
          </div>
        <?php endforeach; ?>
      </div>
 
      <div class="mb-3">
        <label for="locandina" class="form-label">Locandina <span class="text-muted small">(facoltativa, jpg/png/webp, max 2MB)</span></label>
        <input type="file" id="locandina" name="locandina" class="form-control" accept="image/*">
      </div>
 
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>
 
<?php require "../../includes/footer.php"; ?>