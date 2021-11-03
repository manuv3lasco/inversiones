<?php


namespace Drupal\inversiones\Services;


use GuzzleHttp\Client;

class WebServices {

  private $client;

  private $apiType;

  public function __construct($apiType = NULL, $baseUri = NULL, $debug = false) {
    $this->apiType = $apiType;
    $stack = null;
    $this->client = new Client(
      [
        'base_uri' => $baseUri,
        'connect_timeout' => 5,
        'timeout' => 5,
        'handler' => $stack,
      ]
    );
  }

  public static function getInstance($apiType = 'api') {

    $config = [
      'apiBaseUri' => 'https://eservices.inversionessecurity.cl/api/website/',
      'docBaseUri' => 'https://eservices.inversionessecurity.cl/doc/website/',
      'debug' => false,
      'iframeLoginUrl' => 'https://privado.inversionessecurity.cl/login?publico=1',
    ];
    if ($apiType === 'api') {
      $baseUri = $config['apiBaseUri'];
    } else {
      $baseUri = $config['docBaseUri'];
    }
    return new self($apiType, $baseUri, !empty($config['debug']));
  }

  public function request($method, $path, array $params = []) {
    try {

      if ($params) {
        $params = $method === 'GET' ? ['query' => $params] : ['body' => json_encode($params)];
      }

      $res = $this->client->request(
        $method,
        $path,
        [
          'verify' => false,
          'headers' => [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Invsec-Version' => 1,
            'Invsec-Channel' => 1,
          ]
        ]+$params
      );

      if ($this->apiType === 'doc') {
        // download

        $this->downloadFile($res);

        return true;
      }

      return json_decode($res->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
      $return = json_decode($e->getResponse()->getBody(), true);

      if (isset($return['Message'])) {
        // el webservice a veces devuelve message y otras veces Message...
        $return['message'] = $return['Message'];
        unset($return['Message']);
      }

      return $return;

    } catch (\Exception $e) {

      \Drupal::logger('inversiones')->error($e->getMessage());

      return [];
    }
  }

  public function informacionFondo($idFondo, $idSerie, $fechaInicio, $fechaFin) {
    $path = 'publico/ffmm/fondo/'.$idFondo.'/'.$idSerie.'?fechaInicio='.$fechaInicio.'&fechaTermino='.$fechaFin;
    return $this->request('GET', $path);
  }

  public function valoresDiarios($idFondo, $fecha) {
    $path = 'publico/fondosInversion/valoresDiarios/fondo/' . $idFondo;
    $response = $this->request('GET', $path, ['fecha' => $fecha]);

    if (empty($response)) {
      $response = 'No fue posible acceder a la informacion solicitada. Por favor, reintente en unos instantes.';
    }
    else {
      $base = $response['ValoresDiarios'][0];

      $response = ' <table class="c-table  c-table--daily">
        <tbody class="c-table__body">
        <tr class="c-table__titles">
            <td>Código Cuota</td>
            <td>Participes con Saldo</td>
            <td>Cuotas Emitidas</td>
        </tr>
        <tr>
            <td class="c-table__color-mobile">
                <span class="c-table__title">Código Cuota</span>
                <strong>' . $base['Codigocuota'] . '</strong>
            </td>
            <td>
                <span class="c-table__title">Participes con Saldo</span>
                <strong>' . $base['Total_aportantes'] . '</strong></td>
            <td class="c-table__color-mobile">
                <span class="c-table__title">Cuotas Emitidas</span>
                <strong>'.inversiones_number_format($base['Cuotas_emitidas'], 4).'</strong></td>
        </tr>
        <tr class="c-table__titles">
            <td>Cuotas Pagadas</td>
            <td>Cuotas Suscritas No Pagadas</td>
            <td>Cuotas Promesas Suscripción Pago</td>
        </tr>
        <tr>
            <td>
                <span class="c-table__title">Cuotas Pagadas</span>
                <strong>'.inversiones_number_format($base['Cuotas_pagadas'], 4).'</strong></td>
            <td class="c-table__color-mobile">
                <span class="c-table__title">Cuotas Suscritas No Pagadas</span>
                <strong>'.inversiones_number_format($base['Cuotas_suscritas_no_pagadas'], 4).'</strong></td>
            <td>
                <span class="c-table__title">Cuotas Promesas Suscripción Pago</span>
                <strong>'.inversiones_number_format($base['Cuotas_promesas_suscripcion_pago'], 4).'</strong></td>
        </tr>
        </tbody>
    </table>

    <table class="c-table c-table--daily">
        <tbody class="c-table__body">
        <tr class="c-table__titles">
            <td>Contratos Promesas Suscripción Pago</td>
            <td>Número Prominentes Suscriptores Cuota</td>
        </tr>
        <tr>
            <td class="c-table__color-mobile">
                <span class="c-table__title">Contratos Promesas Suscripción Pago</span>
                <strong>' . $base['Contratos_promesas_suscricpcion_pago'] . '</strong></td>
            <td>
                <span class="c-table__title">Número Prominentes Suscriptores Cuota</span><strong>' . $base['Numero_prominentes_suscriptores_cuota'] . '</strong>
            </td>
        </tr>
        <tr class="c-table__titles">
            <td>Valor Cuota</td>
            <td>Fecha Valor Cuota</td>
        </tr>
        <tr>
            <td class="c-table__color-mobile">
                <span class="c-table__title">Valor Cuota</span>
                <strong>'.inversiones_number_format($base['Valor_cuota'], 4).'</strong></td>
            <td>
                <span class="c-table__title">Fecha Valor Cuota</span>
                <strong>'.\DateTime::createFromFormat('Ymd', $base['Fecha_valor_cuota'])->format('d/m/Y').'</strong></td>
        </tr>
        </tbody>
    </table>';
      $test = inversiones_number_format($base['Cuotas_emitidas'],4);
      return $response;
    }
  }

  public function fondosMutuos() {
    return $this->request('GET', 'publico/ffmm/fondos');
  }

  public function fondosMutuosSeries($idFondo) {
    return $this->request('GET', 'publico/ffmm/fondo/'.$idFondo.'/series');
  }

  public function fondosInversion() {
    return $this->request('GET', 'publico/fondosInversion');
  }

  public function canastaValores() {
    return $this->request('GET', 'publico/ffmm/fondo/canastaValores');
  }

  public function trackingError() {
    return $this->request('GET', 'publico/trackError');
  }

  public function fondosMutuosValoresCuota() {
    return $this->request('GET', 'publico/ffmm/valoresCuota');
  }
  public function perfilEncuesta() {
    return $this->request('GET', 'publico/perfil/encuesta', ['tipo' => 'PN']);
  }
  public function perfilEncuestaResultado($respuestas) {
    $params = [
      "Respuestas" => $respuestas,
      "TipoPersona" => "PN"
    ];
    return $this->request('POST', 'publico/perfil/encuesta', $params);
  }

  public function simuladoresApvPension($sexo, $fechaNacimiento, $estadoCivil, $edadJubilacion,
                                        $saldoApv, $saldoCuentaApv, $rentaMensualBruta, $rentaPromedioMensual,
                                        $cuentaCapitalizacion, $saldoDepositosConvenidos, $valorNominalBonoReconocimiento) {

    $params = [
      "Sexo" => $sexo,
      "FechaNacimiento" => $fechaNacimiento,
      "EstadoCivil" => $estadoCivil,
      "EdadJubilacion" => $edadJubilacion,
      "SaldoApv" => (int)$saldoApv,
      "SaldoCuentaApv" => (int)($saldoCuentaApv === "" ? 0 : $saldoCuentaApv),
      "RentaMensualBruta" => (int)$rentaMensualBruta,
      "RentaPromedioMensual" => (int)$rentaPromedioMensual,
      "CuentaCapitalizacion" => (int)$cuentaCapitalizacion,
      "SaldoDepositosConvenidos" => (int)($saldoDepositosConvenidos === "" ? 0 : $saldoDepositosConvenidos),
      "ValorNominalBonoReconocimiento" => (int)($valorNominalBonoReconocimiento === "" ? 0 : $valorNominalBonoReconocimiento),
    ];
    return $this->request('POST', 'publico/simuladores/apv/pension', $params);
  }
}
