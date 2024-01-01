<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserDetail
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $phone
 * @property string|null $id_card_no
 * @property string|null $id_attachment
 * @property string|null $profile_image
 * @property string|null $card_number
 * @property string|null $card_name
 * @property string|null $card_cvv
 * @property string|null $card_date
 * @property int $credit_score
 * @property float $balance
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class UserDetail extends Model
{
	protected $table = 'user_detail';

	protected $casts = [
		'user_id' => 'int',
		'credit_score' => 'int',
		'balance' => 'float',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'user_id',
		'phone',
        'job_type',
		'id_card_no',
		'id_attachment',
		'profile_image',
		'card_number',
		'card_name',
		'card_cvv',
		'card_date',
		'credit_score',
		'balance',
		'created_by',
		'updated_by'
	];
}
