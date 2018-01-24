<?php

class FaceApi
{

	const API_ID = 'd45fd466-51e2-4701-8da8-04351c872236';
	const API_SECRET = '171e8465-f548-401d-b63b-caf0dc28df5f';
	const API_URL = 'http://www.betafaceapi.com/service_json.svc';
	const API_POLL_INTERVAL = 1;
	public $filename = '17309521_10212621347589196_346796061167266982_n.jpg';

	private function curlHelper($url,$request_data)
	{

		$ch = curl_init();
		$headers[] = 'Content-Type: application/json; charset=utf-8';
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$request_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
	}

	public function imageUploadFile()
	{
		$url = self::API_URL.'/UploadNewImage_File';

		$request_data = array(
							'api_key' => self::API_ID,
							'api_secret' => self::API_SECRET,
							'detection_flags'=> "propoints,classifiers,extended",
							'imagefile_data' => $this->getImageData(),
							'original_filename'=> $this->filename
						);

		$result = $this->curlHelper($url,json_encode($request_data));
		return $result;
	}

	private function getImageData()
	{
		$data = file_get_contents($this->filename);
		$array = array();
		foreach(str_split($data) as $char){
		    array_push($array, ord($char));
		}

		return $array;
	}

	public function getImageInfo()
	{
		$image = json_decode($this->imageUploadFile());
		$url = self::API_URL.'/GetImageInfo';
		$request_data = array(
							  'api_key' => self::API_ID,
							  'api_secret' => self::API_SECRET,
							  'img_uid' => $image->img_uid
							  );

		return $this->curlHelper($url,json_encode($request_data));
	}
}
	/* Usage */
	$api = new FaceApi();
	$image = imagecreatefromjpeg('test_image.jpg');
	$color = imagecolorallocate ($image, 255, 255, 5);
	$data = json_decode($api->getImageInfo());

	foreach ($data->faces as $key => $value) {
		//var_dump($value->points);
		foreach ($value->points as $key2 => $val) {
			imagesetpixel ($image , $val->x , $val->y , $color );
		}
	}

	header('Content-Type: image/jpeg');
	imagejpeg($image);
	imagedestroy($image);
?>