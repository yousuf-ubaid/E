<?php
echo head_page('New Purchase Request',false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-4">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td>
                    <div class="radio radio-info radio-inline">
                        <input type="radio" id="r1" value="x" name="PRConfirmedYN" class="pr_Filter" checked="checked">
                        <label for="r1"> All </label>
                    </div>
                </td>
                <td>
                    <div class="radio radio-success radio-inline">
                        <input type="radio" id="r2" value="1" name="PRConfirmedYN" class="pr_Filter">
                        <label for="r2"> Confirmed</label>
                    </div>
                </td>
                <td>
                    <div class="radio radio-danger radio-inline">
                        <input type="radio" id="r3" value="0" name="PRConfirmedYN" class="pr_Filter">
                        <label for="r3"> Not Confirmed</label>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-5 text-center">
            <!-- <table class="<?php //echo table_class() ?>">
                <tr>
                    <td><span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span> Confirmed /
                        Approved
                    </td>
                    <td><span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span> Not Confirmed
                        / Not Approved
                    </td>
                    <td><span class="glyphicon glyphicon-stop" style="color:orange; font-size:15px;"></span> Refer-back
                    </td>
                </tr>
            </table> -->
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/procurement/erp_purchase_request_new',null,'Add Purchase Request','PR');"><i class="fa fa-plus"></i> New Purchase Request </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="purchase_request_table" class="<?php echo table_class()?>">
        <thead>
            <tr>
                <th style="min-width: 20%">Purchase Request Code</th>
                <th style="min-width: 30%">Comments</th>
                <th style="min-width: 10%">Location</th>
                <th style="min-width: 10%">Department</th>
                <th style="min-width: 15%">Service Line</th>
                <th style="min-width: 5%">Confirm</th>
                <th style="min-width: 5%">Approved</th>
                <th style="min-width: 5%">&nbsp;</th>
                <th >&nbsp;</th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>