<?php
    $wl = getWhiteLabel();
    $site_name = '';
    $site_footer = '';
    $site_title = '';
    $site_link = '';
    $site_logo = '';
    $site_favicon = '';
    if($wl){
        if($wl->site_name){
            $site_name = $wl->site_name;
        }
        if($wl->site_footer){
            $site_footer = $wl->site_footer;
        }
        if($wl->site_title){
            $site_title = $wl->site_title;
        }
        if(isset($wl->site_link) && $wl->site_link){
            $site_link = $wl->site_link;
        }
        if($wl->site_logo){
            $site_logo = base_url()."uploads/site_settings/".$wl->site_logo;
        }
        if($wl->site_favicon){
            $site_favicon = base_url()."uploads/site_settings/".$wl->site_favicon;
        }
    }
    $company_info = getCompanyInfo();
    $company_short_name =  $company_info->short_name;
    // Get Outlet Information
    $outlet_id = $this->session->userdata('outlet_id');
    $outlet_info = getOutletInfoById($outlet_id);
    $businessname = $company_info->business_name;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        /* Gmail-specific CSS */
        .rectangle-parent {
            background-color: #143157;
            padding-top: 15px;
            padding-bottom: 15px;
            margin: 0 auto;
        }
        .frame-child {
            display: none;
        }
        /* Hide the frame */
        .welcome-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .welcome-to {
            margin: 0;
            color: #ffffff;
        }
        .welcome-container {
            float: left;
        }
        .content-email-icon {
            height: auto;
            width: 100%;
            max-width: 82.6px;
            float: right;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f7f7f8;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0"
                    style="margin: auto;">
                    <tr>
                        <td>
                            <div class="rectangle-parent">
                                <div class="frame-child"
                                    style="height: 155px; width: 578px; position: relative; background-color: #143157; max-width: 100%; z-index: 0;">
                                </div>
                                <div class="bg"
                                    style="height: 100%; width: 100%; position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 1; margin: 0;">
                                </div>
                                <div class="welcome-wrapper">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                        border="0">
                                        <tr>
                                            <td align="left" style="padding-left: 20px;">
                                                <div class="welcome-container">
                                                    <p class="welcome-to" style="font-style: normal;font-weight: 600;font-size: 32px;line-height: 120%;"><?php echo escape_output($businessname);?></p>
                                                    <p class="welcome-to" style="font-style: normal;font-weight: 600;font-size: 16px;line-height: 120%;"><?php echo escape_output($outlet_info->outlet_name);?></p>
                                                </div>
                                            </td>
                                            <td align="right" style="padding-right: 20px;">
                                                <img class="content-email-icon" alt="company-logo" src="<?php echo $site_logo;?>" width="82.6">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 20px 20px 0px 20px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="middle">
                                        <h1 style="font-size: 28px; color: #121a26; margin: 0;">"Unlock Your Shopping Potential: Explore Our Exclusive Product Offerings Today!"</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding: 10px 0;">
                                        <p style="font-size: 16px; color: #494f5a;">
                                          <b> Dear <?php echo escape_output($customer_name);?>,</b> <br>
                                          I hope this message finds you well. I am reaching out from <?php echo escape_output($company_info->business_name) ;?>, and I would like to express our interest in purchasing products from your esteemed company. We have thoroughly reviewed your product offerings and are impressed with the quality and range you provide.

                                          To facilitate our purchasing process, we kindly request a formal quotation for the following products.

                                          <b>Find the attachment below</b>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 20px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 5px; margin-top: 0px">Contact Information</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <p style="font-size: 14px; color: #494f5a; margin:0px;"><b>Phone:</b> <?php echo escape_output($outlet_info->phone);?></p>
                                        <p style="font-size: 14px; color: #494f5a; margin:0px;"><b>Email:</b> <?php echo escape_output($outlet_info->email);?></p>
                                        <p style="font-size: 14px; color: #494f5a; margin:0px;"><b>Address:</b> <?php echo escape_output($outlet_info->address);?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <p style="font-size: 14px; color: #494f5a;">
                                        <?php echo escape_output($site_footer); ?> <?php echo lang('by');?> 
                                            <a target="_blank" href="<?php echo escape_output($site_link); ?>"><?php echo escape_output($site_title); ?></a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
