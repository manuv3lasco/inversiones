<?php


namespace Drupal\inversiones\Controller;
use Symfony\Component\HttpFoundation\Response;


class PerfilInversionista extends \Drupal\Core\Controller\ControllerBase {
  public function resultado() {

    if (!empty($_GET['Respuestas'])) {
      $result = \Drupal::service('inversiones.services')->getInstance()->perfilEncuestaResultado($_GET['Respuestas']);
      $respuesta = '
         <div class="c-simulation-steps--results">
    <div class="coh-container-boxed coh-style-contenedor-base-boxed ">
        <div class="coh-row-inner">
            <div class="coh-col-sm-12 coh-col-xl-6">
                <h4>Tu perfil de <br> inversionista es <strong>'.$result['PerfilInversionista']['Nombre'].' </strong></h4>
                <div class="o-tag--group">
                    <div class="o-tag o-tag--green'.(($result['PerfilInversionista']['Nombre'] === 'CONSERVADOR') ? " o-tag--active" : "").' "> Conservador</div>
                    <div class="o-tag o-tag--yellow'.(($result['PerfilInversionista']['Nombre'] === 'MODERADO') ? " o-tag--active" : "").' "> Moderado</div>
                    <div class="o-tag o-tag--red'.(($result['PerfilInversionista']['Nombre'] === 'AGRESIVO') ? " o-tag--active" : "").' "> Agresivo</div>
                </div>
                <p>'.$result['PerfilInversionista']['Descripcion'].'</p>

            </div>
            <div class="coh-col-sm-12  coh-col-xl-6">
                <h4>¿Te gustaría empezar a invertir?</h4>
                <p>Puedes llamarnos al 2 2581 56 00</p>
                <p>Ó puedes escribirnos a través de el siguiente formulario de contacto, en el cual un ejecutivo se pondrá en contacto contigo:</p>
                <form id="solicitud_contacto" action="">
                    <div class="o-form__item">
                        <div class="o-form__field">
                            <label class="o-form__label">Nombre</label>
                            <input name="nombre" class="o-form__input" type="text" value="" required />
                        </div>
                    </div>
                    <div class="o-form__item">
                        <div class="o-form__field">
                            <label class="o-form__label">Apellido</label>
                            <input name="apellido" class="o-form__input" type="text" value="" required />
                        </div>
                    </div>
                    <div class="o-form__item">
                        <div class="o-form__field">
                            <label class="o-form__label">Rut</label>
                            <input name="rut" class="o-form__input" type="text" value="" required />
                        </div>
                    </div>
                    <div class="o-form__item">
                        <div class="o-form__field">
                            <label class="o-form__label">E-Mail</label>
                            <input name="email" class="o-form__input" type="text" value="" required />
                        </div>
                    </div>
                    <div id="field_phone" class="o-form__item">
                        <label class="o-dropdown__label">Teléfono Fijo</label>
                        <div class="coh-container-boxed coh-row-inner u-mb20">
                            <div class="coh-col-xl-3">
                                <div class="o-radio">
                                    <input name="op_phone" id="op_fijo" class="o-radio__input" type="radio" value="0" checked />
                                    <label class="o-radio__label" for="op_fijo">Red fija</label>
                                </div>
                            </div>
                            <div class="coh-col-xl-3">
                                <div class="o-radio">
                                    <input name="op_phone" id="op_movil" class="o-radio__input" type="radio" value="1" />
                                    <label class="o-radio__label" for="op_movil">Móvil</label>
                                </div>
                            </div>
                        </div>
                        <div class="coh-container-boxed coh-row-inner">
                            <div id="area" class="coh-col-xl-3">
                                <div id="telephone">
                                    <input name="area" class="o-form__input" type="text" value="+56" />
                                </div>
                                <div id="mobile" class="o-dropdown o-dropdown--inline" style="display:none;">
                                    <input name="area_mobile" class="o-form__input" type="text" value="+569" />
                                </div>
                            </div>
                            <div id="code" class="coh-col-xl-3">
                                <input name="sector" class="o-form__input" type="text" value="" />
                            </div>
                            <div id="number" class="coh-col-xl-6">
                                <div class="o-form">
                                    <div class="o-form__field">
                                        <input name="numero" class="o-form__input" type="text" value="" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="u-text_right u-mt40">
                        <input type="submit" class="o-btn--outline" value="Quiero que me contacten">
                    </div>
                    <div id="resultado_solicitud_contacto"></div>
                </form>
            </div>
        </div>
    </div>
</div>
      ';
    }
    else {
      $respuesta = '<html><div>Required Respuestas is empty.</div></html>';
    }
    return new Response($respuesta);
  }

  public function email() {

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'inversiones';
    $key = 'resultado';
    $to = "sacinversiones@security.cl";
    $params =  ['vars' => $_POST];
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
       $message = t('There was a problem sending your message and it was not sent.');
    }
    else {
      $message = t('Your message has been sent.');
    }
    return new Response($message);
  }
}
