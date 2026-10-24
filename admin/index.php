<?php
require_once '../config.php';
checkAdminLogin();

$total = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$stats_board = $pdo->query("SELECT status_board, COUNT(*) as qtd FROM projects GROUP BY status_board")->fetchAll();
$stats_nivel = $pdo->query("SELECT nivel_projeto, COUNT(*) as qtd FROM projects GROUP BY nivel_projeto")->fetchAll();

// Dados mockados ou reais para a dashboard não ficar vazia
$total_ideias = $pdo->query("SELECT COUNT(*) FROM projects WHERE status_board = 'Ideia'")->fetchColumn();
$total_prod = $pdo->query("SELECT COUNT(*) FROM projects WHERE status_board = 'Produção'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - No-Code Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#7c3aed', dark: '#0f172a' } } } }</script>
</head>
<body class="bg-[#0f172a] text-white flex min-h-screen">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col">
        <div class="p-6">
            <h1 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">No-Code Hub</h1>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="block px-4 py-2 bg-purple-600/20 text-purple-400 rounded-lg">Dashboard</a>
            <a href="board.php" class="block px-4 py-2 hover:bg-gray-800 text-gray-300 rounded-lg">Kanban</a>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <a href="login.php" class="text-gray-400 hover:text-white text-sm">Sair do sistema</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        <!-- Topbar -->
        <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-end px-6">
            <div class="flex items-center gap-3">
                <span class="text-sm"><?= htmlspecialchars($_SESSION['admin_nome']) ?></span>
                <span class="px-2 py-1 bg-purple-500/20 text-purple-400 text-xs rounded-full border border-purple-500/30">Mentor</span>
            </div>
        </header>

        <!-- Content -->
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-6">Visão Geral</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
                    <h3 class="text-gray-400 text-sm mb-1">Total de Projetos</h3>
                    <p class="text-4xl font-bold text-white"><?= $total ?></p>
                </div>
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
                    <h3 class="text-gray-400 text-sm mb-1">Na Gaveta (Ideia)</h3>
                    <p class="text-4xl font-bold text-purple-400"><?= $total_ideias ?></p>
                </div>
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
                    <h3 class="text-gray-400 text-sm mb-1">Em Produção</h3>
                    <p class="text-4xl font-bold text-green-400"><?= $total_prod ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
                    <h3 class="text-lg font-bold mb-4">Status no Kanban</h3>
                    <ul class="space-y-3">
                        <?php foreach($stats_board as $s): ?>
                        <li class="flex justify-between border-b border-gray-700 pb-2">
                            <span class="text-gray-300"><?= htmlspecialchars($s->status_board) ?></span>
                            <span class="font-bold"><?= $s->qtd ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
                    <h3 class="text-lg font-bold mb-4">Nível dos Projetos (IA)</h3>
                    <ul class="space-y-3">
                        <?php foreach($stats_nivel as $n): ?>
                        <li class="flex justify-between border-b border-gray-700 pb-2">
                            <span class="text-gray-300"><?= htmlspecialchars($n->nivel_projeto ?: 'Aguardando Análise') ?></span>
                            <span class="font-bold text-blue-400"><?= $n->qtd ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
