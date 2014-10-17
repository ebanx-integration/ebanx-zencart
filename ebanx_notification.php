<?php
 ini_set('display_errors', -1);
 error_reporting(E_ALL ^ E_NOTICE);

require('includes/application_top.php');
require (DIR_WS_MODULES . 'payment/ebanx/ebanx-php-master/src/autoload.php');
global $db;



        \Ebanx\Config::set(array(
            'integrationKey' => MODULE_PAYMENT_EBANX_INTEGRATIONKEY
           
           ,'testMode'       => MODULE_PAYMENT_EBANX_TESTMODE
        ));



$hashes = $_REQUEST['hash_codes'];

$response = \Ebanx\Ebanx::doQuery(array('hash' => $hashes));


if ($response->status == 'SUCCESS')
{

    if($response->payment->status == 'CO')
    {   
        $code = $response->payment->merchant_payment_code;
        $db->Execute('UPDATE ' . TABLE_ORDERS . ' SET orders_status = 2 WHERE orders_id = ' . $code);
        $db->Execute('UPDATE ' . TABLE_ORDERS_STATUS_HISTORY . ' SET orders_status_id = 2 WHERE orders_status_history_id = ' . $code);
        echo 'Payment CO';
    }
    if($response->payment->status == 'CA')
    {   
        $check_query = $db->Execute("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'Cancelled' limit 1");
        $status_id = $check_query->fields['orders_status_id'];
        $code = $response->payment->merchant_payment_code;
        $db->Execute('UPDATE ' . TABLE_ORDERS . ' SET orders_status = ' . $status_id . ' WHERE orders_id = ' . $code);
        $db->Execute('UPDATE ' . TABLE_ORDERS_STATUS_HISTORY . ' SET orders_status_id = ' . $status_id . ' WHERE orders_status_history_id = ' . $code);
        echo 'Payment CA';

    }


}

else echo 'Failure in contacting EBANX';