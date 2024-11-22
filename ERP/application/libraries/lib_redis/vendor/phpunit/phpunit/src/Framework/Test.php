
<?php
function leaveEmployeeCalculation()
{
$companyID = current_companyID();
$leaveTypeID = $this->input->post('leaveTypeID');
$halfDay = $this->input->post('halfDay');
$startDate = $this->input->post('startDate');
$endDate = $this->input->post('endDate');
$isAllowminus = $this->input->post('isAllowminus');
$isCalenderDays = $this->input->post('isCalenderDays');
$isCalenderDays = ($isCalenderDays == '' ? 0 : $isCalenderDays);
$entitleSpan = $this->input->post('entitleSpan');
$entitleSpan = ($entitleSpan == '' ? 0 : $entitleSpan);
/*date diff*/
$date1 = new DateTime("$startDate");
$date2 = new DateTime("$endDate");
$diff = $date2->diff($date1)->format("%a");
$dateDiff = $diff + 1;
$dateDiff2 = $diff + 1;
$calenderDays['workingDays'] = 0;
if ($isCalenderDays == 1) {
    $sd = explode('-', $startDate);
    $sYear = $sd[0];
    $sMonth   = $sd[1];

    $ed = explode('-', $endDate);
    $eYear = $ed[0];
    $eMonth   = $ed[1];

    $calendervalidate=$this->db->query("SELECT sum(IF(monthnumber = {$sMonth} && year={$sYear}, 1, 0)) as startDate ,  sum(IF(monthnumber = {$eMonth} && year={$eYear}, 1, 0)) as endDate FROM `srp_erp_calender` WHERE monthnumber AND year AND companyID={$companyID}")->row_array();

    if($calendervalidate['startDate']==0 || $calendervalidate['endDate']==0 ){
        echo json_encode(array('error' => 1, 'message' => 'Calender not configured for selected date.'));
        exit;
    }

    $calenderDays = $this->db->query("SELECT SUM(IF(fulldate != '', 1, 0)) AS nonworkingDays, SUM(IF(fulldate != '', 1, 0)) - SUM(IF(weekend_flag = 1 || holiday_flag = 1, 1, 0)) AS workingDays FROM `srp_erp_calender` WHERE fulldate BETWEEN '{$startDate}' AND '{$endDate}' AND companyID = {$companyID}")->row_array();
    if ($calenderDays['workingDays'] == null) {
        /*    $calenderDays['workingDays']=  ($calenderDays['workingDays'] == null ? 0:$calenderDays['workingDays']);*/
        /* if ($calenderDays['workingDays'] == null) {
             echo json_encode(array('error' => 1, 'message' => 'Calender is not set for this company'));
             exit;
         }
         }*/
        $dateDiff = $calenderDays['workingDays'];
    }
    if ($halfDay == 1) {
        $dateDiff = $dateDiff2= $calenderDays['workingDays'] = 0.5; /*half day*/
    }
    $leaveBlance = $entitleSpan - $dateDiff;
    if ($isAllowminus == 1) {
        if ($leaveBlance < 0) {
            echo json_encode(array('error' => 3, 'message' => 'The maximum leave accumulation is  ' . "$entitleSpan" . ' days'));
            exit;
        }
    }
    echo json_encode(array('error' => 0, 'appliedLeave' => $dateDiff2, 'leaveBlance' => $leaveBlance, 'calenderYN' => $isCalenderDays, 'workingDays' => $calenderDays['workingDays']));
    exit;

}

?>('Dashboard','system/erp_dashboard','',0,1,0)
('Configuration','system/company/erp_company_configuration','',0,2,0)
('Procurement','#','',0,3,1)
('Inventory','#','',0,4,1)
('Accounts Payable','#','',0,5,1)
('Accounts Receivable','#','',0,6,1)
('Finance','#','',0,7,1)
('Asset Managment','#','',0,8,1)
('Treasury','#','',0,9,1)
('HRMS','#','',0,10,1)
('Masters','#','',0,11,1)
('TAX','#','',0,12,1)
('POS','#','',0,13,1)
('User Group Access','#','',0,14,1)


//procurment
('Approval',31,'#',1,1,1),
('Transactions',31,'#',1,2,1),
('Report',31,'#',1,3,1),
('Masters',31,'#',1,4,1)

// sub procurment
('PO',133,'#',2,1,0),
('Purchase Order',134,'#',2,1,0),
('Purchase Order List',135,'#',2,1,0),
('Purchasing Address',136,'#',2,1,0),


//Inventory),
('Approval',32,'#',1,1,1),
('Transactions',32,'#',1,2,1),
('Report',32,'#',1,3,1),
('Masters',32,'#',1,4,1)

// sub Inventory
('GRV',137,'#',2,1,0),
('Material Issue',137,'#',2,2,0),
('Stock Transfer',137,'#',2,3,0),
('Purchase Return',137,'#',2,4,0),
('Stock Adjustment',137,'#',2,5,0),

('Good Reciept Voucher',138,'#',2,1,0),
('Purchase Return',138,'#',2,2,0),
('Material Issue',138,'#',2,3,0),
('Stock Transfer',138,'#',2,4,0),
('Stock Adjustment',138,'#',2,5,0),



('Item Ledger',139,'#',2,1,0),
('Item Valuation Summary',139,'#',2,2,0),
('Item Counting',139,'#',2,3,0),
('Fast Moving Item',139,'#',2,4,0),
('Unbilled GRV',139,'#',2,5,0),


('Item Master',140,'#',2,1,0),
('Item Category',140,'#',2,2,0),
('GRV Addon Category',140,'#',2,3,0),
('Warehouse Master ',140,'#',2,4,0),


//Accounts Payable),
('Approval',33,'#',1,1,1),
('Transactions',33,'#',1,2,1),
('Report',33,'#',1,3,1),
('Masters',33,'#',1,4,1)

// sub Accounts Payable
('Supplier Invoices',141,'#',2,1,0),
('Debit Note',141,'#',2,2,0),
('Payment Voucher',141,'#',2,3,0),

('Supplier Invoices',142,'#',2,1,0),
('Debit Note',142,'#',2,2,0),
('Payment Voucher',142,'#',2,3,0),
('Payment Match',142,'#',2,4,0),


('Vendor Ledger',143,'#',2,1,0),
('Vendor Statment',143,'#',2,2,0),
('Vendor Aging summary',143,'#',2,3,0),
('Vendor Aging Detail',143,'#',2,4,0),

('Supplier Master',144,'#',2,1,0),



//Accounts Receivable),
('Approval',34,'#',1,1,1),
('Transactions',34,'#',1,2,1),
('Report',34,'#',1,3,1),
('Masters',34,'#',1,4,1)

// sub Accounts Receivable
('Invoice',145,'#',2,1,0),
('Credit Note',145,'#',2,2,0),
('Receipt Voucher',145,'#',2,3,0),

('Customer Invoices',146,'#',2,1,0),
('Credit Note',146,'#',2,2,0),
('Receipt Voucher',146,'#',2,3,0),



('Customer Ledger',147,'#',2,1,0),
('Customer Statment',147,'#',2,2,0),
('Customer Aging summary',147,'#',2,3,0),
('Customer Aging Detail',147,'#',2,4,0),

('Customer Master',148,'#',2,1,0),


//Finance),
('Approval',35,'#',1,1,1),
('Transactions',35,'#',1,2,1),
('Report',35,'#',1,3,1),
('Masters',35,'#',1,4,1)

//Asset Managment),
('Approval',36,'#',1,1,1),
('Transactions',36,'#',1,2,1),
('Report',36,'#',1,3,1),
('Masters',36,'#',1,4,1)

//Treasury),
('Approval',37,'#',1,1,1),
('Transactions',37,'#',1,2,1),
('Report',37,'#',1,3,1),
('Masters',37,'#',1,4,1)

//HRMS),
('Approval',38,'#',1,1,1),
('Transactions',38,'#',1,2,1),
('Report',38,'#',1,3,1),
('Masters',38,'#',1,4,1)

//Masters),
('Approval Users',39,'#',1,1,1)

//TAX),
('Approval',40,'#',1,1,1),
('Transactions',40,'#',1,2,1),
('Report',40,'#',1,3,1),
('Masters',40,'#',1,4,1)

//POS),
('Dashboard',41,'#',1,1,1),
('POS General',41,'#',1,2,1),
('POS Resturant',,41,'#',1,3,1),
('config',41,'#',1,4,1),
('Promotions',,41,'#',1,5,1),
('Masters',41,'#',1,6,1)

//groupAccess),
('Navigation group setup',42,'#',1,1,1),
('Employee navigation access',42,'#',1,2,1)

