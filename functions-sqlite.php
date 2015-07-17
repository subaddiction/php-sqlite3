<?php



$link = new SQLite3('test.db');
$sqlite_table_prefix = '';
unset($query_error);



function mrksql3_error(){
	global $query_error;
	if($query_error){
	echo'<div class="error">'.$query_error.'</div>';
	}
}

//escape GET and POST before doing anything

function mrksql3_escape(){
	
	global $db, $_GET, $_POST, $_REQUEST;

	foreach($_GET as $k=> $v){
		$_GET[$k] = $db->escapeString($v);
	}

	foreach($_POST as $k=> $v){
		$_POST[$k] = $db->escapeString($v);
	}

	foreach($_REQUEST as $k=> $v){
		$_REQUEST[$k] = $db->escapeString($v);
	}
	
}


function mrksql3_select($table, $fields, $condition, $order, $limit){

global $link, $db_charset, $query_error, $sqlite_table_prefix;
//mrksql_escape();

$query = "SELECT ".$fields." FROM ".$sqlite_table_prefix.$table;

	if($condition){
		//PER VALIDARE UN INPUT UTENTE PASSARE UN ARRAY CHE VIENE ESCAPATO E CONCATENATO COME SERIE DI AND $K = $V
		if(!is_array($condition)){
		//echo '---querying with condition not sanitized---';
		$query .= " WHERE ".$condition;
		} else {
			
			$query .= " WHERE ";
			
			$fields = 0;
			$totfields = count($condition) - 1;
			foreach($condition as $k => $cond){
			//$query .= htmlentities($k, ENT_QUOTES, $db_charset)." = '".htmlentities($cond, ENT_QUOTES, $db_charset)."'";
			$query .= $k." = '".$cond."'";
				if($fields < $totfields){
				$query .= " AND ";
				}
				$fields++;
			}
		}
		//$query .= " WHERE ".$condition;	
	}
	if($order){
	$query .= " ORDER BY ".$order;
	}
	if($limit){
	$query .= " LIMIT ".$limit;
	}
//echo $query;	
$result = $link->query($query);
$data = array();

while($row = $result->fetchArray(SQLITE3_ASSOC)){
	$data[] = $row;
}


return $data;

}


function mrksql3_insert($table, $array, $htmlentitize=false){

global $link, $db_charset, $query_error, $sqlite_table_prefix;
//mrksql_escape();

$keys = "";
$values = "";
$fields = 0;
$totfields = count($array) - 1;
foreach($array as $k => $v){

	if($htmlentitize){
	$keys .= htmlentities($k, ENT_QUOTES, $db_charset);
	} else {
	$keys .= $k;
	}
	if($fields < $totfields){
	$keys .=",";
	}
	
	if($htmlentitize){
	$values .= "'".htmlentities($v, ENT_QUOTES, $db_charset)."'";
	} else {
	$values .= "'".$v."'";
	}
	if($fields < $totfields){
	$values .=",";
	}
	$fields++;

//$keys[] = htmlentities($k, ENT_QUOTES, $db_charset);
//$values[] = htmlentities($v, ENT_QUOTES, $db_charset);
}

//$keys = explode(',' $keys);
$query = "INSERT INTO ".$sqlite_table_prefix.$table." (".$keys.") VALUES (".$values.")";
//echo $query;
	if(!$link->exec($query)){
	$query_error .= $link->lastErrorMsg();
	return false;
	} else {
	return true;
	}
	
}

function mrksql3_update($table, $array, $condition, $htmlentitize=true){

global $link, $db_charset, $query_error, $sqlite_table_prefix;
//mrksql_escape();

$fields = 0;
$totfields = count($array) - 1;

$query = "UPDATE ".$sqlite_table_prefix.$table." SET ";
foreach($array as $key => $value){
	if($htmlentitize){
	$query .= htmlentities($key, ENT_QUOTES, $db_charset)." = '".htmlentities($value, ENT_QUOTES, $db_charset)."'";
	} else {
	$query .= $key." = '".$value."'";
	}
	if($fields < $totfields){
	$query .=', ';
	}
	$fields++;
}

	if($condition){
		if(!is_array($condition)){
		//echo '---querying with condition not sanitized---';
		$query .= " WHERE ".$condition;
		} else {
			
			$query .= " WHERE ";
			
			$fields = 0;
			$totfields = count($condition) - 1;
			foreach($condition as $k => $cond){
			//$query .= htmlentities($k, ENT_QUOTES, $db_charset)." = '".htmlentities($cond, ENT_QUOTES, $db_charset)."'";
			$query .= $k." = '".$cond."'";
				if($fields < $totfields){
				$query .= " AND ";
				}
				$fields++;
			}
		}
	}
	
	if(!$link->exec($query)){
	$query_error .= $link->lastErrorMsg();
	return false;
	} else {
	return true;
	}

//echo $query;
	
}


function mrksql3_delete($table, $condition){

	global $link, $query_error, $sqlite_table_prefix;
	//mrksql_escape();

	$query = "DELETE FROM ".$sqlite_table_prefix.$table;
	
	if(!is_array($condition)){
		//echo '---querying with condition not sanitized---';
		$query .= " WHERE ".$condition;
		} else {	
			$query .= " WHERE ";
			$fields = 0;
			$totfields = count($condition) - 1;
			foreach($condition as $k => $cond){
			//$query .= htmlentities($k, ENT_QUOTES, $db_charset)." = '".htmlentities($cond, ENT_QUOTES, $db_charset)."'";
			$query .= $k." = '".$cond."'";
				if($fields < $totfields){
				$query .= " AND ";
				}
				$fields++;
			}
		}
	
	if(!$link->exec($query)){
	$query_error .= $link->lastErrorMsg();
	return false;
	} else {
	return true;
	}
}


//LAST ID
function mrksql3_lastid(){
	
	global $link;
	return $link->lastInsertRowID();

}


//COUNT A RESULT SET
function mrksql3_count($table, $fields=0, $condition='1'){
//mrksql_escape();
	global $link;
	//$query = "SELECT COUNT(".$fields.") FROM ".$table;
	$query = "SELECT ".$fields." FROM ".$table;
	
	if(!is_array($condition)){
	//echo '---querying with condition not sanitized---';
	$query .= " WHERE ".$condition;
	} else {	
		$query .= " WHERE ";
		$fields = 0;
		$totfields = count($condition) - 1;
		foreach($condition as $k => $cond){
		//$query .= htmlentities($k, ENT_QUOTES, $db_charset)." = '".htmlentities($cond, ENT_QUOTES, $db_charset)."'";
		$query .= $k." = '".$cond."'";
			if($fields < $totfields){
			$query .= " AND ";
			}
			$fields++;
		}
	}
	
	//echo $query;
	
	$data = $link->exec($query);
	//$data = mysql_result($data, 0);
	$data = $data->num_rows;

	return $data; 

}


//FULL TEXT MODE SEARCH
function mrksql3_search($table, $fields, $searchfields, $searchkey, $condition=false, $wildcard=true, $boolean_mode=true, $query_expansion=true){
//mrksql_escape();
	
	global $link;
	
	if($boolean_mode && $wildcard){
	$searchkey = $searchkey.'*';
	}
	
	$query = "SELECT ".$fields." FROM ".$table." WHERE ";
	$query .= "MATCH (".$searchfields.") ";
	$query .= "AGAINST ('".$searchkey."' ";
	
	if($boolean_mode){
	$query .= "IN BOOLEAN MODE ";
	} else {
	$query .= "IN NATURAL LANGUAGE MODE ";
	
		if($query_expansion){
		$query .= "WITH QUERY EXPANSION";
		}
		
	}
	
	

	$query .= ')';
	
	if($condition){
		if(!is_array($condition)){
		//echo '---querying with condition not sanitized---';
		$query .= " AND ".$condition;
		} else {	
			$query .= " AND ";
			$fields = 0;
			$totfields = count($condition) - 1;
			foreach($condition as $k => $cond){
			//$query .= htmlentities($k, ENT_QUOTES, $db_charset)." = '".htmlentities($cond, ENT_QUOTES, $db_charset)."'";
			$query .= $k." = '".$cond."'";
				if($fields < $totfields){
				$query .= " AND ";
				}
				$fields++;
			}
		}
	}
	
	//print_r($query);
	
	$result = $link->exec($query);
	
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		$data[] = $row;
	}

	return $data;


}

// EOF sqlite lib



//DEBUG UTILITY
function debug($var){

	echo '<pre class="debug">';
	print_r($var);
	echo '</pre>';

}

?>
