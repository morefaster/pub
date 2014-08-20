<?php

/**
 * @package base
 */
class api_Twilio
{
	public static function NewInstance()
	{
		return new self();
	}

	public function Init($aPaths, $oHttp)
	{
		\CApi::Log('twilio_xml_start');

		\CApi::LogObject($aPaths);
		\CApi::LogObject($_REQUEST);

		/* @var $oApiIntegrator \CApiIntegratorManager */
		$oApiIntegrator = \CApi::Manager('integrator');
		$oApiUsers = \CApi::Manager('users');

		$oAccount = $oApiIntegrator->GetLogginedDefaultAccount();
		$bTwilioAllowUser = $oAccount->User->TwilioEnable;

		$bDirection = $oHttp->GetRequest('Direction') === 'inbound' ? true : false;
		$sDigits = $oHttp->GetRequest('Digits');

		$sTenantId = isset($aPaths[1]) ? $aPaths[1] : null;

		$bTwilioAllowTenant = false;
		$sTwilioPhoneNumber = '';

		if (is_numeric($sTenantId))
		{
			$oApiTenants = \CApi::Manager('tenants');
			$oTenant = $oApiTenants ? $oApiTenants->GetTenantById($sTenantId) : null;

			if ($oTenant)
			{
				$bTwilioAllowTenant = $oTenant->TwilioAllow && $oTenant->TwilioAllowConfiguration; // TODO consider user enable twilio checkbox
				$sTwilioPhoneNumber = $oTenant->TwilioPhoneNumber;
			}
		}
		else
		{
			$bTwilioAllowTenant = true; // TODO
		}

		@header('Content-type: text/xml');

		$aResult = array('<?xml version="1.0" encoding="UTF-8"?>');
		$aResult[] = '<Response>';

		if ($bTwilioAllowTenant)
		{
			if ($bDirection) // inbound
			{
				// TODO
				if (!$sDigits)
				{
					$aResult[] = '<Gather timeout="10" numDigits="4">';
					$aResult[] = '<Say>Please enter the extension number or stay on the line to talk to an operator</Say>';
					$aResult[] = '</Gather>';
					$aResult[] = '<Say>You will be connected with an operator</Say>';
					$aResult[] = '<Dial><Client>TwilioAftId_'.$sTenantId.'_0</Client></Dial>';
				}
				else
				{
					//$aResult[] = '<Dial><Client>TwilioAftId_'.$sTenantId.'_'.$sDigits.'</Client></Dial>';
					$aResult[] = '<Dial><Client>TwilioAftId_'.$sDigits.'</Client></Dial>';
				}
			}
			else //Outbound
			{
				/* @var $oApiCapability \CApiCapabilityManager */
				$oApiCapability = \CApi::Manager('capability');

				if ($oApiCapability->IsTwilioSupported($oAccount))
				{
					$sPhoneNumber = $oHttp->GetRequest('PhoneNumber');
					//$aResult[] = '<Say>Call to phone number '.$sPhoneNumber.'</Say>';
					//$aResult[] = '<Dial callerId="17064030887">'.$sPhoneNumber.'</Dial>';
					//$aResult[] = '<Dial callerId="17064030887"><Client>TwilioAftId_'.$sPhoneNumber.'</Client></Dial>';
					//$aResult[] = '<Dial callerId="'.$sPhoneNumber.'"><Client>TwilioAftId_'.$sPhoneNumber.'</Client></Dial>';

					if (preg_match("/^[\d\+\-\(\) ]+$/", $sPhoneNumber) && strlen($sPhoneNumber) > 10) {
						$aResult[] = '<Dial callerId="'.$sTwilioPhoneNumber.'">'.$sPhoneNumber.'</Dial>';
					} else {
						$aResult[] = '<Dial callerId="'.$sPhoneNumber.'"><Client>TwilioAftId_'.$sPhoneNumber.'</Client></Dial>';
					}
				}
			}
		} else {
			$aResult[] = '<Say>This functionality doesn\'t allowed</Say>';
		}

		$aResult[] = '</Response>';

		//$sResult = implode("\r\n", $aResult);

		\CApi::Log($aResult);
		\CApi::Log('twilio_xml_end');

		return implode('', $aResult);
	}
}
