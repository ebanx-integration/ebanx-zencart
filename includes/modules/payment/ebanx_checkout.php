<?php


require_once 'ebanx/ebanx-php-master/src/autoload.php';

ini_set('display_errors', -1);
error_reporting(E_ALL ^ E_NOTICE);

//require_once(IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES)
class ebanx_checkout extends base {

    var $code, $title, $description, $enabled, $payment, $checkoutURL, $status;
    //var $installments = array();

// class constructor
    function ebanx_checkout() {
      global $order;
      $this->code = 'ebanx_checkout';
      $this->title = MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_EBANX_CHECKOUT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_EBANX_CHECKOUT_STATUS == 'True') ? true : false);

      // if ((int)MODULE_PAYMENT_EBANX_ORDER_STATUS_ID > 0) {
      //   $this->order_status = MODULE_PAYMENT_EBANX_ORDER_STATUS_ID;
      //   $payment='ebanx';
      // } else {
      //   if ($payment=='ebanx') {
      //     $payment='';
      //   }
      // }

      if (is_object($order)) $this->update_status();

       //$this->email_footer = MODULE_PAYMENT_BEBANX_TEXT_EMAIL_FOOTER;

      // if (is_object($order)) $this->update_status();

      // $this->email_footer = MODULE_PAYMENT_BEBANX_TEXT_EMAIL_FOOTER;

      // if(isset($check_query))
      // {
      //   $integrationKey = $check_query->fields['configuration_value'];
      //   $db->Execute("update" . TABLE_CONFIGURATION . " set configuration_value = " . $integrationKey . "where configuration_key = MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY");
      
      // }

    }

// class methods
    function update_status() {
      global $db;
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_EBANX_CHECKOUT_ZONE > 0) ) {
        $check_flag = false;
        $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_EBANX_CHECKOUT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

      $fieldsArray = array();

      $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CUSTOMER_CPF,
                             'field' => zen_draw_input_field('ebanxb_cpf', '',
                             'id="'.$this->code.'-cpf"'. $onFocus),
                             'tag' => $this->code.'-cpf');

      $selection = array('id' => $this->code,
             'module' => MODULE_PAYMENT_EBANX_CHECKOUT_TEXT_CATALOG_TITLE,
             'fields' => $fieldsArray);

      //global $order;

    // global $order;

    //     for ($i=1; $i<13; $i++) {
    //       $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
    //     }

    //     $today = getdate();
    //     for ($i=$today['year']; $i < $today['year']+10; $i++) {
    //       $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
    //     }
    //     $onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';

    //     $fieldsArray = array();

    //     $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_OWNER,
    //                            'field' => zen_draw_input_field('ebanx_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'],
    //                            'id="'.$this->code.'-cc-owner"'. $onFocus),
    //                            'tag' => $this->code.'-cc-owner');
    //     $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_NUMBER,
    //                            'field' => zen_draw_input_field('ebanx_cc_number', '',
    //                            'id="'.$this->code.'-cc-number"' . $onFocus),
    //                            'tag' => $this->code.'-cc-number');
    //     $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_EXPIRES,
    //                            'field' => zen_draw_pull_down_menu('ebanx_cc_expires_month', $expires_month, '',
    //                'id="'.$this->code.'-cc-expires-month"' . $onFocus) . '&nbsp;' . zen_draw_pull_down_menu('ebanx_cc_expires_year', $expires_year, '',
    //                            'id="'.$this->code.'-cc-expires-year"' . $onFocus),
    //                            'tag' => $this->code.'-cc-expires-month');

       
    //     $fieldsArray[]= array('title' => MODULE_PAYMENT_EBANX_TEXT_CVV,
    //                            'field' => zen_draw_input_field('ebanx_cc_cvv','', 'size="4", maxlength="4" ' .
    //                            'id="'.$this->code.'-cc-cvv"' . $onFocus) . ' ' . '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_CVV_HELP) . '\')">' . MODULE_PAYMENT_EBANX_TEXT_POPUP_CVV_LINK. '</a>',
    //                            'tag' => $this->code.'-cc-cvv');
                        
    //                  //array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES,

        


    //     if (MODULE_PAYMENT_EBANX_INSTALLMENTS == 'True') {

    //       for ($i=0; $i < $this->num_installments; $i++) {

          
    //         $installments[$i] = array('id' => $i+1, 'text' => $i+1 );   //sprintf('%02d', $i)
          
    //     }


          


    //       $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_INSTALLMENTS,
    //                                              'field' => zen_draw_pull_down_menu('ebanx_installments', $installments, '', 'id="'.$this->code.'-ebanx-cc-installments"' .  $onFocus . ' autocomplete="on"'),
    //                                              'tag' => $this->code.'-ebanx-cc-installments');
    //     } 

        

    //           $selection = array('id' => $this->code,
    //                        'module' => MODULE_PAYMENT_EBANX_TEXT_CATALOG_TITLE,
    //                        'fields' => $fieldsArray);

    
        
    //     return $selection;

      return $selection;

   // return false;
    }
  
    function pre_confirmation_check() {
      global $db, $_POST, $messageStack;

      if (!$this->validaCPF($_POST['ebanxb_cpf'])){

          $payment_error_return = 'payment_error=' . $this->code . '&ebanx_cpf=' . $_POST['ebanx_cpf'];
          $messageStack->add_session('checkout_payment', 'CPF Inválido!');
          zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));

      }

      return false;
    }

    function confirmation() {
    // global $order;

    // $fieldsArray = array();

    // $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_CREDIT_CARD_OWNER,
    //                                            'field' => $_POST['ebanx_cc_owner']);


    // if (isset($_POST['ebanx_installments'])) {
    //       $fieldsArray[] = array('title' => MODULE_PAYMENT_EBANX_TEXT_INSTALLMENTS,
    //                                            'field' => $_POST['ebanx_installments']);
    // }

    //     $confirmation = array(//'title' => MODULE_PAYMENT_PLUGNPAY_API_TEXT_CATALOG_TITLE,
    //                       'fields' => $fieldsArray);

    // return $confirmation;

      return false;
      
    }

    function process_button() {

        $process_button_string .= zen_draw_hidden_field('customerb_cpf', $_POST['ebanxb_cpf']);
        return $process_button_string;
    }

    function before_process() {
      global $_POST,  $order, $sendto, $currency, $charge,$db, $messageStack;


      $returnURL = zen_href_link('ebanx_return.php', '', 'SSL', false, false, true);
      $callbackURL = zen_href_link('ebanx_notification.php', '', 'SSL', false, false, true);
      $streetNumber = preg_replace('/[\D]/', '', $order->billing['street_address']);
      $streetNumber = ($streetNumber > 0) ? $streetNumber : '1';


    
      
      \Ebanx\Config::set(array(
          'integrationKey' => MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY
         
         ,'testMode'       => MODULE_PAYMENT_EBANX_CHECKOUT_TESTMODE
      ));
      
      if($order->billing['country']['title'] == 'Brazil'){
        $order->billing['country']['title'] = 'BR';
      }

      if($order->billing['country']['title'] == 'Peru'){
        $order->billing['country']['title'] = 'PE';
      }

      $last_order_id = $db->Execute("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");
      $new_order_id = $last_order_id->fields['orders_id'];
      $new_order_id = ($new_order_id + 1);
      

      $request = \Ebanx\Ebanx::doRequest(array(
          'currency_code'     =>  $order->info['currency']
        , 'amount'            =>  $order->info['total']
        , 'name'              =>  $order->billing['firstname'] . ' ' . $order->billing['lastname']
        , 'email'             =>  $order->customer['email_address']
        , 'payment_type_code' =>  '_all'
        , 'merchant_payment_code' => $new_order_id
        , 'country'           => $order->billing['country']['title']

      )); 

      $this->status = $request->status;


      if($this->status == 'SUCCESS'){
          $this->checkoutURL = $request->redirect_url;
      }

      return false;
    }

    function after_process() {

      global $messageStack;


      if($this->status == 'SUCCESS'){
        unset($_SESSION['cart']);
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

    function get_error() {
      return false;
    }

    function check() {
      global $db;
      //var_dump(MODULE_PAYMENT_EBANX_CHECKOUT_STATUS);
      if (!isset($this->_check)) {
        $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EBANX_CHECKOUT_STATUS'");
        $this->_check = $check_query->RecordCount();
      }
  
      return $this->_check;
    }

    function install() {
    $integrationKey = 0;
     // global $db;
      global $db, $messageStack;
      if (defined('MODULE_PAYMENT_EBANX_CHECKOUT_STATUS')) {
        $messageStack->add_session('Ebanx Checkout module already installed.', 'error');
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
        



      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " c where c.configuration_key = 'MODULE_PAYMENT_EBANX_INTEGRATIONKEY'");
      
      if(isset($check_query))
      {
        $integrationKey = $check_query->fields['configuration_value'];
      }


  	  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ebanx Checkout', 'MODULE_PAYMENT_EBANX_CHECKOUT_STATUS', 'True', 'Do you want to accept EBANX Boleto and TEF payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Integration Key', 'MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY', '". $integrationKey . "', 'Your EBANX unique integration key', '6', '0', now())");
		  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_EBANX_CHECKOUT_TESTMODE', 'True', 'Test Mode?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
     	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_EBANX_CHECKOUT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
      //$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_EBANX_CHECKOUT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    

        // var_dump($integrationKey);
        // die;
        //$db->Execute("update " . TABLE_CONFIGURATION . " set configuration_value = " . $integrationKey . " where configuration_key = 'MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY' ");
      


      // if(isset($check_query))
      // {
      //   $integrationKey = $check_query->fields['configuration_value'];
      //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_value) values (" . $integrationKey . ") where configuration_key = MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY");
      
      // }

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
      // 	, 'MODULE_PAYMENT_EBANX_CHECKOUT'
      // 	, 'MODULE_PAYMENT_EBANX_CCARD'
      // 	, 'MODULE_PAYMENT_EBANX_TEF');

      return array('MODULE_PAYMENT_EBANX_CHECKOUT_STATUS', 'MODULE_PAYMENT_EBANX_CHECKOUT_INTEGRATIONKEY', 'MODULE_PAYMENT_EBANX_CHECKOUT_TESTMODE', 'MODULE_PAYMENT_EBANX_CHECKOUT_ZONE');
    
    }
  
 
    function validaCPF($cpf)
    { // Verifiva se o número digitado contém todos os digitos
        $cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
      
      // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999')
      {
      return false;
        }
      else
      {   // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
     
                $d = ((10 * $d) % 11) % 10;
     
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
     
            return true;
        }
    }
  }
 ?>