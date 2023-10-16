<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DynamoDB\Articles;

class TestController extends Controller
{
    protected $dynamoDb;

    public function __construct(DynamoDbClient $dynamoDb)
    {
        $this->dynamoDb = $dynamoDb;
    }

    public function index()
    {
        // Now you can use $this->dynamoDb to interact with DynamoDB\
        $test = new Articles();
        $test->saveRecord(new Articles());
        return view('test',$test);
    }
}
