<?php
require_once '../config.php';
checkAdminLogin();

$projetos = $pdo->query("SELECT id, projeto_nome, aluno_nome, nivel_projeto, tags_ai, impacto_estimado, status_board FROM projects ORDER BY created_at DESC")->fetchAll();

$colunas = ['Ideia' => [], 'Construção' => [], 'Testes' => [], 'Produção' => []];
foreach ($projetos as $p) {
    if (array_key_exists($p->status_board, $colunas)) {
        $colunas[$p->status_board][] = $p;
    } else {
        $colunas['Ideia'][] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Kanban - No-Code Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .kanban-col { min-height: 500px; }
        .card.dragging { opacity: 0.5; }
    </style>
</head>
<body class="bg-[#0f172a] text-white flex h-screen overflow-hidden">
    
    <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col">
        <div class="p-6">
            <h1 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">No-Code Hub</h1>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="block px-4 py-2 hover:bg-gray-800 text-gray-300 rounded-lg">Dashboard</a>
            <a href="board.php" class="block px-4 py-2 bg-purple-600/20 text-purple-400 rounded-lg">Kanban</a>
        </nav>
    </aside>
    
    <div class="flex-1 flex flex-col h-full">
        <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center px-6"><h2 class="text-xl font-bold text-purple-400">Quadro de Projetos</h2></header>

        <div class="flex-1 p-6 overflow-x-auto">
            <div class="flex gap-6 h-full items-start">
                
                <?php foreach($colunas as $nome_col => $cards): ?>
                <div class="w-80 flex-shrink-0 bg-gray-800/50 rounded-xl p-4 flex flex-col h-full border border-gray-700/50 kanban-col" data-status="<?= $nome_col ?>">
                    <div class="flex justify-between items-center mb-4 px-1">
                        <h3 class="font-bold text-gray-200"><?= $nome_col ?></h3>
                        <span class="bg-gray-700 text-xs px-2 py-1 rounded-full"><?= count($cards) ?></span>
                    </div>
                    
                    <div class="flex-1 space-y-3 dropzone">
                        <?php foreach($cards as $card): ?>
                        <div class="card bg-gray-800 border border-gray-700 rounded-lg p-4 shadow-lg cursor-grab hover:border-purple-500/50 transition-colors" draggable="true" data-id="<?= $card->id ?>">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-semibold text-purple-400 bg-purple-500/10 px-2 py-1 rounded"><?= htmlspecialchars($card->nivel_projeto ?: 'Sem nível') ?></span>
                                <span class="text-xs text-gray-400"><?= htmlspecialchars($card->impacto_estimado) ?></span>
                            </div>
                            <h4 class="font-bold text-white mb-1"><a href="project-view.php?id=<?= $card->id ?>" class="hover:underline"><?= htmlspecialchars($card->projeto_nome) ?></a></h4>
                            <p class="text-xs text-gray-400 mb-3">por <?= htmlspecialchars($card->aluno_nome) ?></p>
                            
                            <div class="flex flex-wrap gap-1">
                                <?php 
                                $tags = array_filter(array_map('trim', explode(',', $card->tags_ai ?? '')));
                                foreach(array_slice($tags, 0, 3) as $tag): 
                                ?>
                                <span class="text-[10px] bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <script>
        const cards = document.querySelectorAll('.card');
        const dropzones = document.querySelectorAll('.dropzone');
        let draggedCard = null;

        cards.forEach(card => {
            card.addEventListener('dragstart', () => {
                draggedCard = card;
                setTimeout(() => card.classList.add('dragging'), 0);
            });
            card.addEventListener('dragend', () => {
                draggedCard = null;
                card.classList.remove('dragging');
            });
        });

        dropzones.forEach(zone => {
            zone.addEventListener('dragover', e => {
                e.preventDefault();
                zone.classList.add('bg-gray-700/30');
            });
            zone.addEventListener('dragleave', () => {
                zone.classList.remove('bg-gray-700/30');
            });
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('bg-gray-700/30');
                if (draggedCard) {
                    zone.appendChild(draggedCard);
                    
                    const cardId = draggedCard.getAttribute('data-id');
                    const newStatus = zone.parentElement.getAttribute('data-status');
                    
                    fetch('../api/update-status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: cardId, status: newStatus })
                    }).then(res => res.json()).then(data => console.log('Status atualizado!'));
                }
            });
        });
    </script>
</body>
</html>
