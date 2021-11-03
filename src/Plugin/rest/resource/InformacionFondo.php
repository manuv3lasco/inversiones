<?php


namespace Drupal\inversiones\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * @RestResource(
 *   id = "informacion_fondo",
 *   label = "informacion fondo",
 *   uri_paths = {
 *     "canonical" = "/informacion-fondo"
 *   }
 * )
 */
class InformacionFondo extends ResourceBase {

  /**
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {

    if (empty($_GET['IdFondo']) || !isset($_GET['IdSerie']) || empty($_GET['Plazo'])) {
      return new ResourceResponse(['error' => 1, 'message' => 'Required fields: IdFondo, IdSerie, Plazo.']);
      //throw new ServiceUnavailableHttpException(3, t('Image generation in progress. Try again shortly.'));
    }

    switch($_GET['Plazo']) {
      case "mensual":
        $fechaInicio = date('Ymd', strtotime('-1 month'));
        break;
      case "anual":
        $fechaInicio = date('Y').'0101';
        break;
      case "ultimos_años":
        $fechaInicio = (date('Y')-3).'0101';
        break;
      default:
        //case "ultimos_meses":
        $fechaInicio = date('Ymd', strtotime('-1 year'));
        break;
    }
    $result = \Drupal::service('inversiones.services')->getInstance()->informacionFondo($_GET['IdFondo'], $_GET['IdSerie'], $fechaInicio, date('Ymd'));
    if (empty($result) || isset($result['message'])) {
      return new ResourceResponse(['error' => 1, 'message' => 'No fue posible acceder a la informacion solicitada. Por favor, reintente en unos instantes.']);
    }
    $result['Rentabilidad']['Grafico'] = array_map(function($data){
      $data["Fecha"] = \DateTime::createFromFormat('Ymd', $data["Fecha"])->format('d/m/Y');
      return $data;
    }, $result['Rentabilidad']['Grafico']);

    $response = $result['Rentabilidad']['Grafico'];
    return new ResourceResponse($response);
  }
}
