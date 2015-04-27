CREATE TABLE IF NOT EXISTS staticpage (
    id SERIAL PRIMARY KEY,
    nome VARCHAR NOT NULL UNIQUE,
    titulo VARCHAR NOT NULL,
    conteudo TEXT,
    registro TIMESTAMP DEFAULT now()
);