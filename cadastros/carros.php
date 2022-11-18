<?

class carros extends funcoes{
	function carros(){
        $this->xml=$this->carregaGlade("carros");
        
		$this->button_relatorio=$this->xml->get_widget("button_relatorio");
        $this->button_relatorio->connect_object('clicked', array(&$this, chamarelatorio),'carros', null,null);

        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->fontcolor=new GdkColor(0,0,0);
		$this->backcolor=new GdkColor(65535,65535,65535);
		$this->font=gdk::font_load ("-*-helvetica-r-r-*-*-*-120-*-*-*-*-*-*");        

        $this->pixmap_foto1=$this->xml->get_widget('pixmap_foto1');
        $button_busca_foto1=$this->xml->get_widget('button_busca_foto1');
        $button_busca_foto1->connect_object('clicked', array(&$this,'buscar_foto_carros'),'1',&$this->pixmap_foto1); 
        $button_limpar_foto1=$this->xml->get_widget('button_limpar_foto1');
        $button_limpar_foto1->connect_object('clicked', array(&$this,'limpar_foto_carros'),'1'); 
        $button_ver_foto1=$this->xml->get_widget('button_ver_foto1');
        $button_ver_foto1->connect_object('clicked', array(&$this,'ver_foto_carros'),'1'); 

        $this->pixmap_foto2=$this->xml->get_widget('pixmap_foto2');
        $button_busca_foto2=$this->xml->get_widget('button_busca_foto2');
        $button_busca_foto2->connect_object('clicked', array(&$this,'buscar_foto_carros'),'2',&$this->pixmap_foto2); 
        $button_limpar_foto2=$this->xml->get_widget('button_limpar_foto2');
        $button_limpar_foto2->connect_object('clicked', array(&$this,'limpar_foto_carros'),'2'); 
        $button_ver_foto2=$this->xml->get_widget('button_ver_foto2');
        $button_ver_foto2->connect_object('clicked', array(&$this,'ver_foto_carros'),'2'); 

        $this->pixmap_foto3=$this->xml->get_widget('pixmap_foto3');
        $button_busca_foto3=$this->xml->get_widget('button_busca_foto3');
        $button_busca_foto3->connect_object('clicked', array(&$this,'buscar_foto_carros'),'3',&$this->pixmap_foto3); 
        $button_limpar_foto3=$this->xml->get_widget('button_limpar_foto3');
        $button_limpar_foto3->connect_object('clicked', array(&$this,'limpar_foto_carros'),'3'); 
        $button_ver_foto3=$this->xml->get_widget('button_ver_foto3');
        $button_ver_foto3->connect_object('clicked', array(&$this,'ver_foto_carros'),'3'); 

        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
        $this->combo_estado=$this->xml->get_widget('combo_estado');
        $this->entry_estado=$this->xml->get_widget('entry_estado');
        $this->entry_documento=$this->xml->get_widget('entry_documento');
        $this->entry_dtcadastro=$this->xml->get_widget('entry_dtcadastro');
        //$this->entry_dtcadastro->connect('focus-in-event', array(&$this,'calendario'));
        $this->entry_dtcadastro->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        
        $this->entry_via=$this->xml->get_widget('entry_via');
        $this->entry_renavam=$this->xml->get_widget('entry_renavam');
        $this->entry_rtb=$this->xml->get_widget('entry_rtb');
        $this->combo_exercicio=$this->xml->get_widget('combo_exercicio');
        $this->entry_exercicio=$this->xml->get_widget('entry_exercicio');
        
        $this->entry_proprietarioatual=$this->xml->get_widget('entry_proprietarioatual');
        $this->label_proprietarioatual=$this->xml->get_widget('label_proprietarioatual');
        $this->entry_proprietarioatual->connect_object('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo,nome from clientes', 
            true,
            &$this->entry_proprietarioatual, 
            &$this->label_proprietarioatual,
            "clientes",
            "nome",
            "codigo"
        );
		$this->entry_proprietarioatual->connect_object('focus-out-event',
            array(&$this,retornabusca2), 
            'clientes', 
            &$this->entry_proprietarioatual, 
            &$this->label_proprietarioatual, 
            'codigo', 
            'nome', 
            'carros'
        );
        
        $this->entry_proprietarioanterior=$this->xml->get_widget('entry_proprietarioanterior');
        $this->label_proprietarioanterior=$this->xml->get_widget('label_proprietarioanterior');
        $this->entry_proprietarioanterior->connect_object('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo,nome from clientes', 
            true,
            &$this->entry_proprietarioanterior, 
            &$this->label_proprietarioanterior,
            "clientes",
            "nome",
            "codigo"
        );
		$this->entry_proprietarioanterior->connect_object('focus-out-event',
            array(&$this,retornabusca2), 
            'clientes', 
            &$this->entry_proprietarioanterior, 
            &$this->label_proprietarioanterior, 
            'codigo', 
            'nome', 
            'carros'
        );
        
        $this->entry_placaatual=$this->xml->get_widget('entry_placaatual');
        $this->entry_placaanterior=$this->xml->get_widget('entry_placaanterior');
        $this->entry_chassi=$this->xml->get_widget('entry_chassi');
        $this->entry_especie=$this->xml->get_widget('entry_especie');
        $this->combo_combustivel=$this->xml->get_widget('combo_combustivel');
        $this->entry_combustivel=$this->xml->get_widget('entry_combustivel');
        $this->entry_marca=$this->xml->get_widget('entry_marca');
        $this->entry_modelo=$this->xml->get_widget('entry_modelo');
        $this->combo_anofab=$this->xml->get_widget('combo_anofab');        
        $this->combo_anomod=$this->xml->get_widget('combo_anomod');
            $j=1;
            for($i=2010;$i>1900;$i--){
                $anos[$j]=$i;
                $j++;
            }
            $this->combo_anofab->set_popdown_strings($anos);
            $this->combo_anomod->set_popdown_strings($anos);
            $this->combo_exercicio->set_popdown_strings($anos);
        $this->entry_anofab=$this->xml->get_widget('entry_anofab');        
        $this->entry_anomod=$this->xml->get_widget('entry_anomod');
        $this->entry_capacidade=$this->xml->get_widget('entry_capacidade');
        $this->entry_potencia=$this->xml->get_widget('entry_potencia');
        $this->entry_cilindrada=$this->xml->get_widget('entry_cilindrada');
        $this->entry_categoria=$this->xml->get_widget('entry_categoria');
        $this->entry_cor=$this->xml->get_widget('entry_cor');
        $this->entry_cotaunica=$this->xml->get_widget('entry_cotaunica');
        $this->entry_cotaunica->connect('key-press-event', array(&$this, mascara),'moeda',null,null,null);       
        $this->entry_cotaunica->connect_object('focus-out-event', array(&$this, corrigeNumero),'moeda', 'carros', &$this->entry_cotaunica);
        $this->entry_venccotaunica=$this->xml->get_widget('entry_venccotaunica');
        $this->entry_venccotaunica->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        $this->entry_faixaipva=$this->xml->get_widget('entry_faixaipva');
        $this->entry_parcelamento=$this->xml->get_widget('entry_parcelamento');
        $this->entry_venc1=$this->xml->get_widget('entry_venc1');
        $this->entry_venc1->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        $this->entry_venc2=$this->xml->get_widget('entry_venc2');
        $this->entry_venc2->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        $this->entry_venc3=$this->xml->get_widget('entry_venc3');
        $this->entry_venc3->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        $this->entry_premioliquido=$this->xml->get_widget('entry_premioliquido');
        $this->entry_premioliquido->connect('key-press-event', array(&$this, mascara),'moeda',null,null,null);       
        $this->entry_premioliquido->connect_object('focus-out-event', array(&$this, corrigeNumero),'moeda', 'carros', &$this->entry_premioliquido);
        $this->entry_isof=$this->xml->get_widget('entry_isof');
        $this->entry_premiototal=$this->xml->get_widget('entry_premiototal');
        $this->entry_premiototal->connect('key-press-event', array(&$this, mascara),'moeda',null,null,null);       
        $this->entry_premiototal->connect_object('focus-out-event', array(&$this, corrigeNumero),'moeda', 'carros', &$this->entry_premiototal);
        $this->entry_datapgto=$this->xml->get_widget('entry_datapgto');
        $this->entry_datapgto->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        $this->entry_local=$this->xml->get_widget('entry_local');
        $this->entry_data=$this->xml->get_widget('entry_data');
        $this->entry_data->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
        $this->entry_preco=$this->xml->get_widget('entry_preco');
        $this->entry_preco->connect('key-press-event', array(&$this, mascara),'moeda',null,null,null);       
        $this->entry_kilometragem=$this->xml->get_widget('entry_kilometragem');
        $this->text_obs=$this->xml->get_widget('text_obs');

        $this->button_atualiza_clist=$this->xml->get_widget("button_atualiza_clist");

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

		$button_novo->connect_object('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?',null);
		$button_gravar->connect_object('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
		$button_primeiro->connect_object('clicked', array(&$this,cadastro_primeiro), 'carros', 'carros','codcarro','func_novo','atualiza');
		$button_ultimo->connect_object('clicked', array(&$this,cadastro_ultimo), 'carros', 'carros','codcarro','func_novo','atualiza');
		$button_proximo->connect_object('clicked', array(&$this,cadastro_proximo), 'carros', 'carros','codcarro','func_novo','atualiza',&$this->entry_codigo);
		$button_anterior->connect_object('clicked', array(&$this,cadastro_anterior), 'carros', 'carros','codcarro','func_novo','atualiza',&$this->entry_codigo);
		$button_excluir->connect_object('clicked', array(&$this,confirma_excluir), 'carros', 'carros','codcarro','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
		$button_alterar->connect_object('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar este registro?',true);		
       
        $this->cria_clist_cadastro("carros", "placaatual", "codcarro", &$this->combo_estado, "carros", 
        "select codcarro, estado, documento, dtcadastro, via, renavam, rtb, exercicio, proprietarioatual, proprietarioanterior, chassi, especie, combustivel, marca, modelo, anofab, anomod, capacidade, potencia, cilindrada, categoria, cor, cotaunica, venccotaunica, faixaipva, parcelamento, venc1, venc2, venc3, premioliquido, isof, premiototal, datapgto, local, kilometragem, preco, data, obs from carros as carros");
        $this->func_novo(true);
        $this->janela->show();
	}
    
    function ver_foto_carros($num){
        $codigo=$this->entry_codigo->get_text();
        if(empty($codigo)){
            msg('Codigo em branco!');
            return;
        }
        $sql="select foto$num from carros where codcarro='$codigo'";
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $resultado=$con->Query($sql);        
        $resultado2=$con->FetchArray($resultado);        
        $this->ver_foto(@gzuncompress($resultado2["foto$num"]));
        $con->Disconnect();
    }    
    function buscar_foto_carros($num,$widget,$resize=null){
        eval('$this->foto'.$num.'=$this->buscar_foto(&$widget,$resize);');
    }    
    function limpar_foto_carros($num){
        eval('$this->fotoCarrosLimpa'.$num.'=true;');
        eval('$this->foto'.$num.'="";');
        eval('$this->limpar_foto(&$this->pixmap_foto'.$num.');');
    }

	function func_novo($first){
        if(!$first){
            $this->limpar_foto_carros('1');
            $this->limpar_foto_carros('2');
            $this->limpar_foto_carros('3');
        }
        $this->entry_codigo->set_text('');        
        $this->entry_estado->set_text(retorna_CONFIG("Estado"));
        $this->entry_documento->set_text('');
        $this->entry_dtcadastro->set_text('');
        $this->entry_via->set_text('');
        $this->entry_renavam->set_text('');
        $this->entry_rtb->set_text('');        
        $this->entry_exercicio->set_text($this->anodehoje);
        $this->entry_proprietarioatual->set_text('');
        $this->label_proprietarioatual->set_text('');
        $this->entry_proprietarioanterior->set_text('');
        $this->label_proprietarioanterior->set_text('');
        $this->entry_placaatual->set_text('');
        $this->entry_placaanterior->set_text('');
        $this->entry_chassi->set_text('');
        $this->entry_especie->set_text('');
        $this->entry_combustivel->set_text('');
        $this->entry_marca->set_text('');
        $this->entry_modelo->set_text('');
        $this->entry_anofab->set_text($this->anodehoje);
        $this->entry_anomod->set_text($this->anodehoje);
        $this->entry_capacidade->set_text('');
        $this->entry_potencia->set_text('');
        $this->entry_cilindrada->set_text('');
        $this->entry_categoria->set_text('');
        $this->entry_cor->set_text('');
        $this->entry_cotaunica->set_text('');
        $this->entry_venccotaunica->set_text('');
        $this->entry_faixaipva->set_text('');
        $this->entry_parcelamento->set_text('');
        $this->entry_venc1->set_text('');
        $this->entry_venc2->set_text('');
        $this->entry_venc3->set_text('');
        $this->entry_premioliquido->set_text('');
        $this->entry_isof->set_text('');
        $this->entry_premiototal->set_text('');
        $this->entry_datapgto->set_text('');
        $this->entry_local->set_text('');
        $this->entry_data->set_text('');
        $this->entry_kilometragem->set_text('');
        $this->entry_preco->set_text('');
        $this->text_obs->delete_text(0,-1);        
	}

	function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
        if($alterar and empty($codigo)){
            msg('Codigo nao encontrado!');
            return;
        }
        $estado=$this->entry_estado->get_text();
        $documento=$this->entry_documento->get_text();
        $dtcadastro=$this->entry_dtcadastro->get_text();
        if($dtcadastro=="" or $dtcadastro=="00-00-0000"){
            $dtcadastro="0001-01-01";
        }else{
            if($this->valida_data($dtcadastro)){
                $dtcadastro=$this->corrigeNumero($dtcadastro,"dataiso");                
            }else{
                msg("Data de cadastro incorreta!");
                $this->janela->set_focus($this->entry_dtcadastro);
                return;
            }
        }
        $via=$this->entry_via->get_text();
        $renavam=$this->entry_renavam->get_text();
        if(empty($renavam)){
            msg('Preencha o campo RENAVAM!');
            return;
        }elseif(!$alterar and $this->ja_cadastrado('carros','renavam',$renavam)){
            msg('RENAVAM ja cadastrado!');
            return;
        }
        $rtb=$this->entry_rtb->get_text();        
        $exercicio=$this->entry_exercicio->get_text();
        $proprietarioatual=$this->entry_proprietarioatual->get_text();        
        if(!$this->retornabusca2(null,'clientes', &$this->entry_proprietarioatual, &$this->label_proprietarioatual, 'codigo', 'nome', 'carros')){
            msg('Codigo do Proprietario Atual incorreto!');
            return;
        }
        $proprietarioanterior=$this->entry_proprietarioanterior->get_text();        
        if(!$this->retornabusca2(null,'clientes', &$this->entry_proprietarioanterior, &$this->label_proprietarioanterior, 'codigo', 'nome', 'carros')){
            msg('Codigo do Proprietario Anterior incorreto!');
            return;
        }

        $placaatual=$this->entry_placaatual->get_text();
        $placaanterior=$this->entry_placaanterior->get_text();
        $chassi=$this->entry_chassi->get_text();
        $especie=$this->entry_especie->get_text();
        $combustivel=$this->entry_combustivel->get_text();
        $marca=$this->entry_marca->get_text();
        $modelo=$this->entry_modelo->get_text();
        $anofab=$this->entry_anofab->get_text();
        $anomod=$this->entry_anomod->get_text();
        $capacidade=$this->entry_capacidade->get_text();
        $potencia=$this->entry_potencia->get_text();
        $cilindrada=$this->entry_cilindrada->get_text();
        $categoria=$this->entry_categoria->get_text();
        $cor=$this->entry_cor->get_text();
        $cotaunica=$this->DeixaSoNumeroDecimal($this->entry_cotaunica->get_text(),2);
        $venccotaunica=$this->entry_venccotaunica->get_text();
        if($venccotaunica=="" or $venccotaunica=="00-00-0000"){
            $venccotaunica="0001-01-01";
        }else{
            if($this->valida_data($venccotaunica)){
                $venccotaunica=$this->corrigeNumero($venccotaunica,"dataiso");
            }else{
                msg("Data de vencimento da cota unica incorreta!");
                $this->janela->set_focus($this->entry_venccotaunica);
                return;
            }
        }
        $faixaipva=$this->entry_faixaipva->get_text();
        $parcelamento=$this->entry_parcelamento->get_text();
        $venc1=$this->entry_venc1->get_text();
        if($venc1=="" or $venc1=="00-00-0000"){
            $venc1="0001-01-01";
        }else{
            if($this->valida_data($venc1)){
                $venc1=$this->corrigeNumero($venc1,"dataiso");
            }else{
                msg("Data de vencimento da 1° cota incorreta");
                $this->janela->set_focus($this->entry_venc1);
                return;
            }
        }
        $venc2=$this->entry_venc2->get_text();
        if($venc2=="" or $venc2=="00-00-0000"){
            $venc2="0001-01-01";
        }else{
            if($this->valida_data($venc2)){
                $venc2=$this->corrigeNumero($venc2,"dataiso");
            }else{
                msg("Data de vencimento da 2° cota incorreta");
                $this->janela->set_focus($this->entry_venc2);
                return;
            }
        }
        $venc3=$this->entry_venc3->get_text();
        if($venc3=="" or $venc3=="00-00-0000"){
            $venc3="0001-01-01";
        }else{
            if($this->valida_data($venc3)){
                $venc3=$this->corrigeNumero($venc3,"dataiso");
            }else{
                msg("Data de vencimento da 3° cota incorreta");
                $this->janela->set_focus($this->entry_venc3);
                return;
            }
        }
        $premioliquido=$this->DeixaSoNumeroDecimal($this->entry_premioliquido->get_text(),2);
        $isof=$this->entry_isof->get_text();
        $premiototal=$this->DeixaSoNumeroDecimal($this->entry_premiototal->get_text(),2);
        $datapgto=$this->entry_datapgto->get_text();
        if($datapgto=="" or $datapgto=="00-00-0000"){
            $datapgto="0001-01-01";
        }else{
            if($this->valida_data($datapgto)){
                $datapgto=$this->corrigeNumero($datapgto,"dataiso");
            }else{
                msg("Data de pagamento incorreta");
                $this->janela->set_focus($this->entry_datapgto);
                return;
            }
        }
        $local=$this->entry_local->get_text();
        $data=$this->entry_data->get_text();
        if($data=="" or $data=="00-00-0000"){
            $data="0001-01-01";
        }else{
            if($this->valida_data($data)){
                $data=$this->corrigeNumero($data,"dataiso");
            }else{
                msg("Data de pagamento incorreta");
                $this->janela->set_focus($this->entry_data);
                return;
            }
        }
        $preco=$this->DeixaSoNumeroDecimal($this->entry_preco->get_text(),2);
        $kilometragem=$this->entry_kilometragem->get_text();
		$obs=strtoupper($this->text_obs->get_chars(0,-1));
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	

        if(!empty($this->foto1)){
            $this->foto1=$con->EscapeString(gzcompress(var_export($this->foto1,TRUE),9));            
        }
        if(!empty($this->foto2)){
            $this->foto2=$con->EscapeString(gzcompress(var_export($this->foto2,TRUE),9));
        }
        if(!empty($this->foto3)){
            $this->foto3=$con->EscapeString(gzcompress(var_export($this->foto3,TRUE),9));
        }
        
		if ($alterar){
			$sql="UPDATE carros SET estado='$estado', documento='$documento', dtcadastro='$dtcadastro', via='$via', renavam='$renavam', rtb='$rtb', exercicio='$exercicio', proprietarioatual='$proprietarioatual', proprietarioanterior='$proprietarioanterior', chassi='$chassi', especie='$especie', combustivel='$combustivel', marca='$marca', modelo='$modelo', anofab='$anofab', anomod='$anomod', capacidade='$capacidade', potencia='$potencia', cilindrada='$cilindrada', categoria='$categoria', cor='$cor', cotaunica='$cotaunica', venccotaunica='$venccotaunica', faixaipva='$faixaipva', parcelamento='$parcelamento', venc1='$venc1', venc2='$venc2', venc3='$venc3', premioliquido='$premioliquido', isof='$isof', premiototal='$premiototal', datapgto='$datapgto', local='$local', data='$data', obs='$obs', kilometragem='$kilometragem', preco='$preco'";
            
            if(!empty($this->foto1) or $this->fotoCarrosLimpa1){
                $sql.=", foto1='$this->foto1'";
            }
            if(!empty($this->foto2) or $this->fotoCarrosLimpa2){
                $sql.=", foto2='$this->foto2'";
            }
            if(!empty($this->foto3) or $this->fotoCarrosLimpa3){
                $sql.=", foto3='$this->foto3'";
            }
            $sql.=" WHERE codcarro='$codigo';";
            
		} else {
			$sql="INSERT INTO carros (estado, documento, dtcadastro, via, renavam, rtb, exercicio, proprietarioatual, proprietarioanterior, chassi, especie, combustivel, marca, modelo, anofab, anomod, capacidade, potencia, cilindrada, categoria, cor, cotaunica, venccotaunica, faixaipva, parcelamento, venc1, venc2, venc3, premioliquido, isof, premiototal, datapgto, local, data, obs, kilometragem, preco, foto1, foto2, foto3) ";
			$sql.="VALUES ('$estado', '$documento', '$dtcadastro', '$via', '$renavam', '$rtb', '$exercicio', '$proprietarioatual', '$proprietarioanterior', '$chassi', '$especie', '$combustivel', '$marca', '$modelo', '$anofab', '$anomod', '$capacidade', '$potencia', '$cilindrada', '$categoria', '$cor', '$cotaunica', '$venccotaunica', '$faixaipva', '$parcelamento', '$venc1', '$venc2', '$venc3', '$premioliquido', '$isof', '$premiototal', '$datapgto', '$local', '$data', '$obs', '$kilometragem', '$preco', '$this->foto1', '$this->foto2', '$this->foto3')";
		}

        if($alterar){
            if($con->Query($sql,null,null,true)){
                $this->status('Registro alterado com sucesso');
            }else{
                msg('Erro alterando o registro.');
            }
        }else{
            if($lastcod=$con->QueryLastCod($sql,null,null,true)){
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else{
                msg('Erro gravando o registro.');  
            }
        }        
        $con->Disconnect();
        $this->button_atualiza_clist->clicked();
	}


	function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;        
        $resultado2=$con->FetchArray($resultado);    
		$this->entry_codigo->set_text($resultado2["codcarro"]);
        $this->entry_estado->set_text($resultado2["estado"]);
        $this->entry_documento->set_text($resultado2["documento"]);
        $this->entry_dtcadastro->set_text($this->corrigeNumero($resultado2["dtcadastro"],"data"));
        $this->entry_via->set_text($resultado2["via"]);
        $this->entry_renavam->set_text($resultado2["renavam"]);
        $this->entry_rtb->set_text($resultado2["rtb"]);        
        $this->entry_exercicio->set_text($resultado2["exercicio"]);
        $this->entry_proprietarioatual->set_text($resultado2["proprietarioatual"]);
        $this->retornabusca2(null,'clientes', &$this->entry_proprietarioatual, &$this->label_proprietarioatual, 'codigo', 'nome', 'carros');        
        $this->entry_proprietarioanterior->set_text($resultado2["proprietarioanterior"]);
        $this->retornabusca2(null,'clientes', &$this->entry_proprietarioanterior, &$this->label_proprietarioanterior, 'codigo', 'nome', 'carros');
        $this->entry_placaatual->set_text($resultado2["placaatual"]);
        $this->entry_placaanterior->set_text($resultado2["placaanterior"]);
        $this->entry_chassi->set_text($resultado2["chassi"]);
        $this->entry_especie->set_text($resultado2["especie"]);
        $this->entry_combustivel->set_text($resultado2["combustivel"]);
        $this->entry_marca->set_text($resultado2["marca"]);
        $this->entry_modelo->set_text($resultado2["modelo"]);
        $this->entry_anofab->set_text($resultado2["anofab"]);
        $this->entry_anomod->set_text($resultado2["anomod"]);
        $this->entry_capacidade->set_text($resultado2["capacidade"]);
        $this->entry_potencia->set_text($resultado2["potencia"]);
        $this->entry_cilindrada->set_text($resultado2["cilindrada"]);
        $this->entry_categoria->set_text($resultado2["categoria"]);
        $this->entry_cor->set_text($resultado2["cor"]);
        $this->entry_cotaunica->set_text($this->mascara2($resultado2["cotaunica"],'moeda'));
        $this->entry_venccotaunica->set_text($this->corrigeNumero($resultado2["venccotaunica"],"data"));
        $this->entry_faixaipva->set_text($resultado2["faixaipva"]);
        $this->entry_parcelamento->set_text($resultado2["parcelamento"]);
        $this->entry_venc1->set_text($this->corrigeNumero($resultado2["venc1"],"data"));
        $this->entry_venc2->set_text($this->corrigeNumero($resultado2["venc2"],"data"));
        $this->entry_venc3->set_text($this->corrigeNumero($resultado2["venc3"],"data"));
        $this->entry_premioliquido->set_text($this->mascara2($resultado2["premioliquido"],'moeda'));
        $this->entry_isof->set_text($resultado2["isof"]);
        $this->entry_premiototal->set_text($this->mascara2($resultado2["premiototal"],'moeda'));
        $this->entry_datapgto->set_text($this->corrigeNumero($resultado2["datapgto"],"data"));
        $this->entry_local->set_text($resultado2["local"]);
        $this->entry_data->set_text($this->corrigeNumero($resultado2["data"],"data"));
        $this->entry_kilometragem->set_text($resultado2["kilometragem"]);
        $this->entry_preco->set_text($this->mascara2($resultado2["preco"],'moeda'));
        $this->text_obs->delete_text(0,-1);
		$this->text_obs->insert($this->font, $this->fontcolor, $this->backcolor ,$resultado2["obs"]);
        
        // descompacta a foto e bota a foto na tela
        $this->limpar_foto_carros('1');
        $this->mostra_foto(&$this->pixmap_foto1,null,false,@gzuncompress($resultado2["foto1"]));
        $this->fotoCarrosLimpa1=false;
        $this->limpar_foto_carros('2');
        $this->mostra_foto(&$this->pixmap_foto2,null,false,@gzuncompress($resultado2["foto2"]));
        $this->fotoCarrosLimpa2=false;
        $this->limpar_foto_carros('3');
        $this->mostra_foto(&$this->pixmap_foto3,null,false,@gzuncompress($resultado2["foto3"]));
        $this->fotoCarrosLimpa3=false;

	}
}
?>