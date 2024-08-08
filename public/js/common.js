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

    $(".img-cropped").on('click', function(event){
        event.preventDefault();
        let data = this.outerHTML;

        let approve = $(this).parents('.image-row').first().find('a.approve-button'); //.find('a.approve-button');
        $("#image-modal").find(".modal-body").html(this.outerHTML);
        $("#image-modal").find(".modal-body").find('img').removeClass('img-cropped');
        $("#image-modal").find(".modal-footer").html(approve[0].outerHTML);

        let imageApproved = approve.hasClass('btn-primary');
        let title = imageApproved ? 'Remove Approval' : 'Approve Image'
        $("#image-modal").find(".modal-title").text(title);
        $("#image-modal").modal('show')



    })

});