<?php
include_once('relatorios'.bar.'relatorios.php');
class detalhadaporitem extends relatorios {

    function detalhadaporitem(){
	
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
        
        $this->frame_codigo2->show_all();
		$this->labelframe_codigo2->set_text('Funcionario/Vendedor');
		$this->entry_codigo2->connect('key_press_event',
        	array($this,'entry_enter'),
            'select c.codigo, c.nome, c.cnpj_cpf, c.ie_rg from funcionarios as c',
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
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		$this->orderby["s.data, s.hora"]="Data/Hora";
		$this->orderby["s.vendedor"]="Cod Vendedor";
		$this->orderby["f.nome"]="Nome Vendedor";
		$this->orderby["s.codcli"]="Cod. Cliente";
		$this->orderby["c.nome"]="Nome Cliente";
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

		$this->checkbutton1->set_label("Cod Vendedor");
		$this->checkbutton1->set_active(TRUE);
		$this->checkbutton2->set_label("Nome Vendedor");
		$this->checkbutton3->set_label("Cod Cliente");
		$this->checkbutton4->set_label("Nome Cliente");
		$this->checkbutton4->set_active(TRUE);
		$this->checkbutton5->set_label("Cod Merc");
		$this->checkbutton5->set_active(TRUE);
		$this->checkbutton6->set_label("Descr Merc");
		$this->checkbutton6->set_active(TRUE);

		//$this->janela->show();
		$this->janela->resize(1,1); // coloca janela no menor tamanho possivel
		$this->janela->set_title("Relatorio de Vendas Detalhado por Item");
		
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
        if (!empty($codcliente)){
			if(!$this->retornabusca2('clientes', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Cliente ou deixe em branco!');
                return;
            }
		}
		
		$codvendedor=$this->pegaNumero($this->entry_codigo2);
        if (!empty($codcliente)){
			if(!$this->retornabusca2('funcionarios', $this->entry_codigo2, $this->label_codigo2, 'codigo', 'nome')){
                msg('Preencha corretamente o campo Funcionario/Vendedor ou deixe em branco!');
                return;
            }
		}
			
		$this->con=$this->conecta();
		$sql="SELECT s.vendedor, f.nome, s.data, s.hora, s.codcli, c.nome, e.codmerc, m.descricao, e.precocomdesconto as precounitario, e.quantidade, e.precocomdesconto*e.quantidade as precototal FROM entsai AS e INNER JOIN saidas AS s ON (e.codentsai=s.codsaidas) INNER JOIN clientes AS c ON (s.codcli=c.codigo) INNER JOIN mercadorias AS m ON (e.codmerc=m.codmerc) INNER JOIN funcionarios AS f ON (s.vendedor=f.codigo) WHERE e.tipo='S' AND s.data>='$this->data1' AND s.data<='$this->data2' ";
		if (!empty($codcliente)){
			$sql.=" AND s.codcli='$codcliente' ";
			$cabeca[1].=" Cliente: $codcliente ";
		}
		
		if (!empty($codvendedor)){
			$sql.=" AND s.vendedor='$codvendedor' ";
			$cabeca[1].=" - Vendedor: $codvendedor ";
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
    	
		$titulo="Relatorio de Vendas Detalhado por Item";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		
		
		if($this->checkbutton1->get_active()) $cabtabela[0]="Vend.Cod.";
		if($this->checkbutton2->get_active()) $cabtabela[1]="Vendedor";
		$cabtabela[2]="Data";
		$cabtabela[3]="Hora";
		if($this->checkbutton3->get_active()) $cabtabela[4]="Cli.Cod.";
		if($this->checkbutton4->get_active()) $cabtabela[5]="Cliente";
		if($this->checkbutton5->get_active()) $cabtabela[6]="Merc.Cod.";
		if($this->checkbutton6->get_active()) $cabtabela[7]="Mercadoria";		
		$cabtabela[8]="$ Unit.";
		$cabtabela[9]="Quant.";
		$cabtabela[10]="$ Total.";
		
		$j=0;
		while($i = $this->con->FetchRow($this->resultado)) {
			if($this->checkbutton1->get_active()) $corpo[$j][0]=$i[0];
			if($this->checkbutton2->get_active()) $corpo[$j][1]=$i[1];
			$corpo[$j][2]=$this->corrigeNumero($i[2],'data');
			$corpo[$j][3]=$i[3];
			if($this->checkbutton3->get_active()) $corpo[$j][4]=$i[4];
			if($this->checkbutton4->get_active()) $corpo[$j][5]=$i[5];
			if($this->checkbutton5->get_active()) $corpo[$j][6]=$i[6];
			if($this->checkbutton6->get_active()) $corpo[$j][7]=$i[7];
			$corpo[$j][8]=$this->mascara2($i[8],'virgula');
			$corpo[$j][9]=number_format($i[9], 3, ',', '');
			$corpo[$j][10]=$this->mascara2($i[10],'virgula');
			// soma total
			$total+=$i[10];
			
			$j++;
		}
		
		$pe[0]="Total de Vendas: ".$this->mascara2($total,'moeda');;
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