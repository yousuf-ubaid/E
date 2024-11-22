<?php

class MFQ_BillOfMaterial extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_BillOfMaterial_model');
    }

    function fetch_bom()
    {
        $this->datatables->select('BoM.bomMasterID as bomMasterID, BoM.documentCode as documentCode,BoM.description as bomDescription, IF(BoM.mfqItemID = 0 || BoM.mfqItemID IS NULL, WFT.description, MFQI.itemDescription) as description , BoM.industryTypeID, IT.industryTypeDescription as industryTypeDescription', false)
            ->from('srp_erp_mfq_billofmaterial BoM')->join('srp_erp_mfq_industrytypes IT', 'IT.industrytypeID = BoM.industryTypeID', 'left')
            ->join('srp_erp_mfq_itemmaster MFQI', 'MFQI.mfqItemID = BoM.mfqItemID', 'left')
            ->join('srp_erp_mfq_workflowtemplate WFT', 'WFT.workFlowTemplateID = BoM.mfqProcessID', 'left');

        $this->datatables->where('BoM.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '$1', 'editBoM(bomMasterID)');
        echo $this->datatables->generate();
    }

    function fetch_related_uom_id()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_related_uom_id());
    }

    function fetch_related_uom_fn()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_related_uom_fn());
    }


    function load_unitprice_exchangerate()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->load_unitprice_exchangerate());

    }

    function add_edit_BoMMaster()
    {

        $fileExist = false;
        if (isset($_FILES['productImage']['name']) && !empty($_FILES['productImage']['name'])) {
            $fileExist = true;


            $path = './uploads/';
            $tmpImagePath = $_FILES['productImage']['name'];
            $ext = pathinfo($tmpImagePath, PATHINFO_EXTENSION);

            $fileName = 'mfq_product_' . time() . '.' . $ext;

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg';
            $config['max_size'] = '200000';
            $config['file_name'] = $fileName;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $this->upload->do_upload("productImage");
            $tmpData = $this->upload->data();
            $_POST['productImage'] = isset($tmpData['file_name']) ? $tmpData['file_name'] : '';
        }


        $bomMasterID = $this->input->post('bomMasterID');

        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('documentDate', 'Date', 'required');
        $this->form_validation->set_rules('industryTypeID', 'Industry Type', 'required');
        $this->form_validation->set_rules('Qty', 'Qty', 'required');
        $this->form_validation->set_rules('uomID', 'Unit of Measure', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            if ($bomMasterID) {
                /** Update */
                echo json_encode($this->MFQ_BillOfMaterial_model->update_BoM());
            } else {
                /** Insert */
                echo json_encode($this->MFQ_BillOfMaterial_model->insert_BoM());
            }
        }
    }

    function add_edit_BillOfMaterial()
    {
        $fileExist = false;

        if (isset($_FILES['productImage']['name']) && !empty($_FILES['productImage']['name'])) {
            $fileExist = true;

            $path = './uploads/';
            $tmpImagePath = $_FILES['productImage']['name'];
            $ext = pathinfo($tmpImagePath, PATHINFO_EXTENSION);

            $fileName = 'mfq_product_' . time() . '.' . $ext;

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg';
            $config['max_size'] = '200000';
            $config['file_name'] = $fileName;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $this->upload->do_upload("productImage");
            $tmpData = $this->upload->data();
            $_POST['productImage'] = isset($tmpData['file_name']) ? $tmpData['file_name'] : '';
        }

        $this->form_validation->set_rules('bomType', 'BOM type', 'required');
        $bomType = $this->input->post('bomType');
        if($bomType==1){
            $this->form_validation->set_rules('product', 'Item', 'required|callback_itemCheck[' . $this->input->post('bomMasterID') . ']');
        } else{
            $this->form_validation->set_rules('process', 'Process', 'required');
        }
        //$this->form_validation->set_rules('product', 'Item', 'required|callback_itemCheck[' . $this->input->post('bomMasterID') . ']');
        $this->form_validation->set_rules('documentDate', 'Date', 'required');
        // if (!$this->input->post("estimateDetailID")) {
        //     $this->form_validation->set_rules('industryTypeID', 'Industry Type', 'required');
        // }
        $this->form_validation->set_rules('Qty', 'Qty', 'required');
//        $this->form_validation->set_rules('uomID', 'Unit of Measure', 'required');
//        $this->form_validation->set_rules('search[]', 'Item', 'required');
//        $this->form_validation->set_rules('tps_search[]', 'Third Party Service', 'required');
//        $this->form_validation->set_rules('mc_search[]', 'Material Consumption', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            echo json_encode($this->MFQ_BillOfMaterial_model->add_edit_BillOfMaterial());
        }
    }

    function itemCheck($id, $bomMasterID)
    {
        $result = "";
        $company = getPolicyValues('LNG', 'All');

        if ($bomMasterID) {
            $this->db->select('mfqItemID');
            $this->db->from('srp_erp_mfq_billofmaterial');
            $this->db->where('mfqItemID', $id);
            $this->db->where('bomMasterID <>', $bomMasterID);
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->row_array();
        } else {
            $this->db->select('mfqItemID');
            $this->db->from('srp_erp_mfq_billofmaterial');
            $this->db->where('mfqItemID', $id);
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->row_array();
        }

        if($company = 'FlowServe'){
            return true;
        }else{

            if ($result) {
                $this->form_validation->set_message('itemCheck', 'Already a bom created for selected item');
                return FALSE;
            } else {
                return true;
            }

        }
       

    }

    function load_mfq_billOfMaterial()
    {
        $bomMasterID = $this->input->post('bomMasterID');
        $mfqItemMaster = $this->MFQ_BillOfMaterial_model->get_srp_erp_mfq_billofmaterial($bomMasterID);
        if (!empty($mfqItemMaster)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'done'), $mfqItemMaster));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'no record found!'));
        }
    }

    function load_mfq_billOfMaterial_detail()
    {
        $bomMasterID = $this->input->post('bomMasterID');
        echo json_encode($this->MFQ_BillOfMaterial_model->load_mfq_billOfMaterial_detail($bomMasterID));
    }


    function save_material_consumption()
    {
        $bomMaterialConsumptionID = $this->input->post('bomMaterialConsumptionID');

        $mfqItemID = $this->input->post('mfqItemID');
        if (!empty($mfqItemID)) {
            try {
                foreach ($mfqItemID as $key => $val) {
                    if (!empty($bomMaterialConsumptionID[$key])) {

                        $materialCost = ($this->input->post('markUp')[$key]) * ($this->input->post('qtyUsed')[$key]);
                        $materialCharge = $this->input->post('markUp')[$key] * ($materialCost / 100);

                        $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key]);
                        $this->db->set('qtyUsed', $this->input->post('qtyUsed')[$key]);
                        $this->db->set('unitCost', $this->input->post('unitCost')[$key]);
                        $this->db->set('materialCost', $materialCost);
                        $this->db->set('markUp', $this->input->post('markUp')[$key]);
                        $this->db->set('materialCharge', $materialCharge);

                        $this->db->set('crewID', $this->input->post('crewID')[$key]);
                        $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('modifiedUserID', current_userID());
                        $this->db->set('modifiedUserName', current_user());
                        $this->db->set('modifiedDateTime', current_date(true));
                        $this->db->where('bomMaterialConsumptionID', $bomMaterialConsumptionID[$key]);
                        $this->db->update('srp_erp_mfq_bom_materialconsumption');

                    } else {

                        $materialCost = ($this->input->post('markUp')[$key]) * ($this->input->post('qtyUsed')[$key]);
                        $materialCharge = $this->input->post('markUp')[$key] * ($materialCost / 100);

                        $this->db->set('bomMasterID', $this->input->post('bomMasterID'));
                        $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key]);
                        $this->db->set('qtyUsed', $this->input->post('qtyUsed')[$key]);
                        $this->db->set('unitCost', $this->input->post('unitCost')[$key]);
                        $this->db->set('materialCost', $materialCost);
                        $this->db->set('markUp', $this->input->post('markUp')[$key]);
                        $this->db->set('materialCharge', $materialCharge);
                        $this->db->set('modifiedPCID', current_pc());
                        $this->db->set('modifiedUserID', current_userID());
                        $this->db->set('modifiedDateTime', format_date_mysql_datetime());

                        $this->db->insert('srp_erp_mfq_bom_materialconsumption');
                        echo $this->db->last_query();
                    }
                }
                $this->db->trans_commit();
                return array('error' => 0, 'message' => 'Material Added Successfully.');

            } catch (Exception $e) {
                $this->db->trans_rollback();
                return array('error' => 1, 'message' => 'Error while adding material' . $this->db->_error_message());
            }
        }
    }

    function delete_materialConsumption()
    {
        $id = $this->input->post('bomMaterialConsumptionID');
        echo json_encode($this->MFQ_BillOfMaterial_model->delete_materialConsumption($id));
    }

    function load_bom_material_consumption()
    {
        $bomMasterID = $this->input->post('bomMasterID');
        $output = $this->MFQ_BillOfMaterial_model->load_bom_material_consumption($bomMasterID);
        echo json_encode($output);
    }


    /** Labour Task */
    function fetch_labourTask()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_labourTask());
    }

    function fetch_overhead()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_overhead());
    }

    function fetch_machine()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_machine());
    }

    function fetch_bom_labour_task()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_bom_labour_task());
    }

    function delete_labour_task()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->delete_labour_task());
    }

    function delete_overhead_cost()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->delete_overhead_cost());
    }

    function fetch_bom_overhead_cost()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_bom_overhead_cost());
    }

    function fetch_bom_machine_cost()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_bom_machine_cost());
    }

    function checkItemInBom()
    {
        $result = "";
        $company = getPolicyValues('LNG', 'All');

        if ($this->input->post("bomMasterID")) {
            $this->db->select('mfqItemID');
            $this->db->from('srp_erp_mfq_billofmaterial');
            $this->db->where('mfqItemID', $this->input->post("mfqItemID"));
            $this->db->where('bomMasterID <>', $this->input->post("bomMasterID"));
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->result_array();
        } else {
            $this->db->select('mfqItemID');
            $this->db->from('srp_erp_mfq_billofmaterial');
            $this->db->where('mfqItemID', $this->input->post("mfqItemID"));
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->result_array();
        }


        //Allowing same item multiple bom

        if($company = 'FlowServe'){
            echo json_encode(false);
        }else{

            if ($result) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }

        }

       
    }

    function deleteBOM()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->deleteBOM());
    }

    function load_segment_hours()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->load_segment_hours());
    }

    function fetch_third_party_service()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->fetch_third_party_service());
    }

    function delete_third_party_service_cost()
    {
        echo json_encode($this->MFQ_BillOfMaterial_model->delete_third_party_service_cost());
    }

    function bom_excelUpload()
    {
        $docID = $this->input->post('docID');
        $isdocTypeID = $this->input->post('isdocTypeID');
        $companyID = $this->common_data['company_data']['company_id'];
        $totalQty = 0;
       // $this->db->trans_start();
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
          //  print_r($type);exit;
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0;
            $x = 0;
            $n = 0;
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $filed = fopen($filename, "r");
            $dataExcel = [];
            $dataExcel2 = [];
            $dataExcel3 = [];
            $dataExcel4 = [];
            $last_id=0;
            $required_header = array();
            $upload_file_header=array();
            $required_header_details=array();
            $upload_file_header_detail=array();
            $required_header=['Product Type','Product Code','Date','Qty','',''];
            $required_header_details=['Type','Code','Name','Qty','Unit','Total'];

            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i==0){
                    $upload_file_header=$getData;

                    
                }

                if($i==3){
                    $upload_file_header_detail=$getData;
                }
                if ($i > 0) {
                 
                    if (!empty($getData[0])) {
                        $dataExcel[$i]['id'] = $getData[0];
                    }

                }
                $i++;
            }
            fclose($file);
           // print_r($upload_file_header);exit;
            
            if(($required_header===$upload_file_header) && ($required_header_details === $upload_file_header_detail)){

                $data['noOfRecords']=count($dataExcel);

                while (($getData1 = fgetcsv($filed, 10000, ",")) !== FALSE) {
                    
                    if($x==0){
                        $header=$getData1;
                    }
                    
                    if ($x > 0) {

                        if($x==1){
                            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_billofmaterial', 'bomMasterID', 'companyID');
                            $codes = $this->sequence->sequence_generator('BOM', $serialInfo['serialNo']);
                            $datetime = format_date_mysql_datetime();

                            $productcode = $getData1[1];
                            $this->db->select("mfqItemID,itemSystemCode,itemDescription,defaultUnitOfMeasureID");
                            $this->db->from('srp_erp_mfq_itemmaster');
                            $this->db->where('companyID', current_companyID());
                            $this->db->where('itemSystemCode', $productcode);
                            $output = $this->db->get()->row_array();

                            if($output){
                                $dataExcel2['mfqItemID'] = $output['mfqItemID'];

                                $dataExcel2['uomID'] = $output['defaultUnitOfMeasureID'];
                                //$getData1[2]
                                $dataExcel2['documentDate'] = format_date_mysql_datetime(date('Y-m-d'));
                                $dataExcel2['Qty'] = $getData1[3];
                                $totalQty = $getData1[3];
    
                                $dataExcel2['serialNo'] = $serialInfo['serialNo'];
                                $dataExcel2['documentCode'] = $codes;
                                $dataExcel2['productImage'] = $this->input->post('productImage');
                                $dataExcel2['companyID'] = current_companyID();
                                $dataExcel2['createdPCID'] = current_pc();
                                $dataExcel2['createdUserID'] = current_userID();
                                $dataExcel2['createdDateTime'] = $datetime;
                                $dataExcel2['createdUserName'] = current_user();
                                $dataExcel2['timestamp'] = $datetime;
                                $this->db->insert('srp_erp_mfq_billofmaterial', $dataExcel2);
                                $last_id = $this->db->insert_id();
                            }else{
                                echo json_encode(['e', 'Product Not Found!']);
                            }
                        }else{
                            if($x==2 || $x==3){
                                
                            }else{
                                $dataExcel3['bomID'] = $last_id;
                                $dataExcel3['type'] = $getData1[0];
                                $dataExcel3['code'] =  $getData1[1];
                                $dataExcel3['Name'] =  $getData1[2];
                                $dataExcel3['qty'] =  $getData1[3]/$totalQty;
                                $dataExcel3['unitRate'] =  $getData1[4];
                                $dataExcel3['totalamount'] =  $getData1[5]/$totalQty;

                                $this->db->insert('srp_erp_bom_upload', $dataExcel3);

                                $dataExcel4[]=$dataExcel3;
                            }
                        }
                
                    }
                    $x++;
                }
                fclose($filed);
                

                if (!empty($dataExcel4)) {

                    $this->db->query("INSERT INTO `srp_erp_mfq_bom_labourtask` (
                        `bomMasterID`,
                        `labourTask`,
                        `uomID`,
                        `segmentID`,
                        `hourlyRate`,
                        `totalHours`,
                        `totalValue`,
                        `companyID`
                        )(
                        SELECT
                            srp_erp_mfq_billofmaterial.bomMasterID AS `bomMasterID`,
                            srp_erp_mfq_overhead.overHeadID AS `overheadID`,
                            srp_erp_mfq_overhead.unitofmeasureID,
                            srp_erp_mfq_overhead.mfqsegmentID,
                            srp_erp_bom_upload.unitRate AS `hourlyRate`,
                            srp_erp_bom_upload.qty `totalHours`,
                            (
                                srp_erp_bom_upload.unitRate * srp_erp_bom_upload.qty
                            ) AS totalValue,
                            srp_erp_mfq_billofmaterial.`companyID`
                        FROM
                            srp_erp_bom_upload
                        JOIN srp_erp_mfq_overhead ON srp_erp_bom_upload.code = srp_erp_mfq_overhead.overHeadCode and srp_erp_mfq_overhead.overHeadCategoryID=2
                        JOIN srp_erp_mfq_billofmaterial ON srp_erp_bom_upload.bomID = srp_erp_mfq_billofmaterial.bomMasterID
                        JOIN srp_erp_company ON srp_erp_mfq_billofmaterial.companyID = srp_erp_company.company_id
                        WHERE
                            srp_erp_bom_upload.bomID = {$last_id}
                        AND srp_erp_bom_upload.type = 'Labour'
                    )");

                    $this->db->query("INSERT INTO `srp_erp_mfq_bom_overhead` (
                                    `bomMasterID`,
                                    `overheadID`,
                                    `uomID`,
                                    `segmentID`,
                                    `hourlyRate`,
                                    `totalHours`,
                                    `totalValue`,
                                    `companyID`
                                )(
                                    SELECT
                                        srp_erp_mfq_billofmaterial.bomMasterID AS `bomMasterID`,
                                        srp_erp_mfq_overhead.overHeadID AS `overheadID`,
                                        srp_erp_mfq_overhead.unitofmeasureID,
                                        srp_erp_mfq_overhead.mfqsegmentID,
                                        srp_erp_bom_upload.unitRate AS `hourlyRate`,
                                        srp_erp_bom_upload.qty `totalHours`,
                                        (
                                            srp_erp_bom_upload.unitRate * srp_erp_bom_upload.qty
                                        ) AS totalValue,
                                        srp_erp_mfq_billofmaterial.`companyID`
                                    FROM
                                        srp_erp_bom_upload
                                    JOIN srp_erp_mfq_overhead ON srp_erp_bom_upload.code = srp_erp_mfq_overhead.overHeadCode and srp_erp_mfq_overhead.overHeadCategoryID=1
                                    JOIN srp_erp_mfq_billofmaterial ON srp_erp_bom_upload.bomID = srp_erp_mfq_billofmaterial.bomMasterID
                                    JOIN srp_erp_company ON srp_erp_mfq_billofmaterial.companyID = srp_erp_company.company_id
                                    WHERE
                                        srp_erp_bom_upload.bomID = {$last_id}
                                    AND srp_erp_bom_upload.type = 'Overhead'
                                )");

                    $this->db->query("INSERT INTO `srp_erp_mfq_bom_overhead` (
                                    `bomMasterID`,
                                    `overheadID`,
                                    `uomID`,
                                    `segmentID`,
                                    `hourlyRate`,
                                    `totalHours`,
                                    `totalValue`,
                                    `companyID`
                                )(
                                    SELECT
                                        srp_erp_mfq_billofmaterial.bomMasterID AS `bomMasterID`,
                                        srp_erp_mfq_overhead.overHeadID AS `overheadID`,
                                        srp_erp_mfq_overhead.unitofmeasureID,
                                        srp_erp_mfq_overhead.mfqsegmentID,
                                        srp_erp_bom_upload.unitRate AS `hourlyRate`,
                                        srp_erp_bom_upload.qty `totalHours`,
                                        (
                                            srp_erp_bom_upload.unitRate * srp_erp_bom_upload.qty
                                        ) AS totalValue,
                                        srp_erp_mfq_billofmaterial.`companyID`
                                    FROM
                                        srp_erp_bom_upload
                                    JOIN srp_erp_mfq_overhead ON srp_erp_bom_upload.code = srp_erp_mfq_overhead.overHeadCode and srp_erp_mfq_overhead.overHeadCategoryID=1 and srp_erp_mfq_overhead.typeID = 2
                                    JOIN srp_erp_mfq_billofmaterial ON srp_erp_bom_upload.bomID = srp_erp_mfq_billofmaterial.bomMasterID
                                    JOIN srp_erp_company ON srp_erp_mfq_billofmaterial.companyID = srp_erp_company.company_id
                                    WHERE
                                        srp_erp_bom_upload.bomID = {$last_id}
                                    AND srp_erp_bom_upload.type = 'Thirdparty'
                                )");

                    $this->db->query("INSERT INTO `srp_erp_mfq_bom_machine` (
                        `bomMasterID`,
                        `mfq_faID`,
                        `uomID`,
                        `segmentID`,
                        `hourlyRate`,
                        `totalHours`,
                        `totalValue`,
                        `companyID`
                    )(
                        SELECT
                            srp_erp_mfq_billofmaterial.bomMasterID AS `bomMasterID`,
                            srp_erp_mfq_fa_asset_master.mfq_faID AS `mfq_faID`,
                            srp_erp_mfq_fa_asset_master.unitOfmeasureID,
                            srp_erp_mfq_fa_asset_master.segmentID,
                            srp_erp_bom_upload.unitRate AS `hourlyRate`,
                            srp_erp_bom_upload.qty `totalHours`,
                            (
                                srp_erp_bom_upload.unitRate * srp_erp_bom_upload.qty
                            ) AS totalValue,
                            srp_erp_mfq_billofmaterial.`companyID`
                        FROM
                            srp_erp_bom_upload
                        JOIN srp_erp_mfq_fa_asset_master ON srp_erp_bom_upload.code = srp_erp_mfq_fa_asset_master.faCode
                        JOIN srp_erp_mfq_billofmaterial ON srp_erp_bom_upload.bomID = srp_erp_mfq_billofmaterial.bomMasterID
                        JOIN srp_erp_company ON srp_erp_mfq_billofmaterial.companyID = srp_erp_company.company_id
                        WHERE
                            srp_erp_bom_upload.bomID = {$last_id}
                        AND srp_erp_bom_upload.type = 'Machine'
                    )");

                    $this->db->query("INSERT INTO `srp_erp_mfq_bom_materialconsumption` (
                        `bomMasterID`,
                        `mfqItemID`,
                        `qtyUsed`,
                        `costingType`,
                        `unitCost`,
                        `materialCost`,
                        `markUp`,
                        `materialCharge`,
                        `companyID`
                    )
                    
                        (
                    select 
                    srp_erp_mfq_billofmaterial.bomMasterID as `bomMasterID`,
                        srp_erp_mfq_itemmaster.mfqItemID as `mfqItemID`,
                        srp_erp_bom_upload.qty as `qtyUsed`,
                        3 as `costingType`,
                        srp_erp_bom_upload.unitRate as `unitCost`,
                        (srp_erp_bom_upload.unitRate*srp_erp_bom_upload.qty) as `materialCost`,
                        0 as `markUp`,
                        (srp_erp_bom_upload.unitRate*srp_erp_bom_upload.qty) as `materialCharge`,
                        srp_erp_mfq_billofmaterial.`companyID`

                    
                    from 
                    srp_erp_bom_upload 
                    join srp_erp_mfq_itemmaster on srp_erp_bom_upload.code=srp_erp_mfq_itemmaster.itemSystemCode
                    join srp_erp_mfq_billofmaterial on srp_erp_bom_upload.bomID=srp_erp_mfq_billofmaterial.bomMasterID
                    join srp_erp_company on srp_erp_mfq_billofmaterial.companyID=srp_erp_company.company_id
                    
                    where 
                            srp_erp_bom_upload.bomID={$last_id} and srp_erp_bom_upload.type='Material'
                        );");
                    echo json_encode(['s', 'Successfully Updated']);
                } else {
                    echo json_encode(['e', 'No records in the uploaded Details']);
                }
            }else{
                echo json_encode(['e', 'Required file format incorrect']);
            }
        } else {
            echo json_encode(['e', 'No Files Attached']);
        }
    }

    function downloadExcel(){


        $csv_data[0] = 
            [
                0 => 'Product Type',
                1 => 'Product Code',
                2 => 'Date',
                3 => 'Qty',
                4 => '',
                5 => '',
            ]
        ;

        $csv_data[1] = 
            [
                0 => '',
                1 => '',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
            ];

        $csv_data[2] = 
        [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
        ];

        $csv_data[3] = 
            [
                0 => 'Type',
                1 => 'Code',
                2 => 'Name',
                3 => 'Qty',
                4 => 'Unit',
                5 => 'Total',
            ];


        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=bill of material.csv");


        $output = fopen("php://output", "w");
        foreach ($csv_data as $row){
            fputcsv($output, $row);
        }
        fclose($output);
    }
}