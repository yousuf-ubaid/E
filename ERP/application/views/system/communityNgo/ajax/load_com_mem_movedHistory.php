
    <div class="form-group">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Family Name</th>
                                <th>Added Date</th>
                                <th>Is Default</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($memMovedHis)) {
                                $f =1;
                            foreach ($memMovedHis as $mainJb => $comChat) {

                            ?>
                            <tr>
                                <td><?php echo $f; ?></td>
                                <td><?php echo $comChat['FamilyName']; ?></td>
                                <td><?php echo $comChat['FamMemAddedDate']; ?></td>
                                <td style="text-align: center;">
                                    <?php   if($comChat['isMove'] ==1){ ?>
                                        <img style="width: 15%;" src="<?php echo base_url("images/community/right.jpg") ?>">
                                    <?php } else{?>
                                        <img style="width: 15%;" src="<?php echo base_url("images/community/wrong.jpg") ?>">
                                    <?php } ?>
                                </td>
                            </tr>
                                <?php
                                $f++;
                            }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->


    </div>


<?php
