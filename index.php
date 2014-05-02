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
*  Copyright  2007  TECSUA LTDA. Jose Antonio Cely Saidiza
*  Email jose.cely@tecsua.com
*  Bogotá Colombia
****************************************************************************/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>GENERADOR DE FORMULARIOS CON PHP Y AJAX </title>
<meta name="author" content="electro"/>
<meta name="description" content=""/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<style type="text/css" media="all">
	@import "style.css";
</style>


  <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="library/calendar-system.css" title="win2k-cold-1" />

  <!-- main calendar program -->
  <script type="text/javascript" src="library/calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="library/calendar-es.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="library/calendar-setup.js"></script>
  
	
<script type="text/javascript" src="library/dynamic-list.js"></script>

<script type="text/javascript" src="library/tw-sack.js"></script>

<script type="text/javascript">
var ajax = new sack();
	
</script>
</head>
<body leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">


<?php

	// Include ezSQL core
	include_once "library/ez_sql_core.php";
	include_once "library/ez_sql_mysql.php";
		
	$db = new ezSQL_mysql('root','','Cascarera','localhost');
        
	include_once "library/classmakeform.php";
	

	
	$contacto = new makeform('Contacto');
	//$forma->settitle('FORMULARIO DE GRUPOS Y LINEAS');
		
        $contacto->makejavascript();
        $contacto->setselect('Genero,Genero,id,Descripcion');
        $contacto->firstvalueselect('Genero');
        
	$contacto->showform();



	
	

?>
</body>
</html>
