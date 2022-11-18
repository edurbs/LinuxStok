<?php
class menugeral extends funcoes {

	function sistema_sair(){
		if(!empty($this->ultima_janela)){
            $this->fecha_janela();
        }
        $this->menu_principal->hide();
        //new login;
        //GTK::main_quit();
        exit;        
        	
	}
	
    function menugeral(){
        global $ultima_janela,$parente;

        $this->xml=$this->carregaGlade("menugeral",true,false,false,false,false);
        //$this->xml=$this->carregaGlade("menugeral",false,false,false,false);
        $this->menu_principal = $this->xml->get_widget('window1');
        $this->menu_principal->maximize();
		$this->menu_principal->set_icon_from_file('tema'.bar.'icone.png');
		
		$this->vbox_menu = $this->xml->get_widget('vbox1');		
		$this->notebook_menugeral=new GtkNotebook();
		$this->notebook_menugeral->set_scrollable(true);
		$this->notebook_menugeral->popup_enable();
		$this->vbox_menu->pack_start($this->notebook_menugeral);
		
        $parente=$this->menu_principal;
        $this->menu_principal->connect_simple('destroy', array($this,'sistema_sair'));
        //$this->menu_principal->set_size_request(retorna_CONFIG("largura"),-1);
        $this->menu_principal->set_uposition(1,1);
		
		$this->menu_fechar_aba_notebook = $this->xml->get_widget('menu_fechar_aba_notebook');
        $this->menu_fechar_aba_notebook->connect_simple('activate', array($this,'fechar_aba_notebook'));
        $this->button_fechar_aba_notebook = $this->xml->get_widget('button_fechar_aba_notebook');
        $this->button_fechar_aba_notebook->connect_simple('clicked', array($this,'fechar_aba_notebook'));
		
        $this->menu_sistema_sair = $this->xml->get_widget('menu_sistema_sair');
        $this->menu_sistema_sair->connect_simple('activate', array($this,'sistema_sair'));
        
        $this->menu_sistema_backup_gravar = $this->xml->get_widget('menu_sistema_backup_gravar');
        $this->menu_sistema_backup_gravar->connect_simple('activate', array($this,'chamaCadastro'), '', 'funcoes','backup','gravar');
        
        $this->menu_sistema_backup_restaurar = $this->xml->get_widget('menu_sistema_backup_restaurar');
        $this->menu_sistema_backup_restaurar->connect_simple('activate', array($this,'chamaCadastro'), '', 'funcoes','backup','restaurar');

        $this->menu_sistema_sobre = $this->xml->get_widget('menu_sistema_sobre');
        $this->menu_sistema_sobre->connect_simple('activate', array($this,'menu_sistema_sobre'));
        
        $this->menu_sistema_calculadora = $this->xml->get_widget('menu_sistema_calculadora');
        $this->menu_sistema_calculadora->connect_simple('activate', array($this,'menu_sistema_calculadora'));

        $this->menu_importa_ap2005= $this->xml->get_widget('menu_importa_ap2005');
        $this->menu_importa_ap2005->connect_simple('activate', 'confirma', array($this,'chamaCadastro'), 'CUIDADO!! Esta opcao pode demorar MUITO tempo e vai modificar sua base de dados!! Deseja realmente fazer a importacao?', '050501', 'modulos','importaap2005','importa_ap2005');
        $this->menu_importa_ap2005->set_sensitive($this->verificaPermissao('050501',false));
		/*
        $this->menu_importaPadroes= $this->xml->get_widget('menu_importaPadroes');
        $this->menu_importaPadroes->connect_simple('activate', 'confirma', array($this,'importaPadroes'), 'CUIDADO!! Esta opcao vai modificar sua base de dados!! Deseja realmente fazer a importacao?');
        $this->menu_importaPadroes->set_sensitive($this->verificaPermissao('050301',false));

        $this->menu_importaExtras= $this->xml->get_widget('menu_importaExtras');
        $this->menu_importaExtras->connect_simple('activate', 'confirma', array($this,'importaExtras'), 'CUIDADO!! Esta opcaoo vai modificar sua base de dados e pode DEMORAR muito tempo!! Deseja realmente fazer a importacao?');
        $this->menu_importaExtras->set_sensitive($this->verificaPermissao('050401',false));
*/
        $this->menu_sistema_logoff = $this->xml->get_widget('menu_sistema_logoff');
        $this->menu_sistema_logoff->connect_simple('activate', array($this,'logoff'));

        $this->menu_configuracoes_locais= $this->xml->get_widget('menu_configuracoes_locais');
        $this->menu_configuracoes_locais->connect_simple('activate', array($this,'chamaCadastro'), '050101', 'modulos','configuracoes','configuracoes', 'Configuracoes Locais');
        $this->menu_configuracoes_locais->set_sensitive($this->verificaPermissao('050101',false));

        $this->menu_configuracoes_gerais= $this->xml->get_widget('menu_configuracoes_gerais');
        $this->menu_configuracoes_gerais->connect_simple('activate', array(&$this,'chamaCadastro'), '050201', 'modulos','opcoes','opcoes','true');
        $this->menu_configuracoes_gerais->set_sensitive($this->verificaPermissao('050201',false));

        $this->menu_senhas= $this->xml->get_widget('menu_senhas');
        $this->menu_senhas->connect_simple('activate', array(&$this,'chamaCadastro'), '060301', 'cadastros','nivel2funcionario','nivel2funcionario', "Senhas");
        $this->menu_senhas->set_sensitive($this->verificaPermissao('060301',false));

        $this->menu_permissoes= $this->xml->get_widget('menu_permissoes');
        $this->menu_permissoes->connect_simple('activate', array($this,'chamaCadastro'), '060201', 'cadastros','permissoes','permissoes',"Cadastro de Permissoes dos Niveis de Acesso");
        $this->menu_permissoes->set_sensitive($this->verificaPermissao('060201',false));

        $this->menu_cadastro_clientes = $this->xml->get_widget('menu_cadastro_clientes');
        $this->menu_cadastro_clientes->connect_simple('activate', array(&$this,'chamaCadastro'), '010101', 'cadastros','geral','clientes','Clientes');
        $this->button_cadastro_clientes = $this->xml->get_widget('button_cadastro_clientes');
        $this->button_cadastro_clientes->connect_simple('clicked', array(&$this,'chamaCadastro'), '010101','cadastros','geral','clientes','Clientes');
        $this->menu_cadastro_clientes->set_sensitive($this->verificaPermissao('010101',false));
        $this->button_cadastro_clientes->set_sensitive($this->verificaPermissao('010101',false));

        $this->menu_cadastro_funcionarios= $this->xml->get_widget('menu_cadastro_funcionarios');
        $this->menu_cadastro_funcionarios->connect_simple('activate', array(&$this,'chamaCadastro'), '010201', 'cadastros','geral','funcionarios','Funcionarios');
        $this->button_cadastro_funcionarios= $this->xml->get_widget('button_cadastro_funcionarios');
        $this->button_cadastro_funcionarios->connect_simple('clicked', array(&$this,'chamaCadastro'),  '010201', 'cadastros','geral','funcionarios','Funcionarios');
        $this->menu_cadastro_funcionarios->set_sensitive($this->verificaPermissao('010201',false));
        $this->button_cadastro_funcionarios->set_sensitive($this->verificaPermissao('010201',false));

        $this->menu_cadastro_fornecedores = $this->xml->get_widget('menu_cadastro_fornecedores');
        $this->menu_cadastro_fornecedores->connect_simple('activate', array(&$this,'chamaCadastro'), '010301',  'cadastros','geral','fornecedores','Fornecedores');
        $this->button_cadastro_fornecedores = $this->xml->get_widget('button_cadastro_fornecedores');
        $this->button_cadastro_fornecedores->connect_simple('clicked', array(&$this,'chamaCadastro'), '010301',  'cadastros','geral','fornecedores','Fornecedores');
        $this->menu_cadastro_fornecedores->set_sensitive($this->verificaPermissao('010301',false));
        $this->button_cadastro_fornecedores->set_sensitive($this->verificaPermissao('010301',false));

        $this->menu_cadastro_empregador = $this->xml->get_widget('menu_cadastro_empregador');
        $this->menu_cadastro_empregador->connect_simple('activate', array(&$this,'chamaCadastro'), '010601',  'cadastros','geral','empregador','Empregadores');
        $this->menu_cadastro_empregador->set_sensitive($this->verificaPermissao('010601',false));

		$this->menu_cadastro_veiculos = $this->xml->get_widget('menu_cadastro_veiculos');
        $this->menu_cadastro_veiculos->connect_simple('activate', array(&$this,'chamaCadastro'), '', 'cadastros','veiculos','veiculos','Veiculos');
        $this->menu_cadastro_veiculos->set_sensitive($this->verificaPermissao('011001',false));

        $this->menu_cadastro_profissao = $this->xml->get_widget('menu_cadastro_profissao');
        $this->menu_cadastro_profissao->connect_simple('activate', array(&$this,'chamaCadastro'), '020201',  'tabelas','tabelas','profissao',"Profissoes");
        $this->menu_cadastro_profissao->set_sensitive($this->verificaPermissao('020201',false));

        $this->menu_tabelas_parentesco= $this->xml->get_widget('menu_tabelas_parentesco');
        $this->menu_tabelas_parentesco->connect_simple('activate', array(&$this,'chamaCadastro'), '020801',  'tabelas','tabelas','parentesco',"Parentesco");
        $this->menu_tabelas_parentesco->set_sensitive($this->verificaPermissao('020801',false));

        $this->midias_de_propaganda= $this->xml->get_widget('midias_de_propaganda');
        $this->midias_de_propaganda->connect_simple('activate', array(&$this,'chamaCadastro'), '020901',  'tabelas','tabelas','midiapropaganda',"Midias de Propaganda");
        $this->midias_de_propaganda->set_sensitive($this->verificaPermissao('020901',false));

		$this->cadastro_ocorrencias_tipos = $this->xml->get_widget('cadastro_ocorrencias_tipos');
        $this->cadastro_ocorrencias_tipos->connect_simple('activate', array($this,'chamaCadastro'), '',  'tabelas','tabelas','ocorrencia_tipo',"Tipo Ocorrencias");
        $this->cadastro_ocorrencias_tipos->set_sensitive($this->verificaPermissao('',false));
        
        $this->cadastro_ocorrencias_cadastro = $this->xml->get_widget('cadastro_ocorrencias_cadastro');
        $this->cadastro_ocorrencias_cadastro->connect_simple('activate', array($this,'chamaCadastro'), '',  'cadastros','ocorrencias', null, "Ocorrencias");
        $this->cadastro_ocorrencias_cadastro->set_sensitive($this->verificaPermissao('',false));

        $this->menu_tabelas_nomebanco= $this->xml->get_widget('menu_tabelas_nomebanco');
        $this->menu_tabelas_nomebanco->connect_simple('activate', array(&$this,'chamaCadastro'), '020601',  'tabelas','nomebanco','nomebanco',"Inst.Bancarias");
        $this->menu_tabelas_nomebanco->set_sensitive($this->verificaPermissao('020601',false));

        $this->menu_tabelas_romaneio= $this->xml->get_widget('menu_tabelas_romaneio');
        $this->menu_tabelas_romaneio->connect_simple('activate', array(&$this,'chamaCadastro'), '020701',  'tabelas', 'tabelas','romaneio',"Romaneio");
        $this->menu_tabelas_romaneio->set_sensitive($this->verificaPermissao('020701',false));

        $this->menu_cadastro_placon = $this->xml->get_widget('menu_cadastro_placon');
        $this->menu_cadastro_placon->connect_simple('activate', array(&$this,'chamaCadastro'), '020301',  'tabelas','tabelas','placon',"Planos de Contas",true);
        $this->menu_cadastro_placon->set_sensitive($this->verificaPermissao('020301',false));

        $this->menu_cadastro_bancos = $this->xml->get_widget('menu_cadastro_bancos');
        $this->menu_cadastro_bancos->connect_simple('activate', array(&$this,'chamaCadastro'), '010501',  'cadastros','bancos','bancos',"Bancos");
        //$this->button_cadastro_bancos = $this->xml->get_widget('button_cadastro_bancos');
        //$this->button_cadastro_bancos->connect_simple('clicked', array(&$this,'chamaCadastro'), '010501',  'cadastros','bancos','bancos',"Cadastro de Bancos");
        $this->menu_cadastro_bancos->set_sensitive($this->verificaPermissao('010501',false));
        //$this->button_cadastro_bancos->set_sensitive($this->verificaPermissao('010501',false));
        /*
        $this->menu_cadastro_veiculos = $this->xml->get_widget('menu_cadastro_veiculos');
        $this->menu_cadastro_veiculos->connect_simple('activate', array(&$this,'chamaCadastro'), '011001',  'cadastros','carros','carros',"Cadastro de Veiculos");
        $this->menu_cadastro_veiculos->set_sensitive($this->verificaPermissao('011001',false));

        $this->menu_cadastro_devedores = $this->xml->get_widget('menu_cadastro_devedores');
        $this->menu_cadastro_devedores->connect_simple('activate', array(&$this,'chamaCadastro'), '070101',  'cadastros','geral','devedores','Cadastro de Devedores');
        $this->button_cadastro_devedores = $this->xml->get_widget('button_cadastro_devedores');
        $this->button_cadastro_devedores->connect_simple('clicked', array(&$this,'chamaCadastro'), '070101',  'cadastros','geral','devedores','Cadastro de Devedores');
        $this->menu_cadastro_devedores->set_sensitive($this->verificaPermissao('070101',false));
        $this->button_cadastro_devedores->set_sensitive($this->verificaPermissao('070101',false));
        */
        $this->menu_cadastro_formapgto = $this->xml->get_widget('menu_cadastro_formapgto');
        $this->menu_cadastro_formapgto->connect_simple('activate', array(&$this,'chamaCadastro'), '010901',  'cadastros','formapgto','formapgto',"Formas de Pagamento");
        $this->menu_cadastro_formapgto->set_sensitive($this->verificaPermissao('010901',false));

        $this->menu_cadastro_nivelacesso= $this->xml->get_widget('menu_cadastro_nivelacesso');
        $this->menu_cadastro_nivelacesso->connect_simple('activate', array(&$this,'chamaCadastro'), '060101',  'tabelas','tabelas','nivelacesso',"Niveis de Acesso");
        $this->menu_cadastro_nivelacesso->set_sensitive($this->verificaPermissao('060101',false));

        $this->menu_cadastro_parcelapgto = $this->xml->get_widget('menu_cadastro_parcelapgto');
        $this->menu_cadastro_parcelapgto->connect_simple('activate', array(&$this,'chamaCadastro'), '010901',  'cadastros','parcelapgto','parcelapgto',"Parcelas de Pagamento");
        $this->menu_cadastro_parcelapgto->set_sensitive($this->verificaPermissao('010901',false));
		/*
        $this->menu_cobranca_titulos = $this->xml->get_widget('menu_cobranca_titulos');
        $this->menu_cobranca_titulos->connect_simple('activate', array(&$this,'chamaCadastro'), '070201',  'cadastros','tituloscobranca','tituloscobranca',"Titulos de Cobranca");
        $this->button_cobranca_titulos = $this->xml->get_widget('button_cobranca_titulos');
        $this->button_cobranca_titulos->connect_simple('clicked', array(&$this,'chamaCadastro'), '070201',  'cadastros','tituloscobranca','tituloscobranca',"Titulos de Cobranca");
        $this->menu_cobranca_titulos->set_sensitive($this->verificaPermissao('070201',false));
        $this->button_cobranca_titulos->set_sensitive($this->verificaPermissao('070201',false));
		*/

        $this->menu_contas_pagar = $this->xml->get_widget('menu_contas_pagar');
        $this->menu_contas_pagar->connect_simple('activate', array(&$this,'chamaCadastro'), '030701',  'movimenta','contas','pagar','Pagar','fornecedores');
        $this->button_contas_pagar = $this->xml->get_widget('button_contas_pagar');
        $this->button_contas_pagar->connect_simple('clicked', array(&$this,'chamaCadastro'), '030701',  'movimenta','contas','pagar','Pagar','fornecedores');
        $this->menu_contas_pagar->set_sensitive($this->verificaPermissao('030701',false));
        $this->button_contas_pagar->set_sensitive($this->verificaPermissao('030701',false));

        $this->menu_contas_receber = $this->xml->get_widget('menu_contas_receber');
        $this->menu_contas_receber->connect_simple('activate', array(&$this,'chamaCadastro'), '030801',  'movimenta','contas','receber','Receber','clientes');
        $this->button_contas_receber = $this->xml->get_widget('button_contas_receber');
        $this->button_contas_receber->connect_simple('clicked', array(&$this,'chamaCadastro'), '030801',  'movimenta','contas','receber','Receber','clientes');
        $this->menu_contas_receber->set_sensitive($this->verificaPermissao('030801',false));
        $this->button_contas_receber->set_sensitive($this->verificaPermissao('030801',false));
        
        $this->menu_movcontas = $this->xml->get_widget('menu_movcontas');
        $this->menu_movcontas->connect_simple('activate', array($this,'chamaCadastro'), '030901',  'movimenta','movcontas', 'movcontas', 'Mov.Financeiro');
        $this->button_movcontas = $this->xml->get_widget('button_movcontas');
        $this->button_movcontas->connect_simple('clicked', array($this,'chamaCadastro'), '030901',  'movimenta','movcontas','movcontas','Mov.Financeiro');
        $this->menu_movcontas->set_sensitive($this->verificaPermissao('030901',false));
        $this->button_movcontas->set_sensitive($this->verificaPermissao('030901',false));
        
        //$this->menu_estoque_romaneio= $this->xml->get_widget('menu_estoque_romaneio');
        //$this->menu_estoque_romaneio->connect_simple('activate', array($this,'chamaCadastro'), '',  'movimenta','romaneio');
        //$this->menu_estoque_romaneio->set_sensitive($this->verificaPermissao('',false));
        //$this->menu_estoque_romaneio->set_sensitive(false);
        //$this->menu_estoque_romaneio->hide();
        
		$this->menu_cadastro_caixas= $this->xml->get_widget('menu_cadastro_caixas');
        $this->menu_cadastro_caixas->connect_simple('activate', array(&$this,'chamaCadastro'), '030407',  'tabelas','tabelas','cadcaixa',"Caixas");
        $this->menu_cadastro_caixas->set_sensitive($this->verificaPermissao('030407',false));
        
        $this->estoque_controleentregas= $this->xml->get_widget('estoque_controleentregas');
        $this->estoque_controleentregas->connect_simple('activate', array($this,'chamaCadastro'), '',  'movimenta','entrega');
        $this->estoque_controleentregas->set_sensitive($this->verificaPermissao('',false));

        /*$this->menu_contas_caixa= $this->xml->get_widget('menu_contas_caixa');
        $this->menu_contas_caixa->connect_simple('activate', array(&$this,'chamaCadastro'), '030401',  'movimenta','caixa','caixa','Caixa');
        $this->menu_contas_caixa->set_sensitive($this->verificaPermissao('030401',false));
        */

        $this->menu_caixa_abrir= $this->xml->get_widget('menu_caixa_abrir');
        $this->menu_caixa_abrir->connect_simple('activate', array(&$this,'chamaCadastro'), '030405',  'movimenta','controlecaixa','abrircontrolecaixa','Abrir Caixa');
        $this->menu_caixa_abrir->set_sensitive($this->verificaPermissao('030405',false));        

        $this->menu_caixa_fechar= $this->xml->get_widget('menu_caixa_fechar');
        $this->menu_caixa_fechar->connect_simple('activate', array(&$this,'chamaCadastro'), '030406',  'movimenta','controlecaixa','fecharcontrolecaixa','Fechar Caixa');
        $this->menu_caixa_fechar->set_sensitive($this->verificaPermissao('030406',false));
        
        $this->menu_caixa_sangria= $this->xml->get_widget('menu_caixa_sangria');
        $this->menu_caixa_sangria->connect_simple('activate', array(&$this,'chamaCadastro'), '',  'movimenta','sangria','sangria','Sangria do Caixa');
        $this->menu_caixa_sangria->set_sensitive($this->verificaPermissao('',false));
        
		$this->menu_caixa_suprimento= $this->xml->get_widget('menu_caixa_suprimento');
        $this->menu_caixa_suprimento->connect_simple('activate', array(&$this,'chamaCadastro'), '',  'movimenta','sangria','suprimento','Suprimento do Caixa');
        $this->menu_caixa_suprimento->set_sensitive($this->verificaPermissao('',false));

        /*$this->menu_contas_movbanc= $this->xml->get_widget('menu_contas_movbanc');
        $this->menu_contas_movbanc->connect_simple('activate', array(&$this,'chamaCadastro'), '030501',  'movimenta','caixa','movbanc','Movimento Bancario');        
        $this->menu_contas_movbanc->set_sensitive($this->verificaPermissao('030501',false));*/

        $this->menu_contas_cheques= $this->xml->get_widget('menu_contas_cheques');
        $this->menu_contas_cheques->connect_simple('activate', array(&$this,'chamaCadastro'), '030601',  'cadastros','cheques','cheques','Cheques');
        $this->button_contas_cheques= $this->xml->get_widget('button_contas_cheques');
        $this->button_contas_cheques->connect_simple('clicked', array(&$this,'chamaCadastro'), '030601',  'cadastros','cheques','cheques','Cheques');
        $this->menu_contas_cheques->set_sensitive($this->verificaPermissao('030601',false));
        $this->button_contas_cheques->set_sensitive($this->verificaPermissao('030601',false));
  
        $this->relatorio_compras_produtosfornecedores= $this->xml->get_widget('relatorio_compras_produtosfornecedores');
        $this->relatorio_compras_produtosfornecedores->connect_simple('activate', array(&$this,'chamaCadastro'), '040501',  'relatorios','relperiodo','produtosfornecedores','Relatorio de Compras de Produtos por Fornecedores');
        $this->relatorio_compras_produtosfornecedores->set_sensitive($this->verificaPermissao('040501',false));

        $this->relatorio_vendas_periodo= $this->xml->get_widget('relatorio_vendas_periodo');
        $this->relatorio_vendas_periodo->connect_simple('activate', array(&$this,'chamaCadastro'), '040101',  'relatorios','relperiodo','vendasperiodo','Relatorio de Vendas por Periodo');
        $this->relatorio_vendas_periodo->set_sensitive($this->verificaPermissao('040101',false));
        
        $this->relatorio_vendas_contasgeradas= $this->xml->get_widget('relatorio_vendas_contasgeradas');
        $this->relatorio_vendas_contasgeradas->connect_simple('activate', array(&$this,'chamaCadastro'), '040107',  'relatorios','vendas_contasgeradas');
        $this->relatorio_vendas_contasgeradas->set_sensitive($this->verificaPermissao('040107',false));

        $this->relatorio_vendas_periodomeiopgto= $this->xml->get_widget('relatorio_vendas_periodomeiopgto');
        $this->relatorio_vendas_periodomeiopgto->connect_simple('activate', array(&$this,'chamaCadastro'), '040102',  'relatorios', 'totaisdosmeiosdepgto');
        $this->relatorio_vendas_periodomeiopgto->set_sensitive($this->verificaPermissao('040102',false));
		
		$this->menu_relatorios_devolucoes_totaisdocliente= $this->xml->get_widget('menu_relatorios_devolucoes_totaisdocliente');
        $this->menu_relatorios_devolucoes_totaisdocliente->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios', 'devolucoestotaisdocliente');
        $this->menu_relatorios_devolucoes_totaisdocliente->set_sensitive($this->verificaPermissao('',false));
		
		$this->menu_relatorios_devolucoes_detalhadasdocliente= $this->xml->get_widget('menu_relatorios_devolucoes_detalhadasdocliente');
        $this->menu_relatorios_devolucoes_detalhadasdocliente->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios', 'devolucoesdetalhadasdocliente');
        $this->menu_relatorios_devolucoes_detalhadasdocliente->set_sensitive($this->verificaPermissao('',false));
		
		$this->rel_sql_personalizado=$this->xml->get_widget('rel_sql_personalizado');
		$this->rel_sql_personalizado->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios', 'relsql', 'rel_sql_personalizado', 'Relatorio Personalizado');
		$this->rel_sql_personalizado->set_sensitive($this->verificaPermissao('',false));
		
		$this->relatorio_vendas_vendedor_cliente=$this->xml->get_widget('relatorio_vendas_vendedor_cliente');
		$this->relatorio_vendas_vendedor_cliente->connect_simple('activate', array($this,'chamaCadastro'), '040105',  'relatorios', 'vendas_vendedor_cliente');
		$this->relatorio_vendas_vendedor_cliente->set_sensitive($this->verificaPermissao('040105',false));
        
        $this->relatorio_vendas_vendedor= $this->xml->get_widget('relatorio_vendas_vendedor');
        $this->relatorio_vendas_vendedor->connect_simple('activate', array($this,'chamaCadastro'), '040104',  'relatorios','vendas_vendedor');
        $this->relatorio_vendas_vendedor->set_sensitive($this->verificaPermissao('040104',false));
        
        $this->rel_orcamento_abertos=$this->xml->get_widget('rel_orcamento_abertos');
        $this->rel_orcamento_abertos->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','relperiodo','rel_orcamento_abertos','Relatorio de Orcamentos Abertos', 'abertos');
        $this->rel_orcamento_abertos->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_orcamento_fechados=$this->xml->get_widget('rel_orcamento_fechados');
        $this->rel_orcamento_fechados->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','relperiodo','rel_orcamento_abertos','Relatorio de Orcamentos Fechados', 'fechados');
		$this->rel_orcamento_fechados->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_orcamento_todos=$this->xml->get_widget('rel_orcamento_todos');
        $this->rel_orcamento_todos->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','relperiodo','rel_orcamento_abertos','Relatorio de Todos Orcamentos', 'todos');
		$this->rel_orcamento_fechados->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_abaixo_do_estoque_minimo1= $this->xml->get_widget('rel_abaixo_do_estoque_minimo1');
        $this->rel_abaixo_do_estoque_minimo1->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','relmerc','rel_abaixo_do_estoque_minimo1');
        $this->rel_abaixo_do_estoque_minimo1->set_sensitive($this->verificaPermissao('040702',false));
        
        $this->rel_lista_de_preco_simples= $this->xml->get_widget('rel_lista_de_preco_simples');
        $this->rel_lista_de_preco_simples->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','relmerc','lista_preco_simples');
        $this->rel_lista_de_preco_simples->set_sensitive($this->verificaPermissao('040701',false));
        
        $this->rel_com_preco_promocional1= $this->xml->get_widget('rel_com_preco_promocional1');
        $this->rel_com_preco_promocional1->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','relmerc','rel_com_preco_promocional1');
        $this->rel_com_preco_promocional1->set_sensitive($this->verificaPermissao('040703',false));
        
        $this->relatorio_contasdebanco_periodo= $this->xml->get_widget('relatorio_contasdebanco_periodo');
        $this->relatorio_contasdebanco_periodo->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','movbanc_periodo');
        $this->relatorio_contasdebanco_periodo->set_sensitive($this->verificaPermissao('',false));
 
        $this->relatorio_caixa= $this->xml->get_widget('relatorio_caixa');
        $this->relatorio_caixa->connect_simple('activate', array($this,'chamaCadastro'), '040601', 'relatorios','caixa_periodo');
        $this->relatorio_caixa->set_sensitive($this->verificaPermissao('040601',false));
        
        $this->relatorio_caixa_recebimento_vendas= $this->xml->get_widget('relatorio_caixa_recebimento_vendas');
        $this->relatorio_caixa_recebimento_vendas->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','caixa_recebimento_vendas');
        $this->relatorio_caixa_recebimento_vendas->set_sensitive($this->verificaPermissao('',false));
        
        $this->relatorio_clientes_inativos= $this->xml->get_widget('relatorio_clientes_inativos');
        $this->relatorio_clientes_inativos->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','clientes_inativos');
        $this->relatorio_clientes_inativos->set_sensitive($this->verificaPermissao('',false));
        
        $this->relatorio_clientes_ocorrencias= $this->xml->get_widget('relatorio_clientes_ocorrencias');
        $this->relatorio_clientes_ocorrencias->connect_simple('activate', array($this,'chamaCadastro'), '',  'relatorios','clientes_ocorrencias');
        $this->relatorio_clientes_ocorrencias->set_sensitive($this->verificaPermissao('',false));

        $this->menu_cadastro_mercadorias = $this->xml->get_widget('menu_cadastro_mercadorias');
        $this->menu_cadastro_mercadorias->connect_simple('activate', array(&$this,'chamaCadastro'), '010801',  'cadastros','mercadorias','mercadorias','Cadastro de Mercadorias');
        $this->button_cadastro_mercadorias = $this->xml->get_widget('button_cadastro_mercadorias');
        $this->button_cadastro_mercadorias->connect_simple('clicked', array(&$this,'chamaCadastro'), '010801',  'cadastros','mercadorias','mercadorias','Cadastro de Mercadorias');
        $this->menu_cadastro_mercadorias->set_sensitive($this->verificaPermissao('010801',false));
        $this->button_cadastro_mercadorias->set_sensitive($this->verificaPermissao('010801',false));

        $this->menu_cadastro_localarma = $this->xml->get_widget('menu_cadastro_localarma');
        $this->menu_cadastro_localarma->connect_simple('activate', array(&$this,'chamaCadastro'), '020501',  'tabelas','tabelas','localarma',"Cadastro de Local de Armazenamento");
        $this->menu_cadastro_localarma->set_sensitive($this->verificaPermissao('020501',false));

        $this->menu_cadastro_grpmerc = $this->xml->get_widget('menu_cadastro_grpmerc');
        $this->menu_cadastro_grpmerc->connect_simple('activate', array(&$this,'chamaCadastro'), '020401',  'tabelas','tabelas','grpmerc',"Cadastro de Grupo de Mercadorias");
        $this->menu_cadastro_grpmerc->set_sensitive($this->verificaPermissao('020401',false));

        $this->menu_cadastro_fabricantes = $this->xml->get_widget('menu_cadastro_fabricantes');
        $this->menu_cadastro_fabricantes->connect_simple('activate', array(&$this,'chamaCadastro'), '010401',  'cadastros','geral','fabricantes','Cadastro de Fabricantes');
        $this->button_cadastro_fabricantes = $this->xml->get_widget('button_cadastro_fabricantes');
        $this->button_cadastro_fabricantes->connect_simple('clicked', array(&$this,'chamaCadastro'), '010401',  'cadastros','geral','fabricantes','Cadastro de Fabricantes');
        $this->menu_cadastro_fabricantes->set_sensitive($this->verificaPermissao('010401',false));
        $this->button_cadastro_fabricantes->set_sensitive($this->verificaPermissao('010401',false));

        $this->menu_movimentacoes_venda = $this->xml->get_widget('menu_movimentacoes_venda');
        //$this->menu_movimentacoes_venda->connect_simple('activate', array(&$this,'chamaCadastro'), '030101',  'movimenta','pdv','janelapdv');
        $this->menu_movimentacoes_venda->connect_simple('activate', array($this,'chamaCadastro'), '030101',  'movimenta','pdv', null, "Vendas");
        $this->button_movimentacoes_venda = $this->xml->get_widget('button_movimentacoes_venda');
        //$this->button_movimentacoes_venda->connect_simple('clicked', array(&$this,'chamaCadastro'), '030101',  'movimenta','pdv','janelapdv');
        $this->button_movimentacoes_venda->connect_simple('clicked', array($this,'chamaCadastro'), '030101',  'movimenta','pdv', null, "Vendas");
        $this->menu_movimentacoes_venda->set_sensitive($this->verificaPermissao('030101',false));
        $this->button_movimentacoes_venda->set_sensitive($this->verificaPermissao('030101',false));

        $this->menu_devolucao= $this->xml->get_widget('menu_devolucao');
        $this->menu_devolucao->connect_simple('activate', array(&$this,'chamaCadastro'), '030301',  'movimenta','devolucao','janeladevolucao','Devolucao');
        $this->button_devolucao= $this->xml->get_widget('button_devolucao');
        $this->button_devolucao->connect_simple('clicked', array(&$this,'chamaCadastro'), '030301',  'movimenta','devolucao','janeladevolucao','Devolucao');
        $this->menu_devolucao->set_sensitive($this->verificaPermissao('030301',false));
        $this->button_devolucao->set_sensitive($this->verificaPermissao('030301',false));

        $this->menu_movimentacoes_compras = $this->xml->get_widget('menu_movimentacoes_compras');
        $this->menu_movimentacoes_compras->connect_simple('activate', array(&$this,'chamaCadastro'), '030201',  'movimenta','compras','janelacompras', 'Compras');
        $this->button_movimentacoes_compras = $this->xml->get_widget('button_movimentacoes_compras');
        $this->button_movimentacoes_compras->connect_simple('clicked', array(&$this,'chamaCadastro'), '030201',  'movimenta','compras','janelacompras', 'Compras');
        $this->menu_movimentacoes_compras->set_sensitive($this->verificaPermissao('030201',false));
        $this->button_movimentacoes_compras->set_sensitive($this->verificaPermissao('030201',false));

/*
        $this->relorcamento= $this->xml->get_widget('relorcamento');
        $this->relorcamento->connect_simple('activate', array(&$this,'chamaCadastro'), '040301',  'relatorios','relorcamento','alguma','Orï¿½mentos nao concretizados');
        $this->relorcamento->set_sensitive($this->verificaPermissao('040301',false));
*/
        $this->menu_tabela_meiopgto= $this->xml->get_widget('menu_tabela_meiopgto');
        $this->menu_tabela_meiopgto->connect_simple('activate', array(&$this,'chamaCadastro'), '020101',  'tabelas','meiopgto','meiopgto',"Meios de Pagamento");
        $this->menu_tabela_meiopgto->set_sensitive($this->verificaPermissao('020101',false));

        $this->cadastro_cep_estados= $this->xml->get_widget('cadastro_cep_estados');
        $this->cadastro_cep_estados->connect_simple('activate', array(&$this,'chamaCadastro'), '080101',  'tabelas','tabelas','estados',"Cadastro de Estado",true);
        $this->cadastro_cep_estados->set_sensitive($this->verificaPermissao('080101',false));

        $this->cadastro_cep_localidades= $this->xml->get_widget('cadastro_cep_localidades');
        $this->cadastro_cep_localidades->connect_simple('activate', array(&$this,'chamaCadastro'), '080301',  'tabelas','cep_loc','cep_loc',"Cadastro de Localidades para CEP");
        $this->cadastro_cep_localidades->set_sensitive($this->verificaPermissao('080301',false));

        $this->cadastro_cep_bairros= $this->xml->get_widget('cadastro_cep_bairros');
        $this->cadastro_cep_bairros->connect_simple('activate', array(&$this,'chamaCadastro'), '080201',  'tabelas','cep_bai','cep_bai',"Cadastro de Bairros para CEP");
        $this->cadastro_cep_bairros->set_sensitive($this->verificaPermissao('080201',false));

        $this->cadastro_cep_enderecos= $this->xml->get_widget('cadastro_cep_enderecos');
        $this->cadastro_cep_enderecos->connect_simple('activate', array(&$this,'chamaCadastro'), '080401', 'tabelas', 'cep','cep', "Cadastro de Endereï¿½s para CEP");
        $this->cadastro_cep_enderecos->set_sensitive($this->verificaPermissao('080401',false));
        
        $this->trocaprecomerc= $this->xml->get_widget('trocaprecomerc');
        $this->trocaprecomerc->connect_simple('activate', array($this,'chamaCadastro'), '010705', 'movimenta', 'trocaprecomerc','trocaprecomerc', "Alteracao de Precos de Mercadorias");
        $this->trocaprecomerc->set_sensitive($this->verificaPermissao('010705',false));
        
        $this->rel_contasapagar= $this->xml->get_widget('rel_contasapagar');
        $this->rel_contasapagar->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relcontas','fornecedores', "Relatorio Contas a Pagar", "pagar", "contas");
        $this->rel_contasapagar->set_sensitive($this->verificaPermissao('',false));

		$this->rel_contasareceber= $this->xml->get_widget('rel_contasareceber');
        $this->rel_contasareceber->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relcontas','clientes', "Relatorio Contas a Receber", "receber", "contas");
        $this->rel_contasareceber->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_movimentocontasareceber= $this->xml->get_widget('rel_movimentocontasareceber');
        $this->rel_movimentocontasareceber->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relcontas','clientes', "Relatorio Mov. Contas a Receber", "receber","movimento");
        $this->rel_movimentocontasareceber->set_sensitive($this->verificaPermissao('',false));
        
		$this->rel_movimentocontasapagar= $this->xml->get_widget('rel_movimentocontasapagar');
        $this->rel_movimentocontasapagar->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relcontas','fornecedores', "Relatorio Mov. Contas a Pagar", "pagar","movimento");
        $this->rel_movimentocontasapagar->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_clientes_simples_com_endereco= $this->xml->get_widget('rel_clientes_simples_com_endereco');
        $this->rel_clientes_simples_com_endereco->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relgeral','simplescomendereco','clientes');
        $this->rel_clientes_simples_com_endereco->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_fornecedores_simples_com_endereco= $this->xml->get_widget('rel_fornecedores_simples_com_endereco');
        $this->rel_fornecedores_simples_com_endereco->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relgeral','simplescomendereco','fornecedores');
        $this->rel_fornecedores_simples_com_endereco->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_fabricantes_simples_com_endereco= $this->xml->get_widget('rel_fabricantes_simples_com_endereco');
        $this->rel_fabricantes_simples_com_endereco->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relgeral','simplescomendereco','fabricantes');
        $this->rel_fabricantes_simples_com_endereco->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_empregadores_simples_com_endereco= $this->xml->get_widget('rel_empregadores_simples_com_endereco');
        $this->rel_empregadores_simples_com_endereco->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relgeral','simplescomendereco','empregador');
        $this->rel_empregadores_simples_com_endereco->set_sensitive($this->verificaPermissao('',false));
        
        $this->rel_funcionarios_simples_com_endereco= $this->xml->get_widget('rel_funcionarios_simples_com_endereco');
        $this->rel_funcionarios_simples_com_endereco->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'relgeral','simplescomendereco','funcionarios');
        $this->rel_funcionarios_simples_com_endereco->set_sensitive($this->verificaPermissao('',false));
		
		$this->menu_relatorios_vendas_detalhadaporitem= $this->xml->get_widget('menu_relatorios_vendas_detalhadaporitem');
        $this->menu_relatorios_vendas_detalhadaporitem->connect_simple('activate', array($this,'chamaCadastro'), '040106', 'relatorios', 'detalhadaporitem');
        $this->menu_relatorios_vendas_detalhadaporitem->set_sensitive($this->verificaPermissao('040106',false));
		
		$this->menu_relatorios_compras_detalhadaporitem= $this->xml->get_widget('menu_relatorios_compras_detalhadaporitem');
        $this->menu_relatorios_compras_detalhadaporitem->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'comprasdetalhadaporitem');
        $this->menu_relatorios_compras_detalhadaporitem->set_sensitive($this->verificaPermissao('',false));
		
		$this->menu_relatorios_compras_totais= $this->xml->get_widget('menu_relatorios_compras_totais');
        $this->menu_relatorios_compras_totais->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'comprastotais');
        $this->menu_relatorios_compras_totais->set_sensitive($this->verificaPermissao('',false));
		
		$this->menu_relatorios_vendas_totaisformasdepgto=$this->xml->get_widget('menu_relatorios_vendas_totaisformasdepgto');
        $this->menu_relatorios_vendas_totaisformasdepgto->connect_simple('activate', array($this,'chamaCadastro'), '040103', 'relatorios', 'totaisdasformasdepgto');
        $this->menu_relatorios_vendas_totaisformasdepgto->set_sensitive($this->verificaPermissao('040103',false));
		
		$this->menu_relatorios_contas_totalizador=$this->xml->get_widget('menu_relatorios_contas_totalizador');
        $this->menu_relatorios_contas_totalizador->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios', 'contastotalizador');
        $this->menu_relatorios_contas_totalizador->set_sensitive($this->verificaPermissao('',false));
		
		$this->relatorio_contas_recebimento_media= $this->xml->get_widget('relatorio_contas_recebimento_media');
        $this->relatorio_contas_recebimento_media->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','contas_recebimento_media');
        $this->relatorio_contas_recebimento_media->set_sensitive($this->verificaPermissao('',false));
        
        $this->relatorio_vendas_lucro_bruto= $this->xml->get_widget('relatorio_vendas_lucro_bruto');
        $this->relatorio_vendas_lucro_bruto->connect_simple('activate', array($this,'chamaCadastro'), '040108', 'relatorios','vendas_lucro_bruto');
        $this->relatorio_vendas_lucro_bruto->set_sensitive($this->verificaPermissao('040108',false));
        
        $this->relatorio_contas_mov_agrupado_placon= $this->xml->get_widget('relatorio_contas_mov_agrupado_placon');
        $this->relatorio_contas_mov_agrupado_placon->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','contas_mov_agrupado_placon');
        $this->relatorio_contas_mov_agrupado_placon->set_sensitive($this->verificaPermissao('',false));

		$this->relatorio_mercadorias_ultimaalteracao= $this->xml->get_widget('relatorio_mercadorias_ultimaalteracao');
        $this->relatorio_mercadorias_ultimaalteracao->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','mercadorias_ultimaalteracao');
        $this->relatorio_mercadorias_ultimaalteracao->set_sensitive($this->verificaPermissao('',false));
        
        $this->relatorio_vendas_canceladas= $this->xml->get_widget('relatorio_vendas_canceladas');
        $this->relatorio_vendas_canceladas->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','vendas_canceladas');
        $this->relatorio_vendas_canceladas->set_sensitive($this->verificaPermissao('',false));  
        
        $this->relatorio_entregas_pendentes= $this->xml->get_widget('relatorio_entregas_pendentes');
        $this->relatorio_entregas_pendentes->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','entregas_pendentes');
        $this->relatorio_entregas_pendentes->set_sensitive($this->verificaPermissao('',false));
        
        $this->relatorio_entregas_efetuadas= $this->xml->get_widget('relatorio_entregas_efetuadas');
        $this->relatorio_entregas_efetuadas->connect_simple('activate', array($this,'chamaCadastro'), '', 'relatorios','entregas_efetuadas');
        $this->relatorio_entregas_efetuadas->set_sensitive($this->verificaPermissao('',false));
  		
  		
        $this->ultima_janela="";

        // abre telas de vendas (se tiver configurado para isso)
        if(retorna_CONFIG("abrirvendas") and $this->verificaPermissao('030101',false)){
            //$this->button_movimentacoes_venda->clicked();
			$this->menu_movimentacoes_venda->activate();
        }
		
		
		$this->menu_principal->show_all();
		
		
		$this->atalho_criapadrao();
		$this->atalho_ativarpadrao();
		
		
    }


    function logoff(){
        if(!empty($this->ultima_janela)){
            $this->fecha_janela();
        }
        $this->menu_principal->hide();
        new login;
    }

    function fecha_janela(){
        // verifica se a variavel nao esta vazia e se nao chama GtkWindow
        eval('
            if(!empty($this->'.$this->ultima_janela.') and is_a($this->'.$this->ultima_janela.'->janela,"GtkWindow")){
                $this->'.$this->ultima_janela.'->janela->hide();
            }
        ');
        return true;
    }

    /*function trocausuario(){
        $this->loginNovo=new login(true);
    }*/
	function atalho_criapadrao(){
		global $atalho_padrao;

    	// cria grupo de atalhos global
    	$atalho_padrao = new GtkAccelGroup();
    	
	}

	function atalho_ativarpadrao(){
		global $atalho_padrao;
		
		// atalho para a calculadora CONTROL + ALT + C
		$this->menu_sistema_calculadora->add_accelerator('activate', $atalho_padrao, Gdk::KEY_C, Gdk::CONTROL_MASK+Gdk::MOD1_MASK, Gtk::ACCEL_VISIBLE);
			
		// adiciona grupo de atalhos a janela de menu principal
		$this->menu_principal->add_accel_group($atalho_padrao);
	}
	
    function chamaCadastro($permissao, $pasta, $php, $tabela='none', $titulo=null, $tabela2=null,$tabela3=null, $tabela4=null, $tabela5=null){        
        global $atalho_padrao;
        if($permissao){
            if(!$this->verificaPermissao($permissao)){
                return;
            }
        }
        include_once($pasta.bar.$php.".php");
                
        $nome_janela=$pasta.$php.$tabela.$titulo.$tabela2.$tabela3.$tabela4.$tabela5;

        // remove caracteres invalidos para nome de variavel
        $nome_janela=str_replace(" ","",$nome_janela);
        $nome_janela=str_replace(".","",$nome_janela);
        $nome_janela=str_replace("\\","",$nome_janela);
        $nome_janela=str_replace("/","",$nome_janela);
        $nome_janela=str_replace("-","",$nome_janela);
		
        if($this->array_window[$nome_janela]===NULL){
	        $this->menu_principal->set_sensitive(FALSE);
	        $this->menu_principal->freeze_child_notify();
            
            $new_nome_janela=new $php($tabela, $titulo, $tabela2, $tabela3, $tabela4,$tabela5);            
            if(is_a($new_nome_janela->janela,"GtkWindow")){
	      		if($vbox1=$new_nome_janela->xml->get_widget("vbox1")){
	            	if($this->array_window[$nome_janela]===NULL){            	
		            	$new_vbox_notebook=new GtkVBox();
		            	$scroll=new GtkScrolledWindow();
		            	$scroll->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
		            	$scroll->add_with_viewport($new_vbox_notebook);
		            	if(!empty($titulo)){
		            		$label_notebook=new GtkLabel($titulo);
		            	}else{
		            		$label_notebook=null;
		            	}
		            	$this->array_window[$nome_janela]=$this->notebook_menugeral->append_page($scroll, $label_notebook);
		            	
				        $vbox1->reparent($new_vbox_notebook);
				        $scroll->show();
				        $new_vbox_notebook->show();
	            	}
				    $this->notebook_menugeral->set_current_page($this->array_window[$nome_janela]);
				    $new_nome_janela->janela->destroy();
				    	
		 		}else{
	          		$new_nome_janela->janela->set_uposition( retorna_CONFIG("posicaox"), retorna_CONFIG("posicaoy"));
	          		$new_nome_janela->janela->show();
	      		};
	        }else{
	        	$especial=true; 
	        }
	        $this->menu_principal->set_sensitive(TRUE);
			$this->menu_principal->thaw_child_notify();
					
	        if(is_a($new_nome_janela->windowHideShow,"GtkWindow")){
				$new_nome_janela->windowHideShow->destroy();
			}            
        }else{
        	$this->notebook_menugeral->set_current_page($this->array_window[$nome_janela]);
        }
		
		// ativa atalhos padroes e os novos da classe
		//$this->atalho_ativarpadrao();
		$this->notebook_menugeral->grab_focus();

		// quebra-galho para o cursos receber o foco ao clicar nos campos
		if($this->gambiarra_do_cursor_sumido==false){
			$this->menu_principal->hide();
			$this->menu_principal->show();
			$this->menu_principal->maximize();
			$this->gambiarra_do_cursor_sumido=true;
		}
		//if ($especial and $php<>"opcoes") $tmp=new $php($tabela);
    }
    
    function fechar_aba_notebook(){    	
 		$aba_atual=$this->notebook_menugeral->get_current_page();
    	    	
    	if(confirma(false,"Deseja fechar esta aba?")){    		
    		$this->notebook_menugeral->remove_page($aba_atual);
    		$key=array_search($aba_atual, $this->array_window);
    		unset($this->array_window[$key]);    			
    	}
    }
    
	function menu_sistema_calculadora(){
		$calculadora=retorna_CONFIG("calculadora");
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
            pclose(popen("start ".$calculadora, "r"));
        } else {               
            exec($calculadora." > /dev/null &");
        }
	}
    function menu_sistema_sobre(){
		global $setlocalelc_ctype, $setlocalelc_all, $GLOBALVERSAO, $GLOBALBUILD;
		$dlg = new GtkAboutDialog();
		$txt="";
		if(!function_exists('gd_info')){
			$extgd["GD Version"]="none";
		}else{
			$extgd=gd_info();
		}
		if($extgd["JPG Support"]==true){
			$txt.=" JPG";
		}
		if($extgd["PNG Support"]==true){
			$txt.=" PNG";
		}
		if($extgd["GIF Read Support"]==true){
			$txt.=" GIF";
		}
		$logo = GtkImage::new_from_file('tema'.bar.'logo2.png');
		$pixbufLogo=$logo->get_pixbuf();
		$dlg->set_logo($pixbufLogo);
		
		$dlg->set_program_name('LinuxStok');
                
		
		$con=$this->conecta();
		$sql="SELECT build FROM opcoes";
		$resultado=$con->Query($sql);
		$i = $con->FetchRow($resultado);
		$dlg->set_version($GLOBALVERSAO." build ".$GLOBALBUILD."/".$i[0]);
		$this->desconecta($con);
		
		
		$dlg->set_copyright(
			"Copyleft (C) 2004-2010 Eduardo RBS\n".
			"PHP ".phpversion()."\nPHP-GTK 2\n".
			"GD ".$extgd["GD Version"].$txt
		);
		$dlg->set_license(utf8_decode(
"LinuxStok é um software livre; você pode redistribuí-lo \ne/ou modificá-lo sob os termos da Licença Pública Geral \nGNU conforme publicada pela Free Software Foundation; ou a \nversão 2 da Licença, ou (sob sua opção) qualquer versão \nposterior.\nEste programa é distribuido na esperança de que seja útil, \nmas SEM QUALQUER GARANTIA; sem mesmo as garantias \nimplícitas de COMERCIALIZAÇÃO ou AJUSTES A UM \nPROPÓSITO PARTICULAR. Veja a Licença Pública Geral GNU \npara mais detalhes."));

		$dlg->set_website('http://linuxstok.sourceforge.net');

		$dlg->set_translator_credits(utf8_decode("Mantenedor e Administrador: \nEduardo RBS - edurbs@gmail.com \n\nContribuições e sugestões de:\nCarlos Alberto Rosa - foxbeto@gmail.com \nPablo Dall'Oglio - pablo@dalloglio.net \nEduardo A. E. Perez eperez@prognum.com.br \nLucas Saud - lucas.saud@gmail.com \nCyro Corte Real Filho cyroreal@gmail.com\nErick Wilder erickwilder@gmail.com\nKleiton kleiton@pcs.com.br"));

		$dlg->run();
                $dlg->destroy();

    }
    function menu_sistema_sobre_fecha(){
        $this->janela_sobre->hide();
    }
}
?>
