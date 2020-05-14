<?php

namespace App\Http\Requests;

use App\Utilities\helpers;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory;

class BaseRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }
  /**
   * Get custom attributes for validator errors.
   *
   * @return array
   */
  public function attributes()
  {
    return [];
  }
  /**
   * Get the validation messages.
   *
   * @return array
   */
  public function messages()
  {
    return [
      '*.required' => ':attribute es requerido.',
      '*.numeric'  => ':attribute debe ser un número',
      '*.email'    => ':attribute debe coincidir con una dirección de correo electronico',
      '*.date_format' => ':attribute debe coincidir con un formato de fecha',
      '*.min' => ':attribute debe cumplir con el tamaño mínimo que es de :value',
      '*.max' => ':attribute no debe contener más de :value caracteres.',
      '*.array' => ':attribute debe ser un arreglo de elementos',
      '*.required_if' => ':attribute es necesario cuando :other es :value',
      '*.digits_between' => ':attribute debe contener entre :min y :max',
      '*.unique' => ':attribute ya existe intente con uno diferente'
    ];
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      //
    ];
  }
  /**
   * Handle a failed validation attempt.
   *
   * @param  \Illuminate\Contracts\Validation\Validator  $validator
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  protected function failedValidation(Validator $validator)
  {
    $errors = (new ValidationException($validator))->errors();
    throw new HttpResponseException(helpers::ResponseJson("Ha sucedido un error procesando los datos", $errors, "ERROR", helpers::emptyocontent));
  }
  protected function failedAuthorization()
  {
    throw new HttpResponseException(helpers::ResponseJson("No se encuentra autenticado, acción no permitida", null, "ERROR", helpers::fail_auth));
  }
  /**
   * Get the validator instance for the request.
   *
   * @return \Illuminate\Validation\Validator
   */
  protected function getValidatorInstance()
  {
    $factory = $this->container->make(Factory::class);

    if (method_exists($this, 'validator')) {
      return $this->container->call([$this, 'validator'], compact('factory'));
    }
    return $factory->make(
      $this->json()->all(),
      $this->container->call([$this, 'rules']),
      $this->messages(),
      $this->attributes()
    );
  }
}
