// Utilidades gerais: confirmação de exclusão, máscaras e busca de CEP.
document.addEventListener('DOMContentLoaded', function () {
  // Confirmação antes de excluir
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      if (!window.confirm(form.getAttribute('data-confirm'))) ev.preventDefault();
    });
  });

  // Máscaras simples
  var masks = {
    cep: function (v) { v = v.replace(/\D/g, '').slice(0, 8); return v.length > 5 ? v.slice(0, 5) + '-' + v.slice(5) : v; },
    doc: function (v) {
      v = v.replace(/\D/g, '').slice(0, 14);
      if (v.length <= 11) {
        return v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      }
      return v.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
    },
    tel: function (v) {
      v = v.replace(/\D/g, '').slice(0, 11);
      if (v.length > 10) return v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
      if (v.length > 6) return v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
      if (v.length > 2) return v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
      return v;
    },
    placa: function (v) { return v.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7); }
  };
  document.querySelectorAll('[data-mask]').forEach(function (input) {
    var fn = masks[input.getAttribute('data-mask')];
    if (!fn) return;
    input.value = fn(input.value);
    input.addEventListener('input', function () { input.value = fn(input.value); });
  });

  // Preenchimento automático do endereço pelo CEP (ViaCEP). Se falhar, o usuário digita manualmente.
  var cep = document.getElementById('cep');
  if (cep && document.getElementById('logradouro')) {
    cep.addEventListener('blur', function () {
      var v = cep.value.replace(/\D/g, '');
      if (v.length !== 8) return;
      fetch('https://viacep.com.br/ws/' + v + '/json/')
        .then(function (r) { return r.json(); })
        .then(function (d) {
          if (d.erro) return;
          ['logradouro', 'bairro', 'cidade', 'uf'].forEach(function (id) {
            var campo = document.getElementById(id);
            var valor = id === 'cidade' ? d.localidade : d[id];
            if (campo && valor && !campo.value) campo.value = valor;
          });
        })
        .catch(function () { /* sem internet: segue manual */ });
    });
  }
});
