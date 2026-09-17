<?php
require "../../config/db.php";
require "../../includes/header.php";

$sql = "SELECT id_autore, nome, cognome, nazionalità, data_nascita FROM autori ORDER BY cognome, nome";
$risultato = mysqli_query($conn, $sql);
?>

<h2 class="mb-3">Elenco Autori</h2>
<a href="nuovo.php" class="btn btn-success mb-3">+ Nuovo autore</a>

<table class="table table-striped table-hover bg-white shadow-sm">
  <thead class="table-dark">
    <tr>
      <th>Nome</th>
      <th>Cognome</th>
      <th>Nazionalità</th>
      <th>Data di nascita</th>
      <th>Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($riga = mysqli_fetch_assoc($risultato)): ?>
    <tr>
      <td><?= htmlspecialchars($riga['nome']) ?></td>
      <td><?= htmlspecialchars($riga['cognome']) ?></td>
      <td><?= htmlspecialchars($riga['nazionalità'] ?? '-') ?></td>
      <td>
        <?php if ($riga['data_nascita']): ?>
          <?= date("d/m/Y", strtotime($riga['data_nascita'])) ?>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td>
        <a href="modifica.php?id=<?= $riga['id_autore'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a>
        <a href="elimina.php?id=<?= $riga['id_autore'] ?>" class="btn btn-sm btn-outline-danger">Elimina</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php require "../../includes/footer.php"; ?>