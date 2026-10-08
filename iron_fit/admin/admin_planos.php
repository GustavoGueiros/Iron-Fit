<?php
// CRUD administrativo dos planos comerciais da academia.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('admin', 'admin.php');

$mensagem = '';
$plano = ['id' => '', 'nome' => '', 'descricao' => '', 'valor' => ''];

if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare('DELETE FROM planos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mensagem = 'Plano removido com sucesso.';
}

if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $exec = $conn->prepare('SELECT * FROM planos WHERE id = ?');
    $exec->bind_param('i', $id);
    $exec->execute();
    $plano = $exec->get_result()->fetch_assoc();
    if (!$plano) {
        $plano = ['id' => '', 'nome' => '', 'descricao' => '', 'valor' => ''];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_plano'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $valor = $_POST['valor'];

    if ($id > 0) {
        $stmt = $conn->prepare('UPDATE planos SET nome = ?, descricao = ?, valor = ? WHERE id = ?');
        $stmt->bind_param('ssdi', $nome, $descricao, $valor, $id);
        $stmt->execute();
        $mensagem = 'Plano atualizado com sucesso.';
    } else {
        $stmt = $conn->prepare('INSERT INTO planos (nome, descricao, valor) VALUES (?, ?, ?)');
        $stmt->bind_param('ssd', $nome, $descricao, $valor);
        $stmt->execute();
        $mensagem = 'Plano cadastrado com sucesso.';
    }

    header('Location: admin_planos.php?mensagem=' . urlencode($mensagem));
    exit;
}

if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
}

$resultado = $conn->query('SELECT * FROM planos ORDER BY valor DESC');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos - Admin</title>
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
                <h2>Planos</h2>
                <a href="admin_painel.php">Voltar</a>
            </div>

            <?php if ($mensagem): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <div class="card-panel mb-4">
                <h3><?= empty($plano['id']) ? 'Cadastrar plano' : 'Editar plano' ?></h3>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($plano['id']) ?>">
                    <div class="col-md-4">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($plano['nome']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Valor (R$)</label>
                        <input type="number" step="0.01" min="0" name="valor" class="form-control" value="<?= htmlspecialchars($plano['valor']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($plano['descricao']) ?>">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="salvar_plano" class="btn btn-danger">Salvar</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="tabela table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Plano</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($linha = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($linha['nome']); ?></td>
                                <td><?= htmlspecialchars($linha['descricao']); ?></td>
                                <td>R$ <?= number_format((float) $linha['valor'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="admin_planos.php?acao=editar&id=<?= $linha['id']; ?>" class="btn btn-sm btn-outline-light">Editar</a>
                                    <a href="admin_planos.php?acao=excluir&id=<?= $linha['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este plano?');">Excluir</a>
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
