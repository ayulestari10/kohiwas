<?php

	$this->load->view('operator_unit/template/header', array('title' => $title));
	$this->load->view('operator_unit/template/navbar');
	$this->load->view('operator_unit/template/sidebar');
	$this->load->view($content);
	$this->load->view('operator_unit/template/footer');
?>
