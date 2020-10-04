$(function () {
    // Code pour la popup de suppression d'une école
    $('.delete-school-button').on('click', function () {
        var url = $(this).attr('data-delete-url');
        $('#delete-confirm').attr('href', url);

        var name = $(this).attr('data-delete-name');
        $('#schoolDeleteModal #school-name').text(name);
    });
});

$(function () {
    // Code pour la popup de suppression d'une candidature
    $('.delete-candidature-button').on('click', function () {
        var url = $(this).attr('data-delete-url');
        $('#delete-confirm').attr('href', url);

        var name = $(this).attr('data-delete-name');
        $('#candidatureDeleteModal #candidature-name').text(name);
    });
});