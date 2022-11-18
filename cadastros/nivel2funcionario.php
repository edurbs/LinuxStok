<?php
class nivel2funcionario extends funcoes {
    
    function nivel2funcionario(){
        $this->xml=$this->carregaGlade("nivel2funcionario");

		$this->entry_nivel=$this->xml->get_widget('entry_nivel');
        $this->label_nivel=$this->xml->get_widget('label_nivel');
        $this->entry_nivel->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, descricao from nivelacesso', 
            true,
            &$this->entry_nivel, 
            &$this->label_nivel,
            "nivelacesso",
            "descricao",
            "codigo"
        );
        $this->entry_nivel->connect_simple('focus-out-event',
            array(&$this,retornabusca22), 
            'nivelacesso', 
            &$this->entry_nivel, 
            &$this->label_nivel, 
            'codigo', 
            'descricao', 
            'nivelacesso'
        );
        
        
        $this->entry_funcionario=$this->xml->get_widget('entry_funcionario');
        $this->label_funcionario=$this->xml->get_widget('label_funcionario');
        $this->entry_funcionario->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, nome from funcionarios', 
            true,
            &$this->entry_funcionario, 
            &$this->label_funcionario,
            "funcionarios",
            "nome",
            "codigo"
        );
        $this->entry_funcionario->connect_simple('focus-out-event',
            array(&$this,retornabusca22), 
            'funcionarios', 
            &$this->entry_funcionario, 
            &$this->label_funcionario, 
            'codigo', 
            'nome', 
            'funcionarios'
        );

		$this->entry_senha=$this->xml->get_widget('entry_senha');

        
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
		$button_excluir=$this->xml->get_widget('button_excluir');
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false,$editacod);
		$button_excluir->connect_simple('clicked', array(&$this, 'confirma_excluir'), 'Deseja excluir este registro?');
		
        //$this->janela->show();
        
	}

    function func_novo(){    
        $this->entry_nivel->set_text('');
        $this->label_nivel->set_text('');
        $this->entry_funcionario->set_text('');
        $this->label_funcionario->set_text('');
        $this->entry_senha->set_text('');
    }
    
    function func_gravar($alterar){
        $this->nivel=$this->entry_nivel->get_text();        
        if (!$this->retornabusca2('nivelacesso', &$this->entry_nivel, &$this->label_nivel, 'codigo', 'descricao', 'nivelacesso')){
            msg('Preencha corretamente o campo nivel de acesso!');
            return;
        }
        $this->funcionario=$this->entry_funcionario->get_text();        
        if (!$this->retornabusca2('funcionarios', &$this->entry_funcionario, &$this->label_funcionario, 'codigo', 'nome', 'funcionarios')){
            msg('Preencha corretamente o campo funcionario!');
            return;
        }
        $this->senha=$this->entry_senha->get_text();
        if(empty($this->senha)){
            msg('Digite a senha!');
            return;
        }
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        
        // conta quantas tipos de permissao existem
        $sql="select count(codigoctree) from ctree where descricao<>''";
        
        $resultado=$con->Query($sql);
        
        $i = $con->FetchRow($resultado);
        $totalctree=$i[0];
        // verifica se este nivel contem todas permissoes cadastradas
        $sql="select count(codigopermissao) from permissao where codigonivelacesso='$this->nivel'";
        $resultado=$con->Query($sql);
        $i = $con->FetchRow($resultado);
        $con->FreeResult($resultado);
        $totalpermissao=$i[0];
         // se permissao nao tiver todos os niveis cadastrados entao...
        if($totalctree<>$totalpermissao){
			msg("$totalctree<>$totalpermissao)");
            msg("Este nivel de acesso não está com as permissões cadastradas.\n Va no menu Sistema->Seguranca->Permissoes e verifique o nivel de acesso.");
        }else{
            $sql="DELETE FROM nivel2funcionario WHERE codigofuncionario=$this->funcionario";
            if(!$con->Query($sql)){
            		msg("Erro excluindo senha antiga");
            		return;
            }
            $sql="INSERT INTO nivel2funcionario VALUES ('$this->nivel','$this->funcionario','$this->senha')";
            if(!$con->Query($sql)){
            		msg("Erro inserindo nova senha.");
            		return;
            	}            
            $this->status('Registro gravado com sucesso');        
        }
        
        
        $con->Disconnect();        
    }
    
    function confirma_excluir(){
        $this->nivel=$this->entry_nivel->get_text();        
        if (!$this->retornabusca2('nivelacesso', &$this->entry_nivel, &$this->label_nivel, 'codigo', 'descricao', 'nivelacesso')){
            msg('Preencha corretamente o campo nivel de acesso!');
            return;
        }
        $this->funcionario=$this->entry_funcionario->get_text();        
        if (!$this->retornabusca2('funcionarios', &$this->entry_funcionario, &$this->label_funcionario, 'codigo', 'nome', 'funcionarios')){
            msg('Preencha corretamente o campo funcionario!');
            return;
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
        $sql="DELETE FROM nivel2funcionario WHERE codigofuncionario=$this->funcionario";
        $con->Query($sql);
        $this->status('Registro excluido com sucesso');        
        $con->Disconnect();        
        $this->func_novo();
    }

}
?>