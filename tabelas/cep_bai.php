<?php
class cep_bai extends funcoes {
    
    function cep_bai(){
            // pegaentry eh uma funcao do cep_bai.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry();
        // cria o clist da tabela
        $this->abre_clist_cep_bai();
        
	}
    function grava_dados($alterar){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT chave_bai FROM cep_bai WHERE chave_bai='$this->chave'";
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Chave n�o encontrada!");
            }else{
                $sql="UPDATE cep_bai SET extenso_bai='$this->extenso', local_bai='$this->local' where chave_bai='$this->chave'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO cep_bai (extenso_bai, local_bai) ";
            $sql.="VALUES ('$this->extenso','$this->local')";
            if ($lastcod=$con->QueryLastCod($sql)){
                $this->entry_chave->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao executar SQL");
            };
        }        
        $con->Disconnect();
    }
    
	
    
    function pegaentry(){
        $this->xml=$this->carregaGlade("cep_bai");

        $this->entry_chave=$this->xml->get_widget('entry_chave');
		
        $this->entry_local=$this->xml->get_widget('entry_local');
        $this->label_local=$this->xml->get_widget('label_local');        
		$this->entry_local->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select chave_local, nome_local, uf_local, cep8_local from cep_loc', 
            true,
            &$this->entry_local, 
            &$this->label_local,
            "cep_loc",
            "nome_local",
            "chave_local"
        );
        $this->entry_local->connect_simple('focus-out-event',array(&$this,retornabusca22), 'cep_loc', &$this->entry_local, &$this->label_local, 'chave_local', 'nome_local', 'cep_loc');
        
        $this->entry_extenso=$this->xml->get_widget('entry_extenso');
        
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
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), cep_bai, cep_bai,'chave_bai','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), cep_bai, cep_bai, 'chave_bai','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), cep_bai, cep_bai,'chave_bai','func_novo','atualiza',&$this->entry_chave);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), cep_bai, cep_bai,'chave_bai','func_novo','atualiza',&$this->entry_chave);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), cep_bai, cep_bai,'chave_bai','func_novo','atualiza',&$this->entry_chave, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
		
        //$this->janela->show();
	}
    
    function abre_clist_cep_bai(){
        $this->cria_clist_cadastro(cep_bai, "chave_bai", 'chave_bai', &$this->entry_chave, cep_bai, "select b.chave_bai, b.extenso_bai, l.nome_local, l.uf_local, l.cep8_local from cep_bai AS b LEFT JOIN cep_loc AS l ON (b.local_bai=l.chave_local)");
    }
    
    function func_novo(){
        $this->entry_chave->set_text('');
		$this->entry_local->set_text('');
        $this->label_local->set_text('');
        $this->entry_extenso->set_text('');
    }
    
    function func_gravar($alterar){        
        $this->extenso=$this->entry_extenso->get_text();        
        $this->chave=$this->entry_chave->get_text();
        
        $this->local=$this->entry_local->get_text();
        if(!$this->retornabusca2('cep_loc', &$this->entry_local, &$this->label_local, 'chave_local', 'nome_local', 'cep_loc')){
            msg('Preencha corretamente o campo Local!');
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
        $this->entry_chave->set_text($resultado2["chave_bai"]);
        
        $this->entry_extenso->set_text($resultado2["extenso_bai"]);
        
        $this->entry_local->set_text($resultado2["local_bai"]);
        $this->retornabusca2('cep_loc', &$this->entry_local, &$this->label_local, 'chave_local', 'nome_local', 'cep_loc');
    }

}
?>