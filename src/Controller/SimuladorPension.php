<?php


namespace Drupal\inversiones\Controller;
use Symfony\Component\HttpFoundation\Response;


class SimuladorPension extends \Drupal\Core\Controller\ControllerBase {

  public function resultado() {
    $requiredFields = [
      "Sexo", "SaldoApv", "SaldoCuentaApv", "RentaMensualBruta", "RentaPromedioMensual",
      "CuentaCapitalizacion", "SaldoDepositosConvenidos", "ValorNominalBonoReconocimiento"
    ];

    if($_GET["Sexo"] == 'masculino'){
      $EdadJubilacion = '65';
    }else{
      $EdadJubilacion = '60';
    }
    $EstadoCivil = 'soltero';
    $Anios = $_GET["years"];
    $YearCurrent = date('Y');
    $YearTotal = $YearCurrent - $Anios;
    $FechaNacimiento = date('d').'/'.date('m').'/'.$YearTotal;


    foreach($requiredFields as $field) {
      if (!isset($_GET[$field])) {
        echo theme('inversiones_alert_message', ['mensaje' => 'Completa todos los campos.']);
        drupal_exit();
      }
    }

    $numberFields = ['SaldoApv', 'SaldoCuentaApv', 'RentaMensualBruta', 'RentaPromedioMensual', 'CuentaCapitalizacion', 'SaldoDepositosConvenidos', 'ValorNominalBonoReconocimiento'];

    foreach($numberFields as $numberField) {
      $_GET[$numberField] = inversiones_fix_webservice_number($_GET[$numberField]);
    }

    $result = \Drupal::service('inversiones.services')->getInstance()->simuladoresApvPension(
      $_GET["Sexo"], $FechaNacimiento, $EstadoCivil, $EdadJubilacion,
      $_GET["SaldoApv"], $_GET["SaldoCuentaApv"],
      $_GET["RentaMensualBruta"], $_GET["RentaPromedioMensual"],
      $_GET["CuentaCapitalizacion"], $_GET["SaldoDepositosConvenidos"],
      $_GET["ValorNominalBonoReconocimiento"]
    );

    if (!$result) {
      $message = '
        <div class="o-alert o-alert--error o-alert--actions">
            <div class="o-alert__icon"><i class="fa fa-exclamation-circle"></i></div>
            <div class="o-alert__content">
                <p class="o-alert__text"><span>No fue posible acceder a la informacion solicitada. Por favor, reintente en unos instantes.</span></p>
            </div>
        </div>
      ';
    }
    else {
      if (isset($result['message'])) {
        $message = '
        <div class="o-alert o-alert--error o-alert--actions">
            <div class="o-alert__icon"><i class="fa fa-exclamation-circle"></i></div>
            <div class="o-alert__content">
                <p class="o-alert__text"><span>'.$result['message'].'</span></p>
            </div>
        </div>
      ';
      }
      else {
        $message = '
      <div class="c-simulation__result js-simulation-result">
            <div class="c-simulation__result--header">
                <div class="coh-row-inner">
                    <div class="coh-col-sm-12 coh-col-xl-6">
                        <p>
                            '.nl2br(
                    inversiones_replace_number_placeholder(
                      $result['APVPension']['Glosa']
                    )).'
                        </p>
                    </div>
                    <div class="coh-col-sm-12 coh-col-xl-6">
                        <p><strong>¿Suena bien, verdad?</strong></p>
                        <p>¡No pierdas más tiempo y comienza a ahorrar ahora!</p><a href="/servicio-al-cliente/contactenos" class="o-btn o-btn--outline">Quiero que me contacten </a>
                    </div>
                </div>
            </div>
            <div class="coh-row-inner">
                <div class="coh-col-xl-12">
                    <h4>Revisa el detalle de la simulación de tu pensión con APV</h4>
                </div>
                <div class="coh-col-sm-12 coh-col-xl-6">
                    <table class="c-table--clean">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="u-text_center">Sin APV</th>
                            <th class="c-table__red u-text_center">Con APV</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="td-title">APV Mensual, hasta los 65 años</td>
                            <td class="u-text_center"><span>Sin APV</span> $'.inversiones_number_format($result["APVPension"]["Calculos"]["SinAPV"]["APVMensual"]).'</td>
                            <td class="c-table__red u-text_center">$'.inversiones_number_format($result["APVPension"]["Calculos"]["ConAPV"]["APVMensual"]).'</td>
                        </tr>
                        <tr>
                            <td class="td-title">Saldo cuenta AFP a los 65 años</td>
                            <td class="u-text_center"><span>Sin APV</span> $'.inversiones_number_format($result["APVPension"]["Calculos"]["SinAPV"]["SaldoCuentaAFP"]).'</td>
                            <td class="c-table__red u-text_center"><span>Con APV</span>  $'.inversiones_number_format($result["APVPension"]["Calculos"]["ConAPV"]["SaldoCuentaAFP"]).'</td>
                        </tr>
                        <tr>
                            <td class="td-title">Saldo cuenta APV a los 65 años</td>
                            <td class="u-text_center"><span>Sin APV</span> $'. inversiones_number_format($result["APVPension"]["Calculos"]["SinAPV"]["SaldoCuentaAPV"]).'</td>
                            <td class="c-table__red u-text_center"><span>Con APV</span> $'. inversiones_number_format($result["APVPension"]["Calculos"]["ConAPV"]["SaldoCuentaAPV"]).'</td>
                        </tr>
                        <tr>
                            <td class="td-title">Saldo total para jubilación a los 65 años</td>
                            <td class="u-text_center"><span>Sin APV</span> $'. inversiones_number_format($result["APVPension"]["Calculos"]["SinAPV"]["SaldoTotalJubilacion"]).'</td>
                            <td class="c-table__red u-text_center"><span>Con APV</span> $'. inversiones_number_format($result["APVPension"]["Calculos"]["ConAPV"]["SaldoTotalJubilacion"]).'</td>
                        </tr>
                        <tr>
                            <td class="td-title">Aumento de pensión</td>
                            <td class="u-text_center"><span>Sin APV</span> '. inversiones_number_format($result["APVPension"]["Calculos"]["SinAPV"]["AumentoPension"]).'</td>
                            <td class="c-table__red u-text_center"><span>Con APV</span> '. inversiones_number_format($result["APVPension"]["Calculos"]["ConAPV"]["AumentoPension"]).'</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="coh-col-sm-12 coh-col-xl-6">
                    <div id="graphic_simulation_pension"
                        conapv="'. $result["APVPension"]["Grafico"]["ConAPV"].'"
                        sinapv="'. $result["APVPension"]["Grafico"]["SinAPV"].'">grafico</div>
                </div>
            </div>
            <div class="c-box">
                <div class="coh-row-inner">
                    <div class="coh-col-sm-12 coh-col-xl-6">
                        <div class="c-box__item">
                            <h5>Parámetros para cálculo de proyección de pensión</h5>
                            <p>'. $result["APVPension"]["ParametrosParaCalculo"]["Glosa"].'</p>
                            <table>
                                <tr>
                                    <td>Renta mensual bruta: </td>
                                    <td> <strong>$'. inversiones_number_format($result["APVPension"]["ParametrosParaCalculo"]["RentaMensualBruta"]).'</strong></td>
                                </tr>
                                <tr>
                                    <td>Valor bono reconocimiento:</td>
                                    <td> <strong>$'. inversiones_number_format($result["APVPension"]["ParametrosParaCalculo"]["ValorBonoReconocimiento"]).'</strong></td>
                                </tr>
                                <tr>
                                    <td>Tope imponible Cotizaciones:</td>
                                    <td> <strong>'. inversiones_number_format($result["APVPension"]["ParametrosParaCalculo"]["TopeImponibleCotizaciones"], 1).' UF</strong></td>
                                </tr>
                                <tr>
                                    <td>Tope imponible APV: </td>
                                    <td> <strong>'. inversiones_number_format($result["APVPension"]["ParametrosParaCalculo"]["TopeImponibleAPV"], 1).' UF </strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="coh-col-sm-12 coh-col-xl-6">
                        <div class="c-box__item">
                            <h5>Supuestos para cálculo de proyección de pensión</h5>
                            <p>'. $result["APVPension"]["SupuestosParaCalculo"]["Glosa"].'</p>
                            <table>
                                <tr>
                                    <td>Valor UF</td>
                                    <td> <strong>$'. inversiones_number_format($result["APVPension"]["SupuestosParaCalculo"]["ValorUF"], 2).'</strong></td>
                                </tr>
                                <tr>
                                    <td>Descuento AFP Mensual</td>
                                    <td> <strong>'. $result["APVPension"]["SupuestosParaCalculo"]["DescuentoAFPMensual"].' UF</strong></td>
                                </tr>
                                <tr>
                                    <td>Monto de la orden:</td>
                                    <td> <strong>'. inversiones_number_format($result["APVPension"]["SupuestosParaCalculo"]["MontoOrden"]).' UF</strong></td>
                                </tr>
                                <tr>
                                    <td>Rentabilidad AFP Proyectada anual</td>
                                    <td> <strong>'. inversiones_number_format($result["APVPension"]["SupuestosParaCalculo"]["RentabilidadAFPProyectadaAnual"], 1).' UF</strong></td>
                                </tr>
                                <tr>
                                    <td>Rentabilidad APV Proyectada anual</td>
                                    <td> <strong>'. inversiones_number_format($result["APVPension"]["SupuestosParaCalculo"]["RentabilidadAPVProyectadaAnual"], 1).' UF</strong></td>
                                </tr>
                                <tr>
                                    <td>Rentabilidad bono reconocimiento proyectado anual</td>
                                    <td> <strong>'. inversiones_number_format($result["APVPension"]["SupuestosParaCalculo"]["RentabilidadBonoReconocimientoProyectadoAnual"], 1).' UF </strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="coh-col-xl-12 u-text_center"><a href="/servicio-al-cliente/contactenos" class="o-btn o-btn--primary">Quiero que me contacten</a></div>
                </div>
            </div>
        </div>
        ';
      }
    }
    return new Response($message);
  }
}
