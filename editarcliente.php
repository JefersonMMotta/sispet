<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'editar' ||
    !filter_var($_GET['cliente_id'],
        FILTER_VALIDATE_INT) ||
    $_GET['cliente_id'] < 1

) {
    header("location:" . base_url('clientes.php'));
}

buscarCliente();

function buscarCliente()
{
    global $data;
    $data['cliente'] = [];
    $data['cliente_id'] = (int) $_GET['cliente_id'];
    $conn = conectar();
    $sql = "SELECT * FROM clientes where id_cliente = '{$data['cliente_id']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('clientes.php'));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['cliente'], $row);
    }

}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $sql = "";

    $nome = validar($_POST['nome'], 'nome', true);
    $telefone = validar($_POST['telefone'], 'telefone', true);
    $email = validar($_POST['email'], 'email');
    if($email != $data['cliente'][0]['email']){
        if(!isUnique('clientes', 'email', $email)){
            $data['validation']['has_error'] = true;
            $data['validation']['email'] = "Esse email ja esta sendo usado por outro cliente";
        }
    }
    
      if (!$data['validation']['has_error']) {
        $conn = conectar();
        $sql = "UPDATE clientes set
                        nome =  '{$data['value']['nome']}',
                        telefone =   '{$data['value']['telefone']}',
                        email =  '{$data['value']['email']}'
                         WHERE id_cliente =  '{$data['cliente_id']}' ";
        if (mysqli_query($conn, $sql) === true) {
            $data['success'] = "Cliente editado com sucesso";
            buscarCliente();
            unset($data['value']);
        } else {
            $data['fail'] = "Ocorreu um erro ao editar o cliente.";
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        mysqli_close($conn);
    }
}



?>
<h1>Editar cliente</h1>
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

<form action="<?=base_url("editarcliente.php?action=editar&cliente_id=" . $data['cliente'][0]['id_cliente'])?>" method="post">

    <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" name="nome" value="<?=(isset($data['value']['nome'])) ? $data['value']['nome'] : $data['cliente'][0]['nome']?>" >
        <span style="color:red"><?=(isset($data['validation']['nome'])) ? $data['validation']['nome'] : ''?></span>
    </div>
    <div class="form-group">
        <label for="telefone">Telefone</label>
         <input type="text" class="form-control" name="telefone" value="<?=(isset($data['value']['telefone'])) ? $data['value']['telefone'] : $data['cliente'][0]['telefone']?>" >
         <span style="color:red"><?=(isset($data['validation']['telefone'])) ? $data['validation']['telefone'] : ''?></span>
    </div>
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="text" class="form-control" name="email" value="<?=(isset($data['value']['email'])) ? $data['value']['email'] : $data['cliente'][0]['email']?>" >
        <span style="color:red"><?=(isset($data['validation']['email'])) ? $data['validation']['email'] : ''?></span>
    </div>
    <div class="form-group">
        <button class="btn btn-secondary" type="submit">Editar</button>

    </div>

</form>


<?php require_once "includes/footer.php";?>