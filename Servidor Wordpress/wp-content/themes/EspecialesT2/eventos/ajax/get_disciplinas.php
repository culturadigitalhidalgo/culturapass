<?php  
/* 
* Disciplina
* 
* @category Disciplina
* @author Centro de Información Cultural del Estado de Hidalgo CECULTAH <cic.innovacion@gmail.com>
* @copyleft Algunos derechos reservados Centro de Información Cultural del Estado de Hidalgo SC
* @since Versión 1.0, revisión 1. Marzo/2017
* @versión 1.0 
*/
require ('../../../../../wp-load.php');
$posts = get_posts(array(
    'post_type'   => 'eventos',
    'post_status' => 'publish',
    'posts_per_page' => -1
    )
);

foreach($posts as $p){
	$disc[] = get_post_meta($p->ID,"disciplina",true);
}

$resultado = array_unique($disc);
echo '<option value="0">Todas las disciplinas</option>';
sort($resultado);
foreach ($resultado as $valor) {
   echo '<option value="'.$valor.'">'.$valor.'</option>';
}

?>
