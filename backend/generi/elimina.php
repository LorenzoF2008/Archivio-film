<?php
require "../../config/db.php";
 
$id = $_GET['id'] ?? $_POST['id'] ?? null;
 
if (!$id || !is_numeric($id)) {
    die("ID genere non valido.");
}
 
// Se l'utente ha confermato (form inviato in POST), procediamo con l'eliminazione
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM generi WHERE id_genere = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
 
    header("Location: elenco.php");
    exit;
}
 
// Altrimenti (prima apertura, GET), leggiamo il genere per mostrarne il nome nella domanda di conferma
$sql = "SELECT nome FROM generi WHERE id_genere = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$risultato = mysqli_stmt_get_result($stmt);
$genere = mysqli_fetch_assoc($risultato);
 
if (!$genere) {
    die("Genere non trovato.");
}
 
require "../../includes/header.php";
?>
 
<h2 class="mb-3">Conferma eliminazione</h2>
 
<div class="card shadow-sm" style="max-width: 500px;">
  <div class="card-body">
    <p>Sei sicuro di voler eliminare il genere «<strong><?= htmlspecialchars($genere['nome']) ?></strong>»?</p>
    <p class="text-muted small">Attenzione: verranno rimossi anche tutti i collegamenti di questo genere con eventuali film esistenti.</p>
 
    <form method="POST" action="elimina.php">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <button type="submit" class="btn btn-danger">Sì, elimina</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>
 
<?php require "../../includes/footer.php"; ?>