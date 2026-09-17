<?php
require "../../config/db.php";

$errori = [];
$nome = "";
$cognome = "";
$nazionalita = "";
$data_nascita = "";

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

    // La data di nascita è facoltativa, ma SE viene scritta, deve avere un formato valido
    if ($data_nascita !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_nascita)) {
        $errori[] = "La data di nascita non è in un formato valido.";
    }

    if (empty($errori)) {
        // I campi facoltativi vuoti li salviamo come NULL, non come stringa vuota
        $nazionalita_db = $nazionalita !== '' ? $nazionalita : null;
        $data_nascita_db = $data_nascita !== '' ? $data_nascita : null;

        $sql = "INSERT INTO autori (nome, cognome, nazionalità, data_nascita) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $nome, $cognome, $nazionalita_db, $data_nascita_db);
        mysqli_stmt_execute($stmt);

        header("Location: elenco.php");
        exit;
    }
}

require "../../includes/header.php";
?>

<h2 class="mb-3">Nuovo Autore</h2>

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
    <form method="POST" action="nuovo.php">
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
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>

<?php require "../../includes/footer.php"; ?>