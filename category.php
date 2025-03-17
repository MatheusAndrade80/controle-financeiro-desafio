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

//Verifica se esta ocorrendo o post e se estiver vai armazenar as informações no banco de dados
if (isset($_POST['submit'])) {
    $category = $_POST['category'];

    $sql1 = mysqli_query($conexao, "INSERT INTO categorias (nome, usuario_id) VALUES ('$category', '$usuario_id')");
}

//edita a categoria selecionada para o nome digitado 
if (isset($_POST['update'])) {
    if (!empty($_POST['category_edit']) && !empty($_POST['category_name'])) {
        $id = $_POST['category_edit'];  
        $nome = $_POST['category_name'];  

      
        $sqlUpdate = "UPDATE categorias SET nome='$nome' WHERE id='$id'";
        $queryUpdate = mysqli_query($conexao, $sqlUpdate);
    }
}

//deleta a categoria selecionada do banco de dados caso ela nao esteja sendo usada
if (isset($_POST['delete'])) {
    if (!empty($_POST['category_delete'])) {

        $id = $_POST['category_delete'];  // Captura a categoria selecionada
        $verificarTransacao = mysqli_query($conexao, "SELECT id FROM transacoes WHERE categoria_id = '$id'");
        $transacoes = mysqli_fetch_assoc($verificarTransacao);

        if ($transacoes > 0) {
            echo "<script>alert('Não é possível excluir a categoria, pois ela está sendo usada.');</script>";
        } else {
            $sqlDelete = "DELETE FROM categorias WHERE id='$id'";
            $queryDelete = mysqli_query($conexao, $sqlDelete);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/category.css">
    <title>Categorias</title>
</head>

<body>
    <div class="category">
        <a href="index.php" class="backIcon">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="category-field">
            <h2 style="text-align: center">Criar Categoria</h2>
            <form action="" method="POST">
                <div class="info">
                    <input class="input text" placeholder="Digite o nome da nova categoria" type="text" name="category" id="category">
                    <input class="submit-button create" type="submit" name="submit" id="submit">
                </div>
            </form>
        </div>

        <div class="category-field">
            <h2 style="text-align: center">Editar Categoria</h2>
            <form action="" method="POST">
                <div class="info">
                    <select name="category_edit" id="category_edit">
                        <option value="">Selecione uma categoria</option>
                        <?php
                        // Pega todas as categorias do usuario logado
                        $categorias = mysqli_query($conexao, "SELECT * from categorias WHERE $usuario_id = usuario_id");
                        while ($c = mysqli_fetch_array($categorias)) {
                            echo '<option value="' . $c['id'] . '">' . $c['nome'] . '</option>';
                        }
                        ?>
                    </select>
                    <input class="input text" type="text" name="category_name" id="category_name" placeholder="Escreva o nome desejado">
                </div>
                <div class="button-field">
                    <input class="submit-button" type="submit" name="update" value="Atualizar">
                </div>
            </form>
        </div>

        <div class="category-field">
            <h2 style="text-align: center">Excluir Categoria</h2>
            <form action="" method="POST">
                <div class="info">
                    <select name="category_delete" id="category_delete">
                        <?php
                        $categorias = mysqli_query($conexao, "SELECT * from categorias WHERE $usuario_id = usuario_id");
                        while ($c = mysqli_fetch_array($categorias)) {
                            echo '<option value="' . $c['id'] . '">' . $c['nome'] . '</option>';
                        }
                        ?>
                    </select>
                    <input class="submit-button delete" type="submit" name="delete" value="Deletar">
            </form>
        </div>
    </div>
    </div>
</body>

</html>