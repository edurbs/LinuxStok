<?php
class contas extends funcoes {
	function cor_treeview_contas($column,$cell,$liststore,$iter){
		$hoje=date('Y-m-d',time());
		
		$data=$liststore->get_value($iter,2);
		$valor=$this->pegaNumero($liststore->get_value($iter,3));
		$saldo=$this->pegaNumero($liststore->get_value($iter,4));
		//$diff=$this->date_diff($hoje,$data);
		//echo $data." ".$diff."\n";
		if($saldo<=0){ // conta quitada
			//$cinza=new GdkColor(32767,32767,32767,0);
			$verde=new GdkColor(0,45000,0,0);    			
			$cell->set_property("foreground-gdk",$verde);
		}elseif($data>$hoje){ // conta a vencer
			$preto=new GdkColor(0,0,0,0);
			$cell->set_property("foreground-gdk",$preto);
		}elseif($data==$hoje){ // vence hoje
			$azul=new GdkColor(0,0,65534,0);
			$cell->set_property("foreground-gdk",$azul);
		}elseif($data<$hoje){ // conta atrasada
			$vermelho=new GdkColor(65534,0,0,0);
			$cell->set_property("foreground-gdk",$vermelho);
		}
	}
	
	function contas($tab,$tituloWindow,$tab2){
        // pega a variavel da tabela que sera usada.. por exemplo grpmerc
        $this->tabela=$tab;
        // tabela2 = "clientes" para contas a receber e "fornecedores" para contas a pagar
        $this->tabela2=$tab2;
        
        if($this->tabela=="receber"){
        		$this->permissaoGravar='030802';
        		$this->permissaoExcluir='030803';
        		$this->permissaoAlterar='030804'	;					        
        }elseif($this->tabela=="pagar"){
        		$this->permissaoGravar='030702';
        		$this->permissaoExcluir='030703';
        		$this->permissaoAlterar='030704'	;
        }

        $this->formamovim=strtoupper($this->tabela{0});
        if($this->formamovim=='R'){
            $this->formamovim='E';
        }elseif($this->formamovim=='P'){
            $this->formamovim='S';
        }
        // pegaentry eh uma funcao do tabelas.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry($tituloWindow);
        // cria o clist da tabela
        $this->abre_clist_contas();
    }
    function pegaentry($tituloWindow){
        $this->xml=$this->carregaGlade("contas",$tituloWindow);

		$this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;


// aba cadastro
		$this->entry_codigo=$this->xml->get_widget('entry_codigo');
		$this->entry_nfiscal=$this->xml->get_widget('entry_nfiscal');
		$this->entry_datavc=$this->xml->get_widget('entry_datavc');
        $this->entry_datavc->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_datavc->set_text($this->datadehoje);

		$this->entry_valor=$this->xml->get_widget('entry_valor');
        $this->entry_valor->connect('key-press-event', array($this,'mascaraNew'),'virgula2');
        $this->entry_valor->connect('focus-out-event', array($this,'foscusOutSaldo'));

		$this->entry_saldo=$this->xml->get_widget('entry_saldo');
		$this->entry_saldo->connect('key-press-event', array($this,'mascaraNew'),'virgula2');
		

		$this->entry_descricao=$this->xml->get_widget('entry_descricao');
		$this->entry_codorigem=$this->xml->get_widget('entry_codorigem');
        $this->frame_origem=$this->xml->get_widget('frame_origem');
        // coloca o primeiro caracter em maiusculas e bota no frame
        $this->frame_origem->set_label(ucwords($this->tabela2));
        $this->label_codorigem=$this->xml->get_widget('label_codorigem');
        $this->entry_codorigem->connect('key_press_event',
            array(&$this,entry_enter),
            'select codigo, nome, contato, dtnasc, cnpj_cpf, ie_rg from '.$this->tabela2,
            true,
            &$this->entry_codorigem,
            &$this->label_codorigem,
            &$this->tabela2,
            "nome",
            "codigo"
        );
		$this->entry_codorigem->connect_simple('focus-out-event',array(&$this,retornabusca22), $this->tabela2, &$this->entry_codorigem, &$this->label_codorigem, 'codigo', 'nome', &$this->tabela);
        $this->entry_codplacon=$this->xml->get_widget('entry_codplacon');
        $this->label_codplacon=$this->xml->get_widget('label_codplacon');
        $this->entry_codplacon->connect('key_press_event',
          array($this,entry_enter),
            'select * from placon  order by codigo',
            true,
            &$this->entry_codplacon,
            &$this->label_codplacon,
            'placon',
            "descricao",
            "codigo"
        );
		$this->entry_codplacon->connect_simple('focus-out-event',array(&$this,retornabusca22), 'placon', &$this->entry_codplacon, &$this->label_codplacon, 'codigo', 'descricao', $this->tabela);
        $this->entry_datacadastro=$this->xml->get_widget('entry_datacadastro');
        $this->entry_datacadastro->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');

        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);

		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
		$button_alterar=$this->xml->get_widget('button_alterar');

		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_gravar->set_sensitive($this->verificaPermissao($this->permissaoGravar,false));

        $button_primeiro->connect_simple('clicked', array(&$this,cadastro_primeiro), $this->tabela, $this->tabela,'codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this,cadastro_ultimo), $this->tabela, $this->tabela,'codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this,cadastro_proximo), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this,cadastro_anterior), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', confirma, array(&$this,'excluirContas'),'Tem certeza que deseja excluir esta conta e TODOS seus pagamentos (incluindo caixa, banco, etc..)?',true);
		$button_excluir->set_sensitive($this->verificaPermissao($this->permissaoExcluir,false));
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar os dados?',true);
		$button_alterar->set_sensitive($this->verificaPermissao($this->permissaoAlterar,false));
		
		$this->entry_checkdia=$this->xml->get_widget('entry_checkdia');
		//$this->entry_checkdia->connect_simple('key-release-event',array($this,'toggleContas'));
		$this->entry_checkdia->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');
		$this->entry_checkdia->set_text($this->datadehoje);

		$this->checkbutton_quitadas=$this->xml->get_widget('checkbutton_quitadas');
		$this->checkbutton_quitadas->set_active(false);		
		
		
		$this->radiobutton_atrasadas=$this->xml->get_widget('radiobutton_atrasadas');
		

		$this->radiobutton_vincendo=$this->xml->get_widget('radiobutton_vincendo');
		

		$this->radiobutton_dia=$this->xml->get_widget('radiobutton_dia');
		$this->radiobutton_dia->set_active(true);	
		
		//$this->checkbutton_quitadas->connect_simple('toggled',array($this,'toggleContas'));
		
		//$this->radiobutton_atrasadas->connect_simple_after('toggled',array($this,'toggleContas'));
		//$this->radiobutton_vincendo->connect_simple_after('toggled',array($this,'toggleContas'));
		//$this->radiobutton_dia->connect_simple_after('toggled',array($this,'toggleContas'));

		$this->checkbutton_cliente=$this->xml->get_widget('checkbutton_cliente');
		$this->checkbutton_cliente->set_label(ucwords($this->tabela2));
		//$this->checkbutton_cliente->connect_simple('toggled',array($this,'toggleContas'));
		
		$this->entry_checkcliente=$this->xml->get_widget('entry_checkcliente');
		$this->entry_checkcliente->connect('key_press_event',
            array($this,entry_enter),
            'select codigo, nome, contato, dtnasc, cnpj_cpf, ie_rg from '.$this->tabela2,
            true,
            $this->entry_checkcliente,
            false,
            $this->tabela2,
            "nome",
            "codigo"
        );
		//$this->entry_checkcliente->connect_simple('key-release-event',array($this,'toggleContas'));
		
		$this->checkbutton_placon=$this->xml->get_widget('checkbutton_placon');
		//$this->checkbutton_placon->connect_simple('toggled',array($this,'toggleContas'));
		
		$this->entry_checkplacon=$this->xml->get_widget('entry_checkplacon');
		$this->entry_checkplacon->connect('key_press_event',
          array($this,entry_enter),
            'select * from placon',
            true,
            $this->entry_checkplacon,
            false,
            'placon',
            "descricao",
            "codigo"
        );
		$this->button_atualizar_lista=$this->xml->get_widget('button_atualizar_lista');
		$this->button_atualizar_lista->connect_simple('clicked',array($this,'atualizar_lista_contas'));
		
		$this->button_totalcontas=$this->xml->get_widget('button_totalcontas');
		$this->button_totalcontas->connect_simple('clicked',array($this,'total_contas'));
		
        $this->func_novo();
        //$this->janela->show();

    }
	function total_contas(){
		// zera variaveis
		$this->total_contas=0;
		$this->saldo_contas=0;
		// soma totais da lista
		$this->liststore->foreach(array($this,'total_contasAUX'));
		// formata totais
		$this->saldo_contas=$this->mascara2($this->saldo_contas,'moeda');
		$this->total_contas=$this->mascara2($this->total_contas,'moeda');
		// mostra resultado
		msg("Valor das Contas: ".$this->total_contas."\nSaldo a receber: ".$this->saldo_contas);
		return;
	}
	
	function total_contasAUX($store, $path, $iter){
		// soma total
		$tmp=$store->get_value($iter,3);
		$this->total_contas+=$tmp;
		// soma saldo
		$tmp=$store->get_value($iter,4);
		$this->saldo_contas+=$tmp;
	}

	
	function foscusOutSaldo(){
		//$valor=$this->pegaNumero($this->entry_valor);
		//$this->entry_saldo->set_text($this->mascara2($valor,'moeda'));
	}
    function iniciaContasComDataDeHoje(){
        //$this->entry_busca_clist->set_text($this->corrigeNumero($this->datadehoje,"dataiso"));
        //$this->button_busca_clist->clicked();
    }

    function abre_clist_contas(){
    		// sql usado para lista , veja atualizar_lista_contas()
    		$this->sqlpadraoclist="SELECT c.codigo, o.nome, c.data_v, c.valor, c.saldo, c.descr,  c.fiscal, p.descricao, c.obs FROM ".$this->tabela." AS c LEFT JOIN placon AS p ON (c.codplacon = p.codigo) LEFT JOIN ".$this->tabela2." as o ON (c.codorigem=o.codigo) WHERE c.codigo>0 ";
    		
        $this->cria_clist_cadastro("$this->tabela", "data_v", "codigo", $this->entry_nfiscal, $this->tabela, $this->sqlpadraoclist." AND c.saldo>0 AND c.data_v='".$this->corrigeNumero($this->datadehoje,'dataiso')."'",true, array(true,$this->permissaoExcluir),'cor_treeview_contas');
        //echo $this->botaoatualizaVerdeVermelho;
        $this->botaoatualizaVerdeVermelho->connect_simple_after('clicked',array($this,'atualizar_lista_contas'));
        //$this->iniciaContasComDataDeHoje();
        //$this->radiobutton_dia->set_active(true);
	}
	function atualizar_lista_contas(){
		$sql=$this->sqlpadraoclist;
		if($this->checkbutton_quitadas->get_active()){
			$sql.=" AND c.saldo=0 ";
		}else{
			$sql.=" AND c.saldo<>0 ";			
		}
		$data=$this->entry_checkdia->get_text();
		if($this->radiobutton_dia->get_active() and $this->valida_data($data)){
			$sql.=" AND c.data_v='".$this->corrigeNumero($data,'dataiso')."' ";
		}elseif($this->radiobutton_atrasadas->get_active()){
			$sql.=" AND c.data_v<'".$this->corrigeNumero($this->datadehoje,'dataiso')."' ";
		}elseif($this->radiobutton_vincendo->get_active()){
			$sql.=" AND c.data_v>'".$this->corrigeNumero($this->datadehoje,'dataiso')."' ";
		}
		$cliente=$this->pegaNumero($this->entry_checkcliente);
		if($this->checkbutton_cliente->get_active() and !empty($cliente)){
			$sql.=" AND c.codorigem='".$cliente."' ";
		}
		$placon=$this->entry_checkplacon->get_text();
		if($this->checkbutton_placon->get_active() and !empty($placon)){
			$sql.=" AND c.codplacon='".$placon."' ";
		}
		//echo $sql."\n";
		$this->atualiza_clist_cadastro(null, null, false , $sql." ORDER BY c.data_v ");
		msg("Filtro aplicado na lista");
	}
    function func_novo(){
		// aba cadastro
        $this->entry_codigo->set_text('');
		$this->entry_nfiscal->set_text('');
		$this->entry_datavc->set_text($this->datadehoje);
		$this->entry_valor->set_text('');
        $this->entry_saldo->set_text('');
		$this->entry_descricao->set_text('');
		$this->entry_codorigem->set_text('');
        $this->label_codorigem->set_text('');
		$this->entry_codplacon->set_text('');
        $this->label_codplacon->set_text('');
        $this->entry_datacadastro->set_text($this->datadehoje);
        $this->textBuffer_obs->set_text('');
    }
	function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
		$nfiscal=$this->entry_nfiscal->get_text();
/*		if (empty($nfiscal)){
			msg('Nota Fiscal nao encontrada');
			$this->entry_nfiscal->grab_focus();
            return;
		}*/
		$dtvenc=$this->entry_datavc->get_text();
        if($this->valida_data($dtvenc)){
            $dtvenc=$this->corrigeNumero($dtvenc,"dataiso");
        }else{
            msg("Data de Vencimento incorreta!");
			$this->entry_datavc->grab_focus();
            return;
        }
        $valor=$this->pegaNumero($this->entry_valor);
		if(empty($valor)){
			msg('Valor nao encontrado');
			$this->entry_valor->grab_focus();
            return;
		}
        $saldo=$this->pegaNumero($this->entry_saldo);
        if($saldo>$valor){
        		msg("Saldo não pode ser maior que o valor da conta!");
        		$this->entry_saldo->grab_focus();
        		return;
        }elseif($saldo==0){
        		if(!confirma(false,"Voce deseja que o saldo seja ZERO?")){
        			$this->entry_saldo->grab_focus();
        			return;
        		}
        }
		$descricao=strtoupper($this->entry_descricao->get_text());
		if (empty($descricao)){
			msg('Descricao nao encontrada');
			$this->entry_descricao->grab_focus();
            return;
		}
        // busca na tabela fornecedores se o codigo existe
        $codorigem=$this->pegaNumero($this->entry_codorigem);
        if(!empty($codorigem)){
	        if (!$this->retornabusca2($this->tabela2, $this->entry_codorigem, $this->label_codorigem, 'codigo', 'nome', $this->tabela)){
    		        msg('Codigo em '.$this->tabela2.' nao encontrado');
    		        $this->entry_codorigem->grab_focus();
    		        return;
    		    }
    		}

		 // busca na tabela plano de contas se o codigo existe
        $codplacon=$this->entry_codplacon->get_text();
        if (!$this->retornabusca2('placon', &$this->entry_codplacon, &$this->label_codplacon, 'codigo', 'descricao', &$this->tabela)){
            msg('Plano de Contas nao encontrado');
			$this->entry_codplacon->grab_focus();
            return;
        }
        $dtcadpag=$this->entry_datacadastro->get_text();
        if($this->valida_data($dtcadpag)){
            $dtcadpag=$this->corrigeNumero($dtcadpag,"dataiso");
        }else{
            msg("Data de Cadastro incorreta!");
			$this->entry_datacadastro->grab_focus();
            return;
        }
        $obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
        $sqlarray=array(
        	array('fiscal', $nfiscal),
        	array('data_c', $dtcadpag),
        	array('data_v', $dtvenc),
        	array('valor', $valor), 
        	array('saldo', $saldo),
        	array('descr', $descricao, true),
        	array('codplacon', $codplacon),
        	array('obs',$obs, true)
        );
        if(!empty($codorigem)){
        	array_push($sqlarray, 
				array('codorigem', $codorigem)				
			);
        }
        $this->con_geral=$this->Conecta();
		if ($alterar){
			if($this->con_geral->Update($this->tabela, $sqlarray, " where codigo='$codigo'")){
                $this->status('Registro alterado com sucesso');
            }else{
                msg('Erro ao alterar registro');
                return;
            }
		} else {
			if($lastcod=$this->con_geral->Insert($this->tabela, $sqlarray)){
			    $this->status('Registro gravado com sucesso');
                $this->entry_codigo->set_text($lastcod);
            }else{
                msg('Erro ao gravar registro.');
                return;
            }
            $this->entry_saldo->set_text($this->mascara2($valor,'moeda'));
		}
		$this->con_geral->Disconnect();
		$this->decideSeAtualizaClist();
	}

	function atualiza($resultado){
        $this->func_novo();
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);

		$this->entry_codigo->set_text($resultado2["codigo"]);
		$this->entry_nfiscal->set_text($resultado2["fiscal"]);
		$this->entry_datavc->set_text($this->corrigeNumero($resultado2["data_v"],'data'));

		$this->entry_valor->set_text($this->mascara2($resultado2["valor"],'moeda'));
        // tem que usar o mascara2 em todos os entry de moeda
        $this->entry_saldo->set_text($this->mascara2($resultado2["saldo"],'moeda'));
		$this->entry_descricao->set_text($resultado2["descr"]);
		$this->entry_codorigem->set_text($resultado2["codorigem"]);

		$this->entry_codplacon->set_text($resultado2["codplacon"]);
        $this->entry_datacadastro->set_text($this->corrigeNumero($resultado2["data_c"],'data'));

        $this->textBuffer_obs->set_text($resultado2["obs"]);

        $this->retornabusca2($this->tabela2, &$this->entry_codorigem, &$this->label_codorigem, 'codigo', 'nome', &$this->tabela);
        $this->retornabusca2('placon', &$this->entry_codplacon, &$this->label_codplacon, 'codigo', 'descricao', &$this->tabela);

    }


    function excluirContas(){
		$codigo=$this->entry_codigo->get_text();
        if(empty($codigo)){
			msg("Escolha uma conta para excluir");
			return;
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        
        // verifica se existe algum pagamento me movimentos
        $sql="SELECT codigompr, codcadcaixa, tipomovim, tipodoc FROM movimentos WHERE (codmovim='$codigo' AND formamovim='$this->formamovim')";        
        $resultado=$con->Query($sql);
		$sqlexcluir="";                
        if($con->NumRows($resultado)>0){ // se existir movimentacao...
			// varre movimentos e faz sql de exclusao
			while($i = $con->FetchRow($resultado)){
				if($i[2]=="C"){ // se a movimentacao e CAIXA
					if(!$this->VerificaAberturaDoCaixa($i[1], $this->corrigeNumero($this->datadehoje,"dataiso"))){
						// se caixa nao tiver aberto
						msg('Nao foi possivel excluir porque alguns pagamentos foram feitos num caixa que esta fechado.');
						return;
					}else{
						$con->Delete("caixa", "WHERE codigompr='$i[0]'");
					}
				}elseif($i[2]=="B"){ // se movimentacao for BANCO
					$con->Delete("movbanc", "WHERE codigompr='$i[0]'");
				}
				if($i[3]=="C"){ // se for pgto em cheque
					$con->Delete("cheque", "WHERE codigompr='$i[0]'");
				}
			}
		// exclui todos movimentos
			$con->Delete("movimentos", " WHERE (codmovim='$codigo' AND formamovim='$this->formamovim')");
		}
        // excluir conta
        $con->Delete($this->tabela," WHERE codigo='$codigo'");
        $this->status('Registro excluido');
        $con->Disconnect();
        $this->decideSeAtualizaClist();
        $this->func_novo();
    }
}
?>
