<?php
//verifica se o usuario ja esta logado
session_start();
if ((isset($_SESSION['email']) == true) && (isset($_SESSION['senha']) == true)) {
    header('Location: index.php');
}

include_once('config.php');

//verifica se o email e senha estao no banco de dados
if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha']))
{
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $usuarios = mysqli_query($conexao, "SELECT senha from usuarios WHERE email = '$email'");
    $s = mysqli_fetch_array($usuarios);
    $securePassword = $s['senha'];
    if (mysqli_num_rows($usuarios) > 0 && password_verify($senha, $securePassword)) {
        $usuario_sql = mysqli_query($conexao, "SELECT id from usuarios WHERE email = '$email'");
        $usuario = mysqli_fetch_assoc($usuario_sql);
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['email'] = $email;
        $_SESSION['senha'] = $senha;
        header('Location: index.php');
    }
    else {
        unset( $_SESSION['email']);
        unset( $_SESSION['senha']);
        unset( $_SESSION['id']);
        header('Location: login.php');
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/login.css">
    <title>Login</title>
</head>
<body>
    <div class="login">       
        <h2>Faça o Login</h2>        
        <form action="" method="POST" class="form">            
        <label for="email">Email:</label>
        <input class="inputText" type="text" placeholder="Digite seu email" name="email" id="email" required>
        <label for="password">Senha:</label>
        <input class="inputText" type="password" placeholder="Digite sua senha" name="senha" id="senha" required>  
        <input class="submitButton" type="submit" name="submit" value="Logar">
        </form>
        <p>Não tem uma conta? 
        <a href="register.php">Registre-se!</a> 
        </p>
    </div>
</body>
</html>