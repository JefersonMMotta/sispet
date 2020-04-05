<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'editar' ||
    !filter_var($_GET['id_pet'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_pet'] < 1

) {
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}
$data['id_pet'] = (int) $_GET['id_pet'];

function buscarPet()
{
    global $data;
    $data['pet'] = [];
    $sql = "SELECT * FROM tb_pets WHERE id_pet = '{$data['id_pet']}'";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
        exit();
    }
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['pet'], $row);
    }
}
buscarPet();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_pet = $_POST['id_pet'];
    $nome = validar($_POST['nome'], 'nome', true);
    $sexo = validar($_POST['sexo'], 'sexo', true);
    $data_nascimento = validar($_POST['data_nascimento'], 'Data de Nascimento', true);
    $cor = validar($_POST['cor'], 'Cor', true);
    $raca = validar($_POST['raca'], 'Raça', true);
    $especie = validar($_POST['especie'], 'Espécie', true);
    $data_nascimento = converterData($data_nascimento);

    if (!$data['validation']['has_error'] && $id_pet == $_GET['id_pet'] ) {
        $sql = "UPDATE tb_pets set
                nome = '{$nome}',
                sexo = '{$sexo}',
                data_nascimento = '{$data_nascimento}',
                cor = '{$cor}',
                raca = '{$raca}',
                especie = '{$especie}'
                WHERE id_pet = '{$id_pet}' ";
                $conn = conectar();
                if (mysqli_query($conn, $sql) === true) {
                    $data['success'] = "PET editado com sucesso";
                    buscarPet();
                    unset($data['value']);
                }else{
                    $data['fail'] = "Ocorreu um erro ao editar o cliente.";
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }  
        mysqli_close($conn);          
    }

}


?>
<h1>Editar Pet</h1>
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
<form action="<?=base_url('editarpet.php?action=editar&id_pet=' . $data['id_pet'])?>" method="post">
<input type="hidden" name="id_pet" value="<?=$_GET['id_pet']?>" >
    <div class="row">
        <div class="col-md-6">
        <div class="form-group">
            <label for="nome">Nome</label>
                <input type="text" class="form-control" name='nome' id="nome"  value="<?=(isset($data['value']['nome'])) ? $data['value']['nome'] : $data['pet'][0]['nome']?>"  >
                <span style="color:red"><?=(isset($data['validation']['nome'])) ? $data['validation']['nome'] : ''?></span>
        </div>
        </div>
        <div class="col-md-3">
        <div class="form-group">
            <label for="sexo">Sexo</label>
                <input list="lista-sexo" type="text" class="form-control" name='sexo' id="sexo" value="<?=(isset($data['value']['Sexo'])) ? $data['value']['Sexo'] :  $data['pet'][0]['sexo'] ?>">
                <datalist id="lista-sexo">
                    <option value="Macho">
                    <option value="Fêmea">
                </datalist>
                <span style="color:red"><?=(isset($data['validation']['Sexo'])) ? $data['validation']['Sexo'] : ''?></span>
        </div>
        </div>
        <div class="col-md-3">
        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento</label>
                <input type="text"   class="form-control" name='data_nascimento' id="data_nascimento" value="<?=(isset($data['value']['Data de Nascimento'])) ?$data['value']['Data de Nascimento'] : converterData($data['pet'][0]['data_nascimento'])?>">
              <span style="color:red"><?=(isset($data['validation']['Data de Nascimento'])) ? $data['validation']['Data de Nascimento'] : ''?></span>
        </div>
        </div>
     </div>

     <div class="row">
     <div class="col-md-4">
     <div class="form-group">
        <label for="raca">Raça</label>
            <input type="text" class="form-control" name='raca' id="raca" value="<?=(isset($data['value']['Raça'])) ? $data['value']['Raça'] :  $data['pet'][0]['raca']?>" >
            <span style="color:red"><?=(isset($data['validation']['Raça'])) ? $data['validation']['Raça'] : ''?></span>

     </div>
     </div>
     <div class="col-md-4">
     <div class="form-group">
        <label for="especie">Espécie</label>
            <input type="text" class="form-control" name='especie' id="especie" value="<?=(isset($data['value']['Espécie'])) ? $data['value']['Espécie'] :  $data['pet'][0]['especie'] ?>" >
            <span style="color:red"><?=(isset($data['validation']['Espécie'])) ? $data['validation']['Espécie'] : ''?></span>

     </div>
     </div>
     <div class="col-md-4">
     <div class="form-group">
        <label for="cor">Cor</label>
            <input type="text" class="form-control" name='cor' id="cor" value="<?=(isset($data['value']['Cor'])) ? $data['value']['Cor'] :  $data['pet'][0]['cor'] ?> ">
            <span style="color:red"><?=(isset($data['validation']['Cor'])) ? $data['validation']['Cor'] : ''?></span>
     </div>
     </div>
     </div>



     <button class="btn btn-secondary" type="submit">Editar</button>

</form>

<?php require_once "includes/header.php";?>
