<?php
require_once '../config.php';
checkAdminLogin();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) die("Projeto não encontrado.");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Projeto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f172a] text-white p-8">
    
    <div class="max-w-4xl mx-auto">
        <a href="board.php" class="text-purple-400 mb-6 inline-block">⬅ Voltar ao Kanban</a>
        
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold"><?= htmlspecialchars($p->projeto_nome) ?></h1>
                <p class="text-gray-400">Por <?= htmlspecialchars($p->aluno_nome) ?> (<?= htmlspecialchars($p->aluno_nivel) ?>)</p>
            </div>
            <span class="bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-bold shadow-lg shadow-blue-500/30">
                <?= htmlspecialchars($p->status_board) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                <h2 class="text-xl font-bold mb-4 text-purple-400 border-b border-gray-700 pb-2">Detalhes</h2>
                <div class="space-y-4">
                    <div><span class="block text-xs text-gray-400 uppercase">Objetivo</span>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($p->projeto_objetivo) ?></p></div>
                    
                    <div><span class="block text-xs text-gray-400 uppercase">Descrição</span>
                    <p class="text-sm bg-gray-900 p-3 rounded mt-1 border border-gray-700"><?= nl2br(htmlspecialchars($p->projeto_descricao)) ?></p></div>
                    
                    <div><span class="block text-xs text-gray-400 uppercase">Ferramentas</span>
                    <p class="text-sm"><?= htmlspecialchars($p->ferramentas_usadas) ?></p></div>
                </div>
            </div>

            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">🤖</div>
                <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
                    <h2 class="text-xl font-bold text-blue-400">Análise do Mentor IA</h2>
                    <button id="btn-reanalisar" class="text-xs bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-white transition">Reanalisar</button>
                </div>
                
                <div class="space-y-4" id="ia-content">
                    <div class="grid grid-cols-2 gap-4">
                        <div><span class="block text-xs text-gray-400 uppercase">Nível</span>
                        <p class="font-bold text-purple-400"><?= htmlspecialchars($p->nivel_projeto ?: '-') ?></p></div>
                        <div><span class="block text-xs text-gray-400 uppercase">Impacto</span>
                        <p class="font-bold text-green-400"><?= htmlspecialchars($p->impacto_estimado ?: '-') ?></p></div>
                    </div>

                    <div><span class="block text-xs text-gray-400 uppercase">Resumo</span>
                    <p class="text-sm italic text-gray-300">"<?= htmlspecialchars($p->resumo_curto) ?>"</p></div>

                    <div><span class="block text-xs text-gray-400 uppercase">Tags</span>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <?php 
                        $tags = array_filter(explode(',', $p->tags_ai ?? ''));
                        foreach($tags as $t): ?>
                        <span class="bg-blue-900/30 border border-blue-800 text-blue-300 text-xs px-2 py-1 rounded-full"><?= htmlspecialchars(trim($t)) ?></span>
                        <?php endforeach; ?>
                    </div></div>

                    <div class="bg-indigo-900/20 border border-indigo-500/20 p-3 rounded">
                        <span class="block text-xs text-indigo-300 uppercase mb-1">Próxima Aula Recomendada</span>
                        <p class="text-sm text-indigo-100"><?= htmlspecialchars($p->proxima_aula_recomendada) ?></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('btn-reanalisar').addEventListener('click', (e) => {
            const btn = e.target;
            btn.innerText = 'Processando...';
            btn.disabled = true;
            
            setTimeout(() => {
                alert("Pra reanalisar, depois criamos um script PHP separado pra bater na API de novo!");
                btn.innerText = 'Reanalisar';
                btn.disabled = false;
            }, 1000);
        });
    </script>
</body>
</html>
