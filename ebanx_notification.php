<?php

/**
 * Copyright (c) 2014, EBANX Tecnologia da Informação Ltda.
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * Neither the name of EBANX nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require('includes/application_top.php');
require (DIR_WS_MODULES . 'payment/ebanx/ebanx-php-master/src/autoload.php');
global $db;

if(defined(MODULE_PAYMENT_EBANX_INTEGRATIONKEY))
{
    $int = MODULE_PAYMENT_EBANX_INTEGRATIONKEY;
}
else
{
    $int = MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY;
}

\Ebanx\Config::set(array(
    'integrationKey' => $int
   ,'testMode'       => MODULE_PAYMENT_EBANX_TESTMODE
));

$hashes = $_REQUEST['hash_codes'];

$hashes = explode(',', $hashes);

if (isset($hashes) && $hashes != null)
{
    foreach ($hashes as $hash)
    {
        $response = \Ebanx\Ebanx::doQuery(array('hash' => $hash));

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
        else 
        {
            echo 'Failure in contacting EBANX';
        }
    }
}
