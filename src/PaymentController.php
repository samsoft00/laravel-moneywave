<?php

namespace Samsoft\Moneywave;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Samsoft\Moneywave\Facades\Moneywave;

class PaymentController extends Controller
{
    public function getIndex(){

        return view('moneywave::index');
    }

    public function postMakePayment()
    {
        Moneywave::makePaymentRequest();
    }
}
