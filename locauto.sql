
CREATE TABLE equipamento (
    codigoEquipamento INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    peso VARCHAR(100),
    valorQuinzenal FLOAT,
    valorMensal FLOAT,
    valorCaucao FLOAT
);


CREATE TABLE cliente (
    cpf VARCHAR(11) PRIMARY KEY,
    nome VARCHAR(50),
    telefone VARCHAR(11),
    email VARCHAR(50),
    cep VARCHAR(8),
    endereco VARCHAR(100),
    numero INT,
    complemento VARCHAR(100)
);



CREATE TABLE reservas (
    numero INT AUTO_INCREMENT PRIMARY KEY,
    saida DATETIME NOT NULL,
    retorno DATETIME,
    preco FLOAT NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    codigoEquipamento INT NOT NULL,
    valorMensal FLOAT,
    valorQuinzenal FLOAT,
    tipoLocacao VARCHAR(50),
    reservaFinalizada TINYINT(1),
    dataFinalizada DATETIME,

    FOREIGN KEY (cpf) REFERENCES cliente(cpf),
    FOREIGN KEY (codigoEquipamento) REFERENCES equipamento(codigoEquipamento)
);


CREATE TABLE renovacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeroReserva INT NOT NULL,
    dataRenovacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    novaDataDevolucao DATETIME NOT NULL,
    tipoLocacao VARCHAR(50),
    valorRenovacao FLOAT NOT NULL,

    FOREIGN KEY (numeroReserva) REFERENCES reservas(numero)
);


CREATE TABLE cobrancas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numeroReserva INT NOT NULL,
    dataCobranca DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    formaPagamento VARCHAR(20) NOT NULL,  -- Ex: 'dinheiro', 'cartao', 'pix', 'cheque'
    valorTotal FLOAT NOT NULL,
    observacoes TEXT,
    quitado TINYINT(1) DEFAULT 0, -- 0 = não pago, 1 = pago
    dataRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (numeroReserva) REFERENCES reservas(numero)
);



INSERT INTO cliente VALUES
('37372120829', 'Fernando Melo', 'fernando@credsystem.com', 05328000, 'av antonio', 200, 'apto 14', '1989-03-11'),
('47474125478', 'antonio silva', 'antonio@credsystem.com', 05328000, 'av peregrino', 400, 'apto 21', '1974-07-01'),
('57474125479', 'Valdir Melo', 'val@credsystem.com', 05328000, 'rua feliz', 2040, 'apto 47', '1999-04-07'),
('67474125410', 'Valdir lucas', 'lucas@credsystem.com', 05328000, 'av jose', 74, 'apto 88', '2000-01-31');


insert into equipamento values(1,"Jaquaribe","1009","100kg",1500.00);
insert into equipamento values(2,"Jaquaribe","1016","110kg",2000.00);
insert into equipamento values(3,"Praxis","dobravel aluminio","100kg",2500.00);
insert into equipamento values(4,"jaguaribe","aluminio","100kg",200.00);

INSERT INTO reservas (saida, retorno, preco, cpf, codigoEquipamento) 
VALUES ('2025-03-08', '2025-04-08', 55.00, '37372120829', 1);

INSERT INTO reservas (saida, retorno, preco, cpf, codigoEquipamento) 
VALUES ('2025-03-08', '2025-04-08', 65.00, '47474125478', 2);

INSERT INTO reservas (saida, preco, cpf, codigoEquipamento) 
VALUES ('2025-03-08', 75.00, '57474125479', 2);

---------------------------------------------------------------




	