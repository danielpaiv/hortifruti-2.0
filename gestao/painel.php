
<?php

    session_start();
    
    include 'db_connection.php';

    // Configurar o fuso horário para horário de Brasília
    date_default_timezone_set('America/Sao_Paulo');

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Obter a data atual no formato 'YYYY-MM-DD'
    $data_atual = date('Y-m-d');

    // Variáveis para filtros
    $data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
    $data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

    // Adicionar o filtro de período ou usar a data do dia atual como padrão
    if (!empty($data_inicio) && !empty($data_fim)) {
        // Filtro por período (datas fornecidas)
        $filtro_data = " WHERE data_venda BETWEEN '$data_inicio' AND '$data_fim'";
    } else {
        // Filtro padrão: data atual
        $filtro_data = " WHERE DATE(data_venda) = '$data_atual'";
    }

    // Consultar o total de vendas com base no filtro
    $sql_total_vendas = "SELECT SUM(valor_total) AS total_vendas FROM vendas" . $filtro_data;
    $result_total_vendas = $conn->query($sql_total_vendas);
    $total_vendas = 0;

    if ($result_total_vendas->num_rows > 0) {
        $row = $result_total_vendas->fetch_assoc();
        $total_vendas = $row['total_vendas'];
    }

    // Consultar o total de itens vendidos com base no filtro
    $sql_total_itens = "SELECT produto, SUM(quantidade) AS total_quantidade, SUM(valor_total) AS total_valor 
    FROM vendas" . $filtro_data . " GROUP BY produto";
    $result_total_itens = $conn->query($sql_total_itens);

    // Consultar o estoque para mostrar o restante de itens com base no filtro
    $sql_estoque = "
        SELECT 
            e.produto,
            e.quantidade AS quantidade_estoque,
            IFNULL(SUM(v.quantidade), 0) AS quantidade_vendida,
            (e.quantidade - IFNULL(SUM(v.quantidade), 0)) AS quantidade_restante
        FROM 
            estoque e
        LEFT JOIN 
            (SELECT * FROM vendas" . $filtro_data . ") v ON e.produto = v.produto
        GROUP BY 
            e.produto";
    $result_estoque = $conn->query($sql_estoque);



    // Verificar se o filtro "desconsiderar vendas zeradas" está ativo
    $desconsiderar_vendas_zeradas = isset($_POST['filtro_zeradas']) && $_POST['filtro_zeradas'] == '1';

    // Filtro para vendas zeradas
    $filtro_zeradas = $desconsiderar_vendas_zeradas ? "HAVING quantidade_vendida > 0" : "";

    // Consultar o estoque com base no filtro de vendas zeradas
    $sql_estoque = "
        SELECT 
            e.produto,
            e.quantidade AS quantidade_estoque,
            IFNULL(SUM(v.quantidade), 0) AS quantidade_vendida,
            (e.quantidade - IFNULL(SUM(v.quantidade), 0)) AS quantidade_restante
        FROM 
            estoque e
        LEFT JOIN 
            (SELECT * FROM vendas) v ON e.produto = v.produto
        GROUP BY 
            e.produto
        $filtro_zeradas";

    $result_estoque = $conn->query($sql_estoque);


    // Verificar se o filtro "desconsiderar estoque zerado" está ativo
    $desconsiderar_estoque_zerado = isset($_POST['filtro_zerado']) && $_POST['filtro_zerado'] == '1';

    // Adicionar o filtro ao SQL, se necessário
    $filtro_zerado = $desconsiderar_estoque_zerado ? "HAVING quantidade_restante > 0" : "";

    // Consultar o estoque com base no filtro de estoque zerado
    $sql_estoque = "
        SELECT 
            e.produto,
            e.quantidade AS quantidade_estoque,
            IFNULL(SUM(v.quantidade), 0) AS quantidade_vendida,
            (e.quantidade - IFNULL(SUM(v.quantidade), 0)) AS quantidade_restante
        FROM 
            estoque e
        LEFT JOIN 
            vendas v ON e.produto = v.produto
        GROUP BY 
            e.produto
        $filtro_zerado
    ";

    // Executar a consulta
    $result_estoque = $conn->query($sql_estoque);

    
    //esse codigo é responsável por criptografar a pagina viinculado ao codigo teste login.
    // Verificar se as variáveis de sessão 'email' e 'senha' não estão definidas
    if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
        unset($_SESSION['nome']);
        unset($_SESSION['senha']);
        header('Location: index.php');
        exit();  // Importante adicionar o exit() após o redirecionamento
    }

    // Fechar a conexão após as consultas
    CloseCon($conn);
?>





<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu lateral</title>
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
        
        /*odd aplicado em todos elementos inpares*/
        table tr:nth-child(odd){
            background-color: #ddd;

        }
        /*odd aplicado em todos elementos pares*/
        table tr:nth-child(even){
            background-color:white ;

        }
        div{
            display: inline-block;
            background-color: rgb(181, 179, 199);
            padding: 5px;
            text-align: center;
            width: 100%;
        }
        legend{
            color: white;
        }
        
        p{
            color: white;
        }
        .container {
            background-color: rgb(181, 179, 199);
            max-width: 1400px; /* Aumentei o tamanho para acomodar as tabelas lado a lado */
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            
        }
        .cont{
            background-color: rgb(21, 4, 98  ) ;
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
        .tables-container {
            display: flex; /* Usando Flexbox */
            gap: 20px; /* Espaçamento entre as tabelas */
            
        }
        .table-wrapper {
            flex: 1; /* As tabelas ocuparão a mesma largura */
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
        <a href="financeiro.php">financeiro</a>
        <a href="balanco_geral.php">Balanço geral</a>
        <a href="editarEstoque.php">Editar estoque</a>
        <a href="formulario_estoque.php">Adicionar produtos</a>
        <a href="relatorio.php">Gerar relatorio</a>
        <a href="perdas.php">Registrar Perdas</a>
        <a href="despesas.php">Registrar Despesas</a>
        <a href="visualizar_perdas.php">Perdas\Despesas</a>
        <a href="sair.php">Sair</a>
    </nav>

    <main id="conteudo">
            
            <div class="container">
            <div class= "cont"></div>
                <div class="tables-container">
                
                    <div class="table-wrapper">

                     <!-- Formulário para filtros -->
                            <form method="GET" action="">
                                <label for="data_inicio">Data Início:</label>
                                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $data_inicio; ?>">
                                
                                <label for="data_fim">Data Fim:</label>
                                <input type="date" id="data_fim" name="data_fim" value="<?php echo $data_fim; ?>">

                                <button type="submit">Filtrar</button>
                            </form>

                        <h3>Itens Vendidos</h3>
                        <table class="table-wrapper">
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

                    <div class="table-wrapper">
    <h3>Estoque Atual</h3>

    <label for="filtroNome">Filtrar por nome:</label>
    <input type="text" id="filtroNome" onkeyup="filtrarPorNome()"><br><br>

    <!-- Formulário para filtrar vendas zeradas 
    <form method="POST" class="filter-form">
        <input type="hidden" name="filtro_zeradas" value="<?= $desconsiderar_vendas_zeradas ? '0' : '1' ?>">
        <button type="submit">
            <?= $desconsiderar_vendas_zeradas ? 'Mostrar Todas as Vendas' : 'Desconsiderar Vendas Zeradas' ?>
        </button>
    </form>-->

     <!-- Formulário para alternar o filtro -->
     <form method="POST">
        <input type="hidden" name="filtro_zerado" value="<?= $desconsiderar_estoque_zerado ? '0' : '1' ?>">
        <button type="submit">
            <?= $desconsiderar_estoque_zerado ? 'Mostrar Todos os Produtos' : 'Desconsiderar Estoque Zerado' ?>
        </button>
    </form>

    <button onclick="imprimirTabela()">Imprimir Tabela</button>

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
                    // Filtrar produtos com estoque zerado, se necessário
                    if ($desconsiderar_estoque_zerado && $row['quantidade_restante'] <= 0) {
                        continue;
                    }
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

<script>
    function filtrarPorNome() {
        const filtro = document.getElementById('filtroNome').value.toUpperCase();
        const tabela = document.getElementById('estoqueAtual');
        const linhas = tabela.getElementsByTagName('tr');

        for (let i = 1; i < linhas.length; i++) {
            const celula = linhas[i].getElementsByTagName('td')[0];
            if (celula) {
                const textoCelula = celula.textContent || celula.innerText;
                linhas[i].style.display = textoCelula.toUpperCase().includes(filtro) ? '' : 'none';
            }
        }
    }

    function imprimirTabela() {
        const tabela = document.getElementById('estoqueAtual').outerHTML;
        const janelaImpressao = window.open('', '_blank');
        janelaImpressao.document.write('<html><head><title>Impressão de Tabela</title></head><body>');
        janelaImpressao.document.write('<h3>Estoque Atual</h3>');
        janelaImpressao.document.write(tabela);
        janelaImpressao.document.write('</body></html>');
        janelaImpressao.document.close();
        janelaImpressao.print();
    }
</script>

        
                </div>

            </div>

    </main>
    
    <script>

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
        function abrirMenu() {
            document.getElementById('menu').style. height = '100%';
            document.getElementById('conteudo').style.marginLeft = '20%';
        }
        function facharMenu(){
            document.getElementById('menu').style. height = '0%'
            document.getElementById('conteudo').style.marginLeft = '0%';
        }
        
    </script>

</body>
</html>