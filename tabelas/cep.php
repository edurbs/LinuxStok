<?php
class cep extends funcoes {
    
    function cep(){
        // pegaentry eh uma funcao do cep.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry();
        // cria o clist da tabela
        $this->abre_clist_cep();
        
	}
    function grava_dados($alterar){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT chave_log FROM cep WHERE chave_log='$this->chave'";
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Chave n�o encontrada!");
            }else{
                $sql="UPDATE cep SET nome_log='$this->nome', chvlocal_log='$this->local', chvbai1_log='$this->bairro', cep8_log='$this->ceplog' WHERE chave_log='$this->chave'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO cep (nome_log, chvlocal_log, chvbai1_log, cep8_log) ";
            $sql.="VALUES ('$this->nome','$this->local','$this->bairro','$this->ceplog')";
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
        $this->xml=$this->carregaGlade("cep");
        $this->entry_chave=$this->xml->get_widget('entry_chave');
        
        $this->entry_local=$this->xml->get_widget('entry_local');
        $this->label_local=$this->xml->get_widget('label_local');        
        $this->label_estado=$this->xml->get_widget('label_estado');        
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
        $this->entry_local->connect_simple('focus-out-event',array(&$this,retornabusca22), 'cep_loc', &$this->entry_local, &$this->label_estado, 'chave_local', 'uf_local', 'cep_loc');
        
		$this->entry_nome=$this->xml->get_widget('entry_nome');        
        $this->entry_cep=$this->xml->get_widget('entry_cep');
        $this->entry_cep->connect('key-press-event', array(&$this,'mascaraNew'),'**.***-***');
        
        $this->entry_bairro=$this->xml->get_widget('entry_bairro');
        $this->label_bairro=$this->xml->get_widget('label_bairro');        
		$this->entry_bairro->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select b.chave_bai, b.extenso_bai, l.nome_local, l.cep8_local, l.uf_local from cep_bai as b left join cep_loc as l on (l.chave_local=b.local_bai)', 
            true,
            &$this->entry_bairro, 
            &$this->label_bairro,
            "cep_bai",
            "extenso_bai",
            "chave_bai"
        );
        $this->entry_bairro->connect_simple('focus-out-event',array(&$this,retornabusca22), 'cep_bai', &$this->entry_bairro, &$this->label_bairro, 'chave_bai', 'extenso_bai', 'cep_bai');
        
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
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), cep, cep,'chave_log','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), cep, cep, 'chave_log','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), cep, cep,'chave_log','func_novo','atualiza',&$this->entry_chave);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), cep, cep,'chave_log','func_novo','atualiza',&$this->entry_chave);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), cep, cep,'chave_log','func_novo','atualiza',&$this->entry_chave, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
		
        //$this->janela->show();
	}
    
    function abre_clist_cep(){
        $this->cria_clist_cadastro(cep, "c.nome_log", 'chave_log', &$this->entry_chave, cep, "select c.chave_log, c.nome_log, l.nome_local, b.extenso_bai, c.cep8_log FROM cep AS c LEFT JOIN cep_loc AS l ON (l.chave_local=c.chvlocal_log) LEFT JOIN cep_bai AS b ON (b.chave_bai=c.chvbai1_log)", true);
    }
    
    function func_novo(){
        $this->entry_chave->set_text('');
        $this->entry_nome->set_text('');
        $this->entry_local->set_text('');
        $this->label_local->set_text('');
        $this->entry_bairro->set_text('');
        $this->label_bairro->set_text('');
		$this->label_estado->set_text('');
        $this->entry_cep->set_text('');
    }
    
    function func_gravar($alterar){
        
        
        $this->chave=$this->entry_chave->get_text();
        $this->nome=$this->entry_nome->get_text();
        if(empty($this->nome)){
            msg('Preencha o campo nome!');
            return;
        }
        
        $this->ceplog=$this->entry_cep->get_text();
        if(empty($this->ceplog)){
            msg('Preencha o campo cep!');
            return;
        }elseif($this->ja_cadastrado("cep",'cep8_log',"$this->ceplog") and !$alterar){
            msg('Cep j� cadastrado.');
            return;
        }
        if(!empty($this->ceplog) and !$this->valida_CEP("$this->ceplog",null,&$this->label_estado)){
            msg('CEP invalido!');
            return;
        }
        
        $this->local=$this->entry_local->get_text();
        if(!$this->retornabusca2('cep_loc', &$this->entry_local, &$this->label_local, 'chave_local', 'nome_local', 'cep_tit')){
            msg('Preencha corretamente o campo Cidade!');
            return;
        }
        
        $this->bairro=$this->entry_bairro->get_text();
        if(!$this->retornabusca2('cep_bai', &$this->entry_bairro, &$this->label_bairro, 'chave_bai', 'extenso_bai', 'cep_tit')){
            msg('Preencha corretamente o campo Bairro!');
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
        
        $this->entry_chave->set_text($resultado2["chave_log"]);
        $this->entry_nome->set_text($resultado2["nome_log"]);
        $this->entry_local->set_text($resultado2["chvlocal_log"]);
        $this->retornabusca2('cep_loc', &$this->entry_local, &$this->label_local, 'chave_local', 'nome_local', 'cep_tit');
        $this->entry_bairro->set_text($resultado2["chvbai1_log"]);
        $this->retornabusca2('cep_bai', &$this->entry_bairro, &$this->label_bairro, 'chave_bai', 'extenso_bai', 'cep_tit');
        $this->retornabusca2('cep_loc', &$this->entry_local, &$this->label_estado, 'chave_local', 'uf_local', 'cep_loc');
        $this->entry_cep->set_text($resultado2["cep8_log"]);
    }

}
?>