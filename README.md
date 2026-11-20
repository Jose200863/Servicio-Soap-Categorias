# Servicio SOAP de Categorías - ITI-621

Servicio SOAP de categorías con PHP y MySQL, desarrollado con Visual Studio Code. Usa la misma base de datos del API REST.

## Estructura

- `soap.php`: endpoint del servicio (SoapServer)
- `wsdl/categorias.wsdl`: contrato del servicio (SOAP 1.1)
- `src/`: conexión a MySQL, repositorio y las 5 operaciones
- `middleware/`: registro (fecha, hora, operación) y validación de parámetros
- `catalogo/catalogo_servicios.json`: catálogo que simula UDDI
- `database/database.sql`: tabla y datos iniciales
- `config/config.example.php`: plantilla de configuración

## Operaciones

`agregarCategoria`, `listarCategorias`, `buscarCategoria`, `actualizarCategoria`, `eliminarCategoria`

## Requisitos

- XAMPP (Apache y MySQL)
- PHP con la extensión SOAP habilitada
- SQLyog
- Postman

## Instrucciones de ejecución

1. Copiar el proyecto a `C:\xampp\htdocs\Extraclase-Soap-Categorias`.
2. En `php.ini` activar `extension=soap` (sin `;`) y reiniciar Apache.
3. Iniciar Apache y MySQL desde XAMPP.
4. En SQLyog, ejecutar `database/database.sql` en la misma base de datos del API REST.
5. Copiar `config/config.example.php` como `config/config.php` y completar los datos de la base de datos.
6. Abrir el WSDL en el navegador para comprobar que carga.

## URLs

- Endpoint: `http://localhost/Extraclase-Soap-Categorias/soap.php`
- WSDL: `http://localhost/Extraclase-Soap-Categorias/wsdl/categorias.wsdl`

## Pruebas con Postman

- Método POST al endpoint
- Body: raw → XML
- Headers: `Content-Type: text/xml; charset=utf-8` y `SOAPAction` según el WSDL

Pruebas: agregar, listar y buscar, actualizar, eliminar, y al menos un error controlado (código repetido, nombre vacío o categoría inexistente). Los cambios se verifican en SQLyog.

## Integrantes

- Brayan Azofeifa Amador
- Oscar Mario Alvarez Cruz
- Jeshuan Joel Torres Gonzalez
- Isaac Alfredo Chaves Reyes 