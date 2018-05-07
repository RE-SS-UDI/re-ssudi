<?php
    //Funcion que valida si el RFC ingresado por el usuario es valido
    function rfcValido($valor) {
           $valor = str_replace("-", "", $valor);
           $cuartoValor = substr($valor, 3, 1);
           //RFC sin homoclave
           if(strlen($valor)==10) {
               $letras = substr($valor, 0, 4);
               $numeros = substr($valor, 4, 6);
               if (ctype_alpha($letras) && ctype_digit($numeros)) {
                   return true;
               }
               return false;
           }
           // Sólo la homoclave
           else if (strlen($valor) == 3) {
               $homoclave = $valor;
               if(ctype_alnum($homoclave)){
                   return true;
               }
               return false;
           }
           //RFC Persona Moral.
           else if (ctype_digit($cuartoValor) && strlen($valor) == 12) {
               $letras = substr($valor, 0, 3);
               $numeros = substr($valor, 3, 6);
               $homoclave = substr($valor, 9, 3);
               if (ctype_alpha($letras) && ctype_digit($numeros) && ctype_alnum($homoclave)) {
                   return true;
               } 
               return false;
           //RFC Persona Física. 
           } else if (ctype_alpha($cuartoValor) && strlen($valor) == 13) {
               $letras = substr($valor, 0, 4);
               $numeros = substr($valor, 4, 6);
               $homoclave = substr($valor, 10, 3);
               if (ctype_alpha($letras) && ctype_digit($numeros) && ctype_alnum($homoclave)) {
                   return true;
               }
               return false;
           }else {
               return false;
           } 
    }
?>
