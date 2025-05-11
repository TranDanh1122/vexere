<?php

namespace DreamTeam\AdminUser\Support;

use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAAuthenticator extends Authenticator
{
    protected function canPassWithoutCheckingOTP()
    {
        $option = getOption('googleAuthenticate', null, false);
        $isEnabled = $option['enabled'] ?? 0; // config $this->isEnabled()
        return
            !$isEnabled ||
            $this->noUserIsAuthenticated() ||
            !$this->isActivated() ||
            $this->twoFactorAuthStillValid();
    }

    protected function getGoogle2FASecretKey()
    {
        $secret = $this->getUser()->{$this->config('otp_secret_column')};
        if (is_null($secret) || empty($secret)) {
            return redirect('admin.login');
        }

        return $secret;
    }

}
