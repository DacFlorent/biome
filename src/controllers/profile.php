<?php
// Login controller
require_once "../config/config.php";
$user = new user();


// Set notification
$notification = null;

class User
{
    public $ID;
    public $pseudo;
    public $CreateAt;
    public $Points;
    public $Role;
    public $passwordHash;

    // ======================= //
    // ===== Get methods ===== //
    // ======================= //

    public function getUserById($ID)
    {
        try {
            // Get user by ID
            $sql = "SELECT * FROM User WHERE ID = :userId";
            $query = Database::queryAssoc($sql, [
                ':userId' => $ID
            ]);
            // If no result, return null
            if (is_null($query)) {
                return null;
            }
            // Return associated array of user
            return $query[0];
        } catch (PDOException $e) {
            throw new Error("Error in getUserById: " . $e->getMessage());
        }
    }

    public function getUserByPseudo($pseudo)
    {
        try {
            $sql = "SELECT * FROM User WHERE pseudo = :pseudo";
            $query = Database::queryAssoc($sql, [
                ':pseudo' => $pseudo
            ]);
            // If no result, return null
            if (is_null($query)) {
                return null;
            }
            // Return instance object
            return $query[0];
        } catch (PDOException $e) {
            throw new Error("Error in getUserByPseudo: " . $e->getMessage());
        }
    }


    function getFirstUserPseudo() {
        try {
            $pdo = getDBConnection();
            $sql = "SELECT pseudo FROM User LIMIT 1";
            $query = $pdo->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['pseudo'] : null;
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }



    public function getAllPseudo()
    {
        try {
            // Obtenir la connexion à la base de données
            $pdo = getDBConnection();

            // Préparer la requête SQL pour sélectionner tous les pseudos
            $sql = "SELECT pseudo FROM User";
            $stmt = $pdo->prepare($sql);

            // Exécuter la requête
            $stmt->execute();

            // Récupérer tous les pseudos dans un tableau associatif
            $pseudoList = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Vérifier si des pseudos ont été récupérés
            if ($pseudoList) {
                // Extraire les pseudos du tableau associatif
                return array_column($pseudoList, 'pseudo'); // Retourne uniquement les pseudos
            }

            return null; // Aucune donnée trouvée
        } catch (PDOException $e) {
            throw new Exception("Failed to retrieve pseudos: " . $e->getMessage());
        }
    }

    // ======================= //
    // ===== Add & Delete users methods ===== //
    // ======================= //

//    public function addUser($pseudo) {
//        try {
//            // Obtenir la connexion à la base de données
//            $pdo = getDBConnection();
//
//            // Vérifier si le pseudo existe déjà dans la base de données
//            $sql = "SELECT * FROM User WHERE pseudo = :pseudo";
//            $stmt = $pdo->prepare($sql);
//            $stmt->execute([':pseudo' => $pseudo]);
//            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
//
//            // Si le pseudo existe déjà, retourner un message ou une valeur pour indiquer un doublon
//            if ($existingUser) {
//                return ["error" => "Ce pseudo est déjà pris."];
//            }
//
//            // Préparer la requête SQL pour insérer un nouvel utilisateur
//            $sql = "INSERT INTO User (pseudo) VALUES (:pseudo)";
//            $stmt = $pdo->prepare($sql);
//
//            // Exécuter la requête avec le paramètre
//            $result = $stmt->execute([':pseudo' => $pseudo]);
//
//            // Vérifier si l'insertion a réussi
//            if ($result) {
//                // Récupérer l'ID de l'utilisateur ajouté
//                $userId = $pdo->lastInsertId();
//
//                // Récupérer les informations de l'utilisateur ajouté pour confirmation
//                $sql = "SELECT * FROM User WHERE ID = :userId";
//                $stmt = $pdo->prepare($sql);
//                $stmt->execute([':userId' => $userId]);
//                $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
//
//                // Vérifier si l'utilisateur a bien été récupéré
//                if ($newUser) {
//                    return $newUser; // Retourne les détails de l'utilisateur ajouté
//                }
//            }
//
//        } catch (PDOException $e) {
//            // Gérer l'exception en cas d'erreur de base de données
//            return ["error" => "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage()];
//        }
//    }

    public function showProfile()
    {
        try {
            // Créer une instance de la classe User
            $user = new User();

            // Récupérer tous les pseudos
            $usernameList = $user->getAllPseudo();

            // Passer la liste des pseudos à la vue
            require_once 'views/profile.php';
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // ======================= //
    // ===== Update methods ===== //
    // ======================= //

    public function updatePseudo($pseudo) {
        try {
            // Obtenir la connexion à la base de données
            $pdo = getDBConnection();

            // Vérifier si le pseudo est déjà pris
            $checkQuery = "SELECT COUNT(*) FROM User WHERE pseudo = :pseudo";
            $stmt = $pdo->prepare($checkQuery);
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->execute();

            // Si le pseudo existe déjà, retourner une erreur
            if ($stmt->fetchColumn() > 0) {
                return ['error' => 'Ce pseudo est déjà pris.'];
            }

            // Utiliser un ID fixe pour l'utilisateur (ou remplacer par la session si disponible)
            $userId = 1; // Remplacez par l'ID de l'utilisateur actuel

            // Préparer la requête SQL pour mettre à jour le pseudo
            $sql = "UPDATE User SET pseudo = :pseudo WHERE ID = :userId";
            $stmt = $pdo->prepare($sql);

            // Exécuter la requête avec les paramètres
            $result = $stmt->execute([
                ':userId' => $userId,
                ':pseudo' => $pseudo
            ]);

            // Vérifier si la mise à jour a réussi
            if ($result) {
                $this->pseudo = $pseudo;
                return true; // Indiquer que la mise à jour a réussi
            }

            return false; // Indiquer que la mise à jour a échoué
        } catch (PDOException $e) {
            throw new Exception("Failed to update pseudo: " . $e->getMessage());
        }
    }


    public function updatePassword($password) {
        try {
            // Obtenir la connexion à la base de données via getDBConnection()
            $pdo = getDBConnection();  // Cette fonction retourne déjà la connexion PDO

            // Hash du mot de passe avant de le stocker
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Utiliser un ID fixe pour l'utilisateur (ou remplacer par la session si disponible)
            $userId = $_SESSION['user_id'];  // Utilise l'ID de l'utilisateur connecté depuis la session

            // Préparer la requête SQL pour mettre à jour le mot de passe
            $sql = "UPDATE User SET password = :password WHERE ID = :userId";
            $stmt = $pdo->prepare($sql);

            // Exécuter la requête avec les paramètres
            $result = $stmt->execute([
                ':userId' => $userId,
                ':password' => $hashedPassword
            ]);

            // Vérifier si la mise à jour a réussi
            if ($result) {
                $this->passwordHash = $hashedPassword;  // Mise à jour de l'attribut
                return true; // Indiquer que la mise à jour a réussi
            }

            return ['error' => 'La mise à jour du mot de passe a échoué.'];
        } catch (PDOException $e) {
            throw new Exception("Échec de la mise à jour du mot de passe : " . $e->getMessage());
        }
    }

    public function fillUserInstance($userData)
    {
        if ($userData) {
            $this->ID = $userData['ID'];
            $this->pseudo = $userData['pseudo'];
            $this->CreateAt = $userData['CreateAt'];
            $this->Points = $userData['Points'];
            $this->Role = $userData['Role'];
            $this->passwordHash = $userData['passwordHash'];
        }
    }
}

// Check if a form has been submitted
if (isset($_POST['login'])) {
    try {
        $fieldList = ['username', 'password'];

        // Sanitize data
        foreach ($fieldList as $field) {
            ${$field} = htmlspecialchars($_POST[$field], ENT_QUOTES, 'UTF-8');
        }

        // Create user object and retrieve data
        $user = new User();
        $userData = $user->getUserById($_SESSION['userId']);
        $user->fillUserInstance($userData);

        $notification = null;

        if (isset($_POST["updateInfo"])) {
            // Update pseudo if needed
            $pseudo = htmlspecialchars($_POST['pseudo'], ENT_QUOTES, 'UTF-8');
            if (!empty($pseudo) && $pseudo !== $user->pseudo) {
                $user->updatePseudo($pseudo);
            }

            $notification = [
                "state" => "success",
                "title" => "infoUpdated",
                "message" => "Vos informations ont été mises à jour."
            ];
        } elseif (isset($_POST["updatePassword"])) {
            try {
                $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
                $confirmPassword = htmlspecialchars($_POST['confirmPassword'], ENT_QUOTES, 'UTF-8');

                if (strlen($password) < 8 || $password !== $confirmPassword) {
                    throw new Error("Passwords do not match or are too short.");
                }

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $user->updatePassword($passwordHash);

                $notification = [
                    "state" => "success",
                    "title" => "passwordUpdated",
                    "message" => "Votre mot de passe a été mis à jour."
                ];
            } catch (Throwable $e) {
                $notification = [
                    "state" => "error",
                    "title" => "passwordUpdateFailed",
                    "message" => "Erreur lors de la mise à jour du mot de passe."
                ];
            }
        }
    } catch (Throwable $e) {
        // Handle any exceptions in processing the form
        $notification = [
            "state" => "error",
            "title" => "formProcessingError",
            "message" => "Une erreur s'est produite lors du traitement du formulaire."
        ];
    }
}
