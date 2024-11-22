<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Subscription Renewal</title>

    <style type="text/css">
        img {
            max-width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
            background-color: #f6f6f6;
        }

        @media only screen and (max-width: 640px) {
            body {
                padding: 0 !important;
            }

            h3 {
                font-weight: 800 !important; margin: 20px 0 5px !important;
                font-size: 16px !important;
            }
            .container {
                padding: 0 !important; width: 100% !important;
            }
            .content {
                padding: 0 !important;
            }
            .content-wrap {
                padding: 10px !important;
            }
        }

        .panel-default{
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
            box-shadow: 0 1px 1px rgba(0,0,0,.05);
        }

        .panel-heading{
            color: #333;
            background-color: #f5f5f5;
            border-color: #ddd;
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            font-size: 12px;
        }

        .inv-table{
            width: 100% !important;
            margin-bottom: 0;
            border-spacing: 0;
            border-collapse: collapse;
            background-color: transparent;
        }

        .inv-table td {
            border-top: 1px solid #f4f4f4;
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
            font-size: 11px !important;
        }
    </style>
</head>

<body style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
        <td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display:
            block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
            <div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px;
                display: block; margin: 0 auto; padding: 20px;">
                <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing:
                    border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td colspan="2" align="center">
                            <?php $companyLogo = 'https://cloud.spur-int.com/images/spur-cirl-100.png'; ?>
                            <img src="<?php echo $companyLogo; ?>" alt="<?php echo $companyLogo ?>" style="width: 100px; height: 55px"/>
                        </td>
                    </tr>
                </table>
                <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;
                    font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="content-wrap" style="vertical-align: top; margin: 0; padding: 10px 20px;" valign="top">
                            <table width="100%" cellpadding="0" cellspacing="0" style="box-sizing: border-box; font-size: 14px; margin: 0;">
                                <tr style="box-sizing: border-box; font-size: 14px; margin: 0; text-align: justify; text-justify: inter-word;">
                                    <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">

                                        <?php
                                        $dPlace = $mas_data['invDecPlace'];
                                        $cur_code = $mas_data['CurrencyCode'];
                                        ?>
                                        <div style="font-size: 12px;">
                                            <h2 style="padding: 0px">Invoice No #<?=$mas_data['invNo']?></h2>
                                            <hr/>
                                            <span style="font-weight: bold">Invoiced To</span><br/>
                                            <?=$mas_data['company_name']?><br/>
                                            <?=$mas_data['companyPrintAddress']?>

                                            <br/><br/><span style="font-weight: bold">Invoice Date</span><br/>
                                            <?=date('l, F dS, Y ', strtotime($mas_data['invDate']));?>
                                        </div>

                                        <br/>

                                        <div class="panel-default">
                                            <div class="panel-heading"><b>Invoice Items</b></div>
                                            <div style="padding-right: 5px; padding-left: 15px">
                                                <table class="inv-table">
                                                    <tbody>
                                                    <tr>
                                                        <td><b>Description</b></td>
                                                        <td style="width: 120px; text-align: center"><b>Amount</b></td>
                                                    </tr>
                                                    <?php
                                                    $total = 0;
                                                    foreach ($det_data as $row){
                                                        $total += round($row['amount'], $dPlace);
                                                        echo '<tr>    
                                                                <td>'.$row['itemDescription'].'</td>
                                                                <td style="width: 120px; text-align: right">'.number_format($row['amount'], $dPlace).' '.$cur_code.'</td>
                                                             </tr>';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td style="text-align: right; background: #fafafb"><b>Total</b></td>
                                                        <td style="text-align: right; background: #fafafb"><?=number_format($row['amount'], $dPlace).' '.$cur_code?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <br/>
                                        </div>
                                    </td>
                                </tr>
                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; margin: 0;">
                                    <td class="" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                        <em>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox.</em>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class="footer" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
                    <table width="100%" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <td class="aligncenter " style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top">
                                <a href="<?php echo STATIC_LINK ?>" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;
                                font-size: 12px; color: #999; text-decoration: underline; margin: 0;"><?php echo SYS_NAME ?></a>.
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
    </tr>
</table>
</body>
</html>