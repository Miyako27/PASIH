<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'assignment_documents hasil_analisis: '.DB::table('assignment_documents')->where('document_type','hasil_analisis')->count().PHP_EOL;
echo 'distinct assignment_id on assignment_documents hasil_analisis: '.DB::table('assignment_documents')->where('document_type','hasil_analisis')->distinct('assignment_id')->count('assignment_id').PHP_EOL;
echo 'assignments status completed: '.DB::table('assignments')->where('status','completed')->count().PHP_EOL;

echo PHP_EOL.'Top assignment_id from assignment_documents hasil_analisis:'.PHP_EOL;
DB::table('assignment_documents')
  ->select('assignment_id', DB::raw('count(*) as total'))
  ->where('document_type','hasil_analisis')
  ->groupBy('assignment_id')
  ->orderByDesc('total')
  ->limit(10)
  ->get()
  ->each(function($row){ echo 'assignment_id '.$row->assignment_id.' => '.$row->total.PHP_EOL; });
