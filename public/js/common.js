$(document).ready(function(){

    $(".copytext-button").on('click', function(event){
        event.preventDefault();
        let target = $(this).data('target');
        let text = $("#" + target).html();

        text = text.replaceAll("&lt;", "<").replaceAll("&gt;", ">");
        navigator.clipboard.writeText(text);
        alert("Code copied");
    });

    $(".copyinput-button").on('click', function(event){
        event.preventDefault();
        let target = $(this).data('target');
        let text = $("#" + target).val();

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

    $(".clear-field").on('click', function(event){
        event.preventDefault();
        var rent = $(this).parents('.input-group').first();
        var inp = $(rent).find('input, textarea').first();
        $(inp).val('');
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

    $(".carousel-sortable").sortable({
        cursor: "grab",
        classes: {
            ".sortable-item": "highlight"
        },
        handle: ".sortable-handle",
        update: function (event, ui) {
            let mySortable = $(".carousel-sortable");
            let items = mySortable.children('.sortable-item');
            let count = 0;
            let carousel = mySortable.data('carousel');
            let data = {};
            for( let item of items) {
                data[count] = $(item).data('comic');
                count++;
            }

            $.post(
                "/carousel/order/" + carousel,
                { items: data }
            ).done(function() {
                document.location.reload();
            });

            console.log(data);
        }
    });


    $(".hotbox-sortable").sortable({
        cursor: "grab",
        classes: {
            ".sortable-item": "highlight"
        },
        handle: ".sortable-handle",
        update: function (event, ui) {
            let mySortable = $(".hotbox-sortable");
            let items = mySortable.children('.sortable-item');
            let count = 0;
            let hotbox = mySortable.data('hotbox');
            let data = {};
            for( let item of items) {
                data[count] = $(item).data('rotation');
                count++;
            }

            $.post(
                "/hotbox/order/" + hotbox,
                { items: data }
            ).done(function() {
                document.location.reload();
            });

            console.log(data);
        }
    });


    $(".webring-sortable").sortable({
        cursor: "grab",
        classes: {
            ".sortable-item": "highlight"
        },
        handle: ".sortable-handle",
        update: function (event, ui) {
            let mySortable = $(".webring-sortable");
            let items = mySortable.children('.sortable-item');
            let count = 0;
            let webring = mySortable.data('webring');
            let data = {};
            for( let item of items) {
                data[count] = $(item).data('comic');
                count++;
            }

            $.post(
                "/webring/order/" + webring,
                { items: data }
            ).done(function() {
                document.location.reload();
            });

            console.log(data);
        }
    });


    function closePop(target)
    {
        if(target){
            target.fadeOut(1000, function(){ console.log("Closing pop"); target.remove(); });
        }
    }



});