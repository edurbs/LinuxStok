<?php
class cep_loc extends funcoes {
    
    function cep_loc(){
        // pegaentry eh uma funcao do cep_loc.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry();
        // cria o clist da tabela
        $this->abre_clist_cep_loc();
        
	}
    function grava_dados($alterar){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT chave_local FROM cep_loc WHERE chave_local='$this->chave'";
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Chave n�o encontrada!");
            }else{
                $sql="UPDATE cep_loc SET nome_local='$this->nome', cep8_local='$this->cep', uf_local='$this->uf' where chave_local='$this->chave'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO cep_loc (nome_local,cep8_local, uf_local) ";
            $sql.="VALUES ('$this->nome','$this->cep','$this->uf')";
            if ($con->Query($sql)){
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao executar SQL");
            };
        }        
        $con->Disconnect();
    }
    
	
    
    function pegaentry(){
        $this->xml=$this->carregaGlade("cep_loc");

        $this->entry_chave=$this->xml->get_widget('entry_chave');
		$this->entry_nome=$this->xml->get_widget('entry_nome');
        $this->entry_cep=$this->xml->get_widget('entry_cep');
        $this->entry_cep->connect('key-press-event', array(&$this,'mascaraNew'),'**.***-***');
        
        $this->entry_uf=$this->xml->get_widget('entry_uf');
        $this->label_uf=$this->xml->get_widget('label_uf');
        $this->entry_uf->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select str_uf, str_estado from estados', 
            true,
            &$this->entry_uf, 
            &$this->label_uf,
            "estados",
            "str_estado",
            "str_uf"
        );
        $this->entry_uf->connect_simple('focus-out-event',array(&$this,retornabusca22), 'estados', &$this->entry_uf, &$this->label_uf, 'str_uf', 'str_estado', 'estados');
        
  
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('',false));
		
        $button_primeiro=$this->xml->get_widget('button_primeiro');        
		$button_ultimo=$this->xml->get_widget('button_ultimo');        
		$button_proximo=$this->xml->get_widget('button_proximo');        
		$button_anterior=$this->xml->get_widget('button_anterior');
     	$button_excluir=$this->xml->get_widget('button_excluir');
        
        $button_excluir->set_sensitive($this->verificaPermissao('',false));
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false,$editacod);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), cep_loc, cep_loc,'chave_local','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), cep_loc, cep_loc, 'chave_local','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), cep_loc, cep_loc,'chave_local','func_novo','atualiza',&$this->entry_chave);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), cep_loc, cep_loc,'chave_local','func_novo','atualiza',&$this->entry_chave);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), cep_loc, cep_loc,'chave_local','func_novo','atualiza',&$this->entry_chave, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
		
        //$this->janela->show();
	}
    
    function abre_clist_cep_loc(){
        $this->cria_clist_cadastro(cep_loc, "nome_local", 'chave_local', &$this->entry_chave, cep_loc, "select * from cep_loc");
    }
    
    function func_novo(){
        $this->entry_chave->set_text('');
		$this->entry_nome->set_text('');
        $this->entry_cep->set_text('');
        $this->entry_uf->set_text('');
        $this->label_uf->set_text('');
    }
    
    function func_gravar($alterar){
        $this->nome=$this->entry_nome->get_text();
        
        $this->chave=$this->entry_chave->get_text();

        $this->cep=$this->entry_cep->get_text();
        if(empty($this->cep)){
            msg('Preencha o campo cep!');
            return;
                    }elseif($this->ja_cadastrado("cep_loc",'cep8_local',"$this->cep") and !$alterar){
            msg('Cep j� cadastrado.');
            return;
        }        
        if(!empty($this->cep) and !$this->valida_CEP("$this->cep",null,&$this->entry_uf)){
            msg('CEP invalido!');
            return;
        }
        $this->uf=strtoupper($this->entry_uf->get_text());
        if(!$this->retornabusca2('estados', &$this->entry_uf, &$this->label_uf, 'str_uf', 'str_estado', 'estados')){
            msg('Preencha corretamente o campo UF!');
            return;
        }
        
        $this->grava_dados($alterar);
       
        // atualiza clist
        //$this->button_atualiza_clist->clicked();
        $this->decideSeAtualizaClist();
    }
    
   
    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        $this->entry_chave->set_text($resultado2["chave_local"]);
        $this->entry_nome->set_text($resultado2["nome_local"]);
        $this->entry_cep->set_text($resultado2["cep8_local"]);
        $this->entry_uf->set_text($resultado2["uf_local"]);
        $this->retornabusca2('estados', &$this->entry_uf, &$this->label_uf, 'str_uf', 'str_estado', 'estados');
    }

}
?>