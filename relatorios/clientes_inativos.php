<?php
include_once('relatorios'.bar.'relatorios.php');
class clientes_inativos extends relatorios {

    function clientes_inativos(){
	
		parent::__construct();
		
		$this->hbox_data->show_all();
		$this->label_data1->set_text('Inativos sao os que nao compraram desde: ');
		
		$this->frame_data2->hide();
		//$this->entry_data2->hide();
		//$this->label_data2->hide();
		
		$this->hbox_checkbutton->show_all();

		$this->checkbutton1->set_label("Incluir cadastrados como Inativos");
		$this->checkbutton1->set_active(FALSE);
		$this->checkbutton2->hide();
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
		if(!$this->valida_data($this->entry_data1->get_text())){
            msg('Data Invalida');
            return false;
        }
        $this->data1=$this->corrigeNumero($this->entry_data1->get_text(),"dataiso");

		$inativo=$this->checkbutton1->get_active();
		
		$this->con=$this->conecta();
		// olha que loucura esse SQL?!?!
		$sql="SELECT c.codigo, c.nome, c.dtcadastro, (SELECT SUM(s.totalnf) FROM saidas AS s WHERE s.codcli=c.codigo) AS compras, c.ultvenda FROM clientes AS c WHERE (SELECT count(s.codcli) FROM saidas AS s WHERE s.codcli=c.codigo AND s.data>='$this->data1')=0 AND c.dtcadastro<'$this->data1' ";
		
		if(!$inativo){ // NAO incluir inativos 
			$sql.=" AND c.inativo='0' ";
		}
		
		$sql.=" ORDER BY c.nome ";		
		
		
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
    	
		$titulo="Relatorio de Clientes Inativos";
		$cabeca[0]="Inativos sao aqueles que nao compraram desde ".$this->entry_data1->get_text();
		
		
		
		$cabtabela[0]="Codigo";
		$cabtabela[1]="Nome";
		$cabtabela[2]="Data Cadastro";
		$cabtabela[3]="Total de Compras";
		$cabtabela[4]="Ultima Venda";
		
		$j=0;
		while($i = $this->con->FetchRow($this->resultado)) {
			$corpo[$j][0]=$i[0];
			$corpo[$j][1]=$i[1];
			$corpo[$j][2]=$this->corrigeNumero($i[2],'data');
			$corpo[$j][3]=$this->corrigeNumero($i[3],'virgula2');
			$corpo[$j][4]=$this->corrigeNumero($i[4],'data');
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