<?php
//$confirmedUser = fetch_employeeNo($header["createdUserID"]);
//$approvedUser = fetch_employeeNo($header["approvedbyEmpID"]);
//$currencyCode = fetch_currency_dec($this->common_data["company_data"]["company_default_currency"]);

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
// $confirmedUser = fetch_employeeNo($header["createdUserID"]);
// $approvedUser = fetch_employeeNo($header["approvedbyEmpID"]);
// $reviewedUser = fetch_employeeNo($header["reviewedBy"]);
// $currencyCode = fetch_currency_dec($header['CurrencyCode']);
//$this->load->library('NumberToWords');
$colspan = 4;
if ($viewMargin == 1) {
    $colspan = 8;
}
?>

<script>

</script>


