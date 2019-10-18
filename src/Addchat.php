<?php

namespace Classiebit\Addchat;

use Illuminate\Filesystem\Filesystem;

class Addchat
{
    protected $version;
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = app(Filesystem::class);

        $this->findVersion();
    }

    public function routes()
    {
        require __DIR__.'/../routes/addchat.php';
    }

    public function getVersion()
    {
        return $this->version;
    }

    protected function findVersion()
    {
        if (!is_null($this->version)) {
            return;
        }

        if ($this->filesystem->exists(base_path('composer.lock'))) {
            // Get the composer.lock file
            $file = json_decode(
                $this->filesystem->get(base_path('composer.lock'))
            );

            // Loop through all the packages and get the version of package
            foreach ($file->packages as $package) {
                if ($package->name == 'classiebit/addchat') {
                    $this->version = $package->version;
                    break;
                }
            }
        }
    }
}
