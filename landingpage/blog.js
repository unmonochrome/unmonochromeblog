document.addEventListener("DOMContentLoaded", () => {
  const postInput = document.getElementById("imagem");
  const fileNamePost = document.getElementById("file-name-post");

  if (postInput && fileNamePost) {
    postInput.addEventListener("change", function () {
      fileNamePost.textContent = this.files.length > 0
        ? this.files[0].name
        : "Nenhum arquivo escolhido";
    });
  }
});