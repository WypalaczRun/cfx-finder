<?php
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo "Fora Temer!";
    return;
}

if (empty($_POST['cfx'])) {
    $array = array(
        "status" => "N",
        "msg" => "Não deixe nenhum campo em branco!"
    );
    echo json_encode($array);
    return;
} else {
    $cfxlink = filter_var($_POST['cfx'], FILTER_SANITIZE_STRING);
    
    $cfxfinal = strtolower(RemoveCfx($cfxlink));
    
    if (strlen(strval($cfxfinal)) > 6) {
        $array = array(
            "status" => "N",
            "msg" => "Código inválido!"
        );
        echo json_encode($array);
        return;
    }
    $ch = curl_init("https://servers-frontend.fivem.net/api/servers/single/$cfxfinal");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data     = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    switch ($httpcode) {
        case 200:
            $dados = json_decode($data);
            $name  = $dados->Data->hostname;
            $ip    = $dados->Data->connectEndPoints[0];
            $geoip = GEOIp($ip);
            if ($geoip) {
                $dados1 = json_decode($geoip);
                if ($dados1->status === "Y") {
                    $country    = $dados1->country;
                    $ip1        = $dados1->ip;
                    $porta      = $dados1->porta;
                    $isp        = $dados1->isp;
                    $org        = $dados1->org;
                    $players    = $dados->Data->clients;
                    $versao     = $dados->Data->server;
                    $maxplayers = $dados->Data->sv_maxclients;
                    $array      = array(
                        "status" => "Y",
                        "msg" => "Veja abaixo as informações do servidor!",
                        "http" => $httpcode,
                        "name" => $name,
                        "ip" => $ip1,
                        "ipfull" => $ip,
                        "porta" => $porta,
                        "versao" => $versao,
                        "players" => $players,
                        "maxplayers" => $maxplayers,
                        "country" => $country,
                        "isp" => $isp,
                        "org" => $org
                    );
                    echo json_encode($array);
                } else {
                    $array = array(
                        "status" => "N",
                        "msg" => "Tivemos problemas durante a sua requisição, aguarde alguns segundos e tente novamente!",
                        "http" => $httpcode
                    );
                    echo json_encode($array);
                }
            }
            break;
        case 403:
            $array = array(
                "status" => "N",
                "msg" => "Link inválido!",
                "http" => $httpcode
            );
            echo json_encode($array);
            break;
        case 404:
            $array = array(
                "status" => "N",
                "msg" => "Código inválido!",
                "http" => $httpcode
            );
            echo json_encode($array);
            break;
        default:
            $array = array(
                "status" => "N",
                "msg" => "Tivemos problemas durante a sua requisição, aguarde alguns segundos e tente novamente!",
                "http" => $httpcode
            );
            return json_encode($array);
            break;
    }
    curl_close($ch);
}

function RemoveCfx($url)
{
    $disallowed = array(
        'http://cfx.re/join/',
        'https://cfx.re/join/',
        'cfx.re/join/'
    );
    foreach ($disallowed as $d) {
        if (strpos($url, $d) === 0) {
            return str_replace($d, '', $url);
        }
    }
    return $url;
}

function GEOIp($ip)
{
    if (empty($ip)) {
        $array = array(
            "status" => "N",
            "msg" => "IP em branco!"
        );
        echo json_encode($array);
        return;
    }
    ;
    $iparr = explode(":", $ip);
    
    $req = curl_init("http://ip-api.com/json/$iparr[0]");
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($req, CURLOPT_HEADER, 0);
    $data     = curl_exec($req);
    $httpcode = curl_getinfo($req, CURLINFO_HTTP_CODE);
    switch ($httpcode) {
        case 200:
            $dados    = json_decode($data);
            $ip1      = $iparr[0];
            $porta    = $iparr[1];
            $status   = $dados->status;
            $country  = $dados->country;
            $isp      = $dados->isp;
            $org      = $dados->org;
            if ($status === 'success') {
                $array = array(
                    "status" => "Y",
                    "ip" => $ip1,
                    "porta" => $porta,
                    "country" => $country,
                    "isp" => $isp,
                    "org" => $org
                );
            } else {
                $array = array(
                    "status" => "N",
                    "msg" => "Tivemos problemas durante a sua requisição, aguarde alguns segundos e tente novamente!",
                    "http" => $httpcode
                );
            }
            return json_encode($array);
            break;
        case 403:
            $array = array(
                "status" => "N",
                "msg" => "Link inválido",
                "http" => $httpcode
            );
            return json_encode($array);
            break;
        default:
            $array = array(
                "status" => "N",
                "msg" => "Tivemos problemas durante a sua requisição, aguarde alguns segundos e tente novamente!",
                "http" => $httpcode
            );
            return json_encode($array);
            break;
    }
    curl_close($req);
}
?>
