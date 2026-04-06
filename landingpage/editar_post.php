<?php
require_once "../backend/verificar_login.php";
require_once "../backend/conexao.php";

if (!isset($_GET["id"])) {
    header("Location: blog.php");
    exit;
}

$id = intval($_GET["id"]);
$usuario_id = $_SESSION["usuario_id"];
$usuario_tipo = $_SESSION["usuario_tipo"];

$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: blog.php?erro=post_nao_encontrado");
    exit;
}

$post = $resultado->fetch_assoc();

if ($usuario_tipo !== "admin" && $post["usuario_id"] != $usuario_id) {
    header("Location: blog.php?erro=sem_permissao");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Post - UNMONOCHROME</title>
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="site.css">
  <style>
    .edit-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }

    .edit-card {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 22px;
      padding: 28px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.18);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .edit-card h1 {
      color: #ff4f86;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .edit-card label {
      display: block;
      margin-bottom: 8px;
      color: #fff;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .edit-card input,
    .edit-card textarea {
      width: 100%;
      padding: 14px;
      margin-bottom: 18px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.08);
      background: rgba(255,255,255,0.06);
      color: #fff;
      font-size: 1rem;
      outline: none;
    }

    .edit-card textarea {
      min-height: 180px;
      resize: vertical;
    }

    .current-image {
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      border-radius: 16px;
      margin-bottom: 16px;
      border: 1px solid rgba(255,255,255,0.08);
    }

    .custom-file-wrapper {
      margin-bottom: 18px;
    }

    .custom-file-input {
      display: none;
    }

    .custom-file-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 18px;
      border-radius: 12px;
      background: linear-gradient(90deg, #ff2d73, #ff4f86);
      color: #fff;
      font-weight: 700;
      cursor: pointer;
      transition: 0.3s ease;
      box-shadow: 0 10px 25px rgba(255, 45, 115, 0.18);
    }

    .custom-file-label:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 35px rgba(255, 45, 115, 0.28);
    }

    .file-name {
      display: block;
      margin-top: 10px;
      color: rgba(255,255,255,0.7);
      font-size: 1rem;
    }
  </style>
</head>
<body>
  <header class="navbar">
    <a href="blog.php" class="logo">
      <img src="img/logo-novo.png" alt="UNMONOCHROME">
    </a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">☰</button>

    <nav class="nav-links" id="navLinks">
      <div class="nav-actions-mobile">
        <a href="blog.php" class="btn-nav secondary">Voltar</a>
      </div>
    </nav>

    <div class="nav-actions nav-actions-desktop">
      <a href="blog.php" class="btn-nav secondary">Voltar</a>
    </div>
  </header>

  <main class="edit-container">
    <div class="edit-card">
      <h1>Editar postagem</h1>

      <form action="../backend/salvar_edicao_post.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $post["id"]; ?>">

        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($post["titulo"]); ?>" required>

        <label for="conteudo">Conteúdo</label>
        <textarea id="conteudo" name="conteudo" required><?php echo htmlspecialchars($post["conteudo"]); ?></textarea>

        <?php if (!empty($post["imagem"])): ?>
          <label>Imagem atual</label>
          <img src="uploads/<?php echo htmlspecialchars($post["imagem"]); ?>" class="current-image" alt="Imagem atual">
        <?php endif; ?>

        <label>Nova imagem (opcional)</label>
        <div class="custom-file-wrapper">
          <input type="file" id="imagem" name="imagem" class="custom-file-input" accept="image/*">
          <label for="imagem" class="custom-file-label">Escolher imagem</label>
          <span class="file-name" id="file-name-edit">Nenhum arquivo escolhido</span>
        </div>

        <button type="submit" class="btn primary">Salvar alterações</button>
      </form>
    </div>
  </main>

  <script>
    const editInput = document.getElementById("imagem");
    const fileNameEdit = document.getElementById("file-name-edit");

    if (editInput) {
      editInput.addEventListener("change", function () {
        fileNameEdit.textContent = this.files.length > 0 ? this.files[0].name : "Nenhum arquivo escolhido";
      });
    }
  </script>
</body>
</html>
