<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    // 🔒 Constructeur privé
    private function __construct()
    {
        global $hote, $port, $nom_bd, $identifiant, $mot_de_passe, $encodage, $debug;

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $encodage,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($debug) {
            $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        }

        try {
            $this->connection = new PDO(
                "mysql:host=$hote;port=$port;dbname=$nom_bd",
                $identifiant,
                $mot_de_passe,
                $options
            );
        } catch (PDOException $e) {
            echo "Serveur actuellement inaccessible, veuillez nous excuser.";
            exit();
        }
    }

    // 🔁 Empêche le clonage
    private function __clone() {}

    // 🔁 Empêche la désérialisation
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    // ✅ Point d’accès unique
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // 🔌 Accès à PDO
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}


?>