<?php  

class Feedback_m extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table_name 	= 'feedback';
		$this->pk 			= 'id_feedback';
	}
}