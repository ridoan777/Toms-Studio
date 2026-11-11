<?php

namespace App\Domains\Users\Enums;

enum ApiAuthEnums: string
{
	case LOGIN = 'login';
	case REGISTER = 'register';
	// --------------------
	case PASSWORD_CHANGE_ATTEMPT = 'password_change_attempt';
	case EMAIL_CHANGE_ATTEMPT = 'email_change_attempt';
	case ACCOUNT_DELETE_ATTEMPT = 'account_delete_attempt';
	// --------------------
	case PASSWORD_CHANGED = 'password_changed';
	case EMAIL_CHANGED = 'email_changed';
	case ACCOUNT_DELETED = 'account_deleted';

	// ----------------- LABEL -----------------
	public function label(): string
	{
		return match ($this) {
			self::LOGIN => 'login',
			self::REGISTER => 'register',
			self::PASSWORD_CHANGE_ATTEMPT => 'password_change_attempt',
			self::EMAIL_CHANGE_ATTEMPT => 'email_change_attempt',
			self::ACCOUNT_DELETE_ATTEMPT => 'account_delete_attempt',
			self::PASSWORD_CHANGED => 'password_changed',
			self::EMAIL_CHANGED => 'email_changed',
			self::ACCOUNT_DELETED => 'account_deleted',
		};
	}
}