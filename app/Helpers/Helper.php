<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Models\User;

class Helper
{
    public static function getStaffName($id)
    {
        $user =User::find($id);
        return  $user->name??"-";
    }
}
