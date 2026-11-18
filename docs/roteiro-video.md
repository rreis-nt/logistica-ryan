# Roteiro do vídeo (5 a 10 minutos) — Gerenciador Logístico

> Preencha os dados entre [colchetes] com os SEUS dados. Fale olhando para a câmera e mostre o seu rosto.
> Dica: antes de gravar, importe schema.sql e dados_exemplo.sql, abra o XAMPP (Apache + MySQL) e deixe o navegador em http://localhost/gerenciador-logistico/

## 0:00 – 0:45 | Apresentação (aparecendo no vídeo)
"Olá! Meu nome é [SEU NOME COMPLETO], meu RA é [SEU RA], sou do curso de [SEU CURSO], do polo [SEU POLO], e sou aluno da UNIPAR EAD.
Este vídeo apresenta a Atividade Prática Orientada de Fundamentos de Programação para Internet: um gerenciador logístico feito em PHP."

## 0:45 – 2:00 | Visão geral e Figma/telas
- Explique: "O sistema cadastra produtos, endereços (origem e destino) e transportadores, e gera um frete em que um transportador leva vários produtos."
- Mostre o PDF das telas (docs/telas-gerenciador-logistico.pdf) / Figma e cite: Bootstrap 5, menu de navegação, campos obrigatórios com *, confirmação ao excluir.

## 2:00 – 5:30 | Demonstração (PRECISA preencher um formulário e gerar um frete)
1. Início: mostre o painel e o menu.
2. Produtos > Novo produto: preencha nome, peso, valor e salve. (Mostre a validação deixando um campo vazio.)
3. Endereços > Novo endereço: preencha o CEP (mostre o preenchimento automático), cadastre origem e depois destino.
4. Transportadores > Novo transportador: nome, CNPJ, telefone, veículo e placa (mostre as máscaras).
5. Fretes > Gerar frete: escolha transportador, origem, destino, data e valor.
   - Clique em "+ Adicionar produto" para incluir vários produtos (campos dinâmicos com JavaScript) e mostre o peso total e valor da carga mudando.
   - Clique em "Gerar frete" e mostre a tela de detalhe.
6. Fretes: mostre a lista, o filtro por data e a edição de um frete.
7. Relatórios: escolha a data, clique em "Baixar PDF" e "Baixar CSV" e abra os arquivos.

## 5:30 – 8:30 | Comparação com o Figma e código
- Compare 2 ou 3 telas do sistema com o esboço do PDF (início, formulário de frete, relatório).
- Mostre o código no VS Code e explique por alto:
  - config/database.php: conexão PDO com o MySQL.
  - includes/functions.php: funções auxiliares, menu (render_header) e fretes_resumo().
  - produtos/, enderecos/, transportadores/: index.php (lista), form.php (cadastro/edição), excluir.php.
  - fretes/form.php: validação e gravação do frete e dos itens (transação) + assets/js/frete.js: campos dinâmicos.
  - relatorios/download.php + includes/pdf.php: geração do PDF e do CSV em PHP.
  - database/schema.sql: tabelas e relação 1 para N (fretes e frete_itens).

## 8:30 – 9:30 | Git e GitHub
- Mostre o repositório no GitHub (público), o README.md e o histórico de commits.

## 9:30 – 10:00 | Encerramento
"Esse foi o meu gerenciador logístico. Obrigado!"

## Checklist de entrega
- [ ] Vídeo entre 5 e 10 minutos
- [ ] Você aparece e fala nome, RA, curso, polo e que é aluno da UNIPAR EAD
- [ ] Preencheu um formulário e gerou um frete
- [ ] Comparou as telas com o Figma e mostrou o código
- [ ] Postado no YouTube como "Não listado" e link enviado no ambiente virtual
- [ ] Link do repositório público do GitHub enviado
- [ ] PDF das telas enviado (Etapa 1)
