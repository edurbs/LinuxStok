<?php
// date ("w") num dia sem.
class formapgto extends funcoes
{

    function Formapgto($tmp,$tituloWindow )
    {
        $this->xml=$this->carregaGlade("formapgto",$tituloWindow);
        
        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
        $this->entry_descricao=$this->xml->get_widget('entry_descricao');
        $this->spinbutton_variacao=$this->xml->get_widget('spinbutton_variacao');
		
		$this->entry_taxafixa=$this->xml->get_widget('entry_taxafixa');
		$this->entry_taxafixa->connect('key-press-event', array($this,'mascaraNew'),'virgula2');
        
		$this->spinbutton_parcelas=$this->xml->get_widget('spinbutton_parcelas');		
        $this->checkbutton_arredonda=$this->xml->get_widget('checkbutton_arredonda');
        $this->checkbutton_ativa=$this->xml->get_widget('checkbutton_ativa');
		$this->checkbutton_avista=$this->xml->get_widget('checkbutton_avista');
		
		$this->radiobutton_datavariavel=$this->xml->get_widget('radiobutton_datavariavel');
		//$this->radiobutton_datavariavel->connect('toggled',array($this,'toggledatavariavel'));
		
		$this->radiobutton_variacaoobrigatoria=$this->xml->get_widget('radiobutton_variacaoobrigatoria');
		$this->radiobutton_variacaoopcional=$this->xml->get_widget('radiobutton_variacaoopcional');
		
		$this->spinbutton_datafixa=$this->xml->get_widget('spinbutton_datafixa');
		$this->radiobutton_datafixa=$this->xml->get_widget('radiobutton_datafixa');
		$this->radiobutton_datafixa->connect('toggled',array($this,'toggledatafixa'));
		$this->radiobutton_datafixa->toggled();
		
		$this->combo_diadasemana=$this->xml->get_widget('combo_diadasemana');
		$this->radiobutton_diadasemana=$this->xml->get_widget('radiobutton_diadasemana');
		$this->radiobutton_diadasemana->connect('toggled',array($this,'togglediadasemana'));
		$this->radiobutton_diadasemana->toggled();		
                
		$button_novo=$this->xml->get_widget('button_novo');
		$button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao('010802',false));
		$button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao('010804',false));
		$button_primeiro=$this->xml->get_widget('button_primeiro');
		$button_ultimo=$this->xml->get_widget('button_ultimo');
		$button_proximo=$this->xml->get_widget('button_proximo');
		$button_anterior=$this->xml->get_widget('button_anterior');        
		$button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao('010803',false));
		
		$button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',false);
		$button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), formapgto, formapgto,'codigoformapgto','func_novo','atualiza');
		$button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), formapgto, formapgto, 'codigoformapgto','func_novo','atualiza');
		$button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), formapgto, formapgto,'codigoformapgto','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), formapgto, formapgto,'codigoformapgto','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_simple('clicked', array(&$this, confirma_excluir), formapgto, formapgto,'codigoformapgto','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
        
        $this->cria_clist_cadastro('formapgto', "descricao", 'codigoformapgto', &$this->entry_descricao, 'formapgto', "select * from formapgto",true, array(true, '010803'));
        
        //$this->janela->show();
    } // end of member function Formapgto

	function toggledatafixa($radio){
		if($radio->get_active()==true){
			$this->spinbutton_datafixa->set_sensitive(true);
		}else{
			$this->spinbutton_datafixa->set_sensitive(false);
		}
	}
	function togglediadasemana($radio){
		if($radio->get_active()==true){
			$this->combo_diadasemana->set_sensitive(true);
		}else{
			$this->combo_diadasemana->set_sensitive(false);
		}		
	}
	
    function func_novo(){
        $this->entry_codigo->set_text('');
        $this->entry_descricao->set_text('');
		$this->entry_taxafixa->set_text('');
        $this->spinbutton_variacao->set_text(0.00);
        $this->spinbutton_parcelas->set_text(1);
		$this->spinbutton_datafixa->set_text(0);
        $this->checkbutton_arredonda->set_active(false);
        $this->checkbutton_ativa->set_active(true);
		$this->checkbutton_avista->set_active(true);
		$this->radiobutton_datavariavel->set_active(true);
    }
    
    function func_gravar($alterar){
        $this->codigo=$this->entry_codigo->get_text();
        if(empty($this->codigo) and $alterar){
            msg('Codigo nao informado!');
            return;
        }
        $this->descricao=strtoupper($this->entry_descricao->get_text());        
        if(empty($this->descricao)){
            msg('Preencha o campo descricao!');
            return;
        }elseif($this->ja_cadastrado("formapgto",'descricao',"$this->descricao") and !$alterar){
            msg('Descricao ja cadastrada.');
            return;
        }
		$this->taxafixa=$this->pegaNumero($this->entry_taxafixa);
		
        $this->spinbutton_variacao->update();
        $this->variacao=round($this->spinbutton_variacao->get_value(), 2);
        $this->parcelas=$this->spinbutton_parcelas->get_value_as_int();
		$this->datafixa=$this->spinbutton_datafixa->get_value_as_int();
		$this->diadasemana=$this->combo_diadasemana->get_active();
		
		if($this->radiobutton_variacaoobrigatoria->get_active()){
            $this->variacaoobrigatoria='1'; // true
        }else{
            $this->variacaoobrigatoria='0'; // false
        }
        
		if($this->radiobutton_diadasemana->get_active()){
            $this->chkdiadasemana='1'; // true
        }else{
            $this->chkdiadasemana='0'; // false
        }
		if($this->radiobutton_datavariavel->get_active()){
            $this->chkdatavariavel='1'; // true
        }else{
            $this->chkdatavariavel='0'; // false
        }
		if($this->radiobutton_datafixa->get_active()){
            $this->chkdatafixa='1'; // true
        }else{
            $this->chkdatafixa='0'; // false
        }
        if($this->checkbutton_arredonda->get_active()){
            $this->arredonda='1'; // true
        }else{
            $this->arredonda='0'; // false
        }
        if($this->checkbutton_ativa->get_active()){
            $this->ativa='1'; // true
        }else{
            $this->ativa='0'; // false
        }
		if($this->checkbutton_avista->get_active()){
            $this->avista='1'; // true
        }else{
            $this->avista='0'; // false
        }
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();	
        if ($alterar){
            $sql="SELECT descricao FROM formapgto WHERE codigoformapgto='$this->codigo'";            
            $resultado=$con->Query($sql);
                
            if($con->NumRows($resultado)==0){
                msg("Codigo nao encontrado!");                
            }else{
                $sql="UPDATE formapgto SET descricao='$this->descricao', variacao='$this->variacao', taxafixa='$this->taxafixa', parcelas='$this->parcelas', arredonda='$this->arredonda', ativa='$this->ativa', avista='$this->avista', chkdatavariavel='$this->chkdatavariavel', chkdatafixa='$this->chkdatafixa', datafixa='$this->datafixa', chkdiadasemana='$this->chkdiadasemana', 	diadasemana='$this->diadasemana', variacaoobrigatoria='$this->variacaoobrigatoria' WHERE codigoformapgto='$this->codigo'";
                if(!$con->Query($sql)){
                    msg("Erro ao executar SQL!");
                }else{
                    $this->status('Registro alterado com sucesso');
                }
            }
        } else {
            $sql="INSERT INTO formapgto (descricao, variacao, taxafixa, parcelas, arredonda, ativa, avista, chkdatavariavel, chkdatafixa, datafixa, chkdiadasemana, diadasemana, variacaoobrigatoria)";
            $sql.="VALUES ('$this->descricao', '$this->variacao', '$this->taxafixa', '$this->parcelas', '$this->arredonda', '$this->ativa', '$this->avista', '$this->chkdatavariavel', '$this->chkdatafixa', '$this->datafixa', '$this->chkdiadasemana', '$this->diadasemana', '$this->variacaoobrigatoria')";
            if ($lastcod=$con->QueryLastCod($sql)){            
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao executar SQL");
            };
        }        
        $con->Disconnect();

        $this->decideSeAtualizaClist();
    }
    
    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);    
        $this->entry_codigo->set_text($resultado2["codigoformapgto"]);
        $this->entry_descricao->set_text($resultado2["descricao"]);
        $this->spinbutton_variacao->set_text($resultado2["variacao"]);
		$this->entry_taxafixa->set_text($this->mascara2($resultado2["taxafixa"],'moeda'));
        $this->spinbutton_parcelas->set_text($resultado2["parcelas"]);		
        $this->checkbutton_arredonda->set_active($resultado2["arredonda"]);
        $this->checkbutton_ativa->set_active($resultado2["ativa"]);
		$this->checkbutton_avista->set_active($resultado2["avista"]);
		$this->radiobutton_datavariavel->set_active($resultado2["chkdatavariavel"]);
		if($resultado2["variacaoobrigatoria"]=="1"){
			$this->radiobutton_variacaoobrigatoria->set_active(true);
		}else{
			$this->radiobutton_variacaoopcional->set_active(true);
		}
		$this->radiobutton_datafixa->set_active($resultado2["chkdatafixa"]);
		$this->spinbutton_datafixa->set_text($resultado2["datafixa"]);
		$this->radiobutton_diadasemana->set_active($resultado2["chkdiadasemana"]);
		if(!empty($resultado2["diadasemana"])) $this->combo_diadasemana->set_active($resultado2["diadasemana"]);
		$this->spinbutton_variacao->update();
		$this->spinbutton_parcelas->update();
		$this->spinbutton_datafixa->update();
    }
}
?>
