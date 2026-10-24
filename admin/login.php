<?php
require_once '../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user->senha_hash)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_nome'] = $user->nome;
        header('Location: index.php');
        exit;
    } else {
        $error = "Login ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - No-Code Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: #0f172a; }</style>
</head>
<body class="flex items-center justify-center min-h-screen text-white">
    <div class="bg-gray-800 p-8 rounded-xl shadow-xl w-96 border border-gray-700">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-purple-500">No-Code Hub</h2>
            <p class="text-sm text-gray-400">Acesso Restrito</p>
        </div>
        <?php if($error): ?><div class="bg-red-500/20 text-red-400 p-3 rounded mb-4 text-sm"><?= $error ?></div><?php endif; ?>
        <form method="POST" class="space-y-4">
            <div><label class="block text-sm mb-1">E-mail</label>
            <input type="email" name="email" required class="w-full p-2 bg-gray-700 rounded border border-gray-600 focus:border-purple-500 outline-none"></div>
            <div><label class="block text-sm mb-1">Senha</label>
            <input type="password" name="senha" required class="w-full p-2 bg-gray-700 rounded border border-gray-600 focus:border-purple-500 outline-none"></div>
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 py-2 rounded font-bold transition">Entrar</button>
        </form>
    </div>
</body>
</html>
