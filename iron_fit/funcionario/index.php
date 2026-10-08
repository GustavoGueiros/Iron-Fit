<?php
// Área do funcionário: cria, edita, lista e exclui treinos atribuídos.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('funcionario', '../index.php');

$dias = ['segunda' => 'Segunda-feira', 'terca' => 'Terça-feira', 'quarta' => 'Quarta-feira', 'quinta' => 'Quinta-feira', 'sexta' => 'Sexta-feira', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
$mensagem = '';
$edicao = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $treinoId = (int) ($_POST['treino_id'] ?? 0);
    if ($acao === 'excluir' && $treinoId > 0) {
        $usuarioId = (int) $_SESSION['usuario_id'];
        $stmt = $conn->prepare('DELETE e FROM exercicios_treino e INNER JOIN treinos t ON t.id = e.treino_id WHERE e.treino_id = ? AND t.funcionario_id = ?');
        $stmt->bind_param('ii', $treinoId, $usuarioId);
        $stmt->execute();
        $stmt = $conn->prepare('DELETE FROM treinos WHERE id = ? AND funcionario_id = ?');
        $stmt->bind_param('ii', $treinoId, $usuarioId);
        $stmt->execute();
        $mensagem = 'Treino excluído.';
    } elseif (in_array($acao, ['criar', 'editar'], true)) {
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $dia = $_POST['dia_semana'] ?? '';
        $observacoes = trim($_POST['observacoes'] ?? '');
        $nomes = $_POST['exercicio_nome'] ?? [];
        if ($clienteId < 1 || !isset($dias[$dia]) || count(array_filter($nomes)) === 0) {
            $mensagem = 'Selecione um cliente, um dia e ao menos um exercício.';
        } else {
            if ($acao === 'editar' && $treinoId > 0) {
                $stmt = $conn->prepare('UPDATE treinos SET cliente_id = ?, dia_semana = ?, observacoes = ? WHERE id = ? AND funcionario_id = ?');
                $usuarioId = (int) $_SESSION['usuario_id'];
                $stmt->bind_param('issii', $clienteId, $dia, $observacoes, $treinoId, $usuarioId);
                $stmt->execute();
                $stmt = $conn->prepare('DELETE FROM exercicios_treino WHERE treino_id = ?');
                $stmt->bind_param('i', $treinoId);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare('INSERT INTO treinos (cliente_id, funcionario_id, dia_semana, observacoes) VALUES (?, ?, ?, ?)');
                $usuarioId = (int) $_SESSION['usuario_id'];
                $stmt->bind_param('iiss', $clienteId, $usuarioId, $dia, $observacoes);
                $stmt->execute();
                $treinoId = $conn->insert_id;
            }
            $stmt = $conn->prepare('INSERT INTO exercicios_treino (treino_id, nome, series, repeticoes, carga, descanso, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)');
            foreach ($nomes as $indice => $nome) {
                $nome = trim($nome);
                if ($nome === '') continue;
                $series = max(1, (int) ($_POST['series'][$indice] ?? 3));
                $repeticoes = trim($_POST['repeticoes'][$indice] ?? '10');
                $carga = trim($_POST['carga'][$indice] ?? '');
                $descanso = trim($_POST['descanso'][$indice] ?? '');
                $nota = trim($_POST['exercicio_nota'][$indice] ?? '');
                $stmt->bind_param('isissss', $treinoId, $nome, $series, $repeticoes, $carga, $descanso, $nota);
                $stmt->execute();
            }
            $mensagem = $acao === 'editar' ? 'Treino atualizado.' : 'Treino criado e atribuído ao cliente.';
        }
    }
}

$clientes = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo = 'cliente' ORDER BY nome");
if (isset($_GET['editar'])) {
    $editarId = (int) $_GET['editar'];
    $stmt = $conn->prepare('SELECT * FROM treinos WHERE id = ? AND funcionario_id = ? LIMIT 1');
    $stmt->bind_param('ii', $editarId, $_SESSION['usuario_id']);
    $stmt->execute();
    $edicao = $stmt->get_result()->fetch_assoc();
    if ($edicao) {
        $edicao['exercicios'] = [];
        $itens = $conn->query('SELECT * FROM exercicios_treino WHERE treino_id = ' . $editarId . ' ORDER BY id');
        while ($item = $itens->fetch_assoc()) $edicao['exercicios'][] = $item;
    }
}
$treinos = $conn->query("SELECT t.*, u.nome AS cliente_nome FROM treinos t JOIN usuarios u ON u.id = t.cliente_id WHERE t.funcionario_id = " . (int) $_SESSION['usuario_id'] . ' ORDER BY FIELD(t.dia_semana, "segunda", "terca", "quarta", "quinta", "sexta", "sabado", "domingo"), u.nome');
?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Treinos | Iron Fit</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/estilo.css"></head>
<body class="app-page"><header class="site-header"><nav class="navbar navbar-expand-lg navbar-dark bg-black"><div class="container"><a class="navbar-brand logo" href="index.php">IRON FIT / EQUIPE</a><button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">Menu</button><div class="collapse navbar-collapse" id="menu"><ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3"><li class="nav-item"><a class="nav-link" href="index.php">Treinos</a></li><li class="nav-item"><a class="nav-link" href="../pages/home.php">Site</a></li><li class="nav-item"><a class="nav-link text-danger" href="../admin/logout.php">Sair</a></li></ul></div></div></nav></header>
<main class="container py-5"><div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"><div><p class="eyebrow">ÁREA DO FUNCIONÁRIO</p><h1>Olá, <?= htmlspecialchars($_SESSION['nome']) ?></h1><p class="text-secondary">Monte e acompanhe os treinos dos seus clientes.</p></div><a class="btn btn-danger" href="#novo">+ Novo treino</a></div><?php if ($mensagem): ?><div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
<section id="novo" class="workout-form mb-5"><h2>Novo treino</h2><form method="post" class="row g-3"><input type="hidden" name="acao" value="criar"><div class="col-md-6"><label class="form-label">Cliente</label><select class="form-select" name="cliente_id" required><option value="">Selecione</option><?php while ($cliente = $clientes->fetch_assoc()): ?><option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?> · <?= htmlspecialchars($cliente['email']) ?></option><?php endwhile; ?></select></div><div class="col-md-6"><label class="form-label">Dia da semana</label><select class="form-select" name="dia_semana" required><option value="">Selecione</option><?php foreach ($dias as $chave => $nome): ?><option value="<?= $chave ?>"><?= $nome ?></option><?php endforeach; ?></select></div><div class="col-12"><label class="form-label">Observações do treino</label><textarea class="form-control" name="observacoes" rows="2"></textarea></div><div class="col-12"><h3 class="h5">Exercícios</h3><div id="exercicios"><div class="exercise-row row g-2 mb-2"><div class="col-lg-3"><input class="form-control" name="exercicio_nome[]" placeholder="Ex.: Supino reto" required></div><div class="col-2"><input class="form-control" type="number" min="1" name="series[]" placeholder="Séries" value="3"></div><div class="col-2"><input class="form-control" name="repeticoes[]" placeholder="Repetições" value="10"></div><div class="col-2"><input class="form-control" name="carga[]" placeholder="Carga"></div><div class="col-2"><input class="form-control" name="descanso[]" placeholder="Descanso"></div><div class="col-lg-1"><button class="btn btn-outline-danger remove-exercise" type="button" aria-label="Remover exercício">×</button></div><div class="col-12"><input class="form-control" name="exercicio_nota[]" placeholder="Nota do exercício (opcional)"></div></div></div><button class="btn btn-outline-light" id="add-exercise" type="button">+ Adicionar exercício</button></div><div class="col-12"><button class="btn btn-danger" type="submit">Salvar treino</button></div></form></section>
<section><h2>Treinos atribuídos</h2><div class="workout-list"><?php while ($treino = $treinos->fetch_assoc()): ?><article class="workout-item"><div><span class="eyebrow"><?= $dias[$treino['dia_semana']] ?? $treino['dia_semana'] ?></span><h3><?= htmlspecialchars($treino['cliente_nome']) ?></h3><p><?= htmlspecialchars($treino['observacoes'] ?: 'Sem observações.') ?></p><?php $itens = $conn->query('SELECT * FROM exercicios_treino WHERE treino_id = ' . (int) $treino['id']); ?><ul><?php while ($item = $itens->fetch_assoc()): ?><li><?= htmlspecialchars($item['nome']) ?> · <?= $item['series'] ?> séries × <?= htmlspecialchars($item['repeticoes']) ?><?php if ($item['carga']): ?> · <?= htmlspecialchars($item['carga']) ?><?php endif; ?></li><?php endwhile; ?></ul></div><form method="post" onsubmit="return confirm('Excluir este treino?')"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="treino_id" value="<?= $treino['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button></form></article><?php endwhile; ?></div></section></main><script>document.getElementById('add-exercise').addEventListener('click',function(){const row=document.querySelector('.exercise-row').cloneNode(true);row.querySelectorAll('input').forEach(input=>{if(input.name==='series[]'||input.name==='repeticoes[]')return;input.value='';});document.getElementById('exercicios').appendChild(row);});document.addEventListener('click',function(e){if(e.target.classList.contains('remove-exercise')&&document.querySelectorAll('.exercise-row').length>1)e.target.closest('.exercise-row').remove();});</script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>
