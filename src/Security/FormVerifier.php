<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\Security;

use ReCaptcha\ReCaptcha;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class FormVerifier
{
    public function recaptchaVerifier(string $hostname, string $expectedAction, string $recaptchaResponse, string $clientIp): array
    { 
        $recaptcha = new ReCaptcha($_ENV['GOOGLE_RECAPTCHA_SECRET']);
        $response = $recaptcha->setExpectedAction($expectedAction)
            ->setScoreThreshold(0.5)
            ->verify($recaptchaResponse, $clientIp);
        
        if ($response->isSuccess() && $response->getHostname() == $hostname && $response->getAction() == $expectedAction) {
            return ['status' => true, 'message' => ''];
        } else {
            return ['status' => false, 'message' => 'verifier.form.recaptcha'];
        }
    }
    
    public function honeyPotVerifier(array $honeyPots): array
    {
        foreach ($honeyPots as $honeyPot) {
            if (!empty($honeyPot)) {
                return ['status' => false, 'message' => 'verifier.form.honeypot'];
            }
        }
        
        return ['status' => true, 'message' => ''];
    }
    
    public function timestampVerifier(int $timestamp): array
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $nowTimestamp = $now->getTimestamp() * 1000;
        $comparision = $nowTimestamp - $timestamp;
        
        //time in miliseconds - 86400000 is 24h
        if ($comparision < 4000 || $comparision > 86400000) {
            return ['status' => false, 'message' => 'verifier.form.timestamp'];
        }
        
        return ['status' => true, 'message' => ''];
    }
}
