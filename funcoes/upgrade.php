<?php
/*
NEW nome de versao 0.5.1 build 105
				   | | |        |
				   | | |		    +-- correcoes e desenvolvimento interno (controle de upgrade) 
				   | | |
				   | | +-- correcoes de versoes lancadas ao publico
				   | +-- novos modulos e funcoes
				   +-- versao estavel
*/
class upgrade extends funcoes {
	function upgrade($inicio=null, $fim=null){
		global $GLOBALVERSAO, $GLOBALBUILD;
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        if($BancoDeDados=="AgataSqlite"){
        	msg("Voce esta usando o LinuxStok com o banco de dados SQLite (versao 2). Use-o apenas para testes visto que o mesmo nao suporta alteracoes de tabelas (alter table). Para ambientes de producao use PostgreSQL (mais testado) ou MySQL.");
        	return;
        }
        $con=new $BancoDeDados;
        $con->Connect();
		//if($con=$this->upgrade_banco()){
			$sql2="SELECT build FROM opcoes";
			$resultado=$con->Query($sql2);
			$i = $con->FetchRow($resultado);

			$buildDB=$i[0];

			$buildDB=intval(str_replace(".","",$buildDB));
			$buildSW=intval(str_replace(".","",$GLOBALBUILD));
			if(empty($buildDB)){
				$con->Query("UPDATE opcoes SET versao='$GLOBALVERSAO', build='$GLOBALBUILD'");
				return;
			}
			// parametros opcionais especificados na linha de comando
			if(!empty($inicio) and !empty($fim)){
				$buildDB=$inicio;
				$buildSW=$fim;
			}
			if($buildDB!=$buildSW){
				//$sql[8][0]=... 8 e o build antigo a sofrer o upgrade.. Exemplo: novo build do software  tem o numero 9.. vai dar upgrade no build 8.. entao o sql deve ser sql[8]. Voce pode setar o numero do build no inicio do arquivo LinuxStok.php
				
				$sql[9][0]="alter table formapgto add column variacaoobrigatoria char";
				$sql[9][1]="insert into ctree values ('030117','Desconto total alem do limite da forma de pagamento')";
				
				$sql[11][0]="alter table entradas add column hora varchar(10)";
				
				$sql[13][0]="alter table receber add FOREIGN KEY (codorigem) REFERENCES clientes (codigo)  ON DELETE RESTRICT";
				$sql[13][1]="alter table pagar add FOREIGN KEY (codorigem) REFERENCES fornecedores (codigo)  ON DELETE RESTRICT";
				$sql[13][2]="alter table opcoes add column placondevolucao varchar(20)";
				$sql[13][3]="alter table caixa add column hora varchar(10)";
				$sql[13][4]="alter table movbanc add column hora varchar(10)";
				$sql[13][5]="alter table caixa add column saldo decimal(12,2)";
				$sql[13][6]="alter table movbanc add column saldo decimal(12,2)";
				$sql[13][7]="alter table controlecaixa rename column data to dataaberto";
				$sql[13][8]="alter table controlecaixa add column datafechado date";
				$sql[13][9]="ALTER TABLE bancos ADD COLUMN contadaempresa CHAR(1)";
				$sql[13][10]="alter table controlecaixa drop constraint controlecaixa_pkey";
				$sql[13][11]="alter table controlecaixa add primary key (dataaberto,codcadcaixa)";
				$sql[13][12]="drop table carros";
				if($BancoDeDados=="AgataPgsql"){
					$sql[13][13]="create table veiculos ( codigo SERIAL PRIMARY KEY, descricao varchar(100), renavam varchar(20), placa varchar(8), combustivel varchar(20), kilometragem varchar(9), marca varchar(20), modelo varchar(20), anofab varchar(4), anomod varchar(4), tara numeric(20), liquido numeric(20), obs text ); CREATE RULE on_insert_veiculos AS ON INSERT TO veiculos DO SELECT currval('veiculos_codigo_seq') AS id;";
				}else{
					$sql[13][13]="create table veiculos ( codigo INTEGER auto_increment PRIMARY KEY, descricao varchar(100), renavam varchar(20), placa varchar(8), combustivel varchar(20), kilometragem varchar(9), marca varchar(20), modelo varchar(20), anofab varchar(4), anomod varchar(4), tara numeric(20), liquido numeric(20), obs text )";
				}
				$sql[14][0]="alter table entsai add column precocusto decimal(12,2)";
				$sql[14][1]="insert into ctree values ('030702','Incluir')";
				$sql[14][2]="insert into ctree values ('030703','Excluir')";
				$sql[14][3]="insert into ctree values ('030704','Alterar')";
				$sql[14][4]="insert into ctree values ('030802','Incluir')";
				$sql[14][5]="insert into ctree values ('030803','Excluir')";
				$sql[14][6]="insert into ctree values ('030804','Alterar')";
				$sql[14][7]="insert into ctree values ('030902','A Pagar')";
				$sql[14][8]="insert into ctree values ('030903','A Receber')";
				$sql[14][9]="insert into ctree values ('030904','No Caixa')";
				$sql[14][10]="insert into ctree values ('030905','No Banco')";
				$sql[14][11]="insert into ctree values ('030906','Cancelar Pgto')";
				$sql[14][12]="insert into ctree values ('030907','Multas')";
				$sql[14][13]="insert into ctree values ('030908','Juros')";
				$sql[14][14]="insert into ctree values ('030909','Efetuar Pgto')";
				$sql[14][15]="alter table orcamento add column datafinalizado date";
				$sql[14][16]="alter table veiculos add column volume numeric(20)";
				$sql[14][17]="alter table mercadorias add column volume numeric(20)";
				$sql[20][0]="alter table opcoes add column largurapagina varchar(20)";
				$sql[20][1]="alter table opcoes add column alturarecibo varchar(20)";
				$sql[20][2]="alter table opcoes add column cabecalhorecibo text";
				$sql[20][3]="alter table opcoes add column rodaperecibo text";
				$sql[20][4]="alter table opcoes add column viasrecibo varchar(3)";
				$sql[20][5]="alter table opcoes add column autotrocasenhapdv char";
				$sql[20][6]="alter table opcoes add column observacaorecibo char";
				$sql[20][7]="alter table opcoes add column pdvenderecocliente char";
				$sql[20][8]="alter table formapgto add column taxafixa decimal(12,2)";
				$sql[21][0]="alter table opcoes add column tiporecibo varchar(2)";
				$sql[21][1]="alter table opcoes add column reciboimprimircliente char";
				$sql[21][2]="alter table opcoes add column recibodescricaoresumida char";
				$sql[23][0]="update ctree set descricao='Totais Meio de Pgto' where codigoctree='040102'";
				$sql[23][1]="update ctree set descricao='Totais Forma de Pgto' where codigoctree='040103'";
				$sql[23][2]="insert into ctree values ('040104','Por periodo/vendedor')";
				$sql[23][3]="insert into ctree values ('040105','Por periodo/vendedor do cliente')";
				$sql[23][4]="insert into ctree values ('040106','Detalhada por item')";
				$sql[23][5]="insert into ctree values ('040107','Contas Geradas')";
				$sql[23][6]="insert into ctree values ('040108','Lucro Bruto')";
				$sql[24][0]="alter table parcelapgto add column tipoentrada char";
				$sql[24][1]="update parcelapgto set tipoentrada='0'";
				$sql[28][0]="alter table saidas add column futura char";
				$sql[28][1]="update saidas set futura='0' ";
				$sql[28][2]="alter table entsai add column entregue numeric(12,3)";
				$sql[28][3]="update entsai set entregue=quantidade ";
				if($BancoDeDados=="AgataPgsql"){
					$sql[29][0]=" 
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
CREATE RULE on_insert_entregas AS ON INSERT TO entregas DO SELECT currval('entregas_codentregas_seq') AS id;";
					$sql[29][1]=" 
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
);";
				}else{
					$sql[29][0]=" 
create table entregas (
    codentregas INTEGER auto_increment PRIMARY KEY,
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
) TYPE=INNODB;
";
					$sql[29][1]=" 
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
) TYPE=INNODB;
";					
				}
				$sql[30][0]="alter table movimentos add column desconto decimal(12,2)";
				$sql[30][1]="insert into ctree values ('030911','Sangria do Caixa')";
				$sql[30][2]="insert into ctree values ('030912','Suprimento do Caixa')";
				$sql[30][3]="insert into ctree values ('030910','Descontos')";
				$sql[31][0]="alter table clientes add column inativo char";
				$sql[31][1]="update clientes set inativo='0'";
				$sql[31][2]="alter table fornecedores add column inativo char";
				$sql[31][3]="update fornecedores set inativo='0'";
				$sql[31][4]="alter table fabricantes add column inativo char";
				$sql[31][5]="update fabricantes set inativo='0'";
				$sql[31][6]="alter table funcionarios add column inativo char";
				$sql[31][7]="update funcionarios set inativo='0'";
				$sql[31][8]="alter table empregador add column inativo char";
				$sql[31][9]="update empregador set inativo='0'";
				$sql[32][0]="alter table clientes add column comissaovendedor numeric(12,2)";
				if($BancoDeDados=="AgataPgsql"){
					$sql[33][0]="create table ocorrencia_tipo (
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
CREATE RULE on_insert_ocorrencia AS ON INSERT TO ocorrencia DO SELECT currval('ocorrencia_codigo_seq') AS id;";	
				}elseif($BancoDeDados=="AgataMysql"){
					$sql[33][0]="create table ocorrencia_tipo (
	codigo INTEGER auto_increment PRIMARY KEY,
	descricao varchar(100)
) TYPE=INNODB;

create table ocorrencia (
	codigo INTEGER auto_increment PRIMARY KEY,
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
) TYPE=INNODB;
";
				}
				$sql[34][0]="alter table mercadorias add column mostraobs char ";
				$sql[35][0]="alter table mercadorias add column icms numeric(4,2)";
				$sql[35][1]="alter table mercadorias add column impostoextra numeric(4,2)";
				$sql[36][0]="alter table opcoes add column autotreeview char";
 
				
				$this->CriaProgressBar("Fazendo Upgrade...");
				$total=$buildSW-$buildDB;
				//echo "$total=$buildSW-$buildDB;";
				$esteI=1;
				 
				for($j=$buildDB;$j<=$buildSW;$j++){
					if(!empty($sql[$j])){
						foreach($sql[$j] as $tmp){
							$con->Query($tmp);
							$atual=(100*$total)/$esteI;
							$this->AtualizaProgressBar(null,$atual,true);
							$esteI++;
						}
					}
				}
				//$con->Disconnect();
				$this->FechaProgressBar();
				$sql="UPDATE opcoes SET versao='$GLOBALVERSAO', build='$GLOBALBUILD'";
				if(!$con->Query($sql)){
					return;
				}				
				msg("Upgrade efetuado do build $buildDB ate $buildSW.");
			}
		//}
	}
}
?>

