<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix: profile_
 */
defined('BASEPATH') OR exit('No direct script access allowed');


$languageflowserve = getPolicyValues('LNG', 'All');

$lang['profile_my_profile'] = 'My Profile';

$lang['profile_personal_detail'] = 'Personal Detail';
$lang['profile_employee_id'] = 'Employee ID';
$lang['profile_full_name'] = 'Full Name';
$lang['profile_surname'] = 'Surname';
$lang['profile_date_of_birth'] = 'Date of Birth';
$lang['profile_marital_status'] = 'Marital Status';
$lang['profile_blood_group'] = 'Blood Group';
$lang['profile_employment_data'] = 'Employment Data';


$lang['profile_change_password'] = 'Change Password';
$lang['profile_current_password'] = 'Current Password';
$lang['profile_new_password'] = 'New Password';
$lang['profile_confirm_password'] = 'Confirm Password';

/*Pay slip*/
$lang['profile_pay_slip'] = 'Pay Slip';
$lang['profile_filter'] = 'Filter';
$lang['profile_payroll_type'] = 'Payroll Type';
$lang['profile_Month'] = 'Month';
$lang['profile_load'] = 'Load';
$lang['profile_monthly_allowance'] = 'Pay Slip - Monthly Allowance';
$lang['profile_payroll'] = 'Payroll';
$lang['profile_non_payroll'] = 'Non payroll';

/*Expense Claim*/
$lang['profile_expense_claim'] = 'Expense Claim';
$lang['profile_expense_claim_approval'] = 'Expense Claim Approval';
$lang['profile_monthly_allowance'] = 'Document Code';
$lang['profile_total_value'] = 'Total Value';
$lang['profile_file_name'] = 'File Name';
$lang['profile_add_expense_claim'] = 'Add Expense Claim';
$lang['profile_Step1_expense_claim_header'] = 'Step 1 - Expense Claim Header';
$lang['profile_Step2_expense_claim_detail'] = 'Step 2 - Expense Claim Detail';
// $lang['profile_Step3_expense_claim_detail'] = 'Step 3 - Attachment Detail';
$lang['profile_Step3_expense_claim_confirmation'] = 'Step 3 - Expense Claim Confirmation';
$lang['profile_expense_claim_detail'] = 'Expense Claim Detail';
$lang['profile_expense_claim_attachments'] = 'Expense Claim Attachments';
$lang['profile_expense_claim_category'] = 'Expense Claim Category';
$lang['profile_doc_reference'] = 'Doc Ref';
$lang['profile_add_expense_claim'] = 'Add Expense Claim';


if (in_array($languageflowserve, ['MSE', 'SOP', 'GCC','Nov', 'Flowserve','Micoda'])) {
    $lang['profile_expense_claim'] = 'Employee Claim';
    $lang['profile_expense_claim_approval'] = 'Employee Claim Approval';
    $lang['profile_monthly_allowance'] = 'Document Code';
    $lang['profile_total_value'] = 'Total Value';
    $lang['profile_file_name'] = 'File Name';
    $lang['profile_add_expense_claim'] = 'Add Employee Claim';
    $lang['profile_Step1_expense_claim_header'] = 'Step 1 - Employee Claim Header';
    $lang['profile_Step2_expense_claim_detail'] = 'Step 2 - Employee Claim Detail';
    // $lang['profile_Step3_expense_claim_detail'] = 'Step 3 - Attachment Detail';
    $lang['profile_Step3_expense_claim_confirmation'] = 'Step 3 - Employee Claim Confirmation';
    $lang['profile_expense_claim_detail'] = 'Employee Claim Detail';
    $lang['profile_expense_claim_attachments'] = 'Employee Claim Attachments';
    $lang['profile_expense_claim_category'] = 'Employee Claim Category';
    $lang['profile_doc_reference'] = 'Doc Ref';
    $lang['profile_add_expense_claim'] = 'Add Employee Claim';
} else {
    
    $lang['profile_expense_claim'] = 'Expense Claim';
    $lang['profile_expense_claim_approval'] = 'Expense Claim Approval';
    $lang['profile_monthly_allowance'] = 'Document Code';
    $lang['profile_total_value'] = 'Total Value';
    $lang['profile_file_name'] = 'File Name';
    $lang['profile_add_expense_claim'] = 'Add Expense Claim';
    $lang['profile_Step1_expense_claim_header'] = 'Step 1 - Expense Claim Header';
    $lang['profile_Step2_expense_claim_detail'] = 'Step 2 - Expense Claim Detail';
    // $lang['profile_Step3_expense_claim_detail'] = 'Step 3 - Attachment Detail';
    $lang['profile_Step3_expense_claim_confirmation'] = 'Step 3 - Expense Claim Confirmation';
    $lang['profile_expense_claim_detail'] = 'Expense Claim Detail';
    $lang['profile_expense_claim_attachments'] = 'Expense Claim Attachments';
    $lang['profile_expense_claim_category'] = 'Expense Claim Category';
    $lang['profile_doc_reference'] = 'Doc Ref';
    $lang['profile_add_expense_claim'] = 'Add Expense Claim';
}



/*Employee Leave Application*/
$lang['profile_add_leave'] = 'New Leave';
$lang['profile_document_code'] = 'Document Code';
$lang['profile_leave_type'] = 'Leave Type';

$lang['profile_employee_leave_application'] = 'Employee Leave Application';
$lang['profile_employee_name'] = 'Employee Name';
$lang['profile_starting_date'] = 'Starting Date';
$lang['profile_ending_date'] = 'Ending Date';
$lang['profile_half_day'] = 'Half Day';
$lang['profile_leave_entitled'] = 'Leave Entitled';
$lang['profile_leave_applied'] = 'Leave Applied';
$lang['profile_balance'] = 'Balance';

$lang['profile_taken'] = 'Taken';
$lang['profile_policy'] = 'Policy';
$lang['profile_leave_detail'] = 'Leave Detail';

$lang['profile_approval'] = 'Approval';
$lang['profile_approval_users'] = 'Approval Users';
$lang['profile_approval_level'] = 'Approval Level';

$lang['profile_add_item_detail'] = 'Add Item Detail';
$lang['profile_edit_item_detail'] = 'Edit Item Detail';
$lang['profile_discount_0_100'] = 'Discount % should be between 0 - 100';
$lang['profile_discount_unit_cost'] = 'Discount Amount should be less than the Unit Cost';
$lang['profile_cancelled'] = 'Cancelled';


$lang['profile_emp_code'] = 'Employee Code';
$lang['profile_designation'] = 'Designation';
$lang['profile_please_select_an_employee_to_continue'] = 'Please select an employee to continue';
$lang['profile_Personal_email'] = 'Personal Email';
$lang['profile_visa_expiry_date'] = 'Visa Expiry Date';
$lang['profile_date_of_join'] = 'Date of Join';
$lang['profile_man_power_no'] = 'Man Power No';
$lang['profile_departments'] = 'Departments';
$lang['profile_report_manager'] = 'Reporting Manager';
$lang['profile_family_details'] = 'Family Details';
$lang['profile_family_documents'] = 'Documents';
$lang['profile_bank_details'] = 'Bank Details';
$lang['profile_my_employee_list'] = 'My Employee List';
$lang['profile_current_password'] = 'Current Password';
$lang['profile_add_family_detail'] = 'Add Family Detail';
$lang['profile_relationship'] = 'Relationship';
$lang['profile_familydetail'] = 'Family Detail';
$lang['profile_passport_expiry_date'] = 'Passport Expiry Date';
$lang['profile_visa_no'] = 'Visa No';
$lang['profile_visa_expiry_date'] = 'Visa Expiry Date';
$lang['profile_relationship_status'] = 'Relationship Status';

$lang['profile_education'] = 'Education';

$lang['profile_sales_target_achieved'] = 'Sales Target Achieved';
$lang['profile_add_sales_target_achieved'] = 'Add Sales Target';
$lang['profile_update_sales_target_achieved'] = 'Update Sales Target Achieved';
$lang['profile_date_from_is_required'] = 'Date From is required';
$lang['profile_amount_is_required'] = 'Amount is required';

$lang['profile_period'] = 'Period';
$lang['profile_target_amount'] = 'Target Amount';

$lang['profile_achived_amount'] = 'Achieved Amount';
$lang['profile_there_are_no_sales'] = 'THERE ARE NO SALES TARGET ACHIEVED TO DISPLAY';

$lang['profile_payroll_nor_run_on_selected_month_for_you'] = 'Payroll Not run on selected month for you';
$lang['profile_segment'] = 'Segment';

$lang['profile_earnings'] = 'Earnings';
$lang['profile_total_earnings'] = 'Total Earnings';
$lang['profile_deductions'] = 'Deductions';
$lang['profile_installment_no'] = 'Installment No';
$lang['profile_total_deductions'] = 'Total Deductions';
$lang['profile_net_pay'] = 'Net Pay';
$lang['profile_salary_transfer_details'] = 'Salary Transfer Details';
$lang['profile_loan_details'] = 'Loan Details';
$lang['profile_pending_amount'] = 'Pending Amount';
$lang['profile_loan_code'] = 'Loan Code';
$lang['profile_no_pending_nstallments'] = 'No.Pending Installments';
$lang['profile_leave_details'] = 'Leave Details';
$lang['profile_entitled'] = 'Entitled';

$lang['profile_pay_slip_for_the_month_of'] = 'Pay Slip For The Month Of';
$lang['profile_pay_slip_capital'] = 'PAY SLIP';
$lang['profile_employee_no'] = 'Employee No';
$lang['profile_basic_br_allowance'] = 'Basic+BR Allowance';
$lang['profile_deduction_as_direct'] = 'Deduction as Direct';
$lang['profile_net_remuneration'] = 'Net Remuneration';
$lang['profile_emp_funtion'] = 'Function';
$lang['profile_emp_bussines_level_deviion'] = 'Business Level-Division';
$lang['profile_emp_bussines_level_segment'] = 'Business Level-Segment';
$lang['profile_emp_bussines_level_sub_segment'] = 'Business Level-Sub-Segment';

$lang['profile_change_profile_pic'] = 'Change Profile Picture';