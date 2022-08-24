<?php

namespace App\Console\Commands;

use App\Models\ApiApp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateApiApps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:api-apps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API Apps for API Access';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('Input app name:');
        $alias = Str::slug($name, '_');
        $appkey = Str::random(20);
        ApiApp::create([
            'name' => $name,
            'alias' => $alias,
            'appkey' => Hash::make($appkey)
        ]);
        $this->info('Generated Alias: '.$alias);
        $this->info('Generated Appkey: '.$appkey);
    }
}
