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
	public function index()
	{	
		if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }
		$data['title'] = 'Home';
		$data['clientDeal'] = $this->client_deal->getClientsDeals();
		$this->load->view('/templates/header', $data);
		$this->load->view('home');
		$this->load->view('/templates/footer');
	}
	public function importData(){
		$this->load->library('form_validation');
		$this->load->helper('file');
		$this->load->library('CSVReader');
		$allData = array();
		$clientData = array();
		$dealData = array();
		if($this->input->post('importSubmit')){
			$this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
			if($this->form_validation->run() == true){
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					$csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_name']);
					if(!empty($csvData)){
						$this->client_deal->TruncateAllClientDeal();
						foreach($csvData as $rowData){
							list($cname, $cid) = explode (" @", $rowData['client']);

							$clientData = array(
                                'cid' => $cid,
                                'cname' => $cname
							);

							$this->client_deal->addClient($clientData);

							list($dname, $did) = explode(" #", $rowData['deal']);
							$rowData['dname'] = $dname;
							$rowData['did'] = $did;
							$dealData = array(
                                'did' => $did,
								'dname' => $dname
							);

							$this->client_deal->addDeal($dealData);

							$clientDealData = array(
								'cid' => $cid,
                                'did' => $did,
								'hour'=> $rowData['hour'],
								'accepted' => $rowData['accepted'],
								'refused' => $rowData['refused']
							);

							$this->client_deal->addClientDeal($clientDealData);
						}

						$this->session->set_userdata('success_msg', 'Data imported successfully');
					}
				}else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
				}
						
			}else{
				$this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
			}
		}
		redirect('/');
	}

	public function file_check($str){
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != ""){
            $mime = get_mime_by_extension($_FILES['file']['name']);
            $fileAr = explode('.', $_FILES['file']['name']);
            $ext = end($fileAr);
            if(($ext == 'csv') && in_array($mime, $allowed_mime_types)){
                return true;
            }else{
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        }else{
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }

}
