<?php
class relsql extends funcoes {
    function relsql() {
        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        $this->xml=$this->carregaGlade('relsql');

        $this->textView_sql=$this->xml->get_widget('textview_sql');
        $this->textBuffer_sql=new GtkTextBuffer();
        $this->textView_sql->set_buffer($this->textBuffer_sql);

        //$this->textBuffer_sql->set_text("SELECT codigo, cnpj_cpf, nome FROM clientes WHERE dtnasc like '%-\".date('m',time()).\"-\".date('d',time()).\"' ORDER BY nome ");
        $this->textBuffer_sql->set_text("SELECT codigo, cnpj_cpf, nome FROM clientes ORDER BY nome ");

        $this->window_sql=$this->xml->get_widget('window1');
        $this->scrolledwindow_resultado=$this->xml->get_widget('scrolledwindow_resultado');

        $this->bbutton_executar= $this->xml->get_widget('button_executar');
        $this->bbutton_executar->connect_simple("clicked", array($this,"button_executar"));

        $this->bbutton_html= $this->xml->get_widget('button_html');
        $this->bbutton_html->connect_simple("clicked", array($this,"button_html"));

        $this->bbutton_texto= $this->xml->get_widget('button_texto');
        $this->bbutton_texto->connect_simple("clicked", array($this,"button_texto"));

        $this->bbutton_limpar= $this->xml->get_widget('button_limpar');
        $this->bbutton_limpar->connect_simple("clicked", array($this,"button_limpar"));

        $this->bbutton_ajuda= $this->xml->get_widget('button_ajuda');
        $this->bbutton_ajuda->connect_simple("clicked", array($this,"button_ajuda"));

        $this->bbutton_abrir= $this->xml->get_widget('button_abrir');
        $this->bbutton_abrir->connect_simple("clicked", array($this,"button_abrir"));

        $this->bbutton_salvar= $this->xml->get_widget('button_salvar');
        $this->bbutton_salvar->connect_simple("clicked", array($this,"button_salvar"));

        $this->bbutton_fechar= $this->xml->get_widget('button_fechar');
        $this->bbutton_fechar->connect_simple("clicked", array($this,"button_fechar"));
    }

    function button_limpar() {
        $this->textBuffer_sql->set_text('');
        $this->setSQL('');
        //$this->liststore_sql->clear();
        if($this->treeview_sql) {
            $this->treeview_sql->destroy();
        }
    }

    function button_abrir() {
        $this->escolheArquivo(array($this,'button_abrir2'),'Scripts');
    }
    function button_abrir2($file) {
        if(!$texto=file_get_contents($file)) {
            msg("Erro ao abrir arquivo");
        }else {
            $this->textBuffer_sql->set_text($texto);
        }
    }
    function button_salvar() {
        if(!confirma(false,"Deseja realmente salvar este script?")) {
            return;
        }
        //$this->setSQL($sql);
        $this->escolhePasta(array($this,'button_salvar2'),'Scripts');
    }
    function button_salvar2($path) {
        $arquivo=inputdialog("Digite o nome do arquivo (com extensao .sql)");
        if(empty($arquivo)) {
            msg("Preciso do nome do arquivo!!");
            return;
        }

        if(!$sql=$this->getSQL(false)) { // false para nao executar o script php
            return;
        }

        $relfile=$path.$arquivo;
        if(!$handle=fopen($relfile,"w")) {
            msg("Erro abrindo o arquivo.");
        }else {
            if(!fwrite($handle, $sql)) {
                msg("Erro escrevendo no arquivo");
            }else {
                if(!fclose($handle)) {
                    msg("Erro fechando o arquivo");
                }else {
                    msg("Script gravado com sucesso!");
                }
            }
        }

    }
    function button_fechar() {
        $this->window_sql->hide();
    }

    function button_texto() {
        $this->con=$this->conecta();
        if($this->executar_sql()) {
            //WIDTH=80
            $titulo="Relatorio Personalizado";
            $cabeca[0]="";
            for ($i=0;$i<$this->con->NumFields($this->resultado);$i++) {
                $cabtabela[$i]=$this->con->FieldName($this->resultado,$i);
            }
            $j=0;
            while($i = $this->con->FetchRow($this->resultado)) {
                for($t=0;$t<count($cabtabela);$t++) {
                    $corpo[$j][$t]=$i[$t];
                }
                $j++;
            }
            $pe[0]="";
            $this->geraTEXTO($titulo, $cabeca, $cabtabela, $corpo, $pe, true, 80);
        }
        $this->disconecta($this->con);
    }
    function button_ajuda() {
        msg("O arquivo DBDriver/cria.pgsql contem todos nomes das tabelas e colunas. Pode ser util para lhe ajudar a saber quais campos e tabelas estao disponiveis para consulta. \nVoce pode usar tambem codigos PHP dentro do script SQL, basta iniciar o codigo com \". e terminar com .\"");
    }
    function getSQL($execphp=true) {
        $sql=$this->textBuffer_sql->get_text(
                $this->textBuffer_sql->get_start_iter(),
                $this->textBuffer_sql->get_end_iter()
        );
        if(empty($sql)) {
            msg("Digite um comando SQL para executar");
            return false;
        }
        if($execphp) {
            $sql='"'.$sql.'"'; // coloca aspas iniciais e finais
            $tmp='$sql='.$sql.';';
            eval($tmp); // executa codigos php, se houver
        }

        $delete = stripos($sql, 'delete ');
        $update = stripos($sql, 'update ');
        $insert = stripos($sql, 'insert ');

        if ($delete===false and $update===false and $insert===false) {
            return $sql;
        }else {
            if(!confirma(false,"Voce usou DELETE, UPDATE ou INSERT no seu script e isso vai alterar seus dados, podendo haver grandes perdas de informacoes. Tem certeza que deseja continuar?")) {
                return false;
            }else {
                return $sql;
            }
        }
    }
    function setSQL($sql) {
        $this->SQL=$sql;
    }
    function executar_sql() {
        if(!$sql=$this->getSQL()) {
            return;
        }
        $this->setSQL($sql);

        if(!$this->resultado=$this->con->Query($this->SQL)) {
            msg("Erro ao executar comando SQL!");
        }else {
            $this->numerolin=$this->con->NumRows($this->resultado);
            if($this->numerolin==0) {
                msg("Sua consulta nao retornou nenhum resultado!");
            }else {
                return true;
            }
        }
        return false;
    }
    function button_html() {
        $this->con=$this->conecta();
        if($this->executar_sql()) {
            $titulo="Relatorio Personalizado";
            $cabeca[0]="";
            for ($i=0;$i<$this->con->NumFields($this->resultado);$i++) {
                $cabtabela[$i]=$this->con->FieldName($this->resultado,$i);
            }
            $j=0;
            while($i = $this->con->FetchRow($this->resultado)) {
                for($t=0;$t<count($cabtabela);$t++) {
                    $corpo[$j][$t]=$i[$t];
                }
                $j++;
            }
            $pe[0]="";
            $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        }
        $this->disconecta($this->con);
    }
    function button_executar() {
        $this->con=$this->conecta();
        if($this->executar_sql()) {
            for ($i=0;$i<$this->con->NumFields($this->resultado);$i++) {
                $campos[$i]=$this->con->FieldName($this->resultado,$i);
            }
            $tmp=str_repeat('Gobject::TYPE_STRING,',count($campos));
            $tmp=substr($tmp,0,-1);
            eval('$this->liststore_sql=new GtkListStore('.$tmp.');');
            if($this->treeview_sql) {
                $this->treeview_sql->destroy();
            }
            $this->treeview_sql = new GtkTreeView($this->liststore_sql);
            $this->treeview_sql->set_rules_hint(TRUE);
            $this->treeview_sql->set_enable_search(TRUE);

            $this->add_coluna_treeview($campos,$this->treeview_sql);

            $this->CriaProgressBar("Criando lista");
            $lin=0;
            while ($lin<$this->numerolin) {
                $linha[$lin]=$this->con->FetchRow($this->resultado);
                array_walk ($linha[$lin], array($this, 'utf8_encode_array'));
                $this->liststore_sql->append($linha[$lin]);
                $lin++;
                $atual=(100*$lin)/$this->numerolin;
                $this->AtualizaProgressBar(null,$atual,false);
            }
            $this->FechaProgressBar();

            $this->scrolledwindow_resultado->add($this->treeview_sql);

            //$this->window_sql->show_all();
            $this->scrolledwindow_resultado->show_all();
        }
        $this->disconecta($this->con);
    }
}
?>
