<?php
require_once "../backend/verificar_login.php";
require_once "../backend/conexao.php";

$usuario_logado_id = $_SESSION["usuario_id"];
$usuario_id = isset($_GET["id"]) ? intval($_GET["id"]) : $usuario_logado_id;

$sqlUsuario = "SELECT id, usuario, tipo, data_cadastro, foto_perfil FROM usuarios WHERE id = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $usuario_id);
$stmtUsuario->execute();
$resultadoUsuario = $stmtUsuario->get_result();

if ($resultadoUsuario->num_rows === 0) {
    header("Location: blog.php");
    exit;
}

$usuario = $resultadoUsuario->fetch_assoc();

$sqlPosts = "SELECT COUNT(*) AS total FROM posts WHERE usuario_id = ?";
$stmtPosts = $conn->prepare($sqlPosts);
$stmtPosts->bind_param("i", $usuario_id);
$stmtPosts->execute();
$resPosts = $stmtPosts->get_result();
$totalPosts = $resPosts->fetch_assoc()["total"];

$sqlComentarios = "SELECT COUNT(*) AS total FROM comentarios WHERE usuario_id = ?";
$stmtComentarios = $conn->prepare($sqlComentarios);
$stmtComentarios->bind_param("i", $usuario_id);
$stmtComentarios->execute();
$resComentarios = $stmtComentarios->get_result();
$totalComentarios = $resComentarios->fetch_assoc()["total"];

$sqlSeguidores = "SELECT COUNT(*) AS total FROM seguidores WHERE seguido_id = ?";
$stmtSeguidores = $conn->prepare($sqlSeguidores);
$stmtSeguidores->bind_param("i", $usuario_id);
$stmtSeguidores->execute();
$resSeguidores = $stmtSeguidores->get_result();
$totalSeguidores = $resSeguidores->fetch_assoc()["total"];

$sqlSeguindo = "SELECT COUNT(*) AS total FROM seguidores WHERE seguidor_id = ?";
$stmtSeguindo = $conn->prepare($sqlSeguindo);
$stmtSeguindo->bind_param("i", $usuario_id);
$stmtSeguindo->execute();
$resSeguindo = $stmtSeguindo->get_result();
$totalSeguindo = $resSeguindo->fetch_assoc()["total"];

$jaSegue = false;

if ($usuario_logado_id != $usuario_id) {
    $sqlJaSegue = "SELECT id FROM seguidores WHERE seguidor_id = ? AND seguido_id = ?";
    $stmtJaSegue = $conn->prepare($sqlJaSegue);
    $stmtJaSegue->bind_param("ii", $usuario_logado_id, $usuario_id);
    $stmtJaSegue->execute();
    $resJaSegue = $stmtJaSegue->get_result();
    $jaSegue = $resJaSegue->num_rows > 0;
}

$sqlPostsUsuario = "SELECT * FROM posts WHERE usuario_id = ? ORDER BY data_postagem DESC";
$stmtPostsUsuario = $conn->prepare($sqlPostsUsuario);
$stmtPostsUsuario->bind_param("i", $usuario_id);
$stmtPostsUsuario->execute();
$postsUsuario = $stmtPostsUsuario->get_result();

$ehMeuPerfil = ($usuario_logado_id == $usuario_id);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil - UNMONOCHROME</title>
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="site.css">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicon.png">
<link rel="apple-touch-icon" href="img/favicon.png">

  <style>
    .perfil-container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 0 20px 40px;
    }

    .perfil-header {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 28px;
      padding: 34px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.18);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      margin-bottom: 28px;
    }

    .perfil-top {
      display: grid;
      grid-template-columns: 180px 1fr;
      gap: 34px;
      align-items: start;
    }

    .perfil-avatar-col {
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .perfil-avatar-img,
    .perfil-avatar-fallback {
      width: 160px;
      height: 160px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid rgba(255,255,255,0.12);
      box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    }

    .perfil-avatar-fallback {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.2rem;
      font-weight: 900;
      color: #fff;
      background: linear-gradient(135deg, #ff2d73, #ff4f86);
    }

    .perfil-main-info {
      min-width: 0;
    }

    .perfil-row-1 {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .perfil-nome {
      margin: 0;
      font-size: 2.2rem;
      line-height: 1;
      color: #fff;
      font-weight: 800;
    }

    .perfil-user-badge {
      display: inline-flex;
      align-items: center;
      padding: 7px 14px;
      border-radius: 999px;
      font-size: 0.92rem;
      font-weight: 700;
      color: #ff78a6;
      background: rgba(255,79,134,0.12);
      border: 1px solid rgba(255,79,134,0.22);
    }

    .perfil-actions-top {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-left: auto;
    }

    .perfil-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 11px 18px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      transition: 0.25s ease;
      border: 1px solid rgba(255,255,255,0.08);
    }

    .perfil-btn.primary {
      color: #fff;
      background: linear-gradient(90deg, #ff2d73, #ff4f86);
      box-shadow: 0 10px 25px rgba(255, 45, 115, 0.22);
    }

    .perfil-btn.secondary {
      color: #fff;
      background: rgba(255,255,255,0.06);
    }

    .perfil-btn:hover {
      transform: translateY(-2px);
    }

    .perfil-stats-inline {
      display: flex;
      flex-wrap: wrap;
      gap: 28px;
      margin-bottom: 18px;
    }

    .perfil-stat {
      color: rgba(255,255,255,0.86);
      font-size: 1.03rem;
    }

    .perfil-stat strong {
      color: #fff;
      font-size: 1.1rem;
      margin-right: 6px;
    }

    .perfil-bio {
      color: rgba(255,255,255,0.78);
      font-size: 1.03rem;
      line-height: 1.65;
      max-width: 760px;
    }

    .perfil-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 16px;
    }

    .perfil-meta-item {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      border-radius: 999px;
      font-size: 0.95rem;
      color: rgba(255,255,255,0.78);
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.08);
    }

    .perfil-upload-box {
      margin-top: 22px;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.08);
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .custom-file-wrapper {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .custom-file-input {
      display: none;
    }

    .custom-file-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 11px 16px;
      border-radius: 12px;
      background: rgba(255,255,255,0.06);
      color: #fff;
      font-weight: 700;
      cursor: pointer;
      transition: 0.25s ease;
      border: 1px solid rgba(255,255,255,0.08);
    }

    .custom-file-label:hover {
      transform: translateY(-2px);
      border-color: rgba(255,79,134,0.25);
    }

    .file-name {
      color: rgba(255,255,255,0.66);
      font-size: 0.96rem;
    }

    .feedback {
      padding: 14px 18px;
      border-radius: 14px;
      margin-bottom: 20px;
      font-size: 1.05rem;
      font-weight: 700;
    }

    .feedback.success {
      background: rgba(72, 187, 120, 0.12);
      color: #7dffb3;
      border: 1px solid rgba(72, 187, 120, 0.25);
    }

    .feedback.error {
      background: rgba(255, 79, 134, 0.12);
      color: #ff8fab;
      border: 1px solid rgba(255, 79, 134, 0.25);
    }

    .perfil-posts {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 22px;
      padding: 28px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.18);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .perfil-posts-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .perfil-posts h2 {
      font-size: 2rem;
      margin: 0;
      color: #fff;
    }

    .posts-count {
      color: rgba(255,255,255,0.62);
      font-size: 0.98rem;
    }

    .meu-post {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 18px;
      padding: 20px;
      margin-bottom: 18px;
      transition: 0.25s ease;
    }

    .meu-post:hover {
      transform: translateY(-4px);
      border-color: rgba(255,79,134,0.25);
    }

    .meu-post h3 {
      color: #ff4f86;
      font-size: 1.45rem;
      margin: 0;
    }

    .meu-post p {
      color: rgba(255,255,255,0.8);
      font-size: 1.03rem;
      line-height: 1.65;
    }

    .meu-post small {
      display: block;
      margin-bottom: 10px;
      color: rgba(255,255,255,0.55);
    }

    .meu-post img {
      width: 100%;
      max-height: 320px;
      object-fit: cover;
      border-radius: 14px;
      margin-bottom: 14px;
      border: 1px solid rgba(255,255,255,0.08);
      cursor: pointer;
      transition: 0.25s ease;
    }

    .meu-post img:hover {
      transform: scale(1.02);
    }

    .post-divider {
      height: 1px;
      background: rgba(255,255,255,0.08);
      margin: 12px 0 16px;
    }

    .post-actions {
      margin-top: 14px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    @media (max-width: 900px) {
      .perfil-top {
        grid-template-columns: 1fr;
        gap: 24px;
      }

      .perfil-avatar-col {
        justify-content: flex-start;
      }

      .perfil-row-1 {
        align-items: flex-start;
      }

      .perfil-actions-top {
        margin-left: 0;
        width: 100%;
      }
    }

    @media (max-width: 600px) {
      .perfil-header,
      .perfil-posts {
        padding: 20px;
      }

      .perfil-avatar-img,
      .perfil-avatar-fallback {
        width: 128px;
        height: 128px;
      }

      .perfil-nome {
        font-size: 1.7rem;
      }

      .perfil-stats-inline {
        gap: 14px;
        flex-direction: column;
      }

      .perfil-upload-box {
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

<header class="navbar">
  <a href="index.php" class="logo">
    <img src="img/logo-novo.png" alt="UNMONOCHROME">
  </a>

  <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">☰</button>

  <nav class="nav-links" id="navLinks">
    <div class="nav-actions-mobile">
      <a href="blog.php" class="btn-nav primary">Blog</a>
      <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
    </div>
  </nav>

  <div class="nav-actions nav-actions-desktop">
    <a href="blog.php" class="btn-nav primary">Blog</a>
    <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
  </div>
</header>

<main class="perfil-container">
  <?php if (isset($_GET["sucesso"]) && $_GET["sucesso"] === "foto_atualizada"): ?>
    <div class="feedback success">Foto de perfil atualizada com sucesso!</div>
  <?php endif; ?>

  <?php if (isset($_GET["sucesso"]) && $_GET["sucesso"] === "follow"): ?>
    <div class="feedback success">Agora você está seguindo este usuário.</div>
  <?php endif; ?>

  <?php if (isset($_GET["sucesso"]) && $_GET["sucesso"] === "unfollow"): ?>
    <div class="feedback success">Você deixou de seguir este usuário.</div>
  <?php endif; ?>

  <?php if (isset($_GET["erro"]) && $_GET["erro"] === "auto_follow"): ?>
    <div class="feedback error">Você não pode seguir a si mesmo.</div>
  <?php endif; ?>

  <?php if (isset($_GET["erro"]) && $_GET["erro"] === "follow_fail"): ?>
    <div class="feedback error">Não foi possível seguir/deixar de seguir agora.</div>
  <?php endif; ?>

  <?php if (isset($_GET["erro"]) && $_GET["erro"] === "usuario_nao_encontrado"): ?>
    <div class="feedback error">Usuário não encontrado.</div>
  <?php endif; ?>

  <section class="perfil-header">
    <div class="perfil-top">
      <div class="perfil-avatar-col">
        <?php if (!empty($usuario["foto_perfil"])): ?>
          <img src="profile_pics/<?php echo htmlspecialchars($usuario["foto_perfil"]); ?>" class="perfil-avatar-img" alt="Foto de perfil">
        <?php else: ?>
          <div class="perfil-avatar-fallback">
            <?php echo strtoupper(substr($usuario["usuario"], 0, 1)); ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="perfil-main-info">
        <div class="perfil-row-1">
          <h1 class="perfil-nome"><?php echo htmlspecialchars($usuario["usuario"]); ?></h1>
          <span class="perfil-user-badge"><?php echo $ehMeuPerfil ? 'Seu perfil' : 'Perfil público'; ?></span>

          <div class="perfil-actions-top">
            <?php if (!$ehMeuPerfil): ?>
              <a href="../backend/seguir_usuario.php?id=<?php echo $usuario["id"]; ?>" class="perfil-btn <?php echo $jaSegue ? 'secondary' : 'primary'; ?>">
                <?php echo $jaSegue ? 'Seguindo' : 'Seguir'; ?>
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="perfil-stats-inline">
          <div class="perfil-stat"><strong><?php echo $totalPosts; ?></strong> publicações</div>
          <div class="perfil-stat"><strong><?php echo $totalComentarios; ?></strong> comentários</div>
          <div class="perfil-stat"><strong><?php echo $totalSeguidores; ?></strong> seguidores</div>
          <div class="perfil-stat"><strong><?php echo $totalSeguindo; ?></strong> seguindo</div>
        </div>

        <div class="perfil-bio">
          <?php echo $ehMeuPerfil
            ? 'Aqui você acompanha sua participação na comunidade, atualiza sua foto e acessa rapidamente suas publicações.'
            : 'Veja as publicações, números e atividade deste usuário dentro da comunidade.'; ?>
        </div>

        <div class="perfil-meta">
          <div class="perfil-meta-item">
            <strong>Status:</strong> Conta ativa
          </div>
          <div class="perfil-meta-item">
            <strong>Membro desde:</strong> <?php echo date("d/m/Y", strtotime($usuario["data_cadastro"])); ?>
          </div>
        </div>

        <?php if ($ehMeuPerfil): ?>
          <form action="../backend/atualizar_foto_perfil.php" method="POST" enctype="multipart/form-data" class="perfil-upload-box">
            <div class="custom-file-wrapper">
              <input type="file" name="foto_perfil" id="foto_perfil" class="custom-file-input" accept="image/*" required>
              <label for="foto_perfil" class="custom-file-label">Escolher nova foto</label>
              <span class="file-name" id="file-name-foto">Nenhum arquivo escolhido</span>
            </div>

            <button type="submit" class="perfil-btn primary">Atualizar foto</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="perfil-posts">
    <div class="perfil-posts-header">
      <h2><?php echo $ehMeuPerfil ? 'Minhas postagens' : 'Postagens de ' . htmlspecialchars($usuario["usuario"]); ?></h2>
      <span class="posts-count"><?php echo $totalPosts; ?> post<?php echo $totalPosts == 1 ? '' : 's'; ?></span>
    </div>

    <?php if ($postsUsuario->num_rows > 0): ?>
      <?php while ($post = $postsUsuario->fetch_assoc()): ?>
        <article class="meu-post">
          <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:6px; flex-wrap:wrap;">
            <h3><?php echo htmlspecialchars($post["titulo"]); ?></h3>
          </div>

          <small>
            <?php echo date("d/m/Y \à\s H:i", strtotime($post["data_postagem"])); ?>
          </small>

          <div class="post-divider"></div>

          <?php if (!empty($post["imagem"])): ?>
           <img 
  class="post-image"
  src="uploads/<?php echo htmlspecialchars($post["imagem"]); ?>" 
  alt="Imagem do post"
>
          <?php endif; ?>

          <p><?php echo nl2br(htmlspecialchars($post["conteudo"])); ?></p>

          <?php if ($ehMeuPerfil || $_SESSION["usuario_tipo"] === "admin"): ?>
            <div class="post-actions">
              <a href="editar_post.php?id=<?php echo $post["id"]; ?>" class="btn-nav secondary">Editar</a>
              <a href="../backend/excluir_post.php?id=<?php echo $post["id"]; ?>"
                 class="btn-nav primary"
                 onclick="return confirm('Deseja excluir este post?');">
                Excluir
              </a>
            </div>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="meu-post">
        <p>Este usuário ainda não publicou nenhum post.</p>
      </div>
    <?php endif; ?>
  </section>
</main>


<script>
  const inputFoto = document.getElementById("foto_perfil");
  const fileNameFoto = document.getElementById("file-name-foto");

  if (inputFoto && fileNameFoto) {
    inputFoto.addEventListener("change", function () {
      fileNameFoto.textContent = this.files && this.files[0]
        ? this.files[0].name
        : "Nenhum arquivo escolhido";
    });
  }
</script>

<script src="script.js?v=3"></script>
</body>
</html>
