<?php

class veiculos extends funcoes{
	function veiculos(){
        $this->xml=$this->carregaGlade("veiculos");
        
        
	    $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());

        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
		$this->entry_descricao=$this->xml->get_widget('entry_descricao');
        $this->entry_renavam=$this->xml->get_widget('entry_renavam');
        $this->entry_placa=$this->xml->get_widget('entry_placa');
        $this->entry_combustivel=$this->xml->get_widget('entry_combustivel');
        $this->entry_marca=$this->xml->get_widget('entry_marca');
        $this->entry_modelo=$this->xml->get_widget('entry_modelo');
        $this->entry_anofab=$this->xml->get_widget('entry_anofab');        
        $this->entry_anomod=$this->xml->get_widget('entry_anomod');
        $this->entry_kilometragem=$this->xml->get_widget('entry_kilometragem');
        $this->entry_tara=$this->xml->get_widget('entry_tara');
        $this->entry_liquido=$this->xml->get_widget('entry_liquido');
		$this->entry_volume=$this->xml->get_widget('entry_volume');

        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);
		
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('011002',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('011003',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('011004',false));

		$button_novo->connect_simple('clicked', confirma, array($this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',null);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array(&$this,cadastro_primeiro), 'veiculos', 'veiculos','codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this,cadastro_ultimo), 'veiculos', 'veiculos','codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this,cadastro_proximo), 'veiculos', 'veiculos','codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this,cadastro_anterior), 'veiculos', 'veiculos','codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this,confirma_excluir), 'veiculos', 'veiculos','codigo','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar este registro?',true,array(true,'011003'));		

		/*
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
		
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array(&$this,cadastro_primeiro), 'veiculos', 'veiculos','codigo','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this,cadastro_ultimo), 'veiculos', 'veiculos','codigo','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this,cadastro_proximo), 'veiculos', 'veiculos','codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this,cadastro_anterior), 'veiculos', 'veiculos','codigo','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this,confirma_excluir), 'veiculos', 'veiculos','codigo','func_novo','atualiza',$this->entry_codigo, $this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array($this, 'func_gravar'),'Deseja alterar este registro?',true);
		*/
				
       	$this->func_novo(true);
        
        $this->cria_clist_cadastro("veiculos", "descricao", "codigo", $this->entry_descricao, "veiculos", "select * from veiculos");
        
        //$this->janela->show();
	}
    

	function func_novo(){
        $this->entry_codigo->set_text('');
		$this->entry_descricao->set_text('');
        $this->entry_renavam->set_text('');
        $this->entry_placa->set_text('');
        $this->entry_combustivel->set_text('');
        $this->entry_marca->set_text('');
        $this->entry_modelo->set_text('');
        $this->entry_anofab->set_text('');        
        $this->entry_anomod->set_text('');
        $this->entry_kilometragem->set_text('');
        $this->entry_tara->set_text('');
        $this->entry_liquido->set_text('');
		$this->entry_volume->set_text('');
		$this->textBuffer_obs->set_text('');
	}

	function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
        if($alterar and empty($codigo)){
            msg('Codigo nao encontrado!');
            return;
        }
        $renavam=$this->entry_renavam->get_text();
		if(!$alterar and !empty($renavam) and $this->ja_cadastrado('veiculos','renavam',$renavam)){
            msg('RENAVAM ja cadastrado!');
            return;
        }
        $placa=$this->entry_placa->get_text();
        if(!$alterar and !empty($placa) and$this->ja_cadastrado('veiculos','placa',$placa)){
            msg('Placa ja cadastrada!');
            return;
        }
		$descricao=$this->entry_descricao->get_text();
		if(empty($descricao)){
			msg("Descricao deve ser informada");
			$this->entry_descricao->grab_focus();
			return;
		}
        $combustivel=$this->entry_combustivel->get_text();
        $marca=$this->entry_marca->get_text();
        $modelo=$this->entry_modelo->get_text();
        $anofab=$this->entry_anofab->get_text();
        $anomod=$this->entry_anomod->get_text();
        $kilometragem=$this->entry_kilometragem->get_text();
        $tara=$this->pegaNumero($this->entry_tara);
        $liquido=$this->pegaNumero($this->entry_liquido);
		$volume=$this->pegaNumero($this->entry_volume);

		$obs=$this->textBuffer_obs->get_text(
            $this->textBuffer_obs->get_start_iter(),
            $this->textBuffer_obs->get_end_iter()
        );
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();	

        
		if ($alterar){
			$sql="UPDATE veiculos SET renavam='$renavam',combustivel='$combustivel', marca='$marca', modelo='$modelo', anofab='$anofab', anomod='$anomod', obs='$obs', kilometragem='$kilometragem', placa='$placa', tara='$tara', liquido='$liquido', volume='$volume', descricao='$descricao' WHERE codigo='$codigo';";
            
		} else {
			$sql="INSERT INTO veiculos (renavam, combustivel, marca, modelo, anofab, anomod, obs, kilometragem, placa, tara, liquido, volume, descricao) ";
			$sql.="VALUES ('$renavam','$combustivel', '$marca', '$modelo', '$anofab', '$anomod', '$obs', '$kilometragem', '$placa', '$tara', '$liquido', '$volume', '$descricao')";
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
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;                
        $resultado2=$con->FetchArray($resultado);
  
        $this->entry_codigo->set_text($resultado2["codigo"]);
		$this->entry_descricao->set_text($resultado2["descricao"]);
        $this->entry_renavam->set_text($resultado2["renavam"]);
        $this->entry_placa->set_text($resultado2["placa"]);
        $this->entry_combustivel->set_text($resultado2["combustivel"]);
        $this->entry_marca->set_text($resultado2["marca"]);
        $this->entry_modelo->set_text($resultado2["modelo"]);
        $this->entry_anofab->set_text($resultado2["anofab"]);        
        $this->entry_anomod->set_text($resultado2["anomod"]);
        $this->entry_kilometragem->set_text($resultado2["kilometragem"]);
        $this->entry_tara->set_text($resultado2["tara"]);
        $this->entry_liquido->set_text($resultado2["liquido"]);
		$this->entry_volume->set_text($resultado2["volume"]);
		$this->textBuffer_obs->set_text($resultado2["obs"]);

	}
}
?>