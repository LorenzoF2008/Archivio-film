<?php
$percorso_immagine = $film['locandina']
    ? "/archivio-film/assets/img/" . $film['locandina']
    : "/archivio-film/assets/img/placeholder.png";
?>
<div class="card h-100 shadow-sm">
  <img src="<?= htmlspecialchars($percorso_immagine) ?>" class="card-img-top" alt="Locandina" style="height:280px; object-fit:cover;">
  <div class="card-body">
    <h5 class="card-title"><?= htmlspecialchars($film['titolo']) ?></h5>
    <p class="card-text text-muted mb-1"><?= htmlspecialchars($film['anno_uscita'] ?? '-') ?></p>
    <p class="card-text small">
      <?= $film['nome_regista'] ? htmlspecialchars($film['nome_regista'] . ' ' . $film['cognome_regista']) : 'Regista non specificato' ?>
    </p>
    <a href="dettaglio.php?id=<?= $film['id_film'] ?>" class="btn btn-sm btn-primary">Vedi dettagli</a>
  </div>
</div>