<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_document_setup');
$financeyear_arr = all_financeyear_drop(true);
$financeyear_arr_location = all_financeyear_drop_location(true);
$locationdrop_arr = all_company_location_code_drop();
$emplocationID = $this->common_data['emplanglocationid'];

$locationdefault = '';
$locationwisecodegenerate = getPolicyValues('LDG', 'All');
echo head_page($title, false);

/*echo head_page('Document Setup', FALSE);*/
?>
<style>

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

  <a class="btn btn-primary btn-wizard " href="#step1" data-toggle="tab"><?php echo $this->lang->line('config_standard_document_code');?><!--Standard Document Code--></a>
  <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab"><?php echo $this->lang->line('config_financial_document_code');?><!--Financial Document Code--></a>
    <?php if($locationwisecodegenerate == 1) {?>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_companyCode_location()" data-toggle="tab">Location Wise Document Code</a>
    <?php }?>

</div>
<hr>
<div class="tab-content">
  <div id="step1" class="tab-pane active">
      <?php echo form_open('', 'role="form" id="companycodeform"'); ?>
      <div id="companyCode_body" style="height:500px;"></div>
      <hr>
      <div class="text-right m-t-xs">
          <button type="button" onclick="save_companycode_form()" class="btn btn-primary"><?php echo $this->lang->line('common_update');?><!--Update--></button>
      </div>
      </form>
  </div>
  <div id="step2" class="tab-pane">
      <div class="form-group col-sm-4">
          <label
              for="financeyear"><?php echo $this->lang->line('common_financial_year');?></label>
          <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" onchange="add_missing_document_code(this)" id="financeyear" onchange="load_companyCode_fin()"'); ?>
      </div>
      <?php echo form_open('', 'role="form" id="financeyeardocSetup"'); ?>
      <div id="companyCode_fin_body"></div>
      <hr>
      <div class="text-right m-t-xs">
          <button type="button" onclick="financeyeardocSetup()" class="btn btn-primary"><?php echo $this->lang->line('common_update');?><!--Update--></button>
      </div>
      </form>
  </div>
    <div id="step3"  class="tab-pane">
        <div class="form-group col-sm-4">
            <label for="financeyear">Localtion</label>

            <?php
            $currentcompany = current_companyID();

            $location = $this->db->query("SELECT locationID FROM `srp_erp_location` where  isCostLocation = 1 AND companyID = $currentcompany LIMIT 1")->row_array();
            $locationdefault = $location['locationID'];
            ?>
            <?php echo form_dropdown('location', $locationdrop_arr, $locationdefault, 'class="form-control select2 location"  id="location"'); ?>
        </div>

        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode">Financial Year</label><br>
            <div style="width: 60%;">
                <?php echo form_dropdown('financeyearlocation', $financeyear_arr_location, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control"  id="financeyearlocation"'); ?>
            </div>
            <button type="button" class="btn btn-primary pull-right"  onclick="add_missing_document_code_location()"  style="margin-top: -9%;"><?php echo $this->lang->line('common_load');?><!--Load-->
            </button>

        </div>


        <?php echo form_open('', 'role="form" id="locationdocsetup"'); ?>
        <div id="companyCode_location_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button type="button" onclick="locationdocSetup()" class="btn btn-primary"><?php echo $this->lang->line('common_update');?><!--Update--></button>
        </div>
        </form>
    </div>
</div>


<?php
echo footer_page('Right foot', 'Left foot', FALSE); ?>
<script type="text/javascript">
    var companyid =<?php echo json_encode(trim($this->common_data['company_data']['company_id'])); ?>;
    // load_companyCode_table();

    $(document).ready(function () {
        $('.select2').select2();
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
        load_companyCode();
        load_companyCode_fin();
        load_companyCode_location();
    });


    function load_companyCode_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Company/fetch_company_codeMaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#companyCode_body').html(data);
                stopLoad();
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
    function financeyeardocSetup(){
        var data = $("#financeyeardocSetup").serialize()+ "&financeTable=TRUE";
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Company/update_company_codes_prefixChange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_companycode_form() {
        var data = $("#companycodeform").serialize();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Company/update_company_codes_prefixChange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_companyCode();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_companyCode() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Company/fetch_company_codeMaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#companyCode_body').html(data);
                stopLoad();
                $('#standedtbl').tableHeadFixer({
                    head: true,
                    foot: true,
                    left: 0,
                    right: 0,
                    'z-index': 0
                });
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }

    function load_companyCode_fin() {
        var financeyear=$('#financeyear').val();
        if(financeyear>0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'financeyear': financeyear
                },
                url: "<?php echo site_url('Company/fetch_company_codeMaster_fin'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#companyCode_fin_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }else{
            myAlert('w','Select finance Year')
        }

    }

    function load_companyCode_location() {
        var location =$('#location').val();
        var financeyearlocation = $('#financeyearlocation').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'location':location,'financeyearlocation':financeyearlocation},
                url: "<?php echo site_url('Company/fetch_comapny_code_location'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#companyCode_location_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });

    }

    function update_Serialization(codeID){
        var isFYBasedSerialNo = $('#isFYBasedSerialNo_'+codeID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'codeID': codeID,
                'isFYBasedSerialNo': isFYBasedSerialNo
            },
            url: "<?php echo site_url('Company/update_Serialization'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_companyCode();
                }
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function add_missing_document_code(val){
       var financeyearID=$(val).val();
        if(financeyearID==''){

        }else{
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'financeyearID': financeyearID
                },
                url: "<?php echo site_url('Company/add_missing_document_code'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    load_companyCode_fin();
                    if (data[0] == 's') {
                        myAlert(data[0], data[1]);
                    }
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }

    }
    function add_missing_document_code_location(val){
        var location =$('#location').val();
        var financeyearlocation =$('#financeyearlocation').val();

        if(location)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'location': location,'financeyearlocation':financeyearlocation
                },
                url: "<?php echo site_url('Company/add_missing_document_code_location'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    load_companyCode_location();
                    if (data[0] == 's') {
                        myAlert(data[0], data[1]);
                        load_companyCode_location();
                    }
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }



    }

    function locationdocSetup(){
        var data = $("#locationdocsetup").serialize()+ "&locationTable=TRUE";
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Company/update_company_location_codes_prefixChange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function update_document_finaceyear(finaceyear) {
        var finaceyear =$(finaceyear).val();
        var location =$('#location').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'finaceyear': finaceyear,'location':location},
                url: "<?php echo site_url('Company/document_code_location_finacebasedup'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        myAlert(data[0], data[1]);
                        load_companyCode_location();
                    }
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

    }

    function update_postDate(codeID){
        var postDate = $('#postDate_'+codeID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'codeID': codeID,
                'postDate': postDate
            },
            url: "<?php echo site_url('Company/update_postDate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_companyCode();
                }
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }
</script>



