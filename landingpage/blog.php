<?php
require_once "../backend/verificar_login.php";
require_once "../backend/conexao.php";

$sql = "SELECT posts.*, usuarios.usuario, usuarios.tipo, usuarios.foto_perfil
        FROM posts
        INNER JOIN usuarios ON posts.usuario_id = usuarios.id
        ORDER BY data_postagem DESC";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog - UNMONOCHROME</title>
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="site.css">
  <link rel="stylesheet" href="blog.css">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicon.png">
<link rel="apple-touch-icon" href="img/favicon.png">
</head>

<body>

  <header class="navbar">
    <a href="index.php#inicio" class="logo">
      <img src="img/logo-novo.png" alt="UNMONOCHROME">
    </a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">☰</button>

    <nav class="nav-links" id="navLinks">
      <a href="index.php#inicio">Início</a>
      <a href="index.php#gameplay">Gameplay</a>
      <a href="index.php#daltonismo">Daltonismo</a>
      <a href="index.php#processo">Processo Criativo</a>
      <a href="index.php#sobre">Sobre Nós</a>
      <a href="index.php#comunidade">Comunidade</a>

      <div class="nav-actions-mobile">
        <a href="perfil.php" class="btn-nav secondary">Perfil</a>
        <a href="blog.php" class="btn-nav primary">Blog</a>
        <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
      </div>
    </nav>

    <div class="nav-actions nav-actions-desktop">
      <a href="perfil.php" class="btn-nav secondary">Perfil</a>
      <a href="blog.php" class="btn-nav primary">Blog</a>
      <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
    </div>
  </header>

  <main class="blog-container">
    <h1 class="blog-title">Blog da Comunidade</h1>
    <p class="blog-subtitle">
      Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>
      (<?php echo htmlspecialchars($_SESSION["usuario_tipo"]); ?>)
    </p>

    <?php if (isset($_GET["sucesso"])): ?>
      <div class="feedback success">
        <?php
        if ($_GET["sucesso"] === "post_criado") echo "Post criado com sucesso!";
        if ($_GET["sucesso"] === "post_excluido") echo "Post excluído com sucesso!";
        if ($_GET["sucesso"] === "comentario_criado") echo "Comentário adicionado com sucesso!";
        if ($_GET["sucesso"] === "post_editado") echo "Post editado com sucesso!";
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET["erro"])): ?>
      <div class="feedback error">
        <?php
        if ($_GET["erro"] === "post_vazio") echo "Preencha título e conteúdo.";
        if ($_GET["erro"] === "sem_permissao") echo "Você não tem permissão para excluir este conteúdo.";
        if ($_GET["erro"] === "comentario_vazio") echo "O comentário não pode estar vazio.";
        if ($_GET["erro"] === "post_nao_encontrado") echo "Post não encontrado.";
        ?>
      </div>
    <?php endif; ?>

    <form class="blog-form" action="../backend/criar_post.php" method="POST" enctype="multipart/form-data">
      <h2>Criar nova postagem</h2>

      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" required>

      <label for="conteudo">Conteúdo</label>
      <textarea id="conteudo" name="conteudo" required></textarea>

      <label>Imagem (opcional)</label>
      <div class="custom-file-wrapper">
        <input type="file" id="imagem" name="imagem" class="custom-file-input" accept="image/*">
        <label for="imagem" class="custom-file-label">Escolher imagem</label>
        <span class="file-name" id="file-name-post">Nenhum arquivo escolhido</span>
      </div>

      <button type="submit" class="btn primary">Publicar</button>
    </form>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
      <?php while ($post = $resultado->fetch_assoc()): ?>
        <?php
        $sqlLikes = "SELECT COUNT(*) AS total FROM curtidas WHERE post_id = ?";
        $stmtLikes = $conn->prepare($sqlLikes);
        $stmtLikes->bind_param("i", $post["id"]);
        $stmtLikes->execute();
        $resLikes = $stmtLikes->get_result();
        $totalLikes = $resLikes->fetch_assoc()["total"];

        $jaCurtiu = false;
        $sqlJaCurtiu = "SELECT id FROM curtidas WHERE usuario_id = ? AND post_id = ?";
        $stmtJaCurtiu = $conn->prepare($sqlJaCurtiu);
        $stmtJaCurtiu->bind_param("ii", $_SESSION["usuario_id"], $post["id"]);
        $stmtJaCurtiu->execute();
        $resJaCurtiu = $stmtJaCurtiu->get_result();

        if ($resJaCurtiu->num_rows > 0) {
          $jaCurtiu = true;
        }

        $podeExcluir = false;
        $podeEditar = false;

        if ($_SESSION["usuario_tipo"] === "admin") {
          $podeExcluir = true;
          $podeEditar = true;
        } elseif ($_SESSION["usuario_id"] == $post["usuario_id"]) {
          $podeExcluir = true;
          $podeEditar = true;
        }
        ?>

        <article class="post-card">
          <h2><?php echo htmlspecialchars($post["titulo"]); ?></h2>

          <div class="post-author-box">
            <?php if (!empty($post["foto_perfil"])): ?>
              <a href="perfil.php?id=<?php echo $post['usuario_id']; ?>">
                <img
                  src="profile_pics/<?php echo htmlspecialchars($post["foto_perfil"]); ?>"
                  class="post-author-avatar"
                  alt="Foto de perfil de <?php echo htmlspecialchars($post["usuario"]); ?>">
              </a>
            <?php else: ?>
              <a href="perfil.php?id=<?php echo $post['usuario_id']; ?>" class="post-author-fallback">
                <?php echo strtoupper(substr($post["usuario"], 0, 1)); ?>
              </a>
            <?php endif; ?>

            <div class="post-author-info">
              <div class="post-author-name-row">
                <span class="post-author-name"><?php echo htmlspecialchars($post["usuario"]); ?></span>
                <?php if ($post["tipo"] === "admin"): ?>
                  <span class="admin-badge">admin</span>
                <?php endif; ?>
              </div>

              <div class="post-author-date">
                Publicado em <?php echo date("d/m/Y \à\s H:i", strtotime($post["data_postagem"])); ?>
              </div>
            </div>
          </div>

          <div class="post-body">
            <?php if (!empty($post["imagem"])): ?>
              <img
                src="uploads/<?php echo htmlspecialchars($post["imagem"]); ?>"
                class="post-image"
                alt="Imagem da postagem: <?php echo htmlspecialchars($post["titulo"]); ?>">
            <?php endif; ?>

            <div class="post-content">
              <?php echo nl2br(htmlspecialchars($post["conteudo"])); ?>
            </div>
          </div>

          <div class="post-divider"></div>

          <div class="post-like-row" style="margin-bottom:15px; display:flex; align-items:center; gap:12px;">
            <a href="../backend/curtir_post.php?post_id=<?php echo $post['id']; ?>"
               style="
                 text-decoration:none;
                 font-weight:700;
                 display:flex;
                 align-items:center;
                 gap:6px;
                 color: <?php echo $jaCurtiu ? '#ff4f86' : 'rgba(255,255,255,0.6)'; ?>;
               ">
              <?php echo $jaCurtiu ? '❤️ Curtido' : '🤍 Curtir'; ?>
            </a>

            <span style="color:rgba(255,255,255,0.6); font-size:0.95rem;">
              <?php echo $totalLikes; ?> curtidas
            </span>
          </div>

          <?php if ($podeExcluir || $podeEditar): ?>
            <div class="admin-actions">
              <?php if ($podeEditar): ?>
                <a href="editar_post.php?id=<?php echo $post['id']; ?>">Editar postagem</a>
              <?php endif; ?>

              <?php if ($podeExcluir): ?>
                <a href="../backend/excluir_post.php?id=<?php echo $post['id']; ?>"
                   onclick="return confirm('Tem certeza que deseja excluir esta postagem?');">
                  Excluir postagem
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="comment-box">
            <h3>Comentários</h3>

            <?php
            $sqlComentarios = "SELECT comentarios.*, usuarios.usuario, usuarios.tipo, usuarios.foto_perfil
                               FROM comentarios
                               INNER JOIN usuarios ON comentarios.usuario_id = usuarios.id
                               WHERE comentarios.post_id = ?
                               ORDER BY comentarios.data_comentario ASC";
            $stmtComentarios = $conn->prepare($sqlComentarios);
            $stmtComentarios->bind_param("i", $post["id"]);
            $stmtComentarios->execute();
            $comentarios = $stmtComentarios->get_result();
            ?>

            <?php if ($comentarios->num_rows > 0): ?>
              <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                <div class="comment-item">
                  <div style="display:flex; gap:10px; align-items:flex-start;">

                    <?php if (!empty($comentario["foto_perfil"])): ?>
                      <img
                        src="profile_pics/<?php echo htmlspecialchars($comentario["foto_perfil"]); ?>"
                        alt="Foto de perfil de <?php echo htmlspecialchars($comentario["usuario"]); ?>"
                        style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <?php else: ?>
                      <div style="
                        width:40px; height:40px;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:linear-gradient(135deg,#ff2d73,#ff4f86);
                        color:#fff;
                        font-weight:900;">
                        <?php echo strtoupper(substr($comentario["usuario"], 0, 1)); ?>
                      </div>
                    <?php endif; ?>

                    <div>
                      <div class="comment-meta">
                        <a href="perfil.php?id=<?php echo $comentario["usuario_id"]; ?>" class="comment-author-link">
                          <?php echo htmlspecialchars($comentario["usuario"]); ?>
                        </a>
                        • <?php echo date("d/m/Y H:i", strtotime($comentario["data_comentario"])); ?>
                      </div>

                      <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comentario["conteudo"])); ?>
                      </div>

                      <?php if ($_SESSION["usuario_tipo"] === "admin" || $_SESSION["usuario_id"] == $comentario["usuario_id"]): ?>
                        <div class="admin-actions" style="margin-top:8px;">
                          <a href="../backend/excluir_comentario.php?id=<?php echo $comentario['id']; ?>"
                             onclick="return confirm('Deseja excluir este comentário?');">
                            Excluir comentário
                          </a>
                        </div>
                      <?php endif; ?>
                    </div>

                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="comment-empty">Nenhum comentário ainda.</p>
            <?php endif; ?>

            <form class="comment-form" action="../backend/criar_comentario.php" method="POST">
              <input type="hidden" name="post_id" value="<?php echo $post["id"]; ?>">
              <textarea name="conteudo" placeholder="Escreva um comentário..." required></textarea>
              <button type="submit" class="btn secondary">Comentar</button>
            </form>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="post-card">
        Ainda não existem postagens no blog. Seja o primeiro a publicar!
      </div>
    <?php endif; ?>
  </main>

  <script src="script.js?v=3"></script>
</body>

</html>