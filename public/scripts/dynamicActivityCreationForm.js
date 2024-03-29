$(document).ready(function() {
    // Désactiver la liste déroulante city-selector au chargement de la page
    $('.place-selector').prop('disabled', true);

    $('.city-selector').change(function() {
        let cityId = $(this).val();
        $.get('/projetbde/public/city/zipcode/' + cityId, function(data) {
            $('.zipcode').val(data);
            $('.label-zipcode').css('display', 'block');
            $('.zipcode').css('display','block');

        });
        $('.place-selector').empty().append('<option value="">Loading...</option>');
        $.get('/projetbde/public/activity/places/' + cityId, function(data) {

            $('.place-selector').empty().append(data);

            $('.place-selector').prop('disabled', false);
            let placeId = $('.place-selector').val();
            getPlaceInformations(placeId);

        });
        $('.place-selector').change(function () {
            let placeId = $(this).val();

            getPlaceInformations(placeId);

        })
    });


});


function redirectToHomePage() {
    let redirectUrl = window.location.origin + '/projetbde/public'
        window.location.replace(redirectUrl);
}



function getPlaceInformations(placeId){
    $.get('/projetbde/public/place/informations/' + placeId, function (data) {
        $('.adresse').val(data.adresse);
        $('.adresse').css('display', 'block');
        $('.label-adresse').css('display', 'block');

        $('.coordinates').val(data.coordinates);
        $('.coordinates').css('display', 'block');
        $('.label-coordinates').css('display', 'block');
    })
}