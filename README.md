# EBANX Zen Cart Payment Gateway Extension

This plugin allows you to integrate your Zen Cart store with the EBANX payment gateway.
It includes support to installments and custom interest rates.

## Requirements

* PHP >= 5.3
* cURL
* Zen Cart >= 1.5.1

## Installation
### Source
1. Clone the git repo to your Zen Cart root folder
```
git clone --recursive https://github.com/ebanx/zencart.git
```
2. Visit your Zen Cart payment settings at **Modules > Payment**.
3. If you wish to accept credit card payments from Brazil through EBANX, locate module **EBANX**and click "Install".
4. Add the integration key you were given by the EBANX integration team. You will need to use different keys in test and production modes.
5. Change the other settings if needed.
6. Now select module **EBANX Checkout** if you wish to accept "Boleto Bancário" and "TEF" payments.
7. Click "Install".
8. Change the other settings if needed.
9. Go to the EBANX Merchant Area, then to **Integration > Merchant Options**.
  1. Change the _Status Change Notification URL_ to:
```
{YOUR_SITE}/ebanx_notification.php
```
  2. Change the _Response URL_ to:
```
{YOUR_SITE}ebanx_return.php
```
10. That's all!

## Changelog
* 1.0.0: first release.
