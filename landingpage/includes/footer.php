  </main>

  <footer class="footer reveal" role="contentinfo">
    <div class="footer-brand">
      <img src="img/logo-novo.png" alt="Logo UNMONOCHROME">
    </div>

    <nav class="footer-links" aria-label="Links do rodapé">
      <a href="index.php#inicio">Início</a>
      <a href="index.php#gameplay">Gameplay</a>
      <a href="index.php#daltonismo">Daltonismo</a>
      <a href="index.php#processo">Processo Criativo</a>
      <a href="index.php#sobre">Sobre Nós</a>
      <a href="index.php#comunidade">Comunidade</a>
    </nav>

    <p class="footer-title">UNMONOCHROME © 2025</p>
    <p class="footer-description">Projeto autoral sobre acessibilidade, percepção visual e expressão artística.</p>
  </footer>

  <link rel="stylesheet" href="../a11y.css">
  <script src="../a11y.js" defer></script>
  <script src="script.js?v=3" defer></script>
  
  <?php if (isset($extraJs)): ?>
    <?php foreach ($extraJs as $js): ?>
      <script src="<?= $js ?>" defer></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
