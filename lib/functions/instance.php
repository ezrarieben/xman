<?php
/**
* All instance functions are defined in this file
*/

namespace xman\instance;

use function µ\config;
use function µ\database;

/**
* Retreive all the instances from the DB
*
* @param int $limit --> Limit of instances to show
* @param string $query --> Query to instance fields with
*
* @return array --> Array of instances
*/
function getAll($query = "", $limit = 0)
{
    if(!empty($query)){
      $query = array(
        'OR' => array(
          'id' => $query,
          'name[~]' => $query,
          'url[~]' => $query,
          'modx_token[~]' => $query,
          'xman_token[~]' => $query,
        ),
        'LIMIT' => $limit,
      );
    } else {
      $query = array(
        'LIMIT' => $limit,
      );
    }

    $instances = database()->select("instance", "*", $query);

    foreach ($instances as $key => $instance) {
        // Get http response status code
        $instanceStatusRequest = new \xman\ApiCall($instance['url']);
        $instance['statusCode'] = intval($instanceStatusRequest->getResponseCode());


        $instanceInfoRequestData = array(
          'modx_token' => $instance['modx_token'],
          'xman_token' => $instance['xman_token'],
          'requesting_domain' => $_SERVER['HTTP_HOST'],
        );
        $instanceInfoRequest = new \xman\ApiCall($instance['url']."/".config()->get('xman.clients.connector_path')."?action=web/api/getinstanceinfo", $instanceInfoRequestData);
        $instance['info'] = $instanceInfoRequest->getResponse();
        if(!empty($instance['info'])){
          $instance['info'] = \xman\helper\toArray($instance['info']);
        }

        $instance['hasUpdate'] = (\xman\helper\getCurrentModxVersion() !== $instance['info']['modx']['version_label']) ? true : false;

        // Set new values in instances array
        $instances[$key] = $instance;
    }

    return $instances;
}


/**
* Get the total count of instances in the database
*
* @return int --> Count of instances
*/
function getCount(): Int
{
    return intval(database()->count("instance"));
}

/**
* Deletes an instance according to it's ID
*
* @param Int $id --> ID of instance to delete
*
* @return Bool --> True if deletion is successful
*/
function delete($id): Bool
{
  // Delete the instance
  $deletion = database()->delete("instance", array(
    'id' => $id
  ));

  // Get number of deleted rows
  $deleteCount = $deletion->rowCount();

  if($deleteCount > 0){
    return true;
  } else {
    return false;
  }
}

/**
* Creates an instance from POST parameters
*
* @param $_POST --> POST vars set
*
* @return array --> Array of placeholders for template
*/
function create(): array
{
    // Get instance fields
    $instance = array(
      'name' => strip_tags($_POST['name']),
      'url' => strip_tags($_POST['url']),
      'modx_token' => strip_tags($_POST['modx_token']),
    );

    // Check required fields
    $requiredPostFields = array(
      'name',
      'url',
      'modx_token',
    );

    foreach ($requiredPostFields as $requiredField) {
        if (empty($_POST[$requiredField])) {
            return array(
              'error' => "One or many required fields are empty.",
              'instance' => $instance,
            );
        }
    }

    // Check if instance URL starts with http:// or https://
    if(substr($instance['url'], 0, 7) !== "http://" && substr($instance['url'], 0, 8) !== "https://"){
      return array(
        'error' => "URL must start with http:// or https://.",
        'instance' => $instance,
      );
    }

    // Check if instance URL already exists
    if(!empty($existingInstance = database()->get("instance", "name", ['url' => $instance['url']]))){
      return array(
        'error' => "Instance with the given URL already exists and is named: '".$existingInstance['name']."'",
        'instance' => $instance,
      );
    }

    // Generate XMan API token
    $instance['xman_token'] = \xman\helper\getRandomString(64);

    // Check if modx_token is valid
    $restfulUrl = $instance['url']."/".config()->get('xman.clients.connector_path')."?action=web/api/verifyinstance";
    $restfulData = array(
      'modx_token' => $instance['modx_token'],
      'xman_token' => $instance['xman_token'],
      'requesting_domain' => $_SERVER['HTTP_HOST'],
    );

    $restfulRequest = new \xman\ApiCall($restfulUrl, $restfulData);

    $response = $restfulRequest->getResponse();
    $responseCode = $restfulRequest->getResponseCode();

    if($responseCode !== 200){
      return array(
        'error' => "Error occured whilst calling MODX XMan API. Response code: '{$responseCode}'",
        'instance' => $instance,
      );
    }

    // Show error message if error occured during modx_token validation
    if(is_object($response) && $response->success !== true){
      return array(
        'error' => "API error occured: ".$response->message,
        'instance' => $instance,
      );
    }

    // Create instance
    database()->insert("instance", $instance);

    return ['success' => "Instance: '".$instance['name']."' created successfully!"];
}

/**
* Update instance in DB
*
* @param int $id --> ID of the instance that is meant to be updated
*
* @return array -->  Array of fields for template
*/
function update($id): Array
{
  // Get instance fields
  $instance = array(
    'name' => strip_tags($_POST['name']),
    'url' => strip_tags($_POST['url']),
    'modx_token' => strip_tags($_POST['modx_token'])
  );

  // Check required fields
  $requiredPostFields = array(
    'name',
    'url',
    'modx_token',
  );

  // Check if instance exists
  $existingInstance = database()->get("instance", "*", ['id' => $id]);

  if(empty($existingInstance)){
    return array(
      'error' => "Instance with the ID: '{$id}' was not found",
      'instance' => $instance,
    );
  }

  foreach ($requiredPostFields as $requiredField) {
      if (empty($_POST[$requiredField])) {
          return array(
            'error' => "One or many required fields are empty.",
            'instance' => array_merge($existingInstance, $instance),
          );
      }
  }

  // Check if instance URL starts with http:// or https://
  if(substr($instance['url'], 0, 7) !== "http://" && substr($instance['url'], 0, 8) !== "https://"){
    return array(
      'error' => "URL must start with http:// or https://.",
      'instance' => array_merge($existingInstance, $instance),
    );
  }

  // Check if modx_token is valid
  $restfulUrl = $instance['url']."/".config()->get('xman.clients.connector_path')."?action=web/api/verifyinstance";
  $restfulData = array(
    'modx_token' => $instance['modx_token'],
    'xman_token' => $existingInstance['xman_token'],
    'requesting_domain' => $_SERVER['HTTP_HOST'],
  );

  $restfulRequest = new \xman\ApiCall($restfulUrl, $restfulData);

  $response = $restfulRequest->getResponse();
  $responseCode = $restfulRequest->getResponseCode();

  if($responseCode !== 200){
    return array(
      'error' => "Error occured whilst calling MODX XMan API. Response code: '{$responseCode}'",
      'instance' => array_merge($existingInstance, $instance),
    );
  }

  // Show error message if error occured during modx_token validation
  if(is_object($response) && $response->success !== true){
    return array(
      'error' => "API error occured: {$response->message}",
      'instance' => array_merge($existingInstance, $instance),
    );
  }

  // Update instance
  $update = database()->update("instance", $instance, array(
    'id' => $id
  ));

  if(!$update){
    return array(
      'error' => "Failed to update instance with the ID: '{$id}'",
      'instance' => array_merge($existingInstance, $instance),
    );
  } else {
    return array(
      'success' => "Instance: '".$instance['name']."' updated successfully!",
      'instance' => array_merge($existingInstance, $instance),
    );
  }
}

/**
* Retreive instance from the DB
*
* @param int $id --> ID of instance to retreive
*
* @return array --> Instance fields | bool: false if instance not found
*/
function get($id)
{
    $instance = database()->get("instance", "*", array(
      'id' => $id
    ));

    if (empty($instance)) {
        return false;
    }

    // Get http response status code
    $instanceStatusRequest = new \xman\ApiCall($instance['url']);
    $instance['statusCode'] = intval($instanceStatusRequest->getResponseCode());

    $instanceInfoRequestData = array(
      'modx_token' => $instance['modx_token'],
      'xman_token' => $instance['xman_token'],
      'requesting_domain' => $_SERVER['HTTP_HOST'],
    );
    $instanceInfoRequest = new \xman\ApiCall($instance['url']."/".config()->get('xman.clients.connector_path')."?action=web/api/getinstanceinfo", $instanceInfoRequestData);
    $instance['info'] = $instanceInfoRequest->getResponse();
    if(!empty($instance['info'])){
      $instance['info'] = \xman\helper\toArray($instance['info']);
    }

    $instance['hasUpdate'] = (\xman\helper\getCurrentModxVersion() !== $instance['info']['modx']['version_label']) ? true : false;
    $instance['managerUrl'] = $instance['url']."/".config()->get('xman.clients.manager_path');

    // Get all log entries
    $instance['logEntries'] = \xman\log\get($instance['id']);

    // Format the timestamp to a readable date
    foreach ($instance['logEntries'] as $key => $logEntry) {
        $instance['logEntries'][$key]['createdon'] = strftime(config()->get('xman.date_format'), $logEntry['createdon']);
    }

    return $instance;
}

/**
* This function sends an API request to an instance to update it to the latest MODX version
*
* @param $id --> ID of instance to update
*
* @return array --> Array of placeholders for template
*/
function updateModx($id): Array
{
  $instance = \xman\instance\get($id);

  if(empty($instance)){
    return array(
      'error' => "Instance with the ID: '{$id}' not found",
      'instance' => $instance,
    );
  }

  \xman\log\create($id, "Starting MODX update: ".\xman\helper\getCurrentModxVersion());

  // Make sure latest MODX version is downloaded
  \xman\helper\downloadCurrentModxVersion();

  // Send update request to MODX API
  $restfulUrl = $instance['url']."/".config()->get('xman.clients.connector_path')."?action=web/api/updateinstance";
  $restfulData = array(
    'modx_token' => $instance['modx_token'],
    'xman_token' => $instance['xman_token'],
    'requesting_domain' => $_SERVER['HTTP_HOST'],
  );

  $restfulRequest = new \xman\ApiCall($restfulUrl, $restfulData);

  $response = $restfulRequest->getResponse();
  $responseCode = $restfulRequest->getResponseCode();

  if($responseCode !== 200){
    \xman\log\create($id, "Update failed!");

    return array(
      'error' => "Error occured whilst calling MODX XMan client API. Response code: '{$responseCode}'",
      'instance' => $instance,
    );
  }

  // Show error message if error occured during update request
  if(is_object($response) && $response->success !== true){
    \xman\log\create($id, "Update failed!");

    return array(
      'error' => "Error occured whilst calling MODX XMan client API: ".$response->message,
      'instance' => $instance,
    );
  }

  \xman\log\create($id, "Finished updating to MODX: ".\xman\helper\getCurrentModxVersion());

  return array(
    'success' => "Successfully updated instance '".$instance['name']."' to MODX: ".\xman\helper\getCurrentModxVersion(),
    'instance' => $instance,
  );
}
