<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_delivery.php
 * -- Project Name : Point of Sales Management with ERP
 * -- Module Name : POS Delivery
 * -- Create date : 11 November 2017
 * -- Description : Delivery Management Module  .
 *
 * --REVISION HISTORY
 * --Date: 11-Nov2017 : file created
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_delivery extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_delivery_model');
        $this->load->model('Pos_restaurant_model');
        $this->load->helper('pos');
    }

    function get_delivery_customer_details(){
        $phone = $this->input->post('phone');
        echo json_encode($this->db->select('*')->from('srp_erp_pos_customermaster')->where('customerTelephone', $phone)->get()->row());
    }

    function confirm_delivery_order()
    {
        $this->form_validation->set_rules('phone', 'Phone number', 'trim|required');
        $this->form_validation->set_rules('deliveryDate', 'Delivery Date', 'trim|required');
        //$this->form_validation->set_rules('email', 'Email Address', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            echo $this->Pos_delivery_model->confirm_delivery_order();
        }
    }

    function loadDeliveryCustomerInfo()
    {
        $phoneNo = $this->input->post('phoneNo');
        $customerInfo = $this->db->select('*')->from('srp_erp_pos_customermaster')->where('customerTelephone', $phoneNo)->get()->row_array();
        if ($customerInfo) {
            $customerInfo['DOB'] = !empty($customerInfo['DOB']) ? date('d/m/Y', strtotime($customerInfo['DOB'])) : null;
            echo json_encode(array('error' => 0, 'message' => 'customer exist', 'customerData' => $customerInfo));
        } else {
            echo json_encode(array('error' => 2, 'message' => 'Customer not exist'));
        }

    }

    public function allCalenderEvents()
    {


        $event_array2 = array();
        $companyID = current_companyID();

        //$where = "WHERE salesMaster.companyID = " . $companyID . $filterCategory . $filterStatus;
        $where = "WHERE salesMaster.isHold = 1  AND salesMaster.wareHouseAutoID ";

        $sql2 = "select deliveryOrderID as deliveryOrderID, salesMaster.invoiceCode as invoiceCode, salesMaster.holdRemarks as holdRemarks, deliveryOrders.deliveryDate as deliveryDate,  deliveryDate as DueDate,DATE_FORMAT(deliveryTime,'%h:%i %p') AS StartTime, deliveryTime as deliveryTime, '#FFC400' as backGroundColor,deliveryOrders.menuSalesMasterID as invoiceID,salesMaster.subTotal as billTotal,customerMaster.CustomerName,customerMaster.CustomerAddress1,salesMaster.companyLocalCurrency as amountCurrency FROM srp_erp_pos_deliveryorders as deliveryOrders INNER JOIN srp_erp_pos_menusalesmaster as salesMaster ON deliveryOrders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customermaster as customerMaster ON customerMaster.posCustomerAutoID = deliveryOrders.posCustomerAutoID " . $where;
        $result2 = $this->db->query($sql2)->result_array();

        foreach ($result2 as $record2) {
            $dateTime = $record2['deliveryDate'] . $record2['deliveryTime'];
            $record2['deliveryDate'] = date('Y-m-d h:i:s', strtotime($dateTime));

            $event_array2[] = array(
                'id' => $record2['deliveryOrderID'],
                'title' => $record2['invoiceCode'] . ' ' . $record2['amountCurrency'] . ':' . $record2['billTotal'] . '/=' . ' ' . $record2['CustomerName'] . ' ' . $record2['CustomerAddress1'],
                'start' => $record2['deliveryDate'],
                'end' => $record2['DueDate'],
                'color' => $record2['backGroundColor'],
                'invoiceID' => $record2['invoiceID'],
            );
        }
        $arr = array_merge($event_array2);

        echo json_encode($arr);
    }

    function delivery_load_payment_detail()
    {
        $invoiceID = $this->input->post('invoiceID');
        $this->db->select('payment.*,configMaster.description as paymentDescription');
        $this->db->from('srp_erp_pos_menusalespayments payment');
        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configMaster.autoID = payment.paymentConfigMasterID');
        $this->db->where('payment.menuSalesID', $invoiceID);
        $result = $this->db->get()->result_array();

        $data['invoiceID'] = $invoiceID;
        $data['payments'] = $result;
        $this->load->view('system/pos/delivery/delivery_load_payment_detail', $data);
    }

    function load_delivery_info()
    {
        $invoiceID = $this->input->post('invoiceID');
        $this->db->select('*');
        $this->db->from('srp_erp_pos_deliveryorders');
        $this->db->where('menuSalesMasterID', $invoiceID);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            $result['deliveryDate'] = date('d-m-Y', strtotime($result['deliveryDate']));
            $result['deliveryTime'] = date('h:i A', strtotime($result['deliveryTime']));
            echo json_encode(array('error' => 0, 'message' => 'found', 'tmpData' => $result));
        } else {
            $this->db->select('wareHouseAutoID');
            $this->db->from('srp_erp_pos_menusalesmaster');
            $this->db->where('menuSalesID', $invoiceID);
            $wareHouseAutoID = $this->db->get()->row_array();
            echo json_encode(array('error' => 1, 'message' => 'New Order', 'wareHouseAutoID' => $wareHouseAutoID['wareHouseAutoID']));
        }
    }

    function update_deliveryInfo()
    {
        $id = $this->input->post('deliveryOrderID');
        $data['deliveryDate'] = date('Y-m-d', strtotime($this->input->post('deliveryDate')));
        $data['deliveryTime'] = date('H:i:s', strtotime($this->input->post('deliveryTime')));
        $data['landMarkLocation'] = $this->input->post('landMarkLocation');
        $data['deliveryType'] = $this->input->post('deliveryType');

        $this->db->where('deliveryOrderID', $id);
        $result = $this->db->update('srp_erp_pos_deliveryorders', $data);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Delivery detail updated successfully'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Something went wrong, Please try again.'));
        }
    }

    function loadPrintTemplate()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $outletID = $this->input->post('outletID');
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $orderDetail = $this->Pos_delivery_model->get_deliveryOrder($invoiceID, $outletID);
        $totalAdvance = $this->Pos_delivery_model->get_totalAdvance($invoiceID, $outletID);
        $data['invoiceList'] = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID, $outletID);;
        $data['masters'] = $masters;
        $data['outletID'] = $outletID;
        $data['orderDetail'] = $orderDetail;
        $data['totalAdvance'] = $totalAdvance;
        $data['auth'] = true;

        $this->load->view('system/pos/printTemplate/restaurant-pos-advance-payment', $data);
    }

    function save_send_pos_email()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);
        $orderDetail = $this->Pos_delivery_model->get_deliveryOrder($invoiceID);
        $totalAdvance = $this->Pos_delivery_model->get_totalAdvance($invoiceID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['orderDetail'] = $orderDetail;
        $data['totalAdvance'] = $totalAdvance;
        $data['auth'] = true;
        $data['email'] = true;

        $msg = $this->load->view('system/pos/printTemplate/restaurant-pos-advance-payment', $data, true);
        $this->Pos_delivery_model->send_pos_email_advancePayment_deilvery($msg);
    }

    function delivery_dispatchOrder()
    {
        $outletID = get_outletID();
        $deliveryOrderID = $this->input->post('deliveryOrderID');
        if ($deliveryOrderID) {
            $orderInfo = $this->Pos_delivery_model->get_deliveryOrder_by_deliveryOrderID($deliveryOrderID);
            if (!empty($orderInfo)) {

                /** Total Bill Amount */
                $totalBillAmount = $this->db->select('*')
                    ->from('srp_erp_pos_menusalesmaster')
                    ->where('menuSalesID', $orderInfo['menuSalesMasterID'])
                    ->where('wareHouseAutoID', $outletID)
                    ->get()->row('subTotal');

                $discountPer = $this->db->select('*')
                    ->from('srp_erp_pos_menusalesmaster')
                    ->where('menuSalesID', $orderInfo['menuSalesMasterID'])
                    ->where('wareHouseAutoID', $outletID)
                    ->get()->row('discountPer');

                $promotionDiscount = $this->db->select('*')
                    ->from('srp_erp_pos_menusalesmaster')
                    ->where('menuSalesID', $orderInfo['menuSalesMasterID'])
                    ->where('wareHouseAutoID', $outletID)
                    ->get()->row('promotionDiscount');

                /** Total Paid Amount*/
                $q = "SELECT SUM(amount) as totalPaid FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $orderInfo['menuSalesMasterID'] . "' AND wareHouseAutoID = " . $outletID;

                $totalPaid = $this->db->query($q)->row('totalPaid');
                if ($totalPaid || $discountPer==100 || $promotionDiscount==100) {

                    if ((round($totalBillAmount, 1) == round($totalPaid, 1)) || $discountPer==100 || $promotionDiscount==100) {
                        $dataMS['isHold'] = 0;
                        $dataMS['modifiedPCID'] = current_pc();
                        $dataMS['modifiedUserID'] = current_userID();
                        $dataMS['modifiedUserName'] = current_user();
                        $dataMS['modifiedDateTime'] = format_date_mysql_datetime();
                        $this->db->where('menuSalesID', $orderInfo['menuSalesMasterID']);
                        $this->db->where('wareHouseAutoID', $outletID);
                        $result = $this->db->update('srp_erp_pos_menusalesmaster', $dataMS);

                        $dataDelivery['isDispatched'] = 1;
                        $dataDelivery['dispatchedDatetime'] = format_date_mysql_datetime();
                        $dataDelivery['dispatchedBy'] = current_userID();
                        $this->db->where('deliveryOrderID', $deliveryOrderID);
                        $this->db->where('id_store', $outletID);
                        $this->db->update('srp_erp_pos_deliveryorders', $dataDelivery);


                        if ($result) {
                            echo json_encode(array('error' => 0, 'message' => 'Order successfully Dispatched','menuSalesID' => $orderInfo['menuSalesMasterID'],'wareHouseAutoID' => $outletID));
                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'Error while updating menu sales master'));
                        }
                    } else {
                        $balance = $totalBillAmount - $totalPaid;
                        echo json_encode(array('error' => 2, 'message' => '<strong>This bill is not fully paid!</strong><br/>Please completed the remaining payment of ' . $balance . ' dispatch the order, ' . '<br/>Bill Amount:  ' . $totalBillAmount . '<br/> Total Paid : ' . $totalPaid));
                    }
                } else {
                    echo json_encode(array('error' => 2, 'message' => '<strong>Payment not submitted</strong><br/>Please complete the payment and dispatch this order.'));
                }


            } else {
                echo json_encode(array('error' => 1, 'message' => 'Error has occurred, order information is empty'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'delivery order not confirmed!'));
        }

    }

    public function upcoming_orders() {
        $this->form_validation->set_rules('date_from', 'From Date', 'trim|required');
        $this->form_validation->set_rules('date_to', 'To Date', 'trim|required');
        //$this->form_validation->set_rules('customers[]', 'Customer', 'trim|required');
        $this->form_validation->set_rules('outletID[]', 'Outlet', 'trim|required');
        $this->form_validation->set_rules('sort_by', 'Sort By', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $customers = $this->input->post('customers');
        $outlet = $this->input->post('outletID');
        $type_by = $this->input->post('type_by');
        $sort_by = $this->input->post('sort_by');
        $companyID = current_companyID();
        $custDrop='';
        $date_format_policy = date_format_policy();
        //$date_from = input_format_date($date_from, $date_format_policy);
        //$date_to = input_format_date($date_to, $date_format_policy);

        if (isset($date_from) && !empty($date_from)) {
            $date_from = new DateTime($date_from);
            $date_from = $date_from->format('Y-m-d H:i:s');
        }

        if (isset($date_to) && !empty($date_to)) {
            $date_to = new DateTime($date_to);
            $date_to = $date_to->format('Y-m-d H:i:s');
        }

        if($date_from > $date_to){
            die(json_encode(['e', 'To date should be greater than from date']));
        }
        if(!empty($customers)){
            $customers = implode(',', $customers);
            $custDrop='AND delOrd.posCustomerAutoID IN ('.$customers.')';
        }

        $outlet = implode(',', $outlet);
        $master_data = $this->db->query("SELECT menuSales.*, CONCAT(menuSales.menuSalesID,'_',menuSales.wareHouseAutoID) AS gr_auto_id, delOrd.deliveryType,
                             delOrd.posCustomerAutoID, cusMas.customerName pos_customerName, cusMas.customerTelephone, wareHouseDescription, delOrd.deliveryDate, 
                             delOrd.deliveryTime, delOrd.landMarkLocation, cusMas.CustomerAddress1
                             FROM srp_erp_pos_menusalesmaster AS menuSales
                             JOIN srp_erp_pos_deliveryorders AS delOrd ON delOrd.menuSalesMasterID = menuSales.menuSalesID AND menuSales.wareHouseAutoID = delOrd.wareHouseAutoID
                             JOIN srp_erp_pos_customermaster AS cusMas ON cusMas.posCustomerAutoID = delOrd.posCustomerAutoID AND cusMas.wareHouseAutoID = menuSales.wareHouseAutoID
                             JOIN srp_erp_warehousemaster AS wh_master ON wh_master.wareHouseAutoID = menuSales.wareHouseAutoID
                             WHERE delOrd.companyID = {$companyID} AND CONCAT(delOrd.deliveryDate,' ',delOrd.deliveryTime) BETWEEN '{$date_from}' AND '{$date_to}' AND isDispatched = 0 
                             $custDrop AND menuSales.wareHouseAutoID IN ({$outlet}) GROUP BY menuSales.menuSalesID  ORDER BY delOrd.{$sort_by}")->result_array();

        if(empty($master_data)){
            die(json_encode(['e' , 'No records found']));
        }


        $path = base_url();
        if($type_by==1){    //Detail Wise
            $master_details = $this->db->query("SELECT sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription,
                             CONCAT('{$path}',menuMaster.menuImage) AS menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,
                             sales.remarkes, menuMaster.pricewithoutTax, menuMaster.totalTaxAmount, menuMaster.totalServiceCharge, menu.isTaxEnabled, size_tb.code AS sizeCode,
                             size_tb.description AS sizeDescription, sales.isSamplePrinted, CONCAT(sales.menuSalesID,'_',sales.wareHouseAutoID) AS gr_auto_id
                             FROM srp_erp_pos_menusalesitems AS sales
                             JOIN srp_erp_pos_warehousemenumaster AS menu ON menu.warehouseMenuID = sales.warehouseMenuID
                             JOIN srp_erp_pos_warehousemenucategory AS category ON menu.warehouseMenuCategoryID = category.autoID
                             LEFT JOIN srp_erp_pos_menumaster AS menuMaster ON menuMaster.menuMasterID = menu.menuMasterID
                             LEFT JOIN srp_erp_pos_menusize AS size_tb ON size_tb.menuSizeID = menuMaster.menuSizeID
                             WHERE menu.isActive = 1 AND menu.isDeleted = 0 AND menuMaster.isDeleted = 0 AND sales.companyID = {$companyID} AND menu.companyID =  {$companyID}")->result_array();

            $template = 'system/pos/reports/upcoming_orders_ajax';
        }else{ // Category wise - summary
            $master_details = $this->db->query("SELECT
	delOrd.deliveryType,
	delOrd.posCustomerAutoID,
	menuMaster.menuMasterDescription,
	SUM(sales.qty) AS qty,
	menucategory.menuCategoryID,
	menucategory.menuCategoryDescription
FROM
	srp_erp_pos_menusalesmaster AS menuSales
JOIN srp_erp_pos_deliveryorders AS delOrd ON delOrd.menuSalesMasterID = menuSales.menuSalesID
AND menuSales.wareHouseAutoID = delOrd.wareHouseAutoID

LEFT JOIN srp_erp_pos_menusalesitems AS sales ON menuSales.menuSalesID = sales.menuSalesID AND menuSales.wareHouseAutoID = sales.wareHouseAutoID
JOIN srp_erp_pos_warehousemenumaster AS menu ON menu.warehouseMenuID = sales.warehouseMenuID
LEFT JOIN srp_erp_pos_menumaster AS menuMaster ON menuMaster.menuMasterID = menu.menuMasterID
JOIN srp_erp_pos_menucategory AS menucategory ON menuMaster.menuCategoryID = menucategory.menuCategoryID
                             WHERE delOrd.companyID = {$companyID} AND CONCAT(delOrd.deliveryDate,' ',delOrd.deliveryTime) BETWEEN '{$date_from}' AND '{$date_to}' AND isDispatched = 0
                             $custDrop AND menuSales.wareHouseAutoID IN ({$outlet}) GROUP BY menuMaster.menuMasterID ORDER BY delOrd.{$sort_by}")->result_array();

            $template = 'system/pos/reports/upcoming_orders_category_wise_ajax';
        }

        $data['template'] = $template;
        $data['auth'] = true;
        $data['sampleBill'] = true;
        $data['email'] = true;
        $data['from_up_coming'] = true;

        $view = '<div class="pull-right"> 
                     <button type="button" class="btn btn-danger btn-sm" onclick="print_orders()" ><i class="fa fa-print"></i> Print </button>
                     <a class="btn btn-success btn-sm" download="Upcoming Orders.xls"
                         onclick="var file = tableToExcel(\'print_content\', \'Upcoming Orders\'); $(this).attr(\'href\', file);">
                         <i class="fa fa-file-excel-o"></i> Excel 
                     </a>
                 </div>
                 <div id="print_content">';
        if($type_by==1) {   //detail
            $master_details = array_group_by($master_details, 'gr_auto_id');
            foreach ($master_data as $mas_data) {
                if (array_key_exists($mas_data['gr_auto_id'], $master_details)) {
                    $data['masters'] = $mas_data;
                    $data['invoiceList'] = $master_details[$mas_data['gr_auto_id']];

                    $view .= $this->load->view($template, $data, true);
                    $view .= '<hr style="width: 500px; margin-left: 0px; border-top: 1px solid #756f6f;"/>';
                }
            }
        }else{  //category wise - summary
            $unique_ids = array_unique(array_column($master_details,'menuCategoryID'));
            $this->db->select('menuCategoryID,menuCategoryDescription');
            $this->db->from('srp_erp_pos_menucategory');
            $this->db->where_in('menuCategoryID',$unique_ids);
            $category_array = $this->db->get()->result_array();

            $final_array = array();
            foreach ($category_array as $category){
                $semifinal_array = array();
                foreach ($master_details as $row){

                    if($category['menuCategoryID'] == $row['menuCategoryID']){

                        $semifinal_array[] = $row;
                    }
                    $final_array[$category['menuCategoryDescription']] = $semifinal_array;
                }

            }

            $data['invoiceList']= $final_array;
            $data['category_array']= $category_array;
            $view .= $this->load->view($template, $data, true);
            $view .= '<hr style="width: 500px; margin-left: 0px; border-top: 1px solid #756f6f;"/>';
        }
        $view .= '</div>';

        echo json_encode(['s', $template, 'view'=>$view]);

    }

}