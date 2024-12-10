<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Company_updates_model
 */
final class Company_updates_model extends CI_Model
{
   /**
    * Constructor
    * 
    */
   public function __construct()
   {
      parent::__construct();
      $this->load->library('S3');
   }

   /**
    * Create a new company update.
    * 
    * @param array $data 
    * @return bool 
    */
   public function create(array $data): bool
   {
      $expiryDateFormat = date('Y-m-d H:i:s', strtotime($data['expiryDate']));
      $insertData = [
         'title'            => $data['title'],
         'description'      => $data['description'],
         'expiryDate'       => $expiryDateFormat,
         'companyID'        => current_companyID(),
         'createdUserGroup' => current_user_group(),
         'createdPCID'      => $this->common_data['current_pc'],
         'createdUserID'    => $this->common_data['current_userID'],
         'createdDatetime'  => $this->common_data['current_date'],
         'createdUserName'  => $this->common_data['current_user']
      ];

      return $this->db->insert('srp_erp_companyupdates', $insertData);
   }

   /**
    * Update an existing company update by ID.
    * 
    * @param int $id 
    * @param array $data 
    * @return bool 
    */
   public function update(int $id, array $data): bool
   {
      $updateData = [
         'title'            => $data['title'] ?? null, 
         'description'      => $data['description'] ?? null,
         'expiryDate'       => date('Y-m-d H:i:s', strtotime($data['expiryDate'] ?? 'now')),
         'modifiedDateTime' => $this->common_data['current_date'],
         'modifiedUserID'   => $this->common_data['current_userID'],
         'modifiedPCID'     => $this->common_data['current_pc'],
         'modifiedUserName' => $this->common_data['current_user'],
     ];
 
      $this->db->where('id', $id);
      return $this->db->update('srp_erp_companyupdates', $updateData);
   }

   /**
    * Delete a company update by ID.
    * 
    * @param int $id
    * @return bool 
    */
   public function delete(int $id): bool
   {
      return $this->db->delete('srp_erp_companyupdates', ['id' => $id]);
   }

   /**
    * Retrieve all company updates with view count.
    * 
    * @return array 
    */
   public function getAll(): array
   {
      $this->db->select('cu.*, COALESCE(view_counts.view_count, 0) as view_count');
      $this->db->from('srp_erp_companyupdates cu');
      $this->db->join('(SELECT notificationID, COUNT(*) as view_count 
                        FROM srp_erp_notificationview 
                        WHERE documentID = "CU" 
                        GROUP BY notificationID) view_counts', 
                        'view_counts.notificationID = cu.id', 'left');
      $this->db->where('cu.companyID', current_companyID());
      $this->db->where('cu.expiryDate >', (new DateTime())->format('Y-m-d H:i:s'));
      $this->db->group_by('cu.id');
      $this->db->order_by('cu.id', 'DESC');

      $query = $this->db->get();
      return $query->result_array();
   }

   /**
    * Retrieve a single company update by ID.
    * 
    * @param int $id 
    * @return array 
    */
   public function getById(int $id): array 
   {
      $convertFormat = convert_date_format_sql(). ' %h:%i %p';
      $this->db->select('*,DATE_FORMAT(expiryDate,\''.$convertFormat.'\') AS expiryDate');
      $this->db->where('id', $id);
      return $this->db->get('srp_erp_companyupdates')->row_array();
   }

   /**
    * Load all company updates for display in a datatable format.
    * 
    * @return void 
    */
   function loadCompanyUpdates()
   {
      $convertFormat = convert_date_format_sql(). ' %h:%i %p';
      $this->datatables->select('id,title,description,DATE_FORMAT(expiryDate,\''.$convertFormat.'\') AS expiryDate')
         ->where('companyID', $this->common_data['company_data']['company_id'])
         ->from('srp_erp_companyupdates')
         ->edit_column('action', '<div class="btn-group" style="display: flex;justify-content: center;">
                     <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                           Actions <span class="caret"></span>
                     </button>
                  <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                     <li><a onclick="editCompanyUpdatesModal($1)"><span class="glyphicon glyphicon-pencil" style="color:blue;"></span> Edit</a></li>
                     <li><a onclick="deleteUpdate($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>
                     <li><a onclick="attachment_modal($1, \'Company Updates\', \'CU\', true)"><span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>
                  </ul>
               </div>', 'id,title,description,expiryDate');
      echo $this->datatables->generate();
   }

}