<?php

    session_start();

    //esse codigo é responsável por criptografar a pagina viinculado ao codigo teste login.
        // Verificar se as variáveis de sessão 'email' e 'senha' não estão definidas
        if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
            unset($_SESSION['nome']);
            unset($_SESSION['senha']);
            header('Location: index.php');
            exit();  // Importante adicionar o exit() após o redirecionamento
        }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - Estoque</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color:rgb(0, 37, 160);
        }
        .container {
            background-color:rgb(181, 179, 199);
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
            width: 110%;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 90%;
            padding: 8px;
            box-sizing: border-box;
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
        a{
            text-decoration: none;
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
        table,th,td{
            border: 1px solid black;
            border-collapse:collapse ;
        }
        th,td{
            padding: 5px 10px;
        }
        th{
            background-color: rgb(21, 4, 98);
            color: white;
        }
        /*odd aplicado em todos elementos inpares*/
        table tr:nth-child(odd){
            background-color: #ddd;

        }
        /*odd aplicado em todos elementos pares*/
        table tr:nth-child(even){
            background-color:white ;

        }
        .cont{
            background-color: rgb(21, 4, 98);
            padding: 5px;
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
            <a href="financeiro.php">financeiro</a>
            <a href="editarEstoque.php">Editar estoque</a>
            <a href="relatorio.php">Gerar relatorio</a>
            <a href="perdas.php">Registrar Perdas</a>
            <a href="visualizar_perdas.php">Perdas\Despesas</a>
            <a href="sair.php">Sair</a>
        </nav>
    <main id="conteudo">

        <div class="container">

            <div class="cont"></div>

            <h2>Cadastro de Produtos - Estoque</h2>

            <form id="stock-form" action="process_estoque.php" method="post">
                <!-- Produto -->
                <div class="form-group">
                    <label for="produto">Produto:</label>
                    <input type="text" id="produto" name="produto" required autofocus >
                </div>

                <!-- Quantidade -->
                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" step="0.01" min="0" required>
                </div>

                <!-- Preço -->
                <div class="form-group">
                    <label for="preco">Investimento (R$):</label>
                    <input type="number" id="preco" name="preco" step="0.01" min="0" readonly>
                </div>

                <!-- Preço unitario -->
                <div class="form-group">
                    <label for="preco_unitario">Preço Unitario (R$ / KG):</label>
                    <input type="number" id="preco_unitario" name="preco_unitario" step="0.01" min="0" required>
                </div>

                <!-- Descrição -->
                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="4"></textarea>
                    </div>

                <!-- Botão de Submissão -->
                    <div class="form-group">
                        <button type="submit">Cadastrar Produto</button>
                     </div>
            </form>
                
            
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

         // Função para capturar o pressionamento da tecla Esquerda
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                window.location.href = 'Painel.php';  // Redireciona para o formulário
            }
        });

    </script>
</body>
</html>
