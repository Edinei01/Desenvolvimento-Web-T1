-- ============================================================
-- 📌 Resetar o banco de dados
-- ============================================================
DROP DATABASE IF EXISTS agendaWeb;
CREATE DATABASE IF NOT EXISTS agendaWeb;
USE agendaWeb;

-- ============================================================
-- 📌 Tabela TB_USER
-- ============================================================
-- Armazena usuários do sistema
CREATE TABLE TB_USER (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NAME VARCHAR(100) NOT NULL,
    EMAIL VARCHAR(100) UNIQUE NOT NULL,
    PASS VARCHAR(100) NOT NULL
);

-- ============================================================
-- 📌 Procedure: inserir usuário
-- ============================================================
DELIMITER //
CREATE PROCEDURE insert_user(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_pass VARCHAR(100),
    INOUT p_id INT,
    OUT p_success BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
    END;

    INSERT INTO TB_USER (NAME, EMAIL, PASS)
    VALUES (p_name, p_email, p_pass);

    SET p_id = LAST_INSERT_ID();
    SET p_success = TRUE;
END //
DELIMITER ;

-- ============================================================
-- 📌 Procedure: verificar login
-- ============================================================
DELIMITER //
CREATE PROCEDURE check_login(
    IN p_email VARCHAR(100),
    IN p_pass VARCHAR(100),
    OUT p_user_id INT,
    OUT p_success BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
    END;

    SET p_user_id = NULL;
    SET p_success = FALSE;

    SELECT ID INTO p_user_id
    FROM TB_USER
    WHERE EMAIL = p_email AND PASS = p_pass;

    IF p_user_id IS NOT NULL THEN
        SET p_success = TRUE;
    END IF;
END //
DELIMITER ;

-- ============================================================
-- 📌 Função: check_login_func
-- ============================================================
DELIMITER //
CREATE FUNCTION check_login_func(
    p_email VARCHAR(100),
    p_pass VARCHAR(100)
) 
RETURNS VARCHAR(10)
DETERMINISTIC
BEGIN
    DECLARE user_count INT;

    SELECT COUNT(*) INTO user_count
    FROM TB_USER
    WHERE EMAIL = p_email AND PASS = p_pass;

    IF user_count > 0 THEN
        RETURN 'ok';
    END IF;

    SELECT COUNT(*) INTO user_count
    FROM TB_USER
    WHERE EMAIL = p_email;

    IF user_count = 0 THEN
        RETURN 'email';
    ELSE
        RETURN 'password';
    END IF;
END //
DELIMITER ;

-- ============================================================
-- 📌 Inserção de usuários iniciais
-- ============================================================
-- Usuário 1: Edinei
INSERT INTO TB_USER (NAME, EMAIL, PASS)
VALUES ('Edinei', 'edinei@email.com', '123456');

-- Usuário 2: Admin da professora
INSERT INTO TB_USER (NAME, EMAIL, PASS)
VALUES ('Admin', 'admin@email.com', '123456');

-- ============================================================
-- 📌 Tabela TB_CONTACTS
-- ============================================================
CREATE TABLE TB_CONTACTS (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    USER_ID INT NOT NULL,
    NAME VARCHAR(100) NOT NULL,
    EMAIL VARCHAR(100),
    PHONE VARCHAR(20),
    CATEGORY ENUM('Família', 'Trabalho', 'Amigos', 'Cliente', 'Fornecedor', 'Outros') NOT NULL DEFAULT 'Outros',
    NOTES TEXT,
    CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UPDATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (USER_ID) REFERENCES TB_USER(ID) ON DELETE CASCADE
);

-- ============================================================
-- 📌 Procedure: inserir contato
-- ============================================================
DELIMITER //
CREATE PROCEDURE insert_contact(
    IN p_user_id INT,
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_category VARCHAR(50),
    IN p_notes TEXT,
    INOUT p_id INT,
    OUT p_success BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
    END;

    INSERT INTO TB_CONTACTS (USER_ID, NAME, EMAIL, PHONE, CATEGORY, NOTES)
    VALUES (p_user_id, p_name, p_email, p_phone, p_category, p_notes);

    SET p_id = LAST_INSERT_ID();
    SET p_success = TRUE;
END //
DELIMITER ;

-- ============================================================
-- 📌 Procedure: atualizar contato
-- ============================================================
DELIMITER //
CREATE PROCEDURE update_contact(
    IN p_contact_id INT,
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_category VARCHAR(50),
    IN p_notes TEXT,
    OUT p_success BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
    END;

    UPDATE TB_CONTACTS
    SET NAME = p_name,
        EMAIL = p_email,
        PHONE = p_phone,
        CATEGORY = p_category,
        NOTES = p_notes,
        UPDATED_AT = CURRENT_TIMESTAMP
    WHERE ID = p_contact_id;

    SET p_success = TRUE;
END //
DELIMITER ;

-- ============================================================
-- 📌 Procedure: listar contatos do usuário
-- ============================================================
DELIMITER //
CREATE PROCEDURE get_user_contacts(
    IN p_user_id INT
)
BEGIN
    SELECT ID, NAME, EMAIL, PHONE, CATEGORY, NOTES, CREATED_AT, UPDATED_AT
    FROM TB_CONTACTS
    WHERE USER_ID = p_user_id
    ORDER BY NAME ASC;
END //
DELIMITER ;

-- ============================================================
-- 📌 Função: verificar existência de contato
-- ============================================================
DELIMITER //
CREATE OR REPLACE FUNCTION check_contact_exists(contact_id INT)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE resultado VARCHAR(20);

    IF EXISTS(SELECT 1 FROM tb_contacts WHERE id = contact_id) THEN
        SET resultado = 'existe';
    ELSE
        SET resultado = 'nao existe';
    END IF;

    RETURN resultado;
END //
DELIMITER ;

-- ============================================================
-- 📌 Procedure: deletar contato
-- ============================================================
DELIMITER //
CREATE OR REPLACE PROCEDURE delete_contact(
    IN contact_id INT,
    OUT resultado VARCHAR(50)
)
BEGIN
    DECLARE status_check VARCHAR(20);

    -- Verifica se o contato existe
    SET status_check = check_contact_exists(contact_id);

    -- Evita conflitos de collation
    IF status_check COLLATE utf8mb4_unicode_ci = 'existe' COLLATE utf8mb4_unicode_ci THEN
        DELETE FROM tb_contacts WHERE id = contact_id;

        IF ROW_COUNT() > 0 THEN
            SET resultado = 'deletado com sucesso';
        ELSE
            SET resultado = 'falha ao deletar';
        END IF;
    ELSE
        SET resultado = 'contato nao existe';
    END IF;
END //
DELIMITER ;

-- ============================================================
-- 📌 Inserir 20 contatos fictícios para o usuário ID 1
-- ============================================================
INSERT INTO TB_CONTACTS (USER_ID, NAME, EMAIL, PHONE, CATEGORY, NOTES) VALUES
(1, 'Alice Souza', 'alice.souza@email.com', '11990001001', 'Amigos', 'Amiga de longa data'),
(1, 'Bruno Lima', 'bruno.lima@email.com', '11990001002', 'Trabalho', 'Colega de escritório'),
(1, 'Carla Mendes', 'carla.mendes@email.com', '11990001003', 'Família', 'Prima de Edinei'),
(1, 'Daniel Rocha', 'daniel.rocha@email.com', '11990001004', 'Cliente', 'Cliente importante'),
(1, 'Eduarda Alves', 'eduarda.alves@email.com', '11990001005', 'Amigos', 'Amiga da faculdade'),
(1, 'Fernando Costa', 'fernando.costa@email.com', '11990001006', 'Fornecedor', 'Fornecedor de materiais'),
(1, 'Gabriela Nunes', 'gabriela.nunes@email.com', '11990001007', 'Outros', 'Conhecida da academia'),
(1, 'Hugo Martins', 'hugo.martins@email.com', '11990001008', 'Trabalho', 'Supervisor do setor'),
(1, 'Isabela Ferreira', 'isabela.ferreira@email.com', '11990001009', 'Família', 'Irmã de Edinei'),
(1, 'João Pedro', 'joao.pedro@email.com', '11990001010', 'Cliente', 'Cliente VIP'),
(1, 'Karina Lima', 'karina.lima@email.com', '11990001011', 'Amigos', 'Colega de curso'),
(1, 'Lucas Almeida', 'lucas.almeida@email.com', '11990001012', 'Fornecedor', 'Parceiro de negócios'),
(1, 'Mariana Santos', 'mariana.santos@email.com', '11990001013', 'Outros', 'Vizinha do prédio'),
(1, 'Natália Souza', 'natalia.souza@email.com', '11990001014', 'Trabalho', 'Assistente administrativa'),
(1, 'Otávio Ribeiro', 'otavio.ribeiro@email.com', '11990001015', 'Família', 'Cunhado de Edinei'),
(1, 'Paula Carvalho', 'paula.carvalho@email.com', '11990001016', 'Amigos', 'Amiga de infância'),
(1, 'Rafael Gomes', 'rafael.gomes@email.com', '11990001017', 'Cliente', 'Cliente frequente'),
(1, 'Sabrina Lima', 'sabrina.lima@email.com', '11990001018', 'Fornecedor', 'Fornecedor de serviços'),
(1, 'Thiago Fernandes', 'thiago.fernandes@email.com', '11990001019', 'Outros', 'Conhecido do bairro'),
(1, 'Vanessa Rocha', 'vanessa.rocha@email.com', '11990001020', 'Trabalho', 'Colega de equipe');

-- ============================================================
-- 📌 Inserir 20 contatos fictícios para o usuário ID 2
-- ============================================================
INSERT INTO TB_CONTACTS (USER_ID, NAME, EMAIL, PHONE, CATEGORY, NOTES) VALUES
(2, 'Alice Souza', 'alice.souza@email.com', '11990001001', 'Amigos', 'Amiga de longa data'),
(2, 'Bruno Lima', 'bruno.lima@email.com', '11990001002', 'Trabalho', 'Colega de escritório'),
(2, 'Carla Mendes', 'carla.mendes@email.com', '11990001003', 'Família', 'Prima de Edinei'),
(2, 'Daniel Rocha', 'daniel.rocha@email.com', '11990001004', 'Cliente', 'Cliente importante'),
(2, 'Eduarda Alves', 'eduarda.alves@email.com', '11990001005', 'Amigos', 'Amiga da faculdade'),
(2, 'Fernando Costa', 'fernando.costa@email.com', '11990001006', 'Fornecedor', 'Fornecedor de materiais'),
(2, 'Gabriela Nunes', 'gabriela.nunes@email.com', '11990001007', 'Outros', 'Conhecida da academia'),
(2, 'Hugo Martins', 'hugo.martins@email.com', '11990001008', 'Trabalho', 'Supervisor do setor'),
(2, 'Isabela Ferreira', 'isabela.ferreira@email.com', '11990001009', 'Família', 'Irmã de Edinei'),
(2, 'João Pedro', 'joao.pedro@email.com', '11990001010', 'Cliente', 'Cliente VIP'),
(2, 'Karina Lima', 'karina.lima@email.com', '11990001011', 'Amigos', 'Colega de curso'),
(2, 'Lucas Almeida', 'lucas.almeida@email.com', '11990001012', 'Fornecedor', 'Parceiro de negócios'),
(2, 'Mariana Santos', 'mariana.santos@email.com', '11990001013', 'Outros', 'Vizinha do prédio'),
(2, 'Natália Souza', 'natalia.souza@email.com', '11990001014', 'Trabalho', 'Assistente administrativa'),
(2, 'Otávio Ribeiro', 'otavio.ribeiro@email.com', '11990001015', 'Família', 'Cunhado de Edinei'),
(2, 'Paula Carvalho', 'paula.carvalho@email.com', '11990001016', 'Amigos', 'Amiga de infância'),
(2, 'Rafael Gomes', 'rafael.gomes@email.com', '11990001017', 'Cliente', 'Cliente frequente'),
(2, 'Sabrina Lima', 'sabrina.lima@email.com', '11990001018', 'Fornecedor', 'Fornecedor de serviços'),
(2, 'Thiago Fernandes', 'thiago.fernandes@email.com', '11990001019', 'Outros', 'Conhecido do bairro'),
(2, 'Vanessa Rocha', 'vanessa.rocha@email.com', '11990001020', 'Trabalho', 'Colega de equipe');