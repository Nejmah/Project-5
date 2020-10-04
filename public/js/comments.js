$(function () {
    let pageNum = 1;
    let requestUrl = $('#read-more').data('url');

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
            .done(function (data) {
                console.log("Response data:", data);
                $('#read-more').attr('disabled', false);

                if (data.comments) {
                    data.comments.forEach(function (comment) {}

                    );
                }

                // TODO cacher le bouton quand tous les commentaires sont affichés
            });
    });
});