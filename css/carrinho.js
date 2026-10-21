// Abrir modal do carrinho
if (document.getElementById('carrinho-btn')) {
    document.getElementById('carrinho-btn').addEventListener('click', function() {
        document.getElementById('carrinho-modal').style.display = 'flex';
        atualizarCarrinhoModal();
    });
}

// Função para atualizar modal do carrinho
function atualizarCarrinhoModal() {
    var carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    var lista = document.getElementById('carrinho-itens');
    if (!lista) return;
    lista.innerHTML = '';
    if (carrinho.length === 0) {
        lista.innerHTML = '<li style="text-align:center; color:#a855f7;">Seu carrinho está vazio.</li>';
    } else {
        carrinho.forEach(function(item) {
            var li = document.createElement('li');
            li.innerHTML = `<img src="${item.img}" alt="${item.nome}" style="height:40px; width:auto; vertical-align:middle; margin-right:12px; border-radius:8px;"> <span style="vertical-align:middle;">${item.nome}</span>`;
            li.style = 'padding:8px 0; border-bottom:1px solid #eee; color:#23272F; font-family: Bebas Neue, sans-serif; font-size:1.1rem; display:flex; align-items:center;';
            lista.appendChild(li);
        });
    }
    var contador = document.getElementById('carrinho-count');
    if (contador) contador.textContent = carrinho.length;
}

// Atualiza contador ao carregar página
atualizarCarrinhoModal();
