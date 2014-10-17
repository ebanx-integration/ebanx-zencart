<?php

require ('includes/configure.php');

header('Location: ' . 'http://' . $_SERVER['HTTP_HOST'] . DIR_WS_CATALOG . '/index.php?main_page=checkout_success');
		