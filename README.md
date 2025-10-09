# Aditional IMAP Accounts

[![Packagist Downloads](https://img.shields.io/packagist/dt/texxasrulez/additional_imap?style=plastic&logo=packagist&logoColor=white&label=Downloads&labelColor=blue&color=gold)](https://packagist.org/packages/texxasrulez/additional_imap)
[![Packagist Version](https://img.shields.io/packagist/v/texxasrulez/additional_imap?style=plastic&logo=packagist&logoColor=white&label=Version&labelColor=blue&color=limegreen)](https://packagist.org/packages/texxasrulez/additional_imap)
[![Github License](https://img.shields.io/github/license/texxasrulez/additional_imap?style=plastic&logo=github&label=License&labelColor=blue&color=coral)](https://github.com/texxasrulez/additional_imap/LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/texxasrulez/additional_imap?style=plastic&logo=github&label=Stars&labelColor=blue&color=deepskyblue)](https://github.com/texxasrulez/additional_imap/stargazers)
[![GitHub Issues](https://img.shields.io/github/issues/texxasrulez/additional_imap?style=plastic&logo=github&label=Issues&labelColor=blue&color=aqua)](https://github.com/texxasrulez/additional_imap/issues)
[![GitHub Contributors](https://img.shields.io/github/contributors/texxasrulez/additional_imap?style=plastic&logo=github&logoColor=white&label=Contributors&labelColor=blue&color=orchid)](https://github.com/texxasrulez/additional_imap/graphs/contributors)
[![GitHub Forks](https://img.shields.io/github/forks/texxasrulez/additional_imap?style=plastic&logo=github&logoColor=white&label=Forks&labelColor=blue&color=darkorange)](https://github.com/texxasrulez/additional_imap/forks)
[![Donate Paypal](https://img.shields.io/badge/Paypal-Money_Please!-blue.svg?style=plastic&labelColor=blue&color=forestgreen&logo=paypal)](https://www.paypal.me/texxasrulez)

**Check Email from an external IMAP account from within Roundcube**   

Supported Webmail Providers "out of the box"  
* gmail.com (Tested - Works)  
* googlemail.com (Un-Tested)  
* yahoo.com (Tested - Works. Does require app password from Yahoo to use here not your account password)  
* hotmail.com (Tested - Works)  
* live.com (Un-Tested)  
* outlook.com (Un-Tested)  
* aol.com (Un-Tested)  
* gmx.com (Un-Tested)  
* icloud.com (Un-Tested)  
* yandex.com (Un-Tested)  
  
If you would like more, just give me the url of the webmail provider and I will add it if able to.  

**Installation**  
Easy Way (Recommended)  
`composer require texxasrulez/additional_imap`  
Composer does its thang, puts files where they need to be, injects SQL schema, adds plugin to main RC config.inc.php  
and creates config.inc.php in plugin/additional_imap directory.  
You just need to edit config.inc.php file if needed to suit your individual needs.  

Not as Easy (Old School)
upload files to `/path_to_roundcube/plugins/additional_imap`  

Import SQL schema to your database located in the SQL directory of this repo.  

Rename `config.inc.php.dist` to `config.inc.php` and edit to suit your needs.  

Enable plugin via config.inc.php with

`$config['plugins'] = array('additional_imap');`

Enable from Settings - Identities Tab  
Add a new identity with your webmail provided email address, fill in username and password, check that box to enable, save. Done!  

Screenshots
-----------

Gmail  
![Alt text](/screenshots/gmail_additional_imap.png?raw=true "GMail Inbox")

Yahoo Mail  
![Alt text](/screenshots/yahoo_additional_imap.png?raw=true "Yahoo Mail Inbox")

Hotmail  
![Alt text](/screenshots/hotmail_additional_imap.png?raw=true "Hotmail Inbox")


Enjoy!  

:moneybag: **Donations** :moneybag:  

If you use this plugin and would like to show your appreciation by buying me a cup of coffee, I surely would appreciate it. A regular cup of Joe is sufficient, but a Starbucks Coffee would be better ... \
Zelle (Zelle is integrated within many major banks Mobile Apps by default) - Just send to texxasrulez at yahoo dot com \
No Zelle in your banks mobile app, no problem, just click [Paypal](https://paypal.me/texxasrulez?locale.x=en_US) and I can make a Starbucks run ...

I appreciate the interest in this plugin and hope all the best ...

