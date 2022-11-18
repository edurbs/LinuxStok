create table midiapropaganda (
 codigo SERIAL PRIMARY KEY,
 descricao varchar(50)
);
CREATE RULE on_insert_midiapropaganda AS ON INSERT TO midiapropaganda DO SELECT currval('midiapropaganda_codigo_seq') AS id;

create table cadcaixa (
 codigo SERIAL PRIMARY KEY,
 descricao varchar(50)
);
CREATE RULE on_insert_cadcaixa AS ON INSERT TO cadcaixa DO SELECT currval('cadcaixa_codigo_seq') AS id;

create table parentesco (
 codigo SERIAL PRIMARY KEY,
 descricao varchar(50)
);
CREATE RULE on_insert_parentesco AS ON INSERT TO parentesco DO SELECT currval('parentesco_codigo_seq') AS id;

create table romaneio (
 codigo SERIAL PRIMARY KEY,
 descricao varchar(100)
);
CREATE RULE on_insert_romaneio AS ON INSERT TO romaneio DO SELECT currval('romaneio_codigo_seq') AS id;

create table estados (  
  str_estado varchar(30),
  str_uf char(2) PRIMARY KEY
);

create table cep_loc (
  chave_local SERIAL PRIMARY KEY,
  nome_local varchar(60),
  cep8_local varchar(10),
  uf_local char(2),
  FOREIGN KEY (uf_local) REFERENCES estados (str_uf) ON DELETE RESTRICT
);
CREATE RULE on_insert_cep_loc AS ON INSERT TO cep_loc DO SELECT currval('cep_loc_chave_local_seq') AS id;

create table cep_bai (
  chave_bai SERIAL PRIMARY KEY,
  extenso_bai varchar(60),
  local_bai INTEGER,
  FOREIGN KEY (local_bai) REFERENCES cep_loc (chave_local) ON DELETE RESTRICT
);
CREATE RULE on_insert_cep_bai AS ON INSERT TO cep_bai DO SELECT currval('cep_bai_chave_bai_seq') AS id;

create table cep (
  chave_log SERIAL PRIMARY KEY,
  nome_log varchar(60),
  chvlocal_log INTEGER,
  chvbai1_log INTEGER,
  cep8_log varchar(10),
  FOREIGN KEY (chvlocal_log) REFERENCES cep_loc (chave_local) ON DELETE RESTRICT,
  FOREIGN KEY (chvbai1_log) REFERENCES cep_bai (chave_bai) ON DELETE RESTRICT
);
CREATE RULE on_insert_cep AS ON INSERT TO cep DO SELECT currval('cep_chave_log_seq') AS id;

create table nomebanco (
    codigo INTEGER PRIMARY KEY,
    sigla varchar(30),
    nome varchar(100)
);

create table nivelacesso (
    codigo SERIAL PRIMARY KEY,
    descricao varchar(100)
);
CREATE RULE on_insert_nivelacesso AS ON INSERT TO nivelacesso DO SELECT currval('nivelacesso_codigo_seq') AS id;

create table localarma (
 codigo SERIAL PRIMARY KEY,
 descricao varchar(100)
);
CREATE RULE on_insert_localarma AS ON INSERT TO localarma DO SELECT currval('localarma_codigo_seq') AS id;

create table placon (
 codigo varchar(20) PRIMARY KEY,
 descricao varchar(100)
);

create table meiopgto (
    codigo SERIAL PRIMARY KEY,
    descricao varchar(100),
    codplacon varchar(20), 
    FOREIGN KEY (codplacon) REFERENCES placon (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_meiopgto AS ON INSERT TO meiopgto DO SELECT currval('meiopgto_codigo_seq') AS id;

create table grpmerc (
 codigo    SERIAL PRIMARY KEY,
 descricao     varchar(100)
);
CREATE RULE on_insert_grpmerc AS ON INSERT TO grpmerc DO SELECT currval('grpmerc_codigo_seq') AS id;


CREATE TABLE profissao (
  codigo  SERIAL PRIMARY KEY,
  descricao    varchar(100)
);
CREATE RULE on_insert_profissao AS ON INSERT TO profissao DO SELECT currval('profissao_codigo_seq') AS id;


CREATE TABLE bancos (
  codbanco SERIAL PRIMARY KEY,
  numero INTEGER,
  titular varchar(40),
  agencia  varchar(12),
  conta    varchar(10),
  gerente  varchar(40),
  telefone varchar(13),
  fax      varchar(13),
  email    varchar(100),
  clientedesde date,
  valorcheque numeric(12,2),
  obs	   text,
  contadaempresa char(1),
  FOREIGN KEY (numero) REFERENCES nomebanco (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_bancos AS ON INSERT TO bancos DO SELECT currval('bancos_codbanco_seq') AS id;

create table empregador (
 codigo SERIAL PRIMARY KEY,
 inativo char, 
 natureza varchar(8), 
 nome varchar(100),
 contato varchar(40),
 dtnasc date,
 sexo char(1),
 dtcadastro date,
 obs text,
 ultvenda date,
 ultcompra date,
 cnpj_cpf varchar(18),
 ie_rg varchar(20),
 orgaorg varchar(30),
 dtemissaorg date,
 im varchar(20),
 ir varchar(20),
 pis varchar(20),
 carteira varchar(20),
 titulo varchar(20),
 habcomprar varchar(20),
 debmaximo numeric(12,2),
 comissao numeric(12,2),
 foto bytea,
 referencias text,
 codmidiapropaganda int,
 FOREIGN KEY (codmidiapropaganda) REFERENCES midiapropaganda (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_empregador AS ON INSERT TO empregador DO SELECT currval('empregador_codigo_seq') AS id;

create table funcionarios (
 codigo SERIAL PRIMARY KEY,
 inativo char,
 natureza varchar(8), 
 nome varchar(100),
 contato varchar(40),
 dtnasc date,
 sexo char(1),
 dtcadastro date,
 obs text,
 ultvenda date,
 ultcompra date,
 cnpj_cpf varchar(18),
 ie_rg varchar(20),
 orgaorg varchar(30),
 dtemissaorg date,
 im varchar(20),
 ir varchar(20),
 pis varchar(20),
 carteira varchar(20),
 titulo varchar(20),
 habcomprar varchar(20),
 debmaximo numeric(12,2),
 comissao numeric(12,2), 
 foto bytea, 
 referencias text,
 codmidiapropaganda int,
 FOREIGN KEY (codmidiapropaganda) REFERENCES midiapropaganda (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_funcionarios AS ON INSERT TO funcionarios DO SELECT currval('funcionarios_codigo_seq') AS id;


create table clientes (
 codigo SERIAL PRIMARY KEY,
 inativo char,
 natureza varchar(8), 
 nome varchar(100),
 contato varchar(40),
 dtnasc date,
 sexo char(1),
 dtcadastro date,
 obs text,
 ultvenda date,
 ultcompra date,
 cnpj_cpf varchar(18),
 ie_rg varchar(20),
 orgaorg varchar(30),
 dtemissaorg date,
 im varchar(20),
 ir varchar(20),
 pis varchar(20),
 carteira varchar(20),
 titulo varchar(20),
 habcomprar varchar(20),
 debmaximo numeric(12,2),
 comissao numeric(12,2),
 foto bytea, 
 referencias text, 
 codvendedor int,
 comissaovendedor numeric(12,2),
 codmidiapropaganda int,
 FOREIGN KEY (codmidiapropaganda) REFERENCES midiapropaganda (codigo) ON DELETE RESTRICT,
 FOREIGN KEY (codvendedor) REFERENCES funcionarios (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_clientes AS ON INSERT TO clientes DO SELECT currval('clientes_codigo_seq') AS id;


create table fornecedores (
 codigo SERIAL PRIMARY KEY,
 inativo char,
 natureza varchar(8), 
 nome varchar(100),
 contato varchar(40),
 dtnasc date,
 sexo char(1),
 dtcadastro date,
 obs text,
 ultvenda date,
 ultcompra date,
 cnpj_cpf varchar(18),
 ie_rg varchar(20),
 orgaorg varchar(30),
 dtemissaorg date,
 im varchar(20),
 ir varchar(20),
 pis varchar(20),
 carteira varchar(20),
 titulo varchar(20),
 habcomprar varchar(20),
 debmaximo numeric(12,2),
 comissao numeric(12,2),
 foto bytea, 
 referencias text,
 codmidiapropaganda int,
 FOREIGN KEY (codmidiapropaganda) REFERENCES midiapropaganda (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_fornecedores AS ON INSERT TO fornecedores DO SELECT currval('fornecedores_codigo_seq') AS id;

create table fabricantes (
 codigo SERIAL PRIMARY KEY,
 inativo char,
 natureza varchar(8), 
 nome varchar(100),
 contato varchar(40),
 dtnasc date,
 sexo char(1),
 dtcadastro date,
 obs text,
 ultvenda date,
 ultcompra date,
 cnpj_cpf varchar(18),
 ie_rg varchar(20),
 orgaorg varchar(30),
 dtemissaorg date,
 im varchar(20),
 ir varchar(20),
 pis varchar(20),
 carteira varchar(20),
 titulo varchar(20),
 habcomprar varchar(20),
 debmaximo numeric(12,2),
 comissao numeric(12,2),
 foto bytea, 
 referencias text,
 codmidiapropaganda int,
 FOREIGN KEY (codmidiapropaganda) REFERENCES midiapropaganda (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_fabricantes AS ON INSERT TO fabricantes DO SELECT currval('fabricantes_codigo_seq') AS id;


CREATE TABLE cadastro2bancos (
  codinterno SERIAL PRIMARY KEY,
  codbanco int,
  codigo int,
  cadastro varchar(20),
  FOREIGN KEY (codbanco) REFERENCES bancos(codbanco) ON DELETE RESTRICT
);
CREATE RULE on_insert_cadastro2bancos AS ON INSERT TO cadastro2bancos DO SELECT currval('cadastro2bancos_codinterno_seq') AS id;


CREATE TABLE cadastro2profissao (
  codinterno SERIAL PRIMARY KEY,
  descricao varchar(50),
  codprofissao INTEGER,
  codempregador INTEGER,
  codigo int,
  cadastro varchar(20),
  renda numeric(12,2),
  obs text,
  FOREIGN KEY (codprofissao) REFERENCES profissao (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codempregador) REFERENCES empregador (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_cadastro2profissao AS ON INSERT TO cadastro2profissao DO SELECT currval('cadastro2profissao_codinterno_seq') AS id;


CREATE TABLE cadastro2enderecos (
  codigo INTEGER,
  codinterno SERIAL PRIMARY KEY,
  cadastro varchar(20),
  descricao     varchar(40),
  endereco     varchar(80),
  numero  varchar(10),
  complemento  varchar(40),
  bairro       varchar(40),
  cidade       varchar(30),
  estado       char(2)    ,
  cep          varchar(10),
  telefone     varchar(20),
  fax          varchar(20),
  celular      varchar(20),
  email        varchar(100),
  site         varchar(50),
  romaneio INTEGER,
  FOREIGN KEY (romaneio) REFERENCES romaneio (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_cadastro2enderecos AS ON INSERT TO cadastro2enderecos DO SELECT currval('cadastro2enderecos_codinterno_seq') AS id;

CREATE TABLE cadastro2contatos (
  codigo int,
  codinterno SERIAL PRIMARY KEY,
  cadastro varchar(20),
  nome varchar(40),
  departamento varchar(40),
  telefone varchar(20),
  email  varchar(100),
  obs  text
);
CREATE RULE on_insert_cadastro2contatos AS ON INSERT TO cadastro2contatos DO SELECT currval('cadastro2contatos_codinterno_seq') AS id;

CREATE TABLE cadastro2familias (
  codigo int,
  codinterno SERIAL PRIMARY KEY,
  cadastro varchar(20),
  codparentesco INTEGER,
  nome varchar(50),
  dtnasc date,
  obs text,
  FOREIGN KEY (codparentesco) REFERENCES parentesco (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_cadastro2familias AS ON INSERT TO cadastro2familias DO SELECT currval('cadastro2familias_codinterno_seq') AS id;

create table mercadorias (
 codmerc SERIAL PRIMARY KEY,
 referencia varchar(40),
 codigobarras varchar(40),
 descricao varchar(80),
 resumo varchar(40),
 peso numeric(12,4),
 unidade varchar (3),
 codgrpmerc int,
 codlocalarma int,
 codfor int,
 codfab int, 
 estoqueatual numeric(12,3),
 estoqueminimo numeric(12,3),
 falsolucro numeric(12,2),
 precocusto numeric(12,3),
 customedio numeric(12,2),
 margemlucro numeric(4,2), 
 precovenda numeric(12,2),
 promopreco numeric(12,2),
 promoinicio date,
 promofim date,
 comissionada char(1),
 comissaomaxima numeric(4,2),
 descontomaximo numeric(4,2),
 ipi numeric(4,2),
 icms numeric(4,2),
 impostoextra numeric(4,2),
 cst varchar(10),
 cti varchar(10),
 cf varchar(10),
 st varchar(10),
 ultimavenda date,
 ultimacompra date,
 ultimaaltera date,
 obs text,
 foto bytea, 
 inativa char,
 precoatacado numeric(12,2), 
 falsolucroatacado numeric(12,2), 
 margemlucroatacado numeric(12,2), 
 quantatacado numeric(12,3),
 mostraobs char, 
 volume numeric(20),
 FOREIGN KEY (codfab)       REFERENCES fabricantes (codigo)     ON DELETE RESTRICT,
 FOREIGN KEY (codfor)       REFERENCES fornecedores (codigo)    ON DELETE RESTRICT,
 FOREIGN KEY (codgrpmerc)   REFERENCES grpmerc (codigo)     ON DELETE RESTRICT,
 FOREIGN KEY (codlocalarma) REFERENCES localarma (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_mercadorias AS ON INSERT TO mercadorias DO SELECT currval('mercadorias_codmerc_seq') AS id;


CREATE TABLE orcamento (
  codorcamento   SERIAL PRIMARY KEY,
  data         date,
  codcli      INTEGER,
  endereco varchar(40), 
  totalmerc numeric(12,2),
  desconto numeric(12,2), 
  totalnf numeric(12,2), 
  vendedor INTEGER,
  datafinalizado date,
  finalizado char(1),
  variacao numeric(12,2),
  hora varchar(10), 
  FOREIGN KEY (codcli) REFERENCES clientes (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (vendedor) REFERENCES funcionarios (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_orcamento AS ON INSERT TO orcamento DO SELECT currval('orcamento_codorcamento_seq') AS id;

create table entradas (
 codentradas SERIAL PRIMARY KEY,
 data date,
 codfor INTEGER,
 totalmerc numeric (12,2), /* total da compra SEM descontos */
 desconto numeric (12,2),  /* descontos */
 hora varchar(10), /* NEW */ 
 FOREIGN KEY (codfor) REFERENCES fornecedores (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_entradas AS ON INSERT TO entradas DO SELECT currval('entradas_codentradas_seq') AS id;

create table saidas (
 codsaidas SERIAL PRIMARY KEY,
 data date,
 codcli INTEGER,
 endereco varchar(40),  
 totalmerc numeric (12,2), /* total COM o desconto */
 desconto numeric (12,2), /* desconto final */
 totalnf numeric (12,2),  /* total SEM o desconto */
 vendedor INTEGER,
 hora varchar(10), 
 obs text,
 futura char,
 FOREIGN KEY (codcli) REFERENCES clientes (codigo) ON DELETE RESTRICT,
 FOREIGN KEY (vendedor) REFERENCES funcionarios (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_saidas AS ON INSERT TO saidas DO SELECT currval('saidas_codsaidas_seq') AS id;


create table entsai (
 codentsai  varchar(20),
 tipo       char(1), /* E=entrada(compra), S=saida(venda), D=devolucao, C=cancela(venda) */
 codmerc    INTEGER,
 precooriginal numeric (12,2),
 precocomdesconto numeric (12,2),
 precocusto numeric (12,2),
 quantidade numeric(12,3),
 entregue numeric(12,3),
 FOREIGN KEY (codmerc) REFERENCES mercadorias (codmerc) ON DELETE RESTRICT
);

create table pagar (
  codigo   SERIAL PRIMARY KEY,
  fiscal     varchar(50),
  data_c     date,
  data_v     date,
  valor      decimal(12,2),
  saldo      decimal(12,2),
  descr      text,
  codorigem int,
  codplacon varchar(20),
  obs        text,
    codsaidas int,
  codentradas int,
  FOREIGN KEY (codplacon) REFERENCES placon (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codsaidas) REFERENCES saidas (codsaidas)  ON DELETE RESTRICT,
  FOREIGN KEY (codentradas) REFERENCES entradas (codentradas)  ON DELETE RESTRICT,
  FOREIGN KEY (codorigem) REFERENCES fornecedores (codigo)  ON DELETE RESTRICT
);
CREATE RULE on_insert_pagar AS ON INSERT TO pagar DO SELECT currval('pagar_codigo_seq') AS id;

create table receber (
  codigo SERIAL PRIMARY KEY,
  fiscal     varchar(50),
  data_c     date,
  data_v     date,
  valor      decimal(12,2),
  saldo      decimal(12,2),
  descr      text,
  codorigem  int,
  codplacon  varchar(20),
  obs        text,
  vendedor   int,
  comissao   decimal(12,10),
    codsaidas int,
  codentradas int,
  FOREIGN KEY (codplacon) REFERENCES placon (codigo)  ON DELETE RESTRICT,
  FOREIGN KEY (vendedor) REFERENCES funcionarios (codigo)  ON DELETE RESTRICT,
  FOREIGN KEY (codsaidas) REFERENCES saidas (codsaidas)  ON DELETE RESTRICT,
  FOREIGN KEY (codorigem) REFERENCES clientes (codigo)  ON DELETE RESTRICT,
  FOREIGN KEY (codentradas) REFERENCES entradas (codentradas)  ON DELETE RESTRICT
);
CREATE RULE on_insert_receber AS ON INSERT TO receber DO SELECT currval('receber_codigo_seq') AS id;

create table movimentos (
  /* tabela com os pagamentos efetuados no mov. financeiro*/
  codigompr   SERIAL PRIMARY KEY,
  codmovim   int, /* codigo da conta a pagar ou receber*/
  tipomovim  char(1), /*C ou B (caixa ou banco) */
  codcadcaixa   INTEGER, /* codigo do caixa ou do banco */
  formamovim char(1), /* E ou S (entrada (receber) ou saida (pagar)) */
  data_c     date, /* data de pagamento */
  valor      decimal(12,2),
  desconto decimal(12,2),
  multa      decimal(12,2),
  juros      decimal(12,2),
  tipodoc char(1), /* NEW C ou O (cheque ou outro documento) ou E - estorno */
  numdoc     varchar(50), /* numero do documento ou do cheque */
  codbancocheque int, /* codigo do banco do cheque*/
  datacheque date, /* data de emissao do cheque */
  historico  text
);
CREATE RULE on_insert_movimentos AS ON INSERT TO movimentos DO SELECT currval('movimentos_codigompr_seq') AS id;

CREATE TABLE caixa (
  codigo SERIAL PRIMARY KEY,
  codigompr   int,
  formamovim   char(1),
  numero     varchar(12),
  data    date,
  hora varchar(10),
  valor     decimal(12,2),
  saldo decimal(12,2),
  historico  text,  
  origem int, /* cadcaixa */
  codplacon varchar(20), 
  obs       text,
  FOREIGN KEY (codplacon) REFERENCES placon (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codigompr) REFERENCES movimentos (codigompr) ON DELETE RESTRICT,
  FOREIGN KEY (origem) REFERENCES cadcaixa (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_caixa AS ON INSERT TO caixa DO SELECT currval('caixa_codigo_seq') AS id;

create table movbanc (
  codigo SERIAL PRIMARY KEY,
  codigompr   int,
  formamovim    char(1),
  numero     varchar(12),
  data     date,
  hora varchar(10),
  valor      decimal(12,2),
  saldo      decimal(12,2),
  historico      text,
  origem   INTEGER,
  codplacon varchar(20),  
  obs        text,
  FOREIGN KEY (codplacon) REFERENCES placon (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codigompr) REFERENCES movimentos (codigompr) ON DELETE RESTRICT,  
  FOREIGN KEY (origem) REFERENCES bancos (codbanco) ON DELETE RESTRICT
);
CREATE RULE on_insert_movbanc AS ON INSERT TO movbanc DO SELECT currval('movbanc_codigo_seq') AS id;

create table veiculos (
    codigo SERIAL PRIMARY KEY,  
    descricao varchar(100),
    renavam varchar(20),
    placa varchar(8),
    combustivel varchar(20),
	kilometragem varchar(9),
    marca varchar(20),
    modelo varchar(20),
    anofab varchar(4),
    anomod varchar(4),
    tara numeric(20),
    liquido numeric(20),
    volume numeric(20),
    obs text
);
CREATE RULE on_insert_veiculos AS ON INSERT TO veiculos DO SELECT currval('veiculos_codigo_seq') AS id;

create table comissao (
    codigo SERIAL PRIMARY KEY,
    valor numeric(12,2),
    codvendedor INTEGER,
    codreceber INTEGER,
    codsaidas int,  
    data date,
    FOREIGN KEY (codreceber) REFERENCES receber (codigo) ON DELETE RESTRICT,
    FOREIGN KEY (codvendedor) REFERENCES funcionarios (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_comissao AS ON INSERT TO comissao DO SELECT currval('comissao_codigo_seq') AS id;

CREATE TABLE cheque (
  codigo SERIAL PRIMARY KEY,
  situacao varchar(40),
  codbanco int,
  agencia varchar(20),
  conta varchar(20),
  titular varchar(50), 
  documento varchar(50),
  dataemissao date,
  bompara date,
  numero varchar(20),
  valor numeric(12,2),
  codcliente int,
  codfornecedor int,
  codpagar int,
  codreceber int,
  obs	   text,
  codigompr int,
  FOREIGN KEY (codigompr) REFERENCES movimentos (codigompr) ON DELETE RESTRICT,
  FOREIGN KEY (codbanco) REFERENCES nomebanco (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codcliente) REFERENCES clientes (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codfornecedor) REFERENCES fornecedores (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codpagar) REFERENCES pagar (codigo) ON DELETE RESTRICT,  
  FOREIGN KEY (codreceber) REFERENCES receber (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_cheque AS ON INSERT TO cheque DO SELECT currval('cheque_codigo_seq') AS id;

create table formapgto (
    codigoformapgto SERIAL PRIMARY KEY,
    descricao varchar(50),
    variacao numeric(12,2),
    taxafixa decimal(12,2),
    parcelas INTEGER,
    arredonda char,
    ativa char,
	avista char,
	chkdatavariavel char,
	chkdatafixa char,
	datafixa numeric(2),
	chkdiadasemana char,
	diadasemana numeric(1),
	variacaoobrigatoria char
);
CREATE RULE on_insert_formapgto AS ON INSERT TO formapgto DO SELECT currval('formapgto_codigoformapgto_seq') AS id;

create table parcelapgto (
    codigoparcelapgto SERIAL PRIMARY KEY,
    codigoformapgto INTEGER,
    codigomeiopgto INTEGER,
    codplacon varchar(20),
    porcentagem numeric(12,2),
    prazo INTEGER,
    tipoprazo varchar(20),
    tolerancia INTEGER,
    tipoentrada char,
    FOREIGN KEY (codigoformapgto) REFERENCES formapgto (codigoformapgto) ON DELETE RESTRICT,
    FOREIGN KEY (codigomeiopgto) REFERENCES meiopgto (codigo) ON DELETE RESTRICT,
    FOREIGN KEY (codplacon) REFERENCES placon (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_parcelapgto AS ON INSERT TO parcelapgto DO SELECT currval('parcelapgto_codigoparcelapgto_seq') AS id;

create table ctree (
    codigoctree varchar(10) PRIMARY KEY,
    descricao varchar(100)
);

create table permissao (
    codigopermissao SERIAL PRIMARY KEY,
    codigonivelacesso INTEGER,
    codigoctree varchar(10),
    permitido char,
    FOREIGN KEY (codigonivelacesso) REFERENCES nivelacesso (codigo) ON DELETE RESTRICT,
    FOREIGN KEY (codigoctree) REFERENCES ctree (codigoctree) ON DELETE RESTRICT
);
CREATE RULE on_insert_permissao AS ON INSERT TO permissao DO SELECT currval('permissao_codigopermissao_seq') AS id;

create table nivel2funcionario(
    codigonivelacesso INTEGER,
    codigofuncionario INTEGER,
    senha varchar(50),
    FOREIGN KEY (codigonivelacesso) REFERENCES nivelacesso (codigo)  ON DELETE RESTRICT,
    FOREIGN KEY (codigofuncionario) REFERENCES funcionarios (codigo)  ON DELETE RESTRICT
);

create table opcoes (
    placoncobranca varchar(20),
    placondevolucao varchar(20),
    versao varchar(20),
	build INTEGER,
    integraestoquefinanceiro char,
    largurapagina varchar(20),
    alturarecibo varchar(20),
    cabecalhorecibo text,
    rodaperecibo text,
    autotrocasenhapdv char,
    viasrecibo varchar(3),
    pdvenderecocliente char,
    observacaorecibo char,
    tiporecibo varchar(2),
    reciboimprimircliente char,
    recibodescricaoresumida char,
    autotreeview char,
    FOREIGN KEY (placoncobranca) REFERENCES placon (codigo)  ON DELETE RESTRICT,
    FOREIGN KEY (placondevolucao) REFERENCES placon (codigo)  ON DELETE RESTRICT
);

create table controlecaixa (
    dataaberto date,
    datafechado date,
    aberto char,
    fechado char,
    valorinicial numeric(12,2),
    valorfinal numeric(12,2),
    codcadcaixa int,
    PRIMARY KEY (dataaberto,codcadcaixa),
    FOREIGN KEY (codcadcaixa) REFERENCES cadcaixa (codigo) ON DELETE RESTRICT
);

CREATE TABLE clientestmp (
    codigo SERIAL PRIMARY KEY,
    nome text, 
    contato text, 
    ie text, 
    cgc text, 
    enderec text, 
    comple text, 
    cidade text, 
    estado text, 
    cep text, 
    fone text, 
    fax text, 
    email text, 
    obs text, 
    celular text, 
    datanas text, 
    cadastro text, 
    ultimaco text
);
CREATE RULE on_insert_clientestmp AS ON INSERT TO clientestmp DO SELECT currval('clientestmp_codigo_seq') AS id;

create table devolucoes (
 coddevolucoes SERIAL PRIMARY KEY,
 data date,
 codcli INTEGER,
 endereco varchar(40),
 totalmerc numeric (12,2),
 desconto numeric (12,2),
 totalnf numeric (12,2), 
 vendedor INTEGER,
 hora varchar(10), 
 obs text,
 codsaidas INTEGER,
 FOREIGN KEY (codsaidas) REFERENCES saidas (codsaidas) ON DELETE RESTRICT,
 FOREIGN KEY (codcli) REFERENCES clientes (codigo) ON DELETE RESTRICT,
 FOREIGN KEY (vendedor) REFERENCES funcionarios (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_devolucoes AS ON INSERT TO devolucoes DO SELECT currval('devolucoes_coddevolucoes_seq') AS id;

create table movpagamentos (
  codorigem INTEGER, /* codsaidas, codorcamento, codentrada, coddevolucao*/
  tipo char(1), /* O-orcamento, S-saida(venda), ou D-devolucao, C-Cancelada, E-entrada */
  nnf varchar(50),
  codmeiopgto INTEGER,
  meio varchar(50),
  valor numeric (12,2),
  data date,
  data_c date,
  codformapgto INTEGER,
  FOREIGN KEY (codmeiopgto) REFERENCES meiopgto (codigo) ON DELETE RESTRICT,
  FOREIGN KEY (codformapgto) REFERENCES formapgto (codigoformapgto) ON DELETE RESTRICT
);

create table entregas (
    codentregas SERIAL PRIMARY KEY,
	data date, 
	codcli INTEGER, 
	endereco varchar(40), 
	total numeric (12,2), 
	hora varchar(10),
	totalmerc numeric (12,2), 
	desconto numeric (12,2), 
	totalnf numeric (12,2), 
	vendedor INTEGER,
    FOREIGN KEY (vendedor) REFERENCES funcionarios (codigo) ON DELETE RESTRICT,
    FOREIGN KEY (codcli) REFERENCES clientes (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_entregas AS ON INSERT TO entregas DO SELECT currval('entregas_codentregas_seq') AS id;

create table entrega_itens (
	codentregas INTEGER, 
	codmerc INTEGER, 
	precooriginal numeric (12,2), 
	precocomdesconto numeric (12,2), 
	quantidade numeric(12,3), 
	codsaidas INTEGER,
	FOREIGN KEY (codentregas) REFERENCES entregas (codentregas) ON DELETE RESTRICT,
	FOREIGN KEY (codsaidas) REFERENCES saidas (codsaidas) ON DELETE RESTRICT,
	FOREIGN KEY (codmerc) REFERENCES mercadorias (codmerc) ON DELETE RESTRICT
);

create table ocorrencia_tipo (
	codigo SERIAL PRIMARY KEY,
	descricao varchar(100)
);
CREATE RULE on_insert_ocorrencia_tipo AS ON INSERT TO ocorrencia_tipo DO SELECT currval('ocorrencia_tipo_codigo_seq') AS id;

create table ocorrencia (
	codigo SERIAL PRIMARY KEY,
	resumo varchar(100),
	data date,
	hora varchar(10),
	cadastro varchar(20), /* clientes, fornecedores, funcionarios, fabricantes */
	cadastro_codigo INTEGER,
	conta varchar(20), /* pagar ou receber */
	conta_codigo INTEGER,
	funcionario INTEGER,
	codigo_tipo INTEGER,
	obs text,
	FOREIGN KEY (codigo_tipo) REFERENCES ocorrencia_tipo (codigo) ON DELETE RESTRICT
);
CREATE RULE on_insert_ocorrencia AS ON INSERT TO ocorrencia DO SELECT currval('ocorrencia_codigo_seq') AS id;
