<?php
// Visualização semanal somente leitura dos treinos do cliente autenticado.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('cliente', '../index.php');
$dias = ['segunda' => 'Segunda-feira', 'terca' => 'Terça-feira', 'quarta' => 'Quarta-feira', 'quinta' => 'Quinta-feira', 'sexta' => 'Sexta-feira', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
$clienteId = (int) $_SESSION['usuario_id'];
$treinos = [];
$stmt = $conn->prepare('SELECT * FROM treinos WHERE cliente_id = ? ORDER BY FIELD(dia_semana, "segunda", "terca", "quarta", "quinta", "sexta", "sabado", "domingo")');
$stmt->bind_param('i', $clienteId);
$stmt->execute();
$resultado = $stmt->get_result();
while ($treino = $resultado->fetch_assoc()) {
    $treino['exercicios'] = [];
    $itens = $conn->prepare('SELECT * FROM exercicios_treino WHERE treino_id = ? ORDER BY id');
    $itens->bind_param('i', $treino['id']);
    $itens->execute();
    $lista = $itens->get_result();
    while ($item = $lista->fetch_assoc()) $treino['exercicios'][] = $item;
    $treinos[$treino['dia_semana']][] = $treino;
}
?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Meus treinos | Iron Fit</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/estilo.css"></head>
<body class="app-page"><header class="site-header"><nav class="navbar navbar-expand-lg navbar-dark bg-black"><div class="container"><a class="navbar-brand logo" href="home.php">IRON FIT</a><button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">Menu</button><div class="collapse navbar-collapse" id="menu"><ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3"><li class="nav-item"><a class="nav-link" href="home.php">Início</a></li><li class="nav-item"><a class="nav-link active" href="meus_treinos.php">Meus treinos</a></li><li class="nav-item"><a class="nav-link" href="planos.php">Planos</a></li><li class="nav-item"><a class="nav-link text-danger" href="../admin/logout.php">Sair</a></li></ul></div></div></nav></header><main class="container py-5"><p class="eyebrow">ÁREA DO CLIENTE</p><h1>Meus treinos</h1><p class="lead text-secondary mb-5">Sua semana organizada para você treinar com clareza.</p><div class="week-grid"><?php foreach ($dias as $chave => $nome): ?><section class="day-card"><div class="day-heading"><h2><?= $nome ?></h2><span><?= count($treinos[$chave] ?? []) ?> treino(s)</span></div><?php if (empty($treinos[$chave])): ?><p class="empty-state">Nenhum treino foi atribuído para este dia.</p><?php else: ?><?php foreach ($treinos[$chave] as $treino): ?><p class="text-secondary"><?= htmlspecialchars($treino['observacoes'] ?: 'Foco e constância.') ?></p><ul class="exercise-list"><?php foreach ($treino['exercicios'] as $item): ?><li><strong><?= htmlspecialchars($item['nome']) ?></strong><span><?= $item['series'] ?> séries · <?= htmlspecialchars($item['repeticoes']) ?> repetições<?php if ($item['carga']): ?> · <?= htmlspecialchars($item['carga']) ?><?php endif; ?><?php if ($item['descanso']): ?> · descanso <?= htmlspecialchars($item['descanso']) ?><?php endif; ?></span><?php if ($item['observacoes']): ?><small><?= htmlspecialchars($item['observacoes']) ?></small><?php endif; ?></li><?php endforeach; ?></ul><?php endforeach; ?><?php endif; ?></section><?php endforeach; ?></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>
