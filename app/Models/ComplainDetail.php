<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComplainDetail
 * 
 * @property int $id
 * @property int $tranc_id
 * @property int $tranc_user_id
 * @property int $tranc_service_id
 * @property string $title
 * @property string $description
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ComplainDetail extends Model
{
	protected $table = 'complain_detail';

	protected $casts = [
		'tranc_id' => 'int',
		'tranc_user_id' => 'int',
		'tranc_service_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'tranc_id',
		'tranc_user_id',
		'tranc_service_id',
		'title',
		'description',
		'created_by',
		'updated_by'
	];
}
