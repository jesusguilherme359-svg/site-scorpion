document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
        const response = await fetch('process_login.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            // Salva dados do usuário logado
            localStorage.setItem('usuarioLogado', 'true');
            localStorage.setItem('userData', JSON.stringify(result.data));
            // Sincroniza carrinho do usuário
            const idUsuario = result.data.id;
            try {
                const carrinhoResp = await fetch('carrinho_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `acao=listar&id_usuario=${encodeURIComponent(idUsuario)}`
                });
                const carrinhoResult = await carrinhoResp.json();
                if (carrinhoResult.success && Array.isArray(carrinhoResult.items)) {
                    // Salva itens do carrinho no localStorage
                    localStorage.setItem('carrinho', JSON.stringify(carrinhoResult.items));
                } else {
                    localStorage.setItem('carrinho', '[]');
                }
            } catch (err) {
                localStorage.setItem('carrinho', '[]');
            }
            alert(result.message);
            window.location.href = 'index.html';
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao fazer login. Tente novamente.');
    }
});