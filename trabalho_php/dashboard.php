<?php
session_start();

// CONTROLE DE ACESSO
// se não estiver logado, manda pro login
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// INICIALIZAÇÃO DE DADOS
// ?? garante que o valor existe antes de usar
$_SESSION['saldo']      = $_SESSION['saldo']      ?? 0;
$_SESSION['transacoes'] = $_SESSION['transacoes'] ?? [];

$erro = "";

// PROCESSAMENTO DOS FORMULÁRIOS
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ---- adicionar receita ----
    if (isset($_POST['add_receita'])) {

        $receita = floatval($_POST['receita']);

        if ($receita <= 0) {
            $erro = "O valor da receita deve ser maior que zero.";
        } else {
            $_SESSION['saldo'] += $receita; // SOMA ao saldo (antes sobrepunha!)

            $_SESSION['transacoes'][] = [
                "tipo"        => "receita",
                "valor"       => $receita,
                "saldo_final" => $_SESSION['saldo']
            ];

            // evita reenvio do formulário ao atualizar a página
            header("Location: dashboard.php");
            exit;
        }
    }

    // ---- adicionar despesa ----
    if (isset($_POST['add_despesa'])) {

        $despesa = floatval($_POST['despesa']);

        if ($despesa <= 0) {
            $erro = "O valor da despesa deve ser maior que zero.";
        } else {
            $_SESSION['saldo'] -= $despesa; // SUBTRAI do saldo

            $_SESSION['transacoes'][] = [
                "tipo"        => "despesa",
                "valor"       => $despesa,
                "saldo_final" => $_SESSION['saldo']
            ];

            header("Location: dashboard.php");
            exit;
        }
    }

    // ---- zerar tudo ----
    if (isset($_POST['limpar'])) {
        $_SESSION['saldo']      = 0;
        $_SESSION['transacoes'] = [];

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Banco MJ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* ---- barra de navegação ---- */
        nav {
            background: #1e293b;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo { color: #fff; font-size: 17px; font-weight: bold; }

        .nav-links { display: flex; align-items: center; gap: 16px; }

        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-links a:hover { color: #fff; }

        .btn-sair {
            background: #dc2626;
            color: #fff !important;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
        }

        /* ---- conteúdo ---- */
        .pagina { padding: 28px; max-width: 800px; margin: 0 auto; }

        h2 { color: #1e293b; margin-bottom: 20px; }

        /* ---- cards de resumo ---- */
        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        .card p { color: #64748b; font-size: 13px; margin-bottom: 6px; }

        .card strong { font-size: 22px; }

        .card.receitas strong { color: #16a34a; }
        .card.despesas strong { color: #dc2626; }
        .card.saldo    strong { color: #6366f1; }

        /* ---- formulários ---- */
        .formularios {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        .form-card h3 { font-size: 15px; color: #1e293b; margin-bottom: 12px; }

        .form-card input[type="number"] {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .form-card input:focus { outline: none; border-color: #6366f1; }

        .btn-verde {
            width: 100%;
            padding: 9px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-verde:hover { background: #15803d; }

        .btn-vermelho {
            width: 100%;
            padding: 9px;
            background: #dc2626;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-vermelho:hover { background: #b91c1c; }

        /* ---- botões inferiores ---- */
        .acoes {
            display: flex;
            gap: 12px;
        }

        .btn-historico {
            flex: 1;
            display: block;
            text-align: center;
            padding: 11px;
            background: #fff;
            border: 1px solid #6366f1;
            border-radius: 8px;
            color: #6366f1;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-zerar {
            flex: 1;
            padding: 11px;
            background: #fff;
            border: 1px solid #dc2626;
            border-radius: 8px;
            color: #dc2626;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-zerar:hover { background: #fee2e2; }

        /* ---- erro ---- */
        .erro {
            background: #fee2e2;
            color: #dc2626;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<!-- barra de navegação -->
<nav>
    <span class="nav-logo">💰 Banco MJ</span>
    <div class="nav-links">
        <span style="color:#94a3b8; font-size:14px">
            Olá, <?= htmlspecialchars($_SESSION['usuario'] ?? 'Admin') ?>!
        </span>
        <a href="historico.php">📋 Histórico</a>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>
</nav>

<div class="pagina">

    <h2>Painel de Controle Financeiro</h2>

    <?php if ($erro): ?>
        <div class="erro">⚠️ <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <!-- cards de resumo -->
    <?php
        // calcula totais percorrendo o array de transações
        $total_receitas = 0;
        $total_despesas = 0;

        foreach ($_SESSION['transacoes'] as $t) {
            if ($t['tipo'] === 'receita') {
                $total_receitas += $t['valor'];
            } else {
                $total_despesas += $t['valor'];
            }
        }
    ?>
    <div class="cards">
        <div class="card receitas">
            <p>Total de Receitas</p>
            <strong>R$ <?= number_format($total_receitas, 2, ',', '.') ?></strong>
        </div>
        <div class="card despesas">
            <p>Total de Despesas</p>
            <strong>R$ <?= number_format($total_despesas, 2, ',', '.') ?></strong>
        </div>
        <div class="card saldo">
            <p>Saldo Disponível</p>
            <strong>R$ <?= number_format($_SESSION['saldo'], 2, ',', '.') ?></strong>
        </div>
    </div>

    <!-- formulários de receita e despesa lado a lado -->
    <div class="formularios">

        <div class="form-card">
            <h3>➕ Adicionar Receita</h3>
            <form method="post" action="">
                <input type="number" name="receita"
                       placeholder="Valor em R$" min="0.01" step="0.01" required>
                <button type="submit" name="add_receita" class="btn-verde">
                    Confirmar Receita
                </button>
            </form>
        </div>

        <div class="form-card">
            <h3>➖ Adicionar Despesa</h3>
            <form method="post" action="">
                <input type="number" name="despesa"
                       placeholder="Valor em R$" min="0.01" step="0.01" required>
                <button type="submit" name="add_despesa" class="btn-vermelho">
                    Confirmar Despesa
                </button>
            </form>
        </div>

    </div>

    <!-- botões de ação -->
    <div class="acoes">
        <a href="historico.php" class="btn-historico">📋 Ver Histórico Completo</a>
        <form method="post" action="" style="flex:1">
            <button type="submit" name="limpar" class="btn-zerar"
                onclick="return confirm('Tem certeza? Isso apaga tudo!')">
                🗑 Zerar Tudo
            </button>
        </form>
    </div>

</div>
</body>
</html>
