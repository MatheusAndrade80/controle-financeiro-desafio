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

//verifica se esta ocorrendo o post e se estiver vai armazenar as informações no banco de dados
if (isset($_POST['submit'])) {
    $type = $_POST['type'];
    $category = $_POST['category'];
    $value = $_POST['value'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $usuario_id = $_SESSION['id'];

    $sql1 = mysqli_query($conexao, "INSERT INTO transacoes (tipo, descricao, valor, data, categoria_id, usuario_id) VALUES ('$type', '$description', '$value', '$date', '$category', '$usuario_id')");
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/create.css">
    <title>Criar</title>
</head>

<body>
    <div class="create">
        <a href="index.php" class="backIcon">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <h2 style="text-align: center">Nova Entrada</h2>
        <form action="" method="POST">
            <div class="info">
                <p>Tipo:</p>
                <select name="type" id="type">
                    <option value="Receita">Receita</option>
                    <option value="Despesa">Despesa</option>
                </select>

                <p>Categoria: </p>
                <?php
                // Consulta para pegar as categorias do usuario logado
                $categorias = mysqli_query($conexao, "SELECT * from categorias WHERE $usuario_id = usuario_id");
                echo '<select name="category" id="category">';
                // Verifica se há categorias
                if (mysqli_num_rows($categorias) > 0) {
                    // Loop para pegar todas as categorias e criar as opções
                    while ($c = mysqli_fetch_array($categorias)) {
                        echo '<option value="' . $c['id'] . '">' . $c['nome'] . '</option>';
                    }
                    $disableSubmit = '';
                } else {
                    echo '<option> Nenhuma categoria encontrada </option>';
                    $disableSubmit = 'disabled';
                }
                echo '</select>';
                ?>
                <a href="category.php" class="addIcon">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>

            <div class="info">
                <p>Valor:</p>
                <input class="input" type="number" name="value" id="value" placeholder="Digite o valor" required min="1">

                <p>Data:</p>
                <input class="input" type="date" name="date" id="date" required>
            </div>

            <div>
                <p>Descrição:</p>
                <textarea class="input descricao" name="description" id="description" placeholder="Escreva uma descrição da transação" required></textarea>
            </div>

            <div class="button-field">
                <input class="submit-button" type="submit" name="submit" id="submit" <?php echo $disableSubmit; ?>>
            </div>
        </form>
    </div>
</body>

</html>