<?php

namespace App\Models;
enum AdminRoutesEnum: string
{
    case DASHBOARD = 'admin.dashboard';
    case EQUIPMENT = 'admin.equipment';
    case REQUESTS = 'admin.requests';
    case SETTINGS = 'settings';
}
