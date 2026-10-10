<?php

class ProdiRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM prodi ORDER BY nama ASC"
        );

        return $stmt->fetchAll();
    }
}