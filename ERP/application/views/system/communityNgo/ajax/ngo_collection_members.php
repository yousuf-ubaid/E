<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('community_ngo_helper');

$financeyear_arr = all_financeyear_drop(true);

?>
    <style>
        .title {
            float: left;
            text-align: left;
            font-size: 13px;
            color: #7b7676;
        }

    </style>

    <input type="hidden" name="Collection_DetailID" value="<?php echo $CollectionDetailID; ?>">
    <input type="hidden" name="CollectionMasterID" value="<?php echo $CollectionMasterID; ?>">
    <input type="hidden" name="CollectionAmount" value="<?php echo $CollectionAmount; ?>">
    <input type="hidden" name="CollectionType" value="<?php echo $CollectionType; ?>">

    <div id="AddMemberDiv">
        <div class="animated zoomIn ">

            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Details</legend>
                <div class="col-sm-2" style="">
                    <label class="control-label title"
                           for="companyFinanceYearID"><?php echo $this->lang->line('communityngo_collection_Financial_Year'); ?></label>
                    <?php echo form_dropdown('companyFinanceYearID', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control select2" id="companyFinanceYearID" disabled '); ?>
                </div>

                <div class="col-sm-2 filterDivOccType">
                    <label class="control-label title" for="OccTypeID"><?php echo $this->lang->line('communityngo_occupationType'); ?></label>
                    <?php echo form_dropdown('OccTypeID[]', fetch_occupationType_drop(), '', 'multiple  class="form-control OccTypeID" onchange="get_occupationType(\'ngoOccTypeID\')" id="OccTypeID" required'); ?>
                </div>
                <div class="col-sm-2 filterDivAge">
                    <label class="control-label text-left title"
                           for="age"><?php echo $this->lang->line('communityngo_above_age'); ?></label>
                    <input type="number" name="age" id="age" value="" class="form-control" onkeyup="get_age('ngoAge')">
                </div>
                <div class="col-sm-2 filterFamMaster">
                    <label class="control-label title" for="FamMasterID"><?php echo $this->lang->line('CommunityNgo_leader'); ?></label>
                    <?php echo form_dropdown('FamMasterID[]', fetch_familyMaster(false), '', 'multiple  class="form-control FamMasterID" onchange="get_familyMaster(\'ngoFamMasterID\')" id="FamMasterID" required'); ?>

                </div>
                <div class="form-group col-sm-1" style="margin-bottom: 0px;">
                    <!--<button type="button" class="btn btn-primary pull-left" onclick="get_member_for_collection()"
                            name="filterbtn" id="filterbtn"><i class="fa fa-search"></i>
                    </button>-->
                </div>

            </fieldset>

        </div>

        <div class="animated zoomIn">
            <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border">Members</legend>
                <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                    <div class="col-sm-5" id="MemberDiv">
                        <select name="MemberID[]" id="search" class="form-control" size="8" multiple="multiple">
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                        ><i class="fa fa-forward"></i></button>
                        <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                class="fa fa-chevron-right"></i></button>
                        <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                class="fa fa-chevron-left"></i></button>
                        <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                class="fa fa-backward"></i></button>
                    </div>
                    <div class="col-sm-5">
                        <select name="Com_MasterID[]" id="search_to" class="form-control" size="8"
                                multiple="multiple">
                            <?php
                            /* $members = all_collection_member_drop();
                             unset($members[""]);
                             if (!empty($members)) {
                                 foreach ($members as $key => $val) {
                                     echo '<option value="' . $key . '">' . $val . '</option>';
                                 }
                             }*/
                            ?>
                        </select>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="animated zoomIn" id="AddMemberAmountDiv" style="padding-top: 1%; display: none;">
        <div id="memberAmount_view"></div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-top: 10px">

            <button type="button" class="btn btn-primary pull-right submitBtn"
                    onclick="save_collection_members()" name="filtersubmit" style="margin-left: 1%;"
                    id="filtersubmit"><i
                    class="fa fa-plus"></i> Create Invoice
            </button>

            <button class="btn btn-primary nxtBtn pull-right" type="button" id="footerNxt_DT"
                    onclick="display_member_amounts_form()"><i class="fa fa-share"></i>
                <?php echo $this->lang->line('common_next'); ?><!--Next--></button>

            <button class="btn btn-danger backBtn pull-right" type="button"
                    id="footerBack_DT" onclick="display_backHm()"><i class="fa fa-reply"></i>
                <?php echo $this->lang->line('communityngo_back'); ?><!--Back--> </button>


        </div>
    </div>

    <script>
        $(document).ready(function () {

            var colType = '<?php echo $CollectionType; ?>';
            if (colType == 1) {
                $('.filterDivOccType').removeClass('hide');
                $('.filterDivAge').removeClass('hide');
                $('.filterFamMaster').removeClass('hide');
            } else {
                $('.filterDivOccType').addClass('hide');
                $('.filterDivAge').addClass('hide');
                $('.filterFamMaster').addClass('hide');
            }

            var ngoOccTypeID = localStorage.ngoOccTypeID;
            if (ngoOccTypeID != undefined) {
                if (ngoOccTypeID != 'null') {
                    $('#OccTypeID').val(localStorage.ngoOccTypeID).change();
                }
            }

            var ngoAge = localStorage.ngoAge;
            if (ngoAge != undefined) {
                if (ngoAge != 'null') {
                    $('#age').val(localStorage.ngoAge);
                }
            }

            var ngoFamMasterID = localStorage.ngoFamMasterID;
            if (ngoFamMasterID != undefined) {
                if (ngoFamMasterID != 'null') {
                    $('#FamMasterID').val(localStorage.ngoFamMasterID).change();
                }
            }

            $('#search').multiselect({
                search: {
                    left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                    right: '<input type="text" name="q" class="form-control" placeholder="Search..." />'
                },
                afterMoveToLeft: function ($left, $right, $options) {
                    $("#search_to option").prop("selected", "selected");
                }
            });

            $("#search_to option").prop("selected", "selected");

            $('#OccTypeID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#OccTypeID").multiselect2('selectAll', true);
            $("#OccTypeID").multiselect2('updateButtonText');

            $('#FamMasterID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#FamMasterID").multiselect2('selectAll', true);
            $("#FamMasterID").multiselect2('updateButtonText');

            get_member_for_collection();
        });


        function get_occupationType(OccTypeID) {

            if (OccTypeID != undefined) {
                window.localStorage.setItem(OccTypeID, $('#OccTypeID').val());
            } else {
            }

            /*if (OccTypeID != undefined) {

             var TypeIDs = $('#OccTypeID').val();
             var types = [];
             types.push(TypeIDs);

             window.localStorage.setItem(OccTypeID, JSON.stringify(types));
             } else {
             }*/
            get_member_for_collection();
        }

        function get_familyMaster(FamMasterID) {

            if (FamMasterID != undefined) {
                window.localStorage.setItem(FamMasterID, $('#FamMasterID').val());
            } else {
            }

            /*if (FamMasterID != undefined) {

             var TypeIDs = $('#FamMasterID').val();
             var types = [];
             types.push(TypeIDs);

             window.localStorage.setItem(FamMasterID, JSON.stringify(types));
             } else {
             }*/
            get_member_for_collection();
        }


        function get_age(Age) {
            if (Age != undefined) {
                window.localStorage.setItem(Age, $('#age').val());
            } else {
            }
            get_member_for_collection();
        }

        function get_member_for_collection() {
            var data = $('#collection_member_form').serializeArray();

            data.push({'name': 'CollectionType', 'value': CollectionType});
            data.push({'name': 'CollectionMasterID', 'value': CollectionMasterID});

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_member_for_collection'); ?>",
                data: data,
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#search').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function save_collection_members() {

            var data = $('#collection_member_form').serializeArray();

            // data.push({'name': 'Collection_MasterID', 'value': CollectionMasterID});
            //  data.push({'name': 'Collection_DetailID', 'value': Collection_DetailID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_collection_members'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    refreshNotifications(true);
                    stopLoad();
                    myAlert(data[0], data[1]);

                  //  var mem_search = $('#search');
                    // if (data[0] == 's') {

                    //  mem_search.append('<option value="' + data[2] + '"></option>');
                    //  mem_search.val(data[2]);

//                    $('#collection_member_form')[0].reset();

                    display_backHm();
                    //$('#search_to').val('').change();
                    $('#search_to').empty();

                    var Collection_DetailID = $('#Collection_DetailID').val();
                    get_collection_member_details(Collection_DetailID);
                    get_member_for_collection();


                    //  }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function display_member_amounts_form() {

            document.getElementById('AddMemberDiv').style.display = 'none';
            document.getElementById('AddMemberAmountDiv').style.display = 'block';

            $.ajax({

                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_selected_members'); ?>",
                dataType: 'html',
                data: $('#collection_member_form').serializeArray(),
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    refreshNotifications(true);
                    stopLoad();
                    $('#memberAmount_view').html(data);

                    $('.submitBtn').removeClass('hide');
                    $('.backBtn').removeClass('hide');
                    $('.nxtBtn').addClass('hide');

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                    refreshNotifications(true);
                }
            });
        }

        function display_backHm() {

            document.getElementById('AddMemberDiv').style.display = 'block';
            document.getElementById('AddMemberAmountDiv').style.display = 'none';

            $('.submitBtn').addClass('hide');
            $('.backBtn').addClass('hide');
            $('.nxtBtn').removeClass('hide');
        }

    </script>
<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 2/26/2018
 * Time: 2:55 PM
 */