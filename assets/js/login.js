// Seleciona o formulário pelo ID
const form = document.getElementById('login-form');

function errorMessage(message) {
    const errorSpan = document.getElementById('login-error');
    if (errorSpan) {
        errorSpan.textContent = message;
        errorSpan.classList.remove('hidden');
    }  
}

function clearErrorMessage() {
    const errorSpan = document.getElementById('login-error');
    if (errorSpan) {
        errorSpan.textContent = '';
        errorSpan.classList.add('hidden');
    }
}

function clearInputFields() {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    if (email) email.value = '';
    if (password) password.value = '';
}

function inputFocus(field) {
    if (field) {
        field.classList.add('input-error');
        field.focus();
    }
}

// / Remove o erro quando o usuário começa a digitar
function removeInputError(field) {
    field.addEventListener('input', () => {
        field.classList.remove('input-error');
    });
}


document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('./../includes/check_session.php'); // arquivo PHP que verifica sessão
        const result = await response.json();

        if (result.logged_in) {
            // Se já estiver logado, redireciona direto
            window.location.href = '../pages/teste.html';
        }
        // Se não estiver logado, continua na página de login
    } catch (err) {
        console.error('Erro ao verificar sessão:', err);
    }
});


if (form) {

    form.addEventListener('submit', (e) => {

        e.preventDefault();

        const user_data = {
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        }

        removeInputError(email);
        removeInputError(password);

        fetch('./../includes/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(user_data)
            })
            .then(response => response.json())
            .then(result => {
                console.log('Resposta da API:', result);
                if (result.success) {
                    clearErrorMessage();
                    clearInputFields();
                    window.location.href = 'teste.html';
                }

                if (result.invalid_field) {
                    const field = document.getElementById(result.invalid_field);
                    inputFocus(field);
                    errorMessage(`Login ou senha inválido!`);
                }else {
                    clearErrorMessage();
                }
                
            })
            .catch(err => {
                console.error('Erro na requisição:', err);
                alert('Erro ao conectar com a API.');
            })   
    });
}