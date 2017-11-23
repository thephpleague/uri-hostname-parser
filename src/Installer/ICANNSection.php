<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\PublicSuffix
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license    https://github.com/thephpleague/uri-hostname-parser/blob/master/LICENSE (MIT License)
 * @version    1.0.3
 * @link       https://github.com/thephpleague/uri-hostname-parser
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace League\Uri\Installer;

use Composer\Script\Event;
use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\ICANNSectionManager;
use Throwable;

/**
 * A class to manage PSL ICANN Section rules updates
 */
final class ICANNSection
{
    /**
     * Script to update the local cache using composer hook
     *
     * @param Event $event
     */
    public static function update(Event $event = null)
    {
        $io = static::getIO($event);
        $vendor = static::getVendorPath($event);
        if (null === $vendor) {
            $io->writeError([
                'You must set up the project dependencies using composer',
                'see https://getcomposer.org',
            ]);
            die(1);
        }

        require $vendor.'/autoload.php';

        $io->write('Updating your Public Suffix List ICANN Section local cache.');
        if (!extension_loaded('curl')) {
            $io->writeError([
                '😓 😓 😓 Your local cache could not be updated. 😓 😓 😓',
                'The PHP cURL extension is missing.',
            ]);
            die(1);
        }

        try {
            $manager = new ICANNSectionManager(new Cache(), new CurlHttpClient());
            if ($manager->refreshRules()) {
                $io->write([
                    '💪 💪 💪 Your local cache has been successfully updated. 💪 💪 💪',
                    'Have a nice day!',
                ]);
                die(0);
            }
            $io->writeError([
                '😓 😓 😓 Your local cache could not be updated. 😓 😓 😓',
                'Please verify you can write in your local cache directory.',
            ]);
            die(1);
        } catch (Throwable $e) {
            $io->writeError([
                '😓 😓 😓 Your local cache could not be updated. 😓 😓 😓',
                'An error occurred during the update.',
                '----- Error Trace ----',
            ]);
            $io->writeError($e->getMessage());
            die(1);
        }
    }

    /**
     * Detect the vendor path
     *
     * @param Event $event
     *
     * @return string|null
     */
    private static function getVendorPath(Event $event = null)
    {
        if ($event instanceof Event) {
            return $event->getComposer()->getConfig()->get('vendor-dir');
        }

        if (is_dir($vendor = dirname(__DIR__, 2).'/vendor')) {
            return $vendor;
        }

        if (is_dir($vendor = dirname(__DIR__, 5).'/vendor')) {
            return $vendor;
        }

        return null;
    }

    /**
     * Detect the I/O interface to use
     *
     * @param Event|null $event
     *
     * @return object
     */
    private static function getIO(Event $event = null)
    {
        if ($event instanceof Event) {
            return $event->getIO();
        }

        return new class() {
            public function write($messages, bool $newline = true, int $verbosity = 2)
            {
                $this->doWrite($messages, $newline, false, $verbosity);
            }

            public function writeError($messages, bool $newline = true, int $verbosity = 2)
            {
                $this->doWrite($messages, $newline, true, $verbosity);
            }

            private function doWrite($messages, bool $newline, bool $stderr, int $verbosity)
            {
                fwrite(
                    $stderr ? STDERR : STDOUT,
                    implode($newline ? PHP_EOL : '', (array) $messages).PHP_EOL
                );
            }
        };
    }
}
