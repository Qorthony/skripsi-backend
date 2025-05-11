<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{    /**
     * The name and signature of the console command.
     *
     * @var string
     */    protected $signature = 'admin:create {email} {password} {--name=Administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with specified email and password';

    /**
     * Execute the console command.
     */    
    public function handle()
    {        
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name');
        
        // Validate input
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'name' => $name
        ], [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        try {            // Create the admin user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin', // Always create with 'admin' role
                'email_verified_at' => now(),
            ]);

            $this->info('Admin user created successfully!');
            $this->table(
                ['Name', 'Email', 'Role'],
                [[$user->name, $user->email, $user->role]]
            );
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }
}
