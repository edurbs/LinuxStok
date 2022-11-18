<?php
include_once('relatorios'.bar.'relatorios.php');
class ocorrencia extends relatorios {

    function ocorrencia(){
	
		parent::__construct();
		$this->frame_codigo1->show_all();
        
        $this->frame_codigo3->show_all();
		$this->labelframe_codigo3->set_text('Tipo');
		$this->entry_codigo3->connect('key_press_event',
        	array($this,'entry_enter'),
            'select * from ocorrencia_tipo ',
            true,
            $this->entry_codigo3, 
            $this->label_codigo3,
            "ocorrencia_tipo",
            "descricao",
            "codigo"
        );
        $this->entry_codigo3->connect_simple('focus-out-event',
        	array($this,'retornabusca22'), 
            'ocorrencia_tipo', 
            $this->entry_codigo3, 
            $this->label_codigo3, 
            'codigo', 
            'descricao'
        );
		
		$this->hbox_data->show_all();
		$this->hbox_button->show_all();
		
	

		
		$this->button_html->connect_simple('clicked',array($this, 'gerar'),'html');
		$this->button_texto->connect_simple('clicked',array($this, 'gerar'),'texto');
		$this->button_tela->connect_simple('clicked',array($this, 'gerar'),'tela');
	}
	
	function gerar_ocorrencia_inicio(){
		$this->label_combo1->show();
		$this->label_combo1->set_text("Ordenar por ");
		
		
		$this->combo1->show();
		// coloca arrays na combobox 
		foreach ($this->orderby as $tmp){
			$this->combo1->append_text($tmp);
		}
		// bota o primeiro como escolha atual 
		$this->combo1->set_active(0);	
	}
	function gerar_ocorrencia($tipo){
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
		
		$this->codigo_tipo=$this->pegaNumero($this->entry_codigo3);
        if (!empty($this->codigo_tipo)){
        	if(!$this->retornabusca2('ocorrencia_tipo', $this->entry_codigo3, $this->label_codigo3, 'codigo', 'descricao')){
	     	    msg('Preencha corretamente o campo Tipo ou deixe em branco');
	        	return;
        	} 
		}
			
		$this->con=$this->conecta();
		if (!empty($this->codigo_tipo)){
			$this->sql.=" AND o.codigo_tipo='$this->codigo_tipo' ";
		}
		$this->sql.=" AND o.data>='$this->data1' AND o.data<='$this->data2' ";

		// pega qual a escolha da combobox
		$order=$this->combo1->get_active_text();
		// pega o nome do campo da array criada na funcao anterior
		$key=array_search($order, $this->orderby);
		
		$this->sql.=" ORDER BY ".$key;
		
		
    	if(!$this->resultado=$this->con->Query($this->sql)){
			msg("Erro ao executar comando SQL!");
			return;
		}else{
			$this->numerolin=$this->con->NumRows($this->resultado);
			if($this->numerolin==0){
				msg("Sua consulta nao retornou nenhum resultado!");
				return;
			}
		}
    	
		$titulo="Relatorio de Ocorrencia";
		$cabeca[0]="Periodo: ".$this->entry_data1->get_text()." a ".$this->entry_data2->get_text();
		$cabeca[1]=$this->cabeca[1];
		
		
		
		$cabtabela[0]="Data";
		$cabtabela[1]="Resumo";
		$cabtabela[2]="Cod.T";
		$cabtabela[3]="Tipo";
		$cabtabela[4]="Cod.F";
		$cabtabela[5]="Funcionario";
		$cabtabela[6]="Obs";	
		
		
		$j=0;
		while($i = $this->con->FetchRow($this->resultado)) {
			$corpo[$j][0]=$this->corrigeNumero($i[0],'data');
			$corpo[$j][1]=$i[1];
			$corpo[$j][2]=$i[2];
			$corpo[$j][3]=$i[3];
			$corpo[$j][4]=$i[4];
			$corpo[$j][5]=$i[5];
			$corpo[$j][6]=$i[6];
			
			$j++;
		}
		
		$pe[0]="";
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