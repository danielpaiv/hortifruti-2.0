<?php   

    session_start();

    include 'db_connection.php';

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Consultar os dados da tabela estoque
    $sql_estoque = "SELECT * FROM estoque";
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
    <title>Editar estoque - Hortifruti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: rgb(0, 37, 160);
        }
        .container {
            background-color: rgb(181, 179, 199);
            max-width: 1000px;
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
        .imprimir-btn {
            background-color: #007bff;
        }
        .imprimir-btn:hover {
            background-color: #0056b3;
        }
        a {
            text-decoration: none;
            color: #fff;
        }

        /* Cor de fundo da linha em foco */
        tr.focused {
            background-color: #f0f0f0; /* Cor clara, você pode personalizar */
        }
        h2{
            color:white;
        }
        label{
            color:white;
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
        h2{
            color:black;
        }
        label{
            color:black;
        }
        .cont{
            background-color: #060642 ;
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
        <a href="painel.php">Painel</a>
        <a href="financeiro.php">Financeiro</a>
        <a href="formulario_estoque.php">Adicionar estoque</a>
        <a href="perdas.php">Registrar Perdas</a>
        <a href="sair.php">Sair</a>
    </nav>
    <main id="conteudo">

        <div>

            <div class="container">
            <div class=cont></div>
                <div>
                    
                    <h2> Editar Estoque - Hortifruti</h2>

                    <script>

                        
                        // Verifica se o localStorage contém a chave "focusOnFiltroNome"
                        window.onload = function() {
                            if (localStorage.getItem("focusOnFiltroNome") === "true") {
                                document.getElementById("filtroNome").focus();
                                // Após definir o foco, você pode limpar a chave no localStorage
                                localStorage.removeItem("focusOnFiltroNome");
                            }
                        }

                        window.onload = function() {
                            document.getElementById("filtroNome").focus();
                        };

                        document.addEventListener("keydown", function(event) {
                            if (event.key === "ArrowDown" || event.key === "ArrowUp") { 
                            const botoesEnviar = Array.from(document.querySelectorAll("table#estoqueTabela button"))
                                .filter(btn => btn.offsetParent !== null); // Seleciona apenas os botões visíveis

                            const elementoAtivo = document.activeElement; // Elemento atualmente focado
                            let proximoIndice = 0; // Índice do próximo botão a ser focado

                            // Encontra o índice do botão atualmente ativo
                            for (let i = 0; i < botoesEnviar.length; i++) {
                                if (botoesEnviar[i] === elementoAtivo) {
                                    if (event.key === "ArrowDown") {
                                        proximoIndice = (i + 1) % botoesEnviar.length; // Avança para o próximo ou retorna ao primeiro
                                    } else if (event.key === "ArrowUp") {
                                        proximoIndice = (i - 1 + botoesEnviar.length) % botoesEnviar.length; // Volta para o anterior ou vai para o último
                                    }
                                    break;
                                }
                            }

                                // Foca no próximo botão
                                if (botoesEnviar.length > 0) {
                                    const proximoBotao = botoesEnviar[proximoIndice];
                                    const linha = proximoBotao.closest('tr'); // Encontrando a linha do botão

                                    // Chama a função para mudar a cor da linha
                                    mudarCorDaLinha(linha);

                                    proximoBotao.focus();
                                }

                                event.preventDefault(); // Evita comportamento padrão
                            }
                        });


                        document.addEventListener("keydown", function(event) {
                            // Verifica se a tecla pressionada foi asseta direita simples ou aspas duplas
                            if (event.key === 'ArrowRight' || event.key === "'") {
                                window.location.href = "editarEstoque.php"; // Redireciona para o arquivo estoque.php
                            }

                            
                        });

                        // Função para capturar o pressionamento da tecla ESC
                        document.addEventListener('keydown', function(event) {
                            if (event.key === 'ArrowLeft') {  // Se a tecla pressionada for 'ESC'
                                window.location.href = 'painel.php';  // Redireciona para o formulário
                            }
                        });

                        function mudarCorDaLinha(linha) {
                            // Remove a classe "focused" de todas as linhas
                            document.querySelectorAll("table#estoqueTabela tr").forEach(function(row) {
                                row.classList.remove("focused");
                            });

                            // Adiciona a classe "focused" à linha
                            linha.classList.add("focused");
                        }

                    </script>

                    <label for="filtroNome">Filtrar por nome:</label>
                        <input type="text" id="filtroNome" onkeyup="filtrarPorNome()">

                    <!-- Tabela de Estoque -->
                    <table id="estoqueTabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço</th>
                                <th>Preço unitário</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($result_estoque->num_rows > 0) {
                                    while($row = $result_estoque->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["id"] . "</td>";
                                        echo "<td>" . $row["produto"] . "</td>";
                                        echo "<td>" . $row["quantidade"] . "</td>";
                                        echo "<td>R$ " . number_format($row["preco"], 2, ',', '.') . "</td>";
                                        echo "<td>R$ " . number_format($row["preco_unitario"], 2, ',', '.') . "</td>";
                                        echo "<td>" . $row["data_hora"] . "</td>";

                                        //echo "<td><button onclick=\"enviarProduto(" . $row['id'] . ")\">Enviar</button></td>";
                                        echo "<td>
                                        <a href='edit.php?id=" . $row["id"] . "'>
                                            <button>Editar</button>
                                        </a>
                                    </td>";
                                        echo "</tr>";
                                    }
                                    // Linha em branco com texto dentro
                                    echo "<tr><td colspan='5'>Clik no botõa para adicionaar</td></tr>";
                                    echo "<tr><td colspan='5'></td></tr>";
                                } else {
                                    echo "<tr><td colspan='6'>Nenhum dado encontrado</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>

                    <!-- Botão de Imprimir -->
                    <button class="imprimir-btn" onclick="imprimirTabela()">Imprimir Tabela</button>
                </div>
            </div>
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

        function imprimirTabela() {
            var conteudo = document.querySelector('table').outerHTML;
            var janela = window.open('', '_blank');
            
            // Adicionando o estilo para a fonte Arial
            janela.document.write('<html><head><title>Imprimir Estoque</title>');
            janela.document.write('<style>body { font-family: Arial, sans-serif; }</style>'); // Fonte Arial
            janela.document.write('</head><body>');
            janela.document.write(conteudo);
            janela.document.write('</body></html>');
            
            janela.document.close();
            janela.print();
        }

        function filtrarPorNome() {
            const input = document.getElementById('filtroNome');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('estoqueTabela');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[1]; // coluna "Nome"
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

        function irParaBotaoEnviar() {
        const table = document.getElementById('estoqueTabela');
        const tr = table.getElementsByTagName('tr');
        
            for (let i = 1; i < tr.length; i++) {
                if (tr[i].style.display !== 'none') {
                    const button = tr[i].querySelector('button');
                    if (button) {
                        button.focus(); // Move o cursor para o botão
                        return;
                    }
                }
            }
        }

        document.getElementById('filtroNome').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Impede a submissão padrão do formulário
                irParaBotaoEnviar();
            }
        });

        function enviarProduto(id) {
            // Salvar o id do produto no localStorage
            localStorage.setItem('product-id', id);
            
            // Redirecionar para o formulário
            window.location.href = "formulario_hortifruti.php";
        }

    </script>
</body>
</html>
