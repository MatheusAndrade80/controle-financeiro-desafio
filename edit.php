<?php
//verifica se o usuario esta logado
session_start();
if ((!isset($_SESSION['email']) == true) && (!isset($_SESSION['senha']) == true)) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    unset( $_SESSION['id']);
    header('Location: login.php');
}

include_once('config.php');
$usuario_id = $_SESSION['id'];

//verifica se o id da transação foi passado, afim de acha-la no banco de dados e deixar os input preenchidos com as informaçoes atuais
if(!empty($_GET['id']))
{   
    $id = $_GET['id'];
    $sqlselect = mysqli_query($conexao,"SELECT * FROM transacoes WHERE id=$id");

    if($sqlselect->num_rows > 0)
    {
        while ($user_data = mysqli_fetch_assoc($sqlselect)) {
            $type = $user_data['tipo'];
            $category = $user_data['categoria_id'];
            $value = $user_data['valor'];
            $date = $user_data['data'];
            $description = $user_data['descricao'];
        }
    } else {
        header('Location: index.php');
    } 
 }

 //edita as informações da transação conforme os dados passado pelo usuario
 if (isset($_POST['update'])) 
 {  
    $id_update = $_POST['id'];
    $type_update = $_POST['type'];
    $category_update = $_POST['category'];
    $value_update = $_POST['value'];
    $date_update = $_POST['date'];
    $description_update = $_POST['description'];

    $sqlupdate = mysqli_query($conexao, "UPDATE transacoes SET tipo ='$type_update', descricao ='$description_update', valor ='$value_update', data = '$date_update', categoria_id = '$category_update' WHERE id='$id_update'");
    header('Location: index.php');
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/edit.css">
    <title>Editar</title>
</head>
<body>
    <div class="edit"> 
    <a href="index.php" class="backIcon">
    <i class="fa-solid fa-arrow-left"></i>
    </a>  
        <h2 style="text-align: center">Editar informações</h2>  
        <form action="edit.php" method="POST">
        <div class="info">
            <p>Tipo:</p> 
            <input class="input" type="text" name="type" id="type" value="<?php echo $type ?>">

            <p>Categoria:</p> 

            <select name="category" id="category">
                <?php
                //pega todas as categorias da tabela
                $categorias = mysqli_query($conexao, "SELECT * from categorias WHERE $usuario_id = usuario_id");
                while ($c = mysqli_fetch_array($categorias)) {
                    $selected = ($c['id'] == $category) ? "selected" : "";
                    echo '<option value="' . $c['id'] . '" ' . $selected . '>' . $c['nome'] . '</option>';
                }
                ?>         
            </select>            
        </div>

        <div class="info"> 
            <p>Valor:</p> 
            <input class="input" type="text" name="value" id="value" value="<?php echo $value ?>">
    
            <p>Data:</p> 
            <input class="input" type="date" name="date" id="date" value="<?php echo $date ?>">
        </div>

        <div>
        <p>Descrição:</p> 
        <textarea class="input descricao" name="description" id="description"><?php echo $description ?></textarea>
        </div>

        <input type="hidden" name="id" value="<?php echo $id ?>">
        <div class="button-field">
        <input class="submit-button" type="submit" name="update" id="update"> 
        </div>  
        </form>        
    </div>    
</body>
</html>