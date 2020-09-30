$('#candidature_image').on('change', function () {
    // Get the file name
    let fileName = $(this).val().replace('C:\\fakepath\\', ' ');
    // var fileName = $(this).val();
    // Replace the "Choose a file" label
    $(this).next('.custom-file-label').html(fileName);
})