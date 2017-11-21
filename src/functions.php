<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license    https://github.com/thephpleague/uri-parser/blob/master/LICENSE (MIT License)
 * @version    1.2.0
 * @link       https://github.com/thephpleague/uri-parser/
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
 * Returns PSL ICANN public info for a given domain.
 *
 * @param string|null $domain
 *
 * @see League\Uri\PublicSuffix\Rules::resolve
 *
 * @return Domain
 */
function resolve_domain($domain): Domain
{
    static $icann_rules;

    $icann_rules = $icann_rules ?? (new ICANNSectionManager(new Cache(), new CurlHttpClient()))->getRules();

    return $icann_rules->resolve($domain);
}
