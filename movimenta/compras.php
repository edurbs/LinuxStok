<?php

class compras extends funcoes {
	function compras(){
        $this->xml=$this->carregaGlade('compras');
        
        $this->scrolledwindow_compra=$this->xml->get_widget('scrolledwindow_compra');
        $this->liststore_compra=new GtkListStore(
            Gobject::TYPE_STRING, //0 codigo
            Gobject::TYPE_STRING, //1 descricao
            Gobject::TYPE_STRING, //2 quant
            Gobject::TYPE_STRING, //3 un
            Gobject::TYPE_STRING, //4 custo
            Gobject::TYPE_STRING,  //5 venda
            Gobject::TYPE_STRING, //6 ipi
            Gobject::TYPE_STRING, //7 icms
            Gobject::TYPE_STRING, //8 preco custo total //8 imposto extra
            Gobject::TYPE_STRING, //9 falsolucro lucro hidden
            Gobject::TYPE_STRING, //10 margem lucro hidden
            Gobject::TYPE_STRING  //11 estoque minimo hidden
            
        );
        $this->treeview_compra=new GtkTreeView($this->liststore_compra);
		$this->treeview_compra->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
            array('Cod.', 'Descricao', 'Qt', 'UN', 'Custo Unit.', 'Venda Unit.', 'IPI%', 'ICMS%', 'Custo Total', 'falsolucro', 'margemlucro', 'estoquemin'),
            $this->treeview_compra,
            array(-1, -1, -1, -1, -1, -1, -1, -1, -1, 0, 0, 0)
        );
        $this->scrolledwindow_compra->add($this->treeview_compra);
        $this->scrolledwindow_compra->show_all();
        
        $this->scrolledwindow_pgto=$this->xml->get_widget('scrolledwindow_pgto');
        $this->liststore_pgto=new GtkListStore(
            Gobject::TYPE_STRING, //0 documento
            Gobject::TYPE_STRING, //1 cod. meio pgto
            Gobject::TYPE_STRING, //2 desc meio pgto
            Gobject::TYPE_STRING, //3 valor
            Gobject::TYPE_STRING, //4 data venc.
            Gobject::TYPE_STRING, //5 cod placon
            Gobject::TYPE_STRING, //6 desc. placon
            Gobject::TYPE_STRING  //7 obs
        );
        $this->treeview_pgto=new GtkTreeView($this->liststore_pgto);
		$this->treeview_pgto->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
            array('Documento','Cod.MP','Descricao Meio Pgto','Valor', 'Data Venc.', 'Cod.Pla.Con', 'Descricao Plano de Contas', 'Observacoes'),
            $this->treeview_pgto
        );
        $this->scrolledwindow_pgto->add($this->treeview_pgto);
        $this->scrolledwindow_pgto->show_all();
        
        
        $this->entry_codcompra=$this->xml->get_widget('entry_codcompra');
        $this->entry_codpedido=$this->xml->get_widget('entry_codpedido');
        
        $this->entry_fornecedor=$this->xml->get_widget('entry_fornecedor');
        $this->label_fornecedor=$this->xml->get_widget('label_fornecedor');
        $this->entry_fornecedor->connect('key_press_event', 
            array($this, 'entry_enter'), 
            "SELECT codigo, nome, contato, dtnasc, dtcadastro, cnpj_cpf, ie_rg FROM fornecedores WHERE inativo='0' ORDER BY nome ", 
            true,
            $this->entry_fornecedor, 
            $this->label_fornecedor,
            "fornecedores",
            "nome",
            "codigo"
        );
        $this->entry_fornecedor->connect_simple('focus-out-event',
        	array($this, 'retornabusca22'), 
			'fornecedores', 
			$this->entry_fornecedor, 
			$this->label_fornecedor, 
			'codigo', 
			'nome'
		);
        
        $this->entry_data=$this->xml->get_widget('entry_data');
        $this->entry_data->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_numeronota=$this->xml->get_widget('entry_numeronota');
        
        // busca mercadoria
        $this->entry_codmerc=$this->xml->get_widget('entry_codmerc');
        $this->entry_codmerc->connect_simple('changed', array($this,'eventoCodMerc'));
        $this->label_descricao=$this->xml->get_widget('label_descricao');
        $this->button_codmerc_atualizar=$this->xml->get_widget('button_codmerc_atualizar');
        $this->entry_codmerc->connect('key_press_event', 
            array($this, 'entry_enter'),
            "SELECT m.codmerc, m.descricao, m.referencia, m.precocusto, m.precovenda, m.unidade, f.nome, m.estoqueatual, m.obs FROM mercadorias AS m LEFT JOIN fornecedores AS f ON (f.codigo=m.codfor) WHERE m.inativa='0' ORDER BY m.descricao", 
            true,
            $this->entry_codmerc, 
            null,
            "mercadorias",
            "descricao",
            "codmerc"
        );
        $this->entry_codmerc->connect_simple('focus-out-event',
        	array($this, 'retornabusca22'), 
			'mercadorias', 
			$this->entry_codmerc, 
			null, 
			'codmerc', 
			'descricao'
		);        
        
        $this->label_unidade=$this->xml->get_widget('label_unidade');
        $this->label_estoque=$this->xml->get_widget('label_estoque');
        $this->entry_estoqueminimo=$this->xml->get_widget('entry_estoqueminimo');
        $this->entry_estoqueminimo->connect('key-press-event', array($this, 'mascaraNew'),'virgula3');
        $this->label_ultimavenda=$this->xml->get_widget('label_ultimavenda');
        $this->label_customedio=$this->xml->get_widget('label_customedio');
        
        $this->entry_quantidade=$this->xml->get_widget('entry_quantidade');
        $this->entry_quantidade->connect('key-press-event', array($this, 'mascaraNew'),'virgula3');
        $this->entry_quantidade->connect_simple('focus-out-event', array($this,'FocusOutQuantidade'));
        
        $this->entry_custo=$this->xml->get_widget('entry_custo');
        $this->entry_custo->connect('key-press-event', array($this, 'mascaraNew'),'virgula4');
        $this->entry_custo->connect_simple('focus-out-event', array($this,'FocusOutCusto'));
        
        $this->entry_venda=$this->xml->get_widget('entry_venda');
        $this->entry_venda->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_venda->connect_simple('focus-out-event', array($this,'FocusOutVenda'));
        
        $this->label_custototal=$this->xml->get_widget('label_custototal');
        
        $this->entry_ipi=$this->xml->get_widget('entry_ipi');
        $this->entry_ipi->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_icms=$this->xml->get_widget('entry_icms');
        $this->entry_icms->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        
        $this->entry_falsolucro=$this->xml->get_widget('entry_falsolucro');
        $this->entry_falsolucro->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_falsolucro->connect_simple('focus-out-event', array($this,'FocusOutFalsoLucro'));
        
        $this->entry_margemlucro=$this->xml->get_widget('entry_margemlucro');
        $this->entry_margemlucro->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_margemlucro->connect_simple('focus-out-event', array($this,'FocusOutMargemLucro'));
        
        $this->entry_total=$this->xml->get_widget('entry_total');
        $this->entry_total->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_total->connect_simple('focus-out-event', array($this,'FocusOutTotal'));
        
        $this->button_adicionar_compra=$this->xml->get_widget('button_adicionar_compra');
        $this->button_adicionar_compra->connect_simple('clicked', array($this,'adicionarItemCompra'));
        
        $this->button_excluir_compra=$this->xml->get_widget('button_excluir_compra');
        $this->button_excluir_compra->connect_simple('clicked', array($this,'removerItemCompra'));
        
        $this->button_historico_codmerc=$this->xml->get_widget('button_historico_codmerc');
        $this->button_historico_codmerc->connect_simple('clicked', array($this,'historico_codmerc'));
        
        // totais da compra
        $this->entry_ipi_total=$this->xml->get_widget('entry_ipi_total');
        $this->entry_ipi_total->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_icms_total=$this->xml->get_widget('entry_icms_total');
        $this->entry_icms_total->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        
        $this->entry_seguro=$this->xml->get_widget('entry_seguro');
        $this->entry_seguro->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_seguro->connect_simple('focus-out-event', array($this,'somaListaCompra2'));
        
        $this->entry_frete=$this->xml->get_widget('entry_frete');
        $this->entry_frete->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_frete->connect_simple('focus-out-event', array($this,'somaListaCompra2'));
        
        
        $this->entry_outrasdespesas=$this->xml->get_widget('entry_outrasdespesas');
        $this->entry_outrasdespesas->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_outrasdespesas->connect_simple('focus-out-event', array($this,'somaListaCompra2'));
        
        $this->label_total_compra=$this->xml->get_widget('label_total_compra');
        
        // forma de pagamento
        $this->entry_documento=$this->xml->get_widget('entry_documento');
 
        $this->entry_codmeiopgto=$this->xml->get_widget('entry_codmeiopgto');
        $this->label_codmeiopgto=$this->xml->get_widget('label_codmeiopgto');
        $this->entry_codmeiopgto->connect('key_press_event', 
            array($this, 'entry_enter'),
            "SELECT * FROM meiopgto ORDER BY descricao ", 
            true,
            $this->entry_codmeiopgto, 
            $this->label_codmeiopgto,
            "meiopgto",
            "descricao",
            "codigo"
        );
        $this->entry_codmeiopgto->connect_simple('focus-out-event',
        	array($this, 'retornabusca22'), 
			'meiopgto', 
			$this->entry_codmeiopgto, 
			$this->label_codmeiopgto, 
			'codigo', 
			'descricao'
		);
		$this->entry_codmeiopgto->connect_simple('focus-out-event',array($this,'FocusOutMeioPgto'));
		
 
        $this->entry_valor=$this->xml->get_widget('entry_valor');
        $this->entry_valor->connect('key-press-event', array($this, 'mascaraNew'),'virgula2');
        $this->entry_dtvencimento=$this->xml->get_widget('entry_dtvencimento');
        $this->entry_dtvencimento->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        
        $this->entry_codplacon=$this->xml->get_widget('entry_codplacon');
        $this->label_codplacon=$this->xml->get_widget('label_codplacon');
        $this->entry_codplacon->connect('key_press_event', 
            array($this, 'entry_enter'),
            "SELECT * FROM placon ORDER BY descricao ", 
            true,
            $this->entry_codplacon, 
            $this->label_codplacon,
            "placon",
            "descricao",
            "codigo"
        );
        $this->entry_codplacon->connect_simple('focus-out-event',
        	array($this, 'retornabusca22'), 
			'placon', 
			$this->entry_codplacon, 
			$this->label_codplacon, 
			'codigo', 
			'descricao'
		);
        
        $this->entry_obs=$this->xml->get_widget('entry_obs');
        
        $this->label_total_pgtos=$this->xml->get_widget('label_total_pgtos');
 
        $this->button_adicionar_pgto=$this->xml->get_widget('button_adicionar_pgto');
        $this->button_adicionar_pgto->connect_simple('clicked', array($this,'adicionarParcelaPgto'));        
        
        $this->button_excluir_pgto=$this->xml->get_widget('button_excluir_pgto');
        $this->button_excluir_pgto->connect_simple('clicked', array($this,'excluirParcelaPgto'));
        
        // botoes finais
        $this->button_comprar=$this->xml->get_widget('button_comprar');
        $this->button_comprar->connect_simple('clicked', array($this,'comprar'));
        
        $this->button_pedir=$this->xml->get_widget('button_pedir');
        $this->button_pedir->connect_simple('clicked', array($this,'pedir'));
        
        $this->button_limpar=$this->xml->get_widget('button_limpar');
        $this->button_limpar->connect_simple('clicked', array($this,'limpar_geral'));
        
        $this->button_cancelar=$this->xml->get_widget('button_cancelar');
        $this->button_cancelar->connect_simple('clicked', array($this,'cancelar'));

		// atalhos do teclado
		global $atalho_padrao;        
        $this->button_limpar->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F7, 	0, Gtk::ACCEL_VISIBLE);
        $this->button_pedir->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F9, 	0, Gtk::ACCEL_VISIBLE);
        $this->button_comprar->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F12, 	0, Gtk::ACCEL_VISIBLE);
        $this->button_adicionar_compra->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F4, 	0, Gtk::ACCEL_VISIBLE);
        $this->button_historico_codmerc->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F6, 	0, Gtk::ACCEL_VISIBLE);
        $this->button_adicionar_pgto->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F5, 	0, Gtk::ACCEL_VISIBLE);
        
        $this->limpar_geral(false);
	}
	
	function limpa_mercadoria($codmerc=true){
		$this->setLabel_descricao('<< Pressione <ENTER> para buscar');
		$this->setLabel_unidade('');
		$this->setLabel_estoque('');
		$this->setEntry_estoqueminimo('');
		$this->setLabel_ultimavenda('');
		$this->setLabel_customedio('');
		$this->setEntry_custo('');
		$this->setEntry_venda('');
		$this->setEntry_ipi('');
		$this->setEntry_icms('');
		$this->setEntry_falsolucro('');
		$this->setEntry_margemlucro('');
		if($codmerc) $this->setEntry_codmerc('');		
		$this->setEntry_quantidade('');
		$this->setEntry_total('');		
	}
	
	function limpa_cabecalho(){
		$this->setEntry_codcompra('');
		$this->setEntry_codpedido('');
		$this->setEntry_fornecedor('');
		$this->setLabel_fornecedor('<< Pressione <ENTER> para buscar');
		$this->setEntry_data(date("Y-m-d"));
		$this->setEntry_numeronota('');
	}
	
	function limpa_totais(){
		$this->setLabel_total_compra('');
		$this->setLabel_total_pgtos('');
		$this->setEntry_ipi_total('');
		$this->setEntry_icms_total('');
		$this->setEntry_frete('');
		$this->setEntry_seguro('');
		$this->setEntry_outrasdespesas('');
		$this->setLabel_custototal('');
	}
	
	
	function limpa_pgtos(){
		$this->setEntry_documento('');
		$this->setEntry_codmeiopgto('');
		$this->setLabel_codmeiopgto('');
		$this->setEntry_valor('');
		$this->setEntry_dtvencimento('');
		$this->setEntry_codplacon('');
		$this->setLabel_codplacon('');
		$this->liststore_pgto->clear();
	}
	
	function limpar_geral($ask=true){
		if($ask){
			if(!confirma(false,'Deseja limpar todos os campos da tela?')){
				return;
			}
		}
		$this->liststore_compra->clear();
		$this->limpa_pgtos();
		$this->limpa_cabecalho();
        $this->limpa_mercadoria();
        $this->limpa_totais();
	}

	function getEntry_codmeiopgto(){
		return $this->pegaNumero($this->entry_codmeiopgto);
	}
	
	function getLabel_codmeiopgto(){
		return $this->label_codmeiopgto->get_text();
	}
	
	function getEntry_codplacon(){
		return $this->entry_codplacon->get_text();
	}
	
	function getLabel_codplacon(){
		return $this->label_codplacon->get_text();
	}	
	
	function getEntry_documento(){
		return $this->entry_documento->get_text();
	}
	
	function getEntry_obs(){
		return strtoupper($this->entry_obs->get_text());
	}
	
	function getEntry_valor(){
		return $this->pegaNumero($this->entry_valor);
	}
	
	function getEntry_dtvencimento(){
		return $this->entry_dtvencimento->get_text();
	}
	
	function getEntry_data(){
		return $this->entry_data->get_text();
	}	

	function getEntry_codmerc(){
		return $this->pegaNumero($this->entry_codmerc);
	}
	function getEntry_fornecedor(){
		return $this->pegaNumero($this->entry_fornecedor);
	}
	
	function getLabel_descricao(){
		return $this->label_descricao->get_text();
	}
	
	function getLabel_unidade(){
		return $this->label_unidade->get_text();
	}
	
	function getEntry_quantidade(){
		return $this->pegaNumero($this->entry_quantidade);
	}
	
	function getEntry_custo(){
		return $this->pegaNumero($this->entry_custo);
	}
	
	function getEntry_venda(){
		return $this->pegaNumero($this->entry_venda);
	}
	
	function getEntry_total(){
		return $this->pegaNumero($this->entry_total);
	}
	
	function getEntry_margemlucro(){
		return $this->pegaNumero($this->entry_margemlucro);
	}
	
	function getEntry_falsolucro(){
		return $this->pegaNumero($this->entry_falsolucro);
	}
	
	function getEntry_ipi(){
		return $this->pegaNumero($this->entry_ipi);
	}
	
	function getEntry_icms(){
		return $this->pegaNumero($this->entry_icms);
	}
	
	function getEntry_ipi_total(){
		return $this->pegaNumero($this->entry_ipi_total);
	}
	
	function getEntry_icms_total(){
		return $this->pegaNumero($this->entry_icms_total);
	}
	
	function getEntry_seguro(){
		return $this->pegaNumero($this->entry_seguro);
	}
	
	function getEntry_frete(){
		return $this->pegaNumero($this->entry_frete);
	}
	
	function getEntry_outrasdespesas(){
		return $this->pegaNumero($this->entry_outrasdespesas);
	}
	
	function getLabel_total_compra(){
		return $this->pegaNumero($this->label_total_compra);
	}
	
	function getLabel_total_pgtos(){
		return $this->pegaNumero($this->label_total_pgtos);
	}
	
	function getEntry_estoqueminimo(){
		return $this->pegaNumero($this->entry_estoqueminimo);
	}
	
	function setLabel_estoque($var){
		$this->label_estoque->set_text($this->corrigeNumero($var,'virgula3'));
	}
	
	function setEntry_estoqueminimo($var){
		$this->entry_estoqueminimo->set_text($this->corrigeNumero($var,'virgula3'));
	}
	
	function setLabel_descricao($var){
		$this->label_descricao->set_text($var);
	}
	
	function setLabel_ultimavenda($var){
		$this->label_ultimavenda->set_text($this->corrigeNumero($var,'data'));
	}
	
	function setLabel_customedio($var){
		$this->label_customedio->set_text($this->corrigeNumero($var,'virgula3'));
	}
	
	function setLabel_unidade($var){
		$this->label_unidade->set_text($var);
	}
	
	function setEntry_custo($var){
		$this->entry_custo->set_text($this->corrigeNumero($var,'virgula4'));
		$this->FocusOutCusto();
	}
	
	function setEntry_venda($var){
		$this->entry_venda->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_ipi($var){
		$this->entry_ipi->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_ipi_total($var){
		$this->entry_ipi_total->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_icms($var){
		$this->entry_icms->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_icms_total($var){
		$this->entry_icms_total->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_outrasdespesas($var){
		$this->entry_outrasdespesas->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_seguro($var){
		$this->entry_seguro->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_frete($var){
		$this->entry_frete->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setLabel_custototal($var){
		$this->label_custototal->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setLabel_total_compra($var){
		$this->label_total_compra->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setLabel_total_pgtos($var){
		$this->label_total_pgtos->set_text($this->corrigeNumero($var,'virgula2'));
	}		
	
	function setEntry_falsolucro($var){
		$this->entry_falsolucro->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_margemlucro($var){
		$this->entry_margemlucro->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_total($var){
		$this->entry_total->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_quantidade($var){
		$this->entry_quantidade->set_text($this->corrigeNumero($var,'virgula3'));
	}
	
	function setEntry_codmerc($var){
		$this->entry_codmerc->set_text($var);
	}
	
	function setEntry_codcompra($var){
		$this->entry_codcompra->set_text($var);
	}
	
	function setEntry_codpedido($var){
		$this->entry_codpedido->set_text($var);
	}
	
	function setEntry_fornecedor($var){
		$this->entry_fornecedor->set_text($var);
	}
	
	function setLabel_fornecedor($var){
		$this->label_fornecedor->set_text($var);
	}
	
	function setEntry_data($var){
		$this->entry_data->set_text($this->corrigeNumero($var,'data'));
	}
	
	function setEntry_numeronota($var){
		$this->entry_numeronota->set_text($var);
	}
	
	function setEntry_documento($var){
		$this->entry_documento->set_text($var);
	}
	
	function setEntry_codmeiopgto($var){
		$this->entry_codmeiopgto->set_text($var);
	}
	
	function setLabel_codmeiopgto($var){
		$this->label_codmeiopgto->set_text($var);
	}
	
	function setEntry_valor($var){
		$this->entry_valor->set_text($this->corrigeNumero($var,'virgula2'));
	}
	
	function setEntry_dtvencimento($var){
		$this->entry_dtvencimento->set_text($this->corrigeNumero($var,'data'));
	}
	
	function setEntry_codplacon($var){
		$this->entry_codplacon->set_text($var);
	}
	
	function setLabel_codplacon($var){
		$this->label_codplacon->set_text($var);
	}
	
	function setEntry_obs($var){
		$this->entry_obs->set_text($var);
	}
	
	function eventoCodMerc(){
        $con=$this->conecta();
        
        $codmerc=$this->getEntry_codmerc();
        if(empty($codmerc)){
        	$this->limpa_mercadoria(false);
        	return;	
        };
        
        $sql="SELECT descricao, unidade, estoqueatual, estoqueminimo, ultimavenda, customedio, precocusto, precovenda, ipi, icms, falsolucro, margemlucro FROM mercadorias WHERE codmerc='$codmerc' ";
        $query=$con->Query($sql);
        
        if($con->NumRows($query)==0){
        	$this->limpa_mercadoria(false);
        	return;	
        };
        
        $resultado=$con->FetchRow($query);
		
		$this->setLabel_descricao($resultado[0]);
		$this->setLabel_unidade($resultado[1]);
		$this->setLabel_estoque($resultado[2]);
		$this->setEntry_estoqueminimo($resultado[3]);
		$this->setLabel_ultimavenda($resultado[4]);
		$this->setLabel_customedio($resultado[5]);
		$this->setEntry_custo($resultado[6]);
		$this->setEntry_venda($resultado[7]);
		$this->setEntry_ipi($resultado[8]);
		$this->setEntry_icms($resultado[9]);
		$this->setEntry_falsolucro($resultado[10]);
		$this->setEntry_margemlucro($resultado[11]);
		$this->setEntry_quantidade('');
		$this->setEntry_total('');
		
		$this->desconecta($con);
	}
	
	function FocusOutQuantidade(){
		$this->calculaCustoTotal();
	}
	
	function FocusOutCusto(){
		$this->calculaPrecoVenda();
		$this->calculaCustoTotal();
	}
	
	function FocusOutVenda(){
        $this->calculaLucro();
	}
	
	function FocusOutTotal(){
        $this->calculaCustoUnitario();
	}
	
	function FocusOutMargemLucro(){
        $this->calculaNovoFalsoLucro();
        $this->calculaLucro();
	}
	
	function FocusOutFalsoLucro(){
        $this->calculaNovoMargemLucro();
        $this->calculaLucro();
	}
	
	function calculaNovoFalsoLucro(){
		// calcula novo falso lucro com base na margem de lucro informada
		$custo=$this->getEntry_custo();
		$margemlucro=$this->getEntry_margemlucro();
		$venda=$custo/(1-($margemlucro/100));  // by Eduardo A. Ewerton Perez eperez@prognum.com.br
		$this->setEntry_venda($venda); 
	}

	function calculaNovoMargemLucro(){
		// calcula novo margem lucro com base no falso de lucro informado
		$custo=$this->getEntry_custo();
		$falsolucro=$this->getEntry_falsolucro();
		
		$venda=$custo+($custo*($falsolucro/100));
	
		$this->setEntry_venda($venda);
	}
	
	function calculaLucro(){
   		// calcula margem de lucro e falso lucro
   		$venda=$this->getEntry_venda();
        if($venda>0){
            $custo=$this->getEntry_custo();
			if($custo==0){
				return;
			}
            
            $margemlucro=(($venda-$custo)/$venda)*100;            
			$falsolucro=(($venda-$custo)/$custo)*100;
			
			$this->setEntry_margemlucro($margemlucro);
			$this->setEntry_falsolucro($falsolucro);
	   }else{
       		$this->setEntry_margemlucro('');
			$this->setEntry_falsolucro('');
       }
	}
	
	function calculaCustoUnitario(){
		// calcula preco unitario dividindo o custo total pela quantidade
        $total=$this->getEntry_total();
        $quantidade=$this->getEntry_quantidade();
        if($quantidade>0){
	        $custo=$total/$quantidade;
	        $this->setEntry_custo($custo);
        }
	}
	
	function calculaCustoTotal(){
		// calcula valor do custo total quantidade X custo 
		$quantidade=$this->getEntry_quantidade();
		$custo=$this->getEntry_custo();
        $custototal=$custo*$quantidade;
        $this->setEntry_total($custototal);
	}

	function calculaPrecoVenda(){
        // calcula preco de venda baseado no preco de custo e a margem de lucro real
        $margemlucro=$this->getEntry_margemlucro();
        if(!empty($margemlucro) and $margemlucro>0 and $margemlucro<100 ){
            $custo=$this->getEntry_custo();        
			if($custo==0){
				return;
			}
            $venda=$custo/(1-($margemlucro/100));  // by Eduardo A. Ewerton Perez eperez@prognum.com.br
           
            $this->setEntry_venda($venda);
        }
	}
	
	function historico_codmerc(){
		$codmerc=$this->getEntry_codmerc();
		if(!$this->retornabusca4('codmerc','mercadorias','codmerc',$codmerc)){
			msg("Selecione uma mercadoria!");
			$this->limpa_mercadoria(false);			
			return;
		}
		$sql="SELECT c.codfor, f.nome AS fornecedor, c.data, e.quantidade, e.precooriginal AS precocusto " .
				"FROM entsai AS e " .
				"INNER JOIN entradas AS c ON e.codentsai=c.codentradas " .
				"INNER JOIN fornecedores AS f ON c.codfor=f.codigo " .
				"WHERE e.tipo='E' AND e.codmerc=$codmerc " .
				"ORDER BY c.data, f.nome ";
        $this->buscatab($sql, false, null, null, null, null, null);
	}
	
	function adicionarItemCompra(){
		
		$codmerc=$this->getEntry_codmerc();
		if(!$this->retornabusca4('codmerc','mercadorias','codmerc',$codmerc)){
			msg("Mercadoria nao encontrada!");
			$this->limpa_mercadoria(false);			
			return;
		}
		
		$this->verificaSeExisteAUX=false;
		$this->liststore_compra->foreach(array($this,'verificaSeExisteNaLista'), 0, $codmerc, false);
		
		if ($this->verificaSeExisteAUX){
			if(!confirma(false,"Esta mercadoria ja esta na lista de compras! Deseja adiciona-la mesmo assim?")){
				return;	
			}
		}
		
		$quantidade=$this->getEntry_quantidade();
		if($quantidade==0){
			msg("Quantidade deve ser maior que zero!");
			$this->entry_quantidade->grab_focus();
			return;
		}
		
		$custo=$this->getEntry_custo();
		if($custo==0){
			msg("Custo deve ser maior que zero!");
			$this->entry_custo->grab_focus();
			return;
		}
		
		$this->entry_codmerc->grab_focus();
		
		$this->liststore_compra->append(array(
			$codmerc,
			$this->getLabel_descricao(),
			$this->corrigeNumero($quantidade,'virgula3'),
			$this->getLabel_unidade(),
			$this->corrigeNumero($custo,'virgula4'),
			$this->corrigeNumero($this->getEntry_venda(),'virgula2'),
			$this->corrigeNumero($this->getEntry_ipi(),'virgula2'),
			$this->corrigeNumero($this->getEntry_icms(),'virgula2'),
			$this->corrigeNumero($this->getEntry_total(),'virgula2'),
			$this->corrigeNumero($this->getEntry_falsolucro(),'virgula2'),
			$this->corrigeNumero($this->getEntry_margemlucro(),'virgula2'),
			$this->corrigeNumero($this->getEntry_estoqueminimo(),'virgula3')
		));
		$this->somaListaCompra();
	}
	
	function removerItemCompra(){
		$selecionado=$this->treeview_compra->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_compra)){
            $this->liststore_compra->remove($iter);
        }
        $this->somaListaCompra();
	}
	
	function somaListaCompra(){
		$this->soma_custototal=0;
		$this->soma_ipi=0;
		$this->soma_icms=0;
		
		$this->liststore_compra->foreach(array($this,'somaListaCompraAUX'));
		
		$this->setEntry_ipi_total($this->soma_ipi);
		$this->setEntry_icms_total($this->soma_icms);
		$this->setLabel_custototal($this->soma_custototal);
		$this->somaListaCompra2();
	}
	function somaListaCompra2(){		
		$soma_total_compra=$this->soma_custototal+$this->soma_ipi+$this->getEntry_frete()+$this->getEntry_seguro()+$this->getEntry_outrasdespesas();
		$this->setLabel_total_compra($soma_total_compra);
	}
	
	function somaListaCompraAUX($store, $path, $iter){
		$custototal=$this->pegaNumero($this->liststore_compra->get_value($iter,8));
		$this->soma_custototal+=$custototal;
		
		$ipi=$this->pegaNumero($this->liststore_compra->get_value($iter,6));
		$this->soma_ipi+=($custototal/100)*$ipi;
		
		$icms=$this->pegaNumero($this->liststore_compra->get_value($iter,7));
		$this->soma_icms+=($custototal/100)*$icms;

	} 
	
	function FocusOutMeioPgto(){
		$codmeiopgto=$this->getEntry_codmeiopgto();
		if($placon=$this->retornabusca4('codplacon','meiopgto','codigo',$codmeiopgto)){
			$this->setEntry_codplacon($placon);
			$this->setLabel_codplacon($this->retornabusca4('descricao','placon','codigo',$placon));
		}
		return false;
	}
	
	function adicionarParcelaPgto(){
		$valor=$this->getEntry_valor();
		if($valor<=0){
			msg("Valor deve ser maior que zero!");
			$this->entry_valor->grab_focus();
			return;
		}
		
		$this->somaListaPgto();
		$total_pgto=$valor+$this->soma_pagamentos;
		$total_compra=$this->getLabel_total_compra();
		if($total_pgto>$total_compra){
			msg("Valor das parcelas maior que valor da compra!! Digite um valor menor.");
			$this->entry_valor->grab_focus();
			return;
		}
		
		$dtvencimento=$this->getEntry_dtvencimento();
		if(!$this->valida_data($dtvencimento)){
            msg("Data de vencimento incorreta!");
            $this->entry_dtvencimento->grab_focus();
            return;
        }
		
		$codmeiopgto=$this->getEntry_codmeiopgto();
		if(!$this->retornabusca4('codigo','meiopgto','codigo',$codmeiopgto)){
			msg('Meio de Pagamento nao encontrado!');
			$this->entry_codmeiopgto->grab_focus();
			return;
		}
		
		$codplacon=$this->getEntry_codplacon();
		if(!$this->retornabusca4('codigo','placon','codigo',$codplacon)){
			msg('Plano de contas nao encontrado!');
			$this->entry_codplacon->grab_focus();
			return;
		}
		
		$this->liststore_pgto->append(array(
			$this->getEntry_documento(),
			$codmeiopgto,
			$this->getLabel_codmeiopgto(),
			$this->corrigeNumero($valor,'virgula2'),
			$dtvencimento,
			$codplacon,
			$this->getLabel_codplacon(),
			$this->getEntry_obs()
		));
		
		$this->entry_documento->grab_focus();
		$this->setLabel_total_pgtos($total_pgto);
	}
	
	function excluirParcelaPgto(){
		$selecionado=$this->treeview_pgto->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_pgto)){
            $this->liststore_pgto->remove($iter);
        }
        $this->somaListaPgto();
	}
	
	function somaListaPgto(){
		$this->soma_pagamentos=0;
		$this->liststore_pgto->foreach(array($this,'somaListaPgtoAUX'));
		$this->setLabel_total_pgtos($this->soma_pagamentos);
	}
	
	function somaListaPgtoAUX($store, $path, $iter){
		$this->soma_pagamentos+=$this->pegaNumero($store->get_value($iter,3));
	}
	
	function comprar(){		 
		if(!confirma(false,'Deseja efetuar esta compra?')){
			return;
		}
			
		if($this->numero_rows_liststore($this->liststore_compra)==0){
            msg('Adicione mercadorias na lista de compra');
            return;
        }
        if($this->numero_rows_liststore($this->liststore_pgto)==0){
            msg('Adicione parcelas de pagamento da compra');
            return;
        }
        $total_pgto=$this->getLabel_total_pgtos();
		$total_compra=$this->getLabel_total_compra();
		if($total_pgto<>$total_compra){
			msg("Total da compra nao pode ser diferente do total das parcelas de pagamento");
			return;
		}
        
        $data_compra=$this->getEntry_data();
        if(!$this->valida_data($data_compra)){
            msg("Data da compra incorreta!");
            $this->entry_data->grab_focus();
            return;
        }
        $this->data_compra=$this->corrigeNumero($data_compra,"dataiso");
        
        $this->fornecedor=$this->getEntry_fornecedor();
        if(!$this->retornabusca4('codigo','fornecedores','codigo',$this->fornecedor)){
			msg('Fornecedor nao encontrado!');
			$this->entry_fornecedor->grab_focus();
			return;
		}
        
        $this->con_compras=$this->conecta();
        
        // registra campo ultima compra do fornecedor
        $this->con_compras->Update('fornecedores',array(
        	array('ultcompra', $this->data_compra)
        ), "WHERE codigo='$this->fornecedor' ");
        
        $horafinal=date("H:i:s");
        $this->codentsaiAUX=$this->con_compras->Insert('entradas',array(
        	array('data',$this->data_compra), 
        	array('codfor',$this->fornecedor),
        	array('totalmerc',$total_compra),
        	array('hora',$horafinal),
        ));
        
        // le lista de mercadorias		
		$this->liststore_compra->foreach(array($this,'compraAUXlistaCompra'));

        // le lista de pagamentos
		$this->liststore_pgto->foreach(array($this,'compraAUXlistaPgto'));
		
		$this->con_compras->Disconnect();
		$this->limpar_geral(false);		
		
		msg("Compra $this->codentsaiAUX efetuada com sucesso");         
	}
	function compraAUXlistaCompra($store, $path, $iter){

		$codmerc=$store->get_value($iter,0);
		$quantidade=$this->pegaNumero($store->get_value($iter,2));
		
		$preco_custo=$this->pegaNumero($store->get_value($iter,4));
		
		$preco_total=$this->pegaNumero($store->get_value($iter,8));
		
		$preco_venda=$this->pegaNumero($store->get_value($iter,5));
		$preco_vendaDB=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
		if($preco_vendaDB<>$preco_venda){
			$ultimaalteracao=true;
		}else{
			$ultimaalteracao=false;
		}
		$ipi=$this->pegaNumero($store->get_value($iter,6));
		$icms=$this->pegaNumero($store->get_value($iter,7));
		
		$falso_lucro=$this->pegaNumero($store->get_value($iter,9));		
		$margem_lucro=$this->pegaNumero($store->get_value($iter,10));
		$estoque_minimo=$this->pegaNumero($store->get_value($iter,11));

		// grava itens na tabela de entradas/saidas/orcamentos/pedidos
		$this->con_compras->Insert('entsai',array(
			array('codentsai',$this->codentsaiAUX),
			array('tipo','E'),
			array('codmerc',$codmerc),
			array('precooriginal',$preco_custo),
			array('quantidade',$quantidade),
		));

		// pega o estoque atual
		$estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);
		// aumenta o estoque atual
		$estoqueatual+=$quantidade;		
		// pega o antigo preco de custo medio
		$customedio=$this->retornabusca4('customedio','mercadorias','codmerc',$codmerc);                        
        // calcula o preco de custo medio
        if($customedio==0){
        	$customedio=$preco_custo;
        }else{
        	$customedio=($customedio+$preco_custo)/2;
        }

        // grava o preco de custo medio e o fornecedor
        $sqlarray=array(
        	array('estoqueminimo', $estoque_minimo),
        	array('ipi', $ipi),
        	array('icms', $icms),
        	array("estoqueatual", $estoqueatual),
        	array("ultimacompra",$this->data_compra),
        	array('codfor', $this->fornecedor),
        	array('customedio', $customedio),
        	array('precocusto', $preco_custo),
        	array('precovenda', $preco_venda),
        	array('falsolucro', $falso_lucro),
        	array('margemlucro', $margem_lucro)
        );
        
        if($ultimaalteracao){
        	array_push($sqlarray, 
				array('ultimaaltera', date("Y-m-d"))				
			);
    	}
        $etc=" WHERE codmerc='$codmerc' ";
        $this->con_compras->Update('mercadorias',$sqlarray,$etc);
	}
	function compraAUXlistaPgto($store, $path, $iter){
		$dtvencimento=$this->corrigeNumero($store->get_value($iter,4),"dataiso");
		$valor=$this->pegaNumero($store->get_value($iter,3));
		$nnf=$store->get_value($iter,0);
		$tipo='E';
		$codmeiopgto=$store->get_value($iter,1);
		$meiopgto=$store->get_value($iter,2);
		$placon=$store->get_value($iter,5);
		$obs=$store->get_value($iter,7);

		//grava no contas a pagar
		// se integracao estoque-financeiro estiver ativada
		if($this->retorna_OPCAO('integraestoquefinanceiro')){
			$this->con_compras->Insert('pagar',array(
				array('fiscal',$nnf),
				array('data_c',date("Y-m-d")),
				array('data_v',$dtvencimento),
				array('valor',$valor),
				array('saldo',$valor),
				array('descr',"COMPRA COD. $this->codentsaiAUX DE MERCADORIA EM $meiopgto',", true),
				array('codorigem',$this->fornecedor),
				array('codplacon',$placon),
				array('obs',$obs, true),
				array('codentradas',$this->codentsaiAUX)
			));
		}

		// grava tipos de pagamentos
		$this->con_compras->Insert('movpagamentos',array(
			array('codorigem',$this->codentsaiAUX),
			array('tipo',$tipo),
			array('nnf',$nnf),
			array('codmeiopgto',$codmeiopgto),
			array('meio',$meiopgto),
			array('valor',$valor),
			array('data',$dtvencimento),
			array('data_c',date("Y-m-d"))
		));
	}
	
	function pedir(){
		msg("Funcao de pedidos ainda nao implementada");
	}
	
	function cancelar(){
		msg("Funcao de cancelar ainda nao implementada");
	}
}

?>
