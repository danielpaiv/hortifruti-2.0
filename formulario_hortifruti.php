<?php
    session_start();

    // Verificar se a sessão contém os dados esperados
    if (isset($_SESSION['user_id']) && isset($_SESSION['nome'])) {
        echo 'ID  : ' . $_SESSION['user_id'] . '<br>';
        echo 'Nome  : ' . $_SESSION['nome'] . '<br>';
    } else {
        echo 'Nenhum dado de usuário encontrado na sessão.';
    }
    include 'db_connection.php';


    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Consultar os produtos no estoque
    $sql_estoque = "SELECT id, produto FROM estoque";
    $result_estoque = $conn->query($sql_estoque);

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
    <title>Formulário de Vendas - Hortifruti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color:rgb(0, 37, 160);
           display: flex;
            justify-content: space-between;
            
        }
        .container {
            font-size:120%;
            position: fixed;
            top: 20%;
            background-color:rgb(181, 179, 199);
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            border-collapse: collapse;
            display: flex;
            justify-content: space-between; /* Espaço entre os objetos */
            align-items: center; /* Alinha os objetos verticalmente no centro */
            gap: 50px; /* Espaçamento entre os objetos */

           
        }
        .form-group {
            margin-bottom: 15px;
            
        }
        #quantity{
            font-size:150%;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .buttons {
            margin-bottom: 20px;
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
        .cart {
            margin-top: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            height: 4000px; /* Ajusta a altura conforme o conteúdo */

           /* width: 100%;
            margin-left:20%;*/
        }
        #product-id{
            font-size:120%;
        }
        #product{
            font-size:100%;
        }
        #unit-price{
            font-size:120%;
        }
        tbody{
            font-size:120%;
        }
        #cash-payment{
            font-size:120%;
        }
        #card-payment{
            font-size:120%;
        }
        #pix-payment{
            font-size:120%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-collapse: collapse;
            width: 50%; /* Ajusta o tamanho das tabelas */
            max-width: 45%; /* Limita a largura para caber lado a lado */
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        thead{
            background-color: rgb(0, 37, 160);
            color: white;
            
        }
        
        

        
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <div class="buttons">
        <button><a href="verifica_sessao.php" style="color: white; text-decoration: none;">Verificar sessao</a></button>
        <button><a href="sair.php" style="color: white; text-decoration: none;"> Sair</a></button>
        <!--<button><a href="financeiro.php" style="color: white; text-decoration: none;">Financeiro</a></button>-->
        <button><a href="vendas.php" style="color: white; text-decoration: none;">💲 Vendas</a></button>
        <button onclick="setFocus()"><a href="estoque.php" style="color: white; text-decoration: none;">📦 Estoque</a></button>
        <!--<button onclick="setFocus()"><a href="editarEstoque.php" style="color: white; text-decoration: none;">Atualizar estoque</a></button>-->
    </div>

    <div class="container">
        <h2>Formulário de Vendas - Hortifruti</h2>
        <form id="sales-form">
            <!-- Campo de ID do Produto -->
            <div class="form-group">
                <label for="product-id">ID do Produto:</label>
                <input type="number" id="product-id" name="product-id" required>
            </div>

            <!-- Campo de Nome do Produto -->
            <div class="form-group">
                <label for="product">Produto:</label>
                <select id="product" name="product" required >
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

            <div class="form-group">
                <label for="quantity">Quantidade (kg):</label>
                <input type="number" id="quantity" name="quantity" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="unit-price">Preço Unitário (R$):</label>
                <input type="number" id="unit-price" name="unit-price" step="0.01" min="0" required readonly>
            </div>

            <div class="form-group">
                <button type="button" id="add-to-cart">🛒 Adicionar ao Carrinho</button>
            </div>
        </form>
    </div>
    
    <div class="cart">
        <h3></h3>
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade (kg)</th>
                    <th>Preço Unitário (R$)</th>
                    <th>Valor Total (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Forma de pagamento -->
        <div class="form-group">
            <label>
                <input type="radio" name="payment-method" value="cash">
                Dinheiro (R$):
            </label>
            <input type="number" id="cash-payment" step="0.01" min="0" placeholder="💵 Valor Dinheiro">
        </div>

        <div class="form-group">
            <label>
                <input type="radio" name="payment-method" value="card">
                Cartão (R$):
            </label>
            <input type="number" id="card-payment" step="0.01" min="0" placeholder="💳 Valor Cartão">
        </div>

        <div class="form-group">
            <label>
                <input type="radio" name="payment-method" value="pix">
                PIX (R$):
            </label>
            <input type="number" id="pix-payment" step="0.01" min="0" placeholder="💠 Valor Pix">
        </div>

        <div class="form-group">
            <button id="finalize-sale">✔️ Finalizar Venda</button>
            <button id="print-cart">🖨️ Imprimir Carrinho</button>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const finalizeButton = document.getElementById("finalize-sale");

            // Função para simular o clique no botão "Finalizar Venda" quando pressionar f
            document.addEventListener("keydown", (event) => {
                if (event.key === "f") {
                    finalizeButton.click(); // Simula o clique no botão
                }
            });
        });

        document.addEventListener("DOMContentLoaded", () => {
            const printButton = document.getElementById("print-cart");

            // Função para simular o clique no botão "Imprimir" quando pressionar i
            document.addEventListener("keydown", (event) => {
                if (event.key === "i") {
                    printButton.click(); // Simula o clique no botão
                }
            });
        });

    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cashInput = document.getElementById("cash-payment");
            const cardInput = document.getElementById("card-payment");
            const pixInput = document.getElementById("pix-payment");
            const radios = document.querySelectorAll("input[name='payment-method']");

            // Função para obter o valor total do carrinho
            const getTotalCartValue = () => {
                const tableBody = document.querySelector("#cart-table tbody");
                let total = 0;

                tableBody.querySelectorAll("tr").forEach(row => {
                    const totalCell = row.querySelector("td:nth-child(4)"); // 4ª coluna: Valor Total
                    if (totalCell) {
                        total += parseFloat(totalCell.textContent.replace(",", ".") || "0");
                    }
                });

                return total.toFixed(2); // Retorna o total formatado
            };

            // Função para calcular e preencher os valores restantes
            const updatePaymentFields = (changedInput) => {
                const total = parseFloat(getTotalCartValue());
                const cashValue = parseFloat(cashInput.value) || 0;
                const cardValue = parseFloat(cardInput.value) || 0;
                const pixValue = parseFloat(pixInput.value) || 0;

                const remaining = total - (cashValue + cardValue + pixValue);

                radios.forEach(radio => {
                    if (radio.checked) {
                        if (radio.value === "cash" && changedInput !== cashInput) {
                            cashInput.value = Math.max(remaining, 0).toFixed(2);
                        } else if (radio.value === "card" && changedInput !== cardInput) {
                            cardInput.value = Math.max(remaining, 0).toFixed(2);
                        } else if (radio.value === "pix" && changedInput !== pixInput) {
                            pixInput.value = Math.max(remaining, 0).toFixed(2);
                        }
                    }
                });
            };

            // Função para configurar o comportamento da tecla Enter
            const setupEnterKeyForRadio = (inputElement, radioValue) => {
                inputElement.addEventListener("keydown", (event) => {
                    if (event.key === "Enter") {
                        const targetRadio = Array.from(radios).find(radio => radio.value === radioValue);
                        if (targetRadio) {
                            targetRadio.checked = true; // Seleciona o botão de rádio correspondente
                            updatePaymentFields(inputElement); // Atualiza o campo selecionado com o valor restante
                        }
                    }
                });
            };

            // Adiciona eventos aos campos de entrada para a tecla Enter
            setupEnterKeyForRadio(cashInput, "cash");
            setupEnterKeyForRadio(cardInput, "card");
            setupEnterKeyForRadio(pixInput, "pix");

            // Adiciona eventos de input para atualizar os valores dinamicamente
            cashInput.addEventListener("input", () => updatePaymentFields(cashInput));
            cardInput.addEventListener("input", () => updatePaymentFields(cardInput));
            pixInput.addEventListener("input", () => updatePaymentFields(pixInput));

            // Adiciona evento de mudança nos botões de rádio
            radios.forEach(radio => {
                radio.addEventListener("change", () => {
                    // Atualiza os campos de pagamento com base no rádio selecionado
                    updatePaymentFields(null);
                });
            });

            // Adiciona a funcionalidade de atalhos de teclado
            document.addEventListener("keydown", function(event) {
                const total = parseFloat(getTotalCartValue());

                if (event.key === "d" || event.key === "D") { // Tecla D para Dinheiro
                    const targetRadio = Array.from(radios).find(radio => radio.value === "cash");
                    if (targetRadio) {
                        targetRadio.checked = true;
                        cashInput.value = total.toFixed(2); // Preenche com o valor total do carrinho
                        updatePaymentFields(cashInput);
                    }
                } else if (event.key === "c" || event.key === "C") { // Tecla C para Cartão
                    const targetRadio = Array.from(radios).find(radio => radio.value === "card");
                    if (targetRadio) {
                        targetRadio.checked = true;
                        cardInput.value = total.toFixed(2); // Preenche com o valor total do carrinho
                        updatePaymentFields(cardInput);
                    }
                } else if (event.key === "p" || event.key === "P") { // Tecla P para PIX
                    const targetRadio = Array.from(radios).find(radio => radio.value === "pix");
                    if (targetRadio) {
                        targetRadio.checked = true;
                        pixInput.value = total.toFixed(2); // Preenche com o valor total do carrinho
                        updatePaymentFields(pixInput);
                    }
                }
            });
        });
    </script>


    <script>

        document.addEventListener("keydown", function(event) {
            // Verifica se a tecla pressionada foi seta para direita
            if (event.key === 'ArrowRight' || event.key === "'") {
                window.location.href = "estoque.php"; // Redireciona para o arquivo estoque.php
            }

            
        });

        document.addEventListener("keydown", function(event) {
            // Verifica se a tecla pressionada foi a letra V
            if (event.key === 'v' || event.key === "'") {
                window.location.href = "vendas.php"; // Redireciona para o arquivo estoque.php
            }

            
        });

        document.addEventListener("keydown", function(event) {
            // Verifica se a tecla pressionada foi a letra V
            if (event.key === 's' || event.key === "'") {
                window.location.href = "sair.php"; // Redireciona para o arquivo estoque.php
            }

            
        });

        document.addEventListener("keydown", function(event) {
            if (event.key === " ") { // Verifica se a tecla pressionada é a barra de espaço
                const cartFormGroups = document.querySelectorAll(".cart .form-group input, .cart .form-group button"); // Seleciona inputs e botões no form-group do carrinho
                const elementoAtivo = document.activeElement; // Elemento atualmente focado
                let proximoIndice = 0; // Índice do próximo elemento a ser focado

                // Encontra o índice do elemento atualmente ativo
                for (let i = 0; i < cartFormGroups.length; i++) {
                    if (cartFormGroups[i] === elementoAtivo) {
                        proximoIndice = (i + 1) % cartFormGroups.length; // Avança para o próximo ou retorna ao primeiro
                        break;
                    }
                }

                // Foca no próximo elemento do grupo de formulário do carrinho
                cartFormGroups[proximoIndice].focus();
                event.preventDefault(); // Evita comportamento padrão da tecla espaço
            }
        });


        function setFocus() {
            // Armazena um valor no localStorage que indica que o foco deve ser colocado no campo filtroNome
            localStorage.setItem("focusOnFiltroNome", "true");
        }

        window.onload = function() {
            // Recuperar o id do produto do localStorage
            const productId = localStorage.getItem('product-id');
            
            if (productId) {
                // Preencher o campo com o id do produto
                document.getElementById('product-id').value = productId;
            }
            // Foca no campo ID do produto
            document.getElementById('product-id').focus();
        }

        const cartTableBody = document.querySelector("#cart-table tbody");
        const addToCartButton = document.getElementById("add-to-cart");
        const finalizeSaleButton = document.getElementById("finalize-sale");
        const printCartButton = document.getElementById("print-cart");
        const cashPaymentInput = document.getElementById("cash-payment");
        const cardPaymentInput = document.getElementById("card-payment");
        const pixPaymentInput = document.getElementById("pix-payment");
        const productIdInput = document.getElementById("product-id");
        const quantityInput = document.getElementById("quantity");
        const productInput = document.getElementById("product");
        const unitPriceInput = document.getElementById("unit-price");

        productIdInput.addEventListener("keydown", function(event) {
            if (event.key === "Enter") { // Verifica se a tecla pressionada foi Enter
                event.preventDefault(); // Evita o comportamento padrão (submissão de formulário ou avanço para o próximo campo)
                
                // Verifica se o campo ID do produto está vazio
                if (productIdInput.value.trim() === "") {
                    productIdInput.focus(); // Foca no campo de ID novamente
                } else {
                    quantityInput.focus(); // Foca no campo de quantidade
                    quantityInput.addEventListener("blur", function() { // Quando o foco sair do campo de quantidade
                        if (quantityInput.value.trim() === "") { // Verifica se o campo de quantidade está vazio
                            quantityInput.focus(); // Foca novamente no campo de quantidade até ser preenchido
                        }
                    });
                }
            }
        });

        

        productIdInput.addEventListener("blur", function () {
            const productId = productIdInput.value;
            if (productId) {
                fetch(`get_product_by_id.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.product) {
                            productInput.value = data.product;
                            document.getElementById("unit-price").value = data.unit_price || ""; // Preenche o preço unitário
                        } else {
                            alert(data.error || "Produto não encontrado.");
                            productInput.value = "";
                            document.getElementById("unit-price").value = ""; // Limpa o preço unitário
                        }
                    })
                    .catch(error => {
                        alert("Erro ao buscar o produto.");
                        console.error(error);
                    });
            }
        });

        // Adicionar um evento para mover o foco para o botão "Adicionar ao Carrinho" após preencher a quantidade
        document.getElementById("quantity").addEventListener("keydown", function(event) {
            if (event.key === "Enter") { // Verifica se a tecla pressionada foi Enter
                event.preventDefault(); // Evita que a tecla Enter faça o comportamento padrão (como enviar formulário)
                document.getElementById("add-to-cart").focus(); // Foca no botão "Adicionar ao Carrinho"
            }
        });


        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        // Atualiza a tabela do carrinho
        function updateCartTable() {
            cartTableBody.innerHTML = "";
            cart.forEach((item, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${item.product}</td>
                    <td>${item.quantity}</td>
                    <td>${item.unitPrice}</td>
                    <td>${item.totalPrice}</td>
                    <td><button onclick="removeFromCart(${index})">Remover</button></td>
                `;
                cartTableBody.appendChild(row);
            });

            const subtotal = calculateSubtotal();
            const subtotalRow = document.createElement("tr");
            subtotalRow.innerHTML = `
                <td colspan="3" style="text-align: right; font-weight: bold;">Subtotal</td>
                <td>${subtotal}</td>
                <td></td>
            `;
            cartTableBody.appendChild(subtotalRow);

            function calculateSubtotal() {
                return cart.reduce((sum, item) => sum + parseFloat(item.totalPrice), 0).toFixed(2);
            }
        }

        // Função de adicionar ao carrinho
         // Função de adicionar ao carrinho
        function addToCart() {
            const product = document.getElementById("product").value;
            const quantity = parseFloat(document.getElementById("quantity").value) || 0;
            const unitPrice = parseFloat(document.getElementById("unit-price").value) || 0;
            const totalPrice = (quantity * unitPrice).toFixed(2);

            if (product && quantity > 0 && unitPrice > 0) {
                cart.push({ product, quantity, unitPrice, totalPrice });
                localStorage.setItem("cart", JSON.stringify(cart));
                updateCartTable();

                // Retornar o foco para o campo de ID
                document.getElementById("product-id").focus();

                // Limpar os campos preenchidos
                document.getElementById("quantity").value = "";
                document.getElementById("unit-price").value = "";
                document.getElementById("product").value = "";
                document.getElementById("product-id").value = "";
            } else {
                alert("Preencha todos os campos corretamente.");
            }
        }

        // Função de pesquisa do produto pelo ID
        productIdInput.addEventListener("blur", function() {
            const productId = productIdInput.value;
            if (productId) {
                fetch(`get_product_by_id.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.product) {
                            productInput.value = data.product;
                        } else {
                            alert("Produto não encontrado.");
                            productInput.value = "";
                        }
                    })
                    .catch(error => {
                        alert("Erro ao buscar o produto.");
                        console.error(error);
                    });
            }
        });

        // Função para remover item do carrinho
        function removeFromCart(index) {
            cart.splice(index, 1);
            localStorage.setItem("cart", JSON.stringify(cart));
            updateCartTable();
        }

        // Função para finalizar venda
        function finalizeSale() {
            const total = cart.reduce((sum, item) => sum + parseFloat(item.totalPrice), 0);
            const cash = parseFloat(cashPaymentInput.value) || 0;
            const card = parseFloat(cardPaymentInput.value) || 0;
            const pix = parseFloat(pixPaymentInput.value) || 0;

            if (Math.abs((cash + card + pix) - total) < 0.01) {
                const formData = new FormData();
                cart.forEach((item, index) => {
                    formData.append(`product[${index}][name]`, item.product);
                    formData.append(`product[${index}][quantity]`, item.quantity);
                    formData.append(`product[${index}][unitPrice]`, item.unitPrice);
                    formData.append(`product[${index}][totalPrice]`, item.totalPrice);
                });

                formData.append("cash-payment", cash);
                formData.append("card-payment", card);
                formData.append("pix-payment", pix);

                // Enviar o ID do usuário da sessão
                const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; ?>; // Garantir que o user_id esteja disponível
                if (userId > 0) {
                    formData.append("user-id", userId);
                } else {
                    alert("Usuário não autenticado.");
                    return;
                }

                fetch("process_form.php", {
                    method: "POST",
                    body: formData,
                })
                fetch("salvar_faturamento.php", {
                        method: "POST",
                        body: formData,
                    })

                .then(response => response.text())
                .then(data => {
                    alert("Venda finalizada com sucesso!");
                    localStorage.removeItem("cart");
                    cart = [];
                    updateCartTable();
                    window.location.href = "formulario_hortifruti.php";
                })
                .catch(error => {
                    alert("Ocorreu um erro ao finalizar a venda. Tente novamente.");
                    console.error(error);
                });
            } else {
                alert("Os valores de pagamento não correspondem ao total do carrinho.");
            }
        }

        // Função para imprimir o carrinho
        function printCart() {
            const cartContent = document.querySelector(".cart").innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <html>
                <head>
                    <title>Impressão do Carrinho</title>
                    <style>
                        table {
                            width: 30%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                    </style>
                </head>
                <body>
                    <div class="cart">
                        <h3>Carrinho</h3>
                        ${cartContent}
                    </div>
                </body>
                </html>
            `;
            window.print();
            window.location.href = "formulario_hortifruti.php";
        }

        addToCartButton.addEventListener("click", addToCart);
        finalizeSaleButton.addEventListener("click", (e) => {
            e.preventDefault();
            finalizeSale();
        });
        printCartButton.addEventListener("click", (e) => {
            e.preventDefault();
            printCart();
        });

        document.addEventListener("DOMContentLoaded", updateCartTable);
    </script>
</body>
</html>
