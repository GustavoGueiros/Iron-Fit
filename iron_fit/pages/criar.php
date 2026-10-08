<?php
// Cadastro público de novos clientes e início automático da sessão.
require_once '../conexao.php';
require_once '../auth.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_conta'])) {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    if ($senha !== $confirmarSenha) {
        $mensagem = 'As senhas não conferem.';
    } else {
        $stmtVerifica = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $stmtVerifica->bind_param('s', $email);
        $stmtVerifica->execute();
        $resultado = $stmtVerifica->get_result();

        if ($resultado->num_rows > 0) {
            $mensagem = 'Este e-mail já está cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO usuarios (nome, cpf, telefone, email, senha, tipo) VALUES (?, ?, ?, ?, ?, "cliente")');
            $stmt->bind_param('sssss', $nome, $cpf, $telefone, $email, $senhaHash);
            if ($stmt->execute()) {
                iniciarSessaoUsuario(['id' => $conn->insert_id, 'nome' => $nome, 'tipo' => 'cliente']);
                header('Location: index.php');
                exit;
            }
            $mensagem = 'Não foi possível criar a conta.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Iron Fit</title>
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
                    <h2 class="text-danger mb-5">CRIAR CONTA</h2>

                    <?php if ($mensagem): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($mensagem) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <label>Nome</label>
                        <input type="text" class="form-control mb-3" name="nome" required>

                        <label>CPF</label>
                        <input type="text" name="cpf" id="cpf" class="form-control mb-3" placeholder="000.000.000-00" inputmode="numeric" maxlength="14" autocomplete="off" required>

                        <label>Telefone</label>
                        <input type="tel" class="form-control mb-3" id="telefone" name="telefone" placeholder="(11) 99999-9999" maxlength="15" required>

                        <label>Email</label>
                        <input type="email" class="form-control mb-3" name="email" required>

                        <label>Senha</label>
                        <input type="password" class="form-control mb-3" name="senha" required>

                        <label>Confirmar Senha</label>
                        <input type="password" class="form-control mb-3" name="confirmar_senha" required>

                        <a href="acesso.php" class="text-danger text-decoration-none d-block mb-3">Já tenho uma conta.</a>

                        <button type="submit" name="criar_conta" class="btn btn-outline-danger btn-lg w-100">Criar Conta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cpf = document.getElementById("cpf");
        cpf?.addEventListener("input", function () {
            let valor = cpf.value.replace(/\D/g, "").substring(0, 11);

            if (valor.length > 9) {
                valor = valor.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, "$1.$2.$3-$4");
            } else if (valor.length > 6) {
                valor = valor.replace(/^(\d{3})(\d{3})(\d{1,3})$/, "$1.$2.$3");
            } else if (valor.length > 3) {
                valor = valor.replace(/^(\d{3})(\d{1,3})$/, "$1.$2");
            }

            cpf.value = valor;
        });

        const telefone = document.getElementById("telefone");
        telefone?.addEventListener("input", function () {
            let valor = telefone.value.replace(/\D/g, "");
            if (valor.length > 11) valor = valor.substring(0, 11);
            if (valor.length <= 10) {
                valor = valor.replace(/^(\d{2})(\d)/, "($1) $2");
                valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
            } else {
                valor = valor.replace(/^(\d{2})(\d)/, "($1) $2");
                valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
            }
            telefone.value = valor;
        });
    </script>
</body>
</html>