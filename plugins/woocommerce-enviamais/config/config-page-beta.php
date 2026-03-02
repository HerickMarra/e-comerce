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
			<div class="envia-mais-header">
				<img src="http://app.enviamais.com.br/img/enviamais-logo.png" style="width: 40%; height: auto;" />
			</div>

			<?php if ($this->getOptionsValidated('validToken') && !$this->getOptionsValidated('user')->complete) : ?>
				<table class="envia-mais envia-mais-warning">
					<thead>
						<tr>
							<th>Você precisa completar seus dados na plataforma do Envia Mais</th>
						</tr>
					</thead>
				</table>
			<?php endif ?>



			<?php settings_errors(); ?>

			<?php echo (print_r($this->getOptionsValidated('user'), true)); ?>


			<form method="post" action="options.php">
				<?php
				settings_fields('enviamais_partner_api_option_group');
				do_settings_sections('enviamais-partner-api-admin');
				?>

				<?php if ($this->getOptionsValidated('validToken') == 1) : ?>
					<table class="envia-mais">
						<thead>
							<tr>
								<th><?php echo ($this->getOptionsValidated('user')->company->name) ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ($this->getOptionsValidated('user')->complete) : ?>
								<tr>
									<td>Email Empresarial:</td>
									<td><?php echo ($this->getOptionsValidated('user')->company->email) ?></td>
								</tr>
								<tr>
									<td>CPF/CNPJ:</td>
									<td><?php echo ($this->getOptionsValidated('user')->company->doc) ?></td>
								</tr>
								<tr>
									<td>Fone:</td>
									<td><?php echo ($this->getOptionsValidated('user')->company->landline_phone) ?></td>
								</tr>

								<tr>
									<td>Cep:</td>
									<td><?php echo ($this->getOptionsValidated('user')->company->zip_code) ?></td>
								</tr>
								<tr>
									<td>Endereço:</td>
									<td>
										<span>
											<?php echo ($this->getOptionsValidated('user')->company->address) ?>, <?php echo ($this->getOptionsValidated('user')->company->neighborhood) ?>
										</span>
										</br>
										<span>
											<?php echo ($this->getOptionsValidated('user')->company->city->name) ?>, <?php echo ($this->getOptionsValidated('user')->company->city->state->name) ?> - <?php echo ($this->getOptionsValidated('user')->company->city->state->acronym) ?>
										</span>
									</td>
								</tr>
							<?php else : ?>
								<tr>
									<td>Cadastro Completo:</td>
									<td class="envia-mais-warning">Não</td>
								</tr>
							<?php endif ?>
						</tbody>
					</table>

				<?php elseif ($this->getOptionsValidated('validToken') == 0) : ?>
					<table class="envia-mais envia-mais-error">
						<thead>
							<tr>
								<th>Erro: Token Inválido</th>
							</tr>
						</thead>
					</table>
				<?php endif ?>


				<?php submit_button() ?>

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
			'Configurações', // title
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

	private function getOptionsValidated($name)
	{
		$options = get_option('enviamais_validated');

		if (isset($options[$name])) {
			return $options[$name];
		}

		return null;
	}

	public function enviamais_partner_api_section_info()
	{
	}

	public function token_0_callback()
	{
		printf(
			'<input class="regular-text envia-mais-input" 
			type="text" name="enviamais_partner_api_option_name[token_0]" 
			id="token_0" value="%s"
			>',
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


add_action('updated_option', function ($option_name, $old_value, $value) {
	if ($option_name == 'enviamais_validated') {
		error_log(print_r([$option_name, $old_value, $value], true));
	}

	if ($option_name == 'enviamais_partner_api_option_name') {
		error_log(print_r([$option_name, $old_value, $value], true));
		$valid = ['user' => null, 'validToken' => false];
		$api = new EnviaMaisAPI();

		try {
			$request = ($api->getUserToken());
			if (isset($request->error)) {
				$value['validToken'] = false;
			} else {
				if (isset($request->data)) {
					$value['user'] = $request->data;
					$value['validToken'] = true;
				} else {
					$value['validToken'] = false;
				}
			}
		} catch (\Throwable $th) {
			$value['validToken'] = false;
		}

		update_option('enviamais_validated', $value);
	}
}, 10, 3);

?>


<style>
	@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

	.envia-mais {
		width: 100%;
		margin-top: 2vh;
		margin-bottom: 2vh;
		border-right-color: #ffffff;
		font-size: 18px;
		font-family: 'Inter', sans-serif;
		border-collapse: collapse;
	}

	.envia-mais-button {
		background-color: #71CC22 !important;
		font-size: 18px !important;
		color: #fff !important;
		border: 0px solid transparent !important;
		font-family: 'Inter', sans-serif !important;
		border-radius: 0px !important;

	}

	.envia-mais th,
	td {
		padding: 16px 24px;
		text-align: left;
	}

	.envia-mais th {
		background-color: #71CC22;
		color: #fff;
	}

	.envia-mais-error th {
		background-color: #ee1111;
		color: #fff;
	}

	.envia-mais-warning th {
		background-color: #e3a21a;
		color: #fff;
	}

	.envia-mais tbody tr:nth-child(odd) {
		background-color: #f8f9fa;
	}


	.envia-mais tbody tr:nth-child(even) {
		background-color: #e9ecef;
	}

	.envia-mais-input {
		width: 100% !important;
		border: 0px !important;
		padding: 10px 5px !important;
	}

	.envia-mais-header {
		display: flex;
		flex-flow: row wrap;
	}
</style>