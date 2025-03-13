
<?php

    session_start();

    include 'db_connection.php';

    // Definir a data atual
    $data_atual = date('Y-m-d');

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Consultar os produtos no estoque
    $sql_estoque = "SELECT id, produto FROM estoque";
    $result_estoque = $conn->query($sql_estoque);

     // Consultar os nomes do usuarios
     $sql_usuarios = "SELECT id, nome FROM usuarios";
     $result_usuarios = $conn->query($sql_usuarios);

      // Consultar os nomes da despesas
      $sql_despesas = "SELECT id, aluguel FROM despesas";
      $result_despesas = $conn->query($sql_despesas);

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
    <title>Formulário de Despesas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: rgb(0, 37, 160);
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background: #b5b3c7;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea, 
        .form-group button {
            width: 95%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group button {
            background-color: #007BFF;
            color: #fff;
            cursor: pointer;
        }
        .form-group button:hover {
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

        legend{
            color: white;
        }
        h3{
            color: blue;
        }
        p{
            color: white;
        }
        .cont{
            background-color: #060642 ;
            padding: 5px;
        }
        #loss-date{
            width: 110px;
        }
        #quantity{
            width: 100px;
        }
        #value{
            width: 100px;
        }
        #product{
            width: 260px;
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
        <a href="visualizar_perdas.php">Perdas\Despesas</a>
        <a href="perdas.php">Registrar Perdas</a>
        <a href="financeiro.php">financeiro</a>
        <a href="editarEstoque.php">Editar estoque</a>
        <a href="formulario_estoque.php">Adicionar produtos</a>
        <a href="relatorio.php">Gerar relatorio</a>
        <a href="sair.php">Sair</a>
    </nav>

    <main id="conteudo">

        <div class="form-container">

            <div class=cont></div>

            <h2>Formulário de despesas</h2>

             <form id="loss-form" action="process_perda copy.php" method="POST">
        <!-- Data da Perda -->
        <div class="form-group">
            <label for="loss-date">Data da Despesa:</label>
            <input type="date" id="loss-date" name="loss-date" required autofocus>
        </div>

        <!-- Produto 
        <div class="form-group">
            <label for="product">Produto:</label>
            <select id="product" name="product" >
                <option value="">Selecione o produto</option>
                <?php
                    if ($result_estoque->num_rows > 0) {
                        while($row = $result_estoque->fetch_assoc()) {
                            echo "<option value='" . $row['produto'] . "'>" . $row['produto'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhum produto cadastrado no estoque</option>";
                    }
                    ?>
            </select>
        </div>
       -->
         <!-- Produto -->
         <div class="form-group">
            <label for="despesa">Despesas:</label>
            <select id="despesa" name="despesa" >
                <option value="">Selecione a despesa</option >
                <?php
                    if ($result_despesas->num_rows > 0) {
                        while($row = $result_despesas->fetch_assoc()) {
                            echo "<option value='" . $row['aluguel'] . "'>" . $row['aluguel'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhuma despesa cadastrado</option>";
                    }
                    ?>
            </select>
        </div>

         <!-- Responsável -->
         <div class="form-group">
            <label for="funcionario">Funcionário:</label>
            <select id="funcionario" name="funcionario" required>
                <option value="">Selecione o Funcionário</option>
                <?php
                    if ($result_usuarios->num_rows > 0) {
                        while($row = $result_usuarios->fetch_assoc()) {
                            echo "<option value='" . $row['nome'] . "'>" . $row['nome'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhum usuario cadastrado </option>";
                    }
                    ?>
            </select>
        </div>

        <!-- Quantidade 
        <div class="form-group">
            <label for="quantity">Quantidade Perdida:</label>
            <input type="number" id="quantity" name="quantity" step="0.01" min="0" required>
        </div>
        -->            
        <!-- Valor -->
        <div class="form-group">
            <label for="value">Valor:</label>
            <input type="number" id="value" name="valor" step="0.01" min="0" required>
        </div>

        <!-- Motivo da Perda -->
        <div class="form-group">
            <label for="reason">Descrição:</label>
            <textarea id="reason" name="reason" rows="4" placeholder="Descreva o motivo da perda..." required></textarea>
        </div>

        <!-- Responsável 
        <div class="form-group">
            <label for="responsible">Responsável:</label>
            <select id="responsible" name="responsible" >
                <option value="">Selecione o responsável</option>
                <?php
                    if ($result_usuarios->num_rows > 0) {
                        while($row = $result_usuarios->fetch_assoc()) {
                            echo "<option value='" . $row['nome'] . "'>" . $row['nome'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhum usuario cadastrado </option>";
                    }
                    ?>
            </select>
        </div>-->

        <!-- Botão Enviar -->
        <div class="form-group">
            <button type="submit">Registrar Perda</button>
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
        // Função para capturar o pressionamento da tecla seta para esquerda
        document.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                    window.location.href = 'painel.php';  // Redireciona para o formulário
                }
            });
        
    </script>

</body>
</html>
