<?php
require "../../config/db.php";
 
$id = $_GET['id'] ?? $_POST['id'] ?? null;
 
if (!$id || !is_numeric($id)) {
    die("ID genere non valido.");
}
 
$errori = [];
 
// Se il form è stato inviato, proviamo prima a salvare
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
 
    if ($nome === '') {
        $errori[] = "Il nome del genere è obbligatorio.";
    }
 
    if (empty($errori)) {
        $sql = "UPDATE generi SET nome = ? WHERE id_genere = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nome, $id);
        mysqli_stmt_execute($stmt);
 
        header("Location: elenco.php");
        exit;
    }
} else {
    // Prima apertura della pagina: leggiamo i dati attuali dal database per precompilare il form
    $sql = "SELECT nome FROM generi WHERE id_genere = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $risultato = mysqli_stmt_get_result($stmt);
    $genere = mysqli_fetch_assoc($risultato);
 
    if (!$genere) {
        die("Genere non trovato.");
    }
 
    $nome = $genere['nome'];
}
 
require "../../includes/header.php";
?>
 
<h2 class="mb-3">Modifica Genere</h2>
 
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
        <label for="nome" class="form-label">Nome del genere</label>
        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>">
      </div>
      <button type="submit" class="btn btn-primary">Salva modifiche</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>
 
<?php require "../../includes/footer.php"; ?>