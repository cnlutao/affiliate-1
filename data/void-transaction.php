<?php include_once '../auth/startup.php';
$transaction_id = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_STRING);
$affiliate_id = filter_input(INPUT_POST, 'a', FILTER_SANITIZE_STRING);
$redirect = filter_input(INPUT_POST, 'r', FILTER_SANITIZE_STRING);
$get_ta = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT net_earnings FROM ap_earnings WHERE id=$transaction_id"));
$ta = $get_ta['net_earnings'];

if($admin_user=='1'){
	//VOID TRANSACTION
	$one = '1';
	$update_one = $mysqli->prepare("UPDATE ap_earnings SET void = ?, stop_recurring = ? WHERE id=$transaction_id"); 
	$update_one->bind_param('ss', $one, $one);
	$update_one->execute();
	$update_one->close();
	
//UPDATE BALANCE
$get_tb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT balance FROM ap_members WHERE id=$affiliate_id"));
$tb = $get_tb['balance'];

$updated_balance = $tb - $ta;
if($updated_balance < 0){$updated_balance ='0.00';}
	$update_one = $mysqli->prepare("UPDATE ap_members SET balance = ? WHERE id=$affiliate_id"); 
	$update_one->bind_param('s', $updated_balance);
	$update_one->execute();
	$update_one->close();
}
$mysqli->close();

if($redirect == 'affiliate-stats'){$redirect = 'affiliate-stats?a='.$affiliate_id.'';}else { $redirect = 'sales-profits';}

header('Location: ../'.$redirect.'');