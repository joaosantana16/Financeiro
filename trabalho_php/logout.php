<?php
session_start();

// destrói a sessão inteira (apaga login, saldo e transações)
session_destroy();

// manda pro login com header que impede voltar pelo navegador
header("Location: login.php");
header("Cache-Control: no-store, no-cache, must-revalidate"); // impede cache da página
exit;
?>
