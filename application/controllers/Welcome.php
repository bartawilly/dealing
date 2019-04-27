<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	// here is the home function '/' controller
	public function index()
	{	
		// check all the success and error message to pass them to the view

		//check if there is a success message and set it to the user session
		if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
		}
		//check if there is a error message and set it to the user session
        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
		}
		//check if there is a erro credentials message and set it to the user session
		if($this->session->userdata('error_credentials')){
            $data['error_credentials'] = $this->session->userdata('error_credentials');
            $this->session->unset_userdata('error_credentials');
		}
		// set the page title
		$data['title'] = 'Home';
		
		//check the database connection before procceed
		if($this->client_deal->dbCheckConnection()){
			$data['clientDeal'] = $this->client_deal->getClientsDeals();
		}else{
			// if the connection fails pass the error messages to the session that retrieved from the model
			$data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
		}
		// load the views and templates
		$this->load->view('/templates/header', $data);
		$this->load->view('home');
		$this->load->view('/templates/footer');
	}

	// import the CSV file that contains the data
	public function importData(){
		// first check the database connection 
		if($this->client_deal->dbCheckConnection()){
			// form validation library to check the form is valid
			$this->load->library('form_validation');
			// file helper to use uploading the file
			$this->load->helper('file');
			// external library saved in the libraries folder for reading the data from the CSV file
			$this->load->library('CSVReader');
			// initialize arrays that will get the data from the file

			$allData = array(); // this one will contain all the data
			$clientData = array(); // this one will contain the client data ID, Name
			$dealData = array(); // this one will contain the deal data ID, Name
			// check if the form is submitted
			if($this->input->post('importSubmit')){
				// form validation rules for the uploaded file
				$this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
				//if the validation is done
				if($this->form_validation->run() == true){
					// start fetching the uploaed file and parse it to csv reader function - CSVReader library - to get the data 
					if(is_uploaded_file($_FILES['file']['tmp_name'])){
						$csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
						// check if the the parsed data is not empty
						if(!empty($csvData)){
							//before staring we should check if any data was already in our clients_deals and if so it will be deleted to avoid the intersecion duplication
							$this->client_deal->TruncateAllClientDeal();
							// loop on the exported array csv data
							foreach($csvData as $rowData){
								//split the cell that contains the client name and id 
								list($cname, $cid) = explode (" @", $rowData['client']);
								// fill the client data array one by one
								$clientData = array(
									'cid' => $cid,
									'cname' => $cname
								);
								// add the client to the client table but note inside the addClient func it will check if the client is already exist
								// and if so it will only update and if not it will add it
								$this->client_deal->addClient($clientData);
								//split the cell that contains the deal name and id 
								list($dname, $did) = explode(" #", $rowData['deal']);
								$rowData['dname'] = $dname;
								$rowData['did'] = $did;
								$dealData = array(
									'did' => $did,
									'dname' => $dname
								);
								// add the deal to the deal table but note inside the addDeal func it will check if the deal is already exist
								// and if so it will only update and if not it will add it
								$this->client_deal->addDeal($dealData);
								
								// as we already check and truncate if any data already exist in clients_deals table
								// here we can safely add the clients deals details to the clients_deals table
								$clientDealData = array(
									'cid' => $cid,
									'did' => $did,
									'hour'=> $rowData['hour'],
									'accepted' => $rowData['accepted'],
									'refused' => $rowData['refused']
								);
								// add the clients deals details to the clients_deals table
								$this->client_deal->addClientDeal($clientDealData);
							}
							// popup a success message to the user after the csv data is imported successfully
							$this->session->set_userdata('success_msg', 'Data imported successfully');
						}
					}else{
						// if the file wasn't uploaded show an error message to the user to try import the file correctly again
						$this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
					}
							
				}else{
					// if the file is not a csv extention show an error message to the user
					$this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
				}
			}
		}
		// redirect to the home after finish the importing
		redirect('/');
	}
	// the call back function for the form validation rules to check the file uploaded is a csv file
	public function file_check($str){
		// here is the array of allowed 
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
		// check the file name is exist and not empty 
		if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != ""){
			// use the file helper to get the file mime
			$mime = get_mime_by_extension($_FILES['file']['name']);
			//split the file name to by . to get the extention
			$fileAr = explode('.', $_FILES['file']['name']);
			//get the file extention
			$ext = end($fileAr);
			//check if the file extention is withen the allowed mime types
            if(($ext == 'csv') && in_array($mime, $allowed_mime_types)){
                return true;
            }else{
				// if not csv file show an error message to the user
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        }else{
			//if the file is not uploaded by the user show him an error message
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
	}
	// here is a controller function to call the intialize function in the model to start creating the database and tables
	public function intialize(){
		// check if the username and password form is submitted successfully and pass the username and password to the intialize function in the model
		if($this->input->post('intializeSubmit')){
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$this->client_deal->intialize($username , $password);
			
		}else{
			//show an error message to the user if any error occures while submiting the form
			$this->session->set_userdata('error_msg', 'Error submitting you data, please try again.');
		}
		//redirect back to the home
		redirect('/');
	}
	// reset all func controller calls a model func that drop the entire database
	public function resetAll(){
		// first check the database connection 
		if($this->client_deal->dbCheckConnection()){
			//call the model func to reset all
			$this->client_deal->resetAll();
		}
		else{
			$this->session->set_userdata('error_msg', 'nothing here to be reseted!');
		}
		//redirect back to the home
		redirect('/');
	}

}
