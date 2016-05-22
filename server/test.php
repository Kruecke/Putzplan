<?php
header("Content-type:application/x-www-form-urlencoded");
    $return = array("state" => "undefined", "dataURL" => "nothing");
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $skey = $request->skey;

    if($skey=="12345"){
        $return['state'] = "success";
        $return['dataURL'] = "http://rdrdphi.de/fsr/server/dlTestArchive.zip";
        echo json_encode($return);
    }else{
        $return['state'] = "failed";
        echo json_encode($return);
    }
/*
class putzplanAPI {
//	private	$builddir = "build";
//	private	$putzplan = "Putzplan.pdf";
//	private $pp_path  = "$builddir/$putzplan";	
//	private $config   = "config.ini";
//	private $cfg_path = "$builddir/$config";
	private $Request = 0;
//	private $Answer =  array(	"state" => "undefined", 
//								"content" => array()
//							);
	// Initialize the class
	public function __construct () {
		echo "zwei";
		// Get POST Message and globalize it.
		$this->getPostMessage();
		// Determine the kind of request and create the answer.
		$this->demux();
		// Answer the request.
		$this->SendAnswer();
	}
	
	private function getPostMessage(){
		echo "drei";
		$postdata = file_get_contents("php://input");
		$this->Request = json_decode($postdata);
	}
	// demultiplex :)
	private function demux(){
		echo "vier";
/*		switch ($this->Request['state']){
			case "init":
				echo "funf";
				//$this->initAnswer();
			break;
			case "reset":
				$this->resetAnswer();
			break;
			case "generate":
				$this->generateAnswer();
			break;
			case "download":
				$this->downloadAnswer();
			break;*/
		}
	}
/*	private function initAnswer(){
		$this->Answer['state'] = 'init';

		//Check if file exists and get date.
		if (file_exists($pp_path)) {
			$datum = date("d.m.Y, H:i", filemtime($this->pp_path));
		} else {
			$datum = "noFile";
		}
		// create Answer content
		$content = array(	"datum" => $datum;
							"config" => file_get_contents($this->cfg_path);
						);
		$this->Answer['content'] = $content;
		
	}
	private function resetAnswer(){

	}
	private function generateAnswer(){

	}
	private function downloadAnswer(){

	}

	// Sending request return.
	private function SendAnswer(){
		echo json_encode($this->Answer);
	}

}
	$req = new putzplanAPI();
*/
?>
