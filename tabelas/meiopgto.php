<?php
class meiopgto extends funcoes {
    
    function meiopgto(){        
        // pegaentry eh uma funcao do meiopgto.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry();
        // cria o clist da tabela
        $this->abre_clist_meiopgto();
        
	}
    function grava_dados_tabela($alterar){    
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT descricao FROM meiopgto WHERE codigo='$this->codigo'";            
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Codigo nao encontrado!");                
            }else{
                $sql="UPDATE meiopgto SET descricao='$this->descricao',codplacon='$this->placon' where codigo='$this->codigo'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO meiopgto (descricao,codplacon) ";
            $sql.="VALUES ('$this->descricao','$this->placon')";
            if ($lastcod=$con->QueryLastCod($sql)){            
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao executar SQL");
            };
        }        
        $con->Disconnect();
    }
    
	
    
    function pegaentry(){
        $this->xml=$this->carregaGlade("meiopgto");

		$this->entry_codigo=$this->xml->get_widget('entry_codigo');
        // bloqueia ou nao a edicao do codigo

 		$this->entry_descricao=$this->xml->get_widget('entry_descricao');
        
        $this->entry_placon=$this->xml->get_widget('entry_placon');
        $this->label_placon=$this->xml->get_widget('label_placon');
        $this->entry_placon->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, descricao from placon', 
            true,
            &$this->entry_placon, 
            &$this->label_placon,
            "placon",
            "descricao",
            "codigo"
        );
        $this->entry_placon->connect_simple('focus-out-event',
            array(&$this,retornabusca22), 
            'placon', 
            &$this->entry_placon, 
            &$this->label_placon, 
            'codigo', 
            'descricao', 
            'placon'
        );            

        
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('020104',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('020102',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('020103',false));
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false,$editacod);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), meiopgto, meiopgto,'codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), meiopgto, meiopgto, 'codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), meiopgto, meiopgto,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), meiopgto, meiopgto,'codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), meiopgto, meiopgto,'codigo','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
		
        //$this->janela->show();
	}
    
    function abre_clist_meiopgto(){
        $this->cria_clist_cadastro(meiopgto, "p.descricao", 'codigo', &$this->entry_descricao, meiopgto, "select m.codigo,m.descricao,m.codplacon,p.descricao from meiopgto as m left join placon as p on (p.codigo=m.codplacon)");
    }
    
    function func_novo(){    
        $this->entry_codigo->set_text('');
        $this->entry_descricao->set_text('');
        $this->entry_placon->set_text('');
        $this->label_placon->set_text('');
    }
    
    function func_gravar($alterar,$editacod){
        $this->codigo=$this->entry_codigo->get_text();
        if(empty($this->codigo) and $alterar){
            msg('Codigo nao informado!');
            return;
        }
        $this->descricao=strtoupper($this->entry_descricao->get_text());        
        if(empty($this->descricao)){
            msg('Preencha o campo descricao!');
            return;
        }elseif($this->ja_cadastrado("meiopgto",'descricao',"$this->descricao") and !$alterar){
            msg('Descricao ja cadastrada.');
            return;
        }
        $this->placon=strtoupper($this->entry_placon->get_text());        
        if (!$this->retornabusca2('placon', &$this->entry_placon, &$this->label_placon, 'codigo', 'descricao', 'placon')){
            msg('Preencha corretamente o campo Plano de Contas!');
            return;
        }
        $this->grava_dados_tabela($alterar);
       
        // atualiza clist
        $this->button_atualiza_clist->clicked();
    }
    
   
    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        $this->entry_codigo->set_text($resultado2["codigo"]);
        $this->entry_descricao->set_text($resultado2["descricao"]);
        $this->entry_placon->set_text($resultado2["codplacon"]);
        $this->retornabusca2('placon', &$this->entry_placon, &$this->label_placon, 'codigo', 'descricao', 'placon'); 
    }

}
?>