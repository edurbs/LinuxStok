<?php
class pdv_extras extends funcoes {
	function chamabuscatabBuscamerc(){
		$this->window1_buscatabPDV->show_all();
		$this->treeview_buscatabPDV->grab_focus();
	}
	function fechaBuscatabPDV(){
		$this->window1_buscatabPDV->hide();
		//$this->janela->show_all();
		//$this->janela->grab_focus();
		return true;
	}
    function buscatabBuscamerc(){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
		
        $sql="select m.codmerc, m.descricao, m.precovenda, m.unidade, m.estoqueatual, f.nome, m.obs from mercadorias as m LEFT JOIN fabricantes AS f ON (f.codigo=m.codfab) WHERE m.inativa='0'  ORDER BY m.descricao";
        $resultado=$con->Query($sql);
		
        $janela = new GladeXML('interface'.bar."buscatab.glade2");
		
        $this->window1_buscatabPDV=$janela->get_widget('window1');
        $this->window1_buscatabPDV->connect_simple('focus-out-event',array($this,'fechaBuscatabPDV'));
		$this->window1_buscatabPDV->connect_simple('delete-event',array($this,'fechaBuscatabPDV'));
        
        //$this->window1_buscatabPDV->set_skip_taskbar_hint(true);

        $this->window1_buscatabPDV->set_icon_from_file('tema'.bar.'icone.png');
		
        if(retorna_CONFIG("fullbuscas")==1){
			$this->window1_buscatabPDV->fullscreen();
		}
		
 		$this->window1_buscatabPDV->set_uposition( retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy") );
        $this->window1_buscatabPDV->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );

        $scrolledwindow1=$janela->get_widget('scrolledwindow1');
     	
        for ($i=0;$i<$con->NumFields($resultado);$i++){
            $campos[$i]=$con->FieldName($resultado,$i);
        }
		
		$tmp=str_repeat('Gobject::TYPE_STRING,',count($campos));
        $tmp=substr($tmp,0,-1);        
        eval('$this->liststore_buscatabPDV=new GtkListStore('.$tmp.');');        
        $this->treeview_buscatabPDV = new GtkTreeView($this->liststore_buscatabPDV);
        $this->treeview_buscatabPDV->set_rules_hint(TRUE);
		$this->treeview_buscatabPDV->set_enable_search(TRUE);
        $this->add_coluna_treeview($campos,$this->treeview_buscatabPDV);
        $scrolledwindow1->add($this->treeview_buscatabPDV);
        $numerolin=$con->NumRows($resultado);
        if($numerolin==0){
        		msg("Nao ha mercadorias cadastradas!");
        		$this->fechaBuscatabPDV();
        		return;
        }
        // Adiciona linhas
        $this->atualizabuscatabPDV($sql);
		$this->button_ok_buscatabPDV= new GtkButton();
        // botao vai transferir dados pra tela
        $this->button_ok_buscatabPDV->connect_simple('clicked', array($this,'mudaPDV'), 'mercadorias',  'codmerc', $this->entry_codmerc, $this->button_codmercdoido, 'descricao');
        //$this->button_ok_buscatabPDV->connect_simple('clicked', array($this, 'fechaBuscatabPDV'));            

        // se dar enter fecha a janela e passa dados pro cadastro
        $this->treeview_buscatabPDV->connect('row-activated',array($this,clicaNoBotaoTransfere_buscatabPDV));
        $this->treeview_buscatabPDV->connect('key-press-event',array($this,keypressBuscatabPDV));
        $this->treeview_buscatabPDV->grab_focus();
        
        if(!$status=$janela->get_widget('status')){
			$hbox=new GtkHbox(false,0);
			$botaoatualizaVerdeVermelho=new GtkButton("");
			$p = GdkPixbuf::new_from_file('interface'.bar.'on.png');
			$a = new GtkImage;
			$a->set_from_pixbuf($p);
			$botaoatualizaVerdeVermelho->set_image($a);

			$botaoatualizaVerdeVermelho->connect_simple('clicked',array($this,'atualizabuscatabPDV'),$sql,true);
			$botaoatualizaVerdeVermelho->set_size_request(20,20);
			$status=new GtkLabel();
			$status->set_use_markup(true);
			$hbox->pack_start($botaoatualizaVerdeVermelho,false);
			$hbox->pack_start($status,true,true);
			$vbox1=$janela->get_widget('vbox1');
			$vbox1->pack_start($hbox,false);
			$vbox1->show_all();
			$accgrp = new GtkAccelGroup();
			//$this->botaoatualizaVerdeVermelho->add_accelerator('clicked', $accgrp, Gdk::KEY_F5, 0, Gtk::ACCEL_VISIBLE);
			$botaoatualizaVerdeVermelho->add_accelerator('clicked', $accgrp, 65474, 0, Gtk::ACCEL_VISIBLE);
			$this->window1_buscatabPDV->add_accel_group($accgrp);			
		}
        
        
        $con->Disconnect();
        
        //$this->fechaBuscatabPDV();
        $this->treeview_buscatabPDV->set_search_column(1);
        // seleciona primeira linha
        $selecao=$this->treeview_buscatabPDV->get_selection();
        list($model, $iter) = $selecao->get_selected();
        $selecao->select_path('0');
		$this->treeview_buscatabPDV->set_cursor("0");
		
    }
    
    function atualizabuscatabPDV($sql,$progress=false){
    	$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;    
        $con->Connect();
        $resultado=$con->Query($sql);
    		$numerolin=$con->NumRows($resultado);
    		if($numerolin==0){
        		msg("Nao ha mercadorias cadastradas!");
        		$this->fechaBuscatabPDV();
        		return;
        }
        $this->liststore_buscatabPDV->clear();
        $lin=0;    
        if($progress) $this->CriaProgressBar("Atualizando lista");        
        while ($lin<$numerolin){
            $linha[$lin]=$con->FetchRow($resultado);
            array_walk ($linha[$lin], array(&$this, 'utf8_encode_array'));
            $this->liststore_buscatabPDV->append($linha[$lin]);
            $lin++;
            if($progress){
				$atual=(100*$lin)/$numerolin;
                if($atual%5==0){
					$this->AtualizaProgressBar(null,$atual);
				}
            }
        }
        if($progress) $this->FechaProgressBar();
        $con->Disconnect();
	}
	
	function clicaNoBotaoTransfere_buscatabPDV(){
		$this->button_ok_buscatabPDV->clicked();
	}
    function keypressBuscatabPDV($widget, $evento){
    		$tecla=$evento->keyval;
    		if($tecla==65307){ // ESC
    			$this->fechaBuscatabPDV();
    		}
    } 
    function mudaPDV($tabela,$codigo,$entry,$label,$campoRetorna){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;    
        $con->Connect();    	
    		if($selecionado=$this->treeview_buscatabPDV->get_selection()){
			$cp0=$this->get_valor_liststore($selecionado,$this->liststore_buscatabPDV,0);
	        if(!empty($cp0)){
				$sql="SELECT $campoRetorna FROM $tabela WHERE $codigo='$cp0'";
	            //echo $sql;
	            $resultado=$con->Query($sql);
	            $resultado2=$con->FetchRow($resultado);
	            if(is_a($entry,'GtkEntry') or is_a($entry,'GtkLabel')){
	                $entry->set_text($cp0);
	            }
	            if(is_a($label,'GtkEntry') or is_a($label,'GtkLabel')){
	                $label->set_text($resultado2[0]);
	            }elseif(is_a($label,'GtkButton')){
	            		$label->clicked();
	            }
	        }
        }
        $con->Disconnect();
		$this->window1_buscatabPDV->hide();
		//$this->entry_listamercadorias->grab_focus();
    }
    function desconto($tipo){
        // abre tela de descontos
		$this->UltimoDescontoPDV=0;
        // bloqueia desconto totla com lista de pgtos vazia
        if($tipo=="T"){ // se tiver apenas uma linha
			if($this->numero_rows_liststore($this->liststore_prazo)==0){
				msg("Primeiro escolha uma forma de pagamento, depois de o desconto!");
        		    	return;
			}
			$this->setLabel_descontoT(0);
			$this->setLabel_valorfinal($this->pegaNumero($this->label_total));
        }
        $this->xmlDesconto=$this->carregaglade('desconto',false,false,false,false);
        $this->janelaDesconto = $this->xmlDesconto->get_widget('janeladesconto');
        $this->button_ok_desconto=$this->xmlDesconto->get_widget('button_ok_desconto');
        $this->button_ok_desconto->connect_simple('clicked',array(&$this, okdesconto),"$tipo");

        $this->button_cancelar_desconto=$this->xmlDesconto->get_widget('button_cancelar_desconto');
        $this->button_cancelar_desconto->connect_simple('clicked',array(&$this, hidejaneladesconto));

        $this->entry_descontoR=$this->xmlDesconto->get_widget('entry_descontoR');
        $this->entry_descontoR->connect("activate", array(&$this,"clica_button_ok_desconto"));
        $this->entry_descontoR->connect('key-press-event', array(&$this, mascaraNew),'virgula2');

        $this->entry_descontoP=$this->xmlDesconto->get_widget('entry_descontoP');
        $this->entry_descontoP->connect("activate", array(&$this,"clica_button_ok_desconto"));
        $this->entry_descontoP->connect('key-press-event', array(&$this, mascaraNew),'virgula2');

        $this->entry_acrescimoR=$this->xmlDesconto->get_widget('entry_acrescimoR');
        $this->entry_acrescimoR->connect("activate", array(&$this,"clica_button_ok_desconto"));
        $this->entry_acrescimoR->connect('key-press-event', array(&$this, mascaraNew),'virgula2');


        $this->entry_acrescimoP=$this->xmlDesconto->get_widget('entry_acrescimoP');
        $this->entry_acrescimoP->connect("activate", array(&$this,"clica_button_ok_desconto"));
        $this->entry_acrescimoP->connect('key-press-event', array(&$this, mascaraNew),'virgula2');

        $this->janelaDesconto->show();
    }

    function clica_button_ok_desconto(){
		$this->button_ok_desconto->clicked();
	}
    function okdesconto($tipo){
        // tipo U=unitario T=total
        $codigo=$this->getCodmerc();
        $quantidade=$this->getQuantidade();
        if(empty($codigo) and $tipo=='U'){
			msg('Digite o código da mercadoria!');
			return;
        }else{
            $descontoR=$this->pegaNumero($this->entry_descontoR);
            $descontoP=$this->pegaNumero($this->entry_descontoP);
            $acrescimoP=$this->pegaNumero($this->entry_acrescimoP);
            $acrescimoR=$this->pegaNumero($this->entry_acrescimoR);
            if($tipo=='U'){
                $precoatual=$this->retornabusca4('precovenda','mercadorias','codmerc',$codigo);
                $descontomaximo=$this->retornabusca4('descontomaximo','mercadorias','codmerc',$codigo);
            }else{
				$precoatual=$this->pegaNumero($this->label_total);
            }

            if($descontoR>0){
                $preconovo=$precoatual-$descontoR;
                $mostra=true;
            }elseif($descontoP>0){
                $preconovo=$precoatual-($precoatual/100*$descontoP);
                $mostra=true;
            }elseif($acrescimoR>0){
                $preconovo=$precoatual+$acrescimoR;
                $mostra=true;
            }elseif($acrescimoP>0){
                $preconovo=$precoatual+($precoatual/100*$acrescimoP);
                $mostra=true;
            }
            if($mostra and $tipo=='U'){
                // acha o desconto
                $diferenca=floatval($precoatual-$preconovo);

                // se a diferenca for maior que o desconto maximo sobre o preco original ou tem permissao de dar desconto maior que o maximo
                $calculo=floatval(($precoatual/100)*$descontomaximo);
                if($diferenca-0.01<=($calculo) or $this->verificaPermissao('030114',false)){
				    $this->UltimoDescontoPDV=$diferenca;
                    $preconovo=number_format($preconovo, 2, ',', '');
                    $this->setLabel_precounitario($preconovo);
		    
                }else{
                    msg('Desconto maior que o MAXIMO permitido!!');
                }
            }elseif($mostra and $tipo=='T'){
            		$desconto=$preconovo-$precoatual;
            		if($this->limiteDescontoVariacao[0]==false){	                
    		            $this->setLabel_descontoT($desconto);
    		            $this->setLabel_valorfinal($preconovo);
    		        }else{ // poe limite de desconto
    		        		$tmp=$this->limiteDescontoVariacao[1];
    		        		if($desconto*(-1)>$tmp and !$this->verificaPermissao('030117',false)){
    		        			msg("Sem permissao para dar desconto alem do limite desta forma de pagamento.");
    		        			return;
    		        		}else{
    		        			$this->setLabel_descontoT($desconto);
	    		            	$this->setLabel_valorfinal($preconovo);
    		        		}
    		        }
    		            //refaz as formas de pagamento
    		            $this->inserirparcela();
            }
            $this->hidejaneladesconto();
            return;
        }
    }

	function setLabel_descontoT($desconto){
		$desconto=number_format($desconto, 2, ',', '');
		$this->label_descontoT->set_text($desconto);
	}

    function hidejaneladesconto(){
        $this->janelaDesconto->destroy();
        $this->entry_descontoR->set_text('');
        $this->entry_descontoP->set_text('');
        $this->entry_acrescimoR->set_text('');
        $this->entry_acrescimoP->set_text('');
        $this->quantidade();
    }
	function trocausuariofecha(){
		return true;
	}
    function trocausuario(){
        if($this->moduloPDV<>"ativado"){
  	    	return;
  	    }

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="select count(*) from  nivel2funcionario";
        $resultado=$con->Query($sql);
        $i = $con->FetchRow($resultado);
        if($i[0]==0){ // se nao tiver usuario cadastrado
	        msg("Nao existe usuario cadastrado.");
            return;
        }

    		$this->janela->hide();
    		
    		$xml=new GladeXML('interface'.bar.'senhapdv.glade2');
    		$this->janelasenhapdv=$xml->get_widget('window1');
    		$this->janelasenhapdv->fullscreen();
    		$this->janelasenhapdv->connect('delete-event',array($this,'trocausuariofecha'));
        $this->janelasenhapdv->set_modal(TRUE);
    		$this->janelasenhapdv->set_icon_from_file('tema'.bar.'icone.png');
    		
    		$this->button_entrar=$xml->get_widget('button_ok');
    		$this->button_entrar->connect_simple('clicked', array($this,'trocausuarioEntrar'));
    		
		$this->usuarioPDV=$xml->get_widget('entry_codigo');
		$this->usuarioPDV->connect("key-press-event", array($this, "intro_keypressedPDV"), 'senha');
		
		$this->senhaPDV=$xml->get_widget('entry_senha');
		$this->senhaPDV->connect("key-press-event", array($this, "intro_keypressedPDV"), 'button_entrar');
		
		$this->usuarioPDV->grab_focus();
		$this->janelasenhapdv->show_all();
    }
    
    function trocausuarioEntrar(){
  	    global $NivelAcessoDoLoginGeral,$usuario, $parente;
  	    
            $erro=false;
            $this->usuario1 = $this->usuarioPDV->get_text();
            $this->usuario1=$this->DeixaSoNumero($this->usuario1);
            if (!$this->retornabusca4("nome",'funcionarios','codigo',$this->usuario1)){
                $erro=true;
            }else{
                $this->senha1 = $this->senhaPDV->get_text();

                $BancoDeDados=retorna_CONFIG("BancoDeDados");
                $con=new $BancoDeDados;
                $con->Connect();
                $sql="select codigonivelacesso,senha from nivel2funcionario where codigofuncionario='$this->usuario1'";
                $resultado=$con->Query($sql);
                $i = $con->FetchRow($resultado);
                if($i[1]<>$this->senha1) $erro=true;
            }
            if($erro){
                msg('Usuario/Senha invalido(s)!');
                return;
            }else{
                $NivelAcessoDoLoginGeral = $i[0];
                //if($parente){
                	//$nome_usuario=$this->retornabusca4('nome','funcionarios','codigo',$this->usuario1);
					//$parente->set_title('LinuxStok - Gestao Comercial GPL - Usuario : '.$nome_usuario);
				//}
            }
        $this->janelasenhapdv->hide();
        $usuario=$this->usuario1;
        $this->janela->show();
    }
}
?>