<?php

// -----------------------------------------------------
// 1. SIMULAÇÃO DE REGISTRO DE NOVO USUÁRIO (Criando o Hash)
// -----------------------------------------------------
echo "## 📝 Simulação de Registro (Hashing) \n";

// A senha em texto claro que o usuário escolhe
$senha_nova = "SenhaForte123@";

// Gera um hash seguro. 
// O PHP usa um algoritmo forte (Bcrypt) e adiciona um salt automaticamente.
$hash_para_armazenar = password_hash($senha_nova, PASSWORD_DEFAULT);

echo "Senha Original: **$senha_nova** \n";
echo "Hash Gerado (para o Banco de Dados): \n";
echo "**$hash_para_armazenar** \n";

// O hash muda a cada execução, mas representa a mesma senha.
echo "--- \n";


// -----------------------------------------------------
// 2. SIMULAÇÃO DE LOGIN/AUTENTICAÇÃO (Verificando o Hash)
// -----------------------------------------------------
echo "## 🔑 Simulação de Login (Verificação) \n";

// --- Teste de Senha Correta ---
$tentativa_correta = "SenhaForte123@";
echo "Tentativa 1 (Correta): '$tentativa_correta' \n";

// A função password_verify() compara a senha digitada com o hash armazenado.
// Se elas corresponderem, ela retorna TRUE.
if (password_verify($tentativa_correta, $hash_para_armazenar)) {
    echo "✅ **Autenticação Bem-Sucedida!** (Senha correta) \n";
} else {
    echo "❌ Autenticação Falhou! (Erro inesperado) \n";
}

echo "--- \n";

// --- Teste de Senha Incorreta ---
$tentativa_incorreta = "senha_errada";
echo "Tentativa 2 (Incorreta): '$tentativa_incorreta' \n";

// Se as senhas não coincidirem, a função retorna FALSE.
if (password_verify($tentativa_incorreta, $hash_para_armazenar)) {
    echo "❌ Autenticação Falhou! (Erro de segurança) \n";
} else {
    echo "✅ **Autenticação Falhou!** (Senha incorreta, comportamento esperado) \n";
}

echo "--- \n";


// -----------------------------------------------------
// 3. RECOMENDAÇÃO DE SEGURANÇA (Verificação de Atualização)
// -----------------------------------------------------
echo "## 🛡️ Verificação de Upgrade (Melhor Prática) \n";

// É uma boa prática verificar se o algoritmo de hashing precisa de uma atualização.
// Se o PHP decidir usar um algoritmo mais novo/melhor no futuro, você pode atualizar
// o hash no banco de dados do usuário quando ele fizer login.
if (password_needs_rehash($hash_para_armazenar, PASSWORD_DEFAULT)) {
    echo "⚠️ O hash precisa ser atualizado (re-hash). Você deve criar e salvar um novo hash para este usuário. \n";
    // $novo_hash = password_hash($tentativa_correta, PASSWORD_DEFAULT);
    // // Código para salvar $novo_hash no banco de dados.
} else {
    echo "✅ O hash está atualizado e seguro. \n";
}

?>