<?php  

	$this->load->view('admin/header', array('title' => $title));
	$this->load->view('admin/navbar');
	$this->load->view($content);
	$this->load->view('admin/footer');

?>