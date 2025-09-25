<?php
error_reporting(0);
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /user/sign-in");
    exit;
}

$path = $_SERVER["DOCUMENT_ROOT"];
require_once $path."/api/v1.1/core/brain.php";

function get_news()
{
    global $connection;
    try {
        $query = $connection->prepare("SELECT * FROM breathe_news WHERE is_published = 1 ORDER BY priority DESC, created_at DESC LIMIT 5");
        $query->execute();

        $list = "";

        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $priority_badge = "";
            if ($result["priority"] == 1) {
                $priority_badge = '<span class="badge badge-warning">ALTA</span> ';
            } elseif ($result["priority"] == 2) {
                $priority_badge = '<span class="badge badge-danger">MUY ALTA</span> ';
            }
            
            $date = date('d/m/Y H:i', strtotime($result["created_at"]));
            $content = strlen($result["content"]) > 100 ? substr($result["content"], 0, 100) . '...' : $result["content"];
            
            $list .= "<div class='listview listview--bordered'>
                        <div class='listview__item'>
                            <div class='listview__content'>
                                <div class='listview__heading'>
                                    {$priority_badge}" . htmlspecialchars($result["title"]) . "
                                    <small class='text-muted float-right'>{$date}</small>
                                </div>
                                <div class='listview__attrs'>
                                    <span>" . htmlspecialchars($content) . "</span>
                                    <br>
                                    <small class='text-muted'>Por: " . htmlspecialchars($result["author"]) . "</small>
                                </div>
                            </div>
                        </div>
                      </div>";
        }
        
        if (empty($list)) {
            $list = "<div class='listview listview--bordered'>
                        <div class='listview__item'>
                            <div class='listview__content'>
                                <div class='listview__heading'>No hay noticias disponibles</div>
                                <div class='listview__attrs'>
                                    <span>Las noticias aparecerán aquí cuando estén disponibles.</span>
                                </div>
                            </div>
                        </div>
                      </div>";
        }
        
        return $list;
    } catch (Exception $e) {
        error_log("Error en get_news(): " . $e->getMessage());
        return "<div class='listview listview--bordered'>
                    <div class='listview__item'>
                        <div class='listview__content'>
                            <div class='listview__heading'>Error al cargar noticias</div>
                            <div class='listview__attrs'>
                                <span>No se pudieron cargar las noticias en este momento.</span>
                            </div>
                        </div>
                    </div>
                  </div>";
    }
}

function get_users($count = false)
{
    global $connection;
    if ($count == true) {
        $query = $connection->prepare("SELECT COUNT(*) FROM breathe_users");
        $query->execute();
        return $query->fetchColumn();
        $query = null;
        $connection = null;
    } else {
        $query = $connection->prepare("SELECT username FROM breathe_users ORDER BY ID DESC LIMIT 5");
        $query->execute();

        $list = "";

        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $list .= "<div class='listview listview--striped'><div class='checkbox-char todo__item'><label for='char-1'>" . $result["username"][0] . "</label><div class='listview__content'><span class='listview__heading'>" . $result["username"] . "</span><p>DARKCT-CODE</p></div></div></div></div>";
        }
        return $list;
        $query = null;
        $connection = null;
    }
}

function get_lives()
{
    global $connection;
    $query = $connection->query("SELECT COUNT(*) total FROM breathe_lives");
    return $query->fetchColumn();
    $query = null;
    $connection = null;
}

function get_data()
{
    global $connection;
    $query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
    $query->bindParam("id", $_SESSION["user_id"], PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
    $query = null;
    $connection = null;
}

function get_permissions()
{
    global $connection;
    $query = $connection->prepare("SELECT * FROM breathe_users WHERE id=:id");
    $query->bindParam("id", $_SESSION["user_id"], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $storage = "<hr><li><a href='/herramientas/ccdump.php'><i class='zwicon-search'></i> Search Extra</a></li>";
    $seller  = "<hr><li class='navigation__sub'><a href='#'><i class='zwicon-password'></i> Admin Tools</a><ul style='display:block;max-height:1000px;'><li><a href='/admin-consultar-usuario.php'>Consultar Usuario</a></li><li><a href='/admin-generar-key.php'>Generar Key</a></li><li><a href='/admin-keys.php'>Gestionar Keys</a></li><li><a href='/admin-login-monitor.php'><i class='zwicon-shield'></i> Monitor de Login</a></li></ul></li>";

    $extra = "";
    if ($result["suscripcion"] >= 2) {
        $extra .= $storage;
    }
    if ($result["suscripcion"] == 3) {
        $extra .= $seller;
    }

    $nav = "<ul class='navigation'>"
        ."<li class='navigation__sub'><a href='#'><i class='zwicon-slider-circle-h'></i> ID BOT LIVES</a><ul style='max-height:1000px;'><li><a href='/mis-lives.php'><i class='zwicon-eye'></i> Mis Lives</a></li><li><a href='/herramientas/telegram-config.php'><i class='zwicon-telegram'></i> Telegram Config</a></li></ul></li>"
        .$extra
        ."</ul>";

    return $nav;
    $query = null;
    $connection = null;
}
?>