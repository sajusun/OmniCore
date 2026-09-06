<?php

namespace App\Modules\Post\Enums;

enum PostType: string
{
    case POST = 'post';
    case SHARED = 'shared';
}
