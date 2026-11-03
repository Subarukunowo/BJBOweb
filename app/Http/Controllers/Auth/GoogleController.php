<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class GoogleController extends Controller
{
    protected SocialAuthController $socialAuthController;

    public function __construct(SocialAuthController $socialAuthController)
    {
        $this->socialAuthController = $socialAuthController;
    }

    public function redirectToGoogle()
    {
        return $this->socialAuthController->redirectToGoogle();
    }

    public function handleGoogleCallback()
    {
        return $this->socialAuthController->handleGoogleCallback();
    }
}
