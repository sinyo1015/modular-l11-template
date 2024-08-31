<?php
use Illuminate\Support\Facades\Route;
Route::group(['prefix' => "hello_world", 'as' => "hello_world."], function(){
    Route::get("/", function(){
        return response("Hello World!");
    });
});