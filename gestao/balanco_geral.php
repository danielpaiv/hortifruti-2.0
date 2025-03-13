<?php
    session_start();
    include 'db_connection.php';
    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Definir datas padrão (últimos 30 dias)
    $data_inicio = $_GET['data_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $data_fim = $_GET['data_fim'] ?? date('Y-m-d');

    // Consulta total de vendas dentro do período
    $sql_vendas = "SELECT SUM(valor_total) AS total_vendas FROM vendas WHERE data_venda BETWEEN ? AND ?";
    $stmt_vendas = $conn->prepare($sql_vendas);
    $stmt_vendas->bind_param("ss", $data_inicio, $data_fim);
    $stmt_vendas->execute();
    $result_vendas = $stmt_vendas->get_result();

    $total_vendas = 0;
    if ($result_vendas) {
        $row = $result_vendas->fetch_assoc();
        $total_vendas = $row['total_vendas'] ?? 0;
    }

    // Consulta total de produtos vendidos dentro do período
    $sql_quantidades = "SELECT SUM(quantidade) AS total_quantidades FROM vendas WHERE data_venda BETWEEN ? AND ?";
    $stmt_quantidades = $conn->prepare($sql_quantidades);
    $stmt_quantidades->bind_param("ss", $data_inicio, $data_fim);
    $stmt_quantidades->execute();
    $result_quantidades = $stmt_quantidades->get_result();

    $total_quantidades = 0;
    if ($result_quantidades) {
        $row = $result_quantidades->fetch_assoc();
        $total_quantidades = $row['total_quantidades'] ?? 0;
    }

    // Consulta total de vendas concluídas na tabela faturamento
    $sql_faturamento = "SELECT COUNT(*) AS total_faturamento FROM faturamento WHERE data_venda BETWEEN ? AND ?";
    $stmt_faturamento = $conn->prepare($sql_faturamento);
    $stmt_faturamento->bind_param("ss", $data_inicio, $data_fim);
    $stmt_faturamento->execute();
    $result_faturamento = $stmt_faturamento->get_result();

    $total_faturamento = 0;
    if ($result_faturamento) {
        $row = $result_faturamento->fetch_assoc();
        $total_faturamento = $row['total_faturamento'] ?? 0;
    }


    // Consulta perdas dentro do período
    $sql_perdas = "SELECT despesa, SUM(valor) AS total_perdas FROM perdas WHERE data_registro BETWEEN ? AND ? GROUP BY despesa";

    $stmt_perdas = $conn->prepare($sql_perdas);
    $stmt_perdas->bind_param("ss", $data_inicio, $data_fim);
    $stmt_perdas->execute();
    $result_perdas = $stmt_perdas->get_result();

    $perdas = [];
    $subtotal_perdas = 0;
    if ($result_perdas) {
        while ($row = $result_perdas->fetch_assoc()) {
            $perdas[] = $row;
            $subtotal_perdas += $row['total_perdas'];
        }
    }

    // Calcula o lucro líquido (Total de Vendas - Subtotal de Perdas)
    $lucro_liquido = $total_vendas - $subtotal_perdas;

    // Fechar a conexão após as consultas
    CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balanço Geral</title>
    <style>
         body{
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px;
            background-color: rgb(0, 37, 160);

        }
        a{
            text-decoration: none;
        }
        header{
            background-color: rgb(21, 4, 98  );
            padding: 10px;
        }
        .btn-abrir{
            color: white;
            font-size: 20px;
        }
        nav{
            height: 0%;
            width: 250px;
            background-color: rgb(21, 4, 98  ) ;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
            overflow: hidden;
            transition: width 0.3s;
        }
        nav a{
            color: white;
            font-size: 25px;
            display: block;
            padding: 12px 10px 12px 32px;
        }
        nav a:hover{
            color: rgb(21, 4, 98  );
            background-color: white;
        }
        main{
            padding: 10px;
            transition: margin-left 0.5s;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
            background-color: #f2f2f2;
        }
        tfoot {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .container {
            background-color: rgb(181, 179, 199);
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        div{
            
            background-color: #060642 ;
            padding: 5px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <!--criei uma class para usar no css e não ter conflito com outros links-->
        <a href="#" class="btn-abrir" onclick="abrirMenu()">&#9776; Menu</a>

    </header>
   
    <nav id="menu">
        <a href="#" onclick="facharMenu()">&times; Fechar</a>
        <a href="painel.php">Voltar</a>
        <!--<a href="#">Sobre</a>
        <a href="#">Contato</a>
        <a href="#">Mais opções</a>-->
    </nav>

    <main id="conteudo">
        
        <div class="container">
            <div></div>
            <h2 style="text-align: center;">Balanço Geral</h2>
    
            <!-- Formulário para selecionar datas -->
            <form method="GET">
                <label for="data_inicio">Data Início:</label>
                <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>" required>
    
                <label for="data_fim">Data Fim:</label>
                <input type="date" name="data_fim" value="<?php echo $data_fim; ?>" required>
    
                <button type="submit">Filtrar</button>
            </form>
    
            <table>
                <tr>
                    <th>Total de Vendas </th>
                    <th>Total de Produtos Vendidos</th>
                </tr>
                <tr>
                    <td>R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></td>
                    <td><?php echo number_format($total_quantidades, 0, ',', '.'); ?></td>
                </tr>
            </table>
    
            <h2 style="text-align: center;">Faturamento</h2>
            <table>
                <tr>
                    <th>Total de Vendas Concluídas</th>
                </tr>
                <tr>
                    <td><?php echo number_format($total_faturamento, 0, ',', '.'); ?></td>
                </tr>
            </table>
            
            <h2 style="text-align: center;">Total de Perdas / Despesa </h2>
            <table>
                <tr>
                    <th>Despesa / Perdas</th>
                    <th>Total</th>
                </tr>
                <?php if (!empty($perdas)): ?>
                    <?php foreach ($perdas as $perda): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($perda['despesa']); ?></td>
                        <td>R$ <?php echo number_format($perda['total_perdas'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tfoot>
                        <tr>
                            <td>Subtotal</td>
                            <td>R$ <?php echo number_format($subtotal_perdas, 2, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Nenhuma perda encontrada no período selecionado.</td>
                    </tr>
                <?php endif; ?>
            </table>
    
            <h2 style="text-align: center;">Lucro Líquido</h2>
            <table>
                <tr>
                    <th>Total de Vendas</th>
                    <th>Subtotal de Perdas</th>
                    <th>Lucro Líquido</th>
                </tr>
                <tr>
                    <td>R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($subtotal_perdas, 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($lucro_liquido, 2, ',', '.'); ?></td>
                </tr>
            </table>
        </div>
    </main>

    <script>
        function abrirMenu() {
            document.getElementById('menu').style. height = '100%';
            document.getElementById('conteudo').style.marginLeft = '20%';
        }
        function facharMenu(){
            document.getElementById('menu').style. height = '0%'
            document.getElementById('conteudo').style.marginLeft = '0%';
        }
        // Função para capturar o pressionamento da tecla seta para esquerda
        document.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                    window.location.href = 'painel.php';  // Redireciona para o formulário
                }
            });
    </script>

</body>
</html>
