-- SupVet: criação e carga inicial. Execute com psql conectado ao banco postgres.
-- Não apaga bancos existentes. Cada tabela recebe três registros de demonstração.
\set ON_ERROR_STOP on
SELECT 'CREATE DATABASE supvet_db' WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'supvet_db') \gexec
\connect supvet_db
BEGIN;
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(128) NOT NULL CHECK (btrim(nome) <> ''),
    email VARCHAR(128) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL CHECK (perfil IN ('admin', 'veterinario', 'recepcionista')),
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS tutores (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(128) NOT NULL CHECK (btrim(nome) <> ''),
    cpf_criptografado TEXT NOT NULL,
    telefone VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS pets (
    id SERIAL PRIMARY KEY,
    tutor_id INTEGER NOT NULL REFERENCES tutores(id) ON DELETE RESTRICT,
    nome VARCHAR(128) NOT NULL CHECK (btrim(nome) <> ''),
    especie VARCHAR(128) NOT NULL CHECK (btrim(especie) <> ''),
    raca VARCHAR(128) NOT NULL CHECK (btrim(raca) <> ''),
    data_nascimento DATE NOT NULL,
    cadastrado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS agendamentos (
    id SERIAL PRIMARY KEY,
    pet_id INTEGER NOT NULL REFERENCES pets(id) ON DELETE RESTRICT,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME NOT NULL,
    servico VARCHAR(128) NOT NULL CHECK (btrim(servico) <> ''),
    status VARCHAR(20) NOT NULL DEFAULT 'AGENDADO' CHECK (status IN ('AGENDADO','CONCLUIDO','CANCELADO')),
    observacoes TEXT,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS pets_tutor_idx ON pets(tutor_id);
CREATE INDEX IF NOT EXISTS agenda_pet_idx ON agendamentos(pet_id);
CREATE INDEX IF NOT EXISTS agenda_usuario_idx ON agendamentos(usuario_id);
CREATE INDEX IF NOT EXISTS agenda_data_idx ON agendamentos(data_agendamento, hora_agendamento);
-- Senha de demonstração para os três usuários: 123456 (hash Argon2id).
INSERT INTO usuarios (id,nome,email,senha_hash,perfil) VALUES
(1,'Dra. Camila Alencar','camila.vet@supvet.com','$argon2id$v=19$m=65536,t=4,p=1$WkN3NzdwZzh0bnByM0xpZQ$Q9eSDJrLdMJUJx48WL6U6oy7MnMJmU/ZLWo7MdEZU10','veterinario'),
(2,'Lucas Fernandes','lucas.recepcao@supvet.com','$argon2id$v=19$m=65536,t=4,p=1$WkN3NzdwZzh0bnByM0xpZQ$Q9eSDJrLdMJUJx48WL6U6oy7MnMJmU/ZLWo7MdEZU10','recepcionista'),
(3,'Mariana Rocha','gestao@supvet.com','$argon2id$v=19$m=65536,t=4,p=1$WkN3NzdwZzh0bnByM0xpZQ$Q9eSDJrLdMJUJx48WL6U6oy7MnMJmU/ZLWo7MdEZU10','admin') ON CONFLICT DO NOTHING;
-- CPFs fictícios cifrados em AES-256-GCM com a chave de sistema/config/database.ini.
INSERT INTO tutores (id,nome,cpf_criptografado,telefone,email) VALUES
(1,'Carlos Eduardo Silva','Ph2kx3RsFinhTiSH9c3fJdGy2uw7aIptUUlvVycz+Urm7fuobX+w','(11) 98765-4321','carlos.silva@email.com'),
(2,'Beatriz Souza Martins','v13/dxnOy+ZLK2ioXALakJZymcHwy7gY8ilUB+vq6rXDgToYjEfh','(11) 97654-3210','beatriz.martins@email.com'),
(3,'Fernando Henrique Lima','Zxgs11Dw5G24bhs6ha7AfDshNPJTyUKo63IEQgp7IMZUYpotygeL','(19) 99123-4567','fernando.lima@email.com') ON CONFLICT DO NOTHING;
INSERT INTO pets (id,tutor_id,nome,especie,raca,data_nascimento) VALUES
(1,1,'Thor','Canina','Golden Retriever','2021-05-10'),
(2,2,'Mia','Felina','Siamês','2022-08-15'),
(3,3,'Bob','Canina','Bulldog Francês','2020-02-20') ON CONFLICT DO NOTHING;
INSERT INTO agendamentos (id,pet_id,usuario_id,data_agendamento,hora_agendamento,servico,status,observacoes) VALUES
(1,1,1,'2024-11-15','09:30','Consulta geral','CONCLUIDO','Carteira de vacinação atualizada'),
(2,2,1,'2025-06-20','14:00','Profilaxia dentária','CONCLUIDO','Exames pré-operatórios normais'),
(3,3,2,'2026-12-10','10:30','Check-up preventivo','AGENDADO','Paciente alérgico a dipirona') ON CONFLICT DO NOTHING;
SELECT setval(pg_get_serial_sequence('usuarios','id'), (SELECT MAX(id) FROM usuarios));
SELECT setval(pg_get_serial_sequence('tutores','id'), (SELECT MAX(id) FROM tutores));
SELECT setval(pg_get_serial_sequence('pets','id'), (SELECT MAX(id) FROM pets));
SELECT setval(pg_get_serial_sequence('agendamentos','id'), (SELECT MAX(id) FROM agendamentos));
COMMIT;
