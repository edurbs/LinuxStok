<?php
class sqlcomum extends funcoes {
 	function Select($query){
 		return $this->Execute($query);
 	}
 	
 	function Insert($tabela, $campos, $etc=null){
    	$query="INSERT INTO $tabela (";
    	foreach($campos as $i){
    		$query.=" $i[0],";
    	}
    	$query=substr($query,0,-1);
    	$query.=") VALUES (";
    	foreach($campos as $i){
    		if($i[2]==true){
                    $i[1]=$this->limpaString($i[1]);
    		}
    		if($i[1]=='null' and $i[2]<>true){
    			$query.=" $i[1],";
    		}else{
    			$query.=" '$i[1]',";	
    		}
    		
    	}
    	$query=substr($query,0,-1);
    	$query.=") $etc";
    	
		return $this->Execute($query, true, true);
    }
    
    function Update($tabela, $campos, $etc=null){
    	$query="UPDATE $tabela SET ";
    	foreach($campos as $i){
    		if($i[2]==true){
                    $i[1]=$this->limpaString($i[1]);
    		}
    		if($i[1]=='null' and $i[2]<>true){
    			$query.=" $i[0]=$i[1],";
    		}else{
    			$query.=" $i[0]='$i[1]',";	
    		}
    		
    	}
    	$query=substr($query,0,-1);
    	$query.=" $etc";
    	
    	return $this->Execute($query,null,false);
    }
    
    function Delete($tabela, $etc){
    	$query="DELETE FROM $tabela $etc ";
    	$ret=$this->Execute($query,null,false);
    	return $ret; 
    }
    
    function Execute($query, $last=false, $retorna=true){
    	if($last){
    		$dbRes=$this->QueryLastCod($query);
    	}else{
			$dbRes=$this->Query($query);
    	}
    	if($retorna){
			if(!$dbRes){
				echo "\nSql-> ".$query."\n";
				$this->RaiseError($dbRes);
				return false;
			}else{
				return $dbRes; 
			}
		}else{
			$this->RaiseError($dbRes);
			return true;
		}
    }
    function limpaString($string){
    	$string=$this->tira_acentos($string);
		$string=strtoupper($string);
		$string=$this->EscapeString($string);
		return $string;
    }
}
?>
