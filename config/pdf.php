<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('storage/app/mpdf'),
	'pdf_a'                 => false,
	'pdf_a_auto'            => false,
	'icc_profile_path'      => '',
	'font_path' => base_path('resources/fonts/'),
	'font_data' => [
		"Khmeros" => [/* Khmer */
			'R' => "KhmerOS.ttf",
			'useOTL' => 0xFF,
		]
	]
];
