<?php
require_once "../backend/verificar_login.php";
require_once "../backend/conexao.php";

$pageTitle = "Blog - UNMONOCHROME";
$extraCss = ["blog.css"];
$extraJs = ["blog-interacoes.js"];

require_once "includes/header.php";

// 1. Buscar posts + total de curtidas em uma única query
$sql = "SELECT posts.*, usuarios.usuario, usuarios.tipo, usuarios.foto_perfil,
               COUNT(curtidas.id) AS total_likes
        FROM posts
        INNER JOIN usuarios ON posts.usuario_id = usuarios.id
        LEFT JOIN curtidas ON curtidas.post_id = posts.id
        GROUP BY posts.id
        ORDER BY posts.data_postagem DESC";

$resultado = $conn->query($sql);

// 2. Buscar todos os posts que o usuário atual curtiu
$postsCurtidos = [];
$postIds = [];
$posts = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $postIds[] = $row['id'];
        $posts[$row['id']] = $row;
    }

    if (!empty($postIds)) {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $stmtCurtidas = $conn->prepare("SELECT post_id FROM curtidas WHERE usuario_id = ? AND post_id IN ($placeholders)");
        $types = 'i' . str_repeat('i', count($postIds));
        $params = array_merge([$_SESSION["usuario_id"]], $postIds);
        $stmtCurtidas->bind_param($types, ...$params);
        $stmtCurtidas->execute();
        $resCurtidas = $stmtCurtidas->get_result();

        while ($curtida = $resCurtidas->fetch_assoc()) {
            $postsCurtidos[$curtida['post_id']] = true;
        }
    }
}

// 3. Buscar todos os comentários de uma vez
$comentariosPorPost = [];
if (!empty($postIds)) {
    $placeholders = implode(',', array_fill(0, count($postIds), '?'));
    $sqlComentarios = "SELECT comentarios.*, usuarios.usuario, usuarios.tipo, usuarios.foto_perfil
                       FROM comentarios
                       INNER JOIN usuarios ON comentarios.usuario_id = usuarios.id
                       WHERE comentarios.post_id IN ($placeholders)
                       ORDER BY comentarios.data_comentario ASC";

    $stmtComentarios = $conn->prepare($sqlComentarios);
    $types = str_repeat('i', count($postIds));
    $stmtComentarios->bind_param($types, ...$postIds);
    $stmtComentarios->execute();
    $resComentarios = $stmtComentarios->get_result();

    while ($comentario = $resComentarios->fetch_assoc()) {
        $comentariosPorPost[$comentario['post_id']][] = $comentario;
    }
}
?>

  <div class="blog-container">
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

    <form class="blog-form" action="../backend/criar_post.php" method="POST" enctype="multipart/form-data" id="criarPostForm">
      <?php require_once "../backend/csrf.php"; ?>
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
      
      <h2>Criar nova postagem</h2>

      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" maxlength="200" required>

      <label for="conteudo">Conteúdo</label>
      <textarea id="conteudo" name="conteudo" maxlength="5000" required></textarea>

      <label>Imagem (opcional - máx. 5MB)</label>
      <div class="custom-file-wrapper">
        <input type="file" id="imagem" name="imagem" class="custom-file-input" accept="image/*">
        <label for="imagem" class="custom-file-label">Escolher imagem</label>
        <span class="file-name" id="file-name-post">Nenhum arquivo escolhido</span>
      </div>

      <button type="submit" class="btn primary" id="submitPostBtn">Publicar</button>
    </form>

    <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?>
        <?php
        $totalLikes = $post['total_likes'] ?? 0;
        $jaCurtiu = isset($postsCurtidos[$post['id']]);
        $podeExcluir = ($_SESSION["usuario_tipo"] === "admin" || $_SESSION["usuario_id"] == $post["usuario_id"]);
        $podeEditar = $podeExcluir;
        ?>

        <article class="post-card">
          <h2><?php echo htmlspecialchars($post["titulo"]); ?></h2>

          <div class="post-author-box">
            <?php if (!empty($post["foto_perfil"])): ?>
              <a href="perfil.php?id=<?php echo $post['usuario_id']; ?>">
                <img src="profile_pics/<?php echo htmlspecialchars($post["foto_perfil"]); ?>"
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
              <img src="uploads/<?php echo htmlspecialchars($post["imagem"]); ?>"
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
               style="text-decoration:none; font-weight:700; display:flex; align-items:center; gap:6px;
                      color: <?php echo $jaCurtiu ? '#ff4f86' : 'rgba(255,255,255,0.6)'; ?>;">
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
            $comentarios = $comentariosPorPost[$post['id']] ?? [];
            if (!empty($comentarios)): 
            ?>
              <?php foreach ($comentarios as $comentario): ?>
                <div class="comment-item">
                  <div style="display:flex; gap:10px; align-items:flex-start;">
                    <?php if (!empty($comentario["foto_perfil"])): ?>
                      <img src="profile_pics/<?php echo htmlspecialchars($comentario["foto_perfil"]); ?>"
                           alt="Foto de perfil de <?php echo htmlspecialchars($comentario["usuario"]); ?>"
                           style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <?php else: ?>
                      <div style="width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;
                                  background:linear-gradient(135deg,#ff2d73,#ff4f86); color:#fff; font-weight:900;">
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
              <?php endforeach; ?>
            <?php else: ?>
              <p class="comment-empty">Nenhum comentário ainda.</p>
            <?php endif; ?>

            <form class="comment-form" action="../backend/criar_comentario.php" method="POST">
              <?php require_once "../backend/csrf.php"; ?>
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
              <input type="hidden" name="post_id" value="<?php echo $post["id"]; ?>">
              <textarea name="conteudo" placeholder="Escreva um comentário..." required></textarea>
              <button type="submit" class="btn secondary">Comentar</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="post-card">
        Ainda não existem postagens no blog. Seja o primeiro a publicar!
      </div>
    <?php endif; ?>
  </div>

<?php
require_once "includes/footer.php";
?>