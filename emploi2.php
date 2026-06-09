<?php
/**
 * NextMind Academy – API Emploi du temps lié à la base de données
 * Chemin à placer en autre fichier : C:/xampp/htdocs/nextacademy_3/emploi2.php
 */
require_once __DIR__ . '/database2.php';
require_once __DIR__ . '/helpers2.php';
setCORS();

$etudiant_id = intval($_GET['etudiant_id'] ?? 0);
if (!$etudiant_id) {
    jsonResponse(['error' => 'etudiant_id manquant'], 400);
}

$db = getDB();

try {
    // Récupération la classe de l'étudiant connecté
    $stmtClass = $db->prepare("SELECT classe FROM etudiants WHERE id = ?");
    $stmtClass->execute([$etudiant_id]);
    $etudiant = $stmtClass->fetch();
    
    if (!$etudiant) {
        jsonResponse(['error' => 'Étudiant introuvable'], 404);
    }
    $classe = $etudiant['classe'];

    // Récupérer les séances de cours avec une jointure 
    $stmt = $db->prepare("
        SELECT p.jour, p.heure_debut, p.salle, m.nom AS matiere 
        FROM planning p
        JOIN matieres m ON p.matiere_id = m.id
        WHERE p.classe = ?
    ");
    $stmt->execute([$classe]);
    $seances = $stmt->fetchAll();

    jsonResponse([
        'classe'  => $classe,
        'seances' => $seances
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Erreur SQL : ' . $e->getMessage()], 500);
}
?>