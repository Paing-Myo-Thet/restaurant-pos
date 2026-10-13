<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    public function index()
    {
        return 'Welcome to, Hello Controller';
    }

    public function restaurant()
    {
        $name = 'My Restaurant POS';
        return view('restaurant', [
            'name' => $name,
        ]);
    }
}
