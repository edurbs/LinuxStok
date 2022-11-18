<?php
include_once('relatorios'.bar.'relatorios.php');
class comprastotais extends relatorios {

    function comprastotais (){
	
		parent::__construct();
		$this->frame_codigo1->show_all();
		$this->labelframe_codigo1->set_text('Fornecedor');
		$this->entry_codigo1->connect('key_press_event',
        	array($this,'entry_enter'),
            'select c.codigo, c.nome, c.contato, c.dtnasc, c.sexo, c.dtcadastro, c.cnpj_cpf, c.ie_rg from fornecedores as c',
            true,
            $this->entry_codigo1, 
            $this->label_codigo1,
            "fornecedores",
            "nome",
            "codigo"
        );
        $this->entry_codigo1->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'fornecedores', 
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
		$this->orderby["d.codfor"]="Cod. Fornecedor";
		$this->orderby["c.nome"]="Nome Fornecedor";
		$this->orderby["totalnf"]="Total";
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);
		
		$this->hbox_checkbutton->show_all();

		$this->checkbutton1->set_label("Hora");		
		$this->checkbutton2->set_label("Cod.Fornecedor");
		$this->checkbutton3->set_label("Nome Fornecedor");
		$this->checkbutton3->set_active(TRUE);
		$this->checkbutton4->hide();
		$this->checkbutton5->hide();
		$this->checkbutton6->hide();

		//$this->janela->show();
		$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		$this->janela->set_title("Totais das Compras");
		
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
		
		
		$codfornecedor=$this->pegaNumero($this->entry_codigo1);
		$nomefornecedor=$this->label_codigo1->get_text();
        if (!empty($codfornecedor)){
			if(!$this->retornabusca2('fornecedores', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Fornecedor ou deixe em branco!');
                return;
            }
		}
			
		$this->con=$this->conecta();
		$sql="SELECT d.codentradas, d.data, d.hora, d.codfor, c.nome, d.totalmerc-d.desconto AS totalnf FROM entradas AS d INNER JOIN fornecedores AS c ON (d.codfor=c.codigo) WHERE d.data>='$this->data1' AND d.data<='$this->data2' ";
		
		if (!empty($codfornecedor)){
			$sql.=" AND d.codfor='$codfornecedor' ";
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
    	
		$titulo="Relatorio de Compras Totais Por Fornecedor";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		if (!empty($codfornecedor)){
			$cabeca[1]=" Fornecedor: ".$codfornecedor." - ".$nomefornecedor;
		}
		
		$cabtabela[0]="Cod.";
		$cabtabela[1]="Data";
		if($this->checkbutton1->get_active()) $cabtabela[2]="Hora";
		if($this->checkbutton2->get_active()) $cabtabela[3]="Cliente Cod.";
		if($this->checkbutton3->get_active()) $cabtabela[4]="Cliente Nome";
		$cabtabela[5]="Total";
		
		$j=0;
		while($i = $this->con->FetchRow($this->resultado)) {
			$corpo[$j][0]=$i[0];
			$corpo[$j][1]=$this->corrigeNumero($i[1],'data');
			if($this->checkbutton1->get_active()) $corpo[$j][2]=$i[2];
			if($this->checkbutton2->get_active()) $corpo[$j][3]=$i[3];
			if($this->checkbutton3->get_active()) $corpo[$j][4]=$i[4];
			$corpo[$j][5]=$this->corrigeNumero($i[5],'virgula');

			// soma total
			$total+=$i[5];
			
			$j++;
		}

		$pe[0]="Total de Compras: ".$this->mascara2($total,'moeda');

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