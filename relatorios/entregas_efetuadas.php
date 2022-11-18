<?php
include_once('relatorios'.bar.'relatorios.php');
class entregas_efetuadas extends relatorios {

    function entregas_efetuadas (){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		
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
        
        $this->frame_codigo2->show_all();
		$this->labelframe_codigo2->set_text('Funcionario-Vendedor');
		$this->entry_codigo2->connect('key_press_event',
        	array($this,'entry_enter'),
            'select codigo, nome from funcionarios',
            true,
            $this->entry_codigo2, 
            $this->label_codigo2,
            "funcionarios",
            "nome",
            "codigo"
        );
        $this->entry_codigo2->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'funcionarios', 
            $this->entry_codigo2, 
            $this->label_codigo2, 
            'codigo', 
            'nome'
        );
        
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		$this->orderby["s.data, s.hora"]="Data e Hora";
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
    	$this->checkbutton3->set_label("Hora");
    	$this->checkbutton4->set_label("Vendedor");
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
		
		$vendedor=$this->pegaNumero($this->entry_codigo2);
        if (!empty($vendedor)){
			if(!$this->retornabusca2('funcionarios', $this->entry_codigo2, $this->label_codigo2, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Funcionarios ou deixe em branco!');
                return;
            }
		}
		
		if(!$this->valida_data($this->entry_data1->get_text())){
            msg('Data Inicial Invalida');
            return false;
        }
        $this->data1=$this->corrigeNumero($this->entry_data1->get_text(),"dataiso");
        
        if(!$this->valida_data($this->entry_data2->get_text())){
            msg('Data Final Invalida');
            return false;
        }
        $this->data2=$this->corrigeNumero($this->entry_data2->get_text(),"dataiso");
		
		$con=$this->conecta();
		
		$sql="SELECT s.data, s.hora, s.codcli, c.nome, e.codmerc, m.descricao, e.precocomdesconto, e.quantidade, s.vendedor, f.nome FROM entrega_itens AS e  INNER JOIN entregas AS s ON s.codentregas=e.codentregas INNER JOIN mercadorias AS m  ON m.codmerc=e.codmerc INNER JOIN clientes AS c ON s.codcli=c.codigo INNER JOIN funcionarios AS f ON s.vendedor=f.codigo WHERE e.quantidade>0 AND s.data>='$this->data1' AND s.data<='$this->data2' ";
		if (!empty($codcli)){
			$sql.=" AND s.codcli='$codcli' ";
		}
		if (!empty($vendedor)){
			$sql.=" AND s.vendedor='$vendedor' ";
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
		$titulo="Relatorio de Entregas Efetuadas";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Data";
		if($this->checkbutton3->get_active()) $cabtabela[1]="Hora";
		if($this->checkbutton1->get_active()) $cabtabela[2]="Cod.Cli.";
		$cabtabela[3]="Nome Cliente";
		$cabtabela[4]="Cod.Merc";
		$cabtabela[5]="Mercadoria";
		if($this->checkbutton2->get_active()) $cabtabela[6]="Valor";
		$cabtabela[7]="Quant.";
		if($this->checkbutton4->get_active()) $cabtabela[8]="Cod.Vendedor";
		if($this->checkbutton4->get_active()) $cabtabela[9]="Nome Vendedor";
		
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$this->corrigeNumero($i[0],'data');
            if($this->checkbutton3->get_active()) $corpo[$j][1]=$i[1];
            if($this->checkbutton1->get_active()) $corpo[$j][2]=$i[2];
            $corpo[$j][3]=$i[3];
            $corpo[$j][4]=$i[4];
            $corpo[$j][5]=$i[5];
            if($this->checkbutton2->get_active()) $corpo[$j][6]=$this->corrigeNumero($i[6],'virgula');
            $corpo[$j][7]=$i[7];
            if($this->checkbutton4->get_active()) $corpo[$j][8]=$i[8];
            if($this->checkbutton4->get_active()) $corpo[$j][9]=$i[9];

            // soma total
            if($this->checkbutton2->get_active()) $total+=$i[6]*$i[7];
            // controla linha
            $j++;
        }
        
        $pe[0]="";
        if($this->checkbutton2->get_active()) $pe[0]='Total Entregue: R$ '.$this->corrigeNumero($total,'virgula'); 
        

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