<?php 

class MY_Model extends CI_Model
{
	protected $table_name;
	protected $pk;

	public function __construct()
	{
		parent::__construct();
	}

	public function get($c = array())
	{
		if (count($c) > 0)
		{
			$this->db->where($c);
		}

		$query = $this->db->get($this->table_name);
		return $query->result();
	}
}
