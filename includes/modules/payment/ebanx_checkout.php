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

require_once 'ebanx/ebanx-php-master/src/autoload.php';

class ebanx_checkout extends base 
{
    var $code, $title, $description, $enabled, $payment, $checkoutURL, $status, $message;
    
    // class constructor
    function ebanx_checkout()
    {
        global $order;
        $this->code = 'ebanx_checkout';
        $this->title = MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_EBANX_CHECKOUT_STATUS == 'True') ? true : false);

        if (is_object($order))
        {
            $this->update_status();
        }
    }

    // class methods
    function update_status()
    {
        global $db;
        global $order;

        if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_EBANX_CHECKOUT_ZONE > 0) )
        {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES .
            " where geo_zone_id = '" . MODULE_PAYMENT_EBANX_CHECKOUT_ZONE . "' and zone_country_id = '" .
            $order->billing['country']['id'] . "' order by zone_id");
            while (!$check->EOF)
            {
                if ($check->fields['zone_id'] < 1)
                {
                    $check_flag = true;
                    break;
                }
                elseif ($check->fields['zone_id'] == $order->billing['zone_id'])
                {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }

            if ($check_flag == false)
            {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation()
    {
        return false;
    }

    function selection()
    {
        global $order;
        if($order->billing['country']['title'] == 'Peru')
        {
            $selection   = array('id' => $this->code,
                                 'module' => MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_CATALOG_TITLE_PERU
                                );
        }

        if($order->billing['country']['title'] == 'Brazil')
        {
            $selection   = array('id' => $this->code,
                                 'module' => MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_CATALOG_TITLE
                                );
        }

        if($order->billing['country']['title'] == 'Mexico')
        {
            $selection   = array('id' => $this->code,
                                 'module' => MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_CATALOG_TITLE_MEXICO
                                );
        }

        return $selection;
    }
  
    function pre_confirmation_check()
    {
        return false;
    }

    function confirmation()
    {
        return false;
    }

    function process_button()
    {
        return false;
    }

    function before_process()
    {                         
        global $_POST,  $order, $sendto, $currency, $charge,$db, $messageStack;

        // Street number workaround
        $streetNumber = preg_replace('/[\D]/', '', $order->billing['street_address']);
        $streetNumber = ($streetNumber > 0) ? $streetNumber : '1';

        // Creates notification and return URL
        $returnURL = zen_href_link('ebanx_return.php', '', 'SSL', false, false, true);
        $callbackURL = zen_href_link('ebanx_notification.php', '', 'SSL', false, false, true);
      
        // Sets EBANX Configuration parameters within lib
        \Ebanx\Config::set(array(
             'integrationKey' => MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY
            ,'testMode'       => MODULE_PAYMENT_EBANX_CHECKOUT_TESTMODE
        ));

        //Country title workaround
        if($order->billing['country']['title'] == 'Brazil')
        {
            $country = 'BR';
        }
 
        if($order->billing['country']['title'] == 'Peru')
        {
            $country = 'PE';
        }

        if($order->billing['country']['title'] == 'Mexico')
        {
            $country = 'MX';
        }

        // Creates next order ID
        $last_order_id = $db->Execute("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");
        $new_order_id = $last_order_id->fields['orders_id'];
        $new_order_id = ($new_order_id + 1);
      
        // Creates array and submits data to EBANX
        $submit = \Ebanx\Ebanx::doRequest(array(
                 'currency_code'     =>  $order->info['currency']
                , 'amount'            =>  $order->info['total']
                , 'name'              =>  $order->billing['firstname'] . ' ' . $order->billing['lastname']
                , 'email'             =>  $order->customer['email_address']
                , 'payment_type_code' =>  '_all'
                , 'merchant_payment_code' => $new_order_id
                , 'country'           => $country
                , 'zipcode'           => $order->billing['postcode']
                , 'phone_number'      => $order->customer['telephone']
                                          )
        ); 

      $this->status = $submit->status;
      $this->message = $submit->status_message;
      if($this->status == 'SUCCESS')
      {   
          
          // Resets cart, saves Checkout URL
          $_SESSION['cart']->reset(true);
          $this->checkoutURL = $submit->redirect_url;
          $hash = $submit->payment->hash;
      }

      return false;
    }

    function after_process()
    {
        global $messageStack;
        // Redirects to Checkout URL
        if($this->status == 'SUCCESS')
        {
            zen_redirect($this->checkoutURL);
        }
        else
        {
            $payment_error_return = 'payment_error=' . $this->code;
            $messageStack->add_session('checkout_payment', 'Erro no pagamento, contate o administrador do site!' . ' '. $this->message);
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }

        return false;
    }

    function get_error()
    {
        return false;
    }

    function check()
    {
        global $db;
        
        if (!isset($this->_check))
        {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EBANX_CHECKOUT_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
  
        return $this->_check;
    }

    function install()
    {
        require_once 'ebanx/installer.php';
        $integrationKey = 0;
        global $db, $messageStack;
        if (defined('MODULE_PAYMENT_EBANX_CHECKOUT_STATUS'))
        {
            $messageStack->add_session('Ebanx Checkout module already installed.', 'error');
            zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=ebanx', 'NONSSL'));
            return 'failed';
        }

        //Creates states list to Brazil
        $installer = new Installer();
        $installer->stateInstaller($db);

        //Creates order statuses for EBANX
        $installer->statusInstaller($db);

        // Sets Integration Key if already existing in TABLE_CONFIGURATION
        $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " c where c.configuration_key = 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY'");
      
        if(isset($check_query))
        {
            $integrationKey = $check_query->fields['configuration_value'];
        }

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ebanx Checkout', 'MODULE_PAYMENT_EBANX_CHECKOUT_STATUS', 'True', 'Enables EBANX Checkout for LATAM', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Integration Key', 'MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY', '". $integrationKey . "', 'Your EBANX unique integration key', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_EBANX_CHECKOUT_TESTMODE', 'True', 'Test Mode?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_EBANX_CHECKOUT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    }

    function remove()
    {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
        return array('MODULE_PAYMENT_EBANX_CHECKOUT_STATUS', 'MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY', 'MODULE_PAYMENT_EBANX_CHECKOUT_TESTMODE', 'MODULE_PAYMENT_EBANX_CHECKOUT_ZONE');
    }
  
  }
