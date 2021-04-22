<?php

namespace Core\FileStorage;

use Illuminate\Http\Request;
use Core\AppInterface;
use Illuminate\Support\Manager;
use function Core\setting;
use Core\FileStorage\Channels\ChannelInterface;

class ChannelManager extends Manager
{
    /**
     * Support drives.
     * @var array
     */
    public const DRIVES = ['public', 'protected', 'private'];

    /**
     * Filesystem manager.
     * @var \Core\FileStorage\FilesystemManager
     */
    protected $fielsystemManager;

    /**
     * Create the manager instance.
     * @param \Core\AppInterface $app
     * @param \Core\FileStorage\FilesystemManager $fielsystemManager
     */
    public function __construct(AppInterface $app, FilesystemManager $fielsystemManager)
    {
        parent::__construct($app);
        $this->filesystemManager = $fielsystemManager;
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver()
    {
        return null;
    }

    /**
     * Create public channel driver.
     * @return \Core\FileStorage\Channels\ChannelInterface
     */
    protected function createPublicDriver(): ChannelInterface
    {
        $filesystem = $this->filesystemManager->driver(
            setting('file-storage', 'channels.public')['filesystem'] ?? null
        );

        $channel = $this->app->make(Channels\PublicChannel::class);
        $channel->setFilesystem($filesystem);
        $channel->setRequest($this->app->make(Request::class));

        return $channel;
    }
}
