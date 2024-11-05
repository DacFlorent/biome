<?php
require_once "../controllers/profile.php";
$pseudo = null;
$user = new User();
// Vérifier si le bouton a été cliqué
if (isset($_POST['fetch_pseudo'])) {
    $pseudo = $user->getFirstUserPseudo();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateInfo'])) {
    $pseudo = trim($_POST['pseudo']);

    // Créez une instance de l'utilisateur
    $user = new User();

    // Essayez de mettre à jour le pseudo
    if ($user->updatePseudo($pseudo)) {
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_users'])) {
    // Vérifiez si 'pseudo' existe dans le tableau $_POST
    if (isset($_POST['pseudo'])) {
        $pseudo = $_POST['pseudo'];
        $newUser = $user->addUser($pseudo);

        if ($newUser) {
            // Message de succès
            $notification = [
                "title" => "infoAdded",
                "message" => "Utilisateur ajouté avec succès"
            ];
        } else {
            // Message d'erreur
            $notification = [
                "title" => "infoError",
                "message" => "Une erreur est survenue lors de l'ajout de l'utilisateur."
            ];
        }
    } else {
        // Message d'erreur si le pseudo n'est pas défini
        $notification = [
            "title" => "infoError",
            "message" => "Le pseudo est requis."
        ];
    }
}
?>


<div class="h-full flex flex-row-to-col gap-2">
    <div class="flex flex-col gap-2">
        <h1>Ajoute un utilisateur</h1>
        <form method="POST" action="profile.php">
            <input type="text" name="pseudo" placeholder="Entrez le pseudo" required>
            <button type="submit" name="add_users">Ajouter un utilisateur</button>
        </form>
    </div>
</div>
<?php if (isset($notification)): ?>
    <p class="<?= $notification['title'] === 'infoError' ? 'text-danger' : 'text-success' ?>">
        <?= htmlspecialchars($notification['message']) ?>
    </p>
<?php endif; ?>

</div>
<div class="h-full flex flex-row-to-col gap-2">
    <div class="w-full flex flex-column gap-2">
        <h2>Chercher les pseudos</h2>
        <form action="profile.php" method="post">
            <button type="submit" name="fetch_pseudo">Chercher</button>
        </form>

        <!-- Afficher le pseudo si disponible -->
        <?php if ($pseudo !== null): ?>
            <p>Premier pseudo : <?= htmlspecialchars($pseudo) ?></p>
        <?php endif; ?>

        <h3 class="bold">Mettre à jour vos informations</h3>
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
    </div>
</div>
<!---->
<!--    <div class="w-full flex flex-column gap-2">-->
<!--        <h3 class="bold">Update your password</h3>-->
<!--        <form action="" method="post" class="flex flex-column gap-2">-->
<!--            <div class="rounded-box flex flex-column gap-2">-->
<!--                <div class="flex flex-column">-->
<!--                    <label for="password" class="bold">Password</label>-->
<!--                    <input type="password" name="password" id="password" placeholder="Password" class="input-form" aria-label="Password">-->
<!--                </div>-->
<!--                <div class="flex flex-column">-->
<!--                    <label for="confirmPassword" class="bold">Confirm password</label>-->
<!--                    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm password" class="input-form" aria-label="Confirm password">-->
<!--                </div>-->
<!--                --><?php //if (isset($notification) && $notification["title"] === "passwordUpdated"): ?>
<!--                    <div class="h-full flex items-center">-->
<!--                        <p class="italic bold font-sm w-full center --><?php //= $notification['state'] === 'success' ? 'text-success' : 'text-alert'; ?><!--">--><?php //= $notification["message"] ?><!--</p>-->
<!--                    </div>-->
<!--                --><?php //endif; ?>
<!--            </div>-->
<!--            <div class="center">-->
<!--                <button type="submit" name="updatePassword" class="submit-button" aria-label="Mettre à jour le mot de passe">Mettre à jour</button>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--    <div class="w-full flex flex-column gap-2">-->
<!--        <h3 class="bold">Add Friends</h3>-->
<!--        <form name="updatePseudo" class="rounded-box flex flex-column gap-3"-->
<!--              aria-label="Formulaire de modification des privilèges">-->
<!--            <div class="flex flex-column gap-1">-->
<!--                <label for="selectPseudoForUpdateType">Rechercher un utilisateur</label>-->
<!--                <input-->
<!--                        class="input-form"-->
<!--                        list="datalist"-->
<!--                        id="selectPseudoForUpdateType"-->
<!--                        placeholder="Pseudo de l'utilisateur"-->
<!--                        required-->
<!--                        aria-label="Rechercher un utilisateur"/>-->
<!--                <datalist id="datalist">-->
<!--                    --><?php //foreach ($pseudoList as $pseudo) : ?>
<!--                        <option value="--><?php //= $pseudo; ?><!--"></option>-->
<!--                    --><?php //endforeach; ?>
<!--                </datalist>-->
<!--            </div>-->
<!--            <div class="flex flex-column gap-1">-->
<!--                <label for="pseudoForUpdateType">Sélectionner un niveau de privilèges</label>-->
<!--                <select id="pseudoForUpdateType" class="input-form pointer" required-->
<!--                        aria-label="Sélectionner un niveau de privilèges">-->
<!--                    <option value="USER">Utilisateur</option>-->
<!--                    <option value="ADMIN">Administrateur</option>-->
<!--                </select>-->
<!--                <div id="feedbackForUpdateType" class="italic bold font-sm w-full center text-success"-->
<!--                     role="status"></div>-->
<!--                <div class="flex justify-center">-->
<!--                    <button type="submit" class="submit-button" aria-label="Modifier les privilèges">Modifier</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
<!--    <div>-->
<!--        <h3 class="bold mb-1">Delete Friends</h3>-->
<!--        <form name="deleteUser" class="rounded-box flex flex-column gap-3" aria-label="Formulaire de suppression d'utilisateur">-->
<!--            <div class="flex flex-column gap-1">-->
<!--                <label for="searchDeleteUser">Rechercher un utilisateur</label>-->
<!--                <input-->
<!--                        class="input-form"-->
<!--                        list="datalist"-->
<!--                        id="searchDeleteUser"-->
<!--                        placeholder="Pseudo de l'utilisateur"-->
<!--                        required-->
<!--                        aria-label="Rechercher un utilisateur" />-->
<!--                <datalist id="datalist">-->
<!--                    --><?php //foreach ($usernameList as $username) : ?>
<!--                        <option value="--><?php //= $username; ?><!--"></option>-->
<!--                    --><?php //endforeach; ?>
<!--                </datalist>-->
<!--            </div>-->
<!--            <div class="flex flex-column gap-1">-->
<!--                <label for="selectDeleteUser">Action à réaliser</label>-->
<!--                <select id="selectDeleteUser" class="input-form pointer" required aria-label="Sélectionner une action">-->
<!--                    <option value="anonymisation">Conserver ses messages et anonymiser leur auteur</option>-->
<!--                    <option value="deletion">Supprimer ses messages et supprimer leur auteur</option>-->
<!--                </select>-->
<!--                <div id="feedbackForDeleteUser" class="italic bold font-sm w-full center text-success" role="status"></div>-->
<!--                <div class="flex justify-center">-->
<!--                    <button type="submit" name="deleteUser" class="submit-button mt-1" aria-label="Supprimer l'utilisateur">Supprimer</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
</main>

</body>
</html>
