<?php

namespace App\Service\Stream\Enum;

enum EnumStreamTopic: string
{
    case TOPIC_USER = "TOPIC_USER";
    case TOPIC_SHOP = "TOPIC_SHOP";
    case TOPIC_WAVE = "TOPIC_WAVE";
    case TOPIC_ORDER = "TOPIC_ORDER";
}
