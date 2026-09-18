<?php
require "../../config/db.php";
require "../../includes/header.php";
 
$sql = "SELECT f.id_film, f.titolo, f.anno_uscita, f.locandina,
               a.nome AS nome_regista, a.cognome AS cognome_regista
        FROM film f
        LEFT JOIN autori a ON f.id_autore = a.id_autore
        ORDER BY f.titolo";
$risultato = mysqli_query($conn, $sql);
?>
 
<h2 class="mb-3">Elenco Film</h2>
<a href="nuovo.php" class="btn btn-success mb-3">+ Nuovo film</a>
 
<table class="table table-striped table-hover bg-white shadow-sm align-middle">
  <thead class="table-dark">
    <tr>
      <th>Locandina</th>
      <th>Titolo</th>
      <th>Anno</th>
      <th>Regista</th>
      <th>Generi</th>
      <th>Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($film = mysqli_fetch_assoc($risultato)): ?>
      <?php
        // Per ogni film, recuperiamo separatamente i suoi generi
        $sql_generi = "SELECT g.nome
                       FROM generi g
                       JOIN film_generi fg ON g.id_genere = fg.id_genere
                       WHERE fg.id_film = ?";
        $stmt_generi = mysqli_prepare($conn, $sql_generi);
        mysqli_stmt_bind_param($stmt_generi, "i", $film['id_film']);
        mysqli_stmt_execute($stmt_generi);
        $risultato_generi = mysqli_stmt_get_result($stmt_generi);
 
        $nomi_generi = [];
        while ($g = mysqli_fetch_assoc($risultato_generi)) {
            $nomi_generi[] = $g['nome'];
        }
 
        $percorso_immagine = $film['locandina']
            ? "/archivio-film/assets/img/" . $film['locandina']
            : "/archivio-film/assets/img/placeholder.png";
      ?>
      <tr>
        <td><img src="<?= htmlspecialchars($percorso_immagine) ?>" alt="Locandina" style="width:50px;"></td>
        <td><?= htmlspecialchars($film['titolo']) ?></td>
        <td><?= htmlspecialchars($film['anno_uscita'] ?? '-') ?></td>
        <td><?= htmlspecialchars($film['nome_regista'] ? $film['nome_regista'] . ' ' . $film['cognome_regista'] : '-') ?></td>
        <td>
          <?php foreach ($nomi_generi as $nome_genere): ?>
            <span class="badge bg-secondary"><?= htmlspecialchars($nome_genere) ?></span>
          <?php endforeach; ?>
        </td>
        <td>
          <a href="modifica.php?id=<?= $film['id_film'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a>
          <a href="elimina.php?id=<?= $film['id_film'] ?>" class="btn btn-sm btn-outline-danger">Elimina</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
 
<?php require "../../includes/footer.php"; ?>