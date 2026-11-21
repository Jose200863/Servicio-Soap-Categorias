<?php
/**
 * Middleware para el Servicio SOAP de Categorías
 * 
 * Responsabilidades (Persona 3):
 * 1. Registro (Logging): Fecha, hora, operación solicitada y parámetros (sin datos sensibles).
 * 2. Validación: Validar parámetros antes de consultar la base de datos / repositorio.
 */

class Middleware
{
    private string $logFile;
    private array $clavesSensibles = ['password', 'contrasena', 'clave', 'token', 'secret'];

    public function __construct(?string $logFile = null)
    {
        $this->logFile = $logFile ?? __DIR__ . '/soap_requests.log';
    }

    /**
     * Intercepta la llamada a cualquier operación para registrar y validar antes
     * de consultar el repositorio.
     *
     * @param string $operacion Nombre de la operación solicitada
     * @param array $parametros Parámetros recibidos
     * @param callable $siguiente Callback que ejecuta la consulta al repositorio
     * @return array Estructura de respuesta compatible con el contrato WSDL
     */
    public function ejecutar(string $operacion, array $parametros, callable $siguiente): array
    {
        // 1. REGISTRO (Logging): fecha, hora, operación y parámetros (sin datos sensibles)
        $this->registrar($operacion, $parametros);

        // 2. VALIDACIÓN: verificar parámetros antes de consultar el repositorio
        $error = $this->validar($operacion, $parametros);
        if ($error !== null) {
            return [
                'exito'   => false,
                'mensaje' => $error
            ];
        }

        // 3. CONTINUAR: ejecutar la operación en el repositorio
        return $siguiente();
    }

    /**
     * Registra en archivo de log con fecha, hora y parámetros filtrados.
     */
    public function registrar(string $operacion, array $parametros): void
    {
        $fechaHora = date('Y-m-d H:i:s');
        $filtrados = $this->filtrarSensibles($parametros);
        $jsonParam = json_encode($filtrados, JSON_UNESCAPED_UNICODE);

        $linea = sprintf("[%s] [OPERACION: %s] [PARAMETROS: %s]%s", $fechaHora, $operacion, $jsonParam, PHP_EOL);
        file_put_contents($this->logFile, $linea, FILE_APPEND | LOCK_EX);
    }

    /**
     * Oculta información sensible como contraseñas antes de escribir en el log.
     */
    private function filtrarSensibles(array $datos): array
    {
        $resultado = [];
        foreach ($datos as $clave => $valor) {
            if (is_array($valor)) {
                $resultado[$clave] = $this->filtrarSensibles($valor);
            } elseif (in_array(strtolower($clave), $this->clavesSensibles, true)) {
                $resultado[$clave] = '********';
            } else {
                $resultado[$clave] = $valor;
            }
        }
        return $resultado;
    }

    /**
     * Valida los parámetros requeridos según la operación.
     * Devuelve mensaje de error si falla, o null si es válido.
     */
    public function validar(string $operacion, array $parametros): ?string
    {
        switch ($operacion) {
            case 'actualizarCategoria':
            case 'agregarCategoria':
                if (!isset($parametros['codigo']) || !is_numeric($parametros['codigo']) || (int)$parametros['codigo'] <= 0) {
                    return "Error de validación (Middleware): El código debe ser un número entero mayor a 0.";
                }
                if (!isset($parametros['nombre']) || trim((string)$parametros['nombre']) === '') {
                    return "Error de validación (Middleware): El nombre de la categoría no puede estar vacío.";
                }
                $longitud = mb_strlen(trim((string)$parametros['nombre']));
                if ($longitud < 2 || $longitud > 100) {
                    return "Error de validación (Middleware): El nombre debe tener entre 2 y 100 caracteres.";
                }
                break;

            case 'buscarCategoria':
            case 'eliminarCategoria':
                if (!isset($parametros['codigo']) || !is_numeric($parametros['codigo']) || (int)$parametros['codigo'] <= 0) {
                    return "Error de validación (Middleware): El código debe ser un número entero mayor a 0.";
                }
                break;
        }

        return null;
    }
}
