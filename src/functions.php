<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\PublicSuffix
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license    https://github.com/thephpleague/uri-hostname-parser/blob/master/LICENSE (MIT License)
 * @version    1.0.4
 * @link       https://github.com/thephpleague/uri-hostname-parser
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace League\Uri;

use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\Domain;
use League\Uri\PublicSuffix\ICANNSectionManager;

/**
 * Returns PSL ICANN section info for a given domain.
 *
 * @param string|null $domain
 * @param string      $source_url
 *
 * @see League\Uri\PublicSuffix\Rules::resolve
 *
 * @return Domain
 */
function resolve_domain($domain, string $source_url = ICANNSectionManager::PSL_URL): Domain
{
    static $manager;

    $manager = $manager ?? new ICANNSectionManager(new Cache(), new CurlHttpClient());

    return $manager->getRules($source_url)->resolve($domain);
}
