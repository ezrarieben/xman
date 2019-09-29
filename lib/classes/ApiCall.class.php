<?php

namespace xman;

/**
* This class is used to make a JSON REST API call
*
* @var $result --> API Result
* @var $responseCode --> HTTP response code
*/
class ApiCall {

  private $response;
  private $responseCode;

  /**
  * Constructor
  *
  * @param string $url --> URL to call
  * @param array $data --> Data to send via post
  *
  * @return ApiCall
  */
  public function __construct(string $url = '', array $data = array()){
    $curl = curl_init();
    if(!empty($data)){
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    // Authentication for modx.erieben.ch
    if(strpos($url, "modx.erieben.ch")){
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, "admin:ezraRieben");
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

    $this->response = json_decode(curl_exec($curl));
    $this->responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

    curl_close($curl);
  }

  /**
  * Getter function for API request result
  *
  * @return stdClass--> JSON object with response of call
  */
  public function getResponse()
  {
    return $this->response;
  }

  /**
  * Getter function for API HTML response code
  *
  * @return int --> HTML response code as int
  */
  public function getResponseCode(): int
  {
    return $this->responseCode;
  }
}
