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
        <label class="title" style="padding-right:15%"><strong> Visits &nbsp;</strong><span class="label label-success"><?php echo $extra['master']['numberOfVisit'];?> </span> </label>
    </div>

    <div class="table-responsive">
        <h5><strong>Technical Data</strong></h5>
    </div>
    <div class="table-responsive">
        <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th rowspan="2" style="width: 5%">#</th>
                <th rowspan="2" style="width: 10%">Age (Days)</th>
                <th rowspan="2" style="width: 10%">No of Birds</th>
                <th colspan="2" style="width: 10%">Mortality</th>
                <th rowspan="2" style="width: 10%">Total Feed (Kg)</th>
                <th rowspan="2" style="width: 10%">Av. Feed per Bird</th>
                <th rowspan="2" style="width: 10%">Av. Body Weight</th>
                <th rowspan="2" style="width: 10%">FCR</th>
                <th rowspan="2" style="width: 20%">Remarks</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Percent</th>
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
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td ><strong>No Of Feeders</strong></td>
                <td ><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['numberOfFeeders'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['numberOfFeeders'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['numberOfFeeders'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>

                <td><strong>Litter Quality</td>
                <td><strong>:</strong></td>
                <td>
                    <?php  if($extra['master']['litterQuality'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['litterQuality'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['litterQuality'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td ><strong>No Of Drinkers</strong></td>
                <td ><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['numberOfDrinkers'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['numberOfDrinkers'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else  if($extra['master']['numberOfDrinkers'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>

                <td><strong>Smell Of Amonia</td>
                <td><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['smellOfAmonia'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['smellOfAmonia'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['smellOfAmonia'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td ><strong>Feeder Height</strong></td>
                <td ><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['feederHeight'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['feederHeight'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['feederHeight'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>

                <td><strong>Ventilation</td>
                <td><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['ventilation'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['ventilation'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['ventilation'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td ><strong>Drinker Height</strong></td>
                <td ><strong>:</strong></td>

                <td>
                    <?php if($extra['master']['drinkerHeight'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['drinkerHeight'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['drinkerHeight'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>

                <td><strong>Bio Security</td>
                <td><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['biosecurity'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['biosecurity'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['biosecurity'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td ><strong>Density</strong></td>
                <td ><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['density'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['density'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['density'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>

                <td><strong>Record Keeping</td>
                <td><strong>:</strong></td>
                <td>
                    <?php if($extra['master']['recordKeeping'] == 1){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Very Satisfactory</label>
                    <?php } else if($extra['master']['recordKeeping'] == 2){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; Satisfactory</label>
                    <?php } else if($extra['master']['recordKeeping'] == 3){ ?>
                        <label><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;Un Satisfactory</label>
                    <?php } ?>
                </td>
            </tr>
            </tbody>
        </table>
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
    </script>
<?php
