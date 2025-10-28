// =====================
// Seleção de elementos
// =====================
const form = document.getElementById("cadastro-form");
const nameField = document.getElementById("name");
const emailField = document.getElementById("email");
const passwordField = document.getElementById("password");
const errorSpan = document.getElementById("cadastro-error");

// =====================
// Funções de validação
// =====================
function validateName(name) {
  return name.trim().length >= 3;
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(email).toLowerCase());
}

function validatePassword(password) {
  return password.length >= 6;
}

function validateForm(name, email, password) {
  if (!name || !email || !password) {
    showErrorMessage("Todos os campos são obrigatórios.");
    return false;
  }

  if (!validateName(name)) {
    showFieldError(nameField, "O nome deve ter pelo menos 3 caracteres.");
    return false;
  }

  if (!validateEmail(email)) {
    showFieldError(emailField, "Formato de email inválido.");
    return false;
  }

  if (!validatePassword(password)) {
    showFieldError(passwordField, "A senha deve ter pelo menos 6 caracteres.");
    return false;
  }

  clearErrorMessage();
  return true;
}

// =====================
// Manipulação visual
// =====================
function showErrorMessage(message) {
  errorSpan.textContent = message;
  errorSpan.classList.remove("hidden");
}

function clearErrorMessage() {
  errorSpan.textContent = "";
  errorSpan.classList.add("hidden");
}

function showFieldError(field, message) {
  field.classList.add("input-error");
  field.focus();
  showErrorMessage(message);

  field.addEventListener("input", () => {
    field.classList.remove("input-error");
    clearErrorMessage();
  }, { once: true });
}

// =====================
// Requisição Fetch
// =====================
async function sendRegisterRequest(userData) {
  try {
    const response = await fetch("./../../app/controllers/UserController.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(userData)
    });

    const result = await response.json();
    console.log("Resposta do servidor:", result);

    if (result.status === "success") {
      alert(`Usuário cadastrado com sucesso! ID: ${result.user_id}`);
      form.reset();
      // window.location.href = "./../pages/contacts.php";
    } else {
      // Se o erro for email já cadastrado
      if (result.message === "Email já cadastrado") {
        showFieldError(emailField, result.message);
      } else {
        showErrorMessage(result.message || "Erro ao cadastrar.");
      }
    }

  } catch (error) {
    console.error("Erro na requisição:", error);
    showErrorMessage("Falha na comunicação com o servidor.");
  }
}

// =====================
// Função principal
// =====================
function cadastro(formElement) {
  if (formElement) {
    formElement.addEventListener("submit", (e) => {
      e.preventDefault();

      const name = nameField.value;
      const email = emailField.value;
      const password = passwordField.value;

      if (!validateForm(name, email, password)) return;

      const user = {
        name: name,
        email: email,
        password: password,
        action: "register"
      };

      sendRegisterRequest(user);
    });
  }
}

// =====================
// Inicialização
// =====================
cadastro(form);