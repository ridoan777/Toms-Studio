<?php

namespace App\Enums;

enum NotifyChannelEnums: string
{
	case PUSH = 'push';
	case IN_APP = 'in_app';
	case EMAIL = 'email';
}