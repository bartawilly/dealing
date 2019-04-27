<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Client_deal extends CI_Model{
    
    public function __construct()
    {
        $this->load->database();
    }
    public function getClientsDeals(){
        $this->db->
        select('*')
        ->from('clients_deals as cd')
        ->join('client as c', 'c.cid = cd.cid')
        ->join('deal as d', 'd.did = cd.did');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getClient($id=''){
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
    public function getDeal($id=''){
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
    public function addClient($client = array()){
        $clientEX =  $this->getClient($client['cid']);
        if($clientEX){
            $this->db->where('cid', $client['cid']);
            $this->db->update('client', $client);
            
        }
        else{
            $this->db->insert('client', $client);
        }

    }
    public function addDeal($deal = array()){
        $dealEX =  $this->getDeal($deal['did']);
        if($dealEX){
            
            $this->db->where('did', $deal['did']);
            $this->db->update('deal', $deal);
        }
        else{
            $this->db->insert('deal', $deal);
        }

    }
    public function addClientDeal($clientDeal = array()){
     
        if(!empty($clientDeal)){
        $this->db->insert('clients_deals', $clientDeal);
     }

    }
    public function TruncateAllClientDeal(){
     
        $this->db->truncate('clients_deals');

    }
    
}


?>