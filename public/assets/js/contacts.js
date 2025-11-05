document.addEventListener('DOMContentLoaded', () => {

    // ============================
    // FUNÇÕES UTILITÁRIAS
    // ============================
    const fetchJSON = (url, options = {}) =>
        fetch(url, options).then(res => res.json());

    const alertMsg = (msg) => alert(msg);

    // ============================
    // 1️⃣ Adicionar Contato (add_contact.php)
    // ============================
    const initAddContact = () => {
    const formAddContacts = document.getElementById('add-contact-form');
    const btnAdd = document.getElementById('btn-add');
    if (!formAddContacts || !btnAdd) return;

    const name = formAddContacts.querySelector('input#name');
    const email = formAddContacts.querySelector('input#email');
    const phone = formAddContacts.querySelector('input#phone');
    const category = formAddContacts.querySelector('select');
    const notes = formAddContacts.querySelector('textarea');

    btnAdd.addEventListener('click', (e) => {
        e.preventDefault();

        // Objeto do contato
        const contact = {
            name: name.value.trim(),
            email: email.value.trim(),
            phone: phone.value.trim(),
            category: category.value,
            notes: notes.value.trim()
        };

       
        const data = {
            action: "addContact",  
            contact: contact        
        };

        
        fetchJSON("./../../app/controllers/ContactController.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
        })
        .then(data => {
            if (data.status === 'success') {
                alertMsg("Contato enviado com sucesso!");
                name.value = "";
                email.value = "";
                phone.value = "";
                category.value = "";
                notes.value = "";
                name.focus();
            } else {
                alertMsg("Erro: " + (data.message || "Erro desconhecido"));
            }
        })
        .catch(err => console.error("Erro na requisição: ", err));
        });
    };


    // ============================
    // 2️⃣ Listagem de contatos (contacts.php)
    // ============================
    const loadContactsList = () => {
        const contactsList = document.getElementById('contacts-list');
        if (!contactsList) return;

        fetchJSON("./../../app/controllers/ContactController.php", { 
            method: "POST", 
            headers: { "Content-Type": "application/json" }, body: JSON.stringify({ action: "listContacts" }) })
            .then(data => {
                contactsList.innerHTML = "";
                if (data.status === "success" && data.data.length > 0) {
                    data.data.forEach(contact => {
                        const row = document.createElement("tr");
                        row.dataset.contactId = contact.ID; 
                        row.innerHTML = `
                            <td><span>${contact.NAME}</span></td>
                            <td><span>${contact.EMAIL || "-"}</span></td>
                            <td><span>${contact.PHONE || "-"}</span></td>
                            <td class="actions">
                                <a href="view_contact.php?id=${contact.ID}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="edit_contact.php?id=${contact.ID}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <button class="btn btn-sm btn-danger btn-delete">
                                    <i class="bi bi-trash"></i> Deletar
                                </button>
                            </td>
                        `;
                        contactsList.appendChild(row);
                    });
                    initDeleteButtons(); 
                } else {
                    contactsList.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Nenhum contato encontrado.</td></tr>`;
                }
            })
            .catch(err => {
                console.error("Erro ao carregar contatos:", err);
                contactsList.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar contatos.</td></tr>`;
            });
    };

    // ============================
    // 3️⃣ Deletar Contato
    // ============================
    const initDeleteButtons = () => {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async () => {
                const contactRow = btn.closest('tr');
                const contactId = contactRow.dataset.contactId;

                if (!contactId) return;
                if (!confirm("Tem certeza que deseja deletar este contato?")) return;

                try {
                    const data = await fetchJSON('../includes/contacts/delete_contact.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: contactId })
                    });

                    if (data.status === 'success') {
                        alertMsg(data.message || "Contato deletado com sucesso!");
                        // Remove a linha sem precisar recarregar toda a lista
                        contactRow.remove();
                    } else {
                        alertMsg("Erro ao deletar: " + (data.message || "Erro desconhecido"));
                    }
                } catch (err) {
                    console.error("Erro ao deletar contato:", err);
                    alertMsg("Erro ao deletar contato. Veja console.");
                }
            });
        });
    };

    // ============================
    // 4️⃣ Editar Contato (edit_contact.php)
    // ============================
    const initEditContact = () => {
        const editForm = document.getElementById('edit-contact-form');
        if (!editForm) return;

        const contactId = editForm.dataset.contactId;
        const nameInput = editForm.querySelector('#name');
        const emailInput = editForm.querySelector('#email');
        const phoneInput = editForm.querySelector('#phone');
        const categoryInput = editForm.querySelector('#category');
        const notesInput = editForm.querySelector('#notes');
        
        // Buscar dados do contato
        // fetchJSON(`../includes/contacts/get_contacts.php?id=${contactId}`)

        fetchJSON(`./../../app/controllers/ContactController.php`,{
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'getContact', id: contactId })
            }
        )
            .then(data => {

                // console.log("contactId = "+contactId);
                // alertMsg(JSON.stringify(data));
                // console.log(JSON.stringify(data, null, 2));
                if (data.status === 'success') {
                    // alertMsg("Contato carregado com sucesso! qwert");
                    contact = data.data;
                    // const contact = data.data.find(c => String(c.ID) == String(contactId));
                    // console.log("Contato encontrado:"+ contact);
                    if (contact) {
                        nameInput.value = contact.NAME || "";
                        emailInput.value = contact.EMAIL || "";
                        phoneInput.value = contact.PHONE || "";
                        categoryInput.value = contact.CATEGORY || "Outros";
                        notesInput.value = contact.NOTES || "";
                    } else {
                        alertMsg("Contato não encontrado!");
                    }
                } else {
                    alertMsg('Erro ao carregar contato: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(err => console.error('Erro ao buscar contato:', err));

        editForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const updatedContact = {
                id: contactId,
                name: nameInput.value,
                email: emailInput.value,
                phone: phoneInput.value,
                category: categoryInput.value,
                notes: notesInput.value
            };

            // alert('vou testar aqui o category-> '+categoryInput.value);
            // console.log("categoryInput.value = "+categoryInput.value);

            // alertMsg("Atualizando contato...");
            // console.log("aqui->"+JSON.stringify(updatedContact, null, 2));

            fetchJSON('./../../app/controllers/ContactController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({action: 'editContact', data: updatedContact})
            }).then(data => {

                // alertMsg('Contato atualizado com sucesso!');
                // console.log(JSON.stringify(data, null, 2));
                // console.log("Resposta do servidor:\n" + JSON.stringify(data, null, 2));

                if (data.status === 'success') {
                    // alertMsg('Contato atualizado com sucesso!');
                    alertMsg('Contato atualizado com sucesso!');
                    console.log("Resposta do servidor:\n" + JSON.stringify(data, null, 2));
                    window.location.href = 'contacts.php';
                } else {
                    alertMsg('Erro ao atualizar: ' + (data.message || 'Erro desconhecido'));
                }
            }).catch(err => {
                console.error('Erro ao atualizar contato:', err)
                console.log(err);
            });
        });
    };

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

            try {
                const data = await fetchJSON('./../../app/controllers/UserController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });

                if (data.status === 'success') 
                    {
                    window.location.href = './../../public/index.php';
                } else {
                    alertMsg('Falha no logout: ' + (data.message || 'erro desconhecido'));
                }
            } catch (err) {
                console.error('Erro no fetch de logout:', err);
                alertMsg('Erro ao tentar deslogar. Veja console.');
            }
        });
    };

    // ============================
    // 6️⃣ Visualizar Contato (view_contact.php)
    // ============================
    const initViewContact = () => {
        const view = document.getElementById('page-view');
        if (!view) return;

        const contactInfoEl = view.querySelector('#contact-info');
        if (!contactInfoEl) return;

        const contactId = contactInfoEl.dataset.contactId;
        if (!contactId) return;
        
        // Faz POST para o controller pedindo o contato específico
        fetchJSON('./../../app/controllers/ContactController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'viewContact', id: contactId })
        })
        .then(data => {
            if (data.status !== 'success') {
                console.log(JSON.stringify(data));
                alertMsg('Erro ao carregar contato: ' + (data.message || 'Erro desconhecido'));
                return;
            }

            // data.data pode ser um objeto único ou um array de objetos
            let contact = null;
            if (Array.isArray(data.data)) {
                contact = data.data.find(c => String(c.ID) === String(contactId)) || data.data[0];
            } else if (data.data && typeof data.data === 'object') {
                contact = data.data;
            }

            if (!contact) {
                alertMsg('Contato não encontrado!');
                return;
            }

            const setField = (fieldId, value) => {
                // tente primeiro '#id .contact-text', senão caia para '#id'
                let fieldEl = view.querySelector(`#${fieldId} .contact-text`);
                if (!fieldEl) fieldEl = view.querySelector(`#${fieldId}`);
                if (fieldEl) fieldEl.textContent = (value != null && value !== '') ? value : '-';
            };

            setField('name', contact.NAME || contact.name || '-');
            setField('email', contact.EMAIL || contact.email || '-');
            setField('phone', contact.PHONE || contact.phone || '-');
            setField('category', contact.CATEGORY || contact.category || 'Outros');
            setField('notes', contact.NOTES || contact.notes || '-');
        })
        .catch(err => {
            console.error('Erro no fetch de View Contact:', err);
            alertMsg('Erro ao tentar buscar contato. Veja console.');
        });
    };


    // ============================
    // 7️⃣ Filtrar contatos carregados (input + botão)
    // ============================
    const initSearchContacts = () => {
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('btn-search');
        const contactsList = document.getElementById('contacts-list');
        if (!searchInput || !searchBtn || !contactsList) return;

        // Função que faz o filtro tipo "LIKE 'texto%'"
        const filterContacts = () => {
            const filter = searchInput.value.toLowerCase();

            contactsList.querySelectorAll('tr').forEach(row => {
                const cells = row.querySelectorAll('td span');
                let match = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().startsWith(filter)) {
                        match = true;
                    }
                });

                row.style.display = match ? '' : 'none';
            });
        };

        // Evento ao digitar
        searchInput.addEventListener('input', filterContacts);

        // Evento ao clicar no botão
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault(); // evita comportamento padrão se for botão de submit
            filterContacts();
        });
    };



    // ============================
    // INICIALIZAÇÃO GERAL
    // ============================
    const initApp = () => {
        initAddContact();
        loadContactsList();
        initEditContact();
        initLogout();
        initViewContact();
        initSearchContacts();
    };

    initApp();
});
