<?php echo head_page('Ware House Master', false);
//$company=current_companyID();
$location=load_location_drop();
$itemwarehouse=load_warehouse_items()
?>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-9"></div>
    <div class="col-md-3" style="margin-bottom: 15px; margin-top:15px;">
        <button type="button" onclick="reset_form()"  class="btn btn-xs btn-primary pull-right" data-toggle="modal"
                data-target="#warehouseitem_model"><span
                class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create
            New
        </button>
    </div>
</div>

<div class="table-responsive">
    <table id="warehouseitem_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 10%">Items ID</th>
            <th>Warehouse Location</th>
            <th>Item System Code</th>
            <th>Item Primary Code</th>
            <th>Item Description</th>
            <th>Unit Of Measure</th>
            <th>Stock Qty</th>
            <!--<th style="min-width: 5%">&nbsp;</th>-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="warehouseitem_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Warehouse Item</h4>
            </div>
            <form role="form" id="warehouseitem_form" class="form-group">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" class="form-control" id="warehouseitemedit" name="warehouseitemedit">
                        <div class="form-group col-sm-4">
                            <label for="">Warehouse Location</label>
                            <select name="warehouselocation" id="warehouselocation" class="form-control searchbox">
                                <option value="">Please Select</option>
                                <?php foreach ($location as $locat) { ?>
                                    <option value="<?php echo $locat['wareHouseSystemCode'] ?>_<?php echo $locat['wareHouseDescription'] ?>"><?php echo $locat['wareHouseDescription'] ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">


                        <table class="table-bordered">
                            <thead>

                            </thead>
                            <tbody>
                            <?php foreach ($itemwarehouse as $itemware) { ?>
                               <tr>
                                   <td style="width: 217px; padding-left: 15px;"><?php echo $itemware['itemDescriptionshort'] ?></td>
                                   <td><input id="itm<?php echo $itemware['itemCodeSystem'] ?>" name="itm[]"  type="checkbox" value="<?php echo $itemware['itemCodeSystem'] ?>"></td>
                               </tr>
                            <?php }; ?>
                            </tbody>
                        </table>



                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> </button>
                    <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">
    $( document ).ready(function() {

        warehouseitemview();

        $('#warehouseitem_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                warehouselocation: {validators: {notEmpty: {message: 'Warehouse Location is required.'}}},
                //itm: {validators: {notEmpty: {message: 'Select Item is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('srp_Warehouse_Item/save_warehouseitem'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        if(data){
                            $("#warehouseitem_model").modal("hide");
                            warehouseitemview();
                            //fetchPage('system/srp_mu_suppliermaster_view','Test','Supplier Master');
                        }
                    }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
                });
        });

    });

    function warehouseitemview() {
        var Otable = $('#warehouseitem_table').DataTable({
            "Processing": true,
            "ServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('srp_Warehouse_Item/load_warehouseitemtable'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "aoColumns": [
                {"mData": "warehouseItemsID"},
                {"mData": "wareHouseLocation"},
                {"mData": "itemSystemCode"},
                {"mData": "itemPrimaryCode"},
                {"mData": "itemDescription"},
                {"mData": "unitOfMeasure"},
                {"mData": "stockQty"},
                //{"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function reset_form() {

        $('#warehouseitem_form')[0].reset();

        return false;
    }

    $("#warehouseitem_model").on("hidden.bs.modal", function () {
        warehouseitemview();
    });

</script>