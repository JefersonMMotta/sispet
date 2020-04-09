<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'editar' ||
    !filter_var($_GET['id_fornecedor'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_fornecedor'] < 1

) {
   // echo 'Parametros invalidos!';
   // sleep(30);
    //header("location:" . base_url('fornecedores.php'));
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}

buscarFornecedor();

function buscarFornecedor()
{
    global $data;
    $data['fornecedor'] = [];
    $data['id_fornecedor'] = (int) $_GET['id_fornecedor'];
    $conn = conectar();
    $sql = "SELECT * FROM tb_fornecedores where id_fornecedor = '{$data['id_fornecedor']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('fornecedores.php'));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['fornecedor'], $row);
    }

}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $sql = "";

    $nome = validar($_POST['nome'], 'nome', true);
    $telefone = validar($_POST['telefone'], 'telefone', true);
    $email = validar($_POST['email'], 'email');
    if($email != $data['fornecedor'][0]['email']){
        if(!isUnique('tb_fornecedores', 'email', $email)){
            $data['validation']['has_error'] = true;
            $data['validation']['email'] = "Esse email ja esta sendo usado por outro fornecedor";
        }
    }
    
      if (!$data['validation']['has_error']) {
        $conn = conectar();
        $sql = "UPDATE tb_fornecedores set
                        nome =  '{$data['value']['nome']}',
                        telefone =   '{$data['value']['telefone']}',
                        email =  '{$data['value']['email']}'
                         WHERE id_fornecedor =  '{$data['id_fornecedor']}' ";
        if (mysqli_query($conn, $sql) === true) {
            $data['success'] = "Fornecedor editado com sucesso";
            buscarFornecedor();
            unset($data['value']);
        } else {
            $data['fail'] = "Ocorreu um erro ao editar o Fornecedor.";
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        mysqli_close($conn);
    }
}



?>
<h1>Editar fornecedor</h1>
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

<form action="<?=base_url("editarfornecedor.php?action=editar&id_fornecedor=" . $data['fornecedor'][0]['id_fornecedor'])?>" method="post">

    <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" name="nome" value="<?=(isset($data['value']['nome'])) ? $data['value']['nome'] : $data['fornecedor'][0]['nome']?>" >
        <span style="color:red"><?=(isset($data['validation']['nome'])) ? $data['validation']['nome'] : ''?></span>
    </div>
    <div class="form-group">
        <label for="telefone">Telefone</label>
         <input type="text" class="form-control" name="telefone" value="<?=(isset($data['value']['telefone'])) ? $data['value']['telefone'] : $data['fornecedor'][0]['telefone']?>" >
         <span style="color:red"><?=(isset($data['validation']['telefone'])) ? $data['validation']['telefone'] : ''?></span>
    </div>
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="text" class="form-control" name="email" value="<?=(isset($data['value']['email'])) ? $data['value']['email'] : $data['fornecedor'][0]['email']?>" >
        <span style="color:red"><?=(isset($data['validation']['email'])) ? $data['validation']['email'] : ''?></span>
    </div>
    <div class="form-group">
        <button class="btn btn-secondary" type="submit">Editar</button>

    </div>

</form>


<?php require_once "includes/footer.php";?>