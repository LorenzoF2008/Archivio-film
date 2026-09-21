<?php
require "../../config/db.php";
 
$id = $_GET['id'] ?? $_POST['id'] ?? null;
 
if (!$id || !is_numeric($id)) {
    die("ID film non valido.");
}
 
$errori = [];
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = trim($_POST['titolo'] ?? '');
    $anno_uscita = trim($_POST['anno_uscita'] ?? '');
    $durata_minuti = trim($_POST['durata_minuti'] ?? '');
    $trama = trim($_POST['trama'] ?? '');
    $id_autore_selezionato = trim($_POST['id_autore'] ?? '');
    $generi_selezionati = $_POST['generi'] ?? [];
    $locandina_attuale = $_POST['locandina_attuale'] ?? null; // il nome file già esistente, passato come campo nascosto
 
    if ($titolo === '') {
        $errori[] = "Il titolo è obbligatorio.";
    }
    if ($anno_uscita !== '' && !is_numeric($anno_uscita)) {
        $errori[] = "L'anno di uscita deve essere un numero.";
    }
    if ($durata_minuti !== '' && !is_numeric($durata_minuti)) {
        $errori[] = "La durata deve essere un numero.";
    }
 
    // Per la locandina: partiamo dal presupposto di NON cambiarla...
    $nome_file_locandina = $locandina_attuale;
 
    // ...ma se l'utente ne ha caricata una nuova, la sostituiamo
    if (!empty($_FILES['locandina']['name'])) {
        $estensioni_ammesse = ['jpg', 'jpeg', 'png', 'webp'];
        $estensione = strtolower(pathinfo($_FILES['locandina']['name'], PATHINFO_EXTENSION));
 
        if (!in_array($estensione, $estensioni_ammesse)) {
            $errori[] = "La locandina deve essere un'immagine (jpg, png o webp).";
        } elseif ($_FILES['locandina']['size'] > 2 * 1024 * 1024) {
            $errori[] = "La locandina non può superare i 2 MB.";
        } else {
            $nome_file_locandina = uniqid("film_") . "." . $estensione;
            $percorso_destinazione = "../../assets/img/" . $nome_file_locandina;
            move_uploaded_file($_FILES['locandina']['tmp_name'], $percorso_destinazione);
        }
    }
 
    if (empty($errori)) {
        $anno_db = $anno_uscita !== '' ? $anno_uscita : null;
        $durata_db = $durata_minuti !== '' ? $durata_minuti : null;
        $id_autore_db = $id_autore_selezionato !== '' ? $id_autore_selezionato : null;
 
        $sql = "UPDATE film
                SET titolo = ?, anno_uscita = ?, durata_minuti = ?, trama = ?, locandina = ?, id_autore = ?
                WHERE id_film = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "siissii", $titolo, $anno_db, $durata_db, $trama, $nome_file_locandina, $id_autore_db, $id);
        mysqli_stmt_execute($stmt);
 
        // Sincronizziamo i generi: cancelliamo tutti i collegamenti attuali e re-inseriamo quelli scelti ora.
        // È la strategia più semplice per gestire una relazione N-N in fase di modifica.
        $sql_delete = "DELETE FROM film_generi WHERE id_film = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        mysqli_stmt_execute($stmt_delete);
 
        $sql_genere = "INSERT INTO film_generi (id_film, id_genere) VALUES (?, ?)";
        $stmt_genere = mysqli_prepare($conn, $sql_genere);
        foreach ($generi_selezionati as $id_genere) {
            mysqli_stmt_bind_param($stmt_genere, "ii", $id, $id_genere);
            mysqli_stmt_execute($stmt_genere);
        }
 
        header("Location: elenco.php");
        exit;
    }
} else {
    // Prima apertura: leggiamo tutti i dati esistenti del film
    $sql = "SELECT titolo, anno_uscita, durata_minuti, trama, locandina, id_autore FROM film WHERE id_film = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $risultato = mysqli_stmt_get_result($stmt);
    $film = mysqli_fetch_assoc($risultato);
 
    if (!$film) {
        die("Film non trovato.");
    }
 
    $titolo = $film['titolo'];
    $anno_uscita = $film['anno_uscita'] ?? '';
    $durata_minuti = $film['durata_minuti'] ?? '';
    $trama = $film['trama'] ?? '';
    $id_autore_selezionato = $film['id_autore'] ?? '';
    $locandina_attuale = $film['locandina'];
 
    // Leggiamo anche quali generi sono attualmente collegati a questo film
    $sql_generi_attuali = "SELECT id_genere FROM film_generi WHERE id_film = ?";
    $stmt_generi = mysqli_prepare($conn, $sql_generi_attuali);
    mysqli_stmt_bind_param($stmt_generi, "i", $id);
    mysqli_stmt_execute($stmt_generi);
    $risultato_generi_attuali = mysqli_stmt_get_result($stmt_generi);
 
    $generi_selezionati = [];
    while ($riga = mysqli_fetch_assoc($risultato_generi_attuali)) {
        $generi_selezionati[] = $riga['id_genere'];
    }
}
 
// Elenco di tutti gli autori e tutti i generi (serve sia alla prima apertura sia dopo un errore di validazione)
$autori = [];
$risultato_autori = mysqli_query($conn, "SELECT id_autore, nome, cognome FROM autori ORDER BY cognome");
while ($riga = mysqli_fetch_assoc($risultato_autori)) {
    $autori[] = $riga;
}
 
$generi = [];
$risultato_generi = mysqli_query($conn, "SELECT id_genere, nome FROM generi ORDER BY nome");
while ($riga = mysqli_fetch_assoc($risultato_generi)) {
    $generi[] = $riga;
}
 
require "../../includes/header.php";
?>
 
<h2 class="mb-3">Modifica Film</h2>
 
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
 
    <?php if ($locandina_attuale): ?>
      <p>
        <img src="/archivio-film/assets/img/<?= htmlspecialchars($locandina_attuale) ?>" style="width:100px;" class="mb-2"><br>
        <span class="text-muted small">Locandina attuale (caricane una nuova solo se vuoi sostituirla)</span>
      </p>
    <?php endif; ?>
 
    <form method="POST" action="modifica.php" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <input type="hidden" name="locandina_attuale" value="<?= htmlspecialchars($locandina_attuale ?? '') ?>">
 
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
        <label for="locandina" class="form-label">Nuova locandina <span class="text-muted small">(lascia vuoto per non cambiarla)</span></label>
        <input type="file" id="locandina" name="locandina" class="form-control" accept="image/*">
      </div>
 
      <button type="submit" class="btn btn-primary">Salva modifiche</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>
 
<?php require "../../includes/footer.php"; ?>