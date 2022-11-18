<?php
global $setlocalelc_ctype, $setlocalelc_all, $GLOBALVERSAO, $GLOBALBUILD, $ingles, $portugues;

//require_once('GuiInspector.php');
// $gi = new Dev_GuiInspector($this->frame_descricao);
        
$GLOBALVERSAO='0.8.1';
// use sempre o build 1 numero maior que o ultimo upgrade 
$GLOBALBUILD='37'; // se desejar aplicar um upgrade sql este numero deve ser superior ao ultimo numero da matriz do upgrade. Veja o arquivo upgrade.php
if( !class_exists('gtk')) {
    die('Nao foi possivel iniciar o PHP-GTK, verifique sua instalacao. Visite o site linuxstok.sf.net ou www.php-gtk.com.br para mais informacoes' . "\r\n");    
}
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
    define("bar",'\\');
    $portugues=array("portuguese", "ptb", "ptb_ptb");
}else{
    define("bar",'/');
    $portugues=array( "pt_BR", "pt_BR.iso88591", "pt_BR.iso-88591", "pt_BR.iso8859-1", "pt_BR.iso-8859-1", "pt_BR.utf-8", "pt_BR.utf8", "pt_BR.UTF-8", "pt_BR.UTF8", "pt-br", "portuguese", "ptb", "ptb_ptb");
}

include_once('funcoes'.bar.'validacao.php');
include_once('funcoes'.bar.'funcoes.php');

// Desativa o relatório de todos os erros
//error_reporting(0);
//error_reporting(E_ERROR | E_WARNING);
// Reporta erros simples
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Reportar E_NOTICE pode ser bom também (para reportar variáveis não iniciadas
// ou eros de digitação em nomes de variáveis ...)
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// Reportar todos os erros exceto E_NOTICE
// Este é o valor padrão no php.ini
//error_reporting(E_ALL ^ E_NOTICE);

// Reporta todos os erros
//error_reporting(E_ALL);

// gestor de erros
//$old_error_handler = set_error_handler("myErrorHandler");

if(!function_exists('sqlite_query')){
    msg("Seu PHP nao tem suporte a SQLITE!! Visite o site linuxstok.sf.net www.php-gtk.com.br para instrucoes sobre como corrigir isso\n");
    exit;
}
$ingles=array('en_US', 'en', 'english', 'usa_usa', 'usa', 'en_US.utf8', 'en_US.utf-8', 'en_US.iso8859-1', 'en_US.iso88591', 'en_US.iso-8859-1', 'en_US.iso-88591' );
#$setlocalelc_ctype=setlocale(LC_ALL, $ingles);
$setlocalelc_ctype=setlocale(LC_ALL, $portugues);
$setlocalelc_ctype=setlocale(LC_CTYPE, $portugues);
$setlocalelc_ctype=setlocale(LC_COLLATE, $portugues);
$setlocalelc_ctype=setlocale(LC_MONETARY, $ingles);
$setlocalelc_ctype=setlocale(LC_TIME, $ingles);
//$setlocalelc_ctype=setlocale(LC_NUMERIC, $ingles);
$setlocalelc_ctype=setlocale(LC_NUMERIC, 'POSIX');


// verificacao de configuracoes
include_once('modulos'.bar.'configuracoes.php');
$configuracoes = new configuracoes;
// drivers adaptados do Agata para os Bancos de Dados.
$file="DBDriver".bar.retorna_CONFIG("BancoDeDados").'.class';
if(!file_exists($file)){
    echo "***************\nConfiguracoes inexistentes, restaurando....\n";
    $configuracoes->func_restaurar();
}

include_once("DBDriver".bar.retorna_CONFIG("BancoDeDados").'.class');
$BBancoDeDados=retorna_CONFIG("BancoDeDados");
if($BBancoDeDados=="AgataSqlite" or $BBancoDeDados=="AgataPgsql"){
	$teste=new $BBancoDeDados; 
	$teste->Connect();
	$teste->teste_criacao();

}

// usado para incluir um arquivo de atualizao tipo: php-gtk2 -a LinuxStok.php atualizacao.php
if(count($argv)>1){
	include_once("funcoes".bar."parametros.php");
	new parametros($argv[1], $argv[2], $argv[3]);
}


include_once("funcoes".bar."upgrade.php");
new upgrade();

include_once('modulos'.bar.'login.php');
$login=new login;

gtk::main();


		
?>
