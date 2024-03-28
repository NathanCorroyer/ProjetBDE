$(document).ready(function() {
    $('.city-selector').change(function() {
        var cityId = $(this).val();
        $('.place-selector').empty().append('<option value="">Loading...</option>');
        $.get('/projetbde/public/activity/places/' + cityId, function(data) {
            $('.place-selector').empty().append(data);
        });
    });
});


function redirectToHomePage() {
    let redirectUrl = window.location.origin + '/projetbde/public'
        window.location.replace(redirectUrl);
}

