<?php
   /*
   * Collect all Details from Angular HTTP Request.
   */
//header("Content-type:application/x-www-form-urlencoded");
class putzplanAPI {
	private	$builddir = "build";
	private	$putzplan = "Putzplan.pdf";
	private $config   = "config.ini";

	private $pp_path = " ";
	private $cfg_path = " ";
	private $Request = 0;
	private $Return =  array(	"state" => "undefined",
								"content" => array()
							);
	// Initialize the class
	public function __construct () {
		$this->pp_path  = "$this->builddir/$this->putzplan";
		$this->cfg_path = "$this->builddir/$this->config";
		// Get POST Message, decode and globalize it.
		$this->getPostMessage();
		// Determine the kind of request and create the answer.
		$this->demux();
		// Answer the request.
		$this->SendReturn();
	}
	private function getPostMessage(){
		$postdata = file_get_contents("php://input");
		$this->Request = json_decode($postdata);
	}

	// demultiplex :)
	private function demux(){
		$state = $this->Request->state;
		switch ($state) {
			case "init":
				$this->initReturn();
			break;
			case "reset":
				$this->resetReturn();
			break;
			case "save":
				$this->saveReturn();
			break;
			case "generate":
				$this->generateReturn();
			break;
			case "download":
				$this->downloadReturn();
			break;
		}
	}
	//INIT
	private function initReturn(){
		$this->Return['state'] = 'init';

		//Check if file exists and get date.
		if ( file_exists($this->pp_path) ) {
			$datum = date("d.m.Y, H:i", filemtime($this->pp_path));
		} else {
			$datum = "noFile";
		}
		// create Answer content
		$content = array(	"datum" => $datum,
							"config" => file_get_contents($this->cfg_path)
						);
		$this->Return['content'] = $content;
		
	}
	// RESET
	private function resetReturn(){
		$this->Return['state'] = 'reset';
		$content = array(	"config" => file_get_contents($this->cfg_path)
						);
		$this->Return['content'] = $content;
	}
	//SAVE
	private function saveReturn(){
		$this->Return['state'] = 'save';
		// Save the new configuration on harddrive
		if( file_put_contents($this->cfg_path, htmlspecialchars($this->Request->content->config) ) )
			$status = "success";
		else
			$status = "failed";
		$content = array(	"status" => $status
						);
		$this->Return['content'] = $content;
	}
	//GENERATE
	private function generateReturn(){
		$this->Return['state'] = 'generate';
		$command = "make clean $pp_path 2>&1";
		exec($command, $output, $return_var);
		
		if ($return_var == 0) {
        		$status = "success";
    		}else {
			$status = "failed";	
	    	}
		$content = array(	"status" => $status,
					"log" => $output
				);
		$this->Return['content'] = $content;
	}
	// DOWNLOAD
	private function downloadReturn(){
		$this->Return['state'] = 'download';
		if( file_exists($_SERVER['DOCUMENT_ROOT']."/build/Putzplan.pdf") )
			$status = "notavailable";
		else 
			$status = "available";
		$content = array(	"status" => $status,
							"pdfURL" => $this->pp_path
						);
		$this->Return['content'] = $content;
	}


	// Encode data and send request return.
	private function SendReturn(){
		echo json_encode($this->Return);
	}
}
	// Object invocation
	$req = new putzplanAPI();
?>
