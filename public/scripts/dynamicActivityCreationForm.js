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

   // setMinToDates();

    changeMaxDependingOnField('startDate', 'limitDate');
    changeMinDependingOnField('limitDate', 'startDate');
});

function setMinToDates(){
    let dateInputs = document.querySelectorAll('.js-datepicker');
    dateInputs.forEach(function(input) {
        // Set the minimum value to the current date
        input.min = new Date().toISOString().split("T")[0];
    });
}

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
        let date = new Date($(this).val());
        //Je veux que mon activité soit minimum le jour après la fin des inscriptions
        date.setDate(date.getDate()+1);
        //Je passe les heures à minuit pour ne pas être embêté par le check
        date.setHours(23, 59, 59, 999);
        target.attr('min',formatISOToCustomFormat(date.toISOString()));
    })
}
function changeMaxDependingOnField(referenceClass, targetClass) {
    let target = $('.'+targetClass)
    $('.'+referenceClass).change(function () {
        let date = new Date($(this).val());
        //Je veux que si j'ai sélectionné d'abord ma date de début d'activité, la date de fin
        // d'inscription soit au maximum à 1 jour avant
        date.setDate(date.getDate()-1);
        //Pareil, je ne veux pas être embêté par le check des heures
        date.setHours(0, 0, 0, 0);
        target.attr('max', formatISOToCustomFormat(date.toISOString()));
    })
}


function formatISOToCustomFormat(isoString) {
    // Create a new Date object from the ISO string
    let date = new Date(isoString);

    // Extract date components
    let year = date.getFullYear();
    let month = ('0' + (date.getMonth() + 1)).slice(-2); // Month is zero-based, so we add 1 and zero-pad
    let day = ('0' + date.getDate()).slice(-2);

    // Extract time components
    let hours = ('0' + date.getHours()).slice(-2);
    let minutes = ('0' + date.getMinutes()).slice(-2);

    // Concatenate the components in the desired format
    return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
}