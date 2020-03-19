<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function formatValidationErrors(Validator $validator)
    {
        return redirect('/');
    }
}
