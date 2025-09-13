<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller
{
    // Di Laravel 11, kelas ini secara default kosong.
    // Ia tidak lagi extends BaseController.
    // Ini menyelesaikan konflik method middleware().
    use AuthorizesRequests;
}

