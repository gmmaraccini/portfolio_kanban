<?php
require 'config.php';

// Busca as listas
$stmt = $pdo->query("SELECT * FROM task_lists");
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Kanban PHP Puro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
</head>
<body class="bg-blue-600 h-screen p-10">
<h1 class="text-white text-3xl font-bold mb-8">Kanban PHP Puro</h1>

<div class="flex gap-4 overflow-x-auto h-full items-start">

    <?php foreach ($lists as $list): ?>
        <div class="bg-gray-100 w-80 rounded-lg p-4 flex-shrink-0 flex flex-col">
            <h3 class="font-bold text-gray-700 mb-4"><?php echo $list['title']; ?></h3>

            <div class="sortable-list min-h-[50px] flex-1" data-list-id="<?php echo $list['id']; ?>">
                <?php
                // Busca os cards desta lista
                $stmt = $pdo->prepare("SELECT * FROM cards WHERE task_list_id = ? ORDER BY position ASC");
                $stmt->execute([$list['id']]);
                $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php foreach ($cards as $card): ?>
                    <div class="bg-white p-3 rounded shadow mb-3 cursor-pointer hover:bg-gray-50"
                         data-id="<?php echo $card['id']; ?>">
                        <?php echo $card['title']; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4 pt-2 border-t border-gray-200">
                <input type="text"
                       id="input-lista-<?php echo $list['id']; ?>"
                       placeholder="+ Nova tarefa"
                       class="w-full p-2 rounded border border-gray-300 mb-2 text-sm focus:outline-none focus:border-blue-500">

                <button onclick="adicionarTarefa(<?php echo $list['id']; ?>)"
                        class="bg-blue-600 text-white w-full py-1 rounded text-sm hover:bg-blue-700 font-semibold">
                    Adicionar
                </button>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<script>
    // --- 1. Configuração do Arrastar e Soltar (SortableJS) ---
    const lists = document.querySelectorAll('.sortable-list');

    lists.forEach(list => {
        new Sortable(list, {
            group: 'shared', // Permite mover de uma lista para outra
            animation: 150,
            onEnd: function (evt) {
                let newListId = evt.to.getAttribute('data-list-id');
                let orderIds = [];

                // Pega a nova ordem dos IDs
                evt.to.querySelectorAll('[data-id]').forEach(el => {
                    orderIds.push(el.getAttribute('data-id'));
                });

                // Envia para o PHP atualizar
                fetch('atualizar.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        list_id: newListId,
                        order: orderIds
                    })
                });
            }
        });
    });

    // --- 2. Função para Criar Tarefa Nova (AJAX) ---
    function adicionarTarefa(listId) {
        const input = document.getElementById(`input-lista-${listId}`);
        const title = input.value;

        if (!title) return alert("Digite o nome da tarefa!");

        fetch('criar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                list_id: listId,
                title: title
            })
        })
            .then(response => response.json())
            .then(data => {
                // Cria o elemento HTML do card visualmente
                const novoCard = document.createElement('div');
                novoCard.className = 'bg-white p-3 rounded shadow mb-3 cursor-pointer hover:bg-gray-50';
                novoCard.setAttribute('data-id', data.id); // Usa o ID real do banco
                novoCard.innerText = data.title;

                // Encontra a lista certa e adiciona o card lá
                const listaContainer = document.querySelector(`.sortable-list[data-list-id='${listId}']`);
                listaContainer.appendChild(novoCard);

                // Limpa o campo de texto
                input.value = '';
            })
            .catch(error => console.error('Erro:', error));
    }
</script>
</body>
</html>