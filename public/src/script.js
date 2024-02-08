
$(document).ready(function() {
    // Verifica se há uma mensagem na URL
    var mensagem = getUrlParameter('mensagem');

    if (mensagem !== undefined && mensagem !== null) {
        // Exibe a mensagem
        $('.notificacao').text(mensagem);

        // Após 3 segundos, oculta a mensagem
        setTimeout(function() {
            $('.notificacao').fadeOut('slow');
        }, 1000);
    }
});

// Função para obter parâmetros da URL
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}