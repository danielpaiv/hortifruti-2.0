
<?php
    // Conexão com o banco de dados
    include 'db_connection.php'; // Certifique-se de ajustar o caminho do arquivo
    $conn = OpenCon(); // Chama a função para abrir a conexão

    // Verificar se o ID foi passado
    if (!isset($_GET['id'])) {
        echo "ID do produto não especificado.";
        exit;
    }

    // Obter os dados do produto do banco de dados
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM estoque WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Produto não encontrado.";
        exit;
    }

    $produto = $result->fetch_assoc();

    CloseCon($conn);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - Edita estoque</title>
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
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
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
        .cont{
            background-color: rgb(21, 4, 98  );
            padding: 5px;
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
    </style>
</head>
<body>

            <nav id="menu">
                <a href="#" onclick="facharMenu()">&times; Fechar</a>
                <a href="Painel.php">Painel</a>
                <a href="financeiro.php">financeiro</a>
                <a href="editarEstoque.php">Editar estoque</a>
                <a href="formulario_estoque.php">Adicionar produtos</a>
                <a href="perdas.php">Registrar Perdas</a>
                <a href="sair.php">Sair</a>
            </nav>
    <main id="conteudo">
        <div class="container">
            <div class="cont"></div>
            <h2>Editar Produto - Estoque</h2>
            <form id="edit-form" action="saveEdit.php" method="post">
                <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">

                <div class="form-group">
                    <label for="produto">Produto:</label>
                    <input type="text" id="produto" name="produto" value="<?php echo $produto['produto']; ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" value="<?php echo $produto['quantidade']; ?>"step="0.01" min="0" required>
                </div>

                <!--<div class="form-group">
                    <label for="preco">Preço (R$):</label>
                    <input type="number" id="preco" name="preco" value="<?php echo $produto['preco']; ?>" step="0.01" min="0" required>
                </div>-->

                <div class="form-group">
                    <label for="preco_unitario">Preço Unitário (R$):</label>
                    <input type="number" id="preco_unitario" name="preco_unitario" value="<?php echo $produto['preco_unitario']; ?>" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" rows="4"><?php echo $produto['descricao']; ?></textarea>
                </div>

                <div class="form-group">
                <label for="data_hora"><b>Data e Hora</b></label>
                <input type="datetime-local" name="data_hora" id="data_hora" value="<?php echo date('datetime-local', strtotime($data)); ?>" required>

                </div>

                <div class="form-group">
                    <button type="submit">Salvar Alterações</button>
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
         // Função para capturar o pressionamento da tecla ESC
         document.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                    window.location.href = 'editarEstoque.php';  // Redireciona para o formulário
                }
            });
    </script>

</body>
</html>
