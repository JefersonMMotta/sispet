<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['cliente_id']) ||
    !filter_var($_GET['cliente_id'],
        FILTER_VALIDATE_INT) ||
    $_GET['cliente_id'] < 1

) {
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
    //header("location:" . base_url('clientes.php'));
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
    $id_cliente = (int)$_POST['id_cliente'];
    $nome = validar($_POST['nome'],'nome',true);
    $data_nasimento = validar($_POST['data_nascimento'],'Data de Nascimento',true);
    $raca = validar($_POST['raca'],'Raça',true);
    $especie = validar($_POST['especie'],'Espécie',true);
    $cor = validar($_POST['cor'],'Cor',true);
    $data_nasimento = converterData($data_nasimento);
    if (!$data['validation']['has_error'] && $data['id_cliente'] == $id_cliente) {
        $sql = "INSERT INTO tb_pets (nome, data_nascimento, raca, especie, cor, cod_cliente,ativo)";
        $sql .= "VALUES('$nome', '$data_nasimento', '$raca', '$especie', '$cor', $id_cliente,  1)";
        $conn = conectar();
        $result = mysqli_query($conn, $sql);
        if($result){
            $data['success'] = "Pet cadastrado com sucesso";
            unset($data['value']);
        }else{
            echo mysqli_error($conn);
            $data['fail'] = "Ocorreu um erro ao cadastrar o PET";
        }      
    
    }

}

?>

<h1>Cadastrar Pet</h1>
<p>Cliente: <?=$data['cliente'][0]['nome'];?></p>

<?php if (isset($data['success'])): ?>
        <div class="alert alert-success">
             <strong>Sucesso!</strong> <?=$data['success']?>.
        </div>
    <?php endif;?>
     <?php if (isset($data['fail'])): ?>
        <div class="alert alert-danger">
            <strong>Falha!</strong> <?=$data['fail']?>.
        </div>
<?php endif;?>
<form action="<?= base_url('cadastrarpet.php?cliente_id='.$data['id_cliente'])?>" method="post">

    <input type="hidden" name="id_cliente" value="<?= $_GET['cliente_id']?>" >
    <div class="row">
        <div class="col-md-8">
        <div class="form-group">
            <label for="nome">Nome</label>
                <input type="text" class="form-control" name='nome' id="nome"  value="<?= (isset($data['value']['nome'])) ? $data['value']['nome']:'' ?>"  >
                <span style="color:red"><?=(isset($data['validation']['nome'])) ? $data['validation']['nome'] : ''?></span>
        </div>
        </div>
        <div class="col-md-4">
        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento</label>
                <input type="text" class="form-control" name='data_nascimento' id="data_nascimento" value="<?= (isset($data['value']['Data de Nascimento'])) ? $data['value']['Data de Nascimento']:'' ?>">
                <span style="color:red"><?=(isset($data['validation']['Data de Nascimento'])) ? $data['validation']['Data de Nascimento'] : ''?></span>
        </div>
        </div>
     </div>

     <div class="row">
     <div class="col-md-4">
     <div class="form-group">
        <label for="raca">Raça</label>
            <input type="text" class="form-control" name='raca' id="raca" value="<?= (isset($data['value']['Raça'])) ? $data['value']['Raça']:'' ?>" >
            <span style="color:red"><?=(isset($data['validation']['Raça'])) ? $data['validation']['Raça'] : ''?></span>

     </div>
     </div>
     <div class="col-md-4">
     <div class="form-group">
        <label for="especie">Espécie</label>
            <input type="text" class="form-control" name='especie' id="especie" value="<?= (isset($data['value']['Espécie'])) ? $data['value']['Espécie']:'' ?>" >
            <span style="color:red"><?=(isset($data['validation']['Espécie'])) ? $data['validation']['Espécie'] : ''?></span>

     </div>
     </div>
     <div class="col-md-4">
     <div class="form-group">
        <label for="cor">Cor</label>
            <input type="text" class="form-control" name='cor' id="cor" value="<?= (isset($data['value']['Cor'])) ? $data['value']['Cor']:'' ?>">
            <span style="color:red"><?=(isset($data['validation']['Cor'])) ? $data['validation']['Cor'] : ''?></span>

            
     </div>
     </div>
     </div>

     

     <button class="btn btn-secondary" type="submit">Cadastrar</button>

</form>

<?php require_once "includes/footer.php";?>