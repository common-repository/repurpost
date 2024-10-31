<?php
require_once plugin_dir_path(__FILE__) . '../../api/token/validate.php';

/* if (!repurpostWP_is_polylang_plugin_active()) {
  include_once('alerts/repurpost-admin-alert-polylang.php');
} */

if (!repurpostWP_is_yoast_plugin_active()) {
  include_once('alerts/repurpost-admin-alert-yoast.php');
}

function repurpostWP_get_my_endpoint()
{
  $rest_url = get_rest_url();
  return str_replace("/wp-json", "", $rest_url);
}

function repurpostWP_avanced_options()
{

  //$options = get_option( 'plugin_options' );
  //$checked = ( isset($options['checkbox_example']) && $options['checkbox_example'] == 1) ? 1 : 0;

  $referrer_status = get_option('repurpostWP_referrer_status');
  $checked = (isset($referrer_status) && $referrer_status == 1) ? 1 : 0;

  $html = '<label for="repurpostWP_avanced_options[repurpostWP_referrer_status]">';
  $html .= '<input type="checkbox" id="repurpostWP_referrer_status" name="repurpostWP_avanced_options[repurpostWP_referrer_status]" value="1"' . checked(1, $checked, false) . '/>';
  $html .= 'Activate referrer lock';
  $html .= '</label> <br>';

  echo $html;
}

function repurpostWP_get_options_user_access($blogusers, $permitted_users){

  $selectedAll = 'selected';
  $options = '';
  foreach ($blogusers as $user) {
    $selected = (!empty($permitted_users) && in_array($user->ID, $permitted_users)) ? 'selected' : '';
    $options .= '<option value="' . $user->ID . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
    if($selected === 'selected'){
      $selectedAll = '';
    }
  }
  
  return '<option value="all" ' . $selectedAll . '>All</option>'.$options;

}

function repurpostWP_select_user_access()
{
  $blogusers = get_users(['role__in' => ['administrator', 'editor', 'author', 'contributor']]);
  $permitted_users = get_option('repurpostWP_blogusers');

  $html = '<label for="repurpostWP_avanced_options[repurpostWP_blogusers]">Select the users who will publish from Repurpost:</label><br />';
  $html .= '<select multiple="multiple" name="repurpostWP_avanced_options[repurpostWP_blogusers][]" style="width:99%;max-width:25em;">';
  $html .= repurpostWP_get_options_user_access($blogusers, $permitted_users);
  $html .= '</select></p>';

  echo $html;
}
?>
<style type="text/css">
  button {
    height: 30px;
    padding: 0 10px;
    color: green;
    font-size: 14px;
  }
</style>

<script type="text/javascript">
    var clipboard = new ClipboardJS('.copy');
</script>

<div class="wrap">
  <h1>Repurpost Plugin</h1>

  <h2 class="title">General</h2>
  <table class="form-table">
    <tbody>
      <tr>
        <th><label for="endpoint">Your endpoint:</label></th>
        <td><input name="endpoint" id="endpoint" type="text" value="<?php echo repurpostWP_get_my_endpoint() ?>" class="regular-text" readonly />
          <button class="copy" data-clipborad-action="copy" data-clipboard-target="#endpoint" reference="endpoint">
            Copy Endpoint
          </button>
        </td>
      </tr>
      <tr>
        <th><label for="token">Your access token:</label></th>
        <td>
          <input name="token" id="token" type="text" value="<?php echo $token ?>" class="regular-text" readonly />
          <button class="copy" data-clipborad-action="copy" data-clipboard-target="#token" reference="token">
            Copy token
          </button></td>
      </tr>
    </tbody>
  </table>

  <form method="post" action="">
    <?php submit_button('Generate new token', 'secondary', 'generateToken'); ?>
  </form>

  <h2 class="title">Advanced Options <span style="font-size:12px;font-weight:400;text-decoration: underline; cursor: pointer" onClick="javascript:showAdvancedOptions()">Show</span></h2>
  
  <form method="post" action="" id="advanced-options-form" style="display:none">
    <div style="border-width: 0 0 0 4px;border-style: solid;padding:15px;margin-bottom: 23px;background: #f9f4d4;border-color: #f90;font-weight: 600;">
      <p>Only for advanced users. Contact Repurpost for more info. Thanks</p>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th><label for="repurpostWP_avanced_options[repurpostWP_referrer_status]">API Requests</label></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><span>API Requests</span></legend>
              <p><?php repurpostWP_avanced_options(); ?></p>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th><label for="repurpostWP_avanced_options[repurpostWP_blogusers]">User access</label></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><span>User access</span></legend>
              <?php repurpostWP_select_user_access(); ?>
            </fieldset>
        </tr>
      </tbody>
    </table>
    <?php submit_button('Save Advanced Options', 'primary', 'referrerLock'); ?>
  </form>

  <script>
    var advanced_options_form = document.getElementById('advanced-options-form');

    function showAdvancedOptions() {
      if (advanced_options_form.style.display === "none") {
          advanced_options_form.style.display = "block";
        } else {
          advanced_options_form.style.display = "none";
        }
    }
  </script>

</div>