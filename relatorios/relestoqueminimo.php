<?

class relEstoqueMinimo extends funcoes {

    function relEstoqueMinimo(){
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;        
        $con->Connect();
        $sql="select m.descricao, m.unidade, f.nome, m.precocusto, m.estoqueatual, m.estoqueminimo, m.estoqueruim from mercadorias as m left join fornecedores as f on (m.codfor=f.codigo) where m.estoqueatual<m.estoqueminimo order by f.nome";
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            $con->Disconnect();
            return;
        }
        $titulo='Relatorio de Estoque Baixo';
        $cabeca[0]='';
        $cabtabela[0]="Descricao";
        $cabtabela[1]="UN";
        $cabtabela[2]="Fornecedor";
        $cabtabela[3]="\$ Custo";
        $cabtabela[4]="E.Atual";
        $cabtabela[5]="E.Min.";
        $cabtabela[6]="E.Ruim";
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$i[2];
            $corpo[$j][3]=$this->mascara2($i[3],'moeda');
            $corpo[$j][4]=$i[4];
            $corpo[$j][5]=$i[5];
            $corpo[$j][6]=$i[6];
            $j++;
        }
        $pe[0]='';
        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
    }
}    
?>