<?php

class MFQ_SegmentMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_segment_model');
    }

    function fetch_segments()
    {
        $this->datatables->select('rowTbl.description as masterdescription,masterTbl.mfqSegmentID as mfqSegmentID, masterTbl.segmentCode  AS  segmentCode, masterTbl.description  AS  description, masterTbl.isFromERP as isFromERP, masterTbl.serialNo as serialNo', false)
            ->from('srp_erp_mfq_segment as masterTbl')
            ->join('srp_erp_segment rowTbl', 'rowTbl.segmentID = masterTbl.segmentID', 'left')
            ->where('masterTbl.companyID', current_companyID())
            ->where('masterTbl.levelNo', 0);
        $this->datatables->add_column('edit', '$1', 'edit_mfq_segment(mfqSegmentID, isFromERP)');

        $result = $this->datatables->generate();
        echo $result;
    }


    function fetch_sync_segment()
    {
        $this->datatables->select('masterTbl.segmentID as segmentID, masterTbl.companyCode as companyCode, masterTbl.description  as  description', false)
            ->from('srp_erp_segment as masterTbl')
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_segment WHERE srp_erp_mfq_segment.segmentID = masterTbl.segmentID AND companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('edit', '$1', 'edit(segmentID,isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'segmentID');

        $result = $this->datatables->generate();
        echo $result;
    }
    function fetch_link_segment()
    {
        $this->datatables->select('masterTbl.segmentID as segmentID, masterTbl.companyCode as companyCode, masterTbl.description  as  description', false)
            ->from('srp_erp_segment as masterTbl')
            ->where('status',1)
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_segment WHERE srp_erp_mfq_segment.segmentID = masterTbl.segmentID AND companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('edit', '$1', 'edit(segmentID,isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkItem_$1" name="linkItem" type="radio"  value="$1" class="radioChk" data-itemAutoID="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'segmentID');

        $result = $this->datatables->generate();
        echo $result;
    }
    function link_segment(){
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_segment_model->link_segment());
        }

    }

    function add_segments()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_segment_model->add_segment());
        }
    }

    function add_edit_segment()
    {
        $mfqSegmentID = $this->input->post('mfqSegmentID');

        $this->form_validation->set_rules('segmentCode', 'Segment Code', 'trim|required');
        $this->form_validation->set_rules('description', 'description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            if ($mfqSegmentID) {
                /** Update */
                echo json_encode($this->MFQ_segment_model->update_segment());
            } else {
                /** Insert */
                echo json_encode($this->MFQ_segment_model->insert_segment());
            }
        }
    }

    function loadSegmentDetail()
    {
        $result = $this->MFQ_segment_model->get_srp_erp_mfq_segment();
        if (!empty($result)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'loading segment detail'), $result));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'record not found!'));
        }
    }
    function save_subsegment()
    {
        $this->form_validation->set_rules('mfqSegmentID_sub', 'Mfq Segment', 'required');
        $this->form_validation->set_rules('segmentCode', 'Segment Code', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_segment_model->save_subsegment());
        }
    }
    function get_mfq_sub_segment()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $mfqSegmentID = trim($this->input->post('mfqSegmentID') ?? '');
        $data["mfq_segmentID"] = $mfqSegmentID;
        $data['mfq_subsegment'] = $this->db->query("select mfqSegmentID,segmentID,masterSegmentID,segmentCode,description from 
                                                        srp_erp_mfq_segment where companyID = $companyID AND  levelNo = 1 AND masterSegmentID = $mfqSegmentID")->result_array();

        $this->load->view('system/mfq/mfq_sub_segment',$data);

    }
    function fetch_mfq_subsegment()
    {
        $type = trim($this->input->post('type') ?? '');
        $segmentID = $this->input->post('segmentID');
        $companyID = current_companyID();
        $subsegment = $this->db->query("select mfqSegmentID, CONCAT(segmentCode,' | ',description)as segmentcode from srp_erp_mfq_segment 
                                          where companyID = $companyID AND masterSegmentID = $segmentID ")->result_array();
        if($type == 1 ){
           echo json_encode($subsegment);
           exit();
        }else
        {
            $data_arr = array('' => 'Select a Sub Segment');
            if (!empty($subsegment)) {
                foreach ($subsegment as $row) {
                    $data_arr[trim($row['mfqSegmentID'] ?? '')] = trim($row['segmentcode'] ?? '');
                }
            }
            echo form_dropdown('mfqsubSegmentID', $data_arr, '', 'class="form-control select2" id="mfqsubSegmentID"');
        }


    }
}
