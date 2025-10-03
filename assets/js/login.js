// =====================
// Seleção de elementos
// =====================

// Seleciona o formulário pelo ID 'login-form' no HTML
const form = document.getElementById("login-form");


// =====================
// Funções de validação
// =====================

// Função para validar se o email está no formato correto
function validateEmail(email) {
  // Expressão regular que verifica o formato básico de um email
  // Deve conter: texto@dominio.extensao
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  // Converte o email para string minúscula e testa com a regex
  return re.test(String(email).toLowerCase());
}

// Função para validar a senha
function validatePassword(password) {
  // Verifica se a senha tem pelo menos 6 caracteres
  return password.length >= 6;
}

// Função que valida o formulário completo (email + senha)
function validateForm(email, password) {
  // Se algum campo estiver vazio, exibe erro no console e retorna false
  if (!email || !password) {
    console.error("Email e senha são obrigatórios.");
    return false;
  }
  // Retorna true apenas se o email e a senha forem válidos
  return validateEmail(email) && validatePassword(password);
}


// =====================
// Funções de manipulação de campos
// =====================

// Limpa os campos de input após envio
function inputClear() {
  document.getElementById("email").value = "";
  document.getElementById("password").value = "";
}

// Exibe mensagem de erro abaixo do formulário
function errorMessage() {
  const errorSpan = document.getElementById("login-error");
  const message = "Login ou senha inválido!";
  if (errorSpan) {
    errorSpan.textContent = message;
    errorSpan.classList.remove("hidden"); // Mostra o elemento de erro
  }
}

// Limpa a mensagem de erro exibida
function clearErrorMessage() {
  const errorSpan = document.getElementById("login-error");
  if (errorSpan) {
    errorSpan.textContent = "";
    errorSpan.classList.add("hidden"); // Esconde o elemento de erro
  }
}

// Marca o campo com erro visualmente e foca nele
function inputFocus(field) {
  if (field) {
    field.classList.add("input-error"); // Adiciona estilo de erro no input
    field.focus(); // Coloca o cursor no campo com erro
    errorMessage(); // Mostra a mensagem de erro
  }
}

// Remove o estado de erro quando o usuário começa a digitar no campo
function removeInputError(field) {
  field.addEventListener("input", () => {
    field.classList.remove("input-error"); // Remove a classe de erro
    clearErrorMessage(); // Limpa a mensagem de erro
  });
}


// =====================
// Função de requisição (Fetch)
// =====================

function sendRequest(userObject) {
  // Envia os dados para o servidor usando Fetch API
  fetch("./../includes/login2.php", {
    method: "POST", // Método de envio
    headers: {
      "Content-Type": "application/json", // Especifica o tipo do conteúdo enviado
    },
    body: JSON.stringify(userObject), // Converte o objeto JS para JSON
  })
    // Converte a resposta recebida do servidor em objeto JS
    .then((response) => response.json())
    // Manipula o resultado recebido
    .then((result) => {
      console.log(result); // Exibe no console para depuração

      // Se o login foi bem-sucedido
      if (result.status === "success") {
        inputClear(); // Limpa os campos
        window.location.replace("teste.html"); // Redireciona o usuário
        return;       // Interrompe a execução da função
      }

      // Caso contrário, identifica o campo com erro
      const id = result.invalid_field; // 'email' ou 'password'
      const field = document.getElementById(id); // Seleciona o campo pelo ID

      // Marca e foca no campo com erro
      inputFocus(field);
      // Remove a marcação assim que o usuário começar a digitar novamente
      removeInputError(field);
    })
    // Captura erros de rede ou falhas na requisição
    .catch((error) => {
      console.error("Erro:", error);
    });
}


// =====================
// Função principal de login
// =====================

function login(formElement) {
  // Verifica se o formulário existe na página
  if (formElement) {
    // Adiciona um ouvinte de evento de "submit"
    formElement.addEventListener("submit", (e) => {
      e.preventDefault(); // Impede o recarregamento da página

      // Captura os valores digitados pelo usuário
      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;
      const field_email = document.getElementById("email");
      const field_password = document.getElementById("email"); // NOTE: está pegando email duas vezes, mas não alterei nada

      // Valida os dados do formulário antes de enviar
      if (!validateForm(email, password)) {

        // Se o email estiver inválido, foca no campo
        if(!validateEmail(email)){
          inputFocus(field_email);
        }

        // Remove erro quando o usuário começa a digitar novamente
        if(field_email.classList.contains("input-error")){
          removeInputError(field_email);
        }

        // Se a senha estiver inválida, foca no campo
        if(!validatePassword(field_password)){
          inputFocus(field_password);
        }
        
        if(field_password.classList.contains("input-error")){
          removeInputError(field_password);
        }
      }

      // Monta o objeto com os dados para enviar ao servidor
      const user = {
        email: email,       // Email digitado
        password: password, // Senha digitada
        action: "login",    // Ação a ser executada no backend
      };

      // Chama a função que envia a requisição
      sendRequest(user);
    });
  }
}


// =====================
// Inicialização
// =====================

// Inicializa o comportamento de login passando o formulário
login(form);