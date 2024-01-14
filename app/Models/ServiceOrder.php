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
 * @property Carbon|null $accepted_at
 * @property int $created_by
 * @property int|null $updated_by
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
		'accepted_at' => 'datetime',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'service_id',
		'service_order',
		'freelancer_id',
		'order_by',
		'cancel_by',
		'isCancel',
        'isAgreementAgreed',
        'accepted_at',
        'start_at',
		'cancel_at',
		'cancel_desc',
		'order_title',
		'order_description',
		'order_status',
		'completed_attachments',
		'order_attachments',
		'expected_expand_date',
		'expand_end_date',
		'expected_start_date',
		'expected_end_date',
		'accepted_at',
		'created_by',
		'updated_by'
	];
}
