<?php
class cheques extends funcoes {
    
    function cheques (){
		global $vbox_menu, $parente;
        $this->xml=$this->carregaGlade("cheques");
/*        
        $vbox1=$this->xml->get_widget('vbox1');
        $vbox1->reparent($vbox_menu);
        $this->janela->set_skip_taskbar_hint(true);
        $this->janela->set_skip_pager_hint(true);
        $this->janela->iconify();
        $parente->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );
*/
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

		$this->entry_codigo=$this->xml->get_widget('entry_codigo');
        $this->entry_situacao=$this->xml->get_widget('entry_situacao');
        $this->combo_situacao=$this->xml->get_widget('combo_situacao');

        $this->label_nomebanco=$this->xml->get_widget('label_nomebanco');
        $this->entry_numerobanco=$this->xml->get_widget('entry_numerobanco');
        $this->entry_numerobanco->connect('key_press_event', 
            array($this,entry_enter), 
            'select *  from nomebanco', 
            true,
            $this->entry_numerobanco, 
            $this->label_nomebanco,
            "nomebanco",
            "nome",
            "codigo"
        );
		$this->entry_numerobanco->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'nomebanco', 
            $this->entry_numerobanco, 
            $this->label_nomebanco, 
            'codigo', 
            'nome'
        );
        
        $this->entry_agenciabanco=$this->xml->get_widget('entry_agenciabanco');
        $this->entry_contabanco=$this->xml->get_widget('entry_contabanco');
        $this->entry_titularbanco=$this->xml->get_widget('entry_titularbanco');
		$this->entry_documento=$this->xml->get_widget('entry_documento');
        
        $this->entry_dataemissao=$this->xml->get_widget('entry_dataemissao');
        $this->entry_dataemissao->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        
        $this->entry_bompara=$this->xml->get_widget('entry_bompara');
        $this->entry_bompara->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        
        $this->entry_numero=$this->xml->get_widget('entry_numero');
        
        $this->entry_valor=$this->xml->get_widget('entry_valor');
        $this->entry_valor->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');       
        
        $this->entry_codcliente=$this->xml->get_widget('entry_codcliente');
        $this->label_codcliente=$this->xml->get_widget('label_codcliente');
        $this->entry_codcliente->connect('key_press_event', 
            array($this,entry_enter), 
            'select *  from clientes', 
            true,
            $this->entry_codcliente, 
            $this->label_codcliente,
            "clientes",
            "nome",
            "codigo"
        );
		$this->entry_codcliente->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'clientes', 
            $this->entry_codcliente, 
            $this->label_codcliente, 
            'codigo', 
            'nome', 
            'cheques'
        );
        
        $this->entry_codfornecedor=$this->xml->get_widget('entry_codfornecedor');
        $this->label_codfornecedor=$this->xml->get_widget('label_codfornecedor');
        $this->entry_codfornecedor->connect('key_press_event', 
            array($this,entry_enter), 
            'select *  from fornecedores', 
            true,
            $this->entry_codfornecedor, 
            $this->label_codfornecedor,
            "fornecedores",
            "nome",
            "codigo"
        );
		$this->entry_codfornecedor->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'fornecedores', 
            $this->entry_codfornecedor, 
            $this->label_codfornecedor, 
            'codigo', 
            'nome', 
            'cheques'
        );
        
        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);

        
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
		$button_alterar=$this->xml->get_widget('button_alterar');
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), 'cheque', 'cheque','codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), 'cheque', 'cheque', 'codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), 'cheque', 'cheque','codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), 'cheque', 'cheque','codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), 'cheque', 'cheque','codigo','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
		
        $this->sqlpadraoclist="SELECT x.codigo, x.situacao, x.codbanco, nb.nome, x.agencia, x.conta, x.titular, x.dataemissao, x.bompara, x.numero, x.valor, clientes.codigo AS codcli, clientes.nome AS cliente, fornecedores.codigo AS codfor, fornecedores.nome AS fornecedor, x.obs FROM cheque AS x LEFT JOIN clientes ON (x.codcliente=clientes.codigo) LEFT JOIN fornecedores ON (x.codfornecedor=fornecedores.codigo) LEFT JOIN nomebanco AS nb ON (nb.codigo=x.codbanco) ";
        $this->cria_clist_cadastro('cheque', "", 'codigo', $this->entry_codigo, 'cheque', $this->sqlpadraoclist." ORDER BY x.bompara ");
		
		$this->label_filtro_total=$this->xml->get_widget('label_filtro_total');
		$this->entry_filtro_origem=$this->xml->get_widget('entry_filtro_origem');
		$this->entry_filtro_origem->connect('key_press_event', 
            array($this,'entry_enter'), 
            'select *  from clientes', 
            true,
            $this->entry_filtro_origem, 
            null,
            "clientes",
            "nome",
            "codigo"
        );
		$this->entry_filtro_origem->connect_simple('focus-out-event',
            array($this,'retornabusca22'), 
            'clientes', 
            $this->entry_filtro_origem, 
            null, 
            'codigo', 
            'nome', 
            'cheques'
        );
		$this->entry_filtro_destino=$this->xml->get_widget('entry_filtro_destino');
		$this->entry_filtro_destino->connect('key_press_event', 
            array($this,'entry_enter'), 
            'select *  from fornecedores', 
            true,
            $this->entry_filtro_destino, 
            null,
            "fornecedores",
            "nome",
            "codigo"
        );
		$this->entry_filtro_destino->connect_simple('focus-out-event',
            array($this,'retornabusca22'), 
            'fornecedores', 
            $this->entry_filtro_destino, 
            null, 
            'codigo', 
            'nome', 
            'cheques'
        );
		$this->combo_filtro_situacao=$this->xml->get_widget('combo_filtro_situacao');
		$this->entry_filtro_bompara=$this->xml->get_widget('entry_filtro_bompara');
		$this->entry_filtro_bompara->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
		$this->botaoatualizaVerdeVermelho->connect_simple_after('clicked',array($this,'atualiza_filtro'));
		
		$this->atualiza_filtro();
		
		$this->func_novo();
    
    }
    
	function atualiza_filtro(){
		$this->filtra_opcoes();
		$this->total_cheques();
	}
   	function total_cheques(){
		// zera variaveis
		$this->total_cheques=0;
		// soma totais da lista
		$this->liststore->foreach(array($this,'total_chequesAUX'));
		// formata totais
		$this->total_cheques=$this->mascara2($this->total_cheques,'moeda');
		// mostra resultado
		$this->label_filtro_total->set_text($this->total_cheques);
		return;
	}
	
	function total_chequesAUX($store, $path, $iter){
		// soma total
		$tmp=$store->get_value($iter,10);
		$this->total_cheques+=$tmp;
	}
	
	function filtra_opcoes(){
		$sql=$this->sqlpadraoclist;
		
		$entry_combo=$this->combo_filtro_situacao->entry;
		$situacao=$entry_combo->get_text();
		if(!empty($situacao)){
			$sql2.=" x.situacao='$situacao' AND";
		}
		
		$destino=$this->pegaNumero($this->entry_filtro_destino);
		if(!empty($destino)){
			$sql2.=" x.codfornecedor='$destino' AND";
		}
		$origem=$this->pegaNumero($this->entry_filtro_origem);
		if(!empty($origem)){
			$sql2.=" x.codcliente='$origem' AND";
		}

		$data=$this->entry_filtro_bompara->get_text();
		if($this->valida_data($data)){
			$sql2.=" x.bompara='".$this->corrigeNumero($data,'dataiso')."' AND";
		}
		
		if(!empty($sql2)){
			$sql2=substr($sql2,0,-3); // tira ultimo AND
			$sql.=" WHERE ".$sql2;
		}
		$sql.=" ORDER BY x.bompara";

		$this->atualiza_clist_cadastro(null, null, false , $sql);
	}

    function func_novo(){    
        $this->entry_codigo->set_text('');        
        $this->entry_situacao->set_text('NOVO');        
        $this->entry_numerobanco->set_text('');
        $this->label_nomebanco->set_text('');
        $this->entry_numerobanco->set_text('');
        $this->entry_agenciabanco->set_text('');
        $this->entry_contabanco->set_text('');
        $this->entry_titularbanco->set_text('');
        $this->entry_documento->set_text('');
        $this->entry_dataemissao->set_text($this->datadehoje);
        $this->entry_bompara->set_text($this->datadehoje);
        $this->entry_numero->set_text('');
        $this->entry_valor->set_text('');
        $this->entry_codcliente->set_text('');
        $this->label_codcliente->set_text('');
        $this->entry_codfornecedor->set_text('');
        $this->label_codfornecedor->set_text('');
        $this->textBuffer_obs->set_text('');
    }
    
    function func_gravar($alterar){
        $this->codigo=$this->entry_codigo->get_text();
        if(empty($this->codigo) and $alterar){
            msg('Codigo nao informado!');
            return;
        }
        $this->situacao=$this->entry_situacao->get_text();
        $this->numerobanco=$this->entry_numerobanco->get_text();
        if(!$this->retornabusca2('nomebanco', $this->entry_numerobanco, $this->label_numerobanco, 'codigo', 'nome', 'cheque')){
            msg('Codigo do Banco nao informado!');
            return;
        }
        
        $this->dataemissao=$this->entry_dataemissao->get_text();                
        if(empty($this->dataemissao) or $this->dataemissao=="00-00-0000" or !$this->valida_data($this->dataemissao)){
            msg("Data de emissao incorreta!");
            $this->entry_dataemissao->grab_focus();
            return;            
        }else{            
            $this->dataemissao=$this->corrigeNumero(&$this->dataemissao,"dataiso");
        }
        
        $this->bompara=$this->entry_bompara->get_text();
        if(empty($this->bompara) or $this->bompara=="00-00-0000" or !$this->valida_data($this->bompara)){
            msg("Data de 'Bom Para' incorreta!");
            $this->entry_bompara->grab_focus();
            return;            
        }else{            
            $this->bompara=$this->corrigeNumero(&$this->bompara,"dataiso");
        }
        
        $this->numero=$this->entry_numero->get_text();
        $this->valor=$this->pegaNumero($this->entry_valor);
        if($this->valor==0){
            msg("Preencha o campo valor!");
            $this->entry_valor->grab_focus();
            return;
        }
        $this->codcliente=$this->entry_codcliente->get_text();        
        if(!empty($this->codcliente)){
            if(!$this->retornabusca2('clientes', &$this->entry_codcliente, &$this->label_codcliente, 'codigo', 'nome', 'cheque')){
                msg('Codigo do Clientes incorreto!');
                return;
            }
        }else{
            $this->codcliente='0';
        }
        $this->codfornecedor=$this->entry_codfornecedor->get_text();
        if(!empty($this->codfornecedor)){
            if(!$this->retornabusca2('fornecedores', &$this->entry_codfornecedor, &$this->label_codfornecedor, 'codigo', 'nome', 'cheque')){
                msg('Codigo do Fornecedor incorreto!');
                return;
            }
        }else{
            $this->codfornecedor='0';
        }
        $this->obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
        $this->agencia=$this->entry_agenciabanco->get_text();
        $this->conta=$this->entry_contabanco->get_text();
        $this->titular=$this->entry_titularbanco->get_text();
        $this->documento=$this->entry_documento->get_text();
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="UPDATE cheque SET situacao='$this->situacao', codbanco='$this->numerobanco', dataemissao='$this->dataemissao', bompara='$this->bompara', numero='$this->numero', valor='$this->valor', agencia='$this->agencia', conta='$this->conta', titular='$this->titular', documento='$this->documento', ";
            if(!empty($this->codfornecedor)){
                $sql.="codcliente='$this->codcliente',  ";
            }
            if(!empty($this->codfornecedor)){
                $sql.="codfornecedor='$this->codfornecedor', ";
            }
            $sql.="obs='$this->obs' WHERE codigo='$this->codigo'";
            if(!$con->Query($sql)){
                msg("Erro ao executar SQL");
            }else{
                $this->status('Registro alterado com sucesso');
            }
        } else {
            $sql="INSERT INTO cheque (situacao, codbanco, dataemissao, bompara, numero, valor, agencia, conta, titular, documento, ";
            if(!empty($this->codcliente)){
                $sql.="codcliente,";
            }
            if(!empty($this->codfornecedor)){
                $sql.="codfornecedor,";
            }
            $sql.=" obs) ";
            $sql.="VALUES ('$this->situacao', '$this->numerobanco', '$this->dataemissao', '$this->bompara', '$this->numero', '$this->valor', '$this->agencia', '$this->conta', '$this->titular', '$this->documento', ";
            if(!empty($this->codcliente)){
                $sql.="'$this->codcliente',";
            }
            if(!empty($this->codfornecedor)){
                $sql.="'$this->codfornecedor',";
            }
            $sql.=" '$this->obs')";
            
            //if (!$lastcod=$con->QueryLastCod($sql)){
            if ($con->Query($sql)){            		
                $this->status('Registro gravado com sucesso');
            }else{
            		msg("Erro ao executar SQL");                
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
        $this->entry_situacao->set_text($resultado2["situacao"]);
        $this->entry_numerobanco->set_text($resultado2["codbanco"]);
        $this->retornabusca2('nomebanco', $this->entry_numerobanco, $this->label_nomebanco, 'codigo', 'nome');
        $this->entry_agenciabanco->set_text($resultado2["agencia"]);
        $this->entry_contabanco->set_text($resultado2["conta"]);
        $this->entry_titularbanco->set_text($resultado2["titular"]);
        $this->entry_documento->set_text($resultado2["documento"]);
  
        $this->entry_dataemissao->set_text($this->corrigeNumero($resultado2["dataemissao"],"data"));
        $this->entry_bompara->set_text($this->corrigeNumero($resultado2["bompara"],"data"));
        $this->entry_numero->set_text($resultado2["numero"]);
        $this->entry_valor->set_text($this->mascara2($resultado2["valor"],'moeda'));
        $this->entry_codcliente->set_text($resultado2["codcliente"]);
        $this->retornabusca2('clientes', $this->entry_codcliente, $this->label_codcliente, 'codigo', 'nome');
        $this->entry_codfornecedor->set_text($resultado2["codfornecedor"]);
        $this->retornabusca2('fornecedores', $this->entry_codfornecedor, $this->label_codfornecedor, 'codigo', 'nome');
        $this->textBuffer_obs->set_text($resultado2["obs"]);
    }

}
?>