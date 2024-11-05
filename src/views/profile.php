<?php
require_once "../controllers/profile.php";
$pseudo = null;
$user = new User();
// Vérifier si le bouton a été cliqué
if (isset($_POST['fetch_pseudo'])) {
    $pseudo = $user->getFirstUserPseudo();
}

// Inclure la vue avec la variable $pseudo
?>
<div class="h-full flex flex-row-to-col gap-2">
    <div class="w-full flex flex-column gap-2">
        <h2>CHercher les pseudos</h2>
        <form action="profile.php" method="post">
            <button type="submit" name="fetch_pseudo">Chercher</button>
        </form>

        <!-- Afficher le pseudo si disponible -->
        <?php if ($pseudo !== null): ?>
            <p>Premier pseudo : <?= htmlspecialchars($pseudo) ?></p>
        <?php endif; ?>
        <h3 class="bold">Update your informations</h3>
        <form action="" method="post" class="flex flex-column gap-2">
            <div class="rounded-box flex flex-column gap-2">
                <div class="flex flex-column">
                    <label for="pseudo" class="bold">Pseudo</label>
                    <input type="text" autocomplete="off" id="pseudo" name="pseudo" class="input-form" placeholder="<?= $pseudo; ?>" aria-label="Pseudo">
                </div>
                <?php if (isset($notification) && $notification["title"] === "infoUpdated"): ?>
                    <p class="italic bold font-sm w-full center text-success"><?= $notification["message"] ?></p>
                <?php endif; ?>
            </div>
            <div class="center">
                <button type="submit" name="updateInfo" class="submit-button" aria-label="Mettre à jour les informations">Mettre à jour</button>
            </div>
        </form>
    </div>

    <div class="w-full flex flex-column gap-2">
        <h3 class="bold">Update your password</h3>
        <form action="" method="post" class="flex flex-column gap-2">
            <div class="rounded-box flex flex-column gap-2">
                <div class="flex flex-column">
                    <label for="password" class="bold">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" class="input-form" aria-label="Password">
                </div>
                <div class="flex flex-column">
                    <label for="confirmPassword" class="bold">Confirm password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm password" class="input-form" aria-label="Confirm password">
                </div>
                <?php if (isset($notification) && $notification["title"] === "passwordUpdated"): ?>
                    <div class="h-full flex items-center">
                        <p class="italic bold font-sm w-full center <?= $notification['state'] === 'success' ? 'text-success' : 'text-alert'; ?>"><?= $notification["message"] ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="center">
                <button type="submit" name="updatePassword" class="submit-button" aria-label="Mettre à jour le mot de passe">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

    <div class="w-full flex flex-column gap-2">
        <h3 class="bold">Add Friends</h3>
        <form name="updatePseudo" class="rounded-box flex flex-column gap-3"
              aria-label="Formulaire de modification des privilèges">
            <div class="flex flex-column gap-1">
                <label for="selectPseudoForUpdateType">Rechercher un utilisateur</label>
                <input
                        class="input-form"
                        list="datalist"
                        id="selectPseudoForUpdateType"
                        placeholder="Pseudo de l'utilisateur"
                        required
                        aria-label="Rechercher un utilisateur"/>
                <datalist id="datalist">
                    <?php foreach ($pseudoList as $pseudo) : ?>
                        <option value="<?= $pseudo; ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="flex flex-column gap-1">
                <label for="pseudoForUpdateType">Sélectionner un niveau de privilèges</label>
                <select id="pseudoForUpdateType" class="input-form pointer" required
                        aria-label="Sélectionner un niveau de privilèges">
                    <option value="USER">Utilisateur</option>
                    <option value="ADMIN">Administrateur</option>
                </select>
                <div id="feedbackForUpdateType" class="italic bold font-sm w-full center text-success"
                     role="status"></div>
                <div class="flex justify-center">
                    <button type="submit" class="submit-button" aria-label="Modifier les privilèges">Modifier</button>
                </div>
            </div>
        </form>
    </div>
    <div>
        <h3 class="bold mb-1">Delete Friends</h3>
        <form name="deleteUser" class="rounded-box flex flex-column gap-3" aria-label="Formulaire de suppression d'utilisateur">
            <div class="flex flex-column gap-1">
                <label for="searchDeleteUser">Rechercher un utilisateur</label>
                <input
                        class="input-form"
                        list="datalist"
                        id="searchDeleteUser"
                        placeholder="Pseudo de l'utilisateur"
                        required
                        aria-label="Rechercher un utilisateur" />
                <datalist id="datalist">
                    <?php foreach ($usernameList as $username) : ?>
                        <option value="<?= $username; ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="flex flex-column gap-1">
                <label for="selectDeleteUser">Action à réaliser</label>
                <select id="selectDeleteUser" class="input-form pointer" required aria-label="Sélectionner une action">
                    <option value="anonymisation">Conserver ses messages et anonymiser leur auteur</option>
                    <option value="deletion">Supprimer ses messages et supprimer leur auteur</option>
                </select>
                <div id="feedbackForDeleteUser" class="italic bold font-sm w-full center text-success" role="status"></div>
                <div class="flex justify-center">
                    <button type="submit" name="deleteUser" class="submit-button mt-1" aria-label="Supprimer l'utilisateur">Supprimer</button>
                </div>
            </div>
        </form>
    </div>
</main>

</body>
</html>
