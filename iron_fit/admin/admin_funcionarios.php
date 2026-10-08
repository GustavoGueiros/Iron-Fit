<?php
// CRUD administrativo dos dados cadastrais dos funcionários.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('admin', 'admin.php');

$mensagem = '';
$funcionario = ['id' => '', 'nome' => '', 'cpf' => '', 'data_nascimento' => '', 'data_admissao' => '', 'cargo' => '', 'email' => '', 'telefone' => '', 'endereco' => ''];

if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare('DELETE FROM funcionarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mensagem = 'Funcionário removido com sucesso.';
}

if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $exec = $conn->prepare('SELECT * FROM funcionarios WHERE id = ?');
    $exec->bind_param('i', $id);
    $exec->execute();
    $funcionario = $exec->get_result()->fetch_assoc();
    if (!$funcionario) {
        $funcionario = ['id' => '', 'nome' => '', 'cpf' => '', 'data_nascimento' => '', 'data_admissao' => '', 'cargo' => '', 'email' => '', 'telefone' => '', 'endereco' => ''];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_funcionario'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = $_POST['data_nascimento'];
    $data_admissao = $_POST['data_admissao'];
    $cargo = trim($_POST['cargo']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    if ($id > 0) {
        $stmt = $conn->prepare('UPDATE funcionarios SET nome = ?, cpf = ?, data_nascimento = ?, data_admissao = ?, cargo = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?');
        $stmt->bind_param('ssssssssi', $nome, $cpf, $data_nascimento, $data_admissao, $cargo, $email, $telefone, $endereco, $id);
        $stmt->execute();
        $mensagem = 'Funcionário atualizado com sucesso.';
    } else {
        $stmt = $conn->prepare('INSERT INTO funcionarios (nome, cpf, data_nascimento, data_admissao, cargo, email, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssss', $nome, $cpf, $data_nascimento, $data_admissao, $cargo, $email, $telefone, $endereco);
        $stmt->execute();
        $mensagem = 'Funcionário cadastrado com sucesso.';
    }

    header('Location: admin_funcionarios.php?mensagem=' . urlencode($mensagem));
    exit;
}

if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
}

$resultado = $conn->query('SELECT * FROM funcionarios ORDER BY nome');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - Admin</title>
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
                <h2>Funcionários</h2>
                <a href="admin_painel.php">Voltar</a>
            </div>

            <?php if ($mensagem): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <div class="card-panel mb-4">
                <h3><?= empty($funcionario['id']) ? 'Cadastrar funcionário' : 'Editar funcionário' ?></h3>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id']) ?>">
                    <div class="col-md-4">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($funcionario['nome']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" class="form-control" value="<?= htmlspecialchars($funcionario['cpf']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cargo</label>
                        <input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars($funcionario['cargo']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data de nascimento</label>
                        <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($funcionario['data_nascimento']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data de admissão</label>
                        <input type="date" name="data_admissao" class="form-control" value="<?= htmlspecialchars($funcionario['data_admissao']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($funcionario['email']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($funcionario['telefone']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($funcionario['endereco']) ?>">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="salvar_funcionario" class="btn btn-danger">Salvar</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="tabela table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($linha = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $linha['id']; ?></td>
                                <td><?= htmlspecialchars($linha['nome']); ?></td>
                                <td><?= htmlspecialchars($linha['cargo']); ?></td>
                                <td><?= htmlspecialchars($linha['email']); ?></td>
                                <td>
                                    <a href="admin_funcionarios.php?acao=editar&id=<?= $linha['id']; ?>" class="btn btn-sm btn-outline-light">Editar</a>
                                    <a href="admin_funcionarios.php?acao=excluir&id=<?= $linha['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este funcionário?');">Excluir</a>
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
