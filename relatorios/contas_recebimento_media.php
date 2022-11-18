<?php
include_once('relatorios'.bar.'relatorios.php');
class contas_recebimento_media extends relatorios {

    function contas_recebimento_media (){
	
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
		$sql="SELECT s.data, avg(r.data_v-s.data) FROM receber AS r INNER JOIN clientes AS c ON (r.codorigem=c.codigo) INNER JOIN saidas AS s ON (s.codsaidas=r.codsaidas) WHERE r.saldo>0 AND s.data>='$this->data1' AND s.data<='$this->data2' GROUP BY s.data ORDER BY s.data";
		
    	if(!$resultado=$con->Query($sql)){
			msg("Erro ao executar comando SQL");
			return;
		}
		
		$numerolin=$con->NumRows($resultado);
		if($numerolin==0){
			msg("Sua consulta nao retornou nenhum resultado!");
			return;
		}
    	
		$titulo="Relatorio de Media de Recebimento de Contas";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Data";
		$cabtabela[1]="Media";
		
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            $corpo[$j][1]=$this->corrigeNumero($i[1],'virgula');
            // controla linha
            $j++;
        }
        $sql="SELECT avg(r.data_v-s.data) FROM receber AS r INNER JOIN clientes AS c ON (r.codorigem=c.codigo) INNER JOIN saidas AS s ON (s.codsaidas=r.codsaidas) WHERE r.saldo>0 AND s.data>='$this->data1' AND s.data<='$this->data2'";
        $resultado=$con->Query($sql);
        $i = $con->FetchRow($resultado);
        
        
        $pe[0]="Media geral: ".$this->corrigeNumero($i[0],'virgula');
        

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