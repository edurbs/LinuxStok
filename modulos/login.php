<?php
class login extends funcoes {
    function login($simples=false){
        global $ingles, $portugues;
        $this->Branco= new GtkStyle;
        $this->splash = new GtkWindow(0);
        $this->splash->set_position(Gtk::WIN_POS_CENTER);
        $this->splash->set_modal(TRUE);
        $this->splash->connect('delete-event',array($this,'tentafecharLogin'));

        $this->splash->realize();
        $this->fixed = new GtkFixed;
        $this->splash->add($this->fixed);

        $this->splash->realize();
        $this->splash->set_icon_from_file('tema'.bar.'icone.png');

        if(!$simples){
            // Faz a leitura de uma imagem
	    $ImagemPixmap = GtkImage::new_from_file('tema'.bar.'logo.png');
            $this->fixed->put($ImagemPixmap, 0, 0);
        }

        $altura=0;
        $this->button_entrar= new GtkButton("Entrar");
        $this->button_entrar->connect_simple('clicked', array($this,'entrar'),$simples);
		$esquerda=223+170;
        $this->fixed->put($this->button_entrar, $esquerda, $altura+10);

        if(!$simples){
            $this->button_sair= new GtkButton("Sair");
            $this->button_sair->connect_simple('clicked', array(&$this,'sair'),$simples);
            $this->fixed->put($this->button_sair, $esquerda+50, $altura+10);
        }
        
        $this->frame3=new GtkFrame('Modulo');
        $this->combo_modulo = new GtkComboBox();
        $this->listStore_combo = new GtkListStore(GObject::TYPE_STRING);
        $this->listStore_combo->append(array("Completo"));
        $this->listStore_combo->append(array("PDV"));
        $this->listStore_combo->append(array("Mov.Financeiro"));
        $this->combo_modulo->set_model($this->listStore_combo);
		$cellRenderer = new GtkCellRendererText();
		$this->combo_modulo->pack_start($cellRenderer);
		$this->combo_modulo->set_attributes($cellRenderer, 'text', 0);
		$this->combo_modulo->set_active(0);
        $this->combo_modulo->set_size_request(180,35);

        $this->combo_modulo->connect("key-press-event", array($this, "intro_keypressed"), 'button_entrar',$simples);
        $this->frame3->add($this->combo_modulo);
        $this->fixed->put($this->frame3, $esquerda-100, $altura+45);
        $this->frame3->set_style($this->Branco);

        //verifica se ha usuario cadastrados
        include_once("DBDriver".bar.retorna_CONFIG("BancoDeDados").'.class');
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $sql="select count(*) from  nivel2funcionario";
        if($resultado=$con->Query($sql,false)){
            $i = $con->FetchRow($resultado);
            if($i[0]>0){ // se tiver usuario cadastrado
                $this->frame1=new GtkFrame('Cod.Funcionario');
                $this->usuario = new GtkEntry();
                $this->usuario->set_size_request(90,25);
                $this->usuario->connect("key-press-event", array($this, "intro_keypressed"), 'senha',$simples);
                $this->frame1->add($this->usuario);
                $this->fixed->put($this->frame1, $esquerda-215, $altura);

                $this->frame2=new GtkFrame('Senha');
                $this->senha = new GtkEntry();
                $this->senha->set_size_request(90,25);
                $this->senha->set_visibility(0);
                $this->senha->connect("key-press-event", array($this, "intro_keypressed"), 'button_entrar',$simples);
                $this->frame2->add($this->senha);
                $this->fixed->put($this->frame2, $esquerda-100, $altura);
                $this->usuario->grab_focus();
                $this->frame2->set_style($this->Branco);
                $this->frame1->set_style($this->Branco);
                //echo 'existe usuario';
            }else{
                $this->usuario=false;
                $this->button_entrar->grab_focus();
				//$this->button_entrar->clicked();
				//$entrar=true;
                if($simples) {
                    msg("Nao existe usuario cadastrado.");
                    return;
                }
                //echo 'nao existe usuario';
            }
        }
        
		
		// confirmar data de hoje
		$setlocalelc_ctype=setlocale(LC_TIME, $portugues);

		$this->diadehoje=date('d',time());
		$this->nomedodiadehoje=ucwords(strftime("%A"));
		$this->nomedomesdehoje=ucwords(strftime("%B"));
		$this->anodehoje=date('Y',time());
        $this->datadehoje=$this->nomedodiadehoje.", ".$this->diadehoje." de ".$this->nomedomesdehoje." de ".$this->anodehoje;
        
        $hora=date("H:i");
	
        $label_data=new GtkLabel();
        $label_data->set_use_markup(TRUE);

        $label_data->set_markup(
			'<span foreground="red" size="12000">'.
			"<b>".$this->datadehoje." - ".$hora."</b>"
			.'</span>'
		);

        $this->fixed->put($label_data, 100, 325);
        
        
        $setlocalelc_ctype=setlocale(LC_TIME, $ingles);
        $this->splash->show_all();
        
        if($entrar) $this->button_entrar->clicked();
        
    }

    function tentafecharLogin(){
    		return true;
    	}
    	
    function entrar($simples){
  	    global $NivelAcessoDoLoginGeral,$parente,$usuario;

        if($this->usuario){
            $erro=false;
            $this->usuario1 = $this->usuario->get_text();
            $this->usuario1=$this->DeixaSoNumero($this->usuario1);
            if (!$this->retornabusca4("nome",'funcionarios','codigo',"$this->usuario1")){
                $erro=true;
            }else{
                $this->senha1 = $this->senha->get_text();

                $BancoDeDados=retorna_CONFIG("BancoDeDados");
                $con=&new $BancoDeDados;
                $con->Connect();
                $sql="select codigonivelacesso,senha from nivel2funcionario where codigofuncionario='$this->usuario1'";
                $resultado=$con->Query($sql);
                $i = $con->FetchRow($resultado);
                if($i[1]<>$this->senha1) $erro=true;
            }
            if($erro){
                msg('Usuario/Senha invalido(s)!');
                return;
            }else{
                $NivelAcessoDoLoginGeral = $i[0];
            }
        }

        $this->splash->hide();

        // modulo escolhido
        $modulo=$this->combo_modulo->get_active();
        
        if(!$simples and $modulo==0){
            // modulo completo - padrao
            include_once('modulos'.bar.'menugeral.php');
            new menugeral;
        }elseif($modulo==1 and $this->verificaPermissao('030101',false)){
        	// modulo PDV
        	include_once('movimenta'.bar.'pdv.php');
            new pdv("ativado");
        }elseif($modulo==2 and $this->verificaPermissao('030901',false)){
        	// modulo movimentacao financeira
        	include_once('movimenta'.bar.'movcontas.php');
            new movcontas("ativado");
        }
        if($parente){
			$parente->set_title("LinuxStok - Gestao Comercial GPL");
			//---ini Adilson
            $nome_usuario=$this->retornabusca4('nome','funcionarios','codigo',$this->usuario1);
			$parente->set_title('LinuxStok - '.$nome_usuario);
			//---fim Adilson		
			// ----> Linha Original : $parente->set_title("LinuxStok - Gestao Comercial GPL");
		}
        $usuario=$this->usuario1;
    }
    function sair($simples){
        if($simples){
            $this->splash->hide();
        }else{
            exit;
        }
        return true;
    }


    function intro_keypressed($widget, $event, $proximo, $simples=false) {
    		if($event->keyval==65293 or $event->keyval==65421){
        //if($event->keyval==GDK::KEY_Return or $event->keyval==GDK::KEY_KP_Enter){
            if($proximo<>'button_sair'){
                $this->entrar($simples);
            }
        }
    }

}
?>
