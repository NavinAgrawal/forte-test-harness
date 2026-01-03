<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<!-- Template Design by www.studio7designs.com. -->

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
<meta content="en-gb" http-equiv="Content-Language" />
<title>Freshdesk Tickets</title>
<link href="favicon.ico" rel="SHORTCUT ICON" />
<link href="style.css" type="text/css" rel="stylesheet" />
</head>

<!-- Begin Body -->
<body>

<div id="border">
  <div id="container">
    <!-- navbar -->
    <?php include 'include-navbar.php'; ?>
    <!-- header backround image is in the style sheet-->
    <div id="header">
      <a href="index.php"></a>
    </div>
    <!-- content -->
    <div id="content">
      <div class="splitleft">
        <p><span class="style4">Freshdesk Tickets</span></p>
        <p>&nbsp;</p>
        <p>This form is for creating Freshdesk tickets.
        </p>
        <center><img border="0" src="images/spacer.gif" width="20" height="30">
        <br>
		
		
<div style="display:inline-block; border: 3px solid gray; padding:20px; background-color:#F9F9F9">
    <form align="left" action="send_email.php" method="post">
        <table>
            <tr>
                <td align="right">Caller's first name: </td>
                <td>
                    <input type="text" name="first_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Caller's last name: </td>
                <td>
                    <input type="text" name="last_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Caller's company: </td>
                <td>
                    <input type="text" size="43" name="company_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Caller's email: </td>
                <td>
                    <input type="text" size="43" name="email_address" />
                </td>
            </tr>
            <tr>
                <td align="right">Category of caller: </td>
                <td>
                    <select class="select" name="caller_category">
                        <option value="Account Holder">Account Holder</option>
                        <option value="Developer">Developer</option>
                        <option selected value="Existing Merchant">Existing Merchant</option>
                        <option value="Existing Partner">Existing Partner</option>
                        <option value="Partner - Application Inquiry">Partner - Application Inquiry</option>
                        <option value="Merchant - Application Inquiry">Merchant - Application Inquiry</option>
                        <option value="Internal Department">Internal Department</option>
                        <option value="3rd Party Vendor / Other">3rd Party Vendor / Other</option>
                    </select>
                </td>
            </tr>
            <td align="right">Merchant/ISO ID: </td>
            <td>
                <input type="text" size="24" name="merchant_id" />
            </td>
            </tr>
            <tr>
                <td align="right">Tags: </td>
                <td>
                    <input type="text" size="24" name="tags" />
                </td>
            </tr>
            <tr>
                <td align="right">Status: </td>
                <td>
                    <select class="select" name="status">
                        <option value="2">Open</option>
                        <option value="3">Pending</option>
                        <option value="5">Closed</option>
                        <!--option value="Waiting on Customer">Waiting on Customer</option>
                        <option value="Waiting on Third Party">Waiting on Third Party</option -->
                        <option selected value="4">Resolved</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">Type: </td>
                <td>
                    <select class="select" name="type">
                        <!--option value="ACH Reserve Release">ACH Reserve Release</option>
                        <option value="ACH/CC Reserve Balance Inquiry">ACH/CC Reserve Balance Inquiry</option>
                        <option value="Account Change - Company / Business Entity Change">Account Change - Company / Business Entity Change</option>
                        <option value="Add ACH">Add ACH</option>
                        <option value="Add Account Updater">Add Account Updater</option>
                        <option value="Add Amex">Add Amex</option>
                        <option value="Add Bank Account">Add Bank Account</option>
                        <option value="Add Credit Card (Forte Relationship)">Add Credit Card (Forte Relationship)</option>
                        <option value="Add Credit Card (Gateway/VAR)">Add Credit Card (Gateway/VAR)</option>
                        <option value="Add to Hierarchy Tree">Add to Hierarchy Tree</option>
                        <option value="Address Change">Address Change</option>
                        <option value="Add Forte Protect">Add Forte Protect</option>
                        <option value="Add Forte Verify">Add Forte Verify</option>
                        <option value="Application-Additional Documentation Request">Application-Additional Documentation Request</option>
                        <option value="Application Status">Application Status</option>
                        <option value="Assistance Locating a Transaction">Assistance Locating a Transaction</option>
                        <option value="Auto-Daily - Add / Delete / Update Existing">Auto-Daily - Add / Delete / Update Existing</option>
                        <option value="Auto-Daily - Missing Report / Request to Regenerate">Auto-Daily - Missing Report / Request to Regenerate</option>
                        <option value="Auto-Daily - Update Report Contact Email">Auto-Daily - Update Report Contact Email</option>
                        <option value="Auto-Daily - New  Report / Setup">Auto-Daily - New Report / Setup</option>
                        <option value="Bank Account Change - Other">Bank Account Change - Other</option>
                        <option value="Billing Bank Change">Billing Bank Change</option>
                        <option value="Billing">Billing</option>
                        <option value="Billing Return Collections">Billing Return Collections</option>
                        <option value="Billing Return Outside Collections">Billing Return Outside Collections</option>
                        <option value="BillPay- General / Inquiry">BillPay- General / Inquiry</option>
                        <option value="BillPay Incident/Issue">BillPay Incident/Issue</option>
                        <option value="BillPay - Spec Update">BillPay - Spec Update</option>
                        <option value="BillPay - Training">BillPay - Training</option>
                        <option value="BillPay Website Update">BillPay Website Update</option>
                        <option value="CC Reserve Release">CC Reserve Release</option>
                        <option value="CC Processor Conversion Request">CC Processor Conversion Request</option>
                        <option value="CFO / Payment File Issue/Incident">CFO / Payment File Issue/Incident</option>
                        <option value="CFO / Payment File - New Setup Request">CFO / Payment File - New Setup Request</option>
                        <option value="CFO / Payment File - Regenerate CFO / Payment File">CFO / Payment File - Regenerate CFO / Payment File</option>
                        <option value="CFO / Payment File Requests - General Inquiry / Other">CFO / Payment File Requests - General Inquiry / Other</option>
                        <option value="CFO/Payment File- Re-sending File">CFO/Payment File- Re-sending File</option>
                        <option value="CFO / Payment File - Training">CFO / Payment File - Training</option>
                        <option value="CFO / Payment File Update">CFO / Payment File Update</option>
                        <option value="Checks to Enter">Checks to Enter</option>
                        <option value="Client Data Transfer">Client Data Transfer</option>
                        <option value="Closure">Closure</option>
                        <option value="Company / DBA Name Change">Company / DBA Name Change</option>
                        <option value="Commission Adjustment / Refunds">Commission Adjustment / Refunds</option>
                        <option value="Compliance POA">Compliance POA</option>
                        <option value="Compliance Request">Compliance Request</option>
                        <option value="Conversion and Migrations">Conversion and Migrations</option>
                        <option value="Contact Change Request (Signer)">Contact Change Request (Signer)</option>
                        <option value="Contact Change Request">Contact Change Request</option>
                        <option value="Customer Inquiry">Customer Inquiry</option>
                        <option value="Declined Application Inquiry">Declined Application Inquiry</option>
                        <option value="Defect">Defect</option>
                        <option value="Descriptor Updates">Descriptor Updates</option>
                        <option value="Dex - Question or Issue">Dex - Question or Issue</option>
                        <option value="Dex - Other">Dex - Other</option>
                        <option value="Dex Sandbox">Dex Sandbox</option>
                        <option value="Dex - Send Invite">Dex - Send Invite</option>
                        <option value="Dex Training">Dex Training</option>
                        <option value="Dex Training - All Modules">Dex Training - All Modules</option>
                        <option value="Direct Recovery Comments">Direct Recovery Comments</option>
                        <option value="Discount Fee">Discount Fee</option>
                        <option value="Disputes / Chargebacks - Billing for Chargeback Activity">Disputes / Chargebacks - Billing for Chargeback Activity</option>
                        <option value="Dispute / Chargeback Credit">Dispute / Chargeback Credit</option>
                        <option value="Dispute / Chargeback Debit">Dispute / Chargeback Debit</option>
                        <option value="Dispute / Chargeback Debit - Buildium">Dispute / Chargeback Debit - Buildium</option>
                        <option value="Dispute / Chargeback -  Documentation Request">Dispute / Chargeback - Documentation Request</option>
                        <option value="Disputes / Chargebacks Manual Debit / Credit">Disputes / Chargebacks Manual Debit / Credit</option>
                        <option value="Dispute / Chargebacks - Other">Dispute / Chargebacks - Other</option>
                        <option value="Dispute / Chargeback Re-debit">Dispute / Chargeback Re-debit</option>
                        <option value="Dispute Chargeback Status or Inquiry">Dispute Chargeback Status or Inquiry</option>
                        <option value="Documentation Requested from Bank">Documentation Requested from Bank</option>
                        <option value="Do Not Net">Do Not Net</option>
                        <option value="Duplicate Bank Account">Duplicate Bank Account</option>
                        <option value="Duplicate Payment Timeout">Duplicate Payment Timeout</option>
                        <option value="Email Address Change">Email Address Change</option>
                        <option value="Enrollment">Enrollment</option>
                        <option value="Equipment">Equipment</option>
                        <option value="Equipment Return Audit">Equipment Return Audit</option>
                        <option value="Equipment Invoice Processing">Equipment Invoice Processing</option>
                        <option value="Equipment  - New Order">Equipment - New Order</option>
                        <option value="Equipment - Order Inventory Status">Equipment - Order Inventory Status</option>
                        <option value="Expedited Funding Request">Expedited Funding Request</option>
                        <option value="External Billing Return Collection">External Billing Return Collection</option>
                        <option value="Faster Funding">Faster Funding</option>
                        <option value="Forte Verify Inquiry">Forte Verify Inquiry</option>
                        <option value="Fraud Alert">Fraud Alert</option>
                        <option value="Freshdesk Issue or Request">Freshdesk Issue or Request</option>
                        <option value="Funding Correction Research">Funding Correction Research</option>
                        <option value="Funding Credit Correction">Funding Credit Correction</option>
                        <option value="Funding Debit Correction">Funding Debit Correction</option>
                        <option value="Funding Delay Inquiry">Funding Delay Inquiry</option>
                        <option value="Funding Hold Inquiry">Funding Hold Inquiry</option>
                        <option value="Funding Hold Release Request">Funding Hold Release Request</option>
                        <option value="Funding Reconciliation Assistance">Funding Reconciliation Assistance</option>
                        <option value="Funding Return">Funding Return</option>
                        <option value="Fund Four Funding">Fund Four Funding</option>
                        <option value="Hold Day Change">Hold Day Change</option>
                        <option value="Immediate Termination">Immediate Termination</option>
                        <option value="Incident">Incident</option>
                        <option value="Integration Inquiry">Integration Inquiry</option>
                        <option value="Internal Data Request">Internal Data Request</option>
                        <option value="Internal Funding - Recall / Reversal Request">Internal Funding - Recall / Reversal Request</option>
                        <option value="Invoice Request to Delete / Waive Current Receivable">Invoice Request to Delete / Waive Current Receivable</option>
                        <option value="IVR">IVR</option>
                        <option value="IVR - Disable">IVR - Disable</option>
                        <option value="IVR - Enable">IVR - Enable</option>
                        <option value="IVR  - General Inquiry / Other">IVR - General Inquiry / Other</option>
                        <option value="IVR Incident / Issue">IVR Incident / Issue</option>
                        <option value="IVR - New Setup">IVR - New Setup</option>
                        <option value="IVR Training">IVR Training</option>
                        <option value="Legal Entity Change">Legal Entity Change</option>
                        <option value="Legal Inquiry">Legal Inquiry</option>
                        <option value="Less Day Hold">Less Day Hold</option>
                        <option value="Limit Increase">Limit Increase</option>
                        <option value="Limit Inquiry">Limit Inquiry</option>
                        <option value="Limit Request - Limit Increase Request">Limit Request - Limit Increase Request</option>
                        <option value="Limit Request - Over the Limit Notification">Limit Request - Over the Limit Notification</option>
                        <option value="Manual Bill">Manual Bill</option>
                        <option value="MCC Code Update">MCC Code Update</option>
                        <option value="Merchant Setup on Incorrect Partner Channel">Merchant Setup on Incorrect Partner Channel</option>
                        <option value="Merchant Set Up with Incorrect Fees">Merchant Set Up with Incorrect Fees</option>
                        <option value="Migrate to Dex">Migrate to Dex</option>
                        <option value="Missing Funds Inquiry">Missing Funds Inquiry</option>
                        <option value="Net Funding">Net Funding</option>
                        <option value="Next Day Funding Request">Next Day Funding Request</option>
                        <option value="New Application Request">New Application Request</option>
                        <option value="New Application Request - Existing Merchant">New Application Request - Existing Merchant</option>
                        <option value="New Feature Request">New Feature Request</option>
                        <option value="Operations Issue">Operations Issue</option-->
                        <option value="Other">Other</option>
                        <!--option value="Outage Inquiry/Report Outage">Outage Inquiry/Report Outage</option>
                        <option value="Over The Limit Notification (OLTN)">Over The Limit Notification (OLTN)</option>
                        <option value="Partial Dispute / Chargeback Credit">Partial Dispute / Chargeback Credit</option>
                        <option value="Partial Dispute / Chargeback Debit">Partial Dispute / Chargeback Debit</option>
                        <option value="Partial Reserve Release">Partial Reserve Release</option>
                        <option value="Partner Bank Account Change">Partner Bank Account Change</option>
                        <option value="Partner Billing Refund">Partner Billing Refund</option>
                        <option value="Partner Proposal">Partner Proposal</option>
                        <option value="PCI Inquiry">PCI Inquiry</option>
                        <option value="Permissions Request">Permissions Request</option>
                        <option value="Phone Number Change">Phone Number Change</option>
                        <option value="Pricing Inquiry">Pricing Inquiry</option>
                        <option value="Pricing Adjustment">Pricing Adjustment</option>
                        <option value="Primary Contact / Signer Update">Primary Contact / Signer Update</option>
                        <option value="Process Equipment Invoice">Process Equipment Invoice</option>
                        <option value="Product Documentation Update/Request">Product Documentation Update/Request</option>
                        <option value="Product Idea/Feedback">Product Idea/Feedback</option>
                        <option value="Production Issue">Production Issue</option>
                        <option value="Product /Service Outage Inquiry">Product /Service Outage Inquiry</option>
                        <option value="Product Video Request">Product Video Request</option>
                        <option value="Reactivate Account">Reactivate Account</option>
                        <option value="Recall Requests">Recall Requests</option>
                        <option value="Refund Request">Refund Request</option>
                        <option value="Remove Account Updater">Remove Account Updater</option>
                        <option value="Remove AMEX">Remove AMEX</option>
                        <option value="Remove Credit Card">Remove Credit Card</option>
                        <option value="Remove from Hierarchy Tree">Remove from Hierarchy Tree</option>
                        <option value="Remove from Known Bad List">Remove from Known Bad List</option>
                        <option value="Remove Forte Protect">Remove Forte Protect</option>
                        <option value="Remove Forte Verify">Remove Forte Verify</option>
                        <option value="Remove Funding Hold">Remove Funding Hold</option>
                        <option value="Re-pop Batches">Re-pop Batches</option>
                        <option value="Reports Inquiry">Reports Inquiry</option>
                        <option value="Request for Check Back">Request for Check Back</option>
                        <option value="Request to Delete Invoice">Request to Delete Invoice</option>
                        <option value="Request to Delete Payment">Request to Delete Payment</option>
                        <option value="Request for Historical Processing Data">Request for Historical Processing Data</option>
                        <option value="Request for Invoice / Statement">Request for Invoice / Statement</option>
                        <option value="Request to Send Wire Transfer">Request to Send Wire Transfer</option>
                        <option value="Request to Waive Invoice">Request to Waive Invoice</option>
                        <option value="Research Funding /  Debit / Credit Corrections">Research Funding / Debit / Credit Corrections</option>
                        <option value="Research Invoice">Research Invoice</option>
                        <option value="Resend Approval Letters">Resend Approval Letters</option>
                        <option value="Reserve Release">Reserve Release</option>
                        <option value="Resubmit Payment">Resubmit Payment</option>
                        <option value="Retention Program">Retention Program</option>
                        <option value="Review Items">Review Items</option>
                        <option value="Reversal Request">Reversal Request</option>
                        <option value="RFP Support Request">RFP Support Request</option>
                        <option value="Risk Review - Other">Risk Review - Other</option>
                        <option value="Sales - New Merchant">Sales - New Merchant</option>
                        <option value="Sales - New Partner">Sales - New Partner</option>
                        <option value="Sales - Rate Review">Sales - Rate Review</option>
                        <option value="Settlement Bank Change">Settlement Bank Change</option>
                        <option value="Signer Change">Signer Change</option>
                        <option value="Stop Auto-Resubmit">Stop Auto-Resubmit</option>
                        <option value="Status Update">Status Update</option>
                        <option value="Suspicious Activity">Suspicious Activity</option>
                        <option value="Tax ID Update">Tax ID Update</option>
                        <option value="Tax Inquiry - 1099K">Tax Inquiry - 1099K</option>
                        <option value="Tax Inquiry - 1099 Misc">Tax Inquiry - 1099 Misc</option>
                        <option value="Tax Inquiry - W9">Tax Inquiry - W9</option>
                        <option value="Tax Inquiry - other">Tax Inquiry - other</option>
                        <option value="Tax Related Inquiry">Tax Related Inquiry</option>
                        <option value="Trace ID Request">Trace ID Request</option>
                        <option value="Training">Training</option>
                        <option value="Transactions">Transactions</option>
                        <option value="UAT Issue">UAT Issue</option>
                        <option value="UAT Request">UAT Request</option>
                        <option value="UI Enhancement Request">UI Enhancement Request</option>
                        <option value="Update Bank Account Error">Update Bank Account Error</option>
                        <option value="Update CC Credentials">Update CC Credentials</option>
                        <option value="Update Hold Day Error">Update Hold Day Error</option>
                        <option value="Update ISO ID">Update ISO ID</option>
                        <option value="Update ISO / Partner Vertical">Update ISO / Partner Vertical</option>
                        <option value="Update IVR Script">Update IVR Script</option>
                        <option value="Update Limit Error">Update Limit Error</option>
                        <option value="UX - Logo Request">UX - Logo Request</option>
                        <option value="Vendor Registration Form">Vendor Registration Form</option>
                        <option value="Verify Wire Received">Verify Wire Received</option>
                        <option value="Virtual Terminal Inquiry">Virtual Terminal Inquiry</option>
                        <option value="Virtual Terminal (VT) - Sandbox">Virtual Terminal (VT) - Sandbox</option>
                        <option value="Virtual Terminal Training">Virtual Terminal Training</option>
                        <option value="Wire Transfer - Other">Wire Transfer - Other</option -->
                        <option value="Incident">Incident</option>
                        <option value="Integration - Forte Checkout Question">Integration - Forte Checkout Question</option>
                        <option value="Integration - Forte CheckOut Issue">Integration - Forte CheckOut Issue</option>
                        <option value="Integration - REST Question">Integration - REST Question</option>
                        <option value="Integration - REST Issue">Integration - REST Issue</option>
                        <option value="Integration - Secure WebPay Question">Integration - Secure WebPay Question</option>
                        <option value="Integration - Secure WebPay Issue">Integration - Secure WebPay Issue</option>
                        <option value="Integration - Batch Repop">Integration - Batch Repop</option>
                        <option value="Integration - Web Services-AGI Issues">Integration - Web Services-AGI Issues</option>
                        <option value="Integration - Web Services-AGI Questions">Integration - Web Services-AGI Questions</option>
                        <option value="Integration - Mobile App Issues">Integration - Mobile App Issues</option>
                        <option value="Integration - Mobile App Questions">Integration - Mobile App Questions</option>
                        <option value="Integration - Bill Pay It Questions">Integration - Bill Pay It Questions</option>
                        <option value="Integration - Bill Pay It Issues">Integration - Bill Pay It Issues</option>
                        <option value="Integration - 3rd Party Software/Developer Questions">Integration - 3rd Party Software/Developer Questions</option>
                        <option value="Integration - 3rd Party Software/Developer Issues">Integration - 3rd Party Software/Developer Issues</option>
                        <option value="Integration - Batch Questions">Integration - Batch Questions</option>
                        <option value="Integration - Batch Issues">Integration - Batch Issues</option>
                        <option selected value="Integration - Integration Questions">Integration - Integration Questions</option>
                        <option value="Integration - Integration Issues">Integration - Integration Issues</option>
                        <option value="Integration - Client Data Transfer Import">Integration - Client Data Transfer Import</option>
                        <option value="Integration - Client Data Transfer Export">Integration - Client Data Transfer Export</option>
                        <option value="Integration - Consult">Integration - Consult</option>
                        <option value="Integration - IVR Question">Integration - IVR Question</option>
                        <option value="Integration - IVR Incident / Issue">Integration - IVR Incident / Issue</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">Caller's issue: </td>
                <td>
                    <textarea class="textbox" rows="4" cols="42" name="issue_question" /></textarea>
                </td>
            </tr>
            <tr>
                <td align="right">Resolution: </td>
                <td>
                    <textarea class="textbox" rows="4" cols="42" name="resolution" /></textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td align="right">
                    <input type="submit" class="button10" value="Submit ticket">
                </td>
            </tr>
        </table>
    </form>
</div>


        </center><br>
        <p></p>
        <p></p>
        <img border="0" src="images/spacer.gif" width="20" height="20">
      </div>
      <!-- Begin Page Menu -->
      <?php include 'include-menu.php'; ?>
    </div>
  </div>
  <?php include 'include-footer.php'; ?>
</div>

</body>

</html>
