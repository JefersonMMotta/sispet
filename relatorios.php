<?php require_once "includes/functions.php";
require_once "includes/header.php";

function listarVendas()
{
    $sql = "SELECT * FROM tb_orders WHERE MONTH(data) = MONTH(CURDATE())";
}

?>
<h1>Relatórios</h1>
<form class="" action="<?=base_url('relatorios.php')?>" method="post">

    <div class="form-group">
        <label for="tabela">Pesquisar em :</label>
        <select class="form-control" id="tabela">
            <option>Selecione</option>
            <option>Vendas</option>
            <option>Produtos</option>
            <option>Clientes</option>
            <option>Pets</option>
        </select>
    </div>

    <div class="form-group">
        <label for="campos">Pesquisar em :</label>
        <select class="form-control" id="campos">
            
        </select>
    </div>

    <div class="form-group">
            <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" value=""> Não
            </label>
        </div>
        <label for="comparacao">Comparação:</label>
        <select class="form-control" id="comparacao">
            <option >Contém</option>
            <option>É igual a</option>
            <option>Começa com</option>
            <option>Esta em</option>
            <option>É em ou depois de </option>
            <option>É em ou antes de </option>
            <option>Está entre </option>            
        </select>
    </div>

    <div class="form-group">
        <label for="pesquisa">Pesquisa</label>
        <input type="text" class="form-control" name="pesquisa" id="pesquisa">
    </div>

   <button type="submit" class="btn btn-primary" id="myBtn">Pesquisar <i class="fa fa-search" aria-hidden="true"></i></button>
</form>

<script>
    let Vendas = {
        campos:['data', 'forma_pagamento', 'total_bruto', 'total_liquido', 'valor_desconto', 'qtd_itens','data_cadastro']
    }
    $("#tabela").on("change", function(){
        let tabela = this.value;
        if(tabela =="Vendas"){
                       
        }
    });
    </script>
<?php require_once "includes/footer.php"?>