<?php
/**
 *
 */
class Detail_permintaan_m extends MY_Model
{

  function __construct()
  {
    parent::__construct();
    $this->data['primary_key'] = 'id_detail_permintaan';
    $this->data['table_name'] = 'detail_permintaan';
  }
}

 ?>
