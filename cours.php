<?php
/**
 * NextMind Academy – API Cours
 */
require_once __DIR__ . '/database2.php';
require_once __DIR__ . '/helpers2.php';
setCORS();

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

// GET : récupérer tous les cours 
if ($method === 'GET') {
    $stmt = $db->prepare("SELECT id, titre, description, professeur, couleur_badge FROM cours ORDER BY id");
    $stmt->execute();
    jsonResponse($stmt->fetchAll());
}

// POST : ajouter un cours (optionnel, pour admin) 
if ($method === 'POST') {
    $data = getBody();
    $titre = trim($data['titre'] ?? '');
    $description = trim($data['description'] ?? '');
    $professeur = trim($data['professeur'] ?? '');
    $couleur_badge = trim($data['couleur_badge'] ?? 'primary');

    if (!$titre) jsonResponse(['error' => 'titre requis'], 400);

    $stmt = $db->prepare("
        INSERT INTO cours (titre, description, professeur, couleur_badge)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$titre, $description, $professeur, $couleur_badge]);
    jsonResponse(['message' => 'Cours ajouté', 'id' => $db->lastInsertId()], 201);
}
?>