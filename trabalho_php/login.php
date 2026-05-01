<?php
session_start();

/*
    Credenciais fixas do sistema.
    A senha é comparada diretamente — sem hash.
    Validamos também se os campos estão vazios ou cheios de espaço.
*/
$usuario_correto = "admin";
$senha_correta   = "123";

// se já está logado, manda direto pro dashboard
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: dashboard.php");
    exit;
}

$erro = "";

// processa só quando o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // trim() remove espaços do início e fim do que foi digitado
    $usuario = trim($_POST["usuario"] ?? "");
    $senha   = trim($_POST["senha"]   ?? "");

    // verifica se os campos estão vazios
    if ($usuario === "" || $senha === "") {
        $erro = "Preencha todos os campos.";

    // verifica se usuário e senha estão corretos
    } elseif ($usuario === $usuario_correto && $senha === $senha_correta) {

        $_SESSION['logado']     = true;
        $_SESSION['usuario']    = $usuario;
        $_SESSION['saldo']      = 0;
        $_SESSION['transacoes'] = [];

        header("Location: dashboard.php");
        exit;

    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login — Banco MJ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 40px 36px;
            width: 340px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .icone { font-size: 40px; margin-bottom: 8px; }
        h1 { font-size: 22px; color: #1e293b; margin-bottom: 4px; }
        .subtitulo { color: #64748b; font-size: 13px; margin-bottom: 28px; }

        label {
            display: block;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
            color: #475569;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        input:focus { outline: none; border-color: #6366f1; }

        .btn {
            width: 100%;
            padding: 11px;
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover { background: #4f46e5; }

        .erro {
            background: #fee2e2;
            color: #dc2626;
            border-radius: 8px;
            padding: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .dica { color: #94a3b8; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="card">
    <div class="icone">💰</div>
    <h1>Banco MJ</h1>
    <p class="subtitulo">Gestão Financeira Pessoal</p>

    <?php if ($erro): ?>
        <div class="erro">⚠️ <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" action="">

        <label for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario"
               placeholder="admin"
               value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••">

        <button type="submit" class="btn">Entrar</button>
    </form>

    <p class="dica">Usuário: admin | Senha: 123</p>
</div>

</body>
</html>
