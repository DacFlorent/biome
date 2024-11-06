function autocompleteUser() {
    var input = document.getElementById('searchUserInput').value;
    var resultsList = document.getElementById('autocompleteResults');

    // Si l'input est vide, vider les suggestions
    if (input.length === 0) {
        resultsList.innerHTML = '';
        return;
    }

    // Créer une requête AJAX pour récupérer les pseudos
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'path_to_your_php_script.php', true); // Remplace par le chemin de ton script PHP
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Gérer la réponse du serveur
    xhr.onload = function() {
        if (xhr.status == 200) {
            var pseudos = JSON.parse(xhr.responseText); // Supposons que ton PHP renvoie un tableau JSON

            // Vider les résultats précédents
            resultsList.innerHTML = '';

            // Ajouter les nouveaux résultats
            pseudos.forEach(function(pseudo) {
                var li = document.createElement('li');
                li.textContent = pseudo;
                resultsList.appendChild(li);
            });
        }
    };

    // Envoyer la requête
    xhr.send('fetch_pseudo=' + encodeURIComponent(input));
}
