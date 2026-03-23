<?php
if( function_exists('acf_add_local_field_group') ):
	
// wordpress settings privacy page
$privacy_page_id = (int) get_option('wp_page_for_privacy_policy');

acf_add_local_field_group(array(
	'key' => 'group_nos_popup_settings',
	'title' => 'Generelle Popup Einstellungen',
	'fields' => array(

		// Page Select (only Pages)
		// array(
		// 	'key' => 'field_nos_privacy_page',
		// 	'label' => 'Seite für Datenschutzerklärung auswählen',
		// 	'name' => 'nos_popups_data_privacy_page',
		// 	'type' => 'post_object',
		// 	'post_type' => array('page'),
		// 	'return_format' => 'id', // returns page ID
		// 	'ui' => 1,
		// 	'required' => 1,
		// 	'default_value' => $privacy_page_id,
		// ),

		// Radio Button (Simple / Extended)
		array(
			'key' => 'field_nos_privacy_variation',
			'label' => 'Variante auswählen',
			'name' => 'nos_popus_privacy_text_variation',
			'type' => 'radio',
			'choices' => array(
				'simple'   => 'Einfach',
				'extended' => 'Erweitert',
			),
			'default_value' => 'simple',
			'layout' => 'horizontal',
		),

		// WYSIWYG: Simple
		array(
			'key' => 'field_nos_privacy_text_simple',
			'label' => 'Textbaustein Einfach',
			'name' => 'nos_popus_privacy_text_simple',
			'type' => 'wysiwyg',
			'default_value' => '<h3>Verwendung von localStorage und sessionStorage</h3><p>Wir verwenden die Webspeichertechnologien localStorage und/oder sessionStorage, die von Ihrem Browser bereitgestellt werden. Diese dienen dazu, bestimmte Einstellungen oder Aktionen auf unserer Website zwischenzuspeichern. Konkret nutzen wir diese Technologien, um zu verhindern, dass ein bereits gesehenes Hinweisfenster (z. B. ein Popup) mehrfach angezeigt wird.<br>Dabei werden keine personenbezogenen Daten gespeichert oder verarbeitet. Die Informationen im localStorage bzw. sessionStorage verbleiben ausschließlich auf Ihrem Endgerät und werden nicht an unsere Server übermittelt. Sie können die gespeicherten Daten jederzeit über die Einstellungen Ihres Browsers löschen.</p>',
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_nos_privacy_variation',
						'operator' => '==',
						'value' => 'simple',
					),
				),
			),
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 0,
		),

		// WYSIWYG: Extended
		array(
			'key' => 'field_nos_privacy_text_extended',
			'label' => 'Textbaustein Erweitert',
			'name' => 'nos_popus_privacy_text_extended',
			'type' => 'wysiwyg',
			'default_value' => '<h3>Verwendung von localStorage und sessionStorage</h3><p>Um die Nutzererfahrung auf unserer Website zu verbessern, setzen wir Technologien wie localStorage und sessionStorage ein. Dabei handelt es sich um Speichermechanismen Ihres Browsers, die Daten auf Ihrem Endgerät speichern.</p><p><strong>Zweck der Speicherung:</strong><br>Wir nutzen diese Technologien ausschließlich, um bestimmte Seiteneinstellungen und -interaktionen vorübergehend zwischenzuspeichern. Konkret setzen wir sie ein, um zu verhindern, dass Ihnen ein Hinweisfenster (Popup), das Sie bereits gesehen und geschlossen haben, erneut angezeigt wird.</p><p><strong>Gespeicherte Daten:</strong><br>Es werden keine personenbezogenen Daten oder trackingbezogenen Informationen gespeichert. Die Technologie legt lediglich einen einfachen Schalter (z.B. popup_seen = true) ab, der signalisiert, dass das Popup bereits angezeigt wurde.</p><p><strong>Rechtsgrundlage:</strong><br>Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO (Berechtigtes Interesse). Unser berechtigtes Interesse liegt in der Optimierung der Benutzerfreundlichkeit unserer Website und der Vermeidung lästiger Wiederholungsanzeigen.</p><p><strong>Speicherdauer:</strong><br><strong>sessionStorage:</strong> Die darin gespeicherten Daten werden automatisch gelöscht, sobald Sie Ihren Browser-Tab oder -Fenster schließen.<br><strong>localStorage:</strong> Die Daten verbleiben auf Ihrem Endgerät, bis Sie sie manuell in Ihren Browsereinstellungen löschen. Eine automatische Löschung nach einem bestimmten Zeitraum findet nicht statt.</p><p><strong>Weitergabe von Daten:</strong><br>Die gespeicherten Informationen verbleiben ausschließlich auf Ihrem Endgerät und werden nicht an unsere Server übermittelt oder an Dritte weitergegeben.</p><p><strong>Ihre Rechte und Kontrollmöglichkeiten:</strong><br>Sie haben die volle Kontrolle über diese Speicherfunktion:<br>Sie können die gespeicherten Daten jederzeit über die Einstellungen Ihres Browsers einsehen und löschen.</p><p>Sie können die Nutzung von localStorage und sessionStorage vollständig in Ihrem Browser deaktivieren. Bitte beachten Sie, dass dies die Funktionalität unserer Website einschränken kann (z.B. indem Popups bei jedem Seitenaufruf erneut erscheinen).</p>',
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_nos_privacy_variation',
						'operator' => '==',
						'value' => 'extended',
					),
				),
			),
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'nos-general-popup-settings',
			),
		),
	),
));

endif;