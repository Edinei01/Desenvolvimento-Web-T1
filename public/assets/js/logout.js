    // ============================
    // 5️⃣ Logout
    // ============================
    const initLogout = () => {
        const contactsPg = document.getElementById('page-contact');
        if (!contactsPg) return;

        const btnLogOut = contactsPg.querySelector('#btn-sair');
        if (!btnLogOut) return;
        
        btnLogOut.addEventListener("click", async (e) => {
            e.preventDefault();

            fetch('./../../app/controllers/AuthController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'logout' })
            })
            .then(response => response.json()) 
            .then(data => {
                console.log(JSON.stringify(data, null, 2));
                if (data.status === 'success') {
                    // 🔥 REMOVE SESSÃO LOCAL
                    localStorage.removeItem("session");

                    // 🔥 DISPARA EVENTO DE LOGOUT PARA TODAS ABAS
                    localStorage.setItem("logout", Date.now());

                    window.location.href = './../../public/index.php';
                } else {
                    alertMsg('Falha no logout: ' + (data.message || 'erro desconhecido'));
                }
            })
            .catch(err => {
                console.error('Erro no fetch de logout:', err);
                alertMsg('Erro ao tentar deslogar. Veja console.');
            });
        });
    };

    initLogout();
