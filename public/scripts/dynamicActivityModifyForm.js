document.addEventListener('DOMContentLoaded', function() {
    let placeId = document.querySelector('.place-selector').value;
    getPlaceInformations(placeId);

});

function supprimer(id) {
    // Envoyer une requête AJAX au serveur pour supprimer l'activité
    fetch('/activity/delete/' + id)
        .then(response => {
            if (response.ok) {
                redirectToHomePage();
            } else {
                console.error('Erreur lors de la suppression de l\'activité');
            }
        })
        .catch(error => console.error('Erreur lors de la suppression de l\'activité:', error));
}