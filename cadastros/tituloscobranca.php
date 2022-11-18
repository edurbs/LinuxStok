<?
class tituloscobranca extends funcoes {
    function tituloscobranca(){
		
        
        $this->xml=$this->carregaGlade('tituloscobranca');        
        
        /*if(empty($this->xml)){
            $this->xml = new GladeXML('interface'.$bar.'tituloscobranca.glade');
            $this->janela = $this->xml->get_widget('window1');
            $this->janela->hide();
            $this->janela->connect_object('delete-event', array(&$this,'fecha_janela'));
            $this->janela->set_uposition(retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy"));
            $this->janela->set_usize(retorna_CONFIG("largura"),retorna_CONFIG("altura"));
        }
        */
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        $this->button_relatorio=$this->xml->get_widget("button_relatorio");
        $this->button_relatorio->connect_object('clicked', array(&$this, chamarelatorio),'tituloscobranca');
        
        $this->fontcolor=new GdkColor(0,0,0);
        $this->backcolor=new GdkColor(65535,65535,65535);
        $this->font=gdk::font_load ("-*-helvetica-r-r-*-*-*-120-*-*-*-*-*-*");
        $this->button_atualiza_clist=$this->xml->get_widget("button_atualiza_clist");      
        $this->entry_codigo=$this->xml->get_widget('entry_codigo');
        $this->entry_nfdoc=$this->xml->get_widget('entry_nfdoc');
        $this->entry_codcliente=$this->xml->get_widget('entry_codcliente');
        $this->label_codcliente=$this->xml->get_widget('label_codcliente');
        $this->entry_codcliente->connect_object('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, nome, contato, dtnasc, sexo, dtcadastro, cnpj_cpf, ie_rg from clientes', 
            true,
            &$this->entry_codcliente, 
            &$this->label_codcliente,
            "clientes",
            "nome",
            "codigo"
        );
        $this->entry_codcliente->connect_object('focus-out-event',
            array(&$this,retornabusca2), 
            'clientes', 
            &$this->entry_codcliente, 
            &$this->label_codcliente, 
            'codigo', 
            'nome', 
            'tituloscobranca'
        );
        $this->entry_coddevedor=$this->xml->get_widget('entry_coddevedor');
        $this->label_coddevedor=$this->xml->get_widget('label_coddevedor');
        $this->entry_coddevedor->connect_object('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, nome, contato, dtnasc, sexo, dtcadastro, cnpj_cpf, ie_rg from devedores', 
            true,
            &$this->entry_coddevedor, 
            &$this->label_coddevedor,
            "devedores",
            "nome",
            "codigo"
        );
        $this->entry_coddevedor->connect_object('focus-out-event',
            array(&$this,retornabusca2), 
            'devedores', 
            &$this->entry_coddevedor, 
            &$this->label_coddevedor, 
            'codigo', 
            'nome', 
            'tituloscobranca'
        );
        $this->entry_codmeiopgto=$this->xml->get_widget('entry_codmeiopgto');
        $this->label_codmeiopgto=$this->xml->get_widget('label_codmeiopgto');
        $this->entry_codmeiopgto->connect_object('key_press_event', 
            array(&$this,entry_enter), 
            'select codigo, descricao from meiopgto', 
            true,
            &$this->entry_codmeiopgto, 
            &$this->label_codmeiopgto,
            "meiopgto",
            "descricao",
            "codigo"
        );
        $this->entry_codmeiopgto->connect_object('focus-out-event',
            array(&$this,retornabusca2), 
            'meiopgto', 
            &$this->entry_codmeiopgto, 
            &$this->label_codmeiopgto, 
            'codigo', 
            'descricao', 
            'tituloscobranca'
        );
        $this->entry_valortotal=$this->xml->get_widget('entry_valortotal');
        $this->label_saldo=$this->xml->get_widget('label_saldo');
        $this->entry_valortotal->connect('key-press-event', array(&$this, mascara),'moeda',null,null,null);
		$this->entry_valortotal->connect_object('focus-out-event', array(&$this, corrigeNumero),'moeda', 'tituloscobranca', &$this->entry_valortotal);
        $this->entry_dtvencimento=$this->xml->get_widget('entry_dtvencimento');
        $this->entry_dtvencimento->connect('key-press-event', array(&$this, mascara),'data',null,null,null);
        $this->entry_situacao=$this->xml->get_widget('entry_situacao');
        $this->entry_spc=$this->xml->get_widget('entry_spc');
        $this->entry_protesto=$this->xml->get_widget('entry_protesto');
        $this->entry_cobranca=$this->xml->get_widget('entry_cobranca');
        $this->entry_execucao=$this->xml->get_widget('entry_execucao');
        $this->entry_criminal=$this->xml->get_widget('entry_criminal');
        $this->text_obs=$this->xml->get_widget('text_obs');

        $button_novo=$this->xml->get_widget('button_novo');
        $button_gravar=$this->xml->get_widget('button_gravar');
        $button_alterar=$this->xml->get_widget('button_alterar');
        $button_primeiro=$this->xml->get_widget('button_primeiro');
        $button_ultimo=$this->xml->get_widget('button_ultimo');
        $button_proximo=$this->xml->get_widget('button_proximo');
        $button_anterior=$this->xml->get_widget('button_anterior');
        $button_excluir=$this->xml->get_widget('button_excluir');
        
        $button_novo->connect_object('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?');
        $button_gravar->connect_object('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false);
        $button_primeiro->connect_object('clicked', array(&$this, cadastro_primeiro), 'tituloscobranca', 'tituloscobranca','codigo','func_novo','atualiza');
        $button_ultimo->connect_object('clicked', array(&$this, cadastro_ultimo), 'tituloscobranca', 'tituloscobranca','codigo','func_novo','atualiza');
        $button_proximo->connect_object('clicked', array(&$this, cadastro_proximo), 'tituloscobranca', 'tituloscobranca','codigo','func_novo','atualiza',&$this->entry_codigo);
        $button_anterior->connect_object('clicked', array(&$this, cadastro_anterior), 'tituloscobranca', 'tituloscobranca','codigo','func_novo','atualiza',&$this->entry_codigo);
        $button_excluir->connect_object('clicked', array(&$this, confirma_excluir), 'tituloscobranca', 'tituloscobranca','codigo','func_novo','atualiza',&$this->entry_codigo, &$this->button_atualiza_clist);
        $button_alterar->connect_object('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',true);
          
// aba movimentacoes ******************
        $this->entry_mcodigo=$this->xml->get_widget('entry_mcodigo');
        $this->entry_tipomov=$this->xml->get_widget('entry_tipomov');
        $this->entry_datamov=$this->xml->get_widget('entry_datamov');
        $this->entry_datamov->connect('key-press-event', array(&$this, mascara),'data',null,null,null);
        $this->entry_valor=$this->xml->get_widget('entry_valor');
        $this->entry_valor->connect('key-press-event', array(&$this, mascara),'moeda',null,null,null);
		$this->entry_valor->connect_object('focus-out-event', array(&$this, corrigeNumero),'moeda', 'tituloscobranca', &$this->entry_valor);
        $this->entry_dtvencimentovalor=$this->xml->get_widget('entry_dtvencimentovalor');
        $this->entry_dtvencimentovalor->connect('key-press-event', array(&$this, mascara),'data',null,null,null);
        $this->entry_ativo=$this->xml->get_widget('entry_ativo');
        $this->text_obs2=$this->xml->get_widget('text_obs2');
        $this->clist_movimenta=$this->xml->get_widget('clist_movimenta');
        $this->button_efetuar=$this->xml->get_widget('button_efetuar');
        $this->button_efetuar->connect_object('clicked', confirma, array(&$this, 'efetuarMovimento'),'Deseja efetuar a movimentaçao?');
        $this->button_limpar=$this->xml->get_widget('button_limpar');
        $this->button_limpar->connect_object('clicked', confirma,array(&$this, 'limparMovimento'),'Excluir esta movimentacao nao ira limpar os dados do contas a receber (se for o caso).',true);
        
        $this->cria_clist_cadastro("tituloscobranca", "c.nome", "codigo", &$this->entry_nfdoc,"tituloscobranca", 
        "
        SELECT t.codigo, cliente.nome, devedor.nome, tipo.descricao, t.valortotal, t.saldo, t.dtvencimento, t.nfdoc, t.situacao, t.spc, t.protesto, t.cobranca, t.execucao, t.criminal, t.obs  
        FROM tituloscobranca AS t 
        LEFT JOIN clientes AS cliente ON (t.codcliente=cliente.codigo) 
        LEFT JOIN devedores AS devedor ON (t.coddevedor=devedor.codigo)
        LEFT JOIN meiopgto AS tipo ON (t.meiopgto=tipo.codigo)
        "
        );
        $this->func_novo();
        $this->janela->show();
    }
    
    function func_novo(){        
        $this->entry_codigo->set_text('');
        $this->entry_nfdoc->set_text('');
        $this->entry_codcliente->set_text('');
        $this->label_codcliente->set_text('');
        $this->entry_coddevedor->set_text('');
        $this->label_coddevedor->set_text('');
        $this->entry_codmeiopgto->set_text('');
        $this->label_codmeiopgto->set_text('');
        $this->entry_valortotal->set_text('');
        $this->label_saldo->set_text('');
        $this->entry_dtvencimento->set_text('');
        $this->entry_situacao->set_text('NOVO');
        $this->entry_spc->set_text('NAO');
        $this->entry_protesto->set_text('NAO');
        $this->entry_cobranca->set_text('NAO');
        $this->entry_execucao->set_text('NAO');
        $this->entry_criminal->set_text('NAO');
        $this->text_obs->delete_text(0,-1);
        
        $this->entry_mcodigo->set_text('');
        $this->entry_tipomov->set_text('');
        $this->entry_datamov->set_text($this->datadehoje);
        $this->entry_valor->set_text('');
        $this->entry_dtvencimentovalor->set_text('');
        $this->entry_ativo->set_text('SIM');
        $this->text_obs2->delete_text(0,-1);        
        $this->clist_movimenta->clear();
    }
    
    function func_gravar($alterar){
        $codigo=$this->entry_codigo->get_text();
        if (empty($codigo) and $alterar){
            msg("Selecione o titulo!");
            return;
        }
        $nfdoc=$this->entry_nfdoc->get_text();
        $codcliente=$this->entry_codcliente->get_text();
        if(!$this->retornabusca2(null, 'clientes', &$this->entry_codcliente, &$this->label_codcliente, 'codigo', 'nome', 'tituloscobranca')){
            msg('Preencha corretamente o campo Cliente!');
            return;
        }
        $coddevedor=$this->entry_coddevedor->get_text();
        if(!$this->retornabusca2(null, 'devedores', &$this->entry_coddevedor, &$this->label_coddevedor, 'codigo', 'nome', 'tituloscobranca')){
            msg('Preencha corretamente o campo Devedor!');
            return;
        }
        $meiopgto=$this->entry_codmeiopgto->get_text();
        if(!$this->retornabusca2(null, 'meiopgto', &$this->entry_codmeiopgto, &$this->label_codmeiopgto, 'codigo', 'descricao', 'tituloscobranca')){
            msg('Preencha corretamente o campo Tipo de Divida!');
            return;
        }
        $valortotal=$this->DeixaSoNumeroDecimal($this->entry_valortotal->get_text(),2);
        if($valortotal==0){
            msg("Preencha o campo Valor Total");
            return;
        }
        $dtvencimento=$this->entry_dtvencimento->get_text();
        if($this->valida_data($dtvencimento)){
            $dtvencimento=$this->corrigeNumero($dtvencimento,"dataiso");
        }else{
            msg("Data de vencimento incorreta!");                
            return;
        }
        $situacao=$this->entry_situacao->get_text();
        $spc=$this->entry_spc->get_text();
        $protesto=$this->entry_protesto->get_text();
        $cobranca=$this->entry_cobranca->get_text();
        $execucao=$this->entry_execucao->get_text();
        $criminal=$this->entry_criminal->get_text();
        $obs=strtoupper($this->text_obs->get_chars(0,-1));
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;               
        $con->Connect();
        
        if ($alterar){
            $sql="UPDATE tituloscobranca SET codcliente='$codcliente', coddevedor='$coddevedor', valortotal='$valortotal',dtvencimento='$dtvencimento',nfdoc='$nfdoc',meiopgto='$meiopgto',situacao='$situacao',spc='$spc',protesto='$protesto',cobranca='$cobranca',execucao='$execucao',criminal='$criminal', obs='$obs' where codigo='$codigo'";
            if ($con->Query($sql)){                            
                $this->status('Registro alterado com sucesso');
            }else {
                msg("Erro ao alterar SQL");
            }
        } else {
            $sql="INSERT INTO tituloscobranca (codcliente, coddevedor, valortotal,saldo,dtvencimento,nfdoc,meiopgto,situacao,spc,protesto,cobranca,execucao,criminal,obs) 
            VALUES ('$codcliente','$coddevedor','$valortotal','$valortotal','$dtvencimento','$nfdoc','$meiopgto','$situacao','$spc','$protesto','$cobranca','$execucao','$criminal','$obs')";
            if ($lastcod=$con->QueryLastCod($sql)){            
                $this->entry_codigo->set_text($lastcod);
                $this->status('Registro gravado com sucesso');
            }else {
                msg("Erro ao gravar SQL");
            }
            $this->label_saldo->set_text($this->mascara2($valortotal,'moeda'));
        }        
        $con->Disconnect();
        $this->button_atualiza_clist->clicked();
    }


    function atualiza($resultado){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $resultado2=$con->FetchArray($resultado);
        $this->entry_codigo->set_text($resultado2["codigo"]);    
        $this->entry_nfdoc->set_text($resultado2["nfdoc"]);
        $this->entry_codcliente->set_text($resultado2["codcliente"]);
        $this->retornabusca2(null, 'clientes', &$this->entry_codcliente, &$this->label_codcliente, 'codigo', 'nome', 'tituloscobranca'); 
        $this->entry_coddevedor->set_text($resultado2["coddevedor"]);
        $this->retornabusca2(null, 'devedores', &$this->entry_coddevedor, &$this->label_coddevedor, 'codigo', 'nome', 'tituloscobranca'); 
        $this->entry_codmeiopgto->set_text($resultado2["meiopgto"]);
        $this->retornabusca2(null, 'meiopgto', &$this->entry_codmeiopgto, &$this->label_codmeiopgto, 'codigo', 'descricao', 'tituloscobranca'); 
        $this->entry_valortotal->set_text($this->mascara2($resultado2["valortotal"], 'moeda'));
        $this->label_saldo->set_text($this->mascara2($resultado2["saldo"], 'moeda'));
        $this->entry_dtvencimento->set_text($this->corrigeNumero($resultado2["dtvencimento"],"data"));
        $this->entry_situacao->set_text($resultado2["situacao"]);
        $this->entry_spc->set_text($resultado2["spc"]);
        $this->entry_protesto->set_text($resultado2["protesto"]);
        $this->entry_cobranca->set_text($resultado2["cobranca"]);
        $this->entry_execucao->set_text($resultado2["execucao"]);
        $this->entry_criminal->set_text($resultado2["criminal"]);
        $this->text_obs->delete_text(0,-1);
        $this->text_obs->insert($this->font, $this->fontcolor, $this->backcolor ,$resultado2["obs"]);
        // mostra lista de pagamentos
        $this->clist_movimenta->clear();
        $con->Connect();
        $sql="SELECT codigo, tipomov, dtmov,valor,dtvencimento,obs FROM movcobranca WHERE codtitulo='".$resultado2["codigo"]."';";
        $resultado=$con->Query($sql);
        echo "oi";
        while($i = $con->FetchRow($resultado)) {            
            $this->clist_movimenta->append(
                array(
                    $i[0],
                    $i[1],
                    $this->corrigeNumero($i[2],'data'),
                    $this->mascara2($i[3],'moeda'),
                    $this->corrigeNumero($i[4],'data'),
                    $i[5]
                )
            );
        }
    }
    
    function limparMovimento(){
        $selecionado=$this->clist_movimenta->selection;        
		if(empty($selecionado)){
			msg("Selecione uma movimentaçao para excluir!");
		}else{
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;               
            $con->Connect();	
            $codigo=$this->clist_movimenta->get_text($selecionado[0],0);
            $sql="DELETE FROM movcobranca WHERE codigo=$codigo;";
            if ($con->Query($sql)){ 
                $this->clist_movimenta->remove($selecionado[0]);            
            }else{
                msg('Erro ao excluir movimentacao!');
            }
            $con->Disconnect();
        }
        
    }
    function efetuarMovimento(){
        $codtitulo=$this->entry_codigo->get_text();
        if (empty($codtitulo)){
            msg("Selecione o titulo para efetuar a movimentaçao!");
            return;
        }
        $tipomov=$this->entry_tipomov->get_text();
        if (empty($tipomov)){
            msg("Selecione o tipo de movimentaçao!");
            return;
        }
        $datamov=$this->entry_datamov->get_text();
        if($this->valida_data($datamov)){
            $datamov=$this->corrigeNumero($datamov,"dataiso");
        }else{
            msg("Data da movimentacao incorreta!");                
            return;
        }
        $valor=$this->DeixaSoNumeroDecimal($this->entry_valor->get_text(),2);
        $dtvencimentovalor=$this->entry_dtvencimentovalor->get_text();        
        $ativo=$this->entry_ativo->get_text();
        $obs2=strtoupper($this->text_obs2->get_chars(0,-1));
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        switch ($tipomov){
            case "RECEBIMENTO":
                //saldo = saldo - valor
                //se saldo=0 entao 
                //    situacao=QUITADO
                //senao 
                //    situacao=PARCELADO
                //fim-se
                
                $valortotal=$this->DeixaSoNumeroDecimal($this->entry_valortotal->get_text(),2);
                $saldo=$this->DeixaSoNumeroDecimal($this->label_saldo->get(),2);
                if($valor==0){
                    msg("Preencha o campo Valor!");
                    return;
                }
                if($valor>$saldo){
                    msg("Valor recebido maior que o saldo a pagar! \n Use o tipo ACRESCIMO para aumentar o saldo devedor.");
                    $con->Disconnect();
                    return;
                }
                $saldoatual=$saldo-$valor;
                $saldoatual=number_format($saldoatual,2,".","");
                $sql="UPDATE tituloscobranca SET saldo='$saldoatual'";
                if($saldoatual=="0.00"){
                    $sql.=",situacao='QUITADO' ";
                    $this->entry_situacao->set_text('QUITADO');
                }else{
                    $sql.=",situacao='PARCELADO' ";
                    $this->entry_situacao->set_text('PARCELADO');
                }
                $sql.=" where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->label_saldo->set_text($this->mascara2($saldoatual,'moeda'));
                break;
                //************************
            case "ACRESCIMO":
                // saldo = saldo + valor
                $saldo=$this->DeixaSoNumeroDecimal($this->label_saldo->get(),2);
                if($valor==0){
                    msg("Preencha o campo Valor!");
                    return;
                }
                $saldoatual=$saldo+$valor;
                $saldoatual=number_format($saldoatual,2,".","");
                $sql="UPDATE tituloscobranca SET saldo='$saldoatual' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->label_saldo->set_text($this->mascara2($saldoatual,'moeda'));
                break;
                //************************
            case "LOCALIZACAO":
                // situacao=LOCALIZADO
                $sql="UPDATE tituloscobranca SET situacao='LOCALIZADO' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_situacao->set_text('LOCALIZADO');
                break;
                //************************
            case "TELEFONEMA":
                // apenas registra movimentacao
                break;
                //************************
            case "CARTA":
                // apenas registra movimentacao
                break;
                //************************
            case "VISITA":
                // apenas registra movimentacao
                break;
                //************************
            case "SPC":
                // se ativo=sim entao
                //    SPC=sim
                // senao
                //    SPC=nao
                // fim-se
                $ativo=$this->entry_ativo->get_text();
                $sql="UPDATE tituloscobranca SET spc='$ativo' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_spc->set_text($ativo);
                break;
                //************************
            case "PROTESTO":
                $ativo=$this->entry_ativo->get_text();
                $sql="UPDATE tituloscobranca SET protesto='$ativo' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_protesto->set_text($ativo);
                break;
                //************************
            case "COBRANCA":
                $ativo=$this->entry_ativo->get_text();
                $sql="UPDATE tituloscobranca SET cobranca='$ativo' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_cobranca->set_text($ativo);
                break;
                //************************
            case "EXECUCAO":
                $ativo=$this->entry_ativo->get_text();
                $sql="UPDATE tituloscobranca SET execucao='$ativo' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_execucao->set_text($ativo);
                break;
                //************************
            case "CRIMINAL":
                $ativo=$this->entry_ativo->get_text();
                $sql="UPDATE tituloscobranca SET criminal='$ativo' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_criminal->set_text($ativo);
                break;
                //************************
            case "PARCELAMENTO":                
                $sql="UPDATE tituloscobranca SET situacao='PARCELADO' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_situacao->set_text('PARCELADO');
                break;
                //************************
            case "QUITACAO":
                // se saldo<>0 entao nao altera situacao para quitado
                $saldo=$this->DeixaSoNumeroDecimal($this->label_saldo->get(),2);
                if($saldo<>0.00){
                    msg("Saldo precisa ser 0 para quitar o titulo.\n Utilize o tipo RECEBIMENTO para diminuir o valor do saldo.");
                    return;                
                }
                $sql="UPDATE tituloscobranca SET situacao='QUITADO' where codigo='$codtitulo'";
                if (!$con->Query($sql)){
                    msg('Erro SQL atualizando tabela tituloscobranca');
                    $con->Disconnect();
                    return;
                }
                $this->entry_situacao->set_text('QUITADO');
                break;
                //************************
            case "HONORARIOS":
                // coloca no "contas a receber" o valor de honorarios a receber do cliente
                if(!$this->receberHonorarios('LANCAMENTO DE HONORARIOS DE COBRANCA')){return;}
                break;
                //************************
        }
        if($dtvencimentovalor=="" or $dtvencimentovalor=="00-00-0000"){
            $dtvencimentovalor="0001-01-01";
        }else{
            if($this->valida_data($dtvencimentovalor)){                
                $dtvencimentovalor=$this->corrigeNumero($dtvencimentovalor,"dataiso");
            }else{
                msg("Data de vencimento incorreta!");                
                return;
            }
        }
        $sql= "INSERT INTO movcobranca (codtitulo,tipomov,dtmov,valor,dtvencimento,obs)";
        $sql.=" VALUES ('$codtitulo','$tipomov','$datamov','$valor','$dtvencimentovalor','$obs2')";
		if (!$lastcod=$con->QueryLastCod($sql)){
			msg('Erro SQL gravando movimento');
            $con->Disconnect();
            return;
        }
        
        $this->clist_movimenta->append(
            array(
                $lastcod,
                $tipomov,
                $this->corrigeNumero($datamov,'data'),
                $this->mascara2($valor,'moeda'),
                $this->corrigeNumero($dtvencimentovalor,'data'),
                $obs2
            )
        );
        $this->button_atualiza_clist->clicked();
        
        // limpando campos
        $this->entry_mcodigo->set_text('');
        $this->entry_tipomov->set_text('');
        $this->entry_datamov->set_text($this->datadehoje);
        $this->entry_valor->set_text('');
        $this->entry_dtvencimentovalor->set_text('');
        $this->entry_ativo->set_text('SIM');
        $this->text_obs2->delete_text(0,-1);      
        
        $this->status('Movimento de cobranca efetuado com sucesso');
    }
    
    function receberHonorarios($historico){
        $valor=$this->DeixaSoNumeroDecimal($this->entry_valor->get_text(),2);
        if($valor==0){            
            msg('Valor nao pode ser 0');
            return false;
        }
        $dtvencimentovalor=$this->entry_dtvencimentovalor->get_text();        
        if($this->valida_data($dtvencimentovalor)){        
            $dtvencimentovalor=$this->corrigeNumero($dtvencimentovalor,"dataiso");
        }else{
            msg('Entre com a data de vencimento do Honorario!');
            return false;
        }        
        $obs2=strtoupper($this->text_obs2->get_chars(0,-1));        
        if(empty($obs2)){
            msg('Digite o numero do documento ou Nota Fiscal no campo observacoes.');
            return false;
        }
        $codcliente=$this->entry_codcliente->get_text();
        $codplacon=1;
        $data_c=$this->corrigeNumero($this->datadehoje,"dataiso");
        $sql ="INSERT INTO receber (fiscal, data_c, data_v, valor, saldo, descr, codorigem, codplacon) ";
        $sql.="VALUES ('$obs2', '$data_c', '$dtvencimentovalor', '$valor', '$valor', '$historico', '$codcliente', '$codplacon')";
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        if (!$con->Query($sql)){
            msg('Erro SQL atualizando tabela receber');
            $con->Disconnect();
            return false;
        }
        return true;
    }
}
?>