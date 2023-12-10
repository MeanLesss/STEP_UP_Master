<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ServiceOrder
 *
 * @property int $id
 * @property int $service_id
 * @property int $order_by
 * @property int $cancel_by
 * @property bool $isCancel
 * @property Carbon|null $cancel_at
 * @property string|null $cancel_desc
 * @property string $order_title
 * @property string $order_description
 * @property int $order_status
 * @property array $order_attachments
 * @property Carbon|null $expected_expand_date
 * @property Carbon|null $expand_end_date
 * @property Carbon $expected_start_date
 * @property Carbon $expected_end_date
 * @property Carbon|null $Accepted_At
 * @property int $created_By
 * @property int|null $updated_By
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ServiceOrder extends Model
{
	protected $table = 'service_order';

	protected $casts = [
		'service_id' => 'int',
		'order_by' => 'int',
		'cancel_by' => 'int',
		'isCancel' => 'bool',
		'cancel_at' => 'datetime',
		'order_status' => 'int',
		'order_attachments' => 'json',
		'expected_expand_date' => 'datetime',
		'expand_end_date' => 'datetime',
		'expected_start_date' => 'datetime',
		'expected_end_date' => 'datetime',
		'accepted_At' => 'datetime',
		'created_By' => 'int',
		'updated_By' => 'int'
	];

	protected $fillable = [
		'service_id',
		'order_by',
		'cancel_by',
		'isCancel',
		'cancel_at',
		'cancel_desc',
		'order_title',
		'order_description',
		'order_status',
		'order_attachments',
		'expected_expand_date',
		'expand_end_date',
		'expected_start_date',
		'expected_end_date',
		'Accepted_At',
		'created_By',
		'updated_By'
	];
}
