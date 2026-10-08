<?php
session_start();
require_once '../conexao.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin.php');
    exit;
}

$mensagem = '';
$aluno = [
    'id' => '',
    'nome' => '',
    'data_nascimento' => '',
    'data_matricula' => '',
    'cpf' => '',
    'email' => '',
    'telefone' => '',
    'endereco' => ''
];

if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare('DELETE FROM alunos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mensagem = 'Aluno removido com sucesso.';
}

if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare('SELECT * FROM alunos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $aluno = $stmt->get_result()->fetch_assoc();
    if (!$aluno) {
        $aluno = ['id' => '', 'nome' => '', 'data_nascimento' => '', 'data_matricula' => '', 'cpf' => '', 'email' => '', 'telefone' => '', 'endereco' => ''];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_aluno'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome']);
    $data_nascimento = $_POST['data_nascimento'];
    $data_matricula = $_POST['data_matricula'];
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    if ($id > 0) {
        $stmt = $conn->prepare('UPDATE alunos SET nome = ?, data_nascimento = ?, data_matricula = ?, cpf = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?');
        $stmt->bind_param('sssssssi', $nome, $data_nascimento, $data_matricula, $cpf, $email, $telefone, $endereco, $id);
        $stmt->execute();
        $mensagem = 'Aluno atualizado com sucesso.';
    } else {
        $stmt = $conn->prepare('INSERT INTO alunos (nome, data_nascimento, data_matricula, cpf, email, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssss', $nome, $data_nascimento, $data_matricula, $cpf, $email, $telefone, $endereco);
        $stmt->execute();
        $mensagem = 'Aluno cadastrado com sucesso.';
    }

    header('Location: admin_alunos.php?mensagem=' . urlencode($mensagem));
    exit;
}

if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
}

$resultado = $conn->query('SELECT * FROM alunos ORDER BY nome');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-panel">
    <div class="admin-shell">
        <aside class="sidebar">
            <h1>Iron Fit</h1>
            <a href="admin_painel.php">Dashboard</a>
            <a href="admin_alunos.php">Alunos</a>
            <a href="admin_funcionarios.php">Funcionários</a>
            <a href="admin_planos.php">Planos</a>
            <a href="logout.php">Sair</a>
        </aside>

        <main class="content">
            <div class="topo">
                <h2>Lista de Alunos</h2>
                <a href="admin_painel.php">Voltar</a>
            </div>

            <?php if ($mensagem): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <div class="card-panel mb-4">
                <h3><?= empty($aluno['id']) ? 'Cadastrar aluno' : 'Editar aluno' ?></h3>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($aluno['id']) ?>">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($aluno['nome']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data de nascimento</label>
                        <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($aluno['data_nascimento']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data da matrícula</label>
                        <input type="date" name="data_matricula" class="form-control" value="<?= htmlspecialchars($aluno['data_matricula']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" class="form-control" value="<?= htmlspecialchars($aluno['cpf']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($aluno['email']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($aluno['telefone']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($aluno['endereco']) ?>">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="salvar_aluno" class="btn btn-danger">Salvar</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="tabela table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($linha = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $linha['id']; ?></td>
                                <td><?= htmlspecialchars($linha['nome']); ?></td>
                                <td><?= htmlspecialchars($linha['email']); ?></td>
                                <td><?= htmlspecialchars($linha['telefone']); ?></td>
                                <td>
                                    <a href="admin_alunos.php?acao=editar&id=<?= $linha['id']; ?>" class="btn btn-sm btn-outline-light">Editar</a>
                                    <a href="admin_alunos.php?acao=excluir&id=<?= $linha['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este aluno?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
