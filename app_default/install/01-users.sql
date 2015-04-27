CREATE TABLE IF NOT EXISTS usuario (
    id SERIAL PRIMARY KEY,
    email VARCHAR NOT NULL UNIQUE CHECK (length(email) > 5),
    password VARCHAR(32) NOT NULL CHECK (length(password) = 32),
    nome VARCHAR NOT NULL CHECK (length(nome) > 1),
    ativo BOOL DEFAULT true,
    registro TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS grupo (
    id SERIAL PRIMARY KEY,
    nome VARCHAR NOT NULL UNIQUE,
    descricao TEXT,
    ativo BOOL DEFAULT true,
    registro TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS usuariogrupo (
    usuario INT NOT NULL REFERENCES usuario (id),
    grupo INT NOT NULL REFERENCES grupo (id),
    PRIMARY KEY(usuario, grupo)
);

CREATE TABLE IF NOT EXISTS logacesso (
    id SERIAL PRIMARY KEY,
    sessionid CHAR(32),
    ip VARCHAR(45),
    ipreverso VARCHAR,
    navigatorso VARCHAR,
    registro TIMESTAMP DEFAULT now()
);