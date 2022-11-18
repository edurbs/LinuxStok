<?php
include_once('relatorios'.bar.'relatorios.php');
class relcontas extends relatorios {
    
    function relcontas($origem, $titulo, $tabela, $tipo="contas"){
		parent::__construct();
        
		$this->frame_codigo1->show_all();
		$this->labelframe_codigo1->set_text($origem);
		$this->entry_codigo1->connect('key_press_event',
        	      array($this,'entry_enter'),
                'select codigo, nome from '.$origem,
                true,
                $this->entry_codigo1, 
                $this->label_codigo1,
                $origem,
                "nome",
                "codigo"
        );
        $this->entry_codigo1->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            $origem, 
            $this->entry_codigo1, 
            $this->label_codigo1, 
            'codigo', 
            'nome'
        );

		$this->frame_codigo2->show_all();
		$this->labelframe_codigo2->set_text('Plano de Contas');
		$this->entry_codigo2->connect('key_press_event',
        	      array($this,'entry_enter'),
                'select codigo, descricao from placon',
                true,
                $this->entry_codigo2, 
                $this->label_codigo2,
                "placon",
                "descricao",
                "codigo"
        );
        $this->entry_codigo2->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            "placon", 
            $this->entry_codigo2, 
            $this->label_codigo2, 
            'codigo', 
            'descricao'
        );

        
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		$this->orderby["o.nome, c.data_v ASC "]="Nome e Data de Vencimento";
		$this->orderby["c.data_v, o.nome ASC "]="Data de Vencimento e Nome";
		$this->orderby["o.nome"]="Nome";
		$this->orderby["c.data_v ASC"]="Data de Vencimento";
		$this->orderby["c.data_v DESC"]="Data de Vencimento Inversa";
		$this->orderby["c.valor"]="Valor";
		$this->orderby["c.saldo"]="Saldo";
		$this->orderby["c.codplacon"]="Plano de Contas";
		$this->orderby["precototal"]="Preco Total";
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);
    	
    	$this->hbox_radiobutton->show_all();
    	$this->radiobutton1->set_label("Todas");
    	$this->radiobutton1->set_active(TRUE);
    	$this->radiobutton2->set_label("Quitadas");
    	$this->radiobutton3->hide();
    	$this->radiobutton4->hide();
    	$this->radiobutton5->hide();
    	$this->radiobutton6->hide();
    	//$this->radiobutton_todas=$this->xml->get_widget('radiobutton_todas');
    	//$this->radiobutton_quitadas=$this->xml->get_widget('radiobutton_quitadas');		
    	
        if($tipo=="contas"){
			$funcao="gerar_relcontas";
        }else{
        	$this->label_combo1->hide();
        	$this->combo1->hide();
        	$funcao="gerar_relmovimentocontas";
        }

    	$this->button_html->connect_simple('clicked',array($this, $funcao), $origem, $tabela, 'html');
		$this->button_texto->connect_simple('clicked',array($this, $funcao), $origem, $tabela, 'texto');
		$this->button_tela->connect_simple('clicked',array($this, $funcao), $origem, $tabela, 'tela');
		
		
    }
    
    function verificaDataPeriodo($verificavendedor=false){
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
        
        return true;
    }
    
    function gerar_relcontas($origem,$tabela,$tipo){    		
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        
        if(!$this->verificaDataPeriodo()){
            $con->Disconnect();
            return;
        }
        $codcli=$this->pegaNumero($this->entry_codigo1);
        if(!empty($codcli) and !$this->retornabusca2($origem, $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
           msg("Preencha corretamente o campo $origem!");
           $con->Disconnect();
           return;
        }
        
        $placon=$con->EscapeString($this->entry_codigo2->get_text());
        if(!empty($placon) and !$this->retornabusca2('placon', $this->entry_codigo2, $this->label_codigo2, 'codigo', 'descricao')){
           msg("Preencha corretamente o campo Plano de Contas!");
           $con->Disconnect();
           return;
        }
        
        $sql="SELECT c.codigo, o.nome, c.data_v, c.valor, c.saldo, c.codplacon, p.descricao FROM $tabela AS c LEFT JOIN $origem AS o ON (c.codorigem=o.codigo) LEFT JOIN placon AS p ON (p.codigo=c.codplacon) WHERE";
        if(!empty($codcli)){
        		$sql.=" c.codorigem=$codcli AND ";
        }    		
        if(!empty($placon)){
        		$sql.=" c.codplacon='$placon' AND ";
        }
         // AND saldo>0 AND c.data_v='".$this->corrigeNumero($this->datadehoje,'dataiso')."'");
        
        if($this->radiobutton1->get_active()){
    		$sql.="  c.saldo>0 AND c.data_v>='".$this->data1."' AND c.data_v<='".$this->data2."' ";
        }elseif($this->radiobutton2->get_active()){
			$sql.=" c.saldo=0 AND c.data_v>='".$this->data1."' AND c.data_v<='".$this->data2."' ";
		}
		/*elseif($this->radiobutton_atrasadas->get_active()){
			$sql.=" c.saldo>0 AND c.data_v>'".$this->data1."' AND c.data_v<'".$this->data2."' ";
		}elseif($this->radiobutton_vincendo->get_active()){
			//$sql.=" c.saldo>0 AND c.data_v>'".$this->corrigeNumero($this->datadehoje,'dataiso')."' ";
			if($this->corrigeNumero($this->datadehoje,'dataiso')
			$sql.=" c.saldo>0 AND c.data_v>'".$this->data1."' AND c.data_v<'".$this->data2."' ";
		}*/
		//$sql.=" ORDER BY o.nome, c.data_v ASC ";

		// pega qual a escolha da combobox de orderby
		$order=$this->combo1->get_active_text();
		// pega o nome do campo da array criada na funcao anterior
		$key=array_search($order, $this->orderby);
		
		$sql.=" ORDER BY ".$key;
	
        
        
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        //c.codigo, o.nome, c.data_v, c.valor, c.saldo
        $titulo="Relatorio de Contas a $tabela";
        $cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
        $cabtabela[0]="Cod.Conta";
        $cabtabela[1]=$origem;
        $cabtabela[2]="Dt.Venc";
        $cabtabela[3]="Valor";
        $cabtabela[4]="Saldo";
        $cabtabela[5]="Cod.PC.";
        $cabtabela[6]="Pla.Con.";
        $valor=0;
        $saldo=0;
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$this->corrigeNumero($i[2],'data');
            $corpo[$j][3]=$this->corrigeNumero($i[3],'virgula');
            $corpo[$j][4]=$this->corrigeNumero($i[4],'virgula');
            $corpo[$j][5]=$i[5];
            $corpo[$j][6]=$i[6];
            // soma totais
            $valor+=$i[3];
            $saldo+=$i[4];            
            // controla linha
            $j++;
        }
       
        $pe[0]="Total das contas: ".$this->corrigeNumero($valor,'virgula')."; Saldo a $tabela: ".$this->corrigeNumero($saldo,'virgula');
        //$this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        if($tipo=="html"){
			$this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="texto"){
			$this->geraTEXTO($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="tela"){
			$this->geraTELA($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}
        
        $con->Disconnect();
        return;
    }
    
    function gerar_relmovimentocontas($origem,$tabela,$tipo){
        if(!$this->verificaDataPeriodo()){
            return;
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        
        $codcli=$this->entry_codigo1->get_text();
        if(!empty($codcli) and !$this->retornabusca2($origem, $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
           msg("Preencha corretamente o campo $origem!");
           $con->Disconnect();
           return;
        }
        $placon=$con->EscapeString($this->entry_codigo2->get_text());
        if(!empty($placon) and !$this->retornabusca2('placon', $this->entry_codigo2, $this->label_codigo2, 'codigo', 'descricao')){
           msg("Preencha corretamente o campo Plano de Contas!");
           $con->Disconnect();
           return;
        }
        if($tabela=='receber'){
        		$formamovim='E';
        		$sql_add=", c.codsaidas ";
        		$titulo_coluna_venda="Venda";
        }else{
        		$formamovim='S';
        		$sql_add=", c.codentradas ";
        		$titulo_coluna_venda="Compra";
        }
        $codcli=$this->pegaNumero($this->entry_codigo1);
        $sql="SELECT c.codigo, c.data_v, m.data_c, m.tipomovim, c.valor, m.valor, m.multa, m.juros, m.historico, m.tipodoc".$sql_add.", c.codplacon, c.obs  FROM movimentos AS m INNER JOIN receber AS c ON m.codmovim=c.codigo WHERE m.formamovim='$formamovim' ";
        
        if(!empty($codcli)){
        		$sql.=" AND c.codorigem=$codcli ";
        }   
        if(!empty($placon)){
        		$sql.=" AND c.codplacon='$placon' ";
        }
        $sql.=" AND m.data_c>='".$this->data1."' AND m.data_c<='".$this->data2."' "; 		
        $sql.=" ORDER BY c.codigo, m.data_c ASC ";
        
        
        
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            $con->Disconnect();
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            $con->Disconnect();
            return;
        }
        $this->janela->hide();
        
        //c.codigo, o.nome, c.data_v, c.valor, c.saldo
        $titulo="Relatorio de Movimento de Contas a $tabela";
        $cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
        $cabtabela[0]="Cod.Conta";
        $cabtabela[1]="Data Vcto";
        $cabtabela[2]="Data Pgto";
        $cabtabela[3]="Dias Atraso";
        $cabtabela[4]="C/B";
        $cabtabela[5]="Total Conta";
        $cabtabela[6]="Valor Pago";
		$cabtabela[7]=$titulo_coluna_venda; // codigo da venda/compra
		$cabtabela[8]="Pla.Con";
		$cabtabela[9]="Historico"; 
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
        		$codconta=$i[0];
            $corpo[$j][0]=$codconta;
            $soma_contas[$codconta]="1";
            
            $dataV=$this->corrigeNumero($i[1],'data');
            $corpo[$j][1]=$dataV;
            
            $dataP=$this->corrigeNumero($i[2],'data');
            $corpo[$j][2]=$dataP;
            
            $dias_atraso=$this->date_diff($dataV, $dataP);
            if($dias_atraso<0){ 
            		$dias_atraso=0;
            	}			
            $corpo[$j][3]=$dias_atraso; 
            
            $corpo[$j][4]=$i[3]; // caixa ou banco
            $corpo[$j][5]=$this->corrigeNumero($i[4],'virgula'); // valor total da conta
            
            $total_pago=$i[5]+$i[6]+$i[7]; // soma valor+multa+juros
            $corpo[$j][6]=$this->corrigeNumero($total_pago,'virgula');

            $corpo[$j][7]=$i[10]; // codigo da venda/compra
            $corpo[$j][8]=$i[11]; // plano de contas
            $corpo[$j][9]=$i[8]." ".$i[12]; // historico e obs
            
            // calcula de dias de atraso
            $doc=$i[8];
            if($doc<>"E"){ // se nao for estorno 
            		$soma_dias_atraso+=$dias_atraso;
            		$contador_atraso++;
            }else{ // se for estorno entao cancela 
            		$soma_dias_atraso-=$dias_atraso;
            		$contador_atraso--;
            }
            
            // soma pagamentos
            $soma_pagamentos+=$total_pago;
            // controla linha
            $j++;
        }
        $media_atraso=$soma_dias_atraso/$contador_atraso;
        
        $pe[0]="Total de pagamentos: ".$this->corrigeNumero($soma_pagamentos,'virgula');
        $pe[1]="Media de Atraso: ".$this->corrigeNumero($media_atraso,'virgula');
        $pe[2]="Total de contas diferentes: ".count($soma_contas);

        //$this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        if($tipo=="html"){
			$this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="texto"){
			$this->geraTEXTO($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="tela"){
			$this->geraTELA($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}
        
        $con->Disconnect();
        return;
    }
}
?>
