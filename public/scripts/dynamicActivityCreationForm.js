$(document).ready(function() {
    let citySelector =  $('.city-selector');
    let placeSelector = $('.place-selector');
    let zipcode = $('.zipcode');
    // Désactiver la liste déroulante city-selector au chargement de la page
    placeSelector.prop('disabled', true);

    citySelector.change(function() {
        let cityId = $(this).val();
        $.get('/projetbde/public/city/zipcode/' + cityId, function(data) {
            zipcode.val(data);
            $('.label-zipcode').css('display', 'block');
            zipcode.css('display','block');

        });
        placeSelector.empty();
        placeSelector.append('<option value="">Loading...</option>');
        $.get('/projetbde/public/activity/places/' + cityId, function(data) {

            placeSelector.empty();
            placeSelector.append(data);

            placeSelector.prop('disabled', false);
            let placeId = placeSelector.val();
            getPlaceInformations(placeId);

        });
        placeSelector.change(function () {
            let placeId = $(this).val();

            getPlaceInformations(placeId);

        })
    });


    changeMaxDependingOnField('startDate', 'limitDate');
    changeMinDependingOnField('limitDate', 'startDate');
});


function redirectToHomePage() {
    let redirectUrl = window.location.origin + '/projetbde/public'
        window.location.replace(redirectUrl);
}



function getPlaceInformations(placeId){
    let adressField = $('.adresse');
    let adressLabel = $('.label-adresse');
    let coordinatesField = $('.coordinates');
    let coordinatesLabel = $('.label-coordinates');
    if(placeId != null) {
        $.get('/projetbde/public/place/informations/' + placeId, function (data) {


            if (data.adresse != null) {

                adressField.val(data.adresse);
                adressField.css('display', 'block');
                adressLabel.css('display', 'block');
            } else {
                adressField.empty();
                adressField.css('display', 'none')
                adressLabel.css('display', 'none');
            }

            if (data.coordinates != null) {
                coordinatesField.val(data.coordinates);
                coordinatesField.css('display', 'block');
                coordinatesLabel.css('display', 'block');
            } else {
                coordinatesField.empty();
                coordinatesField.css('display', 'none');
                coordinatesLabel.css('display', 'none');
            }
        })
    }else{
        adressField.empty();
        adressField.css('display', 'none')
        adressLabel.css('display', 'none');
        coordinatesField.empty();
        coordinatesField.css('display', 'none');
        coordinatesLabel.css('display', 'none');
    }
}

function changeMinDependingOnField(referenceClass, targetClass) {
    let target = $('.'+targetClass)
    $('.'+referenceClass).change(function () {
        let date = $(this).val();
        target.attr('min', date);
    })
}
function changeMaxDependingOnField(referenceClass, targetClass) {
    let target = $('.'+targetClass)
    $('.'+referenceClass).change(function () {
        let date = $(this).val();
        target.attr('max', date);
    })
}
