<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=300">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
  <title>Validar Código - UNMONOCHROME</title>
</head>

<body>

  <a href="#main-content" class="skip-link">Pular para o conteúdo</a>
  <main id="main-content" role="main">
  <div class="card-login-wrapper">
    <iframe class="character-sit" src="img/animacao/johnbalanco.html" scrolling="no" title="Personagem animado">
    </iframe>

    <div class="character-shadow"></div>

    <div class="card-login">
      <div class="card-shine"></div>

      <h1>NOVA SENHA</h1>

      <form action="../backend/validar_codigo_recuperacao.php" method="POST" style="width:100%;">
        <?php 
        require_once "../backend/csrf.php";
        ?>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        
        <div class="textfield">
          <label for="usuario">Usuário ou Email</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-user input-icon"></i>
            <input type="text" id="usuario" name="usuario" placeholder="Seu usuário ou email" required>
          </div>
        </div>

        <div class="textfield">
          <label for="codigo">Código do Email</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-key input-icon"></i>
            <input type="text" id="codigo" name="codigo" placeholder="Digite o código recebido" maxlength="6" required>
          </div>
        </div>

        <div class="password-grid">
          <div class="textfield">
            <label for="nova_senha">Nova Senha</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres" required>
              <button type="button" class="toggle-password" data-target="nova_senha" aria-label="Mostrar senha">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="textfield">
            <label for="confirmar_senha">Confirmar Senha</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a senha" required>
              <button type="button" class="toggle-password" data-target="confirmar_senha" aria-label="Mostrar senha">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-login">Resetar Senha</button>
      </form>

      <p class="auth-switch-text">
        Não recebeu o código?
        <a href="recuperar_senha.php" class="auth-link">Tentar novamente</a>
      </p>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'campos_vazios'): ?>
        <p class="auth-message error">Preencha todos os campos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'codigo_invalido'): ?>
        <p class="auth-message error">Código inválido ou expirado.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'senhas_nao_coincidem'): ?>
        <p class="auth-message error">As senhas não coincidem.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'senha_fraca'): ?>
        <p class="auth-message error">Senha fraca. Mínimo 6 caracteres.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'muitas_tentativas'): ?>
        <p class="auth-message error">Muitas tentativas. Solicite um novo código.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'db_error'): ?>
        <p class="auth-message error">Erro no banco de dados. Tente novamente.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'csrf'): ?>
        <p class="auth-message error">Sessão expirada. Tente novamente.</p>
      <?php endif; ?>
    </div>
    </main>
  </div>

  <script src="script.js?v=300"></script>
  <link rel="stylesheet" href="../a11y.css">
  <script src="../a11y.js"></script>
</body>

</html>
