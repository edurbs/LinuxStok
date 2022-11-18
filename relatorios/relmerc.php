<?php

class relmerc extends funcoes {
    
    function relmerc($tipo=null,$titulo=null){
        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

    		if(!$this->$tipo()){
    			//msg("Relatorio enviado ao navegador HTML");
    			return;
    		}
        
    }
    function lista_preco_simples(){
    		$sql="SELECT codmerc, descricao, precovenda, obs FROM mercadorias ORDER BY descricao ";
    		        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return false;
        }
        	$rows=$con->NumRows($resultado);
        if($rows==0){
            msg('Sem mercadorias!');
            return false;
        }
        
        
        $titulo="Relatorio Simples de Precos de Mercadorias";
        $cabeca[0]="";
        $cabtabela[0]="Cod.";
        $cabtabela[1]="Descricao";
        $cabtabela[2]="Preco";
        $cabtabela[3]="Obs";
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
			$corpo[$j][2]=$i[2];
			$corpo[$j][3]=$i[3];
			$j++;
		}
        $pe[0]="Total de Mercadorias listadas: ".$rows;
        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);

    		return false;
    }
    
    function rel_abaixo_do_estoque_minimo1(){
    		$sql="SELECT codmerc, descricao, estoqueatual, estoqueminimo, precocusto, customedio, precovenda FROM mercadorias WHERE estoqueatual<=estoqueminimo ORDER BY descricao ";
    		$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return false;
        }
        	$rows=$con->NumRows($resultado);
        if($rows==0){
            msg('Sem mercadorias!');
            return false;
        }
        
        
        $titulo="Relatorio de Mercadorias Abaixo do Estoque Minimo";
        $cabeca[0]="";
        $cabtabela[0]="Cod.";
        $cabtabela[1]="Descricao";
        $cabtabela[2]="Estoque";
        $cabtabela[3]="Minimo";
		$cabtabela[4]="Custo";
		$cabtabela[5]="Medio";
		$cabtabela[6]="Venda";
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
			$corpo[$j][2]=$i[2];
			$corpo[$j][3]=$i[3];
			$corpo[$j][4]=$i[4];
			$corpo[$j][5]=$i[5];
			$corpo[$j][6]=$i[6];
			$j++;
		}
        $pe[0]="Total de Mercadorias listadas: ".$rows;
        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);

    		return false;
    
    }
    
    function rel_com_preco_promocional1(){
    		$hoje=$this->corrigeNumero($this->datadehoje,"dataiso");
    		$sql="SELECT codmerc, descricao, estoqueatual, precovenda, promopreco, promoinicio, promofim FROM mercadorias WHERE '$hoje'>=promoinicio AND '$hoje'<=promofim ORDER BY descricao ";
    		$BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return false;
        }
        	$rows=$con->NumRows($resultado);
        if($rows==0){
            msg('Sem mercadorias!');
            return false;
        }
        
        
        $titulo="Relatorio de Mercadorias com Proco Promocional";
        $cabeca[0]="";
        $cabtabela[0]="Cod.";
        $cabtabela[1]="Descricao";
        $cabtabela[2]="Estoque";
        $cabtabela[3]="Preco";
		$cabtabela[4]="Promocao";
		$cabtabela[5]="Inicio";
		$cabtabela[6]="Fim";
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
			$corpo[$j][2]=$i[2];
			$corpo[$j][3]=$i[3];
			$corpo[$j][4]=$i[4];
			$corpo[$j][5]=$this->corrigeNumero($i[5],'data');
			$corpo[$j][6]=$this->corrigeNumero($i[6],'data');
			$j++;
		}
        $pe[0]="Total de Mercadorias listadas: ".$rows;
        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);

    		return false;    
    
    }
    
}
?>
