<?php
/*$main_category_arr = all_main_category_drop();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)]);*/
$crewID = isset($page_id) && !empty($page_id) ? $page_id : 0;

?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_manage_crew');
echo head_page($title, false);
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>


<form method="post" id="from_add_edit_crew">
    <input type="hidden" value="" id="crewID" name="crewID"/>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_crew_detail');?><!--Crew Detail--> </h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_name');?><!--Name--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="Ename1" id="Ename1" class="form-control" placeholder="<?php echo $this->lang->line('common_name');?>"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_gender');?><!--Gender--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <select name="Gender" id="Gender" class="form-control" required>
                        <option value=""><?php echo $this->lang->line('common_select');?><!--Select--></option>
                        <option value="1"><?php echo $this->lang->line('common_male');?><!--Male--></option>
                        <option value="2"><?php echo $this->lang->line('common_female');?><!--Female--></option>
                    </select>
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>


            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_designation');?><!--Designation--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="designation" id="designation" class="form-control"
                           placeholder="<?php echo $this->lang->line('common_designation');?>">
                    <!--<span class="input-req-inner"></span>-->
                </span>

                </div>
            </div>
        </div>


        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_contact_detail');?><!--Contact Detail--> </h2>
            </header>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_email');?><!--Email--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="email" name="EEmail" id="EEmail" class="form-control" placeholder="@<?php echo $this->lang->line('common_designation');?>"
                           required>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_telephone');?><!--Telephone--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="EpTelephone" id="EpTelephone" class="form-control" placeholder="<?php echo $this->lang->line('common_telephone');?>">
                    <!--<span class="input-req-inner"></span>-->
                </span>

                </div>

            </div>


        </div>
    </div>

    <div class="col-md-12 animated zoomIn">
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-7">
                <div class="pull-right">
                    <button class="btn btn-primary" type="submit" id="submitCrewBtn"><i class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_add_crew');?><!--Add Crew-->
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_crew', '', 'Crew')
        });

        $("#from_add_edit_crew").submit(function (e) {
            addEditCrew();
            return false;
        });
        loadCrewDetail();
    });


    function addEditCrew() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_CrewMaster/add_edit_crew"); ?>',
            dataType: 'json',
            data: $("#from_add_edit_crew").serialize(),
            async: false,
            success: function (data) {
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                else if (data['error'] == 0) {
                    if (data['code'] == 1) {
                        $("#from_add_edit_crew")[0].reset();
                    }
                    myAlert('s', data['message']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    function loadCrewDetail() {
        var crewID = '<?php echo $crewID ?>';
        if (crewID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_CrewMaster/loadCrewDetail"); ?>',
                dataType: 'json',
                data: {crewID: crewID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
                        $("#submitCrewBtn").html('<i class="fa fa-pencil"></i> Edit Crew');
                        $("#crewID").val(data['crewID']);
                        $("#Ename1").val(data['Ename1']);
                        $("#Gender").val(data['Gender']);
                        $("#designation").val(data['designation']);
                        $("#EEmail").val(data['EEmail']);
                        $("#EpTelephone").val(data['EpTelephone']);
                        $("#EpTelephone").val(data['EpTelephone']);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }


</script>