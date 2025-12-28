<?php
require 'config.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data && !empty($data['title'])) {
    $listId = $data['list_id'];
    $title  = $data['title'];

    // Insere no banco
    $sql = "INSERT INTO cards (task_list_id, title, position) VALUES (:list_id, :title, 999)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':list_id' => $listId,
        ':title'   => $title
    ]);

    // Devolve o ID gerado para o JavaScript usar
    echo json_encode([
        'id' => $pdo->lastInsertId(),
        'title' => $title
    ]);
}
?>