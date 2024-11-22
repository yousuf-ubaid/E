<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class AssetLocation_model
 */
class AssetLocation_model extends ERP_Model
{
   /**
    * Constructor
    * 
    */
   function __construct()
   {
      parent::__construct();
   }

   /**
    * Fetch location details by ID
    *
    * @param int $locationID
    * @return array
    */
   public function getById(int $locationID): array
   {
      $this->db->select('*');
      $this->db->where('locationID', $locationID);
      $this->db->where('companyID', current_companyID());
      return $this->db->get('srp_erp_location')->row_array();
   }

}