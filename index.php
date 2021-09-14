<?php 
session_start();
/*
aplikacja pobiera ilość cukierków z bazy danych
CREATE TABLE `settings` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `settings` (`name`, `value`) VALUES
('candy', '2');
*/
class database {
    function __construct() {
        try {
            $this->database = new PDO("mysql:host=localhost;dbname=merce", "root", "password", [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        catch(PDOException $error) {
            exit($error->getMessage());
        }
    }
    public function query($query)
    {
        return $this->database->query($query)->fetch();
    }
}
class mashine {
    /*
    * funkcja sprawdza wrzucenie pieniędzy, czy wrzucony pieniążek to 2 i czy pokrętło do wyrzucenia cukierka nie jest otwarte
    */
    public function mashineinputmoney($howmany) {
        $database = new database;
        if(isset($_SESSION['checkpositionmashine']) && $_SESSION['checkpositionmashine'] == "open") {
            echo "Twoje pieniądze zostały zwrócone ponieważ maszyna jest odblokowana do wydania curkierka";
        }else {
            if($howmany == "2") {
                $showcandy = $database->query("SELECT * FROM settings WHERE name='candy'");
                if($showcandy['value'] > 0)
                {
                    //pokrętło sotało odblokowane
                    $_SESSION['checkpositionmashine'] = "open";
                    echo "teraz przekręć pokrętło żeby dostać cukierek";
                }else {
                    echo "W maszynie nie ma już cukierków, twoje pieniądze zostały zwrócone";
                //
                }
            } else {
                echo "Możesz wrzucić tylko 2 zł";
            }
        }
    }

    /*
    * funkcja odpowiedzialna za sprawdzenie pokrętła
    */
    public function mashinechangeposition($positionofknob) {
        if($positionofknob == "close") {
            if($_SESSION['checkpositionmashine'] == "open") {
                //cukierek został wydany i pokrętło zostało obrócone do trybu zamkniętego
                $database = new database;
                $getcandy = $database->query("SELECT * FROM settings WHERE name='candy'");
                $removecandy = $getcandy['value'] - 1;
                $updatecandy = $database->query("UPDATE settings SET value='$removecandy' WHERE name='candy'");
                echo "cukierek został wydany";
                $_SESSION['checkpositionmashine'] = "close";
            }
        }
    }

    /*
    * funkcja daje możliwość dodania cukierków przez modelatora
    */
    public function addcandybymodelator($varible) {
        if (preg_match('/^[0-9]*$/', $varible)) {
            $database = new database;
            $getcandy = $database->query("SELECT * FROM settings WHERE name='candy'");
            $addcandy = $getcandy['value'] + $varible;
            $addcandys = $database->query("UPDATE settings SET value='$addcandy' WHERE name='candy'");
            echo "Cukierki zostały dodane";
        } else {
            echo "Żeby dodać cukierki liczba musi być całkowita";
        }
    }
}
?>