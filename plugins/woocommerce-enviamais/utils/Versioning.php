<?php


function envia_mais_versioning_outdated_verify() {
	
	if (!is_admin()) {
		return; 
	}
	
	$currentVersion = '1.1.5';

	$api = new EnviaMaisAPI();

	$response = $api->getCurrentVersion();

	if(version_compare($currentVersion, $response->version) >= 0){
		return;
	}

	$class = 'notice notice-error';
	$error = 'Sua versão do plugin Woocommerce EnviaMais está desatualizada.';

	$message = __( $error, 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s <a href="https://app.enviamais.com.br/developer/woocommerce" target="_blank">Clique aqui para obter a versão.</a></div></p>', esc_attr( $class ), esc_html( $message ), esc_html($link) );
}

add_action( 'admin_notices', 'envia_mais_versioning_outdated_verify' );