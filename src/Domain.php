<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\PublicSuffix
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license    https://github.com/thephpleague/uri-hostname-parser/blob/master/LICENSE (MIT License)
 * @version    1.0.0
 * @link       https://github.com/thephpleague/uri-hostname-parser
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace League\Uri\PublicSuffix;

/**
 * Domain Value Object
 *
 * Lifted pretty much completely from Jeremy Kendall PDP
 * project
 *
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @author Ignace Nyamagana Butera <nyamsprod@gmail.com>
 */
final class Domain
{
    /**
     * @var string|null
     */
    private $domain;

    /**
     * @var string|null
     */
    private $publicSuffix;

    /**
     * @var bool
     */
    private $isValid;

    /**
     * New instance.
     *
     * @param string|null $domain
     * @param string|null $publicSuffix
     * @param bool        $isValid
     */
    public function __construct($domain = null, $publicSuffix = null, bool $isValid = false)
    {
        $this->domain = $domain;
        $this->publicSuffix = null !== $this->domain ? $publicSuffix : null;
        $this->isValid = null !== $this->publicSuffix ? $isValid : false;
    }

    /**
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string|null
     */
    public function getPublicSuffix()
    {
        return $this->publicSuffix;
    }

    /**
     * Does the domain have a matching rule in the Public Suffix List?
     *
     * WARNING: "Some people use the PSL to determine what is a valid domain name
     * and what isn't. This is dangerous, particularly in these days where new
     * gTLDs are arriving at a rapid pace, if your software does not regularly
     * receive PSL updates, because it will erroneously think new gTLDs are not
     * valid. The DNS is the proper source for this innormalizeion. If you must use
     * it for this purpose, please do not bake static copies of the PSL into your
     * software with no update mechanism."
     *
     * @see https://publicsuffix.org/learn/
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get registrable domain.
     *
     * Algorithm #7: The registered or registrable domain is the public suffix
     * plus one additional label.
     *
     * This method should return null if the domain provided is a public suffix,
     * per the test cases provided by Mozilla.
     *
     * @see https://publicsuffix.org/list/
     * @see https://raw.githubusercontent.com/publicsuffix/list/master/tests/test_psl.txt
     *
     * @return string|null registrable domain
     */
    public function getRegistrableDomain()
    {
        if (!$this->hasRegistrableDomain()) {
            return null;
        }

        $countLabelsToRemove = count(explode('.', $this->publicSuffix)) + 1;
        $domainLabels = explode('.', $this->domain);
        $domain = implode('.', array_slice($domainLabels, count($domainLabels) - $countLabelsToRemove));

        return $this->normalize($domain);
    }

    /**
     * Tells whether the domain has a registrable domain part.
     *
     * @return bool
     */
    private function hasRegistrableDomain(): bool
    {
        return null !== $this->publicSuffix
            && strpos($this->domain, '.') > 0
            && $this->publicSuffix !== $this->domain;
    }

    /**
     * Normalize the domain according to its representation.
     *
     * @param string $domain
     *
     * @return string
     */
    private function normalize(string $domain): string
    {
        if (strpos($domain, 'xn--') !== false) {
            return strtolower(idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46));
        }

        return idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46);
    }

    /**
     * Get the sub domain.
     *
     * This method should return null if
     *
     * - the registrable domain is null
     * - the registrable domain is the same as the public suffix
     *
     * @return string|null registrable domain
     */
    public function getSubDomain()
    {
        if (!$this->hasRegistrableDomain()) {
            return null;
        }

        $domainLabels = explode('.', $this->domain);
        $countLabels = count($domainLabels);
        $countLabelsToRemove = count(explode('.', $this->publicSuffix)) + 1;
        if ($countLabels === $countLabelsToRemove) {
            return null;
        }

        $domain = implode('.', array_slice($domainLabels, 0, $countLabels - $countLabelsToRemove));

        return $this->normalize($domain);
    }
}
