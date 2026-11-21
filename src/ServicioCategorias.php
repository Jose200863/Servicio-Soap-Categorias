<?php
require_once __DIR__ . '/../middleware/Middleware.php';
require_once __DIR__ . '/CategoriaRepository.php';

class ServicioCategorias
{
    private CategoriaRepository $repo;
    private Middleware $mw;

    public function __construct(CategoriaRepository $repo, Middleware $mw)
    {
        $this->repo = $repo;
        $this->mw   = $mw;
    }

    private function campos($p): array
    {
        return [$p->codigo ?? null, $p->nombre ?? null];
    }

    public function agregarCategoria($p): array
    {
        [$codigo, $nombre] = $this->campos($p);
        return $this->mw->ejecutar('agregarCategoria', compact('codigo', 'nombre'), function () use ($codigo, $nombre) {
            if ($this->repo->buscar((int)$codigo)) {
                return ['exito' => false, 'mensaje' => "Ya existe una categoría con el código $codigo."];
            }
            $nombre = trim($nombre);
            $this->repo->insertar((int)$codigo, $nombre);
            return ['exito' => true, 'mensaje' => 'Categoría agregada correctamente.',
                    'categoria' => ['codigo' => (int)$codigo, 'nombre' => $nombre]];
        });
    }

    public function listarCategorias($p = null): array
    {
        return $this->mw->ejecutar('listarCategorias', [], function () {
            $todas = $this->repo->todas();
            return ['exito' => true, 'mensaje' => count($todas) . ' categoría(s) encontrada(s).',
                    'categorias' => $todas];
        });
    }

    public function buscarCategoria($p): array
    {
    $codigo = $p->codigo ?? null;

    return $this->mw->ejecutar('buscarCategoria', ['codigo' => $codigo], function () use ($codigo) {
        $categoria = $this->repo->buscar((int)$codigo);

        if ($categoria === null) {
            return [
                'exito' => false,
                'mensaje' => 'La categoría no existe.'
            ];
        }

        return [
            'exito' => true,
            'mensaje' => 'Categoría encontrada.',
            'categoria' => $categoria
        ];
      });
    }

    // ===== PERSONA 3: actualizarCategoria =====
    public function actualizarCategoria($p): array
    {
        [$codigo, $nombre] = $this->campos($p);
        return $this->mw->ejecutar('actualizarCategoria', compact('codigo', 'nombre'), function () use ($codigo, $nombre) {
            $categoria = $this->repo->buscar((int)$codigo);

            if ($categoria === null) {
                return [
                    'exito'   => false,
                    'mensaje' => "Error controlado: La categoría con código $codigo no existe."
                ];
            }

            $nombre = trim($nombre);
            $this->repo->actualizar((int)$codigo, $nombre);

            return [
                'exito'     => true,
                'mensaje'   => 'Categoría actualizada correctamente.',
                'categoria' => ['codigo' => (int)$codigo, 'nombre' => $nombre]
            ];
        });
    }

    // ===== PERSONA 4: eliminarCategoria =====
}



