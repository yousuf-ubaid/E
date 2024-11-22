<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attachment extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function fetch_attachments()
    {
        $this->db->where('navigationMenuID', $this->input->post('documentSystemCode'));
        $data = $this->db->get('srp_erp_moduleattachment')->result_array();
        echo json_encode($data);
    }

    function do_upload($description = true)
    {

        if ($description) {
            $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
            $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
            $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        }
        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status'=>0,'type'=>'e','message'=>validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('navigationMenuID', trim($this->input->post('documentSystemCode')));
            $num = $this->db->get('srp_erp_moduleattachment')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(UPLOAD_PATH);
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status'=>0,'type'=>'w','message'=>'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];

                $data['navigationMenuID'] = trim($this->input->post('documentSystemCode'));

                $data['description'] = trim($this->input->post('attachmentDescription'));
                $data['file'] = $file_name . $upload_data["file_ext"];


                $this->db->insert('srp_erp_moduleattachment', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status'=>0,'type'=>'e','message'=>'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status'=>1,'type'=>'s','message'=>'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }

    function delete_attachment()
    {
        $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($this->input->post('attachmentID'))));
        echo json_encode(true);
    }
}

?>