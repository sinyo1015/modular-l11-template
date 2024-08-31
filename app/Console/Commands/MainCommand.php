<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:main {action?} {target?} {arg1?} {arg2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command here';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $target = $this->argument('target');
        $arg1 = $this->argument('arg1');
        $arg2 = $this->argument('arg2');

        if(strlen($action) > 0){
            if(strlen($target) > 0){
                switch($action){
                    case 'create_module':{
                        if(preg_match("/^[a-z]\w+$/", $target) == 0){
                            $this->error("Parameter must in snake_case and not started with number nor special characters except underscore(_)");
                            return;
                        }
                        $moduleName = "";
                        
                        foreach(explode("_", strtolower($target)) as $separate){
                            $moduleName .= ucfirst($separate);
                        }

                        if(File::isDirectory(base_path('app/Modules/') . $moduleName))
                        {
                            $this->error("This module is already exists");
                            return;
                        }
                        $this->line("Creating directories...");
                        File::ensureDirectoryExists(base_path('app/Modules/') . $moduleName . '/Controllers' );
                        File::ensureDirectoryExists(base_path('app/Modules/') . $moduleName . '/Middlewares' );
                        File::ensureDirectoryExists(base_path('app/Modules/') . $moduleName . '/Models' );
                        File::ensureDirectoryExists(base_path('app/Modules/') . $moduleName . '/Routes' );
                        File::ensureDirectoryExists(base_path('app/Modules/') . $moduleName . '/Requests' );
                        File::ensureDirectoryExists(base_path('app/Modules/') . $moduleName . '/views' );


                        $this->line("Bootstraping file...");
                        $routeFile = <<<EOF
                        <?php
                        use Illuminate\Support\Facades\Route;
                        Route::group(['prefix' => "$target", 'as' => "$target."], function(){

                        });
                        EOF;
                        $apiRouteFile = <<<EOF
                        <?php
                        use Illuminate\Support\Facades\Route;
                        Route::group(['prefix' => "$target/api", 'as' => "$target.api."], function(){

                        });
                        EOF;
                        File::put(base_path('app/Modules/') . $moduleName . '/Routes/web.php', $routeFile);
                        File::put(base_path('app/Modules/') . $moduleName . '/Routes/api.php', $apiRouteFile);
                        
                        
                        $this->info("Module creation succeed");
                        break;
                    }

                    case "create_controller":
                        if(strlen($target) == 0 || strlen($arg1) == 0){
                            $this->error("Invalid parameter, format [module name] [controller name]");
                            return;
                        }

                        $checkModuleValidity = File::isDirectory(base_path('app/Modules/' . $target));
                        if(!$checkModuleValidity){
                            $this->error("The module \"" . $target . "\" is not found");
                            return;
                        }

                        $result = Artisan::call("make:controller " . "\\\App\\\Modules\\\\" . $target . "\\\Controllers\\\\" . $arg1);
                        if($result == 0){
                            $this->info("Controller creation succeed");
                        }

                    break;

                    case "create_model":
                        if(strlen($target) == 0 || strlen($arg1) == 0){
                            $this->error("Invalid parameter, [module name] [model name]");
                            return;
                        }

                        $checkModuleValidity = File::isDirectory(base_path('app/Modules/' . $target));
                        if(!$checkModuleValidity){
                            $this->error("The module \"" . $target . "\" is not found");
                            return;
                        }

                        $result = Artisan::call("make:model " . "\\\App\\\Modules\\\\" . $target . "\\\Models\\\\" . $arg1);
                        if($result == 0){
                            $this->info("Model creation succeed");
                        }

                    break;

                    case "create_request":
                        if(strlen($target) == 0 || strlen($arg1) == 0){
                            $this->error("Invalid parameter, [module name] [request name]");
                            return;
                        }

                        $checkModuleValidity = File::isDirectory(base_path('app/Modules/' . $target));
                        if(!$checkModuleValidity){
                            $this->error("The module \"" . $target . "\" is not found");
                            return;
                        }

                        $result = Artisan::call("make:request " . "\\\App\\\Modules\\\\" . $target . "\\\Requests\\\\" . $arg1);
                        if($result == 0){
                            $this->info("Request creation succeed");
                        }

                    break;

                    case "create_middleware":
                        if(strlen($target) == 0 || strlen($arg1) == 0){
                            $this->error("Invalid parameter, [module name] [middleware name]");
                            return;
                        }

                        $checkModuleValidity = File::isDirectory(base_path('app/Modules/' . $target));
                        if(!$checkModuleValidity){
                            $this->error("The module \"" . $target . "\" is not found");
                            return;
                        }

                        $result = Artisan::call("make:middleware " . "\\\App\\\Modules\\\\" . $target . "\\\Middlewares\\\\" . $arg1);
                        if($result == 0){
                            $this->info("Middleware creation succeed");
                        }

                    break;
                }

                
            }
        }
    }
}
