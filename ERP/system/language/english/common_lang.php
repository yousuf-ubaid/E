<?php

/**
 * System messages translation for CodeIgniter(tm)
 */
defined('BASEPATH') or exit('No direct script access allowed');

$ray_Company_document_policy = getPolicyValues('RCDDP', 'All');
$language_policy = getPolicyValues('LNG', 'All');


/** Common */
$lang['common_add'] = 'Add';
$lang['common_save'] = 'Save';
$lang['common_save_and_confirm'] = 'Save & Confirm';
$lang['common_save_and_next'] = 'Save & Next';
$lang['common_save_as_draft'] = 'Save as Draft';
$lang['common_edit'] = 'Edit';
$lang['common_update'] = 'Update';
$lang['common_confirm'] = 'Confirm';
$lang['common_Close'] = 'Close';
$lang['common_is_active'] = 'Is Active';
$lang['common_in_active'] = 'in-active';
$lang['common_action'] = 'Action'; // ok
$lang['common_description'] = 'Description';
$lang['common_is_variable'] = 'Is Variable Payment';
$lang['common_status'] = 'Status';
$lang['common_all'] = 'All';
$lang['common_active'] = 'Active';
$lang['common_code'] = 'Code';
$lang['common_name'] = 'Name';
$lang['common_account_no'] = 'Account No';
$lang['common_holder'] = 'Holder';
$lang['common_bank'] = 'Bank';
$lang['common_branch'] = 'Branch';
$lang['common_pull_from_attendance'] = 'Is Pull From Attandance';


$lang['common_previous'] = 'Previous';

$lang['common_gender'] = 'Gender';
$lang['common_religion'] = 'Religion';
$lang['common_nationality'] = 'Nationality';
$lang['common_address'] = 'Address';
$lang['common_mobile'] = 'Mobile';
$lang['common_telephone'] = 'Telephone';
$lang['common_email'] = 'Email';
$lang['common_initial'] = 'Initial';
$lang['common_title'] = 'Title';
$lang['common_phone'] = 'Phone';
$lang['common_web'] = 'Web site';
$lang['common_user_name'] = 'User Name';
$lang['common_password'] = 'Password';
$lang['common_telephone_is_required'] = 'Telephone is required';


$lang['common_yes'] = 'Yes';
$lang['common_cancel'] = 'Cancel';
$lang['common_shift'] = 'Shift';


$lang['common_details'] = 'Details';
$lang['common_approved'] = 'Approved';
$lang['common_is_confirmed'] = 'Is Confirmed';
$lang['common_confirmed'] = 'Confirmed';
$lang['common_not_confirmed'] = 'Not Confirmed';
$lang['common_not_approved'] = 'Not Approved';
$lang['common_file_name'] = 'File Name';
$lang['common_file'] = 'File';
$lang['common_type'] = 'Type';
$lang['common_date'] = 'Date';
$lang['common_to'] = 'To';
$lang['common_status'] = 'Status';
$lang['common_clear'] = 'Clear';
$lang['common_refer_back'] = 'Refer Back';
$lang['common_day_month'] = 'Days In Month';


if (in_array($language_policy, ['MSE', 'SOP', 'GCC','Nov', 'Flowserve','Micoda'])) {
    $lang['common_segment'] = 'Cost Center';
}else{
    $lang['common_segment'] = 'Segment';
}


$lang['common_currency'] = 'Currency';
$lang['common_amount'] = 'Amount';
$lang['common_no_records_found'] = 'No Records Found';
$lang['common_attachments'] = 'Attachments';
$lang['common_add_attachments'] = 'Add Attachments';
$lang['common_edit_attachments'] = 'Edit Attachments';


$lang['common_view'] = 'View';
$lang['common_comment'] = 'Comment';
$lang['common_month'] = 'Month';
$lang['common_confirmed_date'] = 'Confirmation Date';
$lang['common_confirmed_by'] = 'Confirmed By';
$lang['common_confirmed_on'] = 'On';
$lang['common_approved_by'] = 'Approved By';

$lang['common_approved_date'] = 'Approved Date';
$lang['common_level'] = 'Level';
$lang['common_levels'] = 'Levels';


$lang['common_are_you_sure'] = 'Are you sure?';
$lang['common_you_want_to_delete'] = 'You want to delete this record!';
$lang['common_you_want_to_delete_all_records_of_this_employee'] = 'you want to delete all records of this employee!';
$lang['common_you_want_to_delete_all'] = 'you want to delete all record!';
$lang['common_you_want_to_refer_back'] = 'You want to refer back!';
$lang['common_you_want_to_refer_back_cancellation'] = 'You want to refer back this cancellation!';
$lang['common_you_want_to_cancel'] = 'You want to cancel!';
$lang['common_you_want_to_confirm_this_document'] = 'You want to confirm this document!';
$lang['common_you_want_to_generate_sales_order'] = 'You want to generate sales order!';
$lang['common_you_want_to_save_this_document'] = 'You want to save this document!';
$lang['common_you_want_to_edit_this_document'] = 'You want to edit this document!';
$lang['common_you_want_to_edit_this_record'] = 'You want to edit this record!';
$lang['common_delete'] = 'Delete';
$lang['common_cancel'] = 'Cancel';
$lang['common_company_id'] = 'Company ID';
$lang['common_percentage'] = 'Percentage';
$lang['common_category'] = 'Category';
$lang['common_partially_approved'] = 'Partially Approved';
$lang['common_from'] = 'From';
$lang['common_no'] = 'No';
$lang['common_confirmation'] = 'Confirmation';
$lang['common_are_you_sure_you_want_to_close_the_counter'] = 'Are you sure you want to close the counter?';

$lang['common_are_you_sure_you_want_to_make_this_as_p_d'] = 'You want to make this record as primary designation!';

$lang['common_are_you_sure_you_want_to'] = 'You want to';



$lang['common_filters'] = 'Filters';
$lang['common_search'] = 'Search';
$lang['common_submit'] = 'Submit';
$lang['common_report'] = 'Report';
$lang['common_form'] = 'Form';
$lang['common_generate'] = 'Generate';
$lang['common_print'] = 'Print';
$lang['common_submit_and_print'] = 'Submit and Print';
$lang['common_submit_and_close'] = 'Submit and Close';




$lang['common_step'] = 'Step';
$lang['common_no_attachment_found'] = 'No Attachment Found';
$lang['common_previous'] = 'Previous';
$lang['common_save_change'] = 'Save Changes';
$lang['common_update_changes'] = 'Update changes';



$lang['common_gl_code'] = 'GL Code';
$lang['common_next'] = 'Next';
$lang['common_pending'] = 'Pending';
$lang['common_skipped'] = 'Skipped';
$lang['common_closed'] = 'Closed';
$lang['common_canceled'] = 'Canceled';
$lang['common_canceled_req'] = 'Cancellation Request';
$lang['common_not_closed'] = 'Not Closed';


$lang['common_field'] = 'Field';
$lang['common_bom_number'] = 'BOM Number';


$lang['common_Upload'] = 'Upload';
$lang['common_designation'] = 'Designation';
$lang['common_designation_simple'] = 'designation';
$lang['common_update_and_Confirm'] = 'Update & Confirm';
$lang['common_update_add_new'] = 'Add New';
$lang['common_monthly'] = 'Monthly';

$lang['common_column'] = 'Column';



$lang['common_load'] = 'Load';
$lang['common_clear_all'] = 'Clear All';
$lang['common_processed'] = 'Processed';
$lang['common_total'] = 'Total';
$lang['common_pay'] = 'Pay';

$lang['common_by_cash'] = 'By Cash';
$lang['common_default'] = 'Default';
$lang['common_group'] = 'Group';
$lang['common_new'] = 'New';
$lang['common_note'] = 'Note';
$lang['common_reason'] = 'Reason';
$lang['common_no_records_available'] = 'No Records Available';
$lang['common_add_detail'] = 'Add Detail';
$lang['common_create_new'] = 'Create New';
$lang['common_company'] = 'Company';
$lang['common_select_type'] = 'Select Type';
$lang['common_select_a_option'] = 'Select a option';
$lang['common_select_all'] = 'Select All';
$lang['common_add_all'] = 'Add All';
$lang['common_records_not_found'] = 'Records not Found';
$lang['common_day'] = 'Day';
$lang['common_notes'] = 'Notes';
$lang['common_customer'] = 'Customer';
$lang['common_payment'] = 'Payment';
$lang['common_please_select'] = 'Please select';
$lang['common_non'] = 'None';
$lang['common_processing'] = 'Processing';
$lang['common_menu'] = 'Menu';
$lang['common_image'] = 'Image';
$lang['common_item'] = 'Item';
$lang['common_crew'] = 'Crew';
$lang['common_tables'] = 'Tables';
$lang['common_price'] = 'Price';
$lang['common_select_employee'] = 'Select Employee';
$lang['common_Location'] = 'Location';
$lang['common_warehouse'] = 'Warehouse';
$lang['common_ok'] = 'Ok';
$lang['common_time'] = 'Time';
$lang['common_cash'] = 'Cash';
$lang['common_visa'] = 'VISA';
$lang['common_master_card'] = 'Master Card';
$lang['common_cheque'] = 'Cheque';
$lang['common_change'] = 'Change';


$lang['common_formula_builder'] = 'Formula Builder';
$lang['common_add_formula'] = 'Add Formula';
$lang['common_balance_payment'] = 'Balance Payment';
$lang['common__monthly_addition'] = 'Monthly Addition';
$lang['common__monthly_deduction'] = 'Monthly Deduction';


$lang['common_an_error_occurred_Please_try_again'] = 'An Error Occurred! Please Try Again';
$lang['common_paysheet'] = 'Pay sheet';
$lang['common_not_confirmed_yet'] = 'not confirmed yet';
$lang['common_please_refresh_and_try_again'] = 'Please refresh and try again';
$lang['common_failed'] = 'failed';
$lang['common_error'] = 'Error';
$lang['common_success'] = 'Success!';
$lang['common_warning'] = 'Warning!';
$lang['common_information'] = 'Information';
$lang['common_not_found'] = 'Not Found';
$lang['common_no_data_available_in_table'] = 'No data available in table';
$lang['common_show'] = 'Show ';
$lang['common_select_relationship'] = 'Select Relationship';
$lang['common_select_title'] = 'Select Title';
$lang['common_select_nationality'] = 'Select Nationality';
$lang['common_date_of_birth'] = 'Date of Birth';
$lang['common_male'] = 'Male';
$lang['common_female'] = 'Female';
$lang['common_attachment'] = 'Attachment';
$lang['common_document'] = 'Document';
$lang['common_short_order'] = 'Short Order';
$lang['common_passport'] = 'Passport';
$lang['common_visa'] = 'Visa';
$lang['common_insurance'] = 'Insurance';
$lang['common_national_no'] = 'National No';
$lang['common_id_no'] = 'ID No';
$lang['common_passport_number_no'] = 'Passport No';
$lang['common_insurance_category'] = 'Insurance Category';
$lang['common_insurance_code'] = 'Insurance Code';
$lang['common_cover_from'] = 'Cover From';
$lang['common_approval_user'] = 'Approval user';
$lang['common_no_matching_records_found'] = 'No matching records found';
$lang['common_department'] = 'Department';
$lang['common_addition'] = 'Addition';
$lang['common_deduction'] = 'Deduction';
$lang['common_select_floor'] = 'Select Floor';
$lang['common_data_changes'] = 'Personal data changes';
$lang['common_family_changes'] = 'Family data changes';
$lang['common_grand_total'] = 'Grand Total';
$lang['common_date_is_required'] = 'Date is required';
$lang['common_description_is_required'] = 'Description is required';
$lang['common_type_is_required'] = 'Type is required';
$lang['common_gl_code_is_required'] = 'GL code is required';
$lang['common_number'] = 'Number';
$lang['common_after'] = 'After';
$lang['common_before'] = 'Before';
$lang['common_select_currency'] = 'Select Currency';
$lang['common_document_date_is_required'] = 'Document Date is required';
$lang['common_currency_is_required'] = 'Currency is required';
$lang['common_select'] = 'Select';
$lang['common_reporting_manager'] = 'Reporting Manager';
$lang['common_referred_back_by'] = 'Referred back by';
$lang['common_select_leave_group'] = 'Select Leave Group';
$lang['common_start_hour_is_required'] = 'Start Hour is required';
$lang['common_end_hour_is_required'] = 'End Hour is required';
$lang['common_hourly_rate_is_required'] = 'Hourly Rate is required';
$lang['common_estimatedQty'] = 'Estimated Qty';



$lang['common_employee_is_required'] = 'Employee is required';
$lang['common_effective_date_is_required'] = 'Effective Date is required';
$lang['common_new_amount_is_required'] = 'New amount is required';
$lang['common_category_is_required'] = 'Category is required';
$lang['common_please_fill_all_required_fields'] = 'Please fill all required fields';

$lang['common_hr_documents'] = 'HR Documents';
$lang['common_add_employee'] = 'Add Employee';
$lang['common_hours'] = 'Hours';
$lang['common_rate'] = 'Rate';
$lang['common_equivalent_hrs'] = 'Equivalent Hours';


$lang['common_customer_name'] = 'Customer Name';
$lang['common_value'] = 'Value';
$lang['common_approval'] = 'Approval';
$lang['common_the_selected_file_is_not_valid'] = 'The selected file is not valid';
$lang['common_file_is_required'] = 'File is required';
$lang['common_to_date_is_required'] = 'To date is required';
$lang['common_floor_is_required'] = 'Floor is required';
$lang['common_search_name'] = 'Search Name';
$lang['common_from_date_is_required'] = 'From date is required';
$lang['common_comments'] = 'Comments';
$lang['common_this_value_is_not_valid'] = 'This value is not valid';
$lang['common_employees'] = 'Employees';
$lang['common_employee_name'] = 'Employee Name';
$lang['common_start_date'] = 'Start Date';
$lang['common_invoice_number'] = 'Invoice Number';
$lang['common_reference'] = 'Reference';
$lang['common_reference_number'] = 'Reference Number';
$lang['common_reference_no'] = 'Reference No';
$lang['common_end_date'] = 'End Date';
$lang['common_please_fill_all_fields'] = 'Please fill all fields';
$lang['common_select_salary_category'] = 'Select Salary Category';
$lang['common_account'] = 'Account';
$lang['common_master_category_is_required'] = 'Master category is required';
$lang['common_salary_category_is_required'] = 'Salary Category is required';
$lang['common_following_items_already_exist'] = 'Following items already exist';
$lang['common_fax'] = 'Fax';
$lang['common_select_description'] = 'Select Description';

$lang['common_column'] = 'Column';
$lang['common_please_contac_support_team'] = 'Please contact support team';
$lang['common_approval_level'] = 'Approval Level';
$lang['common_document_confirmed_by'] = 'Document Confirmed By';
$lang['common_document_date'] = 'Document Date';
$lang['common_document_code'] = 'Document code';
$lang['common_confirmed_date'] = 'Confirmed Date';
$lang['common_approved_date'] = 'Approved Date';
$lang['common_date'] = 'Date';
$lang['common_document_not_approved_yet'] = 'Document not approved yet';
$lang['common_expense_claim'] = 'Expense Claim';
$lang['common_select_segment'] = 'Select Segment';
$lang['common_segment_is_required'] = 'Segment is required';
$lang['common_you_want_to_delete_this_attachment_file'] = 'You want to delete this attachment file';
$lang['common_deleted_successfully'] = 'Deleted Successfully';
$lang['common_deletion_failed'] = 'Deletion Failed';
$lang['common_select_claim_category'] = 'Select Claim Category';
$lang['common_expense_claim_attachments'] = 'Expense Claim Attachments';
$lang['common_select_gl_code'] = 'Select GL Code';
$lang['common_as_of_date'] = 'As of Date';
$lang['common_employee'] = 'Employee';
$lang['common_attendees'] = 'Attendees';
$lang['common_please_select_at_least_one_employee_to_proceed'] = 'Please select at least one employee to proceed';
$lang['common_first_month_is_required'] = 'First month is required';
$lang['common_second_month_is_required'] = 'Second month is required';
$lang['common_mandatory'] = 'Mandatory';
$lang['common_sort_order'] = 'Sort Order';
$lang['common_is_required'] = 'Is Required';
$lang['common_select_document'] = 'Select Document';
$lang['common_Country'] = 'Country';
$lang['common_country_name'] = 'Country Name';
$lang['common_showing'] = 'Showing';
$lang['common_of'] = 'of';
$lang['common_entries'] = 'entries';
$lang['common_un_check_all'] = 'Un check all';
$lang['common_document_name_is_required'] = 'Document name is required';
$lang['common_supplier_name'] = 'Supplier Name';
$lang['common_purchase_order'] = 'Purchase Order';
$lang['common_supplier'] = 'Supplier';
$lang['common_contact'] = 'Contact';
$lang['common_uom'] = 'UOM';
$lang['common_qty'] = 'Qty';
$lang['common_unit'] = 'Unit';
$lang['common_discount'] = 'Discount';
$lang['common_net_cost'] = 'Net Cost';
$lang['common_cost'] = 'Cost';
$lang['common_tax'] = 'Tax';
$lang['common_transaction'] = 'Transaction';
$lang['common_referred_back'] = 'Referred-back';
$lang['common_status_is_required'] = 'Status is required';
$lang['common_document_approved_id_is_required'] = 'Document Approved ID is required';
$lang['common_draft'] = 'Draft';
$lang['common_total_value'] = 'Total Value';
$lang['common_comments_are_required'] = 'Comments are required';
$lang['common_you_want_to_re_open'] = 'You want to re open';
$lang['common_year'] = 'Year';

$lang['common_add_item'] = 'Add Item';
$lang['common_unit_cost'] = 'Unit Cost';
$lang['common_net_amount'] = 'Net Amount';
$lang['common_item_id'] = 'Item ID';
$lang['common_item_description'] = 'Item Description';
$lang['common_select_uom'] = 'Select UOM';
$lang['common_name_is_required'] = 'Name is required';
$lang['common_supplier_currency_is_required'] = 'Supplier Currency is required';
$lang['common_filter'] = 'Filter';
$lang['common_standard'] = 'Standard';
$lang['common_aelect_supplier'] = 'Select Supplier';
$lang['common_select_ship'] = 'Select Ship';
$lang['common_contact_number'] = 'Contact Number';
$lang['common_days'] = 'Days';
$lang['common_project'] = 'Project';
$lang['common_select_project'] = 'Select Project';
$lang['common_you_want_to_change_leave_group'] = 'You want to change the leave group';



$lang['common_directory'] = 'Directory';
$lang['common_full_name_is_required'] = 'Full Name is required';
$lang['common_e_mail_required'] = 'E-Mail required';
$lang['common_gender_is_required'] = 'Gender is required';
$lang['common_narration'] = 'Narration';
$lang['common_joined_date'] = 'Joined Date';
$lang['common_manager'] = 'Manager';
$lang['common_select_a_nationality'] = 'Select a Nationality';
$lang['common_select_a_maritial_status'] = 'Select a Maritial Status';
$lang['common_select_a_blood_group'] = 'Select a Blood Group';
$lang['common_select_a_religion'] = 'Select a Religion';
$lang['common_select_country'] = 'Select Country';
$lang['common_po_number'] = 'PO Number';

$lang['common_relationship'] = 'Relationship';

$lang['common_submited'] = 'Submitted';
$lang['common_not_submited'] = 'Not Submitted';
$lang['common_document_is_required'] = 'Document is required';
$lang['common_supplier_invoice_attachments'] = 'Supplier Invoice Attachments';
$lang['common_invoice_date'] = 'Invoice Date';
$lang['common_gl_details'] = 'GL Details';
$lang['common_gl_code_description'] = 'GL Code Description';
$lang['common_gl_total'] = 'GL Total';
$lang['common_electronically_approved_by'] = 'Electronically Approved By';

$lang['common_electronically_approved_date'] = 'Electronically Approved Date';
$lang['common_tax_total'] = 'Tax Total';
$lang['common_debit_note_attachments'] = 'Debit Note Attachments';
$lang['common_remarks'] = 'Remarks';
$lang['common_system_stock'] = ' System Stock';
$lang['common_system_wac'] = 'System WAC';
$lang['common_actual_stock'] = ' Actual Stock';
$lang['common_actual_wac'] = 'Actual WAC';

$lang['common_issue_date'] = 'Issue&nbsp;Date';
$lang['common_expire_date'] = 'Expiry&nbsp;Date';
$lang['common_issued_by'] = 'Issued&nbsp;By';
$lang['common_depreciation'] = 'Depreciation';
$lang['common_balance'] = 'Balance';
$lang['common_industry_type'] = 'Industry Type';
$lang['common_emp_language_chane'] = 'You want to Change the language';
$lang['common_statement'] = 'Statement';
$lang['common_template'] = 'Template';
$lang['common_period'] = 'Period';
$lang['you_want_to_change_payslip_visible_date'] = 'You want to change payslip visible date?';
$lang['common_add_bulk_details'] = 'Add Bulk Detail';
$lang['common_proceed'] = 'Proceed';
$lang['common_employee_details'] = 'Employee Details';
$lang['common_employee_contribution'] = 'Employee Contribution';
$lang['common_employer_contribution'] = 'Employer Contribution';
$lang['common_expense_gl_code'] = 'Expense GL Code';
$lang['common_expense'] = 'Expense';
$lang['common_liability_gl_code'] = 'Liability GL Code';

$lang['common_bank_transfer'] = 'Bank Transfer';
$lang['common_employee_bank'] = 'Employee Bank';
$lang['common_transfer_date'] = 'Transfer Date';
$lang['common_payment_type'] = 'Payment Type';
$lang['common_payee_only'] = 'Payee Only';
$lang['common_bank_transfer_details'] = 'Bank Transfer Details';
$lang['common_cheque_details'] = 'Cheque Details';
$lang['common_bank_transfer_details'] = 'Bank Transfer Details';
$lang['common_account_review'] = 'Account Review';
$lang['common_double_entry'] = 'Double Entry';

$lang['common_from_date'] = 'From&nbsp;Date';
$lang['common_to_date'] = 'To&nbsp;Date';

$lang['common_emp_no'] = 'EMP No';
$lang['common_discharge_date'] = 'Discharged Date';
$lang['common_discharge_date'] = 'Discharged Date';
$lang['common_service'] = 'Service';
$lang['common_years'] = 'Years';
$lang['common_months'] = 'Months';
$lang['common_days'] = 'Days';
$lang['common_contract_type'] = 'Contract Type';

$lang['common_emp_first_name'] = 'First Name';
$lang['common_emp_second_name'] = 'Second Name';
$lang['common_emp_third_name'] = 'Third Name';
$lang['common_emp_fourth_name'] = 'Fourth Name';
$lang['common_emp_family_name'] = 'Family Name';

$lang['common_first_name_is_required'] = 'First Name is required';
$lang['common_second_name_is_required'] = 'Second Name is required';
$lang['common_third_name_is_required'] = 'Third Name is required';
$lang['common_family_name_is_required'] = 'Family Name is required';
$lang['emp_secondary_code_is_required'] = 'Secondary Code is required';

$lang['common_insurance_no'] = 'Insurance No';
$lang['common_emergency_contact_details'] = 'Emergency Contact Details';
$lang['common_work_contact_details'] = 'Work Contact Details';
$lang['common_contact_person'] = 'Contact Person';
$lang['common_customer_weburl'] = 'Customer Website';
$lang['common_payment_terms'] = 'Payment Terms';
$lang['common_add_emergency_contact'] = 'Add Emergency Contact';
$lang['common_contact_number'] = 'Contact Number';
$lang['common_primary'] = 'Primary';
$lang['common_other'] = 'Other';
$lang['common_are_you_sure_you_want_to_make_this_as_default'] = 'You want to make this record as default!';
$lang['common_office_no'] = 'Office No';
$lang['common_ext'] = 'Ext.';
$lang['common_land_line'] = 'Land Line';
$lang['common_travel_frequency'] = 'Travel Frequency';
$lang['common_add_travel_frequency'] = 'Add Travel Frequency';
$lang['common_insurance_details'] = 'Insurance Details';
$lang['common_visa_details'] = 'Visa Details';
$lang['common_passport_details'] = 'Passport Details';
$lang['common_not_active'] = 'Not Active';
$lang['common_history'] = 'History';
$lang['common_document_upload'] = 'Document Upload';
$lang['common_probation_period'] = 'Probation Period';
$lang['common_is_open_contract'] = 'Is Open Contract';
$lang['common_edit_employment_type'] = 'Edit Employment Type';
$lang['common_contract_period'] = 'Contract Period';
$lang['common_adjustment_type'] = 'Adjustment Type';
$lang['common_documents_no'] = 'Document No';
$lang['common_ctccost'] = 'Cost';
$lang['common_isCTC'] = 'is Cost to Company';
$lang['common_other_details'] = 'Other Details';
$lang['common_header'] = 'Header';
$lang['common_no_of_unit'] = 'No Of Unit';
$lang['common_variable_pay_declarations'] = 'Variable Pay Declarations';
$lang['common_variable_pay_declarations_history'] = 'Variable Pay Declarations - History';
$lang['common_effective_date'] = 'Effective Date';
$lang['common_medical_info'] = 'Medical Info';

$lang['common_invoiced_return'] = 'Invoiced / Returned';
$lang['common_order_total'] = 'Order Total';
$lang['common_due'] = 'Due';
$lang['common_paid'] = 'Paid';
$lang['common_un_billed_invoice'] = 'Un billed Invoice';
$lang['common_do_value'] = 'DO Value';
$lang['common_arabic'] = 'Arabic';

$lang['common_basic_gross'] = 'Basic / Gross';
$lang['common_annual_leave'] = 'Annual Leave';
$lang['common_no_of_working_days'] = 'No of working days in the month';
$lang['common_leave_pay_formula'] = '<b>Formula :</b> Leave balance * (Basic or Gross / No Of Working Days In The Month)';
$lang['common_leave_balance'] = 'Leave balance';
$lang['common_select_location'] = 'Select a Location';
$lang['common_leave_days'] = 'No of leave days in the month';

$lang['common_grade'] = 'Grade';
$lang['common_no_of_years'] = 'No Of Years';
$lang['common_fixed_gross_salary'] = 'Basic Pay';
$lang['common_reporting_currency'] = 'Reporting Currency';
$lang['common_local_currency'] = 'Local Currency';

$lang['common_salary_advance_request'] = 'Salary Advance Request';
$lang['common_salary_advance_request_form'] = 'Advance Request Form';
$lang['common_salary_declaration_detail'] = 'Salary Declaration Detail';
$lang['common_salary_advance_request_approval'] = 'Salary Advance Request Approval';
$lang['common_is_salary_advance'] = 'Is Salary Advance';
$lang['common_salary_advance'] = 'Salary Advance';
$lang['common_employer'] = 'Employer';

$lang['common__bank_or_cash'] = 'Bank or Cash';


$lang['common_serial_no'] = 'Serial No';
$lang['common_no_message_found'] = 'No message found';
$lang['common_registration_no'] = 'Registration No';

$lang['common_emp_code'] = 'EMP CODE';
$lang['common_swift_code'] = 'Swift Code';

$lang['common_invoice_no'] = 'Invoice No';
$lang['common_invoice_to'] = 'Invoice To';
$lang['common_invoice_items'] = 'Invoice Items';
$lang['common_invoice_detail'] = 'Invoice Detail';
$lang['common_payment_detail'] = 'Payment Detail';

$lang['common_you_want_to_proceed'] = 'You want to Proceed';

$lang['common_unpaid'] = 'Unpaid';
$lang['common_pending_for_verification'] = 'Pending For Verification';
$lang['common_payment_received_date'] = 'Payment Received Date';


$lang['common_standard'] = 'Standard';
$lang['common_increment'] = 'Increment';

$lang['common_request_letters'] = ' Document Request';
$lang['common_letter_type'] = 'Letters Type';
$lang['common_letter_addressed'] = 'Letter Addressed To';
$lang['common_language'] = 'Language';

$lang['common_no_of_days'] = 'No of Days';
$lang['common_expired'] = 'Expired';
$lang['common_dependents'] = 'Dependents';
$lang['common_completed'] = 'Completed';
$lang['common_not_completed'] = 'Not Completed';

$lang['common_assign_date'] = 'Assign Date';
$lang['common_approve'] = 'Approve';

$lang['common_identity'] = 'Identity';
$lang['common_identity_no'] = 'Identity No';
$lang['common_signature'] = 'Signature';
$lang['common_signature_is_required'] = 'Signature is required';
$lang['common_device_id'] = 'Device ID';
$lang['common_machine_configuration'] = 'Machine configuration';
$lang['common_payroll_group'] = 'Payroll Group';

$lang['common_location_and_date'] = 'Location and date';
$lang['common_location_and_employee'] = 'Location and Employee';

$lang['common_select'] = 'Select';
$lang['common_usage_hours'] = 'Usage Hours';
$lang['common_save_and_complete'] = 'Save & Complete';
$lang['common_start_time'] = 'Start Time';
$lang['common_end_time'] = 'End Time';
$lang['common_hours_spent'] = 'Hours Spent';

$lang['common_customer_category'] = 'Customer Category';
$lang['common_document_types'] = 'Document Types';
//$lang['common_document_code'] = 'Document Code';
//$lang['common_document_ty'] = 'Document Date';
$lang['common_date_from'] = 'Date From';
$lang['common_date_to'] = 'Date To';
$lang['common_financial_year'] = 'Financial Year';

$lang['common_group_by'] = 'Group By';
$lang['common_area'] = 'Area';
$lang['common_type_as'] = 'Type As';
$lang['common_sub_area'] = 'Sub Area';
$lang['common_close'] = 'Close';

$lang['common_document_header'] = 'DOCUMENT HEADER';
$lang['common_you_want_to_deactivate_this_price'] = 'You want to Deactivate this Price!';

$lang['common_you_want_to_apply_this_to_all'] = 'You want to apply this to all';
$lang['common_create'] = 'Create';
$lang['common_create_new_document'] = 'Create New Document';
$lang['common_document_tracing'] = 'Document Tracing';
$lang['common_document_edit_all '] = 'Edit All';
$lang['common_add_note'] = 'Add Note';
$lang['common_customer_telephone'] = 'Customer Telephone';
$lang['common_customer_email'] = 'Customer Email';
$lang['common_discount_details'] = 'Discount Details';

$lang['common_mobile_credit_limit'] = 'Mobile Credit Limit';
$lang['common_step_four'] = 'Step 4';
$lang['common_documents'] = 'Documents';
$lang['common_document_upload'] = 'Document upload';
$lang['common_discount_amount'] = 'Discount Amount';
$lang['common_discount_percentagae'] = 'Discount Percentage';
$lang['common_discount_total'] = 'Discount Total';
$lang['common_driver_name'] = 'Driver Name';
$lang['common_vehicle_no'] = 'Vehicle No';
$lang['common_invoice_code'] = 'Invoice Code';
$lang['common_payment_code'] = 'Payment Code';
$lang['common_payment_date'] = 'Payment Date';
$lang['common_requested_date'] = 'Requested Date';
$lang['common_requested_by'] = 'Requested By';
$lang['common_requested_qty'] = 'Requested QTY';
$lang['common_document_tracing'] = 'Document Tracing';

$lang['common_request_confirmation'] = 'Request Confirmation';
$lang['common_request_header'] = 'Request Header';
$lang['common_request_detail'] = 'Request Detail';
$lang['common_balance_qty'] = 'Balance Qty';
$lang['common_previous_year'] = 'Previous Year';
$lang['common_created_by'] = 'Created By';
$lang['common_created_date'] = 'Created Date';
$lang['common_cap_amount'] = 'Cap Amount';
$lang['common_bulk_upload'] = 'Bulk Upload';
$lang['common_add_attributes'] = 'Add Attibutes';
$lang['common_outlets'] = 'Outlets';
$lang['common_origin_documnet_code'] = 'Origin Document Code';
$lang['common_filter_by_location'] = 'Filter By Location';
$lang['common_filter_by_segment'] = 'Filter By Segment';
$lang['common_item_category'] = 'Item Category';
$lang['common_summary'] = 'Summary';
$lang['common_payee'] = 'Payee';
$lang['common_download'] = 'Download';
$lang['common_confrim_and_submit'] = 'Confirm & Submit';
$lang['common_sub_total'] = 'Sub Total';
$lang['common_compose'] = 'Compose';
$lang['common_back'] = 'Back';
$lang['common_folders'] = 'Folders';
$lang['common_read_mail'] = 'Read Mail';
$lang['common_reply'] = 'Reply';
$lang['common_forward'] = 'Forward';
$lang['common_sent'] = 'sent';
$lang['common_mail_box_configuration'] = 'Mail Box Configuration';
$lang['common_account_type'] = 'Account Type';
$lang['common_email_encryption'] = 'Email Encryption';
$lang['common_host'] = 'Host';
$lang['common_compose_new_message'] = 'Compose New Message';
$lang['common_Mail_box'] = 'Mailbox';
$lang['common_message'] = 'Message';
$lang['common_account_name'] = 'Account Name';
$lang['common_account_category'] = 'Account Category';
$lang['common_bank_account_no'] = 'Bank Account No';
$lang['common_system_code'] = 'System Code';
$lang['common_item_master'] = 'Item Master';
$lang['common_replicate'] = 'Replicate';
$lang['common_item_replicate'] = 'Item Replicate';
$lang['common_item_name'] = 'Item Name';
$lang['common_create_category'] = 'Create Category';
$lang['common_category'] = 'Category';
$lang['common_task'] = 'Task';
$lang['common_income'] = 'Income';
$lang['common_expence'] = 'Expense';
$lang['common_essets'] = 'Assets';
$lang['common_liability'] = 'Liability';
$lang['common_is_default'] = 'Is Default';
$lang['common_location_code'] = 'Location Code';
$lang['common_un_confirm'] = 'Un Confirm';
$lang['common_sub_type'] = 'Sub Type';
$lang['common_to_excel'] = 'To Excel';
$lang['common_QHSE_login'] = 'QHSE Login';
$lang['common_you_want_to_proceed_with'] = 'You want to Proceed With ';

$lang['common_suom'] = 'SUOM';
$lang['common_secondary_qty'] = 'Secondary Qty';

$lang['common_excel'] = 'Excel';

$lang['common_salary_category'] = 'Salary Category';
$lang['common_start_range'] = 'Start Range';
$lang['common_end_range'] = 'End Range';

$lang['common_make_this_primary'] = 'You want to make this record as primary!';

$lang['common_notify_to'] = 'Notify to';
$lang['common_reset'] = 'Reset';
$lang['common_no_of_columns'] = 'Number of Columns';
$lang['common_marks'] = 'Marks';
$lang['common_grades'] = 'Grades';
$lang['common_objective'] = 'Objective';
$lang['common_objectives'] = 'Objectives';
$lang['common_goal_objectives'] = 'Goal Objectives';
$lang['common_closed_by'] = 'Closed By';
$lang['common_closed_date'] = 'Closed Date';
//$lang['common_weight'] = 'Weight';
$lang['common_last_updated'] = 'Last Updated';
$lang['common_comment_is_required'] = 'Comment is required';
$lang['common_remove'] = 'Remove';
$lang['common_narration_is_required'] = 'Narration is required';
$lang['common_date_is_invalid'] = 'Date is invalid';
$lang['common_open'] = 'Open';
$lang['common_saved_as_draft'] = 'Saved as draft';
$lang['common_rejected'] = 'Rejected';
$lang['common_referred_back'] = 'Referred back';
$lang['common_add_task'] = 'Add Task';

$lang['common_supplier_address'] = 'Supplier Address';
$lang['common_supplier_telephone'] = 'Supplier Telephone';
$lang['common_authorized_signature'] = 'Authorized Signature';
$lang['common_unit_rate'] = 'Unit Rate';
$lang['common_approved_details'] = 'Approved Details';
$lang['common_closed_details'] = 'Closed Details';
$lang['common_document_not_closed_yet'] = 'Document not closed yet';
$lang['common_closed_user'] = 'Closed User';
$lang['common_contact_person_is_required'] = 'Contact Person is required';
$lang['common_supplier_code'] = 'Supplier Code';
$lang['common_item_image'] = 'Item Image';
$lang['common_total_amount'] = 'Total Amount';
$lang['common_project_category'] = 'Project Category';
$lang['common_project_subcategory'] = 'Project Subcategory';
$lang['common_invoice_due_date'] = 'Invoice Due Date';
$lang['common_item_status'] = 'Item Status';
$lang['common_unit_price'] = 'Unit Price';
$lang['common_net_unit_price'] = 'Net Unit Price';
$lang['common_not_applicable'] = 'Not Applicable';
$lang['common_remarks'] = 'Remarks';
$lang['common_net_total'] = 'Net Total';

$lang['common_location'] = 'Location';

$lang['common_customer_systemcode'] = 'Customer Code';
$lang['common_secondary_code'] = 'Secondary Code';
$lang['common_referenceNo'] = 'Reference';


//Newly Added
$lang['common_templates'] = 'Templates';

$lang['common_select_provision_gl'] = 'Select Provision GL';
$lang['common_select_gl'] = 'Select GL';
$lang['common_provision_gl'] = 'Provision GL : ';
$lang['common_salary_categories'] = 'Salary Categories';
$lang['common_salary_category'] = 'Salary Category';
$lang['common_leave_salary_provision_cinfiguration'] = 'Leave Salary Provision Configiration';

$lang['common_create_amendment'] = 'Create Amendment';
$lang['common_close_amendment'] = 'Close Amendment';

//inventory catalogue
$lang['common_inventory_catelogue'] = 'Inventory Catalogue';
$lang['common_create_inventory_catlogue'] = 'Create Inventory Catalogue';
$lang['common_add_new_inventory_catalogue'] = 'Create Inventory Catalogue';

$lang['common_inventory_catelogue'] = 'Inventory Catalogue';
$lang['common_create_inventory_catlogue'] = 'Create Inventory Catalogue';
$lang['common_add_new_inventory_catalogue'] = 'Create Inventory Catalogue';

$lang['common_travel_request'] = 'Travel/Trip Request';
$lang['common_travel_request_header'] = 'Travel/Trip Request Header';
$lang['common_travel_request_approval'] = 'Travel/Trip Request Approval';
$lang['common_travel_request_details'] = 'Travel/Trip Request Details';
$lang['common_travel_request_confirmation'] = 'Travel/Trip Request Confirmation';
$lang['common_travel_request_attachments'] = 'Travel/Trip Request Attachments';
$lang['common_travel_request_number'] = 'Travel/Trip Request Number';
$lang['common_travel_request_Date'] = 'Travel/Trip Request Date';
$lang['common_add_travel_request_details'] = 'Add Travel/Trip Request Details';
$lang['common_edit_travel_request_details'] = 'Edit Travel/Trip Request Details';
$lang['common_create_travel-request'] = ' Create Travel/Trip Request';
$lang['common_edit_travel_request'] = ' Edit Travel/Trip Request';
$lang['common_trip_type'] = ' Select a trip type';
$lang['common_Employeee'] = 'Employee Name';
$lang['common_Employeee_secondary_code'] = 'Secondary Code';
$lang['common_reporting_manager'] = 'Reporting Manager';
$lang['commom_trip_type'] = 'Trip Type';
$lang['commom_subject'] = 'Subject';
$lang['commom_description'] = 'Description';
$lang['commom_trip_start_date'] = 'Trip Start Date';
$lang['commom_trip_end_date'] = 'Trip end Date';
$lang['commom_trip_country'] = 'Trip Country';
$lang['commom_Airport_City'] = 'Airport City';
$lang['commom_reason'] = 'Reason for Trip';
$lang['commom_currency'] = 'Currency';
$lang['commom_travel_advance'] = 'Amount';
$lang['commom_destination'] = 'Destination';
$lang['commom_employee_code'] = 'Employee Code';
$lang['commom_trip'] = 'Trip Type';
$lang['commom_Airport'] = 'Airport';


$lang['common_retension'] = 'Retention';
$lang['common_commission'] = 'Commission';
$lang['common_taxapplicable'] = 'Tax Applicable';


$lang['common_stage'] = 'Stages';
$lang['common_DefaultType'] = 'DefaultType';


$lang['common_you_want_to_pull_extra_charges'] = 'You want to pull default extra charges !';

$lang['common_DefaultType'] = 'DefaultType';
$lang['common_weightage'] = 'Weight Age';
$lang['common_checklist'] = 'Check List';
$lang['common_checklist_description'] = 'Check List Description';
$lang['common_actual_hours_spent'] = 'Actual Hours Spent';

$lang['common_link_customer'] = 'Linked Customer';


//start : Ray company Document policy (newly added)
if ($ray_Company_document_policy == 1)
{
    $lang['common_supplier_document_date'] = 'Apply Date';
    $lang['common_supplier_invoice_date'] = 'Supplier Invoice Date';
}
else
{
    $lang['common_supplier_document_date'] = 'Document Date';
    $lang['common_supplier_invoice_date'] = 'Invoice Date';
}
//end ; 

$lang['common_is_inter_company'] = 'Is Inter Company';
$lang['common_inter_company'] = 'Inter Company';
// Travel Request Approval
$lang['common_travel_Request_Approval'] = 'Travel/Trip Request Approval';
$lang['common_travel_Request_status_required'] = 'Status is required.';
$lang['common_travel_Request_order_status_is_required'] = 'Level Order Status is required.';
$lang['common_travel_Request_id_is_required'] = 'Travel/Trip Request ID is required.';
$lang['common_travel_request'] = 'Travel Request';
$lang['common_travel_request_approvals_reject_process_successfully_done'] = 'Approvals  Reject Process Successfully done';
$lang['common_travel_request_previous_level_approval_not_finished'] = 'Previous Level Approval Not Finished';
$lang['common_travel_request_error_in_paysheet_approvals_of'] = 'Error in Travel/Trip Request Approvals Of';

$lang['common_you_want_to_generate'] = 'You want to generate!';
$lang['commom_local_mobile'] = 'Local Mobile No';
$lang['commom_seat_preference'] = 'Seat Prefernce';
$lang['commom_meal_preference'] = 'Meal Prefernce';
$lang['commom_flyer_no_if_any'] = 'Frequent Flyer No. If any';
$lang['common_family_name'] = 'Family Name';
$lang['common_emirates_details'] = 'Emirates Details';
$lang['common_emirates_no'] = 'Emirates ID';
$lang['emp_emirate_expiry_date'] = 'Emirates Expiry Date';
$lang['common_Accrual'] = 'Accrual JV ';
$lang['common_reporting_changes'] = 'Reporting Manger Changes ';
$lang['common_is_primary'] = 'Is Primary';
$lang['common_department_changes'] = 'Department Changes ';
$lang['common_bank_changes'] = 'Bank Detail Changes ';
$lang['common_air_ticket_enhancemnet'] = 'Air Ticket Encashment';

$lang['common_Loan'] = 'Loan';
$lang['common__pending_Loan'] = 'Pending Loans';
$lang['Loan_No'] = 'Loan No';
$lang['common_loan_amount'] = 'Loan Amount';
$lang['common_total_intallmanets'] = 'Total Inatallments';
$lang['common_pending_intallmanets'] = 'Pending Installments';
$lang['common_loan_details'] = 'Loan Details';

  
// OT Summary
$lang['common_ot_summary'] = 'OT Summary';
$lang['common_ot_summary_day_wise'] = 'OT Summary day wise';
$lang['common_total_hours'] = 'Total Hours';
$lang['common_ot_type'] = 'OT Type';
$lang['common_normal_ot'] = 'Normal Day OT';
$lang['common_weekend_ot'] = 'Weekend OT';
$lang['common_holiday_ot'] = 'Holiday OT';


$lang['common_master_segment'] = 'Master Segment';
$lang['common__pending_Loan_detail'] = 'Pending Loan Details';

$lang['common_interCompnay'] = 'Inter Company';
$lang['common_reserved'] = 'Reserved ';
$lang['common_previous_month'] = 'Previous Month';
$lang['common_previous_reporting_currency'] = 'Previous Month Reporting Currency';
$lang['common_previous_local_currency'] = 'Previous Month Local Currency';
$lang['common_open_leave'] = 'Open Leave';

$lang['common_purchase_history'] = 'Purchase History';
$lang['common_doc_type'] = 'Doc Type';
$lang['common_doc_number'] = 'Doc Number';
$lang['common_transection_qty'] = 'Transection Qty';
$lang['common_transection_currency'] = 'Transection Amount';

$lang['common_accomodation'] = 'Accomodation';
$lang['common_accomodation_type'] = 'Accomodation Type';
$lang['common_accomodation_add'] = 'Add Accomodation';
$lang['common_add_accomodation_to_employee'] = 'Assign Accomadation';

$lang['common_link_leave'] = 'Link Leave';
$lang['common_trip_request_type'] = 'Request Type';
$lang['common_project_type'] = 'Project Type';
$lang['common_project'] = 'Project';
$lang['commom_class_type'] = 'Class Type';
$lang['commom_economic_class'] = 'Economic Class';
$lang['commom_business_lass'] = 'Business Class';
$lang['commom_birthdate'] = 'Birth Date';
$lang['commom_from_destination'] = 'From Destination';
$lang['commom_to_destination'] = 'To Destination';
$lang['common_get_travel_request'] = 'Get Travel Request';
$lang['common_add_travel_type'] = 'Add Travel Type';
$lang['common_travel_type_master'] = 'Travel Type Master';
$lang['common_travel_type'] = 'Travel Type';
$lang['common_mid_range'] = 'Mid Range';
$lang['common_max_val'] = 'Maximum Value';
$lang['common_special_user'] = 'Special User';
$lang['common_add_special_User'] = 'Add Special User';
$lang['common_Div-Country_Initials_S.No_Year']='(Div-Country / Initials /S.No./Year)';
$lang['common_newbooking']='NEW BOOKING';
$lang['common_OTHERS_pls']='OTHERS (pls.state)';
$lang['common_office_staff']='FOR OFFICE STAFF';
$lang['common_filed_staff']='FOR FIELD STAFF';
$lang['common_focal_person']='Focal Person';
$lang['common_traveller_information']='TRAVELLER INFORMATION (As it appears on passport)';
$lang['common_middle_name']='Middel Name';
$lang['common_last_name']='Last Name';
$lang['common_purpose_of_travel']='Purpose of Travel';
$lang['common_code_div_territory']="Code Div./ Territory";
$lang['common_expense_code']="Expense Code";
$lang['common_project_code']="Project Code";
$lang['common_age']="Age";
$lang['common_type_of_travel']="Type Of Travel";
$lang['common_one_way']="One Way";
$lang['common_return']="Return";
$lang['common_class']="Class";
$lang['common_economy']="Economy";
$lang['common_business']="Business";
$lang['common_business_trip_approved']="Business Trip Approved by CEO ";
$lang['common_Na']='N/A';
$lang['common_approval_Type']='Approval Type';
$lang['common_leave_approve']="Leave Approved";
$lang['common_trip_approve']="Trip Approved";
$lang['common_family_travel']="Family Travel";
$lang['common_itinerary_required']="TRAVEL ITINERARY REQUIRED";
$lang['common_departure_date']="Departure Date";
$lang['common_sector']="Sector";
$lang['common_return_date']="Return Date";
$lang['common_overseas_mob']="Overseas Mob No";
$lang['common_do_you_have_travel_visa']="Do you have Travel Visa";
$lang['commom_LPO_to_be_filled']="LPO - TO BE FILLED BY FOCAL POINT";
$lang['commom_LPO_no']="LPO No. (Travel Agent abbrevation / TRF No.)";
$lang['commom_airline']="Airline";
$lang['common_travel_date']="Travel Date";
$lang['common_ticket_no']="Ticket No";
$lang['common_base_fare']="Base Fare";
$lang['common_taxes']="Taxes";
$lang['common_currency_total']="Currency/Total";
$lang['common_to_be_filled_by_MSE']="To be filled by MSE Travel Focal Point, if any";
$lang['common_total_in_words']="Total in words";
$lang['common_add_more_details']="Add More Details";
$lang['common_depature_details']="Departure Details";
$lang['common_return_details']="Return Details";
$lang['common_do_you_have_visa']="Do you have visa";
$lang['common_booking_type']="Booking Type";
$lang['common_new_booking']="New Booking";
$lang['common_others_pls']="OTHERS (pls.state)";
$lang['common_office_staff_simple']='For Office Staff';
$lang['common_expiry_date']='Expiry date';
$lang['common_item_details'] = 'Item details';
$lang['common_add_item_details'] = 'Add Item details';
$lang['common_item_code'] = 'Item Code';