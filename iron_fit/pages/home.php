<?php
// Rota antiga mantida por compatibilidade e encaminhada para a nova home.
require_once '../conexao.php';
require_once '../auth.php';
exigirPapel('cliente', '../index.php');
header('Location: index.php');
exit;

$planos = $conn->query('SELECT * FROM planos ORDER BY valor ASC LIMIT 3');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Iron Fit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>

<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-black">
            <div class="container">
                <a class="navbar-brand logo" href="index.php">IRON FIT</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                        <li class="nav-item"><a class="nav-link" href="index.php">Minha área</a></li>
                        <li class="nav-item"><a class="nav-link" href="meus_treinos.php">Meus treinos</a></li>
                        <li class="nav-item"><a class="nav-link" href="planos.php">Planos</a></li>
                        <li class="nav-item"><a class="nav-link" href="unidades.php">Unidades</a></li>
                        <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="../admin/logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="container text-center">
            <h1>Bem-vindo à IRON FIT</h1>
            <p>Treine mais forte e viva melhor.</p>
            <a class="btn btn-danger btn-lg" href="planos.php">Ver Planos</a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2>Por que escolher a Iron Fit?</h2>
            <div class="row g-4 mt-3">
                <div class="col-md-4">
                    <div class="feature-card">Equipamentos modernos</div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">Ambiente climatizado</div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">Professores qualificados</div>
                </div>
            </div>
        </div>
    </section>

    <section class="plans-section py-5">
        <div class="container">
            <h2 class="text-center mb-4">Planos em destaque</h2>
            <div class="row g-4">
                <?php while ($plano = $planos->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="plan-card">
                            <h3><?= htmlspecialchars($plano['nome']) ?></h3>
                            <p><?= htmlspecialchars($plano['descricao']) ?></p>
                            <div class="price">R$ <?= number_format((float) $plano['valor'], 2, ',', '.') ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>