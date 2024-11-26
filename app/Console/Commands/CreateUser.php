<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('Input email');
        $pw = $this->secret('Input password');
        $name = $this->ask('Input name');

        $validator = Validator::make([
            'email' => $email,
            'pw'    => $pw,
            'name'  => $name
        ],[
            'email' => 'required|email|unique:users,email|max:255',
            'pw'    => 'required|min:8|max:255',
            'name'  => 'required|max:255'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->get('email') as $key => $err) {
                $this->error("- ".$err);
            }
            foreach ($validator->errors()->get('pw') as $key => $err) {
                $this->error("- ".$err);
            }
            foreach ($validator->errors()->get('name') as $key => $err) {
                $this->error("- ".$err);
            }

            return 0;
        }
        
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($pw);
        $user->email_verified_at = now();
        $saved = $user->save();

        if (!$saved) {
            $this->error('gagal menyimpan!');
            return 0;
        }

        $this->line('Recap:');
        $this->line('email : '.$email);
        $this->line('password : '.$pw);
        $this->line('name : '.$name);

    }
}
