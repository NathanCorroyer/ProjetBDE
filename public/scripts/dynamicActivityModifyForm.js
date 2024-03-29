$(document).ready(function() {


    let placeId = $('.place-selector').val();

    getPlaceInformations(placeId)


})

function supprimer(id) {


    // Envoyer une requête AJAX au serveur pour supprimer l'activité
    $.get('/projetbde/public/activity/delete/' + id,


       function () {
        redirectToHomePage()
    }

    )
}