<?php
class controlecaixa extends funcoes {
    function controlecaixa($tipo) {

        $this->diadehoje=date('d',time());
        $this->mesdehoje=date('m',time());
        $this->anodehoje=date('Y',time());
        $this->datadehoje=$this->diadehoje."-".$this->mesdehoje."-".$this->anodehoje;

        $this->xml=$this->carregaGlade("controlecaixa",'Controle de Caixas',false,false,false,false);

        $this->button_ok= $this->xml->get_widget('button_ok');

        $this->janela->connect_simple('destroy', array($this,'cancelar'));

        $this->button_cancelar= $this->xml->get_widget('button_cancelar');
        $this->button_cancelar->connect_simple('clicked', array(&$this,'cancelar'));

        if($tipo=="fecharcontrolecaixa") {
            $this->button_ok->connect_simple('clicked', confirma, array(&$this, 'botaofechar'),"Depois de fechado nao sera possivel abrir novamente. \n Deseja realmente fechar este caixa?",null);
            $label=$this->button_ok->child;
            $label->set_text("Fechar");
        }else {
            $this->button_ok->connect_simple('clicked', confirma, array(&$this, 'botaoabrir'),'Deseja realmente abrir este caixa nesta data?',null);
        }
        $this->checkbutton_aberto= $this->xml->get_widget('checkbutton_aberto');
        $this->checkbutton_aberto->set_sensitive(false);
        $this->checkbutton_fechado= $this->xml->get_widget('checkbutton_fechado');
        $this->checkbutton_fechado->set_sensitive(false);

        $this->entry_data= $this->xml->get_widget('entry_data');
        $this->entry_data->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_data->set_text($this->datadehoje);
        $this->entry_data->connect('key-press-event', array(&$this, 'muda_enter'),&$this->button_ok);
        $this->entry_data->grab_focus();

        $this->entry_caixa=$this->xml->get_widget('entry_caixa');
        $this->label_caixa=$this->xml->get_widget('label_caixa');
        $this->entry_caixa->connect('key_release_event', array($this,'checkControleCaixa'));
        $this->entry_caixa->connect('key_press_event',
                array(&$this,entry_enter),
                'select * from cadcaixa',
                true,
                $this->entry_caixa,
                $this->label_caixa,
                'cadcaixa',
                'descricao',
                'codigo'
        );
        $this->entry_caixa->connect_simple('focus-out-event',
                array($this,retornabusca22),
                'cadcaixa',
                $this->entry_caixa,
                $this->label_caixa,
                'codigo',
                'descricao'
        );
        //$this->janela->show_all();
    }
    function checkControleCaixa() {
        if(!$codcadcaixa=$this->DeixaSoNumero($this->entry_caixa->get_text())) {
            $this->checkbutton_aberto->set_active(false);
            $this->checkbutton_fechado->set_active(false);
            return;
        }
        if(!$this->retornabusca4('codigo','cadcaixa','codigo',$codcadcaixa,false)) return;
        if(!$data=$this->pegadatacontrolecaixa()) return;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();

        $sql="SELECT aberto, fechado FROM controlecaixa WHERE dataaberto<='$data' AND datafechado>='$data' AND codcadcaixa=$codcadcaixa";
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        $fechado=$resultado2['fechado'];
        $aberto=$resultado2['aberto'];
        $this->checkbutton_aberto->set_active($aberto);
        $this->checkbutton_fechado->set_active($fechado);
        $con->Disconnect();
    }

    function botaofechar() {
        global $usuario;
        if(!$data=$this->pegadatacontrolecaixa()) return;
        if(!$codcadcaixa=$this->pegacodcadcaixacontrolecaixa()) return;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();

        $sql="SELECT data, saldo FROM caixa WHERE origem=$codcadcaixa ORDER BY data DESC LIMIT 1";
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        $ultima_data=$resultado2['data'];
        $ultimo_saldo=$resultado2['saldo'];

        if(!empty($ultima_data)) {
            if($data<$ultima_data) {
                msg("Existem lancamentos posteriores a esta data. Voce devera fechar o caixa mais tarde ou com uma data maior");
                return;
            }
        }

        $sql="SELECT aberto, fechado FROM controlecaixa WHERE dataaberto<='$data' AND datafechado>='$data' AND codcadcaixa=$codcadcaixa";
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        $fechado=$resultado2['fechado'];
        $aberto=$resultado2['aberto'];
        $hora=date("H:i:s");

        if($fechado) {
            msg("Este Caixa ja foi aberto e esta fechado.");
        }elseif($aberto) {
            $sql="UPDATE controlecaixa SET fechado='1', datafechado='$data' WHERE dataaberto<='$data' AND datafechado>='$data' AND codcadcaixa='$codcadcaixa' ;";
            $sql.="INSERT INTO caixa (data, hora, historico, origem, saldo) VALUES ('$data', '$hora', 'FECHAMENTO DO CAIXA USUARIO $usuario', $codcadcaixa, $ultimo_saldo)";
            if($con->Query($sql)) {
                msg('Este Caixa foi fechado com sucesso!!!');
            }else {
                msg('Erro SQL ao fechar Caixa');
            }
        }elseif(!$fechado AND !$aberto) {
            msg('Este Caixa nao foi aberto!');
        }else {
            msg('Nao foi possivel fechar/abrir este caixa!');
        }
        $con->Disconnect();
    }
    function pegadatacontrolecaixa() {
        $data=$this->entry_data->get_text();
        if($this->valida_data($data)) {
            $data=$this->corrigeNumero($data,"dataiso");
        }else {
            msg("Data incorreta!");
            $this->entry_data->grab_focus();
            return false;
        }
        return $data;

    }

    function pegacodcadcaixacontrolecaixa() {
        $codcadcaixa=$this->DeixaSoNumero($this->entry_caixa->get_text());
        if (empty($codcadcaixa)) {
            msg('Codigo do caixa nao encontrado');
            $this->entry_caixa->grab_focus();
            return false;
        }
        if (!$this->retornabusca2('cadcaixa', $this->entry_caixa, false, 'codigo', 'descricao')) {
            msg('Codigo do caixa nao encontrado');
            $this->entry_caixa->grab_focus();
            return false;
        }
        return $codcadcaixa;

    }
    function botaoabrir() {
        global $usuario;
        if(!$codcadcaixa=$this->pegacodcadcaixacontrolecaixa()) return;
        if(!$data=$this->pegadatacontrolecaixa()) return;

        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;
        $con->Connect();
        $sql="SELECT aberto, fechado FROM controlecaixa WHERE dataaberto<='$data' AND datafechado>='$data' AND codcadcaixa=$codcadcaixa";
        $resultado=$con->Query($sql);
        $resultado2=$con->FetchArray($resultado);
        $fechado=$resultado2['fechado'];
        $aberto=$resultado2['aberto'];
        $hora=date("H:i:s");

        if($fechado) {
            msg("Este Caixa ja foi aberto e esta fechado.");
        }elseif($aberto) {
            msg("Este Caixa ja esta aberto.");
        }else {
            $sql="INSERT INTO controlecaixa (dataaberto, datafechado, aberto, fechado, codcadcaixa) VALUES ('$data', '9999-12-31', '1', '0', '$codcadcaixa');";
            $sql.="INSERT INTO caixa (data, hora, valor, saldo, historico, origem) VALUES ('$data', '$hora', 0, 0, 'ABERTURA DO CAIXA USUARIO $usuario', $codcadcaixa)";
            if($con->Query($sql,true,null,true)) {
                msg('Este Caixa foi aberto com sucesso!!!');
                msg("Nao esqueca de efetuar o suprimento inicial de valores no caixa!");
            }else {
                msg('Erro SQL controlecaixa');
            }
        }
        $con->Disconnect();

    }
    function cancelar() {
        //$this->janela->hide();
        $this->janela->destroy();
        $this->janela="";
    }

}
?>