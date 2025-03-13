<?php
    include 'db_connection.php';

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Obter a data atual
    $data_atual = date('Y-m-d');

    // Variáveis para filtros
    $data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
    $data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

    // Adicionar filtro de período às consultas, se as datas forem fornecidas
    $filtro_data = '';
    if (!empty($data_inicio) && !empty($data_fim)) {
        $filtro_data = " WHERE data_venda BETWEEN '$data_inicio' AND '$data_fim'";
    }

    // Consultar o total de vendas
    $sql_total_vendas = "SELECT SUM(valor_total) AS total_vendas FROM vendas" . $filtro_data;
    $result_total_vendas = $conn->query($sql_total_vendas);
    $total_vendas = 0;

    if ($result_total_vendas->num_rows > 0) {
        $row = $result_total_vendas->fetch_assoc();
        $total_vendas = $row['total_vendas'];
    }

    // Consultar o total de itens vendidos
    $sql_total_itens = "SELECT produto, SUM(quantidade) AS total_quantidade, SUM(valor_total) AS total_valor 
    FROM vendas " . $filtro_data . " GROUP BY produto";
    $result_total_itens = $conn->query($sql_total_itens);

    // Consultar o estoque para mostrar o restante de itens
    $sql_estoque = "
        SELECT 
            e.produto,
            e.quantidade AS quantidade_estoque,
            IFNULL(SUM(v.quantidade), 0) AS quantidade_vendida,
            (e.quantidade - IFNULL(SUM(v.quantidade), 0)) AS quantidade_restante
        FROM 
            estoque e
        LEFT JOIN 
            (SELECT * FROM vendas " . $filtro_data . ") v ON e.produto = v.produto
        GROUP BY 
            e.produto";
    $result_estoque = $conn->query($sql_estoque);

    // Fechar a conexão após as consultas
    CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Vendas - Hortifruti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: rgb(0, 37, 160);
        }
        .container {
            background-color: rgb(181, 179, 199);
            max-width: 1200px; /* Aumentei o tamanho para acomodar as tabelas lado a lado */
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
            text-align: left;
        }
        h2 {
            text-align: center;
        }
        .tables-container {
            display: flex; /* Usando Flexbox */
            gap: 20px; /* Espaçamento entre as tabelas */
        }
        .table-wrapper {
            flex: 1; /* As tabelas ocuparão a mesma largura */
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
            color: white;
        }
    </style>
</head>
<body>
    <button>
        <a href="http://localhost/hortifruti/formulario_hortifruti.php">Voltar</a>
    </button>
    <div class="container">
        <h2>Gestão de Vendas - Hortifruti</h2>

        <!-- Formulário para filtros -->
        <form method="GET" action="">
            <label for="data_inicio">Data Início:</label>
            <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $data_inicio; ?>">
            
            <label for="data_fim">Data Fim:</label>
            <input type="date" id="data_fim" name="data_fim" value="<?php echo $data_fim; ?>">

            <button type="submit">Filtrar</button>
        </form>

        <h3>Total de Vendas: R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></h3>

        <div class="tables-container">
            <!-- Tabela de Itens Vendidos -->
            <div class="table-wrapper">
                <h3>Itens Vendidos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Total Vendido (kg)</th>
                            <th>Total Vendido (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_total_itens->num_rows > 0) {
                            while ($row = $result_total_itens->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['produto'] . "</td>";
                                echo "<td>" . number_format($row['total_quantidade'], 2, ',', '.') . "</td>";
                                echo "<td>R$ " . number_format($row['total_valor'], 2, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Nenhuma venda registrada.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Tabela de Estoque Atual -->
            <div class="table-wrapper">
                <h3>Estoque Atual</h3>

                <label for="filtroNome">Filtrar por nome:</label>
                <input type="text" id="filtroNome" onkeyup="filtrarPorNome()">

                <table id="estoqueAtual">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade em Estoque (kg)</th>
                            <th>Quantidade Vendida (kg)</th>
                            <th>Quantidade Restante (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_estoque->num_rows > 0) {
                            while ($row = $result_estoque->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['produto'] . "</td>";
                                echo "<td>" . number_format($row['quantidade_estoque'], 2, ',', '.') . "</td>";
                                echo "<td>" . number_format($row['quantidade_vendida'], 2, ',', '.') . "</td>";
                                echo "<td>" . number_format($row['quantidade_restante'], 2, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Nenhum produto no estoque.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
         // Função para capturar o pressionamento da tecla ESC
         document.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                    window.location.href = 'formulario_hortifruti.php';  // Redireciona para o formulário
                }
            });


            function filtrarPorNome() {
            const input = document.getElementById('filtroNome');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('estoqueAtual');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0]; // coluna "Nome"
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>
