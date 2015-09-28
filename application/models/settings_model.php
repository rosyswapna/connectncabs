<?php 
class Settings_model extends CI_Model {

        var $organisation_id;
	public function __construct()
	{ 
		parent::__construct();
		$this->organisation_id=$this->session->userdata('organisation_id');
		

	}
	
	
	public function addValues($tbl,$data){
	$this->db->set('created', 'NOW()', FALSE);
	$this->db->insert($tbl,$data); 
	return true;
	}

	public function addValues_returnId($tbl,$data){
	$this->db->set('created', 'NOW()', FALSE);
	$this->db->insert($tbl,$data);
	return $this->db->insert_id();
	}

        function get_autocomplete_array($q, $table, $name){

                if($name == '')
			return false;

		$this->db->where('organisation_id',$this->organisation_id);
		$this->db->like($name, $q, 'after'); 
		$this->db->order_by($name);
		$qry = $this->db->get($table);//echo $this->db->last_query();exit;
		if($qry->num_rows() > 0){
			return $qry->result_array();
		}else{
			return false;
		}
       }

	public function getValues($id,$tbl){ 
	$this->db->select('id,description,name');
	$this->db->from($tbl);
	$this->db->where('id',$id );
	 //newly added-to be organisation based
		$org_id=$this->session->userdata('organisation_id');
		$this->db->where( 'organisation_id', $org_id );
		//---
	return $this->db->get()->result_array();
	
	}
	public function updateValues($tbl,$data,$id){
	$this->db->where('id',$id );
	//newly added-to be organisation based
		$org_id=$this->session->userdata('organisation_id');
		$this->db->where( 'organisation_id', $org_id );
	//---
	 $this->db->set('updated', 'NOW()', FALSE);
	$this->db->update($tbl,$data);
	return true;
	}
	public function deleteValues($tbl,$id){
	$this->db->where('id',$id );
	 //newly added-to be organisation based
		$org_id=$this->session->userdata('organisation_id');
		$this->db->where( 'organisation_id', $org_id );
		//---
	$this->db->delete($tbl);
	return true;
	}
	
	
}
?>
