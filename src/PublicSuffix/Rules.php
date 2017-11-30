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

namespace League\Uri\PublicSuffix;

final class Rules
{
    /**
     * @var array
     */
    private $rules;

    /**
     * new instance.
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Returns PSL ICANN public info for a given domain.
     *
     * @param string|null $domain
     *
     * @return Domain
     */
    public function resolve(string $domain = null): Domain
    {
        if (!$this->isMatchable($domain)) {
            return new Domain();
        }

        $normalizedDomain = $this->normalize($domain);
        $reverseLabels = array_reverse(explode('.', $normalizedDomain));
        $publicSuffix = $this->findPublicSuffix($reverseLabels);
        if (null === $publicSuffix) {
            return $this->handleNoMatches($domain);
        }

        return $this->handleMatches($domain, $publicSuffix);
    }

    /**
     * Tells whether the given domain is valid.
     *
     * @param string|null $domain
     *
     * @return bool
     */
    private function isMatchable($domain): bool
    {
        return $domain !== null
            && strpos($domain, '.') > 0
            && strlen($domain) === strcspn($domain, '][')
            && !filter_var($domain, FILTER_VALIDATE_IP);
    }

    /**
     * Normalize domain.
     *
     * "The domain must be canonicalized in the normal way for hostnames - lower-case, Punycode."
     *
     * @see http://www.ietf.org/rfc/rfc3492.txt
     *
     * @param string $domain
     *
     * @return string
     */
    private function normalize(string $domain): string
    {
        if (false !== strpos($domain, '%')) {
            $domain = rawurldecode($domain);
        }

        $normalize = idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
        if (false === $normalize) {
            return '';
        }

        return strtolower($normalize);
    }

    /**
     * Returns the matched public suffix or null
     * if none found.
     *
     * @param array $labels
     *
     * @return string|null
     */
    private function findPublicSuffix(array $labels)
    {
        $matches = [];
        $rules = $this->rules;
        foreach ($labels as $label) {
            //match exception rule
            if (isset($rules[$label], $rules[$label]['!'])) {
                break;
            }

            //match wildcard rule
            if (isset($rules['*'])) {
                array_unshift($matches, $label);
                break;
            }

            //no match found
            if (!isset($rules[$label])) {
                // Avoids improper parsing when $domain's subdomain + public suffix ===
                // a valid public suffix (e.g. domain 'us.example.com' and public suffix 'us.com')
                //
                // Added by @goodhabit in https://github.com/jeremykendall/php-domain-parser/pull/15
                // Resolves https://github.com/jeremykendall/php-domain-parser/issues/16
                break;
            }

            array_unshift($matches, $label);
            $rules = $rules[$label];
        }

        return empty($matches) ? null : implode('.', array_filter($matches, 'strlen'));
    }

    /**
     * Returns the Domain value object.
     *
     * @param string $domain
     * @param string $publicSuffix
     *
     * @return Domain
     */
    private function handleMatches(string $domain, string $publicSuffix): Domain
    {
        if (!$this->isPunycoded($domain)) {
            $publicSuffix = idn_to_utf8($publicSuffix, 0, INTL_IDNA_VARIANT_UTS46);
        }

        return new Domain($domain, $publicSuffix, true);
    }

    /**
     * Tells whether the domain is punycoded.
     *
     * @param string $domain
     *
     * @return bool
     */
    private function isPunycoded(string $domain): bool
    {
        return strpos($domain, 'xn--') !== false;
    }

    /**
     * Returns the Domain value object.
     *
     * @param string $domain
     *
     * @return Domain
     */
    private function handleNoMatches(string $domain): Domain
    {
        $labels = explode('.', $domain);
        $publicSuffix = array_pop($labels);

        if (!$this->isPunycoded($domain)) {
            $publicSuffix = idn_to_utf8($publicSuffix, 0, INTL_IDNA_VARIANT_UTS46);
            if (false === $publicSuffix) {
                $publicSuffix = null;
            }
        }

        return new Domain($domain, $publicSuffix);
    }
}
