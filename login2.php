<?php
/** Login du page index2.html vers acceuil2.html
 * Fichier : C:/xampp/htdocs/nextacademy_3/login2.php
 */
require_once __DIR__ . '/database2.php';

$db = getDB();

if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
    $email        = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe']; 

    try {
        // Recherche stricte basée sur table SQL 
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ? AND mot_de_passe = ?");
        $stmt->execute([$email, $mot_de_passe]);
        $user = $stmt->fetch();

        if ($user) {
            // Récupérer l'étudiant lié
            $stmt2 = $db->prepare("SELECT id FROM etudiants WHERE utilisateur_id = ?");
            $stmt2->execute([$user['id']]);
            $etudiant = $stmt2->fetch();

            $userInfo = json_encode([
                'id'       => $etudiant['id'] ?? 1,
                'username' => $user['prenom'] . ' ' . $user['nom'],
                'email'    => $user['email'],
            ]);

            echo "<script>
                localStorage.setItem('etudiant', " . json_encode($userInfo) . ");
                window.location.href = 'http://localhost:8080/nextacademy_3/acceuil2.html';
            </script>";
        } else {
            echo "<script>
                alert('Email ou mot de passe incorrect !');
                window.location.href = 'http://localhost:8080/nextacademy_3/index2.html';
            </script>";
        }
    } catch (PDOException $e) {
        die("Erreur connexion : " . $e->getMessage());
    }
} else {
    echo "Veuillez vous connecter via le formulaire.";
}
?>