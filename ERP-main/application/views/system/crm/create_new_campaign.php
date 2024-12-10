<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('crm_helper');
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$status_arr = all_campaign_status();
$types_arr = all_campaign_types();
$employees_arr = fetch_employees_by_company_multiple(false);
$isgroupadmin = crm_isGroupAdmin();
$admin = crm_isSuperAdmin();
$cuurentuser = current_userID();
$countries_arr = load_all_countrys();
$groupmaster_arr = all_crm_groupMaster();
$current_userid = current_userID();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .posts-holder {
        padding: 0 0 10px 4px;
        margin-right: 10px;
    }

    #toolbar, .past-info .toolbar {
        background: #f8f8f8;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        border-radius: 3px 3px 0 0;
        -webkit-border-radius: 3px 3px 0 0;
        border: #dcdcdc solid 1px;
        padding: 5px 15px 12px 10px;
        height: 20px;
    }

    .past-info {
        background: #fff;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 0 0 8px 10px;
        margin-left: 2px;
    }

    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
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
    .bootstrap-datetimepicker-widgets{
        z-index: 99999999999999999999999999 !important;
        position: relative;
        background-color:#ccc;
    }

</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('crm_step_one');?> - <?php echo $this->lang->line('crm_campaign_header');?></a><!--Step 1--> <!--Campaign Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_campaign_attendees_detail_table()"
       data-toggle="tab"><?php echo $this->lang->line('crm_step_two');?> - <?php echo $this->lang->line('crm_campaign_attendees');?></a><!--Step 2--> <!--Campaign Attendees-->
    <a class="btn btn-default btn-wizard" href="#step3" onclick="campaign_multiple_attachemts()" data-toggle="tab"><?php echo $this->lang->line('crm_step_three');?> - <?php echo $this->lang->line('crm_campaign_attachments');?></a><!--Step 3--><!--Campaign Attachments-->
    <a class="btn btn-default btn-wizard hide" id="emailCampaignTab" href="#step4" onclick="email_campaign()"
       data-toggle="tab"><?php echo $this->lang->line('crm_step_four');?> - <?php echo $this->lang->line('crm_email_campaign');?></a><!--Step 4--><!--Email Campaign-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="campaign_header_form"'); ?>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('crm_campaign_details');?></h2><!--CAMPAIGN DETAILS-->
                </header>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 10px;">
                        <label class="title"><?php echo $this->lang->line('crm_campaign_type');?></label><!--Campaign Type-->
                    </div>
                    <div class="form-group col-sm-4" style="margin-top: 10px;">
                            <span class="input-req"
                                  title="Required Field"><?php echo form_dropdown('typeID', $types_arr, '', 'class="form-control select2" id="typeID" onchange="check_isEmail_campaign()" required'); ?>
                                <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_campaign_name');?></label><!--Campaign Name-->

                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field"><input type="text" name="campaign_name"
                                                                                  id="campaign_name"
                                                                                  class="form-control" required><span
                                    class="input-req-inner"></span></span>
                        <input type="hidden" name="campaignID" id="campaignID_edit">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_objective');?></label><!--Objective-->
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field"><textarea class="form-control" rows="3"
                                                                                     name="objective" id="objective"
                                                                                     required></textarea><span
                                    class="input-req-inner"></span></span>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <header class="head-title">
                            <h2><?php echo $this->lang->line('crm_campaign_details');?></h2><!--CAMPAIGN DETAILS-->
                        </header>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_status');?></label><!--Status-->
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req"
                                  title="Required Field"><?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control select2" onchange="statuscheack(this.value)" id="statusID"  required'); ?>
                                <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 10px;">
                        <label class="title"><?php echo $this->lang->line('common_start_date');?></label><!--Start Date-->
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="startdate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="" id="startdate" class="form-control " >
                        </div>
                            <span class="input-req-inner" style="z-index:100;"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_end_date');?></label><!--End Date-->
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="end_date"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="" id="end_date" class="form-control">
                        </div>
                             <span class="input-req-inner" style="z-index:100;"></span></span>
                    </div>
                </div>
                <div class="row closedatehideshow hide" style="z-index:100;">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Close Date</label><!--Due Date-->
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                             <div class="input-group dateDatepic">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" name="closedate"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $current_date; ?>" id="closedate"
                                        class="form-control" required>
                             </div>
                             <span class="input-req-inner" style="z-index:100;"></span></span>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <header class="head-title">
                            <h2><?php echo $this->lang->line('crm_campaign_assignee');?></h2><!--CAMPAIGN ASSIGNEE-->
                        </header>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 10px;">
                        <label class="title"><?php echo $this->lang->line('crm_assignee');?></label><!--Assignee-->
                    </div>
                    <div class="form-group col-sm-4"
                         style="margin-top: 5px;"><?php echo form_dropdown('employees[]', $employees_arr, '', 'class="form-control select2" id="employeesID"  multiple="" '); ?>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('crm_permissions');?></h2><!--PERMISSIONS-->
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('crm_visibility');?></label><!--Visibility-->
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
                                                                          type="radio" data-caption=""
                                                                          class="columnSelected" value="2"><label
                                    for="checkbox">&nbsp;</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2" style="margin-left: -6%;">
                        <label style="font-weight: 400"><?php echo $this->lang->line('crm_only_for_me');?></label><!--Only For Me-->
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
                            <div class="skin-section extraColumns"><input name="userPermission"
                                                                          id="isPermissionMultiple"
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
                        <?php echo form_dropdown('employees_permission[]', $employees_arr, '', 'class="form-control select2" id="employees_permission"  multiple="" style="z-index: 0;"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('crm_description');?></h2><!--DESCRIPTION-->
                </header>
                <div class="row">
                    <div class="form-group col-sm-10" style="margin-top: 5px;">
                                <span class="input-req" title="Required Field"><textarea class="form-control" rows="5"
                                                                                         name="description"
                                                                                         id="description"></textarea><span
                                        class="input-req-inner" style="top: 25px;"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="text-right m-t-xs">
                        <div class="form-group col-sm-10" style="margin-top: 10px;">
                            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?></button><!--Save-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div id="attendees_form_show">
            <?php echo form_open('', 'role="form" id="campaign_attendees_form"'); ?>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <div class="row">
                        <div class="col-md-12 animated zoomIn">
                            <header class="head-title">
                                <h2><?php echo $this->lang->line('crm_name_and_occupation');?> </h2><!--NAME AND OCCUPATION-->
                            </header>
                        </div>
                    </div>
                    <div class="row hide" style="margin-top: 10px;" id="linkcontact_text">
                        <div class="form-group col-sm-1">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_search_contact');?></label><!--Search Contact-->
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" class="form-control f_search valcontact" name="contactname"
                                   id="contactname"
                                   placeholder="Search Contact Name..">
                            <input type="hidden" name="contactID" id="contactID">
                        </div>
                        <div class="col-sm-1 search_cancel" style="width: 3%;">
                            <i class="fa fa-external-link" onclick="unlinkContact()" title="Unlink to Contact"
                               aria-hidden="true"
                               style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-1">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_first_name');?> </label><!--First Name-->
                        </div>
                        <div class="form-group col-sm-1">
                               <span class="input-req" title="Required Field"><input type="text" name="prefix"
                                                                                     id="prefix"
                                                                                     class="form-control valcontact calcontactread"
                                                                                     placeholder="<?php echo $this->lang->line('crm_prefix');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Prefix-->
                        </div>
                        <div class="form-group col-sm-3">
                               <span class="input-req" title="Required Field"><input type="text" name="firstName"
                                                                                     id="firstName"
                                                                                     class="form-control valcontact calcontactread"
                                                                                     placeholder="<?php echo $this->lang->line('crm_first_name');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--First Name-->
                            <input type="hidden" name="campaignID" id="campaignID_attendees_edit">
                            <input type="hidden" name="attendeesID" id="attendeesID_edit">
                        </div>
                        <div class="col-sm-1 search_cancel" style="width: 3%;" id="contact_text">
                            <i class="fa fa-link" onclick="linkContact()" title="Link to Contact" aria-hidden="true"
                               style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_last_name');?> </label><!--Last Name-->

                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                                <span class="input-req" title="Required Field"><input type="text" name="lastName"
                                                                                      id="lastName"
                                                                                      class="form-control valcontact calcontactread"
                                                                                      placeholder="<?php echo $this->lang->line('crm_last_name');?>"
                                                                                      required><span
                                        class="input-req-inner"></span></span><!--Last Name-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_occupation');?> </label><!--Occupation-->

                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="occupation" id="occupation"
                                   class="form-control valcontact calcontactread"
                                   placeholder="<?php echo $this->lang->line('crm_role');?> "><!--Role-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_organization');?> </label><!--Organization-->

                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="organization" id="organization" class="form-control valcontact"
                                   placeholder="<?php echo $this->lang->line('crm_organization');?>"><!--Organization-->
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <div class="row">
                        <div class="col-md-12 animated zoomIn">
                            <header class="head-title">
                                <h2><?php echo $this->lang->line('crm_contact_details');?></h2><!--CONTACT DETAILS-->
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2" style="margin-top: 5px;">
                            <label class="title"><?php echo $this->lang->line('common_email');?></label><!--Email-->

                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                            <span class="input-req" title="Required Field"><input type="text" name="email" id="email"
                                                                                  class="form-control valcontact"
                                                                                  placeholder="<?php echo $this->lang->line('common_email');?>" required><span
                                    class="input-req-inner"></span></span><!--Email-->
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_phone_mobile');?></label><!--Phone (Mobile)-->
                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                                <span class="input-req" title="Required Field"><input type="text" name="phoneMobile"
                                                                                      id="phoneMobile"
                                                                                      class="form-control valcontact"
                                                                                      placeholder="<?php echo $this->lang->line('crm_phone_mobile');?>"
                                                                                      required><span
                                        class="input-req-inner"></span></span><!--Phone (Mobile)-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('crm_phone_home');?></label><!--Phone (Home)-->
                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                            <input type="text" name="phoneHome" id="phoneHome" class="form-control valcontact"
                                   placeholder="<?php echo $this->lang->line('crm_phone_home');?>">
                        </div><!--Phone (Home)-->
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_fax');?></label><!--Fax-->
                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                            <input type="text" name="fax" id="fax" class="form-control valcontact" placeholder="<?php echo $this->lang->line('common_fax');?>"><!--Fax-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_address');?></label><!--Address-->
                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                            <textarea class="form-control valcontact" id="address" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <div class="row">
                        <div class="col-md-12 animated zoomIn">
                            <header class="head-title">
                                <h2><?php echo $this->lang->line('crm_other_details');?></h2><!--OTHER DETAILS-->
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-2" style="margin-top: 5px;">
                            <label class="title"><?php echo $this->lang->line('common_Country');?></label><!--Country-->

                        </div>
                        <div class="form-group col-sm-4" style="margin-top: 5px;">
                            <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" id="countryID"'); ?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-sm-6">
                            <div class="text-right m-t-xs">
                                <button class="btn btn-danger" type="button" onclick="campaign_attendees_close()">
                                    <?php echo $this->lang->line('common_Close');?>
                                </button><!--Close-->
                                <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="campaign_attendees_table_show">
            <div class="row">
                <div class="col-md-4">
                    <a href="<?php echo base_url('uploads/crm/attendeesExcel/CampainAttendees.csv'); ?>" type="button" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel-o"></i><?php echo $this->lang->line('crm_download_excel');?>
                    </a><!--Download Excel-->
                    <a onclick="addNew_lead()" type="button" class="btn btn-success btn-sm" style="background-color: #a60017">
                        <i class="fa fa-cog" aria-hidden="true"></i> <?php echo $this->lang->line('crm_convert_to_lead');?>
                    </a><!--Convert to Lead-->
                </div>
                <div class="col-md-8">
                    <button type="button" onclick="campaign_attendees_table()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i><?php echo $this->lang->line('crm_add_attendees');?>
                    </button><!--Add Attendees-->
                    <button type="button" class="btn btn-default pull-right" title="Upload Excel" onclick="uploadExcelAttendees_model()" style="margin-right: 1%;"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i><?php echo $this->lang->line('crm_campaign_attendees');?> </h4></div><!--Campaign Attendees-->
            </div>
            <table class="table table-bordered table-striped table-condesed">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 20%"><?php echo $this->lang->line('common_name');?></th><!--Name-->
                    <th style="min-width: 25%"><?php echo $this->lang->line('crm_organization');?></th><!--Organization-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('crm_mobile_no');?></th><!--Mobile No-->
                    <th style="min-width: 8%"><?php echo $this->lang->line('crm_completed');?></th><!--Completed-->
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
                </tr>
                </thead>
                <tbody id="attendees_table_body">
                <tr class="danger">
                    <td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td><!--No Records Found-->
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i><?php echo $this->lang->line('crm_campaign_attachments');?> </h4></div><!--Campaign Attachments-->
            <div class="col-md-4">
                <button type="button" onclick="show_campaign_button()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('crm_add_attachment');?>
                </button><!--Add Attachment-->
            </div>
        </div>
        <div class="row hide" id="add_attachemnt_show">
            <?php echo form_open_multipart('', 'id="campaign_attachment_uplode_form" class="form-inline"'); ?>
            <div class="col-sm-10" style="margin-left: 3%">
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="campaignattachmentDescription"
                               name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description');?>..." style="width: 240%;"><!--Description-->
                        <input type="hidden" class="form-control" id="documentID" name="documentID" value="1">
                        <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                               value="Campaign">
                        <input type="hidden" class="form-control" id="campaign_documentAutoID" name="documentAutoID">
                    </div>
                </div>
                <div class="col-sm-8" style="margin-top: -8px;">
                    <div class="form-group">
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                             style="margin-top: 8px;">
                            <div class="form-control" data-trigger="fileinput"><i
                                    class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                    class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_file" id="document_file"></span>
                            <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                               data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                              aria-hidden="true"></span></a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                            class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                    </form>
                </div>
            </div>
        </div>
        <br>

        <div id="campaign_multiple_attachemts"></div>
    </div>
    <div id="step4" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i><?php echo $this->lang->line('crm_email_campaign');?></h4></div><!--Email Campaign-->
            <div class="col-md-4">
            </div>
        </div>
        <br>

        <div id="emailCampaign_body"></div>
    </div>
</div>

<div class="modal fade" id="attendees_Excel_upload_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('crm_upload_campaign_attendees_in_excel');?></h4>
            </div><!--Upload Campaign Attendees in Excel-->
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="campaign_attendeesExcel_uplode_form" class="form-inline"'); ?>
                    <div class="col-sm-10" style="margin-left: 3%">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control" id="campaignattachmentDescription"
                                       name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description');?>..." style="width: 240%;"><!--Description-->
                                <input type="hidden" class="form-control" id="campaign_attendeesExcel_id" name="campaignID" value="1">
                                <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                       value="Campaign">
                                <input type="hidden" class="form-control" id="campaign_documentAutoID" name="documentAutoID">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                            class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="attendees_excel_uplode()"><span
                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    var selectedItemsSync = [];
    $(document).ready(function () {

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.headerclose').click(function () {
            fetchPage('system/crm/campaign_management', '', 'Campaign');
        });

        $('.select2').select2();

        campaign_attendees_table();

        initializeContactTypeahead();

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#search').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',<!--Search-->
                right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',<!--Search-->
            },
            afterMoveToLeft: function ($left, $right, $options) {
                $("#search_to option").prop("selected", "selected");
            }
        });
        $('.dateDatepic').datetimepicker({
            showTodayButton: true,
            format: date_format_policy,
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {

        });

        tinymce.init({
            selector: "#description",
            height: 200,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

            menubar: false,
            toolbar_items_size: 'small',

            style_formats: [{
                title: 'Bold text',
                inline: 'b'
            }, {
                title: 'Red text',
                inline: 'span',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Red header',
                block: 'h1',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Example 1',
                inline: 'span',
                classes: 'example1'
            }, {
                title: 'Example 2',
                inline: 'span',
                classes: 'example2'
            }, {
                title: 'Table styles'
            }, {
                title: 'Table row 1',
                selector: 'tr',
                classes: 'tablerow1'
            }],

            templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            }, {
                title: 'Test template 2',
                content: 'Test 2'
            }]
        });

        Inputmask().mask(document.querySelectorAll("input"));

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#campaign_header_form').bootstrapValidator('revalidateField', 'startdate');
            $('#campaign_header_form').bootstrapValidator('revalidateField', 'end_date');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            campaignID = p_id;
            load_campaign_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');


        }

        frmlock = '<?php if((isset($_POST['data_arr'])) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';
        if (frmlock == 'view') {
            $("input").prop('disabled', true);
            $("select").prop('disabled', true);
            $("textarea").prop('disabled', true);
            $('button').prop('disabled', true);
            $('.headerclose').prop('disabled', false);
        } else {
            $("input").prop('disabled', false);
            $("select").prop('disabled', false);
            $('button').prop('disabled', false);
            $("textarea").prop('disabled', false);
        }

        $('#campaign_header_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                campaign_name: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_campaign_name_is_required');?>.'}}},/*Campaign Name is required*/
                //description: {validators: {notEmpty: {message: 'Description is required.'}}},
                objective: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_objective_is_required');?>.'}}},/*Objective is required*/
                typeID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_type_is_required');?>.'}}},/*Type is required*/
                statusID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                startdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_start_date_id_required');?>.'}}},/*Start Date is required*/
                end_date: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_end_date_is_required');?>.'}}}/*End Date is required*/
            },
        }).on('success.form.bv', function (e) {
            $('#typeID').prop("disabled", false);
            $("#employeesID").prop("disabled", false);
            $("#employees_permission").prop("disabled", false);
            $('#isPermissionEveryone').iCheck('Enable');
            $('#isPermissionCreator').iCheck('Enable');
            $('#isPermissionGroup').iCheck('Enable');
            $('#isPermissionMultiple').iCheck('Enable');

            e.preventDefault();
            tinymce.triggerSave();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Crm/save_campaign_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        campaignID = data[2];
                        $('#campaignID_attendees_edit').val(campaignID);
                        $('#campaign_documentAutoID').val(campaignID);
                        $('#campaign_attendeesExcel_id').val(campaignID);
                        $('#campaignID_edit').val(campaignID);
                        fetch_campaign_attendees_detail_table();
                        campaign_multiple_attachemts();
                        check_isEmail_campaign();
                        employeeassigntype(campaignID);
                        $('.btn-wizard').removeClass('disabled');
                        $('[href=#step2]').tab('show');
                        $(document).scrollTop(0);
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#campaign_attendees_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                firstName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_first_name_is_required');?>.'}}},/*First Name is required*/
                lastName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_last_name_is_required');?>.'}}},/*Last Name is required*/
                phoneMobile: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_phone_mobile_is_required');?>.'}}},/*Phone (Mobile) is required*/
                email: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_email_is_required');?>.'}}}/*Email is required*/
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
                url: "<?php echo site_url('Crm/save_campaign_attendees'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        campaign_attendees_close();
                        fetch_campaign_attendees_detail_table();
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

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
        if(('<?php echo $admin['isSuperAdmin'] ?? 0?>' != 1) && ('<?php echo $isgroupadmin['adminYN'] ?? 0?>' != 1))
        {
            $('#isPermissionEveryone').iCheck('check');

            $("#isPermissionEveryone").on("ifChanged", function () {
                //$("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionCreator").on("ifChanged", function () {
               // $("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
                //$("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionGroup").on("ifChanged", function () {
                //$("#employeesID").val(null).trigger("change");
                $("#show_groupPermission").removeClass('hide');
                $("#show_multiplePermission").addClass('hide');
            });

            $("#isPermissionMultiple").on("ifChanged", function () {
              //  $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
                $("#show_multiplePermission").removeClass('hide');
            });
        }else
        {
            $('#isPermissionEveryone').iCheck('check');

            $("#isPermissionEveryone").on("ifChanged", function () {
                // $("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
                $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionCreator").on("ifChanged", function () {
                // $("#employeesID").val(null).trigger("change");
                $("#show_multiplePermission").addClass('hide');
                $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
            });

            $("#isPermissionGroup").on("ifChanged", function () {
                //$("#employeesID").val(null).trigger("change");
                $("#show_groupPermission").removeClass('hide');
                $("#show_multiplePermission").addClass('hide');
            });

            $("#isPermissionMultiple").on("ifChanged", function () {
                $("#groupID").val('');
                $("#show_groupPermission").addClass('hide');
                $("#show_multiplePermission").removeClass('hide');
            });
        }

    });

    function relatedChange() {
        initializeContactTypeahead();
        $('#related_search').val('');
    }

    function fetch_assignees() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': campaignID},
            url: "<?php echo site_url('Crm/fetch_campaign_employee_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    var selectedItems = [];
                    $.each(data, function (key, value) {
                        selectedItems.push(value['empID']);
                        $('#employeesID').val(selectedItems).change();
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_campaign_attendees_detail_table() {
        if (campaignID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'campaignID': campaignID},
                url: "<?php echo site_url('Crm/fetch_campaign_attendees_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.attendeecheck').iCheck('destroy');
                    $('.isLeadChecked').iCheck('destroy');
                    $('#attendees_table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#attendees_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            var markedby = '';
                            var isLeadConverted = '';
                            if (value['Ename2'] != null) {
                                markedby = value['Ename2'];
                            }
                            if (value['convertedToLead'] != 0) {
                                isLeadConverted = 'disabled';
                            }
                            $('#attendees_table_body').append('<tr><td><div class="skin skin-square"><div class="skin-section extraColumns"><input name="isLead_' + value['attendeesID'] + '" id="isLead_' + value['attendeesID'] + '" type="checkbox" data-caption="" class="columnSelected isLeadChecked" ' + isLeadConverted + ' value="' + value['attendeesID'] + '"><label for="checkbox">&nbsp;</label></div></div></div></td><td>' + x + '</td><td><div class="contact-box"><img class="align-left" src="<?php echo base_url("images/crm/icon-list-contact.png") ?>" alt="" width="32" height="32"><div class="link-box"><strong class="title"><a class="link-person noselect" href="#">' + value['fullname'] + ' </a><br><a class="link-person noselect" href="#">' + value['email'] + ' </a></strong></div></div></td><td class="text-center">' + value['organization'] + '</td><td class="text-center">' + value['phoneMobile'] + '</td><td><div class="col-sm-1"><div class="skin skin-square"><div class="skin-section extraColumns"><input id="isAttended_' + value['attendeesID'] + '" type="checkbox" data-caption="" class="columnSelected attendeecheck" name="isActive" value="' + value['attendeesID'] + '"><label for="checkbox">&nbsp;</label></div></div></div><div class="col-sm-8">Marked By :' + markedby + '</div></td><td class="text-right"><a onclick="load_campaign_attendees_header(' + value['attendeesID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_campaign_attendees_detail(' + value['attendeesID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            if (value['isAttended'] == 1) {
                                $('#isAttended_' + value['attendeesID']).iCheck('check');
                            }
                            if (value['convertedToLead'] == 1) {
                                $('#isLead_' + value['attendeesID']).iCheck('check');
                            }
                            x++;
                        });
                        $('.extraColumns input').iCheck({
                            checkboxClass: 'icheckbox_square_relative-blue',
                            radioClass: 'iradio_square_relative-blue',
                            increaseArea: '20%'
                        });
                    }

                    $('.attendeecheck').on('ifChanged', function (event) {
                        if ($(this).is(':checked')) {
                            var value = $(this).val();
                            checkAttendeesMark(value, 1);
                        } else {
                            var value = $(this).val();
                            checkAttendeesMark(value, 0);
                        }
                    });

                    $('.isLeadChecked').on('ifChanged', function (event) {
                        ItemsSelectedSync(this.value);
                    });

                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function load_campaign_header() {
        if (campaignID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'campaignID': campaignID},
                url: "<?php echo site_url('Crm/load_campaign_header'); ?>",
                beforeSend: function () {
                    startLoad();
                    campaign_attendees_close();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('#campaignID_edit').val(campaignID);
                        $('#campaignID_attendees_edit').val(campaignID);
                        $('#campaign_documentAutoID').val(campaignID);
                        $('#campaign_attendeesExcel_id').val(campaignID);
                        $('#campaign_name').val(data['header']['name']);
                        $('#startdate').val(data['header']['startDate']);
                        $('#end_date').val(data['header']['endDate']);
                        $('#objective').val(data['header']['objective']);
                        $('#typeID').val(data['header']['type']).change();
                        $('#statusID').val(data['header']['status']).change();
                      /*  $("#description").wysihtml5();
                        $('#description').val(data['header']['description']);*/
                        /*tinymce.get("description").getBody().innerHTML = data['header']['description'];
                        $('#description').html(data['header']['description']);
                        tinyMCE.activeEditor.setContent(data['header']['description']);*/
                        setTimeout(function () {
                            tinyMCE.get("description").setContent(data['header']['description']);
                        }, 300);
                        $('#campaign_assignDiv').removeClass('hide');
                        if (data['header']['isClosed'] == 1) {
                            $('#isClosed').iCheck('check');
                        }
                        fetch_assignees();
                        fetch_campaign_attendees_detail_table();
                        check_isEmail_campaign();
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
                                $('#employees_permission').val(selectedItems).change();
                            }
                        });
                    }

                    if (!jQuery.isEmptyObject(data['assignpermission'])) {
                        if((data['assignpermission'] == 1) && ('<?php echo $admin['isSuperAdmin'] ?? 0?>' != 1) && ('<?php echo $isgroupadmin['adminYN'] ?? 0?>' != 1)&& (data['header']['createdUserID'] != '<?php echo $current_userid?>'))
                        {
                            $('#campaign_name').prop('readOnly', true);
                            $('#objective').prop('readOnly', true);
                            $('#typeID').prop("disabled", true);
                            $("#employeesID").prop("disabled", true);
                            $("#employees_permission").prop("disabled", true);
                            if (!jQuery.isEmptyObject(data['permission'])) {
                                var selectedItems = [];
                                $.each(data['permission'], function (key, value) {
                                    if (value.permissionID == 1) {
                                        $('#isPermissionEveryone').iCheck('check');
                                        $('#isPermissionEveryone').iCheck('disable');
                                        $('#isPermissionCreator').iCheck('disable');
                                        $('#isPermissionGroup').iCheck('disable');
                                        $('#isPermissionMultiple').iCheck('disable');
                                        $("#show_groupPermission").addClass('hide');
                                        $("#show_multiplePermission").addClass('hide');
                                    } else if (value.permissionID == 2) {
                                        $('#isPermissionCreator').iCheck('check');
                                        $('#isPermissionEveryone').iCheck('disable');
                                        $('#isPermissionCreator').iCheck('disable');
                                        $('#isPermissionGroup').iCheck('disable');
                                        $('#isPermissionMultiple').iCheck('disable');
                                        $("#show_groupPermission").addClass('hide');
                                        $("#show_multiplePermission").addClass('hide');
                                    } else if (value.permissionID == 3) {
                                        $('#isPermissionGroup').iCheck('check');
                                        setTimeout(function () {
                                            $('#groupID').val(value.permissionValue);
                                        }, 600)

                                        $('#isPermissionEveryone').iCheck('disable');
                                        $('#isPermissionCreator').iCheck('disable');
                                        $('#isPermissionGroup').iCheck('disable');
                                        $('#isPermissionMultiple').iCheck('disable');
                                        $("#show_groupPermission").removeClass('hide');
                                        $("#show_multiplePermission").addClass('hide');
                                    } else if (value.permissionID == 4) {
                                        $('#isPermissionMultiple').iCheck('check');
                                        selectedItems.push(value.empID);
                                        $('#employees_permission').val(selectedItems).change();
                                        $('#isPermissionEveryone').iCheck('disable');
                                        $('#isPermissionCreator').iCheck('disable');
                                        $('#isPermissionGroup').iCheck('disable');
                                        $('#isPermissionMultiple').iCheck('disable');
                                    }
                                });
                            }

                        }

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

    function load_campaign_attendees_header(attendeesID) {
        if (attendeesID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'attendeesID': attendeesID},
                url: "<?php echo site_url('Crm/load_campaign_attendees_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        campaign_attendees_table();
                        $('#attendeesID_edit').val(data['attendeesID']);
                        $('#prefix').val(data['prefix']);
                        $('#firstName').val(data['firstName']);
                        $('#lastName').val(data['lastName']);
                        $('#occupation').val(data['occupation']);
                        $('#organization').val(data['organization']);
                        $('#email').val(data['email']);
                        $('#phoneMobile').val(data['phoneMobile']);
                        $('#phoneHome').val(data['phoneHome']);
                        $('#fax').val(data['fax']);
                        $('#countryID').val(data['countryID']).change();
                        $('#address').val(data['address']);
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

    function delete_campaign_detail(id) {
        if (campaignID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('crm_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'AssingeeID': id},
                        url: "<?php echo site_url('Crm/delete_campaign_detail'); ?>",
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
        ;
    }

    function delete_campaign_attendees_detail(id) {
        if (campaignID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('crm_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attendeesID': id},
                        url: "<?php echo site_url('Crm/delete_campaign_attendees_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_campaign_attendees_detail_table();
                            }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function campaign_attendees_table() {
        $('#campaign_attendees_table_show').addClass('hide');
        $('#attendees_form_show').removeClass('hide');
        $('#attendeesID_edit').val('');
        $("#countryID").val(null).trigger("change");
        $('#campaign_attendees_form')[0].reset();
        $('#campaign_attendees_form').bootstrapValidator('resetForm', true);
        $("#prefix").prop("readonly", false);
        $("#firstName").prop("readonly", false);
        $("#lastName").prop("readonly", false);
        $("#occupation").prop("readonly", false);
        /*        var x = document.getElementById('attendees_form_show');
         if (x.style.display === 'none') {
         x.style.display = 'block';
         } else {
         x.style.display = 'none';
         }*/
        $(document).scrollTop(0);
    }

    function campaign_attendees_close() {
        $('#campaign_attendees_table_show').removeClass('hide');
        $('#attendees_form_show').addClass('hide');
        $(document).scrollTop(0);
    }

    function checkAttendeesMark(id, value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'attendeesID': id, value: value},
            url: "<?php echo site_url('Crm/campaign_attendess_marked'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#statusModal').modal('hide');
                    fetch_campaign_attendees_detail_table();
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    function email_campaign() {
        var campaignID = $('#campaign_documentAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {campaignID: campaignID},
            url: "<?php echo site_url('crm/load_email_campaign_tab'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#emailCampaign_body').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function check_isEmail_campaign() {
        var categoryID = $('#typeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'categoryID': categoryID},
            url: "<?php echo site_url('Crm/check_isEmail_campaign_type'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if (data['isdefault'] == 1) {
                        $('#emailCampaignTab').removeClass('hide');
                    } else {
                        $('#emailCampaignTab').addClass('hide');
                    }
                }
                stopLoad();
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function campaign_multiple_attachemts() {
        var campaignID = $('#campaign_documentAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {campaignID: campaignID},
            url: "<?php echo site_url('crm/load_campaign_multiple_attachemts'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#campaign_multiple_attachemts').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_campaign_button() {
        $('#add_attachemnt_show').removeClass('hide');
    }

    function document_uplode() {
        var formData = new FormData($("#campaign_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('crm/attachement_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#campaignattachmentDescription').val('');
                    campaign_multiple_attachemts();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function attendees_excel_uplode() {
        var formData = new FormData($("#campaign_attendeesExcel_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('crm/attendees_excel_uplode'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#attendees_Excel_upload_Modal').modal('hide');
                    fetch_campaign_attendees_detail_table();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function delete_crm_attachment(id, fileName) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('crm_you_want_to_delete_a');?>",/*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName},
                    url: "<?php echo site_url('crm/delete_crm_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            campaign_multiple_attachemts();
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function initializeContactTypeahead() {
        $('#contactname').autocomplete({
            serviceUrl: '<?php echo site_url();?>Crm/fetch_contact_relate_search/',
            onSelect: function (suggestion) {
                $('#contactID').val(suggestion.contactID);
                $('#prefix').val(suggestion.prefix);
                $('#firstName').val(suggestion.firstName);
                $('#lastName').val(suggestion.lastName);
                $('#occupation').val(suggestion.title);
                $('#phoneMobile').val(suggestion.phoneMobile);
                $('#email').val(suggestion.email);
                $('#phoneHome').val(suggestion.phoneHome);
                $('#fax').val(suggestion.fax);
                $('#postalcode').val(suggestion.postalCode);
                $('#state').val(suggestion.state);
                $('#city').val(suggestion.city);
                $('#address').val(suggestion.address);
                $('#countryID').val(suggestion.countryID).change();
                $("#prefix").prop("readonly", true);
                $("#firstName").prop("readonly", true);
                $("#lastName").prop("readonly", true);
                $("#occupation").prop("readonly", true);
            }
        });
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

    function send_email(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('crm_you_want_to_send_email');?>",/*You want to Send Email !*/
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#00a65a",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'campaignID': id},
                    url: "<?php echo site_url('crm/send_email_campaign'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {

                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function uploadExcelAttendees_model() {
        $('#campaign_attendeesExcel_uplode_form')[0].reset();
        $('#attendees_Excel_upload_Modal').modal({backdrop: "static"});
    }

    function ItemsSelectedSync(item) {
        if ($('input[name=isLead_'+item+']').is(':checked')){
            var inArray = $.inArray(item, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(item);
            }
        } else {
            selectedItemsSync.splice( $.inArray(item, selectedItemsSync), 1 );
        }
    }

    function addNew_lead() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("crm/convert_attendees_to_lead"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    selectedItemsSync = [];
                    fetch_campaign_attendees_detail_table();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function employeeassigntype(campaignID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'campaignID': campaignID},
            url: "<?php echo site_url('Crm/load_campaign_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission'])) {
                    if((data['assignpermission'] == 1) && ('<?php echo $admin['isSuperAdmin'] ?? 0?>' != 1) && ('<?php echo $isgroupadmin['adminYN'] ?? 0?>' != 1) && (data['header']['createdUserID'] != '<?php echo $current_userid?>'))
                    {
                        $('#campaign_name').prop('readOnly', true);
                        $('#objective').prop('readOnly', true);
                        $('#typeID').prop("disabled", true);
                        $("#employeesID").prop("disabled", true);
                        $("#employees_permission").prop("disabled", true);
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 1) {
                                    $('#isPermissionEveryone').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").addClass('hide');
                                } else if (value.permissionID == 2) {
                                    $('#isPermissionCreator').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").addClass('hide');
                                } else if (value.permissionID == 3) {
                                    $('#isPermissionGroup').iCheck('check');
                                    setTimeout(function () {
                                        $('#groupID').val(value.permissionValue);
                                    }, 600)
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                } else if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#employees_permission').val(selectedItems).change();
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").addClass('hide');
                                    $("#show_multiplePermission").removeClass('hide');
                                }
                            });
                        }
                    }
                    else
                    {
                        $('#campaign_name').prop('readOnly', false);
                        $('#objective').prop('readOnly', false);
                        $('#typeID').prop("disabled", false);
                        $("#employeesID").prop("disabled", false);
                        $("#employees_permission").prop("disabled", false);
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
                                    $('#employees_permission').val(selectedItems).change();
                                }
                            });
                        }
                    }

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
    function statuscheack(statusid)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'statusid':statusid},
            url: "<?php echo site_url('Crm/crm_campaigns'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
               if(data['isexist'] == 1)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to close this task!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }else
                {
                    $('.closedatehideshow').addClass('hide');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>