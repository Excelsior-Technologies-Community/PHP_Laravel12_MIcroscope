<?php

namespace App\Http\Controllers;

use App\Models\User;
class DemoController extends Controller
{
    public function index()
    {
        $user = new User();  

        return "Hello";
    }
}
