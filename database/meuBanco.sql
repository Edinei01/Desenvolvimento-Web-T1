-- 🔹 1) Resetar o banco de dados
DROP DATABASE IF EXISTS meubanco;
CREATE DATABASE IF NOT EXISTS meubanco;
USE meubanco;

-- 🔹 2) Criar tabela TB_USER
CREATE TABLE TB_USER(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NAME VARCHAR(100),
    EMAIL VARCHAR(100),
    PASS VARCHAR(100)
);

-- 🔹 3) Criar procedure para inserir usuário e retornar ID + sucesso
DELIMITER //

CREATE PROCEDURE insert_user(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_pass VARCHAR(100),
    INOUT p_id INT,
    OUT SUCCESS BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET SUCCESS = FALSE;
    END;

    -- Inserção
    INSERT INTO TB_USER (NAME, EMAIL, PASS)
    VALUES (p_name, p_email, p_pass);

    -- Retorna o ID gerado
    SET p_id = LAST_INSERT_ID();

    -- Indica sucesso
    SET SUCCESS = TRUE;
END //

DELIMITER ;
-- 🔹 5) Criar procedure para verificar login
DELIMITER //
CREATE PROCEDURE check_login(
    IN p_email VARCHAR(100),
    IN p_pass VARCHAR(100),
    OUT p_user_id INT,
    OUT SUCCESS BOOLEAN
)

BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET SUCCESS = FALSE;
    END;

    -- Inicializa variáveis
    SET p_user_id = NULL;
    SET SUCCESS = FALSE;

    -- Verifica se o usuário existe com o email e senha fornecidos
    SELECT ID INTO p_user_id
    FROM TB_USER
    WHERE EMAIL = p_email AND PASS = p_pass;

    -- Se encontrou um usuário, indica sucesso
    IF p_user_id IS NOT NULL THEN
        SET SUCCESS = TRUE;
    END IF;
END //

DELIMITER ;

-- 🔹 4) Exemplo de uso da procedure
-- Declarar variáveis
SET @p_id = 0;
SET @success = FALSE;

-- Chamar a procedure
CALL insert_user('Edinei', 'edinei@email.com', '123456', @p_id, @success);

-- Verificar os valores retornados
SELECT @p_id AS user_id, @success AS sucesso;


-- -- 🔹 6) Exemplo de uso da procedure de login
-- -- Declarar variáveis
-- SET @p_user_id = NULL;
-- SET @success = FALSE;
-- -- Chamar a procedure
-- CALL check_login('SDSDSD','ASASASSA', @p_user_id, @success);
-- -- Verificar os valores retornados
-- SELECT @p_user_id AS user_idLOG, @success AS sucesso;
-- CALL check_login('edinei@email.com','123456', @p_user_id, @success);
-- SELECT @p_user_id AS user_idLOGINBCT, @success AS sucesso;