<?php

namespace App\Utilities;

use App\Libraries\Email;
use Illuminate\Support\Facades\Log;

/**
 * Description of helpers
 *
 * @author 1024511539
 */
class helpers
{

  //generales
  static $success = 'Se ha completado exitosamente';
  static $error = 'Se ha presentado un error';
  static $not_result = 'No se ha encontrado ningun resultado';
  static $field_empty = 'Campo vacio';
  static $ws_success = 'Se ha procesado la solicitud';
  static $ws_error = 'No se ha podido procesar la solicitud';
  static $ws_error_exist_request = 'No se ha procesado la solicitud, hay una en curso';
  static $ws_connection = 'No hay comunicación , intente más tarde';
  static $user_inactive = "El usuario se encuentra inhabilitado, consulte con el administrador";
  static $user_registered = "El usuario ha sido registrado exitosamente";
  static $user_active = "Usuario en sesión";
  static $user_registered_Email = "El usuario está registrado, Mensaje de registro ha sido enviado al email";
  // List of codes Response
  const emptyocontent = 100;  //Cuerpo de la petición vacío / No Content-Type	
  const errorvalidate = 101;  //Error validacion del campo	
  const user_inactive_code = 102; //Usuario inactivo	
  const fail_auth = 103;  //Error Autenticación	
  const error_web_service = 104;  //Error conexión de servicio o web_service	
  const noallowed = 105;  //Proceso no permitido	
  const error_saving = 106; //No se pudo almacenar	
  const error_db_handled = 201; //Errores controlados en programación en base de datos	
  const error_services_av = 202;  //Error conexión de servicio asistente virtual	
  const error_db = 300; //Errores de programación en base de datos	
  const error_processing_data = 320;  //Error de decodificado y grabado de imagenes	
  const error_general = 400;  //Error desconocido	
  const error_sms_visible = 600;  //Error proveedor sms visibles	
  const error_sms_no_visible = 601; //Error proveedor sms no visibles
  const complete_process = 0; //Petición exitosa
  const nofound = 404; //No se ha encontrada el contenido buscado
  //Mensajes de proveedor sms
  static $error_sms_alt = "No se ha podido validar el número telefónico, intente más tarde";
  static $sms1 = "Autenticaciòn fallida";
  static $sms2 = "Fuera de horario autorizado";
  static $sms3 = "No tiene saldo en la cuenta para realizar el envio";
  static $sms4 = "El número ingresado no tiene la cantidad de dígitos esperados ( 12 caracteres)";
  static $sms6 = "Sistemas en mantenimiento, intente más tarde";
  static $sms7 = "Supera la cantidad máxima de celulares permitidos enviados por parámetro";
  static $sms8 = "Numero de celular se encuentra en lista de bloqueo";
  static $sms0 = "El indicativo del celular no corresponde a ningún operador o el operador se encuentra desactivado";
  static $sms_success = "El mensaje fue enviado con éxito";

  static function errorResponseJsonDB($ex)
  {
    Log::error('Error DB-->>' . $ex->getMessage());
    return response()->json([
      'status' => 'ERROR',
      'statusCode' => helpers::error_db,
      'message' => $ex->getMessage(),
      'data' => ''
    ]);
  }
  /**
   * Create a new JSON response.
   * @param  string|array $message
   * @param  string|array|object  $data
   * @param  string  $status
   * @param  int  $statusCode
   */
  static function ResponseJson($message, $data = [], $status = 'OK', $statusCode = helpers::complete_process)
  {
    return response()->json([
      'status' => $status,
      'statusCode' => $statusCode,
      'message' => $message,
      'data' => $data
    ]);
  }

  static function httpPost($url, $data)
  {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }
  static function httpPostLeads($url, $data)
  {
    $headers = array("token: " . env('TOKEN_LANDINGS', ''));
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    curl_close($handle);
    if ($code == 200)
      return $response;
    else
      return $code;
  }

  static function httpPostJson($url, $data)
  {
    $curl = curl_init($url);
    $send_push = json_encode($data);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $send_push);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }

  //Emular navegador con petición GET
  static function httpGet($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_exec($ch);
    curl_close($ch);
    return $output;
  }

  public static function cleanString($string)
  {
    $string = trim($string);

    $string = str_replace(
      array('à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä', 'á'),
      array('a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'a'),
      $string
    );

    $string = str_replace(
      array('è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'é'),
      array('e', 'e', 'e', 'E', 'E', 'E', 'E', 'e'),
      $string
    );

    $string = str_replace(
      array('ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'í'),
      array('i', 'i', 'i', 'I', 'I', 'I', 'I', 'i'),
      $string
    );

    $string = str_replace(
      array('ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'ó'),
      array('o', 'o', 'o', 'O', 'O', 'O', 'O', 'o'),
      $string
    );

    $string = str_replace(
      array('ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'ú'),
      array('u', 'u', 'u', 'U', 'U', 'U', 'U', 'u'),
      $string
    );


    $string = str_replace(
      array('ñ', 'Ñ', 'ç', 'Ç'),
      array('n', 'N', 'c', 'C',),
      $string
    );


    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
      array("¨", "º", "`", "¨", "´",),
      '',
      $string
    );

    return $string;
  }

  public static function sendEmail($data)
  {
    $current_hour = date('G:i');
    $current_date = date('Y-m-d');
    $gral_notification_id = $data['gral_notification_id'];
    $type = $data['type'];

    unset($data['type']);
    unset($data['gral_notification_id']);
    $response = Email::send_email($data);
    //cast al response del servicio de postmark 
    if ($response == "200") {
      $status = "Enviado";
      return $status;
    } else {
      $status = "No Enviado";
      return $status;
    }
  }

  public static function sendSms($mobile, $message, $option, $data)
  {
    $current_hour = date('G:i');
    $current_date = date('Y-m-d');
    switch ($option) {
      case 'generalNotification':
        $gral_notification_id = $data['gral_notification_id'];
        $type = $data['type'];
        return helpers::sendSmsGeneralNotification($mobile, $message, $current_hour, $current_date, $gral_notification_id, $type);
        break;
    }
  }

  public static function sendSmsGeneralNotification($mobile, $message)
  {
    $message = helpers::cleanString("bellpi: " . $message);


    $user_sms = env('SMS_USER');
    $password_sms = env('SMS_USER_PASSWORD');
    $datos = array();
    $datos['user'] = $user_sms;
    $datos['password'] = $password_sms;
    $datos['GSM'] = $mobile;
    $datos['SMSText'] = $message;

    //Envio de datos por medio de curl al service web
    $response = helpers::httpPost(env('WEB_SERVICE_SMS'), $datos);
    $response_request = helpers::verifySmsResponse($response);
    return $response_request['status'];
  }

  public static function verifySmsResponse($response)
  {
    if (isset($response) && $response != "") {

      $_response = explode(",", $response);
      //Verificar autenticación
      if ($response == -1) {
        return response()->json(['status' => 'No enviado', 'errors_sms' => helpers::$sms1]);
      }
      //Verificar el segundo array que contiene el status del envio web a través del service web
      else if (isset($_response[1]) && $_response[1] != "") {
        //limpiar respuesta de etiquetas html
        $message_response = strip_tags($_response[1]);
        //Dar respuesta del servicio dependiendo el caso
        switch (trim($message_response)) {
          case -2:
            return (['status' => 'No enviado', 'message_sms' => helpers::$sms2]);
            break;

          case -3:
            return (['status' => 'No enviado', 'message_sms' => helpers::$sms3]);
            break;

          case -4:
            return (['status' => 'Descartado', 'message_sms' => helpers::$sms4]);
            break;

          case -6:
            return (['status' => 'Falla en servicio', 'message_sms' => helpers::$sms6]);
            break;

          case -7:
            return (['status' => 'No enviado', 'message_sms' => helpers::$sms7]);

            break;

          case -8:
            return (['status' => 'No enviado', 'message_sms' => helpers::$sms8]);

            break;

          case 0:
            return (['status' => 'Descartado', 'message_sms' => helpers::$sms0]);
            break;
          case ($message_response > 0):
            return (['status' => 'Enviado', 'message_sms' => helpers::$sms_success]);
            break;
        }
      }
    }
  }
}
