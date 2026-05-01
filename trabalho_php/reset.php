<?php
session_start();

// CONTROLE DE ACESSO
// mesmo o reset precisa verificar se o usuário está logado
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// limpa apenas as transações e zera o saldo
// (mantém o login ativo)
$_SESSION['transacoes'] = [];
$_SESSION['saldo']      = 0;

header("Location: historico.php");
exit;
?>
