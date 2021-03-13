<?php

namespace Pnuggz\LaravelRestrictedUrl\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const STORAGE_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const STORAGE_DATE_FORMAT      = 'Y-m-d';
    const STORAGE_TIME_FORMAT      = 'H:i:s';

    const DISPLAY_DATE_TIME_FORMAT      = 'd/m/Y g:i A';
    const DISPLAY_DATE_LONG_TIME_FORMAT = 'l, j M g:i A';
    const DISPLAY_DATE_FORMAT           = 'd/m/Y';
    const DISPLAY_DATE_LONG_FORMAT      = 'l, j M';
    const DISPLAY_TIME_FORMAT           = 'g:i A';

    const DISPLAY_FORMAT_OPTIONS = [
        self::DISPLAY_DATE_TIME_FORMAT,
        self::DISPLAY_DATE_LONG_TIME_FORMAT,
        self::DISPLAY_DATE_FORMAT,
        self::DISPLAY_TIME_FORMAT
    ];

    const FILE_DATE_TIME_FORMAT = 'Ymd_Hi';

    public function storageDateTimeFormat()
    {
        return BaseModel::STORAGE_DATE_TIME_FORMAT;
    }

    public function storageDateFormat()
    {
        return BaseModel::STORAGE_DATE_FORMAT;
    }

    public function storageTimeFormat()
    {
        return BaseModel::STORAGE_TIME_FORMAT;
    }

    public function displayDateTimeFormat()
    {
        return BaseModel::DISPLAY_DATE_TIME_FORMAT;
    }

    public function displayDateLongFormat()
    {
        return BaseModel::DISPLAY_DATE_LONG_FORMAT;
    }

    public function displayDateFormat()
    {
        return BaseModel::DISPLAY_DATE_FORMAT;
    }

    public function displayTimeFormat()
    {
        return BaseModel::DISPLAY_TIME_FORMAT;
    }
}
