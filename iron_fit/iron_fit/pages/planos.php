<?php
session_start();
require_once '../conexao.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../index.php');
    exit;
}

$planos = $conn->query('SELECT * FROM planos ORDER BY valor ASC');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Planos - Iron Fit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>

<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-black">
            <div class="container">
                <a class="navbar-brand logo" href="home.php">IRON FIT</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                        <li class="nav-item"><a class="nav-link" href="home.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="planos.php">Planos</a></li>
                        <li class="nav-item"><a class="nav-link" href="unidades.php">Unidades</a></li>
                        <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="../admin/logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <h1 class="page-title text-center mb-4">Nossos planos</h1>
        <div class="row g-4">
            <?php while ($plano = $planos->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="plan-card">
                        <h3><?= htmlspecialchars($plano['nome']) ?></h3>
                        <p><?= htmlspecialchars($plano['descricao']) ?></p>
                        <div class="price">R$ <?= number_format((float) $plano['valor'], 2, ',', '.') ?></div>
                        <a href="contato.php?plano=<?= urlencode($plano['nome']) ?>" class="btn btn-danger mt-3">Contratar</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
