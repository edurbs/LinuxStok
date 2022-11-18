<?php
include_once('relatorios'.bar.'relatorios.php');
class entregas_pendentes extends relatorios {

    function entregas_pendentes (){
	
		parent::__construct();
		
		$this->frame_codigo1->show_all();
		$this->labelframe_codigo1->set_text('Cliente');
		$this->entry_codigo1->connect('key_press_event',
        	array($this,'entry_enter'),
            'select codigo, nome from clientes',
            true,
            $this->entry_codigo1, 
            $this->label_codigo1,
            "clientes",
            "nome",
            "codigo"
        );
        $this->entry_codigo1->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'clientes', 
            $this->entry_codigo1, 
            $this->label_codigo1, 
            'codigo', 
            'nome'
        );
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		$this->orderby["s.data"]="Data e Hora";
		$this->orderby["c.nome"]="Cliente";
		$this->orderby["m.descricao"]="Mercadoria";
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);
		
		$this->hbox_checkbutton->show_all();    	
    	$this->checkbutton1->set_label("Cliente");
    	$this->checkbutton1->set_active(TRUE);
    	$this->checkbutton2->set_label("Valor Mercadoria");
    	$this->checkbutton2->set_active(FALSE);
    	$this->checkbutton3->hide();
    	$this->checkbutton4->hide();
    	$this->checkbutton5->hide();
    	$this->checkbutton6->hide();
		
		$this->hbox_button->show_all();
		$this->button_html->connect_simple('clicked',array($this, 'gerar'),'html');
		$this->button_texto->connect_simple('clicked',array($this, 'gerar'),'texto');
		$this->button_tela->connect_simple('clicked',array($this, 'gerar'),'tela');
	}
	
	function gerar($tipo){
		$codcli=$this->pegaNumero($this->entry_codigo1);
        if (!empty($codcli)){
			if(!$this->retornabusca2('clientes', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Cliente ou deixe em branco!');
                return;
            }
		}
		
		$con=$this->conecta();
		
		$sql="SELECT s.data, s.codcli, c.nome, e.codmerc, m.descricao, e.precocomdesconto, e.quantidade, e.entregue, e.quantidade-e.entregue AS pendente FROM entsai AS e  INNER JOIN saidas AS s ON s.codsaidas=e.codentsai INNER JOIN mercadorias AS m  ON m.codmerc=e.codmerc INNER JOIN clientes AS c ON s.codcli=c.codigo WHERE e.entregue<e.quantidade AND e.tipo='S' AND e.quantidade>0 AND entregue>=0 ";
		if (!empty($codcli)){
			$sql.=" AND s.codcli='$codcli' ";
		}
		// pega qual a escolha da combobox de orderby
		$order=$this->combo1->get_active_text();
		// pega o nome do campo da array criada na funcao anterior
		$key=array_search($order, $this->orderby);
		$sql.=" ORDER BY ".$key;
    	if(!$resultado=$con->Query($sql)){
			msg("Erro ao executar comando SQL");
			return;
		}
		$numerolin=$con->NumRows($resultado);
		if($numerolin==0){
			msg("Sua consulta nao retornou nenhum resultado!");
			return;
		}
		$titulo="Relatorio de Vendas por Vendedor";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Data";
		if($this->checkbutton1->get_active()) $cabtabela[1]="Cod.Cli.";
		$cabtabela[2]="Nome Cliente";
		$cabtabela[3]="Cod.Merc";
		$cabtabela[4]="Mercadoria";
		if($this->checkbutton2->get_active()) $cabtabela[5]="Valor";
		$cabtabela[6]="Qt.Vendida";
		$cabtabela[7]="Qt.Entregue";
		$cabtabela[8]="Qt.Pendente";
		
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            if($this->checkbutton1->get_active()) $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$i[2];
            $corpo[$j][3]=$i[3];
            $corpo[$j][4]=$i[4];
            if($this->checkbutton2->get_active()) $corpo[$j][5]=$this->corrigeNumero($i[5],'virgula');
            $corpo[$j][6]=$i[6];
            $corpo[$j][7]=$i[7];
            $corpo[$j][8]=$i[8];

            // soma total
            if($this->checkbutton2->get_active()) $total+=$i[5]*$i[8];
            // controla linha
            $j++;
        }
        
        $pe[0]="";
        if($this->checkbutton2->get_active()) $pe[0]='Total Pendente: R$ '.$this->corrigeNumero($total,'virgula'); 
        

		if($tipo=="html"){
			$this->geraHTML($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="texto"){
			$this->geraTEXTO($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}elseif($tipo=="tela"){
			$this->geraTELA($titulo, $cabeca, $cabtabela, $corpo, $pe, true);
		}
        $this->disconecta($this->con);
		
		msg("Relatorio gerado com sucesso!");
	}
}

?>