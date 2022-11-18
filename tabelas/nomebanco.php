<?php
class nomebanco extends funcoes {
    
    function nomebanco(){
        // pegaentry eh uma funcao do nomebanco.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry();
        // cria o clist da tabela
        $this->abre_clist_nomebanco();
        
	}
    function grava_dados($alterar){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT nome FROM nomebanco WHERE codigo='$this->codigo'";            
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Codigo nao encontrado!");                
            }else{
                $sql="UPDATE nomebanco SET nome='$this->nome',sigla='$this->sigla' where codigo='$this->codigo'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO nomebanco (codigo,sigla,nome) ";
            $sql.="VALUES ('$this->codigo','$this->sigla','$this->nome')";
            if ($con->Query($sql)){            
                //$this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                echo "error";
                msg("Erro ao executar SQL");
            };
        }        
        $con->Disconnect();
    }
    
	
    
    function pegaentry(){
        $this->xml=$this->carregaGlade("nomebanco");

		$this->entry_codigo=$this->xml->get_widget('entry_codigo');
        
        $this->entry_sigla=$this->xml->get_widget('entry_sigla');
		$this->entry_nome=$this->xml->get_widget('entry_nome');

        //pega o botao pra dar clique automaticamente ao gravar e excluir novo registro
        $this->button_atualiza_clist=$this->xml->get_widget("button_atualiza_clist");
        
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('020602',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('020604',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('020603',false));
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false,$editacod);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), nomebanco, nomebanco,'codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), nomebanco, nomebanco, 'codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), nomebanco, nomebanco,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), nomebanco, nomebanco,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), nomebanco, nomebanco,'codigo','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
		
        //$this->janela->show();
	}
    
    function abre_clist_nomebanco(){
        $this->cria_clist_cadastro(nomebanco, "nome", 'codigo', &$this->entry_nome, nomebanco, "select * from nomebanco");
    }
    
    function func_novo(){    
        $this->entry_codigo->set_text('');
        $this->entry_nome->set_text('');
        $this->entry_sigla->set_text('');
    }
    
    function func_gravar($alterar){
        $this->codigo=$this->DeixaSoNumero($this->entry_codigo->get_text());
        if(empty($this->codigo)){
            msg('Codigo nao informado! Deve ser s�numeros.');
            return;
        }elseif($this->ja_cadastrado("nomebanco",'codigo',"$this->codigo") and !$alterar){
            msg('Codigo ja cadastrado.');
            return;
        }
        $this->nome=strtoupper($this->entry_nome->get_text());        
        if(empty($this->nome)){
            msg('Preencha o campo nome!');
            return;
        }elseif($this->ja_cadastrado("nomebanco",'nome',"$this->nome") and !$alterar){
            msg('Descricao ja cadastrada.');
            return;
        }
        $this->sigla=strtoupper($this->entry_sigla->get_text());        
        if(empty($this->sigla)){
            msg('Preencha o campo sigla!');
            return;
        }elseif($this->ja_cadastrado("nomebanco",'sigla',"$this->sigla") and !$alterar){
            msg('Sigla j�cadastrada.');
            return;
        }
        $this->grava_dados($alterar);
       
        // atualiza clist
        $this->button_atualiza_clist->clicked();
    }
    
   
    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        $this->entry_codigo->set_text($resultado2["codigo"]);
        $this->entry_sigla->set_text($resultado2["sigla"]);
        $this->entry_nome->set_text($resultado2["nome"]);
    }

}
?>