<?php
class permissoes extends funcoes {

    function permissoes(){
        //if(!$iniciar) return;
        //global $bar;
        $this->xml=$this->carregaGlade('permissoes');
        /*
        if(empty($this->xml)){
            $this->xml = new GladeXML('interface'.$bar.'permissoes.glade');
            $this->janela = $this->xml->get_widget('window1');
            $this->janela->hide();
            $this->janela->connect_object('delete-event', array(&$this,'fecha_janela'));
            $this->janela->set_uposition(retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy"));
            $this->janela->set_default_size(intval(retorna_CONFIG("largura")),intval(retorna_CONFIG("altura")));
        }*/
        //$gdkwindow=$this->janela->window;
        //$this->spacing=2; // espacamento da imagem on off
        //$this->off = $this->file2pixmap($gdkwindow, getcwd().'/interface/off.png', null, false, null);            
        //$this->on = $this->file2pixmap($gdkwindow, getcwd().'/interface/on.png', null, false, null);
        
        //$this->janela->set_default_size(retorna_CONFIG("largura"),retorna_CONFIG("altura"));
        
        $this->entry_usuario=$this->xml->get_widget('entry_usuario');
        $this->label_usuario=$this->xml->get_widget('label_usuario');
        $this->label_usuario->connect_simple('event',array($this,'eventoLabel')); 
        $this->entry_usuario->connect('key_press_event', 
            array($this,entry_enter), 
            'select codigo, descricao from nivelacesso', 
            true,
            $this->entry_usuario, 
            $this->label_usuario,
            "nivelacesso",
            "descricao",
            "codigo"
        );
        $this->entry_usuario->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'nivelacesso', 
            $this->entry_usuario, 
            $this->label_usuario, 
            'codigo', 
            'descricao', 
            'nivelacesso'
        );
        
        //$this->treestore_permissao = new GtkTreeStore(Gobject::TYPE_STRING, Gtk::TYPE_BOOLEAN);
        $this->treestore_permissao = new GtkTreeStore(Gobject::TYPE_STRING, Gobject::TYPE_STRING, Gobject::TYPE_STRING);
        
        $this->treeview_permissao=new GtkTreeView($this->treestore_permissao);
        
        $this->treeview_permissao->connect('row-activated',array($this,'cliqueCTree'));
        
        $cell_renderer = new GtkCellRendererText(); 
		//Create the first column, make it resizable and sortable
		$colLanguage = new GtkTreeViewColumn('Descricao', $cell_renderer, 'text', 0);
		//make the column resizable in width
		$colLanguage->set_resizable(true);
		//make it sortable and let it sort after model column 1
		//$colLanguage->set_sort_column_id(0);
		//add the column to the view
		$this->treeview_permissao->append_column($colLanguage);


        $cell_renderer2 = new GtkCellRendererText(); 
		//Create the first column, make it resizable and sortable
		$colLanguage2 = new GtkTreeViewColumn('Ativo', $cell_renderer2, 'text', 1);
		//make the column resizable in width
		$colLanguage2->set_resizable(true);
		//make it sortable and let it sort after model column 1
		//$colLanguage2->set_sort_column_id(1);
		//add the column to the view
		$this->treeview_permissao->append_column($colLanguage2);
		
		$cell_renderer3 = new GtkCellRendererText(); 
		$colLanguage3 = new GtkTreeViewColumn('Codigo', $cell_renderer3, 'text', 2);
		$colLanguage3->set_resizable(true);
		$this->treeview_permissao->append_column($colLanguage3);
		$colLanguage3->set_visible(false);
        
        //we want to display a boolean value, so we can use a check box for display
		/*$bool_cell_renderer = new GtkCellRendererToggle();
		$colUsed = new GtkTreeViewColumn('Ativado', $bool_cell_renderer, 'true', 1);
		//$colUsed->set_sort_column_id(1);
		$this->treeview_permissao->append_column($colUsed);*/

        $this->scrolledwindow1=$this->xml->get_widget('scrolledwindow1');
        $this->scrolledwindow1->add($this->treeview_permissao);
        $this->scrolledwindow1->show_all();
        //$this->janela->show_all();
        //$this->janela->set_focus($this->entry_usuario);
        $this->entry_usuario->grab_focus();
    }
    
   
    function encheCTree(){
        $this->usuario=$this->entry_usuario->get_text();
        if (!$this->retornabusca2('nivelacesso', &$this->entry_usuario, &$this->label_usuario, 'codigo', 'descricao', 'nivelacesso') and !empty($this->usuario)){
            msg('Preencha corretamente o campo N�vel de Usuario!');
        }elseif(!empty($this->usuario)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();
            // conta quantas tipos de permissao existem
            $sql="select count(codigoctree) from ctree where descricao<>''";
            
            $resultado=$con->Query($sql);
            
            $i = $con->FetchRow($resultado);
            $totalctree=$i[0];
            // verifica se este nivel contem todas permissoes cadastradas
            $sql="select count(codigopermissao) from permissao where codigonivelacesso='$this->usuario'";
            $resultado=$con->Query($sql);
            $i = $con->FetchRow($resultado);
            $con->FreeResult($resultado);
            $totalpermissao=$i[0];
            
            if($totalctree<>$totalpermissao){ // se permissao nao tiver todos os niveis cadastrados entao cria tudo como true
        		//echo "$totalctree<>$totalpermissao";
        		msg("Serao criadas permissoes para este nivel.\nQualquer configuracao anterior de permissao sera perdida para este nivel.\nSera necessario reconfigurar as permissoes para este nivel.");
        		$this->CriaProgressBar("Criando permissoes...");        		
        		$sql="DELETE FROM permissao WHERE codigonivelacesso='$this->usuario'";
        		if(!$con->Query($sql)){
        			msg("Erro excluindo permissoes.");
        			return;
        		}
        		$this->AtualizaProgressBar(null,0);
                $sql="select codigoctree from ctree where descricao<>''";
                $resultado=$con->Query($sql);                
                $linhas=$con->NumRows($resultado);
                $iedu=0;
                while($i = $con->FetchRow($resultado)){
                    $sql2="INSERT INTO permissao (codigonivelacesso, codigoctree, permitido) VALUES ('$this->usuario','$i[0]','1');";
                    if(!$con->Query($sql2,null,"Criando permissoes")){
		                msg('Erro ao incluir permissoes');
		                $this->FechaProgressBar();
		                return;
                    }
                    $iedu++;
            		$atual=(100*$iedu)/$linhas;
            		$this->AtualizaProgressBar(null,$atual,true);
                }
                $this->FechaProgressBar();
            }            
            $con->FreeResult($resultado);
            $sql="SELECT codigoctree,descricao from ctree order by codigoctree";
             
            
            $resultado=$con->Query($sql);
            $this->treestore_permissao->clear();
            //$numero=$con->NumRows($resultado);
            $i = $con->FetchRow($resultado);
            $vai=true;
            //for($conta=0;$conta<$numero;$conta++){ 010203
            //                                       012345
            while($vai){
                //echo $conta;
                $navo=substr($i[0],0,2);
                //$avo[$navo]=$this->ctree_usuario->insert_node(null,null,array($i[1]),$this->spacing,null,null,null,null,null,false);
                //append a row to the root (NULL for parent)
				$avo[$navo] = $this->treestore_permissao->append(null, array($i[1],null,null));
                $i = $con->FetchRow($resultado);
                while($navo==substr($i[0],0,2)){
                    $npai=substr($i[0],2,2);
                    $pai[$npai]=$this->treestore_permissao->append($avo[$navo], array($i[1], null, null));
                    //$this->ctree_usuario->insert_node($avo[$navo],null,array($i[1]),$this->spacing,null,null,null,null,null,false);
                    $i = $con->FetchRow($resultado);
                    while($npai==substr($i[0],2,2)){
                        $nfilho=substr($i[0],4,2);
                        $filho[$nfilho]=$this->treestore_permissao->append($pai[$npai], array($i[1], null,$i[0]));
                        //$this->ctree_usuario->insert_node($pai[$npai],null,array($i[1]),$this->spacing,null,null,null,null,null,false);
                        // on ou off nesta linha
                        $this->onoff($this->treestore_permissao,$filho[$nfilho],$i[0]);
                        if(!$i = $con->FetchRow($resultado)){
                            $vai=false;
                        }
                    }
                }
            }
            
            
            $con->Disconnect();
        }
    }
    
    function onoff($widget,$node,$codigoctree){
        // funcao que coloca true se tiver true no Banco de dados
        //$retorno=$widget->node_get_pixtext($node,0);
        $this->codigoctree=$codigoctree;
        //if(!empty($codigoctree)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();        
            $sql="SELECT permitido FROM permissao where codigonivelacesso='$this->usuario' AND codigoctree='$codigoctree';";
            $resultado=$con->Query($sql);
            $i = $con->FetchRow($resultado);
            $con->Disconnect();
        //}else{
          //  $permitido='1'; //true
        //}
        //eval("\$permitido=$i[0];");
        $permitido=$i[0];
        if($permitido==1){ // se true
            $this->treestore_permissao->set($node,1,'OK');
            //$this->ctree_usuario->node_set_row_data($node,array(true,$codigoctree));
            //$widget->node_set_pixtext($node,0, $retorno[0], $this->spacing,$this->on[0], $this->on[1]);
        }else{ // se false
            $this->treestore_permissao->set($node,1,'');
            //$this->ctree_usuario->node_set_row_data($node,array(false,$codigoctree));            
            //$widget->node_set_pixtext($node,0, $retorno[0], $this->spacing,$this->off[0], $this->off[1]);            
        }        
        
    }
    
    function cliqueCTree($treeview, $path, $colunm){
    		// treeview, path, colunm 
        // true ou false dependendo do clique do usuario e j� grava no banco de dados tambpem
        $iter=$this->treestore_permissao->get_iter($path);
        $valor=$this->treestore_permissao->get_value($iter,1);
        $codigo=$this->treestore_permissao->get_value($iter,2);
        if($valor=="OK"){
        		$permitido=0; //false		
        	}else{
        		$permitido=1; //true
        	}
        //$retorno=$widget->node_get_pixtext($node,0);
        //print_r($retorno);        
        //$permitido=$widget->node_get_row_data($node);
        //print_r($permitido);
        if($permitido==1 and !empty($codigo)){ // se true
        		$this->treestore_permissao->set($iter,1,'OK');
            //$this->ctree_usuario->node_set_row_data($node,array(false,$permitido[1]));
            //$widget->node_set_pixtext($node,0, $retorno[0], $this->spacing,$this->off[0], $this->off[1]);                
        }elseif($permitido==0 and !empty($codigo)){ // se false
        		$this->treestore_permissao->set($iter,1,'');
            //$this->ctree_usuario->node_set_row_data($node,array(true,$permitido[1]));
            //$widget->node_set_pixtext($node,0, $retorno[0], $this->spacing,$this->on[0], $this->on[1]);
        }else{
        		return;
        }   
        $sql="UPDATE permissao SET permitido='$permitido' WHERE codigonivelacesso='$this->usuario' AND codigoctree='$codigo'";             
        //$widget->unselect($node);
        
        //echo $sql;
        //if($sql){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $con->Query($sql);
        $con->Disconnect();
        //}
    }
    
    function eventoLabel(){
        $tmp=$this->label_usuario->get();
        if($tmp<>$this->label and !empty($tmp)){
            $this->label=$tmp;
            $this->encheCTree();
        }
    }
}

?>