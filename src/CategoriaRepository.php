<?php
// Acceso a datos con consultas preparadas.
class CategoriaRepository
{
    private PDO $db;
    private string $t;

    public function __construct(PDO $db, string $tabla)
    {
        $this->db = $db;
        $this->t  = preg_replace('/[^A-Za-z0-9_]/', '', $tabla);
    }

    public function todas(): array
    {
        $r = $this->db->query("SELECT codigo, nombre FROM {$this->t} ORDER BY codigo")->fetchAll();
        return array_map([$this, 'normalizar'], $r);
    }

    public function buscar(int $codigo): ?array
    {
        $s = $this->db->prepare("SELECT codigo, nombre FROM {$this->t} WHERE codigo = ?");
        $s->execute([$codigo]);
        $f = $s->fetch();
        return $f ? $this->normalizar($f) : null;
    }

    public function insertar(int $codigo, string $nombre): void
    {
        $s = $this->db->prepare("INSERT INTO {$this->t} (codigo, nombre) VALUES (?, ?)");
        $s->execute([$codigo, $nombre]);
    }

    public function actualizar(int $codigo, string $nombre): void
    {
        $s = $this->db->prepare("UPDATE {$this->t} SET nombre = ? WHERE codigo = ?");
        $s->execute([$nombre, $codigo]);
    }

    public function eliminar(int $codigo): void
    {
        $s = $this->db->prepare("DELETE FROM {$this->t} WHERE codigo = ?");
        $s->execute([$codigo]);
    }

    private function normalizar(array $f): array
    {
        return ['codigo' => (int)$f['codigo'], 'nombre' => (string)$f['nombre']];
    }
}

