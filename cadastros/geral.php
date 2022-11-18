<?php
class geral extends funcoes {
    function geral($tab,$tituloWindow) {
        // pega a variavel da tabela que sera usada.. por exemplo grpmerc
        $this->tabela=$tab;
        // entra com as variaveis de permissao de acordo com a tabela
        if($tab=="clientes") {
            $this->permissaoIncluir='010102';
            $this->permissaoExcluir='010103';
            $this->permissaoAlterar='010104';
        }elseif($tab=="funcionarios") {
            $this->permissaoIncluir='010202';
            $this->permissaoExcluir='010203';
            $this->permissaoAlterar='010204';
        }elseif($tab=="fornecedores") {
            $this->permissaoIncluir='010302';
            $this->permissaoExcluir='010303';
            $this->permissaoAlterar='010304';
        }elseif($tab=="fabricantes") {
            $this->permissaoIncluir='010402';
            $this->permissaoExcluir='010403';
            $this->permissaoAlterar='010404';
        }elseif($tab=="empregador") {
            $this->permissaoIncluir='010602';
            $this->permissaoExcluir='010603';
            $this->permissaoAlterar='010604';
        }

        // pegaentry eh uma funcao do tabelas.php que declara todos
        // os entrys do glade e de quebra bota o titulo correto na janela
        $this->pegaentry($tituloWindow);
        // cria o clist da tabela
        $this->abre_clist_geral();

        // funcoes de busca
        //$this->treeview->set_enable_search(true);

        //teste search
        //$this->treeview->connect_simple('start-interactive-search', array($this, 'start_interactive_search'));
        //$this->treeview->connect('key-press-event', array($this, 'start_interactive_search'));
        //$this->search_entry->connect('key-press-event', array($this, 'on_entry_keypress'));
        //$this->search_entry->connect('focus-out-event', array($this, 'stop_interactive_search'));

        //$this->treeview->set_search_column(2);

        $this->teste_foco();
    }
    function cor_treeview_geral($column,$cell,$liststore,$iter) {
        //codigo, natureza, nome, obs, referencias, inativo
        $inativo=$liststore->get_value($iter,3);
        if($inativo=="1") {
            $cinza=new GdkColor(32767,32767,32767,0);
            $cell->set_property("foreground-gdk",$cinza);
        }else {
            $preto=new GdkColor(0,0,0,0);
            $cell->set_property("foreground-gdk",$preto);
        }
    }

    function teste_foco() {
        global $parente;
        if($parente) {
            $parente->grab_focus();
            $parente->set_accept_focus(TRUE);
            $parente->set_focus_on_map(TRUE);
        }
    }
    function grava_dados_geral($alterar) {
        // se permite cadastro menos de 2 enderecos
        if($this->clist_enderecos->rows<2 and $this->tabela=="clientes" ) {
            if(!$this->verificaPermissao('010107',true,'Cadastre no minimo 2 endere�s')) {
                return;
            }
        }

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_geral=&new $BancoDeDados;
        $this->con_geral->Connect();


        $sqlarray=array(
                array('natureza', $this->natureza),
                array('nome', $this->nome, true),
                array('cnpj_cpf', $this->cpf),
                array('ie_rg', $this->rg),
                array('orgaorg', $this->orgaorg),
                array('dtemissaorg', $this->dtemissaorg),
                array('im', $this->im),
                array('ir', $this->ir),
                array('pis', $this->pis),
                array('carteira', $this->carteira, true),
                array('titulo', $this->titulo),
                array('dtnasc', $this->dtnasc),
                array('sexo', $this->sexo),
                array('dtcadastro', $this->dtcadastro),
                array('obs', $this->obs, true),
                array('habcomprar', $this->habcomprar),
                array('comissao', $this->comissao),
                array('debmaximo', $this->debmaximo),
                array('referencias', $this->referencias, true),
                array('inativo', $this->inativo)
        );
        if($this->tabela=="clientes" and !empty($this->codvendedorcliente)) {
            array_push($sqlarray,
                    array('codvendedor', $this->codvendedorcliente),
                    array('comissaovendedor', $this->comissaovendedor)
            );
        }

        if(!empty($this->codmidiapropaganda)) {
            array_push($sqlarray,
                    array('codmidiapropaganda', $this->codmidiapropaganda)
            );
        }

        if(!empty($this->fotoGeral) or $this->fotoGeralLimpa) {
            array_push($sqlarray,
                    array('foto',$this->fotoGeral)
            );
        }

        if ($alterar) {
            $this->lastcod=$this->codigo;
            if(!$this->con_geral->Update($this->tabela, $sqlarray, "WHERE codigo='$this->codigo'")) {
                msg("Erro ao executar SQL");
                $this->con_geral->Disconnect();
                return;
            }else {
                $this->status('Registro alterado com sucesso');
            }

        } else {

            if(!$this->lastcod=$this->con_geral->Insert($this->tabela, $sqlarray)) {
                msg("Erro ao executar SQL");
                $this->con_geral->Disconnect();
                return;
            }else {
                $this->entry_codigo->set_text($this->lastcod);
                $this->status('Registro gravado com sucesso');
            }

        }

        // inclui enderecos
        $sql="WHERE codigo='$this->lastcod' AND cadastro='$this->tabela'";
        $this->con_geral->Delete("cadastro2enderecos", $sql);
        $this->liststore_enderecos->foreach(array($this,'gravaEnderecos'));

        // inclui contatos
        $sql="WHERE codigo=$this->lastcod AND cadastro='$this->tabela'";
        $this->con_geral->Delete('cadastro2contatos', $sql);
        $this->liststore_contato->foreach(array($this,'gravaContatos'));

        // inclui profissoes
        $sql="WHERE codigo=$this->lastcod AND cadastro='$this->tabela';";
        $this->con_geral->Delete('cadastro2profissao', $sql);
        $this->liststore_profissao->foreach(array($this,'gravaProfissao'));

        // inclui profissoes
        $sql="WHERE codigo=$this->lastcod AND cadastro='$this->tabela';";
        $this->con_geral->Delete('cadastro2familias', $sql);
        $this->liststore_familia->foreach(array($this,'gravaFamilia'));

        // inclui bancos
        $sql="WHERE codigo=$this->lastcod AND cadastro='$this->tabela';";
        $this->con_geral->Delete('cadastro2bancos', $sql);
        $this->liststore_bancos->foreach(array($this,'gravaBancos'));

        $this->con_geral->Disconnect();
    }

    function gravaEnderecos($store, $path, $iter) {
        $descricao=$store->get_value($iter,0);
        $romaneio=$store->get_value($iter,1);
        if(empty($romaneio)) $romaneio='null';
        $endereco=$store->get_value($iter,2);
        $numero=$store->get_value($iter,3);
        $complemento=$store->get_value($iter,4);
        $bairro=$store->get_value($iter,5);
        $estado=$store->get_value($iter,6);
        $cidade=$Xnome=$this->tira_acentos($store->get_value($iter,7));
        $cep=$store->get_value($iter,8);
        $telefone=$store->get_value($iter,9);
        $fax=$store->get_value($iter,10);
        $celular=$store->get_value($iter,11);
        $email=$store->get_value($iter,12);
        $site=$store->get_value($iter,13);

        if(!$this->con_geral->Insert('cadastro2enderecos', array(
        array('descricao', $descricao, true),
        array('romaneio', $romaneio),
        array('endereco', $endereco, true),
        array('numero', $numero, true),
        array('complemento', $complemento, true),
        array('bairro', $bairro, true),
        array('estado', $estado, true),
        array('cidade', $cidade, true),
        array('cep', $cep, true),
        array('telefone', $telefone, true),
        array('fax', $fax, true),
        array('celular', $celular, true),
        array('email', $email, true),
        array('site', $site, true),
        array('codigo', $this->lastcod),
        array('cadastro', $this->tabela)
        ))) {
            msg("Erro ao executar SQL de enderecos");
            $this->con_geral->Disconnect();
            return;
        }
    }
    function gravaContatos($store, $path, $iter) {
        $nome=$store->get_value($iter,0);
        $departamento=$store->get_value($iter,1);
        $telefone=$store->get_value($iter,2);
        $email=$store->get_value($iter,3);
        $obs=$store->get_value($iter,4);
        if(!$this->con_geral->Insert('cadastro2contatos', array(
        array('nome',$nome, true),
        array('departamento',$departamento, true),
        array('telefone',$telefone, true),
        array('email',$email, true),
        array('obs',$obs, true),
        array('codigo',$this->lastcod),
        array('cadastro',$this->tabela)
        ))) {
            msg("Erro ao executar SQL contatos");
            $this->con_geral->Disconnect();
            return;
        }
    }

    function gravaProfissao($store, $path, $iter) {
        $descricao=$store->get_value($iter,0);
        $codprof=$store->get_value($iter,3);
        $codempregador=$store->get_value($iter,1);
        $obs=$store->get_value($iter,6);
        $renda=$this->DeixaSoNumeroDecimal($store->get_value($iter,5),2);
        if(!$this->con_geral->Insert('cadastro2profissao', array(
        array('descricao',$descricao, true),
        array('codprofissao',$codprof),
        array('codempregador',$codempregador),
        array('obs',$obs, true),
        array('renda',$renda),
        array('cadastro',$this->tabela),
        array('codigo',$this->lastcod),
        ))) {
            msg("Erro ao executar SQL profissao");
            $this->con_geral->Disconnect();
            return;
        }
    }

    function gravaFamilia($store, $path, $iter) {
        $codpar=$store->get_value($iter,0);
        $nome=$store->get_value($iter,2);
        $dtnasc=$store->get_value($iter,3);
        $obs=$store->get_value($iter,4);
        if(!$this->con_geral->Insert('cadastro2familias', array(
        array('codparentesco',$codpar),
        array('nome',$nome),
        array('dtnasc',$dtnasc),
        array('obs',$obs),
        array('codigo',$this->lastcod),
        array('cadastro',$this->tabela),
        ))) {
            msg("Erro ao executar SQL familia");
            $this->con_geral->Disconnect();
            return;
        }
    }

    function gravaBancos($store, $path, $iter) {
        $codbanco=$store->get_value($iter,0);
        if(!$this->con_geral->Insert('cadastro2bancos', array(
        array('codbanco',$codbanco),
        array('codigo',$this->lastcod),
        array('cadastro',$this->tabela),
        ))) {
            msg("Erro ao executar SQL bancos");
            $this->con_geral->Disconnect();
            return;
        }
    }

    function click_button_busca_geral(){
        $this->button_busca_geral->clicked();
    }

    function search_busca_geral(){

        $busca=$this->entry_busca_geral->get_text();
        $busca=mysql_escape_string($busca);
        $sql="select codigo, nome, cnpj_cpf, ie_rg, inativo from ".$this->tabela." as ".$this->tabela." WHERE nome LIKE '$busca%' order by nome";
        $this->atualiza_clist_cadastro($this->tabela, false, false, $sql);
        //$this->AdicionaLinhasBuscatab($sql,true);
        //$this->window1_buscatab->show_all();
        //$this->treeview_buscatab->grab_focus();

       
    }

    function pegaentry($tituloWindow) {

        $this->xml=$this->carregaGlade('geral',$tituloWindow,null,null,false);


        $this->entry_busca_geral=$this->xml->get_widget('entry_busca_geral');
        $this->entry_busca_geral->connect_simple('activate', array($this,'click_button_busca_geral'));

        $this->button_busca_geral=$this->xml->get_widget('button_busca_geral');
        $this->button_busca_geral->connect_simple('clicked', array($this,'search_busca_geral'));

        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        $this->fontcolor=new GdkColor(0,0,0);
        $this->backcolor=new GdkColor(65535,65535,65535);

        //$this->font=gdk::font_load ("-*-helvetica-r-r-*-*-*-120-*-*-*-*-*-*");
        $this->notebook=$this->xml->get_widget('notebook');

        // aba "basico" ******************************
        $this->entry_codigo=$this->xml->get_widget('entry_codigo');

        $this->combo_natureza=$this->xml->get_widget('combo_natureza');
        //$this->entry_natureza=$this->xml->get_widget('entry_natureza');
        $this->combo_natureza->connect("changed", array($this,'changedNatureza'));
        $this->entry_nome=$this->xml->get_widget('entry_nome');
        $this->entry_dtcadastro=$this->xml->get_widget('entry_dtcadastro');
        $this->entry_dtcadastro->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');

        $this->frame_sexo=$this->xml->get_widget('frame_sexo');
        $this->radiobutton_masculino=$this->xml->get_widget('radiobutton_masculino');
        $this->radiobutton_feminino=$this->xml->get_widget('radiobutton_feminino');
        $this->label_dtnasc=$this->xml->get_widget('label_dtnasc');
        $this->entry_dtnasc=$this->xml->get_widget('entry_dtnasc');
        $this->entry_dtnasc->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_ultvenda=$this->xml->get_widget('entry_ultvenda');
        $this->entry_ultcompra=$this->xml->get_widget('entry_ultcompra');
        $this->checkbutton_inativo=$this->xml->get_widget('checkbutton_inativo');

        $this->entry_comoconheceu=$this->xml->get_widget('entry_comoconheceu');
        $this->label_comoconheceu=$this->xml->get_widget('label_comoconheceu');
        $this->entry_comoconheceu->connect('key_press_event',
                array($this,'entry_enter'),
                'select codigo, descricao from midiapropaganda',
                true,
                $this->entry_comoconheceu,
                $this->label_comoconheceu,
                "midiapropaganda",
                "descricao",
                "codigo"
        );
        $this->entry_comoconheceu->connect_simple('focus-out-event',
                array($this,'retornabusca22'),
                'midiapropaganda',
                $this->entry_comoconheceu,
                $this->label_comoconheceu,
                'codigo',
                'descricao'
        );

        $this->textView_obs=$this->xml->get_widget('text_obs');
        $this->textBuffer_obs=new GtkTextBuffer();
        $this->textView_obs->set_buffer($this->textBuffer_obs);

        $this->textView_referencias=$this->xml->get_widget('text_referencias');
        $this->textBuffer_referencias=new GtkTextBuffer();
        $this->textView_referencias->set_buffer($this->textBuffer_referencias);

//aba documentos *********************************
        $this->entry_cpf=$this->xml->get_widget('entry_cpf');
        $this->label_cpf=$this->xml->get_widget('label_cpf');
        $this->entry_cpf->connect('key-press-event', array($this,'mascaraNew'),'cnpj',&$this->combo_natureza);
        $this->entry_rg=$this->xml->get_widget('entry_rg');
        $this->label_rg=$this->xml->get_widget('label_rg');
        
        $this->combo_estadoie=$this->xml->get_widget('combo_estadoie');
        //$this->entry_estadoie=$this->xml->get_widget('entry_estadoie');
        $this->entry_estadoie=$this->combo_estadoie->entry;
        $this->encheComboEstado($this->combo_estadoie);
        $this->entry_rg->connect('key-press-event', array($this,'mascaraNew'), 'ie', &$this->combo_natureza, &$this->entry_estadoie);
        $this->entry_orgaorg=$this->xml->get_widget('entry_orgaorg');
        $this->entry_dtemissaorg=$this->xml->get_widget('entry_dtemissaorg');
        $this->entry_dtemissaorg->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_im=$this->xml->get_widget('entry_im');
        $this->entry_ir=$this->xml->get_widget('entry_ir');
        $this->entry_titulo=$this->xml->get_widget('entry_titulo');
        $this->entry_titulo->connect('key-press-event', array($this,'mascaraNew'),'**********/**');
        $this->entry_carteira=$this->xml->get_widget('entry_carteira');
        $this->entry_pis=$this->xml->get_widget('entry_pis');
        $this->entry_pis->connect('key-press-event', array(&$this,mascaraNew),'***.*****.**.*');

// aba enderecos **********************************
        $this->entry_romaneio=$this->xml->get_widget('entry_romaneio');
        $this->label_romaneio=$this->xml->get_widget('label_romaneio');
        $this->entry_romaneio->connect('key_press_event',
                array(&$this,entry_enter),
                'select codigo, descricao from romaneio',
                true,
                &$this->entry_romaneio,
                &$this->label_romaneio,
                "romaneio",
                "descricao",
                "codigo"
        );
        $this->entry_romaneio->connect_simple('focus-out-event',
                array(&$this,retornabusca22),
                'romaneio',
                &$this->entry_romaneio,
                &$this->label_romaneio,
                'codigo',
                'descricao',
                &$this->tabela
        );

        $this->entry_descricao=$this->xml->get_widget('entry_descricao');
        $this->entry_endereco=$this->xml->get_widget('entry_endereco');
        $this->entry_numero=$this->xml->get_widget('entry_numero');
        $this->entry_complemento=$this->xml->get_widget('entry_complemento');
        $this->entry_bairro=$this->xml->get_widget('entry_bairro');

        $this->entry_cidade=$this->xml->get_widget('entry_cidade');

        $this->frame_estado=$this->xml->get_widget('frame_estado');
        
        //$this->combo_estado=$this->xml->get_widget('combo_estado');
        $this->combo_estado=new GtkCombo();
        $this->frame_estado->add($this->combo_estado);
        $this->encheComboEstado($this->combo_estado);


        //$this->entry_estado=$this->xml->get_widget('entry_estado');
        $this->entry_estado=$this->combo_estado->entry;
        $this->entry_estado->set_text(retorna_CONFIG("Estado"));

        $this->entry_estado->connect_simple('changed', array($this,'cidadesNew'),$this->entry_cidade, $this->entry_estado);

        $this->cidadesNew($this->entry_cidade,$this->entry_estado);

        $this->entry_cep=$this->xml->get_widget('entry_cep');
        $this->entry_cep->connect('key-press-event', array($this,mascaraNew),'**.***-***');
        $this->entry_cep->connect('focus-out-event', array($this,'procuracep'));

        $this->entry_telefone=$this->xml->get_widget('entry_telefone');
        $this->entry_telefone->connect('key-press-event', array($this,'mascaraNew'),'(**)****-****');
        $this->entry_fax=$this->xml->get_widget('entry_fax');
        $this->entry_fax->connect('key-press-event', array($this,'mascaraNew'),'(**)****-****');
        $this->entry_celular=$this->xml->get_widget('entry_celular');
        $this->entry_celular->connect('key-press-event', array($this,'mascaraNew'),'(**)****-****');
        $this->entry_email=$this->xml->get_widget('entry_email');
        $this->entry_site=$this->xml->get_widget('entry_site');

        $this->scrolledwindow_enderecos=$this->xml->get_widget('scrolledwindow_enderecos');
        $this->liststore_enderecos=new GtkListStore(
                GObject::TYPE_STRING, // descricao
                GObject::TYPE_STRING, // romaneio
                GObject::TYPE_STRING, // endereco
                GObject::TYPE_STRING, // numero
                GObject::TYPE_STRING, // complemento
                GObject::TYPE_STRING, // bairro
                GObject::TYPE_STRING, // cidade
                GObject::TYPE_STRING, // estado
                GObject::TYPE_STRING, // cep
                GObject::TYPE_STRING, // telefone
                GObject::TYPE_STRING, // fax
                GObject::TYPE_STRING, // celular
                GObject::TYPE_STRING, // email
                GObject::TYPE_STRING // site
        );
        $this->treeview_enderecos=new GtkTreeView($this->liststore_enderecos);
        $this->add_coluna_treeview(
                array('Descricao', 'Romaneio', 'Endereco', 'Numero', 'Complemento', 'Bairro', 'Estado', 'Cidade', 'CEP', 'Telefone', 'Fax', 'Celular', 'E-Mail', 'Site'),
                $this->treeview_enderecos
        );
        $this->scrolledwindow_enderecos->add($this->treeview_enderecos);
        $this->scrolledwindow_enderecos->show_all();
        $this->treeview_enderecos->connect('row-activated',array(&$this,'clicaEndereco'));
        $this->button_enderecos_incluir=$this->xml->get_widget('button_enderecos_incluir');
        $this->button_enderecos_refazer=$this->xml->get_widget('button_enderecos_refazer');
        $this->button_enderecos_excluir=$this->xml->get_widget('button_enderecos_excluir');
        $this->button_enderecos_incluir->connect_simple('clicked', array($this, 'incluirEnderecos'));
        $this->button_enderecos_refazer->connect_simple('clicked', array($this, 'incluirEnderecos'), true);
        $this->button_enderecos_excluir->connect_simple('clicked', array($this, 'excluirEnderecos'));

// aba "contatos" **********************
        $this->scrolledwindow_contato=$this->xml->get_widget('scrolledwindow_contato');
        $this->liststore_contato=new GtkListStore(
                GObject::TYPE_STRING, // nome
                GObject::TYPE_STRING, // departamento
                GObject::TYPE_STRING, // telefone
                GObject::TYPE_STRING, // email
                GObject::TYPE_STRING // obs
        );
        $this->treeview_contato=new GtkTreeView($this->liststore_contato);
        $this->add_coluna_treeview(
                array('Nome', 'Departamento', 'Telefone', 'Email', 'Obs'),
                $this->treeview_contato
        );
        $this->scrolledwindow_contato->add($this->treeview_contato);
        $this->scrolledwindow_contato->show_all();
        $this->treeview_contato->connect('row-activated',array(&$this,'clicaContato'));
        $this->button_contatoincluir=$this->xml->get_widget('button_contatoincluir');
        $this->button_contatorefazer=$this->xml->get_widget('button_contatorefazer');
        $this->button_contatoexcluir=$this->xml->get_widget('button_contatoexcluir');
        $this->button_contatoincluir->connect_simple('clicked', array($this, 'incluirContato'));
        $this->button_contatorefazer->connect_simple('clicked', array($this, 'incluirContato'),true);
        $this->button_contatoexcluir->connect_simple('clicked', array($this, 'excluirContato'));

        $this->entry_nomecontato=$this->xml->get_widget('entry_nomecontato');
        $this->entry_departamentocontato=$this->xml->get_widget('entry_departamentocontato');
        $this->entry_telefonecontato=$this->xml->get_widget('entry_telefonecontato');
        $this->entry_telefonecontato->connect('key-press-event', array(&$this,'mascaraNew'),'(**)****-****');
        $this->entry_emailcontato=$this->xml->get_widget('entry_emailcontato');
        $this->entry_obscontato=$this->xml->get_widget('entry_obscontato');

// aba "grp familiar" **********************
        $this->vbox_familia=$this->xml->get_widget('vbox_familia');
        $this->scrolledwindow_familia=$this->xml->get_widget('scrolledwindow_familia');
        $this->liststore_familia=new GtkListStore(
                GObject::TYPE_STRING, // cod par
                GObject::TYPE_STRING, // par
                GObject::TYPE_STRING, // nome
                GObject::TYPE_STRING, // dt nasc
                GObject::TYPE_STRING // obs
        );
        $this->treeview_familia=new GtkTreeView($this->liststore_familia);
        $this->add_coluna_treeview(
                array('Cod.Par.','Parentesco','Nome','Data Nascimento','Obs.'),
                $this->treeview_familia
        );
        $this->scrolledwindow_familia->add($this->treeview_familia);
        $this->scrolledwindow_familia->show_all();
        $this->treeview_familia->connect('row-activated',array($this,'clicaFamilia'));
        $this->button_familiaincluir=$this->xml->get_widget('button_familiaincluir');
        $this->button_familiaexcluir=$this->xml->get_widget('button_familiaexcluir');
        $this->button_familiaincluir->connect_simple('clicked', array($this, 'incluirFamilia'));
        $this->button_familiaexcluir->connect_simple('clicked', array($this, 'excluirFamilia'));
        $this->button_familiarefazer=$this->xml->get_widget('button_familiarefazer');
        $this->button_familiarefazer->connect_simple('clicked', array($this, 'incluirFamilia'),true);

        $this->entry_codparentesco=$this->xml->get_widget('entry_codparentesco');
        $this->label_codparentesco=$this->xml->get_widget('label_codparentesco');
        $this->entry_codparentesco->connect('key_press_event',
                array($this,entry_enter),
                'select codigo, descricao from parentesco',
                true,
                $this->entry_codparentesco,
                $this->label_codparentesco,
                "parentesco",
                "descricao",
                "codigo"
        );
        $this->entry_codparentesco->connect_simple('focus-out-event',
                array($this,retornabusca22),
                'parentesco',
                $this->entry_codparentesco,
                $this->label_codparentesco,
                'codigo',
                'descricao'
        );
        $this->entry_nomefamilia=$this->xml->get_widget('entry_nomefamilia');
        $this->entry_dtnascfamilia=$this->xml->get_widget('entry_dtnascfamilia');
        $this->entry_dtnascfamilia->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_obsfamilia=$this->xml->get_widget('entry_obsfamilia');

// aba "profissional" **********************
        $this->vbox_profissao=$this->xml->get_widget('vbox_profissao');
        $this->scrolledwindow_profissao=$this->xml->get_widget('scrolledwindow_profissao');
        $this->liststore_profissao=new GtkListStore(
                GObject::TYPE_STRING, // descricao
                GObject::TYPE_STRING, // cod empregador
                GObject::TYPE_STRING, // empregador
                GObject::TYPE_STRING, // cod profissao
                GObject::TYPE_STRING, // profissao
                GObject::TYPE_STRING, // renda
                GObject::TYPE_STRING // obs
        );
        $this->treeview_profissao=new GtkTreeView($this->liststore_profissao);
        $this->add_coluna_treeview(
                array('Descricao', 'Cod. Empregador', 'Empregador', 'Cod. Profissao', 'Profissao', 'Renda', 'Obs.'),
                $this->treeview_profissao
        );
        $this->scrolledwindow_profissao->add($this->treeview_profissao);
        $this->scrolledwindow_profissao->show_all();
        $this->treeview_profissao->connect('row-activated',array(&$this,'clicaProfissao'));
        $this->button_profissao_incluir=$this->xml->get_widget('button_profissao_incluir');
        $this->button_profissao_refazer=$this->xml->get_widget('button_profissao_refazer');
        $this->button_profissao_excluir=$this->xml->get_widget('button_profissao_excluir');
        $this->button_profissao_incluir->connect_simple('clicked', array($this, 'incluirProfissao'));
        $this->button_profissao_refazer->connect_simple('clicked', array($this, 'incluirProfissao'), true);
        $this->button_profissao_excluir->connect_simple('clicked', array($this, 'excluirProfissao'));
        $this->entry_descricaoprof=$this->xml->get_widget('entry_descricaoprof');

        $this->entry_rendaprof=$this->xml->get_widget('entry_rendaprof');

        $this->entry_rendaprof->connect('key-press-event', array(&$this, mascaraNew),'virgula2');

        $this->entry_codprof=$this->xml->get_widget('entry_codprof');
        $this->label_codprof=$this->xml->get_widget('label_codprof');
        $this->entry_codprof->connect('key_press_event',
                array(&$this,entry_enter),
                'select codigo, descricao from profissao',
                true,
                &$this->entry_codprof,
                &$this->label_codprof,
                "profissao",
                "descricao",
                "codigo"
        );
        $this->entry_codprof->connect_simple('focus-out-event',
                array(&$this,retornabusca22),
                'profissao',
                &$this->entry_codprof,
                &$this->label_codprof,
                'codigo',
                'descricao',
                &$this->tabela
        );

        $this->entry_codempregador=$this->xml->get_widget('entry_codempregador');
        $this->label_codempregador=$this->xml->get_widget('label_codempregador');
        $this->entry_codempregador->connect('key_press_event',
                array(&$this,entry_enter),
                'select codigo, nome, contato, cnpj_cpf, ie_rg, obs from empregador',
                true,
                &$this->entry_codempregador,
                &$this->label_codempregador,
                "empregador",
                "nome",
                "codigo"
        );
        $this->entry_codempregador->connect_simple('focus-out-event',
                array(&$this,retornabusca22),
                'empregador',
                &$this->entry_codempregador,
                &$this->label_codempregador,
                'codigo',
                'nome',
                &$this->tabela
        );
        $this->entry_obsprof=$this->xml->get_widget('entry_obsprof');

// aba "financeiro" *****************************
        $this->entry_codbanco=$this->xml->get_widget('entry_codbanco');
        $this->label_codbanco=$this->xml->get_widget('label_codbanco');
        $this->entry_codbanco->connect('key_press_event',
                array(&$this,entry_enter),
                "SELECT b.codbanco,b.titular,b.numero,n.nome,b.agencia,b.conta,b.obs FROM bancos AS b LEFT JOIN nomebanco AS n ON b.numero=n.codigo WHERE b.contadaempresa='1'",
                true,
                $this->entry_codbanco,
                $this->label_codbanco,
                "bancos",
                "titular",
                "codbanco"
        );
        $this->entry_codbanco->connect_simple('focus-out-event',
                array(&$this,retornabusca22),
                'bancos',
                $this->entry_codbanco,
                $this->label_codbanco,
                'codbanco',
                'titular',
                $this->tabela
        );
        $this->scrolledwindow_bancos=$this->xml->get_widget('scrolledwindow_bancos');
        $this->liststore_bancos=new GtkListStore(
                GObject::TYPE_STRING, // codigo
                GObject::TYPE_STRING, // banco
                GObject::TYPE_STRING, // Titular
                GObject::TYPE_STRING, // agencia
                GObject::TYPE_STRING  // conta
        );
        $this->treeview_bancos=new GtkTreeView($this->liststore_bancos);
        $this->add_coluna_treeview(
                array('Codigo', 'Banco', 'Titular', 'Agencia', 'Conta'),
                $this->treeview_bancos
        );
        $this->scrolledwindow_bancos->add($this->treeview_bancos);
        $this->scrolledwindow_bancos->show_all();


        $this->button_bancos_incluir=$this->xml->get_widget('button_bancos_incluir');
        $this->button_bancos_refazer=$this->xml->get_widget('button_bancos_refazer');
        $this->button_bancos_excluir=$this->xml->get_widget('button_bancos_excluir');

        $this->button_bancos_incluir->connect_simple('clicked', array(&$this, 'incluirBanco'));
        $this->button_bancos_refazer->connect_simple('clicked', array(&$this, 'incluirBanco'),true);
        $this->button_bancos_excluir->connect_simple('clicked', array(&$this, 'excluirBanco'));

        $this->entry_debmaximo=$this->xml->get_widget('entry_debmaximo');
        $this->entry_debmaximo->connect('key-press-event', array(&$this,'mascaraNew'),'virgula2');
        if($this->tabela=="clientes") {
            $this->entry_debmaximo->set_sensitive($this->verificaPermissao('010105',false));
        }else {
            $this->frame_debmaximo=$this->xml->get_widget('frame_debmaximo');
            $this->frame_debmaximo->set_sensitive(false);
        }

        
        $this->combo_habcomprar=$this->xml->get_widget('combo_habcomprar');
        //$this->entry_habcomprar=$this->xml->get_widget('entry_habcomprar');
        $this->entry_habcomprar=$this->combo_habcomprar->entry;
        if($this->tabela=="clientes") {
            $this->combo_habcomprar->set_sensitive($this->verificaPermissao('010106',false));
        }else {
            $this->frame_habcomprar=$this->xml->get_widget('frame_habcomprar');
            $this->frame_habcomprar->set_sensitive(false);
        }
        $this->entry_comissaovendedor=$this->xml->get_widget('entry_comissaovendedor');
        $this->entry_comissaovendedor->connect('key-press-event', array($this, mascaraNew),'virgula2');
        if($this->tabela=="clientes") {
            $this->entry_comissaovendedor->set_sensitive($this->verificaPermissao('',false));
        }else {
            $this->frame_comissaovendedor=$this->xml->get_widget('frame_comissaovendedor');
            $this->frame_comissaovendedor->set_sensitive(FALSE);
        }
        $this->entry_comissao=$this->xml->get_widget('entry_comissao');
        $this->entry_comissao->connect('key-press-event', array(&$this, mascaraNew),'virgula2');
        if($this->tabela<>"funcionarios") { // desliga a comissao e o habvender se nao for o cadastro de funcionario
            $this->frame_comissao=$this->xml->get_widget('frame_comissao');
            $this->frame_comissao->set_sensitive(false);
        }


        $this->entry_codvendedorcliente=$this->xml->get_widget('entry_codvendedorcliente');
        $this->label_codvendedorcliente=$this->xml->get_widget('label_codvendedorcliente');
        $this->entry_codvendedorcliente->connect('key_press_event',
                array($this,entry_enter),
                'select codigo, nome, contato, cnpj_cpf, ie_rg, obs from funcionarios',
                true,
                $this->entry_codvendedorcliente,
                $this->label_codvendedorcliente,
                "funcionarios",
                "nome",
                "codigo"
        );
        $this->entry_codvendedorcliente->connect_simple('focus-out-event',
                array($this,retornabusca22),
                'funcionarios',
                $this->entry_codvendedorcliente,
                $this->label_codvendedorcliente,
                'codigo',
                'nome'
        );
        if($this->tabela=="clientes") {
            $this->entry_codvendedorcliente->set_sensitive($this->verificaPermissao('010113',false));
        }else {
            $this->frame_codvendedorcliente=$this->xml->get_widget('frame_codvendedorcliente');
            $this->frame_codvendedorcliente->set_sensitive(false);
        }


// outros WIDGETS *********************
        //echo "FIXME: imagens\n";

        $this->pixmap_foto=$this->xml->get_widget('pixmap_foto');
        //$this->vbox_foto=$this->xml->get_widget('vbox_foto');
        $button_busca_foto=$this->xml->get_widget('button_busca_foto');
        $button_busca_foto->connect_simple('clicked', array(&$this,'buscar_foto_geral'),$this->pixmap_foto, array('200','270'));
        $button_limpar_foto=$this->xml->get_widget('button_limpar_foto');
        $button_limpar_foto->connect_simple('clicked', array($this,'limpar_foto_geral'));
        //$button_ver_foto=$this->xml->get_widget('button_ver_foto');
        //$button_ver_foto->connect_simple('clicked', array(&$this,'ver_foto_geral'));


        $button_novo=$this->xml->get_widget('button_novo');
        $button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->set_sensitive($this->verificaPermissao($this->permissaoIncluir,false));

        $button_primeiro=$this->xml->get_widget('button_primeiro');
        $button_ultimo=$this->xml->get_widget('button_ultimo');
        $button_proximo=$this->xml->get_widget('button_proximo');
        $button_anterior=$this->xml->get_widget('button_anterior');
        $button_excluir=$this->xml->get_widget('button_excluir');
        $button_excluir->set_sensitive($this->verificaPermissao($this->permissaoExcluir,false));

        $button_alterar=$this->xml->get_widget('button_alterar');
        $button_alterar->set_sensitive($this->verificaPermissao($this->permissaoAlterar,false));

        $button_novo->connect_simple('clicked', confirma, array(&$this, 'func_novo'),'Deseja cancelar a digitacao atual e inserir um novo registro?', false,null);
        $button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Os dados digitados estao corretos?',false,&$this->permissaoIncluir);
        $button_primeiro->connect_simple('clicked', array(&$this, cadastro_primeiro), $this->tabela, $this->tabela,'codigo','func_novo','	atualiza');
        $button_ultimo->connect_simple('clicked', array(&$this, cadastro_ultimo), $this->tabela, $this->tabela,'codigo','func_novo','atualiza');
        $button_proximo->connect_simple('clicked', array(&$this, cadastro_proximo), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo);
        $button_anterior->connect_simple('clicked', array(&$this, cadastro_anterior), $this->tabela, $this->tabela,'codigo','func_novo','atualiza',&$this->entry_codigo);
        $button_excluir->connect_simple('clicked', confirma, array(&$this, 'excluirGeral'),'Deseja excluir este registro?',true,&$this->permissaoExcluir);
        $button_alterar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Deseja alterar este registro?',true,&$this->permissaoAlterar);

        $this->func_novo();

        if($this->tabela=="clientes" or $this->tabela=="funcionarios") {
            $this->combo_natureza->set_active(0); // fisica
        }else {
            $this->combo_natureza->set_active(1); // juridica
        }

        //$this->janela->show();
    }
    function changedNatureza() {

        if($this->combo_natureza->get_active()==0) { // fisica
            $fisica=TRUE;
            $this->label_dtnasc->set_text("Dt.Nasc.");
            $this->label_rg->set_text("RG - Carteira de Identidade");
            $this->label_cpf->set_text("CPF");
        }else { // juridica
            $fisica=FALSE;
            $this->label_dtnasc->set_text("Dt.Inicio");
            $this->label_rg->set_text("Inscricao Estadual");
            $this->label_cpf->set_text("CNPJ");
        }
        $this->frame_sexo->set_sensitive($fisica);
        $this->entry_orgaorg->set_sensitive($fisica);
        $this->entry_dtemissaorg->set_sensitive($fisica);
        $this->entry_im->set_sensitive(!$fisica);
        $this->entry_ir->set_sensitive(!$fisica);
        $this->entry_titulo->set_sensitive($fisica);
        $this->entry_carteira->set_sensitive($fisica);
        $this->entry_pis->set_sensitive($fisica);
        $this->vbox_familia->set_sensitive($fisica);
        $this->vbox_profissao->set_sensitive($fisica);
    }
    function excluirGeral() {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $codigo=$this->entry_codigo->get_text();
        $sql="";
        if($codigo<>"") {
            $sql="DELETE FROM ".$this->tabela." WHERE codigo='$codigo'; ";
            if (!$con->Query($sql)) {
                msg("Erro ao excluir");
                return;
            }
            $sql="DELETE FROM cadastro2enderecos WHERE codigo='$codigo' AND cadastro='$this->tabela';";
            $sql.="DELETE FROM cadastro2contatos WHERE codigo='$codigo' AND cadastro='$this->tabela';";
            $sql.="DELETE FROM cadastro2profissao WHERE codigo='$codigo' AND cadastro='$this->tabela';";
            $sql.="DELETE FROM cadastro2familias WHERE codigo='$codigo' AND cadastro='$this->tabela';";
            $sql.="DELETE FROM cadastro2bancos WHERE codigo='$codigo' AND cadastro='$this->tabela';";
            if ($con->Query($sql)) {
                $this->status('Registro excluido com sucesso');
            }else {
                msg("Erro ao excluir");
                return;
            }
            $this->func_novo();
        }
        else {
            msg('Registro NAO excluido. "Codigo" em branco!');
        }
        $this->con_geral->Disconnect();
        //$this->button_atualiza_clist->clicked();
        $this->decideSeAtualizaClist();
    }

    function abre_clist_geral() {
        $this->cria_clist_cadastro($this->tabela, "nome", "codigo", &$this->entry_nome, $this->tabela, "select codigo, nome, cnpj_cpf, ie_rg, inativo from ".$this->tabela." as ".$this->tabela, true, array(true,$this->permissaoExcluir), 'cor_treeview_geral',array(null,null,null,null, 0));
    }


    function buscar_foto_geral($widget,$resize=null) {
        $this->buscar_foto($widget,$resize,'$this->fotoGeral');
    }

    function limpar_foto_geral() {
        $this->fotoGeralLimpa=true;
        $this->fotoGeral="";
        $this->limpar_foto($this->pixmap_foto);
        $this->LastImageMostraFoto="";
    }

    function func_novo() {
        $this->limpar_foto_geral();

// aba basico ***********
        $this->entry_codigo->set_text('');
        $this->entry_nome->set_text('');
        $this->entry_dtnasc->set_text($this->datadehoje);
        $this->entry_dtcadastro->set_text($this->datadehoje);
        $this->entry_ultvenda->set_text('01-01-0001');
        $this->entry_ultcompra->set_text('01-01-0001');
        $this->checkbutton_inativo->set_active(false);
        $this->radiobutton_masculino->set_active(true);

        $this->textBuffer_obs->set_text('');
        $this->textBuffer_referencias->set_text('');

// aba documentos
        $this->entry_cpf->set_text('');
        $this->entry_estadoie->set_text(retorna_CONFIG("Estado"));
        $this->entry_rg->set_text('');
        $this->entry_orgaorg->set_text('');
        $this->entry_dtemissaorg->set_text('01-01-0001');
        $this->entry_im->set_text('');
        $this->entry_ir->set_text('');
        $this->entry_titulo->set_text('');
        $this->entry_carteira->set_text('');
        $this->entry_pis->set_text('');

// aba enderecos
        $this->limpaEndereco();
        $this->liststore_enderecos->clear();
// aba contatos
        $this->limpaContato();
        $this->liststore_contato->clear();
// aba grp familiar
        $this->limpaFamilia();
        $this->liststore_familia->clear();
// aba profissional
        $this->limpaProfissao();
        $this->liststore_profissao->clear();

// aba financeiro
        $this->limpaFinanceiro();
        $this->liststore_bancos->clear();
        $this->entry_comissaovendedor->set_text('');
        $this->entry_codvendedorcliente->set_text('');
        $this->label_codvendedorcliente->set_text('');
    }

    function limpaContato() {
        $this->entry_nomecontato->set_text('');
        $this->entry_departamentocontato->set_text('');
        $this->entry_telefonecontato->set_text('');
        $this->entry_emailcontato->set_text('');
        $this->entry_obscontato->set_text('');
    }

    function limpaFamilia() {
        $this->entry_codparentesco->set_text('');
        $this->label_codparentesco->set_text('<< Pressione ENTER');
        $this->entry_nomefamilia->set_text('');
        $this->entry_dtnascfamilia->set_text('01-01-0001');
        $this->entry_obsfamilia->set_text('');
    }

    function limpaFinanceiro() {
        $this->entry_codbanco->set_text('');
        $this->label_codbanco->set_text('');
        $this->entry_comissao->set_text('');
        $this->entry_debmaximo->set_text('');
    }

    function limpaEndereco() {
        $this->entry_descricao->set_text('');
        $this->entry_romaneio->set_text('');
        $this->label_romaneio->set_text('');
        $this->entry_endereco->set_text('');
        $this->entry_numero->set_text('');
        $this->entry_complemento->set_text('');
        $this->entry_bairro->set_text('');
        $this->entry_cep->set_text(retorna_CONFIG("CEP"));
        $this->entry_cidade->set_text(retorna_CONFIG("Cidade"));
        $this->entry_estado->set_text(retorna_CONFIG("Estado"));
        $this->entry_telefone->set_text(retorna_CONFIG("DDD"));
        $this->entry_fax->set_text(retorna_CONFIG("DDD"));
        $this->entry_celular->set_text(retorna_CONFIG("DDD"));
        $this->entry_email->set_text('');
        $this->entry_site->set_text('');
    }

    function limpaProfissao() {
        $this->entry_codprof->set_text('');
        $this->label_codprof->set_text('ENTER para buscar');
        $this->entry_codempregador->set_text('');
        $this->label_codempregador->set_text('ENTER para buscar');
        $this->entry_descricaoprof->set_text('');
        $this->entry_rendaprof->set_text('');
        $this->entry_obsprof->set_text('');
    }
    function func_gravar($alterar) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_geral=&new $BancoDeDados;

// aba basico
        $this->codigo=$this->entry_codigo->get_text();
        if($alterar and empty($this->codigo)) {
            msg('Codigo em branco! Impossivel alterar');
            return;
        }
        $this->natureza=$this->combo_natureza->get_active_text();
        $this->nome=strtoupper($this->entry_nome->get_text());
        if(empty($this->nome)) {
            msg('Preencha o campo nome!');
            return;
        }
        $this->nome=$this->con_geral->EscapeString($this->nome);

        $this->dtcadastro=$this->entry_dtcadastro->get_text();
        if($this->valida_data($this->dtcadastro)) {
            $this->dtcadastro=$this->corrigeNumero($this->dtcadastro,"dataiso");
        }else {
            msg("Data de cadastro incorreta!");
            return;
        }
        $masculino=$this->radiobutton_masculino->get_active();
        if ($masculino) {
            $this->sexo='M';
        } else {
            $this->sexo='F';
        }

        if($this->checkbutton_inativo->get_active()) {
            $this->inativo='1';
        }else {
            $this->inativo='0';
        }

        $this->codmidiapropaganda=$this->entry_comoconheceu->get_text();
        if(!empty($this->codmidiapropaganda)) {
            if (!$this->retornabusca2('midiapropaganda', $this->entry_comoconheceu, $this->label_comoconheceu, 'codigo', 'descricao', 'midiapropaganda')) {
                msg('Preencha corretamente o campo "Como Conheceu" na aba "Basico"!');
                return;
            }
        }

        $this->dtnasc=$this->entry_dtnasc->get_text();
        if($this->valida_data($this->dtnasc)) {
            $this->dtnasc=$this->corrigeNumero($this->dtnasc,"dataiso");
        }else {
            msg("Data de nascimento/inicio incorreta!");
            return;
        }


        $this->obs=$this->textBuffer_obs->get_text(
                $this->textBuffer_obs->get_start_iter(),
                $this->textBuffer_obs->get_end_iter()
        );
        $this->obs=$this->con_geral->EscapeString($this->obs);

        $this->referencias=$this->textBuffer_referencias->get_text(
                $this->textBuffer_referencias->get_start_iter(),
                $this->textBuffer_referencias->get_end_iter()
        );
        $this->referencias=$this->con_geral->EscapeString($this->referencias);


// aba documentos
        $this->cpf=strtoupper($this->entry_cpf->get_text());
        $this->rg=strtoupper($this->entry_rg->get_text());
        if ($this->combo_natureza->get_active_text()=="Fisica") {
            if(!empty($this->cpf)) {
                if(!$this->CalculaCPF($this->cpf)) {
                    msg('CPF invalido!');
                    return;
                }
                //if($this->ja_cadastrado($this->tabela,'cnpj_cpf',$this->cpf,'codigo', $this->codigo)){
                if($this->ja_cadastrado($this->tabela,'cnpj_cpf',$this->cpf) and !$alterar) {
                    msg('CPF ja cadastrado!');
                    return;
                }
            }else {
                if(!$this->verificaPermissao('010108',true,'CPF obrigatorio')) {
                    return;
                }
            }
            if(empty($this->rg)) {
                if(!$this->verificaPermissao('010109',true,'RG obrigatorio')) {
                    return;
                }
            }
        }elseif($this->combo_natureza->get_active_text()=="Juridica") {
            if(!$this->valida_IE($this->rg,$this->tabela,&$this->entry_estadoie) and !empty($this->rg)) {
                msg('Inscricao Estadual invalida!');
                return;
            }else {
                if(!$this->verificaPermissao('010111',true,'Inscricao Estadual obrigatoria')) {
                    return;
                }
                if(!empty($this->rg)) {
                    if($this->ja_cadastrado($this->tabela,'ie_rg',$this->rg) and !$alterar) {
                        msg('Inscricao Estadual ja cadastrada!');
                        return;
                    }
                }
            }
            if(!empty($this->cpf)) {
                if(!$this->CalculaCNPJ($this->cpf)) {
                    msg('CNPJ invalido!');
                    return;
                }
                if($this->ja_cadastrado($this->tabela,'cnpj_cpf',$this->cpf) and !$alterar) {
                    msg('CNPJ ja cadastrado!');
                    return;
                }
            }else {
                if(!$this->verificaPermissao('010110',true,'CNPJ obrigatorio')) {
                    return;
                }
            }
        }
        $this->orgaorg=strtoupper($this->entry_orgaorg->get_text());
        $this->dtemissaorg=$this->entry_dtemissaorg->get_text();
        if(!$this->valida_data($this->dtemissaorg)) {
            msg("Data de emissao do RG incorreta!");
            return;
        }else {
            $this->dtemissaorg=$this->corrigeNumero($this->dtemissaorg,"dataiso");
        }
        $this->im=strtoupper($this->entry_im->get_text());
        $this->ir=strtoupper($this->entry_ir->get_text());
        $this->pis=strtoupper($this->entry_pis->get_text());
        if(!$this->valida_PIS($this->pis) and !empty($this->pis)) {
            msg('PIS invalido!');
            return;
        }
        $this->titulo=$this->entry_titulo->get_text();
        if(!$this->valida_TitEleitor($this->titulo) and !empty($this->titulo)) {
            msg('Titulo de Eleitor invalido!');
            return;
        }
        $this->carteira=strtoupper($this->entry_carteira->get_text());

// aba financeiro
        $this->debmaximo=$this->pegaNumero($this->entry_debmaximo);
        $this->comissao=$this->pegaNumero($this->entry_comissao);
        $this->habcomprar=$this->entry_habcomprar->get_text();

        // pega foto
        if(!empty($this->fotoGeral)) {
            $this->fotoGeral=$this->con_geral->EscapeStringFOTO($this->fotoGeral);
        }

        // registra ultima alteracao
        $this->ultimaaltera=$this->corrigeNumero($this->ultimaaltera,"dataiso");

        $this->comissaovendedor=$this->pegaNumero($this->entry_comissaovendedor);
        $this->codvendedorcliente=$this->pegaNumero($this->entry_codvendedorcliente);
        if(!empty($this->codvendedorcliente)) {
            if (!$this->retornabusca2('funcionarios', $this->entry_codvendedorcliente, $this->label_codvendedorcliente, 'codigo', 'nome', 'funcionarios')) {
                msg('Preencha corretamente o campo Codigo do Vendedor ou deixe em branco!');
                return;
            }
        }

        // gravando dados no SQL
        $this->grava_dados_geral($alterar);

        // atualiza clist
        //$this->button_atualiza_clist->clicked();
        $this->decideSeAtualizaClist();

    }

    function atualiza($resultado) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_geral=&new $BancoDeDados;
        $resultado2=$this->con_geral->FetchArray($resultado);
        // aba basico
        $this->entry_codigo->set_text($resultado2["codigo"]);
        if($resultado2["natureza"]=="Fisica") {
            $this->combo_natureza->set_active(0);
        }else {
            $this->combo_natureza->set_active(1);
        }

        $this->entry_nome->set_text($resultado2["nome"]);
        $this->entry_dtcadastro->set_text($this->corrigeNumero($resultado2["dtcadastro"],"data"));
        $this->entry_dtnasc->set_text($this->corrigeNumero($resultado2["dtnasc"],"data"));
        if ($resultado2["sexo"]=='M') {
            $this->radiobutton_masculino->set_active(true);
        } else {
            $this->radiobutton_feminino->set_active(true);
        }
        $this->checkbutton_inativo->set_active($resultado2["inativo"]);
        $this->entry_ultvenda->set_text($this->corrigeNumero($resultado2["ultvenda"],"data"));
        $this->entry_ultcompra->set_text($this->corrigeNumero($resultado2["ultcompra"],"data"));

        $this->textBuffer_obs->set_text($resultado2["obs"]);
        $this->textBuffer_referencias->set_text($resultado2["referencias"]);

        $this->entry_comoconheceu->set_text($resultado2["codmidiapropaganda"]);
        $this->retornabusca2('midiapropaganda', $this->entry_comoconheceu, $this->label_comoconheceu, 'codigo', 'descricao', 'midiapropaganda');

        // aba documentos
        $this->entry_cpf->set_text($resultado2["cnpj_cpf"]);
        $this->entry_rg->set_text($resultado2["ie_rg"]);
        $this->entry_orgaorg->set_text($resultado2["orgaorg"]);
        $this->entry_dtemissaorg->set_text($this->corrigeNumero($resultado2["dtemissaorg"],"data"));
        $this->entry_im->set_text($resultado2["im"]);
        $this->entry_ir->set_text($resultado2["ir"]);
        $this->entry_carteira->set_text($resultado2["carteira"]);
        $this->entry_pis->set_text($resultado2["pis"]);
        $this->entry_titulo->set_text($resultado2["titulo"]);

        // aba enderecos
        $this->limpaEndereco();

        $this->liststore_enderecos->clear();
        $this->con_geral->Connect();
        $sql="SELECT descricao, romaneio, endereco, numero, complemento, bairro, estado, cidade, cep, telefone, fax, celular, email, site FROM cadastro2enderecos WHERE (codigo='".$resultado2["codigo"]."' AND cadastro='".$this->tabela."');";
        $resultado=$this->con_geral->Query($sql);
        while($i = $this->con_geral->FetchRow($resultado)) {
            $descricao=$i[0];
            $romaneio=$i[1];
            $endereco=$i[2];
            $numero=$i[3];
            $complemento=$i[4];
            $bairro=$i[5];
            $cidade=$i[6];
            $estado=$i[7];
            $cep=$i[8];
            $telefone=$i[9];
            $fax=$i[10];
            $celular=$i[11];
            $email=$i[12];
            $site=$i[13];
            //$romaneio=$i[13];
            $this->liststore_enderecos->append(array($descricao, $romaneio, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $cep, $telefone, $fax, $celular, $email, $site));
        }
// aba contatos
        $this->liststore_contato->clear();
        $this->con_geral->Connect();
        $sql="SELECT c.nome, c.departamento, c.telefone, c.email, c.obs FROM cadastro2contatos AS c WHERE (c.codigo='".$resultado2["codigo"]."' AND c.cadastro='".$this->tabela."');";
        $resultado=$this->con_geral->Query($sql);
        while($i = $this->con_geral->FetchRow($resultado)) {
            $this->liststore_contato->append(array($i[0], $i[1], $i[2], $i[3], $i[4]));
        }

// aba profissional

        // mostra lista de profissoes
        $this->liststore_profissao->clear();
        $this->con_geral->Connect();
        $sql="SELECT c.descricao, c.codempregador, e.nome, c.codprofissao, p.descricao, c.renda, c.obs FROM cadastro2profissao AS c INNER JOIN profissao AS p ON (c.codprofissao=p.codigo) INNER JOIN empregador AS e ON (c.codempregador=e.codigo) WHERE (c.codigo='".$resultado2["codigo"]."' AND c.cadastro='".$this->tabela."');";
        $resultado=$this->con_geral->Query($sql);
        while($i = $this->con_geral->FetchRow($resultado)) {
            $this->liststore_profissao->append(array($i[0], $i[1], $i[2], $i[3], $i[4], $i[5], $i[6]));
        }
//aba familia
        $this->liststore_familia->clear();
        $this->con_geral->Connect();
        $sql="SELECT f.codparentesco, p.descricao, f.nome, f.dtnasc, f.obs FROM cadastro2familias AS f INNER JOIN parentesco AS p ON (f.codparentesco=p.codigo) WHERE (f.codigo='".$resultado2["codigo"]."' AND f.cadastro='".$this->tabela."');";
        $resultado=$this->con_geral->Query($sql);
        while($i = $this->con_geral->FetchRow($resultado)) {
            $this->liststore_familia->append(array($i[0], $i[1], $i[2], $i[3], $i[4]));
        }

        // aba financeiro
        $this->entry_debmaximo->set_text($this->mascara2($resultado2["debmaximo"], 'virgula2'));
        $this->entry_comissao->set_text($this->mascara2($resultado2["comissao"], 'virgula2'));
        $this->entry_habcomprar->set_text($resultado2["habcomprar"]);

        // mostra lista de bancos
        $this->liststore_bancos->clear();
        $this->con_geral->Connect();
        $sql="SELECT c.codbanco,nb.nome,b.titular,b.agencia,b.conta FROM cadastro2bancos AS c INNER JOIN bancos AS b ON (c.codbanco=b.codbanco) INNER JOIN nomebanco AS nb ON nb.codigo=b.numero WHERE (c.codigo='".$resultado2["codigo"]."' AND c.cadastro='".$this->tabela."');";
        $resultado=$this->con_geral->Query($sql);
        while($i = $this->con_geral->FetchRow($resultado)) {
            $codbanco=$i[0];
            $banco=$i[1];
            $titular=$i[2];
            $agencia=$i[3];
            $conta=$i[4];
            $this->liststore_bancos->append(array($codbanco,$banco,$titular,$agencia,$conta));
        }
        $this->entry_codvendedorcliente->set_text($resultado2["codvendedor"]);
        $this->retornabusca2('funcionarios', $this->entry_codvendedorcliente, $this->label_codvendedorcliente, 'codigo', 'nome', 'funcionarios');
        $this->entry_comissaovendedor->set_text($this->mascara2($resultado2["comissaovendedor"], 'virgula2'));

        $this->limpar_foto_geral();
        $this->mostra_foto($this->pixmap_foto,array('90','120'),false,$this->con_geral->UnEscapeStringFOTO($resultado2["foto"]),'$this->fotoGeral');

    }

    function incluirBanco($refazer=false) {
        $codbanco=$this->entry_codbanco->get_text();
        $sql="SELECT nb.nome,b.titular,b.agencia,b.conta FROM bancos as b left join nomebanco as nb on b.numero=nb.codigo WHERE b.codbanco='$codbanco'";
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_geral=&new $BancoDeDados;
        $this->con_geral->Connect();
        $resultado=$this->con_geral->Query($sql);
        if ($this->con_geral->NumRows($resultado)>0) {
            $resultado2=$this->con_geral->FetchArray($resultado);
            $banco=$resultado2[0];
            $titular=$resultado2[1];
            $agencia=$resultado2[2];
            $conta=$resultado2[3];
            if(!$refazer) {
                $this->verificaSeExisteAUX=false;
                $this->liststore_bancos->foreach( array($this,'verificaSeExisteNaLista'), 0, $codbanco);
                if (!$this->verificaSeExisteAUX) {
                    $this->liststore_bancos->append(array($codbanco,$banco,$titular,$agencia,$conta));
                }
            }else {// refazer
                $selecionado=$this->treeview_bancos->get_selection();
                if($iter=$this->get_iter_liststore($selecionado,$this->liststore_bancos)) {
                    $this->liststore_bancos->set($iter,0,$codbanco);
                    $this->liststore_bancos->set($iter,1,$banco);
                    $this->liststore_bancos->set($iter,2,$titular);
                    $this->liststore_bancos->set($iter,3,$agencia);
                    $this->liststore_bancos->set($iter,4,$conta);
                }
            }
        } else {
            msg('Codigo do Banco nao encontrado');
        }
        $this->con_geral->Disconnect();

    }

    function excluirBanco() {
        $selecionado=$this->treeview_bancos->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_bancos)) {
            $this->liststore_bancos->remove($iter);
        }
    }

    function incluirProfissao($refazer=false) {
        $descricao=strtoupper($this->entry_descricaoprof->get_text());
        if(empty($descricao)) {
            msg('Preencha o campo Descricao!');
            return;
        }
        $codprof=$this->entry_codprof->get_text();
        if (!$this->retornabusca2('profissao', $this->entry_codprof, $this->label_codprof, 'codigo', 'descricao')) {
            msg('Preencha corretamente o campo Profissao!');
            return;
        }
        $prof=$this->label_codprof->get_text();

        $codempregador=$this->entry_codempregador->get_text();
        if (!$this->retornabusca2('empregador', &$this->entry_codempregador, $this->label_codempregador, 'codigo', 'nome')) {
            msg('Preencha corretamente o campo Empregador! Se for autonomo, cadastre um empregador com nome de "AUTONOMO""');
            return;
        }
        $empregador=$this->label_codempregador->get_text();
        $rendaprof=$this->pegaNumero($this->entry_rendaprof);
        if(empty($rendaprof)) {
            $renda='0,00';
        }
        $rendaprof=$this->mascara2($rendaprof,'moeda');
        $obs=$this->entry_obsprof->get_text();

        if(!$refazer) {
            $this->verificaSeExisteAUX=false;
            $this->liststore_profissao->foreach( array($this,'verificaSeExisteNaLista'), 0, $descricao);
            if (!$this->verificaSeExisteAUX) {
                $this->liststore_profissao->append(array($descricao, $codempregador, $empregador, $codprof, $prof, $rendaprof,$obs));
                $this->limpaProfissao();
            }
        }else {// refazer
            $selecionado=$this->treeview_profissao->get_selection();
            if($iter=$this->get_iter_liststore($selecionado,$this->liststore_profissao)) {
                $this->liststore_profissao->set($iter,0,$descricao);
                $this->liststore_profissao->set($iter,1,$codempregador);
                $this->liststore_profissao->set($iter,2,$empregador);
                $this->liststore_profissao->set($iter,3,$codprof);
                $this->liststore_profissao->set($iter,4,$prof);
                $this->liststore_profissao->set($iter,5,$rendaprof);
                $this->liststore_profissao->set($iter,6,$obs);
                $this->limpaProfissao();
            }
        }
    }

    function excluirProfissao() {
        $selecionado=$this->treeview_profissao->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_profissao)) {
            $this->liststore_profissao->remove($iter);
        }
    }
    function clicaProfissao($treeview,$path,$coluna) {
        if($selecionado=$treeview->get_selection()) {
            $this->entry_descricaoprof->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,0));
            $this->entry_codempregador->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,1));
            $this->label_codempregador->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,2));
            $this->entry_codprof->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,3));
            $this->label_codprof->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,4));
            $this->entry_rendaprof->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,5));
            $this->entry_obsprof->set_text($this->get_valor_liststore($selecionado, $this->liststore_profissao,6));
        }
    }

    function incluirContato($refazer=false) {
        $nome=strtoupper($this->entry_nomecontato->get_text());
        if(empty($nome)) {
            msg('Preencha o campo Nome!');
            return;
        }
        $departamento=strtoupper($this->entry_departamentocontato->get_text());
        $telefone=$this->entry_telefonecontato->get_text();
        $email=$this->entry_emailcontato->get_text();
        if(!empty($email) and !$this->valida_email($email)) {
            msg('E-Mail invalido!');
            return;
        }
        $obs=strtoupper($this->entry_obscontato->get_text());

        if(!$refazer) {
            $this->verificaSeExisteAUX=false;
            $this->liststore_contato->foreach( array($this,'verificaSeExisteNaLista'), 0, $nome);
            if (!$this->verificaSeExisteAUX) {


                $this->liststore_contato->append(array($nome, $departamento, $telefone, $email, $obs));

                $this->limpaContato();
            }
        }else { //refazer
            $selecionado=$this->treeview_contato->get_selection();
            if($iter=$this->get_iter_liststore($selecionado,$this->liststore_contato)) {
                $this->liststore_contato->set($iter,0,$nome);
                $this->liststore_contato->set($iter,1,$departamento);
                $this->liststore_contato->set($iter,2,$telefone);
                $this->liststore_contato->set($iter,3,$email);
                $this->liststore_contato->set($iter,4,$obs);
                $this->limpaContato();
            }
        }
        $this->entry_nomecontato->grab_focus();
    }

    function excluirContato() {
        $selecionado=$this->treeview_contato->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_contato)) {
            $this->liststore_contato->remove($iter);
        }
    }
    function clicaContato($treeview,$path,$coluna) {
        if($selecionado=$treeview->get_selection()) {
            $this->entry_nomecontato->set_text($this->get_valor_liststore($selecionado, $this->liststore_contato,0));
            $this->entry_departamentocontato->set_text($this->get_valor_liststore($selecionado, $this->liststore_contato,1));
            $this->entry_telefonecontato->set_text($this->get_valor_liststore($selecionado, $this->liststore_contato,2));
            $this->entry_emailcontato->set_text($this->get_valor_liststore($selecionado, $this->liststore_contato,3));
            $this->entry_obscontato->set_text($this->get_valor_liststore($selecionado, $this->liststore_contato,4));
        }
    }
    function incluirFamilia($refazer=false) {
        $codparentesco=$this->entry_codparentesco->get_text();
        if (!$this->retornabusca2('parentesco', $this->entry_codparentesco, $this->label_codparentesco, 'codigo', 'descricao')) {
            msg('Preencha corretamente o campo Parentesco!');
            return;
        }
        $parente=$this->label_codparentesco->get_text();
        $nome=strtoupper($this->entry_nomefamilia->get_text());
        if(empty($nome)) {
            msg('Preencha o campo Nome!');
            return;
        }
        $dtnasc=strtoupper($this->entry_dtnascfamilia->get_text());
        if($this->valida_data($dtnasc)) {
            $dtnasc=$this->corrigeNumero($dtnasc,"dataiso");
        }else {
            msg("Data de nascimento incorreta!");
            return;
        }
        $obs=strtoupper($this->entry_obsfamilia->get_text());
        if(!$refazer) {
            $this->verificaSeExisteAUX=false;
            $this->liststore_familia->foreach(array($this,'verificaSeExisteNaLista'), 2, $nome, false);
            if (!$this->verificaSeExisteAUX) {

                $this->liststore_familia->append(array($codparentesco, $parente, $nome, $dtnasc, $obs));
                $this->limpaFamilia();
            }
        }else { // refazer
            $selecionado=$this->treeview_familia->get_selection();
            if($iter=$this->get_iter_liststore($selecionado,$this->liststore_familia)) {
                $this->liststore_familia->set($iter,0,$codparentesco);
                $this->liststore_familia->set($iter,1,$parente);
                $this->liststore_familia->set($iter,2,$nome);
                $this->liststore_familia->set($iter,3,$dtnasc);
                $this->liststore_familia->set($iter,4,$obs);
                $this->limpaFamilia();
            }
        }

        $this->entry_codparentesco->grab_focus();
    }

    function excluirFamilia() {
        $selecionado=$this->treeview_familia->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_familia)) {
            $this->liststore_familia->remove($iter);
        }
    }
    function refazerFamilia() {
        $selecionado=$this->treeview_familia->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_familia)) {
            $this->liststore_familia->set($iter,0,$codparentesco);
        }
    }

    function clicaFamilia($treeview,$path,$coluna) {
        if($selecionado=$treeview->get_selection()) {
            $this->entry_codparentesco->set_text($this->get_valor_liststore($selecionado, $this->liststore_familia,0));
            $this->label_codparentesco->set_text($this->get_valor_liststore($selecionado, $this->liststore_familia,1));
            $this->entry_nomefamilia->set_text($this->get_valor_liststore($selecionado, $this->liststore_familia,2));
            $this->entry_dtnascfamilia->set_text($this->corrigeNumero($this->get_valor_liststore($selecionado, $this->liststore_familia,3),"data"));
            $this->entry_obsfamilia->set_text($this->get_valor_liststore($selecionado, $this->liststore_familia,4));
        }
    }
    function incluirEnderecos($refazer=false) {
        $descricao=strtoupper($this->entry_descricao->get_text());
        if(empty($descricao)) {
            msg('Preencha o campo Descricao!');
            return;
        }
        $romaneio=$this->entry_romaneio->get_text();
        if(!empty($romaneio)) {
            if (!$this->retornabusca2('romaneio', &$this->entry_romaneio, &$this->label_romaneio, 'codigo', 'descricao', 'geral')) {
                msg('Preencha corretamente o campo Romaneio ou deixe em branco');
                return;
            }
        }
        $endereco=strtoupper($this->entry_endereco->get_text());
        $numero=strtoupper($this->entry_numero->get_text());
        $complemento=strtoupper($this->entry_complemento->get_text());
        $bairro=strtoupper($this->entry_bairro->get_text());
        $cidade=strtoupper($this->entry_cidade->get_text());
        $estado=strtoupper($this->entry_estado->get_text());
        $cep=strtoupper($this->entry_cep->get_text());
        if(!empty($cep) and !$this->valida_CEP($cep,&$this->tabela,&$this->entry_estado)) {
            msg('CEP invalido!');
            return;
        }
        $telefone=strtoupper($this->entry_telefone->get_text());
        $fax=strtoupper($this->entry_fax->get_text());
        $celular=strtoupper($this->entry_celular->get_text());
        $email=strtolower($this->entry_email->get_text());
        if(!empty($email) and !$this->valida_email($email)) {
            msg('E-Mail invalido!');
            return;
        }
        $site=strtolower($this->entry_site->get_text());

        // verifica permissoes
        if(!$this->validaTelefone($telefone)) {
            if(!$this->verificaPermissao('010112',true,'Telefone Valido Obrigatorio')) {
                return;
            }
        }
        if(!$refazer) {
            // verifica se esta descricao j�existe na Clist
            $this->verificaSeExisteAUX=false;
            $this->liststore_enderecos->foreach( array($this,'verificaSeExisteNaLista'), 0, $descricao);
            if (!$this->verificaSeExisteAUX) {
                $this->liststore_enderecos->append(array($descricao,$romaneio,$endereco,$numero,$complemento,$bairro,$estado,$cidade,$cep,$telefone,$fax,$celular,$email,$site));
                $this->limpaEndereco();
            }
        }else { // refazer
            $selecionado=$this->treeview_enderecos->get_selection();
            if($iter=$this->get_iter_liststore($selecionado,$this->liststore_enderecos)) {
                $this->liststore_enderecos->set($iter,0,$descricao);
                $this->liststore_enderecos->set($iter,1,$romaneio);
                $this->liststore_enderecos->set($iter,2,$endereco);
                $this->liststore_enderecos->set($iter,3,$numero);
                $this->liststore_enderecos->set($iter,4,$complemento);
                $this->liststore_enderecos->set($iter,5,$bairro);
                $this->liststore_enderecos->set($iter,6,$estado);
                $this->liststore_enderecos->set($iter,7,$cidade);
                $this->liststore_enderecos->set($iter,8,$cep);
                $this->liststore_enderecos->set($iter,9,$telefone);
                $this->liststore_enderecos->set($iter,10,$fax);
                $this->liststore_enderecos->set($iter,11,$celular);
                $this->liststore_enderecos->set($iter,12,$email);
                $this->liststore_enderecos->set($iter,13,$site);
                $this->limpaEndereco();
            }
        }

    }



    function excluirEnderecos() {
        $selecionado=$this->treeview_enderecos->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_enderecos)) {
            $this->liststore_enderecos->remove($iter);
        }
    }

    function clicaEndereco($treeview,$path,$coluna) {
        if($selecionado=$treeview->get_selection()) {
            $this->entry_descricao->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,0));

            $this->entry_romaneio->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,1));
            $this->retornabusca2('romaneio', $this->entry_romaneio, $this->label_romaneio, 'codigo', 'descricao', 'romaneio');

            $this->entry_endereco->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,2));
            $this->entry_numero->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,3));
            $this->entry_complemento->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,4));
            $this->entry_bairro->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,5));
            $this->entry_estado->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,6));
            $this->entry_cidade->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,7));
            $this->entry_cep->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,8));
            $this->entry_telefone->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,9));
            $this->entry_fax->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,10));
            $this->entry_celular->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,11));
            $this->entry_email->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,12));
            $this->entry_site->set_text($this->get_valor_liststore($selecionado,$this->liststore_enderecos,13));
        }
    }

    function procuracep() {
        // funcao que procura pelo cep digita e se existir preenche os entries
        $cep=$this->entry_cep->get_text();
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        // procura pelo CEP de ruas
        $sql="SELECT c.nome_log,l.nome_local,b.extenso_bai,l.uf_local FROM cep AS c INNER JOIN cep_loc AS l ON (l.chave_local=c.chvlocal_log) INNER JOIN cep_bai AS b ON (b.chave_bai=c.chvbai1_log) WHERE cep8_log='$cep'";
        $resultado=$con->Query($sql);
        if($con->NumRows($resultado)>0) {
            // se achou preenche os entries
            $i=$con->FetchRow($resultado);
            $this->entry_endereco->set_text($i[0]);
            $this->entry_bairro->set_text($i[2]);
            $this->entry_estado->set_text($i[3]);
            $this->entry_cidade->set_text($i[1]);
        }else {
            // se nao achou tenta procurar pelo cep da cidade
            $sql="SELECT nome_local,uf_local FROM cep_loc WHERE cep8_local='$cep'";
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                $i=$con->FetchRow($resultado);
                $this->entry_estado->set_text($i[1]);
                $this->entry_cidade->set_text($i[0]);
            }
        }
        return;
    }
}

?>
