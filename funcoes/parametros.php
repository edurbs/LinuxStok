<?php
class parametros extends funcoes{
	function parametros($argv1, $argv2, $argv3){
		global $GLOBALVERSAO, $GLOBALBUILD;
		if($argv1=="-h" or $argv1=="-help" or $argv1=="--help" or $argv1=="/?"){
			// help de parametros		
			echo "LinuxStok $GLOBALVERSAO $GLOBALBUILD
Modo de Uso:
   -c N = abre o ultimo caixa fechado ( N = codigo do caixa).
   -e script.php = le o script php especificado
   -h = mostra este help
   -u I F = faz o upgrade do build especificado (I= build inicial, F=build final)
   -v = mostra a versao e o build do sistema
\n";
		}elseif ($argv1=="-e"){
			// executa determinado script
			if(empty($argv2)){
				echo "especifique o script php a ser executado\n\n";
			}else{
				include_once($argv2);
				echo "\natualizacao $argv2\n ";
			}
		}elseif ($argv1=="-c"){
			// abre o ultimo caixa fechado
			if(empty($argv2)){
				echo "Voce deve especificar o codigo do caixa a ser aberto!";
			}else{
				// pega ultima data
				$sql="SELECT dataaberto, datafechado, fechado FROM controlecaixa WHERE codcadcaixa='$argv2' ORDER BY datafechado DESC LIMIT 1";
				$con=$this->conecta();
				$resultado=$con->Query($sql);
				$i=$con->FetchRow($resultado);
				// abre ultima data do caixa
				$sql="UPDATE controlecaixa SET fechado=null, datafechado=null WHERE dataaberto='$i[0]' AND datafechado='$i[1]' AND codcadcaixa='$argv2'";
				if(!$con->Query($sql)){
					echo "Erro ao abrir ultimo caixa $argv2 fechado.\n";
				}else{
					echo "Caixa $argv2 aberto com sucesso!!!\n";
				}
				$this->desconecta($con);
			}
		}elseif($argv1=="-u"){
			// faz o upgrade a partir do build especificado
			if(empty($argv2)){
				echo "Especifique o build inicial do upgrade\n";
			}elseif(empty($argv3)){
				echo "Especifique o build final do upgrade\n";
			}else{
				include_once("funcoes".bar."upgrade.php");
				new upgrade($argv2,$argv3);
				echo "Upgrade\n";
			}
			
		}elseif($argv1=="-v"){
			// mostra a versao e o build do sistema
			echo "LinuxStok $GLOBALVERSAO $GLOBALBUILD\n\n";
		}
		exit;
	}
}
?>