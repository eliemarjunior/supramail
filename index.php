<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'api.class.php';
require 'supramail.class.php';

$token = 'SEUTOKENAQUI';
$domain = 'seusite.com.br';

$supramail = new Supramail($token, 'json');

//listar todas as contas
$ret = $supramail->getContas($domain);//getContas|getContasVirtuais|getContasNormais
if( isset($ret->error) ){
  echo $ret->error;
}else{
	foreach($contas['email'] as $conta){
		echo $conta.'<br>';
	}
}

//criar conta de email
$ret = $mav->criarConta($domain, 'Padrao', 'teste@'.$domain, '123456', true);//true=virtual|false=normal
if( isset($ret->error) ){
  echo $ret->error;
}else{
  echo 'Sucesso criando email';
}
echo '<br>';

//remover conta de email
$ret = $mav->removerConta($domain, 'teste@'.$domain);
if( isset($ret->error) ){
  echo $ret->error;
}else{
  echo 'Sucesso excluindo email';
}
echo '<br>';

//alterar senha da conta de email
$ret = $mav->alteraSenhaConta($domain, 'teste@'.$domain,'123123');
if( isset($ret->error) ){
  echo $ret->error;
}else{
  echo 'Sucesso alterando senha';
}

