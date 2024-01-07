<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TopUpLog
 *
 * @property int $id
 * @property int $user_id
 * @property float|null $balance
 * @property string|null $card_number
 * @property string|null $card_name
 * @property string|null $card_cvv
 * @property string|null $card_date
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class TopUpLog extends Model
{
	protected $table = 'top_up_log';

	protected $casts = [
		'user_id' => 'int',
		'balance' => 'float',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'user_id',
		'balance',
		'card_number',
		'card_name',
		'card_cvv',
		'card_date',
		'created_by',
		'updated_by'
	];
}
