<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix : navigation_menu_
 * Created on 28-June-2017
 */
$languageflowserve = getPolicyValues('LNG', 'All');
$isGCC = getPolicyValues('MANFL', 'All');

$lang['navigation_menu__dashboard'] = 'Dashboard';
$lang['navigation_menu__procurement'] = 'Purchasing';
$lang['navigation_menu__inventory'] = 'Inventory';
$lang['navigation_menu__accounts_payable'] = 'Payable';
$lang['navigation_menu__accounts_receivable'] = 'Receivable';
$lang['navigation_menu__finance'] = 'Accounting';
$lang['navigation_menu__asset_management'] = 'Fixed Assets';
$lang['navigation_menu__treasury'] = 'Banking';
$lang['navigation_menu__hrms'] = 'HRMS';
$lang['navigation_menu__configuration'] = 'Settings';
$lang['navigation_menu__tax'] = 'TAX';
$lang['navigation_menu__pos_restaurant'] = 'POS Restaurant';
$lang['navigation_menu__administration'] = 'Manage';
$lang['navigation_menu__pos_general'] = 'POS General';
$lang['navigation_menu__my_profile'] = 'Self Service';

////special company request for change navigation menu Manufacturing to Operation/Service using FlowServe policy
if($languageflowserve=='FlowServe'){
    $lang['navigation_menu__manufacturing'] = 'Operation/Service'; 
    
}else{
    $lang['navigation_menu__manufacturing'] = 'Manufacturing';
}

if (in_array($languageflowserve, ['MSE', 'SOP', 'GCC','Nov', 'Flowserve','Micoda'])) {
    $lang['navigation_menu_38_expense_claim'] = 'Employee Claim';
    $lang['navigation_menu_329_expense_claim'] = 'Employee Claim';
    $lang['navigation_menu_152_segment'] = 'Cost Center';
} else {
    
    $lang['navigation_menu_38_expense_claim'] = 'Expense Claim';
    $lang['navigation_menu_329_expense_claim'] = 'Expense Claim';
    $lang['navigation_menu_152_segment'] = 'Segment';
}



$lang['navigation_menu__sales___marketing'] = 'Sales';
$lang['navigation_menu__crm'] = 'CRM';
$lang['navigation_menu__mpr'] = 'MPR';
$lang['navigation_menu__dashboard'] = 'AI Analytics';
$lang['navigation_menu__qhse'] = 'QHSE';
$lang['navigation_menu__vdr'] = 'VDR';
$lang['navigation_menu__fleet_management'] = 'Fleet Management';
$lang['navigation_menu__srm'] = 'SRM';
$lang['navigation_menu__group_management'] = 'Group Management';
$lang['navigation_menu__project_management'] = 'Project Management';
$lang['navigation_menu__buyback'] = 'Buyback';
$lang['navigation_menu__operation_ngo'] = 'Operation NGO';
$lang['navigation_menu__operation_ngo'] = 'Operation NGO';
$lang['navigation_menu__fund_management'] = 'Fund Management';

$lang['navigation_menu_31_approval'] = 'Approval';
$lang['navigation_menu_31_transactions'] = 'Transactions';
$lang['navigation_menu_31_report'] = 'Report';
$lang['navigation_menu_31_masters'] = 'Masters';
$lang['navigation_menu_32_approval'] = 'Approval';
$lang['navigation_menu_32_transactions'] = 'Transactions';
$lang['navigation_menu_32_report'] = 'Report';
$lang['navigation_menu_32_masters'] = 'Masters';
$lang['navigation_menu_33_approval'] = 'Approval';
$lang['navigation_menu_33_transactions'] = 'Transactions';
$lang['navigation_menu_33_report'] = 'Report';
$lang['navigation_menu_33_masters'] = 'Masters';
$lang['navigation_menu_34_approval'] = 'Approval';
$lang['navigation_menu_34_transactions'] = 'Transactions';
$lang['navigation_menu_34_report'] = 'Report';
$lang['navigation_menu_35_approval'] = 'Approval';
$lang['navigation_menu_35_transactions'] = 'Transactions';
$lang['navigation_menu_35_report'] = 'Report';
$lang['navigation_menu_35_masters'] = 'Masters';
$lang['navigation_menu_36_approval'] = 'Approval';
$lang['navigation_menu_36_transactions'] = 'Transactions';
$lang['navigation_menu_36_report'] = 'Report';
$lang['navigation_menu_36_masters'] = 'Masters';
$lang['navigation_menu_37_approval'] = 'Approval';
$lang['navigation_menu_37_transactions'] = 'Transactions';
$lang['navigation_menu_37_report'] = 'Report';
$lang['navigation_menu_38_leave_management'] = 'Leave Management';
$lang['navigation_menu_38_payroll'] = 'Payroll ';
$lang['navigation_menu_38_attendance'] = 'Attendance ';
$lang['navigation_menu_38_employee'] = 'Employee';
$lang['navigation_menu_38_other_masters'] = 'Others Masters';

$lang['navigation_menu_38_approval'] = 'Approval';
$lang['navigation_menu_38_loan'] = 'Loan';
$lang['navigation_menu_38_report'] = 'Report';
$lang['navigation_menu_38_over_time_management'] = 'Over Time Management';
$lang['navigation_menu_38_final_settlement'] = 'Final Settlement';
$lang['navigation_menu_39_company_user_group'] = 'Company User Group';
$lang['navigation_menu_39_company_configuration'] = 'Company Configuration';
$lang['navigation_menu_39_user_configuration'] = 'User Configuration';
$lang['navigation_menu_39_template_configuration'] = 'Template Configuration';
$lang['navigation_menu_39_approval_setup'] = 'Approval Setup';
$lang['navigation_menu_39_navigation_group_setup'] = 'Navigation Group Setup';
$lang['navigation_menu_39_employee_navigation_access'] = 'Employee Navigation Access';
$lang['navigation_menu_39_document_setup'] = 'Document Setup';
$lang['navigation_menu_39_report_template'] = 'Report Template';
$lang['navigation_menu_40_masters'] = 'Masters';
$lang['navigation_menu_41_kitchen'] = 'Kitchen';
$lang['navigation_menu_41_pos_tablet'] = 'POS Tablet';
$lang['navigation_menu_41_dashboard'] = 'Dashboard';
$lang['navigation_menu_41_pos_terminal'] = 'POS Terminal';
$lang['navigation_menu_41_config'] = 'config';
$lang['navigation_menu_41_masters'] = 'Masters';
$lang['navigation_menu_41_till_management_report'] = 'Till Management Report';
$lang['navigation_menu_41_outlet_sales_report'] = 'Outlet Sales Report';
$lang['navigation_menu_41_delivery_order'] = 'Delivery Order';
$lang['navigation_menu_41_sales_report'] = 'Sales Report';
$lang['navigation_menu_41_sales_detail_report'] = 'Sales Detail Report';
$lang['navigation_menu_41_outlet_item_wise_sales'] = 'Outlet Item Wise Sales';
$lang['navigation_menu_41_pos_sales_report'] = 'POS Sales Report';
$lang['navigation_menu_41_item_wise_sales'] = 'Item Wise Sales';
$lang['navigation_menu_41_product_mix'] = 'Product Mix';
$lang['navigation_menu_41_franchise'] = 'Franchise';
$lang['navigation_menu_41_delivery_commision_report'] = 'Delivery Commision report';
$lang['navigation_menu_41_discount_report'] = 'Discount report';
$lang['navigation_menu_41_reports'] = 'Reports';
$lang['navigation_menu_42_reversing_approved_document'] = 'Reversing Approved Document';
$lang['navigation_menu_42_company_policy'] = 'Company Policy';
$lang['navigation_menu_133_purchase_order'] = 'Purchase Order';
$lang['navigation_menu_133_purchase_request'] = 'Purchase Request';
$lang['navigation_menu_134_purchase_order'] = 'Purchase Order';
$lang['navigation_menu_134_purchase_request'] = 'Purchase Request';
$lang['navigation_menu_135_purchase_order_list'] = 'Purchase Order List';
$lang['navigation_menu_136_purchasing_address'] = 'Purchasing Address';

////special company request for change navigation menu  using Manufacturing Flow policy
$manufacturing_Flow = getPolicyValues('MANFL', 'All');
if($manufacturing_Flow == 'Micoda'){
    $lang['navigation_menu_137_goods_received_voucher'] = 'GRN';  
}else{
    $lang['navigation_menu_137_goods_received_voucher'] = 'Goods Received Voucher';   
}
$lang['navigation_menu_137_material_issue'] = 'Material Issue';
$lang['navigation_menu_137_stock_transfer'] = 'Stock Transfer';
$lang['navigation_menu_137_purchase_return'] = 'Purchase Return';
$lang['navigation_menu_137_stock_adjustment'] = 'Stock Adjustment';
$lang['navigation_menu_137_material_receipt_note'] = 'Material Receipt Note';
$lang['navigation_menu_137_material_request'] = 'Material Request';
$lang['navigation_menu_137_stock_counting'] = 'Stock Counting';
////special company request for change navigation menu  using Manufacturing Flow policy
if($manufacturing_Flow == 'Micoda'){
    $lang['navigation_menu_138_goods_received_voucher'] = 'GRN';
}else{
    $lang['navigation_menu_138_goods_received_voucher'] = 'Goods Received Voucher';
}
$lang['navigation_menu_138_purchase_return'] = 'Purchase Return';
$lang['navigation_menu_138_material_issue'] = 'Material Issue';
$lang['navigation_menu_138_stock_transfer'] = 'Stock Transfer';
$lang['navigation_menu_138_stock_adjustment'] = 'Stock Adjustment';
$lang['navigation_menu_138_material_receipt_note'] = 'Material Receipt Note';
$lang['navigation_menu_138_material_request'] = 'Material Request';
$lang['navigation_menu_138_stock_counting'] = 'Stock Counting';
$lang['navigation_menu_139_item_inquiry'] = 'Item Inquiry';
$lang['navigation_menu_139_item_ledger'] = 'Item Ledger';
$lang['navigation_menu_139_item_valuation_summary'] = 'Item Valuation Summary';
$lang['navigation_menu_139_item_counting'] = 'Item Counting';
$lang['navigation_menu_139_fast_moving_item'] = 'Fast Moving Item';
$lang['navigation_menu_139_unbilled_grv'] = 'Un-billed GRV';
$lang['navigation_menu_140_units_of_measurement'] = 'Units of Measurement';
$lang['navigation_menu_140_item_master'] = 'Item Master';
$lang['navigation_menu_140_item_category'] = 'Item Category';
$lang['navigation_menu_140_grv_addon_category'] = 'GRV Add-on Category';
$lang['navigation_menu_140_warehouse_master'] = 'Warehouse Master ';
$lang['navigation_menu_141_supplier_invoice'] = 'Supplier Invoice';
$lang['navigation_menu_141_debit_note'] = 'Debit Note';
$lang['navigation_menu_141_payment_voucher'] = 'Payment Voucher';
$lang['navigation_menu_142_supplier_invoice'] = 'Supplier Invoice';
$lang['navigation_menu_142_debit_note'] = 'Debit Note';
$lang['navigation_menu_142_payment_voucher'] = 'Payment Voucher';
$lang['navigation_menu_142_payment_matching'] = 'Payment Matching';
$lang['navigation_menu_142_payment_reversal'] = 'Payment Reversal';
$lang['navigation_menu_143_vendor_ledger'] = 'Vendor Ledger';
$lang['navigation_menu_143_vendor_statement'] = 'Vendor Statement';
$lang['navigation_menu_143_vendor_aging_summary'] = 'Vendor Aging Summary';
$lang['navigation_menu_143_vendor_aging_detail'] = 'Vendor Aging Detail';
$lang['navigation_menu_144_supplier_category'] = 'Supplier Category';
$lang['navigation_menu_144_supplier_master'] = 'Supplier Master';
$lang['navigation_menu_145_credit_note'] = 'Credit Note';
$lang['navigation_menu_145_receipt_voucher'] = 'Receipt Voucher';
$lang['navigation_menu_146_receipt_matching'] = 'Receipt Matching';
$lang['navigation_menu_146_credit_note'] = 'Credit Note';
$lang['navigation_menu_146_receipt_voucher'] = 'Receipt Voucher';
$lang['navigation_menu_146_receipt_reversal'] = 'Receipt Reversal';
$lang['navigation_menu_147_customer_ledger'] = 'Customer Ledger';
$lang['navigation_menu_147_customer_statement'] = 'Customer Statement';
$lang['navigation_menu_147_customer_aging_summary'] = 'Customer Aging Summary';
$lang['navigation_menu_147_customer_aging_detail'] = 'Customer Aging Detail';
$lang['navigation_menu_147_collection_summary'] = 'Collection Summary';
$lang['navigation_menu_147_collection_details'] = 'Collection Details';
$lang['navigation_menu_149_journal_voucher'] = 'Journal Voucher';
$lang['navigation_menu_149_recurring_jv'] = 'Recurring JV';
$lang['navigation_menu_150_journal_voucher'] = 'Journal Voucher';
$lang['navigation_menu_150_budget'] = 'Budget';
$lang['navigation_menu_150_recurring_jv'] = 'Recurring JV';
$lang['navigation_menu_151_trial_balance'] = 'Trial Balance';
$lang['navigation_menu_151_income_statement'] = 'Income Statement';
$lang['navigation_menu_151_balance_sheet'] = 'Balance Sheet';
$lang['navigation_menu_151_general_ledger'] = 'General Ledger';
$lang['navigation_menu_152_segment'] = 'Segment';
$lang['navigation_menu_152_financial_year'] = 'Financial Year';
$lang['navigation_menu_152_chart_of_accounts'] = 'Chart of Accounts';
$lang['navigation_menu_153_asset'] = 'Asset';
$lang['navigation_menu_153_depreciation'] = 'Depreciation';
$lang['navigation_menu_153_disposal'] = 'Disposal';
$lang['navigation_menu_154_asset_depreciation'] = 'Asset Depreciation';
$lang['navigation_menu_154_asset_disposal'] = 'Asset Disposal';
$lang['navigation_menu_155_monthly_depreciation_report'] = 'Monthly Depreciation Report';
$lang['navigation_menu_155_asset_register'] = 'Asset Register';
$lang['navigation_menu_155_asset_register_summary'] = 'Asset Register Summary';
$lang['navigation_menu_156_asset_master'] = 'Asset Master';
$lang['navigation_menu_156_asset_location'] = 'Asset Location';
$lang['navigation_menu_157_bank_reconciliation'] = 'Bank Reconciliation';
$lang['navigation_menu_157_bank_transfer'] = 'Bank Transfer';
$lang['navigation_menu_158_loan_management'] = 'Loan Management';
$lang['navigation_menu_158_bank_reconciliation'] = 'Bank Reconciliation';
$lang['navigation_menu_158_bank_transfer'] = 'Bank Transfer';
$lang['navigation_menu_158_currency_exchange'] = 'Currency Exchange';
$lang['navigation_menu_159_post_dated_cheque'] = 'Postdated cheque';
$lang['navigation_menu_159_bank___cash_register'] = 'Bank / Cash Register';
$lang['navigation_menu_161_salary_declaration'] = 'Salary Declaration';
$lang['navigation_menu_161_machine_attendance'] = 'Machine Attendance';
$lang['navigation_menu_161_non_salary_processing'] = 'Non-Salary Processing';
$lang['navigation_menu_161_fixed_element_declaration'] = 'Fixed Element Declaration';
$lang['navigation_menu_161_payroll_processing'] = 'Payroll Processing';
$lang['navigation_menu_161_loan'] = 'Loan';
$lang['navigation_menu_161_leave'] = 'Leave';
$lang['navigation_menu_161_attendance_summary'] = 'Attendance Summary';
$lang['navigation_menu_161_final_settlement_approval'] = 'Final Settlement Approval';
$lang['navigation_menu_161_variable_pay_approval'] = 'Variable Pay Approval';
$lang['navigation_menu_161_salary_advance_request_approval'] = 'Salary Advance Request Approval';
$lang['navigation_menu_161_leave_encashment___salary_approval'] = 'Leave Encashment / Salary Approval';
$lang['navigation_menu_162_employee_loan'] = 'Employee Loan';
$lang['navigation_menu_162_loan_category'] = 'Loan Category';
$lang['navigation_menu_163_employee_pay_scale'] = 'Employee Pay Scale';
$lang['navigation_menu_163_pay_slip'] = 'Pay Slip';
$lang['navigation_menu_163_report_master'] = 'Report Master';
$lang['navigation_menu_163_employee_leave_balance'] = 'Employee Leave Balance';
$lang['navigation_menu_163_etf'] = 'ETF';
$lang['navigation_menu_163_epf'] = 'EPF ';
$lang['navigation_menu_163_allowance_slip'] = 'Allowance Slip';
$lang['navigation_menu_163_c_form'] = 'C Form';
$lang['navigation_menu_163_r_form'] = 'R Form';
$lang['navigation_menu_163_etf_return'] = 'ETF Return';
$lang['navigation_menu_163_payee_registration'] = 'PAYE Registration';
$lang['navigation_menu_163_income_tax_deduction'] = 'Income Tax Deduction';
$lang['navigation_menu_163_salary_comparison'] = 'Salary Comparison';
$lang['navigation_menu_163_localization'] = 'Localization';
$lang['navigation_menu_163_salary_trend'] = 'Salary Trend';
$lang['navigation_menu_163_employee_details_report'] = 'Employee Details Report';
$lang['navigation_menu_163_employee_leave_history'] = 'Employee Leave History';
$lang['navigation_menu_169_tax'] = 'Tax';
$lang['navigation_menu_169_tax_group'] = 'Tax Group';
$lang['navigation_menu_169_tax_authority'] = 'Tax Authority';
$lang['navigation_menu_173_menu_master'] = 'Menu Master';
$lang['navigation_menu_173_outlet_setup'] = 'Outlet Setup';
$lang['navigation_menu_173_create_outlets'] = 'Create Outlets';
$lang['navigation_menu_173_counter_setup'] = 'Counter Setup';
$lang['navigation_menu_173_menu_size'] = 'Menu Size';
$lang['navigation_menu_173_yield_setup'] = 'Yield Setup';
$lang['navigation_menu_173_customer___order_setup'] = 'Customer / Order Setup';
$lang['navigation_menu_173_promotion_discount_setup'] = 'Promotion/Discount Setup';
$lang['navigation_menu_173_yield_preparation'] = 'Yield Preparation';
$lang['navigation_menu_173_user_group'] = 'User Group';
$lang['navigation_menu_173_authentication_process'] = 'Authentication Process';

$lang['navigation_menu_175_gl_configuration'] = 'GL Configuration';
$lang['navigation_menu_175_counter'] = 'Counter';
$lang['navigation_menu_175_warehouse_users'] = 'Warehouse Users';
$lang['navigation_menu_175_crew_roles'] = 'Crew Roles';
$lang['navigation_menu_175_outlet_users'] = 'Outlet Users';
$lang['navigation_menu_175_customers'] = 'Customers';
$lang['navigation_menu_175_card_master'] = 'Card Master';


$lang['navigation_menu_285_template'] = 'Template';
$lang['navigation_menu_313_apply_for_leave'] = 'Apply for Leave';
$lang['navigation_menu_313_leave_master'] = 'Leave Master';
$lang['navigation_menu_313_leave_group'] = 'Leave Group';
$lang['navigation_menu_313_leave_calendar'] = 'Leave Calendar';
$lang['navigation_menu_313_leave_adjustment'] = 'Leave Adjustment';
$lang['navigation_menu_313_monthly_leave_accrual'] = 'Monthly Leave Accrual';
$lang['navigation_menu_313_annual_leave_accrual'] = 'Annual Leave Accrual';
$lang['navigation_menu_313_leave_plan'] = 'Leave Plan';
$lang['navigation_menu_313_approval_setup'] = 'Approval Setup';
$lang['navigation_menu_313_sick_leave_setup'] = 'Sick Leave setup';
$lang['navigation_menu_318_terminal'] = 'Terminal';
$lang['navigation_menu_318_masters'] = 'Masters';
$lang['navigation_menu_318_dashboard'] = 'Dashboard';
$lang['navigation_menu_324_gl_configuration'] = 'GL Configuration';
$lang['navigation_menu_324_counter'] = 'Counter';
$lang['navigation_menu_324_warehouse_users'] = 'Warehouse Users';
$lang['navigation_menu_329_profile'] = 'Profile';
$lang['navigation_menu_329_pay_slip'] = 'Pay Slip';
$lang['navigation_menu_329_apply_for_leave'] = 'Apply for Leave';
$lang['navigation_menu_329_monthly_allowance_slip'] = 'Monthly Allowance Slip ';

$lang['navigation_menu_329_approval'] = 'Approval';
$lang['navigation_menu_329_leave_plan'] = 'Leave Plan';
$lang['navigation_menu_329_sales_target'] = 'Sales Target';
$lang['navigation_menu_329_iou_expenses'] = 'IOU Expenses';
$lang['navigation_menu_329_salary_advance_request'] = 'Salary Advance Request';
$lang['navigation_menu_329_attendance'] = 'Attendance';
$lang['navigation_menu_329_purchase_request'] = 'Purchase Request';

$lang['navigation_menu_342_monthly_addition'] = 'Monthly Addition';
$lang['navigation_menu_342_monthly_deduction'] = 'Monthly Deduction';
$lang['navigation_menu_342_payroll_processing'] = 'Payroll Processing';
$lang['navigation_menu_342_monthly_add___ded'] = 'Monthly Add / Ded';
$lang['navigation_menu_342_salary_category'] = 'Salary Category';
$lang['navigation_menu_342_paysheet_template'] = 'Paysheet Template';
$lang['navigation_menu_342_salary_declaration'] = 'Salary Declaration';
$lang['navigation_menu_342_social_insurance_master'] = 'Social Insurance Master';
$lang['navigation_menu_342_slab_master'] = 'Slab Master';
$lang['navigation_menu_342_paysheet_grouping'] = 'Paysheet Grouping';
$lang['navigation_menu_342_payee_master'] = 'PAYE Master';
$lang['navigation_menu_342_non_salary_processing'] = 'Non-Salary Processing';
$lang['navigation_menu_342_sso_slab_master'] = 'SSO Slab Master';
$lang['navigation_menu_343_attendance_types'] = 'Attendance Types';
$lang['navigation_menu_343_shift_master'] = 'Shift Master';
$lang['navigation_menu_343_floor_master'] = 'Floor Master';
$lang['navigation_menu_343_over_time_master'] = 'Over Time Master';
$lang['navigation_menu_343_over_time_group_master'] = 'Over Time Group Master';
$lang['navigation_menu_343_manual_attendance'] = 'Manual Attendance';
$lang['navigation_menu_343_machine_attendance'] = 'Machine Attendance';
$lang['navigation_menu_343_no_pay_setup'] = 'No pay setup';
$lang['navigation_menu_343_machine_mapping'] = 'Machine Mapping';
$lang['navigation_menu_343_attendance_summary'] = 'Attendance Summary';
$lang['navigation_menu_343_attendance_template'] = 'Attendance Template';
$lang['navigation_menu_344_employee_master'] = 'Employee Master';
$lang['navigation_menu_344_bank_master'] = 'Bank Master';
$lang['navigation_menu_344_employee_type'] = 'Employee Type';
$lang['navigation_menu_344_employee_non_payroll_bank'] = 'Employee Non Payroll Bank';
$lang['navigation_menu_344_grade'] = 'Grade';
$lang['navigation_menu_345_department_master'] = 'Department Master';
$lang['navigation_menu_345_document_setup'] = 'Document Setup';
$lang['navigation_menu_345_religion_master'] = 'Religion Master';
$lang['navigation_menu_345_country_master'] = 'Country Master';
$lang['navigation_menu_345_designation_master'] = 'Designation Master';
$lang['navigation_menu_345_document_master'] = 'Document Master';
$lang['navigation_menu_345_nationality_master'] = 'Nationality Master';
$lang['navigation_menu_345_insurance_category'] = 'Insurance Category';
$lang['navigation_menu_345_hr_documents'] = 'HR Documents';
$lang['navigation_menu_348_job_cart'] = 'Job Cart';
$lang['navigation_menu_348_configuration'] = 'Configuration';
$lang['navigation_menu_348_masters'] = 'Masters';
$lang['navigation_menu_348_dashboard'] = 'Dashboard';
$lang['navigation_menu_348_job'] = 'Job';
$lang['navigation_menu_348_customer_inquiry'] = 'Customer Inquiry';

if($isGCC=='GCC'){
    $lang['navigation_menu_348_estimate'] = 'Quotation';
}
else{
    $lang['navigation_menu_348_estimate'] = 'Estimate';
}

$lang['navigation_menu_348_approval'] = 'Approval';

$lang['navigation_menu_350_bill_of_material'] = 'Bill of Material';
$lang['navigation_menu_350_item_master'] = 'Item Master';
$lang['navigation_menu_350_over_heads'] = 'Over Heads';
$lang['navigation_menu_350_asset_master'] = 'Asset Master';
$lang['navigation_menu_350_crew'] = 'Crew';
$lang['navigation_menu_350_company_workflow'] = 'Company Workflow';
$lang['navigation_menu_350_template'] = 'Template';
$lang['navigation_menu_350_machine'] = 'Machine';
$lang['navigation_menu_350_template_setup'] = 'Template Setup';
$lang['navigation_menu_350_customers'] = 'Customers';
$lang['navigation_menu_350_segment'] = 'Segment';
$lang['navigation_menu_350_workflow_setup'] = 'Workflow Setup';
$lang['navigation_menu_350_unit_of_measure'] = 'Unit Of Measure';
$lang['navigation_menu_350_system_settings'] = 'System Settings';

$lang['navigation_menu_361_masters'] = 'Masters';
$lang['navigation_menu_361_transactions'] = 'Transactions';
$lang['navigation_menu_361_approval'] = 'Approval';
$lang['navigation_menu_361_reports'] = 'Reports';
$lang['navigation_menu_363_customer_category'] = 'Customer Category';
$lang['navigation_menu_363_sales_person'] = 'Sales Person';
$lang['navigation_menu_363_customer_master'] = 'Customer Master';
$lang['navigation_menu_364_quotation___contract'] = 'Quotation / Contract';
$lang['navigation_menu_364_sales_commission'] = 'Sales Commission';
$lang['navigation_menu_364_commission_payment'] = 'Commission Payment';
$lang['navigation_menu_364_sales_return'] = 'Sales Return';
$lang['navigation_menu_364_invoice'] = 'Invoice';
$lang['navigation_menu_365_quotation___contract'] = 'Quotation / Contract';
$lang['navigation_menu_365_sales_commission'] = 'Sales Commission';
$lang['navigation_menu_365_sales_return'] = 'Sales Return';
$lang['navigation_menu_365_commission_payment'] = 'Commision Payment';
$lang['navigation_menu_365_invoice'] = 'Invoice';
$lang['navigation_menu_365_sales_order'] = 'Sales Order';
$lang['navigation_menu_365_revenue_details_report'] = 'Revenue Details Report';
$lang['navigation_menu_365_revenue_details_summary'] = 'Revenue Details Summary';
$lang['navigation_menu_387_dashboard'] = 'Dashboard';
$lang['navigation_menu_387_campaigns'] = 'Campaigns';
$lang['navigation_menu_387_tasks'] = 'Tasks';
$lang['navigation_menu_387_meetings'] = 'Meetings';
$lang['navigation_menu_387_contacts'] = 'Contacts';
$lang['navigation_menu_387_accounts'] = 'Accounts';
$lang['navigation_menu_387_leads'] = 'Leads';
$lang['navigation_menu_387_opportunities'] = 'Opportunities';
$lang['navigation_menu_387_organizations'] = 'Organizations';
$lang['navigation_menu_387_system_settings'] = 'System Settings';
$lang['navigation_menu_387_reports'] = 'Reports';
$lang['navigation_menu_387_projects'] = 'Projects';
$lang['navigation_menu_387_sales_target'] = 'Sales Target';
$lang['navigation_menu_387_quotation'] = 'Quotation';
$lang['navigation_menu_387_expense_claim'] = 'Expense Claim';
$lang['navigation_menu_409_expense_claim_master'] = 'Expense Claim Master';
$lang['navigation_menu_409_expense_claim_category'] = 'Expense Claim Category';
$lang['navigation_menu_410_approval'] = 'Approval';
$lang['navigation_menu_415_expense_claim'] = 'Expense Claim';

$lang['navigation_menu_420_master'] = 'Master';
$lang['navigation_menu_420_dashboard'] = 'Dashboard';
$lang['navigation_menu_420_transactions'] = 'Transactions';
$lang['navigation_menu_421_srm_supplier_master'] = 'SRM Supplier Master';
$lang['navigation_menu_421_srm_customer_master'] = 'SRM Customer Master';
$lang['navigation_menu_421_customer_order'] = 'Customer Order';
$lang['navigation_menu_441_configuration'] = 'Configuration';
$lang['navigation_menu_441_group_consolidation'] = 'Group Consolidation';
$lang['navigation_menu_447_company_sub_groups'] = 'Company Sub Groups';
$lang['navigation_menu_447_sub_group_employees'] = 'Sub Group Employees';
$lang['navigation_menu_447_navigation_access'] = 'Navigation Access ';
$lang['navigation_menu_447_sub_group_template_setup'] = 'Sub Group Template Setup';
$lang['navigation_menu_448_customer_master'] = 'Customer Master';
$lang['navigation_menu_448_supplier_master'] = 'Supplier Master';
$lang['navigation_menu_448_chart_of_accounts'] = 'Chart Of Accounts';
$lang['navigation_menu_448_customer_category'] = 'Customer Category';
$lang['navigation_menu_448_supplier_category'] = 'Supplier Category';
$lang['navigation_menu_448_segment'] = 'Segment';
$lang['navigation_menu_448_item_category'] = 'Item Category';
$lang['navigation_menu_448_item_master'] = 'Item Master';
$lang['navigation_menu_448_unit_of_measurement'] = 'Unit Of Measurement';
$lang['navigation_menu_448_finance_year'] = 'Finance Year';
$lang['navigation_menu_448_warehouse'] = 'Warehouse';

$lang['navigation_menu_454_item_category'] = 'Item Category';
$lang['navigation_menu_454_asset_category'] = 'Asset Category';
$lang['navigation_menu_458_fixed_elements'] = 'Fixed Elements';
$lang['navigation_menu_399_fixed_elements'] = 'Fixed Elements';
$lang['navigation_menu_458_over_time_group'] = 'Over Time Group';
$lang['navigation_menu_399_over_time_group'] = 'Over Time Group';
$lang['navigation_menu_458_over_time_slab'] = 'Over Time Slab';
$lang['navigation_menu_399_over_time_slab'] = 'Over Time Slab';
$lang['navigation_menu_458_monthly_addition'] = 'Monthly Addition';
$lang['navigation_menu_399_monthly_addition'] = 'Monthly Addition';
$lang['navigation_menu_458_fixed_element_declaration'] = 'Fixed Element Declaration';
$lang['navigation_menu_399_fixed_element_declaration'] = 'Fixed Element Declaration';
$lang['navigation_menu_466_project_master'] = 'Project Master';
$lang['navigation_menu_466_project_category'] = 'Project Category';
$lang['navigation_menu_466_project'] = 'Project';
$lang['navigation_menu_466_project_planning'] = 'Project Planning';
$lang['navigation_menu_466_approval'] = 'Approval';
$lang['navigation_menu_466_masters'] = 'Masters';
$lang['navigation_menu_481_project_approval'] = 'Project Approval';
$lang['navigation_menu_483_project_category'] = 'Project Category';
$lang['navigation_menu_483_project_master'] = 'Project Master';

$lang['navigation_menu_478_order_inquiry'] = 'Order Inquiry';
$lang['navigation_menu_361_approval'] = 'Approval';
$lang['navigation_menu_361_transactions'] = 'Transactions';
$lang['navigation_menu_361_masters'] = 'Masters';
$lang['navigation_menu_365_quotation___contract'] = 'Quotation / Contract';
$lang['navigation_menu_365_invoice'] = 'Invoice';
$lang['navigation_menu_365_sales_commission'] = 'Sales Commission';
$lang['navigation_menu_365_sales_return'] = 'Sales Return';
$lang['navigation_menu_365_commission_payment'] = 'Commission Payment';
$lang['navigation_menu_364_quotation___contract'] = 'Quotation / Contract';
$lang['navigation_menu_364_invoice'] = 'Invoice';
$lang['navigation_menu_364_sales_commission'] = 'Sales Commission';
$lang['navigation_menu_364_sales_return'] = 'Sales Return';
$lang['navigation_menu_364_commission_payment'] = 'Commission Payment';
$lang['navigation_menu_363_customer_master'] = 'Customer Master';
$lang['navigation_menu_363_customer_category'] = 'Customer Category';
$lang['navigation_menu_363_sales_person'] = 'Sales Person';
$lang['navigation_menu_421_customer_order'] = 'Customer Order';
$lang['navigation_menu_478_order_inquiry'] = 'Order Inquiry';
$lang['navigation_menu_478_order_review'] = 'Order Review';
$lang['navigation_menu_478_customer_order'] = 'Customer Order';

$lang['navigation_menu_501_approval'] = 'Approval';
$lang['navigation_menu_501_reports'] = 'Reports';
$lang['navigation_menu_501_masters'] = 'Masters';
$lang['navigation_menu_501_transaction'] = 'Transaction';

$lang['navigation_menu_537_dispatch_note'] = 'Dispatch Note';
$lang['navigation_menu_537_grn'] = 'GRN';
$lang['navigation_menu_537_payment_voucher'] = 'Payment Voucher';
$lang['navigation_menu_537_batch_closing'] = 'Batch Closing';


$lang['navigation_menu_502_dispatch_note'] = 'Dispatch Note';
$lang['navigation_menu_502_mortality'] = 'Mortality';
$lang['navigation_menu_502_payment_voucher'] = 'Payment Voucher';
$lang['navigation_menu_502_goods_received_note'] = 'Goods Received Note';

$lang['navigation_menu_501_farm'] = 'Farm';
$lang['navigation_menu_501_batch'] = 'Batch';
$lang['navigation_menu_501_add_on_category'] = 'Add-on Category';
$lang['navigation_menu_501_item_master'] = 'Item Master';
$lang['navigation_menu_501_mortality_causes'] = 'Mortality Causes';
$lang['navigation_menu_501_production_report'] = 'Production Report';


$lang['navigation_menu_519_approval'] = 'Approval';
$lang['navigation_menu_519_donor_collection'] = 'Donor Collection';
$lang['navigation_menu_519_donor_commitments'] = 'Donor Commitments';
$lang['navigation_menu_519_donor_collections'] = 'Donor Collections';
$lang['navigation_menu_519_transactions'] = 'Transactions';


$lang['navigation_menu_519_masters'] = 'Masters';
$lang['navigation_menu_519_donors'] = 'Donors';
$lang['navigation_menu_519_projects'] = 'Projects';
$lang['navigation_menu_519_beneficiary'] = 'Beneficiary';

$lang['navigation_menu_519_document_master'] = 'Document Master';
$lang['navigation_menu_519_document_setup'] = 'Document Setup';
$lang['navigation_menu_519_area_setup'] = 'Area Setup';
$lang['navigation_menu_519_beneficiary_types'] = 'Beneficiary Types';
$lang['navigation_menu_519_configuration'] = 'Configuration';
$lang['navigation_menu_161_final_settlement'] = 'Final Settlement';
$lang['navigation_menu_161_gl_configuration'] = 'GL Configuration';
$lang['navigation_menu_342_variable_pay_declaration'] = 'Variable Pay Declaration';
$lang['navigation_menu_342_gratuity_setup'] = 'Gratuity Setup';
$lang['navigation_menu_348_standard_job_card'] = 'Standard Job Card';
$lang['navigation_menu_348_customer_invoice'] = 'Customer Invoice';

if($isGCC=='GCC'){
    $lang['navigation_menu_348_delivery_note'] = 'Receipt to Warehouse.';
}
else{
    $lang['navigation_menu_348_delivery_note'] = 'Delivery Note';
}

$lang['navigation_menu_364_delivery_order'] = 'Delivery Order';
$lang['navigation_menu_365_delivery_order'] = 'Delivery Order';
$lang['navigation_menu_363_discount_and_extra_charges'] = 'Discount and Extra Charges';
$lang['navigation_menu_365_revenue_summary'] = 'Revenue Summary';
$lang['navigation_menu_365_sales_person_performance'] = 'Sales Person Performance';
$lang['navigation_menu_365_unbilled_invoices'] = 'Unbilled Invoices';
$lang['navigation_menu_139_stock_aging'] = 'Stock Aging';
$lang['navigation_menu_139_itemwise_profitablity'] = 'Itemwise Profitablity';
$lang['navigation_menu_140_item_attribute_assign'] = 'Item Attribute Assign';
$lang['navigation_menu_143_vendor_balance'] = 'Vendor Balance';
$lang['navigation_menu_143_vendor_balance'] = 'Vendor Balance';
$lang['navigation_menu_147_customer_balance'] = 'Customer Balance';
$lang['navigation_menu_149_budget'] = 'Budget';
$lang['navigation_menu_151_budget'] = 'Budget';
$lang['navigation_menu_158_cheque_register'] = 'Cheque Register';
$lang['navigation_menu_31_approval'] = 'Approval';
$lang['navigation_menu_31_transaction'] = 'Transaction';
$lang['navigation_menu_32_masters'] = 'Masters';
$lang['navigation_menu_1109_iou_voucher'] = 'IOU Voucher';
$lang['navigation_menu_1109_iou_booking'] = 'IOU Booking';
$lang['navigation_menu_1109_iou_category'] = 'IOU Category';
$lang['navigation_menu_1109_iou_user'] = 'IOU User';
$lang['navigation_menu_169_tax_formula'] = 'Tax Formula';
$lang['navigation_menu_1065_tax_statement'] = 'Tax Statement';
$lang['navigation_menu_40_report'] = 'Report';
$lang['navigation_menu_344_employment_type'] = 'Employment Type';
$lang['navigation_menu_313_leave_encashment___salary'] = 'Leave Encashment / Salary Approval';

$lang['navigation_menu_163_gratuity_salary'] = 'Gratuity salary';
$lang['navigation_menu_163_social_insurance'] = 'Social Insurance';
$lang['navigation_menu_163_employee_birth_day_report'] = 'Employee Birth Day Report';
$lang['navigation_menu_163_employee_contract_expiry'] = 'Employee Contract Expiry';
$lang['navigation_menu_163_employee_service_analysis'] = 'Employee Service Analysis';


$lang['navigation_menu_1109_approval'] = 'Approval';
$lang['navigation_menu_1109_report'] = 'Report';
$lang['navigation_menu_1109_master'] = 'Master';
$lang['navigation_menu_1109_transaction'] = 'Transaction';

$lang['navigation_menu_1110_fuel_usage'] = 'Fuel Usage';
$lang['navigation_menu_1110_journey_plan'] = 'Journey Plan';

$lang['navigation_menu_1109_fuel_usage'] = 'Fuel Usage';
$lang['navigation_menu_1109_vehicle_master'] = 'Vehicle Master';
$lang['navigation_menu_1109_driver_master'] = 'Driver Master';
$lang['navigation_menu_1109_fuel_types'] = 'Fuel Types';
$lang['navigation_menu_1109_fuel_usage_report'] = 'Fuel Usage Report';
$lang['navigation_menu_1109_expense_category'] = 'Expense Category';
$lang['navigation_menu_1109_journey_plan'] = 'Journey Plan';
$lang['navigation_menu_1109_vehicle_maintenance'] = 'Vehicle Maintenance';
$lang['navigation_menu_1109_maintenance_criteria'] = 'Maintenance Criteria';
$lang['navigation_menu_519_operation'] = 'Operation';


$lang['navigation_menu_1107_company_master'] = 'Company Master';
$lang['navigation_menu_1107_investment_types'] = 'Investment Types';
$lang['navigation_menu_1107_investment'] = 'Investment';
$lang['navigation_menu_1107_document_setup'] = 'Document Setup';
$lang['navigation_menu_1107_financials'] = 'Financials';
$lang['navigation_menu_1107_report'] = 'Report';
$lang['navigation_menu_1153_income_statement'] = 'Income Statement';
$lang['navigation_menu_42_reversing_approved_document'] = 'Reversing Approved Document';
$lang['navigation_menu_39_terms___conditions'] = 'Terms & Conditions';
$lang['navigation_menu_39_payroll_access'] = 'Payroll Access';

$lang['navigation_menu_350_labour'] = 'Labour';
$lang['navigation_menu_350_warehouse'] = 'Warehouse';
$lang['navigation_menu_350_user_groups'] = 'User Groups';
$lang['navigation_menu_350_standard_details'] = 'Standard Details';

$lang['navigation_menu_329_my_tasks'] = 'My Tasks';
$lang['navigation_menu_329_my_appraisal'] = 'My Appraisal';
$lang['navigation_menu_329_request_letters'] = 'Request Letters';

$lang['navigation_menu_134_purchase_order_buy_back'] = 'Purchase Order Buy Back';

$lang['navigation_menu_138_goods_received_voucher_2'] = 'Goods Received Voucher 2';
$lang['navigation_menu_365_quotation___contract_buy_back'] = 'Quotation / Contract buy back';
$lang['navigation_menu_365_sales_return_buy_back'] = 'Sales Return buy back';
$lang['navigation_menu_365_customer_price_setup'] = 'Customer Price Setup';

$lang['navigation_menu_364_quotation___contract_buy_back'] = 'Quotation / Contract buy back';
$lang['navigation_menu_364_sales_return_buy_back'] = 'Sales Return buy back';
$lang['navigation_menu_364_day_close'] = 'Day Close';

$lang['navigation_menu_363_customer_price_setup'] = 'Customer Price Setup';
$lang['navigation_menu_363_insurance_types'] = 'Insurance Types';

$lang['navigation_menu_365_item_wise_sales_report'] = 'Item Wise Sales Report';
$lang['navigation_menu_365_sales_analysis_report'] = 'Sales Analysis Report';

$lang['navigation_menu_387_mail_box'] = 'Mail Box';

$lang['navigation_menu_344_document_request'] = 'Document Request';

$lang['navigation_menu_163_grade_wise_salary_cost'] = 'Grade-wise salary cost';
$lang['navigation_menu_163_attendance'] = 'Attendance';
$lang['navigation_menu_163_leave_cost'] = 'Leave Cost';
$lang['navigation_menu_163_document_expiry'] = 'Document Expiry';
$lang['navigation_menu_163_audit_report'] = 'Audit Report';
$lang['navigation_menu_163_etf'] = 'ETF';
$lang['navigation_menu_163_epf'] = 'EPF';

$lang['navigation_menu_345_commission_scheme'] = 'Commission Scheme';
$lang['navigation_menu_345_travel_frequency'] = 'Travel Frequency';

$lang['navigation_menu_41_portable_pos'] = 'Portable POS';
$lang['navigation_menu_41_kitchen_manual'] = 'Kitchen Manual';

$lang['navigation_menu__performance_appraisal'] = 'Performance Appraisal';
$lang['navigation_menu__operation'] = 'Operation';

$lang['navigation_menu_318_reports'] = 'Reports';
$lang['navigation_menu_1099_sales_report'] = 'Sales Report';
$lang['navigation_menu_1099_item_wise_sales_report'] = 'Item Wise Sales Report';
$lang['navigation_menu_1099_item_wise_profitability_report'] = 'Item Wise Profitability Report';
$lang['navigation_menu_1099_sales_detail_report'] = 'Sales Detail Report';

$lang['navigation_menu_42_group_structure'] = 'Group Structure';
$lang['navigation_menu_42_group_structure_setup'] = 'Group Structure Setup';
$lang['navigation_menu_42_subscription'] = 'Subscription';

$lang['navigation_menu_501_dashboard'] = 'Dashboard';
$lang['navigation_menu_501_configuration'] = 'Configuration';

$lang['navigation_menu_501_batch_creation'] = 'Batch Creation';
$lang['navigation_menu_501_goods_received_note'] = 'Goods Received Note';
$lang['navigation_menu_501_vouchers'] = 'Vouchers';
$lang['navigation_menu_501_return'] = 'Return';

$lang['navigation_menu_502_live_collection'] = 'Live Collection';
$lang['navigation_menu_502_return'] = 'Return';
$lang['navigation_menu_502_vouchers'] = 'Vouchers';
$lang['navigation_menu_502_farm_visit_report'] = 'Farm Visit Report';

$lang['navigation_menu_501_feed_types'] = 'Feed Types';
$lang['navigation_menu_501_task_types'] = 'Task Types';
$lang['navigation_menu_501_farm_visit_task'] = 'Farm Visit Task';

$lang['navigation_menu_501_production_statement'] = 'Production Statement';
$lang['navigation_menu_501_feed_schedule'] = 'Feed Schedule';
$lang['navigation_menu_501_batch_performance'] = 'Batch Performance';
$lang['navigation_menu_501_farm_ledger'] = 'Farm Ledger';
$lang['navigation_menu_501_wip_report'] = 'WIP Report';
$lang['navigation_menu_501_outstanding'] = 'Outstanding';
$lang['navigation_menu_501_batch_aging_report'] = 'Batch Aging Report';
$lang['navigation_menu_501_monthly_summary'] = 'Monthly Summary';
$lang['navigation_menu_501_batch_tracing'] = 'Batch Tracing';

$lang['navigation_menu_501_feed_chart'] = 'Feed Chart';
$lang['navigation_menu_501_area_setup'] = 'Area Setup';
$lang['navigation_menu_501_policy'] = 'Policy';

$lang['navigation_menu_39_location'] = 'Location';
$lang['navigation_menu_39_mpr_template_setup'] = 'MPR Template Setup';

$lang['navigation_menu_135_purchase_order_tracking'] = 'Purchase Order Tracking';
$lang['navigation_menu_140_percentage_setup'] = 'Percentage Setup';
$lang['navigation_menu_34_masters'] = 'Masters';
$lang['navigation_menu_163_sponsor_wise_salary'] = 'Sponsor Wise Salary';
$lang['navigation_menu_163_salary_breakup_report'] = 'Salary Breakup Report';

$lang['navigation_menu_138_exceeded_items'] = 'Exceeded Items';
$lang['navigation_menu_138_item_master_report'] = 'Item Master Report';
$lang['navigation_menu_138_below_min_stock__rol'] = 'Below Min Stock/ ROL';
$lang['navigation_menu_149_budget_transfer'] = 'Budget Transfer';
$lang['navigation_menu_150_budget_transfer'] = 'Budget Transfer';
$lang['navigation_menu_345_sponsor'] = 'Sponsor';
//$lang['navigation_menu_41_kitchen_countdown'] = 'Kitchen Countdown';
$lang['navigation_menu_1236_dashboard'] = 'Dashboard';
$lang['navigation_menu_32_activity'] ='Activity';
$lang['navigation_menu_32_reports'] ='Reports';
$lang['navigation_menu_136_corporate_goal'] ='Corporate Goal';
$lang['navigation_menu_136_employee_wise_performance'] ='Employee Wise Performance';
$lang['navigation_menu_136_soft_skills_based_performance'] ='Soft-Skills Based Performance';
$lang['navigation_menu_136_performance_evaluation_summary'] ='Performance Evaluation Summary';
$lang['navigation_menu_136_department'] ='Department';
$lang['navigation_menu_136_corporate_objective'] ='Corporate Objective';
$lang['navigation_menu_136_soft_skills'] ='Soft Skills';
$lang['navigation_menu_136_approval_setup'] ='Approval Setup';
$lang['navigation_menu_136_department_appraisal'] ='Department Appraisal';

/**duplicated */


