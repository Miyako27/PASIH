<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('assignments')->select('id','status','updated_at')->orderByDesc('id')->limit(20)->get();
foreach($rows as $r){ echo $r->id.' | '.$r->status.' | '.$r->updated_at.PHP_EOL; }
