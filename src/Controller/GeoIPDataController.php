<?php
/**
 *
 * @file
 * Contains \Drupal\geoip_data\Controller\GeoIPDataController.
 */

namespace Drupal\geoip_data\Controller;

use Drupal\Core\Config\ConfigException;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GeoIp2\Database\Reader;


class GeoIPDataController extends ControllerBase
{
    /**
     * Get GeoIP Data in JSON format
     *
     * @param string $ip IP address
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function country($ip = null, $format = 'json')
    {
      $record = $this->getCountryRecord($ip);

      $maxAge = \Drupal::config('system.performance')
        ->get('cache')['page']['max_age'];
      $dateExpires = new \DateTime("+1 month");

      return new Response(
        json_encode(
          ['isoCode' => $record->country->isoCode,
            'name' => $record->country->name,
            'names' => $record->country->names,
          ]
        ),
        200,
        [
          'Content-Type' => 'application/json',
          'cache-control' => 'max-age='.$maxAge.', public',
          'expires' => $dateExpires->format("r")
        ]
      );


    }

  /**
   * Get GeoIP Data country record
   *
   * @param string $ip IP address
   *
   * @return \GeoIp2\Model\Country
   */
  protected function getCountryRecord($ip = null)
  {
    if (!$ip) {
      $ip = \Drupal::request()->getClientIp();
    }

    $dbPath = drupal_get_path('module', 'geoip_data').'/'.
      \Drupal::config('geoip_data.settings')->get('db_path');
    if (!is_file($dbPath)) {
      throw new ConfigException(
        'Path to MaxMind\'s GeoLite2 Country database not defined'
      );
    }

    $reader = new Reader($dbPath);
    try {
      $record = $reader->country($ip);
    } catch (\InvalidArgumentException $argumentException){
      throw new NotFoundHttpException();
    }

    return $record;
  }
}
