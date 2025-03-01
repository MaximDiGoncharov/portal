<?php

class dashaMail
{
   public static string|false $html_body = false;
   public static string|false  $obj_link = false;
   public static array $request_headers =  [
      'Content-Type: application/x-www-form-urlencoded'
   ];
   public static  $dasha_mail_templates = [
      'activation' => DASHA_TEMPLATE_MAIL .  "activation",
   ];

   public static function load_template(string $name_template, array $tempalte_params): string
   {
      $html_template = file_get_contents(self::$dasha_mail_templates[$name_template]);
      foreach ($tempalte_params as $key => $value) {
         $html_template = str_replace('$' . $key, $value, $html_template);
      }
      return $html_template;
   }

   public static function mail_pass(string $email, string $password, string $from = FROM)
   {
      $title = 'Регистрация успешно завершена';
      $tempalte_params = ['password' => $password, 'login' => $email, 'message' => DOMAIN];
      self::$html_body = $mail_body_html = self::load_template('registration_success', $tempalte_params);
      $fields = ['to' => $email, 'subject' => $title, 'from_email' => $from, 'message' => $mail_body_html];
      send_request(DASHA_URL_TRANSACTION,  self::$request_headers,  $fields);
   }


   public static function mail_token(string $mail, $token, $domain = null)
   {
      $link = '<a style="display: table-cell; text-decoration: none; padding: 15px 30px; font-size: 15px; text-align: center; font-weight: bold; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; max-width:250px; width: 100%; color: #ffffff; border: 0px solid; background-color: #f96967; border-radius: 3px;" href="https://' . $domain . 'dbError/activate/' . $mail . '/' . $token . '"  style="text-decoration:none;color:#0089bf" target="_blank" rel="noopener noreferrer">Активировать</a>';
      $host = '<a name="myname" style=\'text-decoration: none; color:#000000\';>' . $domain . 'dbError/activate/' . $mail . '/' . $token . '</a>';

      $title = 'Активация';
      $tempalte_params = ['activation_link' => $link,  'activation_string'=>$host];
      self::$html_body = $mail_body_html = self::load_template('activation', $tempalte_params);
      $fields = ['to' => $mail, 'subject' => $title, 'from_email' => FROM, 'message' => $mail_body_html];
      send_request(DASHA_URL_TRANSACTION,  self::$request_headers,  $fields);
   }
}
