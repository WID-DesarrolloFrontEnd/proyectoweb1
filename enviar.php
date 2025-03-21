<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = htmlspecialchars($_POST['email']);
    $mensaje = htmlspecialchars($_POST['mensaje']);

    // Dirección de correo a la que se enviará el mensaje
    $destinatario = "widinformaciones@gmail.com"; // Cambia esta dirección por tu correo

    // Asunto del correo
    $asunto = "Nuevo mensaje de contacto";

    // Verificar si hay archivo adjunto
    $tieneAdjunto = isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0;

    if ($tieneAdjunto) {
        $archivo_tmp = $_FILES['archivo']['tmp_name'];
        $archivo_nombre = $_FILES['archivo']['name'];
        $archivo_tipo = $_FILES['archivo']['type'];
        $archivo_tamano = $_FILES['archivo']['size'];

        // Verifica que el archivo no sea muy grande
        if ($archivo_tamano > 5000000) { // 5 MB máximo
            echo "El archivo es demasiado grande. El tamaño máximo permitido es 5 MB.";
            exit;
        }
    }

    // Crea un límite para el correo
    $boundary = md5(time());

    // Cabeceras del correo
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Cuerpo del mensaje
    $mensajeCorreo = "--$boundary\r\n";
    $mensajeCorreo .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $mensajeCorreo .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $mensajeCorreo .= "Has recibido un nuevo mensaje de:\r\n";
    $mensajeCorreo .= "Nombre: $nombre\r\n";
    $mensajeCorreo .= "Correo: $email\r\n\r\n";
    $mensajeCorreo .= "Mensaje:\r\n$mensaje\r\n\r\n";

    // Adjuntar el archivo si existe
    if ($tieneAdjunto) {
        $file_data = file_get_contents($archivo_tmp);
        $file_data = chunk_split(base64_encode($file_data));

        $mensajeCorreo .= "--$boundary\r\n";
        $mensajeCorreo .= "Content-Type: $archivo_tipo; name=\"$archivo_nombre\"\r\n";
        $mensajeCorreo .= "Content-Transfer-Encoding: base64\r\n";
        $mensajeCorreo .= "Content-Disposition: attachment; filename=\"$archivo_nombre\"\r\n\r\n";
        $mensajeCorreo .= $file_data . "\r\n";
    }

    // Cerrar el límite
    $mensajeCorreo .= "--$boundary--\r\n";

    // Enviar el correo
    if (mail($destinatario, $asunto, $mensajeCorreo, $headers)) {
        echo "El mensaje se ha enviado correctamente.";
    } else {
        echo "Hubo un error al enviar el mensaje.";
    }
}
