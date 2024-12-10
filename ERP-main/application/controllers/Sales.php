<?php use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Sales_modal');
        $this->load->model('Report_model');
    }

    function fetch_sales_commission()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];
        $this->datatables->select("srp_erp_salescommisionmaster.salesCommisionID as salesCommisionID,srp_erp_salescommisionmaster.confirmedByEmpID as confirmedByEmp ,transactionAmount as transactionAmount, ROUND(transactionAmount, 2) as transactionAmount_search, srp_erp_salescommisionmaster.salesCommisionID, asOfDate ,Description, transactionCurrencyDecimalPlaces, transactionCurrency,confirmedYN,approvedYN,salesCommisionCode,createdUserID,isDeleted,srp_erp_salescommisionmaster.referenceNo as referenceNo");
        $this->datatables->where('srp_erp_salescommisionmaster.companyID', $companyid);
        $this->datatables->from('srp_erp_salescommisionmaster');
        $this->datatables->join('(SELECT SUM(netCommision) as transactionAmount,salesCommisionID FROM srp_erp_salescommisionperson GROUP BY salesCommisionID) det', '(det.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID)', 'left');

        $this->datatables->add_column('detail', '$1 <br> <b>Ref No : </b> $2 ', 'Description,referenceNo');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SC",salesCommisionID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SC",salesCommisionID)');
        $this->datatables->add_column('edit', '$1', 'load_sc_action(salesCommisionID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('asOfDate', '<span >$1 </span>', 'convert_date_format(asOfDate)');
        echo $this->datatables->generate();
    }

    function fetch_sc_approval()
    {
        /** rejected = 1* not rejected = 0* */

        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        if($approvedYN==0)
        {
            $this->datatables->select('srp_erp_salescommisionmaster.salesCommisionID as salesCommisionID ,det2.transactionAmount as  transactionAmount,ROUND(det2.transactionAmount, 2) as  transactionAmount_search,srp_erp_salescommisionmaster.companyCode,salesCommisionCode,Description,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,,DATE_FORMAT(asOfDate,\'' . $convertFormat . '\') AS asOfDate,transactionCurrencyDecimalPlaces, transactionCurrency, srp_erp_salescommisionmaster.referenceNo  as referenceNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,salesCommisionID FROM srp_erp_salescommisiondetail GROUP BY salesCommisionID) det', '(det.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID)', 'left');
            $this->datatables->join('(SELECT SUM(netCommision) as transactionAmount,salesCommisionID FROM srp_erp_salescommisionperson GROUP BY salesCommisionID) det2', '(det2.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID)', 'left');
            $this->datatables->from('srp_erp_salescommisionmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_salescommisionmaster.salesCommisionID AND srp_erp_documentapproved.approvalLevelID = srp_erp_salescommisionmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_salescommisionmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SC');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'SC');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_salescommisionmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('salesCommisionCode', '$1', 'approval_change_modal(salesCommisionCode,salesCommisionID,documentApprovedID,approvalLevelID,approvedYN,SC,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SC", salesCommisionID)');
            $this->datatables->add_column('edit', '$1', 'sc_action_approval(salesCommisionID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_salescommisionmaster.salesCommisionID as salesCommisionID ,det2.transactionAmount as  transactionAmount,ROUND(det2.transactionAmount, 2) as  transactionAmount_search,srp_erp_salescommisionmaster.companyCode,salesCommisionCode,Description,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,,DATE_FORMAT(asOfDate,\'' . $convertFormat . '\') AS asOfDate,transactionCurrencyDecimalPlaces, transactionCurrency,srp_erp_salescommisionmaster.referenceNo  as referenceNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,salesCommisionID FROM srp_erp_salescommisiondetail GROUP BY salesCommisionID) det', '(det.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID)', 'left');
            $this->datatables->join('(SELECT SUM(netCommision) as transactionAmount,salesCommisionID FROM srp_erp_salescommisionperson GROUP BY salesCommisionID) det2', '(det2.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID)', 'left');
            $this->datatables->from('srp_erp_salescommisionmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_salescommisionmaster.salesCommisionID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SC');
            $this->datatables->where('srp_erp_salescommisionmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_salescommisionmaster.salesCommisionID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('salesCommisionCode', '$1', 'approval_change_modal(salesCommisionCode,salesCommisionID,documentApprovedID,approvalLevelID,approvedYN,SC,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SC", salesCommisionID)');
            $this->datatables->add_column('edit', '$1', 'sc_action_approval(salesCommisionID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_sales_commision_header()
    {
        $date_format_policy = date_format_policy();
        $date = $this->input->post('asOfDate');
        $asOfDate = input_format_date($date, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('salesPersonID[]', 'sales Person', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Finance Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Finance Year Period', 'trim|required');
        }
        $this->form_validation->set_rules('asOfDate', 'As Of Date', 'trim|required|validate_date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($asOfDate >= $financePeriod['dateFrom'] && $asOfDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Sales_modal->save_sales_commision_header());
                } else {
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'As Of Date not between Financial period !'));
                }
            }else{
                echo json_encode($this->Sales_modal->save_sales_commision_header());
            }
        }
    }

    function save_sales_target()
    {
        /* $this->form_validation->set_rules('datefrom', 'date from', 'trim|required|validate_date');
         $this->form_validation->set_rules('dateTo', 'date To', 'trim|required|validate_date');*/
        $this->form_validation->set_rules('fromTargetAmount', 'Form Amount', 'trim|required');
        $this->form_validation->set_rules('toTargetAmount', 'To Amount', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Commision Percentage', 'trim|required|less_than[100]');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $fromTargetAmount = $this->input->post('fromTargetAmount');
            $toTargetAmount = $this->input->post('toTargetAmount');
            if ($fromTargetAmount < $toTargetAmount) {
                $datefrom = strtotime($this->input->post('datefrom') ?? '');
                $dateTo = strtotime($this->input->post('dateTo') ?? '');
                if ($datefrom <= $dateTo) {
                    echo json_encode($this->Sales_modal->save_sales_target());
                } else {
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Date from value cannot be greater than Date to value !'));
                }
            } else {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'From Amount cannot be greater than To amount !'));
            }
        }
    }

    function laad_sales_commision_header()
    {
        echo json_encode($this->Sales_modal->laad_sales_commision_header());
    }

    function fetch_detail_header_lock()
    {
        echo json_encode($this->Sales_modal->fetch_detail_header_lock());
    }

    function load_sc_conformation()
    {
        $this->load->library('s3');
        $salesCommisionID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesCommisionID') ?? '');
        $data['extra'] = $this->Sales_modal->fetch_template_data($salesCommisionID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Sales_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        $data['sales_img']='';
        if($this->input->post('html')){
            $data['logo']=htmlImage;
            $data['sales_img']=base_url();
        }


        $html = $this->load->view('system/sales/erp_sc_print', $data, true);


        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function fetch_inv_detail()
    {
        $salesCommisionID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesCommisionID') ?? '');
        $data['extra'] = $this->Sales_modal->fetch_inv_detail($salesCommisionID);
        echo $this->load->view('system/sales/erp_sc_detail', $data, true);
    }

    function sales_commission_detail()
    {
        echo json_encode($this->Sales_modal->sales_commission_detail());
    }

    function delete_sc()
    {
        echo json_encode($this->Sales_modal->delete_sc());
    }

    function save_sales_person()
    {
        $this->form_validation->set_rules('SalesPersonName', 'Sales Person Name', 'trim|required');
        $this->form_validation->set_rules('salesPersonTargetType', 'Target Type', 'trim|required');
        $this->form_validation->set_rules('receivableAutoID', 'Receivable', 'trim|required');
        $this->form_validation->set_rules('expanseAutoID', 'Expanse', 'trim|required');
        $this->form_validation->set_rules('wareHouseAutoID', 'Ware House', 'trim|required');
        $this->form_validation->set_rules('wareHouseAutoID', 'Location', 'trim|required');
        $this->form_validation->set_rules('salesPersonTarget', 'Target', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            echo json_encode($this->Sales_modal->save_sales_person());
        }
    }

    function sc_confirmation()
    {
        echo json_encode($this->Sales_modal->sc_confirmation());
    }

    function save_sc_approval()
    {
        $system_code = trim($this->input->post('salesCommisionID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SC', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('salesCommisionID');
                $this->db->where('salesCommisionID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_salescommisionmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Sales_modal->save_sc_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('salesCommisionID');
            $this->db->where('salesCommisionID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_salescommisionmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'SC', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Sales_modal->save_sc_approval());
                    }
                }
            }
        }
    }

    function referbacksc()
    {
        $salesCommisionID = $this->input->post('salesCommisionID');
        $this->db->select('approvedYN,salesCommisionCode');
        $this->db->where('salesCommisionID', trim($salesCommisionID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_salescommisionmaster');
        $approved_sales_commisiion_master = $this->db->get()->row_array();
        if (!empty($approved_sales_commisiion_master)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_sales_commisiion_master['salesCommisionCode']));
        }
        else {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($salesCommisionID, 'SC');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function re_open_salescommishion()
    {
        echo json_encode($this->Sales_modal->re_open_salescommishion());
    }

    function get_sales_order_report()
    {
        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        //$this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Sales_modal->get_sales_order_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/sales/ajax/load-sales-order-report', $data, true);
        }
    }

    function get_group_sales_order_report()
    {

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {

            $errorHTML = $this->group_unlink(array("CUST"));

            if ($errorHTML) {
                echo warning_message($errorHTML);
            } else {

                $data["details"] = $this->Sales_modal->get_group_sales_order_report();

                $data["type"] = "html";
                echo $html = $this->load->view('system/sales/ajax/load-sales-order-report', $data, true);
            }
        }
    }


    function get_sales_order_drilldown_report()
    {
        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Sales_modal->get_sales_order_drilldown_report();
            $data["type"] = "html";
            $data["amountType"] = $this->input->post("type");
            echo $html = $this->load->view('system/sales/ajax/load-sales-order-drilldown-report', $data, true);
        }
    }

    function get_group_sales_order_drilldown_report()
    {
        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Sales_modal->get_group_sales_order_drilldown_report();
            $data["type"] = "html";
            $data["amountType"] = $this->input->post("type");
            echo $html = $this->load->view('system/sales/ajax/load-sales-order-drilldown-report', $data, true);
        }
    }

    function get_sales_order_report_pdf()
    {
        $data["details"] = $this->Sales_modal->get_sales_order_report();
        $data["type"] = "pdf";
        $html = $this->load->view('system/sales/ajax/load-sales-order-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function get_group_sales_order_report_pdf()
    {
        $data["details"] = $this->Sales_modal->get_group_sales_order_report();
        $data["type"] = "pdf";
        $html = $this->load->view('system/sales/ajax/load-sales-order-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function get_customer_invoice_report()
    {
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
            $this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["details"] = $this->Sales_modal->get_customer_invoice_report();
                $data["type"] = "html";
                $data["currency"] = $currency;
                echo $html = $this->load->view('system/sales/ajax/load-customer-invoice-report', $data, true);
            }
        }
    }

    function get_group_customer_invoice_report()
    {
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
            $this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $errorHTML = $this->group_unlink(array("CUST","SEG"));
                if ($errorHTML) {
                    echo warning_message($errorHTML);
                } else {
                    $data["details"] = $this->Sales_modal->get_group_customer_invoice_report();
                    $data["type"] = "html";
                    $data["currency"] = $currency;
                    echo $html = $this->load->view('system/sales/ajax/load-customer-invoice-report', $data, true);
                }
            }
        }
    }

    function get_customer_invoice_report_pdf()
    {
        $currency = $this->input->post('currency');
        $data["details"] = $this->Sales_modal->get_customer_invoice_report();
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/sales/ajax/load-customer-invoice-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function get_group_customer_invoice_report_pdf()
    {
        $currency = $this->input->post('currency');
        $data["details"] = $this->Sales_modal->get_group_customer_invoice_report();
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/sales/ajax/load-customer-invoice-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function get_sales_order_return_drilldown_report()
    {
        echo json_encode($this->Sales_modal->get_sales_order_return_drilldown_report());
    }

    function get_group_sales_order_return_drilldown_report()
    {
        echo json_encode($this->Sales_modal->get_group_sales_order_return_drilldown_report());
    }

    function get_sales_order_credit_drilldown_report()
    {
        echo json_encode($this->Sales_modal->get_sales_order_credit_drilldown_report());
    }

    function get_group_sales_order_credit_drilldown_report()
    {
        echo json_encode($this->Sales_modal->get_group_sales_order_credit_drilldown_report());
    }

    function get_get_revenue_summery_report()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_companyfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }
        //echo '<pre>';print_r($datearr); echo '</pre>'; die();

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        $this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Sales_modal->get_get_revenue_summery_report($datearr);
            $data["header"] = $datearr;
            $data["type"] = "html";
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/sales/ajax/load-revenue-summary-report', $data, true);
        }
    }

    function get_group_revenue_summery_report()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('groupFinanceYearID', $financeyear);
        $this->db->from('srp_erp_groupfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }
        //echo '<pre>';print_r($datearr); echo '</pre>'; die();

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'required');
        $this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $errorHTML = $this->group_unlink(array("CUST","SEG"));
            if ($errorHTML) {
                echo warning_message($errorHTML);
            } else {
                $data["details"] = $this->Sales_modal->get_group_revenue_summery_report($datearr);
                $data["header"] = $datearr;
                $data["type"] = "html";
                $data["currency"] = $currency;
                echo $html = $this->load->view('system/sales/ajax/load-revenue-summary-report', $data, true);
            }
        }
    }


    function get_revanue_details_drilldown_report()
    {
        $currency = $this->input->post('currency');


        $data["details"] = $this->Sales_modal->get_revanue_details_drilldown_report();
        $data["type"] = "html";
        $data["currency"] = $currency;
        echo $html = $this->load->view('system/sales/ajax/load-customer-invoice-summary-report', $data, true);

    }

    function get_group_revanue_details_drilldown_report()
    {
        $currency = $this->input->post('currency');


        $data["details"] = $this->Sales_modal->get_group_revanue_details_drilldown_report();
        $data["type"] = "html";
        $data["currency"] = $currency;
        echo $html = $this->load->view('system/sales/ajax/load-customer-invoice-summary-report', $data, true);

    }

    function get_revenue_summery_report_pdf(){
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_companyfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }

        $data["details"] = $this->Sales_modal->get_get_revenue_summery_report($datearr);
        $data["header"] = $datearr;
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/sales/ajax/load-revenue-summary-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function group_customer_linked()
    {
        return $this->Report_model->group_customer_linked();
    }

    function group_segment_linked()
    {
        return $this->Report_model->group_segment_linked();
    }

    function group_unlink($report)
    {
        $errorHTML = "";
        if (in_array('CUST', $report)) {
            if ($this->group_customer_linked()) {
                $errorHTML .= "<h4>Please link the following customer</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_customer_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('SEG', $report)) {
            if ($this->group_segment_linked()) {
                $errorHTML .= "<h4>Please link the following segment</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_segment_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        return $errorHTML;
    }
    function get_sales_person_performance_report()
    {
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');



        $this->form_validation->set_rules('salesperson[]', 'Sales Person', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        }
        else if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        }
        else {

            $data["details"] = $this->Sales_modal->get_sales_person_performance_report();
            $data["type"] = "html";
            $data['datefrom'] = $datefrom;
            $data['dateto'] = $dateto;
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/sales/ajax/load-sales-person-report', $data, true);
        }
    }
    function get_sales_person_performance_report_pdf()
    {
        $currency = $this->input->post('currency'); $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $data["details"] = $this->Sales_modal->get_sales_person_performance_report();
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $data['datefrom'] = $datefrom;
        $data['dateto'] = $dateto;
        $data["currency"] = $currency;
        $html = $this->load->view('system/sales/ajax/load-sales-person-report_pdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }
    function get_sales_preformance_dd()
    {
        $type = $this->input->post('salespersontype');
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $salesPersonID = $this->input->post('salesPersonID');
        if($type!=1)
        {
            $data["details"] = $this->Sales_modal->get_sales_preformance_dd_so();

        }else
        {
            $data["details"] = $this->Sales_modal->get_sales_preformance_dd();
        }



        $data["type"] = "html";
        $data["salespersontype"] = $type;
        $data["currency"] = $currency;
        $data["datefrom"] = $datefrom;
        $data["dateto"] = $dateto;
        $data["salesPersonID"] = $salesPersonID;
        echo $html = $this->load->view('system/sales/ajax/load-customer-sales-person-summary-report', $data, true);

        //echo json_encode($this->Sales_modal->get_sales_preformance_dd());
    }

    function get_item_wise_sales_filter()/*item ledger,valuation,counting*/
    {
        $data = array();
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/sales/ajax/erp_itemwise_sales_report_filter', $data);
    }

    function get_erp_itemwise_sales_report()
    {
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
        $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $location = $this->input->post('location');
            $financeyear = $this->input->post('financeyear');
            $this->db->select('beginingDate,endingDate');
            $this->db->where('companyFinanceYearID', $financeyear);
            $this->db->from('srp_erp_companyfinanceyear ');
            $financeyeardtl = $this->db->get()->row_array();
            $beginingDate = $financeyeardtl['beginingDate'];
            $endingDate = $financeyeardtl['endingDate'];

            $start = (new DateTime($beginingDate));
            $end = (new DateTime($endingDate));

            $interval = DateInterval::createFromDateString('1 month');
            $period = new DatePeriod($start, $interval, $end);
            $datearr = [];
            foreach ($period as $dt) {
                $dat = $dt->format("Y-m");
                $text = $dt->format("Y-M");
                $datearr[$dat] = $text;
            }

            $data = array();
            $data["header"] = $datearr;
            $data["groupBy"] = $this->input->post('groupBy');;
            $data["locations"] = $location;
            $data["output"] = $this->Sales_modal->get_itemwise_sales_report($datearr);
            $data["caption"] = $this->input->post('captionChk');
            $data["fieldName"] = $this->input->post('fieldNameChk');
            $data["from"] = $beginingDate;
            $data["to"] = $endingDate;
            $data["type"] = "html";
            $data["warehouse"] = $this->Report_model->get_warehouse();
            $this->load->view('system/sales/ajax/erp_itemwise_sales_report_view', $data);

        }
    }

    function get_erp_itemwise_sales_report_pdf()
    {
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
        $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
//            echo 'success';
            $location = $this->input->post('location');
            $financeyear = $this->input->post('financeyear');
            $this->db->select('beginingDate,endingDate');
            $this->db->where('companyFinanceYearID', $financeyear);
            $this->db->from('srp_erp_companyfinanceyear ');
            $financeyeardtl = $this->db->get()->row_array();
            $beginingDate = $financeyeardtl['beginingDate'];
            $endingDate = $financeyeardtl['endingDate'];

            $start = (new DateTime($beginingDate));
            $end = (new DateTime($endingDate));

            $interval = DateInterval::createFromDateString('1 month');
            $period = new DatePeriod($start, $interval, $end);
            $datearr = [];
            foreach ($period as $dt) {
                $dat = $dt->format("Y-m");
                $text = $dt->format("Y-M");
                $datearr[$dat] = $text;
            }
            $data = array();
            $data["header"] = $datearr;
            $data["locations"] = $location;
            $data["output"] = $this->Sales_modal->get_itemwise_sales_report($datearr);
            $data["caption"] = $this->input->post('captionChk');
            $data["fieldName"] = $this->input->post('fieldNameChk');
            $data["from"] = $beginingDate;
            $data["to"] = $endingDate;
            $data["type"] = "pdf";
            $data["warehouse"] = $this->Report_model->get_warehouse();
            $html = $this->load->view('system/sales/ajax/erp_itemwise_sales_report_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4-L');
        }
    }

    function get_itemwise_sales_drilldown_report()
    {
        if($this->input->post('itemAutoID')){
            $data["details"] = $this->Sales_modal->get_itemwise_sales_drilldown_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/sales/ajax/erp_itemwise_sales_report_drilldown', $data, true);
        }
    }

    /*Weekly Sales Analysis Report For Buyback
      */
    function get_weekly_sales_analysis_filter(){
        $data = array();
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/sales/ajax/weekly_sales_analysis_report_filter', $data);
    }

    function get_erp_weeklySalesIncome_report(){
        $dataFilter = $this->input->post('dataFilter');
        $datafilter2 = $this->input->post('datafilter2');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
        if($dataFilter == 1){
            if($datafilter2 == 1){
                $this->form_validation->set_rules('mainCategoryID[]', 'Main Category', 'trim|required');
                $this->form_validation->set_rules('subcategoryID[]', 'Sub Category', 'trim|required');
                $this->form_validation->set_rules('itemAutoID[]', 'Item', 'trim|required');
            } else if($datafilter2 == 2){
                $this->form_validation->set_rules('mainCategoryID[]', 'Main Category', 'trim|required');
                $this->form_validation->set_rules('subcategoryID[]', 'Sub Category', 'trim|required');
                $this->form_validation->set_rules('itemAutoID[]', 'Item', 'trim|required');
            } else if($datafilter2 == 3){
                $this->form_validation->set_rules('area[]', 'Area', 'trim|required');
            }
        } else if($dataFilter == 2){
            $this->form_validation->set_rules('mainCategoryID[]', 'Main Category', 'trim|required');
            $this->form_validation->set_rules('subcategoryID[]', 'Sub Category', 'trim|required');
            $this->form_validation->set_rules('itemAutoID[]', 'Item', 'trim|required');
        } else if($dataFilter == 3){
            if($datafilter2 == 1){
                $this->form_validation->set_rules('mainCategoryID[]', 'Main Category', 'trim|required');
                $this->form_validation->set_rules('subcategoryID[]', 'Sub Category', 'trim|required');
                $this->form_validation->set_rules('itemAutoID[]', 'Item', 'trim|required');
            } else if($datafilter2 == 2){
                $this->form_validation->set_rules('mainCategoryID[]', 'Main Category', 'trim|required');
                $this->form_validation->set_rules('subcategoryID[]', 'Sub Category', 'trim|required');
                $this->form_validation->set_rules('itemAutoID[]', 'Item', 'trim|required');
            } else if($datafilter2 == 3){
                $this->form_validation->set_rules('area[]', 'Area', 'trim|required');
            } else if($datafilter2 == 4){
                $this->form_validation->set_rules('cusCategory[]', 'Customer Category', 'trim|required');
                $this->form_validation->set_rules('customerID[]', 'Customer', 'trim|required');
            } else if($datafilter2 == 5){
                $this->form_validation->set_rules('cusCategory[]', 'Customer Category', 'trim|required');
                $this->form_validation->set_rules('customerID[]', 'Customer', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $cusCategory = $this->input->post("cusCategory");
            $companyFinancePeriodID = $this->input->post("financeyear_period");
            if(empty($companyFinancePeriodID)){
                $financeyear = $this->input->post('financeyear');
                $this->db->select('beginingDate,endingDate');
                $this->db->where('companyFinanceYearID', $financeyear);
                $this->db->from('srp_erp_companyfinanceyear ');
                $financeyeardtl = $this->db->get()->row_array();
            } else {
                $this->db->select('dateFrom as beginingDate, dateTo as endingDate');
                $this->db->where('companyFinancePeriodID', $companyFinancePeriodID);
                $this->db->from('srp_erp_companyfinanceperiod');
                $financeyeardtl = $this->db->get()->row_array();

            }
            $customerCategory = '';
            if($dataFilter == 4){
                $customerCategory = $this->db->query("SELECT categoryDescription FROM srp_erp_partycategories WHERE companyID = " . $this->common_data['company_data']['company_id'] . "  AND partyCategoryID IN (". join(',', $cusCategory) .")")->result_array();
            }
            $data = array();
            $data['dataFilter'] = $dataFilter;
            $data['customerCategory'] = $customerCategory;
            $data["header"] = $this->Sales_modal->get_erp_weeklySalesIncome_category();;
            $data["output"] = $this->Sales_modal->get_erp_weeklySalesIncome_report();
            $data["caption"] = $this->input->post('captionChk');
            $data["fieldName"] = $this->input->post('fieldNameChk');
            $data["from"] = $financeyeardtl['beginingDate'];
            $data["to"] = $financeyeardtl['endingDate'];
            $data["type"] = "html";
            $data["warehouse"] = $this->Report_model->get_warehouse();
            $this->load->view('system/sales/ajax/weekly_sales_analysis_report_view', $data);

        }
    }

    function get_erp_weeklySalesIncome_drilldown_report(){
        $dataFilter = $this->input->post('dataFilter');
        if($dataFilter == 1){
            $data["details"] = $this->Sales_modal->get_erp_weeklySalesIncome_drilldown_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/sales/ajax/weekly_sales_analysis_report_drilldown', $data, true);
        }
    }

    function loadCustomer(){
        $data_arr = array();
        $this->db->SELECT("customerAutoID,customerSystemCode,customerName");
        $this->db->FROM('srp_erp_customermaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        if (!empty($this->input->post('cusCategory'))) {
            $this->db->where_in('partyCategoryID', $this->input->post('cusCategory'));
        }
        $result = $this->db->get()->result_array();

        if (!empty($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerSystemCode'] ?? '') . ' | ' . trim($row['customerName'] ?? '');
            }
        }
        echo form_dropdown('customerID[]', $data_arr, '', 'class="form-control" id="filter_customerID"  multiple="" ');
    }

    function loadItems(){
        $data_arr = array();
        $this->db->SELECT("itemAutoID, itemSystemCode, itemDescription");
        $this->db->FROM('srp_erp_itemmaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        if (!empty($this->input->post('mainCategoryID'))) {
            $this->db->where_in('mainCategoryID', $this->input->post('mainCategoryID'));
        }
        if (!empty($this->input->post('subCategoryID'))) {
            $this->db->where_in('subCategoryID', $this->input->post('subCategoryID'));
        }
        if (!empty($this->input->post('subSubCategoryID'))) {
            $this->db->where_in('subSubCategoryID', $this->input->post('subSubCategoryID'));
        }
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['itemDescription'] ?? '');
            }
        }
        echo form_dropdown('itemAutoID[]', $data_arr, '', 'class="form-control" id="filter_itemAutoID"  multiple="" ');
    }

    function get_sales_order_details_report()
    {
        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        $this->form_validation->set_rules('datefrom', 'Date From', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div style="margin-top: 10px" class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Sales_modal->get_sales_order_details_report();
            $data["type"] = "html";
            echo $this->load->view('system/sales/ajax/load_sales_order_details_report', $data, true);
        }
    }

    function get_sales_order_details_report_pdf() {
        $data["details"] = $this->Sales_modal->get_sales_order_details_report();
        $data["type"] = "pdf";
        $html =  $this->load->view('system/sales/ajax/load_sales_order_details_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function load_sales_person_performance_details_report(){
        $this->form_validation->set_rules('wareHouseAutoID[]', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('detail_datefrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('detail_dateto', 'Date To', 'trim|required');
        $this->form_validation->set_rules('detail_salesperson[]', 'Sales Person', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        }else{
            $tmpitems = $this->input->post('detail_items');
            $tmpwarehouse = $this->input->post('wareHouseAutoID');
            $tempdatefrom = trim($this->input->post('detail_datefrom') ?? '');
            $tempdateto = trim($this->input->post('detail_dateto') ?? '');
            $tmpsalespersons = $this->input->post('detail_salesperson');
            $currency = $this->input->post('detail_currency');
            
            $requestType = $this->uri->segment(3);

            if (isset($tmpitems) && !empty($tmpitems)) {
                $items = join(",", $tmpitems);
                $filterItem = $items;
            } else {
                $filterItem = null;
            }
            
            if (isset($tmpwarehouse) && !empty($tmpwarehouse)) {
                $warehouse = join(",", $tmpwarehouse);
                $filterWarehouse = $warehouse;
            } else {
                $filterWarehouse = null;
            }

            if (isset($tempdatefrom) && !empty($tempdatefrom)) {
                $filterDateFrom = date('Y-m-d H:i:s', strtotime($tempdatefrom));
            } else {
                $filterDateFrom = date('Y-m-d 00:00:00');
            }

            if (!empty($tempdateto)) {
                $filterDateTo = date('Y-m-d H:i:s', strtotime($tempdateto));
            } else {
                $filterDateTo = date('Y-m-d 23:59:59');
            }

            if (isset($tmpsalespersons) && !empty($tmpsalespersons)) {
                $salespersons = join(",", $tmpsalespersons);
                $filterSalesperson = $salespersons;
            } else {
                $filterSalesperson = null;
            }

            $data["details"] = $this->Sales_modal->load_sales_person_performance_detail_report($filterItem, $filterWarehouse, $filterDateFrom, $filterDateTo, $filterSalesperson);
            $data["type"] = "html";
            $data['datefrom'] = $filterDateFrom;
            $data['dateto'] = $filterDateTo;
            $data["currency"] = $currency;

            if ($requestType == 'excel') {
                $data['file_name'] = 'Item wise Salesperson performance report';
                return $this->excel_itemwise_sales_person_performance_report($data);
            } else {
                echo $html = $this->load->view('system/sales/ajax/load-sales-person-details-report.php', $data, true);
            }
        }
    }
    function excel_itemwise_sales_person_performance_report($data){
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);
        $this->lang->load('sales_marketing_reports', $primaryLanguage);
        $this->lang->load('inventory', $primaryLanguage);
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Salesperson Performance Detail');

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells('A1:E1');
        $this->excel->getActiveSheet()->mergeCells("A2:E2");

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Item Wise Salesperson Performance Report'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:I4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('cee2f3');
        $header = [
            '#',
            $this->lang->line('sales_markating_sales_person'),
            $this->lang->line('transaction_common_item_code'),
            $this->lang->line('erp_item_master_secondary_code'),
            $this->lang->line('transaction_common_item_description'),
            $this->lang->line('transaction_common_uom'),
            $this->lang->line('common_qty'),
            $this->lang->line('common_unit_cost'),
            $this->lang->line('common_total_value')
        ];
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');

        $det = [];
        $details= $data['details'];
        if(empty($details)){
            $det[] =  $this->lang->line('common_no_records_found');
        }
        else{
            $n = 5;$value_total = 0;
            foreach ($details as $key=>$row){
                $format_decimal = ( $row['currencyDecimalPlaces'] == 3)? '#,##0.000': '#,##0.00';
                $dPlace = $row['currencyDecimalPlaces'];
                $averageAmount=0;
                
                $value_total +=   $row['amount'];
                if($row['qty'] == 0){
                    $averageAmount=format_number($row['amount'], $row['currencyDecimalPlaces']);
                }else{
                    $averageAmount=format_number($row['amount']/$row['qty'], $row['currencyDecimalPlaces']);
                }
                $det[] = [
                    ($key+1),$row['salesPersonName'] ,$row['itemSystemCode'], $row['seconeryItemCode'],$row['itemDescription'], $row['UnitOfMeasure'],
                    $row['qty'],$averageAmount, format_number($row['amount'], $row['currencyDecimalPlaces'])
                ];
                $n++;
            }
            $det[] = [
                '', '', '', '', '', '', '', '',  round($value_total, $dPlace)
            ];
            $this->excel->getActiveSheet()->getStyle("A{$n}:I{$n}")->getFont()->setBold(true)->setSize(11)->setName('Calibri');
            $this->excel->getActiveSheet()->getStyle("A{$n}:I{$n}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle("F5:G{$n}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->mergeCells("A{$n}:h{$n}");
            $this->excel->getActiveSheet()->getStyle("H5:I{$n}")->getNumberFormat()->setFormatCode($format_decimal);
            $this->excel->getActiveSheet()->fromArray(['Total'], null, "A{$n}");
        }
        $this->excel->getActiveSheet()->fromArray($det, null, 'A5');

        $filename = $data['file_name'].'.xls';
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }
    function get_sales_preformance_details_dd()
    {
        $docType = $this->input->post('type');
        $currency = $this->input->post('detail_currency');
        $datefrom = $this->input->post('detail_datefrom');
        $dateto = $this->input->post('detail_dateto');
        $salesPersonID = $this->input->post('salesPersonID');
        if($docType == 1 )
        {
            $data["details"] = $this->Sales_modal->get_itemwise_salesperson_preformance_dd_cnt_so();
        }else
        {
            $data["details"] = $this->Sales_modal->get_itemwise_salesperson_preformance_dd_cinv_do();
        }

        $data["type"] = "html";
        $data["docType"] = $docType;
        $data["currency"] = $currency;
        $data["datefrom"] = $datefrom;
        $data["dateto"] = $dateto;
        $data["salesPersonID"] = $salesPersonID;
        echo $html = $this->load->view('system/sales/ajax/load-item-sales-person-detail-report', $data, true);

        
    }

    function get_sales_summary_report()
    {
        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        //$this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Sales_modal->get_sales_summary_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/sales/ajax/load-sales-summary-report', $data, true);
        }
    }

    /**start : back to back revenue report */
    function get_back_to_back_revenue_report()
    {
        //$currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
            $this->form_validation->set_rules('supplier[]', 'Customer', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            }
             else {
                $data["details"] = $this->Sales_modal->get_back_to_back_revenue_report();
                $data["type"] = "html";
                echo $html = $this->load->view('system/sales/ajax/load_back_to_back_revenue_report', $data, true);
            }
        }
    }

    function get_back_to_back_revenue_report_pdf()
    {
        //$currency = $this->input->post('currency');
        $data["details"] = $this->Sales_modal->get_back_to_back_revenue_report();
        $data["type"] = "pdf";
        //$data["currency"] = $currency;
        $html = $this->load->view('system/sales/ajax/load_back_to_back_revenue_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    /**end : back to back revenue report */
}