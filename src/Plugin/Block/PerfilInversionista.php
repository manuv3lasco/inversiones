<?php


namespace Drupal\inversiones\Plugin\Block;

use \Drupal\Core\Block\BlockBase;


/**
 * Provides a Perfil Inversionista security block.
 *
 * @Block(
 *   id = "perfil_inversionista_inversiones_block",
 *   admin_label = @Translation("Perfil inversionista"),
 * )
 */
class PerfilInversionista extends BlockBase  {

    /**
     * @inheritDoc
     */
    public function build()
    {
      $content = [];
      $renderable = [
        '#theme' => 'block_perfil_inversionista_inversiones_security',
        '#content' => $content,

      ];
      return $renderable;
    }
}
