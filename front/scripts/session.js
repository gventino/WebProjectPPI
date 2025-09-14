async function checkSession() {
    try {
        const response = await fetch('../../back/anunciante/AnuncianteController.php?action=checkSession', {
            method: 'GET'
        });
        const result = await response.json();
        
        if (response.ok && result.success && result.obj && result.obj.name) {
            return {
                success: true,
                user: result.obj
            };
        } else {
            return {
                success: false,
                user: null
            };
        }
    } catch (error) {
        console.error('Session check error:', error);
        return {
            success: false,
            user: null,
            error: error.message
        };
    }
}

async function gatekeeper() {
    const check = await checkSession();
    if(!check.success){
        if(check.error!=null){
            alert(check.error.message)
        } else {
            alert("Página apenas para usuários logados!");
            window.location.href = '../login';
        }
    }
}

async function innkeeper() {
    const check = await checkSession();
    if(!check.success){
        if(check.error!=null){
            alert(check.error.message)
        }
    } else {
        alert("Você já está logado! Faça o logoff primeiro!");
            window.location.href = '../principal_interna';
    }
}