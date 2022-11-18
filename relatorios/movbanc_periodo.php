<?php
include_once('relatorios'.bar.'relatorios.php');
class movbanc_periodo extends relatorios {

    function movbanc_periodo (){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		$this->frame_codigo1->show_all();
		$this->labelframe_codigo1->set_text("Conta de Banco");
		$this->entry_codigo1->connect('key_press_event',
        	      array($this,'entry_enter'),
                'select codbanco, numero, titular, agencia, conta from bancos',
                true,
                $this->entry_codigo1, 
                $this->label_codigo1,
                'bancos',
                "codbanco",
                "codbanco"
        );
        $this->entry_codigo1->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'bancos', 
            $this->entry_codigo1, 
            $this->label_codigo1, 
            'codbanco', 
            'codbanco'
        );
        
        $this->frame_codigo2->show_all();
		$this->labelframe_codigo2->set_text("Plano de Contas");
		$this->entry_codigo2->connect('key_press_event',
        	      array($this,'entry_enter'),
                'select codigo, descricao from placon',
                true,
                $this->entry_codigo2, 
                $this->label_codigo2,
                'placon',
                "descricao",
                "codigo"
        );
        $this->entry_codigo2->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'placon', 
            $this->entry_codigo2, 
            $this->label_codigo2, 
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
        if(empty($this->codigocaixa) or !$this->retornabusca2('bancos', $this->entry_codigo1, $this->label_codigo1, 'codbanco', 'codbanco')){
           msg("Preencha corretamente o campo Banco!");
           $con->Disconnect();
           return;
        }
        
        $this->placon=$this->pegaNumero($this->entry_codigo2);
        if(!empty($this->placon) and !$this->retornabusca2('placon', $this->entry_codigo2, $this->label_codigo2, 'codigo', 'descricao')){
           msg("Preencha corretamente o campo Plano de Contas!");
           $con->Disconnect();
           return;
        }        
        
		$con=$this->conecta();
		$sql="SELECT c.data, c.hora, c.formamovim, c.valor, c.saldo, c.historico, c.obs FROM movbanc AS c WHERE c.data>='$this->data1' AND c.data<='$this->data2' AND c.origem='$this->codigocaixa' ";
		if(!empty($this->placon)){
			$sql.=" AND c.codplacon='$this->placon' ";
		}		
		$sql.=" ORDER BY c.data, c.hora";
    	if(!$resultado=$con->Query($sql)){
			msg("Erro ao executar comando SQL");
			return;
		}		
		$numerolin=$con->NumRows($resultado);
		if($numerolin==0){
			msg("Sua consulta nao retornou nenhum resultado!");
			return;
		}
    	
		$titulo="Relatorio de Movimento da Conta de Banco";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Data";
		$cabtabela[1]="Hora";
		$cabtabela[2]="E/S";
		$cabtabela[3]="Valor";
		$cabtabela[4]="Saldo";
		$cabtabela[5]="Historico";
		$cabtabela[6]="Obs";
		
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$i[2];
            $corpo[$j][3]=$this->corrigeNumero($i[3],'virgula');
            $corpo[$j][4]=$this->corrigeNumero($i[4],'virgula');
            $corpo[$j][5]=$i[5];
            $corpo[$j][6]=$i[6];
           
            // controla linha
            $j++;
        }
        
        $pe[0]="";
        

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