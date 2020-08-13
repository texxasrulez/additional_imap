<?php
/* Password encryption:
    'rcmail': encrypt passwords by default Roundcube methods.
    'secure': passwords must be re-entered by users.
*/
$config['additional_imap_crypt'] = 'rcmail';

/* password encryption salt (only used for secure encryption) */
// $config['additional_imap_salt'] = 'Utilizes RC Default des_key generated at installation';

/* predefined imap hosts (associated with the domain part of the identity email property) */
$config['additional_imap_external'] = array(
  'gmail.com' => array(
    'host' =>'ssl://imap.gmail.com:993',
    'delimiter' => '/',
    'drafts_mbox' => '[Google Mail]/Drafts',
    'junk_mbox' => '[Google Mail]/Spam',
    'sent_mbox' => '[Google Mail]/Sent Mail',
    'trash_mbox' => '[Google Mail]/Trash',
    'archive_mbox' => '[Google Mail]/Archive',
    'default_folders' => array('INBOX', '[Google Mail]/Archive', '[Google Mail]/Drafts', '[Google Mail]/Spam', '[Google Mail]/Sent Mail', '[Google Mail]/Trash'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'yahoo.com' => array(
    'host' =>'ssl://imap.mail.yahoo.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'hotmail.com' => array(
    'host' =>'ssl://imap-mail.outlook.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'live.com' => array(
    'host' =>'ssl://imap-mail.outlook.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'outlook.com' => array(
    'host' =>'ssl://imap-mail.outlook.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'aol.com' => array(
    'host' =>'ssl://export.imap.aol.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'gmx.com' => array(
    'host' =>'ssl://imap.gmx.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'icloud.com' => array(
    'host' =>'ssl://imap.mail.me.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
  'yandex.com' => array(
    'host' =>'ssl://imap.yandex.com:993',
    'delimiter' => '/',
    'drafts_mbox' => 'INBOX/Drafts',
    'junk_mbox' => 'INBOX/Spam',
    'sent_mbox' => 'INBOX/Sent',
    'trash_mbox' => 'INBOX/Trash',
    'default_folders' => array('INBOX'),
    'readonly' => true, // This prevents field editing & does not make your remote email "Read Only"
  ),
);

/* auto-detect IMAP server */
$config['additional_imap_autodetect'] = true;

/* Cache remote accounts
   NOTE: if you enable this option your database user must have permissions to CREATE and DROP database tables */
$config['additional_imap_cache'] = false;

/* Cache garbage collection
   Remove unused cache tables every x-nd request (randomly) */
$config['additional_imap_gc'] = 100;

?>
