<?php

/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix : assetmanagement_
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$LanguagePolicy = getPolicyValues('LNG', 'All');

$lang['manufacturing_select_image'] = 'Select Image';
$lang['manufacturing_change'] = 'Change';
$lang['manufacturing_remove'] = 'Remove';
$lang['manufacturing_new_overhead'] = 'New Over Head';
$lang['manufacturing_add_crew'] = 'Add Crew';
$lang['manufacturing_add_customer'] = 'Add Customer';
$lang['manufacturing_add_segment'] = 'Add Segment';
$lang['manufacturing_excel'] = 'Excel';
$lang['manufacturing_add_new_status'] = 'Add New Status';
$lang['manufacturing_add_new_user'] = 'Add New User';
$lang['manufacturing_new_labour'] = 'New Labour';
$lang['manufacturing_add_labour'] = 'Add Labour';
$lang['manufacturing_add_warehouse'] = 'Add Warehouse';
$lang['manufacturing_save_warehouse'] = 'Save Warehouse';
$lang['manufacturing_add_user_group'] = 'Add User Group';
$lang['manufacturing_edit_user_group'] = 'Edit User Group';
$lang['manufacturing_add_employee'] = 'Add Employee';
$lang['manufacturing_add_standard_details'] = 'Add Standard Details';
$lang['manufacturing_edit_standard_details'] = 'Edit Standard Details';
$lang['manufacturing_send_email'] = 'Send Email';
$lang['manufacturing_add_machine'] = 'Add Machine';
$lang['manufacturing_rfi_detial'] = 'RFI Details';

$lang['manufacturing_job_no'] = 'Job No';
$lang['manufacturing_job_number'] = 'Job Number';
$lang['manufacturing_job_id'] = 'Job ID';
$lang['manufacturing_job_card'] = 'Job Card';
$lang['manufacturing_job_approval'] = 'Job Approval';
$lang['manufacturing_job_date'] = 'Job Date';
$lang['manufacturing_jobs'] = 'Jobs';
$lang['manufacturing_job'] = 'Job';
$lang['manufacturing_generate_job'] = 'Generate Job';
$lang['manufacturing_job_attachment'] = 'Job Attachment';
$lang['manufacturing_document_date'] = 'Document Date';
$lang['manufacturing_customer'] = 'Customer';
$lang['manufacturing_item'] = 'Item';
$lang['manufacturing_description'] = 'Description';
$lang['manufacturing_job_status'] = 'Job Status';
$lang['manufacturing_status'] = 'Status';
$lang['manufacturing_percentage'] = 'Percentage';

$lang['manufacturing_new_bill_of_material'] = 'New Bill of Material';
$lang['manufacturing_bom_code'] = 'BOM Code';
$lang['manufacturing_product_name'] = 'Product Name';
$lang['manufacturing_industry_type'] = 'Industry Type';

$lang['manufacturing_bom_information'] = 'BOM Information';
$lang['manufacturing_product'] = 'Product';
$lang['manufacturing_date'] = 'Date';
$lang['manufacturing_unit_of_measure'] = 'Unit of measure';
$lang['manufacturing_quantity'] = 'Qty';
$lang['manufacturing_currency'] = 'Currency';

$lang['manufacturing_material_consumption'] = 'Material Consumption';
$lang['manufacturing_part_no'] = 'Part No';
$lang['manufacturing_unit_of_measure_short'] = 'UoM';
$lang['manufacturing_quantity_required'] = 'Qty Required';
$lang['manufacturing_cost_type'] = 'Cost Type';
$lang['manufacturing_material_cost'] = 'Material Cost';
$lang['manufacturing_standard_loss'] = 'Standard Loss %';
$lang['manufacturing_material_change'] = 'Material Charge';
$lang['manufacturing_material_totals'] = 'Material Totals';

$lang['manufacturing_labour_tasks'] = 'Labour Tasks';
$lang['manufacturing_activity_code'] = 'Activity Code';
$lang['manufacturing_department'] = 'Department';
$lang['manufacturing_unit_rate'] = 'Unit Rate';
if($LanguagePolicy == 'FlowServe'){
    $lang['manufacturing_total_hours'] = 'Estimated Hours';
}else{
    $lang['manufacturing_total_hours'] = 'Total Hours';
}

$lang['manufacturing_total_value'] = 'Total Value';
$lang['manufacturing_labour_totals'] = 'Labour Totals';

$lang['manufacturing_overhead_cost'] = 'Overhead Cost';
$lang['manufacturing_overhead_totals'] = 'Overhead Totals';
$lang['manufacturing_machine'] = 'Machine';
$lang['manufacturing_machine_id'] = 'Machine ID';
$lang['manufacturing_machine_totals'] = 'Machine Totals';
$lang['manufacturing_total_cost'] = 'Total Cost:';
$lang['manufacturing_cost_per_unit'] = 'Cost per unit:';
$lang['manufacturing_quantity_used'] = 'Qty Used';

$lang['manufacturing_item_information'] = 'Item Information';
$lang['manufacturing_finance_category'] = 'Finance Category';
$lang['manufacturing_sub_category'] = 'Sub Category';
$lang['manufacturing_sub_sub_category'] = 'Sub Sub Category';
$lang['manufacturing_secondary_code'] = 'Secondary Code';
$lang['manufacturing_industry'] = 'Industry';

$lang['manufacturing_estimated_qty'] = 'Estimated Qty';
$lang['manufacturing_usage_qty'] = 'Usage Qty';
$lang['manufacturing_loss'] = 'Loss%';
$lang['manufacturing_mark_up'] = 'Mark Up%';

$lang['manufacturing_add_overhead'] = 'Add Over Head';
$lang['manufacturing_workflow_category'] = 'Workflow Category';
$lang['manufacturing_page_link'] = 'Page Link';
$lang['manufacturing_workflow_template'] = 'Workflow Template';

$lang['manufacturing_linked_to_erp'] = 'Linked To ERP';
$lang['manufacturing_crews_from_erp'] = 'Crews From ERP';
$lang['manufacturing_crew_detail'] = 'Crew Detail';
$lang['manufacturing_customer_detail'] = 'Customer Detail';
$lang['manufacturing_contact_detail'] = 'Contact Detail';
$lang['manufacturing_item_name'] = 'Item name';
$lang['manufacturing_item_code'] = 'Item Code';
$lang['manufacturing_categories'] = 'Categories';
$lang['manufacturing_main'] = 'Main';
$lang['manufacturing_sub'] = 'Sub';
$lang['manufacturing_sub_sub'] = 'Sub Sub';

$lang['manufacturing_segment_ID'] = 'Segment ID';
$lang['manufacturing_segment_description'] = 'Segment Description';
$lang['manufacturing_segment_linked_to_erp'] = 'Segment Linked to ERP';
$lang['manufacturing_segment_detail'] = 'Segment Detail';
$lang['manufacturing_segment_code'] = 'Segment Code';

$lang['manufacturing_select_a_work_flow'] = 'Select a Work Flow';
$lang['manufacturing_main_category'] = 'Main Category';
$lang['manufacturing_select_category'] = 'Select Category';
$lang['manufacturing_select_item_type'] = 'Select Item Type';
$lang['manufacturing_select_an_industry'] = 'Select an Industry';
$lang['manufacturing_select_bom'] = 'Select BOM';
$lang['manufacturing_select_order'] = 'Select Order';

$lang['manufacturing_step_1'] = 'Step 1 - ';
$lang['manufacturing_step_2'] = 'Step 2 - ';
$lang['manufacturing_is_default'] = 'Is Default';
                           /*TITLES*/
$lang['manufacturing_customer_master'] = 'CUSTOMER MASTER';
$lang['manufacturing_crew_master'] = 'CREW MASTER';
$lang['manufacturing_segments'] = 'SEGMENTS';
$lang['manufacturing_workflow_process_setup'] = 'WORKFLOW PROCESS SETUP';
$lang['manufacturing_system_settings'] = 'SYSTEM SETTINGS';
$lang['manufacturing_item_category'] = 'MFQ ITEM CATEGORY';
$lang['manufacturing_labour'] = 'Labour';
$lang['manufacturing_warehouse'] = 'WAREHOUSE';
$lang['manufacturing_user_groups'] = 'USER GROUPS';
$lang['manufacturing_estimate'] = 'ESTIMATE';
$lang['manufacturing_customer_inquiry'] = 'CUSTOMER INQUIRY';
$lang['manufacturing_manage_warehouse'] = 'Manage Warehouse';
$lang['manufacturing_manage_segment'] = 'Manage Segment';
$lang['manufacturing_workflow_header'] = 'Workflow Header';
$lang['manufacturing_workflow_configuration'] = 'Workflow Configuration';
$lang['manufacturing_workflow_detail'] = 'Workflow Detail';
$lang['manufacturing_workflow_design'] = 'Workflow Design';
$lang['manufacturing_standard_job_card'] = 'Standard Job Card';
$lang['manufacturing_asset_category'] = 'MFQ Asset Category';

$lang['manufacturing_job'] = 'Job';
$lang['manufacturing_machine'] = 'Machine';
$lang['manufacturing_review'] = 'Review';
$lang['manufacturing_print'] = 'Print';
$lang['manufacturing_review_or_print'] = 'Review / Print';
$lang['manufacturing_header'] = 'Header';
$lang['manufacturing_card'] = 'Card';
$lang['manufacturing_bom'] = 'BOM';
$lang['manufacturing_quotation_reference_id'] = 'Quotation Reference ID';
$lang['manufacturing_quality_assurance'] = 'QA';
$lang['manufacturing_quality_assurance_criteria'] = 'Quality Assurance Criteria';
$lang['manufacturing_specification'] = 'Specification';

$lang['manufacturing_quote_reference'] = 'Quote Ref.';
$lang['manufacturing_item_detail'] = 'Item Detail';
$lang['manufacturing_item_code'] = 'Item Code';
$lang['manufacturing_machine_category'] = 'Machine Cat';
$lang['manufacturing_asset_code'] = 'Asset Code';

$lang['manufacturing_dashboard'] = 'Dashboard';
$lang['manufacturing_production_calendar'] = 'Production Calendar';
$lang['manufacturing_ongoing_job'] = 'Ongoing Job';
$lang['manufacturing_close_date'] = 'Close Date';
$lang['manufacturing_day_scale'] = 'Day Scale';
$lang['manufacturing_week_scale'] = 'Week Scale';
$lang['manufacturing_month_scale'] = 'Month Scale';
$lang['manufacturing_division'] = 'Division';
$lang['manufacturing_job_description'] = 'Job Description';
$lang['manufacturing_client_name'] = 'Client Name';
$lang['manufacturing_job_completion'] = 'Job Completion';
$lang['manufacturing_document_status'] = 'Document Status';
$lang['manufacturing_background_color'] = 'Background Color';
$lang['manufacturing_text_color'] = 'Text Color';
$lang['manufacturing_status_color'] = 'Status Color';
$lang['manufacturing_finance_date'] = 'Finance Date';

$lang['manufacturing_gl_description'] = 'GL Description';
$lang['manufacturing_link_description'] = 'Link Description';
$lang['manufacturing_warehouse_from_erp'] = 'Warehouse From ERP';
$lang['manufacturing_warehouse_detail'] = 'Warehouse Detail';

$lang['manufacturing_group_type'] = 'Group Type';
$lang['manufacturing_is_active'] = 'Is Active';
$lang['manufacturing_is_default'] = 'Is Default';
$lang['manufacturing_employee'] = 'Employee';
$lang['manufacturing_employee_name'] = 'Employee Name';
$lang['manufacturing_added_employee'] = 'Added Employee';
$lang['manufacturing_standard_details'] = 'Standard Details';
$lang['manufacturing_stock_insufficient'] = 'Stock Insufficient';
$lang['manufacturing_current_stock'] = 'Current Stock';

$lang['manufacturing_customer_inquiry_approval'] = 'Customer Inquiry Approval';
$lang['manufacturing_customer_inquiry_attachment'] = 'Customer Inquiry Attachment';
$lang['manufacturing_estimate_approval'] = 'Estimate Approval';
$lang['manufacturing_estimate_attachment'] = 'Estimate Attachment';
$lang['manufacturing_standard_job_card_approval'] = 'Standard Job Card Approval';
$lang['manufacturing_standard_job_card_attachment'] = 'Standard Job Card Attachment';

$lang['manufacturing_batch_number'] = 'Batch Number';
$lang['manufacturing_production_date'] = 'Production Date';
$lang['manufacturing_created_date'] = 'Created Date';
$lang['manufacturing_input'] = 'Input';
$lang['manufacturing_raw_material'] = 'Raw Material';
$lang['manufacturing_total_amount'] = 'Total Amount';
$lang['manufacturing_overhead'] = 'Overhead';
$lang['manufacturing_total_input'] = 'Total Input :';
$lang['manufacturing_output'] = 'Output';
$lang['manufacturing_finish_goods'] = 'Finish Goods';
$lang['manufacturing_total_output'] = 'Total Output';

$lang['manufacturing_new_customer_inquiry'] = 'New Customer Inquiry';
$lang['manufacturing_inquiry_date'] = 'Inquiry Date';
$lang['manufacturing_client'] = 'Client';
$lang['manufacturing_proposal_engineer'] = 'Proposal Engineer';
$lang['manufacturing_client_ref_no'] = 'Client Ref No';
$lang['manufacturing_actual_submission_date'] = 'Actual Submission Date';
$lang['manufacturing_planned_submission_date'] = 'Planned Submission Date';
$lang['manufacturing_inquiry_status'] = 'Inquiry Status';
$lang['manufacturing_quote_status'] = 'Quote Status';
$lang['manufacturing_customer_inquiry_simple'] = 'Customer Inquiry';

$lang['manufacturing_customer_inquiry_information'] = 'Customer Inquiry Information';
$lang['manufacturing_contact_person_name'] = 'Contact Person Name';
$lang['manufacturing_contact_phone_number'] = 'Contact Phone Number';
$lang['manufacturing_type'] = 'Manufacturing Type';
$lang['manufacturing_required_submission_date'] = 'Required Submission Date';
$lang['manufacturing_delay_in_days'] = 'Delay In Days';
$lang['manufacturing_in_days'] = 'In Days';
$lang['manufacturing_send_reminder_email'] = 'Send Reminder Email';
$lang['manufacturing_client_reference_no'] = 'Send Reminder No';
$lang['manufacturing_customer_email'] = 'Customer Email';
$lang['manufacturing_inquiry_type'] = 'Inquiry Type';
$lang['manufacturing_engineering'] = 'Engineering';
$lang['manufacturing_responsible'] = 'Responsible';
$lang['manufacturing_required_date'] = 'Required Date';
$lang['manufacturing_submission_date'] = 'Submission Date';
$lang['manufacturing_purchasing'] = 'Purchasing';
$lang['manufacturing_production'] = 'Production';
$lang['manufacturing_quality_assurance_or_quality_control'] = 'QA/QC';
$lang['manufacturing_expected_quantity'] = 'Expected Qty';
$lang['manufacturing_delivery_date'] = 'Delivery Date';
$lang['manufacturing_delivery_terms'] = 'Delivery Terms';
$lang['manufacturing_remarks'] = 'Remarks';
$lang['manufacturing_inquiry_code'] = 'Inquiry Code';
$lang['manufacturing_client_reference_no'] = 'Client Reference No';
$lang['manufacturing_phone_no'] = 'Phone No';
$lang['manufacturing_validity'] = 'Validity';
$lang['manufacturing_terms_and_condition'] = 'Terms & condition';
$lang['manufacturing_payment_terms'] = 'Payment Terms';
$lang['manufacturing_exclusions'] = 'Exclusions';
$lang['manufacturing_technical_detail'] = 'Technical Detail';
$lang['manufacturing_scope_of_work'] = 'Scope Of Work';
$lang['manufacturing_warranty'] = 'Warranty';
$lang['manufacturing_approval_status'] = 'Approval Status';

$lang['manufacturing_estimation'] = 'Estimation';
$lang['manufacturing_estimate_date'] = 'Estimate Date';
$lang['manufacturing_estimate_information'] = 'Estimate Information';
$lang['manufacturing_estimate_information'] = 'Estimate Header';
$lang['manufacturing_estimate_detail'] = 'Estimate Detail';
$lang['manufacturing_new_estimate'] = 'New Estimate';
$lang['manufacturing_estimate_code'] = 'Estimate Code';
$lang['manufacturing_estimate_status'] = 'Estimate Status';

$lang['manufacturing_tender_SLno'] = 'SL.No';
$lang['manufacturing_tender_tender_no'] = 'Tender No';
$lang['manufacturing_tender_client'] = 'Client';
$lang['manufacturing_tender_description'] = 'Description';
$lang['manufacturing_tender_category'] = 'Category';
$lang['manufacturing_tender_price'] = 'Price';
$lang['manufacturing_tender_rfq_type'] = 'RFQ Type';
$lang['manufacturing_tender_micoda_operation'] = 'Micoda Operation';
$lang['manufacturing_tender_rfq_originator'] = 'RFQ Originator';
$lang['manufacturing_tender_source'] = 'Source';
$lang['manufacturing_tender_Estimator'] = 'Estimator';
$lang['manufacturing_tender_month'] = 'Month';
$lang['manufacturing_tender_year'] = 'Year';
$lang['manufacturing_tender_rfq_status'] = 'RFQ Status';
$lang['manufacturing_tender_status'] = 'Status';
$lang['manufacturing_tender_order_status'] = 'Order Status';
$lang['manufacturing_tender_assigned_date'] = 'Assigned Date';
$lang['manufacturing_tender_submission_date'] = 'Submission Date';
$lang['manufacturing_tender_actual_submission_date'] = 'Actual Submission Date';
$lang['manufacturing_tender_submission_status'] = 'Submission Status';
$lang['manufacturing_tender_alloted_manhours'] = 'Alloted Manhours';
$lang['manufacturing_tender_actual_manhours'] = 'Actual Manhours';
$lang['manufacturing_tender_no_of_days_delayed'] = 'No. of Days Delayed';
$lang['manufacturing_tender_total'] = 'Total';
$lang['manufacturing_tender_rev'] = 'Rev.';
$lang['manufacturing_tender_po_received_date'] = 'PO Received Date';
$lang['manufacturing_tender_po_number'] = 'PO Number';
$lang['manufacturing_tender_project_number'] = 'Project Number';
$lang['manufacturing_tender_remark'] = 'Remarks';

$lang['manufacturing_tender_header'] = 'TENDER LOGS';

$lang['manufacturing_overall_progress_header'] = 'OVERALL PROJECT PROCESS';

$lang['manufacturing_overall_progress_mic_no'] = 'MIC NO';
$lang['manufacturing_overall_progress_tender_no'] = 'Tendor No';
$lang['manufacturing_overall_progress_estimate_no'] = 'Estimate No';
$lang['manufacturing_overall_progress_job_num'] = 'Job Num';
$lang['manufacturing_overall_progress_client'] = 'CLIENT';
$lang['manufacturing_overall_progress_category'] = 'CATEGORY';
$lang['manufacturing_overall_progress_client_po_ref'] = 'CLIENT PO REF. NO';
$lang['manufacturing_overall_progress_project_focal'] = 'PROJECT FOCAL';
$lang['manufacturing_overall_progress_po_value'] = 'PO VALUE';
$lang['manufacturing_overall_progress_delivery'] = 'PO / IJOF DELIVERY';
$lang['manufacturing_overall_progress_committed_date'] = 'COMMITTED COMPLETION DATE';
$lang['manufacturing_overall_progress_actual_date'] = 'ACTUAL COMPLETION DATE';
$lang['manufacturing_overall_progress_month'] = 'MONTH';
$lang['manufacturing_overall_progress_year'] = 'YEAR';
$lang['manufacturing_overall_progress_des'] = 'Description';
$lang['manufacturing_overall_progress_c_status'] = 'Current Status';
$lang['manufacturing_overall_progress_engg'] = 'ENGG';
$lang['manufacturing_overall_progress_remark'] = 'REMARK';
$lang['manufacturing_overall_progress_PR'] = 'PR';
$lang['manufacturing_overall_progress_re2'] = 'REMARK2';
$lang['manufacturing_overall_progress_po'] = 'PO';
$lang['manufacturing_overall_progress_re3'] = 'REMARK3';
$lang['manufacturing_overall_progress_fab'] = 'FAB';
$lang['manufacturing_overall_progress_nde'] = 'NDE';
$lang['manufacturing_overall_progress_hydro'] = 'HYDRO';
$lang['manufacturing_overall_progress_paint'] = 'PAINT';
$lang['manufacturing_overall_progress_fat'] = 'FAT';
$lang['manufacturing_overall_progress_re4'] = 'REMARK4';
$lang['manufacturing_overall_progress_mrb'] = 'MRB';
$lang['manufacturing_overall_progress_pl'] = 'P&L';
$lang['manufacturing_overall_progress_over_pro'] = 'Overall Progress Achieved %';
$lang['manufacturing_overall_progress_total'] = 'TOTAL';
$lang['manufacturing_overall_progress_project_with'] = 'PROJECT WITH VARIATION';
$lang['manufacturing_overall_progress_va_amount'] = 'VARIATION AMOUNT';
$lang['manufacturing_overall_progress_status_variation'] = 'STATUS OF VARIATION PO';
$lang['manufacturing_overall_progress_estimate_pl'] = 'ESTIMATED P&L';
$lang['manufacturing_overall_progress_pL'] = 'RESULT P&L';
$lang['manufacturing_overall_progress_delivery_note'] = 'DELIVERY NOTE';
$lang['manufacturing_overall_progress_clo_goods'] = 'COLLECTION OF GOODS';

$lang['manufacturing_balance_qty'] = 'Balance Qty';
$lang['manufacturing_total_qty'] = 'Total Qty';
$lang['manufacturing_cost_detail'] = 'Cost Detail';
$lang['manufacturing_margin'] = 'Margin';
$lang['manufacturing_discount'] = 'Discount';
$lang['manufacturing_discount_price'] = 'Discount Price';
$lang['manufacturing_selling_price'] = 'Selling Price';
$lang['manufacturing_unit_price'] = 'Unit Price';
$lang['manufacturing_discounted_amount'] = 'Discounted Amount';

$lang['manufacturing_revisions'] = 'Revisions';
$lang['manufacturing_additional_order_detail'] = 'Additional Order Detail';
$lang['manufacturing_master_category'] = 'Master Category';
$lang['manufacturing_add_sub_category'] = 'Add Sub Category';
$lang['manufacturing_category_description'] = 'Category Description';
$lang['manufacturing_edit_sub_category'] = 'Edit Sub Category';
$lang['manufacturing_customer_invoice'] = 'CUSTOMER INVOICE';
$lang['manufacturing_new_customer_invoice'] = 'New Customer Invoice';
$lang['manufacturing_invoice_code'] = 'Invoice Code';
$lang['manufacturing_due_date'] = 'Due Date';
$lang['manufacturing_serial_no'] = 'Serial No';
$lang['manufacturing_contract'] = 'Contract';
$lang['manufacturing_purchase_order_number'] = 'PO Number';
$lang['manufacturing_customer_invoice_simple'] = 'Customer Invoice';
$lang['manufacturing_customer_invoice_information'] = 'Customer Invoice Information';

$lang['manufacturing_gl_detail'] = 'GL Detail';
$lang['manufacturing_item_detail'] = 'Item Detail';
$lang['manufacturing_delivery_note'] = 'Delivery Note';
$lang['manufacturing_invoice_due_date'] = 'Invoice Due Date';
$lang['manufacturing_delivery_note_code'] = 'Delivery Note Code';
$lang['manufacturing_delivery_note_number'] = 'D.N. No';

$lang['manufacturing_purchase_order_reference'] = 'PO Ref';
$lang['manufacturing_description_or_particulars'] = 'Descriptions / Particulars';
$lang['manufacturing_vehicle_no'] = 'Vehicle No';
$lang['manufacturing_mobile_no'] = 'Mobile No';
$lang['manufacturing_certifies_that_the_above_mentioned_materials_have_been_received_in_good_order_and_condition_or_as_per_scope_of_work'] = 'Certifies that
                the above mentioned materials have been received in good order and condition / as per scope of work';
$lang['manufacturing_signed_for_hemt_stores'] = 'Signed For HEMT STORES';
$lang['manufacturing_customer_signature_and_stamp_after_completion_or_receipt'] = 'Customer Signature & Stamp after completion / receipt';
$lang['manufacturing_step_one_delivery_note_header'] = 'Step 1 - Delivery Note Header';
$lang['manufacturing_step_two_delivery_note_confirmation'] = 'Step 2 - Delivery Note Confirmation';

$lang['manufacturing_customer_name'] = 'Customer Name';
$lang['manufacturing_driver_name'] = 'Driver Name';
$lang['manufacturing_bill_of_material_head'] = 'BILL OF MATERIAL';
$lang['manufacturing_add_new_bill_of_material_head'] = 'Add New Bill Of Material';
$lang['manufacturing_item_master_head'] = 'ITEM MASTER';
$lang['manufacturing_main'] = 'Main';
$lang['manufacturing_category'] = 'Cat...';
$lang['manufacturing_category_title'] = 'Category';
$lang['manufacturing_current_stock_title'] = 'Curr. Stock';
$lang['manufacturing_link_item'] = 'Link Item';
$lang['manufacturing_overhead_head'] = 'OVERHEAD';
$lang['manufacturing_machine'] = 'MFQ MACHINE';
$lang['manufacturing_link_to_erp'] = 'Link To ERP';
$lang['manufacturing_manage_machine'] = 'MANAGE MACHINE';
$lang['manufacturing_machine_information'] = 'Machine Information';
$lang['manufacturing_comments'] = 'Comments';
$lang['manufacturing_pull_item_from_erp'] = 'Pull Item from ERP';
$lang['manufacturing_job_order'] = 'Job Order';
$lang['manufacturing_item_from_erp'] = 'Item from ERP';
$lang['manufacturing_asset_from_erp'] = 'Asset from ERP';
$lang['manufacturing_add_asset'] = 'Add Asset';
$lang['manufacturing_machine_name'] = 'Machine Name';
$lang['manufacturing_manufacture'] = 'Manufacture';
$lang['manufacturing_edit_machine'] = 'Edit Machine';
$lang['manufacturing_manage_crew'] = 'Manage Crew';
$lang['manufacturing_manage_customer'] = 'Manage Customer';
$lang['manufacturing_customers_from_erp'] = 'Customers from ERP';
$lang['manufacturing_segments_from_erp'] = 'Segments from ERP';
$lang['manufacturing_add_workflow'] = 'Add Workflow';
$lang['manufacturing_crew'] = 'Crew';
$lang['manufacturing_progress'] = 'Progress';
$lang['manufacturing_overhead'] = 'Over Head';
$lang['manufacturing_add_customer_inquiry'] = 'Add Customer Inquiry';
$lang['manufacturing_add_estimate'] = 'Add Estimate';
$lang['manufacturing_add_customer_invoice'] = 'Add Customer Invoice';
$lang['manufacturing_add_delivery_note'] = 'Add Delivery Note';

$lang['manufacturing_costing_configuration'] = 'Costing Configuration';
$lang['manufacturing_usage_update'] = 'Usage Update';
$lang['manufacturing_for_entries'] = 'For Entries';
$lang['manufacturing_manual'] = 'Manual';
$lang['manufacturing_linked_document'] = 'Linked Document';

/** Document setup*/
$lang['manufacturing_document_setup'] = 'Document Setup';
$lang['manufacturing_add_document_setup'] = 'Add Document Setup';


$lang['manufacturing_awarded_job_status'] = 'Awarded Job Status';
$lang['manufacturing_estimated_job_return'] = 'Estimated Job Return';
$lang['manufacturing_document_configuration'] = 'Document Configuration';
$lang['manufacturing_gl_configuration'] = 'GL Configuration';
$lang['manufacturing_item_configuration'] = 'Item Configuration';
$lang['manufacturing_policy_configuration'] = 'Policy Configuration';
$lang['manufacturing_unbilled_jobs'] = 'Unbilled Jobs';

$lang['manufacturing_estimate_proposal_approval'] = 'Estimate Proposal Review';

$lang['manufacturing_Finance'] = 'Finance';


$lang['manufacturing_stages'] = 'Stages';
$lang['manufacturing_add_stages'] = 'Add Stages';
$lang['manufacturing_edit_stages'] = 'Edit Stages';

$lang['manufacturing_edit_stages'] = 'Edit Stages';
$lang['manufacturing_weightage'] = 'Weight Age';

$lang['manufacturing_packaging'] = 'Packaging';
$lang['manufacturing_add_bom_material_consumption_head'] = 'Material Consumption';



$lang['manufacturing_detail'] = 'Detail';
$lang['manufacturing_estimated_employee'] = 'Estimated Employee';
$lang['manufacturing_sales_manager'] = 'Sales Manager';
$lang['manufacturing_sales_marketing'] = 'Sales & Marketing';
$lang['manufacturing_recipt_warehouse'] = 'Receipt to Warehouse. ';
$lang['manufacturing_add_recipt_warehouse'] = 'Add Receipt to Warehouse. ';
$lang['manufacturing_quotation'] = 'Quotation';
