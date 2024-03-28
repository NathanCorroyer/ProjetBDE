$(document).ready(function() {
    // Désactiver la liste déroulante city-selector au chargement de la page
    $('.place-selector').prop('disabled', true);

    $('.city-selector').change(function() {
        var cityId = $(this).val();
        $('.place-selector').empty().append('<option value="">Loading...</option>');
        $.get('/projetbde/public/activity/places/' + cityId, function(data) {
            $('.place-selector').empty().append(data);

            $('.place-selector').prop('disabled', false);
        });
    });

});


function redirectToHomePage() {
    let redirectUrl = window.location.origin + '/projetbde/public'
        window.location.replace(redirectUrl);
}

