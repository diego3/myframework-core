CREATE TABLE IF NOT EXISTS logbusca (
    id SERIAL PRIMARY KEY,
    termo VARCHAR NOT NULL,
    totalresultado INT,
    query VARCHAR,
    sessionid VARCHAR(32), --Identificador unico da sessao (quando não há usuários não logados)
    usuario INT REFERENCES usuario (id), --codigo do usuario (para usuário logado no sistema)
    registro TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS cliquebusca (
    id SERIAL PRIMARY KEY,
    logbusca INT NOT NULL REFERENCES logbusca (id),
    url VARCHAR,
    registro TIMESTAMP DEFAULT now()
);


--indexador