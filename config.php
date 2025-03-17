<!--link necessário para os ícones-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<?php
//conexão com o banco de dados
$dbHost = 'Localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'sistema-de-controle';

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

