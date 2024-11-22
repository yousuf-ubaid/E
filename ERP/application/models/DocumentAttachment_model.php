<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class DocumentAttachment_model
 */
final class DocumentAttachment_model extends CI_Model
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
    * Get attachments for a company update
    * 
    * @param int $id 
    * @param string $documentID
    * @return array 
    */
   public function getByDocumentId(int $id, string $documentID): array 
   {
      $this->db->select('myFileName, attachmentDescription, fileSize, fileType');
      $this->db->where('documentSystemCode', $id);
      $this->db->where('documentID', $documentID);
      return $this->db->get('srp_erp_documentattachments')->result_array();
   }
}