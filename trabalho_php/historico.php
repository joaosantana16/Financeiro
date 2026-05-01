<?php
session_start();

// CONTROLE DE ACESSO
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

$transacoes = $_SESSION['transacoes'] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico — Banco MJ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* ---- barra de navegação (igual ao dashboard) ---- */
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

        /* ---- cabeçalho da página ---- */
        .topo {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .topo h2 { color: #1e293b; }

        .acoes-topo { display: flex; gap: 10px; }

        .btn-voltar {
            padding: 8px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #fff;
            color: #475569;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }

        .btn-zerar {
            padding: 8px 16px;
            border: 1px solid #dc2626;
            border-radius: 7px;
            background: #fff;
            color: #dc2626;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-zerar:hover { background: #fee2e2; }

        /* ---- tabela ---- */
        .tabela-wrap {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr { border-bottom: 1px solid #f1f5f9; }

        tbody tr:hover { background: #f8fafc; }

        td { padding: 13px 16px; font-size: 14px; color: #1e293b; }

        /* ---- badge do tipo ---- */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge.receita {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge.despesa {
            background: #fee2e2;
            color: #dc2626;
        }

        /* ---- valores coloridos ---- */
        .positivo { color: #16a34a; font-weight: bold; }
        .negativo { color: #dc2626; font-weight: bold; }

        /* ---- mensagem vazia ---- */
        .vazio {
            text-align: center;
            padding: 50px 20px;
            color: #94a3b8;
        }

        .vazio span { font-size: 36px; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>

<!-- barra de navegação -->
<nav>
    <span class="nav-logo">💰 Banco MJ</span>
    <div class="nav-links">
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>
</nav>

<div class="pagina">

    <div class="topo">
        <h2>📋 Histórico de Movimentações</h2>
        <div class="acoes-topo">
            <a href="dashboard.php" class="btn-voltar">← Voltar</a>
            <form method="post" action="reset.php" style="display:inline">
                <button type="submit" class="btn-zerar"
                    onclick="return confirm('Tem certeza? Isso apaga todo o histórico!')">
                    🗑 Zerar Histórico
                </button>
            </form>
        </div>
    </div>

    <div class="tabela-wrap">
        <?php if (empty($transacoes)): ?>

            <div class="vazio">
                <span>📭</span>
                Nenhuma movimentação registrada ainda.
            </div>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Impacto no Saldo</th>
                        <th>Saldo Após</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacoes as $indice => $t): ?>

                        <?php
                            // define o sinal do impacto: receita soma, despesa subtrai
                            if ($t['tipo'] === 'receita') {
                                $sinal   = '+';
                                $classe  = 'positivo';
                            } else {
                                $sinal   = '-';
                                $classe  = 'negativo';
                            }
                        ?>

                        <tr>
                            <!-- número da transação (começa em 1) -->
                            <td style="color:#94a3b8"><?= $indice + 1 ?></td>

                            <!-- badge colorida com o tipo -->
                            <td>
                                <span class="badge <?= $t['tipo'] ?>">
                                    <?= ucfirst($t['tipo']) ?>
                                </span>
                            </td>

                            <!-- valor formatado em reais -->
                            <td>R$ <?= number_format($t['valor'], 2, ',', '.') ?></td>

                            <!-- impacto com sinal e cor -->
                            <td class="<?= $classe ?>">
                                <?= $sinal ?> R$ <?= number_format($t['valor'], 2, ',', '.') ?>
                            </td>

                            <!-- saldo depois dessa transação -->
                            <td>R$ <?= number_format($t['saldo_final'], 2, ',', '.') ?></td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>

</div>
</body>
</html>
