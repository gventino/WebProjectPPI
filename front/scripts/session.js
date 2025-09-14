async function checkSession() {
    try {
        const API_BASE_URL = `${window.location.protocol}//${window.location.host}/back/anunciante/AnuncianteController.php?action=checkSession`;
        const response = await fetch(API_BASE_URL, {
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
