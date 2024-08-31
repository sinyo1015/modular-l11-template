<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        
        $directoryIterator = new \DirectoryIterator(base_path("app/Modules"));
        foreach($directoryIterator as $directory){
            if(!$directory->isDot()){
                $realPath = $directory->getRealPath() . "/Routes";
                if(File::isDirectory($realPath)){
                    $fileIterator = new \DirectoryIterator($realPath);
                    foreach($fileIterator as $file){
                        if(!$file->isDot()){
                            if(preg_match("/^(api|web)\.php+$/", $file->getFilename())){
                                include $file->getRealPath();

                                if(preg_match("/^(web)\.php+$/", $file->getFilename())){
                                    Route::middleware("web")
                                        ->group($file->getRealPath());
                                }

                                if(preg_match("/^(api)\.php+$/", $file->getFilename())){
                                    Route::middleware("api")
                                        ->group($file->getRealPath());
                                }
                            }
                        }
                    }
                }
            }
        }

        include base_path('routes/web.php');
    }
}
