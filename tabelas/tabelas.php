<?php
class tabelas extends funcoes {
    
    function tabelas($tab,$tituloWindow,$editacod){
        // pega a variavel da tabela que sera usada.. por exemplo grpmerc
        $this->tabela=$tab;
        if($tab=="profissao"){
            $this->permissaoIncluir='020202';
            $this->permissaoExcluir='020203';
            $this->permissaoAlterar='020204';
        }elseif($tab=="placon"){
            $this->permissaoIncluir='020302';
            $this->permissaoExcluir='020303';
            $this->permissaoAlterar='020304';
        }elseif($tab=="grpmerc"){
            $this->permissaoIncluir='020402';
            $this->permissaoExcluir='020403';
            $this->permissaoAlterar='020404';
        }elseif($tab=="localarma"){
            $this->permissaoIncluir='020502';
            $this->permissaoExcluir='020503';
            $this->permissaoAlterar='020504';
        }elseif($tab=="romaneio"){
            $this->permissaoIncluir='020702';
            $this->permissaoExcluir='020703';
            $this->permissaoAlterar='020704';
        }elseif($tab=="parentesco"){
            $this->permissaoIncluir='020802';
            $this->permissaoExcluir='020803';
            $this->permissaoAlterar='020804';
        }elseif($tab=="midiapropaganda"){
            $this->permissaoIncluir='020902';
            $this->permissaoExcluir='020903';
            $this->permissaoAlterar='020904';
        }
        $this->sqldescricao="descricao";
        $this->sqlcodigo="codigo";
        $this->Fdescricao="Descricao";
        $this->Fcodigo="Codigo";
        $this->autocodigo=true;
        
        if($tab=="estados"){
            $this->sqldescricao="str_estado";
            $this->sqlcodigo="str_uf";
            $this->Fdescricao="Estado";
            $this->Fcodigo="UF";
            $this->autocodigo=false;
        }elseif($tab=="cep_tit"){
            $this->sqlcodigo="chave_tipo";
            $this->sqldescricao="abrev_tipo";
            $this->Fcodigo="Chave";
            $this->Fdescricao="Abreviatura";
            $this->autocodigo=false;
        }elseif($tab=="placon"){
            $this->autocodigo=false;
        }
        
        // pegaentry eh uma funcao do tabelas.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry($tituloWindow,$editacod);
        // cria o clist da tabela
        $this->abre_clist_tabelas();
        
	}    
	
    
    function pegaentry($tituloWindow,$editacod){
        $this->xml=$this->carregaGlade("tabelas",$tituloWindow);
  
        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
        $this->frame_codigo=$this->xml->get_widget('frame_codigo');
        
        $tmp=$this->frame_codigo->get_children();
        $tmp[1]->set_label($this->Fcodigo);
        
        //    bloqueia ou nao a edicao do codigo
        $this->entry_codigo->set_editable($editacod);
        $this->entry_codigo->set_sensitive($editacod);
        
		$this->entry_descricao=$this->xml->get_widget('entry_descricao');
        $this->frame_descricao=$this->xml->get_widget('frame_descricao');
        
        //$this->frame_descricao->set_label($this->Fdescricao);
        $tmp=$this->frame_descricao->get_children();
        $tmp[1]->set_label($this->Fdescricao);
        
        //$gi = new Dev_GuiInspector($this->frame_descricao);

        //pega o botao pra dar clique automaticamente ao gravar e excluir novo registro
        $this->button_atualiza_clist=$this->xml->get_widget("button_atualiza_clist");
        
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao($this->permissaoIncluir,false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao($this->permissaoAlterar,false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao($this->permissaoExcluir,false));
        
        if($this->tabela=="estados" or $this->tabela=="cep_tit"){
            $button_primeiro->set_sensitive(false);
            $button_ultimo->set_sensitive(false);
            $button_proximo->set_sensitive(false);
            $button_anterior->set_sensitive(false);
        }
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false,$editacod);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), $this->tabela, $this->tabela,"$this->sqlcodigo",'func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), $this->tabela, $this->tabela, "$this->sqlcodigo",'func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), $this->tabela, $this->tabela,"$this->sqlcodigo",'func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), $this->tabela, $this->tabela,"$this->sqlcodigo",'func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), $this->tabela, $this->tabela,"$this->sqlcodigo",'func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar este registro?',true,$editacod);
		
        
        //$this->janela->show_all();
	}
    
    function abre_clist_tabelas(){
    		if($this->tabela=="placon"){
    			$this->cria_clist_cadastro("placon", false, $this->sqlcodigo, $this->entry_descricao, $this->tabela, "select $this->sqlcodigo, $this->sqldescricao from placon ");
    		}else{
        		$this->cria_clist_cadastro($this->tabela, "$this->sqlcodigo", "$this->sqlcodigo", &$this->entry_descricao, $this->tabela, "select $this->sqlcodigo, $this->sqldescricao from $this->tabela");
        	}
    }
    
    function func_novo(){    
        $this->entry_codigo->set_text('');
        $this->entry_descricao->set_text('');
    }
    
    function func_gravar($alterar,$editacod){
        $this->codigo=$this->entry_codigo->get_text();
        if($editacod){
        		if(empty($this->codigo)){
        			msg("Codigo devera ser informado.");
        			return;
        		}elseif($this->ja_cadastrado("$this->tabela","$this->sqlcodigo","$this->codigo") and !$alterar){
        		    msg("$this->sqlcodigo ja cadastrado.");
        		    return;
	        }
        }
        if($alterar and empty($this->codigo)){
            msg("$this->sqlcodigo nao informado!");
            return;
        }
        
        $this->descricao=strtoupper($this->entry_descricao->get_text());        
        if(empty($this->descricao)){
            msg("Preencha o campo $this->sqldescricao!");
            return;
        }elseif($this->ja_cadastrado("$this->tabela","$this->sqldescricao","$this->descricao") and !$alterar){
            msg("$this->sqldescricao ja cadastrada.");
            return;
        }
        $this->grava_dados_tabela($alterar,$editacod);
       
        // atualiza clist
        //$this->button_atualiza_clist->clicked();
        $this->decideSeAtualizaClist();
    }
   
    function grava_dados_tabela($alterar,$editacod){    
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT $this->sqlcodigo FROM $this->tabela WHERE $this->sqlcodigo='$this->codigo'";            
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("$this->sqlcodigo nao encontrado!");                
            }else{
                $sql="UPDATE $this->tabela SET $this->sqldescricao='$this->descricao' where $this->sqlcodigo='$this->codigo'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            if($editacod){ // se for plano de contas ele deve alterar o codigo
                $sql="INSERT INTO $this->tabela ($this->sqlcodigo,$this->sqldescricao) ";
                $sql.="VALUES ('$this->codigo','$this->descricao')";
            }else{
                $sql="INSERT INTO $this->tabela ($this->sqldescricao) ";
                $sql.="VALUES ('$this->descricao')";
            }
            if($this->autocodigo){
                if ($lastcod=$con->QueryLastCod($sql)){
                    $this->entry_codigo->set_text($lastcod);
                    $this->status('Registro gravado com sucesso');
                }else {
                    msg("Erro ao executar SQL");
                };
            }else{
                if ($con->Query($sql)){
                    $this->status('Registro gravado com sucesso');
                }else {
                    msg("Erro ao executar SQL");
                };
            }
        }        
        $con->Disconnect();
    }
    
    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        $this->entry_codigo->set_text($resultado2["$this->sqlcodigo"]);
        $this->entry_descricao->set_text($resultado2["$this->sqldescricao"]);
    }

}
?>