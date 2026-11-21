<?php


class Middleware
{
    private string $logFile;
    private array $clavesSensibles = ['password', 'contrasena', 'clave', 'token', 'secret'];

    public function __construct(?string $logFile = null)
    {
        $this->logFile = $logFile ?? __DIR__ . '/soap_requests.log';
    }


    public function ejecutar(string $operacion, array $parametros, callable $siguiente): array
    {
        $this->registrar($operacion, $parametros);

        $error = $this->validar($operacion, $parametros);
        if ($error !== null) {
            return [
                'exito'   => false,
                'mensaje' => $error
            ];
        }

        return $siguiente();
    }

    public function registrar(string $operacion, array $parametros): void
    {
        $fechaHora = date('Y-m-d H:i:s');
        $filtrados = $this->filtrarSensibles($parametros);
        $jsonParam = json_encode($filtrados, JSON_UNESCAPED_UNICODE);

        $linea = sprintf("[%s] [OPERACION: %s] [PARAMETROS: %s]%s", $fechaHora, $operacion, $jsonParam, PHP_EOL);
        file_put_contents($this->logFile, $linea, FILE_APPEND | LOCK_EX);
    }

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
