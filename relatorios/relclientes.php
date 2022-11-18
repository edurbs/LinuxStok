<?php

class relgeral extends funcoes {
    
    function relgeral($tipo,$tabela){
    		call_user_func(array($this,$tipo),$tabela));
	}
	function simplescomendereco($tabela){
	    $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        $sql="SELECT c.nome, e.endereco, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep, e.telefone, e.fax, e.celular FROM $tabela AS c INNER JOIN cadastro2enderecos AS e ON (e.cadastro='$tabela' AND e.codigo=c.codigo) ORDER BY c.nome";
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Sem dados!');
            return;
        }
        $titulo="Relatorio de $tabela simples com endereco";
        $cabeca[0]="";
        $cabtabela[0]="Nome";
		$cabtabela[1]="Endereco";
		$cabtabela[2]="Cidade";
		$cabtabela[3]="Estado";
		$cabtabela[4]="CEP";
		$cabtabela[5]="Telefones";
        $j=0;
        while($i = $con->FetchRow($resultado)){
        		$corpo[$j][0]=$i[0]; // nome
        		$corpo[$j][1]=$i[1]." ".$i[2]." ".$i[3]." ".$i[4]; // endereco
        		$corpo[$j][2]=$i[5]; // cidade
        		$corpo[$j][3]=$i[6]; // estado
        		$corpo[$j][4]=$i[7]; // cep
        		$corpo[$j][5]=$i[8]." ".$i[9]." ".$i[10]; // telefones
        		$j++;
        	}
        	$pe[0]=" $j $tabela listados";
        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        
        $con->Disconnect();
        return;
	}
}
?>
