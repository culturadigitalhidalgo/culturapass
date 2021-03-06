<?php
/*
* Eventos
* Name: Cartelera Digital
* Author's: Eliel Trigueros Hernandez, Omar Oliver Rodriguez, Eloy Monter Hernández
* Author URI: http://cultura.hidalgo.gob.mx
* @since Versión 3.0, revisión 3. Junio/2017
* @since Versión 4.0, revisión 4. Abril/2018
*/
?>

<style type="text/css">
.cartelera-digital{
	display:inline-block; width:350px; margin:8px; height:auto; vertical-align: text-top;
	font-family:'graphik-medium', sans-serif;
}
.thumb{max-width:350px;}
.thumb img{width:100%; height:auto;}
.title{font-weight: bold; font-size: 1.5em; padding:8px 0px;}
.cartelera-digital .lugar, .cartelera-digital .fecha, .cartelera-digital .persona{margin:8px 0px !important;}

.msj_error{background:#6cbe45; font-weight:bold; letter-spacing:.4em; color:#FFF; padding:16px 0px;}

/*
.ui-datepicker-calendar {
    display: none;
 }
button.ui-datepicker-current {
	display: none;
}
*/
</style>

<script src="../wp-content/themes/EspecialesT2/eventos/slick/slick.min.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="../wp-content/themes/EspecialesT2/eventos/slick/slick.css">
<link rel="stylesheet" type="text/css" href="../wp-content/themes/EspecialesT2/eventos/slick/slick-theme.css"> 
<link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" id="acf-datepicker-css" href="http://cultura.hidalgo.gob.mx/wp-content/plugins/advanced-custom-fields-pro-master/assets/inc/datepicker/jquery-ui.min.css" type="text/css" media="all">
<script type="text/javascript" src="http://cultura.hidalgo.gob.mx/wp-includes/js/jquery/ui/datepicker.min.js"></script>

<script type="text/javascript">
jQuery(document).on('ready', function() {


var f = new Date();
var monthNames = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"]	;

jQuery('#selFecha_input').val(monthNames[f.getMonth()]+", "+f.getFullYear());
jQuery( "#selFecha_input" ).datepicker({
		"currentText": "",
		"changeMonth": "true",
        "changeYear": "true",
        "showButtonPanel": "true",
		"dateFormat":"MM, yy",
		"closeText":"Aceptar",
		"monthNames":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
		"monthNamesShort":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		"isRTL":false,
		onClose: function(dateText, inst) { 
            jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        },
        beforeShow: function( input ) {
            setTimeout(function () {
                jQuery(input).datepicker("widget").find(".ui-datepicker-calendar").hide();
                jQuery(input).datepicker("widget").find(".ui-datepicker-current").hide();
            }, 1 );
        },
        onChangeMonthYear: function( input ) {
            setTimeout(function () {
                jQuery(input).datepicker("widget").find(".ui-datepicker-calendar").hide();
                jQuery(input).datepicker("widget").find(".ui-datepicker-current").hide();
            }, 1 );
        }	
});


jQuery('#selFecha').change(function() {
		jQuery('#selFecha_input').val('');
jQuery("#selFecha_input").datepicker("destroy");
var Fecha = jQuery( "#selFecha option:selected" ).val();

if (Fecha == 'M'){
	jQuery( "#selFecha_input" ).datepicker({
		"currentText": "",
		"changeMonth": "true",
        "changeYear": "true",
        "showButtonPanel": "true",
		"dateFormat":"MM, yy",
		"closeText":"Aceptar",
		"monthNames":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
		"monthNamesShort":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		"isRTL":false,
		onClose: function(dateText, inst) { 
            jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        },
        beforeShow: function( input ) {
            setTimeout(function () {
                jQuery(input).datepicker("widget").find(".ui-datepicker-calendar").hide();
                jQuery(input).datepicker("widget").find(".ui-datepicker-current").hide();
            }, 1 );
        },
        onChangeMonthYear: function( input ) {
            setTimeout(function () {
                jQuery(input).datepicker("widget").find(".ui-datepicker-calendar").hide();
                jQuery(input).datepicker("widget").find(".ui-datepicker-current").hide();
            }, 1 );
        }
	});
jQuery('#selFecha_input').val(monthNames[f.getMonth()]+", "+f.getFullYear());	
}else{
	jQuery( "#selFecha_input" ).datepicker({
		"changeMonth": "true",
        "changeYear": "true",
        "showButtonPanel": "true",
		"closeText":"Cerrar",
		"currentText":"Hoy",
		"monthNames":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
		"monthNamesShort":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		"nextText":"Siguiente",
		"prevText":"Previo",
		"dayNames":["Domingo","Lunes","Martes","Mi\u00e9rcoles","Jueves","Viernes","S\u00e1bado"],
		"dayNamesShort":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
		"dayNamesMin":["D","L","M","X","J","V","S"],
		"dateFormat":"d MM, yy",
		"firstDay":1,
		"isRTL":false
	});
jQuery('#selFecha_input').val(f.getDate()+" "+ monthNames[f.getMonth()]+", "+f.getFullYear());		
}

});    
							jQuery(".regular_sc").slick({
								dots: false,
								infinite: true,
								slidesToShow: 1,
								slidesToScroll: 1,
								autoplay: true,
								autoplaySpeed: 2000,
								prevArrow:"<img class='a-left control-c prev slick-prev' src='../wp-content/themes/EspecialesT2/eventos/slick/ant_verde.png'>",
								nextArrow:"<img class='a-right control-c next slick-next' src='../wp-content/themes/EspecialesT2/eventos/slick/next_verde.png'>",
							});
						 
	
getDisciplinas();

        
});

function getDisciplinas () {
	jQuery.ajax({
            url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_disciplinas.php',
            type:  'post',
            success:  function (response) {
                    jQuery("#selDisciplina").html(response);
                    getOrganiza();
                    //getCategorias();
            }
    });
}

/*function getCategorias () {
	jQuery.ajax({
            url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_categoria.php',
            type:  'post',
            success:  function (response) {
                    jQuery("#selCategorias").html(response);
                    getCostos();
            }
    });
}*/

/*function getCostos () {
	jQuery.ajax({
            url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_costo.php',
            type:  'post',
            success:  function (response) {
                    jQuery("#selCosto").html(response);
                    getRecintos();
            }
    });
}*/

/*function getRecintos () {
	jQuery.ajax({
        url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_recintos.php',
        type:  'post',
        success:  function (response) {
                jQuery("#selRecinto").html(response);
                getPublicos();
        }
    });
}*/
function getOrganiza () {
	jQuery.ajax({
        url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_organiza.php',
        type:  'post',
        success:  function (response) {
                jQuery("#selOrganiza").html(response);
                getMunicipios();
        }
    });
}
function getMunicipios () {
	jQuery.ajax({
        url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_municipios.php',
        type:  'post',
        success:  function (response) {
                jQuery("#selMunicipios").html(response);
                getPublicos();
        }
    });
}

function getPublicos () {
	jQuery.ajax({
            url:   '../wp-content/themes/EspecialesT2/eventos/ajax/get_publico.php',
            type:  'post',
            success:  function (response) {
                    jQuery("#selPublico").html(response);
                    get_filtro_eventos_ajax();
            }
    });
}

function get_filtro_eventos_ajax() {
//    jQuery('input[name="submit"]').click(function() {

		var selFecha = jQuery( "#selFecha option:selected" ).val();
		var selFecha_input = jQuery( "#selFecha_input" ).val();
		var Disciplina = jQuery( "#selDisciplina option:selected" ).val();
		//var Categorias = jQuery( "#selCategorias option:selected" ).val();
		//var Costo = jQuery( "#selCosto option:selected" ).val();
		//var Recinto = jQuery( "#selRecinto option:selected" ).val();
		var Organiza = jQuery( "#selOrganiza option:selected" ).val();
		var Municipios = jQuery( "#selMunicipios option:selected" ).val();
		var Publico = jQuery( "#selPublico option:selected" ).val();
		
			var parametros = {
				"selFecha" : selFecha,
				"selFecha_input" : selFecha_input,
				"Disciplina" : Disciplina,
				//"Categorias" : Categorias,
				//"Costo" : Costo,
				//"Recinto" : Recinto,
				"Organiza" : Organiza,
				"Municipios" : Municipios,
				"Publico" : Publico
			};
           
        
            jQuery.ajax({
                    type: "POST",
                    //url: "wp-content/themes/auberge-plus/includes/setup/setup_agrega_eventos_ajax.php",
                    url: "../wp-content/themes/EspecialesT2/eventos/ajax/filtro_eventos_ajax.php",
                    data: parametros,
                    beforeSend: function () {
                        jQuery("#resultado_sc").html("<div class='msj_error'>Procesando, espere por favor...</div>");
                	},
                    success: function(response){
                        jQuery('#resultado_sc').html(response).fadeIn();
                    }
            });
           
                
}   
</script>


<?php
$args = array(
'post_type'      => 'eventos',
'order'          => 'DESC',
'posts_per_page' => -1,
'post_status' => 'publish',
'meta_query'    => array()
);


array_push($args['meta_query'], array(
	array(
		array(
				'key' => 'slider', 
                'value' => 1, 
                'compare' => '==',
      )),
  ));

$events = new WP_Query($args);

?>
	
<section style="margin-top: 0px;" class="regular_sc slider">

<?php

$hoy = date("Y-m-d", strtotime("now"));
foreach ($events->posts as $post){
	
	$array_de_fechas = Array(); 
	$event_id=$post->ID;
	
if (get_field('tipo_de_evento', $event_id) == 'Evento'){
	
	if( have_rows('fechas') ){
			while( have_rows('fechas') ){
				the_row();
				$array_de_fechas[] = get_sub_field('fecha',false);
			}
		}
    
	if( have_rows('periodo') ){//periodo
		while( have_rows('periodo') ){
			the_row();
			$fecha_inicio = get_sub_field('fecha_inicio',false);
			$fecha_cierre = get_sub_field('fecha_cierre',false);
		}
		$begin = new DateTime( $fecha_inicio );
		$end = new DateTime( $fecha_cierre );
		$end = $end->modify( '+1 day' ); 
		
		$interval = new DateInterval('P1D');
		$daterange = new DatePeriod($begin, $interval ,$end);

		foreach($daterange as $date){
			$array_de_fechas[]= $date->format("Y-m-d");
		}
	}//periodo
	
	$datetime1 = new DateTime($hoy);
	
	foreach($array_de_fechas as $date){	
		$datetime2 = new DateTime($date);
		$interval = $datetime1->diff($datetime2);
		$intervalo = $interval->format('%R%a');
		if ($intervalo >= 0){
			//imagen destacada
			$imagen_destacada = get_field('encabezado', $event_id);
			if (empty($imagen_destacada)) {
				$imagen_destacada = '/wp-content/gallery/sinImagen.jpg';
			}else{
				$imagen_destacada = get_field('encabezado', $event_id);
			}
			//imagen destacada			
			?>
			<div class="CCA2">	 
				<a href="<?php echo get_permalink($event_id); ?>" title="<?php echo $post->post_title; ?>" target="_blank"><img src="<?php echo $imagen_destacada;?>"></a>
			</div>
			<!--veda
			<div class="CCA2">	 
				<img src="/wp-content/gallery/VEDA_D _1.png">
			</div>-->
			<?php			
			break;
			}
	}
}//If evento	
}//foreach

?>
</section>
<?php
//si es administrador
//if ( current_user_can('update_core') ) {
//$WP_User = wp_get_current_user();
//session_start();
?>

<div class="filtro_SC" id="resultado_omar"  align="center">	
	<select id="selFecha" name="selFecha" style="width:70px !important;">
		<option value="M" selected>Mes</option>
		<option value="D">Día</option>
	</select>
	<input type="text" value="" id="selFecha_input" name="selFecha" style="text-align:center;" size="10">
	<select id="selDisciplina" name="selDisciplina"></select>
	<!--<select id="selCategorias" name="selCategorias"></select>
	<select id="selCosto" name="selCosto"></select>
	<select id="selRecinto" name="selRecinto"></select>-->
	<select id="selOrganiza" name="selOrganiza" style="width:200px !important;"></select>
	<select id="selMunicipios" name="selMunicipios" style="width:200px !important;"></select>
	<select id="selPublico" name="selPublico"></select>
	<input type="submit" name="submit" value="Filtrar" onclick="get_filtro_eventos_ajax();">
</div>
<?php
//}//si es administrador
?>

<div style="width:100%; margin:0 auto;" id="resultado_sc"  align="center"><div class='msj_error'>Procesando, espere por favor...</div></div>
