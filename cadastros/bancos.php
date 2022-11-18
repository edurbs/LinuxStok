<?php
class bancos extends funcoes {
    function bancos(){
        $this->xml=$this->carregaGlade('bancos','Cadastro Contas de Banco');

        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
        
        $this->label_nome=$this->xml->get_widget('label_nome');
        $this->entry_numero=$this->xml->get_widget('entry_numero');
        $this->entry_numero->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select * from nomebanco', 
            true,
            &$this->entry_numero, 
            &$this->label_nome,
            "nomebanco",
            "sigla",
            "codigo"
        );
        $this->entry_numero->connect_simple('focus-out-event',
            array(&$this,retornabusca22), 
            'nomebanco', 
            &$this->entry_numero, 
            &$this->label_nome, 
            'codigo', 
            'nome', 
            'nomebanco'
        );          
          
          $this->entry_titular=$this->xml->get_widget('entry_titular');
          $this->entry_agencia=$this->xml->get_widget('entry_agencia');
          $this->entry_conta=$this->xml->get_widget('entry_conta');
          $this->entry_gerente=$this->xml->get_widget('entry_gerente');
          $this->entry_telefone=$this->xml->get_widget('entry_telefone');
          $this->entry_telefone->connect('key-press-event', array(&$this,'mascaraNew'),'(**)****-****');
          $this->entry_fax=$this->xml->get_widget('entry_fax');
          $this->entry_fax->connect('key-press-event', array(&$this,'mascaraNew'),'(**)****-****');
          $this->entry_email=$this->xml->get_widget('entry_email');
          
          $this->entry_valorcheque=$this->xml->get_widget('entry_valorcheque');
          $this->entry_valorcheque->connect('key-press-event', array(&$this, mascaraNew),'virgula2');
          
          $this->entry_clientedesde=$this->xml->get_widget('entry_clientedesde');
          $this->entry_clientedesde->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');
          $this->checkbutton_contadaempresa=$this->xml->get_widget('checkbutton_contadaempresa');
          $this->textView_obs=$this->xml->get_widget('text_obs');
          $this->textBuffer_obs=new GtkTextBuffer();
          $this->textView_obs->set_buffer($this->textBuffer_obs);
          
        
          $button_novo=$this->xml->get_widget('button_novo');
          $button_gravar=$this->xml->get_widget('button_gravar');
          $button_gravar->set_sensitive($this->verificaPermissao('010502',false));
          $button_alterar=$this->xml->get_widget('button_alterar');
          $button_alterar->set_sensitive($this->verificaPermissao('010504',false));
          $button_primeiro=$this->xml->get_widget('button_primeiro');
          $button_ultimo=$this->xml->get_widget('button_ultimo');
          $button_proximo=$this->xml->get_widget('button_proximo');
          $button_anterior=$this->xml->get_widget('button_anterior');
          $button_excluir=$this->xml->get_widget('button_excluir');
          $button_excluir->set_sensitive($this->verificaPermissao('010503',false));
        
          $button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?');
          $button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
          $button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), 'bancos', 'bancos','codbanco','func_novo','atualiza');
		  $button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), 'bancos', 'bancos','codbanco','func_novo','atualiza');
		  $button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), 'bancos', 'bancos','codbanco','func_novo','atualiza',&$this->entry_codigo);
		  $button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), 'bancos', 'bancos','codbanco','func_novo','atualiza',&$this->entry_codigo);
		  $button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), 'bancos', 'bancos','codbanco','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
          $button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
          
          $this->cria_clist_cadastro("bancos", "numero", "codbanco", &$this->label_nome,"bancos", "select b.codbanco, b.numero, bb.nome, b.titular, b.agencia, b.conta, b.clientedesde, b.valorcheque, b.gerente, b.telefone, b.fax, b.email, b.obs from bancos as b left join nomebanco as bb on (bb.codigo=b.numero)", true, array(true,'010503'));
          $this->func_novo();
          //$this->janela->show();
          
     }
    
    function func_novo(){
        
        $this->entry_codigo->set_text('');
        $this->label_nome->set_text('');
        $this->entry_titular->set_text('');
        $this->entry_numero->set_text('');
        $this->entry_agencia->set_text('');
        $this->entry_conta->set_text('');		
        $this->entry_gerente->set_text('');
        $this->entry_telefone->set_text(retorna_CONFIG("DDD"));
        $this->entry_fax->set_text(retorna_CONFIG("DDD"));
        $this->entry_email->set_text('');
        $this->entry_clientedesde->set_text('');
        $this->entry_valorcheque->set_text('');
		$this->checkbutton_contadaempresa->set_active(false);
        $this->textBuffer_obs->set_text('');
    }
    
    function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
        if(empty($codigo) and $alterar){
            msg('Codigo em branco!');
            return;
        }
        $numero=strtoupper($this->entry_numero->get_text());
        if (!$this->retornabusca2('nomebanco', &$this->entry_numero, &$this->label_nome, 'codigo', 'nome', 'nomebanco')){
            msg('Preencha corretamente o campo nome da institui�o!');
            return;
        }
        
        $agencia=strtoupper($this->entry_agencia->get_text());
        if(empty($agencia)){
            msg('Preencha o campo agencia!');
            return;
        }
        $conta=$this->entry_conta->get_text();
        if(empty($conta)){
            msg('Preencha o campo conta!');
            return;
        }
        $email=strtoupper($this->entry_email->get_text());
        if(!empty($email) and !$this->valida_email($email)){
            msg('E-Mail invalido!');
            return;
        }
        
        
        $titular=strtoupper($this->entry_titular->get_text());
        $gerente=strtoupper($this->entry_gerente->get_text());
        
        
        $valorcheque=$this->DeixaSoNumeroDecimal($this->entry_valorcheque->get_text(),2);
        
        $clientedesde=$this->entry_clientedesde->get_text();
        if($clientedesde=="" or $clientedesde=="00-00-0000"){
            $clientedesde="0001-01-01";
        }else{
            if($this->valida_data($clientedesde)){
                $clientedesde=$this->corrigeNumero($clientedesde,"dataiso");
            }else{
                msg("Data do campo 'cliente desde' incorreta!");
                return;
            }
        }

        
        $obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
        if($this->checkbutton_contadaempresa->get_active()){
        		$contadaempresa="1";
        	}else{
        		$contadaempresa="0";
        	}
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;               
        $con->Connect();
        
        $telefone=$con->EscapeString($this->entry_telefone->get_text());
        $fax=$this->entry_fax->get_text();
        
        if ($alterar){
            $sql="UPDATE bancos SET numero='$numero', titular='$titular', agencia='$agencia', conta='$conta', gerente='$gerente', telefone='$telefone', fax='$fax', email='$email', valorcheque='$valorcheque', clientedesde='$clientedesde', obs='$obs', contadaempresa='$contadaempresa' WHERE codbanco='$codigo'";
            if ($con->Query($sql)){
                $this->status('Registro alterado com sucesso');
            }else {
                msg("Erro ao alterar");
            }
        } else {
            $sql="INSERT INTO bancos (numero, titular, agencia, conta, gerente, telefone, fax, email, valorcheque, clientedesde, obs, contadaempresa) VALUES ('$numero', '$titular', '$agencia', '$conta', '$gerente', '$telefone', '$fax', '$email', '$valorcheque', '$clientedesde', '$obs', '$contadaempresa')";
            if ($lastcod=$con->QueryLastCod($sql)){            
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao gravar");
            }
        }
        
        $con->Disconnect();
        //$this->button_atualiza_clist->clicked();
        $this->decideSeAtualizaClist();
    }


    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        
        $this->entry_codigo->set_text($resultado2["codbanco"]);        
        $this->entry_numero->set_text($resultado2["numero"]);
        $this->retornabusca2('nomebanco', &$this->entry_numero, &$this->label_nome, 'codigo', 'nome', 'bancos'); 
        $this->entry_titular->set_text($resultado2["titular"]);
        $this->entry_agencia->set_text($resultado2["agencia"]);
        $this->entry_conta->set_text($resultado2["conta"]);
        $this->entry_gerente->set_text($resultado2["gerente"]);
        $this->entry_telefone->set_text($resultado2["telefone"]);
        $this->entry_fax->set_text($resultado2["fax"]);
        $this->entry_email->set_text($resultado2["email"]);
        $this->entry_clientedesde->set_text($this->corrigeNumero($resultado2["clientedesde"],"data"));
        $this->entry_valorcheque->set_text($this->mascara2($resultado2["valorcheque"],'moeda'));
        $this->textBuffer_obs->set_text($resultado2["obs"]);
        $this->checkbutton_contadaempresa->set_active($resultado2["contadaempresa"]);
    }
}
?>