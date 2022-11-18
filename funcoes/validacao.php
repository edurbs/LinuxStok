<?php
class validacao {
    function validacao(){
    }
    
	function corrigeMarkup($Var){
		$Var = str_replace("&","&amp;",$Var);
		$Var = str_replace("<","&lt;",$Var);
		$Var = str_replace(">","&gt;	",$Var); 		
		return $Var;
	}
	
    function mascaraNewVirgulaAux($texto,$entry,$decimal,$porcento=null){
		$negativo="";
		$ultimo_char=$texto{strlen($texto)-1};
		if($ultimo_char=="-" or $texto{0}=="-"){ // se digitou "-"    
			$negativo="-";		
		}
		if($ultimo_char=="+"){ // se digitou "+"
			$negativo="";
		}		
		if($ultimo_char=="%" and $porcento==TRUE){ // se digitou "%" na ultima vez
			$porcento="%";
		}else{
			$porcento="";
		}
		// conta quantas vezes tem % na string
		$Nporcento=substr_count($texto, '%');
		if($Nporcento>1){
			$porcento=""; // se ja tiver % desliga o porcento
		}
		
		if($ultimo_char=="." or $ultimo_char==","){ // se digitou "," ou '.' transforma em 2 zeros
			$texto.="00";
		}    		
		$texto=$this->DeixaSoNumero($texto);
		$tamanho=strlen($texto);
		if($decimal==0){ // somente numero sem decimal
			$entry->set_text($texto);
		}else{
			if(!$antesv=intval(substr($texto,0,$tamanho-$decimal))){
				$antesv="0";
			}
			if(!$depoisv=substr($texto,$tamanho-$decimal,$decimal)){
				$depoisv=str_repeat("0",$decimal);
			}
			while(strlen($depoisv)<$decimal){
				$depoisv="0".$depoisv;
			}
			$entry->set_text($negativo.$antesv.",".$depoisv.$porcento);			
		}
		$entry->set_position($tamanho+$decimal+1);
	}

    function mascaraNew($entry, $evento, $formato,$entry_Pessoa=null,$entry_Estado=null,$retorno=true){
       	
       	$key=$evento->keyval;
       	if(!empty($key)){
       		$this->mascaraNewKey=$key;
       	}
        
        if($retorno){
            Gtk::timeout_add(50,array($this,'mascaraNew'),$entry,$evento,$formato,$entry_Pessoa,$entry_Estado,false);
            return;
        }
        
        // $entry - widget embutido no sinal key-release-event
        // $evento - embutido no sinal key-release-event
        // $formato - vc DEVE especificar este: tipo da mascara
        //          onde letras e numero sao um * exemplo: mascara para data: **-**-****
        //          NAO Use para campos numericos, nem IE ou CNPJ, CPF
        // $entry_Estado e o entry que contem o estado.. usado para Inscricao Estadual
        // $entry_Pessoa o entry que diz se e pessoa fisica ou juridica.. usado para CPF ou CNPJ
        if(!empty($entry_Estado)){
            $Estado=$entry_Estado->get_text();
        }
        if(!empty($entry_Pessoa)){
            $FouJ=$entry_Pessoa->get_active_text();
        }
        $texto = trim($entry->get_text());
        $tamanho=strlen($texto);
        switch($formato){
			case "numero":
				$this->mascaraNewVirgulaAux($texto,$entry,0);
				break;
			case "virgula2":
				$this->mascaraNewVirgulaAux($texto,$entry,2);
				break;
			case "virgula3":
				$this->mascaraNewVirgulaAux($texto,$entry,3);
				break;
			case "virgula4":
				$this->mascaraNewVirgulaAux($texto,$entry,4);
				break;
			case "porcento2":
				$this->mascaraNewVirgulaAux($texto,$entry,2,TRUE);
				break;
			case "cnpj":
                if($FouJ=="Fisica"){
                    // formata CPF
                    /*$entry->set_max_length(14);
                    switch($tamanho){
                        case 4:
                            $entry->insert_text(3,'.');
                            break;
                        case 8:
                            $entry->insert_text(7,'.');
                            break;
                        case 12:
                            $entry->insert_text(11,'/');
                            break;
                    }*/
                    $this->mascaraEspecial($entry,'***.***.***/**');
                }else{
                    // formata CNPJ
                    /*$entry->set_max_length(18);
                    switch($tamanho){
                        case 3:
                            $entry->insert_text(2,'.');
                            break;
                        case 7:
                            $entry->insert_text(6,'.');
                            break;
                        case 11:
                            $entry->insert_text(10,'/');
                            break;
                        case 16:
                            $entry->insert_text(15,'-');
                            break;
                    }*/
                    $this->mascaraEspecial($entry,'**.***.***/****-**');
                }
                break;
            case "TitEleitor":
                /*$entry->set_max_length(12);
                if($tamanho==10){
                    $entry->insert_text(9,'/');
                }*/
                $this->mascaraEspecial($entry,'*********/**');
                break;
            case "ie":
                if($FouJ=="Fisica"){
                    // formata rg no campo de inscricao estadual
                    $entry->set_max_length(15);
                }else{
                    $Estado=strtoupper($Estado);
                    // se for ISENTO retorna
                    if(strtoupper(substr(trim($texto),0,1))=="I"){
                        $entry->set_max_length(6);
                    }else{
                        switch($Estado){
                            case 'AC':
                                //           12345678901234567
                                //Mascara = '**.***.***/***-**' ;
                                $this->mascaraEspecial($entry,'**.***.***/***-**');
                                break;
                            case 'AL':
                                //           123456789
                                //Mascara = '*********'
                                $this->mascaraEspecial($entry,'*********');
                                break;
                            case 'AP':
                                //           123456789
                                //Mascara = '*********'
                                $this->mascaraEspecial($entry,'*********');
                                break;
                            case 'AM':
                                //           123456789012
                                //Mascara = '**.***.***-*' ;
                                $this->mascaraEspecial($entry,'**.***.***-*');
                                break;
                            case 'BA':
                                //           123456789
                                //Mascara = '******-**' ;
                                $this->mascaraEspecial($entry,'******-**');
                                break;
                            case 'CE':
                                //            1234567890
                                //Mascara = '********-*' ;
                                $this->mascaraEspecial($entry,'********-*');
                                break;
                            case  'DF':
                                //           12345678901234
                                //Mascara = '***********-**' ;
                                $this->mascaraEspecial($entry,'***********-**');
                                break;
                            case 'ES':
                                //            123456789
                                // Mascara = '*********'
                                $this->mascaraEspecial($entry,'*********');
                                break;
                            case 'GO':
                                //           123456789012
                                //Mascara = '**.***.***-*' ;
                                $this->mascaraEspecial($entry,'**.***.***-*');
                                break;
                            case 'MA':
                                //            123456789
                                // Mascara = '*********'
                                $this->mascaraEspecial($entry,'*********');
                                break;
                            case 'MT':
                                //             123456789012
                                //Mascara = '**********-*' ;
                                $this->mascaraEspecial($entry,'**********-*');
                                break;
                            case 'MS':
                                //            123456789
                                // Mascara = '*********'
                                $this->mascaraEspecial($entry,'*********');
                                break;
                            case 'MG':
                                //           1234567890123456
                                //Mascara = '***.***.***/****'
                                $this->mascaraEspecial($entry,'***.***.***/****');
                                break;
                            case 'PA':
                                //           12345678901
                                //Mascara = '**-******-*' ;
                                $this->mascaraEspecial($entry,'**-******-*');
                                break;
                            case 'PB':
                                //           1234567890
                                //Mascara = '********-*' ;
                                $this->mascaraEspecial($entry,'********-*');
                                break;
                            case 'PR':
                                //           12345678901
                                //Mascara = '********-**' ;
                                $this->mascaraEspecial($entry,'********-**');
                                break;
                            case 'PE':
                                //           123456789012345678
                                //Mascara = '**.*.***.*******-*';
                                $this->mascaraEspecial($entry,'**.*.***.*******-*');
                                break;
                            case 'PI':
                                //            123456789
                                // Mascara = '*********'
                                $this->mascaraEspecial($entry,'*********');
                                break;
                            case 'RJ':
                                //           12345678901
                                //Mascara = '**.***.**-*' ;
                                $this->mascaraEspecial($entry,'**.***.**-*');
                                break;
                            case 'RN':
                                //            123456789012
                                // Mascara = '**.***.***-*' ;
                                $this->mascaraEspecial($entry,'**.***.***-*');
                                break;
                            case 'RS':
                                //           12345678901
                                //Mascara = '***/*******' ;
                                $this->mascaraEspecial($entry,'***/*******');
                                break;
                            case 'RO':
                                //           123456789012345
                                //Mascara = '*************-*' ;
                                $this->mascaraEspecial($entry,'*************-*');
                                break;
                            case 'RR':
                                //           1234567890
                                //Mascara = '********-*' ;
                                $this->mascaraEspecial($entry,'********-*');
                                break;
                            case 'SC':
                                //           12345678901
                                //Mascara = '***.***.***' ;
                                $this->mascaraEspecial($entry,'***.***.***');
                                break;
                            case 'SP':
                                $charUm=strtoupper(substr($texto,0,1));
                                if($charUm=="P"){
                                    // 1234567890123456
                                    // P-01100424.3/002
                                    $this->mascaraEspecial($entry,'*-********.*/***');
                                }else{
                                    //           123456789012345
                                    //Mascara = '***.***.***.***' ;
                                    $this->mascaraEspecial($entry,'***.***.***.***');
                                }
                                break;
                            case 'SE':
                                //           1234567890
                                //Mascara = '********-*' ;
                                $this->mascaraEspecial($entry,'********-*');
                                break;
                            case 'TO':
                                //            12345678901
                                // Mascara = '***********'
                                $this->mascaraEspecial($entry,'***********');
                                break;
                        }
                    }

                }
                break;
            default:
                $this->mascaraEspecial($entry,$formato);
                break;
        }
    }

    function mascaraEspecial($entry,$formato,$retorno=true){
        if($retorno){
            Gtk::timeout_add(50,array($this,'mascaraEspecial'),$entry,$formato,false);
            return;
        }
        
        if($this->mascaraNewKey==Gdk::KEY_BackSpace or $this->mascaraNewKey==Gdk::KEY_Delete or $this->mascaraNewKey==Gdk::KEY_KP_Delete){        	
        	return;
        }
        $texto = trim($entry->get_text());
        $tamanho=strlen($texto);
        $coringa="*";

        $entry->set_max_length(strlen($formato));
        //for($i=0;$i<$tamanho;$i++){
        
        // posicao do cursor dentro do entry
        $posicao=$entry->get_position();
        if($posicao>$tamanho){
        	$incremento=1;
        }
        for($i=0;$i<$posicao+$incremento;$i++){
            if($formato{$i}<>$coringa and $texto{$i}<>$formato{$i}){
                $entry->insert_text($i,$formato{$i});
                return;
            }
        }
    }

    //Funcao que calcula CNPJ

      function CalculaCNPJ($CampoNumero)
      {
       $RecebeCNPJ=${"CampoNumero"};

       $s="";
       for ($x=1; $x<=strlen($RecebeCNPJ); $x=$x+1)
       {
        $ch=substr($RecebeCNPJ,$x-1,1);
        if (ord($ch)>=48 && ord($ch)<=57)
        {
         $s=$s.$ch;
        }
       }

       $RecebeCNPJ=$s;
       if (strlen($RecebeCNPJ)!=14)
       {
         return false;
       }
       else
        if ($RecebeCNPJ=="00000000000000")
        {
         $then;
         return false;
       }
       else
       {
        $Numero[1]=intval(substr($RecebeCNPJ,1-1,1));
        $Numero[2]=intval(substr($RecebeCNPJ,2-1,1));
        $Numero[3]=intval(substr($RecebeCNPJ,3-1,1));
        $Numero[4]=intval(substr($RecebeCNPJ,4-1,1));
        $Numero[5]=intval(substr($RecebeCNPJ,5-1,1));
        $Numero[6]=intval(substr($RecebeCNPJ,6-1,1));
        $Numero[7]=intval(substr($RecebeCNPJ,7-1,1));
        $Numero[8]=intval(substr($RecebeCNPJ,8-1,1));
        $Numero[9]=intval(substr($RecebeCNPJ,9-1,1));
        $Numero[10]=intval(substr($RecebeCNPJ,10-1,1));
        $Numero[11]=intval(substr($RecebeCNPJ,11-1,1));
        $Numero[12]=intval(substr($RecebeCNPJ,12-1,1));
        $Numero[13]=intval(substr($RecebeCNPJ,13-1,1));
        $Numero[14]=intval(substr($RecebeCNPJ,14-1,1));

        $soma=$Numero[1]*5+$Numero[2]*4+$Numero[3]*3+$Numero[4]*2+$Numero[5]*9+$Numero[6]*8+$Numero[7]*7+
        $Numero[8]*6+$Numero[9]*5+$Numero[10]*4+$Numero[11]*3+$Numero[12]*2;

        $soma=$soma-(11*(intval($soma/11)));

       if ($soma==0 || $soma==1)
       {
         $resultado1=0;
       }
       else
       {
        $resultado1=11-$soma;
       }
       if ($resultado1==$Numero[13])
       {
        $soma=$Numero[1]*6+$Numero[2]*5+$Numero[3]*4+$Numero[4]*3+$Numero[5]*2+$Numero[6]*9+
        $Numero[7]*8+$Numero[8]*7+$Numero[9]*6+$Numero[10]*5+$Numero[11]*4+$Numero[12]*3+$Numero[13]*2;
        $soma=$soma-(11*(intval($soma/11)));
        if ($soma==0 || $soma==1)
        {
         $resultado2=0;
        }
       else
       {
       $resultado2=11-$soma;
       }
       if ($resultado2==$Numero[14])
       {
         return true;
       }
       else
       {
         return false;
       }
      }
      else
      {
         return false;
      }
     }
    }
    //Fim do Calcula CNPJ


    function CalculaCPF($CampoNumero)
      {
       $RecebeCPF=$CampoNumero;
       //Retirar todos os caracteres que nao sejam 0-9
       $s="";
       for ($x=1; $x<=strlen($RecebeCPF); $x=$x+1)
       {
        $ch=substr($RecebeCPF,$x-1,1);
        if (ord($ch)>=48 && ord($ch)<=57)
        {
          $s=$s.$ch;
        }
       }
       $RecebeCPF=$s;
       if (strlen($RecebeCPF)!=11)
       {

            return false;
       }
       else
         if ($RecebeCPF=="00000000000" or $RecebeCPF=="11111111111" or $RecebeCPF=="22222222222" or $RecebeCPF=="33333333333" or $RecebeCPF=="44444444444" or $RecebeCPF=="55555555555" or $RecebeCPF=="66666666666" or $RecebeCPF=="77777777777" or $RecebeCPF=="88888888888" or $RecebeCPF=="99999999999")
         {
             return false;
         }
         else
         {
          $Numero[1]=intval(substr($RecebeCPF,1-1,1));
          $Numero[2]=intval(substr($RecebeCPF,2-1,1));
          $Numero[3]=intval(substr($RecebeCPF,3-1,1));
          $Numero[4]=intval(substr($RecebeCPF,4-1,1));
          $Numero[5]=intval(substr($RecebeCPF,5-1,1));
          $Numero[6]=intval(substr($RecebeCPF,6-1,1));
          $Numero[7]=intval(substr($RecebeCPF,7-1,1));
          $Numero[8]=intval(substr($RecebeCPF,8-1,1));
          $Numero[9]=intval(substr($RecebeCPF,9-1,1));
          $Numero[10]=intval(substr($RecebeCPF,10-1,1));
          $Numero[11]=intval(substr($RecebeCPF,11-1,1));

         $soma=10*$Numero[1]+9*$Numero[2]+8*$Numero[3]+7*$Numero[4]+6*$Numero[5]+5*
         $Numero[6]+4*$Numero[7]+3*$Numero[8]+2*$Numero[9];
         $soma=$soma-(11*(intval($soma/11)));

        if ($soma==0 || $soma==1)
        {
          $resultado1=0;
        }
        else
        {
          $resultado1=11-$soma;
        }

        if ($resultado1==$Numero[10])
        {
         $soma=$Numero[1]*11+$Numero[2]*10+$Numero[3]*9+$Numero[4]*8+$Numero[5]*7+$Numero[6]*6+$Numero[7]*5+
         $Numero[8]*4+$Numero[9]*3+$Numero[10]*2;
         $soma=$soma-(11*(intval($soma/11)));

         if ($soma==0 || $soma==1)
         {
           $resultado2=0;
         }
         else
         {
          $resultado2=11-$soma;
         }
         if ($resultado2==$Numero[11])
         {
            return true;
         }
         else
         {

             return false;
         }
        }
        else
        {

             return false;
        }
       }
      }
    // Fim do Calcula CPF

    // valida telefone (primeiro retira caracteres numericos para depois validar numeros com 8 digitos)
    function validaTelefone($texto){
        $texto= eregi_replace ("[^0-9]", "", $texto);
        if(strlen($texto)==10){
            return true;
        }else{
            return false;
        }
    }

    // validacao de Inscricao Estadual
    function valida_IE($ie, $cadastro, $entry_Estado){
        //if(!empty($cadastro)){
        //    eval('global $'.$cadastro.';');
        //    if(!empty($entry_Estado)){
        $Estado=$entry_Estado->get_text();
        //    }
        //}
        if(trim(strtoupper($ie))=="ISENTO"){
            return true;
        }
        // verificaï¿½o do primeiro caracter (SP)
        $charUm=substr($ie,0,1);
        //Retirar todos os caracteres que nao sejam 0-9
        $s="";
        for ($x=1; $x<=strlen($ie); $x=$x+1)
        {
            $ch=substr($ie,$x-1,1);
            if (ord($ch)>=48 && ord($ch)<=57)
            {
                $s=$s.$ch;
            }
        }
        $ie=$s;
        // Fim
        $Estado=strtoupper($Estado);
        if($Estado=="AC"){
            if(strlen($ie)<>13){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=0 or $ie[1]!=1){return false;}
            $soma=$ie[0]*4+$ie[1]*3+$ie[2]*2+$ie[3]*9+$ie[4]*8+$ie[5]*7+$ie[6]*6+$ie[7]*5+$ie[8]*4+$ie[9]*3+$ie[10]*2;
            $resto=$soma%11;
            $dig1=11-$resto;
            if($dig1==10 or $dig1==11){$dig1=0;}
            $soma=$ie[0]*5+$ie[1]*4+$ie[2]*3+$ie[3]*2+$ie[4]*9+$ie[5]*8+$ie[6]*7+$ie[7]*6+$ie[8]*5+$ie[9]*4+$ie[10]*3+$ie[11]*2;
            $resto=$soma%11;
            $dig2=11-$resto;
            if($dig2==10 or $dig2==11){$dig2=0;}
            if($ie[11]==$dig1 and $ie[12]==$dig2){return true;}else{return false;}
        }
        if($Estado=="AL"){
            if(strlen($ie)<>9){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=2 or $ie[1]!=4){return false;}
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $produto=$soma*10;
            $resto=$produto-intval($produto/11)*11;
            if($resto==10){$resto=0;}
            if($resto==$ie[8]){return true;} else {return false;}
        }
        if($Estado=="AP"){
            if(strlen($ie)!=9){return false;}
            if(intval($ie)>=30000010 and intval($ie)<=30170009){$p=5;$d=0;}
            if(intval($ie)>=30170010 and intval($ie)<=30190229){$p=9;$d=1;}
            if(intval($ie)>=30190230){$p=0;$d=0;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=0 or $ie[1]!=3){return false;}
            $soma=$p+$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10){$dig=0;}elseif($dig==11){$dig="D";}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="AM"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            if($soma<11){$dig=11-$soma;}else{
                $resto=$soma%11;
                if($resto<=1){$dig=0;}else{$dig=11-$resto;}
            }
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="BA"){
            if(strlen($ie)!=8){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]>=0 or $ie[0]<=5 or $ie[0]==8){
                $soma=$ie[0]*7+$ie[1]*6+$ie[2]*5+$ie[3]*4+$ie[4]*3+$ie[5]*2;
                $resto=$soma%10;
                $dig2=10-$resto;
                if($resto==0){$dig2=0;}
                $soma=$ie[0]*8+$ie[1]*7+$ie[2]*6+$ie[3]*5+$ie[4]*4+$ie[5]*3+$dig2*2;
                $resto=$soma%10;
                $dig1=10-$resto;
            }elseif($ie[0]==6 or $ie[0]==7 or $ie[0]==9){
                $soma=$ie[0]*7+$ie[1]*6+$ie[2]*5+$ie[3]*4+$ie[4]*3+$ie[5]*2;
                $resto=$soma%11;
                $dig2=11-$resto;
                if($resto==0 or $resto==1){$dig2=0;}
                $soma=$ie[0]*8+$ie[1]*7+$ie[2]*6+$ie[3]*5+$ie[4]*4+$ie[5]*3+$dig2*2;
                $resto=$soma%11;
                $dig1=11-$resto;
            }
            if($ie[6]==$dig1 and $ie[7]==$dig2){return true;}else{return false;}
        }
        if($Estado=="CE"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10 or $dig==11){$dig=0;}
            if($dig!=$ie[8]){return false;}else{return true;}
        }
        if($Estado=="DF"){
            if(strlen($ie)!=13){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=0 or $ie[1]!=7){return false;}
            $soma=$ie[0]*4+$ie[1]*3+$ie[2]*2+$ie[3]*9+$ie[4]*8+$ie[5]*7+$ie[6]*6+$ie[7]*5+$ie[8]*4+$ie[9]*3+$ie[10]*2;
            $resto=$soma%11;
            $dig1=11-$resto;
            if($dig1==10 or $dig1==11){$dig1=0;}
            $soma=$ie[0]*5+$ie[1]*4+$ie[2]*3+$ie[3]*2+$ie[4]*9+$ie[5]*6+$ie[6]*7+$ie[7]*6+$ie[8]*5+$ie[9]*4+$ie[10]*3+$ie[11]*2;
            $resto=$soma%11;
            $dig2=11-$resto;
            if($dig2==10 or $dig2==11){$dig2=0;}
            if($ie[11]!=$dig1 or $ie[12]!=$dig2){return false;}else{return true;}
        }
        if($Estado=="ES"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            if($resto<2){$dig=0;}
            if($resto>1){$dig=11-$resto;}
            if($dig!=$ie[8]){return false;}else{return true;}
        }
        if($Estado=="GO"){
            if(strlen($ie)!=9){return false;}
            $ieold=intval(substr($ie,0,8));
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=1 and $ie[1]!=0 or $ie[0]!=1 and $ie[1]!=1 or $ie[0]!=1 and $ie[1]!=5){return false;}
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            if($ieold=="11094402"){if($ie[8]==0 or $ie[8]==1){return true;}}
            if($resto==0){$dig=0;}
            if($resto==1){if($ieold>="10103105" and $ieold<="10119997"){$dig=1;}else{$dig=0;}}
            if($resto!=1 and $resto!=0){$dig=11-$resto;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="MA"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=1 or $ie[1]!=2){return false;}
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            if($resto==0 or $resto==1){$dig=0;}else{$dig=11-$resto;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="MT"){
            if(strlen($ie)!=11){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*3+$ie[1]*2+$ie[2]*9+$ie[3]*8+$ie[4]*7+$ie[5]*6+$ie[6]*5+$ie[7]*4+$ie[8]*3+$ie[9]*2;
            $resto=$soma%11;
            if($resto==0 or $resto==1){$dig=0;}else{$dig=11-$resto;}
            if($ie[10]!=$dig){return false;}else{return true;}
        }
        if($Estado=="MS"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=2 or $ie[1]!=8){return false;}
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            if($resto==0){$dig=0;}else{$dig=11-$resto;if($dig>9){$dig=0;}}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="MG"){
            if(strlen($ie)!=13){return false;}
            $ie2=$ie;
            $ie=substr($ie,0,3)."0".substr($ie,3,8);
            $ie=$this->stringsplit($ie,1);
            for($j=0;$j<11;$j=$j+2){
                $soma1=strval($ie[$j]*1);
                $soma2=$this->stringsplit($soma1,1);
                for($i=0;$i<strlen($soma1);$i++){
                    $soma3.=intval($soma2[$i]);
                }
                $soma1=strval($ie[$j+1]*2);
                $soma2=$this->stringsplit($soma1,1);
                for($i=0;$i<strlen($soma1);$i++){
                    $soma3.=intval($soma2[$i]);
                }
            }
            $tamanho=strlen($soma3);
            $ji=$this->stringsplit($soma3,1);
            $soma=0;
            for($i=0;$i<$tamanho;$i++){
                $soma=$soma+intval($ji[$i]);
            }
            $soma=$this->stringsplit($soma,1);
            $dig1=10-$soma[1];
            $soma=$ie2[0]*3+$ie2[1]*2+$ie2[2]*11+$ie2[3]*10+$ie2[4]*9+$ie2[5]*8+$ie2[6]*7+$ie2[7]*6+$ie2[8]*5+$ie2[9]*4+$ie2[10]*3+$dig1*2;
            $resto=$soma%11;
            $dig2=11-$resto;
            if($resto==0 or $resto==1){$dig2=0;}
            if($dig1!=$ie2[11] or $dig2!=$ie2[12]){return false;}else{return true;}

        }
        if($Estado=="PA"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=1 or $ie[1]!=5){return false;}
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            if($resto==0 or $resto==1){$dig=0;}else{$dig=11-$resto;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="PB"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10 or $dig==11){$dig=0;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="PR"){
            if(strlen($ie)!=10){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*3+$ie[1]*2+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig1=11-$resto;
            if($resto==1 or $resto==0){$dig1=0;}
            $soma=$ie[0]*4+$ie[1]*3+$ie[2]*2+$ie[3]*7+$ie[4]*6+$ie[5]*5+$ie[6]*4+$ie[7]*3+$dig1*2;
            $resto=$soma%11;
            $dig2=11-$resto;
            if($resto==1 or $resto==0){$dig2=0;}
            if($ie[8]!=$dig1 or $ie[9]!=$dig2){return false;}else{return true;}
        }
        if($Estado=="PE"){
            if(strlen($ie)!=14){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*5+$ie[1]*4+$ie[2]*3+$ie[3]*2+$ie[4]*1+$ie[5]*9+$ie[6]*8+$ie[7]*7+$ie[8]*6+$ie[9]*5+$ie[10]*4+$ie[11]*3+$ie[12]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig>9){$dig=$dig-10;}
            if($ie[13]!=$dig){return false;}else{return true;}
        }
        if($Estado=="PI"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10 or $dig==11){$dig=0;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="RJ"){
            if(strlen($ie)!=8){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*2+$ie[1]*7+$ie[2]*6+$ie[3]*5+$ie[4]*4+$ie[5]*3+$ie[6]*2;
            $resto=$soma%11;
            if($resto<=1){$dig=0;}else{$dig=11-$resto;}
            if($ie[7]!=$dig){return false;}else{return true;}
        }
        if($Estado=="RN"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $multi=$soma*10;
            $dig=$multi%11;
            if($dig==10){$dig=0;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="RS"){
            if(strlen($ie)!=10){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*2+$ie[1]*9+$ie[2]*8+$ie[3]*7+$ie[4]*6+$ie[5]*5+$ie[6]*4+$ie[7]*3+$ie[8]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10 or $dig==11){$dig=0;}
            if($ie[9]!=$dig){return false;}else{return true;}
        }
        if($Estado=="RO"){
            if(strlen($ie)!=14){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*6+$ie[1]*5+$ie[2]*4+$ie[3]*3+$ie[4]*2+$ie[5]*9+$ie[6]*8+$ie[7]*7+$ie[8]*6+$ie[9]*5+$ie[10]*4+$ie[11]*3+$ie[12]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10 or $dig==11){$dig=$dig-10;}
            if($ie[13]!=$dig){return false;}else{return true;}
        }
        if($Estado=="RR"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[0]!=2 or $ie[1]!=4){return false;}
            $soma=$ie[0]*1+$ie[1]*2+$ie[2]*3+$ie[3]*4+$ie[4]*5+$ie[5]*6+$ie[6]*7+$ie[7]*8;
            $dig=$soma%9;
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="SC"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($resto==0 or $resto==1){$dig=0;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="SP"){
            if($charUm=="P"){
                if(strlen($ie)!=12){return false;}
                $ie=$this->stringsplit($ie,1);
                $soma=$ie[0]*1+$ie[1]*3+$ie[2]*4+$ie[3]*5+$ie[4]*6+$ie[5]*7+$ie[6]*8+$ie[7]*10;
                $resto=strval($soma%11);
                $dig=substr($resto,-1);
                if($ie[8]!=$dig){return false;}else{return true;}
            }else{
                if(strlen($ie)!=12){return false;}
                $ie=$this->stringsplit($ie,1);
                $soma=$ie[0]*1+$ie[1]*3+$ie[2]*4+$ie[3]*5+$ie[4]*6+$ie[5]*7+$ie[6]*8+$ie[7]*10;
                $resto=strval($soma%11);
                $dig1=substr($resto,-1);
                $soma=$ie[0]*3+$ie[1]*2+$ie[2]*10+$ie[3]*9+$ie[4]*8+$ie[5]*7+$ie[6]*6+$ie[7]*5+$dig1*4+$ie[9]*3+$ie[10]*2;
                $resto=strval($soma%11);
                $dig2=substr($resto,-1);
                if($ie[8]!=$dig1 or $ie[11]!=$dig2){return false;}else{return true;}
            }
        }
        if($Estado=="SE"){
            if(strlen($ie)!=9){return false;}
            $ie=$this->stringsplit($ie,1);
            $soma=$ie[0]*9+$ie[1]*8+$ie[2]*7+$ie[3]*6+$ie[4]*5+$ie[5]*4+$ie[6]*3+$ie[7]*2;
            $resto=$soma%11;
            $dig=11-$resto;
            if($dig==10 or $dig==11){$dig=0;}
            if($ie[8]!=$dig){return false;}else{return true;}
        }
        if($Estado=="TO"){
            if(strlen($ie)!=11){return false;}
            $ie=$this->stringsplit($ie,1);
            if($ie[2]!=0 and $ie[2]!=9 or $ie[3]!=1 and $ie[3]!=2 and $ie[3]!=3 and $ie[3]!=9){return false;}
            $soma=$ie[0]*9+$ie[1]*8+$ie[4]*7+$ie[5]*6+$ie[6]*5+$ie[7]*4+$ie[8]*3+$ie[9]*2;
            $resto=$soma%11;
            if($resto<2){$dig=0;}elseif($resto>=2){$dig=11-$resto;}
            if($ie[10]!=$dig){return false;}else{return true;}
        }
    }


    // validacao de PIS/PASEP
    function valida_PIS($pis){
        //Retirar todos os caracteres que nao sejam 0-9
        $s="";
        for ($x=1; $x<=strlen($pis); $x=$x+1)
        {
            $ch=substr($pis,$x-1,1);
            if (ord($ch)>=48 && ord($ch)<=57)
            {
                $s=$s.$ch;
            }
        }
        $pis=$s;

        if(strlen($pis)!=11){return false;}
        $pis=$this->stringsplit($pis,1);
        $soma=$pis[9]*2+$pis[8]*3+$pis[7]*4+$pis[6]*5+$pis[5]*6+$pis[4]*7+$pis[3]*8+$pis[2]*9+$pis[1]*2+$pis[0]*3;
        $resto=$soma%11;
        $dig=11-$resto;
        if($dig>9){$dig=0;}
        if($pis[10]!=$dig){return false;}else{return true;}
    }

    function valida_TitEleitor($tit){
        $s="";
        for ($x=1; $x<=strlen($tit); $x=$x+1)
        {
            $ch=substr($tit,$x-1,1);
            if (ord($ch)>=48 && ord($ch)<=57)
            {
                $s=$s.$ch;
            }
        }
        $tit=$s;
        while (strlen($tit)!=12){
            //poe zeros
            $tit="0".$tit;
        }
        $tit=$this->stringsplit($tit,1);
        $uf=strval($tit[8]).strval($tit[9]);
        if(intval($uf)<1 or intval($uf)>28){

            return false;}

        //calcula digito 1
        $soma=$tit[0]*9+$tit[1]*8+$tit[2]*7+$tit[3]*6+$tit[4]*5+$tit[5]*4+$tit[6]*3+$tit[7]*2;
        $resto=$soma%11;
        if($resto==1 or $resto==0){
            $dig1=0;
        }else{
            $dig1=11-$resto;
        }

        //calcula digito 2
        $soma=$dig1*2+$tit[9]*3+$tit[8]*4;
        $resto=$soma%11;
        if($resto==1 or $resto==0){
            $dig2=0;
        }else{
            $dig2=11-$resto;
        }

        // verifica digitos com a digitaï¿½o
        if($tit[10]!=$dig1 or $tit[11]!=$dig2){return false;}else{return true;}
    }

    function valida_CEP($cep, $cadastro, $entry_Estado){
        if(is_a($entry_Estado,'GtkEntry')){
            $Estado=strtoupper($entry_Estado->get_text());
        }elseif(is_a($entry_Estado,'GtkLabel')){
            $Estado=strtoupper($entry_Estado->get());
        }else{
            $Estado=$entry_Estado;
        }

        $cep=$this->DeixaSoNumero($cep);
        if(strlen($cep)!=8){return false;}
        $cep1=intval(substr($cep,0,3));
        //echo "cep1=$cep1";
        if($cep>1000000){
            if($Estado=="SP" and $cep1>=10  and $cep1<=199){return true;}
            elseif($Estado=="RJ" and $cep1>=200 and $cep1<=289){return true;}
            elseif($Estado=="ES" and $cep1>=290 and $cep1<=299){return true;}
            elseif($Estado=="MG" and $cep1>=300 and $cep1<=399){return true;}
            elseif($Estado=="BA" and $cep1>=400 and $cep1<=489){return true;}
            elseif($Estado=="SE" and $cep1>=490 and $cep1<=499){return true;}
            elseif($Estado=="PE" and $cep1>=500 and $cep1<=569){return true;}
            elseif($Estado=="AL" and $cep1>=570 and $cep1<=579){return true;}
            elseif($Estado=="PB" and $cep1>=580 and $cep1<=589){return true;}
            elseif($Estado=="RN" and $cep1>=590 and $cep1<=599){return true;}
            elseif($Estado=="CE" and $cep1>=600 and $cep1<=639){return true;}
            elseif($Estado=="PI" and $cep1>=640 and $cep1<=649){return true;}
            elseif($Estado=="MA" and $cep1>=650 and $cep1<=659){return true;}
            elseif($Estado=="PA" and $cep1>=660 and $cep1<=688){return true;}
            elseif($Estado=="AM" and ($cep1>=690 and $cep1<=692 or $cep1>=694 and $cep1<=698)){return true;}
            elseif($Estado=="AP" and $cep1=689){return true;}
            elseif($Estado=="RR" and $cep1=693){return true;}
            elseif($Estado=="AC" and $cep1=699){return true;}
            elseif(($Estado=="DF" or $Estado=="GO") and $cep1>=0 and $cep1<=999){return true;}
            elseif($Estado=="TO" and $cep1>=770 and $cep1<=779){return true;}
            elseif($Estado=="MT" and $cep1>=780 and $cep1<=788){return true;}
            elseif($Estado=="MS" and $cep1>=790 and $cep1<=799){return true;}
            elseif($Estado=="RO" and $cep1=789){return true;}
            elseif($Estado=="PR" and $cep1>=800 and $cep1<=879){return true;}
            elseif($Estado=="SC" and $cep1>=880 and $cep1<=899){return true;}
            elseif($Estado=="RS" and $cep1>=900 and $cep1<=999){return true;}
            elseif($Estado=="RJ" and $cep1>=200 and $cep1<=289){return true;}
            else{return false;}
        }else{
            return false;
        }
    }
    function valida_data($texto,$vazia=false){
		// $texto = a data a ser validada. Se especial = true entao $texto deve ser um GtkEntry ou GtkLabel
		// $vazia = se aceita data vazia

        if(is_a($texto,'GtkEntry') or is_a($texto,'GtkLabel')){
			$data=$texto->get_text();
		}else{
			$data=$texto;
		}
		$datatrans=explode("-",$data);
		$valida = @checkdate($datatrans[1],$datatrans[0],$datatrans[2]);
		return $valida;
    }

    // valida email
    function valida_email($email){
        if(!ereg("^([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$",$email))
        {
            return false;
        } else {
            return true;
        }
    }
	function pegaNumero($widget,$ponto=true){
		if(is_a($widget,'GtkEntry') or is_a($widget,'GtkLabel')){
			$numero=$widget->get_text();
		}else{
			$numero=$widget;
		}
		$numero=str_replace(" ","",$numero);
		//if($ponto) $numero=str_replace(".","",$numero);
		$ponto_ou_virgula=substr($numero,-3,1);
		if($ponto_ou_virgula=="," or $ponto){
			//echo "virgula";
			$numero=str_replace(".","",$numero);
		}elseif($ponto_ou_virgula=="."){
			$numero=str_replace(",","",$numero);
		}
		$numero=str_replace(",",".",$numero);
		$numero=str_replace('R$',"",$numero);
		$numero=str_replace('$',"",$numero);
		$numero=str_replace("%","",$numero);
		$numero=floatval($numero);
		if(empty($numero)) $numero=0;
		return $numero;
	}

    function corrigeNumero($algumacoisa,$formato,$classe=null,$entry=null){
        if(empty($classe)){
            // usado no clist de orcamento
            $retorna=true;
            $texto=$algumacoisa;
        }else{
            if(empty($entry)){
               msg('Falta paramentro entry da funcao corrigeNumero');
            }else{
                $texto=$entry->get_text();
            }
        }
        if(empty($formato)){
            msg('Falta paramentro formato da funcao corrigeNumero');
        }elseif($formato=="moeda"){
       			$texto=$this->mascara2($texto,'moeda');
                /*$texto=$this->DeixaSoNumeroDecimal($texto,2);
                $texto=str_replace(",", ".", $texto); // coloca o ponto separador de decimal
                $texto=number_format($texto, 2, ',', '.'); // converte para ter 2 casas decimais
                $texto='R$ '.str_replace(".", ",", $texto); // volta a virgula */
        }elseif($formato=="moeda4"){
        			$texto=$this->mascara2($texto,'moeda4');
                /*$texto=$this->DeixaSoNumeroDecimal($texto,4);
                $texto=str_replace(",", ".", $texto); // coloca o ponto separador de decimal
                $texto=number_format($texto, 4, ',', '.'); // converte para ter 2 casas decimais
                $texto='R$ '.str_replace(".", ",", $texto); // volta a virgula*/
        }elseif($formato=="decimal2"){
            $tamanho=strlen($texto);
            if($tamanho<3){
                $texto=$this->DeixaSoNumero($texto);
                $texto=number_format($texto, 2, '.', '');
            }
        }elseif($formato=="decimal4"){
            $tamanho=strlen($texto);
            if($tamanho<5){
                $texto=$this->DeixaSoNumero($texto);
                $texto=number_format($texto, 4, '.', '');
            }
        }elseif($formato=="decimal3"){
            $tamanho=strlen($texto);
            if($tamanho<5){
                $texto=$this->DeixaSoNumero($texto);
                $texto=number_format($texto, 3, '.', '');
            }
        }
        elseif($formato=="virgula" or $formato=="virgula2"){
            //echo "antes=$texto";
            $texto=str_replace(",", ".", $texto); // coloca o ponto separador de decimal
            //$texto=number_format($texto, 2, '.', ',');
            $texto=number_format($texto, 2, ',', ''); // converte para ter 2 casas decimais
            //$texto=str_replace(".", ",", $texto); // volta a virgula
            //echo "depois=$texto";
        }
        elseif($formato=="virgula4"){
            $texto=str_replace(",", ".", $texto); // coloca o ponto separador de decimal
            //$texto=number_format($texto, 4, '.', ',');
            $texto=number_format($texto, 4, ',', ''); // converte para ter 4 casas decimais
            //$texto=str_replace(".", ",", $texto); // volta a virgula
        }elseif($formato=="virgula3"){
            $texto=str_replace(",", ".", $texto); // coloca o ponto separador de decimal
            //$texto=number_format($texto, 3, '.', ',');
            $texto=number_format($texto, 3, ',', ''); // converte para ter 4 casas decimais
            //$texto=str_replace(".", ",", $texto); // volta a virgula
        }
        elseif($formato=="data"){
            $texto=substr($texto,8,2).'-'.substr($texto,5,2).'-'.substr($texto,0,4);
        }elseif($formato=="dataiso"){
            $texto=substr($texto,6,4).'-'.substr($texto,3,2).'-'.substr($texto,0,2);
        }
        if(!$retorna){
            $entry->set_text($texto);
        }else{
            return $texto;
        }

    }

    function mascara2($texto,$formato){
        //echo "antes:$texto ";
        if($formato=="moeda" or $formato=="virgula" or $formato=="virgula2"){
			if($formato=="moeda") $RR="R$ ";
            $texto=number_format($texto, 2, '.', ',');
            $negativo=false;
            if($texto<0) $negativo=true;
            $texto=$this->DeixaSoNumeroDecimal($texto,2);
            $texto=$RR.number_format($texto, 2, ',', '.');
            if($negativo) $texto="-".$texto;

        }elseif($formato=="moeda4"){
            $texto=number_format($texto, 4, '.', ',');
            if($texto<0) $negativo=true;
            $texto=$this->DeixaSoNumeroDecimal($texto,4);
            $texto='R$ '.number_format($texto, 4, ',', '.');
            if($negativo) $texto="-".$texto;
        /*}elseif($formato=="virgula" or $formato=="virgula2"){
			$this->corrigeNumero($texto,'virgula');
            $texto=number_format($texto, 2, '', ',');
            if($texto<0) $negativo=true;
            $texto=$this->DeixaSoNumeroDecimal($texto,2);
            $texto=number_format($texto, 2, '', '.');
            if($negativo) $texto="-".$texto;*/
        }elseif($formato=="cep"){
            $texto=$this->DeixaSoNumero($texto);
            //37900000
            //01234567
            $texto=substr($texto,0,2).".".substr($texto,2,3)."-".substr($texto,5,3);
        }
        //echo "depois:$texto \n";
        return $texto;
    }

}
?>
