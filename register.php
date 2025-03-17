<?php
//verifica se o usuario ja esta logado
session_start();
if ((isset($_SESSION['email']) == true) && (isset($_SESSION['senha']) == true)) {
    header('Location: index.php');
}

include_once('config.php');

//registra os dados inseridos no banco de dados
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $name = $_POST['nome'];
    $password = $_POST['senha'];
    //hashing da senha
    $securePassword = password_hash($password, PASSWORD_DEFAULT);

    $sqlSelect = mysqli_query($conexao, "SELECT * FROM usuarios WHERE email = '$email'");
    
    //verifica se o email ja nao existe
    if (mysqli_num_rows($sqlSelect) == 0) {
            $sqlRegister = mysqli_query($conexao, "INSERT INTO usuarios (email, nome, senha) VALUES ('$email', '$name', '$securePassword')");   
            header('Location: login.php');
    } else {
            echo "Esse email ja esta registrado";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/register.css">
    <title>Register</title>
</head>
<body>
    <div class="register">        
        <form action="" method="POST" class="form">
        <h2>Insira as informações para se Registrar</h2>
        <label for="email">Email:</label>
        <input class="inputText"  type="email" placeholder="Digite um email" name="email" id="email" required>
        <label for="nome">Nome:</label>
        <input class="inputText" type="text" placeholder="Digite um nome" name="nome" id="nome" required>           
        <label for="senha">Senha:</label>
        <input class="inputText" type="password" placeholder="Digite uma senha (min: 8 characters)" name="senha" minlength="8" id="senha" required>   
        <input class="submitButton" type="submit" name="submit" value="Registrar">      
        </form>
        <p>Já tem uma conta? 
        <a href="login.php">Ir para o Login!</a> 
        </p>
    </div>
</body>
</html>