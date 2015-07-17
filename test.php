<?

$debug = true;

if($debug){
error_reporting(E_ALL);
} else {
error_reporting(0);
}
date_default_timezone_set('Europe/Rome');

include('functions-sqlite.php');


##########
echo "\n\nDelete test user...\n";
if(mrksql3_delete('users', array('username'=>'test'))){
	echo "\nOK\n\n";
} else {
	mrksql3_error();
}


##########
echo "\n\nSelect users...\n";
if($testquery = mrksql3_select('users', '*', '1', false, false)){
	print_r($testquery);
	echo "\nOK\n\n";
} else {
	mrksql3_error();
}


##########
echo "\n\nInsert test user...\n";
if(mrksql3_insert('users', array('username'=>'test', 'password'=>'test', 'email'=>'debug@subaddiction.net', 'token'=>'1234567890'))){
	print_r($testquery);
	echo "\nOK - ID:".mrksql3_lastid()."\n\n";
} else {
	mrksql3_error();
}


##########
echo "\n\nSelect test user...\n";
if($testquery = mrksql3_select('users', '*', array('username'=>'test'), false, false)){
	print_r($testquery);
	echo "\nOK\n\n";
} else {
	mrksql3_error();
}












?>
