<?php

$directorio = './tablas';
$archivos_tablas = scandir($directorio,1);
$tablas_compartidas = [];
$continuar_comparacion = false;
$linea = "";
$linea_comparar = "";

echo "<pre>";

# Iteracion de los archivos
# Recorro el listado de archivos para cada base de datos
foreach ($archivos_tablas as $archivo) :

   # Lectura del archivo
   # Leo las tablas de cada archivo que representa cada base de datos
   $archivo_abierto = fopen("$directorio/$archivo", "r");

   # Obtengo arreglo con separacion de caracteres para tomar el nombre de la base de datos
   $nombre_tabla = explode("_",$archivo);
   $nombre_tabla = $nombre_tabla[1];

   # Iteracion del contenido del archivo que contiene todas las tablas de la base de datos
   while (!feof($archivo_abierto)) :

      # Leo el nombre de la tabla una a una
      $linea = fgets($archivo_abierto);

      # Recorro el listado de archivos de bases de datos para buscar la similitud de tablas entre bases de datos
      foreach ($archivos_tablas as $archivo_comparar):

         # Lectura del archivo
         # Leo las tablas nuevamente para identificar la tabla similar entre todas las bases de datos
         $archivo_comparar_abierto = fopen("$directorio/$archivo_comparar", "r");

         # Obtengo el nombre de la tabla a comparar para no comparar con la misma tabla
         $nombre_tabla_comparar = explode("_",$archivo_comparar);
         $nombre_tabla_comparar = $nombre_tabla_comparar[1];

         # Valido que no sea la misma tabla a comparar
         if ($nombre_tabla != $nombre_tabla_comparar && $continuar_comparacion == true) :

            # Creo arreglo temporal para insertar el nombre de las tablas que luego comprobare si hay existencia
            # de la linea que viene en el primer recorrido
            $arr = [];

            while (!feof($archivo_comparar_abierto)) :
               # Leo el nombre de la tabla que deseo comparar una a una
               $linea_comparar = fgets($archivo_comparar_abierto);

               # Inserto contenido de datos en un arreglo que utilizare para ver si la tabla existe en el array
               array_push($arr, $linea_comparar);
            endwhile;

            # Valido si la tabla (linea) existe en la comparacion
            if (in_array($linea,$arr)) :
               $continuar_comparacion = true;
            else:
               $continuar_comparacion = false;
            endif;

         else:
            # Si se detiene la comparacion es porque la tabla no existe en alguna de las bases de datos
            break;
         endif;

      endforeach;

      # Valido si ya inserté el nombre de la tabla compartida
      if ($continuar_comparacion == true):
         if (!in_array(trim($linea),$tablas_compartidas)) :
            array_push($tablas_compartidas, trim($linea));
         endif;
      endif;

      # Reset de la variable de control de comparacion
      $continuar_comparacion = true;

   endwhile;


endforeach;
asort($tablas_compartidas);
#var_dump($tablas_compartidas);
#die();
foreach ($tablas_compartidas as $tabla_compartida):
   echo "$tabla_compartida<br>";
endforeach;
echo "</pre>";