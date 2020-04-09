<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['id_pet']) ||
    !filter_var($_GET['id_pet'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_pet'] < 1

) {
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}
$data['id_pet'] = (int) trim(addslashes(filter_var($_GET['id_pet'], FILTER_SANITIZE_STRING)));

if($_SERVER['REQUEST_METHOD']  == "POST"){
    global $data;
    $id_pet = $_POST['id_pet'];
    $nome = validar($_POST['nome'], 'Nome', true);
    $dose = validar($_POST['dose'], 'Dose', true);
    $dataAplicacao = converterData(validar($_POST['dataAplicacao'], 'Data', true));
    $proximaAplicacao = converterData(validar($_POST['proximaAplicacao'],'Próxima Data') );
    if (!$data['validation']['has_error'] && $data['id_pet'] == $id_pet) {
      $sql = "INSERT INTO tb_vacinas (nome, dose, data_aplicacao, data_prox_aplicacao, cod_pet, ativo)";
      $sql .= " VALUES ('$nome', '$dose', '$dataAplicacao', '$proximaAplicacao', '$id_pet', 1)";
      echo $sql;
      $conn = conectar();
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $data['success'] = "Vacina cadastrada com sucesso";
            unset($data['value']);
        } else {
            echo mysqli_error($conn);
            $data['fail'] = "Ocorreu um erro ao cadastrar a vacina";
        } 
  
    }
} 
function buscarPet()
{
    global $data;
    $data['pet'] = [];
    $sql = "SELECT *,(SELECT nome FROM clientes WHERE cod_cliente = id_cliente) as cliente FROM tb_pets WHERE id_pet = '{$data['id_pet']}'";
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

?>
<a href="<?= base_url('vacinaspet.php?id_pet=').$data['id_pet']?>" class="btn btn-secondary float-right md-1" >Voltar</a>
<h1>Aplicar vacina</h1>
<p>Pet: <?= $data['pet'][0]['nome']?></p>
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

<form action="<?= base_url('cadastrarvacina.php?id_pet=').$data['id_pet']?>" method="post">
    <input type="hidden" name="id_pet" value="<?= $data['id_pet']?>">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input list="vacina-list" type="text" class="form-control" name="nome" id="nome" value="<?= (isset($data['value']['Nome'])) ? $data['value']['Nome'] :''; ?>">
                <datalist id="vacina-list">
                    <option value="V8">
                    <option value="V10">
                    <option value="V12">
                    <option value="Gripe Canina">
                    <option value="Giárdia">
                    <option value="Raiva canina">
                    <option value="Leishmaniose">
                </datalist>
                <span style="color:red"><?=(isset($data['validation']['Nome'])) ? $data['validation']['Nome'] : ''?></span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label for="dose">Dose</label>
                <input type="text" class="form-control" name="dose" id="dose" value="<?= (isset($data['value']['Dose'])) ? $data['value']['Dose'] :''; ?>">
                <span style="color:red"><?=(isset($data['validation']['Dose'])) ? $data['validation']['Dose'] : ''?></span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label for="data">Data</label>
                <input type="text" class="form-control" name="dataAplicacao" id="data" value="<?= (isset($data['value']['Data'])) ? $data['value']['Data'] :''; ?>" >
                <span style="color:red"><?=(isset($data['validation']['Data'])) ? $data['validation']['Data'] : ''?></span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="proxima-data">Próxima Data</label>
                <input type="text" class="form-control" name="proximaAplicacao" id="proxima-data" value="<?= (isset($data['value']['Próxima Data'])) ? $data['value']['Próxima Data'] :''; ?>" >
                <span style="color:red"><?=(isset($data['validation']['Próxima Data'])) ? $data['validation']['Próxima Data'] : ''?></span>
            </div>
        </div>
    </div>
    <button class="btn btn-secondary mb-2" type="submit">Cadastrar</button>
</form>
<table class="table table-bordered table-striped table-sm">
    <thead>
        <th>Vacina</th>
        <th>Idade</th>
        <th>Doses</th>
        <th>Reforço</th>
    </thead>
    <tbody>
        <tr>
            <td>V8</td>
            <td>6 a 8 semanas</td>
            <td>4</td>
            <td>com intervalo de 3 a 4 semanas	Anual (uma dose)</td>
        </tr>
        <tr>
            <td>V10</td>
            <td>6 a 8 semanas</td>
            <td>4</td>
            <td>com intervalo de 3 a 4 semanas	Anual (uma dose)</td>
        </tr>
        <tr>
            <td>V12</td>
            <td>6 a 8 semanas</td>
            <td>4</td>
            <td>com intervalo de 3 a 4 semanas	Anual (uma dose)</td>
        </tr>

        <tr>
            <td>Gripe canina</td>
            <td>8 semanas</td>
            <td>2</td>
            <td>com intervalo de a 4 semanas	Anual (uma dose)</td>
        </tr>

        <tr>
            <td>Giárdia</td>
            <td>12 semanas</td>
            <td>2</td>
            <td>com intervalo de 3 a 4 semanas	Anual (uma dose)</td>
        </tr>
        <tr>
            <td>Raiva canina</td>
            <td>16 semanas</td>
            <td>2</td>
            <td>Dose única aos 4 meses	Anual (uma dose)</td>
        </tr>

        <tr>
            <td>Leishmaniose</td>
            <td>16 semanas</td>
            <td>3</td>
            <td>com intervalo de 3 semanas	Anual (uma dose)</td>
        </tr>
    </tbody>

</table>
<?php require_once "includes/footer.php";?>


