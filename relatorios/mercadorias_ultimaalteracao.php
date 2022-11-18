<?php
include_once('relatorios'.bar.'relatorios.php');
class mercadorias_ultimaalteracao extends relatorios {

    function mercadorias_ultimaalteracao (){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		$this->orderby["ultimaaltera "]="Data de Alteracao";
		$this->orderby["descricao "]="Descricao";
		$this->orderby["ultimaaltera, descricao"]="Data de Alteracao e Descricao";
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);
		
		$this->hbox_checkbutton->show_all();
    	$this->checkbutton1->set_label("Com Data de Alteracao");
    	$this->checkbutton1->set_active(TRUE);
    	$this->checkbutton2->set_label("Com Observacao");
    	$this->checkbutton2->set_active(TRUE);
    	$this->checkbutton3->hide();
    	$this->checkbutton4->hide();
    	$this->checkbutton5->hide();
    	$this->checkbutton6->hide();
		
		
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

		$con=$this->conecta();
		
		$sql="SELECT codmerc, descricao, precovenda ";
		
		if($this->checkbutton2->get_active()){
			$sql.=", obs ";	
		}
		if($this->checkbutton1->get_active()){
			$sql.=", ultimaaltera ";	
		}
		
		$sql.=" FROM mercadorias WHERE ultimaaltera>='$this->data1' AND ultimaaltera<='$this->data2'";
		
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
    	
		$titulo="Relatorio de Mercadorias Alteradas";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		
		$cabtabela[0]="Cod.Merc";
		$cabtabela[1]="Descricao da Mercadoria";
		$cabtabela[2]="Preco";
		if($this->checkbutton2->get_active()){
			$cabtabela[3]="Obs";
		}
		if($this->checkbutton1->get_active()){
			$cabtabela[4]="Dt.Altera";
		}
		
		$j=0;
		while($i = $con->FetchRow($resultado)) {
            $corpo[$j][0]=$i[0];
            $corpo[$j][1]=$i[1];
            $corpo[$j][2]=$this->corrigeNumero($i[2],'virgula');
            if($this->checkbutton2->get_active()){
            	$corpo[$j][3]=$i[3]; // obs	
            }
            if($this->checkbutton1->get_active()){
            	$corpo[$j][4]=$this->corrigeNumero($i[4],'data');	
            }
            
            // controla linha
            $j++;
        }
        
        $pe[0]="Quantidade de Mercadorias Alteradas: ".$j;
        

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