<?php

/**
 * Created by PhpStorm.
 * User: Nasik
 * Date: 5/26/2019
 * Time: 4:57 PM
 */
class GenerateInvoice extends CI_Controller
{
    private $date_time;
    private $companyID;
    private $adminType;
    private $cron_log = [];

    function __construct()
    {
        parent::__construct();
        $this->load->helper('configuration');
        $this->date_time = date('Y-m-d H:i:s');
    }

    function index()
    {
        $this->adminType = $adminType = $this->uri->segment(2);
        $this->session->set_userdata(['adminType' => $adminType]);
        $to_day = date('Y-m-d');
        $pending_sub = $this->db->query("SELECT sub_his.subscriptionID, sub_his.companyID, sub_his.nextRenewalDate, com_tb.subscriptionAmount,
                                  cur_mas.currencyID, cur_mas.CurrencyCode, cur_mas.DecimalPlaces, com_tb.company_name, companyPrintAddress, company_email
                                  FROM srp_erp_company AS com_tb
                                  JOIN (
                                      SELECT subscriptionID, companyID, nextRenewalDate FROM companysubscriptionhistory
                                      WHERE nextRenewalDate <= '{$to_day}' AND isNextSubscriptionGenerated = 0
                                  ) AS sub_his ON sub_his.companyID = com_tb.company_id
                                  JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=com_tb.subscriptionCurrency 
                                  WHERE com_tb.paymentEnabled = 1 AND com_tb.isSubscriptionDisabled = 0")->result_array();                                        

        if (empty($pending_sub)) {
            $this->db->insert('cron_job_log', [
                'msg'=> 'Cron job trigged. Expired subscriptions not found.',
                'adminType'=> $this->adminType, 'created_at'=> $this->date_time
            ]);
            return 0;
        }

        $this->load->model('Dashboard_model');
        $pc = current_pc();
        $user_id = current_userID();
        $user_name = current_userName();
        $date_time = $this->date_time;

        $this->db->trans_start();

        $int_arr = [];
        $old_sub_update = [
            'isNextSubscriptionGenerated' => 1, 'modifiedPCID' => $pc, 'modifiedUserID' => $user_id,
            'modifiedUserName' => $user_name, 'modifiedDateTime' => $date_time, 'timestamp' => $date_time
        ];

        $description = $this->db->get_where('system_invoice_item_type', ['type_id' => 1])->row('description');
        
        foreach ($pending_sub as $key => $row) {

            $old_sub_id = $row['subscriptionID'];
            $this->company_id = $company_id = $row['companyID'];
            $nextRenewalDate = $row['nextRenewalDate'];
            $sub_startDate = date('Y-m-d', strtotime("$nextRenewalDate"));
            $due_date = date('Y-m-d', strtotime("$nextRenewalDate +14 days"));
            $nextRenewalDate = date('Y-m-d', strtotime("$sub_startDate +1 year"));
            $sub_amount = $row['subscriptionAmount'];
            $currencyID = $row['currencyID'];
            $dPlace = $row['DecimalPlaces'];

            /******** Update old subscription data ********/
            $qry_status = $this->db->where(['subscriptionID' => $old_sub_id])->update('companysubscriptionhistory', $old_sub_update);
            $this->add_to_log($qry_status);

            $sub_history_data = [
                'subscriptionStartDate' => $sub_startDate, 'dueDate' => $due_date, 'nextRenewalDate' => $nextRenewalDate, 'subscriptionAmount' => $sub_amount,
                'isInvoiceGenerated' => 1, 'companyID' => $company_id, 'createdPCID' => $pc, 'createdUserID' => $user_id, 'createdUserName' => $user_name,
                'createdDateTime' => $date_time, 'timestamp' => $date_time
            ];

            $qry_status = $this->db->insert('companysubscriptionhistory', $sub_history_data);            
            $this->add_to_log($qry_status);

            $sub_id = $this->db->insert_id();
            $invNoData = $this->Dashboard_model->generate_subscription_inv_no(1);
            $serialNo = $invNoData['serialNo'];
            $invNo = $invNoData['inv_no'];

            $master_data = [
                'subscriptionID' => $sub_id, 'invNo' => $invNo, 'invDate' => $date_time, 'invCur' => $currencyID, 'invDecPlace' => $dPlace,
                'serialNo' => $serialNo, 'invTotal' => $sub_amount, 'companyID' => $company_id, 'createdPCID' => $pc,
                'createdUserID' => $user_id, 'createdDateTime' => $date_time, 'timestamp' => $date_time
            ];
            $qry_status = $this->db->insert('subscription_invoice_master', $master_data);
            $this->add_to_log($qry_status);

            $inv_id = $this->db->insert_id();


            $detail = [
                'invID' => $inv_id, 'itemID' => 1, 'itemDescription' => $description, 'amount' => $sub_amount, 
                'amountBeforeDis'=> $sub_amount, 'discountAmount'=> 0, 'discountPer'=> 0, 'companyID' => $company_id,
                'createdPCID' => $pc, 'createdUserID' => $user_id, 'createdDateTime' => $date_time, 'timestamp' => $date_time
            ];
            $qry_status = $this->db->insert('subscription_invoice_details', $detail);
            $this->add_to_log($qry_status);

            $master_data['company_name'] = $row['company_name'];
            $master_data['companyPrintAddress'] = $row['companyPrintAddress'];
            $master_data['company_email'] = $row['company_email'];
            $master_data['CurrencyCode'] = $row['CurrencyCode'];

            $int_arr[$inv_id]['toEmail'] = $row['company_email'];
            $int_arr[$inv_id]['subject'] = 'Subscription Renewal';
            $int_arr[$inv_id]['mas_data'] = $master_data;
            $int_arr[$inv_id]['det_data'][] = $detail;

        }
        

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            echo 'Subscription invoice successfully created';

            $cron_msg = count($int_arr).' invoices generated.';
            if(count($int_arr) == 1){
                $cron_msg = 'Invoice generated for '.$master_data['company_name'];
            }

            $company_list = join(', ', array_column($pending_sub, 'companyID'));          

            $this->cron_log[] = [
                'msg'=> $cron_msg, 'process_company_list'=> $company_list,
                'adminType'=> $this->adminType, 'created_at'=> $date_time
            ];            
        } else {
            echo 'Error in subscription invoice create process.';            
        }

        if(!empty($this->cron_log)){            
            $this->db->insert_batch('cron_job_log', $this->cron_log);            
        }

        $this->load->helper('host');
        foreach ($int_arr as $mailData) {
            send_subscription_mail($mailData);
            //echo $this->load->view('email_subscription_template', $mailData, true);
            //echo '<pre>'; print_r($mailData); echo '</pre>';        die();
        }
    }

    function add_to_log($staus){
        if($staus){
            return true;  
        }
        
        $err = $this->db->error();

        $this->cron_log[] = [
            'msg'=> $err['message'], 'processed_qry'=> $this->db->last_query(), 
            'process_company_list'=> $this->company_id, 
            'adminType'=> $this->adminType, 'created_at'=> $this->date_time
        ];
    }

    function send_reminder_email()
    {
        $reminder_email = $this->db->query("SELECT inv_mas.invID,companysub.companyID, company.company_name, company.company_code, 
                           companysub.subscriptionStartDate, companysub.dueDate, company.company_email AS toEmail
                           FROM subscription_invoice_master AS inv_mas 
                           JOIN subscription_invoice_details AS inv_det ON inv_mas.invID = inv_det.invID 
                           JOIN system_invoice_item_type itm_type ON itm_type.type_id = inv_det.itemID
	                       JOIN companysubscriptionhistory AS companysub on companysub.subscriptionID = inv_mas.subscriptionID 
	                       JOIN srp_erp_company company  on company.company_id = companysub.companyID 
                           WHERE companysub.expiryEmailSent!= 1 AND inv_det.itemID = 1 AND inv_mas.isAmountPaid = 0 ")->result_array();

        $this->load->helper('host');
        if (!empty($reminder_email)) {
            foreach ($reminder_email as $row) {
                $email_reminder = send_reminder_mail($row);
                if ($email_reminder == 1) {
                    $this->db->set('expiryEmailSent', 1);
                    $this->db->where('companyID', $row['companyID']);
                    $this->db->update('companysubscriptionhistory');
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    } else {
                        $this->db->trans_commit();
                    }
                }

            }
        }

    }
}
