<?

class relorcamento extends funcoes {
    
    function relorcamento(){
        
            
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
    function gerar(){
        //if($tipo=="comum"){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();        
            
            //$codvendedor=$this->entryCodigo->get_text();
            
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
            $sql="select c.nome,o.totalnf,o.vendedor,o.data from orcamento as o left join clientes as c on (c.codigo=o.codcli) where finalizado='N' and o.data>='$data1' and o.data<='$data2' order by o.data";
            if(!$resultado=$con->Query($sql)){
                msg("Erro consultando o banco de dados!");
                return;
            }
            if($con->NumRows($resultado)==0){
                msg('Nao ha movimentacao no periodo!');
                return;
            }
            $this->janela->hide();
            
            // testes
            $titulo='Relatorio de Orçamento Nao Concretizados';           
            
            $cabeca[0]="Periodo: ".$this->entryData1->get_text()." a ".$this->entryData2->get_text();
            $cabtabela[0]="Cliente";
            $cabtabela[1]="\$ Total";
            $cabtabela[2]="Vendedor";
            $cabtabela[3]="Data";
            $j=0;
            while($i = $con->FetchRow($resultado)) {                    
                $corpo[$j][0]=$i[0];
                $corpo[$j][1]=$this->mascara2($i[1],'moeda');
                $corpo[$j][2]=$i[2];
                $corpo[$j][3]=$this->corrigeNumero($i[3],"data");
                $j++;
            }
            $pe[0]="";
            $this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
            
            $con->Disconnect();
            
            return;
    }
}
?>