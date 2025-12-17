<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlamableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Blueprint::macro('blamable', function ($wantConstraints = true) {
            $this->unsignedBigInteger('created_by')->nullable();
            $this->unsignedBigInteger('updated_by')->nullable();

            if ($wantConstraints) {
                $this->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $this->foreign('updated_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });
        Blueprint::macro('dropBlamable', function () {
            $this->dropColumn(['created_by', 'updated_by']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
