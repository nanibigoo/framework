<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-10-28 19:11
 */
namespace Notadd\Foundation\Extension\Commands;

use Composer\Json\JsonFile;
use Illuminate\Support\Str;
use Notadd\Foundation\Composer\Abstracts\Command;
use Notadd\Foundation\Extension\ExtensionManager;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class InstallCommand.
 */
class InstallCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of a extension to be install');
        $this->setDescription('Install a Extension');
        $this->setName('extension:install');
    }

    /**
     * @param \Notadd\Foundation\Extension\ExtensionManager           $manager
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $settings
     *
     * @return bool
     */
    public function fire(ExtensionManager $manager, SettingsRepository $settings)
    {
        $name = $this->input->getArgument('name');
        $extensions = $manager->getExtensionPaths();
        if (!$extensions->offsetExists($name)) {
            $this->error("Extension {$name} do not exist!");

            return false;
        }
        if ($settings->get('extension.'.$name.'.installed')) {
            $this->error("Extension {$name} is installed!");

            return false;
        }
        $path = $extensions->get($name);
        if (Str::contains($path, $manager->getVendorPath())) {
            $this->error("Extension {$name} is installed!");

            return false;
        }
        $extensionFile = new JsonFile($path.DIRECTORY_SEPARATOR.'composer.json');
        $extension = collect($extensionFile->read());
        if ($extension->has('autoload')) {
            $autoload = collect($extension->get('autoload'));
            $autoload->has('classmap') && collect($autoload->get('classmap'))->each(function ($value) use ($path) {
                $path = str_replace($this->container->basePath().'/', '', realpath($path.DIRECTORY_SEPARATOR.$value)).'/';
                if (!in_array($path, $this->backup['autoload']['classmap'])) {
                    $this->backup['autoload']['classmap'][] = $path;
                }
            });
            $autoload->has('files') && collect($autoload->get('files'))->each(function ($value) use ($path) {
                $path = str_replace($this->container->basePath().'/', '', realpath($path.DIRECTORY_SEPARATOR.$value));
                if (!in_array($path, $this->backup['autoload']['files'])) {
                    $this->backup['autoload']['files'][] = $path;
                }
            });
            $autoload->has('psr-0') && collect($autoload->get('psr-0'))->each(function ($value, $key) use ($path) {
                $path = str_replace($this->container->basePath().'/', '', realpath($path.DIRECTORY_SEPARATOR.$value)).'/';
                $this->backup['autoload']['psr-0'][$key] = $path;
            });
            $autoload->has('psr-4') && collect($autoload->get('psr-4'))->each(function ($value, $key) use ($path) {
                $path = str_replace($this->container->basePath().'/', '', realpath($path.DIRECTORY_SEPARATOR.$value)).'/';
                $this->backup['autoload']['psr-4'][$key] = $path;
            });
            $this->json->addProperty('autoload', $this->backup['autoload']);
            $settings->set('extension.'.$name.'.autoload', json_encode($autoload->toArray()));
        }
        if ($extension->has('require')) {
            $require = collect($extension->get('require'));
            $require->each(function ($version, $name) {
                $this->backup['require'][$name] = $version;
            });
            $this->json->addProperty('require', $this->backup['require']);
            $settings->set('extension.'.$name.'.require', json_encode($require->toArray()));
        }
        $this->updateComposer(true);
        $settings->set('extension.'.$name.'.installed', true);
        $this->info("Extension {$name} is installed!");

        return true;
    }
}