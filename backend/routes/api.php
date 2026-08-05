<?php

use Illuminate\Support\Facades\Route;

require base_path('app/Domains/Authentication/Routes/api.php');
require base_path('app/Domains/User/Routes/api.php');
require base_path('app/Domains/Subscription/Routes/api.php');
require __DIR__.'/notification.php';
require __DIR__.'/dashboard.php';