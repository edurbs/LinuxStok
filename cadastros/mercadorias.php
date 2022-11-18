<?php

class mercadorias extends funcoes{
	function cor_treeview_mercadorias($column,$cell,$liststore,$iter){
		$inativa=$liststore->get_value($iter,6); // coluna 6 do campo inativa do treeview
		if($inativa=="1"){ // mercadorias esta inativa
			$cinza=new GdkColor(32767,32767,32767,0);    			
			$cell->set_property("foreground-gdk",$cinza);
		}else{
			$preto=new GdkColor(0,0,0,0);
			$cell->set_property("foreground-gdk",$preto);
		}
	}
	function mercadorias(){
        $this->xml=$this->carregaGlade("mercadorias");     
        
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        // aba foto 
        $this->pixmap_foto_mercadorias=$this->xml->get_widget('pixmap_foto_mercadorias');
        $button_busca_foto=$this->xml->get_widget('button_busca_foto');
        $button_busca_foto->connect_simple('clicked', array($this,'buscar_foto_mercadorias'),$this->pixmap_foto_mercadorias); 
        $button_limpar_foto=$this->xml->get_widget('button_limpar_foto');
        $button_limpar_foto->connect_simple('clicked', array($this,'limpar_foto_mercadorias')); 
        //$button_ver_foto=$this->xml->get_widget('button_ver_foto');
        //$button_ver_foto->connect_simple('clicked', array(&$this,'ver_foto_mercadorias')); 

        //$this->button_atualiza_clist=$this->xml->get_widget("button_atualiza_clist");

		$this->entry_codigo=$this->xml->get_widget('entry_codigo');

		$this->entry_referencia=$this->xml->get_widget('entry_referencia');
		$this->entry_codigobarras=$this->xml->get_widget('entry_codigobarras');
        $this->entry_ultimacompra=$this->xml->get_widget('entry_ultimacompra');
        $this->entry_ultimavenda=$this->xml->get_widget('entry_ultimavenda');
        $this->entry_ultimaaltera=$this->xml->get_widget('entry_ultimaaltera');
        
		$this->entry_descricao=$this->xml->get_widget('entry_descricao');       
        $this->entry_resumo=$this->xml->get_widget('entry_resumo');       
        $this->entry_unidade=$this->xml->get_widget('entry_unidade');
		$this->entry_unidade->set_max_length(2);
        
        $this->entry_peso=$this->xml->get_widget('entry_peso');
        $this->entry_peso->connect('key-press-event', array(&$this, mascaraNew),'virgula3');
        
        $this->entry_volume=$this->xml->get_widget('entry_volume');
		
		$this->entry_codfab=$this->xml->get_widget('entry_codfab');
        $this->label_codfab=$this->xml->get_widget('label_codfab');        
        $this->entry_codfab->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, nome, contato, dtnasc, dtcadastro, cnpj_cpf, ie_rg from fabricantes', 
            true,
            &$this->entry_codfab, 
            &$this->label_codfab,
            "fabricantes",
            "nome",
            "codigo"
        );
		$this->entry_codfab->connect_simple('focus-out-event',array(&$this,retornabusca22), 'fabricantes', &$this->entry_codfab, &$this->label_codfab, 'codigo', 'nome', 'mercadorias');

		$this->entry_codfor=$this->xml->get_widget('entry_codfor');
        $this->label_codfor=$this->xml->get_widget('label_codfor');
        $this->entry_codfor->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, nome, contato, dtnasc, dtcadastro, cnpj_cpf, ie_rg from fornecedores', 
            true,
            &$this->entry_codfor, 
            &$this->label_codfor,
            "fornecedores",
            "nome",
            "codigo"
        );
		$this->entry_codfor->connect_simple('focus-out-event',array(&$this,retornabusca22), 'fornecedores', &$this->entry_codfor, &$this->label_codfor, 'codigo', 'nome', 'mercadorias');

        $this->entry_codgrpmerc=$this->xml->get_widget('entry_codgrpmerc');
        $this->label_codgrpmerc=$this->xml->get_widget('label_codgrpmerc');
		$this->entry_codgrpmerc->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, descricao from grpmerc', 
            true,
            &$this->entry_codgrpmerc, 
            &$this->label_codgrpmerc,
            "grpmerc",
            "descricao",
            "codigo"
        );
        $this->entry_codgrpmerc->connect_simple('focus-out-event',array(&$this,retornabusca22), 'grpmerc', &$this->entry_codgrpmerc, &$this->label_codgrpmerc, 'codigo', 'descricao', 'mercadorias');

		$this->entry_codlocalarma=$this->xml->get_widget('entry_codlocalarma');
        $this->label_codlocalarma=$this->xml->get_widget('label_codlocalarma');
        $this->entry_codlocalarma->connect('key_press_event', 
                    array(&$this,entry_enter), 
            'select codigo, descricao from localarma', 
            true,
            &$this->entry_codlocalarma, 
            &$this->label_codlocalarma,
            "localarma",
            "descricao",
            "codigo"
        );
		$this->entry_codlocalarma->connect_simple('focus-out-event',array(&$this,retornabusca22), 'localarma', &$this->entry_codlocalarma, &$this->label_codlocalarma, 'codigo', 'descricao', 'mercadorias');
        
		$this->entry_estoqueatual=$this->xml->get_widget('entry_estoqueatual');
        $this->entry_estoqueatual->connect('key-press-event', array(&$this, mascaraNew),'virgula3');

		
        $this->entry_estoqueminimo=$this->xml->get_widget('entry_estoqueminimo');
		$this->entry_estoqueminimo->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula3');

        $this->entry_falsolucro=$this->xml->get_widget('entry_falsolucro');
		$this->entry_falsolucro->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
		$this->entry_falsolucro->connect_simple('focus-out-event', array($this, 'CalculaFalsoLucro'));
        
        $this->entry_precocusto=$this->xml->get_widget('entry_precocusto');
        $this->entry_precocusto->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula4');
        
        $this->entry_customedio=$this->xml->get_widget('entry_customedio');
        $this->entry_customedio->connect('key-press-event', array(&$this, mascaraNew),'virgula2');       

        $this->entry_precovenda=$this->xml->get_widget('entry_precovenda');
		$this->entry_precovenda->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula2');

        $this->entry_precovenda->connect_simple('focus-out-event', array(&$this, 'CalculaLucro'));
        
        $this->entry_margemlucro=$this->xml->get_widget('entry_margemlucro');
        $this->entry_margemlucro->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula2');
        
        $this->entry_margemlucro->connect_simple('focus-out-event', array(&$this, 'CalculaPrecoVenda'));
        
        // atacado
        $this->entry_precoatacado=$this->xml->get_widget('entry_precoatacado');
		$this->entry_precoatacado->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula2');
        $this->entry_precoatacado->connect_simple('focus-out-event', array(&$this, 'CalculaLucro'),true);
        
        $this->entry_falsolucroatacado=$this->xml->get_widget('entry_falsolucroatacado');
		$this->entry_falsolucroatacado->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
		$this->entry_falsolucroatacado->connect_simple('focus-out-event', array($this, 'CalculaFalsoLucro'),true);

        $this->entry_margemlucroatacado=$this->xml->get_widget('entry_margemlucroatacado');
        $this->entry_margemlucroatacado->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula2');
        $this->entry_margemlucroatacado->connect_simple('focus-out-event', array(&$this, 'CalculaPrecoVenda'),true);
        
        $this->entry_quantatacado=$this->xml->get_widget('entry_quantatacado');
		$this->entry_quantatacado->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula3');
        
// preco de promocao        
        $this->entry_promopreco=$this->xml->get_widget('entry_promopreco');
		$this->entry_promopreco->connect('key-press-event', array(&$this, 'mascaraNew'),'virgula2');

        $this->entry_promoinicio=$this->xml->get_widget('entry_promoinicio');
        $this->entry_promoinicio->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');
        $this->entry_promofim=$this->xml->get_widget('entry_promofim');
        $this->entry_promofim->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');
       
		$this->checkbutton_comissionada=$this->xml->get_widget('checkbutton_comissionada');
		
        $this->entry_comissaomaxima=$this->xml->get_widget('entry_comissaomaxima');
        $this->entry_comissaomaxima->connect('key-press-event', array($this, mascaraNew),'virgula2');
        
        $this->entry_descontomaximo=$this->xml->get_widget('entry_descontomaximo');
        $this->entry_descontomaximo->connect('key-press-event', array($this, mascaraNew),'virgula2');

        $this->entry_icms=$this->xml->get_widget('entry_icms');
        $this->entry_icms->connect('key-press-event', array($this, mascaraNew),'virgula2');
        
        $this->entry_impostoextra=$this->xml->get_widget('entry_impostoextra');
        $this->entry_impostoextra->connect('key-press-event', array($this, mascaraNew),'virgula2');
        
        $this->entry_ipi=$this->xml->get_widget('entry_ipi');
        $this->entry_ipi->connect('key-press-event', array($this, mascaraNew),'virgula2');
        
        $this->checkbutton_inativa=$this->xml->get_widget('checkbutton_inativa');
        $this->checkbutton_mostraobs=$this->xml->get_widget('checkbutton_mostraobs');

        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);


		$this->button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('010702',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('010703',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('010704',false));

		$this->button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',null);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array(&$this,cadastro_primeiro), 'mercadorias', 'mercadorias','codmerc','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this,cadastro_ultimo), 'mercadorias', 'mercadorias','codmerc','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this,cadastro_proximo), 'mercadorias', 'mercadorias','codmerc','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this,cadastro_anterior), 'mercadorias', 'mercadorias','codmerc','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this,confirma_excluir), 'mercadorias', 'mercadorias','codmerc','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar este registro?',true);
		$sql="SELECT m.codmerc, m.descricao, m.referencia, m.estoqueatual, m.precocusto, m.precovenda, m.inativa FROM mercadorias AS m ";
        $this->cria_clist_cadastro("mercadorias", "m.descricao", "codmerc", &$this->entry_descricao, "mercadorias", $sql, true, array(true,'010703'), 'cor_treeview_mercadorias',array(null,null,null,null,null,null,0));
        //$this->janela->show();
	}
    
    function buscar_foto_mercadorias($widget,$resize=null){
        $this->buscar_foto($widget,$resize,'$this->foto_mercadorias');
        //$this->buscar_foto($widget,$resize,'$this->fotoGeral');
    }
    
    function limpar_foto_mercadorias(){
        $this->foto_mercadorias="";
        $this->limpar_foto($this->pixmap_foto_mercadorias);

    }
    
	function func_novo(){
        $this->limpar_foto_mercadorias();        
		$this->entry_codigo->set_text('');
		$this->entry_descricao->set_text('');
        $this->entry_resumo->set_text('');
		$this->entry_referencia->set_text('');        
		$this->entry_codigobarras->set_text('');
        $this->entry_ultimacompra->set_text('');
        $this->entry_ultimavenda->set_text('');
        $this->entry_ultimaaltera->set_text('');
        $this->entry_codfor->set_text('');
        $this->label_codfor->set_text(' << Pressione ENTER para selecionar');
		$this->entry_codfab->set_text('');
        $this->label_codfab->set_text(' << Pressione ENTER para selecionar');
		$this->entry_codgrpmerc->set_text('');
        $this->label_codgrpmerc->set_text(' << Pressione ENTER para selecionar');
		$this->entry_codlocalarma->set_text('');
		$this->label_codlocalarma->set_text(' << Pressione ENTER para selecionar');        
		$this->entry_estoqueatual->set_text('');
		$this->entry_estoqueminimo->set_text('');
        $this->entry_falsolucro->set_text('');
		$this->entry_unidade->set_text('');
		$this->entry_precocusto->set_text('');
		$this->entry_margemlucro->set_text('');
		$this->entry_precovenda->set_text('');
		
		$this->entry_falsolucroatacado->set_text('');
		$this->entry_margemlucroatacado->set_text('');
		$this->entry_precoatacado->set_text('');
		$this->entry_quantatacado->set_text('');
		
        $this->entry_promopreco->set_text('');
        $this->entry_promoinicio->set_text('');
        $this->entry_promofim->set_text('');
        $this->entry_customedio->set_text('');
		$this->entry_ipi->set_text('');
		$this->entry_icms->set_text('');
        $this->entry_impostoextra->set_text('');
		$this->textBuffer_obs->set_text('');
        $this->entry_comissaomaxima->set_text('');
        $this->entry_descontomaximo->set_text('');
        $this->entry_peso->set_text('');
        $this->entry_volume->set_text('');
        $this->checkbutton_inativa->set_active(false);
		$this->checkbutton_comissionada->set_active(true);
		$this->checkbutton_mostraobs->set_active(true);
		
	}

	function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
        if($alterar){
            if(empty($codigo)){
            	msg("Codigo em branco");
            	return;
            }
            if(!$this->retornabusca4('codmerc', 'mercadorias','codmerc',$codigo)){
            	msg("Codigo nao encontrado. Nao eh possivel alterar codigos.");
            	return;
            }
        }
        
        $descricao=strtoupper($this->entry_descricao->get_text());
        
        if (empty($descricao)){
            msg('Preencha o campo descricao!');
            return;
        }elseif($this->ja_cadastrado('mercadorias','descricao',$descricao) and !$alterar){
            msg('Descricao de mercadoria ja cadastrada. Por favor verifique se esta mercadorias ja existe, senao mude esta descricao.');
            return;
        }
        $resumo=strtoupper($this->entry_resumo->get_text());
        $codgrpmerc=$this->entry_codgrpmerc->get_text();
        if(!empty($codgrpmerc) and !$this->retornabusca2('grpmerc', &$this->entry_codgrpmerc, &$this->label_codgrpmerc, 'codigo', 'descricao', 'mercadorias')){
            msg('Preencha corretamente o campo Grupo de Mercadoria!');
            return;
        }
        if(empty($codgrpmerc)){
			$codgrpmerc="null";
		}
        
        $codlocalarma=$this->entry_codlocalarma->get_text();
        if(!empty($codlocalarma) and !$this->retornabusca2('localarma', &$this->entry_codlocalarma, &$this->label_codlocalarma, 'codigo', 'descricao', 'mercadorias')){
            msg('Preencha corretamente o campo Local de Armazenamento!');
            return;
        }
        if(empty($codlocalarma)){
			$codlocalarma="null";
		}
        
        $codfab=$this->entry_codfab->get_text();
        if (!empty($codfab) and !$this->retornabusca2('fabricantes', &$this->entry_codfab, &$this->label_codfab, 'codigo', 'nome', 'mercadorias')){
            msg('Preencha corretamente o campo Fabricante!');
            return;
        }
        if(empty($codfab)){
			$codfab="null";
		}
        
        $codfor=$this->entry_codfor->get_text();
        if (!empty($codfor) and !$this->retornabusca2('fornecedores', &$this->entry_codfor, &$this->label_codfor, 'codigo', 'nome', 'mercadorias')){
            msg('Preencha corretamente o campo Fornecedores!');
            return;
        }
        if(empty($codfor)){
			$codfor="null";
		}

        $estoqueatual=$this->pegaNumero($this->entry_estoqueatual->get_text());

        $falsolucro=$this->pegaNumero($this->entry_falsolucro->get_text());	
        $estoqueminimo=$this->pegaNumero($this->entry_estoqueminimo->get_text());	

		$precocusto=$this->pegaNumero($this->entry_precocusto->get_text());
        $margemlucro=$this->pegaNumero($this->entry_margemlucro->get_text());
        
        $precovenda=floatval($this->pegaNumero($this->entry_precovenda->get_text()));
		if(empty($precovenda)){
			$precovenda=0;
		}
/*        if(floatval($precovenda)==0){
            msg('Preco de Venda deve ser maior que zero!');
            $this->entry_precovenda->grab_focus();
            return;
        }elseif(empty($precovenda)){
			msg('Preencha o campo Preco de Venda!');
			$this->entry_precovenda->grab_focus();
            return;
		}*/
		// preco atacado
		$precoatacado=$this->pegaNumero($this->entry_precoatacado->get_text());
		$falsolucroatacado=$this->pegaNumero($this->entry_falsolucroatacado->get_text());
		$margemlucroatacado=$this->pegaNumero($this->entry_margemlucroatacado->get_text());
		$quantatacado=$this->pegaNumero($this->entry_quantatacado->get_text());
		if($quantatacado>0 and $precoatacado==0){
			msg("Se voce desejar usar o preco de atacado voce deve especificar um preco e a quantidade minima. Se nao quiser usado o preco de atacado deixe o campo 'Quant. Minima' em branco.");
			$this->entry_quantatacado->grab_focus();
			return;
		}
		
        $customedio=$this->pegaNumero($this->entry_customedio->get_text());
        // preco promocao        
        $promopreco=$this->pegaNumero($this->entry_promopreco->get_text());
        $promoinicio=$this->entry_promoinicio->get_text();
        if(!empty($promoinicio)){
            if($this->valida_data($promoinicio)){
                $promoinicio=$this->corrigeNumero($promoinicio,"dataiso");
            }else{
                msg("Data de inicio do preco promocional incorreto!");
                return;
            }
        }else{
            $promoinicio='0001-01-01';
        }
        $promofim=$this->entry_promofim->get_text();
        if(!empty($promofim)){
            if($this->valida_data($promofim)){
                $promofim=$this->corrigeNumero($promofim,"dataiso");
            }else{
                msg("Data de fim do preco promocional incorreto!");
                return;
            }
        }else{
            $promofim='0001-01-01';
        }
        
		$referencia=strtoupper($this->entry_referencia->get_text());
		/*if(!empty($referencia) and $this->ja_cadastrado('mercadorias','referencia',$referencia, 'codmerc', $codigo)){
            msg('Referencia ja cadastrada em outra mercadoria!');
            $this->entry_referencia->grab_focus();
            return;
        }*/
        
		$codigobarras=$this->entry_codigobarras->get_text();
        if(!empty($codigobarras) and $this->ja_cadastrado('mercadorias', 'codigobarras', $codigobarras) and !$alterar){
            msg('Codigo de barras ja cadastrado em outra mercadoria!');
            $this->entry_codigobarras->grab_focus();
            return;
        }
        $unidade=strtoupper($this->entry_unidade->get_text());
		$ipi=$this->pegaNumero($this->entry_ipi);
		$icms=$this->pegaNumero($this->entry_icms);
        $impostoextra=$this->pegaNumero($this->entry_impostoextra);

		if ($this->checkbutton_comissionada->get_active()){
			$comissionada='1';
		} else { 
			$comissionada='0';
		}		
        $comissaomaxima=$this->pegaNumero($this->entry_comissaomaxima);
        $descontomaximo=$this->pegaNumero($this->entry_descontomaximo);
        $peso=$this->pegaNumero($this->entry_peso);
        $volume=$this->pegaNumero($this->entry_volume);
		
		if ($this->checkbutton_mostraobs->get_active()){
			$mostraobs='1';
		} else { 
			$mostraobs='0';
		}
		
		$obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );

        if($this->checkbutton_inativa->get_active()){
        		$inativa='1'; // true
        }else{
        		$inativa='0'; // false
        }
        
        // registra ultima alteracao

        $ultimaaltera=$this->corrigeNumero($this->datadehoje,"dataiso");

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();	
               
        if(!empty($this->foto_mercadorias)){
           $foto=$con->EscapeStringFOTO($this->foto_mercadorias);
        }

        
        $sqlarray=array(
        	array('descricao',$descricao, true),
        	array('resumo', $resumo, true),
			array('codigobarras', $codigobarras),
			array('referencia', $referencia, true), 
			array('codfab', $codfab),
			array('codgrpmerc', $codgrpmerc),
			array('codlocalarma', $codlocalarma),
			array('codfor', $codfor),
			array('customedio', $customedio),
			array('estoqueatual', $estoqueatual),
			array('estoqueminimo', $estoqueminimo),
			array('falsolucro', $falsolucro),
			array('unidade', $unidade, true),
			array('precocusto', $precocusto),
			array('margemlucro', $margemlucro),
			array('precovenda', $precovenda),
			array('ipi', $ipi),
			array('icms', $icms),
			array('impostoextra', $impostoextra),
			array('comissionada', $comissionada),
			array('comissaomaxima', $comissaomaxima),
			array('descontomaximo', $descontomaximo),
			array('peso', $peso),
			array('volume', $volume),
			array('obs', $obs, true),
			array('foto', $foto),
			array('promopreco', $promopreco),
			array('promoinicio', $promoinicio),
			array('promofim', $promofim),
			array('ultimaaltera', $ultimaaltera),
			array('inativa', $inativa),
			array('precoatacado', $precoatacado),
			array('falsolucroatacado', $falsolucroatacado),
			array('margemlucroatacado', $margemlucroatacado),
			array('quantatacado', $quantatacado),
			array('mostraobs', $mostraobs)        	
        );
        if ($alterar){
            if(!$con->Update('mercadorias', $sqlarray, "WHERE codmerc='$codigo'")){
                msg("Erro ao executar SQL");                
            }else{
                $this->status('Registro alterado com sucesso');
            }
		} else {

            if($lastcod=$con->Insert('mercadorias', $sqlarray)){
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            } else {
                msg('Erro ao gravar registro.');
            }
		}
		$con->Disconnect();
		$this->decideSeAtualizaClist();

	}


	function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;        
        $resultado2=$con->FetchArray($resultado);    
		$this->entry_codigo->set_text($resultado2["codmerc"]);
		$this->entry_descricao->set_text($resultado2["descricao"]);
        $this->entry_resumo->set_text($resultado2["resumo"]);
		$this->entry_codigobarras->set_text($resultado2["codigobarras"]);        
		$this->entry_referencia->set_text($resultado2["referencia"]);
		$this->entry_codfor->set_text($resultado2["codfor"]);
        $this->entry_codfab->set_text($resultado2["codfab"]);
		$this->entry_codgrpmerc->set_text($resultado2["codgrpmerc"]);
		$this->entry_codlocalarma->set_text($resultado2["codlocalarma"]);
		$this->entry_estoqueatual->set_text($this->corrigeNumero($resultado2["estoqueatual"],"virgula3"));
		$this->entry_estoqueminimo->set_text($this->corrigeNumero($resultado2["estoqueminimo"],"virgula3"));
        $this->entry_falsolucro->set_text($this->corrigeNumero($resultado2["falsolucro"],"virgula"));
		$this->entry_unidade->set_text($resultado2["unidade"]);
		$this->entry_precocusto->set_text($this->corrigeNumero($resultado2["precocusto"],'virgula4'));
		$this->entry_margemlucro->set_text($this->corrigeNumero($resultado2["margemlucro"],"virgula"));
        $this->entry_customedio->set_text($this->corrigeNumero($resultado2["customedio"],'virgula'));
		$this->entry_precovenda->set_text($this->corrigeNumero($resultado2["precovenda"],'virgula'));
        
        $this->entry_margemlucroatacado->set_text($this->corrigeNumero($resultado2["margemlucroatacado"],"virgula"));
        $this->entry_falsolucroatacado->set_text($this->corrigeNumero($resultado2["falsolucroatacado"],'virgula'));
		$this->entry_precoatacado->set_text($this->corrigeNumero($resultado2["precoatacado"],'virgula'));
		$this->entry_quantatacado->set_text($this->corrigeNumero($resultado2["quantatacado"],'virgula3'));        
        
        $this->entry_promopreco->set_text($this->corrigeNumero($resultado2["promopreco"],'virgula'));
        $this->entry_promoinicio->set_text($this->corrigeNumero($resultado2["promoinicio"],"data"));
        $this->entry_promofim->set_text($this->corrigeNumero($resultado2["promofim"],"data"));

		$this->entry_ipi->set_text($this->corrigeNumero($resultado2["ipi"],"virgula"));
		$this->entry_impostoextra->set_text($this->corrigeNumero($resultado2["impostoextra"],"virgula"));
        $this->entry_icms->set_text($this->corrigeNumero($resultado2["icms"],"virgula"));
        
        $this->entry_ultimavenda->set_text($this->corrigeNumero($resultado2["ultimavenda"],"data"));
        $this->entry_ultimacompra->set_text($this->corrigeNumero($resultado2["ultimacompra"],"data"));
        $this->entry_ultimaaltera->set_text($this->corrigeNumero($resultado2["ultimaaltera"],"data"));
        $this->entry_peso->set_text($this->corrigeNumero($resultado2["peso"],"virgula4"));
        $this->entry_volume->set_text($resultado2["volume"]);

		if ($resultado2["comissionada"]=='1'){
			$this->checkbutton_comissionada->set_active(true);
		} else {
			$this->checkbutton_comissionada->set_active(true);
		}
		if ($resultado2["inativa"]=='1'){
			$this->checkbutton_inativa->set_active(true);
		} else {
			$this->checkbutton_inativa->set_active(false);
		}
		if ($resultado2["mostraobs"]=='1'){
			$this->checkbutton_mostraobs->set_active(true);
		} else {
			$this->checkbutton_mostraobs->set_active(false);
		}

        $this->entry_comissaomaxima->set_text($this->corrigeNumero($resultado2["comissaomaxima"],"virgula"));
        $this->entry_descontomaximo->set_text($this->corrigeNumero($resultado2["descontomaximo"],"virgula"));

        $this->textBuffer_obs->set_text($resultado2["obs"]); 
        
        //$this->foto_mercadorias=$this->mostra_foto(&$this->pixmap_foto_mercadorias,null,false,$resultado2["foto"]);
        $this->limpar_foto_mercadorias();
        $this->mostra_foto($this->pixmap_foto_mercadorias,null,false,$con->UnEscapeStringFOTO($resultado2["foto"]),'$this->foto_mercadorias');

       
        $this->retornabusca2('fabricantes', &$this->entry_codfab, &$this->label_codfab, 'codigo', 'nome', 'mercadorias');
        $this->retornabusca2('fornecedores', &$this->entry_codfor, &$this->label_codfor, 'codigo', 'nome', 'mercadorias');
        $this->retornabusca2('grpmerc', &$this->entry_codgrpmerc, &$this->label_codgrpmerc, 'codigo', 'descricao', 'mercadorias');
        $this->retornabusca2('localarma', &$this->entry_codlocalarma, &$this->label_codlocalarma, 'codigo', 'descricao', 'mercadorias');
        
	}
 
    function CalculaPrecoVenda($atacado=false){
        if($atacado){
	        $lucro=$this->pegaNumero($this->entry_margemlucroatacado);
	    }else{
	    	$lucro=$this->pegaNumero($this->entry_margemlucro);
	    }
        
        if(!empty($lucro) and $lucro>0 and $lucro<100 ){
            $pcusto=$this->pegaNumero($this->entry_precocusto);
			if($pcusto==0){
				return;
			}
            //$pvenda=$pcusto+($pcusto*($lucro/100));
            $pvenda=$pcusto/(1-($lucro/100));  // by Eduardo A. Ewerton Perez eperez@prognum.com.br
            $pvenda=number_format($pvenda, 2, ',', '');
            if($atacado){
	            $this->entry_precoatacado->set_text($pvenda);
	        }else{
	        	$this->entry_precovenda->set_text($pvenda);
	        }
	        $this->CalculaLucro($atacado);
        }else{
        	if($atacado){
            	$this->entry_margemlucroatacado->set_text('');
            }else{
            	$this->entry_margemlucro->set_text('');
            }
        }
    }
    
    function CalculaLucro($atacado=false){
        if($atacado){
        		$pvenda=$this->pegaNumero($this->entry_precoatacado);
        	}else{
        		$pvenda=$this->pegaNumero($this->entry_precovenda);
        	}
        if($pvenda>0){
            $pcusto=$this->pegaNumero($this->entry_precocusto);
			if($pcusto==0){
				$pcusto=$pvenda;
			}
            $lucro=(($pvenda-$pcusto)/$pvenda)*100;
            $lucro=number_format($lucro, 2, ',', '');
            
			if($this->pegaNumero($this->entry_precocusto->get_text())==0 and !$atacado){
				$this->entry_precocusto->set_text(number_format($pvenda, 4,',',''));
			}
			// calcula falso lucro
			$falsolucro=(($pvenda-$pcusto)/$pcusto)*100;
			if($atacado){
				$this->entry_margemlucroatacado->set_text($lucro);
				$this->entry_falsolucroatacado->set_text(number_format($falsolucro, 2,',',''));
			}else{
				$this->entry_margemlucro->set_text($lucro);
				$this->entry_falsolucro->set_text(number_format($falsolucro, 2,',',''));
			}
        }else{
        		if($atacado){
            		$this->entry_margemlucroatacado->set_text('');
            		$this->entry_falsolucroatacado->set_text('');
            	}else{
            		$this->entry_margemlucro->set_text('');
            		$this->entry_falsolucro->set_text('');
            	}
        }
    }
    
    function CalculaFalsoLucro($atacado=false){
    		
        $pcusto=$this->pegaNumero($this->entry_precocusto);
		if($pcusto>0){
		    if($atacado){
			    $lucro=$this->entry_falsolucroatacado->get_text();
			}else{
			    $lucro=$this->entry_falsolucro->get_text();
			}
        		$lucro=$this->pegaNumero($lucro);
        		if($lucro>0){
				$pvenda=$pcusto+($pcusto/100*$lucro);
            		$pvenda=number_format($pvenda, 2, ',', '');
	            if($atacado){
	            		$this->entry_precoatacado->set_text($pvenda);
	            	}else{
	            		$this->entry_precovenda->set_text($pvenda);
	            	}
	            $this->CalculaLucro($atacado);
	        }
		}		
		return;
    }

}
?>