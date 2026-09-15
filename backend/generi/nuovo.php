<?php
require "../../config/db.php";
 
$errori = [];
$nome = "";
 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    
    if ($nome === '') {
        $errori[] = "Il nome del genere è obbligatorio.";
    }
 
    if (empty($errori)) {
        $sql = "INSERT INTO generi (nome) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nome);
        mysqli_stmt_execute($stmt);
 
        header("Location: elenco.php");
        exit;
    }
    }

 
require "../../includes/header.php";
?>
 
<h2 class="mb-3">Nuovo Genere</h2>
 
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
        <label for="nome" class="form-label">Nome del genere</label>
        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>">
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="elenco.php" class="btn btn-secondary">Annulla</a>
    </form>
  </div>
</div>
 
<?php require "../../includes/footer.php"; ?>