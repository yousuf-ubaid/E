<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ItemMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Item_model');
        $this->load->model('Erp_data_sync_model');
        $this->load->helpers('asset_management');
        $this->load->library('s3');
        
    }

    function fetch_item()
    {
        $itemStatus=$this->input->post('activeStatus');
        $deletedYN = $this->input->post('deletedYN');
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];
        $showPurchasePrice = getPolicyValues('SPP', 'All');
        if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
            $showPurchasePrice = 0;
        }

        $this->datatables->select('itemAutoID,srp_erp_itemmaster.deletedYN as deletedYN, itemSystemCode,itemName,srp_erp_itemmaster.itemImage as itemImage ,seconeryItemCode, partNo as partNo,
                                    itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure, companyLocalSellingPrice,companyLocalCurrency,
                                    companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,
                                    subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription, currentStock,
                                    CASE WHEN mainCategory = "Service" THEN CONCAT(0, \'  \', defaultUnitOfMeasure) WHEN mainCategory = "Non Inventory" THEN CONCAT(0, \'  \', defaultUnitOfMeasure) ELSE CONCAT(currentStock, \'  \', defaultUnitOfMeasure) END AS CurrentStock, 
                                    CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription," | ",barcode) as description,
                                    isSubitemExist, masterConfirmedYN, masterApprovedYN,companyReportingSellingPrice,companyLocalPurchasingPrice', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        if (!empty($itemStatus)) {
            if($itemStatus==1){
                $this->datatables->where('isActive', 1);
            }elseif($itemStatus==2){
                $this->datatables->where('isActive', 0);
            }
        }
        $this->datatables->where('deletedYN', $deletedYN);
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
//        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,'.$defaultdecimal.'),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('itemImage','$1', 'get_item_master_image(itemImage,itemAutoID)'); //Item master image
        $this->datatables->add_column('itemApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN, masterApprovedYN,"AIM","INV",itemAutoID)');

        //$this->datatables->add_column('itemApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN, masterApprovedYN,"AIM")');

        //$this->datatables->add_column('img', "<a onclick='change_img(\"$2\",\"$3/$1\")'><img class='img-thumbnail' src='$3/$1' style='width:120px;height: 80px;' ></a>", 'itemImage,itemAutoID,base_url("images/item/")');
        $this->datatables->add_column('edit', '$1', 'load_item_master_action(itemAutoID,isActive,isSubitemExist, deletedYN,masterConfirmedYN,masterApprovedYN)');
   
       if($showPurchasePrice == 1){
            $this->datatables->add_column('price', '<b>Prch Price: </b>$3 $1 <br><b>Sales Price: </b>$3 $2', 'format_number(companyLocalPurchasingPrice,'.$defaultdecimal.'),number_format(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }else{
            $this->datatables->add_column('price', '<b>Sales Price: </b>$2 $1', 'format_number(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }


        echo $this->datatables->generate();

        //var_dump($this->db->last_query());exit;

    }

    function fetch_item_pending()
    {
        $itemStatus=$this->input->post('activeStatus');
        $deletedYN = $this->input->post('deletedYN');
        $currentUserID = current_userID();
        // $masterApprovedYN= $this->input->post('itemApprovalStatus');
       
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];
        $showPurchasePrice = getPolicyValues('SPP', 'All');
        if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
            $showPurchasePrice = 0;
        }

        $this->datatables->select('itemAutoID, srp_erp_itemmaster.deletedYN as deletedYN, itemSystemCode, itemName, srp_erp_itemmaster.itemImage as itemImage, seconeryItemCode, partNo as partNo,
    itemDescription, mainCategoryID, mainCategory, defaultUnitOfMeasure, companyLocalSellingPrice, companyLocalCurrency,
    companyLocalCurrencyDecimalPlaces, revanueDescription, costDescription, assteDescription, isActive, companyLocalWacAmount,
    subcat.description as SubCategoryDescription, subsubcat.description as SubSubCategoryDescription, currentStock,
    CASE WHEN mainCategory = "Service" THEN CONCAT(0, \'  \', defaultUnitOfMeasure) WHEN mainCategory = "Non Inventory" THEN CONCAT(0, \'  \', defaultUnitOfMeasure) ELSE CONCAT(currentStock, \'  \', defaultUnitOfMeasure) END AS CurrentStock,
    CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount, CONCAT(itemSystemCode," - ",itemDescription," | ",barcode) as description,
    isSubitemExist, masterConfirmedYN, masterApprovedYN, companyReportingSellingPrice, companyLocalPurchasingPrice', false)
    ->from('srp_erp_itemmaster')
    ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
    ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID', 'left')
    ->join('srp_erp_documentapproved dca', 'dca.documentID = "INV" and dca.documentSystemCode = srp_erp_itemmaster.itemAutoID and dca.approvalLevelID = srp_erp_itemmaster.masterCurrentLevelNo')
    ->join('srp_erp_approvalusers app', 'app.documentID = "INV" and app.levelNo = dca.approvalLevelID')
    ->where('srp_erp_itemmaster.isActive', 1)
    ->where('srp_erp_itemmaster.deletedYN', '0')
    ->where('srp_erp_itemmaster.masterApprovedYN', 0)
    ->where('srp_erp_itemmaster.masterConfirmedYN', 1)
    ->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id'])
    ->where('app.employeeID', $currentUserID);

if (!empty($this->input->post('mainCategory'))) {
    $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
}
if (!empty($this->input->post('subcategory'))) {
    $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
}
if (!empty($this->input->post('subsubcategoryID'))) {
    $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
}
if (!empty($itemStatus)) {
    if ($itemStatus == 1) {
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
    } elseif ($itemStatus == 2) {
        $this->datatables->where('srp_erp_itemmaster.isActive', 0);
    }
}

$this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,' . $defaultdecimal . '), companyLocalCurrency')
    ->add_column('confirmed', '$1', 'confirm(isActive)')
    ->add_column('itemImage', '$1', 'get_item_master_image(itemImage,itemAutoID)') //Item master image
    ->add_column('itemApprovalStatus', '$1', 'approvalStatus(isActive, masterConfirmedYN, masterApprovedYN,"AIM","INV",itemAutoID)')
    ->add_column('edit', '$1', 'edit_item(itemAutoID, isActive, isSubitemExist, deletedYN, masterConfirmedYN, masterApprovedYN)');

if ($showPurchasePrice == 1) {
    $this->datatables->add_column('price', '<b>Prch Price: </b>$3 $1 <br><b>Sales Price: </b>$3 $2', 'format_number(companyLocalPurchasingPrice,' . $defaultdecimal . '),number_format(companyLocalSellingPrice,' . $defaultdecimal . '),companyLocalCurrency');
} else {
    $this->datatables->add_column('price', '<b>Sales Price: </b>$2 $1', 'format_number(companyLocalSellingPrice,' . $defaultdecimal . '),companyLocalCurrency');
}
       
            
             echo $this->datatables->generate();
            //  echo $this->db->last_query();exit;
    }
    function fetch_item_pricing()
    {
        $itemStatus=$this->input->post('activeStatus');
        $deletedYN = $this->input->post('deletedYN');
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];
        $showPurchasePrice = getPolicyValues('SPP', 'All');
        if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
            $showPurchasePrice = 0;
        }

        $this->datatables->select('itemAutoID,srp_erp_itemmaster.deletedYN as deletedYN, itemSystemCode,itemName,srp_erp_itemmaster.itemImage as itemImage ,seconeryItemCode, partNo as partNo,
                                    itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure, companyLocalSellingPrice,companyLocalCurrency,
                                    companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,
                                    subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription, currentStock,
                                    CASE WHEN mainCategory = "Service" THEN CONCAT(0, \'  \', defaultUnitOfMeasure) WHEN mainCategory = "Non Inventory" THEN CONCAT(0, \'  \', defaultUnitOfMeasure) ELSE CONCAT(currentStock, \'  \', defaultUnitOfMeasure) END AS CurrentStock, 
                                    CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription," | ",barcode) as description,
                                    isSubitemExist, masterConfirmedYN, masterApprovedYN,companyReportingSellingPrice,companyLocalPurchasingPrice', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        if (!empty($itemStatus)) {
            if($itemStatus==1){
                $this->datatables->where('isActive', 1);
            }elseif($itemStatus==2){
                $this->datatables->where('isActive', 0);
            }
        }
        $this->datatables->where('deletedYN', $deletedYN);
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
//        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,'.$defaultdecimal.'),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('itemImage','$1', 'get_item_master_image(itemImage,itemAutoID)'); //Item master image
        $this->datatables->add_column('itemApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN, masterApprovedYN,"AIM","INV",itemAutoID)');

        //$this->datatables->add_column('itemApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN, masterApprovedYN,"AIM")');

        //$this->datatables->add_column('img', "<a onclick='change_img(\"$2\",\"$3/$1\")'><img class='img-thumbnail' src='$3/$1' style='width:120px;height: 80px;' ></a>", 'itemImage,itemAutoID,base_url("images/item/")');
        $this->datatables->add_column('action', '$1', 'item_pricing(itemAutoID,isActive,isSubitemExist, deletedYN,masterConfirmedYN,masterApprovedYN)');
   
       if($showPurchasePrice == 1){
            $this->datatables->add_column('price', '<b>Prch Price: </b>$3 $1 <br><b>Sales Price: </b>$3 $2', 'format_number(companyLocalPurchasingPrice,'.$defaultdecimal.'),number_format(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }else{
            $this->datatables->add_column('price', '<b>Sales Price: </b>$2 $1', 'format_number(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }


        echo $this->datatables->generate();

        //var_dump($this->db->last_query());exit;

    }

    function save_itemmaster()
    {
        $maincategory = $this->db->query("SELECT itemCategoryID,categoryTypeID,codePrefix FROM srp_erp_itemcategory WHERE itemCategoryID ={$this->input->post('mainCategoryID')}")->row_array();
        $secondaryUOM = getPolicyValues('SUOM', 'All');
        $barcode=$this->input->post('barcode');
        if (!$this->input->post('itemAutoID')) {
            $this->form_validation->set_rules('mainCategoryID', 'Main category', 'trim|required');
            $this->form_validation->set_rules('defaultUnitOfMeasureID', 'Unit of messure', 'trim|required');
            if(!empty($barcode)){
                $barcdexist = $this->db->query("SELECT barcode FROM srp_erp_itemmaster WHERE barcode ='$barcode' AND deletedYN = 0")->row_array();
                if(!empty($barcdexist)){
                    echo json_encode(array('e', 'Barcode already exist'));
                    Exit;
                }
            }

        }else{
            if(!empty($barcode)) {
                $barcdexist = $this->db->query("SELECT barcode FROM srp_erp_itemmaster WHERE barcode ='$barcode' AND itemAutoID !={$this->input->post('itemAutoID')} AND deletedYN = 0")->row_array();
                if (!empty($barcdexist)) {
                    echo json_encode(array('e', 'Barcode already exist'));
                    Exit;
                }
            }
        }
        if ($maincategory['categoryTypeID'] == 3) {
            $this->form_validation->set_rules('COSTGLCODEdes', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLCODEdes', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLCODEdes', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLCODEdes', 'Disposal GL Code', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 1) {
            $this->form_validation->set_rules('assteGLAutoID', 'Asset GL Code', 'trim|required');
            $this->form_validation->set_rules('revanueGLAutoID', 'Revenue GL Code', 'trim|required');
            $this->form_validation->set_rules('costGLAutoID', 'Cost GL Code', 'trim|required');
            $this->form_validation->set_rules('stockadjust', 'Stock Adjustment GL Code', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 2) {
            $this->form_validation->set_rules('costGLAutoID', 'Cost GL Code', 'trim|required');
        }

        $subsubCategoryBaseNewSequencePolicy = getPolicyValues('IMSSNS', 'All');
        $companyID = $this->common_data['company_data']['company_id'];

        if($subsubCategoryBaseNewSequencePolicy==1){
            $inv = $this->db->query("SELECT * FROM srp_erp_documentcodemaster WHERE documentID ='{$maincategory['codePrefix']}' AND companyID='{$companyID}'")->row_array();
       
            if($inv){
                if($inv['format_4']=='subsubCat'){
                    $this->form_validation->set_rules('subSubCategoryID', 'Sub Sub Category', 'trim|required');
                }
    
                if($inv['format_5']=='subsubsubCat'){
                    $this->form_validation->set_rules('subSubSubCategoryID', 'Sub Sub Sub Category', 'trim|required');
                }
            }
        }

        $this->form_validation->set_rules('seconeryItemCode', 'Seconery Item Code', 'trim|required');
        $this->form_validation->set_rules('itemName', 'Item Name', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item Full Name', 'trim|required');
        $this->form_validation->set_rules('subcategoryID', 'Sub category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));

        } else {

            echo json_encode($this->Item_model->save_item_master());
        }
    }

    function img_uplode()
    {
        $this->form_validation->set_rules('item_id', 'Item ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Item_model->img_uplode());
        }
    }

    function load_item_header()
    {
        $data = $this->Item_model->load_item_header();

        $empImage = $this->s3->createPresignedRequest('uploads/itemMaster/'.$data['itemImage'], '1 hour');
        $itemnoimage = $this->s3->createPresignedRequest('images/item/no-image.png', '1 hour');
       // $data['emp'];
        $data['emp'] = $empImage;
        $data['item_no_image'] = $itemnoimage;

        echo json_encode($data);
    }

    function load_subcat()
    {
        echo json_encode($this->Item_model->load_subcat());
    }

    function load_se_code_temp()
    {
        echo json_encode($this->Item_model->load_se_code_temp());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Item_model->load_subsubcat());
    }
    function load_sub_item()
    {
        echo json_encode($this->Item_model->load_subitem());
    }



    function edit_item()
    {
        if ($this->input->post('id') != "") {
            echo json_encode($this->Item_model->edit_item());
        } else {
            echo json_encode(FALSE);
        }
    }

    function item_master_img_uplode()
    {

        echo json_encode($this->Item_model->item_master_img_uplode());
    }

    function delete_item()
    {
        echo json_encode($this->Item_model->delete_item());
    }

    function load_gl_codes()
    {
        echo json_encode($this->Item_model->load_gl_codes());
    }
    function load_item_gl_code(){
        echo json_encode($this->Item_model->load_item_gl_codes());
    }

    function changeitemactive()
    {
        echo json_encode($this->Item_model->changeitemactive());

    }

    function load_category_type_id()
    {
        echo json_encode($this->Item_model->load_category_type_id());

    }

    function load_unitprice_exchangerate()
    {
        echo json_encode($this->Item_model->load_unitprice_exchangerate());

    }

    function fetch_sales_price()
    {
        echo json_encode($this->Item_model->fetch_sales_price());
    }

    function fetch_sales_price_customerWise()
    {
        echo json_encode($this->Item_model->fetch_sales_price_customerWise());
    }

    function fetch_customer_details_for_pricing()
    {
        echo json_encode($this->Item_model->fetch_customer_details_for_pricing());
    }

    function fetch_outlet_details_for_pricing()
    {
        echo json_encode($this->Item_model->fetch_outlet_details_for_pricing());
    }

    function fetch_item_details_for_pricing()
    {
        echo json_encode($this->Item_model->fetch_item_details_for_pricing());
    }

    function save_item_pricing_detail()
    {

        $type = $this->input->post('type');

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');

        if(in_array($type,array('Selected'))){
            $this->form_validation->set_rules("customer[]", 'Customer', 'trim|required');
        }

        if(in_array($type,array('Direct'))){
            $this->form_validation->set_rules("outlet", 'Outlet', 'trim|required');
        }

        $this->form_validation->set_rules("type", 'Type', 'trim|required');
        $this->form_validation->set_rules("cost", 'Cost', 'trim|required');
        $this->form_validation->set_rules("margin", 'Margin', 'trim|required');
        $this->form_validation->set_rules("salesprice", 'Sales Price', 'trim|required');
        $this->form_validation->set_rules("discount", 'Discount', 'trim|required');
        $this->form_validation->set_rules("rsalesprice", 'Rsales Price', 'trim|required');
        $this->form_validation->set_rules("profit", 'Profit', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Item_model->save_item_pricing_detail());
        }
    }

    function save_part_number_detail()
    {

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');


        $this->form_validation->set_rules("supplier", 'Supplier', 'trim|required');
        $this->form_validation->set_rules("partNumber", 'Part Number', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Item_model->save_part_number_detail());
        }
    }

    function load_item_pricing_data(){
        //discount,rSalesPrice,profit,isDefault
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];
        $this->datatables->select('pricingAutoID,pricingType,customer,outlet,cost,margin,salesPrice,discount,rSalesPrice,profit,isDefault,isActive,companyLocalCurrencyCode,uomMasterID')
                ->where('itemMasterID', $this->input->post('itemAutoID'))
                ->from('srp_erp_item_master_pricing')
                //$this->datatables->add_column('action', '$1', 'item_pricing_edit(pricingAutoID)');
                ->edit_column('action', '<span class="pull-right" ><a onclick="openaddressmodel($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="deleteaddress($1)"><span title="Delete" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"  rel="tooltip"></span></a></span>', 'pricingAutoID');
                $this->datatables->add_column('isdefault', '$1', 'isDefaultYN(isDefault)');
                $this->datatables->edit_column('cost', '$1', 'format_number(cost,'.$defaultdecimal.')');
                $this->datatables->edit_column('salesPrice', '$1', 'format_number(salesPrice,'.$defaultdecimal.')');
                $this->datatables->edit_column('rSalesPrice', '$1', 'format_number(rSalesPrice,'.$defaultdecimal.')');
                $this->datatables->edit_column('profit', '$1', 'format_number(profit,'.$defaultdecimal.')');
                $this->datatables->add_column('uom', '$1', 'select_uom_shortcode(uomMasterID)');
                $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
                echo $this->datatables->generate();
    }

    function load_item_part_number_data(){
        $this->datatables->select('partNumberAutoID,supplierSystemCode,partNumber,isActive')
            ->where('itemAutoID', $this->input->post('itemAutoID'))
            ->from('srp_erp_item_part_number')
            ->edit_column('action', '<span class="pull-right" ><a onclick="editItemPartNumber($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="deleteItemPartNumber($1)"><span title="Delete" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"  rel="tooltip"></span></a></span>', 'partNumberAutoID');
            $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
            echo $this->datatables->generate();
    }

    function edit_item_pricing()
    {
        if($this->input->post('id') !=""){
            echo json_encode($this->Item_model->edit_item_pricing());
        }
        else{
            echo json_encode(FALSE);
        }
    }

    function edit_item_part_number()
    {
        if($this->input->post('id') !=""){
            echo json_encode($this->Item_model->edit_item_part_number());
        }
        else{
            echo json_encode(FALSE);
        }
    }

    function delete_item_pricing()
    {
        echo json_encode($this->Item_model->delete_item_pricing());
    }

    function delete_item_part_number(){
        echo json_encode($this->Item_model->delete_item_part_number());
    }

    function item_image_upload()
    {
        $this->form_validation->set_rules('faID', 'Document Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Item_model->item_image_upload());
        }
    }

    function fetch_subItem()
    {
        $itemCode = $this->input->post('itemCode');

        $this->datatables->select('itemmaster_sub.*, wh.wareHouseDescription as warehouseDescription', false)
            ->from('srp_erp_itemmaster_sub itemmaster_sub')
            ->join('srp_erp_itemmaster itemmaster', 'itemmaster.itemAutoID = itemmaster_sub.itemAutoID', 'left')
            ->join('srp_erp_warehousemaster wh', 'wh.wareHouseAutoID = itemmaster_sub.wareHouseAutoID', 'left');
        $this->datatables->where('itemmaster.itemAutoID', $itemCode);
        $this->datatables->where("(itemmaster_sub.isSold <> 1 OR itemmaster_sub.isSold IS NULL)");


        /*$this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'number_format(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');*/
        /*$this->datatables->add_column('edit', '$1', 'edit(itemAutoID,isActive,isSubitemExist)');*/

        echo $this->datatables->generate();
        //echo $this->db->last_query();
    }

    function load_sub_itemMaster_view()
    {
        $itemCode = $this->input->post('itemCode');
        $output = $this->Item_model->load_sub_itemMaster_view($itemCode);
        $data['attributes'] = fetch_company_assigned_attributes();
        $data['itemMasterSubTemp'] = $output;

        $this->load->view('system/item/itemmastersub/ajax-item-master-list-view-modal', $data);
    }

    function fetch_item_percentage()
    {
        $this->datatables->select('itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist,finCompanyPercentage,pvtCompanyPercentage', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('DT_RowId', 'common_$1', 'itemAutoID');
        $this->datatables->add_column('fc', '<input style="width: 70%" type="text" class="form-control fc number"
                                   value="$1"
                                   name="finCompanyPercentage[]" onkeyup="validatePercentage(this,\'fc\')" onkeypress="return validateFloatKeyPress(this,event)">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'finCompanyPercentage');
        $this->datatables->add_column('pc', '<input style="width: 70%" type="text" class="form-control pc number"
                                   value="$2"
                                   name="pvtCompanyPercentage[]" onkeyup="validatePercentage(this,\'pc\')" onkeypress="return validateFloatKeyPress(this,event,5)">
                                   <input type="hidden" name="itemAutoID[]" value="$1">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'itemAutoID,pvtCompanyPercentage');

        echo $this->datatables->generate();
    }

    function save_item_percentage(){
        echo json_encode($this->Item_model->save_item_percentage());
    }

    function save_item_bin_location(){
        $binLocationID=$this->input->post('binLocationID');
        $itemBinlocationID=$this->input->post('itemBinlocationID');
        $this->form_validation->set_rules("itemAutoID", 'Item AutoID', 'trim|required');
        if(empty($itemBinlocationID) && $binLocationID!=''){
            $this->form_validation->set_rules("binLocationID", 'Select Bin Location', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            //$this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Item_model->save_item_bin_location());
        }
    }

    function load_item_bin_location(){
        echo json_encode($this->Item_model->load_item_bin_location());
    }

    /*function item_master_excelUpload(){
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0; $n = 0;
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    $dataExcel[$i]['ShortDescription'] = $getData[1];
                    $dataExcel[$i]['LongDescription'] = $getData[2];
                    $dataExcel[$i]['SecondaryCode'] = $getData[3];
                    $dataExcel[$i]['SellingPrice'] = $getData[4];
                    $dataExcel[$i]['Barcode'] = $getData[5];
                    $dataExcel[$i]['PartNo'] = $getData[6];
                    $dataExcel[$i]['MaximumQty'] = $getData[7];
                    $dataExcel[$i]['minimumQty'] = $getData[8];
                    $dataExcel[$i]['ReorderLevel'] = $getData[9];
                }
                $i++;
            }
            fclose($file);


            if(!empty($dataExcel)){
                echo json_encode(['m',$dataExcel]);
                //$this->load->view('system/item/itemmastersub/ajax-item-master-list-view-modal', $dataExcel);
            }else{
                echo json_encode(['e', 'No records in the uploaded file']);
            }
        }else{
            echo json_encode(['e', 'No Files Attached']);
        }
    }*/

    function item_master_excelUpload(){
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0; $n = 0;
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    $dataExcel[$i]['itemName'] = $getData[1];
                    $dataExcel[$i]['itemDescription'] = $getData[2];
                    $dataExcel[$i]['seconeryItemCode'] = $getData[3];
                    $dataExcel[$i]['companyLocalSellingPrice'] = $getData[4];
                    $dataExcel[$i]['barcode'] = $getData[5];
                    $dataExcel[$i]['partNo'] = $getData[6];
                    $dataExcel[$i]['maximunQty'] = $getData[7];
                    $dataExcel[$i]['minimumQty'] = $getData[8];
                    $dataExcel[$i]['reorderPoint'] = $getData[9];
                    $dataExcel[$i]['companyID'] = current_companyID();
                }
                $i++;
            }
            fclose($file);


            if(!empty($dataExcel)){
                $result=$this->db->insert_batch('srp_erp_itemmaster_temp', $dataExcel);
                if($result){
                    echo json_encode(['s','Successfully Updated']);
                }else{
                    echo json_encode(['e', 'Upload Failed']);
                }

                //$this->load->view('system/item/itemmastersub/ajax-item-master-list-view-modal', $dataExcel);
            }else{
                echo json_encode(['e', 'No records in the uploaded file']);
            }
        }else{
            echo json_encode(['e', 'No Files Attached']);
        }
    }

    function saveMultipleItemMaster(){
        $mainCategoryID = $this->input->post('mainCategoryID');
        $mainCategoryIDselect = $this->input->post('mainCategoryIDselect');
        $itemAutoIDhn = $this->input->post('itemAutoIDhn');
        $secondaryUOM = getPolicyValues('SUOM', 'All');
        foreach ($itemAutoIDhn as $key => $search) {
            $maincategory = $this->db->query("SELECT itemCategoryID,categoryTypeID FROM srp_erp_itemcategory WHERE itemCategoryID ={$mainCategoryIDselect}")->row_array();

            if ($maincategory['categoryTypeID'] == 3) {
                $this->form_validation->set_rules("COSTGLCODEdes[{$key}]", 'Cost Account', 'trim|required');
                $this->form_validation->set_rules("ACCDEPGLCODEdes[{$key}]", 'Acc Dep GL Code', 'trim|required');
                $this->form_validation->set_rules("DEPGLCODEdes[{$key}]", 'Dep GL Code', 'trim|required');
                $this->form_validation->set_rules("DISPOGLCODEdes[{$key}]", 'Disposal GL Code', 'trim|required');
            }
            if ($maincategory['categoryTypeID'] == 1) {
                $this->form_validation->set_rules("assteGLAutoID[{$key}]", 'Asset GL Code', 'trim|required');
                $this->form_validation->set_rules("revanueGLAutoID[{$key}]", 'Revenue GL Code', 'trim|required');
                $this->form_validation->set_rules("costGLAutoID[{$key}]", 'Cost GL Code', 'trim|required');
                $this->form_validation->set_rules("stockadjust[{$key}]", 'Stock Adjustment GL Code', 'trim|required');
            }
            if ($maincategory['categoryTypeID'] == 2) {
                //$this->form_validation->set_rules('revanueGLAutoID', 'Revanue GL Code', 'trim|required');
                $this->form_validation->set_rules("costGLAutoID[{$key}]", 'Cost GL Code', 'trim|required');
            }

            //$this->form_validation->set_rules("mainCategoryID[{$key}]", 'Main Category ID', 'trim|required');
            $this->form_validation->set_rules("defaultUnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            if($secondaryUOM == 1){
                $this->form_validation->set_rules("secondaryUnitOfMeasureID[{$key}]", 'Secondary Unit Of Measure', 'trim|required');
            }
            //$this->form_validation->set_rules("seconeryItemCode[{$key}]", 'Secondary Code', 'trim|required');
            //$this->form_validation->set_rules("itemName[{$key}]", 'Unit of Measure', 'trim|required');
            //$this->form_validation->set_rules("itemDescription[{$key}]", 'Long Description', 'trim|required');
            $this->form_validation->set_rules("subcategoryID[{$key}]", 'Sub Category', 'trim|required');
        }
            if ($this->form_validation->run() == FALSE) {
                $msg = explode('</p>', validation_errors());
                $trimmed_array = array_map('trim', $msg);
                $uniqMesg = array_unique($trimmed_array);
                $validateMsg = array_map(function ($uniqMesg) {
                    return $a = $uniqMesg . '</p>';
                }, array_filter($uniqMesg));
                echo json_encode(array('e', join('', $validateMsg)));
            }else{
                $inc=0;
                $barcode = $this->input->post('barcode');
                $itemName = $this->input->post('itemName');
                $itemDescription = $this->input->post('itemDescription');
                $seconeryItemCode = $this->input->post('seconeryItemCode');
                foreach($itemAutoIDhn as $key => $mainCateg) {
                    if (!empty($barcode[$key])) {
                        $barcode = $barcode[$key];
                    } else {
                        $barcode = '';
                    }
                    $barcodeexist = $this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' ")->row_array();
                    if ($barcodeexist && !empty($barcode)) {
                        $inc++;
                        echo json_encode( array('e', 'Barcode already exist. ('.$itemName[$key].')'));
                        exit;
                    }

                    /*$itemNameexist = $this->db->query("SELECT itemAutoID FROM `srp_erp_itemmaster` WHERE itemName= '$itemName[$key]' ")->row_array();
                    if (!empty($itemNameexist)) {
                        $inc++;
                        echo json_encode( array('e', 'Short Description already exist. ('.$itemName[$key].')'));
                        exit;
                    }*/

                    /*$itemDescriptionexist = $this->db->query("SELECT itemAutoID FROM `srp_erp_itemmaster` WHERE itemDescription= '$itemDescription[$key]' ")->row_array();
                    if (!empty($itemDescriptionexist)) {
                        $inc++;
                        echo json_encode( array('e', 'Long Description already exist. ('.$itemName[$key].')'));
                        exit;
                    }*/

                    /*$itemsecCodeexist = $this->db->query("SELECT itemAutoID FROM `srp_erp_itemmaster` WHERE seconeryItemCode= '$seconeryItemCode[$key]' ")->row_array();
                    if (!empty($itemsecCodeexist)) {
                        $inc++;
                        echo json_encode( array('e', 'Secondary Code already exist. ('.$itemName[$key].')'));
                        exit;
                    }*/
                }
                if($inc==0){
                    echo json_encode($this->Item_model->saveMultipleItemMaster());
                }

            }

    }


    function fetch_item_master_server()
    {
        $secondaryUOM = getPolicyValues('SUOM', 'All');
        $main_category_arr = all_main_category_drop();
        $uom_arr = all_umo_new_drop();
        $revenue_gl_arr = all_revenue_gl_drop();
        $cost_gl_arr = all_cost_gl_drop();
        $asset_gl_arr = all_asset_gl_drop();
        $fetch_cost_account = fetch_cost_account();
        $fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
        $fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
        $stock_adjustment = stock_adjustment_control_drop();
        $mainCategoryIDdrp =  form_dropdown('mainCategoryID[]', $main_category_arr, '$1', 'class="form-control mainCategoryID" onchange="load_sub_cat_bulk(this)"');
        $defaultUnitOfMeasureIDdrp =  form_dropdown('defaultUnitOfMeasureID[]', $uom_arr, '$1', 'class="form-control defaultUnitOfMeasureID" required');
        $secondaryUnitOfMeasureIDdrp =  form_dropdown('secondaryUnitOfMeasureID[]', $uom_arr, '$1', 'class="form-control secondaryUnitOfMeasureID" required');
        $revanueGLAutoIDdrp =  form_dropdown('revanueGLAutoID[]', $revenue_gl_arr, '$1', 'class="form-control select2 revanueGLAutoID " ');
        $costGLAutoIDdrp =  form_dropdown('costGLAutoID[]', $cost_gl_arr, '', 'class="form-control select2 costGLAutoID " ');
        $assteGLAutoIDdrp =  form_dropdown('assteGLAutoID[]', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'class="form-control select2 assteGLAutoID " ');
        $faCostGLAutoIDdrp =  form_dropdown('COSTGLCODEdes[]', $fetch_cost_account, '$1', 'class="form-control form1 select2 COSTGLCODEdes "');
        $faACCDEPGLAutoIDdrp =  form_dropdown('ACCDEPGLCODEdes[]', $fetch_cost_account, '$1', 'class="form-control form1 select2 ACCDEPGLCODEdes" ');
        $faDEPGLAutoIDdrp =  form_dropdown('DEPGLCODEdes[]', $fetch_dep_gl_code, '$1', 'class="form-control form1 select2 DEPGLCODEdes "  ');
        $faDISPOGLAutoIDdrp =  form_dropdown('DISPOGLCODEdes[]', $fetch_disposal_gl_code, '$1', 'class="form-control form1 select2 DISPOGLCODEdes "');
        $stockAdjustmentGLAutoIDdrp =  form_dropdown('stockadjust[]', $stock_adjustment, '$1', 'class="form-control form1 select2 stockadjust " ');


        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_itemmaster_temp.companyID = " . $companyid .  "";
        $this->datatables->select('itemAutoID,mainCategoryID,subcategoryID,subSubCategoryID,itemName,itemDescription,seconeryItemCode,defaultUnitOfMeasureID,secondaryUnitOfMeasureID,companyLocalSellingPrice,barcode,partNo,maximunQty,minimumQty,reorderPoint,revanueGLAutoID,costGLAutoID,assteGLAutoID,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID,stockAdjustmentGLAutoID')
            ->where($where)
            ->from('srp_erp_itemmaster_temp');
        $this->datatables->add_column('DT_RowId', 'common_$1', 'itemAutoID');


        $this->datatables->add_column('mainCategoryIDdrp', ''.$mainCategoryIDdrp.'
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'mainCategoryID');


        $this->datatables->add_column('subcategoryIDdrp', '<select name="subcategoryID[]" class="form-control subcategoryID searchbox"
                                            onchange="load_sub_sub_cat_bulk(this),load_gl_codes(this)">
                                        <option value="">Select Category</option>
                                    </select>
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsSubCat(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'subcategoryID');


        $this->datatables->add_column('subSubCategoryIDdrp', '<select name="subSubCategoryID[]" class="form-control subSubCategoryID searchbox">
                                        <option value="">Select Category</option>
                                    </select>
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsSubSubCat(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'subSubCategoryID');

        $this->datatables->add_column('itemNamedrp', '<input type="text" value="$1" class="form-control itemName" name="itemName[]">', 'itemName');

        $this->datatables->add_column('itemDescriptiondrp', '<input type="text" value="$1" class="form-control itemDescription" name="itemDescription[]">', 'itemDescription');

        $this->datatables->add_column('seconeryItemCodedrp', '<input type="text" value="$1" class="form-control seconeryItemCode" name="seconeryItemCode[]">', 'seconeryItemCode');

        $this->datatables->add_column('defaultUnitOfMeasureIDdrp', ''.$defaultUnitOfMeasureIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsUOM(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'defaultUnitOfMeasureID');

        if($secondaryUOM==1){
            $this->datatables->add_column('secondaryUnitOfMeasureIDdrp', ''.$secondaryUnitOfMeasureIDdrp.'
            <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsUOMSecondary(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'secondaryUnitOfMeasureID');
        }

        $this->datatables->add_column('companyLocalSellingPricedrp', '<input type="text"  step="any" class="form-control companyLocalSellingPrice number"  name="companyLocalSellingPrice[]" value="$1">', 'companyLocalSellingPrice');

        $this->datatables->add_column('barcodedrp', '<input type="text" value="$1" class="form-control barcode" name="barcode[]">', 'barcode');

        $this->datatables->add_column('partNodrp', '<input type="text" value="$1" class="form-control partno" name="partno[]">', 'partNo');

        $this->datatables->add_column('maximunQtydrp', '<input type="text"  value="$1" class="form-control number maximunQty cls_maximunQty" name="maximunQty[]"><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllmaximunQty(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'maximunQty');

        $this->datatables->add_column('minimumQtydrp', '<input type="text" value="$1" class="form-control number minimumQty cls_minimumQty" name="minimumQty[]"><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllminimumQty(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'minimumQty');

        $this->datatables->add_column('reorderPointdrp', '<input type="text" value="$1" class="form-control number reorderPoint cls_reorderPoint" name="reorderPoint[]"><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllreorderPoint(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'reorderPoint');

        $this->datatables->add_column('revanueGLAutoIDdrp', ''.$revanueGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllrevanueGLAutoID(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'revanueGLAutoID');

        $this->datatables->add_column('costGLAutoIDdrp', ''.$costGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllcostGLAutoID(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'costGLAutoID');

        $this->datatables->add_column('assteGLAutoIDdrp', ''.$assteGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllassteGLAutoID(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'assteGLAutoID');

        $this->datatables->add_column('faCostGLAutoIDdrp', ''.$faCostGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllCOSTGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faCostGLAutoID');

        $this->datatables->add_column('faACCDEPGLAutoIDdrp', ''.$faACCDEPGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllACCDEPGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faACCDEPGLAutoID');

        $this->datatables->add_column('faDEPGLAutoIDdrp', ''.$faDEPGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllDEPGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faDEPGLAutoID');

        $this->datatables->add_column('faDISPOGLAutoIDdrp', ''.$faDISPOGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllDISPOGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faDISPOGLAutoID');

        $this->datatables->add_column('stockAdjustmentGLAutoIDdrp', ''.$stockAdjustmentGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllstockadjust(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'stockAdjustmentGLAutoID');

        $this->datatables->add_column('itemAutoIDhn', '<input type="hidden" value="$1" class="form-control number itemAutoIDhn" name="itemAutoIDhn[]">', 'itemAutoID');




        echo $this->datatables->generate();
    }

    function downloadExcel(){


        $csv_data = [
            [
                0 => '#',
                1 => 'Short Description',
                2 => 'Long Description',
                3 => 'Secondary Code',
                4 => 'Selling Price',
                5 => 'Barcode',
                6 => 'Part No',
                7 => 'Maximum Qty',
                8 => 'Minimum Qty',
                9 => 'Reorder Level',
            ]
        ];


        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=Item Master.csv");


        $output = fopen("php://output", "w");
        foreach ($csv_data as $row){
            fputcsv($output, $row);
        }
        fclose($output);
    }

    function clear_temp_table()
    {
        echo json_encode($this->Item_model->clear_temp_table());
    }

    function item_pricing_report(){
        echo json_encode($this->Item_model->item_pricing_report());
    }
    function fetch_item_report()
    {
        $search = $this->input->post('search');
        $showPurchasePrice = getPolicyValues('SPP', 'All');
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];
        $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
        $itemStatus=$this->input->post('activeStatus');

        if($itemSecondaryCodePolicy){
        $arguments_for_description = 'seconeryItemCode,itemDescription,barcode,partNo';
    }else{
        $arguments_for_description = 'itemSystemCode,itemDescription,barcode,partNo';
    }
        $this->datatables->select('itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalPurchasingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist,barcode,partNo', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        if (!empty($itemStatus)) {
            if($itemStatus==1){
                $this->datatables->where('isActive', 1);
            }elseif($itemStatus==2){
                $this->datatables->where('isActive', 0);
            }
        }
        if(!empty($search))
        {
            $where = "itemSystemCode LIKE '" . $search . "' OR itemName LIKE '" . $search . "' OR seconeryItemCode LIKE '" . $search . "' OR itemDescription LIKE '" . $search . "' OR seconeryItemCode LIKE '" . $search . "' OR subcat.description LIKE '" . $search . "' OR subsubcat.description LIKE '" . $search . "' OR CONCAT(itemSystemCode,\" - \",itemDescription) LIKE '" . $search . "' OR itemSystemCode LIKE '" . $search . "' OR itemDescription LIKE '%" . $search . "%' ";
            $this->datatables->where($where);
        }

        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <br><b>Barcode: </b> $3 <br><b>Part No:</b> $4', $arguments_for_description);
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        if($showPurchasePrice == 1){
            $this->datatables->add_column('price', '<b>Prch Price: </b>$3 $1 <br><b>Sales Price: </b>$3 $2', 'format_number(companyLocalPurchasingPrice,'.$defaultdecimal.'),number_format(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }else{
            $this->datatables->add_column('price', '<b>Sales Price: </b>$2 $1', 'format_number(companyLocalSellingPrice,'.$defaultdecimal.'),companyLocalCurrency');
        }
        $this->datatables->add_column('edit', '$1', 'edit_item_master_report(itemAutoID,isActive,isSubitemExist)');
        echo $this->datatables->generate();
        //var_dump($this->db->last_query());exit;
    }

    function item_type_pull(){
        echo json_encode($this->Item_model->item_type_pull());
    }

    function load_sales_details_report_in_sales_and_marketing()
    {
        $this->form_validation->set_rules('wareHouseAutoID[]', 'Ware House', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $filterTo = $this->input->post('filterTo');
            $tmpwarehus = $this->input->post('wareHouseAutoID');
            $tmpitems = $this->input->post('items');
            $customerID = $this->input->post('customerID');
            $column_filter = $this->input->post('columSelectionDrop');

            if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
                $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            } else {
                $filterDate = date('Y-m-d 00:00:00');
            }
            if (!empty($tmpFilterDateTo)) {
                $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
            } else {
                $date2 = date('Y-m-d 23:59:59');
            }
            if (isset($tmpwarehus) && !empty($tmpwarehus)) {
                $tmpWarehouse = join(",", $tmpwarehus);
                $warehouse = $tmpWarehouse;
            } else {
                $warehouse = null;
            }
            if (isset($tmpitems) && !empty($tmpitems)) {
                $tmpItems = join(",", $tmpitems);
                $item = $tmpItems;
            } else {
                $item = null;
            }
            if (isset($customerID) && !empty($customerID)) {
                $customerID = join(",", $customerID);
                $customer = $customerID;
            } else {
                $customer = null;
            }

            $itemizedSalesReport = $this->Item_model->load_sales_details_report_in_sales_and_marketing($filterDate, $date2, $warehouse,$item, $customer);
            $data['reportData'] = $itemizedSalesReport;
            $data['warehouse'] = $tmpwarehus;
            $data['currency'] = $this->input->post('currency');
            $data["columnSelectionDrop"] = $column_filter;
            $this->load->view('system/sales/sales_detail_report_body', $data);
        }
    }

    function load_item_wise_prfitability_report()
    {
        $this->form_validation->set_rules('wareHouseAutoID[]', 'Ware House', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $filterTo = $this->input->post('filterTo');
            $tmpwarehus = $this->input->post('wareHouseAutoID');
            $tmpitems = $this->input->post('items');
            $column_filter = $this->input->post('columSelectionDrop');

            if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
                $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            } else {
                $filterDate = date('Y-m-d 00:00:00');
            }


            if (!empty($tmpFilterDateTo)) {
                $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
            } else {
                $date2 = date('Y-m-d 23:59:59');
            }

            if (isset($tmpwarehus) && !empty($tmpwarehus)) {
                $tmpWarehouse = join(",", $tmpwarehus);
                $warehouse = $tmpWarehouse;
            } else {
                $warehouse = null;
            }

            if (isset($tmpitems) && !empty($tmpitems)) {
                $tmpItems = join(",", $tmpitems);
                $item = $tmpItems;
            } else {
                $item = null;
            }


            $itemizedSalesReport = $this->Item_model->get_item_wise_prfitability_report($filterDate, $date2, $warehouse,$item);



            $data['reportData'] = $itemizedSalesReport;
            $data['warehouse'] = $tmpwarehus;
            $data['currency'] = $this->input->post('currency');
            $data["columnSelectionDrop"] = $column_filter;

            $this->load->view('system/inventory/report/item-wise-profitability-report-body', $data);
        }
    }

    function load_item_wise_prfitability_report_DD(){
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $filterTo = $this->input->post('filterTo');
        $tmpwarehus = $this->input->post('wareHouseAutoID');

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpwarehus) && !empty($tmpwarehus)) {
            $tmpWarehouse = join(",", $tmpwarehus);
            $warehouse = $tmpWarehouse;
        } else {
            $warehouse = null;
        }

        $result = $this->Item_model->get_item_wise_prfitability_report_DD($filterDate, $date2, $warehouse);

        echo json_encode($result);
    }

    function item_barcode_validate()
    {
        echo json_encode($this->Item_model->item_barcode_validate());
    }


    function export_excel_sales_detail_report()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Sales Detail Report');
        $this->load->database();
        $fieldName = $this->input->post('columSelectionDrop');
        //$fieldName = explode(',', $column_filter);

        $header = ['#','Customer Code', 'Customer Name', 'Document System Code', 'Document Date', 'Item Code', 'Secondary Code'];
        $currency = $this->input->post('currency');
        $isBarcode = false;
        $isPartNo = false;

        if(isset($fieldName)){
            if (in_array("barcode", $fieldName)) {
                $header[] = 'Barcode';
                $isBarcode = true;
            }

            if (in_array("partNo", $fieldName)) {
                $header[] = 'Part No';
                $isPartNo = true;
            }
        }
        $header[] = 'Item Description';
        $header[] = 'UOM';
        $header[] = 'Qty';
        
        if($currency == 'Local'){
            $header[] = 'Total Sales Value ' . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
            $header[] = 'Total Cost ' . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
            $header[] = 'Profit ' . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
        } else {
            $header[] = 'Total Sales Value ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
            $header[] = 'Total Cost ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
            $header[] = 'Profit ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
        }
        $header[] = 'Profit Margin';
        if($currency == 'Local'){
            $header[] = 'Average Cost ' . "(" . $this->common_data['company_data']['company_default_currency'] . ") up to To Date";
        } else {
            $header[] = 'Average Cost ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ") up to To Date";
        }
        $header[] = 'Sub Category';
        $data = $this->Item_model->get_sales_detail_report_excel($isBarcode,$isPartNo);
        $details = $data['details'];
        $rowCount = $data['rowCount'];
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells('A1:P1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Sales Detail Report'], null, 'A2');
        $this->excel->getActiveSheet()->mergeCells('A2:D2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:R4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:R4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');
        $this->excel->getActiveSheet()->getStyle('F6:M'.$rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $filename = 'Item Wise Profitability Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function export_excel_item_report()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Item Wise Profitability Report');
        $this->load->database();
        $fieldName = $this->input->post('columSelectionDrop');
        //$fieldName = explode(',', $column_filter);

        $header = ['#', 'Item Code', 'Secondary Code'];
        $currency = $this->input->get('currency');
        $isBarcode = false;
        $isPartNo = false;

        if(isset($fieldName)){
            if (in_array("barcode", $fieldName)) {
                $header[] = 'Barcode';
                $isBarcode = true;
            }

            if (in_array("partNo", $fieldName)) {
                $header[] = 'Part No';
                $isPartNo = true;
            }
        }
        $header[] = 'Item Description';
        $header[] = 'UOM';
        $header[] = 'Qty';

        if($currency == 'Local'){
            $header[] = 'Total Sales Value ' . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
            $header[] = 'Total Cost ' . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
            $header[] = 'Profit ' . "(" . $this->common_data['company_data']['company_default_currency'] . ")";
        } else {
            $header[] = 'Total Sales Value ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
            $header[] = 'Total Cost ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
            $header[] = 'Profit ' . "(" . $this->common_data['company_data']['company_reporting_currency'] . ")";
        }
        $header[] = 'Profit Margin';
        $data = $this->Item_model->get_item_wise_prfitability_report_excel($isBarcode,$isPartNo);
        $details = $data['details'];
        $rowCount = $data['rowCount'];
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells('A1:C1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Item Wise Profitability Report'], null, 'A2');
        $this->excel->getActiveSheet()->mergeCells('A2:D2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:l4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:l4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');
        $this->excel->getActiveSheet()->getStyle('F6:M'.$rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $filename = 'Item Wise Profitability Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_item_codification()
    {
        $deletedYN = $this->input->post('deletedYN');
        $defaultdecimal =  $this->common_data['company_data']['company_default_decimal'];

        $this->datatables->select('itemAutoID,srp_erp_itemmaster.deletedYN as deletedYN, itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemDescription," | ",barcode) as description, isSubitemExist', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->where('deletedYN', $deletedYN);
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
//        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,'.$defaultdecimal.'),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        /*$this->datatables->add_column('img', "<a onclick='change_img(\"$2\",\"$3/$1\")'><img class='img-thumbnail' src='$3/$1' style='width:120px;height: 80px;' ></a>", 'itemImage,itemAutoID,base_url("images/item/")');*/
        $this->datatables->add_column('edit', '$1', 'edit_item_codification(itemAutoID,isActive,isSubitemExist, deletedYN)');


        // $this->datatables->add_column('edit', '<spsn class="pull-right"><input type="checkbox" id="itemchkbox" name="itemchkbox" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br><a onclick="fetchPage(\'system/item/erp_item_new\',$1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_master($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'itemAutoID');


        echo $this->datatables->generate();
    }

    function export_excel_item_master(){
        $hideWacAmount = getPolicyValues('HWC','All');
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Item Master List');
        $this->load->database();
        $data = $this->Item_model->export_excel_item_master();
        if($hideWacAmount==1){
            $header = ['#', 'Main Category', 'Sub Category', 'Sub Sub Category', 'Item System Code', 'Item Secondary Code', 'Item Description', 'Barcode', 'Part No', 'UOM', 'Current Stock', 'Maximum Qty', 'Minimum QTY', 'Reorder Level' , 'Local Currency', 'Selling Price'];
        }else{
            $header = ['#', 'Main Category', 'Sub Category', 'Sub Sub Category', 'Item System Code', 'Item Secondary Code', 'Item Description', 'Barcode', 'Part No', 'UOM', 'Current Stock', 'Maximum Qty', 'Minimum QTY', 'Reorder Level' , 'Local Currency', 'Wac Cost', 'Selling Price'];
        }

        $body = $data['items'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Item List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($body, null, 'A6');

        $filename = 'Item Master.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function export_excel_item_master_report(){
        // $hideWacAmount = getPolicyValues('HWC','All');
        $hideWacAmount = 0;
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Item Master List');
        $this->load->database();
        $data = $this->Item_model->export_excel_item_master_report();
        $showPurchasePrice = getPolicyValues('SPP', 'All');
        if($hideWacAmount==1){
            $header = ['#', 'Main Category', 'Sub Category', 'Sub Sub Category', 'Item System Code', 'Item Secondary Code', 'Item Description', 'Barcode', 'Part No', 'UOM', 'Current Stock', 'Maximum Qty', 'Minimum QTY', 'Reorder Level' , 'Local Currency', 'Selling Price'];
        }else{
            $header = ['#', 'Main Category', 'Sub Category', 'Sub Sub Category', 'Item System Code', 'Item Secondary Code', 'Item Description', 'Barcode', 'Part No', 'UOM', 'Current Stock', 'Maximum Qty', 'Minimum QTY', 'Reorder Level' , 'Local Currency', 'Wac Cost', 'Selling Price'];
        }
        if($showPurchasePrice){
            $header[] = 'Purchasing Price';
        }

        $body = $data['items'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Item List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($body, null, 'A6');
//        ob_clean();
//        ob_start(); # added
        $filename = 'Item Master.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    /* Function added */
    function im_confirmation()
    {
        echo json_encode($this->Item_model->im_confirmation());
    }

    function check_confirmation()
    {
        echo json_encode($this->Item_model->check_confirmation());
    }

   
    function  reject_itemmaster()
    {
        
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $comment = trim($this->input->post('comment') ?? '');
        $this->form_validation->set_rules('itemAutoID', 'Master ID', 'trim|required');
     
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $result = $this->db->query("SELECT
                `approvalUserID`,
                `masterCurrentLevelNo` AS levelNo,
                srp_erp_approvalusers.companyCode AS `companyCode`,
                `srp_erp_approvalusers`.`documentID` AS `documentID`,
                `srp_erp_approvalusers`.`document` AS `document`,
                `employeeID`,
                `employeeName`,
                `srp_erp_itemmaster`.`itemAutoID` AS `itemAutoID`, 
                srp_erp_itemmaster.`itemSystemCode` AS `itemSystemCode`
            FROM
                `srp_erp_approvalusers`
                JOIN `srp_erp_documentcodes` ON `srp_erp_approvalusers`.`documentID` = `srp_erp_documentcodes`.`documentID` 
                LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_itemmaster`.`masterCurrentLevelNo` 
                WHERE
                -- isApprovalDocument = 1 AND
                srp_erp_approvalusers.`companyID` =  $companyID 
                AND `srp_erp_approvalusers`.`documentID` = 'INV' 
                AND `employeeID` =  $currentuser
                AND srp_erp_itemmaster.itemAutoID = $itemAutoID " )->row_array();

        $level_id = $result['levelNo'];

        if(empty($result)){
            die( json_encode(['e', 'You are not authorized to perform this approval',2]) );
           //return array('e', 'You are not authorized to perform this approval');
       }else{
            $approvedYN = checkApproved($itemAutoID, 'INV', $level_id);
           if ($approvedYN) {
               die( json_encode(['w', 'Document already approved',2] ) );
           }

           $document_status = $this->db->get_where('srp_erp_itemmaster', ['itemAutoID'=>$itemAutoID])->row('masterConfirmedYN');
           if ($document_status == 2) {
               die( json_encode(['w', 'Document already rejected']) );
           }
           else{
            $data1 = array(
                'masterApprovedYN' => 2,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date'],
            );
            $data2 = array(
                'approvedYN' => 2,
            );
            $data3 = array(
                
                    'documentID' => "INV",
                    'documentCode' => $result['itemSystemCode'],
                    'systemID' => $itemAutoID,
                    'comment' => $comment,
                    'table_name' => "srp_erp_itemmaster",
                    'table_unique_field' => "itemAutoID",
                    'rejectedLevel' => $level_id,
                    'rejectByEmpID' => $this->common_data['current_userID'],
 
           
                    'companyID' => $companyID,
                    'companyCode' => $this->common_data['company_data']['company_code'],
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdDateTime' => $this->common_data['current_date'],
                    'createdUserName' => $this->common_data['current_user'],
                    'timestamp' => current_date(true)
            );
            $this->db->where('itemAutoID', $itemAutoID)->update('srp_erp_itemmaster', $data1);
            $this->db->where('documentSystemCode', $supplierAutoID)->update('srp_erp_documentapproved', $data2);
            // $this->db->where('itemAutoID', $itemAutoID)->update('srp_erp_documentapproved', $data2);
            // $this->db->where('documentSystemCode', $itemAutoID)->update('srp_erp_documentapproved', $data2);
            $this->db->where('systemID', $itemAutoID)->insert('srp_erp_approvalreject', $data3);
            $this->db->trans_complete();
                if ($this->db->trans_status() == true) {
                    echo json_encode(['s', 'Successfully Rejected', 2]);
                } else {
                    echo json_encode(['e', 'Failed to Reject the Item']);
                }
            }
 
        }
    }
   
    function approve_itemmaster()
    {
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        //$level_id = trim($this->input->post('level') ?? '');
        //$status = trim($this->input->post('status') ?? '');

        $this->form_validation->set_rules('itemAutoID', 'Master ID', 'trim|required');
        //$this->form_validation->set_rules('status', 'Status', 'trim|required');
        // $this->form_validation->set_rules('level', 'Level', 'trim|required');
        /*if ($status == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }*/

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $result = $this->db->query("SELECT
                `approvalUserID`,
                `masterCurrentLevelNo` AS levelNo,
                srp_erp_approvalusers.companyCode AS `companyCode`,
                `srp_erp_approvalusers`.`documentID` AS `documentID`,
                `srp_erp_approvalusers`.`document` AS `document`,
                `employeeID`,
                `employeeName`, 
                `srp_erp_itemmaster`.`itemAutoID` AS `itemAutoID`
            FROM
                `srp_erp_approvalusers`
                JOIN `srp_erp_documentcodes` ON `srp_erp_approvalusers`.`documentID` = `srp_erp_documentcodes`.`documentID` 
                LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_itemmaster`.`masterCurrentLevelNo` 
            WHERE
                -- isApprovalDocument = 1 AND
                srp_erp_approvalusers.`companyID` =  $companyID 
                AND `srp_erp_approvalusers`.`documentID` = 'INV' 
                AND `employeeID` =  $currentuser
                AND srp_erp_itemmaster.itemAutoID = $itemAutoID " )->row_array();

        $level_id = $result['levelNo'];

        if(empty($result)){
             die( json_encode(['e', 'You are not authorized to perform this approval',2]) );
            //return array('e', 'You are not authorized to perform this approval');
        }else{
             $approvedYN = checkApproved($itemAutoID, 'INV', $level_id);
            if ($approvedYN) {
                die( json_encode(['w', 'Document already approved',2] ) );
            }

            $document_status = $this->db->get_where('srp_erp_itemmaster', ['itemAutoID'=>$itemAutoID])->row('masterConfirmedYN');
            if ($document_status == 2) {
                die( json_encode(['w', 'Document already rejected']) );
            }

            echo json_encode($this->Item_model->approve_itemmaster($level_id));
        }
    }

    function referback_item()
    {
        $masterID = $this->input->post('itemAutoID');

        $document_status = document_status('INV', $masterID, 1);
         //var_dump($document_status);
        if($document_status['error'] == 1){
            die( json_encode(['e', $document_status['message']]) );
        }

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($masterID, 'INV',false);
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }

       // echo json_encode($this->Item_model->referback_item());

    }

  /*  function reverse_approved_item()
    {
        $masterID = $this->input->post('itemAutoID');
        $companyID = current_companyID();
        $document_output = $this->db->query("SELECT
	`masterConfirmedYN` AS `confirmVal`,
	`masterApprovedYN` AS `approvalVal`,
	`masterCurrentLevelNo` AS `appLevel`,
	itemSystemCode AS `docCode`,
	`createdDateTime` AS `createdDate` 
FROM
	`srp_erp_itemmaster` 
WHERE
	`itemAutoID` = $masterID 
	AND `companyID` = $companyID ")->row_array();

        if(empty($document_output)){
            die( json_encode(['e', 'Document master record not found']) );
            //return ['error' => 1, 'message' => 'Document master record not found'];
        }else{
            echo json_encode(array('s', ' Document master record  found.'));
        }
    }*/

    function fetch_purchase_price()
    {
        echo json_encode($this->Item_model->fetch_purchase_price());
    }

    function load_item_master_view(){
        //$supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        //$data['extra'] = $this->Suppliermaster_model->load_supplier_header();
        $data['signature'] = '';
        $data['logo'] = mPDFImage;
        //echo '<pre>'; print_r($data); echo '</pre>';  exit;
        echo $this->load->view('system/item/erp_item_master_view.php', $data, true);

    }

    function load_service_details_report_in_sales_and_marketing()
    {
        //$this->form_validation->set_rules('s_wareHouseAutoID[]', 'Ware House', 'trim|required');
        $this->form_validation->set_rules('s_filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('s_filterTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('s_filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('s_filterTo')));
            $filterTo = $this->input->post('s_filterTo');
            //$tmpwarehus = $this->input->post('s_wareHouseAutoID');
            $tmpitems = $this->input->post('s_items');
            $customerID = $this->input->post('s_customerID');
            $column_filter = $this->input->post('s_columSelectionDrop');

            if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
                $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            } else {
                $filterDate = date('Y-m-d 00:00:00');
            }
            if (!empty($tmpFilterDateTo)) {
                $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
            } else {
                $date2 = date('Y-m-d 23:59:59');
            }
            /*if (isset($tmpwarehus) && !empty($tmpwarehus)) {
                $tmpWarehouse = join(",", $tmpwarehus);
                $warehouse = $tmpWarehouse;
            } else {
                $warehouse = null;
            }*/
            if (isset($tmpitems) && !empty($tmpitems)) {
                $tmpItems = join(",", $tmpitems);
                $item = $tmpItems;
            } else {
                $item = null;
            }
            if (isset($customerID) && !empty($customerID)) {
                $customerID = join(",", $customerID);
                $customer = $customerID;
            } else {
                $customer = null;
            }

            $itemizedSalesReport = $this->Item_model->load_service_details_report_in_sales_and_marketing($filterDate, $date2, $item, $customer);
            $data['reportData'] = $itemizedSalesReport;
            //$data['warehouse'] = $tmpwarehus;
            $data['currency'] = $this->input->post('currency');
            $data["columnSelectionDrop"] = $column_filter;
            $this->load->view('system/sales/sales_service_detail_report_body', $data);
        }
    }
    /* End  Function */
}
    