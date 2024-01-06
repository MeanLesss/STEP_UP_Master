<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property int $client_status
 * @property int $freelancer_status
 * @property bool $isComplain
 * @property int|null $rate
 * @property int $tranc_status
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Transaction extends Model
{
	protected $table = 'transaction';

	protected $casts = [
		'user_id' => 'int',
		'order_id' => 'int',
		'client_status' => 'int',
		'freelancer_status' => 'int',
		'isComplain' => 'bool',
		'rate' => 'int',
		'tranc_status' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'user_id',
		'order_id',
		'client_status',
		'freelancer_status',
		'isComplain',
		'rate',
		'tranc_attachments',
		'tranc_status',
		'created_by',
		'updated_by'
	];
}
