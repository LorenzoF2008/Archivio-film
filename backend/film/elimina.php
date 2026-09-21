<?php
require "../../config/db.php";
 
$id = $_GET['id'] ?? $_POST['id'] ?? null;
 
if (!$id || !is_numeric($id)) {
    die("ID film non valido.");
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prima di eliminare la riga, recuperiamo il nome del file da cancellare
    $sql_file = "SELECT locandina FROM film WHERE id_film = ?";
    $stmt_file = mysqli_prepare($conn, $sql_file);
    mysqli_stmt_bind_param($stmt_file, "i", $id);
    mysqli_stmt_execute($stmt_file);
    $risultato_file = mysqli_stmt_get_result($stmt_file);
    $riga_file = mysqli_fetch_assoc($risultato_file);
 
    $sql = "DELETE FROM film WHERE id_film = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
 
    // Se il film aveva una locandina caricata, cancelliamo anche il file fisico
    if (!empty($riga_file['locandina'])) {
        $percorso_file = "../../assets/img/" . $riga_file['locandina'];
        if (file_exists($percorso_file)) {
            unlink($percorso_file);
        }
    }
 
    header("Location: elenco.php");
    exit;
}
 
$sql = "SELECT titolo FROM film WHERE id_film = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$risultato = mysqli_stmt_get_result($stmt);
$film = mysqli_fetch_assoc($risultato);
 
if (!$film) {
    die("Film non trovato.");
}
 
require "../../includes/header.php";
?>
 
<h2 class="mb-3">Conferma eliminazione</h2>
 
<div class="card shadow-sm" style="max-width: 500px;">
  <div class="card-body">
    <p>Sei sicuro di voler eliminare il film «<strong><?= htmlspecialchars($film['titolo']) ?></strong>»?</p>
    <p class="text-muted small">Verranno rimossi anche i collegamenti ai generi e la locandina caricata.</p>
 
    <form method="POST" action="elimina.php">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <button type="submit" class="btn btn-danger">Sì, elimina</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>
 
<?php require "../../includes/footer.php"; ?>