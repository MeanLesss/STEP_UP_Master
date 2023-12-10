<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool|null $isGuest
 * @property int|null $role
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $login_attempt
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

	protected $table = 'users';

	protected $casts = [
		'isGuest' => 'bool',
		'role' => 'int',
		'email_verified_at' => 'datetime',
		'login_attempt' => 'int'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'isGuest',
		'role',
		'email_verified_at',
		'password',
		'login_attempt',
		'remember_token'
	];
}
