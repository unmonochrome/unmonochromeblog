document.addEventListener("DOMContentLoaded", () => {
  const passwordInput = document.getElementById("senha");
  const confirmPasswordInput = document.getElementById("confirmar_senha");
  const userInput = document.getElementById("usuario");
  const character = document.querySelector(".character-sit");
  const registerForm = document.getElementById("registerForm");
  const passwordFeedback = document.getElementById("passwordFeedback");
  const cardWrapper = document.querySelector(".card-login-wrapper");
  const card = document.querySelector(".card-login");

  // adiciona orbs de fundo sem precisar mexer no HTML
  const orb1 = document.createElement("div");
  orb1.className = "bg-orb orb-1";

  const orb2 = document.createElement("div");
  orb2.className = "bg-orb orb-2";

  document.body.appendChild(orb1);
  document.body.appendChild(orb2);

  // glow do mouse
  if (card && !card.querySelector(".card-glow")) {
    const glow = document.createElement("div");
    glow.className = "card-glow";
    card.prepend(glow);
  }

  const toggleButtons = document.querySelectorAll(".toggle-password");

  toggleButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-target") || "senha";
      const targetInput = document.getElementById(targetId);
      const icon = button.querySelector("i");

      if (!targetInput) return;

      const isPassword = targetInput.type === "password";
      targetInput.type = isPassword ? "text" : "password";

      if (icon) {
        icon.className = isPassword
          ? "fa-regular fa-eye-slash"
          : "fa-regular fa-eye";
      }
    });
  });

  function setCharacterStyle(transformValue) {
    if (!character) return;
    character.style.transform = transformValue;
  }

  if (userInput) {
    userInput.addEventListener("focus", () => {
      setCharacterStyle("translateY(-2px) rotate(-2deg)");
    });

    userInput.addEventListener("blur", () => {
      setCharacterStyle("");
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener("focus", () => {
      setCharacterStyle("translateY(-1px) rotate(2deg) scale(1.02)");
    });

    passwordInput.addEventListener("blur", () => {
      setCharacterStyle("");
    });
  }

  if (confirmPasswordInput) {
    confirmPasswordInput.addEventListener("focus", () => {
      setCharacterStyle("translateY(-1px) rotate(2deg) scale(1.02)");
    });

    confirmPasswordInput.addEventListener("blur", () => {
      setCharacterStyle("");
    });
  }

  function setWrapperState(input, state) {
    if (!input) return;
    const wrapper = input.closest(".input-wrapper");
    if (!wrapper) return;

    wrapper.classList.remove("error", "success");

    if (state) {
      wrapper.classList.add(state);
    }
  }

  function validatePasswords(showNeutral = false) {
    if (!passwordInput || !confirmPasswordInput || !passwordFeedback) return true;

    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (!password && !confirmPassword) {
      passwordFeedback.textContent = "";
      passwordFeedback.className = "password-feedback";

      setWrapperState(passwordInput, null);
      setWrapperState(confirmPasswordInput, null);
      return true;
    }

    if (!confirmPassword && showNeutral) {
      passwordFeedback.textContent = "Confirme a senha.";
      passwordFeedback.className = "password-feedback";

      setWrapperState(passwordInput, null);
      setWrapperState(confirmPasswordInput, null);
      return false;
    }

    if (password !== confirmPassword) {
      passwordFeedback.textContent = "As senhas não coincidem.";
      passwordFeedback.className = "password-feedback error";

      setWrapperState(passwordInput, "error");
      setWrapperState(confirmPasswordInput, "error");
      return false;
    }

    passwordFeedback.textContent = "Senhas coincidem.";
    passwordFeedback.className = "password-feedback success";

    setWrapperState(passwordInput, "success");
    setWrapperState(confirmPasswordInput, "success");
    return true;
  }

  if (passwordInput && confirmPasswordInput) {
    passwordInput.addEventListener("input", () => validatePasswords(true));
    confirmPasswordInput.addEventListener("input", () => validatePasswords(true));
  }

  if (registerForm) {
    registerForm.addEventListener("submit", (event) => {
      const passwordsAreValid = validatePasswords(true);

      if (!passwordsAreValid) {
        event.preventDefault();
      }
    });
  }

  // parallax do card
  if (cardWrapper && card) {
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!reduceMotion) {
      cardWrapper.addEventListener("mousemove", (event) => {
        const rect = cardWrapper.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateY = ((x - centerX) / centerX) * 5;
        const rotateX = ((centerY - y) / centerY) * 5;

        cardWrapper.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        card.style.setProperty("--mouse-x", `${x}px`);
        card.style.setProperty("--mouse-y", `${y}px`);
      });

      cardWrapper.addEventListener("mouseleave", () => {
        cardWrapper.style.transform = "perspective(1000px) rotateX(0deg) rotateY(0deg)";
      });
    }
  }
});