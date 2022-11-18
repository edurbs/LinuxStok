<?php
include_once('relatorios'.bar.'relatorios.php');
class contas_mov_agrupado_placon extends relatorios {

    function contas_mov_agrupado_placon (){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		//$this->janela->show();
		//$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		
		$this->button_html->connect_simple('clicked',array($this, 'gerar'),'html');
		$this->button_texto->connect_simple('clicked',array($this, 'gerar'),'texto');
		$this->button_tela->connect_simple('clicked',array($this, 'gerar'),'tela');
	}
	
	function gerar($tipo){
		if(!$this->valida_data($this->entry_data1->get_text())){
            msg('Data Inicial Invalida');
            return false;
        }
        $this->data1=$this->corrigeNumero($this->entry_data1->get_text(),"dataiso");
        
        if(!$this->valida_data($this->entry_data2->get_text())){
            msg('Data Final Invalida');
            return false;
        }
        $this->data2=$this->corrigeNumero($this->entry_data2->get_text(),"dataiso");
        
       
        
		$con=$this->conecta();
		$sql="SELECT c.codplacon, p.descricao, sum(m.valor+m.multa+m.juros), m.formamovim, m.tipomovim,  m.codcadcaixa  FROM movimentos AS m INNER JOIN receber AS c ON (m.codmovim=c.codigo) INNER JOIN placon AS p ON (p.codigo=c.codplacon) WHERE m.data_c>='$this->data1' AND m.data_c<='$this->data2' GROUP BY c.codplacon, p.descricao,  m.formamovim, m.tipomovim, m.codcadcaixa ORDER BY p.descricao";
		
    	if(!$resultado=$con->Query($sql)){
			msg("Erro ao executar comando SQL");
			return;
		}
		
		$numerolin=$con->NumRows($resultado);
		if($numerolin==0){
			msg("Sua consulta nao retornou nenhum resultado!");
			return;
		}
    	
		$titulo="Relatorio de Movimento de Contas Agrupado por Plano de Contas";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Cod.P.C.";
		$cabtabela[1]="Plano de Contas";
		$cabtabela[2]="Valor";
		$cabtabela[3]="Forma (E/S)";
		$cabtabela[4]="Tipo (C/B)";
		$cabtabela[5]="Cod. (C/B)";
		$cabtabela[6]="Nome (C/B)";
		
		$soma=0;
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$this->corrigeNumero($i[2],'virgula');
            // entrada ou saida
            if($i[3]=="E") {
            	$corpo[$j][3]="Entrada";
            }elseif($i[3]=="S") {
            	$corpo[$j][3]="Saida";
            }else{
            	$corpo[$j][3]="Erro";
            }
            // caixa ou banco
            if($i[4]=="C") {
            	$corpo[$j][4]="Caixa";
            	// pega o nome do caixa
            	if(!empty($i[5])){
            		$descricao_cb=$this->retornabusca4('descricao','cadcaixa','codigo',$i[5]);
            	}else{
            		$descricao_cb="?";
            	}
            }elseif($i[4]=="B") {
            	$corpo[$j][4]="Banco";
            	// pega o nome do banco
            	if(!empty($i[5])){
	            	$sql2="SELECT nb.sigla, b.agencia, b.conta FROM bancos AS b INNER JOIN nomebanco AS nb ON nb.codigo=b.numero WHERE codbanco='$i[5]'";
	            	$resultado2=$con->Query($sql2);
	            	$i2 = $con->FetchRow($resultado2);
	            	$descricao_cb=$i2[0]." Ag.".$i2[1]." Ct.".$i2[2];
            	}else{
            		$descricao_cb="?";
            	}
            }else{
            	$corpo[$j][4]="Erro";
            }
            // codigo do caixa ou banco
			$corpo[$j][5]=$i[5];            
            $corpo[$j][6]=$descricao_cb;
           
            // soma total
            $soma+=$i[2];
            // controla linha
            $j++;
        }
        
        $pe[0]="Total: ".$this->corrigeNumero($soma,'virgula');

		if($tipo=="html"){
			$this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="texto"){
			$this->geraTEXTO($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="tela"){
			$this->geraTELA($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}
        $this->disconecta($this->con);
		
		msg("Relatorio gerado com sucesso!");
	}
}

?>