<?php

namespace xman\user;

use function µ\database;
use function µ\config;

function isLoggedIn(): Bool
{
  return (isset($_SESSION['xman']['user']) && !empty($_SESSION['xman']['user']));
}

/**
* Unset all session variables and therefore logout the user
*
* @return bool
*/
function logout(): Bool
{
  session_unset();
  return true;
}

/**
* Authenticate user against database
*
* @param string $username
* @param string $password
*
* @return Mixed --> (bool|string) bool: true if user is authenticated | string: error message
*/
function login(string $username, string $password)
{

  if(!empty($username) && !empty($password)){
    $user = database()->get("user", "*", array(
      'username' => $username
    ));

    if(empty($user))
      return "The given user was not found";
  }

  if(password_verify($password, $user['password'])){
    // Remove password from user fields array for security reasons
    unset($user['password']);

    $_SESSION['xman']['user'] = $user;

    return true;
  }

  return "The password you have entered seems to be wrong";
}

/**
* Retreive all the users from the DB
*
* @return array --> Array of users
*/
function getAll()
{
    $users = database()->select("user", "*");

    // Remove password for all users and set active status
    foreach ($users as $key => $user) {
        unset($users[$key]['password']);
        $users[$key]['active'] = ($user['id'] === $_SESSION['xman']['user']['id']) ? true : false;
    }

    return $users;
}

/**
* Get the total count of users in the database
*
* @return int --> Count of users
*/
function getCount(): Int
{
    return intval(database()->count("user"));
}

/**
* Creates a user from POST parameters
*
* @param $_POST --> POST vars set
*
* @return array --> Array of placeholders for template
*/
function create(): array
{
    // Get user fields
    $user = array(
      'username' => strip_tags($_POST['username']),
      'firstname' => strip_tags($_POST['firstname']),
      'lastname' => strip_tags($_POST['lastname']),
      'email' => strip_tags($_POST['email']),
      'password' => strip_tags($_POST['password']),
    );

    // Hash & salt the password using blowfish
    $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT, array(
      'cost' => config()->get('xman.bcrypt_cost')
    ));

    // Check required fields
    $requiredPostFields = array(
      'username',
      'firstname',
      'lastname',
      'email',
      'password',
    );

    foreach ($requiredPostFields as $requiredField) {
        if (empty($_POST[$requiredField])) {
            return array(
              'error' => "One or many required fields are empty.",
              'user' => $user,
            );
        }
    }

    // Create user
    database()->insert("user", $user);

    return ['success' => "User: '".$user['username']."' created successfully!"];
}

/**
* Deletes a user according to his/her ID
*
* @param Int $id --> ID of the user to delete
*
* @return Bool --> True if deletion is successful
*/
function delete($id): Bool
{
  // Delete the user
  $deletion = database()->delete("user", array(
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
* Retreive user from the DB
*
* @param int $id --> ID of the user to retreive
*
* @return array --> User fields | bool: false if user not found
*/
function get($id)
{
    $user = database()->get("user", "*", array(
      'id' => $id
    ));

    if (empty($user)) {
        return false;
    }

    // Remove password
    unset($user['password']);

    return $user;
}

/**
* Update user in DB
*
* @param int $id --> ID of the user that is to be updated
*
* @return array -->  Array of fields for template
*/
function update($id): Array
{
  // Get user fields
  $user = array(
    'username' => strip_tags($_POST['username']),
    'firstname' => strip_tags($_POST['firstname']),
    'lastname' => strip_tags($_POST['lastname']),
    'email' => strip_tags($_POST['email']),
    'password' => strip_tags($_POST['password']),
    'password_confirm' => strip_tags($_POST['password_confirm']),
  );

  // Check required fields
  $requiredPostFields = array(
    'username',
    'firstname',
    'lastname',
    'email',
  );

  // Check if user exists
  $existingUser = database()->get("user", "*", ['id' => $id]);

  if(empty($existingUser)){
    return array(
      'error' => "User with the ID: '{$id}' was not found",
      'user' => $user,
    );
  }

  foreach ($requiredPostFields as $requiredField) {
      if (empty($_POST[$requiredField])) {
          return array(
            'error' => "One or many required fields are empty.",
            'user' => array_merge($existingUser, $user),
          );
      }
  }

  if(!empty($user['password'])){
    if($user['password'] !== $user['password_confirm']){
      return array(
        'error' => "Passwords did not match.",
        'user' => array_merge($existingUser, $user),
      );
    }
  }

  // If username was changed check if it already exists
  if($user['username'] !== $existingUser['username']){
    if(!empty(database()->get("user", "id", ['username' => $user['username']]))){
      return array(
        'error' => "A user with the username: '".$user['username']."' already exists.",
        'user' => array_merge($existingUser, $user),
      );
    }
  }

  // Hash & salt the password using blowfish if the password was reset else remove password from user fields
  if(!empty($user['password'])){
    $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT, array(
      'cost' => config()->get('xman.bcrypt_cost')
    ));
  } else {
    unset($user['password']);
  }

  // Remove password confirm user field
  unset($user['password_confirm']);

  // Update user
  $update = database()->update("user", $user, array(
    'id' => $id
  ));

  if(!$update){
    return array(
      'error' => "Failed to update user with the ID: '{$id}'",
      'user' => array_merge($existingUser, $user),
    );
  } else {
    return array(
      'success' => "User: '".$user['username']."' updated successfully!",
      'user' => array_merge($existingUser, $user),
    );
  }
}
