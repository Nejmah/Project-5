$(function () {
    let pageNum = 1;
    let requestUrl = $('#read-more').data('url');
    let commentsTotal = $('.comments').data('total');

    // Code pour charger les commentaires précédents
    $('#read-more').on('click', function () {
        pageNum += 1;
        $(this).attr('disabled', true);

        $.ajax({
                url: requestUrl,
                type: 'GET',
                data: {
                    page: pageNum
                },
            })
            .done(function (result) {
                $('#read-more').attr('disabled', false);
                $('.comments').append(result);


                // Cacher le bouton quand tous les commentaires sont affichés
                if (pageNum * 10 >= commentsTotal) {
                    $('#read-more').detach();
                }
            });
    });
});