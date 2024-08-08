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
        let imgModal = $("#image-modal");
        let approve = $(this).parents('.image-row').first().find('a.approve-button'); //.find('a.approve-button');
        imgModal.find(".modal-body").html(this.outerHTML);
        imgModal.find(".modal-body").find('img').removeClass('img-cropped');
        imgModal.find(".modal-footer").html(approve[0].outerHTML);

        let imageApproved = approve.hasClass('btn-primary');
        let title = imageApproved ? 'Remove Approval' : 'Approve Image'
        imgModal.find(".modal-title").text(title);
        imgModal.modal('show')

    });


    setTimeout(function(){
        $(".pop").each(function(){
            closePop($(this));
        })
    }, 10000)

    $(".pop-close").on('click', function(event){
        event.preventDefault();
        let target = $(this).parents(".pop").first();
        closePop(target);
    });

    function closePop(target)
    {
        console.log("Close pop triggered")
        if(target){
            target.fadeOut(1000, function(){ console.log("Closing pop"); target.remove(); });
        }
    }


});