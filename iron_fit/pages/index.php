<?php
// Home autenticada do cliente com resumo e próximo treino.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('cliente', '../index.php');

$clienteId = (int) $_SESSION['usuario_id'];
$totalTreinos = 0;
$totalExercicios = 0;
$proximoTreino = null;
$dias = ['segunda' => 'Segunda-feira', 'terca' => 'Terça-feira', 'quarta' => 'Quarta-feira', 'quinta' => 'Quinta-feira', 'sexta' => 'Sexta-feira', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM treinos WHERE cliente_id = ?');
$stmt->bind_param('i', $clienteId);
$stmt->execute();
$totalTreinos = (int) $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM exercicios_treino e INNER JOIN treinos t ON t.id = e.treino_id WHERE t.cliente_id = ?');
$stmt->bind_param('i', $clienteId);
$stmt->execute();
$totalExercicios = (int) $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare('SELECT t.*, COUNT(e.id) AS exercicios FROM treinos t LEFT JOIN exercicios_treino e ON e.treino_id = t.id WHERE t.cliente_id = ? GROUP BY t.id ORDER BY FIELD(t.dia_semana, "segunda", "terca", "quarta", "quinta", "sexta", "sabado", "domingo") LIMIT 1');
$stmt->bind_param('i', $clienteId);
$stmt->execute();
$proximoTreino = $stmt->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minha área | Iron Fit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body class="app-page client-dashboard">
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-black">
            <div class="container">
                <a class="navbar-brand logo" href="index.php">IRON FIT</a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#clientMenu" aria-label="Abrir menu"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="clientMenu">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                        <li class="nav-item"><a class="nav-link active" href="index.php">Minha área</a></li>
                        <li class="nav-item"><a class="nav-link" href="meus_treinos.php">Meus treinos</a></li>
                        <li class="nav-item"><a class="nav-link" href="planos.php">Planos</a></li>
                        <li class="nav-item"><a class="nav-link" href="unidades.php">Unidades</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="../admin/logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <section class="client-welcome">
            <div><p class="eyebrow">ÁREA DO CLIENTE</p><h1>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>.</h1><p>Seu treino está organizado. Agora é só manter a constância.</p></div>
            <a class="btn btn-danger btn-lg" href="meus_treinos.php">Ver meus treinos</a>
        </section>
        <section class="dashboard-stats">
            <article><span>Treinos atribuídos</span><strong><?= $totalTreinos ?></strong></article>
            <article><span>Exercícios cadastrados</span><strong><?= $totalExercicios ?></strong></article>
            <article><span>Seu objetivo</span><strong>EVOLUIR</strong></article>
        </section>
        <section class="dashboard-content">
            <div class="dashboard-panel dashboard-next">
                <div class="panel-heading"><div><p class="eyebrow">PROGRAMAÇÃO</p><h2>Próximo treino</h2></div><a href="meus_treinos.php">Ver semana</a></div>
                <?php if ($proximoTreino): ?>
                    <div class="next-workout-day"><span><?= htmlspecialchars($dias[$proximoTreino['dia_semana']] ?? $proximoTreino['dia_semana']) ?></span><strong><?= (int) $proximoTreino['exercicios'] ?> exercícios</strong></div>
                    <p><?= htmlspecialchars($proximoTreino['observacoes'] ?: 'Prepare-se para treinar com foco e qualidade.') ?></p>
                    <a class="btn btn-outline-light" href="meus_treinos.php">Abrir treino completo</a>
                <?php else: ?>
                    <div class="dashboard-empty"><strong>Ainda não há treinos atribuídos.</strong><p>Assim que sua equipe criar um treino, ele aparecerá nesta área.</p></div>
                <?php endif; ?>
            </div>
            <div class="dashboard-panel dashboard-action"><p class="eyebrow">IRON FIT</p><h2>Cuide do seu ritmo.</h2><p>Consulte seus treinos, acompanhe sua evolução e mantenha o foco no próximo passo.</p><a class="btn btn-danger" href="unidades.php">Encontrar uma unidade</a></div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
