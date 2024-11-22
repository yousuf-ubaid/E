<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\NotFoundException;
use function is_int;

/**
 * Class DocumentAttachmentService
 * 
 * @package App\Src\Services
 */
final class DocumentAttachmentService extends Service
{
   /**
   * Constructor
   */
   public function __construct() {
      parent::__construct();
      $this->ci->load->model('DocumentAttachment_model');
      $this->ci->load->library('s3');
   }

   /**
    * Get attachments of a company update by ID
    *
    * @param array<string, mixed> $data
    * @throws NotFoundException 
    * @return array<int, mixed>
    */
   public function getByDocumentId(array $data): array
   {
      $id = is_int($data['id']) ? $data['id'] : null;
      $documentID = $data['documentID'];

      $attachments = $this->ci->DocumentAttachment_model->getByDocumentId($id, $documentID);
      if(empty($attachments)) {
         throw new NotFoundException('No record found');
      }

      foreach ($attachments as &$attachment) {
         $attachment['myFileName'] = $this->ci->s3->createPresignedRequest($attachment['myFileName']);
         $attachment['iconClass'] = $this->getIconClass(strtolower($attachment['fileType']));
      }

      return $attachments;
   }

   /**
    * Get icon class for file type
    *
    * @param string $fileType
    * @return string
    */
   private function getIconClass(string $fileType): string
   {
      $iconClass = 'fa fa-file-o'; 

      switch ($fileType) {
         case 'pdf':
            $iconClass = 'fa fa-file-pdf-o';
            break;
         case 'jpg':
         case 'jpeg':
         case 'png':
         case 'gif':
            $iconClass = 'fa fa-file-image-o';
            break;
         case 'doc':
         case 'docx':
            $iconClass = 'fa fa-file-word-o';
            break;
         case 'xls':
         case 'xlsx':
            $iconClass = 'fa fa-file-excel-o';
            break;
         default:
            $iconClass = 'fa fa-paperclip';
      }

      return $iconClass;
   }

}