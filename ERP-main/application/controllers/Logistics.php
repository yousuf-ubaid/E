<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * Date: 28/2/2020
 * Time: 2:41 PM
 */
class Logistics extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Logistics_model');
        $this->load->helpers('logistics');
    }

    /*Start Job Request*/
    function fetch_job_request()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
//        $datefrom = $this->input->post('datefrom');
        //      $datefromconvert = input_format_date($datefrom, $date_format_policy);
        //    $dateto = $this->input->post('dateto');
        //  $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];

        $where = "srp_erp_logisticjobs.companyID=" . $companyid . " AND deletedYN = 0";
        $this->datatables->select(" jobID,
	mas.CustomerName AS CustomerName,
	BLLogisticRefNo,
	containerNo,
	shippingLine,
	servicetypemas.serviceType  AS serviceType,
	arrivalDate,
	bookingNumber,
	statusmas.statusDescription AS bayanSystemStatus,
	empdet.Ename2 AS encodeBy,
	reminderInDays,
	internalRefNo,
	confirmedYN,
	createdUserID AS createdUser,
	 srp_erp_logisticjobs.companyID,
	 srp_erp_logisticjobs.Documentcode ");
        $this->datatables->join('(SELECT CustomerName,customerAutoID FROM srp_erp_customermaster GROUP BY customerAutoID) mas', '(mas.customerAutoID = srp_erp_logisticjobs.customerID)', 'left');
        $this->datatables->join('(SELECT statusDescription, statusID, type FROM srp_erp_logisticstatus WHERE type = 1 GROUP BY statusID) statusmas', '(`statusmas`.`statusID` = srp_erp_logisticjobs.bayanStatusID)', 'left');
        $this->datatables->join('(SELECT serviceType,serviceID FROM srp_erp_logisticservicetypes GROUP BY serviceID ) servicetypemas', '(`servicetypemas`.`serviceID` = srp_erp_logisticjobs.serviceTypeID)', 'left');
        $this->datatables->join('(SELECT Ename2,EIdNo FROM srp_employeesdetails GROUP BY EIdNo) empdet', '(`empdet`.`EIdNo` = srp_erp_logisticjobs.encodeByEmpID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_logisticjobs');
        $this->datatables->add_column('edit', '$1', 'load_job_request_action(jobID,createdUser,confirmedYN)');
        $this->datatables->edit_column('arrivalDate', '<span >$1 </span>', 'convert_date_format(arrivalDate)');
        //$this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function delete_job_request()
    {
        echo json_encode($this->Logistics_model->delete_job_request());
    }

    function save_job_request()
    {
        //$this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        //$this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('encodeByEmpID', 'Encoded By', 'trim|required');
        //$this->form_validation->set_rules('IdCardNumber', 'ID card number', 'trim|required');
        /*        $this->form_validation->set_rules('customerTelephone', 'customer Telephone', 'trim|required');
                $this->form_validation->set_rules('customerEmail', 'customer Email', 'trim|required');
                $this->form_validation->set_rules('customerAddress1', 'Address 1', 'trim|required');
                $this->form_validation->set_rules('customerAddress2', 'Address 2', 'trim|required');
                $this->form_validation->set_rules('customerCreditLimit', 'Credit Limit', 'trim|required');
                $this->form_validation->set_rules('customerCreditPeriod', 'Credit Period', 'trim|required|max_length[3]');*/
        //$this->form_validation->set_rules('receivableAccount', 'Receivable Account', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Logistics_model->save_job_request());
        }
    }

    function load_job_request_header()
    {
        echo json_encode($this->Logistics_model->load_job_request_header());
    }
    /*End Job Request*/

    /*Start Document Master*/
    public function fetch_documentMaster()
    {
        $this->datatables->select('docID , description')
            ->from('srp_erp_logisticdocumentmaster ')
            ->add_column('edit', '$1', 'action_docSetup(docID, description)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function save_documentDescriptions()
    {
        $this->form_validation->set_rules('description', 'Documents', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->save_documentDescriptions());
        }
    }

    public function edit_documentDescription()
    {
        $this->form_validation->set_rules('edit_description', 'Documents Description', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->edit_documentDescription());
        }
    }

    public function delete_documentDescription()
    {
        $this->form_validation->set_rules('hidden-id', 'Documents ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->delete_documentDescription());
        }
    }


    /*End Document Master*/

    /*Start Service types*/
    public function fetch_servicetype()
    {
        $this->datatables->select('serviceID, serviceType')
            ->from('srp_erp_logisticservicetypes ')
         /*   ->join('srp_erp_itemmaster ', 'srp_erp_logisticservicetypes.itemAutoID  = srp_erp_itemmaster.itemAutoID','LEFT')*/
            //->join('srp_erp_itemmaster AS mas', 'srp_erp_logisticservicetypes.itemAutoID = mas.itemAutoID')
            //->add_column('status', '$1', 'confirm(isActive)')

            //->add_column('edit', '$1', 'action_designation(DesignationID, DesDescription, usageCount)')

            ->add_column('edit', '$1', 'action_servicetype(serviceID, serviceType)')
            ->where('srp_erp_logisticservicetypes.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function save_servicetype()
    {
        $this->form_validation->set_rules('add_servicetype', 'Service Type', 'required');
        //$this->form_validation->set_rules('item_add', 'Item', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->save_servicetype());
        }
    }

    public function editServicetype()
    {
        $this->form_validation->set_rules('serviceType', 'Service Type', 'required');
        $this->form_validation->set_rules('edit_hidden-id', 'Service Type ID', 'required');
       // $this->form_validation->set_rules('item', 'Item', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->editServicetype());
        }
    }

    public function delete_servicetype()
    {
        $this->form_validation->set_rules('hidden-id', 'Service  ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->delete_servicetype());
        }
    }

    /*End Service Type*/


    function fetch_document_detail_table()
    {
        echo json_encode($this->Logistics_model->fetch_document_detail_table());
    }

    function save_mandatorydocument()
    {
        $this->form_validation->set_rules('ds_hidden-id', 'Service ID', 'trim|required');
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        //$this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Logistics_model->save_mandatorydocument());
        }
    }

    public function delete_mandatoryDocument()
    {
        $this->form_validation->set_rules('serviceDocumentID', 'Service Document ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->delete_mandatoryDocument());
        }
    }

    function logistics_excelUpload()
    {
        $companyID = current_companyID();
        $servicetype = $this->input->post('servicetype');
        $this->form_validation->set_rules('servicetype', 'Service Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $file = fopen($_FILES['excelUpload_file']['tmp_name'],"r");
            if(fgetcsv($file)[0]=='')
            {
                echo json_encode(array('e', 'Uploaded file is empty'));
                fclose($file);
                exit();
            }

            if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
                $type = explode(".", $_FILES['excelUpload_file']['name']);
                if (strtolower(end($type)) != 'csv') {
                    die(json_encode(['e', 'File type is not csv - ', $type]));
                }
                $i = 0;
                $x = 1;
                $n = 0;
                $filename = $_FILES["excelUpload_file"]["tmp_name"];
                $file = fopen($filename, "r");
                $filed = fopen($filename, "r");
                $dataExcel = [];
                $customercode = array();
                $transportdocument_arr = array();
                $staus_arr = array();
                $status_msg = '';
                while (($getData = fgetcsv($filed, 10000, ",")) !== FALSE) {
                    $secondaryCode = trim($getData[5] ?? '');
                    $internalRefNo = trim($getData[7] ?? '');
                    $customerID_ID = $this->db->query("SELECT customerAutoID FROM srp_erp_customermaster where companyID = $companyID AND	secondaryCode = '$secondaryCode'")->row('customerAutoID');
                    $customerID = $this->db->query("SELECT secondaryCode FROM srp_erp_customermaster where companyID = $companyID AND	secondaryCode = '$secondaryCode' ")->row('secondaryCode');
                    $transportdocument = $this->db->query("SELECT internalRefNo FROM `srp_erp_logisticjobs`where companyID = $companyID AND customerID = '$customerID_ID' AND serviceTypeID = $servicetype AND internalRefNo = '$internalRefNo' ")->row('internalRefNo');
                    $statusdescription = strtoupper(trim($getData[8] ?? ''));
                    $statusdescription_declaration = strtoupper(trim($getData[10] ?? ''));
                    $statusdescription_processing = strtoupper(trim($getData[11] ?? ''));
                    $statusdescription_payment = strtoupper(trim($getData[12] ?? ''));
                    $statusdescription_revstatus = strtoupper(trim($getData[13] ?? ''));

                    $partialstatus = $this->db->query("SELECT UPPER(statusDescription) statusDescription FROM `srp_erp_logisticstatus` where type= 2 AND statusDescription = '$statusdescription' AND companyID = ".$companyID )->row('statusDescription');
                    $declarationStatus = $this->db->query("SELECT UPPER(statusDescription) statusDescription FROM `srp_erp_logisticstatus` where type= 3 AND statusDescription = '$statusdescription_declaration' AND companyID = ".$companyID)->row('statusDescription');
                    $processing = $this->db->query("SELECT UPPER(statusDescription) statusDescription FROM `srp_erp_logisticstatus` where type= 4 AND statusDescription = '$statusdescription_processing' AND companyID = ".$companyID)->row('statusDescription');
                    $paymentstatus = $this->db->query("SELECT UPPER(statusDescription) statusDescription FROM `srp_erp_logisticstatus` where type= 5 AND statusDescription = '$statusdescription_payment' AND companyID = ".$companyID)->row('statusDescription');
                    $revstatus = $this->db->query("SELECT UPPER(statusDescription) statusDescription FROM `srp_erp_logisticstatus` where type= 6 AND statusDescription = '$statusdescription_revstatus' AND companyID = ".$companyID)->row('statusDescription');

                    if ($customerID != trim($getData[5] ?? '')) {
                        array_push($customercode, 'Line No ' . $x . ' Customer Secondary Code (' . $getData[5] . ') Does Not Exist');
                    }
                    if ($transportdocument != trim($getData[7] ?? '')) {
                        array_push($transportdocument_arr, 'Line No ' . $x . ' Transport Document Code (' . trim($getData[7] ). ') Does Not Exist');
                    }

                    if ($partialstatus != $statusdescription) {
                        array_push($staus_arr, 'Line No ' . $x . ' Partial Released Status  (' . $getData[8] . ') Does Not Exist');
                        $status_msg = 'Partial Released Status';
                    }
                    if ($declarationStatus != $statusdescription_declaration) {
                        array_push($staus_arr, 'Line No ' . $x . ' Declaration Status  (' . $getData[10] . ') Does Not Exist');
                        $status_msg = 'Declaration Status';
                    }
                    if ($processing != $statusdescription_processing) {
                        array_push($staus_arr, 'Line No ' . $x . ' Processing Status  (' . $getData[11] . ') Does Not Exist');
                        $status_msg = 'Processing Status';
                    }
                    if ($paymentstatus != $statusdescription_payment) {
                        array_push($staus_arr, 'Line No ' . $x . ' Payment Status  (' . $getData[12] . ') Does Not Exist');
                        $status_msg = 'Payment Status';
                    }
                    if ($revstatus != $statusdescription_revstatus) {
                        array_push($staus_arr, 'Line No ' . $x . ' Review Status  (' . $getData[13] . ') Does Not Exist');
                        $status_msg = 'Review Status';
                    }


                    $x++;
                }

                if (!empty($customercode)) {
                    die(json_encode(['e', 'Customer Secondary Code Does Not Exist.', 'customercode' => $customercode]));

                }
                if (!empty($transportdocument_arr)) {
                    die(json_encode(['e', 'Transport Document Code Does Not Exist.', 'customercode' => $transportdocument_arr]));

                }
                if (!empty($staus_arr)) {
                    die(json_encode(['e', $status_msg . ' Not Exist.', 'customercode' => $staus_arr]));
                }
                fclose($filed);

                while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $old_date_timestamp = strtotime($getData[9]);
                    $statusdescription = strtoupper(trim($getData[8] ?? ''));
                    $statusdescription_declaration = strtoupper(trim($getData[10] ?? ''));
                    $statusdescription_processing = strtoupper(trim($getData[11] ?? ''));
                    $statusdescription_payment = strtoupper(trim($getData[12] ?? ''));
                    $statusdescription_revstatus = strtoupper(trim($getData[13] ?? ''));
                    $new_date = date('Y-m-d H:i:s', $old_date_timestamp);
                    $customerID = $this->db->query("SELECT customerAutoID FROM srp_erp_customermaster where companyID = $companyID AND	secondaryCode = '$getData[5]'" )->row('customerAutoID');
                    $status_id_partial = $this->db->query("SELECT statusID FROM `srp_erp_logisticstatus` where type= 2 AND statusDescription = '$statusdescription'  AND companyID = ".$companyID )->row('statusID');
                    $declarationStatus = $this->db->query("SELECT statusID FROM `srp_erp_logisticstatus` where type= 3 AND statusDescription = '$statusdescription_declaration'  AND companyID = ".$companyID )->row('statusID');
                    $processing = $this->db->query("SELECT statusID FROM `srp_erp_logisticstatus` where type= 4 AND statusDescription = '$statusdescription_processing' AND companyID = ".$companyID )->row('statusID');
                    $paymentstatus = $this->db->query("SELECT statusID FROM `srp_erp_logisticstatus` where type= 5 AND statusDescription = '$statusdescription_payment' AND companyID = ".$companyID )->row('statusID');
                    $revstatus = $this->db->query("SELECT statusID FROM `srp_erp_logisticstatus` where type= 6 AND statusDescription = '$statusdescription_revstatus' AND companyID = ".$companyID )->row('statusID');
                    $dataExcel[$i]['declarationNumber'] = $getData[1];
                    $dataExcel[$i]['serviceTypeID'] = $servicetype;
                    $dataExcel[$i]['version'] = $getData[2];
                    $dataExcel[$i]['regime'] = $getData[3];
                    $dataExcel[$i]['uploadType'] = $getData[4];
                    $dataExcel[$i]['customerID'] = $customerID;
                    $dataExcel[$i]['exporter'] = $getData[6];
                    $dataExcel[$i]['transportDocument'] = trim($getData[7] ?? '');
                    $dataExcel[$i]['partialReleasedID'] = $status_id_partial;
                    $dataExcel[$i]['submissionDate'] = $new_date;
                    $dataExcel[$i]['declarationStatusID'] = $declarationStatus;
                    $dataExcel[$i]['processingStatusID'] = $processing;
                    $dataExcel[$i]['paymentStatusID'] = $paymentstatus;
                    $dataExcel[$i]['reviewStatusID'] = $revstatus;
                    $dataExcel[$i]['companyID'] = current_companyID();
                    $i++;
                }

                fclose($file);
                if (!empty($dataExcel)) {
                    $result = $this->db->insert_batch('srp_erp_logisticuploads', $dataExcel);
                    if ($result) {
                        echo json_encode(['s', 'Successfully Updated']);
                    } else {
                        echo json_encode(['e', 'Upload Failed']);
                    }
                } else {
                    echo json_encode(['e', 'No records in the uploaded file']);
                }

            } else {
                echo json_encode(['e', 'No Files Attached']);
            }
        }

    }

    function logistics_fetchuploads()
    {
        $companyID = current_companyID();
        $this->datatables->select('uploadID,declarationNumber,version,regime,uploadType,customerSystemCode,exporter,transportDocument,partial.statusDescription as partial,submissionDate,declaration.statusDescription as declaration,
                                   processing.statusDescription as processing,payment.statusDescription as payment,reveiw.statusDescription as reveiw,service.serviceType,IFNULL(releasedDate,0) as releasedDate,srp_erp_logisticuploads.invoiceAutoID as invoiceAutoID,IFNULL( invoicemaster.invoiceCode,\'-\') as invoiceCode');
        $this->datatables->from('srp_erp_logisticuploads');
        $this->datatables->join('(SELECT customerSystemCode,customerAutoID  from srp_erp_customermaster) customer', '(customer.customerAutoID = srp_erp_logisticuploads.customerID)', 'left');
        $this->datatables->join('(SELECT statusID,statusDescription FROM `srp_erp_logisticstatus` where type= 2 AND companyID = '.$companyID.') partial', '(partial.statusID = srp_erp_logisticuploads.partialReleasedID)', 'left');
        $this->datatables->join('(SELECT statusID,statusDescription FROM `srp_erp_logisticstatus` where type= 3 AND companyID = '.$companyID.' ) declaration', '(declaration.statusID = srp_erp_logisticuploads.declarationStatusID)', 'left');
        $this->datatables->join('(SELECT statusID,statusDescription FROM `srp_erp_logisticstatus` where type= 4 AND companyID = '.$companyID.' ) processing', '(processing.statusID = srp_erp_logisticuploads.processingStatusID)', 'left');
        $this->datatables->join('(SELECT statusID,statusDescription FROM `srp_erp_logisticstatus` where type= 5 AND companyID = '.$companyID.' ) payment', '(payment.statusID = srp_erp_logisticuploads.paymentStatusID)', 'left');
        $this->datatables->join('(SELECT statusID,statusDescription FROM `srp_erp_logisticstatus` where type= 6 AND companyID = '.$companyID.' ) reveiw', '(reveiw.statusID = srp_erp_logisticuploads.reviewStatusID)', 'left');
        $this->datatables->join('(SELECT serviceID,serviceType FROM `srp_erp_logisticservicetypes`) service', 'service.serviceID = srp_erp_logisticuploads.serviceTypeID', 'left');
        $this->datatables->join('(SELECT invoiceAutoID,invoiceCode from srp_erp_customerinvoicemaster) invoicemaster', 'invoicemaster.invoiceAutoID = srp_erp_logisticuploads.invoiceAutoID', 'left');
        $this->datatables->where('srp_erp_logisticuploads.companyID',$companyID );
        $this->datatables->add_column('edit', '$1', 'load_logistic_action(uploadID,releasedDate,2,invoiceAutoID)');
        $this->datatables->add_column('uploaddocview', '$1', 'load_upload_actions(invoiceAutoID,invoiceCode)');
        $this->datatables->add_column('declarationno', '$1', 'load_logistic_action(uploadID,declarationNumber,1,invoiceAutoID)');
        $this->datatables->edit_column('submissionDate', '<span >$1 </span>', 'convert_date_format(submissionDate)');
        echo $this->datatables->generate();
    }

    function logisticupdate_reldate()
    {
        $uploadID = $this->input->post('uploadId');
        $releasedate = $this->input->post('releasedate');
        $this->form_validation->set_rules('releasedate', 'Released Date', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $tmpreleasedate = trim(str_replace('/', '-', $releasedate));
            $newformreldate = date('Y-m-d H:i:s', strtotime($tmpreleasedate));

            $this->db->trans_start();
            $data['releasedDate'] = $newformreldate;
            $this->db->where('uploadID', $uploadID);
            $this->db->update('srp_erp_logisticuploads', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Update Failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Released Date Updated Successfully'));
            }

        }
    }

    function create_customerinvoice()
    {
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $currentdate = input_format_date(current_date(), $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $documentDate = input_format_date($currentdate, $date_format_policy);
        $uploadID = $this->input->post('uploaddetailid');

       /* $ActiveFinanceYear = $this->db->query("SELECT * FROM `srp_erp_companyfinanceperiod` WHERE companyID = $companyID AND isActive = 1
                                               AND '{$currentdate}' BETWEEN dateFrom AND dateTo")->row_array();

        if(empty($ActiveFinanceYear))
        {
            echo json_encode(array('e', 'Active Finance Period not found'));
            die();
        }*/
        
        $itemAutoID_arr = $this->db->query("SELECT itemID FROM `srp_erp_logisticservicetypeitems` WHERE serviceID = (select serviceTypeID from srp_erp_logisticuploads WHERE companyID = $companyID AND uploadID = $uploadID ) ")->result_array();
        if(empty($itemAutoID_arr))
        {
            echo json_encode(array('e', 'Items not assigned for this service type'));
            die();
        }
        $logisticuploaddetail = $this->db->query("select releasedDate from srp_erp_logisticuploads WHERE companyID = $companyID AND uploadID = $uploadID  ")->row('releasedDate');
        if(empty($logisticuploaddetail))
        {
            echo json_encode(array('e', 'Released Date Not Updated'));
            die();
        }
        if($financeyearperiodYN =1)
        {
            $financeperiod = $this->db->query("SELECT companyFinancePeriodID,companyFinanceYearID,dateFrom,dateTo FROM `srp_erp_companyfinanceperiod` WHERE companyID = $companyID AND isActive = 1
	                                           AND '{$currentdate}' BETWEEN dateFrom AND dateTo")->row_array();
            if(empty($financeperiod))
            {
                echo json_encode(array('e', 'Active Finance Period not found'));
                die();
            }else{
                echo json_encode($this->Logistics_model->save_invoice_header());
            }
            /*$financearray = $financeperiod['companyFinancePeriodID'];
            $financePeriod = fetchFinancePeriod($financearray);
            if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
               echo json_encode($this->Logistics_model->save_invoice_header());
            } else {
                echo json_encode(array('e', 'Document Date not between Financial period'));
                die();

            }*/
        }else
        {
            echo json_encode($this->Logistics_model->save_invoice_header());
        }

    }

    function job_request_attachment_view()
    {
        $data['jobID'] = $this->input->post('jobID');
      //  var_dump($jobID);
       // $data['jobID'] = $this->input->post('jobID');
        $jobdetail = $this->db->query("select serviceTypeID,jobID from srp_erp_logisticjobs where jobID =  {$data['jobID']}")->row_array();
        $data['documentdrop'] =  $this->db->query("SELECT logisticdocmaster.docID,logisticdocmaster.description FROM srp_erp_logisticservicetypedocuments
 	LEFT JOIN (SELECT srp_erp_logisticdocumentmaster.docID,description FROM srp_erp_logisticdocumentmaster) logisticdocmaster on  logisticdocmaster.docID = srp_erp_logisticservicetypedocuments.docID
                                                        WHERE serviceID = {$jobdetail['serviceTypeID']}
                                                       ")->result_array();
       // var_dump($data['documentdrop']);
        $this->load->view('system/logistics/document-description-detail',$data);
    }

    function fetch_logistic_attachments()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $jobID = $this->input->post('jobID');
        $data = $this->db->query("SELECT srp_erp_logisticjobattachments.* ,documentmaster.description FROM `srp_erp_logisticjobattachments` 
                                       LEFT JOIN srp_erp_logisticdocumentmaster documentmaster on documentmaster.docID = srp_erp_logisticjobattachments.docID WHERE
	                                    `jobID` = '$jobID' AND srp_erp_logisticjobattachments.companyID = '$companyID'")->result_array();
        $result = '';
        $x = 1;
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');

                $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['attachmentDescription'] . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['description'] . '</td>
                <td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_job_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td>
                </tr>';
                $x++;

            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        echo json_encode($result);

    }

    function fetch_logistic_attachments_2()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $jobID = $this->input->post('jobID');
        $data = $this->db->query("SELECT srp_erp_logisticjobattachments.* ,documentmaster.description, job.confirmedYN  AS confirmedYN FROM `srp_erp_logisticjobattachments` 
                                       LEFT JOIN srp_erp_logisticdocumentmaster documentmaster on documentmaster.docID = srp_erp_logisticjobattachments.docID 
                                       LEFT JOIN (SELECT confirmedYN,jobID FROM srp_erp_logisticjobs) job  ON job.jobID = srp_erp_logisticjobattachments.jobID 
                                       WHERE
	                                    srp_erp_logisticjobattachments.`jobID` = '$jobID' AND srp_erp_logisticjobattachments.companyID = '$companyID'")->result_array();
        $result = '';
        $x = 1;

        if (!empty($data)) {
            foreach ($data as $val) {

                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');

                $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['attachmentDescription'] . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['description'] . '</td>
                <td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> ';
                if($val['confirmedYN'] != 1){
                    $result .= '&nbsp; | &nbsp;<a onclick="delete_job_attachments_2(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td>';

                }
                $result .= '</td></tr>';
                $x++;

            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        echo json_encode($result);

    }
    function attachement_upload()
    {
        //$this->load->model('upload_modal');

            $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('document_ID', 'Document', 'trim|required');
            $this->form_validation->set_rules('jobID', 'Job Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
             } else {

            $this->db->trans_start();

            $file_name = 'JOBREQ'.'_'.$this->input->post('attachmentDescription');
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if(empty($ext)) {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                exit();
            }

            $cc = current_companyCode();
            $folderPath = !empty($cc) ? $cc . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }

            /** end of s3 integration */

            $data['docID'] = $this->input->post('document_ID');
            $data['jobID'] = $this->input->post('jobID');
           // $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_logisticjobattachments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.', 's3Upload' => $s3Upload));
            }
        }
    }
    function delete_attachments_AWS_s3_logistics()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');


        $result = $this->s3->delete($myFileName);

        if ($result) {
            $this->db->delete('srp_erp_logisticjobattachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }
    function logistics_confirmation()
    {
        $jobID = $this->input->post('jobID');
        $jobdetail = $this->db->query("select serviceTypeID,jobID from srp_erp_logisticjobs where jobID =  {$jobID}")->row_array();
        $isRecExist = $this->db->query("SELECT
	GROUP_CONCAT( logisticdocmaster.description)  as description
FROM
	srp_erp_logisticservicetypedocuments
	LEFT JOIN ( SELECT srp_erp_logisticdocumentmaster.docID, description FROM srp_erp_logisticdocumentmaster ) logisticdocmaster ON logisticdocmaster.docID = srp_erp_logisticservicetypedocuments.docID 
WHERE
	serviceID = {$jobdetail['serviceTypeID'] }
	AND isMandatory = 1 
	AND srp_erp_logisticservicetypedocuments.docID NOT IN (
	SELECT
		docID 
	FROM
		srp_erp_logisticjobattachments 
	WHERE
	jobID = $jobID 
	)")->row_array();
        if(!empty($isRecExist['description'])||($isRecExist['description']!=''))
        {
            echo json_encode(array('e', 'Please Add Atttachment to following documents ('.$isRecExist['description'].')'));
            die();
        } else {
            $this->db->select('Documentcode');
            $this->db->where('jobID', $jobID);
            $this->db->where('companyID', current_companyID());
            $this->db->from('srp_erp_logisticjobs');
            $mas_dt = $this->db->get()->row_array();
            $validate_code = validate_code_duplication($mas_dt['Documentcode'], 'Documentcode', $jobID,'jobID', 'srp_erp_logisticjobs');
            if(!empty($validate_code)) {
                echo json_encode(array('e', 'The document Code Already Exist.(' . $validate_code . ')'));
            }

            $this->db->trans_start();
            $data['confirmedYN'] = 1 ;
            $data['confirmedByEmpID'] = current_userID();
            $data['confirmedDate'] = current_date();
            $this->db->where('jobID', $jobID);
            $this->db->update('srp_erp_logisticjobs', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Document Confirmed Failed' . $this->db->_error_message()));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Document Confirmed successfully'));
            }
        }
    }


    function load_job_request_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];


        $data['jobID'] = $this->input->post('jobID');
        //  var_dump($jobID);
        // $data['jobID'] = $this->input->post('jobID');
        $data['attachmentDetails'] = $this->db->query("SELECT srp_erp_logisticjobattachments.* ,documentmaster.description FROM `srp_erp_logisticjobattachments` 
                                       LEFT JOIN srp_erp_logisticdocumentmaster documentmaster on documentmaster.docID = srp_erp_logisticjobattachments.docID WHERE jobID =  {$data['jobID']}  AND srp_erp_logisticjobattachments.companyID = {$companyid}")->result_array();
        $jobdetail = $this->db->query("select serviceTypeID,jobID from srp_erp_logisticjobs where jobID =  {$data['jobID']}")->row_array();
        $data['documentdrop'] =  $this->db->query("SELECT logisticdocmaster.docID,logisticdocmaster.description FROM srp_erp_logisticservicetypedocuments
 	        LEFT JOIN (SELECT srp_erp_logisticdocumentmaster.docID,description FROM srp_erp_logisticdocumentmaster) logisticdocmaster on  logisticdocmaster.docID = srp_erp_logisticservicetypedocuments.docID
                                                        WHERE serviceID = {$jobdetail['serviceTypeID']}
                                                       AND isMandatory = 1")->result_array();
                $data['jobrequestDetail'] =  $this->db->query("SELECT * FROM 	`srp_erp_logisticjobs`
            LEFT JOIN ( SELECT CustomerName, customerAutoID FROM srp_erp_customermaster GROUP BY customerAutoID ) mas ON ( `mas`.`customerAutoID` = srp_erp_logisticjobs.customerID )
	        LEFT JOIN ( SELECT statusDescription, statusID, type FROM srp_erp_logisticstatus WHERE type = 1 GROUP BY statusID ) statusmas ON ( `statusmas`.`statusID` = srp_erp_logisticjobs.bayanStatusID )
	        LEFT JOIN ( SELECT serviceType, serviceID FROM srp_erp_logisticservicetypes GROUP BY serviceID ) servicetypemas ON ( `servicetypemas`.`serviceID` = srp_erp_logisticjobs.serviceTypeID )
	        LEFT JOIN ( SELECT Ename2, EIdNo FROM srp_employeesdetails GROUP BY EIdNo ) empdet ON ( `empdet`.`EIdNo` = srp_erp_logisticjobs.encodeByEmpID ) 
            WHERE `jobID` =   {$data['jobID']} AND 	`srp_erp_logisticjobs`.`companyID` = {$companyid} 	AND `deletedYN` = 0  ")->row_array();
//var_dump( $data['jobrequestDetails'] );

        $this->load->view('system/logistics/job-request-view',$data);

    }

    function load_servicetype_items_details()
    {
        echo json_encode($this->Logistics_model->load_servicetype_items_details());
    }

    function delete_servicetype_item()
    {
        echo json_encode($this->Logistics_model->delete_servicetype_item());
    }

    function load_servicetype_items()
    {
        echo json_encode($this->Logistics_model->load_servicetype_items());
    }

    function save_servicetypeItem()
    {
        echo json_encode($this->Logistics_model->save_servicetypeItem());
    }

    function fetch_status(){
        $this->datatables->select("statusID , statusDescription,type,CASE
                    WHEN type = 2 THEN 'Partial Released' 
                    WHEN type = 3 THEN 'Declaration Status' 
                    WHEN type = 4 THEN 'Processing Status' 
                    WHEN type = 5 THEN 'Payment Status' 
                    WHEN type = 6 THEN 'Review Status' 
                    ELSE ' ' 
                END AS typeText ")
            ->from('srp_erp_logisticstatus ')
            ->where('type !=', 1)
            ->where('companyID', current_companyID())
            ->add_column('edit', '$1', 'action_status(statusID, statusDescription,type)');

        echo $this->datatables->generate();
    }

    public function save_status()
    {
        $this->form_validation->set_rules('statusDescription', 'Status', 'trim|required');
        $this->form_validation->set_rules('statusType', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->save_status());
        }
    }

    public function delete_status()
    {
        $this->form_validation->set_rules('hidden-id', 'Status ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Logistics_model->delete_status());
        }
    }
}