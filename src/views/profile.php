<?php
require_once "../controllers/profile.php";
$pseudo = null;
$user = new User();
$pseudos = [];

if (isset($_SESSION['user_id'])) {
    // Récupérer le pseudo de l'utilisateur à partir de la session ou de la base de données
    $pseudo = $_SESSION['user_id'];
    $Points = $_SESSION['points'];
} else {
    $pseudo = ''; // Valeur vide si non connecté
    $Points = "0";
}
if (isset($_POST['fetch_pseudo'])) {
    // Récupérer tous les pseudos
    $pseudos = $user->getAllPseudo();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateInfo'])) {
    $pseudo = trim($_POST['pseudo']);

    // Crée une instance de l'utilisateur
    $user = new User();

    // Essayer de mettre à jour le pseudo
    $result = $user->updatePseudo($pseudo);

    if (isset($result['error'])) {
        // Si le pseudo est déjà pris
        $notification = [
            "title" => "infoError",
            "message" => $result['error'] // Le message retourné peut être "Ce pseudo est déjà pris"
        ];
    } elseif ($result) {
        // Si la mise à jour a réussi
        $notification = [
            "title" => "infoUpdated",
            "message" => "Votre pseudo a été mis à jour avec succès."
        ];
    } else {
        // Si une autre erreur est survenue
        $notification = [
            "title" => "infoError",
            "message" => "Une erreur est survenue lors de la mise à jour de votre pseudo."
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePassword'])) {
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Vérifie que les mots de passe correspondent
    if ($password === $confirmPassword) {
        // Crée une instance de l'utilisateur
        $user = new User();

        // Essaye de mettre à jour le mot de passe
        $result = $user->updatePassword($password);

        if (isset($result['error'])) {
            // Si le mot de passe n'a pas pu être mis à jour
            $notification = [
                "title" => "passwordError",
                "message" => $result['error'] // Le message d'erreur, si disponible
            ];
        } elseif ($result) {
            // Si la mise à jour a réussi
            $notification = [
                "title" => "passwordUpdated",
                "message" => "Votre mot de passe a été mis à jour avec succès."
            ];
        } else {
            // Si une autre erreur est survenue
            $notification = [
                "title" => "passwordError",
                "message" => "Une erreur est survenue lors de la mise à jour de votre mot de passe."
            ];
        }
    } else {
        // Si les mots de passe ne correspondent pas
        $notification = [
            "title" => "passwordError",
            "message" => "Les mots de passe ne correspondent pas."
        ];
    }
}
if (isset($_POST['fetch_pseudo'])) {
    // Récupérer l'input de l'utilisateur
    $input = $_POST['fetch_pseudo'];

    // Connexion à la base de données
    $pdo = getDBConnection();  // Assure-toi que cette fonction de connexion est correcte

    // Construire la requête pour rechercher les pseudos correspondant à l'input de l'utilisateur
    $query = "SELECT pseudo FROM User WHERE pseudo LIKE :input LIMIT 10"; // Limiter les résultats à 10
    $stmt = $pdo->prepare($query);

    // Ajouter le filtre sur l'input de l'utilisateur
    $stmt->bindValue(':input', '%' . $input . '%', PDO::PARAM_STR);
    $stmt->execute();

    // Récupérer les résultats filtrés
    $pseudos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Vérifier si des pseudos ont été trouvés
    if (is_array($pseudos) && !empty($pseudos)) {
        // Envoyer la réponse en JSON avec les pseudos filtrés
        echo json_encode($pseudos);
    } else {
        // Si aucun pseudo trouvé, envoyer un tableau vide
        echo json_encode([]);
    }
    exit(); // Arrêter l'exécution du script pour éviter d'envoyer du texte supplémentaire
}

//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_users'])) {
//    // Vérifiez si 'pseudo' existe dans le tableau $_POST
//    if (isset($_POST['pseudo'])) {
//        $pseudo = $_POST['pseudo'];
//        $newUser = $user->addUser($pseudo);
//
//        if (isset($newUser['error'])) {
//            // Message d'erreur pour doublon
//            $notification = [
//                "title" => "infoError",
//                "message" => $newUser['error'] // Ce pseudo est déjà pris
//            ];
//        } elseif ($newUser) {
//            // Message de succès
//            $notification = [
//                "title" => "infoAdded",
//                "message" => "Utilisateur ajouté avec succès"
//            ];
//        } else {
//            // Message d'erreur générique
//            $notification = [
//                "title" => "infoError",
//                "message" => "Une erreur est survenue lors de l'ajout de l'utilisateur."
//            ];
//        }
//    } else {
//        // Message d'erreur si le pseudo n'est pas défini
//        $notification = [
//            "title" => "infoError",
//            "message" => "Le pseudo est requis."
//        ];
//    }
//}
?>
<h1>Bienvenue sur ta page de profil <?= $pseudo ?> </h1>
<h2>Ton score est de : <?= $Points ?> </h2>

<h3 class="bold">Mettre à jour ton pseudo</h3>
<form action="" method="post" class="flex flex-column gap-2">
    <div class="rounded-box flex flex-column gap-2">
        <div class="flex flex-column">
            <label for="pseudo" class="bold">Pseudo</label>
            <input type="text" autocomplete="off" id="pseudo" name="pseudo" class="input-form" placeholder="<?= htmlspecialchars($pseudo); ?>" aria-label="Pseudo">
        </div>
        <?php if (isset($notification) && $notification["title"] === "infoUpdated"): ?>
            <p class="italic bold font-sm w-full center text-success"><?= $notification["message"] ?></p>
        <?php elseif (isset($notification) && $notification["title"] === "infoError"): ?>
            <p class="italic bold font-sm w-full center text-danger"><?= $notification["message"] ?></p>
        <?php endif; ?>
    </div>
    <div class="center">
        <button type="submit" name="updateInfo" class="submit-button" aria-label="Mettre à jour les informations">Mettre à jour</button>
    </div>
</form>
<script>
    const pseudos = <?php echo json_encode($pseudos); ?>;
</script>
<div class="w-full flex flex-column gap-2">
    <h3 class="bold">Mettre à jour ton mot de pass</h3>
    <form action="" method="post" class="flex flex-column gap-2">
        <div class="rounded-box flex flex-column gap-2">
            <div class="flex flex-column">
                <label for="password" class="bold">Password</label>
                <input type="password" name="password" id="password" placeholder="New Password" class="input-form" aria-label="New Password">
            </div>
            <div class="flex flex-column">
                <label for="confirmPassword" class="bold">Confirme ton mot de pass</label>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" class="input-form" aria-label="Confirm Password">
            </div>
            <?php if (isset($notification) && $notification["title"] === "passwordUpdated"): ?>
                <p class="italic bold font-sm w-full center text-success"><?= $notification["message"] ?></p>
            <?php elseif (isset($notification) && $notification["title"] === "passwordError"): ?>
                <p class="italic bold font-sm w-full center text-danger"><?= $notification["message"] ?></p>
            <?php endif; ?>
        </div>
        <div class="center">
            <button type="submit" name="updatePassword" class="submit-button" aria-label="Update password">Update Password</button>
        </div>
    </form>

</div>
<!--<div class="h-full flex flex-row-to-col gap-2">-->
<!--    <div class="flex flex-col gap-2">-->
<!--        <h1>Ajoute un utilisateur</h1>-->
<!--        <form method="POST" action="profile.php">-->
<!--            <input type="text" name="pseudo" placeholder="Entrez le pseudo" required>-->
<!--            <button type="submit" name="add_users">Ajouter un utilisateur</button>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->
<?php //if (isset($notification)): ?>
<!--    <p class="--><?php //= $notification['title'] === 'infoError' ? 'text-danger' : 'text-success' ?><!--">-->
<!--        --><?php //= htmlspecialchars($notification['message']) ?>
<!--    </p>-->
<?php //endif; ?>

<!--</div>-->
<!--<div class="h-full flex flex-row-to-col gap-2">-->
<!--    <div class="w-full flex flex-column gap-2">-->
<!--        <h2>Chercher les pseudos</h2>-->
<!--        <form action="profile.php" method="post">-->
<!--            <button type="submit" name="fetch_pseudo">Chercher</button>-->
<!--        </form>-->
<!---->
<!--        <h3 class="bold">Liste de tous les pseudos</h3>-->
<!--        <ul>-->
<!--            --><?php //if (!empty($pseudos)): ?>
<!--                --><?php //foreach ($pseudos as $pseudonym): ?>
<!--                    <li>--><?php //= htmlspecialchars($pseudonym) ?><!--</li>-->
<!--                --><?php //endforeach; ?>
<!--            --><?php //else: ?>
<!--                <li>Aucun pseudo trouvé.</li>-->
<!--            --><?php //endif; ?>
<!--        </ul>-->
<!--    </div>-->
<!--</div>-->
<div class="flex flex-row badge-container w-full">
    <div>
        <h4>Complete All</h4>
        <img src="../images/badge3.png" alt="Badge 3" class="badge" width="50" height="50">
    </div>
    <div>
        <h5>Complete theme</h5>
        <img src="../images/badge1.png" alt="Badge 1" class="badge" width="50" height="50">
    </div>
    <div>
        <h5>Complete chapters</h5>
        <img src="../images/badge2.png" alt="Badge 2" class="badge" width="50" height="50">
    </div>


</div>
<?php
// Connexion à la base de données
$pdo = getDBConnection();

// Requête pour récupérer les pseudos
$sql = "SELECT pseudo FROM User";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Récupérer les résultats sous forme de tableau
$pseudoList = $stmt->fetchAll(PDO::FETCH_COLUMN); // Récupère seulement la colonne 'pseudo'
?>
<?php
//session_start();

// Assurez-vous que l'utilisateur est bien connecté
//if (!isset($_SESSION['user_id'])) {
//    // Redirigez l'utilisateur vers la page de connexion ou une autre action appropriée
//    exit("Vous devez être connecté pour envoyer une demande d'ami.");
//}

// Vérifiez si le formulaire a été soumis
if (isset($_POST['send_request'])) {
    // Récupérez les données
    $sender_id = $_SESSION['user_id']; // L'ID de l'utilisateur connecté
    $receiver_id = $_POST['receiver_id']; // L'ID du receveur de la demande

    // Connexion à la base de données (assurez-vous de définir les bons paramètres)
    $pdo = getDBConnection();

    // Vérifiez la connexion
    if ($pdo->connect_error) {
        die("La connexion a échoué: " . $pdo->connect_error);
    }

    // Préparez la requête d'insertion
    $stmt = $pdo->prepare("INSERT INTO FriendRequest (Status, IdReceiver, IdSender, CreatedAt) VALUES (?, ?, ?, ?)");
    $status = 'pending'; // Statut initial de la demande (par exemple, "en attente")
    $created_at = date("Y-m-d H:i:s"); // Date actuelle

    $stmt->bind_param("siss", $status, $receiver_id, $sender_id, $created_at); // Paramètres à lier

    // Exécutez la requête
    if ($stmt->execute()) {
        echo "Demande d'ami envoyée avec succès.";
    } else {
        echo "Erreur lors de l'envoi de la demande d'ami : " . $stmt->error;
    }

    // Fermez la connexion
    $stmt->close();
    $pdo->close();
}
?>
<div>
    <h3 class="bold mb-1">Rechercher un utilisateur</h3>
    <form name="searchUser" class="rounded-box flex flex-column gap-Z" autocomplete="off" aria-label="Formulaire de recherche d'utilisateur">
        <div class="flex flex-column gap-1">
            <label for="searchUserInput">Rechercher un utilisateur</label>
            <input
                    class="input-form"
                    type="text"
                    id="searchUserInput"
                    placeholder="Pseudo de l'utilisateur"
                    required
                    aria-label="Rechercher un utilisateur"
                    onkeyup="autocompleteUser()"
                    list="datalist" />

            <!-- Liste déroulante dynamique des résultats -->
            <datalist id="datalist">
                <?php foreach ($pseudoList as $pseudo) : ?>
                    <option value="<?= htmlspecialchars($pseudo); ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <!-- Bouton de validation -->
            <button type="button" id="validateBtn" onclick="validatePseudo()">Valider le pseudo</button>

            <!-- Affichage du pseudo validé -->
            <div id="selectedPseudo" class="selected-pseudo" style="display: none;">
                <p><strong>Pseudo sélectionné :</strong> <span id="pseudoDisplay"></span></p>

                <!-- Ajouter les boutons (croix rouge et check vert) à droite du pseudo -->
                <button type="button" id="validateBtn" class="btn-validate" onclick="sendFriendRequest()">✔</button>
                <button type="button" id="cancelBtn" class="btn-cancel" onclick="cancelSelection()">❌</button>
            </div>
            <ul id="autocompleteResults" class="autocomplete-results"></ul>
        </div>
    </form>
</div>
<script>
    // Fonction pour valider et afficher le pseudo
    function validatePseudo() {
        var input = document.getElementById("searchUserInput");
        var selectedPseudo = input.value.trim();

        if (selectedPseudo) {
            // Afficher le pseudo sélectionné sous le champ de saisie
            document.getElementById("selectedPseudo").style.display = "block";
            document.getElementById("pseudoDisplay").innerText = selectedPseudo;
        } else {
            // Si aucun pseudo n'est sélectionné, masquer l'affichage
            document.getElementById("selectedPseudo").style.display = "none";
        }
    }

    // Fonction d'autocomplétion (si nécessaire)
    function autocompleteUser() {
        // Vous pouvez ajouter votre logique d'autocomplétion ici
    }
    function cancelSelection() {
        // Réinitialiser le champ de texte
        document.getElementById("searchUserInput").value = '';

        // Masquer la zone d'affichage du pseudo sélectionné
        document.getElementById("selectedPseudo").style.display = "none";

        // Supprimer toute autre logique si nécessaire
    }
</script>
<!--<script>-->
<!--    function autocompleteUser() {-->
<!--        var input = document.getElementById('searchUserInput').value;-->
<!--        var resultsList = document.getElementById('autocompleteResults');-->
<!---->
<!--        // Si l'input est vide, vider les suggestions-->
<!--        if (input.length === 0) {-->
<!--            resultsList.innerHTML = '';-->
<!--            return;-->
<!--        }-->
<!---->
<!--        // Créer une requête AJAX pour récupérer les pseudos-->
<!--        var xhr = new XMLHttpRequest();-->
<!--        xhr.open('POST', 'profile.php', true); // Remplace par le chemin de ton script PHP-->
<!--        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');-->
<!---->
<!--        // Gérer la réponse du serveur-->
<!--        xhr.onload = function() {-->
<!--            if (xhr.status == 200) {-->
<!--                var pseudos = JSON.parse(xhr.responseText); // Supposons que ton PHP renvoie un tableau JSON-->
<!---->
<!--                // Vider les résultats précédents-->
<!--                resultsList.innerHTML = '';-->
<!---->
<!--                // Ajouter les nouveaux résultats-->
<!--                pseudos.forEach(function(pseudo) {-->
<!--                    var li = document.createElement('li');-->
<!--                    li.textContent = pseudo;-->
<!--                    resultsList.appendChild(li);-->
<!--                });-->
<!--            }-->
<!--        };-->
<!---->
<!--        // Envoyer la requête-->
<!--        xhr.send('fetch_pseudo=' + encodeURIComponent(input));-->
<!--    }-->
<!--</script>-->

<!---->

<!--</div>-->

</main>

</body>
</html>
