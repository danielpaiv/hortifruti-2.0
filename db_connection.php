<?php
    function OpenCon()
        {
            $dbhost = "localhost"; // ou o endereço do seu servidor MySQL
            $dbuser = "root";
            $dbpass = "";
            $dbname = "hortfruti";

            // Criar conexão
            $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

            // Verificar conexão
            if ($conn->connect_error) {
                die("Conexão falhou: " . $conn->connect_error);
            }
            return $conn;
        }

        function CloseCon($conn)
        {
            $conn->close();
    }
?>
