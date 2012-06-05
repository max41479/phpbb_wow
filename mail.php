<?
   $to = 'cod41479@list.ru'; 
   $ot = 'cod41479@gmail.com'; 
   $subj = 'тема сообщения'; 
   $mess = 'само сообщение'; 

      mail($to, $subj, $mess, "From: ".$ot); 

echo 'сообщение отправлено по адресу: ' . $to;  
?>