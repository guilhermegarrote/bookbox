$(document).ready(function() {
    $('#login-form').submit(function(event) {
        event.preventDefault();
    
        const formData = new FormData(this);
    
        $.ajax({
            url: '/bookbox/login',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.redirecionar) {
                    window.location.href = data.redirecionar;
                } else {
                    alert(data.mensagem);
                }
            },
            error: function(xhr, status, error) {
                const data = JSON.parse(xhr.responseText);
    
                if (data.erro) {
                    alert(data.erro);
                }
                
                if (data.redirecionar) {
                    window.location.href = data.redirecionar;
                }
            }
        });
    });
});
