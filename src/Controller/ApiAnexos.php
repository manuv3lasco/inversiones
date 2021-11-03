<?php


namespace Drupal\inversiones\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ApiAnexos extends \Drupal\Core\Controller\ControllerBase {

  public function archivo($name) {

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1);
    $query->condition('type', 'archivo_descargable');
    $query->condition('field_tipo', 'Anexos Tributarios');
    $query->condition('title', $name, '=');
    $nids = $query->execute();

    if(count($nids)!=0) {
      foreach ($nids as $key => $nid_value) {
        $nid = $nid_value;
      }

      $node = \Drupal\node\Entity\Node::load($nid);
      $fid = $node->get('field_archivo')->getValue()[0]["target_id"];

      if($fid != null) {
        $file = \Drupal\file\Entity\File::load($fid);

        $fileName = $file->get('filename')->getValue()[0]["value"];

        $uri = 'public://documentos_descargables/' . $fileName;

        $headers = array(
          'Content-Type'     => 'application/pdf',
          'Content-Disposition' => 'attachment;filename="'.$fileName.'"');

        return new BinaryFileResponse($uri, 200, $headers, true);
      }
      else {
        return new JsonResponse([
          'message' => 'Entity not found',
        ],400);
      }
    }
    else {
      //throw new NotFoundHttpException();
      return new JsonResponse([
        'message' => 'Entity not found',
      ],400);
    }
  }
}
