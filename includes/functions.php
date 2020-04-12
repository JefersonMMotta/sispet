<?php
session_start();
define("BASE_URI", '/');
define("BASE_URL", "http://localhost/petshop/");
define("SERVERNAME", 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DB', 'petshop');
$data = [];
$data['validation']['has_error'] = false;
$data['success'] = null;
$data['fail'] = null;

function base_url($path = null)
{
    if ($path) {
        return BASE_URL . $path;
    } else {
        return BASE_URL;
    }
}

function validar($str, $label, $required = false, $unique = [], $max_len = 45,  $min_len = 0, $exacly_len = null)
{
    global $data;  
    $data['value'][$label] = trim(filter_var($str, FILTER_SANITIZE_STRING));
     $lenth = strlen($str);
   
    if ($required) {
        if (empty($str)) {
            $data['validation'][$label] = "O campo {$label} é obrigatório";
            $data['validation']['has_error'] = true;
        }
    }

    if ($exacly_len) {
        if ($lenth != $exacly_len) {
            $data['validation'][$label] = "O campo {$label} deve conter {$exacly_len} carater(es)";
            $data['validation']['has_error'] = true;
        }
    }
    if ($lenth > $max_len) {
        $data['validation'][$label] = "O campo {$label} deve conter no máximo {$max_len} carater(es)";
        $data['validation']['has_error'] = true;
    }

    if ($min_len > 0) {
        if ($lenth < $min_len) {
            $data['validation'][$label] = "O campo {$label} deve conter no minimo {$max_len} carater(es)";
            $data['validation']['has_error'] = true;
        }
    }   
    if(!empty($unique))
    {
        $tabela = "";
        $campo = "";
        foreach ($unique as $key => $value) {
            $tabela = $key;
            $campo = $value;
        }
         if(!isUnique($tabela, $campo, $str)){
            $data['validation'][$label] = "Esse {$label} já esta cadastrado";
            $data['validation']['has_error'] = true;    
         }
    } 
   
    return $data['value'][$label];
    

}

function isUnique($tabela, $campo, $valor)
{
    $conn = conectar();
    $sql = "SELECT * FROM $tabela WHERE $campo= '{$valor}' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
       return false;
    } else {
        return true;
    }
    mysqli_close($conn);
}

function conectar()
{
    $conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DB);
// Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function paginar($pagina, $qtd_por_pagina, $total_registros = 1)
{
    global $data;
    $data['pagination'] = [];
    $data['pagination']['inicio'] = $pagina - 1;
    $data['pagination']['per_page'] = $qtd_por_pagina;
    $data['links'] = ceil($total_registros/$qtd_por_pagina) ;
    $data['anterior'] = ($pagina > 1) ? $pagina - 1 : 1;
    $data['proximo'] = ($pagina <= $data['links']) ? $pagina + 1 : $data['links'];
}

function showMessage($tipo, $msg ){
   $class = ($tipo == 'success') ? 'alert alert-success ' : 'alert alert-danger' ;
   $msg .=   "<div class='{$class}'>";
   $msg .=  "<strong>Sucesso!</strong>";
   $msg .= $msg;
   $msg .="</div>";  
}

function converterData($data)
{
    $data = $data;
    if(strpos($data, '/')){
        $data = implode("-",array_reverse(explode("/",$data)));
    }else{
        $data = implode("/",array_reverse(explode("-",$data)));
    }
    return $data;
}
function formatarMoeda($numero)
{
    return number_format($numero, 2, ',', '.');
}

function formatarDecimal($numero)
{
   return str_replace(',', '.', str_replace('.', '', $numero));
}
