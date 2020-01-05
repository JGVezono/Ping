<?php
/*
 *
 * Verificador de status de servidor, ON ou OFF
 *
 */

/*
 *
 * Nome do WebSite
 * 
 */
$title = "Verificador de status de servidor";

/*
 *
 * Lista de Servidores que vao ser verificados
 * 
 */
$servers = array(
    'Facebook' => array(       // Nome da array, ao inves de ser $servers[0] => $servers[Facebook]
        'ip' => 'www.facebook.com',                        // Endereço de Ip
        'port' => 80,                                       // Porta utilizado pelo IP
        'info' => 'Servidor local',                         // Informação do servidor de hospedabem
        'purpose' => 'Rede social',                         // Finalidade
    ),
    'Yahoo' => array(
        'ip' => 'www.yahoo.com',
        'port' => 80,
        'info' => 'Servidor local',
        'purpose' => 'Servidor e e-mail',
    ),
    'Globo' => array(
        'ip' => 'www.globo.com',
        'port' => 80,
        'info' => 'Servidor local',
        'purpose' => 'Site de Noticias e de e-mail',
    ),
    'GOOGLE por endereço' => array(
        'ip' => 'www.google.com',
        'port' => 80,
        'info' => 'servidor teste',
        'purpose' => 'teste',
    ),
    'GOOGLE por ip' => array(
        'ip' => '216.58.202.164',
        'port' => 80,
        'info' => 'servidor teste',
        'purpose' => 'teste',
    ),
    'site que nao existe' => array(
        'ip' => 'www.asdhasdkjas.com',
        'port' => 80,
        'info' => 'servidor teste',
        'purpose' => 'teste',
    ),
    'IP que nao existe' => array(
        'ip' => '172.155.190.2',
        'port' => 80,
        'info' => 'servidor teste',
        'purpose' => 'teste',
    ),
    'IP/endereço em branco' => array(
        'ip' => '',
        'port' => 80,
        'info' => 'servidor teste',
        'purpose' => 'teste',
    )
);
// Faz uma verificação de caso exista algum servidor na lista, retorna o status e ms que e verificado
//  pela função declarado no final do arquivo
if (isset($_GET['host'])) {
    $host = $_GET['host'];
    if (isset($servers[$host])) {
        header('Content-Type: application/json');
        $return = array(
            $data = test($servers[$host]),      // Retornado $result array(FALSE ou TRUE, 0 ou VALOR)
            'status' => $data[0],               // Pega $result array(FALSE ou TRUE)
            'ms' => $data[1]                    // Pega $result array(0 ou VALOR)
        );
        echo json_encode($return);              // Retorna array 'Status' => TRUE ou FALSE, 'ms' => valor ou 0
        exit;
    } else {                                    // Caso não ha nenhum servidor na lista $servers
        header("HTTP/1.1 404 Not Found");
    }
}
// Faz uma codificação do IP para não ficar exposto para o cliente
$names = array();
foreach ($servers as $name => $info) {
    $names[$name] = md5($name);
}
?>

<!--
 ---
 --- Estrutura do HTML do Website
 ---
 -->
<!doctype html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?></title>        <!-- Titulo declarado no começo do arquivo -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootswatch/2.3.2/cosmo/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css">
    </head>
    <body>

        <div class="container">
            <h1><center><?php echo $title; ?></center></h1>         <!-- Titulo declarado no começo do arquivo -->
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nome</th>
                        <th>Host</th>
                        <th>Finalidade</th>
                        <th>MS</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 
                     --- 
                     --- Percorre todos os items da variavel $SERVERS e
                     ---    lista todos os servidores da lista 
                     ---
                     -->
                    <?php foreach ($servers as $name => $server): ?>

                        <tr id="<?php echo md5($name); ?>">
                            <td><i class="icon-spinner icon-spin icon-large"></i></td>
                            <td id="div1" class="name"><?php echo $name; ?></td>
                            <td id="div2"><?php echo $server['info']; ?></td>
                            <td id="div3"><?php echo $server['purpose']; ?></td>
                            <td id="div4"> </td>
                        </tr>
                        
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script type="text/javascript">
            // Script Jquery que ~retorna no html o status e ms
            function test(host, hash) {
                // 
                var request;
                // faz a requisição para a propria paragina com o nome do arquivo
                request = $.ajax({
                    url: "<?php echo basename(__FILE__); ?>",
                    type: "get",
                    data: {
                        host: host
                    },
                    beforeSend: function () {
                        $('#' + hash).children().children().css({'visibility': 'visible'});
                    }
                });
                // chamda de requisição do IF no começo do arquivo, para verificar o status
                //  e ms de cada servidor, caso ha algum
                request.done(function (response, textStatus, jqXHR) {
                    var ms = response.ms;               // representa a 'MS' do IF no começo do arquivo
                    var status = response.status;       // representa o 'STATUS' do IF do começo do arquivo
                    var statusClass;
                    if (status) {                       // Caso status == true
                        statusClass = 'success';        // statusClass recebe 'success'
                    } else {                            // Caso status == false
                        statusClass = 'error';          // statusClass recebe 'error'
                    }
                    // Adiciona a class addClass(statusClass) success ou error na div $('#' + hash)
                    //  adiciona a class statusClass success ou error, que altera a cor da div
                    $('#' + hash).removeClass('success error').addClass(statusClass);
                    // adiciona MS no div4, que e filho children() que pertence ao DIV PAI $('#' + hash)
                    //  onde eq(4) representa o div4, caso seja div3 deve ser eq(3)
                    //  e escreve html(ms) passando a variavel(ms) dentro da função .html()
                    $('#' + hash).children().eq(4).html(ms);
                });
                // chamada caso ocorre alguma falha na requisição do IF no começo do arquivo
                request.fail(function (jqXHR, textStatus, errorThrown) {
                    // log de erro no console
                    console.error(
                        "The following error occured: " +
                            textStatus, errorThrown
                    );
                });
                request.always(function () {
                    $('#' + hash).children().children().css({'visibility': 'hidden'});
                })
            }
            $(document).ready(function () {
                var servers = <?php echo json_encode($names); ?>;
                var server, hash;
                for (var key in servers) {
                    server = key;
                    hash = servers[key];
                    test(server, hash);
                    (function loop(server, hash) {
                        setTimeout(function () {
                            test(server, hash);
                            loop(server, hash);
                        }, 6000);
                    })(server, hash);
                }
            });
        </script>

    </body>
</html>

<?php
/*
 *
 * Função de verificaçao dos servidores se estão on-line e retorna TRUE (VERDADEIRO) para ON-LINE,
 *  e FALSE (FALSO) para OFF-LINE, e retorna MS do tempo de respota da consulta, caso OFF-LINE retorna 0.
 */
function test($server) {
    $serverip = @gethostbyname($server['ip']);
    if ($serverip == $server['ip']) {
        $tB = microtime(true);
        $socket = @fsockopen($server['ip'], $server['port'], $errorNo, $errorStr, 3);
        if (!$socket) {
            $ms = 0;
            $false = false;
            $result = array($false, $ms);
            return $result;
        } else {
            $tA = microtime(true);
            $ms = round((($tA - $tB) * 1000), 0)." ms";
            $true = true;
            $result = array($true, $ms);
            return $result;
            if ($socket) {
                @fclose($socket);
            } else {

            }
        }        
    } else if ($serverip == null OR $server['ip'] == null) {
        $ms = 0;
        $false = false;
        $result = array($false, $ms);
        return $result;
    } else {
        $tB = microtime(true);
        $socket = @fsockopen($serverip, $server['port'], $errorNo, $errorStr, 3);
        if (!$socket) {
            $ms = 0;
            $false = false;
            $result = array($false, $ms);
            return $result;
        } else {
            $tA = microtime(true);
            $ms = round((($tA - $tB) * 1000), 0)." ms";
            $true = true;
            $result = array($true, $ms);
            return $result;
            if ($socket) {
                @fclose($socket);
            } else {

            }
        }
    }
}

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }
    return false;
}
?>