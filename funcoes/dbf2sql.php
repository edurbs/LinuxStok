<?php
// This is a class that converts a .DBF file into SQL intructions
// Autor : Pablo Dall'Oglio (pablo@univates.br)
// Date :  Saturday, 07, 2001

class dbf2sql extends funcoes
{
    var $db;
    var $num_records;
    var $num_fields;
    var $nome_arq;
    var $table;
    var $myfile;
    var $string;

    // This function open DBF file, set the variables
    // returns 1 if open with success otherwise, returns 0
    function opendb($dbffile, $sqlfile, $tablename)
    {
      if (($dbffile) && ($sqlfile))
      {
        $this->db = @dbase_open($dbffile, 0);
        if ($this->db)
        {
          $this->num_records = dbase_numrecords($this->db);
          $this->num_fields = dbase_numfields($this->db);
        }
        else
        {
          return(0);
        }
        $this->nome_arq = $sqlfile;
        $this->table = $tablename;        
        return(1);
      }
      return(0);
    }

    // This function open the SQL file for write
    // returns 1 if open with success otherwise, returns 0
    function opensql()
    {
      
      $this->myfile = @fopen($this->nome_arq, "w");
      if (!$this->myfile)
      {
        return(0);
      }
      
      return(1);
    }

    // This function close both DBf and SQL files
    function closeall()
    {
      fclose($this->myfile);
      dbase_close($this->db);
    }

    // This function get the columns from DBF file and
    // whrite it into a SQL file
    function GetColumns($array_columns,$string,$criatable)
    {
        $BancoDeDados=retorna_CONFIG("BancoDeDados");
        $con=&new $BancoDeDados;                
        
      $total = count($array_columns);      
      if($criatable){
          $linha_mens = "create table $this->table ( ";
            // get the column names
            for($count=0; $count<$total; $count++)
            {
              $linha_mens .= $array_columns[$count][1] . ", ";
    
            }
            $linha_mens = substr($linha_mens,0, strlen($linha_mens)-2);
            $linha_mens .= " );";
            $retorno.=$linha_mens;
        }
        
      // loop the database
      for($index=1; $index <= $this->num_records; $index ++)
      {
        $record = dbase_get_record($this->db, $index); // get the actual record
        // linha_mens is the SQL instruction to write into SQL file
        $linha_mens = "insert into $this->table ( ";
        // get the column names
        for($count=0; $count<$total; $count++)
        {
          $linha_mens .= $array_columns[$count][1] . ", ";

        }
        $linha_mens = substr($linha_mens,0, strlen($linha_mens)-2);
        $linha_mens .= " ) values (";

        // get the column values
        for($count=0; $count<$total; $count++)
        {
          $linha_mens .= "'" .$con->EscapeString( trim($record[$array_columns[$count][0]])). "', ";
          //$con->EscapeString($this->tira_acentos (trim($record[$array_columns[$count][0]]))) . "', ";
          //$con->EscapeString(utf8_encode($this->tira_acentos(trim($record[$array_columns[$count][0]])))) . "', ";
          
          
        }


        $linha_mens = substr($linha_mens,0, strlen($linha_mens)-2);
        $linha_mens .= " ); \n";

        // Ignore deleted fields
        if ($record["deleted"] != "1")
        { 
            if(!$string){
                fputs($this->myfile, "$linha_mens");
            }else{
                $retorno.=$linha_mens;
            }
        }
      }
    return $retorno;
    }
    function tira_acentos($Var){
        $Var = ereg_replace("[áàãâ]","a",$Var);
        $Var = ereg_replace("[ÁÀÃÂ]","A",$Var);
        $Var = ereg_replace("[éèê]","e",$Var);
        $Var = ereg_replace("[ÉÈÊ]","E",$Var);
        $Var = ereg_replace("[ÍÌÎ]","I",$Var);
        $Var = ereg_replace("[íìî]","i",$Var);
        $Var = ereg_replace("[óòõôö]","o",$Var);
        $Var = ereg_replace("[ÓÒÕÔÖ]","O",$Var);
        $Var = ereg_replace("[úùûü]","u",$Var);
        $Var = ereg_replace("[ÚÙÛÜ]","U",$Var);
        $Var = ereg_replace("[ýÿ]","y",$Var);
        $Var = ereg_replace("[ÝŸ]","Y",$Var);
        $Var = str_replace("ç","c",$Var);
        $Var = str_replace("Ç","C",$Var);
        //$Var = ereg_replace(" ","",$Var); 
        return $Var;
    }

}
?>