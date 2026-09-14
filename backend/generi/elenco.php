<?php
require "../../config/db.php";
require "../../includes/header.php";
 
$sql = "SELECT id_genere, nome FROM generi ORDER BY nome";
$risultato = mysqli_query($conn, $sql);
?>
 
<h2 class="mb-3">Elenco Generi</h2>
<a href="nuovo.php" class="btn btn-success mb-3">+ Nuovo genere</a>
 
<table class="table table-striped table-hover bg-white shadow-sm">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th>Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($riga = mysqli_fetch_assoc($risultato)): ?>
    <tr>
      <td><?= htmlspecialchars($riga['id_genere']) ?></td>
      <td><?= htmlspecialchars($riga['nome']) ?></td>
      <td>
        <a href="modifica.php?id=<?= $riga['id_genere'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a>
        <a href="elimina.php?id=<?= $riga['id_genere'] ?>" class="btn btn-sm btn-outline-danger">Elimina</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
 
<?php require "../../includes/footer.php"; ?>