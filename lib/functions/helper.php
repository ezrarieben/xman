<?php
/**
* Helper functions are defined in this file
*/

namespace xman\helper;

use function µ\cache;
use function µ\config;

/**
* 301 redirect function
*
* @param $path --> Path to redirect to
*/
function redirect($path): Bool
{
    header("Location: {$path}", true, 301);
    // Why die should be used: http://thedailywtf.com/articles/WellIntentioned-Destruction
    die();

    return true;
}

/**
* Function to retreive the route (URI) from the URL
*/
function getRoute(): String
{
    return rawurldecode(strtok($_SERVER['REQUEST_URI'], '?'));
}

/**
* Function to convert object to array
*
* @param stdClass $object
*
* @return array --> Object converted to array
*/
function toArray(\stdClass $object): array
{
    return json_decode(json_encode($object), true);
}

/**
* This function generates a random string and returns it
*
* @param int $length --> Length of string to generate
*
* @return string --> Random generated string
*/
function getRandomString(int $length = 10): String
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
* This function gets the current modx version
*
* @return string --> MODX version label (e.g.: v2.7.1-pl)
*/
function getCurrentModxVersion(): String
{
    if (!empty(cache()->get('current_modx_version_name'))) {
        return cache()->get('current_modx_version_name');
    }

    $githubVersionsUrl = "https://api.github.com/repos/modxcms/revolution/tags";
    $githubVersions = new \xman\ApiCall($githubVersionsUrl, array());
    $githubVersions = $githubVersions->getResponse();

    // Set the current version to cache for 1hr in order to circumvent github API request limiting: https://developer.github.com/v3/#rate-limiting
    cache()->set('current_modx_version_name', $githubVersions[0]->name, 3600);


    return cache()->get('current_modx_version_name');
}

/**
* This function downloads the current MODX version zip archive
*
* @return bool --> True if download was successful
*/
function downloadCurrentModxVersion(): Bool
{
    // set limit on script execution to 0 for download
    set_time_limit(0);

    $currentVersionName = \xman\helper\getCurrentModxVersion();

    // Get version, major and minor from current version name
    $version = substr($currentVersionName, 1, -3);
    $versionLevels = explode('.', $version);
    $version = $versionLevels[0];
    $major = $versionLevels[1];
    $minor = $versionLevels[2];

    $downloadUrl = "https://modx.s3.amazonaws.com/releases/{$version}.{$major}.{$minor}/modx-{$version}.{$major}.{$minor}-pl-advanced.zip";

    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    $targetPath = $docRoot. config()->get('xman.modx_downloads_path');
    $filename = $currentVersionName . '.zip';
    $targetZipFile = $targetPath . $filename;

    // If most current version is downloaded already stop download
    if (is_file($targetZipFile)){
      return true;
    }

    // Create target path if it does not exist
    if (!file_exists($targetPath)) {
        if (!mkdir($targetPath)) {
            throw new \Exception("Failed to download new MODX version because target path '{$targetPath}' could not be created");
        }
    }

    // Delete all previous update files
    $oldUpdateFiles = glob($targetPath.'*'); // get all file names
    foreach ($oldUpdateFiles as $file) { // iterate files
      if (is_file($file)) {
          if (!unlink($file)) {
              throw new \Exception("Failed to download new MODX version because old versions could not be deleted");
          }
      }
    }

    // Download new version zip
    $ch = curl_init($downloadUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FAILONERROR,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

    $download = curl_exec($ch);
    if(curl_errno($ch)){
      throw new \Exception("Failed to download MODX update zip. cURL error: '".curl_error($ch)."'");
    }
    curl_close($ch);

    if(!file_put_contents($targetZipFile, $download)){
      throw new \Exception("Failed to download MODX update zip because zip could not be saved.");
    }

    return true;
}

/**
* This function returns the latest modx update zip download
*
* @return application/zip --> Zip archive downloadable file
*/
function getCurrentModxVersionDownload()
{
  // set limit on script execution to 0 for allowing file stream
  set_time_limit(0);

  $currentVersionName = \xman\helper\getCurrentModxVersion();

  // Get version, major and minor from current version name
  $version = substr($currentVersionName, 1, -3);
  $versionLevels = explode('.', $version);
  $version = $versionLevels[0];
  $major = $versionLevels[1];
  $minor = $versionLevels[2];

  $docRoot = $_SERVER['DOCUMENT_ROOT'];
  $targetPath = $docRoot. config()->get('xman.modx_downloads_path');
  $filename = $currentVersionName . '.zip';
  $targetZipFile = $targetPath . $filename;

  // Set headers to zip output
  header('Content-type: application/zip');
  header('Content-Disposition: attachment; filename="'.$filename.'"');

  // Read and therefore return zip
  readfile($targetZipFile);
  die();
}
