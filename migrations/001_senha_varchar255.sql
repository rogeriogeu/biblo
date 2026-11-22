-- Executar uma vez no banco bd_saul: permite armazenar hashes de senha (password_hash).
ALTER TABLE usuario MODIFY senha VARCHAR(255) NOT NULL;
