<?php
namespace Motniemtin\FptAi;

use Exception;
class FptAi{
  var $key;
  var $speed=0;
  var $response;
  var $voice='banmai';
  public function __construct($key){
    $this->key=$key;
  }
  public function setSpeed($speed){
    $this->speed=$speed;
  }
  public function setVoice($voice){
    $this->speed=$speed;
  }  
  public function makeSpeech($text){
    if(strlen($text)>5000){
      throw new Exception("Văn bản cần chuyển đổi không được quá 5000 ký tự!");
    }    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fpt.ai/hmi/tts/v5',
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $text,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_HTTPHEADER => array(
        'api-key: '.$this->key,
        'speed: '.$this->speed,
        'voice: '.$this->voice
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
      throw new Exception('cURL Error #:' . $err);
    } else {         
      $this->response=json_decode($response);
      //sleep(10);
    }    
  }
  public function saveFile($file){
    if($this->response->error){
      throw new Exception("Có lỗi khi chuyển văn bản thành âm thanh");
    }else{
      set_time_limit(0);
      //This is the file where we save the    information
      $fp = fopen ($file, 'w+');
      //Here is the file we are downloading, replace spaces with %20
      $ch = curl_init(str_replace(" ","%20",$this->response->async));
      curl_setopt($ch, CURLOPT_TIMEOUT, 50);
      // write curl response to file
      curl_setopt($ch, CURLOPT_FILE, $fp); 
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      // get curl response
      curl_exec($ch); 
      curl_close($ch);
      fclose($fp);
    }
  }
}