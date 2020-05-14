![alt text](http://paneltalleres.test.bellpi.co/img/bellpi-taller.png "beelpi")

# Proyecto Base en Laravel bellpi

Este proyecto tiene la configuración inicial para cualquier proyecto que se quiera hacer en Laravel, además de incluir archivos de referencia para estandarizar ciertos aspectos del mismo.

Comandos básicos al montar el proyecto

> composer install \
> php artisan key:generate

---

## Passport

Passport es la manera que se usa para validar las Autenticación del usuario, ya esta instalado en el proyecto, solo hace falta correr los siguientes comandos:

> php artisan migrate
>
> php artisan passport:install

---

## Libraries

Los proyectos usan PostMark para el envió de correos electronicos, por lo cual el proyecto incluye los archivos necesarios para trabajar con este.

Se debe agregar al archivo **Mail** de config las siguientes opciones:

```TXT
'postmark' => [
  'casatoro' => [
    'api_key' => 'Una vez asignada',
    'from_name' => 'Por determinar',
    'from_address' => 'Por determinar',
    'validation' => TRUE,
    'strip_html' => TRUE,
    'develop' => FALSE,
  ],
],
```

---

## API Controllers

La estructura base de un controlador de API esta dado bajo lo siguiente:

- La carpeta padre del controlador determina la versión de la API, en la defecto esta v1

- Al momento de crear un Controlador que tenga un CRUD y dependa de un Modelo, se puede hacer con el siguiente comando:

    > php artisan make:controller API/v1/[nameController] --api --model=[nameModel]

- Las API seran validadas usando los Request

### Estructura de API routes

En el archivo **api** de **routes** hay una configuración base para todas las rutas API, en caso de requerir middleware también hay un ejemplo con este.

La convención usada para las **APIs** en caso de ser un **CRUD** es de esta manera:

> api/v[numberVersion]/[nameRoute] **Siempre en plural**

Ejemplo: api/v1/users

Si se crea con:

> Route::apiResource('nameRoute','nameController')
>
> Por defecto creara todas las rutas: GET, POST, DELETE, Etc...

De otra manera se puede hacer solo con:

> Route::"[Method]"('nameRoute','nameController@function')
>
> Para este caso el Method puede ser GET, POST, DELETE, Etc...

Ejemplo: api/v1/ejemplo

---

## Request

Los request permiten disminuir las responsabilidades de los controladores de tal manera que el Request se encargue de realizar acciones como: Validar, Responder Errores, Formatear datos.

- El comando básico para crear un Request es mediante:

> php artisan make:request [nameController]Request

Se puede usar el Request de Ejemplo para seguir la convención en los demás archivos.

### Todos los Archivos deben incluir las siguientes lineas de código

```PHP
use App\Http\Requests\BaseRequest;

class example extends BaseRequest{}
```

Se simplifica la funcionalidad extendiendo al **BaseRequest**, el que incluye las funciones básicas, además de todas las opciones que puede usar en cada una de las Request desde: **rules**, **attributes**, **messages**.

Los **Messages** incluye unos mensajes predeterminados, solo haría falta determinar los atributos.

Para usar estas validaciones en un controllador se debe hacer de la siguiente manera:

```PHP
use App\Http\Requests\EjemploRequest;
public function (EjemploRequest $request) {
  "Aquí ira el código Lógico una vez el request termina de validar, seguira con lo que aquí este."
}
```

## Helpers

El proyecto incluye un directorio con Utilities, actualmente solo dispone de Helpers, donde estan plasmados todos las funciones generales como lo pueden ser Responder en Json o Responder en Json cuando es error de base de datos, la manera de usar es:

### Helper Response Json

```PHP
use App\Util\helpers;
public function example() {
  $message = "Proceso completo";
  $data = "Información complementaria" | [
    "key" => "value"
  ];
  return helpers::ResponseJson($message,$data);
}
```

La función **ResponseJson** tiene 4 parametros, 1 es necesarios, el message. Los parametros **\$data**, **\$status**, **\$statusCode** tienen valores por defecto.

> \$data = null;
>
> \$status = 'OK';
>
> \$statusCode = 0;

Sin embargo se pueden enviar diferentes valores, que se pueden observar en la ayuda del editor o IDE.

### Helper Response Error DB

Al usar **tryCatch** en las funciones, los errores capturados por el catch enviados con la función **helpers::errorResponseJsonDB**:

```PHP
use App\Util\helpers;
public function example() {
  try {
    saveUser($user->id);
    $message = "Proceso completo";
    $data = "Información complementaria" | [
      "key" => "value"
    ];
    return helpers::ResponseJson($message,$data);
  } catch(Exeception $e) {
    return helpers::errorResponseJsonDB($e);
  }
}
```

Con esto se puede dar una respuesta al usuario, además se alamcenar el error que sucedio para su previa analisis y solución.

---

## Laravel Excel

Dependencia encargada de generar reportes tiene dos opciones:

- Importar: Se genera con el comando
    > php artisan make:import [name]Import --model=[nameModel]
- Exportart: Se genera con el comando
    > php artisan make:export [name]Export --model=[nameModel]

---

## Notificaciones

Se pueden genar las Notificaciones para que usen diferentes metodos de manera que se usen la configuración defecto de Laravel.

Ejemplo Reset Password:

```PHP
namespace App\Notifications;

use App\Channels\PostmarkChannel;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Libraries\Email;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;
    public $actionUrl;
    public $email;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
      $this->actionUrl = action('Auth\ResetPasswordController@showResetForm',$token);
      $this->email = app()->call(ForgotPasswordController::class.'@returnEmail');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['mail'];
        return [PostmarkChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function setActionUrl($notifiable) {
      return $this->actionUrl;
    }
    public function setEmail($notifiable)
    {
      return $this->email;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
```

Esto nos permite usar la configuración defecto de Laravel y solo modificar la información que se envia, además de permitir cambiar el tipo de medio por el cual se envia eso se hace mediante la función **via**.

Se puede crear usando el comando:

> php artisan make:notification [nameTypeNotification]

### Channel

Ejemplo de Channel:`

```PHP
namespace App\Channels;

use App\Libraries\Email;
use App\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class PostmarkChannel
{

    public function __construct() { }
    /**
     * @param Notifiable $notifiable
     * @param Notification $notification
     * @throws WebHookFailedException
     */
    public function send($notifiable, Notification $notification)
    {
      $url = $notification->setActionUrl($notifiable);
      $email = $notification->setEmail($notifiable);

      $data = [];

      $data['to'] = $email;
      $data['id'] = 'idPlantillaPostMark';

      $data['model'] = [
        'url_password' => $url
      ];

      $responseEmail = Email::send_email_with_template($data);
      try {
        if($responseEmail !== 200){
          throw new \Throwable("Ha sucedido un error en el envio", 1);
        }
        return response()->json("Se ha completado existosamente el envio $email",200);
      } catch (\Throwable $th) {
        return response()->json($th->getMessage(),200);
      }
    }
}
```

> php artisan make:channel [nameTypeChannel]

---

## Integración Asesor Digital

En el archivo de **Helpers** ya hay una función encarga de enviar la información al AD, solo hay que usarla, eso puede hacer mediante la siguiente opción:

```PHP
function example($request) {
  $data = [
    "name" => "Example Pepito",
    "email" => "epepito@example.com",
    "phone" => "3214567890"
    "type_document" => "CC"
    "document_number" => "1023456987"
    "source" => "$request->origin | URL plataforma"
    "terms_conditions" => "true | false | S | N"
    "campaign_id" => env('CAMPAING_ID_[asignada]')
  ];
  helpers::httpPostLeads(env('URL_NEW_LEAD'), $datos);
}
```

La función **helpers::httpPostLeads** recibe dos parametros, la URL que ya esta seteada en el env y un array con datos, este ejemplo muestra un caso, sin embargo puede variar con respecto a la campaña que se tenga asignada.

En el archivo **.env** se pueden determinar diferentes campañas deben cumplir con este estandar:

> CAMPAING*ID*[asignada]=[value]
>
> asignada=Hace referencia al nombre que le establezcan en el Asesor digital
>
> value=El número correspondiente a esa campaña

---

## Documentación Swagger

Lo primero que se tiene que hacer es correo el siguiente comando para generar los archivos necesario de la documentación:

> php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

También hay que agregar lo siguiente a las rutas web, con su respectivo middleware que se encargue de validar al usuario que intenta acceder

> Route::get('api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api');
>
> Route::get('docs/{jsonFile?}','\L5Swagger\Http\Controllers\SwaggerController@docs');
>
> Route::post('docs/{jsonFile?}','\L5Swagger\Http\Controllers\SwaggerController@docs');
>
> Route::put('docs/{jsonFile?}','\L5Swagger\Http\Controllers\SwaggerController@docs');
>
> Route::patch('docs/{jsonFile?}','\L5Swagger\Http\Controllers\SwaggerController@docs');
>
> Route::delete('docs/{jsonFile?}','\L5Swagger\Http\Controllers\SwaggerController@docs');

Además de esto hay que agregar lo siguiente al **.gitignore**

> /storage/api-docs

Cada vez que se realicé un cambio en la documentación o cuando se quiera generar por primera vez la documentación hay que correr este comando:

> php artisan l5-swagger:generate

La documentación de cada función, se recomiendo agregar en la parte superior de la misma. De esa manera se dara el correspondiente contexto sobre lo que realiza esa función.

Explicación de los terminos básicos de la documentación:

> **path**=Es la URL a la cual puede acceder \
> **tags**=Es la encarga de agrupar bajo un mismo Titulo las peticiones \
> **summany**=Es un breve resumen de que hace la funcion \
> **description**=Explicación de que operación se realizan en esta función y cual es su objetivo \
> **operationId**=Es un ID identificador, puede ser igual en bajo diferentes tags. Es como el ID en una clase HTML \
> **security**=Se usa para determinar que tipo de Middleware usa la URL a la que se esta intentando acceder.

### Ejemplo de uso para GET

```TXT
/**
* @OA\Get(
*   path="/api/v1/{nameController}",
*   tags={"Example"},
*   summary="Obtener los datos de ejemplo",
*   description="Se encarga de procersar los queries y devolver la información requerida.",
*   operationId="example",
*   security={{"bearerAuth":{}}},
*   @OA\Response(
*     response=200,
*     description="OK"
*   )
* )
*/
```

### Ejemplo de uso para POST

```TXT
/**
  * @OA\Post(
  *  path="/api/v1/{nameController}",
  *  tags={"Example"},
  *  summary="Guardar la información",
  *  description="Se encarga de procersar los datos enviados y almacenarlos.",
  *  operationId="example-post",
  *  security={{"bearerAuth":{}}},
  *  @OA\RequestBody(
  *   @OA\MediaType(
  *     mediaType="application/json",
  *     @OA\Schema(
  *       required={"data"},
  *       @OA\Property(
  *         property="data",
  *         type="Array|String|Number",
  *         format="Integer|Enum",
  *         emun="{"opcion"}",
  *         example="valueString",
  *         example={
  *           "key": "valueString" | valueInt
  *         },
  *         example=valueInt
  *       )
  *     )
  *   )
  *  ),
  *  @OA\Response(
  *    response=200,
  *    description="OK"
  *  )
  * )
  */
```

### Ejemplo de uso para PUT

```TXT
/**
  * @OA\Put(
  *  path="/api/v1/{nameController}/{key}",
  *  tags={"Example"},
  *  summary="Actualiza la información",
  *  description="Se encarga de procersar los datos enviados y almacenarlos.",
  *  operationId="example-put",
  *  security={{"bearerAuth":{}}},
  *  @OA\Parameter(
  *    name="key",
  *    in="key",
  *    required=true,
  *    @OA\Schema(
  *      type="number",
  *    )
  *  ),
  *  @OA\RequestBody(
  *   @OA\MediaType(
  *     mediaType="application/json",
  *     @OA\Schema(
  *       required={"data"},
  *       @OA\Property(
  *         property="data",
  *         type="Array|String|Number",
  *         format="Integer|Enum",
  *         emun="{"opcion"}",
  *         example="valueString",
  *         example={
  *           "key": "valueString" | valueInt
  *         },
  *         example=valueInt
  *       )
  *     )
  *   )
  *  ),
  *  @OA\Response(
  *    response=200,
  *    description="OK"
  *  )
  * )
  */
```

### Ejemplo de uso para Delete

```TXT
/**
  * @OA\Delete(
  *  path="/api/v1/{nameController}/{key}",
  *  tags={"Example"},
  *  summary="Eliminar la información",
  *  description="Se encarga de eliminar la información.",
  *  operationId="example-delete",
  *  security={{"bearerAuth":{}}},
  *  @OA\Parameter(
  *    name="key",
  *    in="key",
  *    required=true,
  *    @OA\Schema(
  *      type="number",
  *    )
  *  ),
  *  @OA\Response(
  *    response=200,
  *    description="OK"
  *  )
  * )
  */
```

### Template para casos POST y PUT

```TXT
*@OA\Components(
* @OA\Schema(
*   schema="exampleSave",
*   description="Este squema se encarga de crear una elemento que puede ser usado en diferentes funciones donde se usen los campos aquí establecidos.",
*   required={"keyRequired","example"},
*   @OA\Property(
*     property="keyRequired",
*     type="string",
*     enum={"example0","example1","example2","example3"},
*     example="title"
*   ),
*   @OA\Property(
*     property="example",
*     type="string",
*     description="Lo que se requiere usar",
*     example="Lorem Impsun",
*   )
* )
*)
```

Ha esto es lo que se llama crear un componete que se puede usar en diferentes funciones, la manera de uso:

```TXT
@OA\RequestBody(
* @OA\MediaType(
*  mediaType="application/json",
*  @OA\Schema(
*    ref="#/components/schemas/exampleSave"
*  )
* ),
*),
```

---

## Instalación y configuración de UI Scaffolding

Hay que instalar la dependencia del Scaffolding en este caso así:

> composer require laravel/ui

Una vez instalado hay que selecionar el scaffolding(Tecnologia de Front), se puede usar: **Vue.js**, **React**, **bootstrap**

> php artisan ui bootstrap
>
> php artisan ui vue
>
> php artisan ui react

Recordando que si queremos que agregue las configuración de autenticación se le debe agregar el flag de **--auth**

Además de tener en cuenta que esto lo determina el director del área de desarrollo, una vez el proyecto sea definido.

Para mayor información de instalación y configuración de esto [aquí](https://laravel.com/docs/7.x/frontend#introduction)

---

## Instalación y configuración de CoreUI Free

Descargar el paquede de CoreUI

> npm install @coreui/coreui

Agregar a **bootstrap.js** está en **resources/js/bootstrap.js** después de **require('bootstrap');**

>require('@coreui/coreui');

Agregar a **app.scss** está en **resources/sass/app.scss**

>@import '~@coreui/coreui/dist/css/coreui.min.css';

Esto permitira que Coreui funcione correctamente en la plataforma, sin embargo antes de esto es importante tener configurado el UI Scaffolding, explicado en la sección anterior.
