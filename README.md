# Aditional IMAP Accounts   

**Check Email from an external IMAP account from within Roundcube**  

Gmail Yahoo and Hotmail are pre-configured. You can add more within the config.inc.php file.  

upload files to `/path_to_roundcube/plugins/additional_imap`  

Import SQL schema to your database located in the SQL directory of this repo.  

Rename `config.inc.php.dist` to `config.inc.php` and edit to suit your needs.  

Enable plugin via config.inc.php with

`$config['plugins'] = array('additional_imap');`

Enable from Settings - Identities Tab  


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

