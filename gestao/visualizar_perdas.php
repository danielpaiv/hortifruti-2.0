<?php

    session_start();

    include 'db_connection.php';

    // Abrir conexão com o banco de dados
    $conn = OpenCon();
    
    // Verificar se um filtro de data foi enviado
    $filtro_data = "";
    if (!empty($_POST['data_inicio']) && !empty($_POST['data_fim'])) {
        $data_inicio = $conn->real_escape_string($_POST['data_inicio']);
        $data_fim = $conn->real_escape_string($_POST['data_fim']);
        $filtro_data = "WHERE data_perda BETWEEN '$data_inicio' AND '$data_fim'";
    }

    // Consultar as perdas com base no filtro de data
    $sql_perdas = "
        SELECT 
            id, 
            DATE_FORMAT(data_perda, '%d/%m/%Y') AS data_perda_formatada, 
            produto,
            despesa,
            funcionario, 
            quantidade, 
            valor, 
            motivo, 
            responsavel 
        FROM perdas
        $filtro_data
        ORDER BY data_perda DESC";
    $result_perdas = $conn->query($sql_perdas);

    //esse codigo é responsável por criptografar a pagina viinculado ao codigo teste login.
    // Verificar se as variáveis de sessão 'email' e 'senha' não estão definidas
    if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
        unset($_SESSION['nome']);
        unset($_SESSION['senha']);
        header('Location: index.php');
        exit();  // Importante adicionar o exit() após o redirecionamento
    }

    // Fechar a conexão após a consulta
    CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Perdas</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: rgb(0, 37, 160);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            background-color: #b5b3c7;
        }
        
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form label {
            margin-right: 10px;
        }
        .filter-form input, .filter-form button {
            padding: 10px;
            margin-right: 10px;
        }
        .filter-form button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #0056b3;
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
        .cont{
            background-color: #060642 ;
            padding: 5px;
        }
        label{
            color:white;
        }
        h2{
            color:white;
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
        <a href="Painel.php">Painel</a>
        <a href="perdas.php">Registrar Perdas</a>
        <a href="financeiro.php">financeiro</a>
        <a href="editarEstoque.php">Editar estoque</a>
        <a href="formulario_estoque.php">Adicionar produtos</a>
        <a href="relatorio.php">Gerar relatorio</a>
        <a href="sair.php">Sair</a>
    </nav>
    <h2>Visualizar Perdas</h2>

    <!-- Formulário para filtrar perdas por data -->
    <form method="POST" class="filter-form">
        <label for="data_inicio">Data Início:</label>
        <input type="date" id="data_inicio" name="data_inicio" value="<?= isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '' ?>">

        <label for="data_fim">Data Fim:</label>
        <input type="date" id="data_fim" name="data_fim" value="<?= isset($_POST['data_fim']) ? $_POST['data_fim'] : '' ?>">

        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabela para visualizar as perdas -->

    <main id=conteudo>

        <div class=cont></div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data da Perda</th>
                    <th>Perdas</th>
                    <th>Despesa</th>
                    <th>Funcionario</th>
                    <th>Quantidade</th>
                    <th>Valor</th>
                    <th>Motivo</th>
                    <!--<th>Responsável</th>-->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_perdas->num_rows > 0) {
                    while ($row = $result_perdas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['data_perda_formatada'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['produto']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['despesa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['funcionario']) . "</td>";
                        echo "<td>" . number_format($row['quantidade'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['motivo']) . "</td>";
                        //echo "<td>" . htmlspecialchars($row['responsavel']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Nenhuma perda encontrada para o período selecionado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
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
