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
3. Locate module **EBANX**.
4. Select module and click "Install".
5. Add the integration key you were given by the EBANX integration team. You will need to use different keys in test and production modes.
6. Change the other settings if needed.
7. Now select module **EBANX Checkout**.
8. Click "Install".
9. Change the other settings if needed.
10. Go to the EBANX Merchant Area, then to **Integration > Merchant Options**.
  1. Change the _Status Change Notification URL_ to:
```
{YOUR_SITE}/ebanx_notification.php
```
  2. Change the _Response URL_ to:
```
{YOUR_SITE}ebanx_return.php
```
11. That's all!

## Changelog
* 1.0.0: first release.
