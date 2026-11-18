// Campos dinâmicos do frete: adicionar/remover produtos e recalcular totais.
document.addEventListener('DOMContentLoaded', function () {
  var container = document.getElementById('itens');
  var tpl = document.getElementById('tpl-item');
  var btnAdd = document.getElementById('btn-add-item');
  if (!container || !tpl || !btnAdd) return;

  function fmt(n, casas) {
    return n.toLocaleString('pt-BR', { minimumFractionDigits: casas, maximumFractionDigits: casas });
  }

  function recalcular() {
    var peso = 0, valor = 0;
    container.querySelectorAll('.item-row').forEach(function (row) {
      var opt = row.querySelector('.item-produto').selectedOptions[0];
      var qtd = parseInt(row.querySelector('.item-qtd').value, 10) || 0;
      if (opt && opt.value) {
        peso += (parseFloat(opt.getAttribute('data-peso')) || 0) * qtd;
        valor += (parseFloat(opt.getAttribute('data-valor')) || 0) * qtd;
      }
    });
    document.getElementById('total-peso').textContent = fmt(peso, 3) + ' kg';
    document.getElementById('total-valor').textContent = 'R$ ' + fmt(valor, 2);
  }

  function atualizarBotoes() {
    var rows = container.querySelectorAll('.item-row');
    rows.forEach(function (row) {
      row.querySelector('.btn-remove-item').disabled = rows.length === 1;
    });
  }

  btnAdd.addEventListener('click', function () {
    container.appendChild(tpl.content.cloneNode(true));
    atualizarBotoes();
    recalcular();
  });

  container.addEventListener('click', function (ev) {
    var btn = ev.target.closest('.btn-remove-item');
    if (!btn) return;
    btn.closest('.item-row').remove();
    atualizarBotoes();
    recalcular();
  });

  container.addEventListener('input', recalcular);
  container.addEventListener('change', recalcular);

  atualizarBotoes();
  recalcular();
});
