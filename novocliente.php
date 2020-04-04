<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $sql = "";

    $nome = validar($_POST['nome'], 'nome', true);
    $telefone = validar($_POST['telefone'], 'telefone', true);
    $email = validar($_POST['email'], 'email', true, array('clientes' => 'email'));
    if (!$data['validation']['has_error']) {
        $conn = conectar();
        $sql = "INSERT INTO clientes(nome, telefone, email) VALUES(
           '{$data['value']['nome']}',
           '{$data['value']['telefone']}',
           '{$data['value']['email']}'
        )";
        if (mysqli_query($conn, $sql) === true) {
            $data['success'] = "Cliente cadastrado com sucesso";
            unset($data['value']);
        } else {
            $data['fail'] = "Ocorreu um erro ao cadastrar o cliente.";
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        mysqli_close($conn);
    }
}
?>
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

<h1>Novo Cliente</h1>
<form action="<?=base_url("novocliente.php")?>" method="post">

    <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" name="nome" value="<?=(isset($data['value']['nome'])) ? $data['value']['nome'] : ''?>" >
        <span style="color:red"><?=(isset($data['validation']['nome'])) ? $data['validation']['nome'] : ''?></span>
    </div>
    <div class="form-group">
        <label for="telefone">Telefone</label>
         <input type="text" class="form-control" name="telefone" value="<?=(isset($data['value']['telefone'])) ? $data['value']['telefone'] : ''?>" >
         <span style="color:red"><?=(isset($data['validation']['telefone'])) ? $data['validation']['telefone'] : ''?></span>
    </div>
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="text" class="form-control" name="email" value="<?=(isset($data['value']['email'])) ? $data['value']['email'] : ''?>" >
        <span style="color:red"><?=(isset($data['validation']['email'])) ? $data['validation']['email'] : ''?></span>
    </div>
    <div class="form-group">
        <button class="btn btn-secondary" type="submit">Cadatrar</button>

    </div>

</form>


<?php require_once "includes/footer.php";?>