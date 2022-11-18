<?php
// funcoes genericas

// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline) {

    // timestamp para a entrada do erro
    $dt = date("Y-m-d H:i:s (T)");

    // Define uma matriz associativa com as strings dos erros
    $errortype = array (
            E_ERROR          => "Error",
            E_WARNING        => "Warning",
            E_PARSE          => "Parsing Error",
            E_NOTICE          => "Notice",
            E_CORE_ERROR      => "Core Error",
            E_CORE_WARNING    => "Core Warning",
            E_COMPILE_ERROR  => "Compile Error",
            E_COMPILE_WARNING => "Compile Warning",
            E_USER_ERROR      => "User Error",
            E_USER_WARNING    => "User Warning",
            E_USER_NOTICE    => "User Notice",
            E_STRICT          => "Runtime Notice"
    );
    // define quais erros nós iremos salvar
    $user_errors = array(E_ERROR, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

    /*
   $err = "<errorentry>\n";
   $err .= "\t<datetime>" . $dt . "</datetime>\n";
   $err .= "\t<errornum>" . $errno . "</errornum>\n";
   $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
   $err .= "\t<errormsg>" . $errstr . "</errormsg>\n";
   $err .= "\t<scriptname>" . $errfile . "</scriptname>\n";
   $err .= "\t<scriptlinenum>" . $errline . "</scriptlinenum>\n";
    */
    //if (in_array($errno, $user_errors)) {

    //$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
    $err= "$dt $errno $errortype[$errno] $errstr on $errfile:$errline\n";
    //}

//   $err .= "</errorentry>\n";

    // para teste
    // echo $err;

    // salva para o log de erros, e envia um email para o desenvolvedor em caso de erro crítico
    error_log($err, 3, "DBDriver".bar."error.log");

    switch ($errno) {
        case 'FATAL':
            $msg.="$errfile:$errline \n FATAL [$errno] $errstr ";
//   echo "  Fatal error in line $errline of file $errfile";
            $msg.="\n PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
//   echo "Aborting...<br />\n";
            msg($msg);
            exit(1);
            break;
        case 'ERROR':
            $msg.="$errfile:$errline \n ERROR [$errno] $errstr\n";
            msg($msg);
            break;
        case 'WARNING':
            $msg.="$errfile:$errline \n WARNING [$errno] $errstr\n";
            msg($msg);
            break;
//  default:
//   $msg.="Outro [$errno] $errstr\n";
//   break;
    }

}


function retorna_CONFIG($config) {
    $conn = sqlite_open('DBDriver'.bar.'.config.db');
    $sql="SELECT * FROM config;";
    if($result=@sqlite_query($sql, $conn)) {
        $result2=sqlite_fetch_array($result);
        sqlite_close($conn);
        $retorno=$result2["$config"];
    }
    return $retorno;
}


function msg($txt) {
    if(empty($txt)){
        return;
    }
    $txt=utf8_decode($txt);
    $dialog = new GtkMessageDialog(
            null,//parent
            1,
            Gtk::MESSAGE_WARNING,
            Gtk::BUTTONS_OK,
            $txt
    );
    $answer = $dialog->run();
    $dialog->destroy();
}

function inputdialog($pergunta,$mascara=false,$memo=false) {
    /*
	pergunta = texto da pergunta
	mascara = mascara do entry. Exemplo: **-**-****, ou virgula2, etc...
	memo = se desejar usar um campo memo ao inves de uma unica linha de entrada de dados
    */
    $dialogBox = new GtkDialog("Pergunta",null,GObject::DIALOG_MODAL,array(GObject::STOCK_OK, GObject::RESPONSE_OK, GObject::STOCK_CANCEL, GObject::RESPONSE_CANCEL));
    $hbox=new GtkHBox();
    $label= new GtkLabel($pergunta);
    $hbox->pack_start($label,false,false);
    if($memo) {
        $scrol= new GtkScrolledWindow();
        $textBuffer=new GtkTextBuffer();
        $textView=new GtkTextView();
        $textView->set_buffer($textBuffer);
        $scrol->add($textView);
        $scrol->set_size_request(400,100);
        $hbox->pack_start($scrol);
    }else {
        $entry= new GtkEntry();
        $hbox->pack_start($entry);
    }
    if($mascara) {
        //$entry->connect('key-press-event', array($this,'mascaraNew'),$mascara);
    }

    $topArea = $dialogBox->vbox;
    $topArea->add($hbox);
    $dialogBox->show_all();
    $result = $dialogBox->run();
    switch($result) {
        case (Gtk::RESPONSE_OK):
            if($memo) {
                $volta=$textBuffer->get_text(
                        $textBuffer->get_start_iter(),
                        $textBuffer->get_end_iter()
                );
            }else {
                $volta=$entry->get_text();
            }
            break;
        case (Gtk::RESPONSE_CANCEL):
            $volta=false;
            break;
    }
    $dialogBox->destroy();
    return $volta;
}

function confirma($ok,$txt, $retorna1=NULL, $retorna2=NULL, $retorna3=NULL, $retorna4=NULL, $retorna5=NULL, $retorna6=NULL, $retorna7=NULL, $retorna8=NULL, $retorna9=NULL, $retorna10=NULL, $retorna11=NULL, $retorna12=NULL, $retorna13=NULL, $retorna14=NULL, $retorna15=NULL) {

    // $ok = funcao a ser executada de clicar em SIM
    // $txt = texto da pergunta de confirmacao
    // $retorna1 = parametro a passar para a funcao $ok
    /*$sn=new GladeXML('interface'.bar.'confirma.glade2');
    $rotulo=$sn->get_widget('rotulo');
    $janela=$sn->get_widget('window1');
    $rotulo->set_text(utf8_decode($txt));
    $botaoS=$sn->get_widget('sim');
    $botaoN=$sn->get_widget('nao');
    $botaoS->connect_simple('clicked',array($janela,'hide'));
    $botaoS->connect_simple('clicked',&$ok,$retorna1,$retorna2,$retorna3,$retorna4,$retorna5,$retorna6);
    $botaoN->connect_simple('clicked',array($janela,'hide'));
    $janela->grab_focus();
    $botaoS->grab_focus();
	$janela->set_position(3);
    $janela->set_keep_above(TRUE);
    $janela->show();*/

    $dialog = new GtkMessageDialog(
            null,//parent
            0,
            Gtk::MESSAGE_QUESTION,
            Gtk::BUTTONS_YES_NO,
            $txt
    );

    $dialog->set_default_response(Gtk::RESPONSE_YES);
    $answer = $dialog->run();
    $dialog->destroy();

    if ($answer == Gtk::RESPONSE_YES) {
        if(!$ok) {
            return true;
        }else {
            call_user_func($ok,$retorna1, $retorna2, $retorna3, $retorna4, $retorna5, $retorna6,  $retorna7, $retorna8, $retorna9, $retorna10, $retorna11, $retorna12, $retorna13, $retorna14, $retorna15);
        }
    } else if ($answer == Gtk::RESPONSE_NO) {
        return false;
    } else {
        confirma($ok,$txt, $retorna1, $retorna2, $retorna3, $retorna4, $retorna5, $retorna6,  $retorna7, $retorna8, $retorna9, $retorna10, $retorna11, $retorna12, $retorna13, $retorna14, $retorna15);
    }
}

class funcoes extends validacao {

    function funcoes() {

    }

    function atalho_limpa() {
        // limpa os atalhos de teclados deixando apenas os padroes
    }
    function atalho_cria() {
        // cria um novo atalho de teclado

    }

    function encheComboEstado($combo) {

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT str_uf FROM estados ORDER BY str_uf";
        $resultado=$con->Query($sql);
        //$array[0]=array();
        $j=0;
        while($i=$con->FetchRow($resultado)) {
            $array[$j]=$i[0];
            $j++;
        }
        //echo $array;
        if(count($array)==0) {
            $array[0]="??";
        } else {
            $combo->set_popdown_strings($array);
        }
    }
    function cidadesNew($cidade,$estado) {
        $completion = new GtkEntryCompletion();
        $completion_model = $this->__create_completion_model_cidades($estado, $cidade);
        $completion->set_model($completion_model);
        $completion->set_text_column(0);
        $cidade->set_completion($completion);
    }

    function __create_completion_model_cidades($uf,$cidade) {
        $store = new GtkListStore(GObject::TYPE_STRING);


        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $estado=$uf->get_text();
        $estado=$con->EscapeString($estado);
        $sql="SELECT nome_local FROM cep_loc WHERE uf_local='$estado' ORDER BY nome_local";
        $resultado=$con->Query($sql);

        while($i=$con->FetchRow($resultado)) {
            // adiciona a cidade
            $iter = $store->append();
            $store->set($iter, 0, $i[0]);
        }
        if($estado==retorna_CONFIG("Estado")) {
            $cidade->set_text(retorna_CONFIG("Cidade"));
        }
        $con->Disconnect();

        return $store;
    }

    function retorna_OPCAO($opcao) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $resultado=$con->Query("select $opcao from opcoes");
        $result2=$con->FetchRow($resultado);
        $con->Disconnect();
        if($result2[0]=="1") return true;
        return $result2[0];
    }

    function verificaPermissao($codigoctree,$msg=true,$texto=null) {
        //global $UsuarioDoLoginGeral;
        global $NivelAcessoDoLoginGeral;
        if(empty($NivelAcessoDoLoginGeral)) {
            return true;
        }
        if(!empty($codigoctree)) {
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=new $BancoDeDados;
            $con->Connect();

            $sql="SELECT codigonivelacesso, permitido FROM permissao WHERE codigonivelacesso='$NivelAcessoDoLoginGeral' AND codigoctree='$codigoctree'";
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                while($i = $con->FetchRow($resultado)) {
                    //print_r($i);
                    if($i[0]==$NivelAcessoDoLoginGeral AND $i[1]) {
                        $con->Disconnect();
                        return true;
                    }else {
                        if($msg) {
                            if(empty($texto)) {
                                msg("Acesso negado!");
                            }else {
                                msg($texto);
                            }
                        }
                    }
                }
            }else {
                $sql="INSERT INTO permissao (codigonivelacesso, permitido, codigoctree) VALUES ('$NivelAcessoDoLoginGeral', '0', '$codigoctree')";
                if(!$con->Query($sql)) {
                    msg("Erro adicionando nova permissao.");
                }else {
                    msg("Devido a nova versao a permissao $codigoctree foi adicionada como negada.\nLibere o acesso manualmente no menu Sistema->Seguranca->Permissoes.\nEsta mensagem aparecera apenas esta vez.");
                }
                //msg("Codigo CTREE $codigoctree nao encontrado");
                //msg("Acesso negado!");
            }
            $con->Disconnect();
            return false;
        }else {
            return true;
        }
    }

    function perguntaPermissao($codigoctree,$msg=true,$texto=null) {
        //global $UsuarioDoLoginGeral;
        global $NivelAcessoDoLoginGeral;
        if(empty($NivelAcessoDoLoginGeral) or empty($codigoctree)) {
            // sistema nao esta usando senhas
            return true;
        }
        if($this->verificaPermissao($codigoctree,false)) {
            // usuario tem acesso a este recurso
            return true;
        }

        if($msg) {
            if(empty($texto)) {
                $msg="Acesso Negado";
            }else {
                $msg=$texto;
            }
        }

        // pergunta codigo e senha
        $dialogBox = new GtkDialog("Pergunta",null,GObject::DIALOG_MODAL,array(GObject::STOCK_OK, GObject::RESPONSE_OK, GObject::STOCK_CANCEL, GObject::RESPONSE_CANCEL));
        $hbox=new GtkHBox();

        $label= new GtkLabel("Cod. Funcionario");
        $hbox->pack_start($label,false,false);
        $entry= new GtkEntry();
        $hbox->pack_start($entry);

        $label2= new GtkLabel("Senha");
        $hbox->pack_start($label2,false,false);
        $entry2= new GtkEntry();
        $entry2->set_visibility(FALSE);
        $hbox->pack_start($entry2);

        $topArea = $dialogBox->vbox;
        $topArea->add($hbox);
        $dialogBox->show_all();
        $result = $dialogBox->run();
        switch($result) {
            case (GObject::RESPONSE_OK):
                $usuario=$entry->get_text();
                $senha=$entry2->get_text();
                break;
            case (GObject::RESPONSE_CANCEL):
            // cancelou dialogo
                if(!empty($msg)) msg($msg);
                return false;
                break;
        }
        $dialogBox->destroy();

        // verifica validade senha e codigo
        if (!$this->retornabusca4("nome",'funcionarios','codigo',$usuario)) {
            if(!empty($msg)) msg($msg);
            return false;
        }else {
            $nivelacesso=$this->retornabusca4("codigonivelacesso", 'nivel2funcionario', 'codigofuncionario', $usuario);
            $contrasenha=$this->retornabusca4("senha", 'nivel2funcionario', 'codigofuncionario', $usuario);
            if($contrasenha<>$senha) {
                // senha nao confere
                if(!empty($msg)) msg($msg);
                return false;
            }
        }


        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();

        $sql="SELECT codigonivelacesso, permitido FROM permissao WHERE codigonivelacesso='$nivelacesso' AND codigoctree='$codigoctree'";
        $resultado=$con->Query($sql);
        if($con->NumRows($resultado)>0) {
            // permisao existe no cadastro
            while($i = $con->FetchRow($resultado)) {
                if($i[0]==$nivelacesso AND $i[1]) {
                    // acesso permitido!!!
                    $con->Disconnect();
                    return true;
                }else {
                    $con->Disconnect();
                    if(!empty($msg)) msg($msg);
                    return false;
                }
            }
        }else {
            // permisao NAO existe no cadastro
            $sql="INSERT INTO permissao (codigonivelacesso, permitido, codigoctree) VALUES ('$nivelacesso', '0', '$codigoctree')";
            if(!$con->Query($sql)) {
                msg("Erro adicionando nova permissao.");
            }else {
                msg("Devido a nova versao a permissao $codigoctree foi adicionada como negada.\nLibere o acesso manualmente no menu Sistema->Seguranca->Permissoes.\nEsta mensagem aparecera apenas esta vez para esta permissao.");
            }
            $con->Disconnect();
            // nova permissao cadastrada como negada
            if(!empty($msg)) msg($msg);
            return false;
        }
        $con->Disconnect();
        if(!empty($msg)) msg($msg);
        return false;
    }

    function status($txt) {
        if(!$status=$this->xml->get_widget('status')) $status=$this->status;
        if(is_a($status,'GtkLabel')) {
            $status->set_markup(
                    '<span weight="bold" foreground="red" size="11000">'.
                    utf8_decode($txt)
                    .'</span>'
            );
        }
        Gtk::timeout_add(6000,array(&$this,'limpa_status'));
    }
    function limpa_status() {
        if(!$status=$this->xml->get_widget('status')) $status=$this->status;
        if(is_a($status,'GtkLabel')) {
            $status->set_text('');
        }
    }
    function destroy_buscatab() {
        if(is_a($this->window1_buscatab,'GtkWindow')) {
            //$this->window1_buscatab->destroy();
            $this->window1_buscatab->hide();
            $this->window1_buscatab->unrealize();
            //$this->window1_buscatab->destroy();
            //$this->window1_buscatab=null;
        }
    }

    function search_buscatab($sql_original,$sql_comp,$campoRetorna) {
        $busca=$this->entry_busca_buscatab->get_text();
        $busca=mysql_escape_string($busca);
        $sql=$sql_original." WHERE $campoRetorna LIKE '$busca%' ".$sql_comp;
        //msg($sql);
        $this->AdicionaLinhasBuscatab($sql,true);
        $this->window1_buscatab->show_all();
        $this->treeview_buscatab->grab_focus();
    }

    function click_button_busca_buscatab(){
        $this->button_busca_buscatab->clicked();
    }

    function buscatab($sql, $transfere, $entry, $label, $tabela, $campoRetorna, $codigo, $FocoEntry='this->treeview_buscatab') {
        /* $sql: contem o codigo SQL para a consulta a ser mostrana na tela
           $transfere: deve ser FALSE ou TRUE. Se for retornar algum valor deve
               ser TRUE. Se a buscatab for apenas para consulta, coloque FALSE
           $entry: eh o entry que vai receber o codigo do registro selecionado
           $label: eh o label que vai receber o texto do $campoRetorna do registro selecionado
           $tabela: a tabela que sera consultada
           $campoRetorna: o campo que sera retornado o texto do label
           $codigo: o codigo PRIMARY KEY da tabela
           $FocoEntry: o entry ou qualquer outro objeto que terah o foco inicial, nao coloque $
           
           // SEMPRE a primeira coluna da query SQL devera ser o PRIMARY KEY da tabela
        */
        $sql_original=$sql;
        global $parente;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if($campoRetorna and strpos(strtoupper($sql),"ORDER BY")===false) {
            $sql_comp=" ORDER BY $campoRetorna";
            $sql.=$sql_comp;
        }
        if(!empty($this->last_sql_buscatab)) {
            if($this->last_sql_buscatab==$sql) {
                $this->window1_buscatab->realize();
                $this->window1_buscatab->show();
                return;
            }else { // sql diferente
                $this->window1_buscatab=null;
            }
        }
        $this->last_sql_buscatab=$sql;

        $resultado=$con->Query($sql);

        $janela = new GladeXML('interface'.bar."buscatab.glade2");
        $this->window1_buscatab=$janela->get_widget('window1');

        //$this->combobox_buscatab=$janela->get_widget('combobox_buscatab');
        $this->entry_busca_buscatab=$janela->get_widget('entry_busca_buscatab');
        $this->entry_busca_buscatab->connect_simple('activate', array($this,'click_button_busca_buscatab'));

        $this->button_busca_buscatab=$janela->get_widget('button_busca_buscatab');
        $this->button_busca_buscatab->connect_simple('clicked', array($this,'search_buscatab'),$sql_original,$sql_comp,$campoRetorna,$FocoEntry);

        $this->window1_buscatab->set_transient_for($parente);

        $this->window1_buscatab->connect_simple('focus-out-event',array($this,'destroy_buscatab'));

        //$this->window1_buscatab->set_skip_taskbar_hint(true);
        $this->window1_buscatab->set_icon_from_file('tema'.bar.'icone.png');

        $this->window1_buscatab->connect_simple('delete-event', array($this,'destroy_buscatab'));
        //$this->window1_buscatab->set_uposition( retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy") );
        //$this->window1_buscatab->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );
        $this->window1_buscatab->maximize();

        $scrolledwindow1=$janela->get_widget('scrolledwindow1');

        for ($i=0;$i<$con->NumFields($resultado);$i++) {
            $campos[$i]=$con->FieldName($resultado,$i);
        }
        $tmp=str_repeat('GObject::TYPE_STRING,',count($campos));
        $tmp=substr($tmp,0,-1);
        eval('$this->liststore_buscatab=new GtkListStore('.$tmp.');');
        $this->treeview_buscatab = new GtkTreeView($this->liststore_buscatab);
        $this->treeview_buscatab->set_rules_hint(TRUE);
        $this->treeview_buscatab->set_enable_search(FALSE);


        $this->add_coluna_treeview($campos,$this->treeview_buscatab);
        $scrolledwindow1->add($this->treeview_buscatab);


        $numerolin=$con->NumRows($resultado);
        if($numerolin==0) {
            msg("Nao ha resultados para buscar!");
            //$this->window1_buscatab->destroy();
            $this->destroy_buscatab();
            return;
        }
        // Adiciona linhas
        $this->AdicionaLinhasBuscatab($sql);
        /*$lin=0;
        while ($lin<$numerolin){
            $linha[$lin]=$con->FetchRow($resultado);
            array_walk ($linha[$lin], array(&$this, 'utf8_encode_array'));
            $this->liststore_buscatab->append($linha[$lin]);
            $lin++;
        }
        */

        $this->button_ok_buscatab= new GtkButton();
        if(!$transfere) {  // usando em pagar - ver pagamentos realizados
            // botao vai apenas fechar a tela
            $this->button_ok_buscatab->connect_simple('clicked', array($this,'destroy_buscatab'));
        }else {
            // botao vai transferir dados pra tela
            $this->button_ok_buscatab->connect_simple('clicked', array($this,'muda'), $tabela, $codigo, $entry, $label, $campoRetorna);
            //$this->button_ok_buscatab->connect_simple_after('clicked', array($this, 'destroy_buscatab'));


        }
        // se dar enter fecha a janela e passa dados pro cadastro
        $this->treeview_buscatab->connect('row-activated',array(&$this,clicaNoBotaoTransfere_buscatab));
        $this->treeview_buscatab->connect('key-press-event',array($this,keypressBuscatab));


        if(!$status=$janela->get_widget('status')) {
            $hbox=new GtkHbox(false,0);
            $botaoatualizaVerdeVermelho=new GtkButton("");
            //$botaoatualiza2=GtkButton::new_from_stock('gtk-refresh');
            // bota imagem no botao
            //$p = GdkPixbuf::new_from_file('interface'.bar.'on.png');
            //$a = new GtkImage;
            //$a->set_from_pixbuf($p);
            //$this->botaoatualizaVerdeVermelho->set_image($a);
            //$this->botaoatualizaBotaVerde();
            //$p = GdkPixbuf::new_from_file('interface'.bar.'on.png');
            //$a = new GtkImage;
            //$a->set_from_pixbuf($p);
            $a=GtkImage::new_from_file('interface'.bar.'on.png');
            $botaoatualizaVerdeVermelho->set_image($a);

            $botaoatualizaVerdeVermelho->connect_simple('clicked',array($this,'AdicionaLinhasBuscatab'),$sql);
            $botaoatualizaVerdeVermelho->set_size_request(20,20);
            $status=new GtkLabel();
            $status->set_use_markup(true);


            $hbox->pack_start($botaoatualizaVerdeVermelho,false);
            $hbox->pack_start($status,true,true);

            $vbox1=$janela->get_widget('vbox1');
            $vbox1->pack_start($hbox,false);
            $vbox1->show_all();
            $accgrp = new GtkAccelGroup();
            //$this->botaoatualizaVerdeVermelho->add_accelerator('clicked', $accgrp, Gdk::KEY_F5, 0, Gtk::ACCEL_VISIBLE);
            $botaoatualizaVerdeVermelho->add_accelerator('clicked', $accgrp, 65474, 0, Gtk::ACCEL_VISIBLE);
            $this->window1_buscatab->add_accel_group($accgrp);
        }


        $con->Disconnect();
        $this->window1_buscatab->show_all();
        //$this->treeview_buscatab->set_search_column(1);
        // seleciona primeira linha
        $selecao=$this->treeview_buscatab->get_selection();
        list($model, $iter) = $selecao->get_selected();
        $selecao->select_path('0');
        $this->treeview_buscatab->set_cursor("0");
        if($FocoEntry) {
            eval('$'.$FocoEntry.'->grab_focus();');
        }else {
            $this->treeview_buscatab->grab_focus();
        }

    }
    function AdicionaLinhasBuscatab($sql,$aceitaBranco=false) {
        $this->CriaProgressBar("Criando lista");

        $this->treeview_buscatab->set_model(null);
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $resultado=$con->Query($sql);
        $numerolin=$con->NumRows($resultado);
        if($numerolin==0 and !$aceitaBranco) {
            msg("Nao ha resultados para buscar!");
            $this->destroy_buscatab();
            return;
        }
        $this->liststore_buscatab->clear();
        $iedu=0;
        while ($linha[$lin]=$con->FetchRow($resultado)) {
            array_walk ($linha[$lin], array($this, 'utf8_encode_array'));
            $this->liststore_buscatab->append($linha[$lin]);

            $iedu++;
            $atual=(100*$iedu)/$numerolin;
            //$this->AtualizaProgressBar(null,$atual,true);
        }
        $this->FechaProgressBar();

        $con->Disconnect();
        $this->treeview_buscatab->set_model($this->liststore_buscatab);
    }
    function keypressBuscatab($widget, $evento) {
        $tecla=$evento->keyval;
        if($tecla==65307) { // ESC
            //$this->window1_buscatab->destroy();
            //$this->button_ok_buscatab->clicked();
            $this->destroy_buscatab();
        }
    }

    function clicaNoBotaoTransfere_buscatab() {
        $this->button_ok_buscatab->clicked();
    }

    //funcao pra clicar em algum botao ao dar ENTER. $button_ok eh o botao que serah clicado
    function muda_enter(&$entry,$evento, $button_ok) {
        $tecla=$evento->keyval;
        if($tecla==65293 or $tecla==65421 or $evento=="especial") {
            //if($tecla==GDK_KEY_Return or $tecla==GDK_KEY_KP_Enter or $evento=="especial"){
            $button_ok->clicked();
            $entry->emit_stop_by_name('key-press-event');
            return true;
        }
        return false;
    }

    // passa dados da tela do botao FILTRA para tela do cadastro
    function muda($tabela,$codigo,$entry,$label,$campoRetorna) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if($selecionado=$this->treeview_buscatab->get_selection()) {
            $cp0=$this->get_valor_liststore($selecionado,$this->liststore_buscatab,0,true);
            if(!empty($cp0)) {
                $this->destroy_buscatab();
                /*$pos = strpos($campoRetorna, '.');
				if($pos!==false){
					$campoRetorna=substr($campoRetorna,$pos+1);
				}*/
                $sql="SELECT $campoRetorna FROM $tabela WHERE $codigo='$cp0'";
                //echo $sql;
                $resultado=$con->Query($sql);
                $resultado2=$con->FetchRow($resultado);

                //if(!empty($entry)){
                if(is_a($entry,'GtkEntry') or is_a($entry,'GtkLabel')) {
                    $entry->set_text($cp0);
                }
                //if(!empty($label)){
                if(is_a($label,'GtkEntry') or is_a($label,'GtkLabel')) {
                    $label->set_text($resultado2[0]);
                }elseif(is_a($label,'GtkButton')) {
                    //$label->clicked();
                    Gtk::timeout_add(200,array($this,errormaluco),$label);
                }
            }else {
                $this->destroy_buscatab();
            }
        }else {
            $this->destroy_buscatab();
        }
        $con->Disconnect();

    }

    function errormaluco($label) {
        $label->clicked();
    }

    function nomes_dos_campos($tabela) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;

        $con->Connect();

        $tmp=$con->Query("select * from $tabela limit 1");
        for ($i=0;$i<$con->NumFields($tmp);$i++) {
            $retorna[$i]=$con->FieldName($tmp,$i);
        }
        $con->Disconnect();
        return $retorna;
    }

    function atualiza_relogio() {
        $relogio=$this->xml->get_widget('relogio');
        $estilo= new GtkStyle;
        $estilo->fg[GTK_STATE_NORMAL] = new GdkColor(0, 0, 0);
        $estilo->font=gdk::font_load("-*-*-bold-r-*-*-*-120-*-*-*-*-*-*");
        $relogio->set_style($estilo);
        if (intval(date("s")%2)==0) {
            $relogio->set_text(date("H:i - d/m/Y"));
        } else {
            $relogio->set_text(date("H i - d/m/Y"));
        }
        Gtk::timeout_add(1000,'atualiza_relogio');
    }

    function retornabusca($tabela,$entrycodigo,$entrynome,$campobusca,$camporetorna,$varglobal) {
        eval("global $$varglobal;");

        eval('$cod=$'.$varglobal.'->'.$entrycodigo.'->get_text();');
        $cod=strtoupper($cod);
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        if(!(empty($cod))) {
            $sql="SELECT $camporetorna FROM $tabela where $campobusca='$cod';";
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                $retorno=$con->FetchArray($resultado,$camporetorna);
                eval('$'.$varglobal.'->'.$entrynome.'->set_text($retorno["$camporetorna"]);');
            } else {
                eval('$'.$varglobal.'->'.$entrynome.'->set_text(\'\');');
            }
        }

    }

    /* funcoes do enter no entry
    ** INICIO
    */

    function entry_enter($widget, $evento,$sql, $transfere, $entry, $label, $tabela, $campoRetorna, $codigo, $EntryFoco='this->treeview_buscatab') {
        // funcao que chama o buscaTab ao se dar um ENTER no entry
        // Veja buscatab para lista de parametros
        $valor=$evento->keyval;

        if ($valor==65293 or $valor==65421) { // se der enter ou KP_enter
            $this->buscatab($sql, $transfere, $entry, $label,$tabela,$campoRetorna,$codigo,$EntryFoco);
        }
        //return ;
    }
    function retornabusca2($tabela,$entrycodigo,$entrynome,$campobusca,$camporetorna) {
        if($tabela=="placon") {
            $cod=$entrycodigo->get_text();
        }else {
            $cod=$this->DeixaSoNumero($entrycodigo->get_text());
        }

        $volta=true;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $cod=$con->EscapeString($cod);
        if(!(empty($cod))) {
            $sql="SELECT $camporetorna FROM $tabela where $campobusca='$cod';";

            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                $retorno=$con->FetchArray($resultado,$camporetorna);
                if($entrynome) $entrynome->set_text($retorno["$camporetorna"]);
            } else {
                if($entrynome) $entrynome->set_text('');
                //msg("Codigo de $tabela nao encontrado!");
                $volta=false;
            }
        }
        else {
            if($entrynome) $entrynome->set_text('');
            $volta=false;
        }
        $con->Disconnect();
        return $volta;

    }

    function retornabusca22($tabela,$entrycodigo,$entrynome,$campobusca,$camporetorna) {
        // funcao usada pelo focus-out-event
        if($tabela=="placon") {
            $cod=$entrycodigo->get_text();
        }else {
            $cod=$this->DeixaSoNumero($entrycodigo->get_text());
            //$cod=$this->pegaNumero($entrycodigo->get_text()));
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $cod=$con->EscapeString($cod);
        if(!(empty($cod))) {
            $sql="SELECT $camporetorna FROM $tabela where $campobusca='$cod';";
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                $retorno=$con->FetchArray($resultado,$camporetorna);
                if($entrynome<>NULL) $entrynome->set_text($retorno["$camporetorna"]);
            } else {
                if($entrynome<>NULL) $entrynome->set_text('');
            }
        }
        else {
            if($entrynome<>NULL) $entrynome->set_text('');
        }
        $con->Disconnect();
        return false;

    }
    /* funcoes do enter no entry
    ** FIM
    */

    function stringsplit($the_string, $the_number) {
        $startoff_nr = 0;
        $the_output_array = array();
        for($z = 1; $z < ceil(strlen($the_string)/$the_number)+1 ; $z++) {
            $startoff_nr = ($the_number*$z)-$the_number;
            $the_output_array[] = substr($the_string, $startoff_nr, $the_number);
        }
        return($the_output_array);
    }

    function DeixaSoNumero($texto) {

        $s="";
        for ($x=1; $x<=strlen($texto); $x=$x+1) {
            $ch=substr($texto,$x-1,1);
            if (ord($ch)>=48 && ord($ch)<=57) {
                $s=$s.$ch;
            }
        }
        $texto=$s;
        //echo "string=".$texto;
        return $texto;
    }

    function DeixaSoNumeroDecimal($texto, $decimal) {
        if(empty($decimal)) {
            msg('Funcao DeixaSoNumeroDecimal sem o ultimo parametro!');
            return;
        }
        $texto=$this->DeixaSoNumero($texto);
        $tam=strlen($texto);
        $texto=substr($texto,0,$tam-$decimal).'.'.substr($texto,-($decimal));
        if($texto=="." or empty($texto)) $texto=0;
        return $texto;
    }
    /*
    function CalculaPrecoVenda(&$entry, $evento, $global, $entryPC, $entryL, $entryPV){
        eval("global \$$global;");
        eval('$pcusto=$'.$global.'->'.$entryPC.'->get_text();');                
        eval('$lucro=$'.$global.'->'.$entryL.'->get_text();');    
        $pcusto=DeixaSoNumeroDecimal($pcusto, 4);        
        $pvenda=$pcusto+($pcusto*($lucro/100));        
        $pvenda='R$ '.number_format($pvenda, 2, ',', '.');    
        eval('$'.$global.'->'.$entryPV.'->set_text("'.$pvenda.'");');    
    }
    */


    function cidades() {
        $estado=strtoupper($this->entry_estado->get_text());

        if(empty($this->UltimoEstadoFuncoes)) {
            $this->UltimoEstadoFuncoes=$estado;
        }else {
            if($estado==$this->UltimoEstadoFuncoes) {
                return;
            }else {
                $this->UltimoEstadoFuncoes=$estado;
            }
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $sql="SELECT nome_local FROM cep_loc WHERE uf_local='$estado' ORDER BY nome_local";
        $resultado=$con->Query($sql);
        $this->listacidades=array();
        while($i=$con->FetchRow($resultado)) {
            // adiciona a cidade ï¿½array
            array_push($this->listacidades, $i[0]);
        }
        if(count($this->listacidades)>0) {
            $this->combo_cidade->set_popdown_strings($this->listacidades);
        }
        if($estado==retorna_CONFIG("Estado")) {
            $this->entry_cidade->set_text(retorna_CONFIG("Cidade"));
        }
        $con->Disconnect();
        return;
    }

    function tira_acentos($Var) {
        //$Var = ereg_replace("[áàãâä]","a",$Var);
        //$Var = str_replace(";","",$Var);
        /*
        $Var = str_replace("\xc3\xa0","a",$Var); 
        $Var = str_replace("\xc3\xa1","a",$Var); 
        $Var = str_replace("\xc3\xa2","a",$Var);
        $Var = str_replace("\xc3\xa3","a",$Var);
        $Var = str_replace("\xc3\xa4","a",$Var);
        $Var = str_replace("\xc3\xa5","a",$Var);

        $Var = str_replace("\xc3\x80","A",$Var);
        $Var = str_replace("\xc3\x81","A",$Var);
        $Var = str_replace("\xc3\x82","A",$Var);
        $Var = str_replace("\xc3\x83","A",$Var);
        $Var = str_replace("\xc3\x84","A",$Var);
        $Var = str_replace("\xc3\x85","A",$Var);

		$Var = str_replace("\xc3\xa8","e",$Var);
		$Var = str_replace("\xc3\xa9","e",$Var);
		$Var = str_replace("\xc3\xaa","e",$Var);
		$Var = str_replace("\xc3\xab","e",$Var);
		
        $Var = str_replace("\xc3\x88","E",$Var);
        $Var = str_replace("\xc3\x89","E",$Var);
        $Var = str_replace("\xc3\x8a","E",$Var);
        $Var = str_replace("\xc3\x8b","E",$Var);

		$Var = str_replace("\xc3\xac","i",$Var);
		$Var = str_replace("\xc3\xad","i",$Var);
		$Var = str_replace("\xc3\xae","i",$Var);
		$Var = str_replace("\xc3\xaf","i",$Var);

		$Var = str_replace("\xc3\x8c","I",$Var);
		$Var = str_replace("\xc3\x8d","I",$Var);
		$Var = str_replace("\xc3\x8e","I",$Var);
		$Var = str_replace("\xc3\x8f","I",$Var);

		$Var = str_replace("\xc3\xb2","o",$Var);
		$Var = str_replace("\xc3\xb3","o",$Var);
		$Var = str_replace("\xc3\xb4","o",$Var);
		$Var = str_replace("\xc3\xb5","o",$Var);
		$Var = str_replace("\xc3\xb6","o",$Var);

		$Var = str_replace("\xc3\x92","O",$Var);
		$Var = str_replace("\xc3\x93","O",$Var);
		$Var = str_replace("\xc3\x94","O",$Var);
		$Var = str_replace("\xc3\x95","O",$Var);
		$Var = str_replace("\xc3\x96","O",$Var);

		$Var = str_replace("\xc3\xb9","u",$Var);
		$Var = str_replace("\xc3\xba","u",$Var);
		$Var = str_replace("\xc3\xbb","u",$Var);
		$Var = str_replace("\xc3\xbc","u",$Var);

		$Var = str_replace("\xc3\x99","U",$Var);
		$Var = str_replace("\xc3\x9a","U",$Var);
		$Var = str_replace("\xc3\x9b","U",$Var);
		$Var = str_replace("\xc3\x9c","U",$Var);

		$Var = str_replace("\xc3\xa7","c",$Var);
		$Var = str_replace("\xc3\x87","C",$Var);

		$Var = str_replace("\xc2\xaa","a",$Var);
		$Var = str_replace("\xc2\xb0","o",$Var);
		$Var = str_replace("\xc2\xb2","2",$Var);
		$Var = str_replace("\xc2\xb3","3",$Var);
		$Var = str_replace("\xc2\xb9","1",$Var);
		$Var = str_replace("\xc2\xba","o",$Var);*/

        /*$Var = str_replace("\xc7","C",$Var);
		$Var = str_replace("\xe1","?",$Var);
		$Var = str_replace("\xb0","c",$Var);
		$Var = str_replace("\x80","C",$Var);
		$Var = str_replace("\xe7","c",$Var);
		$Var = str_replace("\xc9","E",$Var);
		$Var = str_replace("\xca","E",$Var);
		$Var = str_replace("\xd3","O",$Var);
		$Var = str_replace("\xd4","O",$Var);
		$Var = str_replace("\xd5","O",$Var);
		$Var = str_replace("\xc0","A",$Var);
		$Var = str_replace("\xc1","A",$Var);
		$Var = str_replace("\xc2","A",$Var);
		$Var = str_replace("\xc3","A",$Var);
		$Var = str_replace("\xda","U",$Var);
		$Var = str_replace("\xcd","I",$Var);
		$Var = str_replace("\xf8",".",$Var);
		$Var = str_replace("\xb3","3",$Var);
		$Var = str_replace("\xb2","2",$Var);
		$Var = str_replace("\xba","o",$Var);
		$Var = str_replace("\xaa","?",$Var);
		$Var = str_replace("\xa0","?",$Var);*/
        // agudo
        /*0xc127 A
		0xc927 E
		0xcd27 I
		0xd327 O
		0xda27 U
		
		0xe1272c a
		0xe9272c e
		0xcd272c i
		0xf3272c20 o
		0xfa u
		// crase
		0xc027 A
		0xc827 E
		0xcc27 I
		0xd227 O
		0xd927 U
		0xe0272c a
		0xe8272c e*/



        $Var = ereg_replace("[ÁÀÃÂÄ]","A",$Var);
        $Var = ereg_replace("[éèêë]","e",$Var);
        $Var = ereg_replace("[ÉÈÊË]","E",$Var);
        $Var = ereg_replace("[ÍÌÎÏ]","I",$Var);
        $Var = ereg_replace("[íìîï]","i",$Var);
        $Var = ereg_replace("[óòõôö]","o",$Var);
        $Var = ereg_replace("[ÓÒÕÔÖ]","O",$Var);
        $Var = ereg_replace("[úùûü]","u",$Var);
        $Var = ereg_replace("[ÚÙÛÜ]","U",$Var);
        $Var = ereg_replace("[ýÿ]","y",$Var);
        $Var = ereg_replace("[ÝŸ]","Y",$Var);
        $Var = ereg_replace("[ç]","c",$Var);
        $Var = ereg_replace("[Ç]","C",$Var);
        $Var = ereg_replace("[º°]","o",$Var);
        $Var = ereg_replace("[ª]","a",$Var);
        $Var = ereg_replace("[¹]","1",$Var);
        $Var = ereg_replace("[²]","2",$Var);
        $Var = ereg_replace("[³]","3",$Var);
        /*$a = array(
             '/[ÂÀÁÄÃ]/'=>'A',
             '/[âãàáä]/'=>'a',
             '/[ÊÈÉË]/'=>'E',
             '/[êèéë]/'=>'e',
             '/[ÎÍÌÏ]/'=>'I',
             '/[îíìï]/'=>'i',
             '/[ÔÕÒÓÖ]/'=>'O',
             '/[ôõòóö]/'=>'o',
             '/[ÛÙÚÜ]/'=>'U',
             '/[ûúùü]/'=>'u',
             '/ç/'=>'c',
             '/Ç/'=> 'C');
	                        
	    return preg_replace(array_keys($a), array_values($a), $Var);*/
        return $Var;
    }

    function ja_cadastrado($tabela, $campo, $busca, $codigo=false, $codigo2=false) {
        // retorna true se ja cadastrado

        $sql="select $campo from $tabela where $campo='$busca' ";
        if($codigo and $codigo2) {
            $sql.=" AND $codigo<>'$codigo2' ";
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $resultado=$con->Query($sql);
        $numero=$con->NumRows($resultado);
        if($numero>0) {
            $retorno=true;
        }else {
            $retorno=false;
        }
        $con->Disconnect();
        return $retorno;
    }

    function retornabusca5($sql,$msg=false) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if(!empty($sql)) {
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                $retorno=$con->FetchRow($resultado);
                $con->Disconnect();
                return $retorno[0];
            }
            if($msg) msg("$SQL ao encontrado!");
        }
        $con->Disconnect();
        return false;
    }

    function retornabusca3($tabela,$entrycodigo,$campobusca,$camporetorna,$msg=true) {
        // funcao usada pelo focus-out-event
        //eval("global \$$varglobal;");

        // verifica o tipo do entry
        if(is_a($entrycodigo,'GtkEntry') or is_a($entrycodigo,'GtkLabel')) {
            $cod=$entrycodigo->get_text();
        }else {
            $cod=$entrycodigo;
        }
        //echo "rb3 ".$cod."\n";
        //$volta=true;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        if(!(empty($cod))) {
            $sql="SELECT $camporetorna FROM $tabela where $campobusca='$cod';";
            //echo $sql;
            $resultado=$con->Query($sql);
            if($con->NumRows($resultado)>0) {
                $retorno=$con->FetchArray($resultado,$camporetorna);
                $con->Disconnect();
                if(strtoupper(substr($camporetorna,0,4))=="SUM"."(") $camporetorna="sum";
                if(strtoupper(substr($camporetorna,0,4))=="AVG"."(") $camporetorna="avg";
                if(strtoupper(substr($camporetorna,0,4))=="MAX"."(") $camporetorna="max";
                if(strtoupper(substr($camporetorna,0,4))=="MIN"."(") $camporetorna="min";
                if(strtoupper(substr($camporetorna,0,4))=="COUNT"."(") $camporetorna="count";
                return $retorno["$camporetorna"];
            } else {
                $con->Disconnect();
                if($msg) msg("$camporetorna de $tabela nao encontrado!");
                return false;
            }
        }
        return false;
    }

    function retornabusca4($campo,$tabela,$where,$like,$msg=false) {
        return $this->retornabusca3($tabela,$like,$where,$campo,$msg);
    }


    function date_diff($from, $to) {


        //list($from_month, $from_day, $from_year) = explode("-", $from);
        //list($to_month, $to_day, $to_year) = explode("-", $to);
        list($from_day, $from_month, $from_year) = explode("-", $from);
        list($to_day, $to_month, $to_year) = explode("-", $to);

        $from_date = mktime(0,0,0,$from_month,$from_day,$from_year);
        $to_date = mktime(0,0,0,$to_month,$to_day,$to_year);

        $days = ($to_date - $from_date)/86400;

        /*Adicionado o ceil($days) para garantir que o resultado seja sempre um número inteiro */

        return ceil($days);
    }

    function leading_zero( $aNumber, $intPart, $floatPart=NULL, $dec_point=NULL, $thousands_sep=NULL) {        //Note: The $thousands_sep has no real function because it will be "disturbed" by plain leading zeros -> the main goal of the function
        $formattedNumber = $aNumber;
        if (!is_null($floatPart)) {    //without 3rd parameters the "float part" of the float shouldn't be touched
            $formattedNumber = number_format($formattedNumber, $floatPart, $dec_point, $thousands_sep);
        }
        //if ($intPart > floor(log10($formattedNumber)))
        $formattedNumber = str_repeat("0",($intPart + -1 - floor(log10($formattedNumber)))).$formattedNumber;
        return $formattedNumber;
    }

    function fecha_janela($widget=false,$destroy=false) {
        if($widget) {
            if($destroy) {
                $widget->destroy();
            }else {
                $widget->hide();
            }
        }else {
            if($destroy) {
                $this->janela->destroy();
            }else {
                $this->janela->hide();
            }
        }
        return true;
    }

    // funcoes de navegacao nos cadastros
    function cadastro_primeiro($tabela,$classe,$chave,$funcNovo,$funcAtual) {
        if($this->TotalDeLinhasClistPrincipal>0) {
            $selecao=$this->treeview->get_selection();
            $selecao->select_path("0"); // seleciona a linha
            $this->button_transfere_clist->clicked();
        }
        return;
        // $tabela = a tabela sql do cadastro em questao
        // $classe = o "class" que a tabela esta sendo editada
        // $chave = o primary key da tabela
        // $funcNovo = funcao que limpa os campos pra um novo registro
        // $funcAtual = funcao que atualiza os campos
        //eval("global \$$classe;");
        /*
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();	
    
        $sql="select min($chave) from $tabela;";
        $resultado=$con->Query($sql);
        $ponto=$con->FetchRow($resultado);
        //se nao tiver nenhum registro limpa os campos
        if(empty($ponto[0])){
            $this->func_novo();
            return;
        }
        // senao atualiza os campos
        $sql="select * from $tabela where $chave='$ponto[0]'";
        $resultado=$con->Query($sql);
        $this->atualiza($resultado);
        $con->Disconnect();*/
    }
    function cadastro_ultimo($tabela,$classe,$chave,$funcNovo,$funcAtual) {
        if($this->TotalDeLinhasClistPrincipal>0) {
            $selecao=$this->treeview->get_selection();
            $selecao->select_path(strval($this->TotalDeLinhasClistPrincipal-1)); // seleciona a linha
            $this->button_transfere_clist->clicked();
        }
        return;
        /*
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        
        $con->Connect();	
        $sql="select max($chave) from $tabela;";        
        $resultado=$con->Query($sql);
        $ponto=$con->FetchRow($resultado);        
        if(empty($ponto[0])){
            $this->func_novo();            
            return;
        }        
        $sql="select * from $tabela where $chave='$ponto[0]'";
        $resultado=$con->Query($sql);
        $this->atualiza($resultado);
        $con->Disconnect();*/
    }
    function cadastro_proximo($tabela,$classe,$chave,$funcNovo,$funcAtual,$entryCodigo) {
        // $entryCodigo = o entry que tem o codigo $chave, o primary key da tabela
        //eval("global \$$classe;");
        if($this->TotalDeLinhasClistPrincipal==0) return;
        $selecao=$this->treeview->get_selection();
        list($model, $iter) = $selecao->get_selected();
        $path=$this->liststore->get_path($iter);
        $linha=$path[0]+1;
        if($linha>($this->TotalDeLinhasClistPrincipal-1)) $linha=$this->TotalDeLinhasClistPrincipal-1;
        $selecao->select_path(strval($linha)); // seleciona a proxima linha
        $this->button_transfere_clist->clicked();
        return;
        /*
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        
        $con->Connect();	
        $sql="select max($chave) from $tabela;";
        $resultado=$con->Query($sql);
        $ponto=$con->FetchRow($resultado);
        $regatual=$entryCodigo->get_text();
        $proximo=intval($regatual)+1;
        $achou=false;
        while ($achou==false){
            $sql="select * from $tabela where $chave='$proximo'";
            $resultado=$con->Query($sql);
            $numero=$con->NumRows($resultado);
            if($numero==0){
                $achou=false;
                $proximo++;
            } else { $achou=true;}
            if ($proximo>$ponto[0]) { $achou=true; return; }
        }
        $this->atualiza($resultado);
        $con->Disconnect();	*/
    }
    function cadastro_anterior($tabela,$classe,$chave,$funcNovo,$funcAtual,$entryCodigo) {
        if($this->TotalDeLinhasClistPrincipal==0) return;
        $selecao=$this->treeview->get_selection();
        list($model, $iter) = $selecao->get_selected();
        @$path=$this->liststore->get_path($iter);
        $linha=$path[0]-1;
        if($linha<0) $linha=0;
        $selecao->select_path($linha); // seleciona a proxima linha
        $this->button_transfere_clist->clicked();
        return;
        /*
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;    
        
        $con->Connect();	
        $regatual=$entryCodigo->get_text();
        
        
        $proximo=intval($regatual)-1;
        $achou=false;
        while ($achou==false){
            $sql="select * from $tabela where $chave='$proximo'";		
            $resultado=$con->Query($sql);
            $numero=$con->NumRows($resultado);
            if($numero==0){
                $achou=false;
                $proximo--;
            } else { $achou=true;}
            if ($proximo<=0) { $achou=true; return; }
        }
        $this->atualiza($resultado);
        $con->Disconnect();*/
    }

    function confirma_excluir($tabela,$classe,$chave,$funcNovo,$funcAtual,$entryCodigo, $buttonclist) {
        // $ok = funcao a ser executada de clicar em SIM
        // $txt = texto da pergunta de confirmacao
        // $retorna1 = parametro a passar para a funcao $ok
        //global $bar;
        /*$sn=$this->CarregaGlade('confirma',false,false,false,false);
    
        $rotulo=$sn->get_widget('rotulo');
        $janela=$sn->get_widget('window1');
        $rotulo->set_text('Deseja realmente excluir o registro?');
        $botaoS=$sn->get_widget('sim');
        $botaoN=$sn->get_widget('nao');
        $botaoS->connect_simple('clicked',array($janela,'hide'));
        $botaoS->connect_simple('clicked',
            array(&$this,'cadastro_excluir'),
            $tabela,$chave,$funcNovo,$funcAtual,
            $entryCodigo,&$buttonclist);
        $botaoN->connect_simple('clicked',array($janela,'hide'));*/
        confirma(array($this,'cadastro_excluir'),'Deseja realmente excluir o registro?',$tabela,$chave,$funcNovo,$funcAtual,$entryCodigo,$buttonclist);
    }
    function cadastro_excluir($tabela,$chave,$funcNovo,$funcAtual,$entryCodigo, $buttonclist) {
        // buttonclist eh o button que atualiza a clist
        //eval("global \$$classe;");

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();

        $codigo=$entryCodigo->get_text();
        if($codigo<>"") {
            $sql="delete from $tabela where $chave='$codigo'";
            if ($con->Query($sql)) {
                $this->status('Registro excluido com sucesso');
                $this->cadastro_anterior($tabela,null,$chave,$funcNovo, $funcAtual, null);
                $this->decideSeAtualizaClist();
            }else {
                msg("Erro ao excluir");
            }
        }else {
            msg('Registro NAO excluido. "Codigo" em branco!');
        }
        $con->Disconnect();
    }




    /**********************************
    // funcoes para CList nos Cadastros
    // INICIO
     */
    function decideSeAtualizaClist() {
        if($this->retorna_OPCAO("autotreeview")=="1") {
            $this->button_atualiza_clist->clicked();
        }else {
            $this->botaoatualizaBotaVermelho();
        }
    }

    function botaoatualizaBotaVermelho() {
        if($this->botaoatualizaVerdeVermelho) {
            //$p = GdkPixbuf::new_from_file('interface'.bar.'off.png');
            //$a = new GtkImage;
            //$a->set_from_pixbuf($p);
            $a=GtkImage::new_from_file('interface'.bar.'off.png');
            $this->botaoatualizaVerdeVermelho->set_image($a);
        }else {
            $this->button_atualiza_clist->clicked();
        }
    }
    function botaoatualizaBotaVerde() {
        if($this->botaoatualizaVerdeVermelho) {
            //$p = GdkPixbuf::new_from_file('interface'.bar.'on.png');
            //$a = new GtkImage;
            //$a->set_from_pixbuf($p);
            $a=GtkImage::new_from_file('interface'.bar.'on.png');
            $this->botaoatualizaVerdeVermelho->set_image($a);
        }
    }

    function cria_clist_cadastro($cadastro,$orderby, $codigoabuscar, $foco_entry, $tabela, $sql, $atualizainicial=true, $delkeyligado=array(true,null), $cortreeview=null, $size=null) {


        // $cadastro eh o nome do cadastro sera criado a clist
        // $orderby eh o campo pelo qual a lista sera ordenada
        // $codigoabuscar eh o PRIMARY KEY da tabela do cadastro. Exemplo: Em clientes o $codigoabuscar eh codcli
        // $foco_entry e o "entry" que recebera o foco depois de dar ENTER na CList
        // $tabela e a tabela no BD
        // $sql eh o inicio de um script SQL, tipo select * from clientes, etc...
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_clist=new $BancoDeDados;
        $this->con_clist->Connect();

        $this->ultColunaClist=-1;
        $this->notebook=$this->xml->get_widget("notebook");

        $this->scrolledwindow=$this->xml->get_widget("scrolledwindow");
        $window1 = $this->xml->get_widget("window1");
        if(empty($sql)) {
            $campos=$this->nomes_dos_campos($tabela);
        }else {
            $tmp=$this->con_clist->Query($sql." limit 1");
            for ($i=0;$i<$this->con_clist->NumFields($tmp);$i++) {
                $campos[$i]=$this->con_clist->FieldName($tmp,$i);
            }
        }

        $tmp=str_repeat('GObject::TYPE_STRING,',count($campos));
        $tmp=substr($tmp,0,-1);
        eval('$this->liststore=new GtkListStore('.$tmp.');');
        $this->treeview = new GtkTreeView($this->liststore);
        //$teste = new Interactive_Search($this->treeview);

        // teste de hide show coluna
        $this->colunasClistPrincipal=$this->add_coluna_treeview($campos,$this->treeview,$size,null,null,$tabela, $cortreeview);
        //,$size=null,$cor=null,$fonte=null,$tabela=null

        $this->treeview->set_rules_hint(TRUE);
        $this->treeview->connect('row-activated',array(&$this,clicaNoBotaoTransfere));


        $this->scrolledwindow->add($this->treeview);

        $this->button_atualiza_clist=new GtkButton();

        $this->button_atualiza_clist->connect_simple("clicked",
                array(&$this,"atualiza_clist_cadastro"),
                "$tabela",
                array(&$this, "clist"),
                $orderby,
                $sql
        );

        $this->button_transfere_clist= new GtkButton();
        $this->button_transfere_clist->connect_simple("clicked",
                array(&$this,"transfere_clist_cadastro"),
                "$tabela",
                $codigoabuscar,
                array(&$this,'clist'),
                "$cadastro",
                "atualiza",
                array(&$this,'notebook'),
                $foco_entry
        );
        $this->clicaNoBotaoAtualiza();

        if(!$status=$this->xml->get_widget('status')) {
            $hbox=new GtkHbox(false,0);
            $this->botaoatualizaVerdeVermelho=new GtkButton("");
            //$botaoatualiza2=GtkButton::new_from_stock('gtk-refresh');
            // bota imagem no botao
            //$p = GdkPixbuf::new_from_file('interface'.bar.'on.png');
            //$a = new GtkImage;
            //$a->set_from_pixbuf($p);
            //$this->botaoatualizaVerdeVermelho->set_image($a);

            // bota imagem no botao
            $botaovermelholista=new GtkButton("");
            //$p = GdkPixbuf::new_from_file('interface'.bar.'lista.png');
            //$a = new GtkImage;
            //$a->set_from_pixbuf($p);
            $a=GtkImage::new_from_file('interface'.bar.'lista.png');
            $botaovermelholista->set_image($a);
            // muda de verde ou vermelho
            $botaovermelholista->connect_simple('clicked',array($this,'clicaNoBotaoVermelhoLista'));
            $botaovermelholista->set_size_request(38,25);

            $this->botaoatualizaBotaVerde();
            $this->botaoatualizaVerdeVermelho->connect_simple('clicked',array($this,'clicaNoBotaoAtualiza'));
            $this->botaoatualizaVerdeVermelho->set_size_request(22,25);
            $this->status=new GtkLabel();
            $this->status->set_use_markup(true);

            $tooltips = new GtkTooltips();
            $tooltips->set_tip($botaovermelholista, "Press button to re-set tooltip");
            $tooltips->set_delay(10);

            $hbox->pack_start($this->botaoatualizaVerdeVermelho,false);
            $hbox->pack_start($this->status,true,true);
            $hbox->pack_start($botaovermelholista,false);

            $vbox1=$this->xml->get_widget('vbox1');
            $vbox1->pack_start($hbox,false);
            $vbox1->show_all();

            $accgrp = new GtkAccelGroup();
            //$this->botaoatualizaVerdeVermelho->add_accelerator('clicked', $accgrp, Gdk::KEY_F5, 0, Gtk::ACCEL_VISIBLE);
            $this->botaoatualizaVerdeVermelho->add_accelerator('clicked', $accgrp, 65474, 0, Gtk::ACCEL_VISIBLE);
            $window1->add_accel_group($accgrp);
        }

        if($delkeyligado[0]) $this->treeview->connect('key-press-event', array($this,treeviewCadastroDelKey), $tabela, $codigoabuscar, $delkeyligado[1]);


        $this->con_clist->Disconnect();
        $this->treeview->grab_focus();
        //$this->treeview->set_search_column(1);
        $this->scrolledwindow->show_all();
    }
    function checkHideShow($button,$i) {
        if($button->get_active()) {
            $this->colunasClistPrincipal[$i]->set_visible(true);
        }else {
            $this->colunasClistPrincipal[$i]->set_visible(false);
        }
    }
    function fechaHideShow() {
        // fecha janelinha que mostra/esconde colunas do GtkTreeView
        if(is_a($this->windowHideShow,"GtkWindow")) {
            $this->windowHideShow->destroy();
        }
    }
    function clicaNoBotaoVermelhoLista() {
        $this->fechaHideShow();
        $this->windowHideShow=new GtkWindow();
        $vbox=new GtkVBox();
        for($i=0;$i<count($this->colunasClistPrincipal);$i++) {
            $check[$i]=new GtkCheckButton($this->colunasClistPrincipal[$i]->get_title());
            $check[$i]->set_active($this->colunasClistPrincipal[$i]->get_visible());
            $check[$i]->connect('toggled',array($this,'checkHideShow'),$i);
            $vbox->pack_start($check[$i]);
        }
        $this->windowHideShow->add($vbox);
        $this->windowHideShow->show_all();
    }

    function treeviewCadastroDelKey($widget, $evento, $tabela, $codigoabuscar, $permissao) {
        $tecla=$evento->keyval;
        //echo $tecla."\n";
        if($tecla==65481) { 	// F12
            $this->clicaNoBotaoVermelhoLista();
        }elseif($tecla==65535 or $tecla==65439) {  	//del
            $selecionado=$this->treeview->get_selection();
            if($iter=$this->get_iter_liststore($selecionado,$this->liststore)) {
                $codigo=$this->pegaNumero($this->get_valor_liststore($selecionado,$this->liststore,0));
                if(!empty($codigo)) {
                    $sql="SELECT $codigoabuscar FROM $tabela WHERE $codigoabuscar='$codigo'";
                    $BancoDeDados=retorna_CONFIG("BancoDeDados");
                    $con=new $BancoDeDados;
                    $con->Connect();
                    $resultado=$con->Query($sql);
                    if($con->NumRows($resultado)>1) {
                        msg('Codigo duplicado');
                        $con->Disconnect();
                        return;
                    }else {
                        if($this->verificaPermissao($permissao,true)) {
                            confirma(array($this,'treeviewCadastroDelKey2'),'Deseja realmente excluir este registro?', $tabela, $codigoabuscar, $codigo);
                        }

                    }
                    $con->Disconnect();
                }else {
                    msg("Codigo vazio");
                }
            }else {
                msg("Nao peguei iter");
            }
            //return true;
        }
        //return false;
    }

    function treeviewCadastroDelKey2($tabela, $codigoabuscar, $codigo) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="DELETE FROM $tabela WHERE $codigoabuscar='$codigo'";
        if(!$con->Query($sql,false)) {
            msg("Impossivel excluir! Talvez esse registro esta sendo usado por outro cadastro ou movimentacao.");
        }else {
            msg('Registro excluido com sucesso. Atualize sua tela!');
            $this->decideSeAtualizaClist();
        }
        $con->Disconnect();
        return;
    }

    function clicaNoBotaoAtualiza() {
        $this->button_atualiza_clist->clicked();
    }
    function clicaNoBotaoTransfere() {
        $this->button_transfere_clist->clicked();
    }


    function atualiza_clist_cadastro($tabela, $clist, $orderby, $sql) {
        //echo $sql;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_clist=new $BancoDeDados;
        $this->con_clist->Connect();
        if($orderby) $sql.=" ORDER BY $orderby";
        $this->resultado_clist=$this->con_clist->Query($sql);
        //LIMIT ".retorna_CONFIG("limitequery"));
        $this->colocaLinhasClist($tabela,true);
        $this->treeview->grab_focus();
        $this->botaoatualizaBotaVerde();
    }

    function transfere_clist_cadastro($tabela,$codigoabuscar,$clist,$cadastro,$funcao,$notebook,$foco_entry) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $this->con_clist=new $BancoDeDados;
        $this->con_clist->Connect();

        if($selecionado=$this->treeview->get_selection()) {
            $cp0=$this->get_valor_liststore($selecionado,$this->liststore);

            $sql="SELECT * FROM $tabela where $codigoabuscar='$cp0'";
            //echo "sql2=".$sql;
            $resultado=$this->con_clist->Query($sql);
            if($this->con_clist->NumRows($resultado)==0) {
                $this->status('Registro inexistente. Pressione o botao vermelho/verde ao lado para corrigir isso.');
                return;
            }
            $this->atualiza($resultado);

            $this->con_clist->Disconnect();

            // muda foco para a aba cadastro
            $this->notebook->set_current_page(1);

            // coloca cursor no entry $codigoabuscar
            $foco_entry->grab_focus();
        }
        return;
    }

    function colocaLinhasClist($tabela,$atualizando=false) {
        // $atualizando indica se clicou no botao verde/vermelho
        global $parente;

        // guarda modelo para fins de velocidade

        //$search=$this->treeview->get_search_column();

        //$coluna=$this->treeview->get_column($search);
        //$sort=$coluna->get_sort_column_id();

        $this->treeview->set_enable_search(FALSE);
        if($parente) $parente->set_sensitive(FALSE);
        $this->treeview->set_model(null);

        $selecao=$this->treeview->get_selection();
        list($model, $iter456) = $selecao->get_selected();
        if(!empty($iter456)) {
            $path=$this->liststore->get_path($iter456);
            $linhaselecionada=$path[0];
        }
        $this->liststore->clear();
        $dtisohoje=date('Y',time())."-".date('m',time())."-".date('d',time());

        $lin=0;
        $total=$this->con_clist->NumRows($this->resultado_clist);
        $this->CriaProgressBar("Criando lista");

        $this->TotalDeLinhasClistPrincipal=0;
        while ($linha[$lin]=$this->con_clist->FetchRow($this->resultado_clist)) {
            array_walk ($linha[$lin], array($this, 'utf8_encode_array'));

            $this->liststore->append($linha[$lin]);
            $this->TotalDeLinhasClistPrincipal++;
            $atual=(100*$this->TotalDeLinhasClistPrincipal)/$total;
            $this->AtualizaProgressBar(null,$atual,true);

        }
        $this->AtualizaProgressBar(null,$atual,true);
        // seleciona a linha onde vc estava antes de atualizar
        if(!empty($iter456)) {
            if($linhaselecionada>($this->TotalDeLinhasClistPrincipal-1)) {
                $linhaselecionada=$this->TotalDeLinhasClistPrincipal-1;
            }
            if($this->TotalDeLinhasClistPrincipal>0) {
                $selecao->select_path(strval($linhaselecionada));
            }
        }

        $this->AtualizaProgressBar(null,$atual,true);
        $this->FechaProgressBar();

        // retorna o modelo para fins de velocidade
        $this->treeview->set_model($this->liststore);
        $this->treeview->set_enable_search(false);
        //$this->treeview->set_search_column($search);
        //$coluna->set_sort_column_id($sort);
        //msg($search);


        if($parente) $parente->set_sensitive(TRUE);

        //$vbox_menu->show();
        //$depois=gettimeofday();
        //echo "segundos=".($depois['sec']-$agora['sec']);

    }
    /*
    // funcoes para CList nos Cadastros
    // FIM
    ***********************************/

    function chamarelatorio($tabela,$camposdata=null,$sql=null) {
        //global $bar;
        include_once('funcoes'.bar.'menurel.php');
        if(empty($this->relatorio)) {
            $this->relatorio=new geramenurel($tabela,$camposdata,$sql);
        }else {
            $this->relatorio->janela->show();
        }
    }

    /*
*****************************************************
funcoes de foto
*****************************************************
    */
    function ver_foto($foto) {
        //echo "FIXME: imagens funcoes\n";
        //return;
        //global $bar;
        $xml=$this->CarregaGlade("foto",false,false,false,false);
        $this->janela_foto=$xml->get_widget('window1');

        //$janela_foto->set_position(0);
        $this->janela_foto->set_default_size(gdk::screen_width()-20,gdk::screen_height()-100);
        $this->janela_foto->connect_simple('delete-event', array(&$this,'fecha_janela_foto'));
        $this->janela_foto->set_position(GTK_WIN_POS_CENTER);
        $button_fechar_foto=$xml->get_widget('button_fechar_foto');
        $button_fechar_foto->connect_simple('clicked', array(&$this,'fecha_janela_foto'));
        $pixmap_foto=$xml->get_widget('pixmap');
        $button_fechar_foto->grab_focus();
        $this->mostra_foto($pixmap_foto,null,false,$memo);
        if(!empty($memo)) {
            //$memo2=$memo;
            $com='$memo2='.$memo.";";
            eval ($com);
        }else {
            $this->limpar_foto(&$widget);
            return;
        }

        if(!empty($memo2) and is_array($memo2)) {
            $gdkwindow = $this->janela_foto->window;
            $pixs = Gdk::pixmap_create_from_xpm_d($gdkwindow,null,$memo2);
            $pixmap = &new GtkPixmap($pixs[0], $pixs[1]);
            $pixmap_foto->set($pixs[0],$pixs[1]);
            //return $memo2;
        }
        return;
    }
    function fecha_janela_foto() {
        $this->janela_foto->hide();
        return true;
    }
    function limpar_foto($widget) {
        $file=getcwd().bar.'interface'.bar.'fotobranca.png';
        $widget->set_from_file($file);
    }

    function mostra_foto($widget,$resize=null,$especial=false,$imagem,$var) {

        $tmpfile=gettimeofday();
        $tmpfile=$tmpfile['sec'].$tmpfile['usec'].'.txt';
        $relfile=retorna_CONFIG("tmppath").bar.$tmpfile;

        if(!$handle=fopen($relfile,"w")) {
            msg("Erro abrindo o arquivo. Crie a pasta ".retorna_CONFIG("tmppath"));
        }else {
            fwrite($handle, $imagem);
            fclose($handle);
        }
        eval($var.'=file_get_contents ($relfile);');
        $widget->set_from_file($relfile);
        return;
    }

    function buscar_foto($widget,$resize=null, $especial) {
        $this->escolheArquivo(array($this,'buscar_foto2'),$widget, $resize, $especial);
    }
    function buscar_foto2($file,$widget, $resize, $especial) {
        $extgd=gd_info();
        $extensao=strtoupper(substr($file,-3));
        if(($extensao=="JPG" or $extensao=="PEG") and $extgd["JPG Support"]==true) {
            $jpg=true;
        }elseif($extensao=="PNG" and $extgd["PNG Support"]==true) {
            $png=true;
        }elseif($extensao=="GIF" and $extgd["GIF Read Support"]==true) {
            $gif=true;
        }else {
            msg("Formato de imagem nao suportado. Use JPG, PNG ou GIF.\nO nome do arquivo deve possui umas destas extencoes.");
            return;
        }
        $filename = $file;

        // Set a maximum height and width
        $redimensionar=true;
        $width = $resize[0];
        $height = $resize[1];
        if(empty($width) or empty($height)) {
            $redimensionar=false;
        }
        if($redimensionar) {
            // Get new dimensions
            list($width_orig, $height_orig) = getimagesize($filename);

            if ($width && ($width_orig < $height_orig)) {
                $width = ($height / $height_orig) * $width_orig;
            } else {
                $height = ($width / $width_orig) * $height_orig;
            }

            // Resample
            $image_p = imagecreatetruecolor($width, $height);
            if($jpg) {
                $image = imagecreatefromjpeg($filename);
            }elseif($png) {
                $image = imagecreatefrompng($filename);
            }elseif($gif) {
                $image = imagecreatefromgif($filename);
            }


            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

            $tmpfile=gettimeofday();
            $tmpfile=$tmpfile['sec'].$tmpfile['usec'].'.jpg';
            $relfile=retorna_CONFIG("tmppath").bar.$tmpfile;
            imagejpeg($image_p,$relfile,100);
        }else {
            $relfile=$file;
        }

        $widget->set_from_file($relfile);
        //$this->LastImageMostraFoto = file_get_contents ($relfile);
        eval($especial.'=file_get_contents ($relfile);');
        return;
    }

    function shutdown() {
        exit();
    }

    // funcao que manda para impressora os dados do texto
    function manda2impressora($cabecalho,$rodape,$textofinal) {
        $largurapagina=$this->retorna_OPCAO("largurapagina");
        if($largurapagina<39) {
            msg("Impossivel imprimir. Numero de colunas menor que 40. Verifique as configuracoes no menu Sistema->Configuracoes->Gerais");
            return;
        }
        $maximolinhas=$this->retorna_OPCAO("alturarecibo");
        if($maximolinhas<25) {
            msg("Impossivel imprimir. Numero de linhas menor que 25. Verifique as configuracoes no menu Sistema->Configuracoes->Gerais");
            return;
        }

        $textofinal = explode(retorna_CONFIG("quebralinha"), $textofinal);
        $cabecalho=explode(retorna_CONFIG("quebralinha"), $cabecalho);
        $rodape=explode(retorna_CONFIG("quebralinha"), $rodape);

        $alturacab=count($cabecalho);
        $alturatexto=count($textofinal);
        $alturarod=count($rodape);

        $cab=true;
        $rod=false;
        $maximolinhas=$this->retorna_OPCAO("alturarecibo");
        $alturatotal=$alturacab+$alturarod+$alturatexto;
        $continua=true;
        $txtpronto="";
        $linhas=0;
        $j=1;
        while ($j<$alturatexto) {
            $linhas=0;
            if($cab) {
                if($j<>1) {
                    $textonovo.=retorna_CONFIG("quebralinha");
                    $linhas++;
                };
                $controlequebralinha=count($cabecalho);
                foreach ($cabecalho as $t) {
                    $linhas++;
                    $textonovo.=$t;
                    $cab=false;
                    // controla para ultima linha nao ter quebralinha
                    if($cabecalho[$controlequebralinha-1]<>$t) {
                        $textonovo.=retorna_CONFIG("quebralinha");
                    }
                }

            }
            //echo $textonovo;

            for($i=$j; ;$i++) {
                //if($cab){
                $textonovo.=retorna_CONFIG("quebralinha");
                $linhas++;

                //}
                $textonovo.=$textofinal[$i];
                //$linhas++;
                $j++;

                if($linhas>=($maximolinhas-1) or $j>=$alturatexto) {
                    //$i=9999999;
                    //echo "oi2";
                    $rod=true;
                    $cab=true;
                    break 1;
                }
            }

            if($rod) {
                $textonovo.=retorna_CONFIG("quebralinha");
                $linhas++;
                if($j>=$alturatexto and ($alturarod+$linhas)<$maximolinhas ) {
                    //$textonovo.=str_repeat("=", $largurapagina);
                    if($this->retorna_OPCAO("tiporecibo")=="0") {
                        $textonovo.="=======================================================================================================+--------------+-------------";
                    }elseif($this->retorna_OPCAO("tiporecibo")=="1") {
                        $textonovo.=str_repeat("=", $largurapagina);
                    }
                }else {
                    $textonovo.=str_repeat("=-", $largurapagina/4-7)."continua na proxima pagina".str_repeat("=-", $largurapagina/4-6);
                }
                $textonovo.=retorna_CONFIG("quebralinha");
                $linhas++;
                $rod=false;
            }
        }
        // se o rodape nao couber na pagina coloca espacos em branco e o rodape na proxima pag
        while(($alturarod+$linhas)>$maximolinhas) {
            $linhas++;
            if($linhas>1000) {
                msg("Aumente o numero de linhas no formulario");
                break;
            }
            $textonovo.=retorna_CONFIG("quebralinha");
            if($linhas>=$maximolinhas) {
                $linhas=0;
            }
        }
        foreach ($rodape as $t) {
            $textonovo.=$t;
            $textonovo.=retorna_CONFIG("quebralinha");
        }


        $tmpfile=gettimeofday();
        $tmpfile=$tmpfile['sec'].$tmpfile['usec'].'.txt';
        $relfile=retorna_CONFIG("tmppath").bar.$tmpfile;

        if(!$handle=fopen($relfile,"w")) {
            msg("Erro abrindo o arquivo txt. Crie a pasta ".retorna_CONFIG("tmppath"));
        }else {
            // ativa condensado
            //fwrite($handle, chr(15));
            fwrite($handle, $textonovo);
            //fwrite($handle, "\n\n\n\n");
            // form feed
            //fwrite($handle, chr(12));

            if(!fclose($handle)) {
                msg("Erro fechando o arquivo txt");
            } else {
                if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                    pclose(
                            popen(
                            retorna_CONFIG("lpr")." ".$relfile." ".retorna_CONFIG("lpt1"),
                            "r"
                            )
                    );
                } else {
                    //exec(retorna_CONFIG("lpr")." ".$relfile." ".retorna_CONFIG("lpt1")." > /dev/null &");
                    pclose(
                            popen(
                            retorna_CONFIG("lpr")." ".$relfile." ".retorna_CONFIG("lpt1")." > /dev/null &",
                            "r"
                            )
                    );
                }
            }
            //}
        }
    }

    function relHEAD($titulo) {
        $html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
                    <!--
                        Template de Relatï¿½io desenvolvido por Lucas Saud <lucas.saud at gmail dot com>
                    -->
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';

        $html.='<title>'.$titulo.'</title>';
        $html.='
                    <style><!--
            html {
                    margin: 0 0 1px;
                    height: 100%;
                    background-color: white;
                    padding: 0;
            }
            body {
                    margin: 0;
                    background-color: white;
                    padding: 10px;
                    font-size: 13px;
                    font-family: verdana, sans-serif;
            }
            
            table.tbl {
                    margin-top: 10px;
                    width: 100%;
                    border: 1px solid silver;
                    border-collapse: collapse;
                    border-spacing: 0;
                    empty-cells: show;
            }
            
            table.tbl tr.hrw {
                    background-color: #cbd3d9;
                    font-weight: bold;
            }
            
            table.tbl th {
                    border: 1px solid silver;
                    padding: 2px 4px;
                    text-align: left;
                    white-space: nowrap;
            }
            
            
            
            table.tbl td {
                    border: 1px solid silver;
                    padding: 2px 4px;
            }
            
            table.tbl .shr {
                    width: 15%;
                    white-space: nowrap;
            }
            //-->
            </style>
            ';

        $html.='<link rel="stylesheet" type="text/css" href="screen.css" /></head><body>';
        $html.= "<center><h2>$titulo</h2></center>";
        return $html;
    }

    function chamaBrowser($html) {

        $tmpfile=gettimeofday();
        $tmpfile=$tmpfile['sec'].$tmpfile['usec'].'.html';

        $relfile=retorna_CONFIG("tmppath").$tmpfile;

        if(!$handle=fopen($relfile,"w")) {
            msg("Erro abrindo o arquivo html. Crie a pasta ".retorna_CONFIG("tmppath"));
        }else {
            if(!fwrite($handle, $html)) {
                msg("Erro escrevendo no arquivo html");
            }else {
                if(!fclose($handle)) {
                    msg("Erro fechando o arquivo html");
                } else {
                    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                        //pclose(popen("start ".retorna_CONFIG("browser")." file://".$relfile, "r"));
                        pclose(popen("rundll32 url.dll,FileProtocolHandler file://".$relfile, "r"));
                    } else {
                        exec(retorna_CONFIG("browser")." file://".$relfile." > /dev/null &");
                    }
                }
            }
        }
    }
    function chamaEditorTexto($html) {

        $tmpfile=gettimeofday();
        $tmpfile=$tmpfile['sec'].$tmpfile['usec'].'.txt';

        $relfile=retorna_CONFIG("tmppath").$tmpfile;

        if(!$handle=fopen($relfile,"w")) {
            msg("Erro abrindo o arquivo texto. Crie a pasta ".retorna_CONFIG("tmppath"));
        }else {
            if(!fwrite($handle, $html)) {
                msg("Erro escrevendo no arquivo html");
            }else {
                if(!fclose($handle)) {
                    msg("Erro fechando o arquivo html");
                } else {
                    if(!$editortextos=retorna_CONFIG("editortextos")) {
                        msg("Configuracao de Editor de Textos vazia.");
                        return;
                    }
                    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                        pclose(popen("start ".$editortextos." ".$relfile, "r"));
                    } else {
                        exec($editortextos." ".$relfile." > /dev/null &");
                    }
                }
            }
        }
    }

    function geraHTML($titulo,$cabeca, $cabtabela, $corpo, $pe, $chama=true) {
        /*
            Funcao que gera um relatorio HTML e opcionalmente chama o navegador
        $titulo = variavel contendo o titulo do relatorio
        $cabeca = matriz com apenas uma coluna contendo titulos dos relatorio
        $cabtabela = o cabeï¿½lho da tabela (matriz de uma coluna)
        $corpo = o corpo da tabela (matrix bidimensional)
        $pe = matriz com uma coluna contendo msg finais
        $chama = se true chama o browser; se false retorna o html.
        */
        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        // poe o cabecalho html e css com titulo
        $html=$this->relHEAD($titulo);

        // messagens iniciais
        foreach ($cabeca as $tmp) {
            $html.="<center><b>$tmp</b></center>\n";
        }

        $html.="<table class='tbl'>\n";

        // cabecalho da tabela
        $html.="<tr class='hrw'>";
        foreach ($cabtabela as $tmp) {
            $html.="<td class='shr'>$tmp</td>";
        }
        $html.="</tr>\n";

        // corpo da tabela
        foreach ($corpo as $tmp) {
            // ordena linhas
            ksort($tmp);
            reset($tmp);
            $html.="<tr class='crw'>";
            foreach ($tmp as $tmp2) {
                $html.="<td class='shr'>$tmp2</td>";
            }
            $html.="</tr>\n";
        }
        $html.="</table>";
        $html.="<BR>";
        //rodape do relatorio
        foreach ($pe as $tmp) {
            $html.="<center><b>$tmp</b></center>\n";
        }

        $html.="<br><font size='1'><i><a href='http://linuxstok.sourceforge.net'>LinuxStok.sf.net</a> - $this->datadehoje </i></font>\n";
        $html.="</BODY></HTML>";
        if($chama) {
            $this->chamaBrowser($html);
            return;
        }else {
            return $html;
        }

    }
    function geraTELA($titulo,$cabeca, $cabtabela, $corpo, $pe, $chama=true) {
        // gera relatorio na tela
        $vbox=new GtkVBox();
        $label_titulo=new GtkLabel($titulo);
        $vbox->pack_start($label_titulo,false,false);
        for($i=0;$i<count($cabeca);$i++) {
            $label_cabeca[$i]=new GtkLabel($cabeca[$i]);
            $vbox->pack_start($label_cabeca[$i],false,false);
        }
        $tmp="";
        foreach ($cabtabela as $x) {
            $tmp.='GObject::TYPE_STRING,';
        }
        $tmp=substr($tmp,0,-1);
        eval('$liststore_sql=new GtkListStore('.$tmp.');');
        $treeview_sql = new GtkTreeView($liststore_sql);
        $treeview_sql->set_rules_hint(TRUE);
        $treeview_sql->set_enable_search(false);



        $this->add_coluna_treeview($cabtabela,$treeview_sql);

        $this->CriaProgressBar("Criando lista");
        $lin=0;
        $numerolin=count($corpo);
        // corpo da tabela - linhas
        foreach ($corpo as $tmp) {
            //for($i=0;$i<count($tmp);$i++){
            array_walk ($tmp, array($this, 'utf8_encode_array'));
            // ordena linhas
            ksort($tmp);
            reset($tmp);

            $liststore_sql->append($tmp);
            $lin++;
            $atual=(100*$lin)/$numerolin;
            $this->AtualizaProgressBar(null,$atual,false);
            //}
        }
        $this->FechaProgressBar();
        $scrolledwindow_resultado= new GtkScrolledWindow();
        $scrolledwindow_resultado->set_policy(Gtk::POLICY_AUTOMATIC,Gtk::POLICY_AUTOMATIC);

        $scrolledwindow_resultado->add($treeview_sql);

        $window_sql= new GtkWindow();
        $window_sql->set_icon_from_file('tema'.bar.'icone.png');
        $window_sql->connect_simple('delete-event', array($this,'fecha_janela_geraTELA'), $window_sql);
        //$window_sql->set_uposition( retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy") );
        //$window_sql->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );
        $window_sql->maximize();
        $vbox->pack_start($scrolledwindow_resultado,true,true);




        $scrolledwindow_rodape= new GtkScrolledWindow();
        $scrolledwindow_rodape->set_policy(Gtk::POLICY_AUTOMATIC,Gtk::POLICY_AUTOMATIC);
        $vbox_rodape=new GtkVBox();

        for($i=0;$i<count($pe);$i++) {
            $label_pe[$i]=new GtkLabel($pe[$i]);
            $vbox_rodape->pack_start($label_pe[$i],false,false);
        }
        $scrolledwindow_rodape->add_with_viewport($vbox_rodape);
        $pane=new GtkVPaned();

        //$vbox->pack_start($scrolledwindow_rodape,false,false);

        //$window_sql->add($vbox);
        $pane->pack1($vbox,true,true);
        $pane->pack2($scrolledwindow_rodape,false,false);
        $window_sql->add($pane);
        $window_sql->show_all();
    }
    function fecha_janela_geraTELA($window) {
        $window->destroy();
    }
    function geraTEXTO($titulo,$cabeca, $cabtabela, $corpo, $pe, $chama=true) {
        /*
            Funcao que gera um relatorio HTML e opcionalmente chama o navegador
        $titulo = variavel contendo o titulo do relatorio
        $cabeca = matriz com apenas uma coluna contendo titulos dos relatorio
        $cabtabela = o cabecalho da tabela (matriz de uma coluna)
        $corpo = o corpo da tabela (matrix bidimensional)
        $pe = matriz com uma coluna contendo msg finais
        $chama = se true chama o browser; se false retorna o html.
        */
        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        // largura padrao da pagina
        $largura=$this->retorna_OPCAO("largurapagina");

        // decobre a largura das colunas
        foreach ($corpo as $key=>$asd) {
            // ordena linhas
            ksort($asd);
            reset($asd);
            foreach($asd as $key2=>$asd2) {
                $tmp=strlen($asd2);
                if($tmp>$maior[$key2]) {
                    $maior[$key2]=$tmp;
                }
            }
        }
        // verifica se a soma das colunas e maior que a largura da pagina
        foreach ($maior as $tmp) {
            if($tmp>0) {
                $soma+=$tmp;
            }
        }
        $soma+=count($maior)+1; // soma com os separadores de coluna
        if($soma>$largura) {
            msg("Resultado nao cabe na folha de $largura colunas, desculpe.\nTente consultar menos dados, ou use o HTML.");
            return;
        }

        // faz o traco com as ligacoes
        $trasso="";
        foreach($maior as $vezes) {
            $trasso.="+".str_repeat("-", $vezes);
        }
        $trasso.="+";

        // poe o titulo no meio da pagina
        if(strlen($titulo)>0) {
            $txt.=str_repeat(" ", $largura/2-strlen($titulo)/2).$titulo;
            $txt.=retorna_CONFIG("quebralinha");
        }

        // messagens iniciais
        foreach ($cabeca as $tmp) {
            if(!empty($tmp)) {
                $txt.=$tmp;
                $txt.=retorna_CONFIG("quebralinha");
            }
        }

        // trasso inical
        $txt.=$trasso;
        $txt.=retorna_CONFIG("quebralinha");

        // cabecalho da tabela
        foreach($cabtabela as $key=>$asd) {
            if(strlen($asd)>strlen($maior[$key])) {
                // se titulo da coluna for maior que a coluna vai diminuir o titulo
                $asd=substr($asd,0,$maior[$key]);
            }
            // bota titulo da coluna no meio dela
            $txt.="|";
            $titulo_col=str_repeat(" ", $maior[$key]/2-strlen($asd)/2).$asd;
            // espacos depois do titula da coluna
            $titulo_col.=str_repeat(" ", $maior[$key]/2-strlen($asd)/2);
            while (strlen($titulo_col)<$maior[$key]) {
                $titulo_col.=" ";
            }
            $txt.=$titulo_col;
        }

        $txt.="|";

        $txt.=retorna_CONFIG("quebralinha");
        // trasso depois do cabecalho
        $txt.=$trasso;
        $txt.=retorna_CONFIG("quebralinha");

        // corpo da tabela - linhas
        foreach ($corpo as $tmp) {
            // ordena linhas
            ksort($tmp);
            reset($tmp);

            foreach ($tmp as $key=>$tmp2) {
                if(is_numeric(str_replace(",",".",$tmp2))) {
                    $txt.="|".str_pad($tmp2,$maior[$key]," ",STR_PAD_LEFT);
                }else {
                    $txt.="|".str_pad($tmp2,$maior[$key]," ",STR_PAD_RIGHT);
                }
            }
            $txt.="|";
            $txt.=retorna_CONFIG("quebralinha");
        }

        // passa um traco no final
        $txt.=$trasso;
        $txt.=retorna_CONFIG("quebralinha");

        //rodape do relatorio
        foreach ($pe as $tmp) {
            $txt.=$tmp;
            $txt.=retorna_CONFIG("quebralinha");
        }

        if($chama) {
            $this->chamaEditorTexto($txt);
            return;
        }else {
            return $txt;
        }

    }

    function GetSimpleDirArray($sPath, $onlydir = false, $filter = '') {
        $handle=opendir($sPath);
        while (($file = readdir($handle)) !== false) {
            $nPath = "$sPath/$file";
            $is_dir = is_dir($nPath);

            if (!$onlydir)
                $is_dir = !$is_dir;

            if ($is_dir and ($file != '.') and ($file != '..') and ($file != 'CVS')
                    and (substr($file, -1) != '~') and (substr($entry,0,1) != '.')) {
                if ($filter) {
                    if (strstr($file, $filter))
                        $dirs[' ' . $file] = $file;
                }
                else {
                    $dirs[' ' . $file] = $file;
                }
            }
        }
        closedir($handle);
        if ($dirs)
            ksort($dirs);
        return $dirs;
    }

    function carregaGlade($arquivo, $titulo=false, $maximiza=true, $posicao=true, $destroy=true) {
        $file = 'interface'.bar."$arquivo.glade2";
        $xml = new GladeXML($file);

        if($destroy or $posicao or $maximiza or $titulo) {
            $this->janela = $xml->get_widget('window1');
            //$this->janela->connect_simple('hide', array($this,'fechaHideShow'));
            //if($destroy) $this->janela->connect('delete-event', array($this,'fecha_janela'));
            //if($posicao) $this->janela->set_uposition( retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy") );
            //if($maximiza) $this->janela->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );
            //if($titulo) $this->janela->set_title($titulo);
        }
        if(@$janela = $xml->get_widget('window1')) {
            $janela->set_icon_from_file('tema'.bar.'icone.png');
        }
        return $xml;
    }
    /*
    function mdi_reparente($xml){
		global $vbox_menu, $parente, $old_vbox, $old_window;
    	$vbox1=$xml->get_widget('vbox1');
    	if($old_vbox){
			//$vbox_menu->remove($old_vbox);
		    $old_vbox->reparent($old_window);
		    echo "OLD!";
    	}            	
    	$old_vbox=$vbox1;
    	$old_window=$this->janela;
        $vbox1->reparent($vbox_menu);
        $this->janela->set_skip_taskbar_hint(true);
        $this->janela->set_skip_pager_hint(true);
        $this->janela->iconify();
        $parente->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );
        $nome_usuario=$this->retornabusca4("nome","funcionarios","codigo",$usuario);              	
    	$parente->set_title("LinuxStok - ".$usuario." - ".$titulo);
    }
    */

    function AtualizaProgressBar($texto,$atual,$treeview=false,$step=10) {
        if($this->JanelaProgressBar) {
            $label_atual=$this->pegaNumero($this->LabelProgressBar2->get_text());

            //$step=10;

            for($i=0;$i<=100;$i=$i+$step) {
                $cinco[$i]=$i;
            }

            if($cinco[$label_atual]==intval($atual)) return;

            if(!empty($atual) and $cinco[$atual]==intval($atual)) {
                $atual=number_format($atual,0);
                $this->LabelProgressBar2->set_text($atual."%");
                $this->MeuProgressBar->set_fraction($atual/100);
                if($texto) {
                    $this->LabelProgressBar1->set_text($texto);
                }
                while (Gtk::events_pending())Gtk::main_iteration();
            }
        }
        return;
    }
    function tentafecharCriaProgressBar() {
        return true;
    }
    function CriaProgressBar($texto) {
        $this->JanelaProgressBar = new GtkWindow();
        $this->JanelaProgressBar->connect('delete-event',array($this,'tentafecharCriaProgressBar'));
        $this->JanelaProgressBar->set_icon_from_file('tema'.bar.'icone.png');
        $this->JanelaProgressBar->set_position(1);
        $this->JanelaProgressBar->set_modal(TRUE);
        $this->JanelaProgressBar->set_keep_above(true);

        $ajuste = new GtkAdjustment(0, 0, 100, 1, 0, 0);
        $this->MeuProgressBar = new GtkProgressBar($ajuste);
        $this->MeuProgressBar->set_orientation(Gtk::PROGRESS_LEFT_TO_RIGHT);
        //$this->MeuProgressBar->set_show_text(true);
        //$this->MeuProgressBar->set_format_string("%u%%");
        $this->LabelProgressBar1= new GtkLabel;
        $this->LabelProgressBar2= new GtkLabel;
        $this->LabelProgressBar1->set_text($texto);
        $this->LabelProgressBar2->set_text('0,00%');
        $hbox = new GtkVBox;
        $hbox->pack_start($this->LabelProgressBar1);
        $hbox->pack_start($this->MeuProgressBar);
        $hbox->pack_start($this->LabelProgressBar2);
        $this->JanelaProgressBar->add($hbox);
        $this->JanelaProgressBar->show_all();
        while (Gtk::events_pending())Gtk::main_iteration();
        return;
    }
    function FechaProgressBar() {
        if($this->JanelaProgressBar) {
            $this->JanelaProgressBar->destroy();
            while (Gtk::events_pending())Gtk::main_iteration();
        }
        return;
    }

    function importaPadroes() {

        $path=getcwd().bar."DBDriver".bar."padroes".bar;
        $arquivos[0]=$path.'placon.sql';
        $arquivos[1]=$path.'bancos.sql';
        $arquivos[2]=$path.'estados.sql';
        $arquivos[3]=$path.'permissao.sql';
        $arquivos[4]=$path.'default.sql';

        foreach ($arquivos as $filename) {
            //foreach (glob("DBDriver".bar."padroes".bar."*") as $filename) {
            $this->auxcriaBancoDeDados($filename,"Importando $filename");
        }

        //msg("Importacao de dados Padroes finalizada");
        return;
    }
    function ajustalocalidades() {
        return;
        /*$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;                
        $con->Connect();
        
        $this->CriaProgressBar("Ajustando Localidades");        
        $this->AtualizaProgressBar(null,0);        
        $resultado=$con->Query("SELECT chave_local, uf_local, nome_local, cep8_local FROM cep_loc ORDER BY chave_local");
        $total=$con->NumRows($resultado);
        $j=0;
        $sql="";
        while($i=$con->FetchRow($resultado)){
            //$Xnome=str_replace("'","",ucwords($i[2]));
            $Xnome=ucwords($i[2]);
            $Xnome=$con->EscapeString($Xnome);
            $Xnome=$this->tira_acentos($Xnome);
            $Xuf=strtoupper($i[1]);
            $Xcep=$this->mascara2($i[3],'cep');
            $sql="UPDATE cep_loc SET uf_local='$Xuf', nome_local='$Xnome', cep8_local='$Xcep' WHERE chave_local='$i[0]';";
            //$sql.="INSERT INTO cep_loc (uf_local, nome_local, cep8_local, chave_local) VALUES ('$Xuf', '$Xnome', '$Xcep', '$i[0]'); \n";
            $con->Query($sql);
            $atual=(100*$j)/$total;
            if($atual%5==0) $this->AtualizaProgressBar(null,$atual);
            $j++;
        }
        //file_put_contents('localidades.sql',$sql);
        //exit;
        $this->FechaProgressBar();
        $con->Disconnect();
        return;
        */
    }

    function importaExtras() {

        foreach (glob("DBDriver".bar."extras".bar."*") as $filename) {
            $this->auxcriaBancoDeDados($filename,"Importando $filename");
        }
        $this->ajustalocalidades();
        //msg("Importacao de dados Extras finalizada");
    }

    function auxcriaBancoDeDados($arquivo,$msg=null) {
        $this->CriaProgressBar("Lendo $arquivo");
        $this->AtualizaProgressBar(null,0);

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();

        if(substr($arquivo,-2)=="gz") {
            $query2=gzfile($arquivo);
            $total=count($query2);
            for($i=0;$i<$total;$i++) {
                $query3=trim(str_replace(";","",$query2[$i]));
                if(!empty($query3)) {
                    $con->Query($query3,false,null);
                    $atual=(100*$i)/$total;
                    if($atual%5==0) $this->AtualizaProgressBar(null,$atual);
                }
            }
        }elseif(substr($arquivo,-3)=="sql") {
            $handle = fopen ($arquivo, "r");
            $total=filesize($arquivo);
            while (!feof ($handle)) {
                $sql=fgets($handle);
                $sql3=trim(str_replace(";","",$sql));
                if(!empty($sql3)) {
                    $sql3=$this->tira_acentos($sql3);
                    $con->Query($sql3,true,null);
                }
                $soma+=strlen($sql);
                $atual=(100*$soma)/$total;
                if($atual%5==0) $this->AtualizaProgressBar(null,$atual);
            }
            fclose ($handle);
        }
        $con->Disconnect();
        $this->FechaProgressBar();
        return;
    }

    function CriaNovoBancoDeDados($ext) {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();

        $sql1 = file_get_contents ("DBDriver".bar."cria.".$ext);
        $con->Query($sql1,true,"Criando tabelas vazias");
        $con->Disconnect();
        //confirma(array(&$this,'importaPadroes'),"Deseja Importar os Dados Padroes agora? \nSe nao, podera importar depois no menu Sistema/Importar.");
        $this->importaPadroes();
        return;
    }

    function rgb2int($rgb) {
        $hex_red   = substr($rgb,1,2);
        $hex_green = substr($rgb,3,2);
        $hex_blue  = substr($rgb,5,2);

        $dec_red = hexdec($hex_red);
        $dec_green = hexdec($hex_green);
        $dec_blue = hexdec($hex_blue);

        $int_red = $dec_red/255;
        $int_green = $dec_green/255;
        $int_blue = $dec_blue/255;

        return array($int_red, $int_green, $int_blue);
    }

    function get_valor_liststore($selecionado,$liststore,$coluna=0,$msg=true) {
        // pega valor de uma GtkListStore e retorna o campo
        // parametros:
        //   $selecionado: a linha selecionada da tabela. Use $selecionado=$this->treeview->get_selection()
        //   $liststore: a variavel que contem o GtkListStore
        //   $coluna: numero coluna a ter seu valor retornado
        $iter=$this->get_iter_liststore($selecionado,$liststore,$msg);
        $cp0="";
        if(is_a($iter,'GtkTreeIter')) {
            $cp0=$liststore->get_value($iter,$coluna);
        }
        return $cp0;
    }
    function numero_rows_liststore($liststore) {
        $this->numero_rows_liststoreAUX2=0;
        $liststore->foreach(array($this,'numero_rows_liststoreAUX'));
        return $this->numero_rows_liststoreAUX2;
    }

    function numero_rows_liststoreAUX($store, $path, $iter) {
        $this->numero_rows_liststoreAUX2++;
    }

    function get_iter_liststore($selecionado,$liststore,$msg=true) {
        $row=$selecionado->get_selected_rows(); // use $row[1][0][0]
        $iter=@$liststore->get_iter($row[1][0][0]);
        if(empty($iter)) {
            if($msg) msg('Nada Selecionado!!');
            return false;
        }else {
            return $iter;
        }
    }

    function verificaSeExisteNaLista($store, $path, $iter, $coluna, $descricao, $msg=true) {
        // $coluna = coluna da lista pra verifica se ja existe
        // $descricao = valor pra procurar se ja existe
        // exemplo: $this->liststore_contato->foreach( array($this,'verificaSeExisteNaLista'), 0, $nome);
        $this->verificaSeExisteAUX=false;
        $this->verificaSeExisteAUXLastIter=$iter;
        $tmp=$store->get_value($iter,$coluna);
        if($tmp==$descricao) {
            $this->verificaSeExisteAUX=true; // retorna true pra parar o foreach
            if($msg) msg('Informacao ja existe na lista!');
            return $this->verificaSeExisteAUX;
        }
        return $this->verificaSeExisteAUX;
    }

    function add_coluna_treeview($campos,$treeview,$size=null,$cor=null,$fonte=null,$tabela=null,$funcaocor=null) {
        // adiciona uma coluna simples de modo texto numa GtkTreeView
        // parametros:
        //    $campos: um array com os nomes das colunas
        //    $treeview: a variavel que contem o GtkTreeView
        //	  $size : array com os tamanhos das colunas
        //	  $cor: GdkColor
        //	  $fonte: string com a fonte a ser usada nas linhas. Exemplo: "courier bold 12"

        //the text renderer is used to display text
        $cell_renderer = new GtkCellRendererText();
        if($cor) {
            $cell_renderer->set_property("foreground-gdk",$cor);
        }
        if($fonte) {
            $cell_renderer->set_property('font', $fonte);
        }
        //for($i=0;$i<count($campos);$i++){
        $i=0;
        foreach ($campos as $camp) {
            if(!empty($camp)) {
                //Create the first column, make it resizable and sortable
                $col[$i] = new GtkTreeViewColumn($camp, $cell_renderer, 'text', $i);
                $col[$i]->connect('clicked',array($this,'meuSetSearch'),$treeview);
                //make the column resizable in width
                $col[$i]->set_resizable(true);
                //make it sortable and let it sort after model column 1
                //$col[$i]->set_sort_column_id($i);
                $col[$i]->set_clickable(true);
                //$col[$i]->set_sort_indicator(true);
                //$col[$i]->set_sort_column_id(0);
                // tamanho da coluna
                if($size[$i]>0 and $size<999) {
                    $col[$i]->set_min_width($size[$i]);
                    $col[$i]->set_max_width($size[$i]);
                }
                if($size[$i]=="0") {
                    $col[$i]->set_visible(false);
                    $col[$i]->set_max_width(0);
                }
                //if($tabela=="mercadorias"){ // cor das linhas de merc. inativas
                //$col[$i]->set_cell_data_func($cell_renderer, array($this,'cor_treeview_mercadorias'));
                if($funcaocor) {
                    $col[$i]->set_cell_data_func($cell_renderer, array($this,$funcaocor));
                }
                //add the column to the view
                $treeview->append_column($col[$i]);
                $i++;
            }

        }
        return $col;
    }
    function cor_treeview($column,$cell,$liststore,$iter) {
        // implemente esta funcao nos cadastros para atribuir cor nas listas
        // veja o exemplo mercadorias.php
    }


    function meuSetSearch($coluna, $treeview) {
        // quando clica na coluna ele seta para buscar por ela
        //$id=$coluna->get_sort_column_id();
        //$treeview->set_search_column($id);
    }

    function utf8_encode_array (&$array, $key) {
        if(is_array($array)) {
            array_walk ($array, 'utf8_encode_array');
        } else {
            $array = utf8_encode($array);
        }
    }

    function utf8_decode_array (&$array, $key) {
        if(is_array($array)) {
            array_walk ($array, 'utf8_decode_array');
        } else {
            $array = utf8_decode($array);
        }
    }
    function escolheArquivo($funcao,$widget=null, $resize=null, $especial=null) {
        global $parente;
        /* Creating and initialising a new window to add the FileChooser to */
        $window = new GtkWindow();
        $window->set_transient_for($parente);

        /* Setting parameters of the window */
        $window->set_title('Selecione um Arquivo');
        $window->set_default_size(250,60);
        $window->set_border_width(10);

        $this->FolderChooserButton = new GtkFileChooserButton('Selecione a Pasta',
                GTK::FILE_CHOOSER_ACTION_OPEN);

        $path=getcwd().bar.$widget.bar;
        if(is_dir($path)) {
            $this->FolderChooserButton->set_current_folder($path);
        }else {
            $this->FolderChooserButton->set_current_folder(bar);
        }


        /* Creating a label and a quit button */
        $thelabel = new GtkLabel('Clique para selecionar o Arquivo: ');
        $toquit = new GtkButton('_OK');
        $toquit->connect_simple('clicked', array($window, 'hide'));
        $toquit->connect_simple_after('clicked', array($this, 'fecha_escolheArquivo'), $funcao, $widget, $resize, $especial);

        /* Creating a layout to add the elements */
        $thehbox = new GtkHBox();
        $thevbox = new GtkVBox();

        /* Adding the elements to the layout */
        $thehbox->pack_start($thelabel);

        $thevbox->pack_start($thehbox);
        $thevbox->pack_start($this->FolderChooserButton);
        $thevbox->pack_start($toquit);

        /* Displaying the window and starting the main loop */
        $window->add($thevbox);
        $window->grab_focus();
        $window->set_position(3);

        $window->show_all();
    }

    function fecha_escolheArquivo($funcao,$widget, $resize, $especial) {
        $file=$this->FolderChooserButton->get_filename();
        //$path.=bar;
        if(!is_file($file)) {
            msg("Isto não é um arquivo válido!");
            $this->escolheArquivo($funcao,$widget, $resize, $especial);
        }else {
            call_user_func($funcao,$file,$widget, $resize, $especial);
        }
    }

    function escolhePasta($funcao,$widget=null) {
        global $parente;
        /* Creating and initialising a new window to add the FileChooser to */
        $window = new GtkWindow();
        $window->set_transient_for($parente);

        /* Setting parameters of the window */
        $window->set_title('Selecione uma Pasta');
        $window->set_default_size(250,60);
        $window->set_border_width(10);

        $this->FolderChooserButton = new GtkFileChooserButton('Selecione a Pasta',
                Gtk::FILE_CHOOSER_ACTION_SELECT_FOLDER);

        //$this->FolderChooserButton->set_current_folder(bar);
        $path=getcwd().bar.$widget.bar;
        if(is_dir($path)) {
            $this->FolderChooserButton->set_current_folder($path);
        }else {
            $this->FolderChooserButton->set_current_folder(bar);
        }


        /* Creating a label and a quit button */
        $thelabel = new GtkLabel('Clique para selecionar a Pasta: ');
        $toquit = new GtkButton('_OK');
        $toquit->connect_simple('clicked', array($window, 'hide'));
        $toquit->connect_simple_after('clicked', array($this, 'fecha_escolhePasta'), $funcao);

        /* Creating a layout to add the elements */
        $thehbox = new GtkHBox();
        $thevbox = new GtkVBox();

        /* Adding the elements to the layout */
        $thehbox->pack_start($thelabel);

        $thevbox->pack_start($thehbox);
        $thevbox->pack_start($this->FolderChooserButton);
        $thevbox->pack_start($toquit);

        /* Displaying the window and starting the main loop */
        $window->add($thevbox);
        $window->grab_focus();
        $window->set_position(3);

        $window->show_all();
    }

    function fecha_escolhePasta($funcao) {
        $path=$this->FolderChooserButton->get_current_folder();
        $path.=bar;
        if(!is_dir($path)) {
            msg("Isto não é uma pasta válida!");
            $this->escolhePasta($funcao);
        }else {
            call_user_func($funcao,$path);
        }
    }

    function intro_keypressed($widget, $event, $proximo, $anterior=false) {
        // $event->keyval;
        if(!$anterior) {
            if (in_array($event->keyval, array(65289, 65056))) {
                $proximo->grab_focus();
                return true;
            }
        }else {
            if (in_array($event->keyval, array(65289))) {
                $proximo->grab_focus();
                return true;
            }
            if (in_array($event->keyval, array(65056))) { // shift+tab
                $anterior->grab_focus();
                return true;
            }
        }
        return false;
    }

    function VerificaAberturaDoCaixa($cadcaixa,$data) {
        // verifica abertura do caixa
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();

        $sql="SELECT aberto, fechado FROM controlecaixa WHERE dataaberto<='$data' AND datafechado>='$data' AND codcadcaixa=$cadcaixa";
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        $fechado=$resultado2['fechado'];
        $aberto=$resultado2['aberto'];

        if(!$aberto) {
            msg("O caixa $cadcaixa necessita ser aberto para iniciar ".$this->corrigeNumero($data,'data'));
            return false;
        }elseif($fechado) {
            msg("O caixa $cadcaixa esta fechado para ".$this->corrigeNumero($data,'data'));
            return false;
        }
        return true;
    }
    function conectar() {
        return $this->conecta();
    }
    function conecta() {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        return $con;
    }
    function disconectar($con) {
        return $this->disconecta($con);
    }
    function desconectar($con) {
        return $this->disconecta($con);
    }
    function desconecta($con) {
        return $this->disconecta($con);
    }
    function disconecta($con) {
        if(!empty($con)) {
            $con->Disconnect();
        }
    }

    function imprimirRecibo($lastcod, $tabela, $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura) {
        // teste comprimido lx-300
        //$cabecalho.=CHR(27)+CHR(15);
        // teste impressao draf lx-300
        //$cabecalho.=CHR(27)+"x0";

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $largurapagina=$this->retorna_OPCAO("largurapagina");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT s.codcli, s.desconto, s.totalnf, v.nome, v.codigo, c.nome, e.descricao, e.endereco, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep, e.telefone, e.fax, e.celular, c.ie_rg, c.cnpj_cpf, s.hora, s.data FROM ".$tabela." AS s LEFT JOIN clientes AS c ON (s.codcli=c.codigo) LEFT JOIN cadastro2enderecos AS e ON (s.endereco=e.descricao AND e.codigo=s.codcli) LEFT JOIN funcionarios AS v ON (v.codigo=s.vendedor) WHERE  s.".$codtabela."='$lastcod' ";
        if($this->retorna_OPCAO("pdvenderecocliente")) {
            $sql.=" AND e.cadastro='clientes' ";
        }
        //echo $sql;
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        $desconto=$resultado2[1];
        $codcli=$resultado2[0];
        $cliente=$resultado2[5];
        $rg=$resultado2[17];
        $cpf=$resultado2[18];
        $data=$resultado2[20];
        $hora=$resultado2[19];
        $vendedor=$resultado2[3];
        $codvendedor=$resultado2[4];
        $totalnf=$resultado2[2];
        $endereco=$resultado2[7]." ".$resultado2[8].", ".$resultado2[9]." ".$resultado2[10]." ".$resultado2[11]."-".$resultado2[12]." CEP ".$resultado2[13];

        $cabecalho.=$data." ".$hora." "; // tamanho strlen da data e hora eh 20
        $tmp=" Func".str_pad($codvendedor,3,".",STR_PAD_LEFT).", Cod.".$titulo.":".str_pad($lastcod,7,".",STR_PAD_LEFT);
        /*if($this->retorna_OPCAO("tiporecibo")=="1"){
			$cabecalho.=retorna_CONFIG("quebralinha");
		}*/

        @$cabecalho.=str_repeat("-",(($largurapagina-20)-strlen($tmp))).$tmp;

        $cabecalho.=retorna_CONFIG("quebralinha");

        //$tmp1=$this->retorna_OPCAO("cabecalhorecibo");
        //$cabecalho.=str_repeat(" ", $largurapagina/2-strlen($tmp1)/2).$tmp1;
        $cabecalho.=$this->retorna_OPCAO("cabecalhorecibo");
        $cabecalho.=retorna_CONFIG("quebralinha");

        $cabecalho.=str_repeat("=", $largurapagina);
        $cabecalho.=retorna_CONFIG("quebralinha");

        if($this->retorna_OPCAO("reciboimprimircliente") ) {
            $cabecalho.="Cliente...: $codcli - $cliente";
            $cabecalho.=retorna_CONFIG("quebralinha");

            $cabecalho.="Documentos: RG/IE: ";
            if(empty($rg)) {
                $cabecalho.="nao cadastrado ";
            }else {
                $cabecalho.=$rg." ";
            }
            $cabecalho.=" - CPF/CNPJ: ";
            if(empty($cpf)) {
                $cabecalho.="nao cadastrado ";
            }else {
                $cabecalho.=$cpf." ";
            }
            if($this->retorna_OPCAO("tiporecibo")=="1") {
                $cabecalho.=retorna_CONFIG("quebralinha");
            }
            $cabecalho.=" Telefones.: ";
            if(!empty($resultado2[14])) {
                $cabecalho.=$resultado2[14]; // telefone fixo
            }
            if(!empty($resultado2[15])) {
                $cabecalho.=" - ".$resultado2[15]; // celular
            }
            if(!empty($resultado2[16])) {
                $cabecalho.=" - ".$resultado2[16]; // fax
            }
            $cabecalho.=retorna_CONFIG("quebralinha");

            $cabecalho.="Endereco..: ".substr($endereco,0,$largurapagina);
            $cabecalho.=retorna_CONFIG("quebralinha");

            $cabecalho.=str_repeat("=", $largurapagina);
            $cabecalho.=retorna_CONFIG("quebralinha");
        }


        if($this->retorna_OPCAO("tiporecibo")=="0") {
            $cabecalho.="Codigo | Descricao                                                       | Quant. | UN. |  Preco Unit. | PrecoC/Desc. | Preco Total";
            $cabecalho.=retorna_CONFIG("quebralinha");

            $cabecalho.="-------+-----------------------------------------------------------------+--------+-----+--------------+--------------+-------------";
        }elseif($this->retorna_OPCAO("tiporecibo")=="1") {
            $cabecalho.="Codigo * Descrição        \n                Qte x Val. Unitario = Val. Total\n";
        }


        // meio da nota
        $sql="SELECT s.codmerc, m.descricao, s.quantidade, m.unidade, s.precooriginal, s.precocomdesconto, m.resumo FROM ".$tabela2." AS s, mercadorias AS m WHERE s.codmerc=m.codmerc ";

        if(!empty($tipoPPG)) $sql.=" AND s.tipo='".$tipoPPG."' ";
        $sql.=" AND s.".$codtabela2."='$lastcod';";

        $resultado=$con->Query($sql);

        while($i = $con->FetchRow($resultado)) {
            $codigo=$i[0];
            if($this->retorna_OPCAO("recibodescricaoresumida")=="1" and !empty($i[6])) {
                // usa descricao resumida se nao estiver vazia
                $descricao=trim($i[6]);
            }else {
                // usa descricao normal
                $descricao=trim($i[1]);
            }
            $quantidade=floatval($i[2]);
            $unidade=$i[3];
            $precooriginal=$i[4];
            $precocomdesconto=$i[5];
            $precototal=number_format($i[5]*$i[2], 2, ',', '.');
            $meio.=retorna_CONFIG("quebralinha");
            if($this->retorna_OPCAO("tiporecibo")=="0") {
                $meio.=str_pad($codigo,6, " ", STR_PAD_LEFT)." | ".str_pad($descricao,$largurapagina-60-13+7-3)." | ".str_pad($quantidade,6, " ", STR_PAD_LEFT)." | ".str_pad($unidade,3)." | ".str_pad(number_format($precooriginal,2,",","."),12, " ",STR_PAD_LEFT)." | ".str_pad(number_format($precocomdesconto,2,",","."),12, " ",STR_PAD_LEFT)." | ".str_pad($precototal,12, " ",STR_PAD_LEFT);
            }elseif($this->retorna_OPCAO("tiporecibo")=="1") {
                $meio.=str_pad($codigo,6, " ", STR_PAD_LEFT)." * ".str_pad($descricao,$largurapagina-60-13+7-3).retorna_CONFIG("quebralinha")."              ".str_pad($quantidade,3, " ", STR_PAD_LEFT)." ".str_pad($unidade,3)." x R$ ".str_pad(number_format($precocomdesconto,2,",","."),4, " ",STR_PAD_LEFT)." = R$ ".str_pad($precototal,5, " ",STR_PAD_LEFT);
            }

            // total sem desconto
            $totalSemDesconto+=$precooriginal*$quantidade;
            // desconto de cada produto
            $descontoIndi+=($precocomdesconto*$quantidade)-($precooriginal*$quantidade);
        }
        if($this->retorna_OPCAO("tiporecibo")=="0") {
            // total das mercadorias sem desconto
            $totalmerc=number_format($totalSemDesconto,2,",",".");
            $tabtotais[0]="| Preco Bruto..: ".str_repeat(" ",12-strlen($totalmerc)).$totalmerc;
            $tabtotais[0].=retorna_CONFIG("quebralinha");

            // total de descontos individual
            // poe o sinal de + ou -
            $descontoIndiM=number_format($descontoIndi,2,",",".");
            $tabtotais[1]="| Desc/Acrs.Un.: ".str_repeat(" ",12-strlen($descontoIndiM)).$descontoIndiM;
            $tabtotais[1].=retorna_CONFIG("quebralinha");

            // desconto final
            // poe o sinal de + ou -
            $descontoM=number_format($desconto,2,",",".");
            $tabtotais[2]="| Desc/Acrs Tot: ".str_repeat(" ",12-strlen($descontoM)).$descontoM;
            $tabtotais[2].=retorna_CONFIG("quebralinha");

            $tabtotais[3]="+----------------------------";
            $tabtotais[3].=retorna_CONFIG("quebralinha");
            // total final
            // soma total das merc. com desconto final, porque ele e negativo (se for decrescimo)
            $totalfinal=number_format($totalSemDesconto+($desconto+$descontoIndi),2,",",".");
            $tabtotais[4]="| PRECO FINAL..: ".str_repeat(" ",12-strlen($totalfinal)).$totalfinal;
            $tabtotais[4].=retorna_CONFIG("quebralinha");

            $tabtotais[5]="+============================";
            $tabtotais[5].=retorna_CONFIG("quebralinha");
        }elseif($this->retorna_OPCAO("tiporecibo")=="1") {
            $totalfinal=number_format($totalSemDesconto+($desconto+$descontoIndi),2,",",".");
        }
        if(!empty($tipoPPG)) {
            $sql="SELECT m.nnf, m.meio, m.valor, m.data FROM movpagamentos AS m WHERE m.codorigem=$lastcod AND m.tipo='$tipoPPG' ORDER BY m.data";
            $resultado=$con->Query($sql);
            $jtmp=0;
            while($i = $con->FetchRow($resultado)) {
                $forma=$i[1];
                $valor=$i[2];
                $data=$i[3];
                if($this->retorna_OPCAO("tiporecibo")=="0") {
                    $tabpgto[$jtmp]="$forma -> Vencimento: ".$this->corrigeNumero($data,"data").",  Valor: ".number_format($valor,2,",",".");
                }elseif($this->retorna_OPCAO("tiporecibo")=="1") {
                    $tabpgto[$jtmp]="$forma -> R$ ".number_format($valor,2,",",".");
                }
                $jtmp++;
            }
        }
        // pega qual a matriz que tem mais linhas
        if(count($tabpgto)>count($tabtotais)) {
            $fator=count($tabpgto);
        }else {
            $fator=count($tabtotais);
        }
        // junta as duas matrizes numa so linha
        for($i=0;$i<$fator;$i++) {
            $rodape.=$tabpgto[$i];
            if($this->retorna_OPCAO("tiporecibo")=="0") {
                @$rodape.=str_repeat(" ",$largurapagina-strlen($tabpgto[$i])-29);
            }elseif($this->retorna_OPCAO("tiporecibo")=="1") {

            }
            $rodape.=$tabtotais[$i];
            if(strlen($tabtotais[$i])==0) {
                $rodape.=retorna_CONFIG("quebralinha");
            }
        }

        // no orcamento nao sai o rodape (assinaturas ou outra coisa)
        if($assinatura) {
            // imprimir rodape digitado
            $rodape.=retorna_CONFIG("quebralinha");
            $rodape.=$this->retorna_OPCAO("rodaperecibo");
        }
        // verifica se deve perguntar observacao
        if($this->retorna_OPCAO("observacaorecibo")) {
            $obs=inputdialog("Digite uma observacao",null,true);
            // quebra a observacao de acordo com a largura da folha
            $array_obs = str_split($obs, $largurapagina);
            foreach($array_obs as $tmp) {
                $rodape.=retorna_CONFIG("quebralinha");
                $rodape.=$tmp;
            }
        }

        $this->manda2impressora($cabecalho,$rodape,$meio);
        $con->Disconnect();

//        	confirma(array($this,imprimirRecibo),'Deseja imprimir MAIS UMA via do recibo?', $lastcod, $tabela, $slogan, $titulo, $tabela2, $codtabela, $codtabela2, $tipoPPG, $assinatura);


    }

}


?>
