<?php
/*
Observacoes
1 Se for RECEBER
  * muda busca para cliente
  1.1 Se for CAIXA
  	* procura por caixa
    1.1.1 Se for CHEQUE
      ? vai cadastrar no controle de cheques
      * mostra entry data bom para do cheque
      ? procura banco pelo banco cadastrado filtrando pelo cliente.
      * mostra banco
    1.1.2 Se for OUTRO DOC.
      * esconde data bom para cheque
      * esconde banco
  1.2 Se for BANCO
  	  * procura por conta de banco SEM filtrar pelo cliente na parte de ContaCusto
2 Se for PAGAR
  * muda busca para fornecedor
*/
class movcontas extends funcoes {
    function movcontas($moduloMOVFIN="desativado") {
        $this->pegaentry();
        if($moduloMOVFIN=="ativado") {
            $this->janela->show();
            $this->janela->fullscreen();
            $this->janela->connect_simple('destroy', array('gtk','main_quit'));
            $this->button_fechar_movfinanceiro->connect_simple('clicked', array('gtk','main_quit'));
        }else {
            global $atalho_padrao;
            $this->button_fechar_movfinanceiro->hide();
            $this->button_quitar->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F12, 	0, Gtk::ACCEL_VISIBLE);
            $this->button_mult_aplicar->add_accelerator('activate', $atalho_padrao, Gdk::KEY_F9, 	0, Gtk::ACCEL_VISIBLE);
        }

    }
    function pegaentry() {
        $this->xml=$this->carregaGlade("movcontas");
        $this->button_fechar_movfinanceiro=$this->xml->get_widget("button_fechar_movfinanceiro");
        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        $this->scrolledwindow_pagamentos=$this->xml->get_widget('scrolledwindow_pagamentos');
        $this->liststore_pagamentos=new GtkListStore(
                Gobject::TYPE_STRING, //0 codigo do movimento mpr
                Gobject::TYPE_STRING, //1 tipo C ou B
                Gobject::TYPE_STRING, //2 codigo do caixa ou do banco
                Gobject::TYPE_STRING, //3 data do pagamento
                Gobject::TYPE_STRING, //4 valor nominal
                Gobject::TYPE_STRING, //5 desconto
                Gobject::TYPE_STRING, //6 multa
                Gobject::TYPE_STRING, //7 juros
                Gobject::TYPE_STRING, //8 valor total
                Gobject::TYPE_STRING, //9 tipo do doc C ou O cheque ou outro ou E de Estorno
                Gobject::TYPE_STRING, //10num doc ou do cheque
                Gobject::TYPE_STRING, //11 cod do banco do cheque
                Gobject::TYPE_STRING, //12 data de bom para do cheque
                Gobject::TYPE_STRING //13 historico

        );
        $this->treeview_pagamentos=new GtkTreeView($this->liststore_pagamentos);
        $this->treeview_pagamentos->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
                array('Codigo', 'Tipo(C/B)', 'Cod(C/B)', 'Data.Pg', 'Valor Nominal', 'Desconto', 'Multa', 'Juros', 'Valor Total', 'Doc(Ch/O/E)', 'Num.Doc', 'Banco(Ch)', 'Data(Ch)','Historico'),
                $this->treeview_pagamentos
        );

        $this->scrolledwindow_pagamentos->add($this->treeview_pagamentos);
        $this->scrolledwindow_pagamentos->show_all();

        // lista de contas
        $this->scrolledwindow_mult=$this->xml->get_widget('scrolledwindow_mult');
        $this->liststore_mult=new GtkListStore(
                Gobject::TYPE_STRING, //0 cod da conta
                Gobject::TYPE_STRING, //1 valor nominal
                Gobject::TYPE_STRING, //2 desconto
                Gobject::TYPE_STRING, //3 multa
                Gobject::TYPE_STRING, //4 juros
                Gobject::TYPE_STRING, //5 valor final
                Gobject::TYPE_STRING  //6 saldo da conta

        );
        $this->treeview_mult=new GtkTreeView($this->liststore_mult);
        $this->treeview_mult->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
                array('Cod.Conta', 'Valor Nominal', 'Desconto', 'Multa', 'Juros', 'Valor Final', 'Saldo'),
                $this->treeview_mult
        );
        $this->scrolledwindow_mult->add($this->treeview_mult);
        $this->scrolledwindow_mult->show_all();

        //$this->clist_pagamentos->connect('select-row',array($this,'clicaPagamentos'));
// aba cobranca
        $this->checkbutton_buscasaldozero=$this->xml->get_widget('checkbutton_buscasaldozero');
        $this->checkbutton_buscasaldozero->set_active(false);
        $this->checkbutton_buscasaldozero->connect_simple('toggled',array($this,'connectEntryEscolhaConta'));

        $this->checkbutton_nome=$this->xml->get_widget('checkbutton_nome');
        $this->checkbutton_nome->set_active(true);
        $this->checkbutton_nome->connect_simple('toggled',array($this,'connectEntryEscolhaConta'));

        $this->checkbutton_hoje=$this->xml->get_widget('checkbutton_hoje');
        $this->checkbutton_hoje->set_active(false);
        $this->checkbutton_hoje->connect_simple('toggled',array($this,'connectEntryEscolhaConta'));


        $this->vbox_mult=$this->xml->get_widget('vbox_mult');
        $this->vbox_mult->hide();
        $this->checkbutton_contas_mult=$this->xml->get_widget('checkbutton_contas_mult');
        $this->checkbutton_contas_mult->set_active(false);
        $this->checkbutton_contas_mult->connect_simple('toggled',array($this,'show_contas_mult'));

        $this->button_mult_aplicar=$this->xml->get_widget('button_mult_aplicar');
        $this->button_mult_aplicar->connect_simple('clicked',array($this,'add_lista_mult'));

        $this->button_mult_excluir=$this->xml->get_widget('button_mult_excluir');
        $this->button_mult_excluir->connect_simple('clicked',array($this,'excluir_lista_mult'));

        $this->button_sangria=$this->xml->get_widget('button_sangria');
        $this->button_sangria->connect_simple('clicked', array($this,'chamaSangria'));

        $this->button_suprimento=$this->xml->get_widget('button_suprimento');
        $this->button_suprimento->connect_simple('clicked', array($this,'chamaSuprimento'));

        $this->entry_documento=$this->xml->get_widget('entry_documento');

        $this->label_valorfinal=$this->xml->get_widget('label_valorfinal');
        $this->label_mult_valorfinal=$this->xml->get_widget('label_mult_valorfinal');

        $this->entry_multa=$this->xml->get_widget('entry_multa');
        $this->entry_multa->connect('key-press-event', array($this,'mascaraNew'),'porcento2');
        $this->entry_multa->connect('key-press-event', array($this,'calculaValorFinal'),true,false);
        $this->entry_multa->set_sensitive($this->verificaPermissao('030907',false));

        $this->entry_mult_multa=$this->xml->get_widget('entry_mult_multa');
        /*$this->entry_mult_multa->connect('key-press-event', array($this,'mascaraNew'),'porcento2');
        $this->entry_mult_multa->connect('key-press-event', array($this,'calculaValorFinal'),true,true);
        $this->entry_mult_multa->set_sensitive($this->verificaPermissao('030907',false)); */
        $this->entry_mult_multa->set_sensitive(false);

        /*        $this->entry_codbanco=$this->xml->get_widget('entry_codbanco');
        $this->label_codbanco=$this->xml->get_widget('label_codbanco');
        $this->entry_codbanco->connect_simple('key-release-event',array($this,mostraBanco),$this->label_codbanco, $this->entry_codbanco);
		$this->entry_codbanco->connect_simple('focus-out-event',array($this,mostraBanco),$this->label_codbanco, $this->entry_codbanco);
        */

        $this->entry_historico=$this->xml->get_widget('entry_historico');

        $this->entry_juros=$this->xml->get_widget('entry_juros');
        $this->entry_juros->connect_after("key-press-event", array($this, "intro_keypressed"),$this->entry_documento, $this->entry_multa);
        $this->entry_juros->connect('key-press-event', array($this,'mascaraNew'),'porcento2');
        $this->entry_juros->connect_simple('key-press-event', array($this,'calculaValorFinal'),true,false);
        $this->entry_juros->set_sensitive($this->verificaPermissao('030908',false));

        $this->entry_mult_juros=$this->xml->get_widget('entry_mult_juros');
        /*$this->entry_mult_juros->connect_after("key-press-event", array($this, "intro_keypressed"),$this->entry_documento, $this->entry_multa);
        $this->entry_mult_juros->connect('key-press-event', array($this,'mascaraNew'),'porcento2');
        $this->entry_mult_juros->connect_simple('key-press-event', array($this,'calculaValorFinal'),true,true);
        $this->entry_mult_juros->set_sensitive($this->verificaPermissao('030908',false));
        */
        $this->entry_mult_juros->set_sensitive(false);

        $this->hbox_cheque=$this->xml->get_widget('hbox_cheque');
        $this->entry_cheque_codigo=$this->xml->get_widget('entry_cheque_codigo');
        $this->entry_cheque_codigo->connect('key_press_event',
                array($this,entry_enter),
                'SELECT x.codigo, x.titular, nb.nome, x.agencia, x.conta, x.dataemissao, x.bompara, x.numero, x.valor, clientes.codigo AS codcli, clientes.nome AS cliente, fornecedores.codigo AS codfor, fornecedores.nome AS fornecedor, x.obs FROM cheque AS x LEFT JOIN clientes ON (x.codcliente=clientes.codigo) LEFT JOIN fornecedores ON (x.codfornecedor=fornecedores.codigo) LEFT JOIN nomebanco AS nb ON (nb.codigo=x.codbanco)',
                true,
                $this->entry_cheque_codigo,
                null,
                "cheque",
                "titular",
                "codigo"
        );
        $this->entry_cheque_codigo->connect_simple('key_release_event',array($this,'consulta_cheque_cadastrado'));
        $this->entry_cheque_codigo->connect_simple('focus-out-event',array($this,'consulta_cheque_cadastrado'));

        $this->entry_cheque_banco=$this->xml->get_widget('entry_cheque_banco');
        $this->entry_cheque_agencia=$this->xml->get_widget('entry_cheque_agencia');
        $this->entry_cheque_conta=$this->xml->get_widget('entry_cheque_conta');
        $this->entry_cheque_numero=$this->xml->get_widget('entry_cheque_numero');
        $this->entry_cheque_bompara=$this->xml->get_widget('entry_cheque_bompara');
        $this->entry_cheque_bompara->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_cheque_titular=$this->xml->get_widget('entry_cheque_titular');
        $this->entry_cheque_documento=$this->xml->get_widget('entry_cheque_documento');
        $this->entry_cheque_obs=$this->xml->get_widget('entry_cheque_obs');


        $this->label_cadcaixa=$this->xml->get_widget('label_cadcaixa');
        $this->entry_cadcaixa=$this->xml->get_widget('entry_cadcaixa');
        $this->entry_cadcaixa->connect_simple('key-release-event',array($this,mostraBanco),$this->label_cadcaixa, $this->entry_cadcaixa);
        $this->entry_cadcaixa->connect_simple('focus-out-event',array($this,'mostraBanco'),$this->label_cadcaixa, $this->entry_cadcaixa);

        //$this->label_tipobanco=$this->xml->get_widget('label_tipobanco');



        $this->label_nomeclifor=$this->xml->get_widget('label_nomeclifor');
        $this->label_codclifor=$this->xml->get_widget('label_codclifor');
        $this->labelclifor=$this->xml->get_widget('labelclifor');
        $this->label_saldo=$this->xml->get_widget('label_saldo');
        $this->label_mult_saldo=$this->xml->get_widget('label_mult_saldo');

        $this->entry_desconto=$this->xml->get_widget('entry_desconto');
        $this->entry_desconto->set_sensitive($this->verificaPermissao('030910',false));
        $this->entry_desconto->connect('key-press-event', array($this,'mascaraNew'),'porcento2');
        $this->entry_desconto->connect_simple('key-press-event', array($this,'calculaValorFinal'),true,false);

        $this->entry_mult_desconto=$this->xml->get_widget('entry_mult_desconto');
        /*$this->entry_mult_desconto->set_sensitive($this->verificaPermissao('030910',false));
        $this->entry_mult_desconto->connect('key-press-event', array($this,'mascaraNew'),'porcento2');
        $this->entry_mult_desconto->connect_simple('key-press-event', array($this,'calculaValorFinal'),true,true);*/
        $this->entry_mult_desconto->set_sensitive(false);

        $this->entry_valornominal=$this->xml->get_widget('entry_valornominal');
        $this->entry_valornominal->connect('key-press-event', array($this,'mascaraNew'),'virgula2');
        $this->entry_valornominal->connect_simple('key-press-event', array($this,'calculaValorFinal'),true,true);

        $this->entry_mult_valornominal=$this->xml->get_widget('entry_mult_valornominal');
        $this->entry_mult_valornominal->connect('key-press-event', array($this,'mascaraNew'),'virgula2');
        $this->entry_mult_valornominal->connect_simple('key-press-event', array($this,'calculaValorFinal'),true,true);

        $this->entry_escolhaconta=$this->xml->get_widget('entry_escolhaconta');
        $this->entry_escolhaconta->connect("key-press-event", array($this, "intro_keypressed"),$this->entry_cadcaixa);
        $this->entry_escolhaconta->connect('focus-out-event',array($this,'focusoutEntryEscolhaConta'));
        $this->entry_escolhaconta->connect('focus-in-event',array($this,'focusoutEntryEscolhaConta'));
        $this->entry_escolhaconta->connect('key-release-event',array($this,'focusoutEntryEscolhaConta'));
        $this->label_escolhaconta=$this->xml->get_widget('label_escolhaconta');

        $this->radiobutton_pagar=$this->xml->get_widget('radiobutton_pagar');
        $this->radiobutton_pagar->connect_simple('toggled',array($this,'radiobuttonPagar'));
        $this->radiobutton_pagar->set_sensitive($this->verificaPermissao('030902',false));
        $this->radiobutton_receber=$this->xml->get_widget('radiobutton_receber');
        $this->radiobutton_receber->connect_simple('toggled',array($this,'radiobuttonReceber'));
        $this->radiobutton_receber->set_active(1);
        $this->radiobutton_receber->set_sensitive($this->verificaPermissao('030903',false));
        $this->radiobuttonReceber();


        $this->label_codigompr=$this->xml->get_widget('label_codigompr');
        $this->radiobutton_banco=$this->xml->get_widget('radiobutton_banco');
        $this->radiobutton_banco->set_sensitive($this->verificaPermissao('030905',false));
        $this->radiobutton_caixa=$this->xml->get_widget('radiobutton_caixa');
        $this->radiobutton_caixa->connect_simple('toggled',array($this,'radiobuttonCaixa'));
        $this->radiobutton_caixa->set_active(1);
        $this->radiobutton_caixa->set_sensitive($this->verificaPermissao('030904',false));
        $this->radiobuttonCaixa();

        $this->radiobutton_cheque=$this->xml->get_widget('radiobutton_cheque');
        $this->radiobutton_cheque->connect_simple('toggled',array($this,'radiobuttonCheque'));
        $this->radiobutton_outrodoc=$this->xml->get_widget('radiobutton_outrodoc');
        $this->radiobutton_outrodoc->set_active(1);

        $this->button_quitar=$this->xml->get_widget('button_quitar');
        $this->button_cancelar=$this->xml->get_widget('button_cancelar');
        $this->button_limpar=$this->xml->get_widget('button_limpar');

        $this->button_quitar->connect_simple('clicked', confirma, array(&$this, 'func_quitar'),'Deseja lancar pagamentos nessa conta ?',true);
        $this->button_quitar->set_sensitive($this->verificaPermissao('030909',false));
        $this->button_cancelar->connect_simple('clicked', confirma, array(&$this, 'func_CancelaPG'),'Deseja cancelar este pagamento?',false);
        $this->button_cancelar->set_sensitive($this->verificaPermissao('030906',false));
        $this->button_limpar->connect_simple('clicked', confirma, array(&$this, 'func_limparPG'),'Deseja limpar os campos desta tela de pagamento?',false);



        $this->func_novo();
        $this->entry_escolhaconta->grab_focus();
    }

    function show_contas_mult() {
        if($this->checkbutton_contas_mult->get_active()) {
            $this->vbox_mult->show();
        }else {
            $this->vbox_mult->hide();
        }
    }

    function consulta_cheque_cadastrado() {
        $cheque_codigo=$this->entry_cheque_codigo->get_text();
        if(empty($cheque_codigo)) {
            //permite digitacao nos campos do cheque
            $this->campos_cheque_sensitive(true);
        }else {
            // NAO permite digitacao nos campos do cheque
            $this->campos_cheque_sensitive(false);
            // busca dados de cheque cadastrado
            $cheque_codigo=$this->pegaNumero($cheque_codigo);
            if($codigo=$this->retornabusca4('codigo','cheque','codigo',$cheque_codigo)) {
                // se achou cadastro do cheque bota dados na tela
                $this->entry_cheque_banco->set_text($this->retornabusca4('codbanco','cheque','codigo',$codigo));
                $this->entry_cheque_agencia->set_text($this->retornabusca4('agencia','cheque','codigo',$codigo));
                $this->entry_cheque_conta->set_text($this->retornabusca4('conta','cheque','codigo',$codigo));
                $this->entry_cheque_numero->set_text($this->retornabusca4('numero','cheque','codigo',$codigo));
                $this->entry_cheque_bompara->set_text($this->corrigeNumero($this->retornabusca4('bompara','cheque','codigo',$codigo),'data'));
                $this->entry_cheque_titular->set_text($this->retornabusca4('titular','cheque','codigo',$codigo));
                $this->entry_cheque_documento->set_text($this->retornabusca4('documento','cheque','codigo',$codigo));
                $this->entry_cheque_obs->set_text($this->retornabusca4('obs','cheque','codigo',$codigo));
            }else {
                // se não achou cadastro do cheque limpa dados da tela
                $this->func_limpa_cheque(false);
            }
        }
        return false;
    }

    function campos_cheque_sensitive($bool) {
        $this->entry_cheque_banco->set_sensitive($bool);
        $this->entry_cheque_agencia->set_sensitive($bool);
        $this->entry_cheque_conta->set_sensitive($bool);
        $this->entry_cheque_numero->set_sensitive($bool);
        $this->entry_cheque_bompara->set_sensitive($bool);
        $this->entry_cheque_titular->set_sensitive($bool);
        $this->entry_cheque_documento->set_sensitive($bool);
        $this->entry_cheque_obs->set_sensitive($bool);
    }

    function chamaSangria() {
        include_once('movimenta'.bar.'sangria.php');
        if($this->radiobutton_caixa->get_active()) {
            if(!$this->perguntaPermissao('030911')) return;
            //if(!$this->sangria_caixa){
            $this->sangria_caixa=new sangria('sangria','Sangria do Caixa','caixa');
            //}
            $this->sangria_caixa->sangriaShow();
        }else {
            //if(!$this->perguntaPermissao('030911')) return;
            //if(!$this->sangria_banco){
            $this->sangria_banco=new sangria('sangria','Sangria do Banco','movbanc');
            //}
            $this->sangria_banco->sangriaShow();
        }

    }
    function chamaSuprimento() {
        include_once('movimenta'.bar.'sangria.php');
        if($this->radiobutton_caixa->get_active()) {
            if(!$this->perguntaPermissao('030912')) return;
            //if(!$this->suprimento_caixa){
            $this->suprimento_caixa=new sangria('suprimento','Suprimento do Caixa','caixa');
            //}
            $this->suprimento_caixa->sangriaShow();
        }else {
            //if(!$this->perguntaPermissao('030912')) return;
            //if(!$this->suprimento_banco){
            $this->suprimento_banco=new sangria('suprimento','Suprimento do Banco','movbanc');
            //}
            $this->suprimento_banco->sangriaShow();
        }
    }

    function radiobuttonCaixa() {
        $this->entry_cadcaixa->grab_focus();
        if($this->radiobutton_caixa->get_active()) { // se for caixa
            $this->label_cadcaixa->set_text('');
            //$this->entry_cadcaixa->set_text('');
            if($this->lastConnectEntryCadcaixa1) {
                $this->entry_cadcaixa->disconnect($this->lastConnectEntryCadcaixa1);
            }
            $this->lastConnectEntryCadcaixa1=$this->entry_cadcaixa->connect(
                    'key-press-event',
                    array($this,'entry_enter'),
                    'SELECT codigo, descricao FROM cadcaixa',
                    true,
                    $this->entry_cadcaixa,
                    $this->label_cadcaixa,
                    'cadcaixa',
                    "descricao",
                    "codigo"
            );
            if($this->lastConnectEntryCadcaixa2) {
                @$this->entry_cadcaixa->disconnect($this->lastConnectEntryCadcaixa2);
            }
            $this->lastConnectEntryCadcaixa2=$this->entry_cadcaixa->connect_simple(
                    'focus-out-event',
                    array($this,retornabusca22),
                    'cadcaixa',
                    $this->entry_cadcaixa,
                    $this->label_cadcaixa,
                    'codigo',
                    'descricao'
            );
            $this->historico_bancocaixa="CAIXA";
        }else { // se for banco
            $this->label_cadcaixa->set_text('');
            //$this->entry_cadcaixa->set_text('');
            if($this->lastConnectEntryCadcaixa1) {
                $this->entry_cadcaixa->disconnect($this->lastConnectEntryCadcaixa1);
            }
            $this->lastConnectEntryCadcaixa1=$this->entry_cadcaixa->connect(
                    'key-press-event',
                    array($this,'entry_enter'),
                    //"SELECT * FROM bancos WHERE contadaempresa='1'",
                    "SELECT b.codbanco, b.titular, nb.nome, b.agencia, b.conta FROM bancos AS b INNER JOIN nomebanco AS nb ON nb.codigo=b.numero WHERE contadaempresa='1'",
                    true,
                    $this->entry_cadcaixa,
                    $this->label_cadcaixa,
                    'bancos',
                    "numero",
                    "codbanco"
            );
            if($this->lastConnectEntryCadcaixa2) {
                $this->entry_cadcaixa->disconnect($this->lastConnectEntryCadcaixa2);
                $this->lastConnectEntryCadcaixa2="";
            }
            $this->historico_bancocaixa="BANCO";
        }
    }

    function radiobuttonCheque() {
        $this->entry_documento->grab_focus();
        if($this->radiobutton_cheque->get_active()) { // se for cheque
            $this->historico_documento="CHEQUE";
            $this->hbox_cheque->show();
            $this->entry_documento->set_sensitive(false);
            $this->entry_cheque_banco->grab_focus();
            //$this->frame_datacheque->show();
            //$this->frame_bancocheque->show();
        }else { // se for outro documento
            $this->historico_documento="OUTRO DOC.";
            $this->hbox_cheque->hide();
            $this->entry_documento->set_sensitive(true);
            $this->entry_documento->grab_focus();
            //$this->frame_bancocheque->hide();
            //$this->frame_datacheque->hide();
        }
    }

    function focusoutEntryEscolhaConta() {
        $codigo=$this->DeixaSoNumero($this->entry_escolhaconta->get_text());
        if(!empty($codigo)) {
            $this->func_novo2();
            $this->func_novo3();
            $codclifor=$this->retornabusca4('codorigem',$this->escolhaconta,'codigo',$codigo);
            $nomeclifor=$this->retornabusca4('nome',$this->escolhaconta2,'codigo',$codclifor);
            $this->codplacon2=$this->retornabusca4('codplacon',$this->escolhaconta,'codigo',$codigo);
            $saldo=$this->retornabusca4('saldo',$this->escolhaconta,'codigo',$codigo);
            $descr=$this->retornabusca4('descr',$this->escolhaconta,'codigo',$codigo);

            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();
            if($this->escolhaconta=="receber") {
                $EouS="E";
            }elseif($this->escolhaconta=="pagar") {
                $EouS="S";
            }
            $sql="SELECT codigompr, codmovim, tipomovim, codcadcaixa, formamovim,  data_c, valor, desconto, multa, juros, tipodoc, numdoc, codbancocheque, datacheque, historico FROM movimentos WHERE (codmovim='$codigo' AND formamovim='$EouS') ORDER BY codigompr ";
            $resultado=$con->Query($sql);
            while($i = $con->FetchRow($resultado)) {
                $this->liststore_pagamentos->append(
                        array(
                        //$i[0],
                        $i[0],
                        $i[2],
                        $i[3],
                        $this->corrigeNumero($i[5],'data'),
                        $this->mascara2($i[6],'virgula2'),
                        $this->mascara2($i[7],'virgula2'),
                        $this->mascara2($i[8],'virgula2'),
                        $this->mascara2($i[9],'virgula2'),
                        $this->mascara2($i[6]+$i[8]+$i[9]-$i[7],'virgula2'),
                        $i[10],
                        $i[11],
                        $i[12],
                        $i[13],
                        $i[14]
                        )
                );
            }
        }
        $this->label_codclifor->set_text($codclifor);
        $this->label_nomeclifor->set_text($nomeclifor);
        $this->label_saldo->set_text($this->mascara2($saldo,'virgula2'));
        $this->entry_valornominal->set_text($this->mascara2($saldo,'virgula2'));
        $this->label_escolhaconta->set_text($descr);
        //$this->entry_documento->set_text($documento);
        $this->calculaValorFinal();
        $this->soma_lista_mult();
    }

    function radiobuttonReceber() {
        if($this->radiobutton_receber->get_active()) {
            $this->escolhaconta="receber";
            $this->escolhaconta2="clientes";
            $this->entry_escolhaconta->grab_focus();
            $this->connectEntryEscolhaConta();
            $this->labelclifor->set_text('Clientes');
            $this->focusoutEntryEscolhaConta();
        }
    }
    function radiobuttonPagar() {
        if($this->radiobutton_pagar->get_active()) {
            $this->escolhaconta="pagar";
            $this->escolhaconta2="fornecedores";
            $this->entry_escolhaconta->grab_focus();
            $this->connectEntryEscolhaConta();
            $this->labelclifor->set_text('Fornecedores');
            $this->focusoutEntryEscolhaConta();
        }
    }
    function connectEntryEscolhaConta() {
        // funcao que faz a lista de contas a buscar
        if($this->checkbutton_buscasaldozero->get_active()) { // se a opcao busca saldo=zero tiver marcada
            $sql_where="";
        }else {
            $sql_where=" AND c.saldo>0 ";
        }

        // verifica se busca apenas por contas de hoje
        if($this->checkbutton_hoje->get_active()) {
            $hojeiso=$this->anodehoje."-".$this->mesdehoje."-".$this->diadehoje;
            $sql_where.=" AND c.data_v='$hojeiso' ";
        }else {
            $sql_where.="";
        }

        // ordem de nome ou codigo de venda
        if($this->checkbutton_nome->get_active()) {
            $sql_order=" o.nome, c.data_v ";
            $sql_codsaidas="";
        }else {
            $sql_order=" c.codsaidas ";
            $sql_codsaidas=" c.codsaidas, ";
        }

        if($this->lastConnectEscolhaConta) {
            $this->entry_escolhaconta->disconnect($this->lastConnectEscolhaConta);
        }
        $this->lastConnectEscolhaConta= $this->entry_escolhaconta->connect('key_press_event',
                array($this,'entry_enter'),
                "SELECT c.codigo, ".$sql_codsaidas." o.nome, c.data_v, c.valor, c.saldo, c.descr, c.obs FROM ".$this->escolhaconta." AS c LEFT JOIN ".$this->escolhaconta2." as o ON (c.codorigem=o.codigo) WHERE c.codigo>0 ".$sql_where." ORDER BY ".$sql_order,
                true,
                $this->entry_escolhaconta,
                $this->label_escolhaconta,
                $this->escolhaconta,
                "codorigem",
                "codigo"
        );
    }

    function func_novo() {
        $this->radiobutton_caixa->set_active(true);
        $this->radiobutton_outrodoc->set_active(true);
        $this->entry_escolhaconta->set_text('');
    }
    function func_novo2() {
        $this->label_saldo->set_text('');
        $this->entry_valornominal->set_text('');
        $this->label_valorfinal->set_text('');
        $this->entry_documento->set_text('');
        $this->entry_historico->set_text('');
        $this->func_limpa_cheque();
        //$this->func_limpa_mult();
    }
    function func_novo4() {
        $this->entry_desconto->set_text('');
        $this->entry_multa->set_text('');
        $this->entry_juros->set_text('');
    }
    function func_limpa_mult() {
        $this->label_mult_saldo->set_text('');
        $this->entry_mult_valornominal->set_text('');
        $this->entry_mult_desconto->set_text('');
        $this->entry_mult_multa->set_text('');
        $this->entry_mult_juros->set_text('');
        $this->label_mult_valorfinal->set_text('');
    }
    function func_limpa_cheque($codigo=true) {
        if($codigo) $this->entry_cheque_codigo->set_text('');
        $this->entry_cheque_banco->set_text('');
        $this->entry_cheque_agencia->set_text('');
        $this->entry_cheque_conta->set_text('');
        $this->entry_cheque_numero->set_text('');
        $this->entry_cheque_bompara->set_text('');
        $this->entry_cheque_titular->set_text('');
        $this->entry_cheque_documento->set_text('');
        $this->entry_cheque_obs->set_text('');
    }
    function func_novo3() {
        $this->liststore_pagamentos->clear();
    }
    function func_limpa_mult2() {
        $this->liststore_mult->clear();
    }

    /*function aplicar_descontos(){
    	$valornominal=$this->getValorNominal(true);
		$desconto=$this->getDesconto(true);
		$multa=$this->getMulta(true);
		$juros=$this->getJuros(true);
		$valorfinal=$valornominal+$multa+$juros-$desconto;
        $saldo=$this->pegaNumero($this->label_mult_saldo);

		$linhas=$this->numero_rows_liststore($this->liststore_mult);
		$desconto_new=$desconto/$linhas;
		$desconto_sobra=$desconto_new-number_format($desconto_new,2);
		echo $desconto_sobra." new=".$desconto_new."\n"; 
    }*/

    function func_quitar() {
        $radiobanco=$this->radiobutton_banco->get_active();
        $codcadcaixa=$this->DeixaSoNumero($this->entry_cadcaixa->get_text());
        if ($radiobanco) {
            // verifique se existe o codigo do banco
            if (!$this->retornabusca2("bancos", $this->entry_cadcaixa, $this->label_cadcaixa, "codbanco", "numero")) {
                msg('Conta do Banco nao encontrada');
                $this->entry_cadcaixa->grab_focus();
                return;
            }
        } else {
            // verifique se existe o codigo do caixa
            if (!$this->retornabusca2("cadcaixa", $this->entry_cadcaixa, $this->label_cadcaixa, "codigo", "descricao")) {
                msg('Codigo do caixa nao encontrado.');
                $this->entry_cadcaixa->grab_focus();
                return;
            }
            // bloqueia se caixa tiver fechado
            if(!$this->VerificaAberturaDoCaixa($codcadcaixa,$this->corrigeNumero($this->datadehoje,"dataiso"))) {
                return;
            }
        }

        // se nao tiver nenhuma conta na lista adiciona a selecionada
        if($this->soma_valornominal==0) {
            $this->button_mult_aplicar->clicked();
        }
        $this->liststore_mult->foreach(array($this,'func_quitar2'));
        $this->func_limpa_mult();
        $this->func_limpa_mult2();
        $this->func_novo();
        $this->func_novo2();
        $this->func_novo4();
        $this->entry_escolhaconta->grab_focus();
        $this->status('Pagamento efetuado com sucesso');
    }

    //*****************************************
    // funcao QUITAR***************************
    //*****************************************
    function func_quitar2($store, $path, $iter) {

        $horafinal=date("H:i:s");

        $radiopagar=$this->radiobutton_pagar->get_active();
        if ($radiopagar) {
            // contas a pagar - saida de dinheiro
            $formamovim="S";
            $this->tabela="pagar";
        } else {
            // contas a receber - entrada de dinheiro
            $formamovim="E";
            $this->tabela="receber";
        }

        // pega o codigo da conta a pagar ou receber
        $codigo=$this->pegaNumero($this->liststore_mult->get_value($iter,0)); //$this->DeixaSoNumero($this->entry_escolhaconta->get_text());
        // plano de contas da conta a pagar ou receber
        $codplacon=$this->codplacon2;
        // data de pagamento
        $dtpag=$this->corrigeNumero($this->datadehoje,"dataiso");
        $codclifor=$this->retornabusca4('codorigem',$this->escolhaconta,'codigo',$codigo);

        $radiobanco=$this->radiobutton_banco->get_active();
        $codcadcaixa=$this->DeixaSoNumero($this->entry_cadcaixa->get_text());
        if ($radiobanco) {
            // para incluir lancamento no banco
            $tipomovim="B";
        } else {
            // para incluir lancamento no caixa
            $tipomovim="C";
        }

        $valornominal=$this->pegaNumero($this->liststore_mult->get_value($iter,1));		//$this->getValorNominal(true);
        /*if ($valornominal==0){
			msg('Valor Nominal deve ser diferente que zero (0).');
			$this->entry_mult_valornominal->grab_focus();
            return;
		}*/
        $desconto=$this->pegaNumero($this->liststore_mult->get_value($iter,2)); //$this->getDesconto(true);
        $multa=$this->pegaNumero($this->liststore_mult->get_value($iter,3)); //$this->getMulta(true);
        $juros=$this->pegaNumero($this->liststore_mult->get_value($iter,4)); //$this->getJuros(true);
        $valorfinal=$valornominal+$multa+$juros-$desconto;
        $docCheque=$this->radiobutton_cheque->get_active();
        $documento=$this->entry_documento->get_text();
        if($docCheque) {
            // tipo do documento: CHEQUE
            $tipodoc="C";
            // verifica se cheque ja esta cadastrado
            $cheque_codigo=$this->entry_cheque_codigo->get_text();
            if(!empty($cheque_codigo)) {
                // cheque deveria estar cadastrado.. checando...
                $cheque_codigo=$this->pegaNumero($cheque_codigo);
                if(!$this->retornabusca4('codigo','cheque','codigo',$cheque_codigo)) {
                    // cheque nao cadastrado
                    msg("Codigo do cheque nao encontrado.");
                    $this->entry_cheque_codigo->grab_focus();
                    return;
                }else {
                    $docChequeCadastrado=TRUE;
                    $valor_cheque=$this->retornabusca4('valor','cheque','codigo',$cheque_codigo);
                    if($valorfinal<>$valor_cheque) {
                        if(!confirma(false,"Valor do cheque difere do valor da conta. Deseja efetuar o pagamento mesmo assim?")) {
                            return;
                        }
                    }
                }
            }
            $cheque_banco=$this->pegaNumero($this->entry_cheque_banco);
            // verifique se existe o codigo do banco do cheque

            if(!$this->retornabusca4('codigo','nomebanco',codigo,$this->entry_cheque_banco->get_text())) {
                msg('Codigo do banco do cheque nao encontrado. Cadastre este banco em financeiro->instituicoes bancarias.');
                return;
            }
            $cheque_agencia=$this->pegaNumero($this->entry_cheque_agencia);
            $cheque_conta=$this->pegaNumero($this->entry_cheque_conta);
            $cheque_numero=$this->pegaNumero($this->entry_cheque_numero);
            $cheque_bompara=$this->entry_cheque_bompara->get_text();
            $cheque_titular=$this->entry_cheque_titular->get_text();
            $cheque_documento=$this->entry_cheque_documento->get_text();
            $cheque_obs=$this->entry_cheque_obs->get_text();

            if(!$docChequeCadastrado) {
                if(empty($cheque_agencia)) {
                    msg("Preencha o campo agencia do cheque");
                    $this->entry_cheque_agencia->grab_focus();
                    return;
                }

                if(empty($cheque_conta)) {
                    msg("Preencha o campo conta do cheque");
                    $this->entry_cheque_conta->grab_focus();
                    return;
                }

                if(empty($cheque_numero)) {
                    msg("Preencha o campo numero do cheque");
                    $this->entry_cheque_numero->grab_focus();
                    return;
                }

                if(empty($cheque_bompara) or $cheque_bompara=="00-00-0000" or !$this->valida_data($cheque_bompara)) {
                    msg("Data do cheque 'Bom Para' incorreta!");
                    $this->entry_cheque_bompara->grab_focus();
                    return;
                }else {
                    $cheque_bompara=$this->corrigeNumero($cheque_bompara,"dataiso");
                }

                if(empty($cheque_titular)) {
                    msg("Preencha o campo titular do cheque");
                    $this->entry_cheque_titular->grab_focus();
                    return;
                }

                if(empty($cheque_documento)) {
                    msg("Preencha o campo documento do cheque");
                    $this->entry_cheque_documento->grab_focus();
                    return;
                }
            }

        }else {
            // tipo do documento: OUTRO
            $tipodoc="O";
        }
        $historico=$this->entry_historico->get_text();


        // verifica se o valor nominal pago é maior que o saldo devedor
        $saldo=$this->pegaNumero($this->liststore_mult->get_value($iter,6)); //$this->pegaNumero($this->label_mult_saldo);

        // soma o valor do pagto parcial mais o saldo anterior
        $saldoatual=$saldo-$valornominal;

        // iniciando banco de dados
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();

        /*
        // lanca comissao se for tabela receber
        if($this->tabela=="receber"){
            // pega dados do vendedor
            $sql="SELECT vendedor,comissao FROM receber WHERE codigo=$codigo";
			$resultado=$con->Query($sql);
            $resultado2=$con->FetchArray($resultado);
			$vendedor=$resultado2["vendedor"];
            $comissao=$resultado2["comissao"];
            if(!empty($vendedor) and !empty($comissao)){
                // pega o valor em R$ da comissao a ser creditada
                $comissaoReal=$valornominal/100*$comissao;
                //grava na tabela comissao o valor da comissao
                $sql="INSERT INTO comissao (valor, codvendedor, codreceber, data) VALUES ('$comissaoReal', '$vendedor', '$codigo', '$dtpag');";
                if(!$con->Query($sql)){
                    msg('Erro gravando comissao');
                    $con->Disconnect();
                    return;
                }
            }
        }
        */

        // inclui movimento na tabela movimentos (E=entrada ou S=saida)
        $sqlarray=array(
                array('codmovim',$codigo),
                array('tipomovim',$tipomovim),
                array('codcadcaixa',$codcadcaixa),
                array('formamovim',$formamovim),
                array('data_c',$dtpag),
                array('valor',$valornominal),
                array('desconto',$desconto),
                array('multa',$multa),
                array('juros',$juros),
                array('tipodoc',$tipodoc),
                array('numdoc',$documento),
                array('historico',$historico)
        );
        if(!empty($codbancocheque)) {
            array_push($sqlarray,array('codbancocheque',$codbancocheque));
        }
        if(!empty($datacheque)) {
            array_push($sqlarray,array('datacheque',$datacheque));
        }
        if (!$codigompr=$con->Insert('movimentos',$sqlarray)) {
            msg('Erro SQL em movimentos.');
            return;
        }

        // atualiza o saldo da tabela pagar/receber
        $con->Update($this->tabela,array(
                array('saldo',$saldoatual)
                ),"WHERE codigo='$codigo'");

        // coloca codigo da venda/compra na obs.
        if($this->escolhaconta=="receber") {
            $codvenda=$this->retornabusca4('codsaidas','receber','codigo',$codigo);
            $obs_add="VENDA:$codvenda";
        }else {
            $codcompra=$this->retornabusca4('codentradas','receber','codigo',$codigo);
            $obs_add="COMPRA:$codcompra";
        }

        $obs="LANCAMENTO DA CONTA ".$codigo." A ".strtoupper($this->escolhaconta)." DE ".strtoupper($this->escolhaconta2)." COD:$codclifor ;".$obs_add;
        // inclui movimento no Banco ou no Caixa, conforme opcao do usuario
        if ($tipomovim=="B") {
            $sql="select max(codigo) from movbanc where origem=$codcadcaixa"; // pega o ultimo codigo do banco
            $resultado=$con->Query($sql);
            $max=$con->FetchRow($resultado);

            $saldo=$this->retornabusca4('saldo','movbanc','codigo',$max[0]);
            if($formamovim=="S") {
                $saldo-=$valorfinal; // subtrai
            }else { // entrada
                $saldo+=$valorfinal; // soma
            }

            $sqlarray=array(
                    array('codigompr',$codigompr),
                    array('formamovim',$formamovim),
                    array('data',$dtpag),
                    array('valor',$valorfinal),
                    array('historico',$historico),
                    array('origem',$codcadcaixa),
                    array('numero',$documento),
                    array('obs',$obs),
                    array('hora',$horafinal),
                    array('saldo',$saldo)
            );
            if(!empty($codplacon)) {
                array_push($sqlarray,array('codplacon',$codplacon));
            }
            $con->Insert('movbanc',$sqlarray);
        } else {
            $max=$this->pegaUltimoSaldo($dtpag,$codcadcaixa);
            $saldo=$this->retornabusca4('saldo','caixa','codigo',$max);
            if($formamovim=="S") {
                $saldo-=$valorfinal; // subtrai
            }else { // entrada
                $saldo+=$valorfinal; // soma
            }
            $sqlarray=array(
                    array('codigompr',$codigompr),
                    array('formamovim',$formamovim),
                    array('numero',$documento),
                    array('data',$dtpag),
                    array('valor',$valorfinal),
                    array('origem',$codcadcaixa),
                    array('historico',$historico, true),
                    array('obs',$obs, true),
                    array('hora',$horafinal),
                    array('saldo',$saldo)
            );
            if(!empty($codplacon)) {
                array_push($sqlarray,array('codplacon', $codplacon));
            }
            $con->Insert('caixa',$sqlarray);
        };
        if($docCheque) { // for escolhido o tipo cheque
            if(!$docChequeCadastrado) { // cheque nao cadastrado
                if($this->tabela=="receber") {
                    $campo_codclifor="codcliente";
                }else {
                    $campo_codclifor="codfornecedor";
                }
                $con->Insert('cheque',array(
                        array('codigompr',$codigompr),
                        array('situacao','NOVO'),
                        array('codbanco',$cheque_banco),
                        array('agencia',$cheque_agencia),
                        array('conta',$cheque_conta),
                        array('titular',$cheque_titular, true),
                        array('documento',$cheque_documento, true),
                        array('dataemissao',$dtpag),
                        array('bompara',$cheque_bompara),
                        array('numero',$cheque_numero),
                        array('valor',$valorfinal),
                        array('codreceber',$codigo),
                        array('obs',$cheque_obs, true),
                        array($campo_codclifor, $codclifor)
                ));
            }else { // cheque CADASTRADO
                if($this->tabela=="receber") {
                    $campo_codclifor="codcliente";
                }else {
                    $campo_codclifor="codfornecedor";
                }
                $con->Update('cheque',array($campo_codclifor,$codclifor), "WHERE codigo=$cheque_codigo ");
            }
        }

        //$this->label_mult_saldo->set_text($this->mascara2($saldoatual,'virgula2'));
        // adiciona na lista de pagamentos
        $this->liststore_pagamentos->append(
                array(
                $codigompr,
                $tipomovim,
                $codcadcaixa,
                $this->corrigeNumero($dtpag,'data'),
                $this->mascara2($valornominal,'virgula2'),
                $this->mascara2($desconto,'virgula2'),
                $this->mascara2($multa,'virgula2'),
                $this->mascara2($juros,'virgula2'),
                $this->mascara2($valorfinal,'virgula2'),
                $tipodoc,
                $documento,
                $codbancocheque,
                $datacheque,
                $historico
                )
        );

        $con->Disconnect();

    }


    function add_lista_mult() {
        $codconta=$this->pegaNumero($this->entry_escolhaconta);
        if(!$this->retornabusca4('codigo',$this->escolhaconta,"codigo",$codconta)) {
            msg('Conta a '.$this->escolhaconta.' nao encontrada!');
            $this->entry_escolhaconta->grab_focus();
            return;
        }
        if($this->getValorNominal(false)==0) {
            msg("Valor nominal deve ser diferente de 0 (zero)");
            return;
        }
        if ($this->getValorNominal(false)>$this->pegaNumero($this->label_saldo)) {
            msg('Valor Nominal a pagar maior que saldo devedor. Nao posso quitar.');
            $this->entry_valornominal->grab_focus();
            return;
        }
        $this->verificaSeExisteAUX=false;
        $this->liststore_mult->foreach(array($this,'verificaSeExisteNaLista'), 0, $codconta, false);
        if ($this->verificaSeExisteAUX) {
            msg("Esta conta ja foi adicionada a lista de pagamentos!");
            return;
        }else {
            $this->liststore_mult->append(
                    array(
                    $this->pegaNumero($this->entry_escolhaconta),
                    $this->mascara2($this->getValorNominal(false),'virgula2'),
                    $this->mascara2($this->getDesconto(false), 'virgula2'),
                    $this->mascara2($this->getMulta(false), 'virgula2'),
                    $this->mascara2($this->getJuros(false), 'virgula2'),
                    $this->mascara2($this->pegaNumero($this->label_valorfinal), 'virgula2'),
                    $this->mascara2($this->pegaNumero($this->label_saldo), 'virgula2')
                    )
            );
        }
        $this->soma_lista_mult();
        $this->entry_escolhaconta->grab_focus();
    }
    function excluir_lista_mult() {
        $selecionado=$this->treeview_mult->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_mult)) {
            $this->liststore_mult->remove($iter);
        }
        $this->soma_lista_mult();
    }
    function soma_lista_mult() {

        $this->soma_valornominal=0;
        $this->soma_desconto=0;
        $this->soma_multa=0;
        $this->soma_juros=0;
        $this->soma_valorfinal=0;
        $this->soma_saldo=0;

        $this->liststore_mult->foreach(array($this,'soma_lista_multAUX'));

        $this->entry_mult_valornominal->set_text($this->mascara2($this->soma_valornominal,'virgula2'));
        $this->entry_mult_desconto->set_text($this->mascara2($this->soma_desconto,'virgula2'));
        $this->entry_mult_multa->set_text($this->mascara2($this->soma_multa,'virgula2'));
        $this->entry_mult_juros->set_text($this->mascara2($this->soma_juros,'virgula2'));
        $this->label_mult_valorfinal->set_text($this->mascara2($this->soma_valorfinal,'virgula2'));
        $this->label_mult_saldo->set_text($this->mascara2($this->soma_saldo,'virgula2'));

        if($this->soma_valorfinal==0) $this->func_limpa_mult();

    }
    function soma_lista_multAUX($store, $path, $iter) {
        $this->soma_valornominal+=$this->pegaNumero($this->liststore_mult->get_value($iter,1));
        $this->soma_desconto+=$this->pegaNumero($this->liststore_mult->get_value($iter,2));
        $this->soma_multa+=$this->pegaNumero($this->liststore_mult->get_value($iter,3));
        $this->soma_juros+=$this->pegaNumero($this->liststore_mult->get_value($iter,4));
        $this->soma_valorfinal+=$this->pegaNumero($this->liststore_mult->get_value($iter,5));
        $this->soma_saldo+=$this->pegaNumero($this->liststore_mult->get_value($iter,6));
    }

    function func_limparPG() {
        $this->func_novo();
        $this->func_novo2();
        $this->func_novo3();
        $this->entry_escolhaconta->grab_focus();
    }
    function func_cancelaPG() {
        if(!$codigo=$this->DeixaSoNumero($this->entry_escolhaconta->get_text())) return;
        $codclifor=$this->label_codclifor->get_text();
        $obs="LANCAMENTO DA CONTA ".$codigo." A ".strtoupper($this->escolhaconta)." DE ".strtoupper($this->escolhaconta2)." COD:$codclifor ";

        if($selecionado=$this->treeview_pagamentos->get_selection()) {
            if($this->get_iter_liststore($selecionado,$this->liststore_pagamentos)) {
                $codigompr=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,0);
                $codigompr_cheque=$codigompr;
                $tipomovim=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,1);
                $codcb=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,2);
                $dtpag=$this->corrigeNumero($this->get_valor_liststore($selecionado,$this->liststore_pagamentos,3),'dataiso');
                $valornominal=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_pagamentos,4));
                $desconto=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_pagamentos,5));
                $valorfinal=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore_pagamentos,8));
                $tipodoc=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,9);
                //$valornominal=$valornominal*(-1);
                $valorfinal=$valorfinal*(-1);
                if($tipodoc=="E") { // se for estorno de estorno
                    msg("Nao e possivel cancelar um estorno. Faca o pagamento novamente.");
                    return;
                }elseif($tipodoc=="X") { // conta ja estornada
                    msg("Este pagamento ja foi estornado.");
                    return;
                }
                $numdoc=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,10);
                $bancocheque=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,11);
                $datacheque=$this->get_valor_liststore($selecionado,$this->liststore_pagamentos,12);
                $formamovim=$this->retornabusca4('formamovim','movimentos','codigompr',$codigompr);
            }else {
                return;
            }
        }else {
            return;
        }

        //$saldo=$this->pegaNumero($this->entry_saldo);
        if($tipomovim=="C" AND !$this->VerificaAberturaDoCaixa($codcb,$dtpag)) {
            return;
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();

        /*        if($this->tabela=="receber"){
            // pega dados do vendedor
            $sql="SELECT vendedor,comissao FROM receber WHERE codigo=$codigo";
			$resultado=$con->Query($sql);
            $resultado2=$con->FetchArray($resultado);
			$vendedor=$resultado2["vendedor"];
            $comissao=$resultado2["comissao"];
            if(!empty($vendedor) and !empty($comissao)){
                // data de hoje
                $dtpag=$this->corrigeNumero($this->datadehoje,"dataiso");
                // pega o valor em R$ da comissao a ser creditada
                $comissaoReal=($valornominal/100*$comissao)*(-1); // inverte pra ficar negativo
                //grava na tabela comissao o valor da comissao
                $sql="INSERT INTO comissao (valor, codvendedor, codreceber, data) VALUES ('$comissaoReal', '$vendedor', '$codigo', '$dtpag');";
                if(!$con->Query($sql)){
                    msg('Erro gravando comissao');
                    $con->Disconnect();
                    return;
                }
            }
        }
        */

        // excluir movimento na tabela movimentos (P=pagar e R=receber)
        $codigompr=$con->Insert('movimentos',array(
                array('codmovim',$codigo),
                array('tipomovim',$tipomovim),
                array('codcadcaixa',$codcb),
                array('formamovim',$formamovim),
                array('data_c',$dtpag),
                array('valor',$valorfinal),
                array('historico','ESTORNO'),
                array('tipodoc','E')
        ));

        $saldo=$this->retornabusca4('saldo',$this->escolhaconta,'codigo',$codigo);
        $saldoatual=$saldo+$valornominal;

        $con->Update($this->escolhaconta,array(
                array('saldo',$saldoatual)
                ), "WHERE codigo='$codigo'");

        // altera movimento para estornado impedindo que seja estornado novamente
        $con->Update('movimentos',array(
                array('tipodoc','X')
                ), "WHERE codigompr='$codigompr_cheque'");

        $horafinal=date("H:i:s");
        $this->label_saldo->set_text($this->mascara2($saldoatual,'virgula2'));
        // excluir movimento no Banco ou no Caixa, conforme opcao do usuario
        // nao excluir mas sim um ESTORNO no caixa/banco
        if ($tipomovim=="B") { // faz um estorno
            $sql="select max(codigo) from movbanc where origem=$codcb"; // pega o ultimo codigo do banco
            $resultado=$con->Query($sql);
            $max=$con->FetchRow($resultado);
            $saldo=$this->retornabusca4('saldo','movbanc','codigo',$max[0]);
            if($formamovim=="S") { // inverte forma visto que é estorno
                $saldo-=$valorfinal; // soma
            }else { // entrada
                $saldo+=$valorfinal; // subtrai
            }
            $codplacon=$this->codplacon2;
            $con->Insert('movbanc',array(
                    array('codigompr',$codigompr),
                    array('formamovim',$formamovim),
                    array('data',$dtpag),
                    array('valor',$valorfinal),
                    array('historico','ESTORNO'),
                    array('origem',$codcb),
                    array('codplacon',$codplacon),
                    array('obs',"ESTORNO DE $obs", true),
                    array('hora',$horafinal),
                    array('saldo',$saldo)
            ));
        } else {
            $max=$this->pegaUltimoSaldo($dtpag,$codcb);
            $saldo=$this->retornabusca4('saldo','caixa','codigo',$max);
            if($formamovim=="S") { // inverte visto que e estorno
                $saldo-=$valorfinal; // soma
            }else { // entrada
                $saldo+=$valorfinal; // subtrai
            }
            $codplacon=$this->codplacon2;
            $con->Insert('caixa',array(
                    array('codigompr',$codigompr),
                    array('formamovim',$formamovim),
                    array('data',$dtpag),
                    array('valor',$valorfinal),
                    array('origem',$codcb),
                    array('codplacon',$codplacon),
                    array('historico','ESTORNO'),
                    array('obs',"ESTORNO DE $obs", true),
                    array('hora',$horafinal),
                    array('saldo',$saldo)
            ));
        }

        if($tipodoc=="C") {
            msg("Voce devera alterar/excluir manualmente o cheque codigo ".$this->retornabusca4('codigo','cheque','codigompr',$codigompr_cheque));
        }
        $con->Disconnect();
        $this->focusoutEntryEscolhaConta();
        $this->status('Pagamento cancelado com sucesso.');
    }
    function getJuros($mult=true) {
        // calcula juros
        $valornominal=$this->getValorNominal($mult);
        if($mult) {
            $juros=$this->entry_mult_juros->get_text();
        }else {
            $juros=$this->entry_juros->get_text();
        }
        $pos = strpos($juros, '%');
        if ($pos === false) { // se NAO tiver sinal de porcento
            $juros=$this->pegaNumero($juros);
        } else { // se tiver % calcula porcentagem sobre o valor nominal
            $juros=$valornominal/100*$this->pegaNumero($juros);
        }
        return $juros;
    }
    function getMulta($mult=true) {
        // calcula multa
        $valornominal=$this->getValorNominal($mult);
        if($mult) {
            $multa=$this->entry_mult_multa->get_text();
        }else {
            $multa=$this->entry_multa->get_text();
        }
        $pos = strpos($multa, '%');
        if ($pos === false) { // se NAO tiver sinal de porcento
            $multa=$this->pegaNumero($multa);
        } else { // se tiver % calcula porcentagem sobre o valor nominal
            $multa=$valornominal/100*$this->pegaNumero($multa);
        }
        return $multa;
    }
    function getDesconto($mult=true) {
        // calcula desconto
        $valornominal=$this->getValorNominal($mult);
        if($mult) {
            $desconto=$this->entry_mult_desconto->get_text();
        }else {
            $desconto=$this->entry_desconto->get_text();
        }
        $pos = strpos($desconto, '%');
        if ($pos === false) { // se NAO tiver sinal de porcento
            $desconto=$this->pegaNumero($desconto);
        } else { // se tiver % calcula porcentagem sobre o valor nominal
            $desconto=$valornominal/100*$this->pegaNumero($desconto);
        }
        return $desconto;
    }
    function getValorNominal($mult=true) {
        if($mult) {
            return $this->pegaNumero($this->entry_mult_valornominal);
        }else {
            return $this->pegaNumero($this->entry_valornominal);
        }
    }
    function calculaValorFinal($retorno=true,$mult=false) {
        $valornominal=$this->getValorNominal($mult);

        $valorfinal=$valornominal+$this->getMulta($mult)+$this->getJuros($mult)-$this->getDesconto($mult);
        if($mult) {
            $this->label_mult_valorfinal->set_text($this->mascara2($valorfinal,'virgula2'));
        }else {
            $this->label_valorfinal->set_text($this->mascara2($valorfinal,'virgula2'));
        }

        // quebra galho visto que o key-release nao funciona
        if($retorno) {
            Gtk::timeout_add(100,array($this,'calculaValorFinal'),false,$mult);
        }
    }

    function mostraBanco($label,$entry) {
        $codigo=$this->DeixaSoNumero($entry->get_text());
        if(!empty($codigo)) {
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();
            if($entry->get_name()=="entry_cadcaixa") {
                if($this->radiobutton_banco->get_active()) {
                    $sql="SELECT n.sigla,b.agencia,b.conta FROM bancos AS b LEFT JOIN nomebanco AS n ON b.numero=n.codigo WHERE b.codbanco='$codigo'";
                    $resultado=$con->Query($sql);
                    $i=$con->FetchArray($resultado);
                    if(!empty($i[0])) {
                        $txt=$i[0].", Ag: ".$i[1].", Cta: ".$i[2];
                        $label->set_text($txt);
                    }
                }else {
                    $label->set_text($this->retornabusca4('descricao','cadcaixa','codigo',$codigo));
                }
            }elseif($entry->get_name()=="entry_codbanco") {
                if($this->radiobutton_cheque->get_active()) {
                    $sql="SELECT n.sigla,b.agencia,b.conta FROM bancos AS b LEFT JOIN nomebanco AS n ON b.numero=n.codigo WHERE b.codbanco='$codigo'";
                    $resultado=$con->Query($sql);
                    $i=$con->FetchArray($resultado);
                    if(!empty($i[0])) {
                        $txt=$i[0].", Ag: ".$i[1].", Cta: ".$i[2];
                        $label->set_text($txt);
                    }
                }
            }
            $con->Disconnect();
        }
    }
    function pegaUltimoSaldo($dtpag,$codcadcaixa) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();

        $sql="SELECT dataaberto, datafechado FROM controlecaixa WHERE codcadcaixa=$codcadcaixa AND dataaberto<='$dtpag' AND datafechado>='$dtpag' AND aberto='1' AND fechado='0'";
        $resultado=$con->Query($sql);
        if($con->NumRows($resultado)==0) {
            msg("Erro. Nenhum registro encontrado de caixa aberto");
            return false;
        }
        $i=$con->FetchRow($resultado);
        $dataaberto=$i[0];
        $datafechado=$i[1];
        $sql="SELECT MAX(codigo) FROM caixa WHERE origem=$codcadcaixa AND data>='$dataaberto' AND data<='$datafechado'"; // pega o ultimo codigo do caixa
        $resultado=$con->Query($sql);
        $max=$con->FetchRow($resultado);
        $con->Disconnect();
        return $max[0];
    }
}
?>
