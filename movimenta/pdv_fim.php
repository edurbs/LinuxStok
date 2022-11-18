<?php

class pdv_fim extends pdv_extras {
    function ConfirmaImprimirRecibo($lastcod,$tabela){
        if($tabela=="saidas"){
            $slogan=" --==VENDA==--";
            $titulo="Venda";
            $tabela2="entsai";
            $codtabela="codsaidas";
            $codtabela2="codentsai";
            $tipoPPG="S";
            $assinatura=true;
        }else{
            $slogan=" --==ORCAMENTO==--";
            $titulo="Orca.";
            $tabela2="entsai";
            $codtabela="codorcamento";
            $codtabela2="codentsai";
            $tipoPPG="O";
            $assinatura=false;
        }
		// imprime em varias vias
		$vias=$this->retorna_OPCAO("viasrecibo");
		if(!is_numeric($vias)){
			$vias=1;
		}
        for($i=0;$i<$vias;$i++){
            confirma(array($this,imprimirRecibo),'Deseja imprimir ESTA via do recibo?',$lastcod,$tabela, $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura);
            //$this->imprimirRecibo($lastcod,$tabela);
        }
        // chama automaticamente a troca de funcionarios
        if($this->retorna_OPCAO("autotrocasenhapdv")){
        	$this->trocausuario();
        }
    }

    function vende(){
        global $usuario, $parente;
		$numerorows=$this->numero_rows_liststore($this->liststore_venda);
        if($numerorows==0){
            msg('Lista de venda vazia.');
            return;
        }
        if($this->retorna_OPCAO('pdvenderecocliente')){
			$codendereco=$this->label_codendereco->get_text();
			if(empty($codendereco)){
				msg('Selecione o endereco do cliente!!!');
				return;
			}
		}
        $codcli=$this->entry_codcli->get_text();
        if(empty($codcli)){
            msg('Selecione o cliente!!!');
            return;
        }
        $habcomprar=$this->retornabusca4('habcomprar','clientes','codigo',$codcli);
        if($habcomprar=="Bloqueado" and !$this->verificaPermissao('030115',false)){
        		msg("Cliente bloqueado. Nao ha permissoes para vender.");
        		return;
        }

        $this->xmlF12=$this->carregaGlade('finalizavendas',false,false,false,false);
        $this->janelaF12 = $this->xmlF12->get_widget('window1');
        if($parente){
        	$this->janelaF12->set_transient_for($parente);
        }

        $this->scrolledwindow_prazo=$this->xmlF12->get_widget('scrolledwindow_prazo');
        $this->liststore_prazo=new GtkListStore(
            Gobject::TYPE_STRING, // 0 cod meio pgto
            Gobject::TYPE_STRING, // 1 meio
            Gobject::TYPE_STRING, // 2 valor
            Gobject::TYPE_STRING, // 3 venci
            Gobject::TYPE_STRING, // 4 venci oculto para controlar a tolerancia de alt. data
            Gobject::TYPE_STRING  // 6 cod parcela pgto
        );
        $this->treeview_prazo=new GtkTreeView($this->liststore_prazo);
		$this->treeview_prazo->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
            array('Cod.', 'Meio', 'Valor', 'Vencimento', ''),
            $this->treeview_prazo,
			array('50','150','150','150','0')
        );
        
        $this->scrolledwindow_prazo->add($this->treeview_prazo);
        $this->scrolledwindow_prazo->show_all();

        $this->label_total=$this->xmlF12->get_widget("label_total");
        $totalvenda=$this->pegaNumero($this->label_precototalvenda);
        $this->setLabel_total($totalvenda);


        $this->label_valorfinal=$this->xmlF12->get_widget("label_valorfinal");
        $this->label_variacao=$this->xmlF12->get_widget("label_variacao");
        $this->frame_variacao=$this->xmlF12->get_widget("frame_variacao");
        $this->label_titulovariacao=$this->xmlF12->get_widget("label_titulovariacao");

        $this->entry_formapgto=$this->xmlF12->get_widget('entry_forma');
        $this->label_formapgto=$this->xmlF12->get_widget('label_forma');
        $this->label_formapgto->set_style($this->VermelhoP);


        $this->button_altdata=$this->xmlF12->get_widget("button_altdata");
        $this->button_altdata->connect_simple('clicked', array(&$this, 'alteradataparcela'));

        $this->button_finalizar=$this->xmlF12->get_widget("button_finalizar");
        $this->button_finalizar->connect_simple('clicked',confirma, array(&$this, 'finalizarvenda'),'Deseja realizar esta VENDA?',false);
        $this->button_finalizar->set_sensitive($this->verificaPermissao('030102',false));
        /*if(!$this->problema030108 or !$this->problema030109){ //Vender a prazo para cliente com contas atrasadas OU com d�ito m�imo estourado
            $this->button_finalizar->set_sensitive(false);
        }*/
        $this->button_finalizar->set_sensitive(false);

        $this->button_orca=$this->xmlF12->get_widget("button_orca");
        $this->button_orca->connect_simple('clicked',confirma, array(&$this, 'finalizarvenda'),'Deseja gravar um ORCAMENTO?','orcamento');
        $this->button_orca->set_sensitive($this->verificaPermissao('030103',false));

        $this->button_cancelar=$this->xmlF12->get_widget("button_cancelar");
        $this->button_cancelar->connect_simple('clicked', array(&$this, hidefinalizar));

        $this->buttonDesconto=$this->xmlF12->get_widget("buttonDesconto");
        $this->buttonDesconto->connect_simple('clicked',array(&$this, 'desconto'),'T');
        $this->buttonDesconto->set_sensitive($this->verificaPermissao('030105',false));

        $this->label_descontoT=$this->xmlF12->get_widget("label_descontoT");
        
        $this->radiobutton_programarentrega=$this->xmlF12->get_widget("radiobutton_programarentrega");
        $this->radiobutton_programarentrega->connect_simple("toggled",array($this,'toggle_entrega'));
        $this->radiobutton_clienteleva=$this->xmlF12->get_widget("radiobutton_clienteleva");
        $this->radiobutton_clienteleva->connect_simple("toggled",array($this,'toggle_entrega'));
        $this->checkbutton_baixafutura=$this->xmlF12->get_widget("checkbutton_baixafutura");
        $this->checkbutton_baixafutura->set_sensitive(FALSE);
        //$this->radiobutton_programarentrega->set_active(TRUE); 
        $this->radiobutton_clienteleva->set_active(TRUE);


        $this->janelaF12->set_focus($this->entry_formapgto);

        // verifica se for orcamento pra pegar as formas de pagamento
        $codorca=$this->entry_codorca->get_text();
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
		$con=new $BancoDeDados;
		$con->Connect();
        if(empty($codorca)){ // se for venda comum descobre ultima forma pgto
			$this->conecta_entry_formapgto();
        	// pega ultima venda	
        	$sql="select codsaidas from saidas where codcli='$codcli' order by data desc limit 1";
        	$resultado=$con->Query($sql);
			$i = $con->FetchRow($resultado);
			// pega ultima forma de pagamento da ultima venda
			if(!empty($i[0])){
         		$sql="select codformapgto from movpagamentos where codorigem='$i[0]' order by data desc limit 1";
	         	$resultado=$con->Query($sql);
				$i = $con->FetchRow($resultado);
				$ultimaformapgto=$i[0];
				// bota forma de pgto na tela
				$this->entry_formapgto->set_text($ultimaformapgto);
			}
        }else{ // se for orcamento			
			$sql="SELECT totalmerc, totalnf, desconto, variacao FROM orcamento WHERE codorcamento='$codorca'";
			$resultado=$con->Query($sql);
			$j = $con->FetchRow($resultado);
			$this->setLabel_total($j[1]);
			$valorfinal=$j[0];
			$this->setLabel_valorfinal($valorfinal);
			$this->setLabel_variacao($j[3]);
			$this->setLabel_descontoT($j[2]);

			// se valor final do orcamento = valor da lista (se nao incluiu/excluiu nada
	       	if($j[1]==$this->pegaNumero($this->label_precototalvenda)){
				$sql="SELECT m.codmeiopgto,m.meio,m.valor,m.data,m.codformapgto FROM movpagamentos AS m WHERE tipo='O' AND codorigem='$codorca'";
				$resultado=$con->Query($sql);
				$i = $con->FetchRow($resultado);
				$formapgto=$i[4];
				do {
					$this->liststore_prazo->append(
						array(
							$i[0],
							$i[1],
							number_format($i[2],2,",",""),
							$this->corrigeNumero($i[3],'data'),
							$this->corrigeNumero($i[3],'data'),
							$i[4]
						)
					);
				} while($i = $con->FetchRow($resultado));
				$this->entry_formapgto->set_text($formapgto);
				// pega a taxa fixa
				$taxafixa=$this->retornabusca3('formapgto',$formapgto,'codigoformapgto','taxafixa');
				$this->setLabel_valorfinal($valorfinal+$taxafixa);
				
			}else{ // se valor final <> da lista
				//$sql="SELECT m.codmeiopgto,m.meio,m.valor,m.data,m.codformapgto FROM movpagamentos AS m WHERE tipo='O' AND codorigem='$codorca'";
				//$resultado=$con->Query($sql);
			}
            if($tmp=$this->prazoOUvista($formapgto)){
                msg($tmp);
            }
            $con->Disconnect();
            $this->conecta_entry_formapgto();
        }
    }
	function conecta_entry_formapgto(){
		$this->entry_formapgto->connect('key_press_event',
            array($this,'entry_enter'),
            "select codigoformapgto, descricao, variacao, parcelas, arredonda from formapgto where ativa='1'",
            true,
            $this->entry_formapgto,
            $this->label_formapgto,
            "formapgto",
            "descricao",
            "codigoformapgto"
        );
        $this->entry_formapgto->connect_simple('focus-out-event',
            array($this,'retornabusca22'),
            'formapgto',
            $this->entry_formapgto,
            $this->label_formapgto,
            'codigoformapgto',
            'descricao',
            'formapgto'
        );
        $this->entry_formapgto->connect_simple('changed', array($this,'inserirparcela'));
	}
	function toggle_entrega(){
		if($this->radiobutton_programarentrega->get_active()){
			$this->checkbutton_baixafutura->set_active(FALSE);
			$this->checkbutton_baixafutura->set_sensitive(TRUE);
		}elseif($this->radiobutton_clienteleva->get_active()){
			$this->checkbutton_baixafutura->set_active(FALSE);
			$this->checkbutton_baixafutura->set_sensitive(FALSE);
		}
	}
    function setLabel_total($valor){
	    $valor=number_format($valor, 2, ',', '');
		$this->label_total->set_text($valor);
	}

	function setLabel_variacao($valor){
		$valor=number_format($valor, 2, ',', '');
		$this->label_variacao->set_text($valor."%");
	}

    function hidefinalizar(){
        $this->janelaF12->hide();
    }

    function NaoExisteFormaPgto(){
        $this->label_formapgto->set_text(" << Pressione ENTER para buscar");
        $this->liststore_prazo->clear();
    }

    function inserirparcela(){
    		
        $forma=intval($this->entry_formapgto->get_text());
        if(empty($forma)){
            return;
        }else{
    		if($forma<>$this->LastFormaPgto){
    			$this->LastFormaPgto=$forma;
    			$this->setLabel_descontoT(0);
    		}
        }
        // verifica se a forma existe
        if (!$this->retornabusca2('formapgto', &$this->entry_formapgto, &$this->label_formapgto, 'codigoformapgto', 'descricao', 'parcelapgto')){
            $this->NaoExisteFormaPgto();
            return;
        }
        // verifica se a forma esta ativa
        if($this->retornabusca3('formapgto',$forma,'codigoformapgto','ativa')==0){
            $this->status('Esta forma de pagamento está desativada.');
            $this->NaoExisteFormaPgto();
            return;
        }
        // variacao obrigatoria sim=1 nao=0
        $variacaoobrigatoria=$this->retornabusca3('formapgto',$forma,'codigoformapgto','variacaoobrigatoria');
        
        // verifica se devera arrendondar
        $arredonda=$this->retornabusca3('formapgto',$forma,'codigoformapgto','arredonda');

        // pega a quantidade de parcelas
        $parcelas=$this->retornabusca3('formapgto',$forma,'codigoformapgto','parcelas');

        // pega a variacao
        $variacao=$this->retornabusca3('formapgto',$forma,'codigoformapgto','variacao');
		
		// pega a taxa fixa
		$taxafixa=$this->retornabusca3('formapgto',$forma,'codigoformapgto','taxafixa');

        // verifica se este numero de parcelas estao cadastradas
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="select count(codigoformapgto) from parcelapgto where codigoformapgto='$forma'";
        $resultado=$con->Query($sql);
        $j = $con->FetchRow($resultado);

        $sql="SELECT SUM(porcentagem) FROM parcelapgto WHERE codigoformapgto='$forma' AND tipoentrada='0'";
        $resultado=$con->Query($sql);
        $k = $con->FetchRow($resultado);

        $con->Disconnect();
		// se nao tiver com as parcelas da forma de pgto cadastradas
        if($j[0]<$parcelas){
            msg('Falta cadastrar as parcelas individuais para esta forma de pagamento');
            $this->NaoExisteFormaPgto();
            return;
        }elseif($k[0]<100){
            msg("Cadastro de parcelas da forma de pgto completa apenas $k[0]% do valor da compra.");
            $this->NaoExisteFormaPgto();
            return;
        }else{
            // se tiver com todas parcelas cadastradas
            $this->liststore_prazo->clear();

            $totalnf=$this->pegaNumero($this->label_precototalvenda);
            $totalmerc=$this->pegaNumero($this->label_valorfinal);
            $desconto=$totalmerc-$totalnf;
            if($totalmerc==0){
                $totalmerc=$totalnf;
            }
            $this->setLabel_variacao($variacao);
            if($variacaoobrigatoria){
        		// soma total mais variacao
        		$totalfinal=$totalnf+($totalnf*($variacao/100));
        		$this->limiteDescontoVariacao[0]=false;
        		$this->label_titulovariacao->set_text("Variacao Obrigatoria");
        		// desliga descontos se nao for permitido descontos acima do limite;
    			$this->buttonDesconto->set_sensitive($this->verificaPermissao('030117',false));    		
        	}else{ // variacao opcional
        		// liga descontos;
    			$this->buttonDesconto->set_sensitive(true);
        		// limite de desconto
        		$this->label_titulovariacao->set_text("Variacao Opcional");
        		$this->limiteDescontoVariacao[0]=true; // liga limite de desconto
        		$this->limiteDescontoVariacao[1]=$totalnf*($variacao/100); // seta qual o limite em reais
        		$totalfinal=$totalnf;            			
        	}
            
            // da o desconto final total
            $totalfinal=$totalfinal+$this->pegaNumero($this->label_descontoT);
			// soma taxa fixa
			$totalfinal+=$taxafixa;

			// coloca valor na tela
			$this->setLabel_valorfinal($totalfinal);

            //pega cada parcela e sua porcentagem
            $sql="SELECT p.porcentagem, m.descricao, p.prazo, p.tipoprazo, p.codigomeiopgto, f.chkdatafixa, f.datafixa, f.chkdiadasemana, f.diadasemana, p.codigoparcelapgto, p.tipoentrada FROM parcelapgto AS p INNER JOIN meiopgto AS m ON m.codigo=p.codigomeiopgto INNER JOIN formapgto AS f ON p.codigoformapgto=f.codigoformapgto WHERE p.codigoformapgto='$forma' ORDER BY p.tipoentrada DESC, p.prazo ASC";
            $con->Connect();
            $resultado=$con->Query($sql);
			$j=0; // controle da array
			$totalfinal_sem_entrada=$totalfinal;
            while($i = $con->FetchRow($resultado)){
                // calcula o valor da parcela
                $tipoentrada=$i[10];
                $porcentagem_parcela=$i[0];
                // se tipo da parcela for "entrada", onde se digita o valor da parcela
                if($tipoentrada=="1"){
					// registra a entrada para somar centavos na segunda parcela
					$houve_tipoentrada=TRUE;
                	// pergunta valor da parcela
                	$valorparcela=inputdialog("Digite o valor da entrada",'virgula');
                	$valorparcela=$this->pegaNumero($valorparcela);
                	// verifica se o valor da parcela esta acima da porcentagem minima cadastrada
					$valor_minimo_parcela=($totalfinal_sem_entrada/100)*$porcentagem_parcela;
                	if($valorparcela<$valor_minimo_parcela){
                		msg("Valor abaixo do minimo permitido para esta parcela.");
                		return;
                	}
                	if($valorparcela>$totalfinal){
                		msg("Valor maior que o total da venda! Digite um valor menor.");
                		return;
                	}elseif($valorparcela==0){
                		// se deixar valor ZERO na entrada calcula-se o valor  da parcela proporcionalmente ao numero de parcelas da forma de pagamento
						
                		// pega quantidade de parcelas desta forma de pgto
                		$sql_tmp="SELECT COUNT(codigoparcelapgto) FROM parcelapgto WHERE codigoformapgto='$forma'";
						$resultado_tmp=$con->Query($sql_tmp);
						$tmp=$con->FetchRow($resultado_tmp);
                		$numero_parcelas_formapgto=$tmp[0];
						
						// pega a porcentagem de diferenca das parcelas e soma para porcentagem de entrada
						$divisao=100/$numero_parcelas_formapgto;
						$sql_entrada_zero="SELECT porcentagem FROM parcelapgto WHERE codigoformapgto='$forma' AND tipoentrada='0'";
						$resultado_entrada_zero=$con->Query($sql_entrada_zero);
						while($i_entrada_zero=$con->FetchRow($resultado_entrada_zero)){
							$entrada_zero+=$i_entrada_zero[0]-$divisao;
						}
						// calcula o valor da entrada
 		                $valorparcela=($totalfinal_sem_entrada*$entrada_zero)/100;
                	}// fim do SE for entrada = 0
                	$totalfinal_sem_entrada=$totalfinal-$valorparcela;
                }else{ // se nao for tipoentrada calcula automaticamente valor da parcela
	                // aplica variacao de desconto ou acrescimo
	                $valorparcela=($totalfinal_sem_entrada*$porcentagem_parcela)/100;
                }

                //verifica o tipo do prazo (mes ou dia)
                $diadehoje=date("d");
                $mesdehoje=date("m");
                $prazo=$i[2];
                $tipoprazo=$i[3];
                $chkdatafixa=$i[5];
                $datafixa=$i[6];
				$chkdiadasemana=$i[7];
				$diadasemana=$i[8];
                
                if($tipoprazo=="Dia(s)"){
            		$diadehoje+=$prazo;
                    $vencimento= mktime(0,0,0,$mesdehoje,$diadehoje,date("Y"));
                    if($chkdatafixa=="1"){ // data fixa                    		
                		while(date("d",$vencimento)!=$datafixa){ // se nao for hoje
                			$diadehoje++; // acrescenta uma dia ate chegar na data fixa
                			$vencimento= mktime(0,0,0,$mesdehoje,$diadehoje,date("Y"));
                		}
                    }elseif($chkdiadasemana=="1"){ // dia da semana
                		while(date("w",$vencimento)!=$diadasemana){ // se nao for hoje
                			$diadehoje++; // acrescenta um dia ate chegar no dia especificado
                			$vencimento= mktime(0,0,0,$mesdehoje,$diadehoje,date("Y"));
                		}
                    }
                }elseif($tipoprazo=="Mes(es)"){
            		$mesdehoje+=$prazo;
                    $vencimento= mktime(0,0,0,$mesdehoje,$diadehoje,date("Y"));
                    if($chkdatafixa=="1"){                    		
                		while(date("d",$vencimento)!=$datafixa){
                			$diadehoje++;
                			$vencimento= mktime(0,0,0,$mesdehoje,$diadehoje,date("Y"));
                		}
                    }elseif($chkdiadasemana=="1"){
                		while(date("w",$vencimento)!=$diadasemana){
                			$diadehoje++;
                			$vencimento= mktime(0,0,0,$mesdehoje,$diadehoje,date("Y"));
                		}
                    }
                }else{
                    msg('Tipo de prazo estranho');
                    $con->Disconnect();
                    return;
                }
                $vencimento= date("d/m/Y",$vencimento);
                // acumula diferenca dos centavos
                if($arredonda=='1'){
                    $diferenca+=$valorparcela-intval($valorparcela);
                    $valorparcela=$valorparcela-($valorparcela-intval($valorparcela)); // tira os centavos
                    if($valorparcela==0){
                		msg("Valor muito pequeno para parcelar!");
                		return;
                    }
                }
                // insere dados na lista
				$arrayprazo[$j]=array(
					$i[4], // p.codigomeiopgto
					$i[1],
					number_format($valorparcela,2,',',''),
					$vencimento, // vencimento real 
					$vencimento, // o segundo vencimento eh oculto e serve para controlar a tolerancia
					$i[9]
				); 
				$j++;
            }
            // soma diferenca dos centavos na primeira parcela
            if($arredonda=='1'){
				// se tiver digitado uma entrada, botar na segunda parcela
				if($houve_tipoentrada){
					$num_parcela=1;	
				}else{
					$num_parcela=0;
				}
				$primeiraparcela=$this->pegaNumero($arrayprazo[$num_parcela][2]);
                $primeiraparcela+=$diferenca;
				$arrayprazo[$num_parcela][2]=number_format($primeiraparcela,2,",","");
            }
        }// fim do calculo das parcelas

        // verifica se o valor final nao e maior que o valor da venda
        foreach ($arrayprazo as $tmp){
			$parcela=$tmp[2];
			$soma+=$parcela;
			if($this->pegaNumero($parcela)==0){
				msg("Alguma parcela tem valor ZERO");
				return;
			}
		}
		if($soma>$totalfinal){
			msg("Valor total das parcela maior que o valor da venda!!");
			return;
		}
			
        if($tmp=$this->prazoOUvista($forma)){
            msg($tmp);
        }else{
    		foreach ($arrayprazo as $tmp){
				$this->liststore_prazo->append($tmp);
			}
        }
        return;
    }

    function setLabel_valorfinal($valor){
    	$valor=number_format($valor, 2, ',', '');
		$this->label_valorfinal->set_text($valor);
    }

    function prazoOUvista($formapgto){
        // ativa ou n� o botao de vender de acordo com a permiss�
        $this->button_finalizar->set_sensitive($this->verificaPermissao('030102',false));
        // verifica se a forma de pgto �a vista
		$avista=$this->retornabusca4('avista','formapgto','codigoformapgto',$formapgto);
		if($avista=='1'){
			if($this->ClienteComDebitoMaximo){
				// " se o cliente tiver com debito maximo estourado";
				if(!$this->verificaPermissao('030111',false)){
					$this->button_finalizar->set_sensitive(false);
					$msgtmp.="Sem permissão para vender a vista para cliente com débito máximo estourado\n";
				}
			}
			if($this->ClienteComDebitoAtrasado){
				// " se o cliente tiver com contas atrasadas";
				if(!$this->verificaPermissao('030110',false)){
					$this->button_finalizar->set_sensitive(false);
					$msgtmp.="Sem permissão para vender a vista para cliente com contas atrasadas\n";
				}
			}
         }else{// se for a prazo
         	// se tiver bloqueado no cadastro do cliente para vender somente a vista
         	$codcli=$this->entry_codcli->get_text();
         	if($this->retornabusca4('habcomprar','clientes','codigo',$codcli)=="Somente a vista" and !$this->verificaPermissao('030116',false)){
         		//return "Permitido vender somente a vista para este cliente.";
         		$msgtmp.="Permitido vender somente a vista para este cliente.\n";
         	}
            // "a prazo";
            if($this->ClienteComDebitoMaximo){
                // " se o cliente tiver com debito maximo estourado";
                if(!$this->verificaPermissao('030109',false)){
                    $this->button_finalizar->set_sensitive(false);
                    $msgtmp.="Sem permissão para vender a prazo para cliente com débito máximo estourado\n";
                }
            }
            if($this->ClienteComDebitoAtrasado){
                // "// se o cliente tiver com contas atrasadas";
                if(!$this->verificaPermissao('030108',false)){
                    $this->button_finalizar->set_sensitive(false);
                    $msgtmp.="Sem permissão para vender a prazo para cliente com contas atrasadas\n";
                }
            }
        }
        if(!empty($msgtmp)){
            return $msgtmp;
        }else{
            return false;
        }

    }

    function alteradataparcela(){
    
        $selecionado=$this->treeview_prazo->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_prazo)){
        		$codigo=$this->liststore_prazo->get_value($iter,5); // cod parcela pgto
            $data=substr($this->liststore_prazo->get_value($iter,3),0,2);                
            $tolerancia=$this->retornabusca4('tolerancia','parcelapgto','codigoparcelapgto',$codigo);
            $this->windowdata=new GtkWindow();
            $this->windowdata->set_position(1); // posiciona no centro da tela
            $this->vbox1=&new GtkVBox();
            $this->frame1=&new GtkFrame("Diferenca do dia");
            $this->spinbutton=&new GtkSpinButton();
            $tmp=new GtkAdjustment(0,0-$tolerancia,$tolerancia,1,10,20);
            $this->spinbutton->set_text(0);
            $this->spinbutton->set_adjustment($tmp);

            $this->frame1->add($this->spinbutton);
            $this->button_confirmadata= &new GtkButton("Confirma");
            $this->button_confirmadata->connect_simple('clicked',array($this,'clickalteradata'),$iter, $tolerancia);
            $this->vbox1->add($this->frame1);
            $this->vbox1->add($this->button_confirmadata);
            $this->windowdata->add($this->vbox1);
            $this->windowdata->show_all();
            $this->spinbutton->grab_focus();
        }
    }

    function clickalteradata($iter,$tolerancia){

        $altera=$this->spinbutton->get_text();
        
        // data oculta para controle
        $dataO=$this->liststore_prazo->get_value($iter,4);
        $diaO=substr($dataO,0,2);
        $mesO=substr($dataO,3,2);
        $anoO=substr($dataO,6,4);
        $vencimentoO= mktime(0,0,0,$mesO,$diaO,$anoO);
        $vencimentoO= date("d/m/Y",$vencimentoO);
        
        $vencimentoMAX=mktime(0,0,0,$mesO,$diaO+$tolerancia,$anoO);
        $vencimentoMIN=mktime(0,0,0,$mesO,$diaO-$tolerancia,$anoO);
        
        // data alterada
        $data=$this->liststore_prazo->get_value($iter,3);
        $dia=substr($data,0,2);
        $mes=substr($data,3,2);
        $ano=substr($data,6,4);        
        $vencimento= mktime(0,0,0,$mes,$dia+$altera,$ano);        
        
        // controla tolerancia
        if(($vencimento>$vencimentoMAX or $vencimento<$vencimentoMIN) AND !$this->verificaPermissao('030107',false)){
        		msg("Data alterada alem da tolerancia. Sem permissao para alterar.");
        }else{
        		// grava altera�o da data final
        		$vencimento= date("d/m/Y",$vencimento);
        		$this->liststore_prazo->set($iter,3,$vencimento);
        	}
        $this->windowdata->destroy();
    }


    function finalizarvenda($operacao="venda"){
        global $usuario;
        if($this->numero_rows_liststore($this->liststore_prazo)==0){
            msg('Informe a forma de pagamento e pressione F5.');
            return;
        }
		$formapgto=$this->pegaNumero($this->entry_formapgto);
        $codvendedor=$usuario;
        if(empty($codvendedor)){
			msg('Para efetuar uma venda voce deve cadastrar um vendedor(funcionario) e cadastrar uma senha para o mesmo no menu Sistema->Seguranca->Senhas. Se voce acabou de cadastrar uma senha, efetue logoff do sistema e tente novamente.');
			return;
		}
        $hoje=$this->corrigeNumero($this->datadehoje,"dataiso");

        $desconto=$this->pegaNumero($this->label_descontoT);

        $totalnf=$this->pegaNumero($this->label_precototalvenda);
        $totalmerc=$this->pegaNumero($this->label_valorfinal);

		if($operacao=="orcamento"){
			$this->finalizavenda2($operacao);
		}else{
			// se integracao estoque-financeiro estiver ativada
			if(!$this->retorna_OPCAO('integraestoquefinanceiro')){
				$this->finalizavenda2();
			}else{
				$BancoDeDados=retorna_CONFIG("BancoDeDados");
				$con=new $BancoDeDados;
				$con->Connect();
				$codcli=$this->entry_codcli->get_text();

				// verifica se o cliente tem debito na tabela receber
				$sql="select sum(saldo) from receber where codorigem='$codcli';";
				$resultado=$con->Query($sql);
				$saldo=$con->FetchRow($resultado);

				$debitomaximo=$this->retornabusca3('clientes', &$this->entry_codcli, 'codigo', 'debmaximo', 'pdv');
				$retorna=false;
				if($saldo[0]<=($debitomaximo+$totalmerc)){
					$this->finalizavenda2();
				}else{
					$avista=$this->retornabusca4('avista','formapgto','codigoformapgto',$formapgto);
					if($avista=='1'){					
						// "a vista";
						if(!$this->verificaPermissao('030113',false)){
							$this->button_finalizar->set_sensitive(false);
							$msgtmp.="Sem permissão para vender a vista ultrapassando debito máximo\n";
							$retorna=true;
						}
					}else{
						// " se o cliente tiver com debito maximo estourado";
						if(!$this->verificaPermissao('030112',false)){
							$this->button_finalizar->set_sensitive(false);
							$msgtmp.="Sem permissão para vender a prazo ultrapassando débito máximo\n";
							$retorna=true;
						}
					}
					$msgtmp2="Saldo DEVEDOR: ".$this->mascara2($saldo[0],'moeda')." +\nEsta compra: ".$this->mascara2($totalmerc,'moeda')." =\nTotal de Debitos:".$this->mascara2($saldo[0]+$totalmerc,'moeda')."\nSaldo devedor maior que DEBITO MAXIMO de ".$this->mascara2($debitomaximo,'moeda')." deste cliente.";
					if($retorna){
						msg($msgtmp);
						msg($msgtmp2);
						$con->Disconnect();
						return;
					}else{
						confirma(array($this,'finalizavenda2'),$msgtmp2."\nDeseja Vender mesmo assim?");
					}
				}
			}
		}
    }

    function finalizavenda2($operacao="venda"){
        // acha o desconto total (da tela de pagamentos)
        global $usuario;
		$this->sqltotal="";
		$this->codformapgto=$this->entry_formapgto->get_text();
		$totalnf=$this->pegaNumero($this->label_precototalvenda);
        $totalmerc=$this->pegaNumero($this->label_valorfinal);
        $this->codvendedorAUX=$usuario;
		//if(empty($this->codvendedorAUX)){
		//	$this->codvendedorAUX='null';
		//}
        $this->hojeAUX=$this->corrigeNumero($this->datadehoje,"dataiso");
        
        $this->programarentrega="0"; // cliente leva agora
        $this->futura="0"; // baixar estoque agora
        if($this->radiobutton_programarentrega->get_active()){
			$this->programarentrega="1"; // programa entrega
			if($this->checkbutton_baixafutura->get_active()){
				$this->futura="1"; // baixar estoque ao entrear	
			}        	
        }

        $desconto=$totalmerc-$totalnf;

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->confin=new $BancoDeDados;
        $this->confin->Connect();
        $this->codcliAUX=$this->entry_codcli->get_text();

        $horafinal=date("H:i:s");
        $codendereco=$this->label_codendereco->get_text();
		$variacao=$this->pegaNumero($this->label_variacao);
		if($operacao=="orcamento"){
			$sql="\nINSERT INTO orcamento (data, codcli, endereco, desconto, totalmerc, totalnf, vendedor, finalizado, variacao, hora) VALUES ('$this->hojeAUX', '$this->codcliAUX', '$codendereco', '$desconto','$totalmerc','$totalnf', $this->codvendedorAUX, 'N', '$variacao', '$horafinal');";
		}else{
        	$sql="\nINSERT INTO saidas (data, codcli, endereco, desconto, totalmerc, totalnf, vendedor, hora, futura) VALUES ('$this->hojeAUX', '$this->codcliAUX', '$codendereco', '$desconto', '$totalmerc','$totalnf', $this->codvendedorAUX,'$horafinal', '$this->futura');";
		}
        $this->codentsaiAUX=$this->confin->QueryLastCod($sql);
		$this->liststore_venda->foreach(array($this,'finalizavenda2AUXlistvenda'),$operacao);

        // porcentagem da comissao total
		if($this->codvendedorAUX and $operacao<>"orcamento"){
			$sql="SELECT comissao FROM funcionarios WHERE codigo=$this->codvendedorAUX";
			$resultado=$this->confin->Query($sql);
			$resultado2=$this->confin->FetchArray($resultado);
			$comissao=$resultado2[0];
			// calcula comissao
			$minhacomissao=$this->comissaototal/100*$comissao;
			// calcula porcentagem referente ao totalnf
			$this->porcentagemfinalAUX=$minhacomissao*100/$totalnf;
		}

        
        $this->data_cAUX=$this->corrigeNumero($this->datadehoje,"dataiso");
        
        // registra campo ultima venda do cliente
        $this->confin->Query("\nUPDATE clientes SET ultvenda='$this->data_cAUX' WHERE codigo='$this->codcliAUX' ");
        
		// le lista de pagamentos
		$this->liststore_prazo->foreach(array($this,'finalizavenda2AUXlistprazo'),$operacao);

        // se for orcamento grava que foi concretizado aquele orcamento
        $codorca=$this->entry_codorca->get_text();
        if(!empty($codorca)){
            $this->sqltotal.="\nUPDATE orcamento SET finalizado='S', datafinalizado='$this->data_cAUX' WHERE codorcamento='$codorca';" ;
            //$con->Query($sql);
        }

        if(!$this->confin->Query($this->sqltotal,true,null,true)){
        //if(!$this->confin->Query($this->sqltotal)){
            msg('Erro SQL');
			return;
        }

        $this->confin->Disconnect();
        $this->hidefinalizar();
        $this->limpa();
        //confirma(array(&$this,imprimirRecibo),'Deseja imprimir uma via do recibo?',$codentsai,"saidas");
		if($operacao=="orcamento"){
			$this->ConfirmaImprimirRecibo($this->codentsaiAUX,"orcamento");
		}else{
          	$this->ConfirmaImprimirRecibo($this->codentsaiAUX,"saidas");
		}
    }

	function finalizavenda2AUXlistvenda($store, $path, $iter, $operacao="orcamento"){
		$codmerc=$this->liststore_venda->get_value($iter,0);		
		$quantidade=$this->liststore_venda->get_value($iter,2);
		$preco_unitario=$this->pegaNumero($this->liststore_venda->get_value($iter,4));
		$precocomdesconto=$this->pegaNumero($this->liststore_venda->get_value($iter,5));
		$preco_total=$this->pegaNumero($this->liststore_venda->get_value($iter,6));

		if($operacao=="orcamento"){
			$this->sqltotal.="\nINSERT INTO entsai (codentsai, tipo, codmerc, precooriginal, precocomdesconto, quantidade) VALUES ('$this->codentsaiAUX', 'O', '$codmerc', '$preco_unitario', '$precocomdesconto', '$quantidade');";
		}else{
			// pega preco original da mercadoria
			$sql="SELECT precovenda, comissaomaxima, comissionada, precocusto, estoqueatual FROM mercadorias WHERE codmerc='$codmerc'";
			$resultado=$this->confin->Query($sql);
			$resultado2=$this->confin->FetchArray($resultado);
			$precooriginal=$resultado2[0];
			$precocusto=$resultado2[3];
			$estoqueatual=$resultado2[4];;
			
			// se for comissionada
			$comissionada=$resultado2[2];
			if($comissionada=='S'){
				$comissaomercadoria=$resultado2[1];
				$this->comissaototalAUX+=$preco_total/100*$comissaomercadoria;
			}
			if($this->programarentrega=="1"){
				$entregue="0"; // entrega programada
			}else{
				$entregue=$quantidade; // cliente leva agora
			}
			// grava itens na tabela de entradas/saidas/orcamentos
			$this->sqltotal.="\nINSERT INTO entsai (codentsai, tipo, codmerc, precooriginal, precocomdesconto, quantidade, precocusto, entregue) VALUES ('$this->codentsaiAUX', 'S', '$codmerc', '$precooriginal', '$precocomdesconto', '$quantidade', '$precocusto', '$entregue');";

			// baixa o estoque atual
			if($this->futura=="0"){ // se for para baixar estoque agora
				$estoqueatual-=$quantidade;
				$this->sqltotal.="\nUPDATE mercadorias SET estoqueatual=$estoqueatual, ultimavenda='$this->hojeAUX' WHERE codmerc=$codmerc;";
			}

			$this->totalDaLista+=$preco_total;
		}
	}

	function finalizavenda2AUXlistprazo($store, $path, $iter, $operacao){
		$vencimento=$this->corrigeNumero($this->liststore_prazo->get_value($iter,3),"dataiso");
		$valor=$this->pegaNumero($this->liststore_prazo->get_value($iter,2));
		$tipo=$this->liststore_prazo->get_value($iter,1);
		$nnf="P".$this->liststore_prazo->get_value($iter,0)."C".$this->codentsai;
		$codmeiopgto=$this->liststore_prazo->get_value($iter,0);
		//$placonvendas=$this->retornabusca4('codplacon','parcelapgto','codigoparcelapgto',$this->liststore_prazo->get_value($iter,0));
		$placonvendas=$this->retornabusca4('codplacon','meiopgto','codigo',$this->liststore_prazo->get_value($iter,0));
		$tmp=$this->liststore_prazo->get_value($iter,0);


		//grava no contas a receber
		// se integração estoque-financeiro estiver ativada
		if($this->retorna_OPCAO('integraestoquefinanceiro') and $operacao<>"orcamento"){
			$this->sqltotal.="\nINSERT INTO receber (fiscal, data_c, data_v, valor, saldo, descr, codorigem, codplacon, obs, vendedor, comissao, codsaidas) ";
			$this->sqltotal.="VALUES ('P$nnf', '$this->hojeAUX', '$vencimento', '$valor', '$valor', 'VENDA EM $tipo', $this->codcliAUX, '$placonvendas', 'CODIGO $this->codentsaiAUX',$this->codvendedorAUX, $this->porcentagemfinalAUX, $this->codentsaiAUX);";
		}

		// grava tipos de pagamentos
		if($operacao=="orcamento"){
			$tmpT="O";
		}else{
			$tmpT="S";
		}
		$this->sqltotal.="\nINSERT INTO movpagamentos (codorigem, tipo, nnf, codmeiopgto, meio, valor, data, data_c, codformapgto) VALUES ('$this->codentsaiAUX','$tmpT','$nnf', '$codmeiopgto', '$tipo', '$valor', '$vencimento', '$this->data_cAUX', '$this->codformapgto');";
	}

}

?>