<!--<div class="row" style="margin-top: 5px">
    <div class="col-md-12 pull-right">
        <a target="_blank"
           href="<?php /*echo site_url('buyback/load_farmVisitReport_confirmation/') . '/' . $farmerVisitID */?>"><span
                title="Print" rel="tooltip" class="glyphicon glyphicon-print">PFD</span></a>
    </div>
</div>-->
<?php echo fetch_account_review(false,true); ?>
<?php if($type==true){?>
    <style>

        .bgcolour {
            background-color: #00a65a;
            margin-top: 3%;
        }
        .bgcolourconfirm {
            background-color: #f9ac38;
            margin-top: 3%;
        }
        .item-labellabelbuyback {
            color: #fff;
            height: 21px;
            width: 90px;
            position: absolute;
            font-weight: bold;
            padding-left: 10px;
            padding-top: 0px;
            top: 10px;
            right: -59px;
            margin-right: 0;
            border-radius: 3px 3px 0 3px;
            box-shadow: 0 3px 3px -2px #ccc;
            text-transform: capitalize;
        }
        .item-labellabelbuyback:after {
            top: 20px;
            right: 0;
            border-top: 4px solid #1f1d1d;
            border-right: 4px solid rgba(0, 0, 0, 0);
            content: "";
            position: absolute;
        }
    </style>
<?php }?>

<!--<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php
/*                            echo mPDFImage . $this->common_data['company_data']['company_logo']; */?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php /*echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; */?></strong>
                            </h3>

                            <p><?php /*echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; */?></p>
                            <h4>Farm Visit Report</h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Farmer ID</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['farmerCode']; */?></td>
                    </tr>
                    <tr>
                        <td><strong>Farmer Name</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['farmerName']; */?></td>
                    </tr>
                    <tr>
                        <td><strong>Batch Code</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['batchCode']; */?></td>
                    </tr>
                    <tr>
                        <td><strong>Document Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php /*echo $extra['master']['documentDate']; */?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:15%;"><strong>Farmer Address</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php /*echo $extra['master']['fvrFarmerAddress']; */?></td>
            <td style="width:15%;"><strong>No of Birds</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php /*echo $extra['master']['fvrNumberOfBirds']; */?></td>
        </tr>
        <tr>
            <td style="width:15%;"><strong>Farm Type</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;">
                <?php /*if ($extra['master']['fvrFarmType'] == 1) {
                    echo "All in - All out";
                } elseif ($extra['master']['fvrFarmType'] == 2) {
                    echo "Multi - Age";
                } */?>
            </td>
            <td style="width:15%;"><strong>Hatch Date</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php /*echo $extra['master']['hatchDate']; */?></td>
        </tr>
        <tr>
            <td style="width:15%;"><strong>Breed</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php /*echo $extra['master']['fvrBreed']; */?></td>
            <td style="width:15%;"><strong>Feed</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php /*echo $extra['master']['fvrFeed']; */?></td>
        </tr>
        </tbody>
    </table>
</div>-->
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td>
                <table>
                    <tr>
                        <td style="text-align: center;">
                            <h4 >Farm Visit Report</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php if($type==true){
    $class = 'theadtr';
    ?>
    <?php if($extra['master']['confirmedYN']==1) {
        echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="item-labellabelbuyback file bgcolour">Confirmed</div>
    </article>';
    }?>
<?php }else
{
    $class = '';
}?>
<br>
<hr style="margin-top: 0%">
<br>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td ><strong>Farmer ID</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['farmerCode']; ?></td>

            <td><strong>Farmer Name</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['farmerName']; ?></td>
        </tr>
        <tr>
            <td ><strong>Batch Code</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['batchCode']; ?></td>

            <td><strong>Document Code</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['visitDocumentCode']; ?></td>
        </tr>
        <tr>
            <td ><strong>Farmer Address</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['fvrFarmerAddress']; ?></td>

            <td><strong>Document Date</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        <tr>
            <td ><strong>Farm Type</strong></td>
            <td ><strong>:</strong></td>

            <td> <?php if ($extra['master']['fvrFarmType'] == 1) {
                    echo "All in - All out";
                } elseif ($extra['master']['fvrFarmType'] == 2) {
                    echo "Multi - Age";
                } ?></td>

            <td><strong>No of Birds</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['fvrNumberOfBirds']; ?></td>
        </tr>
        <tr>
            <td ><strong>Breed</strong></td>
            <td ><strong>:</strong></td>

            <td><?php echo $extra['master']['fvrBreed']; ?> </td>

            <td><strong>Hatch Date</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['hatchDate']; ?></td>

        </tr>
        <tr>
            <td ><strong>Field officer</strong></td>
            <td ><strong>:</strong></td>
            <td><?php echo $extra['master']['fieldOfficer']; ?> </td>

            <td><strong>Feed</td>
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['fvrFeed']; ?> </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive" style="margin-left: 0%; ">
    <label class="title" style="padding-right:15%"><strong> Visits</strong><span class="label label-success" style="margin-left: 20%"><?php echo $extra['master']['numberOfVisit'];?> </span> </label>
</div>

<div class="table-responsive">
    <h5><strong>Technical Data</strong></h5>
</div>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th rowspan="2" <?php echo $class?> style="width: 5%">#</th>
            <th rowspan="2" <?php echo $class?> style="width: 10%">Age (Days)</th>
            <th rowspan="2" <?php echo $class?> style="width: 10%">No of Birds</th>
            <th colspan="2" <?php echo $class?> style="width: 10%">Mortality</th>
            <th rowspan="2" <?php echo $class?> style="width: 10%">Total Feed (Kg)</th>
            <th rowspan="2" <?php echo $class?> style="width: 10%">Av. Feed per Bird</th>
            <th rowspan="2" <?php echo $class?> style="width: 10%">Av. Body Weight</th>
            <th rowspan="2" <?php echo $class?> style="width: 10%">FCR</th>
            <th rowspan="2" <?php echo $class?> style="width: 20%">Remarks</th>
        </tr>
        <tr>
            <th <?php echo $class?>>No</th>
            <th <?php echo $class?>>Percent</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php
        if (!empty($extra['detail'])) {
            $x = 1;
            foreach ($extra['detail'] as $val) {
                echo '<tr>';
                echo '<td>' . $x . '</td>';
                echo '<td style="text-align: center">' . $val['age'] . '</td>';
                echo '<td style="text-align: center">' . $val['numberOfBirds'] . '</td>';
                echo '<td style="text-align: center">' . $val['mortalityNumber'] . '</td>';
                echo '<td style="text-align: center">' . $val['mortalityPercent'] . '</td>';
                echo '<td style="text-align: center">' . $val['totalFeed'] . '</td>';
                echo '<td style="text-align: center">' . $val['avgFeedperBird'] . '</td>';
                echo '<td style="text-align: center">' . $val['avgBodyWeight'] . '</td>';
                echo '<td style="text-align: center">' . $val['fcr'] . '</td>';
                echo '<td>' . $val['remarks'] . '</td>';
                echo '</tr>';
                $x++;
            }
        } else {
            echo '<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <h5><strong>Details of the Farm Conditions / Management and Instruction given to the Farmer</strong></h5>
</div>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:100%;"><?php echo $extra['master']['fvrDetailFarmDescription']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <h5><strong>Quick Management Appraisal Score</strong></h5>
</div>
<br>
<div class="table-responsive">
    <div class="row">
        <div class="form-group col-sm-2">
            <label class="title"> <span class="label label-success">A</span>&nbsp;Very
                Satisfactory</label>
        </div>

        <div class="form-group col-sm-2">
            <label class="title"> <span class="label label-warning">B</span>&nbsp;Satisfactory</label>
        </div>

        <div class="form-group col-sm-2">
            <label class="title"> <span class="label label-danger">C</span>&nbsp;Un Satisfactory</label>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
<?php if($extra['taskDone']){
    foreach ($extra['taskDone'] as $val){ ?>
        <div class="col-sm-6">
            <div class="form-group col-sm-5">
                <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;
                    <?php echo $val['description']; ?>
                </label>
            </div>
            <div class="form-group col-sm-7  col-xs-12">
                <div class="skin-section extraColumns feeders">
                    <label class="radio-inline">
                        <div class="skin-section extraColumnsgreen">
                            <input id="numberOfFeedersA" type="radio" data-caption="" class="columnSelected"
                                   name="task<?php echo $val['description']?>[]" value="1" <?php if($val['level'] == 1){ echo "checked"; } ?>>
                            <label for="checkbox">&nbsp;&nbsp;A</label></div>
                    </label>
                    <label class="radio-inline">
                        <div class="skin-section extraColumnsyellow">
                            <input id="numberOfFeedersB" type="radio" data-caption="" class="columnSelected"
                                   name="task<?php echo $val['description']?>[]" value="2" <?php if($val['level'] == 2){ echo "checked"; } ?>>
                            <label for="checkbox">&nbsp;&nbsp;B</label></div>
                    </label>

                    <label class="radio-inline">
                        <div class="skin-section extraColumnsred">
                            <input id="numberOfFeedersC" type="radio" data-caption="" class="columnSelected"
                                   name="task<?php echo $val['description']?>[]" value="3" <?php if($val['level'] == 3){ echo "checked"; } ?>>
                            <label for="checkbox">&nbsp;&nbsp;C</label></div>
                    </label>
                </div>
            </div>
        </div>
  <?php  }
}  else {
    echo '<div class="search-no-results" style="margin-left: 15%; margin-right: 15%">No Tasks To Display</div>';
}?>
</div>
<!--
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;No Of
            Feeders</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns feeders">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="numberOfFeedersA" type="radio" data-caption="" class="columnSelected"
                           name="numberOfFeeders" value="1" <?php /*if($extra['master']['numberOfFeeders'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="numberOfFeedersB" type="radio" data-caption="" class="columnSelected"
                           name="numberOfFeeders" value="2" <?php /*if($extra['master']['numberOfFeeders'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="numberOfFeedersC" type="radio" data-caption="" class="columnSelected"
                           name="numberOfFeeders" value="3" <?php /*if($extra['master']['numberOfFeeders'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Litter Quality
        </label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="litterQualityA" type="radio" data-caption="" class="columnSelected"
                           name="litterQuality" value="1" <?php /*if($extra['master']['litterQuality'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="litterQualityB" type="radio" data-caption="" class="columnSelected"
                           name="litterQuality" value="2" <?php /*if($extra['master']['litterQuality'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="litterQualityC" type="radio" data-caption="" class="columnSelected"
                           name="litterQuality" value="3" <?php /*if($extra['master']['litterQuality'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;No Of
            Drinkers</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="numberOfDrinkersA" type="radio" data-caption="" class="columnSelected"
                           name="numberOfDrinkers" value="1" <?php /*if($extra['master']['numberOfDrinkers'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="numberOfDrinkersB" type="radio" data-caption="" class="columnSelected"
                           name="numberOfDrinkers" value="2" <?php /*if($extra['master']['numberOfDrinkers'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="numberOfDrinkersC" type="radio" data-caption="" class="columnSelected"
                           name="numberOfDrinkers" value="3" <?php /*if($extra['master']['numberOfDrinkers'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Smell Of
            Amonia</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="smellOfAmoniaA" type="radio" data-caption="" class="columnSelected"
                           name="smellOfAmonia" value="1" <?php /*if($extra['master']['smellOfAmonia'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="smellOfAmoniaB" type="radio" data-caption="" class="columnSelected"
                           name="smellOfAmonia" value="2" <?php /*if($extra['master']['smellOfAmonia'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="smellOfAmoniaC" type="radio" data-caption="" class="columnSelected"
                           name="smellOfAmonia" readonly value="3" <?php /*if($extra['master']['smellOfAmonia'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Feeder
            Height</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="feederHeightA" type="radio" data-caption="" class="columnSelected"
                           name="feederHeight" readonly value="1" <?php /*if($extra['master']['feederHeight'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="feederHeightB" type="radio" data-caption="" class="columnSelected"
                           name="feederHeight" readonly value="2" <?php /*if($extra['master']['feederHeight'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="feederHeightC" type="radio" data-caption="" class="columnSelected"
                           name="feederHeight" readonly value="3" <?php /*if($extra['master']['feederHeight'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Ventilation
        </label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="ventilationA" type="radio" data-caption="" class="columnSelected"
                           name="ventilation" readonly value="1" <?php /*if($extra['master']['ventilation'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="ventilationB" type="radio" data-caption="" class="columnSelected"
                           name="ventilation" readonly value="2" <?php /*if($extra['master']['ventilation'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="ventilationC" type="radio" data-caption="" class="columnSelected"
                           name="ventilation" readonly value="3" <?php /*if($extra['master']['ventilation'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Drinker Height
        </label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="drinkerHeightA" type="radio" data-caption="" class="columnSelected"
                           name="drinkerHeight" readonly value="1" <?php /*if($extra['master']['drinkerHeight'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="drinkerHeightB" type="radio" data-caption="" class="columnSelected"
                           name="drinkerHeight" readonly value="2" <?php /*if($extra['master']['drinkerHeight'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="drinkerHeightC" type="radio" data-caption="" class="columnSelected"
                           name="drinkerHeight" readonly value="3" <?php /*if($extra['master']['drinkerHeight'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Bio
            Security</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="biosecurityA" type="radio" data-caption="" class="columnSelected"
                           name="biosecurity" readonly value="1" <?php /*if($extra['master']['biosecurity'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="biosecurityB" type="radio" data-caption="" class="columnSelected"
                           name="biosecurity" readonly value="2" <?php /*if($extra['master']['biosecurity'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="biosecurityC" type="radio" data-caption="" class="columnSelected"
                           name="biosecurity" readonly value="3" <?php /*if($extra['master']['biosecurity'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Density</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="densityA" type="radio" data-caption="" class="columnSelected" name="density"
                           readonly value="1" <?php /*if($extra['master']['density'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="densityB" type="radio" data-caption="" class="columnSelected" name="density"
                           readonly value="2" <?php /*if($extra['master']['density'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="densityC" type="radio" data-caption="" class="columnSelected" name="density"
                           readonly value="3" <?php /*if($extra['master']['density'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
    <div class="form-group col-sm-2">
        <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Record Keeping
        </label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <input id="recordKeepingA" type="radio" data-caption="" class="columnSelected"
                           name="recordKeeping" readonly value="1" <?php /*if($extra['master']['recordKeeping'] == 1){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;A</label></div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsyellow">
                    <input id="recordKeepingB" type="radio" data-caption="" class="columnSelected"
                           name="recordKeeping" readonly value="2" <?php /*if($extra['master']['recordKeeping'] == 2){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;B</label></div>
            </label>

            <label class="radio-inline">
                <div class="skin-section extraColumnsred">
                    <input id="recordKeepingC" type="radio" data-caption="" class="columnSelected"
                           name="recordKeeping" readonly value="3" <?php /*if($extra['master']['recordKeeping'] == 3){ echo "checked"; } */?>>
                    <label for="checkbox">&nbsp;&nbsp;C</label></div>
            </label>
        </div>
    </div>
</div>-->
<br>
<div class="table-responsive">
    <h5><strong>Image Attachment</strong></h5>
</div>
<br>
<div class="row" style="margin-top: 10px; padding-left: 2%;">
<?php
if($extra['images']){
    foreach ($extra['images'] as $val){
        $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
        //echo '<a target="_blank" href="' . $link . '" >' . $val['myFileName'] . '</a><br>';
        echo '<a target="_blank" href="' . $link . '" >';
        echo ' <img src="' . urldecode($link) . '" height="100" width="100">';
        echo '</a>';
    }
} else {
    echo '<div class="text-center">NO ATTACHED IMAGES AVAILABLE</div>';
}?>
</div>
<br>
<div class="table-responsive">
    <?php if ($extra['master']['confirmedYN']) { ?>
        <table style="width: 500px !important;">
            <tbody>
            <tr>
                <td><b> Confirmed By </b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    <?php } ?>
</div>
<script>

    $('.review').removeClass('hide');
    $(document).ready(function () {
        <?php if ($extra['master']['confirmedYN']) { ?>
            $(':radio:not(:checked)').attr('disabled', true);
        <?php } ?>

        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        $('.extraColumnsyellow input').iCheck({
            checkboxClass: 'icheckbox_square_relative-yellow',
            radioClass: 'iradio_square_relative-yellow',
            increaseArea: '20%'
        });

        $('.extraColumnsred input').iCheck({
            checkboxClass: 'icheckbox_square_relative-red',
            radioClass: 'iradio_square_relative-red',
            increaseArea: '20%'
        });
    });


    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Buyback/load_farmVisitReport_confirmation'); ?>/<?php echo $farmerVisitID ?>";
    $("#a_link").attr("href",a_link);
</script>