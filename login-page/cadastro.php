<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=401">
  <title>Cadastro - UNMONOCHROME</title>
</head>
<body>

  <a href="#main-content" class="skip-link">Pular para o conteúdo</a>
  <main id="main-content" role="main">
  <div class="card-login-wrapper">
    <iframe
      class="character-sit"
      src="img/animacao/johnbalanco.html"
      scrolling="no"
      title="Personagem animado">
    </iframe>

    <div class="character-shadow"></div>

    <div class="card-login">
      <div class="card-shine"></div>

      <h1>CADASTRO</h1>

      <form action="../backend/cadastrar_usuario.php" method="POST" style="width:100%;" id="registerForm">
        <?php 
        require_once "../backend/csrf.php";
        ?>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        
        <div class="textfield">
          <label for="usuario">Usuário</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-user input-icon"></i>
            <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" required>
          </div>
        </div>

        <div class="textfield">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-envelope input-icon"></i>
            <input type="email" id="email" name="email" placeholder="Digite seu email" required>
          </div>
        </div>

        <div class="password-grid">
          <div class="textfield">
            <label for="senha">Senha</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
              <button type="button" class="toggle-password" data-target="senha" aria-label="Mostrar senha">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="textfield">
            <label for="confirmar_senha">Confirmar senha</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>
              <button type="button" class="toggle-password" data-target="confirmar_senha" aria-label="Mostrar senha">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
        </div>

        <p class="password-feedback" id="passwordFeedback"></p>

        <button type="submit" class="btn-login">Cadastrar</button>
      </form>

      <p class="auth-switch-text">
        Já tem conta?
        <a href="index.php" class="auth-link">Fazer login</a>
      </p>

      <p class="auth-switch-text">
        <a href="recuperar_senha.php" class="auth-link" style="font-size: 14px;">
          <i class="fa-solid fa-key"></i> Esqueci a senha
        </a>
      </p>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
        <p class="auth-message error">Esse usuário já existe.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 2): ?>
        <p class="auth-message error">Preencha todos os campos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 3): ?>
        <p class="auth-message error">As senhas não coincidem.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'email_invalido'): ?>
        <p class="auth-message error">Email inválido.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'email_duplicado'): ?>
        <p class="auth-message error">Esse email já está cadastrado.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'senha_fraca'): ?>
        <p class="auth-message error">Senha fraca. Mínimo 6 caracteres.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'usuario_invalido'): ?>
        <p class="auth-message error">Usuário inválido. Use apenas letras, números e underscore (3-50 caracteres).</p>
      <?php endif; ?>
    </div>
    </main>
  </div>

  <script src="script.js?v=401"></script>
  <link rel="stylesheet" href="../a11y.css">
  <script src="../a11y.js"></script>
</body>
</html>
