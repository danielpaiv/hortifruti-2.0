<?php
    session_start();
    include 'db_connection.php';
    // Verificar se a sessão contém os dados esperados
    if (isset($_SESSION['user_id']) && isset($_SESSION['nome'])) {
        echo 'ID : ' . $_SESSION['user_id'] . '<br>';
        echo 'Nome : ' . $_SESSION['nome'] . '<br>';
    } else {
        echo 'Nenhum dado de usuário encontrado na sessão.';
    }


    // Verificar se o usuário está autenticado
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');  // Redireciona para a página de login caso não esteja logado
        exit();  // Importante adicionar o exit() após o redirecionamento
    }

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Definir a data atual
    $data_atual = date('Y-m-d');

    // Se uma data de filtro foi enviada via GET, usa ela
    $data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : $data_atual;
    $data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : $data_atual;

    // Consultar vendas dentro do período de data selecionado para o usuário logado
    $sql_vendas = "SELECT produto, SUM(quantidade) AS total_quantidade,
                                 SUM(valor_total) AS total_valor
                                FROM vendas 
                                WHERE data_venda BETWEEN ? AND ? AND user_id = ?
                                GROUP BY produto";
    $stmt_vendas = $conn->prepare($sql_vendas);
    $stmt_vendas->bind_param('sss', $data_inicio, $data_fim, $_SESSION['user_id']);  // Filtra pelo ID do usuário logado
    $stmt_vendas->execute();
    $result_vendas = $stmt_vendas->get_result();

    // Consultar dados financeiros dentro do período de data selecionado para o usuário logado
    $sql_financeiro = "SELECT   SUM(pagamento_dinheiro) AS total_dinheiro, 
                                SUM(pagamento_cartao) AS total_cartao, 
                                SUM(pagamento_pix) AS total_pix, 
                                SUM(pagamento_dinheiro + pagamento_cartao + pagamento_pix) AS sub_total
                                FROM faturamento 
                                WHERE data_venda BETWEEN ? AND ? AND user_id = ?";
    $stmt_financeiro = $conn->prepare($sql_financeiro);
    $stmt_financeiro->bind_param('sss', $data_inicio, $data_fim, $_SESSION['user_id']);  // Filtra pelo ID do usuário logado
    $stmt_financeiro->execute();
    $result_financeiro = $stmt_financeiro->get_result();

    //esse codigo é responsável por criptografar a pagina viinculado ao codigo teste login.

    // Verificar se as variáveis de sessão 'email' e 'senha' não estão definidas
    if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
        unset($_SESSION['nome']);
        unset($_SESSION['senha']);
        header('Location: index.php');
        exit();  // Importante adicionar o exit() após o redirecionamento
    }

    // Fechar a conexão após as consultas
    $stmt_vendas->close();
    $stmt_financeiro->close();
    CloseCon($conn);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas - Hortifruti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: rgb(0, 37, 160);
        }
        .container {
            background-color: rgb(181, 179, 199);
            max-width: 900px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            background-color: #28a745;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        a {
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>
<body>
    <button>
        <a href="http://localhost/hortifruti/formulario_hortifruti.php">Voltar</a>
    </button>
    <div class="container">
        <h2>Relatório de Vendas - Hortifruti</h2>

        <!-- Filtro de Data -->
        <form method="GET" action="relatorio.php">
            <label for="data_inicio">Data Início:</label>
            <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>" required>
            <label for="data_fim">Data Fim:</label>
            <input type="date" name="data_fim" value="<?php echo $data_fim; ?>" required>
            <input type="submit" value="Filtrar">
        </form>

        <!-- Tabela de Vendas -->
        <h3>Vendas do Período</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Vendida</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_vendas->num_rows > 0) {
                    while($row = $result_vendas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['produto'] . "</td>";
                        echo "<td>" . $row['total_quantidade'] . "</td>";
                        echo "<td>R$ " . number_format($row['total_valor'], 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhuma venda encontrada</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Tabela Financeiro -->
        <h3>Relatório Financeiro</h3>
        <table>
            <thead>
                <tr>
                    <th>Valor em Dinheiro</th>
                    <th>Valor em Cartão</th>
                    <th>Valor em Pix</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_financeiro->num_rows > 0) {
                    $row = $result_financeiro->fetch_assoc();
                    echo "<tr>";
                    echo "<td>R$ " . number_format($row['total_dinheiro'], 2, ',', '.') . "</td>";
                    echo "<td>R$ " . number_format($row['total_cartao'], 2, ',', '.') . "</td>";
                    echo "<td>R$ " . number_format($row['total_pix'], 2, ',', '.') . "</td>";
                    echo "<td>R$ " . number_format($row['sub_total'], 2, ',', '.') . "</td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='4'>Nenhum dado financeiro encontrado</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Campo para Troco -->
        <label for="troco">Digite o valor de Troco:</label>
        <input type="number" name="troco" step="0.01">
        <br>
        <br>
        
        <!-- Botão de Imprimir -->
        <button onclick="window.print()">🖨️Imprimir Relatório</button>
    </div>
    <script>
        // Função para capturar o pressionamento da tecla Esquerda
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                window.location.href = 'formulario_hortifruti.php';  // Redireciona para o formulário
            }
        });
    </script>
</body>
</html>
