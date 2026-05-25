<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'UNMONOCHROME' ?></title>

  <!-- Preconnect para performance -->
  <link rel="preconnect" href="https://use.typekit.net" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="site.css">
  
  <?php if (isset($extraCss)): ?>
    <?php foreach ($extraCss as $css): ?>
      <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
  <link rel="icon" type="image/png" sizes="16x16" href="img/favicon.png">
  <link rel="apple-touch-icon" href="img/favicon.png">
</head>
<body>
  <a href="#main-content" class="skip-link">Pular para o conteúdo</a>

  <header class="navbar">
    <a href="index.php#inicio" class="logo" aria-label="Ir para a página inicial">
      <img src="img/logo-novo.png" alt="UNMONOCHROME">
    </a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu de navegação" aria-expanded="false">☰</button>

    <nav class="nav-links" id="navLinks" aria-label="Navegação principal">
      <a href="index.php#inicio" <?= $currentPage === 'index.php' ? 'aria-current="page"' : '' ?>>Início</a>
      <a href="index.php#gameplay">Gameplay</a>
      <a href="index.php#daltonismo">Daltonismo</a>
      <a href="index.php#processo">Processo Criativo</a>
      <a href="index.php#sobre">Sobre Nós</a>
      <a href="index.php#comunidade">Comunidade</a>

      <div class="nav-actions-mobile">
        <?php if (isset($_SESSION["usuario_id"])): ?>
          <span class="user-status mobile-only">
            Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>
            (<?php echo htmlspecialchars($_SESSION["usuario_tipo"]); ?>)
          </span>
          <a href="perfil.php" class="btn-nav secondary">Perfil</a>
          <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
          <a href="blog.php" class="btn-nav primary">Blog</a>
        <?php else: ?>
          <a href="../login-page/index.php" class="btn-nav secondary">Login</a>
          <a href="blog.php" class="btn-nav primary">Blog</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="nav-actions nav-actions-desktop">
      <?php if (isset($_SESSION["usuario_id"])): ?>
        <span class="user-status">
          Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>
          (<?php echo htmlspecialchars($_SESSION["usuario_tipo"]); ?>)
        </span>
        <a href="perfil.php" class="btn-nav secondary">Perfil</a>
        <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
        <a href="blog.php" class="btn-nav primary">Blog</a>
      <?php else: ?>
        <a href="../login-page/index.php" class="btn-nav secondary">Login</a>
        <a href="blog.php" class="btn-nav primary">Blog</a>
      <?php endif; ?>
    </div>
  </header>

  <main id="main-content" role="main">
