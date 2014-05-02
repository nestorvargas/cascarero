<?php
/* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Library General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*  Copyright  2007 Jose Antonio Cely Saidiza
*  Email jose.cely@gmail.com
*  Bogotá Colombia
****************************************************************************/



/**
* objeto para creación de formularios, por default arma un formulario en base a una tabla MySQL
* aunque puede funcionar sin tabla especificando campos virtuales, OJO, depende de ezsql!
* Ejemplo de uso básicos:
* <code>
* $formlinea = new makeform('Linea', 'makeform');
* $formlinea->setselect('id_empresa,Empresa,id_empresa,Nombre'); // menu con la tabla linea
* $formlinea->makejavascript();
* $formlinea->showform();
* </code>
* @author Jose Antonio Cely Saidiza <jose.cely@gmail.com>
* @version 0.7
* TODO: 
* - Funciones de depuración
* - Documentar más a fondo
*/
class makeform {

	var $table;
	var $selects;
	var $hiddencamp;
	var $hiddenform;
	var $moreinfo;
	var $defaultvalue;
	var $dynamiclist;		
	var $depselectr;
	var $depselectv;
	var $reqselect;
	var $radios;

	var $primarykey;
	var $primarykeyauto;	
	var $readonly;	 	
	var $nombres;
	var $variable;
	var $tamanovariable; 
	var $nulo; 
	var $default;
	var $nocampos;
	var $newname;
	var $javscript;
	var $fileform;
	var $showashtml;
        var $firstvalueselect;
	
	var $formname = 'form';
	var $formid = 'form';
	var $title = 'FOMULARIO';
	var $infoboton;
	var $columns = 1;
	var $namebutton = 'Agregar';
	var $styletable = 'tablemakeform';
	var $stylefoother = 'tablemakefoother';
	var $styleheader = 'tablemakeheader';
	var $stylerow = 'tablemakerow';
	var $stylebutton = 'tablemakebutton';
	var $scriptajax = 'ajax.php';
	var $commentsafter;
	var $commentsbefore;	
	var $close = TRUE;
	var $submit = TRUE;
	var $open = TRUE;
	var $debug = FALSE;

	var $printjavascript=FALSE;	
	
	/**
        * Inicializa formulario, si no se especifica tabla se asume una tabla con un campo llamado 'id', tipo small int
        * @param    string  $table Nombre de la tabla
        * @param    string  $database Nombre de la base de datos (obsoleto)
        */
	function __construct($table){
                global $db;
		if($table!='') {

			$infotable = $db->get_results("describe $table");
			$i=0;
			foreach ($infotable as $datatable) {		// aca creo los arrays de caracteristicas
				$this->nombres[$i] = $datatable->Field;
				
				$nombrecampo = explode('(', $datatable->Type);		//  parto el nombre de variable por los parentesis
				$this->variable[$i] = $nombrecampo[0];
				$tamanovar = explode(')', $nombrecampo[1]);
				$this->tamanovariable[$i] = $tamanovar[0];
				$this->nulo[$i] = $datatable->Null;
				if ($datatable->Key=='PRI') {	// aca marco la llave primaria
					$this->primarykey = $datatable->Field; 
				}
				
				if ($datatable->Key=='PRI' AND $datatable->Extra=='auto_increment') {	// aca marco los campos autoincrement como read only
					$this->readonly[] = $datatable->Field;
					$this->primarykeyauto = $datatable->Field; 
				}
				$this->default[$i] = $datatable->Default;
				$i++;
			}
			$this->nocampos=$i;
			
		} else {	// si es un formulario sin tablas
			$this->table='-';
			$this->nombres[] = 'id';
			$this->variable[] = 'smallint';
			$this->tamanovariable[] = '6';
			$this->readonly[] = 'id';
			$this->primarykey = 'id';
			$this->primarykeyauto = 'id';						
			$this->nocampos=1;
			$this->hiddencamp[]='id';		
		}
		$this->infoboton= "$table&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;";		
	}

	/**
        * Cambia el nombre del formulario, el nombre por defecto es 'form'
        * @param    string  $formname Nombre del formulario
        */
	function setformname($formname) {
		$this->formname=$formname;
		$this->formid=$formname;
	}

	/**
        * Cambia el id del formulario, el id por defecto es 'form'
        * @param    string  $formid id del formulario
        */
	function setformid($formid) {
		$this->formid=$formid;
	}
	
	/**
        * Establece si debe abrir las etiquetas <form>, util cuando se estan fusionando dos formularios
        * @param    Boolean     $state  Estado
        */        
	function setopen($state) {
		$this->open=$state;
	}

	/**
        * Establece si debe cerrar las etiquetas </form>, util cuando se estan fusionando dos formularios
        * @param    Boolean     $state  Estado
        */     
	function setclose($state) {
		$this->close=$state;
	}
	
	/**
        * Cambia el nombre de la url que invocará el script ajax, url por defecto 'ajax.php'
        * @param    string  $scriptajax         Nombre/URL al script
        */             
	function setscriptajax($scriptajax) {
		$this->scriptajax=$scriptajax;
	}
		
	/**
        * Establece si crea el boton de envio
        * @param    Boolean $submit  Estado
        */ 
	function submit($submit) {
		$this->submit=$submit;
	}			

	/**
        * Muestra en pantalla el array de campos y propiedades cuando se imprima el formulario en pantalla
        */ 
	function debug() {
		$this->debug=TRUE;
	}
	
	/**
        * Establece un campo del formulario como solo lectura
        * @param   string  $campo Nombre del campo
        */         
	function setreadonly($campo) {
		$this->readonly[]=$campo;
	}

	/**
        * Si el campo esta definido como select, establece los valores que lo compongan como radio buttons
        * @param   string  $campo Nombre del campo
        */ 
	function setradioselect($campo) {
		$this->radios[]=$campo;
	}
	
	/**
        * Establece un valor por defecto si el campo esta definido como select
        * @param   string  $campo Nombre del campo
        * @param   string  $value1 valor del select
        * @param   string  $value2 Nombre en pantalla del select
        */         
	function firstvalueselect($campo, $value1='', $value2='...') { // para un select cuando se quiere mostrar un primer campo
		$this->firstvalueselect[$campo]="<option value=\"$value1\">$value2</option>\n";
	}
        
	/**
        * Crea un nuevo campo en le formulario, justo despues de un campo ya existente
        * @param   string  $before Nombre del campo existente
        * @param   string  $name Nombre del campo nuevp
        * @param   string  $var Tipo de variable del campo
        * @param   string  $size Tamaño de la variable
        * @param   string  $null Varibale Nula o no nula
        */          
	function newcamp($before,$name,$var,$size,$null) {
		if($null=='NULL' OR $null == 'null') { // si es nulo
			$null='YES';
		} else {
			$null='NO';
		}
		$b=0;
		$sis=count($this->nombres);	
		
		for ($i=0; $i<=$sis; $i++) {

			$newnombres[$b]=$this->nombres[$i];
			$newvariable[$b]=$this->variable[$i];
			$newtamanovariable[$b]=$this->tamanovariable[$i];
			$newnulo[$b]=$this->nulo[$i];
			$defaultt[$b]=$this->default[$i];
			$b++;			
			if ($before==$this->nombres[$i]) {
				$newnombres[$b]=$name;
				$newvariable[$b]=$var;
				$newtamanovariable[$b]=$size;
				$newnulo[$b]=$null;
				$defaultt[$b]='';
				$b++;					
			}
		}	// actualizo arrays y numero de campos
		$this->nombres=$newnombres;
		$this->variable=$newvariable;			
		$this->tamanovariable=$newtamanovariable;
		$this->nulo=$newnulo;
		$this->default=$defaultt;
		$this->nocampos++;
	}

	/**
        * Permite establecer información en html junto al boton de envio del formulario
        * @param   string  $info html
        */ 
	function setinfoboton($info) {
		$this->infoboton=$info;
	}
	
	/**
        * Permite establecer un titulo al formulario
        * @param   string  $title html
        */         
	function settitle($title) {
		$this->title=$title;
	}

	/**
        * El formulario por defecto es de una columna, con esta llamada podemos establecer un formulario dividido en más columnas
        * @param   int  $columns html
        */  
	function setcolumns($columns) {
		$this->columns=$columns;
	}
	
	/**
        * Permite cambiar el nombre del boton del formulario, por defecto es 'Agregar'
        * @param   string  $namebutton nuevo nombre del boton
        */         
	function setnamebutton($namebutton) {
		$this->namebutton=$namebutton;
	}

	/**
        * Establece mostrar la información de un campo como html
        * @param   string  $campo Nombre del campo
        */
	function setashtml($campo) {
		$this->showashtml[]=$campo;
	}
	
	function setdynamiclist($campo) {		                        // establece campos como lista dinamica
		$this->dynamiclist[]=$campo;
	}
	
	/**
        * No muestra un campo en el fomulario ni crea un form hidden
        * @param   string  $hiddencamp Nombre del campo
        */	
	function sethidden($hiddencamp) {
		$this->hiddencamp[]=$hiddencamp;
	}

	/**
        * No muestra un campo en el fomulario, CREA un form hidden
        * @param   string  $hiddenform Nombre del campo
        */
	function sethiddenform($hiddenform) {
		$this->hiddenform[]=$hiddenform;
	}

	function setselect($setselect) {		                        // campos a hacer select
		$this->selects[]=$setselect;
	}

	/**
        * Establece cualquier campo como un form tipo file
        * @param   string  $campo Nombre del campo
        */
	function setfileform($campo) {
		$this->fileform[]=$campo;
	}
        
	/**
        * Establece un valor por defecto para un campo
        * @param   string  $campo Nombre del campo
        * @param   string  $value Valor del campo
        */        
	function setdefaultvalue($campo,$value) {
		$this->defaultvalue[$campo]=$value;
	}

	/**
        * Cambia el nombre descriptivo de un campo
        * @param   string  $campo Nombre del campo
        * @param   string  $name Nombre descriptivo nuevo
        */  
	function setnewname($campo,$name) {
		$this->newname[$campo]=$name;
	}	
	
        /**
        * Crea contenido dentro de las etiquetas de un campo de un formulario, muy util por ejemplo para agregar javascripts
        * @param   string  $campo Nombre del campo
        * @param   string  $javascript Javascript o contenido a agregar dentro de la etiqueta
        */  
	function putjavascript($campo,$javascript) {
		$this->javascript[$campo]=$javascript;
	}	
	
        /**
        * Permite ingresar html antes de la descripcion del formulario de un campo
        * @param   string  $campo Nombre del campo
        * @param   string  $value Contenido a agregar
        */ 	
	function setcommentbefore($campo,$value) {
		$this->commentsbefore[$campo]=$value;
	}

        /**
        * Permite ingresar html despues  de la descripcion del formulario de un campo
        * @param   string  $campo Nombre del campo
        * @param   string  $value Contenido a agregar
        */ 
	function setcommentafter($campo,$value) {
		$this->commentsafter[$campo]=$value;
	}
	
        /**
        * ????  Permite ingresar html despues  del formulario de un campo
        * @param   string  $campo Nombre del campo
        * @param   string  $value Contenido a agregar
        */         
	function setmoreinfo($moreinfo,$datainfo) {
		$this->moreinfo[$moreinfo]=$datainfo;
	}
	
	function depselectreal($depselectr) {		                        // configurar ajax depselect reales
		$this->depselectr[]=$depselectr;
	}	

	function reqselect($reqselect) {		                        // configurar ajax depselect reales
		$this->reqselect[]=$reqselect;
	}
	
	function depselectvirtual($depselectv) {		                // configurar ajax depselect un campo virtual
		$this->depselectv[]=$depselectv;
	}
	
        /**
        * Retorna el numero de campos
        * @return integer 
        */           
	function getncamps() {
		return $this->nocampos;
	}

        /**
        * Retorna el campo llave primaria
        * @return string 
        */   
	function getprimarykey() {
		return $this->primarykey;
	}


        /**
        * Crea el codigo javascript automáticamente deacuerdo a los campos
        * - Los campos marcados como NULL, crea el codigo para checkerlos
        * - Valida enteros y flotantes
        * - Valida direcciones de email, para esto el campo debe llamarse 'email', o 'e-mail' o 'mail'
        */ 
	function makejavascript() {		
		$dates=1; 
		$this->printjavascript=TRUE;
		echo "<script type=\"text/javascript\">
		function validar".$this->formname."(formulario) {\n";
		
		echo "	function validarEntero(valor){ 
		     if (valor!='') {
			     valor = parseInt(valor);
			      if (isNaN(valor)) {         
			            return (true); 
			      }else{ 
			            return (false); 
			      } 
		      } else {
		      		return (false);
		      }
		}\n";
		echo "	function validarFlotante(valor){ 
		     if (valor!='') {
			     valor = parseFloat(valor);
			      if (isNaN(valor)) {         
			            return (true); 
			      }else{ 
			            return (false); 
			      } 
		      } else {
		      		return (false);
		      }
		}\n";		
		
		for ($i = 0; $i < $this->nocampos; $i++) {  // en este for recorre el formulariovalidarformz
			
			$hiddencamp = FALSE;
			$nombrecampo=$this->nombres[$i];
			if ($this->hiddencamp!=NULL) {
				foreach ($this->hiddencamp as $hidden) {	// busco si el campo es escondido no ceckeo validacion
					if ($hidden==$this->nombres[$i]){
						$hiddencamp = TRUE;	
					}
				}
			}
			if($this->showashtml!=NULL){
				foreach ($this->showashtml as $value) {		// marco si debo mostrar como html es como si fuera hidden
					if ($value==$this->nombres[$i]){
						$hiddencamp = TRUE;
					}
				}
			}
                        if($this->fileform!=NULL){
                                foreach ($this->fileform as $clave) {		// si es un menu file
                                        if ($clave==$this->nombres[$i]){
                                                $hiddencamp = TRUE;
                                        }
                                }
			}  
			$namecamp=$this->nombres[$i];
			if($this->newname!=NULL){
				foreach ($this->newname as $clave => $value) {		// si reemplazo el nombre
					if ($clave==$this->nombres[$i]){
						$namecamp = "$value";
					}
				}
			}
			
			if ($this->variable[$i]=="date" OR $this->variable[$i]=="datetime") {		// si es campo de fecha
				$nombrecampo="date"."$dates";
				$dates++;
			}	
					
			if($this->hiddenform!=NULL){
				foreach ($this->hiddenform as $value) {		// si es hidden ignora
					if ($value==$this->nombres[$i]){
						$hiddencamp = TRUE;
					}
				}
			}
			
		
			if (!$hiddencamp) {	// si el campo NO es oculto		
				if ($this->nulo[$i]!='YES'){  	// Para crear validacion de no nulo
					echo "if (formulario.".$nombrecampo.".value.length==0) {
						    alert(\"".$namecamp." No puede ser vacio\");
						    formulario.".$nombrecampo.".focus();
						    return (false);
						  }\n";
				}

			
				if ($this->variable[$i]=="bigint" OR $this->variable[$i]=="int" OR $this->variable[$i]=="smallint" OR $this->variable[$i]=="tinyint") {// si es campo es entero
					echo "".$nombrecampo." = formulario.".$nombrecampo.".value;
					var es".$nombrecampo." = validarEntero(".$nombrecampo.");
					if (es".$nombrecampo."){
						alert(\"Tiene que introducir un número entero en ".$namecamp.".\");
						formulario.".$nombrecampo.".focus();
						return (false);
					}\n";
				}
				if ($this->variable[$i]=="float" OR $this->variable[$i]=="double") {		// si es campo es flotante
					echo "".$nombrecampo." = formulario.".$nombrecampo.".value;
					var es".$nombrecampo." = validarFlotante(".$nombrecampo.");
					if (es".$nombrecampo."){
						alert(\"Tiene que introducir un número entero/flotante en ".$namecamp.".\");
						formulario.".$nombrecampo.".focus();
						return (false);
					}\n";
				}
				
				if ($this->nombres[$i]=='email' OR $this->nombres[$i]=='e-mail' OR $this->nombres[$i]=='mail') { // emails
					echo "if(formulario.".$nombrecampo.".value != '') {
						if (formulario.".$nombrecampo.".value.indexOf('@', 1) == -1 || formulario.".$nombrecampo.".value.indexOf('.', formulario.".$nombrecampo.".value.indexOf('@', 0)) == -1) {
						alert(\"Dirección de e-mail inválida\"); 
						formulario.".$nombrecampo.".focus(); 
						return (false);
						}
					}\n";
				}								
 			}
		}
		echo "
		   return (true); 
		}
		</script>\n";	
	}	
	
        /**
        * Crea el javascript/ajaX necesario para los selects dependientes
        */         
	function makeajax() {
		echo "<script type=\"text/javascript\">\n";		
		if($this->depselectv!=NULL){		// si hay un menu depselect virtual
			foreach ($this->depselectv as $depselect) {
				$selectcontents = explode (',', $depselect);
			
				echo "function get".$selectcontents[0]."(sel){
					var ".$selectcontents[1]." = sel.options[sel.selectedIndex].value;
					document.getElementById('".$selectcontents[0]."').options.length = 0;
					if(".$selectcontents[1].".length>0){
						ajax.requestFile = '".$this->scriptajax."?".$selectcontents[1]."='+".$selectcontents[1].";
						ajax.onCompletion = create".$selectcontents[0].";
						ajax.runAJAX();
					}
				}

				function create".$selectcontents[0]."()
				{
					var obj = document.getElementById('".$selectcontents[0]."');
					eval(ajax.response);
				}
				";
			}
		}

		if($this->depselectr!=NULL){		// si hay un menu depselect real
			foreach ($this->depselectr as $depselect) {
				$selectcontents = explode (',', $depselect);
			
				echo "function get".$selectcontents[3]."(sel){
					var ".$selectcontents[0]." = sel.options[sel.selectedIndex].value;
					document.getElementById('".$selectcontents[3]."').options.length = 0;
					if(".$selectcontents[0].".length>0){
						ajax.requestFile = '".$this->scriptajax."?".$selectcontents[0]."='+".$selectcontents[0].";
						ajax.onCompletion = create".$selectcontents[3].";
						ajax.runAJAX();
					}
				}

				function create".$selectcontents[3]."()
				{
					var obj = document.getElementById('".$selectcontents[3]."');
					eval(ajax.response);
				}
				";
			}
		}
		
		if($this->reqselect!=NULL){		// si hay un menu depselect real
			foreach ($this->reqselect as $depselect) {
				$selectcontents = explode (',', $depselect);
			
				echo "function get".$selectcontents[0]."(sel){
					var ".$selectcontents[1]." = sel.options[sel.selectedIndex].value;
					document.getElementById('".$selectcontents[0]."').options.length = 0;
					if(".$selectcontents[1].".length>0){
						ajax.requestFile = '".$this->scriptajax."?".$selectcontents[1]."='+".$selectcontents[1].";
						ajax.onCompletion = create".$selectcontents[0].";
						ajax.runAJAX();
					}
				}

				function create".$selectcontents[0]."()
				{
					var obj = document.getElementById('".$selectcontents[0]."');
					eval(ajax.response);
				}
				";
			}
		}		
		echo "</script>\n";		
	}	

        /**
        * Imprime el formulario en pantalla, funcion final y definitiva, usar antes del final
        */ 
	function showform() {		// muestra el formualrio
                global $db;

                $colspann=2*$this->columns;
						
		if ($this->open) {	// si habre el form						
		echo "\n<table id=\"table_results\" align=\"center\" class=\"".$this->styletable."\"><tbody>\n"; // encabezado tabla
			echo "<tr>\n" ;		// aca inicia la tabla con un formulario para ingresar campos
			echo "<td colspan=\"$colspann\" class=\"".$this->styleheader."\"><div align=\"center\">".$this->title."</div></td>\n";		// aca son los encabezados de la tabla, los nopmbre s de los campos id=\"".$this->formid."\"
			echo "</tr>\n";
			if($this->printjavascript) {	// si hay javascript creado
				$javascripaction="onSubmit=\"return validar".$this->formname."(this)\"";
			} else {
				$javascripaction="";				
			}
                        if ($this->fileform!=NULL) {
                                echo "<form name=\"".$this->formname."\" id=\"".$this->formid."\" $javascripaction action=".$_SERVER["PHP_SELF"]." enctype=\"multipart/form-data\" method=\"POST\">";                        
                        } else {
                                echo "<form name=\"".$this->formname."\" id=\"".$this->formid."\" $javascripaction action=".$_SERVER["PHP_SELF"]." method=\"POST\">";
                        }
		}
		
		$dates=0; // para enumerar los calendarios
		$columnss=0;
		
		for ($i = 0; $i < $this->nocampos; $i++) {  // en este for arma el formulario
			
			$doopentr=FALSE;
						
			if ($columnss==0) {	// para establecer si imprime TR o no
				$doopentr=TRUE;
			}
			//

			$dynamiclist="";
			if($this->dynamiclist!=NULL){
				foreach ($this->dynamiclist as $value) {		// marco si debe armar el javascript de dynamic list   ************
					if ($value==$this->nombres[$i]){
						$dynamiclist = " id=\"".$this->nombres[$i]."\" onkeyup=\"ajax_showOptions(this,'get".$this->nombres[$i]."',event)\"";
					}
				}
			}
			
			$showhtml=FALSE;
			if($this->showashtml!=NULL){
				foreach ($this->showashtml as $value) {		// marco si debo mostrar como html
					if ($value==$this->nombres[$i]){
						$showhtml = TRUE;
					}
				}
			}
								
			$defaultvalues=NULL;
			$selected="";
			if($this->defaultvalue!=NULL){
				foreach ($this->defaultvalue as $clave => $value) {		// cargo valore defecto
					if ($clave==$this->nombres[$i]){
						$selected=$value;
						if (($this->variable[$i]=="text")OR((($this->variable[$i]=="varchar" || $this->variable[$i]=="char")) AND $this->tamanovariable[$i] > 130) OR $showhtml){
							$defaultvalues = "$value";							
						} else {
							$defaultvalues = "value=\"$value\"";
						}
					}
				}
			}

			$hiddenform=FALSE;
			if($this->hiddenform!=NULL){
				foreach ($this->hiddenform as $value) {		// cargo valor a hidden en form
					if ($value==$this->nombres[$i]){
						$hiddenform = "$value";
					}
				}
			}
			
			if($this->commentsbefore!=NULL){
				foreach ($this->commentsbefore  as $clave => $value) {		// valores de info antes
					if ($clave==$this->nombres[$i]){
						if ($doopentr) {
							echo "<tr  class=\"".$this->stylerow."\">\n";
						}
						echo "<td colspan=\"2\">\n";
						echo "$value";  
						echo "</td>";
						$columnss++;
						if ($columnss>=$this->columns) {
							$columnss=0;
							$doopentr=TRUE;						
							echo "</tr>\n";
						} else {
							$doopentr=FALSE;
						}
					}
				}
			}
			
			$hiddencamp = FALSE;
			if ($this->hiddencamp!=NULL) {
				foreach ($this->hiddencamp as $hidden) {		// activo bandera de llave primaria autocrementable
					if ($hidden==$this->nombres[$i]){
						$hiddencamp = TRUE;
						if ($hiddenform) {		// si crea un hidden
							echo "<input type=\"hidden\" name=\"".$this->nombres[$i]."\" $defaultvalues>";
						} 	
					}
				}
			}
		
			if (!$hiddencamp) {	// si el campo NO es oculto
								
				$sreadonly = "";
				$primaryauto = FALSE;
				if($this->readonly!=NULL){
					foreach ($this->readonly as $readonly) {		// activo bandera si es read only
						if ($readonly==$this->nombres[$i]){
							if ($this->primarykeyauto==$this->nombres[$i]) { // si es por llave primaria
								$primaryauto = TRUE;
							} else {		// si es de cheveres
								$sreadonly = "READONLY";							
							}
						}
					}
				}

				$javascript="";
				if($this->javascript!=NULL){
					foreach ($this->javascript as $clave => $value) {		// cargo valor a hidden en form
						if ($clave==$this->nombres[$i]){
							$javascript = $value;
						}
					}
				}
			
				$moreinfo="";
				if($this->moreinfo!=NULL){
					foreach ($this->moreinfo as $clave => $value) {		// cargo informacion adicional
						if ($clave==$this->nombres[$i]){
							$moreinfo = $value;
						}
					}
				}
                                
				$firstvalueselect="";
				if($this->firstvalueselect!=NULL){
					foreach ($this->firstvalueselect as $clave => $value) {		// unvalor paraun primer select
						if ($clave==$this->nombres[$i]){
							$firstvalueselect = $value;
						}
					}
				}
                                
				$fileform=FALSE;
				if($this->fileform!=NULL){
					foreach ($this->fileform as $clave) {		// si es un menu file
						if ($clave==$this->nombres[$i]){
							$fileform = TRUE;
						}
					}
				}                                

				$preselectv="";
				if($this->depselectv!=NULL){		// si hay un menu depselect virtual
					foreach ($this->depselectv as $depselect) {
						$selectcontents = explode (',', $depselect);
						if ($selectcontents[0]==$this->nombres[$i]){
                                                        
                                                        $defaultvalues2=NULL;
                                                        $selected2="";
                                                        if($this->defaultvalue!=NULL){
                                                                foreach ($this->defaultvalue as $clave => $value) {		// cargo valore defecto
                                                                        if ($clave==$selectcontents[1]){
                                                                                $selected2=$value;
                                                                        }
                                                                }
                                                        }
                                                        
							$preselectv="$selectcontents[1]"; // nombre del preselect
							
							if ($doopentr) {
								echo "<tr  class=\"".$this->stylerow."\">\n";
							}
							echo "<td> -&gt; $preselectv $moreinfo</div></td><td>\n";

							$infotablev = $db->get_results("SELECT $selectcontents[2] AS data1, $selectcontents[3] AS data2 FROM $selectcontents[1] ORDER BY data2");
							
							$selectmakedv="<select $javascript id=\"$selectcontents[1]\" onchange=\"get".$this->nombres[$i]."(this)\" name=\"$selectcontents[1]\">$firstvalueselect\n";

							if ($defaultvalues==NULL) {
								$selectmakedv = "$selectmakedv"."<option value=\"\">...</option>\n";
							} else {			// si esta editano un campo virtual entonces saca el valor reacioenado

								if($this->selects!=NULL){
									foreach ($this->selects as $selectbar) {		// activo bandera de selectmake 
											$selectcontentsx = explode (',', $selectbar);
										if ($selectcontentsx[0]==$this->nombres[$i]){
											$selectcontents4=$selectcontentsx[2];
										}
									}
								}
								$datatablee = $db->get_row("SELECT  $selectcontents[1].$selectcontents[2] AS data1, $selectcontents[1].$selectcontents[3] AS data2 FROM `$selectcontents[0]`
								LEFT JOIN $selectcontents[1] ON $selectcontents[0].$selectcontents[2] = $selectcontents[1].$selectcontents[2] 
								WHERE `$selectcontents4` = '$selected' ORDER BY data2");
								$selectmakedv = "$selectmakedv"."<option value=\"".$datatablee->data1."\">".$datatablee->data2."</option>\n";
							}
				
							foreach ($infotablev as $datatable) {		// aca creo los menus
                                                                if($datatable->data1==$selected2){	// si existe uncampo por defecto para un select
									$selectedd="selected=\"selected\" style=\"font-weight: bold\"";
								} else {
									$selectedd="";
								}
								$selectmakedv = "$selectmakedv"."<option $selectedd value=\"".$datatable->data1."\">".$datatable->data2."</option>\n";
							}
							$selectmakedv="$selectmakedv"."</select>\n";
							echo "$selectmakedv</td>\n";
							
							$columnss++;
							if ($columnss>=$this->columns) {
								$columnss=0;
								$doopentr=TRUE;						
								echo "</tr>\n";
							} else {
								$doopentr=FALSE;
							}
						}
					}
				}

				$preselectr2="";
				$preselectr="";
				$flechita="";
				if($this->depselectr!=NULL){		// si hay un menu depselect real
					foreach ($this->depselectr as $depselect) {
						$selectcontents = explode (',', $depselect);
						if ($selectcontents[0]==$this->nombres[$i]){
							$preselectr="id=\"$selectcontents[0]\" onchange=\"get".$selectcontents[3]."(this)\""; // nombre del preselect
							$flechita=" -&gt; ";
						}
						if ($selectcontents[3]==$this->nombres[$i]){
							$preselectr2="$selectcontents[0]";
							
						}
					}
				}
				
				$selectmakepre = FALSE;
				
				if($this->reqselect!=NULL){		// si hay un menu reqselect
					foreach ($this->reqselect as $depselect) {
						$selectcontents = explode (',', $depselect);
						if ($selectcontents[1]==$this->nombres[$i]){
							$masajax=" onchange=\"get".$selectcontents[0]."(this)\""; // nombre del preselect
							$flechita=" -&gt; ";
						}
						if ($selectcontents[0]==$this->nombres[$i]){
							$selectmakepre = TRUE;
							$selectmakedpre ="<select $javascript id=\"".$this->nombres[$i]."\" name=\"".$this->nombres[$i]."\">$firstvalueselect<option value=\"\">Seleccione $selectcontents[1]...</option></select>\n";
						}
					}
				}				
				
				$selectmake = FALSE;
				
				if(($this->selects!=NULL) AND !$selectmakepre){
					foreach ($this->selects as $selectbar) {		// activo bandera de selectmake 
						$selectcontents = explode (',', $selectbar);
						if ($selectcontents[0]==$this->nombres[$i]){
						
							$radio = FALSE;
							if ($this->radios!=NULL) {
								foreach ($this->radios as $radioss) {		// activo bandera convertir selec a radio
									if ($radioss==$this->nombres[$i]){
										$radio = TRUE;
									}
								}
							}

							$subfilter='';
							if ($selectcontents[4] != '' AND $selectcontents[5] != '') {		// si hay filtros para el subselect
								$subfilter=" WHERE $selectcontents[4] = '$selectcontents[5]'";					
							}
                                                        
                                                        if ($selectcontents[6] != '') {
                                                                $subfilter.=" OR $selectcontents[4] = '$selectcontents[6]' "; // si hay mas sublfitros
                                                        }
							
							$DISCTIN=FALSE;
							if ($selectcontents[4] != '' AND $selectcontents[5] == '') {		// si hay filtros para el subselect
								$infotable = $db->get_results("SELECT DISTINCT($selectcontents[2]) AS data1, $selectcontents[3] AS data2 FROM $selectcontents[1] $subfilter ORDER BY data1");
								$DISCTIN=TRUE;
							} else {
								$infotable = $db->get_results("SELECT $selectcontents[2] AS data1, $selectcontents[3] AS data2 FROM $selectcontents[1] $subfilter  ORDER BY data2");			
							}
							
							if (!$radio) {
								if ($preselectv!=''){ // por si es de una carga por defecto
									$selectmaked="<select $javascript $preselectr id=\"".$this->nombres[$i]."\" name=\"".$this->nombres[$i]."\">$firstvalueselect\n";
								} else {
									$selectmaked="<select $javascript $preselectr name=\"".$this->nombres[$i]."\">$firstvalueselect\n";							
								}
								
								foreach ($infotable as $datatable) {		// aca creo los menus
									if($datatable->data1==$selected){	// si existe uncampo por defecto para un select
										$selectedd="selected=\"selected\" style=\"font-weight: bold\"";
									} else {
										$selectedd="";
									}
									if ($sreadonly=='READONLY') {		// si es de solo lectura muestra solo el campo en cuestion
										if ($selectedd!='') {
											$selectmaked .="<option $selectedd value=\"".$datatable->data1."\">".$datatable->data2."</option>\n";
										}
									
									} else {
										if ($DISCTIN) {
											if ($last==$datatable->data2) { // hack pra el filtro DISTINCT
												
											} else {
												$selectmaked .="<option $selectedd value=\"".$datatable->data1."\">".$datatable->data2."</option>\n";							
												$last=$datatable->data2;			
											}									
										} else {
											$selectmaked .="<option $selectedd value=\"".$datatable->data1."\">".$datatable->data2."</option>\n";
										}							
									}
								}
								$selectmaked.="</select>\n";
							
							} else { // si va a armar radios
								$selectmaked="|";							
								foreach ($infotable as $datatable) {

									if($datatable->data1==$selected){	// si existe uncampo por defecto para un select
										$selectedd="checked";
									} else {
										$selectedd="";
									}								
									if ($sreadonly=='READONLY') {		// si es de solo lectura muestra solo el campo en cuestion
										if ($selectedd!='') {
											$selectmaked .= "<input type=\"radio\" name=\"".$this->nombres[$i]."\" value=\"".$datatable->data1."\" $selectedd>".$datatable->data2." |";
										}
									
									} else {
										$selectmaked .="<input type=\"radio\" name=\"".$this->nombres[$i]."\" value=\"".$datatable->data1."\" $selectedd>".$datatable->data2." |";
									}								
								}							
							}
							$selectmake = TRUE;
						}
					}
				}
							
				if ($doopentr) {
					echo "<tr class=\"".$this->stylerow."\">\n";
				}

				$namecamp=$this->nombres[$i];
				if($this->newname!=NULL){
					foreach ($this->newname as $clave => $value) {		// si reemplazo el nombre
						if ($clave==$this->nombres[$i]){
							$namecamp = "$value";
						}
					}
				}
	
				if ($this->nulo[$i]=='YES'){  	// Para poner la marca de asterisco
					echo "<td>$flechita".$namecamp." $moreinfo</td><td>\n";
				} else {
					echo "<td>* $flechita".$namecamp." $moreinfo</td><td>\n";			
				}	

				if ($primaryauto) {		// si es una llave primaria autoincrementable no imprime formulario
				
					echo "Llave primaria, autoincrementable\n";
					if ($hiddenform) {		// si crea un hidden
						echo "<input type=\"hidden\" name=\"".$this->nombres[$i]."\" $defaultvalues>";
					} 
					
				} else if ($selectmake) {		// si hizo un select
					echo "$selectmaked";
	
				} else if ($fileform) {		// si hizo un file form
					echo "<input name=\"".$this->nombres[$i]."\" type=\"file\" id=\"".$this->nombres[$i]."\" $sreadonly $dynamiclist $defaultvalues>";
	
				} else if ($selectmakepre) {		// si hizo un select pre
					echo "$selectmakedpre";
	
				} else if ($preselectv!="") {		// si depende de ajax con campo virtual
				
					echo "<select $masajax id=\"".$this->nombres[$i]."\" name=\"".$this->nombres[$i]."\">\n";
					echo "$firstvalueselect<option value=\"\">Seleccione $preselectv...</option>\n";
					echo "</select>\n";
					
				} else if ($preselectr2!="") {		// si depende de ajax con campo real
				
					echo "<select $masajax id=\"".$this->nombres[$i]."\" name=\"".$this->nombres[$i]."\">\n";
					echo "<option value=\"\">Seleccione $preselectr2...</option>\n";
					echo "</select>\n";
					
				} else if ($this->variable[$i]=="text") {		// si es campo de texto
					if (!$showhtml) {
						echo "<textarea $javascript rows=8 cols=34 name=\"".$this->nombres[$i]."\" $dynamiclist $sreadonly>$defaultvalues</textarea>\n";
					} else {
						echo "$defaultvalues";
					}
				} else if ($this->variable[$i]=="date") {		// si es campo de fecha
					$dates++;
					echo "<input type=\"text\" $javascript id=\"date$dates\" name=\"date$dates\" size=10 maxlength=\"10\" $sreadonly $defaultvalues>
					<img src=\"images/b_calendar.png\" id=\"caldate$dates\" border=\"1\" title=\"Calendario\"/ onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" />&nbsp;&nbsp;<small>(AAAA-MM-DD)</small>\n ";
					
				} else if ($this->variable[$i]=="bigint" OR $this->variable[$i]=="int" OR $this->variable[$i]=="smallint" OR $this->variable[$i]=="tinyint" OR $this->variable[$i]=="float") {  // si es lagun tipo de entero
				
					echo "<input $javascript type=\"text\" name=\"".$this->nombres[$i]."\" size=\"".$this->tamanovariable[$i]."\" maxlength=\"".$this->tamanovariable[$i]."\" $sreadonly $dynamiclist $defaultvalues>\n";
					
				} else if ($this->variable[$i]=="binary") {  // si es un booleano
					if ($defaultvalues=="value=\"1\"") {
						$defaultvalues="checked";
						$selectedd1="checked";
					} else {
						$defaultvalues="";
						$selectedd2="checked";
					}				
					$radio = FALSE;
					if ($this->radios!=NULL) {
						foreach ($this->radios as $radioss) {		// activo bandera convertir selec a radio
							if ($radioss==$this->nombres[$i]){
								$radio = TRUE;
							}
						}
					}
					if (!$radio) {	// si es un checkbox			
						echo "<input $javascript type=\"checkbox\" name=\"".$this->nombres[$i]."\" value=\"1\" $sreadonly $dynamiclist $defaultvalues>\n";
					} else {	// si es un radio
						echo "|<input $javascript type=\"radio\" name=\"".$this->nombres[$i]."\" value=\"1\" $selectedd1> Si |<input type=\"radio\" name=\"".$this->nombres[$i]."\" value=\"0\" $selectedd2> No |";
					
					}					
				} else if ($this->variable[$i]=="double") {
					echo "<input $javascript type=\"text\" name=\"".$this->nombres[$i]."\" size=\"12\" maxlength=\"22\" $sreadonly $dynamiclist $defaultvalues>\n";
				} else if ($this->variable[$i]=="varchar" || $this->variable[$i]=="char") {  // si es algun tipo de texto
					if (!$showhtml) {
						$texttt=FALSE;
						if ($this->tamanovariable[$i] < 8) {
							$sizecampo = 10;
						} else if ($this->tamanovariable[$i] < 40) {
							$sizecampo = 20;
						} else if ($this->tamanovariable[$i] > 130 AND $this->tamanovariable[$i] < 200) {
							$texttt=TRUE;
							$sizecampo = 2;
						} else if ($this->tamanovariable[$i] > 199) {
							$texttt=TRUE;
							$sizecampo = 3;
						} else {
							$sizecampo = 35;
						}
						if ($texttt) {
							echo "<textarea $javascript rows=$sizecampo cols=34 name=\"".$this->nombres[$i]."\" $sreadonly>$defaultvalues</textarea>\n";
						} else {
						
							if ($this->tamanovariable[$i]=='66') {
								echo "<input $javascript type=\"password\" name=\"".$this->nombres[$i]."\" size=\"10\" maxlength=\"20\" $sreadonly $defaultvalues>\n";
							} else {
								echo "<input $javascript type=\"text\" name=\"".$this->nombres[$i]."\" size=\"$sizecampo\" maxlength=\"".$this->tamanovariable[$i]."\" $sreadonly $dynamiclist $defaultvalues>\n";							
							}
						}
					} else {
						echo "$defaultvalues";
					}

				} else if ($this->variable[$i]=="time") {
					echo "<input type=\"text\" name=\"".$this->nombres[$i]."\" size=\"6\" maxlength=\"8\" $sreadonly $dynamiclist $defaultvalues>\n";
				} else if ($this->variable[$i]=="datetime") {

					$dates++;
					$datestime[]=$dates;
					echo "<input $javascript type=\"text\" id=\"date$dates\" name=\"date$dates\" size=16 maxlength=\"17\" $sreadonly $defaultvalues>
					<img src=\"images/b_calendar.png\" id=\"caldate$dates\" border=\"1\" title=\"Calendario\"/ onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" />&nbsp;&nbsp;<small>(AAAA-MM-DD 00:00)</small>\n ";
				
				} else {
					echo "".$this->variable[$i]." unknown";
				}

				echo "</td>\n";
				$columnss++;  
				if ($columnss>=$this->columns) {
					$doopentr=TRUE;
					$columnss=0;				
					echo "</tr>\n";
				} else {
					$doopentr=FALSE;
				}
				
				if($this->commentsafter!=NULL){
					foreach ($this->commentsafter  as $clave => $value) {		// valores de info despues
						if ($clave==$this->nombres[$i]){
							if ($doopentr) {
								echo "<tr  class=\"".$this->stylerow."\">\n";
							}
							echo "<td  colspan=\"2\">\n" ;
							echo "$value";  
							echo "</td>";
							$columnss++;
							if ($columnss>=$this->columns) {
								$columnss=0;				
								echo "</tr>\n";
							}
						}
					}
				}	
			}
		}
		if ($columnss!=0) {
			for ($ci=$columnss; $ci<$this->columns; $ci++) {
				if ($ci==$this->columns) {
					echo "<td  colspan=\"2\">\n" ;
					echo "&nbsp;";  
					echo "</td>";
					echo "</tr>\n";																			
				} else {
					echo "<td  colspan=\"2\">\n" ;
					echo "&nbsp;";  
					echo "</td>";
				}
			}	
		}
		
		if ($this->close) {	// si cierra el form
			echo "<tr class=\"".$this->stylefoother."\"><td colspan=\"$colspann\"><img src=\"images/PoweredBytf.gif\" border=\"0\" align=\"right\" alt=\"www.tecsua.com\"> ";
			echo "".$this->infoboton."";
			if ($this->submit) {
				echo "&nbsp;<input type=\"submit\" class=\"".$this->stylebutton."\" name=\"Action\" VALUE=\"".$this->namebutton."\">";
			}
			echo "</form>\n";	
		}	
			// Codigo para activar calendarios si hay
		if ($dates>0) {
			echo "<script type=\"text/javascript\">\n";
			for ($cc=1;$cc<=$dates;$cc++){
				$timefrrr=FALSE;
				if ($datestime!=''){
					foreach ($datestime as $datestime2) {
				 		if ($datestime2==$cc) {
							$timefrrr=TRUE;
				 		}					 	
				 	}
				}
				if (!$timefrrr) {
					echo "Calendar.setup({
				        inputField     :    \"date$cc\",    
				        ifFormat       :    \"%Y-%m-%d\",
				        button         :    \"caldate$cc\",
				        singleClick    :    true
				    	});\n";
				} else {
					echo "Calendar.setup({
				        inputField     :    \"date$cc\",     
				        ifFormat       :    \"%Y-%m-%d %H:%M\",
				        button         :    \"caldate$cc\",
			                showsTime      :    true,
				        timeFormat     :    \"24\",
				        singleClick    :    true
				    	});\n";
				}
			}
			echo "</script>\n";
		}
		
		if ($this->close) {	// si cierra el form						
			echo "</td></tr>\n";
			echo "</tbody>\n</table>\n\n";
		}
		if ($this->debug) {
			echo "<br>names ";		
			print_r($this->nombres);
			echo "<br>vars ";				
			print_r($this->variable);
			echo "<br>size ";				
			print_r($this->tamanovariable);
			echo "<br>null ";				
			print_r($this->nulo);	
			echo "<br>readonly ";				
			print_r($this->readonly);
			echo "<br>defaults ";				
			print_r($this->default);		
		}
	}
}



/**
* objeto para envio de email

* @authorJose Antonio Cely Saidiza <jose.cely@gmail.com>
* @version 0.01
* TODO: 
* - Archivos adjutnos
* - validacion de emails
* - Documentar más a fondo
*/
class send_mail {

	var $from;
	var $recep;
	var $recepcc;
	var $recepbcc;
	var $replyto;	
	var $asunto;
	var $mensaje;
	var $titulo;
	var $formato = TRUE;	

        /**
        * receptores de correo
        * @param   string  $recep Receptor
        */ 
	function recep($recep) {
		$this->recep[]=$recep;
	}	

        /**
        *  receptores de correo copia
        * @param   string  $recepcc Receptor copia
        */
	function recepcc($recepcc) {
		$this->recepcc[]=$recepcc;
	}	

        /**
        *  receptores de correo copiacoulta
        * @param   string  $recepbcc Receptor copia oculta
        */
	function recepbcc($recepbcc) {	
		$this->recepbcc[]=$recepbcc;
	}	

        /**
        *  origen de email
        * @param   string  $from Receptor copia oculta
        */
	function from($from) {
		$this->from=$from;
	}


        /**
        *  Asunto de email
        * @param   string  $asunto Asunto de email
        */
	function asunto($asunto) {
		$this->asunto=$asunto;
	}
		
        /**
        *  Mensaje de email
        * @param   string  $mensaje Mensaje
        */
	function mensaje($mensaje) {
		$this->mensaje=$mensaje;
	}
	
	/**
        *  Reply to
        * @param   string  $replyto replyto
        */
        function replyto($replyto) {
		$this->replyto=$replyto;
	}
	
	/**
        * Configurar como texto en vez de HTML por defecto
        */        
	function settext() {
		$this->formato=FALSE;
	}

	/**
        * Titulo para el email, solo si es html
        * @param   string  $titulo Tutulo
        */
	function settitulo($titulo) {
		$this->titulo=$titulo;
	}

	/**
        * envia email
        */
	function sendmailnow() {
	
		if ($this->titulo == '') {	// si no hay titulo estabece como titulo el email origen
			$this->titulo=$this->from;
		}

		if ($this->replyto == '') {	// si no hay reply estabece como reply el from
			$replyto="Reply-To: ".$this->from."\n";
		} else {
			$replyto="Reply-To: ".$this->replyto."\n";		
		}
			
		if ($this->formato) {	// si es html
			$cabeceras  = 'MIME-Version: 1.0' . "\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
			$mensaje='<html>
<head>
<title>'.$this->titulo.'</title>
</head>
<body>';
			$mensaje .= $this->mensaje;
			$mensaje .= '</body>
</html>';
		} else {
			$cabeceras="";
			$mensaje=$this->mensaje;			
		}
		
		$recipients="";
		$copys="";
		$bccopys="";
		$from="From: ".$this->from."\n";
				
		$recipientshtml="";

		if($this->recep!=NULL){
                        $thisrecep = array_unique($this->recep);
			foreach ($thisrecep as $value) {		// si reemplazo el nombre
				if ($recipientshtml==""){
					$recipientshtml = 'To: '.$value.'';
					$recipients = $value;					
				} else {
					$recipientshtml .= ', '.$value.'';
					$recipients .= ', '.$value.'';													
				}
			}
			$recipientshtml .= "\n";
		} 

		if($this->recepcc!=NULL){
                        $thisrecepcc = array_unique($this->recepcc);
			foreach ($thisrecepcc as $value) {		// si reemplazo el nombre
				if ($copys==""){
					$copys = 'Cc: '.$value.'';
				} else {
					$copys .= ', '.$value.'';				
				}
			}
			$copys .= "\n";
		} 

		if($this->recepbcc!=NULL){
                        $thisrecepbcc = array_unique($this->recepbcc);
			foreach ($thisrecepbcc as $value) {		// si reemplazo el nombre
				if ($bccopys==""){
					$bccopys = 'Bcc: '.$value.'';
				} else {
					$bccopys .= ', '.$value.'';				
				}
			}
			$bccopys .= "\n";
		} 

		// envia mail
		//$cabeceras .= $recipientshtml;
		$cabeceras .= $from;
		$cabeceras .= $replyto;
		$cabeceras .= $copys;
		$cabeceras .= $bccopys;
		$cabeceras .= "X-Mailer: PHP" . phpversion(); 
		mail($recipients, $this->asunto, $mensaje, $cabeceras);
	}	
}

?>
