<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email : Email address for the administrator}';

    protected $description = 'Create an administrator account using an interactive password prompt';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::where('email', $email)->exists()) {
            $this->error('Provide a valid email address that is not already registered.');

            return self::FAILURE;
        }

        $password = $this->secret('Administrator password (10+ characters, letters and numbers)');
        $validator = Validator::make(['password' => $password], ['password' => ['required', Password::min(10)->letters()->numbers()]]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first('password'));

            return self::FAILURE;
        }

        $name = $this->ask('Administrator name');

        if (! is_string($name) || trim($name) === '' || mb_strlen($name) > 120) {
            $this->error('Provide a name between 1 and 120 characters.');

            return self::FAILURE;
        }

        User::create([
            'name' => trim($name),
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info('Administrator account created. The password was not printed or saved to source.');

        return self::SUCCESS;
    }
}