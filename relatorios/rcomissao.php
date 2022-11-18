<?

class rcomissao extends funcoes {
    
    function rcomissao($tipo){
        if($tipo=='comum'){
            
            $this->diadehoje=date('d',time());
            $this->mesdehoje=date('m',time());
            $this->anodehoje=date('Y',time());
            $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;
            
            $this->xml = $this->carregaGlade('relperiodo');
            $this->janela = $this->xml->get_widget('window1');            
            $this->janela->connect_object('delete-event', array(&$this,'fecha_janela'));
            $this->janela->set_title('Relatorio de Comissao');            
            
            $this->frameCodigo = $this->xml->get_widget('frameCodigo');
            $this->frameCodigo->set_label('Busca Codigo do Vendedor');
            
            $this->entryCodigo= $this->xml->get_widget('entryCodigo');
            $this->labelNome= $this->xml->get_widget('labelNome');
            $this->entryCodigo->connect_object('key_press_event', 
                array(&$this,entry_enter),
                'select c.codigo, c.nome, c.contato, c.dtnasc, c.sexo, c.dtcadastro, c.cnpj_cpf, c.ie_rg from funcionarios as c where habvender="Liberado" ',
                true,
                $this->entryCodigo, 
                $this->labelNome,
                "funcionarios",
                "nome",
                "codigo"
            );
            $this->entryCodigo->connect_object('focus-out-event',
                array(&$this,retornabusca2), 
                'funcionarios', 
                &$this->entryCodigo, 
                &$this->labelNome, 
                'codigo', 
                'nome', 
                'rcomissao'
            );
            
            $this->entryData1= $this->xml->get_widget('entryData1');            
            $this->entryData1->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
            $this->entryData1->set_text($this->datadehoje);
            
            $this->entryData2= $this->xml->get_widget('entryData2');
            $this->entryData2->connect('key-press-event', array(&$this,'mascara'),'data',null,null,null);
            $this->entryData2->set_text($this->datadehoje);
            
            $this->buttonGerar= $this->xml->get_widget('buttonGerar');
            $this->buttonGerar->connect_object('clicked', array(&$this,'gerar'),"comum");
            
            $this->janela->set_focus($this->entryCodigo);
        }
        
    }
    function gerar($tipo){
        if($tipo=="comum"){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();        
            
            $codvendedor=$this->entryCodigo->get_text();            
            if (empty($codvendedor) or !$this->retornabusca2(null,'funcionarios', &$this->entryCodigo, &$this->labelNome, 'codigo', 'nome', 'rcomissao')){
                msg('Preencha corretamente o campo Vendedor!');
                return;
            }            
            
            if(!$this->valida_data($this->entryData1->get_text())){
                msg('Data Inicial Invalida');
                return;
            }
            $data1=$this->corrigeNumero($this->entryData1->get_text(),"dataiso");
            
            if(!$this->valida_data($this->entryData2->get_text())){
                msg('Data Final Invalida');
                return;
            }
            $data2=$this->corrigeNumero($this->entryData2->get_text(),"dataiso");
            
            
            //$sql="select m.data_c,r.comissao,m.valor from receber as r,movimentos as m where r.vendedor='$codvendedor' and m.codmovim=r.codigo";
            $sql="select m.data_c,r.comissao,m.valor,c.nome,r.data_c from clientes as c, receber as r,movimentos as m where r.vendedor='$codvendedor' and m.codmovim=r.codigo and c.codigo=r.codorigem AND m.data_c>='$data1' AND m.data_c<='$data2' order by m.data_c";
            //$con->Query($sql);
            if(!$resultado=$con->Query($sql)){
                msg("Erro consultando o banco de dados!");
                return;
            }
            if($con->NumRows($resultado)==0){
                msg('Este vendedor nao possui movimentacao!');
                return;
            }
            $this->janela->hide();
            
            // testes
            $titulo='Relatorio de Comissao - Vendedor:'.$codvendedor;
            $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
            $cabtabela[0]="Cliente";
            $cabtabela[1]="Data Venda";
            $cabtabela[2]="Data Pgto";
            $cabtabela[3]="%Com.";
            $cabtabela[4]="Valor Pago";
            $cabtabela[5]="Com. R\$"; 
            $j=0;
            while($i = $con->FetchRow($resultado)) {        
                $comissaoParcial=($i[2]/100)*$i[1];
                $corpo[$j][0]=$i[3];
                $corpo[$j][1]=$i[4];
                $corpo[$j][2]=$i[0];
                $corpo[$j][3]=$i[1]."%";
                $corpo[$j][4]=$this->mascara2($i[2],'moeda');
                $corpo[$j][5]=$this->mascara2($comissaoParcial,'moeda');
                // soma a comissao
                $comissaoTotal+=$comissaoParcial;
                $j++;
            }
            $pe[0]="Total de Comissao a Receber: ".$this->mascara2($comissaoTotal,'moeda');
            $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
            
            $con->Disconnect();
            
            return;
            /*
            $html.=$this->relHEAD('Relatorio de Comissao - Vendedor:'.$codvendedor);
            
            $html.="<center><b>Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text()." </b></center><br>";
            $html.="<table class='tbl'>";
            $html.="<tr class='hrw'><td class='shr'> Cliente </td><td class='shr'> Data Venda </td><td class='shr'> Data Pgto </td><td class='shr'> %Com. </td><td class='shr'> Valor Pago </td><td class='shr'> Com. R\$ </td></tr>";
            while($i = $con->FetchRow($resultado)) {        
                $comissaoParcial=($i[2]/100)*$i[1];
                $html.="<tr class='crw'><td class='shr'> $i[3] </td><td class='shr'> $i[4] </td><td class='shr'> $i[0]</td><td class='shr'> $i[1]%</td><td class='shr'> ". $this->mascara2($i[2],'moeda')." </td><td class='shr'> ".$this->mascara2($comissaoParcial,'moeda')."</td></tr>";
                // soma a comissao
                $comissaoTotal+=$comissaoParcial;
            }            
            $html.="</table>";
            $html.="<br><center><b>Total de Comissao a Receber: ".$this->mascara2($comissaoTotal,'moeda')."</b></center>";            
            */
            //$this->chamaBrowser($html);            
        }
    }
}
?>
