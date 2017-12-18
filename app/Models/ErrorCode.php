<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorCode extends Model
{
    public static $RequiredEmail = 100;
    public static $ExistEmail = 101;
    public static $RequiredPassword = 102;
    public static $RequiredName = 103;
    public static $RequiredBirthday = 104;
    public static $RequiredAddress = 105;
    public static $RequiredGender = 106;
    public static $RequiredRating = 107;
    public static $RequiredDescription = 108;
    public static $MinDescription = 109;

    public static $InvalidAccount = 110;
    // public static $ = 111;

    public static $InvalidRePassword = 115;
    public static $InvalidOldPassword = 116;
    public static $InvalidCode = 117;

    public static $RequiredDeviceTokenType = 120;
    public static $RequiredDeviceToken = 121;
    public static $RequiredDeviceLanguage = 125;
    public static $RequiredWalletReceive = 126;
    public static $RequiredFromTo = 127;
    public static $RequiredCode = 128;


    public static $RequiredLatLngDistance = 130;

    public static $ExistMember = 150;

    public static $ExpiredCode = 160;

    public static $UnRegistered = 170;
    public static $UnEmail = 171;

    public static $IsComment = 180;

    public static $EmptyTransaction = 190;

    public static $SuccessRequest = 200;

    public static $RequireClientCustomer = 210;
    public static $RequirePhone = 211;
    public static $RequireTitle = 212;

    public static $InvalidToken = 400;
    public static $Unauthorized = 401;
    public static $Forbidden = 403;
    public static $RequireToken = 405;
    public static $ExpiredToken = 406;

    public static $LoginTimeout = 440;

    public static $ServerError = 500;
    public static $Error = 501;
    public static $BadGateway = 502;
}
