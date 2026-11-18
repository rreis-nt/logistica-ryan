-- Dados de exemplo (opcional) para testar e gravar o vídeo. Execute depois do schema.sql.
USE gerenciador_logistico;

INSERT INTO produtos (nome, descricao, peso_kg, valor) VALUES
 ('Caixa de Parafusos', 'Caixa com 100 unidades', 2.500, 45.90),
 ('Furadeira Industrial', '650W com maleta', 1.800, 299.00),
 ('Tinta Acrílica 18L', 'Branco fosco', 22.000, 389.90);

INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES
 ('Depósito Central', '85980000', 'Rua das Flores', '100', NULL, 'Centro', 'Guaíra', 'PR'),
 ('Loja Curitiba', '80010000', 'Avenida Brasil', '500', 'Sala 2', 'Centro', 'Curitiba', 'PR');

INSERT INTO transportadores (nome, documento, telefone, email, veiculo, placa) VALUES
 ('Rápido Transportes', '12345678000190', '44999991234', 'contato@rapido.com', 'Caminhão baú', 'ABC1D23');
