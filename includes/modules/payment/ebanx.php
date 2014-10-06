<?php

<<<<<<< HEAD
require_once 'ebanx/ebanx-php-master/src/autoload.php';
=======
require_once(DIR_WS_MODULES) . '/payment/ebanx/ebanx-php-master/src/autoload.php';
>>>>>>> 907a771f5e8f0ecf9f5d6c94b5411db60a89cb4e
//require_once(IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES)
class ebanx extends base {

    var $code, $title, $description, $enabled, $payment;

// class constructor
    function ebanx() {
      global $order;
      $this->code = 'ebanx';
      $this->title = MODULE_PAYMENT_EBANX_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_EBANX_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_EBANX_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_EBANX_STATUS == 'True') ? true : false);

      // if ((int)MODULE_PAYMENT_EBANX_ORDER_STATUS_ID > 0) {
      //   $this->order_status = MODULE_PAYMENT_EBANX_ORDER_STATUS_ID;
      //   $payment='ebanx';
      // } else {
      //   if ($payment=='ebanx') {
      //     $payment='';
      //   }
      // }

<<<<<<< HEAD
      if (is_object($order)) $this->update_status();

       $this->email_footer = MODULE_PAYMENT_BEBANX_TEXT_EMAIL_FOOTER;
=======
      // if (is_object($order)) $this->update_status();

      // $this->email_footer = MODULE_PAYMENT_BEBANX_TEXT_EMAIL_FOOTER;
>>>>>>> 907a771f5e8f0ecf9f5d6c94b5411db60a89cb4e
    }

// class methods
    function update_status() {
      global $db;
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_EBANX_ZONE > 0) ) {
        $check_flag = false;
        $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_EBANX_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while (!$check->EOF) {
          if ($check->fields['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
          $check->MoveNext();
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
<<<<<<< HEAD
      //global $order;
=======
      global $order;
>>>>>>> 907a771f5e8f0ecf9f5d6c94b5411db60a89cb4e
      // $fields = array();
      // $fields[] = ('title' => 'EITA',
      //              'field' => zen_draw_input_field('email_address', $account->fields['customers_email_address'], 'id="email-address"')
      //              );


      return array('id' => $this->code, 'module' => $this->title); //FUNCIONA


    }
    /*

    if(MODULE_PAYMENT_EBANX_BOLETO == 'True'){
    	$selection = array('id' => $this->code,
    						'module' => $this->title,
    						'fields' => array(array('title' => MODULE_PAYMENT_EBANX_TEXT_CUSTOMER_CPF,
    							'field' => zen_draw_radio_field('method', 'value=')	




    }



    if (MODULE_PAYMENT_EBANX_CCARD == 'True'){
    

    for ($i=1; $i<13; $i++) {
      $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B - (%m)',mktime(0,0,0,$i,1,2000)));
    }

    $today = getdate();
    for ($i=$today['year']; $i < $today['year']+10; $i++) {
      $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
    }

    //$onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';

    if ($this->gateway_mode == 'offsite') {
      $selection = array('id' => $this->code,
                         'module' => $this->title);
    } else {
      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => zen_draw_input_field('authorizenet_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'], 'id="'.$this->code.'-cc-owner"' . $onFocus . ' autocomplete="off"'),
                                               'tag' => $this->code.'-cc-owner'),
                                         array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_NUMBER,
                                               'field' => zen_draw_input_field('authorizenet_cc_number', '', 'id="'.$this->code.'-cc-number"' . $onFocus . ' autocomplete="off"'),
                                               'tag' => $this->code.'-cc-number'),
                                         array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_EXPIRES,
                                               'field' => zen_draw_pull_down_menu('authorizenet_cc_expires_month', $expires_month, strftime('%m'), 'id="'.$this->code.'-cc-expires-month"' . $onFocus) . '&nbsp;' . zen_draw_pull_down_menu('authorizenet_cc_expires_year', $expires_year, '', 'id="'.$this->code.'-cc-expires-year"' . $onFocus),
                                               'tag' => $this->code.'-cc-expires-month')));
      if (MODULE_PAYMENT_EBANX_USE_CVV == 'True') {
        $selection['fields'][] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CVV,
                                       'field' => zen_draw_input_field('authorizenet_cc_cvv', '', 'size="4" maxlength="4"' . ' id="'.$this->code.'-cc-cvv"' . $onFocus . ' autocomplete="off"') . ' ' . '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_CVV_HELP) . '\')">' . MODULE_PAYMENT_EBANX_TEXT_POPUP_CVV_LINK . '</a>',
                                       'tag' => $this->code.'-cc-cvv');
      }
    }}
    return $selection;*/
  

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {

      return false;
    }

    function process_button() {


      return false;
    }

    function before_process() {
	/*
    	global $insert_id, $db, $order;
    	$address = $order->customer['email_address'].'-'.session_id();

		require_once 'bitcoin/jsonRPCClient.php';

		$bitcoin = new jsonRPCClient('http://'.MODULE_PAYMENT_BITCOIN_LOGIN.':'.MODULE_PAYMENT_BITCOIN_PASSWORD.'@'.MODULE_PAYMENT_BITCOIN_HOST.'/'); 

    	try {
			$bitcoin->getinfo();
		} catch (Exception $e) {
			$confirmation = array('title'=>'Error: Bitcoin server is down.  Please email system administrator regarding your order after confirmation.');
			return $confirmation;
		}
		
		$address = $bitcoin->getaccountaddress($address);
		$order->info['comments'] .= ' | Payment Address: '.$address.' | ';
		*/
      //print_r('before');
      return false;
    }

    function after_process() {
      //print_r('after');
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      global $db;
      if (!isset($this->_check)) {
        $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EBANX_STATUS'");
        $this->_check = $check_query->RecordCount();
      }
      return $this->_check;
    }

    function install() {
     // global $db;
      global $db, $messageStack;
      if (defined('MODULE_PAYMENT_EBANX_STATUS')) {
        $messageStack->add_session('Ebanx module already installed.', 'error');
        zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=ebanx', 'NONSSL'));
        return 'failed';
      }
     // $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ebanx Module', 'MODULE_PAYMENT_EBANX_STATUS', 'True', 'Do you want to accept Ebanx payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now());");
     // $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_EBANX_TESTMODE', 'True', 'Test Mode?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now());");
    //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Username', 'MODULE_PAYMENT_BITCOIN_LOGIN', 'testing', 'The Username for Bitcoin RPC', '6', '0', now())");
    // $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Integration Key', 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY', 'testing', 'The API Login ID used for the Authorize.net service', '6', '0', now())");
    //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_BITCOIN_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_BITCOIN_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_BITCOIN_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
  
   


		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ebanx', 'MODULE_PAYMENT_EBANX_STATUS', 'False', 'Do you want to accept EBANX payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Integration Key', 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY', '', 'Your EBANX unique integration key', '6', '0', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_EBANX_TESTMODE', '', 'Test Mode?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Installments'   ,   'MODULE_PAYMENT_EBANX_INSTALLMENTS', '', 'Enable Installments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Installments Enabled', 'MODULE_PAYMENT_EBANX_MAXINSTALLMENTS', '6', 'Maximum Installments Number', '6', '0',  now())");
	   	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Installments rate (%)', 'MODULE_PAYMENT_EBANX_INSTALLMENTSRATE', '10',  'Installments Rate', '6',  '0', now())");
	   	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Boleto Method', 'MODULE_PAYMENT_EBANX_BOLETO', 'True',  'Enable Boleto Payment Method?', '6', '0',  'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Credit Card Method', 'MODULE_PAYMENT_EBANX_CCARD', 'True', 'Enable Credit Card Payment Method?', '6',  '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . "	(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function,  date_added) values ('Enable TEF Method', 'MODULE_PAYMENT_EBANX_TEF', 'True', 'Enable TEF Payment Method?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
     	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_EBANX_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
     	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_EBANX_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    }

    function remove() {
      global $db;
      $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      // return array('MODULE_PAYMENT_EBANX_STATUS'
      // 	, 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY'
      // 	, 'MODULE_PAYMENT_EBANX_TESTMODE'
      // 	, 'MODULE_PAYMENT_EBANX_INSTALLMENTS'
      // 	, 'MODULE_PAYMENT_EBANX_MAXINSTALLMENTS'
      // 	, 'MODULE_PAYMENT_EBANX_INSTALLMENTSRATE'
      // 	, 'MODULE_PAYMENT_EBANX_BOLETO'
      // 	, 'MODULE_PAYMENT_EBANX_CCARD'
      // 	, 'MODULE_PAYMENT_EBANX_TEF');

      return array('MODULE_PAYMENT_EBANX_STATUS', 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY', 'MODULE_PAYMENT_EBANX_TESTMODE', 'MODULE_PAYMENT_EBANX_INSTALLMENTS', 'MODULE_PAYMENT_EBANX_MAXINSTALLMENTS', 'MODULE_PAYMENT_EBANX_INSTALLMENTSRATE', 'MODULE_PAYMENT_EBANX_BOLETO', 'MODULE_PAYMENT_EBANX_CCARD', 'MODULE_PAYMENT_EBANX_TEF', 'MODULE_PAYMENT_EBANX_ZONE');
    
    }
  }
 
 ?>
