<?php
// Funções compartilhadas de sessão, autorização por papel e logout.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function iniciarSessaoUsuario(array $usuario): void
{
    // Regenera a sessão após o login para evitar fixação de sessão.
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo'] = $usuario['tipo'];
}

function exigirPapel($papeis, string $destino): void
{
    // Bloqueia páginas quando o usuário não tem um dos papéis permitidos.
    $papeis = (array) $papeis;
    if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], $papeis, true)) {
        header('Location: ' . $destino);
        exit;
    }
}

function sairPara(string $destino): void
{
    // Limpa os dados da sessão e encerra o acesso atual.
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
    }
    session_destroy();
    header('Location: ' . $destino);
    exit;
}

function destinoDoPapel(string $tipo): string
{
    // Define a primeira página de cada tipo de usuário.
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pastaAplicacao = dirname(dirname($script));
    $prefixo = $pastaAplicacao === '/' || $pastaAplicacao === '\\' ? '' : rtrim($pastaAplicacao, '/\\');

    return match ($tipo) {
        'admin' => $prefixo . '/admin/admin_painel.php',
        'funcionario' => $prefixo . '/funcionario/index.php',
        default => $prefixo . '/pages/index.php',
    };
}