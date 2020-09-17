$(function () {
    console.log("DEBUG")
    // Code pour la popup de suppression d'une école
    $('.delete-school-button').on('click', function () {
        console.log("CLICK")
        var url = $(this).attr('data-delete-url');
        $('#delete-confirm').attr('href', url);

        var name = $(this).attr('data-delete-name');
        console.log(name)
        $('#schoolDeleteModal #school-name').text(name);
    });
});