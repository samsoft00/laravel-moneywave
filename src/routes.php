<?php

Route::get('students/payment', 'Samsoft\Moneywave\PaymentController@getIndex');

Route::post('students/payment', 'Samsoft\Moneywave\PaymentController@postMakePayment');