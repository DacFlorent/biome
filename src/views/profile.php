<?php
require_once "../controllers/profile.php";

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'JohnDoe';
$_SESSION['points'] = 100;



$pseudo = null;
$user = new User();
$pseudos = [];

if (isset($_SESSION['user_id'])) {
    $pseudo = $_SESSION['username'];
    $Points = $_SESSION['points'];
} else {
    $pseudo = '';
    $Points = "0";
}
if (isset($_POST['fetch_pseudo'])) {
    $pseudos = $user->getAllPseudo();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateInfo'])) {
    $pseudo = trim($_POST['pseudo']);

    $user = new User();

    $result = $user->updatePseudo($pseudo);

    if (isset($result['error'])) {
        $notification = [
            "title" => "infoError",
            "message" => $result['error']
        ];
    } elseif ($result) {
        $notification = [
            "title" => "infoUpdated",
            "message" => "Votre pseudo a été mis à jour avec succès."
        ];
    } else {
        $notification = [
            "title" => "infoError",
            "message" => "Une erreur est survenue lors de la mise à jour de votre pseudo."
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePassword'])) {
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    if ($password === $confirmPassword) {
        $user = new User();

        $result = $user->updatePassword($password);

        if (isset($result['error'])) {
            $notification = [
                "title" => "passwordError",
                "message" => $result['error']
            ];
        } elseif ($result) {
            $notification = [
                "title" => "passwordUpdated",
                "message" => "Votre mot de passe a été mis à jour avec succès."
            ];
        } else {
            $notification = [
                "title" => "passwordError",
                "message" => "Une erreur est survenue lors de la mise à jour de votre mot de passe."
            ];
        }
    } else {
        $notification = [
            "title" => "passwordError",
            "message" => "Les mots de passe ne correspondent pas."
        ];
    }
}
if (isset($_POST['fetch_pseudo'])) {
    $input = $_POST['fetch_pseudo'];

    $pdo = getDBConnection();

    $query = "SELECT pseudo FROM User WHERE pseudo LIKE :input LIMIT 20";
    $stmt = $pdo->prepare($query);

    $stmt->bindValue(':input', '%' . $input . '%', PDO::PARAM_STR);
    $stmt->execute();

    $pseudos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (is_array($pseudos) && !empty($pseudos)) {
        echo json_encode($pseudos);
    } else {
        echo json_encode([]);
    }
    exit();
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
        <button type=   "submit" name="updateInfo" class="submit-button" aria-label="Mettre à jour les informations">Mettre à jour</button>
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
$pdo = getDBConnection();

$sql = "SELECT id, pseudo FROM User";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$pseudoList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Status = $_POST['status'];
    $IdReceiver = $_POST['idReceiver'];
    $IdSender = $_POST['idSender'];
    $CreatedAt = date("Y-m-d H:i:s");

    // Connexion à la base de données
    $pdo = getDBConnection();
    if ($pdo->connect_error) {
        die("La connexion a échoué: " . $pdo->connect_error);
    }

    // Préparer la requête d'insertion
    $stmt = $pdo->prepare("INSERT INTO FriendRequest (Status, IdReceiver, IdSender, CreatedAt) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $Status, $IdReceiver, $IdSender, $CreatedAt);

    if ($stmt->execute()) {
        echo "Demande d'ami envoyée avec succès.";
    } else {
        echo "Erreur lors de l'envoi de la demande d'ami : " . $stmt->error;
    }

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

                <datalist id="datalist" data-user-id="<?= $_SESSION['user_id']; ?>">
                    <?php foreach ($pseudoList as $user) : ?>
                        <option value="<?= htmlspecialchars($user['pseudo']); ?>" data-id="<?= htmlspecialchars($user['id']); ?>"></option>
                    <?php endforeach; ?>
                </datalist>

            <button type="button" id="validateBtn" onclick="validatePseudo()">Valider le pseudo</button>

            <div id="selectedPseudo" class="selected-pseudo" style="display: none;">
                <p><strong>Pseudo sélectionné :</strong> <span id="pseudoDisplay"></span></p>

                <form id="friendRequest" action="" method="POST" style="display: none;">
                    <input type="hidden" name="idSender" id="idSender" value="<?= $IdReceiver ?>">
                    <input type="hidden" name="idReceiver" id="idReceiver" value="<?= $IdSender; ?>">
                    <input type="hidden" name="status" id="status" value="en attente">

                <button type="submit" id="validateBtn" class="btn-validate" onclick="sendFriendRequest()">✔</button>
                <button type="button" id="cancelBtn" class="btn-cancel" onclick="cancelSelection()">❌</button>
                </form>
            </div>
            <ul id="autocompleteResults" class="autocomplete-results"></ul>
        </div>
    </form>
</div>
<script>

    // Fonction pour valider et afficher le pseudo
    function validatePseudo() {
        var input = document.getElementById("searchUserInput");
        var selectedPseudo = input.value.trim(); // Récupère la valeur de l'input, sans espaces au début et à la fin

        // Récupérer l'option correspondante dans le datalist
        var selectedOption = document.querySelector(`#datalist option[value="${selectedPseudo}"]`);

        // Si une option correspondant au pseudo est trouvée, récupérer l'ID
        var userId = selectedOption ? selectedOption.getAttribute("data-id") : null;

        var sessionUserId = document.getElementById('datalist').getAttribute('data-user-id');

        // Affichage du pseudo et de l'ID dans la console
        console.log("Pseudo sélectionné :", selectedPseudo);
        console.log("ID associé :", userId);
        console.log("UserId de la session :", sessionUserId);


        if (selectedPseudo && userId) {
            // Mettre à jour l'affichage du pseudo sélectionné
            document.getElementById("selectedPseudo").style.display = "block";
            document.getElementById("pseudoDisplay").innerText = selectedPseudo;

            // Mettre à jour les champs cachés dans le formulaire avec les bonnes valeurs
            document.getElementById("idReceiver").value = userId; // Met à jour l'ID du récepteur
            document.getElementById("idSender").value = sessionUserId; // Met à jour l'ID de l'expéditeur (userId de la session)
            document.getElementById("status").value = "en attente"; // Valeur par défaut du statut

        }
    }


    // Fonction d'autocomplétion
    function autocompleteUser() {
        const input = document.getElementById('searchUserInput');
        const selectedOption = document.querySelector(`#datalist option[value="${input.value}"]`);

        if (selectedOption) {
            const userId = selectedOption.getAttribute('data-id');
            document.getElementById('idReceiver').value = userId;
            document.getElementById('pseudoDisplay').textContent = input.value;
            document.getElementById('selectedPseudo').style.display = 'block';
        }
    }

    // Fonction demande d'amis
    function sendFriendRequest() {
        // Récupérer les valeurs de IdReceiver et IdSender depuis les éléments du formulaire
        var idReceiver = document.getElementById('idReceiver').value;
        var idSender = document.getElementById('idSender').value;
        var status = document.getElementById('status').value;

        // Afficher les valeurs en console
        console.log("IdReceiver: " + idReceiver); // Affiche l'ID du récepteur
        console.log("Status : " + status);
        console.log("idSender: " + idSender);

        const form = document.getElementById("friendRequest");

        form.submit = function () {

        };
        if (form) {
            form.style.display = "block";  // Rendre le formulaire visible
            form.submit();  // Soumettre le formulaire
        } else {
            console.error("Le formulaire est introuvable.");
        }
    }

</script>

</main>

</body>
</html>
