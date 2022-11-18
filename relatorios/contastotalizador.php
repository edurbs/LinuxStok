<?php
include_once('relatorios'.bar.'relatorios.php');
class contastotalizador extends relatorios {

    function contastotalizador (){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		//$this->janela->show();
		$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		$this->janela->set_title("Contas - Totalizador");
		
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
		
		// total das contas a  receber
		$sql_contas_receber="SELECT r.data_v,sum(r.saldo) AS totalreceber FROM receber AS r WHERE r.data_v>='$this->data1' AND r.data_v<='$this->data2' GROUP BY r.data_v ";
		
		// total cheques a receber
		$sql_cheques_receber="SELECT x.bompara, sum(x.valor) AS total FROM cheque AS x INNER JOIN clientes AS c ON (x.codcliente=c.codigo) WHERE (situacao='NOVO' OR situacao='PROGRAMADO') GROUP BY x.bompara ";
		
		// total das contas a pagar 
		$sql_contas_pagar="SELECT p.data_v,sum(p.saldo) AS totalpagar FROM pagar AS p WHERE p.data_v>='$this->data1' AND p.data_v<='$this->data2' GROUP BY p.data_v ";
			
		// total cheques a pagar
		$sql_cheques_pagar="SELECT x.bompara, sum(x.valor) AS total FROM cheque AS x INNER JOIN fornecedores AS f ON (x.codfornecedor=f.codigo) WHERE (situacao='NOVO' OR situacao='PROGRAMADO') GROUP BY x.bompara ";

		
    	if(!$resultado_contas_receber=$con->Query($sql_contas_receber)){
			msg("Erro ao executar comando SQL contas receber!");
			return;
		}elseif(!$resultado_cheques_receber=$con->Query($sql_cheques_receber)){
			msg("Erro ao executar comando SQL cheques receber!");
			return;
		}elseif(!$resultado_contas_pagar=$con->Query($sql_contas_pagar)){
			msg("Erro ao executar comando SQL contas pagar!");
			return;
		}elseif(!$resultado_cheques_pagar=$con->Query($sql_cheques_pagar)){
			msg("Erro ao executar comando SQL cheques pagar!");
			return;
		}
		
		$numerolin=0;
		$numerolin+=$con->NumRows($resultado_contas_receber);
		$numerolin+=$con->NumRows($resultado_cheques_receber);
		$numerolin+=$con->NumRows($resultado_contas_pagar);
		$numerolin+=$con->NumRows($resultado_cheques_pagar);
		if($numerolin==0){
			msg("Sua consulta nao retornou nenhum resultado!");
			return;
		}
    	
		$titulo="Relatorio Totalizador de Contas";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Data";
		$cabtabela[1]="Receber";
		$cabtabela[2]="Pagar";
		$cabtabela[3]="Parcial";
		$cabtabela[4]="Saldo";
		
		// soma contas a receber
		while($i = $con->FetchRow($resultado_contas_receber)){
			//     data   valor
			$receber[$i[0]]+=$i[1];
			$saldo[$i[0]]+=$i[1];
		}
		// soma cheques a receber
		while($i = $con->FetchRow($resultado_cheques_receber)){
			//     data   valor
			$receber[$i[0]]+=$i[1];
			$saldo[$i[0]]+=$i[1];
		}
		
		// soma contas a pagar
		while($i = $con->FetchRow($resultado_contas_pagar)){
			//     data   valor
			$pagar[$i[0]]+=$i[1];
			$saldo[$i[0]]-=$i[1];
		}
		// soma cheques a pagar
		while($i = $con->FetchRow($resultado_cheques_pagar)){
			//     data   valor
			$pagar[$i[0]]+=$i[1];
			$saldo[$i[0]]-=$i[1];
		}

		$saldo_final=0;
		$j=0;
		foreach($saldo as $key=>$saldo_parcial){
			$corpo[$j][0]=$this->corrigeNumero($key,'data');
			$corpo[$j][1]=$this->corrigeNumero($receber[$key],'virgula');
			$corpo[$j][2]=$this->corrigeNumero($pagar[$key],'virgula');
			$corpo[$j][3]=$this->corrigeNumero($saldo_parcial,'virgula');
			$saldo_final+=$saldo_parcial;
			$corpo[$j][4]=$this->corrigeNumero($saldo_final,'virgula');
			$j++;
		}

		$pe[0]="Total de Entradas: ".$this->mascara2(array_sum($receber),'moeda');
		$pe[1]="Total de Saidas: ".$this->mascara2(array_sum($pagar),'moeda');
		$pe[2]="Saldo Final: ".$this->mascara2(array_sum($saldo),'moeda');

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