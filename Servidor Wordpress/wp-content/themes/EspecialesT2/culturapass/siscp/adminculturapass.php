<?php
//------------CONFIGURAR LINKS------------------------------------------------------------
//config local---------------------------------
//$wpconfigurl='../../../../../wp-config.php';
//$logoutpage="http://192.168.100.92/worpress/wp-login.php?action=logout";
//$path="../wp-content/themes/twentyseventeen/culturapass/siscp/";
//$pathcontrolador="http://192.168.100.92/worpress/wp-content/libccd/ccdcontroladorsiscp.php";
//$pagina=$path."adminculturapass.php";

//config produccion-------------------------------
$wpconfigurl='../../../../../wp-config.php';
$logoutpage="http://cultura.hidalgo.gob.mx/wp-login.php?action=logout";
$path="../wp-content/themes/EspecialesT2/culturapass/siscp/";
$pathcontrolador="http://cultura.hidalgo.gob.mx/wp-content/libccd/ccdcontroladorsiscp.php";
$pagina=$path."adminculturapass.php";
    
//------------CONFIGURAR LINKS------------------------------------------------------------    

//----------------------------Procesamiento de funciones de validacion de WP para crear usuarios
if(isset($_POST['validcultuspassid'])){   
    require_once($wpconfigurl);
    global $wpdb;
    $registros = $wpdb->get_results( "SELECT count(*) as nvals FROM wp_usermeta where meta_key='cp_id_culturapass' and meta_value='".$_POST['validcultuspassid']."'" );    
    echo $registros[0]->nvals;
    die();
}

if(isset($_POST['validarrega'])){
    require_once($wpconfigurl);
    include_once(ABSPATH . 'wp-includes/pluggable.php');
    $type=$_POST['type'];
    $element=$_POST['element'];
    
    if($type==="validusrexist"){
        if ( username_exists( $element ) ){
            echo 'Existe';
        }
    }
    if($type==="validusrname"){
        if ( !validate_username( $element ) ){
            echo 'Error';
        }
    }
    if($type==="validemail"){
        if ( !is_email( $element ) ){
            echo 'Error';
        }
    }
    if($type==="validemailexist"){
        if ( email_exists( $element ) ){
            echo 'Existe'; 
        }
    }
    die();
}

if(isset($_POST['addnewuser_wpcp'])){
    require_once($wpconfigurl);
    $username   =   sanitize_user( $_POST['username'] );
    $password   =   esc_attr( $_POST['password'] );
    $password2   =   esc_attr( $_POST['password2'] );
    $email      =   sanitize_email( $_POST['email'] );
    $first_name =   sanitize_text_field( $_POST['fname'] );
    $last_name  =   sanitize_text_field( $_POST['lname'] );
    $cp_ult_gra  =   sanitize_text_field( $_POST['cp_ult_gra'] );
    $cp_p_origen  =   sanitize_text_field( $_POST['cp_p_origen'] );
    $cp_edo_nac  =   sanitize_text_field( $_POST['cp_edo_nac'] );
    $cp_dom_nac   =   sanitize_text_field( $_POST['cp_dom_nac'] );
    $cp_ynac   =   sanitize_text_field( $_POST['cp_anio']."-".$_POST['cp_mes'].'-'.$_POST['cp_dia'] );
    $cp_gen   =   sanitize_text_field( $_POST['cp_gen'] );
    $cp_tipo_usuario   =   sanitize_text_field( $_POST['cp_tipo_usuario'] );
    $cp_id_culturapass   =   sanitize_text_field( $_POST['cp_id_culturapass'] );
    $cp_culturapass   =   sanitize_text_field( "Si" );
    
    $userdata = array(
    'user_login'    =>   $username,
    'user_email'    =>   $email,
    'user_pass'     =>   $password,
    'first_name'    =>   $first_name,
    'last_name'     =>   $last_name,
    );
    echo $user = wp_insert_user( $userdata );
    echo update_user_meta( $user, 'cp_ult_gra', $cp_ult_gra );
    echo update_user_meta( $user, 'cp_p_origen', $cp_p_origen );
    echo update_user_meta( $user, 'cp_edo_nac', $cp_edo_nac );
    echo update_user_meta( $user, 'cp_dom_nac', $cp_dom_nac );
    echo update_user_meta( $user, 'cp_ynac', $cp_ynac );
    echo update_user_meta( $user, 'cp_gen', $cp_gen );
    echo update_user_meta( $user, 'cp_tipo_usuario', $cp_tipo_usuario );
    echo update_user_meta( $user, 'cp_culturapass', $cp_culturapass );
    echo update_user_meta( $user, 'cp_id_culturapass', $cp_id_culturapass );
    
    die();
}

if(isset($_POST['activar_cultus'])){
    $usuario=$_POST['idusuario'];
    $cp=$_POST['idculturapass'];
    
    $data['cp_id_culturapass']=$cp;
    
    require_once($wpconfigurl);
    
    $cp_id_culturapass   =   sanitize_text_field( $cp );
    echo update_user_meta( $usuario, 'cp_culturapass', "Si" );
    echo update_user_meta( $usuario, 'cp_id_culturapass', $cp_id_culturapass );
    
    die();
}

//
//----------------------------Procesamiento de funciones de validacion de WP para crear usuarios

global $current_user;
get_currentuserinfo();

if($current_user->ID==0){
    echo '<br><br><h2>Lo sentimos no tiene permisos para entrar a esta p&aacute;gina</h2><br><h2><a href="'.$logoutpage.'">Salir</a></h2>';
    die();
}

//------------VALIDAR PERMISOS DE USUARIO------------------------------------------------------------    
$user_info = get_userdata($current_user->ID);
$roles= implode(', ', $user_info->roles);
$validroladmin = strpos($roles, "admin_cultus");
if ($validroladmin === false) {
    echo '<br><br><h2>Lo sentimos no tiene permisos para entrar a esta p&aacute;gina</h2><br><h2><a href="'.$logoutpage.'">Salir</a></h2>';
    die();
}
//------------VALIDAR PERMISOS DE USUARIO------------------------------------------------------------

//------------OBTENER ID USUARIO------------------------------------------------------------
$usuariologin=$current_user->ID;

//------------DECLARACIÓN DE LAS CLASES DE CONTROL------------------------------------------------------------
require_once ('DBClass.php');
$consultasloc=new DBClass(); 
$consultasloc->to_query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';");
//
require_once ('DBClass_thinkcloud_siscp.php');
$consultastc=new DBClass_thinkcloud_SISCP(); 
$consultastc->to_query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';");
//------------DECLARACIÓN DE LAS CLASES DE CONTROL-----------------------------------------------------------

//Variables para generación de formularios y almacen de datos $campos['Nombre_campo']="aray de atributos";
//Formulario para registrar nuevos usuarios de WP
    $atribs['tipo']="texto";
    $atribs['label']="Nombre de usuario";
    $atribs['obligatorio']=true; 
    $camposnewuser['username']=$atribs;
    unset($atribs);  
    $atribs['tipo']="texto";
    $atribs['label']="Email";
    $atribs['obligatorio']=true; 
    $camposnewuser['email']=$atribs;
    unset($atribs);  
    $atribs['tipo']="password";
    $atribs['label']="Contraseña";
    $atribs['obligatorio']=true; 
    $camposnewuser['password']=$atribs;
    unset($atribs);  
    $atribs['tipo']="password";
    $atribs['label']="Confirmar contraseña";
    $atribs['obligatorio']=true; 
    $camposnewuser['password2']=$atribs;
    unset($atribs);  
    $atribs['tipo']="texto";
    $atribs['label']="Nombre";
    $atribs['obligatorio']=true; 
    $camposnewuser['fname']=$atribs;
    unset($atribs);  
    $atribs['tipo']="texto";
    $atribs['label']="Apellidos";
    $atribs['obligatorio']=true; 
    $camposnewuser['lname']=$atribs;
    unset($atribs);  
    $atribs['tipo']="select2";
    $atribs['label']="Último grado de estudios";
    $atribs['obligatorio']=true;                
    $atribs['camposselect']="Ninguno^Ninguno¬Primaria^Primaria¬Secundaria^Secundaria¬Media superior^Media superior¬Superior^Superior";            
    $atribs['defaultselect']='';                
    $camposnewuser['cp_ult_gra']=$atribs;
    unset($atribs);
    $atribs['tipo']="select2";
    $atribs['label']="País de origen";
    $atribs['obligatorio']=true;                
    $atribs['camposselect']="México^México¬Otro^Otro";            
    $atribs['defaultselect']='México';                
    $camposnewuser['cp_p_origen']=$atribs;
    unset($atribs);
    $atribs['tipo']="div";
    $atribs['label']="Estado donde nació";
    $atribs['content']='<select name="cp_edo_nac" id="cp_edo_nac" value=""  class="required all-100" ><option value="">Seleccione...</option><option value="Aguascalientes">Aguascalientes</option><option value="Baja California">Baja California</option><option value="Baja California Sur">Baja California Sur</option><option value="Campeche">Campeche</option><option value="Coahuila de Zaragoza">Coahuila de Zaragoza</option><option value="Colima">Colima</option><option value="Chiapas">Chiapas</option><option value="Chihuahua">Chihuahua</option><option value="Ciudad de México">Ciudad de México</option><option value="Durango">Durango</option><option value="Guanajuato">Guanajuato</option><option value="Guerrero">Guerrero</option><option value="Hidalgo" selected="selected">Hidalgo</option><option value="Jalisco">Jalisco</option><option value="México">México</option><option value="Michoacán de Ocampo">Michoacán de Ocampo</option><option value="Morelos">Morelos</option><option value="Nayarit">Nayarit</option><option value="Nuevo León">Nuevo León</option><option value="Oaxaca">Oaxaca</option><option value="Puebla">Puebla</option><option value="Querétaro de Arteaga">Querétaro de Arteaga</option><option value="Quintana Roo">Quintana Roo</option><option value="San Luis Potosí">San Luis Potosí<option><option value="Sinaloa">Sinaloa</option><option value="Sonora">Sonora</option><option value="Tabasco">Tabasco</option><option value="Tamaulipas">Tamaulipas</option><option value="Tlaxcala">Tlaxcala</option><option value="Veracruz de Ignacio Llave">Veracruz de Ignacio Llave</option><option value="Yucatán">Yucatán</option><option value="Zacatecas">Zacatecas</option></select>'; 
    $camposnewuser['div-cp_edo_nac']=$atribs;
    unset($atribs);
    $atribs['tipo']="div";
    $atribs['label']="Municipio donde nació";
    $atribs['content']='<select name="cp_dom_nac" id="cp_dom_nac" value="" class="required all-100"><option value="">Seleccione...</option><option value="Acatlán">Acatlán</option><option value="Acaxochitlán">Acaxochitlán</option><option value="Actopan">Actopan</option><option value="Agua Blanca de Iturbide">Agua Blanca de Iturbide</option><option value="Ajacuba">Ajacuba</option><option value="Alfajayucan">Alfajayucan</option><option value="Almoloya">Almoloya</option><option value="Apan">Apan</option><option value="El Arenal">El Arenal</option><option value="Atitalaquia">Atitalaquia</option><option value="Atlapexco">Atlapexco</option><option value="Atotonilco el Grande">Atotonilco el Grande</option><option value="Atotonilco de Tula">Atotonilco de Tula</option><option value="Calnali">Calnali</option><option value="Cardonal">Cardonal</option><option value="Cuautepec de Hinojosa">Cuautepec de Hinojosa</option><option value="Chapantongo">Chapantongo</option><option value="Chapulhuacán">Chapulhuacán</option><option value="Chilcuautla">Chilcuautla</option><option value="Eloxochitlán">Eloxochitlán</option><option value="Emiliano Zapata">Emiliano Zapata</option><option value="Epazoyucan">Epazoyucan</option><option value="Francisco I. Madero">Francisco I. Madero</option><option value="Huasca de Ocampo">Huasca de Ocampo</option><option value="Huautla">Huautla</option><option value="Huazalingo">Huazalingo</option><option value="Huehuetla">Huehuetla</option><option value="Huejutla de Reyes">Huejutla de Reyes</option><option value="Huichapan">Huichapan</option><option value="Ixmiquilpan">Ixmiquilpan</option><option value="Jacala de Ledezma">Jacala de Ledezma</option><option value="Jaltocán">Jaltocán</option><option value="Juárez Hidalgo">Juárez Hidalgo</option><option value="Lolotla">Lolotla</option><option value="Metepec">Metepec</option><option value="San Agustín Metzquititlán">San Agustín Metzquititlán</option><option value="Metztitlán">Metztitlán</option><option value="Mineral del Chico">Mineral del Chico</option><option value="Mineral del Monte">Mineral del Monte</option><option value="La Misión">La Misión</option><option value="Mixquiahuala de Juárez">Mixquiahuala de Juárez</option><option value="Molango de Escamilla">Molango de Escamilla</option><option value="Nicolás Flores">Nicolás Flores</option><option value="Nopala de Villagrán">Nopala de Villagrán</option><option value="Omitlán de Juárez">Omitlán de Juárez</option><option value="San Felipe Orizatlán">San Felipe Orizatlán</option><option value="Pacula">Pacula</option><option value="Pachuca de Soto">Pachuca de Soto</option><option value="Pisaflores">Pisaflores</option><option value="Progreso de Obregón">Progreso de Obregón</option><option value="Mineral de la Reforma">Mineral de la Reforma</option><option value="San Agustín Tlaxiaca">San Agustín Tlaxiaca</option><option value="San Bartolo Tutotepec">San Bartolo Tutotepec</option><option value="San Salvador">San Salvador</option><option value="Santiago de Anaya">Santiago de Anaya</option><option value="Santiago Tulantepec de Lugo Guerrero">Santiago Tulantepec de Lugo Guerrero</option><option value="Singuilucan">Singuilucan</option><option value="Tasquillo">Tasquillo</option><option value="Tecozautla">Tecozautla</option><option value="Tenango de Doria">Tenango de Doria</option><option value="Tepeapulco">Tepeapulco</option><option value="Tepehuacán de Guerrero">Tepehuacán de Guerrero</option><option value="Tepeji del Río de Ocampo">Tepeji del Río de Ocampo</option><option value="Tepetitlán">Tepetitlán</option><option value="Tetepango">Tetepango</option><option value="Villa de Tezontepec">Villa de Tezontepec</option><option value="Tezontepec de Aldama">Tezontepec de Aldama</option><option value="Tianguistengo">Tianguistengo</option><option value="Tizayuca">Tizayuca</option><option value="Tlahuelilpan">Tlahuelilpan</option><option value="Tlahuiltepa">Tlahuiltepa</option><option value="Tlanalapa">Tlanalapa</option><option value="Tlanchinol">Tlanchinol</option><option value="Tlaxcoapan">Tlaxcoapan</option><option value="Tolcayuca">Tolcayuca</option><option value="Tula de Allende">Tula de Allende</option><option value="Tulancingo de Bravo">Tulancingo de Bravo</option><option value="Xochiatipan">Xochiatipan</option><option value="Xochicoatlán">Xochicoatlán</option><option value="Yahualica">Yahualica</option><option value="Zacualtipán de Ángeles">Zacualtipán de Ángeles</option><option value="Zapotlán de Juárez">Zapotlán de Juárez</option><option value="Zempoala">Zempoala</option><option value="Zimapán">Zimapán</option></select>'; 
    $camposnewuser['div-cp_dom_nac']=$atribs;
    unset($atribs);
    $atribs['tipo']="select2";
    $atribs['label']="Año de nacimiento";
    $anioini=date('Y');    
    $straniosc="";    
    for($aniofin=($anioini-100);$aniofin<$anioini;$anioini--){
        ($straniosc==="") ? $straniosc=$anioini."^".$anioini :  $straniosc.="¬".$anioini."^".$anioini;
    }
    $atribs['camposselect']=$straniosc;            
    $atribs['defaultselect']="".(date('Y')-20);  
    $atribs['obligatorio']=true; 
    $camposnewuser['cp_anio']=$atribs;
    unset($atribs); 
    $atribs['tipo']="select2";
    $atribs['label']="Mes de nacimiento";
    $anioini=1;    
    $straniosc="";
    $arraymeses=array(''=>'','01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
//    for($aniofin=12;$aniofin>=$anioini;$anioini++){
//        ($straniosc==="") ? $straniosc=$anioini."^".$arraymeses[$anioini] :  $straniosc.="¬".$anioini."^".$arraymeses[$anioini];
//    }
    foreach ($arraymeses as $key => $value) {
        if($key!==""){
            ($straniosc==="") ? $straniosc=$key."^".$value :  $straniosc.="¬".$key."^".$value;        
        }
    }
    $atribs['camposselect']=$straniosc;            
    $atribs['defaultselect']="";  
    $atribs['obligatorio']=true; 
    $camposnewuser['cp_mes']=$atribs;
    unset($atribs); 
    $atribs['tipo']="select2";
    $atribs['label']="Día de nacimiento";
    $anioini=1;    
    $straniosc="";
    for($aniofin=31;$aniofin>=$anioini;$anioini++){
        ($straniosc==="") ? $straniosc=$anioini."^".$anioini :  $straniosc.="¬".$anioini."^".$anioini;
    }
    $atribs['camposselect']=$straniosc;            
    $atribs['defaultselect']="";  
    $atribs['obligatorio']=true; 
    $camposnewuser['cp_dia']=$atribs;
    unset($atribs); 
    $atribs['tipo']="select2";
    $atribs['label']="Género";
    $atribs['obligatorio']=true;                
    $atribs['camposselect']="Hombre^Hombre¬Mujer^Mujer";            
    $atribs['defaultselect']='';                
    $camposnewuser['cp_gen']=$atribs;
    unset($atribs);
    $atribs['tipo']="texto";
    $atribs['label']="Cultura Pass";
    $atribs['obligatorio']=true; 
    $camposnewuser['cp_id_culturapass']=$atribs;
    unset($atribs);
    $atribs['tipo']="hidden";
    $atribs['value']="Consumidor";
    $camposnewuser['cp_tipo_usuario']=$atribs;
    unset($atribs);

//Formulario de Cobro****************
    $atribs['tipo']="texto";
    $atribs['label']="ID Cultura Pass";
    $atribs['obligatorio']=true; 
    $campos['idculturapass']=$atribs;
    unset($atribs);  
    $atribs['tipo']="select";
    $atribs['label']="Forma de Pago";
    $atribs['obligatorio']=true;                
    $atribs['camposselect']="idforma_pago,forma_pago";                
    $atribs['tablaselect']="formas_pago";  
    $atribs['whereselect']="status=1";             
    $atribs['defaultselect']='1';              
    $campos['idforma_pago']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']="Cantidad";
    $atribs['obligatorio']=true; 
    $campos['cantidad']=$atribs;
    unset($atribs);

//Formulario de Preventa    
    $atribs['tipo']="date";
    $atribs['label']="Fecha Evento";
    $atribs['attr']='min="'.date("Y-m-d").'"';
    $atribs['obligatorio']=true; 
    $campospreventa['preventa_fecha']=$atribs;
    unset($atribs);  
    
//Formulario de acceso****************
    $atribs['tipo']="select";
    $atribs['label']="Evento";
    $atribs['obligatorio']=true;                
    $atribs['camposselect']="ID,concat(post_title,' (',substring(meta_value,1,5),')') as fechah,substring(meta_value,1,5) as hora, '". date("Y-m-d")."' as date,meta_key,meta_value";                
    $atribs['tablaselect']="(
        SELECT ID,post_title, replace(meta_key, '_fecha', '') as meta_key_new FROM wp_postmeta join wp_posts on wp_posts.ID=wp_postmeta.post_id  WHERE wp_posts.post_type='eventos' and wp_posts.post_status='publish' and (meta_key like '%fechas_%' and meta_value='". date("Ymd")."') 
        group by wp_posts.ID
        ) as eventosxfech join wp_postmeta on eventosxfech.ID=wp_postmeta.post_id";  
    
    //ajuste de hora para posibles eventos (hasta 30 min despues de haber iniciado
    $nuevafecha = strtotime ( '-60 minute' , strtotime ( date("H:i:s") ) ) ;
    $nuevafecha = date ( 'H:i:s' , $nuevafecha );
    
    $atribs['whereselect']="meta_key like concat('%',meta_key_new,'_horarios_%') and meta_value>'". $nuevafecha."' and meta_value not like '%field_%'  group by eventosxfech.ID,meta_key,meta_value";                
//    $atribs['camposselect']='ID,post_title';                
//    $atribs['tablaselect']='wp_postmeta join wp_posts on wp_posts.ID=wp_postmeta.post_id';                
//    $atribs['whereselect']="wp_posts.post_type='eventos' and wp_posts.post_status='publish' and meta_key like '%fechas_%' and meta_value='". date("Ymd")."' group by wp_posts.ID";                
    $atribs['defaultselect']='';              
    $camposacceso['idevento']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']="Descuento";
    $atribs['attr']='value="0"';
    $atribs['obligatorio']=true; 
    $camposaccesocp['descuentoevent']=$atribs;
    unset($atribs); 
    $atribs['tipo']="number";
    $atribs['label']="puntos";
    $atribs['attr']='value="5"';
    $atribs['obligatorio']=true; 
    $camposaccesocp['puntosevent']=$atribs;
    unset($atribs); 
    $atribs['tipo']="texto";
    $atribs['label']="ID Cultura Pass";
    $atribs['obligatorio']=true; 
    $camposaccesocp['idculturapassevent']=$atribs;
    unset($atribs); 
    $atribs['tipo']="divisor3";
    $atribs['label']="";
    $atribs['classall']="50";
    $atribs['obligatorio']=true; 
    $camposaccesocp['saldocpevento']=$atribs;
    unset($atribs); 
    $atribs['tipo']="divisor3";
    $atribs['label']="";
    $atribs['classall']="50";
    $atribs['obligatorio']=true; 
    $camposaccesocp['eventomax']=$atribs;
    unset($atribs); 
    $atribs['tipo']="number";
    $atribs['label']="Cantidad de Boletos";
    $atribs['obligatorio']=true; 
    $camposaccesocp['cantidadboletos']=$atribs;
    unset($atribs);
    
//Formulario de Acesso Estadistica****************    
    $atribs['tipo']="divisor3";
    $atribs['label']="Hombres";
    $atribs['classall']="50";
    $atribs['attr']='style="text-align:center; font-size:1.2em; margin-bottom:10px; color: blue;"';
    $atribs['obligatorio']=false; 
    $camposaccesodata['hombreslabel']=$atribs;
    unset($atribs); 
    $atribs['tipo']="divisor3";
    $atribs['label']="Mujeres";
    $atribs['classall']="50";
    $atribs['attr']='style="text-align:center; font-size:1.2em; margin-bottom:10px; color: deeppink;"';
    $atribs['obligatorio']=false; 
    $camposaccesodata['mujereslabel']=$atribs;
    unset($atribs); 
    $atribs['tipo']="number";
    $atribs['label']='<i style="color:blue;" class="fa fa-child" aria-hidden="true"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="1" sexo="H" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nniñosh']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color: deeppink;" class="fa fa-child" aria-hidden="true"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="1" sexo="M" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nniñosm']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color:blue;" class="fas fa-walking"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="2" sexo="H" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nadolescenteh']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color: deeppink;" class="fas fa-walking"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="2" sexo="M" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nadolescentem']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color:blue;" class="fa fa-male" aria-hidden="true"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="3" sexo="H" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nadultoh']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color: deeppink;" class="fa fa-female" aria-hidden="true"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="3" sexo="M" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nadultom']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color:blue;" class="fa fa-blind" aria-hidden="true"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="4" sexo="H" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nadultomh']=$atribs;
    unset($atribs);
    $atribs['tipo']="number";
    $atribs['label']='<i style="color: deeppink;" class="fa fa-blind" aria-hidden="true"></i>';
    $atribs['class']="nboleto";
    $atribs['classall']="50";
    $atribs['classalli']="90";
    $atribs['attr']=' idgrupoedad="4" sexo="M" ';
    $atribs['attrlabel']=' style="text-align:center;" ';
    $atribs['obligatorio']=false; 
    $camposaccesodata['nadultomm']=$atribs;
    unset($atribs);
    $atribs['tipo']="divisor3";
    $atribs['label']="";
    $atribs['obligatorio']=false; 
    $camposaccesodata['endform']=$atribs;
    
    
//Formulario de Consulta de Saldo****************
    $atribs['tipo']="texto";
    $atribs['label']="ID Cultura Pass";
    $atribs['obligatorio']=true; 
    $camposconsulta['idculturapass']=$atribs;
    unset($atribs);
    
//Formulario de Acceso para eventos de preventa
    $atribs['tipo']="texto";
    $atribs['label']="ID Cultura Pass";
    $atribs['obligatorio']=true; 
    $camposaccesoprev['idcpacces_prev']=$atribs;
    unset($atribs);

    
//Formulario de Busqueda para para activar tarjeta
    $atribs['tipo']="texto";
    $atribs['label']="Nombre Usuario";
    $atribs['obligatorio']=false;                               
    $camposbuscar['user']=$atribs;
    unset($atribs);
    $atribs['tipo']="texto";
    $atribs['label']="Nombre";
    $atribs['obligatorio']=false;                               
    $camposbuscar['nombre']=$atribs;
    unset($atribs);
    $atribs['tipo']="texto";
    $atribs['label']="Email";
    $atribs['obligatorio']=false;                               
    $camposbuscar['email']=$atribs;
    unset($atribs);
    
//Fomurlario para pedir Culturapass para activar
    $atribs['tipo']="texto";
    $atribs['label']="ID Cultura Pass";
    $atribs['obligatorio']=true; 
    $camposactivar['idculturapass']=$atribs;
    unset($atribs);  
    
//*******************Fin generación de formularios ****************************//
    
    //Construcción del html para formulario Agregar y Editar (catalogos)
    //<!--Formato del bloque por campo.... 
    //        <div
    //            <label
    //            <div
    //                <input
    //            </div>
    //        </div>
    //        -->
    //<!--FIN Formato del bloque por campo....-->
    
    //definición de los ancho de campo (cambia de 5 en 5 a sumar 100)
    $ancholabel=40;
    $anchocampo=60;  
    
    //anchos de los formularios
    $anchoformadd='600px';
    
    $buscari=array("%_ancholabel_%", "%_anchocampo_%");
    $reemplazari=array($ancholabel, $anchocampo);
    
     //formulario de agregar usuario
    $formaddusr='<form id="FormAddUsr" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($camposnewuser as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formaddusr.=str_ireplace($buscari,$reemplazari,$dominput);
        }
    $formaddusr.='</form>';
    
     //formulario de agregar saldo
    $formadd='<form id="FormAgrega" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($campos as $key=>$value){           
            $dominput=$consultastc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formadd.=str_ireplace($buscari,$reemplazari,$dominput);
        }
    $formadd.='</form>';
    
     //formulario de consulta de saldos
    $formconsulta='<form id="FormConsulta" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($camposconsulta as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formconsulta.=str_ireplace($buscari,$reemplazari,$dominput);
        }
    $formconsulta.='<div id="detalles_saldos"></div></form>';
    
     //formulario de acceso
    $formacceso='<form id="FormAccesa" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($camposacceso as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formacceso.=str_ireplace($buscari,$reemplazari,$dominput);
        }
        $formacceso.='<div id="formdetacceso" style="display:none; all-100;">'
                . '<div id="costoeventolabel"></div>';
        foreach($camposaccesocp as $key=>$value){  
                $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            if($key!=="descuentoevent"){                
                $formacceso.=str_ireplace($buscari,$reemplazari,$dominput);                
            }else{
                $reemplazari2=array(40, 50);
                $formacceso.=str_ireplace($buscari,$reemplazari2,$dominput); 
            }
        }
        $formacceso.="</div><hr>";                
        $formacceso.='<div id="formdatacceso" style="all-100; display:none;">';                
        foreach($camposaccesodata as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formacceso.=str_ireplace($buscari,$reemplazari,$dominput);
        }
        $formacceso.="</div>";                
    $formacceso.='</form>';
    
     //formulario de acceso
    $formpreventa='<form id="FormPreventa" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($campospreventa as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formpreventa.=str_ireplace($buscari,$reemplazari,$dominput);
        }        
        $formpreventa.='<div id="formeventpreventa" style="display:none; all-100;">';
        $formpreventa.="<hr>"; 
        foreach($camposacceso as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formpreventa.=str_ireplace($buscari,$reemplazari,$dominput);
        }
        $formpreventa.='</div><div id="formdetacceso" style="display:none; all-100;">'
                . '<div id="costoeventolabel"></div>';
        foreach($camposaccesocp as $key=>$value){  
                $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            if($key!=="descuentoevent"){                
                $formpreventa.=str_ireplace($buscari,$reemplazari,$dominput);                
            }else{
                $reemplazari2=array(40, 50);
                $formpreventa.=str_ireplace($buscari,$reemplazari2,$dominput); 
            }
        }
        $formpreventa.="</div><hr>";                
        $formpreventa.='<div id="formdatacceso" style="all-100; display:none;">';                
        foreach($camposaccesodata as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formpreventa.=str_ireplace($buscari,$reemplazari,$dominput);
        }
        $formpreventa.="</div>";      
        
    $formpreventa.='</form>';
        
    //formulario de acceso a eventos preventa
    $formaccesoprev='<form id="FormAccesoPreventa" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($camposaccesoprev as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formaccesoprev.=str_ireplace($buscari,$reemplazari,$dominput);
        }
    $formaccesoprev.='<div id="boletospreventa" style="all-100; display:none;"><table class="ink-table"><thead><tr><th style=" padding:5px;">Evento</th><th style="text-align:center; padding:5px;  max-width:50px; width:50px;"><i class="fa fa-venus-mars" ></i></th><th style="text-align:center; padding:5px;  max-width:50px; width:50px;"><i class="fa fa-ticket-alt" aria-hidden="true"></i></th><th style="text-align:center; padding:5px; max-width:60px; width:60px;"><i class="fa fa-receipt" aria-hidden="true"></i></th></tr></thead><tbody id="boletospreventabody"></tbody></table></div></form>';
    
    //formulario de busqueda de usuarios CP
    //formulario de agregar
    $formbuscar='<form id="FormBuscar" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">';
        foreach($camposbuscar as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value, false);
            $formbuscar.=str_ireplace($buscari,$reemplazari,$dominput);
        }
    $formbuscar.='<div id="loadresults" style="text-align:center;"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></div><div><table id="resultssearch" class="ink-table condensed-300"><thead><tr class="nohov"><th>Nombre Usuario</th><th>Nombre</th><th>Apellidos</th><th>Email</th></tr></thead><tbody id="bodyresults"></tbody></table></div></form>';    
    
    //formulario de consulta de saldos
    $formactiva='<form id="FormActivar" class="ink-form all-100 content-center" action="" method="post" autocomplete="off">'
            . '<div id="detalles_activar"></div>';
        foreach($camposconsulta as $key=>$value){           
            $dominput=$consultasloc->obtener_plantilla($value['tipo'], $key, $value,false);
            $formactiva.=str_ireplace($buscari,$reemplazari,$dominput);
        }
    $formactiva.='</form>';
    
    //formulario de reporte
    $formreporte='<form id="FormReporte" class="ink-form all-100 content-center" action="" method="post" autocomplete="off"><div id="detalles_repo"></div>';        
    $formreporte.='<div id="loadresults" style="text-align:center;"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></div><div><table id="resultssearch" class="ink-table condensed-300"><thead><tr class="nohov"><th>Nombre Usuario</th><th>Nombre</th><th>Apellidos</th><th>Email</th><th>Fecha Registro</th></tr></thead><tbody id="bodyresults"></tbody></table></div></form>';    
    
    //formateo de las cadenas de los formularios definidos por el usuario a una cadena serializada.(NO MODIFICAR)
    $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
    $reemplazar=array("", "", "", "");
    $formaddusr=str_ireplace($buscar,$reemplazar,$formaddusr);
    $formadd=str_ireplace($buscar,$reemplazar,$formadd);
    $formconsulta=str_ireplace($buscar,$reemplazar,$formconsulta);
    $formacceso=str_ireplace($buscar,$reemplazar,$formacceso);    
    $formpreventa=str_ireplace($buscar,$reemplazar,$formpreventa);    
    $formaccesoprev=str_ireplace($buscar,$reemplazar,$formaccesoprev);    
    $formbuscar=str_ireplace($buscar,$reemplazar,$formbuscar);
    $formactiva=str_ireplace($buscar,$reemplazar,$formactiva);
    $formreporte=str_ireplace($buscar,$reemplazar,$formreporte);
?>
    <!--<link href="../wp-content/libccd/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>-->
    <!--Archivos CSS de INK, y Fuentes-->  
    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/ink.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/quick-start.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/style.css">
    <!--<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/font-awesome.css">-->
    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/ink-flex.min.css">
    <!--<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/font-awesome.min.css">-->
    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>fontawesome/css/all.min.css">
    
    <script type="text/javascript" src="<?php echo $path; ?>js/holder.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/ink.min.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/ink-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/ink-all.min.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/autoload.js"></script>
    
    <link href="<?php echo $path; ?>css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $path; ?>js/jquery-confirm.js" ></script>
    
        <script>
        jQuery(document).ready(function(){
            jQuery(function ($) {
                //Página a la que se realizarán las peticiones AJAX y solicitudes de información
                var paginaact='<?php echo $pagina; ?>';                
                var paginacontrolador='<?php echo $pathcontrolador; ?>';                
                var widthjc="50%";    
                var width = $(window).width();
                if(width<=360){
                    widthjc="99%";
                }else if(width>360 && width<=750){
                    widthjc="70%";
                }
                
                var android=false;
                var webbrowser='<?php echo $_SERVER['HTTP_X_REQUESTED_WITH'];?>';
                if(webbrowser!==""){
                    android=true;
                }
               
                var nclicks=0;
                
                $('#agregarusr').click(function (e){                        
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var consultajc = $.confirm({
                        title: '<b>Activar Cultura Pass</b>',
                        content: '<?php echo trim($formaddusr); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {                                        
                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){                                        
                                        if(!valida("#FormAddUsr")){
                                            $('#precargadiv').fadeOut('fast');
                                            return false;
                                        }else{                                            
                                            var valid =true;
                                            
                                            $('p.errorusername').remove();
                                            $('#username').removeAttr('style'); 
                                            $('p.errormail').remove();
                                            $('#email').removeAttr('style'); 
                                            $('p.errorpassword').remove();
                                            $('#password').removeAttr('style'); 
                                            $('p.errorpassword2').remove();
                                            $('#password2').removeAttr('style'); 
                                            $('p.errorusername2').remove();
                                            $('#username').removeAttr('style'); 
                                            $('p.errorusername3').remove();
                                            $('#username').removeAttr('style'); 
                                            $('p.erroremail').remove();
                                            $('#email').removeAttr('style'); 
                                            $('p.erroremail2').remove();
                                            $('#email').removeAttr('style');
                                            $('#cp_id_culturapass').removeAttr('style');
                                            
                                            if($('#username').val().length<4){							
                                                $('#username').addClass('errorstyle');
                                                $('#divusername').after('<p class="errorusername" style="color:red;">El nombre de usuario debe contener al menos 4 caracteres!</p>');
                                                $('#username').focus();
                                                valid=false;
                                            }
                                            
                                            if($('#password').val().length<5){							
                                                $('#password').addClass('errorstyle');
                                                $('#divpassword').after('<p class="errorpassword" style="color:red;">El Password debe contener al menos 5 caracteres!</p>');
                                                $('#password').focus();
                                                valid=false;
                                            }

                                            if($('#password').val()!==$('#password2').val()){							
                                                $('#password2').addClass('errorstyle');
                                                $('#divpassword2').after('<p class="errorpassword2" style="color:red;">El Password y la Confirmación no coinciden!</p>');
                                                $('#password2').focus();
                                                valid=false;
                                            }
                                            
                                            var infoDatac = new FormData();
                                            infoDatac.append('validarrega','true');
                                            infoDatac.append('type','validusrexist');
                                            infoDatac.append('element',$('#username').val());
                                            var usrexist=obtener_info_local(infoDatac,false);
                                            if(usrexist.indexOf("Existe") > -1){
                                                $('#username').addClass('errorstyle');
                                                $('#divusername').after('<p class="errorusername2" style="color:red;">El nombre de usuario ya esta registrado!<br>Favor de intentar con otro.</p>');
                                                $('#username').focus();
                                                valid=false;
                                            }
                                                    
                                            infoDatac = new FormData();
                                            infoDatac.append('validarrega','true');
                                            infoDatac.append('type','validusrname');
                                            infoDatac.append('element',$('#username').val());
                                            var usrexist=obtener_info_local(infoDatac,false);
                                            if(usrexist.indexOf("Error") > -1){
                                                $('#username').addClass('errorstyle');
                                                $('#divusername').after('<p class="errorusername3" style="color:red;">El nombre de usuario no es válido!<br>Favor de intentar con otro.</p>');
                                                $('#username').focus();
                                                valid=false;
                                            }

                                            infoDatac = new FormData();
                                            infoDatac.append('validarrega','true');
                                            infoDatac.append('type','validemail');
                                            infoDatac.append('element',$('#email').val());
                                            var usrexist=obtener_info_local(infoDatac,false);
                                            if(usrexist.indexOf("Error") > -1){
                                                $('#email').addClass('errorstyle');
                                                $('#divemail').after('<p class="erroremail" style="color:red;">El email no es válido!<br>Favor de intentar con otro.</p>');
                                                $('#email').focus();
                                                valid=false;
                                            }

                                            infoDatac = new FormData();
                                            infoDatac.append('validarrega','true');
                                            infoDatac.append('type','validemailexist');
                                            infoDatac.append('element',$('#email').val());
                                            var emailexist=obtener_info_local(infoDatac,false);
                                            if(emailexist.indexOf("Existe") > -1){
                                                $('#email').addClass('errorstyle');
                                                $('#divemail').after('<p class="erroremail2" style="color:red;">El email ya esta registrado!<br>Favor de intentar con otro.</p>');
                                                $('#email').focus();
                                                valid=false;
                                            }        
                                                    
                                            infoDatac = new FormData();
                                            infoDatac.append('validcultuspassid',$('#cp_id_culturapass').val());
                                            var usrexist=obtener_info_local(infoDatac,false);
                                            if(usrexist!=0){
                                                $.dialog({
                                                    title: "Atención!",
                                                    content: "El Número de la tarjeta ya se encuentra activado!<br>Favor de verificar el número.",
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {
                                                        }
                                                    }
                                                });
                                                $('#cp_id_culturapass').addClass('errorstyle');
                                                $('#cp_id_culturapass').focus();
                                                valid=false;
                                            }               
                                                    
                                            $('#precargadiv').fadeOut('fast');
                                            
                                            if(!valid){   
                                                return false;
                                            }else{
                                                var formData = new FormData();
                                                formData.append('addnewuser_wpcp','true');
                                               
                                                //obtener los input junto con sus valores para agregarlos a la petición
                                                $('#FormAddUsr input,#FormAddUsr select').each(function(){
                                                    if($(this).attr('id')!==undefined){
                                                        formData.append($(this).attr('id'),$(this).val());
                                                    }
                                                });
                                                
                                                senddata_local(formData,true,false,"");   
                                            }
                                            
                                        }
                                    });
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
//                            $('#idculturapass').focus();
                            
//                            if(android){
//                                this.$content.find('#idculturapass').on('keyup', function (e) {
//                                    // if the user submits the form by pressing enter in the field.
//                                    e.preventDefault();
//                                    jc.$$formSubmit.trigger('change'); // reference the button and click it
//                                });
//                            }
                        }
                    });                    
                });
                          
                $('#abono').click(function (e){    
                    
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var abonojc = $.confirm({
                        title: '<b>Abonar Saldo</b>',
                        content: '<?php echo trim($formadd); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {
                                    if(!valida("#FormAgrega")){
                                        return false;
                                    }else{
                                        
                                        $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                            var validacultus=0;
                                            var infoData = new FormData();
                                            infoData.append('obtener_info',"single");
                                            infoData.append('campos','count(*) as validcultus');
                                            infoData.append('tabla','wp_usermeta');
                                            infoData.append('where',"meta_key='cp_id_culturapass' and meta_value='"+$('#idculturapass').val()+"'");
                                            //Vaciado de la información actual
                                            $.each(obtener_info(infoData,true), function(key, value){ 
                                                validacultus=this.validcultus;
                                            });

                                            if(validacultus==0){
                                                $('#precargadiv').fadeOut('fast');
                                                $.dialog({
                                                    title: 'Atención!',
                                                    content: 'La tarjeta no se encuentra activada!',
                                                    icon: 'fa fa-exclamation-circle',
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {
                                                        }
                                                    }
                                                });
                                                return false;
                                            }

                                            //validar si la tarjeta tiene movimientos corruptos
                                            var infoDatac = new FormData();
                                            infoDatac.append('validcp',"true");
                                            infoDatac.append('idculturapass',$('#idculturapass').val());
                                            infoDatac.append('idforma_pago',$('#idforma_pago').val());
                                            
                                            var formapagostr=$('#idforma_pago option:selected').text();
                                            
                                            //Vaciado de la información actual
                                            if(!obtener_info(infoDatac,false)){
                                                $.dialog({
                                                    title: 'Atención!',
                                                    content: '<b style="color:red;" class="push-center"><i class="fa fa-warning"></i> Movimientos Corruptos!<br>La tarjeta se desactivará y se comenzará investigación.</b>',
                                                    icon: 'fa fa-exclamation-circle',
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {
                                                        }
                                                    }
                                                });
                                                $('#precargadiv').fadeOut('fast');
                                                return false;
                                            }

                                            //obtener el nbombre del propietario de la tarjeta
                                            //
                                            var infoDatac = new FormData();
                                            infoDatac.append('getnameuswp',"true");
                                            infoDatac.append('idculturapass',$('#idculturapass').val());
                                            var nombreusuario = obtener_info(infoDatac,false);
                                            
                                            
                                            $('#precargadiv').fadeOut('fast');    
                                            $.confirm({
                                                title: '<b>Confirmar Transacción</b>',
                                                icon: 'fa fa-warning',
                                                type: 'red',
                                                content: 'Propietario: <b>'+nombreusuario+'</b><br><span style="font-size:18px;">¿Deseas abonar la cantidad de <b style="color:red">$'+parseFloat($('#cantidad').val()).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</b> a la tarjeta <b style="color:red">No. '+$('#idculturapass').val()+'</b>?</span><br>Forma de Pago: <b>'+formapagostr+'</b>',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                escapeKey: 'cancel',
                                                buttons: {
                                                    formSubmit: {
                                                        text: 'Aceptar',
                                                        btnClass: 'btn-green',
                                                        action: function () {
                                                            if(!valida("#FormAgrega")){
                                                                return false;
                                                            }else{   
                                                                $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                                                    //formato de la información a enviar por POST con isntrucción SAVE
                                                                    var formData = new FormData();
                                                                    formData.append('abono','true');
                                                                    formData.append('idusuariosc','<?php echo $usuariologin; ?>');

                                                                    //obtener los input junto con sus valores para agregarlos a la petición
                                                                    $('#FormAgrega input,#FormAgrega select').each(function(){
                                                                        if($(this).attr('id')!==undefined){
                                                                            formData.append($(this).attr('id'),$(this).val());
                                                                        }
                                                                    });

                                                                    //bloquear boton para evitar multiples envios de información
//                                                                    this.$$formSubmit.prop('disabled', 'disabled');

                                                                    //procesar petición
                                                                    senddata(formData,true,true,"Imprimir Recibo.");     
//                                                                    abonojc.close();
                                                                });
                                                            }                                                         
                                                        }
                                                    },
                                                    cancel: {
                                                        text: 'Cancelar',
                                                        btnClass: 'btn-red',
                                                        action: function () {
                                                        //close
                                                        }
                                                    }
                                                },
                                                onContentReady: function () {
                                                    // bind to events
                                                    var jc = this;
                                                    this.$content.find('form').on('submit', function (e) {
                                                        // if the user submits the form by pressing enter in the field.
                                                        e.preventDefault();
                                                        jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                    });
                                                }
                                            }); 
                                        });                                        
                                    }   
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            
                            $('#idculturapass').focus();
                            
                            if(android){
                                this.$content.find('#idculturapass').on('keyup', function (e) {
                                    // if the user submits the form by pressing enter in the field.
                                    e.preventDefault();
                                    $('#cantidad').focus();
                                });
                            }
                        }
                    });
                    
                });
                
                $('#consulta').click(function (e){                        
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var consultajc = $.confirm({
                        title: '<b>Consulta de Saldo</b>',
                        content: '<?php echo trim($formconsulta); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {     
                                    $('#detalles_saldos').html(''); 
                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                        $('#detalles_saldos').html('');
                                        if(!valida("#FormConsulta")){
                                            $('#precargadiv').fadeOut('fast');
                                            return false;
                                        }else{
                                            var validacultus=0;
                                            var infoData = new FormData();
                                            infoData.append('obtener_info',"single");
                                            infoData.append('campos','count(*) as validcultus');
                                            infoData.append('tabla','wp_usermeta');
                                            infoData.append('where',"meta_key='cp_id_culturapass' and meta_value='"+$('#idculturapass').val()+"'");
                                            //Vaciado de la información actual
                                            $.each(obtener_info(infoData,true), function(key, value){ 
                                                validacultus=this.validcultus;                                            
                                            });

                                            if(validacultus==0){
                                                $('#precargadiv').fadeOut('fast');
                                                $.dialog({
                                                    title: 'Atención!',
                                                    content: 'La tarjeta no se encuentra activada!',
                                                    icon: 'fa fa-exclamation-circle',
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {
                                                        }
                                                    },onContentReady:function(){                                                        
                                                    }
                                                });

                                                return false;
                                            }else{
                                                var infoDatac = new FormData();
                                                infoDatac.append('consulta','true');
                                                infoDatac.append('idculturapass',$('#idculturapass').val());
                                                var saldos=obtener_info(infoDatac,false);

                                                $('#detalles_saldos').html(saldos);      
                                                $('#precargadiv').fadeOut('fast');
                                            }
                                        }
                                    });
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            $('#idculturapass').focus();
                            
//                            if(android){
//                                this.$content.find('#idculturapass').on('keyup', function (e) {
//                                    // if the user submits the form by pressing enter in the field.
//                                    e.preventDefault();
//                                    jc.$$formSubmit.trigger('change'); // reference the button and click it
//                                });
//                            }
                        }
                    });                    
                });
                
                $('body').on('click','#showmovs',function(){
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var movimientosi=10;
                    var idculturapass=$('#idculturapass').val();
                    var movimientos="";
                    
                    
                    var consultajc = $.confirm({
                        title: '<b>Consulta de Saldo</b>',
                        content: '<table class="ink-table"><thead><tr><th>Fecha</th><th style="text-align:center;">Monto</th><th style="text-align:center;">Movimiento</th></tr></thead><tbody id="showmovimientosbody">'+movimientos+'</tbody></table>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Ver Más',
                                btnClass: 'btn-green',
                                action: function () {
                                    movimientosi+=10;
                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){ 
                                        var infoDatac = new FormData();
                                        infoDatac.append('consultamovimientos','true');
                                        infoDatac.append('nmovimientos',movimientosi);
                                        infoDatac.append('idculturapass',idculturapass);
                                        movimientos=obtener_info(infoDatac,false);
                                        $('#showmovimientosbody').html(movimientos);
                                        $('#precargadiv').fadeOut('fast');
                                    });
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Salir',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            }); 
                            
                            $.when($('#precargadiv').fadeIn('fast')).then(function(){ 
                                var infoDatac = new FormData();
                                infoDatac.append('consultamovimientos','true');
                                infoDatac.append('nmovimientos',movimientosi);
                                infoDatac.append('idculturapass',idculturapass);
                                movimientos=obtener_info(infoDatac,false);
                                $('#showmovimientosbody').html(movimientos);
                                $('#precargadiv').fadeOut('fast');
                            });
                        }
                    });  
                    
                });
                
                
                $('#acceso').click(function (e){                       
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var abonojc = $.confirm({
                        title: '<b>Acceso a Evento</b>',
                        content: '<?php echo trim($formacceso); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {                                    
                                    if(!valida("#FormAccesa")){
                                        return false;
                                    }else{
                                        nclicks++;
                                        if(nclicks>1){
                                            console.log('previniendo evento: '+nclicks);
                                            return false;
                                        }
                                        var nboletos = 0;                                    
                                        var nboletosd=0;
                                        var totalcompra=0;
                                        var costob=parseFloat($('#costoeventolabel').attr("costo"));
                                        var descuento=parseFloat($('#descuentoevent').val());
                                        
                                        var puntos=0;
                                        
                                        descuento=descuento/100;
                                        costob=costob-(costob*descuento);
                                        
                                        var textboletos="boleto";
                                        
                                        nboletos = parseFloat($('#cantidadboletos').val());
                                        $('input.nboleto').each(function(){                                            
                                            if($(this).val()!==""){
                                                var nb=parseFloat($(this).val());
                                                nboletosd+=nb;  
                                            }
                                        });
                                        nboletosd = parseFloat(nboletosd);
                                        
                                        if(nboletos>1){
                                            textboletos="boletos";
                                        }
                                        
                                        totalcompra=parseFloat(nboletos*costob);
                                        
                                        if(costob==0){
                                            puntos=Math.trunc(parseFloat($('#puntosevent').val()));
                                        }else{
                                            puntos=Math.trunc(totalcompra*.10);
                                        }
                                        
                                        //tipo de movimiento en puntos 
                                        //1->Abono
                                        //2->Cargo
                                        var movimientopuntos=1;                                        
                                        
                                        if(nboletos!=nboletosd){
                                            $.dialog({
                                                title: 'Atención!',
                                                content: 'La cantidad de boletos deseada y el desgloce de los mismos debe ser igual!',
                                                icon: 'fa fa-exclamation-circle',
                                                type: 'red',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                buttons: {
                                                    close: function () {
                                                    }
                                                }
                                            });
                                            nclicks=0;
                                            return false;
                                        }else{                                            
                                            //obtener el nbombre del propietario de la tarjeta
                                            //
                                            var infoDatac = new FormData();
                                            infoDatac.append('getnameuswp',"true");
                                            infoDatac.append('idculturapass',$('#idculturapassevent').val());
                                            var nombreusuario = obtener_info(infoDatac,false);
                                            
                                            $.confirm({
                                                title: '<b>Confirmar Transacción</b>',
                                                icon: 'fa fa-warning',
                                                type: 'red',
                                                content: 'Propietario: <b>'+nombreusuario+'</b><br><span style="font-size:18px;">¿Confirmar la compra de <b style="color:red">'+nboletos
                                                    +' '+textboletos+' </b> por la cantidad de <b style="color:red">$'+parseFloat(totalcompra).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+
                                                    '</b> de la tarjeta <b style="color:red">No. '+$('#idculturapassevent').val()+'</b>?<br>Puntos a acumular: <b style="color:red">'+puntos+'</b> </span>',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                escapeKey: 'cancel',
                                                buttons: {
                                                    formSubmit: {
                                                        text: 'Aceptar',
                                                        btnClass: 'btn-green',
                                                        action: function () {
                                                            $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                                                //formato de la información a enviar por POST con isntrucción SAVE
                                                                var formData = new FormData();
                                                                formData.append('cargo','true');
                                                                formData.append('cantidad',totalcompra);
                                                                formData.append('puntos',puntos);
                                                                formData.append('movimientopuntos',movimientopuntos);
                                                                formData.append('idculturapass',$('#idculturapassevent').val());

                                                                formData.append('idservicio','1');
                                                                formData.append('idevento',$('#idevento').val());
                                                                formData.append('fecha',$('#idevento option:selected').attr('attradd2'));
                                                                formData.append('hora',$('#idevento option:selected').attr('attradd'));
                                                                formData.append('idusuariosc','<?php echo $usuariologin; ?>');
                                                                formData.append('cantidadb',nboletos);

                                                                var estadistica="";

                                                                $('input.nboleto').each(function(){
                                                                    if($(this).val()!==""){
                                                                        if(estadistica===""){
                                                                            estadistica=$(this).attr('idgrupoedad')+";"+$(this).attr('sexo')+";"+$(this).val()+";"+$(this).val()+";"+$(this).val();
                                                                        }else{
                                                                            estadistica+="|"+$(this).attr('idgrupoedad')+";"+$(this).attr('sexo')+";"+$(this).val()+";"+$(this).val()+";"+$(this).val();
                                                                        }                         
                                                                    }
                                                                });


                                                                formData.append('estadistica',estadistica);

                                                                //obtener los input junto con sus valores para agregarlos a la petición
    //                                                                $('#FormAgrega input,#FormAgrega select').each(function(){
    //                                                                    if($(this).attr('id')!==undefined){
    //                                                                        formData.append($(this).attr('id'),$(this).val());
    //                                                                    }
    //                                                                });

                                                                //bloquear boton para evitar multiples envios de información
//                                                                this.$$formSubmit.prop('disabled', 'disabled');

                                                                //procesar petición
                                                                senddata(formData,false,true,"Imprimir Recibo.");  
                                                                $('#idevento').change();
                                                                $('#idculturapassevent').focus();
                                                                $('#precargadiv').fadeOut('fast');
//                                                                abonojc.close();     
                                                                nclicks=0;
                                                            });
                                                        }
                                                    },
                                                    cancel: {
                                                        text: 'Cancelar',
                                                        btnClass: 'btn-red',
                                                        action: function () {
                                                            nclicks=0;
                                                        //close
                                                        }
                                                    }
                                                },
                                                onContentReady: function () {
                                                    // bind to events
                                                    var jc = this;
                                                    this.$content.find('form').on('submit', function (e) {
                                                        // if the user submits the form by pressing enter in the field.
                                                        e.preventDefault();
                                                        jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                    });
                                                }
                                            });    
                                        }
                                    }
                                    
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            
                            $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                var infoData = new FormData();
                                infoData.append('obtener_eventoshoy',"true");
                                
                                var eventos=obtener_info(infoData,false);
                                
                                if(eventos!==""){
                                    $('#idevento').html('<option value="">Seleccione...</option>'+eventos);
                                }else{
                                    $('#idevento').html('<option value="">Sin Eventos para Mostrar.</option>');
                                }
                                
//                                $('#idevento').html('<option value="">Seleccione...</option>'+obtener_info(infoData,false));
                                $('#precargadiv').fadeOut('fast');
                            });
                            
                            
                            if(android){
                                this.$content.find('#idculturapassevent').on('keyup', function (e) {
                                    // if the user submits the form by pressing enter in the field.
//                                    e.preventDefault();
//                                    $('#idculturapassev ent').change();
                                    $('#cantidadboletos').focus();
                                });
                            }
                            
                        }
                    });
                    
                });
               
                $('#preventa').click(function (e){                       
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var abonojc = $.confirm({
                        title: '<b>Preventa de Boletos</b>',
                        content: '<?php echo trim($formpreventa); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {                                    
                                    if(!valida("#FormAccesa")){
                                        return false;
                                    }else{
                                        nclicks++;
                                        if(nclicks>1){
                                            console.log('previniendo evento: '+nclicks);
                                            return false;
                                        }
                                        var nboletos = 0;                                    
                                        var nboletosd=0;
                                        var totalcompra=0;
                                        var costob=parseFloat($('#costoeventolabel').attr("costo"));
                                        var descuento=parseFloat($('#descuentoevent').val());
                                        
                                        var puntos=0;
                                        
                                        descuento=descuento/100;
                                        costob=costob-(costob*descuento);
                                        
                                        var textboletos="boleto";
                                        
                                        nboletos = parseFloat($('#cantidadboletos').val());
                                        $('input.nboleto').each(function(){                                            
                                            if($(this).val()!==""){
                                                var nb=parseFloat($(this).val());
                                                nboletosd+=nb;  
                                            }
                                        });
                                        nboletosd = parseFloat(nboletosd);
                                        
                                        if(nboletos>1){
                                            textboletos="boletos";
                                        }
                                        
                                        totalcompra=parseFloat(nboletos*costob);
                                        
                                        if(costob==0){
                                            puntos=Math.trunc(parseFloat($('#puntosevent').val()));
                                        }else{
                                            puntos=Math.trunc(totalcompra*.10);
                                        }
                                        
                                        //tipo de movimiento en puntos 
                                        //1->Abono
                                        //2->Cargo
                                        var movimientopuntos=1;                                        
                                        
                                        if(nboletos!=nboletosd){
                                            $.dialog({
                                                title: 'Atención!',
                                                content: 'La cantidad de boletos deseada y el desgloce de los mismos debe ser igual!',
                                                icon: 'fa fa-exclamation-circle',
                                                type: 'red',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                buttons: {
                                                    close: function () {
                                                    }
                                                }
                                            });
                                            nclicks=0;
                                            return false;
                                        }else{                                            
                                            //obtener el nbombre del propietario de la tarjeta
                                            //
                                            var infoDatac = new FormData();
                                            infoDatac.append('getnameuswp',"true");
                                            infoDatac.append('idculturapass',$('#idculturapassevent').val());
                                            var nombreusuario = obtener_info(infoDatac,false);
                                            
                                            $.confirm({
                                                title: '<b>Confirmar Transacción</b>',
                                                icon: 'fa fa-warning',
                                                type: 'red',
                                                content: 'Propietario: <b>'+nombreusuario+'</b><br><span style="font-size:18px;">¿Confirmar la compra de <b style="color:red">'+nboletos
                                                        +' '+textboletos+' </b> por la cantidad de <b style="color:red">$'+parseFloat(totalcompra).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+
                                                        '</b> de la tarjeta <b style="color:red">No. '+$('#idculturapassevent').val()+'</b>?<br>Puntos a acumular: <b style="color:red">'+puntos+'</b> </span>',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                escapeKey: 'cancel',
                                                buttons: {
                                                    formSubmit: {
                                                        text: 'Aceptar',
                                                        btnClass: 'btn-green',
                                                        action: function () {
                                                            $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                                                //formato de la información a enviar por POST con isntrucción SAVE
                                                                var formData = new FormData();
                                                                formData.append('cargo','true');
                                                                formData.append('cantidad',totalcompra);
                                                                formData.append('puntos',puntos);
                                                                formData.append('movimientopuntos',movimientopuntos);
                                                                formData.append('idculturapass',$('#idculturapassevent').val());

                                                                formData.append('idservicio','1');
                                                                formData.append('idevento',$('#idevento').val());
                                                                formData.append('fecha',$('#idevento option:selected').attr('attradd2'));
                                                                formData.append('hora',$('#idevento option:selected').attr('attradd'));
                                                                formData.append('idusuariosc','<?php echo $usuariologin; ?>');
                                                                formData.append('cantidadb',nboletos);                                                                

                                                                var estadistica="";

                                                                $('input.nboleto').each(function(){
                                                                    if($(this).val()!==""){
                                                                        if(estadistica===""){
                                                                            estadistica=$(this).attr('idgrupoedad')+";"+$(this).attr('sexo')+";"+$(this).val()+";0";
                                                                        }else{
                                                                            estadistica+="|"+$(this).attr('idgrupoedad')+";"+$(this).attr('sexo')+";"+$(this).val()+";0";
                                                                        }                         
                                                                    }
                                                                });


                                                                formData.append('estadistica',estadistica);

                                                                //obtener los input junto con sus valores para agregarlos a la petición
    //                                                                $('#FormAgrega input,#FormAgrega select').each(function(){
    //                                                                    if($(this).attr('id')!==undefined){
    //                                                                        formData.append($(this).attr('id'),$(this).val());
    //                                                                    }
    //                                                                });

                                                                //bloquear boton para evitar multiples envios de información
//                                                                this.$$formSubmit.prop('disabled', 'disabled');

                                                                //procesar petición
                                                                senddata(formData,true,true,"Imprimir Recibo.");  
                                                                $('#idevento').change();
                                                                $('#idculturapassevent').focus();
                                                                $('#precargadiv').fadeOut('fast');
//                                                                abonojc.close();     
                                                                nclicks=0;
                                                            });
                                                        }
                                                    },
                                                    cancel: {
                                                        text: 'Cancelar',
                                                        btnClass: 'btn-red',
                                                        action: function () {
                                                            nclicks=0;
                                                        //close
                                                        }
                                                    }
                                                },
                                                onContentReady: function () {
                                                    // bind to events
                                                    var jc = this;
                                                    this.$content.find('form').on('submit', function (e) {
                                                        // if the user submits the form by pressing enter in the field.
                                                        e.preventDefault();
                                                        jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                    });
                                                }
                                            });    
                                        }
                                    }
                                    
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            
                            $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                var infoData = new FormData();
                                infoData.append('obtener_eventoshoy',"true");

                                $('#idevento').html('<option value="">Seleccione...</option>'+obtener_info(infoData,false));
                                $('#precargadiv').fadeOut('fast');
                            });
                            
                            
                            if(android){
                                this.$content.find('#idculturapassevent').on('keyup', function (e) {
                                    // if the user submits the form by pressing enter in the field.
//                                    e.preventDefault();
//                                    $('#idculturapassev ent').change();
                                    $('#cantidadboletos').focus();
                                });
                            }
                            
                        }
                    });
                    
                });
                
                $('#accesopreventa').click(function (e){                        
                    var width = $(window).width();
                    if(width<=360){
                        widthjc="99%";
                    }else if(width>360 && width<=750){
                        widthjc="70%";
                    }
                    
                    var consultajc = $.confirm({
                        title: '<b>Acceso a Evento Preventa</b>',
                        content: '<?php echo trim($formaccesoprev); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {     
                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                        if(!valida("#FormAccesoPreventa")){
                                            $('#precargadiv').fadeOut('fast');
                                            return false;
                                        }else{
                                            var validacultus=0;
                                            var infoData = new FormData();
                                            infoData.append('obtener_info',"single");
                                            infoData.append('campos','count(*) as validcultus');
                                            infoData.append('tabla','wp_usermeta');
                                            infoData.append('where',"meta_key='cp_id_culturapass' and meta_value='"+$('#idcpacces_prev').val()+"'");
                                            //Vaciado de la información actual
                                            $.each(obtener_info(infoData,true), function(key, value){ 
                                                validacultus=this.validcultus;                                            
                                            });

                                            if(validacultus==0){
                                                $('#precargadiv').fadeOut('fast');
                                                $.dialog({
                                                    title: 'Atención!',
                                                    content: 'La tarjeta no se encuentra activada!',
                                                    icon: 'fa fa-exclamation-circle',
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {
                                                        }
                                                    },onContentReady:function(){                                                        
                                                    }
                                                });

                                                return false;
                                            }else{
                                                var nboletosusar=0;
                                                                                                
                                                $('input.prevboleto').each(function(){
                                                    if($(this).val()!==""){
                                                        var nb=parseFloat($(this).val());
                                                        nboletosusar+=nb;                            
                                                    }
                                                });
                                                
                                                if(nboletosusar>0){
                                                    var infoDatac = new FormData();
                                                    infoDatac.append('getnameuswp',"true");
                                                    infoDatac.append('idculturapass',$('#idcpacces_prev').val());
                                                    var nombreusuario = obtener_info(infoDatac,false);
                                            
                                                    var textboletos="boleto";
                                                    if(nboletosusar>1){
                                                        textboletos="boletos";
                                                    }

                                                    $.confirm({
                                                        title: '<b>Confirmar Transacción</b>',
                                                        icon: 'fa fa-warning',
                                                        type: 'red',
                                                        content: 'Propietario: <b>'+nombreusuario+'</b><br><span style="font-size:18px;">¿Confirmar el uso de <b style="color:red">'+nboletosusar
                                                                +' '+textboletos+' </b> de la tarjeta <b style="color:red">No. '+$('#idcpacces_prev').val()+'</b>?</span>',
                                                        typeAnimated: true,
                                                        backgroundDismissAnimation: 'glow',
                                                        boxWidth: widthjc,
                                                        useBootstrap: false,
                                                        escapeKey: 'cancel',
                                                        buttons: {
                                                            formSubmit: {
                                                                text: 'Aceptar',
                                                                btnClass: 'btn-green',
                                                                action: function () {
                                                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                                                        //formato de la información a enviar por POST con isntrucción SAVE
                                                                        var formData = new FormData();
                                                                        formData.append('acceso_preventa','true');                                                                        
                                                                        formData.append('idculturapass',$('#idcpacces_prev').val());                                         

                                                                        var estadistica="";
                                                                        var valid=true;
                                                                        $('input.prevboleto').each(function(){
                                                                            if($(this).val()!=="" && parseFloat($(this).val())>0){     
                                                                                if($(this).val()>$(this).attr('disponibles')){
                                                                                    $(this).addClass('errorstyle');
                                                                                    valid=false;
                                                                                }
                                                                                if(estadistica===""){
                                                                                    estadistica=$(this).attr('idevento')+";"+$(this).attr('fecha')+";"+$(this).attr('hora')+";"+$(this).attr('key1')+";"+$(this).attr('key2')+";"+$(this).attr('key3')+";"+$(this).attr('key4')+";"+$(this).val();
                                                                                }else{
                                                                                    estadistica+="|"+$(this).attr('idevento')+";"+$(this).attr('fecha')+";"+$(this).attr('hora')+";"+$(this).attr('key1')+";"+$(this).attr('key2')+";"+$(this).attr('key3')+";"+$(this).attr('key4')+";"+$(this).val();
                                                                                }                         
                                                                            }
                                                                        });
                                                                        
                                                                        if(!valid){
                                                                            $('#precargadiv').fadeOut('fast');
                                                                            $.dialog({
                                                                                title: 'Atención!',
                                                                                content: 'La cantidad de boletos a utilizar no puede superar los disponibles!',
                                                                                icon: 'fa fa-exclamation-circle',
                                                                                type: 'red',
                                                                                typeAnimated: true,
                                                                                backgroundDismissAnimation: 'glow',
                                                                                boxWidth: widthjc,
                                                                                useBootstrap: false,
                                                                                buttons: {
                                                                                    close: function () {
                                                                                    }
                                                                                },onContentReady:function(){                                                        
                                                                                }
                                                                            });

                                                                            return false;
                                                                        }
                                                                        
                                                                        formData.append('estadistica',estadistica);

                                                                        //obtener los input junto con sus valores para agregarlos a la petición
            //                                                                $('#FormAgrega input,#FormAgrega select').each(function(){
            //                                                                    if($(this).attr('id')!==undefined){
            //                                                                        formData.append($(this).attr('id'),$(this).val());
            //                                                                    }
            //                                                                });

                                                                        //bloquear boton para evitar multiples envios de información
        //                                                                this.$$formSubmit.prop('disabled', 'disabled');

                                                                        //procesar petición
                                                                        senddata(formData,false,false,"");  
                                                                        $('#idcpacces_prev').val("");
                                                                        $('#idcpacces_prev').change();
                                                                        $('#idcpacces_prev').focus();
                                                                        $('#precargadiv').fadeOut('fast');
//                                                                        abonojc.close();     
                                                                        nclicks=0;
                                                                    });
                                                                }
                                                            },
                                                            cancel: {
                                                                text: 'Cancelar',
                                                                btnClass: 'btn-red',
                                                                action: function () {
                                                                    nclicks=0;
                                                                //close
                                                                }
                                                            }
                                                        },
                                                        onContentReady: function () {
                                                            // bind to events
                                                            var jc = this;
                                                            this.$content.find('form').on('submit', function (e) {
                                                                // if the user submits the form by pressing enter in the field.
                                                                e.preventDefault();
                                                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                            });
                                                        }
                                                    });
                                                }else{
                                                    $('#precargadiv').fadeOut('fast');
                                                    $.confirm({
                                                        title: 'Atención!',
                                                        content: 'Indique el número de boletos que desea utilizar para ingresar!.<br>Tenga en consideración <b>Sexo</b><br><b>H</b> Hombres<br><b>M</b> Mujeres <br> y la siguiente <b>Simbología</b>!<br><i class="fa fa-child" aria-hidden="true"></i> Niños<br><i class="fas fa-walking"></i> Jovenes <br><i class="fa fa-male" aria-hidden="true"></i> Adultos <br><i class="fa fa-blind" aria-hidden="true"></i> Adultos Mayores',
                                                        icon: 'fa fa-exclamation-circle',
                                                        type: 'orange',
                                                        typeAnimated: true,
                                                        backgroundDismissAnimation: 'glow',
                                                        boxWidth: widthjc,
                                                        useBootstrap: false,
                                                        buttons: {
                                                            aceptar: {
                                                                text: 'Cerrar',
                                                                btnClass: 'btn-red',
                                                                action: function () {
                                                                                                                                    
                                                                }
                                                            }
                                                        },onContentReady:function(){                                                        
                                                        }
                                                    });

                                                    return false;
                                                }
                                                
                                                $('#precargadiv').fadeOut('fast');
                                            }
                                        }
                                    });
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            $('#idcpacces_prev').focus();
                            
//                            if(android){
//                                this.$content.find('#idculturapass').on('keyup', function (e) {
//                                    // if the user submits the form by pressing enter in the field.
//                                    e.preventDefault();
//                                    jc.$$formSubmit.trigger('change'); // reference the button and click it
//                                });
//                            }
                        }
                    });                    
                });
                
                $('#activarcultus').click(function(){                    
                    //crear Formulario de Actualización
                    var formsearch=$.confirm({
                        title: 'Buscar <b>Pre-Registro</b>',
                        content: '<?php echo trim($formbuscar); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                                                            
                            $('#loadresults').fadeOut('slow');                            
                        },
                        buttons: {   
                            formSubmit: {
                                text: 'Buscar',
                                btnClass: 'btn-green',
//                                keys: ['ctrl'],
                                action: function () {                                    
                                    if(!valida("#FormBuscar")){
                                        return false;
                                    }else{    
                                        $('#loadresults').show('fast'); 
                                        //formatear datos para enviar por POST
                                        var formData = new FormData();
                                        
                                        var nfiltros=0;
                                        $('#FormBuscar input, #FormBuscar select').each(function(){
                                           var val = $(this).val();
                                           if(val!=="" && $(this).attr('id')!==undefined){
                                               nfiltros++; 
                                           }
                                        });
                                        $('#loadresults').fadeOut('slow');
                                        if(nfiltros<1){
                                            $.dialog({
                                                title: 'Atención!',
                                                content: 'Escriba al menos 1 filtro para realizar una busqueda mas especifica.<BR>',
                                                icon: 'fa fa-exclamation-circle',
                                                type: 'red',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                buttons: {
                                                    close: function () {
                                                    }
                                                }
                                            });
                                            return false;
                                        } 
                                        
                                        var where=" tipouser like '%cultuspass%' and (cpid is null or cpid='') ";
                                        
                                        if($('#user').val()!==""){                                            
                                            where+=" and user_login like '%"+$('#user').val()+"%'";
                                        }                                        
                                        if($('#nombre').val()!==""){
                                            where+=" and fname like '%"+$('#nombre').val()+"%'";
                                        }
                                        if($('#email').val()!==""){
                                            where+=" and user_email like '%"+$('#email').val()+"%'";
                                        }
                                                                                
                                        var domtab="";
                                        
                                        var infoData = new FormData();
                                        infoData.append('obtener_info',"multiple");
                                        infoData.append('campos','*');
                                        infoData.append('tabla',"( "+
                                            "SELECT *, (select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='first_name' ) as fname,  "+
                                            "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='last_name' ) as lname, "+
                                            "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='cp_id_culturapass' ) as cpid, "+
                                            "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='wp_capabilities' ) as tipouser "+
                                            "from wp_users a  "+
                                            ") as users" );
                                        infoData.append('where',where+" ");
                                        //Vaciado de la información actual
                                        jQuery.each(obtener_info(infoData,true), function(){ 
                                            var infodataeval=eval(this);                                    
                                            jQuery.each(infodataeval, function(key, value){
                                                
                                                domtab+='<tr class="tractivar" idusuario="'+this.ID+'" >';
                                                domtab+='<td>'+this.user_login+'</td>';
                                                domtab+='<td>'+this.fname+'</td>';
                                                domtab+='<td>'+this.lname+'</td>';
                                                domtab+='<td>'+this.user_email+'</td>';
                                                domtab+='</tr>';
                                            });
                                        });
                                        
                                         $('#bodyresults').html(domtab);  
                                               
                                         $('#loadresults').fadeOut('slow');
                                        return false;
                                        
                                        
                                        //bloquear boton para evitar multiples envios de información
                                        //this.$$formSubmit.prop('disabled', 'disabled');
                                                                                
                                        //Procesar solicitud Update
                                        //senddata(formData,true);                                                 
                                    }                                                         
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        }
                    });  
                });
                
                $('body').on('click','tr.tractivar',function(){
                    var user=$(this).attr('idusuario');
                    
                    var nombre="";
                    var correo="";
                    var usuario="";
                    
                    var infoData = new FormData();
                    infoData.append('obtener_info',"single");
                    infoData.append('campos','*');
                    infoData.append('tabla',"( "+
                        "SELECT *, (select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='first_name' ) as fname,  "+
                        "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='last_name' ) as lname, "+
                        "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='cp_id_culturapass' ) as cpid, "+
                        "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='wp_capabilities' ) as tipouser "+
                        "from wp_users a  "+
                        ") as users");
                    infoData.append('where',"ID='"+user+"'");
                    //Vaciado de la información actual
                    $.each(obtener_info(infoData,true), function(key, value){ 
                        nombre=this.fname+" "+this.lname;                               
                        correo=this.user_email;                               
                        usuario=this.user_login;                               
                    });
                    
                    var consultajc = $.confirm({
                        title: '<b>Activar CulturaPass</b>',
                        content: '<?php echo trim($formactiva); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: widthjc,
                        useBootstrap: false,
                        escapeKey: 'cancel',                        
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            $('#detalles_activar').html('Usuario: <b>'+usuario+'</b><br>Propietario: <b>'+nombre+'</b><br>Email: <b>'+correo+'</b><br><br>');
                            $('#idculturapass').focus();
                        },
                        buttons: {
                            formSubmit: {
                                text: 'Aceptar',
                                btnClass: 'btn-green',
                                action: function () {     
                                    $('#detalles_saldos').html(''); 
                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                        $('#detalles_saldos').html('');
                                        if(!valida("#FormActivar")){
                                            $('#precargadiv').fadeOut('fast');
                                            return false;
                                        }else{
                                            var validacultus=0;
                                            var infoData = new FormData();
                                            infoData.append('obtener_info',"single");
                                            infoData.append('campos','count(*) as validcultus');
                                            infoData.append('tabla','wp_usermeta');
                                            infoData.append('where',"meta_key='cp_id_culturapass' and meta_value='"+$('#idculturapass').val()+"'");
                                            //Vaciado de la información actual
                                            $.each(obtener_info(infoData,true), function(key, value){ 
                                                validacultus=this.validcultus;                                            
                                            });

                                            if(validacultus>0){
                                                $('#precargadiv').fadeOut('fast');
                                                $.dialog({
                                                    title: 'Atención!',
                                                    content: 'La tarjeta se encuentra activada! <br>Favor de verificar el número de la tarjeta.',
                                                    icon: 'fa fa-exclamation-circle',
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {
                                                        }
                                                    },onContentReady:function(){                                                        
                                                    }
                                                });

                                                return false;
                                            }else{
                                                $('#precargadiv').fadeOut('fast');
                                                $.confirm({
                                                    title: '<b>Confirmar Activación de CulturaPass</b>',
                                                    icon: 'fa fa-warning',
                                                    type: 'red',
                                                    content: 'Nuevo Propietario: <b>'+nombre+'</b><br><span style="font-size:18px;">¿Deseas activar la tarjeta No. <b style="color:red">'+$('#idculturapass').val()+'</b>?',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    escapeKey: 'cancel',
                                                    buttons: {
                                                        formSubmit: {
                                                            text: 'Aceptar',
                                                            btnClass: 'btn-green',
                                                            action: function () {
                                                                if(!valida("#FormAgrega")){
                                                                    return false;
                                                                }else{   
                                                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                                                        //formato de la información a enviar por POST con isntrucción SAVE
                                                                        var formData = new FormData();
                                                                        formData.append('activar_cultus','true');
                                                                        formData.append('idculturapass',$('#idculturapass').val());
                                                                        formData.append('idusuario',user);
                                                                        senddata_local(formData,true,false,"");  

                                                                        $('#precargadiv').fadeOut('fast');
                                                                    });
                                                                }                                                         
                                                            }
                                                        },
                                                        cancel: {
                                                            text: 'Cancelar',
                                                            btnClass: 'btn-red',
                                                            action: function () {
                                                            //close
                                                            }
                                                        }
                                                    },
                                                    onContentReady: function () {
                                                        // bind to events
                                                        var jc = this;
                                                        this.$content.find('form').on('submit', function (e) {
                                                            // if the user submits the form by pressing enter in the field.
                                                            e.preventDefault();
                                                            jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                        });
                                                    }
                                                }); 
                                            }
                                        }
                                    });
                                    return false;
                                }
                            },
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        }
                    });
                });
                
                $('#preregsitrorep').click(function(){                    
                    //crear Formulario de Actualización
                    var nregistros=0;
                    var formsearch=$.confirm({
                        title: 'Usuarios <b>Pre-Registro</b>',
                        content: '<?php echo trim($formreporte); ?>',
                        typeAnimated: true,
                        backgroundDismissAnimation: 'glow',
                        boxWidth: "800px",
                        useBootstrap: false,
                        escapeKey: 'cancel',
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                            });
                            
                            var domtab="";
                                        
                            var infoData = new FormData();
                            infoData.append('obtener_info',"multiple");
                            infoData.append('campos','*, DATE_ADD(user_registered, INTERVAL -5 HOUR) as user_registered_real');
                            infoData.append('tabla',"( "+
                                "SELECT *, (select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='first_name' ) as fname,  "+
                                "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='last_name' ) as lname, "+
                                "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='cp_id_culturapass' ) as cpid, "+
                                "(select meta_value from wp_usermeta b where a.ID=b.user_id and b.meta_key='wp_capabilities' ) as tipouser "+
                                "from wp_users a  "+
                                ") as users" );
                            infoData.append('where'," user_registered>'2018-10-11' ");
                            //Vaciado de la información actual
                            jQuery.each(obtener_info(infoData,true), function(){ 
                                var infodataeval=eval(this);                                    
                                jQuery.each(infodataeval, function(key, value){
                                    nregistros++;
                                    domtab+='<tr class="trnreg" idusuario="'+this.ID+'" >';
                                    domtab+='<td>'+this.user_login+'</td>';
                                    domtab+='<td>'+this.fname+'</td>';
                                    domtab+='<td>'+this.lname+'</td>';
                                    domtab+='<td>'+this.user_email+'</td>';
                                    domtab+='<td>'+this.user_registered_real+'</td>';
                                    domtab+='</tr>';
                                });
                            });

                            $('#bodyresults').html(domtab);  
                            $('#detalles_repo').html('<b>Registros: '+nregistros+'</b>');
                            
                            $('#loadresults').fadeOut('slow');                            
                        },
                        buttons: {
                            cancel: {
                                text: 'Cancelar',
                                btnClass: 'btn-red',
                                action: function () {
                                //close
                                }
                            }
                        }
                    });  
                });
                
                /********************Controlar cambios de change de un select**********************/
                $('body').on('change','select',function(){
                    var id=$(this).attr('id');
                    var value=$(this).val();
                    
                    switch(id){
                        case 'idevento':
                            $('#idculturapassevent').removeClass('errorstyle');
                            $('#idculturapassevent').removeAttr('placeholder');
                            $('#cantidadboletos').removeClass('errorstyle');
                            $('#cantidadboletos').removeAttr('placeholder');
                            $('#idculturapassevent').val("").change();
                            $('#descuentoevent').val("0");
                            $('#puntosevent').val("5");
                            $('#puntosevent').removeAttr("disabled");
                            $('#descuentoevent').removeAttr("disabled");
                            
                            if(value!==""){
                                $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                    var tipoevento="";
                                    var costo="";
                                    var costolabel="";
                                    var infoData = new FormData();
                                    infoData.append('obtener_info',"single");
                                    infoData.append('campos','meta_value');
                                    infoData.append('tabla','wp_postmeta');
                                    infoData.append('where',"meta_key='tipo_entrada' and post_id='"+value+"'");
                                    //Vaciado de la información actual
                                    $.each(obtener_info(infoData,true), function(key, value){ 
                                        tipoevento=this.meta_value;
                                    });

                                    if(tipoevento==="Cuota de recuperación" || tipoevento==="Inscripción"){                                
                                        var infoData = new FormData();
                                        infoData.append('obtener_info',"single");
                                        infoData.append('campos','meta_value');
                                        infoData.append('tabla','wp_postmeta');
                                        infoData.append('where',"meta_key='costo_entrada' and post_id='"+value+"'");
                                        //Vaciado de la información actual
                                        $.each(obtener_info(infoData,true), function(key, value){ 
                                            costo=parseFloat(this.meta_value);
                                            costolabel='<div class="control-group gutters"> <div class="all-40"><b>Costo:</b> </div><div class="all-60"><b>$'+parseFloat(costo).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</b></div></div>';
                                        });                                        
                                        $("#divdescuentoevent").show('fast');
                                        $("#divpuntosevent").hide('fast').val("5");
                                    }else if(tipoevento==="Gratuito" || tipoevento==="Boleto de cortesía"){
                                        costo=0;
                                        costolabel='<div class="control-group gutters"> <div class="all-40"><b>Costo:</b> </div><div class="all-60"><b>Gratuito</b></div></div>';
                                        //ostolabel="Costo: Gratuito";
                                        $("#divdescuentoevent").hide('fast').val("0");
                                        $("#divpuntosevent").show('fast').val("5");
                                    }

                                    $('#formdetacceso').show('slow');
                                    $('#costoeventolabel').html(costolabel);
                                    $('#costoeventolabel').attr("costo",costo);
                                    if(tipoevento==="Cuota de recuperación" || tipoevento==="Inscripción"){
                                        $('#cantidadboletos').attr("disabled","disabled");
                                    }else{
                                        $('#cantidadboletos').attr("disabled","disabled");
                                    }
                                    $('#idculturapassevent').focus();
                                    $('#precargadiv').fadeOut('fast');
                                });
                            }else{
                                
                                $('#costoeventolabel').html('');
                                $('#costoeventolabel').attr("costo",0);
                                $('#formdetacceso').hide('slow');
                            }
                            
                            break;    
                        case 'cp_p_origen':
                            $('#div-cp_dom_nac').html('');
			
                            if(value==="Otro"){
                                $('#div-cp_dom_nac').html('<input  class="required" type="text" name="cp_dom_nac" id="cp_dom_nac" value="">');
                                $('#div-cp_edo_nac').html('<input  class="required" type="text" name="cp_edo_nac" id="cp_edo_nac" value="">');
                            }else{
                                $('#div-cp_edo_nac').html('<select name="cp_edo_nac" id="cp_edo_nac" value="" class="required all-100" ><option value="">Seleccione...</option><option value="Aguascalientes">Aguascalientes</option><option value="Baja California">Baja California</option><option value="Baja California Sur">Baja California Sur</option><option value="Campeche">Campeche</option><option value="Coahuila de Zaragoza">Coahuila de Zaragoza</option><option value="Colima">Colima</option><option value="Chiapas">Chiapas</option><option value="Chihuahua">Chihuahua</option><option value="Ciudad de México">Ciudad de México</option><option value="Durango">Durango</option><option value="Guanajuato">Guanajuato</option><option value="Guerrero">Guerrero</option><option value="Hidalgo" selected="selected">Hidalgo</option><option value="Jalisco">Jalisco</option><option value="México">México</option><option value="Michoacán de Ocampo">Michoacán de Ocampo</option><option value="Morelos">Morelos</option><option value="Nayarit">Nayarit</option><option value="Nuevo León">Nuevo León</option><option value="Oaxaca">Oaxaca</option><option value="Puebla">Puebla</option><option value="Querétaro de Arteaga">Querétaro de Arteaga</option><option value="Quintana Roo">Quintana Roo</option><option value="San Luis Potosí">San Luis Potosí<option><option value="Sinaloa">Sinaloa</option><option value="Sonora">Sonora</option><option value="Tabasco">Tabasco</option><option value="Tamaulipas">Tamaulipas</option><option value="Tlaxcala">Tlaxcala</option><option value="Veracruz de Ignacio Llave">Veracruz de Ignacio Llave</option><option value="Yucatán">Yucatán</option><option value="Zacatecas">Zacatecas</option></select>');
                                $('#div-cp_dom_nac').html('<select class="required all-100"  name="cp_dom_nac" id="cp_dom_nac" value=""><option value="">Seleccione...</option><option value="Acatlán">Acatlán</option><option value="Acaxochitlán">Acaxochitlán</option><option value="Actopan">Actopan</option><option value="Agua Blanca de Iturbide">Agua Blanca de Iturbide</option><option value="Ajacuba">Ajacuba</option><option value="Alfajayucan">Alfajayucan</option><option value="Almoloya">Almoloya</option><option value="Apan">Apan</option><option value="El Arenal">El Arenal</option><option value="Atitalaquia">Atitalaquia</option><option value="Atlapexco">Atlapexco</option><option value="Atotonilco el Grande">Atotonilco el Grande</option><option value="Atotonilco de Tula">Atotonilco de Tula</option><option value="Calnali">Calnali</option><option value="Cardonal">Cardonal</option><option value="Cuautepec de Hinojosa">Cuautepec de Hinojosa</option><option value="Chapantongo">Chapantongo</option><option value="Chapulhuacán">Chapulhuacán</option><option value="Chilcuautla">Chilcuautla</option><option value="Eloxochitlán">Eloxochitlán</option><option value="Emiliano Zapata">Emiliano Zapata</option><option value="Epazoyucan">Epazoyucan</option><option value="Francisco I. Madero">Francisco I. Madero</option><option value="Huasca de Ocampo">Huasca de Ocampo</option><option value="Huautla">Huautla</option><option value="Huazalingo">Huazalingo</option><option value="Huehuetla">Huehuetla</option><option value="Huejutla de Reyes">Huejutla de Reyes</option><option value="Huichapan">Huichapan</option><option value="Ixmiquilpan">Ixmiquilpan</option><option value="Jacala de Ledezma">Jacala de Ledezma</option><option value="Jaltocán">Jaltocán</option><option value="Juárez Hidalgo">Juárez Hidalgo</option><option value="Lolotla">Lolotla</option><option value="Metepec">Metepec</option><option value="San Agustín Metzquititlán">San Agustín Metzquititlán</option><option value="Metztitlán">Metztitlán</option><option value="Mineral del Chico">Mineral del Chico</option><option value="Mineral del Monte">Mineral del Monte</option><option value="La Misión">La Misión</option><option value="Mixquiahuala de Juárez">Mixquiahuala de Juárez</option><option value="Molango de Escamilla">Molango de Escamilla</option><option value="Nicolás Flores">Nicolás Flores</option><option value="Nopala de Villagrán">Nopala de Villagrán</option><option value="Omitlán de Juárez">Omitlán de Juárez</option><option value="San Felipe Orizatlán">San Felipe Orizatlán</option><option value="Pacula">Pacula</option><option value="Pachuca de Soto">Pachuca de Soto</option><option value="Pisaflores">Pisaflores</option><option value="Progreso de Obregón">Progreso de Obregón</option><option value="Mineral de la Reforma">Mineral de la Reforma</option><option value="San Agustín Tlaxiaca">San Agustín Tlaxiaca</option><option value="San Bartolo Tutotepec">San Bartolo Tutotepec</option><option value="San Salvador">San Salvador</option><option value="Santiago de Anaya">Santiago de Anaya</option><option value="Santiago Tulantepec de Lugo Guerrero">Santiago Tulantepec de Lugo Guerrero</option><option value="Singuilucan">Singuilucan</option><option value="Tasquillo">Tasquillo</option><option value="Tecozautla">Tecozautla</option><option value="Tenango de Doria">Tenango de Doria</option><option value="Tepeapulco">Tepeapulco</option><option value="Tepehuacán de Guerrero">Tepehuacán de Guerrero</option><option value="Tepeji del Río de Ocampo">Tepeji del Río de Ocampo</option><option value="Tepetitlán">Tepetitlán</option><option value="Tetepango">Tetepango</option><option value="Villa de Tezontepec">Villa de Tezontepec</option><option value="Tezontepec de Aldama">Tezontepec de Aldama</option><option value="Tianguistengo">Tianguistengo</option><option value="Tizayuca">Tizayuca</option><option value="Tlahuelilpan">Tlahuelilpan</option><option value="Tlahuiltepa">Tlahuiltepa</option><option value="Tlanalapa">Tlanalapa</option><option value="Tlanchinol">Tlanchinol</option><option value="Tlaxcoapan">Tlaxcoapan</option><option value="Tolcayuca">Tolcayuca</option><option value="Tula de Allende">Tula de Allende</option><option value="Tulancingo de Bravo">Tulancingo de Bravo</option><option value="Xochiatipan">Xochiatipan</option><option value="Xochicoatlán">Xochicoatlán</option><option value="Yahualica">Yahualica</option><option value="Zacualtipán de Ángeles">Zacualtipán de Ángeles</option><option value="Zapotlán de Juárez">Zapotlán de Juárez</option><option value="Zempoala">Zempoala</option><option value="Zimapán">Zimapán</option></select>');
                                $('#cp_edo_nac').change(function (){
                                    var estado=$(this).val();
                                    $('#div-cp_dom_nac').html('');

                                    if(estado==="Hidalgo"){
                                        $('#div-cp_dom_nac').html('<select class="required all-100"  name="cp_dom_nac" id="cp_dom_nac" value=""><option value="">Seleccione...</option><option value="Acatlán">Acatlán</option><option value="Acaxochitlán">Acaxochitlán</option><option value="Actopan">Actopan</option><option value="Agua Blanca de Iturbide">Agua Blanca de Iturbide</option><option value="Ajacuba">Ajacuba</option><option value="Alfajayucan">Alfajayucan</option><option value="Almoloya">Almoloya</option><option value="Apan">Apan</option><option value="El Arenal">El Arenal</option><option value="Atitalaquia">Atitalaquia</option><option value="Atlapexco">Atlapexco</option><option value="Atotonilco el Grande">Atotonilco el Grande</option><option value="Atotonilco de Tula">Atotonilco de Tula</option><option value="Calnali">Calnali</option><option value="Cardonal">Cardonal</option><option value="Cuautepec de Hinojosa">Cuautepec de Hinojosa</option><option value="Chapantongo">Chapantongo</option><option value="Chapulhuacán">Chapulhuacán</option><option value="Chilcuautla">Chilcuautla</option><option value="Eloxochitlán">Eloxochitlán</option><option value="Emiliano Zapata">Emiliano Zapata</option><option value="Epazoyucan">Epazoyucan</option><option value="Francisco I. Madero">Francisco I. Madero</option><option value="Huasca de Ocampo">Huasca de Ocampo</option><option value="Huautla">Huautla</option><option value="Huazalingo">Huazalingo</option><option value="Huehuetla">Huehuetla</option><option value="Huejutla de Reyes">Huejutla de Reyes</option><option value="Huichapan">Huichapan</option><option value="Ixmiquilpan">Ixmiquilpan</option><option value="Jacala de Ledezma">Jacala de Ledezma</option><option value="Jaltocán">Jaltocán</option><option value="Juárez Hidalgo">Juárez Hidalgo</option><option value="Lolotla">Lolotla</option><option value="Metepec">Metepec</option><option value="San Agustín Metzquititlán">San Agustín Metzquititlán</option><option value="Metztitlán">Metztitlán</option><option value="Mineral del Chico">Mineral del Chico</option><option value="Mineral del Monte">Mineral del Monte</option><option value="La Misión">La Misión</option><option value="Mixquiahuala de Juárez">Mixquiahuala de Juárez</option><option value="Molango de Escamilla">Molango de Escamilla</option><option value="Nicolás Flores">Nicolás Flores</option><option value="Nopala de Villagrán">Nopala de Villagrán</option><option value="Omitlán de Juárez">Omitlán de Juárez</option><option value="San Felipe Orizatlán">San Felipe Orizatlán</option><option value="Pacula">Pacula</option><option value="Pachuca de Soto">Pachuca de Soto</option><option value="Pisaflores">Pisaflores</option><option value="Progreso de Obregón">Progreso de Obregón</option><option value="Mineral de la Reforma">Mineral de la Reforma</option><option value="San Agustín Tlaxiaca">San Agustín Tlaxiaca</option><option value="San Bartolo Tutotepec">San Bartolo Tutotepec</option><option value="San Salvador">San Salvador</option><option value="Santiago de Anaya">Santiago de Anaya</option><option value="Santiago Tulantepec de Lugo Guerrero">Santiago Tulantepec de Lugo Guerrero</option><option value="Singuilucan">Singuilucan</option><option value="Tasquillo">Tasquillo</option><option value="Tecozautla">Tecozautla</option><option value="Tenango de Doria">Tenango de Doria</option><option value="Tepeapulco">Tepeapulco</option><option value="Tepehuacán de Guerrero">Tepehuacán de Guerrero</option><option value="Tepeji del Río de Ocampo">Tepeji del Río de Ocampo</option><option value="Tepetitlán">Tepetitlán</option><option value="Tetepango">Tetepango</option><option value="Villa de Tezontepec">Villa de Tezontepec</option><option value="Tezontepec de Aldama">Tezontepec de Aldama</option><option value="Tianguistengo">Tianguistengo</option><option value="Tizayuca">Tizayuca</option><option value="Tlahuelilpan">Tlahuelilpan</option><option value="Tlahuiltepa">Tlahuiltepa</option><option value="Tlanalapa">Tlanalapa</option><option value="Tlanchinol">Tlanchinol</option><option value="Tlaxcoapan">Tlaxcoapan</option><option value="Tolcayuca">Tolcayuca</option><option value="Tula de Allende">Tula de Allende</option><option value="Tulancingo de Bravo">Tulancingo de Bravo</option><option value="Xochiatipan">Xochiatipan</option><option value="Xochicoatlán">Xochicoatlán</option><option value="Yahualica">Yahualica</option><option value="Zacualtipán de Ángeles">Zacualtipán de Ángeles</option><option value="Zapotlán de Juárez">Zapotlán de Juárez</option><option value="Zempoala">Zempoala</option><option value="Zimapán">Zimapán</option></select>');
                                    }else{
                                        //en caso contrario pedir el municipio escrito en campo text
                                        $('#div-cp_dom_nac').html('<input  class="required" type="text" name="cp_dom_nac" id="cp_dom_nac" value="">');
                                    }
                                });
                            }
                            
                            break;                            
                        case 'cp_edo_nac':
                            var estado=$(this).val();
                            $('#div-cp_dom_nac').html('');

                            if(estado==="Hidalgo"){
                                $('#div-cp_dom_nac').html('<select class="required all-100"  name="cp_dom_nac" id="cp_dom_nac" value=""><option value="">Seleccione...</option><option value="Acatlán">Acatlán</option><option value="Acaxochitlán">Acaxochitlán</option><option value="Actopan">Actopan</option><option value="Agua Blanca de Iturbide">Agua Blanca de Iturbide</option><option value="Ajacuba">Ajacuba</option><option value="Alfajayucan">Alfajayucan</option><option value="Almoloya">Almoloya</option><option value="Apan">Apan</option><option value="El Arenal">El Arenal</option><option value="Atitalaquia">Atitalaquia</option><option value="Atlapexco">Atlapexco</option><option value="Atotonilco el Grande">Atotonilco el Grande</option><option value="Atotonilco de Tula">Atotonilco de Tula</option><option value="Calnali">Calnali</option><option value="Cardonal">Cardonal</option><option value="Cuautepec de Hinojosa">Cuautepec de Hinojosa</option><option value="Chapantongo">Chapantongo</option><option value="Chapulhuacán">Chapulhuacán</option><option value="Chilcuautla">Chilcuautla</option><option value="Eloxochitlán">Eloxochitlán</option><option value="Emiliano Zapata">Emiliano Zapata</option><option value="Epazoyucan">Epazoyucan</option><option value="Francisco I. Madero">Francisco I. Madero</option><option value="Huasca de Ocampo">Huasca de Ocampo</option><option value="Huautla">Huautla</option><option value="Huazalingo">Huazalingo</option><option value="Huehuetla">Huehuetla</option><option value="Huejutla de Reyes">Huejutla de Reyes</option><option value="Huichapan">Huichapan</option><option value="Ixmiquilpan">Ixmiquilpan</option><option value="Jacala de Ledezma">Jacala de Ledezma</option><option value="Jaltocán">Jaltocán</option><option value="Juárez Hidalgo">Juárez Hidalgo</option><option value="Lolotla">Lolotla</option><option value="Metepec">Metepec</option><option value="San Agustín Metzquititlán">San Agustín Metzquititlán</option><option value="Metztitlán">Metztitlán</option><option value="Mineral del Chico">Mineral del Chico</option><option value="Mineral del Monte">Mineral del Monte</option><option value="La Misión">La Misión</option><option value="Mixquiahuala de Juárez">Mixquiahuala de Juárez</option><option value="Molango de Escamilla">Molango de Escamilla</option><option value="Nicolás Flores">Nicolás Flores</option><option value="Nopala de Villagrán">Nopala de Villagrán</option><option value="Omitlán de Juárez">Omitlán de Juárez</option><option value="San Felipe Orizatlán">San Felipe Orizatlán</option><option value="Pacula">Pacula</option><option value="Pachuca de Soto">Pachuca de Soto</option><option value="Pisaflores">Pisaflores</option><option value="Progreso de Obregón">Progreso de Obregón</option><option value="Mineral de la Reforma">Mineral de la Reforma</option><option value="San Agustín Tlaxiaca">San Agustín Tlaxiaca</option><option value="San Bartolo Tutotepec">San Bartolo Tutotepec</option><option value="San Salvador">San Salvador</option><option value="Santiago de Anaya">Santiago de Anaya</option><option value="Santiago Tulantepec de Lugo Guerrero">Santiago Tulantepec de Lugo Guerrero</option><option value="Singuilucan">Singuilucan</option><option value="Tasquillo">Tasquillo</option><option value="Tecozautla">Tecozautla</option><option value="Tenango de Doria">Tenango de Doria</option><option value="Tepeapulco">Tepeapulco</option><option value="Tepehuacán de Guerrero">Tepehuacán de Guerrero</option><option value="Tepeji del Río de Ocampo">Tepeji del Río de Ocampo</option><option value="Tepetitlán">Tepetitlán</option><option value="Tetepango">Tetepango</option><option value="Villa de Tezontepec">Villa de Tezontepec</option><option value="Tezontepec de Aldama">Tezontepec de Aldama</option><option value="Tianguistengo">Tianguistengo</option><option value="Tizayuca">Tizayuca</option><option value="Tlahuelilpan">Tlahuelilpan</option><option value="Tlahuiltepa">Tlahuiltepa</option><option value="Tlanalapa">Tlanalapa</option><option value="Tlanchinol">Tlanchinol</option><option value="Tlaxcoapan">Tlaxcoapan</option><option value="Tolcayuca">Tolcayuca</option><option value="Tula de Allende">Tula de Allende</option><option value="Tulancingo de Bravo">Tulancingo de Bravo</option><option value="Xochiatipan">Xochiatipan</option><option value="Xochicoatlán">Xochicoatlán</option><option value="Yahualica">Yahualica</option><option value="Zacualtipán de Ángeles">Zacualtipán de Ángeles</option><option value="Zapotlán de Juárez">Zapotlán de Juárez</option><option value="Zempoala">Zempoala</option><option value="Zimapán">Zimapán</option></select>');
                            }else{
                                //en caso contrario pedir el municipio escrito en campo text
                                $('#div-cp_dom_nac').html('<input  class="required" type="text" name="cp_dom_nac" id="cp_dom_nac" value="">');
                            }
                            
                            break;                            
                    }
                });
                
                $('body').on('change','input',function(){
                    var id=$(this).attr('id');
                    var value=$(this).val();
                    
                    switch(id){
                        case 'idculturapassevent':      
                            var descuento=$('#descuentoevent').val();  
                            descuento=descuento/100;
                            
                            if(value!==""){
                                $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                    var validacultus=0;
                                    var infoData = new FormData();
                                    infoData.append('obtener_info',"single");
                                    infoData.append('campos','count(*) as validcultus');
                                    infoData.append('tabla','wp_usermeta');
                                    infoData.append('where',"meta_key='cp_id_culturapass' and meta_value='"+value+"'");
                                    //Vaciado de la información actual
                                    $.each(obtener_info(infoData,true), function(key, value){ 
                                        validacultus=this.validcultus;
                                    });

                                    if(validacultus==0){
                                        $('#precargadiv').fadeOut('fast');
                                        $.dialog({
                                            title: 'Atención!',
                                            content: 'La tarjeta no se encuentra activada!',
                                            icon: 'fa fa-exclamation-circle',
                                            type: 'red',
                                            typeAnimated: true,
                                            backgroundDismissAnimation: 'glow',
                                            boxWidth: widthjc,
                                            useBootstrap: false,
                                            buttons: {
                                                close: function () {
                                                }
                                            }
                                        });
                                        $('#idculturapassevent').val("").change();
                                        return false;
                                    }

                                    //validar si la tarjeta tiene movimientos corruptos
                                    var infoDatac = new FormData();
                                    infoDatac.append('validcp',"true");
                                    infoDatac.append('idculturapass',value);
                                    //Vaciado de la información actual
                                    if(!obtener_info(infoDatac,false)){
                                        $.dialog({
                                            title: 'Atención!',
                                            content: '<b style="color:red;" class="push-center"><i class="fa fa-warning"></i> Movimientos Corruptos!<br>La tarjeta se desactivará y se comenzará investigación.</b>',
                                            icon: 'fa fa-exclamation-circle',
                                            type: 'red',
                                            typeAnimated: true,
                                            backgroundDismissAnimation: 'glow',
                                            boxWidth: widthjc,
                                            useBootstrap: false,
                                            buttons: {
                                                close: function () {                                                   
                                                }
                                            }
                                        });
                                        $('#precargadiv').fadeOut('fast');
                                        $('#idculturapassevent').val("").change();
                                        $('#idculturapassevent').focus();
                                        return false;
                                    }
                                                                        
                                    //verificacion si ya acceso al evento seleccionado para ver si se otorgan o no puntos 
                                    var validacultusaccess=0;
                                    var infoDatace = new FormData();
                                    infoDatace.append('obtener_info_tc',"single");
                                    infoDatace.append('campos','count(*) as validcultus');
                                    infoDatace.append('tabla','boletos join movimientos using(idmovimiento,idtipomovimiento)');
                                    infoDatace.append('where',"idculturapass='"+value+"' and idevento='"+$('#idevento').val()+"' and fecha='"
                                        +$('#idevento option:selected').attr('attradd2')+"' and hora='"+$('#idevento option:selected').attr('attradd')+"'; ");
                                    //Vaciado de la información actual
                                    $.each(obtener_info(infoDatace,true), function(key, value){ 
                                        validacultusaccess=this.validcultus;
                                    });
                                    
                                    if(validacultusaccess>0){
                                        $('#puntosevent').val(0);
                                        $('#puntosevent').attr('disabled','disabled');
                                    }

                                    //consulta del saldo
                                    var infoDatac = new FormData();
                                    infoDatac.append('consultanoprint','true');
                                    infoDatac.append('idculturapass',$('#idculturapassevent').val());
                                    var saldos=obtener_info(infoDatac,false);

                                    $('#divsaldocpevento').html(saldos+"<br>");

                                    var costoevento=parseFloat($('#costoeventolabel').attr("costo"));

                                    if(costoevento==0){
                                        $('#cantidadboletos').removeAttr('disabled');
                                        $('#diveventomax').html("Boletos para compra: <b>---<b><br>");
                                        $('#precargadiv').fadeOut('fast');
                                    }else{
                                        $('#descuentoevent').attr('disabled',true);
                                        //recalcular para aplicar descuento
                                        costoevento=costoevento-(costoevento*descuento);
                                        var saldo=parseFloat($('#saldocp_calc1').attr('saldo'));

                                        if(saldo>=costoevento){
                                            $('#cantidadboletos').removeAttr('disabled');
                                            var maxboletosn=0;
                                            if(costoevento>0){
                                                maxboletosn=Math.trunc(saldo/costoevento);
                                            }else{
                                                maxboletosn="---";
                                            }
                                            $('#diveventomax').html("Boletos para compra: <b>"+maxboletosn+"<b><br>");
                                        }else{
                                            $('#cantidadboletos').attr('disabled','disabled');
                                            $('#cantidadboletos').val("").change();
                                            $('#diveventomax').html("Boletos para compra: <b>0<b><br>");
                                            $.dialog({
                                                title: 'Atención!',
                                                content: 'La tarjeta no cuenta con saldo suficiente!',
                                                icon: 'fa fa-exclamation-circle',
                                                type: 'red',
                                                typeAnimated: true,
                                                backgroundDismissAnimation: 'glow',
                                                boxWidth: widthjc,
                                                useBootstrap: false,
                                                buttons: {
                                                    close: function () {
                                                    }
                                                }
                                            });
                                        }
                                        $('#precargadiv').fadeOut('fast');
                                    }
                                });
                            }else{
                                $('#precargadiv').fadeOut('fast');
                                $('#divsaldocpevento').html("");
                                $('#diveventomax').html("");
                                $('#cantidadboletos').val("").change().attr('disabled','disabled').focus();
                            }
                            break;
                        case 'cantidadboletos':
                            var descuento=$('#descuentoevent').val();  
                            descuento=descuento/100;
                            if(value!==""){
                                if(value>0){
                                    $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                        var costo=parseFloat($('#costoeventolabel').attr('costo'));
                                        var saldo=parseFloat($('#saldocp_calc1').attr('saldo'));
        //                                var maxboletosn=parseFloat(Math.trunc(saldo/costoevento));
                                        var nboletosc=parseFloat(value);
                                        costo=costo-(costo*descuento);
                                        if(costo==0){
                                            $('#formdatacceso').show('slow');
                                            $('#formdatacceso input').each(function(){
                                                $(this).val("");
                                                $('#precargadiv').fadeOut('fast');
                                            });
                                        }else{
                                            if((nboletosc*costo)>saldo){
                                                $.dialog({
                                                    title: 'Atención!',
                                                    content: 'La tarjeta no cuenta con saldo suficiente para comprar '+nboletosc+' boletos!',
                                                    icon: 'fa fa-exclamation-circle',
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    backgroundDismissAnimation: 'glow',
                                                    boxWidth: widthjc,
                                                    useBootstrap: false,
                                                    buttons: {
                                                        close: function () {                                                    
                                                        }
                                                    }
                                                });
                                                $("#cantidadboletos").val("").change();
                                            }else{
                                                $('#formdatacceso').show('slow');
                                                $('#formdatacceso input').each(function(){
                                                    $(this).val("");
                                                });
                                            }
                                            $('#precargadiv').fadeOut('fast');
                                        }
                                    });
                                }else{
                                    $.dialog({
                                        title: 'Atención!',
                                        content: 'El número de boletos no puede ser menor o igual a 0!',
                                        icon: 'fa fa-exclamation-circle',
                                        type: 'red',
                                        typeAnimated: true,
                                        backgroundDismissAnimation: 'glow',
                                        boxWidth: widthjc,
                                        useBootstrap: false,
                                        buttons: {
                                            close: function () {                                                    
                                            }
                                        }
                                    });
                                    $(this).val("").change();
                                }    
                            }else{
                                $('#formdatacceso input').each(function(){
                                    $(this).val("");
                                });
                                
                                $('#formdatacceso').hide('slow');
                            }
                            break;     
                        case 'descuentoevent':
                            if(value>0 && value<=100){
                            
                            }else{
                                $.dialog({
                                    title: 'Atención!',
                                    content: 'El descuento no puede ser menor a 0 ni mayor al 100%!',
                                    icon: 'fa fa-exclamation-circle',
                                    type: 'red',
                                    typeAnimated: true,
                                    backgroundDismissAnimation: 'glow',
                                    boxWidth: widthjc,
                                    useBootstrap: false,
                                    buttons: {
                                        close: function () {                                                    
                                        }
                                    }
                                });
                                $(this).val("0");
                            }   
                            break;
                        case 'puntosevent':
                            if(value!=="5"){
                                $.confirm({
                                    title: '<b>Atención</b>',
                                    icon: 'fa fa-warning',
                                    type: 'red',
                                    content: '¿Estas seguro de cambiar los puntos a otorgar para este evento?',
                                    typeAnimated: true,
                                    backgroundDismissAnimation: 'glow',
                                    boxWidth: widthjc,
                                    useBootstrap: false,
                                    escapeKey: 'cancel',
                                    buttons: {
                                        formSubmit: {
                                            text: 'Cambiar',
                                            btnClass: 'btn-orange',
                                            action: function () {
                                                $('#idculturapassevent').focus();
                                            }
                                        },
                                        cancel: {
                                            text: 'Cancelar',
                                            btnClass: 'btn-red',
                                            action: function () {
                                                $('#puntosevent').val('5');
                                                $('#idculturapassevent').focus();
                                            }
                                        }
                                    },
                                    onContentReady: function () {
                                        // bind to events
                                        var jc = this;
                                        this.$content.find('form').on('submit', function (e) {
                                            // if the user submits the form by pressing enter in the field.
                                            e.preventDefault();
                                            jc.$$formSubmit.trigger('click'); // reference the button and click it
                                        });
                                    }
                                });    
                            }else{
                                
                            }   
                            break;
                        case 'preventa_fecha':    
                            if(value!==""){
                                $('#formeventpreventa').show();
                                $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                    var infoData = new FormData();
                                    infoData.append('obtener_eventosfecha',"true");
                                    infoData.append('fecha',$('#preventa_fecha').val());
                                    var eventos="";
                                    eventos=obtener_info(infoData,false);
                                    if(eventos!==""){
                                        $('#idevento').html('<option value="">Seleccione...</option>'+eventos);
                                    }else{
                                        $('#idevento').html('<option value="">Sin Eventos para Mostrar.</option>');
                                    }
                                    $('#precargadiv').fadeOut('fast');
                                });
                            }else{
                                $('#formeventpreventa').hide();
                                $('#precargadiv').fadeOut('fast');
                            }
                            break;         
                        case 'idcpacces_prev':
                            $('#boletospreventabody').html("");
                            $('#boletospreventa').hide();
                            $("p.propietariocp").remove();
                            $(this).removeClass('errorstyle');
                            $(this).removeAttr('placeholder');
                            $.when($('#precargadiv').fadeIn('fast')).then(function(){
                                if(value!==""){
                                    var validacultus=0;
                                    var infoData = new FormData();
                                    infoData.append('obtener_info',"single");
                                    infoData.append('campos','count(*) as validcultus');
                                    infoData.append('tabla','wp_usermeta');
                                    infoData.append('where',"meta_key='cp_id_culturapass' and meta_value='"+value+"'");
                                    //Vaciado de la información actual
                                    $.each(obtener_info(infoData,true), function(key, value){ 
                                        validacultus=this.validcultus;
                                    });

                                    if(validacultus==0){
                                        $('#precargadiv').fadeOut('fast');
                                        $.dialog({
                                            title: 'Atención!',
                                            content: 'La tarjeta no se encuentra activada!',
                                            icon: 'fa fa-exclamation-circle',
                                            type: 'red',
                                            typeAnimated: true,
                                            backgroundDismissAnimation: 'glow',
                                            boxWidth: widthjc,
                                            useBootstrap: false,
                                            buttons: {
                                                close: function () {
                                                }
                                            }
                                        });
                                        $('#idculturapassevent').val("").change();
                                        return false;
                                    }

                                    //validar si la tarjeta tiene movimientos corruptos
                                    var infoDatac = new FormData();
                                    infoDatac.append('validcp',"true");
                                    infoDatac.append('idculturapass',value);
                                    //Vaciado de la información actual
                                    if(!obtener_info(infoDatac,false)){
                                        $.dialog({
                                            title: 'Atención!',
                                            content: '<b style="color:red;" class="push-center"><i class="fa fa-warning"></i> Movimientos Corruptos!<br>La tarjeta se desactivará y se comenzará investigación.</b>',
                                            icon: 'fa fa-exclamation-circle',
                                            type: 'red',
                                            typeAnimated: true,
                                            backgroundDismissAnimation: 'glow',
                                            boxWidth: widthjc,
                                            useBootstrap: false,
                                            buttons: {
                                                close: function () {                                                   
                                                }
                                            }
                                        });
                                        $('#precargadiv').fadeOut('fast');
                                        $('#idculturapassevent').val("").change();
                                        $('#idculturapassevent').focus();
                                        return false;
                                    }

                                    //obtener DOM de los boletos sin utilizar
                                    var infoDatac = new FormData();
                                    infoDatac.append('get_boletosprev',"true");
                                    infoDatac.append('idculturapass',value);
                                    var boletosdom=obtener_info(infoDatac,false);
//                                    console.log(boletosdom);
                                    $('#boletospreventa').show();
                                    $('#boletospreventabody').html(boletosdom);
                                    
                                    var infoDatac = new FormData();
                                    infoDatac.append('getnameuswp',"true");
                                    infoDatac.append('idculturapass',value);
                                    var nombreusuario = obtener_info(infoDatac,false);
                                    
                                    $('#boletospreventa').before('<p class="propietariocp">Propietario: <b>'+nombreusuario+"</b></p>")
                                    
                                    $('#precargadiv').fadeOut('fast');
                                }else{                                    
                                    $('#boletospreventabody').html("");
                                    $('#boletospreventa').hide();
                                    $('#precargadiv').fadeOut('fast');
                                }
                            });
                            break;
                    }
                    
                });
                
                $('body').on('keyup','input.prevboleto',function(){ 
                    var id=$(this).attr('id');
                    var value=parseFloat($(this).val());
                    var disponibles=$(this).attr('disponibles');
                                        
                    if(value>=0){
                        if(value>disponibles){
                            $.dialog({
                                title: 'Atención!',
                                content: 'El número de boletos a utilizar no puede exceder de '+disponibles+'!',
                                icon: 'fa fa-exclamation-circle',
                                type: 'red',
                                typeAnimated: true,
                                backgroundDismissAnimation: 'glow',
                                boxWidth: widthjc,
                                useBootstrap: false,
                                buttons: {
                                    close: function () {                                                    
                                    }
                                }
                            });
                            $(this).val("0");
                        }else{
                            $(this).val(value);
                        }
                    }else{
                        $.dialog({
                            title: 'Atención!',
                            content: 'El número de boletos a utilizar no puede ser menor de 0!',
                            icon: 'fa fa-exclamation-circle',
                            type: 'red',
                            typeAnimated: true,
                            backgroundDismissAnimation: 'glow',
                            boxWidth: widthjc,
                            useBootstrap: false,
                            buttons: {
                                close: function () {                                                    
                                }
                            }
                        });
                        $(this).val("0");
                        
                    }
                    
                });
                
                $('body').on('keyup','input.nboleto',function(){ 
                    var id=$(this).attr('id');
                    var value=$(this).val();
                    var nboletos=$('#cantidadboletos').val();
                    var totalbol=0;
                    
                    if(value>=0){
                        $('input.nboleto').each(function(){
                            if($(this).val()!==""){
                                var nb=parseFloat($(this).val());
                                totalbol+=nb;                            
                            }
                        });

                        if(totalbol>nboletos){
                            $.dialog({
                                title: 'Atención!',
                                content: 'El número de boletos en el desgloce no puede exceder de '+nboletos+'!',
                                icon: 'fa fa-exclamation-circle',
                                type: 'red',
                                typeAnimated: true,
                                backgroundDismissAnimation: 'glow',
                                boxWidth: widthjc,
                                useBootstrap: false,
                                buttons: {
                                    close: function () {                                                    
                                    }
                                }
                            });
                            $(this).val("");

                        }
                    }else{
                        $.dialog({
                            title: 'Atención!',
                            content: 'El número de boletos en el desgloce no puede ser menor de 0!',
                            icon: 'fa fa-exclamation-circle',
                            type: 'red',
                            typeAnimated: true,
                            backgroundDismissAnimation: 'glow',
                            boxWidth: widthjc,
                            useBootstrap: false,
                            buttons: {
                                close: function () {                                                    
                                }
                            }
                        });
                        $(this).val("");
                        
                    }
                });
                
                //********************Ejecutar Acciones Ajax********************//
                function senddata(formData,reload,print,printtext){
                    $.ajax({
                        url: paginacontrolador,  
                        type: 'POST',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        async: false,
                        //mientras enviamos el archivo
                        beforeSend: function(){   
                            $('#precargadiv').fadeIn('fast');
                        },
                        //una vez finalizado correctamente
                        success: function(data){ 
                            var width = $(window).width();
                            if(width<=360){
                                widthjc="99%";
                            }else if(width>360 && width<=750){
                                widthjc="70%";
                            }
                            if(data.toString().indexOf("Error")!=-1 || data.toString().indexOf("error")!=-1 || data.toString().indexOf("rollback")!=-1){
                                $('#precargadiv').fadeOut('fast'); 
                                $.dialog({
                                    title: 'Atención!',
                                    content: 'Error al Realizar la Operación<BR>'+data,
                                    type: 'red',
                                    typeAnimated: true,
                                    backgroundDismissAnimation: 'glow',
                                    boxWidth: widthjc,
                                    useBootstrap: false,
                                    buttons: {
                                        close: function () {
                                        }
                                    }
                                });
                            }else{
                                $('#precargadiv').fadeOut('fast');                                 
                                var confirm=$.confirm({
                                    title: 'Operación Realizada Correctamente!',
                                    content: 'Actualizando Contenido...',
                                    type: 'green',
                                    typeAnimated: true,
                                    backgroundDismissAnimation: 'glow',
                                    boxWidth: widthjc,
                                    useBootstrap: false,
                                    autoClose: 'aceptar|1000',
                                    lazyOpen: true,
                                    buttons: {
                                        aceptar: {
                                            text: 'Actualizar',
                                            btnClass: 'btn-green',
                                            action: function () {
                                                if(print){
                                                    var confirm2=$.confirm({
                                                        title: printtext,
                                                        content: data.replace("commit",""),
                                                        type: 'green',
                                                        typeAnimated: true,
                                                        backgroundDismissAnimation: 'glow',
                                                        boxWidth: widthjc,
                                                        useBootstrap: false,
                                                        lazyOpen: true,
                                                        buttons: {
                                                            aceptar: {
                                                                text: 'Cerrar',
                                                                btnClass: 'btn-red',
                                                                action: function () {
                                                                    $('#precargadiv').fadeIn('fast',function(){
                                                                        if(reload){
                                                                            $('#precargadiv').fadeIn('fast');    
                                                                            location.reload();
                                                                        }else{
                                                                            $('#precargadiv').fadeOut('fast');     
                                                                        }
                                                                    });                                                                    
                                                                }
                                                            }
                                                        },
                                                        onContentReady: function () {                                        
                                                            // bind to events
                                                            var jc = this;
                                                            this.$content.find('form').on('submit', function (e) {
                                                                // if the user submits the form by pressing enter in the field.
                                                                e.preventDefault();
                                                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                            });                                       
                                                        }                                    
                                                    });     

                                                    confirm2.open();
                                                }else if(reload){
                                                    location.reload();
                                                }
                                            }
                                        }
                                    },
                                    onContentReady: function () {                                        
                                        // bind to events
                                        var jc = this;
                                        this.$content.find('form').on('submit', function (e) {
                                            // if the user submits the form by pressing enter in the field.
                                            e.preventDefault();
                                            jc.$$formSubmit.trigger('click'); // reference the button and click it
                                        });                                       
                                    }                                    
                                });        
                                
                                
                                confirm.open();
                                confirm.buttons.aceptar.hide();
                                
                                
                            }                          
                        },
                        //si ha ocurrido un error se notifica al usuario
                        error: function (xhr, ajaxOptions, thrownError) {
                            $('#precargadiv').fadeOut('fast'); 
                            alert(xhr.status+'\n'+thrownError+'\n'+xhr.responseText);
                        }
                    }); 
                }
                
                function senddata_local(formData,reload,print,printtext){
                    $.ajax({
                        url: paginaact,  
                        type: 'POST',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        async: false,
                        //mientras enviamos el archivo
                        beforeSend: function(){   
                            $('#precargadiv').fadeIn('fast');
                        },
                        //una vez finalizado correctamente
                        success: function(data){ 
                            var width = $(window).width();
                            if(width<=360){
                                widthjc="99%";
                            }else if(width>360 && width<=750){
                                widthjc="70%";
                            }
                            if(data.toString().indexOf("Error")!=-1 || data.toString().indexOf("error")!=-1 || data.toString().indexOf("rollback")!=-1){
                                $('#precargadiv').fadeOut('fast'); 
                                $.dialog({
                                    title: 'Atención!',
                                    content: 'Error al Realizar la Operación<BR>'+data,
                                    type: 'red',
                                    typeAnimated: true,
                                    backgroundDismissAnimation: 'glow',
                                    boxWidth: widthjc,
                                    useBootstrap: false,
                                    buttons: {
                                        close: function () {
                                        }
                                    }
                                });
                            }else{
                                $('#precargadiv').fadeOut('fast');                                 
                                var confirm=$.confirm({
                                    title: 'Operación Realizada Correctamente!',
                                    content: 'Actualizando Contenido...',
                                    type: 'green',
                                    typeAnimated: true,
                                    backgroundDismissAnimation: 'glow',
                                    boxWidth: widthjc,
                                    useBootstrap: false,
                                    autoClose: 'aceptar|1000',
                                    lazyOpen: true,
                                    buttons: {
                                        aceptar: {
                                            text: 'Actualizar',
                                            btnClass: 'btn-green',
                                            action: function () {
                                                if(print){
                                                    var confirm2=$.confirm({
                                                        title: printtext,
                                                        content: data.replace("commit",""),
                                                        type: 'green',
                                                        typeAnimated: true,
                                                        backgroundDismissAnimation: 'glow',
                                                        boxWidth: widthjc,
                                                        useBootstrap: false,
                                                        lazyOpen: true,
                                                        buttons: {
                                                            aceptar: {
                                                                text: 'Cerrar',
                                                                btnClass: 'btn-red',
                                                                action: function () {
                                                                    $('#precargadiv').fadeIn('fast',function(){
                                                                        if(reload){
                                                                            $('#precargadiv').fadeIn('fast');    
                                                                            location.reload();
                                                                        }else{
                                                                            $('#precargadiv').fadeOut('fast');     
                                                                        }
                                                                    });                                                                    
                                                                }
                                                            }
                                                        },
                                                        onContentReady: function () {                                        
                                                            // bind to events
                                                            var jc = this;
                                                            this.$content.find('form').on('submit', function (e) {
                                                                // if the user submits the form by pressing enter in the field.
                                                                e.preventDefault();
                                                                jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                            });                                       
                                                        }                                    
                                                    });     

                                                    confirm2.open();
                                                }else if(reload){
                                                    location.reload();
                                                }
                                            }
                                        }
                                    },
                                    onContentReady: function () {                                        
                                        // bind to events
                                        var jc = this;
                                        this.$content.find('form').on('submit', function (e) {
                                            // if the user submits the form by pressing enter in the field.
                                            e.preventDefault();
                                            jc.$$formSubmit.trigger('click'); // reference the button and click it
                                        });                                       
                                    }                                    
                                });        
                                
                                
                                confirm.open();
                                confirm.buttons.aceptar.hide();
                                
                                
                            }                          
                        },
                        //si ha ocurrido un error se notifica al usuario
                        error: function (xhr, ajaxOptions, thrownError) {
                            $('#precargadiv').fadeOut('fast'); 
                            alert(xhr.status+'\n'+thrownError+'\n'+xhr.responseText);
                        }
                    }); 
                }
                                
                //validación de campos con clase REQUIRED de un FORM ESPECIFICO
                function valida(form){
                    var valid=true;
                    var elementval=0;
                    $(form+' input.required').each(function(){   
                        $(this).removeClass('errorstyle');
                        if($(this).val()=="" && $(this).attr('id')){
                            if(elementval==0){
                                $(this).focus();
                                elementval++;
                            }
                            $(this).addClass('errorstyle');  
                            $(this).attr('placeholder','Campo Obligatorio'); 
                            valid=false;
                        }                                                
                    }); 
                    
                    $(form+' select.required').each(function(){   
                        $(this).removeClass('errorstyle');
                        if($(this).val()=="" && $(this).attr('id')){
                            if(elementval==0){
                                $(this).focus();
                                elementval++;
                            }
                            $(this).addClass('errorstyle');  
                            valid=false;
                        }                        
                    }); 
                    
                    return valid;
                }
                
                 //Función para obtener informacion por medio de ajax 
                function obtener_info(datainfo,evaluar){
                    var informacion=null;
                    
                    $.ajax({
                        url: paginacontrolador,  
                        type: 'POST',
                        data: datainfo,
                        cache: false,
                        contentType: false,
                        processData: false,
                        async: false,
                        //mientras enviamos el archivo
                        beforeSend: function(){  
                        },
                        //una vez finalizado correctamente
                        success: function(data){ 
                              informacion=data;                
                        },
                        //si ha ocurrido un error se notifica al usuario
                        error: function (xhr, ajaxOptions, thrownError) {
//                            $('#carga').fadeOut('fast'); 
                            alert(xhr.status+'\n'+thrownError+'\n'+xhr.responseText);
                        }
                    }); 
                    
                    if(evaluar){
                        //return de la información en formato JSON evaluado en forma de ARRAY
                        return eval("(" + informacion + ")");
                    }else{
                        //retorna informacion sin evaluar, obetenida directamente de la peticion ajax
                        return informacion;
                    }
                }
                                
                 //Función para obtener informacion por medio de ajax 
                function obtener_info_local(datainfo,evaluar){
                    var informacion=null;
                    
                    $.ajax({
                        url: paginaact,  
                        type: 'POST',
                        data: datainfo,
                        cache: false,
                        contentType: false,
                        processData: false,
                        async: false,
                        //mientras enviamos el archivo
                        beforeSend: function(){  
                        },
                        //una vez finalizado correctamente
                        success: function(data){ //console.log(data);
                              informacion=data;                
                        },
                        //si ha ocurrido un error se notifica al usuario
                        error: function (xhr, ajaxOptions, thrownError) {
//                            $('#carga').fadeOut('fast'); 
                            alert(xhr.status+'\n'+thrownError+'\n'+xhr.responseText);
                        }
                    }); 
                    
                    if(evaluar){
                        //return de la información en formato JSON evaluado en forma de ARRAY
                        return eval("(" + informacion + ")");
                    }else{
                        //retorna informacion sin evaluar, obetenida directamente de la peticion ajax
                        return informacion;
                    }
                }
                                
               $('#precargadiv').fadeOut('fast');    
            });
        });
        
            function showAndroidToast(toast) {
                var android=false;
                var webbrowser='<?php echo $_SERVER['HTTP_X_REQUESTED_WITH'];?>';
                if(webbrowser!==""){
                    android=true;
                }
                if(android){
                    Android.showToast(toast);
                }else{
                    
                }                
            }
            
            function PrintTicket(data) {   
                var android=false;
                var webbrowser='<?php echo $_SERVER['HTTP_X_REQUESTED_WITH'];?>';
                if(webbrowser!==""){
                    android=true;
                }
                if(android){
                    AndroidPrint.PrintTicket(data);
                }else{                  
                    var ventana = window.open('', 'PRINT', 'height=400,width=600');
                    ventana.document.write('<html><head><title>Cultura Pass Ticket</title>');
                    ventana.document.write('</head><body ><pre>');
                    ventana.document.write(data);
                    ventana.document.write('</pre></body></html>');
                    ventana.document.close();
                    ventana.focus();
                    ventana.print();
                    ventana.close();
                    return true;
                }
            }
        </script>
        
        
        <?php
	//echo 'Username: ' . $current_user->user_login . "<br>";
	//echo 'User email: ' . $current_user->user_email . "<br>";
	//echo 'User level: ' . $current_user->user_level . "<br>";
	//echo 'User first name: ' . $current_user->user_firstname . "<br>";
	//echo 'User last name: ' . $current_user->user_lastname . "<br>";
//	echo 'User ID: ' . $current_user->ID . "<br>";
	echo '<h4 style="padding-top:0px!important;">Bienvenido: ' . $current_user->user_firstname .' '.$current_user->user_lastname . "</h4>";
	

	//$all_meta_for_user = get_user_meta( $current_user->ID );
	//print_r( $all_meta_for_user );

	//$all_meta_for_user = array_map( function( $a ){ return $a[0]; }, get_user_meta( $current_user->ID ) );
	//print_r( $all_meta_for_user );
	//echo 'ID culturapass: '.$all_meta_for_user['cp_id_culturapass'].'<br><br>';
//	if( !empty($all_meta_for_user['cp_id_culturapass']) ){
//		//echo "Genero: ".$all_meta_for_user['cp_gen'];
//		//echo ':)';
//		echo 'Puedes hacer reservaciones :)';
//	}else{
//		echo '<br>:(  Debes acudir acudir aun recinto certificado por las Secretaría de Cultura del Estado de Hidalgo; donde al presentar una identificación oficial se te activara y entregara tu CulturaPass';
//	}



?>
<!-- Contenido de la Pagina en GRID de INK -->	
    <div class="ink-grid vspace">
        <!-- Div de bloqueo para precarga de información -->
        <div id="precargadiv">
            <div id="precarga" class="" style="text-align: center;">            
                <!--<img src="<?php echo $path; ?>images/cargando.gif" style=" width: 100px; height: 100px;"/><br>-->
                <i class="fa fa-spinner fa-spin fa-fw fa-4x"></i><br>
                <b>Cargando Informaci&oacute;n. Espere...</b>
            </div>	
        </div>

        <!--******************************Div con el contenido a mostrar al usuario (tablas, información, formularios estaticos etc.-->
        <div class="column-group">
            <div class="all-100 control-group gutters " style="text-align:center">
                <h1 style="font-size: 2em; color: #999; margin-bottom: 20px;">Menú de Opciones</h1>
                <!--<a class="ink-button green push-center small-80 tiny-100 medium-70 all-50" target="_blank" href="../wp-admin/user-new.php">Activar Tarjeta</a>-->
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50" id="agregarusr">Registrar Tarjeta</a>
                <br>
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50" id="activarcultus">Activar Tarjeta</a>
                <br>
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="abono">Abonar Saldo</a>
                <br>
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="consulta">Consultar Saldo</a>
                <br>
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="acceso">Acceso a Evento</a>                
                <br>
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="preventa">Preventa Evento</a>                
                <br>                
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="accesopreventa">Acceso a Evento Preventa</a>  
                <br>                
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="preregsitrorep">Reporte Preregistros</a>  
                <br>                
                <br>
                <a class="ink-button green push-center small-80 tiny-100 medium-70 all-50"  id="" href="reportes-culturapass" target="_blank">Reporte Asistencia a Eventos</a>  
            </div>
        </div>            
    </div><!--Contenido-->
        
        
        
<div style="width:100%; clear:left;"></div>
<br>
<a  class="ink-button red" href="<?php echo $logoutpage; ?>">Salir</a>
<br>
<br>