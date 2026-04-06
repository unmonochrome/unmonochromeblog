<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=300">
  <title>Login - UNMONOCHROME</title>
</head>

<body>

  <div class="card-login-wrapper">
    <iframe class="character-sit" src="img/animacao/johnbalanco.html" scrolling="no" title="Personagem animado">
    </iframe>

    <div class="character-shadow"></div>

    <div class="card-login">
      <div class="card-shine"></div>

      <h1>LOGIN</h1>

      <form action="../backend.php/login.php" method="POST" style="width:100%;">
        <div class="textfield">
          <label for="usuario">Usuário</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-user input-icon"></i>
            <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" required>
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

      <?php if (isset($_GET['erro'])): ?>
        <p class="auth-message error">Usuário ou senha inválidos.</p>
      <?php endif; ?>

      <?php if (isset($_GET['cadastro'])): ?>
        <p class="auth-message success">Conta criada com sucesso! Faça login.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="script.js?v=300"></script>
</body>

</html>