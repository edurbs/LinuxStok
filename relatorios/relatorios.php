<?php

class relatorios extends funcoes {

	function relatorios(){
        $this->datadehoje=date('d',time())."-".date('m',time())."-".date('Y',time());
		
        //$this->xml=$this->carregaGlade('relatorios',false,false,false);
        $this->xml=$this->carregaGlade('relatorios');
		
		$this->janela = $this->xml->get_widget('window1');
		//$this->janela->hide();
		
		//$this->vbox_relatorios = $this->xml->get_widget('vbox1');
		
		$this->frame_codigo1 = $this->xml->get_widget('frame_codigo1');
		$this->frame_codigo1->hide();		
		$this->labelframe_codigo1 = $this->xml->get_widget('labelframe_codigo1');
		$this->entry_codigo1 = $this->xml->get_widget('entry_codigo1');
		$this->label_codigo1 = $this->xml->get_widget('label_codigo1');
		
		$this->frame_codigo2 = $this->xml->get_widget('frame_codigo2');
		$this->frame_codigo2->hide();
		$this->labelframe_codigo2 = $this->xml->get_widget('labelframe_codigo2');
		$this->entry_codigo2 = $this->xml->get_widget('entry_codigo2');
		$this->label_codigo2 = $this->xml->get_widget('label_codigo2');
		
		$this->frame_codigo3 = $this->xml->get_widget('frame_codigo3');
		$this->frame_codigo3->hide();
		$this->labelframe_codigo3 = $this->xml->get_widget('labelframe_codigo3');
		$this->entry_codigo3 = $this->xml->get_widget('entry_codigo3');
		$this->label_codigo3 = $this->xml->get_widget('label_codigo3');
		
		$this->hbox_data= $this->xml->get_widget('hbox_data');
		//$this->hbox_data->hide();
		
		$this->frame_data1= $this->xml->get_widget('frame_data1');
		$this->frame_data1->hide();
		$this->label_data1= $this->xml->get_widget('label_data1');
		$this->entry_data1= $this->xml->get_widget('entry_data1');
		$this->entry_data1->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_data1->set_text($this->datadehoje);
		//$this->entry_data1->connect_simple('focus-in-event',array($this,'calendar1_show'));
		//$this->entry_data1->connect_simple('focus-out-event',array($this,'calendar1_hide'));
		
		
		$this->frame_data2= $this->xml->get_widget('frame_data2');
		$this->frame_data2->hide();
		$this->label_data2= $this->xml->get_widget('label_data2');
		$this->entry_data2= $this->xml->get_widget('entry_data2');
		$this->entry_data2->connect('key-press-event', array($this,'mascaraNew'),'**-**-****');
        $this->entry_data2->set_text($this->datadehoje);
		
		$this->hbox_radiobutton= $this->xml->get_widget('hbox_radiobutton');
		//$this->hbox_radiobutton->hide();
		$this->radiobutton1= $this->xml->get_widget('radiobutton1');
		$this->radiobutton1->hide();
		$this->radiobutton2= $this->xml->get_widget('radiobutton2');
		$this->radiobutton2->hide();
		$this->radiobutton3= $this->xml->get_widget('radiobutton3');
		$this->radiobutton3->hide();
		$this->radiobutton4= $this->xml->get_widget('radiobutton4');
		$this->radiobutton4->hide();
		$this->radiobutton5= $this->xml->get_widget('radiobutton5');
		$this->radiobutton5->hide();
		$this->radiobutton6= $this->xml->get_widget('radiobutton6');
		$this->radiobutton6->hide();
		
		$this->hbox_checkbutton= $this->xml->get_widget('hbox_checkbutton');
		//$this->hbox_checkbutton->hide();
		$this->checkbutton1= $this->xml->get_widget('checkbutton1');
		$this->checkbutton1->hide();
		$this->checkbutton2= $this->xml->get_widget('checkbutton2');
		$this->checkbutton2->hide();
		$this->checkbutton3= $this->xml->get_widget('checkbutton3');
		$this->checkbutton3->hide();
		$this->checkbutton4= $this->xml->get_widget('checkbutton4');
		$this->checkbutton4->hide();
		$this->checkbutton5= $this->xml->get_widget('checkbutton5');
		$this->checkbutton5->hide();
		$this->checkbutton6= $this->xml->get_widget('checkbutton6');
		$this->checkbutton6->hide();
		
		$this->hbox_combo= $this->xml->get_widget('hbox_combo');
		//$this->hbox_combo->hide();
		
		$this->label_combo1 = new GtkLabel("label_combo1");
		$this->hbox_combo->pack_start($this->label_combo1,false,false);
		$this->label_combo1->hide();		
		$this->combo1 = GtkComboBox::new_text();
		$this->hbox_combo->pack_start($this->combo1,false,false);
		$this->combo1->hide();
		
		$this->label_combo2 = new GtkLabel("label_combo1");
		$this->hbox_combo->pack_start($this->label_combo2,false,false);
		$this->label_combo2->hide();
		$this->combo2 = GtkComboBox::new_text();
		$this->hbox_combo->pack_start($this->combo2,false,false);
		$this->combo2->hide();
		
		$this->hbox_button= $this->xml->get_widget('hbox_button');
		
		$this->button_tela= $this->xml->get_widget('button_tela');
		$this->button_tela->hide();
		
		$this->button_html= $this->xml->get_widget('button_html');
		$this->button_html->hide();
		//$this->button_html->connect_simple_after('clicked',array($this,'geraHTML'),$this->reltitulo, $this->relcabeca, $this->relcabtabela, $this->relcorpo, $this->relpe, true);
		
		$this->button_texto= $this->xml->get_widget('button_texto');
		$this->button_texto->hide();
		//$this->button_texto->connect_simple_after('clicked',array($this,'geraTEXTO'),$this->reltitulo, $this->relcabeca, $this->relcabtabela, $this->relcorpo, $this->relpe, true, 80);
		
		$this->button_fechar= $this->xml->get_widget('button_fechar');
		$this->button_fechar->connect_simple('clicked',array($this,'fecha_janela'));
    }
	
	function calendar1_show(){
		$this->window_calendar1=new GtkWindow();
		$vbox=new GtkVBox();
		$this->calendar1=new GtkCalendar();
		$vbox->add($this->calendar1);
		$this->window_calendar1->add($vbox);
		$this->window_calendar1->show_all();
	}
	function calendar1_hide(){
		$this->calendar1->hide();
	}
}

?>