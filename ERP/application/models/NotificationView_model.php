<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class NotificationView_model
 */
final class NotificationView_model extends CI_Model
{
   /**
    * Constructor
    * 
    */
   public function __construct()
   {
      parent::__construct();
   }

   /**
    * Create a notification view
    *
    * @param array $data 
    * @return bool
    */
   public function create(array $data): bool
   {
      $data = [
         'notificationID' => $data['id'],
         'documentID'     => $data['documentID'],
         'empID'          => $this->common_data['current_userID'],
         'viewedDate'     => date('Y-m-d H:i:s'),
         'companyID'      => current_companyID(),
         'createdPCID'    => $this->common_data['current_pc']
      ];
        
      return $this->db->insert('srp_erp_notificationview', $data);
   }

   /**
    * Retrieve a single company update by ID.
    * 
    * @param int $id 
    * @return array 
    */
   public function getById(int $id): array 
   {
      $this->db->select('*');
      $this->db->where('id', $id);
      return $this->db->get('srp_erp_companyupdates')->row_array();
   }
}