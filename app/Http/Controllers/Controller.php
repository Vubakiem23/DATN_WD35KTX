<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function return_back_error($message)
    {
        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }

    protected function return_route_error($route_name = null, $message)
    {
        if ($route_name) {
            return redirect()->route($route_name)
                ->with('error', $message)
                ->withInput();
        }

        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }

    protected function return_success($route_name = null, $message)
    {
        if ($route_name) {
            return redirect()->route($route_name)
                ->with('success', $message)
                ->withInput();
        }

        return redirect()->back()
            ->with('success', $message)
            ->withInput();
    }
}
