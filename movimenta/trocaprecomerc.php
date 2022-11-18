<?php
// troca preco de mercadoria
class trocaprecomerc extends funcoes {
    function trocaprecomerc() {
        $this->xml=$this->carregaGlade("trocaprecomerc",$tituloWindow);

        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        $this->scrolledwindow_trocapreco=$this->xml->get_widget('scrolledwindow_trocapreco');
        $this->liststore_trocapreco=new GtkListStore(
                Gobject::TYPE_STRING, //0 cod
                Gobject::TYPE_STRING,  //1 descricao
                Gobject::TYPE_STRING,  //2 valor atual
                Gobject::TYPE_STRING,  //3 valor novo
                Gobject::TYPE_STRING  //4 nome do campo
        );
        $this->treeview_trocapreco=new GtkTreeView($this->liststore_trocapreco);
        $this->treeview_trocapreco->set_rules_hint(TRUE);
        $this->add_coluna_treeview(
                array('Cod.', 'Descricao', 'Atual', 'Alterado','Campo'),
                $this->treeview_trocapreco
        );
        /*$cell_renderer4 = new GtkCellRendererText();
		$colLanguage4 = new GtkTreeViewColumn('Campo', $cell_renderer4, 'text', 2);
		$colLanguage4->set_resizable(true);
		$this->treeview_trocapreco->append_column($colLanguage4);
		$colLanguage4->set_visible(false);*/
        $this->treeview_trocapreco->set_search_column(1);

        $this->scrolledwindow_trocapreco->add($this->treeview_trocapreco);
        $this->scrolledwindow_trocapreco->show_all();


        $this->entry_merc=$this->xml->get_widget('entry_merc');
        $this->label_merc=$this->xml->get_widget('label_merc');
        $this->entry_merc->connect('key_press_event',
                array($this,entry_enter),
                "SELECT m.codmerc, m.descricao, m.precovenda, m.unidade, m.estoqueatual, fa.nome, fo.nome, g.descricao, l.descricao, m.obs FROM mercadorias AS m LEFT JOIN fabricantes AS fa ON (fa.codigo=m.codfab) LEFT JOIN fornecedores AS fo ON (fo.codigo=m.codfor) LEFT JOIN grpmerc AS g ON (g.codigo=m.codgrpmerc) LEFT JOIN localarma AS l ON (l.codigo=m.codlocalarma) ORDER BY m.descricao",
                true,
                $this->entry_merc,
                $this->label_merc,
                "mercadorias",
                "descricao",
                "codmerc"
        );
        $this->entry_merc->connect_simple('focus-out-event',array(&$this,retornabusca22), 'mercadorias', $this->entry_merc, $this->label_merc, 'codmerc', 'descricao', 'mercadorias');


        $this->entry_codgrpmerc=$this->xml->get_widget('entry_codgrpmerc');
        $this->label_codgrpmerc=$this->xml->get_widget('label_codgrpmerc');
        $this->entry_codgrpmerc->connect('key_press_event',
                array($this,entry_enter),
                'select codigo, descricao from grpmerc',
                true,
                $this->entry_codgrpmerc,
                $this->label_codgrpmerc,
                "grpmerc",
                "descricao",
                "codigo"
        );
        $this->entry_codgrpmerc->connect_simple('focus-out-event',array(&$this,retornabusca22), 'grpmerc', &$this->entry_codgrpmerc, &$this->label_codgrpmerc, 'codigo', 'descricao', 'mercadorias');

        $this->entry_codlocalarma=$this->xml->get_widget('entry_codlocalarma');
        $this->label_codlocalarma=$this->xml->get_widget('label_codlocalarma');
        $this->entry_codlocalarma->connect('key_press_event',
                array($this,entry_enter),
                'select codigo, descricao from localarma',
                true,
                $this->entry_codlocalarma,
                $this->label_codlocalarma,
                "localarma",
                "descricao",
                "codigo"
        );
        $this->entry_codlocalarma->connect_simple('focus-out-event',array($this,retornabusca22), 'localarma', $this->entry_codlocalarma, $this->label_codlocalarma, 'codigo', 'descricao', 'mercadorias');


        $this->entry_codfor=$this->xml->get_widget('entry_codfor');
        $this->label_codfor=$this->xml->get_widget('label_codfor');
        $this->entry_codfor->connect('key_press_event',
                array($this,entry_enter),
                'select codigo, nome, contato, dtnasc, dtcadastro, cnpj_cpf, ie_rg from fornecedores',
                true,
                $this->entry_codfor,
                $this->label_codfor,
                "fornecedores",
                "nome",
                "codigo"
        );
        $this->entry_codfor->connect_simple('focus-out-event',array(&$this,retornabusca22), 'fornecedores', &$this->entry_codfor, &$this->label_codfor, 'codigo', 'nome', 'mercadorias');


        $this->entry_codfab=$this->xml->get_widget('entry_codfab');
        $this->label_codfab=$this->xml->get_widget('label_codfab');
        $this->entry_codfab->connect('key_press_event',
                array(&$this,entry_enter),
                'select codigo, nome, contato, dtnasc, dtcadastro, cnpj_cpf, ie_rg from fabricantes',
                true,
                &$this->entry_codfab,
                &$this->label_codfab,
                "fabricantes",
                "nome",
                "codigo"
        );
        $this->entry_codfab->connect_simple('focus-out-event',array(&$this,retornabusca22), 'fabricantes', &$this->entry_codfab, &$this->label_codfab, 'codigo', 'nome', 'mercadorias');

        $this->button_aplicar=$this->xml->get_widget('button_aplicar');
        $this->button_aplicar->connect_simple('clicked',confirma,array($this,'oktrocapreco'),"Deseja realmente alterar os precos no Banco de Dados?");

        $this->button_cancelar=$this->xml->get_widget('button_cancelar');
        $this->button_cancelar->connect_simple('clicked',array($this,'cancelartrocapreco'));

        $this->button_limpar=$this->xml->get_widget('button_limpar');
        $this->button_limpar->connect_simple('clicked',array($this,'limpartrocapreco'));

        $this->button_adicionarmerc=$this->xml->get_widget('button_adicionarmerc');
        $this->button_adicionarmerc->connect_simple('clicked',array($this,'adicionarmerc'));

        $this->button_adicionargrp=$this->xml->get_widget('button_adicionargrp');
        $this->button_adicionargrp->connect_simple('clicked',array($this,'adicionargrp'));

        $this->button_remover=$this->xml->get_widget('button_remover');
        $this->button_remover->connect_simple('clicked',array($this,'removerrmerc'));

        $this->button_preview=$this->xml->get_widget('button_preview');
        $this->button_preview->connect_simple('clicked',array($this,'previewtrocapreco'));

        $this->entry_precofixo=$this->xml->get_widget('entry_precofixo');
        $this->entry_precofixo->connect('key-press-event', array($this, mascaraNew),'virgula2');

        $this->radiobutton_precofixo=$this->xml->get_widget('radiobutton_precofixo');
        $this->radiobutton_precofixo->connect('toggled',array($this,'toggledprecofixo'));
        $this->radiobutton_precofixo->toggled();

        $this->entry_porcento=$this->xml->get_widget('entry_porcento');
        $this->entry_porcento->connect('key-press-event', array($this, mascaraNew),'virgula2');

        $this->radiobutton_porcento=$this->xml->get_widget('radiobutton_porcento');
        $this->radiobutton_porcento->connect('toggled',array($this,'toggledporcento'));
        $this->radiobutton_porcento->toggled();

        $this->entry_real=$this->xml->get_widget('entry_real');
        $this->entry_real->connect('key-press-event', array($this, mascaraNew),'virgula2');

        $this->radiobutton_real=$this->xml->get_widget('radiobutton_real');
        $this->radiobutton_real->connect('toggled',array($this,'toggledreal'));
        $this->radiobutton_real->toggled();


        $this->combo_campo=$this->xml->get_widget('combo_campo');

        $this->limpartrocapreco();
    }

    function toggledprecofixo($radio) {
        if($radio->get_active()==true) {
            $this->entry_precofixo->set_sensitive(true);
        }else {
            $this->entry_precofixo->set_sensitive(false);
        }
    }

    function toggledporcento($radio) {
        if($radio->get_active()==true) {
            $this->entry_porcento->set_sensitive(true);
        }else {
            $this->entry_porcento->set_sensitive(false);
        }
    }

    function toggledreal($radio) {
        if($radio->get_active()==true) {
            $this->entry_real->set_sensitive(true);
        }else {
            $this->entry_real->set_sensitive(false);
        }
    }
    function cancelartrocapreco() {
        $this->janela->hide();
    }

    function limpartrocapreco() {
        $this->liststore_trocapreco->clear();
        $this->entry_porcento->set_text('');
        $this->entry_real->set_text('');
        $this->entry_precofixo->set_text('');
        $this->entry_codfab->set_text('');
        $this->label_codfab->set_text('<< Pressione ENTER');
        $this->entry_codfor->set_text('');
        $this->label_codfor->set_text('<< Pressione ENTER');
        $this->entry_codgrpmerc->set_text('');
        $this->label_codgrpmerc->set_text('<< Pressione ENTER');
        $this->entry_codlocalarma->set_text('');
        $this->label_codlocalarma->set_text('<< Pressione ENTER');
        $this->entry_merc->set_text('');
        $this->label_merc->set_text('<< Pressione ENTER');
    }

    function adicionarmerc() {
        if(!$this->pegaCampoTrocaPreco()) return;
        $codmerc=$this->entry_merc->get_text();
        if(!$descricao=$this->retornabusca4('descricao','mercadorias','codmerc',$codmerc)) {
            msg('Mercadoria nao encontrada');
            return;
        }
        $precoatual=$this->retornabusca4('precovenda','mercadorias','codmerc',$codmerc);
        // verifica se mercadoria já existe!
        $this->verificaSeExisteAUX=false;
        $this->liststore_trocapreco->foreach(array($this,'verificaSeExisteNaLista'),0,$codmerc);
        if ($this->verificaSeExisteAUX) {
            return; // se valor ja existir na lista retorna
        }
        // adiciona na lista
        $this->liststore_trocapreco->append(array($codmerc,$descricao,number_format($precoatual,2,",",""),null,$this->campotrocapreco));
    }

    function adicionargrp() {
        if(!$this->pegaCampoTrocaPreco()) return;
        $sql="";
        $codgrpmerc=$this->entry_codgrpmerc->get_text();
        if(!$this->retornabusca2('grpmerc', &$this->entry_codgrpmerc, &$this->label_codgrpmerc, 'codigo', 'descricao') and !empty($codgrpmerc)) {
            msg('Preencha corretamente o campo Grupo de Mercadoria!');
            return;
        }
        if(!empty($codgrpmerc)) {
            $sql.=" codgrpmerc='$codgrpmerc' AND ";
        }

        $codlocalarma=$this->entry_codlocalarma->get_text();
        if(!$this->retornabusca2('localarma', &$this->entry_codlocalarma, &$this->label_codlocalarma, 'codigo', 'descricao') and !empty($codlocalarma)) {
            msg('Preencha corretamente o campo Local de Armazenamento!');
            return;
        }
        if(!empty($codlocalarma)) {
            $sql.=" codlocalarma='$codlocalarma' AND ";
        }

        $codfab=$this->entry_codfab->get_text();
        if (!$this->retornabusca2('fabricantes', &$this->entry_codfab, &$this->label_codfab, 'codigo', 'nome') and !empty($codfab)) {
            msg('Preencha corretamente o campo Fabricante!');
            return;
        }
        if(!empty($codfab)) {
            $sql.=" codfab='$codfab' AND ";
        }

        $codfor=$this->entry_codfor->get_text();
        if (!$this->retornabusca2('fornecedores', &$this->entry_codfor, &$this->label_codfor, 'codigo', 'nome') and !empty($codfor)) {
            msg('Preencha corretamente o campo Fornecedores!');
            return;
        }
        if(!empty($codfor)) {
            $sql.=" codfor='$codfor' AND ";
        }

        if(empty($codgrpmerc) and empty($codlocalarma) and empty($codfor) and empty($codfab)) {
            msg('Preencha pelo menos um dos grupos');
            return;
        }

        $sql="SELECT codmerc, descricao, precovenda FROM mercadorias WHERE ".$sql;
        $sql=substr($sql,0,strlen($sql)-4);
        $sql.=" ORDER BY descricao ";

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if(!$resultado=$con->Query($sql)) {
            msg("Erro ao executar SQL");
            return;
        }
        $rows=$con->NumRows($resultado);
        if ($rows==0) {
            msg('Nenhum resultado');
            return;
        }
        $linhaslista=$this->numero_rows_liststore($this->liststore_trocapreco); // numero de linhas
        $this->CriaProgressBar("Adicionando mercadorias...");
        $this->AtualizaProgressBar(null,0,true);
        $conta=1;
        $this->treeview_trocapreco->set_enable_search(false);
        while($i=$con->FetchRow($resultado)) {
            if($linhaslista>0) { // se nao existia linhas na lista.. entao nao verifica
                $this->liststore_trocapreco->foreach(array($this,'verificaSeExisteNaLista'),0,$i[0]);
                if ($this->verificaSeExisteAUX) {
                    msg('Limpe a lista de mercadorias para solucionar isto.');
                    $this->FechaProgressBar();
                    return; // se valor ja existir na lista retorna
                }
            }
            $this->liststore_trocapreco->append(array($i[0],$i[1],number_format($i[2],2,",",""),null,$this->campotrocapreco));
            $conta++;
            if($conta>10) {
                $atual=(100*$conta)/$rows;
            }
            $this->AtualizaProgressBar(null,$atual,true);
        }
        $this->FechaProgressBar();
        $this->treeview_trocapreco->set_enable_search(true);
        $con->Disconnect();
    }

    function removerrmerc() {
        $selecionado=$this->treeview_trocapreco->get_selection();
        if($iter=$this->get_iter_liststore($selecionado,$this->liststore_trocapreco)) {
            $this->liststore_trocapreco->remove($iter);
        }
    }

    function oktrocapreco() {
        $linhaslista=$this->numero_rows_liststore($this->liststore_trocapreco); // numero de linhas
        if($linhaslista==0) {
            msg("Lista vazia");
            return;
        }
        $this->previewtrocapreco(); // efetua preview

        $linhaslista=$this->numero_rows_liststore($this->liststore_trocapreco); // numero de linhas
        $this->CriaProgressBar("Atualizando no banco de dados...");
        $this->AtualizaProgressBar(null,0,true);
        $this->oktrocaprecoconta=1;

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->conoktrocapreco=new $BancoDeDados;
        $this->conoktrocapreco->Connect();

        $this->liststore_trocapreco->foreach(array($this,'oktrocaprecoAUX'),$linhaslista);

        $this->conoktrocapreco->Disconnect();

        $this->FechaProgressBar();

        //$this->limpartrocapreco();
        msg("Atualizacao Efetuada com Sucesso!!");
    }

    function oktrocaprecoAUX($store, $path, $iter, $linhaslista) {
        $codmerc=$this->pegaNumero($store->get_value($iter,0));
        $novo=$this->pegaNumero($store->get_value($iter,3));
        $campo=$this->pegaNumero($store->get_value($iter,4));

        $sql="UPDATE mercadorias SET $campo='$novo' WHERE codmerc='$codmerc' ";
        if(!$this->conoktrocapreco->Query($sql)) {
            msg("Erro ao executar SQL de atualizao");
            //return true; // retorna true para parar o foreach
        }
        $this->oktrocaprecoconta++;
        $atual=(100*$this->oktrocaprecoconta)/$linhaslista;
        $this->AtualizaProgressBar(null,$atual,true);
    }

    function previewtrocapreco() {
        $linhaslista=$this->numero_rows_liststore($this->liststore_trocapreco); // numero de linhas
        if($linhaslista==0) {
            msg("Lista vazia");
            return;
        }
        $this->CriaProgressBar("Atualizando na tela...");
        $this->AtualizaProgressBar(null,0,true);
        $this->previewtrocaprecoconta=1;
        $this->liststore_trocapreco->foreach(array($this,'previewtrocaprecoAUX'),$linhaslista);
        $this->FechaProgressBar();

    }
    function previewtrocaprecoAUX($store, $path, $iter, $linhaslista) {
        $preco=$this->pegaNumero($store->get_value($iter,2));
        if($this->radiobutton_precofixo->get_active()==true) {
            $novo=$this->pegaNumero($this->entry_precofixo); // altera para novo valor
        }elseif($this->radiobutton_real->get_active()==true) {
            $novo=$preco+$this->pegaNumero($this->entry_real);// soma simples
        }elseif($this->radiobutton_porcento->get_active()==true) {
            $novo=$preco+($preco/100*$this->pegaNumero($this->entry_porcento)); // soma de porcentagem
        }
        $store->set($iter,3,number_format($novo,2,",",""));
        $this->previewtrocaprecoconta++;
        $atual=(100*$this->previewtrocaprecoconta)/$linhaslista;
        $this->AtualizaProgressBar(null,$atual,true);
    }

    function pegaCampoTrocaPreco() {
        $this->campotrocapreco="";
        $this->campotrocapreco=$this->combo_campo->get_active_text();
        if(empty($this->campotrocapreco)) {
            msg("Escolha um campo para alterar");
            $this->combo_campo->grab_focus();
            return false;
        }
        return true;
    }
}
?>