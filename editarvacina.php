<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['id_vacina']) ||
    !filter_var($_GET['id_vacina'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_vacina'] < 1

) {
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}
$data['id_vacina'] = (int) trim(addslashes(filter_var($_GET['id_vacina'], FILTER_SANITIZE_STRING)));

if($_SERVER['REQUEST_METHOD']  == "POST"){
    global $data;
    $id_vacina = $_POST['id_vacina'];
    $nome = validar($_POST['nome'], 'Nome', true);
    $dose = validar($_POST['dose'], 'Dose', true);
    $dataAplicacao = converterData(validar($_POST['dataAplicacao'], 'Data', true));
    $proximaAplicacao = converterData(validar($_POST['proximaAplicacao'],'Próxima Data') );

    if (!$data['validation']['has_error'] && $data['id_vacina'] == $id_vacina) {
      $sql = "UPDATE tb_vacinas set 
                        nome = '{$nome}',
                        dose = '{$dose}',
                        data_aplicacao = '{$dataAplicacao}',
                        data_prox_aplicacao = '{$proximaAplicacao}'
                        WHERE id_vacina = '{$id_vacina}'";
        $conn = conectar();
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $data['success'] = "Vacina editada com sucesso";
            unset($data['value']);
        } else {
            echo mysqli_error($conn);
            $data['fail'] = "Ocorreu um erro ao editar a vacina";
        } 
    }
} 

function buscarVacina()
{
    global $data;
    $data['vacina'] = [];
    $sql = "SELECT *, (SELECT nome from tb_pets WHERE cod_pet = id_pet) as pet FROM tb_vacinas WHERE id_vacina ={$data['id_vacina']}";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) != 1){
        echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
        exit();
    }
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['vacina'], $row);
    }

}
buscarVacina();

?>
<h1>Editar vacina</h1>
<p>Pet: <?= $data['vacina'][0]['pet']; ?> </p>
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

<form action="<?= base_url('editarvacina.php?action=editar&id_vacina=').$data['id_vacina']?>" method="post">
    <input type="hidden" name="id_vacina" value="<?= $data['id_vacina']?>">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input list="vacina-list" type="text" class="form-control" name="nome" id="nome" value="<?= (isset($data['value']['Nome'])) ? $data['value']['Nome'] : $data['vacina'][0]['nome']; ?>">
                <datalist id="vacina-list">
                    <option value="V8">
                    <option value="V10">
                    <option value="V12">
                    <option value="Gripe Canina">
                    <option value="Giárdia">
                    <option value="Raiva canina">
                    <option value="Leishmaniose">
                </datalist>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label for="dose">Dose</label>
                <input type="text" class="form-control" name="dose" id="dose" value="<?= (isset($data['value']['Dose'])) ? $data['value']['Dose'] :$data['vacina'][0]['dose']; ?>">
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label for="data">Data</label>
                <input type="text" class="form-control" name="dataAplicacao" id="data" value="<?= (isset($data['value']['Data'])) ? $data['value']['Data'] : converterData($data['vacina'][0]['data_aplicacao']); ?>" >
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="proxima-data">Próxima Data</label>
                <input type="text" class="form-control" name="proximaAplicacao" id="proxima-data" value="<?= (isset($data['value']['Próxima Data'])) ? $data['value']['Próxima Data'] : converterData($data['vacina'][0]['data_prox_aplicacao']); ?>" >
            </div>
        </div>
    </div>
    <button class="btn btn-secondary mb-2" type="submit">Editar</button>
</form>
<?php require_once("includes/footer.php");?>
