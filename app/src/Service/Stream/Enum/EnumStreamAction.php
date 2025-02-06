<?php

namespace App\Service\Stream\Enum;

enum EnumStreamAction: string
{
    case APPEND = "append";
    case REPLACE = "replace";
}
