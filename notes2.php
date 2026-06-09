<?php
/**
 * NextMind Academy – API Notes 
 * Chemin à placer en autre fichier : C:/xampp/htdocs/nextacademy_3/notes2.php
 */
require_once __DIR__ . '/database2.php';
require_once __DIR__ . '/helpers2.php';
setCORS();

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

// GET : liste des notes d'un étudiant 
if ($method === 'GET') {
    $etudiant_id = intval($_GET['etudiant_id'] ?? 0);
    if (!$etudiant_id) jsonResponse(['error' => 'etudiant_id manquant'], 400);

    $semestre = trim($_GET['semestre'] ?? '');
    
    $sql = "SELECT id, matiere, professeur, controle1, controle2, examen, semestre,
            ROUND((IFNULL(controle1, 0) + IFNULL(controle2, 0) + IFNULL(examen, 0)) / 
            (IF(controle1 IS NULL, 0, 1) + IF(controle2 IS NULL, 0, 1) + IF(examen IS NULL, 0, 1)), 2) AS note
            FROM notes 
            WHERE etudiant_id = ?";
            
    $params = [$etudiant_id];

    if ($semestre !== '') {
        $sql .= " AND semestre = ?";
        $params[] = $semestre;
    }

    // trier par ID décroissant pour avoir les "Dernières Notes" en premier
    $sql .= " ORDER BY id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());
}

?>