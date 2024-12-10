<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$CI =& get_instance();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$countries_arr = load_all_countrys();
$leadSource_arr = all_crm_leadSource();
$status_arr = lead_status();
$employees_arr = all_crm_employees_drop();
$organization_arr = load_all_organizations();
$groupmaster_arr = all_crm_groupMaster();
$employees_arr = fetch_employees_by_company_multiple(false);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    span.input-req-inner {
        width: 20px;
        height: 40px;
        position: absolute;
        overflow: hidden;
        display: block;
        right: 4px;
        top: -15px;
        -webkit-transform: rotate(135deg);
        -ms-transform: rotate(135deg);
        transform: rotate(135deg);
    }

    span.input-req-inner:before {
        font-size: 20px;
        content: "*";
        top: 15px;
        right: 1px;
        color: #fff;
        position: absolute;
        z-index: 2;
        cursor: default;
    }

    span.input-req-inner:after {
        content: '';
        width: 35px;
        height: 35px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #f45640;
        position: absolute;
        top: 7px;
        right: -29px;
    }
    span.input-req-innernotmandertory {
        width: 20px;
        height: 40px;
        position: absolute;
        overflow: hidden;
        display: block;
        right: 4px;
        top: -15px;
        -webkit-transform: rotate(135deg);
        -ms-transform: rotate(135deg);
        transform: rotate(135deg);
    }
    span.input-req-innernotmandertory:before {
        font-size: 20px;
        content: "*";
        top: 15px;
        right: 1px;
        color: #fff;
        position: absolute;
        z-index: 2;
        cursor: default;
    }
    span.input-req-innernotmandertory:after {
        content: '';
        width: 35px;
        height: 35px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #f45640;
        position: absolute;
        top: 7px;
        right: -29px;
    }


    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
</style>

<?php echo form_open('', 'role="form" id="lead_form"'); ?>
<div class="row">

        <div class="col-md-3 pull-right">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <span class="input-req" title="Required Field">
                           <td><span style="background-color: #4b0082 !important;">&nbsp;</span> Required to fill before converting to opportunity</td>
<span class="input-req-innernotmandertory"></span></span>

                </tr>
            </table>
        </div>
<br>
<br>
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_lead_information');?></h2><!--LEAD INFORMATION-->
        </header>
        <div class="row hide" style="margin-top: 10px;" id="linkcontact_text">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_search_contact');?></label><!--Search Contact-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" class="form-control f_search valcontact" name="contactname" id="contactname"
                       placeholder="<?php echo $this->lang->line('crm_search_comtact_name');?>.."><!--Search Contact Name-->
                <input type="hidden" name="contactID" id="contactID">
                <input type="hidden" name="mastersave" id="mastersave" value="1">
            </div>
            <div class="col-sm-1 search_cancel" style="width: 3%;">
                <i class="fa fa-external-link" onclick="unlinkContact()" title="Link to Contact" aria-hidden="true"
                   style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_first_name');?></label><!--First Name-->
            </div>
            <div class="form-group col-sm-1">
                               <span class="input-req" title="Required Field"><input type="text" name="prefix"
                                                                                     id="prefix"
                                                                                     class="form-control valcontact calcontactread"
                                                                                     placeholder="<?php echo $this->lang->line('crm_prefix');?>"
                                                                                     required  autocomplete="off"><span
                                       class="input-req-inner"></span></span><!--Prefix-->
            </div>
            <div class="form-group col-sm-3">
                               <span class="input-req" title="Required Field"><input type="text" name="firstName"
                                                                                     id="firstName"
                                                                                     class="form-control valcontact calcontactread"
                                                                                     placeholder="<?php echo $this->lang->line('crm_first_name');?>"
                                                                                     required autocomplete="off"><span
                                       class="input-req-inner"></span></span><!--First Name-->
                <input type="hidden" name="leadID" id="leadID_edit">
            </div>
            <div class="col-sm-1 search_cancel" style="width: 3%;" id="contact_text">
                <i class="fa fa-link" onclick="linkContact()" title="Link to Contact" aria-hidden="true"
                   style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_last_name');?></label><!--Last Name-->
            </div>
            <div class="form-group col-sm-4">
                          <span class="input-req" title="Required Field"><input type="text" name="lastName"
                                                                                id="lastName"
                                                                                class="form-control valcontact calcontactread"
                                                                                placeholder="<?php echo $this->lang->line('crm_last_name');?>"
                                                                                required autocomplete="off"><span
                                  class="input-req-inner"></span></span><!--Last Name-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_title');?></label> <!--Title-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="title" id="title" class="form-control valcontact calcontactread"
                       placeholder="<?php echo $this->lang->line('common_title');?>" autocomplete="off"><!--Title-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;" id="organization_text">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_organization');?></label><!--Organization-->

            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <input type="text" name="organization" id="organization" class="form-control"
                       placeholder="<?php echo $this->lang->line('crm_organization');?>" autocomplete="off"><!--Organization-->
                    <span class="input-req-innernotmandertory"></span></span>
            </div>
            <div class="col-sm-1 search_cancel" style="width: 3%;">
                <i class="fa fa-link" onclick="linkOrganization()" title="Link to Organization" aria-hidden="true"
                   style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
            </div>
        </div>
        <div class="row hide" id="linkorganization_text" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_link_organization');?></label><!--Link Organization-->

            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('linkorganization', $organization_arr, '', 'class="form-control select2" id="linkorganization"'); ?>
            </div>
            <div class="col-sm-1 search_cancel" style="width: 3%;">
                <i class="fa fa-external-link" onclick="unlinkOrganization()" title="Link to Organization"
                   aria-hidden="true" style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_status');?></label><!--Status-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req"
                      title="Required Field"><?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control" id="statusID"'); ?>
                    <span class="input-req-innernotmandertory"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_user_responsible');?></label><!--User Responsible-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req"
                      title="Required Field"><?php echo form_dropdown('responsiblePersonEmpID', $employees_arr, $CI->session->userdata("empID"), 'class="form-control select2" id="responsiblePersonEmpID"'); ?>
                    <span class="input-req-innernotmandertory"></span></span>
            </div>
        </div>
        <div class="row hide" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_lead_rating');?> </label><!--Lead Rating-->
            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('ratingID', $status_arr, '', 'class="form-control" id="ratingID"'); ?>

            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_additional_information');?> </h2><!--ADDITIONAL INFORMATION-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_email');?> </label><!--Email-->
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><input type="text" name="email" id="email"
                                                                      class="form-control valcontact"
                                                                      placeholder="<?php echo $this->lang->line('common_email');?>"
                                                                      autocomplete="off"><span
                        class="input-req-innernotmandertory"></span></span><!--Email-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_phone_mobile');?></label><!--Phone (Mobile)-->
            </div>
            <div class="form-group col-sm-4">
                                     <span class="input-req" title="Required Field"><input type="text"
                                                                                           name="phoneMobile"
                                                                                           id="phoneMobile"
                                                                                           class="form-control valcontact"
                                                                                           placeholder="<?php echo $this->lang->line('crm_phone_mobile');?>"
                                                                                           autocomplete="off"><span
                                             class="input-req-innernotmandertory"></span></span><!--Phone (Mobile)-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_phone_home');?> </label><!--Phone (Home)-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="phoneHome" id="phoneHome" class="form-control valcontact"
                       placeholder="<?php echo $this->lang->line('crm_phone_home');?>" autocomplete="off"><!--Phone (Home)-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_fax');?> </label><!--Fax-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="fax" id="fax" class="form-control valcontact" placeholder="<?php echo $this->lang->line('common_fax');?>" autocomplete="off"><!--Fax-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_website');?> </label><!--Website-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="website" id="website" class="form-control valcontact"
                       placeholder="<?php echo $this->lang->line('crm_website');?>" autocomplete="off"><!--Website-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_industry');?> </label><!--Industry-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="industry" id="industry" class="form-control valcontact"
                       placeholder="<?php echo $this->lang->line('crm_industry');?>" autocomplete="off"><!--Industry-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_number_of_employees');?> </label><!--Number of Employees-->

            </div>
            <div class="form-group col-sm-4">
                <input type="number" name="numberofEmployees" id="numberofEmployees" class="form-control"
                       placeholder="<?php echo $this->lang->line('crm_number_of_employees');?>"><!--Number of Employees-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">

                <label class="title"><?php echo $this->lang->line('crm_lead_source');?></label><!--Lead Source-->

            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('sourceID', $leadSource_arr, '', 'class="form-control" id="sourceID"  required'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="z-index: 100;">
                <label class="title"><?php echo $this->lang->line('crm_lead_expiry');?> </label><!--Lead Expiry-->
            </div>
            <div class="form-group col-sm-4">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="expirydate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" id="expirydate" class="form-control">
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_address_capital');?></h2><!--ADDRESS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_postal_code');?></label><!--Postal Code-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="postalcode" id="postalcode" class="form-control valcontact"
                       placeholder="<?php echo $this->lang->line('crm_postal_code');?>"><!--Postal Code-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_city');?></label><!--City-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="city" id="city" class="form-control valcontact" placeholder="<?php echo $this->lang->line('crm_city');?>"><!--City-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_state');?></label><!--State-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="state" id="state" class="form-control valcontact" placeholder="<?php echo $this->lang->line('crm_state');?>"><!--State-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_Country');?></label><!--Country-->
            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2 valcontact" id="countryID"'); ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address');?></label><!--Address-->
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control valcontact" id="address" name="address" rows="2"></textarea>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_permissions');?></h2><!--PERMISSIONS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_visibility');?> </label><!--Visibility-->
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input id="isPermissionEveryone" type="radio"
                                                                  data-caption="" class="columnSelected"
                                                                  name="userPermission"
                                                                  value="1"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_everyone');?></label><!--Everyone-->
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionCreator"
                                                                  type="radio" data-caption="" class="columnSelected"
                                                                  value="2"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_only_for_me');?> </label><!--Only For Me-->
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionGroup"
                                                                  type="radio"
                                                                  data-caption="" class="columnSelected"
                                                                  onclick="leadPermission(3)"
                                                                  value="3"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_a_group');?></label><!--Select a Group-->
            </div>
        </div>
        <div class="row hide" id="show_groupPermission">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-4" style="margin-left: 2%;">
                <?php echo form_dropdown('groupID', $groupmaster_arr, '', 'class="form-control" id="groupID"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionMultiple"
                                                                  type="radio"
                                                                  data-caption="" class="columnSelected"
                                                                  onclick="leadPermission(4)"
                                                                  value="4"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_multiple_pepole');?></label><!--Select Multiple People-->
            </div>
        </div>
        <div class="row hide" id="show_multiplePermission">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-4" style="margin-left: 2%;">
                <?php echo form_dropdown('employees[]', $employees_arr, '', 'class="form-control select2" id="employeesID"  multiple="" style="z-index: 0;"'); ?>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_description');?> </h2><!--DESCRIPTION-->
        </header>
        <div class="row">
            <div class="form-group col-sm-10" style="margin-top: 5px;">
                                <textarea class="form-control" rows="5"
                                                                                         name="description"
                                                                                         id="description"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="text-right m-t-xs">
                <div class="form-group col-sm-10" style="margin-top: 10px;">
                    <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/crm/lead_management', '', 'Lead');
        });
        $('.select2').select2();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        leadID = null;

        initializeContactTypeahead();

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            leadID = p_id;
            load_lead_header();
        } else {
            $("#description").wysihtml5();
        }

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#lead_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                prefix: {validators: {notEmpty: {message: 'Prefix is required.'}}},
                firstName: {validators: {notEmpty: {message: 'First Name is required.'}}},
                lastName: {validators: {notEmpty: {message: 'Last Name is required.'}}},
                //email: {validators: {notEmpty: {message: 'Email is required.'}}},
                //phoneMobile: {validators: {notEmpty: {message: 'Phone Mobile is required.'}}},
                //statusID: {validators: {notEmpty: {message: 'Status is required.'}}},
                sourceID: {validators: {notEmpty: {message: 'Lead Source is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmLead/save_lead_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/crm/lead_management', '', 'Leads');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }

                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#isPermissionEveryone').iCheck('check');

        $("#isPermissionEveryone").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_multiplePermission").addClass('hide');
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
        });

        $("#isPermissionCreator").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_multiplePermission").addClass('hide');
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
        });

        $("#isPermissionGroup").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_groupPermission").removeClass('hide');
            $("#show_multiplePermission").addClass('hide');
        });

        $("#isPermissionMultiple").on("ifChanged", function () {
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
            $("#show_multiplePermission").removeClass('hide');
        });

    });

    function fetch_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': leadID},
            url: "<?php echo site_url('Crm/fetch_lead_employee_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                }
                else {
                    $.each(data, function (key, value) {
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + '</td><td>' + value['Ename2'] + '</td><td class="text-right"><a onclick="delete_lead_detail(' + value['AssingeeID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
                        x++;
                    });
                    $('#t_total').html(parseFloat(tot_amount).formatMoney(currency_decimal, '.', ','));
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_lead_header() {
        if (leadID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'leadID': leadID},
                url: "<?php echo site_url('CrmLead/load_lead_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('#leadID_edit').val(leadID);
                        $('#prefix').val(data['header']['prefix']);
                        $('#firstName').val(data['header']['firstName']);
                        $('#lastName').val(data['header']['lastName']);
                        $('#title').val(data['header']['title']);
                        $('#statusID').val(data['header']['statusID']);
                        $('#responsiblePersonEmpID').val(data['header']['responsiblePersonEmpID']).change();
                        $('#email').val(data['header']['email']);
                        $('#phoneMobile').val(data['header']['phoneMobile']);
                        $('#phoneHome').val(data['header']['phoneHome']);
                        $('#fax').val(data['header']['fax']);
                        $('#website').val(data['header']['website']);
                        $('#industry').val(data['header']['industry']);
                        $('#numberofEmployees').val(data['header']['numberofEmployees']);
                        $('#sourceID').val(data['header']['sourceID']);
                        $('#postalcode').val(data['header']['postalCode']);
                        $('#city').val(data['header']['city']);
                        $('#state').val(data['header']['state']);
                        $('#countryID').val(data['header']['countryID']).change();
                        $('#address').val(data['header']['address']);
                        $('#expirydate').val(data['header']['expiryDate']);
                        $("#description").wysihtml5();
                        $('#description').val(data['header']['description']);
                        if (data['header']['organization'] != '') {
                            $('#organization').val(data['header']['organization']);
                        } else {
                            linkOrganization();
                            $('#linkorganization').val(data['header']['linkedorganizationID']).change();
                        }
                        $('#contactID').val(data['header']['contactID']);
                       // $('#contact_text').addClass('hide');
                    }
                    if (!jQuery.isEmptyObject(data['permission'])) {
                        var selectedItems = [];
                        $.each(data['permission'], function (key, value) {
                            if (value.permissionID == 1) {
                                $('#isPermissionEveryone').iCheck('check');
                            } else if (value.permissionID == 2) {
                                $('#isPermissionCreator').iCheck('check');
                            } else if (value.permissionID == 3) {
                                $('#isPermissionGroup').iCheck('check');
                                $('#groupID').val(value.permissionValue);
                            } else if (value.permissionID == 4) {
                                $('#isPermissionMultiple').iCheck('check');
                                selectedItems.push(value.empID);
                                $('#employeesID').val(selectedItems).change();
                            }
                        });
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function delete_lead_detail(id) {
        if (leadID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'AssingeeID': id},
                        url: "<?php echo site_url('CrmLead/delete_lead_master'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_detail();
                            }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function linkOrganization() {
        $('#organization').val('');
        $('#linkorganization_text').removeClass('hide');
        $('#organization_text').addClass('hide');
    }

    function unlinkOrganization() {
        $('#linkorganization_text').addClass('hide');
        $('#organization_text').removeClass('hide');
    }

    function linkContact() {
        $('#linkcontact_text').removeClass('hide');
        $('#contact_text').addClass('hide');
    }

    function unlinkContact() {
        $('#contactID').val('');
        $('.valcontact').val('');
        $("#countryID").val(null).trigger("change");
        $(".calcontactread").prop("readonly", false);
        $('#linkcontact_text').addClass('hide');
        $('#contact_text').removeClass('hide');
    }

    function initializeContactTypeahead() {
        $('#contactname').autocomplete({
            serviceUrl: '<?php echo site_url();?>Crm/fetch_contact_relate_search/',
            onSelect: function (suggestion) {
                $('#contactID').val(suggestion.contactID);
                $('#prefix').val(suggestion.prefix);
                $('#firstName').val(suggestion.firstName);
                $('#lastName').val(suggestion.lastName);
                $('#title').val(suggestion.title);
                $('#phoneMobile').val(suggestion.phoneMobile);
                $('#email').val(suggestion.email);
                $('#phoneHome').val(suggestion.phoneHome);
                $('#fax').val(suggestion.fax);
                $('#postalcode').val(suggestion.postalCode);
                $('#state').val(suggestion.state);
                $('#city').val(suggestion.city);
                $('#address').val(suggestion.address);
                $('#address').val(suggestion.organizationname);
                $('#countryID').val(suggestion.countryID).change();
                $("#prefix").prop("readonly", true);
                $("#firstName").prop("readonly", true);
                $("#lastName").prop("readonly", true);
                $("#title").prop("readonly", true);
            }
        });
    }


</script>