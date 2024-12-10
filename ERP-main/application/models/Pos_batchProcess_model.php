<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_config.php
 * -- Project Name : POS
 * -- Module Name : POS Config model
 * -- Create date : 13 October 2016
 * -- Description : database script related to pos config.
 *
 * --REVISION HISTORY
 * --Date: 13-Oct 2016 : file created
 * --Date: 26-Dec 2018 :  SME-1272 Sales Summary Report PDF and mail forwarding option (Need to apply warehouseautoID for net totoal calculation. Fixed)
 * -- =============================================
 **/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pos_batchProcess_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_mailing_list()
    {
        $result = $this->db->select('*')
            ->from('srp_erp_pos_mailinglist')
            ->where('employeeID', $this->input->post('employeeID'))
            ->where('companyID', current_companyID())
            ->where('batchlist_id', $this->input->post('batchlist_id'))
            ->get()
            ->row_array();

        if (empty($result)) {
            $data['employeeID'] = $this->input->post('employeeID');
            $data['email'] = $this->input->post('email');
            $data['batchlist_id'] = $this->input->post('batchlist_id');
            $data['companyID'] = current_companyID();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();

            $result = $this->db->insert('srp_erp_pos_mailinglist', $data);
            if ($result) {
                return array('status' => 's', 'message' => 'successfully saved');
            } else {
                return array('status' => 'e', 'message' => 'Error while insert, Please contact your system support team');
            }
        } else {
            return array('status' => 'e', 'message' => 'This user is already added the this mailing List');
        }


    }

    function delete_mailingList($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete('srp_erp_pos_mailinglist');
        if ($result) {
            return array('status' => 's', 'message' => 'Record deleted successfully');
        } else {
            return array('status' => 'e', 'message' => 'Error while deleting, Please contact your system support team');
        }
    }

    function get_camera_setup_by_id($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_camera_setup");
        $this->db->where("id", $id);
        return $this->db->get()->row_array();
    }

    function get_cctv_feed()
    {
        $this->db->select('id, url_host, port');
        $this->db->from('srp_erp_pos_camera_setup');
        $this->db->where('outletID', get_outletID());
        $this->db->where('companyID', current_companyID());
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_report_fullyDiscountBills_admin($date, $data2, $cashier = null, $outlets = null, $companyID)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                  count( salesMaster.menuSalesID ) AS fullyDiscountBills 
                FROM
                      srp_erp_pos_menusalesmaster AS salesMaster
                      LEFT JOIN srp_erp_pos_menusalespayments msp ON msp.menuSalesID = salesMaster.menuSalesID 
                      AND msp.wareHouseAutoID = salesMaster.wareHouseAutoID
                WHERE
                  msp.menuSalesID IS NULL 
                  AND salesMaster.isVoid = 0 
                  AND salesMaster.isHold = 0
                  AND salesMaster.companyID = '" . $companyID . "'
                  AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        //echo $q;
        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function get_report_creditSales($date, $data2, $cashier = null, $outlets = null, $companyID)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND srp_erp_pos_menusalesmaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                Sum( srp_erp_pos_menusalespayments.amount ) AS salesAmount,
                count(srp_erp_pos_menusalespayments.menuSalesPaymentID) as countCreditSales,
                srp_erp_pos_customermaster.CustomerName,
                srp_erp_pos_customermaster.CustomerAutoID 
            FROM
                srp_erp_pos_menusalespayments
                INNER JOIN srp_erp_pos_paymentglconfigmaster ON srp_erp_pos_paymentglconfigmaster.autoID = srp_erp_pos_menusalespayments.paymentConfigMasterID
                INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_pos_customermaster.CustomerAutoID 
                WHERE
                  srp_erp_pos_menusalesmaster.companyID = '" . $companyID . "'
                AND srp_erp_pos_menusalesmaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "  
                AND srp_erp_pos_paymentglconfigmaster.autoID = 7 
                AND  srp_erp_pos_menusalesmaster.isVoid = 0 
                AND srp_erp_pos_menusalesmaster.isHold = 0
                GROUP BY
                    srp_erp_pos_customermaster.CustomerAutoID
                ORDER BY srp_erp_pos_menusalespayments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        return $result;
    }


    public function get_mailingList($id, $companyId)
    {
        return $this->db->select('srp_erp_pos_mailinglist.id as id, employeeID, email, srp_employeesdetails.Ename2 as name')
            ->from('srp_erp_pos_mailinglist')
            ->join('srp_erp_pos_batchlist', 'srp_erp_pos_batchlist.id = srp_erp_pos_mailinglist.batchlist_id', 'left')
            ->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_pos_mailinglist.employeeID', 'left')
            ->where('batchlist_id', $id)
            ->where('srp_erp_pos_mailinglist.companyID', $companyId)
            ->get()
            ->result();

    }


    function get_report_lessAmount_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM(deliveryCommissionAmount) AS lessAmount

                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                JOIN srp_paymentmethodmaster payments ON  payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . $companyID . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0
                AND payments.PaymentDescription = 'Cash'
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount_promotion_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }


        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    customers.customerName AS customerName,
                    SUM(grossTotal) AS netTotal,
                    SUM(IFNULL(promotionDiscountAmount,0) ) as lessAmount
              
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                LEFT JOIN srp_paymentmethodmaster payments ON payments.PaymentMethodMasterID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . $companyID . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(salesMaster.promotionID)
                AND salesMaster.promotionID <> 0
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Java App' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(payments.amount) AS lessAmount
            FROM
                srp_erp_pos_menusalespayments AS payments
                JOIN srp_erp_pos_menusalesmaster  AS salesMaster ON  salesMaster.menuSalesID = payments.menuSalesID
                AND salesMaster.wareHouseAutoID = payments.wareHouseAutoID
            WHERE
                payments.paymentConfigMasterID = 25
            AND  salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . $companyID . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_item_wise_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    'Item Wise Discount' AS customerName,
                    SUM( grossTotal ) AS netTotal,
                    SUM( IFNULL( salesitem.discountAmount, 0 ) ) AS lessAmount 
              
                FROM
                    srp_erp_pos_menusalesitems AS salesitem
	LEFT JOIN srp_erp_pos_menusalesmaster salesMaster ON salesMaster.menuSalesID = salesitem.menuSalesID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . $companyID . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                " . $qString . "
                " . $outletFilter . " ";


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_javaAppDiscount_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Discounts' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(salesMaster.discountAmount) AS lessAmount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . $companyID . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            AND salesMaster.discountAmount>0 " . $qString . $outletFilter;


        //echo $q.'<br/><br/><br/>';

       // exit;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    //SME-1272 - SQL join corrected
    function get_report_paymentMethod_admin($date, $data2, $cashier = null, $outlets = null, $companyID = null, $shift = false)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND payments.wareHouseAutoID IN(" . $outlets . ")";
        }

        if ($shift) {
            $groupBy = ' GROUP BY salesMaster.shiftID ';
        } else {
            $groupBy = ' GROUP BY payments.paymentConfigMasterID ';
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(payments.amount) AS NetTotal,
                    count(payments.menuSalesPaymentID) as countTransaction,
                    TIME_FORMAT(shift.startTime, '%h:%i %p') as  startTime,
                    TIME_FORMAT(shift.endTime, '%h:%i %p') as  endTime
                FROM
                    srp_erp_pos_menusalespayments AS payments
                LEFT JOIN srp_erp_pos_menusalesmaster AS salesMaster ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID
                LEFT JOIN  srp_erp_pos_shiftdetails shift ON salesMaster.shiftID = shift.shiftID AND shift.wareHouseID = payments.wareHouseAutoID
                WHERE
                  salesMaster.isVoid = 0 AND
                  salesMaster.isHold = 0
                AND salesMaster.companyID = '" . $companyID . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . $groupBy . " ORDER BY payments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount_2_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT						
                customertype.customerDescription,
                sum(payments.amount) AS subTotal,					
                count( salesMaster.menuSalesID ) AS countTotal
            FROM						
                srp_erp_pos_menusalesmaster AS salesMaster 
                LEFT JOIN  (
                    SELECT SUM( IFNULL(amount,0) ) as amount, menuSalesID, paymentConfigMasterID, wareHouseAutoID 
                    FROM srp_erp_pos_menusalespayments 
                    GROUP BY menuSalesID, wareHouseAutoID
                ) as payments ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID  
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . $companyID . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY
                    customertype.customerDescription 
                    ORDER BY customertype.customerDescription, salesMaster.wareHouseAutoID ";


        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /**
     * @param $date | From
     * @param $date2 | To
     * @param null $cashier | 1,2,3
     * @param null $outlets | 1,2,3
     * @param null $companyID | integer
     * @param null $shift | true, false
     * @return mixed
     */

    function get_report_salesReport_totalSales_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null, $shift = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ") ";
        }

        $groupBy = '';
        if ($shift) {
            $groupBy = ' GROUP BY salesMaster.shiftID ';
        }

        
        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount,
                salesMaster.shiftID,
                TIME_FORMAT(shift.startTime, '%h:%i %p') as  startTime,
                TIME_FORMAT(shift.endTime, '%h:%i %p') as  endTime
              
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN  srp_erp_pos_shiftdetails shift ON salesMaster.shiftID = shift.shiftID AND shift.wareHouseID = salesMaster.wareHouseAutoID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . $companyID . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter . $groupBy;


        // echo $q;

        if ($shift) {
            $result = $this->db->query($q)->result_array();
        } else {
            $result = $this->db->query($q)->row_array();
        }
        return $result;
    }

    function get_report_salesReport_totalTaxes_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                taxMaster.taxDescription AS Description,
                SUM(tax.taxAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalestaxes tax ON tax.menuSalesID = salesMaster.menuSalesID
            INNER JOIN srp_erp_taxmaster taxMaster ON taxMaster.taxMasterAutoID = tax.taxmasterID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . $companyID . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            " . $qString . $outletFilter . "
            GROUP BY tax.taxmasterID";
        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_ServiceCharge_admin($date, $date2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }


        $q = "SELECT
                'Service Charge' AS Description,
                SUM(sc.serviceChargeAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalesservicecharge sc ON sc.menuSalesID = salesMaster.menuSalesID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . $companyID . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter;
        //echo $q;


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function get_report_voidBills_admin($date, $data2, $cashier = null, $outlets = null, $companyID = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                   'Voided Bills'  AS paymentDescription,
                    SUM(salesMaster.subTotal) AS NetTotal,
                     count(	salesMaster.menuSalesID) as countTransaction
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster

                WHERE
                   salesMaster.isVoid = 1 AND
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . $companyID . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        $result = $this->db->query($q)->row_array();
       // echo $this->db->last_query();exit;
        return $result;
    }


    function getAllActiveOutlet($companyId)
    {
        return $this->db->select('wareHouseAutoID,wareHouseCode,wareHouseDescription')
            ->from('srp_erp_warehousemaster')
            ->where('companyID', $companyId)
            ->where('isPosLocation', 1)
            ->where('isActive', 1)
            ->order_by('wareHouseAutoID')
            ->get()
            ->result_array();
    }
}