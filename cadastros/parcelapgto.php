<?php
class parcelapgto extends funcoes
{
    function parcelapgto($tmp1,$tituloWindow )
    {
        $this->xml=$this->carregaGlade("parcelapgto",$tituloWindow);
        
        $this->entry_codigo=$this->xml->get_widget('entry_codigo');

        $this->entry_formapgto=$this->xml->get_widget('entry_formapgto');
        $this->label_formapgto=$this->xml->get_widget('label_formapgto');
        $this->entry_formapgto->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigoformapgto, descricao, variacao, parcelas, arredonda, ativa from formapgto', 
            true,
            &$this->entry_formapgto, 
            &$this->label_formapgto,
            "formapgto",
            "descricao",
            "codigoformapgto"
        );
        $this->entry_formapgto->connect_simple('focus-out-event',
            array(&$this,retornabusca22), 
            'formapgto', 
            &$this->entry_formapgto, 
            &$this->label_formapgto, 
            'codigoformapgto', 
            'descricao', 
            'formapgto'
        );
        // verifica quantos porcento ja foi parcelado nesta forma de pagamento
        $this->entry_formapgto->connect_simple('focus-out-event',array(&$this,'verificaParcelas'));

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

        $this->entry_meiopgto=$this->xml->get_widget('entry_meiopgto');
        $this->label_meiopgto=$this->xml->get_widget('label_meiopgto');
        $this->entry_meiopgto->connect('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, descricao from meiopgto', 
            true,
            &$this->entry_meiopgto, 
            &$this->label_meiopgto,
            "meiopgto",
            "descricao",
            "codigo"
        );
        $this->entry_meiopgto->connect_simple('focus-out-event',
            array(&$this,retornabusca22), 
            'meiopgto', 
            &$this->entry_meiopgto, 
            &$this->label_meiopgto, 
            'codigo', 
            'descricao', 
            'meiopgto'
        );
        $this->entry_meiopgto->connect_simple('focus-out-event',
            array(&$this,'sugereplaconparcela'));

		$this->radiobutton_porcentagem=$this->xml->get_widget('radiobutton_porcentagem');
		$this->radiobutton_porcentagem->set_active(TRUE);
		$this->radiobutton_perguntavalor=$this->xml->get_widget('radiobutton_perguntavalor');
		//$this->radiobutton_perguntavalor->connect_simple('toggled',array($this,'desliga_porcentagem'));
		//$this->radiobutton_porcentagem->connect_simple('toggled',array($this,'liga_porcentagem'));
		
        $this->spinbutton_porcentagem=$this->xml->get_widget('spinbutton_porcentagem');
        
        $this->spinbutton_prazo=$this->xml->get_widget('spinbutton_prazo');
        $this->combo_tipoprazo=$this->xml->get_widget('combo_prazo');
        $this->spinbutton_tolerancia=$this->xml->get_widget('spinbutton_tolerancia');

        //pega o botao pra dar clique automaticamente ao gravar e excluir novo registro
        $this->button_atualiza_clist=$this->xml->get_widget("button_atualiza_clist");

		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('010902',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('010904',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('010903',false));
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), parcelapgto, parcelapgto,'codigoparcelapgto','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), parcelapgto, parcelapgto, 'codigoparcelapgto','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), parcelapgto, parcelapgto,'codigoparcelapgto','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), parcelapgto, parcelapgto,'codigoparcelapgto','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir),parcelapgto, parcelapgto,'codigoparcelapgto','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
        
        $this->cria_clist_cadastro('parcelapgto', "f.descricao", 'codigoparcelapgto', &$this->entry_codigo, 'parcelapgto', "select p.codigoparcelapgto as codigo, f.descricao, m.descricao, p.porcentagem, p.prazo, p.tipoprazo, p.tolerancia, placon.codigo, placon.descricao from parcelapgto as p left join meiopgto as m on (p.codigomeiopgto=m.codigo) left join formapgto as f on (p.codigoformapgto=f.codigoformapgto) left join placon as placon on (placon.codigo=p.codplacon)",true, array(true,'010903'));
        

        //$this->janela->show();
    } // end of member function Formapgto
/*
	function liga_porcentagem(){
		$this->spinbutton_porcentagem->set_sensitive(TRUE);
	}
	function desliga_porcentagem(){
		$this->spinbutton_porcentagem->set_sensitive(FALSE);
		$this->spinbutton_porcentagem->set_text('0');
		$this->spinbutton_porcentagem->update();
	}*/
    function func_novo(){
        $this->entry_codigo->set_text('');
        $this->entry_formapgto->set_text('');
        $this->label_formapgto->set_text('');
        $this->entry_meiopgto->set_text('');
        $this->label_meiopgto->set_text('');
        $this->radiobutton_porcentagem->set_active(TRUE);
        $this->entry_placon->set_text('');
        $this->label_placon->set_text('');
        
        $tmp=new GtkAdjustment(100,0,100,1,10,20);
        $this->spinbutton_porcentagem->set_adjustment($tmp);
        $this->spinbutton_porcentagem->set_text(0.00);

        $this->spinbutton_prazo->set_text('0');
        $this->spinbutton_tolerancia->set_text('0');
    }
    
    function func_gravar($alterar){
        $this->codigo=$this->entry_codigo->get_text();
        if(empty($this->codigo) and $alterar){
            msg('Codigo nao informado!');
            return;
        }
        
        $this->formapgto=$this->entry_formapgto->get_text();
        if (!$this->retornabusca2('formapgto', $this->entry_formapgto, $this->label_formapgto, 'codigoformapgto', 'descricao', 'parcelapgto')){
            msg('Preencha corretamente o campo forma de pagamento!');
            return;
        }
        $this->spinbutton_porcentagem->update();
        $this->porcentagem=round($this->spinbutton_porcentagem->get_value(), 2);

        if($this->radiobutton_perguntavalor->get_active()){
        	$this->tipoentrada='1';
        	//$this->porcentagem=0;
        }else{
        	$this->tipoentrada='0';
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
            
        if($this->porcentagem>0 or $this->tipoentrada=='1'){
            // verifica se porcentagem nao eh maior do que ja cadastrado

            // verifica se existe outra parcela tipo entrada nesta forma de pgto
        	$sqlEntradas="SELECT tipoentrada FROM parcelapgto WHERE tipoentrada='1'  AND codigoformapgto='$this->formapgto' ";
        	if($alterar){ // se for alterar desconsidera parcela atual
        		$sqlEntradas.=" AND  codigoparcelapgto<>'$this->codigo' ";
        	}             
            $resultadoEntradas=$con->Query($sqlEntradas);
            $entradas = $con->NumRows($resultadoEntradas);
            if($entradas>=1 and $this->tipoentrada=='1'){
            	msg('Cadastre apenas uma parcela tipo "pergunta valor (entrada) para cada forma de pagamento"');
            	return;
            }            
            	
            // seleciona a soma das porcentagens ja cadastradas desta forma de pagamento sem contar a tipo entrada
            $sql="select sum(porcentagem) from parcelapgto where codigoformapgto='$this->formapgto' AND tipoentrada='0'";
            $resultado=$con->Query($sql);
            $i = $con->FetchRow($resultado);
            // pega o maximo de porcentagem que falta para completar esta forma de pagamento
            
            // se for alterar desconsidera o valor da parcela que esta digitando agora
            $estaporcentagem=$this->retornabusca4('porcentagem','parcelapgto','codigoparcelapgto',$this->codigo);
             
            if($alterar) $maximo=100-($i[0]-$estaporcentagem);
            else $maximo=100-$i[0];
            /*
            echo "esta".$estaporcentagem."\n";
            echo 'max'.$maximo."\n";
            echo "total$i[0]\n";
            */
                        
            if($this->porcentagem>$maximo and $this->tipoentrada=='0'){
                msg("Porcentagem maior do que valores ja cadastrados. Digite uma porcentagem menor ou verifique as outras parcelas");
                return;
            }
        }else{
            msg("Digite um valor maior que zero para a porcentagem ou verifique as porcentagens das outras parcelas");
            return;
        }
        //$this->spinbutton_prazo->update();
        $this->prazo=$this->spinbutton_prazo->get_value_as_int();
        $combo_entry=$this->combo_tipoprazo->entry;
        $this->tipoprazo=$combo_entry->get_text();
        $this->spinbutton_tolerancia->update();
        $this->tolerancia=$this->spinbutton_tolerancia->get_value_as_int();
        
        

        $this->meiopgto=$this->entry_meiopgto->get_text();
        if (!$this->retornabusca2('meiopgto', &$this->entry_meiopgto, &$this->label_meiopgto, 'codigo', 'descricao', 'parcelapgto')){
            msg('Preencha corretamente o campo meio de pagamento!');
            return;
        }
        
        $this->placon=strtoupper($this->entry_placon->get_text());        
        if (!$this->retornabusca2('placon', &$this->entry_placon, &$this->label_placon, 'codigo', 'descricao', 'placon')){
            msg('Preencha corretamente o campo Plano de Contas!');
            return;
        }
        
        if ($alterar){
            $sql="SELECT codigoformapgto FROM parcelapgto WHERE codigoparcelapgto='$this->codigo'";            
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Codigo nao encontrado!");                
            }else{
                $sql="UPDATE parcelapgto SET codigoformapgto='$this->formapgto', codigomeiopgto='$this->meiopgto', porcentagem='$this->porcentagem', prazo='$this->prazo', tipoprazo='$this->tipoprazo', tolerancia='$this->tolerancia', codplacon='$this->placon', tipoentrada='$this->tipoentrada' WHERE codigoparcelapgto='$this->codigo'";
                
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO parcelapgto (codigoformapgto, codigomeiopgto, porcentagem, prazo, tipoprazo, tolerancia, codplacon, tipoentrada) ";
            $sql.="VALUES ('$this->formapgto','$this->meiopgto','$this->porcentagem','$this->prazo','$this->tipoprazo','$this->tolerancia','$this->placon', '$this->tipoentrada')";
            if ($lastcod=$con->QueryLastCod($sql)){            
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao executar SQL");
            };
        }        
        $con->Disconnect();
       
        // atualiza clist
        //$this->button_atualiza_clist->clicked();
		$this->decideSeAtualizaClist();
    }
    
    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        if($resultado2["tipoentrada"]=="1"){
        	$this->radiobutton_perguntavalor->set_active(TRUE);
        }else{
        	$this->radiobutton_porcentagem->set_active(TRUE);
        }
        $this->entry_codigo->set_text($resultado2["codigoparcelapgto"]);
        $this->entry_formapgto->set_text($resultado2["codigoformapgto"]);
        $this->retornabusca2('formapgto', &$this->entry_formapgto, &$this->label_formapgto, 'codigoformapgto', 'descricao', 'parcelapgto'); 
        $this->entry_meiopgto->set_text($resultado2["codigomeiopgto"]);
        $this->retornabusca2('meiopgto', &$this->entry_meiopgto, &$this->label_meiopgto, 'codigo', 'descricao', 'parcelapgto'); 
		$this->spinbutton_porcentagem->set_text($resultado2["porcentagem"]);
		$this->spinbutton_porcentagem->update();
		
        $combo_entry=$this->combo_tipoprazo->entry;
        $combo_entry->set_text($resultado2["tipoprazo"]);
        
        $this->entry_placon->set_text($resultado2["placon"]);
        $this->retornabusca2('placon', &$this->entry_placon, &$this->label_placon, 'codigo', 'descricao', 'placon'); 

        $this->spinbutton_prazo->set_text($resultado2["prazo"]);
		$this->spinbutton_prazo->update();
        $this->spinbutton_tolerancia->set_text($resultado2["tolerancia"]);
		$this->spinbutton_tolerancia->update();
        
        $this->entry_placon->set_text($resultado2["codplacon"]);
        $this->retornabusca2('placon', &$this->entry_placon, &$this->label_placon, 'codigo', 'descricao', 'placon'); 
        
    }
    
    function verificaParcelas(){
        $this->formapgto=$this->entry_formapgto->get_text();
        if(!empty($this->formapgto)){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();	
            // seleciona a soma das porcentagens ja cadastradas desta forma de pagamento
            $sql="SELECT SUM(porcentagem) FROM parcelapgto WHERE codigoformapgto='$this->formapgto' AND tipoentrada='0'";
            $resultado=$con->Query($sql);
            $i = $con->FetchRow($resultado);
            // pega o maximo de porcentagem que falta para completar esta forma de pagamento
            $maximo=100-$i[0];
            $tmp=new GtkAdjustment($maximo,0,$maximo,1,10,20);
            //$this->spinbutton_porcentagem->set_adjustment($tmp);
            //$this->spinbutton_porcentagem->update();
            //$this->spinbutton_porcentagem->set_text($maximo);
            
        }
    }
    
    function sugereplaconparcela(){
        $this->meiopgto=$this->entry_meiopgto->get_text();
        $tmp=$this->retornabusca4('codplacon','meiopgto','codigo',"$this->meiopgto");
        $this->entry_placon->set_text($tmp);        
        if (!empty($this->meiopgto) and !$this->retornabusca2('placon', &$this->entry_placon, &$this->label_placon, 'codigo', 'descricao', 'placon')){
            msg('Preencha corretamente o campo Plano de Contas!');
            return;
        }    
    }
} // end of parcelapgto

?>
