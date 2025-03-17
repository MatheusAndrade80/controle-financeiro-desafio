<?php
//verifica se o usuario esta logado
session_start();
if ((!isset($_SESSION['email']) == true) && (!isset($_SESSION['senha']) == true)) {
    unset($_SESSION['id']);
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.php');
}

include_once('config.php');

//deslogar
if (isset($_GET['logout'])) {
    unset($_SESSION['id']);
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.php');
}

// Capturar filtros do formulário
$filtro_data = isset($_POST['filtro_data']) ? $_POST['filtro_data'] : (isset($_GET['filtro_data']) ? $_GET['filtro_data'] : date('Y-m'));
$filtro_tipo = isset($_POST['entrada']) ? $_POST['entrada'] : '';
$filtro_categoria = isset($_POST['category']) ? $_POST['category'] : '';

$usuario_id = $_SESSION['id'];

//verifica se o input de pesquisa foi utilizado ou não
if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    $sql =
        "SELECT t.*, c.nome AS nome_categoria 
        FROM transacoes t 
        JOIN categorias c ON t.categoria_id = c.id 
        WHERE t.usuario_id = '$usuario_id' 
        AND (t.descricao LIKE '%$data%' or t.valor LIKE '%$data%')";
} else {
    $sql =
        "SELECT t.*, c.nome AS nome_categoria 
        FROM transacoes t 
        JOIN categorias c ON t.categoria_id = c.id 
        WHERE t.usuario_id = '$usuario_id'";
}

//filtra de acordo com as opções dos selects
if (!empty($filtro_data)) {
    $sql .= " AND DATE_FORMAT(t.data, '%Y-%m') = '$filtro_data'";
}
if (!empty($filtro_tipo) && $filtro_tipo !== 'Todos') {
    $sql .= " AND t.tipo = '$filtro_tipo'";
}
if (!empty($filtro_categoria) && $filtro_categoria !== 'Todos') {
    $sql .= " AND t.categoria_id = '$filtro_categoria'";
}

$sql .= " ORDER BY t.data ASC";

$sqlShow = mysqli_query($conexao, $sql);

//deleta a transação correspondente
if (isset($_POST['delete_button']) && isset($_POST['transation_delete']) && !empty($_POST['transation_delete'])) {

    $id = $_POST['transation_delete'];  

    $sqlDelete = "DELETE FROM transacoes WHERE id='$id'";
    $queryDelete = mysqli_query($conexao, $sqlDelete);
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/index.css">
    <title>Página Principal</title>
</head>

<body>
    <div class="index">
        <a href="?logout=true" class="logoutIcon" onclick="return confirm('Tem certeza que deseja deslogar?')">
        <i class="fa-solid fa-right-from-bracket"></i>
        </a>

        <h1 style="text-align: center">Lista de Despesas / Receitas</h1>
        <!-- Formulário de Filtro -->
        <form method="POST">
            <div class="inputs">
                <input class="input" type="month" name="filtro_data" id="filtro_data" value="<?= $filtro_data ?>" onchange="this.form.submit()">

                <select name="entrada" id="entrada" onchange="this.form.submit()">
                    <option value="Todos" <?= ($filtro_tipo == 'Todos') ? 'selected' : '' ?>>Todos</option>
                    <option value="Receita" <?= ($filtro_tipo == 'Receita') ? 'selected' : '' ?>>Receita</option>
                    <option value="Despesa" <?= ($filtro_tipo == 'Despesa') ? 'selected' : '' ?>>Despesa</option>
                </select>

                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="Todos">Todas as Categorias</option>
                    <?php
                    $categorias = mysqli_query($conexao, "SELECT * from categorias WHERE $usuario_id = usuario_id");
                    while ($c = mysqli_fetch_array($categorias)) {
                        $selected = ($c['id'] == $filtro_categoria) ? "selected" : "";
                        echo '<option value="' . $c['id'] . '" ' . $selected . '>' . $c['nome'] . '</option>';
                    }
                    ?>
                </select>
                
                <input class="input text" type="search" name="search" id="pesquisar" placeholder="Buscar por valor ou descrição">
                <button class="submitButton" type="button" onclick="searchData()">Pesquisar</button>
            </div>

            <a href='create.php' class="button">Nova Entrada</a>
            <a style="margin-left: 20px;" href='category.php' class="button">Categorias</a>

            <!--dashboard-->
            <div class="info">
                <?php
                
                $filtroDespesas = "SELECT valor FROM transacoes WHERE tipo='Despesa' AND usuario_id = '$usuario_id'";

                if (!empty($filtro_categoria) && $filtro_categoria !== 'Todos') {
                    $filtroDespesas .= " AND categoria_id = '$filtro_categoria'";
                }

                $despesas = mysqli_query($conexao, $filtroDespesas);
                $receitas = mysqli_query($conexao, "SELECT valor from transacoes WHERE tipo='Receita' and usuario_id = '$usuario_id'");

                echo'<div class="field receitas">';
                echo '<p>Receitas:</p>';
                $totalReceitas = 0;
                while ($r = mysqli_fetch_array($receitas)) {
                    $totalReceitas += $r['valor'];
                }
                echo '<p>R$ ' . $totalReceitas . '</p>';
                echo'</div>';

                echo'<div class="field despesas">';
                echo '<p>Despesas:</p>';
                $totalDespesas = 0;
                while ($d = mysqli_fetch_array($despesas)) {
                    $totalDespesas += $d['valor'];
                }
                echo '<p>R$ ' . $totalDespesas . '</p>';
                echo'</div>';

                echo'<div class="field total">';
                echo '<p>Total:</p>';
                $total = $totalReceitas - $totalDespesas;
                echo '<p>R$ ' . $total . '</p>';
                echo'</div>';
                ?>
            </div>

            <div>
                <table>
                    <tr>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Data</th>
                        <th>Ação</th>
                    </tr>
                    <?php
                    //pega todas as categorias da tabela               
                    while ($c = mysqli_fetch_array($sqlShow)) {
                        echo "<tr>";
                        echo "<td>" . $c['tipo'] . "</td>";
                        echo "<td>R$ " . $c['valor'] . "</td>";
                        echo "<td>" . $c['descricao'] . "</td>";
                        echo "<td>" . $c['nome_categoria'] . "</td>";
                        echo "<td>" . date("d/m/Y", strtotime($c['data'])) . "</td>";
                        echo "<td><a class='button edit' href='edit.php?id=$c[id]'>Editar</a>
                        <form method='POST' action='' style='display:inline;'>
                        <input type='hidden' name='transation_delete' value='" . $c['id'] . "'>
                        <input class='button delete' type='submit' name='delete_button' value='Deletar' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>
                        </form>
                </td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
    </div>
</body>
<script>
    //script para passar o input de pesquisa 
    function searchData() {
        var search = document.getElementById('pesquisar').value;
        window.location = 'index.php?search=' + search;
    }
</script>

</html>