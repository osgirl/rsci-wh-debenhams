<?php

include_once("jda_modules.php");

$module = new jdaModules();

$flagCarton = $module->maintainingCartonHeader();
if($flagCarton)
{
	$flagPallet = $module->maintainingPalletHeader();
	if($flagPallet)
	{
		$flagLoad = $module->maintainingLoadHeader();
		if($flagLoad)
		{
			$flagCartonToPallet = $module->assigningCartonToPallet();
			if($flagCartonToPallet)
			{
				$module->loading();
			}
		}
	}
}