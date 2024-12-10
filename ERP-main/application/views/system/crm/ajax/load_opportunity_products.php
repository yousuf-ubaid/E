<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <thead>
            <th>#</th>
            <th>Product Name</th>
            <th>Product Description</th>
            <th>Price</th>
            </thead>
            <tbody>
            <?php
            $x = 1;
            $total = 0;
            foreach ($header as $val) { ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['productName']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['productDescription']; ?></a></td>
                    <td class="mailbox-name" style="text-align: right"><a
                            href="#"><?php echo $val['CurrencyCode'] . " : " . format_number($val['price'],2) ?></a></td>
                </tr>
                <?php
                $x++;
                $total += $val['price'];
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td style="text-align: right"><?php echo format_number($total,2) ?></td>
            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO PRODUCTS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>