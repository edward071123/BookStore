<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');
        // DB::listen(function ($query) {
        //     $sql = $query->sql;
        //     // Binding Data
        //     $bindings = $query->bindings;
        //     // Spend Time
        //     $time = $query->time;

        //     // 針對 Binding 資料進行格式的處理
        //     // 例如字串就加上引號
        //     foreach ($bindings as $index => $binding) {
        //         if (is_bool($binding)) {
        //             $bindings[$index] = ($binding) ? ('1') : ('0');
        //         } elseif (is_string($binding)) {
        //             $bindings[$index] = "'$binding'";
        //         }
        //     }

        //     // 依據將 ? 取代成 Binding Data
        //     $sql = preg_replace_callback('/\?/', function () use (&$bindings) {
        //         return array_shift($bindings);
        //     }, $sql);

        //     File::append(
        //         storage_path('/logs/query.log'),
        //         $sql . PHP_EOL . $time . PHP_EOL
        //     );
        // });
    }
}
