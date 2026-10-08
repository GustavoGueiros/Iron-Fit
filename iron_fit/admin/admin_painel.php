<?php
// Dashboard do administrador com os principais indicadores do sistema.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('admin', 'admin.php');

$resumo = $conn->query('SELECT
    (SELECT COUNT(*) FROM alunos) AS total_alunos,
    (SELECT COUNT(*) FROM funcionarios) AS total_funcionarios,
    (SELECT COUNT(*) FROM planos) AS total_planos,
    (SELECT COUNT(*) FROM usuarios) AS total_usuarios')->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Iron Fit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-panel">
    <div class="admin-shell">
        <aside class="sidebar">
            <h1>Iron Fit</h1>
            <a href="admin_painel.php">Dashboard</a>
            <a href="admin_usuarios.php">Usuários</a>
            <a href="admin_alunos.php">Alunos</a>
            <a href="admin_funcionarios.php">Funcionários</a>
            <a href="admin_planos.php">Planos</a>
            <a href="logout.php">Sair</a>
        </aside>

        <main class="content">
            <div class="topo">
                <h2>Painel Administrativo</h2>
                <a href="../pages/home.php">Ir para o site</a>
            </div>

            <div class="grid-cards">
                <div class="card">
                    <h3>Alunos</h3>
                    <p><?= $resumo['total_alunos']; ?></p>
                </div>
                <div class="card">
                    <h3>Funcionários</h3>
                    <p><?= $resumo['total_funcionarios']; ?></p>
                </div>
                <div class="card">
                    <h3>Planos</h3>
                    <p><?= $resumo['total_planos']; ?></p>
                </div>
                <div class="card">
                    <h3>Usuários</h3>
                    <p><?= $resumo['total_usuarios']; ?></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="tabela table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $alunos = $conn->query('SELECT id, nome, email FROM alunos ORDER BY nome LIMIT 5');
                        while ($aluno = $alunos->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $aluno['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($aluno['nome']) . '</td>';
                            echo '<td>' . htmlspecialchars($aluno['email']) . '</td>';
                            echo '<td><span class="badge bg-success">Ativo</span></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
