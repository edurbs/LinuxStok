<?php

class geramenurel extends funcoes {

    function geramenurel($tabela="cliente",$camposdata,$sql){
		
        $this->xml=new GladeXML("interface".bar."menurel.glade2");
        $this->janela=$this->xml->get_widget('window1');
        $this->janela->connect_simple('delete-event', array(&$this,'fecha_janela'));
        $this->button_sair=$this->xml->get_widget('button_sair');
        $this->button_sair->connect_simple('clicked', array(&$this,'fecha_janela'));
        
        $this->scroll1=$this->xml->get_widget('scrolledwindow1');
        $this->scroll2=$this->xml->get_widget('scrolledwindow2');
            
        $this->lista1=new Lista(array('    Tabela'));
        $this->lista2=new Lista(array('    Relatorio'));
        
        $this->scroll1->add($this->lista1);
        $this->scroll2->add($this->lista2);
        
        $this->campos=$this->nomes_dos_campos($tabela);    
        for($i=0;$i<count($this->campos);$i++){
            $this->lista1->append(array($this->campos[$i]));
        }
    
        $this->botao_incluir=$this->xml->get_widget('button_incluir');
        $this->botao_incluirtudo=$this->xml->get_widget('button_incluirtudo');
        $this->botao_remover=$this->xml->get_widget('button_remover');
        $this->botao_removertudo=$this->xml->get_widget('button_removertudo');
        
        $this->botao_incluir->connect_simple('clicked', array($this->lista1, 'Transfere'), $this->lista2);
        $this->botao_incluirtudo->connect_simple('clicked', array($this->lista1, 'TransfereTodos'), $this->lista2);
        $this->botao_remover->connect_simple('clicked', array($this->lista2, 'Transfere'), $this->lista1);
        $this->botao_removertudo->connect_simple('clicked', array($this->lista2, 'TransfereTodos'), $this->lista1);
        
        $this->combo_classificar=$this->xml->get_widget('combo_classificar');
        $this->combo_classificar->set_popdown_strings($this->campos);
        
        $this->combo_periodo=$this->xml->get_widget('combo_periodo');
        if(!empty($camposdata)){
            $this->combo_periodo->set_popdown_strings($camposdata);
        }
        $this->combo_filtrar=$this->xml->get_widget('combo_filtrar');
        $this->combo_filtrar->set_popdown_strings($this->campos);
        
        $this->button_gerar=$this->xml->get_widget('button_gerar');
        $this->button_gerar->connect_simple('clicked', array(&$this, 'gerar'), $tabela);
        
        $this->button_limpar=$this->xml->get_widget('button_limpar');
        $this->button_limpar->connect_simple('clicked', array(&$this, 'limpar'));
        
        // pega os entry das datas
        $this->entry_dia1=$this->xml->get_widget('entry_dia1');
        $this->entry_mes1=$this->xml->get_widget('entry_mes1');
        $this->entry_ano1=$this->xml->get_widget('entry_ano1');
        $this->entry_dia2=$this->xml->get_widget('entry_dia2');
        $this->entry_mes2=$this->xml->get_widget('entry_mes2');
        $this->entry_ano2=$this->xml->get_widget('entry_ano2');
        
        // pegao os combo de data do glade
        $this->combo_dia1=$this->xml->get_widget('combo_dia1');        
        $this->combo_mes1=$this->xml->get_widget('combo_mes1');
        $this->combo_ano1=$this->xml->get_widget('combo_ano1');
        $this->combo_dia2=$this->xml->get_widget('combo_dia2');
        $this->combo_mes2=$this->xml->get_widget('combo_mes2');
        $this->combo_ano2=$this->xml->get_widget('combo_ano2');        
        // cria os dias, meses e anos
        for($i=1;$i<=31;$i++){
            $dias[$i]=$this->leading_zero($i,2);
        }
        for($i=1;$i<=12;$i++){
            $meses[$i]=$this->leading_zero($i,2);
        }
        for($i=1900;$i<=2015;$i++){
            $anos[$i]="$i";
        }
        // os dias/meses/anos bota no combo
        $this->combo_dia1->set_popdown_strings($dias);
        $this->combo_dia2->set_popdown_strings($dias);
        $this->combo_mes1->set_popdown_strings($meses);
        $this->combo_mes2->set_popdown_strings($meses);
        $this->combo_ano1->set_popdown_strings($anos);
        $this->combo_ano2->set_popdown_strings($anos);
        
        $this->button_limpar->clicked();
        
        $this->janela->show_all();        
    }
    
    function limpar(){
        $this->entry_dia1->set_text('');
        $this->entry_mes1->set_text('');
        $this->entry_ano1->set_text('');
        $this->entry_dia2->set_text('');
        $this->entry_mes2->set_text('');
        $this->entry_ano2->set_text('');
    }
    
    function gerar($tabela){
        $sql="SELECT ";
        $escolha=$this->lista2->ObtemTodos();
        if(count($escolha)==0){
            msg('Escolha os campos para o relatorio!');
            return;
        }
        for($i=0;$i<count($escolha);$i++){
            if($i==0){
                $sql.="$escolha[$i]";
            }else{
                $sql.=",$escolha[$i]";
            }            
        }
        if(empty($tabela)){
            msg('Tabela nao informada.');
            return;
        }
        $sql.=" FROM $tabela ";
        
        
        
        $dia1=$this->entry_dia1->get_text();
		$mes1=$this->entry_mes1->get_text();
		$ano1=$this->entry_ano1->get_text();
		$data1=$ano1.'/'.$mes1.'/'.$dia1;
        
        $dia2=$this->entry_dia2->get_text();
		$mes2=$this->entry_mes2->get_text();
		$ano2=$this->entry_ano2->get_text();
		$data2=$ano2.'/'.$mes2.'/'.$dia2;
        
        $where=" WHERE (";        
        $poewhere=false;
        $poewhere1=false;
        
        $tmp=$this->combo_periodo->entry;
        $periodo=$tmp->get_text();
        if(strlen($data1)==10 and strlen($data2)==10 and !empty($periodo)){
            if($this->date_diff($data1,$data2)<0){
                msg('Data inicial ANTERIOR a data final.');
                return;
            }
            $poewhere=true; // usei a clausula where
            $poewhere1=true; // falo pra funcao do filtrar que usei a clausula where
            
            $where.=" $periodo>='$data2' AND $periodo<='$data2' ";        
        }
        
        
        $this->entry_texto=$this->xml->get_widget('entry_texto');
        $texto=$this->entry_texto->get_text();
        $texto=strtoupper($texto);
        
        if(!empty($texto)){
            $sinais['Contem']='=';
            $sinais['Igual a']='=';
            $sinais['Comeca com']='=';
            $sinais['Termina com']='=';
            $sinais['Diferente de']='<>';
            $sinais['Maior que']='>';
            $sinais['Menor que']='<';
            
            $this->combo_sinal=$this->xml->get_widget('combo_sinal');
            $tmp=$this->combo_sinal->entry;
            $sinal=$tmp->get_text();
            
            $this->combo_filtrar=$this->xml->get_widget('combo_filtrar');
            $tmp=$this->combo_filtrar->entry;
            $filtrar=$tmp->get_text();
            
            $poewhere=true; // usei a clausula where
            
            // se a clausula where ja foi usada.. entaum bota um AND
            if($poewhere1){
                $where.=" AND ";
            }
            
            if($sinal=="Contem"){
                $where.=" $filtrar LIKE '%$texto%' ";
            }elseif($sinal=="Comeca com"){
                $where.="  $filtrar LIKE '$texto%' ";
            }elseif($sinal=="Termina com"){
                $where.=" $filtrar LIKE '%$texto' ";
            }else{
                $where.=" $filtrar".$sinais[$sinal]."'$texto' ";
            }
        }
        
        // se usou a clausula where entao fecha-a
        if($poewhere){
            $where.=")";
            $sql.=$where;
        }
        
        $tmp=$this->combo_classificar->entry;
        $classificar=$tmp->get_text();
        $sql.=" ORDER BY $classificar ";
        
        $this->checkbutton_ordem=$this->xml->get_widget('checkbutton_ordem');
        if($this->checkbutton_ordem->get_active()){
            $sql.=" DESC ";
        } else {
            $sql.=" ASC ";
        }
        $sql.=";";  
        
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();        
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultado o banco de dados, verifique as opcoes do relatorio");
            return;
        }
       
        $this->radiobutton_html=$this->xml->get_widget('radiobutton_html');
        if($this->radiobutton_html->get_active()){
            $tipoarq="HTML";
        }else{
            $tipoarq="TXT";
        }
        
        if($tipoarq=="HTML"){
            $tamanho=$con->NumCols($resultado);
            $html.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
                    <!--
                        Template de Relatório desenvolvido por Lucas Saud <lucas.saud at gmail dot com>
                    -->
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
            
            $html.='<title>Relatorio da tabela $tabela, classificado por $classificar</title>';
            $html.='
                    <style><!--
            html {
                    margin: 0 0 1px;
                    height: 100%;
                    background-color: white;
                    padding: 0;
            }
            body {
                    margin: 0;
                    background-color: white;
                    padding: 10px;
                    font-size: 13px;
                    font-family: verdana, sans-serif;
            }
            
            table.tbl {
                    margin-top: 10px;
                    width: 100%;
                    border: 1px solid silver;
                    border-collapse: collapse;
                    border-spacing: 0;
                    empty-cells: show;
            }
            
            table.tbl tr.hrw {
                    background-color: #cbd3d9;
                    font-weight: bold;
            }
            
            table.tbl th {
                    border: 1px solid silver;
                    padding: 2px 4px;
                    text-align: left;
                    white-space: nowrap;
            }
            
            
            
            table.tbl td {
                    border: 1px solid silver;
                    padding: 2px 4px;
            }
            
            table.tbl .shr {
                    width: 15%;
                    white-space: nowrap;
            }
            //-->
            </style>
            ';
            
            $html.='<link rel="stylesheet" type="text/css" href="screen.css" /></head><body>';
    
            $html.="<h2>Relatorio da tabela $tabela, classificado por $classificar</h2>";
            $html.='<table class="tbl">';
            
            $html.="<thead><tr class='hrw'>";
            for($j=0;$j<$tamanho;$j++){
                $html.="<td class='shr'>".$escolha[$j]."</td>";
            }
            $html.="</tr></thead>";
            $html.="<tbody>";
            
            while($i = $con->FetchRow($resultado)) {
                $html.="<tr class='crw'>";
                for($j=0;$j<$tamanho;$j++){
                    $html.="<td  class='shr'>&nbsp ";
                    $html.=$i[$j]."</td>";
                }
                $html.="</tr>";
            }         
            $html.="</tbody>";
            $html.="</table></body></html>";
            $con->Disconnect();
            
            
            // chama o browser aqui
            
            
            $tmpfile=gettimeofday();        
            $tmpfile=$tmpfile['sec'].$tmpfile['usec'].'.html';
            
            $relfile=retorna_CONFIG("tmppath").$tmpfile;
            
            if(!$handle=fopen($relfile,"w")){
                msg("Erro abrindo o arquivo html. Crie a pasta ".retorna_CONFIG("tmppath"));
            }else{
                if(!fwrite($handle, $html)){
                    msg("Erro escrevendo no arquivo html");            
                }else{
                    if(!fclose($handle)){
                        msg("Erro fechando o arquivo html");            
                    } else {
                        shell_exec(retorna_CONFIG("browser")." file://".$relfile);
                        while (gtk::events_pending())gtk::main_iteration();
                    }
                }
            }
        }else{
        
            msg('Carma que isso ainda num fiz!');
            
        }
    }
    function fecha_janela(){
        $this->janela->hide();
        return true;
    }
}


class Lista extends GtkCList
{
	function Lista($a)
	{
		GtkCList::GtkCList(1, $a);
	}

	function Adiciona($array)
	{
		if(count($array)>=0){
            foreach ($array as $item)
                GtkCList::append(array($item));
        }
	}

	function Obtem()
	{
		$linha = $this->selection[0];
        
        if($linha>=0){
            return GtkCList::get_text($linha,0);
        }else{
            return;
        }
	}

	function ObtemTodos()
	{
		$linha = 0;
		while ($elemento = @GtkCList::get_text($linha, 0))
		{
			$elementos[] = $elemento;
			$linha ++;
		}
		return $elementos;
	}
	
	function Transfere($lista)
	{
		$elemento = $this->Obtem();
		if(!empty($elemento)){
            $lista->Adiciona(array($elemento));
		
            $linha = $this->selection[0];
		
            GtkCList::remove($linha);
        }
        
	}

	function TransfereTodos($lista)
	{
		$elementos = $this->ObtemTodos();
		$lista->Adiciona($elementos);
		$this->clear();
	}

	function ExibeElementos()
	{
		$elementos = $this->ObtemTodos();
		var_dump($elementos);
	}
}
?>