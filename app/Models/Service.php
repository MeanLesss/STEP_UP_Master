<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $status
 * @property string|null $attachments
 * @property string|null $requirement
 * @property float $price
 * @property float|null $discount
 * @property string|null $service_type
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Service extends Model
{
	protected $table = 'service';

	protected $casts = [
		'status' => 'int',
		'price' => 'float',
		'discount' => 'float',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'title',
		'description',
		'status',
		'view',
		'service_rate',
        'service_ordered_count',
		'attachments',
		'requirement',
		'price',
		'discount',
		'service_type',
		'start_date',
		'end_date',
		'created_by',
		'updated_by'
	];
}
