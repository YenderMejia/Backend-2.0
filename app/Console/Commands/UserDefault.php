<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UserDefault extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un usuario por defecto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = new \App\Models\User();
        $user->name = 'Admin';
        $user->email = 'admin@cinema.next.ec';
        $user->password = 'cinema_admin';
        $user->role = 'admin';
        $user->save();
    }
}
