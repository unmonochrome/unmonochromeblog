document.addEventListener("DOMContentLoaded", () => {
  // MENU MOBILE
  const menuToggle = document.getElementById("menuToggle");
  const navLinks = document.getElementById("navLinks");

  if (menuToggle && navLinks) {
    menuToggle.addEventListener("click", () => {
      navLinks.classList.toggle("show");
    });
  }

  // REVEAL ON SCROLL
  const revealElements = document.querySelectorAll(".reveal");

  if (revealElements.length) {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("active");
          }
        });
      },
      { threshold: 0.12 }
    );

    revealElements.forEach((element) => {
      revealObserver.observe(element);
    });
  }

  // INPUT DE ARQUIVO - BLOG / EDITAR POST
  const imagemInput = document.getElementById("imagem");
  const fileNamePost = document.getElementById("file-name-post");
  const fileNameEdit = document.getElementById("file-name-edit");

  if (imagemInput) {
    imagemInput.addEventListener("change", function () {
      const fileName =
        this.files.length > 0 ? this.files[0].name : "Nenhum arquivo escolhido";

      if (fileNamePost) fileNamePost.textContent = fileName;
      if (fileNameEdit) fileNameEdit.textContent = fileName;
    });
  }

  // INPUT DE ARQUIVO - PERFIL
  const fotoInput = document.getElementById("foto_perfil");
  const fileNameFoto = document.getElementById("file-name-foto");

  if (fotoInput && fileNameFoto) {
    fotoInput.addEventListener("change", function () {
      fileNameFoto.textContent =
        this.files.length > 0 ? this.files[0].name : "Nenhum arquivo escolhido";
    });
  }

  // Modal de imagem
  const createImageModal = () => {
    if (document.getElementById("image-modal-overlay")) return;

    const overlay = document.createElement("div");
    overlay.id = "image-modal-overlay";
    overlay.innerHTML = `
      <div class="image-modal-frame" role="dialog" aria-modal="true">
        <button class="image-modal-close" aria-label="Fechar imagem">×</button>
        <img class="image-modal-img" alt="Imagem ampliada">
        <p class="image-modal-caption"></p>
      </div>
    `;

    overlay.addEventListener("click", (event) => {
      if (event.target === overlay || event.target.closest(".image-modal-close")) {
        closeImageModal();
      }
    });

    document.body.appendChild(overlay);
  };

  const openImageModal = (image) => {
    createImageModal();

    const overlay = document.getElementById("image-modal-overlay");
    const modalImg = overlay.querySelector(".image-modal-img");
    const caption = overlay.querySelector(".image-modal-caption");

    modalImg.src = image.src;
    modalImg.alt = image.alt || "Imagem ampliada";
    caption.textContent = image.alt || "Clique para fechar";

    overlay.classList.add("visible");
    document.body.classList.add("image-modal-open");
  };

  const closeImageModal = () => {
    const overlay = document.getElementById("image-modal-overlay");
    if (!overlay) return;
    overlay.classList.remove("visible");
    document.body.classList.remove("image-modal-open");
  };

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeImageModal();
    }
  });

  const isImageExcluded = (img) => {
    return (
      img.closest(".logo") ||
      img.closest(".menu-toggle") ||
      img.closest("footer") ||
      img.closest(".custom-file-wrapper") ||
      img.closest(".nav-actions") ||
      img.closest("#inicio") ||
      img.closest("#daltonismo") ||
      img.closest("#sobre") ||
      img.classList.contains("perfil-avatar-img")
    );
  };

  const makeZoomable = (img) => {
    if (isImageExcluded(img)) return;
    if (img.clientWidth < 80 && img.clientHeight < 80) return;
    img.classList.add("zoomable-image");
  };

  const allImages = document.querySelectorAll("img");
  allImages.forEach(makeZoomable);

  document.body.addEventListener("click", (event) => {
    const target = event.target;
    if (target.tagName !== "IMG") return;
    if (!target.classList.contains("zoomable-image")) return;
    event.preventDefault();
    openImageModal(target);
  });
});