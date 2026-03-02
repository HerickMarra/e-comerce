<?php

if (!defined('ABSPATH')) {
  exit;
}

class EnviaMaisPartnerApi
{
  private $enviamais_partner_api_options;

  public function __construct()
  {
    add_action('admin_menu', array($this, 'enviamais_partner_api_add_plugin_page'));
    add_action('admin_init', array($this, 'enviamais_partner_api_page_init'));
  }

  public function enviamais_partner_api_add_plugin_page()
  {
    add_submenu_page(
      'woocommerce',
      'Envia Mais',
      'Envia Mais',
      'manage_options',
      'enviamais-partner-api',
      array($this, 'enviamais_partner_api_create_admin_page')
    );
  }

  public function enviamais_partner_api_create_admin_page()
  {
    $this->enviamais_partner_api_options = get_option('enviamais_partner_api_option_name'); ?>

    <div class="wrap">
      <h2>EnviaMais Partner Api</h2>
      <p>Configurações do Envia Mais</p>
      <?php settings_errors(); ?>

      <form method="post" action="options.php">
        <?php
        settings_fields('enviamais_partner_api_option_group');
        do_settings_sections('enviamais-partner-api-admin');
        submit_button();
        ?>
      </form>
    </div>
<?php }

  public function enviamais_partner_api_page_init()
  {
    register_setting(
      'enviamais_partner_api_option_group', // option_group
      'enviamais_partner_api_option_name', // option_name
      array($this, 'enviamais_partner_api_sanitize') // sanitize_callback
    );

    add_settings_section(
      'enviamais_partner_api_setting_section', // id
      'Settings', // title
      array($this, 'enviamais_partner_api_section_info'), // callback
      'enviamais-partner-api-admin' // page
    );

    add_settings_field(
      'token_0', // id
      'Token', // title
      array($this, 'token_0_callback'), // callback
      'enviamais-partner-api-admin', // page
      'enviamais_partner_api_setting_section' // section
    );

    add_settings_field(
      'dev_mode_1', // id
      'Dev Mode', // title
      array($this, 'dev_mode_1_callback'), // callback
      'enviamais-partner-api-admin', // page
      'enviamais_partner_api_setting_section' // section
    );
  }

  public function enviamais_partner_api_sanitize($input)
  {
    $sanitary_values = array();
    if (isset($input['token_0'])) {
      $sanitary_values['token_0'] = sanitize_text_field($input['token_0']);
    }

    if (isset($input['dev_mode_1'])) {
      $sanitary_values['dev_mode_1'] = $input['dev_mode_1'];
    }

    return $sanitary_values;
  }

  public function enviamais_partner_api_section_info()
  {
  }

  public function token_0_callback()
  {
    printf(
      '<input class="regular-text" type="text" name="enviamais_partner_api_option_name[token_0]" id="token_0" value="%s">',
      isset($this->enviamais_partner_api_options['token_0']) ? esc_attr($this->enviamais_partner_api_options['token_0']) : ''
    );
  }

  public function dev_mode_1_callback()
  {
    printf(
      '<input type="checkbox" name="enviamais_partner_api_option_name[dev_mode_1]" id="dev_mode_1" value="dev_mode_1" %s> <label for="dev_mode_1">Usa a API de homologação do Envia Mais</label>',
      (isset($this->enviamais_partner_api_options['dev_mode_1']) && $this->enviamais_partner_api_options['dev_mode_1'] === 'dev_mode_1') ? 'checked' : ''
    );
  }
}


if (is_admin()) {
  $enviamais_partner_api = new EnviaMaisPartnerApi();
}
