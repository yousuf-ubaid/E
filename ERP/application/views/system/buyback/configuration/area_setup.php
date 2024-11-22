<?php echo head_page('Buyback Area Setup', false);
$usergroupcompanywiseallow = getPolicyValuesgroup('BBL','All');
?>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <?php if($usergroupcompanywiseallow == 0){?>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="createAreaCompany()"><i
                    class="fa fa-plus"></i> Create Main Area
            </button>
        <?php } else if ($usergroupcompanywiseallow != 0) { ?>
            <button type="button" class="btn btn-primary "
                    onclick="create_buybackArea()"><i
                    class="fa fa-plus"></i> Create Main Area
            </button>
        <?php }?>
    </div>
</div>
<br>
<div id="Buyback_area_setupView"></div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade bs-example-modal-lg" id="add_subarea_modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Create New Sub Area</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="">
                    <input type="text" name="locationID" id="locationID" class="form-control hidden">
                    <input type="text" name="masterID" id="masterID" class="form-control hidden">
                    <div class="form-group">
                        <label for="fuelType" class="col-sm-3 control-label"> Sub Area : </label>
                        <div class="col-sm-7">
                            <input type="text" name="subArea" id="subArea" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Create_new_subarea()">
                    <?php echo $this->lang->line('common_save'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="add_area_modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Create New Area</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm_CreateNewCage">
                    <input type="text" name="locationID" id="locationID" class="form-control hidden">
                    <div class="form-group">
                        <label for="fuelType" class="col-sm-3 control-label"> Area Description </label>
                        <div class="col-sm-7">
                            <input type="text" name="mainArea" id="mainArea" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Create_new_area()">
                    <?php echo $this->lang->line('common_save'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/buyback/configuration/area_setup', '', 'Buyback Area Setup');
        });

        buyback_area_table();

    });

    function buyback_area_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            // data: data,
            url: "<?php echo site_url('Buyback/buyback_area_table_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Buyback_area_setupView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function create_sub_area(locationID){
        $('#locationID').val('');
        $('#masterID').val('');
        $('#masterID').val(locationID);
        $('#subArea').val('');
        $('#add_subarea_modal').modal('show');
    }
    function Create_new_subarea() {
        var locationID = $('#locationID').val();
        var masterID = $('#masterID').val();
        var subArea = $('#subArea').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'subArea' : subArea, 'masterID' : masterID, 'locationID': locationID},
            url: "<?php echo site_url('Buyback/create_new_subarea'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#add_subarea_modal').modal('hide');
                    buyback_area_table();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_main_area(locationID, mainArea) {
        $('#locationID').val(locationID);
        $('#mainArea').val(mainArea);
        $('#add_area_modal').modal('show');
    }
    function Create_new_area(){
        var locationID = $('#locationID').val();
        var area = $('#mainArea').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'area' : area, 'locationID': locationID},
            url: "<?php echo site_url('Buyback/create_new_area'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#add_area_modal').modal('hide');
                    buyback_area_table();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function create_buybackArea() {
        $('#mainArea').val('');
        $('#locationID').val('');
        $('#add_area_modal').modal('show');
    }

    function edit_sub_area(locationID, masterID, subArea){
        $('#locationID').val(locationID);
        $('#masterID').val(masterID);
        $('#subArea').val(subArea);
        $('#add_subarea_modal').modal('show');
    }
    function createAreaCompany() {
        swal(" ", "You do not have permission for Area Setup at company level,please contact your system administrator.", "error");
    }
</script>