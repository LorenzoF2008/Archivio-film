<?php
require "../../config/db.php";

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID autore non valido.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM autori WHERE id_autore = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    header("Location: elenco.php");
    exit;
}

$sql = "SELECT nome, cognome FROM autori WHERE id_autore = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$risultato = mysqli_stmt_get_result($stmt);
$autore = mysqli_fetch_assoc($risultato);

if (!$autore) {
    die("Autore non trovato.");
}

// Contiamo quanti film sono collegati a questo autore, per avvisare l'utente prima di eliminare
$sql_conteggio = "SELECT COUNT(*) AS numero_film FROM film WHERE id_autore = ?";
$stmt2 = mysqli_prepare($conn, $sql_conteggio);
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
$risultato2 = mysqli_stmt_get_result($stmt2);
$conteggio = mysqli_fetch_assoc($risultato2);
$numero_film = $conteggio['numero_film'];

require "../../includes/header.php";
?>

<h2 class="mb-3">Conferma eliminazione</h2>

<div class="card shadow-sm" style="max-width: 500px;">
  <div class="card-body">
    <p>Sei sicuro di voler eliminare l'autore «<strong><?= htmlspecialchars($autore['nome'] . ' ' . $autore['cognome']) ?></strong>»?</p>

    <?php if ($numero_film > 0): ?>
      <div class="alert alert-warning">
        Questo autore è collegato a <strong><?= $numero_film ?></strong> film.
        Non verranno eliminati, ma resteranno senza un regista specificato.
      </div>
    <?php endif; ?>

    <form method="POST" action="elimina.php">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <button type="submit" class="btn btn-danger">Sì, elimina</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>

<?php require "../../includes/footer.php"; ?>