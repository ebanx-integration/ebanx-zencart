<?php


require_once 'ebanx/ebanx-php-master/src/autoload.php';
//setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");

ini_set('display_errors', -1);
error_reporting(E_ALL ^ E_NOTICE);

//require_once(IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES)
class ebanx extends base {

    var $code, $title, $description, $enabled, $payment;
    //var $installments = array();

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

      if(MODULE_PAYMENT_EBANX_INSTALLMENTS == 'True'){
        $this->num_installments = MODULE_PAYMENT_EBANX_MAXINSTALLMENTS;
      }

      if (is_object($order)) $this->update_status();

       //$this->email_footer = MODULE_PAYMENT_BEBANX_TEXT_EMAIL_FOOTER;

      // if (is_object($order)) $this->update_status();

      // $this->email_footer = MODULE_PAYMENT_BEBANX_TEXT_EMAIL_FOOTER;



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

      //global $order;

  global $order;

      for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%m',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate();
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
      $onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';

      $fieldsArray = array();

      $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CUSTOMER_CPF,
                             'field' => zen_draw_input_field('ebanx_cpf', '',
                             'id="'.$this->code.'-cpf"'. $onFocus),
                             'tag' => $this->code.'-cpf');

      $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_OWNER,
                             'field' => zen_draw_input_field('ebanx_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'],
                             'id="'.$this->code.'-cc-owner"'. $onFocus),
                             'tag' => $this->code.'-cc-owner');
      $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_NUMBER,
                             'field' => zen_draw_input_field('ebanx_cc_number', '',
                             'id="'.$this->code.'-cc-number"' . $onFocus),
                             'tag' => $this->code.'-cc-number');
      $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_EXPIRES,
                             'field' => zen_draw_pull_down_menu('ebanx_cc_expires_month', $expires_month, '',
                 'id="'.$this->code.'-cc-expires-month"' . $onFocus) . '&nbsp;' . zen_draw_pull_down_menu('ebanx_cc_expires_year', $expires_year, '',
                             'id="'.$this->code.'-cc-expires-year"' . $onFocus),
                             'tag' => $this->code.'-cc-expires-month');

     
      $fieldsArray[]= array('title' => MODULE_PAYMENT_EBANX_TEXT_CVV,
                             'field' => zen_draw_input_field('ebanx_cc_cvv','', 'size="4", maxlength="4" ' .
                             'id="'.$this->code.'-cc-cvv"' . $onFocus) . ' ' . '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_CVV_HELP) . '\')">' . MODULE_PAYMENT_EBANX_TEXT_POPUP_CVV_LINK. '</a>',
                             'tag' => $this->code.'-cc-cvv');
                      
                   //array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,

      


      if (MODULE_PAYMENT_EBANX_INSTALLMENTS == 'True') {

        for ($i=0; $i < $this->num_installments; $i++) {

        
          $installments[$i] = array('id' => $i+1, 'text' => $i+1 );   //sprintf('%02d', $i)
        
      }


        


        $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_INSTALLMENTS,
                                               'field' => zen_draw_pull_down_menu('ebanx_installments', $installments, '', 'id="'.$this->code.'-ebanx-cc-installments"' .  $onFocus . ' autocomplete="on"'),
                                               'tag' => $this->code.'-ebanx-cc-installments');
      } 

      

            $selection = array('id' => $this->code,
                         'module' => MODULE_PAYMENT_EBANX_TEXT_CATALOG_TITLE,
                         'fields' => $fieldsArray);

  
      
      return $selection;

      //return array('id' => $this->code, 'module' => $this->title); //FUNCIONA


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
    global $db, $_POST, $messageStack;
    include(DIR_WS_CLASSES . 'cc_validation.php');

    $cc_validation = new cc_validation();
    $result = $cc_validation->validate($_POST['ebanx_cc_number'], $_POST['ebanx_cc_expires_month'], $_POST['ebanx_cc_expires_year'], $_POST['ebanx_cc_cvv']);
    $error = '';
    switch ($result) {
      case -1:
        $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
        break;
      case -2:
      case -3:
      case -4:
        $error = TEXT_CCVAL_ERROR_INVALID_DATE;
        break;
      case false:
        $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
        break;
    }
        if (($result == false) || ($result < 1)) {
      $payment_error_return = 'payment_error=' . $this->code . '&ebanx_cc_owner=' . urlencode($_POST['ebanx_cc_owner']) . '&ebanx_cc_expires_month=' . $_POST['ebanx_cc_expires_month'] . '&ebanx_cc_expires_year=' . $_POST['ebanx_cc_expires_year'];
      $messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }

    //$this->cc_card_type = $cc_validation->cc_type;
    $this->cc_card_number = $cc_validation->cc_number;
    $this->cc_expiry_month = $cc_validation->cc_expiry_month;
    $this->cc_expiry_year = $cc_validation->cc_expiry_year;
  }

    function confirmation() {
    global $order;

    $fieldsArray = array();

    $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_OWNER,
                                               'field' => $_POST['ebanx_cc_owner']);

    $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_NUMBER,
                           'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4));


    if (isset($_POST['ebanx_installments'])) {
          $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_INSTALLMENTS,
                                               'field' => $_POST['ebanx_installments']);
    }

        $confirmation = array(//'title' => MODULE_PAYMENT_PLUGNPAY_API_TEXT_CATALOG_TITLE,
                          'fields' => $fieldsArray);

    return $confirmation;
      
    }

    function process_button() {

    $process_button_string = zen_draw_hidden_field('cc_owner', $_POST['ebanx_cc_owner']) .
    zen_draw_hidden_field('cc_expires', $this->cc_expiry_month . substr($this->cc_expiry_year, -2)) .
    //zen_draw_hidden_field('cc_type', $this->cc_card_type) .
    zen_draw_hidden_field('cc_number', $this->cc_card_number);
    
    $process_button_string .= zen_draw_hidden_field('cc_cvv', $_POST['ebanx_cc_cvv']);
    $process_button_string .= zen_draw_hidden_field('customer_cpf', $_POST['ebanx_cpf']);

    //$process_button_string .= zen_draw_hidden_field(zen_session_name(), zen_session_id());
    echo $process_button_string;
    return $process_button_string;


  

      //return false;
    }

    function before_process() {
      global $_POST,  $order, $sendto, $currency, $charge,$db, $messageStack;


      
        // Calculate the next expected order id
        \Ebanx\Config::set(array(
            'integrationKey' => MODULE_PAYMENT_EBANX_INTEGRATIONKEY
           
           ,'testMode'       => MODULE_PAYMENT_EBANX_TESTMODE
        ));


          $last_order_id = $db->Execute("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");
          $new_order_id = $last_order_id->fields['orders_id'];
          $new_order_id = ($new_order_id + 1);


      $submit = array(
         'integration_key' => MODULE_PAYMENT_EBANX_INTEGRATIONKEY
        ,'operation'       => 'request'
        ,'mode'            => 'full'
        ,'payment'         => array(
                                    'merchant_payment_code' => $new_order_id //. time()
                                   ,'currency_code'         => $order->info['currency']
                                   ,'name'  => $order->billing['firstname'] . $order->billing['lastname']
                                   ,'email' => $order->customer['email_address']
                                   ,'birth_date' => $order->customer['birth_date']
                                   ,'document'   => $_POST['customer_cpf']
                                   ,'city'       => $order->billing['city']
                                   ,'state'      => $order->billing['state']
                                   ,'zipcode'    => $order->billing['postcode']
                                   ,'country'    => $order->billing['country']['title']
                                   ,'phone_number' => $order->customer['telephone']
                                   ,'address'      => $order->billing['street_address']
                                   ,

          )


        )    $this->cc_card_number = $cc_validation->cc_number;
             $this->cc_expiry_month = $cc_validation->cc_expiry_month;
              $this->cc_expiry_year = $cc_validation->cc_expiry_year;

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
