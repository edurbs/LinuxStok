<?php
class importaap2005 extends funcoes {

    function importaap2005(){
        if (!function_exists('dbase_open')){
            msg("Seu PHP não está compilado com suporte a arquivos DBF.");
            return;
        }
        $this->escolhePasta(array($this,'importadbf'));
		//$this->importadbf('/tmp/dbf/');

    }
    
    function importadbf($path=null){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;                
        $con->Connect();
        
        $this->diadehoje=date('d',time());
		$this->mesdehoje=date('m',time());
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        
        require_once("funcoes".bar."dbf2sql.php");       
        $conversao = new dbf2sql;
        //echo $path.".dbf";
        if(file_exists($path."ESTOQUE.DBF") or file_exists($path."estoque.dbf")){
            $dbfsimportados.=" Estoque ";
            $myarrayEstoque= array(             
                      "0" => array("23", "obs"),
                      "1" => array("1", "referencia"),
                      "2" => array("2", "descricao"),
                      "3" => array("5", "unidade"),
                      "4" => array("6", "precovenda"),
                      "5" => array("8", "precocusto"),
                      "6" => array("9", "customedio"),
                      "7" => array("11", "estoqueatual"),
                      "8" => array("12", "estoqueminimo"),
                      "9" => array("17", "peso"),                  
                      "10" => array("19", "ipi"),
                      "11" => array("20", "cst"),
                      "12" => array("21", "st")
                            );
    
            if(file_exists($path."ESTOQUE.DBF")){
                $conversao->opendb($path."ESTOQUE.DBF", true, 'mercadorias');
            }else{
                $conversao->opendb($path."estoque.dbf", true, 'mercadorias');
            }
            
            $ok=$conversao->GetColumns( $myarrayEstoque, true, false);
			$ok=$this->tira_acentos($ok);
			//$ok=utf8_encode($ok);
			
            $con->Query($ok,false,"Gravando mercadorias em SQL");
            $con->Query("UPDATE mercadorias SET codgrpmerc='1', codlocalarma='1', codfab='1', codfor='1', promoinicio='0001-01-01', promofim='0001-01-01', inativa='0', ultimaaltera='".$this->anodehoje."-".$this->mesdehoje."-".$this->diadehoje."'");
        }        
        
        if(file_exists($path."clientes.dbf") or file_exists($path."CLIENTES.DBF")){
            $dbfsimportados.=" Clientes ";
            $myarrayClientes= array(
                              "0" => array("0", "nome"),
                              "1" => array("1", "contato"),
                              "2" => array("2", "ie"),
                              "3" => array("3", "cgc"),
                              "4" => array("4", "enderec"),
                              "5" => array("5", "comple"),
                              "6" => array("6", "cidade"),
                              "7" => array("7", "estado"),
                              "8" => array("8", "cep"),
                              "9" => array("9", "fone"),
                              "10" => array("10", "fax"),
                              "11" => array("11", "email"),
                              "12" => array("12", "obs"),
                              "13" => array("13", "celular"),
                              "14" => array("21", "datanas"),
                              "15" => array("22", "cadastro"),
                              "16" => array("23", "ultimaco")
                            );        
            if(file_exists($path."clientes.dbf")){
                $conversao->opendb($path."clientes.dbf", true, 'clientestmp');
            }else{
                $conversao->opendb($path."CLIENTES.DBF", true, 'clientestmp');
            }
            $ok=$conversao->GetColumns( $myarrayClientes, true, false);
            $ok2=$this->tira_acentos($ok);
            $con->Query("DELETE FROM clientestmp;");
            $con->Query($ok2,false,"Gravando clientes em SQL temp.");
            
            $resultado=$con->Query("SELECT * FROM clientestmp ORDER BY codigo;");
            $total=$con->NumRows($resultado);
            $j=1;
            $sql2="";
            $this->CriaProgressBar("Importando Clientes");
            while($i=$con->FetchArray($resultado)){
                // data de nascimento
                $novadata=substr($i["datanas"],6,2)."-".substr($i["datanas"],4,2)."-".substr($i["datanas"],0,4);
                if(!$this->valida_data($novadata)){
                    $novadata="0001-01-01";
                }else{
                    $novadata=$this->corrigeNumero($novadata,"dataiso");
                }
                // data de cadastro
                $dtcadastro=substr($i["cadastro"],6,2)."-".substr($i["cadastro"],4,2)."-".substr($i["cadastro"],0,4);
                if(!$this->valida_data($dtcadastro)){
                    $dtcadastro="0001-01-01";
                }else{
                    $dtcadastro=$this->corrigeNumero($dtcadastro,"dataiso");
                }
                // ultima venda
                $ultvenda=substr($i["ultimaco"],6,2)."-".substr($i["ultimaco"],4,2)."-".substr($i["ultimaco"],0,4);
                if(!$this->valida_data($ultvenda)){
                    $ultvenda="0001-01-01";
                }else{
                    $ultvenda=$this->corrigeNumero($ultvenda,"dataiso");
                }
                $i['nome']=$con->EscapeString($i['nome']);
                $i['contato']=$con->EscapeString($i['contato']);
                $i['obs']=$con->EscapeString($i['obs']);
                // grava cliente e pega o codigo
                $sql="INSERT INTO clientes (nome, natureza, contato, ie_rg, dtemissaorg, cnpj_cpf, obs, dtnasc, dtcadastro, ultvenda, debmaximo, habcomprar) values('".$i['nome']."', 'Fisica', '".$i['contato']."', '".$i['ie']."', '0001-01-01', '".$i['cgc']."', '".$i['obs']."', '$novadata', '$dtcadastro', '$ultvenda', '1000', 'Liberado')";
                $lastcod=$con->QueryLastCod($sql);
                // grava endereco do codigo acima
				$i['enderec']=$con->EscapeString($i['enderec']);
				$i['comple']=$con->EscapeString($i['comple']);
				$i['cidade']=$con->EscapeString($i['cidade']);
				$i['fone']=$this->arrumatelefone($i['fone']);
				$i['fax']=$this->arrumatelefone($i['fax']);
				$i['celular']=$this->arrumatelefone($i['celular']);

                $sql2.="INSERT INTO cadastro2enderecos (codigo, cadastro, descricao, endereco, bairro, cidade, estado, cep, telefone, fax, celular, email, romaneio) values ('$lastcod', 'clientes', 'principal', '".$i["enderec"]."', '".$i["comple"]."', '".$i["cidade"]."', '".$i["estado"]."', '".$i["cep"]."', '".$i["fone"]."', '".$i["fax"]."', '".$i["celular"]."', '".$i["email"]."', '1');";
     			$atual=(100*$j)/$total;
     			if($atual%5==0){
                		$this->AtualizaProgressBar(null,$atual);
                	}
                $j++;
            }
            $this->FechaProgressBar();
            
            $con->Query($sql2,true,"Importando Enderecos dos clientes");
        }
        
        if(file_exists($path."forneced.dbf") or file_exists($path."FORNECED.DBF")){
            $dbfsimportados.=" Fornecedores ";
            // fornecedores
                    $myarrayFornecedores= array(
                              "0" => array("0", "nome"),
                              "1" => array("1", "contato"),
                              "2" => array("2", "ie"),
                              "3" => array("3", "cgc"),
                              "4" => array("4", "enderec"),
                              "5" => array("5", "comple"),
                              "6" => array("6", "cidade"),
                              "7" => array("7", "estado"),
                              "8" => array("8", "cep"),
                              "9" => array("9", "fone"),
                              "10" => array("10", "fax"),
                              "11" => array("11", "email"),
                              "12" => array("12", "obs")
                            );        
            if(file_exists($path."forneced.dbf")){
                $conversao->opendb($path."forneced.dbf", true, 'clientestmp');
            }else{
                $conversao->opendb($path."FORNECED.DBF", true, 'clientestmp');
            }
            $ok=$conversao->GetColumns( $myarrayFornecedores, true, false);
            $ok2=$this->tira_acentos($ok);
            $con->Query("DELETE FROM clientestmp;");
            $con->Query($ok2,false,"Gravando Fornecedores em SQL temp.");
            
            $resultado=$con->Query("SELECT * FROM clientestmp ORDER BY codigo;");
            $total=$con->NumRows($resultado);
            $j=1;
            $sql2="";
            $this->CriaProgressBar("Importando Fornecedores");
            while($i=$con->FetchArray($resultado)){
                // grava e pega o codigo
                $i['nome']=$con->EscapeString($i['nome']);
                $i['contato']=$con->EscapeString($i['contato']);
                $i['obs']=$con->EscapeString($i['obs']);
                $sql="INSERT INTO fornecedores (nome, natureza, contato, ie_rg, cnpj_cpf, obs, dtnasc, dtcadastro, dtemissaorg) values('".$i['nome']."', 'Juridica', '".$i['contato']."', '".$i['ie']."', '".$i['cgc']."', '".$i['obs']."', '0001-01-01', '0001-01-01', '0001-01-01')";
                $lastcod=$con->QueryLastCod($sql);
                // grava endereco do codigo acima
				$i['enderec']=$con->EscapeString($i['enderec']);
				$i['comple']=$con->EscapeString($i['comple']);
				$i['cidade']=$con->EscapeString($i['cidade']);
				$i['fone']=$this->arrumatelefone($i['fone']);
				$i['fax']=$this->arrumatelefone($i['fax']);
				$i['celular']=$this->arrumatelefone($i['celular']);
                $sql2.="INSERT INTO cadastro2enderecos (codigo, romaneio, cadastro, descricao, endereco, complemento, cidade, estado, cep, telefone, fax, celular, email) values ('$lastcod', '1',  'fornecedores', 'principal', '".$i["enderec"]."', '".$i["comple"]."', '".$i["cidade"]."', '".$i["estado"]."', '".$i["cep"]."', '".$i["fone"]."', '".$i["fax"]."', '".$i["celular"]."', '".$i["email"]."');";
    
                $this->AtualizaProgressBar(null,(100*$j)/$total);
                $j++;
            }
            $this->FechaProgressBar();        
            $con->Query($sql2,false,"Importando Enderecos dos fornecedores");
        }
        
        if(file_exists($path."grupo.dbf") or file_exists($path."GRUPO.DBF")){
            $dbfsimportados.=" Grupos ";
            // grupos de mercadorias
            $myarrayGrpmerc= array(
                              "0" => array("0", "descricao")
                            );
            if(file_exists($path."grupo.dbf")){
                $conversao->opendb($path."grupo.dbf", true, 'grpmerc');
            }else{
                $conversao->opendb($path."GRUPO.DBF", true, 'grpmerc');
            }
            $ok=$conversao->GetColumns( $myarrayGrpmerc, true, false);
            $ok2=$this->tira_acentos($ok);
            $con->Query($ok2,false,"Gravando grupos de merc. em SQL");
        }
        /*
        if(file_exists($path."contas.dbf") or file_exists($path."CONTAS.DBF")){
            $dbfsimportados.=" Plano de Contas ";            
            $myarrayPlacon= array(
                              "0" => array("0", "codigo")
                              "1" => array("1", "descricao")
                            );
            if(file_exists($path."contas.dbf")){
                $conversao->opendb($path."contas.dbf", true, 'placon');
            }else{
                $conversao->opendb($path."CONTAS.DBF", true, 'placon');
            }
            $ok=$conversao->GetColumns( $myarrayPlacon, true, false);
            $ok2=$this->tira_acentos($ok);
            $con->Query($ok2,false,"Gravando plano de contas em SQL");
        }
        */
        if(empty($dbfsimportados)){
            msg("Não foi possível importar nenhum arquivo DBF.");
        }else{
            msg("Cadastros importados: ".$dbfsimportados);
        }
    }
    
    function arrumatelefone($Var){
    		$Var = str_replace("x","",$Var); // remove x
		$Var = str_replace("(0","(",$Var); // remove (0 de (0xx35)...
		//(35)99517396
		//0123456789012
		$Var=substr($Var,0,8)."-".substr($Var,8,4);
		return $Var;
    }
}
?>