<?php
include_once('relatorios'.bar.'relatorios.php');
class comprasdetalhadaporitem extends relatorios {

    function comprasdetalhadaporitem(){
	
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
		
		$this->orderby["s.data, s.hora"]="Data/Hora";
		$this->orderby["s.codfor"]="Cod. Fornecedor";
		$this->orderby["c.nome"]="Nome Fornecedor";
		$this->orderby["e.codmerc"]="Cod Mercadoria";
		$this->orderby["m.descricao"]="Descricao Mercadoria";
		$this->orderby["e.quantidade"]="Quantidade";
		$this->orderby["precototal"]="Preco Total";
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);

		$this->hbox_checkbutton->show_all();
		$this->checkbutton1->hide();
		$this->checkbutton2->set_label("Hora");
		$this->checkbutton3->set_label("Cod Fornecedor");
		$this->checkbutton4->set_label("Nome Fornecedor");
		$this->checkbutton4->set_active(TRUE);
		$this->checkbutton5->set_label("Cod Merc");
		$this->checkbutton5->set_active(TRUE);
		$this->checkbutton6->set_label("Descr Merc");
		$this->checkbutton6->set_active(TRUE);

		//$this->janela->show();
		$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		$this->janela->set_title("Relatorio de Compras Detalhado por Item");
		
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
		
		
		$codfor=$this->pegaNumero($this->entry_codigo1);
        if (!empty($codfor)){
			if(!$this->retornabusca2('fornecedores', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Fornecedor ou deixe em branco!');
                return;
            }
		}
			
		$this->con=$this->conecta();
		$sql="SELECT s.data, s.hora, s.codfor, c.nome, e.codmerc, m.descricao, e.precooriginal as precounitario, e.quantidade, e.precooriginal*e.quantidade as precototal FROM entsai AS e INNER JOIN entradas AS s ON (e.codentsai=s.codentradas) INNER JOIN fornecedores AS c ON (s.codfor=c.codigo) INNER JOIN mercadorias AS m ON (e.codmerc=m.codmerc) WHERE e.tipo='E' AND s.data>='$this->data1' AND s.data<='$this->data2' ";
		if (!empty($codfor)){
			$sql.=" AND s.codfor='$codfor' ";
		}
		// pega qual a escolha da combobox
		$order=$this->combo1->get_active_text();
		// pega o nome do campo da array criada na funcao anterior
		$key=array_search($order, $this->orderby);
		
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
    	
		$titulo="Relatorio de Compras Detalhado por Item";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Data";
		if($this->checkbutton2->get_active()) $cabtabela[1]="Hora";
		if($this->checkbutton3->get_active()) $cabtabela[2]="Cli.For.";
		if($this->checkbutton4->get_active()) $cabtabela[3]="Fornecedor";
		if($this->checkbutton5->get_active()) $cabtabela[4]="Merc.Cod.";
		if($this->checkbutton6->get_active()) $cabtabela[5]="Mercadoria";		
		$cabtabela[6]="$ Unit.";
		$cabtabela[7]="Quant.";
		$cabtabela[8]="$ Total.";
		
		$j=0;
		while($i = $this->con->FetchRow($this->resultado)) {
			$corpo[$j][0]=$this->corrigeNumero($i[0],'data');
			if($this->checkbutton2->get_active()) $corpo[$j][1]=$i[1];
			if($this->checkbutton3->get_active()) $corpo[$j][2]=$i[2];
			if($this->checkbutton4->get_active()) $corpo[$j][3]=$i[3];
			if($this->checkbutton5->get_active()) $corpo[$j][4]=$i[4];
			if($this->checkbutton6->get_active()) $corpo[$j][5]=$i[5];
			$corpo[$j][6]=$this->mascara2($i[6],'virgula');
			$corpo[$j][7]=number_format($i[7], 3, ',', '');
			$corpo[$j][8]=$this->mascara2($i[8],'virgula');
			// soma total
			$total+=$i[8];
			
			$j++;
		}
		
		$pe[0]="Total de Compras: ".$this->mascara2($total,'moeda');;
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