<?php
require 'config.php';

// Pega os dados enviados pelo JavaScript (JSON)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    $listId = $data['list_id'];
    $order  = $data['order']; // Array com os IDs na nova ordem [5, 2, 8...]

    // Prepara a query SQL
    // Atualizamos tanto a COLUNA (task_list_id) quanto a POSIÇÃO
    $sql = "UPDATE cards SET position = :pos, task_list_id = :list_id WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Loop para salvar a nova posição de cada card
    foreach ($order as $position => $cardId) {
        $stmt->execute([
            ':pos'     => $position,
            ':list_id' => $listId,
            ':id'      => $cardId
        ]);
    }

    echo json_encode(['status' => 'sucesso']);
}
?>