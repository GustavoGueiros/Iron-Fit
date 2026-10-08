<?php
// Exibe as unidades, imagens e mapas da franquia.

$unidades = [
    [
        'nome' => 'Centro',
        'imagem' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=900&q=80',
        'maps' => 'https://www.google.com/maps?q=S%C3%A3o%20Paulo&output=embed'
    ],
    [
        'nome' => 'Zona Sul',
        'imagem' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=900&q=80',
        'maps' => 'https://www.google.com/maps?q=Rio%20de%20Janeiro&output=embed'
    ],
    [
        'nome' => 'Shopping',
        'imagem' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=900&q=80',
        'maps' => 'https://www.google.com/maps?q=Belo%20Horizonte&output=embed'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Unidades - Iron Fit</title>
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
        <h1 class="page-title text-center mb-4">Nossas unidades</h1>
        <div class="row g-4">
            <?php foreach ($unidades as $unidade): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="unit-card">
                        <img src="<?= $unidade['imagem']; ?>" alt="<?= $unidade['nome']; ?>" class="img-fluid mb-3">
                        <h3><?= $unidade['nome']; ?></h3>
                        <iframe src="<?= $unidade['maps']; ?>" class="map-frame" loading="lazy" allowfullscreen></iframe>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>