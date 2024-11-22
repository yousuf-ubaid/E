<section class="content">
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" id="tmpCompanyID" value="0">
            <div id="div_loadCompanyAdminUsers" class="ajaxContainer"></div>
            <div id="mainContainer" class="ajaxContainer">
                <table id="company_table" class="table table-bordered table-striped table-condensed">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 5%">Update Date</th>
                        <th style="min-width: 15%">Subscription Start Date</th>
                        <th style="min-width: 15%">Next Renewal Date</th>
                        <th style="min-width: 15%">Last RenewedDate</th>
                        <th style="min-width: 15%">subscriptionAmount</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if ($invoices) {
                        $i = 1;
                        foreach ($invoices as $value) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $value['sub_update_datetime'] ?></td>
                                <td><?php echo $value['subscriptionStartDate'] ?></td>
                                <td><?php echo $value['nextRenewalDate'] ?></td>
                                <td><?php echo $value['lastRenewedDate'] ?></td>
                                <td><?php echo $value['subscriptionAmount'] ?></td>
                            </tr>
                            <?php
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>

