<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=300">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicon.png">
<link rel="apple-touch-icon" href="img/favicon.png">
  <title>Login - UNMONOCHROME</title>
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

      <h1>LOGIN</h1>

      <form action="../backend/login.php" method="POST" style="width:100%;">
        <?php 
        require_once "../backend/csrf.php";
        ?>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        
        <div class="textfield">
          <label for="usuario">Email ou Usuário</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-user input-icon"></i>
            <input type="text" id="usuario" name="usuario" placeholder="Digite seu email ou usuário" required>
          </div>
        </div>

        <div class="textfield">
          <label for="senha">Senha</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar senha">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login">Login</button>
      </form>

      <p class="auth-switch-text">
        Não tem conta?
        <a href="cadastro.php" class="auth-link">Cadastre-se</a>
      </p>

      <p class="auth-switch-text">
        <a href="recuperar_senha.php" class="auth-link" style="font-size: 14px;">
          <i class="fa-solid fa-key"></i> Esqueci a senha
        </a>
      </p>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
        <p class="auth-message error">Usuário ou senha inválidos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'csrf'): ?>
        <p class="auth-message error">Sessão expirada. Tente novamente.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'campos_vazios'): ?>
        <p class="auth-message error">Preencha todos os campos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['cadastro'])): ?>
        <p class="auth-message success">Conta criada com sucesso! Faça login.</p>
      <?php endif; ?>

      <?php if (isset($_GET['sucesso_recuperacao'])): ?>
        <p class="auth-message success">✓ Senha redefinida com sucesso! Faça login.</p>
      <?php endif; ?>
    </div>
  </div>
  </main>

  <link rel="stylesheet" href="../a11y.css">
  <script src="../a11y.js"></script>
  <script src="script.js?v=300"></script>
</body>

</html>
