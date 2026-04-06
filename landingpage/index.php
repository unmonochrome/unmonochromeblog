<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UNMONOCHROME</title>
  <link rel="stylesheet" href="https://use.typekit.net/xvn1qry.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="site.css">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicon.png">
<link rel="apple-touch-icon" href="img/favicon.png">
  <style>
    .user-status {
      color: white;
      font-weight: 700;
      font-size: 1rem;
      white-space: nowrap;
    }

    .active-nav-btn {
      color: #fff;
      background: linear-gradient(90deg, #ff2d73, #ff4f86);
      border: 1px solid rgba(255, 255, 255, 0.10);
      box-shadow: 0 12px 30px rgba(255, 45, 115, 0.22);
    }

    .active-nav-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 18px 40px rgba(255, 45, 115, 0.32);
    }
  </style>
</head>
<body>

  <header class="navbar">
    <a href="#inicio" class="logo">
      <img src="img/logo-novo.png" alt="UNMONOCHROME">
    </a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">☰</button>

    <nav class="nav-links" id="navLinks">
      <a href="#inicio">Início</a>
      <a href="#gameplay">Gameplay</a>
      <a href="#daltonismo">Daltonismo</a>
      <a href="#processo">Processo Criativo</a>
      <a href="#sobre">Sobre Nós</a>
      <a href="#comunidade">Comunidade</a>

      <div class="nav-actions-mobile">
        <?php if (isset($_SESSION["usuario_id"])): ?>
          <span class="user-status mobile-only">
            Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>
            (<?php echo htmlspecialchars($_SESSION["usuario_tipo"]); ?>)
          </span>
          <a href="perfil.php" class="btn-nav secondary">Perfil</a>
          <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
          <a href="blog.php" class="btn-nav primary">Blog</a>
        <?php else: ?>
          <a href="../login-page/index.php" class="btn-nav secondary">Login</a>
          <a href="blog.php" class="btn-nav primary">Blog</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="nav-actions nav-actions-desktop">
      <?php if (isset($_SESSION["usuario_id"])): ?>
        <span class="user-status">
          Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>
          (<?php echo htmlspecialchars($_SESSION["usuario_tipo"]); ?>)
        </span>
        <a href="perfil.php" class="btn-nav secondary">Perfil</a>
        <a href="../backend/logout.php" class="btn-nav secondary">Sair</a>
        <a href="blog.php" class="btn-nav primary">Blog</a>
      <?php else: ?>
        <a href="../login-page/index.php" class="btn-nav secondary">Login</a>
        <a href="blog.php" class="btn-nav primary">Blog</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <section class="hero reveal" id="inicio">
      <div class="hero-text">
        <p class="tag">Jogo autoral • acessibilidade • identidade visual • daltonismo</p>
        <h1>
          <span class="hero-un">UN</span><span class="hero-rest">MONOCHROME</span>
        </h1>
        <p class="hero-description">
          Como sensibilizar pessoas com visão típica sobre as nuances reais da percepção de cores dos daltônicos?
        </p>

        <div class="hero-buttons">
          <a href="#gameplay" class="btn primary">Jogar agora</a>
          <a href="#processo" class="btn secondary">Ver processo criativo</a>
        </div>

        <p class="hero-mini-info">Projeto acadêmico • Boss rush 2D • Sensibilização sobre acessibilidade visual</p>
      </div>

      <div class="hero-image">
        <div class="hero-glow"></div>
        <img src="img/personagem-principal.png" alt="Personagem principal de UNMONOCHROME">
      </div>
    </section>

    <section class="section gameplay reveal" id="gameplay">
      <div class="section-header">
        <span class="section-tag">Gameplay</span>
        <h2>Conheça o jogo</h2>
        <p>
          Aqui você pode colocar trailer, screenshots ou embed da página do itch.io.
        </p>
      </div>

      <div class="gameplay-grid">
        <div class="gameplay-preview card">
          <div class="placeholder media-box">
            <p>Substitua este espaço por trailer, vídeo ou embed do itch.io</p>
          </div>

          <div class="gameplay-images">
            <img src="img/gameplay-1.png" alt="Screenshot do jogo 1">
            <img src="img/gameplay-2.png" alt="Screenshot do jogo 2">
          </div>
        </div>

        <div class="gameplay-info card">
          <h3>Jogue nosso jogo :)</h3>
          <p>
            Esse jogo é um boss rush indie desenvolvido como TCC tendo as cores e a percepção visual como tema central.
          </p>
          <ul>
            <li>Identidade única</li>
            <li>Percepção visual</li>
            <li>Serious Game</li>
          </ul>
          <a href="https://itch.io/" target="_blank" rel="noopener noreferrer" class="btn primary">Abrir no itch.io</a>
        </div>
      </div>
    </section>

    <section class="section daltonismo reveal" id="daltonismo">
      <div class="section-header">
        <span class="section-tag">Acessibilidade</span>
        <h2>Curiosidades sobre o daltonismo</h2>
        <p>
          O projeto também busca informar e sensibilizar sobre diferentes formas de percepção das cores.
        </p>
      </div>

      <div class="cards-grid">
        <article class="info-card card">
          <h3>O que é?</h3>
          <p>
            Daltonismo é uma condição visual que afeta a percepção de determinadas cores,
            principalmente entre tons de vermelho, verde e, em alguns casos, azul.
          </p>
        </article>

        <article class="info-card card">
          <h3>Nem todo daltonismo é igual</h3>
          <p>
            Existem diferentes tipos e níveis de alteração na percepção de cor.
            Cada pessoa pode experimentar o mundo visual de uma forma distinta.
          </p>
        </article>

        <article class="info-card card">
          <h3>Jogos também precisam ser acessíveis</h3>
          <p>
            Interface, contraste, feedback visual e escolha de paleta são pontos
            essenciais para tornar experiências interativas mais inclusivas.
          </p>
        </article>
      </div>

      <div class="tipos-header">
        <h2>Como o personagem é visto em cada tipo</h2>
        <p>Veja como a percepção de cores muda dependendo do tipo de daltonismo.</p>
      </div>

      <div class="tipos-grid">
        <div class="tipo-card card">
          <div class="tipo-img-wrapper">
            <img src="img/personagem-normal.png" alt="Visão Normal do personagem" onclick="openImageModal(this.src, this.alt)">
            <span class="tipo-badge badge-normal">Referência</span>
          </div>
          <div class="tipo-info">
            <h3>Visão Normal</h3>
            <p>Percepção completa do espectro de cores, sem alterações.</p>
          </div>
        </div>

        <div class="tipo-card card">
          <div class="tipo-img-wrapper">
            <img src="img/personagem-protanopia.png" alt="Visão com Protanopia" onclick="openImageModal(this.src, this.alt)">
            <span class="tipo-badge badge-protanopia">Tipo 1</span>
          </div>
          <div class="tipo-info">
            <h3>Protanopia</h3>
            <p>Dificuldade em perceber a cor <strong>vermelha</strong>. Tons de vermelho podem parecer marrons ou verdes escuros.</p>
            <span class="tipo-tag tag-vermelho">Afeta o vermelho</span>
          </div>
        </div>

        <div class="tipo-card card">
          <div class="tipo-img-wrapper">
            <img src="img/personagem-deuteranopia.png" alt="Visão com Deuteranopia" onclick="openImageModal(this.src, this.alt)">
            <span class="tipo-badge badge-deuteranopia">Tipo 2</span>
          </div>
          <div class="tipo-info">
            <h3>Deuteranopia</h3>
            <p>Dificuldade em perceber a cor <strong>verde</strong>. É o tipo mais comum de daltonismo.</p>
            <span class="tipo-tag tag-verde">Afeta o verde</span>
          </div>
        </div>

        <div class="tipo-card card">
          <div class="tipo-img-wrapper">
            <img src="img/personagem-tritanopia.png" alt="Visão com Tritanopia" onclick="openImageModal(this.src, this.alt)">
            <span class="tipo-badge badge-tritanopia">Tipo 3</span>
          </div>
          <div class="tipo-info">
            <h3>Tritanopia</h3>
            <p>Dificuldade em perceber a cor <strong>azul</strong>. Mais raro, confunde azul com verde e amarelo com violeta.</p>
            <span class="tipo-tag tag-azul">Afeta o azul</span>
          </div>
        </div>
      </div>
    </section>

    <section class="section processo reveal" id="processo">
      <div class="section-header">
        <span class="section-tag">Bastidores</span>
        <h2>Documentação do processo criativo</h2>
        <p>
          Registros da construção artística, conceitual e visual do projeto.
        </p>
      </div>

      <div class="timeline">
        <div class="timeline-item card">
          <img src="img/processo-1.png" alt="Arte conceitual do protagonista" onclick="openImageModal(this.src, this.alt)">
          <h3>Conceito inicial</h3>
          <p>Um boss rush 2D de conscientização, que utiliza a interatividade dos games para gerar empatia e compreensão sobre a daltonia.</p>
        </div>

        <div class="timeline-item card">
          <img src="img/processo-2.png" alt="Primeiras artes" onclick="openImageModal(this.src, this.alt)">
          <h3>Exploração visual</h3>
          <p>A estética maximalista de Hirohiko Araki (JoJo) fundida à icônica identidade punk de Supla. Uma união entre o design de personagens japonês e o visual clássico do rock nacional.</p>
        </div>

        <div class="timeline-item card">
          <img src="img/processo-3.png" alt="Esboços do protagonista" onclick="openImageModal(this.src, this.alt)">
          <h3>Personagem e expressão</h3>
          <p>No processo de criação do personagem, a prioridade foi a legibilidade visual.</p>
          <p>O uso de um tom de vermelho intenso no cabelo garante que o jogador nunca perca o protagonista de vista, independentemente do filtro de cor aplicado.</p>
          <p>A heterocromia atua como um elemento narrativo e funcional, reagindo às mudanças cromáticas da história.</p>
          <p>As roupas seguem a linha do punk brasileiro, trazendo uma estética urbana e crua que complementa a jornada de descoberta visual.</p>
        </div>

        <div class="timeline-item card">
          <div class="four-process-images">
            <img src="img/processo-4.png" alt="Giorno Giovanna de Jojo Bizarre Adventure">
            <img src="img/processo-5.png" alt="Encarte do álbum de Supla">
            <img src="img/processo-6.png" alt="Menu do jogo Persona 3 Reload">
            <img src="img/processo-7-novo.png" alt="Fotografia de Gal Costa">
          </div>
          <h3>Referências</h3>
          <p>Algumas das milhares de referências utilizadas para a criação da identidade visual do projeto, sejam elas de artistas, obras literárias, obras audiovisuais e tendências culturais.</p>
        </div>
      </div>
    </section>

    <section class="section sobre reveal" id="sobre">
      <div class="section-header">
        <span class="section-tag">Equipe</span>
        <h2>Sobre nós</h2>
        <p>Conheça quem participou da criação de UNMONOCHROME.</p>
      </div>

      <div class="team-grid">
        <article class="team-card card">
          <img src="img/equipe-1.png" alt="Cauã Carvalho" onclick="openImageModal(this.src, this.alt)">
          <h3>Cauã Carvalho</h3>
          <p class="role">Programador e Designer</p>
          <p>Programação, design dos menus e interface do jogo.</p>
          <a href="https://instagram.com/smoking.app" target="_blank" rel="noopener noreferrer" class="team-instagram">
            <i class="fa-brands fa-instagram"></i> @smoking.app
          </a>
        </article>

        <article class="team-card card">
          <img src="img/equipe-2.png" alt="Gabriel Felix" onclick="openImageModal(this.src, this.alt)">
          <h3>Gabriel Felix</h3>
          <p class="role">Programador e Analista</p>
          <p>Programação, análise de acessibilidade e documentação do processo criativo.</p>
          <a href="https://instagram.com/gbrielfelx" target="_blank" rel="noopener noreferrer" class="team-instagram">
            <i class="fa-brands fa-instagram"></i> @gbrielfelx
          </a>
        </article>

        <article class="team-card card">
          <img src="img/equipe-3.png" alt="Yochanan Ismael" onclick="openImageModal(this.src, this.alt)">
          <h3>Yochanan Ismael</h3>
          <p class="role">Designer e Artista</p>
          <p>Design dos personagens e todas as artes do jogo.</p>
          <a href="https://instagram.com/yorra_nan" target="_blank" rel="noopener noreferrer" class="team-instagram">
            <i class="fa-brands fa-instagram"></i> @yorra_nan
          </a>
        </article>
      </div>
    </section>

    <section class="section comunidade reveal" id="comunidade">
      <div class="community-box card">
        <span class="section-tag">Comunidade</span>
        <h2>Participe da conversa</h2>
        <p>
          Quer acompanhar o desenvolvimento, compartilhar feedback e participar das discussões sobre acessibilidade nos games?
          Entre no blog da comunidade e contribua com o projeto.
        </p>
        <div class="hero-buttons community-buttons">
          <a href="blog.php" class="btn primary">Acessar blog</a>
          <?php if (!isset($_SESSION["usuario_id"])): ?>
            <a href="../login-page/index.php" class="btn secondary">Fazer login</a>
          <?php else: ?>
            <a href="perfil.php" class="btn secondary">Meu perfil</a>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer reveal">
    <div class="footer-brand">
      <img src="img/logo-novo.png" alt="UNMONOCHROME">
    </div>

    <div class="footer-links">
      <a href="#inicio">Início</a>
      <a href="#gameplay">Gameplay</a>
      <a href="#daltonismo">Daltonismo</a>
      <a href="#processo">Processo Criativo</a>
      <a href="#sobre">Sobre Nós</a>
      <a href="#comunidade">Comunidade</a>
    </div>

    <p class="footer-title">UNMONOCHROME © 2025</p>
    <p class="footer-description">Projeto autoral sobre acessibilidade, percepção visual e expressão artística.</p>
  </footer>


  <script src="script.js?v=3"></script>
</body>
</html>
