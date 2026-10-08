<?php
session_start();
require_once '../conexao.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'cliente') {
	header('Location: ../index.php');
	exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_contato'])) {
	$nome = trim($_POST['nome']);
	$email = trim($_POST['email']);
	$assunto = trim($_POST['assunto']);
	$mensagemContato = trim($_POST['mensagem']);

	$stmt = $conn->prepare('INSERT INTO contatos (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)');
	$stmt->bind_param('ssss', $nome, $email, $assunto, $mensagemContato);
	$stmt->execute();
	$mensagem = 'Mensagem enviada com sucesso! Nossa equipe entrará em contato em breve.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Contato - Iron Fit</title>
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
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="contact-card">
					<h1 class="page-title text-center mb-4">Fale conosco</h1>

					<?php if ($mensagem): ?>
						<div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
					<?php endif; ?>

					<form method="POST" class="row g-3">
						<div class="col-md-6">
							<label class="form-label">Nome</label>
							<input type="text" name="nome" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label class="form-label">Email</label>
							<input type="email" name="email" class="form-control" required>
						</div>
						<div class="col-12">
							<label class="form-label">Assunto</label>
							<input type="text" name="assunto" class="form-control" value="<?= htmlspecialchars($_GET['plano'] ?? '') ?>" required>
						</div>
						<div class="col-12">
							<label class="form-label">Mensagem</label>
							<textarea name="mensagem" class="form-control" rows="5" required></textarea>
						</div>
						<div class="col-12 text-center">
							<button type="submit" name="enviar_contato" class="btn btn-danger btn-lg">Enviar mensagem</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
