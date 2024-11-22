<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('srm_helper');
echo head_page($_POST['page_name'], false);


$country = load_country_drop();
$location_arr = fetch_all_location();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$customer_arr=all_srm_supplier_drop_for_company_request();
?>
<style>
    @font-face {
        font-family: barCodeFont;
        src: url(<?php echo base_url('font/fre3of9x.ttf') ?>);
    }

    .barcodeDiv {
        width: 200px;
        height: 42px;
        margin-top: 10px;

    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps">
    <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
        <span class="step__icon"></span>
        <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
            - Basic Details</span><!--Step 1--><!--Item Header-->
    </a>
  
    <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_submit_attachment()" onclick="" data-toggle="tab">
        <span class="step__icon"></span>
        <span class="step__label">Attachments</span>
    </a>
    <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step4" onclick="company_request_submit_other_info()" data-toggle="tab">
        <span class="step__icon"></span>
        <span class="step__label">Confirmation</span>
    </a>
    </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="company_request_basic_form"'); ?>
        <div class="row modal-body" style="padding-bottom: 0px;">
            
            <div class="col-md-12" style="padding-left: 0px;">
                <div class="row">
                   <input type="hidden" name="btn_status" id="btn_status">
                    <div class="form-group col-sm-3">
                        <label for="">
                        Provider Name  </label>
                        <input type="text" class="form-control" id="companyname" name="companyname" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="supplierUrl">Company URL</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierUrl" name="supplierUrl" readonly>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                        Comany Registration No</label>
                        <input type="text" class="form-control" id="companyRegNo" name="companyRegNo" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                        License Expiry Date </label>
                        <input type="text" class="form-control" id="licenseExpireDate" name="licenseExpireDate" readonly>
                    </div>
                    

                </div>

                <div class="row">
                    
                    <div class="form-group col-sm-3">
                        <label for="supplierUrl">Year of Establishment</label>
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="yearofEstablishment" name="yearofEstablishment" readonly>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                        Business Category</label>
                        <input type="text" class="form-control" id="natureofBusiness" name="natureofBusiness" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                        Group Company  </label>
                        <input type="text" class="form-control" id="groupCompany" name="groupCompany" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="supplierUrl">Sponsor Name</label>
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="sponsorName" name="sponsorName" readonly>
                        </div>
                    </div>

                    

                </div>

                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="">Sponsor Country<!--Sponsor Country--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('sponsercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="sponsercountry"  required'); ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">HOLocation<!--HOLocation--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('holocation', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="holocation"  required'); ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                        Number of years in Business</label>
                        <input type="text" class="form-control" id="numberofYearBusiness" name="numberofYearBusiness" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                        Number of Branches </label>
                        <input type="text" class="form-control" id="numberofBranch" name="numberofBranch" readonly>
                    </div>

                </div>

                <div class="row">
                <div class="form-group col-sm-3">
                        <label for="vatPercentage">VAT Number </label>
                        <input type="text" class="form-control" id="vatNumber" name="vatNumber" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="vatPercentage">VAT Expire Date </label>
                        <input type="text" class="form-control" id="vatExpire" name="vatExpire" readonly>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="supplierUrl">Brands</label>
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="brands" name="brands" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-sm-3">
                        <label for="vatNo">Is PDO Approved</label>
                        <div class="skin skin-square">
                        <div class="skin-section extraColumns"><input id="ispdo" type="checkbox"
                                                                      data-caption="" class="columnSelected"
                                                                      name="ispdo" readonly><label
                                for="checkbox">&nbsp;</label></div>
                    </div>
                        
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="vatNo">Is DCRP Approved</label>
                        <div class="skin skin-square">
                        <div class="skin-section extraColumns"><input id="isdcrp" type="checkbox"
                                                                      data-caption="" class="columnSelected"
                                                                      name="isdcrp" readonly><label
                                for="checkbox">&nbsp;</label></div>
                    </div>
                        
                    </div>
                   
                </div>

                <div class="header-title-bg">
                    <div class="row pb-10">
                        <div class="col-sm-6">
                            <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;Vendor SubCategories</h4>
                        </div>
                    </div>
                </div>
                <div class="row pb-20 pt-20">
                    <div class="col-sm-12">
                        <table class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nature of Business</th>
                            
                                <th>Brief Description of Products</th>
                            </tr>
                            </thead>
                            <tbody id="request_Category">
                            
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">

                    <div class="form-group col-sm-3">
                        <label for="vatNo">Have Certification</label>
                        <div class="skin skin-square">
                        <div class="skin-section extraColumns"><input id="havecertifi" type="checkbox"
                                                                      data-caption="" class="columnSelected"
                                                                      name="havecertifi" ><label
                                for="checkbox">&nbsp;</label></div>
                    </div>
                        
                    </div>
                    <div class="form-group col-sm-3" id ="dev_ce">
                        <label for="vatNo">Certification </label>

                        <p id="cer"></p>
                        
                    </div>
                   
                </div>
                <div class="header-title-bg">
                    <div class="row pb-10">
                        <div class="col-sm-6">
                            <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;Contact Information</h4>
                        </div>
                    </div>
                </div>

                <div class="row pt-20">
                    <div class="form-group col-sm-4">
                        <label for="supplierTelephone"><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierEmail" name="supplierEmail" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierFax">Fax</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierFax" name="supplierFax" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-sm-4">
                        <label for="supplierAddress1">Address 1</label>
                        <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1"></textarea>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierAddress2">Address 2</label>
                        <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2"></textarea>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="supplierUrl">State</label>
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="state" name="state" readonly>
                        </div>
                    </div>
                    
                </div>

                <div class="row">

                    <div class="form-group col-sm-4">
                        <label for="supplierAddress1">City</label>
                       
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="city" name="city" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierAddress2">Pincode</label>
                        
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="pincode" name="pincode" readonly>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="supplierUrl">Contact Name</label>
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="contactName" name="contactName" readonly>
                        </div>
                    </div>
                    
                </div>

                <div class="row">

                    <div class="form-group col-sm-4">
                        <label for="supplierAddress1">Contact Phone</label>
                      
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="pointContactphone" name="pointContactphone" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierAddress2">Contact Role</label>
                       
                        <div class="input-group w-100">
                           
                            <input type="text" class="form-control" id="pointofContactRole" name="pointofContactRole" readonly>
                        </div>
                    </div>

                    
                    
                </div>

                <div class="row">

                   
                    
                </div>
            </div>
        </div>

        <div class="header-title-bg">
                    <div class="row pb-10">
                        <div class="col-sm-6">
                            <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;Family Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row pb-20 pt-20">
                    <div class="col-sm-12">
                        <table class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Relationship</th>
                            
                                <th>Employee Name</th>

                                <th>Designation</th>
                            </tr>
                            </thead>
                            <tbody id="family_des">
                            
                            </tbody>
                        </table>
                    </div>
                </div>
        
        <hr>
        <div id="">
            <a class="btn btn-primary-new size-lg pull-right" onclick="company_request_submit_basic_info()">
                Next</a>
        </div>
        </form>
    </div>
    <!-- <div id="step2" class="tab-pane">
        <?php echo form_open('', 'role="form" id="company_request_financial_form"'); ?>
        <div class="row">
            
            <div class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_name_on_cheque');?> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="nameOnCheque1" name="nameOnCheque">
            </div>

            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_category');?></label>
                <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="liabilityAccount"><?php echo $this->lang->line('accounts_payable_sm_liability_account');?> <?php required_mark(); ?></label>
                <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required'); ?>
            </div>
        </div>
        <div class="row">
            
            <div class="form-group col-sm-4">
                <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?> <?php required_mark(); ?></label>
                <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_Country');?> <?php required_mark(); ?></label>
                <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('accounts_payable_sm_tax_group');?></label>
                <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
            </div>
        </div>
        <div class="row">
            
       
            <div class="form-group col-sm-4">
                <label for="vatPercentage">VAT Number </label>
                <input type="text" class="form-control" id="vatNumber" name="vatNumber">
            </div>

            <div class="form-group col-sm-4">
                <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('accounts_payable_sm_credit_period');?></label>
                <div class="input-group">
                    <div class="input-group-addon"><?php echo $this->lang->line('common_month');?></div>
                    <input type="text" class="form-control number" id="supplierCreditPeriod"
                           name="supplierCreditPeriod">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('accounts_payable_sm_credit_limit');?></label>
                <div class="input-group">
                    <div class="input-group-addon"><span class="currency">LKR</span></div>
                    <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit">
                </div>
            </div>
        </div>
        
        <div class="row">
            
            

            <div class="form-group col-sm-4">
                <label for="vatEligible"><?php echo $this->lang->line('accounts_payable_vat_eligible');?></label>
                <?php echo form_dropdown('vatEligible', array('1'=> $this->lang->line('common_no'), '2'=> $this->lang->line('common_yes')), 1, 'class="form-control select2" id="vatEligible" required'); ?>
            </div>

        
            <div class="form-group col-sm-4">
                <label for="vatPercentage">VAT Expire Date </label>
                <input type="text" class="form-control" id="vatExpire" name="vatExpire">
            </div>

            <div class="form-group col-sm-4">
                <label for="vatPercentage">VAT <?php echo $this->lang->line('common_percentage');?></label>
                <input type="text" class="form-control" id="vatPercentage" name="vatPercentage">
            </div>
        </div>
        <div class="row">

        </div>
        <hr>
        <div class="" >
            <a class="btn btn-primary-new size-lg pull-right" onclick="company_request_submit_financial_info()" id="supplier_btn" type="submit">Save and Next</a>
        </div>
        </form>
    </div> -->
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-sm-12">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document Name</th>
                     
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="request_document">
                    
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 mb-3 mt-5 pt-20">
                    <div class="row pb-10">
                        <div class="col-sm-6">
                            <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;Other documents</h4>
                        </div>
                    </div>
            </div>
            <div class="col-sm-12">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document Name</th>
                     
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="request_document_other">
                    
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="" >
            <a class="btn btn-primary-new size-lg pull-right" onclick="company_request_submit_other_info()" id="supplier_btn" type="submit">Next</a>
        </div>
    </div>
    <div id="step4" class="tab-pane">
    <?php echo form_open('', 'role="form" id="company_request_confirm_form"'); ?>
        <div class="col-md-12" style="padding-left: 0px;">
                <!-- <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="">
                        Secondary Code</label>
                        
                        <h5 id="seconeryItemCodeV"></h5>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="">
                        Company Name  </label>
                        <h5 id="companynamev"></h5>
                        
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="supplierUrl">URL</label>
                        <h5 id="supplierUrlv"></h5>
                        
                    </div>

                </div> -->

        </div>

        <div class="row">
            <div class="col-sm-12">

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Select Supplier</label>
                            <div class="col-sm-6" style="top: 5px;">
                                <input type="checkbox" value="" id="existSupplier" name="existSupplier">
                                <input type="hidden" value="" id="existSupplierVal" name="existSupplierVal">
                            </div>
                        </div>
                    </div>
                    <div class="row hide" id="existSupplierView">

                        <!-- <div class="form-group">
                            <label class="col-sm-4 control-label">Supplier</label>
                            <div class="col-sm-4" id="sup_data_show_old">
                            <?php echo form_dropdown('sup', $customer_arr, '', 'class="form-control" id="sup" '); ?> 
                            </div>

                            <div class="col-sm-4">
                            <h4 id="sup_data_show"></h4>
                            </div>

                        </div> -->

                        <div class="row">

                            <div class="form-group col-sm-4">
                                <label for="">
                                Secondary Code <?php required_mark(); ?></label>
                                <input type="text" class="form-control" id="seconeryItemCode2" name="seconeryItemCode2" readonly>
                            </div>
                            
                            

                            <div class="form-group col-sm-4">
                                <label for=""><?php echo $this->lang->line('common_category');?></label>
                                <?php echo form_dropdown('partyCategoryID2', $customerCategory, '', 'class="form-control select2" disabled  id="partyCategoryID2"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="liabilityAccount"><?php echo $this->lang->line('accounts_payable_sm_liability_account');?> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('liabilityAccount2', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" disabled id="liabilityAccount2" required'); ?>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="form-group col-sm-4">
                                <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('supplierCurrency2', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" disabled id="supplierCurrency2" required'); ?>
                            </div>

                            
                            <div class="form-group col-sm-4">
                                <label for=""><?php echo $this->lang->line('accounts_payable_sm_tax_group');?></label>
                                <?php echo form_dropdown('suppliertaxgroup2', $taxGroup_arr, '', 'class="form-control select2" disabled  id="suppliertaxgroup2"'); ?>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('accounts_payable_sm_credit_period');?></label>
                                <div class="input-group">
                                    <div class="input-group-addon"><?php echo $this->lang->line('common_month');?></div>
                                    <input type="text" class="form-control number" id="supplierCreditPeriod2"
                                        name="supplierCreditPeriod2" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                    
                            <div class="form-group col-sm-4">
                                <label for="vatEligible"><?php echo $this->lang->line('accounts_payable_vat_eligible');?></label>
                                <?php echo form_dropdown('vatEligible2', array('1'=> $this->lang->line('common_no'), '2'=> $this->lang->line('common_yes')), 1, 'class="form-control select2" id="vatEligible2" disabled required'); ?>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="vatPercentage">VAT <?php echo $this->lang->line('common_percentage');?></label>
                                <input type="text" class="form-control" id="vatPercentage2" name="vatPercentage2" readonly>
                            </div>

                            
                            <div class="form-group col-sm-4">
                                <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('accounts_payable_sm_credit_limit');?></label>
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="currency"></span></div>
                                    <input type="text" class="form-control number" id="supplierCreditLimit2" name="supplierCreditLimit2" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            
                    
                            <div class="form-group col-sm-4">
                                <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_name_on_cheque');?> <?php required_mark(); ?></label>
                                <input type="text" class="form-control" id="nameOnCheque2" name="nameOnCheque2" readonly>
                            </div>

                            <div class="form-group col-sm-4">
                                    <label for="supplierName">Comment <?php required_mark(); ?></label>
                                    <textarea class="form-control" id="system_comment" name="system_comment" rows="2" readonly></textarea>
                                </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Create Supplier</label>
                            <div class="col-sm-6" style="top: 5px;">
                                <input type="checkbox" value="" id="addSupplier" name="addSupplier">
                                <input type="hidden" value="" id="addSupplierVal" name="addSupplierVal">
                            </div>
                        </div>
                    </div>
                    <div class="row hide" id="newSupplierDetailAdd">

                      
                            <div class="row">

                                 <div class="form-group col-sm-4">
                                    <label for="">
                                    Secondary Code <?php required_mark(); ?></label>
                                    <input type="text" class="form-control" id="seconeryItemCode" name="seconeryItemCode" readonly>
                                </div>
                                
                               

                                <div class="form-group col-sm-4">
                                    <label for=""><?php echo $this->lang->line('common_category');?></label>
                                    <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2" disabled  id="partyCategoryID"'); ?>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="liabilityAccount"><?php echo $this->lang->line('accounts_payable_sm_liability_account');?> <?php required_mark(); ?></label>
                                    <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" disabled id="liabilityAccount" required'); ?>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="form-group col-sm-4">
                                    <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?> <?php required_mark(); ?></label>
                                    <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" disabled onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
                                </div>

                               
                                <div class="form-group col-sm-4">
                                    <label for=""><?php echo $this->lang->line('accounts_payable_sm_tax_group');?></label>
                                    <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2" disabled  id="suppliertaxgroup"'); ?>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('accounts_payable_sm_credit_period');?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><?php echo $this->lang->line('common_month');?></div>
                                        <input type="text" class="form-control number" id="supplierCreditPeriod"
                                            name="supplierCreditPeriod" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                        
                                <div class="form-group col-sm-4">
                                    <label for="vatEligible"><?php echo $this->lang->line('accounts_payable_vat_eligible');?></label>
                                    <?php echo form_dropdown('vatEligible', array('1'=> $this->lang->line('common_no'), '2'=> $this->lang->line('common_yes')), 1, 'class="form-control select2" disabled id="vatEligible" required'); ?>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label for="vatPercentage">VAT <?php echo $this->lang->line('common_percentage');?></label>
                                    <input type="text" class="form-control" id="vatPercentage" name="vatPercentage" readonly>
                                </div>

                                
                                <div class="form-group col-sm-4">
                                    <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('accounts_payable_sm_credit_limit');?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="currency"></span></div>
                                        <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                
                        
                                <div class="form-group col-sm-4">
                                    <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_name_on_cheque');?> <?php required_mark(); ?></label>
                                    <input type="text" class="form-control" id="nameOnCheque1" name="nameOnCheque" readonly>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label for="supplierName">Comment <?php required_mark(); ?></label>
                                    <textarea class="form-control" id="add_comment" name="add_comment" rows="2" readonly></textarea>
                                </div>
                            </div>
                            
                       
                    </div>
               
            </div>
        </div>
        <hr>
        <div  class=" text-right m-t-xs" >
            <a class="btn btn-primary-new size-lg headerclose pull-right" id="supplier_btn" >Close</a>
        <!-- <div id="btn_dev" class="hide">
            <a class="btn btn-default-new size-lg" onclick="company_request_reject_info(2)" id="supplier_btn" type="submit">Refer Back</a>
            <a class="btn btn-danger-new size-lg" onclick="company_request_reject_info(1)" id="supplier_btn" type="submit">Reject</a>            
            <a class="btn btn-success-new size-lg mr-1" onclick="company_request_confirm_info()" id="supplier_btn" type="submit">confirm</a>
        </div> -->
       
        </div>
        
        </form>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="company_request_doc_approve_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Comment</h4>
            </div>
            <form class="form-horizontal" id="company_doc_reject_form">
                <div class="modal-body">
                    <div id="conform_body"></div>
               
                    <input type="hidden" name="requestDocID" id="requestDocID">
                    <input type="hidden" name="typeReq" id="typeReq">
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                    <a onclick="saveCompanyDocumentRejectRequest()" class="btn btn-primary">Submit</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="company_request_reject_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Comment</h4>
            </div>
            <form class="form-horizontal" id="company_req_reject_form">
                <div class="modal-body">
                    <div id="conform_body"></div>
               
                    <input type="hidden" name="requestDocID_rej" id="requestDocID_rej">
                    <input type="hidden" name="typeReq_rej" id="typeReq_rej">
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments_rej" id="comments_rej" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                    <a onclick="saveCompanyRequestRejectRequest()" class="btn btn-primary">Submit</a>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var itemAutoID;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_vendor_company_request', '', 'Company Request')
        });
        $('.select2').select2();
        itemAutoID = null;

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('.isSubItemExist input').on('ifChecked', function (event) {
            $('.subitemapplicableon').removeClass('hide');
        });

        $('.isSubItemExist input').on('ifUnchecked', function (event) {
            $('.subitemapplicableon').addClass('hide');
        });
        load_supplier_company_request_header();
       // $('#approvebtn').empty();
       

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });
    });

    $('#existSupplier').on('change', function(){
        if($('#existSupplier').is(":checked")){
            $('#existSupplierView').removeClass('hide');
            $('#existSupplierVal').val(1);
            $('#addSupplier').addClass('hide');
        }else {

            $('#existSupplierView').addClass('hide');
            $('#existSupplierVal').val(2);

            $("#sup").val(null).trigger("change");
            $('#addSupplier').removeClass('hide');
        }

    });

    $('#addSupplier').on('change', function(){
        if($('#addSupplier').is(":checked")){
          //  $('#addSupplierView').removeClass('hide');
            $('#addSupplierVal').val(1);
            $("#sup").val(null).trigger("change");
            $('#existSupplier').addClass('hide');
            $('#newSupplierDetailAdd').removeClass('hide');
        }else {

           // $('#addSupplierView').addClass('hide');
            $('#addSupplierVal').val(2);
            $('#existSupplier').removeClass('hide');
            $("#sup").val(null).trigger("change");
            $('#newSupplierDetailAdd').addClass('hide');
        }

    });


    function load_supplier_company_request_header() {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyReqID': p_id},
                url: "<?php echo site_url('srm_master/load_supplier_company_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#request_document').empty("");
                        $('#request_document_other').empty("");
                        $('#request_Category').empty("");
                        $('#family_des').empty("");
                       
                        var htqty="";
                        var htqty1="";
                        var htqty2="";
                        var htqty3="";
                        var dataSub=data.masterSub;
                        var dataMaster=data.masterData;
                        var masterOther=data.masterOther;	
                        var masterSubCategory=data.masterSubCategory;
                        var family=data.family;
                        if(dataSub){
                            $.each(dataSub, function (key, value) {

                                if(value.url!=null){
                                    htqty='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" ></span></a>&nbsp;&nbsp;</td></tr>';
                                    if(value.approveYN==1){

                                    htqty='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" ></span></a>&nbsp;&nbsp;<span class="label label-success">Approved</span></td></tr>';
                                    }if(value.approveYN==2){

                                    htqty='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" ></span></a>&nbsp;&nbsp;<span class="label label-danger">Rejected</span></td></tr>';
                                    }
                                }else{
                                    htqty='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.documentName+'</td><td style="min-width: 20%"><span class="label label-primary">Not Submitted</span></td></tr>';
                                }
                               
                               // htqty='<tr><td style="min-width: 20%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye-open" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                                $('#request_document').append(htqty);
                                htqty="";
                            });
                        }

                        if(masterOther){
                            $.each(masterOther, function (key, value) {

                                
                                    htqty1='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.name+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn"></span></a>  </td></tr>';
                                    
                               
                               // htqty='<tr><td style="min-width: 20%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye-open" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                                $('#request_document_other').append(htqty1);
                                htqty1="";
                            });
                        }

                        if(masterSubCategory){
                            $.each(masterSubCategory, function (key, value) {

                                
                                    htqty2='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.category+'</td></td><td style="min-width: 40%">'+value.description+'</td></tr>';
                                    
                               
                               // htqty='<tr><td style="min-width: 20%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye-open" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                                $('#request_Category').append(htqty2);
                                htqty2="";
                            });
                        }

                        if(family){
                            $.each(family, function (key, value) {

                                
                                    htqty3='<tr><td style="min-width: 3%">'+(key+1)+'</td><td style="min-width: 40%">'+value.relationship+'</td></td><td style="min-width: 40%">'+value.ename+'</td><td style="min-width: 40%">'+value.designationName+'</td></tr>';
                                    
                               
                               // htqty='<tr><td style="min-width: 20%">'+value.documentName+'</td><td style="min-width: 20%"><a href="'+value.url+'" target="_blank"> <span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye-open" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                                $('#family_des').append(htqty3);
                                htqty3="";
                            });
                        }

                        //Basic details
                        
                        $("#btn_status").val(dataMaster['approveYN']);

                        if(dataMaster['confirmYN']==0){
                            $('#btn_dev').removeClass('hide');
                        }else{
                            $('#btn_dev').addClass('hide');
                        }

                        if(dataMaster['isPdo']==1){
                            $('#ispdo').prop('checked', true);
                        }

                        if(dataMaster['isdcrp']==1){
                            $('#isdcrp').prop('checked', true);
                        }

                        if(dataMaster['haveCetification']==1){
                            $('#havecertifi').prop('checked', true);
                        }

                        if(dataMaster['accountType']==1){
                            $('#existSupplier').prop('checked', true);
                            $('#existSupplier').prop('disabled', true);
                            $('#addSupplier').prop('disabled', true);
                            $('#existSupplierView').removeClass('hide');
                           // $('#sup').val(dataMaster['certification']).change();
                            $('#sup_data_show_old').addClass('hide');

                            //load supplier details
                            var masterSupplier=data.masterSupplier;
                            $("#seconeryItemCode2").val(masterSupplier['secondaryCode']);
                            $('#partyCategoryID2').val(masterSupplier['partyCategoryID']).change();
                            $('#liabilityAccount2').val(masterSupplier['liabilityAutoID']).change();
                           //  $('#supplierCurrency2').val(dataMaster['supplierCurrencyID']).change();
                            $('#suppliertaxgroup2').val(masterSupplier['taxGroupID']).change();
                            $('#vatEligible2').val(masterSupplier['vatEligible']).change();

                            $("#nameOnCheque2").val(masterSupplier['nameOnCheque']);
                            $("#supplierCreditPeriod2").val(masterSupplier['supplierCreditPeriod']);
                            $("#supplierCreditLimit2").val(masterSupplier['supplierCreditLimit']);
                            $("#vatPercentage2").val(masterSupplier['vatPercentage']);
                            $("#syatem_comment").val(dataMaster['systemComment']);
                           
                        }else{
                            
                            $('#addSupplier').prop('checked', true);
                            $('#addSupplier').prop('disabled', true);
                            $('#existSupplier').prop('disabled', true);
                            $('#newSupplierDetailAdd').removeClass('hide');
                        }

                   
                            
                        $('#cer').text(dataMaster['certification']);
                         $("#seconeryItemCode").val(dataMaster['secondaryCode']);
                         $("#companyname").val(dataMaster['providerName']);
                         $("#supplierTelephone").val(dataMaster['companyPhone']);
                         $("#supplierEmail").val(dataMaster['contactPersonEmail']);
                         $("#supplierFax").val(dataMaster['companyfax']);
                         $("#supplierAddress1").val(dataMaster['address1']);
                         $("#supplierAddress2").val(dataMaster['address2']);

                         $("#companyRegNo").val(dataMaster['companyRegNo']);
                         $("#brands").val(dataMaster['brands']);
                         $("#licenseExpireDate").val(dataMaster['licenseExpireDate']);
                         $("#yearofEstablishment").val(dataMaster['yearofEstablishment']);
                         $("#natureofBusiness").val(dataMaster['natureofBusiness']);
                         $("#groupCompany").val(dataMaster['groupCompany']);
                         $("#sponsorName").val(dataMaster['sponsorName']);
                         $("#numberofYearBusiness").val(dataMaster['numberofYearBusiness']);
                         $("#numberofBranch").val(dataMaster['numberofBranch']);
                         $("#state").val(dataMaster['state']);
                         $("#city").val(dataMaster['city']);
                         $("#pincode").val(dataMaster['pincode']);
                         $("#contactName").val(dataMaster['contactName']);
                         $("#pointContactphone").val(dataMaster['pointContactphone']);
                         $("#pointofContactRole").val(dataMaster['pointofContactRole']);

                        //  //financial details
                         $("#supplierUrl").val(dataMaster['companyUrl']);
                         $("#vatNumber").val(dataMaster['vatNumber']);
                         $("#vatExpire").val(dataMaster['vatExpire']);
                         $('#suppliercountry').val(dataMaster['country']).change();

                         $('#partyCategoryID').val(dataMaster['category']).change();
                         $('#liabilityAccount').val(dataMaster['liabilityAccount']).change();
                         // $('#supplierCurrency').val(dataMaster['currency']).change();
                         $('#suppliertaxgroup').val(dataMaster['taxGroup']).change();
                         $('#vatEligible').val(dataMaster['vatElifible']).change();

                         $("#nameOnCheque1").val(dataMaster['nameOfCheque']);
                         $("#supplierCreditPeriod").val(dataMaster['creditPeriod']);
                         $("#supplierCreditLimit").val(dataMaster['creditLimit']);
                         $("#vatPercentage").val(dataMaster['vatPercentage']);
                         $("#add_comment").val(dataMaster['systemComment']);
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function company_request_confirm_info(){
        var data = $('#company_request_confirm_form').serializeArray();

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            data.push({'name': 'requestMasterID', 'value':p_id});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Srm_master/company_request_confirm_info'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        stopLoad();
                        if(data[0]){
                            myAlert(data[0], data[1]);
                        }
                        
                        if(data[0]=='s'){
                            //$('[href=#step4]').tab('show');
                            fetchPage('system/srm/srm_suppliermaster', '', 'Supplier Master');
                        }
                        
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
        }
    }

    function company_request_submit_basic_info(){
        var data = $('#company_request_basic_form').serializeArray();

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        $('[href=#step3]').tab('show');
        // if (p_id) {
        //     data.push({'name': 'requestMasterID', 'value':p_id});
        //     $.ajax(
        //         {
        //             async: true,
        //             type: 'post',
        //             dataType: 'json',
        //             data: data,
        //             url: "<?php echo site_url('Srm_master/save_company_request_basic_info'); ?>",
        //             beforeSend: function () {
        //                 startLoad();
                       
        //             },
        //             success: function (data) {
        //                 stopLoad();
        //                 if(data[0]){
        //                     myAlert(data[0], data[1]);
        //                 }

        //                 if(data.error==0){
        //                     $('[href=#step2]').tab('show');
        //                 }
                        
        //             }, error: function () {
        //                 alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                       
        //                 stopLoad();
        //                 refreshNotifications(true);
        //             }
        //     });
        // }
    }

    function company_request_submit_financial_info(){
        var data = $('#company_request_financial_form').serializeArray();

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        
        if (p_id) {
            data.push({'name': 'requestMasterID', 'value':p_id});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Srm_master/save_company_request_financial_info'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        stopLoad();
                        
                        if(data[0]){
                            myAlert(data[0], data[1]);
                        }
                        if(data.error==0){
                            $('[href=#step3]').tab('show');
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
        }
    }

    function company_request_submit_other_info(){
       // var data = $('#company_request_basic_form').serializeArray();

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {"requestMasterID":p_id},
                    url: "<?php echo site_url('Srm_master/fetch_confirm_company_request_info'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        stopLoad();

                        $("#seconeryItemCodeV").text(data['secondaryCode']);
                        
                        $("#companynamev").text(data['providerName']);
                        $("#supplierUrlv").text(data['companyUrl']);

                        $('[href=#step4]').tab('show');
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
            
        }
    }

    function vendor_company_request_document_approve(id) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'requestID': id},
                        url: "<?php echo site_url('srm_master/vendor_company_request_document_approve'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            load_submit_attachment();
                            // if(data[0]=='s'){
                            //     fetchPage('system/srm/srm_order_review_management', '', 'Order Review Master');
                            // }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function approve_vendor_company_document_request(id,type){
        $("#company_request_doc_approve_modal").modal("show");
        $('#requestDocID').val(id);
        $('#typeReq').val(type);
    }

    function company_request_reject_info(type){

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        $("#company_request_reject_modal").modal("show");
        $('#requestDocID_rej').val(p_id);
        $('#typeReq_rej').val(type);
    }

    function saveCompanyRequestRejectRequest(){
        var data = $('#company_req_reject_form').serializeArray();

           // data.push({'name': 'requestMasterID', 'value':p_id});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Srm_master/vendor_company_request_reject'); ?>",
                    beforeSend: function () {
                        startLoad();
                       
                    },
                    success: function (data) {
                        stopLoad();
                        if(data[0]){
                            myAlert(data[0], data[1]);
                           
                        }

                        if(data[0]=="s"){
                            $("#company_request_reject_modal").modal("hide");
                            fetchPage('system/srm/srm_vendor_company_request', '', 'Company Request')
                        }
                        
                        
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
        
    }

    
    function saveCompanyDocumentRejectRequest(){
        var data = $('#company_doc_reject_form').serializeArray();

           // data.push({'name': 'requestMasterID', 'value':p_id});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Srm_master/vendor_company_request_document_reject'); ?>",
                    beforeSend: function () {
                        startLoad();
                       
                    },
                    success: function (data) {
                        stopLoad();
                        if(data[0]){
                            myAlert(data[0], data[1]);
                           
                        }

                        if(data[0]=="s"){
                            $("#company_request_doc_approve_modal").modal("hide");
                            load_submit_attachment();
                        }
                        
                        
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
        
    }

    function load_submit_attachment(){
        load_supplier_company_request_header();
        $('[href=#step3]').tab('show');
    }

</script>