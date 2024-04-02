document.addEventListener('DOMContentLoaded', function() {
    let citySelector = document.querySelector('.city-selector');
    let placeLabel = document.querySelector('.place-label');
    let placeSelector = document.querySelector('.place-selector');
    let zipcode = document.querySelector('.zipcode');
    let adressField = document.querySelector('.adresse');
    let adressLabel = document.querySelector('.label-adresse');
    let coordinatesField = document.querySelector('.coordinates');
    let coordinatesLabel = document.querySelector('.label-coordinates');
    let placeIntrouvable = document.querySelector('#place-introuvable');

    placeSelector.disabled = true;

    citySelector.addEventListener('change', function() {
        let cityId = this.value;

        // Requête Ajax pour obtenir le code postal
        fetch('/city/zipcode/' + cityId)
            .then(response => response.text())
            .then(data => {
                zipcode.value = data;
                document.querySelector('.label-zipcode').style.display = 'block';
                zipcode.style.display = 'block';
            });

        // Requête Ajax pour obtenir les lieux
        placeSelector.innerHTML = '<option value="">Loading...</option>';
        console.log("City id : " + cityId);
        fetch('/activity/places/'+cityId)
            .then(response => response.text())
            .then(data => {
                if(data.length > 0){
                    placeSelector.innerHTML = data;
                    placeLabel.style.display = 'block';
                    placeSelector.style.display ='block';
                    placeSelector.disabled = false;
                    let placeId = placeSelector.value;
                    getPlaceInformations(placeId);
                }else{
                    adressField.innerHTML = '';
                    adressField.style.display = 'none';
                    adressLabel.style.display = 'none';
                    coordinatesField.innerHTML = '';
                    coordinatesField.style.display = 'none';
                    coordinatesLabel.style.display = 'none';
                    placeIntrouvable.classList.remove('hidden')
                    placeSelector.innerHTML = '';
                    placeSelector.disabled = true;
                }

            });
    });

    placeSelector.addEventListener('change', function () {
        let placeId = this.value;
        getPlaceInformations(placeId);
    });

    // setMinToDates();

    changeMaxDependingOnField('startDate', 'limitDate');
    changeMinDependingOnField('limitDate', 'startDate');
});

function redirectToHomePage() {
    let redirectUrl = window.location.origin + '/projetbde/public';
    window.location.replace(redirectUrl);
}

function getPlaceInformations(placeId){
    let adressField = document.querySelector('.adresse');
    let adressLabel = document.querySelector('.label-adresse');
    let coordinatesField = document.querySelector('.coordinates');
    let coordinatesLabel = document.querySelector('.label-coordinates');

    if(placeId != null) {
        fetch('/place/informations/' + placeId)
            .then(response => response.json())
            .then(data => {
                console.log("Data du lieu : " + data)
                if (data.adresse !=null && data.coordinates !=null ){
                    adressField.value = data.adresse;
                    adressField.style.display = 'block';
                    adressLabel.style.display = 'block';


                    coordinatesField.value = data.coordinates;
                    coordinatesField.style.display = 'block';
                    coordinatesLabel.style.display = 'block';

                }else{
                    adressField.innerHTML = '';
                    adressField.style.display = 'none';
                    adressLabel.style.display = 'none';
                    coordinatesField.innerHTML = '';
                    coordinatesField.style.display = 'none';
                    coordinatesLabel.style.display = 'none';
                }

            });
    } else {
        adressField.innerHTML = '';
        adressField.style.display = 'none';
        adressLabel.style.display = 'none';
        coordinatesField.innerHTML = '';
        coordinatesField.style.display = 'none';
        coordinatesLabel.style.display = 'none';
    }
}

function changeMinDependingOnField(referenceClass, targetClass) {
    let target = document.querySelector('.'+targetClass);
    let reference = document.querySelector('.'+referenceClass);

    reference.addEventListener('change', function () {
        let date = new Date(this.value);
        date.setDate(date.getDate() + 1);
        date.setHours(0, 0, 0, 0);
        target.min = formatISOToCustomFormat(date.toISOString());
    });
}

function changeMaxDependingOnField(referenceClass, targetClass) {
    let target = document.querySelector('.'+targetClass);
    let reference = document.querySelector('.'+referenceClass);

    reference.addEventListener('change', function () {
        let date = new Date(this.value);
        date.setDate(date.getDate() - 1);
        date.setHours(23, 59, 59, 999);
        target.max = formatISOToCustomFormat(date.toISOString());
    });
}

function formatISOToCustomFormat(isoString) {
    let date = new Date(isoString);

    let year = date.getFullYear();
    let month = ('0' + (date.getMonth() + 1)).slice(-2);
    let day = ('0' + date.getDate()).slice(-2);
    let hours = ('0' + date.getHours()).slice(-2);
    let minutes = ('0' + date.getMinutes()).slice(-2);

    return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
}