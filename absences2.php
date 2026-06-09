<?php
/**
 * NextMind Academy – API Absences 
 */
require_once __DIR__ . '/database2.php';
require_once __DIR__ . '/helpers2.php';
setCORS();

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

// GET : absences d'un étudiant
if ($method === 'GET') {
    $etudiant_id = intval($_GET['etudiant_id'] ?? 0);
    if (!$etudiant_id) jsonResponse(['error' => 'etudiant_id manquant'], 400);

    $statut   = trim($_GET['statut']   ?? '');
    $semestre = trim($_GET['semestre'] ?? '');

    $query  = "SELECT * FROM absences WHERE etudiant_id = ?";
    $params = [$etudiant_id];

    if ($statut   !== '') { $query .= " AND statut = ?";   $params[] = $statut; }
    if ($semestre !== '') { $query .= " AND semestre = ?"; $params[] = $semestre; }

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());
}

?>