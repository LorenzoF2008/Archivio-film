<?php
require "../../config/db.php";

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID autore non valido.");
}

$errori = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $nazionalita = trim($_POST['nazionalita'] ?? '');
    $data_nascita = trim($_POST['data_nascita'] ?? '');

    if ($nome === '') {
        $errori[] = "Il nome è obbligatorio.";
    }
    if ($cognome === '') {
        $errori[] = "Il cognome è obbligatorio.";
    }
    if ($data_nascita !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_nascita)) {
        $errori[] = "La data di nascita non è in un formato valido.";
    }

    if (empty($errori)) {
        $nazionalita_db = $nazionalita !== '' ? $nazionalita : null;
        $data_nascita_db = $data_nascita !== '' ? $data_nascita : null;

        $sql = "UPDATE autori SET nome = ?, cognome = ?, nazionalità = ?, data_nascita = ? WHERE id_autore = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $nome, $cognome, $nazionalita_db, $data_nascita_db, $id);
        mysqli_stmt_execute($stmt);

        header("Location: elenco.php");
        exit;
    }
} else {
    $sql = "SELECT nome, cognome, nazionalità, data_nascita FROM autori WHERE id_autore = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $risultato = mysqli_stmt_get_result($stmt);
    $autore = mysqli_fetch_assoc($risultato);

    if (!$autore) {
        die("Autore non trovato.");
    }

    $nome = $autore['nome'];
    $cognome = $autore['cognome'];
    $nazionalita = $autore['nazionalità'] ?? '';
    $data_nascita = $autore['data_nascita'] ?? '';
}

require "../../includes/header.php";
?>

<h2 class="mb-3">Modifica Autore</h2>

<?php if (!empty($errori)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errori as $errore): ?>
        <li><?= htmlspecialchars($errore) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 500px;">
  <div class="card-body">
    <form method="POST" action="modifica.php">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>">
      </div>
      <div class="mb-3">
        <label for="cognome" class="form-label">Cognome</label>
        <input type="text" id="cognome" name="cognome" class="form-control" value="<?= htmlspecialchars($cognome) ?>">
      </div>
      <div class="mb-3">
        <label for="nazionalita" class="form-label">Nazionalità <span class="text-muted small">(facoltativo)</span></label>
        <input type="text" id="nazionalita" name="nazionalita" class="form-control" value="<?= htmlspecialchars($nazionalita) ?>">
      </div>
      <div class="mb-3">
        <label for="data_nascita" class="form-label">Data di nascita <span class="text-muted small">(facoltativo)</span></label>
        <input type="date" id="data_nascita" name="data_nascita" class="form-control" value="<?= htmlspecialchars($data_nascita) ?>">
      </div>
      <button type="submit" class="btn btn-primary">Salva modifiche</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>

<?php require "../../includes/footer.php"; ?>