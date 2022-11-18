<?php
include_once('relatorios'.bar.'relatorios.php');
include_once('relatorios'.bar.'ocorrencia.php');
class clientes_ocorrencias extends ocorrencia {

    function clientes_ocorrencias(){
	
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
        
		
		$this->orderby["o.data, o.hora"]="Data/Hora";
		$this->orderby["o.tipo"]="Tipo";
		$this->orderby["o.funcionario"]="Funcionario";
		$this->orderby["o.conta_codigo"]="Conta";
		$this->orderby["o.resumo"]="Resumo";
		$this->gerar_ocorrencia_inicio();
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
		
		
		$this->cadastro_codigo=$this->pegaNumero($this->entry_codigo1);
        if (empty($this->cadastro_codigo) or !$this->retornabusca2('clientes', $this->entry_codigo1, $this->label_codigo1, 'codigo', 'nome')){
	        msg('Preencha corretamente o campo do Cliente');
	        return;
		}

		$this->sql="SELECT o.data, o.resumo, o.codigo_tipo, ot.descricao, o.funcionario, f.nome, o.obs FROM ocorrencia AS o INNER JOIN ocorrencia_tipo AS ot ON (ot.codigo=o.codigo_tipo) LEFT JOIN funcionarios AS f ON (f.codigo=o.funcionario) WHERE o.cadastro='clientes' AND o.cadastro_codigo='$this->cadastro_codigo' ";
		
		$this->cabeca[1]="Cliente ".$this->cadastro_codigo." - ".$this->retornabusca4('nome','clientes','codigo',$this->cadastro_codigo);
				
		$this->gerar_ocorrencia($tipo);
	}
}

?>