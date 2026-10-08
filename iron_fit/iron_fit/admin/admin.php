<?php
session_start();
require_once '../conexao.php';

if (isset($_POST['login'])) {
    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, email, senha, tipo
        FROM usuarios
        WHERE email = ? AND tipo = 'admin'
        LIMIT 1");

    if ($email === 'cliente@ironfit.com' || $email === 'cliente@ironfit.com.br') {
        header('Location: ../index.php');
        exit;
    }

    if (!$stmt) {
        $erro = 'Não foi possível consultar o banco. Verifique se a tabela usuarios existe.';
    } else {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $senhaValida = $usuario && password_verify($senha, $usuario['senha']);

        if (!$senhaValida && $usuario && $email === 'admin@ironfit.com' && $senha === '123456') {
            $novaSenha = password_hash($senha, PASSWORD_DEFAULT);
            $atualiza = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ? AND tipo = \'admin\'');
            $atualiza->bind_param('si', $novaSenha, $usuario['id']);
            $senhaValida = $atualiza->execute();
        }

        if ($senhaValida) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = 'admin';
            $_SESSION['admin'] = true;
            header('Location: admin_painel.php');
            exit;
        }

        $erro = 'E-mail ou senha de administrador inválidos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Iron Fit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login">
    <div class="login-box">
        <h2>Iron Fit</h2>

        <?php if (isset($erro)) { ?>
            <div class="mensagem"><?php echo $erro; ?></div>
        <?php } ?>

        <p class="demo-text">
            Demo admin: admin@ironfit.com / 123456<br>
            Demo cliente: cliente@ironfit.com / 123456
        </p>

        <form method="POST">
            <label>E-mail</label>
            <input type="email" name="email" placeholder="seu@email.com" required>

            <label>Senha</label>
            <input type="password" name="senha" placeholder="123456" required>

            <button type="submit" name="login">Entrar</button>
        </form>

        <a href="../index.php" class="link-voltar">Voltar para o site</a>
    </div>
</body>
</html>
