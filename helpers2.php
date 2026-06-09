<?php
/**
 * NextMind Academy – Fonctions utilitaires
 * Chemin à placer en autre fichier : C:/xampp/htdocs/nextacademy_3/helpers2.php
 */
function setCORS(): void {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Répondre immédiatement aux pré-vols OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
// Envoyer une réponse JSON avec le code HTTP indiqué.
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
// Lire et décode le corps JSON de la requête.
function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

/**
 * Calculer la moyenne pondérée d'un tableau de notes.
 * * @param array $listeNotes Exemple : [['note' => 15, 'coefficient' => 2], ['note' => 12, 'coefficient' => 1]]
 * @return float|null
 */
function calcMoyennePonderee(array $listeNotes): ?float {
    $totalNotesPonderees = 0;
    $totalCoefficients = 0;

    foreach ($listeNotes as $item) {
        if (isset($item['note']) && $item['note'] !== null) {
            $coeff = isset($item['coefficient']) ? floatval($item['coefficient']) : 1.0;
            $totalNotesPonderees += floatval($item['note']) * $coeff;
            $totalCoefficients += $coeff;
        }
    }

    if ($totalCoefficients === 0) {
        return null; // Éviter la division par zéro si aucune note n'est présente
    }

    return round($totalNotesPonderees / $totalCoefficients, 2);
}
// Retourner la mention correspondant à une moyenne.
function getMention(?float $moy): string {
    if ($moy === null) return '—';
    if ($moy >= 16)   return 'Très bien';
    if ($moy >= 14)   return 'Bien';
    if ($moy >= 12)   return 'Assez bien';
    if ($moy >= 10)   return 'Passable';
    return 'Insuffisant';
}

function hashPwd(string $pwd): string {
    return md5($pwd);
}
?>