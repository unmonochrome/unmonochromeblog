<?php
require_once "../backend/verificar_login.php";
require_once "../backend/conexao.php";
require_once "../backend/csrf.php";

// só admin
$current_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare('SELECT tipo FROM usuarios WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $current_id);
$stmt->execute();
$r = $stmt->get_result();
if ($r->num_rows === 0) {
    header('Location: blog.php'); exit;
}
$me = $r->fetch_assoc();
if ($me['tipo'] !== 'admin') {
    header('Location: blog.php'); exit;
}

$sql = 'SELECT id, usuario, email, tipo FROM usuarios ORDER BY usuario ASC';
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gerenciar Usuários - UNMONOCHROME</title>
  <link rel="stylesheet" href="../landingpage/site.css">
  <link rel="stylesheet" href="../landingpage/admin_users.css">
  <link rel="stylesheet" href="../a11y.css">
  <script src="../a11y.js"></script>
  <head>
    <meta charset="UTF-8">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Darker+Grotesque:wght@400;700;900&display=swap" rel="stylesheet">

</head>
</head>
<body>
  <a href="#main-content" class="skip-link">Pular para o conteúdo</a>
  <main id="main-content" role="main" class="admin-users-page">
    <div class="admin-users-wrapper">
      <h1>Gerenciar Usuários</h1>



    <?php if (isset($_GET['sucesso'])): ?>
      <p class="auth-message success">Usuário promovido a administrador.</p>
    <?php endif; ?>
    <?php if (isset($_GET['sucesso_delete'])): ?>
      <p class="auth-message success">Conta excluída com sucesso.</p>
    <?php endif; ?>
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'target_not_found'): ?>
      <p class="auth-message error">Usuário alvo não encontrado.</p>
    <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'forbidden'): ?>
      <p class="auth-message error">Apenas administradores podem executar essa ação.</p>
    <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'invalid'): ?>
      <p class="auth-message error">Ação inválida.</p>
    <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'self_delete'): ?>
      <p class="auth-message error">Você não pode excluir sua própria conta por aqui.</p>
    <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'db_error'): ?>
      <p class="auth-message error">Erro no banco de dados. Tente novamente.</p>
    <?php endif; ?>

    <?php if ($res && $res->num_rows > 0): ?>
      <table>
        <thead><tr><th>Usuário</th><th>Email</th><th>Tipo</th><th>Ações</th></tr></thead>
        <tbody>
          <?php while ($u = $res->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($u['usuario']); ?></td>
              <td><?php echo htmlspecialchars($u['email']); ?></td>
              <td><?php echo htmlspecialchars($u['tipo']); ?></td>
              <td>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                  <?php if ($u['tipo'] !== 'admin'): ?>
                    <form method="POST" action="../backend/make_admin.php" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <button type="submit">Tornar admin</button>
                    </form>
                  <?php endif; ?>

                  <?php if ($u['id'] !== $current_id): ?>
                    <form method="POST" action="../backend/delete_usuario.php" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');">
                      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <button type="submit">Excluir conta</button>
                    </form>
                  <?php else: ?>
                    <span style="color:#bbb;">Sua conta</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Nenhum usuário encontrado.</p>
    <?php endif; ?>
  </div>
  </main>
</body>
</html>

