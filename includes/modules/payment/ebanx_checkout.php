<?php

require_once 'ebanx/ebanx-php-master/src/autoload.php';

class ebanx_checkout extends base 
{

    var $code, $title, $description, $enabled, $payment, $checkoutURL, $status;
    

// class constructor
    function ebanx_checkout()
    {
        global $order;
        $this->code = 'ebanx_checkout';
        $this->title = MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_DESCRIPTION;
        //$this->sort_order = MODULE_PAYMENT_EBANX_CHECKOUT_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_EBANX_CHECKOUT_STATUS == 'True') ? true : false);

        if (is_object($order)) $this->update_status();
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
        if($order->billing['country']['title'] == 'Brazil' || $order->billing['country']['title'] == 'Peru'){
              
            $fieldsArray = array();

            // $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CUSTOMER_CPF,
            //                        'field' => zen_draw_input_field('ebanxb_cpf', '',
            //     20537924218                   'id="'.$this->code.'-cpf"'. $onFocus),
            //                        'tag' => $this->code.'-cpf');

            $selection = array('id' => $this->code,
                               'module' => MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_CATALOG_TITLE
                               //,'fields' => $fieldsArray);
                              );
        }
        return $selection;
    }
  
    function pre_confirmation_check()
    {
      // global $db, $_POST, $messageStack;

      // if (!$this->validaCPF($_POST['ebanxb_cpf'])){

      //     $payment_error_return = 'payment_error=' . $this->code . '&ebanx_cpf=' . $_POST['ebanx_cpf'];
      //     $messageStack->add_session('checkout_payment', 'CPF Inválido!');
      //     zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));

      // }

        return false;
    }

    function confirmation()
    {
        return false;
    }

    function process_button()
    {

        // $process_button_string .= zen_draw_hidden_field('customerb_cpf', $_POST['ebanxb_cpf']);
        // return $process_button_string;
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
                          )
        );

        //Country title workaround
        if($order->billing['country']['title'] == 'Brazil')
        {
            $order->billing['country']['title'] = 'BR';
        }
 
        if($order->billing['country']['title'] == 'Peru')
        {
            $order->billing['country']['title'] = 'PE';
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
                                               , 'country'           => $order->billing['country']['title']
                                               //, 'cpf'          => $_POST['customerb_cpf']
                                               //, 'document'          => $_POST['customerb_cpf']
                                               , 'zipcode'           => $order->billing['postcode']
                                               , 'phone_number'      => $order->customer['telephone']
                                          )
        ); 

      $this->status = $submit->status;

      if($this->status == 'SUCCESS')
      {   
          
          // Resets cart, saves Checkout URL and stores data in database
          unset($_SESSION['cart']);
          $this->checkoutURL = $submit->redirect_url;
          $hash = $submit->payment->hash;
          $db->Execute("insert into ebanx_data (order_id, customers_cpf, hash) values ('" . $new_order_id . "', '" . $_POST['customerb_cpf'] . "', '" . $hash . "')");
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
            $messageStack->add_session('checkout_payment', 'Erro no pagamento, contate o administrador do site!');
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
        private $integrationKey = 0;
        // global $db;
        global $db, $messageStack;
        if (defined('MODULE_PAYMENT_EBANX_CHECKOUT_STATUS'))
        {
            $messageStack->add_session('Ebanx Checkout module already installed.', 'error');
            zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=ebanx', 'NONSSL'));
            return 'failed';
        }

        //Creates EBANX custom table
        $db->Execute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."ebanx_data` (
            `ebanx_id` INT( 11 ) NOT NULL  auto_increment,
            `order_id` VARCHAR( 64 ) NOT NULL ,
            `customers_cpf` VARCHAR( 64 ) NOT NULL ,
            `hash` VARCHAR( 64 ) NOT NULL ,
             PRIMARY KEY  (`ebanx_id`)
             )   AUTO_INCREMENT=1 ;"
        );
        
        // Creates status "Cancelled" for EBANX orders
        $check_query = $db->Execute("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'Cancelled' limit 1");
        if ($check_query->RecordCount() < 1)
        {
            $status    = $db->Execute("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);
            $status_id = $status->fields['status_id'] + 1;
            $languages = zen_get_languages();
            foreach ($languages as $lang)
            {
                $db->Execute("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $status_id . "', '" . $lang['id'] . "', 'Cancelled')");
            } //$languages as $lang
        }
        else
        {
            $status_id = $check_query->fields['orders_status_id'];
        }
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ebanx Module', 'MODULE_PAYMENT_EBANX_STATUS', 'True', 'Do you want to accept Ebanx payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now());");
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_EBANX_TESTMODE', 'True', 'Test Mode?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now());");
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Username', 'MODULE_PAYMENT_BITCOIN_LOGIN', 'testing', 'The Username for Bitcoin RPC', '6', '0', now())");
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Integration Key', 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY', 'testing', 'The API Login ID used for the Authorize.net service', '6', '0', now())");
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_BITCOIN_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_BITCOIN_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
        //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_BITCOIN_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
        

        // Sets Integration Key if already existing in TABLE_CONFIGURATION
        $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " c where c.configuration_key = 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY'");
      
        if(isset($check_query))
        {
            $integrationKey = $check_query->fields['configuration_value'];
        }

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ebanx Checkout', 'MODULE_PAYMENT_EBANX_CHECKOUT_STATUS', 'True', 'Do you want to accept EBANX Boleto and TEF payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
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
 ?>
