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
 * on composer install or update
 */
final class ICANNSection
{
    /**
     * Post install information to update the local cache
     *
     * @param Event $event
     *
     * @return bool
     */
    public static function update(Event $event): bool
    {
        require $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        $io = $event->getIO();
        $io->write('Updating your Public Suffix List ICANN Section local cache.');
        if (!extension_loaded('curl')) {
            $io->writeError('Your local cache could not be updated.');
            $io->writeError('The PHP cURL extension is missing.');
            return true;
        }

        try {
            $manager = new ICANNSectionManager(new Cache(), new CurlHttpClient());
            if ($manager->refreshRules()) {
                $io->write('Your local cache has been sucessfully updated.');
                return true;
            }
            $io->writeError('Your local cache could not be updated.');
            $io->writeError('Please verify you can write in your local cache directory.');
            return true;
        } catch (Throwable $e) {
            $io->writeError('Your local cache could not be updated.');
            $io->writeError('An error occurred during the update');
            $io->writeError($e->getMessage());
            return true;
        }
    }
}
