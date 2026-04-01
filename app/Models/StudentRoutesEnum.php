<?php

namespace App\Models;
enum StudentRoutesEnum: string
{
    case DASHBOARD = 'student.dashboard';
    case REQUESTS = 'student.requests';
    case SETTINGS = 'settings';
}
