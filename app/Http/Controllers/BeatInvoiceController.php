<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BeatInvoiceController extends Controller
{
    public function index()
    {
        return view('beat-invoices.index');
    }
}
