document.addEventListener('DOMContentLoaded', function() {
    let citySelector = document.querySelector('.city-selector');
    let form = document.getElementById('place-form');

    form.addEventListener("submit", function(event){
        event.preventDefault();
        let placeName = document.getElementById("place-name").value;
        let placeAddress = document.getElementById('place-address').value;
        let placeLatitude = document.getElementById('place-latitude').value;
        let placeLongitude = document.getElementById('place-longitude').value;
        let cityId = citySelector.value;

        // Créer un objet contenant les données à envoyer
        let formData = {
            placeName: placeName,
            placeAddress: placeAddress,
            placeLatitude: placeLatitude,
            placeLongitude: placeLongitude,
            placeCityId: cityId
        };

        // Effectuer la requête AJAX
        fetch('place/create/' + cityId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la requête : ' + response.statusText);
                }else{
                    emptyForm('place-form');
                    document.getElementById('button-modal-close').click();
                }
                return response.json();
            })
            .then(data => {

                let placeSelector = document.querySelector(".place-selector");
                placeSelector.innerHTML+=data.options;
                getPlaceInformations(data.id);
            })
            .catch(error => {
                console.error('Erreur lors de la requête : ', error);
            });
    });




})

function emptyForm(formId){
    var form = document.getElementById(formId);

// Parcourez tous les champs du formulaire et réinitialisez leurs valeurs
    var formElements = form.elements;
    for (var i = 0; i < formElements.length; i++) {
        var field = formElements[i];
        // Vérifiez si le champ n'est pas un bouton de soumission
        if (field.type !== 'submit') {
            field.value = '';
        }
    }
}

function getPlaceCreated(){

}