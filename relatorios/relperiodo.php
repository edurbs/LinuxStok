<?php

class relperiodo extends funcoes {
    
    function relperiodo($tipo,$titulo,$modelo=null){
        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
        
        $this->xml=$this->carregaGlade('relperiodo',$titulo,false,false);
        
        if($tipo=="produtosfornecedores"){
            $this->frameCodigo = $this->xml->get_widget('frameCodigo');
            $this->frameCodigo->set_label("Codigo do produto");
            $this->entryCodigo= $this->xml->get_widget('entryCodigo');
            $this->labelNome= $this->xml->get_widget('labelNome');
            $this->entryCodigo->connect('key_press_event',
                array($this,'entry_enter'),
                'select codmerc, descricao from mercadorias',
                true,
                $this->entryCodigo, 
                $this->labelNome,
                "mercadorias",
                "descricao",
                "codmerc"
            );
            $this->entryCodigo->connect_simple('focus-out-event',
                array($this,'retornabusca22'), 
                'mercadorias', 
                &$this->entryCodigo, 
                &$this->labelNome, 
                'codmerc', 
                'descricao', 
                'rcomissao'
            );
        }elseif($tipo=="relcaixaperiodo"){
            $this->frameCodigo = $this->xml->get_widget('frameCodigo');
            $this->frameCodigo->set_label("Codigo do caixa");
            $this->entryCodigo= $this->xml->get_widget('entryCodigo');
            $this->labelNome= $this->xml->get_widget('labelNome');
            $this->entryCodigo->connect('key_press_event',
                array($this,'entry_enter'),
                'select * from cadcaixa',
                true,
                $this->entryCodigo, 
                $this->labelNome,
                "cadcaixa",
                "descricao",
                "codigo"
            );
            $this->entryCodigo->connect_simple('focus-out-event',
                array($this,'retornabusca22'), 
                'cadcaixa', 
                $this->entryCodigo, 
                $this->labelNome, 
                'codigo', 
                'descricao'
            );
        }else{
            $this->frameCodigo = $this->xml->get_widget('frameCodigo');
            $this->entryCodigo= $this->xml->get_widget('entryCodigo');
            $this->labelNome= $this->xml->get_widget('labelNome');
            $this->entryCodigo->connect('key_press_event',
                array($this,'entry_enter'),
                'select c.codigo, c.nome, c.contato, c.dtnasc, c.sexo, c.dtcadastro, c.cnpj_cpf, c.ie_rg from funcionarios as c',
                true,
                $this->entryCodigo, 
                $this->labelNome,
                "funcionarios",
                "nome",
                "codigo"
            );
            $this->entryCodigo->connect_simple('focus-out-event',
                array($this,'retornabusca22'), 
                'funcionarios', 
                &$this->entryCodigo, 
                &$this->labelNome, 
                'codigo', 
                'nome', 
                'rcomissao'
            );
        }
        
        
        $this->entryData1= $this->xml->get_widget('entryData1');
        $this->entryData1->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');
        $this->entryData1->set_text($this->datadehoje);
        
        $this->entryData2= $this->xml->get_widget('entryData2');
        $this->entryData2->connect('key-press-event', array(&$this,'mascaraNew'),'**-**-****');
        $this->entryData2->set_text($this->datadehoje);
        
        if($tipo=="vendasperiodo" or $tipo=="vendasperiodomeiopgto"){
            $this->frameCodigo->destroy();
            $this->entryData1->grab_focus();
        }else{
            $this->entryCodigo->grab_focus();
        }

        $this->buttonGerar= $this->xml->get_widget('buttonGerar');
        eval('$this->buttonGerar->connect_simple("clicked", array(&$this,"gerar'.$tipo.'"),$modelo);');
        
        
        
    }
    
    function verificaDataPeriodo($verificavendedor=false){
        if(!$this->valida_data($this->entryData1->get_text())){
            msg('Data Inicial Invalida');
            return false;
        }
        $this->data1=$this->corrigeNumero($this->entryData1->get_text(),"dataiso");
        
        if(!$this->valida_data($this->entryData2->get_text())){
            msg('Data Final Invalida');
            return false;
        }
        $this->data2=$this->corrigeNumero($this->entryData2->get_text(),"dataiso");
        
        if($verificavendedor){
            $this->codvendedor=$this->entryCodigo->get_text();
            if (empty($this->codvendedor) or !$this->retornabusca2('funcionarios', $this->entryCodigo, $this->labelNome, 'codigo', 'nome', 'relperiodo')){
                msg('Preencha corretamente o campo Vendedor!');
                return false;
            }            
        }
        return true;
    }
    
    function gerarrel_orcamento_abertos($modelo=null){
        if(!$this->verificaDataPeriodo(false)){
            return;
        }
        $this->codvendedor=$this->entryCodigo->get_text();
        if (!empty($this->codvendedor)){
        		if(!$this->retornabusca2('funcionarios', $this->entryCodigo, $this->labelNome, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Vendedor ou deixe-o em branco!');
                return false;
            }
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();		
        if($modelo=="abertos"){
        		$titulo="Relatorio de Orcamentos Abertos";
        		$sql="SELECT o.data, o.datafinalizado, c.nome, o.desconto, o.totalmerc, o.vendedor FROM orcamento AS o INNER JOIN clientes AS c ON (c.codigo=o.codcli) WHERE o.data>='$this->data1' AND o.data<='$this->data2'  AND finalizado='N'";
        	}elseif($modelo=="fechados"){
			$titulo="Relatorio de Orcamentos Fechados";
        		$sql="SELECT o.data, o.datafinalizado,c.nome, o.desconto, o.totalmerc, o.vendedor FROM orcamento AS o INNER JOIN clientes AS c ON (c.codigo=o.codcli) WHERE o.datafinalizado>='$this->data1' AND o.datafinalizado<='$this->data2' AND finalizado='S'";
        	}elseif($modelo=="todos"){
        		$titulo="Relatorio de Todos Orcamentos";
        		$sql="SELECT o.data,o.datafinalizado,c.nome,o.desconto,o.totalmerc,o.vendedor FROM orcamento AS o INNER JOIN clientes AS c ON (c.codigo=o.codcli) WHERE o.data>='$this->data1' AND o.data<='$this->data2'";        		
        	}
        	if (!empty($this->codvendedor)){
        		$sql.=" AND o.vendedor='$this->codvendedor' ";
        	}
        	$sql.=" ORDER BY c.nome, o.data DESC";

        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
        $cabtabela[0]="Dt.Aberto";
        $cabtabela[1]="Dt.Fechado";
        $cabtabela[2]="Cliente";
        $cabtabela[3]="Descontos";
        $cabtabela[4]="Total";
        $cabtabela[5]="Vendedor";
        $total=0;
        $totaldesconto=0;
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            $corpo[$j][1]=$this->corrigeNumero($i[1],'data');
            $corpo[$j][2]=$i[2];
            $corpo[$j][3]=$this->mascara2($i[3],'moeda');
            $corpo[$j][4]=$this->mascara2($i[4],'moeda');
            $corpo[$j][5]=$i[5];
            $total+=$i[4];
            $totaldesconto+=$i[3];

            // controla linha
            $j++;
        }
        
        $pe[0]="Total de Descontos: ".$this->mascara2($totaldesconto,'moeda')." Total de Orcamentos: ".$this->mascara2($total,'moeda');

        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
        return;
    }
    
    function gerarrelcaixaperiodo(){
        // relatorio de caixa por periodo e codigo de plano de contas
        if(!$this->verificaDataPeriodo()){
            return;
        }
        $codcadcaixa=$this->pegaNumero($this->entryCodigo);
        //$descricao_caixa=$this->labelNome->get_text();
        if (empty($codcadcaixa) or !$descricao_caixa=$this->retornabusca4('descricao','cadcaixa','codigo',$codcadcaixa)){
            msg('Preencha corretamente o Codigo do Caixa!');
            return;
        }       
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
		//INNER JOIN placon AS p ON (c.origem=p.codigo)        
        $sql="SELECT c.data, c.hora, c.formamovim, c.valor, c.saldo, c.origem, c.historico, c.obs FROM caixa AS c WHERE c.data>='$this->data1' AND c.data<='$this->data2' AND c.origem=$codcadcaixa";
        
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        
        $titulo="Relatorio do Caixa ".$codcadcaixa." - ".$descricao_caixa." por Periodo/Plano de Contas";
        $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
        $cabtabela[0]="Data";
        $cabtabela[1]="Hora";
        $cabtabela[2]="Forma";
        $cabtabela[3]="Ent/Sai";
        $cabtabela[4]="Saldo";
        $cabtabela[5]="Cod.Pla.Con";
        $cabtabela[6]="Obs.";
        $j=0;
        while($i = $con->FetchRow($resultado)){
        		$corpo[$j][0]=$this->corrigeNumero($i[0],'data');
        		$corpo[$j][1]=$i[1];
        		$forma=$i[2];
        		$corpo[$j][2]=$forma;
        		$valor=$i[3];
        		//if($forma=="S") $valor=$valor*(-1);
        		$corpo[$j][3]=$this->corrigeNumero($valor,'moeda');       		
        		$corpo[$j][4]=$this->corrigeNumero($i[4],'moeda');
        		$corpo[$j][5]=$i[5];
        		$corpo[$j][6]=$i[7];
        		$j++;
        }
        
        /*
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            
            // busca o valor de entrada
            $sqlE="SELECT sum(c.valor) FROM caixa AS c WHERE c.formamovim='E' AND c.data='$i[0]' AND c.origem='$i[1]'";
            $resultadoE=$con->Query($sqlE);
            if($con->NumRows($resultadoE)>0){
                $i1=$con->FetchRow($resultadoE);
                $corpo[$j][1]=$this->mascara2($i1[0],'moeda');
                $entradaperiodo+=$i1[0];
            }else{
                $corpo[$j][1]=$this->mascara2(0,'moeda');
            }
            
            // busca o valor de saida
            $sqlS="SELECT sum(c.valor) FROM caixa AS c WHERE c.formamovim='S' AND c.data='$i[0]' AND c.origem='$i[1]'";
            $resultadoS=$con->Query($sqlS);
            if($con->NumRows($resultadoS)>0){
                $i2=$con->FetchRow($resultadoS);
                $corpo[$j][2]=$this->mascara2($i2[0],'moeda');
                $saidaperiodo+=$i2[0];
            }else{
                $corpo[$j][2]=$this->mascara2(0,'moeda');
            }
            // faturamento
            $corpo[$j][3]=$this->mascara2($i1[0]-$i2[0],'moeda');
            $corpo[$j][4]=$i[1];
            $corpo[$j][5]=$i[2];
            // controla linha
            $j++;
        }
        $faturaperiodo=$entradaperiodo-$saidaperiodo;
        */
        $pe[0]="";
        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        
        $con->Disconnect();
        return;
    }
    function gerarvendasperiodo(){
        // Relatorio de vendas por per�do
        if(!$this->verificaDataPeriodo()){
            return;
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        
        $sql="SELECT s.data,s.hora,s.codcli,c.nome,s.desconto,s.totalmerc,s.vendedor FROM saidas AS s INNER JOIN clientes AS c ON (c.codigo=s.codcli) WHERE s.data>='$this->data1' AND s.data<='$this->data2'";

        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        
        $titulo="Relatorio de Vendas por Periodo";
        $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
        $cabtabela[0]="Data";
        $cabtabela[1]="Hora";
        $cabtabela[2]="Cod.Cli.";
        $cabtabela[3]="Cliente";
        $cabtabela[4]="Descontos";
        $cabtabela[5]="Total da Venda";
        $cabtabela[6]="Vendedor";
        $total=0;
        $totaldesconto=0;
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->mascara2($i[0],'data');
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$i[2];
            $corpo[$j][3]=$i[3];
            $corpo[$j][4]=$this->mascara2($i[4],'moeda');
            $corpo[$j][5]=$this->mascara2($i[5],'moeda');
            $corpo[$j][6]=$i[6];
            $total+=$i[5];
            $totaldesconto+=$i[4];

            // controla linha
            $j++;
        }
        
        $pe[0]="Total de Descontos: ".$this->mascara2($totaldesconto,'moeda')." Total das Vendas: ".$this->mascara2($total,'moeda');

        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
        return;
    }
    
    function gerarvendasperiodomeiopgto(){
        // Relatorio de vendas por per�do e meio de pagamento
        if(!$this->verificaDataPeriodo()){
            return;
        }
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        
        //$sql="select m.data,sum(m.valor),m.codmeiopgto,mm.descricao FROM movpagamentos AS m INNER JOIN meiopgto AS mm ON (mm.codigo=m.codmeiopgto) WHERE m.data>='$this->data1' AND m.data<='$this->data2' GROUP BY m.codmeiopgto";
        $sql="SELECT m.data_c,sum(m.valor),m.codmeiopgto,mm.descricao FROM movpagamentos AS m INNER JOIN meiopgto AS mm ON (mm.codigo=m.codmeiopgto) WHERE m.data_c>='$this->data1' AND m.data_c<='$this->data2' AND m.tipo='S' GROUP BY m.codmeiopgto, mm.descricao, m.data_c ORDER BY m.data_c";
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        
        $titulo="Relatorio de Vendas por Periodo/Meio de Pagamento";
        $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
        $cabtabela[0]="Data";
        $cabtabela[1]="Valor";
        $cabtabela[2]="Meio de Pgto.";
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            $corpo[$j][1]=$this->mascara2($i[1],'moeda');
            $corpo[$j][2]=$i[3];

            // controla linha
            $j++;
        }
        
        $pe[0]="";

        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
        return;
    }
    
    function gerarvendasperiodovendedor(){
        // Relatorio de vendas por periodo/vendedor
        if(!$this->verificaDataPeriodo(true)){
            return;
        }
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        
        $sql="SELECT s.data,s.codcli,c.nome,s.desconto,s.totalmerc,s.hora FROM saidas AS s INNER JOIN clientes AS c ON (c.codigo=s.codcli) WHERE s.data>='$this->data1' AND s.data<='$this->data2' AND s.vendedor='$this->codvendedor'";
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        
        $titulo="Relatorio de Vendas por Periodo/Vendedor: ".$this->codvendedor;
        $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
        $cabtabela[0]="Data";
        $cabtabela[1]="Hora";
        $cabtabela[2]="Cod.Cli.";
        $cabtabela[3]="Cliente";
        $cabtabela[4]="Descontos";
        $cabtabela[5]="Total da Venda";
        $total=0;
        $totaldesconto=0;
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            $corpo[$j][1]=$i[5];
            $corpo[$j][2]=$i[1];
            $corpo[$j][3]=$i[2];
            $corpo[$j][4]=$this->corrigeNumero($i[3],'moeda');
            $corpo[$j][5]=$this->corrigeNumero($i[4],'moeda');
            $total+=$i[4];
            $totaldesconto+=$i[3];

            // controla linha
            $j++;
        }
        
        $pe[0]="Total de Descontos: ".$this->corrigeNumero($totaldesconto,'moeda')." Total das Vendas: ".$this->corrigeNumero($total,'moeda');

        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
        return;
    }
    
    function gerarrel_por_periodo_vendedor_do_cliente(){
        // Relatorio de vendas por periodo/vendedor do cliente
        if(!$this->verificaDataPeriodo(true)){
            return;
        }
        
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=new $BancoDeDados;
        $con->Connect();
        
        $sql="SELECT s.data,s.codcli,c.nome,s.desconto,s.totalmerc,s.hora,s.vendedor FROM saidas AS s INNER JOIN clientes AS c ON (c.codigo=s.codcli) WHERE s.data>='$this->data1' AND s.data<='$this->data2' AND c.codvendedor='$this->codvendedor'";
        
        if(!$resultado=$con->Query($sql)){
            msg("Erro consultando o banco de dados!");
            return;
        }
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        
        $titulo="Relatorio de Vendas por Periodo/Vendedor: ".$this->codvendedor;
        $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
        $cabtabela[0]="Data";
        $cabtabela[1]="Hora";
        $cabtabela[2]="Cod.Cli.";
        $cabtabela[3]="Cliente";
        $cabtabela[4]="Descontos";
        $cabtabela[5]="Total da Venda";
        $total_geral=0;
        $total_desconto=0;
        $j=0;
        
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            $corpo[$j][1]=$i[5];
            $corpo[$j][2]=$i[1];
            $corpo[$j][3]=$i[2];
            $corpo[$j][4]=$this->corrigeNumero($i[3],'moeda');
            $corpo[$j][5]=$this->corrigeNumero($i[4],'moeda');
            $total_geral+=$i[4];
            $total_vendedores[$i[6]]+=$i[4];
            $total_desconto+=$i[3];            

            // controla linha
            $j++;
        }
        
        $t=0;
        foreach($total_vendedores as $key=>$tmp){
        		$pe[$t]="Total de Vendas Feitas pelo Vendedor ".$key.": ".$this->corrigeNumero($tmp,'moeda');
        		$t++;
        }    
        $t++;    
		$pe[$t]="Total de Descontos: ".$this->corrigeNumero($total_desconto,'moeda');
		$t++;
		$pe[$t]="Total das Vendas: ".$this->corrigeNumero($total_geral,'moeda');

        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
        return;
    }
    
    
    function gerarprodutosfornecedores(){
        if(!$this->verificaDataPeriodo(false)){
            return;
        }
        
        $this->codvendedor=$this->pegaNumero($this->entryCodigo);
        /*if (empty($this->codvendedor) or !$this->retornabusca2('mercadorias', &$this->entryCodigo, &$this->labelNome, 'codmerc', 'descricao', 'relperiodo')){
            msg('Preencha corretamente o Mercadoria!');
            return;
        } */       
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $sql="SELECT e.codmerc,m.descricao,e.precooriginal,e.quantidade,t.codfor,f.nome,t.data FROM entsai AS e INNER JOIN entradas AS t ON (e.codentsai=t.codentradas) INNER JOIN fornecedores AS f ON (f.codigo=t.codfor) INNER JOIN mercadorias AS m ON (e.codmerc=m.codmerc) WHERE e.tipo='E' ";
        if(!empty($this->codvendedor)) $sql.=" AND e.codmerc='$this->codvendedor' ";
        $sql.=" AND t.data>='$this->data1' AND t.data<='$this->data2' ORDER BY t.data";
        $resultado=$con->query($sql);
        
        if($con->NumRows($resultado)==0){
            msg('Esta data nao possui movimentacao!');
            return;
        }
        $this->janela->hide();
        
        $titulo="Relatorio de Compras de Produtos(".$this->codvendedor.") por Fornecedores";
        $cabeca[0]="";
        $cabtabela[0]="Cod.Merc";
        $cabtabela[1]="Descricao Merc.";
        $cabtabela[2]="Preco Unit.";
        $cabtabela[3]="Preco Total";
        $cabtabela[4]="Quantidade";
        $cabtabela[5]="Cod.For.";
        $cabtabela[6]="Nome Fornecedor";
        $cabtabela[7]="Data";
        
        $j=0;
        while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$this->corrigeNumero($i[2]/$i[3],'virgula');
            $corpo[$j][3]=$this->corrigeNumero($i[2],'virgula');
            $corpo[$j][4]=$i[3];
            $corpo[$j][5]=$i[4];
            $corpo[$j][6]=$i[5];
            $corpo[$j][7]=$this->corrigeNumero($i[6],'data');;
            
            // controla linha
            $j++;
        }
        
        $pe[0]="";

        $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
        $con->Disconnect();
        return;
    }
}
?>
