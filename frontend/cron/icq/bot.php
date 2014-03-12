<?php
error_reporting(E_ALL);

define('UIN', '677092525');
define('PASSWORD', 'muxaxa20');
define('ADMINUIN', '603432370');
define('STARTXSTATUS', 'studying');
define('STARTSTATUS', 'STATUS_ONLINE');

$message_response = array();

require_once dirname(__FILE__) . '/WebIcqPro.class.php';

$admins = array(
    '603432370',
    '622399572',
);

while(1) {

    $icq = new WebIcqPro();

    if($icq->connect(UIN, PASSWORD)) {
        $icq->sendMessage(ADMINUIN, "iTwip ICQ Boot Started.");
        $uptime = $status_time = $xstatus_time = time();
        $icq->setStatus(STARTSTATUS, 'STATUS_DCCONT', 'I\'m ready');
        $icq->setXStatus(STARTXSTATUS);
        $xstatus = STARTXSTATUS;
        $status = STARTSTATUS;
    } else {
        exit();
        echo "connect filed! Next try in 20 minutes!\n";
        echo $icq->error . "\n";
        sleep(1200);
    }

    $msg_old = array();

    while($icq->isConnected()) {
        $msg = $icq->readMessage();

        if($msg && $msg !== $msg_old) {
            echo $icq->error;
            $icq->error = '';
            if(isset($msg['encoding']) && is_array($msg['encoding'])) {
                if($msg['encoding']['numset'] === 'UNICODE') {
                    $msg['realmessage'] = $msg['message'];
                    $msg['message'] = mb_convert_encoding($msg['message'], 'cp1251', 'UTF-16');
                }
                if($msg['encoding']['numset'] === 'UTF-8') {
                    $msg['realmessage'] = $msg['message'];
                    $msg['message'] = mb_convert_encoding($msg['message'], 'cp1251', 'UTF-8');
                }
            }

            $msg_old = $msg;
            if(isset($msg['type']) && $msg['type'] == 'message' && isset($msg['from']) && isset($msg['message']) && $msg['message'] != '' && !in_array($msg['from'], $ignore_list)) // && preg_match('~^[a-z0-9\-!�-� \t]+$~im', $msg['from']))
            {
                $icq->sendMessage(ADMINUIN, $msg['from'] . ' > ' . $msg['message']);

                if(in_array($msg['from'], $admins)) {
                    switch(strtolower(trim($msg['message']))) {
                        case '!help':
                            $icq->sendMessage($msg['from'], $help);
                            break;
                        case '!exit':
                            if($msg['from'] == ADMINUIN) {
                                $icq->sendMessage(ADMINUIN, "iTwip ICQ Boot is stoped.");
                                $icq->disconnect();
                                exit();
                            } else {
                                $icq->sendMessage($msg['from'], "The system is going down for reboot NOW! :)");
                            }
                            break;
                        case '!clear':
                            $list = $icq->getContactList();
                            if($msg['from'] == ADMINUIN) {
                                call_user_method_array('deleteContact', $icq, array_keys($list));
                                $icq->sendMessage(ADMINUIN, "Contact list cleared...");
                            } else {
                                $icq->sendMessage($msg['from'], "What do you want to clear?");
                            }
                            break;
                        case '!contact':
                            $c = getContactList($icq->getContactList());
                            foreach($c as $m) {
                                $m = str_replace("\x00", '', $m);
                                $icq->sendMessage($msg['from'], $m);
                            }
                            break;
                        case '!removeme':
                            $list = $icq->getContactList();
                            if(isset($list[$msg['from']])) {
                                $icq->deleteContact($msg['from']);
                            }
                            $icq->sendMessage($msg['from'], $msg['from'] . ' deleted from bot contact list');
                            break;
                        case '!uptime':
                            $seconds = time() - $uptime;
                            $time = '';
                            $days = (int) floor($seconds / 86400);
                            if($days > 1) {
                                $time .= $days . ' days, ';
                            } elseif($days == 1) {
                                $time .= $days . ' day, ';
                            }
                            $hours = (int) floor(($seconds - $days * 86400) / 3600);
                            $time .= ($hours > 1 ? $hours . ' hours, ' : ($hours == 1 ? '1 hour, ' : ''));
                            $minutes = (int) floor(($seconds - $days * 86400 - $hours * 3600) / 60);
                            $time .= ($minutes > 1 ? $minutes . ' minutes, ' : ($minutes == 1 ? '1 minute, ' : ''));
                            $seconds = (int) fmod($seconds, 60);
                            $time .= ($seconds > 1 ? $seconds . ' seconds' : ($seconds == 1 ? '1 second' : ''));
                            $time =
                                $icq->sendMessage($msg['from'], $time . ' online. Last login : ' . date('d.m.Y H:i:s', $uptime));
                            break;
                        default:
                            $command = explode(' ', $msg['message']);
                            if(count($command) > 1) {
                                switch($command[0]) {
                                    case '!info':
                                        $id = $icq->getShortInfo($command[1]);
                                        if($id) {
                                            $message_response[$id] = $msg['from'];
                                        } else {
                                            $icq->sendMessage($msg['from'], 'Error to get info for ' . $command[1]);
                                        }
                                        break;
                                    default:
                                        $icq->sendMessage($msg['from'], "Type '!help' for assistance.");
                                        break;
                                }
                            } else {
                                $icq->sendMessage($msg['from'], "Type '!help' for assistance.");
                            }
                            break;
                    }
                }
            } elseif(isset($msg['id']) && isset($message_response[(String) $msg['id']])) {
                if(isset($msg['type'])) {
                    switch($msg['type']) {
                        case 'shortinfo':
                            $message = 'Nick: ' . $msg['nick'] . "\r\n";
                            $message .= 'First Name: ' . $msg['firstname'] . "\r\n";
                            $message .= 'Last Name: ' . $msg['lastname'] . "\r\n";
                            $message .= 'Email: ' . $msg['email'] . "\r\n";
                            //$message .= 'Authorization: '.($msg['authorization'] > 0 ? 'true' : 'false')."\r\n";
                            //$message .= 'Gender: '.($msg['gender'] == 0 ? 'W' : 'M');

                            $icq->sendMessage($message_response[$msg['id']], $message);
                            break;
                        case 'accepted':
                            $message = 'Message to ' . $msg['uin'] . " sent to server. Message id: " . $msg['id'];
                            $icq->sendMessage($message_response[(String) $msg['id']], $message);
                            break;

                        default:
                            break;
                    }
                }
                unset($message_response[(String) $msg['id']]);
            } elseif(isset($msg['type'])) {
                switch($msg['type']) {
                    case 'error':
                        $icq->sendMessage(ADMINUIN, 'Error: ' . $msg['code'] . " " . (isset($msg['error']) ? $msg['error'] : ''));
                        break;
                    case 'authrequest':
                        $icq->setAuthorization($msg['from'], 'Just for fun!');
                        break;
                    case 'authresponse':
                        $icq->sendMessage(ADMINUIN, 'Authorization response: ' . $msg['from'] . ' - ' . $msg['granted'] . ' - ' . $msg['reason']);
                        break;
                    case 'accepted':
                        if(!$msg['uin'] == ADMINUIN) {
                            var_dump($msg);
                        }
                        break;
                    case 'useronline':
                    case 'autoaway':
                        break;
                }
            } elseif(isset($msg['errors'])) {
                $answer = "";
                foreach($msg['errors'] as $error) {
                    $answer .= 'Error: ' . $error['code'] . " " . $error['error'] . "\r\n";
                }
                $icq->sendMessage(ADMINUIN, $answer);
            }
        } else {
            echo $icq->error;
            $icq->error = '';
        }

        $fdir = opendir(dirname(__FILE__) . '/messages');

        $messages = array();

        while($file = readdir($fdir)) {
            if($file != '.' and $file != '..' and $file != '.htaccess') {
                $mdata = @file_get_contents(dirname(__FILE__) . '/messages/' . $file);

                if(trim($mdata) != '') {
                    $marr = explode("||", $mdata);

                    if(count($marr) == 2) {
                        if(!$icq->sendMessage($marr[0], mb_convert_encoding($marr[1], 'cp1251', 'UTF-8'))) {
                            $icq->sendMessage(ADMINUIN, $icq->error);
                        }
                    }

                    @unlink(dirname(__FILE__) . '/messages/' . $file);
                }
            }
        }

        flush();
        sleep(1);

        if(($status_time + 60) < time() && $status != STARTSTATUS) {
            $icq->setStatus(STARTSTATUS, 'STATUS_DCCONT', 'I\'m ready');
            $status = STARTSTATUS;
        }

        if(($xstatus_time + 60) < time() && $xstatus != STARTXSTATUS) {
            $icq->setXStatus(STARTXSTATUS);
            $xstatus = STARTXSTATUS;
        }
    }

    echo "Will restart in 30 seconds...\n";
    sleep(30);
}