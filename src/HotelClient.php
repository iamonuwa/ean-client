<?php

namespace Otg\Ean;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Serializer;
use Otg\Ean\RequestLocation\XmlQueryLocation;
use Otg\Ean\Subscriber\EanError;
use Otg\Ean\Subscriber\ContentLength;

/**
 * HotelClient object for executing commands against the EAN Hotel API
 *
 * @method array getHotelList(array $arguments)
 * @method array getRoomAvailability(array $arguments)
 * @method array postReservation(array $arguments)
 * @method array getRoomCancellation(array $arguments)
 * @package Otg\Ean
 */
class HotelClient extends GuzzleClient
{
    /**
     * Gets a new HotelClient
     *
     * @param  array       $config GuzzleClient $config options
     * @return HotelClient
     */
    public static function factory($config = array())
    {
        $description = new Description(include(__DIR__ . '/Resources/hotel-xml-v3.php'));

        $defaults = array(
            'serializer' => new Serializer($description, array(
                'xml.query' => new XmlQueryLocation('xml.query')
            )),
            'defaults' => array(
                'booking_endpoint' => 'https://book.api.ean.com',
                'general_endpoint' => 'http://api.ean.com',
                'cid' => '',
                'apiKey' => '',
                'customerIpAddress' => '',
                'customerUserAgent' => '',
            )
        );

        $config += $defaults;
        $config['defaults'] += $defaults['defaults'];

        $httpClient = new HttpClient();

        $client = new self($httpClient, $description, $config);
        $client->getEmitter()->attach(new EanError());
        $client->getEmitter()->attach(new ContentLength());

        return $client;
    }
}
