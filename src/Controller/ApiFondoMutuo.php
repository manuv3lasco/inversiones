<?php


namespace Drupal\inversiones\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ApiFondoMutuo extends \Drupal\Core\Controller\ControllerBase {

  public function folleto_informativo($fid) {

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1);
    $query->condition('type', 'fondo_mutuo');
    $query->condition('field_id_fondo_mutuo', $fid, '=');
    $nids = $query->execute();

    if(count($nids)!=0) {
      foreach ($nids as $key => $nid_value) {
        $nid = $nid_value;
      }

      $node = \Drupal\node\Entity\Node::load($nid);
      $fid = $node->get('field_folleto_informativo')->getValue()[0]["target_id"];

      if($fid != null) {
        $file = \Drupal\file\Entity\File::load($fid);

        $fileName = $file->get('filename')->getValue()[0]["value"];

        $uri = 'public://folleto-informativo/' . $fileName;

        $headers = array(
          'Content-Type'     => 'application/pdf',
          'Content-Disposition' => 'attachment;filename="'.$fileName.'"');

        return new BinaryFileResponse($uri, 200, $headers, true);
      }
      else {
        return new JsonResponse([
          'message' => 'Entity not found',
        ],404);
      }
    }
    else {
      return new JsonResponse([
        'message' => 'Entity not found',
      ],404);
    }
  }
}
