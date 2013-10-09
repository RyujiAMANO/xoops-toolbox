<?php
require 'mainfile.php';

$root = XCube_Root::getSingleton();
$db = $root->mController->getDB();


$INBOX = "INSERT INTO `".$db->prefix('message_inbox')."` (`inbox_id`, `uid`, `from_uid`, `title`, `message`, `utime`, `is_read`) VALUES (0, %d, %d, '%s', '%s', %d, %d)";
$OUTBOX = "INSERT INTO `".$db->prefix('message_outbox')."` (`outbox_id`, `uid`, `to_uid`, `title`, `message`, `utime`) VALUES (0, %d, %d, '%s', '%s', %d)";

$num = 0;
$sql = "SELECT * FROM `".$db->prefix('priv_msgs')."` ORDER BY `msg_id`";
$result = $db->query($sql);
while ($val = $db->fetchArray($result)) {
  $sql = sprintf("SELECT count(*) FROM %s WHERE uid=%d AND from_uid=%d AND title = '%s' AND message = '%s' AND utime=%d",
    $db->prefix('message_inbox'),
    $val['to_userid'], $val['from_userid'], mysql_real_escape_string($val['subject']), mysql_real_escape_string($val['msg_text']), $val['msg_time']
    );
  $isExistsResult = $db->queryF($sql);
  list($count) = $db->fetchRow($isExistsResult);
  if($count == 0){
    $sql = sprintf($INBOX, $val['to_userid'], $val['from_userid'], mysql_real_escape_string($val['subject']), mysql_real_escape_string($val['msg_text']), $val['msg_time'], $val['read_msg']);
    $db->queryF($sql);        
  }

  $sql = sprintf("SELECT count(*) FROM %s WHERE uid=%d AND to_uid=%d AND title = '%s' AND message = '%s' AND utime=%d",
    $db->prefix('message_outbox'),
    $val['from_userid'], $val['to_userid'], mysql_real_escape_string($val['subject']), mysql_real_escape_string($val['msg_text']), $val['msg_time']
    );
  $isExistsResult = $db->queryF($sql);
  list($count) = $db->fetchRow($isExistsResult);
  if($count == 0){
    $sql = sprintf($OUTBOX, $val['from_userid'], $val['to_userid'], mysql_real_escape_string($val['subject']), mysql_real_escape_string($val['msg_text']), $val['msg_time']);
    $db->queryF($sql);
    $num++;
  }
  echo "*";
}
