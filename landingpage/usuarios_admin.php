<?php
require_once "../backend/verificar_login.php";
require_once "../backend/conexao.php";
require_once "../backend/csrf.php";

// Verifica se é admin
$current_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare('SELECT tipo FROM usuarios WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $current_id);
$stmt->execute();
$r = $stmt->get_result();
if ($r->num_rows === 0 || $r->fetch_assoc()['tipo'] !== 'admin') {
    header('Location: blog.php');
    exit;
}

$sql = 'SELECT id, usuario, email, tipo FROM usuarios ORDER BY usuario ASC';
$res = $conn->query($sql);

$pageTitle = "Gerenciar Usuários - UNMONOCHROME";
$extraCss = ["usuarios_admin.css"];
require_once "includes/header.php";
?>

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
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['tipo']); ?></td>
                        <td>
                            <div class="admin-actions">
                                <?php if ($u['tipo'] !== 'admin'): ?>
                                    <form method="POST" action="../backend/make_admin.php">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn primary">Tornar admin</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($u['id'] !== $current_id): ?>
                                    <form method="POST" action="../backend/delete_usuario.php" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn secondary">Excluir conta</button>
                                    </form>
                                <?php else: ?>
                                    <span class="self-account">Sua conta</span>
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

<?php require_once "includes/footer.php"; ?>
