<?php
// Administração de contas, papéis e permissões de acesso.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('admin', 'admin.php');
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'criar') {
        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? 'funcionario';
        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6 || !in_array($tipo, ['funcionario', 'cliente'], true)) {
            $mensagem = 'Preencha nome, e-mail válido, senha com 6 caracteres e um papel permitido.';
        } else {
            $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)');
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt->bind_param('ssss', $nome, $email, $hash, $tipo);
            $mensagem = $stmt->execute() ? 'Usuário criado.' : 'Não foi possível criar. Verifique se o e-mail já existe.';
        }
    } elseif ($acao === 'papel') {
        $id = (int) ($_POST['id'] ?? 0);
        $tipo = $_POST['tipo'] ?? '';
        if ($id !== (int) $_SESSION['usuario_id'] && in_array($tipo, ['admin', 'funcionario', 'cliente'], true)) {
            $stmt = $conn->prepare('UPDATE usuarios SET tipo = ? WHERE id = ?');
            $stmt->bind_param('si', $tipo, $id);
            $stmt->execute();
            $mensagem = 'Papel atualizado.';
        }
    } elseif ($acao === 'excluir') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id !== (int) $_SESSION['usuario_id']) {
            $stmt = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $mensagem = 'Usuário removido.';
        }
    }
}
$usuarios = $conn->query('SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY tipo, nome');
?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Usuários | Iron Fit</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/admin.css"></head><body class="admin-panel"><div class="admin-shell"><aside class="sidebar"><h1>Iron Fit</h1><a href="admin_painel.php">Dashboard</a><a href="admin_usuarios.php">Usuários</a><a href="admin_alunos.php">Alunos</a><a href="admin_funcionarios.php">Funcionários</a><a href="admin_planos.php">Planos</a><a href="logout.php">Sair</a></aside><main class="content"><div class="topo"><h2>Usuários e permissões</h2><a href="admin_painel.php">Voltar ao painel</a></div><?php if ($mensagem): ?><div class="mensagem"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?><section class="card-panel mb-4"><h3>Criar acesso de funcionário</h3><form method="post" class="row g-2"><input type="hidden" name="acao" value="criar"><div class="col-md-3"><input class="form-control" name="nome" placeholder="Nome" required></div><div class="col-md-3"><input class="form-control" type="email" name="email" placeholder="E-mail" required></div><div class="col-md-3"><input class="form-control" type="password" name="senha" minlength="6" placeholder="Senha" required></div><div class="col-md-3"><button class="btn btn-danger w-100" type="submit">Criar conta</button></div></form></section><div class="table-responsive"><table class="tabela table table-dark align-middle"><thead><tr><th>Nome</th><th>E-mail</th><th>Papel</th><th>Ações</th></tr></thead><tbody><?php while ($usuario = $usuarios->fetch_assoc()): ?><tr><td><?= htmlspecialchars($usuario['nome']) ?></td><td><?= htmlspecialchars($usuario['email']) ?></td><td><span class="badge bg-<?= $usuario['tipo'] === 'admin' ? 'danger' : ($usuario['tipo'] === 'funcionario' ? 'warning text-dark' : 'secondary') ?>"><?= htmlspecialchars($usuario['tipo']) ?></span></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="acao" value="papel"><input type="hidden" name="id" value="<?= $usuario['id'] ?>"><select class="form-select form-select-sm" name="tipo" <?= $usuario['id'] === $_SESSION['usuario_id'] ? 'disabled' : '' ?>><option value="cliente" <?= $usuario['tipo'] === 'cliente' ? 'selected' : '' ?>>Cliente</option><option value="funcionario" <?= $usuario['tipo'] === 'funcionario' ? 'selected' : '' ?>>Funcionário</option><option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Admin</option></select><button class="btn btn-sm btn-outline-light" type="submit" <?= $usuario['id'] === $_SESSION['usuario_id'] ? 'disabled' : '' ?>>Salvar</button></form><?php if ($usuario['id'] !== $_SESSION['usuario_id']): ?><form method="post" class="mt-2" onsubmit="return confirm('Remover este usuário?')"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= $usuario['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Remover</button></form><?php endif; ?></td></tr><?php endwhile; ?></tbody></table></div></main></div></body></html>
