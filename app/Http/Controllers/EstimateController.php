<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimate;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class EstimateController extends Controller
{
    public function store(Request $request)
    {
        $from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $message = null;

        $phone = $this->dbSavedRequest($from, $body);

        // return $phone;

        if($body == "estimate"){
            $phone->stage_model = 'inputPickup';
            $phone->save();
        }

        if($body == "courier"){
            $phone->stage_model = 'inputCourierPickup';
            $phone->save();
        }

        if($body == 'cancel'){
            $phone->terminate = true;
            $phone->finished = true;
            $phone->save();

            $message = "Search Session was cancelled. Type menu to proceed";
        }

        if($phone->stage_model == "new"){
            $message = "*Welcome To Swifno!!!*\n";
            $message .= "I am here to assist you\n";
            $message .= "Type *estimate* to access our delivery estimate features\n";
            $message .= "Type *courier* to access our courier vendor lists\n";
        }


        if($phone->stage_model == 'inputCourierPickup' && $phone->pickup == null){
            $message = $this->inputCourierPickup($from, $body);
        }elseif($phone->stage_model == 'checkCourierPickup' && $phone->pickup == null){
            $message = $this->checkCourierPickup($from, $body);
        }elseif($phone->stage_model == 'inputCourierDropoff' && $phone->dropoff == null){
            $message = $this->inputCourierDropoff($from, $body);
        }elseif($phone->stage_model == 'checkCourierDropoff' && $phone->dropoff == null){
            $message = $this->checkCourierDropoff($from, $body);
        }elseif($phone->stage_model == 'vendorList' && $phone->dropoff == null){
            $message = $this->vendorList($from, $body);
        }elseif($phone->stage_model == 'inputPickup' && $phone->pickup == null){
        	$message = $this->inputPickup($from, $body);
        }elseif($phone->stage_model == 'checkPickup' && $phone->pickup == null){
        	$message = $this->checkPickup($from, $body);
        }elseif($phone->stage_model == 'inputDropoff' && $phone->dropoff == null){
        	$message = $this->inputDropoff($from, $body);
        }elseif($phone->stage_model == 'checkDropoff' && $phone->dropoff == null){
        	$message = $this->checkDropoff($from, $body);
        }elseif($phone->stage_model == 'inputShortCategory' && $phone->category == null){
        	$message = $this->checkCategory($from, $body);
        }elseif($phone->stage_model == 'inputFullCategory' && $phone->category == null){
        	$message = $this->checkCategory($from, $body);
        }elseif($phone->stage_model == 'checkCategory' && $phone->category == null){
        	$message = $this->checkCategory($from, $body);
        }elseif($phone->stage_model == 'inputSize' && $phone->size == null){
        	$message = $this->inputSize($from, $body);
        }elseif($phone->stage_model == 'checkSize' && $phone->size == null){
        	$message = $this->checkSize($from, $body);
        }elseif($phone->stage_model == 'insurance' && $phone->insurance == null){
        	$message = $this->inputInsurance($from, $body);
        }elseif($phone->stage_model == 'checkInsurance' && $phone->insurance == null){
        	$message = $this->checkInsurance($from, $body);
        }


        return $message;
         // return $this->sendWhatsAppMessage($message, $from);
    }

    public function inputCourierPickup($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'checkCourierPickup';
        $phone->save();

        return 'Kindly type a courier pickup address';
    }

    public function checkCourierPickup($from, $body)
    {
        if (str_word_count($body) <= 1){
            return "invalid address input";
        }

        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'inputCourierDropoff';
        $phone->pickup = $body;
        $phone->save();

        return $this->inputCourierDropoff($from, $body);
    }

    public function inputCourierDropoff($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'checkCourierDropoff';
        $phone->save();

        $message =  null;

        $message .= 'PickUp Location : ' . $phone->pickup." \n  \n ";

        $message .= "kindly type a courier dropoff address \n  \n  ";

        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function checkCourierDropoff($from, $body)
    {
        $message =  null;

        $phone = $this->dbSavedRequest($from, $body);

        if($body == 'f9'){
            $phone->stage_model = 'checkCourierDropoff';
            $phone->save();
            return $this->inputCourierDropoff($from, $body);
        }elseif (str_word_count($body) <= 1){
            $message .= "Invalid courier dropoff address input \n \n ";
        }else{

            $response = Http::withOptions([
                'verify' => false
            ])->get('https://swifno.com/v1/api.php', [
                'action' => 'couriers',
                'pickup' => $phone->pickup,
                'dropoff' => $body,
                'auth_token' => '8dc59f308dcedf091d4310c928e581cd',
            ]);

            $response = $response->json();

            // dd($response);

            if(array_key_exists('RESPONSECODE', $response)){
                if($response['RESPONSECODE'] == false){

                    $message .= "Invalid courier dropoff address input. Please insert an accurate address\n \n ";

                }else{

                    $message .= "*Vendor List*   \n ";

                    foreach ($response['VENDOR_LIST'] as $key => $vendor) {
                        $message .= $key+1 ." - ". $vendor ." \n ";
                    }
                    $phone->stage_model = "end";
                    $phone->terminate = true;
                    $phone->finished = true;
                    $phone->save();
                }
            }

            return $message;
        }

        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function inputPickup($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'checkPickup';
        $phone->save();

        return 'Kindly type a pickup address';
    }

    public function checkPickup($from, $body)
    {
    	if (str_word_count($body) <= 3){
    		return "invalid address input";
    	}

    	$phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'inputDropoff';
        $phone->pickup = $body;
        $phone->save();

        return $this->inputDropoff($from, $body);
    }

    public function inputDropoff($from, $body)
    {
    	$phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'checkDropoff';
        $phone->save();

        $message =  null;

        $message .= 'PickUp Location : ' . $phone->pickup." \n  \n ";

        $message .= "kindly type a dropoff address \n  \n  ";

    	$message .= "Please Press *f8* to view full list \n ";
        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function checkDropoff($from, $body)
    {
    	$message =  null;

    	if (str_word_count($body) <= 3){
    		$message .= "invalid dropoff address input \n \n ";
    	}else{
    		$phone = $this->dbSavedRequest($from, $body);
	        $phone->stage_model = 'inputShortCategory';
	        $phone->dropoff = $body;
	        $phone->save();
	        return $this->inputShortCategory($from, $body);
    	}

        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function inputShortCategory($from, $body)
    {

    	$phone = $this->dbSavedRequest($from, $body);

    	$response = Http::withOptions([
            'verify' => false
        ])->get('https://swifno.com/v1/api.php', [
		    'action' => 'categories',
		    'auth_token' => '8dc59f308dcedf091d4310c928e581cd'
		]);

		$response = $response->json();

		$message = null;

    	$message .= 'PickUp Location : ' . $phone->pickup. " \n  \n ";
    	$message .= 'DropOff Location : ' . $phone->dropoff. " \n  \n ";

    	$message .= 'Please Select Category from the list below: ??'." \n  \n ";

        $keys = array_column($response['CATEGORYLIST'], 'cat_id');

        array_multisort($keys, SORT_ASC, $response['CATEGORYLIST']);

        // dd($response['CATEGORYLIST']);

    	foreach ($response['CATEGORYLIST'] as $key => $category) {
    		if($key <= 7){
    			$message .= $category['cat_id'] .' - '. $category['cat_name'] .' || '. $category['group_name'] ." \n ";
    		}
    	}

        $phone->stage_model = 'checkCategory';
        $phone->save();

    	$message .= " \n  Please Press *f8* to view full list \n ";
        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function inputFullCategory($from, $body)
    {
		$message = null;

        $response = Http::withOptions([
            'verify' => false
        ])->get('https://swifno.com/v1/api.php', [
		    'action' => 'categories',
		    'auth_token' => '8dc59f308dcedf091d4310c928e581cd'
		]);

		$response = $response->json();

    	$phone = $this->dbSavedRequest($from, $body);

        $phone->stage_model = 'checkCategory';
        $phone->save();

    	$message .= 'PickUp Location : ' . $phone->pickup." \n  \n ";
    	$message .= 'DropOff Location : ' . $phone->dropoff." \n  \n ";

    	$message .= 'Please Select Category from the list below: ??'." \n  \n ";

        $keys = array_column($response['CATEGORYLIST'], 'cat_id');

        array_multisort($keys, SORT_ASC, $response['CATEGORYLIST']);

    	foreach ($response['CATEGORYLIST'] as $key => $category) {
    		$message .= $category['cat_id'] .' - '. $category['cat_name'] .' || '. $category['group_name'] ." \n ";
    	}

        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function checkCategory($from, $body)
    {
    	$message =  null;

	    $phone = $this->dbSavedRequest($from, $body);

    	if($body == 'f8'){
    		return $this->inputFullCategory($from, $body);
    	}

    	if($body == 'f9'){
    		$phone->stage_model = 'inputDropoff';
    		$phone->dropoff = null;
    		return $this->inputDropoff($from, $body);
    	}

    	$response = Http::withOptions([
            'verify' => false
        ])->get('https://swifno.com/v1/api.php', [
		    'action' => 'categories',
		    'auth_token' => '8dc59f308dcedf091d4310c928e581cd',
		]);

		$response = $response->json();

		foreach ($response['CATEGORYLIST'] as $key => $category) {
			if($category['cat_id'] == $body){
	        	$phone->category = $category['cat_name'];
	        	$phone->save();
			}
    	}

    	if($phone->category != null){
			$phone->stage_model = 'inputSize';
	        $phone->save();

			return $this->inputSize($from, $body);

    	}else{
    		$message .= "Invalid Input \n";
    		$message .= $this->inputShortCategory($from, $body);
    		$message .= "Please Press *f8* to view full list \n";
    		return $message;
    	}

        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function inputSize($from, $body)
    {
    	$message = null;

    	$response = Http::withOptions([
            'verify' => false
        ])->get('https://swifno.com/v1/api.php', [
		    'action' => 'sizes',
		    'auth_token' => '8dc59f308dcedf091d4310c928e581cd',
		]);

		$response = $response->json();

    	$phone = $this->dbSavedRequest($from, $body);
    	$phone->stage_model = 'checkSize';
    	$phone->save();

    	$message .= 'PickUp Location : ' . $phone->pickup." \n  \n ";
    	$message .= 'DropOff Location : ' . $phone->dropoff." \n  \n ";
    	$message .= 'Item Category: '.$phone->category. " \n  \n ";
    	$message .= 'Please Select item size from the list below: ??'." \n  \n ";

    	foreach ($response['SIZELIST'] as $key => $category) {
    		$message .= $category['size_id'] .' - '. $category['size_name'] . " \n ";
    	}

        $message .= "\n Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function checkSize($from, $body)
    {
    	$message = null;

    	$phone = $this->dbSavedRequest($from, $body);

    	$response = Http::withOptions([
            'verify' => false
        ])->get('https://swifno.com/v1/api.php', [
		    'action' => 'sizes',
		    'auth_token' => '8dc59f308dcedf091d4310c928e581cd',
		]);

		$message .= 'PickUp Location : ' . $phone->pickup." \n  \n ";
    	$message .= 'DropOff Location : ' . $phone->dropoff." \n  \n ";
    	$message .= 'Item Category: '.$phone->category. " \n  \n ";
    	$message .= 'Please Select item size from the list below: ??'." \n  \n ";


    	foreach ($response['SIZELIST'] as $key => $size) {
    		if($size['size_id'] == $body){
	        	$phone->size = $size['size_name'];
	        	$phone->save();
			}
    	}

    	if($phone->category != null){
			$phone->stage_model = 'insurance';
			$phone->save();

			return $this->inputInsurance($from, $body);

    	}else{
    		$message .= "Invalid Input \n";
    		$message .= $this->inputSize($from, $body);
    		$message .= "Please Press *f8* to view full list \n";
    		return $message;
    	}


        $message .= " \n Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $message;
    }

    public function inputInsurance($from, $body)
    {
    	$message = null;

    	$phone = $this->dbSavedRequest($from, $body);
    	$phone->stage_model = "checkInsurance";
    	$phone->save();

		$message .= 'PickUp Location : ' . $phone->pickup." \n  \n ";
    	$message .= 'DropOff Location : ' . $phone->dropoff." \n  \n ";
    	$message .= 'Item Category: '.$phone->category. " \n  \n ";
    	$message .= 'Item size: '.$phone->size. " \n  \n ";

    	$message .= 'Please Select number of insurance mode from the list below: ??'." \n  \n ";

        $message .= "0 - Not Insured \n ";
        $message .= "1 - Insured \n ";

        return $message;
    }

    public function checkInsurance($from, $body)
    {
    	$message = null;

    	$phone = $this->dbSavedRequest($from, $body);

    	if($body == 0 || $body == 1){

    		$phone->insurance = $body;
    		$phone->save();

    		$response = Http::withOptions([
	            'verify' => false
	        ])->get('https://swifno.com/v1/api.php', [
			    'action' => 'estimate',
			    'pickup' => $phone->pickup,
			    'dropoff' => $phone->dropoff,
			    'category' => $phone->category,
			    'size' => $phone->size,
			    'insurance' => $phone->insurance,
			    'auth_token' => '8dc59f308dcedf091d4310c928e581cd',
			]);

			$response = $response->json();

			$message .= 'PickUp Location : ' . $phone->pickup." \n ";
    		$message .= 'DropOff Location : ' . $phone->dropoff." \n ";
    		$message .= 'Item Category: '.$phone->category. " \n ";
    		$message .= 'Item size: '.$phone->size. " \n ";
			$message .= 'Your logistics fee is '. array_key_exists('ESTIMATION', $response) ? $response['ESTIMATION'] : 'Not Availble at the moment'. " \n  \n ";


    		$phone->stage_model = "end";
    		$phone->terminate = true;
    		$phone->finished = true;
    		$phone->save();

            return $message;

    	}else{
    		$message .= "Invalid Input \n";
    		$message .= $this->inputInsurance($from, $body);

            return $message;
    	}
    }


    public function dbSavedRequest($from, $body)
    {
        $phone = Estimate::where('phone', $from)
	            ->where('terminate', false)
	            ->where('finished', false)
	            ->first();

	    if(!$phone){
	        $phone = new Estimate;
	        $phone->phone = $from;
	        $phone->stage_model = 'new';
	        $phone->request_received = $body;
	        $phone->save();
	        return $phone;
	    }

	    return $phone;
    }

    public function sendWhatsAppMessage(string $message, string $recipient)
    {
        $twilio_whatsapp_number = '+14155238886';
        $account_sid = 'AC02dd6e16114e3fe1db4e2e5ce134fd8e';
        $auth_token = config('services.twilio.token');

        $client = new Client($account_sid, $auth_token);

        return $client->messages->create($recipient, array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));

        // return $client->messages->create('whatsapp:' . $recipient, [
        //     "from" => 'whatsapp:' . $twilio_whatsapp_number,
        //     "body" => $message
        // ]);
    }

}
