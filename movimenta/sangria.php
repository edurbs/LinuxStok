<?php
class sangria extends funcoes {
    function sangria($operacao,$titulo,$tabela="caixa"){
        $this->tabela=$tabela;
        if($this->tabela=="caixa"){        		
            $this->tabelaorigem="cadcaixa";
            $this->frameorigem="Caixa";
            $this->codigoorigem="codigo";
            $this->descricaoorigem="descricao";
            $this->sqlorigem='select * from '.$this->tabelaorigem;
        }else{
            $this->tabelaorigem="bancos";
            $this->frameorigem="Banco";
            $this->codigoorigem="codbanco";
            $this->descricaoorigem="numero";
			$this->sqlorigem="SELECT * FROM bancos WHERE contadaempresa='1'";
        }
        
        $this->titulo=$titulo;
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        $this->datadehojeiso=$this->anodehoje."-".$this->mesdehoje."-".$this->diadehoje;

        $this->xml=$this->carregaGlade("sangria",$titulo,false,false,false,false);
        
        $this->button_ok= $this->xml->get_widget('button_ok');
		$this->button_ok->connect_simple('clicked',array($this,'botaoOK'),$operacao);
		
		//$this->janela->connect_simple('destroy', array($this,'cancelar'));

        $this->button_cancelar= $this->xml->get_widget('button_cancelar');
        $this->button_cancelar->connect_simple('clicked', array(&$this,'cancelar'));        
        
        $this->entry_valor= $this->xml->get_widget('entry_valor');
        $this->entry_valor->connect('key-press-event', array($this,'mascaraNew'),'virgula2');
        
        
        $this->textView_obs=$this->xml->get_widget('textview_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);
          
        $this->entry_caixa=$this->xml->get_widget('entry_caixa');
        $this->entry_caixa->grab_focus();
        $this->labelframe_caixa=$this->xml->get_widget('labelframe_caixa');
        $this->labelframe_caixa->set_text($this->frameorigem);
        $this->label_caixa=$this->xml->get_widget('label_caixa');
        $this->entry_caixa->connect('key_press_event', 
            array($this,'entry_enter'), 
            $this->sqlorigem, 
            true,
            $this->entry_caixa, 
            $this->label_caixa,
            $this->tabelaorigem,
            $this->descricaoorigem,
            $this->codigoorigem
        );            
		$this->entry_caixa->connect_simple('focus-out-event',
            array($this,'retornabusca22'), 
            $this->tabelaorigem, 
            $this->entry_caixa, 
            $this->label_caixa, 
            $this->codigoorigem, 
            $this->descricaoorigem
        );
        
        $this->entry_placon=$this->xml->get_widget('entry_placon');
        $this->label_placon=$this->xml->get_widget('label_placon');
        $this->entry_placon->connect('key_press_event', 
            array($this,'entry_enter'), 
            'select * from placon', 
            true,
            $this->entry_placon, 
            $this->label_placon,
            'placon',
            'descricao',
            'codigo'
        );            
		$this->entry_placon->connect_simple('focus-out-event',
            array($this,'retornabusca22'), 
            'placon', 
            $this->entry_placon, 
            $this->label_placon, 
            'codigo', 
            'descricao'
        );
        //$this->janela->show_all();
    }
    function sangriaShow(){
    	global $parente;
    	// funcao chama pelo movcontas para mostrar a janela
    	if(!$this->janela_sangria){
    		$this->janela_sangria= $this->xml->get_widget('window1');
    	}
    	$this->janela_sangria->set_title($this->titulo);
    	$this->janela_sangria->hide();
    	$this->janela_sangria->show_all();
    	if($parente) $this->janela_sangria->set_transient_for($parente);
    	
    }
    function getCodCaixa(){
        $codcadcaixa=$this->pegaNumero($this->entry_caixa);
		if (empty($codcadcaixa)){
			msg('Codigo nao encontrado');
			$this->entry_caixa->grab_focus();
            return false;
		}
        if (!$this->retornabusca2($this->tabelaorigem, $this->entry_caixa, false, $this->codigoorigem, $this->descricaoorigem)){
        		msg('Codigo nao encontrado');
        		$this->entry_caixa->grab_focus();
            return false;
        }
        return $codcadcaixa;    
    }
    function getCodPlacon(){
        $codcadcaixa=$this->entry_placon->get_text();
		if (empty($codcadcaixa)){
			msg('Plano de Contas nao encontrado');
			$this->entry_placon->grab_focus();
            return false;
		}
        if (!$this->retornabusca2('placon', $this->entry_placon, false, 'codigo', 'descricao')){
        		msg('Plano de Contas nao encontrado');
        		$this->entry_placon->grab_focus();
            return false;
        }
        return $codcadcaixa;    
    }
    function getValor(){
        $valor=$this->pegaNumero($this->entry_valor);
		if (empty($valor)){
			msg('Valor nao encontrado');
			$this->entry_valor->grab_focus();
            return false;
		}
        return $valor;    
    }    
    function botaoOK($operacao){
    	global $usuario;
    	if(!$valor=$this->getValor()) return;
        if(!$codcadcaixa=$this->getCodCaixa()) return;
		if(!$codplacon=$this->getCodPlacon()) return;
		if(!$this->VerificaAberturaDoCaixa($codcadcaixa,$this->datadehojeiso)) return;
		$hora=date("H:i:s");
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="select max(codigo) from $this->tabela where origem=$codcadcaixa"; // pega o ultimo codigo do caixa/movbanc
        $resultado=$con->Query($sql);
        $max=$con->FetchRow($resultado);
        $saldo=$this->retornabusca4('saldo',$this->tabela,'codigo',$max[0]);
        if($operacao=="sangria"){
        	$formamovim="S";
        	$saldo-=$valor; // subtrai
        }else{ // suprimento
        	$formamovim="E";
		$saldo+=$valor; // soma 
        }
        $obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
        $sql="INSERT INTO $this->tabela (formamovim, data, hora, valor, saldo, historico, origem, codplacon, obs) ";
		$sql.="VALUES ('$formamovim', '$this->datadehojeiso', '$hora', '$valor', '$saldo', '".strtoupper($operacao)." CAIXA $codcadcaixa USUARIO $usuario', '$codcadcaixa', '$codplacon', '$obs')";
		if(!$con->Query($sql)){ 
			msg("Erro SQL ao efetuar $operacao");
		}else{
			msg(ucwords($operacao)." de ".$this->mascara2($valor,'moeda')." feito com sucesso");
		}			
        $con->Disconnect();
        
    }    
    function cancelar(){
        $this->janela->destroy();
        //$this->janela_sangria->destroy();
        //$this->janela_sangria->hide();
        $this->janela="";
        
    }

}
?>
