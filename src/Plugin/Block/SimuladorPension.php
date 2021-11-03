<?php


namespace Drupal\inversiones\Plugin\Block;

use \Drupal\Core\Block\BlockBase;


/**
 * Provides a Simulador Pension security block.
 *
 * @Block(
 *   id = "simulador_pension_inversiones_block",
 *   admin_label = @Translation("Simulador Pension"),
 * )
 */
class SimuladorPension extends BlockBase  {

    /**
     * @inheritDoc
     */
    public function build()
    {
      $content = [];
      $renderable = [
        '#theme' => 'block_simulador_pension_inversiones_security',
        '#content' => $content,

      ];
      return $renderable;
    }
}
