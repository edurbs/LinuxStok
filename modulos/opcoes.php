<?php
class opcoes extends funcoes {
	function cria_opcoes(){
		/*
			aqui se constroi a lista de opcoes do linuxstok usando uma matriz
			a coluna 1 eh a descricao da opcoes que vai aparecer na tela
			a col. 2 eh o nome da opcoes para ser recuperada com a opcoes retorna_opcao()
			a col. 3 eh o nome do widget que sera usado para pegar os dados
			no caso de o widget for um campo memo, entao a col 3 sera o scrolledwindow, a col 4 sera o textbuffer e a 5 o textview
			col. 10 contem a validacao do campo
			col. 20 retorna o conteudo do campo para a variavel conteudo
			col. 30 seta o conteudo do campo pegando da variavel conteudo

		*/
		
		$this->tab_op[0][1]="Integra estoque<->financeiro?"; 
		$this->tab_op[0][2]="integraestoquefinanceiro"; 
		$this->tab_op[0][3]=new GtkCheckButton();
		$this->tab_op[0][20]='
		$conteudo=$this->tab_op[0][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[0][30]='
		$this->tab_op[0][3]->set_active($conteudo);
		';
		
		$this->tab_op[1][1]="Plano de contas de Devolucao"; 
		$this->tab_op[1][2]="placondevolucao"; 
		$this->tab_op[1][3]=new GtkEntry();
		$this->tab_op[1][3]->connect('key_press_event',
          array($this,'entry_enter'),
            'select * from placon  order by codigo',
            true,
            $this->tab_op[1][3],
            null,
            'placon',
            "descricao",
            "codigo"
        );
		$this->tab_op[1][3]->connect_simple('focus-out-event',
			array($this,'retornabusca22'), 
			'placon', 
			$this->tab_op[1][3], 
			null, 
			'codigo', 
			'descricao'
		);
		
		$this->tab_op[1][10]='
		$tmp=$this->tab_op[1][3]->get_text();
        $tmp2=$this->retornabusca4("codigo", "placon", "codigo", $tmp);
		if(empty($tmp2)){
            msg("Preencha corretamente o campo plano de contas de Cobranca!");
            $retorna=TRUE;
        }
		';
		$this->tab_op[1][20]='
		$conteudo=$this->tab_op[1][3]->get_text();
		';
		$this->tab_op[1][30]='
		$this->tab_op[1][3]->set_text($conteudo);
		';
		
		$this->tab_op[2][1]="Recibo de vendas/devolucao: Numero maximo de colunas"; 
		$this->tab_op[2][2]="largurapagina"; 
		$this->tab_op[2][3]=new GtkEntry();
		$this->tab_op[2][10]='
		$tmp=$this->pegaNumero($this->tab_op[2][3]);
		if(empty($tmp)){
			msg("Numero de colunas invalido!");
			$retorna=TRUE;
		}
		';
		$this->tab_op[2][20]='
		$conteudo=$this->pegaNumero($this->tab_op[2][3]);
		';
		$this->tab_op[2][30]='
		$this->tab_op[2][3]->set_text($conteudo);
		';
		
		$this->tab_op[3][1]="Recibo de vendas/devolucao: Numero maximo de linhas"; 
		$this->tab_op[3][2]="alturarecibo"; 
		$this->tab_op[3][3]=new GtkEntry();
		$this->tab_op[3][10]='
		$tmp=$this->pegaNumero($this->tab_op[3][3]);
		if(empty($tmp)){
			msg("Numero de linhas invalido!");
			$retorna=TRUE;
		}
		';
		$this->tab_op[3][20]='
		$conteudo=$this->pegaNumero($this->tab_op[3][3]);
		';
		$this->tab_op[3][30]='
		$this->tab_op[3][3]->set_text($conteudo);
		';
		
		$this->tab_op[4][1]="Recibo de vendas/devolucao: Cabecalho"; 
		$this->tab_op[4][2]="cabecalhorecibo"; 
		$this->tab_op[4][3]=new GtkScrolledWindow();
		$this->tab_op[4][4]=new GtkTextBuffer();
		$this->tab_op[4][5]=new GtkTextView();
        $this->tab_op[4][5]->set_buffer($this->tab_op[4][4]);
        
        $this->tab_op[4][3]->add($this->tab_op[4][5]);
        $this->tab_op[4][20]='
        $conteudo=$this->tab_op[4][4]->get_text(
            $this->tab_op[4][4]->get_start_iter(),
            $this->tab_op[4][4]->get_end_iter()
        );
        ';
        $this->tab_op[4][30]='
		$this->tab_op[4][4]->set_text($conteudo);
		';
		
		$this->tab_op[5][1]="Recibo de vendas/devolucao: Rodape (nao afeta orcamento)"; 
		$this->tab_op[5][2]="rodaperecibo"; 
		$this->tab_op[5][3]=new GtkScrolledWindow();
		$this->tab_op[5][4]=new GtkTextBuffer();
		$this->tab_op[5][5]=new GtkTextView();
        $this->tab_op[5][5]->set_buffer($this->tab_op[5][4]);
        
        $this->tab_op[5][3]->add($this->tab_op[5][5]);
        $this->tab_op[5][20]='
        $conteudo=$this->tab_op[5][4]->get_text(
            $this->tab_op[5][4]->get_start_iter(),
            $this->tab_op[5][4]->get_end_iter()
        );
        ';
        $this->tab_op[5][30]='
		$this->tab_op[5][4]->set_text($conteudo);
		';
		
		$this->tab_op[6][1]="Recibo de vendas/devolucao: numero de vias"; 
		$this->tab_op[6][2]="viasrecibo"; 
		$this->tab_op[6][3]=new GtkEntry();
		$this->tab_op[6][10]='
		$tmp=$this->pegaNumero($this->tab_op[6][3]);
		if(empty($tmp)){
			msg("Numero de vias do recibo invalido!");
			$retorna=TRUE;
		}
		';
		$this->tab_op[6][20]='
		$conteudo=$this->pegaNumero($this->tab_op[6][3]);
		';
		$this->tab_op[6][30]='
		$this->tab_op[6][3]->set_text($conteudo);
		';

		$this->tab_op[7][1]="Troca automatica de senha no pdv apos a venda?"; 
		$this->tab_op[7][2]="autotrocasenhapdv"; 
		$this->tab_op[7][3]=new GtkCheckButton();
		$this->tab_op[7][20]='
		$conteudo=$this->tab_op[7][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[7][30]='
		$this->tab_op[7][3]->set_active($conteudo);
		';
		
		$this->tab_op[8][1]="Recibo de vendas/devolucao: digitar observacao ao imprimir?"; 
		$this->tab_op[8][2]="observacaorecibo"; 
		$this->tab_op[8][3]=new GtkCheckButton();
		$this->tab_op[8][20]='
		$conteudo=$this->tab_op[8][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[8][30]='
		$this->tab_op[8][3]->set_active($conteudo);
		';
		
		$this->tab_op[9][1]="PDV: pergunta endereco do cliente?"; 
		$this->tab_op[9][2]="pdvenderecocliente"; 
		$this->tab_op[9][3]=new GtkCheckButton();
		$this->tab_op[9][20]='
		$conteudo=$this->tab_op[9][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[9][30]='
		$this->tab_op[9][3]->set_active($conteudo);
		';
		
		$this->tab_op[10][1]="Recibo de vendas/devolucao: tipo do recibo"; 
		$this->tab_op[10][2]="tiporecibo"; 
		$this->tab_op[10][3]= GtkComboBox::new_text();
		$this->combo_tiporecibo[0]="Largo";
		$this->combo_tiporecibo[1]="Estreito";
		foreach ($this->combo_tiporecibo as $tmp){
			$this->tab_op[10][3]->append_text($tmp);
		}
		$this->tab_op[10][3]->set_active(0);
		$this->tab_op[10][20]='
		$escolha_combo=$this->tab_op[10][3]->get_active_text();
		$conteudo=array_search($escolha_combo, $this->combo_tiporecibo);
		';
		$this->tab_op[10][30]='
		$this->tab_op[10][3]->set_active($conteudo);
		';
		
		$this->tab_op[11][1]="Recibo de vendas/devolucao: imprimir cliente/endereco?"; 
		$this->tab_op[11][2]="reciboimprimircliente"; 
		$this->tab_op[11][3]=new GtkCheckButton();
		$this->tab_op[11][20]='
		$conteudo=$this->tab_op[11][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[11][30]='
		$this->tab_op[11][3]->set_active($conteudo);
		';
		
		$this->tab_op[12][1]="Recibo de vendas/devolucao: usar descricao resumida da mercadoria?"; 
		$this->tab_op[12][2]="recibodescricaoresumida"; 
		$this->tab_op[12][3]=new GtkCheckButton();
		$this->tab_op[12][20]='
		$conteudo=$this->tab_op[12][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[12][30]='
		$this->tab_op[12][3]->set_active($conteudo);
		';
		
		$this->tab_op[13][1]="Atualizacao automatica das listas dos cadastros?"; 
		$this->tab_op[13][2]="autotreeview"; 
		$this->tab_op[13][3]=new GtkCheckButton();
		$this->tab_op[13][20]='
		$conteudo=$this->tab_op[13][3]->get_active();
        if($conteudo){
            $conteudo=1;
        }else{
            $conteudo=0;
        }
		';
		$this->tab_op[13][30]='
		$this->tab_op[13][3]->set_active($conteudo);
		';
		
	}
	
	function opcoes(){
		global $parente;
		// chama funcao que cria matriz com as opcoes
		$this->cria_opcoes();
		
		$this->window_opcoes=new GtkWindow(); // cria a janela
		if($parente) $this->window_opcoes->set_transient_for($parente);
		$this->window_opcoes->connect_simple('hide', array($this,'fechaHideShow'));
        $this->window_opcoes->connect('delete-event', array($this,'fecha_janela'));
        //$this->window_opcoes->set_uposition( retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy") );
        //$this->window_opcoes->set_size_request( intval( retorna_CONFIG("largura") ), intval( retorna_CONFIG("altura") ) );
        $this->window_opcoes->maximize();
        $this->window_opcoes->set_title("Opcoes");
        $this->window_opcoes->set_icon_from_file('tema'.bar.'icone.png');
		
		$vbox=new GtkVBox(); // cria as linhas para colocar as opcoes
		for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
			// controla de repeticao de nomes de campo
			if(count($controla_nomes)>0){
				foreach($controla_nomes as $tmp){
					if($this->tab_op[$i][2]==$tmp){
						msg("Nome do campo em opcoes esta repetido: ".$this->tab_op[$i][2]." \nVerifique o arquivo opcoes.php");
						return;
					}
				} 
			}
			// se nao tiver repetido continua
			$controla_nomes[$i]=$this->tab_op[$i][2];
			
			$hbox[$i]=new GtkHBox(); // cria um gtkhbox para esta opcao
			$hbox[$i]->set_homogeneous(TRUE);
			$label=new GtkLabel($this->tab_op[$i][1]); // cria um label com o titulo da opcao
			$label->set_line_wrap(TRUE);
			$label->set_justify(Gtk::JUSTIFY_LEFT);
			$hbox[$i]->pack_start($label, false, FALSE); // adiciona o label na linha

			$hbox[$i]->pack_start($this->tab_op[$i][3], TRUE, TRUE);
			$vbox->pack_start($hbox[$i], false, false); // adiciona a linha na tela
		}
		
		
		// coloca os botoes de gravar/mostrar
		$i++;
		$this->botao_salvar=GtkButton::new_from_stock(Gtk::STOCK_SAVE);
		$this->botao_salvar->connect_simple("clicked",array($this,"salvar_opcoes"));
		
		$this->botao_atualizar=GtkButton::new_from_stock(Gtk::STOCK_REFRESH);
		$this->botao_atualizar->connect_simple("clicked",array($this,"atualizar_opcoes"));
		
		$this->botao_limpar=GtkButton::new_from_stock(Gtk::STOCK_CLEAR);
		$this->botao_limpar->connect_simple("clicked",array($this,"limpar_opcoes"));
		
		$hbox[$i]=new GtkHBox(); // cria um gtkhbox para esta opcao
		$hbox[$i]->set_homogeneous(TRUE);
		$hbox[$i]->pack_start($this->botao_atualizar, false, FALSE);
		$hbox[$i]->pack_start($this->botao_salvar, false, FALSE);
		$hbox[$i]->pack_start($this->botao_limpar, false, FALSE);
		$vbox->pack_start($hbox[$i], false, false); // adiciona a linha na tela
		
		
		$scrol=new GtkScrolledWindow();
		$scrol->add_with_viewport($vbox);
		$this->window_opcoes->add($scrol);
		$this->window_opcoes->show_all();
		
		$this->atualizar_opcoes();
	}
	
	function salvar_opcoes(){
		// faz validacao
		for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
			if(!empty($this->tab_op[$i][10])){
				eval($this->tab_op[$i][10]);
				if($retorna==TRUE){
					return;
				}
			}
		}			
        $con=$this->conectar();

		$sql="SELECT build FROM opcoes";
		$resultado=$con->Query($sql);
		$linhas=$con->NumRows($resultado);
		if($linhas==0){
			$sql="INSERT INTO opcoes (";
			for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
    			$sql.=" ".$this->tab_op[$i][2].","; // insere os nomes dos campos
			}
			$sql=substr($sql,0,-1); // remove a ultima virgula, senao da erro
			$sql.=") VALUES (";
			for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
				$conteudo="";
				if(!empty($this->tab_op[$i][20])){
					eval($this->tab_op[$i][20]); // pega dados do widget e bota na var. conteudo
				}
				$sql.=" '".$conteudo."',"; // insere os dados dos campos
			}			
			$sql=substr($sql,0,-1); // remove a ultima virgula, senao da erro
			$sql.=")";
		}else{
			$sql="UPDATE opcoes SET ";
			for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
				$sql.=" ".$this->tab_op[$i][2]."="; // insere os nomes dos campos
				$conteudo="";
				if(!empty($this->tab_op[$i][20])){
					eval($this->tab_op[$i][20]); // pega dados do widget e bota na var. conteudo
				}
				$sql.=" '".$conteudo."',"; // insere os dados dos campos
			}
			$sql=substr($sql,0,-1); // remove a ultima virgula, senao da erro
		}

    	if(!$con->Query($sql)){
        	msg("Erro ao gravar opcoes");
        	return;
        }
        msg("Opcoes gravadas com sucesso!");
        $this->desconectar($con);
	}
	
	function atualizar_opcoes(){
		$con=$this->conectar();
		$sql="SELECT ";
		for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
    		$sql.=" ".$this->tab_op[$i][2].","; // insere os nomes dos campos
    	}
    	$sql=substr($sql,0,-1); // remove a ultima virgula, senao da erro
		$sql.=" FROM opcoes ";
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
        	$conteudo=$resultado2[$this->tab_op[$i][2]];
			if($conteudo<>""){
        		eval($this->tab_op[$i][30]);
			}
        }
        $this->desconectar($con);
	}
	
	function limpar_opcoes(){
		for($i=0;$i<count($this->tab_op);$i++){ // le todas linhas da matriz
        	$conteudo="";
        	eval($this->tab_op[$i][30]);
        }
	}

}
?>