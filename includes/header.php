<!DOCTYPE html>
<html lang="en">
<head>
  <title>Petshop</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" 
  integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <a class="navbar-brand" href="<?=base_url();?>">Petshop</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?=base_url('clientes.php')?>">Clientes</a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?=base_url('pets.php')?>">Pets</a>
      </li>
      <li class="nav-item">
       <a class="nav-link" href="<?=base_url('produtos.php')?>">Produtos</a>
       <li class="nav-item">
          <a class="nav-link" href="<?=base_url('fornecedores.php')?>">Fornecedores</a>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url('pedidos.php')?>">Pedidos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?=base_url('relatorios.php')?>">Relatórios</a>
        </li>
      </li>
      </li>
    </ul>
  </div>
</nav>
<br>

<div class="container">
    

