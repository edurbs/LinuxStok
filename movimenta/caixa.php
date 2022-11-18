<?php
class caixa extends funcoes {
	function caixa($tab,$tituloWindow){
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        
        $this->tabela=$tab;
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
        $this->pegaentry($tituloWindow);
        $this->abre_clist_contas();
    }
    
    function pegaentry($tituloWindow){
        
        $this->xml=$this->carregaGlade("caixabanc",$tituloWindow);
        
		$this->entry_codigo=$this->xml->get_widget('entry_codigo');
		
		$this->entry_codigo=$this->xml->get_widget('entry_codigo');

		$this->entry_data=$this->xml->get_widget('entry_data');
        $this->entry_data->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        
        $this->entry_numero=$this->xml->get_widget('entry_numero');
		$this->entry_valor=$this->xml->get_widget('entry_valor');
        $this->entry_valor->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
                
		$this->entry_historico=$this->xml->get_widget('entry_historico');

        $this->entry_placon=$this->xml->get_widget('entry_placon');
        $this->label_placon=$this->xml->get_widget('label_placon');
        $this->entry_placon->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select * from placon ORDER BY codigo', 
            true,
            $this->entry_placon, 
            $this->label_placon,
            'placon',
            'descricao',
            'codigo'
        );            
		$this->entry_placon->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'placon', 
            $this->entry_placon, 
            $this->label_placon, 
            'codigo', 
            'descricao' 
        );


		$this->frame_origem=$this->xml->get_widget('frame_origem');
        $this->frame_origem->set_label($this->frameorigem);
        
        $this->entry_origem=$this->xml->get_widget('entry_origem');
        $this->label_origem=$this->xml->get_widget('label_origem');
        $this->entry_origem->connect('key_press_event', 
            array(&$this,entry_enter), 
            //'select * from '.$this->tabelaorigem,
            $this->sqlorigem, 
            true,
            &$this->entry_origem, 
            &$this->label_origem,
            &$this->tabelaorigem,
            &$this->descricaoorigem,
            &$this->codigoorigem
        );            
		$this->entry_origem->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            &$this->tabelaorigem, 
            &$this->entry_origem, 
            &$this->label_origem, 
            &$this->codigoorigem, 
            &$this->descricaoorigem, 
            &$this->tabelaorigem
        );
        if($this->tabela<>"caixa"){
            $this->entry_origem->connect_simple('focus-out-event',array($this,informaBancoContas),$this->entry_origem,$this->label_origem);
        }
        
		$this->radiobutton_entrada=$this->xml->get_widget('radiobutton_entrada');
		$this->radiobutton_saida=$this->xml->get_widget('radiobutton_saida');
		$this->radiobutton_saida->set_active(true);

        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);

		$button_novo=$this->xml->get_widget('button_novo');
		if($this->tabela=="caixa") $button_novo->set_sensitive(false);
		 
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('030402',false));
        if($this->tabela=="caixa") $button_gravar->set_sensitive(false);
        
        $button_primeiro=$this->xml->get_widget('button_primeiro');
        $button_ultimo=$this->xml->get_widget('button_ultimo');
        $button_proximo=$this->xml->get_widget('button_proximo');
        $button_anterior=$this->xml->get_widget('button_anterior');
        
        $button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('030403',false));
        if($this->tabela=="caixa") $button_excluir->set_sensitive(false);
        
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('030404',false));
        if($this->tabela=="caixa") $button_alterar->set_sensitive(false);

		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
        $button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
        $button_primeiro->connect_simple('clicked', array(&$this, 'cadastro_primeiro'), $this->tabela, $this->tabela,'codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, 'cadastro_ultimo'), $this->tabela, $this->tabela,'codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, 'cadastro_proximo'), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, 'cadastro_anterior'), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, 'confirma_excluir'), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar os dados?',true);
        
        $this->func_novo();
        //$this->janela->show();
	}
    
    function abre_clist_contas(){
        if($this->tabela=="caixa"){
            $sql="SELECT c.codigo,c.numero,c.formamovim, c.data, c.valor, c.saldo, c.historico, c.origem, x.descricao, c.codplacon, placon.descricao, c.obs FROM caixa AS c LEFT JOIN placon ON placon.codigo=c.codplacon LEFT JOIN cadcaixa as x ON x.codigo=c.origem";
        }else{
            $sql="SELECT m.codigo,m.numero,m.formamovim, m.data, m.valor, m.saldo, m.historico, bancos.numero, bancos.agencia, bancos.conta, m.codplacon, placon.descricao, m.obs FROM movbanc AS m LEFT JOIN bancos ON bancos.codbanco=m.origem LEFT JOIN placon ON placon.codigo=m.codplacon";
        }
        $delkey=true;
        if($this->tabela=="caixa") $delkey=false;
        $this->cria_clist_cadastro("$this->tabela", "data", "codigo", &$this->entry_data, &$this->tabela, $sql, true, $delkey);
	}
    
	function func_novo(){
		$this->entry_codigo->set_text('');
		$this->entry_data->set_text($this->datadehoje);
		$this->entry_valor->set_text('');
		$this->entry_historico->set_text('');
		$this->entry_placon->set_text('');
		$this->label_placon->set_text('Pressione ENTER para buscar');
		$this->entry_origem->set_text('');
		$this->label_origem->set_text('Pressione ENTER para buscar');
		$this->entry_numero->set_text('');			
		$this->radiobutton_saida->set_active(true);
		$this->textBuffer_obs->set_text('');
		//$this->entry_numero->grab_focus();
	}
	
    function func_gravar($alterar){
		$codigo=$this->entry_codigo->get_text();
        if(empty($codigo) and $alterar){
          	msg('Codigo em branco');
            return;
        }
		$data=$this->entry_data->get_text();
		if($this->valida_data($data)){
            $data=$this->corrigeNumero($data,"dataiso");
        }else{
            msg("Data do Documento incorreta!");
            $this->entry_data->grab_focus();                
            return;
        }
        
        $valor=$this->pegaNumero($this->entry_valor);
		if (empty($valor)){
			msg('Valor nao encontrado');
			$this->entry_valor->grab_focus();
            return;
		}

		/*$historico=strtoupper($this->entry_historico->get_text());
		if (empty($historico)){
			msg('Historico nao encontrado');
			$this->entry_historico->grab_focus();
            return;
		}*/
		$placon=$this->entry_placon->get_text();
		if (empty($placon)){
			msg('Plano de Conta nao encontrado');
			$this->entry_placon->grab_focus();
            return;
		}
        if (!$this->retornabusca2('placon', $this->entry_placon, false, 'codigo', 'descricao')){
        		msg('Plano de Conta nao encontrado');
        		$this->entry_placon->grab_focus();
            return;
        }
        if($this->tabela=="caixa"){
        		$msg="Codigo do Caixa nao encontrado";
        }else{
        		$msg="Conta do Banco nao encontrado";
        }
		$origem=$this->entry_origem->get_text();
		if (empty($origem)){
			msg($msg);
			$this->entry_origem->grab_focus();
            return;
		}
        if (!$this->retornabusca2($this->tabelaorigem, $this->entry_origem, false, $this->codigoorigem, $this->descricaoorigem)){
        		msg($msg);
        		$this->entry_origem->grab_focus();
            return;
        }
        // bloqueia alterar/gravar se caixa tiver fechado
        if($this->tabela=="caixa" and !$this->VerificaAberturaDoCaixa($origem,$data)){
           return;
        }
		$numero=$this->entry_numero->get_text();
		/*if (empty($numero)){
			msg('Numero do Documento nao encontrado');
            return;
		}*/
		$operacao=$this->radiobutton_entrada->get_active();
		if ($operacao){
			$operacao='E';
		} else { 
			$operacao='S';
		}
		$obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
		
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
		if ($alterar){
			$sql="UPDATE $this->tabela SET formamovim='$operacao', data='$data', valor='$valor', historico='$historico', origem='$origem', numero='$numero', codplacon='$placon', obs='$obs' WHERE codigo='$codigo';";
            if ($con->Query($sql)){
                $this->status('Registro gravado com sucesso');
            }else{
                msg('Erro ao alterar');
            }
		} else {
			$sql="INSERT INTO $this->tabela (formamovim, data, valor, historico, origem, numero, codplacon, obs) ";
			$sql.="VALUES ('$operacao', '$data', '$valor', '$historico', '$origem', '$numero', '$placon', '$obs')";
            if($lastcod=$con->QueryLastCod($sql)){
                $this->status('Registro gravado com sucesso');
                $this->entry_codigo->set_text($lastcod);
            } else {
                msg('Erro ao gravar registro.');
            }
		}
		
		$con->Disconnect();
        // atualiza clist
        //$this->button_atualiza_clist->clicked();
        	$this->decideSeAtualizaClist();
	}

	function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;        
        $resultado2=$con->FetchArray($resultado);    
		$this->entry_codigo->set_text($resultado2["codigo"]);
		
		$this->entry_data->set_text($this->corrigeNumero($resultado2["data"],'data'));		
        
		$this->entry_valor->set_text($this->mascara2($resultado2["valor"],'moeda'));
		$this->entry_historico->set_text($resultado2["historico"]);
		$this->entry_placon->set_text($resultado2["codplacon"]);
		$this->retornabusca2('placon', $this->entry_placon, $this->label_placon, 'codigo', 'descricao');
		$this->entry_origem->set_text($resultado2["origem"]);
        
		$this->entry_numero->set_text($resultado2["numero"]);
		if ($resultado2["formamovim"]=='E'){
			$this->radiobutton_entrada->set_active(true);
		} else {
			$this->radiobutton_saida->set_active(true);
		}
		$this->textBuffer_obs->set_text($resultado2["obs"]);

        $this->retornabusca2(&$this->tabelaorigem, &$this->entry_origem, &$this->label_origem, &$this->codigoorigem, &$this->descricaoorigem, &$this->tabela);
        if($this->tabela<>"caixa"){
           $this->informaBancoContas($this->entry_origem,$this->label_origem);
        }
	}
	
    function informaBancoContas($entry,$label){
        $codigo=$this->DeixaSoNumero($entry->get_text());
        if(!empty($codigo)){
            
            
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();
      
            $sql="select n.sigla,b.agencia,b.conta from bancos as b left join nomebanco as n on b.numero=n.codigo where b.codbanco='$codigo'";
            $resultado=$con->Query($sql);
            $i=$con->FetchArray($resultado);
            //eval($label."->set_text('$i[0] Ag:$i[1] Cta:$i[2]');");
            $label->set_text("$i[0] Ag:$i[1] Cta:$i[2]");
            
            $con->Disconnect();
        }
    }
}
?>