<?php
    class ServiceAPI
    {

        private $key = '4013b00817038b88698619efd8f80327';
        public $cep1 = "";
        public $cep2 = "";

        // Function responsible for making requests in the API and returning the information in json.
        public function request($uri, $type_request)
        {
            if (!empty($uri)) {
                try {
                    $request = curl_init();
                    curl_setopt($request, CURLOPT_HTTPHEADER, array('Authorization: Token token='.$this->key)); // Access token for request.
                    curl_setopt($request, CURLOPT_URL, $uri);                                                     // Request URL.
                    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 5);            // Connect time out.
                    curl_setopt($request, CURLOPT_CUSTOMREQUEST, $type_request); // HTPP Request Type.
                    $file_contents = curl_exec($request);
                    curl_close($request);

                    return $file_contents;

                } catch (Exception $e) {
                    return $e->getMessage();
                }
            }
        }

        // Preparation of parameters and URL for searching zip codes.
        public function getCep1()
        {
            $type_request = "GET";
            $params       = 'cep='.$this->cep1;
            $uri          = "https://www.cepaberto.com/api/v3/cep?".$params;

            if (!empty($params)) {
                return $this->request($uri, $type_request);
            }
        }

        // Preparation of parameters and URL for searching zip codes.
        public function getCep2()
        {
            $type_request = "GET";
            $params       = 'cep='.$this->cep2;
            $uri          = "https://www.cepaberto.com/api/v3/cep?".$params;

            if (!empty($params)) {
                return $this->request($uri, $type_request);
            }
        }

        /*  Set */
        public function set($atr, $val)
        {
            $this->$atr = $val;
        }

        /*  Get */
        public function get($atr)
        {
            return $this->$atr;
        }
    }

    class CoordDistance
    {
        public $lat_a = 0;
        public $lon_a = 0;
        public $lat_b = 0;
        public $lon_b = 0;

        public $measure_unit  = 'km';
        public $measure_state = false;
        public $measure       = 0;
        public $error         = '';

        public function DistAB()
        {
            $delta_lat = $this->lat_b - $this->lat_a;
            $delta_lon = $this->lon_b - $this->lon_a;

            $earth_radius = 6372.795477598;

            $alpha    = $delta_lat / 2;
            $beta     = $delta_lon / 2;
            $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($this->lat_a)) * cos(deg2rad($this->lat_b)) * sin(deg2rad($beta)) * sin(deg2rad($beta));
            $c        = asin(min(1, sqrt($a)));
            $distance = 2 * $earth_radius * $c;
            $distance = round($distance, 4);

            $this->measure = round($distance, 2);

        }

        /*  Set */
        public function set($atr, $val)
        {
            $this->$atr = $val;
        }

        /*  Get */
        public function get($atr)
        {
            return $this->$atr;
        }

    }

    class Distancia
    {
        public $cd             = "";
        public $origem         = "";
        public $destino        = "";
        public $distancia      = "";
        public $dt_cadastro    = "";
        public $dt_atualizacao = "";

        /*  Set */
        public function set($atr, $val)
        {
            $this->$atr = $val;
        }

        /*  Get */
        public function get($atr)
        {
            return $this->$atr;
        }

        public function Add()
        {
            $mySQL = new MySQL();
            $sql   = "INSERT INTO
                        distancias
                    (
                        origem
                        , destino
                        , distancia
                        , dt_cadastro
                        , dt_atualizacao
                    ) VALUES (
                        '$this->origem'
                        , '$this->destino'
                        , '$this->distancia'
                        , '$this->dt_cadastro'
                        , '$this->dt_atualizacao'
                    );";
            $mySQL->runQuery($sql);
        }

        public function Update()
        {
            $mySQL = new MySQL();
            $sql = "UPDATE
                        distancias
                    SET
                        origem = '$this->origem'
                        , destino = '$this->destino'
                        , distancia = '$this->distancia'
                        , dt_atualizacao = '$this->dt_atualizacao'
                    WHERE
                        cd = $this->cd";
            $mySQL->runQuery($sql);
        }

        public function Delete()
        {
            $mySQL = new MySQL();
            $sql = "DELETE FROM
                        distancias
                    WHERE
                        cd = $this->cd";
            $mySQL->runQuery($sql);
        }

        public function GetData($cd = "")
        {
            $mySQL = new MySQL();
            $sql   = "SELECT * FROM distancias WHERE 1=1";
            $sql  .= (!empty($cd)) ? " AND cd = {$cd}" : "";
            $rs    = $mySQL->runQuery($sql);
            $i     = 0;
            while ($row = mysqli_fetch_assoc($rs)) {

                $arrayObj[$i] = new Distancia();
                $props        = get_class_vars(get_class($this));
                foreach ($props as $prop => $valor) {
                    $arrayObj[$i]->$prop = $row[$prop];
                }
                $i++;

            }

            return ($i > 0) ? $arrayObj : '';
        }
    }



    #   Autoload classes
    //function __autoload($class)
    //{require_once $class.".class.php";}


    /*===============================ADD================================*/
    if (isset($_GET["act"]) && $_GET["act"] == "add") {

        $cep_origem  = $_GET["origem"];
        $cep_destino = $_GET["destino"];
        $api         = new ServiceAPI();
        $api->set("cep1", $cep_origem);
        $api->set("cep2", $cep_destino);
        $origem = json_decode($api->getCep1());
        if (empty($origem->cidade)) {
            die("CEP {$cep_origem} invalido!!!");
        }

        sleep(1);
        $destino = json_decode($api->getCep2());
        if (empty($destino->cidade)) {
            die("CEP {$cep_destino} invalido!!!");
        }

        $dist = new CoordDistance();
        $dist->set("lat_a", $origem->latitude);
        $dist->set("lon_a", $origem->longitude);
        $dist->set("lat_b", $destino->latitude);
        $dist->set("lon_b", $destino->longitude);
        $dist->DistAB();

        echo "<p style='color: #062cfb'>Origem: </p>".$origem->cidade->nome." (latitude: {$origem->latitude}, longitude: {$origem->longitude})";
        echo "<p style='color: #062cfb'>Destino: </p>".$destino->cidade->nome." (latitude: {$destino->latitude}, longitude: {$destino->longitude})";

        echo "<p style='color: #062cfb'>Distancia: </p>".$dist->measure." ".$dist->measure_unit;

        // ADD
        $d = new Distancia();
        $d->set("origem", $cep_origem);
        $d->set("destino", $cep_destino);
        $d->set("distancia", $dist->measure." ".$dist->measure_unit);
        $d->set("dt_cadastro", date("Y-m-d H:i:s"));
        $d->set("dt_atualizacao", "0000-00-00 00:00:00");

        $d->Add();

        ?>
        <script>
            setTimeout(function() {
                window.location.href = "index.php";
            }, 3000);
        </script>
        <?php
    }
    /*===============================ADD================================*/

    /*===============================EDIT================================*/
    if (isset($_GET["act"]) && $_GET["act"] == "edit") {

        $cep_origem  = $_GET["origem"];
        $cep_destino = $_GET["destino"];
        $api         = new ServiceAPI();
        $api->set("cep1", $cep_origem);
        $api->set("cep2", $cep_destino);
        $origem = json_decode($api->getCep1());
        if (empty($origem->cidade)) {
            die("CEP {$cep_origem} invalido!!!");
        }

        sleep(1);
        $destino = json_decode($api->getCep2());
        if (empty($destino->cidade)) {
            die("CEP {$cep_destino} invalido!!!");
        }

        $dist = new CoordDistance();
        $dist->set("lat_a", $origem->latitude);
        $dist->set("lon_a", $origem->longitude);
        $dist->set("lat_b", $destino->latitude);
        $dist->set("lon_b", $destino->longitude);
        $dist->DistAB();

        echo "<p style='color: #062cfb'>Origem: </p>".$origem->cidade->nome." (latitude: {$origem->latitude}, longitude: {$origem->longitude})";
        echo "<p style='color: #062cfb'>Destino: </p>".$destino->cidade->nome." (latitude: {$destino->latitude}, longitude: {$destino->longitude})";

        echo "<p style='color: #062cfb'>Distancia: </p>".$dist->measure." ".$dist->measure_unit;

        // ADD
        $d = new Distancia();
        $d->set("cd", $_GET["cd"]);
        $d->set("origem", $cep_origem);
        $d->set("destino", $cep_destino);
        $d->set("distancia", $dist->measure." ".$dist->measure_unit);
        $d->set("dt_atualizacao", date("Y-m-d H:i:s"));
        $d->Update();

        ?>
        <script>
            setTimeout(function() {
                window.location.href = "index.php";
            }, 3000);
        </script>
        <?php
    }
    /*===============================EDIT================================*/

    /*===============================DELETE================================*/
    if (isset($_GET["act"]) && $_GET["act"] == "exc") {
        // EXC
        $d = new Distancia();
        $d->set("cd", $_GET["cd"]);
        $d->Delete();
    }
    /*===============================DELETE================================*/
    ?>