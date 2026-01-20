<?php 
// $database = Database::getInstance()->getConnection(); <= pour intéragir avec la base de données !!!!
// $query = $database->query("SELECT * FROM table");
// $a = $query->fetchAll(PDO::FETCH_ASSOC);
// print_r($a);

class Partenaire {

    public $id;
    public $logo;
    public $nom;

    public function __construct($id, $logo, $nom) {
        $this->id = $id;
        $this->logo = $logo;
        $this->nom = $nom;
    }

    public function uploadLogo($file) {
        $uploadDir = __DIR__ . '/images/sponsors/';

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $this->id . '.' . $extension;

        move_uploaded_file($file['tmp_name'], $filePath);
        $this->logo = $this->id . '.' . $extension;

        $db = Database::getInstance()->getConnection();

        $sql = "UPDATE partenaire 
            SET photo = :photo
            WHERE id_partenaire = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'id' => $this->id,
            'photo' => $this->logo
            ]);
    }

    public function delete() {
        if ($this->id === null) {
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM partenaire WHERE id_partenaire = :id"); 
        $stmt->execute(['id' => $this->id]);

        $this->id = null;
    }

    public function save() {
        $db = Database::getInstance()->getConnection();
        if ($this->id === null) {
            // CREATE
            $sql = "INSERT INTO partenaire (photo, nom_societe)
                    VALUES (:photo, :nom_societe)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'photo' => $this->logo,
                'nom_societe' => $this->nom
            ]);
            $this->id = (int)$db->lastInsertId();
        } else {
            // UPDATE
            $sql = "UPDATE partenaire 
                    SET nom_societe = :nom_societe
                    WHERE id_partenaire = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => $this->id,
                'nom_societe' => $this->nom
            ]);
        }
    }
}

?>