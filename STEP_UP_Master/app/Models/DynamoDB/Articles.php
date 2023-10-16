<?php

namespace App\Models\DynamoDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use BaoPham\DynamoDb\DynamoDbModel as Model;

class Articles extends Model
{
    use HasFactory;
    //protected $table = 'Articles'; // replace with your DynamoDB table name
    public function __construct(array $attributes = []){
        $this->table = 'STEP_UP';
        parent::__construct($attributes);
    }

    protected $fillable = [
        'Pages',
        'Articles',
        'ID',
        'STEP_UP'
    ];
    public function saveRecord(Articles $articles)
    {
        $model = new Articles();
        $date = now();
        $model->ID = 0;
        $model->Articles = "test laravel";
        $model->save();
    }

    public function setPagesAttribute($value)
    {
        $this->attributes['Pages'] = json_encode($value);
    }

    public function getPagesAttribute($value)
    {
        return json_decode($value);
    }
}
