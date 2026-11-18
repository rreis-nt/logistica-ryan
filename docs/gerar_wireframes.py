# Gera docs/telas-gerenciador-logistico.pdf (esboços das telas - Etapa 1)
from reportlab.lib.pagesizes import A4, landscape
from reportlab.pdfgen import canvas
from reportlab.lib.colors import HexColor, white, black

W, H = landscape(A4)
DARK, BLUE, GREEN, RED, GRAY, LIGHT = HexColor('#212529'), HexColor('#0d6efd'), HexColor('#198754'), HexColor('#dc3545'), HexColor('#6c757d'), HexColor('#f1f3f5')
MENU = ['Início', 'Produtos', 'Endereços', 'Transportadores', 'Fretes', 'Relatórios']

c = canvas.Canvas('docs/telas-gerenciador-logistico.pdf', pagesize=landscape(A4))
c.setTitle('Esboço das telas - Gerenciador Logístico')
c.setAuthor('Projeto APO - Fundamentos de Programação para Internet')

def txt(x, y, s, size=10, bold=False, color=black):
    c.setFillColor(color); c.setFont('Helvetica-Bold' if bold else 'Helvetica', size); c.drawString(x, y, s)

def box(x, y, w, h, fill=white, stroke=GRAY, r=4):
    c.setFillColor(fill); c.setStrokeColor(stroke); c.setLineWidth(.8); c.roundRect(x, y, w, h, r, fill=1, stroke=1)

def button(x, y, w, label, color=BLUE, h=22):
    box(x, y, w, h, color, color); txt(x + 8, y + 7, label, 9, True, white)

def field(x, y, w, label, value='', h=22):
    txt(x, y + h + 4, label, 8, True, DARK); box(x, y, w, h); txt(x + 6, y + 7, value, 9, False, GRAY)

def table(x, y, w, cols, rows, widths=None, rh=22):
    widths = widths or [w / len(cols)] * len(cols)
    box(x, y - rh, w, rh, LIGHT)
    cx = x
    for col, cw in zip(cols, widths):
        txt(cx + 5, y - rh + 7, col, 8, True); cx += cw
    for i, row in enumerate(rows):
        yy = y - rh * (i + 2); box(x, yy, w, rh, white)
        cx = x
        for cell, cw in zip(row, widths):
            txt(cx + 5, yy + 7, cell, 8); cx += cw
    return y - rh * (len(rows) + 1)

def page(title, active, note, n):
    # moldura do navegador
    box(30, 30, W - 60, H - 60, white, DARK, 8)
    box(30, H - 62, W - 60, 32, LIGHT, DARK, 8)
    for i, col in enumerate([RED, HexColor('#ffc107'), GREEN]):
        c.setFillColor(col); c.circle(48 + i * 14, H - 46, 4, fill=1, stroke=0)
    box(110, H - 55, W - 200, 18, white); txt(118, H - 50, 'http://localhost/gerenciador-logistico/', 8, False, GRAY)
    # navbar
    c.setFillColor(DARK); c.rect(31, H - 100, W - 62, 38, fill=1, stroke=0)
    txt(45, H - 85, '[caminhão] Gerenciador Logístico', 11, True, white)
    x = W - 45
    for m in reversed(MENU):
        w = c.stringWidth(m, 'Helvetica-Bold' if m == active else 'Helvetica', 9) + 16
        x -= w
        txt(x + 8, H - 84, m, 9, m == active, white if m == active else HexColor('#adb5bd'))
        if m == active: c.setStrokeColor(white); c.line(x + 8, H - 89, x + w - 8, H - 89)
    txt(50, H - 130, title, 18, True, DARK)
    txt(50, 44, 'Tela %d - %s' % (n, note), 8, False, GRAY)

def cover():
    c.setFillColor(DARK); c.rect(0, 0, W, H, fill=1, stroke=0)
    txt(60, H - 140, 'Gerenciador Logístico', 34, True, white)
    txt(60, H - 175, 'Esboço das telas (Etapa 1 - Análise)', 16, False, HexColor('#adb5bd'))
    txt(60, H - 215, 'Disciplina: Fundamentos de Programação para Internet - Atividade Prática Orientada', 11, False, white)
    txt(60, H - 235, 'Framework visual: Bootstrap 5  |  Backend: PHP  |  Banco: MySQL (XAMPP)', 11, False, white)
    txt(60, H - 280, 'Mapa de navegação', 13, True, white)
    labels = [('Início', 0), ('Produtos', 1), ('Endereços', 2), ('Transportadores', 3), ('Fretes', 4), ('Relatórios', 5)]
    for i, (l, _) in enumerate(labels):
        bx = 60 + i * 125
        box(bx, H - 340, 112, 36, BLUE, BLUE); txt(bx + 10, H - 326, l, 10, True, white)
    txt(60, H - 380, 'Cada módulo de cadastro tem: Lista -> Novo/Editar -> Excluir.  Fretes: Lista -> Gerar frete -> Detalhe.', 10, False, white)
    txt(60, H - 400, 'Menu de navegação fixo no topo de todas as telas.', 10, False, white)
    txt(60, H - 440, 'Boas práticas de UX aplicadas:', 12, True, white)
    for i, t in enumerate(['Menu consistente e item ativo destacado', 'Campos obrigatórios marcados com *', 'Mensagens de erro e sucesso claras', 'Confirmação antes de excluir', 'Botão de ação principal em destaque (azul)', 'Layout responsivo (celular e desktop)']):
        txt(75, H - 462 - i * 17, '- ' + t, 10, False, white)
    c.showPage()

cover()

# 1 Início
page('Painel', 'Início', 'Painel inicial com totais e últimos fretes', 1)
for i, (l, v) in enumerate([('Produtos', '12'), ('Endereços', '8'), ('Transportadores', '5'), ('Fretes', '23')]):
    bx = 50 + i * 175; box(bx, H - 230, 160, 70); txt(bx + 12, H - 180, l, 9, False, GRAY); txt(bx + 12, H - 215, v, 24, True)
button(W - 180, H - 140, 130, '+ Gerar frete')
txt(50, H - 260, 'Últimos fretes', 11, True)
table(50, H - 268, W - 100, ['Nº', 'Data', 'Transportador', 'Rota', 'Valor do frete', ''], [['23', '16/11/2026', 'Rápido Transportes', 'Guaíra/PR -> Curitiba/PR', 'R$ 350,50', '[Ver]'], ['22', '15/11/2026', 'Translog', 'Maringá/PR -> Londrina/PR', 'R$ 210,00', '[Ver]']], [40, 90, 190, 230, 120, 60])
c.showPage()

# 2 Produtos lista
page('Produtos', 'Produtos', 'Lista de produtos', 2)
button(W - 180, H - 135, 130, '+ Novo produto')
table(50, H - 160, W - 100, ['ID', 'Nome', 'Descrição', 'Peso', 'Valor', 'Ações'], [['1', 'Caixa de Parafusos', 'Cx 100un', '2,500 kg', 'R$ 45,90', '[Editar] [Excluir]'], ['2', 'Furadeira', 'Industrial', '1,800 kg', 'R$ 299,00', '[Editar] [Excluir]']], [40, 190, 170, 90, 90, 150])
c.showPage()

# 3 Produto form
page('Novo produto', 'Produtos', 'Cadastro/edição de produto (campos: id, nome, descrição, peso, valor)', 3)
field(50, H - 210, 420, 'Nome *', 'Ex.: Caixa de Parafusos'); field(490, H - 210, 200, 'Peso (kg) *', '0,000')
field(50, H - 270, 420, 'Descrição', ''); field(490, H - 270, 200, 'Valor unitário (R$) *', '0,00')
button(50, H - 320, 80, 'Salvar', BLUE); button(140, H - 320, 80, 'Cancelar', GRAY)
c.showPage()

# 4 Endereço form
page('Novo endereço', 'Endereços', 'Cadastro/edição de endereço (usado como origem ou destino do frete)', 4)
field(50, H - 210, 440, 'Identificação *', 'Ex.: Depósito Central'); field(510, H - 210, 180, 'CEP *', '00000-000')
field(50, H - 270, 440, 'Logradouro *', ''); field(510, H - 270, 85, 'Número *', ''); field(605, H - 270, 85, 'Compl.', '')
field(50, H - 330, 200, 'Bairro *', ''); field(270, H - 330, 220, 'Cidade *', ''); field(510, H - 330, 180, 'UF *', 'Selecione')
button(50, H - 380, 80, 'Salvar'); button(140, H - 380, 80, 'Cancelar', GRAY)
txt(50, H - 405, 'Ao digitar o CEP o endereço é preenchido automaticamente (ViaCEP).', 8, False, GRAY)
c.showPage()

# 5 Transportador form
page('Novo transportador', 'Transportadores', 'Cadastro/edição de transportador', 5)
field(50, H - 210, 440, 'Nome / Razão social *', ''); field(510, H - 210, 180, 'CPF ou CNPJ *', '')
field(50, H - 270, 180, 'Telefone', '(00) 00000-0000'); field(250, H - 270, 440, 'E-mail', '')
field(50, H - 330, 440, 'Veículo', 'Ex.: Caminhão baú'); field(510, H - 330, 180, 'Placa', 'ABC1D23')
button(50, H - 380, 80, 'Salvar'); button(140, H - 380, 80, 'Cancelar', GRAY)
c.showPage()

# 6 Gerar frete
page('Gerar novo frete', 'Fretes', 'Frete: 1 transportador + origem + destino + vários produtos (campos dinâmicos em JavaScript)', 6)
box(50, H - 330, W - 100, 190)
txt(60, H - 155, '1. Dados do frete', 10, True)
field(60, H - 195, 300, 'Transportador *', 'Selecione'); field(375, H - 195, 140, 'Data *', '16/11/2026'); field(530, H - 195, 150, 'Valor do frete (R$) *', '0,00')
field(60, H - 255, 310, 'Endereço de origem *', 'Selecione'); field(385, H - 255, 295, 'Endereço de destino *', 'Selecione')
field(60, H - 315, 620, 'Observações', '')
box(50, H - 500, W - 100, 155)
txt(60, H - 365, '2. Produtos transportados', 10, True); button(W - 230, H - 372, 160, '+ Adicionar produto', GREEN, 20)
field(60, H - 415, 400, 'Produto', 'Selecione'); field(475, H - 415, 80, 'Qtd.', '1'); button(570, H - 413, 90, 'Remover', RED, 22)
field(60, H - 470, 400, 'Produto', 'Selecione'); field(475, H - 470, 80, 'Qtd.', '1'); button(570, H - 468, 90, 'Remover', RED, 22)
txt(60, 70, 'Peso total: 0,000 kg      Valor da carga: R$ 0,00  (atualizados na hora)', 9, True)
button(W - 230, 60, 90, 'Gerar frete'); button(W - 130, 60, 80, 'Cancelar', GRAY)
c.showPage()

# 7 Detalhe frete
page('Frete nº 23', 'Fretes', 'Detalhe do frete gerado', 7)
for i, (t, a, b) in enumerate([('Transportador', 'Rápido Transportes', 'CNPJ - Caminhão baú ABC1D23'), ('Origem', 'Depósito Central', 'Rua das Flores, 100 - Guaíra/PR'), ('Destino', 'Loja Curitiba', 'Av. Brasil, 500 - Curitiba/PR')]):
    bx = 50 + i * 235; box(bx, H - 230, 220, 80); txt(bx + 10, H - 168, t, 8, False, GRAY); txt(bx + 10, H - 188, a, 11, True); txt(bx + 10, H - 206, b, 8)
table(50, H - 245, W - 100, ['Produto', 'Qtd.', 'Peso unit.', 'Valor unit.', 'Peso total', 'Subtotal'], [['Caixa de Parafusos', '5', '2,500 kg', 'R$ 45,90', '12,500 kg', 'R$ 229,50'], ['Furadeira', '1', '1,800 kg', 'R$ 299,00', '1,800 kg', 'R$ 299,00']], [220, 60, 100, 100, 100, 100])
txt(50, 90, 'Data: 16/11/2026      Valor do frete: R$ 350,50      Observações: Frágil', 10, True)
button(W - 200, 85, 70, 'Editar', BLUE); button(W - 120, 85, 70, 'Voltar', GRAY)
c.showPage()

# 8 Fretes lista
page('Fretes', 'Fretes', 'Lista de fretes com filtro por data', 8)
txt(50, H - 160, 'Filtrar por data', 8, True); box(50, H - 188, 140, 22); txt(56, H - 181, '16/11/2026', 9, False, GRAY); button(200, H - 188, 70, 'Filtrar', GRAY)
button(W - 180, H - 135, 130, '+ Gerar novo frete')
table(50, H - 205, W - 100, ['Nº', 'Data', 'Transportador', 'Rota', 'Itens', 'Peso', 'Valor', 'Ações'], [['23', '16/11/2026', 'Rápido Transportes', 'Guaíra/PR -> Curitiba/PR', '6', '14,300 kg', 'R$ 350,50', '[Ver][Editar][x]']], [35, 80, 135, 150, 40, 80, 80, 110])
c.showPage()

# 9 Relatório
page('Relatório de fretes por dia', 'Relatórios', 'Consulta por data e download do relatório (PDF e CSV)', 9)
txt(50, H - 160, 'Data', 8, True); box(50, H - 188, 140, 22); txt(56, H - 181, '16/11/2026', 9, False, GRAY); button(200, H - 188, 90, 'Consultar', GRAY)
box(50, H - 250, W - 100, 45); txt(60, H - 222, '1 frete(s) em 16/11/2026  |  peso total 14,300 kg  |  soma dos fretes R$ 350,50', 9, True)
button(W - 280, H - 240, 105, 'Baixar PDF', RED); button(W - 165, H - 240, 105, 'Baixar CSV', GREEN)
table(50, H - 262, W - 100, ['Nº', 'Transportador', 'Rota', 'Itens', 'Peso', 'Valor do frete'], [['23', 'Rápido Transportes', 'Guaíra/PR -> Curitiba/PR', '6', '14,300 kg', 'R$ 350,50']], [40, 190, 210, 60, 100, 100])
c.showPage()
c.save()
