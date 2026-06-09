<?php
/**
 * NextMind Academy – API Statistiques 
 * Chemin à placer en autre fichier : C:/xampp/htdocs/nextacademy_3/stats2.php
 */
require_once __DIR__ . '/database2.php';
require_once __DIR__ . '/helpers2.php';
setCORS();

$etudiant_id = intval($_GET['etudiant_id'] ?? 0);
if (!$etudiant_id) jsonResponse(['error' => 'etudiant_id manquant'], 400);

$db = getDB();

// Récupération de la classe
$stmt = $db->prepare("SELECT classe FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();
$classe = $etudiant ? $etudiant['classe'] : 'Non définie';

//  Moyenne générale réelle 
$stmt = $db->prepare("
    SELECT ROUND(AVG((IFNULL(controle1, 0) + IFNULL(controle2, 0) + IFNULL(examen, 0)) / 
    (IF(controle1 IS NULL, 0, 1) + IF(controle2 IS NULL, 0, 1) + IF(examen IS NULL, 0, 1))), 2) AS moy_gen 
    FROM notes 
    WHERE etudiant_id = ?
");
$stmt->execute([$etudiant_id]);
$moy_gen = $stmt->fetch()['moy_gen'];

// Nombre de matières distinctes
$stmt = $db->prepare("SELECT COUNT(DISTINCT matiere) AS n FROM notes WHERE etudiant_id = ?");
$stmt->execute([$etudiant_id]);
$nb_matieres = intval($stmt->fetch()['n']);

// des absences par statut
$stmt = $db->prepare("SELECT COUNT(*) AS n FROM absences WHERE etudiant_id = ?");
$stmt->execute([$etudiant_id]);
$abs_total = intval($stmt->fetch()['n']);

$stmt = $db->prepare("SELECT COUNT(*) AS n FROM absences WHERE etudiant_id = ? AND statut = 'Justifiée'");
$stmt->execute([$etudiant_id]);
$abs_just = intval($stmt->fetch()['n']);

$stmt = $db->prepare("SELECT COUNT(*) AS n FROM absences WHERE etudiant_id = ? AND statut = 'Non justifiée'");
$stmt->execute([$etudiant_id]);
$abs_non_just = intval($stmt->fetch()['n']);

$stmt = $db->prepare("SELECT COUNT(*) AS n FROM absences WHERE etudiant_id = ? AND statut = 'En attente'");
$stmt->execute([$etudiant_id]);
$abs_en_attente = intval($stmt->fetch()['n']);

// Meilleure et pire matière 
$stmt = $db->prepare("
    SELECT matiere, ROUND((IFNULL(controle1, 0) + IFNULL(controle2, 0) + IFNULL(examen, 0)) / 3, 2) AS calcul_moy 
    FROM notes WHERE etudiant_id = ? 
    ORDER BY calcul_moy DESC LIMIT 1
");
$stmt->execute([$etudiant_id]);
$best = $stmt->fetch();

$stmt = $db->prepare("
    SELECT matiere, ROUND((IFNULL(controle1, 0) + IFNULL(controle2, 0) + IFNULL(examen, 0)) / 3, 2) AS calcul_moy 
    FROM notes WHERE etudiant_id = ? 
    ORDER BY calcul_moy ASC LIMIT 1
");
$stmt->execute([$etudiant_id]);
$worst = $stmt->fetch();

jsonResponse([
    'classe'            => $classe,
    'moyenne_generale'  => $moy_gen !== null ? floatval($moy_gen) : null,
    'mention_generale'  => getMention($moy_gen !== null ? floatval($moy_gen) : null),
    'nb_matieres'       => $nb_matieres,
    'absences' => [
        'total'          => $abs_total,
        'justifiees'     => $abs_just,
        'non_justifiees' => $abs_non_just,
        'en_attente'     => $abs_en_attente,
    ],
    'meilleure_matiere' => $best  ? ['matiere' => $best['matiere'],  'moyenne' => floatval($best['calcul_moy'])]  : null,
    'pire_matiere'      => $worst ? ['matiere' => $worst['matiere'], 'moyenne' => floatval($worst['calcul_moy'])] : null,
]);
?>