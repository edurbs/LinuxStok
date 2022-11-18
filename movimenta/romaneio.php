<?php
class romaneio extends funcoes {
	function romaneio(){
        $this->xml=$this->carregaGlade('romaneio');

        $this->scrolledwindow_venda=$this->xml->get_widget('scrolledwindow_venda');
        $this->liststore_venda=new GtkListStore(
            Gtk::TYPE_STRING, // 0 codmerc
            Gtk::TYPE_STRING, // 1 descricao
            Gtk::TYPE_STRING, // 2 quant
            Gtk::TYPE_STRING, // 3 un
            Gtk::TYPE_STRING, // 4 preco orig
            Gtk::TYPE_STRING, //  5 preco com desc.
            Gtk::TYPE_STRING // 6 preco total
        );
        $this->treeview_venda=new GtkTreeView($this->liststore_venda);
        $this->add_coluna_treeview(
            array('Merc', 'Descricao', 'Quant', 'UN', 'Preco Tot.', 'Peso Tot.', 'Volume Tot.'),
            $this->treeview_venda
        );
        $this->scrolledwindow_venda->add($this->treeview_venda);
        $this->scrolledwindow_venda->show_all();
        //$this->treeview_venda->connect('key-press-event', array(&$this,clistVendaKey));
        $this->treeview_venda->set_rules_hint(TRUE);

		// tela de carga        
        $this->scrolledwindow_carga=$this->xml->get_widget('scrolledwindow_carga');
        $this->liststore_carga=new GtkListStore(
            Gtk::TYPE_STRING, // 0 codmerc
            Gtk::TYPE_STRING, // 1 descricao
            Gtk::TYPE_STRING, // 2 quant
            Gtk::TYPE_STRING, // 3 un
            Gtk::TYPE_STRING, // 4 preco total
			Gtk::TYPE_STRING, // 5 peso total
			Gtk::TYPE_STRING // 6 volume total m3
        );
        $this->treeview_carga=new GtkTreeView($this->liststore_carga);
        $this->add_coluna_treeview(
            array('Merc.', 'Descricao', 'Quant', 'UN', 'Preco Tot.', 'Peso Tot.', 'Volume Tot.'),
            $this->treeview_carga
        );
        $this->scrolledwindow_carga->add($this->treeview_carga);
        $this->scrolledwindow_carga->show_all();
        //$this->treeview_romaneio->connect('key-press-event', array($this,'clistromaneioKey'));
        $this->treeview_carga->set_rules_hint(TRUE);


		// tela de roteiro        
        $this->scrolledwindow_roteiro=$this->xml->get_widget('scrolledwindow_roteiro');

        $this->liststore_roteiro=new GtkListStore(
            Gtk::TYPE_STRING, // 0 codsaidas
            Gtk::TYPE_STRING, // 1 data
            Gtk::TYPE_STRING, // 2 totalnf
            Gtk::TYPE_STRING, // 3 nome
            Gtk::TYPE_STRING, // 4 endereco+numero+bairro
			Gtk::TYPE_STRING, // 5 cidade
			Gtk::TYPE_STRING, // 6 estado
			Gtk::TYPE_STRING // 7 obs
        );
        $this->treeview_roteiro=new GtkTreeView($this->liststore_roteiro);
        $this->add_coluna_treeview(
            array('Venda', 'Data', 'Total', 'Cliente', 'Endereco', 'Cidade', 'UF', 'Obs'),
            $this->treeview_roteiro
        );
        $this->scrolledwindow_roteiro->add($this->treeview_roteiro);
        $this->scrolledwindow_roteiro->show_all();
        $this->treeview_roteiro->set_rules_hint(TRUE);

		
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        
        $this->button_limpa=$this->xml->get_widget("button_limpa");
        $this->button_limpa->connect_simple('clicked',confirma, array(&$this, 'limpa'),"Deseja limpar os campos da tela?",null);
        
        
        $this->label_cliente=$this->xml->get_widget("label_cliente");        

        $this->label_codcli=$this->xml->get_widget("label_codcli");

        //$this->label_codcli->connect_simple('event', array($this,'eventoCodCli'));
        
        /*$this->button_carregar=$this->xml->get_widget("button_carregar");
        $this->button_carregar->connect_simple('clicked',confirma, array($this,Confirmaromaneio),"Deseja efetuar a romaneio?",null);*/ 
        $this->label_codvenda=new GtkEntry();

        $this->label_codvenda->connect_simple('changed', array($this,'eventoCodVenda'));        
        
        $this->button_venda=$this->xml->get_widget("button_venda");
        $this->button_venda->connect_simple('clicked', 
            array($this, 'buscatab'),
            " SELECT s.codsaidas, s.data, s.totalnf, c.nome, e.endereco, e.bairro, e.cidade, e.estado, s.obs FROM saidas AS s INNER JOIN clientes AS c ON c.codigo=s.codcli INNER JOIN cadastro2enderecos AS e ON (e.codigo=c.codigo AND e.cadastro='clientes') ORDER BY s.data DESC",
            true,
            $this->label_codvenda, 
            $this->label_codcli,
            "saidas",
            "codcli",
            "codsaidas"
        );
        /*
        $this->label_venda=$this->xml->get_widget("label_venda");        

        $this->label_romaneio=$this->xml->get_widget("label_romaneio");        


        $this->label_codvendaOLD=$this->xml->get_widget("label_codvenda");
        $this->label_codvenda=new GtkEntry();

        $this->label_codvenda->connect_simple('changed', array(&$this,'eventoCodVenda'));
        */
        //$this->button_venda=$this->xml->get_widget("button_venda");
        //$this->button_venda->connect_simple('clicked',array($this,'mostraVendas'));
        
        // F8
        $this->button_CancelaTudo=$this->xml->get_widget("button_CancelaTudo");
        $this->button_CancelaTudo->connect_simple('clicked',array($this,'CancelaTudo'));
        
        // F7 OK
        $this->button_CancelaItem=$this->xml->get_widget("button_CancelaItem");
        $this->button_CancelaItem->connect_simple('clicked',array($this,'CancelaItem'));
        
        // F6
        $this->button_carregaItemParcial=$this->xml->get_widget("button_carregaItemParcial");
        $this->button_carregaItemParcial->connect_simple('clicked',array($this,'carregaItemParcial'));
        $this->entry_quantcarregar=$this->xml->get_widget("entry_quantcarregar");
        	$this->entry_quantcarregar->connect('key-press-event', array($this, 'mascaraNew'),'virgula3');
        
        // F5 OK
        $this->button_carregaItem=$this->xml->get_widget("button_carregaItem");
        $this->button_carregaItem->connect_simple('clicked',array($this,'carregaItem'));
        
        // F4 OK
        $this->button_carregaTudo=$this->xml->get_widget("button_carregaTudo");
        $this->button_carregaTudo->connect_simple('clicked',array($this,'carregaTudo'));
        
        $this->limpa();
        $this->janela->show();
	}

	    
    function CancelaItem($remover=true){
        $selecionado=$this->treeview_carga->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_carga)){
            $quantidade=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_carga,2));
            $codmercD=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_carga,0));            
        // verifica se o produto ja existe na lista de venda
	        $this->verificaSeExisteAUX=false;
			$this->liststore_venda->foreach(array($this,'verificaSeExisteNaListaCarga'), $codmercD);
			if ($this->verificaSeExisteAUX){
				// ja existe na lista de venda
	            $novaquant=$this->liststore_venda->get_value($this->iterSeExiste,2);
	            
	            $somaquant=number_format($novaquant+$quantidade, 3, ',', '');
	            //$precodesconto=$this->liststore_venda->get_value($this->iterSeExiste,5);
	            //$precototalunit=$precodesconto*$somaquant;
            
	            //$this->liststore_venda->set($this->iterSeExiste, 6, number_format($precototalunit,2,',',''));
				$this->liststore_venda->set($this->iterSeExiste, 2, number_format($somaquant,2,',',''));

	            if($remover) $this->liststore_carga->remove($iter);
	        }else{
				// nao existe na lista de venda
	            $this->liststore_venda->prepend(
	                array(                    
                    		$this->get_valor_liststore($selecionado,$this->liststore_carga,0),
 		                $this->get_valor_liststore($selecionado,$this->liststore_carga,1),
    		                $this->get_valor_liststore($selecionado,$this->liststore_carga,2),
    		                $this->get_valor_liststore($selecionado,$this->liststore_carga,3),
    		                $this->get_valor_liststore($selecionado,$this->liststore_carga,4),
    		                $this->get_valor_liststore($selecionado,$this->liststore_carga,5),
    		                $this->get_valor_liststore($selecionado,$this->liststore_carga,6)
    		            )
    		        );
    		        if($remover) $this->liststore_carga->remove($iter);
	        }
	        
	    }
	    $this->updatePrecoTotalVendaromaneio();
    }

    function CancelaTudo(){
    		if($this->numero_rows_liststore($this->liststore_carga)==0){
    			msg("Lista de carga vazia!");
    			return;
    		}
        $this->liststore_carga->foreach(array($this,'CancelaTudoAUX'));
        $this->liststore_carga->clear();
        //$this->updatePrecoTotalVendaromaneio();        
    }
    
    function CancelaTudoAUX($store, $path, $iter){
    		$selecionado=$this->treeview_carga->get_selection();
    		$selecionado->select_iter($iter);
    		$this->CancelaItem(false);
    		
    }    
    
    function eventoLabelVenda(){
    		//echo "eventoLabelVenda";
        // coloca mascara no preco total da venda
        //$valorV=$this->DeixaSoNumeroDecimal($this->label_venda->get_text(),2);
        //$valorD=$this->DeixaSoNumeroDecimal($this->label_romaneio->get_text(),2);
        //$this->label_venda->set_text($this->mascara2($valorV,'moeda'));
        //$this->label_romaneio->set_text($this->mascara2($valorD,'moeda'));
    }
    
    function mostraVendas(){
        $codcli=$this->label_codcli->get_text();
        if(empty($codcli)){
            msg('Escolha primeiro um cliente!!');
        }else{
            $sql="SELECT s.codsaidas, s.data, s.hora, s.totalmerc FROM saidas AS s WHERE s.codcli='$codcli' AND s.totalmerc>0 ORDER BY s.data DESC, s.hora DESC";
            $this->buscatab($sql, true, $this->label_codvenda, $this->label_venda, 'saidas',"totalmerc",'codsaidas');
        }
        return true;
    }
    
    function eventoCodVenda($forcar=false){
        $codvenda=$this->label_codvenda->get_text();
        //$this->label_codvendaOLD->set_text($codvenda);
        if(!empty($codvenda)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();        
            $sql="SELECT e.codmerc, m.descricao, e.quantidade, m.unidade, e.precooriginal, e.precocomdesconto, (m.peso*e.quantidade), (m.volume*e.quantidade) FROM entsai AS e INNER JOIN mercadorias AS m ON (m.codmerc=e.codmerc) WHERE e.codentsai='$codvenda' AND e.tipo='S'";
            $resultado=$con->Query($sql);            
            $this->liststore_venda->clear();
            //$this->liststore_romaneio->clear();
            
            while($i = $con->FetchRow($resultado)) {
                $this->liststore_venda->append(
                    array(
                        $i[0],
                        $i[1],
                        number_format($i[2], 3, ',', ''),
                        $i[3],
                        //number_format($i[4], 2, ',', ''),
                        //number_format($i[5], 2, ',', ''),
                        number_format($i[5]*$i[2], 2, ',', ''), // preco com desconto * quantidade
                        number_format($i[6], 3, ',', ''), // peso total
                        number_format($i[7], 0, ',', '') // volume total
                    )
                );
            }
            //$this->label_codvenda->set_text('');
            $con->Disconnect();
        }
        $this->eventoLabelVenda();
        
    }
    
	function limpa(){
        // funcao que limpa toda a tela
        $this->liststore_venda->clear();
        $this->liststore_carga->clear();
        $this->liststore_roteiro->clear();
        $this->label_codcli->set_text('');
        $this->label_codvenda->set_text('');
//        $this->label_cliente->set_text('');
        //$this->label_venda->set_text('');
        //$this->label_codvendaOLD->set_text('');
        //$this->label_romaneio->set_text('');
        //$this->codvenda="";
	}
    

    function clistromaneioKey($widget,$evento){
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
        // muda o foto para a clista de romaneio
        $tecla=$evento->keyval;
        if($tecla==65289 or $tecla==65417 or $tecla==65056 or $tecla==64773){
            $this->treeview_romaneio->grab_focus();
            return true;
        }
        return false;
    }

    function eventoCodCli(){
    		
        $codcli=$this->DeixaSoNumero($this->label_codcli->get_text());
        if(empty($codcli)){
            return;
        }else{
        		$nome=$this->retornabusca4('nome','clientes','codigo',$codcli);
        		$this->label_codcli->set_text(" Ultimo Cliente: ".$nome);
        }
        if($this->UltimoCliente<>$codcli){
            //$this->UltimoCliente=$codcli;
            //$this->liststore_venda->clear();
            //$this->liststore_romaneio->clear();
            //$this->button_venda->clicked();
        }
        return;
    }
    
    function carregaItem(){
        $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $quantidade=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_venda,2));
            if($quantidade>0){
                $this->carregaItem2(false,$iter);
                $this->liststore_venda->remove($iter);
                //$this->updatePrecoTotalVendaromaneio();
            }else{            
                msg('Mercadoria com quantidade ZERO');                
            }
        }
        
    }
    
    function carregaTudo(){
        	if($this->numero_rows_liststore($this->liststore_venda)==0){
    			msg("Lista de venda vazia!");
    			return;
    		}
        $this->liststore_venda->foreach(array($this,'carregaTudoAUX'));
        $this->liststore_venda->clear();
        $this->updatePrecoTotalVendaromaneio();
    }
    
    function carregaTudoAUX($store, $path, $iter){
    		$this->carregaItem2(false,$iter);
    }
    
    function carregaItemParcial(){
        $selecionado=$this->treeview_venda->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_venda)){
            $quantidade=$this->pegaNumero($this->liststore_venda->get_value($iter,2));
            if($quantidade>0){
            		$this->carregaItem2(true,$iter);
            }else{            
                msg('Mercadoria com quantidade ZERO');                
            }
        }
    }
    
    function carregaItem2($parcial,$iter){
        $quantidade=$this->pegaNumero($this->liststore_venda->get_value($iter,2));
        if($parcial){
            $spin=$this->pegaNumero($this->entry_quantcarregar);
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
        
        // altera na lista de venda
        $this->liststore_venda->set($iter,2,$novaquant);
   
        // update no preço total unitario
        //$precodesconto=$this->pegaNumero($this->liststore_venda->get_value($iter,5));
        //$precototalunit=$precodesconto*$novaquant;
        //$this->liststore_venda->set($iter,6,number_format($precototalunit,2,',',''));
        

        // verifica se o produto ja existe na lista de romaneio
        $this->verificaSeExisteAUX=false;
		$this->liststore_carga->foreach( array($this,'verificaSeExisteNaListaCarga'), $codmerc);
		if ($this->verificaSeExisteAUX){
            // se o codmerc ja existir na lista de romaneio
            // pega quantidade
			$OLDquant=$this->liststore_carga->get_value($this->iterSeExiste,2);
            // soma quantidade
            $novaquant=$OLDquant+$spin;
            // altera quantidade na clist
            $this->liststore_romaneio->set($this->iterSeExiste,2,number_format($novaquant,3,',',''));
            // calcula novo preco total unitario
            //$precototalunitdevolvido=$precodesconto*$novaquant;
            // altera preco total unitario na lista
            //$this->liststore_romaneio->set($this->iterSeExiste,6,number_format($precototalunitdevolvido,2,',',''));
        }else{
            //$precototalunitcarregado=$precodesconto*$spin;
            // coloca na lista de romaneio
            $this->liststore_carga->prepend(
                array(
                		$this->liststore_venda->get_value($iter,0),                    
                		$this->liststore_venda->get_value($iter,1),
                		number_format($spin, 3, ',', ''),
                		$this->liststore_venda->get_value($iter,3),
                		$this->liststore_venda->get_value($iter,4),
                		$this->liststore_venda->get_value($iter,5),
                		$this->liststore_venda->get_value($iter,6)
                )
            );
        }
        
        // coloca na lista de roteiro
        $this->verificaSeExisteAUX=false;
        echo "codvenda".$codvenda;
		$this->liststore_roteiro->foreach( array($this,'verificaSeExisteNaListaCarga'), $codvenda);
		if ($this->verificaSeExisteAUX){
            // se o codmerc ja existir na lista de romaneio
            // pega quantidade
			//$OLDquant=$this->liststore_roteiro->get_value($this->iterSeExiste,2);
            // soma quantidade
            //$novaquant=$OLDquant+$spin;
            // altera quantidade na clist
            //$this->liststore_roteiro->set($this->iterSeExiste,2,number_format($novaquant,3,',',''));
            // calcula novo preco total unitario
            //$precototalunitdevolvido=$precodesconto*$novaquant;
            // altera preco total unitario na lista
            //$this->liststore_romaneio->set($this->iterSeExiste,6,number_format($precototalunitdevolvido,2,',',''));
            //msg('existe venda no roteiro');
        }else{
            //$precototalunitcarregado=$precodesconto*$spin;
            // coloca na lista de romaneio
            $con=$this->conecta();
            $sql="SELECT s.codsaidas, s.data, s.totalnf, c.nome, e.endereco, e.numero, e.bairro, e.cidade, e.estado, s.obs FROM saidas AS s INNER JOIN clientes AS c ON c.codigo=s.codcli INNER JOIN cadastro2enderecos AS e ON (e.codigo=c.codigo AND e.cadastro='clientes') WHERE codsaidas=$codvenda";
            $resultado=$con->Query($sql);
            $i=$con->FetchArray($resultado);
            $this->liststore_roteiro->prepend(
                array(
                		$i[0], // codsaidas                    
                		$i[1], // data
                		$i[2], // valor 
                		$i[3], // cliente
                		$i[4]." ".$i[5]." ".$i[6], // endereco
                		$i[7], // cidade
                		$i[8], // estado
                		$i[9] // obs
                )
            );
            $this->disconecta($con);
        }
        

        $this->updatePrecoTotalVendaromaneio();
    }
    
    function verificaSeExisteNaListaCarga($store, $path, $iter, $codmerc){
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
	
    function updatePrecoTotalVendaromaneio(){
    		
        // update no preço total da venda
        /*
        $this->somaVenda=0;
        $this->liststore_venda->foreach( array($this,'updatePrecoTotalVenda'));

		$this->somaromaneio=0;
		$this->liststore_romaneio->foreach( array($this,'updatePrecoTotalromaneio'));

        $this->label_venda->set_text(number_format($this->somaVenda,2,',',''));
        $this->label_romaneio->set_text(number_format($this->somaromaneio,2,',',''));
        */
    }
    
    function updatePrecoTotalVenda($store, $path, $iter){
    		$precototalunit=$this->pegaNumero($store->get_value($iter,6));
    		$this->somaVenda+=$precototalunit;
    }
    function updatePrecoTotalromaneio($store, $path, $iter){
    		$precototalunit=$this->pegaNumero($store->get_value($iter,6));
    		$this->somaromaneio+=$precototalunit;
    }
    
    function Confirmaromaneio(){
        // grava no entsai com tipo D
        // grava devolucoes o total da romaneio
        $codplaconromaneio=$this->retorna_OPCAO("placonromaneio");
        if(empty($codplaconromaneio)){
        		msg("Cadastre o plano de contas para devolucoes em configuracoes.");
        		return;
        }
        $codsaidas=$this->label_codvenda->get_text();
        
        // carrega no banco de dados
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();        
        
        $hora=date("H:i:s");
        $data=date("Y-m-d");
        
        $endereco=$this->retornabusca4('endereco','saidas','codsaidas',$codsaidas);
        $vendedor=$this->retornabusca4('vendedor','saidas','codsaidas',$codsaidas);
        
        $totalnfV=$this->retornabusca4('totalnf','saidas','codsaidas',$codsaidas);
        $descontoV=$this->retornabusca4('desconto','saidas','codsaidas',$codsaidas);
        $totalmercV=$this->retornabusca4('totalmerc','saidas','codsaidas',$codsaidas);
        $totalnfD=$this->pegaNumero($this->label_romaneio);
        if($totalnfD<=0){
        		msg("romaneio com valor zero!!!");
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
		$this->liststore_romaneio->foreach(array($this,'ConfirmaromaneioAUX'),$codsaidas,  $codentsai);
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
        		$sql="SELECT codigo, valor, saldo, obs, vendedor, comissao FROM receber WHERE codsaidas=$codsaidas ORDER BY data_v DESC";
        		$resultado=$con->Query($sql);            
        		while($i=$con->FetchRow($resultado)){
        			$saldo=$i[2]; // 66
        			if($valor_a_abater>0 and $saldo>0){
        			$codigo=$i[0];
        			$valor=$i[1];        			
        			$obs=$i[3]."; romaneio feita em ".$this->corrigeNumero($data,'data')." VALOR ";
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
        			}
        		}
        		// se saldo das contas a receber continuar maior que valor devolvido com desconto/acrescimo da venda
        		if($valor_a_abater>0){
        			// entao cria uma a receber num valor NEGATIVO
        			$valor_inverso=$valor_a_abater*(-1);        			
        			$nomecli=$this->retornabusca4('nome','clientes','codigo',$codcli);
        			$sql ="INSERT INTO receber (fiscal, data_c, data_v, valor, saldo, descr, obs, codplacon, codorigem, vendedor, comissao) ";
				$sql.="VALUES ('N/A', '$data', '$data', '$valor_inverso', '$valor_inverso', 'romaneio DE MERCADORIA', 'romaneio DE MERCADORIA PARA O CLIENTE $codcli - $nomecli', '$codplaconromaneio', $codcli, $vendedor, $comissao);";				
				if(!$con->Query($sql)) msg("Erro ao adicionar conta a receber negativa");
				
				// insere pagamentos para aparecer no recibo
				$sql="INSERT INTO movpagamentos (codorigem, valor, data, tipo) VALUES ($codentsai,  '$valor_inverso', '$data', 'D')";
				if(!$con->Query($sql)) msg("Erro ao adicionar mov. pagamentos de conta a receber negativa");				
        		}
			
		}
        
        $con->Disconnect();
		$this->limpa();
		msg("romaneio efetuada com sucesso!");

        $slogan=" --==romaneio==--";
        $titulo="romaneio";
        $tabela2="entsai";
        $codtabela="coddevolucoes";
        $codtabela2="codentsai";
        $tipoPPG="D";
        $assinatura=true;
            
        for($i=0;$i<retorna_CONFIG("viasrecibo");$i++){
            confirma(array($this,'imprimirRecibo'),'Deseja imprimir as vias do recibo?', $codentsai, 'devolucoes', $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura);
        }
       
    }
    
   function ConfirmaromaneioAUX($store, $path, $iter, $codsaidas, $codentsai){
        $codmerc=$store->get_value($iter,0);
        $quantidade=$this->pegaNumero($store->get_value($iter,2));
        $precooriginal=$this->pegaNumero($store->get_value($iter,4));
        $precocomdesconto=$this->pegaNumero($store->get_value($iter,5));
        
        // verifica quantidade vendida no entsai
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT quantidade FROM entsai WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc'";                
        $resultado=$con->Query($sql);            
        $i = $con->FetchRow($resultado);
        $quantvendida=$i[0];

        $novaquant=$quantvendida-$quantidade; // grava a quantidade que sobrou .. qt vendida-qt devolvida.. se carregau tudo grava zero 0
        	$this->sqlAUX.="UPDATE entsai SET quantidade='$novaquant' WHERE codentsai='$codsaidas' AND tipo='S' AND codmerc='$codmerc'; ";
        
        $this->sqlAUX.="INSERT INTO entsai (codentsai, tipo, codmerc, precooriginal, precocomdesconto, quantidade) VALUES ('$codentsai', 'D', '$codmerc', '$precooriginal', '$precocomdesconto', '$quantidade'); ";
        // retorna ao estoque
        $estoqueatual=$this->retornabusca4('estoqueatual','mercadorias','codmerc',$codmerc);
        $estoqueatual+=$quantidade;
		$this->sqlAUX.="UPDATE mercadorias SET estoqueatual='$estoqueatual' WHERE codmerc='$codmerc';";
  }
}
?>

