// Seleciona elementos
const form = document.getElementById('login-form');
const email = document.getElementById('email');
const password = document.getElementById('password');
const errorSpan = document.getElementById('login-error');

function errorMessage(message) {
    if (errorSpan) {
        errorSpan.textContent = message;
        errorSpan.classList.remove('hidden');
    }
}

function clearErrorMessage() {
    if (errorSpan) {
        errorSpan.textContent = '';
        errorSpan.classList.add('hidden');
    }
}

function clearInputFields() {
    if (email) email.value = '';
    if (password) password.value = '';
}

function inputFocus(field) {
    if (field) {
        field.classList.add('input-error');
        field.focus();
    }
}

function removeInputError(field) {
    field.addEventListener('input', () => field.classList.remove('input-error'));
}

// 🔹 Verifica sessão existente
async function existsSession() {
    try {
        const response = await fetch('../includes/check_session.php');
        const result = await response.json();
        if (result.logged_in) {
            window.location.href = '../pages/teste.html';
            return true;
        }
        return false;
    } catch (err) {
        console.error('Erro ao verificar sessão:', err);
        return false;
    }
}

// 🔹 Função para enviar login
function loginUser() {
    const user_data = {
        action: 'login', // ESSENCIAL
        email: email.value,
        password: password.value
    };

    removeInputError(email);
    removeInputError(password);

    fetch('../includes/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(user_data)
    })
    .then(res => res.json())
    .then(result => {
        console.log('Resposta da API:', result);

        if (result.success) {
            clearErrorMessage();
            clearInputFields();
            window.location.href = '../pages/teste.html';
            return;
        }

        if (result.invalid_field) {
            const field = document.getElementById(result.invalid_field);
            inputFocus(field);
            errorMessage('Login ou senha inválidos!');
        } else {
            errorMessage(result.message || 'Erro ao logar.');
        }
    })
    .catch(err => {
        console.error('Erro na requisição:', err);
        alert('Erro ao conectar com a API.');
    });
}

// 🔹 Inicializa quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', async () => {
    if (!form) return;

    const loggedIn = await existsSession();
    if (!loggedIn) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            loginUser();
        });
    }
});
