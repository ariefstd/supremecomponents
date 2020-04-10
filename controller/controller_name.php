<?php
			
	// file controller
defined('BASEPATH') OR exit('No direct script access allowed');
use  \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\ExpiredException;
// This original library is available at github 
require_once APPPATH .'libraries/JWT.php';
require_once APPPATH .'libraries/ExpiredException.php';
require_once APPPATH .'libraries/BeforeValidException.php';
require_once APPPATH .'libraries/SignatureInvalidException.php';

use Restserver\Libraries\REST_Controller;
// import library dari REST_Controller
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Controller_name extends CI_Controller {
	
	private $secret = "This is a secret key";
	public function __construct() {
		parent::__construct();
		$this->load->model('component');
		//$this->load->library('ion_auth');
		///Allowing CORS
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');		
	}
	public function response($data, $status = 200) {
		$this->output
			 ->set_content_type('application/json')
			 ->set_status_header($status)
			 ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
			 ->_display();
		exit;
	}	
	public function get_component() {
		return $this->response($this->component->get_all());
	}
		// get login from other project in the same server and post into function below
	public function login(){		
		header("Access-Control-Allow-Origin: http://localhost/user_login/"); // get access login from other project in the same server
		header("Content-Type: application/json; charset=UTF-8");
		header("Access-Control-Allow-Methods: POST");
		header("Access-Control-Max-Age: 3600");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

		// get posted data
		$data = json_decode(file_get_contents("php://input"));
		
		// set product property values
		$username = $data->username;
		$password = $data->password;
		
		if ($this->ion_auth->login($username, $password))
        {
			$user = $this->user->get_all('username',$username);
			$date = new DateTime();
			$payload['username'] 	= $user[0]->username;
			$payload['emailna'] 	= $user[0]->email;
			$payload['iat'] 	= $date->getTimestamp();
			$payload['exp'] 	= $date->getTimestamp() + 60*1;

			//$output['id_token'] = JWT::encode($payload, $this->secret);
			$output['token'] =JWT::encode($payload, $this->secret);
			echo json_encode(
				array(
					"message" => "Successful login.",
					"jwt" => $output['token']
				)
			);
			//$this->response($output, REST_Controller::HTTP_OK);		
		}else{
			return $this->response([
				'success'	=> false,
				'message'	=> 'Password or username is wrong'
			], REST_Controller::HTTP_UNAUTHORIZED);
		}		
		// files for jwt will be here
	}
}