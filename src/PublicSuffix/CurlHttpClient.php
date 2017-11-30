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

namespace League\Uri\PublicSuffix;

/**
 * Simple cURL Http client
 *
 * Lifted pretty much completely from William Durand's excellent Geocoder
 * project
 *
 * @see https://github.com/willdurand/Geocoder Geocoder on GitHub
 *
 * @author William Durand <william.durand1@gmail.com>
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 * @author Ignace Nyamagana Butera <nyamsprod@gmail.com>
 */
final class CurlHttpClient implements HttpClient
{
    /**
     * {@inheritdoc}
     */
    public function getContent(string $url): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FAILONERROR => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        ]);
        $content = curl_exec($curl);
        if (CURLE_OK !== ($code = curl_errno($curl))) {
            $message = curl_error($curl);
            curl_close($curl);
            throw new HttpClientException($message, $code);
        }
        curl_close($curl);

        return $content;
    }
}
