<?php
// Tela oficial de login: identifica o usuário e encaminha pelo papel.
require_once '../conexao.php';
require_once '../auth.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Informe seu e-mail e senha.';
    } else {
        $stmt = $conn->prepare('SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $usuario = $stmt->get_result()->fetch_assoc();
            if ($usuario && password_verify($senha, $usuario['senha']) && in_array($usuario['tipo'], ['admin', 'funcionario', 'cliente'], true)) {
                iniciarSessaoUsuario($usuario);
                header('Location: ' . destinoDoPapel($usuario['tipo']));
                exit;
            }
        }
        $erro = 'E-mail ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso | Iron Fit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body class="access-page">
    <main class="access-layout">
        <section class="access-intro">
            <a class="access-brand" href="../index.php">IRON FIT</a>
            <div>
                <p class="eyebrow">ÁREA EXCLUSIVA</p>
                <h1>Seu treino começa com um acesso.</h1>
                <p>Entre para acompanhar seus treinos ou acesse a área da equipe Iron Fit.</p>
            </div>
            <a class="access-back" href="../index.php">← Voltar para a home</a>
        </section>
        <section class="access-form-wrap">
            <div class="access-form">
                <p class="eyebrow">BEM-VINDO DE VOLTA</p>
                <h2>Entrar na sua conta</h2>
                <?php if ($erro): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
                <form method="POST">
                    <label for="email">E-mail</label>
                    <input id="email" type="email" name="email" placeholder="cliente@ironfit.com" autocomplete="email" required>
                    <label for="senha">Senha</label>
                    <input id="senha" type="password" name="senha" placeholder="Sua senha" autocomplete="current-password" required>
                    <button type="submit" name="login" class="btn btn-danger btn-lg w-100">Entrar</button>
                </form>
                <div class="access-divider"><span>ou</span></div>
                <a href="criar.php" class="btn btn-outline-danger btn-lg w-100">Criar uma conta</a>
                <p class="access-demo">Demo cliente: cliente@ironfit.com / 123456<br>Demo funcionário: funcionario@ironfit.com / 123456</p>
                <a href="../admin/admin.php" class="access-admin-link">Acesso administrativo</a>
            </div>
        </section>
    </main>
</body>
</html>
