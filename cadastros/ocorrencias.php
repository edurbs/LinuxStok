<?php

class ocorrencias extends funcoes{
	function ocorrencias(){
        $this->xml=$this->carregaGlade("ocorrencia");

        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
		$this->entry_resumo=$this->xml->get_widget('entry_resumo');
        
        $this->combo_cadastro=$this->xml->get_widget('combo_cadastro');
        $this->combo_cadastro->connect("changed", array($this,'escolha_cadastro'));
        $this->entry_cadastro=$this->xml->get_widget('entry_cadastro');
        $this->label_cadastro=$this->xml->get_widget('label_cadastro');
        
        $this->entry_data=$this->xml->get_widget('entry_data');
        $this->entry_data->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');

        $this->entry_hora=$this->xml->get_widget('entry_hora');
        $this->entry_hora->connect('key-press-event', array($this,'mascaraNew'),'**:**:**');
        
        $this->entry_funcionario=$this->xml->get_widget('entry_funcionario');
        $this->label_funcionario=$this->xml->get_widget('label_funcionario');
        $this->entry_funcionario->connect('key_press_event', 
            array($this,entry_enter), 
            'select *  from funcionarios', 
            true,
            $this->entry_funcionario, 
            $this->label_funcionario,
            "funcionarios",
            "nome",
            "codigo"
        );
		$this->entry_funcionario->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'funcionarios', 
            $this->entry_funcionario, 
            $this->label_funcionario, 
            'codigo', 
            'nome'
        );
        
        $this->entry_tipo=$this->xml->get_widget('entry_tipo');
        $this->label_tipo=$this->xml->get_widget('label_tipo');
        $this->entry_tipo->connect('key_press_event', 
            array($this,entry_enter), 
            'select *  from ocorrencia_tipo', 
            true,
            $this->entry_tipo, 
            $this->label_tipo,
            "ocorrencia_tipo",
            "descricao",
            "codigo"
        );
		$this->entry_tipo->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            'ocorrencia_tipo', 
            $this->entry_tipo, 
            $this->label_tipo, 
            'codigo', 
            'descricao'
        );
        
        $this->entry_conta=$this->xml->get_widget('entry_conta');
        $this->label_conta=$this->xml->get_widget('label_conta');

        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);
		
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('',false));

		$button_novo->connect_simple('clicked', confirma, array($this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',null);
		$button_gravar->connect_simple('clicked', confirma, array($this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array($this,cadastro_primeiro), 'ocorrencia', 'ocorrencia','codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array($this,cadastro_ultimo), 'ocorrencia', 'ocorrencia','codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array($this,cadastro_proximo), 'ocorrencia', 'ocorrencia','codigo','func_novo','atualiza',$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array($this,cadastro_anterior), 'ocorrencia', 'ocorrencia','codigo','func_novo','atualiza',$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array($this,confirma_excluir), 'ocorrencia', 'ocorrencia','codigo','func_novo','atualiza',$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array($this, 'func_gravar'),'Deseja alterar este registro?',true,array(true,''));		

				
       	$this->func_novo();
        
        $this->cria_clist_cadastro("ocorrencia", "resumo", "codigo", $this->entry_resumo, "ocorrencia", "select * from ocorrencia", true, array(true,''));
        
	}
 
 	function escolha_cadastro($limpa=true){
 		$this->cadastro=$this->combo_cadastro->get_active_text();
 		
		if(strtolower($this->cadastro)<>"clientes" and strtolower($this->cadastro)<>"fornecedores" and strtolower($this->cadastro)<>"funcionarios" and strtolower($this->cadastro)<>"fabricantes"){
			return;
		}
 		
 		if(strtolower($this->cadastro)=="clientes"){
        	$this->cadastro_conta="receber";	
        }elseif(strtolower($this->cadastro)=="fornecedores"){
        	$this->cadastro_conta="pagar";
        }else{
        	$this->cadastro_conta=null;
        	$this->entry_conta->set_text('');
        	$this->label_conta->set_text('');
        }
 		// verifica se ja existe conexao de sinal para o cadastro
 		if(is_int($this->cadastro_connect1)){
 			if($this->entry_cadastro->is_connected($this->cadastro_connect1)){
 				$this->entry_cadastro->disconnect($this->cadastro_connect1);
 			}
 		}
 		if(is_int($this->cadastro_connect2)){
 			if($this->entry_cadastro->is_connected($this->cadastro_connect2)){
 				$this->entry_cadastro->disconnect($this->cadastro_connect2);
 			}
 		}
 		
 		if($this->cadastro_conta==null) return;
 		
 		$this->cadastro_connect1=$this->entry_cadastro->connect('key_press_event', 
            array($this,entry_enter), 
            "select *  from $this->cadastro ", 
            true,
            $this->entry_cadastro, 
            $this->label_cadastro,
            $this->cadastro,
            "nome",
            "codigo"
        );
		$this->cadastro_connect2=$this->entry_cadastro->connect_simple('focus-out-event',
            array($this,'retornabusca22'), 
            $this->cadastro, 
            $this->entry_cadastro, 
            $this->label_cadastro, 
            'codigo', 
            'nome'
        );
        // filta pela conta       
        $this->cadastro_connect2=$this->entry_cadastro->connect_simple_after('focus-out-event',
            array($this,'filtra_conta')
        );
        
        if(strtolower($this->cadastro)=="funcionarios"){
        	// desliga funcionarios
        	$this->entry_funcionario->set_sensitive(false);
        	$this->entry_funcionario->set_text(false);
        	$this->label_funcionario->set_text(false);
        }else{
        	$this->entry_funcionario->set_sensitive(true);
        }
        if($limpa){
	        $this->entry_cadastro->set_text('');
	        $this->label_cadastro->set_text('');	        
	        $this->entry_conta->set_text('');
	        $this->label_conta->set_text('');
        }
        $this->entry_conta->set_sensitive(false);
 	}   
	
	function filtra_conta(){
		$this->entry_conta->set_sensitive(false);
		
		
		if(strtolower($this->cadastro)<>"clientes" and strtolower($this->cadastro)<>"fornecedores"){
			return;
		}
		$this->codigo_cadastro=$this->pegaNumero($this->entry_cadastro);
		if (empty($this->codigo_cadastro) or !$this->retornabusca2($this->cadastro, $this->entry_cadastro, $this->label_cadastro, 'codigo', 'nome')){            
            // codigo invalido ou vazio
            return;
        }

		// verifica se ja existe conexao de sinal para o cadastro
 		if(is_int($this->conta_connect1)){
 			if($this->entry_conta->is_connected($this->conta_connect1)){
 				$this->entry_conta->disconnect($this->conta_connect1);
 			}
 		}
 		if(is_int($this->conta_connect2)){
 			if($this->entry_conta->is_connected($this->conta_connect2)){
 				$this->entry_conta->disconnect($this->conta_connect2);
 			}
 		}
 		$this->conta_connect1=$this->entry_conta->connect('key_press_event', 
            array($this,entry_enter), 
            "select *  from $this->cadastro_conta where codorigem='$this->codigo_cadastro' ", 
            true,
            $this->entry_conta, 
            $this->label_conta,
            $this->cadastro_conta,
            "descr",
            "codigo"
        );
		$this->conta_connect2=$this->entry_conta->connect_simple('focus-out-event',
            array($this,retornabusca22), 
            $this->cadastro_conta, 
            $this->entry_conta, 
            $this->label_conta, 
            'codigo', 
            'descr'
        );
        $this->entry_conta->set_sensitive(true);
	}
	
	function func_novo(){
        $this->entry_codigo->set_text('');
		$this->entry_resumo->set_text('');
		$this->combo_cadastro->set_active(-1);
        $this->entry_cadastro->set_text('');
        $this->label_cadastro->set_text('');
        $this->entry_funcionario->set_text('');
        $this->label_funcionario->set_text('');
        $this->entry_conta->set_text('');
        $this->label_conta->set_text('');
        $this->entry_conta->set_sensitive(false);
        $this->entry_tipo->set_text('');
        $this->label_tipo->set_text('');
        $this->entry_data->set_text(date("d-m-Y",time()));
        $this->entry_hora->set_text(date("H:i:s"));
		$this->textBuffer_obs->set_text('');
	}

	function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
        if($alterar and empty($codigo)){
            msg('Codigo nao encontrado!');
            return;
        }
        $resumo=strtoupper($this->entry_resumo->get_text());
        if(empty($resumo)){
        	msg("Digite o resumo da ocorrencia");
        	$this->entry_resumo->grab_focus();
        	return;
        }
        $cadastro=strtolower($this->cadastro);
        
        $origem=$this->pegaNumero($this->entry_cadastro);
        if (empty($origem) or !$this->retornabusca2($cadastro, $this->entry_cadastro, $this->label_cadastro, 'codigo', 'nome')){
            msg('Preencha corretamente o campo Cadastro!');
            $this->entry_cadastro->grab_focus();
            return;
        }
        
        $funcionario=$this->pegaNumero($this->entry_funcionario);
        if (!empty($funcionario) and !$this->retornabusca2('funcionarios', $this->entry_funcionario, $this->label_funcionario, 'codigo', 'nome')){
            msg('Preencha corretamente o campo Funcionario ou deixe em branco!');
            $this->entry_funcionario->grab_focus();
            return;
        }
        if(empty($funcionario)){
			$funcionario="null";
		}
		
		$conta=$this->entry_conta->get_text();
        if (!empty($conta)){
        	if(!$this->retornabusca2($this->cadastro_conta, $this->entry_conta, $this->label_conta, 'codigo', 'descr')){
            	msg('Preencha corretamente o campo Conta ou deixe em branco!');
            	$this->entry_conta->grab_focus();
            return;        
        	}
        } 
        if(empty($conta)){
			$conta="null";
		}
		
		$tipo=$this->pegaNumero($this->entry_tipo);
        if (empty($tipo) or !$this->retornabusca2('ocorrencia_tipo', $this->entry_tipo, $this->label_tipo, 'codigo', 'descricao')){
            msg('Preencha corretamente o campo Tipo!');
            $this->entry_tipo->grab_focus();
            return;
        }
        
        $data=$this->entry_data->get_text();
        if(empty($data) or !$this->valida_data($data)){
        	msg("Data incorreta!");
            $this->entry_data->grab_focus();
            return;
        }else{
            $data=$this->corrigeNumero($data,"dataiso");
        }
        
        $hora=$this->entry_hora->get_text();
        
		$obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();	

        $obs=$con->EscapeString($obs);
        $resumo=$con->EscapeString($resumo);
        $contaPR=strtolower($this->cadastro_conta);
        
		if ($alterar){
			$sql="UPDATE ocorrencia SET resumo='$resumo', data='$data', hora='$hora',	cadastro='$cadastro', cadastro_codigo=$origem, conta='$contaPR', conta_codigo=$conta, funcionario=$funcionario, codigo_tipo=$tipo, obs='$obs' WHERE codigo=$codigo ";
            
		} else {
			$sql="INSERT INTO ocorrencia (resumo, data, hora,	cadastro, cadastro_codigo, conta,	conta_codigo, funcionario, codigo_tipo, obs) ";
			$sql.="VALUES ('$resumo', '$data', '$hora',	'$cadastro', $origem, '$contaPR', $conta, $funcionario, $tipo, '$obs') ";
		}

        if($alterar){
            if($con->Query($sql)){
                $this->status('Registro alterado com sucesso');
            }else{
                msg('Erro alterando o registro.');
            }
        }else{
            if($lastcod=$con->QueryLastCod($sql)){
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else{
                msg('Erro gravando o registro.');  
            }
        }        
        $con->Disconnect();
		$this->decideSeAtualizaClist();
	}


	function atualiza($resultado){
        $cadastro["clientes"]=0;
        $cadastro["fornecedores"]=1;
        $cadastro["fabricantes"]=2;
        $cadastro["funcionarios"]=3;
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;                
        $resultado2=$con->FetchArray($resultado);
  
  		$this->entry_codigo->set_text($resultado2["codigo"]);
		$this->entry_resumo->set_text($resultado2["resumo"]);
		
		if(!empty($resultado2["cadastro"])){
			$this->combo_cadastro->set_active($cadastro[$resultado2["cadastro"]]);	
		}else{
			$this->combo_cadastro->set_active(-1);
		}
        $this->entry_cadastro->set_text($resultado2["cadastro_codigo"]);
        $this->label_cadastro->set_text('');
        $this->retornabusca2($resultado2["cadastro"], $this->entry_cadastro, $this->label_cadastro, 'codigo', 'nome');
        
        $this->entry_funcionario->set_text($resultado2["funcionario"]);
        $this->label_funcionario->set_text('');
        $this->retornabusca2('funcionarios', $this->entry_funcionario, $this->label_funcionario, 'codigo', 'nome');
        
        $this->entry_conta->set_text($resultado2["conta_codigo"]);
        $this->label_conta->set_text('');
        $this->retornabusca2($resultado2["conta"], $this->entry_conta, $this->label_conta, 'codigo', 'descr');
        
        $this->entry_tipo->set_text($resultado2["codigo_tipo"]);
        $this->label_tipo->set_text('');
        $this->retornabusca2('ocorrencia_tipo', $this->entry_tipo, $this->label_tipo, 'codigo', 'descricao');
        
        $this->entry_data->set_text($this->corrigeNumero($resultado2["data"],"data"));
        $this->entry_hora->set_text($resultado2["hora"]);
		$this->textBuffer_obs->set_text($resultado2["obs"]);
		
		
		$this->escolha_cadastro(false);
		$this->filtra_conta();
	}
}
?>