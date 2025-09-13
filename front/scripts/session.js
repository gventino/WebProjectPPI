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
