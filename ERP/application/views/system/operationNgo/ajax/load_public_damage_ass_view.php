

<?php
if (!empty($benificiaryArray)) {

    foreach ($benificiaryArray as $familyInfo) {
        ?>
        <div>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROPERTY ASSESSMENT</h2>
                    </header>
                    <input type="hidden" name="publicPropertyBeneID" id="edit_damageAssesment_beneficiary">

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Type of Damage</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo $familyInfo['TypeOfHouseDamage'] ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">House Type</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo $familyInfo['buildingtype'] ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Property Condition</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo $familyInfo['houseCondition'] ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Building Damages</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo $familyInfo['damagetype'] ?>

                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Estimated Cost for Repair</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo $familyInfo['da_estimatedRepairingCost'] ?>

                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Need assistance to repair?</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php if($familyInfo['da_needAssistancetoRepairYN']==1){  ?>
                                Yes
                            <?php } else{ ?>
                                No
                            <?php }  ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Total Paid Amount</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo $familyInfo['da_paidAmount'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>DAMAGE ASSESSMENT FOR PROPERTY ITEMS</h2>
                    </header>
                </div>
            </div>
            <!--        <div class="row">
                        <div class="col-md-12">
                            <button type="button" onclick="add_familyDetail_houseItem_model()" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i>&nbsp;Add
                            </button>
                        </div>
                    </div>-->
            <div class="row">
                <div class="col-md-12">
                    <div id="house_items_injuryAssessment_body"></div>
                </div>
            </div>
        </div>
        <hr>
    <?php }
} else {

    ?>
    <div id="familydetails" style="">
        <div class="alert alert-danger" role="alert">
            <span class="fa fa-exclamation-circle" aria-hidden="true"></span>
            <span class="sr-only">Not Found:</span>
            No Details Found!
        </div>
    </div>
    <?php exit;
}
?>


<script>

    function fetch_publicPropertyAssessment(publicPropertyBeneID) {
        //  var publicPropertyBeneID = $('#edit_beneficiary').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'publicPropertyBeneID': publicPropertyBeneID},
            url: '<?php echo site_url("OperationNgo/load_property_damage_pd_view"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#house_items_injuryAssessment_body').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>