<?php
include_once('movimenta'.bar.'pdv_extras.php');
include_once('movimenta'.bar.'pdv_fim.php');

class pdv extends pdv_fim {
	function pdv($moduloPDV="desativado"){	    

	    $this->CriaProgressBar("Iniciando Vendas...");
        
        $this->xml=$this->carregaGlade('vendas');


        $fonte20= new PangoFontDescription;
        $fonte20->set_size(20000);
        $fonte10= new PangoFontDescription;
        $fonte10->set_size(10000);
        $fonte12= new PangoFontDescription;
        $fonte12->set_size(12000);
        // red green blue
        $azul=new GdkColor(0,0,65535,0);
        $vermelho=new GdkColor(65535,0,0,0);
        $verde= new GdkColor(0,25535,0,0);

		$this->entry_codmerc=$this->xml->get_widget("entry_codmerc");

        $this->scrolledwindow_venda=$this->xml->get_widget('scrolledwindow_venda');
        $this->liststore_venda=new GtkListStore(
            Gobject::TYPE_STRING, //0 codigo
            Gobject::TYPE_STRING, //1 descricao
            Gobject::TYPE_STRING, //2 quant
            Gobject::TYPE_STRING, //3 un
            Gobject::TYPE_STRING, //4 preco unit
            Gobject::TYPE_STRING, //5 preco com desconto
            Gobject::TYPE_STRING  //6 Total
        );
        $this->treeview_venda=new GtkTreeView($this->liststore_venda);
		$this->treeview_venda->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
            array('Cod.', 'Descricao', 'Qt', 'UN', 'Preco Orig.', 'Preco Unit', 'Preco Total'),
            $this->treeview_venda,
            array(50, 300, 50, 30, 0, 100, 100),
            $verde,
            "bold 11"
        );
        $this->scrolledwindow_venda->add($this->treeview_venda);
        $this->scrolledwindow_venda->show_all();        
        $this->treeview_venda->connect('key-press-event', array($this,'clistVendaKey'));

		
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        $this->entry_quantidade=$this->xml->get_widget("entry_quantidade");
        $this->entry_quantidade->connect("key-press-event", array($this,"quantidade"));
        $this->entry_quantidade->connect_simple_after("activate", array($this,"incluir"));
        $this->entry_quantidade->connect("key-press-event", array($this, "intro_keypressed"),&$this->entry_codmerc);
        $this->entry_quantidade->connect_simple('focus-in-event',array($this,'setLabel_cliente'));
        $this->entry_quantidade->set_text('');
        $this->entry_quantidade->modify_text(0,$azul);
        $this->entry_quantidade->modify_font($fonte20);

		$this->button_cadcliente=$this->xml->get_widget("button_cadcliente");
        $this->button_cadcliente->connect_simple('clicked', array($this,'cadcliente'));
        
        $this->button_entrega=$this->xml->get_widget("button_entrega");
        $this->button_entrega->connect_simple('clicked', array($this,'chamaEntrega'));
		
		$this->button_viarecibo=$this->xml->get_widget("button_viarecibo");
        $this->button_viarecibo->connect_simple('clicked',array($this, 'viarecibo'));
        
        $this->button_vende=$this->xml->get_widget("button_vende");
        $this->button_vende->connect_simple('clicked',array($this, 'vende'));

        $this->buttonHistorico=$this->xml->get_widget("buttonHistorico");
        $this->buttonHistorico->connect_simple('clicked',array(&$this, 'mostraHistorico'));

        $this->button_limpa=$this->xml->get_widget("button_limpa");
        $this->button_limpa->connect_simple('clicked','confirma', array(&$this, 'limpa'),"Deseja limpar os campos da tela?",null);
    		$this->entry_codcli=new GtkEntry();
        $this->entry_codcli->connect_simple('changed', array($this,'eventoCodCli'));
        
        $this->label_cliente=$this->xml->get_widget("label_cliente");
        $this->label_cliente->modify_font($fonte10);

        $this->button_endereco=$this->xml->get_widget("button_endereco");
        $this->button_endereco->connect_simple('clicked',array($this, 'buscaendereco'));
        
        $this->button_cliente=$this->xml->get_widget("button_cliente");
        $this->button_cliente->connect_simple('clicked',
            array($this,'buscatab'),
            "SELECT codigo, nome FROM clientes WHERE (habcomprar='Liberado' OR habcomprar='Somente a vista') AND inativo<>'1' ",
            true,
            $this->entry_codcli,
            //$this->button_endereco,
            null,
            "clientes",
            "nome",
            "codigo"
        );

        $this->button_codvendedor=$this->xml->get_widget("button_codvendedor");
        $this->button_codvendedor->connect_simple('clicked', array($this,'trocausuario'));

        $this->button_descunit=$this->xml->get_widget("button_descunit");
        $this->button_descunit->connect_simple('clicked',array($this, 'desconto'),'U');
        $this->button_descunit->set_sensitive($this->verificaPermissao('030104',false));

        $this->entry_codorca=new GtkEntry();
        $this->entry_codorca->connect_simple('changed', array($this,'eventoOrca'));

		$this->entry_codvenda=new GtkEntry();
        $this->entry_codvenda->connect_simple('changed', array($this,'eventoBuscaVenda'));

        $this->button_buscavendaorca=$this->xml->get_widget("button_buscavendaorca");
        $this->button_buscavendaorca->connect_simple('clicked', array($this,'buscaVendaOrca'));

        $this->button_incluir=$this->xml->get_widget("button_incluir");
        $this->button_incluir->connect_simple('clicked',array($this, 'incluir'));
        $this->button_excluir=$this->xml->get_widget("button_excluir");
        $this->button_excluir->connect_simple('clicked',array($this, 'excluir'));

        $this->label_endereco=$this->xml->get_widget("label_endereco");
        $this->botao_endereco_doido=new GtkButton();
        $this->botao_endereco_doido->connect_simple('clicked',array($this,'eventoEndereco'));
        $this->label_codendereco=new GtkEntry();
        $this->label_codendereco->add_events(1);
        
        $this->label_unidade=$this->xml->get_widget("label_unidade");

        $this->entry_codmerc->modify_text(0,$azul);
        $this->entry_codmerc->modify_font($fonte20);

        $this->label_codmerc=$this->xml->get_widget("label_codmerc");
        $this->button_buscamerc=$this->xml->get_widget("button_buscamerc");
        $this->button_codmercdoido=new GtkButton();
        $this->button_codmercdoido->connect_simple('clicked',array($this,'codmerc_doido'));
		$this->button_buscamerc->connect_simple('clicked', array($this,'chamabuscatabBuscamerc'));
		$this->entry_codmerc->connect_simple_after("activate", array($this,"incluir"));			
        //$this->entry_codmerc->connect("key-press-event", array(&$this, "intro_keypressed"),&$this->entry_quantidade);
        $this->entry_codmerc->connect("key-press-event", array(&$this, "intro_keypressed"),&$this->entry_listamercadorias);
		$this->entry_codmerc->connect_simple('focus-out-event',array($this,'preco'));
        $this->entry_codmerc->connect_simple('key-press-event',array($this,'preco'),true);
        $this->entry_codmerc->connect_simple('focus-in-event',array($this,'setLabel_codmerc'));
        $this->entry_codmerc->connect_simple('focus-in-event',array($this,'setLabel_cliente'));
        $this->entry_codmerc->connect_simple('focus-in-event',array($this,'setEntryListaMercadorias'));
        //$this->entry_codmerc->grab_focus();
        
        // botao para atualiza mercadorias
        $this->button_atualizalistamercadorias=$this->xml->get_widget("button_atualizalistamercadorias");
        $this->button_atualizalistamercadorias->connect_simple('clicked',array($this,'atualizalistamercadorias'));
        
        // entry que digita e aparece a lista de mercadorias
        $this->entry_listamercadorias=$this->xml->get_widget("entry_listamercadorias");
        $this->entry_listamercadorias->grab_focus();
        $this->entry_listamercadorias->connect("key-press-event", array($this, "intro_keypressed"),$this->entry_quantidade);
        $this->entry_listamercadorias->connect_simple('focus-in-event',array($this,'setEntryListaMercadorias'),'',true);
        $this->entry_listamercadorias->connect_simple('focus-out-event',array($this,'preco'));
    	$this->entry_listamercadorias->modify_text(0,$azul);
        $this->entry_listamercadorias->modify_font($fonte20);
    		

        $this->label_estoque=$this->xml->get_widget("label_estoque");
        $this->frame_estoque=$this->xml->get_widget("frame_estoque");

        $this->label_precounitario=$this->xml->get_widget("label_precounitario");

        $this->label_precototal=$this->xml->get_widget("label_precototal");

        $this->label_precototalvenda=$this->xml->get_widget("label_precototalvenda");
        $this->totalDaVenda=0;
        $this->setLabel_precototalvenda();
        $this->limpa();
        //$this->entry_quantidade->grab_focus();
        
        $this->radiobutton_tipocod=$this->xml->get_widget("radiobutton_tipocod");
        $this->radiobutton_tiporef=$this->xml->get_widget("radiobutton_tiporef");
		$this->radiobutton_tipobarras=$this->xml->get_widget("radiobutton_tipobarras");
		$this->radiobutton_tipocod->connect_simple('toggled',array($this,'preco'));
		$this->radiobutton_tiporef->connect_simple('toggled',array($this,'preco'));
		$this->radiobutton_tipobarras->connect_simple('toggled',array($this,'preco'));
		
		$this->radiobutton_precovenda=$this->xml->get_widget("radiobutton_precovenda");
        $this->radiobutton_precopromocao=$this->xml->get_widget("radiobutton_precopromocao");
		$this->radiobutton_precoatacado=$this->xml->get_widget("radiobutton_precoatacado");

		$this->checkbutton_pvendaautomatico=$this->xml->get_widget("checkbutton_pvendaautomatico");
		$this->checkbutton_pvendaautomatico->connect('toggled',array($this,'sinal_radio_preco'));
		$this->checkbutton_pvendaautomatico->connect_simple_after('toggled',array($this,'preco'));
		$this->checkbutton_pvendaautomatico->set_active(TRUE);
		
		$this->button_cancelavenda=$this->xml->get_widget("button_cancelavenda");
		//$this->button_cancelavenda->connect_simple('clicked', 'confirma', array($this,'cancelaVendaSimples'),"Deseja realmente cancelar uma venda?");
		$this->button_cancelavenda->connect_simple('clicked', array($this, 'cancela_devolve_pdv'));
        

        $this->AtualizaProgressBar("Lista de Mercadorias",10);     
        $this->ListaMercadorias();
        $this->AtualizaProgressBar("Busca de Mercadorias",50);
    	$this->buscatabBuscamerc();
    	$this->AtualizaProgressBar("Pronto",100);
    		
    	//$this->janela->show();
    	$this->button_fechar_vendas=$this->xml->get_widget("button_fechar_vendas");
        
        $this->moduloPDV=$moduloPDV;
    	if($moduloPDV=="ativado"){ 
    		$this->ativar_modulo_pdv();
    	}else{
    		$this->desativar_modulo_pdv();
    	}
		
        $this->FechaProgressBar();
    		
	}
	function ativar_modulo_pdv(){
		$this->janela->fullscreen();
		$this->janela->show_all();
		$this->janela->connect_simple('destroy', array('gtk','main_quit'));
		$this->button_fechar_vendas->connect_simple('clicked', array('gtk','main_quit'));
	}
	function desativar_modulo_pdv(){		
		global $atalho_padrao;
		
		// desativa botao para trocar de vendedor. Use logoff
		$this->button_codvendedor->set_sensitive(FALSE);
		
		//$this->button_fechar_vendas->connect_simple('clicked', array($this,'fecha_janela'));
		$this->button_fechar_vendas->hide();
		// ativar atalhos para o teclado visto que sao pedidos ao usar a funcao reparent
		
    	// atalho buscar mercadoria F1    	 
    	$this->button_buscamerc->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F1, 	0, Gtk::ACCEL_VISIBLE);	
   	 	// cadastra cliente
    	$this->button_cadcliente->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F8, 	0, Gtk::ACCEL_VISIBLE);
    	// vender
    	$this->button_vende->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F12, 	0, Gtk::ACCEL_VISIBLE);
    	// historico
    	$this->buttonHistorico->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F6, 	0, Gtk::ACCEL_VISIBLE);
    	// limpar tela
    	$this->button_limpa->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F7, 	0, Gtk::ACCEL_VISIBLE);
    	// endereco
    	$this->button_endereco->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F3, 	0, Gtk::ACCEL_VISIBLE);
    	// busca cliente
    	$this->button_cliente->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F2, 	0, Gtk::ACCEL_VISIBLE);
    	// vendedor/usuario
    	$this->button_codvendedor->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F11, 	0, Gtk::ACCEL_VISIBLE);
    	// desconto unitario
    	$this->button_descunit->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F4, 	0, Gtk::ACCEL_VISIBLE);
    	// busca venda/orcamento
    	$this->button_buscavendaorca->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F10, 0, Gtk::ACCEL_VISIBLE);
    	// cancela venda
    	$this->button_cancelavenda->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F5, 	0, Gtk::ACCEL_VISIBLE);

	}
	
	function atualizalistamercadorias(){
		$this->ListaMercadorias(true);
	}
	
	function ListaMercadorias($progress=false){
	    $completion = new GtkEntryCompletion();
	    $completion->connect('match-selected',array($this,'OnSelectListaMercadorias'));
        $completion_model = $this->__create_completion_model_lista_mercadorias($progress);
        $completion->set_model($completion_model);
        $completion->set_text_column(1);
        $this->entry_listamercadorias->set_completion($completion);
	}
	function OnSelectListaMercadorias($completion, $model, $iter){		
		$codmerc=$model->get_value($iter,0);
		$this->entry_codmerc->set_text($codmerc);
		$this->entry_quantidade->grab_focus();
	}
	
	function __create_completion_model_lista_mercadorias($progress){
		// codmerc descricao precovenda unidade estoqueatual nome.fornecedor obs
		$store = new GtkListStore(
			Gobject::TYPE_STRING,
			Gobject::TYPE_STRING
		);
		
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT m.codmerc, m.descricao FROM mercadorias AS m WHERE m.inativa='0' ORDER BY m.descricao";                
        $resultado=$con->Query($sql);
        if($progress){
        		$this->CriaProgressBar("Atualizando...");
        		$total=$con->NumRows($resultado);
        		$j=0;
        }
        
        while($i=$con->FetchRow($resultado)){
            // adiciona a cidade
            $iter = $store->append();
        		$store->set($iter, 0, $i[0]);
        		$store->set($iter, 1, $i[1]);
			if($progress){
				$j++;
				$atual=(100*$j)/$total;
                if($atual%5==0){
					$this->AtualizaProgressBar(null,$atual);
				}
			}
        }
        if($progress){
        		$this->FechaProgressBar();
        	}
        $con->Disconnect();        
        return $store;
	}
	
	    
    function cancelaVendaSimples(){
		$codsaidas=inputdialog("Digite o codigo da Venda a Cancelar");
		if(empty($codsaidas)){
			msg("Cancelamento nao realizado!");
			return;
		}		
		$obs_cancela=inputdialog("Digite o motivo do cancelamento",null,true);
		if(empty($obs_cancela)){
			msg("Digite a observacao para cancelar a venda!");
			return;
		}
		if(!$this->retornabusca4('codsaidas','saidas',"codsaidas",$codsaidas)){
			msg("Venda nao encontrada.");
			return;
		}
		if($this->retornabusca4('codsaidas','devolucoes','codsaidas',$codsaidas)){
			msg('Esta venda possui registro em devolucoes. Impossivel cancelar. Use o modulo DEVOLUCOES');
			return;
		}
		if($this->retornabusca4('codsaidas','entrega_itens','codsaidas',$codsaidas)){
			msg('Esta venda possui registro em entregas. Impossivel cancelar. Use o modulo DEVOLUCOES');
			return;
		}
		 
		// se for venda futura, ou seja, para baixar estoque ao receber
		$futura=$this->retornabusca4('futura','saidas','codsaidas',$codsaidas);
		
		$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
		//cancela venda no pdv? delete receber, entsai, saidas.. movimento codmovim=receber.codigo formamovim=E
		//if($this->retorna_OPCAO('integraestoquefinanceiro')){
		if($codreceber=$this->retornabusca4('codsaidas','receber','codsaidas',$codsaidas)){
			// se tiver movimentacao em contas a receber
			$sql="SELECT codigompr FROM movimentos WHERE codmovim='$codreceber' AND formamovim='E'";
			$resultado=$con->Query($sql);
			if($con->NumRows($resultado)>0){
				msg('Existe movimentacao de pagamentos desta venda. Impossivel cancelar. Use o modulo DEVOLUCOES.');
				$con->Disconnect();
				return;
			}else{
    			// exclui contas a receber
				$sql="DELETE FROM receber WHERE codsaidas='$codsaidas';";
				if(!$con->Query($sql)){
					msg('Erro ao excluir contas a receber');
					return;
				}
			}
		}
		// se for venda futura ($futura=1), nao volta ao estoque, visto que nao baixou do estoque
		if($futura==0){ // se nao for venda futura, i.e, baixou estoque, agora tem que voltar ao estoque
			$sql="SELECT codmerc, quantidade, entregue FROM entsai WHERE codentsai='$codsaidas' AND tipo='S';";
			$resultado=$con->Query($sql);
			
			while($i = $con->FetchRow($resultado)){
				$codmerc=$i[0];			
				$quantidade=$i[1];
				$estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);
				$novaquant=$estoqueatual+$quantidade; // devolve no estoque
				$sqlQ="UPDATE mercadorias SET estoqueatual='$novaquant' WHERE codmerc='$codmerc' ";
				if(!$con->Query($sqlQ)){
	    			msg("Erro ao update em mercadorias"); // ****************
	    			return;
	    		}
			}
		}
		
		$sql="UPDATE movpagamentos SET tipo='C' WHERE codorigem='$codsaidas' AND tipo='S' ";
		if(!$con->Query($sql)){
		 	msg("Erro ao update no movpagamentos"); 
		 	return;	
		}
		
		$sql="UPDATE entsai SET tipo='C' WHERE codentsai='$codsaidas' AND tipo='S' ";
		if(!$con->Query($sql)){
		 	msg("Erro ao update no entsai"); 
		 	return;	
		}
		$sql="UPDATE saidas SET totalmerc='0', desconto='0', totalnf='0', obs='$obs_cancela' WHERE codsaidas='$codsaidas' ";
		if(!$con->Query($sql)){
			msg("Erro ao excluir em saidas");
			return;
		} 
		msg("Venda $codsaidas cancelada com sucesso!");
   		$con->Disconnect();
    }
    
    function intro_keypressedPDV($widget, $event, $proximo) {
        //if($event->keyval==GDK::KEY_Return or $event->keyval==GDK::KEY_KP_Enter){
        if($event->keyval==65293 or $event->keyval==65421){
            if($proximo<>'button_sair'){
                $this->trocausuarioEntrar();
            }
        }
    }
	function cancela_devolve_pdv(){
		$this->cancelar_cancela_devolve_pdv();
		$this->xml=$this->carregaGlade("buscavendaorca",false,false,false,false);
		$this->radiobutton_cancela=$this->xml->get_widget('radiobutton_venda');
		$this->radiobutton_cancela->set_label("Cancelar");
		$this->radiobutton_devolve=$this->xml->get_widget('radiobutton_orca');
		$this->radiobutton_devolve->set_label("Devolver");
		$this->window_cancela_devolve_pdv=$this->xml->get_widget('window1');
		$this->button_ok_cancela_devolve_pdv=$this->xml->get_widget('button_ok');
		$this->button_cancelar_cancela_devolve_pdv=$this->xml->get_widget('button_cancelar');
		$this->button_ok_cancela_devolve_pdv->connect_simple('clicked',array($this,'ok_cancela_devolve_pdv'));
		$this->button_cancelar_cancela_devolve_pdv->connect_simple('clicked',array($this,'cancelar_cancela_devolve_pdv'));		
		$this->window_cancela_devolve_pdv->show_all();
		$this->window_cancela_devolve_pdv->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
		
	}
	function ok_cancela_devolve_pdv(){
		$this->cancelar_cancela_devolve_pdv();
		if($this->radiobutton_cancela->get_active()){
			$this->cancelarVendaSimples_cancela_devolve_pdv();
		}else{
			$this->devolverVenda_cancela_devolve_pdv();
		}		
	}
	function cancelar_cancela_devolve_pdv(){
		if($this->window_cancela_devolve_pdv) $this->window_cancela_devolve_pdv->destroy();
	}
	function cancelarVendaSimples_cancela_devolve_pdv(){
		$this->cancelaVendaSimples();
	}
	function devolverVenda_cancela_devolve_pdv(){
		include_once('movimenta'.bar.'devolucao.php');
		if($this->nova_devolucao){			
			$this->nova_devolucao->janela->destroy();	
		}	
		$this->nova_devolucao=new devolucao();		
		$this->nova_devolucao->janela->show();
		
	}
	
	function viarecibo(){
		$this->cancelarTVR();
		$this->xml=$this->carregaGlade("buscavendaorca",false,false,false,false);
		$this->radiobutton_vendaTVR=$this->xml->get_widget('radiobutton_venda');
		$this->radiobutton_orcaTVR=$this->xml->get_widget('radiobutton_orca');
		$this->window1TVR=$this->xml->get_widget('window1');
		$this->button_okTVR=$this->xml->get_widget('button_ok');
		$this->button_cancelarTVR=$this->xml->get_widget('button_cancelar');
		$this->button_okTVR->connect_simple('clicked',array($this,'okTVR'));
		$this->button_cancelarTVR->connect_simple('clicked',array($this,'cancelarTVR'));
		$this->window1TVR->show_all();				
	}
	function okTVR(){
		if($this->radiobutton_vendaTVR->get_active()){
			$tabela="saidas";
		}else{
			$tabela="orcamento";
		}		
		$this->cancelarTVR();
		
		if($codigo=inputdialog("Digite o codigo")){		
			if($achou=$this->retornabusca4('cod'.$tabela,$tabela,"cod".$tabela,$codigo)){
				$this->ConfirmaImprimirRecibo($codigo,$tabela);
			}else{
				msg("Movimentacao nao encontrada.");
			}
		}

	}
	function cancelarTVR(){
		if($this->window1TVR) $this->window1TVR->destroy();
	}
	
	function codmerc_doido(){
		$this->preco();
        $this->setLabel_codmerc();
        $this->setEntryListaMercadorias();
        $this->setLabel_cliente();
        $this->entry_quantidade->grab_focus();
	}
	function cadcliente(){
		if($this->verificaPermissao('010101',false)){
			include_once('cadastros'.bar.'geral.php');
			$novocliente=new geral('clientes','Cadastro de Clientes (tela de vendas)');
			$novocliente->janela->show();
		}
	}
	function chamaEntrega(){
		if($this->verificaPermissao('',false)){
			include_once('movimenta'.bar.'entrega.php');
			$entrega=new entrega;
			$entrega->janela->show();
		}
	}

	function setLabel_cliente($texto=null,$retorna=true){
		if($retorna) Gtk::timeout_add(100,array($this,'setLabel_cliente'),$texto,false);
		if(empty($texto)){
			$texto=$this->label_cliente->get_text();
		}
		$this->label_cliente->set_markup(
			'<span foreground="red" size="12000">'.
			$texto
			.'</span>'
		);
	}
	function setLabel_codendereco($texto=null,$retorna=true){
		if($retorna) Gtk::timeout_add(200,array($this,'setLabel_codendereco'),$texto,false);
		if(empty($texto)){
			$texto=$this->label_endereco->get_text();
		}
		$this->label_endereco->set_markup(
			'<span foreground="red" size="12000">'.
			$texto
			.'</span>'
		);
	}

	function setLabel_precototalvenda(){
		$tmp=number_format($this->totalDaVenda, 2, ',', '');
		$this->label_precototalvenda->set_markup(
			'<span foreground="blue" size="20000">'.
			$tmp
			.'</span>'
		);
	}
	function setLabel_codmerc($codmerc=null){
		if(empty($codmerc)) $codmerc=$this->label_codmerc->get_text();
        $this->label_codmerc->set_markup(
			'<span foreground="blue" size="15000">'.
			$codmerc
			.'</span>'
		);
	}
	function setEntryListaMercadorias($codmerc=null,$limpa=false){
		/*if($codmerc=="eitaTira-01234567980123456798"){
			$codmerc="";
		}else*/
		if(empty($codmerc)){
			$codmerc=$this->label_codmerc->get_text();
		}
        //$this->entry_listamercadorias->set_text($codmerc);
        if($limpa) $this->entry_listamercadorias->set_text('');
	}

	function setLabel_unidade($unidade=null){
		if(empty($unidade)) $unidade=$this->label_unidade->get_text();
        $this->label_unidade->set_markup(
			'<span foreground="blue" size="15000">'.
			$unidade
			.'</span>'
		);
	}

	function setLabel_estoque($estoqueatual,$codmerc){
        // retorna o estoqueminimo da mercadoria
        $estoqueminimo=$this->retornabusca4('estoqueminimo','mercadorias','codmerc',$codmerc);
        if($estoqueatual<$estoqueminimo){
            // vermelho
            $this->label_estoque->set_markup(
				'<span foreground="red" size="20000">'.
				intval($estoqueatual)
				.'</span>'
			);
        }else{
            // azul
            $this->label_estoque->set_markup(
				'<span foreground="blue" size="20000">'.
				intval($estoqueatual)
				.'</span>'
			);
        }
	}

 	function setLabel_precounitario($preco){
 		$this->label_precounitario->set_markup(
			'<span foreground="blue" size="20000">'.
			$preco
			.'</span>'
		);
 	}

 	function setLabel_precototal($preco){
 		$this->label_precototal->set_markup(
			'<span foreground="blue" size="20000">'.
			$preco
			.'</span>'
		);
 	}
	function verificaPrecoPromocao($codmerc){
	            $promoinicio=$this->retornabusca4('promoinicio','mercadorias','codmerc',$codmerc);
    		        $promofim=$this->retornabusca4('promofim','mercadorias','codmerc',$codmerc);
	
    		        $aInicio = Explode( "-",$promoinicio );
    		        $aFim    = Explode( "-",$promofim    );
    		        $aHoje   = Explode( "-",$this->corrigeNumero($this->datadehoje,"dataiso"));

    		        $nTempo1= mktime(0,0,0,$aInicio[1],$aInicio[2],$aInicio[0]);
    		        $nTempo2 = mktime(0,0,0,$aFim[1],$aFim[2],$aFim[0]);
    		        $nHoje  = mktime(0,0,0,$aHoje[1],$aHoje[2],$aHoje[0]);

    		        // se data de hoje estiver entre a data de inicio e a data de fim
    		        if($nHoje>=$nTempo1 and $nHoje<=$nTempo2){
    		        		return true; // dentro da promocao
    		        }else{
    		        		return false; // FORA da promocao
    		        }

	}
	
	function sinal_radio_preco($toggle){
		if($toggle->get_active()==false){ // preco venda NAO automatico
			$this->handler_radiobutton_precovenda=$this->radiobutton_precovenda->connect_simple('toggled',array($this,'preco'));
			$this->handler_radiobutton_precopromocao=$this->radiobutton_precopromocao->connect_simple('toggled',array($this,'preco'));
			$this->handler_radiobutton_precoatacado=$this->radiobutton_precoatacado->connect_simple('toggled',array($this,'preco'));
		}else{ //preco venda automatico
			$this->radiobutton_precovenda->disconnect($this->handler_radiobutton_precovenda);
			$this->radiobutton_precopromocao->disconnect($this->handler_radiobutton_precopromocao);
			$this->radiobutton_precoatacado->disconnect($this->handler_radiobutton_precoatacado);
		}
	}
	
	function preco($retorno=false){
    	if($retorno){
        	Gtk::timeout_add(100,array(&$this,'preco'),false);
            return;
    	}

        $codmerc=$this->getCodmerc();
	    if(empty($codmerc)){
    	    		//$this->setLabel_codmerc(' Pressione F1 para buscar a mercadoria');
			$this->entry_listamercadorias->set_text('Pressione F1 ou digite aqui p/ buscar mercadoria');
            $this->UltimaBuscaCodMerc="";
            $this->label_precounitario->set_text('');
            $this->label_precototal->set_text('');
            $this->label_estoque->set_text('');
            return;
        }
        
        if($this->UltimaBuscaCodMerc<>$codmerc){
            $this->UltimaBuscaCodMerc=$codmerc;
	    		$this->UltimoDescontoPDV=0;
        }//else{
	    //$this->UltimoDescontoPDV=0;
            //return;
        //}
		// buscar por ultimo pelo codmerc
        if(!empty($codmerc)){
			// verifica se mercadoria esta inativa
			$inativa=$this->retornabusca4('inativa','mercadorias','codmerc',$codmerc);
			if($inativa=="1"){
				return;
			}
           // retorna a descricao da mercadoria
       		$this->setLabel_codmerc($this->retornabusca4('descricao','mercadorias','codmerc',$codmerc));
       		$this->entry_listamercadorias->set_text($this->retornabusca4('descricao','mercadorias','codmerc',$codmerc));

            // retorna a unidade da mercadoria
            $this->setLabel_unidade($this->retornabusca4('unidade','mercadorias','codmerc',$codmerc));

            // retorna o estoqueatual da mercadoria
            $estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);

            if($estoqueatual<=0){
                if(!$this->verificaPermissao('030106',false)){
                		
					$this->status("Sem permissão de vender produto com estoque ZERO.");
                		
                    $this->entry_codmerc->set_text('');
                    $this->label_codmerc->set_text('');
                    $this->entry_listamercadorias->set_text('');
                    return false;
                    
                }
            }
            // bota o texto
            $this->setLabel_estoque($estoqueatual,$codmerc);

            // pega a quantidade
            $quantidade=$this->getQuantidade();
            if($quantidade==0 and $this->achoucodigobarras){
          		// se achou pelo codigo de barras o padrao e quantidade=1
           		$quantidade=1;
            } 
			if($this->checkbutton_pvendaautomatico->get_active()==true){
				// verifica a validade do preco  promocional
				if($this->verificaPrecoPromocao($codmerc)){
					// pega o preco promocional
					$preco=$this->retornabusca4('promopreco','mercadorias','codmerc',$codmerc);
					if($this->radiobutton_precopromocao->get_active()==FALSE){
						$this->radiobutton_precopromocao->set_active(true);
					}
				}else{ // senao
					// pega o preco normal
					$preco=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
					if($this->radiobutton_precovenda->get_active()==FALSE){
						$this->radiobutton_precovenda->set_active(true);
					}
				}
            
				// verifica se pega preco de atacado
				$quantatacado=$this->retornabusca4('quantatacado','mercadorias','codmerc',$codmerc);
				// se quantidade for maior que o minimo e a
			
				if($quantidade>=$quantatacado and $quantatacado!=0){ // quant!=0 ou seja.. ligado
					// pega o preco de atacado
					$preco=$this->retornabusca4('precoatacado','mercadorias','codmerc',$codmerc);
					if($this->radiobutton_precoatacado->get_active()==FALSE){
						$this->radiobutton_precoatacado->set_active(true);
					}
				}
			}else{ // se tiver desmarcado a opcao de preco automatico
				if($this->radiobutton_precoatacado->get_active()){
					$quantatacado=$this->retornabusca4('quantatacado','mercadorias','codmerc',$codmerc);
					// se quantidade for maior que o minimo e a
					if($quantatacado==0){
						msg("Preco de atacado inativo. Especifique a quantidade minima no cadastro de mercadorias.");
						$preco=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
						if($this->radiobutton_precovenda->get_active()==FALSE){
    			        	$this->radiobutton_precovenda->set_active(true);
						}
					}else{			
						if($quantidade>=$quantatacado){ // quant!=0 ou seja.. ligado
							// pega o preco de atacado
	    		             	$preco=$this->retornabusca4('precoatacado','mercadorias','codmerc',$codmerc);
								if($this->radiobutton_precoatacado->get_active()==FALSE){
    			             		$this->radiobutton_precoatacado->set_active(true);
								}
						}else{
							msg("Quantidade minima nao atingida para atacado");
							$preco=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
							if($this->radiobutton_precovenda->get_active()==FALSE){
    			             	$this->radiobutton_precovenda->set_active(true);
							}
						}
					}
				}elseif($this->radiobutton_precovenda->get_active()){
					$preco=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
				}elseif($this->radiobutton_precopromocao->get_active()){
					if($this->verificaPrecoPromocao($codmerc)){
						// pega o preco promocional
						$preco=$this->retornabusca4('promopreco','mercadorias','codmerc',$codmerc);
						if($this->radiobutton_precopromocao->get_active()==FALSE){
							$this->radiobutton_precopromocao->set_active(true);
						}
					}else{ // senao
							// pega o preco normal
						$preco=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
						if($this->radiobutton_precovenda->get_active()==FALSE){
							$this->radiobutton_precovenda->set_active(true);
						}
					}
				}
			}
	    $preco-=$this->UltimoDescontoPDV;
		$precototal=$preco*$quantidade;
		$precototal=number_format($precototal, 2, ',', '');
		$preco=number_format($preco, 2, ',', '');
		$this->setLabel_precounitario($preco);
		$this->setLabel_precototal($precototal);
        }

    }

    function quantidade($retorno=true){
        // quebra galho visto que o key-release nao funciona
        if($retorno){Gtk::timeout_add(100,array($this,'quantidade'),false);return;}

        $quantidade=$this->getQuantidade();
        $codmerc=$this->getCodmerc();

        if(empty($quantidade) or empty($codmerc)){
			$this->label_precounitario->set_text('');
            $this->label_precototal->set_text('');
        }else{
        		//$preco=$this->pegaNumero($this->label_precounitario);
			$this->preco(true);
        		/*$preco=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);		
            $precototal=$preco*$quantidade;

            $precototal=number_format($precototal, 2, ',', '');
            $preco=number_format($preco, 2, ',', '');
            $this->setLabel_precounitario($preco);
            $this->setLabel_precototal($precototal);*/
        }

    }

	function limpa($orcamento=false){
        // funcao que limpa toda a tela
        $this->liststore_venda->clear();
        if(!$orcamento) $this->entry_codcli->set_text('');
        if(!$orcamento) $this->UltimoClientePDV="";
        if(!$orcamento) $this->label_cliente->set_text('');
        if(!$orcamento) $this->label_endereco->set_text('');
        if(!$orcamento) $this->label_codendereco->set_text('');
        if(!$orcamento) $this->entry_codorca->set_text('');
        if(is_object($this->liststore_prazo)){$this->liststore_prazo->clear();};
        $this->lastCodOrca="";
        $this->lastCodVenda="";
        $this->totalDaVenda=0;
        $this->setLabel_precototalvenda();
        $this->entry_codvenda->set_text('');
        // limpa os outros entrys
        $this->limpa_entry();
	}

    function limpa_entry(){
        // funcao que limpa os entry de uma mercadoria
        // para limpar toda a tela veja limpa()
        $this->entry_codmerc->set_text('');
        //$this->setLabel_codmerc(' Pressione F1 para buscar a Mercadoria');
        $this->entry_listamercadorias->set_text('Pressione F1 ou digite aqui p/ buscar Mercadoria');
        $this->label_estoque->set_text('');
        $this->entry_quantidade->set_text('');
        $this->label_precounitario->set_text('');
        $this->label_precototal->set_text('');
        $this->label_unidade->set_text('');
    }

    function getQuantidade(){
        $quantidade=$this->entry_quantidade->get_text();
        $pos = strpos($quantidade,",");
        if ($pos === false) {
        }else{
            $quantidade=$this->pegaNumero($quantidade); // coloca o ponto separador de decimal
        }
        $quantidade=floatval($quantidade);
        if($quantidade<0){
            $quantidade=$quantidade*-1;
        }
        return $quantidade;
    }

    function getCodmerc(){
        $this->achoucodigobarras=false;
        $codmerc=$this->entry_codmerc->get_text();
        if(empty($codmerc)){
			return false;
    	}
    	$codmerc2=false;
    	if($this->radiobutton_tipocod->get_active()){
    		$codmerc=$this->DeixaSoNumero($codmerc);
    		$codmerc2=$this->retornabusca4('codmerc','mercadorias', 'codmerc', $codmerc);
    	}elseif($this->radiobutton_tiporef->get_active()){
    		$codmerc2=$this->retornabusca4('codmerc','mercadorias', 'referencia', $codmerc);
    	}elseif($this->radiobutton_tipobarras->get_active()){
    		$codmerc2=$this->retornabusca4('codmerc','mercadorias', 'codigobarras', $codmerc);
		$this->achoucodigobarras=true;
    	}
    	
        $inativa=$this->retornabusca4('inativa','mercadorias','codmerc',$codmerc2);
    	if($inativa=="1"){
    		return false;
    	}
        return $codmerc2;
    }

    function incluir(){
        // incluir mercadoria na clist
        $codigo=$this->getCodmerc();

        $quantidade=$this->getQuantidade();
        if(($quantidade==0 or empty($quantidade)) and $this->achoucodigobarras==true){
        		// se achou pelo codigo de barras o padrao e quantidade=1
        		$quantidade=1;
        }
        if(empty($codigo) or empty($quantidade) or floatval($quantidade)==0 or !$descricao=$this->retornabusca4('descricao','mercadorias','codmerc',$codigo)){
            $this->status('Escolha uma mercadoria para adicionar digitando o codigo ou pressionando ENTER para procurar.');
            return;
        }else{
    		    	$this->verificaSeExisteAUX=false;
			$this->liststore_venda->foreach( array($this,'verificaSeExisteNaLista'), 0, $codigo, false);
			if ($this->verificaSeExisteAUX){ // se ja exista na lista de venda
				$quantatual=$this->liststore_venda->get_value($this->verificaSeExisteAUXLastIter,2);
				$this->status("O sistema adicionou $quantidade na mercadoria existente na lista");
				$quantidade+=$quantatual;
				$this->liststore_venda->remove($this->verificaSeExisteAUXLastIter);
			}
            $this->UltimaBuscaCodMerc="";
            $unidade=$this->retornabusca4('unidade','mercadorias','codmerc',$codigo);
            $precounitoriginal=$this->retornabusca4('precovenda','mercadorias','codmerc',$codigo);
            $precounitario=$this->pegaNumero($this->label_precounitario);
            $precototal=$precounitario*$quantidade;
            // adiciona na clist
            $precototalnaclist=number_format($precototal, 2, ',', '');
            $precounitarionaclist=number_format($precounitario, 2, ',', '');
            $precounitoriginalnaclist=number_format($precounitoriginal, 2, ',', '');
            $lista=array($codigo, $descricao, $quantidade, $unidade, $precounitoriginalnaclist,$precounitarionaclist, $precototalnaclist);
            array_walk ($lista, array(&$this, 'utf8_encode_array'));
            $this->liststore_venda->append($lista);
            // bota o estilo de fonte e cor
            $this->limpa_entry();
            //$this->entry_codmerc->grab_focus();
            $this->entry_listamercadorias->grab_focus();
            $this->atualizaTotalVenda();
	    $this->UltimoDescontoPDV=0;
        }

    }
    function excluir(){
	    $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $this->liststore_venda->remove($iter);
			$this->atualizaTotalVenda();
        }else{
        	$this->excluirAUXcodmerc="";
			if($codmerc=inputdialog("Codigo da Mercadoria a excluir:")){
				$this->liststore_venda->foreach(array($this,'excluirAUX'),$codmerc);
				if(!empty($this->excluirAUXcodmerc)){
					$this->liststore_venda->remove($this->excluirAUXcodmerc);
					$this->atualizaTotalVenda();
				}
			}
        }
	}
	function excluirAUX($store, $path, $iter, $codmerc){
		$cod=$store->get_value($iter,0);
		if($cod==$codmerc){
			$this->excluirAUXcodmerc=$iter;
			return true;
		}
		return false;
	}

	function atualizaTotalVenda(){
		$this->totalDaVenda=0;
		$this->liststore_venda->foreach(array($this,'atualizaTotalVendaAux'));
		$this->setLabel_precototalvenda();
	}

    function atualizaTotalVendaAux($store, $path, $iter){
        $totalitem=$store->get_value($iter,6);
		$this->totalDaVenda+=$this->pegaNumero($totalitem);
	}

    function buscaendereco(){
    	if($this->BloqueiaBuscaEndereco){
    		$this->entry_listamercadorias->grab_focus();
    		$this->BloqueiaBuscaEndereco=false;
    		return;
    	}
        $codcli=$this->entry_codcli->get_text();
        if(empty($codcli)){
            msg("Selecione primeiro o cliente, depois selecione o endereco de entrega.");
            return;
        }
        $nome=$this->retornabusca4('nome','clientes','codigo',$codcli);
        $this->setLabel_cliente($nome);
        $this->label_codendereco->set_text('');
        $this->label_endereco->set_text('');
        if(!$this->retorna_OPCAO('pdvenderecocliente')){
    		return;
    	}
        $sql="select descricao, endereco, numero, complemento, bairro, cidade, estado, cep, telefone, fax, celular from cadastro2enderecos as cadastro2enderecos where cadastro='clientes' and codigo=$codcli";
        $this->buscatab($sql, true, $this->label_codendereco, $this->botao_endereco_doido, 'cadastro2enderecos',"endereco",'descricao');
    }

    function eventoEndereco(){
    	if(!$this->retorna_OPCAO('pdvenderecocliente')){
    		$this->entry_listamercadorias->grab_focus();
    		return;
    	}
        $codigo=$this->entry_codcli->get_text();
        $descricao=$this->label_codendereco->get_text();
        if(!empty($codigo) and !empty($descricao)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();
            $sql="SELECT endereco, numero FROM cadastro2enderecos WHERE codigo='$codigo' AND cadastro='clientes' AND descricao='$descricao'";
            $resultado=$con->Query($sql);
            $i = $con->FetchRow($resultado);
            $texto=$i[0].", ".$i[1];
            $this->setLabel_codendereco($texto);
            $con->Disconnect();
            $this->entry_listamercadorias->grab_focus();
        }
    }

    function clistVendaKey($widget,$evento){
        $tecla=$evento->keyval;
        if($tecla==65535 or $tecla==65439){
            // del
            $this->excluir();
        }
    }


    // limpa codigo do endereco e verifica debido do cliente
    function eventoCodCli(){
        $codcli=$this->entry_codcli->get_text();
        $this->setLabel_cliente();
        if(empty($codcli)){
            return;
        }
        if(strval($this->UltimoClientePDV)<>strval($codcli)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();
            $this->UltimoClientePDV=$codcli;

            $this->button_endereco->clicked();
        }else{
            return;
        }
        // se integracao estoque-financeiro estiver ativada
        if($this->retorna_OPCAO('integraestoquefinanceiro')){
            // verifica se o cliente tem debito na tabela receber
            $sql="select sum(saldo) from receber where codorigem='$codcli';";
            $resultado=$con->Query($sql);
            $saldo=$con->FetchRow($resultado);

            $debitomaximo=$this->retornabusca3('clientes', &$this->entry_codcli, 'codigo', 'debmaximo', 'pdv');


            $dataiso=$this->anodehoje=date('Y',time()).'-'.$this->mesdehoje=date('m',time()).'-'.$this->diadehoje=date('d',time());
            $sql="select count(saldo),sum(saldo) from receber where data_v<='$dataiso' and codorigem='$codcli' and saldo>'0'";
            $resultado=$con->Query($sql);
            $i=$con->FetchRow($resultado);

            //$achou=$con->NumRows($resultado);

            if($saldo[0]>$debitomaximo){
                $msg.="Saldo DEVEDOR (".$this->mascara2($saldo[0],'moeda').") no contas a receber maior que DEBITO MAXIMO(".$this->mascara2($debitomaximo,'moeda').")\n";
                //$this->ClienteComDebitoMaximo=$this->verificaPermissao('030109',false);
                $this->ClienteComDebitoMaximo=true;
            }else{
                $this->ClienteComDebitoMaximo=false;
            }
            if($i[0]>0){
                $msg.="Este cliente possui DEBITOS no \"contas a receber\" em ATRASO\n";
                //$this->ClienteComDebitoAtrasado=$this->verificaPermissao('030108',false);
                $this->ClienteComDebitoAtrasado=true;
            }else{
                $this->ClienteComDebitoAtrasado=false;
            }
            if($i[1]>0){
                $msg.="Saldo devedor atrasado: ".$this->mascara2($i[1],'moeda');
            }
            //chama busca endereco
            if(!empty($msg)){
                msg($msg);
                $this->buscaEndereco();
            }
        }
        $con->Disconnect();
    }
	function buscaVendaOrca(){
		$this->cancelarBuscaVendaOrca();
		$codcli=$this->entry_codcli->get_text();
		$this->xml=$this->carregaGlade("buscavendaorca2",false,false,false,false);
		$this->radiobutton_codigo=$this->xml->get_widget('radiobutton_codigo');
		$this->radiobutton_cliente=$this->xml->get_widget('radiobutton_cliente');
		$this->radiobutton_todos=$this->xml->get_widget('radiobutton_todos');
		if(empty($codcli)){ // se nao houver cliente na tela
			$this->radiobutton_cliente->set_sensitive(false);
		}
		$this->radiobutton_vendaBVO=$this->xml->get_widget('radiobutton_venda');
		$this->radiobutton_orcaBVO=$this->xml->get_widget('radiobutton_orca');
		$this->window1BVO=$this->xml->get_widget('window1');
		$this->window1BVO->set_position(3);
		$this->button_okBVO=$this->xml->get_widget('button_ok');
		$this->button_cancelarBVO=$this->xml->get_widget('button_cancelar');
		$this->button_okBVO->connect_simple('clicked',array($this,'okBuscaVendaOrca'));
		$this->button_cancelarBVO->connect_simple('clicked',array($this,'cancelarBuscaVendaOrca'));
		
		$this->window1BVO->show_all();
	}
	
	function okBuscaVendaOrca(){
		// tipo de busca
		if($this->radiobutton_codigo->get_active()){
			$this->buscaVendaOrcaPorCodigo();
			return;
		}elseif($this->radiobutton_cliente->get_active()){
			$vecliente=true;
		}elseif($this->radiobutton_todos->get_active()){
			$vecliente=false;
		}
		// venda ou orcamento
		if($this->radiobutton_vendaBVO->get_active()){
			$this->buscaVenda($vecliente);
		}else{
			$this->buscaOrca($vecliente);
		}		
		$this->cancelarBuscaVendaOrca();
	}
	
	function cancelarBuscaVendaOrca(){
		if($this->window1BVO) $this->window1BVO->destroy();
	}
	
	function buscaVendaOrcaPorCodigo(){
		if($this->radiobutton_vendaBVO->get_active()){
			$tabela="saidas";
			$entry=$this->entry_codvenda;
		}else{
			$tabela="orcamento";
			$entry=$this->entry_codorca;
		}
		$this->cancelarBuscaVendaOrca(); //fecha tela		
		
		if($codigo=inputdialog("Digite o codigo")){		
			if($achou=$this->retornabusca4('cod'.$tabela,$tabela,"cod".$tabela,$codigo)){
				$this->BloqueiaBuscaEndereco=true;
				$entry->set_text($codigo);
			}else{
				msg("Movimentacao nao encontrada.");
			}
		}

	}
	
    function buscaOrca($vecliente=false){
        $codcli=$this->entry_codcli->get_text();
        if(empty($codcli) and $vecliente){
            msg('Escolha primeiro o cliente!!');
            return;
        }
        $sql="SELECT o.codorcamento, c.nome, o.data, o.totalnf FROM orcamento AS o INNER JOIN clientes AS c ON c.codigo=o.codcli WHERE o.finalizado='N' ";
        if($vecliente) $sql.=" AND o.codcli='$codcli'";
		$sql.=" ORDER BY c.nome,o.data DESC";
        $this->buscatab(
            $sql,
            true,
            $this->entry_codorca,
            null,
            "orcamento",
            "codorcamento",
            "codorcamento"
        );
    }

	function buscaVenda($vecliente=false){
        $codcli=$this->entry_codcli->get_text();
        if(empty($codcli) and $vecliente){
            msg('Escolha primeiro o cliente!!');
            return;
        }
        $sql="SELECT o.codsaidas, c.nome, o.data, o.totalnf, o.obs FROM saidas AS O, clientes AS c WHERE c.codigo=o.codcli ";
        if($vecliente) $sql.=" AND o.codcli='$codcli' ";
        $sql.=" ORDER BY c.nome,o.data DESC";
        $this->buscatab(
            $sql,
            true,
            $this->entry_codvenda,
            null,
            "saidas",
            "codsaidas",
            "codsaidas"
        );
    }

    function eventoOrca(){
        $label=$this->entry_codorca->get_text();
        $codcli=$this->entry_codcli->get_text();

        if(!empty($label) and $label<>$this->lastCodOrca){
            $codendereco=$this->label_codendereco->get_text();
            $this->limpa(true);
            $this->lastCodOrca=$label;
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();

            $sql="SELECT e.codmerc, m.descricao, e.quantidade, m.unidade, m.precovenda, e.precocomdesconto FROM entsai as e, mercadorias as m WHERE m.codmerc=e.codmerc AND e.codentsai='$label' AND e.tipo='O';";
            $resultado=$con->Query($sql);
            while($i = $con->FetchRow($resultado)) {
                $this->liststore_venda->append(
                    array(
                        $i[0],
                        utf8_encode($i[1]),
                        $i[2],
                        $i[3],
                        number_format($i[4], 2, ',', ''),
                        number_format($i[5], 2, ',', ''),
                        number_format($i[5]*$i[2], 2, ',', '') // precoDec * quantidade
                    )
                );
            }
            $this->atualizaTotalVenda();
                        
            // bloqueia busca de endereco
            $this->BloqueiaBuscaEndereco=true;
            
            $sql="SELECT o.codcli, o.vendedor, c.nome, e.codinterno, e.descricao, e.endereco, e.numero FROM orcamento AS o INNER JOIN clientes AS c ON (o.codcli=c.codigo) INNER JOIN cadastro2enderecos AS e ON (o.endereco=e.descricao) WHERE o.codorcamento='$label' AND e.cadastro='clientes' AND e.codigo=o.codcli;";
            $resultado=$con->Query($sql);
            $resultado2=$con->FetchArray($resultado);
            $this->setLabel_codendereco($resultado2[5].$resultado2[6]);
            	
            	$this->label_codendereco->set_text($resultado2[4]);
			$this->setLabel_codendereco();
            $this->setLabel_cliente($resultado2[2]);
            $this->entry_codcli->set_text($resultado2[0]);
            $con->Disconnect();
        }
        return;
    }

    function eventoBuscaVenda(){
        
        $label=$this->entry_codvenda->get_text();
        $codcli=$this->entry_codcli->get_text();

        if(!empty($label) and $label<>$this->lastCodVenda){        		
            $codendereco=$this->label_codendereco->get_text();
            $this->limpa(true);
            $this->lastCodVenda=$label;
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();
			
            $sql="SELECT e.codmerc, m.descricao, e.quantidade, m.unidade, m.precovenda, e.precocomdesconto FROM entsai as e, mercadorias as m WHERE m.codmerc=e.codmerc AND e.codentsai='$label' AND e.tipo='S';";
            $resultado=$con->Query($sql);
            while($i = $con->FetchRow($resultado)) {
                $this->liststore_venda->append(
                    array(
                        $i[0],
                        utf8_encode($i[1]),
                        $i[2],
                        $i[3],
                        number_format($i[4], 2, ',', ''),
                        number_format($i[5], 2, ',', ''),
                        number_format($i[5]*$i[2], 2, ',', '') // precoDec * quantidade
                    )
                );
            }
            
            $this->atualizaTotalVenda();
            
            // bloqueia busca de endereco
            $this->BloqueiaBuscaEndereco=true;
            
            $sql="SELECT s.codcli, s.vendedor, c.nome, e.codinterno, e.descricao, e.endereco, e.numero FROM saidas AS s INNER JOIN clientes AS c ON (s.codcli=c.codigo) INNER JOIN cadastro2enderecos AS e ON (s.endereco=e.descricao) WHERE s.codsaidas='$label' AND e.cadastro='clientes' AND e.codigo=s.codcli";
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0){
	            // se houver endereco cadastrado na venda para o cliente

	            $resultado2=$con->FetchArray($resultado);
	            //$this->label_endereco->set_text($resultado2[5].$resultado2[6]);
				$this->label_codendereco->set_text($resultado2[4]);
				$this->setLabel_codendereco($resultado2[5].$resultado2[6]);
	            $this->setLabel_cliente($resultado2[2]);
	            $this->entry_codcli->set_text($resultado2[0]);
	            //$this->entry_codvenda->set_text('');
            }else{
	            // se nao houver endereco na venda
	            $sql="SELECT s.codcli, s.vendedor, c.nome FROM saidas AS s INNER JOIN clientes AS c ON (s.codcli=c.codigo) WHERE s.codsaidas='$label'";
            	$resultado=$con->Query($sql);
				if($con->NumRows($resultado)>0){
					$resultado2=$con->FetchArray($resultado);
					$this->setLabel_cliente($resultado2[2]);
					$this->entry_codcli->set_text($resultado2[0]);
					// limpa endereco anterior
					$this->label_codendereco->set_text("");
					$this->setLabel_codendereco("");
				}            	
            }            
            $con->Disconnect();            
        }
        return;
    }


    function mostraHistorico(){
        // esta funcao retorna uma lista(historico) com as compras efetuadas pelo cliente
        $codcli=$this->entry_codcli->get_text();
        if(empty($codcli)){
            msg('Escolha primeiro um cliente!!');
        }else{
            $sql="SELECT e.codmerc, m.descricao, f.nome, e.quantidade, e.precooriginal, e.precocomdesconto, s.data, s.vendedor FROM entsai as e LEFT JOIN saidas AS s ON (e.codentsai=s.codsaidas) LEFT JOIN mercadorias AS m ON (m.codmerc=e.codmerc) LEFT JOIN fabricantes AS f ON (f.codigo=m.codfab) WHERE e.tipo='S' AND s.codcli='$codcli' ORDER BY s.data DESC";
            $this->buscatab($sql, false, null, null, null, null, null);
        }
    }

	function quantidadeFocusOut(){
		$this->entry_codmerc->grab_focus();
	}
	function codmercFocusOut(){
		$this->entry_quantidade->grab_focus();
	}
	


}
?>

