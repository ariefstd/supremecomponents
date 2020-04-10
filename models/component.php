<?php
class Component extends CI_Model
{
// file model (file model name component)	
	public function get_all($key = null, $value = null) {
		if($key != null) {
			$query  = $this->db->get_where('coba_test', array($key => $value));
			return $query->result();
		}
		$query  = $this->db->get('coba_test');
		return $query->result();
	}
}