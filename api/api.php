<?php
header("Access-Control-Allow-Origin: *");  // Permite solicitudes desde cualquier dominio
header("Access-Control-Allow-Methods: POST");  // Permite solo solicitudes POST
header("Content-Type: application/json");  // Indica que los datos enviados son en formato JSON

// Configuración de conexión a la base de datos
$servername = "autorack.proxy.rlwy.net"; // Obtén el host de Railway, ejemplo: containers-us-west-1.railway.app
$username = "root";   // El nombre de usuario de tu base de datos en Railway
$password = "oMtfTSfbUBXsLoiPOAGqCNdTVPoOhEFP."; // La contraseña de la base de datos en Railway
$dbname = "menu"; // El nombre de tu base de datos en Railway
// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Leer el cuerpo de la solicitud
$inputData = file_get_contents("php://input");
// Depuración: Verifica qué datos llegan
if (empty($inputData)) {
    echo "No se recibieron datos.";
    exit;
}

echo "Datos recibidos: " . $inputData;

// Verificar si el cuerpo de la solicitud está vacío
if (empty($inputData)) {
    echo "Datos de la orden vacíos";
    exit;
}

// Decodificar el JSON recibido
$data = json_decode($inputData, true);

// Verificar si la decodificación fue exitosa
if ($data === null) {
    echo "Error al decodificar el JSON";
    exit;
}

// Verificar si se recibió la orden correctamente
if (isset($data['orders']) && is_array($data['orders'])) {
    // Preparar una consulta para insertar las órdenes en la base de datos
    foreach ($data['orders'] as $order) {
        $id_usuario = $order['id_usuario'];
        $id_producto = $order['id_producto'];
        $cantidad = $order['cantidad'];

        // Escapar las entradas para prevenir inyecciones SQL
        $id_usuario = $conn->real_escape_string($id_usuario);
        $id_producto = $conn->real_escape_string($id_producto);
        $cantidad = $conn->real_escape_string($cantidad);

        // Inserción de la orden en la tabla productosxusuario
        $sql = "INSERT INTO productosxusuario (id_usuario, id_producto, cantidad) 
                VALUES ('$id_usuario', '$id_producto', '$cantidad')";

        if ($conn->query($sql) === TRUE) {
            echo "Nueva orden registrada correctamente";
        } else {
            echo "Error al registrar la orden: " . $conn->error;
        }
    }
} else {
    echo "Datos de orden no válidos";
}

// Cerrar la conexión
$conn->close();
?>





