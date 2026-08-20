document.addEventListener("DOMContentLoaded", () => {
  const loginToggle = document.getElementById("btnLogin");
  const registroButton = document.getElementById("btnRegistro");

  const modals = {
    registro: document.getElementById("registroModal"),
    admin: document.getElementById("loginAdminModal"),
    verificacion: document.getElementById("verificacionModal")
  };

  function openModal(modal) {
    if (!modal) return;

    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");

    const firstInput = modal.querySelector("input");
    setTimeout(() => firstInput?.focus(), 150);
  }

  function closeModal(modal) {
    if (!modal) return;

    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");

    if (!document.querySelector(".modal-overlay.is-open")) {
      document.body.classList.remove("modal-open");
    }
  }

  function closeAllModals() {
    Object.values(modals).forEach(closeModal);
  }

  loginToggle?.addEventListener("click", () => {
    openModal(modals.admin);
  });

  registroButton?.addEventListener("click", () => {
    openModal(modals.registro);
  });

  document.querySelectorAll("[data-close-modal]").forEach((button) => {
    button.addEventListener("click", () => {
      closeModal(button.closest(".modal-overlay"));
    });
  });

  Object.values(modals).forEach((modal) => {
    modal?.addEventListener("click", (event) => {
      if (event.target === modal) {
        closeModal(modal);
      }
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeAllModals();
    }
  });

  // =========================================================
  // REGISTRO -> Controller/PersonaController.php
  // =========================================================
  document.getElementById("formRegistro")?.addEventListener("submit", async (event) => {
    event.preventDefault();

    const form = event.currentTarget;
    const submitButton = form.querySelector("button[type=submit]");
    const originalText = submitButton?.textContent || "REGISTRAR";

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const tipoperSelect = document.getElementById("tipoper");
    if (!tipoperSelect || tipoperSelect.value === "0" || tipoperSelect.value === "") {
      alert("Selecciona un tipo de persona.");
      tipoperSelect?.focus();
      return;
    }

    const formData = new FormData();

    formData.append("nombres", document.getElementById("regNombres").value.trim());
    formData.append("apellidos", document.getElementById("regApellidos").value.trim());
    formData.append("f_nac", document.getElementById("regFecha").value);
    formData.append("sexo", form.querySelector("input[name=sexo]:checked")?.value || "");
    formData.append("ci", document.getElementById("regCedula").value.trim());
    formData.append("extension", document.getElementById("regExtension").value.trim());
    formData.append("estcivil", form.querySelector("input[name=estado_civil]:checked")?.value || "");
    formData.append("telefono", document.getElementById("regTelefono").value.trim());
    formData.append("correo", document.getElementById("regCorreo").value.trim());
    formData.append("password", document.getElementById("regPassword").value);
    formData.append("tipoper", document.getElementById("tipoper").value);

    try {
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "REGISTRANDO...";
      }

      const response = await fetch("controllers/PersonaController.php?action=registrar", {
        method: "POST",
        body: formData
      });

      const resultado = await response.json();

      if (!response.ok || !resultado.ok) {
        throw new Error(resultado.mensaje || "No se pudo registrar la persona.");
      }

      alert(resultado.mensaje);
      form.reset();
      closeModal(modals.registro);
    } catch (error) {
      console.error("Error de registro:", error);
      alert(error.message || "No se pudo completar el registro.");
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
      }
    }
  });

  // =========================================================
  // LOGIN (admin, por ahora único modal) -> Controller/AuthController.php
  // =========================================================
  document.querySelectorAll(".login-form").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      const usuarioInput = form.querySelector('input[name="usuario"]');
      const passwordInput = form.querySelector('input[name="password"]');
      const submitButton = form.querySelector("button[type=submit]");
      const originalText = submitButton?.textContent || "INGRESAR";

      const formData = new FormData();
      formData.append("correo", usuarioInput.value.trim());
      formData.append("password", passwordInput.value);

      try {
        if (submitButton) {
          submitButton.disabled = true;
          submitButton.textContent = "INGRESANDO...";
        }

        const response = await fetch("controllers/AuthController.php?action=login", {
          method: "POST",
          body: formData
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.ok) {
          throw new Error(resultado.mensaje || "No se pudo iniciar sesión.");
        }

        if (resultado.data?.requiere_verificacion) {
          // Contraseña correcta: ahora pedimos el código de 4 dígitos
          // que se envió al correo, en vez de entrar directo.
          const correo = resultado.data.correo;
          document.getElementById("verifCorreo").value = correo;

          const subtexto = document.getElementById("verifSubtexto");
          if (subtexto) subtexto.textContent = `Te enviamos un código de 4 dígitos a ${correo}.`;

          const verifMensaje = document.getElementById("verifMensaje");
          if (verifMensaje) {
            verifMensaje.textContent = "";
            verifMensaje.style.color = "";
          }

          closeModal(modals.admin);
          openModal(modals.verificacion);
        } else {
          window.location.href = resultado.data.redirect;
        }
      } catch (error) {
        console.error("Error de login:", error);
        alert(error.message || "No se pudo iniciar sesión.");
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = originalText;
        }
      }
    });
  });

  // =========================================================
  // VERIFICACIÓN DE CÓDIGO (2do factor) -> AuthController.php
  // =========================================================
  document.getElementById("formVerificacion")?.addEventListener("submit", async (event) => {
    event.preventDefault();

    const form = event.currentTarget;
    const submitButton = form.querySelector("button[type=submit]");
    const originalText = submitButton?.textContent || "VERIFICAR";
    const verifMensaje = document.getElementById("verifMensaje");

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData();
    formData.append("correo", document.getElementById("verifCorreo").value);
    formData.append("codigo", document.getElementById("verifCodigo").value.trim());

    try {
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "VERIFICANDO...";
      }

      const response = await fetch("controllers/AuthController.php?action=verificar-codigo", {
        method: "POST",
        body: formData
      });

      const resultado = await response.json();

      if (!response.ok || !resultado.ok) {
        throw new Error(resultado.mensaje || "No se pudo verificar el código.");
      }

      if (verifMensaje) {
        verifMensaje.style.color = "green";
        verifMensaje.textContent = resultado.mensaje;
      }

      window.location.href = resultado.data.redirect;
    } catch (error) {
      console.error("Error de verificación:", error);
      if (verifMensaje) {
        verifMensaje.style.color = "crimson";
        verifMensaje.textContent = error.message || "No se pudo verificar el código.";
      }
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
      }
    }
  });

  document.getElementById("btnReenviarCodigo")?.addEventListener("click", async (event) => {
    event.preventDefault();

    const verifMensaje = document.getElementById("verifMensaje");
    if (verifMensaje) {
      verifMensaje.style.color = "";
      verifMensaje.textContent = "Reenviando...";
    }

    try {
      const response = await fetch("controllers/AuthController.php?action=reenviar-codigo", {
        method: "POST"
      });
      const resultado = await response.json();

      if (verifMensaje) {
        verifMensaje.style.color = resultado.ok ? "green" : "crimson";
        verifMensaje.textContent = resultado.mensaje;
      }
    } catch (error) {
      console.error("Error al reenviar código:", error);
      if (verifMensaje) {
        verifMensaje.style.color = "crimson";
        verifMensaje.textContent = "No se pudo reenviar el código.";
      }
    }
  });
});
