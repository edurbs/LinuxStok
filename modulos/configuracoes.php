<?php
class configuracoes extends funcoes {

    function configuracoes($iniciar=false) {
        if(!$iniciar) return;

        $this->xml=$this->carregaGlade("configuracoes");

        $this->notebook1=$this->xml->get_widget('notebook1');

// aba banco de dados
       
        $this->entry_tipo=$this->xml->get_widget('entry_tipo');

        //$this->comboboxentry_tipo=$this->xml->get_widget('comboboxentry_tipo');
        //$this->entry_tipo=$this->comboboxentry_tipo->entry;
        //$this->entry_tipo=$this->xml->get_widget('entry_tipo');
        $this->entry_host=$this->xml->get_widget('entry_host');
        $this->entry_senha=$this->xml->get_widget('entry_senha');
        $this->entry_nome=$this->xml->get_widget('entry_nome');
        $this->entry_usuario=$this->xml->get_widget('entry_usuario');
        $this->button_testarconexao=$this->xml->get_widget('button_testarconexao');
        $this->button_testarconexao->connect_simple('clicked',array($this,'testarconexao'));

        $this->entry_cidade=$this->xml->get_widget('entry_cidade');

        $this->comboboxentry_estado=$this->xml->get_widget('comboboxentry_estado');
        //$this->entry_estado=$this->comboboxentry_estado->entry;

        $this->entry_estado=$this->xml->get_widget('entry_estado');
        //$this->combo_estado=$this->xml->get_widget('combo_estado');
        $this->entry_estado->set_text(retorna_CONFIG("Estado"));
        $this->entry_estado->connect_simple('changed', array($this,'cidadesNew'),$this->entry_cidade, $this->entry_estado);
        //$this->encheComboEstado($this->comboboxentry_estado);
        $this->cidadesNew($this->entry_cidade,$this->entry_estado);

        $this->entry_ddd=$this->xml->get_widget('entry_ddd');
        $this->entry_cep=$this->xml->get_widget('entry_cep');
        $this->entry_cep->connect('key-press-event', array(&$this, 'mascara') ,'cep',null,null,null);

// aba impressora

        $this->entry_lpr=$this->xml->get_widget('entry_lpr');
        $this->entry_lpt1=$this->xml->get_widget('entry_lpt1');
        $this->entry_browser=$this->xml->get_widget('entry_browser');
        $this->entry_tmppath=$this->xml->get_widget('entry_tmppath');
        $this->entry_editortextos=$this->xml->get_widget('entry_editortextos');

// outras
        $this->entry_calculadora=$this->xml->get_widget('entry_calculadora');

// botoes        
        $button_gravar=$this->xml->get_widget('button_gravar');
        $button_gravar->connect_simple('clicked', confirma, array(&$this, 'func_gravar'),'Voce tem certeza que deseja alterar as configuracoes?',null);

        $button_restaurar=$this->xml->get_widget('button_restaurar');
        $button_restaurar->connect_simple('clicked', confirma, array(&$this, 'func_restaurar'),'Voce deseja restaurar as configuracoes originais?',null,true);

        $button_mostrar=$this->xml->get_widget('button_mostrar');
        $button_mostrar->connect_simple('clicked', array(&$this, 'func_mostrar'));

        $this->func_mostrar();
        //$this->janela->show();
        //$this->janela->set_focus($this->entry_usuario);
    }
    function testarconexao() {
        $tipo=$this->entry_tipo->get_text();
        $host=$this->entry_host->get_text();
        $nome=$this->entry_nome->get_text();
        $usuario=$this->entry_usuario->get_text();
        $senha=$this->entry_senha->get_text();

        if($tipo=="SQLite" or $tipo=="AgataSqlite") {
            //if (!is_writable($nome)) {
            //	msg("Escolha uma pasta com permissoes de gravacao");
            //	return;
            //}
            if(sqlite_open($nome)) {
                msg("Conexao com Sqlite realizada com sucesso!!!");
            }else {
                msg("Erro ao conectar ao servidor Sqlite");
            }
        }elseif($tipo=="SQLite3" or $tipo=="AgataSqlite3") {
            //if (!is_writable($nome)) {
            //	msg("Escolha uma pasta com permissoes de gravacao");
            //	return;
            //}
            if($dbh = new PDO("sqlite:$nome")) {
                msg("Conexao com Sqlite3 realizada com sucesso!!!");
            }else {
                msg("Erro ao conectar ao servidor Sqlite3");
            }
        }elseif($tipo=="MySQL" or $tipo=="AgataMysql" or $tipo=="MySQLi" or $tipo=="AgataMysqli") {
            if ($host && $usuario && $senha) {
                @$conn = mysql_connect($host, $usuario, $senha);
            }
            elseif ($host && $usuario) {
                @$conn = mysql_connect($host, $usuario);
            }
            elseif ($host) {
                @$conn = mysql_connect($host);
            }
            else {
                $conn = false;
            }

            if (!$conn) {
                msg("Erro ao conectar ao servidor MySQL");
            }else {
                msg("Conexao com MySQL realizada com sucesso!!!");
            }
        }elseif($tipo=="PostgreSQL" or $tipo=="AgataPgsql") {
            $protocol = 'tcp';
            $connstr="";
            if (strpos($host, ':')) {
                $pieces = explode(':', $host);
                $host = $pieces[0];
                $port = $pieces[1];
                $connstr .= "port=" . $port;
            }
            if (!empty($host)) {
                $connstr .= "host=" . $host;
            }

            if (!empty($nome)) {
                $connstr .= " dbname=" . $nome;
            }
            if (!empty($usuario)) {
                $connstr .= " user=" . $usuario;
            }
            if (!empty($senha)) {
                $connstr .= " password=" . $senha;
            }

            if(pg_pconnect($connstr)) {
                msg("Conexao com PostgreSQL realizada com sucesso!!!");
            }else {
                msg("Erro ao conectar ao servidor PostgreSQL. \nVoce devera ter criado o banco de dados por rodar o script DBDriver/cria.pgsql \nVerifique tambem o arquivo pg_hba.conf para configurar os direitos de acesso ao banco de dados. ");
            }
        }
    }

    function func_gravar() {
        $this->tipo=$this->entry_tipo->get_text();
        if($this->tipo=="SQLite") {
            $this->tipo="AgataSqlite";
        }elseif($this->tipo=="SQLite3") {
            $this->tipo="AgataSqlite3";
        }elseif($this->tipo=="MySQL") {
            $this->tipo="AgataMysql";
        }elseif($this->tipo=="MySQLi") {
            $this->tipo="AgataMysqli";
        }elseif($this->tipo=="PostgreSQL") {
            $this->tipo="AgataPgsql";
        }
        $this->host=$this->entry_host->get_text();
        $this->nome=$this->entry_nome->get_text();
        $this->usuario=$this->entry_usuario->get_text();
        $this->senha=$this->entry_senha->get_text();

        $this->estado=strtoupper($this->entry_estado->get_text());
        $this->cidade=$this->entry_cidade->get_text();
        $this->ddd=$this->entry_ddd->get_text();
        $this->cep=$this->entry_cep->get_text();

        $this->lpr=$this->entry_lpr->get_text();
        $this->lpt1=$this->entry_lpt1->get_text();
        $this->browser=$this->entry_browser->get_text();
        $this->tmppath=$this->entry_tmppath->get_text();
        $this->editortextos=$this->entry_editortextos->get_text();

        $this->calculadora=$this->entry_calculadora->get_text();

        $this->gravaconfig(true);
    }

    function func_restaurar($modulo=null,$msg=false) {
        $this->tipo="AgataSqlite";
        $this->host="";
        $this->nome="DBDriver".bar."linuxstok.db";
        //$this->nome="linuxstok";
        $this->usuario="";
        $this->senha="";
        $this->estado="MG";
        $this->cidade="Varginha";
        $this->ddd="(35)";
        $this->cep="37.100-000";

        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $this->lpr="print ";
            $this->lpt1=" ";
            $this->browser="c:\\arquiv~1\\intern~1\\iexplore.exe";
            $this->tmppath="c:\\windows\\temp\\";
            $this->editortextos="c:\\windows\\notepad.exe";
            $this->calculadora="c:\\windows\\calc.exe";
        }else {
            //$this->lpr="/usr/X11R6/bin/nedit ";
            $this->lpr="/usr/bin/lp ";
            $this->lpt1=" ";
            $this->browser="/usr/bin/firefox";
            $this->tmppath="/tmp/";
            $this->editortextos="/usr/bin/gedit";
            $this->calculadora="/usr/bin/gcalctool";
        }
        $this->gravaconfig($msg);
    }

    function gravaconfig($msg=false) {
        global $GLOBALVERSAO;
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $quebralinha="\n\r";
            $this->rodaperecibo = str_replace($quebralinha, "\n", $this->rodaperecibo);
        }else {
            $quebralinha="\n";
        }


        $conn = sqlite_open('DBDriver'.bar.'.config.db');

        $sql="DROP TABLE config ";
        @sqlite_query($sql, $conn,false);

        $sql="CREATE TABLE config (versao, BancoDeDados, host, database, user, pass,  Estado, Cidade, DDD, CEP, lpr, lpt1, browser, tmppath, editortextos, calculadora, quebralinha);";
        sqlite_query($sql, $conn);

        $sql="INSERT INTO config VALUES ('$GLOBALVERSAO', '$this->tipo', '$this->host', '$this->nome', '$this->usuario', '$this->senha', '$this->estado', '$this->cidade', '$this->ddd', '$this->cep', '$this->lpr', '$this->lpt1', '$this->browser', '$this->tmppath', '$this->editortextos', '$this->calculadora', '$quebralinha');";
        if(!sqlite_query($sql, $conn)) {
            msg("Erro gravando configuracoes");
            return;
        }
        sqlite_close($conn);

        if($msg) {
            msg("Novas configuracoes gravadas. Talvez sera preciso reiniciar o LinuxStok para que as configuracoes tenham efeito!");
        }

    }
    function func_mostrar() {
        $this->tipo=retorna_CONFIG("BancoDeDados");
        if($this->tipo=="AgataSqlite") {
            $this->tipo="SQLite";
        }elseif($this->tipo=="AgataSqlite3") {
            $this->tipo="SQLite3";
        }elseif($this->tipo=="AgataMysql") {
            $this->tipo="MySQL";
        }elseif($this->tipo=="AgataMysqli") {
            $this->tipo="MySQLi";
        }elseif($this->tipo=="AgataPgsql") {
            $this->tipo="PostgreSQL";
        }

        $this->entry_tipo->set_text($this->tipo);
        $this->entry_host->set_text(retorna_CONFIG("host"));
        $this->entry_nome->set_text(retorna_CONFIG("database"));
        $this->entry_usuario->set_text(retorna_CONFIG("user"));
        $this->entry_senha->set_text(retorna_CONFIG("pass"));
        $this->entry_estado->set_text(retorna_CONFIG("Estado"));
        $this->entry_cidade->set_text(retorna_CONFIG("Cidade"));
        $this->entry_ddd->set_text(retorna_CONFIG("DDD"));
        $this->entry_cep->set_text(retorna_CONFIG("CEP"));

        $this->entry_lpr->set_text(retorna_CONFIG("lpr"));
        $this->entry_lpt1->set_text(retorna_CONFIG("lpt1"));
        $this->entry_browser->set_text(retorna_CONFIG("browser"));
        $this->entry_tmppath->set_text(retorna_CONFIG("tmppath"));
        $this->entry_editortextos->set_text(retorna_CONFIG("editortextos"));

        $this->entry_calculadora->set_text(retorna_CONFIG("calculadora"));

    }


}
?>