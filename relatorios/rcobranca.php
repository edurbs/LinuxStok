<?php
class rcobranca extends funcoes {

    function rcobranca($tipo){
        if($tipo=='geral'){
            $this->janela= new GtkWindow;
            $this->janela->set_position(1);
            $vbox= &new GtkVBox;
            $hbox= &new GtkHBox;
            $hbox2= &new GtkHBox;
            $frame1= &new GtkFrame('Codigo do Cliente');
            $this->entry1= &new GtkEntry();
            $this->entry1->set_usize(50,25);
            $label1= &new GtkLabel(' << Pressione ENTER para procurar');
            $label1->set_usize(300,25);
            $this->entry1->connect_object('key_press_event', 
                array(&$this,entry_enter), 
                'select c.codigo, c.nome, c.contato, c.dtnasc, c.sexo, c.dtcadastro, c.cnpj_cpf, c.ie_rg from clientes as c',
                true,
                $this->entry1, 
                $label1,
                "clientes",
                "nome",
                "codigo"
            );
            $button1=&new GtkButton('OK');
            $button1->connect_object('clicked',array(&$this,'gerar'),$tipo);
            $hbox->pack_start($this->entry1);
            $hbox->pack_start($label1);                
            $frame1->add($hbox);
            $vbox->pack_start($frame1);
            $hbox2->pack_start($button1);
            $vbox->pack_start($hbox2);
            $this->janela->add($vbox);
            //$this->janela->show_all();
            $this->janela->set_focus($this->entry1);
            $this->janela->connect_object('delete-event', array(&$this,'fecha_janela'));
        }
        
    }
    function gerar($tipo){
        global $bar;
        if($tipo=="geral"){
            $BancoDeDados=retorna_CONFIG("BancoDeDados");
            $con=&new $BancoDeDados;
            $con->Connect();        
            $codcliente=$this->entry1->get_text();
            if(empty($codcliente)){
                msg('Digite o codigo do cliente!');
                return;
            }            
            $sql='select c.nome,c.codigo,d.nome,d.codigo,m.tipomov,m.dtmov,m.valor,m.dtvencimento,mp.descricao,t.nfdoc,t.situacao,t.saldo,m.obs from movcobranca as m INNER JOIN tituloscobranca AS t ON (t.codigo=m.codtitulo) INNER JOIN devedores AS d ON (d.codigo=t.coddevedor) INNER JOIN clientes AS c ON (c.codigo=t.codcliente) INNER JOIN meiopgto AS mp ON (mp.codigo=t.meiopgto) WHERE c.codigo='.$codcliente.' ORDER BY c.nome,d.nome';
            $con->Query($sql);
            if(!$resultado=$con->Query($sql)){
                msg("Erro consultando o banco de dados!");
                return;
            }
            if($con->NumRows($resultado)==0){
                msg('Este cliente nao possui movimentacao de cobranca de seus devedores!');
                return;
            }
            $this->janela->hide();
            
            
            $html.=$this->relHEAD('Relatorio de Movimenta�ao de Cobran�a');
            $tabela=false;
            while($i = $con->FetchRow($resultado)) {
                if($i[1]<>$ultimoCliente){
                    $html.= "<h2>Cliente: $i[0] </h2>";
                    $ultimoCliente=$i[1];
                }
                if($i[3]<>$ultimoDevedor){
                    if($tabela){$html.="</table><br><hr>";};
                    $html.="<h3>Devedor: $i[2] </h3>";
                    $ultimoDevedor=$i[3];
                    $html.="<h4>Tipo de Divida: $i[8], Documento $i[9], Situacao $i[10], Saldo $i[11] </h4> ";
                    $html.="<table class='tbl'>";
                    $tabela=true;
                    $html.="<tr class='hrw'>";
                    $html.='<td class="shr"> Movimentacao</td> <td class="shr"> Data </td> <td class="shr">Valor</td><td class="shr">Vencimento</td><td class="shr">Observacao</td></tr>';
                    
                }
                $html.="<tr class='crw'><td class='shr'> $i[4]</td><td class='shr'> $i[5]</td><td class='shr'> $i[6]</td><td class='shr'> $i[7]</td><td class='shr'> $i[12]</td></tr>";
            }
            $html.="</table>";
        $this->chamaBrowser($html);        
    }
}
}
?>