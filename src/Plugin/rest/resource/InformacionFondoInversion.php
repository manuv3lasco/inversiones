<?php


namespace Drupal\inversiones\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * @RestResource(
 *   id = "informacion_fondo_inversion",
 *   label = "informacion fondo inversion",
 *   uri_paths = {
 *     "canonical" = "/fondo-de-inversion/valores-diarios"
 *   }
 * )
 */
class InformacionFondoInversion extends ResourceBase {

  /**
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {

    if (empty($_GET['id_fondo']) || empty($_GET['fecha'])) {
      return new ResourceResponse(['error' => 1, 'message' => 'Required fields: id_fondo, fecha.']);
      //throw new ServiceUnavailableHttpException(3, t('Image generation in progress. Try again shortly.'));
    }

    $id_fondo = $_GET['id_fondo'];
    $fecha = $_GET['fecha'];


    $result['body'] = \Drupal::service('inversiones.services')->getInstance()->valoresDiarios($id_fondo, $fecha);
    if (empty($result) || isset($result['message'])) {
      return new ResourceResponse(['error' => 1, 'message' => 'No fue posible acceder a la informacion solicitada. Por favor, reintente en unos instantes.']);
    }

    $response = $result;
    return new ResourceResponse($response);
  }
}
