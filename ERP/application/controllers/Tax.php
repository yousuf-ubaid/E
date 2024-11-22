<?php
// =============================================
// -  File Name : Tax.php
// -  Project Name : MERP
// -  Module Name : Tax
// -  Create date : 11 - September 2016
// -  Description : This file contains the add function for tax.

// - REVISION HISTORY
// -  =============================================

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class Tax extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Tax_modal');
    }

    function load_tax()
    {
        $this->datatables->select("taxMasterAutoID,companyID,companyCode as companyCode,taxShortCode as taxShortCode,taxDescription as taxDescription,isActive,taxType as taxType,supplierSystemCode as supplierSystemCode, supplierName as supplierName");
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->from('srp_erp_taxmaster');
        $this->datatables->add_column('type', '$1', 'text_type(taxType)');
        $this->datatables->add_column('status', '$1', 'confirm(isActive)');
        $this->datatables->add_column('supplier', '( $1 ) $2', 'supplierSystemCode,supplierName');
        //$this->datatables->add_column('action', '<span class="pull-right"><a onclick="fetchPage(\'system/tax/erp_tax_new\',$1,\'Add Tax\',\'Tax\');"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;" rel="tooltip"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_tax($1,\'$2\')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>','taxMasterAutoID,taxShortCode');
        $this->datatables->add_column('action', '<span class="pull-right"><a onclick="fetchPage(\'system/tax/erp_tax_new\',$1,\'Add Tax\',\'Tax\');"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;" rel="tooltip"></span></a>&nbsp;&nbsp;&nbsp;</span>', 'taxMasterAutoID,taxShortCode');
        echo $this->datatables->generate();
    }

    function save_tax_header()
    {
        $this->form_validation->set_rules('taxDescription', 'taxDescription', 'trim|required');
        $this->form_validation->set_rules('supplierID', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('taxShortCode', 'taxShortCode', 'trim|required');

        $this->form_validation->set_rules('effectiveFrom', 'effectiveFrom', 'trim|required');
        $this->form_validation->set_rules('supplierGLAutoID', 'Liability Account', 'trim|required');
        $this->form_validation->set_rules('taxCategory', 'Tax Category', 'trim|required');
        $taxCategory = $this->input->post('taxCategory');
        if ($taxCategory == 2) {
            $this->form_validation->set_rules('inputVatGLAccountAutoID', 'Input VAT GL Account', 'trim|required');
            $this->form_validation->set_rules('inputVatTransferGLAccountAutoID', 'Input VAT Transfer GL Account', 'trim|required');
            $this->form_validation->set_rules('outputVatGLAccountAutoID', 'Output VAT GL Account', 'trim|required');
            $this->form_validation->set_rules('outputVatTransferGLAccountAutoID', 'Output VAT Transfer GL Account', 'trim|required');
            //$this->form_validation->set_rules('taxRegistrationNo', 'Tax Registration No', 'trim|required');
            //$this->form_validation->set_rules('taxIdentificationNo', 'Tax Identification No', 'trim|required');
        } else {
            $this->form_validation->set_rules('taxType', 'taxType', 'trim|required');
        }
        //$this->form_validation->set_rules('grvDate', 'Delivered Date', 'trim|required');
        //$this->form_validation->set_rules('location', 'Delivery Location', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Tax_modal->save_tax_header());
        }
    }

    function delete_tax()
    {
        echo json_encode($this->Tax_modal->delete_tax());
    }

    function laad_tax_header()
    {
        echo json_encode($this->Tax_modal->laad_tax_header());
    }

    function load_tax_group_master()
    {
        $this->datatables->select('taxGroupID,taxType,Description')
            ->from('srp_erp_taxgroup')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->edit_column('taxType', '$1', 'tax_groupMaster(taxType)')
            ->add_column('action', '<span class="pull-right"><a onclick="openTaxGgroupEdit($1);"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;" rel="tooltip"></span></span>', 'taxGroupID');
        echo $this->datatables->generate();
    }

    function save_tax_group_header()
    {
        $this->form_validation->set_rules('taxgroup', 'Tax Group', 'trim|required');
        $this->form_validation->set_rules('taxdescription', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Tax_modal->save_tax_group_header());
        }
    }

    function get_tax_group_edit()
    {
        if ($this->input->post('id') != "") {
            echo json_encode($this->Tax_modal->get_tax_group_edit());
        } else {
            echo json_encode(FALSE);
        }
    }

    function changesupplierGLAutoID()
    {
        echo json_encode($this->Tax_modal->changesupplierGLAutoID());
    }

    function get_tax_details()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $taxType = $this->input->post('taxType');
        $currency = $this->input->post('currency');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $this->form_validation->set_rules('taxType[]', 'Tax Type', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["taxtype"] = $this->Tax_modal->get_tax_type($taxType);
                $data["details"] = $this->Tax_modal->get_tax_details($taxType, $datefrom, $dateto);
                $data["currency"] = $currency;
                $data["type"] = "html";
                echo $html = $this->load->view('system/tax/load-tax-detail-report', $data, true);
            }
        }
    }

    function get_tax_details_report_pdf()
    {
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $taxType = $this->input->post('taxType');
        $data["taxtype"] = $this->Tax_modal->get_tax_type($taxType);
        $data["details"] = $this->Tax_modal->get_tax_details($taxType, $datefrom, $dateto);
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/tax/load-tax-detail-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function save_vat_main_category()
    {
        //   $this->form_validation->set_rules('taxMasterAutoID', 'Tax Master ID', 'trim|required');
        $this->form_validation->set_rules('mainCategoryDesc', 'VAT Main Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Tax_modal->save_vat_main_category());
        }
    }

    function fetch_VAT_main_category()
    {
        echo json_encode($this->Tax_modal->fetch_VAT_main_category());
    }

    function delete_vat_main_category()
    {
        echo json_encode($this->Tax_modal->delete_vat_main_category());
    }

    function load_vat_main_category()
    {
        echo json_encode($this->Tax_modal->load_vat_main_category());
    }

    function load_main_category_dropdown()
    {
        echo json_encode($this->Tax_modal->load_main_category_dropdown());
    }

    function save_vat_sub_category()
    {
        $this->form_validation->set_rules('taxMasterAutoID', 'Tax Master ID', 'trim|required');
        $this->form_validation->set_rules('mainCategoryVAT', 'VAT Main Category', 'trim|required');
        $this->form_validation->set_rules('subCategoryDesc', 'VAT Sub Category', 'trim|required');
        $this->form_validation->set_rules('subPercentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('applicableOn', 'Applicable On', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Tax_modal->save_vat_sub_category());
        }
    }

    function load_vat_sub_category()
    {
        echo json_encode($this->Tax_modal->load_vat_sub_category());
    }

    function fetch_VAT_sub_category()
    {
        echo json_encode($this->Tax_modal->fetch_VAT_sub_category());
    }

    function delete_vat_sub_category()
    {
        echo json_encode($this->Tax_modal->delete_vat_sub_category());
    }

    function fetch_VAT_add_item()
    {
        $taxVatSubCategoriesAutoID = $this->input->post('id');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('srp_erp_itemmaster.itemAutoID as itemAutoID,
                srp_erp_itemmaster.itemSystemCode as itemSystemCode,
                srp_erp_itemmaster.itemName as itemName,
                srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,
                srp_erp_itemmaster.itemImage,
                srp_erp_itemmaster.itemDescription as itemDescription,
                mainCategoryID,
                mainCategory as mainCategory,
                defaultUnitOfMeasure,
                companyLocalSellingPrice as companyLocalSellingPrice,
                srp_erp_itemmaster.companyLocalCurrency,
                srp_erp_itemmaster.companyLocalCurrencyDecimalPlaces,
                revanueDescription,costDescription,assteDescription,isActive,
                srp_erp_itemcategory.description as SubCategoryDescription', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');

        $this->datatables->where('srp_erp_itemmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_itemmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('(srp_erp_itemmaster.taxVatSubCategoriesAutoID !=' . $taxVatSubCategoriesAutoID . ' OR srp_erp_itemmaster.taxVatSubCategoriesAutoID IS NULL)');
        $this->datatables->where('srp_erp_itemmaster.masterApprovedYN', 1);
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        } else {
            $this->datatables->where('srp_erp_itemmaster.mainCategory IN ("Inventory","Non Inventory","Service","Services")');
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        echo $this->datatables->generate();
    }

    function assign_item_VAT()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('taxVatSubCategoriesAutoID', 'VAT Sub Category ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Tax_modal->assign_item_VAT());
        }
    }

    function fetch_VAT_item_view()
    {
        $taxVatSubCategoriesAutoID = $this->input->post('id');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('srp_erp_itemmaster.itemAutoID as itemAutoID,
                srp_erp_itemmaster.itemSystemCode as itemSystemCode,
                srp_erp_itemmaster.itemName as itemName,
                srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,
                srp_erp_itemmaster.itemImage,
                srp_erp_itemmaster.itemDescription as itemDescription,
                mainCategoryID,
                mainCategory as mainCategory,
                defaultUnitOfMeasure,
                companyLocalSellingPrice as companyLocalSellingPrice,
                srp_erp_itemmaster.companyLocalCurrency,
                srp_erp_itemmaster.companyLocalCurrencyDecimalPlaces,
                revanueDescription,costDescription,assteDescription,isActive,
                srp_erp_itemcategory.description as SubCategoryDescription', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');

        $this->datatables->where('srp_erp_itemmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_itemmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('srp_erp_itemmaster.taxVatSubCategoriesAutoID', $taxVatSubCategoriesAutoID);
        $this->datatables->where('srp_erp_itemmaster.masterApprovedYN', 1);
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        } else {
            $this->datatables->where('srp_erp_itemmaster.mainCategory IN ("Inventory","Non Inventory","Service","Services")');
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        echo $this->datatables->generate();
    }

    function load_output_vat_summary_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $currency = $this->input->post('currency');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">Date From is required</div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">Date To is required</div>';
        } else {
            $this->form_validation->set_rules('taxType[]', 'Tax Type', 'required');
            $this->form_validation->set_rules('documentType[]', 'Document Type', 'required');
            $this->form_validation->set_rules('customerAutoID[]', 'Customer', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data["details"] = $this->Tax_modal->load_output_vat_summary_report();
                $data["currency"] = $currency;
                $data["reportType"] = 'Output';
                $data["type"] = "html";

                echo $html = $this->load->view('system/tax/load_vat_report', $data, true);
            }
        }
    }

    function load_output_vat_summary_report_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Output VAT Summary Report');
        $this->load->database();
        $header = ['#', 'Document code', 'Document Types', 'Document Date',	'Invoice Code', 'Invoice Date', 'VAT No', 'Customer', 'Description', 'VAT Type',	'VAT Claimed',	'Approved By',	'Total Amount',	'VAT Amount'];
        $details = $this->Tax_modal->fetch_output_vat_summary_report_excel();

        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->fromArray(['Date From : ' . $datefrom . ' To : ' . $dateto], null, 'A4');
        $this->excel->getActiveSheet()->fromArray(['Currency : ' . $details['currency']], null, 'A5');
        $this->excel->getActiveSheet()->fromArray(['Documents : ' . $details['documents']], null, 'A6');
        $this->excel->getActiveSheet()->fromArray(['Tax Types : ' . $details['vatType']], null, 'A7');
        $this->excel->getActiveSheet()->fromArray(['Customers : ' . $details['customer']], null, 'A8');

        $this->excel->getActiveSheet()->mergeCells("A1:D1");
        $this->excel->getActiveSheet()->mergeCells("A2:D2");
        $this->excel->getActiveSheet()->mergeCells("A4:D4");
        $this->excel->getActiveSheet()->mergeCells("A5:D5");
        $this->excel->getActiveSheet()->mergeCells("A6:D6");
        $this->excel->getActiveSheet()->mergeCells("A7:D7");
        $this->excel->getActiveSheet()->mergeCells("A8:Z8");
        $this->excel->getActiveSheet()->getStyle('A1:Z8')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1:Z8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A10:N10')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A10:N10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('cee2f3');
        $this->excel->getActiveSheet()->fromArray(['Output VAT Summary Report'], null, 'A2');
        $this->excel->getActiveSheet()->fromArray($header, null, 'A10');
        $this->excel->getActiveSheet()->fromArray($details['data'], null, 'A12');
        
        $filename = 'Output VAT Summary Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function load_input_vat_summary_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $currency = $this->input->post('currency');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">Date From is required</div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">Date To is required</div>';
        } else {
            $this->form_validation->set_rules('taxType[]', 'Tax Type', 'required');
            $this->form_validation->set_rules('documentType[]', 'Document Type', 'required');
            $this->form_validation->set_rules('supplierAutoID[]', 'Supplier', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data["details"] = $this->Tax_modal->load_input_vat_summary_report();
                $data["currency"] = $currency;
                $data["reportType"] = 'Input';
                $data["type"] = "html";
                echo $html = $this->load->view('system/tax/load_vat_report', $data, true);
            }
        }
    }

    function load_input_vat_summary_report_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Input VAT Summary Report');
        $this->load->database();

        $header = ['#', 'Document code', 'Document Types', 'Document Date',	'Invoice Code', 'Invoice Date', 'VAT No', 'Supplier', 'Description', 'VAT Type',	'VAT Claimed',	'Approved By',	'Total Amount',	'VAT Amount'];
        $details = $this->Tax_modal->fetch_input_vat_summary_report_excel();

        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->fromArray(['Date From : ' . $datefrom . ' To : ' . $dateto], null, 'A4');
        $this->excel->getActiveSheet()->fromArray(['Currency : ' . $details['currency']], null, 'A5');
        $this->excel->getActiveSheet()->fromArray(['Documents : ' . $details['documents']], null, 'A6');
        $this->excel->getActiveSheet()->fromArray(['Tax Types : ' . $details['vatType']], null, 'A7');
        $this->excel->getActiveSheet()->fromArray(['Suppliers : ' . $details['suppliers']], null, 'A8');

        $this->excel->getActiveSheet()->mergeCells("A1:D1");
        $this->excel->getActiveSheet()->mergeCells("A2:D2");
        $this->excel->getActiveSheet()->mergeCells("A4:D4");
        $this->excel->getActiveSheet()->mergeCells("A5:D5");
        $this->excel->getActiveSheet()->mergeCells("A6:D6");
        $this->excel->getActiveSheet()->mergeCells("A7:D7");
        $this->excel->getActiveSheet()->mergeCells("A8:Z8");
        $this->excel->getActiveSheet()->getStyle('A1:Z8')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1:Z8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A10:N10')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A10:N10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('cee2f3');
        $this->excel->getActiveSheet()->fromArray(['Input VAT Summary Report'], null, 'A2');
        $this->excel->getActiveSheet()->fromArray($header, null, 'A10');
        $this->excel->getActiveSheet()->fromArray($details['data'], null, 'A12');
        
        $filename = 'Input VAT Summary Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function delete_tax_formula_master()
    {
        echo json_encode($this->Tax_modal->delete_tax_formula_master());
    }

    function load_output_vat_return_filling_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $currency = $this->input->post('currency');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">Date From is required</div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">Date To is required</div>';
        } else {
            $this->form_validation->set_rules('taxType[]', 'Tax Type', 'required');
            $this->form_validation->set_rules('documentType[]', 'Document Type', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data["details"] = $this->Tax_modal->load_output_vat_return_filling_report();
                $data["currency"] = $currency;
                $data["reportType"] = 'Output';
                $data["type"] = "html";
                echo $html = $this->load->view('system/tax/load_vat_return_filing_report', $data, true);
            }
        }
    }

    function load_input_vat_return_filling_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $currency = $this->input->post('currency');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">Date From is required</div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">Date To is required</div>';
        } else {
            $this->form_validation->set_rules('taxType[]', 'Tax Type', 'required');
            $this->form_validation->set_rules('documentType[]', 'Document Type', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data["details"] = $this->Tax_modal->load_input_vat_return_filling_report();
                $data["currency"] = $currency;
                $data["reportType"] = 'Input';
                $data["type"] = "html";
                echo $html = $this->load->view('system/tax/load_vat_return_filing_report', $data, true);
            }
        }
    }
}