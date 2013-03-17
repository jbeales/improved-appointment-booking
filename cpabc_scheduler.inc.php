<?php if ( !defined('CPABC_AUTH_INCLUDE') ) { echo 'Direct access not allowed.'; exit; } ?>
<form class="cpp_form" name="FormEdit" action="<?php get_site_url(); ?>" method="post" onsubmit="return doValidate(this);">
<input name="cpabc_appointments_post" type="hidden" id="1" />
<div <?php if (count($myrows) < 2) echo 'style="display:none"'; ?>>
  <?php _e("Calendar").":"; ?><br />
  <select name="cpabc_item" id="cpabc_item" onchange="cpabc_updateItem()"><?php echo $calendar_items; ?></select><br /><br />
</div>
<?php
  _e("Select date and time").":";
  foreach ($myrows as $item)
      echo '<div id="calarea_'.$item->id.'" style="display:none"><input name="selDaycal'.$item->id.'" type="hidden" id="selDaycal'.$item->id.'" /><input name="selMonthcal'.$item->id.'" type="hidden" id="selMonthcal'.$item->id.'" /><input name="selYearcal'.$item->id.'" type="hidden" id="selYearcal'.$item->id.'" /><input name="selHourcal'.$item->id.'" type="hidden" id="selHourcal'.$item->id.'" /><input name="selMinutecal'.$item->id.'" type="hidden" id="selMinutecal'.$item->id.'" /><div class="appContainer"><div style="z-index:1000;" class="appContainer2"><div id="cal'.$item->id.'Container"></div></div></div> <div style="clear:both;"></div></div>';
?>
<div id="selddiv" style="font-weight: bold;margin-top:5px;padding:3px;"></div>
<script type="text/javascript">
 cpabc_do_init(<?php echo $myrows[0]->id; ?>);
 setInterval('updatedate()',200);
 function doValidate(form)
 {
    if (form.phone.value == '')
    {
        alert('<?php _e('Please enter a valid phone number'); ?>.');
        return false;
    }
    if (form.email.value == '')
    {
        alert('<?php _e('Please enter a valid email address'); ?>.');
        return false;
    }
    if (form.name.value == '')
    {
        alert('<?php _e('Please write your name'); ?>.');
        return false;
    }
    if (document.getElementById("selDaycal"+cpabc_current_calendar_item).value == '')
    {
        alert('<?php _e('Please select date and time'); ?>.');
        return false;
    }
    <?php if (cpabc_get_option('dexcv_enable_captcha', CPABC_TDEAPP_DEFAULT_dexcv_enable_captcha) != 'false') { ?> if (form.hdcaptcha.value == '')
    {
        alert('<?php _e('Please enter the captcha verification code'); ?>.');
        return false;
    }        
    // check captcha
    $dexQuery = jQuery.noConflict();
    var result = $dexQuery.ajax({
        type: "GET",
        url: "<?php echo cpabc_appointment_get_site_url(); ?>?hdcaptcha="+form.hdcaptcha.value,
        async: false,
    }).responseText;
    if (result == "captchafailed")
    {
        $dexQuery("#captchaimg").attr('src', $dexQuery("#captchaimg").attr('src')+'&'+Date());
        alert('Incorrect captcha code. Please try again.');
        return false;
    }
    else <?php } ?>
        return true;
 }
</script>



<p><label for="cpabc_phone"><?php _e( 'Your phone number:', 'cpabc' ); ?></label>
<input type="text" name="phone" value=""></p>

<p><label for="cpabc_name"><?php _e( 'Your name:', 'cpabc' ); ?></label>
<input type="text" name="name" value=""></p>

<p><label for="cpabc_email"><?php _e( 'Your email:', 'cpabc' ); ?></label>
<input type="text" name="email" value=""></p>

<p><label for="cpabc_question"><?php _e( 'Comments/Questions:', 'cpabc' ); ?></label>
<textarea name="question" cols="40" rows="8"></textarea></p>

<?php      
if ( count( $codes ) ) {
  echo '<p><label for="cpabc_couponcode">' . __( 'Coupon code (optional):', 'cpabc' ) . '</label>';
  echo '<input type="text" name="couponcode" value=""></p>';
}

if ( $cpabc_buffer != '' ) {
  echo '<p><label for="cpabc_services">' . __( 'Service: ', 'cpabc' ) . '</label>';
  echo '<select name="services">'.$cpabc_buffer.'</select></p>';
}

?>

<?php do_action('cpabc_end_of_form'); ?>

<?php if (cpabc_get_option('dexcv_enable_captcha', CPABC_TDEAPP_DEFAULT_dexcv_enable_captcha) != 'false') { ?>
  <p>
    <?php _e( 'Please enter the security code:', 'cpabc' ); ?><br />
  <img src="<?php echo cpabc_appointment_get_site_url().'/?cpabc_app=captcha&width='.cpabc_get_option('dexcv_width', CPABC_TDEAPP_DEFAULT_dexcv_width).'&height='.cpabc_get_option('dexcv_height', CPABC_TDEAPP_DEFAULT_dexcv_height).'&letter_count='.cpabc_get_option('dexcv_chars', CPABC_TDEAPP_DEFAULT_dexcv_chars).'&min_size='.cpabc_get_option('dexcv_min_font_size', CPABC_TDEAPP_DEFAULT_dexcv_min_font_size).'&max_size='.cpabc_get_option('dexcv_max_font_size', CPABC_TDEAPP_DEFAULT_dexcv_max_font_size).'&noise='.cpabc_get_option('dexcv_noise', CPABC_TDEAPP_DEFAULT_dexcv_noise).'&noiselength='.cpabc_get_option('dexcv_noise_length', CPABC_TDEAPP_DEFAULT_dexcv_noise_length).'&bcolor='.cpabc_get_option('dexcv_background', CPABC_TDEAPP_DEFAULT_dexcv_background).'&border='.cpabc_get_option('dexcv_border', CPABC_TDEAPP_DEFAULT_dexcv_border).'&font='.cpabc_get_option('dexcv_font', CPABC_TDEAPP_DEFAULT_dexcv_font); ?>"  id="captchaimg" alt="security code" border="0"  />
  </p>
  <label for="hdcaptcha"><?php _e( 'Security Code (lowercase letters):', 'cpabc' ); ?></label>
  <div class="dfield">
  <input type="text" size="20" name="hdcaptcha" id="hdcaptcha" value="" />
  <div class="error message" id="hdcaptcha_error" generated="true" style="display:none;position: absolute; left: 0px; top: 25px;"></div>
  </div>
  <br />
<?php } ?>

<input type="submit" name="subbtn" value="<?php _e( 'Continue', 'cpabc' ); ?>">
</form>


