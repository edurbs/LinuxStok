<?php
include_once('relatorios'.bar.'relatorios.php');
class devolucoestotaisdocliente extends relatorios {

    function devolucoestotaisdocliente(){
	
		parent::__construct();
		$this->frame_codigo1->show_all();
		$this->labelframe_codigo1->set_text('Cliente');
		$this->entry_codigo1->connect('key_press_event',
        	array($this,'entry_enter'),
            'select c.codigo, c.nome, c.contato, c.dtnasc, c.sexo, c.dtcadastro, c.cnpj_cpf, c.ie_rg from clientes as c',
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
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		$this->orderby["d.data, d.hora"]="Data";
		$this->orderby["d.codcli"]="Cod. Cliente";
		$this->orderby["c.nome"]="Nome Cliente";
		$this->orderby["d.totalnf"]="Total";
		$this->orderby["d.vendedor"]="Cod. Vendedor";
		$this->orderby["f.nome"]="Nome Vendedor";
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);
		
		$this->hbox_checkbutton->show_all();

		$this->checkbutton1->set_label("Hora");		
		$this->checkbutton2->set_label("Cod.Cliente");
		$this->checkbutton3->set_label("Nome Cliente");
		$this->checkbutton3->set_active(TRUE);
		$this->checkbutton4->set_label("Cod.Vendedor");
		$this->checkbutton5->set_active(TRUE);
		$this->checkbutton5->set_label("Nome Vendedor");
		$this->checkbutton6->set_label("Cod.Venda");

		//$this->janela->show();
		$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		$this->janela->set_title("Totais das formas de Pagamento");
		
		$this->button_html->connect_simple('clicked',array($this, 'gerar'),'html');
		$this->button_texto->connect_simple('clicked',array($this, 'gerar'),'texto');
		$this->button_tela->connect_simple('clicked',array($this, 'gerar'),'tela');
	}
	
	function gerar($tipo){
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
		
		
		$codcliente=$this->pegaNumero($this->entry_codigo1);
		$nomecliente=$this->label_codigo1->get_text();
        if (!empty($codcliente)){
			if(!$this->retornabusca2('clientes', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Cliente ou deixe em branco!');
                return;
            }
		}
			
		$this->con=$this->conecta();
		$sql="SELECT d.coddevolucoes, d.data, d.hora, d.codcli, c.nome, d.totalnf, d.vendedor, f.nome, d.codsaidas FROM devolucoes AS d INNER JOIN clientes AS c ON (d.codcli=c.codigo) INNER JOIN funcionarios AS f ON (d.vendedor=f.codigo) WHERE d.data>='$this->data1' AND d.data<='$this->data2' ";
		
		if (!empty($codcliente)){
			$sql.=" AND d.codcli='$codcliente' ";
		}
		// pega qual a escolha da combobox
		$order=$this->combo1->get_active_text();
		// pega o nome do campo da array criada na funcao anterior
		$key=array_search($order, $this->orderby);
		 //ORDER BY m.data_c 
		$sql.=" ORDER BY ".$key;
		
		
    	if(!$this->resultado=$this->con->Query($sql)){
			msg("Erro ao executar comando SQL!");
			return;
		}else{
			$this->numerolin=$this->con->NumRows($this->resultado);
			if($this->numerolin==0){
				msg("Sua consulta nao retornou nenhum resultado!");
				return;
			}
		}
    	
		$titulo="Relatorio de Devolucoes Totais Por Cliente";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		if (!empty($codcliente)){
			$cabeca[1]=" Cliente: ".$codcliente." - ".$nomecliente;
		}
		
		
		$cabtabela[0]="Cod.";
		$cabtabela[1]="Data";
		if($this->checkbutton1->get_active()) $cabtabela[2]="Hora";
		if($this->checkbutton2->get_active()) $cabtabela[3]="Cliente Cod.";
		if($this->checkbutton3->get_active()) $cabtabela[4]="Cliente Nome";
		$cabtabela[5]="Total";
		if($this->checkbutton4->get_active()) $cabtabela[6]="Vendedor Cod.";
		if($this->checkbutton5->get_active()) $cabtabela[7]="Vendedor Nome";
		if($this->checkbutton6->get_active()) $cabtabela[8]="Venda Cod.";
		
		$j=0;
		while($i = $this->con->FetchRow($this->resultado)) {
			$corpo[$j][0]=$i[0];
			$corpo[$j][1]=$this->corrigeNumero($i[1],'data');
			if($this->checkbutton1->get_active()) $corpo[$j][2]=$i[2];
			if($this->checkbutton2->get_active()) $corpo[$j][3]=$i[3];
			if($this->checkbutton3->get_active()) $corpo[$j][4]=$i[4];
			$corpo[$j][5]=$this->corrigeNumero($i[5],'moeda');
			if($this->checkbutton4->get_active()) $corpo[$j][6]=$i[6];
			if($this->checkbutton5->get_active()) $corpo[$j][7]=$i[7];
			if($this->checkbutton6->get_active()) $corpo[$j][8]=$i[8];

			// soma total
			$total+=$i[5];
			
			$j++;
		}

		$pe[0]="Total de Devolucoes: ".$this->mascara2($total,'moeda');

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