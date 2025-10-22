<?php

namespace App\Controllers;

class SubscriptionEnded extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays the login view.
     *
     * @return mixed
     */
    public function index()
    {
        return view('subscriptionEnded');
    }
}
