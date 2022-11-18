<?php

class backup extends funcoes {
    function backup($funcao) {
        if($funcao=="restaurar") {
            if(confirma(false,"Restauracao de backup vai destruir todos os seus dados atuais e restaurar os dados da copia de seguranca informada. E um procedimento extremamente perigoso e voce deve saber o que esta fazendo! Esta operacao pode demorar MUITO tempo! Tem certeza que deseja continuar?")) {
                $this->escolheArquivo(array($this,'restaurar_bkp'),bar);
            }
        }elseif($funcao=="gravar") {
            if(confirma(false,"A copia de seguranca deve ser feita com apenas 1 (UM) usuario no sistema. Tem certeza que todos os usuarios estao fora do sistema?")) {
                $this->escolhePasta(array($this,'gravar_bkp'),bar);
            }
        }
    }
    function gravar_bkp($path) {
        $arquivo="bkpls-".date("Y_m_d");
        $relfile=$path.$arquivo.".sql";

        if(!$handle=fopen($relfile,"w")) {
            msg("Erro abrindo o arquivo.");
            return;
        }
        // array com nomes das tabelas e suas chaves SERIAIS!! primarias
        $tabelas=array(
                array("midiapropaganda",'codigo'),
                array("cadcaixa",'codigo'),
                array("parentesco",'codigo'),
                array("romaneio",'codigo'),
                array("estados",''),
                array("cep_loc",'chave_local'),
                array("cep_bai",'chave_bai'),
                array("cep",'chave_log'),
                array("nomebanco",''),
                array("nivelacesso",'codigo'),
                array("localarma",'codigo'),
                array("placon",''),
                array("meiopgto",'codigo'),
                array("grpmerc",'codigo'),
                array("profissao",'codigo'),
                array("bancos",'codbanco'),
                array("empregador",'codigo'),
                array("funcionarios",'codigo'),
                array("clientes",'codigo'),
                array("fornecedores",'codigo'),
                array("fabricantes",'codigo'),
                //array("devedores",''), // tabela antiga
                //array("tituloscobranca",''), // tabela antiga
                //array("movcobranca",''), // tabela antiga
                array("cadastro2bancos",'codinterno'),
                array("cadastro2profissao",'codinterno'),
                array("cadastro2enderecos",'codinterno'),
                array("cadastro2contatos",'codinterno'),
                array("cadastro2familias",'codinterno'),
                array("mercadorias",'codmerc'),
                array("orcamento",'codorcamento'),
                array("entradas",'codentradas'),
                array("saidas",'codsaidas'),
                array("entsai",''),
                array("pagar",'codigo'),
                array("receber",'codigo'),
                array("movimentos",'codigompr'),
                array("caixa",'codigo'),
                array("movbanc",'codigo'),
                array("veiculos",'codigo'),
                array("comissao",'codigo'),
                array("cheque",'codigo'),
                array("formapgto",'codigoformapgto'),
                array("parcelapgto",'codigoparcelapgto'),
                array("ctree",''),
                array("permissao",'codigopermissao'),
                array("nivel2funcionario",''),
                array("opcoes",''),
                array("controlecaixa",''),
                array("clientestmp",'codigo'),
                array("devolucoes",'coddevolucoes'),
                array("movpagamentos",''),
                array("entregas",'codentregas'),
                array("entrega_itens",''),
                array("ocorrencia_tipo",'codigo'),
                array("ocorrencia",'codigo')
        );

        $con=$this->conecta();
        $BancoDeDados=retorna_CONFIG("BancoDeDados");

        $this->CriaProgressBar("Fazendo Copia de Seguranca...");
        // drop tables
        $tabelas2=array_reverse($tabelas);
        foreach($tabelas2 as $nome) {
            if($BancoDeDados<>"AgataPgsql") {
                $ifexists="IF EXISTS";
            }else {
                $ifexists="";
            }
            $drop.="DROP TABLE $ifexists $nome[0] ;\n";
        }

        // grava dados de drop
        if(!fwrite($handle, $drop)) {
            msg("Erro escrevendo no arquivo (drop)");
            return;
        }
        // criacao das tabelas

        if($BancoDeDados=="AgataSqlite" or $BancoDeDados=="AgataSqlite3") {
            $create_db="sqlite";
        }elseif($BancoDeDados=="AgataPgsql") {
            $create_db="pgsql";
        }elseif($BancoDeDados=="AgataMysql") {
            $create_db="mysql";
        }
        $create_sql=file_get_contents("DBDriver".bar.'cria.'.$create_db);
        $create_sql=str_replace("\n", "", $create_sql);
        $create_sql=str_replace(";", ";\n", $create_sql);

        // grava dados de create
        if(!fwrite($handle, $create_sql)) {
            msg("Erro escrevendo no arquivo (create_sql)");
            return;
        }

        // ajustando sequencias do postgresql
        if($BancoDeDados=="AgataPgsql") {
            foreach($tabelas as $key=>$table_name) {
                if(!empty($table_name[1])) {
                    $res=$con->Query("select max($table_name[1]) from $table_name[0]");
                    $i=$con->FetchArray($res);
                    if(!empty($i[0])) {
                        $reset_serial.="SELECT setval('$table_name[0]_$table_name[1]_seq', $i[0]) ;\n";
                    }
                }
            }
            if(!fwrite($handle, $reset_serial)) {
                msg("Erro escrevendo no arquivo (reset_serial)");
                return;
            }
        }


        $total=count($tabelas);
        $atual=0;

        foreach($tabelas as $key=>$table_name) {
            $por2=0;
            $atual2=0;
            $atual3=$key;
            $sql="SELECT * FROM $table_name[0] ";
            $res=$con->Query($sql);
            $total2=$con->NumRows($res);
            while($i=$con->FetchRow($res)) {
                $propriedades=$con->Properties($res);
                $this->bkp_file.="INSERT INTO $table_name[0] (";
                foreach ($i as $key=>$row) {
                    $campo=$con->FieldName($res,$key);
                    $this->bkp_file.=" $campo,";
                }
                $this->bkp_file=substr($this->bkp_file,0,-1);
                $this->bkp_file.=") VALUES (";
                foreach ($i as $key=>$row) {
                    /*if($propriedades[$key]["type"]=="date"){
						if(!$this->valida_data($row)){
							$this->bkp_file.="'0001-01-01', ";
						}else{
							$this->bkp_file.="'$row', ";
						}						
					}*/
                    /*elseif(substr(strtoupper($propriedades[$key]["type"]),0,3)=="INT"
					or strtoupper($propriedades[$key]["type"])=="NUMERIC" 
					or strtoupper($propriedades[$key]["type"])=="DECIMAL"
					or strtoupper($propriedades[$key]["type"])=="FLOAT"){
						if($row===""){
							$this->bkp_file.="null, ";
						}else{
							$this->bkp_file.="'$row', ";
						}
					}*/
                    ///else{
                    //if($con->FieldName($res,$key)=="foto"){
                    //}else{
                    $row=$con->EscapeString($row);
                    $row=str_replace("\n",'\n',$row);
                    $row=str_replace("\r",'\r',$row);

                    if($row==="") {
                        $this->bkp_file.="null, ";
                    }else {
                        $this->bkp_file.="'$row', ";
                    }
                    ///}

                }

                $this->bkp_file=substr($this->bkp_file,0,-2);
                $this->bkp_file.=") ;\n";
                $atual++;


                // grava dados
                if(!fwrite($handle, $this->bkp_file)) {
                    msg("Erro escrevendo no arquivo (bkp_file)");
                    return;
                }
                unset($this->bkp_file);

                $por2=(100*$atual2)/$total2;
                $this->AtualizaProgressBar("Copiando tabela $atual3 de $total\n$table_name[0]",$por2,false);
                $atual2++;
            }
        }

        $this->FechaProgressBar();

        if(!fclose($handle)) {
            msg("Erro fechando o arquivo");
            return;
        }else {
            msg("Backup gravado com sucesso!\n" .
                    "Arquivo:\n$relfile\n" .
                    "Tamanho:".number_format(filesize($relfile),0,',',".")." bytes");
        }
        $this->desconecta($con);
    }
    function restaurar_bkp($file) {

        if(!file_exists($file)) {
            msg("Arquivo nao existe!");
            return;
        }

        if(!$handle=fopen($file,"r")) {
            msg("Erro abrindo o arquivo.");
            return;
        }

        $this->CriaProgressBar("Restaurando...");
        while (gtk::events_pending())gtk::main_iteration();
        $con=$this->conecta();
        $total=filesize($file);
        $atual=0;
        while (!feof ($handle)) {
            $por=(100*$atual)/$total;
            $this->AtualizaProgressBar(null,$por,false,1);

            $tmp=fgets($handle);
            $atual+=strlen($tmp);
            if(!empty($tmp)) {
                // retira ponto e virgula por causa do mysql
                $continuar =true;
                $contador=0;
                while ($continuar) {
                    $contador--;
                    $pontoEvirgula=substr($tmp,$contador,1);
                    if($pontoEvirgula==";" or !$pontoEvirgula) {
                        $continuar=false;
                    }
                }
                $tmp=substr($tmp,0,$contador);

                $ok=$con->Query("$tmp");
                if(!$ok) {
                    echo $tmp;
                }
            }

        }
        fclose ($handle);
        $this->desconecta($con);
        $this->FechaProgressBar();
        msg("Restauracao de Bakcup terminada.");
    }
}
?>


