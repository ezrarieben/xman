<?php

namespace xman\log;

use function µ\database;
use function µ\config;

/**
* This function retreives log entries from the DB for a certain instance
*
* @param $instance --> ID of the instance to get the log entries for
*
* @return array --> Array of log entries
*/
function get($instance): Array
{
  $logEntries = database()->select("log_entry", "*", array(
    'instance' => $instance
  ));

  // Default to empty array if no log entries were found
  if (empty($logEntries)) {
      $logEntries = array();
  }

  return $logEntries;
}

/**
* This function creates a log entry for a certain instance
*
* @param $instance --> ID of the instance to create the log entry for
* @param $message --> Message to add to the log
*
* @return bool --> True if insert of log entry succeeded
*/
function create($instance, $message): Bool
{
  $logEntry = array(
    'instance' => $instance,
    'text' => $message,
    'createdon' => time(),
  );

  $logEntryCreation = database()->insert("log_entry", $logEntry);

  if($logEntryCreation->rowCount() > 0){
    return true;
  } else {
    return false;
  }
}

/**
* This function deletes all log entries for a certain instance
*
* @param $instance --> ID of the instance to clear the log for
*
* @return bool --> True if clearing of log succeeded
*/
function clear($instance): Bool
{
  $logEntryCreation = database()->delete("log_entry", array(
    'instance' => $instance,
  ));

  if($logEntryCreation->rowCount() > 0){
    return true;
  } else {
    return false;
  }
}
