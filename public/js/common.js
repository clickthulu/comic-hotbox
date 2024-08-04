$(document).ready(function(){

    $(".copytext-button").on('click', function(event){
        event.preventDefault();
        let target = $(this).data('target');
        let text = $("#" + target).html();

        text = text.replaceAll("&lt;", "<").replaceAll("&gt;", ">");
        navigator.clipboard.writeText(text);
        alert("Code copied");
    });

    $(".copy-data").on('click', function(event){
        event.preventDefault();
        let text = $(this).data('copy');
        let type = $(this).data('type')
        navigator.clipboard.writeText(text);
        alert(type + " copied");
    });

});