<?php
class devolucao extends funcoes {
	function devolucao(){
        
        $this->xml=$this->carregaGlade('devolucao');

        $this->scrolledwindow_venda=$this->xml->get_widget('scrolledwindow_venda');
        $this->liststore_venda=new GtkListStore(
            Gobject::TYPE_STRING, // 0 codmerc
            Gobject::TYPE_STRING, // 1 descricao
            Gobject::TYPE_STRING, // 2 quant
            Gobject::TYPE_STRING, // 3 un
            Gobject::TYPE_STRING, // 4 preco orig
            Gobject::TYPE_STRING, //  5 preco com desc.
            Gobject::TYPE_STRING // 6 preco total
        );
        $this->treeview_venda=new GtkTreeView($this->liststore_venda);
        $this->add_coluna_treeview(
            array('Cod.', 'Descricao', 'Quantidade', 'UN', 'Preco Orig.', 'Preco Vendido', 'Preco Total'),
            $this->treeview_venda
        );
        $this->scrolledwindow_venda->add($this->treeview_venda);
        $this->scrolledwindow_venda->show_all();
        $this->treeview_venda->connect('key-press-event', array(&$this,clistVendaKey));
        $this->treeview_venda->set_rules_hint(TRUE);
        
        $this->scrolledwindow_devolucao=$this->xml->get_widget('scrolledwindow_devolucao');
        $this->liststore_devolucao=new GtkListStore(
            Gobject::TYPE_STRING, // 0 codmerc
            Gobject::TYPE_STRING, // 1 descricao
            Gobject::TYPE_STRING, // 2 quant
            Gobject::TYPE_STRING, // 3 un
            Gobject::TYPE_STRING, // 4 preco orig
            Gobject::TYPE_STRING, //  5 preco com desc.
			Gobject::TYPE_STRING // 6 preco total
        );
        $this->treeview_devolucao=new GtkTreeView($this->liststore_devolucao);
        $this->add_coluna_treeview(
            array('Cod.', 'Descricao', 'Quantidade', 'UN', 'Preco Orig.', 'Preco Vendido', 'Preco Total'),
            $this->treeview_devolucao
        );
        $this->scrolledwindow_devolucao->add($this->treeview_devolucao);
        $this->scrolledwindow_devolucao->show_all();
        $this->treeview_devolucao->connect('key-press-event', array(&$this,clistDevolucaoKey));
        $this->treeview_devolucao->set_rules_hint(TRUE);
		
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        
        $this->button_limpa=$this->xml->get_widget("button_limpa");
        $this->button_limpa->connect_simple('clicked',confirma, array(&$this, 'limpa'),"Deseja limpar os campos da tela?",null);
        
        
        $this->label_cliente=$this->xml->get_widget("label_cliente");        

        $this->label_codcli=$this->xml->get_widget("label_codcli");

        $this->label_codcli->connect_simple('event', array(&$this,'eventoCodCli'));
        
		$this->button_2via=$this->xml->get_widget("button_2via");
		$this->button_2via->connect_simple('clicked',array($this,'segunda_via'));
	
        $this->button_devolver=$this->xml->get_widget("button_devolver");
        $this->button_devolver->connect_simple('clicked',confirma, array($this,ConfirmaDevolucao),"Deseja efetuar a devolucao?",null);
        
        $this->button_cliente=$this->xml->get_widget("button_cliente");
        $this->button_cliente->connect_simple('clicked', 
            array(&$this,buscatab), 
            'select codigo, nome from clientes', 
            true,
            $this->label_codcli, 
            $this->label_cliente,
            "clientes",
            "nome",
            "codigo"
        );
        
        $this->label_venda=$this->xml->get_widget("label_venda");        

        $this->label_devolucao=$this->xml->get_widget("label_devolucao");        


        $this->label_codvendaOLD=$this->xml->get_widget("label_codvenda");
        $this->label_codvenda=new GtkEntry();

        $this->label_codvenda->connect_simple('changed', array(&$this,'eventoCodVenda'));
        $this->button_venda=$this->xml->get_widget("button_venda");
        $this->button_venda->connect_simple('clicked',array($this,'mostraVendas'));
        
        // F8
        $this->button_CancelaTudo=$this->xml->get_widget("button_CancelaTudo");
        $this->button_CancelaTudo->connect_simple('clicked',array($this,'CancelaTudo'));
        
        // F7 OK
        $this->button_CancelaItem=$this->xml->get_widget("button_CancelaItem");
        $this->button_CancelaItem->connect_simple('clicked',array($this,'CancelaItem'));
        
        // F6
        $this->button_DevolveItemParcial=$this->xml->get_widget("button_DevolveItemParcial");
        $this->button_DevolveItemParcial->connect_simple('clicked',array($this,'DevolveItemParcial'));
        $this->entry_quantdevolver=$this->xml->get_widget("entry_quantdevolver");
        //$this->entry_quantdevolver->connect('key-press-event', array($this, 'mascaraNew'),'virgula3');
        
        // F5 OK
        $this->button_DevolveItem=$this->xml->get_widget("button_DevolveItem");
        $this->button_DevolveItem->connect_simple('clicked',array($this,'DevolveItem'));
        
        // F4 OK
        $this->button_DevolveTudo=$this->xml->get_widget("button_DevolveTudo");
        $this->button_DevolveTudo->connect_simple('clicked',array($this,'DevolveTudo'));
        
        $this->limpa();
        //$this->janela->show();
        global $atalho_padrao;
		if($atalho_padrao==NULL){
        	$atalho_padrao = new GtkAccelGroup();
        }

		// ativar atalhos para o teclado visto que sao pedidos ao usar a funcao reparent
		
    	// atalho buscar mercadoria F1    	 
    	$this->button_DevolveTudo->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F4, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_DevolveItem->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F5, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_DevolveItemParcial->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F6, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_CancelaItem->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F7, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_CancelaTudo->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F8, 0, Gtk::ACCEL_VISIBLE);
    	$this->button_cliente->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F2, 0, Gtk::ACCEL_VISIBLE); 
    	$this->button_venda->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F3, 0, Gtk::ACCEL_VISIBLE); 
    	$this->button_limpa->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F9, 0, Gtk::ACCEL_VISIBLE); 
    	$this->button_devolver->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F12, 0, Gtk::ACCEL_VISIBLE);
    	
	}

	function segunda_via(){
		$tabela="devolucoes";
		if($codigo=inputdialog("Digite o codigo")){
			if($achou=$this->retornabusca4('cod'.$tabela,$tabela,"cod".$tabela,$codigo)){
				// imprime em varias vias
				$vias=$this->retorna_OPCAO("viasrecibo");
				if(!is_numeric($vias)){
					$vias=1;
				}
				$slogan=" --==Devolucao==--";
				$titulo="Devolucao";
				$tabela2="entsai";
				$codtabela="coddevolucoes";
				$codtabela2="codentsai";
				$tipoPPG="D";
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
        $selecionado=$this->treeview_devolucao->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_devolucao)){
            $quantidade=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_devolucao,2));
            $codmercD=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_devolucao,0));            
        // verifica se o produto ja existe na lista de venda
	        $this->verificaSeExisteAUX=false;
			$this->liststore_venda->foreach( array($this,'verificaSeExisteNaListaDevolucao'), $codmercD);
			if ($this->verificaSeExisteAUX){
				// ja existe na lista de venda
	            $novaquant=$this->liststore_venda->get_value($this->iterSeExiste,2);
	            
	            $somaquant=number_format($novaquant+$quantidade, 3, ',', '');
	            $precodesconto=$this->liststore_venda->get_value($this->iterSeExiste,5);
	            $precototalunit=$precodesconto*$somaquant;
            
	            $this->liststore_venda->set($this->iterSeExiste,6,number_format($precototalunit,2,',',''));
				$this->liststore_venda->set($this->iterSeExiste,2,number_format($somaquant,2,',',''));

	            if($remover) $this->liststore_devolucao->remove($iter);
	        }else{
				// nao existe na lista de venda
	            $this->liststore_venda->prepend(
	                array(                    
                    		$this->get_valor_liststore($selecionado,$this->liststore_devolucao,0),
 		                $this->get_valor_liststore($selecionado,$this->liststore_devolucao,1),
    		                $this->get_valor_liststore($selecionado,$this->liststore_devolucao,2),
    		                $this->get_valor_liststore($selecionado,$this->liststore_devolucao,3),
    		                $this->get_valor_liststore($selecionado,$this->liststore_devolucao,4),
    		                $this->get_valor_liststore($selecionado,$this->liststore_devolucao,5),
    		                $this->get_valor_liststore($selecionado,$this->liststore_devolucao,6),
    		            )
    		        );
    		        if($remover) $this->liststore_devolucao->remove($iter);
	        }
	        
	    }
	    $this->updatePrecoTotalVendaDevolucao();
    }

    function CancelaTudo(){
    		if($this->numero_rows_liststore($this->liststore_devolucao)==0){
    			msg("Lista de devolucao vazia!");
    			return;
    		}
        $this->liststore_devolucao->foreach(array($this,'CancelaTudoAUX'));
        $this->liststore_devolucao->clear();
        $this->updatePrecoTotalVendaDevolucao();        
    }
    
    function CancelaTudoAUX($store, $path, $iter){
    		$selecionado=$this->treeview_devolucao->get_selection();
    		$selecionado->select_iter($iter);
    		$this->CancelaItem(false);
    		
    }    
    
    function eventoLabelVenda(){
    		//echo "eventoLabelVenda";
        // coloca mascara no preco total da venda
        $valorV=$this->DeixaSoNumeroDecimal($this->label_venda->get_text(),2);
        $valorD=$this->DeixaSoNumeroDecimal($this->label_devolucao->get_text(),2);
        $this->label_venda->set_text($this->mascara2($valorV,'moeda'));
        $this->label_devolucao->set_text($this->mascara2($valorD,'moeda'));
    }
    
    function mostraVendas(){
        $codcli=$this->label_codcli->get_text();
        if(empty($codcli)){
            msg('Escolha primeiro um cliente!!');
        }else{
            $sql="SELECT s.codsaidas, s.data, s.hora, s.totalmerc FROM saidas AS s WHERE s.codcli='$codcli' AND s.totalmerc>0 ORDER BY s.data DESC, s.hora DESC";
            $this->buscatab($sql, true, &$this->label_codvenda, &$this->label_venda, 'saidas',"totalmerc",'codsaidas');
        }
        return true;
    }
    
    function eventoCodVenda($forcar=false){
        $codvenda=$this->label_codvenda->get_text();
        $this->label_codvendaOLD->set_text($codvenda);
        if(!empty($codvenda)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();        
            $sql="SELECT e.codmerc, m.descricao, e.quantidade, m.unidade, e.precooriginal, e.precocomdesconto FROM entsai AS e INNER JOIN mercadorias AS m ON (m.codmerc=e.codmerc) WHERE e.codentsai='$codvenda' AND e.tipo='S'";
            $resultado=$con->Query($sql);            
            $this->liststore_venda->clear();
            $this->liststore_devolucao->clear();
            while($i = $con->FetchRow($resultado)) {
                $this->liststore_venda->append(
                    array(
                        $i[0],
                        $i[1],
                        number_format($i[2], 3, ',', ''),
                        $i[3],
                        number_format($i[4], 2, ',', ''),
                        number_format($i[5], 2, ',', ''),
                        number_format($i[5]*$i[2], 2, ',', '') // preco com desconto * quantidade
                    )
                );
            }
            $con->Disconnect();
        }
        $this->eventoLabelVenda();
        
    }
    
	function limpa(){
        // funcao que limpa toda a tela
        $this->liststore_venda->clear();
        $this->liststore_devolucao->clear();
        $this->label_codcli->set_text('');
        $this->label_codvenda->set_text('');
        $this->label_cliente->set_text('');
        $this->label_venda->set_text('');
        $this->label_codvendaOLD->set_text('');
        $this->label_devolucao->set_text('');
        $this->codvenda="";
	}
    

    function clistDevolucaoKey($widget,$evento){
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
            $this->treeview_devolucao->grab_focus();
            return true;
        }
        return false;
    }

    function eventoCodCli(){
    		
        $codcli=$this->label_codcli->get_text();
        if(empty($codcli)){
            return;
        }
        if($this->UltimoCliente<>$codcli){
            $this->UltimoCliente=$codcli;
            $this->liststore_venda->clear();
            $this->liststore_devolucao->clear();
            $this->button_venda->clicked();
        }
        return;
    }
    
    function DevolveItem(){
        $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $quantidade=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_venda,2));
            if($quantidade>0){
                $this->DevolveItem2(false,$iter);
                $this->liststore_venda->remove($iter);
                //$this->updatePrecoTotalVendaDevolucao();
            }else{            
                msg('Mercadoria com quantidade ZERO');                
            }
        }
        
    }
    
    function DevolveTudo(){
        	if($this->numero_rows_liststore($this->liststore_venda)==0){
    			msg("Lista de venda vazia!");
    			return;
    		}
        $this->liststore_venda->foreach(array($this,'DevolveTudoAUX'));
        $this->liststore_venda->clear();
        $this->updatePrecoTotalVendaDevolucao();
    }
    
    function DevolveTudoAUX($store, $path, $iter){
    		$this->DevolveItem2(false,$iter);
    }
    
    function DevolveItemParcial(){
        $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $quantidade=$this->pegaNumero($this->liststore_venda->get_value($iter,2));
            if($quantidade>0){
            		$this->DevolveItem2(true,$iter);
            }else{            
                msg('Mercadoria com quantidade ZERO');                
            }
        }
    }
    
    function DevolveItem2($parcial,$iter){
        $quantidade=$this->pegaNumero($this->liststore_venda->get_value($iter,2));
        if($parcial){
            $spin=$this->pegaNumero($this->entry_quantdevolver);
            if($spin>$quantidade){
            		//echo "$spin>$quantidade";
            		msg("Quantidade deve ser menor ou igual a vendida");
            		return;
            }elseif($spin==0){
            		msg("Especifique a quantidade parcial a ser devolvida");
            		return;
            }
        }else{
            $spin=$quantidade; // pega quantidade total do item
        }        
        
        $novaquant=number_format($quantidade-$spin, 3, ',', '');
        $codvenda=$this->label_codvenda->get_text();
        $codmerc=$this->pegaNumero($this->liststore_venda->get_value($iter,0));
        // devolve na lista

        $this->liststore_venda->set($iter,2,$novaquant);
   
        // update no pre� total unitario
        $precodesconto=$this->pegaNumero($this->liststore_venda->get_value($iter,5));
        $precototalunit=$precodesconto*$novaquant;
        $this->liststore_venda->set($iter,6,number_format($precototalunit,2,',',''));
        

        // verifica se o produto ja existe na lista de devolucao
        $this->verificaSeExisteAUX=false;
		$this->liststore_devolucao->foreach( array($this,'verificaSeExisteNaListaDevolucao'), $codmerc);
		if ($this->verificaSeExisteAUX){
            // se o codmerc ja existir na lista de devolucao
            // pega quantidade
			$OLDquant=$this->liststore_devolucao->get_value($this->iterSeExiste,2);
            // soma quantidade
            $novaquant=$OLDquant+$spin;
            // altera quantidade na clist
            $this->liststore_devolucao->set($this->iterSeExiste,2,number_format($novaquant,3,',',''));
            // calcula novo preco total unitario
            $precototalunitdevolvido=$precodesconto*$novaquant;
            // altera preco total unitario na lista
            $this->liststore_devolucao->set($this->iterSeExiste,6,number_format($precototalunitdevolvido,2,',',''));
        }else{
            $precototalunitdevolvido=$precodesconto*$spin;
            // coloca na lista de devolucao
            $this->liststore_devolucao->prepend(
                array(
                		$this->liststore_venda->get_value($iter,0),                    
                		$this->liststore_venda->get_value($iter,1),
                		number_format($spin, 3, ',', ''),
                		$this->liststore_venda->get_value($iter,3),
                		$this->liststore_venda->get_value($iter,4),
                		$this->liststore_venda->get_value($iter,5),
                		number_format($precototalunitdevolvido,2,',','')                    
                )
            );        

        }
        $this->updatePrecoTotalVendaDevolucao();
    }
    
    function verificaSeExisteNaListaDevolucao($store, $path, $iter, $codmerc){
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
	
    function updatePrecoTotalVendaDevolucao(){
    		
        // update no pre� total da venda
        $this->somaVenda=0;
        $this->liststore_venda->foreach( array($this,'updatePrecoTotalVenda'));

		$this->somaDevolucao=0;
		$this->liststore_devolucao->foreach( array($this,'updatePrecoTotalDevolucao'));

        $this->label_venda->set_text(number_format($this->somaVenda,2,',',''));
        $this->label_devolucao->set_text(number_format($this->somaDevolucao,2,',',''));
    }
    
    function updatePrecoTotalVenda($store, $path, $iter){
    		$precototalunit=$this->pegaNumero($store->get_value($iter,6));
    		$this->somaVenda+=$precototalunit;
    }
    function updatePrecoTotalDevolucao($store, $path, $iter){
    		$precototalunit=$this->pegaNumero($store->get_value($iter,6));
    		$this->somaDevolucao+=$precototalunit;
    }
    
    function ConfirmaDevolucao(){
        // grava no entsai com tipo D
        // grava devolucoes o total da devolucao
        $codplacondevolucao=$this->retorna_OPCAO("placondevolucao");
        if(empty($codplacondevolucao)){
        	msg("Cadastre o plano de contas para devolucoes em configuracoes.");
        	return;
        }
        $codsaidas=$this->label_codvenda->get_text();
        
        // devolve no banco de dados
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();        
        
        $hora=date("H:i:s");
        $data=date("Y-m-d");
        
        $this->futura=$this->retornabusca4('futura','saidas','codsaidas',$codsaidas);
        $endereco=$this->retornabusca4('endereco','saidas','codsaidas',$codsaidas);
        $vendedor=$this->retornabusca4('vendedor','saidas','codsaidas',$codsaidas);
        
        $totalnfV=$this->retornabusca4('totalnf','saidas','codsaidas',$codsaidas);
        $descontoV=$this->retornabusca4('desconto','saidas','codsaidas',$codsaidas);
        $totalmercV=$this->retornabusca4('totalmerc','saidas','codsaidas',$codsaidas);
        $totalnfD=$this->pegaNumero($this->label_devolucao);
        if($totalnfD<=0){
    		msg("Devolucao com valor zero!!!");
    		return;
        }
        if($descontoV>0){
            $porcento=100-$totalmercV*100/$totalnfV; // pega quantos porcento foi o desconto na venda
        }else{
            $porcento=0;
        }
        //100-110*100/150
        //nf merc
        //150 110 
        //100 x
        $descontoD=(($totalnfD/100)*$porcento)*(-1);
        $totalmercD=$totalnfD+($descontoD); // valor devolvido com desconto/acrescimo da venda
       
        $codcli=$this->label_codcli->get_text();
        
        $codentsai=$con->QueryLastCod("INSERT INTO devolucoes (data, codcli, endereco, totalmerc, desconto, totalnf, vendedor, hora, codsaidas) VALUES ('$data','$codcli','$endereco','$totalmercD','$descontoD','$totalnfD','$vendedor','$hora','$codsaidas') ");
 		
 		$this->sqlAUX="";
		$this->liststore_devolucao->foreach(array($this,'ConfirmaDevolucaoAUX'),$codsaidas,  $codentsai);
        $con->Query($this->sqlAUX);
        
        $novototalnfV=$totalnfV-$totalnfD;
        $novototalmercV=$totalmercV-$totalnfD;
        $novodescontoV=$descontoV-$descontoD;        
        $sql="UPDATE saidas SET totalmerc='$novototalmercV', totalnf='$novototalnfV', desconto='$novodescontoV' WHERE codsaidas='$codsaidas'";
        $con->Query($sql);
        
        
        
        
        if($this->retorna_OPCAO('integraestoquefinanceiro')){
    		$valor_a_abater=$totalmercD; // 128
    		$saldo_a_receber=$this->retornabusca4('sum(saldo)','receber','codsaidas',$codsaidas); // 192
    		// se saldo das contas a receber maior que valor devolvido com desconto/acrescimo da venda
    		// entao abate das valor das parcelas iniciando da primeira
    		// abate do saldo E do valor inicial da conta
    		$sql="SELECT codigo, valor, saldo, obs, vendedor, comissao, data_c, data_v FROM receber WHERE codsaidas=$codsaidas ORDER BY data_v DESC";
    		$resultado=$con->Query($sql);            
    		while($i=$con->FetchRow($resultado)){
    			$saldo=$i[2]; // 66
    			if($valor_a_abater>0 and $saldo>0){
    			$codigo=$i[0];
    			$valor=$i[1];
    			$data_c=$i[6];
    			$data_v=$i[7];
    			$obs=$i[3]."; DEVOLUCAO feita em ". $this->corrigeNumero($data, 'data'). " VALOR ";
    			$vendedor=$i[4];
    			$comissao=$i[5];
    			if($saldo<=$valor_a_abater){ // saldo menor que o valor devolvido
    				$obs.=$saldo;
    				$valor-=$saldo; // 15
    				$valor_a_abater-=$saldo; //10
    				$saldo=0;
    			}else{ // saldo > $saldo_a_abater saldo MAIOR que o valor devolvido
    				$obs.=$valor_a_abater;
    				$valor-=$valor_a_abater;
    				$saldo-=$valor_a_abater;
    				$valor_a_abater=0;
    			}
    			$sql="UPDATE receber SET valor='$valor', saldo='$saldo', obs='$obs' WHERE codsaidas=$codsaidas and codigo=$codigo";

    			if(!$con->query($sql)) msg("Erro ao atualizar contas a receber");
    			
    			$sql="UPDATE movpagamentos SET valor='$valor' WHERE codorigem='$codsaidas' AND tipo='S' AND data_c='$data_c' AND data='$data_v'";
    			
    			if(!$con->query($sql)) msg("Erro ao atualizar movpagamentos");
    			
    			}
    		}
    		// se saldo das contas a receber continuar maior que valor devolvido com desconto/acrescimo da venda
    		if($valor_a_abater>0){
    			// entao cria uma a receber num valor NEGATIVO
    			$valor_inverso=$valor_a_abater*(-1);
    			$nomecli=$this->retornabusca4('nome','clientes','codigo',$codcli);
    			$sql ="INSERT INTO receber (fiscal, data_c, data_v, valor, saldo, descr, obs, codplacon, codorigem, vendedor, comissao) ";
			$sql.="VALUES ('N/A', '$data', '$data', '$valor_inverso', '$valor_inverso', 'DEVOLUCAO DE MERCADORIA', 'DEVOLUCAO DE MERCADORIA PARA O CLIENTE $codcli - $nomecli', '$codplacondevolucao', $codcli, $vendedor, $comissao);";				
			if(!$con->Query($sql)) msg("Erro ao adicionar conta a receber negativa");
			
			// insere pagamentos para aparecer no recibo
			$sql="INSERT INTO movpagamentos (codorigem, valor, data, tipo) VALUES ($codentsai,  '$valor_inverso', '$data', 'D')";
			if(!$con->Query($sql)) msg("Erro ao adicionar mov. pagamentos de conta a receber negativa");
    		}
			
		}
        
        $con->Disconnect();
		$this->limpa();
		msg("Devolucao efetuada com sucesso!");

        $slogan=" --==DEVOLUCAO==--";
        $titulo="Devolucao";
        $tabela2="entsai";
        $codtabela="coddevolucoes";
        $codtabela2="codentsai";
        $tipoPPG="D";
        $assinatura=true;
            
        for($i=0;$i<$this->retorna_OPCAO("viasrecibo");$i++){
            confirma(array($this,'imprimirRecibo'),'Deseja imprimir as vias do recibo?', $codentsai, 'devolucoes', $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura);
        }
       
    }
    
   function ConfirmaDevolucaoAUX($store, $path, $iter, $codsaidas, $codentsai){
        $codmerc=$store->get_value($iter,0);
        $quantidade=$this->pegaNumero($store->get_value($iter,2));
        $precooriginal=$this->pegaNumero($store->get_value($iter,4));
        $precocomdesconto=$this->pegaNumero($store->get_value($iter,5));
        
        // verifica quantidade vendida no entsai
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT quantidade, entregue FROM entsai WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc'";                
        $resultado=$con->Query($sql);            
        $i = $con->FetchRow($resultado);
        $quantvendida=$i[0];
        $quantentregue=$i[1];
		
        $novaquant=$quantvendida-$quantidade; // grava a quantidade que sobrou .. qt vendida-qt devolvida.. se devolveu tudo grava zero 0
        $novaquantentregue=$quantentregue-$quantidade; // baixa a quantidade entregue
        $this->sqlAUX.="UPDATE entsai SET quantidade='$novaquant', entregue='$novaquantentregue' WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc'; ";
        
        $this->sqlAUX.="INSERT INTO entsai (codentsai, tipo, codmerc, precooriginal, precocomdesconto, quantidade) VALUES ('$codentsai', 'D', '$codmerc', '$precooriginal', '$precocomdesconto', '$quantidade'); ";
        // retorna ao estoque
        $estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);
        $estoqueatual+=$quantidade;

        if($this->futura==1){ // se for venda futura, i.e, nao baixou estoque ao vender, agora tem que voltar ao estoque somente a quantidade que foi entregue. Quando eh venda futura, baixa o estoque somente ao entregar
        	$estoqueatual+=$quantentregue;	
        }
		$this->sqlAUX.="UPDATE mercadorias SET estoqueatual='$estoqueatual' WHERE codmerc='$codmerc';";
  }
}
?>

