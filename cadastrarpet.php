<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['cliente_id']) ||
    !filter_var($_GET['cliente_id'],
        FILTER_VALIDATE_INT) ||
    $_GET['cliente_id'] < 1

) {
    header("location:" . base_url('clientes.php'));
}
$data['id_cliente'] = (int) $_GET['cliente_id'];

function isCLiente()
{
    global $data;
    $data['cliente'] = [];

    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE id_cliente = '{$data['id_cliente']}' AND ativo = 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('clientes.php'));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['cliente'], $row);
    }
    mysqli_close($conn);
}
isCLiente();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $nome = validar($_POST['nome'],'nome',true);
    $data_nasimento = validar($_POST['data_nascimento'],'Data de Nascimento',true);
    $raca = validar($_POST['raca'],'Raça',true);
    $especie = validar($_POST['especie'],'Espécie',true);
    $cor = validar($_POST['cor'],'Cor',true);

}

?>

<h1>Cadastrar Pet</h1>
<p>Cliente: <?=$data['cliente'][0]['nome'];?></p>
<form action="<?= base_url('cadastrarpet.php?cliente_id='.$data['id_cliente'])?>" method="post">
    <div class="row">
        <div class="col-md-8">
        <div class="form-group">
            <label for="nome">Nome</label>
                <input type="text" class="form-control" name='nome' id="nome">
                <span style="color:red"><?=(isset($data['validation']['nome'])) ? $data['validation']['nome'] : ''?></span>
        </div>
        </div>
        <div class="col-md-4">
        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento</label>
                <input type="text" class="form-control" name='data_nascimento' id="data_nascimento">
                <span style="color:red"><?=(isset($data['validation']['Data de Nascimento'])) ? $data['validation']['Data de Nascimento'] : ''?></span>
        </div>
        </div>
     </div>

     <div class="row">
     <div class="col-md-4">
     <div class="form-group">
        <label for="raca">Raça</label>
            <input type="text" class="form-control" name='raca' id="raca">
            <span style="color:red"><?=(isset($data['validation']['Raça'])) ? $data['validation']['Raça'] : ''?></span>

     </div>
     </div>
     <div class="col-md-4">
     <div class="form-group">
        <label for="especie">Espécie</label>
            <input type="text" class="form-control" name='especie' id="especie">
            <span style="color:red"><?=(isset($data['validation']['Espécie'])) ? $data['validation']['Espécie'] : ''?></span>

     </div>
     </div>
     <div class="col-md-4">
     <div class="form-group">
        <label for="cor">Cor</label>
            <input type="text" class="form-control" name='cor' id="cor">
            <span style="color:red"><?=(isset($data['validation']['Cor'])) ? $data['validation']['Cor'] : ''?></span>

            
     </div>
     </div>
     </div>

     

     <button class="btn btn-secondary" type="submit">Cadastrar</button>

</form>
<?php print_r($data); ?>

<?php require_once "includes/footer.php";?>