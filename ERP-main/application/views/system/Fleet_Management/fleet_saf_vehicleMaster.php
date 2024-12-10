<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fleet_vehicle_master');
echo head_page($title, false);

?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-3">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active');?> </td><!--Active-->
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_in_active');?></td><!-- In-Active-->
                </tr>
            </table>
        </div>
        <div class="col-md-9 text-right">
            <button type="button" class="btn btn-primary btn-sm pull-right"  onclick="fetchPage('system/Fleet_Management/load_Vehicle_edit_view', '', '<?php echo $this->lang->line('fleet_add_new_asset')?>')"
                    style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_new_asset');?><!--New Asset-->
            </button>
        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table id="vehicle_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_product_no');?></th><!--Product No-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_Brand');?> </th><!--Brand-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_Model');?></th><!--Model-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                <th style="min-width: 5%"><?php echo $this->lang->line('fleet_capacity');?></th><!--Capacity-->
                <th style="min-width: 5%"><?php echo $this->lang->line('fleet_vehicle_fuel');?></th><!--Fuel Type-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_expected_km_per_hrs');?></th><!--Expected km per hrs-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>


<script>
  //  var NewVehicleAddForm = $('#NewVehicleAddForm');
    var oTable;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/fleet_saf_vehicleMaster', '', '');
        });
        usersTable();
    });


  function usersTable() {
      oTable = $('#vehicle_table').DataTable({

          "bProcessing": true,
          "bServerSide": true,
          "bDestroy": true,
          "bStateSave": true,
          "sAjaxSource": "<?php echo site_url('Fleet/fetch_vehicles'); ?>",
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
                  if (parseInt(oSettings.aoData[x]._aData['vehicleMasterID']) == selectedRowID) {
                      var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                      $(thisRow).addClass('dataTable_selectedTr');
                  }
                  x++;
              }
          },

          "aoColumns": [
              {"mData": "vehicleMasterID"},
              {"mData": "vehicleCode"},
              {"mData": "VehicleNo"},
              {"mData": "brand_description"},
              {"mData": "model_description"},
              {"mData": "vehDescription"},
              {"mData": "engineCapacity"},
              {"mData": "fuel_type_description"},
              {"mData": "expKMperLiter"},
              {"mData": "isActive"},
              {"mData": "action"},
          ],
          "columnDefs": [{"searchable": false, "targets": [0,9,10]}],
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
/*
  function edit_vehicle(id,element) {
      var table = $('#vehicle_table').DataTable();
      var thisRow = $(element);
      var details = table.row(thisRow.parents('tr')).data();
      $('#Driver-modal').modal({backdrop: "static"});
      $('#driverMasID').val($.trim(id));
      $('#driverName').val($.trim(details.driverName));
      $('#drivPhoneNo').val($.trim(details.drivPhoneNo));
      $('#drivAddress').val($.trim(details.drivAddress));
      $('#isActive').val($.trim(details.isActive));
  }
*/

  function delete_vehicle(id, description) {
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
                  url: "<?php echo site_url('Fleet/delete_vehicleMaster'); ?>",
                  type: 'post',
                  dataType: 'json',
                  data: {'vehicleMasterID': id},
                  beforeSend: function () {
                      startLoad();
                  },
                  success: function (data) {
                      stopLoad();
                      myAlert(data[0], data[1]);
                      if (data[0] == 's') {
                          usersTable();
                      }
                  }, error: function () {
                      stopLoad();
                      myAlert('e', 'error');
                  }
              });
          }
      );
  }



  function Edit_vehicles(id,element) {
          $.ajax({
              async: true,
              url: "*/<?php // echo site_url('Fleet/Save_vehicle'); ?>/*",
              type: 'post',
              dataType: 'json',
              data: {'vehicleMasterID': id},
              beforeSend: function () {
                  startLoad();
              },
              success: function (data) {
                  stopLoad();
                  myAlert(data[0], data[1]);
                  if (data[0] == 's') {
                      usersTable();
                  }
              }, error: function () {
                  stopLoad();
                  myAlert('e', 'error');
              }
          });
  }

</script>
