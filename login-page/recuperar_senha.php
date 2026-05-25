<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=300">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
  <title>Recuperar Senha - UNMONOCHROME</title>
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

      <h1>RECUPERAR SENHA</h1>

      <form action="../backend/enviar_senha.php" method="POST" style="width:100%;">
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
            <input type="email" id="email" name="email" placeholder="Digite seu email cadastrado" required>
          </div>
        </div>

        <p style="font-size: 12px; color: #999; text-align: center; margin: 10px 0;">
          📧 Enviaremos um código para seu email
        </p>

        <button type="submit" class="btn-login">Enviar Código</button>
      </form>

      <p class="auth-switch-text">
        Lembrou a senha?
        <a href="index.php" class="auth-link">Voltar ao Login</a>
      </p>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'campos_vazios'): ?>
        <p class="auth-message error">Preencha todos os campos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'email_invalido'): ?>
        <p class="auth-message error">Email inválido.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'db_error'): ?>
        <p class="auth-message error">Erro no banco de dados. Tente novamente.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'csrf'): ?>
        <p class="auth-message error">Sessão expirada. Tente novamente.</p>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'email_envio'): ?>
        <p class="auth-message error">Não foi possível enviar o email agora. Tente novamente em alguns minutos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['sucesso'])): ?>
        <p class="auth-message success">
          ✓ Código enviado com sucesso! Verifique seu email.
        </p>
      <?php endif; ?>
    </div>
    </main>
  </div>

  <link rel="stylesheet" href="../a11y.css">
  <script src="../a11y.js"></script>
  <script src="script.js?v=300"></script>
</body>

</html>
