// Loading states e validações client-side para melhor UX
document.addEventListener('DOMContentLoaded', () => {
  const submitPostBtn = document.getElementById('submitPostBtn');
  const criarPostForm = document.getElementById('criarPostForm');
  const imagemInput = document.getElementById('imagem');
  const fileNameSpan = document.getElementById('file-name-post');

  // Validar tamanho de arquivo no client
  if (imagemInput) {
    imagemInput.addEventListener('change', function() {
      if (this.files.length > 0) {
        const file = this.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB

        // Mostrar nome
        fileNameSpan.textContent = file.name;

        // Validar tamanho
        if (file.size > maxSize) {
          alert('Arquivo muito grande! Máximo 5MB.');
          this.value = '';
          fileNameSpan.textContent = 'Nenhum arquivo escolhido';
          return;
        }

        // Validar tipo
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type)) {
          alert('Formato inválido! Use JPG, PNG, WebP ou GIF.');
          this.value = '';
          fileNameSpan.textContent = 'Nenhum arquivo escolhido';
          return;
        }
      }
    });
  }

  // Loading state ao submeter
  if (criarPostForm) {
    criarPostForm.addEventListener('submit', function() {
      if (submitPostBtn) {
        submitPostBtn.disabled = true;
        submitPostBtn.textContent = '⏳ Publicando...';
        submitPostBtn.style.opacity = '0.6';
        submitPostBtn.style.cursor = 'not-allowed';
      }
    });
  }

  // Confirmar deletar com melhor UX
  const deleteLinks = document.querySelectorAll('a[href*="excluir_post.php"]');
  deleteLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Tem certeza que deseja excluir esta postagem? Esta ação não pode ser desfeita.')) {
        window.location.href = this.href;
      }
    });
  });

  // Prevenir múltiplos submits em forms de comentários
  const commentForms = document.querySelectorAll('form[action*="criar_comentario"]');
  commentForms.forEach(form => {
    form.addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ Enviando...';
      }
    });
  });
});
