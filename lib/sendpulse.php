<?php
class sendpulse{

   static public function create_book(array $param){
      $url = SENDPULSE_URL . 'addressbooks';
      if (!empty($param['bookName'])) {
         return self::query($param, $url);
      }
      return null;
   }

   static public function  add_contact_to_book(array|false $param, int $book_id = SENDPULSE_BOOK_ID){
      $url = SENDPULSE_URL . 'addressbooks/' . $book_id . '/emails';
      $data['emails'] = [];
      if (!$param) {
         $param = owner::get_list(false, true);
      } 

      foreach ([$param] as $row) {
         $obj['email'] = $row['user_email'];
         $obj['variables']['Имя'] = $row['user_name'];
         $obj['variables']['Phone'] = $row['user_phone'];
         $obj['variables']['domain'] = $row['user_domain'];
         $obj['variables']['дата регистрации'] = date("d.m.y",  $row['user_register_date']);
         $data['emails'][] = $obj;
      }
      return self::query($data, $url);
   }
   
   static public  function query(array $param, string $url = SENDPULSE_URL,$token = false, string|null $method = 'POST', ){
   $headers = ['Content-Type:application/json'];
      if (!$token) {
         $token = get_access_token();
      }
      array_push($headers, 'Authorization: Bearer ' . $token);

   $ch = curl_init($url);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param, JSON_UNESCAPED_UNICODE));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HEADER, false);
   $res = curl_exec($ch);
   curl_close($ch);
   $res = json_decode($res, JSON_UNESCAPED_UNICODE);
   return $res;
   }

}