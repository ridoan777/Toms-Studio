<?php

namespace App\Enums;

enum CRUDActionEnums: string
{
	case CREATE = 'created';
	case UPDATE = 'updated';
	case ACTIVATE = 'activated';
	case DEACTIVATE = 'deactivated';
	case DELETE = 'deleted';
}