<?php
class additional_imap extends rcube_plugin
{
    var $task = '?(?!login|logout).*';
    private $rcmail;

    function init() {
        $this->rcmail = rcmail::get_instance();
        $this->load_config();
        $this->register_action('plugin.additional_imap', array($this, 'switch_account'));
        $this->register_action('plugin.additional_imap_uninstall', array($this, 'uninstall'));
        $this->add_hook('startup', array($this, 'startup'));
        $this->add_hook('render_page', array($this, 'render_page'));
        $this->add_hook('message_outgoing_headers', array($this, 'message_outgoing_headers'));
        $this->add_hook('smtp_connect', array($this, 'smtp_connect'));
        $this->add_hook('template_object_userprefs', array($this, 'userprefs'));
        $this->add_hook('preferences_save', array($this, 'preferences_save'));
        $this->add_hook('preferences_update', array($this, 'preferences_update'));
        $this->add_hook('identity_form', array($this, 'identity_form'));
        $this->add_hook('identity_update', array($this, 'identity_update'));
        $this->add_hook('identity_delete', array($this, 'identity_delete'));
        $this->add_hook('identity_select', array($this, 'identity_select'));
        $this->add_hook('password_change', array($this, 'password_change'));
        $this->add_hook('config_get', array($this, 'config_get'));
    }

    function switch_account() {
        $rcmail = $this->rcmail;
        if ($_ = rcube_utils::get_input_value('_switch', rcube_utils::INPUT_GET)) {
            $_SESSION['additional_imap_id'] = $_;
            if ($_ == -1) {
                $imap_menu = array('storage_host', 'storage_ssl', 'storage_port');
                foreach($imap_menu as $E) {
                    $_SESSION[$E] = $_SESSION[$E.
                        '_def'];
                }
                $BB = $_SESSION;
                foreach($BB as $E => $L) {
                    if (substr($E, 0, strlen('STORAGE')) == 'STORAGE' && substr($E, strlen($E) - 4) == '_sav') {
                        $_SESSION[$E] = $_SESSION[$E.
                            '_sav'];
                        $rcmail->session->remove($E.
                            '_sav');
                    }
                }
                $_SESSION['username'] = $rcmail->user->data['username'];
                $_SESSION['password'] = $_SESSION['default_account_password'];
                $_SESSION['imap_delimiter'] = $_SESSION['imap_delimiter_def'];
                $rcmail->output->redirect(array('_task' => 'mail', '_mbox' => rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_GET)));
            } else {
                $C = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('additional_imap').
                ' WHERE id=? AND user_id=?';
                $res = $rcmail->db->query($C, $_, $rcmail->user->ID);
                $sql_arr = $rcmail->db->fetch_assoc($res);
                if (is_array($sql_arr)) {
                    $fB = $sql_arr['server'];
                    $F = parse_url($fB);
                    $KB = false;
                    if ($F['scheme'] == 'ssl' || $F['scheme'] == 'tls') {
                        $KB = true;
                        $F['port'] = $F['port'] ? $F['port'] : 993;
                    }
                    $NB = $F['port'] ? $F['port'] : 143;
                    $M = $this->decrypt($sql_arr['password'], $rcmail->decrypt($_SESSION['password']), $rcmail->config->get('additional_imap_salt', '%E`c{2;<J2F^4_&._BxfQ<5Pf3qv!m{e'));
                    $c = $F['scheme'] ? $F['scheme'] : false;
                    $j = $F['host'] ? $F['host'] : $F['path'];
                    if ($this->test_connection($sql_arr['username'], $M, $j, $NB, $c)) {
                        if ($rcmail->config->get('additional_imap_cache')) {
                            $x = array('cache', 'cache_index', 'cache_messages', 'cache_shared', 'cache_thread');
                            $W = asciiwords($j, true, '_');
                            $C = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('cache_tables').
                            ' WHERE suffix=?';
                            $res = $rcmail->db->limitquery($C, 0, 1, $W);
                            $iB = $rcmail->db->fetch_assoc($res);
                            if (is_array($iB)) {
                                $C = 'UPDATE ' . rcmail::get_instance()->db->table_name('cache_tables').
                                ' SET ts=? WHERE suffix=?';
                                $rcmail->db->query($C, time(), $W);
                            } else {
                                $C = 'INSERT INTO ' . rcmail::get_instance()->db->table_name('cache_tables').
                                ' (suffix, ts) VALUES (?, ?)';
                                $rcmail->db->query($C, $W, time());
                                $lB = $rcmail->config->get('db_dsnw');
                                $CB = strtolower(current(explode(':', $lB, 2)));
                                $CB = self::$db_map[$CB];
                                if ($FB = file_get_contents(INSTALL_PATH.
                                        'plugins/additional_imap/'.$CB.
                                        '.tpl')) {
                                    $FB = str_replace('##host##', $W, $FB);
                                    $oB = explode(';', $FB);
                                    foreach($oB as $y) {
                                        $y = trim($y);
                                        if ($y) {
                                            $rcmail->db->query($y);
                                        }
                                    }
                                }
                            }
                        }
                        if ($_SESSION['username'] == $rcmail->user->data['username']) $_SESSION['storage_host_sav'] = $_SESSION['storage_host'];
                        $_SESSION['storage_host'] = $j;
                        if ($_SESSION['username'] == $rcmail->user->data['username']) $_SESSION['storage_ssl_sav'] = $_SESSION['storage_ssl'];
                        $_SESSION['storage_ssl'] = $KB;
                        if ($_SESSION['username'] == $rcmail->user->data['username']) $_SESSION['storage_port_sav'] = $_SESSION['storage_port'];
                        $_SESSION['storage_port'] = $NB;
                        $_SESSION['username'] = $sql_arr['username'];
                        $_SESSION['password'] = $rcmail->encrypt($M);
                        $BB = $_SESSION;
                        foreach($BB as $E => $L) {
                            if (substr($E, 0, strlen('STORAGE')) == 'STORAGE' && substr($E, strlen($E) - 4) != '_sav') {
                                $_SESSION[$E.
                                    '_sav'] = $_SESSION[$E];
                                $rcmail->session->remove($E);
                            }
                        }
                        $rcmail->session->remove('folders');
                        $rcmail->output->redirect(array('_task' => 'mail', '_mbox' => rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_GET)));
                    } else {
                        $this->add_texts('localization/');
                        $rcmail->output->show_message('additional_imap.connectionfailed', 'error');
                        $rcmail->output->add_script('rcmail.set_busy(true, "loading");window.setTimeout("document.location.href=\'./\'", 6000);', 'docready');
                    }
                }
            }
        }
    }

    function startup($B) {
        if ($B['task'] == 'mail' || $B['task'] == 'settings') {
            if (!$_SESSION['storage_host_def']) {
                $_SESSION['storage_host_def'] = $_SESSION['storage_host'];
                $_SESSION['storage_ssl_def'] = $_SESSION['storage_ssl'];
                $_SESSION['storage_port_def'] = $_SESSION['storage_port'];
                $_SESSION['imap_delimiter_def'] = $_SESSION['imap_delimiter'];
                $_SESSION['default_account_password'] = $_SESSION['password'];
            }
            if (strtolower($this->rcmail->user->data['username']) != strtolower($_SESSION['username'])) {
                if ($B['task'] == 'mail') {
                    $this->add_texts('localization/');
                    $this->rcmail->config->set('create_default_folders', false);
                }
                $O = strtolower(end(explode('@', $_SESSION['username'])));
                $N = $this->rcmail->config->get('additional_imap_external', array());
                if (isset($N[$O])) {
                    if ($jB = $N[$O]['default_folders']) {
                        $this->rcmail->config->set('default_folders', $jB);
                    }
                    if (isset($N[$O]['junk_mbox'])) {
                        $this->rcmail->config->set('junk_mbox', $N[$O]['junk_mbox']);
                    }
                    if (isset($N[$O]['sent_mbox'])) {
                        $this->rcmail->config->set('sent_mbox', $N[$O]['sent_mbox']);
                    }
                    if (isset($N[$O]['trash_mbox'])) {
                        $this->rcmail->config->set('trash_mbox', $N[$O]['trash_mbox']);
                    }
                    if (isset($N[$O]['drafts_mbox'])) {
                        $this->rcmail->config->set('drafts_mbox', $N[$O]['drafts_mbox']);
                    }
                    if (isset($N[$O]['sticky_notes_imap_folder'])) {
                        $this->rcmail->config->set('sticky_notes_imap_folder', $N[$O]['sticky_notes_imap_folder']);
                    }
                    if (isset($N[$O]['archive_mbox'])) {
                        $this->rcmail->config->set('archive_mbox', $N[$O]['archive_mbox']);
                    }
                }
                $EB = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('additional_imap').
                ' WHERE user_id=? AND username=?';
                $C = $this->rcmail->db->query($EB, $this->rcmail->user->ID, $_SESSION['username']);
                $X = $this->rcmail->db->fetch_assoc($C);
                if ($X) {
                    $X = unserialize($X['preferences']);
                }
                if (is_array($X)) {
                    foreach($X as $E => $L) {
                        $this->rcmail->config->set($E, $L);
                        if ($E == 'imap_delimiter') {
                            $_SESSION['imap_delimiter'] = $L;
                        }
                    }
                }
            }
        }
        if (strtolower($this->rcmail->user->data['username']) != strtolower($_SESSION['username'])) {
            if ($this->rcmail->config->get('additional_imap_cache')) {
                $this->rcmail->config->set('imap_cache', 'db');
                $this->rcmail->config->set('messages_cache', 'db');
                $W = asciiwords($_SESSION['storage_host'], true, '_');
                $this->gc($W);
                $x = array('cache', 'cache_index', 'cache_messages', 'cache_shared', 'cache_thread');
                foreach($x as $d) {
                    $this->rcmail->config->set('db_table_'.$d, $d.
                        '_'.$W);
                }
            } else {
                $this->rcmail->config->set('imap_cache', null);
                $this->rcmail->config->set('messages_cache', null);
            }
        }
        return $B;
    }

    function message_outgoing_headers($B) {
        if (strtolower($this->rcmail->user->data['username']) != strtolower($_SESSION['username'])) {
            $dB = rcube_utils::get_input_value('_store_target', rcube_utils::INPUT_POST);
            if (!$this->rcmail->storage->folder_exists($dB)) {
                $_POST['_store_target'] = $this->rcmail->config->get('sent_mbox', 'Sent');
            }
        }
        return $B;
    }

    function render_page($args) {
        $this->include_script('additional_imap.js');
        if (rcube_utils::get_input_value('_framed', rcube_utils::INPUT_GPC) || $_SESSION['impersonate']) {
            return $args;
        }
        $rcmail = $this->rcmail;
        $g = true;
        if (($rcmail->config->get('skin') == 'classic' && $args['template'] != 'mail')) {
            $g = false;
        }
        $b = $this->get_sorted_list();
        if (is_array($b) && count($b) > 0) {
            $this->include_stylesheet($this->local_skin_path().
                '/additional_imap.css');
            $f = array();
            $rc_uname = $rcmail->user->data['username'];
            $eB = (array) $rcmail->user->list_identities();
            foreach($eB as $v => $R) {
                if (strtolower($rc_uname) == strtolower($R['email'])) {
                    if ($R['name']) {
                        $rc_uname = $R['name'];
                        break;
                    }
                }
            }
            if (strtolower($_SESSION['username']) == strtolower($rc_uname)) {
                $f = array('selected' => 'selected');
            }
            $pB = $rc_uname;
            if (strlen($rc_uname) > 20) $rc_uname = substr($rc_uname, 0, 20).
            "...";
            $HB = html::tag('option', array_merge(array('value' => -1), $f), $rc_uname);
            foreach($b as $E => $L) {
                if (strtolower($_SESSION['username']) == strtolower($L['username'])) {
                    $f = array('selected' => 'selected');
                } else {
                    $f = array();
                }
                if (strlen($L['label']) > 20) $L['label'] = substr($L['label'], 0, 20).
                "...";
                $HB.= html::tag('option', array_merge(array('value' => $L['id']), $f), $L['label']);
            }
            if (class_exists('tabbed')) {
                $LB = html::tag('select', array('class' => 'deco', 'onchange' => 'switch_account(this.value, "parent")'), $HB);
            } else {
                $LB = html::tag('select', array('class' => 'deco', 'onchange' => 'switch_account(this.value, "self")'), $HB);
            }
            $IB = html::tag('div', array('id' => 'accounts', 'style' => 'display: none'), $LB);
            if ($g) {
                $rcmail->output->add_footer($IB);
            }
            if ((strtolower($this->rcmail->user->data['username']) != strtolower($_SESSION['username'])) && ($rcmail->task == 'mail' || ($rcmail->task == 'settings' && $rcmail->action == 'folders'))) {
                $this->add_texts('localization/');
                $rcmail->output->add_footer(html::tag('div', array('class' => 'remotehint'), $this->gettext('browsingremoteaccount').
                    ' '.$_SESSION['username']));
            }
            if ($this->rcmail->task == 'settings' && $this->rcmail->action == 'identities') {
                $this->add_texts('localization/');
                $gB = html::tag('a', array('href' => 'https://accounts.google.com/DisplayUnlockCaptcha', 'target' => '_blank', 'style' => 'padding:0;height:1px;color:black;display:inline;text-decoration:underline;'), $this->gettext('here'));
                $rcmail->output->add_footer(html::tag('div', array('class' => 'remotehint '.$rcmail->config->get('skin', 'classic'), 'style' => 'display:none;', 'onclick' => 'this.style.display="none";'), sprintf($this->gettext('googlecaptchahint'), $gB)), 'taskbar');
            }
        }
        return $args;
    }

    function identity_select($B) {
        $rcmail = $this->rcmail;
        if (strtolower($rcmail->user->data['username']) != strtolower($_SESSION['username'])) {
            foreach($B['identities'] as $v => $hB) {
                if (strtolower($hB['email_ascii']) == strtolower($_SESSION['username'])) {
                    $B['selected'] = $v;
                    break;
                }
            }
        }
        return $B;
    }

    function smtp_connect($B) {
        $rcmail = $this->rcmail;
        if (strtolower($rcmail->user->data['username']) != strtolower($_SESSION['username'])) {
            if ($B['smtp_user'] == '%u') {
                $B['smtp_user'] = $rcmail->user->data['username'];
            }
            if ($B['smtp_pass'] == '%p') {
                $B['smtp_pass'] = $rcmail->decrypt($_SESSION['default_account_password']);
            }
        }
        return $B;
    }

    function password_change($B) {
        $rcmail = $this->rcmail;
        $_SESSION['default_account_password'] = $rcmail->encrypt($B['new_pass']);
        if ($rcmail->config->get('additional_imap_crypt') == 'secure') {
            $C = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('additional_imap').
            ' WHERE user_id=?';
            $res = $rcmail->db->query($C, $rcmail->user->ID);
            $MB = array();
            while ($args = $rcmail->db->fetch_assoc($res)) {
                $MB[] = $args;
            }
            foreach($MB as $args) {
                $M = $this->decrypt($args['password'], $B['old_pass'], $rcmail->config->get('additional_imap_salt', '%E`c{2;<J2F^4_&._BxfQ<5Pf3qv!m{e'));
                $M = $this->encrypt($M, $B['new_pass'], $rcmail->config->get('additional_imap_salt', '%E`c{2;<J2F^4_&._BxfQ<5Pf3qv!m{e'));
                $C = 'UPDATE ' . rcmail::get_instance()->db->table_name('additional_imap').
                ' SET password=? WHERE user_id=? AND iid=?';
                $rcmail->db->query($C, $M, $rcmail->user->ID, $args['iid']);
            }
        }
        return $B;
    }

    function config_get($B) {
        if ($B['name'] == 'keyboard_shortcuts_threads') {
            if (is_array($_SESSION['STORAGE_THREAD'])) {
                $B['result'] = true;
            }
        }
        return $B;
    }

    function userprefs($args) {
        if (rcube_utils::get_input_value('_section', rcube_utils::INPUT_GPC) == "folders") {
            $rc_uname = $_SESSION['username'];
            $args['content'] = str_replace("</legend>", " ::: ".$rc_uname.
                "</legend>", $args['content']);
        }
        return $args;
    }

    function preferences_save($B) {
        $rcmail = $this->rcmail;
        if (strtolower($this->rcmail->user->data['username']) != strtolower($_SESSION['username'])) {
            if ($B['section'] == 'folders') {
                $B['prefs']['archive_mbox'] = rcube_utils::get_input_value('_archive_mbox', rcube_utils::INPUT_POST);
                $B['prefs']['sticky_notes_imap_folder'] = rcube_utils::get_input_value('_notes_mbox', rcube_utils::INPUT_POST);
                $B['prefs']['imap_delimiter'] = $_SESSION['imap_delimiter'];
                $C = 'UPDATE ' . rcmail::get_instance()->db->table_name('additional_imap').
                ' SET preferences=? WHERE user_id=? AND username=?';
                $rcmail->db->query($C, serialize($B['prefs']), $rcmail->user->ID, $_SESSION['username']);
                $rcmail->output->redirect(array('_task' => 'settings', '_action' => 'edit-prefs', '_section' => 'folders', '_framed' => '1'));
                exit;
            }
        }
        return $B;
    }

    function preferences_update($B) {
        if (strtolower($_SESSION['username']) != strtolower($this->rcmail->user->data['username']) && is_array($B['prefs']['message_threading'])) {
            $C = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('additional_imap').
            ' WHERE user_id=? AND id=?';
            $res = $this->rcmail->db->limitquery($C, 0, 1, $this->rcmail->user->ID, $_SESSION['additional_imap_id']);
            $rc_uname = $this->rcmail->db->fetch_assoc($res);
            if (is_array($rc_uname)) {
                if ($rc_uname['preferences']) {
                    $u = unserialize($rc_uname['preferences']);
                } else {
                    $u = array();
                }
                $u = array_merge($u, array('message_threading' => $B['prefs']['message_threading']));
                $C = 'UPDATE ' . rcmail::get_instance()->db->table_name('additional_imap').
                ' SET preferences=? WHERE user_id=? AND id=?';
                $this->rcmail->db->query($C, serialize($u), $this->rcmail->user->ID, $rc_uname['id']);
                $B['abort'] = true;
            }
        }
        return $B;
    }

    function identity_form($B) {
        $rcmail = $this->rcmail;
        if ($rcmail->action == 'edit-identity') {
            $this->add_texts('localization/', true);
            $C = 'SELECT * from ' . rcmail::get_instance()->db->table_name('additional_imap').
            ' WHERE iid=? AND user_id=?';
            $res = $rcmail->db->limitquery($C, 0, 1, $B['record']['identity_id'], $rcmail->user->ID);
            $data = 'data';
            $q = 'data';
            $g = true;
            if ($args = $rcmail->db->fetch_assoc($res)) {
                $sql = $args['server'];
                $K = $args['username'];
                $T = $args['password'];
                $h = $args['label'];
                $imap_menu_B = $args['enabled'] ? true : false;
                $I = unserialize($args['preferences']);
                if (is_array($I)) {
                    $I = $I['imap_delimiter'];
                }
                $R = explode('@', $B['record']['email'], 2);
                $Y = $rcmail->config->get('additional_imap_external', array());
                $Y = array_merge($Y, $rcmail->config->get('additional_imap_internal', array()));
                if ($V = $Y[strtolower($R[1])]) {
                    $sql = $V['host'];
                    if ($V['readonly']) {
                        $data = 'readonly';
                    }
                    if ($V['delimiter']) {
                        $I = $V['delimiter'];
                        $q = 'readonly';
                    }
                }
            } else {
                $K = $B['record']['email'];
                if (strtolower($K) == strtolower($rcmail->user->data['username'])) {
                    $g = false;
                } else {
                    $R = explode('@', $B['record']['email'], 2);
                    $rc_uname = explode('@', $this->rcmail->user->data['username'], 2);
                    $I = '.';
                    $Y = $rcmail->config->get('additional_imap_external', array());
                    $Y = array_merge($Y, $rcmail->config->get('additional_imap_internal', array()));
                    if ($V = $Y[strtolower($R[1])]) {
                        $sql = $V['host'];
                        if ($V['readonly']) {
                            $data = 'readonly';
                        }
                        if ($V['delimiter']) {
                            $I = $V['delimiter'];
                            $q = 'readonly';
                        }
                    } else {
                        $C = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('additional_imap_hosts').
                        ' WHERE domain=?';
                        $i = $rcmail->db->limitquery($C, 0, 1, $R[1]);
                        $i = $rcmail->db->fetch_assoc($i);
                        if (is_array($i) && strtotime($i['ts']) + 86400 * 7 > time()) {
                            $sql = $i['host'];
                        } else {
                            $sql = 'imap.'.$R[1];
                            if ($rcmail->config->get('additional_imap_autodetect', false)) {
                                $S = @fsockopen('ssl://'.$sql, 993, $AB, $DB, 5);
                                if ($S) {
                                    fclose($S);
                                    $sql = 'ssl://'.$sql.
                                    ':993';
                                } else {
                                    $S = @fsockopen($sql, 143, $AB, $DB, 5);
                                    if ($S) {
                                        fclose($S);
                                        $sql = $sql.
                                        ':143';
                                    } else {
                                        if (function_exists('getmxrr')) {
                                            if (@getmxrr($R[1], $bB, $nB)) {
                                                $t = array();
                                                foreach($nB as $v => $kB) {
                                                    $t[$kB] = $bB[$v];
                                                }
                                                ksort($t);
                                                if (!empty($t)) {
                                                    $sql = current($t);
                                                    $S = @fsockopen('ssl://'.$sql, 993, $AB, $DB, 5);
                                                    if ($S) {
                                                        fclose($S);
                                                        $sql = 'ssl://'.$sql.
                                                        ':993';
                                                    } else {
                                                        $S = @fsockopen($sql, 143, $AB, $DB, 5);
                                                        if ($S) {
                                                            $sql = $sql.
                                                            ':143';
                                                        } else {
                                                            $sql = null;
                                                        }
                                                    }
                                                } else {
                                                    $sql = null;
                                                }
                                            } else {
                                                $sql = null;
                                            }
                                        } else {
                                            $sql = null;
                                        }
                                    }
                                }
                                $C = 'DELETE FROM ' . rcmail::get_instance()->db->table_name('additional_imap_hosts').
                                ' WHERE host=?';
                                $rcmail->db->query($C, $sql);
                                $C = 'INSERT INTO ' . rcmail::get_instance()->db->table_name('additional_imap_hosts').
                                ' (domain, host, ts) VALUES (?, ?, ?)';
                                $rcmail->db->query($C, $R[1], $sql, date('Y-m-d H:i:s'));
                            } else {
                                $sql = null;
                            }
                        }
                    }
                }
            }
            if ($g) {
                if (!$I) {
                    $I = '.';
                }
                if ($T) {
                    $JB = $this->gettext('isset');
                } else {
                    $JB = $this->gettext('isnotset');
                }
                $IB = array('additional_imap.enabled' => array('type' => 'checkbox'), 'additional_imap.label' => array('type' => 'text', 'size' => 40), 'additional_imap.imapserver' => array('type' => 'text', 'size' => 40, 'placeholder' => $this->gettext('ie').
                    ' ssl://imap.gmail.com:993', $data => $data), 'additional_imap.imapuser' => array('type' => 'text', 'size' => 40, 'placeholder' => $this->gettext('username')), 'additional_imap.imappass' => array('type' => 'password', 'size' => 40, 'placeholder' => $JB), 'additional_imap.delimiter' => array('type' => 'text', 'size' => 1, 'id' => 'delimiter', $q => $q), );
                $B['form']['imap'] = array('name' => $this->gettext('additional_imap.imap'), 'content' => $IB);
                $B['record']['additional_imap.label'] = $h;
                $B['record']['additional_imap.imapserver'] = $sql;
                $B['record']['additional_imap.imapuser'] = $K;
                $B['record']['additional_imap.enabled'] = $imap_menu_B;
                $B['record']['additional_imap.delimiter'] = $I;
            }
        }
        return $B;
    }

    function identity_update($B) {
        $rcmail = $this->rcmail;
        if ($sql = rcube_utils::get_input_value('_additional_imap_imapserver', rcube_utils::INPUT_POST)) {
            $h = rcube_utils::get_input_value('_additional_imap_label', rcube_utils::INPUT_POST);
            if ($K = rcube_utils::get_input_value('_additional_imap_imapuser', rcube_utils::INPUT_POST)) {
                $K = strtolower($K);
            }
            $Q = rcube_utils::get_input_value('_additional_imap_enabled', rcube_utils::INPUT_POST) ? 1 : 0;
            if ($T = trim(rcube_utils::get_input_value('_additional_imap_imappass', rcube_utils::INPUT_POST, true))) {
                $l = $T;
                if ($Q) {
                    if (!$this->identity_update_test($K, $l, $sql)) {
                        $this->add_texts('localization/');
                        $rcmail->output->show_message("additional_imap.connectionfailed", "error");
                        if (stripos($sql, 'gmail.com') !== false) {
                            $rcmail->output->add_script("parent.$('.remotehint').show();", 'docready');
                        } else {
                            $rcmail->output->add_script("parent.$('.remotehint').hide();", 'docready');
                        }
                        $Q = 0;
                    } else {
                        $rcmail->output->add_script("parent.$('.remotehint').hide();", 'docready');
                    }
                }
                $T = $this->encrypt($T, $rcmail->decrypt($_SESSION['password']), $rcmail->config->get('additional_imap_salt', '%E`c{2;<J2F^4_&._BxfQ<5Pf3qv!m{e'));
            }
            $I = rcube_utils::get_input_value('_delimiter', rcube_utils::INPUT_POST);
            $C = 'SELECT * from ' . rcmail::get_instance()->db->table_name('additional_imap').
            ' WHERE iid=? AND user_id=?';
            $res = $rcmail->db->query($C, $B['id'], $rcmail->user->ID);
            if ($args = $rcmail->db->fetch_assoc($res)) {
                if ($T) {
                    if ($Q) {
                        if (!$this->identity_update_test($K, $l, $sql)) {
                            $this->add_texts('localization/');
                            $rcmail->output->show_message("additional_imap.connectionfailed", "error");
                            if (stripos($sql, 'gmail.com') !== false || stripos($sql, 'googlemail.com') !== false) {
                                $rcmail->output->add_script("parent.$('.remotehint').show()", 'docready');
                            } else {
                                $rcmail->output->add_script("parent.$('.remotehint').hide()", 'docready');
                            }
                            $Q = 0;
                        } else {
                            $rcmail->output->add_script("parent.$('.remotehint').hide()", 'docready');
                        }
                    }
                    $J = unserialize($args['preferences']);
                    if (is_array($J)) {
                        $J['imap_delimiter'] = $I;
                    } else {
                        $J = array('imap_delimiter' => $I);
                    }
                    $J = serialize($J);
                    $C = 'UPDATE ' . rcmail::get_instance()->db->table_name('additional_imap').
                    ' SET username=?, password=?, server=?, enabled=?, label=?, preferences=? WHERE user_id=? AND iid=?';
                    $rcmail->db->query($C, $K, $T, $sql, $Q, $h, $J, $rcmail->user->ID, $B['id']);
                } else {
                    if ($Q) {
                        $C = 'SELECT password FROM ' . rcmail::get_instance()->db->table_name('additional_imap').
                        ' WHERE user_id=? AND iid=?';
                        $res = $rcmail->db->query($C, $rcmail->user->ID, $B['id']);
                        $M = $rcmail->db->fetch_assoc($res);
                        $l = $this->decrypt($M['password'], $rcmail->decrypt($_SESSION['password']), $rcmail->config->get('additional_imap_salt', '%E`c{2;<J2F^4_&._BxfQ<5Pf3qv!m{e'));
                        if (!$this->identity_update_test($K, $l, $sql)) {
                            $this->add_texts('localization/');
                            $rcmail->output->show_message("additional_imap.connectionfailed", "error");
                            $Q = 0;
                        }
                    }
                    $J = unserialize($args['preferences']);
                    if (is_array($J)) {
                        $J['imap_delimiter'] = $I;
                    } else {
                        $J = array('imap_delimiter' => $I);
                    }
                    $J = serialize($J);
                    $C = 'UPDATE ' . rcmail::get_instance()->db->table_name('additional_imap').
                    ' SET username=?, server=?, enabled=?, label=?, preferences=? WHERE user_id=? AND iid=?';
                    $rcmail->db->query($C, $K, $sql, $Q, $h, $J, $rcmail->user->ID, $B['id']);
                }
            } else {
                if ($Q) {
                    if (!$this->identity_update_test($K, $l, $sql)) {
                        $this->add_texts('localization/');
                        $rcmail->output->show_message("additional_imap.connectionfailed", "error");
                        $Q = 0;
                    }
                }
                $J = serialize(array('imap_delimiter' => $I));
                $C = 'INSERT INTO ' . rcmail::get_instance()->db->table_name('additional_imap').
                ' (username, password, server, user_id, iid, enabled, label, preferences) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                $rcmail->db->query($C, $K, $T, $sql, $rcmail->user->ID, $B['id'], $Q, $h, $J);
            }
        }
        return $B;
    }

    function identity_delete($B) {
        $rcmail = $this->rcmail;
        $C = 'DELETE from ' . rcmail::get_instance()->db->table_name('additional_imap').
        ' WHERE iid=? AND user_id=?';
        $rcmail->db->query($C, $B['id'], $rcmail->user->ID);
        return $B;
    }
    private function encrypt($U, $M, $z) {
        $rcmail = $this->rcmail;
		$method = 'AES-256-CBC';
        if ($rcmail->config->get('additional_imap_crypt', 'rcmail') == 'rcmail') {
            return $rcmail->encrypt($U);
        } else {
            $E = hash('SHA256', $z.$M, true);
            srand();
            $r = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
            if (strlen($SB = rtrim(base64_encode($r), '=')) != 22) {
                return false;
            }
            $a = base64_encode(openssl_encrypt($method, $E, $U.md5($U), $method, $r));
            return $SB.$a;
        }
    }
    private function decrypt($a, $M, $z) {
        $rcmail = $this->rcmail;
		$method = 'AES-256-CBC';
        if ($rcmail->config->get('additional_imap_crypt', 'rcmail') == 'rcmail') {
            return $rcmail->decrypt($a);
        } else {
            $E = hash('SHA256', $z.$M, true);
            $r = base64_decode(substr($a, 0, 22).
                '==');
            $a = substr($a, 22);
            $U = rtrim(mcrypt_decrypt($method, $E, base64_decode($a), $method, $r), "\0\4");
            $RB = substr($U, -32);
            $U = substr($U, 0, -32);
            if (md5($U) != $RB) {
                return false;
            }
            return $U;
        }
    }
    private function get_sorted_list() {
        $rcmail = $this->rcmail;
		$method = 'AES-256-CBC';
		
        $aB = $rcmail->user->data['user_id'];
        $b = array();
        $EB = "SELECT * FROM ".rcmail::get_instance()->db->table_name('additional_imap').
        " WHERE user_id=? AND enabled=?";
        $C = $rcmail->db->query($EB, $aB, 1);
        while ($XB = $rcmail->db->fetch_assoc($C)) $b[] = $XB;
        $GB = array();
        foreach($b as $E => $L) {
            $GB[$E] = $L['label'];
        }
        asort($GB);
        $n = array();
        foreach($GB as $E => $L) {
            $n[] = $b[$E];
        }
        return $n;
    }
    private function identity_update_test($K, $T, $sql) {
        $F = parse_url($sql);
        $j = $F['host'] ? $F['host'] : $F['path'];
        $c = false;
        if ($F['scheme'] == 'ssl' || $F['scheme'] == 'tls') {
            $c = $F['scheme'];
            $F['port'] = $F['port'] ? $F['port'] : 993;
        }
        $F['port'] = $F['port'] ? $F['port'] : 143;
        return $this->test_connection($K, $T, $j, $F['port'], $c);
    }
    private function test_connection($VB, $M, $YB, $UB, $c) {
        $rcmail = $this->rcmail;
        if (!is_object($rcmail->storage)) {
            $rcmail->storage_init();
        }
        $res = $rcmail->storage->connect($YB, $VB, trim($M), $UB, $c);
        return $res;
    }
    private function gc($W) {
        $QB = $this->rcmail->config->get('additional_imap_gc', 100);
        $WB = mt_rand(0, $QB);
        $ZB = round($QB / 2);
        if ($WB == $ZB) {
            $TB = get_offset_sec($this->rcmail->config->get('imap_cache_ttl', '10d'));
            $C = 'SELECT * FROM ' . rcmail::get_instance()->db->table_name('cache_tables').
            ' WHERE ts < ? AND suffix <> ?';
            $res = $this->rcmail->db->query($C, time() - $TB, $W);
            while ($PB = $this->rcmail->db->fetch_assoc($res)) {
                $x = array('cache', 'cache_index', 'cache_messages', 'cache_shared', 'cache_thread');
                $C = 'DELETE FROM ' . rcmail::get_instance()->db->table_name('cache_tables').
                ' WHERE suffix=?';
                $this->rcmail->db->query($C, $PB['suffix']);
                foreach($x as $d) {
                    $d = rcmail::get_instance()->db->table_name($d);
                    $C = 'DROP TABLE '.$d.
                    '_'.$PB['suffix'];
                    $this->rcmail->db->query($C);
                }
            }
        }
    }
}