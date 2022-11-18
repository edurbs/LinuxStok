<?php
class entrega extends funcoes {
	function entrega(){
        global $atalho_padrao;
        if($atalho_padrao==NULL){
        	$atalho_padrao = new GtkAccelGroup();
        }
        $this->xml=$this->carregaGlade('entrega');

        $this->scrolledwindow_venda=$this->xml->get_widget('scrolledwindow_venda');
        $this->liststore_venda=new GtkListStore(
            GObject::TYPE_STRING, // 0 codmerc
            GObject::TYPE_STRING, // 1 descricao
            GObject::TYPE_STRING, // 2 quant
            GObject::TYPE_STRING, // 3 un
            GObject::TYPE_STRING, // 4 preco orig
            GObject::TYPE_STRING, // 5 preco com desc.
            GObject::TYPE_STRING, // 6 preco total
            GObject::TYPE_STRING, // 7 cod venda - codsaidas
            GObject::TYPE_STRING, // 8 data venda
            GObject::TYPE_STRING // 9 codigo entrega (escondido)
        );
        $this->treeview_venda=new GtkTreeView($this->liststore_venda);
        $this->add_coluna_treeview(
            array('Cod.', 'Descricao', 'Quantidade', 'UN', 'Preco Orig.', 'Preco Vendido', 'Preco Total', 'Cod.Venda', 'Data', ''),
            $this->treeview_venda,
            array(999,999,999,999,999,999,999,999,0)
        );
        $this->scrolledwindow_venda->add($this->treeview_venda);
        $this->scrolledwindow_venda->show_all();
        $this->treeview_venda->connect('key-press-event', array(&$this,clistVendaKey));
        $this->treeview_venda->set_rules_hint(TRUE);
        
        $this->scrolledwindow_entrega=$this->xml->get_widget('scrolledwindow_entrega');
        $this->liststore_entrega=new GtkListStore(
            GObject::TYPE_STRING, // 0 codmerc
            GObject::TYPE_STRING, // 1 descricao
            GObject::TYPE_STRING, // 2 quant
            GObject::TYPE_STRING, // 3 un
            GObject::TYPE_STRING, // 4 preco orig
            GObject::TYPE_STRING, //  5 preco com desc.
			GObject::TYPE_STRING, // 6 preco total
			GObject::TYPE_STRING, // 7 cod venda - codsaidas
			GObject::TYPE_STRING, // 8 data venda
			GObject::TYPE_STRING // 9 codigo entrega (escondido)
        );
        $this->treeview_entrega=new GtkTreeView($this->liststore_entrega);
        $this->add_coluna_treeview(
            array('Cod.', 'Descricao', 'Quantidade', 'UN', 'Preco Orig.', 'Preco Vendido', 'Preco Total', 'Cod.Venda', 'Data',''),
            $this->treeview_entrega,
            array(999,999,999,999,999,999,999,999,0)
        );
        $this->scrolledwindow_entrega->add($this->treeview_entrega);
        $this->scrolledwindow_entrega->show_all();
        $this->treeview_entrega->connect('key-press-event', array($this,clistEntregaKey));
        $this->treeview_entrega->set_rules_hint(TRUE);
		
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        
        $this->button_vendas=$this->xml->get_widget("button_vendas");
        $this->button_vendas->connect_simple('clicked',array($this, 'carregaVendas'));
        
        $this->button_entregas=$this->xml->get_widget("button_entregas");
        $this->button_entregas->connect_simple('clicked',array($this, 'carregaEntregas'));
        
        $this->button_cancelar=$this->xml->get_widget("button_cancelar");
        $this->button_cancelar->connect_simple('clicked',confirma, array($this, 'cancelarEntregas'),"Deseja relamente cancelar a entrega das mercadorias acima?",null);
        
        $this->button_limpa=$this->xml->get_widget("button_limpa");
        $this->button_limpa->connect_simple('clicked',confirma, array(&$this, 'limpa'),"Deseja limpar os campos da tela?",null);
        
        $this->label_venda=$this->xml->get_widget("label_venda");        

        $this->label_entrega=$this->xml->get_widget("label_entrega");        

        
        $this->label_cliente=$this->xml->get_widget("label_cliente");        

        $this->label_codcli=$this->xml->get_widget("label_codcli");
		
		$this->label_endereco=$this->xml->get_widget("label_endereco");
		$this->label_codendereco=$this->xml->get_widget("label_codendereco");
		$this->entry_endereco=new GtkEntry();
		$this->entry_endereco->connect_simple('changed', array($this,'eventoEndereco'));
		
		$this->entry_codcli=new GtkEntry();
		$this->entry_codcli->connect_simple('changed', array($this,'eventoCodCli'));
        //$this->label_codcli->connect_simple('event', array(&$this,'eventoCodCli'));
        
		$this->button_2via=$this->xml->get_widget("button_2via");
		$this->button_2via->connect_simple('clicked',array($this,'segunda_via'));
	
        $this->button_entregar=$this->xml->get_widget("button_entregar");
        $this->button_entregar->connect_simple('clicked',confirma, array($this,ConfirmaEntrega),"Deseja efetuar a entrega?",null);
        
        $this->button_endereco=$this->xml->get_widget("button_endereco");
        $this->button_endereco->connect_simple('clicked',array($this,'buscaEndereco'));
        
        $this->button_cliente=$this->xml->get_widget("button_cliente");
        $this->button_cliente->connect_simple('clicked', 
            array($this,buscatab), 
            'select codigo, nome from clientes', 
            true,
            $this->entry_codcli, 
            $this->label_cliente,
            "clientes",
            "nome",
            "codigo"
        );
       
        // F8
        $this->button_CancelaTudo=$this->xml->get_widget("button_CancelaTudo");
        $this->button_CancelaTudo->connect_simple('clicked',array($this,'CancelaTudo'));
        
        // F7 OK
        $this->button_CancelaItem=$this->xml->get_widget("button_CancelaItem");
        $this->button_CancelaItem->connect_simple('clicked',array($this,'CancelaItem'));
        
        // F6
        $this->button_EntregaItemParcial=$this->xml->get_widget("button_EntregaItemParcial");
        $this->button_EntregaItemParcial->connect_simple('clicked',array($this,'EntregaItemParcial'));
        $this->entry_quantentregar=$this->xml->get_widget("entry_quantentregar");
        //$this->entry_quantentregar->connect('key-press-event', array($this, 'mascaraNew'),'virgula3');
        
        // F5 OK
        $this->button_EntregaItem=$this->xml->get_widget("button_EntregaItem");
        $this->button_EntregaItem->connect_simple('clicked',array($this,'EntregaItem'));
        
        // F4 OK
        $this->button_EntregaTudo=$this->xml->get_widget("button_EntregaTudo");
        $this->button_EntregaTudo->connect_simple('clicked',array($this,'EntregaTudo'));
        
        $this->limpa();
        //$this->janela->show();
        
		

		// ativar atalhos para o teclado visto que sao pedidos ao usar a funcao reparent
		
    	// atalho buscar mercadoria F1    	 
    	$this->button_EntregaTudo->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F4, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_EntregaItem->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F5, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_EntregaItemParcial->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F6, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_CancelaItem->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F7, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_CancelaTudo->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F8, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_cliente->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F2, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_endereco->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F3, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_limpa->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F9, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_entregar->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F12, 0, Gtk::ACCEL_VISIBLE);
	}
	
	function cancelarEntregas(){
		// cancela a entrega dos itens jah entregues
		if($this->numero_rows_liststore($this->liststore_venda)==0){
			msg("Lista de venda vazia!");
			return;
		}
		$this->liststore_venda->foreach(array($this,'CancelarAUX'));
		
	}
    function CancelarAUX($store, $path, $iter){
        $codmerc=$store->get_value($iter,0);
        $quantidade=$this->pegaNumero($store->get_value($iter,2));
        $precooriginal=$this->pegaNumero($store->get_value($iter,4));
        $precocomdesconto=$this->pegaNumero($store->get_value($iter,5));
        $codsaidas=$this->pegaNumero($store->get_value($iter,7));
        $codentregas=$this->pegaNumero($store->get_value($iter,9));
		$futura=$this->retornabusca4('futura','saidas','codsaidas',$codsaidas);

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        
        // volta quantidades para o entsai
        $sql="SELECT entregue FROM entsai WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc'";                        
        $resultado=$con->Query($sql);            
        $i = $con->FetchRow($resultado);
        $quantentregue=$i[0];		
        $novaquantentregue=$quantentregue-$quantidade; // diminui a quantidade entregue
        $sql="UPDATE entsai SET entregue='$novaquantentregue' WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc' ";
        if(!$con->Query($sql)){
        	msg("Erro ao update entsai");
        }

		// retira quantidades do entrega_itens
		$sql="SELECT quantidade FROM entrega_itens WHERE codsaidas='$codsaidas' AND codmerc='$codmerc' AND codentregas='$codentregas' ";
        $resultado=$con->Query($sql);            
        $i = $con->FetchRow($resultado);
        $EXquantentregue=$i[0];
        $EXnovaquantentregue=$EXquantentregue-$quantidade; // diminui a quantidade entregue
        $sql="UPDATE entrega_itens SET quantidade='$EXnovaquantentregue' WHERE codsaidas='$codsaidas' AND codmerc='$codmerc'  AND codentregas='$codentregas' ";
        if(!$con->Query($sql)){
        	msg("Erro ao update entrega_itens");
        }

		
        // retorna ao estoque se for venda tipo futura
        if($futura=='1'){
        	$estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);       
        	$estoqueatual+=$quantidade; // aumenta a quantidade entregue
        	$sql="UPDATE mercadorias SET estoqueatual='$estoqueatual' WHERE codmerc='$codmerc' ";	
        	$con->Query($sql);
        }
 
		$con->Disconnect();
		$this->limpa();
		msg("Cancelamento de entrega efetuado com sucesso!");
	}
	
	function carregaEntregas(){
		// carrega lista de itens entregues
		$this->getCodcli();
		if(empty($this->codcli)) return;
		// lista todas mercadorias a entregar para este cliente
		
		$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();        
        $sql="SELECT e.codmerc, m.descricao, e.quantidade, m.unidade, e.precooriginal, e.precocomdesconto, e.codsaidas, s.data, e.codentregas FROM entrega_itens AS e  INNER JOIN entregas AS s ON s.codentregas=e.codentregas INNER JOIN mercadorias AS m  ON m.codmerc=e.codmerc INNER JOIN clientes AS c ON s.codcli=c.codigo WHERE e.quantidade>0 AND s.codcli='$this->codcli' ORDER BY m.descricao";
        $resultado=$con->Query($sql);            
        $this->liststore_venda->clear();
        $this->liststore_entrega->clear();
        while($i = $con->FetchRow($resultado)) {
            $this->liststore_entrega->append(
                array(
                    $i[0], //codmerc
                    $i[1], // descricao
                    number_format($i[2], 3, ',', ''), // quantidade
                    $i[3], // unidade
                    number_format($i[4], 2, ',', ''), // precooriginal
                    number_format($i[5], 2, ',', ''), // precocondesconto
                    number_format($i[5]*$i[2], 2, ',', ''), // preco com desconto * quantidade
                    $i[6], // codsaidas
                    $this->corrigeNumero($i[7],'data'), // data
                    $i[8] // codigo entregas
                )
            );
        }
        $con->Disconnect();
        
        $this->updatePrecoTotalVendaEntrega();
		$this->desligarBotaoEntregar();
	}
	function desligarBotaoEntregar(){
		$this->button_entregar->set_sensitive(FALSE);
		$this->button_2via->set_sensitive(FALSE);
		$this->button_cancelar->set_sensitive(TRUE);
	}
	function desligarBotaoCancelar(){
		$this->button_cancelar->set_sensitive(FALSE);
		$this->button_entregar->set_sensitive(TRUE);
		$this->button_2via->set_sensitive(TRUE);
	}
	function segunda_via(){
		$tabela="entregas";
		if($codigo=inputdialog("Digite o codigo")){
			if($achou=$this->retornabusca4('cod'.$tabela,$tabela,"cod".$tabela,$codigo)){
				// imprime em varias vias
				$vias=$this->retorna_OPCAO("viasrecibo");
				if(!is_numeric($vias)){
					$vias=1;
				}
				$slogan=" --==ENTREGA==--";
				$titulo="Entrega";
				$tabela2="entrega_itens";
				$codtabela="codentregas";
				$codtabela2="codentregas";
				$tipoPPG=null;
				$assinatura=false;
        		for($i=0;$i<$vias;$i++){
            		confirma(array($this,'imprimirRecibo'),'Deseja imprimir ESTA via do recibo?',$codigo,$tabela, $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura);
            
        		}
			}else{
				msg("Devolucao nao encontrada.");
			}
		}

	}	    
    function CancelaItem($remover=true){
        $selecionado=$this->treeview_entrega->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_entrega)){
            $quantidade=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_entrega,2));
            $codmercD=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_entrega,0));            
        // verifica se o produto ja existe na lista de venda
	        $this->verificaSeExisteAUX=false;
			$this->liststore_venda->foreach( array($this,'verificaSeExisteNaListaEntrega'), $codmercD);
			if ($this->verificaSeExisteAUX){
				// ja existe na lista de venda
	            $novaquant=$this->pegaNumero($this->liststore_venda->get_value($this->iterSeExiste,2));
	            
	            //$somaquant=number_format($novaquant+$quantidade, 3, ',', '');
	            $somaquant=$novaquant+$quantidade;
	            $precodesconto=$this->pegaNumero($this->liststore_venda->get_value($this->iterSeExiste,5));
	            $precototalunit=$precodesconto*$somaquant;
            
	            $this->liststore_venda->set($this->iterSeExiste,6,number_format($precototalunit,2,',',''));
				$this->liststore_venda->set($this->iterSeExiste,2,number_format($somaquant,2,',',''));

	            if($remover) $this->liststore_entrega->remove($iter);
	        }else{
				// nao existe na lista de venda
	            $this->liststore_venda->prepend(
	                array(                    
                    		$this->get_valor_liststore($selecionado,$this->liststore_entrega,0),
 		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,1),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,2),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,3),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,4),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,5),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,6),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,7),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,8),
    		                $this->get_valor_liststore($selecionado,$this->liststore_entrega,9)
    		            )
    		        );
    		        if($remover) $this->liststore_entrega->remove($iter);
	        }
	        
	    }
	    $this->updatePrecoTotalVendaEntrega();
    }

    function CancelaTudo(){
		if($this->numero_rows_liststore($this->liststore_entrega)==0){
			msg("Lista de entrega vazia!");
			return;
		}
        $this->liststore_entrega->foreach(array($this,'CancelaTudoAUX'));
        $this->liststore_entrega->clear();
        $this->updatePrecoTotalVendaEntrega();        
    }
    
    function CancelaTudoAUX($store, $path, $iter){
    		$selecionado=$this->treeview_entrega->get_selection();
    		$selecionado->select_iter($iter);
    		$this->CancelaItem(false);
    		
    }    
    

	function limpa(){
        // funcao que limpa toda a tela
        $this->liststore_venda->clear();
        $this->liststore_entrega->clear();
        $this->label_codcli->set_text('');
        $this->entry_codcli->set_text('');
        $this->entry_endereco->set_text('');
        $this->label_cliente->set_text('');
        $this->label_venda->set_text('0,00');
        $this->label_entrega->set_text('0,00');
        $this->label_codendereco->set_text('');
        $this->label_endereco->set_text('');
        $this->entry_quantentregar->set_text('');
	}
    

    function clistEntregaKey($widget,$evento){
        // muda o foto para a clista de venda
        $tecla=$evento->keyval;
        //if($tecla==GDK::KEY_Tab or $tecla==GDK::KEY_KP_Tab or $tecla==GDK::KEY_ISO_Left_Tab or $tecla==GDK::KEY_3270_BackTab){
        if($tecla==65289 or $tecla==65417 or $tecla==65056 or $tecla==64773){
            $this->treeview_venda->grab_focus();
            return true;
        }
        return false;
    }
    function clistVendaKey($widget,$evento){
        // muda o foto para a clista de devolucao
        $tecla=$evento->keyval;
        if($tecla==65289 or $tecla==65417 or $tecla==65056 or $tecla==64773){
            $this->treeview_entrega->grab_focus();
            return true;
        }
        return false;
    }
	function carregaVendas(){
		$this->getCodcli();
		if(empty($this->codcli)) return;
		// lista todas mercadorias a entregar para este cliente
		
		$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();        
        $sql="SELECT e.codmerc, m.descricao, e.quantidade-e.entregue AS saldo, m.unidade, e.precooriginal, e.precocomdesconto, s.codsaidas, s.data FROM entsai AS e INNER JOIN mercadorias AS m ON (m.codmerc=e.codmerc) INNER JOIN saidas AS s ON s.codsaidas=e.codentsai INNER JOIN clientes AS c ON c.codigo=s.codcli WHERE (e.quantidade-e.entregue)>0 AND e.entregue<e.quantidade AND e.quantidade>0 AND e.tipo='S' AND s.codcli='$this->codcli' ORDER BY s.codsaidas";
        $resultado=$con->Query($sql);            
        $this->liststore_venda->clear();
        $this->liststore_entrega->clear();
        while($i = $con->FetchRow($resultado)) {
            $this->liststore_venda->append(
                array(
                    $i[0],
                    $i[1],
                    number_format($i[2], 3, ',', ''),
                    $i[3],
                    number_format($i[4], 2, ',', ''),
                    number_format($i[5], 2, ',', ''),
                    number_format($i[5]*$i[2], 2, ',', ''), // preco com desconto * quantidade
                    $i[6],
                    $this->corrigeNumero($i[7],'data'),
                    null
                )
            );
        }
        $con->Disconnect();
        
        $this->updatePrecoTotalVendaEntrega();
        $this->desligarBotaoCancelar();		
	}
	function getCodcli(){
		$this->codcli=$this->entry_codcli->get_text();
		$this->label_codcli->set_text($this->codcli);
	}
    function eventoCodCli(){
        $this->carregaVendas();
        // chama endereco
        $this->buscaEndereco();		
    }
    function buscaEndereco(){
    	$this->getCodcli();
    	if(empty($this->codcli)) return;
    	$this->entry_endereco->set_text('');
    	$sql="select descricao, endereco, numero, complemento, bairro, cidade, estado, cep, telefone, fax, celular from cadastro2enderecos as cadastro2enderecos where cadastro='clientes' and codigo='$this->codcli'";
        $this->buscatab($sql, true, $this->entry_endereco, null, 'cadastro2enderecos',"endereco",'descricao');
    }
    function eventoEndereco(){
        if(empty($this->codcli)){
        	//msg("Selecione primeiro o cliente");
        	return;
        }
        $descricao=$this->entry_endereco->get_text();
        if(empty($descricao)){
        	$descricao=$this->label_codendereco->get_text();	
        }
        if(!empty($this->codcli) and !empty($descricao)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();
            $sql="SELECT endereco, numero FROM cadastro2enderecos WHERE codigo='$this->codcli' AND cadastro='clientes' AND descricao='$descricao'";
            $resultado=$con->Query($sql);
            $i = $con->FetchRow($resultado);
            $texto=$i[0].", ".$i[1];
            $this->label_endereco->set_text($texto);
            $this->label_codendereco->set_text($descricao);
            $con->Disconnect();
            $this->treeview_venda->grab_focus();
        }
    }
    function EntregaItem(){
        $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $quantidade=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_venda,2));
            if($quantidade>0){
                $this->EntregaItem2(false,$iter);
                $this->liststore_venda->remove($iter);
                //$this->updatePrecoTotalVendaEntrega();
            }else{            
                msg('Mercadoria com quantidade ZERO');                
            }
        }
        
    }
    
    function EntregaTudo(){
        	if($this->numero_rows_liststore($this->liststore_venda)==0){
    			msg("Lista de venda vazia!");
    			return;
    		}
        $this->liststore_venda->foreach(array($this,'EntregaTudoAUX'));
        $this->liststore_venda->clear();
        $this->updatePrecoTotalVendaEntrega();
    }
    
    function EntregaTudoAUX($store, $path, $iter){
    		$this->EntregaItem2(false,$iter);
    }
    
    function EntregaItemParcial(){
        $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $quantidade=$this->pegaNumero($this->liststore_venda->get_value($iter,2));
            if($quantidade>0){
            		$this->EntregaItem2(true,$iter);
            }else{            
                msg('Mercadoria com quantidade ZERO');                
            }
        }
    }
    
    function EntregaItem2($parcial,$iter){
        $quantidade=$this->pegaNumero($this->liststore_venda->get_value($iter,2));
        if($parcial){
            $spin=$this->pegaNumero($this->entry_quantentregar);
            if($spin>$quantidade){
            		//echo "$spin>$quantidade";
            		msg("Quantidade deve ser menor ou igual a vendida");
            		return;
            }elseif($spin==0){
            		msg("Especifique a quantidade parcial a ser entregue");
            		return;
            }
        }else{
            $spin=$quantidade; // pega quantidade total do item
        }        
        
        $novaquant=number_format($quantidade-$spin, 3, ',', '');

        $codmerc=$this->pegaNumero($this->liststore_venda->get_value($iter,0));
        // devolve na lista

        $this->liststore_venda->set($iter,2,$novaquant);
   
        // update no preco total unitario
        $precodesconto=$this->pegaNumero($this->liststore_venda->get_value($iter,5));
        $precototalunit=$precodesconto*$novaquant;
        $this->liststore_venda->set($iter,6,number_format($precototalunit,2,',',''));
        

        // verifica se o produto ja existe na lista de devolucao
        $this->verificaSeExisteAUX=false;
		$this->liststore_entrega->foreach( array($this,'verificaSeExisteNaListaEntrega'), $codmerc);
		if ($this->verificaSeExisteAUX){
            // se o codmerc ja existir na lista de devolucao
            // pega quantidade
			$OLDquant=$this->liststore_entrega->get_value($this->iterSeExiste,2);
            // soma quantidade
            $novaquant=$OLDquant+$spin;
            // altera quantidade na clist
            $this->liststore_entrega->set($this->iterSeExiste,2,number_format($novaquant,3,',',''));
            // calcula novo preco total unitario
            $precototalunitentregue=$precodesconto*$novaquant;
            // altera preco total unitario na lista
            $this->liststore_entrega->set($this->iterSeExiste,6,number_format($precototalunitentregue,2,',',''));
        }else{
            $precototalunitentregue=$precodesconto*$spin;
            // coloca na lista de devolucao
            $this->liststore_entrega->prepend(
                array(
                		$this->liststore_venda->get_value($iter,0),                    
                		$this->liststore_venda->get_value($iter,1),
                		number_format($spin, 3, ',', ''),
                		$this->liststore_venda->get_value($iter,3),
                		$this->liststore_venda->get_value($iter,4),
                		$this->liststore_venda->get_value($iter,5),
                		number_format($precototalunitentregue,2,',',''),
                		$this->liststore_venda->get_value($iter,7),
                		$this->liststore_venda->get_value($iter,8),
                		$this->liststore_venda->get_value($iter,9)
                )
            );        

        }
        $this->updatePrecoTotalVendaEntrega();
    }
    
    function verificaSeExisteNaListaEntrega($store, $path, $iter, $codmerc){
		// $coluna = coluna da lista pra verifica se ja existe
		// $descricao = valor pra procurar se ja existe
		// exemplo: $this->liststore_contato->foreach( array($this,'verificaSeExisteNaLista'), 0, $nome);
		$this->verificaSeExisteAUX=false;
		$tmp=$store->get_value($iter,0);
		$this->iterSeExiste=null;
		if($tmp==$codmerc){
			$this->verificaSeExisteAUX=true; // retorna true pra parar o foreach
			$this->iterSeExiste=$iter;			
			return $this->verificaSeExisteAUX;
		}
		return $this->verificaSeExisteAUX;
	}
    
    function ConfirmaEntrega(){
		global $usuario;
        // grava no entsai com tipo D
        // grava devolucoes o total da devolucao
        
        // devolve no banco de dados
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();        
        
        $hora=date("H:i:s");
        $data=date("Y-m-d");
        
        $endereco=$this->label_codendereco->get_text();
		if(empty($endereco)){
			msg("Sem codigo do endereco");
			return;
		}
        $vendedor=$usuario;

        $totalnfE=$this->pegaNumero($this->label_entrega);
        
		if($this->numero_rows_liststore($this->liststore_entrega)==0){
			msg("Lista de entrega vazia!");
			return;
		}
        
        $descontoE=0;
        $totalmercE=$totalnfE;
        
        $codentregas=$con->QueryLastCod("INSERT INTO entregas (data, codcli, endereco,  hora, totalmerc, desconto, totalnf, vendedor) VALUES ('$data','$this->codcli','$endereco','$hora','$totalmercE','$descontoE','$totalnfE','$vendedor') ");
 		
 		
		$this->liststore_entrega->foreach(array($this,'ConfirmaEntregaAUX'), $codentregas);
        
        
        $con->Disconnect();
		$this->limpa();
		msg("Entrega efetuada com sucesso!");

        $slogan=" --==ENTREGA==--";
        $titulo="Entrega";
        $tabela2="entrega_itens";
        $codtabela="codentregas";
        $codtabela2="codentregas";
        $tipoPPG=null;
        $assinatura=true;
            
        for($i=0;$i<$this->retorna_OPCAO("viasrecibo");$i++){
            confirma(array($this,'imprimirRecibo'),'Deseja imprimir as vias do recibo?', $codentregas, 'entregas', $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura);
        }
       
    }
    
   function ConfirmaEntregaAUX($store, $path, $iter, $codentregas){
        $codmerc=$store->get_value($iter,0);
        $quantidade=$this->pegaNumero($store->get_value($iter,2));
        $precooriginal=$this->pegaNumero($store->get_value($iter,4));
        $precocomdesconto=$this->pegaNumero($store->get_value($iter,5));
        $codsaidas=$this->pegaNumero($store->get_value($iter,7));
		$futura=$this->retornabusca4('futura','saidas','codsaidas',$codsaidas);

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT entregue FROM entsai WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc'";
                        
        $resultado=$con->Query($sql);            
        $i = $con->FetchRow($resultado);
        $quantentregue=$i[0];
		
        $novaquantentregue=$quantentregue+$quantidade; // aumenta a quantidade entregue
        $sql="UPDATE entsai SET entregue='$novaquantentregue' WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc' ";
        if(!$con->Query($sql)){
        	msg("Erro ao update entsai");
        }

        $sql="INSERT INTO entrega_itens (codentregas, codmerc, precooriginal, precocomdesconto, quantidade, codsaidas) VALUES ('$codentregas', '$codmerc', '$precooriginal', '$precocomdesconto', '$quantidade', '$codsaidas') ";
        if(!$con->Query($sql)){
        	msg("Erro ao inser entrega_itens");
        }

        // baixa estoque se for venda tipo futura
        if($futura=='1'){
        	$estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);       
        	$estoqueatual-=$quantidade; // baixa a quantidade entregue
        	$sql="UPDATE mercadorias SET estoqueatual='$estoqueatual' WHERE codmerc='$codmerc' ";	
        	$con->Query($sql);
        }
		$con->Disconnect();
	}
	function updatePrecoTotalVendaEntrega(){
    		
        // update no pre� total da venda
        $this->somaVenda=0;
        $this->liststore_venda->foreach( array($this,'updatePrecoTotalVenda'));

		$this->somaEntrega=0;
		$this->liststore_entrega->foreach( array($this,'updatePrecoTotalEntrega'));

        $this->label_venda->set_text(number_format($this->somaVenda,2,',',''));
        $this->label_entrega->set_text(number_format($this->somaEntrega,2,',',''));
    }
    
    function updatePrecoTotalVenda($store, $path, $iter){
    		$precototalunit=$this->pegaNumero($store->get_value($iter,6));
    		$this->somaVenda+=$precototalunit;
    }
    function updatePrecoTotalEntrega($store, $path, $iter){
    		$precototalunit=$this->pegaNumero($store->get_value($iter,6));
    		$this->somaEntrega+=$precototalunit;
    }
}
?>

