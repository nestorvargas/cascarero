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

	// Include ezSQL core
	include_once "library/ez_sql_core.php";
	include_once "library/ez_sql_mysql.php";

	$db = new ezSQL_mysql('root','','makeform','localhost');

if(isset($_GET['pais'])){

	$conDpto = $db->get_results("SELECT id, nombre FROM division WHERE pais = '".$_GET['pais']."' ORDER BY nombre");
	echo "obj.options[obj.options.length] = new Option('...','');\n";	
	foreach ( $conDpto as $cgDpto )
	{
	         echo "obj.options[obj.options.length] = new Option('".$cgDpto->nombre."','".$cgDpto->id."');\n";
	}
	
}

if(isset($_GET['division'])){

	$conDpto = $db->get_results("SELECT id, nombre FROM ciudad WHERE division = '".$_GET['division']."' ORDER BY nombre");
	foreach ( $conDpto as $cgDpto )
	{
	
	         echo "obj.options[obj.options.length] = new Option('".$cgDpto->nombre."','".$cgDpto->id."');\n";
	}
	
}

if(isset($_GET['Dpto'])){

	$conDpto = $db->get_results("SELECT id_ciudad, Nombre FROM Ciudad WHERE id_dpto = '".$_GET['Dpto']."'");
	foreach ( $conDpto as $cgDpto )
	{
	         echo "obj.options[obj.options.length] = new Option('".$cgDpto->Nombre."','".$cgDpto->id_ciudad."');\n";
	}
	
}


if(isset($_GET['Grupo'])){

	$congrupo = $db->get_results("SELECT id_sub_grupo, Descripcion FROM `Sub_grupo` WHERE `id_grupo` = '".$_GET['Grupo']."'");
	foreach ( $congrupo as $cgrupo )
	{
	         echo "obj.options[obj.options.length] = new Option('".$cgrupo->Descripcion."','".$cgrupo->id_sub_grupo."');\n";
	}
	
}


if(isset($_GET['grupocod'])){

	$congrupo = $db->get_results("SELECT id_sub_grupo, Descripcion FROM `Sub_grupo` WHERE `id_grupo` = '".$_GET['grupocod']."'");
	foreach ( $congrupo as $cgrupo )
	{
	         echo "obj.options[obj.options.length] = new Option('".$cgrupo->Descripcion."','".$cgrupo->id_sub_grupo."');\n";
	}
	
}


if(isset($_GET['getNombre']) && isset($_GET['letters'])){  // para el dynamic list

	$letters = $_GET['letters'];
	$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
	//id valor en el select
	$conNombre = $db->get_results("SELECT Nombre, Nombre AS Nombre2 FROM `Ciudad` WHERE `Nombre`  like '".$letters."%'");
		
	#echo "1###select ID,countryName from ajax_countries where countryName like '".$letters."%'|";
	foreach ( $conNombre as $cNombre )
	{
	         echo "".$cNombre->Nombre.""."###"."".$cNombre->Nombre2.""."|";
	}	
	
}


?>
