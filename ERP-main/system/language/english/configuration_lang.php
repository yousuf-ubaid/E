<?php

/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix: config_
 */
defined('BASEPATH') or exit('No direct script access allowed');

/** Common */
$lang['config_common_create_user_group'] = 'Create UserGroup';
$lang['config_common_sub_group'] = 'Sub Group';
$lang['config_common_user_group'] = 'User Group';
$lang['config_common_main_group'] = 'Main Group';
$lang['config_common_inactive'] = 'Inactive';
$lang['config_common_add_link'] = 'Add Link';
$lang['config_common_step_one'] = 'Step 1';
$lang['config_common_add_save'] = 'Add Save';
$lang['config_common_supplier_code'] = 'Supplier Code';
$lang['config_common_supplier_details'] = 'Supplier Details';
$lang['config_common_you_want_to_delete_this_supplier'] = 'You want to delete this supplier!';
$lang['config_common_add_supplier'] = 'Add Supplier';
/*Company Sub Groups*/
$lang['config_company_sub_groups'] = 'Company Sub Groups';
$lang['config_company_sub_group_edit'] = 'Sub Group Edit';
$lang['config_main_group_is_required'] = 'Main Group is required';
$lang['config_add_employees'] = 'Add Employees';
$lang['config_emp_id'] = 'EmpID';
$lang['config_navigation_access'] = 'Navigation Access';
$lang['config_customer_code_is_required'] = 'customer Code is required';
$lang['config_customer_name_is_required'] = 'customer Name is required';
/*Group Conciladation*/
$lang['config_customer_master'] = 'Customer Master';
$lang['config_create_customer'] = 'Create Customer';
$lang['config_add_new_customer'] = 'Add New Customer';
$lang['config_group_customer_code'] = 'Group Customer Code';
$lang['config_group_customer_details'] = 'Group Customer Details';
$lang['config_customer_link'] = 'Customer Link';
$lang['config_you_want_to_delete_this_customer'] = 'You want to delete this customer!';
$lang['config_customer_header'] = 'Customer Header';
$lang['config_customer_secondary_code'] = 'Customer Secondary Code';
$lang['config_customer_receivable_account'] = 'Receivable Account';
$lang['config_customer_currency'] = 'Customer Currency';
$lang['config_add_customer'] = 'Add Customer';
$lang['config_customer_code'] = 'Customer Code';
$lang['config_customer_company'] = 'Customer Company';
$lang['config_customer_details'] = 'Customer Details';
$lang['config_gl_descriprion'] = 'GL Description';
$lang['config_gl_receivable_account_is_required'] = 'Receivabl Account is required';
$lang['config_gl_receivable_customer_currency_is_required'] = 'customer Currency  is required';
$lang['config_update_customer'] = 'Update Customer';
/*Supplier Master*/
$lang['config_supplier_master'] = 'Supplier Master';
$lang['config_add_new_supplier'] = 'Add New Supplier';
$lang['config_create_supplier'] = 'Create Supplier';
$lang['config_supplier_link'] = 'Supplier Link';
$lang['config_supplier_name'] = 'Supplier Name';
$secondary_code = getPolicyValues('SCAC', 'All');

if($secondary_code == 1){
    $lang['config_secondary_code'] = 'Account Code';
}else{
    $lang['config_secondary_code'] = 'Secondary Code';
}

$lang['config_supplier_header'] = 'Supplier Header';
$lang['config_liability_account'] = 'Liability Account';
$lang['config_supplier_code_is_required'] = 'Supplier Code is required';
$lang['config_supplier_name_is_required'] = 'Supplier Name is required';
$lang['config_liability_account_is_required'] = 'Liability Account is required';
$lang['config_supplier_currency_is_required'] = 'Supplier Currency  is required';
$lang['config_update_supplier'] = 'Update Supplier';
$lang['config_customer_country'] = 'Customer Country';
$lang['config_secondary_address'] = 'Secondary Address';
$lang['config_primary_address'] = 'Primary Address';
$lang['config_credit_limit'] = 'Credit Limit';
$lang['config_credit_period'] = 'Credit Period';
$lang['config_identification_no'] = 'Identification No';
$lang['config_name_on_cheque'] = 'Name On Cheque';
$lang['config_chart_of_accounts'] = 'Chart of Accounts';
$lang['config_chart_of_accounts_or_category_not_linked '] = 'Chart of account or category not linked';
$lang['config_duplicate'] = 'Duplicate';
$lang['config_chart_of_account_replication '] = 'Chart of account Replication';
$lang['config_chart_of_account_link '] = 'Chart of account Link';
$lang['config_bank_currency'] = 'Bank Currency';
$lang['config_bank_swift_code'] = 'Bank Swift Code';
$lang['config_bank_brach'] = 'Bank Branch';
$lang['config_check_number'] = 'Check Number';
$lang['config_bank_name'] = 'Bank Name';
$lang['config_master_account'] = 'Master Account';
$lang['config_find_gl'] = 'Find GL';
$lang['config_segment_group'] = 'Segment Group';
$lang['config_segment_group_replication'] = 'Segment Group Replication';
$lang['config_segment_name'] = 'Segment Name';
$lang['config_segment_link'] = 'Segment Link';
$lang['config_segment_code'] = 'Segment Code';
$lang['config_create_segment'] = 'Create Segment';
$lang['config_add_new_segment'] = 'Add New Segment';
$lang['config_item_master_link'] = 'Item Master Link';
$lang['config_add_new_item'] = 'Add New Item';
$lang['config_add_new_category'] = 'Add New Category';
$lang['config_edit_category'] = 'Edit Category';
$lang['config_common_supplier_category'] = 'Supplier Category';
$lang['config_customer_category_replication'] = 'Customer Category Replication';
$lang['config_supplier_category_replication'] = 'Supplier Category Replication';
$lang['config_uom_replication'] = 'Unit of Measure Replication';
$lang['config_warehouse_replication'] = 'Warehouse Replication';
$lang['config_group_financial_year'] = 'Group Financial Year';
$lang['config_create_financial_year'] = 'Create Financial Year';
$lang['config_create_new_group_financial_year'] = 'Add New Group Financial Year';
$lang['config_group_warehouse_master'] = 'Group Warehouse Master';
$lang['config_bom_number'] = 'BOM number';
