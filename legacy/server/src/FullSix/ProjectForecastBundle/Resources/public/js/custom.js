$(document).ready(function() {

    activation('.activation');
    activation('.tasksload-activation');
    activation('.tasks-activation');

    activationOnGrid();
    showSocialInfo();
});

function activation(elementID)
{
    $(elementID).on('submit', function(e){
        e.preventDefault();
            $(this).ajaxSubmit({
            success: function (data) {

                if (data['status'] == false) {
                    displayErrorMessage(data);
                } else if (data['status'] == true) {
//                    $("#activate").toggleClass('icon-play icon-pause');
                    location.reload( true );
                }
            }
        });
    });
}

function activationOnGrid()
{
    $('a[title="Activer"], a[title="Pause"]').click(function (e) {
        e.preventDefault();
        var route = $(this).attr('href');
        $.ajax({
            url: route,
            context: this
            }).done(function(data) {

            if (data['status'] == false) {
                displayErrorMessage(data);
            } else if (data['status'] == true){
                //$(this).children().first().toggleClass('icon-play icon-pause');
                //$(this).parent().parent().children().find('.icon-remove, .icon-ok').toggleClass('icon-remove icon-ok');

                location.reload( true );
            }
        });
    });
}

function displayErrorMessage(response){
    $('#activation-error').find('ul').html(response['html']);
    $.colorbox({inline:true, href:"#activation-error"});
}

function showSocialInfo()
{
    $('.social-info h2').click(function() {
        $(this).next('.social-info-content').slideToggle();
        $(this).toggleClass('opened');
    })
}
