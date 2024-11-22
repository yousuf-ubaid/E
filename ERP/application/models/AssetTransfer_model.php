<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class AssetTransfer_model
 */
final class AssetTransfer_model extends CI_Model
{
   /**
    * Constructor
    * 
    */
   public function __construct()
   {
      parent::__construct();
      $this->load->helper('asset_transfer');
   }

   /**
    * fetch Assets 
    *
    * @return array
    */
   public function fetchAssets(): array
   {
      $search_query = "%" . $this->input->get('query') . "%";

      $dataArr = [];

      $this->db->select('
         srp_erp_fa_asset_master.faID,
         srp_erp_fa_asset_master.faCode,
         srp_erp_fa_asset_master.assetDescription,
         CONCAT(IFNULL(faCode, "empty"), " - ", IFNULL(assetDescription, "empty")) AS "Match",
      ');
      $this->db->from('srp_erp_fa_asset_master');
      $this->db->where('srp_erp_fa_asset_master.companyID', current_companyID());
      $this->db->where('srp_erp_fa_asset_master.currentLocation', $this->input->get('location'));
      $this->db->where('srp_erp_fa_asset_master.confirmedYN', 1);

      $this->db->group_start();
      $this->db->where("faCode LIKE '$search_query'");
      $this->db->or_where("assetDescription LIKE '$search_query'");
      $this->db->group_end();
      
      $this->db->group_by('srp_erp_fa_asset_master.faID');
      
      $query = $this->db->get();
      $data = $query->result_array();

      if (!empty($data)) {
         foreach ($data as $val) {
               $dataArr[] = array(
                  'value'            => $val["Match"], 
                  'data'             => $val['faCode'], 
                  'faID'             => $val['faID'], 
                  'assetDescription' => $val['assetDescription'],
               );
         }
      }

      return ['suggestions' => $dataArr];
   }

   /**
    * Load Asset transfer
    *
    * @return void
    */
   public function getAssetTransfer()
   {
      $convertFormat = convert_date_format_sql();
      
      $this->datatables->select('srp_erp_fa_asset_transfer.id as id, documentCode, 
               DATE_FORMAT(documentDate, \'' . $convertFormat . '\') AS documentDate, 
               fromLocation.locationName AS locationFromName, 
               toLocation.locationName AS locationToName, 
               emp.Ename1 AS requestedEmpName, 
               confirmedYN, 
               status')
      ->from('srp_erp_fa_asset_transfer')

      ->join('srp_erp_location AS fromLocation', 'fromLocation.locationID = srp_erp_fa_asset_transfer.locationFromID', 'left')
      ->join('srp_erp_location AS toLocation', 'toLocation.locationID = srp_erp_fa_asset_transfer.locationToID', 'left')
      ->join('srp_employeesdetails as emp', 'emp.EidNo = srp_erp_fa_asset_transfer.requestedEmpID', 'left')

      ->where('srp_erp_fa_asset_transfer.companyID', $this->common_data['company_data']['company_id'])
      ->where('srp_erp_fa_asset_transfer.isDeleted', 0)
      ->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"FAT",id)')
      ->edit_column('action','$1','getAssetTransferAction(id, confirmedYN)');

      echo $this->datatables->generate();
   }

   /**
    * Load all Asset details to display
    *
    * @return void
    */
   function getAssetdetail()
   {   
      $this->db->select('id,faID,faDescription,comment');
      $this->db->where('companyID', current_companyID());
      $this->db->from('srp_erp_fa_asset_master');
      return $this->db->get()->result_array();
   }

   /**
    * Fetch Asset details by ID
    *
    * @param int $faId
    * @return array
    */
   public function getById(int $faId): array
   {
      $this->db->select('*');
      $this->db->where('faID', $faId);
      $this->db->where('confirmedYN', 1);
      $this->db->where('companyID', current_companyID());
      return $this->db->get('srp_erp_fa_asset_master')->row_array();
   }
}