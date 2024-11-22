
<?php
$umo_arr = all_umo_new_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fleet_Fuel_Master');
echo head_page($title, false);

?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary btn-sm pull-right"  onclick="NewFuelADD_model()"
                    style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?>
            </button>
        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table id="fuel_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_FuelDescription');?></th><!--Reference-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_Uom');?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('fuel_rate');?></th><!--Reference-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade bs-example-modal-lg" id="NewFuelADD_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    <?php echo $this->lang->line('fleet_add_fuel'); ?><!--Link Supplier--></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="NewFuelAdd_form">
                    <input type="hidden" name="fuelTypeID" id="fuelTypeID"/>

                    <div class="form-group">
                        <label for="fuelType" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('fleet_FuelDescription'); ?><!--Employee--></label>
                        <div class="col-sm-7">
                            <input type="text" name="fuelType"
                                   placeholder="<?php echo $this->lang->line('fleet_FuelDescription'); ?>"
                                   value="" id="fuelType" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fuel_rate" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('fuel_rate'); ?><!--Employee--></label>
                        <div class="col-sm-7">
                            <input type="number" name="fuel_rate"
                                   placeholder=""
                                   value="" id="fuel_rate" class="form-control text-right">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fuel_rate" class="col-sm-3 control-label"> UoM</label>
                        <div class="col-sm-7">
                        <?php echo form_dropdown('UOMid', $umo_arr, 'Each', 'class="form-control select2 umoDropdown input-mini" onchange="convertPrice_DO(this)" required'); ?>          
                        </div>
                    </div>



                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Save_new_fuel()">
                    <?php echo $this->lang->line('common_save'); ?><!--Add Fuel--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="NewFuelEDIT_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    <?php echo $this->lang->line('fleet_add_fuel'); ?><!--Link Supplier--></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="NewFuel_edit_form">
                    <input type="hidden" name="fuelTypeID_edit" id="fuelTypeID_edit"/>

                    <div class="form-group">
                        <label for="fuelType_edit" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('fleet_FuelDescription'); ?><!--Employee--></label>
                        <div class="col-sm-7">
                            <input type="text" name="fuelType_edit"
                                   placeholder="<?php echo $this->lang->line('fleet_FuelDescription'); ?>"
                                   value="" id="fuelType_edit" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fuel_rate_edit" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('fuel_rate'); ?><!--Employee--></label>
                        <div class="col-sm-7">
                            <input type="text" name="fuel_rate_edit"
                                   placeholder="<?php echo $this->lang->line('fuel_rate'); ?>"
                                   value="" id="fuel_rate_edit" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fuel_rate_edit" class="col-sm-3 control-label"> UoM</label>
                        <div class="col-sm-7">
                        <?php echo form_dropdown('UOMid', $umo_arr, 'Each', 'class="form-control select2 umoDropdown input-mini" onchange="convertPrice_DO(this)" required'); ?>          
                        </div>
                    </div>





                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Update_new_fuel()">
                    <?php echo $this->lang->line('common_save'); ?><!--Add Fuel--></button>
            </div>
        </div>
    </div>
</div>


<script>
  //  var NewVehicleAddForm = $('#NewVehicleAddForm');
    var oTable;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/fleet_saf_fuelMaster', '', 'Fuel Type Master');
        });
        usersTable();
    });


  function usersTable() {
      oTable = $('#fuel_table').DataTable({

          "bProcessing": true,
          "bServerSide": true,
          "bDestroy": true,
          "bStateSave": true,
          "sAjaxSource": "<?php echo site_url('Fleet/fetch_fuelMaster'); ?>",
          "aaSorting": [[0, 'desc']],
          "fnInitComplete": function () {
          },
      /*    "fnDrawCallback": function (oSettings) {
              $("[rel=tooltip]").tooltip();
              var tmp_i = oSettings._iDisplayStart;
              var iLen = oSettings.aiDisplay.length;
              var x = 0;
              for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                  $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                  x++;
              }
          },
          */
          "fnDrawCallback": function (oSettings) {
              $("[rel=tooltip]").tooltip();
              var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
              var tmp_i = oSettings._iDisplayStart;
              var iLen = oSettings.aiDisplay.length;
              var x = 0;
              for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                  $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                  if (parseInt(oSettings.aoData[x]._aData['fuelTypeID']) == selectedRowID) {
                      var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                      $(thisRow).addClass('dataTable_selectedTr');
                  }
                  x++;
              }
          },
          "aoColumns": [
              {"mData": "fuelTypeID"},
              {"mData": "description"},
              {"mData": "UoM"},
              {"mData": "fuel_price"},
              {"mData": "action"},
              {"mData": "fuelRate_search"}
          ],
          "columnDefs": [{"searchable": true, "visible": false, "targets": [5], "orderable": false},{"searchable": false, "targets": [0,2]}],
          "fnServerData": function (sSource, aoData, fnCallback) {
              $.ajax({
                  'dataType': 'json',
                  'type': 'POST',
                  'url': sSource,
                  'data': aoData,
                  'success': fnCallback
              });
          }
      });

  }

  function delete_fuel(id) {
      swal({
              title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
              text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
              cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
          },
          function () {
              $.ajax({
                  async: true,
                  url: "<?php echo site_url('Fleet/delete_fuel'); ?>",
                  type: 'post',
                  dataType: 'json',
                  data: {'fuelTypeID': id},
                  beforeSend: function () {
                      startLoad();
                  },
                  success: function (data) {
                      stopLoad();
                      myAlert(data[0], data[1]);
                      if (data[0] == 's') {
                          oTable.draw();
                      }
                  }, error: function () {
                      stopLoad();
                      myAlert('e', 'error');
                  }
              });
          }
      );
  }

  function NewFuelADD_model() {
          $('#fuelTypeID').val('').change();
          $('#fuelType').val('').change();
          $('#fuel_rate').val('').change();
          $('#NewFuelADD_model').modal('show');
  }

  function Save_new_fuel() {
      var data = $('#NewFuelAdd_form').serializeArray();
     // var fuelID = $('#fuelTypeID').val();

    /*  if(!fuelID){
          $('#fuelType').val();
          $('#fuel_rate').val();
      }*/

      $.ajax(
          {
              async: true,
              type: 'post',
              dataType: 'json',
              data: data,
              url: "<?php echo site_url('Fleet/SaveNewFuel_Master'); ?>",
              beforeSend: function () {
                  startLoad();
              },
              success: function (data) {
                  stopLoad();
                  refreshNotifications(true);
                  if (data['error'] == 1) {
                      myAlert('e', data['message']);
                  }
                  else if (data['error'] == 0) {
                      //oTable.draw();
                      $('#NewFuelADD_model').modal('hide');
                      oTable.draw();
                      myAlert('s', data['message']);
                  }

              }, error: function () {
                  alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                  /*An Error Occurred! Please Try Again*/
                  stopLoad();
                  refreshNotifications(true);
              }
          });
  }

  function edit_fuel(fuelTypeID) {

      $.ajax({
          async: true,
          type: 'post',
          dataType: 'json',
          data: {'fuelTypeID': fuelTypeID},
          url: "<?php echo site_url('Fleet/edit_fuel'); ?>",
          beforeSend: function () {
              startLoad();
          },
          success: function (data) {
              if (!jQuery.isEmptyObject(data)) {

                  $('#NewFuelEDIT_model').modal({backdrop: "static"});
                //  $('.exampleModalLabel').text('Edit | Fuel');
                  $('#fuelTypeID_edit').val(fuelTypeID);
                  $('#fuelType_edit').val(data['description']);
                  $('#fuel_rate_edit').val(data['fuelRate']);

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

  function Update_new_fuel(){
      var postData = $('#NewFuel_edit_form').serialize();
      $.ajax({
          type: 'post',
          url: '<?php echo site_url('Fleet/EditNewFuel_Master'); ?>',
          data: postData,
          dataType: 'json',
          beforeSend: function () {
              startLoad();
          },
          success :function(data){

              stopLoad();
              refreshNotifications(true);
              if (data['error'] == 1) {
                  myAlert('e', data['message']);
              }
              else if (data['error'] == 0) {
                  //oTable.draw();
                  $('#NewFuelEDIT_model').modal('hide');
                  oTable.draw();
                  myAlert('s', data['message']);
              }
          },
          error: function () {
              stopLoad();
              myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
          }
      })

  }
</script>



