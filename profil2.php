<?php
/**
 * NextMind Academy – API Profil Utilisateur
 * GET /profile2.php?etudiant_id=1
 */
require_once __DIR__ . '/database2.php';
require_once __DIR__ . '/helpers2.php';
setCORS();

$etudiant_id = intval($_GET['etudiant_id'] ?? 0);
if (!$etudiant_id) jsonResponse(['error' => 'etudiant_id manquant'], 400);

$db = getDB();

// Récupérer les infos utilisateur et étudiant en une seule requête
$stmt = $db->prepare("
    SELECT 
        u.id, u.prenom, u.nom, u.email, u.telephone, u.date_naissance, 
        u.adresse, u.ville, u.pays, u.bio, u.linkedin, u.github,
        e.cne, e.cin, e.classe, e.date_inscription
    FROM utilisateurs u
    LEFT JOIN etudiants e ON u.id = e.utilisateur_id
    WHERE e.id = ?
");
$stmt->execute([$etudiant_id]);
$profil = $stmt->fetch();

if (!$profil) {
    jsonResponse(['error' => 'Profil non trouvé'], 404);
}

jsonResponse($profil, 200);
?>