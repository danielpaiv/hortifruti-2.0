<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(to right,rgb(20, 40, 220), rgb(17, 21, 71));
        }
        div{
            column-count: 1;
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            padding: 80px;
            border-radius: 15px;
            color: aliceblue;
        }
        input{
            padding: 15px;
            border: none;
            outline: none;
            font-size: 15px;
        }
        .inputsubmit{
            background-color: dodgerblue;
            border-radius: 10px;
            border: none;
            padding: 15px;
            width: 100%;
            color: aliceblue;
            font-size: 17px;
            cursor: pointer;   
        }
        .inputsubmit:hover{
            background-color: deepskyblue;
        }
        .btn{
        color:white ;
        padding: 5px;
        text-decoration: none;
        }
        button{
            border-radius: 5px;
            text-decoration: none;
            border: none;
            padding: 15px;
            background-image: linear-gradient(to right,rgb(130, 20, 220), rgb(44, 31, 220));

        }


    </style>
</head>
<body>

<button><a href="/gestao/index.php"class="btn btn-danger me-5">Gestão</a></button>
    <script>
        window.onload = function() {
        document.querySelector('input[name="nome"]').focus();
    }
    </script>
    <div>
        <h1>Login</h1>
        <h3>usuarios</h3>
        <form action="testlogin.php"method="POST">
        <input type="text" name="nome" placeholder="nome"required>
        <br><br>
        <input type="password" name="senha" placeholder="senha"required>
        <br><br>
        <input class="inputsubmit" type="submit" name="submit" value="Enviar">
        </form>
    </div>

    
</body>
</html>