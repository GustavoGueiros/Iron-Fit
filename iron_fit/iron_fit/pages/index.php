<?php
session_start();
require_once __DIR__ . '/../conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    if ($email !== '' && $senha !== '') {
        $stmt = $conn->prepare('SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1');

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuario = $resultado->fetch_assoc();

            $senhaValida = $usuario && password_verify($senha, $usuario['senha']);

            if ($senhaValida && $usuario['tipo'] === 'cliente') {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo'] = 'cliente';
                $_SESSION['cliente'] = true;
                header('Location: home.php');
                exit;
            }

            $erro = 'E-mail ou senha inválidos.';
        } else {
            $erro = 'Não foi possível realizar a consulta no banco de dados.';
        }
    } else {
        $erro = 'Informe seu e-mail e senha.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Academia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <div class="col-md-6 d-flex justify-content-center align-items-center logo">
                <div class="text-center">
                    <h1 class="display-1 fw-bold">Iron Fit</h1>
                    <p class="text-danger mt-3">SUPERE SEUS LIMITES</p>
                </div>
            </div>

            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div class="login" style="width:450px;">
                    <h2 class="text-danger mb-5">LOGIN</h2>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="email" class="form-control mb-3" name="email" placeholder="cliente@ironfit.com" required>
                        <input type="password" class="form-control mb-3" name="senha" placeholder="123456" required>

                        <div class="text-end mb-4">
                            <small class="text-light">Usuário demo:</small><br>
                            <small class="text-danger">cliente@ironfit.com / 123456</small>
                        </div>

                        <button type="submit" name="login" class="btn btn-danger btn-lg w-100 mb-3">
                            Entrar
                        </button>
                    </form>

                    <a href="../admin/admin.php" class="btn btn-outline-danger btn-lg w-100 d-block text-center mb-3 text-decoration-none">
                        Entrar como Admin
                    </a>

                    <form action="criar.php" method="GET">
                        <div class="text-center text-white mb-4">ou</div>
                        <button type="submit" class="btn btn-outline-danger btn-lg w-100">
                            Criar Conta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
