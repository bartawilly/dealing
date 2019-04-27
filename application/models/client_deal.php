<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 // Client & Deal model for handling the database operations & querries and data importing
class Client_deal extends CI_Model{
    public function __construct()
    {
        
    }
    // Initialize the database and tables also save the credentials to json file
    public function intialize($username, $password){
        // set the credentials json file path
        $credentialsFile = APPPATH. "db_credentials.json";
        // Get the database credentials variables note database name is static as default
        $servername = "localhost";
        $username = $username;
        $password = $password;
        $db = "client_deal";
        // Create mysql connection
        $conn = new mysqli($servername, $username, $password);
        // Check the connection
        if ($conn->connect_error) {
            // return message to user to check the credentials he entered
            $this->session->set_userdata('error_credentials', 'check MySql username & password!');
        } else{
            
            $credentials = array('servername'=> $servername,'username'=> $username, 'password'=> $password, 'db' =>$db );
            // check if the json file is already exist
            if (file_exists($credentialsFile)){
                unlink($credentialsFile);
            }
            // save the credentials to the json file 
            $fp = fopen($credentialsFile, 'w');
            fwrite($fp, json_encode($credentials));
            fclose($fp);
            
            
        

        // after a successfull connection start for run some queries to initialize the database and tables

        // staring here with some default configs for the database
        $sql = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"';
        $conn->query($sql);
        $sql = 'SET AUTOCOMMIT = 0';
        $conn->query($sql);
        $sql = 'START TRANSACTION';
        $conn->query($sql);
        $sql = 'SET time_zone = "+00:00"';
        $conn->query($sql);

        // Create database if it isn't exist
        $sql = 'CREATE DATABASE IF NOT EXISTS `client_deal` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
        if ($conn->query($sql) === TRUE) {
            echo "Database created successfully";
        } else {
            echo "Error creating database: " . $conn->error;
        }

        // Use the client_deal DB for the upcomming querries
        $sql = 'USE `client_deal`';
        $conn->query($sql);

        // Create client Table
        $sql = 'DROP TABLE IF EXISTS `client`';
        $conn->query($sql);
        $sql = 'CREATE TABLE IF NOT EXISTS `client` (
            `cid` int(11) NOT NULL AUTO_INCREMENT,
            `cname` varchar(512) NOT NULL,
            PRIMARY KEY (`cid`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $conn->query($sql);

        // Create clients_deals Table
        $sql = 'DROP TABLE IF EXISTS `clients_deals`';
        $conn->query($sql);
        $sql = 'CREATE TABLE IF NOT EXISTS `clients_deals` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `cid` int(11) NOT NULL,
            `did` int(11) NOT NULL,
            `hour` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `accepted` int(11) NOT NULL,
            `refused` int(11) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $conn->query($sql);

        // Create deal Table
        $sql = 'DROP TABLE IF EXISTS `deal`';
        $conn->query($sql);
        $sql = 'CREATE TABLE IF NOT EXISTS `deal` (
            `did` int(11) NOT NULL AUTO_INCREMENT,
            `dname` varchar(512) NOT NULL,
            PRIMARY KEY (`did`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $conn->query($sql);

        //Commit
        $sql = 'COMMIT';
        $conn->query($sql);

        //close the db connection
        $conn->close();

        //show success message to the end user
        $this->session->set_userdata('success_msg', 'DB and Tables have been created successfully you can import CSV data now.');
        }
    }
    // this function checks if the database connection is successfully done before starting with any operation
    public function dbCheckConnection(){
        // first check is the file has been saved and already existed 
        $credentialsFile = APPPATH. "db_credentials.json";
        if (file_exists($credentialsFile)){
        // read the database credentials from the json file
        $cred = file_get_contents($credentialsFile, true);
        $credentials = json_decode($cred, true);
            
        // Create db connection
        $conn = mysqli_connect($credentials['servername'], $credentials['username'], $credentials['password']);

        // check the db connection
        if ($conn && mysqli_select_db($conn, $credentials['db'])) {
            return true;

        } else{
            // if the file already exist by wronge and contains a wronge credentials popup a message for the user to re-initialize
           $this->session->set_userdata('error_msg','error while connecting to DB wronge crendentials saved! please re-initialize!');
           return false;
        }
        }
        else{
            // if the file is not exist anymore popup a message for the end user to initialize first
            $this->session->set_userdata('error_msg','error while connecting to DB please initialize first!');
            return false;
         }
    }
    // connect to the database with the credentials after a successfull intialize and before procceding with any extra operations
    // note this function have no constraints or conditions as all already checked in the above dbCheckConnection function
    // so it will work directly to get the credentials from the json file and connect to the database
    public function dbConnect(){
        // get the credentials from the json file
        $credentialsFile = APPPATH. "db_credentials.json";
        $cred = file_get_contents($credentialsFile, true);
        $credentials = json_decode($cred, true);
        // pass the database configurations
            $config['hostname'] = $credentials['servername'];
            $config['username'] = $credentials['username'];
            $config['password'] = $credentials['password'];
            $config['database'] = $credentials['db'];
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = '';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = TRUE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $this->load->database($config); 
    }
  
    // get all the clients with the deals from the database
    public function getClientsDeals(){
        $this->dbConnect();
        $this->db->
        select('*')
        ->from('clients_deals as cd')
        ->join('client as c', 'c.cid = cd.cid')
        ->join('deal as d', 'd.did = cd.did');
        $query = $this->db->get();
        return $query->result_array();
    }
    // get a specific client or all the clients depending on the optional id param
    public function getClient($id=''){
        $this->dbConnect();
        if($id){
            $this->db->
            select('*')
            ->from('client')
            ->where('client.cid', $id);
            $query = $this->db->get();
        }
        else{
            $this->db->
            select('*')
            ->from('client');
            $query = $this->db->get();
        }
        return $query->result_array();
    }
    // get a specific deal or all the deals depending on the optional id param
    public function getDeal($id=''){
        $this->dbConnect();
        if($id){
            $this->db->
            select('*')
            ->from('deal')
            ->where('deal.did', $id);
            $query = $this->db->get();
        }
        else{
            $this->db->
            select('*')
            ->from('deal');
            $query = $this->db->get();
        }
        return $query->result_array();
    }
    // add client to the client table in the database
    public function addClient($client = array()){
        $this->dbConnect();
        $clientEX =  $this->getClient($client['cid']);
        if($clientEX){
            $this->db->where('cid', $client['cid']);
            $this->db->update('client', $client);
            
        }
        else{
            $this->db->insert('client', $client);
        }

    }
    // add deal to the deal table in the database
    public function addDeal($deal = array()){
        $this->dbConnect();
        $dealEX =  $this->getDeal($deal['did']);
        if($dealEX){
            
            $this->db->where('did', $deal['did']);
            $this->db->update('deal', $deal);
        }
        else{
            $this->db->insert('deal', $deal);
        }

    }
    // add a new row to the clients_deals table in the database tha containing all the intersections between the clients and deals
    public function addClientDeal($clientDeal = array()){
        $this->dbConnect();
        if(!empty($clientDeal)){
        $this->db->insert('clients_deals', $clientDeal);
     }

    }
    // truncate the clients_deals table if needed to avoid the duplication
    public function TruncateAllClientDeal(){
        $this->dbConnect();
        $this->db->truncate('clients_deals');

    }
    
}


?>