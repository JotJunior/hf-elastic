<?php

require_once __DIR__ . '/../vendor/autoload.php';


$queryBuilder = new \Jot\HfElastic\QueryBuilder();

$query = $queryBuilder
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('status', '=', 'active')
    ->where('salary', '>', 1000)
    ->where('birth_date', 'between', ['2010-01-01', '2019-12-31'])
    ->whereMust(fn($query) => $query->where('name', 'like', 'mary%')->where('email', '=', 'bar@ster'))
    ->whereShould(fn($query) => $query->where('name', '=', 'john')->where('email', '=', 'bla@ster'))
    ->whereNested('orders', fn($query) => $query->where('status', '=', 'orders.paid'))
    ->orderBy('name', 'asc')
    ->limit(100)
    ->offset(0);

echo json_encode($query->toArray(), JSON_PRETTY_PRINT);

//
//
//
//$queryBuilder = new \Jot\HfElastic\QueryBuilder();
//
//$query = $queryBuilder
//    ->select(['id', 'account', 'amount'])
//    ->from('2go_tracker_transactions')
//    ->where(
//        'user.geo_location',
//        'distance',
//        ['lat' => -23.6039153, 'lon' => -46.6659276, 'distance' => '1km']
//    )
//    ->limit(10);
//
//echo json_encode($query->toArray()['body'], JSON_PRETTY_PRINT);
//
//
