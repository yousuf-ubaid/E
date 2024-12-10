<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tracing_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function trace_po_document(){
        $poid = trim($this->input->post('purchaseOrderID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'PO');
        $this->db->where('startDocumentAutoID', $poid);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('purchaseOrderID,purchaseOrderCode,transactionAmount,documentDate,narration');
        $this->db->where('purchaseOrderID', $poid);
        $this->db->from('srp_erp_purchaseordermaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'PO';
        $data['documentName'] = 'Purchase Order';
        $data['documentAutoID'] = $starting['purchaseOrderID'];
        $data['documentNarration'] = $starting['narration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'PO';
        $data['startDocumentAutoID'] = $starting['purchaseOrderID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['purchaseOrderCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDpo=$this->db->insert_id();
        if($insertStarting){
            $prDerails=$this->getPRbyPO($starting['purchaseOrderID'],-1,'PO',$starting['purchaseOrderID'],$masterIDpo);
            $grvDerails=$this->getGRVbyPO($starting['purchaseOrderID'],1,'PO',$starting['purchaseOrderID'],$masterIDpo);
            $grvDerails=$this->getBSIbyPO($starting['purchaseOrderID'],1,'PO',$starting['purchaseOrderID'],$masterIDpo);
            $pvDerails=$this->getPVbyPO($starting['purchaseOrderID'],1,'PO',$starting['purchaseOrderID'],$masterIDpo);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'PO');
            $this->db->where('startDocumentAutoID', $starting['purchaseOrderID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function trace_customer_order_document(){
        $customer_order_id = trim($this->input->post('invoiceAutoID') ?? ''); //customer order id
        $DocumentID = trim($this->input->post('DocumentID') ?? ''); //SRM-ORD

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'SRM-ORD');
        $this->db->where('startDocumentAutoID', $customer_order_id);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('customerOrderID,customerOrderCode,transactionAmount,documentDate,narration');
        $this->db->where('customerOrderID', $customer_order_id);
        $this->db->from('srp_erp_srm_customerordermaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'SRM-ORD';
        $data['documentName'] = 'Customer Order';
        $data['documentAutoID'] = $starting['customerOrderID'];
        $data['documentNarration'] = $starting['narration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'SRM-ORD';
        $data['startDocumentAutoID'] = $starting['customerOrderID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['customerOrderCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDpo=$this->db->insert_id();
        if($insertStarting){
            $prDerails=$this->getPRbyPO($starting['customerOrderID'],-1,'PO',$starting['customerOrderID'],$masterIDpo);
            $grvDerails=$this->getGRVbyPO($starting['customerOrderID'],1,'PO',$starting['customerOrderID'],$masterIDpo);
            $grvDerails=$this->getBSIbyPO($starting['customerOrderID'],1,'PO',$starting['customerOrderID'],$masterIDpo);
            $pvDerails=$this->getPVbyPO($starting['customerOrderID'],1,'PO',$starting['customerOrderID'],$masterIDpo);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'SRM-ORD');
            $this->db->where('startDocumentAutoID', $starting['customerOrderID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getPRbyPO($purchaseOrderID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $prDetails = $this->db->query('SELECT
                prMasterID,
                po.purchaseOrderID,
            pr.purchaseRequestCode,
            pr.transactionAmount,
            pr.documentDate,
            pr.narration,
            SUM(po.totalAmount) as matchedAmnt,
            CASE
                WHEN pr.confirmedYN=0 THEN
                0
                WHEN pr.approvedYN = 1 THEN
                2
            WHEN pr.confirmedYN=2 AND pr.approvedYN=0 THEN
                0
            Else
                0
                END documentStatus
            FROM
                srp_erp_purchaseorderdetails as po
            LEFT JOIN srp_erp_purchaserequestmaster as pr ON po.prMasterID = pr.purchaseRequestID
            WHERE
                purchaseOrderID IN ("'.$purchaseOrderID .'")
            AND po.companyID = "'.$compID.'"
            AND prMasterID is NOT NULL GROUP BY prMasterID')->result_array();

        if(!empty($prDetails)){
            foreach($prDetails as $val){
                $dataPR['empID'] = current_userID();
                $dataPR['documentID'] = 'PRQ';
                $dataPR['documentName'] = 'Purchase Request';
                $dataPR['documentAutoID'] = $val['prMasterID'];
                $dataPR['documentNarration'] = $val['narration'];
                $dataPR['relatedType'] = 1;
                $dataPR['relatedDocumentType'] = 'PO';
                $dataPR['relatedDocumentAutoID'] = $val['purchaseOrderID'];
                $dataPR['levelNo'] = $levelNo;
                $dataPR['masterID'] = $masterID;
                $dataPR['documentStatus'] = $val['documentStatus'];
                $dataPR['startDocumentID'] = $startDocumentID;
                $dataPR['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPR['documentAmount'] = $val['transactionAmount'];
                $dataPR['matchedAmount'] = $val['matchedAmnt'];
                $dataPR['documentDate'] = $val['documentDate'];
                $dataPR['documentSystemCode'] = $val['purchaseRequestCode'];
                $dataPR['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPR['createdUserGroup'] = $this->common_data['user_group'];
                $dataPR['createdPCID'] = $this->common_data['current_pc'];
                $dataPR['createdUserID'] = $this->common_data['current_userID'];
                $dataPR['createdUserName'] = $this->common_data['current_user'];
                $dataPR['createdDateTime'] = $this->common_data['current_date'];

                $insertStarting=$this->db->insert('srp_erp_documenttracing', $dataPR);
            }
        }
    }

    function getGRVbyPO($purchaseOrderID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $grvDetails = $this->db->query('SELECT
	grv.grvAutoID,
	grv.purchaseOrderMastertID,
grvM.grvPrimaryCode,
(
		IFNULL(det.receivedTotalAmount, 0) + IFNULL(addondet.total_amount, 0)
	) AS total_value,
grvM.grvDate,
grvM.grvNarration,
SUM(grv.receivedTotalAmount) as matchedAmnt,
CASE
    WHEN grvM.confirmedYN=0 THEN
    0
    WHEN grvM.approvedYN = 1 THEN
    2
WHEN grvM.confirmedYN=2 AND grvM.approvedYN=0 THEN
    0
Else
    0
    END documentStatus
FROM
	srp_erp_grvdetails as grv
LEFT JOIN srp_erp_grvmaster as grvM ON grvM.grvAutoID = grv.grvAutoID
LEFT JOIN (
	SELECT
		SUM(receivedTotalAmount) AS receivedTotalAmount,
		grvAutoID
	FROM
		srp_erp_grvdetails
	GROUP BY
		grvAutoID
) det ON (
	`det`.`grvAutoID` = grvM.grvAutoID
)
LEFT JOIN (
	SELECT
		SUM(total_amount) AS total_amount,
		grvAutoID
	FROM
		srp_erp_grv_addon
	GROUP BY
		grvAutoID
) addondet ON (
	`addondet`.`grvAutoID` = grvM.grvAutoID
)
WHERE
	grv.purchaseOrderMastertID IN ("'.$purchaseOrderID .'")
AND grv.companyID = "'.$compID.'"
AND grv.purchaseOrderMastertID is NOT NULL
GROUP BY grv.grvAutoID')->result_array();

        if(!empty($grvDetails)){
            foreach($grvDetails as $val){
                $levelNog=0;
                $dataGRV['empID'] = current_userID();
                $dataGRV['documentID'] = 'GRV';
                $dataGRV['documentName'] = 'Goods Received Voucher';
                $dataGRV['documentAutoID'] = $val['grvAutoID'];
                $dataGRV['documentNarration'] = $val['grvNarration'];
                $dataGRV['relatedType'] = 2;
                $dataGRV['relatedDocumentType'] = 'PO';
                $dataGRV['relatedDocumentAutoID'] = $val['purchaseOrderMastertID'];
                $dataGRV['levelNo'] = $levelNo;
                $dataGRV['masterID'] = $masterID;
                $dataGRV['documentStatus'] = $val['documentStatus'];
                $dataGRV['startDocumentID'] = $startDocumentID;
                $dataGRV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataGRV['documentAmount'] = $val['total_value'];
                $dataGRV['matchedAmount'] = $val['matchedAmnt'];
                $dataGRV['documentDate'] = $val['grvDate'];
                $dataGRV['documentSystemCode'] = $val['grvPrimaryCode'];
                $dataGRV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataGRV['createdUserGroup'] = $this->common_data['user_group'];
                $dataGRV['createdPCID'] = $this->common_data['current_pc'];
                $dataGRV['createdUserID'] = $this->common_data['current_userID'];
                $dataGRV['createdUserName'] = $this->common_data['current_user'];
                $dataGRV['createdDateTime'] = $this->common_data['current_date'];
                $insertGrv=$this->db->insert('srp_erp_documenttracing', $dataGRV);
                $masterIDGrv=$this->db->insert_id();
                if($insertGrv){
                    $levelNog=$levelNo+1;
                    $grvDerails=$this->getBSIbyGRV($val['grvAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDGrv);
                    $grvDerails=$this->getSRbyGRV($val['grvAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDGrv);
                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }


    function getBSIbyGRV($grvAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID, $currenyID=''){
        $compID=$this->common_data['company_data']['company_id'];
        $bsiDetails = $this->db->query('SELECT
                                            sinvm.InvoiceAutoID,
                                            sinv.grvAutoID,
                                            sinvm.bookingInvCode,
                                            sinvm.transactionCurrencyID,
                                            (((IFNULL(addondet.taxPercentage, 0) / 100) * IFNULL(det.transactionAmount, 0)) + IFNULL(det.transactionAmount, 0)) AS total_value,
                                            sinvm.bookingDate,
                                            sinvm.comments,
                                            SUM(sinv.transactionAmount) as matchedAmnt,
                                            CASE
                                                WHEN sinvm.confirmedYN=0 THEN
                                                0
                                                WHEN sinvm.approvedYN = 1 THEN
                                                2
                                            WHEN sinvm.confirmedYN=2 AND sinvm.approvedYN=0 THEN
                                                0
                                            Else
                                                0
                                                END documentStatus
                                        FROM
                                            srp_erp_paysupplierinvoicedetail as sinv
                                        LEFT JOIN srp_erp_paysupplierinvoicemaster as sinvm ON sinv.InvoiceAutoID = sinvm.InvoiceAutoID
                                        LEFT JOIN (SELECT SUM(transactionAmount) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det ON ( `det`.`InvoiceAutoID` = sinvm.InvoiceAutoID)
                                        LEFT JOIN (SELECT SUM(taxPercentage) AS taxPercentage, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID) addondet ON (`addondet`.`InvoiceAutoID` = sinvm.InvoiceAutoID)
                                        WHERE
                                            sinv.grvAutoID IN ("'.$grvAutoID .'")
                                            AND sinv.companyID = "'.$compID.'"
                                            AND sinv.grvAutoID is NOT NULL
                                        GROUP BY sinvm.InvoiceAutoID')->result_array();

        if(!empty($bsiDetails)){
            foreach($bsiDetails as $val){
                $exchangeRate['conversion'] = 1;
                if($currenyID != '' && $currenyID != $val['transactionCurrencyID']) {
                    $exchangeRate = currency_conversionID($val['transactionCurrencyID'], $currenyID);
                }

                $levelNog=0;
                $dataBSI['empID'] = current_userID();
                $dataBSI['documentID'] = 'BSI';
                $dataBSI['documentName'] = 'Supplier Invoice';
                $dataBSI['documentAutoID'] = $val['InvoiceAutoID'];
                $dataBSI['documentNarration'] = $val['comments'];
                $dataBSI['relatedType'] = 2;
                $dataBSI['relatedDocumentType'] = 'GRV';
                $dataBSI['relatedDocumentAutoID'] = $val['grvAutoID'];
                $dataBSI['levelNo'] = $levelNo;
                $dataBSI['masterID'] = $masterID;
                $dataBSI['documentStatus'] = $val['documentStatus'];
                $dataBSI['startDocumentID'] = $startDocumentID;
                $dataBSI['startDocumentAutoID'] = $startDocumentAutoID;
                $dataBSI['documentAmount'] = $val['total_value'] / $exchangeRate['conversion'];
                $dataBSI['matchedAmount'] = $val['matchedAmnt'] / $exchangeRate['conversion'];
                $dataBSI['documentDate'] = $val['bookingDate'];
                $dataBSI['documentSystemCode'] = $val['bookingInvCode'];
                $dataBSI['companyID'] = $this->common_data['company_data']['company_id'];
                $dataBSI['createdUserGroup'] = $this->common_data['user_group'];
                $dataBSI['createdPCID'] = $this->common_data['current_pc'];
                $dataBSI['createdUserID'] = $this->common_data['current_userID'];
                $dataBSI['createdUserName'] = $this->common_data['current_user'];
                $dataBSI['createdDateTime'] = $this->common_data['current_date'];
                $insertBSI=$this->db->insert('srp_erp_documenttracing', $dataBSI);
                $masterIDbsi=$this->db->insert_id();
                if($insertBSI){
                    $levelNog=$levelNo+1;
                    $bsiDerails=$this->getPVbyBSI($val['InvoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi, $exchangeRate['conversion']);
                    $dnDerails=$this->getDNbyBSI($val['InvoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi, $exchangeRate['conversion']);
                    $pvmDerails=$this->getPVMbyBSI($val['InvoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi, $exchangeRate['conversion']);
                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getPVbyBSI($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID, $exchangeRate = 1){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
                                            pvm.payVoucherAutoId,
                                            pvd.InvoiceAutoID,
                                            pvm.PVcode,
                                            (((IFNULL(addondet.taxPercentage, 0) / 100) * IFNULL(tyepdet.transactionAmount,0)) + IFNULL(det.transactionAmount, 0) - IFNULL(debitnote.transactionAmount, 0)) AS total_value,
                                            pvm.PVdate,
                                            pvm.PVNarration,
                                            SUM(pvd.transactionAmount) as matchedAmnt,
                                            CASE
                                                WHEN pvm.confirmedYN=0 THEN 0
                                                WHEN pvm.approvedYN = 1 THEN 2
                                                WHEN pvm.confirmedYN=2 AND pvm.approvedYN=0 THEN 0
                                                Else 0
                                            END documentStatus
                                        FROM
                                            srp_erp_paymentvoucherdetail as pvd
                                        LEFT JOIN srp_erp_paymentvouchermaster as pvm ON pvd.payVoucherAutoId = pvm.payVoucherAutoId
                                        LEFT JOIN (SELECT SUM(transactionAmount) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type != "debitnote" GROUP BY payVoucherAutoId) det ON (`det`.`payVoucherAutoId` = pvm.payVoucherAutoId)
                                        LEFT JOIN (SELECT SUM(transactionAmount) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = "GL" OR srp_erp_paymentvoucherdetail.type = "Item" GROUP BY payVoucherAutoId) tyepdet ON (`tyepdet`.`payVoucherAutoId` = pvm.payVoucherAutoId)
                                        LEFT JOIN (SELECT SUM(transactionAmount) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = "debitnote" GROUP BY payVoucherAutoId) debitnote ON (`debitnote`.`payVoucherAutoId` = pvm.payVoucherAutoId)
                                        LEFT JOIN (SELECT SUM(taxPercentage) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId) addondet ON (`addondet`.`payVoucherAutoId` = pvm.payVoucherAutoId)
                                        WHERE
                                            pvd.InvoiceAutoID IN ("'.$InvoiceAutoID .'")
                                            AND pvd.companyID = "'.$compID.'"
                                            AND pvd.InvoiceAutoID is NOT NULL
                                            GROUP BY pvm.payVoucherAutoId')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataPV['empID'] = current_userID();
                $dataPV['documentID'] = 'PV';
                $dataPV['documentName'] = 'Payment Voucher';
                $dataPV['documentAutoID'] = $val['payVoucherAutoId'];
                $dataPV['documentNarration'] = $val['PVNarration'];
                $dataPV['relatedType'] = 2;
                $dataPV['relatedDocumentType'] = 'BSI';
                $dataPV['relatedDocumentAutoID'] = $val['InvoiceAutoID'];
                $dataPV['levelNo'] = $levelNo;
                $dataPV['masterID'] = $masterID;
                $dataPV['documentStatus'] = $val['documentStatus'];
                $dataPV['startDocumentID'] = $startDocumentID;
                $dataPV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPV['documentAmount'] = $val['total_value'] / $exchangeRate;
                $dataPV['matchedAmount'] = $val['matchedAmnt'] / $exchangeRate;
                $dataPV['documentDate'] = $val['PVdate'];
                $dataPV['documentSystemCode'] = $val['PVcode'];
                $dataPV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPV['createdUserGroup'] = $this->common_data['user_group'];
                $dataPV['createdPCID'] = $this->common_data['current_pc'];
                $dataPV['createdUserID'] = $this->common_data['current_userID'];
                $dataPV['createdUserName'] = $this->common_data['current_user'];
                $dataPV['createdDateTime'] = $this->common_data['current_date'];
                $insertPV=$this->db->insert('srp_erp_documenttracing', $dataPV);
                if($insertPV){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function get_tracing_data(){
        $autoID=$this->input->post('autoID');
        $DocumentID=$this->input->post('DocumentID');
        $current_userID=$this->common_data['current_userID'];
        
        $tracingDetails = $this->db->query('SELECT
                    *
                FROM
                    `srp_erp_documenttracing`
                WHERE
                    empID = '.$current_userID.'
                AND startDocumentID = "'.$DocumentID.'"
                AND  startDocumentAutoID= '.$autoID.'
                AND levelNo >= 0

                ORDER BY levelNo,relatedDocumentType,relatedDocumentAutoID DESC')->result_array();

        $tracingMaster = $this->db->query('SELECT
                    *
                FROM
                    `srp_erp_documenttracing`
                WHERE
                    empID = '.$current_userID.'
                AND startDocumentID = "'.$DocumentID.'"
                AND  startDocumentAutoID= '.$autoID.'
                AND levelNo = 0

                ORDER BY levelNo,relatedDocumentType,relatedDocumentAutoID DESC')->row_array();

        $datasd=$this->buildTrees($tracingDetails,$tracingMaster['tracingAutoID']);
        $datasm=$tracingMaster;

        if($DocumentID=="PO"){

            $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_purchaseordermaster`
            WHERE
                purchaseOrderID = '.$autoID.'
            ')->row_array();

            }elseif($DocumentID=="PRQ"){
            
            $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_purchaserequestmaster`
            WHERE
                purchaseRequestID = '.$autoID.'
            ')->row_array();
                    }elseif($DocumentID=="GRV"){
                        $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_grvmaster`
            WHERE
                grvAutoID = '.$autoID.'
                ')->row_array();
                    }elseif($DocumentID=="BSI"){
                        $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_paysupplierinvoicemaster`
            WHERE
                InvoiceAutoID = '.$autoID.'
                ')->row_array();
                    }elseif($DocumentID=="CNT" || $DocumentID=="SO" || $DocumentID=="QUT"){
                        $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_contractmaster`
            WHERE
                contractAutoID = '.$autoID.'
                ')->row_array();
                    }elseif($DocumentID=="CINV"){
                        $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_customerinvoicemaster`
            WHERE
                invoiceAutoID = '.$autoID.'
                ')->row_array();
                    }elseif($DocumentID=="HCINV"){
                        $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_customerinvoicemaster_temp`
            WHERE
                invoiceAutoID = '.$autoID.'
                ')->row_array();
                    }elseif($DocumentID=="DO"){
                        $currency = $this->db->query('SELECT
                transactionCurrency
            FROM
                `srp_erp_deliveryorder`
            WHERE
                DOAutoID = '.$autoID.'
                ')->row_array();
        }elseif($DocumentID=="SLR"){
            $currency = $this->db->query('SELECT
                    transactionCurrency AS transactionCurrency
                FROM
                    `srp_erp_salesreturnmaster`
                WHERE
                    salesReturnAutoID = '.$autoID.'
                    ')->row_array();
                        }elseif($DocumentID=="CN"){
                            $currency = $this->db->query('SELECT
                    transactionCurrency AS transactionCurrency
                FROM
                    `srp_erp_creditnotemaster`
                WHERE
                    creditNoteMasterAutoID = '.$autoID.'
                    ')->row_array();
                        } elseif($DocumentID=="DN"){
                            $currency = $this->db->query('SELECT
                    transactionCurrency AS transactionCurrency
                FROM
                    `srp_erp_debitnotemaster`
                WHERE
                    debitNoteMasterAutoID = '.$autoID.'
                    ')->row_array();
        } elseif($DocumentID=="MR"){
            $currency = $this->db->query('SELECT
                companyLocalCurrency AS transactionCurrency
            FROM
                `srp_erp_materialrequest`
            WHERE
                mrAutoID = '.$autoID.'
                ')->row_array();
                    } elseif($DocumentID=="MI"){
                        $currency = $this->db->query('SELECT
                companyLocalCurrency AS transactionCurrency
            FROM
                `srp_erp_itemissuemaster`
            WHERE
                itemIssueAutoID = '.$autoID.'
                ')->row_array();
        } elseif($DocumentID=="MRN"){
            $currency = $this->db->query('SELECT
                    companyLocalCurrency AS transactionCurrency
                FROM
                    `srp_erp_materialreceiptmaster`
                WHERE
                    mrnAutoID = '.$autoID.'
	        ')->row_array();
        }else if($DocumentID=="RV")
        {
            $currency = $this->db->query("SELECT
                    transactionCurrency AS transactionCurrency
                FROM
                    `srp_erp_customerreceiptmaster`
                WHERE
                    receiptVoucherAutoId = '$autoID'")->row_array();
        } else if($DocumentID=="JOB")
        {
            $currency = $this->db->query("SELECT transactionCurrency AS transactionCurrency FROM srp_erp_mfq_job WHERE workProcessID = '$autoID'")->row_array();
        } else if($DocumentID=="MDN")
        {
            $currency['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];;
        } else if($DocumentID=="SRM-ORD")
        {
            $currency['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];;
        } else if($DocumentID=="MCINV")
        {
            $currency = $this->db->query("SELECT srp_erp_currencymaster.CurrencyCode as transactionCurrency
                                FROM srp_erp_mfq_customerinvoicemaster
                                JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_mfq_customerinvoicemaster.transactionCurrencyID
                                WHERE invoiceAutoID = '$autoID'")->row_array();
        } else if($DocumentID=="EST")
        {
            $currency = $this->db->query("SELECT srp_erp_currencymaster.CurrencyCode as transactionCurrency
                                FROM srp_erp_mfq_estimatemaster
                                JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_mfq_estimatemaster.currencyID
                                WHERE estimateMasterID = '$autoID'")->row_array();
        }


        echo $this->treeStructure($datasd,$datasm,$currency['transactionCurrency']);


        //print_r($datasd);
        //return json_encode($datasss);
    }

    function treeStructure($detail,$master,$currency){
        $datas['detail']=$detail;
        $datas['master']=$master;
        $datasss='';
        $decimalplaces=2;
        if(!empty($currency)){
            $decimalplaces=fetch_currency_desimal($currency);
        }

        //print_r($datas['detail']);exit;
        $datasss.='[{"description":"<div style=\'font-weight: bold; color:white;background-color: #6F2DAB;border-bottom: solid;border-width: thin;border-color:rgb(75, 134, 183);font-size: 1.2em !important;text-align: center;\'><span>'.$datas['master']['documentName'].'</span></div><span style=\'text-align:left;cursor: pointer; font-size: 1.1em !important; font-weight:bold; \' class=\'texttree\'><a onclick=\'documentPageView_modal(\"'.$datas['master']['documentID'].'\",'.$datas['master']['documentAutoID'].')\'>'.$datas['master']['documentSystemCode'].'</a></span><br><span style=\'text-align:left;\' class=\'texttree\'><b>Date :- </b>'.$datas['master']['documentDate'].'</span><br><div><span style=\'text-align:left;\' class=\'texttree\'><b>Currency :-</b> '.$currency.' </span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Doc Amount :-</b> '. number_format($datas['master']['matchedAmount'],$decimalplaces) .' </span></div><div><span style=\'text-align:left;\' class=\'texttree\'><b>Tot Amount :-</b> '. number_format($datas['master']['documentAmount'],$decimalplaces) .'</span></div> ",';//<div><span style=\'text-align:left;\' class=\'texttree\'><b>Narration :-</b> '.ucwords($this->trim_value_tracinng($datas['master']['documentNarration'], 30)).'</span></div>
        $datasss.=' "children":[';
        $ch1=0;
        foreach($datas['detail'] as $val){
            $datasss.='{';
            $datasss.='"description":"<div style=\'font-weight: bold; color:white;background-color: #3BB143;border-bottom: solid;border-width: thin;border-color:rgb(75, 134, 183);font-size: 1.2em !important;text-align: center;\'><span>'.$val['documentName'].'</span></div><span style=\'text-align:left;cursor: pointer; font-size: 1.1em !important; font-weight:bold;\' class=\'texttree\'><a onclick=\'documentPageView_modal(\"'.$val['documentID'].'\",'.$val['documentAutoID'].')\'>'.$val['documentSystemCode'].'</a></span><br><span style=\'text-align:left;\' class=\'texttree\'><b>Date :-</b> '.$val['documentDate'].'</span> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Currency :-</b> '.$currency.' </span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Doc Amount :-</b> '. number_format($val['matchedAmount'],$decimalplaces) .'</span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Tot Amount :-</b> '. number_format($val['documentAmount'],$decimalplaces) .'</span></div> ",';//<div><span style=\'text-align:left;\' class=\'texttree\'><b>Narration :-</b> '.ucwords($this->trim_value_tracinng($val['documentNarration'], 30)).'</span></div>
            if(!empty($val['children'])){
                $datasss .= '"children":[';
                $ch2=0;
                foreach($val['children'] as $child2) {
                    $datasss.='{';
                    $datasss.='"description":"<div style=\'font-weight: bold; color:white;background-color: #FD6A02;border-bottom: solid;border-width: thin;border-color:rgb(75, 134, 183);font-size: 1.2em !important;text-align: center;\'><span>'.$child2['documentName'].'</span></div><span style=\'text-align:left;cursor: pointer; font-size: 1.1em !important; font-weight:bold;\' class=\'texttree\'><a onclick=\'documentPageView_modal(\"'.$child2['documentID'].'\",'.$child2['documentAutoID'].')\'>'.$child2['documentSystemCode'].'</a></span><br><span style=\'text-align:left;\' class=\'texttree\'><b>Date :-</b> '.$child2['documentDate'].'</span> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Currency :-</b> '.$currency.' </span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Doc Amount :-</b> '. number_format($child2['matchedAmount'],$decimalplaces) .'</span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Tot Amount :-</b> '. number_format($child2['documentAmount'],$decimalplaces) .'</span></div> ",';//<div><span style=\'text-align:left;\' class=\'texttree\'><b>Narration :-</b> '.ucwords($this->trim_value_tracinng($child2['documentNarration'], 30)).'</span></div>
                    if(!empty($child2['children'])){
                        $datasss .= '"children":[';
                        $ch3=0;
                        foreach($child2['children'] as $child3) {
                            $datasss.='{';
                            $datasss.='"description":"<div style=\'font-weight: bold; color:white;background-color: #598BAF;border-bottom: solid;border-width: thin;border-color:rgb(75, 134, 183);font-size: 1.2em !important;text-align: center;\'><span>'.$child3['documentName'].'</span></div><span style=\'text-align:left;cursor: pointer; font-size: 1.1em !important; font-weight:bold;\' class=\'texttree\'><a onclick=\'documentPageView_modal(\"'.$child3['documentID'].'\",'.$child3['documentAutoID'].')\'>'.$child3['documentSystemCode'].'</a></span><br><span style=\'text-align:left;\' class=\'texttree\'><b>Date :-</b> '.$child3['documentDate'].'</span> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Currency :-</b> '.$currency.' </span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Doc Amount :-</b> '. number_format($child3['matchedAmount'],$decimalplaces) .'</span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Tot Amount :-</b> '. number_format($child3['documentAmount'],$decimalplaces) .'</span></div> ",';//<div><span style=\'text-align:left;\' class=\'texttree\'><b>Narration :-</b> '.ucwords($this->trim_value_tracinng($child3['documentNarration'], 30)).'</span></div>
                                if(!empty($child3['children'])){
                                    $datasss .= '"children":[';
                                    $ch4=0;
                                    foreach($child3['children'] as $child4) {
                                        $datasss .= '{';
                                        $datasss.='"description":"<div style=\'font-weight: bold; color:white;background-color: #D21F3C;border-bottom: solid;border-width: thin;border-color:rgb(75, 134, 183);font-size: 1.2em !important;text-align: center;\'><span>'.$child4['documentName'].'</span></div><span style=\'text-align:left;cursor: pointer; font-size: 1.1em !important; font-weight:bold;\' class=\'texttree\'><a onclick=\'documentPageView_modal(\"'.$child4['documentID'].'\",'.$child4['documentAutoID'].')\'>'.$child4['documentSystemCode'].'</a></span><br><span style=\'text-align:left;\' class=\'texttree\'><b>Date :-</b> '.$child3['documentDate'].'</span> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Currency :-</b> '.$currency.' </span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Doc Amount :-</b> '. number_format($child4['matchedAmount'],$decimalplaces) .'</span></div> <div><span style=\'text-align:left;\' class=\'texttree\'><b>Tot Amount :-</b> '. number_format($child4['documentAmount'],$decimalplaces) .'</span></div> ",';//<div><span style=\'text-align:left;\' class=\'texttree\'><b>Narration :-</b> '.ucwords($this->trim_value_tracinng($child4['documentNarration'], 30)).'</span></div>
                                        $datasss.='"children":[]';
                                        $ch4++;
                                        if(count($child3['children'])>1){
                                            if (count($child3['children']) ==$ch4){
                                                $datasss .= '}';
                                            }else{
                                                $datasss .= '},';
                                            }
                                        }else{
                                            $datasss.='}';
                                        }
                                    }
                                    $datasss .= ']';
                                }else{
                                    $datasss.='"children":[]';
                                }
                            $ch3++;
                            if(count($child2['children'])>1){
                                if (count($child2['children']) ==$ch3){
                                    $datasss .= '}';
                                }else{
                                    $datasss .= '},';
                                }
                            }else{
                                $datasss.='}';
                            }
                        }
                        $datasss .= ']';

                    }else{
                        $datasss.='"children":[]';
                    }
                    $ch2++;
                    if(count($val['children'])>1){
                        if (count($val['children']) ==$ch2){
                            $datasss .= '}';
                        }else{
                            $datasss .= '},';
                        }
                    }else{
                        $datasss.='}';
                    }
                }
                $datasss .= ']';

            }else{
                $datasss.='"children":[]';
            }
            $ch1++;
            if(count($datas['detail'])>1) {
                if (count($datas['detail']) ==$ch1){
                    $datasss .= '}';
                }else{
                    $datasss .= '},';
                }
            }else{
                $datasss.='}';
            }
        }
        $datasss.=']';

        $datasss.='}]';




        return $datasss;
    }

    /*function buildTrees(array &$elements, $parentId = null)
    {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['relatedDocumentAutoID'] == $parentId) {
                $children =  $this->buildTrees($elements, $element['documentAutoID']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }*/

    function buildTrees(array &$elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['masterID'] == $parentId) {
                $children =$this->buildTrees($elements, $element['tracingAutoID']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['tracingAutoID']] = $element;
                unset($elements[$element['tracingAutoID']]);
            }
        }
        return $branch;
    }

    function deleteDocumentTracing(){
        $poid=$this->input->post('purchaseOrderID');
        $startDocumentID=$this->input->post('DocumentID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $startDocumentID);
        $this->db->where('startDocumentAutoID', $poid);
        $result=$this->db->delete('srp_erp_documenttracing');
        if($result){
            return array('s','success');
        }
    }


    function trim_value_tracinng($comments = '', $trimVal = 150)
    {
        $String = $comments;
        $truncated = (strlen($String) > $trimVal) ? substr($String, 0, $trimVal) . '<span class=\'tol\' rel=\'tooltip\' style=\'color:#0088cc\' title=\'' . str_replace('"', '&quot;', $String) . '\'> more </span>' : $String;

        return $truncated;
    }



    function trace_pr_document(){
        $prid = trim($this->input->post('purchaseRequestID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'PRQ');
        $this->db->where('startDocumentAutoID', $prid);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('purchaseRequestID,purchaseRequestCode,transactionAmount,documentDate,narration');
        $this->db->where('purchaseRequestID', $prid);
        $this->db->from('srp_erp_purchaserequestmaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'PRQ';
        $data['documentName'] = 'Purchase Request';
        $data['documentAutoID'] = $starting['purchaseRequestID'];
        $data['documentNarration'] = $starting['narration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'PRQ';
        $data['startDocumentAutoID'] = $starting['purchaseRequestID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['purchaseRequestCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDpr=$this->db->insert_id();
        if($insertStarting){
            $prDerails=$this->getPObyPR($starting['purchaseRequestID'],1,'PRQ',$starting['purchaseRequestID'],$masterIDpr);
            $prDerails=$this->getPVbyPR($starting['purchaseRequestID'],1,'PRQ',$starting['purchaseRequestID'],$masterIDpr);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'PRQ');
            $this->db->where('startDocumentAutoID', $starting['purchaseRequestID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getPObyPR($purchaseRequestID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];

        $poDetails = $this->db->query('SELECT
	pod.prMasterID,
	pod.purchaseOrderID,
pom.purchaseOrderCode,
(
		det.transactionAmount - pom.generalDiscountAmount
	) AS total_value,
pom.documentDate,
pom.narration,
SUM(pod.totalAmount) as matchedAmnt,
CASE
    WHEN pom.confirmedYN=0 THEN
    0
    WHEN pom.approvedYN = 1 THEN
    2
WHEN pom.confirmedYN=2 AND pom.approvedYN=0 THEN
    0
Else
    0
    END documentStatus
FROM
	srp_erp_purchaserequestmaster as prm
LEFT JOIN srp_erp_purchaseorderdetails as pod ON pod.prMasterID = prm.purchaseRequestID
LEFT JOIN srp_erp_purchaseordermaster as pom ON pom.purchaseOrderID = pod.purchaseOrderID

LEFT JOIN (
	SELECT
		SUM(totalAmount) AS transactionAmount,
		purchaseOrderID
	FROM
		srp_erp_purchaseorderdetails
	GROUP BY
		purchaseOrderID
) det ON (
	`det`.`purchaseOrderID` = pom.purchaseOrderID
)

WHERE
	purchaseRequestID IN ("'.$purchaseRequestID .'")
AND prm.companyID = "'.$compID.'"
AND prMasterID is NOT NULL GROUP BY pom.purchaseOrderID')->result_array();

        if(!empty($poDetails)){
            foreach($poDetails as $val){
                $dataPO['empID'] = current_userID();
                $dataPO['documentID'] = 'PO';
                $dataPO['documentName'] = 'Purchase Order';
                $dataPO['documentAutoID'] = $val['purchaseOrderID'];
                $dataPO['documentNarration'] = $val['narration'];
                $dataPO['relatedType'] = 1;
                $dataPO['relatedDocumentType'] = 'PRQ';
                $dataPO['relatedDocumentAutoID'] = $val['prMasterID'];
                $dataPO['levelNo'] = $levelNo;
                $dataPO['masterID'] = $masterID;
                $dataPO['documentStatus'] = $val['documentStatus'];
                $dataPO['startDocumentID'] = $startDocumentID;
                $dataPO['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPO['documentAmount'] = $val['total_value'];
                $dataPO['matchedAmount'] = $val['matchedAmnt'];
                $dataPO['documentDate'] = $val['documentDate'];
                $dataPO['documentSystemCode'] = $val['purchaseOrderCode'];
                $dataPO['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPO['createdUserGroup'] = $this->common_data['user_group'];
                $dataPO['createdPCID'] = $this->common_data['current_pc'];
                $dataPO['createdUserID'] = $this->common_data['current_userID'];
                $dataPO['createdUserName'] = $this->common_data['current_user'];
                $dataPO['createdDateTime'] = $this->common_data['current_date'];
                $insertPO=$this->db->insert('srp_erp_documenttracing', $dataPO);
                $masterIDpo=$this->db->insert_id();
                if($insertPO){
                    $grvDerails=$this->getGRVbyPO($val['purchaseOrderID'],2,$startDocumentID,$startDocumentAutoID,$masterIDpo);
                }
            }
        }
    }


    function trace_grv_document(){
        $grvid = trim($this->input->post('grvAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'GRV');
        $this->db->where('startDocumentAutoID', $grvid);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('grvAutoID,grvPrimaryCode,transactionAmount,transactionCurrencyID,grvDate,grvNarration');
        $this->db->where('grvAutoID', $grvid);
        $this->db->from('srp_erp_grvmaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'GRV';
        $data['documentName'] = 'Goods Received Voucher';
        $data['documentAutoID'] = $starting['grvAutoID'];
        $data['documentNarration'] = $starting['grvNarration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'GRV';
        $data['startDocumentAutoID'] = $starting['grvAutoID'];
        $data['documentDate'] = $starting['grvDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['grvPrimaryCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDgrv=$this->db->insert_id();
        if($insertStarting){
            $currenyID = $starting['transactionCurrencyID'];
            $grvDerails=$this->getBSIbyGRV($starting['grvAutoID'],1,'GRV',$starting['grvAutoID'],$masterIDgrv, $currenyID);
            $srDerails=$this->getSRbyGRV($starting['grvAutoID'],1,'GRV',$starting['grvAutoID'],$masterIDgrv, $currenyID);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'GRV');
            $this->db->where('startDocumentAutoID', $starting['grvAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }


    function trace_bsi_document(){
        $InvoiceAutoID = trim($this->input->post('InvoiceAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        //Remove existing tracing record
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'BSI');
        $this->db->where('startDocumentAutoID', $InvoiceAutoID);
        $this->db->delete('srp_erp_documenttracing');


        // $this->db->select('InvoiceAutoID,bookingInvCode,transactionAmount,invoiceDate,comments');
        // $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        // $this->db->from('srp_erp_paysupplierinvoicemaster');
        // $starting = $this->db->get()->row_array();

        $starting = $this->db->query("
            Select pmaster.InvoiceAutoID,pmaster.bookingInvCode,(pmaster.transactionAmount+IFNULL(sup_detail.taxAmount,0)) as transactionAmount,pmaster.invoiceDate,pmaster.comments
            FROM srp_erp_paysupplierinvoicemaster as pmaster
            LEFT JOIN (
                SELECT SUM(taxAmount) as  taxAmount, invoiceAutoID
                FROM srp_erp_paysupplierinvoicedetail as detail
                WHERE invoiceAutoID = '$InvoiceAutoID'
                GROUP BY invoiceAutoID
            ) as sup_detail ON pmaster.InvoiceAutoID = sup_detail.invoiceAutoID
            WHERE pmaster.invoiceAutoID = '$InvoiceAutoID'
        ")->row_array();

        // print_r($starting); exit;

        $data['empID'] = current_userID();
        $data['documentID'] = 'BSI';
        $data['documentName'] = 'Supplier Invoice';
        $data['documentAutoID'] = $starting['InvoiceAutoID'];
        $data['documentNarration'] = $starting['comments'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'BSI';
        $data['startDocumentAutoID'] = $starting['InvoiceAutoID'];
        $data['documentDate'] = $starting['invoiceDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['bookingInvCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDbsi=$this->db->insert_id();
        if($insertStarting){
            $bsiDerails=$this->getPVbyBSI($starting['InvoiceAutoID'],1,'BSI',$starting['InvoiceAutoID'],$masterIDbsi);
            $dnDerails=$this->getDNbyBSI($starting['InvoiceAutoID'],1,'BSI',$starting['InvoiceAutoID'],$masterIDbsi);
            $pvmDerails=$this->getPVMbyBSI($starting['InvoiceAutoID'],1,'BSI',$starting['InvoiceAutoID'],$masterIDbsi);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'BSI');
            $this->db->where('startDocumentAutoID', $starting['InvoiceAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getDNbyBSI($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID, $exchangeRate = 1){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
                                            dnm.debitNoteMasterAutoID,
                                            dnd.InvoiceAutoID,
                                            dnm.debitNoteDate,
                                            dnm.debitNoteCode,
                                            det.transactionAmount AS total_value,
                                            dnm.comments,
                                            SUM(dnd.transactionAmount) as matchedAmnt,
                                            CASE
                                                WHEN dnm.confirmedYN=0 THEN 0
                                                WHEN dnm.approvedYN = 1 THEN 2
                                                WHEN dnm.confirmedYN=2 AND dnm.approvedYN=0 THEN 0
                                                Else 0
                                            END documentStatus
                                        FROM
                                            srp_erp_debitnotedetail as dnd
                                        LEFT JOIN srp_erp_debitnotemaster as dnm ON dnd.debitNoteMasterAutoID = dnm.debitNoteMasterAutoID
                                        LEFT JOIN (SELECT SUM(transactionAmount) AS transactionAmount, debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det ON (`det`.`debitNoteMasterAutoID` = dnm.debitNoteMasterAutoID)
                                        WHERE
                                            dnd.InvoiceAutoID IN ("'.$InvoiceAutoID .'")
                                            AND dnd.companyID = "'.$compID.'"
                                            AND dnd.InvoiceAutoID is NOT NULL
                                        GROUP BY dnd.debitNoteMasterAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataDN['empID'] = current_userID();
                $dataDN['documentID'] = 'DN';
                $dataDN['documentName'] = 'Debit Note';
                $dataDN['documentAutoID'] = $val['debitNoteMasterAutoID'];
                $dataDN['documentNarration'] = $val['comments'];
                $dataDN['relatedType'] = 2;
                $dataDN['relatedDocumentType'] = 'BSI';
                $dataDN['relatedDocumentAutoID'] = $val['InvoiceAutoID'];
                $dataDN['levelNo'] = $levelNo;
                $dataDN['masterID'] = $masterID;
                $dataDN['documentStatus'] = $val['documentStatus'];
                $dataDN['startDocumentID'] = $startDocumentID;
                $dataDN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataDN['documentAmount'] = $val['total_value'] / $exchangeRate;
                $dataDN['matchedAmount'] = $val['matchedAmnt'] / $exchangeRate;
                $dataDN['documentDate'] = $val['debitNoteDate'];
                $dataDN['documentSystemCode'] = $val['debitNoteCode'];
                $dataDN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataDN['createdUserGroup'] = $this->common_data['user_group'];
                $dataDN['createdPCID'] = $this->common_data['current_pc'];
                $dataDN['createdUserID'] = $this->common_data['current_userID'];
                $dataDN['createdUserName'] = $this->common_data['current_user'];
                $dataDN['createdDateTime'] = $this->common_data['current_date'];
                $insertDN=$this->db->insert('srp_erp_documenttracing', $dataDN);
                if($insertDN){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }


    function getPVMbyBSI($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID, $exchangeRate = 1){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
                                            pvmm.matchID,
                                            pvmd.InvoiceAutoID,
                                            pvmm.matchDate,
                                            pvmm.matchSystemCode,
                                            pvmm.Narration,
                                            det.transactionAmount AS total_value,
                                            SUM(pvmd.transactionAmount) as matchedAmnt,
                                            CASE
                                                WHEN pvmm.confirmedYN=0 THEN 0
                                                WHEN pvmm.confirmedYN = 1 THEN 1
                                                WHEN pvmm.confirmedYN=2 THEN 0
                                                Else 1
                                            END documentStatus
                                        FROM
                                            srp_erp_pvadvancematchdetails as pvmd
                                        LEFT JOIN srp_erp_pvadvancematch as pvmm ON pvmd.matchID = pvmm.matchID
                                        LEFT JOIN (SELECT SUM(transactionAmount) AS transactionAmount, matchID FROM srp_erp_pvadvancematchdetails GROUP BY matchID) det ON (`det`.`matchID` = pvmm.matchID)
                                        WHERE
                                            pvmd.InvoiceAutoID IN ("'.$InvoiceAutoID .'")
                                            AND pvmd.companyID = "'.$compID.'"
                                            AND pvmd.InvoiceAutoID is NOT NULL
                                        GROUP BY pvmd.matchID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataPVM['empID'] = current_userID();
                $dataPVM['documentID'] = 'PVM';
                $dataPVM['documentName'] = 'Payment Matching';
                $dataPVM['documentAutoID'] = $val['matchID'];
                $dataPVM['documentNarration'] = $val['Narration'];
                $dataPVM['relatedType'] = 2;
                $dataPVM['relatedDocumentType'] = 'BSI';
                $dataPVM['relatedDocumentAutoID'] = $val['InvoiceAutoID'];
                $dataPVM['levelNo'] = $levelNo;
                $dataPVM['masterID'] = $masterID;
                $dataPVM['documentStatus'] = $val['documentStatus'];
                $dataPVM['startDocumentID'] = $startDocumentID;
                $dataPVM['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPVM['documentAmount'] = $val['total_value'] / $exchangeRate;
                $dataPVM['matchedAmount'] = $val['matchedAmnt'] / $exchangeRate;
                $dataPVM['documentDate'] = $val['matchDate'];
                $dataPVM['documentSystemCode'] = $val['matchSystemCode'];
                $dataPVM['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPVM['createdUserGroup'] = $this->common_data['user_group'];
                $dataPVM['createdPCID'] = $this->common_data['current_pc'];
                $dataPVM['createdUserID'] = $this->common_data['current_userID'];
                $dataPVM['createdUserName'] = $this->common_data['current_user'];
                $dataPVM['createdDateTime'] = $this->common_data['current_date'];
                $insertPVM=$this->db->insert('srp_erp_documenttracing', $dataPVM);
                if($insertPVM){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }


    function trace_cnt_document(){
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='Contract';
        if($DocumentID=='QUT'){
            $docName='Quotation';
        }elseif($DocumentID=='SO'){
            $docName='Sales Order';
        }else{
            $docName='Contract';
        }

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $contractAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('contractAutoID,contractCode,transactionAmount,contractDate,contractNarration');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['contractAutoID'];
        $data['documentNarration'] = $starting['contractNarration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['contractAutoID'];
        $data['documentDate'] = $starting['contractDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['contractCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDcnt=$this->db->insert_id();
        if($insertStarting){
            $bsiDerails=$this->getCINVbyCNT($starting['contractAutoID'],1,$DocumentID,$starting['contractAutoID'],$masterIDcnt);
            $DODerails=$this->getDObyCNT($starting['contractAutoID'],1,$DocumentID,$starting['contractAutoID'],$masterIDcnt);

        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', $DocumentID);
            $this->db->where('startDocumentAutoID', $contractAutoID);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getCINVbyCNT($contractAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	cinvm.invoiceAutoID,
	cinvd.contractAutoID,
cinvm.invoiceDate,
cinvm.invoiceCode,
cinvm.invoiceNarration,
(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value,
SUM(cinvd.transactionAmount) as matchedAmnt,
CASE
    WHEN cinvm.confirmedYN=0 THEN
    0
    WHEN cinvm.approvedYN = 1 THEN
    2
WHEN cinvm.confirmedYN=2 AND cinvm.approvedYN=0 THEN
    0
Else
    0
    END documentStatus
FROM
	srp_erp_customerinvoicedetails as cinvd
LEFT JOIN srp_erp_customerinvoicemaster as cinvm ON cinvd.invoiceAutoID = cinvm.invoiceAutoID

LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = cinvm.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = cinvm.InvoiceAutoID
)
WHERE
	cinvd.contractAutoID IN ("'.$contractAutoID .'")
AND cinvd.companyID = "'.$compID.'"
AND cinvd.contractAutoID is NOT NULL
GROUP BY cinvd.invoiceAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCINV['empID'] = current_userID();
                $dataCINV['documentID'] = 'CINV';
                $dataCINV['documentName'] = 'Customer Invoice';
                $dataCINV['documentAutoID'] = $val['invoiceAutoID'];
                $dataCINV['documentNarration'] = $val['invoiceNarration'];
                $dataCINV['relatedType'] = 2;
                $dataCINV['relatedDocumentType'] = $startDocumentID;
                $dataCINV['relatedDocumentAutoID'] = $val['contractAutoID'];
                $dataCINV['levelNo'] = $levelNo;
                $dataCINV['masterID'] = $masterID;
                $dataCINV['documentStatus'] = $val['documentStatus'];
                $dataCINV['startDocumentID'] = $startDocumentID;
                $dataCINV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCINV['documentAmount'] = $val['total_value'];
                $dataCINV['matchedAmount'] = $val['matchedAmnt'];
                $dataCINV['documentDate'] = $val['invoiceDate'];
                $dataCINV['documentSystemCode'] = $val['invoiceCode'];
                $dataCINV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCINV['createdUserGroup'] = $this->common_data['user_group'];
                $dataCINV['createdPCID'] = $this->common_data['current_pc'];
                $dataCINV['createdUserID'] = $this->common_data['current_userID'];
                $dataCINV['createdUserName'] = $this->common_data['current_user'];
                $dataCINV['createdDateTime'] = $this->common_data['current_date'];
                $insertCINV=$this->db->insert('srp_erp_documenttracing', $dataCINV);
                $masterIDcinv=$this->db->insert_id();
                if($insertCINV){
                    $levelNog=$levelNo+1;
                    $SRDerails=$this->getSLRbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $CNDerails=$this->getCNbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $CNDerails=$this->getRVbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $CNDerails=$this->getRVMbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getDObyCNT($contractAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query("SELECT
srp_erp_deliveryorder.DOAutoID,
	srp_erp_deliveryorderdetails.contractAutoID,
	DODate,
	DOCode,
	narration,
SUM(srp_erp_deliveryorderdetails.deliveredTransactionAmount) AS matchedValue,
srp_erp_deliveryorder.deliveredTransactionAmount AS totalValue,
	CASE
		
		WHEN confirmedYN = 0 THEN
		0 
		WHEN approvedYN = 1 THEN
		2 
		WHEN confirmedYN = 2 
		AND approvedYN = 0 THEN
			0 ELSE 0 
		END documentStatus 
FROM
	srp_erp_deliveryorderdetails
	INNER JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
	WHERE contractAutoID IN ($contractAutoID)
	AND srp_erp_deliveryorderdetails.companyID = {$compID}
	AND contractAutoID is NOT NULL
	GROUP BY srp_erp_deliveryorder.DOAutoID")->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCINV['empID'] = current_userID();
                $dataCINV['documentID'] = 'DO';
                $dataCINV['documentName'] = 'Delivery Order';
                $dataCINV['documentAutoID'] = $val['DOAutoID'];
                $dataCINV['documentNarration'] = $val['narration'];
                $dataCINV['relatedType'] = 2;
                $dataCINV['relatedDocumentType'] = $startDocumentID;
                $dataCINV['relatedDocumentAutoID'] = $val['contractAutoID'];
                $dataCINV['levelNo'] = $levelNo;
                $dataCINV['masterID'] = $masterID;
                $dataCINV['documentStatus'] = $val['documentStatus'];
                $dataCINV['startDocumentID'] = $startDocumentID;
                $dataCINV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCINV['documentAmount'] = $val['totalValue'];
                $dataCINV['matchedAmount'] = $val['matchedValue'];
                $dataCINV['documentDate'] = $val['DODate'];
                $dataCINV['documentSystemCode'] = $val['DOCode'];
                $dataCINV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCINV['createdUserGroup'] = $this->common_data['user_group'];
                $dataCINV['createdPCID'] = $this->common_data['current_pc'];
                $dataCINV['createdUserID'] = $this->common_data['current_userID'];
                $dataCINV['createdUserName'] = $this->common_data['current_user'];
                $dataCINV['createdDateTime'] = $this->common_data['current_date'];
                $insertCINV=$this->db->insert('srp_erp_documenttracing', $dataCINV);
                $masterIDcinv=$this->db->insert_id();
                if($insertCINV){
                    $levelNog=$levelNo+1;
                    $bsiDerails=$this->getCINVbyDO($val['DOAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $SRDerails=$this->getSLRbyDO($val['DOAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
//                    $CNDerails=$this->getCNbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
//                    $CNDerails=$this->getRVbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
//                    $CNDerails=$this->getRVMbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                }
            }
        }
    }

    function getSLRbyDO($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	srm.salesReturnAutoID,
	srd.DOAutoID,
srm.returnDate,
srm.salesReturnCode,
srm.`comment`,
SUM(srd.totalValue) as matchedAmnt,
CASE
    WHEN srm.confirmedYN=0 THEN
    0
    WHEN srm.approvedYN = 1 THEN
    2
WHEN srm.confirmedYN=2 AND srm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_salesreturndetails as srd
LEFT JOIN srp_erp_salesreturnmaster as srm ON srd.salesReturnAutoID = srm.salesReturnAutoID


WHERE
	srd.DOAutoID IN ("'.$InvoiceAutoID .'")
AND srd.companyID = "'.$compID.'"
AND srd.DOAutoID is NOT NULL
GROUP BY srd.salesReturnAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataSLR['empID'] = current_userID();
                $dataSLR['documentID'] = 'SLR';
                $dataSLR['documentName'] = 'Sales Return';
                $dataSLR['documentAutoID'] = $val['salesReturnAutoID'];
                $dataSLR['documentNarration'] = $val['comment'];
                $dataSLR['relatedType'] = 2;
                $dataSLR['relatedDocumentType'] = 'CINV';
                $dataSLR['relatedDocumentAutoID'] = $val['DOAutoID'];
                $dataSLR['levelNo'] = $levelNo;
                $dataSLR['masterID'] = $masterID;
                $dataSLR['documentStatus'] = $val['documentStatus'];
                $dataSLR['startDocumentID'] = $startDocumentID;
                $dataSLR['startDocumentAutoID'] = $startDocumentAutoID;
                $dataSLR['documentAmount'] = $val['matchedAmnt'];
                $dataSLR['matchedAmount'] = $val['matchedAmnt'];
                $dataSLR['documentDate'] = $val['returnDate'];
                $dataSLR['documentSystemCode'] = $val['salesReturnCode'];
                $dataSLR['companyID'] = $this->common_data['company_data']['company_id'];
                $dataSLR['createdUserGroup'] = $this->common_data['user_group'];
                $dataSLR['createdPCID'] = $this->common_data['current_pc'];
                $dataSLR['createdUserID'] = $this->common_data['current_userID'];
                $dataSLR['createdUserName'] = $this->common_data['current_user'];
                $dataSLR['createdDateTime'] = $this->common_data['current_date'];
                $insertSLR=$this->db->insert('srp_erp_documenttracing', $dataSLR);
                if($insertSLR){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getCINVbyDO($contractAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	cinvm.invoiceAutoID,
	cinvd.contractAutoID,
cinvm.invoiceDate,
cinvm.invoiceCode,
cinvm.invoiceNarration,
(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value,
SUM(cinvd.transactionAmount) as matchedAmnt,
CASE
    WHEN cinvm.confirmedYN=0 THEN
    0
    WHEN cinvm.approvedYN = 1 THEN
    2
WHEN cinvm.confirmedYN=2 AND cinvm.approvedYN=0 THEN
    0
Else
    0
    END documentStatus
FROM
	srp_erp_customerinvoicedetails as cinvd
LEFT JOIN srp_erp_customerinvoicemaster as cinvm ON cinvd.invoiceAutoID = cinvm.invoiceAutoID

LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = cinvm.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = cinvm.InvoiceAutoID
)
WHERE
	cinvd.DOMasterID IN ("'.$contractAutoID .'")
AND cinvd.companyID = "'.$compID.'"
AND cinvd.DOMasterID is NOT NULL
GROUP BY cinvd.invoiceAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCINV['empID'] = current_userID();
                $dataCINV['documentID'] = 'CINV';
                $dataCINV['documentName'] = 'Customer Invoice';
                $dataCINV['documentAutoID'] = $val['invoiceAutoID'];
                $dataCINV['documentNarration'] = $val['invoiceNarration'];
                $dataCINV['relatedType'] = 2;
                $dataCINV['relatedDocumentType'] = $startDocumentID;
                $dataCINV['relatedDocumentAutoID'] = $val['contractAutoID'];
                $dataCINV['levelNo'] = $levelNo;
                $dataCINV['masterID'] = $masterID;
                $dataCINV['documentStatus'] = $val['documentStatus'];
                $dataCINV['startDocumentID'] = $startDocumentID;
                $dataCINV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCINV['documentAmount'] = $val['total_value'];
                $dataCINV['matchedAmount'] = $val['matchedAmnt'];
                $dataCINV['documentDate'] = $val['invoiceDate'];
                $dataCINV['documentSystemCode'] = $val['invoiceCode'];
                $dataCINV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCINV['createdUserGroup'] = $this->common_data['user_group'];
                $dataCINV['createdPCID'] = $this->common_data['current_pc'];
                $dataCINV['createdUserID'] = $this->common_data['current_userID'];
                $dataCINV['createdUserName'] = $this->common_data['current_user'];
                $dataCINV['createdDateTime'] = $this->common_data['current_date'];
                $insertCINV=$this->db->insert('srp_erp_documenttracing', $dataCINV);
                $masterIDcinv=$this->db->insert_id();
                if($insertCINV){
                    $levelNog=$levelNo+1;
                    $SRDerails=$this->getSLRbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $CNDerails=$this->getCNbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $CNDerails=$this->getRVbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                    $CNDerails=$this->getRVMbyCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDcinv);
                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getSLRbyCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	srm.salesReturnAutoID,
	srd.invoiceAutoID,
srm.returnDate,
srm.salesReturnCode,
srm.`comment`,
SUM(srd.totalValue) as matchedAmnt,
CASE
    WHEN srm.confirmedYN=0 THEN
    0
    WHEN srm.approvedYN = 1 THEN
    2
WHEN srm.confirmedYN=2 AND srm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_salesreturndetails as srd
LEFT JOIN srp_erp_salesreturnmaster as srm ON srd.salesReturnAutoID = srm.salesReturnAutoID


WHERE
	srd.invoiceAutoID IN ("'.$InvoiceAutoID .'")
AND srd.companyID = "'.$compID.'"
AND srd.invoiceAutoID is NOT NULL
GROUP BY srd.salesReturnAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataSLR['empID'] = current_userID();
                $dataSLR['documentID'] = 'SLR';
                $dataSLR['documentName'] = 'Sales Return';
                $dataSLR['documentAutoID'] = $val['salesReturnAutoID'];
                $dataSLR['documentNarration'] = $val['comment'];
                $dataSLR['relatedType'] = 2;
                $dataSLR['relatedDocumentType'] = 'CINV';
                $dataSLR['relatedDocumentAutoID'] = $val['invoiceAutoID'];
                $dataSLR['levelNo'] = $levelNo;
                $dataSLR['masterID'] = $masterID;
                $dataSLR['documentStatus'] = $val['documentStatus'];
                $dataSLR['startDocumentID'] = $startDocumentID;
                $dataSLR['startDocumentAutoID'] = $startDocumentAutoID;
                $dataSLR['documentAmount'] = $val['matchedAmnt'];
                $dataSLR['matchedAmount'] = $val['matchedAmnt'];
                $dataSLR['documentDate'] = $val['returnDate'];
                $dataSLR['documentSystemCode'] = $val['salesReturnCode'];
                $dataSLR['companyID'] = $this->common_data['company_data']['company_id'];
                $dataSLR['createdUserGroup'] = $this->common_data['user_group'];
                $dataSLR['createdPCID'] = $this->common_data['current_pc'];
                $dataSLR['createdUserID'] = $this->common_data['current_userID'];
                $dataSLR['createdUserName'] = $this->common_data['current_user'];
                $dataSLR['createdDateTime'] = $this->common_data['current_date'];
                $insertSLR=$this->db->insert('srp_erp_documenttracing', $dataSLR);
                if($insertSLR){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getCNbyCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	cnm.creditNoteMasterAutoID,
	cnd.invoiceAutoID,
cnm.creditNoteDate,
cnm.creditNoteCode,
cnm.`comments`,
det.transactionAmount AS total_value,
SUM(cnd.transactionAmount) as matchedAmnt,
CASE
    WHEN cnm.confirmedYN=0 THEN
    0
    WHEN cnm.approvedYN = 1 THEN
    2
WHEN cnm.confirmedYN=2 AND cnm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_creditnotedetail as cnd
LEFT JOIN srp_erp_creditnotemaster as cnm ON cnd.creditNoteMasterAutoID = cnm.creditNoteMasterAutoID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
	GROUP BY
		creditNoteMasterAutoID
) det ON (
	`det`.`creditNoteMasterAutoID` = cnm.creditNoteMasterAutoID
)

WHERE
	cnd.invoiceAutoID IN ("'.$InvoiceAutoID .'")
AND cnd.companyID = "'.$compID.'"
AND cnd.invoiceAutoID is NOT NULL
GROUP BY cnd.creditNoteMasterAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCN['empID'] = current_userID();
                $dataCN['documentID'] = 'CN';
                $dataCN['documentName'] = 'Credit Note';
                $dataCN['documentAutoID'] = $val['creditNoteMasterAutoID'];
                $dataCN['documentNarration'] = $val['comments'];
                $dataCN['relatedType'] = 2;
                $dataCN['relatedDocumentType'] = 'CINV';
                $dataCN['relatedDocumentAutoID'] = $val['invoiceAutoID'];
                $dataCN['levelNo'] = $levelNo;
                $dataCN['masterID'] = $masterID;
                $dataCN['documentStatus'] = $val['documentStatus'];
                $dataCN['startDocumentID'] = $startDocumentID;
                $dataCN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCN['documentAmount'] = $val['total_value'];
                $dataCN['matchedAmount'] = $val['matchedAmnt'];
                $dataCN['documentDate'] = $val['creditNoteDate'];
                $dataCN['documentSystemCode'] = $val['creditNoteCode'];
                $dataCN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCN['createdUserGroup'] = $this->common_data['user_group'];
                $dataCN['createdPCID'] = $this->common_data['current_pc'];
                $dataCN['createdUserID'] = $this->common_data['current_userID'];
                $dataCN['createdUserName'] = $this->common_data['current_user'];
                $dataCN['createdDateTime'] = $this->common_data['current_date'];
                $insertCN=$this->db->insert('srp_erp_documenttracing', $dataCN);
                if($insertCN){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getRVbyCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	rvm.receiptVoucherAutoId,
	rvd.invoiceAutoID,
rvm.RVdate,
rvm.RVcode,
rvm.`RVNarration`,
(
	(
		(
			IFNULL(addondet.taxPercentage, 0) / 100
		) * IFNULL(
			tyepdet.transactionAmount,
			0
		)
	) + IFNULL(det.transactionAmount, 0)
) AS total_value,
SUM(rvd.transactionAmount) as matchedAmnt,
CASE
    WHEN rvm.confirmedYN=0 THEN
    0
    WHEN rvm.approvedYN = 1 THEN
    2
WHEN rvm.confirmedYN=2 AND rvm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_customerreceiptdetail as rvd
LEFT JOIN srp_erp_customerreceiptmaster as rvm ON rvd.receiptVoucherAutoId = rvm.receiptVoucherAutoId
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceiptdetail
	GROUP BY
		receiptVoucherAutoId
) det ON (
	`det`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceipttaxdetails
	GROUP BY
		receiptVoucherAutoId
) addondet ON (
	`addondet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId
)
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceiptdetail
	WHERE
		srp_erp_customerreceiptdetail.type = "GL"
	OR srp_erp_customerreceiptdetail.type = "Item"
	GROUP BY
		receiptVoucherAutoId
) tyepdet ON (
	`tyepdet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId
)

WHERE
	rvd.invoiceAutoID IN ("'.$InvoiceAutoID .'")
AND rvd.companyID = "'.$compID.'"
AND rvd.invoiceAutoID is NOT NULL
GROUP BY rvd.receiptVoucherAutoId')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCN['empID'] = current_userID();
                $dataCN['documentID'] = 'RV';
                $dataCN['documentName'] = 'Receipt Voucher';
                $dataCN['documentAutoID'] = $val['receiptVoucherAutoId'];
                $dataCN['documentNarration'] = $val['RVNarration'];
                $dataCN['relatedType'] = 2;
                $dataCN['relatedDocumentType'] = 'CINV';
                $dataCN['relatedDocumentAutoID'] = $val['invoiceAutoID'];
                $dataCN['levelNo'] = $levelNo;
                $dataCN['masterID'] = $masterID;
                $dataCN['documentStatus'] = $val['documentStatus'];
                $dataCN['startDocumentID'] = $startDocumentID;
                $dataCN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCN['documentAmount'] = $val['total_value'];
                $dataCN['matchedAmount'] = $val['matchedAmnt'];
                $dataCN['documentDate'] = $val['RVdate'];
                $dataCN['documentSystemCode'] = $val['RVcode'];
                $dataCN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCN['createdUserGroup'] = $this->common_data['user_group'];
                $dataCN['createdPCID'] = $this->common_data['current_pc'];
                $dataCN['createdUserID'] = $this->common_data['current_userID'];
                $dataCN['createdUserName'] = $this->common_data['current_user'];
                $dataCN['createdDateTime'] = $this->common_data['current_date'];
                $insertCN=$this->db->insert('srp_erp_documenttracing', $dataCN);
                if($insertCN){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getRVMbyCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	rvmm.matchID,
	rvmd.InvoiceAutoID,
rvmm.matchDate,
rvmm.matchSystemCode,
rvmm.`Narration`,
det.transactionAmount AS total_value,
SUM(rvmd.transactionAmount) as matchedAmnt,
CASE
    WHEN rvmm.confirmedYN=0 THEN
    0
    WHEN rvmm.confirmedYN = 1 THEN
    1
WHEN rvmm.confirmedYN=2 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_rvadvancematchdetails as rvmd
LEFT JOIN srp_erp_rvadvancematch as rvmm ON rvmd.matchID = rvmm.matchID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		matchID
	FROM
		srp_erp_rvadvancematchdetails
	GROUP BY
		matchID
) det ON (
	`det`.`matchID` = rvmm.matchID
)

WHERE
	rvmd.InvoiceAutoID IN ("'.$InvoiceAutoID .'")
AND rvmd.companyID = "'.$compID.'"
AND rvmd.InvoiceAutoID is NOT NULL
GROUP BY rvmd.matchID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataRVM['empID'] = current_userID();
                $dataRVM['documentID'] = 'RVM';
                $dataRVM['documentName'] = 'Receipt Voucher Match';
                $dataRVM['documentAutoID'] = $val['matchID'];
                $dataRVM['documentNarration'] = $val['Narration'];
                $dataRVM['relatedType'] = 2;
                $dataRVM['relatedDocumentType'] = 'CINV';
                $dataRVM['relatedDocumentAutoID'] = $val['InvoiceAutoID'];
                $dataRVM['levelNo'] = $levelNo;
                $dataRVM['masterID'] = $masterID;
                $dataRVM['documentStatus'] = $val['documentStatus'];
                $dataRVM['startDocumentID'] = $startDocumentID;
                $dataRVM['startDocumentAutoID'] = $startDocumentAutoID;
                $dataRVM['documentAmount'] = $val['total_value'];
                $dataRVM['matchedAmount'] = $val['matchedAmnt'];
                $dataRVM['documentDate'] = $val['matchDate'];
                $dataRVM['documentSystemCode'] = $val['matchSystemCode'];
                $dataRVM['companyID'] = $this->common_data['company_data']['company_id'];
                $dataRVM['createdUserGroup'] = $this->common_data['user_group'];
                $dataRVM['createdPCID'] = $this->common_data['current_pc'];
                $dataRVM['createdUserID'] = $this->common_data['current_userID'];
                $dataRVM['createdUserName'] = $this->common_data['current_user'];
                $dataRVM['createdDateTime'] = $this->common_data['current_date'];
                $insertRVM=$this->db->insert('srp_erp_documenttracing', $dataRVM);
                if($insertRVM){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }


    function trace_cinv_document(){
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='Customer Invoice';

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $invoiceAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('invoiceAutoID,invoiceCode,transactionAmount,invoiceDate,invoiceNarration');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicemaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['invoiceAutoID'];
        $data['documentNarration'] = $starting['invoiceNarration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['invoiceAutoID'];
        $data['documentDate'] = $starting['invoiceDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['invoiceCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDcinv=$this->db->insert_id();
        if($insertStarting){
            $SRDerails=$this->getSLRbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getCNbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getRVbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getRVMbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'CINV');
            $this->db->where('startDocumentAutoID', $starting['invoiceAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }


    function getSRbyGRV($grvAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID, $currenyID = ''){
        $compID=$this->common_data['company_data']['company_id'];
        $srDetails = $this->db->query('SELECT
                                        srm.stockReturnAutoID,
                                        srd.grvAutoID,
                                        srm.transactionCurrencyID,
                                        srm.stockReturnCode,
                                        srm.returnDate,
                                        srm.`comment`,
                                        SUM(srd.totalValue) as matchedAmnt,
                                        CASE
                                            WHEN srm.confirmedYN=0 THEN 0
                                            WHEN srm.approvedYN = 1 THEN 2
                                            WHEN srm.confirmedYN=2 AND srm.approvedYN=0 THEN 0
                                            Else 0
                                        END documentStatus
                                    FROM
                                        srp_erp_stockreturndetails as srd
                                    LEFT JOIN srp_erp_stockreturnmaster as srm ON srd.stockReturnAutoID = srm.stockReturnAutoID
                                    WHERE
                                        srd.grvAutoID IN ("'.$grvAutoID .'")
                                        AND srm.companyID = "'.$compID.'"
                                        AND srd.grvAutoID is NOT NULL
                                        GROUP BY srd.stockReturnAutoID')->result_array();


        if(!empty($srDetails)){
            foreach($srDetails as $val){
                $exchangeRate['conversion'] = 1;
                if($currenyID != '' && $currenyID != $val['transactionCurrencyID']) {
                    $exchangeRate = currency_conversionID($val['transactionCurrencyID'], $currenyID);
                }
                $levelNog=0;
                $dataSR['empID'] = current_userID();
                $dataSR['documentID'] = 'SR';
                $dataSR['documentName'] = 'Purchase Return';
                $dataSR['documentAutoID'] = $val['stockReturnAutoID'];
                $dataSR['documentNarration'] = $val['comment'];
                $dataSR['relatedType'] = 2;
                $dataSR['relatedDocumentType'] = 'GRV';
                $dataSR['relatedDocumentAutoID'] = $val['grvAutoID'];
                $dataSR['levelNo'] = $levelNo;
                $dataSR['masterID'] = $masterID;
                $dataSR['documentStatus'] = $val['documentStatus'];
                $dataSR['startDocumentID'] = $startDocumentID;
                $dataSR['startDocumentAutoID'] = $startDocumentAutoID;
                $dataSR['documentAmount'] = $val['matchedAmnt'] / $exchangeRate['conversion'];
                $dataSR['matchedAmount'] = $val['matchedAmnt'] / $exchangeRate['conversion'];
                $dataSR['documentDate'] = $val['returnDate'];
                $dataSR['documentSystemCode'] = $val['stockReturnCode'];
                $dataSR['companyID'] = $this->common_data['company_data']['company_id'];
                $dataSR['createdUserGroup'] = $this->common_data['user_group'];
                $dataSR['createdPCID'] = $this->common_data['current_pc'];
                $dataSR['createdUserID'] = $this->common_data['current_userID'];
                $dataSR['createdUserName'] = $this->common_data['current_user'];
                $dataSR['createdDateTime'] = $this->common_data['current_date'];
                $insertSR=$this->db->insert('srp_erp_documenttracing', $dataSR);
                if($insertSR){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }


    function getBSIbyPO($purchaseOrderID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $bsiDetails = $this->db->query('SELECT
                                            sinvm.InvoiceAutoID,
                                            sinv.purchaseOrderMastertID,
                                            sinvm.bookingInvCode,
                                            (((IFNULL(addondet.taxPercentage, 0) / 100) * IFNULL(det.transactionAmount, 0)) + IFNULL(det.transactionAmount, 0)) AS total_value,
                                            sinvm.bookingDate,
                                            sinvm.comments,
                                            SUM(sinv.transactionAmount + IFNULL(taxAmount, 0)) as matchedAmnt,
                                            CASE
                                                WHEN sinvm.confirmedYN=0 THEN 0
                                                WHEN sinvm.approvedYN = 1 THEN 2
                                                WHEN sinvm.confirmedYN=2 AND sinvm.approvedYN=0 THEN 0
                                                Else 0
                                            END documentStatus
                                        FROM
                                            srp_erp_paysupplierinvoicedetail as sinv
                                        LEFT JOIN srp_erp_paysupplierinvoicemaster as sinvm ON sinv.InvoiceAutoID = sinvm.InvoiceAutoID
                                        LEFT JOIN (
                                            SELECT
                                                SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                                                InvoiceAutoID
                                            FROM
                                                srp_erp_paysupplierinvoicedetail
                                            GROUP BY
                                                InvoiceAutoID
                                        ) det ON (`det`.`InvoiceAutoID` = sinvm.InvoiceAutoID)
                                        LEFT JOIN (
                                            SELECT
                                                SUM(taxPercentage) AS taxPercentage,
                                                InvoiceAutoID
                                            FROM
                                                srp_erp_paysupplierinvoicetaxdetails
                                            GROUP BY
                                                InvoiceAutoID
                                        ) addondet ON (
                                            `addondet`.`InvoiceAutoID` = sinvm.InvoiceAutoID
                                        )
                                        WHERE
                                            sinv.purchaseOrderMastertID = "'.$purchaseOrderID.'"
                                            AND sinv.companyID = "'.$compID.'"
                                            AND sinv.purchaseOrderMastertID is NOT NULL
                                            GROUP BY sinvm.InvoiceAutoID')->result_array();


        if(!empty($bsiDetails)){
            foreach($bsiDetails as $val){
                $levelNog=0;
                $dataBSI['empID'] = current_userID();
                $dataBSI['documentID'] = 'BSI';
                $dataBSI['documentName'] = 'Supplier Invoice';
                $dataBSI['documentAutoID'] = $val['InvoiceAutoID'];
                $dataBSI['documentNarration'] = $val['comments'];
                $dataBSI['relatedType'] = 2;
                $dataBSI['relatedDocumentType'] = 'PO';
                $dataBSI['relatedDocumentAutoID'] = $val['purchaseOrderMastertID'];
                $dataBSI['levelNo'] = $levelNo;
                $dataBSI['masterID'] = $masterID;
                $dataBSI['documentStatus'] = $val['documentStatus'];
                $dataBSI['startDocumentID'] = $startDocumentID;
                $dataBSI['startDocumentAutoID'] = $startDocumentAutoID;
                $dataBSI['documentAmount'] = $val['total_value'];
                $dataBSI['matchedAmount'] = $val['matchedAmnt'];
                $dataBSI['documentDate'] = $val['bookingDate'];
                $dataBSI['documentSystemCode'] = $val['bookingInvCode'];
                $dataBSI['companyID'] = $this->common_data['company_data']['company_id'];
                $dataBSI['createdUserGroup'] = $this->common_data['user_group'];
                $dataBSI['createdPCID'] = $this->common_data['current_pc'];
                $dataBSI['createdUserID'] = $this->common_data['current_userID'];
                $dataBSI['createdUserName'] = $this->common_data['current_user'];
                $dataBSI['createdDateTime'] = $this->common_data['current_date'];
                $insertBSI=$this->db->insert('srp_erp_documenttracing', $dataBSI);
                $masterIDbsi=$this->db->insert_id();
                if($insertBSI){
                    $levelNog=$levelNo+1;
                    $bsiDerails=$this->getPVbyBSI($val['InvoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi);
                    $dnDerails=$this->getDNbyBSI($val['InvoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi);
                    $pvmDerails=$this->getPVMbyBSI($val['InvoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi);
                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }

    function getPVbyPR($purchaseRequestID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];

        $pvDetails = $this->db->query('SELECT
	pvd.prMasterID,
	pvd.payVoucherAutoId,
pvm.PVcode,
(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * IFNULL(PRQ.transactionAmount,0)
		) + IFNULL(PRQ.transactionAmount, 0)
	) AS total_value,
pvm.PVdate,
pvm.PVNarration,
SUM(pvd.transactionAmount) as matchedAmnt,
CASE
    WHEN pvm.confirmedYN=0 THEN
    0
    WHEN pvm.approvedYN = 1 THEN
    2
WHEN pvm.confirmedYN=2 AND pvm.approvedYN=0 THEN
    0
Else
    0
    END documentStatus
FROM
	srp_erp_purchaserequestmaster as prm
LEFT JOIN srp_erp_paymentvoucherdetail as pvd ON pvd.prMasterID = prm.purchaseRequestID
LEFT JOIN srp_erp_paymentvouchermaster as pvm ON pvm.payVoucherAutoId = pvd.payVoucherAutoId

LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		payVoucherAutoId
	FROM
		srp_erp_paymentvoucherdetail
	WHERE
		srp_erp_paymentvoucherdetail.type = "PRQ"
	GROUP BY
		payVoucherAutoId
) PRQ ON (
	`PRQ`.`payVoucherAutoId` = pvm.payVoucherAutoId
)

LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		payVoucherAutoId
	FROM
		srp_erp_paymentvouchertaxdetails
	GROUP BY
		payVoucherAutoId
) addondet ON (
	`addondet`.`payVoucherAutoId` = pvm.payVoucherAutoId
)

WHERE
	purchaseRequestID IN ("'.$purchaseRequestID .'")
AND prm.companyID = "'.$compID.'"
AND prMasterID is NOT NULL GROUP BY pvm.payVoucherAutoId')->result_array();

        if(!empty($pvDetails)){
            foreach($pvDetails as $val){
                $dataPV['empID'] = current_userID();
                $dataPV['documentID'] = 'PV';
                $dataPV['documentName'] = 'Payment Voucher';
                $dataPV['documentAutoID'] = $val['payVoucherAutoId'];
                $dataPV['documentNarration'] = $val['PVNarration'];
                $dataPV['relatedType'] = 1;
                $dataPV['relatedDocumentType'] = 'PRQ';
                $dataPV['relatedDocumentAutoID'] = $val['prMasterID'];
                $dataPV['levelNo'] = $levelNo;
                $dataPV['masterID'] = $masterID;
                $dataPV['documentStatus'] = $val['documentStatus'];
                $dataPV['startDocumentID'] = $startDocumentID;
                $dataPV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPV['documentAmount'] = $val['total_value'];
                $dataPV['matchedAmount'] = $val['matchedAmnt'];
                $dataPV['documentDate'] = $val['PVdate'];
                $dataPV['documentSystemCode'] = $val['PVcode'];
                $dataPV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPV['createdUserGroup'] = $this->common_data['user_group'];
                $dataPV['createdPCID'] = $this->common_data['current_pc'];
                $dataPV['createdUserID'] = $this->common_data['current_userID'];
                $dataPV['createdUserName'] = $this->common_data['current_user'];
                $dataPV['createdDateTime'] = $this->common_data['current_date'];
                $insertPO=$this->db->insert('srp_erp_documenttracing', $dataPV);
                $masterIDpo=$this->db->insert_id();
                /*if($insertPO){
                    $grvDerails=$this->getGRVbyPO($val['purchaseOrderID'],2,$startDocumentID,$startDocumentAutoID,$masterIDpo);
                }*/
            }
        }
    }
    function getSLRbyHCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	srm.salesReturnAutoID,
	srd.invoiceAutoID,
srm.returnDate,
srm.salesReturnCode,
srm.`comment`,
SUM(srd.totalValue) as matchedAmnt,
CASE
    WHEN srm.confirmedYN=0 THEN
    0
    WHEN srm.approvedYN = 1 THEN
    2
WHEN srm.confirmedYN=2 AND srm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_salesreturndetails as srd
LEFT JOIN srp_erp_salesreturnmaster as srm ON srd.salesReturnAutoID = srm.salesReturnAutoID


WHERE
	srd.invoiceAutoID IN ("'.$InvoiceAutoID .'")
AND srd.companyID = "'.$compID.'"
AND srd.invoiceAutoID is NOT NULL
GROUP BY srd.salesReturnAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataSLR['empID'] = current_userID();
                $dataSLR['documentID'] = 'SLR';
                $dataSLR['documentName'] = 'Sales Return';
                $dataSLR['documentAutoID'] = $val['salesReturnAutoID'];
                $dataSLR['documentNarration'] = $val['comment'];
                $dataSLR['relatedType'] = 2;
                $dataSLR['relatedDocumentType'] = 'HCINV';
                $dataSLR['relatedDocumentAutoID'] = $val['invoiceAutoID'];
                $dataSLR['levelNo'] = $levelNo;
                $dataSLR['masterID'] = $masterID;
                $dataSLR['documentStatus'] = $val['documentStatus'];
                $dataSLR['startDocumentID'] = $startDocumentID;
                $dataSLR['startDocumentAutoID'] = $startDocumentAutoID;
                $dataSLR['documentAmount'] = $val['matchedAmnt'];
                $dataSLR['matchedAmount'] = $val['matchedAmnt'];
                $dataSLR['documentDate'] = $val['returnDate'];
                $dataSLR['documentSystemCode'] = $val['salesReturnCode'];
                $dataSLR['companyID'] = $this->common_data['company_data']['company_id'];
                $dataSLR['createdUserGroup'] = $this->common_data['user_group'];
                $dataSLR['createdPCID'] = $this->common_data['current_pc'];
                $dataSLR['createdUserID'] = $this->common_data['current_userID'];
                $dataSLR['createdUserName'] = $this->common_data['current_user'];
                $dataSLR['createdDateTime'] = $this->common_data['current_date'];
                $insertSLR=$this->db->insert('srp_erp_documenttracing', $dataSLR);
                if($insertSLR){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }
    function getCNbyHCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	cnm.creditNoteMasterAutoID,
	cnd.invoiceAutoID,
cnm.creditNoteDate,
cnm.creditNoteCode,
cnm.`comments`,
det.transactionAmount AS total_value,
SUM(cnd.transactionAmount) as matchedAmnt,
CASE
    WHEN cnm.confirmedYN=0 THEN
    0
    WHEN cnm.approvedYN = 1 THEN
    2
WHEN cnm.confirmedYN=2 AND cnm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_creditnotedetail as cnd
LEFT JOIN srp_erp_creditnotemaster as cnm ON cnd.creditNoteMasterAutoID = cnm.creditNoteMasterAutoID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
	GROUP BY
		creditNoteMasterAutoID
) det ON (
	`det`.`creditNoteMasterAutoID` = cnm.creditNoteMasterAutoID
)

WHERE
	cnd.invoiceAutoID IN ("'.$InvoiceAutoID .'")
AND cnd.companyID = "'.$compID.'"
AND cnd.invoiceAutoID is NOT NULL
GROUP BY cnd.creditNoteMasterAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCN['empID'] = current_userID();
                $dataCN['documentID'] = 'CN';
                $dataCN['documentName'] = 'Credit Note';
                $dataCN['documentAutoID'] = $val['creditNoteMasterAutoID'];
                $dataCN['documentNarration'] = $val['comments'];
                $dataCN['relatedType'] = 2;
                $dataCN['relatedDocumentType'] = 'HCINV';
                $dataCN['relatedDocumentAutoID'] = $val['invoiceAutoID'];
                $dataCN['levelNo'] = $levelNo;
                $dataCN['masterID'] = $masterID;
                $dataCN['documentStatus'] = $val['documentStatus'];
                $dataCN['startDocumentID'] = $startDocumentID;
                $dataCN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCN['documentAmount'] = $val['total_value'];
                $dataCN['matchedAmount'] = $val['matchedAmnt'];
                $dataCN['documentDate'] = $val['creditNoteDate'];
                $dataCN['documentSystemCode'] = $val['creditNoteCode'];
                $dataCN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCN['createdUserGroup'] = $this->common_data['user_group'];
                $dataCN['createdPCID'] = $this->common_data['current_pc'];
                $dataCN['createdUserID'] = $this->common_data['current_userID'];
                $dataCN['createdUserName'] = $this->common_data['current_user'];
                $dataCN['createdDateTime'] = $this->common_data['current_date'];
                $insertCN=$this->db->insert('srp_erp_documenttracing', $dataCN);
                if($insertCN){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }
    function getRVbyHCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	rvm.receiptVoucherAutoId,
	rvd.invoiceAutoID,
rvm.RVdate,
rvm.RVcode,
rvm.`RVNarration`,
(
	(
		(
			IFNULL(addondet.taxPercentage, 0) / 100
		) * IFNULL(
			tyepdet.transactionAmount,
			0
		)
	) + IFNULL(det.transactionAmount, 0)
) AS total_value,
SUM(rvd.transactionAmount) as matchedAmnt,
CASE
    WHEN rvm.confirmedYN=0 THEN
    0
    WHEN rvm.approvedYN = 1 THEN
    2
WHEN rvm.confirmedYN=2 AND rvm.approvedYN=0 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_customerreceiptdetail as rvd
LEFT JOIN srp_erp_customerreceiptmaster as rvm ON rvd.receiptVoucherAutoId = rvm.receiptVoucherAutoId
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceiptdetail
	GROUP BY
		receiptVoucherAutoId
) det ON (
	`det`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceipttaxdetails
	GROUP BY
		receiptVoucherAutoId
) addondet ON (
	`addondet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId
)
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceiptdetail
	WHERE
		srp_erp_customerreceiptdetail.type = "GL"
	OR srp_erp_customerreceiptdetail.type = "Item"
	GROUP BY
		receiptVoucherAutoId
) tyepdet ON (
	`tyepdet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId
)

WHERE
	rvd.invoiceAutoID IN ("'.$InvoiceAutoID .'")
AND rvd.companyID = "'.$compID.'"
AND rvd.invoiceAutoID is NOT NULL
GROUP BY rvd.receiptVoucherAutoId')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCN['empID'] = current_userID();
                $dataCN['documentID'] = 'RV';
                $dataCN['documentName'] = 'Receipt Voucher';
                $dataCN['documentAutoID'] = $val['receiptVoucherAutoId'];
                $dataCN['documentNarration'] = $val['RVNarration'];
                $dataCN['relatedType'] = 2;
                $dataCN['relatedDocumentType'] = 'HCINV';
                $dataCN['relatedDocumentAutoID'] = $val['invoiceAutoID'];
                $dataCN['levelNo'] = $levelNo;
                $dataCN['masterID'] = $masterID;
                $dataCN['documentStatus'] = $val['documentStatus'];
                $dataCN['startDocumentID'] = $startDocumentID;
                $dataCN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCN['documentAmount'] = $val['total_value'];
                $dataCN['matchedAmount'] = $val['matchedAmnt'];
                $dataCN['documentDate'] = $val['RVdate'];
                $dataCN['documentSystemCode'] = $val['RVcode'];
                $dataCN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCN['createdUserGroup'] = $this->common_data['user_group'];
                $dataCN['createdPCID'] = $this->common_data['current_pc'];
                $dataCN['createdUserID'] = $this->common_data['current_userID'];
                $dataCN['createdUserName'] = $this->common_data['current_user'];
                $dataCN['createdDateTime'] = $this->common_data['current_date'];
                $insertCN=$this->db->insert('srp_erp_documenttracing', $dataCN);
                if($insertCN){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }
    function getRVMbyHCINV($InvoiceAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	rvmm.matchID,
	rvmd.InvoiceAutoID,
rvmm.matchDate,
rvmm.matchSystemCode,
rvmm.`Narration`,
det.transactionAmount AS total_value,
SUM(rvmd.transactionAmount) as matchedAmnt,
CASE
    WHEN rvmm.confirmedYN=0 THEN
    0
    WHEN rvmm.confirmedYN = 1 THEN
    1
WHEN rvmm.confirmedYN=2 THEN
    0
Else
    1
    END documentStatus
FROM
	srp_erp_rvadvancematchdetails as rvmd
LEFT JOIN srp_erp_rvadvancematch as rvmm ON rvmd.matchID = rvmm.matchID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		matchID
	FROM
		srp_erp_rvadvancematchdetails
	GROUP BY
		matchID
) det ON (
	`det`.`matchID` = rvmm.matchID
)

WHERE
	rvmd.InvoiceAutoID IN ("'.$InvoiceAutoID .'")
AND rvmd.companyID = "'.$compID.'"
AND rvmd.InvoiceAutoID is NOT NULL
GROUP BY rvmd.matchID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataRVM['empID'] = current_userID();
                $dataRVM['documentID'] = 'RVM';
                $dataRVM['documentName'] = 'Receipt Voucher Match';
                $dataRVM['documentAutoID'] = $val['matchID'];
                $dataRVM['documentNarration'] = $val['Narration'];
                $dataRVM['relatedType'] = 2;
                $dataRVM['relatedDocumentType'] = 'CINV';
                $dataRVM['relatedDocumentAutoID'] = $val['InvoiceAutoID'];
                $dataRVM['levelNo'] = $levelNo;
                $dataRVM['masterID'] = $masterID;
                $dataRVM['documentStatus'] = $val['documentStatus'];
                $dataRVM['startDocumentID'] = $startDocumentID;
                $dataRVM['startDocumentAutoID'] = $startDocumentAutoID;
                $dataRVM['documentAmount'] = $val['total_value'];
                $dataRVM['matchedAmount'] = $val['matchedAmnt'];
                $dataRVM['documentDate'] = $val['matchDate'];
                $dataRVM['documentSystemCode'] = $val['matchSystemCode'];
                $dataRVM['companyID'] = $this->common_data['company_data']['company_id'];
                $dataRVM['createdUserGroup'] = $this->common_data['user_group'];
                $dataRVM['createdPCID'] = $this->common_data['current_pc'];
                $dataRVM['createdUserID'] = $this->common_data['current_userID'];
                $dataRVM['createdUserName'] = $this->common_data['current_user'];
                $dataRVM['createdDateTime'] = $this->common_data['current_date'];
                $insertRVM=$this->db->insert('srp_erp_documenttracing', $dataRVM);
                if($insertRVM){

                }else{
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }
    function trace_hcinv_document(){
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='Customer Invoice Buyback';

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $invoiceAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('invoiceAutoID,invoiceCode,transactionAmount,invoiceDate,invoiceNarration');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->from('srp_erp_customerinvoicemaster_temp');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['invoiceAutoID'];
        $data['documentNarration'] = $starting['invoiceNarration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['invoiceAutoID'];
        $data['documentDate'] = $starting['invoiceDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['invoiceCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDcinv=$this->db->insert_id();
        if($insertStarting){
            $SRDerails=$this->getSLRbyHCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getCNbyHCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getRVbyHCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getRVMbyHCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);

        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'HCINV');
            $this->db->where('startDocumentAutoID', $starting['invoiceAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function trace_do_document(){
        $DOAutoID = trim($this->input->post('DOAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='Delivery Order';

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $DOAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('DOAutoID,DOCode,deliveredTransactionAmount,DODate,narration');
        $this->db->where('DOAutoID', $DOAutoID);
        $this->db->from('srp_erp_deliveryorder');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['DOAutoID'];
        $data['documentNarration'] = $starting['narration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['DOAutoID'];
        $data['documentDate'] = $starting['DODate'];
        $data['documentAmount'] = $starting['deliveredTransactionAmount'];
        $data['matchedAmount'] = $starting['deliveredTransactionAmount'];
        $data['documentSystemCode'] = $starting['DOCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDdo=$this->db->insert_id();
        if($insertStarting){
            $bsiDerails=$this->getCINVbyDO($starting['DOAutoID'],1,$DocumentID,$starting['DOAutoID'],$masterIDdo);
            $SRDerails=$this->getSLRbyDO($starting['DOAutoID'],1,$DocumentID,$starting['DOAutoID'],$masterIDdo);
//            $bsiDerails=$this->getCINVbyCNT($starting['contractAutoID'],1,$DocumentID,$starting['contractAutoID'],$masterIDcnt);
//            $DODerails=$this->getDObyCNT($starting['contractAutoID'],1,$DocumentID,$starting['contractAutoID'],$masterIDcnt);

        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', $DocumentID);
            $this->db->where('startDocumentAutoID', $DOAutoID);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getPVbyPO($purchaseOrderID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID){
        $compID=$this->common_data['company_data']['company_id'];
        $pvDetails = $this->db->query('SELECT
	srp_erp_paymentvoucherdetail.payVoucherAutoId,
	purchaseOrderID,
	PVcode,
	SUM( srp_erp_paymentvoucherdetail.transactionAmount ) AS matchedAmnt,
	PVdate,
	PVNarration,
	(((IFNULL(addondet.taxPercentage, 0)/ 100)* IFNULL( tyepdet.transactionAmount, 0 ))+ IFNULL(det.transactionAmount, 0)- IFNULL(debitnote.transactionAmount, 0)- IFNULL(SR.transactionAmount, 0)) AS transactionAmount,
CASE
		
		WHEN confirmedYN = 0 THEN
		0 
		WHEN approvedYN = 1 THEN
		2 
		WHEN confirmedYN = 2 
		AND approvedYN = 0 THEN
			0 ELSE 0 
		END documentStatus 
FROM
	srp_erp_paymentvoucherdetail
	LEFT JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type != "debitnote" AND srp_erp_paymentvoucherdetail.type != "SR" GROUP BY payVoucherAutoId ) det ON ( `det`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = "SR" GROUP BY payVoucherAutoId ) SR ON ( `SR`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = "debitnote" GROUP BY payVoucherAutoId ) debitnote ON ( `debitnote`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON ( `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN (
	SELECT
		SUM( transactionAmount ) AS transactionAmount,
		payVoucherAutoId 
	FROM
		srp_erp_paymentvoucherdetail 
	WHERE	srp_erp_paymentvoucherdetail.type = "GL" 
		OR srp_erp_paymentvoucherdetail.type = "Item" 
		OR srp_erp_paymentvoucherdetail.type = "PRQ" 
	GROUP BY
		payVoucherAutoId 
	) tyepdet ON ( `tyepdet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
WHERE
	purchaseOrderID IS NOT NULL 
AND purchaseOrderID IN ("'. $purchaseOrderID .'")
AND srp_erp_paymentvoucherdetail.companyID = "'. $compID .'"
GROUP BY srp_erp_paymentvouchermaster.payVoucherAutoId')->result_array();

        if(!empty($pvDetails)){
            foreach($pvDetails as $val){
                $levelNog=0;
                $dataPV['empID'] = current_userID();
                $dataPV['documentID'] = 'PV';
                $dataPV['documentName'] = 'Payment Voucher';
                $dataPV['documentAutoID'] = $val['payVoucherAutoId'];
                $dataPV['documentNarration'] = $val['PVNarration'];
                $dataPV['relatedType'] = 2;
                $dataPV['relatedDocumentType'] = 'PO';
                $dataPV['relatedDocumentAutoID'] = $val['purchaseOrderID'];
                $dataPV['levelNo'] = $levelNo;
                $dataPV['masterID'] = $masterID;
                $dataPV['documentStatus'] = $val['documentStatus'];
                $dataPV['startDocumentID'] = $startDocumentID;
                $dataPV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPV['documentAmount'] = $val['transactionAmount'];
                $dataPV['matchedAmount'] = $val['matchedAmnt'];
                $dataPV['documentDate'] = $val['PVdate'];
                $dataPV['documentSystemCode'] = $val['PVcode'];
                $dataPV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPV['createdUserGroup'] = $this->common_data['user_group'];
                $dataPV['createdPCID'] = $this->common_data['current_pc'];
                $dataPV['createdUserID'] = $this->common_data['current_userID'];
                $dataPV['createdUserName'] = $this->common_data['current_user'];
                $dataPV['createdDateTime'] = $this->common_data['current_date'];
                $insertPV=$this->db->insert('srp_erp_documenttracing', $dataPV);
            }
        }
    }

    function trace_slr_document()
    {
        $salesReturnAutoID = trim($this->input->post('salesReturnAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'SLR');
        $this->db->where('startDocumentAutoID', $salesReturnAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('srp_erp_salesreturnmaster.salesReturnAutoID AS salesReturnAutoID, salesReturnCode, returnDate AS documentDate, det.transactionAmount AS transactionAmount, comment');
        $this->db->where('srp_erp_salesreturnmaster.salesReturnAutoID', $salesReturnAutoID);
        $this->db->from('srp_erp_salesreturnmaster');
        $this->db->join('(SELECT SUM( totalValue ) AS transactionAmount, salesReturnAutoID FROM srp_erp_salesreturndetails GROUP BY salesReturnAutoID ) det', 'det.salesReturnAutoID = srp_erp_salesreturnmaster.salesReturnAutoID', 'LEFT');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'SLR';
        $data['documentName'] = 'Sales Return';
        $data['documentAutoID'] = $starting['salesReturnAutoID'];
        $data['documentNarration'] = $starting['comment'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'SLR';
        $data['startDocumentAutoID'] = $starting['salesReturnAutoID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['salesReturnCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDpo=$this->db->insert_id();
        if($insertStarting){
            $pvDerails=$this->getRVbySLR($starting['salesReturnAutoID'],1,'SLR',$starting['salesReturnAutoID'],$masterIDpo);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'SLR');
            $this->db->where('startDocumentAutoID', $starting['salesReturnAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getRVbySLR($salesReturnAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	rvm.receiptVoucherAutoId,
	rvd.creditNoteAutoID,
	rvm.RVdate,
	rvm.RVcode,
	rvm.`RVNarration`,
	(
		( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) 
	) AS total_value,
	SUM( rvd.transactionAmount ) AS matchedAmnt,
CASE
		
		WHEN rvm.confirmedYN = 0 THEN
		0 
		WHEN rvm.approvedYN = 1 THEN
		2 
		WHEN rvm.confirmedYN = 2 
		AND rvm.approvedYN = 0 THEN
			0 ELSE 1 
		END documentStatus 
FROM
	srp_erp_customerreceiptdetail AS rvd
	LEFT JOIN srp_erp_customerreceiptmaster AS rvm ON rvd.receiptVoucherAutoId = rvm.receiptVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail GROUP BY receiptVoucherAutoId ) det ON ( `det`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = "GL" OR srp_erp_customerreceiptdetail.type = "Item" GROUP BY receiptVoucherAutoId ) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId ) 
WHERE
    rvd.type = "SLR"
	AND rvd.creditNoteAutoID IN ("'.$salesReturnAutoID .'")
	AND rvd.companyID = "'.$compID.'"
	AND rvd.creditNoteAutoID IS NOT NULL 
GROUP BY
	rvd.receiptVoucherAutoId')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCN['empID'] = current_userID();
                $dataCN['documentID'] = 'RV';
                $dataCN['documentName'] = 'Receipt Voucher';
                $dataCN['documentAutoID'] = $val['receiptVoucherAutoId'];
                $dataCN['documentNarration'] = $val['RVNarration'];
                $dataCN['relatedType'] = 2;
                $dataCN['relatedDocumentType'] = 'SLR';
                $dataCN['relatedDocumentAutoID'] = $val['creditNoteAutoID'];
                $dataCN['levelNo'] = $levelNo;
                $dataCN['masterID'] = $masterID;
                $dataCN['documentStatus'] = $val['documentStatus'];
                $dataCN['startDocumentID'] = $startDocumentID;
                $dataCN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCN['documentAmount'] = $val['total_value'];
                $dataCN['matchedAmount'] = $val['matchedAmnt'];
                $dataCN['documentDate'] = $val['RVdate'];
                $dataCN['documentSystemCode'] = $val['RVcode'];
                $dataCN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCN['createdUserGroup'] = $this->common_data['user_group'];
                $dataCN['createdPCID'] = $this->common_data['current_pc'];
                $dataCN['createdUserID'] = $this->common_data['current_userID'];
                $dataCN['createdUserName'] = $this->common_data['current_user'];
                $dataCN['createdDateTime'] = $this->common_data['current_date'];
                $insertCN=$this->db->insert('srp_erp_documenttracing', $dataCN);
            }
        }
    }

    function trace_cn_document()
    {
        $creditNoteMasterAutoID = trim($this->input->post('creditNoteMasterAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'CN');
        $this->db->where('startDocumentAutoID', $creditNoteMasterAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('srp_erp_creditnotemaster.creditNoteMasterAutoID, creditNoteCode, creditNoteDate AS documentDate, det.transactionAmount AS transactionAmount, comments');
        $this->db->where('srp_erp_creditnotemaster.creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $this->db->from('srp_erp_creditnotemaster');
        $this->db->join('(SELECT SUM( transactionAmount ) AS transactionAmount, creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID) det', 'det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID', 'LEFT');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'CN';
        $data['documentName'] = 'Credit Note';
        $data['documentAutoID'] = $starting['creditNoteMasterAutoID'];
        $data['documentNarration'] = $starting['comments'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'CN';
        $data['startDocumentAutoID'] = $starting['creditNoteMasterAutoID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['creditNoteCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDpo=$this->db->insert_id();
        if($insertStarting){
            $pvDerails=$this->getRVbyCN($starting['creditNoteMasterAutoID'],1,'CN',$starting['creditNoteMasterAutoID'],$masterIDpo);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'CN');
            $this->db->where('startDocumentAutoID', $starting['salesReturnAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getRVbyCN($creditNoteMasterAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
	rvm.receiptVoucherAutoId,
	rvd.creditNoteAutoID,
	rvm.RVdate,
	rvm.RVcode,
	rvm.`RVNarration`,
	(
		( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) 
	) AS total_value,
	SUM( rvd.transactionAmount ) AS matchedAmnt,
CASE
		
		WHEN rvm.confirmedYN = 0 THEN
		0 
		WHEN rvm.approvedYN = 1 THEN
		2 
		WHEN rvm.confirmedYN = 2 
		AND rvm.approvedYN = 0 THEN
			0 ELSE 1 
		END documentStatus 
FROM
	srp_erp_customerreceiptdetail AS rvd
	LEFT JOIN srp_erp_customerreceiptmaster AS rvm ON rvd.receiptVoucherAutoId = rvm.receiptVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail GROUP BY receiptVoucherAutoId ) det ON ( `det`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = "GL" OR srp_erp_customerreceiptdetail.type = "Item" GROUP BY receiptVoucherAutoId ) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = rvm.receiptVoucherAutoId ) 
WHERE
    rvd.type = "creditnote"
	AND rvd.creditNoteAutoID IN ("'.$creditNoteMasterAutoID .'")
	AND rvd.companyID = "'.$compID.'"
	AND rvd.creditNoteAutoID IS NOT NULL 
GROUP BY
	rvd.receiptVoucherAutoId')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCN['empID'] = current_userID();
                $dataCN['documentID'] = 'RV';
                $dataCN['documentName'] = 'Receipt Voucher';
                $dataCN['documentAutoID'] = $val['receiptVoucherAutoId'];
                $dataCN['documentNarration'] = $val['RVNarration'];
                $dataCN['relatedType'] = 2;
                $dataCN['relatedDocumentType'] = 'CN';
                $dataCN['relatedDocumentAutoID'] = $val['creditNoteAutoID'];
                $dataCN['levelNo'] = $levelNo;
                $dataCN['masterID'] = $masterID;
                $dataCN['documentStatus'] = $val['documentStatus'];
                $dataCN['startDocumentID'] = $startDocumentID;
                $dataCN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCN['documentAmount'] = $val['total_value'];
                $dataCN['matchedAmount'] = $val['matchedAmnt'];
                $dataCN['documentDate'] = $val['RVdate'];
                $dataCN['documentSystemCode'] = $val['RVcode'];
                $dataCN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCN['createdUserGroup'] = $this->common_data['user_group'];
                $dataCN['createdPCID'] = $this->common_data['current_pc'];
                $dataCN['createdUserID'] = $this->common_data['current_userID'];
                $dataCN['createdUserName'] = $this->common_data['current_user'];
                $dataCN['createdDateTime'] = $this->common_data['current_date'];
                $insertCN=$this->db->insert('srp_erp_documenttracing', $dataCN);
            }
        }
    }

    function trace_dn_document()
    {
        $debitNoteMasterAutoID = trim($this->input->post('debitNoteMasterAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'DN');
        $this->db->where('startDocumentAutoID', $debitNoteMasterAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('srp_erp_debitnotemaster.debitNoteMasterAutoID, debitNoteCode, debitNoteDate AS documentDate, det.transactionAmount AS transactionAmount, comments');
        $this->db->where('srp_erp_debitnotemaster.debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->from('srp_erp_debitnotemaster');
        $this->db->join('(SELECT SUM( transactionAmount ) AS transactionAmount, debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', 'det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID', 'LEFT');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'DN';
        $data['documentName'] = 'Debit Note';
        $data['documentAutoID'] = $starting['debitNoteMasterAutoID'];
        $data['documentNarration'] = $starting['comments'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'DN';
        $data['startDocumentAutoID'] = $starting['debitNoteMasterAutoID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['debitNoteCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
//echo'<pre>';print_r($data); exit();
        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDpo=$this->db->insert_id();
        if($insertStarting){
            $pvDerails=$this->getPVbyDN($starting['debitNoteMasterAutoID'],1,'DN',$starting['debitNoteMasterAutoID'],$masterIDpo);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'DN');
            $this->db->where('startDocumentAutoID', $starting['debitNoteMasterAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function getPVbyDN($debitNoteMasterAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $pvDetails = $this->db->query('SELECT
	srp_erp_paymentvoucherdetail.payVoucherAutoId,
	debitNoteAutoID,
	PVcode,
	SUM( srp_erp_paymentvoucherdetail.transactionAmount ) AS matchedAmnt,
	PVdate,
	PVNarration,
	(((
				IFNULL( addondet.taxPercentage, 0 )/ 100 
			)* IFNULL( tyepdet.transactionAmount, 0 ))+ IFNULL( det.transactionAmount, 0 )- IFNULL( debitnote.transactionAmount, 0 )- IFNULL( SR.transactionAmount, 0 )) AS transactionAmount,
CASE
		
		WHEN confirmedYN = 0 THEN
		0 
		WHEN approvedYN = 1 THEN
		2 
		WHEN confirmedYN = 2 
		AND approvedYN = 0 THEN
			0 ELSE 0 
		END documentStatus 
FROM
	srp_erp_paymentvoucherdetail
	LEFT JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type != "debitnote" AND srp_erp_paymentvoucherdetail.type != "SR" GROUP BY payVoucherAutoId ) det ON ( `det`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = "SR" GROUP BY payVoucherAutoId ) SR ON ( `SR`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = "debitnote" GROUP BY payVoucherAutoId ) debitnote ON ( `debitnote`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON ( `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
	LEFT JOIN (
	SELECT
		SUM( transactionAmount ) AS transactionAmount,
		payVoucherAutoId 
	FROM
		srp_erp_paymentvoucherdetail 
	WHERE
		srp_erp_paymentvoucherdetail.type = "GL" 
		OR srp_erp_paymentvoucherdetail.type = "Item" 
		OR srp_erp_paymentvoucherdetail.type = "PRQ" 
	GROUP BY
		payVoucherAutoId 
	) tyepdet ON ( `tyepdet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId ) 
WHERE
	type = "debitnote" 
	AND debitNoteAutoID IS NOT NULL 
	AND debitNoteAutoID IN ("'. $debitNoteMasterAutoID .'")
	AND srp_erp_paymentvoucherdetail.companyID = "'. $compID .'"
GROUP BY
	srp_erp_paymentvouchermaster.payVoucherAutoId')->result_array();

        if(!empty($pvDetails)){
            foreach($pvDetails as $val){
                $levelNog=0;
                $dataPV['empID'] = current_userID();
                $dataPV['documentID'] = 'PV';
                $dataPV['documentName'] = 'Payment Voucher';
                $dataPV['documentAutoID'] = $val['payVoucherAutoId'];
                $dataPV['documentNarration'] = $val['PVNarration'];
                $dataPV['relatedType'] = 2;
                $dataPV['relatedDocumentType'] = 'DN';
                $dataPV['relatedDocumentAutoID'] = $val['debitNoteAutoID'];
                $dataPV['levelNo'] = $levelNo;
                $dataPV['masterID'] = $masterID;
                $dataPV['documentStatus'] = $val['documentStatus'];
                $dataPV['startDocumentID'] = $startDocumentID;
                $dataPV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataPV['documentAmount'] = $val['transactionAmount'];
                $dataPV['matchedAmount'] = $val['matchedAmnt'];
                $dataPV['documentDate'] = $val['PVdate'];
                $dataPV['documentSystemCode'] = $val['PVcode'];
                $dataPV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataPV['createdUserGroup'] = $this->common_data['user_group'];
                $dataPV['createdPCID'] = $this->common_data['current_pc'];
                $dataPV['createdUserID'] = $this->common_data['current_userID'];
                $dataPV['createdUserName'] = $this->common_data['current_user'];
                $dataPV['createdDateTime'] = $this->common_data['current_date'];
                $insertPV=$this->db->insert('srp_erp_documenttracing', $dataPV);
            }
        }
    }


    function trace_mr_document()
    {
        $materialRequestID = trim($this->input->post('materialRequestID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'MR');
        $this->db->where('startDocumentAutoID', $materialRequestID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('srp_erp_materialrequest.mrAutoID, MRCode, requestedDate AS documentDate, det.transactionAmount AS transactionAmount, comment');
        $this->db->where('srp_erp_materialrequest.mrAutoID', $materialRequestID);
        $this->db->from('srp_erp_materialrequest');
        $this->db->join('(SELECT SUM( totalValue ) AS transactionAmount, mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID) det', 'det.mrAutoID = srp_erp_materialrequest.mrAutoID', 'LEFT');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'MR';
        $data['documentName'] = 'Material Request';
        $data['documentAutoID'] = $starting['mrAutoID'];
        $data['documentNarration'] = $starting['comment'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'MR';
        $data['startDocumentAutoID'] = $starting['mrAutoID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['MRCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
//echo'<pre>';print_r($data); exit();
        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDmr=$this->db->insert_id();
        if($insertStarting){
            $miDerails=$this->getMIbyMR($starting['mrAutoID'],1,'MR',$starting['mrAutoID'],$masterIDmr);

        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'MR');
            $this->db->where('startDocumentAutoID', $starting['mrAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }

    }


    function getMIbyMR($MaterialRequestMasterAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $miDetails = $this->db->query('SELECT
	srp_erp_itemissuedetails.itemIssueAutoID,
	mrAutoID,
	srp_erp_itemissuemaster.itemIssueCode,
	SUM( srp_erp_itemissuedetails.totalValue ) AS matchedAmnt,
	issueDate AS documentDate,
	comment,
	det.transactionAmount AS transactionAmount,
CASE
	    WHEN confirmedYN = 0 THEN
		0 
		WHEN approvedYN = 1 THEN
		2 
		WHEN confirmedYN = 2 
		AND approvedYN = 0 THEN
			0 ELSE 0 
		END documentStatus 
FROM
	srp_erp_itemissuedetails
	LEFT JOIN srp_erp_itemissuemaster ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID
	LEFT JOIN ( SELECT SUM( totalValue ) AS transactionAmount, itemIssueAutoID  FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID ) det ON ( `det`.`itemIssueAutoID` = srp_erp_itemissuemaster.itemIssueAutoID )
WHERE
	mrAutoID IS NOT NULL 
	AND mrAutoID IN ( "'.$MaterialRequestMasterAutoID.'" ) 
	AND srp_erp_itemissuedetails.companyID = "'.$compID.'" 
GROUP BY
	srp_erp_itemissuedetails.itemIssueAutoID')->result_array();

        if(!empty($miDetails)){
            foreach($miDetails as $val){
                $levelNog=0;
                $dataMI['empID'] = current_userID();
                $dataMI['documentID'] = 'MI';
                $dataMI['documentName'] = 'Material Issue';
                $dataMI['documentAutoID'] = $val['itemIssueAutoID'];
                $dataMI['documentNarration'] = $val['comment'];
                $dataMI['relatedType'] = 2;
                $dataMI['relatedDocumentType'] = 'MR';
                $dataMI['relatedDocumentAutoID'] = $val['mrAutoID'];
                $dataMI['levelNo'] = $levelNo;
                $dataMI['masterID'] = $masterID;
                $dataMI['documentStatus'] = $val['documentStatus'];
                $dataMI['startDocumentID'] = $startDocumentID;
                $dataMI['startDocumentAutoID'] = $startDocumentAutoID;
                $dataMI['documentAmount'] = $val['transactionAmount'];
                $dataMI['matchedAmount'] = $val['matchedAmnt'];
                $dataMI['documentDate'] = $val['documentDate'];
                $dataMI['documentSystemCode'] = $val['itemIssueCode'];
                $dataMI['companyID'] = $this->common_data['company_data']['company_id'];
                $dataMI['createdUserGroup'] = $this->common_data['user_group'];
                $dataMI['createdPCID'] = $this->common_data['current_pc'];
                $dataMI['createdUserID'] = $this->common_data['current_userID'];
                $dataMI['createdUserName'] = $this->common_data['current_user'];
                $dataMI['createdDateTime'] = $this->common_data['current_date'];
                $insertMI=$this->db->insert('srp_erp_documenttracing', $dataMI);
                $masterIDMI=$this->db->insert_id();
                if($insertMI){
                    $levelNog=$levelNo+1;
                    $mrnDerails=$this->getMRNbyMI($val['itemIssueAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDMI);
                }else {
                    /*$this->db->where('empID', current_userID());
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('startDocumentID', $startDocumentID);
                    $this->db->where('startDocumentAutoID', $startDocumentAutoID);
                    $this->db->delete('srp_erp_documenttracing');
                    exit;*/
                }
            }
        }
    }


    function trace_mi_document()
    {
        $materialIssueID = trim($this->input->post('materialIssueID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', 'MI');
        $this->db->where('startDocumentAutoID', $materialIssueID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('srp_erp_itemissuemaster.itemIssueAutoID,itemIssueCode,	issueDate AS documentDate,det.transactionAmount AS transactionAmount,comment');
        $this->db->where('srp_erp_itemissuemaster.itemIssueAutoID', $materialIssueID);
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->join('(SELECT SUM( totalValue ) AS transactionAmount, itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', 'det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID', 'LEFT');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = 'MI';
        $data['documentName'] = 'Material Issue';
        $data['documentAutoID'] = $starting['itemIssueAutoID'];
        $data['documentNarration'] = $starting['comment'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = 'MI';
        $data['startDocumentAutoID'] = $starting['itemIssueAutoID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['itemIssueCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
//echo'<pre>';print_r($data); exit();
        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDmi=$this->db->insert_id();
        if($insertStarting){
            $miDerails=$this->getMRNbyMI($starting['itemIssueAutoID'],1,'MI',$starting['itemIssueAutoID'],$masterIDmi);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'MI');
            $this->db->where('startDocumentAutoID', $starting['itemIssueAutoID']);
            $this->db->delete('srp_erp_documenttracing');
        }

    }


    function getMRNbyMI($MaterialIssueMasterAutoID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $mrnDetails = $this->db->query('SELECT
	srp_erp_materialreceiptdetails.mrnAutoID,
	itemIssueAutoID,
    mrnCode,
	SUM( srp_erp_materialreceiptdetails.totalValue ) AS matchedAmnt,
	receivedDate AS documentDate,
	comment,
	det.transactionAmount AS transactionAmount,
	CASE
		WHEN confirmedYN = 0 THEN
		0 
		WHEN approvedYN = 1 THEN
		2 
		WHEN confirmedYN = 2 
		AND approvedYN = 0 THEN
			0 ELSE 0 
		END documentStatus 
FROM
	srp_erp_materialreceiptdetails
	LEFT JOIN srp_erp_materialreceiptmaster ON srp_erp_materialreceiptmaster.mrnAutoID = srp_erp_materialreceiptdetails.mrnAutoID
	
	LEFT JOIN ( SELECT SUM( totalValue ) AS transactionAmount, mrnAutoID  FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID ) det ON ( `det`.`mrnAutoID` = srp_erp_materialreceiptmaster.mrnAutoID )
WHERE
	itemIssueAutoID IS NOT NULL 
	AND itemIssueAutoID IN ( "'.$MaterialIssueMasterAutoID.'" ) 
	AND srp_erp_materialreceiptdetails.companyID = "'.$compID.'" 
GROUP BY
	srp_erp_materialreceiptdetails.mrnAutoID')->result_array();

        if(!empty($mrnDetails)){
            foreach($mrnDetails as $val){
                $levelNog=0;
                $dataMRN['empID'] = current_userID();
                $dataMRN['documentID'] = 'MRN';
                $dataMRN['documentName'] = 'Material Receipt Note';
                $dataMRN['documentAutoID'] = $val['mrnAutoID'];
                $dataMRN['documentNarration'] = $val['comment'];
                $dataMRN['relatedType'] = 2;
                $dataMRN['relatedDocumentType'] = 'MI';
                $dataMRN['relatedDocumentAutoID'] = $val['itemIssueAutoID'];
                $dataMRN['levelNo'] = $levelNo;
                $dataMRN['masterID'] = $masterID;
                $dataMRN['documentStatus'] = $val['documentStatus'];
                $dataMRN['startDocumentID'] = $startDocumentID;
                $dataMRN['startDocumentAutoID'] = $startDocumentAutoID;
                $dataMRN['documentAmount'] = $val['transactionAmount'];
                $dataMRN['matchedAmount'] = $val['matchedAmnt'];
                $dataMRN['documentDate'] = $val['documentDate'];
                $dataMRN['documentSystemCode'] = $val['mrnCode'];
                $dataMRN['companyID'] = $this->common_data['company_data']['company_id'];
                $dataMRN['createdUserGroup'] = $this->common_data['user_group'];
                $dataMRN['createdPCID'] = $this->common_data['current_pc'];
                $dataMRN['createdUserID'] = $this->common_data['current_userID'];
                $dataMRN['createdUserName'] = $this->common_data['current_user'];
                $dataMRN['createdDateTime'] = $this->common_data['current_date'];
                $insertMRN = $this->db->insert('srp_erp_documenttracing', $dataMRN);
            }
        }
    }
    function trace_rv_document(){
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='Receipt Voucher';

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $invoiceAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $this->db->select('receiptVoucherAutoId,RVcode,transactionAmount,RVdate,RVNarration');
        $this->db->where('receiptVoucherAutoId', $invoiceAutoID);
        $this->db->from('srp_erp_customerreceiptmaster');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['receiptVoucherAutoId'];
        $data['documentNarration'] = $starting['RVNarration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['receiptVoucherAutoId'];
        $data['documentDate'] = $starting['RVdate'];
        $data['documentAmount'] = $starting['transactionAmount'];
        $data['matchedAmount'] = $starting['transactionAmount'];
        $data['documentSystemCode'] = $starting['RVcode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDRV=$this->db->insert_id();
        if($insertStarting){
           /* $SRDerails=$this->getSLRbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getCNbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getRVbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);
            $CNDerails=$this->getRVMbyCINV($starting['invoiceAutoID'],1,$DocumentID,$starting['invoiceAutoID'],$masterIDcinv);*/
            $CINVDetails=$this->getCINVbyrv($starting['receiptVoucherAutoId'],1,$DocumentID,$starting['receiptVoucherAutoId'],$masterIDRV);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'RV');
            $this->db->where('startDocumentAutoID', $starting['receiptVoucherAutoId']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }
    function getCINVbyrv($receiptvoucherID,$levelNo,$startDocumentID,$startDocumentAutoID,$masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $rvmDetails = $this->db->query('SELECT
                        srp_erp_rvadvancematch.matchID,
                        srp_erp_rvadvancematchdetails.receiptVoucherAutoId,
                        srp_erp_rvadvancematch.matchSystemCode,
                        SUM(srp_erp_rvadvancematchdetails.transactionAmount) as matchedamount,
                        RVdate as documentcode,
                        Narration,
                        SUM(srp_erp_rvadvancematchdetails.transactionAmount) as transactionAmount,
                        
                        CASE 
                        when confirmedYN = 0 THEN 
                        0		
                        when confirmedYN = 1 THEN 
                        2
                        when confirmedYN = 2   THEN
                        0 
                        ELSE
                        0 
                        END documentstatus,
                        RVdate as docdate

                        
                    FROM
                        `srp_erp_rvadvancematchdetails`
                        LEFT JOIN srp_erp_rvadvancematch on srp_erp_rvadvancematch.matchID = srp_erp_rvadvancematchdetails.matchID
                        LEFT JOIN (SELECT SUM(IFNULL(transactionAmount,0) ) as Invoice_amount,receiptVoucherAutoId FROM srp_erp_customerreceiptdetail GROUP BY receiptVoucherAutoId) rvdet on rvdet.receiptVoucherAutoId = srp_erp_rvadvancematchdetails.receiptVoucherAutoId
                        where 
                        srp_erp_rvadvancematchdetails.companyID = '.$compID.'
                        AND srp_erp_rvadvancematchdetails.receiptVoucherAutoId = '.$receiptvoucherID.'
                        GROUP BY 
                        srp_erp_rvadvancematchdetails.matchID')->result_array();

        if(!empty($rvmDetails)){
            foreach($rvmDetails as $val){
                $levelNog=0;
                $dataRVM['empID'] = current_userID();
                $dataRVM['documentID'] = 'RVM';
                $dataRVM['documentName'] = 'Receipt Matching';
                $dataRVM['documentAutoID'] = $val['matchID'];
                $dataRVM['documentNarration'] = $val['Narration'];
                $dataRVM['relatedType'] = 2;
                $dataRVM['relatedDocumentType'] = 'RV';
                $dataRVM['relatedDocumentAutoID'] = $val['receiptVoucherAutoId'];
                $dataRVM['levelNo'] = $levelNo;
                $dataRVM['masterID'] = $masterID;
                $dataRVM['documentStatus'] = $val['documentstatus'];
                $dataRVM['startDocumentID'] = $startDocumentID;
                $dataRVM['startDocumentAutoID'] = $startDocumentAutoID;
                $dataRVM['documentAmount'] = $val['transactionAmount'];
                $dataRVM['matchedAmount'] = $val['matchedamount'];
                $dataRVM['documentDate'] = $val['docdate'];
                $dataRVM['documentSystemCode'] = $val['matchSystemCode'];
                $dataRVM['companyID'] = $this->common_data['company_data']['company_id'];
                $dataRVM['createdUserGroup'] = $this->common_data['user_group'];
                $dataRVM['createdPCID'] = $this->common_data['current_pc'];
                $dataRVM['createdUserID'] = $this->common_data['current_userID'];
                $dataRVM['createdUserName'] = $this->common_data['current_user'];
                $dataRVM['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documenttracing', $dataRVM);
            }
        }
    }

    function trace_job_document()
    {
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='JOB';

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $workProcessID);
        $this->db->delete('srp_erp_documenttracing');

        $this->load->model('MFQ_Dashboard_modal');
        $qry = $this->MFQ_Dashboard_modal->job_entry_query();
        if(!empty($qry)) {
            $query = " (SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate";
        }

        $this->db->select("srp_erp_mfq_job.workProcessID as workProcessID, '' as narration, documentDate, documentCode, ROUND(SUM(IFNULL(wipAmount,0)), transactionCurrencyDecimalPlaces) AS amount");
        if(!empty($query)) {
            $this->db->join($query,'wipCalculate.workProcessID = srp_erp_mfq_job.workProcessID','left');
        }
        $this->db->where('srp_erp_mfq_job.workProcessID', $workProcessID);
        $this->db->from('srp_erp_mfq_job');
        $starting = $this->db->get()->row_array();

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['workProcessID'];
        $data['documentNarration'] = $starting['narration'];
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 2;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['workProcessID'];
        $data['documentDate'] = $starting['documentDate'];
        $data['documentAmount'] = $starting['amount'];
        $data['matchedAmount'] = $starting['amount'];
        $data['documentSystemCode'] = $starting['documentCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDRV=$this->db->insert_id();
        if($insertStarting){
            $MDNDetails = $this->get_MDN_by_JOB($starting['workProcessID'],1,$DocumentID,$starting['workProcessID'],$masterIDRV);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'JOB');
            $this->db->where('startDocumentAutoID', $starting['workProcessID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function trace_MDN_document()
    {
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='MDN';

        $starting = $this->db->query("SELECT deliveryNoteID, deliveryDate, deliveryNoteCode, IF(srp_erp_mfq_deliverynote.confirmedYN = 1, 1,0) as docStatus 
        FROM srp_erp_mfq_deliverynote
        JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID
        WHERE srp_erp_mfq_deliverynotedetail.deliveryNoteID = $deliverNoteID")->row_array();

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $deliverNoteID);
        $this->db->delete('srp_erp_documenttracing');

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['deliveryNoteID'];
        $data['documentNarration'] = '';
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 1;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['deliveryNoteID'];
        $data['documentDate'] = $starting['deliveryDate'];
        $data['documentAmount'] = 0;
        $data['matchedAmount'] = 0;
        $data['documentSystemCode'] = $starting['deliveryNoteCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDRV=$this->db->insert_id();
        if($insertStarting){
            $MCINVDerails = $this->get_MCINV_by_MDN($starting['deliveryNoteID'],1,$DocumentID,$starting['deliveryNoteID'],$masterIDRV);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'MDN');
            $this->db->where('startDocumentAutoID', $starting['deliveryNoteID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }

    function get_MDN_by_JOB($workProcessID, $levelNo, $startDocumentID, $startDocumentAutoID, $masterID) 
    {   
        $MDNDetails = $this->db->query("SELECT deliveryNoteID, deliveryDate, deliveryNoteCode, IF(srp_erp_mfq_deliverynote.confirmedYN = 1, 1,0) as docStatus 
                        FROM srp_erp_mfq_deliverynote
                        JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID
                        WHERE srp_erp_mfq_deliverynotedetail.jobID = $workProcessID")->result_array();

        if(!empty($MDNDetails)){
            foreach($MDNDetails as $val){
                $levelNog=0;
                $dataBSI['empID'] = current_userID();
                $dataBSI['documentID'] = 'MDN';
                $dataBSI['documentName'] = 'Delivery Note';
                $dataBSI['documentAutoID'] = $val['deliveryNoteID'];
                $dataBSI['documentNarration'] = '';
                $dataBSI['relatedType'] = 2;
                $dataBSI['relatedDocumentType'] = 'PO';
                $dataBSI['relatedDocumentAutoID'] = $workProcessID;
                $dataBSI['levelNo'] = $levelNo;
                $dataBSI['masterID'] = $masterID;
                $dataBSI['documentStatus'] = $val['docStatus'];
                $dataBSI['startDocumentID'] = $startDocumentID;
                $dataBSI['startDocumentAutoID'] = $startDocumentAutoID;
                $dataBSI['documentAmount'] = 0;
                $dataBSI['matchedAmount'] = 0;
                $dataBSI['documentDate'] = $val['deliveryDate'];
                $dataBSI['documentSystemCode'] = $val['deliveryNoteCode'];
                $dataBSI['companyID'] = $this->common_data['company_data']['company_id'];
                $dataBSI['createdUserGroup'] = $this->common_data['user_group'];
                $dataBSI['createdPCID'] = $this->common_data['current_pc'];
                $dataBSI['createdUserID'] = $this->common_data['current_userID'];
                $dataBSI['createdUserName'] = $this->common_data['current_user'];
                $dataBSI['createdDateTime'] = $this->common_data['current_date'];
                $insertBSI=$this->db->insert('srp_erp_documenttracing', $dataBSI);
                $masterIDbsi=$this->db->insert_id();
                if($insertBSI){
                    $levelNog=$levelNo+1;
                    $MCINVDerails = $this->get_MCINV_by_MDN($val['deliveryNoteID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi);
                }
            }
        }
    }

    function get_MCINV_by_MDN($deliveryNoteID, $levelNo, $startDocumentID, $startDocumentAutoID, $masterID) 
    {
        $MCINVDerails = $this->db->query("SELECT
                        srp_erp_mfq_customerinvoicemaster.invoiceAutoID, invoiceDate, invoiceCode, 
                        IF(confirmedYN = 1, 1, 0) AS docStatus, invoiceAmount, IFNULL(DNAmount, 0) AS deliveredAmount 
                    FROM
                        srp_erp_mfq_customerinvoicemaster
                        JOIN ( SELECT SUM( transactionAmount ) AS invoiceAmount, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails GROUP BY invoiceAutoID ) total ON total.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS DNAmount, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails WHERE deliveryNoteDetID IS NOT NULL GROUP BY invoiceAutoID ) DN ON DN.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID 
                    WHERE srp_erp_mfq_customerinvoicemaster.deliveryNoteID = $deliveryNoteID")->result_array();

        if(!empty($MCINVDerails)){
            foreach($MCINVDerails as $val){
                $levelNog=0;
                $dataBSI['empID'] = current_userID();
                $dataBSI['documentID'] = 'MCINV';
                $dataBSI['documentName'] = 'Manufacturing Invoice';
                $dataBSI['documentAutoID'] = $val['invoiceAutoID'];
                $dataBSI['documentNarration'] = '';
                $dataBSI['relatedType'] = 2;
                $dataBSI['relatedDocumentType'] = 'MDN';
                $dataBSI['relatedDocumentAutoID'] = $deliveryNoteID;
                $dataBSI['levelNo'] = $levelNo;
                $dataBSI['masterID'] = $masterID;
                $dataBSI['documentStatus'] = $val['docStatus'];
                $dataBSI['startDocumentID'] = $startDocumentID;
                $dataBSI['startDocumentAutoID'] = $startDocumentAutoID;
                $dataBSI['documentAmount'] = $val['invoiceAmount'];
                $dataBSI['matchedAmount'] = $val['deliveredAmount'];
                $dataBSI['documentDate'] = $val['invoiceDate'];
                $dataBSI['documentSystemCode'] = $val['invoiceCode'];
                $dataBSI['companyID'] = $this->common_data['company_data']['company_id'];
                $dataBSI['createdUserGroup'] = $this->common_data['user_group'];
                $dataBSI['createdPCID'] = $this->common_data['current_pc'];
                $dataBSI['createdUserID'] = $this->common_data['current_userID'];
                $dataBSI['createdUserName'] = $this->common_data['current_user'];
                $dataBSI['createdDateTime'] = $this->common_data['current_date'];
                $insertBSI=$this->db->insert('srp_erp_documenttracing', $dataBSI);
                $masterIDbsi=$this->db->insert_id();
                if($insertBSI){
                    $levelNog=$levelNo+1;
                    $CINVDerails = $this->get_CINV_by_MCINV($val['invoiceAutoID'],$levelNog,$startDocumentID,$startDocumentAutoID,$masterIDbsi);
                }
            }
        }
    }

    function get_CINV_by_MCINV($invoiceAutoID, $levelNo, $startDocumentID, $startDocumentAutoID, $masterID)
    {
        $compID=$this->common_data['company_data']['company_id'];
        $invDetails = $this->db->query('SELECT
                            cinvm.invoiceAutoID,
                            cinvd.contractAutoID,
                            cinvm.invoiceDate,
                            cinvm.invoiceCode,
                            cinvm.invoiceNarration,
                            (((IFNULL(addondet.taxPercentage, 0) / 100) * ((IFNULL(det.transactionAmount, 0) - (IFNULL(det.detailtaxamount, 0))))) + IFNULL(det.transactionAmount, 0)) AS total_value,
                        SUM(cinvd.transactionAmount) as matchedAmnt,
                        CASE WHEN cinvm.confirmedYN=0 THEN 0 WHEN cinvm.approvedYN = 1 THEN 2 WHEN cinvm.confirmedYN=2 AND cinvm.approvedYN=0 THEN 0 Else 0 END documentStatus
                    FROM
                        srp_erp_customerinvoicedetails as cinvd
                    LEFT JOIN srp_erp_customerinvoicemaster as cinvm ON cinvd.invoiceAutoID = cinvm.invoiceAutoID
                    LEFT JOIN (
                        SELECT SUM(transactionAmount) AS transactionAmount, sum(totalafterTax) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID
                    ) det ON det.invoiceAutoID = cinvm.invoiceAutoID
                    LEFT JOIN (
                        SELECT SUM(taxPercentage) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails GROUP BY InvoiceAutoID
                    ) addondet ON addondet.InvoiceAutoID = cinvm.InvoiceAutoID
                    WHERE
                        cinvd.mfqinvoiceDetailsAutoID IS NOT NULL
                        AND cinvm.mfqInvoiceAutoID IN ("'.$invoiceAutoID .'")
                        AND cinvd.companyID = "'.$compID.'"
                    GROUP BY cinvd.invoiceAutoID')->result_array();

        if(!empty($invDetails)){
            foreach($invDetails as $val){
                $levelNog=0;
                $dataCINV['empID'] = current_userID();
                $dataCINV['documentID'] = 'CINV';
                $dataCINV['documentName'] = 'Customer Invoice';
                $dataCINV['documentAutoID'] = $val['invoiceAutoID'];
                $dataCINV['documentNarration'] = $val['invoiceNarration'];
                $dataCINV['relatedType'] = 2;
                $dataCINV['relatedDocumentType'] = $startDocumentID;
                $dataCINV['relatedDocumentAutoID'] = $val['contractAutoID'];
                $dataCINV['levelNo'] = $levelNo;
                $dataCINV['masterID'] = $masterID;
                $dataCINV['documentStatus'] = $val['documentStatus'];
                $dataCINV['startDocumentID'] = $startDocumentID;
                $dataCINV['startDocumentAutoID'] = $startDocumentAutoID;
                $dataCINV['documentAmount'] = $val['total_value'];
                $dataCINV['matchedAmount'] = $val['matchedAmnt'];
                $dataCINV['documentDate'] = $val['invoiceDate'];
                $dataCINV['documentSystemCode'] = $val['invoiceCode'];
                $dataCINV['companyID'] = $this->common_data['company_data']['company_id'];
                $dataCINV['createdUserGroup'] = $this->common_data['user_group'];
                $dataCINV['createdPCID'] = $this->common_data['current_pc'];
                $dataCINV['createdUserID'] = $this->common_data['current_userID'];
                $dataCINV['createdUserName'] = $this->common_data['current_user'];
                $dataCINV['createdDateTime'] = $this->common_data['current_date'];
                $insertCINV=$this->db->insert('srp_erp_documenttracing', $dataCINV);
                $masterIDcinv=$this->db->insert_id();
            }
        }
    }

    function trace_MCINV_document()
    {
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $DocumentID = trim($this->input->post('DocumentID') ?? '');
        $docName='MCINV';

        $starting = $this->db->query("SELECT
                    srp_erp_mfq_customerinvoicemaster.invoiceAutoID, invoiceDate, invoiceCode, 
                    IF(confirmedYN = 1, 1, 0) AS docStatus, invoiceAmount, IFNULL(DNAmount, 0) AS deliveredAmount 
                FROM
                    srp_erp_mfq_customerinvoicemaster
                    JOIN ( SELECT SUM( transactionAmount ) AS invoiceAmount, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails GROUP BY invoiceAutoID ) total ON total.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS DNAmount, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails WHERE deliveryNoteDetID IS NOT NULL GROUP BY invoiceAutoID ) DN ON DN.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID 
                WHERE srp_erp_mfq_customerinvoicemaster.invoiceAutoID = $invoiceAutoID")->row_array();

        $this->db->where('empID', current_userID());
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('startDocumentID', $DocumentID);
        $this->db->where('startDocumentAutoID', $invoiceAutoID);
        $this->db->delete('srp_erp_documenttracing');

        $data['empID'] = current_userID();
        $data['documentID'] = $DocumentID;
        $data['documentName'] = $docName;
        $data['documentAutoID'] = $starting['invoiceAutoID'];
        $data['documentNarration'] = '';
        $data['relatedType'] = 0;
        $data['relatedDocumentType'] = null;
        $data['relatedDocumentAutoID'] = 0;
        $data['levelNo'] = 0;
        $data['masterID'] = 0;
        $data['documentStatus'] = 1;
        $data['startDocumentID'] = $DocumentID;
        $data['startDocumentAutoID'] = $starting['invoiceAutoID'];
        $data['documentDate'] = $starting['invoiceDate'];
        $data['documentAmount'] = $starting['invoiceAmount'];
        $data['matchedAmount'] = $starting['invoiceAmount'];
        $data['documentSystemCode'] = $starting['invoiceCode'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $insertStarting=$this->db->insert('srp_erp_documenttracing', $data);
        $masterIDRV=$this->db->insert_id();
        if($insertStarting){
            $CINVDerails = $this->get_CINV_by_MCINV($invoiceAutoID,1,$DocumentID,$invoiceAutoID,$masterIDRV);
        }else{
            $this->db->where('empID', current_userID());
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('startDocumentID', 'MCINV');
            $this->db->where('startDocumentAutoID', $starting['deliveryNoteID']);
            $this->db->delete('srp_erp_documenttracing');
        }
    }
}