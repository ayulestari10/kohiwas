<?php
/**
 *
 */
class Permintaan_bahan_baku_m extends MY_Model
{

  function __construct()
  {
    parent::__construct();
    $this->data['primary_key'] = 'id_permintaan';
    $this->data['table_name'] = 'permintaan_bahan_baku';
  }
}

 ?>
