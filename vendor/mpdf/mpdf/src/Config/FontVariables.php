<?php

namespace Mpdf\Config;

class FontVariables
{

	private $defaults;

	public function __construct()
	{
		$this->defaults = [

			
			'fontdata' => [
				"inter" => [
					'R' => "Inter-Regular.ttf",
					'B' => "Inter-Bold.ttf",
					'I' => "Inter-Italic.ttf",
					'BI' => "Inter-BoldItalic.ttf",
					// เพิ่ม useOTL/useKashida ถ้าต้องการ
				],
			],
			'backupSubsFont' => ['inter'],
			'sans_fonts' => ['inter', 'sans', 'sans-serif'],
			

		];
	}

	public function getDefaults()
	{
		return $this->defaults;
	}
}
