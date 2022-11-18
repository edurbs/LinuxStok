<?php
include_once('relatorios'.bar.'relatorios.php');
class caixa_recebimento_vendas extends relatorios {

    function caixa_recebimento_vendas (){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		//$this->janela->show();
		//$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		
		$this->frame_codigo1->show_all();
		$this->labelframe_codigo1->set_text("Caixa");
		$this->entry_codigo1->connect('key_press_event',
        	      array($this,'entry_enter'),
                'select codigo, descricao from cadcaixa',
                true,
                $this->entry_codigo1, 
                $this->label_codigo1,
                'cadcaixa',
                "descricao",
                "codigo"
        );
        $this->entry_codigo1->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'cadcaixa', 
            $this->entry_codigo1, 
            $this->label_codigo1, 
            'codigo', 
            'descricao'
        );
		
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
        
        $this->codigocaixa=$this->pegaNumero($this->entry_codigo1);
        if(!empty($this->codigocaixa) and !$this->retornabusca2('cadcaixa', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'descricao')){
           msg("Preencha corretamente o campo Caixa!");
           $con->Disconnect();
           return;
        }
        
        
		$con=$this->conecta();
		$sql="SELECT cli.nome AS cliente, r.codsaidas AS codvenda, s.data AS datavenda, m.data_c AS datapgto, (m.valor+m.multa+m.juros) AS valor, m.desconto FROM movimentos AS m INNER JOIN receber AS r ON (m.codmovim=r.codigo) INNER JOIN saidas AS s ON (r.codsaidas=s.codsaidas)  INNER JOIN clientes AS cli ON (cli.codigo=r.codorigem) WHERE m.data_c>='$this->data1' AND m.data_c<='$this->data2' AND m.formamovim='E' AND m.codcadcaixa='$this->codigocaixa' AND m.tipomovim='C'";		
		
    	if(!$resultado=$con->Query($sql)){
			msg("Erro ao executar comando SQL");
			return;
		}
		
		$numerolin=$con->NumRows($resultado);
		if($numerolin==0){
			msg("Sua consulta nao retornou nenhum resultado!");
			return;
		}
    	
		$titulo="Relatorio de Recebimento de Vendas no Caixa";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Nome Cliente";
		$cabtabela[1]="Cod.Venda";
		$cabtabela[2]="Dt.Venda";
		$cabtabela[3]="Dt.Pgto";
		$cabtabela[4]="Valor Pago";
		$cabtabela[5]="Desconto";
		
		$saldo=0;
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$this->corrigeNumero($i[2],'data');
            $corpo[$j][3]=$this->corrigeNumero($i[3],'data');
            $corpo[$j][4]=$this->corrigeNumero($i[4],'virgula');
            $corpo[$j][5]=$this->corrigeNumero($i[5],'virgula');
            // soma totais
            $saldo+=$i[4];
            $desconto+=$i[5];            
            // controla linha
            $j++;
        }
        
        $pe[0]="Recebimentos: ".$this->corrigeNumero($saldo,'virgula');
        $pe[1]="Descontos: ".$this->corrigeNumero($desconto,'virgula');
        

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