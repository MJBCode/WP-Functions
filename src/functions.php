<?php

if(!function_exists('remove_emoji')){
    function remove_emoji($text){
        return preg_replace('/[[:^print:]]/', '', $text);
    }
}

if(!function_exists('get_user_role')){
	function get_user_role() {
	 	global $wp_roles;

	    $current_user = wp_get_current_user();
	    $roles = $current_user->roles;
	    $role = array_shift($roles);

	    return $role;
	}
}

if(!function_exists('is_cli_running')){
	function is_cli_running() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}
}

if(!function_exists('cli_log')){
	function cli_log($title, $message, $force_plain=false){
		date_default_timezone_set('UTC');

	   	$line = '['.date("Y-m-d H:i:s").'] '.$title.': '.$message;

	   	if($force_plain){
			echo $line.PHP_EOL;
	   	}else{
	   		if(is_cli_running()) {
		   		WP_CLI::line($line);
		   	}else{
		   		echo $line.PHP_EOL.'<br />';
		   	}
	   	}
	}
}

if(!function_exists('addslashes_array')){
	function addslashes_array($array){
		foreach ($array as $key => $value) {
		    $array[$key] = addslashes($value);
		}

		return $array;
	}
}

if(!function_exists('get_domain')){
	function get_domain(){
		$domain = parse_url(get_site_url(), PHP_URL_HOST);

		return $domain;
	}
}

if(!function_exists('file_cache_ver')){
	function file_cache_ver($file, $theme=true, $prefix=true){
	    $filepath = '';

	    if($theme){
	        $filepath = get_stylesheet_directory().'/'.$file;
	    }else{
	        $filepath = $file;
	    }

	    $time = filemtime($filepath);

	    if($prefix) $time = '?ver='.$time;

	    return $time;
	}
}

if(!function_exists('build_the_query')){
	function build_the_query($array, $trim=false){
	    $query_array = array();

	    foreach($array as $field=>$value){
	        if($trim) $value=trim($value);
	        $query_array[] = $field.'="'.$value.'"';
	    }

	    $query_array = implode(",", $query_array);

	    return $query_array;
	}

}

if(!function_exists('query_builder')){
	function query_builder($array=array(), $table="", $action="", $where="", $exclude=array(), $execute=false, $update_field=""){

		global $wpdb;

		if(strpos($table, $wpdb->prefix)===FALSE) $table = $wpdb->prefix.$table;

	    if(empty($exclude)) $exclude = array('option_page', 'action', '_wpnonce', '_wp_http_referer', 'submit');

	    $query_array = array();
	    $data_return = array();

	    foreach($array as $field=>$value){
	        if(!in_array($field, $exclude)){
	            if($value=="") {
	                $value = "NULL";
	            }else{
	                $value = "'".$value."'";
	            }
	            $query_array[] = $field."=".$value;
	        }
	    }

	    $query_array = implode(",", $query_array);

	    if(!empty($where)){
	        $where = " WHERE ".$where;
	    }

	    $the_query = $action.' '.$table.' SET '.$query_array.$where;
	    
	    //echo $the_query;exit;
	    
	    $data_return['data']['query'] = $the_query;

	    if($execute){
	        $query_result = $wpdb->query($the_query);
	   
	        $data_return['data']['id'] = $wpdb->insert_id;

	        if(!empty($update_field)){
	            $update_field_query = 'UPDATE '.$table.' SET '.$update_field.'='.$data_return['data']['id'].' WHERE id='.$data_return['data']['id'];

	            $query_result = $wpdb->query($update_field_query);
	        }
	    }

	    $data_return['status'] = true;
	    $data_return['message'] = "Updated Successfully";
	    
	    return $data_return;
	}
}

if(!function_exists('basedomain')){
    function basedomain(){
	    $url = site_url();

        $url = @parse_url( $url );
        if ( empty( $url['host'] ) ) return;
        $parts = explode( '.', $url['host'] );
        $slice = ( strlen( reset( array_slice( $parts, -2, 1 ) ) ) == 2 ) && ( count( $parts ) > 2 ) ? 3 : 2;
        return implode( '.', array_slice( $parts, ( 0 - $slice ), $slice ) );
    }
}

if(!function_exists('log_sync_error')){
    function log_sync_error($type, $message){
	    // UTC Time
        date_default_timezone_set('UTC');
	    
	    global $wpdb;

	    $query = "
		    INSERT INTO 
			    ".$wpdb->prefix."error_log
		    SET
			    type='".$type."',
			    message='".addslashes($message)."',
			    datetime='".date('Y-m-d H:i:s')."'
		    ";

	    $wpdb->query($query);
    }
}

if(!function_exists('validateDate')){
    function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if(!function_exists('print_r2')){
    function print_r2($array){
	    echo '<pre>';
	    print_r($array);
	    echo '</pre>';
    }
}

if(!function_exists('dd')){
    function dd($array){
	    print_r2($array);
    }
}

if(!function_exists('convertDateTimeLocal')){
    function convertDateTimeLocal($datetime, $timezone=""){	
	    if(empty($datetime)) return $datetime;
	   	if(empty($timezone)) $timezone = get_option('timezone_string');

	    $datetime = new DateTime($datetime);
	    $timezone = new DateTimeZone($timezone);

	    $datetime->setTimezone($timezone);

	    $datetime = $datetime->format('Y-m-d H:i:s');

	    return $datetime;
    }
}

if(!function_exists('convertDateTimeUTC')){
    function convertDateTimeUTC($datetime, $timezone=""){
	    if(empty($datetime)) return $datetime;
	    if(empty($timezone)) $timezone = get_option('timezone_string');
	    
	    date_default_timezone_set($timezone);

	    $datetime = new DateTime($datetime);
	    $timezone = new DateTimeZone('UTC');

	    $datetime->setTimezone($timezone);

	    $datetime = $datetime->format('Y-m-d H:i:s');

	    return $datetime;
    }
}

if(!function_exists('getDateTimeFromDates')){
    function getDateTimeFromDates($start, $finish){
        if(empty($start) || empty($finish)) return '-';

        $datetime1 = strtotime($start);
        $datetime2 = strtotime($finish);
        $interval  = abs($datetime2 - $datetime1);
        $minutes   = number_format(($interval / 60), 2);

        return $minutes."mins";
    }
}

if(!function_exists('xmlToArray')){
    function xmlToArray($path){
        $xmlfile = file_get_contents($path);
        $ob= simplexml_load_string($xmlfile);
        $json  = json_encode($ob);
        $configData = json_decode($json, true);

        return $configData;
    }
}

if(!function_exists('updateCronMeta')){
	function updateCronMeta($data_array, $table, $insert_array=array()){
	    global $wpdb;

	    foreach($data_array as $key=>$value){
	        $update_query = build_the_query($value);

	        $insert_query = build_the_query($insert_array);
	        if(!empty($insert_query)) $insert_query = ",".$insert_query; 

	        $query = "INSERT INTO
	                    ".$table."
	                    SET
	                        ".$update_query.$insert_query."
	                    ON DUPLICATE KEY UPDATE
	                    ".$update_query."
	                ";

	        $wpdb->query($query);
	    }
	}
}
